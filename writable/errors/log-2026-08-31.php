<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-08-31 11:40:22 --> [LedgerCondensed][acc=10029] ── FUNCTION START ── from=2026-08-01  to=2026-08-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-31 11:40:22 --> [LedgerCondensed][acc=10029] Q1-VoucherCount => 7  |  0.0016s
1 - 2026-08-31 11:40:22 --> [LedgerCondensed][acc=10029] Q2-OpeningBalance => 269519.19  |  0.0007s
1 - 2026-08-31 11:40:22 --> [LedgerCondensed][acc=10029] Q3-MainLedger => rows=2  total=2  |  0.018s
1 - 2026-08-31 11:40:22 --> [LedgerCondensed][acc=10029] Q4-OtherAccounts => 2 rows  |  0.0016s
1 - 2026-08-31 11:40:22 --> [LedgerCondensed][acc=10029] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0016s   (  6.9%)
   Q2-OpeningBalance           0.0007s   (  3.2%)
   Q3-MainLedger               0.0180s   ( 80.4%)
   Q4-OtherAccounts            0.0016s   (  7.0%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0224s   (100%)
[LedgerCondensed][acc=10029] ── FUNCTION END ──
1 - 2026-08-31 12:11:01 --> [LedgerCondensed][acc=10029] ── FUNCTION START ── from=2026-08-01  to=2026-08-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-31 12:11:01 --> [LedgerCondensed][acc=10029] Q1-VoucherCount => 7  |  0.0016s
1 - 2026-08-31 12:11:01 --> [LedgerCondensed][acc=10029] Q2-OpeningBalance => 269519.19  |  0.0008s
1 - 2026-08-31 12:11:01 --> [LedgerCondensed][acc=10029] Q3-MainLedger => rows=2  total=2  |  0.0173s
1 - 2026-08-31 12:11:01 --> [LedgerCondensed][acc=10029] Q4-OtherAccounts => 2 rows  |  0.0012s
1 - 2026-08-31 12:11:01 --> [LedgerCondensed][acc=10029] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0016s   (  7.5%)
   Q2-OpeningBalance           0.0008s   (  3.7%)
   Q3-MainLedger               0.0173s   ( 81.4%)
   Q4-OtherAccounts            0.0012s   (  5.6%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0213s   (100%)
[LedgerCondensed][acc=10029] ── FUNCTION END ──
1 - 2026-08-31 12:11:41 --> Company is gstin type check => 1
1 - 2026-08-31 12:11:41 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-31 12:11:41 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301603, Bill Sundry ID: 10188
1 - 2026-08-31 12:11:41 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-08-30","acc_txn_dr_cr":2,"acc_txn_amt":392.93000000000000682121026329696178436279296875,"acc_txn_fcy":0,"vch_txn_id":"301603","txn_id":1023036,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-31 12:11:41 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-31 12:11:41 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301603, Bill Sundry ID: 10189
1 - 2026-08-31 12:11:41 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-08-30","acc_txn_dr_cr":2,"acc_txn_amt":392.93000000000000682121026329696178436279296875,"acc_txn_fcy":0,"vch_txn_id":"301603","txn_id":1023037,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-31 12:11:43 --> [LedgerCondensed][acc=10029] ── FUNCTION START ── from=2026-08-01  to=2026-08-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-31 12:11:43 --> [LedgerCondensed][acc=10029] Q1-VoucherCount => 7  |  0.0013s
1 - 2026-08-31 12:11:43 --> [LedgerCondensed][acc=10029] Q2-OpeningBalance => 269519.19  |  0.0007s
1 - 2026-08-31 12:11:43 --> [LedgerCondensed][acc=10029] Q3-MainLedger => rows=2  total=2  |  0.0116s
1 - 2026-08-31 12:11:43 --> [LedgerCondensed][acc=10029] Q4-OtherAccounts => 2 rows  |  0.001s
1 - 2026-08-31 12:11:43 --> [LedgerCondensed][acc=10029] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0013s   (  8.8%)
   Q2-OpeningBalance           0.0007s   (  4.9%)
   Q3-MainLedger               0.0116s   ( 76.9%)
   Q4-OtherAccounts            0.0010s   (  6.7%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0151s   (100%)
[LedgerCondensed][acc=10029] ── FUNCTION END ──
1 - 2026-08-31 14:40:41 --> [LedgerCondensed][acc=8224] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-31 14:40:41 --> [LedgerCondensed][acc=8224] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-08-31 14:40:41 --> [LedgerCondensed][acc=8224] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-31 14:40:41 --> [LedgerCondensed][acc=8224] Q3-MainLedger => rows=1  total=1  |  0.0123s
1 - 2026-08-31 14:40:41 --> [LedgerCondensed][acc=8224] Q4-OtherAccounts => 1 rows  |  0.0013s
1 - 2026-08-31 14:40:41 --> [LedgerCondensed][acc=8224] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  7.4%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0123s   ( 79.4%)
   Q4-OtherAccounts            0.0013s   (  8.5%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0155s   (100%)
[LedgerCondensed][acc=8224] ── FUNCTION END ──
1 - 2026-08-31 14:41:51 --> [LedgerCondensed][acc=13824] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-31 14:41:51 --> [LedgerCondensed][acc=13824] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-08-31 14:41:51 --> [LedgerCondensed][acc=13824] Q2-OpeningBalance => 453.00  |  0.0002s
1 - 2026-08-31 14:41:51 --> [LedgerCondensed][acc=13824] Q3-MainLedger => rows=2  total=2  |  0.0088s
1 - 2026-08-31 14:41:51 --> [LedgerCondensed][acc=13824] Q4-OtherAccounts => 2 rows  |  0.0018s
1 - 2026-08-31 14:41:51 --> [LedgerCondensed][acc=13824] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.9%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0088s   ( 72.8%)
   Q4-OtherAccounts            0.0018s   ( 14.9%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0120s   (100%)
[LedgerCondensed][acc=13824] ── FUNCTION END ──
1 - 2026-08-31 14:42:33 --> [LedgerCondensed][acc=13824] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-31 14:42:33 --> [LedgerCondensed][acc=13824] Q1-VoucherCount => 0  |  0.001s
1 - 2026-08-31 14:42:33 --> [LedgerCondensed][acc=13824] Q2-OpeningBalance => 25888.00  |  0.0002s
1 - 2026-08-31 14:42:33 --> [LedgerCondensed][acc=13824] Q3-MainLedger => rows=27  total=27  |  0.0121s
1 - 2026-08-31 14:42:33 --> [LedgerCondensed][acc=13824] Q4-OtherAccounts => 27 rows  |  0.0054s
1 - 2026-08-31 14:42:33 --> [LedgerCondensed][acc=13824] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.0%)
   Q2-OpeningBalance           0.0002s   (  1.3%)
   Q3-MainLedger               0.0121s   ( 63.0%)
   Q4-OtherAccounts            0.0054s   ( 28.1%)
   BuildRecords                0.0001s   (  0.5%)
   TOTAL                       0.0192s   (100%)
[LedgerCondensed][acc=13824] ── FUNCTION END ──
1 - 2026-08-31 20:08:41 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 237
AND "cmp_id" = 149
AND "itm_id_unit_id" IN ('21673_3','21674_3','21675_3','21676_3','21677_3','21678_3','21679_17','21680_17','21681_19','21682_18','21683_3','39416_1992','39417_1992','39418_1992','39419_1992')
AND "hobo_id" = 200
GROUP BY "itm_id_unit_id"
1 - 2026-08-31 20:08:41 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 237
AND "cmp_id" = 149
AND "itm_id_unit_id" IN ('21673_3','21674_3','21675_3','21676_3','21677_3','21678_3','21679_17','21680_17','21681_19','21682_18','21683_3','39416_1992','39417_1992','39418_1992','39419_1992')
AND "hobo_id" = 200
GROUP BY "itm_id_unit_id"
1 - 2026-08-31 20:08:51 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 237
AND "cmp_id" = 149
AND "itm_id_unit_id" IN ('21673_3','21674_3','21675_3','21676_3','21677_3','21678_3','21679_17','21680_17','21681_19','21682_18','21683_3','39416_1992','39417_1992','39418_1992','39419_1992')
AND "hobo_id" = 200
GROUP BY "itm_id_unit_id"
1 - 2026-08-31 20:08:51 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 237
AND "cmp_id" = 149
AND "itm_id_unit_id" IN ('21673_3','21674_3','21675_3','21676_3','21677_3','21678_3','21679_17','21680_17','21681_19','21682_18','21683_3','39416_1992','39417_1992','39418_1992','39419_1992')
AND "hobo_id" = 200
GROUP BY "itm_id_unit_id"
1 - 2026-08-31 20:08:51 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 237
AND "cmp_id" = 149
AND "itm_id_unit_id" IN ('21673_3','21674_3','21675_3','21676_3','21677_3','21678_3','21679_17','21680_17','21681_19','21682_18','21683_3','39416_1992','39417_1992','39418_1992','39419_1992')
AND "hobo_id" = 200
GROUP BY "itm_id_unit_id"
1 - 2026-08-31 20:08:51 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 237
AND "cmp_id" = 149
AND "itm_id_unit_id" IN ('21673_3','21674_3','21675_3','21676_3','21677_3','21678_3','21679_17','21680_17','21681_19','21682_18','21683_3','39416_1992','39417_1992','39418_1992','39419_1992')
AND "hobo_id" = 200
GROUP BY "itm_id_unit_id"
1 - 2026-08-31 20:09:45 --> [LedgerCondensed][acc=11914] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-31 20:09:45 --> [LedgerCondensed][acc=11914] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-08-31 20:09:45 --> [LedgerCondensed][acc=11914] Q2-OpeningBalance => 200001.09  |  0.0003s
1 - 2026-08-31 20:09:45 --> [LedgerCondensed][acc=11914] Q3-MainLedger => rows=1  total=1  |  0.0116s
1 - 2026-08-31 20:09:45 --> [LedgerCondensed][acc=11914] Q4-OtherAccounts => 1 rows  |  0.0014s
1 - 2026-08-31 20:09:45 --> [LedgerCondensed][acc=11914] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.9%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0116s   ( 80.1%)
   Q4-OtherAccounts            0.0014s   (  9.6%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0145s   (100%)
[LedgerCondensed][acc=11914] ── FUNCTION END ──
1 - 2026-08-31 20:11:02 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 237
AND "cmp_id" = 149
AND "itm_id_unit_id" IN ('21673_3','21674_3','21675_3','21676_3','21677_3','21678_3','21679_17','21680_17','21681_19','21682_18','21683_3','39416_1992','39417_1992','39418_1992','39419_1992')
AND "hobo_id" = 200
GROUP BY "itm_id_unit_id"
1 - 2026-08-31 20:11:02 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 237
AND "cmp_id" = 149
AND "itm_id_unit_id" IN ('21673_3','21674_3','21675_3','21676_3','21677_3','21678_3','21679_17','21680_17','21681_19','21682_18','21683_3','39416_1992','39417_1992','39418_1992','39419_1992')
AND "hobo_id" = 200
GROUP BY "itm_id_unit_id"
1 - 2026-08-31 20:11:02 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 237
AND "cmp_id" = 149
AND "itm_id_unit_id" IN ('21673_3','21674_3','21675_3','21676_3','21677_3','21678_3','21679_17','21680_17','21681_19','21682_18','21683_3','39416_1992','39417_1992','39418_1992','39419_1992')
AND "hobo_id" = 200
GROUP BY "itm_id_unit_id"
1 - 2026-08-31 20:11:02 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 237
AND "cmp_id" = 149
AND "itm_id_unit_id" IN ('21673_3','21674_3','21675_3','21676_3','21677_3','21678_3','21679_17','21680_17','21681_19','21682_18','21683_3','39416_1992','39417_1992','39418_1992','39419_1992')
AND "hobo_id" = 200
GROUP BY "itm_id_unit_id"
