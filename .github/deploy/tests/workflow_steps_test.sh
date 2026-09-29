#!/usr/bin/env bash
# Local test of the secret handling in .github/workflows/deploy-production.yml.
# GitHub cannot be run from here, so the workflow file itself is checked and the bash of its
# "Validate ..." and "Install the SSH key ..." steps is extracted and executed the way the runner
# does (bash --noprofile --norc -eo pipefail) with made-up keys and a stand-in for ssh-keyscan.
# Local only: no network, no real secrets, nothing is sent anywhere.
set -uo pipefail
HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO="$(cd "$HERE/../../.." && pwd)"
WF="$REPO/.github/workflows/deploy-production.yml"
T="$(mktemp -d "${TMPDIR:-/tmp}/wf-steps.XXXXXX")"
trap 'rm -rf "$T"' EXIT
PASS=0; FAILN=0
ok()    { echo "  PASS: $*"; PASS=$((PASS+1)); }
bad()   { echo "  FAIL: $*"; FAILN=$((FAILN+1)); }
check() { local desc="$1"; shift; if "$@" >/dev/null 2>&1; then ok "$desc"; else bad "$desc"; fi; }

echo "################ static: triggers, which secret feeds which variable, who can see it ################"
python3 - "$WF" "$T" <<'PY' >"$T/static.out"; static_rc=$?
import sys, os, re, yaml
wf, out = sys.argv[1:3]
d = yaml.safe_load(open(wf))
on = d.get('on', d.get(True))
steps = d['jobs']['deploy']['steps']
fails = 0
def result(cond, msg):
    global fails
    print(('PASS: ' if cond else 'FAIL: ') + msg)
    if not cond: fails += 1
def step(prefix):
    for s in steps:
        if s['name'].startswith(prefix): return s
    raise SystemExit('step not found: ' + prefix)

result(list(on.keys()) == ['workflow_dispatch'], "the only trigger is workflow_dispatch (nothing deploys by itself) -> %s" % list(on.keys()))
result('environment' in d['jobs']['deploy'] and d['jobs']['deploy']['environment'] == 'production', "the job runs in the 'production' environment")
result(not any('secrets.' in str(v) for v in d['jobs']['deploy'].get('env', {}).values()), "no secret in the job-level env (steps get only what they need)")

expect = {
  'Validate that all required secrets': {'SSH_HOST': ['secrets.PROD_SSH_HOST', 'vars.PROD_SSH_HOST', 'secrets.SSH_HOST'],
                                          'SSH_USER': ['secrets.PROD_SSH_USER', 'secrets.SSH_USER'],
                                          'SSH_PRIVATE_KEY': ['secrets.PROD_SSH_PRIVATE_KEY', 'secrets.SSH_PRIVATE_KEY'],
                                          'SSH_KNOWN_HOSTS': ['secrets.PROD_SSH_KNOWN_HOSTS', 'secrets.SSH_KNOWN_HOSTS'],
                                          'DEPLOY_PATH': ['secrets.PROD_SSH_REMOTE_ROOT', 'secrets.DEPLOY_PATH']},
  'Install the SSH key':               {'SSH_PRIVATE_KEY': ['secrets.PROD_SSH_PRIVATE_KEY', 'secrets.SSH_PRIVATE_KEY'],
                                          'SSH_KNOWN_HOSTS': ['secrets.PROD_SSH_KNOWN_HOSTS', 'secrets.SSH_KNOWN_HOSTS'],
                                          'SSH_HOST': ['secrets.PROD_SSH_HOST', 'vars.PROD_SSH_HOST', 'secrets.SSH_HOST'],
                                          'SSH_PORT': ['secrets.PROD_SSH_PORT', 'secrets.SSH_PORT']},
  'Plan - rsync dry run':              {'SSH_HOST': ['secrets.PROD_SSH_HOST', 'vars.PROD_SSH_HOST', 'secrets.SSH_HOST'],
                                          'SSH_PORT': ['secrets.PROD_SSH_PORT', 'secrets.SSH_PORT'],
                                          'SSH_USER': ['secrets.PROD_SSH_USER', 'secrets.SSH_USER'],
                                          'DEPLOY_PATH': ['secrets.PROD_SSH_REMOTE_ROOT', 'secrets.DEPLOY_PATH']},
  'Apply - upload the changes':        {'SSH_HOST': ['secrets.PROD_SSH_HOST', 'vars.PROD_SSH_HOST', 'secrets.SSH_HOST'],
                                          'SSH_PORT': ['secrets.PROD_SSH_PORT', 'secrets.SSH_PORT'],
                                          'SSH_USER': ['secrets.PROD_SSH_USER', 'secrets.SSH_USER'],
                                          'DEPLOY_PATH': ['secrets.PROD_SSH_REMOTE_ROOT', 'secrets.DEPLOY_PATH']},
}
for prefix, mapping in expect.items():
    env = step(prefix).get('env', {})
    for var, needles in mapping.items():
        expr = str(env.get(var, ''))
        result(all(n in expr for n in needles), "%s: %s reads %s" % (prefix.split(' - ')[0][:34], var, ' || '.join(needles)))

