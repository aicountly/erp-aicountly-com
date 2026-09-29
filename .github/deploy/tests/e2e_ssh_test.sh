#!/usr/bin/env bash
# End-to-end test of .github/deploy/deploy.sh (plan/apply) over a REAL ssh connection to a
# throwaway sshd on 127.0.0.1:2222, using a non-root "deployer" user and a fake production docroot.
# LOCAL ONLY: needs root (it creates a throw-away user "deployer", /home/deployer and an sshd on 127.0.0.1:2222,
# and removes them again if it created them), git, rsync, openssh-server, python3 with PyYAML. Nothing here talks to a real server.
set -uo pipefail
SCR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
E2E="${E2E_DIR:-/var/tmp/erp_deploy_e2e}"
REPO="$(cd "$SCR/../../.." && pwd)"
[ "$(id -u)" = 0 ] || { echo "e2e_ssh_test.sh must run as root (throw-away user + sshd); see .github/deploy/README.md"; exit 2; }
case "$E2E" in /var/tmp/?*|/tmp/?*) ;; *) echo "E2E_DIR must be a folder under /tmp or /var/tmp (it is emptied first)"; exit 2 ;; esac
DEPLOY="$REPO/.github/deploy/deploy.sh"
PROD=/home/deployer/public_html
BACKUPS=/home/deployer/erp-deploy-backups
PASS=0; FAILN=0
ok()  { echo "  PASS: $*"; PASS=$((PASS+1)); }
bad() { echo "  FAIL: $*"; FAILN=$((FAILN+1)); }
check() { local desc="$1"; shift; if "$@" >/dev/null 2>&1; then ok "$desc"; else bad "$desc"; fi; }

prod_hash()      { tar -C "$PROD" --sort=name -cf - . | sha256sum | cut -d' ' -f1; }
protected_hash() { tar -C "$PROD" --sort=name -cf - .env .htaccess vendor public writable .user.ini php.ini system/.htaccess uploads 2>/dev/null | sha256sum | cut -d' ' -f1; }

stop_sshd() { [ -f "$E2E/sshd/sshd.pid" ] && kill "$(cat "$E2E/sshd/sshd.pid")" 2>/dev/null; sleep 0.5; }
created_user=0
# Leave the machine as it was: stop sshd, and remove the user and the working folder this script created
# (E2E_KEEP=1 keeps the folder with the sshd log for debugging).
# shellcheck disable=SC2329  # runs through the EXIT trap
cleanup() {
  stop_sshd
  if [ "$created_user" = 1 ]; then userdel -r deployer >/dev/null 2>&1; fi
  if [ "${E2E_KEEP:-0}" != 1 ]; then rm -rf "$E2E"; fi
  return 0
}
trap cleanup EXIT

