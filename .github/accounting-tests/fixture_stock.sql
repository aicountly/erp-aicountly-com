-- SANDBOX-ONLY. Two financial years for company 1 / branch 1, so the opening stock of FY 2 can be set from
-- what FY 1 closed with. The closing figures themselves come from a stubbed Stock Status report (run_carry.php),
-- because the real one reads item tables this sandbox does not carry; what is under test here is what the
-- command does with those figures, not the report that produces them.
--
-- FY 2 as stored today, which is what the roll-over leaves behind:
--   ITEM-A_1   quantity row 0, value rows AVG 0 / FIFO 0          -> must become 2,186 and 4,262,806.62 / 0
--   ITEM-B_1   no rows at all                                      -> must be inserted
--   ITEM-C_1   quantity 12, value AVG 999.00                       -> FY 1 did not close with it: left alone, warned
--   ITEM-D_1   quantity row 0, value rows AVG 0 / LIFO 500.00      -> the LIFO row must be cleared, or it is counted twice

TRUNCATE itmoppyval, itmoppybal, cmpfymastr, hobomaster, cmpmastern RESTART IDENTITY;
INSERT INTO cmpfymastr (cmp_id, fy_beg_date, fy_end_date, def_val_method, is_imported) VALUES
 (1,'2023-04-01','2024-03-31',3,1),(1,'2024-04-01','2025-03-31',3,1);
INSERT INTO hobomaster VALUES (1,1,'Head Office');
INSERT INTO cmpmastern (cmp_id, cmp_name) VALUES (1,'Test Co');

-- FY 2 (cmpfymastr_id 2) as the roll-over left it
INSERT INTO itmoppybal (cmp_id,cmpfymastr_id,hobo_id,itm_id_unit_id,itm_op_bal_qty,itm_py_bal_qty,mat_cent_id) VALUES
 (1,2,1,'ITEM-A_1',0,0,NULL),
 (1,2,1,'ITEM-C_1',12,0,NULL),
 (1,2,1,'ITEM-D_1',0,0,NULL);
INSERT INTO itmoppyval (cmp_id,cmpfymastr_id,hobo_id,itm_id_unit_id,itm_op_val_amt,itm_py_val_amt,itm_val_method_id,mat_cent_id) VALUES
 (1,2,1,'ITEM-A_1',0,0,3,NULL),
 (1,2,1,'ITEM-A_1',0,0,1,NULL),
 (1,2,1,'ITEM-C_1',999.00,0,3,NULL),
 (1,2,1,'ITEM-D_1',0,0,3,NULL),
 (1,2,1,'ITEM-D_1',500.00,0,2,NULL);
