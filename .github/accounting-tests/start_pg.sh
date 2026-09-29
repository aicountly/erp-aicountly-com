#!/usr/bin/env bash
# SANDBOX-ONLY: start a throw-away PostgreSQL (trust auth, 127.0.0.1 only) for these tests. Holds synthetic data only.
#   ERP_TEST_PGPORT (default 5433)   ERP_TEST_PGROOT (default /var/tmp/erp_pgtest)
set -euo pipefail
PORT=${ERP_TEST_PGPORT:-5433}; ROOT=${ERP_TEST_PGROOT:-/var/tmp/erp_pgtest}
BIN=$(find /usr/lib/postgresql -mindepth 2 -maxdepth 2 -type d -name bin 2>/dev/null | sort -V | tail -n 1 || true)
[ -n "$BIN" ] || { echo "PostgreSQL server binaries not found under /usr/lib/postgresql" >&2; exit 1; }
as_pg() { if [ "$(id -u)" = 0 ]; then su postgres -s /bin/bash -c "$1"; else bash -c "$1"; fi; }
if [ ! -d "$ROOT/data" ]; then
  mkdir -p "$ROOT"; [ "$(id -u)" = 0 ] && chown postgres "$ROOT"
  as_pg "$BIN/initdb -D $ROOT/data --auth=trust -U postgres > $ROOT/initdb.log"
fi
if ! as_pg "$BIN/pg_ctl -D $ROOT/data status" >/dev/null 2>&1; then
  as_pg "$BIN/pg_ctl -D $ROOT/data -o '-p $PORT -c listen_addresses=127.0.0.1 -c unix_socket_directories=$ROOT' -l $ROOT/server.log start -w" >/dev/null
fi
echo "throw-away PostgreSQL ready on 127.0.0.1:$PORT (data in $ROOT)"
