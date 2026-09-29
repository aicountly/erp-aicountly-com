#!/usr/bin/env python3
"""SANDBOX-ONLY: prove the screen-vs-file parity test can fail. Each mutant edits the sandbox copy only."""
import subprocess, sys, os
SBX=os.environ.get('SBX','/var/tmp/erp_sbx')+'/app/'
H=os.path.dirname(os.path.abspath(__file__))
def sh(cmd, **k): return subprocess.run(cmd, shell=True, capture_output=True, text=True, **k)
M=[
 # (name, report, file, old, new)
 ('tb_swap_debit_credit','trial_balance','Libraries/ReportSheetWriter.php',
  "self::putNumber($sh, \"C{$r}\", $d, $fmt);\n            self::putNumber($sh, \"D{$r}\", $c, $fmt);",
  "self::putNumber($sh, \"C{$r}\", $c, $fmt);\n            self::putNumber($sh, \"D{$r}\", $d, $fmt);"),
 ('h_drop_last_row','balance_sheet','Libraries/ReportSheetWriter.php',
  "foreach ($rows as $row) {\n            $isTotal",
  "foreach (array_slice($rows, 0, -1) as $row) {\n            $isTotal"),
 ('blank_becomes_zero','profit_loss','Libraries/ReportSheetWriter.php',
  "if (!$always) { return; }                                             // blank stays blank",
  "if (false) { return; }"),
 ('amount_abs','balance_sheet','Libraries/ReportSheetWriter.php',
  "round((float)$value, 2), DataType::TYPE_NUMERIC",
  "abs(round((float)$value, 2)), DataType::TYPE_NUMERIC"),
 ('detail_column_dropped','profit_loss','Libraries/ReportSheetWriter.php',
  "if ($detail) { self::putNumber($sh, \"{$dc}{$r}\", $row[$p . '_det'] ?? null, $fmt); }",
  ""),
 ('notes_not_written','balance_sheet','Libraries/ReportSheetWriter.php',
  "$notes = array_values(array_filter($notes, static fn($n) => (string)$n !== ''));",
  "$notes = [];"),
 ('bs_export_nil_constant','balance_sheet','Controllers/Admin/Export.php',
  "$p['format'], $p['view'], $p['from_date'], $p['to_date'], $p['nil_type'], $p['consolidated']);\n    $opt   = [\n        'title'      => $this->reportTitle(),\n        'subtitle'   => 'Balance Sheet",
  "$p['format'], $p['view'], $p['from_date'], $p['to_date'], 1, $p['consolidated']);\n    $opt   = [\n        'title'      => $this->reportTitle(),\n        'subtitle'   => 'Balance Sheet"),
 ('pl_export_ignores_consolidated','profit_loss','Controllers/Admin/Export.php',
  "$p['format'], $p['view'], $p['from_date'], $p['to_date'], $p['nil_type'], $p['consolidated']);\n    $opt   = [\n        'title'      => $this->reportTitle(),\n        'subtitle'   => 'Profit",
  "$p['format'], $p['view'], $p['from_date'], $p['to_date'], $p['nil_type'], 0);\n    $opt   = [\n        'title'      => $this->reportTitle(),\n        'subtitle'   => 'Profit"),
 ('tb_export_view_zero','trial_balance','Controllers/Admin/Export.php',
  "$p['view'], $p['from_date'], $p['to_date'], $p['consolidated'], $p['nil_type']);\n    $opt      = [",
  "0, $p['from_date'], $p['to_date'], $p['consolidated'], $p['nil_type']);\n    $opt      = ["),
 ('bs_screen_ignores_consolidated','balance_sheet','Controllers/Admin/Reportings.php',
  "$p['format'], $p['view'], $p['from_date'], $p['to_date'], $p['nil_type'], $p['consolidated']);\n\n     $data     = [",
  "$p['format'], $p['view'], $p['from_date'], $p['to_date'], $p['nil_type'], 0);\n\n     $data     = ["),
 ('pl_screen_nil_zero','profit_loss','Controllers/Admin/Reportings.php',
  "$p['format'], $p['view'], $p['from_date'], $p['to_date'], $p['nil_type'], $p['consolidated']);\n        $data = [",
  "$p['format'], $p['view'], $p['from_date'], $p['to_date'], 0, $p['consolidated']);\n        $data = ["),
 ('tb_screen_view_default_differs','trial_balance','Controllers/Admin/Reportings.php',
  "$p['view'], $p['from_date'], $p['to_date'], $p['consolidated'], $p['nil_type']);\n\n        $data['from_date']",
  "$p['view'] === 2 ? 1 : $p['view'], $p['from_date'], $p['to_date'], $p['consolidated'], $p['nil_type']);\n\n        $data['from_date']"),
 ('params_dates_not_clamped_in_export','balance_sheet','Controllers/Admin/Export.php',
  "$p     = ReportParams::balanceSheet($_GET);\n    $rows  = $this->ReportingModel->load_balance_sheet_view(",
  "$p     = ReportParams::balanceSheet($_GET); $p['from_date'] = '01-04-2025'; $p['to_date'] = '30-06-2025';\n    $rows  = $this->ReportingModel->load_balance_sheet_view("),
]
only=sys.argv[1] if len(sys.argv)>1 else None
res=[]
for name,rep,f,old,new in M:
    if only and only!=name: continue
    sh(f'bash {H}/sync_sandbox.sh')
    p=SBX+f; s=open(p,encoding='utf-8').read()
    if s.count(old)!=1: res.append((name,'BAD-MUTANT (anchor count %d)'%s.count(old))); continue
    open(p,'w',encoding='utf-8').write(s.replace(old,new))
    r=sh(f'php -l {p}')
    if r.returncode: res.append((name,'BAD-MUTANT (syntax)')); continue
    t=sh(f'ONLY={rep} php {H}/test_export.php', cwd=H)
    last=[l for l in t.stdout.strip().split('\n') if l][-1] if t.stdout.strip() else t.stderr[-200:]
    res.append((name,('KILLED' if t.returncode!=0 else 'SURVIVED')+' -- '+last))
    print(name, res[-1][1], flush=True)
sh(f'bash {H}/sync_sandbox.sh')
bad=[r for r in res if not r[1].startswith('KILLED')]
print('\n%d mutants, %d killed, %d not killed'%(len(res),len(res)-len(bad),len(bad)))
sys.exit(1 if bad else 0)
