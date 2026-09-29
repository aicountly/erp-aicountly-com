-- SANDBOX-ONLY. Applied ON TOP of fixture_basic.sql (which balances): two more shapes found in the audit of real companies.
--
--   51 sales     Dr 71,000 customer / Cr 71,000 Sales                 balanced
--   52 journal   (type 23, link type 4 to sale 51) Dr GST PAID A/C 0.00, Cr CGST INPUT 355, Cr SGST INPUT 355
--                                                                     -> group 51+52 is 710.00 Cr short: the two tax credits equal it, and the GST PAID debit leg is 0.00
--   53 purchase  Dr 10,000 + Dr GST PAID 1,800 + Dr CGST X 900 + Dr SGST X 900, Cr supplier 11,800
--                                                                     -> 1,800.00 Dr over: EITHER the GST PAID debit OR the two tax debits are the extra legs
--   55 purchase  Dr 10,000 + Dr GST PAID 300, Cr 10,000 + Cr CGST X 300 + Cr CGST X 300 (two credit lines on the SAME account)
--                                                                     -> 300.00 Cr short: either credit line equals it - one answer, not two
-- Whole-ledger imbalance: 790.00 Dr.

INSERT INTO acctmaster (acc_id, cmp_id, acc_name, acc_is_restrict) VALUES (117, 1, 'GST PAID A/C', 2);
INSERT INTO billsundry (bsd_id, bsd_name, bsd_type, bsd_input_output, tax_cat_type, tax_cat_sub_type) VALUES
 (11,'CGST input',1,1,1,2),(12,'SGST input',1,1,1,3),(16,'CGST other',1,2,1,2),(17,'SGST other',1,2,1,3);
INSERT INTO acctmaster (acc_id, cmp_id, acc_name, bsd_id) VALUES (141,1,'CGST INPUT',11),(142,1,'SGST INPUT',12),(146,1,'CGST X',16),(147,1,'SGST X',17);
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary) VALUES
 (1,1,1,117, 0,13, 0,1),                                            -- GST PAID A/C: primary account under Indirect Expenses
 (1,1,1,141,13, 4,13,0),(1,1,1,142,13, 4,13,0),(1,1,1,146,13, 4,13,0),(1,1,1,147,13, 4,13,0);   -- tax accounts under Duties & Taxes
INSERT INTO vchtypemst VALUES (11,'Purchase') ON CONFLICT DO NOTHING;

CREATE TEMP TABLE v (vch int, dt date, acc int, dc int, amt numeric);
INSERT INTO v VALUES
 (51,'2025-08-01',104,1,71000),(51,'2025-08-01',107,2,71000),
 (52,'2025-08-01',117,1,    0),(52,'2025-08-01',141,2,  355),(52,'2025-08-01',142,2,  355),
 (53,'2025-08-05',108,1,10000),(53,'2025-08-05',117,1, 1800),(53,'2025-08-05',146,1,  900),(53,'2025-08-05',147,1,  900),(53,'2025-08-05',103,2,11800),
 (55,'2025-08-10',108,1,10000),(55,'2025-08-10',117,1,  300),(55,'2025-08-10',103,2,10000),(55,'2025-08-10',146,2,  300),(55,'2025-08-10',146,2,  300);
INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
  SELECT 1,1,acc,dt,dc,amt,1,vch,vch FROM v;
INSERT INTO vchtxnconso (vch_txn_id,cmp_id,hobo_id,txn_id,vch_type_id,vch_date,vch_sub_type_id,vch_series_id) VALUES
 (51,1,1,51,18,'2025-08-01',8,2),(52,1,1,52,23,'2025-08-01',0,7),(53,1,1,53,11,'2025-08-05',5,3),(55,1,1,55,11,'2025-08-10',5,3);
SELECT setval('vchtxnconso_vch_txn_id_seq', 55);
INSERT INTO vchbridgen (cmp_id, vch_bridge_type, vch_txn_id_src, vch_txn_id_dest) VALUES (1,4,51,52);
