#!/usr/bin/env bash
# SANDBOX-ONLY: rebuild the throw-away test database. usage: reload_db.sh basic|stress|rollover
set -euo pipefail
H=$(dirname "$0"); P="psql -h 127.0.0.1 -p ${ERP_TEST_PGPORT:-5433} -U postgres -v ON_ERROR_STOP=1 -q"
$P -d postgres -c "DROP DATABASE IF EXISTS erp_test" -c "CREATE DATABASE erp_test"
$P -d erp_test -f "$H/schema.sql" >/dev/null
case "${1:-basic}" in
  rollover) $P -d erp_test -f "$H/fixture_rollover.sql" >/dev/null ;;
  *) $P -d erp_test -f "$H/fixture_basic.sql" >/dev/null; [ "${1:-basic}" = stress ] && $P -d erp_test -f "$H/fixture_stress.sql" >/dev/null ;;
esac
echo "erp_test reloaded ($1): $($P -d erp_test -Atc 'select count(*) from accttxnmst') ledger rows"
