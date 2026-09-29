#!/usr/bin/env bash
#
# Production deploy helper for the cPanel host (SSH + rsync).
# Called by .github/workflows/deploy-production.yml - see .github/deploy/README.md.
#
#   deploy.sh selftest   Prove the exclusion rules on a synthetic "production" tree.
#                        Local only: no network, no credentials needed.
#   deploy.sh plan       rsync --dry-run against production. Lists what WOULD change,
#                        transfers nothing.
#   deploy.sh apply      Upload the changes. Refuses to run unless DRY_RUN is exactly "false".
#
# Environment for plan / apply:
#   SSH_HOST, SSH_USER   connection target
#   DEPLOY_PATH          absolute path of the ERP web root on the server
#   SSH_PORT             optional, default 22
#   SSH_KEY_FILE         path to the (passphrase-less) private key file
#   SSH_KNOWN_HOSTS_FILE path to the pinned known_hosts file
#   DRY_RUN              apply only: must be exactly "false"
#
# Safety properties (all exercised by `selftest`):
#   * .env*, .htaccess, /vendor/, /public/, /writable/ are never transferred.
#   * rsync is never run with --delete, so files that exist only on production survive.
#   * Files that ARE overwritten are first moved to a backup directory on the server.
#   * An independent checker fails the run if a protected path shows up in the
#     transfer list, even if rsync's own exclude handling were misconfigured.
# Credentials are read from files/environment and are never printed; shell tracing
# is never enabled.

set -Eeuo pipefail
set +x

here="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
repo_root="${REPO_ROOT:-$(cd "$here/../.." && pwd)}"
exclude_file="$here/rsync-excludes.txt"

die()  { echo "::error::$*" >&2; exit 1; }
note() { echo "==> $*"; }

cleanup_dirs=()
cleanup() {
  local d
  for d in "${cleanup_dirs[@]}"; do
    [ -n "$d" ] && rm -rf "$d"
  done
}
trap cleanup EXIT

# ---------------------------------------------------------------------------
# Rules
# ---------------------------------------------------------------------------

# Every one of these must be present in rsync-excludes.txt.
required_rules=(
  '.env' '.env.*' '.htaccess'
  '/vendor/' '/public/' '/writable/'
  '/.user.ini' '/php.ini'
  '.git' '/.github/'
)

# Independent description of the protected locations (relative to the web root).
# Used only to audit rsync's transfer list; it does not rely on rsync's exclude engine.
protected_re=(
  '(^|/)\.env(\.[^/]*)?(/|$)'
  '(^|/)\.htaccess(/|$)'
  '(^|/)\.git(/|$)'
  '^(vendor|public|writable|\.github)(/|$)'
  '^(\.user\.ini|php\.ini)$'
)

verify_exclude_file() {
  [ -f "$exclude_file" ] || die "Missing $exclude_file"
  local rule
  for rule in "${required_rules[@]}"; do
    grep -qxF -- "$rule" "$exclude_file" \
      || die "rsync-excludes.txt is missing the mandatory rule '$rule'"
  done
  # '+' (include) and '!' (reset) lines could re-admit a protected path.
  if grep -Eq '^[[:space:]]*[+!]' "$exclude_file"; then
    die "rsync-excludes.txt may contain only exclude patterns (no '+' include or '!' reset lines)"
  fi
}

is_protected_path() {
  local p="$1" re
  for re in "${protected_re[@]}"; do
    if [[ $p =~ $re ]]; then
      return 0
    fi
  done
  return 1
}

# Fails (returns 1) if any path in an rsync --itemize-changes listing is protected.
assert_no_protected_paths() {
  local log="$1" line path bad=0
  local itemized_re='^(\*deleting +|[<>ch.][fdLDS][^ ]{9} )(.*)$'
  while IFS= read -r line; do
    [[ $line =~ $itemized_re ]] || continue
    path="${BASH_REMATCH[2]#./}"
    if is_protected_path "$path"; then
      echo "::error::Protected path in the transfer list: $path" >&2
      bad=1
    fi
  done <"$log"
  return "$bad"
}

