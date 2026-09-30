<?php namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

/**
 * Composition-scheme GST postings: keeps every voucher of that flow a complete double entry.
 *
 * WHY THIS EXISTS
 * A branch registered under the composition scheme cannot claim input tax credit, so the GST on a
 * purchase is a cost and the composition tax on a sale is an expense. The application books that
 * through a "GST PAID A/C" system journal (voucher type 23) tied to the purchase or the sale by the
 * link table `vchbridgen`. The journal and the voucher it belongs to are one entry:
 *
 *      purchase :  Dr Purchase (base)  Dr <input tax> (tax)   Cr Supplier (base + tax)
 *      journal  :  Dr GST PAID (tax)                          Cr <input tax> (tax)
 *      sale     :  Dr Customer (goods)                        Cr Sales (goods)
 *      journal  :  Dr GST PAID (tax)                          Cr <tax payable> (tax)
 *
 * The posting code wrote the two halves from two independent calculations, so the entry could come out
 * one-sided: the tax credited to the tax ledgers with no debit anywhere, or the journal's GST PAID
 * debit posted as 0.00, or posted as the rounded total of the invoice while the legs were the rounded
 * components, or a tax ledger that could not be resolved and its leg dropped in silence. Each of those
 * leaves total debit != total credit for the whole company, which is why a Balance Sheet or a Trial
 * Balance built from that ledger cannot tally.
 *
 * WHAT THIS CLASS DOES
 * It derives the missing side from the rows the vouchers already carry - never from a figure of its
 * own - and it acts only where the stored data shows what is missing:
 *
 *   J  the GST PAID row of a type-23 journal must be whatever balances that journal's own tax legs
 *      (inserted when absent, removed when the legs net to nothing). The journal exists for no other
 *      purpose, so this is its definition rather than a guess.
 *   Z  a GST PAID row of exactly 0.00 on a voucher of any type is set to the amount that balances the
 *      voucher group. A zero row is an unfinished leg; nothing else can be read from it.
 *   M  when the group's difference equals the total of its GST tax-ledger legs on the heavy side, the
 *      opposite legs are added on those same ledgers, account by account. This is the "the difference
 *      equals these legs" case the audit reports, and the mirror image is the only entry that clears
 *      it while leaving the tax where the voucher put it.
 *   S  when the group has no tax-ledger leg at all and the amount GST PAID A/C would have to hold for the
 *      entry to balance is exactly the total tax recorded in the voucher's own GST summary
 *      (`vchgstsumn`), GST PAID A/C is given that amount - it is the account the composition journal
 *      exists to debit, and two stored facts have to agree before anything is written.
 *   W  every leg of the entry is on the credit side and the GST PAID A/C leg holds exactly the total of
 *      the tax legs, so the entry has a difference of twice that leg. GST PAID A/C is moved to the debit
 *      side, which is the shape every balanced entry of this flow has; its amount, account and date are
 *      untouched. The same thing on the debit side is ambiguous (a levy or the reversal of one) and is
 *      left for a person.
 *   R  a difference of 0.05 or less on a group that has tax legs is the rounding of the tax breakup
 *      (components rounded per line, the invoice total rounded once) and is added to its largest leg.
 *
 * Anything else is left exactly as it is and reported for a person to look at. There is no other rule,
 * no balancing figure and no suspense account: a difference this class cannot explain from the stored
 * rows stays in the books and keeps showing on the reports.
 *
 * The same code runs in two places - at save time from TransactionTrait, so a voucher cannot be
 * committed one-sided again, and over a whole financial year from `php spark books:repair-composition`,
 * which repairs what is already in the books (dry run first, one transaction, verified afterwards).
 */
final class CompositionPosting
{
    /** Voucher type of the composition "GST PAID A/C" system journal. */
    public const JOURNAL_TYPE = 23;

    /** vchbridgen link types that tie a voucher to its composition journal: 3 purchase, 4 sale, 5 credit note. */
    public const LINK_TYPES = [3, 4, 5];

    /** Amounts closer than this are equal (the ledger is stored to two decimals). */
    public const TOLERANCE = 0.005;

    /** A difference up to this is the rounding of a tax breakup, not a missing leg. */
    public const ROUNDING = 0.05;

    private BaseConnection $db;
    private int $cmp;
    private ?int $gstPaid = null;
    private ?array $taxAccounts = null;

    /** Vouchers whose ledger rows this request wrote; filled by VouchersModel, read by TransactionTrait. */
    private static array $touched = [];

    public function __construct(?BaseConnection $db = null, ?int $cmp = null)
    {
        $this->db  = $db ?? Database::connect();
        $this->cmp = (int)($cmp ?? (int)(session('ses_company_id') ?? 0));
    }

    // ==================================================================================================
    //  what the current request wrote
    // ==================================================================================================

    public static function touch($vchTxnId): void
    {
        $id = (int)$vchTxnId;
        if ($id > 0) { self::$touched[$id] = true; }
    }

    /** @return array<int,int> */
    public static function touchedIds(): array
    {
        return array_map('intval', array_keys(self::$touched));
    }

    public static function forget(): void
    {
        self::$touched = [];
    }

    // ==================================================================================================
    //  masters
    // ==================================================================================================

    /** The company's restricted "GST PAID A/C" ledger, or null when the company has none. */
    public function gstPaidAccount(): ?int
    {
        if ($this->gstPaid === null) {
            $row = $this->db->table('acctmaster')->select('acc_id')
                ->where('cmp_id', $this->cmp)->where('upper(acc_name)', 'GST PAID A/C')
                ->where('acc_is_restrict', 2)->limit(1)->get()->getRowArray();
            $this->gstPaid = $row ? (int)$row['acc_id'] : 0;
        }
        return $this->gstPaid > 0 ? $this->gstPaid : null;
    }

