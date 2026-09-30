<?php namespace App\Commands;

use App\Libraries\CompositionPosting;
use App\Models\Admin\ReportingModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Repairs the composition-scheme GST entries already in the books: the purchases and sales whose GST was
 * credited to a tax ledger with no debit anywhere, whose "GST PAID A/C" leg was posted as 0.00, or whose
 * journal is missing a leg. Those one-sided entries are the reason total debit does not equal total
 * credit, and therefore the reason a Balance Sheet or Trial Balance cannot tally.
 *
 * Every change is derived from the rows the vouchers already carry (see App\Libraries\CompositionPosting
 * for the five rules and what each one requires). A difference the stored data cannot explain is listed
 * for a person to look at and left untouched - nothing is forced, and no balancing figure is invented.
 *
 * It is a DRY RUN unless --apply is given:
 *
 *   php spark books:repair-composition --company 148 --fy 235 --branch 199
 *   php spark books:repair-composition --company 148 --fy 235 --branch 199 --csv ~/repair_148.csv
 *   php spark books:repair-composition --company 148 --fy 235 --branch 199 --apply
 *
 * The dry run measures the result for real: it applies the plan inside a transaction, reads the ledger
 * back, prints what the reports would show, and then rolls everything back. With --apply the same work
 * is committed - but only if the verification passes; otherwise it rolls back and says so.
 *
 * Either way it ends with a BALANCE SHEET RECONCILIATION that ties the two totals on the screen to the
 * vouchers behind them:
 *   1  the Balance Sheet's own difference against the ledger's debit-minus-credit. Equal means the report
 *      adds up and the ledger is what is wrong; a gap between them is the report's doing and is named as such.
 *   2  that difference split into the part the vouchers' own rows account for and the part that needs a
 *      person - the first from the plan, the second measured - and the two added back up against step 1.
 *   3  what the Balance Sheet reads once the first part is completed, taken from the real reports inside
 *      the transaction. What is left there is the only difference the stored data cannot explain.
 *
 * TAKE A DATABASE BACKUP BEFORE USING --apply.
 */
class RepairComposition extends BaseCommand
{
    protected $group       = 'Accounting';
    protected $name        = 'books:repair-composition';
    protected $description = 'Completes the one-sided composition-scheme GST entries in a financial year (dry run unless --apply).';
    protected $usage       = 'books:repair-composition --company ID --fy ID [--branch ID] [--to YYYY-MM-DD] [--csv FILE] [--no-rounding] [--apply]';
    protected $options     = [
        '--company'     => 'Company id (cmp_id).',
        '--fy'          => 'Financial year id (cmpfymastr_id).',
        '--branch'      => 'Branch id (hobo_id); every branch of the company when left out.',
        '--to'          => 'Last date to include; the end of the financial year when left out.',
        '--csv'         => 'Write one line per planned change to this file.',
        '--no-rounding' => 'Leave differences of 0.05 or less alone instead of putting them on the largest tax leg.',
        '--fy-start'    => 'Start of the financial year, if the year master cannot be read from here.',
        '--fy-end'      => 'End of the financial year, if the year master cannot be read from here.',
        '--apply'       => 'Commit the changes. Without this nothing is written.',
    ];

    private $db;
    private int $cmp = 0;
    private int $fy = 0;
    private ?int $bo = null;

