<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-08-12 12:16:51 --> 1. Check if current user owns the company
1 - 2026-08-12 12:16:51 --> SELECT *
FROM "cmpacsmstr"
WHERE "cmp_id" = '136'
AND "uuid_acs_type" = 1
AND "uuid_aictly_by" = '7'
1 - 2026-08-12 12:16:51 --> 2. Prevent sharing to self
1 - 2026-08-12 12:16:51 --> 3. Check if company already shared to this user
1 - 2026-08-12 12:16:51 --> SELECT *
FROM "cmpacsmstr"
WHERE "cmp_id" = '136'
AND "uuid_acs_type" = 0
AND "uuid_aictly_acs" = '304'
AND "uuid_aictly_by" = '7'
1 - 2026-08-12 12:16:51 --> 4. Insert new share record
1 - 2026-08-12 12:16:51 --> INSERT INTO "cmpacsmstr" ("cmp_id", "uuid_acs_type", "uuid_aictly_acs", "uuid_aictly_by", "uuid_acs_datetime", "erp_acs_prof_id") VALUES ('136', 0, '304', '7', '2026-08-12 12:16:51', '94')
1 - 2026-08-12 12:16:51 --> Case: valid UUID => 403
1 - 2026-08-12 12:17:44 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-12 12:17:44 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-12 12:18:44 --> [LedgerCondensed][acc=11439] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-12 12:18:44 --> [LedgerCondensed][acc=11439] Q1-VoucherCount => 0  |  0.0013s
1 - 2026-08-12 12:18:44 --> [LedgerCondensed][acc=11439] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-12 12:18:44 --> [LedgerCondensed][acc=11439] Q3-MainLedger => rows=63  total=63  |  0.3836s
1 - 2026-08-12 12:18:44 --> [LedgerCondensed][acc=11439] Q4-OtherAccounts => 63 rows  |  0.0044s
1 - 2026-08-12 12:18:44 --> [LedgerCondensed][acc=11439] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0013s   (  0.3%)
   Q2-OpeningBalance           0.0003s   (  0.1%)
   Q3-MainLedger               0.3836s   ( 98.3%)
   Q4-OtherAccounts            0.0044s   (  1.1%)
   BuildRecords                0.0003s   (  0.1%)
   TOTAL                       0.3904s   (100%)
[LedgerCondensed][acc=11439] ── FUNCTION END ──
1 - 2026-08-12 12:18:50 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-12 12:18:50 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-12 12:18:52 --> [LedgerCondensed][acc=11440] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-12 12:18:52 --> [LedgerCondensed][acc=11440] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-08-12 12:18:52 --> [LedgerCondensed][acc=11440] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-12 12:18:53 --> [LedgerCondensed][acc=11440] Q3-MainLedger => rows=100  total=158  |  0.9472s
1 - 2026-08-12 12:18:53 --> [LedgerCondensed][acc=11440] Q4-OtherAccounts => 100 rows  |  0.0029s
1 - 2026-08-12 12:18:53 --> [LedgerCondensed][acc=11440] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  0.1%)
   Q2-OpeningBalance           0.0003s   (  0.0%)
   Q3-MainLedger               0.9472s   ( 99.5%)
   Q4-OtherAccounts            0.0029s   (  0.3%)
   BuildRecords                0.0004s   (  0.0%)
   TOTAL                       0.9523s   (100%)
[LedgerCondensed][acc=11440] ── FUNCTION END ──
1 - 2026-08-12 12:18:59 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-12 12:18:59 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-12 12:19:02 --> [LedgerCondensed][acc=11468] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-12 12:19:02 --> [LedgerCondensed][acc=11468] Q1-VoucherCount => 0  |  0.0012s
1 - 2026-08-12 12:19:02 --> [LedgerCondensed][acc=11468] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-12 12:19:02 --> [LedgerCondensed][acc=11468] Q3-MainLedger => rows=6  total=6  |  0.0398s
1 - 2026-08-12 12:19:02 --> [LedgerCondensed][acc=11468] Q4-OtherAccounts => 6 rows  |  0.002s
1 - 2026-08-12 12:19:02 --> [LedgerCondensed][acc=11468] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  2.8%)
   Q2-OpeningBalance           0.0003s   (  0.6%)
   Q3-MainLedger               0.0398s   ( 91.1%)
   Q4-OtherAccounts            0.0020s   (  4.5%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0437s   (100%)
[LedgerCondensed][acc=11468] ── FUNCTION END ──
1 - 2026-08-12 12:19:04 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-12 12:19:04 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-12 13:25:55 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-12 13:25:55 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-12 13:25:55 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-12 13:25:55 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
