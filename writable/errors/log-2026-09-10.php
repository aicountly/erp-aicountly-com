<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-09-10 13:39:54 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:39:54 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:39:54 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:39:54 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:40:01 --> [LedgerCondensed][acc=11994] ── FUNCTION START ── from=2025-09-01  to=2025-09-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 13:40:01 --> [LedgerCondensed][acc=11994] Q1-VoucherCount => 797  |  0.0131s
1 - 2026-09-10 13:40:01 --> [LedgerCondensed][acc=11994] Q2-OpeningBalance => 253649.43  |  0.0015s
1 - 2026-09-10 13:40:01 --> [LedgerCondensed][acc=11994] Q3-MainLedger => rows=100  total=176  |  0.0178s
1 - 2026-09-10 13:40:02 --> [LedgerCondensed][acc=11994] Q4-OtherAccounts => 100 rows  |  0.0184s
1 - 2026-09-10 13:40:02 --> [LedgerCondensed][acc=11994] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0131s   ( 25.3%)
   Q2-OpeningBalance           0.0015s   (  3.0%)
   Q3-MainLedger               0.0178s   ( 34.3%)
   Q4-OtherAccounts            0.0184s   ( 35.6%)
   BuildRecords                0.0004s   (  0.7%)
   TOTAL                       0.0518s   (100%)
[LedgerCondensed][acc=11994] ── FUNCTION END ──
1 - 2026-09-10 13:40:05 --> [LedgerCondensed][acc=11994] ── FUNCTION START ── from=2025-09-01  to=2025-09-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 13:40:05 --> [LedgerCondensed][acc=11994] Q1-VoucherCount => 797  |  0.0027s
1 - 2026-09-10 13:40:05 --> [LedgerCondensed][acc=11994] Q2-OpeningBalance => 253649.43  |  0.0016s
1 - 2026-09-10 13:40:05 --> [LedgerCondensed][acc=11994] Q3-MainLedger => rows=76  total=176  |  0.0105s
1 - 2026-09-10 13:40:05 --> [LedgerCondensed][acc=11994] Q4-OtherAccounts => 76 rows  |  0.0059s
1 - 2026-09-10 13:40:05 --> [LedgerCondensed][acc=11994] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0027s   ( 12.6%)
   Q2-OpeningBalance           0.0016s   (  7.5%)
   Q3-MainLedger               0.0105s   ( 48.8%)
   Q4-OtherAccounts            0.0059s   ( 27.5%)
   BuildRecords                0.0003s   (  1.3%)
   TOTAL                       0.0216s   (100%)
[LedgerCondensed][acc=11994] ── FUNCTION END ──
1 - 2026-09-10 13:42:59 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:42:59 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:42:59 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:42:59 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:43:19 --> 1. Check if current user owns the company
1 - 2026-09-10 13:43:19 --> SELECT *
FROM "cmpacsmstr"
WHERE "cmp_id" = '150'
AND "uuid_acs_type" = 1
AND "uuid_aictly_by" = '7'
1 - 2026-09-10 13:43:19 --> 2. Prevent sharing to self
1 - 2026-09-10 13:43:19 --> 3. Check if company already shared to this user
1 - 2026-09-10 13:43:19 --> SELECT *
FROM "cmpacsmstr"
WHERE "cmp_id" = '150'
AND "uuid_acs_type" = 0
AND "uuid_aictly_acs" = '304'
AND "uuid_aictly_by" = '7'
1 - 2026-09-10 13:43:19 --> 4. Insert new share record
1 - 2026-09-10 13:43:19 --> INSERT INTO "cmpacsmstr" ("cmp_id", "uuid_acs_type", "uuid_aictly_acs", "uuid_aictly_by", "uuid_acs_datetime", "erp_acs_prof_id") VALUES ('150', 0, '304', '7', '2026-09-10 13:43:19', '96')
1 - 2026-09-10 13:43:19 --> Case: valid UUID => 405
1 - 2026-09-10 13:45:41 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:45:41 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:45:42 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:45:42 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:45:42 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:45:42 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:47:38 --> [LedgerCondensed][acc=11979] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 13:47:38 --> [LedgerCondensed][acc=11979] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-10 13:47:38 --> [LedgerCondensed][acc=11979] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-10 13:47:38 --> [LedgerCondensed][acc=11979] Q3-MainLedger => rows=100  total=755  |  0.0164s
1 - 2026-09-10 13:47:38 --> [LedgerCondensed][acc=11979] Q4-OtherAccounts => 100 rows  |  0.0066s
1 - 2026-09-10 13:47:38 --> [LedgerCondensed][acc=11979] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  4.1%)
   Q2-OpeningBalance           0.0003s   (  1.0%)
   Q3-MainLedger               0.0164s   ( 65.1%)
   Q4-OtherAccounts            0.0066s   ( 26.3%)
   BuildRecords                0.0004s   (  1.6%)
   TOTAL                       0.0252s   (100%)
