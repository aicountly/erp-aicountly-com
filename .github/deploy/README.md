# Production deployment (cPanel, SSH + rsync)

Workflow: [`.github/workflows/deploy-production.yml`](../workflows/deploy-production.yml)
Logic: [`deploy.sh`](deploy.sh) · Exclusions: [`rsync-excludes.txt`](rsync-excludes.txt) · Local tests: [`tests/`](tests)

## What is guaranteed

| Rule | How it is enforced |
| --- | --- |
| `.env`, `.htaccess`, `vendor`, `public`, `writable` are **never** copied, overwritten or deleted | `rsync-excludes.txt`, plus an independent checker that aborts the run if any of them appears in the transfer list |
| The folders of the other projects in the web root (`aisonode`, `app_old`, `erp3-accounts-xml-export`, `erp3-voucher-verification`) and the unused `app/Views/grpcomp` are **never** copied, overwritten or deleted either | same two layers; root-anchored exact paths |
| Files that exist only on production are preserved | rsync is never run with `--delete` (and `--max-delete=0` turns an accidental `--delete` into a hard failure) |
| Existing files keep their permissions/owner/timestamps | `--no-perms --no-owner --no-group --no-times`; new files get `644`, new dirs `755` |
| Nothing is lost when a file is overwritten | overwritten files are moved to `~/erp-deploy-backups/<UTC-stamp>-<sha>/` on the server (outside the web root) together with a `DEPLOY-LOG.txt` |
| A merge can never deploy by itself | manual trigger only (`workflow_dispatch`, no push/schedule trigger); `dry_run` is ticked by default; real deploys only from `main`; one run at a time |
| No secrets in the repo or in logs | SSH key, host, user, port and path come from GitHub secrets, are only written to `$RUNNER_TEMP` (mode 600) and deleted at the end; each secret is visible only to the steps that need it (the private key to two); no third-party actions; no shell tracing; the workflow token can only read the repository |
| Wrong server / wrong directory is refused | a real deploy needs the server's host key pinned (`PROD_SSH_KNOWN_HOSTS`); the target must already exist and contain `index.php` and `app/Config/Paths.php` |

Additional protective excludes (beyond the five first requested): `/.user.ini` and `/php.ini` (cPanel-managed PHP settings), `.git` and `/.github/` (never publish repository internals in a web-served directory). Remove a line from `rsync-excludes.txt` and from `required_rules` in `deploy.sh` if you ever want to deploy them.

Later additions, requested by the owner:

* `/aisonode/`, `/app_old/`, `/erp3-accounts-xml-export/`, `/erp3-voucher-verification/` — other projects that live in the same web root. None of them is in this repository, so a deployment could never have uploaded them and never deletes them; the rules keep it that way even if a folder of that name is ever added to the repository, or `--delete` were ever added. Root only: a folder of the same name deeper in the tree (say `app/Libraries/app_old/`) is ordinary ERP code and still deploys.
* `/app/Views/grpcomp/` — the group-company views, which are not in use. **Exactly that path.** The group-company controllers (`app/Controllers/Grpcomp`) and models (`app/Models/Grpcomp`), `app/Views/includes/grpcomp`, and any other folder that merely has the same name still deploy. Nothing in `app/Views/grpcomp` is uploaded, created or removed; whatever is there on the server stays as it is.

Consequences worth knowing:

* `.htaccess` is excluded at **every** depth, so `system/.htaccess` and `app/ThirdParty/tcpdf/tools/.htaccess` are not deployed either (they already exist on the server).
* `vendor` is excluded only at the repository root. `app/Libraries/vendor/` is real application code and **is** deployed.
* Nothing under `writable/` or `public/` is ever created or changed by a deployment. If new code needs a new folder there, create it on the server by hand.
* `vendor` is not deployed: a code change that needs a new/updated Composer package has to be installed on the server separately.

## One-time setup

1. **Create a dedicated deploy key** (on your own machine, no passphrase):
   ```bash
   ssh-keygen -t ed25519 -N '' -C 'github-actions-deploy' -f ./erp_deploy_key
   ```
