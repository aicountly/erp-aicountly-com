<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-08-26 17:05:33 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:05:34 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:05:34 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:05:34 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:05:58 --> [LedgerCondensed][acc=11353] ── FUNCTION START ── from=2026-03-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-26 17:05:58 --> [LedgerCondensed][acc=11353] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-08-26 17:05:58 --> [LedgerCondensed][acc=11353] Q2-OpeningBalance => 90310.97  |  0.001s
1 - 2026-08-26 17:05:58 --> [LedgerCondensed][acc=11353] Q3-MainLedger => rows=3  total=3  |  0.0259s
1 - 2026-08-26 17:05:58 --> [LedgerCondensed][acc=11353] Q4-OtherAccounts => 3 rows  |  0.0019s
1 - 2026-08-26 17:05:58 --> [LedgerCondensed][acc=11353] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  3.0%)
   Q2-OpeningBalance           0.0010s   (  3.4%)
   Q3-MainLedger               0.0259s   ( 85.7%)
   Q4-OtherAccounts            0.0019s   (  6.4%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0302s   (100%)
[LedgerCondensed][acc=11353] ── FUNCTION END ──
1 - 2026-08-26 17:06:04 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:04 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:04 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:04 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:06 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:06 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:06 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:06 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:10 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:10 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:10 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:10 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:12 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:12 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:12 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:12 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:24 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:24 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:24 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:24 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:26 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:26 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:27 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:27 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:31 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:31 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:37 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:37 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:44 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:06:44 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:07:58 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:07:58 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:07:58 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:07:58 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:09:29 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:09:29 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:09:29 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:09:29 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:03 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:03 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:03 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:03 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:26 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:26 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:31 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:31 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:51 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:51 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:51 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:52 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:54 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:54 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:54 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:54 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:57 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:57 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:57 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:10:57 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:11:01 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:11:01 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:11:01 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
1 - 2026-08-26 17:11:01 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 210
AND "cmp_id" = 135
AND "itm_id_unit_id" IN ('75171_1928','75172_1928')
AND "hobo_id" = 196
GROUP BY "itm_id_unit_id"