    /**
     * The company's GST tax ledgers (bill-sundry of type 1, tax category 1) - exactly the accounts
     * save_taxacc_yes_out_data() can post to. Freight, discount and other bill-sundry are NOT in this
     * set: a leg on one of those is an ordinary part of the voucher and is never mirrored.
     *
     * @return array<int,int> acc_id => acc_id
     */
    public function taxAccounts(): array
    {
        if ($this->taxAccounts === null) {
            $out = [];
            try {
                $rows = $this->db->table('acctmaster a')->select('a.acc_id')
                    ->join('billsundry b', 'b.bsd_id = a.bsd_id')
                    ->where('a.cmp_id', $this->cmp)->where('b.bsd_type', 1)->where('b.tax_cat_type', 1)
                    ->get()->getResultArray();
                foreach ($rows as $r) { $out[(int)$r['acc_id']] = (int)$r['acc_id']; }
            } catch (\Throwable $e) {
                $out = [];                            // no bill-sundry master here: rules M and R stay off
            }
            $this->taxAccounts = $out;
        }
        return $this->taxAccounts;
    }

    // ==================================================================================================
    //  reading the books
    // ==================================================================================================

    /**
     * Posted ledger rows (acc_txn_type = 1) of the given vouchers, and which of them carry a leg that is
     * still waiting for approval or was rejected - such a voucher is incomplete by design, so it is
     * reported and never changed.
     *
     * @param  array<int,int> $ids
     * @return array{rows: array<int,array<int,array<string,mixed>>>, blocked: array<int,bool>}
     */
    private function ledger(array $ids): array
    {
        $rows = []; $blocked = [];
        foreach (array_chunk(array_values(array_unique(array_map('intval', $ids))), 500) as $chunk) {
            $in = implode(',', $chunk);
            if ($in === '') { continue; }
            foreach ($this->db->query(
                "SELECT acc_txn_id, vch_txn_id, acc_id, hobo_id, acc_txn_date, acc_txn_dr_cr, acc_txn_amt, acc_txn_type
                   FROM accttxnmst WHERE cmp_id = ? AND vch_txn_id IN ($in)", [$this->cmp])->getResultArray() as $r) {
                $v = (int)$r['vch_txn_id'];
                if ((int)$r['acc_txn_type'] === 1) {
                    $rows[$v][] = ['id' => (int)$r['acc_txn_id'], 'acc' => (int)$r['acc_id'], 'bo' => (int)$r['hobo_id'],
                                   'date' => (string)$r['acc_txn_date'], 'side' => (int)$r['acc_txn_dr_cr'],
                                   'amt' => round((float)$r['acc_txn_amt'], 2), 'vch' => $v];
                } elseif (in_array((int)$r['acc_txn_type'], [4, 5], true) && abs((float)$r['acc_txn_amt']) > self::TOLERANCE) {
                    $blocked[$v] = true;
                }
            }
        }
        return ['rows' => $rows, 'blocked' => $blocked];
    }

    /** Voucher headers (type, date, series, branch). @return array<int,array<string,mixed>> */
    private function headers(array $ids): array
    {
        $out = [];
        foreach (array_chunk(array_values(array_unique(array_map('intval', $ids))), 500) as $chunk) {
            $in = implode(',', $chunk);
            if ($in === '') { continue; }
            foreach ($this->db->query("SELECT vch_txn_id, vch_type_id, vch_date, vch_series_id, hobo_id
                                         FROM vchtxnconso WHERE cmp_id = ? AND vch_txn_id IN ($in)", [$this->cmp])->getResultArray() as $r) {
                $out[(int)$r['vch_txn_id']] = ['type' => (int)$r['vch_type_id'], 'date' => (string)$r['vch_date'],
                                               'series' => (int)($r['vch_series_id'] ?? 0), 'bo' => (int)($r['hobo_id'] ?? 0)];
            }
        }
        return $out;
    }

    /**
     * Puts every voucher together with the vouchers it is linked to by a composition link, so that a
     * purchase or a sale and its GST PAID journal are one group. Groups are keyed by their lowest id.
     *
     * @param  array<int,int> $ids
     * @return array<int,array<int,int>>
     */
    public function groups(array $ids): array
    {
        $parent = [];
        foreach (array_values(array_unique(array_map('intval', $ids))) as $i) { $parent[$i] = $i; }
        $find = function (int $x) use (&$parent, &$find): int { return $parent[$x] === $x ? $x : ($parent[$x] = $find($parent[$x])); };

        $links = [];
        try {
            $links = $this->db->query('SELECT vch_txn_id_src s, vch_txn_id_dest d FROM vchbridgen WHERE cmp_id = ? AND vch_bridge_type IN ('
                . implode(',', self::LINK_TYPES) . ')', [$this->cmp])->getResultArray();
        } catch (\Throwable $e) { $links = []; }

        foreach ($links as $l) {
            $s = (int)$l['s']; $d = (int)$l['d'];
            if ($s === $d || $s <= 0 || $d <= 0) { continue; }                 // a voucher linked to itself is one voucher
            if (!isset($parent[$s]) && !isset($parent[$d])) { continue; }      // neither side is in scope
            foreach ([$s, $d] as $x) { if (!isset($parent[$x])) { $parent[$x] = $x; } }
            $a = $find($s); $b = $find($d);
            if ($a !== $b) { $parent[max($a, $b)] = min($a, $b); }
        }
        $out = [];
        foreach (array_keys($parent) as $i) { $out[$find($i)][] = $i; }
        foreach ($out as $k => $v) { sort($v); $out[$k] = $v; }
        return $out;
    }

    /** Total tax the vouchers' own GST summary records (vchgstsumn), or null when there is no summary. */
    private function summaryTax(array $ids): ?float
    {
        $in = implode(',', array_map('intval', $ids));
        if ($in === '') { return null; }
        try {
            $r = $this->db->query("SELECT COALESCE(SUM(vch_total_tax),0) t, COUNT(*) n FROM vchgstsumn
                                    WHERE cmp_id = ? AND vch_txn_id IN ($in)", [$this->cmp])->getRowArray();
        } catch (\Throwable $e) { return null; }
        return ((int)($r['n'] ?? 0) > 0) ? round((float)$r['t'], 2) : null;
    }

    /**
     * What shape a voucher group has, for a report that only describes: the correction a rule would make,
     * or the reason no rule may make one. Returns null unless this class measures the group's difference
     * exactly as the caller did, so a description is never attached to somebody else's figure - the caller
     * may filter by date or branch where this class reads the whole voucher.
     *
     * @param  array<int,int> $members the group's vouchers (a voucher and whatever it is linked to)
     * @return array{tag: string, label: string, text: string}|null  'tag' is a few words to hang off another
     *         label, 'label' groups shapes in a summary of their own, 'text' explains one group in full
     */
    public function shapeOf(array $members, float $difference): ?array
    {
        $gstPaid = $this->gstPaidAccount();
        if ($gstPaid === null) { return null; }

        $led = $this->ledger($members);
        $rowsOf = []; $mine = 0.0;
        foreach ($members as $v) {
            $rowsOf[$v] = $led['rows'][$v] ?? [];
            foreach ($rowsOf[$v] as $r) { $mine += $r['side'] === 1 ? $r['amt'] : -$r['amt']; }
        }
        $mine = round($mine, 2);
        if (abs(round($mine - $difference, 2)) > self::TOLERANCE) { return null; }

        $tax = $this->taxAccounts();
        $acted = []; $diff = $mine;
        $add = function (array $a) use (&$acted, &$diff) { $acted[] = $a; $diff = round($diff + ($a['delta'] ?? 0.0), 2); };
        if ($tax) { $this->ruleWrongSide($members, $rowsOf, $this->headers($members), $gstPaid, $tax, $diff, $add); }
        if ($acted && abs($diff) <= self::TOLERANCE) {
            return ['tag'   => 'books:repair-composition corrects this',
                    'label' => 'every leg on the credit side, GST PAID A/C holding the tax total: books:repair-composition corrects it',
                    'text'  => 'every leg is on the credit side and GST PAID A/C holds the total of the tax legs:'
                               . ' books:repair-composition moves that one leg to the debit side'];
        }
        return $this->describe($members, $rowsOf, $gstPaid, $tax, $mine);
    }

    // ==================================================================================================
    //  the plan
    // ==================================================================================================

    /**
     * Works out what each voucher group needs, writing nothing. A group is either fully explained (its
     * actions are returned) or left alone and listed for review - a partial answer counts as no answer.
     *
     * @param  array<int,int> $ids       vouchers to consider (their linked vouchers are pulled in)
     * @param  bool           $rounding  apply rule R (differences of 0.05 or less)
     * @return array{actions: array<int,array<string,mixed>>, fixed: array<int,array<string,mixed>>, review: array<int,array<string,mixed>>, groups: int, degraded: bool}
     */
    public function plan(array $ids, bool $rounding = true): array
    {
        $groups = $this->groups($ids);
        $all = [];
        foreach ($groups as $g) { foreach ($g as $v) { $all[] = $v; } }
        $led = $this->ledger($all);
        $hdr = $this->headers($all);
        $gstPaid = $this->gstPaidAccount();
        $tax = $this->taxAccounts();
        $signed = static function (array $r): float { return $r['side'] === 1 ? $r['amt'] : -$r['amt']; };

        $actions = []; $fixed = []; $review = [];
        foreach ($groups as $members) {
            $rowsOf = [];
            foreach ($members as $v) { $rowsOf[$v] = $led['rows'][$v] ?? []; }
            if (!array_filter($rowsOf)) { continue; }

            $diff = 0.0;
            foreach ($rowsOf as $rs) { foreach ($rs as $r) { $diff += $signed($r); } }
            $diff = round($diff, 2);
            if (abs($diff) <= self::TOLERANCE) { continue; }

            $blocked = false;
            foreach ($members as $v) { if (!empty($led['blocked'][$v])) { $blocked = true; } }

            $mine = [];
            $add = function (array $a) use (&$mine, &$diff) {
                $mine[] = $a;
                $diff = round($diff + ($a['delta'] ?? 0.0), 2);
            };

            if (!$blocked && $gstPaid !== null) {
                $this->ruleJournal($members, $rowsOf, $hdr, $gstPaid, $signed, $add);
                $this->ruleZeroLeg($members, $rowsOf, $hdr, $gstPaid, $diff, $add);
            }
            if (!$blocked && $gstPaid !== null && $tax) {
                $this->ruleWrongSide($members, $rowsOf, $hdr, $gstPaid, $tax, $diff, $add);
            }
            if (!$blocked && $tax) {
                $this->ruleMirror($members, $rowsOf, $hdr, $tax, $diff, $add);
            }
            if (!$blocked && $gstPaid !== null) {
                $this->ruleSummary($members, $rowsOf, $hdr, $gstPaid, $tax, $diff, $add);
            }
            if (!$blocked && $rounding && $tax) {
                $this->ruleRounding($members, $rowsOf, $hdr, $tax, $diff, $add);
            }

            if (!$mine || abs($diff) > self::TOLERANCE) {
                $shape = $blocked || $mine ? null : $this->describe($members, $rowsOf, $gstPaid, $tax, $diff);
                $why = $blocked ? 'a leg of this entry is waiting for approval or was rejected'
                     : ($mine ? 'only part of the difference could be explained from the stored rows'
                              : ($shape['text'] ?? 'the stored rows do not show which leg is missing'));
                $review[] = ['group' => $members, 'difference' => $diff, 'blocked' => $blocked, 'why' => $why];
                continue;
            }
            foreach ($mine as $a) { $actions[] = $a; }
            $fixed[] = ['group' => $members, 'rules' => array_values(array_unique(array_column($mine, 'rule'))), 'rows' => count($mine)];
        }
        // Without the bill-sundry master there is no way to tell a GST tax ledger from freight or
        // discount, so most rules stay off and nothing here may be treated as a verdict.
        return ['actions' => $actions, 'fixed' => $fixed, 'review' => $review, 'groups' => count($groups),
                'degraded' => ($gstPaid === null || !$tax)];
    }

    /**
     * Names the shape of a difference no rule may touch, so the review list says what is actually wrong
     * instead of "a leg is missing". It proposes nothing and writes nothing: where two corrections are
     * arithmetically equal but land in different places, choosing between them is an accounting decision.
     */
    private function describe(array $members, array $rowsOf, ?int $gstPaid, array $tax, float $diff): ?array
    {
        if ($gstPaid === null) { return null; }

        $gstRow = null; $gstLegs = 0; $taxTotal = 0.0; $taxLegs = 0; $foreign = 0;
        foreach ($members as $v) {
            foreach ($rowsOf[$v] as $r) {
                if ($r['acc'] === $gstPaid) { $gstLegs++; $gstRow = $r; continue; }
                if (isset($tax[$r['acc']])) { $taxTotal = round($taxTotal + $r['amt'], 2); $taxLegs++; continue; }
                $foreign++;
            }
        }
        if ($gstLegs !== 1 || $foreign > 0 || $gstRow === null) { return null; }
        $drcr = static function (int $side): string { return $side === 1 ? 'Dr' : 'Cr'; };

        // No tax leg at all: the entry is a single GST PAID amount and the account it belongs against is
        // nowhere in the stored rows, so there is nothing to derive the other leg from.
        if ($taxLegs === 0) {
            return ['tag'   => 'the only leg is GST PAID A/C',
                    'label' => 'the only leg is GST PAID A/C, with nothing to derive the other from',
                    'text'  => 'the only leg of this entry is ' . $drcr($gstRow['side']) . ' ' . number_format($gstRow['amt'], 2, '.', '')
                               . ' on GST PAID A/C: the account it belongs against is not in the stored rows'];
        }

        // Both descriptions below compare GST PAID A/C against the total of the tax legs, which only means
        // something when those legs are all on one side: tax legs facing each other are a shape of their
        // own, and $taxSide is 0 for them, so neither description claims to have recognised it.
        // Every leg on the debit side, GST PAID A/C holding the total of the tax legs: the mirror of rule W.
        // Crediting the tax legs makes it a levy, crediting GST PAID A/C makes it the reversal of one, and
        // the stored rows do not say which was meant.
        $taxSide = $this->sideOfTaxLegs($members, $rowsOf, $gstPaid, $tax);
        if ($gstRow['side'] === 1 && $taxSide === 1 && $taxTotal > self::TOLERANCE
            && abs(round($gstRow['amt'] - $taxTotal, 2)) <= self::TOLERANCE) {
            return ['tag'   => 'every leg on the debit side: a levy or the reversal of one',
                    'label' => 'every leg on the debit side, GST PAID A/C holding the tax total: a levy or the reversal of one',
                    'text'  => 'every leg of this entry is on the debit side and GST PAID A/C holds the total of its '
                               . ($taxLegs === 1 ? 'tax leg' : $taxLegs . ' tax legs') . ' (' . number_format($taxTotal, 2, '.', '')
                               . '): crediting the tax legs makes it a levy, crediting GST PAID A/C makes it the reversal of one'
                               . ' - the stored rows do not say which, so it is an accounting decision'];
        }

        // GST PAID holds twice the levy its tax legs carry. Halving it and doubling a tax leg both balance
        // the entry, but one moves the profit and the other moves a tax balance, so neither is automatic.
        if ($taxSide !== 0 && $taxTotal > self::TOLERANCE
            && abs(round($gstRow['amt'] - 2 * $taxTotal, 2)) <= self::TOLERANCE
            && $gstRow['side'] !== $taxSide) {
            return ['tag'   => 'GST PAID A/C is twice its tax legs',
                    'label' => 'GST PAID A/C holds exactly twice its tax legs: the amount is double, or the tax is half',
                    'text'  => 'the GST PAID A/C leg is ' . $drcr($gstRow['side']) . ' ' . number_format($gstRow['amt'], 2, '.', '')
                               . ', exactly twice the ' . number_format($taxTotal, 2, '.', '') . ' on its '
                               . ($taxLegs === 1 ? 'tax leg' : $taxLegs . ' tax legs')
                               . ': either that amount is double or the tax is half, which is an accounting decision'];
        }
        return null;
    }

    /** The side every tax leg of the group sits on, or 0 if they are not all on one side. */
    private function sideOfTaxLegs(array $members, array $rowsOf, int $gstPaid, array $tax): int
    {
        $side = 0;
        foreach ($members as $v) {
            foreach ($rowsOf[$v] as $r) {
                if ($r['acc'] === $gstPaid || !isset($tax[$r['acc']])) { continue; }
                if ($side === 0) { $side = $r['side']; } elseif ($side !== $r['side']) { return 0; }
            }
        }
        return $side;
    }

    /** Rule J: the GST PAID row of a composition journal is whatever balances that journal's own legs. */
    private function ruleJournal(array $members, array $rowsOf, array $hdr, int $gstPaid, callable $signed, callable $add): void
    {
        $tax = $this->taxAccounts();
        foreach ($members as $v) {
            if ((int)($hdr[$v]['type'] ?? 0) !== self::JOURNAL_TYPE) { continue; }
            $legs = []; $gstRows = []; $foreign = false;
            foreach ($rowsOf[$v] as $r) {
                if ($r['acc'] === $gstPaid) { $gstRows[] = $r; continue; }
                if (!isset($tax[$r['acc']])) { $foreign = true; }
                $legs[] = $r;
            }
            // A composition journal holds GST PAID A/C against GST tax ledgers and nothing else. If it
            // carries anything else then it is not the entry this rule describes, and it is left alone.
            if ($foreign) { continue; }
            // With no tax leg there is nothing for GST PAID A/C to be measured against: a lone GST PAID
            // row might be the only trace of a tax the books still owe, so it is never removed here. Rule
            // S can still correct it, but only against the voucher's own GST summary.
            if (!$legs) { continue; }

            $need = 0.0; foreach ($legs as $r) { $need -= $signed($r); }        // what GST PAID must contribute
            $need = round($need, 2);
            $have = 0.0; foreach ($gstRows as $r) { $have += $signed($r); }
            if (abs($need - round($have, 2)) <= self::TOLERANCE) { continue; }

            $keep = $gstRows[0] ?? null;
            foreach (array_slice($gstRows, 1) as $extra) {
                $add(['kind' => 'delete_row', 'rule' => 'J', 'vch' => $v, 'row' => $extra['id'], 'acc' => $extra['acc'],
                      'side' => $extra['side'], 'amount' => $extra['amt'], 'delta' => -$signed($extra), 'register' => true,
                      'why' => 'a second GST PAID A/C row on the same composition journal']);
            }
            if (abs($need) <= self::TOLERANCE) {
                if ($keep) {
                    $add(['kind' => 'delete_row', 'rule' => 'J', 'vch' => $v, 'row' => $keep['id'], 'acc' => $keep['acc'],
                          'side' => $keep['side'], 'amount' => $keep['amt'], 'delta' => -$signed($keep), 'register' => true,
                          'why' => 'the journal has no tax leg left for GST PAID A/C to balance']);
                }
                continue;
            }
            $side = $need > 0 ? 1 : 2;
            $amt  = round(abs($need), 2);
            if ($keep) {
                $add(['kind' => 'update_row', 'rule' => 'J', 'vch' => $v, 'row' => $keep['id'], 'acc' => $gstPaid,
                      'side' => $side, 'amount' => $amt, 'was_side' => $keep['side'], 'was_amount' => $keep['amt'],
                      'delta' => ($side === 1 ? $amt : -$amt) - $signed($keep), 'register' => true,
                      'date' => $keep['date'], 'bo' => $keep['bo'],
                      'why' => 'GST PAID A/C must balance the tax legs of its own journal']);
            } else {
                $add(['kind' => 'insert_row', 'rule' => 'J', 'vch' => $v, 'acc' => $gstPaid, 'side' => $side, 'amount' => $amt,
                      'date' => $legs[0]['date'] ?? ($hdr[$v]['date'] ?? null), 'bo' => $legs[0]['bo'] ?? (int)($hdr[$v]['bo'] ?? 0),
                      'delta' => ($side === 1 ? $amt : -$amt), 'register' => true,
                      'why' => 'the composition journal has no GST PAID A/C row']);
            }
        }
    }

    /** Rule Z: a GST PAID row of exactly 0.00 is an unfinished leg; it becomes the balancing amount. */
    private function ruleZeroLeg(array $members, array $rowsOf, array $hdr, int $gstPaid, float $diff, callable $add): void
    {
        if (abs($diff) <= self::TOLERANCE) { return; }
        foreach ($members as $v) {
            foreach ($rowsOf[$v] as $r) {
                if ($r['acc'] !== $gstPaid || abs($r['amt']) > self::TOLERANCE) { continue; }
                $side = $diff < 0 ? 1 : 2;
                $amt  = round(abs($diff), 2);
                $add(['kind' => 'update_row', 'rule' => 'Z', 'vch' => $v, 'row' => $r['id'], 'acc' => $gstPaid,
                      'side' => $side, 'amount' => $amt, 'was_side' => $r['side'], 'was_amount' => $r['amt'],
                      'delta' => ($side === 1 ? $amt : -$amt), 'date' => $r['date'], 'bo' => $r['bo'],
                      'register' => (int)($hdr[$v]['type'] ?? 0) === self::JOURNAL_TYPE,
                      'why' => 'the GST PAID A/C leg of this entry was posted as 0.00']);
                return;
            }
        }
    }

    /**
     * Rule W: every leg of the entry sits on the credit side, and the GST PAID A/C leg holds exactly the
     * total of the tax legs - so the GST PAID A/C leg is on the wrong side.
     *
     * This flow has one shape: GST PAID A/C is debited with the levy and the tax ledgers are credited with
     * the same levy, split across them (see the entries at the top of this class). Some entries were
     * written with GST PAID A/C credited as well, which leaves every leg on the credit side - an entry no
     * reading of double entry allows - and a difference of exactly twice that leg. Correcting its side
     * restores the shape every balanced entry of this flow has. Nothing is recalculated: the amount, the
     * account and the date are the ones already stored, and only one row changes.
     *
     * Debiting the tax legs instead would also balance the entry, and would turn it into the reversal of a
     * levy rather than a levy. Where GST PAID A/C and the tax ledgers sit in the same part of the reports -
     * as they do in both companies this was built for, all of them Indirect Expenses - the two are
     * indistinguishable in the profit, the Balance Sheet and the Trial Balance, and differ only in which
     * expense line carries the amount. Where they do not, the choice matters, which is why this rule asks
     * two independent facts to agree before it writes: the GST PAID amount equals the total of the tax legs
     * to the paisa, and moving that one leg closes the whole difference.
     *
     * The mirror image - every leg on the DEBIT side - is deliberately not touched. There the two readings
     * are a levy with its tax legs miswritten and a reversal with GST PAID A/C miswritten, and the stored
     * rows do not say which, so it is reported for a person to decide.
     */
    private function ruleWrongSide(array $members, array $rowsOf, array $hdr, int $gstPaid, array $tax, float $diff, callable $add): void
    {
        if ($diff >= -self::TOLERANCE || !$tax) { return; }   // credit-heavy only

        $gstRow = null; $taxTotal = 0.0; $taxLegs = 0;
        foreach ($members as $v) {
            foreach ($rowsOf[$v] as $r) {
                if ($r['side'] !== 2) { return; }             // any debit leg: not this shape
                if ($r['acc'] === $gstPaid) {
                    // A second GST PAID leg, or a zero one, is a different shape: leave it alone.
                    if ($gstRow !== null || $r['amt'] <= self::TOLERANCE) { return; }
                    $gstRow = $r + ['vch' => $v];
                    continue;
                }
                if (!isset($tax[$r['acc']]) || $r['amt'] <= 0) { return; }   // a leg that is not a GST tax ledger
                $taxTotal = round($taxTotal + $r['amt'], 2);
                $taxLegs++;
            }
        }
        if ($gstRow === null || $taxLegs === 0) { return; }
        if (abs(round($gstRow['amt'] - $taxTotal, 2)) > self::TOLERANCE) { return; }
        if (abs(round($diff + 2 * $gstRow['amt'], 2)) > self::TOLERANCE) { return; }

        $add(['kind' => 'update_row', 'rule' => 'W', 'vch' => $gstRow['vch'], 'row' => $gstRow['id'], 'acc' => $gstPaid,
              'side' => 1, 'amount' => $gstRow['amt'], 'was_side' => 2, 'was_amount' => $gstRow['amt'],
              'delta' => 2 * $gstRow['amt'], 'date' => $gstRow['date'], 'bo' => $gstRow['bo'],
              'register' => (int)($hdr[$gstRow['vch']]['type'] ?? 0) === self::JOURNAL_TYPE,
              'why' => 'every leg of this entry was posted on the credit side and GST PAID A/C holds the total of the '
                       . ($taxLegs === 1 ? 'tax leg' : $taxLegs . ' tax legs') . ' (' . number_format($taxTotal, 2, '.', '')
                       . '): debiting GST PAID A/C is the shape this flow posts and balances the entry']);
    }

    /** Rule M: the difference equals the tax legs on the heavy side, so the mirror legs are missing. */
    private function ruleMirror(array $members, array $rowsOf, array $hdr, array $tax, float $diff, callable $add): void
    {
        if (abs($diff) <= self::TOLERANCE) { return; }
        $heavy = $diff > 0 ? 1 : 2;
        $byAcc = [];
        foreach ($members as $v) {
            foreach ($rowsOf[$v] as $r) {
                if ($r['side'] !== $heavy || !isset($tax[$r['acc']]) || $r['amt'] <= 0) { continue; }
                if (isset($byAcc[$r['acc']])) { $byAcc[$r['acc']]['amt'] = round($byAcc[$r['acc']]['amt'] + $r['amt'], 2); continue; }
                $byAcc[$r['acc']] = ['acc' => $r['acc'], 'amt' => $r['amt'], 'date' => $r['date'], 'bo' => $r['bo']];
            }
        }
        if (!$byAcc) { return; }
        $sum = 0.0; foreach ($byAcc as $a) { $sum += $a['amt']; }
        $sum = round($sum, 2);
        $residual = round(abs($diff) - $sum, 2);
        if (abs($residual) > self::ROUNDING) { return; }

        // The invoice total is rounded once and its tax components are rounded per line, so the legs can
        // come to a paisa less (or more) than the tax the party was charged. The mirror carries the tax
        // the voucher actually has to clear, and that last paisa goes on the largest leg.
        if (abs($residual) > self::TOLERANCE) {
            $biggest = null;
            foreach ($byAcc as $k => $a) { if ($biggest === null || $a['amt'] > $byAcc[$biggest]['amt']) { $biggest = $k; } }
            $byAcc[$biggest]['amt'] = round($byAcc[$biggest]['amt'] + $residual, 2);
            $byAcc[$biggest]['rounded'] = true;
        }

        $other  = $heavy === 1 ? 2 : 1;
        $target = $this->mirrorVoucher($members, $hdr);
        $why    = 'the difference of this entry equals its tax legs on the ' . ($heavy === 1 ? 'debit' : 'credit') . ' side';
        foreach ($byAcc as $a) {
            $add(['kind' => 'insert_row', 'rule' => 'M', 'vch' => $target, 'acc' => $a['acc'], 'side' => $other,
                  'amount' => $a['amt'], 'date' => $a['date'], 'bo' => $a['bo'],
                  'delta' => ($other === 1 ? $a['amt'] : -$a['amt']),
                  'why' => $why . (empty($a['rounded']) ? '' : ', with ' . number_format($residual, 2, '.', '')
                           . ' of rounding between the tax total and its components')]);
        }
    }

    /**
     * Rule S: no tax ledger carries this GST at all, and the amount GST PAID A/C would have to hold for
     * the entry to balance is exactly the total tax the voucher's own GST summary records. Two
     * independent facts have to agree before anything is written - the entry balances AND the amount is
     * the one stored on the voucher - so a GST PAID row that is simply wrong (half the tax, say) is
     * corrected rather than removed, and a voucher with no GST summary is left alone.
     */
    private function ruleSummary(array $members, array $rowsOf, array $hdr, int $gstPaid, array $tax, float $diff, callable $add): void
    {
        if (abs($diff) <= self::TOLERANCE) { return; }
        foreach ($members as $v) {
            foreach ($rowsOf[$v] as $r) { if (isset($tax[$r['acc']])) { return; } }
        }
        $sumTax = $this->summaryTax($members);
        if ($sumTax === null || $sumTax <= 0) { return; }

        $existing = null;
        foreach ($members as $v) {
            foreach ($rowsOf[$v] as $r) { if ($r['acc'] === $gstPaid && $existing === null) { $existing = $r; } }
        }
        $was    = $existing ? ($existing['side'] === 1 ? $existing['amt'] : -$existing['amt']) : 0.0;
        $target = round($was - $diff, 2);                   // what GST PAID must hold for the entry to balance
        if (abs(abs($target) - $sumTax) > self::TOLERANCE) { return; }

        $side = $target > 0 ? 1 : 2;
        $amt  = round(abs($target), 2);
        $why  = "no tax ledger carries this GST, and GST PAID A/C balancing the entry is the total tax of the voucher's own GST summary";
        if ($existing) {
            $add(['kind' => 'update_row', 'rule' => 'S', 'vch' => $existing['vch'], 'row' => $existing['id'], 'acc' => $gstPaid,
                  'side' => $side, 'amount' => $amt, 'was_side' => $existing['side'], 'was_amount' => $existing['amt'],
                  'delta' => ($side === 1 ? $amt : -$amt) - $was, 'date' => $existing['date'], 'bo' => $existing['bo'],
                  'register' => (int)($hdr[$existing['vch']]['type'] ?? 0) === self::JOURNAL_TYPE, 'why' => $why]);
            return;
        }
        $on = null;
        foreach ($members as $v) { if ((int)($hdr[$v]['type'] ?? 0) === self::JOURNAL_TYPE) { $on = $v; } }
        $on  = $on ?? $members[0];
        $any = $rowsOf[$on] ?: ($rowsOf[$members[0]] ?? []);
        $add(['kind' => 'insert_row', 'rule' => 'S', 'vch' => $on, 'acc' => $gstPaid, 'side' => $side, 'amount' => $amt,
              'date' => $any[0]['date'] ?? ($hdr[$on]['date'] ?? null), 'bo' => $any[0]['bo'] ?? (int)($hdr[$on]['bo'] ?? 0),
              'delta' => ($side === 1 ? $amt : -$amt), 'register' => (int)($hdr[$on]['type'] ?? 0) === self::JOURNAL_TYPE,
              'why' => $why]);
    }

    /** Rule R: a difference of 0.05 or less is the rounding of the tax breakup. */
    private function ruleRounding(array $members, array $rowsOf, array $hdr, array $tax, float $diff, callable $add): void
    {
        if (abs($diff) <= self::TOLERANCE || abs($diff) > self::ROUNDING) { return; }
        $big = null;
        foreach ($members as $v) {
            foreach ($rowsOf[$v] as $r) {
                if (!isset($tax[$r['acc']])) { continue; }
                if ($big === null || $r['amt'] > $big['amt']) { $big = $r; }
            }
        }
        if ($big === null) { return; }
        $side = $diff > 0 ? 2 : 1;
        $amt  = round(abs($diff), 2);
        $add(['kind' => 'insert_row', 'rule' => 'R', 'vch' => $big['vch'], 'acc' => $big['acc'], 'side' => $side, 'amount' => $amt,
              'date' => $big['date'], 'bo' => $big['bo'], 'delta' => ($side === 1 ? $amt : -$amt),
              'why' => 'rounding of the tax breakup (components rounded per line, the total rounded once)']);
    }

    /** The voucher a mirrored tax leg belongs on: the source voucher of the group, not its journal. */
    private function mirrorVoucher(array $members, array $hdr): int
    {
        foreach ($members as $v) { if ((int)($hdr[$v]['type'] ?? 0) !== self::JOURNAL_TYPE) { return $v; } }
        return (int)$members[0];
    }

    // ==================================================================================================
    //  writing
    // ==================================================================================================

    /**
     * Writes a plan's actions. The caller owns the transaction: nothing here commits or rolls back.
     *
     * @param  array<int,array<string,mixed>> $actions
     * @return array{inserted:int, updated:int, deleted:int}
     */
    public function apply(array $actions): array
    {
        $n = ['inserted' => 0, 'updated' => 0, 'deleted' => 0];
        foreach ($actions as $a) {
            switch ($a['kind']) {
                case 'update_row':
                    $this->db->table('accttxnmst')->where('acc_txn_id', (int)$a['row'])->where('cmp_id', $this->cmp)
                        ->update(['acc_txn_dr_cr' => (int)$a['side'], 'acc_txn_amt' => round((float)$a['amount'], 2)]);
                    $n['updated']++;
                    break;
                case 'delete_row':
                    $this->db->table('accttxnmst')->where('acc_txn_id', (int)$a['row'])->where('cmp_id', $this->cmp)->delete();
                    $n['deleted']++;
                    break;
                case 'insert_row':
                    $this->db->table('accttxnmst')->insert([
                        'cmp_id' => $this->cmp, 'acc_id' => (int)$a['acc'], 'acc_txn_date' => $a['date'],
                        'acc_txn_dr_cr' => (int)$a['side'], 'acc_txn_amt' => round((float)$a['amount'], 2), 'acc_txn_fcy' => 0,
                        'vch_txn_id' => (int)$a['vch'], 'txn_id' => $this->compTxn((int)$a['vch'], (int)$a['acc']),
                        'hobo_id' => (int)$a['bo'], 'acc_txn_type' => 1,
                    ]);
                    $n['inserted']++;
                    break;
            }
            if (!empty($a['register'])) { $this->syncRegister($a); }
        }
        return $n;
    }

    /** A composition-transaction row for a new ledger row, wired the way the posting code wires one. */
    private function compTxn(int $vch, int $acc): int
    {
        try {
            $hdr = $this->headers([$vch]);
            $this->db->table('cmptxnmstn')->insert([
                'cmp_id' => $this->cmp, 'vch_series_id' => (int)($hdr[$vch]['series'] ?? 0), 'vch_txn_id' => $vch,
                'master_id' => $acc, 'master_id_type' => ($acc === $this->gstPaidAccount() ? 'acc' : 'tax'),
            ]);
            return (int)$this->db->insertID();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /** Keeps the voucher register (acctvchreg) in step with a changed GST PAID row. */
    private function syncRegister(array $a): void
    {
        try {
            $vch = (int)$a['vch']; $acc = (int)$a['acc'];
            if ($a['kind'] === 'delete_row') {
                $this->db->table('acctvchreg')->where('cmp_id', $this->cmp)->where('vch_txn_id', $vch)->where('acc_id', $acc)->delete();
                return;
            }
            $dr = ((int)$a['side'] === 1) ? round((float)$a['amount'], 2) : 0.0;
            $cr = ((int)$a['side'] === 2) ? round((float)$a['amount'], 2) : 0.0;
            $found = $this->db->table('acctvchreg')->where('cmp_id', $this->cmp)->where('vch_txn_id', $vch)
                ->where('acc_id', $acc)->countAllResults();
            if ($found > 0) {
                $this->db->table('acctvchreg')->where('cmp_id', $this->cmp)->where('vch_txn_id', $vch)->where('acc_id', $acc)
                    ->update(['acc_txn_dr_amt' => $dr, 'acc_txn_cr_amt' => $cr]);
                return;
            }
            $hdr = $this->headers([$vch]);
            $this->db->table('acctvchreg')->insert([
                'acct_vch_type' => (int)($hdr[$vch]['type'] ?? self::JOURNAL_TYPE), 'cmp_id' => $this->cmp, 'vch_txn_id' => $vch,
                'txn_id' => null, 'vch_date' => $a['date'] ?? ($hdr[$vch]['date'] ?? null), 'acc_id' => $acc,
                'acc_txn_dr_amt' => $dr, 'acc_txn_cr_amt' => $cr, 'vch_narr' => '',
                'hobo_id' => (int)($a['bo'] ?? ($hdr[$vch]['bo'] ?? 0)), 'acc_txn_type' => 1,
            ]);
        } catch (\Throwable $e) {
            // the register is a listing table and no report reads it, so a failure here is not fatal
        }
    }

    // ==================================================================================================
    //  verification
    // ==================================================================================================

    /**
     * Voucher groups of the given set whose posted rows still do not balance.
     *
     * @param  array<int,int> $ids
     * @return array<int,array{group: array<int,int>, difference: float}>
     */
    public function unbalanced(array $ids): array
    {
        $groups = $this->groups($ids);
        $all = []; foreach ($groups as $g) { foreach ($g as $v) { $all[] = $v; } }
        $led = $this->ledger($all);
        $out = [];
        foreach ($groups as $members) {
            $d = 0.0; $any = false;
            foreach ($members as $v) {
                foreach ($led['rows'][$v] ?? [] as $r) { $any = true; $d += $r['side'] === 1 ? $r['amt'] : -$r['amt']; }
            }
            if ($any && abs(round($d, 2)) > self::TOLERANCE) { $out[] = ['group' => $members, 'difference' => round($d, 2)]; }
        }
        return $out;
    }

    /**
     * Is this voucher group part of the composition flow? True when it holds a type-23 "GST PAID A/C"
     * journal or any row on the GST PAID ledger. A regular-registration branch writes neither, so this
     * is what keeps the save-time check away from everything else the application posts.
     *
     * @param array<int,int> $ids
     */
    public function isCompositionFlow(array $ids): bool
    {
        $in = implode(',', array_map('intval', $ids));
        if ($in === '') { return false; }
        $hdr = $this->headers($ids);
        foreach ($hdr as $h) { if ((int)$h['type'] === self::JOURNAL_TYPE) { return true; } }
        $gstPaid = $this->gstPaidAccount();
        if ($gstPaid === null) { return false; }
        $r = $this->db->query("SELECT 1 x FROM accttxnmst WHERE cmp_id = ? AND acc_id = ? AND vch_txn_id IN ($in) LIMIT 1",
            [$this->cmp, $gstPaid])->getResultArray();
        return (bool)$r;
    }

    /** Debit minus credit of every posted row of the company (optionally one branch / date window). */
    public function imbalance(?string $from = null, ?string $to = null, ?int $bo = null): float
    {
        $sql = "SELECT COALESCE(SUM(CASE WHEN acc_txn_dr_cr = 1 THEN acc_txn_amt WHEN acc_txn_dr_cr = 2 THEN -acc_txn_amt ELSE 0 END),0) t
                  FROM accttxnmst WHERE cmp_id = ? AND acc_txn_type = 1 AND vch_txn_id > 0";
        $bind = [$this->cmp];
        if ($from !== null) { $sql .= ' AND acc_txn_date >= ?'; $bind[] = $from; }
        if ($to !== null)   { $sql .= ' AND acc_txn_date <= ?'; $bind[] = $to; }
        if ($bo !== null)   { $sql .= ' AND hobo_id = ?'; $bind[] = $bo; }
        return round((float)($this->db->query($sql, $bind)->getRowArray()['t'] ?? 0), 2);
    }

    /** Every voucher of the window that has at least one posted row. @return array<int,int> */
    public function vouchersInWindow(string $from, string $to, ?int $bo = null): array
    {
        $sql = 'SELECT DISTINCT vch_txn_id FROM accttxnmst WHERE cmp_id = ? AND acc_txn_type = 1 AND vch_txn_id > 0
                  AND acc_txn_date >= ? AND acc_txn_date <= ?';
        $bind = [$this->cmp, $from, $to];
        if ($bo !== null) { $sql .= ' AND hobo_id = ?'; $bind[] = $bo; }
        $out = [];
        foreach ($this->db->query($sql . ' ORDER BY 1', $bind)->getResultArray() as $r) { $out[] = (int)$r['vch_txn_id']; }
        return $out;
    }
}