2. **Authorize the public key on cPanel**: *cPanel → Security → SSH Access → Manage SSH Keys → Import Key*, paste `erp_deploy_key.pub`, then *Manage → Authorize*.
3. **Create the secrets** (*Settings → Secrets and variables → Actions*; repository secrets, or the same names as secrets of an environment called `production`, which the job uses):

   | Secret | Required | Value |
   | --- | --- | --- |
   | `PROD_SSH_HOST` | yes | server hostname or IP. Keep it a **secret**: run logs of a public repository are public. (A repository *variable* of the same name is also read, but it is not masked.) |
   | `PROD_SSH_PORT` | no | SSH port, default `22` (many cPanel hosts use a custom one) |
   | `PROD_SSH_USER` | yes | the cPanel account user name |
   | `PROD_SSH_PRIVATE_KEY` | yes | complete contents of `erp_deploy_key`, including the `BEGIN`/`END` lines |
   | `PROD_SSH_REMOTE_ROOT` | yes | the ERP web root: absolute (`/home/<cpanel-user>/public_html`) or relative to the SSH user's home (`public_html`, `~/public_html`). No spaces, no `..`; it must already exist and is never created |
   | `PROD_SSH_KNOWN_HOSTS` | before the first **real** deploy | the pinned host key, see step 4. Optional for a dry run |

   The earlier names `SSH_HOST`, `SSH_PORT`, `SSH_USER`, `SSH_PRIVATE_KEY`, `SSH_KNOWN_HOSTS` and `DEPLOY_PATH` are still accepted when the `PROD_*` one is not set. Then delete `erp_deploy_key` and `erp_deploy_key.pub` from your machine.
4. **Pin the server's host key.** The first dry run may be started without `PROD_SSH_KNOWN_HOSTS`: it fetches the server's key with `ssh-keyscan` (trust on first use, read-only, nothing is uploaded) and prints the fingerprints (the host name is left out of the log). Compare one with *cPanel → Security → SSH Access*, or on the server with `ssh-keygen -lf /etc/ssh/ssh_host_ed25519_key.pub`. Then create the secret `PROD_SSH_KNOWN_HOSTS` from the output of
   ```bash
   ssh-keyscan -p 22 -t ed25519,ecdsa,rsa YOUR.SERVER.HOSTNAME
   ```
   (replace `22` with your SSH port; the value must contain an entry for the same host and port as the secrets, for a non-standard port `[host]:port`, which `ssh-keyscan -p` writes for you). A real deployment is refused until it is set.
5. The workflow only shows up in the Actions tab once the file is on the default branch (`main`).
6. *Optional:* under *Settings → Environments → production* add **required reviewers**, so every run needs a click of approval.

## Running a deployment

**Always start with a dry run**, and read the list of files it prints.

1. *Actions → Deploy to production (cPanel) → Run workflow*, leave **dry_run ticked**.
2. In the log of the step *Plan - rsync dry run against production*, every line starting with `<f` is a file that would be sent (`<f+++++++++` = new file, anything else = an existing production file whose content differs from the repository and would be replaced). The job summary shows the counts and the first 200 files.
3. **First deploy only:** the repository is a backup of the old ERP. If production was edited after that backup, those files show up here as "changed" and a real deploy would replace them with the older repository version (a backup copy is kept, but review the list before continuing). Investigate anything you do not expect.
4. Pin the host key (setup step 4), then run the workflow again from `main` with **dry_run unticked** to upload.

## Rollback

Each real deploy writes `~/erp-deploy-backups/<stamp>/` on the server: the previous version of every file it overwrote plus `DEPLOY-LOG.txt` (commit, time, and every file sent). To undo a deploy, in cPanel Terminal or over SSH:

```bash
cd ~/erp-deploy-backups && ls -1                   # choose the deploy to undo
STAMP=20260929T101500Z-abc1234                     # <- its folder name
# 1) put back every overwritten file
rsync -rl --exclude=DEPLOY-LOG.txt ~/erp-deploy-backups/$STAMP/ ~/public_html/
# 2) remove files that deploy added
grep '^<f+++++++++' ~/erp-deploy-backups/$STAMP/DEPLOY-LOG.txt | cut -d' ' -f2- \
  | (cd ~/public_html && xargs -r -d '\n' rm -v --)
```

(Use your real web-root instead of `~/public_html`.) Backups are never pruned automatically; delete old folders when you no longer need them.

## Troubleshooting

| Message / symptom | Cause |
| --- | --- |
| `Required secret ... is not set` | create the secret named in the message (see the table above) |
| `PROD_SSH_PRIVATE_KEY is not a valid, passphrase-less private key` | the full key file was not pasted, or it has a passphrase |
| `A real deployment needs the server's host key pinned in PROD_SSH_KNOWN_HOSTS` | run a dry run, verify the printed fingerprints, then create the secret (setup step 4) |
| `PROD_SSH_KNOWN_HOSTS has no host key for the configured PROD_SSH_HOST/PROD_SSH_PORT` | regenerate it with `ssh-keyscan -p <port> ...`; host and port must match the secrets |
| `Could not fetch the server's host key with ssh-keyscan` | wrong host/port, or the server does not accept SSH from GitHub's runners |
| `SSH connection to production failed` | wrong host/port/user, key not authorized in cPanel, host key changed, or the server firewall blocks GitHub's runners (allow-list GitHub's Actions IP ranges from `https://api.github.com/meta`, or use a self-hosted runner) |
| `DEPLOY_PATH does not look like the ERP web root` | wrong `PROD_SSH_REMOTE_ROOT`; the target must contain `index.php` and `app/Config/Paths.php` |
| `rsync is not installed on the server` | ask your host to enable rsync for SSH users |
| `Real deployments are only allowed from 'main'` | start the workflow from `main`, or tick `dry_run` |
| A failed upload | `--delay-updates` stages files first, so a failure normally leaves the old files in place; stray `.~tmp~` folders are cleaned up by the next successful run. Re-run the workflow. |

## Verification performed on this setup

`deploy.sh selftest` runs on every workflow run before the network is touched: it copies a synthetic tree over a fake production tree with the real rsync binary and checks that every protected item is byte-, mode- and mtime-identical afterwards, that production-only files survive, that backups are taken, that permissions are cPanel-safe, that the transfer list checker catches protected paths (including a deliberately broken exclude file), and that a second run is a no-op.

Everything else is tested locally, on a developer machine or in a sandbox, never against GitHub or a real server (`tests/`):

| Test | What it proves |
| --- | --- |
| `tests/workflow_steps_test.sh` | the workflow starts only by hand; which secret feeds which variable and which steps can see it; minimal token permissions; then the bash of the *branch guard*, *Validate* and *Install SSH key* steps is extracted from the workflow and executed with made-up keys and a stand-in for `ssh-keyscan` (pinned/unpinned host key, custom port, CRLF pasted keys, wrong host, passphrase key, nothing secret in the output) |
| `tests/e2e_ssh_test.sh` | `deploy.sh` against a real throw-away `sshd` (needs root: it creates a temporary user `deployer` and an `sshd` on 127.0.0.1:2222 and removes them again): plan/apply, protected files untouched, production-only files kept, backups, host-key mismatch, wrong/missing/foreign target directories, absolute and home-relative `PROD_SSH_REMOTE_ROOT`, injection attempts, the apply gates, no key material in any log, the dry run without a pinned key |
| `tests/mutation_suite.sh` | breaks `deploy.sh`, the exclude list and the workflow on purpose (`tests/workflow_mutants.py`); every break must make a test fail |

```bash
bash .github/deploy/deploy.sh selftest
bash .github/deploy/tests/workflow_steps_test.sh
sudo bash .github/deploy/tests/e2e_ssh_test.sh
bash .github/deploy/tests/mutation_suite.sh
actionlint .github/workflows/deploy-production.yml
shellcheck .github/deploy/deploy.sh .github/deploy/tests/*.sh
```
