<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-09-03 10:02:02 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-03 10:02:02 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301606, Bill Sundry ID: 10188
1 - 2026-09-03 10:02:02 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-09-03","acc_txn_dr_cr":2,"acc_txn_amt":268.80000000000001136868377216160297393798828125,"acc_txn_fcy":0,"vch_txn_id":301606,"txn_id":1023135,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-03 10:02:02 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-03 10:02:02 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301606, Bill Sundry ID: 10189
1 - 2026-09-03 10:02:02 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-09-03","acc_txn_dr_cr":2,"acc_txn_amt":268.80000000000001136868377216160297393798828125,"acc_txn_fcy":0,"vch_txn_id":301606,"txn_id":1023136,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-03 10:07:13 --> [LedgerCondensed][acc=10356] ── FUNCTION START ── from=2026-09-01  to=2026-09-03  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-03 10:07:13 --> [LedgerCondensed][acc=10356] Q1-VoucherCount => 7  |  0.0018s
1 - 2026-09-03 10:07:13 --> [LedgerCondensed][acc=10356] Q2-OpeningBalance => 107654.93  |  0.0009s
1 - 2026-09-03 10:07:13 --> [LedgerCondensed][acc=10356] Q3-MainLedger => rows=1  total=1  |  0.0153s
1 - 2026-09-03 10:07:13 --> [LedgerCondensed][acc=10356] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-09-03 10:07:13 --> [LedgerCondensed][acc=10356] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0018s   (  9.0%)
   Q2-OpeningBalance           0.0009s   (  4.6%)
   Q3-MainLedger               0.0153s   ( 79.0%)
   Q4-OtherAccounts            0.0010s   (  5.0%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0194s   (100%)
[LedgerCondensed][acc=10356] ── FUNCTION END ──
1 - 2026-09-03 10:09:41 --> [LedgerCondensed][acc=10356] ── FUNCTION START ── from=2026-09-01  to=2026-09-03  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-03 10:09:41 --> [LedgerCondensed][acc=10356] Q1-VoucherCount => 7  |  0.0014s
1 - 2026-09-03 10:09:41 --> [LedgerCondensed][acc=10356] Q2-OpeningBalance => 107654.93  |  0.0008s
1 - 2026-09-03 10:09:41 --> [LedgerCondensed][acc=10356] Q3-MainLedger => rows=1  total=1  |  0.0123s
1 - 2026-09-03 10:09:41 --> [LedgerCondensed][acc=10356] Q4-OtherAccounts => 1 rows  |  0.0008s
1 - 2026-09-03 10:09:41 --> [LedgerCondensed][acc=10356] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0014s   (  8.6%)
   Q2-OpeningBalance           0.0008s   (  5.3%)
   Q3-MainLedger               0.0123s   ( 78.1%)
   Q4-OtherAccounts            0.0008s   (  5.4%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0158s   (100%)
[LedgerCondensed][acc=10356] ── FUNCTION END ──
