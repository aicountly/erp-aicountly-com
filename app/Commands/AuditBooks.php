<?php
namespace App\Commands;

use App\Libraries\AccountingEngine;
use App\Libraries\LedgerSnapshot;
use App\Libraries\ReportSheetWriter;
use App\Models\Admin\ReportingModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * php spark audit:books --list
 * php spark audit:books --company ID --fy ID --branch ID [--to YYYY-MM-DD] [--limit 15] [--json FILE]      ("--company=ID" works too)
 *
 * READ ONLY. Everything runs inside "BEGIN READ ONLY" (PostgreSQL refuses any write) and is rolled back at the end.
 * It never adjusts a figure: it measures the ledger as stored and says, from the data, why a report does or does not tally.
 *
 * What it checks for one company / financial year / branch:
 *   1  what is in the ledger (row types, pending / rejected / memo rows, opening mirror rows)
 *   2  vouchers whose debit and credit rows differ (by voucher type - GST PAID A/C system journals, pending approvals, ...)
 *   3  ledgers the group structure cannot place; sub-groups under profit & loss categories
 *   4  opening balances: profit & loss ledgers that carry one, bill-sundry openings, opening stock, "Difference in Opening"
 *   5  the year-end carry-forward: previous year's closing against this year's opening, and an exact attribution of the
 *      "Difference in Opening" to its causes
 *   6  the corrected Trial Balance / Profit & Loss / Balance Sheet in every view, and the identities that must hold
 *   7  the previous (old) calculation next to the corrected one
 *   8  Excel / CSV parity: the workbooks are built and read back and compared with the rows the page shows
 *
 * Section 2 also groups each unbalanced voucher with the vouchers it is linked to (vchbridgen: a composition-scheme
 * purchase / sale and its GST PAID A/C system journal) and reports what is still unbalanced as a group.
 * --vouchers [N] / --voucher ID,ID print the ledger rows of such vouchers; --csv FILE lists all of them for the accountant.
 */
class AuditBooks extends BaseCommand
{
    protected $group       = 'Audit';
    protected $name        = 'audit:books';
    protected $description = 'Read-only audit of one company / financial year / branch: ledger integrity, year-end carry-forward, Trial Balance, Profit & Loss, Balance Sheet (old vs corrected) and Excel-vs-screen parity.';
    protected $usage       = 'audit:books --list | audit:books --company ID --fy ID [--branch ID | --consolidated] [--to YYYY-MM-DD] [--limit N] [--json FILE] [--vouchers [N]] [--voucher ID,ID] [--csv FILE] [--no-legacy] [--no-excel] [--no-continuity]';
    protected $options     = [
        '--list'           => 'List companies, financial years, branches and ledger volume, then stop.',
        '--company'        => 'Company id (cmp_id).',
        '--fy'             => 'Financial year id (cmpfymastr_id).',
        '--branch'         => 'Branch id (hobo_id). Optional when the company has a single branch.',
        '--consolidated'   => 'All branches together (the "Consolidated In All Branches" tick).',
        '--from'           => 'Report period start (default: FY start).',
        '--to'             => 'Report date (default: FY end).',
        '--limit'          => 'Rows shown in each detail list (default 15).',
        '--json'           => 'Also write the findings to this JSON file.',
        '--vouchers'       => 'Show, row by row, the N (default 3) largest vouchers that are still unbalanced after adding the vouchers they are linked to, and N that balance thanks to a link.',
        '--voucher'        => 'Show the ledger rows, GST summary and linked vouchers of these voucher ids (comma separated).',
        '--csv'            => 'Write every voucher whose debit and credit rows differ to this CSV file (with its linked vouchers and whether they balance together).',
        '--no-legacy'      => 'Skip the old-calculation comparison.',
        '--no-excel'       => 'Skip the Excel/CSV read-back test.',
        '--no-continuity'  => 'Skip the previous-year carry-forward check.',
    ];

    /** vchbridgen link types that tie a voucher to its composition-scheme "GST PAID A/C" system journal (3 purchase, 4 sale, 5 credit note).
     *  Types 1 and 2 (credit / debit note against an invoice) link vouchers that are NOT meant to balance each other and are never added together. */
    private const COMPOSITION_LINKS = [3, 4, 5];

    /** @var \CodeIgniter\Database\BaseConnection */
    private $db;
    private int $cmp = 0, $fy = 0, $bo = 0, $limit = 15;
    private bool $cons = false;
    private string $fyStart = '', $fyEnd = '', $from = '', $to = '';
    /** @var array<string,mixed> */
    private array $R = ['checks' => []];
    private ?ReportingModel $m = null;
    private string $valMethod = 'AVG';
    /** Amounts may carry more than 2 decimals and every ledger is rounded to 2: allow half a paisa per active ledger in sums. */
    private float $tol = 0.02;

    // ======================================================================================================
    //  entry
    // ======================================================================================================

    public function run(array $params)
    {
        $this->limit = max(1, (int)($this->opt('limit') ?: 15));
        $this->db    = \Config\Database::connect();

        if ($this->opt('list') !== null) {
            return $this->listCompanies();
        }

        $this->cmp  = (int)$this->opt('company');
        $this->fy   = (int)$this->opt('fy');
        $this->cons = $this->opt('consolidated') !== null;
        if ($this->cmp <= 0 || $this->fy <= 0) {
            CLI::error('Give --company ID and --fy ID (run "php spark audit:books --list" to see them).');
            return 1;
        }

        $fyRow = $this->fyRow($this->cmp, $this->fy);
        if (!$fyRow) {
            CLI::error("Financial year {$this->fy} of company {$this->cmp} was not found.");
            return 1;
        }
        $this->fyStart   = date('Y-m-d', strtotime((string)$fyRow['fy_beg_date']));
        $this->fyEnd     = date('Y-m-d', strtotime((string)$fyRow['fy_end_date']));
        $this->valMethod = (string)($fyRow['def_val_method'] ?? 'AVG');
        $this->from      = $this->dateOpt('from', $this->fyStart);
        $this->to        = $this->dateOpt('to', $this->fyEnd);

        // branch
        $bo = $this->opt('branch');
        if ($bo === null || $bo === true || $bo === '') {
            $branches = $this->branchesWithData();
            if ($this->cons)             { $this->bo = $branches[0] ?? 0; }
            elseif (count($branches) === 1) { $this->bo = $branches[0]; }
            else {
                CLI::error('This company has data in branches [' . implode(', ', $branches) . ']. Give --branch ID (or --consolidated).');
                return 1;
            }
        } else {
            $this->bo = (int)$bo;
        }

        $this->db->simpleQuery('BEGIN READ ONLY');                 // from here on nothing can be written
        try {
            $ro = $this->db->query('SHOW transaction_read_only')->getRowArray();
            if (strtolower((string)reset($ro)) !== 'on') {
                CLI::error('Could not open a read-only transaction; nothing was run.');
                return 1;
            }
            $this->openSession($fyRow);
            try { $first = $this->snap(); }
            catch (\Throwable $e) { CLI::error('The ledger could not be read: ' . $e->getMessage()); return 1; }
            $active = 0; foreach ($first->accounts as $a) { if ($a['op'] != 0.0 || $a['pre'] != 0.0 || $a['dr'] != 0.0 || $a['cr'] != 0.0) { $active++; } }
            $this->tol = 0.02 + 0.005 * $active;
            $this->header($fyRow);
            $this->guard('ledger', fn() => $this->checkLedger());
            $this->guard('vouchers', fn() => $this->checkVouchers());
            if (is_string($this->opt('voucher')) && $this->opt('voucher') !== '') { $this->guard('voucher_detail', fn() => $this->checkVoucherIds((string)$this->opt('voucher'))); }
            $this->guard('structure', fn() => $this->checkStructure());
            $this->guard('openings', fn() => $this->checkOpenings());
            if ($this->opt('no-continuity') === null) { $this->guard('carry', fn() => $this->checkCarryForward()); }
            $this->guard('reports', fn() => $this->checkReports());
            if ($this->opt('no-legacy') === null)     { $this->guard('legacy', fn() => $this->checkLegacy()); }
            if ($this->opt('no-excel') === null)      { $this->guard('excel', fn() => $this->checkExcel()); }
            $this->verdict();
        } finally {
            $this->db->simpleQuery('ROLLBACK');
        }

        $json = $this->opt('json');
        if (is_string($json) && $json !== '') {
            file_put_contents($json, json_encode($this->R, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR));
            CLI::write("Findings written to $json", 'green');
        }
        return 0;
    }

    /** Option value: spark's own parser understands "--company 1"; "--company=1" is accepted as well. true = flag given, null = absent. */
    private function opt(string $name)
    {
        $v = CLI::getOption($name);
        if ($v !== null) { return $v; }
        foreach (($_SERVER['argv'] ?? []) as $a) {
            if (strpos((string)$a, "--$name=") === 0) { return substr((string)$a, strlen($name) + 3); }
        }
        return null;
    }

    // ======================================================================================================
    //  setup
    // ======================================================================================================

    private function univ()
    {
        static $u = null;
        if ($u === null) { $u = (new \App\Libraries\externaldb())->univaictly_db(); }
        return $u;
    }

    private function fyRow(int $cmp, int $fy): ?array
    {
        foreach ([fn() => $this->univ(), fn() => $this->db] as $conn) {
            try {
                $r = $conn()->table('cmpfymastr')->where('cmp_id', $cmp)->where('cmpfymastr_id', $fy)->get()->getRowArray();
                if ($r) { return $r; }
            } catch (\Throwable $e) { /* try the next connection */ }
        }
        $s = $this->opt('fy-start'); $e = $this->opt('fy-end');
        if (is_string($s) && is_string($e)) { return ['fy_beg_date' => $s, 'fy_end_date' => $e, 'def_val_method' => 'AVG']; }
        return null;
    }

    private function dateOpt(string $name, string $default): string
    {
        $v = $this->opt($name);
        if (!is_string($v) || $v === '') { return $default; }
        $t = strtotime($v);
        return $t === false ? $default : date('Y-m-d', $t);
    }

    /** @return int[] */
    private function branchesWithData(): array
    {
        $rows = $this->db->query(
            "SELECT DISTINCT hobo_id FROM accoppybal WHERE cmp_id = ? AND cmpfymastr_id = ?
             UNION SELECT DISTINCT hobo_id FROM accttxnmst WHERE cmp_id = ? AND acc_txn_date BETWEEN ? AND ?
             ORDER BY 1",
            [$this->cmp, $this->fy, $this->cmp, $this->fyStart, $this->fyEnd])->getResultArray();
        $out = [];
        foreach ($rows as $r) { if ($r['hobo_id'] !== null) { $out[] = (int)$r['hobo_id']; } }
        return $out;
    }

    /** The session values the report models read (what a logged-in user's session holds after opening the company). */
    private function openSession(array $fyRow, ?int $fy = null, ?string $start = null, ?string $end = null): void
    {
        $name = '';
        try { $r = $this->univ()->table('cmpmastern')->select('cmp_name')->where('cmp_id', $this->cmp)->get()->getRowArray(); $name = (string)($r['cmp_name'] ?? ''); } catch (\Throwable $e) {}
        $_SESSION = [
            'ses_company_id'           => $this->cmp,
            'ses_comp_fy_id'           => $fy ?? $this->fy,
            'ses_boid'                 => $this->bo,
            'ses_company_fy_beginning' => $start ?? $this->fyStart,
            'ses_company_fy_end'       => $end ?? $this->fyEnd,
            'ses_dflt_val_method'      => $fyRow['def_val_method'] ?? 'AVG',
            'ses_company_name'         => $name,
        ];
        $this->m = new ReportingModel();
    }

    private function engine(): AccountingEngine { return $this->m->engine(); }

    private function snap(): LedgerSnapshot { return $this->engine()->snapshot($this->from, $this->to, $this->cons); }

    private function b(string $alias = 'a'): string { return $this->cons ? '' : " AND {$alias}.hobo_id = " . (int)$this->bo; }

    // ======================================================================================================
    //  output helpers
    // ======================================================================================================

    private function n(float $x): string { return number_format($x, 2, '.', ','); }

    /** plain signed number (for differences between two sides, where Dr/Cr would be misleading) */
    private function sn(float $x): string { return ($x < 0 ? '-' : ($x > 0 ? '+' : '')) . $this->n(abs($x)); }