setup() {
  stop_sshd
  rm -rf "$E2E" "$BACKUPS" "$PROD" /home/deployer/other_site
  mkdir -p "$E2E/sshd" "$E2E/client" /run/sshd
  if ! id deployer >/dev/null 2>&1; then useradd -m -s /bin/bash deployer && created_user=1; fi
  usermod -p '*' deployer
  ssh-keygen -q -t ed25519 -N '' -f "$E2E/sshd/host_ed25519"
  ssh-keygen -q -t ed25519 -N '' -C test-deploy-key -f "$E2E/client/id_deploy"
  ssh-keygen -q -t ed25519 -N '' -f "$E2E/client/wrong_host"
  install -d -m 700 -o deployer -g deployer /home/deployer/.ssh
  install -m 600 -o deployer -g deployer "$E2E/client/id_deploy.pub" /home/deployer/.ssh/authorized_keys
  cat >"$E2E/sshd/sshd_config" <<EOF
Port 2222
ListenAddress 127.0.0.1
HostKey $E2E/sshd/host_ed25519
PidFile $E2E/sshd/sshd.pid
PasswordAuthentication no
KbdInteractiveAuthentication no
PubkeyAuthentication yes
UsePAM no
PermitRootLogin no
AllowUsers deployer
LogLevel INFO
EOF
  /usr/sbin/sshd -f "$E2E/sshd/sshd_config" -E "$E2E/sshd/sshd.log" || { echo "sshd failed to start"; exit 2; }
  sleep 1
  ssh-keyscan -p 2222 -t ed25519 127.0.0.1 2>/dev/null >"$E2E/client/known_hosts"
  # known_hosts that carries a DIFFERENT host key for the same host:port (MITM simulation)
  printf '[127.0.0.1]:2222 %s\n' "$(cut -d' ' -f1,2 "$E2E/client/wrong_host.pub")" >"$E2E/client/known_hosts_wrong"

  # "Repository checkout": exactly what actions/checkout would give, plus .git/.github.
  mkdir -p "$E2E/repo"
  git -C "$REPO" archive HEAD | tar -x -C "$E2E/repo"
  mkdir -p "$E2E/repo/.git" && echo "gitdata" >"$E2E/repo/.git/config"

  # Fake production: deployable subset identical to the repo, plus production-only sentinels.
  mkdir -p "$PROD"
  rsync -rl --checksum --exclude-from="$REPO/.github/deploy/rsync-excludes.txt" "$E2E/repo/" "$PROD/"
  printf 'PROD-ENV-SENTINEL\n'      >"$PROD/.env"
  printf 'PROD-HTACCESS-SENTINEL\n' >"$PROD/.htaccess"
  printf 'PROD-SYSTEM-HTACCESS\n'   >"$PROD/system/.htaccess"
  mkdir -p "$PROD/vendor/acme" "$PROD/public/img" "$PROD/writable/comp1" "$PROD/uploads"
  printf 'PROD-VENDOR\n'   >"$PROD/vendor/autoload.php"
  printf 'PROD-VENDOR\n'   >"$PROD/vendor/acme/lib.php"
  printf 'PROD-PUBLIC\n'   >"$PROD/public/img/logo.png"
  printf 'PROD-WRITABLE\n' >"$PROD/writable/comp1/data.json"
  printf 'PROD-INI\n'      >"$PROD/.user.ini"
  printf 'PROD-INI\n'      >"$PROD/php.ini"
  printf 'PROD-UPLOAD\n'   >"$PROD/uploads/customer.pdf"
  printf 'PROD-ONLY\n'     >"$PROD/app/Controllers/Prod_Only.php"
  chown -R deployer:deployer /home/deployer
  find "$PROD" -exec touch -d '2020-01-01 00:00:00' {} + 2>/dev/null
  chown -R deployer:deployer /home/deployer
}

# Runs deploy.sh with the same variables the workflow provides.
run_deploy() {  # mode, then VAR=value overrides
  local mode="$1"; shift
  env -i PATH="$PATH" HOME=/root REPO_ROOT="$E2E/repo" \
    SSH_HOST=127.0.0.1 SSH_PORT=2222 SSH_USER=deployer DEPLOY_PATH="$PROD" \
    SSH_KEY_FILE="$E2E/client/id_deploy" SSH_KNOWN_HOSTS_FILE="$E2E/client/known_hosts" \
    RUNNER_TEMP="$E2E" GITHUB_SHA=0123456789abcdef0123456789abcdef01234567 \
    "$@" bash "$DEPLOY" "$mode"
}

echo "################ SETUP ################"
setup && echo "sshd up, fake production seeded ($(find "$PROD" -type f | wc -l) files)"
KEYBODY="$(sed -n '2p' "$E2E/client/id_deploy" | cut -c1-24)"

echo; echo "################ S1: identical trees -> plan lists nothing ################"
H0=$(prod_hash)
run_deploy plan >"$E2E/s1.log" 2>&1; rc=$?
check "plan exits 0" test $rc -eq 0
check "no file would be transferred" bash -c "! grep -Eq '^([<>]f|cd|\*deleting)' '$E2E/s1.log'"
check "production untouched by a dry run" test "$(prod_hash)" = "$H0"
grep -E '^==>' "$E2E/s1.log" | sed 's/^/    /'