    public function run(array $params)
    {
        $cmp = (int)($this->opt('company') ?? 0);
        $fy  = (int)($this->opt('fy') ?? 0);
        if ($cmp <= 0 || $fy <= 0) {
            CLI::error('Both --company and --fy are required.');
            CLI::write('  ' . $this->usage);
            CLI::write('  php spark audit:books --list   lists the companies and their financial years');
            return 1;
        }
        $this->cmp = $cmp;
        $this->fy  = $fy;
        $this->bo  = $this->opt('branch') !== null ? (int)$this->opt('branch') : null;
        $this->db  = Database::connect();

        $fyRow = $this->fyRow($cmp, $fy);
        if (!$fyRow) {
            CLI::error("Could not read financial year $fy of company $cmp.");
            CLI::write('  php spark audit:books --list   lists the companies and their financial years');
            CLI::write('  or give the dates directly:    --fy-start 2024-04-01 --fy-end 2025-03-31');
            return 1;
        }
        $from = date('Y-m-d', strtotime((string)$fyRow['fy_beg_date']));
        $to   = date('Y-m-d', strtotime((string)($this->opt('to') ?: $fyRow['fy_end_date'])));

        $apply    = $this->opt('apply') !== null;
        $rounding = $this->opt('no-rounding') === null;

        $this->heading('REPAIR of composition-scheme GST entries  company ' . $cmp . '  fy ' . $fy
            . '  branch ' . ($this->bo === null ? 'ALL' : $this->bo));
        CLI::write('  period: ' . $from . ' .. ' . $to . '    mode: ' . ($apply ? 'APPLY (changes are committed)' : 'DRY RUN (nothing is written)'));
        CLI::write('  every change is taken from the rows the vouchers already carry; a difference the data cannot explain is left alone');
        CLI::newLine();

        $posting = new CompositionPosting($this->db, $cmp);
        if ($posting->gstPaidAccount() === null) {
            CLI::error('This company has no restricted "GST PAID A/C" ledger, so it has no composition-scheme entries to repair.');
            return 1;
        }
        $ids = $posting->vouchersInWindow($from, $to, $this->bo);
        if (!$ids) { CLI::write('  no vouchers in this window.'); return 0; }

        $before = $posting->imbalance($from, $to, $this->bo);
        $plan   = $posting->plan($ids, $rounding);
        CLI::write('  ledger before        : debit - credit = ' . $this->drcr($before));
        CLI::write('  vouchers looked at   : ' . count($ids) . ' in ' . $plan['groups'] . ' entry group(s)');
        CLI::write('  entries to complete  : ' . count($plan['fixed']) . '  (' . count($plan['actions']) . ' ledger row(s))');
        CLI::write('  left for review      : ' . count($plan['review']));
        if (!empty($plan['degraded'])) {
            CLI::write('  [WARN] the bill-sundry master could not be read, so most rules are switched off', 'yellow');
        }
        CLI::newLine();

        $this->showByRule($plan['actions']);
        $this->showChanges($plan['actions']);
        $this->showReview($plan['review']);

        if ($this->opt('csv') !== null) { $this->writeCsv((string)$this->opt('csv'), $plan); }

        $bsBefore = $this->bsTotals($fyRow, $from, $to);

        if (!$plan['actions']) {
            $this->reconciliation($bsBefore, null, $before, $before, $plan);
            CLI::write('  Nothing to change.' . ($plan['review'] ? ' Every remaining difference needs a person to decide.' : ''), 'green');
            return 0;
        }

        // --------------------------------------------------------------------------------------------
        //  measure the result for real, then keep it or roll it back
        // --------------------------------------------------------------------------------------------
        $this->db->transBegin();
        $counts  = $posting->apply($plan['actions']);
        $after   = $posting->imbalance($from, $to, $this->bo);
        $left    = $posting->unbalanced($ids);
        $ok      = (count($left) === count($plan['review']));
        $bsAfter = $this->bsTotals($fyRow, $from, $to, true);

        $this->heading('RESULT');
        CLI::write('  rows inserted / updated / deleted : ' . $counts['inserted'] . ' / ' . $counts['updated'] . ' / ' . $counts['deleted']);
        CLI::write('  ledger before        : debit - credit = ' . $this->drcr($before));
        CLI::write('  ledger after         : debit - credit = ' . $this->drcr($after));
        CLI::write('  entry groups still unbalanced : ' . count($left) . ' (all of them are the ones listed for review: '
            . ($ok ? 'yes' : 'NO - see below') . ')');
        if (!$ok) {
            foreach ($left as $g) {
                CLI::write('    voucher(s) ' . implode(' + ', $g['group']) . '  difference ' . $this->drcr($g['difference']), 'yellow');
            }
        }

        $this->reconciliation($bsBefore, $bsAfter, $before, $after, $plan);

        if (!$apply) {
            $this->db->transRollback();
            CLI::newLine();
            CLI::write('  DRY RUN: everything above was rolled back; the database is exactly as it was.', 'yellow');
            CLI::write('  Take a database backup, have the figures agreed, then run the same command with --apply.');
            return 0;
        }
        if (!$ok) {
            $this->db->transRollback();
            CLI::newLine();
            CLI::error('  Rolled back: the verification did not pass, so nothing was changed.');
            return 1;
        }
        $this->db->transCommit();
        CLI::newLine();
        CLI::write('  APPLIED and committed. Re-run `php spark audit:books` to see the reports on the repaired books.', 'green');
        CLI::write('  What is left is only what needs a person: ' . count($plan['review']) . ' entry group(s).');
        return 0;
    }