[LedgerCondensed][acc=11979] ── FUNCTION END ──
1 - 2026-09-10 13:47:42 --> [LedgerCondensed][acc=11979] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 13:47:42 --> [LedgerCondensed][acc=11979] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 13:47:42 --> [LedgerCondensed][acc=11979] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-10 13:47:42 --> [LedgerCondensed][acc=11979] Q3-MainLedger => rows=100  total=755  |  0.0112s
1 - 2026-09-10 13:47:42 --> [LedgerCondensed][acc=11979] Q4-OtherAccounts => 100 rows  |  0.0059s
1 - 2026-09-10 13:47:42 --> [LedgerCondensed][acc=11979] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.9%)
   Q2-OpeningBalance           0.0003s   (  1.4%)
   Q3-MainLedger               0.0112s   ( 58.1%)
   Q4-OtherAccounts            0.0059s   ( 31.0%)
   BuildRecords                0.0005s   (  2.5%)
   TOTAL                       0.0192s   (100%)
[LedgerCondensed][acc=11979] ── FUNCTION END ──
1 - 2026-09-10 13:47:45 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 13:47:45 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 14:42:47 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 14:42:47 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 14:42:47 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 14:42:47 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 14:42:54 --> [LedgerCondensed][acc=11994] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 14:42:54 --> [LedgerCondensed][acc=11994] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-10 14:42:54 --> [LedgerCondensed][acc=11994] Q2-OpeningBalance => 250337.82  |  0.0003s
1 - 2026-09-10 14:42:54 --> [LedgerCondensed][acc=11994] Q3-MainLedger => rows=100  total=973  |  0.0158s
1 - 2026-09-10 14:42:54 --> [LedgerCondensed][acc=11994] Q4-OtherAccounts => 100 rows  |  0.0058s
1 - 2026-09-10 14:42:54 --> [LedgerCondensed][acc=11994] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  4.4%)
   Q2-OpeningBalance           0.0003s   (  1.2%)
   Q3-MainLedger               0.0158s   ( 66.5%)
   Q4-OtherAccounts            0.0058s   ( 24.4%)
   BuildRecords                0.0005s   (  1.9%)
   TOTAL                       0.0238s   (100%)
[LedgerCondensed][acc=11994] ── FUNCTION END ──
1 - 2026-09-10 14:42:59 --> [LedgerCondensed][acc=11994] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 14:42:59 --> [LedgerCondensed][acc=11994] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 14:42:59 --> [LedgerCondensed][acc=11994] Q2-OpeningBalance => 250337.82  |  0.0003s
1 - 2026-09-10 14:42:59 --> [LedgerCondensed][acc=11994] Q3-MainLedger => rows=73  total=973  |  0.0113s
1 - 2026-09-10 14:42:59 --> [LedgerCondensed][acc=11994] Q4-OtherAccounts => 73 rows  |  0.0047s
1 - 2026-09-10 14:42:59 --> [LedgerCondensed][acc=11994] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.8%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0113s   ( 63.3%)
   Q4-OtherAccounts            0.0047s   ( 26.4%)
   BuildRecords                0.0003s   (  1.7%)
   TOTAL                       0.0178s   (100%)
