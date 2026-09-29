<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-09-01 15:59:13 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-01 15:59:13 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301604, Bill Sundry ID: 10188
1 - 2026-09-01 15:59:13 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-08-31","acc_txn_dr_cr":2,"acc_txn_amt":1296.59999999999990905052982270717620849609375,"acc_txn_fcy":0,"vch_txn_id":301604,"txn_id":1023058,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-01 15:59:13 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-01 15:59:13 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301604, Bill Sundry ID: 10189
1 - 2026-09-01 15:59:13 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-08-31","acc_txn_dr_cr":2,"acc_txn_amt":1296.59999999999990905052982270717620849609375,"acc_txn_fcy":0,"vch_txn_id":301604,"txn_id":1023059,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-01 15:59:13 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 5
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-01 15:59:13 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301604, Bill Sundry ID: 10187
1 - 2026-09-01 15:59:13 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10187","acc_txn_date":"2026-08-31","acc_txn_dr_cr":2,"acc_txn_amt":201.599999999999994315658113919198513031005859375,"acc_txn_fcy":0,"vch_txn_id":301604,"txn_id":1023060,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-01 16:00:20 --> [LedgerCondensed][acc=10357] ── FUNCTION START ── from=2026-09-01  to=2026-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-01 16:00:20 --> [LedgerCondensed][acc=10357] Q1-VoucherCount => 15  |  0.0026s
1 - 2026-09-01 16:00:20 --> [LedgerCondensed][acc=10357] Q2-OpeningBalance => 708400.59  |  0.001s
1 - 2026-09-01 16:00:20 --> [LedgerCondensed][acc=10357] Q3-MainLedger => rows=0  total=0  |  0.0019s
1 - 2026-09-01 16:00:20 --> [LedgerCondensed][acc=10357] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-01 16:00:20 --> [LedgerCondensed][acc=10357] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0026s   ( 43.9%)
   Q2-OpeningBalance           0.0010s   ( 17.3%)
   Q3-MainLedger               0.0019s   ( 32.3%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0060s   (100%)
[LedgerCondensed][acc=10357] ── FUNCTION END ──
1 - 2026-09-01 16:00:41 --> [LedgerCondensed][acc=10357] ── FUNCTION START ── from=2026-09-01  to=2026-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-01 16:00:41 --> [LedgerCondensed][acc=10357] Q1-VoucherCount => 15  |  0.0014s
1 - 2026-09-01 16:00:41 --> [LedgerCondensed][acc=10357] Q2-OpeningBalance => 708400.59  |  0.0009s
1 - 2026-09-01 16:00:41 --> [LedgerCondensed][acc=10357] Q3-MainLedger => rows=0  total=0  |  0.0011s
1 - 2026-09-01 16:00:41 --> [LedgerCondensed][acc=10357] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-01 16:00:41 --> [LedgerCondensed][acc=10357] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0014s   ( 36.3%)
   Q2-OpeningBalance           0.0009s   ( 24.1%)
   Q3-MainLedger               0.0011s   ( 29.6%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0038s   (100%)
[LedgerCondensed][acc=10357] ── FUNCTION END ──
1 - 2026-09-01 16:00:57 --> [LedgerCondensed][acc=10357] ── FUNCTION START ── from=2026-08-01  to=2026-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-01 16:00:57 --> [LedgerCondensed][acc=10357] Q1-VoucherCount => 12  |  0.0014s
1 - 2026-09-01 16:00:57 --> [LedgerCondensed][acc=10357] Q2-OpeningBalance => 650328.54  |  0.001s
1 - 2026-09-01 16:00:57 --> [LedgerCondensed][acc=10357] Q3-MainLedger => rows=3  total=3  |  0.0115s
1 - 2026-09-01 16:00:57 --> [LedgerCondensed][acc=10357] Q4-OtherAccounts => 3 rows  |  0.0022s
1 - 2026-09-01 16:00:57 --> [LedgerCondensed][acc=10357] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0014s   (  8.4%)
   Q2-OpeningBalance           0.0010s   (  5.9%)
   Q3-MainLedger               0.0115s   ( 69.8%)
   Q4-OtherAccounts            0.0022s   ( 13.4%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0164s   (100%)
[LedgerCondensed][acc=10357] ── FUNCTION END ──