# ---------------------------------------------------------------------------
# rsync options (identical for selftest, plan and apply)
# ---------------------------------------------------------------------------
#   --checksum        compare content, not mtime: a fresh checkout stamps every file
#                     "now", so mtime comparison would re-upload everything and make
#                     the dry-run list useless.
#   --no-perms/...    never alter permissions, owner or timestamps of files that
#                     already exist on the server.
#   --chmod           permissions for NEW files/dirs only (cPanel-safe 644 / 755).
#   --max-delete=0    belt and braces: refuse any deletion even if --delete were
#                     ever added by mistake.
#   --safe-links      ignore symlinks that point outside the tree.
#   --keep-dirlinks   a directory that is a symlink on the server stays a symlink (files are
#                     written through it) instead of being replaced by a real directory.
common_args=(
  --recursive
  --links --safe-links --keep-dirlinks
  --checksum
  --no-perms --no-owner --no-group --no-times
  '--chmod=D755,F644'
  --max-delete=0
  --itemize-changes --human-readable --stats
  --timeout=120
)

# build_rsync_args <plan|apply> <backup-dir> <exclude-file>  -> fills RSYNC_ARGS
build_rsync_args() {
  local mode="$1" backup_dir="$2" excludes="$3"
  RSYNC_ARGS=("${common_args[@]}" "--exclude-from=$excludes")
  case "$mode" in
    plan)
      RSYNC_ARGS+=(--dry-run)
      ;;
    apply)
      # --delay-updates: stage everything first, then rename into place, so the live
      # site never runs a half-old / half-new code base for long.
      # --backup: overwritten files are moved into the backup dir, never lost.
      RSYNC_ARGS+=(--delay-updates --backup "--backup-dir=$backup_dir")
      ;;
    *) die "internal error: unknown rsync mode '$mode'" ;;
  esac
}

# Prints "<new files> <changed files> <new directories>" for an rsync --itemize-changes log.
# '<f...' (sent to the remote host) is what a push prints; '>f...' is what a local copy prints.
count_items() {
  local log="$1" total new dirs
  total="$(grep -Ec '^[<>]f' "$log" || true)"
  new="$(grep -Ec '^[<>]f\+{9} ' "$log" || true)"
  dirs="$(grep -Ec '^cd\+{9} ' "$log" || true)"
  echo "$new $((total - new)) $dirs"
}

summarize() {
  local log="$1" mode="$2" new updated dirs label
  read -r new updated dirs <<<"$(count_items "$log")"
  if [ "$mode" = plan ]; then label="would be uploaded"; else label="uploaded"; fi
  note "Files $label: $new new, $updated changed (new directories: $dirs)"
  if [ -n "${GITHUB_STEP_SUMMARY:-}" ]; then
    {
      echo "### Production deploy - $mode"
      echo
      echo "- Commit: \`${GITHUB_SHA:-local}\`"
      echo "- New files: **$new**, changed files: **$updated**, new directories: **$dirs**"
      echo "- Protected paths (\`.env*\`, \`.htaccess\`, \`vendor\`, \`public\`, \`writable\`) verified absent from the transfer list"
      echo
      echo "Files ($mode, first 200):"
      echo '```'
      grep -E '^[<>]f' "$log" | head -n 200 || true
      echo '```'
    } >>"$GITHUB_STEP_SUMMARY"
  fi
}

# ---------------------------------------------------------------------------
# selftest: exercise the real rsync binary and the real rules on a fake tree
# ---------------------------------------------------------------------------