[LedgerCondensed][acc=11994] ── FUNCTION END ──
1 - 2026-09-10 15:02:56 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:02:56 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:02:56 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:02:56 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:03:17 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:03:17 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:03:17 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:03:17 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:05:08 --> [LedgerCondensed][acc=11975] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:05:08 --> [LedgerCondensed][acc=11975] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-10 15:05:08 --> [LedgerCondensed][acc=11975] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-10 15:05:08 --> [LedgerCondensed][acc=11975] Q3-MainLedger => rows=100  total=663  |  0.0145s
1 - 2026-09-10 15:05:08 --> [LedgerCondensed][acc=11975] Q4-OtherAccounts => 100 rows  |  0.0063s
1 - 2026-09-10 15:05:08 --> [LedgerCondensed][acc=11975] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  4.7%)
   Q2-OpeningBalance           0.0003s   (  1.4%)
   Q3-MainLedger               0.0145s   ( 62.8%)
   Q4-OtherAccounts            0.0063s   ( 27.3%)
   BuildRecords                0.0004s   (  1.8%)
   TOTAL                       0.0231s   (100%)
[LedgerCondensed][acc=11975] ── FUNCTION END ──
1 - 2026-09-10 15:05:14 --> [LedgerCondensed][acc=11975] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:05:14 --> [LedgerCondensed][acc=11975] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-10 15:05:14 --> [LedgerCondensed][acc=11975] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-10 15:05:14 --> [LedgerCondensed][acc=11975] Q3-MainLedger => rows=63  total=663  |  0.0106s
1 - 2026-09-10 15:05:14 --> [LedgerCondensed][acc=11975] Q4-OtherAccounts => 63 rows  |  0.0055s
1 - 2026-09-10 15:05:14 --> [LedgerCondensed][acc=11975] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.4%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0106s   ( 59.0%)
   Q4-OtherAccounts            0.0055s   ( 30.5%)
   BuildRecords                0.0002s   (  1.2%)
   TOTAL                       0.0180s   (100%)
[LedgerCondensed][acc=11975] ── FUNCTION END ──
1 - 2026-09-10 15:05:18 --> [LedgerCondensed][acc=11975] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:05:18 --> [LedgerCondensed][acc=11975] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-10 15:05:18 --> [LedgerCondensed][acc=11975] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-10 15:05:18 --> [LedgerCondensed][acc=11975] Q3-MainLedger => rows=100  total=663  |  0.0101s
1 - 2026-09-10 15:05:18 --> [LedgerCondensed][acc=11975] Q4-OtherAccounts => 100 rows  |  0.0053s
1 - 2026-09-10 15:05:18 --> [LedgerCondensed][acc=11975] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  4.9%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0101s   ( 58.8%)
   Q4-OtherAccounts            0.0053s   ( 30.9%)
   BuildRecords                0.0003s   (  1.9%)
   TOTAL                       0.0172s   (100%)
[LedgerCondensed][acc=11975] ── FUNCTION END ──
1 - 2026-09-10 15:05:24 --> [LedgerCondensed][acc=11975] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:05:24 --> [LedgerCondensed][acc=11975] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-10 15:05:24 --> [LedgerCondensed][acc=11975] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-10 15:05:24 --> [LedgerCondensed][acc=11975] Q3-MainLedger => rows=100  total=663  |  0.0105s
1 - 2026-09-10 15:05:24 --> [LedgerCondensed][acc=11975] Q4-OtherAccounts => 100 rows  |  0.0055s
1 - 2026-09-10 15:05:24 --> [LedgerCondensed][acc=11975] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  4.6%)
   Q2-OpeningBalance           0.0003s   (  1.4%)
   Q3-MainLedger               0.0105s   ( 59.2%)
   Q4-OtherAccounts            0.0055s   ( 31.0%)
   BuildRecords                0.0003s   (  1.9%)
   TOTAL                       0.0177s   (100%)
[LedgerCondensed][acc=11975] ── FUNCTION END ──
1 - 2026-09-10 15:05:35 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:05:35 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:12:22 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:12:22 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:12:22 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:12:22 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:12:36 --> [LedgerCondensed][acc=11993] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:12:36 --> [LedgerCondensed][acc=11993] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 15:12:36 --> [LedgerCondensed][acc=11993] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-10 15:12:36 --> [LedgerCondensed][acc=11993] Q3-MainLedger => rows=100  total=411  |  0.013s
1 - 2026-09-10 15:12:36 --> [LedgerCondensed][acc=11993] Q4-OtherAccounts => 100 rows  |  0.0056s
1 - 2026-09-10 15:12:36 --> [LedgerCondensed][acc=11993] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.6%)
   Q2-OpeningBalance           0.0002s   (  1.2%)
   Q3-MainLedger               0.0130s   ( 63.3%)
   Q4-OtherAccounts            0.0056s   ( 27.5%)
   BuildRecords                0.0003s   (  1.4%)
   TOTAL                       0.0205s   (100%)
