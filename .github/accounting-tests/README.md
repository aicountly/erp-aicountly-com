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
| `test_pl_bsd.php` | basic + | bill-sundry (tax) ledgers directly under a profit & loss category (found on real data): the corrected report includes them in all six layouts, the old condensed view left them out |
| `test_invariants.php` | stress | 314 checks over 8 scenarios (branch / consolidated, several periods): TB debit-credit = raw ledger imbalance, BS assets-liabilities = ledger imbalance, P&L identical in every layout, BS profit line = FY-to-date P&L, the warning banner splits the difference by where the rows are and states no assumed cause |
| `test_export.php` | stress | the REAL `Reportings` controller (page rows) against the REAL `Export` controller (xlsx and csv read back): 144 files, every cell, blanks, totals, notes |
| `render_views.php` | stress / basic | the real report views render without PHP notices, the banner appears only when the ledger is out of balance, the Excel/CSV buttons build the right URL |
| `test_rollover.php` | rollover | the REAL `FYModel` roll-over over three years: openings equal an independent computation and the opening trial balance nets to zero |
| `test_audit.php` | stress / rollover / pairs | the read-only `audit:books` command: figures it reports equal the hand-known ones, the database is byte-identical afterwards, the year-end attribution adds up, and (pairs) vouchers are grouped with their linked composition-scheme journals, the row-by-row detail and the CSV are right, optional tables may be missing |
| `test_composition.php` | pairs / gst | composition-scheme posting end to end: the real `runTransaction()` writes a balanced entry for every shape, an entry that cannot be balanced is refused, and `books:repair-composition` completes only what the stored rows account for. Its Balance Sheet reconciliation is checked too: the report's difference against the ledger's, the split into what the rows explain and what needs a person, the plan against the effect actually measured, and that an unreadable report cannot take the repair down with it |
| `test_carry.php` | stock | `books:carry-opening-stock`: the previous year's closing becomes this year's opening in BOTH tables (`itmoppybal` quantity and `itmoppyval` value - the value alone leaves the valuation average at nil and the year worse off), another valuation method's row is cleared so the un-filtered sum cannot count the stock twice, stock the previous year did not close with is reported and left alone, a negative closing quantity is refused, and a write that does not read back rolls the whole run back |
| `demo_legacy_failures.php` | stress | the same identities against the OLD calculation (kept as `legacy_*`): it fails, which is the root cause of the mismatches |
| `mutation_export.py` | stress | 13 deliberate breakages of the export path (swap debit/credit, drop a row, blank->0, abs(), lose a note, wrong nil_type / consolidated / view / dates ...) must each make `test_export.php` fail |

`fixture_pairs.sql` models the composition-scheme purchases found on real data: the purchase voucher credits the supplier with base + GST but debits only the base, and a linked type-23 system journal (vchbridgen type 3) debits "GST PAID A/C" - sometimes as a single debit, sometimes with a credit to each tax account so that the journal balances by itself and the group is short by exactly those credits. Some pairs balance, some do not, some have no link, one links to another year, one links to itself (the journal rows are inside the purchase), one to a journal without ledger rows, one needs three legs to explain its difference, and a type-1 (credit note) link must never merge groups. The two Output-tax accounts sit under Indirect Expenses (a Profit & Loss category), the input-tax accounts under Duties & Taxes (Balance Sheet).

`fixture_gst.sql` adds a sale whose journal (link type 4) credits the tax accounts but debits GST PAID A/C with 0.00, a purchase whose difference two different sets of legs explain, and one with two identical credit lines.

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

`fixture_sides.sql` holds the two shapes the "System Generated" GST entries come in on real data - every leg on the credit side with GST PAID A/C equal to the tax legs (which rule W corrects by moving that one leg), and GST PAID A/C at exactly twice its tax legs (which it refuses, because halving the debit and doubling the credit both balance the entry but land in different places) - plus eleven near misses it must leave alone: a tax leg of 0.00, a leg that is not a tax ledger, two GST PAID legs, tax legs facing each other, and an amount 2.50 short of the total.

`fixture_stock.sql` is two financial years whose opening stock rows are what the roll-over leaves behind: an item at nil that should carry 2,186 units, an item with no rows at all, an item this year opens with that the previous year never closed with, and an item carrying a value on a second valuation method. The previous year's closing figures come from a stubbed Stock Status report (`run_carry.php`), because the real one reads item tables this sandbox does not carry; what is under test is what the command does with those figures.

`app/Commands/AuditBooks.php` (`php spark audit:books`) is the tool for real companies. It runs inside `BEGIN READ ONLY`
(PostgreSQL refuses any write) and never adjusts a figure. `run_audit.php` runs it against the sandbox:

```bash
./reload_db.sh stress && ./sync_sandbox.sh
php run_audit.php --list
php run_audit.php --company 1 --fy 1 --branch 1
```

Use `php spark audit:books --list` on the server to see the company / financial-year / branch ids.

When the ledger itself does not balance (section 2), the audit groups each unbalanced voucher with the vouchers it is linked to
(composition-scheme "GST PAID A/C" system journals, `vchbridgen` link types 3/4/5; a voucher linked to itself, a journal without
ledger rows and a link into another year are told apart) and says what is *still* unbalanced. For each such group it looks for the legs
that add up to the difference (for example the two credits on the CGST / SGST accounts): if they exist, the group balances exactly
without them; if not, a leg is missing. Add `--vouchers 1` to print the ledger rows of the largest group of each kind (and of one that
balances thanks to its link), `--voucher ID,ID` for specific vouchers, and `--csv FILE` to write every unbalanced voucher, with its
status and what its difference equals, for the accountant. The condensed Profit & Loss is also compared line by line, old code against
corrected.

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
