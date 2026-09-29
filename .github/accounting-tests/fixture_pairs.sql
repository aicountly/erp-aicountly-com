-- SANDBOX-ONLY. Applied ON TOP of fixture_basic.sql (which balances): composition-scheme purchases and their linked
-- "GST PAID A/C" system journals (voucher type 23, vchbridgen link type 3), shaped like the ones found in the audit of
-- real companies: the purchase voucher credits the supplier with base + GST but debits only the base; the linked
-- system journal carries the GST as a single debit to "GST PAID A/C".
--
--   21 purchase  Dr 10,000  Cr 11,800    linked 22 (Dr 1,800)          -> balance together
--   23 purchase  Dr 50,000  Cr 59,000    linked 24 (Dr 4,500 only)     -> together still 4,500.00 Cr short
--   25 purchase  Dr 20,000  Cr 23,600    no linked voucher             -> 3,600.00 Cr
--   27 journal   Dr    500  (GST PAID A/C) no linked voucher           -> 500.00 Dr
--   29 purchase  Dr  1,000  Cr  1,180    linked 30, dated in the PREVIOUS year -> 180.00 Cr, linked voucher outside the year
--   31 purchase  Dr  2,000  Cr  2,360    linked 32 (Dr 360)            -> balance together (a second pair)
--   33 purchase  Dr  5,000  Cr  5,900    no link; its GST summary says 500 tax, not the 900 difference -> 900.00 Cr
--   35 purchase  Dr 10,000  Cr 11,800    linked 36 = Dr GST PAID 1,800, Cr CGST INPUT 900, Cr SGST INPUT 900 (a journal that balances by itself,
--                                         as in the audited companies) -> together 1,800.00 Cr short: equals the two credit legs
--   37 purchase  Dr 20,000 + Dr GST PAID 2,400, Cr 22,400 + Cr CGST 1,200 + Cr SGST 1,200; linked to ITSELF (journal rows inside the
--                                         voucher) -> 2,400.00 Cr: equals the two credit legs
--   38 purchase  Dr  5,000  Cr  5,900    linked 39, a journal voucher that exists but has NO ledger rows -> 900.00 Cr, a leg is missing
--   40 purchase  Dr 10,000 + Dr GST PAID 2,000, Cr 12,000 + Cr CGST 900 + Cr SGST 900 + Cr CESS 200; no link -> 2,000.00 Cr: equals THREE credit legs
--   26 sales     balanced (Dr 11,800, Cr 10,000 + 900 + 900)           -> not in any list
-- Whole-ledger imbalance: 15,780.00 Cr.

INSERT INTO acctmaster (acc_id, cmp_id, acc_name, acc_is_restrict) VALUES (117, 1, 'GST PAID A/C', 2);
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary)
  VALUES (1,1,1,117,0,13,0,1);                                     -- primary account under Indirect Expenses
INSERT INTO acctmaster (acc_id, cmp_id, acc_name) VALUES (141,1,'CGST INPUT A/C'),(142,1,'SGST INPUT A/C'),(143,1,'Central Tax (Output)'),(144,1,'State Tax (Output)'),(145,1,'CESS INPUT A/C');
INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary) VALUES
 (1,1,1,141,13,4,13,0),(1,1,1,142,13,4,13,0),(1,1,1,143,13,4,13,0),(1,1,1,144,13,4,13,0),(1,1,1,145,13,4,13,0);   -- under Duties & Taxes (Current Liabilities)
INSERT INTO vchtypemst VALUES (11,'Purchase') ON CONFLICT DO NOTHING;