selftest() {
  verify_exclude_file
  note "Self-test: exclusion rules against a synthetic production tree"

  local sandbox
  sandbox="$(mktemp -d "${RUNNER_TEMP:-${TMPDIR:-/tmp}}/deploy-selftest.XXXXXX")"
  cleanup_dirs+=("$sandbox")
  local src="$sandbox/repo" prod="$sandbox/production" backup="$sandbox/backup"
  local failures=0

  mk() { mkdir -p "$(dirname "$1")"; printf '%s\n' "$2" >"$1"; }
  fail() { echo "  FAIL: $*"; failures=$((failures + 1)); }
  expect_content() {
    local f="$prod/$1" got
    [ -f "$f" ] || { fail "$1 is missing on production"; return; }
    got="$(cat "$f")"
    [ "$got" = "$2" ] || fail "$1 contains '$got', expected '$2'"
  }
  expect_absent() { [ ! -e "$prod/$1" ] || fail "$1 must not exist on production"; }
  expect_mode() {
    local got
    got="$(stat -c '%a' "$prod/$1")"
    [ "$got" = "$2" ] || fail "$1 has mode $got, expected $2"
  }

  # What the repository (the deploy source) contains.
  mk "$src/index.php" NEW
  mk "$src/app/Controllers/Foo.php" NEW
  mk "$src/app/Controllers/Brand_New.php" NEW
  mk "$src/app/Libraries/vendor/autoload.php" NEW          # nested vendor: MUST deploy
  mk "$src/system/.htaccess" REPO-SYSTEM-HTACCESS          # nested .htaccess: must NOT deploy
  mk "$src/.env" REPO-ENV
  mk "$src/.env.production" REPO-ENV
  mk "$src/.htaccess" REPO-HTACCESS
  mk "$src/vendor/autoload.php" REPO-VENDOR
  mk "$src/vendor/acme/lib.php" REPO-VENDOR
  mk "$src/public/asset.css" REPO-PUBLIC
  mk "$src/writable/comp1/data.json" REPO-WRITABLE
  mk "$src/writable/cache/entry" REPO-WRITABLE
  mk "$src/.git/config" REPO-GIT
  mk "$src/.github/workflows/x.yml" REPO-CI
  mk "$src/.user.ini" REPO-INI
  mk "$src/php.ini" REPO-INI
  mk "$src/app/Helpers/same_helper.php" SAME               # identical on production: must not be touched
  mk "$src/app/Linked/new.php" NEW                         # production: app/Linked is a symlink to a directory

  # What production looks like before the deploy.
  mk "$prod/index.php" OLD
  mk "$prod/app/Controllers/Foo.php" OLD
  mk "$prod/app/Controllers/Prod_Only.php" PROD-ONLY       # not in repo: must survive
  mk "$prod/.env" PROD-ENV
  mk "$prod/.htaccess" PROD-HTACCESS
  mk "$prod/system/.htaccess" PROD-SYSTEM-HTACCESS
  mk "$prod/vendor/autoload.php" PROD-VENDOR
  mk "$prod/vendor/prod_only.php" PROD-VENDOR
  mk "$prod/public/logo.png" PROD-PUBLIC
  mk "$prod/writable/comp1/data.json" PROD-WRITABLE
  mk "$prod/writable/comp1/prod_only.json" PROD-WRITABLE
  mk "$prod/.user.ini" PROD-INI
  mk "$prod/php.ini" PROD-INI
  mk "$prod/uploads/customer.pdf" PROD-ONLY                # unrelated production dir: must survive
  mk "$prod/app/Helpers/same_helper.php" SAME
  mk "$prod/linked_target/existing.php" PROD-ONLY
  ln -s ../linked_target "$prod/app/Linked"
  # Backdate production so an accidental rewrite (new mtime) is detectable.
  find "$prod" -exec touch -d '2020-01-01 00:00:00' {} +
  # Deliberately unusual modes: files/dirs that already exist must keep their permissions.
  chmod 640 "$prod/index.php"
  chmod 750 "$prod/app/Controllers"

  # Everything that must be byte-, mode- and mtime-identical after the deploy.
  local protected_set=(.env .htaccess system/.htaccess vendor public writable
    .user.ini php.ini uploads app/Controllers/Prod_Only.php)
  fingerprint() { tar -C "$prod" --sort=name -cf - "${protected_set[@]}" | sha256sum | cut -d' ' -f1; }
  whole_tree()  { tar -C "$prod" --sort=name -cf - . | sha256sum | cut -d' ' -f1; }

  local before_protected before_tree same_mtime_before
  before_protected="$(fingerprint)"
  before_tree="$(whole_tree)"
  same_mtime_before="$(stat -c '%Y %a' "$prod/app/Helpers/same_helper.php")"
  if [ "$(id -u)" -eq 0 ]; then
    # Foreign owner in the source: --owner/--group would leak it onto production.
    chown 4242:4242 "$src/index.php" "$src/app/Controllers/Brand_New.php"
  fi

  # 1. Dry run must change nothing at all and list no protected path.
  build_rsync_args plan "$backup" "$exclude_file"
  (umask 000; rsync "${RSYNC_ARGS[@]}" "$src/" "$prod/") >"$sandbox/plan.log" 2>&1 \
    || { cat "$sandbox/plan.log"; fail "dry-run rsync failed"; }
  [ "$(whole_tree)" = "$before_tree" ] || fail "dry run modified the production tree"
  assert_no_protected_paths "$sandbox/plan.log" || fail "plan lists a protected path"
  grep -q 'app/Libraries/vendor/autoload.php' "$sandbox/plan.log" \
    || fail "plan does not list app/Libraries/vendor/autoload.php (nested vendor code must deploy)"

  # 2. Real run with the same options + backup, exactly as `apply` does.
  mkdir -p "$backup"
  build_rsync_args apply "$backup" "$exclude_file"
  (umask 000; rsync "${RSYNC_ARGS[@]}" "$src/" "$prod/") >"$sandbox/apply.log" 2>&1 \
    || { cat "$sandbox/apply.log"; fail "apply rsync failed"; }
  assert_no_protected_paths "$sandbox/apply.log" || fail "apply transferred a protected path"

  # 3. Protected production files: untouched (content, mode, mtime), nothing added or removed.
  [ "$(fingerprint)" = "$before_protected" ] \
    || fail "a protected production file/dir was modified, added or removed"
  expect_content .env PROD-ENV
  expect_content .htaccess PROD-HTACCESS
  expect_content system/.htaccess PROD-SYSTEM-HTACCESS
  expect_content vendor/autoload.php PROD-VENDOR
  expect_content public/logo.png PROD-PUBLIC
  expect_content writable/comp1/data.json PROD-WRITABLE
  expect_content .user.ini PROD-INI
  expect_content php.ini PROD-INI
  expect_absent .env.production
  expect_absent public/asset.css
  expect_absent vendor/acme
  expect_absent writable/cache
  expect_absent .git
  expect_absent .github

  # 4. Nothing that only exists on production was deleted.
  expect_content app/Controllers/Prod_Only.php PROD-ONLY
  expect_content vendor/prod_only.php PROD-VENDOR
  expect_content writable/comp1/prod_only.json PROD-WRITABLE
  expect_content uploads/customer.pdf PROD-ONLY

  # 5. Intended deployment content arrived, with cPanel-safe permissions.
  expect_content index.php NEW
  expect_content app/Controllers/Foo.php NEW
  expect_content app/Controllers/Brand_New.php NEW
  expect_content app/Libraries/vendor/autoload.php NEW
  expect_mode app/Controllers/Brand_New.php 644
  expect_mode app/Libraries 755
  expect_mode app/Libraries/vendor 755
  # ...while existing files and directories keep the permissions they already had.
  expect_mode index.php 640
  expect_mode app/Controllers 750
  # A file that is already identical on production is neither listed nor touched (mtime/mode).
  [ "$(stat -c '%Y %a' "$prod/app/Helpers/same_helper.php")" = "$same_mtime_before" ] \
    || fail "app/Helpers/same_helper.php was touched although its content is identical"
  if grep -q 'same_helper.php' "$sandbox/plan.log"; then
    fail "plan lists same_helper.php although its content is identical"
  fi
  if [ "$(id -u)" -eq 0 ]; then
    [ "$(stat -c '%u' "$prod/app/Controllers/Brand_New.php")" != 4242 ] \
      || fail "source ownership leaked onto production (--owner in effect)"
    [ "$(stat -c '%u' "$prod/index.php")" != 4242 ] || fail "source ownership leaked onto production"
  fi

  # 5b. A symlinked directory on production is preserved; files are written through it.
  [ -L "$prod/app/Linked" ] || fail "app/Linked symlink on production was replaced"
  expect_content linked_target/new.php NEW
  expect_content linked_target/existing.php PROD-ONLY

  # 6. Overwritten files were backed up first; protected files never appear in the backup.
  [ "$(cat "$backup/index.php" 2>/dev/null)" = OLD ] || fail "backup of overwritten index.php missing"
  [ "$(cat "$backup/app/Controllers/Foo.php" 2>/dev/null)" = OLD ] \
    || fail "backup of overwritten Foo.php missing"
  if find "$backup" \( -name '.env*' -o -name '.htaccess' -o -path '*/vendor/acme*' -o -path '*/writable*' \) \
    -print -quit | grep -q .; then
    fail "backup contains protected files"
  fi

  # 7. A second run is a no-op (checksum comparison gives a clean, meaningful diff).
  build_rsync_args plan "$backup" "$exclude_file"
  (umask 000; rsync "${RSYNC_ARGS[@]}" "$src/" "$prod/") >"$sandbox/plan2.log" 2>&1 || fail "second dry-run failed"
  if grep -Eq '^(\*deleting|[<>ch.][fdLDS])' "$sandbox/plan2.log"; then
    grep -E '^(\*deleting|[<>ch.][fdLDS])' "$sandbox/plan2.log" | sed 's/^/    /'
    fail "second run is not idempotent"
  fi

  # 7b. The summary counters must understand both rsync directions ('<' push, '>' local copy).
  local count_log="$sandbox/count.log"
  printf '%s\n' '<fcsT...... app/A.php' '<f+++++++++ app/B.php' 'cd+++++++++ app/new/' \
    '<f+++++++++ app/new/C.php' 'sent 1 bytes  received 2 bytes' >"$count_log"
  [ "$(count_items "$count_log")" = "2 1 1" ] || fail "count_items wrong for push-format log: $(count_items "$count_log")"
  sed 's/^</>/' "$count_log" >"$sandbox/count2.log"
  [ "$(count_items "$sandbox/count2.log")" = "2 1 1" ] || fail "count_items wrong for local-format log"
  : >"$sandbox/count3.log"
  [ "$(count_items "$sandbox/count3.log")" = "0 0 0" ] || fail "count_items wrong for an empty log"

  # 8. The independent plan-checker must catch protected paths on its own.
  local bad_log="$sandbox/bad.log" ok_log="$sandbox/ok.log" p
  printf '%s\n' \
    '>f.st...... app/Controllers/Foo.php' \
    '>f+++++++++ app/Libraries/vendor/autoload.php' \
    'cd+++++++++ app/Libraries/vendor/' >"$ok_log"
  assert_no_protected_paths "$ok_log" 2>/dev/null || fail "plan-checker rejects legitimate paths"
  for p in '.env' '.env.production' 'x/.env' '.htaccess' 'system/.htaccess' 'vendor/autoload.php' \
    'public/logo.png' 'writable/comp1/a.json' '.git/config' '.github/workflows/x.yml' \
    '.user.ini' 'php.ini'; do
    printf '>fc.T...... %s\n' "$p" >"$bad_log"
    if assert_no_protected_paths "$bad_log" 2>/dev/null; then fail "plan-checker did not flag '$p'"; fi
    printf '<fcsT...... %s\n' "$p" >"$bad_log"
    if assert_no_protected_paths "$bad_log" 2>/dev/null; then fail "plan-checker did not flag '$p' (push format)"; fi
  done
  printf '*deleting   writable/comp1/data.json\n' >"$bad_log"
  if assert_no_protected_paths "$bad_log" 2>/dev/null; then fail "plan-checker did not flag a protected deletion"; fi

  # 9. Mutation test: with the '.env' rule removed, rsync WOULD send .env;
  #    the checker must notice (proves the second layer works independently).
  local broken="$sandbox/broken-excludes.txt"
  grep -vxF '.env' "$exclude_file" >"$broken"
  build_rsync_args plan "$backup" "$broken"
  (umask 000; rsync "${RSYNC_ARGS[@]}" "$src/" "$prod/") >"$sandbox/mutant.log" 2>&1 || true
  if assert_no_protected_paths "$sandbox/mutant.log" 2>/dev/null; then
    fail "mutation test: checker did not notice a broken exclude file"
  fi

  if [ "$failures" -ne 0 ]; then
    die "Self-test FAILED ($failures problem(s)); nothing was sent to production"
  fi
  note "Self-test passed: protected paths untouched, production-only files preserved, backups taken, checker verified"
}