echo; echo "################ S2: real change set: plan, then apply ################"
# modify 3 deployable files, add 1 new file (source side only)
printf '\n// deploy-test change\n' >>"$E2E/repo/app/Controllers/Admin/Dashboard.php"
printf '\n// deploy-test change\n' >>"$E2E/repo/app/Config/Routes.php"
printf '\n// deploy-test change\n' >>"$E2E/repo/index.php"
mkdir -p "$E2E/repo/app/Controllers/Admin"
printf '<?php // brand new\n' >"$E2E/repo/app/Controllers/Admin/ZzDeployTest.php"
# ...and change every protected item on the SOURCE side: none of these may reach production
printf 'REPO-ENV-CHANGED\n'      >"$E2E/repo/.env"
printf 'REPO-HTACCESS-CHANGED\n' >"$E2E/repo/.htaccess"
printf 'REPO-SYSHT-CHANGED\n'    >"$E2E/repo/system/.htaccess"
printf 'REPO-VENDOR-CHANGED\n'   >"$E2E/repo/vendor/autoload.php"
printf '{"changed":true}\n'      >"$(find "$E2E/repo/writable/comp1" -maxdepth 1 -type f | sort | head -n 1)"
mkdir -p "$E2E/repo/public" && printf 'REPO-PUBLIC\n' >"$E2E/repo/public/new.css"
printf 'REPO-INI-CHANGED\n' >"$E2E/repo/php.ini"; printf 'REPO-INI-CHANGED\n' >"$E2E/repo/.user.ini"

H1=$(prod_hash); P1=$(protected_hash)
run_deploy plan >"$E2E/s2plan.log" 2>&1; rc=$?
check "plan exits 0" test $rc -eq 0
check "dry run modified nothing" test "$(prod_hash)" = "$H1"
CHANGED=$(grep -Ec '^[<>]f' "$E2E/s2plan.log"); NEW=$(grep -Ec '^[<>]f\+{9} ' "$E2E/s2plan.log")
check "plan lists exactly 4 files (3 changed + 1 new), got $CHANGED (new=$NEW)" test "$CHANGED" -eq 4 -a "$NEW" -eq 1
check "the script's own summary says '1 new, 3 changed'" grep -q 'Files would be uploaded: 1 new, 3 changed' "$E2E/s2plan.log"
grep -E '^[<>]f' "$E2E/s2plan.log" | sed 's/^/    /'

run_deploy apply DRY_RUN=false >"$E2E/s2apply.log" 2>&1; rc=$?
check "apply exits 0" test $rc -eq 0
[ $rc -ne 0 ] && tail -20 "$E2E/s2apply.log"
check "protected items untouched (.env .htaccess vendor public writable .user.ini php.ini ...)" test "$(protected_hash)" = "$P1"
check ".env still has production content"      grep -qx PROD-ENV-SENTINEL "$PROD/.env"
check ".htaccess still has production content" grep -qx PROD-HTACCESS-SENTINEL "$PROD/.htaccess"
check "public/new.css was not created"         test ! -e "$PROD/public/new.css"
check "production-only file preserved (no --delete)" grep -qx PROD-ONLY "$PROD/app/Controllers/Prod_Only.php"
check "Dashboard.php updated"  grep -q 'deploy-test change' "$PROD/app/Controllers/Admin/Dashboard.php"
check "Routes.php updated"     grep -q 'deploy-test change' "$PROD/app/Config/Routes.php"
check "index.php updated"      grep -q 'deploy-test change' "$PROD/index.php"
check "new controller created" test -f "$PROD/app/Controllers/Admin/ZzDeployTest.php"
check "new file is 644"        test "$(stat -c %a "$PROD/app/Controllers/Admin/ZzDeployTest.php")" = 644
check "no .git / .github on production" bash -c "test ! -e '$PROD/.git' -a ! -e '$PROD/.github'"
check "no stray .~tmp~ staging dirs left behind" bash -c "test -z \"\$(find '$PROD' -name '.~tmp~' -print -quit)\""
BK=$(find "$BACKUPS" -mindepth 1 -maxdepth 1 -type d 2>/dev/null | sort | head -n 1)
if [ -n "$BK" ]; then BK="$BK/"; fi
check "backup dir exists outside the web root ($BACKUPS/<stamp>-0123456)" test -n "$BK"
check "backup holds the OLD Dashboard.php" bash -c "test -f '${BK}app/Controllers/Admin/Dashboard.php' && ! grep -q 'deploy-test change' '${BK}app/Controllers/Admin/Dashboard.php'"
check "backup holds ONLY the 3 overwritten files (+ DEPLOY-LOG.txt)" test "$(find "$BK" -type f ! -name DEPLOY-LOG.txt | wc -l)" -eq 3
check "DEPLOY-LOG.txt records the commit"            grep -q '^commit:  0123456789abcdef' "${BK}DEPLOY-LOG.txt"
check "DEPLOY-LOG.txt lists the new file as '+++++++++'" grep -q '^<f+++++++++ app/Controllers/Admin/ZzDeployTest.php' "${BK}DEPLOY-LOG.txt"
check "DEPLOY-LOG.txt lists an overwritten file"     grep -q 'app/Config/Routes.php' "${BK}DEPLOY-LOG.txt"
check "DEPLOY-LOG.txt contains no protected path"    bash -c "! grep -Eq '(^|/)(\.env|\.htaccess|vendor/|writable/)' '${BK}DEPLOY-LOG.txt'"
check "backup has no protected files" bash -c "test -z \"\$(find '$BK' \\( -name '.env*' -o -name '.htaccess' -o -path '*writable*' -o -path '*vendor/autoload.php' \\) -print -quit)\""
check "apply's own summary says '1 new, 3 changed'" grep -q 'Files uploaded: 1 new, 3 changed' "$E2E/s2apply.log"
grep -E '^==>' "$E2E/s2apply.log" | sed 's/^/    /'