[LedgerCondensed][acc=11993] ── FUNCTION END ──
1 - 2026-09-10 15:12:41 --> [LedgerCondensed][acc=11993] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:12:41 --> [LedgerCondensed][acc=11993] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-10 15:12:41 --> [LedgerCondensed][acc=11993] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-10 15:12:41 --> [LedgerCondensed][acc=11993] Q3-MainLedger => rows=100  total=411  |  0.0097s
1 - 2026-09-10 15:12:41 --> [LedgerCondensed][acc=11993] Q4-OtherAccounts => 100 rows  |  0.0051s
1 - 2026-09-10 15:12:41 --> [LedgerCondensed][acc=11993] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.7%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0097s   ( 57.6%)
   Q4-OtherAccounts            0.0051s   ( 30.3%)
   BuildRecords                0.0004s   (  2.4%)
   TOTAL                       0.0169s   (100%)
[LedgerCondensed][acc=11993] ── FUNCTION END ──
1 - 2026-09-10 15:12:46 --> [LedgerCondensed][acc=11993] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:12:46 --> [LedgerCondensed][acc=11993] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 15:12:46 --> [LedgerCondensed][acc=11993] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-10 15:12:46 --> [LedgerCondensed][acc=11993] Q3-MainLedger => rows=11  total=411  |  0.0098s
1 - 2026-09-10 15:12:46 --> [LedgerCondensed][acc=11993] Q4-OtherAccounts => 11 rows  |  0.0016s
1 - 2026-09-10 15:12:46 --> [LedgerCondensed][acc=11993] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.8%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0098s   ( 75.8%)
   Q4-OtherAccounts            0.0016s   ( 12.1%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0130s   (100%)
[LedgerCondensed][acc=11993] ── FUNCTION END ──
1 - 2026-09-10 15:13:32 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:13:32 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:13:32 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:13:32 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:38:50 --> [LedgerCondensed][acc=12028] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:38:50 --> [LedgerCondensed][acc=12028] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-10 15:38:50 --> [LedgerCondensed][acc=12028] Q2-OpeningBalance => 36337.00  |  0.0002s
1 - 2026-09-10 15:38:50 --> [LedgerCondensed][acc=12028] Q3-MainLedger => rows=100  total=892  |  0.0151s
1 - 2026-09-10 15:38:50 --> [LedgerCondensed][acc=12028] Q4-OtherAccounts => 100 rows  |  0.0062s
1 - 2026-09-10 15:38:50 --> [LedgerCondensed][acc=12028] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  4.1%)
   Q2-OpeningBalance           0.0002s   (  1.0%)
   Q3-MainLedger               0.0151s   ( 64.7%)
   Q4-OtherAccounts            0.0062s   ( 26.5%)
   BuildRecords                0.0004s   (  1.7%)
   TOTAL                       0.0233s   (100%)
[LedgerCondensed][acc=12028] ── FUNCTION END ──
1 - 2026-09-10 15:38:58 --> [LedgerCondensed][acc=12028] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:38:58 --> [LedgerCondensed][acc=12028] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 15:38:58 --> [LedgerCondensed][acc=12028] Q2-OpeningBalance => 36337.00  |  0.0003s
1 - 2026-09-10 15:38:58 --> [LedgerCondensed][acc=12028] Q3-MainLedger => rows=92  total=892  |  0.0117s
1 - 2026-09-10 15:38:58 --> [LedgerCondensed][acc=12028] Q4-OtherAccounts => 92 rows  |  0.0062s
1 - 2026-09-10 15:38:58 --> [LedgerCondensed][acc=12028] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.4%)
   Q2-OpeningBalance           0.0003s   (  1.3%)
   Q3-MainLedger               0.0117s   ( 59.1%)
   Q4-OtherAccounts            0.0062s   ( 31.4%)
   BuildRecords                0.0004s   (  1.8%)
   TOTAL                       0.0198s   (100%)
