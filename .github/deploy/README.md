# Production deployment (cPanel, SSH + rsync)

Workflow: [`.github/workflows/deploy-production.yml`](../workflows/deploy-production.yml)
Logic: [`deploy.sh`](deploy.sh) · Exclusions: [`rsync-excludes.txt`](rsync-excludes.txt)

## What is guaranteed

| Rule | How it is enforced |
| --- | --- |
| `.env`, `.htaccess`, `vendor`, `public`, `writable` are **never** copied, overwritten or deleted | `rsync-excludes.txt`, plus an independent checker that aborts the run if any of them appears in the transfer list |
| Files that exist only on production are preserved | rsync is never run with `--delete` (and `--max-delete=0` turns an accidental `--delete` into a hard failure) |
| Existing files keep their permissions/owner/timestamps | `--no-perms --no-owner --no-group --no-times`; new files get `644`, new dirs `755` |
| Nothing is lost when a file is overwritten | overwritten files are moved to `~/erp-deploy-backups/<UTC-stamp>-<sha>/` on the server (outside the web root) together with a `DEPLOY-LOG.txt` |
| A merge can never deploy by itself | manual trigger only; `dry_run` is ticked by default; real deploys only from `main` |
| No secrets in the repo or in logs | SSH key, host, user and path come from GitHub secrets, are only written to `$RUNNER_TEMP` (mode 600) and deleted at the end; no third-party actions; no shell tracing |
| Wrong server / wrong directory is refused | the server's host key is pinned (`SSH_KNOWN_HOSTS`); the target must already exist and contain `index.php` and `app/Config/Paths.php` |

Additional protective excludes (beyond the five requested): `/.user.ini` and `/php.ini` (cPanel-managed PHP settings), `.git` and `/.github/` (never publish repository internals in a web-served directory). Remove a line from `rsync-excludes.txt` and from `required_rules` in `deploy.sh` if you ever want to deploy them.

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
3. **Pin the server's host key** (prints the value for the `SSH_KNOWN_HOSTS` secret):
   ```bash
   ssh-keyscan -p 22 -t ed25519,ecdsa,rsa YOUR.SERVER.HOSTNAME
   ```
   Replace `22` with your SSH port if it differs. Compare the fingerprints (`ssh-keygen -lf <(ssh-keyscan -p 22 YOUR.SERVER.HOSTNAME 2>/dev/null)`) with what your host shows, or with what your own SSH client displayed the first time you connected.
4. **Create the repository secrets** (*Settings → Secrets and variables → Actions*; or the same names as environment secrets of an environment called `production`):

   | Secret | Required | Value |
   | --- | --- | --- |
   | `SSH_HOST` | yes | server hostname or IP |
   | `SSH_PORT` | no | SSH port, default `22` (many cPanel hosts use a custom one) |
   | `SSH_USER` | yes | the cPanel account user name |
   | `SSH_PRIVATE_KEY` | yes | complete contents of `erp_deploy_key`, including the `BEGIN`/`END` lines |
   | `SSH_KNOWN_HOSTS` | yes | the output of the `ssh-keyscan` command above |
   | `DEPLOY_PATH` | yes | **absolute** web-root path of the ERP, e.g. `/home/<cpanel-user>/public_html` (no `~`, no spaces; it is never created automatically) |

   Then delete `erp_deploy_key` and `erp_deploy_key.pub` from your machine.
5. The workflow only shows up in the Actions tab once the file is on the default branch (`main`). Merge the branch that contains it first.
6. *Optional:* under *Settings → Environments → production* add **required reviewers**, so every run needs a click of approval.

## Running a deployment

**Always start with a dry run**, and read the list of files it prints.

1. *Actions → Deploy to production (cPanel) → Run workflow*, leave **dry_run ticked**.
2. In the log of the step *Plan - rsync dry run against production*, every line starting with `<f` is a file that would be sent (`<f+++++++++` = new file, anything else = an existing production file whose content differs from the repository and would be replaced). The job summary shows the counts and the first 200 files.
3. **First deploy only:** the repository is a backup of the old ERP. If production was edited after that backup, those files show up here as "changed" and a real deploy would replace them with the older repository version (a backup copy is kept, but review the list before continuing). Investigate anything you do not expect.
4. Run the workflow again from `main` with **dry_run unticked** to upload.

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
| `Required secret ... is not set` | create the secret (see table above) |
| `SSH_PRIVATE_KEY is not a valid, passphrase-less private key` | the full key file was not pasted, or it has a passphrase |
| `SSH_KNOWN_HOSTS has no host key for the configured SSH_HOST/SSH_PORT` | regenerate it with `ssh-keyscan -p <port> ...`; host and port must match the secrets |
| `SSH connection to production failed` | wrong host/port/user, key not authorized in cPanel, host key changed, or the server firewall blocks GitHub's runners (allow-list GitHub's Actions IP ranges from `https://api.github.com/meta`, or use a self-hosted runner) |
| `DEPLOY_PATH does not look like the ERP web root` | wrong `DEPLOY_PATH`; the target must contain `index.php` and `app/Config/Paths.php` |
| `rsync is not installed on the server` | ask your host to enable rsync for SSH users |
| `Real deployments are only allowed from 'main'` | start the workflow from `main`, or tick `dry_run` |
| A failed upload | `--delay-updates` stages files first, so a failure normally leaves the old files in place; stray `.~tmp~` folders are cleaned up by the next successful run. Re-run the workflow. |

## Verification performed on this setup

`deploy.sh selftest` runs on every workflow run before the network is touched: it copies a synthetic tree over a fake production tree with the real rsync binary and checks that every protected item is byte-, mode- and mtime-identical afterwards, that production-only files survive, that backups are taken, that permissions are cPanel-safe, that the transfer list checker catches protected paths (including a deliberately broken exclude file), and that a second run is a no-op.

Locally the same script was also exercised end to end against a throw-away `sshd` (non-root user, fake production docroot seeded from this repository): plan/apply, pinned-host-key mismatch, wrong/missing/foreign target directories, injection attempts in `DEPLOY_PATH`/`SSH_HOST`/`SSH_PORT`, the apply gates, key material never appearing in logs, and the rollback procedure above.
