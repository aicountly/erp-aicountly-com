-- SANDBOX-ONLY. Two financial years for company 1 / branch 1, built so every shape books:carry-openings has
-- to tell apart is present and hand-checkable. Stock is stubbed to zero by the runner, so every figure is
-- ledger-only.
--
-- FY 1 (2023-04-01 .. 2024-03-31) closes at:
--   101 Owner Capital      500.00 Cr      104 Purchases      100.00 Dr  (P&L)
--   102 HDFC Bank          550.00 Dr      103 Sales          300.00 Cr  (P&L)
--   107 OLD SUPPLIER       100.00 Cr      110 P&L Appropriation   0.00
--   109 MISSING ROW LTD    250.00 Dr      -> FY 1 profit 200.00
--
-- FY 2 opens with, and what each one is:
--   101  500.00 Cr   right, so it must not appear in the list at all
--   102  540.00 Dr   wrong by 10.00                          -> to correct
--   107       0.00   retired in favour of 108                -> LEAVE ALONE
--   108  100.00 Cr   took 107's balance                      -> LEAVE ALONE
--   109  no row      never carried                           -> to insert
--   110  900.00 Dr   should hold last year's result, 200 Cr  -> only from --appropriation
--
-- Stored openings net 840.00 Dr, which is 'Difference in Opening'. Correcting 102, 109 and 110 takes it to
-- exactly 0.00; the 107/108 pair nets to nothing, which is why leaving it alone is safe.
--
-- 110 differs from its previous closing on purpose: it must NOT show up in the list of differing ledgers,
-- because naming it does not set it to that closing. It has a line of its own.

TRUNCATE itmoppyval, itmoppybal, accttxnmst, accoppybal, undercrsmt, acctmaster, accgrpmstn, grpparentn,
         cmpfymastr, vchtxnconso, hobomaster, acctmstdet, cmpmastern, vchtypemst RESTART IDENTITY;
INSERT INTO cmpfymastr (cmp_id, fy_beg_date, fy_end_date, def_val_method, is_imported) VALUES
 (1,'2023-04-01','2024-03-31',3,1),(1,'2024-04-01','2025-03-31',3,1);
INSERT INTO hobomaster VALUES (1,1,'Head Office');
INSERT INTO cmpmastern (cmp_id, cmp_name) VALUES (1,'Test Co');
INSERT INTO grpparentn (acc_grp_parent_id, acc_grp_parent_name) VALUES
 (1,'Owner''s Fund'),(4,'Current Liabilities'),(5,'Current Assets'),(8,'Sales'),(11,'Purchase'),(13,'Indirect Expenses');
INSERT INTO accgrpmstn (acc_grp_id, cmp_id, acc_grp_name) VALUES
 (1,1,'Capital Account'),(3,1,'Bank Accounts'),(4,1,'Sales Accounts'),(5,1,'Purchase Accounts'),
 (8,1,'Sundry Creditors'),(9,1,'Sundry Debtors');
SELECT setval('accgrpmstn_acc_grp_id_seq', 9);
INSERT INTO acctmaster (acc_id, cmp_id, acc_name) VALUES
 (101,1,'Owner Capital'),(102,1,'HDFC Bank'),(103,1,'Sales'),(104,1,'Purchases'),
 (107,1,'OLD SUPPLIER'),(108,1,'NEW SUPPLIER'),(109,1,'MISSING ROW LTD'),(110,1,'Profit & Loss Appropriation');
SELECT setval('acctmaster_acc_id_seq', 110);
DO $$ DECLARE f int; BEGIN FOR f IN 1..2 LOOP
  INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary) VALUES
   (1,f,2,1,0,1,0,1),(1,f,2,3,0,5,0,1),(1,f,2,4,0,8,0,1),(1,f,2,5,0,11,0,1),(1,f,2,8,0,4,0,1),(1,f,2,9,0,5,0,1),
   (1,f,1,101,1,1,1,0),(1,f,1,102,3,5,3,0),(1,f,1,103,4,8,4,0),(1,f,1,104,5,11,5,0),
   (1,f,1,107,8,4,8,0),(1,f,1,108,8,4,8,0),(1,f,1,109,9,5,9,0),(1,f,1,110,1,1,1,0);
END LOOP; END $$;

-- FY 1 openings, then its transactions
INSERT INTO accoppybal (cmp_id,cmpfymastr_id,acc_id,hobo_id,acc_op_bal,acc_py_bal,acc_memo_bal) VALUES
 (1,1,102,1,500,500,0),(1,1,101,1,-500,-500,0);
INSERT INTO vchtypemst VALUES (10,'Journal') ON CONFLICT DO NOTHING;
CREATE TEMP TABLE v (vch int, dt date, acc int, dc int, amt numeric);
INSERT INTO v VALUES
 (1,'2023-05-01',102,1,300),(1,'2023-05-01',103,2,300),     -- sale 300
 (2,'2023-06-01',104,1,100),(2,'2023-06-01',107,2,100),     -- purchase 100 on credit from OLD SUPPLIER
 (3,'2023-07-01',109,1,250),(3,'2023-07-01',102,2,250);     -- 250 advanced to MISSING ROW LTD
INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
  SELECT 1,1,acc,dt,dc,amt,1,vch,vch FROM v;
INSERT INTO vchtxnconso (vch_txn_id,cmp_id,hobo_id,txn_id,vch_type_id,vch_date,vch_sub_type_id,vch_series_id)
  SELECT DISTINCT vch,1,1,vch,10,dt,0,1 FROM v;
SELECT setval('vchtxnconso_vch_txn_id_seq', 3);

-- FY 2 openings, as described at the top
INSERT INTO accoppybal (cmp_id,cmpfymastr_id,acc_id,hobo_id,acc_op_bal,acc_py_bal,acc_memo_bal) VALUES
 (1,2,101,1,-500,-500,0),
 (1,2,102,1, 540, 540,0),
 (1,2,107,1,   0,   0,0),
 (1,2,108,1,-100,-100,0),
 (1,2,110,1, 900, 900,0);