# ---------------------------------------------------------------------------
# plan / apply: talk to the server
# ---------------------------------------------------------------------------

init_remote() {
  local v
  for v in SSH_HOST SSH_USER DEPLOY_PATH SSH_KEY_FILE SSH_KNOWN_HOSTS_FILE; do
    [ -n "${!v:-}" ] || die "Environment variable $v is not set"
  done
  SSH_PORT="${SSH_PORT:-22}"
  DEPLOY_PATH="${DEPLOY_PATH%/}"

  local port_re='^[0-9]{1,5}$' host_re='^[A-Za-z0-9._:-]+$' user_re='^[A-Za-z0-9._-]+$'
  local path_re='^/[A-Za-z0-9._-]+(/[A-Za-z0-9._-]+)+$' file_re='^/[A-Za-z0-9._/-]+$'
  [[ $SSH_PORT =~ $port_re ]] && [ "$SSH_PORT" -ge 1 ] && [ "$SSH_PORT" -le 65535 ] \
    || die "SSH_PORT must be a number between 1 and 65535"
  # shellcheck disable=SC2153  # SSH_HOST is supplied by the caller's environment
  [[ $SSH_HOST =~ $host_re ]] || die "SSH_HOST contains unexpected characters"
  [[ $SSH_USER =~ $user_re ]] || die "SSH_USER contains unexpected characters"
  [[ $DEPLOY_PATH =~ $path_re ]] \
    || die "DEPLOY_PATH must be an absolute path with at least two components, e.g. /home/<cpanel-user>/public_html (letters, digits, . _ - and / only)"
  [[ $DEPLOY_PATH != *..* ]] || die "DEPLOY_PATH must not contain '..'"
  [[ $SSH_KEY_FILE =~ $file_re && -r $SSH_KEY_FILE ]] || die "SSH_KEY_FILE is not a readable absolute path"
  [[ $SSH_KNOWN_HOSTS_FILE =~ $file_re && -r $SSH_KNOWN_HOSTS_FILE ]] \
    || die "SSH_KNOWN_HOSTS_FILE is not a readable absolute path"

  work_dir="$(mktemp -d "${RUNNER_TEMP:-${TMPDIR:-/tmp}}/deploy-work.XXXXXX")"
  cleanup_dirs+=("$work_dir")
  ssh_config="$work_dir/ssh_config"
  (
    umask 077
    cat >"$ssh_config" <<EOF
Host deploy-target
  HostName $SSH_HOST
  Port $SSH_PORT
  User $SSH_USER
  IdentityFile $SSH_KEY_FILE
  IdentitiesOnly yes
  IdentityAgent none
  BatchMode yes
  StrictHostKeyChecking yes
  UserKnownHostsFile $SSH_KNOWN_HOSTS_FILE
  GlobalKnownHostsFile /dev/null
  ConnectTimeout 20
  ServerAliveInterval 15
  ServerAliveCountMax 4
  ForwardAgent no
  ForwardX11 no
  LogLevel ERROR
EOF
  )
}