    // ==================================================================================================
    //  the financial year
    // ==================================================================================================

    /**
     * The financial year's start and end dates. The year master lives in the separate company database
     * (`univaictly`) on a live installation and in the main one on some others, so both are tried; failing
     * that, --fy-start and --fy-end can be given on the command line. Only these two dates are used, and
     * the whole repair works on the ledger of the main database.
     *
     * @return array{fy_beg_date: string, fy_end_date: string}|null
     */
    private function fyRow(int $cmp, int $fy): ?array
    {
        foreach ([fn() => $this->univ(), fn() => $this->db] as $conn) {
            try {
                $c = $conn();
                if (!$c) { continue; }
                $r = $c->table('cmpfymastr')->where('cmp_id', $cmp)->where('cmpfymastr_id', $fy)->get()->getRowArray();
                if ($r && !empty($r['fy_beg_date']) && !empty($r['fy_end_date'])) { return $r; }
            } catch (\Throwable $e) { /* try the next connection */ }
        }
        $s = $this->opt('fy-start'); $e = $this->opt('fy-end');
        if (is_string($s) && $s !== '' && is_string($e) && $e !== '' && strtotime($s) && strtotime($e)) {
            CLI::write('  [INFO] the financial year master could not be read here; using the dates given on the command line', 'yellow');
            return ['fy_beg_date' => $s, 'fy_end_date' => $e];
        }
        return null;
    }

    /** The separate company / financial-year database, or null when this installation has none. */
    private function univ()
    {
        static $u = null;
        if ($u === null) {
            try { $u = (new \App\Libraries\externaldb())->univaictly_db(); } catch (\Throwable $e) { $u = false; }
        }
        return $u ?: null;
    }

    // ==================================================================================================
    //  balance sheet reconciliation
    // ==================================================================================================

    /**
     * The Balance Sheet totals, read through the same model the page uses, so the figures are the ones on the
     * screen and not a second calculation of my own. Returns null when the reports cannot be built from here;
     * the repair itself does not depend on them.
     *
     * In PostgreSQL one failed statement aborts the whole transaction, and every statement after it fails too.
     * The reports read many tables this command does not need, so inside the transaction the read is fenced with
     * a savepoint: if anything in the reporting stack fails, only the read is undone and the repair - which is
     * the part that matters - still commits or rolls back on its own terms.
     *
     * @param array{fy_beg_date: string, fy_end_date: string} $fyRow
     * @return array{liab: float, asset: float, pl: float}|null
     */
    private function bsTotals(array $fyRow, string $from, string $to, bool $inTransaction = false): ?array
    {
        if ($inTransaction) { $this->db->simpleQuery('SAVEPOINT bs_reconciliation'); }
        $out = $this->readBsTotals($fyRow, $from, $to);
        if ($inTransaction) {
            $this->db->simpleQuery($out === null ? 'ROLLBACK TO SAVEPOINT bs_reconciliation' : 'RELEASE SAVEPOINT bs_reconciliation');
        }
        return $out;
    }

