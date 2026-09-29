#!/usr/bin/env bash
# SANDBOX-ONLY: full regression. Needs a throw-away PostgreSQL on 127.0.0.1:5433 (see README). Never point this at real data.
set -uo pipefail
H=$(cd "$(dirname "$0")" && pwd); cd "$H"
bash ./sync_sandbox.sh >/dev/null
rc=0
run() { local name=$1; shift; local out; out=$("$@" 2>&1); local st=$?; printf '%-46s %s\n' "$name" "$(echo "$out" | tail -1)"; [ $st -eq 0 ] || { rc=1; echo "$out" | grep -E 'FAIL|Error|error' | head -20; }; }
./reload_db.sh basic >/dev/null 2>&1
run "engine (basic ledger)"            php test_engine.php
run "profit & loss (basic ledger)"     php test_pl.php
run "balance sheet (basic ledger)"     php test_bs.php
run "trial balance (basic ledger)"     php test_tb.php
./reload_db.sh stress >/dev/null 2>&1
run "ledger identities (stress ledger)"  php test_invariants.php
run "views render + export buttons"      php render_views.php
run "export files == page rows"          php test_export.php
./reload_db.sh rollover >/dev/null 2>&1
run "FY roll-over (Rewrite Books)"       php test_rollover.php
run "audit command (read only, attribution)"     php test_audit.php
./reload_db.sh stress >/dev/null 2>&1
run "old code vs the same identities (demo)"  bash -c 'php demo_legacy_failures.php | head -1; exit 0'
exit $rc