[LedgerCondensed][acc=12028] ── FUNCTION END ──
1 - 2026-09-10 15:39:37 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:39:37 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:39:37 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:39:37 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:39:53 --> [LedgerCondensed][acc=11989] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:39:53 --> [LedgerCondensed][acc=11989] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 15:39:53 --> [LedgerCondensed][acc=11989] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-10 15:39:53 --> [LedgerCondensed][acc=11989] Q3-MainLedger => rows=100  total=503  |  0.0116s
1 - 2026-09-10 15:39:53 --> [LedgerCondensed][acc=11989] Q4-OtherAccounts => 100 rows  |  0.0057s
1 - 2026-09-10 15:39:53 --> [LedgerCondensed][acc=11989] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.8%)
   Q2-OpeningBalance           0.0003s   (  1.3%)
   Q3-MainLedger               0.0116s   ( 60.1%)
   Q4-OtherAccounts            0.0057s   ( 29.4%)
   BuildRecords                0.0004s   (  2.1%)
   TOTAL                       0.0193s   (100%)
[LedgerCondensed][acc=11989] ── FUNCTION END ──
1 - 2026-09-10 15:40:02 --> [LedgerCondensed][acc=11989] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:40:02 --> [LedgerCondensed][acc=11989] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 15:40:02 --> [LedgerCondensed][acc=11989] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-10 15:40:02 --> [LedgerCondensed][acc=11989] Q3-MainLedger => rows=100  total=503  |  0.01s
1 - 2026-09-10 15:40:02 --> [LedgerCondensed][acc=11989] Q4-OtherAccounts => 100 rows  |  0.0055s
1 - 2026-09-10 15:40:02 --> [LedgerCondensed][acc=11989] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.1%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0100s   ( 57.5%)
   Q4-OtherAccounts            0.0055s   ( 31.7%)
   BuildRecords                0.0003s   (  1.9%)
   TOTAL                       0.0174s   (100%)
[LedgerCondensed][acc=11989] ── FUNCTION END ──
1 - 2026-09-10 15:40:06 --> [LedgerCondensed][acc=11989] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:40:06 --> [LedgerCondensed][acc=11989] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-10 15:40:06 --> [LedgerCondensed][acc=11989] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-10 15:40:06 --> [LedgerCondensed][acc=11989] Q3-MainLedger => rows=3  total=503  |  0.0099s
1 - 2026-09-10 15:40:06 --> [LedgerCondensed][acc=11989] Q4-OtherAccounts => 3 rows  |  0.0009s
1 - 2026-09-10 15:40:06 --> [LedgerCondensed][acc=11989] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.7%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0099s   ( 79.5%)
   Q4-OtherAccounts            0.0009s   (  7.6%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0124s   (100%)
[LedgerCondensed][acc=11989] ── FUNCTION END ──
1 - 2026-09-10 15:40:08 --> [LedgerCondensed][acc=11989] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:40:08 --> [LedgerCondensed][acc=11989] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 15:40:08 --> [LedgerCondensed][acc=11989] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-10 15:40:09 --> [LedgerCondensed][acc=11989] Q3-MainLedger => rows=100  total=503  |  0.0101s
1 - 2026-09-10 15:40:09 --> [LedgerCondensed][acc=11989] Q4-OtherAccounts => 100 rows  |  0.0068s
1 - 2026-09-10 15:40:09 --> [LedgerCondensed][acc=11989] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.7%)
   Q2-OpeningBalance           0.0003s   (  1.4%)
   Q3-MainLedger               0.0101s   ( 53.7%)
   Q4-OtherAccounts            0.0068s   ( 36.1%)
   BuildRecords                0.0004s   (  2.2%)
   TOTAL                       0.0188s   (100%)
