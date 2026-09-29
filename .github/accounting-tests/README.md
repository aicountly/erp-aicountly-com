# Accounting regression tests (sandbox only)

Checks the Trial Balance, Profit & Loss, Balance Sheet, their Excel/CSV exports and the year-end carry-forward
(*Rewrite Books*) against **synthetic** ledgers in a **throw-away local PostgreSQL**. It never touches a real database:
`boot.php` points the application's default DB group at `127.0.0.1:5433`, the code under test is copied to
`/var/tmp/erp_sbx` (no `.env`, own `writable/`), and the stock valuation is replaced by fixed figures because stock is
outside the accounting fixes. This folder is under `.github/`, which the production deployment never uploads.

## Run

```bash
cd .github/accounting-tests
./start_pg.sh        # throw-away PostgreSQL 12+ on 127.0.0.1:5433 (needs the server binaries; ERP_TEST_PGPORT to change the port)
./run_all.sh         # syncs the code, reloads each fixture, runs every suite
```

PHP 8.1+ CLI with `pgsql`, `zip`, `xml`, `mbstring`, `gd`; `node` for the export-button test.

## What each suite proves

| suite | ledger | proves |
|---|---|---|
| `test_engine.php`, `test_pl.php`, `test_bs.php`, `test_tb.php` | basic | hand-computed figures: P&L net profit 28,200 in all six layouts, Balance Sheet 891,200 = 891,200, Trial Balance balances and every ledger equals independent SQL |
| `test_invariants.php` | stress | 312 identities over 8 scenarios (branch / consolidated, several periods): TB debit-credit = raw ledger imbalance, BS assets-liabilities = ledger imbalance, P&L identical in every layout, BS profit line = FY-to-date P&L |
| `test_export.php` | stress | the REAL `Reportings` controller (page rows) against the REAL `Export` controller (xlsx and csv read back): 144 files, every cell, blanks, totals, notes |
| `render_views.php` | stress / basic | the real report views render without PHP notices, the banner appears only when the ledger is out of balance, the Excel/CSV buttons build the right URL |
| `test_rollover.php` | rollover | the REAL `FYModel` roll-over over three years: openings equal an independent computation and the opening trial balance nets to zero |
| `test_audit.php` | stress / rollover / pairs | the read-only `audit:books` command: figures it reports equal the hand-known ones, the database is byte-identical afterwards, the year-end attribution adds up, and (pairs) vouchers are grouped with their linked composition-scheme journals, the row-by-row detail and the CSV are right, optional tables may be missing |
| `demo_legacy_failures.php` | stress | the same identities against the OLD calculation (kept as `legacy_*`): it fails, which is the root cause of the mismatches |
| `mutation_export.py` | stress | 13 deliberate breakages of the export path (swap debit/credit, drop a row, blank->0, abs(), lose a note, wrong nil_type / consolidated / view / dates ...) must each make `test_export.php` fail |

`fixture_pairs.sql` models composition-scheme purchases: the purchase voucher credits the supplier with base + GST but debits only the base, and a linked type-23 system journal (vchbridgen type 3) carries the GST as a single debit to "GST PAID A/C" - some pairs balance, some do not, some have no link, one links to another year, and a type-1 (credit note) link must never merge groups.

`fixture_stress.sql` holds the awkward real-world patterns found in the audit: a debit-only composition-scheme
"GST PAID A/C" journal (voucher type 23), a voucher approved on one leg only, an unmapped ledger, a ledger whose group
chain is broken, a ledger missing from the account master, a duplicate mapping row, ledgers in categories the reports do not
know, a sub-group under a P&L category, a P&L ledger carrying an opening balance, negative balances on both sides, a second branch.

## Reproduce the roll-over defects and the repair

```bash
./reload_db.sh rollover
# FYModel.php as it was before the commit "Fix the year-end carry-forward ..." (parent of that commit):
git show "$(git log --format=%H -n1 --grep='Fix the year-end carry-forward' -- app/Models/FYModel.php)^:app/Models/FYModel.php" \
    > /var/tmp/erp_sbx/app/Models/FYModel.php
php test_rollover.php                       # 9 FAIL: P&L ledgers carried in, appropriation sign reversed, profit stored as debit
./sync_sandbox.sh && ./reload_db.sh rollover
php test_rollover.php                       # 18 PASS with the fixed FYModel
php repair_rehearsal.php roll && php repair_rehearsal.php show   # re-running Rewrite Books over old-code openings restores them
```

## Read-only audit of real data

`app/Commands/AuditBooks.php` (`php spark audit:books`) is the tool for real companies. It runs inside `BEGIN READ ONLY`
(PostgreSQL refuses any write) and never adjusts a figure. `run_audit.php` runs it against the sandbox:

```bash
./reload_db.sh stress && ./sync_sandbox.sh
php run_audit.php --list
php run_audit.php --company 1 --fy 1 --branch 1
```

Use `php spark audit:books --list` on the server to see the company / financial-year / branch ids.

When the ledger itself does not balance (section 2), the audit groups each unbalanced voucher with the vouchers it is linked to
(composition-scheme "GST PAID A/C" system journals, `vchbridgen` link types 3/4/5) and says what is *still* unbalanced. Add
`--vouchers 3` to print the ledger rows of the largest ones (and of some that balance thanks to their link), `--voucher ID,ID` for
specific vouchers, and `--csv FILE` to write every unbalanced voucher, with its status, for the accountant.

## Run the audit on the server without deploying (private copy, live site untouched)

The audit only needs the new code, not a deployment. Build a private copy of the live ERP, overlay the changed
files on it, and run `spark` there; it reads the same database inside a read-only transaction.

```bash
LIVE=/home/CPANELUSER/public_html      # live ERP folder (contains spark, app/, vendor/, .env)
STAGE=$HOME/erp-audit                  # private copy, outside the web root
PHP=/opt/cpanel/ea-php81/root/usr/bin/php
mkdir -p "$STAGE" && cd "$LIVE" && cp -a app system spark composer.json .env "$STAGE"/
ln -sfn "$LIVE/vendor" "$STAGE/vendor"
unzip -o -q ~/erp-accounting-update.zip -d "${STAGE:?}"      # the files changed on the branch (git diff --name-only <backup> HEAD -- app)
mkdir -p "$STAGE/public" "$STAGE"/writable/{logs,cache,session,debugbar,errors}
cd "$STAGE" && $PHP spark audit:books --list --company ID
$PHP -d memory_limit=1024M spark audit:books --company ID --fy FYID --branch BRANCHID --json ~/audit.json | tee ~/audit.txt
```

Never extract the overlay into the live folder: a real deployment goes through the workflow, which keeps backups.
Delete `$STAGE` afterwards (it holds a copy of `.env`).
