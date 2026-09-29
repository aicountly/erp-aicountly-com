-- SANDBOX-ONLY synthetic fixture. Company 1, FY 1 (2025-04-01..2026-03-31), branch 1.
-- Stock is stubbed by the harness: opening 40,000 / closing 65,000 (not in the ledger).
TRUNCATE itmoppyval, accttxnmst, accoppybal, undercrsmt, acctmaster, accgrpmstn, grpparentn, cmpfymastr, vchtxnconso, hobomaster, acctmstdet, cmpmastern, vchtypemst RESTART IDENTITY;

INSERT INTO cmpfymastr (cmp_id, fy_beg_date, fy_end_date, def_val_method) VALUES (1,'2025-04-01','2026-03-31',3);

INSERT INTO grpparentn (acc_grp_parent_id, acc_grp_parent_name) VALUES
 (1,'Owner''s Fund'),(2,'Non Current Liabilities'),(3,'Non Current Assets'),(4,'Current Liabilities'),
 (5,'Current Assets'),(6,'Opening Stock'),(7,'Direct Expenses'),(8,'Sales'),(9,'Closing Stock'),
 (10,'Direct Income'),(11,'Purchase'),(12,'Indirect Income'),(13,'Indirect Expenses');

-- groups: id, name
INSERT INTO accgrpmstn (acc_grp_id, cmp_id, acc_grp_name) VALUES
 (1,1,'Capital Account'),(2,1,'Reserves & Surplus'),(3,1,'Sundry Creditors'),(4,1,'Sundry Debtors'),
 (5,1,'Bank Accounts'),(6,1,'Fixed Assets'),(7,1,'Sales Accounts'),(8,1,'Purchase Accounts'),
 (9,1,'Direct Expenses'),(10,1,'Indirect Expenses'),(11,1,'Salaries'),(12,1,'Indirect Income'),
 (13,1,'Duties & Taxes');
SELECT setval('accgrpmstn_acc_grp_id_seq', 13);

-- group hierarchy (type 2): cols = cmp, fy, type, id, under_group, parent_category, under_main, is_primary
-- NOTE: a sub-group copies its parent's category into crs_mst_parent_id (Accounts.php add_group).
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary) VALUES
 (1,1,2, 1, 0, 1, 0, 1),   -- Capital Account            (primary, Owner's Fund)
 (1,1,2, 2, 1, 1, 1, 0),   -- Reserves & Surplus         (sub-group of Capital)
 (1,1,2, 3, 0, 4, 0, 1),   -- Sundry Creditors           (Current Liabilities)
 (1,1,2, 4, 0, 5, 0, 1),   -- Sundry Debtors             (Current Assets)
 (1,1,2, 5, 0, 5, 0, 1),   -- Bank Accounts              (Current Assets)
 (1,1,2, 6, 0, 3, 0, 1),   -- Fixed Assets               (Non Current Assets)
 (1,1,2, 7, 0, 8, 0, 1),   -- Sales Accounts             (Sales)
 (1,1,2, 8, 0,11, 0, 1),   -- Purchase Accounts          (Purchase)
 (1,1,2, 9, 0, 7, 0, 1),   -- Direct Expenses            (Direct Expenses)
 (1,1,2,10, 0,13, 0, 1),   -- Indirect Expenses          (Indirect Expenses)
 (1,1,2,11,10,13,10, 0),   -- Salaries (SUB-GROUP)       under Indirect Expenses, category copied
 (1,1,2,12, 0,12, 0, 1),   -- Indirect Income
 (1,1,2,13, 0, 4, 0, 1);   -- Duties & Taxes             (Current Liabilities)

-- accounts
INSERT INTO acctmaster (acc_id, cmp_id, acc_name, bsd_id) VALUES
 (101,1,'Owner Capital',NULL),(102,1,'General Reserve',NULL),(103,1,'ABC Suppliers',NULL),
 (104,1,'XYZ Customer',NULL),(105,1,'HDFC Bank',NULL),(106,1,'Plant & Machinery',NULL),
 (107,1,'Sales',NULL),(108,1,'Purchases',NULL),(109,1,'Freight Inward',NULL),(110,1,'Office Rent',NULL),
 (111,1,'Staff Salary',NULL),(112,1,'Interest Received',NULL),
 (113,1,'CGST Output',1),(114,1,'SGST Output',2),
 (115,1,'Discount Allowed',NULL),
 (116,1,'Suspense (unmapped)',NULL);
SELECT setval('acctmaster_acc_id_seq', 116);

-- account mapping: type 1 (normal) / 14 (bill sundry). under=0 => "primary account" directly under a category.
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary) VALUES
 (1,1, 1,101, 1, 1, 1,0),
 (1,1, 1,102, 2, 1, 1,0),
 (1,1, 1,103, 3, 4, 3,0),
 (1,1, 1,104, 4, 5, 4,0),
 (1,1, 1,105, 5, 5, 5,0),
 (1,1, 1,106, 6, 3, 6,0),
 (1,1, 1,107, 7, 8, 7,0),
 (1,1, 1,108, 8,11, 8,0),
 (1,1, 1,109, 9, 7, 9,0),
 (1,1, 1,110,10,13,10,0),
 (1,1, 1,111,11,13,10,0),
 (1,1, 1,112,12,12,12,0),
 (1,1,14,113,13, 4,13,0),
 (1,1,14,114,13, 4,13,0),
 (1,1, 1,115, 0,13, 0,0);   -- Discount Allowed: PRIMARY ACCOUNT directly under Indirect Expenses
