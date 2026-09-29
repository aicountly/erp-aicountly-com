<?php namespace App\Commands;

use App\Libraries\CompositionPosting;
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

        $fyRow = $this->db->table('cmpfymastr')->where('cmp_id', $cmp)->where('cmpfymastr_id', $fy)->get()->getRowArray();
        if (!$fyRow || empty($fyRow['fy_beg_date']) || empty($fyRow['fy_end_date'])) {
            CLI::error("No financial year $fy for company $cmp (or it has no start / end date).");
            CLI::write('  php spark audit:books --list   lists the companies and their financial years');
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

        if (!$plan['actions']) {
            CLI::write('  Nothing to change.' . ($plan['review'] ? ' Every remaining difference needs a person to decide.' : ''), 'green');
            return 0;
        }

        // --------------------------------------------------------------------------------------------
        //  measure the result for real, then keep it or roll it back
        // --------------------------------------------------------------------------------------------
        $this->db->transBegin();
        $counts = $posting->apply($plan['actions']);
        $after  = $posting->imbalance($from, $to, $this->bo);
        $left   = $posting->unbalanced($ids);
        $ok     = (count($left) === count($plan['review']));

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
        $by = [];
        foreach ($actions as $a) {
            $r = $a['rule'];
            $by[$r]['rows'] = ($by[$r]['rows'] ?? 0) + 1;
            $by[$r]['amt']  = ($by[$r]['amt'] ?? 0) + (float)$a['amount'];
        }
        ksort($by);
        CLI::write('  by rule:');
        foreach ($by as $r => $x) {
            CLI::write(sprintf('         %s  %5d row(s)  %16s   %s', $r, $x['rows'], $this->n($x['amt']), $names[$r] ?? ''));
        }
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