[LedgerCondensed][acc=11989] ── FUNCTION END ──
1 - 2026-09-10 15:40:15 --> [LedgerCondensed][acc=12030] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:40:15 --> [LedgerCondensed][acc=12030] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 15:40:15 --> [LedgerCondensed][acc=12030] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-10 15:40:15 --> [LedgerCondensed][acc=12030] Q3-MainLedger => rows=11  total=11  |  0.0093s
1 - 2026-09-10 15:40:15 --> [LedgerCondensed][acc=12030] Q4-OtherAccounts => 10 rows  |  0.0019s
1 - 2026-09-10 15:40:15 --> [LedgerCondensed][acc=12030] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.7%)
   Q2-OpeningBalance           0.0002s   (  1.9%)
   Q3-MainLedger               0.0093s   ( 73.2%)
   Q4-OtherAccounts            0.0019s   ( 15.1%)
   BuildRecords                0.0000s   (  0.4%)
   TOTAL                       0.0128s   (100%)
[LedgerCondensed][acc=12030] ── FUNCTION END ──
1 - 2026-09-10 15:40:18 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:40:18 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:40:18 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:40:18 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:40:23 --> [LedgerCondensed][acc=12028] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:40:23 --> [LedgerCondensed][acc=12028] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-10 15:40:23 --> [LedgerCondensed][acc=12028] Q2-OpeningBalance => 36337.00  |  0.0002s
1 - 2026-09-10 15:40:23 --> [LedgerCondensed][acc=12028] Q3-MainLedger => rows=100  total=892  |  0.0112s
1 - 2026-09-10 15:40:23 --> [LedgerCondensed][acc=12028] Q4-OtherAccounts => 100 rows  |  0.0052s
1 - 2026-09-10 15:40:23 --> [LedgerCondensed][acc=12028] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  4.4%)
   Q2-OpeningBalance           0.0002s   (  1.3%)
   Q3-MainLedger               0.0112s   ( 61.8%)
   Q4-OtherAccounts            0.0052s   ( 28.8%)
   BuildRecords                0.0003s   (  1.7%)
   TOTAL                       0.0181s   (100%)
[LedgerCondensed][acc=12028] ── FUNCTION END ──
1 - 2026-09-10 15:40:29 --> [LedgerCondensed][acc=12028] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:40:29 --> [LedgerCondensed][acc=12028] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-10 15:40:29 --> [LedgerCondensed][acc=12028] Q2-OpeningBalance => 36337.00  |  0.0003s
1 - 2026-09-10 15:40:29 --> [LedgerCondensed][acc=12028] Q3-MainLedger => rows=100  total=892  |  0.0118s
1 - 2026-09-10 15:40:29 --> [LedgerCondensed][acc=12028] Q4-OtherAccounts => 98 rows  |  0.0056s
1 - 2026-09-10 15:40:29 --> [LedgerCondensed][acc=12028] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  4.3%)
   Q2-OpeningBalance           0.0003s   (  1.4%)
   Q3-MainLedger               0.0118s   ( 61.4%)
   Q4-OtherAccounts            0.0056s   ( 28.9%)
   BuildRecords                0.0004s   (  2.0%)
   TOTAL                       0.0192s   (100%)
[LedgerCondensed][acc=12028] ── FUNCTION END ──
1 - 2026-09-10 15:40:38 --> [LedgerCondensed][acc=12028] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:40:38 --> [LedgerCondensed][acc=12028] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-10 15:40:38 --> [LedgerCondensed][acc=12028] Q2-OpeningBalance => 36337.00  |  0.0003s
1 - 2026-09-10 15:40:38 --> [LedgerCondensed][acc=12028] Q3-MainLedger => rows=100  total=892  |  0.0116s
1 - 2026-09-10 15:40:38 --> [LedgerCondensed][acc=12028] Q4-OtherAccounts => 100 rows  |  0.0053s
1 - 2026-09-10 15:40:38 --> [LedgerCondensed][acc=12028] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  5.6%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0116s   ( 60.8%)
   Q4-OtherAccounts            0.0053s   ( 27.6%)
   BuildRecords                0.0004s   (  2.0%)
   TOTAL                       0.0191s   (100%)
