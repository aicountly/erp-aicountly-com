-- SANDBOX-ONLY synthetic fixture for the FY roll-over (Rewrite Books) code. Company 1, branch 1, three FYs.
-- Stock is stubbed to zero by the test, so every figure below is ledger-only and hand-checkable.
TRUNCATE itmoppyval, accttxnmst, accoppybal, undercrsmt, acctmaster, accgrpmstn, grpparentn, cmpfymastr, vchtxnconso, hobomaster, acctmstdet, cmpmastern, vchtypemst RESTART IDENTITY;
INSERT INTO cmpfymastr (cmp_id, fy_beg_date, fy_end_date, def_val_method, is_imported) VALUES
 (1,'2023-04-01','2024-03-31',3,1),(1,'2024-04-01','2025-03-31',3,1),(1,'2025-04-01','2026-03-31',3,1);
INSERT INTO hobomaster VALUES (1,1,'Head Office');
INSERT INTO grpparentn (acc_grp_parent_id, acc_grp_parent_name) VALUES
 (1,'Owner''s Fund'),(2,'Non Current Liabilities'),(3,'Non Current Assets'),(4,'Current Liabilities'),(5,'Current Assets'),
 (6,'Opening Stock'),(7,'Direct Expenses'),(8,'Sales'),(9,'Closing Stock'),(10,'Direct Income'),(11,'Purchase'),(12,'Indirect Income'),(13,'Indirect Expenses');
INSERT INTO accgrpmstn (acc_grp_id, cmp_id, acc_grp_name) VALUES
 (1,1,'Capital Account'),(2,1,'Reserves & Surplus'),(3,1,'Bank Accounts'),(4,1,'Sales Accounts'),(5,1,'Purchase Accounts'),
 (6,1,'Direct Incomes'),(7,1,'Indirect Expenses');
SELECT setval('accgrpmstn_acc_grp_id_seq', 7);
INSERT INTO acctmaster (acc_id, cmp_id, acc_name) VALUES
 (101,1,'Owner Capital'),(102,1,'HDFC Bank'),(103,1,'Sales'),(104,1,'Purchases'),(105,1,'Commission Received'),(106,1,'Office Rent');
SELECT setval('acctmaster_acc_id_seq', 106);
-- mappings for every FY: groups (type 2) and accounts (type 1). cols: cmp, fy, type, id, under_group, category, under_main, primary
DO $$ DECLARE f int; BEGIN FOR f IN 1..3 LOOP
  INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary) VALUES
   (1,f,2,1,0,1,0,1),(1,f,2,2,1,1,1,0),(1,f,2,3,0,5,0,1),(1,f,2,4,0,8,0,1),(1,f,2,5,0,11,0,1),(1,f,2,6,0,10,0,1),(1,f,2,7,0,13,0,1),
   (1,f,1,101,1,1,1,0),(1,f,1,102,3,5,3,0),(1,f,1,103,4,8,4,0),(1,f,1,104,5,11,5,0),(1,f,1,105,6,10,6,0),(1,f,1,106,7,13,7,0);
END LOOP; END $$;
-- FY1 opening: Bank Dr 500 / Capital Cr 500
INSERT INTO accoppybal (cmp_id,cmpfymastr_id,acc_id,hobo_id,acc_op_bal,acc_py_bal,acc_memo_bal) VALUES (1,1,102,1,500,500,0),(1,1,101,1,-500,-500,0);

CREATE TEMP TABLE v (vch int, dt date, acc int, dc int, amt numeric);
INSERT INTO v VALUES
 -- FY1: sale 300, purchase 100, commission 50, rent 40  => profit 210
 (1,'2023-05-01',102,1,300),(1,'2023-05-01',103,2,300),
 (2,'2023-06-01',104,1,100),(2,'2023-06-01',102,2,100),
 (3,'2023-07-01',102,1, 50),(3,'2023-07-01',105,2, 50),
 (4,'2023-08-01',106,1, 40),(4,'2023-08-01',102,2, 40),
 -- FY2: sale 200, rent 30 => profit 170
 (5,'2024-05-01',102,1,200),(5,'2024-05-01',103,2,200),
 (6,'2024-06-01',106,1, 30),(6,'2024-06-01',102,2, 30),
 -- FY3: sale 100 (no roll-over from FY3 in the test)
 (7,'2025-05-01',102,1,100),(7,'2025-05-01',103,2,100);
INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
  SELECT 1,1,acc,dt,dc,amt,1,vch,vch FROM v;
INSERT INTO vchtxnconso (vch_txn_id,cmp_id,hobo_id,txn_id,vch_type_id,vch_date)
  SELECT DISTINCT vch,1,1,vch,1,dt FROM v;

INSERT INTO cmpmastern VALUES (1,'Roll Co',1);
INSERT INTO vchtypemst VALUES (1,'Journal'),(23,'Composition GST journal');
