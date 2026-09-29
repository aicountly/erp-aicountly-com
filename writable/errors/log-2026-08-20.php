<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-08-20 12:47:53 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-20 12:47:53 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301599, Bill Sundry ID: 10188
1 - 2026-08-20 12:47:53 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-08-20","acc_txn_dr_cr":2,"acc_txn_amt":568.8799999999999954525264911353588104248046875,"acc_txn_fcy":0,"vch_txn_id":301599,"txn_id":1022959,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-20 12:47:53 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-20 12:47:53 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301599, Bill Sundry ID: 10189
1 - 2026-08-20 12:47:53 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-08-20","acc_txn_dr_cr":2,"acc_txn_amt":568.8799999999999954525264911353588104248046875,"acc_txn_fcy":0,"vch_txn_id":301599,"txn_id":1022960,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-20 12:48:31 --> [LedgerCondensed][acc=10357] ── FUNCTION START ── from=2026-08-01  to=2026-08-20  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-20 12:48:31 --> [LedgerCondensed][acc=10357] Q1-VoucherCount => 12  |  0.0026s
1 - 2026-08-20 12:48:31 --> [LedgerCondensed][acc=10357] Q2-OpeningBalance => 650328.54  |  0.001s
1 - 2026-08-20 12:48:31 --> [LedgerCondensed][acc=10357] Q3-MainLedger => rows=2  total=2  |  0.0121s
1 - 2026-08-20 12:48:31 --> [LedgerCondensed][acc=10357] Q4-OtherAccounts => 2 rows  |  0.0022s
1 - 2026-08-20 12:48:31 --> [LedgerCondensed][acc=10357] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0026s   ( 14.0%)
   Q2-OpeningBalance           0.0010s   (  5.3%)
   Q3-MainLedger               0.0121s   ( 66.0%)
   Q4-OtherAccounts            0.0022s   ( 12.1%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0183s   (100%)
[LedgerCondensed][acc=10357] ── FUNCTION END ──
1 - 2026-08-20 16:35:22 --> [LedgerCondensed][acc=2912] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-20 16:35:22 --> [LedgerCondensed][acc=2912] Q1-VoucherCount => 0  |  0.0012s
1 - 2026-08-20 16:35:22 --> [LedgerCondensed][acc=2912] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-20 16:35:22 --> [LedgerCondensed][acc=2912] Q3-MainLedger => rows=5  total=5  |  0.0121s
1 - 2026-08-20 16:35:22 --> [LedgerCondensed][acc=2912] Q4-OtherAccounts => 0 rows  |  0.0012s
1 - 2026-08-20 16:35:22 --> [LedgerCondensed][acc=2912] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  7.6%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0121s   ( 79.8%)
   Q4-OtherAccounts            0.0012s   (  8.1%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0151s   (100%)
[LedgerCondensed][acc=2912] ── FUNCTION END ──
1 - 2026-08-20 16:35:30 --> [LedgerCondensed][acc=2912] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-20 16:35:30 --> [LedgerCondensed][acc=2912] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-08-20 16:35:30 --> [LedgerCondensed][acc=2912] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-20 16:35:31 --> [LedgerCondensed][acc=2912] Q3-MainLedger => rows=5  total=5  |  0.0096s
1 - 2026-08-20 16:35:31 --> [LedgerCondensed][acc=2912] Q4-OtherAccounts => 0 rows  |  0.0011s
1 - 2026-08-20 16:35:31 --> [LedgerCondensed][acc=2912] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.3%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0096s   ( 78.5%)
   Q4-OtherAccounts            0.0011s   (  8.7%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0122s   (100%)
[LedgerCondensed][acc=2912] ── FUNCTION END ──
1 - 2026-08-20 17:41:55 --> [LedgerCondensed][acc=2655] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-20 17:41:55 --> [LedgerCondensed][acc=2655] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-08-20 17:41:55 --> [LedgerCondensed][acc=2655] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-20 17:41:55 --> [LedgerCondensed][acc=2655] Q3-MainLedger => rows=12  total=12  |  0.0134s
1 - 2026-08-20 17:41:55 --> [LedgerCondensed][acc=2655] Q4-OtherAccounts => 0 rows  |  0.0029s
1 - 2026-08-20 17:41:55 --> [LedgerCondensed][acc=2655] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.8%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0134s   ( 74.7%)
   Q4-OtherAccounts            0.0029s   ( 16.1%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0179s   (100%)
[LedgerCondensed][acc=2655] ── FUNCTION END ──
1 - 2026-08-20 17:42:06 --> [LedgerCondensed][acc=2655] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-20 17:42:06 --> [LedgerCondensed][acc=2655] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-08-20 17:42:06 --> [LedgerCondensed][acc=2655] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-20 17:42:06 --> [LedgerCondensed][acc=2655] Q3-MainLedger => rows=12  total=12  |  0.0094s
1 - 2026-08-20 17:42:06 --> [LedgerCondensed][acc=2655] Q4-OtherAccounts => 0 rows  |  0.0021s
1 - 2026-08-20 17:42:06 --> [LedgerCondensed][acc=2655] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.9%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0094s   ( 71.8%)
   Q4-OtherAccounts            0.0021s   ( 15.9%)
   BuildRecords                0.0001s   (  0.6%)
   TOTAL                       0.0131s   (100%)
[LedgerCondensed][acc=2655] ── FUNCTION END ──
1 - 2026-08-20 17:42:22 --> [LedgerCondensed][acc=2651] ── FUNCTION START ── from=2024-04-01  to=2024-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-20 17:42:22 --> [LedgerCondensed][acc=2651] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-08-20 17:42:22 --> [LedgerCondensed][acc=2651] Q2-OpeningBalance => 7283671.78  |  0.0002s
1 - 2026-08-20 17:42:22 --> [LedgerCondensed][acc=2651] Q3-MainLedger => rows=54  total=54  |  0.0091s
1 - 2026-08-20 17:42:22 --> [LedgerCondensed][acc=2651] Q4-OtherAccounts => 0 rows  |  0.0019s
1 - 2026-08-20 17:42:22 --> [LedgerCondensed][acc=2651] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.0%)
   Q2-OpeningBalance           0.0002s   (  1.9%)
   Q3-MainLedger               0.0091s   ( 71.4%)
   Q4-OtherAccounts            0.0019s   ( 14.8%)
   BuildRecords                0.0002s   (  1.8%)
   TOTAL                       0.0127s   (100%)
[LedgerCondensed][acc=2651] ── FUNCTION END ──
1 - 2026-08-20 17:42:50 --> [LedgerCondensed][acc=2651] ── FUNCTION START ── from=2024-04-01  to=2024-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-20 17:42:50 --> [LedgerCondensed][acc=2651] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-08-20 17:42:50 --> [LedgerCondensed][acc=2651] Q2-OpeningBalance => 7283671.78  |  0.0003s
1 - 2026-08-20 17:42:50 --> [LedgerCondensed][acc=2651] Q3-MainLedger => rows=54  total=54  |  0.0093s
1 - 2026-08-20 17:42:50 --> [LedgerCondensed][acc=2651] Q4-OtherAccounts => 0 rows  |  0.0019s
1 - 2026-08-20 17:42:50 --> [LedgerCondensed][acc=2651] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.1%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0093s   ( 71.9%)
   Q4-OtherAccounts            0.0019s   ( 14.4%)
   BuildRecords                0.0002s   (  1.8%)
   TOTAL                       0.0130s   (100%)
[LedgerCondensed][acc=2651] ── FUNCTION END ──
1 - 2026-08-20 17:43:14 --> [LedgerCondensed][acc=2654] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-20 17:43:14 --> [LedgerCondensed][acc=2654] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-08-20 17:43:14 --> [LedgerCondensed][acc=2654] Q2-OpeningBalance => 700000.00  |  0.0003s
1 - 2026-08-20 17:43:14 --> [LedgerCondensed][acc=2654] Q3-MainLedger => rows=16  total=16  |  0.0093s
1 - 2026-08-20 17:43:14 --> [LedgerCondensed][acc=2654] Q4-OtherAccounts => 0 rows  |  0.0022s
1 - 2026-08-20 17:43:14 --> [LedgerCondensed][acc=2654] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.8%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0093s   ( 70.8%)
   Q4-OtherAccounts            0.0022s   ( 16.8%)
   BuildRecords                0.0001s   (  0.8%)
   TOTAL                       0.0132s   (100%)
[LedgerCondensed][acc=2654] ── FUNCTION END ──
1 - 2026-08-20 17:43:43 --> [LedgerCondensed][acc=2655] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-20 17:43:43 --> [LedgerCondensed][acc=2655] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-08-20 17:43:43 --> [LedgerCondensed][acc=2655] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-20 17:43:43 --> [LedgerCondensed][acc=2655] Q3-MainLedger => rows=12  total=12  |  0.0093s
1 - 2026-08-20 17:43:43 --> [LedgerCondensed][acc=2655] Q4-OtherAccounts => 0 rows  |  0.0018s
1 - 2026-08-20 17:43:43 --> [LedgerCondensed][acc=2655] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.2%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0093s   ( 72.7%)
   Q4-OtherAccounts            0.0018s   ( 14.0%)
   BuildRecords                0.0001s   (  0.6%)
   TOTAL                       0.0128s   (100%)
[LedgerCondensed][acc=2655] ── FUNCTION END ──
1 - 2026-08-20 17:43:55 --> [LedgerCondensed][acc=2655] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-20 17:43:55 --> [LedgerCondensed][acc=2655] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-08-20 17:43:55 --> [LedgerCondensed][acc=2655] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-20 17:43:55 --> [LedgerCondensed][acc=2655] Q3-MainLedger => rows=12  total=12  |  0.0096s
1 - 2026-08-20 17:43:55 --> [LedgerCondensed][acc=2655] Q4-OtherAccounts => 0 rows  |  0.002s
1 - 2026-08-20 17:43:55 --> [LedgerCondensed][acc=2655] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.6%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0096s   ( 72.6%)
   Q4-OtherAccounts            0.0020s   ( 15.3%)
   BuildRecords                0.0001s   (  0.6%)
   TOTAL                       0.0132s   (100%)
[LedgerCondensed][acc=2655] ── FUNCTION END ──
1 - 2026-08-20 23:09:50 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-20 23:09:50 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301600, Bill Sundry ID: 10188
1 - 2026-08-20 23:09:50 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-08-21","acc_txn_dr_cr":2,"acc_txn_amt":648.1299999999999954525264911353588104248046875,"acc_txn_fcy":0,"vch_txn_id":301600,"txn_id":1022981,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-20 23:09:50 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-20 23:09:50 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301600, Bill Sundry ID: 10189
1 - 2026-08-20 23:09:50 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-08-21","acc_txn_dr_cr":2,"acc_txn_amt":648.1299999999999954525264911353588104248046875,"acc_txn_fcy":0,"vch_txn_id":301600,"txn_id":1022982,"hobo_id":"184","acc_txn_type":1}