[LedgerCondensed][acc=12028] ── FUNCTION END ──
1 - 2026-09-10 15:40:47 --> [LedgerCondensed][acc=12028] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:40:47 --> [LedgerCondensed][acc=12028] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 15:40:47 --> [LedgerCondensed][acc=12028] Q2-OpeningBalance => 36337.00  |  0.0003s
1 - 2026-09-10 15:40:47 --> [LedgerCondensed][acc=12028] Q3-MainLedger => rows=100  total=892  |  0.0117s
1 - 2026-09-10 15:40:47 --> [LedgerCondensed][acc=12028] Q4-OtherAccounts => 100 rows  |  0.0053s
1 - 2026-09-10 15:40:47 --> [LedgerCondensed][acc=12028] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.6%)
   Q2-OpeningBalance           0.0003s   (  1.4%)
   Q3-MainLedger               0.0117s   ( 61.8%)
   Q4-OtherAccounts            0.0053s   ( 28.1%)
   BuildRecords                0.0004s   (  2.1%)
   TOTAL                       0.0189s   (100%)
[LedgerCondensed][acc=12028] ── FUNCTION END ──
1 - 2026-09-10 15:40:55 --> [LedgerCondensed][acc=12028] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:40:55 --> [LedgerCondensed][acc=12028] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 15:40:55 --> [LedgerCondensed][acc=12028] Q2-OpeningBalance => 36337.00  |  0.0003s
1 - 2026-09-10 15:40:55 --> [LedgerCondensed][acc=12028] Q3-MainLedger => rows=100  total=892  |  0.012s
1 - 2026-09-10 15:40:55 --> [LedgerCondensed][acc=12028] Q4-OtherAccounts => 100 rows  |  0.0057s
1 - 2026-09-10 15:40:55 --> [LedgerCondensed][acc=12028] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.3%)
   Q2-OpeningBalance           0.0003s   (  1.4%)
   Q3-MainLedger               0.0120s   ( 61.2%)
   Q4-OtherAccounts            0.0057s   ( 29.2%)
   BuildRecords                0.0004s   (  1.9%)
   TOTAL                       0.0197s   (100%)
[LedgerCondensed][acc=12028] ── FUNCTION END ──
1 - 2026-09-10 15:41:09 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:41:09 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:41:09 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:41:09 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:41:15 --> [LedgerCondensed][acc=12030] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:41:15 --> [LedgerCondensed][acc=12030] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-10 15:41:15 --> [LedgerCondensed][acc=12030] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-10 15:41:15 --> [LedgerCondensed][acc=12030] Q3-MainLedger => rows=11  total=11  |  0.0093s
1 - 2026-09-10 15:41:15 --> [LedgerCondensed][acc=12030] Q4-OtherAccounts => 10 rows  |  0.0017s
1 - 2026-09-10 15:41:15 --> [LedgerCondensed][acc=12030] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  8.4%)
   Q2-OpeningBalance           0.0002s   (  1.9%)
   Q3-MainLedger               0.0093s   ( 72.8%)
   Q4-OtherAccounts            0.0017s   ( 13.0%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0128s   (100%)
[LedgerCondensed][acc=12030] ── FUNCTION END ──
1 - 2026-09-10 15:41:19 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:41:19 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:41:19 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:41:19 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:41:50 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:41:50 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:45:58 --> [LedgerCondensed][acc=11979] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:45:58 --> [LedgerCondensed][acc=11979] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 15:45:58 --> [LedgerCondensed][acc=11979] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-10 15:45:58 --> [LedgerCondensed][acc=11979] Q3-MainLedger => rows=100  total=755  |  0.0133s
1 - 2026-09-10 15:45:58 --> [LedgerCondensed][acc=11979] Q4-OtherAccounts => 100 rows  |  0.0059s
1 - 2026-09-10 15:45:58 --> [LedgerCondensed][acc=11979] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.4%)
   Q2-OpeningBalance           0.0003s   (  1.3%)
   Q3-MainLedger               0.0133s   ( 62.6%)
   Q4-OtherAccounts            0.0059s   ( 27.9%)
   BuildRecords                0.0004s   (  1.8%)
   TOTAL                       0.0212s   (100%)