echo; echo "################ S2b: idempotency after apply ################"
run_deploy plan >"$E2E/s2b.log" 2>&1
check "second plan lists nothing" bash -c "! grep -Eq '^([<>]f|cd|\*deleting)' '$E2E/s2b.log'"

echo; echo "################ S3: wrong host key (MITM) is refused before any transfer ################"
H3=$(prod_hash)
run_deploy plan SSH_KNOWN_HOSTS_FILE="$E2E/client/known_hosts_wrong" >"$E2E/s3.log" 2>&1; rc=$?
check "plan fails" test $rc -ne 0
check "reason is reported (SSH connection failed)" grep -q 'SSH connection to production failed' "$E2E/s3.log"
check "production untouched" test "$(prod_hash)" = "$H3"

echo; echo "################ S4: target directory guards ################"
mkdir -p /home/deployer/other_site && echo '<html>' >/home/deployer/other_site/index.html && chown -R deployer: /home/deployer/other_site
run_deploy plan DEPLOY_PATH=/home/deployer/other_site >"$E2E/s4a.log" 2>&1; rc=$?
check "another site's directory (no app/Config/Paths.php) is refused" test $rc -ne 0
check "  ...with the 'does not look like the ERP web root' message" grep -q 'does not look like the ERP web root' "$E2E/s4a.log"
check "  ...and nothing was written there" test "$(ls /home/deployer/other_site)" = "index.html"
run_deploy plan DEPLOY_PATH=/home/deployer/does_not_exist >"$E2E/s4b.log" 2>&1; rc=$?
check "non-existent DEPLOY_PATH is refused" test $rc -ne 0
check "  ...and is NOT created" test ! -e /home/deployer/does_not_exist
run_deploy plan DEPLOY_PATH=/home/deployer >"$E2E/s4c.log" 2>&1; rc=$?
check "the home directory itself is refused" test $rc -ne 0

echo; echo "################ S5: input validation / injection ################"
# shellcheck disable=SC2088  # the literal ~ is the point of these inputs
for bad_path in '/home/deployer/public_html; touch /tmp/pwned' '/home/deployer/public_html/../public_html' '/home/deployer/pub lic' '~' '.' './public_html' '../public_html' '~/../deployer/public_html' 'public_html/../x' 'public_html; touch /tmp/pwned' 'pub lic' '/' '/onlyone'; do
  run_deploy plan "DEPLOY_PATH=$bad_path" >"$E2E/s5.log" 2>&1; rc=$?
  check "DEPLOY_PATH '$bad_path' rejected" test $rc -ne 0