    private function drcr(float $x): string { return $this->n(abs($x)) . ($x < 0 ? ' Cr' : ($x > 0 ? ' Dr' : '')); }

    private function heading(string $t): void { CLI::newLine(); CLI::write(str_repeat('=', 100), 'light_gray'); CLI::write($t, 'white'); CLI::write(str_repeat('=', 100), 'light_gray'); }

    /** @param string $st OK | WARN | FAIL | INFO */
    private function check(string $id, string $st, string $text, array $data = []): void
    {
        $colour = ['OK' => 'green', 'WARN' => 'yellow', 'FAIL' => 'red', 'INFO' => 'light_gray'][$st] ?? null;
        CLI::write(sprintf('  [%-4s] %s', $st, $text), $colour);
        $this->R['checks'][] = ['id' => $id, 'status' => $st, 'text' => $text, 'data' => $data];
    }

    /** @param array<int,array<string,mixed>> $rows @param array<string,string> $cols key => title (prefix '>' = right aligned number) */
    private function table(array $rows, array $cols, ?int $limit = null): void
    {
        $limit = $limit ?? $this->limit;
        if (!$rows) { return; }
        $w = [];
        foreach ($cols as $k => $t) { $w[$k] = mb_strlen(ltrim($t, '>')); }
        $shown = array_slice($rows, 0, $limit);
        foreach ($shown as $r) { foreach ($cols as $k => $t) { $w[$k] = min(52, max($w[$k], mb_strlen((string)($r[$k] ?? '')))); } }
        $fmt = function (array $r) use ($cols, $w): string {
            $cells = [];
            foreach ($cols as $k => $t) {
                $v = mb_substr((string)($r[$k] ?? ''), 0, 52);
                $cells[] = $t[0] === '>' ? str_repeat(' ', max(0, $w[$k] - mb_strlen($v))) . $v : $v . str_repeat(' ', max(0, $w[$k] - mb_strlen($v)));
            }
            return '         ' . implode('  ', $cells);
        };
        CLI::write($fmt(array_map(fn($t) => ltrim($t, '>'), $cols)), 'light_gray');
        foreach ($shown as $r) { CLI::write($fmt($r)); }
        if (count($rows) > $limit) { CLI::write('         ... ' . (count($rows) - $limit) . ' more (raise --limit)', 'light_gray'); }
    }

    /** Runs one section; an error in it is reported and the audit carries on (savepoint keeps the transaction usable). */
    private function guard(string $name, callable $fn): void
    {
        $this->db->simpleQuery('SAVEPOINT audit_sp');
        try {
            $fn();
            $this->db->simpleQuery('RELEASE SAVEPOINT audit_sp');
        } catch (\Throwable $e) {
            $this->db->simpleQuery('ROLLBACK TO SAVEPOINT audit_sp');
            $this->check($name . '_error', 'FAIL', "Section '$name' could not be completed: " . $e->getMessage());
        }
    }

    private function listCompanies(): int
    {
        // With --company ID only that company is read (much lighter on a large multi-company database).
        $only = (int)$this->opt('company');
        $this->heading('Companies, financial years, branches (read only)' . ($only ? "  - company $only" : ''));
        $cos = []; $fys = []; $bos = [];
        try {
            $u = $this->univ();
            $q = fn($t, $cols, $order) => (function () use ($u, $t, $cols, $order, $only) { $b = $u->table($t)->select($cols); if ($only) { $b->where('cmp_id', $only); } foreach ($order as $o) { $b->orderBy($o); } return $b->get()->getResultArray(); })();
            $cos = $q('cmpmastern', 'cmp_id, cmp_name', ['cmp_id']);
            $fys = $q('cmpfymastr', 'cmpfymastr_id, cmp_id, fy_beg_date, fy_end_date, is_imported', ['cmp_id', 'fy_beg_date']);
            $bos = $q('hobomaster', 'cmp_id, hobo_id, hobo_name', ['cmp_id', 'hobo_id']);
        } catch (\Throwable $e) {
            CLI::write('  (company master not readable: ' . $e->getMessage() . ')', 'yellow');
        }
        $vol = [];
        try {
            $sql = 'SELECT cmp_id, COUNT(*) n, MIN(acc_txn_date) d0, MAX(acc_txn_date) d1 FROM accttxnmst ' . ($only ? 'WHERE cmp_id = ' . $only . ' ' : '') . 'GROUP BY cmp_id ORDER BY cmp_id';
            foreach ($this->db->query($sql)->getResultArray() as $r) { $vol[(int)$r['cmp_id']] = $r; }
        } catch (\Throwable $e) {
            CLI::write('  (ledger volume not readable: ' . $e->getMessage() . ')', 'yellow');
        }
        $ids = array_unique(array_merge(array_column($cos, 'cmp_id'), array_keys($vol)));
        sort($ids);
        foreach ($ids as $id) {
            $name = ''; foreach ($cos as $c) { if ((int)$c['cmp_id'] === (int)$id) { $name = (string)$c['cmp_name']; } }
            $v = $vol[(int)$id] ?? null;
            CLI::newLine();
            CLI::write(sprintf('Company %d  %s', $id, $name), 'white');
            CLI::write('   ledger rows: ' . ($v ? $v['n'] . '  (' . $v['d0'] . ' .. ' . $v['d1'] . ')' : 'none'));
            foreach ($fys as $f) {
                if ((int)$f['cmp_id'] !== (int)$id) { continue; }
                $extra = '';
                if ($only) {                                                   // per-year volume, only when a single company was asked for
                    try {
                        $a = $this->db->query('SELECT COUNT(*) n, COUNT(DISTINCT hobo_id) b FROM accttxnmst WHERE cmp_id = ? AND acc_txn_date BETWEEN ? AND ?', [$id, date('Y-m-d', strtotime((string)$f['fy_beg_date'])), date('Y-m-d', strtotime((string)$f['fy_end_date']))])->getRowArray();
                        $o = $this->db->query('SELECT COUNT(*) n FROM accoppybal WHERE cmp_id = ? AND cmpfymastr_id = ?', [$id, (int)$f['cmpfymastr_id']])->getRowArray();
                        $extra = sprintf('   ledger rows %s (%s branch(es)), opening-balance rows %s', $a['n'], $a['b'], $o['n']);
                    } catch (\Throwable $e) { $extra = '   (volume not readable)'; }
                }
                CLI::write(sprintf('   --fy %-4d %s .. %s%s', $f['cmpfymastr_id'], $f['fy_beg_date'], $f['fy_end_date'], $extra));
            }
            foreach ($bos as $b) { if ((int)$b['cmp_id'] === (int)$id) { CLI::write(sprintf('   --branch %-3d %s', $b['hobo_id'], $b['hobo_name'])); } }
        }
        CLI::newLine();
        CLI::write('Then:  php spark audit:books --company ID --fy ID --branch ID   (add --consolidated for all branches)');
        return 0;
    }

    private function header(array $fyRow): void
    {
        $this->heading('AUDIT (read only)  company ' . $this->cmp . '  fy ' . $this->fy . '  branch ' . ($this->cons ? 'ALL (consolidated)' : $this->bo));
        CLI::write("  financial year : {$this->fyStart} .. {$this->fyEnd}    report period: {$this->from} .. {$this->to}");
        CLI::write('  php ' . PHP_VERSION . '   database ' . $this->db->getPlatform() . '   default valuation method: ' . ($fyRow['def_val_method'] ?? '?'));
        try {
            $b = $this->univ()->table('hobomaster')->where('cmp_id', $this->cmp)->where('hobo_id', $this->bo)->get()->getRowArray();
            if ($b && isset($b['hobo_gstin_type']) && $b['hobo_gstin_type'] !== '') {
                $gt = (int)$b['hobo_gstin_type'];
                CLI::write('  branch GST registration type: ' . $gt . ($gt === 2 ? ' (composition scheme: GST on purchases and sales is posted through "GST PAID A/C" system journals)' : ($gt === 1 ? ' (regular)' : '')));
                $this->R['branch_gstin_type'] = $gt;
            }
        } catch (\Throwable $e) { /* the branch master is optional here */ }
        $this->R['meta'] = ['company' => $this->cmp, 'fy' => $this->fy, 'branch' => $this->cons ? 'all' : $this->bo, 'fy_start' => $this->fyStart, 'fy_end' => $this->fyEnd,
                            'from' => $this->from, 'to' => $this->to, 'generated' => date('c')];
    }

    private function vchTypeNames(): array
    {
        static $names = null;
        if ($names === null) {
            $names = [];
            try { foreach ($this->db->query('SELECT vch_type_id, vch_name FROM vchtypemst')->getResultArray() as $r) { $names[(int)$r['vch_type_id']] = (string)$r['vch_name']; } } catch (\Throwable $e) {}
        }
        return $names;
    }

    private function vt(?int $id): string
    {
        $n = $this->vchTypeNames();
        return $id === null ? '(no voucher header)' : $id . (isset($n[$id]) ? ' ' . $n[$id] : ($id === 23 ? ' (system journal: GST PAID A/C)' : ''));
    }

    // ======================================================================================================
    //  1  what is in the ledger
    // ======================================================================================================