    /**
     * @param array{fy_beg_date: string, fy_end_date: string} $fyRow
     * @return array{liab: float, asset: float, pl: float}|null
     */
    private function readBsTotals(array $fyRow, string $from, string $to): ?array
    {
        try {
            // merged, not replaced: whatever else the installation keeps in the session is left alone
            $_SESSION = array_merge(is_array($_SESSION ?? null) ? $_SESSION : [], [
                'ses_company_id'           => $this->cmp,
                'ses_comp_fy_id'           => $this->fy,
                'ses_boid'                 => $this->bo ?? 0,
                'ses_company_fy_beginning' => (string)$fyRow['fy_beg_date'],
                'ses_company_fy_end'       => (string)$fyRow['fy_end_date'],
                'ses_dflt_val_method'      => $fyRow['def_val_method'] ?? 'AVG',
                'ses_company_name'         => (string)($_SESSION['ses_company_name'] ?? ''),
            ]);
            // A repair over every branch is the report's "Consolidated In All Branches"; one branch is that branch.
            $rows = (new ReportingModel())->load_balance_sheet_view(1, 1, $from, $to, 1, $this->bo === null);
            $last = end($rows);
            if (!is_array($last) || !isset($last['l_balance_total'], $last['r_balance_total'])) { return null; }
            $pl = 0.0;
            foreach ($rows as $r) {
                if (($r['l_type'] ?? '') === 'pl' && ($r['l_group_name'] ?? '') === 'Profit / Loss') { $pl = (float)$r['l_balance_total']; }
            }
            return ['liab' => (float)$last['l_balance_total'], 'asset' => (float)$last['r_balance_total'], 'pl' => $pl];
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Ties the Balance Sheet on the screen to the vouchers behind it, in three steps:
     *   1  is the report adding up correctly?  Its difference must be the ledger's own debit-minus-credit.
     *      If they agree the arithmetic is right and the ledger is what is wrong; if not, the gap between them
     *      is a reporting problem and is named as one.
     *   2  what that difference is made of: the part the vouchers' own rows account for, and the part that
     *      needs a person. The two are measured (ledger before minus ledger after), not estimated, and their
     *      sum is checked back against step 1.
     *   3  what the Balance Sheet reads once the first part is completed - the figures are taken from the real
     *      thing inside the transaction that is about to be rolled back.
     * Nothing here changes a figure or proposes a balancing entry.
     *
     * @param array{liab: float, asset: float, pl: float}|null $bsBefore
     * @param array{liab: float, asset: float, pl: float}|null $bsAfter   null when nothing would be changed
     * @param array{fixed: array<mixed>, review: array<mixed>}            $plan
     */
    /**
     * Does the Balance Sheet's own difference account for the ledger's, or is part of it the report's doing?
     * The two are measured from opposite sides - liabilities minus assets against debit minus credit - so they
     * are equal and opposite when the report is merely showing what the ledger holds. Half a paisa per side is
     * allowed for the rounding every ledger balance carries.
     */
    private function reportAgreesWithLedger(float $bsDifference, float $ledgerDifference): bool
    {
        return abs(round($bsDifference + $ledgerDifference, 2)) < 0.02;
    }

    private function reconciliation(?array $bsBefore, ?array $bsAfter, float $before, float $after, array $plan): void
    {
        $this->heading('BALANCE SHEET RECONCILIATION');

        CLI::write('  1. Is the Balance Sheet calculating wrongly, or is the ledger out of balance?');
        if ($bsBefore === null) {
            CLI::write('     the Balance Sheet could not be built from here, so this step is unproven; the ledger figures below still hold.', 'yellow');
        } else {
            $bsDiff = round($bsBefore['liab'] - $bsBefore['asset'], 2);
            CLI::write(sprintf('     Balance Sheet   liabilities %18s   assets %18s   difference %18s', $this->n($bsBefore['liab']), $this->n($bsBefore['asset']), $this->drcr(-$bsDiff)));
            CLI::write(sprintf('     Ledger          debit minus credit of every posted row%48s', $this->drcr($before)));
            $gap = round($bsDiff + $before, 2);
            if ($this->reportAgreesWithLedger($bsDiff, $before)) {
                CLI::write('     Both are the same figure, so the report adds up correctly. It cannot tally because the ledger it reads', 'green');
                CLI::write('     does not obey double entry: that is a posting problem, not a reporting one and not an accounting difference.', 'green');
            } else {
                CLI::write('     They are NOT the same: ' . $this->n(abs($gap)) . ' of the difference is not in the ledger at all, so that much is a', 'yellow');
                CLI::write('     reporting problem and has to be found in the report code, not in the vouchers.', 'yellow');
            }
        }

        CLI::newLine();
        CLI::write('  2. What the ledger difference is made of');
        // Two independent figures, deliberately: what the rules say they close (added up from the plan) and what
        // the ledger actually moved when the plan was applied. They must be the same, and the first one plus what
        // is left must be the whole difference. If either does not hold, the run says so and is not to be acted on.
        $planned = 0.0;
        foreach ($plan['actions'] as $a) { $planned += (float)($a['delta'] ?? 0.0); }
        $planned  = round(-$planned, 2);
        $measured = round($before - $after, 2);
        CLI::write(sprintf('     %5d entr%-3s whose own rows show which leg is missing (the posting defect) %18s',
            count($plan['fixed']), count($plan['fixed']) === 1 ? 'y' : 'ies', $this->drcr($planned)));
        CLI::write(sprintf('     %5d entry group(s) whose rows do not show it, for a person to decide      %18s',
            count($plan['review']), $this->drcr($after)));
        $sum = round($planned + $after, 2);
        CLI::write(sprintf('     %5s                                                                      %18s   %s', '', $this->drcr($sum),
            abs($sum - $before) < 0.005 ? 'exactly the difference in step 1' : 'DOES NOT match step 1 - do not act on this run'));
        if (abs($planned - $measured) >= 0.005) {
            CLI::write('     [WARN] completing those entries moved the ledger by ' . $this->drcr($measured) . ', not the '
                . $this->drcr($planned) . ' the rules planned: something else changed these rows. Do not act on this run.', 'yellow');
        }

        CLI::newLine();
        CLI::write('  3. What the Balance Sheet reads once the first line of step 2 is completed');
        if ($bsAfter === null) {
            CLI::write('     nothing can be completed from the stored rows, so the figures above are unchanged.', 'yellow');
        } else {
            $d = round($bsAfter['liab'] - $bsAfter['asset'], 2);
            CLI::write(sprintf('     Balance Sheet   liabilities %18s   assets %18s   difference %18s', $this->n($bsAfter['liab']), $this->n($bsAfter['asset']), $this->drcr(-$d)));
            if ($bsBefore !== null) {
                CLI::write(sprintf('     Profit / Loss   %18s   (it reads %s today)', $this->n($bsAfter['pl']), $this->n($bsBefore['pl'])));
            }
            if (abs($d) < 0.005) {
                CLI::write('     The Balance Sheet tallies. Every difference was an incomplete entry that the vouchers themselves accounted for.', 'green');
            } else {
                CLI::write('     ' . $this->drcr(-$d) . ' is left. It is the ' . count($plan['review']) . ' entry group(s) listed above, and it is the only part of the', 'yellow');
                CLI::write('     difference the stored data cannot explain - each one has to be looked at before anyone calls it a genuine difference.', 'yellow');
            }
        }
        CLI::newLine();
        CLI::write('  Measured on the real ledger; no figure here was adjusted and no balancing entry was invented.');
    }

    // ==================================================================================================
    //  output
    // ==================================================================================================

    private function showByRule(array $actions): void
    {
        if (!$actions) { return; }
        $names = [
            'J' => 'GST PAID A/C set to what balances its own composition journal',
            'Z' => 'a GST PAID A/C leg posted as 0.00 given the amount that balances the entry',
            'M' => 'the mirror of the tax legs the difference equals, on the same ledgers',
            'S' => 'GST PAID A/C debited with the tax the voucher\'s own GST summary records',
            'R' => 'rounding between a tax total and its components',
        ];
        // "closes" is the effect on the ledger, not the size of the row written: correcting a leg from 7,323.75
        // to 7,323.76 writes 7,323.76 but closes one paisa, and a mirror on the credit side closes a negative
        // amount. Added up, this column is the first line of step 2 of the reconciliation.
        $by = [];
        foreach ($actions as $a) {
            $r = $a['rule'];
            $by[$r]['rows'] = ($by[$r]['rows'] ?? 0) + 1;
            $by[$r]['net']  = ($by[$r]['net'] ?? 0) - (float)($a['delta'] ?? 0.0);
        }
        ksort($by);
        $net = 0.0;
        CLI::write('  by rule, and what each closes of the difference:');
        foreach ($by as $r => $x) {
            $net += $x['net'];
            CLI::write(sprintf('         %s  %5d row(s)  %18s   %s', $r, $x['rows'], $this->drcr(round($x['net'], 2)), $names[$r] ?? ''));
        }
        CLI::write(sprintf('         %s  %5d row(s)  %18s', ' ', count($actions), $this->drcr(round($net, 2))));
        CLI::newLine();
    }

    private function showChanges(array $actions): void
    {
        if (!$actions) { return; }
        $names = $this->accountNames(array_column($actions, 'acc'));
        $show  = array_slice($actions, 0, 40);
        CLI::write('  the changes (first ' . count($show) . ' of ' . count($actions) . '; --csv writes them all):');
        foreach ($show as $a) {
            $what = $a['kind'] === 'insert_row' ? 'add   ' : ($a['kind'] === 'update_row' ? 'change' : 'remove');
            $line = sprintf('         %s  voucher %-9s %s %14s  %s', $what, $a['vch'],
                ((int)$a['side'] === 1 ? 'Dr' : 'Cr'), $this->n((float)$a['amount']),
                $names[(int)$a['acc']] ?? ('acc ' . $a['acc']));
            if ($a['kind'] === 'update_row') {
                $line .= '   (was ' . ((int)$a['was_side'] === 1 ? 'Dr' : 'Cr') . ' ' . $this->n((float)$a['was_amount']) . ')';
            }
            CLI::write($line . '  [' . $a['rule'] . ']');
        }
        CLI::newLine();
    }

    private function showReview(array $review): void
    {
        if (!$review) { return; }
        $by = [];
        foreach ($review as $r) { $by[$r['why']]['n'] = ($by[$r['why']]['n'] ?? 0) + 1; $by[$r['why']]['d'] = ($by[$r['why']]['d'] ?? 0) + $r['difference']; }
        CLI::write('  left exactly as it is, for a person to decide:', 'yellow');
        foreach ($by as $why => $x) {
            CLI::write(sprintf('         %4d entry group(s)  %16s   %s', $x['n'], $this->drcr($x['d']), $why));
        }
        $show = array_slice($review, 0, 15);
        foreach ($show as $r) {
            CLI::write('         voucher(s) ' . implode(' + ', $r['group']) . '  difference ' . $this->drcr($r['difference']));
        }
        if (count($review) > count($show)) { CLI::write('         ... and ' . (count($review) - count($show)) . ' more (in the CSV)'); }
        CLI::newLine();
    }

    private function writeCsv(string $file, array $plan): void
    {
        $fh = @fopen($file, 'w');
        if (!$fh) { CLI::error('  could not write ' . $file); return; }
        $names = $this->accountNames(array_column($plan['actions'], 'acc'));
        fputcsv($fh, ['what', 'rule', 'vch_txn_id', 'account_id', 'account', 'side', 'amount', 'was_side', 'was_amount', 'date', 'why']);
        foreach ($plan['actions'] as $a) {
            fputcsv($fh, [
                $a['kind'] === 'insert_row' ? 'add row' : ($a['kind'] === 'update_row' ? 'change row' : 'remove row'),
                $a['rule'], $a['vch'], $a['acc'], $names[(int)$a['acc']] ?? '',
                (int)$a['side'] === 1 ? 'Dr' : 'Cr', number_format((float)$a['amount'], 2, '.', ''),
                isset($a['was_side']) ? ((int)$a['was_side'] === 1 ? 'Dr' : 'Cr') : '',
                isset($a['was_amount']) ? number_format((float)$a['was_amount'], 2, '.', '') : '',
                $a['date'] ?? '', $a['why'],
            ]);
        }
        foreach ($plan['review'] as $r) {
            fputcsv($fh, ['for review', '', implode(' + ', $r['group']), '', '', '', '', '', '',
                          '', $r['why'] . ' (difference ' . number_format($r['difference'], 2, '.', '') . ')']);
        }
        fclose($fh);
        CLI::write('  [INFO] wrote ' . (count($plan['actions']) + count($plan['review'])) . ' line(s) to ' . $file);
        CLI::newLine();
    }

    /** @return array<int,string> */
    private function accountNames(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', array_filter($ids))));
        if (!$ids) { return []; }
        $out = [];
        foreach (array_chunk($ids, 500) as $chunk) {
            foreach ($this->db->table('acctmaster')->select('acc_id, acc_name')->where('cmp_id', $this->cmp)
                         ->whereIn('acc_id', $chunk)->get()->getResultArray() as $r) {
                $out[(int)$r['acc_id']] = trim(strip_tags((string)$r['acc_name']));
            }
        }
        return $out;
    }

    // ==================================================================================================
    //  small helpers
    // ==================================================================================================

    /** Reads --opt value and --opt=value, and returns null when the option is absent. */
    private function opt(string $name): ?string
    {
        $argv = $_SERVER['argv'] ?? [];
        foreach ($argv as $i => $a) {
            if ($a === '--' . $name) {
                $next = $argv[$i + 1] ?? null;
                return ($next === null || str_starts_with($next, '--')) ? '' : (string)$next;
            }
            if (str_starts_with($a, '--' . $name . '=')) { return substr($a, strlen($name) + 3); }
        }
        return null;
    }

    private function n(float $v): string { return number_format($v, 2, '.', ','); }

    private function drcr(float $v): string
    {
        if (abs($v) < 0.005) { return '0.00'; }
        return $this->n(abs($v)) . ($v < 0 ? ' Cr' : ' Dr');
    }

    private function heading(string $t): void
    {
        CLI::newLine();
        CLI::write(str_repeat('=', 100));
        CLI::write($t);
        CLI::write(str_repeat('=', 100));
    }
}
