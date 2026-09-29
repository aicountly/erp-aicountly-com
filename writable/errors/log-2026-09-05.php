<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-09-05 10:11:04 --> [LedgerCondensed][acc=10357] ── FUNCTION START ── from=2026-08-01  to=2026-09-05  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-05 10:11:04 --> [LedgerCondensed][acc=10357] Q1-VoucherCount => 12  |  0.0016s
1 - 2026-09-05 10:11:04 --> [LedgerCondensed][acc=10357] Q2-OpeningBalance => 650328.54  |  0.0009s
1 - 2026-09-05 10:11:04 --> [LedgerCondensed][acc=10357] Q3-MainLedger => rows=3  total=3  |  0.0172s
1 - 2026-09-05 10:11:04 --> [LedgerCondensed][acc=10357] Q4-OtherAccounts => 3 rows  |  0.0011s
1 - 2026-09-05 10:11:04 --> [LedgerCondensed][acc=10357] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0016s   (  7.4%)
   Q2-OpeningBalance           0.0009s   (  4.3%)
   Q3-MainLedger               0.0172s   ( 80.9%)
   Q4-OtherAccounts            0.0011s   (  5.3%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0213s   (100%)
[LedgerCondensed][acc=10357] ── FUNCTION END ──
1 - 2026-09-05 10:20:45 --> Company is gstin type check => 1
1 - 2026-09-05 10:20:45 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-05 10:20:45 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301604, Bill Sundry ID: 10188
1 - 2026-09-05 10:20:45 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-08-31","acc_txn_dr_cr":2,"acc_txn_amt":869.6499999999999772626324556767940521240234375,"acc_txn_fcy":0,"vch_txn_id":"301604","txn_id":1023158,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-05 10:20:45 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-05 10:20:45 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301604, Bill Sundry ID: 10189
1 - 2026-09-05 10:20:45 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-08-31","acc_txn_dr_cr":2,"acc_txn_amt":869.6499999999999772626324556767940521240234375,"acc_txn_fcy":0,"vch_txn_id":"301604","txn_id":1023159,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-05 10:20:45 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 5
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-05 10:20:45 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301604, Bill Sundry ID: 10187
1 - 2026-09-05 10:20:45 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10187","acc_txn_date":"2026-08-31","acc_txn_dr_cr":2,"acc_txn_amt":201.599999999999994315658113919198513031005859375,"acc_txn_fcy":0,"vch_txn_id":"301604","txn_id":1023160,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-05 10:20:49 --> [LedgerCondensed][acc=10357] ── FUNCTION START ── from=2026-08-01  to=2026-09-05  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-05 10:20:49 --> [LedgerCondensed][acc=10357] Q1-VoucherCount => 12  |  0.0015s
1 - 2026-09-05 10:20:49 --> [LedgerCondensed][acc=10357] Q2-OpeningBalance => 650328.54  |  0.0009s
1 - 2026-09-05 10:20:49 --> [LedgerCondensed][acc=10357] Q3-MainLedger => rows=3  total=3  |  0.0117s
1 - 2026-09-05 10:20:49 --> [LedgerCondensed][acc=10357] Q4-OtherAccounts => 3 rows  |  0.001s
1 - 2026-09-05 10:20:49 --> [LedgerCondensed][acc=10357] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0015s   (  9.6%)
   Q2-OpeningBalance           0.0009s   (  5.9%)
   Q3-MainLedger               0.0117s   ( 75.2%)
   Q4-OtherAccounts            0.0010s   (  6.5%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0156s   (100%)
[LedgerCondensed][acc=10357] ── FUNCTION END ──
1 - 2026-09-05 10:26:22 --> [LedgerCondensed][acc=10357] ── FUNCTION START ── from=2026-08-01  to=2026-09-05  type=1  is_export=1  compId=  boId=  fyId=
1 - 2026-09-05 10:26:22 --> [LedgerCondensed][acc=10357] Q1-VoucherCount => 12  |  0.0012s
1 - 2026-09-05 10:26:22 --> [LedgerCondensed][acc=10357] Q2-OpeningBalance => 650328.54  |  0.0006s
1 - 2026-09-05 10:26:22 --> [LedgerCondensed][acc=10357] Q3-MainLedger => rows=3  total=3  |  0.0114s
1 - 2026-09-05 10:26:22 --> [LedgerCondensed][acc=10357] Q4-OtherAccounts => 3 rows  |  0.0008s
1 - 2026-09-05 10:26:22 --> [LedgerCondensed][acc=10357] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  8.3%)
   Q2-OpeningBalance           0.0006s   (  4.1%)
   Q3-MainLedger               0.0114s   ( 78.9%)
   Q4-OtherAccounts            0.0008s   (  5.8%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0144s   (100%)
[LedgerCondensed][acc=10357] ── FUNCTION END ──
1 - 2026-09-05 12:55:51 --> [LedgerCondensed][acc=10213] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-05 12:55:51 --> [LedgerCondensed][acc=10213] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-05 12:55:51 --> [LedgerCondensed][acc=10213] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-05 12:55:51 --> [LedgerCondensed][acc=10213] Q3-MainLedger => rows=100  total=368  |  0.0134s
1 - 2026-09-05 12:55:51 --> [LedgerCondensed][acc=10213] Q4-OtherAccounts => 100 rows  |  0.0067s
1 - 2026-09-05 12:55:51 --> [LedgerCondensed][acc=10213] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  3.7%)
   Q2-OpeningBalance           0.0003s   (  1.2%)
   Q3-MainLedger               0.0134s   ( 60.9%)
   Q4-OtherAccounts            0.0067s   ( 30.7%)
   BuildRecords                0.0003s   (  1.6%)
   TOTAL                       0.0220s   (100%)
[LedgerCondensed][acc=10213] ── FUNCTION END ──
1 - 2026-09-05 12:55:58 --> [LedgerCondensed][acc=10213] ── FUNCTION START ── from=2026-03-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-05 12:55:58 --> [LedgerCondensed][acc=10213] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-05 12:55:58 --> [LedgerCondensed][acc=10213] Q2-OpeningBalance => -14607256.49  |  0.0014s
1 - 2026-09-05 12:55:58 --> [LedgerCondensed][acc=10213] Q3-MainLedger => rows=8  total=8  |  0.0089s
1 - 2026-09-05 12:55:58 --> [LedgerCondensed][acc=10213] Q4-OtherAccounts => 8 rows  |  0.0016s
1 - 2026-09-05 12:55:58 --> [LedgerCondensed][acc=10213] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.6%)
   Q2-OpeningBalance           0.0014s   ( 10.7%)
   Q3-MainLedger               0.0089s   ( 67.1%)
   Q4-OtherAccounts            0.0016s   ( 12.2%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0133s   (100%)
[LedgerCondensed][acc=10213] ── FUNCTION END ──
