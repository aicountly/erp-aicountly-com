<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-08-18 10:50:06 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-18 10:50:06 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301597, Bill Sundry ID: 10188
1 - 2026-08-18 10:50:06 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-08-18","acc_txn_dr_cr":2,"acc_txn_amt":604.25,"acc_txn_fcy":0,"vch_txn_id":301597,"txn_id":1022942,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-18 10:50:06 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-18 10:50:06 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301597, Bill Sundry ID: 10189
1 - 2026-08-18 10:50:06 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-08-18","acc_txn_dr_cr":2,"acc_txn_amt":604.25,"acc_txn_fcy":0,"vch_txn_id":301597,"txn_id":1022943,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-18 10:50:44 --> [LedgerCondensed][acc=10356] ── FUNCTION START ── from=2026-08-01  to=2026-08-18  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-18 10:50:44 --> [LedgerCondensed][acc=10356] Q1-VoucherCount => 6  |  0.0022s
1 - 2026-08-18 10:50:44 --> [LedgerCondensed][acc=10356] Q2-OpeningBalance => 93226.43  |  0.0009s
1 - 2026-08-18 10:50:44 --> [LedgerCondensed][acc=10356] Q3-MainLedger => rows=1  total=1  |  0.0124s
1 - 2026-08-18 10:50:44 --> [LedgerCondensed][acc=10356] Q4-OtherAccounts => 1 rows  |  0.0016s
1 - 2026-08-18 10:50:44 --> [LedgerCondensed][acc=10356] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0022s   ( 12.7%)
   Q2-OpeningBalance           0.0009s   (  5.1%)
   Q3-MainLedger               0.0124s   ( 70.6%)
   Q4-OtherAccounts            0.0016s   (  9.1%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0175s   (100%)
[LedgerCondensed][acc=10356] ── FUNCTION END ──
1 - 2026-08-18 11:17:22 --> [LedgerCondensed][acc=19533] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-18 11:17:22 --> [LedgerCondensed][acc=19533] Q1-VoucherCount => 0  |  0.0012s
1 - 2026-08-18 11:17:22 --> [LedgerCondensed][acc=19533] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-18 11:17:22 --> [LedgerCondensed][acc=19533] Q3-MainLedger => rows=9  total=9  |  0.019s
1 - 2026-08-18 11:17:22 --> [LedgerCondensed][acc=19533] Q4-OtherAccounts => 9 rows  |  0.0037s
1 - 2026-08-18 11:17:22 --> [LedgerCondensed][acc=19533] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  4.8%)
   Q2-OpeningBalance           0.0003s   (  1.2%)
   Q3-MainLedger               0.0190s   ( 77.2%)
   Q4-OtherAccounts            0.0037s   ( 14.9%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0247s   (100%)
[LedgerCondensed][acc=19533] ── FUNCTION END ──
1 - 2026-08-18 11:17:30 --> [LedgerCondensed][acc=19533] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=1  compId=  boId=  fyId=
1 - 2026-08-18 11:17:30 --> [LedgerCondensed][acc=19533] Q1-VoucherCount => 0  |  0.0007s
1 - 2026-08-18 11:17:30 --> [LedgerCondensed][acc=19533] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-08-18 11:17:30 --> [LedgerCondensed][acc=19533] Q3-MainLedger => rows=9  total=9  |  0.0119s
1 - 2026-08-18 11:17:30 --> [LedgerCondensed][acc=19533] Q4-OtherAccounts => 9 rows  |  0.0009s
1 - 2026-08-18 11:17:30 --> [LedgerCondensed][acc=19533] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0007s   (  4.9%)
   Q2-OpeningBalance           0.0002s   (  1.2%)
   Q3-MainLedger               0.0119s   ( 84.5%)
   Q4-OtherAccounts            0.0009s   (  6.7%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0141s   (100%)
[LedgerCondensed][acc=19533] ── FUNCTION END ──
1 - 2026-08-18 11:17:56 --> [LedgerCondensed][acc=19533] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-18 11:17:56 --> [LedgerCondensed][acc=19533] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-08-18 11:17:56 --> [LedgerCondensed][acc=19533] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-18 11:17:56 --> [LedgerCondensed][acc=19533] Q3-MainLedger => rows=13  total=13  |  0.0143s
1 - 2026-08-18 11:17:56 --> [LedgerCondensed][acc=19533] Q4-OtherAccounts => 13 rows  |  0.0045s
1 - 2026-08-18 11:17:56 --> [LedgerCondensed][acc=19533] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  5.3%)
   Q2-OpeningBalance           0.0003s   (  1.3%)
   Q3-MainLedger               0.0143s   ( 69.4%)
   Q4-OtherAccounts            0.0045s   ( 21.7%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0206s   (100%)
[LedgerCondensed][acc=19533] ── FUNCTION END ──
1 - 2026-08-18 11:18:04 --> [LedgerCondensed][acc=19533] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=1  compId=  boId=  fyId=
1 - 2026-08-18 11:18:04 --> [LedgerCondensed][acc=19533] Q1-VoucherCount => 0  |  0.0006s
1 - 2026-08-18 11:18:04 --> [LedgerCondensed][acc=19533] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-08-18 11:18:04 --> [LedgerCondensed][acc=19533] Q3-MainLedger => rows=13  total=13  |  0.0115s
1 - 2026-08-18 11:18:04 --> [LedgerCondensed][acc=19533] Q4-OtherAccounts => 13 rows  |  0.0023s
1 - 2026-08-18 11:18:04 --> [LedgerCondensed][acc=19533] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0006s   (  4.2%)
   Q2-OpeningBalance           0.0002s   (  1.1%)
   Q3-MainLedger               0.0115s   ( 76.1%)
   Q4-OtherAccounts            0.0023s   ( 14.9%)
   BuildRecords                0.0001s   (  0.5%)
   TOTAL                       0.0151s   (100%)
[LedgerCondensed][acc=19533] ── FUNCTION END ──
1 - 2026-08-18 11:19:18 --> [LedgerCondensed][acc=19533] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-18 11:19:18 --> [LedgerCondensed][acc=19533] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-08-18 11:19:18 --> [LedgerCondensed][acc=19533] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-18 11:19:18 --> [LedgerCondensed][acc=19533] Q3-MainLedger => rows=14  total=14  |  0.0118s
1 - 2026-08-18 11:19:18 --> [LedgerCondensed][acc=19533] Q4-OtherAccounts => 14 rows  |  0.0026s
1 - 2026-08-18 11:19:18 --> [LedgerCondensed][acc=19533] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  5.3%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0118s   ( 73.3%)
   Q4-OtherAccounts            0.0026s   ( 16.3%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0160s   (100%)
[LedgerCondensed][acc=19533] ── FUNCTION END ──
1 - 2026-08-18 11:19:40 --> [LedgerCondensed][acc=19533] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-18 11:19:40 --> [LedgerCondensed][acc=19533] Q1-VoucherCount => 0  |  0.001s
1 - 2026-08-18 11:19:40 --> [LedgerCondensed][acc=19533] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-18 11:19:40 --> [LedgerCondensed][acc=19533] Q3-MainLedger => rows=14  total=14  |  0.0123s
1 - 2026-08-18 11:19:40 --> [LedgerCondensed][acc=19533] Q4-OtherAccounts => 14 rows  |  0.0025s
1 - 2026-08-18 11:19:40 --> [LedgerCondensed][acc=19533] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.8%)
   Q2-OpeningBalance           0.0003s   (  1.6%)
   Q3-MainLedger               0.0123s   ( 73.8%)
   Q4-OtherAccounts            0.0025s   ( 15.3%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0167s   (100%)
[LedgerCondensed][acc=19533] ── FUNCTION END ──
1 - 2026-08-18 11:19:43 --> [LedgerCondensed][acc=19533] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=1  compId=  boId=  fyId=
1 - 2026-08-18 11:19:43 --> [LedgerCondensed][acc=19533] Q1-VoucherCount => 0  |  0.0007s
1 - 2026-08-18 11:19:43 --> [LedgerCondensed][acc=19533] Q2-OpeningBalance => 0.00  |  0.0001s
1 - 2026-08-18 11:19:43 --> [LedgerCondensed][acc=19533] Q3-MainLedger => rows=14  total=14  |  0.0115s
1 - 2026-08-18 11:19:43 --> [LedgerCondensed][acc=19533] Q4-OtherAccounts => 14 rows  |  0.0024s
1 - 2026-08-18 11:19:43 --> [LedgerCondensed][acc=19533] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0007s   (  4.3%)
   Q2-OpeningBalance           0.0001s   (  0.9%)
   Q3-MainLedger               0.0115s   ( 76.0%)
   Q4-OtherAccounts            0.0024s   ( 16.2%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0151s   (100%)
[LedgerCondensed][acc=19533] ── FUNCTION END ──
1 - 2026-08-18 12:38:46 --> [LedgerCondensed][acc=19441] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-18 12:38:46 --> [LedgerCondensed][acc=19441] Q1-VoucherCount => 0  |  0.001s
1 - 2026-08-18 12:38:46 --> [LedgerCondensed][acc=19441] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-18 12:38:46 --> [LedgerCondensed][acc=19441] Q3-MainLedger => rows=85  total=85  |  0.0182s
1 - 2026-08-18 12:38:46 --> [LedgerCondensed][acc=19441] Q4-OtherAccounts => 85 rows  |  0.0074s
1 - 2026-08-18 12:38:46 --> [LedgerCondensed][acc=19441] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  3.8%)
   Q2-OpeningBalance           0.0003s   (  1.0%)
   Q3-MainLedger               0.0182s   ( 65.8%)
   Q4-OtherAccounts            0.0074s   ( 26.8%)
   BuildRecords                0.0003s   (  1.2%)
   TOTAL                       0.0277s   (100%)
[LedgerCondensed][acc=19441] ── FUNCTION END ──
1 - 2026-08-18 12:39:06 --> [LedgerCondensed][acc=19441] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=1  compId=  boId=  fyId=
1 - 2026-08-18 12:39:06 --> [LedgerCondensed][acc=19441] Q1-VoucherCount => 0  |  0.0007s
1 - 2026-08-18 12:39:06 --> [LedgerCondensed][acc=19441] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-08-18 12:39:06 --> [LedgerCondensed][acc=19441] Q3-MainLedger => rows=85  total=85  |  0.0116s
1 - 2026-08-18 12:39:06 --> [LedgerCondensed][acc=19441] Q4-OtherAccounts => 85 rows  |  0.0023s
1 - 2026-08-18 12:39:06 --> [LedgerCondensed][acc=19441] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0007s   (  4.3%)
   Q2-OpeningBalance           0.0002s   (  1.2%)
   Q3-MainLedger               0.0116s   ( 75.2%)
   Q4-OtherAccounts            0.0023s   ( 14.9%)
   BuildRecords                0.0003s   (  1.9%)
   TOTAL                       0.0154s   (100%)
[LedgerCondensed][acc=19441] ── FUNCTION END ──