with_secrets = [s['name'] for s in steps if any(re.search(r'\b(secrets|vars)\.', str(v)) for v in s.get('env', {}).values())]
allowed = {s['name'] for p in expect for s in steps if s['name'].startswith(p)}
result(set(with_secrets) == allowed, "only the four steps that need them see secrets -> %d steps" % len(with_secrets))
key_steps = [s['name'] for s in steps if 'SSH_PRIVATE_KEY' in s.get('env', {})]
result(len(key_steps) == 2 and all(n.startswith(('Validate', 'Install')) for n in key_steps), "the private key is in the environment of exactly two steps (validate, install)")
runs = {s['name']: s.get('run', '') for s in steps}
result(not any(re.search(r'set\s+-[a-zA-Z]*x|xtrace|printenv|\benv\b\s*$|declare -p', r, re.M) for r in runs.values()), "no shell tracing / environment dumps in any run script")
result(step('Apply - upload')['if'].replace(' ', '') == '${{inputs.dry_run==false}}', "the upload step only runs when dry_run is unticked")
result('refs/heads/$DEPLOY_BRANCH' in runs[step('Refuse real deployments')['name']], "real deployments are refused from any branch but main")
result(d.get('permissions') == {'contents': 'read'}, "the workflow token can only read the repository (permissions: contents: read)")
result(step('Check out').get('with', {}).get('persist-credentials') is False, "checkout does not leave the GitHub token in .git/config (persist-credentials: false)")
result(d.get('concurrency', {}).get('group') and d['concurrency'].get('cancel-in-progress') is False, "two deployments never run at once and a running one is never cancelled half way")
result(isinstance(d['jobs']['deploy'].get('timeout-minutes'), int), "the job has a timeout")
clean = step('Remove SSH material')
result(str(clean.get('if', '')).replace(' ', '') == '${{always()}}' and 'rm -rf' in clean['run'] and 'deploy-ssh' in clean['run'], "the key directory is removed from the runner even when the job fails")

for prefix, fname in (('Refuse real deployments', 'guard.sh'), ('Validate that all required secrets', 'validate.sh'), ('Install the SSH key', 'install.sh')):
    open(os.path.join(out, fname), 'w').write(step(prefix)['run'])
sys.exit(1 if fails else 0)
PY
cat "$T/static.out" | sed 's/^/  /'
PASS=$((PASS + $(grep -c '^PASS' "$T/static.out"))); FAILN=$((FAILN + $(grep -c '^FAIL' "$T/static.out")))
[ $static_rc -eq 0 ] || [ "$FAILN" -gt 0 ] || bad "static checks failed to run"

# ------------------------------------------------------------------------------------------------
# dynamic: run the extracted steps
# ------------------------------------------------------------------------------------------------
mkdir -p "$T/bin" "$T/home"
ssh-keygen -q -t ed25519 -N '' -C t -f "$T/id_ok"
ssh-keygen -q -t ed25519 -N 'a-passphrase' -C t -f "$T/id_pass"
ssh-keygen -q -t ed25519 -N '' -C h -f "$T/hostkey"
HK="$(cut -d' ' -f1,2 "$T/hostkey.pub")"
KEYBODY="$(sed -n '2p' "$T/id_ok")"
HOST=deploy.example.test
cat >"$T/bin/ssh-keyscan" <<'EOS'
#!/usr/bin/env bash
# stand-in for the real ssh-keyscan: prints one ed25519 host key line, or fails on request
[ -n "${FAKE_KEYSCAN_FAIL:-}" ] && exit 1
port=22; host=""
while [ $# -gt 0 ]; do
  case "$1" in -p) port="$2"; shift 2 ;; -T|-t) shift 2 ;; *) host="$1"; shift ;; esac
done
if [ "$port" = 22 ]; then echo "$host $FAKE_HOSTKEY"; else echo "[$host]:$port $FAKE_HOSTKEY"; fi
EOS
chmod +x "$T/bin/ssh-keyscan"
: >"$T/all.log"