done
check "injection payload did not run" test ! -e /tmp/pwned
# shellcheck disable=SC2016  # ${IFS} must reach deploy.sh literally
run_deploy plan 'SSH_HOST=127.0.0.1 -oProxyCommand=touch${IFS}/tmp/pwned2' >"$E2E/s5b.log" 2>&1; rc=$?
check "SSH_HOST with option injection rejected" test $rc -ne 0
check "  ...payload did not run" test ! -e /tmp/pwned2
run_deploy plan SSH_PORT=99999 >"$E2E/s5c.log" 2>&1; check "SSH_PORT out of range rejected" test $? -ne 0
run_deploy plan SSH_PORT=abc   >"$E2E/s5d.log" 2>&1; check "SSH_PORT non-numeric rejected" test $? -ne 0

echo; echo "################ S5r: DEPLOY_PATH relative to the SSH user's home ################"
ln -s /home/deployer/public_html /home/deployer/www; ln -s /home/deployer /home/deployer/self; chown -h deployer: /home/deployer/www /home/deployer/self
H5=$(prod_hash)
# shellcheck disable=SC2088  # the literal ~ is the point of these inputs
for good_path in 'public_html' '~/public_html' 'public_html/' 'www'; do
  run_deploy plan "DEPLOY_PATH=$good_path" >"$E2E/s5r.log" 2>&1; rc=$?
  check "DEPLOY_PATH '$good_path' resolves on the server and plans OK" test $rc -eq 0
  check "  ...with nothing to upload (trees are identical)" bash -c "! grep -Eq '^([<>]f|cd|\*deleting)' '$E2E/s5r.log'"
done
run_deploy plan DEPLOY_PATH=does_not_exist >"$E2E/s5r2.log" 2>&1; rc=$?
check "a relative folder that does not exist is refused" test $rc -ne 0
check "  ...with the 'relative to the SSH user's home' message" grep -q "does not exist relative to the SSH user's home" "$E2E/s5r2.log"
check "  ...and is NOT created" test ! -e /home/deployer/does_not_exist
run_deploy plan DEPLOY_PATH=other_site >"$E2E/s5r3.log" 2>&1; rc=$?
check "a relative folder that is another site is refused" test $rc -ne 0
check "  ...with the 'does not look like the ERP web root' message" grep -q 'does not look like the ERP web root' "$E2E/s5r3.log"
run_deploy plan DEPLOY_PATH=self >"$E2E/s5r4.log" 2>&1; rc=$?
check "a relative path that resolves to the home directory itself is refused" test $rc -ne 0
check "production untouched by all relative-path plans" test "$(prod_hash)" = "$H5"

echo; echo "################ S6: apply safety gates ################"
printf '\n// second change\n' >>"$E2E/repo/app/Config/Routes.php"
H6=$(prod_hash)
run_deploy apply >"$E2E/s6a.log" 2>&1;               check "apply without DRY_RUN refused" test $? -ne 0
run_deploy apply DRY_RUN=true >"$E2E/s6b.log" 2>&1;  check "apply with DRY_RUN=true refused" test $? -ne 0
run_deploy apply DRY_RUN= >"$E2E/s6c.log" 2>&1;      check "apply with empty DRY_RUN refused (null input is not 'false')" test $? -ne 0
run_deploy apply DRY_RUN=false GITHUB_ACTIONS=true GITHUB_REF=refs/heads/feature/x >"$E2E/s6d.log" 2>&1
check "apply from a non-main ref refused" test $? -ne 0
check "  ...with the branch message" grep -q "only allowed from 'main'" "$E2E/s6d.log"
check "production untouched by all refused applies" test "$(prod_hash)" = "$H6"
run_deploy apply DRY_RUN=false GITHUB_ACTIONS=true GITHUB_REF=refs/heads/main >"$E2E/s6e.log" 2>&1
check "apply from refs/heads/main is allowed" test $? -eq 0
check "  ...and deployed the change" grep -q 'second change' "$PROD/app/Config/Routes.php"

