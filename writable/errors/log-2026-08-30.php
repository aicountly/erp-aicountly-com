<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-08-30 14:56:33 --> [LedgerCondensed][acc=10029] ── FUNCTION START ── from=2026-08-01  to=2026-08-29  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-30 14:56:33 --> [LedgerCondensed][acc=10029] Q1-VoucherCount => 7  |  0.0023s
1 - 2026-08-30 14:56:33 --> [LedgerCondensed][acc=10029] Q2-OpeningBalance => 269519.19  |  0.0008s
1 - 2026-08-30 14:56:33 --> [LedgerCondensed][acc=10029] Q3-MainLedger => rows=1  total=1  |  0.0176s
1 - 2026-08-30 14:56:33 --> [LedgerCondensed][acc=10029] Q4-OtherAccounts => 1 rows  |  0.0013s
1 - 2026-08-30 14:56:33 --> [LedgerCondensed][acc=10029] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0023s   ( 10.4%)
   Q2-OpeningBalance           0.0008s   (  3.6%)
   Q3-MainLedger               0.0176s   ( 78.0%)
   Q4-OtherAccounts            0.0013s   (  5.8%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0225s   (100%)
[LedgerCondensed][acc=10029] ── FUNCTION END ──
1 - 2026-08-30 15:08:49 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-30 15:08:49 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301603, Bill Sundry ID: 10188
1 - 2026-08-30 15:08:49 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-08-30","acc_txn_dr_cr":2,"acc_txn_amt":413.93000000000000682121026329696178436279296875,"acc_txn_fcy":0,"vch_txn_id":301603,"txn_id":1023013,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-30 15:08:49 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-30 15:08:49 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301603, Bill Sundry ID: 10189
1 - 2026-08-30 15:08:49 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-08-30","acc_txn_dr_cr":2,"acc_txn_amt":413.93000000000000682121026329696178436279296875,"acc_txn_fcy":0,"vch_txn_id":301603,"txn_id":1023014,"hobo_id":"184","acc_txn_type":1}