CREATE TEMP TABLE v (vch int, dt date, acc int, dc int, amt numeric);
INSERT INTO v VALUES
 (21,'2025-08-05',108,1,10000),(21,'2025-08-05',103,2,11800),
 (22,'2025-08-05',117,1, 1800),
 (23,'2025-09-10',108,1,50000),(23,'2025-09-10',103,2,59000),
 (24,'2025-09-10',117,1, 4500),
 (25,'2025-10-01',108,1,20000),(25,'2025-10-01',103,2,23600),
 (26,'2025-10-15',104,1,11800),(26,'2025-10-15',107,2,10000),(26,'2025-10-15',113,2,900),(26,'2025-10-15',114,2,900),
 (27,'2025-11-02',117,1,  500),
 (29,'2025-12-01',108,1, 1000),(29,'2025-12-01',103,2, 1180),
 (31,'2026-01-10',108,1, 2000),(31,'2026-01-10',103,2, 2360),
 (32,'2026-01-10',117,1,  360),
 (33,'2026-02-01',108,1, 5000),(33,'2026-02-01',103,2, 5900),
 (35,'2026-02-10',108,1,10000),(35,'2026-02-10',103,2,11800),
 (36,'2026-02-10',117,1, 1800),(36,'2026-02-10',141,2,  900),(36,'2026-02-10',142,2,  900),
 (37,'2026-02-15',108,1,20000),(37,'2026-02-15',117,1, 2400),(37,'2026-02-15',103,2,22400),(37,'2026-02-15',143,2,1200),(37,'2026-02-15',144,2,1200),
 (38,'2026-02-20',108,1, 5000),(38,'2026-02-20',103,2, 5900),
 (40,'2026-02-25',108,1,10000),(40,'2026-02-25',117,1, 2000),(40,'2026-02-25',103,2,12000),(40,'2026-02-25',141,2,  900),(40,'2026-02-25',142,2,  900),(40,'2026-02-25',145,2,  200);
INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
  SELECT 1,1,acc,dt,dc,amt,1,vch,vch FROM v;
-- the linked voucher of 29 belongs to the previous financial year (outside the report window)
INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
  VALUES (1,1,117,'2025-03-31',1,180,1,30,30);

INSERT INTO vchtxnconso (vch_txn_id,cmp_id,hobo_id,txn_id,vch_type_id,vch_date,vch_sub_type_id,vch_series_id) VALUES
 (21,1,1,21,11,'2025-08-05',5,3),(22,1,1,22,23,'2025-08-05',0,7),
 (23,1,1,23,11,'2025-09-10',5,3),(24,1,1,24,23,'2025-09-10',0,7),
 (25,1,1,25,11,'2025-10-01',5,3),
 (26,1,1,26,18,'2025-10-15',0,2),
 (27,1,1,27,23,'2025-11-02',0,7),
 (29,1,1,29,11,'2025-12-01',5,3),(30,1,1,30,23,'2025-03-31',0,7),
 (31,1,1,31,11,'2026-01-10',5,3),(32,1,1,32,23,'2026-01-10',0,7),
 (33,1,1,33,11,'2026-02-01',5,3),
 (35,1,1,35,11,'2026-02-10',5,3),(36,1,1,36,23,'2026-02-10',0,7),
 (37,1,1,37,11,'2026-02-15',5,3),
 (38,1,1,38,11,'2026-02-20',5,3),(39,1,1,39,23,'2026-02-20',0,7),          -- 39: a journal header with no ledger rows at all
 (40,1,1,40,11,'2026-02-25',5,3);
SELECT setval('vchtxnconso_vch_txn_id_seq', 40);

INSERT INTO vchbridgen (cmp_id, vch_bridge_type, vch_txn_id_src, vch_txn_id_dest) VALUES
 (1,3,21,22),(1,3,23,24),(1,3,29,30),(1,3,31,32),(1,3,35,36),(1,3,37,37),(1,3,38,39),      -- 37 is linked to itself
 (1,1,25,27);                                        -- link type 1 (credit note against an invoice): NOT a composition link, must never merge groups

INSERT INTO vchgstsumn (cmp_id, vch_txn_id, txn_id, acc_bsd_id, acc_bsd_type, vch_taxable_value, vch_igst, vch_total_tax) VALUES
 (1,21,1,1,3,10000,1800,1800),(1,23,1,1,3,50000,9000,9000),(1,25,1,1,3,20000,3600,3600),(1,29,1,1,3,1000,180,180),
 (1,31,1,1,3,2000,360,360),(1,33,1,1,3,5000,500,500),(1,35,1,1,3,10000,0,1800),(1,37,1,1,3,20000,0,2400);
INSERT INTO gstrinwsup (cmp_id, vch_txn_id, inwsup_bill_ref_no) VALUES (1,21,'INV-101'),(1,23,'INV-102'),(1,25,'INV-103'),(1,31,'INV-104'),(1,33,'INV-105');
INSERT INTO itemtxnmst (cmp_id, hobo_id, vch_txn_id, itm_id_unit_id, itm_txn_qty, itm_txn_amt, itm_txn_type, itm_txn_dr_cr) VALUES
 (1,1,21,'1_1',100,10000,1,1),(1,1,23,'1_1',500,50000,1,1);

-- the branch is registered under the composition scheme (hobo_gstin_type 2)
UPDATE hobomaster SET hobo_gstin_type = 2 WHERE cmp_id = 1 AND hobo_id = 1;
