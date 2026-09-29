#!/usr/bin/env bash
# SANDBOX-ONLY: refresh the sandbox copy of the application code (no .env, no third-party bundles, own writable/).
set -euo pipefail
REPO=${REPO:-$(cd "$(dirname "$0")/../.." && pwd)}
SBX=${SBX:-/var/tmp/erp_sbx}
mkdir -p "$SBX/writable"/{cache,logs,session,debugbar,uploads,errors}
rsync -a --delete --exclude='Libraries/vendor' --exclude='ThirdParty' "$REPO/app/" "$SBX/app/"
rsync -a --delete "$REPO/system/" "$SBX/system/"
[ -e "$SBX/vendor" ] || ln -s "$REPO/vendor" "$SBX/vendor"
cp -f "$REPO/composer.json" "$SBX/composer.json" 2>/dev/null || true
echo "sandbox synced at $SBX (no .env present: $( [ -e "$SBX/.env" ] && echo NO-FOUND-ONE || echo confirmed ))"
