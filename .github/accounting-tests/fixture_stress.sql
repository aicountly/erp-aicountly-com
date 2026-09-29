-- SANDBOX-ONLY. Applied ON TOP of fixture_basic.sql: awkward patterns found in the audit.
-- (none of this is loaded anywhere except the throw-away test database)

-- (a) composition-scheme "GST PAID A/C": a debit-only system journal (voucher type 23), exactly as Sales.php posts it
INSERT INTO acctmaster (acc_id, cmp_id, acc_name) VALUES (117, 1, 'GST PAID A/C');
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary)
  VALUES (1,1,1,117,0,13,0,1);                                     -- primary account under Indirect Expenses
INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
  VALUES (1,1,117,'2025-07-01',1,1000,1,9,9);
INSERT INTO vchtxnconso (vch_txn_id,cmp_id,hobo_id,txn_id,vch_type_id,vch_date) VALUES (9,1,1,9,23,'2025-07-01');

-- (b) a P&L ledger that carries an opening balance (what the FY roll-over does for categories 10/11)
INSERT INTO acctmaster (acc_id, cmp_id, acc_name) VALUES (118, 1, 'Old Sales (carried forward)');
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary)
  VALUES (1,1,1,118,7,8,7,0);                                       -- inside Sales Accounts
INSERT INTO accoppybal (cmp_id,cmpfymastr_id,acc_id,hobo_id,acc_op_bal) VALUES (1,1,118,1,-5000);

-- (c) a sub-group whose parent group is missing for this FY (broken chain) + a ledger below it
INSERT INTO accgrpmstn (acc_grp_id, cmp_id, acc_grp_name) VALUES (14, 1, 'Orphan Sub-group');
SELECT setval('accgrpmstn_acc_grp_id_seq', 14);
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary)
  VALUES (1,1,2,14,99,13,99,0);                                     -- parent group 99 does not exist
INSERT INTO acctmaster (acc_id, cmp_id, acc_name) VALUES (119, 1, 'Misc Expense (orphan group)');
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary)
  VALUES (1,1,1,119,14,13,99,0);

-- (d) ledgers in categories the reports do not know (6 = Opening Stock, 14)
INSERT INTO acctmaster (acc_id, cmp_id, acc_name) VALUES (120, 1, 'Opening Stock (manual ledger)'), (121, 1, 'Branch Control');
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary)
  VALUES (1,1,1,120,0,6,0,1), (1,1,1,121,0,14,0,1);

-- (e) a ledger row whose account is not in the account master
-- (f) duplicate mapping row for an existing ledger (would fan out a plain join)
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary)
  VALUES (1,1,1,104,4,5,4,0);

-- vouchers (balanced unless stated)
CREATE TEMP TABLE v2 (vch int, dt date, acc int, dc int, amt numeric, typ int, hobo int);
INSERT INTO v2 VALUES
 (10,'2025-08-01',119,1, 200,1,1),(10,'2025-08-01',105,2, 200,1,1),          -- ledger under broken chain
 (11,'2025-08-05',120,1, 300,1,1),(11,'2025-08-05',121,2, 300,1,1),          -- ledgers in unknown categories
 (12,'2025-08-10',999,1,  50,1,1),(12,'2025-08-10',105,2,  50,1,1),          -- account missing from master
 (13,'2025-09-01',104,1, 700,4,1),(13,'2025-09-01',107,2, 700,1,1),          -- pending approval: ONLY the party leg is type 4
 (14,'2025-11-01',105,1,4000,1,2),(14,'2025-11-01',107,2,4000,1,2);          -- branch 2 sale
INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
  SELECT 1,hobo,acc,dt,dc,amt,typ,vch,vch FROM v2;
INSERT INTO vchtxnconso (vch_txn_id,cmp_id,hobo_id,txn_id,vch_type_id,vch_date)
  SELECT DISTINCT vch,1,hobo,vch,1,dt FROM v2;
-- branch 2 opening balance
INSERT INTO accoppybal (cmp_id,cmpfymastr_id,acc_id,hobo_id,acc_op_bal) VALUES (1,1,105,2,25000),(1,1,101,2,-25000);

-- (g) negative displayed amounts: a debtor with a credit balance, a creditor with a debit balance, and a purchase-return ledger
INSERT INTO acctmaster (acc_id, cmp_id, acc_name) VALUES (122,1,'Customer Advance (credit balance)'),(123,1,'Supplier Advance (debit balance)'),(124,1,'Purchase Returns');
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary) VALUES
 (1,1,1,122, 4, 5, 4,0),      -- Sundry Debtors (Current Assets)
 (1,1,1,123, 3, 4, 3,0),      -- Sundry Creditors (Current Liabilities)
 (1,1,1,124, 8,11, 8,0);      -- Purchase Accounts (Purchase)
CREATE TEMP TABLE v3 (vch int, dt date, acc int, dc int, amt numeric);
INSERT INTO v3 VALUES
 (15,'2025-09-10',105,1,5000),(15,'2025-09-10',122,2,5000),
 (16,'2025-09-12',123,1,3000),(16,'2025-09-12',105,2,3000),
 (17,'2025-10-20',103,1,2000),(17,'2025-10-20',124,2,2000);
INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
  SELECT 1,1,acc,dt,dc,amt,1,vch,vch FROM v3;
INSERT INTO vchtxnconso (vch_txn_id,cmp_id,hobo_id,txn_id,vch_type_id,vch_date)
  SELECT DISTINCT vch,1,1,vch,1,dt FROM v3;