echo; echo "################ S6b: a real apply with DEPLOY_PATH=~/public_html ################"
printf '\n// third change\n' >>"$E2E/repo/app/Config/Routes.php"
P6=$(protected_hash)
run_deploy apply DRY_RUN=false GITHUB_ACTIONS=true GITHUB_REF=refs/heads/main 'DEPLOY_PATH=~/public_html' >"$E2E/s6f.log" 2>&1
check "apply with a home-relative DEPLOY_PATH succeeds" test $? -eq 0
check "  ...and deployed the change to the resolved directory" grep -q 'third change' "$PROD/app/Config/Routes.php"
check "  ...protected items still untouched" test "$(protected_hash)" = "$P6"
check "  ...backup taken outside the web root" test -n "$(find "$BACKUPS" -mindepth 1 -maxdepth 1 -type d 2>/dev/null | tail -n 1)"

echo; echo "################ S9: workflow dry run without a pinned host key (ssh-keyscan), end to end ################"
python3 - "$REPO/.github/workflows/deploy-production.yml" "$E2E/wf_install.sh" <<'PY'
import sys, yaml
steps = yaml.safe_load(open(sys.argv[1]))['jobs']['deploy']['steps']
run = next(s['run'] for s in steps if s['name'].startswith('Install the SSH key'))
open(sys.argv[2], 'w').write(run)
PY
rm -rf "$E2E/wfrun"; mkdir -p "$E2E/wfrun"
env -i PATH="$PATH" HOME=/root RUNNER_TEMP="$E2E/wfrun" DRY_RUN=true SSH_HOST=127.0.0.1 SSH_PORT=2222 SSH_KNOWN_HOSTS= \
    SSH_PRIVATE_KEY="$(cat "$E2E/client/id_deploy")" bash --noprofile --norc -eo pipefail "$E2E/wf_install.sh" >"$E2E/s9a.log" 2>&1
check "install step (dry run, no pinned key) fetches the host key" test $? -eq 0
check "  ...prints fingerprints" grep -q 'SHA256:' "$E2E/s9a.log"
check "  ...and leaves the key files mode 600" test "$(stat -c %a "$E2E/wfrun/deploy-ssh/id_deploy")-$(stat -c %a "$E2E/wfrun/deploy-ssh/known_hosts")" = "600-600"
run_deploy plan "SSH_KEY_FILE=$E2E/wfrun/deploy-ssh/id_deploy" "SSH_KNOWN_HOSTS_FILE=$E2E/wfrun/deploy-ssh/known_hosts" >"$E2E/s9b.log" 2>&1
check "deploy.sh plan works with the key files the fallback produced (real ssh, strict host key checking)" test $? -eq 0
env -i PATH="$PATH" HOME=/root RUNNER_TEMP="$E2E/wfrun" DRY_RUN=false SSH_HOST=127.0.0.1 SSH_PORT=2222 SSH_KNOWN_HOSTS= \
    SSH_PRIVATE_KEY="$(cat "$E2E/client/id_deploy")" bash --noprofile --norc -eo pipefail "$E2E/wf_install.sh" >"$E2E/s9c.log" 2>&1
check "install step refuses the fallback for a real deployment" test $? -ne 0

echo; echo "################ S7: no key material in any log ################"
if grep -l -F -- "$KEYBODY" "$E2E"/*.log >/dev/null 2>&1; then bad "private key material found in a log"; else ok "private key material appears in no log"; fi
check "no 'PRIVATE KEY' text in any log" bash -c "! grep -l 'PRIVATE KEY' '$E2E'/*.log"

echo; echo "################ S8: work dirs cleaned up ################"
check "no leftover deploy-work dirs (contain ssh_config)" bash -c "test -z \"\$(ls -d '$E2E'/deploy-work.* 2>/dev/null)\""

echo; echo "================ RESULT: $PASS passed, $FAILN failed ================"
exit "$FAILN"
