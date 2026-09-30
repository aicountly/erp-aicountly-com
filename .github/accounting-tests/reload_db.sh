#!/usr/bin/env bash
# SANDBOX-ONLY: rebuild the throw-away test database. usage: reload_db.sh basic|stress|pairs|gst|sides|stock|rollover
set -euo pipefail
H=$(dirname "$0"); P="psql -h 127.0.0.1 -p ${ERP_TEST_PGPORT:-5433} -U postgres -v ON_ERROR_STOP=1 -q"
$P -d postgres -c "DROP DATABASE IF EXISTS erp_test" -c "CREATE DATABASE erp_test"
$P -d erp_test -f "$H/schema.sql" >/dev/null
case "${1:-basic}" in
  rollover) $P -d erp_test -f "$H/fixture_rollover.sql" >/dev/null ;;
  *) $P -d erp_test -f "$H/fixture_basic.sql" >/dev/null
     case "${1:-basic}" in
       stress) $P -d erp_test -f "$H/fixture_stress.sql" >/dev/null ;;
       pairs)  $P -d erp_test -f "$H/fixture_pairs.sql" >/dev/null ;;
       gst)    $P -d erp_test -f "$H/fixture_gst.sql" >/dev/null ;;
       sides)  $P -d erp_test -f "$H/fixture_sides.sql" >/dev/null ;;
       stock)  $P -d erp_test -f "$H/fixture_stock.sql" >/dev/null ;;
     esac ;;
esac
echo "erp_test reloaded ($1): $($P -d erp_test -Atc 'select count(*) from accttxnmst') ledger rows"