-- 116 Suspense: deliberately NO undercrsmt row for this FY (unmapped)

-- opening balances (+Dr / -Cr). Sum = -40,000 = -(opening stock, which sits outside the ledger)
INSERT INTO accoppybal (cmp_id,cmpfymastr_id,acc_id,hobo_id,acc_op_bal) VALUES
 (1,1,101,1,-610000),(1,1,102,1,-100000),(1,1,103,1,-80000),
 (1,1,104,1,150000),(1,1,105,1,400000),(1,1,106,1,200000);

-- vouchers: every voucher balances (Dr = Cr). dr_cr: 1 = Dr, 2 = Cr.
CREATE TEMP TABLE v (vch int, dt date, acc int, dc int, amt numeric);
INSERT INTO v VALUES
 (1,'2025-04-10',104,1,118000),(1,'2025-04-10',107,2,100000),(1,'2025-04-10',113,2,9000),(1,'2025-04-10',114,2,9000),
 (2,'2025-04-15',108,1, 50000),(2,'2025-04-15',109,1,  5000),(2,'2025-04-15',103,2, 55000),
 (3,'2025-05-01',105,1,118000),(3,'2025-05-01',104,2,118000),
 (4,'2025-05-05',110,1, 12000),(4,'2025-05-05',105,2, 12000),
 (5,'2025-05-31',111,1, 30000),(5,'2025-05-31',105,2, 30000),
 (6,'2025-06-01',105,1,   500),(6,'2025-06-01',112,2,   500),
 (7,'2025-06-10',115,1,   300),(7,'2025-06-10',104,2,   300),
 (8,'2025-06-15',116,1,  1000),(8,'2025-06-15',105,2,  1000);
INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
  SELECT 1,1,acc,dt,dc,amt,1,vch,vch FROM v;
INSERT INTO vchtxnconso (vch_txn_id,cmp_id,hobo_id,txn_id,vch_type_id,vch_date)
  SELECT DISTINCT vch,1,1,vch,1,dt FROM v;

INSERT INTO itmoppyval (cmp_id,cmpfymastr_id,hobo_id,itm_id_unit_id,itm_op_val_amt,itm_val_method_id,mat_cent_id)
  VALUES (1,1,1,'1_1',40000,3,1);

INSERT INTO cmpmastern VALUES (1,'Test Co',1);
INSERT INTO hobomaster VALUES (1,1,'Head Office'),(1,2,'Branch 2');
INSERT INTO vchtypemst VALUES (1,'Journal'),(18,'Sales'),(23,'Composition GST journal');