# run_step <script> VAR=value ...   (same shell flags GitHub uses for `shell: bash`)
run_step() {
  local f="$1"; shift
  rm -rf "$T/runner"; mkdir -p "$T/runner"
  env -i PATH="$T/bin:$PATH" HOME="$T/home" RUNNER_TEMP="$T/runner" FAKE_HOSTKEY="$HK" "$@" \
    bash --noprofile --norc -eo pipefail "$f" >"$T/out" 2>&1
  local rc=$?
  cat "$T/out" >>"$T/all.log"
  return $rc
}
KEY="$(cat "$T/id_ok")"
KH="$HOST $HK"

echo; echo "################ branch guard step ################"
run_step "$T/guard.sh" DRY_RUN=false DEPLOY_BRANCH=main GITHUB_REF=refs/heads/main
check "real deployment from main: allowed" test $? -eq 0
run_step "$T/guard.sh" DRY_RUN=false DEPLOY_BRANCH=main GITHUB_REF=refs/heads/claude/trusting-euler-2j9147
check "real deployment from a feature branch: refused" test $? -ne 0
check "  ...with the 'only allowed from' message" grep -q 'Real deployments are only allowed from' "$T/out"
run_step "$T/guard.sh" DRY_RUN=false DEPLOY_BRANCH=main GITHUB_REF=refs/heads/main-old
check "real deployment from a branch whose name merely starts with main: refused" test $? -ne 0
run_step "$T/guard.sh" DRY_RUN=false DEPLOY_BRANCH=main GITHUB_REF=refs/tags/v1
check "real deployment from a tag: refused" test $? -ne 0
run_step "$T/guard.sh" DRY_RUN=true DEPLOY_BRANCH=main GITHUB_REF=refs/heads/claude/trusting-euler-2j9147
check "dry run from a feature branch: allowed (nothing is transferred)" test $? -eq 0

echo; echo "################ validate step ################"
run_step "$T/validate.sh" DRY_RUN=false SSH_HOST=$HOST SSH_USER=u SSH_PRIVATE_KEY="$KEY" SSH_KNOWN_HOSTS="$KH" DEPLOY_PATH=/home/u/public_html
check "everything set, real deployment: passes" test $? -eq 0
run_step "$T/validate.sh" DRY_RUN=true SSH_HOST= SSH_USER=u SSH_PRIVATE_KEY="$KEY" SSH_KNOWN_HOSTS="$KH" DEPLOY_PATH=/home/u/public_html
rc=$?; check "missing host: fails" test $rc -ne 0
check "  ...and names PROD_SSH_HOST" grep -q 'PROD_SSH_HOST' "$T/out"
run_step "$T/validate.sh" DRY_RUN=true SSH_HOST= SSH_USER= SSH_PRIVATE_KEY= SSH_KNOWN_HOSTS="$KH" DEPLOY_PATH=
check "nothing set: fails" test $? -ne 0
check "  ...naming all four required secrets" bash -c "grep -q PROD_SSH_HOST '$T/out' && grep -q PROD_SSH_USER '$T/out' && grep -q PROD_SSH_PRIVATE_KEY '$T/out' && grep -q PROD_SSH_REMOTE_ROOT '$T/out'"
run_step "$T/validate.sh" DRY_RUN=true SSH_HOST=$HOST SSH_USER=u SSH_PRIVATE_KEY="$KEY" SSH_KNOWN_HOSTS= DEPLOY_PATH=/home/u/public_html
check "no pinned host key, dry run: passes with a warning" bash -c "test $? -eq 0"
check "  ...the warning is issued" grep -q 'PROD_SSH_KNOWN_HOSTS is not set' "$T/out"
run_step "$T/validate.sh" DRY_RUN=false SSH_HOST=$HOST SSH_USER=u SSH_PRIVATE_KEY="$KEY" SSH_KNOWN_HOSTS= DEPLOY_PATH=/home/u/public_html
check "no pinned host key, real deployment: refused" test $? -ne 0
check "  ...and names PROD_SSH_KNOWN_HOSTS" grep -q 'PROD_SSH_KNOWN_HOSTS' "$T/out"
run_step "$T/validate.sh" DRY_RUN=true SSH_HOST=$HOST SSH_USER=u SSH_PRIVATE_KEY="TOP-SECRET-MARKER-123" SSH_KNOWN_HOSTS= DEPLOY_PATH=/home/u/secret-path-marker
check "secret values are never echoed by the validation step" bash -c "! grep -q -e TOP-SECRET-MARKER-123 -e secret-path-marker '$T/out'"