[LedgerCondensed][acc=11979] ── FUNCTION END ──
1 - 2026-09-10 15:46:13 --> [LedgerCondensed][acc=11979] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 15:46:13 --> [LedgerCondensed][acc=11979] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 15:46:13 --> [LedgerCondensed][acc=11979] Q2-OpeningBalance => 0.00  |  0.0004s
1 - 2026-09-10 15:46:13 --> [LedgerCondensed][acc=11979] Q3-MainLedger => rows=100  total=755  |  0.0107s
1 - 2026-09-10 15:46:13 --> [LedgerCondensed][acc=11979] Q4-OtherAccounts => 100 rows  |  0.0056s
1 - 2026-09-10 15:46:13 --> [LedgerCondensed][acc=11979] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.8%)
   Q2-OpeningBalance           0.0004s   (  2.0%)
   Q3-MainLedger               0.0107s   ( 58.4%)
   Q4-OtherAccounts            0.0056s   ( 30.7%)
   BuildRecords                0.0004s   (  2.0%)
   TOTAL                       0.0183s   (100%)
[LedgerCondensed][acc=11979] ── FUNCTION END ──
1 - 2026-09-10 15:46:15 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 15:46:15 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 16:10:17 --> [LedgerCondensed][acc=11994] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 16:10:17 --> [LedgerCondensed][acc=11994] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-10 16:10:17 --> [LedgerCondensed][acc=11994] Q2-OpeningBalance => 250337.82  |  0.0003s
1 - 2026-09-10 16:10:17 --> [LedgerCondensed][acc=11994] Q3-MainLedger => rows=100  total=973  |  0.0148s
1 - 2026-09-10 16:10:17 --> [LedgerCondensed][acc=11994] Q4-OtherAccounts => 100 rows  |  0.0062s
1 - 2026-09-10 16:10:17 --> [LedgerCondensed][acc=11994] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  4.5%)
   Q2-OpeningBalance           0.0003s   (  1.3%)
   Q3-MainLedger               0.0148s   ( 63.8%)
   Q4-OtherAccounts            0.0062s   ( 26.7%)
   BuildRecords                0.0004s   (  1.6%)
   TOTAL                       0.0231s   (100%)
[LedgerCondensed][acc=11994] ── FUNCTION END ──
1 - 2026-09-10 16:10:22 --> [LedgerCondensed][acc=11994] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 16:10:22 --> [LedgerCondensed][acc=11994] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 16:10:22 --> [LedgerCondensed][acc=11994] Q2-OpeningBalance => 250337.82  |  0.0002s
1 - 2026-09-10 16:10:22 --> [LedgerCondensed][acc=11994] Q3-MainLedger => rows=100  total=973  |  0.0116s
1 - 2026-09-10 16:10:22 --> [LedgerCondensed][acc=11994] Q4-OtherAccounts => 100 rows  |  0.005s
1 - 2026-09-10 16:10:22 --> [LedgerCondensed][acc=11994] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.0%)
   Q2-OpeningBalance           0.0002s   (  1.3%)
   Q3-MainLedger               0.0116s   ( 62.9%)
   Q4-OtherAccounts            0.0050s   ( 27.3%)
   BuildRecords                0.0003s   (  1.6%)
   TOTAL                       0.0185s   (100%)
[LedgerCondensed][acc=11994] ── FUNCTION END ──
1 - 2026-09-10 16:10:25 --> [LedgerCondensed][acc=11994] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-10 16:10:25 --> [LedgerCondensed][acc=11994] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-10 16:10:25 --> [LedgerCondensed][acc=11994] Q2-OpeningBalance => 250337.82  |  0.0003s
1 - 2026-09-10 16:10:25 --> [LedgerCondensed][acc=11994] Q3-MainLedger => rows=73  total=973  |  0.0112s
1 - 2026-09-10 16:10:25 --> [LedgerCondensed][acc=11994] Q4-OtherAccounts => 73 rows  |  0.0047s
1 - 2026-09-10 16:10:25 --> [LedgerCondensed][acc=11994] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.3%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0112s   ( 63.0%)
   Q4-OtherAccounts            0.0047s   ( 26.3%)
   BuildRecords                0.0003s   (  1.6%)
   TOTAL                       0.0178s   (100%)
[LedgerCondensed][acc=11994] ── FUNCTION END ──
1 - 2026-09-10 16:12:23 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 16:12:23 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 16:12:55 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 16:12:55 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 16:12:55 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-09-10 16:12:55 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 239
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
