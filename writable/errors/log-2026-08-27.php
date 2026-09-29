<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-08-27 11:16:36 --> [LedgerCondensed][acc=2664] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-27 11:16:36 --> [LedgerCondensed][acc=2664] Q1-VoucherCount => 0  |  0.0025s
1 - 2026-08-27 11:16:36 --> [LedgerCondensed][acc=2664] Q2-OpeningBalance => -500000.00  |  0.0002s
1 - 2026-08-27 11:16:36 --> [LedgerCondensed][acc=2664] Q3-MainLedger => rows=4  total=4  |  0.0156s
1 - 2026-08-27 11:16:36 --> [LedgerCondensed][acc=2664] Q4-OtherAccounts => 0 rows  |  0.0029s
1 - 2026-08-27 11:16:36 --> [LedgerCondensed][acc=2664] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0025s   ( 11.4%)
   Q2-OpeningBalance           0.0002s   (  1.1%)
   Q3-MainLedger               0.0156s   ( 72.0%)
   Q4-OtherAccounts            0.0029s   ( 13.3%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0217s   (100%)
[LedgerCondensed][acc=2664] ── FUNCTION END ──
1 - 2026-08-27 12:57:55 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-27 12:57:55 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301601, Bill Sundry ID: 10188
1 - 2026-08-27 12:57:55 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-08-27","acc_txn_dr_cr":2,"acc_txn_amt":80,"acc_txn_fcy":0,"vch_txn_id":301601,"txn_id":1022987,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-27 12:57:55 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-27 12:57:55 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301601, Bill Sundry ID: 10189
1 - 2026-08-27 12:57:55 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-08-27","acc_txn_dr_cr":2,"acc_txn_amt":80,"acc_txn_fcy":0,"vch_txn_id":301601,"txn_id":1022988,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-27 12:58:33 --> [LedgerCondensed][acc=10344] ── FUNCTION START ── from=2026-08-01  to=2026-08-27  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-27 12:58:33 --> [LedgerCondensed][acc=10344] Q1-VoucherCount => 17  |  0.0035s
1 - 2026-08-27 12:58:33 --> [LedgerCondensed][acc=10344] Q2-OpeningBalance => 978797.99  |  0.0009s
1 - 2026-08-27 12:58:33 --> [LedgerCondensed][acc=10344] Q3-MainLedger => rows=3  total=3  |  0.0114s
1 - 2026-08-27 12:58:33 --> [LedgerCondensed][acc=10344] Q4-OtherAccounts => 3 rows  |  0.0013s
1 - 2026-08-27 12:58:33 --> [LedgerCondensed][acc=10344] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0035s   ( 20.1%)
   Q2-OpeningBalance           0.0009s   (  5.2%)
   Q3-MainLedger               0.0114s   ( 65.2%)
   Q4-OtherAccounts            0.0013s   (  7.3%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0175s   (100%)
[LedgerCondensed][acc=10344] ── FUNCTION END ──
