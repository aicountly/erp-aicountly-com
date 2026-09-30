-- SANDBOX-ONLY. Applied ON TOP of fixture_basic.sql (which balances). Every voucher here is a
-- "System Generated" composition entry (voucher type 22) of the two shapes found in the real books of
-- both audited companies, where GST PAID A/C and the tax ledgers disagree about sides or amounts:
--
--   fixed by rule W - the GST PAID A/C leg sits on the same side as its tax legs and holds their total:
--   51  Cr GST PAID 402.50, Cr CGST 201.25, Cr SGST 201.25   -> 805.00 Cr   (the shape of K.K. voucher 144587)
--   52  Cr GST PAID 402.50, Cr CGST 402.50                   -> 805.00 Cr   (one tax leg, not two)
--
--   left for review - each one balances two different ways, so which leg is wrong is not in the data:
--   53  Dr GST PAID 300.00, Dr CGST 150.00, Dr SGST 150.00   -> 600.00 Dr   (rule W's mirror: a levy, or the reversal of one?)
--   54  Dr GST PAID 284.00, Cr CGST 142.00                   -> 142.00 Dr   (the shape of K.K. voucher 143799: GST PAID is twice the tax)
--   59  Dr GST PAID 355.00, Cr CGST  88.75, Cr SGST 88.75    -> 177.50 Dr   (the same, split across two tax ledgers)
--   55  Cr GST PAID 400.00, Cr CGST 201.25, Cr SGST 201.25   -> 802.50 Cr   (GST PAID is 2.50 short of the tax legs: not the wrong-side shape)
--   56  Cr GST PAID 402.50, Cr CGST 201.25, Cr SGST 201.25, Cr Purchase 100.00 -> 905.00 Cr  (a leg that is not a tax ledger)
--   57  Cr GST PAID 201.25, Cr GST PAID 201.25, Cr CGST 402.50 -> 805.00 Cr  (two GST PAID legs: a different shape)
--   58  Cr GST PAID 402.50, Cr Freight 402.50                -> 805.00 Cr   (Freight is a bill sundry but not a GST tax ledger)
--   60  Cr GST PAID 402.50, Cr CGST 201.25                    -> 603.75 Cr   (GST PAID is twice the tax, but on the SAME side: not the doubled shape either)
--   61  Cr GST PAID 402.50, Cr CGST 402.50, Cr SGST 0.00      -> 805.00 Cr   (a tax leg of 0.00: rule W must not treat it as the wrong-side shape)
--   62  Dr GST PAID 600.00, Dr CGST 200.00, Cr SGST 100.00    -> 700.00 Dr   (tax legs facing each other: a shape of its own)
--   63  Dr GST PAID 600.00, Cr CGST 200.00, Dr SGST 100.00    -> 500.00 Dr   (the same, the other way round)
--
-- Whole-ledger imbalance: 4,216.75 Cr, of which rule W closes 1,610.00, leaving 2,606.75 Cr for review.

INSERT INTO acctmaster (acc_id, cmp_id, acc_name, acc_is_restrict) VALUES (117, 1, 'GST PAID A/C', 2);
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary)
  VALUES (1,1,1,117,0,13,0,1);                                     -- primary account under Indirect Expenses
INSERT INTO billsundry (bsd_id, bsd_name, bsd_type, bsd_input_output, tax_cat_type, tax_cat_sub_type) VALUES
 (11,'CGST input',1,1,1,2),(12,'SGST input',1,1,1,3),
 (19,'Freight',2,1,0,0);                                           -- NOT a tax ledger: rule W must not count it
INSERT INTO acctmaster (acc_id, cmp_id, acc_name, bsd_id) VALUES
 (141,1,'CGST INPUT A/C',11),(142,1,'SGST INPUT A/C',12),(146,1,'FREIGHT INWARD',19);
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary) VALUES
 (1,1,1,141,13,4,13,0),(1,1,1,142,13,4,13,0),(1,1,1,146,13,4,13,0);
INSERT INTO vchtypemst VALUES (22,'System Generated') ON CONFLICT DO NOTHING;

CREATE TEMP TABLE w (vch int, dt date, acc int, dc int, amt numeric);
INSERT INTO w VALUES
 (51,'2025-06-10',117,2,402.50),(51,'2025-06-10',141,2,201.25),(51,'2025-06-10',142,2,201.25),
 (52,'2025-07-14',117,2,402.50),(52,'2025-07-14',141,2,402.50),
 (53,'2025-08-22',117,1,300.00),(53,'2025-08-22',141,1,150.00),(53,'2025-08-22',142,1,150.00),
 (54,'2025-09-30',117,1,284.00),(54,'2025-09-30',141,2,142.00),
 (55,'2025-10-18',117,2,400.00),(55,'2025-10-18',141,2,201.25),(55,'2025-10-18',142,2,201.25),
 (56,'2025-11-05',117,2,402.50),(56,'2025-11-05',141,2,201.25),(56,'2025-11-05',142,2,201.25),(56,'2025-11-05',108,2,100.00),
 (57,'2025-12-01',117,2,201.25),(57,'2025-12-01',117,2,201.25),(57,'2025-12-01',141,2,402.50),
 (58,'2026-01-20',117,2,402.50),(58,'2026-01-20',146,2,402.50),
 (59,'2026-03-05',117,1,355.00),(59,'2026-03-05',141,2, 88.75),(59,'2026-03-05',142,2, 88.75),
 (60,'2025-06-20',117,2,402.50),(60,'2025-06-20',141,2,201.25),
 (61,'2025-07-25',117,2,402.50),(61,'2025-07-25',141,2,402.50),(61,'2025-07-25',142,2,  0.00),
 (62,'2025-09-05',117,1,600.00),(62,'2025-09-05',141,1,200.00),(62,'2025-09-05',142,2,100.00),
 (63,'2025-09-06',117,1,600.00),(63,'2025-09-06',141,2,200.00),(63,'2025-09-06',142,1,100.00);
INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
  SELECT 1,1,acc,dt,dc,amt,1,vch,vch FROM w;

INSERT INTO vchtxnconso (vch_txn_id,cmp_id,hobo_id,txn_id,vch_type_id,vch_date,vch_sub_type_id,vch_series_id) VALUES
 (51,1,1,51,22,'2025-06-10',8,7),(52,1,1,52,22,'2025-07-14',8,7),(53,1,1,53,22,'2025-08-22',8,7),
 (54,1,1,54,22,'2025-09-30',8,7),(55,1,1,55,22,'2025-10-18',8,7),(56,1,1,56,22,'2025-11-05',8,7),
 (57,1,1,57,22,'2025-12-01',8,7),(58,1,1,58,22,'2026-01-20',8,7),(59,1,1,59,22,'2026-03-05',8,7),
 (60,1,1,60,22,'2025-06-20',8,7),(61,1,1,61,22,'2025-07-25',8,7),
 (62,1,1,62,22,'2025-09-05',8,7),(63,1,1,63,22,'2025-09-06',8,7);
SELECT setval('vchtxnconso_vch_txn_id_seq', 63);

-- the branch is registered under the composition scheme (hobo_gstin_type 2)
INSERT INTO hobogstinm (cmp_id, hobo_id, hobo_gstin_type) VALUES (1, 1, 2);