remote() { ssh -F "$ssh_config" deploy-target "$@"; }

remote_preflight() {
  note "Checking SSH access and the target directory on production"
  local out status
  out="$(remote "cd '$DEPLOY_PATH' 2>/dev/null || { echo NO_DIR; exit 0; }
[ -w . ] || { echo NOT_WRITABLE; exit 0; }
[ -f index.php ] || { echo NO_INDEX; exit 0; }
[ -f app/Config/Paths.php ] || { echo NO_PATHS; exit 0; }
command -v rsync >/dev/null 2>&1 || { echo NO_RSYNC; exit 0; }
echo OK")" \
    || die "SSH connection to production failed. Check SSH_HOST, SSH_PORT, SSH_USER, the private key and SSH_KNOWN_HOSTS."
  status="${out##*$'\n'}"
  case "$status" in
    OK) ;;
    NO_DIR)       die "DEPLOY_PATH does not exist on the server (it is never created automatically)" ;;
    NOT_WRITABLE) die "DEPLOY_PATH is not writable by the SSH user" ;;
    NO_INDEX|NO_PATHS)
      die "DEPLOY_PATH does not look like the ERP web root (index.php and app/Config/Paths.php expected). Refusing to deploy into an unknown directory." ;;
    NO_RSYNC)     die "rsync is not installed on the server (or not in the SSH user's PATH)" ;;
    *)            die "Unexpected answer from the server during preflight" ;;
  esac

  # Single quotes are intentional: $HOME must be expanded by the remote shell.
  # shellcheck disable=SC2016
  remote_home="$(remote 'printf %s "$HOME"')"
  remote_home="${remote_home##*$'\n'}"
  local home_re='^/[A-Za-z0-9._/-]+$'
  [[ $remote_home =~ $home_re ]] || die "Could not determine a safe home directory on the server"
  local sha="${GITHUB_SHA:-local}" stamp
  stamp="$(date -u +%Y%m%dT%H%M%SZ)-${sha:0:7}"
  backup_dir="$remote_home/erp-deploy-backups/$stamp"
  case "$backup_dir/" in
    "$DEPLOY_PATH"/*) die "The backup directory would be inside DEPLOY_PATH; DEPLOY_PATH must not be the home directory" ;;
  esac
}

run_rsync() {
  local mode="$1" log rc=0
  log="$work_dir/rsync-$mode.log"
  build_rsync_args "$mode" "${backup_dir:-}" "$exclude_file"
  rsync "${RSYNC_ARGS[@]}" -e "ssh -F $ssh_config" "$repo_root/" "deploy-target:$DEPLOY_PATH/" 2>&1 \
    | tee "$log" || rc=$?
  [ "$rc" -eq 0 ] || die "rsync ($mode) failed with exit code $rc"
  assert_no_protected_paths "$log" || die "A protected path is in the $mode transfer list - aborting"
  summarize "$log" "$mode"
}

plan() {
  verify_exclude_file
  init_remote
  remote_preflight
  note "Dry run against production (nothing is transferred)"
  run_rsync plan
}

apply() {
  [ "${DRY_RUN:-}" = "false" ] \
    || die "Refusing to upload: DRY_RUN must be exactly 'false' (got '${DRY_RUN:-<unset>}')"
  if [ "${GITHUB_ACTIONS:-}" = "true" ] && [ "${GITHUB_REF:-}" != "refs/heads/${DEPLOY_BRANCH:-main}" ]; then
    die "Real deployments are only allowed from '${DEPLOY_BRANCH:-main}'"
  fi
  verify_exclude_file
  init_remote
  remote_preflight

  note "Pre-upload verification (dry run)"
  run_rsync plan

  note "Uploading to production; overwritten files are backed up on the server first"
  remote "mkdir -p '$backup_dir'"
  run_rsync apply

  # Leave an audit/rollback record next to the backups (paths only, no secrets).
  {
    echo "commit:  ${GITHUB_SHA:-local}"
    echo "actor:   ${GITHUB_ACTOR:-local}"
    echo "run:     ${GITHUB_RUN_ID:-local}"
    echo "when:    $(date -u +%Y-%m-%dT%H:%M:%SZ)"
    echo
    echo "Files sent by this deploy. '<f+++++++++' = new file (delete it to roll back);"
    echo "every other '<f' line replaced an existing file, whose previous version is in this directory."
    grep -E '^([<>]f|cd)' "$work_dir/rsync-apply.log" || true
  } | remote "cat > '$backup_dir/DEPLOY-LOG.txt'"
  note "Previous versions of overwritten files + DEPLOY-LOG.txt are kept on the server in: ~/erp-deploy-backups/${backup_dir##*/}"
}

case "${1:-}" in
  selftest) selftest ;;
  plan)     plan ;;
  apply)    apply ;;
  *) echo "usage: $0 selftest|plan|apply" >&2; exit 2 ;;
esac