echo; echo "################ install step ################"
run_step "$T/install.sh" DRY_RUN=false SSH_HOST=$HOST SSH_PORT= SSH_KNOWN_HOSTS="$KH" SSH_PRIVATE_KEY="$KEY"
check "pinned key, port 22: installs" test $? -eq 0
check "  ...key file and known_hosts are mode 600, directory 700" test "$(stat -c %a "$T/runner/deploy-ssh/id_deploy")-$(stat -c %a "$T/runner/deploy-ssh/known_hosts")-$(stat -c %a "$T/runner/deploy-ssh")" = "600-600-700"
run_step "$T/install.sh" DRY_RUN=false SSH_HOST=$HOST SSH_PORT=2222 SSH_KNOWN_HOSTS="[$HOST]:2222 $HK" SSH_PRIVATE_KEY="$KEY"
check "pinned key with a custom port ([host]:port entry): installs" test $? -eq 0
run_step "$T/install.sh" DRY_RUN=false SSH_HOST=$HOST SSH_PORT=2222 SSH_KNOWN_HOSTS="$HOST $HK" SSH_PRIVATE_KEY="$KEY"
check "pinned entry without the port for a custom port: refused" test $? -ne 0
run_step "$T/install.sh" DRY_RUN=false SSH_HOST=$HOST SSH_PORT= SSH_KNOWN_HOSTS="other.example.test $HK" SSH_PRIVATE_KEY="$KEY"
check "pinned key for a different host: refused" test $? -ne 0
check "  ...with the 'no host key for the configured' message" grep -q 'PROD_SSH_KNOWN_HOSTS has no host key for the configured' "$T/out"
run_step "$T/install.sh" DRY_RUN=false SSH_HOST=$HOST SSH_PORT= SSH_KNOWN_HOSTS="$(printf '%s\r' "$KH")" SSH_PRIVATE_KEY="$(sed 's/$/\r/' "$T/id_ok")"
check "CRLF (pasted on Windows) in both key and host key: installs" test $? -eq 0
run_step "$T/install.sh" DRY_RUN=true SSH_HOST=$HOST SSH_PORT= SSH_KNOWN_HOSTS= SSH_PRIVATE_KEY="$KEY"
check "no pinned key, dry run: fetches the host key with ssh-keyscan" test $? -eq 0
check "  ...prints a SHA256 fingerprint" grep -q 'SHA256:' "$T/out"
check "  ...but not the host name" bash -c "! grep -q '$HOST' '$T/out'"
check "  ...and writes a usable known_hosts (entry found for the host)" bash -c "ssh-keygen -F $HOST -f '$T/runner/deploy-ssh/known_hosts'"
run_step "$T/install.sh" DRY_RUN=true SSH_HOST=$HOST SSH_PORT=2222 SSH_KNOWN_HOSTS= SSH_PRIVATE_KEY="$KEY"
check "no pinned key, dry run, custom port: entry is [host]:port" bash -c "test $? -eq 0 && ssh-keygen -F '[$HOST]:2222' -f '$T/runner/deploy-ssh/known_hosts'"
run_step "$T/install.sh" DRY_RUN=false SSH_HOST=$HOST SSH_PORT= SSH_KNOWN_HOSTS= SSH_PRIVATE_KEY="$KEY"
check "no pinned key, real deployment: refused (defence in depth)" test $? -ne 0
run_step "$T/install.sh" DRY_RUN=true SSH_HOST=$HOST SSH_PORT= SSH_KNOWN_HOSTS= SSH_PRIVATE_KEY="$KEY" FAKE_KEYSCAN_FAIL=1
check "ssh-keyscan failure: reported, not ignored" test $? -ne 0
check "  ...with the 'Could not fetch' message" grep -q 'Could not fetch the server' "$T/out"
run_step "$T/install.sh" DRY_RUN=false SSH_HOST=$HOST SSH_PORT= SSH_KNOWN_HOSTS="$KH" SSH_PRIVATE_KEY="not a key at all"
check "garbage instead of a private key: refused" test $? -ne 0
check "  ...naming PROD_SSH_PRIVATE_KEY" grep -q 'PROD_SSH_PRIVATE_KEY is not a valid' "$T/out"
run_step "$T/install.sh" DRY_RUN=false SSH_HOST=$HOST SSH_PORT= SSH_KNOWN_HOSTS="$KH" SSH_PRIVATE_KEY="$(cat "$T/id_pass")"
check "passphrase-protected private key: refused" test $? -ne 0

echo; echo "################ nothing secret in any output ################"
check "no private key material in any step output" bash -c "! grep -qF -- '$KEYBODY' '$T/all.log' && ! grep -q 'PRIVATE KEY' '$T/all.log'"

echo; echo "================ RESULT: $PASS passed, $FAILN failed ================"
exit "$FAILN"
