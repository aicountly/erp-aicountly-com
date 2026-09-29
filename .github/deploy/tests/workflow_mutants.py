#!/usr/bin/env python3
"""Broken copies of .github/workflows/deploy-production.yml for mutation_suite.sh.

  workflow_mutants.py list                      -> one mutant name per line
  workflow_mutants.py make NAME SRC.yml DST.yml -> writes the broken copy; exit 3 if the edit changed nothing

Every mutant weakens one safety property (a new trigger, a secret in the wrong place, a disabled guard,
a wrong secret name, a relaxed permission ...). workflow_steps_test.sh must fail for each of them.
Each entry: (name, step-name prefix or '' for the whole file, old text, new text or None to delete the step).
Only the first match inside the chosen step is replaced.
"""
import re
import sys

WHOLE = ''
KEY_TO_PLAN_ENV = "          SSH_KEY_FILE: ${{ runner.temp }}/deploy-ssh/id_deploy\n"

MUTANTS = [
    # --- what starts a deployment and where it may run -------------------------------------------
    ('push_trigger', WHOLE, "on:\n  workflow_dispatch:", "on:\n  push:\n    branches: [main]\n  workflow_dispatch:"),
    ('schedule_trigger', WHOLE, "on:\n  workflow_dispatch:", "on:\n  schedule:\n    - cron: '0 3 * * *'\n  workflow_dispatch:"),
    ('no_environment', WHOLE, "    environment: production\n", ""),
    ('permissions_write', WHOLE, "  contents: read", "  contents: write"),
    ('persist_credentials', WHOLE, "persist-credentials: false", "persist-credentials: true"),
    ('cancel_in_progress', WHOLE, "cancel-in-progress: false", "cancel-in-progress: true"),
    ('no_timeout', WHOLE, "    timeout-minutes: 30\n", ""),
    ('apply_always', 'Apply', "if: ${{ inputs.dry_run == false }}", "if: ${{ always() }}"),
    ('apply_unconditional', 'Apply', "        if: ${{ inputs.dry_run == false }}\n", ""),
    ('guard_removed', 'Refuse', None, None),
    ('guard_disabled', 'Refuse', '''if [ "$DRY_RUN" = "false" ] && [ "$GITHUB_REF" != "refs/heads/$DEPLOY_BRANCH" ]; then''', "if false; then"),
    ('guard_prefix_match', 'Refuse', '''[ "$GITHUB_REF" != "refs/heads/$DEPLOY_BRANCH" ]''', '''[[ "$GITHUB_REF" != "refs/heads/$DEPLOY_BRANCH"* ]]'''),
    ('guard_ignores_tags', 'Refuse', '''[ "$GITHUB_REF" != "refs/heads/$DEPLOY_BRANCH" ]''', '''[[ "$GITHUB_REF" == refs/heads/* && "$GITHUB_REF" != "refs/heads/$DEPLOY_BRANCH" ]]'''),
    # --- which secret reaches which step ---------------------------------------------------------
    ('job_level_secret', WHOLE, "      DRY_RUN: ${{ inputs.dry_run }}\n", "      DRY_RUN: ${{ inputs.dry_run }}\n      SSH_PRIVATE_KEY: ${{ secrets.PROD_SSH_PRIVATE_KEY }}\n"),
    ('key_leaks_to_plan', 'Plan', KEY_TO_PLAN_ENV, KEY_TO_PLAN_ENV + "          SSH_PRIVATE_KEY: ${{ secrets.PROD_SSH_PRIVATE_KEY }}\n"),
    ('secret_to_selftest', 'Self-test', "        run: bash", "        env:\n          SSH_USER: ${{ secrets.PROD_SSH_USER }}\n        run: bash"),
    ('user_secret_typo', 'Apply', "secrets.PROD_SSH_USER", "secrets.PROD_SSH_USR"),
    ('root_secret_typo', 'Validate', "secrets.PROD_SSH_REMOTE_ROOT", "secrets.PROD_SSH_REMOTE_DIR"),
    ('root_secret_typo_plan', 'Plan', "secrets.PROD_SSH_REMOTE_ROOT", "secrets.PROD_SSH_REMOTE_DIR"),
    ('port_secret_typo', 'Install', "secrets.PROD_SSH_PORT", "secrets.PROD_SSH_PRT"),
    ('key_secret_typo', 'Install', "secrets.PROD_SSH_PRIVATE_KEY", "secrets.PROD_SSH_PRIVATE_KY"),
    ('known_hosts_typo', 'Install', "secrets.PROD_SSH_KNOWN_HOSTS", "secrets.PROD_SSH_KNOWN_HOST"),
    ('host_not_mapped_plan', 'Plan', "          SSH_HOST: ${{ secrets.PROD_SSH_HOST || vars.PROD_SSH_HOST || secrets.SSH_HOST }}\n", ""),
    ('host_variable_dropped', 'Apply', " || vars.PROD_SSH_HOST", ""),
    # --- logging -----------------------------------------------------------------------------------
    ('xtrace_on', 'Install', "set +x", "set -x"),
    ('env_dump', 'Validate', "missing=0\n", "missing=0\n          printenv\n"),
    ('validate_echoes_key', 'Validate', "missing=0\n", 'missing=0\n          echo "key=$SSH_PRIVATE_KEY"\n'),
    ('validate_echoes_path', 'Validate', "missing=0\n", 'missing=0\n          echo "root=$DEPLOY_PATH"\n'),
    ('install_prints_host', 'Install', '$2 " " $NF', '$2 " " $3 " " $NF'),
    # --- validation step ----------------------------------------------------------------------------
    ('validate_removed', 'Validate', None, None),
    ('validate_no_host', 'Validate', "          need SSH_HOST PROD_SSH_HOST\n", ""),
    ('validate_no_user', 'Validate', "          need SSH_USER PROD_SSH_USER\n", ""),
    ('validate_no_key', 'Validate', "          need SSH_PRIVATE_KEY PROD_SSH_PRIVATE_KEY\n", ""),
    ('validate_no_root', 'Validate', "          need DEPLOY_PATH PROD_SSH_REMOTE_ROOT\n", ""),
    ('validate_real_unpinned', 'Validate', '''if [ "$DRY_RUN" = "false" ]; then''', '''if [ "$DRY_RUN" = "never" ]; then'''),
    ('validate_ignores_missing', 'Validate', 'exit "$missing"', "exit 0"),
    # --- install step ------------------------------------------------------------------------------
    ('install_removed', 'Install', None, None),
    ('install_real_unpinned', 'Install', '''if [ "$DRY_RUN" = "false" ]; then''', '''if [ "$DRY_RUN" = "never" ]; then'''),
    ('install_no_host_check', 'Install', '''if ! ssh-keygen -F "$lookup" -f "$ssh_dir/known_hosts" >/dev/null 2>&1; then''', "if false; then"),
    ('install_port_ignored', 'Install', '''lookup="[$SSH_HOST]:$port"''', '''lookup="$SSH_HOST"'''),
    ('install_no_key_check', 'Install', '''if ! ssh-keygen -y -P '' -f "$ssh_dir/id_deploy" >/dev/null 2>&1; then''', "if false; then"),
    ('install_key_world_readable', 'Install', 'chmod 600 "$ssh_dir/id_deploy"', 'chmod 644 "$ssh_dir/id_deploy"'),
    ('install_hosts_world_writable', 'Install', 'chmod 600 "$ssh_dir/known_hosts"', 'chmod 666 "$ssh_dir/known_hosts"'),
    ('install_dir_open', 'Install', 'chmod 700 "$ssh_dir"', 'chmod 755 "$ssh_dir"'),
    # A CRLF private key really breaks (ssh-keygen -y rejects it). The same tr on the known_hosts value is
    # deliberately NOT mutated: OpenSSH 9.x ignores a trailing CR after the host key, so that would be an
    # equivalent mutant (no behaviour change to detect); the tr stays as a harmless safeguard.
    ('install_key_crlf', 'Install', '''printf '%s\\n' "$SSH_PRIVATE_KEY" | tr -d '\\r' ''', '''printf '%s\\n' "$SSH_PRIVATE_KEY" '''),
    ('install_keyscan_unchecked', 'Install',
     '''if ! ssh-keyscan -p "$port" -T 15 -t ed25519,ecdsa,rsa "$SSH_HOST" > "$ssh_dir/known_hosts" 2>/dev/null || [ ! -s "$ssh_dir/known_hosts" ]; then''',
     '''ssh-keyscan -p "$port" -T 15 -t ed25519,ecdsa,rsa "$SSH_HOST" > "$ssh_dir/known_hosts" 2>/dev/null || true\n            if false; then'''),
    ('install_keyscan_port', 'Install', 'ssh-keyscan -p "$port"', 'ssh-keyscan -p 22'),
    ('install_leaves_key_dir', 'Remove SSH material', 'rm -rf "$RUNNER_TEMP/deploy-ssh"', 'true'),
    ('cleanup_only_on_success', 'Remove SSH material', "if: ${{ always() }}", "if: ${{ success() }}"),
]


def apply(name, src_text):
    for n, step, old, new in MUTANTS:
        if n != name:
            continue
        if step == WHOLE:
            return src_text.replace(old, new, 1)
        parts = re.split(r'(?m)^(?=      - name: )', src_text)
        out = []
        for part in parts:
            if part.startswith('      - name: ' + step):
                if old is None:
                    continue  # delete the whole step
                part = part.replace(old, new, 1)
            out.append(part)
        return ''.join(out)
    raise SystemExit('unknown mutant: ' + name)


def main(argv):
    if len(argv) == 2 and argv[1] == 'list':
        print('\n'.join(m[0] for m in MUTANTS))
        return 0
    if len(argv) == 5 and argv[1] == 'make':
        src_text = open(argv[3]).read()
        out = apply(argv[2], src_text)
        open(argv[4], 'w').write(out)
        return 0 if out != src_text else 3
    print(__doc__)
    return 2


if __name__ == '__main__':
    sys.exit(main(sys.argv))