    private function checkLedger(): void
    {
        $this->heading('1  Ledger content  (accttxnmst, FY start .. report date)');
        $rows = $this->db->query(
            "SELECT a.acc_txn_type t, COUNT(*) n,
                    ROUND(COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END),0)::numeric, 2) dr,
                    ROUND(COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END),0)::numeric, 2) cr
               FROM accttxnmst a
              WHERE a.cmp_id = ? AND a.vch_txn_id > 0 AND a.acc_txn_date BETWEEN ? AND ? {$this->b()}
              GROUP BY a.acc_txn_type ORDER BY a.acc_txn_type", [$this->cmp, $this->fyStart, $this->to])->getResultArray();
        $label = [1 => 'posted (used by every report)', 2 => 'optional', 3 => 'memorandum', 4 => 'pending approval', 5 => 'rejected'];
        $tbl = [];
        foreach ($rows as $r) { $tbl[] = ['type' => $r['t'] . ' ' . ($label[(int)$r['t']] ?? 'other'), 'rows' => $r['n'], '>debit' => $this->n((float)$r['dr']), '>credit' => $this->n((float)$r['cr'])]; }
        $this->table($tbl, ['type' => 'acc_txn_type', '>rows' => '>rows', '>debit' => '>debit', '>credit' => '>credit'], 12);
        $this->R['ledger_types'] = $rows;
        $br = $this->db->query(
            "SELECT COALESCE(t.hobo_id, o.hobo_id) branch, COALESCE(t.n, 0) n, COALESCE(t.dr, 0) dr, COALESCE(t.cr, 0) cr, COALESCE(o.op, 0) op FROM
               (SELECT hobo_id, COUNT(*) n, SUM(CASE WHEN acc_txn_dr_cr = 1 THEN acc_txn_amt ELSE 0 END) dr, SUM(CASE WHEN acc_txn_dr_cr = 2 THEN acc_txn_amt ELSE 0 END) cr
                  FROM accttxnmst WHERE cmp_id = ? AND acc_txn_type = 1 AND vch_txn_id > 0 AND acc_txn_date BETWEEN ? AND ? GROUP BY hobo_id) t
               FULL JOIN (SELECT hobo_id, SUM(acc_op_bal) op FROM accoppybal WHERE cmp_id = ? AND cmpfymastr_id = ? GROUP BY hobo_id) o ON o.hobo_id = t.hobo_id
              ORDER BY 1", [$this->cmp, $this->fyStart, $this->to, $this->cmp, $this->fy])->getResultArray();
        $tbl = []; $odd = 0;
        foreach ($br as $r) {
            $id = $r['branch'];
            if ($id === null || (int)$id === 0) { $odd++; }
            $tbl[] = ['branch' => $id === null ? '(none)' : (string)$id, '>rows' => $r['n'], '>debit' => $this->n((float)$r['dr']), '>credit' => $this->n((float)$r['cr']),
                      '>debit - credit' => $this->drcr((float)$r['dr'] - (float)$r['cr']), '>opening balances' => $this->drcr((float)$r['op'])];
        }
        CLI::write('  by branch (posted rows; opening balances of the FY):');
        $this->table($tbl, ['branch' => 'branch', '>rows' => '>rows', '>debit' => '>debit', '>credit' => '>credit', '>debit - credit' => '>debit - credit', '>opening balances' => '>opening balances'], 12);
        $this->check('ledger_branches', $odd ? 'WARN' : 'INFO', $odd ? 'Some rows belong to branch NULL/0, which no single-branch report shows (they appear only when consolidated).' : 'Each branch above is a separate set of books; a single-branch report shows only its own rows and openings.', $br);
        $pending = 0; foreach ($rows as $r) { if ((int)$r['t'] === 4) { $pending = (int)$r['n']; } }
        $this->check('ledger_types', $pending ? 'INFO' : 'OK', $pending ? "$pending row(s) are pending approval (type 4): reports count posted rows only." : 'No pending-approval rows.');

        $r = $this->db->query(
            "SELECT COUNT(*) n, ROUND(COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt WHEN a.acc_txn_dr_cr = 2 THEN -a.acc_txn_amt ELSE 0 END),0)::numeric, 2) net
               FROM accttxnmst a WHERE a.cmp_id = ? AND (a.vch_txn_id IS NULL OR a.vch_txn_id = 0) AND a.acc_txn_date = ? {$this->b()}", [$this->cmp, $this->fyStart])->getRowArray();
        $this->check('ledger_mirror', 'INFO', "{$r['n']} opening mirror row(s) (vch_txn_id 0/NULL) dated the FY start, net " . $this->drcr((float)$r['net']) . '; reports read openings from accoppybal and ignore these rows.');

        $bad = $this->db->query(
            "SELECT COUNT(*) FILTER (WHERE a.acc_txn_dr_cr NOT IN (1,2) OR a.acc_txn_dr_cr IS NULL) badside,
                    COUNT(*) FILTER (WHERE a.acc_txn_amt IS NULL OR a.acc_txn_amt < 0) badamt,
                    COUNT(*) FILTER (WHERE h.vch_txn_id IS NULL) nohdr,
                    COUNT(*) FILTER (WHERE h.vch_txn_id IS NOT NULL AND a.acc_txn_date <> h.vch_date) datediff,
                    COUNT(*) FILTER (WHERE h.vch_txn_id IS NOT NULL AND a.hobo_id <> h.hobo_id) branchdiff
               FROM accttxnmst a LEFT JOIN vchtxnconso h ON h.vch_txn_id = a.vch_txn_id AND h.cmp_id = a.cmp_id
              WHERE a.cmp_id = ? AND a.acc_txn_type = 1 AND a.vch_txn_id > 0 AND a.acc_txn_date BETWEEN ? AND ? {$this->b()}", [$this->cmp, $this->fyStart, $this->to])->getRowArray();
        $this->check('ledger_shape', ((int)$bad['badside'] + (int)$bad['badamt']) ? 'WARN' : 'OK',
            "posted rows with a debit/credit flag other than 1/2: {$bad['badside']}; with a missing/negative amount: {$bad['badamt']} (ignored by all reports)", $bad);
        $this->check('ledger_header', ((int)$bad['nohdr'] + (int)$bad['datediff'] + (int)$bad['branchdiff']) ? 'WARN' : 'OK',
            "posted rows without a voucher header: {$bad['nohdr']}; dated differently from their header: {$bad['datediff']}; in another branch than their header: {$bad['branchdiff']}"
            . (((int)$bad['datediff'] + (int)$bad['branchdiff']) ? '  (reports use the row; the year-end carry-forward uses the header - they can differ)' : ''));
    }

    // ======================================================================================================
    //  2  vouchers that do not balance
    // ======================================================================================================

    private function checkVouchers(): void
    {
        $this->heading('2  Vouchers whose debit and credit rows differ  (posted rows, FY start .. report date)');
        $eng = $this->engine(); $s = $this->snap();
        $imb = $eng->imbalance($s);
        $rawImb = $this->rawImbalance();
        $this->R['imbalance'] = ['engine' => $imb, 'raw_sql' => $rawImb];
        $this->check('imbalance_total', abs($imb) < 0.005 ? 'OK' : 'WARN',
            'Total debit - total credit of all posted rows = ' . $this->drcr($imb) . (abs($imb - $rawImb) < $this->tol ? '   (independent SQL agrees)' : '   !! independent SQL says ' . $this->drcr($rawImb)));
        $this->voucherCounts();
        if (abs($imb) < 0.005) { return; }

        $list = $eng->unbalancedVouchers($s, 100000);
        $byType = [];
        foreach ($list as $v) { $k = (string)($v['vch_type_id'] ?? 'none'); $byType[$k]['n'] = ($byType[$k]['n'] ?? 0) + 1; $byType[$k]['diff'] = ($byType[$k]['diff'] ?? 0) + $v['diff']; $byType[$k]['abs'] = ($byType[$k]['abs'] ?? 0) + abs($v['diff']); }
        uasort($byType, fn($a, $b) => $b['abs'] <=> $a['abs']);
        $all = $this->optional(fn() => $this->db->query(
            "SELECT h.vch_type_id t, COUNT(*) n FROM vchtxnconso h
              WHERE h.cmp_id = ? AND h.vch_txn_id IN (SELECT a.vch_txn_id FROM accttxnmst a WHERE a.cmp_id = ? AND a.acc_txn_type = 1 AND a.vch_txn_id > 0 AND a.acc_txn_date BETWEEN ? AND ? {$this->b()} GROUP BY a.vch_txn_id)
              GROUP BY h.vch_type_id", [$this->cmp, $this->cmp, $this->fyStart, $this->to])->getResultArray());
        $allBy = []; foreach ((array)$all as $r) { $allBy[(string)(int)$r['t']] = (int)$r['n']; }
        $tbl = [];
        foreach ($byType as $k => $t) { $tbl[] = ['type' => $this->vt($k === 'none' ? null : (int)$k), '>vouchers' => $t['n'], '>of all' => isset($allBy[(string)$k]) ? (string)$allBy[(string)$k] : '', '>net (Dr-Cr)' => $this->drcr($t['diff']), '>gross' => $this->n($t['abs'])]; }
        CLI::write('  by voucher type (unbalanced vouchers, and how many vouchers of that type the year has in all):');
        $this->table($tbl, ['type' => 'voucher type', '>vouchers' => '>unbalanced', '>of all' => '>of all', '>net (Dr-Cr)' => '>net (Dr-Cr)', '>gross' => '>gross'], 12);
        $this->R['vouchers_by_type_total'] = $allBy;
        $this->R['imbalance_by_type'] = $byType;

        $tiny = 0; $tinyNet = 0.0;
        foreach ($list as $v) { if (abs($v['diff']) <= 0.05) { $tiny++; $tinyNet += $v['diff']; } }
        if ($tiny) { $this->check('imbalance_rounding', 'INFO', "$tiny of these voucher(s) differ by 0.05 or less each (rounding of tax / sundry lines), together " . $this->drcr($tinyNet) . '.'); }
        $gst = 0.0; foreach ($list as $v) { if ((int)$v['vch_type_id'] === 23) { $gst += $v['diff']; } }
        $this->check('imbalance_gst', 'INFO', 'Part caused by GST PAID A/C system journals (voucher type 23, composition scheme): ' . $this->drcr($gst) . '; everything else: ' . $this->drcr($imb - $gst) . '.');

        $rows = [];
        foreach (array_slice($list, 0, $this->limit) as $v) { $rows[] = ['id' => $v['vch_txn_id'], 'date' => $v['vch_date'], 'type' => $this->vt($v['vch_type_id']), '>debit' => $this->n($v['dr']), '>credit' => $this->n($v['cr']), '>diff' => $this->drcr($v['diff'])]; }
        CLI::write('  largest differences:');
        $this->table($rows, ['id' => 'vch_txn_id', 'date' => 'date', 'type' => 'type', '>debit' => '>debit', '>credit' => '>credit', '>diff' => '>diff'], $this->limit);
        $this->R['imbalance_vouchers'] = array_slice($list, 0, 200);
        $this->guard('linked_vouchers', fn() => $this->checkLinked($list));
    }

    private function rawImbalance(): float
    {
        $r = $this->db->query(
            "SELECT COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt WHEN a.acc_txn_dr_cr = 2 THEN -a.acc_txn_amt ELSE 0 END),0)::numeric t
               FROM accttxnmst a WHERE a.cmp_id = ? AND a.acc_txn_type = 1 AND a.vch_txn_id > 0 AND a.acc_txn_date BETWEEN ? AND ? {$this->b()}",
            [$this->cmp, $this->fyStart, $this->to])->getRowArray();
        return round((float)$r['t'], 2);
    }

    private function voucherCounts(): void
    {
        // how many of the unbalanced vouchers would balance if their pending-approval legs (type 4) were counted
        $sql = "SELECT COUNT(*) n FROM (SELECT a.vch_txn_id FROM accttxnmst a
                 WHERE a.cmp_id = ? AND a.vch_txn_id > 0 AND a.acc_txn_type IN (%s) AND a.acc_txn_date BETWEEN ? AND ? {$this->b()}
                 GROUP BY a.vch_txn_id
                 HAVING ABS(SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) - SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END)) > 0.005) x";
        $only1 = (int)$this->db->query(sprintf($sql, '1'), [$this->cmp, $this->fyStart, $this->to])->getRow()->n;
        $with4 = (int)$this->db->query(sprintf($sql, '1,4'), [$this->cmp, $this->fyStart, $this->to])->getRow()->n;
        $multi = (int)$this->db->query("SELECT COUNT(*) n FROM (SELECT a.vch_txn_id FROM accttxnmst a WHERE a.cmp_id = ? AND a.vch_txn_id > 0 AND a.acc_txn_type = 1 AND a.acc_txn_date BETWEEN ? AND ? {$this->b()} GROUP BY a.vch_txn_id HAVING COUNT(DISTINCT a.hobo_id) > 1) x",
            [$this->cmp, $this->fyStart, $this->to])->getRow()->n;
        $this->check('vouchers_unbalanced', $only1 ? 'WARN' : 'OK', "$only1 voucher(s) do not balance on posted rows; $with4 would still not balance if pending-approval legs were counted" . ($only1 > $with4 ? ' (' . ($only1 - $with4) . ' differ only because one leg awaits approval)' : '') . '.');
        $this->check('vouchers_multibranch', $multi ? 'WARN' : 'OK', "$multi voucher(s) have rows in more than one branch" . ($multi && !$this->cons ? ' (a single-branch report sees only part of them)' : '') . '.');

        // GST PAID A/C
        $acc = $this->db->query("SELECT m.acc_id, m.acc_name FROM acctmaster m WHERE m.cmp_id = ? AND LOWER(TRIM(m.acc_name)) IN ('gst paid a/c', 'gst paid') ORDER BY m.acc_id", [$this->cmp])->getResultArray();
        if (!$acc) { $this->check('gst_paid', 'INFO', "No ledger named 'GST PAID A/C' in this company."); return; }
        foreach ($acc as $g) {
            $r = $this->db->query(
                "SELECT COUNT(*) n, COUNT(DISTINCT a.vch_txn_id) v,
                        ROUND(COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END),0)::numeric, 2) dr,
                        ROUND(COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END),0)::numeric, 2) cr
                   FROM accttxnmst a WHERE a.cmp_id = ? AND a.acc_id = ? AND a.acc_txn_type = 1 AND a.vch_txn_id > 0 AND a.acc_txn_date BETWEEN ? AND ? {$this->b()}",
                [$this->cmp, (int)$g['acc_id'], $this->fyStart, $this->to])->getRowArray();
            $this->check('gst_paid', 'INFO', "Ledger '{$g['acc_name']}' (id {$g['acc_id']}): {$r['n']} row(s) in {$r['v']} voucher(s), debit " . $this->n((float)$r['dr']) . ', credit ' . $this->n((float)$r['cr']) . '.', $r);
        }
        $t = $this->db->query(
            "SELECT COUNT(*) v, COUNT(*) FILTER (WHERE ABS(x.dr - x.cr) > 0.005) unbal, ROUND(COALESCE(SUM(x.dr - x.cr),0)::numeric, 2) net FROM (
               SELECT a.vch_txn_id, SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) dr, SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) cr
                 FROM accttxnmst a JOIN vchtxnconso h ON h.vch_txn_id = a.vch_txn_id AND h.cmp_id = a.cmp_id
                WHERE a.cmp_id = ? AND h.vch_type_id = 23 AND a.acc_txn_type = 1 AND a.acc_txn_date BETWEEN ? AND ? {$this->b()} GROUP BY a.vch_txn_id) x",
            [$this->cmp, $this->fyStart, $this->to])->getRowArray();
        $this->check('gst_journals', 'INFO', "System journals (voucher type 23): {$t['v']}, of which {$t['unbal']} do not balance; their net debit-minus-credit is " . $this->drcr((float)$t['net']) . '.', $t);
    }

    // ======================================================================================================
    //  2b  linked vouchers, voucher detail, CSV
    // ======================================================================================================

    /** Runs an optional query in its own savepoint: a missing table or column must not spoil the read-only transaction. @return mixed|null  null = it failed */
    private function optional(callable $fn)
    {
        $this->db->simpleQuery('SAVEPOINT audit_opt');
        try {
            $r = $fn();
            if ($r === false || $this->db->simpleQuery('SELECT 1') === false) { throw new \RuntimeException('query failed'); }
            $this->db->simpleQuery('RELEASE SAVEPOINT audit_opt');
            return $r;
        } catch (\Throwable $e) {
            $this->db->simpleQuery('ROLLBACK TO SAVEPOINT audit_opt');
            return null;
        }
    }

    /** @return array<int,array{dr:float,cr:float}>  every voucher with posted rows in the report window (same window and branch as the imbalance above) */
    private function voucherTotals(): array
    {
        $rows = $this->db->query(
            "SELECT a.vch_txn_id id,
                    ROUND(COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END),0)::numeric, 2) dr,
                    ROUND(COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END),0)::numeric, 2) cr
               FROM accttxnmst a
              WHERE a.cmp_id = ? AND a.acc_txn_type = 1 AND a.vch_txn_id > 0 AND a.acc_txn_date BETWEEN ? AND ? {$this->b()}
              GROUP BY a.vch_txn_id", [$this->cmp, $this->fyStart, $this->to])->getResultArray();
        $out = [];
        foreach ($rows as $r) { $out[(int)$r['id']] = ['dr' => (float)$r['dr'], 'cr' => (float)$r['cr']]; }
        return $out;
    }

    /**
     * Puts every voucher of the window together with the vouchers it is linked to (vchbridgen, either direction, composition link types
     * only): a composition-scheme purchase or sale and its GST PAID A/C system journal are two vouchers that belong together.
     * @param array<int,array{dr:float,cr:float}> $tot
     * @return array<int,array{members:int[],dr:float,cr:float,net:float,outside:int}>|null  keyed by group root; null = the link table cannot be read
     */
    private function linkGroups(array $tot): ?array
    {
        $links = $this->optional(fn() => $this->db->query('SELECT vch_bridge_type t, vch_txn_id_src s, vch_txn_id_dest d FROM vchbridgen WHERE cmp_id = ? AND vch_bridge_type IN (' . implode(',', self::COMPOSITION_LINKS) . ')', [$this->cmp])->getResultArray());
        if ($links === null) { return null; }
        $parent = [];
        $find = static function (int $x) use (&$parent): int {
            if (!isset($parent[$x])) { $parent[$x] = $x; }
            $r = $x;
            while ($parent[$r] !== $r) { $r = $parent[$r]; }
            while ($parent[$x] !== $r) { $n = $parent[$x]; $parent[$x] = $r; $x = $n; }
            return $r;
        };
        foreach ($links as $l) {
            $a = (int)$l['s']; $b = (int)$l['d'];
            if ($a <= 0 || $b <= 0 || $a === $b || (!isset($tot[$a]) && !isset($tot[$b]))) { continue; }
            $ra = $find($a); $rb = $find($b);
            if ($ra !== $rb) { $parent[$rb] = $ra; }
        }
        $groups = [];
        foreach ($tot as $id => $t) {
            $root = isset($parent[$id]) ? $find($id) : $id;
            $groups[$root] = $groups[$root] ?? ['members' => [], 'dr' => 0.0, 'cr' => 0.0, 'net' => 0.0, 'outside' => 0];
            $groups[$root]['members'][] = $id;
            $groups[$root]['dr'] += $t['dr'];
            $groups[$root]['cr'] += $t['cr'];
        }
        foreach (array_keys($parent) as $id) {                      // linked vouchers that have no posted rows in this window (other year / branch)
            if (!isset($tot[$id])) { $root = $find($id); if (isset($groups[$root])) { $groups[$root]['outside']++; } }
        }
        foreach ($groups as &$g) { $g['dr'] = round($g['dr'], 2); $g['cr'] = round($g['cr'], 2); $g['net'] = round($g['dr'] - $g['cr'], 2); }
        unset($g);
        return $groups;
    }

    /** @param int[] $ids @return array<int,array<string,mixed>>  voucher headers by id */
    private function headers(array $ids): array
    {
        $out = [];
        foreach (array_chunk(array_values(array_unique(array_map('intval', $ids))), 500) as $chunk) {
            $rows = $this->optional(fn() => $this->db->table('vchtxnconso')->where('cmp_id', $this->cmp)->whereIn('vch_txn_id', $chunk)->get()->getResultArray());
            foreach ((array)$rows as $r) { $out[(int)$r['vch_txn_id']] = $r; }
        }
        return $out;
    }

    /** Section 2b: vouchers grouped with the vouchers they are linked to; what is still unbalanced after that; optional detail and CSV. @param array<int,array<string,mixed>> $list unbalanced vouchers, largest first */
    private function checkLinked(array $list): void
    {
        $tot = $this->voucherTotals();
        $groups = $this->linkGroups($tot);
        if ($groups === null) {
            $this->check('linked', 'INFO', "The link table 'vchbridgen' could not be read here, so vouchers were not grouped with the vouchers they are linked to.");
            return;
        }
        $of = [];
        foreach ($groups as $root => $g) { foreach ($g['members'] as $m) { $of[$m] = $root; } }
        $own = []; foreach ($list as $v) { $own[(int)$v['vch_txn_id']] = $v; }

        $linked = 0; $balanceWithLink = 0; $outside = 0;
        foreach ($own as $id => $v) {
            $g = $groups[$of[$id] ?? $id] ?? null;
            if (!$g) { continue; }
            if (count($g['members']) > 1) { $linked++; if (abs($g['net']) <= 0.005) { $balanceWithLink++; } }
            if ($g['outside'] > 0) { $outside++; }
        }
        $still = array_filter($groups, fn($g) => abs($g['net']) > 0.005);
        uasort($still, fn($a, $b) => abs($b['net']) <=> abs($a['net']));
        $stillNet = 0.0; foreach ($still as $g) { $stillNet += $g['net']; }
        $this->R['linked'] = ['unbalanced_alone' => count($own), 'linked' => $linked, 'balance_with_link' => $balanceWithLink,
                              'groups_still_unbalanced' => count($still), 'net_still_unbalanced' => round($stillNet, 2), 'linked_outside_window' => $outside];

        $this->check('linked', 'INFO', sprintf("Linked vouchers (vchbridgen link types 3/4/5, composition-scheme GST PAID A/C journals): %d of the %d unbalanced voucher(s) are linked to another voucher; %d of them balance once the linked voucher is added%s.",
            $linked, count($own), $balanceWithLink, $outside ? "; $outside more are linked to a voucher outside this year / branch" : ''));
        $this->check('linked_remaining', $still ? 'WARN' : 'OK', sprintf('After adding each voucher to the vouchers it is linked to, %d voucher group(s) still do not balance, together %s%s.',
            count($still), $this->drcr($stillNet), $still ? ' - this is the real ledger imbalance' : ''), $this->R['linked']);
        if (!$still) { return; }

        $hdr = $this->headers(array_merge(...array_map(fn($g) => $g['members'], $still)));
        $typeOf = function (int $id) use ($hdr, $own): ?int { return isset($hdr[$id]['vch_type_id']) ? (int)$hdr[$id]['vch_type_id'] : (isset($own[$id]['vch_type_id']) ? (int)$own[$id]['vch_type_id'] : null); };
        $kinds = [];
        foreach ($still as $g) {
            $names = [];
            foreach ($g['members'] as $m) { $names[$this->vt($typeOf($m))] = true; }
            $k = implode(' + ', array_keys($names)) . (count($g['members']) === 1 ? ($g['outside'] ? ' (linked voucher outside this year)' : ' (no linked voucher)') : '');
            $kinds[$k]['n'] = ($kinds[$k]['n'] ?? 0) + 1; $kinds[$k]['net'] = ($kinds[$k]['net'] ?? 0) + $g['net']; $kinds[$k]['abs'] = ($kinds[$k]['abs'] ?? 0) + abs($g['net']);
        }
        uasort($kinds, fn($a, $b) => $b['abs'] <=> $a['abs']);
        $rows = []; foreach ($kinds as $k => $x) { $rows[] = ['kind' => $k, '>groups' => $x['n'], '>net (Dr-Cr)' => $this->drcr($x['net']), '>gross' => $this->n($x['abs'])]; }
        CLI::write('  still unbalanced after adding linked vouchers, by kind:');
        $this->table($rows, ['kind' => 'kind', '>groups' => '>groups', '>net (Dr-Cr)' => '>net (Dr-Cr)', '>gross' => '>gross'], 12);
        $this->R['linked_by_kind'] = $kinds;

        $rows = [];
        foreach (array_slice($still, 0, $this->limit, true) as $g) {
            $ids = $g['members']; $date = '';
            foreach ($ids as $m) { $d = (string)($hdr[$m]['vch_date'] ?? $own[$m]['vch_date'] ?? ''); if ($d !== '' && ($date === '' || $d < $date)) { $date = $d; } }
            $rows[] = ['ids' => implode(' + ', array_slice($ids, 0, 3)) . (count($ids) > 3 ? ' ...' : ''), 'date' => substr($date, 0, 10),
                       'types' => implode(' + ', array_map(fn($m) => (string)($typeOf($m) ?? '?'), array_slice($ids, 0, 3))),
                       '>debit' => $this->n($g['dr']), '>credit' => $this->n($g['cr']), '>diff' => $this->drcr($g['net'])];
        }
        CLI::write('  largest groups that still do not balance:');
        $this->table($rows, ['ids' => 'vch_txn_id', 'date' => 'date', 'types' => 'type ids', '>debit' => '>debit', '>credit' => '>credit', '>diff' => '>diff'], $this->limit);
        $this->R['linked_groups'] = array_slice(array_map(fn($g) => ['members' => $g['members'], 'dr' => $g['dr'], 'cr' => $g['cr'], 'net' => $g['net'], 'outside' => $g['outside']], array_values($still)), 0, 200);

        // detail on request
        $want = $this->opt('vouchers');
        if ($want !== null) {
            $n = ($want === true || $want === '') ? 3 : max(1, (int)$want);
            $this->heading('2b  The largest vouchers that still do not balance, row by row');
            $shown = 0;
            foreach ($still as $g) {
                if ($shown++ >= $n) { break; }
                $this->voucherBlock($this->largestMember($g, $tot), $tot);
            }
            $paired = array_filter($groups, function ($g) use ($own) { if (count($g['members']) < 2 || abs($g['net']) > 0.005) { return false; } foreach ($g['members'] as $m) { if (isset($own[$m])) { return true; } } return false; });
            uasort($paired, fn($a, $b) => abs($this->largestDiff($b, $tot)) <=> abs($this->largestDiff($a, $tot)));
            if ($paired) {
                $this->heading('2b  Vouchers that balance only together with their linked voucher (for comparison)');
                $shown = 0;
                foreach ($paired as $g) {
                    if ($shown++ >= $n) { break; }
                    $this->voucherBlock($this->largestMember($g, $tot), $tot);
                }
            }
        }
        $csv = $this->opt('csv');
        if (is_string($csv) && $csv !== '') { $this->writeCsv($csv, $own, $groups, $of, $tot); }
    }

    private function largestDiff(array $g, array $tot): float
    {
        $best = 0.0;
        foreach ($g['members'] as $m) { $d = ($tot[$m]['dr'] ?? 0) - ($tot[$m]['cr'] ?? 0); if (abs($d) > abs($best)) { $best = $d; } }
        return $best;
    }

    private function largestMember(array $g, array $tot): int
    {
        $best = $g['members'][0]; $bd = -1.0;
        foreach ($g['members'] as $m) { $d = abs(($tot[$m]['dr'] ?? 0) - ($tot[$m]['cr'] ?? 0)); if ($d > $bd) { $bd = $d; $best = $m; } }
        return (int)$best;
    }

    /** --voucher ID,ID: the given vouchers row by row, whether or not they balance. */
    private function checkVoucherIds(string $csv): void
    {
        $tot = $this->voucherTotals();
        $this->heading('2b  Vouchers asked for with --voucher');
        foreach (array_filter(array_map('intval', explode(',', $csv))) as $id) { $this->voucherBlock($id, $tot); }
    }

    /** One voucher: header, every ledger row, stock rows, GST summary, and the vouchers linked to it with their rows. */
    private function voucherBlock(int $id, array $tot): void
    {
        $h = $this->headers([$id])[$id] ?? null;
        $sub = $h['vch_sub_type_id'] ?? null; $ser = $h['vch_series_id'] ?? null;
        CLI::newLine();
        CLI::write(sprintf('  voucher %d   date %s   type %s%s%s   branch %s', $id, substr((string)($h['vch_date'] ?? '?'), 0, 10), $this->vt(isset($h['vch_type_id']) ? (int)$h['vch_type_id'] : null),
            $sub !== null ? "   sub-type $sub" : '', $ser !== null ? "   series $ser" : '', $h['hobo_id'] ?? '?'), 'white');
        $rec = ['id' => $id, 'header' => $h];
        $rec['rows'] = $this->printRows($id);
        $bill = $this->optional(fn() => $this->db->query('SELECT inwsup_bill_ref_no b FROM gstrinwsup WHERE cmp_id = ? AND vch_txn_id = ? LIMIT 1', [$this->cmp, $id])->getRowArray());
        if ($bill && (string)($bill['b'] ?? '') !== '') { CLI::write('         supplier bill no: ' . $bill['b']); $rec['bill_no'] = $bill['b']; }
        $st = $this->optional(fn() => $this->db->query('SELECT COUNT(*) n, COALESCE(SUM(itm_txn_amt),0) amt FROM itemtxnmst WHERE cmp_id = ? AND vch_txn_id = ? AND itm_txn_type = 1', [$this->cmp, $id])->getRowArray());
        if ($st && (int)$st['n'] > 0) { CLI::write(sprintf('         stock rows: %d, value %s', $st['n'], $this->n((float)$st['amt']))); $rec['stock'] = $st; }
        $g = $this->optional(fn() => $this->db->query(
            'SELECT COUNT(*) n, COALESCE(SUM(vch_taxable_value),0) taxable, COALESCE(SUM(vch_igst),0) igst, COALESCE(SUM(vch_cgst),0) cgst, COALESCE(SUM(vch_sgst_ugst),0) sgst, COALESCE(SUM(vch_cess),0) cess, COALESCE(SUM(vch_total_tax),0) tax
               FROM vchgstsumn WHERE cmp_id = ? AND vch_txn_id = ?', [$this->cmp, $id])->getRowArray());
        $diff = round(($tot[$id]['dr'] ?? 0) - ($tot[$id]['cr'] ?? 0), 2);
        if ($g && (int)$g['n'] > 0) {
            CLI::write(sprintf('         GST summary (vchgstsumn, %d row(s)): taxable %s   IGST %s   CGST %s   SGST/UTGST %s   cess %s   total tax %s',
                $g['n'], $this->n((float)$g['taxable']), $this->n((float)$g['igst']), $this->n((float)$g['cgst']), $this->n((float)$g['sgst']), $this->n((float)$g['cess']), $this->n((float)$g['tax'])));
            $rec['gst_summary'] = $g;
            if (abs($diff) > 0.005 && abs(abs($diff) - (float)$g['tax']) <= 0.05) {
                $this->check('voucher_tax_' . $id, 'INFO', "voucher $id: its debit/credit difference (" . $this->drcr($diff) . ') equals the total tax in its GST summary (' . $this->n((float)$g['tax']) . ').');
            }
        }
        $links = $this->optional(fn() => $this->db->query('SELECT vch_bridge_type t, vch_txn_id_src s, vch_txn_id_dest d FROM vchbridgen WHERE cmp_id = ? AND (vch_txn_id_src = ? OR vch_txn_id_dest = ?) ORDER BY 1, 2, 3', [$this->cmp, $id, $id])->getResultArray());
        $sumDr = (float)($tot[$id]['dr'] ?? 0); $sumCr = (float)($tot[$id]['cr'] ?? 0); $rec['linked'] = [];
        if (!$links) {
            CLI::write('         linked vouchers: none' . ($links === null ? ' (link table not readable)' : ''), 'light_gray');
        }
        $added = 0;
        foreach ((array)$links as $l) {
            $other = (int)$l['s'] === $id ? (int)$l['d'] : (int)$l['s'];
            $oh = $this->headers([$other])[$other] ?? null;
            $compo = in_array((int)$l['t'], self::COMPOSITION_LINKS, true);
            CLI::write(sprintf('         linked voucher %d (link type %s, this voucher is the %s): date %s, type %s, branch %s%s%s', $other, $l['t'], (int)$l['s'] === $id ? 'source' : 'destination',
                substr((string)($oh['vch_date'] ?? '?'), 0, 10), $this->vt(isset($oh['vch_type_id']) ? (int)$oh['vch_type_id'] : null), $oh['hobo_id'] ?? '?', $oh === null ? '  [voucher header not found]' : '',
                $compo ? '' : '  [not a composition-scheme link: shown for information, not added]'), 'yellow');
            $orows = $this->printRows($other, '            ');
            $inWindow = isset($tot[$other]);
            if ($compo && $inWindow) { $sumDr += $tot[$other]['dr']; $sumCr += $tot[$other]['cr']; $added++; }
            $rec['linked'][] = ['id' => $other, 'link_type' => $l['t'], 'composition_link' => $compo, 'in_window' => $inWindow, 'rows' => $orows];
        }
        if ($added) {
            CLI::write(sprintf('         together with the linked voucher(s): debit %s   credit %s   difference %s', $this->n($sumDr), $this->n($sumCr), $this->drcr(round($sumDr - $sumCr, 2))), 'light_gray');
        }
        $this->R['voucher_detail'][] = $rec;
    }

    /** Prints the ledger rows of one voucher; returns them. @return array<int,array<string,mixed>> */
    private function printRows(int $id, string $pad = '         '): array
    {
        $rows = $this->optional(fn() => $this->db->query(
            'SELECT a.acc_txn_type t, a.acc_txn_dr_cr s, a.acc_txn_amt amt, a.acc_id, m.acc_name, a.hobo_id, a.acc_txn_date d
               FROM accttxnmst a LEFT JOIN acctmaster m ON m.acc_id = a.acc_id AND m.cmp_id = a.cmp_id
              WHERE a.cmp_id = ? AND a.vch_txn_id = ? ORDER BY a.acc_txn_type, a.acc_txn_dr_cr, a.acc_txn_amt DESC, a.acc_id', [$this->cmp, $id])->getResultArray());
        if (!$rows) { CLI::write($pad . '(no ledger rows)', 'light_gray'); return []; }
        $dr = $cr = 0.0; $tag = [1 => '', 2 => ' [optional]', 3 => ' [memorandum]', 4 => ' [pending approval]', 5 => ' [rejected]'];
        foreach ($rows as $r) {
            $side = (int)$r['s'] === 1 ? 'Dr' : ((int)$r['s'] === 2 ? 'Cr' : '??');
            CLI::write(sprintf('%s%s %16s   %s (acc %s)%s%s', $pad, $side, $this->n((float)$r['amt']), trim((string)($r['acc_name'] ?? '(no account master row)')), $r['acc_id'] ?? '?',
                $tag[(int)$r['t']] ?? ' [type ' . $r['t'] . ']', (!$this->cons && (int)$r['hobo_id'] !== $this->bo) ? ' [branch ' . $r['hobo_id'] . ']' : ''));
            if ((int)$r['t'] === 1) { if ((int)$r['s'] === 1) { $dr += (float)$r['amt']; } elseif ((int)$r['s'] === 2) { $cr += (float)$r['amt']; } }
        }
        CLI::write(sprintf('%s   posted rows: debit %s   credit %s   difference %s', $pad, $this->n($dr), $this->n($cr), $this->drcr(round($dr - $cr, 2))), 'light_gray');
        return $rows;
    }

    /** --csv FILE: every voucher that does not balance on its own, with the vouchers linked to it and whether they balance together. */
    private function writeCsv(string $file, array $own, array $groups, array $of, array $tot): void
    {
        $ids = array_keys($own);
        $hdr = $this->headers($ids);
        $leg = []; $tax = []; $bill = [];
        foreach (array_chunk($ids, 500) as $chunk) {
            $in = implode(',', array_map('intval', $chunk));
            $r = $this->optional(fn() => $this->db->query(
                "SELECT DISTINCT ON (a.vch_txn_id) a.vch_txn_id id, m.acc_name FROM accttxnmst a LEFT JOIN acctmaster m ON m.acc_id = a.acc_id AND m.cmp_id = a.cmp_id
                  WHERE a.cmp_id = ? AND a.acc_txn_type = 1 AND a.vch_txn_id IN ($in) ORDER BY a.vch_txn_id, a.acc_txn_amt DESC", [$this->cmp])->getResultArray());
            foreach ((array)$r as $x) { $leg[(int)$x['id']] = (string)$x['acc_name']; }
            $r = $this->optional(fn() => $this->db->query("SELECT vch_txn_id id, SUM(vch_total_tax) t FROM vchgstsumn WHERE cmp_id = ? AND vch_txn_id IN ($in) GROUP BY vch_txn_id", [$this->cmp])->getResultArray());
            foreach ((array)$r as $x) { $tax[(int)$x['id']] = (float)$x['t']; }
            $r = $this->optional(fn() => $this->db->query("SELECT vch_txn_id id, MAX(inwsup_bill_ref_no) b FROM gstrinwsup WHERE cmp_id = ? AND vch_txn_id IN ($in) GROUP BY vch_txn_id", [$this->cmp])->getResultArray());
            foreach ((array)$r as $x) { $bill[(int)$x['id']] = (string)$x['b']; }
        }
        $fh = @fopen($file, 'w');
        if (!$fh) { $this->check('csv', 'WARN', "Could not write $file."); return; }
        fputcsv($fh, ['vch_txn_id', 'date', 'type_id', 'type', 'sub_type_id', 'branch', 'debit', 'credit', 'diff_debit_minus_credit', 'linked_voucher_ids', 'group_debit', 'group_credit', 'group_diff', 'status', 'largest_leg_account', 'gst_summary_total_tax', 'supplier_bill_no']);
        $names = $this->vchTypeNames(); $n = 0;
        foreach ($own as $id => $v) {
            $g = $groups[$of[$id] ?? $id] ?? ['members' => [$id], 'dr' => $v['dr'], 'cr' => $v['cr'], 'net' => $v['diff'], 'outside' => 0];
            $others = array_values(array_diff($g['members'], [$id]));
            $status = count($g['members']) > 1 ? (abs($g['net']) <= 0.005 ? 'balances together with linked voucher' : 'STILL UNBALANCED with linked voucher') : ($g['outside'] ? 'STILL UNBALANCED (linked voucher outside this year)' : 'STILL UNBALANCED (no linked voucher)');
            $t = (int)($v['vch_type_id'] ?? $hdr[$id]['vch_type_id'] ?? 0);
            fputcsv($fh, [$id, substr((string)($v['vch_date'] ?? $hdr[$id]['vch_date'] ?? ''), 0, 10), $t, $names[$t] ?? '', $hdr[$id]['vch_sub_type_id'] ?? '', $hdr[$id]['hobo_id'] ?? '',
                          number_format((float)$v['dr'], 2, '.', ''), number_format((float)$v['cr'], 2, '.', ''), number_format((float)$v['diff'], 2, '.', ''), implode(' ', $others),
                          number_format($g['dr'], 2, '.', ''), number_format($g['cr'], 2, '.', ''), number_format($g['net'], 2, '.', ''), $status, $leg[$id] ?? '',
                          isset($tax[$id]) ? number_format($tax[$id], 2, '.', '') : '', $bill[$id] ?? '']);
            $n++;
        }
        fclose($fh);
        $this->check('csv', 'INFO', "Wrote $n unbalanced voucher(s) to $file (open it in Excel; the 'status' column tells which ones still do not balance).");
    }

    // ======================================================================================================
    //  3  structure
    // ======================================================================================================

    private function checkStructure(): void
    {
        $this->heading('3  Account structure for this financial year');
        $s = $this->snap(); $q = $s->quality;
        $this->check('quality', array_sum($q) ? 'WARN' : 'OK',
            "ledgers with activity but no group mapping: {$q['unmapped']}; group chain broken: {$q['broken_chains']}; missing from account master: {$q['missing_masters']}; duplicate mapping rows: {$q['duplicate_mappings']}; duplicate groups: {$q['duplicate_groups']}", $q);
        $unc = $this->engine()->unclassified($s);
        if ($unc) {
            $rows = [];
            foreach ($unc as $a) { $rows[] = ['id' => $a['id'], 'name' => strip_tags($a['name']), 'cat' => $a['cat'], '>opening' => $this->drcr($a['op']), '>closing' => $this->drcr($a['closing']),
                        'why' => $a['missing_master'] ? 'not in account master' : (!$a['mapped'] ? 'not mapped to a group for this FY' : 'category outside balance sheet / P&L')]; }
            $this->check('unclassified', 'WARN', count($unc) . ' ledger(s) sit outside the Balance Sheet / P&L structure; the corrected reports list them as "Unclassified" (they were dropped by the old code):');
            $this->table($rows, ['id' => 'acc_id', 'name' => 'ledger', 'cat' => 'cat', '>opening' => '>opening', '>closing' => '>closing', 'why' => 'why'], $this->limit);
        } else {
            $this->check('unclassified', 'OK', 'Every ledger with a balance is placed in the Balance Sheet / P&L structure.');
        }

        $names = []; foreach ($this->db->query('SELECT acc_grp_parent_id id, acc_grp_parent_name n FROM grpparentn ORDER BY 1')->getResultArray() as $r) { $names[(int)$r['id']] = $r['n']; }
        $sub = [];
        foreach ($s->groups as $g) {
            if ($g['parent_gid'] > 0) { $sub[] = ['name' => $g['name'], 'cat' => $g['cat'], 'category' => $names[$g['cat']] ?? '?', 'pl' => AccountingEngine::isPlCat((int)$g['cat']) ? 'P&L' : 'BS',
                        '>closing' => $this->drcr($s->groupSum($g['id'], 'closing')), '>movement' => $this->drcr($s->groupSum($g['id'], 'mv'))]; }
        }
        $plSub = count(array_filter($sub, fn($x) => $x['pl'] === 'P&L'));
        $this->check('subgroups', $plSub ? 'INFO' : 'OK', count($sub) . " sub-group(s) exist, $plSub of them under profit & loss categories. The old Profit & Loss (Schedules / Detailed views) counted a sub-group's balance twice - in the sub-group and again in its parent group - which distorted the profit and, through its profit line, the Balance Sheet:");
        usort($sub, fn($a, $b) => [$a['pl'] === 'P&L' ? 0 : 1, $a['name']] <=> [$b['pl'] === 'P&L' ? 0 : 1, $b['name']]);
        $this->table($sub, ['name' => 'sub-group', 'category' => 'category', 'pl' => 'type', '>closing' => '>closing', '>movement' => '>movement'], $this->limit);
    }

    // ======================================================================================================
    //  4  opening balances
    // ======================================================================================================

    private function checkOpenings(): void
    {
        $this->heading('4  Opening balances of this financial year');
        $eng = $this->engine(); $s = $this->snap();
        [$opStock, $clStock] = $this->m->stockFigures($s->from, $s->to, (int)$this->cons, true);
        $total = $eng->openingTotal($s);
        $this->R['openings'] = ['ledger_total' => $total, 'opening_stock' => $opStock, 'closing_stock' => $clStock];
        $this->check('open_total', 'INFO', 'Ledger opening balances net ' . $this->drcr($total) . '; opening stock ' . $this->n($opStock) . ' (Dr); closing stock at report date ' . $this->n($clStock) . '.');
        $diff = round($total + $opStock, 2);
        $this->check('open_diff', abs($diff) < 0.01 ? 'OK' : 'WARN', abs($diff) < 0.01 ? 'Opening balances (ledgers + opening stock) net to zero.' : "'Difference in Opening' (ledger openings + opening stock) = " . $this->drcr($diff) . ' - the opening data of this year does not balance.');
        $this->R['openings']['difference'] = $diff;

        $pl = [];
        foreach ($s->accounts as $a) { if (AccountingEngine::isPlCat((int)$a['cat']) && abs($a['op']) >= 0.005) { $pl[] = $a; } }
        usort($pl, fn($x, $y) => abs($y['op']) <=> abs($x['op']));
        $sum = 0.0; foreach ($pl as $a) { $sum += $a['op']; }
        $this->R['openings']['pl_ledgers'] = ['count' => count($pl), 'net' => round($sum, 2)];
        if ($pl) {
            $this->check('open_pl', 'WARN', count($pl) . ' profit & loss ledger(s) carry an opening balance (net ' . $this->drcr($sum) . '). The account screen refuses opening balances on profit & loss accounts and profit & loss accounts start every year at zero:');
            $rows = []; foreach ($pl as $a) { $rows[] = ['id' => $a['id'], 'name' => strip_tags($a['name']), 'cat' => $a['cat'], '>opening' => $this->drcr($a['op'])]; }
            $this->table($rows, ['id' => 'acc_id', 'name' => 'ledger', 'cat' => 'category', '>opening' => '>opening'], $this->limit);
        } else {
            $this->check('open_pl', 'OK', 'No profit & loss ledger carries an opening balance.');
        }

        $bsd = 0; $bsdSum = 0.0;
        foreach ($s->accounts as $a) { if ($a['is_bsd'] && abs($a['op']) >= 0.005) { $bsd++; $bsdSum += $a['op']; } }
        $this->check('open_bsd', 'INFO', "$bsd bill-sundry ledger(s) carry an opening balance (net " . $this->drcr($bsdSum) . '); they are included in the totals.');

        // per category
        $names = []; foreach ($this->db->query('SELECT acc_grp_parent_id id, acc_grp_parent_name n FROM grpparentn ORDER BY 1')->getResultArray() as $r) { $names[(int)$r['id']] = $r['n']; }
        $cat = [];
        foreach ($s->accounts as $a) { $c = (int)$a['cat']; $cat[$c] = ($cat[$c] ?? 0) + $a['op']; }
        ksort($cat);
        $rows = []; foreach ($cat as $c => $v) { if (abs($v) >= 0.005) { $rows[] = ['cat' => $c . ' ' . ($names[$c] ?? ($c === 0 ? 'unclassified' : '?')), '>opening' => $this->drcr($v)]; } }
        CLI::write('  opening balance by category:');
        $this->table($rows, ['cat' => 'category', '>opening' => '>opening'], 20);

        // stock valuation rows
        try {
            $rows = $this->db->query(
                "SELECT itm_val_method_id m, COUNT(*) n, ROUND(COALESCE(SUM(itm_op_val_amt),0)::numeric,2) v FROM itmoppyval
                  WHERE cmp_id = ? AND cmpfymastr_id = ? " . ($this->cons ? '' : 'AND hobo_id = ' . (int)$this->bo) . ' GROUP BY 1 ORDER BY 1', [$this->cmp, $this->fy])->getResultArray();
            $txt = []; foreach ($rows as $r) { $txt[] = "method {$r['m']}: {$r['n']} row(s) = " . $this->n((float)$r['v']); }
            $this->check('open_stock_rows', count($rows) > 1 ? 'INFO' : 'OK', 'itmoppyval opening stock rows - ' . ($txt ? implode('; ', $txt) : 'none') . '; the reports use ' . $this->n($opStock) . '.', $rows);
        } catch (\Throwable $e) {
            $this->check('open_stock_rows', 'INFO', 'itmoppyval not readable: ' . $e->getMessage());
        }
        $b = (int)date('n', strtotime($this->fyStart));
        if ($b !== 4 || (int)date('j', strtotime($this->fyStart)) !== 1) {
            $this->check('stock_fy_start', 'WARN', 'This financial year does not start on 1 April; the stock valuation routine assumes it does, so opening/closing stock may be measured from the wrong date.');
        }
    }

    // ======================================================================================================
    //  5  year-end carry-forward
    // ======================================================================================================

    private function checkCarryForward(): void
    {
        $this->heading('5  Year-end carry-forward: previous year closing -> this year opening');
        $prev = null;
        try {
            foreach ($this->univ()->table('cmpfymastr')->where('cmp_id', $this->cmp)->orderBy('fy_beg_date')->get()->getResultArray() as $f) {
                if (date('Y-m-d', strtotime($f['fy_end_date'])) < $this->fyStart) { $prev = $f; }
            }
        } catch (\Throwable $e) {}
        if (!$prev) { $this->check('carry_prev', 'INFO', 'There is no earlier financial year for this company; nothing to compare.'); return; }
        $pStart = date('Y-m-d', strtotime($prev['fy_beg_date'])); $pEnd = date('Y-m-d', strtotime($prev['fy_end_date'])); $pFy = (int)$prev['cmpfymastr_id'];

        // previous year's balances, in a session of its own (the stock code reads the FY from the session)
        $cur = $this->snap();
        $curM = $this->m; $curSession = $_SESSION;
        $this->openSession($prev, $pFy, $pStart, $pEnd);
        $pm  = $this->m;
        $ps  = $pm->engine()->snapshot($pStart, $pEnd, $this->cons);
        [$pOpStock, $pClStock] = $pm->stockFigures($ps->from, $ps->to, (int)$this->cons, true);
        $pPl = $pm->engine()->profitLoss($ps, $pOpStock, $pClStock, 'cum');
        $_SESSION = $curSession; $this->m = $curM;
        [$opStock] = $this->m->stockFigures($cur->from, $cur->to, (int)$this->cons, true);

        $prevProfit = $pPl['net'];                                       // profit +, loss -
        $plApp = $this->db->query("SELECT acc_id FROM acctmaster WHERE cmp_id = ? AND LOWER(TRIM(acc_name)) = 'profit & loss appropriation' ORDER BY acc_id LIMIT 1", [$this->cmp])->getRowArray();
        $plAppId = $plApp ? (int)$plApp['acc_id'] : 0;

        $x1 = 0.0; $x2 = 0.0; $x3 = 0.0; $rowsBs = []; $rowsPl = [];
        $ids = array_unique(array_merge(array_keys($ps->accounts), array_keys($cur->accounts)));
        foreach ($ids as $id) {
            $pa = $ps->accounts[$id] ?? null; $ca = $cur->accounts[$id] ?? null;
            $open = $ca ? (float)$ca['op'] : 0.0; $pClose = $pa ? (float)$pa['closing'] : 0.0;
            $pCat = $pa ? (int)$pa['cat'] : ($ca ? (int)$ca['cat'] : 0);
            $isPl = $pCat >= 6 && $pCat <= 13;
            if ($id === $plAppId) { continue; }
            if ($isPl) {                                                  // must start at zero
                if (abs($open) >= 0.005) { $x1 += $open; $rowsPl[] = ['id' => $id, 'name' => strip_tags($ca['name'] ?? ($pa['name'] ?? '')), 'cat' => $pCat, '>opening' => $this->drcr($open)]; }
                continue;
            }
            $d = round($open - $pClose, 2);
            if (abs($d) >= 0.005) { $x3 += $d; $rowsBs[] = ['id' => $id, 'name' => strip_tags($ca['name'] ?? ($pa['name'] ?? '')), '>prev closing' => $this->drcr($pClose), '>opening' => $this->drcr($open), '>difference' => $this->drcr($d), 'abs' => abs($d)]; }
        }
        usort($rowsBs, fn($a, $b) => $b['abs'] <=> $a['abs']);

        // P&L Appropriation account
        $appOpen = $plAppId && isset($cur->accounts[$plAppId]) ? (float)$cur->accounts[$plAppId]['op'] : 0.0;
        $appPrev = $plAppId && isset($ps->accounts[$plAppId]) ? (float)$ps->accounts[$plAppId]['closing'] : 0.0;
        $expected = round($appPrev - $prevProfit, 2);                       // debit +, credit -: last closing less the year's profit (a credit)
        $oldFormula = round(-($appPrev + $prevProfit), 2);                   // what the old carry-forward stored
        $x2 = round($appOpen - $expected, 2);

        // stock
        $x4 = round($opStock - $pClStock, 2);

        // What the previous year itself left unbalanced is inherited: its openings + its unbalanced vouchers + its opening stock,
        // less the profit & loss openings it carried (they were never part of its profit, see the identity in the header comment).
        $prevOpenTotal = $pm->engine()->openingTotal($ps);
        $prevImb       = $pm->engine()->imbalance($ps);
        $prevPlOpen    = 0.0;
        foreach ($ps->accounts as $pa) { if ((int)$pa['cat'] >= 6 && (int)$pa['cat'] <= 13) { $prevPlOpen += (float)$pa['op']; } }
        $ePrev         = round($prevOpenTotal + $prevImb + $pOpStock - $prevPlOpen, 2);

        $diff = round($this->engine()->openingTotal($cur) + $opStock, 2);
        $explained = round($x1 + $x2 + $x3 + $x4 + $ePrev, 2);

        $this->check('carry_prev', 'INFO', "Previous year: fy $pFy ($pStart .. $pEnd); its profit " . $this->drcr(-$prevProfit) . ' (profit = credit); closing stock ' . $this->n($pClStock) . '.');
        if ($rowsPl) {
            $this->check('carry_pl', 'WARN', count($rowsPl) . ' profit & loss ledger(s) were carried in with an opening balance, net ' . $this->drcr($x1) . ':');
            $this->table($rowsPl, ['id' => 'acc_id', 'name' => 'ledger', 'cat' => 'category', '>opening' => '>opening'], $this->limit);
        } else { $this->check('carry_pl', 'OK', 'No profit & loss ledger was carried into this year.'); }
        if ($plAppId) {
            $st = abs($x2) < 0.005 ? 'OK' : 'WARN';
            $this->check('carry_app', $st, "'Profit & Loss Appropriation' (acc $plAppId): opening " . $this->drcr($appOpen) . '; expected (previous closing ' . $this->drcr($appPrev) . ' less profit) ' . $this->drcr($expected)
                . (abs($x2) < 0.005 ? '.' : '; difference ' . $this->drcr($x2) . (abs($appOpen - $oldFormula) < 0.005 ? ' - equals what the OLD carry-forward formula stores (sign of the accumulated balance reversed).' : '.')));
        } else {
            $this->check('carry_app', $prevProfit != 0.0 ? 'WARN' : 'INFO', "No 'Profit & Loss Appropriation' ledger exists; the previous year's result of " . $this->drcr(-$prevProfit) . ' has nowhere to be carried.');
            $x2 = round(0 - (-$prevProfit), 2);                                // profit never carried
        }
        if ($rowsBs) {
            $this->check('carry_bs', 'WARN', count($rowsBs) . ' balance-sheet ledger(s) opened with a balance different from the previous closing, net ' . $this->drcr($x3) . ':');
            $this->table($rowsBs, ['id' => 'acc_id', 'name' => 'ledger', '>prev closing' => '>prev closing', '>opening' => '>opening', '>difference' => '>difference'], $this->limit);
        } else { $this->check('carry_bs', 'OK', 'Every balance-sheet ledger opened with the previous year\'s closing balance.'); }
        $this->check('carry_stock', abs($x4) < 0.005 ? 'OK' : 'WARN', 'Opening stock ' . $this->n($opStock) . ' vs previous closing stock ' . $this->n($pClStock) . ' -> difference ' . $this->drcr($x4) . '.');
        $this->check('carry_inherited', abs($ePrev) < 0.005 ? 'OK' : 'INFO', "Inherited from the previous year's own data (its opening balances, unbalanced vouchers and opening stock): " . $this->drcr($ePrev) . '.');

        $tbl = [
            ['part' => 'P&L ledgers carried in with an opening balance', '>amount' => $this->drcr($x1)],
            ['part' => "'Profit & Loss Appropriation' opening vs expected", '>amount' => $this->drcr($x2)],
            ['part' => 'balance-sheet ledgers: opening vs previous close', '>amount' => $this->drcr($x3)],
            ['part' => 'opening stock vs previous closing stock', '>amount' => $this->drcr($x4)],
            ['part' => "inherited from the previous year's own data", '>amount' => $this->drcr($ePrev)],
            ['part' => 'TOTAL explained', '>amount' => $this->drcr($explained)],
            ['part' => "'Difference in Opening' of this year (section 4)", '>amount' => $this->drcr($diff)],
        ];
        CLI::write("  attribution of 'Difference in Opening':");
        $this->table($tbl, ['part' => 'cause', '>amount' => '>amount'], 10);
        $this->check('carry_identity', abs($explained - $diff) < $this->tol ? 'OK' : 'WARN',
            abs($explained - $diff) < $this->tol ? "The causes above account for the whole 'Difference in Opening'." : "The causes above explain " . $this->drcr($explained) . ", leaving " . $this->drcr($diff - $explained) . ' unexplained (e.g. openings entered by hand, branch scope, or a different profit figure at roll-over time).');
        $this->R['carry'] = ['pl_ledgers' => $x1, 'appropriation' => $x2, 'balance_sheet' => $x3, 'stock' => $x4, 'inherited' => $ePrev, 'explained' => $explained, 'difference' => $diff];
    }

    // ======================================================================================================
    //  6  reports (corrected code)
    // ======================================================================================================

    /** last row totals of a horizontal report / TB sums */
    private function tbTotals(array $rows): array { $d = $c = 0.0; foreach ($rows as $r) { $d += (float)$r['debit_total']; $c += (float)$r['credit_total']; } return [round($d, 2), round($c, 2)]; }

    private function checkReports(): void
    {
        $this->heading('6  Corrected reports (the same calculation the screens and the Excel files now use)');
        $m = $this->m; $cons = (int)$this->cons; $f = $this->from; $t = $this->to;
        $imb = $this->rawImbalance();
        $s = $this->snap();
        $open = round($this->engine()->openingTotal($s), 2);

        CLI::write('  Trial Balance (debit - credit must equal the ledger imbalance ' . $this->drcr($imb) . '; opening view must balance):');
        foreach ([0 => 'Groups', 1 => 'Accounts', 2 => 'Opening'] as $v => $label) {
            $rows = $m->load_trial_balance_view($v, $f, $t, $cons, 1);
            [$d, $c] = $this->tbTotals($rows);
            $want = $v === 2 ? 0.0 : $imb;
            $ok = abs(($d - $c) - $want) < $this->tol;
            $this->check("tb_$v", $ok ? 'OK' : 'FAIL', sprintf('%-9s debit %s  credit %s  difference %s', $label, $this->n($d), $this->n($c), $this->drcr($d - $c)) . ($ok ? '' : '  <-- expected ' . $this->drcr($want)), ['debit' => $d, 'credit' => $c]);
        }
        // independent per-ledger check of the Accounts view
        $raw = [];
        foreach ($this->db->query(
            "SELECT x.acc_id, ROUND(SUM(x.v)::numeric,2) closing FROM (
               SELECT acc_id, acc_op_bal v FROM accoppybal WHERE cmp_id = ? AND cmpfymastr_id = ? " . ($this->cons ? '' : 'AND hobo_id = ' . (int)$this->bo) . "
               UNION ALL
               SELECT acc_id, CASE WHEN acc_txn_dr_cr = 1 THEN acc_txn_amt WHEN acc_txn_dr_cr = 2 THEN -acc_txn_amt ELSE 0 END FROM accttxnmst
                WHERE cmp_id = ? AND acc_txn_type = 1 AND vch_txn_id > 0 AND acc_txn_date BETWEEN ? AND ? " . ($this->cons ? '' : 'AND hobo_id = ' . (int)$this->bo) . ") x GROUP BY x.acc_id",
            [$this->cmp, $this->fy, $this->cmp, $this->fyStart, $t])->getResultArray() as $r) { $raw[(int)$r['acc_id']] = (float)$r['closing']; }
        $rows = $m->load_trial_balance_view(1, $this->fyStart, $t, $cons, 1);
        $seen = []; $bad = 0;
        foreach ($rows as $r) { if (($r['type'] ?? '') === 'dfs' && (int)$r['group_id'] > 0) { $seen[(int)$r['group_id']] = round((float)$r['debit_total'] - (float)$r['credit_total'], 2); } }
        foreach ($raw as $id => $val) { if (abs($val) >= 0.005 && abs(($seen[$id] ?? 0.0) - $val) > 0.015) { $bad++; } }
        $this->check('tb_ledgers', $bad ? 'FAIL' : 'OK', $bad ? "$bad ledger balance(s) in the Trial Balance differ from an independent SQL recomputation." : count($raw) . ' ledger balances in the Trial Balance equal an independent SQL recomputation (FY start .. report date).');

        CLI::write('  Profit & Loss (net result must be identical in every layout):');
        $nets = [];
        foreach ([0, 1, 2] as $v) {
            $rows = $m->load_profit_loss_view(1, $v, $f, $t, 1, $cons); $l = end($rows); $nets["horizontal $v"] = round((float)$l['l_balance_total'] - (float)$l['r_balance_total'], 2);
            $rows = $m->load_profit_loss_view(2, $v, $f, $t, 1, $cons); $by = []; foreach ($rows as $r) { $by[$r['group_name']] = (float)$r['amt']; } $nets["vertical $v"] = round(($by['Net Profit C/D'] ?? 0.0) - ($by['Net Loss C/D'] ?? 0.0), 2);
        }
        $one = count(array_unique(array_map('strval', $nets))) === 1;
        $net = reset($nets);
        $this->check('pl_layouts', $one ? 'OK' : 'FAIL', 'Net ' . ($net >= 0 ? 'profit ' : 'loss ') . $this->n(abs($net)) . ($one ? ' in all six layouts.' : ' - layouts disagree: ' . json_encode($nets)), $nets);

        CLI::write('  Balance Sheet (assets minus liabilities must equal the ledger imbalance ' . $this->sn($imb) . '):');
        $fyPl = $m->load_profit_loss_view(1, 1, $this->fyStart, $t, 1, $cons); $l = end($fyPl); $fyNet = round((float)$l['l_balance_total'] - (float)$l['r_balance_total'], 2);
        foreach ([0, 1, 2] as $v) {
            $rows = $m->load_balance_sheet_view(1, $v, $f, $t, 1, $cons); $last = end($rows);
            $lt = (float)$last['l_balance_total']; $rt = (float)$last['r_balance_total'];
            $pl = null; foreach ($rows as $r) { if (($r['l_type'] ?? '') === 'pl' && ($r['l_group_name'] ?? '') === 'Profit / Loss') { $pl = (float)$r['l_balance_total']; } }
            $ok = abs(($lt - $rt) + $imb) < $this->tol && ($pl === null || abs($pl - $fyNet) < $this->tol);
            $this->check("bs_h$v", $ok ? 'OK' : 'FAIL', sprintf('horizontal view %d: liabilities %s  assets %s  assets - liabilities %s  (profit line %s)', $v, $this->n($lt), $this->n($rt), $this->sn($rt - $lt), $pl === null ? 'not shown' : $this->n($pl)));
            $rows = $m->load_balance_sheet_view(2, $v, $f, $t, 1, $cons); $by = []; foreach ($rows as $r) { $by[$r['group_name']] = (float)($r['amt'] ?? 0); }
            $lt = $by['TOTAL LIABILITIES'] ?? 0.0; $rt = $by['TOTAL ASSETS'] ?? 0.0; $ok = abs(($lt - $rt) + $imb) < $this->tol;
            $this->check("bs_v$v", $ok ? 'OK' : 'FAIL', sprintf('vertical   view %d: liabilities %s  assets %s  assets - liabilities %s', $v, $this->n($lt), $this->n($rt), $this->sn($rt - $lt)));
        }
        $this->check('bs_residual', abs($imb) < 0.005 ? 'OK' : 'WARN', abs($imb) < 0.005 ? 'The Balance Sheet balances.' : 'The Balance Sheet does not balance: ' . ($imb > 0 ? 'assets exceed liabilities' : 'liabilities exceed assets') . ' by ' . $this->n(abs($imb)) . ', exactly the ledger imbalance (section 2 names the vouchers); no adjustment is made.');
        $this->R['reports'] = ['profit_net' => $net, 'fy_to_date_net' => $fyNet, 'ledger_imbalance' => $imb, 'openings' => $open];
    }

    // ======================================================================================================
    //  7  old calculation
    // ======================================================================================================

    private function checkLegacy(): void
    {
        $this->heading('7  Previous calculation (old code) next to the corrected one');
        $m = $this->m; $cons = (int)$this->cons; $f = $this->from; $t = $this->to;
        $rows = [];
        $try = function (string $label, callable $old, callable $new) use (&$rows) {
            $call = function (callable $fn, string $tag) {
                $this->db->simpleQuery('SAVEPOINT legacy_sp');
                try { $v = $fn(); $this->db->simpleQuery('RELEASE SAVEPOINT legacy_sp'); return $v; }
                catch (\Throwable $e) { $this->db->simpleQuery('ROLLBACK TO SAVEPOINT legacy_sp'); return $tag . mb_substr($e->getMessage(), 0, 60); }
            };
            $o = $call($old, 'error: ');
            $n = $call($new, 'error: ');
            $rows[] = ['report' => $label, '>old' => is_float($o) ? $this->sn($o) : $o, '>corrected' => is_float($n) ? $this->sn($n) : $n, '>change' => (is_float($o) && is_float($n)) ? $this->sn($n - $o) : ''];
        };
        $tbdiff = fn(array $r) => (function () use ($r) { [$d, $c] = $this->tbTotals($r); return round($d - $c, 2); })();
        $try('TB groups:   debit - credit', fn() => (float)$tbdiff($m->legacy_load_trial_balance_grps($f, $t, $cons)), fn() => (float)$tbdiff($m->load_trial_balance_grps($f, $t, $cons)));
        $try('TB accounts: debit - credit', fn() => (float)$tbdiff($m->legacy_load_trial_balance_accnts($f, $t, $cons)), fn() => (float)$tbdiff($m->load_trial_balance_accnts($f, $t, $cons)));
        foreach ([0, 1, 2] as $v) {
            $bsd = function (array $r) { $l = end($r); return round((float)$l['l_balance_total'] - (float)$l['r_balance_total'], 2); };
            $try("BS view $v: liabilities - assets", fn() => (float)$bsd($m->legacy_load_balance_sheet_horizontal($v, $f, $t, 1, $cons)), fn() => (float)$bsd($m->load_balance_sheet_horizontal($v, $f, $t, 1, $cons)));
            $pln = function (array $r) { $l = end($r); return round((float)$l['l_balance_total'] - (float)$l['r_balance_total'], 2); };
            $try("P&L view $v: net result (+profit / -loss)", fn() => (float)$pln($m->legacy_load_profit_loss_horizontal($v, $f, $t, 1, $cons)), fn() => (float)$pln($m->load_profit_loss_horizontal($v, $f, $t, 1, $cons)));
        }
        $this->table($rows, ['report' => 'report', '>old' => '>old code', '>corrected' => '>corrected', '>change' => '>change'], 30);
        $this->R['legacy'] = $rows;
        $this->legacyPlLines();
        $this->check('legacy', 'INFO', 'Old = the previous calculation logic; "change" is what the correction alters on this data. For a balanced set of books the Balance Sheet and Trial Balance differences would be 0.');
    }

    /** The condensed Profit & Loss (view 0) line by line, old code against corrected: shows which lines the correction moved. */
    private function legacyPlLines(): void
    {
        $m = $this->m; $cons = (int)$this->cons;
        $lines = function (callable $fn): ?array {
            $this->db->simpleQuery('SAVEPOINT legacy_pl_sp');
            try {
                $rows = $fn(); $out = [];
                $names = [11 => 'Purchase', 7 => 'Direct Expenses', 8 => 'Sales', 10 => 'Direct Income', 13 => 'Indirect Expenses', 12 => 'Indirect Income'];
                foreach ((array)$rows as $r) {
                    foreach (['l', 'r'] as $side) {
                        $type = $r[$side . '_type'] ?? null; $amt = (float)($r[$side . '_balance_total'] ?? 0);
                        if ($type === 'opn') { $k = 'Opening Stock'; }
                        elseif ($type === 'clo') { $k = 'Closing Stock'; }
                        elseif ($type === 'prt') { $id = (int)($r[$side . '_parent_id'] ?? $r[$side . '_group_id'] ?? 0); $k = $names[$id] ?? "category $id"; }
                        else { continue; }
                        $out[$k] = ($out[$k] ?? 0.0) + $amt;
                    }
                }
                $this->db->simpleQuery('RELEASE SAVEPOINT legacy_pl_sp');
                return $out;
            } catch (\Throwable $e) { $this->db->simpleQuery('ROLLBACK TO SAVEPOINT legacy_pl_sp'); return null; }
        };
        $old = $lines(fn() => $m->legacy_load_profit_loss_horizontal(0, $this->from, $this->to, 1, $cons));
        $new = $lines(fn() => $m->load_profit_loss_horizontal(0, $this->from, $this->to, 1, $cons));
        if ($old === null || $new === null) { return; }
        $order = ['Opening Stock', 'Purchase', 'Direct Expenses', 'Sales', 'Direct Income', 'Closing Stock', 'Indirect Expenses', 'Indirect Income'];
        $rows = [];
        foreach (array_unique(array_merge($order, array_keys($old), array_keys($new))) as $k) {
            $o = round((float)($old[$k] ?? 0), 2); $n = round((float)($new[$k] ?? 0), 2);
            if (abs($o - $n) > 0.005) { $rows[] = ['line' => $k, '>old' => $this->n($o), '>corrected' => $this->n($n), '>change' => $this->sn($n - $o)]; }
        }
        $this->R['legacy_pl_lines'] = ['old' => $old, 'corrected' => $new];
        if (!$rows) { CLI::write('  Profit & Loss (condensed view), line by line: the old code and the corrected one agree on every line.'); return; }
        CLI::write('  Profit & Loss (condensed view), the lines the correction changed (amounts as shown on the report, debit side / credit side):');
        $this->table($rows, ['line' => 'line', '>old' => '>old code', '>corrected' => '>corrected', '>change' => '>change'], 12);
    }

    // ======================================================================================================
    //  8  Excel / CSV parity
    // ======================================================================================================

    private function checkExcel(): void
    {
        $this->heading('8  Excel / CSV read-back: every generated file compared with the rows the page shows');
        $m = $this->m; $cons = (int)$this->cons; $f = $this->from; $t = $this->to;
        $files = 0; $cells = 0; $bad = 0; $examples = [];
        $txt = function ($s): string { $s = preg_replace('/<[^>]*>/', ' ', (string)$s); $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8'); $s = str_replace(["\xC2\xA0", '»'], ' ', $s); return trim(preg_replace('/\s+/u', ' ', $s)); };
        $num = function ($s): ?float { $s = (string)$s; if ($s === '') { return null; } $x = preg_replace('/[^0-9.\-]/', '', html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8')); return $x === '' ? null : round((float)$x, 2); };
        $same = fn(?float $a, ?float $b): bool => ($a === null && $b === null) || ($a !== null && $b !== null && abs($a - $b) < 0.005);
        $read = function (string $bytes, bool $xlsx): array {
            if ($xlsx) {
                $tmp = tempnam(sys_get_temp_dir(), 'aud') . '.xlsx'; file_put_contents($tmp, $bytes);
                $sh = IOFactory::load($tmp)->getActiveSheet(); @unlink($tmp);
                $out = []; $hc = $sh->getHighestColumn();
                for ($r = 1; $r <= $sh->getHighestRow(); $r++) { $out[$r] = $sh->rangeToArray("A{$r}:{$hc}{$r}", null, false, false, false)[0]; }
                return $out;
            }
            $out = []; $h = fopen('php://temp', 'r+'); fwrite($h, $bytes); rewind($h); $r = 1;
            while (($line = fgetcsv($h, 0, ',', '"', '\\')) !== false) { $out[$r++] = $line; }
            fclose($h); return $out;
        };
        $cellv = fn(array $rows, int $r, int $c) => (($rows[$r][$c] ?? '') === '' ? null : $rows[$r][$c]);
        $note = function (string $what) use (&$bad, &$examples) { $bad++; if (count($examples) < 8) { $examples[] = $what; } };

        $jobs = [];
        foreach ([1, 2] as $fmt) foreach ([0, 1, 2] as $v) {
            $jobs[] = ['bs', $fmt, $v]; $jobs[] = ['pl', $fmt, $v];
        }
        foreach ([0, 1, 2] as $v) { $jobs[] = ['tb', 0, $v]; }
        foreach ($jobs as [$kind, $fmt, $v]) {
            if ($kind === 'bs')      { $rows = $m->load_balance_sheet_view($fmt, $v, $f, $t, 1, $cons); }
            elseif ($kind === 'pl')  { $rows = $m->load_profit_loss_view($fmt, $v, $f, $t, 1, $cons); }
            else                     { $rows = $m->load_trial_balance_view($v, $f, $t, $cons, 1); }
            $opt = ['title' => 'audit', 'subtitle' => 'audit', 'left_head' => $kind === 'bs' ? 'LIABILITIES' : 'DEBITS', 'right_head' => $kind === 'bs' ? 'ASSETS' : 'CREDITS',
                    'detail' => $v === 2, 'sheet' => 'Audit', 'notes' => []];
            foreach ([false, true] as $csv) {
                $opt['csv'] = $csv;
                $book  = $kind === 'tb' ? ReportSheetWriter::trialBalance($rows, $opt) : ($fmt === 2 ? ReportSheetWriter::vertical($rows, $opt) : ReportSheetWriter::horizontal($rows, $opt));
                $file  = $read(ReportSheetWriter::render($book, $csv ? 'csv' : 'xlsx'), !$csv);
                $files++; $tag = "$kind format $fmt view $v " . ($csv ? 'csv' : 'xlsx');
                $r = 4;
                foreach ($rows as $i => $row) {
                    if ($kind === 'tb') {
                        $want = [[0, $txt($row['group_name'] ?? ''), null], [1, $txt($row['parent'] ?? ''), null], [2, null, $num($row['debit'] ?? '')], [3, null, $num($row['credit'] ?? '')]];
                    } elseif ($fmt === 2) {
                        $want = [[0, $txt($row['group_name'] ?? ''), null], [1, null, $num($row['balance'] ?? '')]];
                    } else {
                        $c = $v === 2 ? [[0, 1, 2], [3, 4, 5]] : [[0, null, 1], [2, null, 3]]; $want = [];
                        foreach ([['l', $c[0]], ['r', $c[1]]] as [$p, [$nc, $dc, $ac]]) {
                            $want[] = [$nc, $txt($row[$p . '_group_name'] ?? ''), null];
                            $want[] = [$ac, null, $num($row[$p . '_balance'] ?? '')];
                            if ($dc !== null) { $want[] = [$dc, null, $num($row[$p . '_detail'] ?? '')]; }
                        }
                    }
                    foreach ($want as [$col, $text, $number]) {
                        $cells++; $got = $cellv($file, $r, $col);
                        if ($number === null && $text !== null) { if (trim((string)$got) !== $text) { $note("$tag row $i col $col: file '" . trim((string)$got) . "' page '$text'"); } }
                        else { $g = $got === null ? null : round((float)$got, 2); if (!$same($g, $number)) { $note("$tag row $i col $col: file " . var_export($g, true) . ' page ' . var_export($number, true)); } }
                    }
                    $r++;
                }
            }
        }
        $this->check('excel', $bad ? 'FAIL' : 'OK', "$files file(s) generated (xlsx + csv, all views and layouts), $cells cell(s) compared with the page rows: " . ($bad ? "$bad difference(s)" : 'no difference') . '.');
        foreach ($examples as $e) { CLI::write('         ' . $e, 'red'); }
        $this->R['excel'] = ['files' => $files, 'cells' => $cells, 'differences' => $bad, 'examples' => $examples];
    }

    // ======================================================================================================
    //  verdict
    // ======================================================================================================

    private function verdict(): void
    {
        $this->heading('VERDICT');
        $by = ['FAIL' => [], 'WARN' => []];
        foreach ($this->R['checks'] as $c) { if (isset($by[$c['status']])) { $by[$c['status']][] = $c; } }
        foreach (['FAIL', 'WARN'] as $st) {
            foreach ($by[$st] as $c) { CLI::write(sprintf('  [%-4s] %s', $st, rtrim(trim(preg_replace('/\s+/', ' ', $c['text'])), ':')), $st === 'FAIL' ? 'red' : 'yellow'); }
        }
        if (!$by['FAIL'] && !$by['WARN']) { CLI::write('  Everything checked is consistent.', 'green'); }
        CLI::newLine();
        CLI::write('  FAIL = the corrected code disagrees with an independent computation (report to the developer).');
        CLI::write('  WARN = a genuine difference supported by the stored data (see the section named in the line); nothing was adjusted to hide it.');
        $this->R['verdict'] = ['fail' => count($by['FAIL']), 'warn' => count($by['WARN'])];
    }
}
