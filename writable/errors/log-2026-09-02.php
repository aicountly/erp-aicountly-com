<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-09-02 12:05:54 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-02 12:05:54 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301605, Bill Sundry ID: 10188
1 - 2026-09-02 12:05:54 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-08-02","acc_txn_dr_cr":2,"acc_txn_amt":691.6299999999999954525264911353588104248046875,"acc_txn_fcy":0,"vch_txn_id":301605,"txn_id":1023079,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-02 12:05:54 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-02 12:05:54 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301605, Bill Sundry ID: 10189
1 - 2026-09-02 12:05:54 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-08-02","acc_txn_dr_cr":2,"acc_txn_amt":691.6299999999999954525264911353588104248046875,"acc_txn_fcy":0,"vch_txn_id":301605,"txn_id":1023080,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-02 12:06:31 --> [LedgerCondensed][acc=10344] ── FUNCTION START ── from=2026-09-01  to=2026-09-02  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-02 12:06:31 --> [LedgerCondensed][acc=10344] Q1-VoucherCount => 21  |  0.003s
1 - 2026-09-02 12:06:31 --> [LedgerCondensed][acc=10344] Q2-OpeningBalance => 1063328.78  |  0.001s
1 - 2026-09-02 12:06:31 --> [LedgerCondensed][acc=10344] Q3-MainLedger => rows=0  total=0  |  0.0012s
1 - 2026-09-02 12:06:31 --> [LedgerCondensed][acc=10344] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-02 12:06:31 --> [LedgerCondensed][acc=10344] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0030s   ( 53.0%)
   Q2-OpeningBalance           0.0010s   ( 18.4%)
   Q3-MainLedger               0.0012s   ( 21.2%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0056s   (100%)
[LedgerCondensed][acc=10344] ── FUNCTION END ──
1 - 2026-09-02 12:07:11 --> [LedgerCondensed][acc=10344] ── FUNCTION START ── from=2026-08-01  to=2026-09-02  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-02 12:07:11 --> [LedgerCondensed][acc=10344] Q1-VoucherCount => 17  |  0.0014s
1 - 2026-09-02 12:07:11 --> [LedgerCondensed][acc=10344] Q2-OpeningBalance => 978797.99  |  0.0009s
1 - 2026-09-02 12:07:11 --> [LedgerCondensed][acc=10344] Q3-MainLedger => rows=4  total=4  |  0.0114s
1 - 2026-09-02 12:07:11 --> [LedgerCondensed][acc=10344] Q4-OtherAccounts => 4 rows  |  0.0011s
1 - 2026-09-02 12:07:11 --> [LedgerCondensed][acc=10344] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0014s   (  8.9%)
   Q2-OpeningBalance           0.0009s   (  5.8%)
   Q3-MainLedger               0.0114s   ( 75.5%)
   Q4-OtherAccounts            0.0011s   (  7.0%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0151s   (100%)
[LedgerCondensed][acc=10344] ── FUNCTION END ──
1 - 2026-09-02 12:07:58 --> Company is gstin type check => 1
1 - 2026-09-02 12:07:58 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-02 12:07:58 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301605, Bill Sundry ID: 10188
1 - 2026-09-02 12:07:58 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-09-02","acc_txn_dr_cr":2,"acc_txn_amt":691.6299999999999954525264911353588104248046875,"acc_txn_fcy":0,"vch_txn_id":"301605","txn_id":1023099,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-02 12:07:58 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-02 12:07:58 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301605, Bill Sundry ID: 10189
1 - 2026-09-02 12:07:58 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-09-02","acc_txn_dr_cr":2,"acc_txn_amt":691.6299999999999954525264911353588104248046875,"acc_txn_fcy":0,"vch_txn_id":"301605","txn_id":1023100,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-02 12:07:59 --> [LedgerCondensed][acc=10344] ── FUNCTION START ── from=2026-08-01  to=2026-09-02  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-02 12:07:59 --> [LedgerCondensed][acc=10344] Q1-VoucherCount => 17  |  0.0014s
1 - 2026-09-02 12:07:59 --> [LedgerCondensed][acc=10344] Q2-OpeningBalance => 978797.99  |  0.0009s
1 - 2026-09-02 12:07:59 --> [LedgerCondensed][acc=10344] Q3-MainLedger => rows=4  total=4  |  0.0114s
1 - 2026-09-02 12:07:59 --> [LedgerCondensed][acc=10344] Q4-OtherAccounts => 4 rows  |  0.001s
1 - 2026-09-02 12:07:59 --> [LedgerCondensed][acc=10344] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0014s   (  9.3%)
   Q2-OpeningBalance           0.0009s   (  6.1%)
   Q3-MainLedger               0.0114s   ( 75.5%)
   Q4-OtherAccounts            0.0010s   (  6.5%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0151s   (100%)
[LedgerCondensed][acc=10344] ── FUNCTION END ──
1 - 2026-09-02 12:18:28 --> [LedgerCondensed][acc=10344] ── FUNCTION START ── from=2026-08-01  to=2026-09-02  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-02 12:18:28 --> [LedgerCondensed][acc=10344] Q1-VoucherCount => 17  |  0.0015s
1 - 2026-09-02 12:18:28 --> [LedgerCondensed][acc=10344] Q2-OpeningBalance => 978797.99  |  0.001s
1 - 2026-09-02 12:18:28 --> [LedgerCondensed][acc=10344] Q3-MainLedger => rows=4  total=4  |  0.0119s
1 - 2026-09-02 12:18:28 --> [LedgerCondensed][acc=10344] Q4-OtherAccounts => 4 rows  |  0.001s
1 - 2026-09-02 12:18:28 --> [LedgerCondensed][acc=10344] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0015s   (  9.3%)
   Q2-OpeningBalance           0.0010s   (  6.1%)
   Q3-MainLedger               0.0119s   ( 75.5%)
   Q4-OtherAccounts            0.0010s   (  6.5%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0157s   (100%)
[LedgerCondensed][acc=10344] ── FUNCTION END ──
1 - 2026-09-02 12:19:01 --> [LedgerCondensed][acc=10357] ── FUNCTION START ── from=2026-09-01  to=2026-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-02 12:19:01 --> [LedgerCondensed][acc=10357] Q1-VoucherCount => 15  |  0.0012s
1 - 2026-09-02 12:19:01 --> [LedgerCondensed][acc=10357] Q2-OpeningBalance => 708400.59  |  0.0009s
1 - 2026-09-02 12:19:01 --> [LedgerCondensed][acc=10357] Q3-MainLedger => rows=0  total=0  |  0.0011s
1 - 2026-09-02 12:19:01 --> [LedgerCondensed][acc=10357] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-02 12:19:01 --> [LedgerCondensed][acc=10357] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   ( 34.3%)
   Q2-OpeningBalance           0.0009s   ( 24.6%)
   Q3-MainLedger               0.0011s   ( 31.0%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0036s   (100%)
[LedgerCondensed][acc=10357] ── FUNCTION END ──
1 - 2026-09-02 12:19:22 --> [LedgerCondensed][acc=10357] ── FUNCTION START ── from=2026-08-01  to=2026-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-02 12:19:22 --> [LedgerCondensed][acc=10357] Q1-VoucherCount => 12  |  0.0014s
1 - 2026-09-02 12:19:22 --> [LedgerCondensed][acc=10357] Q2-OpeningBalance => 650328.54  |  0.001s
1 - 2026-09-02 12:19:22 --> [LedgerCondensed][acc=10357] Q3-MainLedger => rows=3  total=3  |  0.0123s
1 - 2026-09-02 12:19:22 --> [LedgerCondensed][acc=10357] Q4-OtherAccounts => 3 rows  |  0.001s
1 - 2026-09-02 12:19:22 --> [LedgerCondensed][acc=10357] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0014s   (  8.6%)
   Q2-OpeningBalance           0.0010s   (  6.3%)
   Q3-MainLedger               0.0123s   ( 76.0%)
   Q4-OtherAccounts            0.0010s   (  6.3%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0161s   (100%)
[LedgerCondensed][acc=10357] ── FUNCTION END ──
1 - 2026-09-02 12:31:11 --> InsertOppBal => {"itm_id_unit_id":"75182_1800","cmp_id":"140","itm_op_bal_qty":0,"itm_py_bal_qty":0,"mat_cent_id":122,"hobo_id":"184","cmpfymastr_id":"315"}
1 - 2026-09-02 12:31:11 --> InsertOppBal => {"itm_id_unit_id":"75182_1800","cmp_id":"140","itm_op_bal_qty":0,"itm_py_bal_qty":0,"mat_cent_id":122,"hobo_id":"184","cmpfymastr_id":"315"}
1 - 2026-09-02 12:31:11 --> InsertOppBal => {"itm_id_unit_id":"75182_1800","cmp_id":"140","itm_op_bal_qty":0,"itm_py_bal_qty":0,"mat_cent_id":122,"hobo_id":"184","cmpfymastr_id":"315"}
1 - 2026-09-02 12:31:11 --> InsertOppBal => {"itm_id_unit_id":"75182_1800","cmp_id":"140","itm_op_bal_qty":0,"itm_py_bal_qty":0,"mat_cent_id":122,"hobo_id":"184","cmpfymastr_id":"315"}
1 - 2026-09-02 12:31:11 --> InsertOppBal => {"itm_id_unit_id":"75182_1800","cmp_id":"140","itm_op_bal_qty":0,"itm_py_bal_qty":0,"mat_cent_id":122,"hobo_id":"184","cmpfymastr_id":"315"}
1 - 2026-09-02 12:31:50 --> [LedgerCondensed][acc=10357] ── FUNCTION START ── from=2026-08-01  to=2026-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-02 12:31:50 --> [LedgerCondensed][acc=10357] Q1-VoucherCount => 12  |  0.0014s
1 - 2026-09-02 12:31:50 --> [LedgerCondensed][acc=10357] Q2-OpeningBalance => 650328.54  |  0.0009s
1 - 2026-09-02 12:31:50 --> [LedgerCondensed][acc=10357] Q3-MainLedger => rows=3  total=3  |  0.0115s
1 - 2026-09-02 12:31:50 --> [LedgerCondensed][acc=10357] Q4-OtherAccounts => 3 rows  |  0.001s
1 - 2026-09-02 12:31:50 --> [LedgerCondensed][acc=10357] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0014s   (  9.1%)
   Q2-OpeningBalance           0.0009s   (  5.7%)
   Q3-MainLedger               0.0115s   ( 75.6%)
   Q4-OtherAccounts            0.0010s   (  6.7%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0151s   (100%)
[LedgerCondensed][acc=10357] ── FUNCTION END ──
1 - 2026-09-02 12:34:20 --> [LedgerCondensed][acc=10357] ── FUNCTION START ── from=2026-08-01  to=2026-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-02 12:34:20 --> [LedgerCondensed][acc=10357] Q1-VoucherCount => 12  |  0.0014s
1 - 2026-09-02 12:34:20 --> [LedgerCondensed][acc=10357] Q2-OpeningBalance => 650328.54  |  0.0009s
1 - 2026-09-02 12:34:20 --> [LedgerCondensed][acc=10357] Q3-MainLedger => rows=3  total=3  |  0.0116s
1 - 2026-09-02 12:34:20 --> [LedgerCondensed][acc=10357] Q4-OtherAccounts => 3 rows  |  0.001s
1 - 2026-09-02 12:34:20 --> [LedgerCondensed][acc=10357] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0014s   (  9.1%)
   Q2-OpeningBalance           0.0009s   (  5.8%)
   Q3-MainLedger               0.0116s   ( 75.3%)
   Q4-OtherAccounts            0.0010s   (  6.7%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0154s   (100%)
[LedgerCondensed][acc=10357] ── FUNCTION END ──
1 - 2026-09-02 12:35:13 --> Company is gstin type check => 1
1 - 2026-09-02 12:35:13 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-02 12:35:13 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301604, Bill Sundry ID: 10188
1 - 2026-09-02 12:35:13 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-08-31","acc_txn_dr_cr":2,"acc_txn_amt":1302.84999999999990905052982270717620849609375,"acc_txn_fcy":0,"vch_txn_id":"301604","txn_id":1023122,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-02 12:35:13 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-02 12:35:13 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301604, Bill Sundry ID: 10189
1 - 2026-09-02 12:35:13 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-08-31","acc_txn_dr_cr":2,"acc_txn_amt":1302.84999999999990905052982270717620849609375,"acc_txn_fcy":0,"vch_txn_id":"301604","txn_id":1023123,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-02 12:35:13 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 5
AND "acctmaster"."cmp_id" = '140'
1 - 2026-09-02 12:35:13 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301604, Bill Sundry ID: 10187
1 - 2026-09-02 12:35:13 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10187","acc_txn_date":"2026-08-31","acc_txn_dr_cr":2,"acc_txn_amt":201.599999999999994315658113919198513031005859375,"acc_txn_fcy":0,"vch_txn_id":"301604","txn_id":1023124,"hobo_id":"184","acc_txn_type":1}
1 - 2026-09-02 12:35:14 --> [LedgerCondensed][acc=10357] ── FUNCTION START ── from=2026-08-01  to=2026-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-02 12:35:14 --> [LedgerCondensed][acc=10357] Q1-VoucherCount => 12  |  0.0015s
1 - 2026-09-02 12:35:14 --> [LedgerCondensed][acc=10357] Q2-OpeningBalance => 650328.54  |  0.0009s
1 - 2026-09-02 12:35:14 --> [LedgerCondensed][acc=10357] Q3-MainLedger => rows=3  total=3  |  0.012s
1 - 2026-09-02 12:35:14 --> [LedgerCondensed][acc=10357] Q4-OtherAccounts => 3 rows  |  0.0014s
1 - 2026-09-02 12:35:14 --> [LedgerCondensed][acc=10357] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0015s   (  9.3%)
   Q2-OpeningBalance           0.0009s   (  5.6%)
   Q3-MainLedger               0.0120s   ( 74.3%)
   Q4-OtherAccounts            0.0014s   (  8.4%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0161s   (100%)
[LedgerCondensed][acc=10357] ── FUNCTION END ──
