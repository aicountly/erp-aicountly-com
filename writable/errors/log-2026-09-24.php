<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-09-24 12:07:20 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 12:07:20 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 12:07:21 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 12:07:21 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 12:07:36 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 12:07:36 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 12:07:36 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 12:07:36 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 12:35:02 --> [LedgerCondensed][acc=6419] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 12:35:02 --> [LedgerCondensed][acc=6419] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-24 12:35:02 --> [LedgerCondensed][acc=6419] Q2-OpeningBalance => 10650.00  |  0.0003s
1 - 2026-09-24 12:35:02 --> [LedgerCondensed][acc=6419] Q3-MainLedger => rows=4  total=4  |  0.0123s
1 - 2026-09-24 12:35:02 --> [LedgerCondensed][acc=6419] Q4-OtherAccounts => 4 rows  |  0.0012s
1 - 2026-09-24 12:35:02 --> [LedgerCondensed][acc=6419] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  7.2%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0123s   ( 80.4%)
   Q4-OtherAccounts            0.0012s   (  7.9%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0153s   (100%)
[LedgerCondensed][acc=6419] ── FUNCTION END ──
1 - 2026-09-24 12:35:13 --> [LedgerCondensed][acc=6419] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 12:35:13 --> [LedgerCondensed][acc=6419] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 12:35:13 --> [LedgerCondensed][acc=6419] Q2-OpeningBalance => 10650.00  |  0.0002s
1 - 2026-09-24 12:35:13 --> [LedgerCondensed][acc=6419] Q3-MainLedger => rows=46  total=46  |  0.0116s
1 - 2026-09-24 12:35:13 --> [LedgerCondensed][acc=6419] Q4-OtherAccounts => 46 rows  |  0.0028s
1 - 2026-09-24 12:35:13 --> [LedgerCondensed][acc=6419] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.0%)
   Q2-OpeningBalance           0.0002s   (  1.5%)
   Q3-MainLedger               0.0116s   ( 71.2%)
   Q4-OtherAccounts            0.0028s   ( 17.4%)
   BuildRecords                0.0002s   (  1.1%)
   TOTAL                       0.0163s   (100%)
[LedgerCondensed][acc=6419] ── FUNCTION END ──
1 - 2026-09-24 13:30:26 --> [LedgerCondensed][acc=4520] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 13:30:26 --> [LedgerCondensed][acc=4520] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 13:30:26 --> [LedgerCondensed][acc=4520] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 13:30:26 --> [LedgerCondensed][acc=4520] Q3-MainLedger => rows=4  total=4  |  0.0186s
1 - 2026-09-24 13:30:26 --> [LedgerCondensed][acc=4520] Q4-OtherAccounts => 4 rows  |  0.0013s
1 - 2026-09-24 13:30:26 --> [LedgerCondensed][acc=4520] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  4.8%)
   Q2-OpeningBalance           0.0003s   (  1.2%)
   Q3-MainLedger               0.0186s   ( 85.9%)
   Q4-OtherAccounts            0.0013s   (  5.9%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0216s   (100%)
[LedgerCondensed][acc=4520] ── FUNCTION END ──
1 - 2026-09-24 13:30:37 --> [LedgerCondensed][acc=4520] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 13:30:37 --> [LedgerCondensed][acc=4520] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 13:30:37 --> [LedgerCondensed][acc=4520] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 13:30:37 --> [LedgerCondensed][acc=4520] Q3-MainLedger => rows=4  total=4  |  0.0134s
1 - 2026-09-24 13:30:37 --> [LedgerCondensed][acc=4520] Q4-OtherAccounts => 4 rows  |  0.0012s
1 - 2026-09-24 13:30:37 --> [LedgerCondensed][acc=4520] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.8%)
   Q2-OpeningBalance           0.0003s   (  1.6%)
   Q3-MainLedger               0.0134s   ( 81.6%)
   Q4-OtherAccounts            0.0012s   (  7.5%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0165s   (100%)
[LedgerCondensed][acc=4520] ── FUNCTION END ──
1 - 2026-09-24 13:30:47 --> [LedgerCondensed][acc=4529] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 13:30:47 --> [LedgerCondensed][acc=4529] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 13:30:47 --> [LedgerCondensed][acc=4529] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 13:30:47 --> [LedgerCondensed][acc=4529] Q3-MainLedger => rows=2  total=2  |  0.0135s
1 - 2026-09-24 13:30:47 --> [LedgerCondensed][acc=4529] Q4-OtherAccounts => 2 rows  |  0.0012s
1 - 2026-09-24 13:30:47 --> [LedgerCondensed][acc=4529] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.0%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0135s   ( 82.6%)
   Q4-OtherAccounts            0.0012s   (  7.2%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0163s   (100%)
[LedgerCondensed][acc=4529] ── FUNCTION END ──
1 - 2026-09-24 13:31:09 --> [LedgerCondensed][acc=4525] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 13:31:09 --> [LedgerCondensed][acc=4525] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 13:31:09 --> [LedgerCondensed][acc=4525] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 13:31:09 --> [LedgerCondensed][acc=4525] Q3-MainLedger => rows=14  total=14  |  0.0201s
1 - 2026-09-24 13:31:09 --> [LedgerCondensed][acc=4525] Q4-OtherAccounts => 14 rows  |  0.0015s
1 - 2026-09-24 13:31:09 --> [LedgerCondensed][acc=4525] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  4.3%)
   Q2-OpeningBalance           0.0002s   (  1.0%)
   Q3-MainLedger               0.0201s   ( 86.1%)
   Q4-OtherAccounts            0.0015s   (  6.6%)
   BuildRecords                0.0001s   (  0.3%)
   TOTAL                       0.0233s   (100%)
[LedgerCondensed][acc=4525] ── FUNCTION END ──
1 - 2026-09-24 13:33:12 --> [LedgerCondensed][acc=4529] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 13:33:12 --> [LedgerCondensed][acc=4529] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 13:33:12 --> [LedgerCondensed][acc=4529] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 13:33:12 --> [LedgerCondensed][acc=4529] Q3-MainLedger => rows=2  total=2  |  0.0191s
1 - 2026-09-24 13:33:12 --> [LedgerCondensed][acc=4529] Q4-OtherAccounts => 2 rows  |  0.0012s
1 - 2026-09-24 13:33:12 --> [LedgerCondensed][acc=4529] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  4.6%)
   Q2-OpeningBalance           0.0003s   (  1.3%)
   Q3-MainLedger               0.0191s   ( 86.6%)
   Q4-OtherAccounts            0.0012s   (  5.4%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0220s   (100%)
[LedgerCondensed][acc=4529] ── FUNCTION END ──
1 - 2026-09-24 13:35:17 --> [LedgerCondensed][acc=4526] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 13:35:17 --> [LedgerCondensed][acc=4526] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 13:35:17 --> [LedgerCondensed][acc=4526] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 13:35:17 --> [LedgerCondensed][acc=4526] Q3-MainLedger => rows=1  total=1  |  0.0191s
1 - 2026-09-24 13:35:17 --> [LedgerCondensed][acc=4526] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-09-24 13:35:17 --> [LedgerCondensed][acc=4526] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  4.6%)
   Q2-OpeningBalance           0.0003s   (  1.2%)
   Q3-MainLedger               0.0191s   ( 87.7%)
   Q4-OtherAccounts            0.0010s   (  4.5%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0217s   (100%)
[LedgerCondensed][acc=4526] ── FUNCTION END ──
1 - 2026-09-24 15:24:45 --> [LedgerCondensed][acc=6419] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:24:45 --> [LedgerCondensed][acc=6419] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:24:45 --> [LedgerCondensed][acc=6419] Q2-OpeningBalance => 10650.00  |  0.0003s
1 - 2026-09-24 15:24:45 --> [LedgerCondensed][acc=6419] Q3-MainLedger => rows=46  total=46  |  0.0122s
1 - 2026-09-24 15:24:45 --> [LedgerCondensed][acc=6419] Q4-OtherAccounts => 46 rows  |  0.0028s
1 - 2026-09-24 15:24:45 --> [LedgerCondensed][acc=6419] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.5%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0122s   ( 72.4%)
   Q4-OtherAccounts            0.0028s   ( 16.6%)
   BuildRecords                0.0002s   (  1.1%)
   TOTAL                       0.0169s   (100%)
[LedgerCondensed][acc=6419] ── FUNCTION END ──
1 - 2026-09-24 15:26:23 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 15:26:23 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 15:26:24 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 15:26:24 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 15:26:34 --> [LedgerCondensed][acc=6419] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:26:34 --> [LedgerCondensed][acc=6419] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 15:26:34 --> [LedgerCondensed][acc=6419] Q2-OpeningBalance => 10650.00  |  0.0002s
1 - 2026-09-24 15:26:34 --> [LedgerCondensed][acc=6419] Q3-MainLedger => rows=4  total=4  |  0.0116s
1 - 2026-09-24 15:26:34 --> [LedgerCondensed][acc=6419] Q4-OtherAccounts => 4 rows  |  0.0012s
1 - 2026-09-24 15:26:34 --> [LedgerCondensed][acc=6419] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.8%)
   Q2-OpeningBalance           0.0002s   (  1.7%)
   Q3-MainLedger               0.0116s   ( 80.2%)
   Q4-OtherAccounts            0.0012s   (  8.0%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0144s   (100%)
[LedgerCondensed][acc=6419] ── FUNCTION END ──
1 - 2026-09-24 15:26:39 --> [LedgerCondensed][acc=6419] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:26:39 --> [LedgerCondensed][acc=6419] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:26:39 --> [LedgerCondensed][acc=6419] Q2-OpeningBalance => 10650.00  |  0.0002s
1 - 2026-09-24 15:26:39 --> [LedgerCondensed][acc=6419] Q3-MainLedger => rows=46  total=46  |  0.0098s
1 - 2026-09-24 15:26:39 --> [LedgerCondensed][acc=6419] Q4-OtherAccounts => 46 rows  |  0.0026s
1 - 2026-09-24 15:26:39 --> [LedgerCondensed][acc=6419] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.1%)
   Q2-OpeningBalance           0.0002s   (  1.7%)
   Q3-MainLedger               0.0098s   ( 69.9%)
   Q4-OtherAccounts            0.0026s   ( 18.8%)
   BuildRecords                0.0001s   (  1.0%)
   TOTAL                       0.0140s   (100%)
[LedgerCondensed][acc=6419] ── FUNCTION END ──
1 - 2026-09-24 15:27:01 --> [LedgerCondensed][acc=6419] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:27:01 --> [LedgerCondensed][acc=6419] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:27:01 --> [LedgerCondensed][acc=6419] Q2-OpeningBalance => 10650.00  |  0.0003s
1 - 2026-09-24 15:27:01 --> [LedgerCondensed][acc=6419] Q3-MainLedger => rows=46  total=46  |  0.0098s
1 - 2026-09-24 15:27:01 --> [LedgerCondensed][acc=6419] Q4-OtherAccounts => 46 rows  |  0.0025s
1 - 2026-09-24 15:27:01 --> [LedgerCondensed][acc=6419] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.6%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0098s   ( 69.0%)
   Q4-OtherAccounts            0.0025s   ( 17.6%)
   BuildRecords                0.0002s   (  1.5%)
   TOTAL                       0.0141s   (100%)
[LedgerCondensed][acc=6419] ── FUNCTION END ──
1 - 2026-09-24 15:28:57 --> [LedgerCondensed][acc=6419] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:28:57 --> [LedgerCondensed][acc=6419] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:28:57 --> [LedgerCondensed][acc=6419] Q2-OpeningBalance => 10650.00  |  0.0004s
1 - 2026-09-24 15:28:57 --> [LedgerCondensed][acc=6419] Q3-MainLedger => rows=47  total=47  |  0.0095s
1 - 2026-09-24 15:28:57 --> [LedgerCondensed][acc=6419] Q4-OtherAccounts => 47 rows  |  0.0027s
1 - 2026-09-24 15:28:57 --> [LedgerCondensed][acc=6419] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.6%)
   Q2-OpeningBalance           0.0004s   (  2.6%)
   Q3-MainLedger               0.0095s   ( 67.3%)
   Q4-OtherAccounts            0.0027s   ( 19.1%)
   BuildRecords                0.0002s   (  1.3%)
   TOTAL                       0.0142s   (100%)
[LedgerCondensed][acc=6419] ── FUNCTION END ──
1 - 2026-09-24 15:30:51 --> [LedgerCondensed][acc=6419] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:30:51 --> [LedgerCondensed][acc=6419] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 15:30:51 --> [LedgerCondensed][acc=6419] Q2-OpeningBalance => 10650.00  |  0.0003s
1 - 2026-09-24 15:30:51 --> [LedgerCondensed][acc=6419] Q3-MainLedger => rows=4  total=4  |  0.0117s
1 - 2026-09-24 15:30:51 --> [LedgerCondensed][acc=6419] Q4-OtherAccounts => 4 rows  |  0.0012s
1 - 2026-09-24 15:30:51 --> [LedgerCondensed][acc=6419] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.8%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0117s   ( 79.9%)
   Q4-OtherAccounts            0.0012s   (  8.4%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0147s   (100%)
[LedgerCondensed][acc=6419] ── FUNCTION END ──
1 - 2026-09-24 15:31:09 --> [LedgerCondensed][acc=6421] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:31:09 --> [LedgerCondensed][acc=6421] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 15:31:09 --> [LedgerCondensed][acc=6421] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 15:31:09 --> [LedgerCondensed][acc=6421] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-24 15:31:09 --> [LedgerCondensed][acc=6421] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 15:31:09 --> [LedgerCondensed][acc=6421] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 29.6%)
   Q2-OpeningBalance           0.0002s   (  7.2%)
   Q3-MainLedger               0.0016s   ( 48.4%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0033s   (100%)
[LedgerCondensed][acc=6421] ── FUNCTION END ──
1 - 2026-09-24 15:31:14 --> [LedgerCondensed][acc=6421] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:31:14 --> [LedgerCondensed][acc=6421] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 15:31:14 --> [LedgerCondensed][acc=6421] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 15:31:14 --> [LedgerCondensed][acc=6421] Q3-MainLedger => rows=37  total=37  |  0.0115s
1 - 2026-09-24 15:31:14 --> [LedgerCondensed][acc=6421] Q4-OtherAccounts => 37 rows  |  0.0019s
1 - 2026-09-24 15:31:14 --> [LedgerCondensed][acc=6421] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.4%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0115s   ( 76.3%)
   Q4-OtherAccounts            0.0019s   ( 12.3%)
   BuildRecords                0.0001s   (  0.9%)
   TOTAL                       0.0151s   (100%)
[LedgerCondensed][acc=6421] ── FUNCTION END ──
1 - 2026-09-24 15:34:21 --> [LedgerCondensed][acc=6421] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:34:21 --> [LedgerCondensed][acc=6421] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 15:34:21 --> [LedgerCondensed][acc=6421] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 15:34:21 --> [LedgerCondensed][acc=6421] Q3-MainLedger => rows=37  total=37  |  0.0092s
1 - 2026-09-24 15:34:21 --> [LedgerCondensed][acc=6421] Q4-OtherAccounts => 37 rows  |  0.0018s
1 - 2026-09-24 15:34:21 --> [LedgerCondensed][acc=6421] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.1%)
   Q2-OpeningBalance           0.0002s   (  1.9%)
   Q3-MainLedger               0.0092s   ( 73.4%)
   Q4-OtherAccounts            0.0018s   ( 14.2%)
   BuildRecords                0.0001s   (  1.2%)
   TOTAL                       0.0125s   (100%)
[LedgerCondensed][acc=6421] ── FUNCTION END ──
1 - 2026-09-24 15:34:43 --> [LedgerCondensed][acc=6421] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:34:43 --> [LedgerCondensed][acc=6421] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:34:43 --> [LedgerCondensed][acc=6421] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 15:34:43 --> [LedgerCondensed][acc=6421] Q3-MainLedger => rows=0  total=0  |  0.0017s
1 - 2026-09-24 15:34:43 --> [LedgerCondensed][acc=6421] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 15:34:43 --> [LedgerCondensed][acc=6421] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 27.6%)
   Q2-OpeningBalance           0.0003s   (  7.9%)
   Q3-MainLedger               0.0017s   ( 52.8%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0032s   (100%)
[LedgerCondensed][acc=6421] ── FUNCTION END ──
1 - 2026-09-24 15:34:51 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 15:34:51 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 15:34:51 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 15:34:51 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 15:35:02 --> [LedgerCondensed][acc=6419] ── FUNCTION START ── from=2026-03-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:35:02 --> [LedgerCondensed][acc=6419] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 15:35:02 --> [LedgerCondensed][acc=6419] Q2-OpeningBalance => -3350  |  0.0011s
1 - 2026-09-24 15:35:02 --> [LedgerCondensed][acc=6419] Q3-MainLedger => rows=2  total=2  |  0.0129s
1 - 2026-09-24 15:35:02 --> [LedgerCondensed][acc=6419] Q4-OtherAccounts => 2 rows  |  0.0013s
1 - 2026-09-24 15:35:02 --> [LedgerCondensed][acc=6419] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.8%)
   Q2-OpeningBalance           0.0011s   (  6.6%)
   Q3-MainLedger               0.0129s   ( 76.9%)
   Q4-OtherAccounts            0.0013s   (  7.7%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0168s   (100%)
[LedgerCondensed][acc=6419] ── FUNCTION END ──
1 - 2026-09-24 15:35:24 --> [LedgerCondensed][acc=6419] ── FUNCTION START ── from=2026-03-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:35:24 --> [LedgerCondensed][acc=6419] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:35:24 --> [LedgerCondensed][acc=6419] Q2-OpeningBalance => -3350  |  0.001s
1 - 2026-09-24 15:35:24 --> [LedgerCondensed][acc=6419] Q3-MainLedger => rows=1  total=1  |  0.0096s
1 - 2026-09-24 15:35:24 --> [LedgerCondensed][acc=6419] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-09-24 15:35:24 --> [LedgerCondensed][acc=6419] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.7%)
   Q2-OpeningBalance           0.0010s   (  8.0%)
   Q3-MainLedger               0.0096s   ( 74.8%)
   Q4-OtherAccounts            0.0010s   (  7.4%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0128s   (100%)
[LedgerCondensed][acc=6419] ── FUNCTION END ──
1 - 2026-09-24 15:36:18 --> [LedgerCondensed][acc=6422] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:36:18 --> [LedgerCondensed][acc=6422] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 15:36:18 --> [LedgerCondensed][acc=6422] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 15:36:18 --> [LedgerCondensed][acc=6422] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-24 15:36:18 --> [LedgerCondensed][acc=6422] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 15:36:18 --> [LedgerCondensed][acc=6422] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 31.0%)
   Q2-OpeningBalance           0.0002s   (  7.4%)
   Q3-MainLedger               0.0016s   ( 50.3%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0032s   (100%)
[LedgerCondensed][acc=6422] ── FUNCTION END ──
1 - 2026-09-24 15:36:22 --> [LedgerCondensed][acc=6422] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:36:22 --> [LedgerCondensed][acc=6422] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-24 15:36:22 --> [LedgerCondensed][acc=6422] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 15:36:22 --> [LedgerCondensed][acc=6422] Q3-MainLedger => rows=12  total=12  |  0.0097s
1 - 2026-09-24 15:36:22 --> [LedgerCondensed][acc=6422] Q4-OtherAccounts => 12 rows  |  0.002s
1 - 2026-09-24 15:36:22 --> [LedgerCondensed][acc=6422] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  8.4%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0097s   ( 70.7%)
   Q4-OtherAccounts            0.0020s   ( 14.5%)
   BuildRecords                0.0001s   (  0.7%)
   TOTAL                       0.0137s   (100%)
[LedgerCondensed][acc=6422] ── FUNCTION END ──
1 - 2026-09-24 15:38:15 --> [LedgerCondensed][acc=6422] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:38:15 --> [LedgerCondensed][acc=6422] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:38:15 --> [LedgerCondensed][acc=6422] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 15:38:15 --> [LedgerCondensed][acc=6422] Q3-MainLedger => rows=13  total=13  |  0.0098s
1 - 2026-09-24 15:38:15 --> [LedgerCondensed][acc=6422] Q4-OtherAccounts => 13 rows  |  0.0015s
1 - 2026-09-24 15:38:15 --> [LedgerCondensed][acc=6422] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.6%)
   Q2-OpeningBalance           0.0002s   (  1.9%)
   Q3-MainLedger               0.0098s   ( 75.6%)
   Q4-OtherAccounts            0.0015s   ( 11.9%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0130s   (100%)
[LedgerCondensed][acc=6422] ── FUNCTION END ──
1 - 2026-09-24 15:38:26 --> [LedgerCondensed][acc=6422] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:38:26 --> [LedgerCondensed][acc=6422] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:38:26 --> [LedgerCondensed][acc=6422] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 15:38:26 --> [LedgerCondensed][acc=6422] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-24 15:38:26 --> [LedgerCondensed][acc=6422] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 15:38:26 --> [LedgerCondensed][acc=6422] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 28.4%)
   Q2-OpeningBalance           0.0003s   (  8.5%)
   Q3-MainLedger               0.0016s   ( 52.2%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0031s   (100%)
[LedgerCondensed][acc=6422] ── FUNCTION END ──
1 - 2026-09-24 15:38:45 --> [LedgerCondensed][acc=6423] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:38:45 --> [LedgerCondensed][acc=6423] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-24 15:38:45 --> [LedgerCondensed][acc=6423] Q2-OpeningBalance => 35650.00  |  0.0002s
1 - 2026-09-24 15:38:45 --> [LedgerCondensed][acc=6423] Q3-MainLedger => rows=15  total=15  |  0.0118s
1 - 2026-09-24 15:38:45 --> [LedgerCondensed][acc=6423] Q4-OtherAccounts => 15 rows  |  0.0013s
1 - 2026-09-24 15:38:45 --> [LedgerCondensed][acc=6423] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  7.2%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0118s   ( 79.3%)
   Q4-OtherAccounts            0.0013s   (  8.8%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0149s   (100%)
[LedgerCondensed][acc=6423] ── FUNCTION END ──
1 - 2026-09-24 15:38:54 --> [LedgerCondensed][acc=6423] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:38:54 --> [LedgerCondensed][acc=6423] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:38:54 --> [LedgerCondensed][acc=6423] Q2-OpeningBalance => 35650.00  |  0.0002s
1 - 2026-09-24 15:38:54 --> [LedgerCondensed][acc=6423] Q3-MainLedger => rows=100  total=140  |  0.0102s
1 - 2026-09-24 15:38:54 --> [LedgerCondensed][acc=6423] Q4-OtherAccounts => 100 rows  |  0.0037s
1 - 2026-09-24 15:38:54 --> [LedgerCondensed][acc=6423] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.6%)
   Q2-OpeningBalance           0.0002s   (  1.5%)
   Q3-MainLedger               0.0102s   ( 64.7%)
   Q4-OtherAccounts            0.0037s   ( 23.4%)
   BuildRecords                0.0004s   (  2.3%)
   TOTAL                       0.0158s   (100%)
[LedgerCondensed][acc=6423] ── FUNCTION END ──
1 - 2026-09-24 15:48:28 --> [LedgerCondensed][acc=6526] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:48:28 --> [LedgerCondensed][acc=6526] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:48:28 --> [LedgerCondensed][acc=6526] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 15:48:28 --> [LedgerCondensed][acc=6526] Q3-MainLedger => rows=0  total=0  |  0.0017s
1 - 2026-09-24 15:48:28 --> [LedgerCondensed][acc=6526] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 15:48:28 --> [LedgerCondensed][acc=6526] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 28.9%)
   Q2-OpeningBalance           0.0002s   (  7.6%)
   Q3-MainLedger               0.0017s   ( 52.6%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0032s   (100%)
[LedgerCondensed][acc=6526] ── FUNCTION END ──
1 - 2026-09-24 15:48:48 --> [LedgerCondensed][acc=6537] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:48:48 --> [LedgerCondensed][acc=6537] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:48:48 --> [LedgerCondensed][acc=6537] Q2-OpeningBalance => 15182.67  |  0.0002s
1 - 2026-09-24 15:48:48 --> [LedgerCondensed][acc=6537] Q3-MainLedger => rows=11  total=11  |  0.0119s
1 - 2026-09-24 15:48:48 --> [LedgerCondensed][acc=6537] Q4-OtherAccounts => 11 rows  |  0.0014s
1 - 2026-09-24 15:48:48 --> [LedgerCondensed][acc=6537] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.0%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0119s   ( 79.5%)
   Q4-OtherAccounts            0.0014s   (  9.5%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0150s   (100%)
[LedgerCondensed][acc=6537] ── FUNCTION END ──
1 - 2026-09-24 15:49:12 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:49:12 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-24 15:49:12 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0002s
1 - 2026-09-24 15:49:12 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=100  total=622  |  0.0146s
1 - 2026-09-24 15:49:12 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 100 rows  |  0.0033s
1 - 2026-09-24 15:49:12 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  5.2%)
   Q2-OpeningBalance           0.0002s   (  1.2%)
   Q3-MainLedger               0.0146s   ( 72.1%)
   Q4-OtherAccounts            0.0033s   ( 16.3%)
   BuildRecords                0.0005s   (  2.6%)
   TOTAL                       0.0203s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 15:51:42 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-09-01  to=2025-09-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:51:42 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 333  |  0.0028s
1 - 2026-09-24 15:51:42 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 324508.95  |  0.0022s
1 - 2026-09-24 15:51:42 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=44  total=44  |  0.0118s
1 - 2026-09-24 15:51:42 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 44 rows  |  0.0026s
1 - 2026-09-24 15:51:42 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0028s   ( 14.0%)
   Q2-OpeningBalance           0.0022s   ( 10.7%)
   Q3-MainLedger               0.0118s   ( 58.9%)
   Q4-OtherAccounts            0.0026s   ( 13.0%)
   BuildRecords                0.0002s   (  1.0%)
   TOTAL                       0.0201s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 15:53:07 --> [LedgerCondensed][acc=6423] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:53:07 --> [LedgerCondensed][acc=6423] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 15:53:07 --> [LedgerCondensed][acc=6423] Q2-OpeningBalance => 35650.00  |  0.0003s
1 - 2026-09-24 15:53:07 --> [LedgerCondensed][acc=6423] Q3-MainLedger => rows=100  total=140  |  0.0099s
1 - 2026-09-24 15:53:07 --> [LedgerCondensed][acc=6423] Q4-OtherAccounts => 100 rows  |  0.0029s
1 - 2026-09-24 15:53:07 --> [LedgerCondensed][acc=6423] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.9%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0099s   ( 66.3%)
   Q4-OtherAccounts            0.0029s   ( 19.8%)
   BuildRecords                0.0004s   (  2.5%)
   TOTAL                       0.0149s   (100%)
[LedgerCondensed][acc=6423] ── FUNCTION END ──
1 - 2026-09-24 15:55:34 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-09-01  to=2025-09-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:55:34 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 333  |  0.0026s
1 - 2026-09-24 15:55:34 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 324508.95  |  0.0019s
1 - 2026-09-24 15:55:34 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=44  total=44  |  0.0118s
1 - 2026-09-24 15:55:34 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 44 rows  |  0.0031s
1 - 2026-09-24 15:55:34 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0026s   ( 12.7%)
   Q2-OpeningBalance           0.0019s   (  9.4%)
   Q3-MainLedger               0.0118s   ( 59.0%)
   Q4-OtherAccounts            0.0031s   ( 15.5%)
   BuildRecords                0.0002s   (  0.8%)
   TOTAL                       0.0200s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 15:57:21 --> [LedgerCondensed][acc=6423] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:57:21 --> [LedgerCondensed][acc=6423] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 15:57:21 --> [LedgerCondensed][acc=6423] Q2-OpeningBalance => 35650.00  |  0.0002s
1 - 2026-09-24 15:57:21 --> [LedgerCondensed][acc=6423] Q3-MainLedger => rows=100  total=140  |  0.0093s
1 - 2026-09-24 15:57:21 --> [LedgerCondensed][acc=6423] Q4-OtherAccounts => 100 rows  |  0.0035s
1 - 2026-09-24 15:57:21 --> [LedgerCondensed][acc=6423] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.4%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0093s   ( 62.8%)
   Q4-OtherAccounts            0.0035s   ( 23.6%)
   BuildRecords                0.0004s   (  2.5%)
   TOTAL                       0.0148s   (100%)
[LedgerCondensed][acc=6423] ── FUNCTION END ──
1 - 2026-09-24 15:57:28 --> [LedgerCondensed][acc=6423] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:57:28 --> [LedgerCondensed][acc=6423] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:57:28 --> [LedgerCondensed][acc=6423] Q2-OpeningBalance => 35650.00  |  0.0002s
1 - 2026-09-24 15:57:28 --> [LedgerCondensed][acc=6423] Q3-MainLedger => rows=100  total=140  |  0.0091s
1 - 2026-09-24 15:57:28 --> [LedgerCondensed][acc=6423] Q4-OtherAccounts => 100 rows  |  0.0028s
1 - 2026-09-24 15:57:28 --> [LedgerCondensed][acc=6423] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.8%)
   Q2-OpeningBalance           0.0002s   (  1.7%)
   Q3-MainLedger               0.0091s   ( 65.6%)
   Q4-OtherAccounts            0.0028s   ( 20.2%)
   BuildRecords                0.0004s   (  2.8%)
   TOTAL                       0.0138s   (100%)
[LedgerCondensed][acc=6423] ── FUNCTION END ──
1 - 2026-09-24 15:57:34 --> [LedgerCondensed][acc=6423] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:57:34 --> [LedgerCondensed][acc=6423] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 15:57:34 --> [LedgerCondensed][acc=6423] Q2-OpeningBalance => 35650.00  |  0.0002s
1 - 2026-09-24 15:57:34 --> [LedgerCondensed][acc=6423] Q3-MainLedger => rows=100  total=140  |  0.009s
1 - 2026-09-24 15:57:34 --> [LedgerCondensed][acc=6423] Q4-OtherAccounts => 100 rows  |  0.0031s
1 - 2026-09-24 15:57:34 --> [LedgerCondensed][acc=6423] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.0%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0090s   ( 64.8%)
   Q4-OtherAccounts            0.0031s   ( 22.2%)
   BuildRecords                0.0004s   (  2.6%)
   TOTAL                       0.0138s   (100%)
[LedgerCondensed][acc=6423] ── FUNCTION END ──
1 - 2026-09-24 15:57:49 --> [LedgerCondensed][acc=6423] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:57:49 --> [LedgerCondensed][acc=6423] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 15:57:49 --> [LedgerCondensed][acc=6423] Q2-OpeningBalance => 35650.00  |  0.0003s
1 - 2026-09-24 15:57:49 --> [LedgerCondensed][acc=6423] Q3-MainLedger => rows=100  total=140  |  0.0088s
1 - 2026-09-24 15:57:49 --> [LedgerCondensed][acc=6423] Q4-OtherAccounts => 100 rows  |  0.0027s
1 - 2026-09-24 15:57:49 --> [LedgerCondensed][acc=6423] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.2%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0088s   ( 65.1%)
   Q4-OtherAccounts            0.0027s   ( 20.2%)
   BuildRecords                0.0004s   (  2.6%)
   TOTAL                       0.0136s   (100%)
[LedgerCondensed][acc=6423] ── FUNCTION END ──
1 - 2026-09-24 15:58:07 --> [LedgerCondensed][acc=6423] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:58:07 --> [LedgerCondensed][acc=6423] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 15:58:07 --> [LedgerCondensed][acc=6423] Q2-OpeningBalance => 35650.00  |  0.0003s
1 - 2026-09-24 15:58:07 --> [LedgerCondensed][acc=6423] Q3-MainLedger => rows=100  total=140  |  0.0092s
1 - 2026-09-24 15:58:07 --> [LedgerCondensed][acc=6423] Q4-OtherAccounts => 100 rows  |  0.0028s
1 - 2026-09-24 15:58:07 --> [LedgerCondensed][acc=6423] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.1%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0092s   ( 65.6%)
   Q4-OtherAccounts            0.0028s   ( 19.7%)
   BuildRecords                0.0004s   (  2.6%)
   TOTAL                       0.0141s   (100%)
[LedgerCondensed][acc=6423] ── FUNCTION END ──
1 - 2026-09-24 15:58:17 --> [LedgerCondensed][acc=6423] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:58:17 --> [LedgerCondensed][acc=6423] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:58:17 --> [LedgerCondensed][acc=6423] Q2-OpeningBalance => 35650.00  |  0.0002s
1 - 2026-09-24 15:58:17 --> [LedgerCondensed][acc=6423] Q3-MainLedger => rows=100  total=140  |  0.009s
1 - 2026-09-24 15:58:17 --> [LedgerCondensed][acc=6423] Q4-OtherAccounts => 100 rows  |  0.0027s
1 - 2026-09-24 15:58:17 --> [LedgerCondensed][acc=6423] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.9%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0090s   ( 65.9%)
   Q4-OtherAccounts            0.0027s   ( 19.9%)
   BuildRecords                0.0004s   (  2.9%)
   TOTAL                       0.0136s   (100%)
[LedgerCondensed][acc=6423] ── FUNCTION END ──
1 - 2026-09-24 15:58:27 --> [LedgerCondensed][acc=6423] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:58:27 --> [LedgerCondensed][acc=6423] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 15:58:27 --> [LedgerCondensed][acc=6423] Q2-OpeningBalance => 35650.00  |  0.0002s
1 - 2026-09-24 15:58:27 --> [LedgerCondensed][acc=6423] Q3-MainLedger => rows=100  total=139  |  0.0092s
1 - 2026-09-24 15:58:27 --> [LedgerCondensed][acc=6423] Q4-OtherAccounts => 100 rows  |  0.0031s
1 - 2026-09-24 15:58:27 --> [LedgerCondensed][acc=6423] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.7%)
   Q2-OpeningBalance           0.0002s   (  1.7%)
   Q3-MainLedger               0.0092s   ( 64.6%)
   Q4-OtherAccounts            0.0031s   ( 21.5%)
   BuildRecords                0.0003s   (  2.4%)
   TOTAL                       0.0143s   (100%)
[LedgerCondensed][acc=6423] ── FUNCTION END ──
1 - 2026-09-24 15:58:37 --> [LedgerCondensed][acc=6423] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:58:37 --> [LedgerCondensed][acc=6423] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:58:37 --> [LedgerCondensed][acc=6423] Q2-OpeningBalance => 35650.00  |  0.0003s
1 - 2026-09-24 15:58:37 --> [LedgerCondensed][acc=6423] Q3-MainLedger => rows=100  total=138  |  0.0094s
1 - 2026-09-24 15:58:37 --> [LedgerCondensed][acc=6423] Q4-OtherAccounts => 100 rows  |  0.0028s
1 - 2026-09-24 15:58:37 --> [LedgerCondensed][acc=6423] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.7%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0094s   ( 66.3%)
   Q4-OtherAccounts            0.0028s   ( 19.6%)
   BuildRecords                0.0004s   (  2.5%)
   TOTAL                       0.0142s   (100%)
[LedgerCondensed][acc=6423] ── FUNCTION END ──
1 - 2026-09-24 15:58:46 --> [LedgerCondensed][acc=6423] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:58:46 --> [LedgerCondensed][acc=6423] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:58:46 --> [LedgerCondensed][acc=6423] Q2-OpeningBalance => 35650.00  |  0.0003s
1 - 2026-09-24 15:58:46 --> [LedgerCondensed][acc=6423] Q3-MainLedger => rows=100  total=137  |  0.0091s
1 - 2026-09-24 15:58:46 --> [LedgerCondensed][acc=6423] Q4-OtherAccounts => 100 rows  |  0.0028s
1 - 2026-09-24 15:58:46 --> [LedgerCondensed][acc=6423] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.7%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0091s   ( 65.0%)
   Q4-OtherAccounts            0.0028s   ( 20.4%)
   BuildRecords                0.0004s   (  2.9%)
   TOTAL                       0.0140s   (100%)
[LedgerCondensed][acc=6423] ── FUNCTION END ──
1 - 2026-09-24 15:59:14 --> [LedgerCondensed][acc=6423] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:59:14 --> [LedgerCondensed][acc=6423] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 15:59:14 --> [LedgerCondensed][acc=6423] Q2-OpeningBalance => 35650.00  |  0.0003s
1 - 2026-09-24 15:59:14 --> [LedgerCondensed][acc=6423] Q3-MainLedger => rows=15  total=15  |  0.0089s
1 - 2026-09-24 15:59:14 --> [LedgerCondensed][acc=6423] Q4-OtherAccounts => 15 rows  |  0.0013s
1 - 2026-09-24 15:59:14 --> [LedgerCondensed][acc=6423] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.2%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0089s   ( 74.4%)
   Q4-OtherAccounts            0.0013s   ( 10.6%)
   BuildRecords                0.0001s   (  0.6%)
   TOTAL                       0.0120s   (100%)
[LedgerCondensed][acc=6423] ── FUNCTION END ──
1 - 2026-09-24 15:59:40 --> [LedgerCondensed][acc=6424] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:59:40 --> [LedgerCondensed][acc=6424] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 15:59:40 --> [LedgerCondensed][acc=6424] Q2-OpeningBalance => -13465.00  |  0.0003s
1 - 2026-09-24 15:59:40 --> [LedgerCondensed][acc=6424] Q3-MainLedger => rows=8  total=8  |  0.0087s
1 - 2026-09-24 15:59:40 --> [LedgerCondensed][acc=6424] Q4-OtherAccounts => 8 rows  |  0.0011s
1 - 2026-09-24 15:59:40 --> [LedgerCondensed][acc=6424] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.7%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0087s   ( 75.4%)
   Q4-OtherAccounts            0.0011s   (  9.9%)
   BuildRecords                0.0000s   (  0.4%)
   TOTAL                       0.0116s   (100%)
[LedgerCondensed][acc=6424] ── FUNCTION END ──
1 - 2026-09-24 15:59:48 --> [LedgerCondensed][acc=6424] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 15:59:48 --> [LedgerCondensed][acc=6424] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 15:59:48 --> [LedgerCondensed][acc=6424] Q2-OpeningBalance => -13465.00  |  0.0003s
1 - 2026-09-24 15:59:48 --> [LedgerCondensed][acc=6424] Q3-MainLedger => rows=64  total=64  |  0.0089s
1 - 2026-09-24 15:59:48 --> [LedgerCondensed][acc=6424] Q4-OtherAccounts => 64 rows  |  0.003s
1 - 2026-09-24 15:59:48 --> [LedgerCondensed][acc=6424] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.3%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0089s   ( 64.8%)
   Q4-OtherAccounts            0.0030s   ( 21.6%)
   BuildRecords                0.0003s   (  2.3%)
   TOTAL                       0.0137s   (100%)
[LedgerCondensed][acc=6424] ── FUNCTION END ──
1 - 2026-09-24 16:01:59 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-09-01  to=2025-09-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:01:59 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 333  |  0.0025s
1 - 2026-09-24 16:01:59 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 324508.95  |  0.0018s
1 - 2026-09-24 16:01:59 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=44  total=44  |  0.0119s
1 - 2026-09-24 16:01:59 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 44 rows  |  0.0026s
1 - 2026-09-24 16:01:59 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0025s   ( 13.0%)
   Q2-OpeningBalance           0.0018s   (  9.2%)
   Q3-MainLedger               0.0119s   ( 61.0%)
   Q4-OtherAccounts            0.0026s   ( 13.1%)
   BuildRecords                0.0002s   (  1.1%)
   TOTAL                       0.0195s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 16:02:33 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:02:33 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-24 16:02:33 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0003s
1 - 2026-09-24 16:02:33 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=100  total=622  |  0.0111s
1 - 2026-09-24 16:02:33 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 100 rows  |  0.0024s
1 - 2026-09-24 16:02:33 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  6.7%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0111s   ( 70.6%)
   Q4-OtherAccounts            0.0024s   ( 15.4%)
   BuildRecords                0.0003s   (  2.2%)
   TOTAL                       0.0157s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 16:03:52 --> [LedgerCondensed][acc=6424] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:03:52 --> [LedgerCondensed][acc=6424] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 16:03:52 --> [LedgerCondensed][acc=6424] Q2-OpeningBalance => -13465.00  |  0.0003s
1 - 2026-09-24 16:03:52 --> [LedgerCondensed][acc=6424] Q3-MainLedger => rows=65  total=65  |  0.0095s
1 - 2026-09-24 16:03:52 --> [LedgerCondensed][acc=6424] Q4-OtherAccounts => 65 rows  |  0.0029s
1 - 2026-09-24 16:03:52 --> [LedgerCondensed][acc=6424] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.7%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0095s   ( 66.7%)
   Q4-OtherAccounts            0.0029s   ( 20.2%)
   BuildRecords                0.0002s   (  1.7%)
   TOTAL                       0.0143s   (100%)
[LedgerCondensed][acc=6424] ── FUNCTION END ──
1 - 2026-09-24 16:03:57 --> [LedgerCondensed][acc=6424] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:03:57 --> [LedgerCondensed][acc=6424] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:03:57 --> [LedgerCondensed][acc=6424] Q2-OpeningBalance => -13465.00  |  0.0003s
1 - 2026-09-24 16:03:57 --> [LedgerCondensed][acc=6424] Q3-MainLedger => rows=8  total=8  |  0.0093s
1 - 2026-09-24 16:03:57 --> [LedgerCondensed][acc=6424] Q4-OtherAccounts => 8 rows  |  0.0012s
1 - 2026-09-24 16:03:57 --> [LedgerCondensed][acc=6424] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.3%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0093s   ( 77.2%)
   Q4-OtherAccounts            0.0012s   ( 10.1%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0120s   (100%)
[LedgerCondensed][acc=6424] ── FUNCTION END ──
1 - 2026-09-24 16:04:17 --> [LedgerCondensed][acc=6426] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:04:17 --> [LedgerCondensed][acc=6426] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:04:17 --> [LedgerCondensed][acc=6426] Q2-OpeningBalance => -23550.00  |  0.0002s
1 - 2026-09-24 16:04:17 --> [LedgerCondensed][acc=6426] Q3-MainLedger => rows=7  total=7  |  0.0095s
1 - 2026-09-24 16:04:17 --> [LedgerCondensed][acc=6426] Q4-OtherAccounts => 7 rows  |  0.0011s
1 - 2026-09-24 16:04:17 --> [LedgerCondensed][acc=6426] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.1%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0095s   ( 78.3%)
   Q4-OtherAccounts            0.0011s   (  9.0%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0122s   (100%)
[LedgerCondensed][acc=6426] ── FUNCTION END ──
1 - 2026-09-24 16:04:24 --> [LedgerCondensed][acc=6426] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:04:24 --> [LedgerCondensed][acc=6426] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:04:24 --> [LedgerCondensed][acc=6426] Q2-OpeningBalance => -23550.00  |  0.0003s
1 - 2026-09-24 16:04:24 --> [LedgerCondensed][acc=6426] Q3-MainLedger => rows=45  total=45  |  0.0117s
1 - 2026-09-24 16:04:24 --> [LedgerCondensed][acc=6426] Q4-OtherAccounts => 45 rows  |  0.003s
1 - 2026-09-24 16:04:24 --> [LedgerCondensed][acc=6426] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.4%)
   Q2-OpeningBalance           0.0003s   (  1.6%)
   Q3-MainLedger               0.0117s   ( 71.1%)
   Q4-OtherAccounts            0.0030s   ( 18.1%)
   BuildRecords                0.0002s   (  1.2%)
   TOTAL                       0.0165s   (100%)
[LedgerCondensed][acc=6426] ── FUNCTION END ──
1 - 2026-09-24 16:07:51 --> [LedgerCondensed][acc=6426] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:07:51 --> [LedgerCondensed][acc=6426] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 16:07:51 --> [LedgerCondensed][acc=6426] Q2-OpeningBalance => -23550.00  |  0.0003s
1 - 2026-09-24 16:07:51 --> [LedgerCondensed][acc=6426] Q3-MainLedger => rows=45  total=45  |  0.0097s
1 - 2026-09-24 16:07:51 --> [LedgerCondensed][acc=6426] Q4-OtherAccounts => 45 rows  |  0.0028s
1 - 2026-09-24 16:07:51 --> [LedgerCondensed][acc=6426] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.7%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0097s   ( 67.4%)
   Q4-OtherAccounts            0.0028s   ( 19.2%)
   BuildRecords                0.0002s   (  1.7%)
   TOTAL                       0.0144s   (100%)
[LedgerCondensed][acc=6426] ── FUNCTION END ──
1 - 2026-09-24 16:08:36 --> [LedgerCondensed][acc=6426] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:08:36 --> [LedgerCondensed][acc=6426] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:08:36 --> [LedgerCondensed][acc=6426] Q2-OpeningBalance => -23550.00  |  0.0002s
1 - 2026-09-24 16:08:36 --> [LedgerCondensed][acc=6426] Q3-MainLedger => rows=44  total=44  |  0.0097s
1 - 2026-09-24 16:08:36 --> [LedgerCondensed][acc=6426] Q4-OtherAccounts => 44 rows  |  0.0025s
1 - 2026-09-24 16:08:36 --> [LedgerCondensed][acc=6426] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.8%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0097s   ( 69.2%)
   Q4-OtherAccounts            0.0025s   ( 18.0%)
   BuildRecords                0.0002s   (  1.2%)
   TOTAL                       0.0140s   (100%)
[LedgerCondensed][acc=6426] ── FUNCTION END ──
1 - 2026-09-24 16:08:46 --> [LedgerCondensed][acc=6426] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:08:46 --> [LedgerCondensed][acc=6426] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 16:08:46 --> [LedgerCondensed][acc=6426] Q2-OpeningBalance => -23550.00  |  0.0003s
1 - 2026-09-24 16:08:46 --> [LedgerCondensed][acc=6426] Q3-MainLedger => rows=43  total=43  |  0.0095s
1 - 2026-09-24 16:08:46 --> [LedgerCondensed][acc=6426] Q4-OtherAccounts => 43 rows  |  0.0023s
1 - 2026-09-24 16:08:46 --> [LedgerCondensed][acc=6426] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.3%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0095s   ( 69.6%)
   Q4-OtherAccounts            0.0023s   ( 16.9%)
   BuildRecords                0.0002s   (  1.3%)
   TOTAL                       0.0136s   (100%)
[LedgerCondensed][acc=6426] ── FUNCTION END ──
1 - 2026-09-24 16:08:52 --> [LedgerCondensed][acc=6426] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:08:52 --> [LedgerCondensed][acc=6426] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 16:08:52 --> [LedgerCondensed][acc=6426] Q2-OpeningBalance => -23550.00  |  0.0003s
1 - 2026-09-24 16:08:52 --> [LedgerCondensed][acc=6426] Q3-MainLedger => rows=43  total=43  |  0.0094s
1 - 2026-09-24 16:08:52 --> [LedgerCondensed][acc=6426] Q4-OtherAccounts => 43 rows  |  0.0023s
1 - 2026-09-24 16:08:52 --> [LedgerCondensed][acc=6426] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.3%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0094s   ( 70.1%)
   Q4-OtherAccounts            0.0023s   ( 17.3%)
   BuildRecords                0.0002s   (  1.3%)
   TOTAL                       0.0134s   (100%)
[LedgerCondensed][acc=6426] ── FUNCTION END ──
1 - 2026-09-24 16:09:44 --> [LedgerCondensed][acc=6426] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:09:44 --> [LedgerCondensed][acc=6426] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:09:44 --> [LedgerCondensed][acc=6426] Q2-OpeningBalance => -23550.00  |  0.0002s
1 - 2026-09-24 16:09:44 --> [LedgerCondensed][acc=6426] Q3-MainLedger => rows=43  total=43  |  0.0092s
1 - 2026-09-24 16:09:44 --> [LedgerCondensed][acc=6426] Q4-OtherAccounts => 43 rows  |  0.0027s
1 - 2026-09-24 16:09:44 --> [LedgerCondensed][acc=6426] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.5%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0092s   ( 67.8%)
   Q4-OtherAccounts            0.0027s   ( 19.7%)
   BuildRecords                0.0002s   (  1.2%)
   TOTAL                       0.0136s   (100%)
[LedgerCondensed][acc=6426] ── FUNCTION END ──
1 - 2026-09-24 16:10:26 --> [LedgerCondensed][acc=6426] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:10:26 --> [LedgerCondensed][acc=6426] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 16:10:26 --> [LedgerCondensed][acc=6426] Q2-OpeningBalance => -23550.00  |  0.0002s
1 - 2026-09-24 16:10:26 --> [LedgerCondensed][acc=6426] Q3-MainLedger => rows=7  total=7  |  0.0119s
1 - 2026-09-24 16:10:26 --> [LedgerCondensed][acc=6426] Q4-OtherAccounts => 7 rows  |  0.0013s
1 - 2026-09-24 16:10:26 --> [LedgerCondensed][acc=6426] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.4%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0119s   ( 79.6%)
   Q4-OtherAccounts            0.0013s   (  8.9%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0150s   (100%)
[LedgerCondensed][acc=6426] ── FUNCTION END ──
1 - 2026-09-24 16:10:39 --> [LedgerCondensed][acc=6427] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:10:39 --> [LedgerCondensed][acc=6427] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 16:10:39 --> [LedgerCondensed][acc=6427] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 16:10:39 --> [LedgerCondensed][acc=6427] Q3-MainLedger => rows=0  total=0  |  0.0014s
1 - 2026-09-24 16:10:39 --> [LedgerCondensed][acc=6427] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 16:10:39 --> [LedgerCondensed][acc=6427] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 31.6%)
   Q2-OpeningBalance           0.0003s   (  8.4%)
   Q3-MainLedger               0.0014s   ( 45.8%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0031s   (100%)
[LedgerCondensed][acc=6427] ── FUNCTION END ──
1 - 2026-09-24 16:10:44 --> [LedgerCondensed][acc=6427] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:10:44 --> [LedgerCondensed][acc=6427] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 16:10:44 --> [LedgerCondensed][acc=6427] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 16:10:44 --> [LedgerCondensed][acc=6427] Q3-MainLedger => rows=31  total=31  |  0.0119s
1 - 2026-09-24 16:10:44 --> [LedgerCondensed][acc=6427] Q4-OtherAccounts => 31 rows  |  0.0019s
1 - 2026-09-24 16:10:44 --> [LedgerCondensed][acc=6427] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  5.1%)
   Q2-OpeningBalance           0.0002s   (  1.5%)
   Q3-MainLedger               0.0119s   ( 77.3%)
   Q4-OtherAccounts            0.0019s   ( 12.3%)
   BuildRecords                0.0001s   (  0.9%)
   TOTAL                       0.0154s   (100%)
[LedgerCondensed][acc=6427] ── FUNCTION END ──
1 - 2026-09-24 16:12:36 --> [LedgerCondensed][acc=6427] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:12:36 --> [LedgerCondensed][acc=6427] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 16:12:36 --> [LedgerCondensed][acc=6427] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 16:12:36 --> [LedgerCondensed][acc=6427] Q3-MainLedger => rows=32  total=32  |  0.0098s
1 - 2026-09-24 16:12:36 --> [LedgerCondensed][acc=6427] Q4-OtherAccounts => 32 rows  |  0.0018s
1 - 2026-09-24 16:12:36 --> [LedgerCondensed][acc=6427] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.0%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0098s   ( 73.9%)
   Q4-OtherAccounts            0.0018s   ( 13.7%)
   BuildRecords                0.0002s   (  1.2%)
   TOTAL                       0.0133s   (100%)
[LedgerCondensed][acc=6427] ── FUNCTION END ──
1 - 2026-09-24 16:12:40 --> [LedgerCondensed][acc=6427] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:12:40 --> [LedgerCondensed][acc=6427] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 16:12:40 --> [LedgerCondensed][acc=6427] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 16:12:40 --> [LedgerCondensed][acc=6427] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-24 16:12:40 --> [LedgerCondensed][acc=6427] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 16:12:40 --> [LedgerCondensed][acc=6427] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   ( 26.7%)
   Q2-OpeningBalance           0.0002s   (  8.3%)
   Q3-MainLedger               0.0016s   ( 54.1%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0030s   (100%)
[LedgerCondensed][acc=6427] ── FUNCTION END ──
1 - 2026-09-24 16:12:54 --> [LedgerCondensed][acc=6432] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:12:54 --> [LedgerCondensed][acc=6432] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:12:54 --> [LedgerCondensed][acc=6432] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 16:12:54 --> [LedgerCondensed][acc=6432] Q3-MainLedger => rows=6  total=6  |  0.0098s
1 - 2026-09-24 16:12:54 --> [LedgerCondensed][acc=6432] Q4-OtherAccounts => 6 rows  |  0.0012s
1 - 2026-09-24 16:12:54 --> [LedgerCondensed][acc=6432] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.4%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0098s   ( 77.4%)
   Q4-OtherAccounts            0.0012s   (  9.4%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0126s   (100%)
[LedgerCondensed][acc=6432] ── FUNCTION END ──
1 - 2026-09-24 16:13:00 --> [LedgerCondensed][acc=6432] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:13:00 --> [LedgerCondensed][acc=6432] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:13:00 --> [LedgerCondensed][acc=6432] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 16:13:00 --> [LedgerCondensed][acc=6432] Q3-MainLedger => rows=30  total=30  |  0.0099s
1 - 2026-09-24 16:13:00 --> [LedgerCondensed][acc=6432] Q4-OtherAccounts => 30 rows  |  0.0018s
1 - 2026-09-24 16:13:00 --> [LedgerCondensed][acc=6432] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.5%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0099s   ( 74.1%)
   Q4-OtherAccounts            0.0018s   ( 13.5%)
   BuildRecords                0.0001s   (  1.1%)
   TOTAL                       0.0134s   (100%)
[LedgerCondensed][acc=6432] ── FUNCTION END ──
1 - 2026-09-24 16:14:46 --> [LedgerCondensed][acc=6432] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:14:46 --> [LedgerCondensed][acc=6432] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-24 16:14:46 --> [LedgerCondensed][acc=6432] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 16:14:46 --> [LedgerCondensed][acc=6432] Q3-MainLedger => rows=31  total=31  |  0.0094s
1 - 2026-09-24 16:14:46 --> [LedgerCondensed][acc=6432] Q4-OtherAccounts => 31 rows  |  0.0018s
1 - 2026-09-24 16:14:46 --> [LedgerCondensed][acc=6432] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  8.0%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0094s   ( 71.3%)
   Q4-OtherAccounts            0.0018s   ( 14.0%)
   BuildRecords                0.0001s   (  0.9%)
   TOTAL                       0.0132s   (100%)
[LedgerCondensed][acc=6432] ── FUNCTION END ──
1 - 2026-09-24 16:14:51 --> [LedgerCondensed][acc=6432] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:14:51 --> [LedgerCondensed][acc=6432] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 16:14:51 --> [LedgerCondensed][acc=6432] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 16:14:51 --> [LedgerCondensed][acc=6432] Q3-MainLedger => rows=6  total=6  |  0.0091s
1 - 2026-09-24 16:14:51 --> [LedgerCondensed][acc=6432] Q4-OtherAccounts => 6 rows  |  0.0011s
1 - 2026-09-24 16:14:51 --> [LedgerCondensed][acc=6432] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  7.2%)
   Q2-OpeningBalance           0.0002s   (  2.1%)
   Q3-MainLedger               0.0091s   ( 78.1%)
   Q4-OtherAccounts            0.0011s   (  9.4%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0117s   (100%)
[LedgerCondensed][acc=6432] ── FUNCTION END ──
1 - 2026-09-24 16:15:09 --> [LedgerCondensed][acc=6434] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:15:09 --> [LedgerCondensed][acc=6434] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 16:15:09 --> [LedgerCondensed][acc=6434] Q2-OpeningBalance => 10650.00  |  0.0003s
1 - 2026-09-24 16:15:09 --> [LedgerCondensed][acc=6434] Q3-MainLedger => rows=8  total=8  |  0.0088s
1 - 2026-09-24 16:15:09 --> [LedgerCondensed][acc=6434] Q4-OtherAccounts => 8 rows  |  0.0011s
1 - 2026-09-24 16:15:09 --> [LedgerCondensed][acc=6434] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  7.0%)
   Q2-OpeningBalance           0.0003s   (  2.4%)
   Q3-MainLedger               0.0088s   ( 76.5%)
   Q4-OtherAccounts            0.0011s   (  9.6%)
   BuildRecords                0.0001s   (  0.5%)
   TOTAL                       0.0115s   (100%)
[LedgerCondensed][acc=6434] ── FUNCTION END ──
1 - 2026-09-24 16:15:15 --> [LedgerCondensed][acc=6434] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:15:15 --> [LedgerCondensed][acc=6434] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 16:15:15 --> [LedgerCondensed][acc=6434] Q2-OpeningBalance => 10650.00  |  0.0003s
1 - 2026-09-24 16:15:15 --> [LedgerCondensed][acc=6434] Q3-MainLedger => rows=77  total=77  |  0.0101s
1 - 2026-09-24 16:15:15 --> [LedgerCondensed][acc=6434] Q4-OtherAccounts => 77 rows  |  0.0033s
1 - 2026-09-24 16:15:15 --> [LedgerCondensed][acc=6434] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.3%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0101s   ( 66.3%)
   Q4-OtherAccounts            0.0033s   ( 21.8%)
   BuildRecords                0.0002s   (  1.5%)
   TOTAL                       0.0152s   (100%)
[LedgerCondensed][acc=6434] ── FUNCTION END ──
1 - 2026-09-24 16:22:48 --> [LedgerCondensed][acc=6434] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:22:48 --> [LedgerCondensed][acc=6434] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:22:48 --> [LedgerCondensed][acc=6434] Q2-OpeningBalance => 10650.00  |  0.0003s
1 - 2026-09-24 16:22:48 --> [LedgerCondensed][acc=6434] Q3-MainLedger => rows=77  total=77  |  0.0093s
1 - 2026-09-24 16:22:48 --> [LedgerCondensed][acc=6434] Q4-OtherAccounts => 77 rows  |  0.0033s
1 - 2026-09-24 16:22:48 --> [LedgerCondensed][acc=6434] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.0%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0093s   ( 64.3%)
   Q4-OtherAccounts            0.0033s   ( 22.7%)
   BuildRecords                0.0003s   (  1.9%)
   TOTAL                       0.0145s   (100%)
[LedgerCondensed][acc=6434] ── FUNCTION END ──
1 - 2026-09-24 16:23:33 --> [LedgerCondensed][acc=6434] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:23:33 --> [LedgerCondensed][acc=6434] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 16:23:33 --> [LedgerCondensed][acc=6434] Q2-OpeningBalance => 10650.00  |  0.0003s
1 - 2026-09-24 16:23:33 --> [LedgerCondensed][acc=6434] Q3-MainLedger => rows=77  total=77  |  0.0092s
1 - 2026-09-24 16:23:33 --> [LedgerCondensed][acc=6434] Q4-OtherAccounts => 77 rows  |  0.0027s
1 - 2026-09-24 16:23:33 --> [LedgerCondensed][acc=6434] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.4%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0092s   ( 66.1%)
   Q4-OtherAccounts            0.0027s   ( 19.6%)
   BuildRecords                0.0002s   (  1.8%)
   TOTAL                       0.0139s   (100%)
[LedgerCondensed][acc=6434] ── FUNCTION END ──
1 - 2026-09-24 16:44:36 --> [LedgerCondensed][acc=6434] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:44:36 --> [LedgerCondensed][acc=6434] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 16:44:36 --> [LedgerCondensed][acc=6434] Q2-OpeningBalance => 10650.00  |  0.0003s
1 - 2026-09-24 16:44:36 --> [LedgerCondensed][acc=6434] Q3-MainLedger => rows=77  total=77  |  0.0094s
1 - 2026-09-24 16:44:36 --> [LedgerCondensed][acc=6434] Q4-OtherAccounts => 77 rows  |  0.0034s
1 - 2026-09-24 16:44:36 --> [LedgerCondensed][acc=6434] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.6%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0094s   ( 63.6%)
   Q4-OtherAccounts            0.0034s   ( 23.0%)
   BuildRecords                0.0003s   (  2.1%)
   TOTAL                       0.0148s   (100%)
[LedgerCondensed][acc=6434] ── FUNCTION END ──
1 - 2026-09-24 16:46:15 --> [LedgerCondensed][acc=6434] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:46:15 --> [LedgerCondensed][acc=6434] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:46:15 --> [LedgerCondensed][acc=6434] Q2-OpeningBalance => 10650.00  |  0.0003s
1 - 2026-09-24 16:46:15 --> [LedgerCondensed][acc=6434] Q3-MainLedger => rows=77  total=77  |  0.0091s
1 - 2026-09-24 16:46:15 --> [LedgerCondensed][acc=6434] Q4-OtherAccounts => 77 rows  |  0.0029s
1 - 2026-09-24 16:46:15 --> [LedgerCondensed][acc=6434] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.8%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0091s   ( 65.4%)
   Q4-OtherAccounts            0.0029s   ( 21.1%)
   BuildRecords                0.0003s   (  2.0%)
   TOTAL                       0.0140s   (100%)
[LedgerCondensed][acc=6434] ── FUNCTION END ──
1 - 2026-09-24 16:49:06 --> [LedgerCondensed][acc=6434] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:49:06 --> [LedgerCondensed][acc=6434] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-24 16:49:06 --> [LedgerCondensed][acc=6434] Q2-OpeningBalance => 10650.00  |  0.0003s
1 - 2026-09-24 16:49:06 --> [LedgerCondensed][acc=6434] Q3-MainLedger => rows=78  total=78  |  0.0092s
1 - 2026-09-24 16:49:06 --> [LedgerCondensed][acc=6434] Q4-OtherAccounts => 78 rows  |  0.0034s
1 - 2026-09-24 16:49:06 --> [LedgerCondensed][acc=6434] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  7.3%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0092s   ( 62.7%)
   Q4-OtherAccounts            0.0034s   ( 23.3%)
   BuildRecords                0.0003s   (  2.1%)
   TOTAL                       0.0147s   (100%)
[LedgerCondensed][acc=6434] ── FUNCTION END ──
1 - 2026-09-24 16:49:24 --> [LedgerCondensed][acc=6434] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:49:24 --> [LedgerCondensed][acc=6434] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 16:49:24 --> [LedgerCondensed][acc=6434] Q2-OpeningBalance => 10650.00  |  0.0003s
1 - 2026-09-24 16:49:24 --> [LedgerCondensed][acc=6434] Q3-MainLedger => rows=8  total=8  |  0.0095s
1 - 2026-09-24 16:49:24 --> [LedgerCondensed][acc=6434] Q4-OtherAccounts => 8 rows  |  0.0012s
1 - 2026-09-24 16:49:24 --> [LedgerCondensed][acc=6434] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.3%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0095s   ( 76.1%)
   Q4-OtherAccounts            0.0012s   (  9.4%)
   BuildRecords                0.0000s   (  0.4%)
   TOTAL                       0.0125s   (100%)
[LedgerCondensed][acc=6434] ── FUNCTION END ──
1 - 2026-09-24 16:49:38 --> [LedgerCondensed][acc=6435] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:49:38 --> [LedgerCondensed][acc=6435] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 16:49:38 --> [LedgerCondensed][acc=6435] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 16:49:38 --> [LedgerCondensed][acc=6435] Q3-MainLedger => rows=5  total=5  |  0.0091s
1 - 2026-09-24 16:49:38 --> [LedgerCondensed][acc=6435] Q4-OtherAccounts => 5 rows  |  0.0014s
1 - 2026-09-24 16:49:38 --> [LedgerCondensed][acc=6435] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.1%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0091s   ( 75.0%)
   Q4-OtherAccounts            0.0014s   ( 11.1%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0122s   (100%)
[LedgerCondensed][acc=6435] ── FUNCTION END ──
1 - 2026-09-24 16:49:42 --> [LedgerCondensed][acc=6435] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:49:42 --> [LedgerCondensed][acc=6435] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:49:42 --> [LedgerCondensed][acc=6435] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 16:49:42 --> [LedgerCondensed][acc=6435] Q3-MainLedger => rows=16  total=16  |  0.0093s
1 - 2026-09-24 16:49:42 --> [LedgerCondensed][acc=6435] Q4-OtherAccounts => 16 rows  |  0.0014s
1 - 2026-09-24 16:49:42 --> [LedgerCondensed][acc=6435] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.2%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0093s   ( 76.0%)
   Q4-OtherAccounts            0.0014s   ( 11.4%)
   BuildRecords                0.0001s   (  0.6%)
   TOTAL                       0.0123s   (100%)
[LedgerCondensed][acc=6435] ── FUNCTION END ──
1 - 2026-09-24 16:54:07 --> [LedgerCondensed][acc=6435] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:54:07 --> [LedgerCondensed][acc=6435] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:54:07 --> [LedgerCondensed][acc=6435] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 16:54:07 --> [LedgerCondensed][acc=6435] Q3-MainLedger => rows=18  total=18  |  0.0096s
1 - 2026-09-24 16:54:07 --> [LedgerCondensed][acc=6435] Q4-OtherAccounts => 18 rows  |  0.0016s
1 - 2026-09-24 16:54:07 --> [LedgerCondensed][acc=6435] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.3%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0096s   ( 74.4%)
   Q4-OtherAccounts            0.0016s   ( 12.4%)
   BuildRecords                0.0001s   (  0.6%)
   TOTAL                       0.0129s   (100%)
[LedgerCondensed][acc=6435] ── FUNCTION END ──
1 - 2026-09-24 16:55:34 --> [LedgerCondensed][acc=6435] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:55:34 --> [LedgerCondensed][acc=6435] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:55:34 --> [LedgerCondensed][acc=6435] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 16:55:34 --> [LedgerCondensed][acc=6435] Q3-MainLedger => rows=19  total=19  |  0.009s
1 - 2026-09-24 16:55:34 --> [LedgerCondensed][acc=6435] Q4-OtherAccounts => 19 rows  |  0.0016s
1 - 2026-09-24 16:55:34 --> [LedgerCondensed][acc=6435] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.3%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0090s   ( 72.9%)
   Q4-OtherAccounts            0.0016s   ( 13.2%)
   BuildRecords                0.0001s   (  0.7%)
   TOTAL                       0.0123s   (100%)
[LedgerCondensed][acc=6435] ── FUNCTION END ──
1 - 2026-09-24 16:55:40 --> [LedgerCondensed][acc=6435] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:55:40 --> [LedgerCondensed][acc=6435] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:55:40 --> [LedgerCondensed][acc=6435] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 16:55:40 --> [LedgerCondensed][acc=6435] Q3-MainLedger => rows=5  total=5  |  0.009s
1 - 2026-09-24 16:55:40 --> [LedgerCondensed][acc=6435] Q4-OtherAccounts => 5 rows  |  0.0011s
1 - 2026-09-24 16:55:40 --> [LedgerCondensed][acc=6435] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.8%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0090s   ( 76.5%)
   Q4-OtherAccounts            0.0011s   (  9.7%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0117s   (100%)
[LedgerCondensed][acc=6435] ── FUNCTION END ──
1 - 2026-09-24 16:56:32 --> [LedgerCondensed][acc=6440] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:56:32 --> [LedgerCondensed][acc=6440] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:56:32 --> [LedgerCondensed][acc=6440] Q2-OpeningBalance => 63900.00  |  0.0002s
1 - 2026-09-24 16:56:32 --> [LedgerCondensed][acc=6440] Q3-MainLedger => rows=11  total=11  |  0.0122s
1 - 2026-09-24 16:56:32 --> [LedgerCondensed][acc=6440] Q4-OtherAccounts => 11 rows  |  0.0014s
1 - 2026-09-24 16:56:32 --> [LedgerCondensed][acc=6440] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.6%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0122s   ( 80.5%)
   Q4-OtherAccounts            0.0014s   (  9.0%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0151s   (100%)
[LedgerCondensed][acc=6440] ── FUNCTION END ──
1 - 2026-09-24 16:56:36 --> [LedgerCondensed][acc=6440] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 16:56:36 --> [LedgerCondensed][acc=6440] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 16:56:36 --> [LedgerCondensed][acc=6440] Q2-OpeningBalance => 63900.00  |  0.0002s
1 - 2026-09-24 16:56:36 --> [LedgerCondensed][acc=6440] Q3-MainLedger => rows=35  total=35  |  0.0096s
1 - 2026-09-24 16:56:36 --> [LedgerCondensed][acc=6440] Q4-OtherAccounts => 35 rows  |  0.0018s
1 - 2026-09-24 16:56:36 --> [LedgerCondensed][acc=6440] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.8%)
   Q2-OpeningBalance           0.0002s   (  1.7%)
   Q3-MainLedger               0.0096s   ( 73.7%)
   Q4-OtherAccounts            0.0018s   ( 13.9%)
   BuildRecords                0.0001s   (  1.2%)
   TOTAL                       0.0130s   (100%)
[LedgerCondensed][acc=6440] ── FUNCTION END ──
1 - 2026-09-24 17:01:43 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-11-01  to=2025-11-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:01:43 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 425  |  0.0022s
1 - 2026-09-24 17:01:43 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 1138150.8  |  0.0018s
1 - 2026-09-24 17:01:43 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=41  total=41  |  0.0117s
1 - 2026-09-24 17:01:43 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 41 rows  |  0.0023s
1 - 2026-09-24 17:01:43 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0022s   ( 11.6%)
   Q2-OpeningBalance           0.0018s   (  9.8%)
   Q3-MainLedger               0.0117s   ( 62.8%)
   Q4-OtherAccounts            0.0023s   ( 12.5%)
   BuildRecords                0.0002s   (  0.9%)
   TOTAL                       0.0187s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 17:02:57 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-11-01  to=2025-11-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:02:57 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 425  |  0.0018s
1 - 2026-09-24 17:02:57 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 1138150.8  |  0.0018s
1 - 2026-09-24 17:02:57 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=41  total=41  |  0.0098s
1 - 2026-09-24 17:02:57 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 41 rows  |  0.0024s
1 - 2026-09-24 17:02:57 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0018s   ( 10.9%)
   Q2-OpeningBalance           0.0018s   ( 10.9%)
   Q3-MainLedger               0.0098s   ( 59.6%)
   Q4-OtherAccounts            0.0024s   ( 14.7%)
   BuildRecords                0.0002s   (  1.2%)
   TOTAL                       0.0164s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 17:03:00 --> [LedgerCondensed][acc=6440] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:03:00 --> [LedgerCondensed][acc=6440] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:03:00 --> [LedgerCondensed][acc=6440] Q2-OpeningBalance => 63900.00  |  0.0002s
1 - 2026-09-24 17:03:00 --> [LedgerCondensed][acc=6440] Q3-MainLedger => rows=36  total=36  |  0.0098s
1 - 2026-09-24 17:03:00 --> [LedgerCondensed][acc=6440] Q4-OtherAccounts => 36 rows  |  0.0015s
1 - 2026-09-24 17:03:00 --> [LedgerCondensed][acc=6440] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.2%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0098s   ( 75.0%)
   Q4-OtherAccounts            0.0015s   ( 11.5%)
   BuildRecords                0.0001s   (  1.1%)
   TOTAL                       0.0131s   (100%)
[LedgerCondensed][acc=6440] ── FUNCTION END ──
1 - 2026-09-24 17:06:44 --> [LedgerCondensed][acc=6440] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:06:44 --> [LedgerCondensed][acc=6440] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:06:44 --> [LedgerCondensed][acc=6440] Q2-OpeningBalance => 63900.00  |  0.0002s
1 - 2026-09-24 17:06:44 --> [LedgerCondensed][acc=6440] Q3-MainLedger => rows=39  total=39  |  0.0095s
1 - 2026-09-24 17:06:44 --> [LedgerCondensed][acc=6440] Q4-OtherAccounts => 39 rows  |  0.0021s
1 - 2026-09-24 17:06:44 --> [LedgerCondensed][acc=6440] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.7%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0095s   ( 71.0%)
   Q4-OtherAccounts            0.0021s   ( 15.3%)
   BuildRecords                0.0001s   (  1.1%)
   TOTAL                       0.0134s   (100%)
[LedgerCondensed][acc=6440] ── FUNCTION END ──
1 - 2026-09-24 17:07:08 --> [LedgerCondensed][acc=6440] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:07:08 --> [LedgerCondensed][acc=6440] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:07:08 --> [LedgerCondensed][acc=6440] Q2-OpeningBalance => 63900.00  |  0.0003s
1 - 2026-09-24 17:07:08 --> [LedgerCondensed][acc=6440] Q3-MainLedger => rows=11  total=11  |  0.0093s
1 - 2026-09-24 17:07:08 --> [LedgerCondensed][acc=6440] Q4-OtherAccounts => 11 rows  |  0.0011s
1 - 2026-09-24 17:07:08 --> [LedgerCondensed][acc=6440] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.9%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0093s   ( 76.8%)
   Q4-OtherAccounts            0.0011s   (  9.4%)
   BuildRecords                0.0001s   (  0.5%)
   TOTAL                       0.0120s   (100%)
[LedgerCondensed][acc=6440] ── FUNCTION END ──
1 - 2026-09-24 17:07:33 --> [LedgerCondensed][acc=6448] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:07:33 --> [LedgerCondensed][acc=6448] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:07:33 --> [LedgerCondensed][acc=6448] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:07:33 --> [LedgerCondensed][acc=6448] Q3-MainLedger => rows=0  total=0  |  0.0017s
1 - 2026-09-24 17:07:33 --> [LedgerCondensed][acc=6448] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 17:07:33 --> [LedgerCondensed][acc=6448] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 28.2%)
   Q2-OpeningBalance           0.0002s   (  7.4%)
   Q3-MainLedger               0.0017s   ( 52.6%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0032s   (100%)
[LedgerCondensed][acc=6448] ── FUNCTION END ──
1 - 2026-09-24 17:07:36 --> [LedgerCondensed][acc=6448] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:07:36 --> [LedgerCondensed][acc=6448] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:07:36 --> [LedgerCondensed][acc=6448] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:07:36 --> [LedgerCondensed][acc=6448] Q3-MainLedger => rows=9  total=9  |  0.0094s
1 - 2026-09-24 17:07:36 --> [LedgerCondensed][acc=6448] Q4-OtherAccounts => 9 rows  |  0.0014s
1 - 2026-09-24 17:07:36 --> [LedgerCondensed][acc=6448] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.7%)
   Q2-OpeningBalance           0.0002s   (  1.9%)
   Q3-MainLedger               0.0094s   ( 75.8%)
   Q4-OtherAccounts            0.0014s   ( 11.0%)
   BuildRecords                0.0000s   (  0.4%)
   TOTAL                       0.0124s   (100%)
[LedgerCondensed][acc=6448] ── FUNCTION END ──
1 - 2026-09-24 17:14:10 --> Company is gstin type check => 2
1 - 2026-09-24 17:14:10 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 17:14:10 --> Company ID: 98, Voucher Txn ID (Tax Applied): 145814, Bill Sundry ID: 6722
1 - 2026-09-24 17:14:10 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6722","acc_txn_date":"2025-06-23","acc_txn_dr_cr":2,"acc_txn_amt":53.25,"acc_txn_fcy":0,"vch_txn_id":"145814","txn_id":1023766,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 17:14:10 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 17:14:10 --> Company ID: 98, Voucher Txn ID (Tax Applied): 145814, Bill Sundry ID: 6581
1 - 2026-09-24 17:14:10 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6581","acc_txn_date":"2025-06-23","acc_txn_dr_cr":2,"acc_txn_amt":53.25,"acc_txn_fcy":0,"vch_txn_id":"145814","txn_id":1023767,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 17:14:11 --> [LedgerCondensed][acc=6448] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:14:11 --> [LedgerCondensed][acc=6448] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:14:11 --> [LedgerCondensed][acc=6448] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:14:11 --> [LedgerCondensed][acc=6448] Q3-MainLedger => rows=8  total=8  |  0.0101s
1 - 2026-09-24 17:14:11 --> [LedgerCondensed][acc=6448] Q4-OtherAccounts => 8 rows  |  0.0013s
1 - 2026-09-24 17:14:11 --> [LedgerCondensed][acc=6448] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.1%)
   Q2-OpeningBalance           0.0003s   (  2.6%)
   Q3-MainLedger               0.0101s   ( 77.3%)
   Q4-OtherAccounts            0.0013s   (  9.7%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0131s   (100%)
[LedgerCondensed][acc=6448] ── FUNCTION END ──
1 - 2026-09-24 17:15:19 --> [LedgerCondensed][acc=6448] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:15:19 --> [LedgerCondensed][acc=6448] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:15:19 --> [LedgerCondensed][acc=6448] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:15:19 --> [LedgerCondensed][acc=6448] Q3-MainLedger => rows=0  total=0  |  0.0018s
1 - 2026-09-24 17:15:19 --> [LedgerCondensed][acc=6448] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 17:15:19 --> [LedgerCondensed][acc=6448] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 27.4%)
   Q2-OpeningBalance           0.0003s   (  7.9%)
   Q3-MainLedger               0.0018s   ( 51.7%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0034s   (100%)
[LedgerCondensed][acc=6448] ── FUNCTION END ──
1 - 2026-09-24 17:15:51 --> [LedgerCondensed][acc=6450] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:15:51 --> [LedgerCondensed][acc=6450] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:15:51 --> [LedgerCondensed][acc=6450] Q2-OpeningBalance => 8150.00  |  0.0002s
1 - 2026-09-24 17:15:51 --> [LedgerCondensed][acc=6450] Q3-MainLedger => rows=9  total=9  |  0.012s
1 - 2026-09-24 17:15:51 --> [LedgerCondensed][acc=6450] Q4-OtherAccounts => 9 rows  |  0.0013s
1 - 2026-09-24 17:15:51 --> [LedgerCondensed][acc=6450] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.2%)
   Q2-OpeningBalance           0.0002s   (  1.5%)
   Q3-MainLedger               0.0120s   ( 80.3%)
   Q4-OtherAccounts            0.0013s   (  9.0%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0149s   (100%)
[LedgerCondensed][acc=6450] ── FUNCTION END ──
1 - 2026-09-24 17:15:55 --> [LedgerCondensed][acc=6450] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:15:55 --> [LedgerCondensed][acc=6450] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:15:55 --> [LedgerCondensed][acc=6450] Q2-OpeningBalance => 8150.00  |  0.0003s
1 - 2026-09-24 17:15:55 --> [LedgerCondensed][acc=6450] Q3-MainLedger => rows=52  total=52  |  0.0092s
1 - 2026-09-24 17:15:55 --> [LedgerCondensed][acc=6450] Q4-OtherAccounts => 52 rows  |  0.0027s
1 - 2026-09-24 17:15:55 --> [LedgerCondensed][acc=6450] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.0%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0092s   ( 67.2%)
   Q4-OtherAccounts            0.0027s   ( 19.5%)
   BuildRecords                0.0002s   (  1.4%)
   TOTAL                       0.0137s   (100%)
[LedgerCondensed][acc=6450] ── FUNCTION END ──
1 - 2026-09-24 17:19:37 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:19:37 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:19:37 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0002s
1 - 2026-09-24 17:19:37 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=100  total=142  |  0.0122s
1 - 2026-09-24 17:19:37 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 100 rows  |  0.0027s
1 - 2026-09-24 17:19:37 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.9%)
   Q2-OpeningBalance           0.0002s   (  1.4%)
   Q3-MainLedger               0.0122s   ( 72.0%)
   Q4-OtherAccounts            0.0027s   ( 16.3%)
   BuildRecords                0.0003s   (  2.0%)
   TOTAL                       0.0169s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 17:23:11 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:23:11 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-24 17:23:11 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0003s
1 - 2026-09-24 17:23:11 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=100  total=142  |  0.0089s
1 - 2026-09-24 17:23:11 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 100 rows  |  0.0027s
1 - 2026-09-24 17:23:11 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  7.8%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0089s   ( 65.2%)
   Q4-OtherAccounts            0.0027s   ( 19.4%)
   BuildRecords                0.0004s   (  2.8%)
   TOTAL                       0.0137s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 17:23:17 --> [LedgerCondensed][acc=6450] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:23:17 --> [LedgerCondensed][acc=6450] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:23:17 --> [LedgerCondensed][acc=6450] Q2-OpeningBalance => 8150.00  |  0.0003s
1 - 2026-09-24 17:23:17 --> [LedgerCondensed][acc=6450] Q3-MainLedger => rows=53  total=53  |  0.0091s
1 - 2026-09-24 17:23:17 --> [LedgerCondensed][acc=6450] Q4-OtherAccounts => 53 rows  |  0.0027s
1 - 2026-09-24 17:23:17 --> [LedgerCondensed][acc=6450] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.9%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0091s   ( 67.1%)
   Q4-OtherAccounts            0.0027s   ( 19.7%)
   BuildRecords                0.0002s   (  1.6%)
   TOTAL                       0.0135s   (100%)
[LedgerCondensed][acc=6450] ── FUNCTION END ──
1 - 2026-09-24 17:26:48 --> [LedgerCondensed][acc=6450] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:26:48 --> [LedgerCondensed][acc=6450] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:26:48 --> [LedgerCondensed][acc=6450] Q2-OpeningBalance => 8150.00  |  0.0003s
1 - 2026-09-24 17:26:48 --> [LedgerCondensed][acc=6450] Q3-MainLedger => rows=54  total=54  |  0.0093s
1 - 2026-09-24 17:26:49 --> [LedgerCondensed][acc=6450] Q4-OtherAccounts => 54 rows  |  0.0028s
1 - 2026-09-24 17:26:49 --> [LedgerCondensed][acc=6450] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.2%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0093s   ( 67.2%)
   Q4-OtherAccounts            0.0028s   ( 19.8%)
   BuildRecords                0.0002s   (  1.8%)
   TOTAL                       0.0139s   (100%)
[LedgerCondensed][acc=6450] ── FUNCTION END ──
1 - 2026-09-24 17:27:04 --> [LedgerCondensed][acc=6450] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:27:04 --> [LedgerCondensed][acc=6450] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:27:04 --> [LedgerCondensed][acc=6450] Q2-OpeningBalance => 8150.00  |  0.0002s
1 - 2026-09-24 17:27:04 --> [LedgerCondensed][acc=6450] Q3-MainLedger => rows=10  total=10  |  0.0091s
1 - 2026-09-24 17:27:04 --> [LedgerCondensed][acc=6450] Q4-OtherAccounts => 10 rows  |  0.0013s
1 - 2026-09-24 17:27:04 --> [LedgerCondensed][acc=6450] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.6%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0091s   ( 75.2%)
   Q4-OtherAccounts            0.0013s   ( 11.1%)
   BuildRecords                0.0001s   (  0.5%)
   TOTAL                       0.0121s   (100%)
[LedgerCondensed][acc=6450] ── FUNCTION END ──
1 - 2026-09-24 17:28:07 --> [LedgerCondensed][acc=6452] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:28:07 --> [LedgerCondensed][acc=6452] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:28:07 --> [LedgerCondensed][acc=6452] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:28:07 --> [LedgerCondensed][acc=6452] Q3-MainLedger => rows=4  total=4  |  0.0126s
1 - 2026-09-24 17:28:07 --> [LedgerCondensed][acc=6452] Q4-OtherAccounts => 4 rows  |  0.0014s
1 - 2026-09-24 17:28:07 --> [LedgerCondensed][acc=6452] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.6%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0126s   ( 80.0%)
   Q4-OtherAccounts            0.0014s   (  8.9%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0157s   (100%)
[LedgerCondensed][acc=6452] ── FUNCTION END ──
1 - 2026-09-24 17:28:13 --> [LedgerCondensed][acc=6452] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:28:13 --> [LedgerCondensed][acc=6452] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:28:13 --> [LedgerCondensed][acc=6452] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:28:13 --> [LedgerCondensed][acc=6452] Q3-MainLedger => rows=24  total=24  |  0.0095s
1 - 2026-09-24 17:28:13 --> [LedgerCondensed][acc=6452] Q4-OtherAccounts => 24 rows  |  0.0016s
1 - 2026-09-24 17:28:13 --> [LedgerCondensed][acc=6452] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.8%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0095s   ( 74.1%)
   Q4-OtherAccounts            0.0016s   ( 12.3%)
   BuildRecords                0.0001s   (  0.8%)
   TOTAL                       0.0129s   (100%)
[LedgerCondensed][acc=6452] ── FUNCTION END ──
1 - 2026-09-24 17:31:07 --> [LedgerCondensed][acc=6452] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:31:07 --> [LedgerCondensed][acc=6452] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:31:07 --> [LedgerCondensed][acc=6452] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:31:07 --> [LedgerCondensed][acc=6452] Q3-MainLedger => rows=25  total=25  |  0.0099s
1 - 2026-09-24 17:31:07 --> [LedgerCondensed][acc=6452] Q4-OtherAccounts => 25 rows  |  0.0016s
1 - 2026-09-24 17:31:07 --> [LedgerCondensed][acc=6452] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.2%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0099s   ( 75.2%)
   Q4-OtherAccounts            0.0016s   ( 12.0%)
   BuildRecords                0.0001s   (  0.7%)
   TOTAL                       0.0132s   (100%)
[LedgerCondensed][acc=6452] ── FUNCTION END ──
1 - 2026-09-24 17:31:12 --> [LedgerCondensed][acc=6452] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:31:12 --> [LedgerCondensed][acc=6452] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:31:12 --> [LedgerCondensed][acc=6452] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:31:12 --> [LedgerCondensed][acc=6452] Q3-MainLedger => rows=4  total=4  |  0.0098s
1 - 2026-09-24 17:31:12 --> [LedgerCondensed][acc=6452] Q4-OtherAccounts => 4 rows  |  0.0011s
1 - 2026-09-24 17:31:12 --> [LedgerCondensed][acc=6452] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.1%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0098s   ( 78.4%)
   Q4-OtherAccounts            0.0011s   (  9.1%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0125s   (100%)
[LedgerCondensed][acc=6452] ── FUNCTION END ──
1 - 2026-09-24 17:31:33 --> [LedgerCondensed][acc=6461] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:31:33 --> [LedgerCondensed][acc=6461] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:31:33 --> [LedgerCondensed][acc=6461] Q2-OpeningBalance => 31150.00  |  0.0003s
1 - 2026-09-24 17:31:33 --> [LedgerCondensed][acc=6461] Q3-MainLedger => rows=2  total=2  |  0.0095s
1 - 2026-09-24 17:31:33 --> [LedgerCondensed][acc=6461] Q4-OtherAccounts => 2 rows  |  0.0011s
1 - 2026-09-24 17:31:33 --> [LedgerCondensed][acc=6461] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.7%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0095s   ( 77.6%)
   Q4-OtherAccounts            0.0011s   (  9.0%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0122s   (100%)
[LedgerCondensed][acc=6461] ── FUNCTION END ──
1 - 2026-09-24 17:31:38 --> [LedgerCondensed][acc=6461] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:31:38 --> [LedgerCondensed][acc=6461] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:31:38 --> [LedgerCondensed][acc=6461] Q2-OpeningBalance => 31150.00  |  0.0003s
1 - 2026-09-24 17:31:38 --> [LedgerCondensed][acc=6461] Q3-MainLedger => rows=20  total=20  |  0.01s
1 - 2026-09-24 17:31:38 --> [LedgerCondensed][acc=6461] Q4-OtherAccounts => 20 rows  |  0.0015s
1 - 2026-09-24 17:31:38 --> [LedgerCondensed][acc=6461] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.6%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0100s   ( 76.3%)
   Q4-OtherAccounts            0.0015s   ( 11.3%)
   BuildRecords                0.0001s   (  0.9%)
   TOTAL                       0.0131s   (100%)
[LedgerCondensed][acc=6461] ── FUNCTION END ──
1 - 2026-09-24 17:33:34 --> [LedgerCondensed][acc=6461] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:33:34 --> [LedgerCondensed][acc=6461] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:33:34 --> [LedgerCondensed][acc=6461] Q2-OpeningBalance => 31150.00  |  0.0002s
1 - 2026-09-24 17:33:34 --> [LedgerCondensed][acc=6461] Q3-MainLedger => rows=21  total=21  |  0.0096s
1 - 2026-09-24 17:33:34 --> [LedgerCondensed][acc=6461] Q4-OtherAccounts => 21 rows  |  0.002s
1 - 2026-09-24 17:33:34 --> [LedgerCondensed][acc=6461] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.5%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0096s   ( 72.5%)
   Q4-OtherAccounts            0.0020s   ( 14.9%)
   BuildRecords                0.0002s   (  1.1%)
   TOTAL                       0.0133s   (100%)
[LedgerCondensed][acc=6461] ── FUNCTION END ──
1 - 2026-09-24 17:33:37 --> [LedgerCondensed][acc=6461] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:33:37 --> [LedgerCondensed][acc=6461] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 17:33:37 --> [LedgerCondensed][acc=6461] Q2-OpeningBalance => 31150.00  |  0.0002s
1 - 2026-09-24 17:33:37 --> [LedgerCondensed][acc=6461] Q3-MainLedger => rows=2  total=2  |  0.0092s
1 - 2026-09-24 17:33:37 --> [LedgerCondensed][acc=6461] Q4-OtherAccounts => 2 rows  |  0.001s
1 - 2026-09-24 17:33:37 --> [LedgerCondensed][acc=6461] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  7.0%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0092s   ( 79.0%)
   Q4-OtherAccounts            0.0010s   (  8.8%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0117s   (100%)
[LedgerCondensed][acc=6461] ── FUNCTION END ──
1 - 2026-09-24 17:33:51 --> [LedgerCondensed][acc=6462] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:33:51 --> [LedgerCondensed][acc=6462] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:33:51 --> [LedgerCondensed][acc=6462] Q2-OpeningBalance => -14630.00  |  0.0002s
1 - 2026-09-24 17:33:51 --> [LedgerCondensed][acc=6462] Q3-MainLedger => rows=3  total=3  |  0.0096s
1 - 2026-09-24 17:33:51 --> [LedgerCondensed][acc=6462] Q4-OtherAccounts => 3 rows  |  0.0011s
1 - 2026-09-24 17:33:51 --> [LedgerCondensed][acc=6462] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.0%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0096s   ( 78.7%)
   Q4-OtherAccounts            0.0011s   (  8.9%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0122s   (100%)
[LedgerCondensed][acc=6462] ── FUNCTION END ──
1 - 2026-09-24 17:33:56 --> [LedgerCondensed][acc=6462] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:33:56 --> [LedgerCondensed][acc=6462] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:33:56 --> [LedgerCondensed][acc=6462] Q2-OpeningBalance => -14630.00  |  0.0003s
1 - 2026-09-24 17:33:56 --> [LedgerCondensed][acc=6462] Q3-MainLedger => rows=25  total=25  |  0.0097s
1 - 2026-09-24 17:33:56 --> [LedgerCondensed][acc=6462] Q4-OtherAccounts => 25 rows  |  0.0017s
1 - 2026-09-24 17:33:56 --> [LedgerCondensed][acc=6462] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.4%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0097s   ( 74.1%)
   Q4-OtherAccounts            0.0017s   ( 12.7%)
   BuildRecords                0.0001s   (  0.8%)
   TOTAL                       0.0131s   (100%)
[LedgerCondensed][acc=6462] ── FUNCTION END ──
1 - 2026-09-24 17:37:10 --> Company is gstin type check => 2
1 - 2026-09-24 17:37:10 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 17:37:10 --> Company ID: 98, Voucher Txn ID (Tax Applied): 145464, Bill Sundry ID: 6722
1 - 2026-09-24 17:37:10 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6722","acc_txn_date":"2025-05-07","acc_txn_dr_cr":2,"acc_txn_amt":53.25,"acc_txn_fcy":0,"vch_txn_id":"145464","txn_id":1023786,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 17:37:10 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 17:37:10 --> Company ID: 98, Voucher Txn ID (Tax Applied): 145464, Bill Sundry ID: 6581
1 - 2026-09-24 17:37:10 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6581","acc_txn_date":"2025-05-07","acc_txn_dr_cr":2,"acc_txn_amt":53.25,"acc_txn_fcy":0,"vch_txn_id":"145464","txn_id":1023787,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 17:37:11 --> [LedgerCondensed][acc=6462] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:37:11 --> [LedgerCondensed][acc=6462] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:37:11 --> [LedgerCondensed][acc=6462] Q2-OpeningBalance => -14630.00  |  0.0002s
1 - 2026-09-24 17:37:11 --> [LedgerCondensed][acc=6462] Q3-MainLedger => rows=24  total=24  |  0.0093s
1 - 2026-09-24 17:37:11 --> [LedgerCondensed][acc=6462] Q4-OtherAccounts => 24 rows  |  0.0017s
1 - 2026-09-24 17:37:11 --> [LedgerCondensed][acc=6462] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.1%)
   Q2-OpeningBalance           0.0002s   (  1.9%)
   Q3-MainLedger               0.0093s   ( 73.5%)
   Q4-OtherAccounts            0.0017s   ( 13.4%)
   BuildRecords                0.0001s   (  0.9%)
   TOTAL                       0.0127s   (100%)
[LedgerCondensed][acc=6462] ── FUNCTION END ──
1 - 2026-09-24 17:38:29 --> [LedgerCondensed][acc=6462] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:38:29 --> [LedgerCondensed][acc=6462] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:38:29 --> [LedgerCondensed][acc=6462] Q2-OpeningBalance => -14630.00  |  0.0002s
1 - 2026-09-24 17:38:29 --> [LedgerCondensed][acc=6462] Q3-MainLedger => rows=3  total=3  |  0.012s
1 - 2026-09-24 17:38:29 --> [LedgerCondensed][acc=6462] Q4-OtherAccounts => 3 rows  |  0.0016s
1 - 2026-09-24 17:38:29 --> [LedgerCondensed][acc=6462] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.4%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0120s   ( 78.4%)
   Q4-OtherAccounts            0.0016s   ( 10.7%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0153s   (100%)
[LedgerCondensed][acc=6462] ── FUNCTION END ──
1 - 2026-09-24 17:38:51 --> [LedgerCondensed][acc=6463] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:38:51 --> [LedgerCondensed][acc=6463] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:38:51 --> [LedgerCondensed][acc=6463] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:38:51 --> [LedgerCondensed][acc=6463] Q3-MainLedger => rows=6  total=6  |  0.0121s
1 - 2026-09-24 17:38:51 --> [LedgerCondensed][acc=6463] Q4-OtherAccounts => 6 rows  |  0.0014s
1 - 2026-09-24 17:38:51 --> [LedgerCondensed][acc=6463] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.7%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0121s   ( 79.1%)
   Q4-OtherAccounts            0.0014s   (  9.1%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0152s   (100%)
[LedgerCondensed][acc=6463] ── FUNCTION END ──
1 - 2026-09-24 17:38:56 --> [LedgerCondensed][acc=6463] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:38:56 --> [LedgerCondensed][acc=6463] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:38:56 --> [LedgerCondensed][acc=6463] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:38:56 --> [LedgerCondensed][acc=6463] Q3-MainLedger => rows=43  total=43  |  0.01s
1 - 2026-09-24 17:38:56 --> [LedgerCondensed][acc=6463] Q4-OtherAccounts => 43 rows  |  0.0029s
1 - 2026-09-24 17:38:56 --> [LedgerCondensed][acc=6463] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.1%)
   Q2-OpeningBalance           0.0002s   (  1.7%)
   Q3-MainLedger               0.0100s   ( 68.4%)
   Q4-OtherAccounts            0.0029s   ( 19.8%)
   BuildRecords                0.0002s   (  1.5%)
   TOTAL                       0.0146s   (100%)
[LedgerCondensed][acc=6463] ── FUNCTION END ──
1 - 2026-09-24 17:40:11 --> [LedgerCondensed][acc=6463] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:40:11 --> [LedgerCondensed][acc=6463] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:40:11 --> [LedgerCondensed][acc=6463] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:40:11 --> [LedgerCondensed][acc=6463] Q3-MainLedger => rows=43  total=43  |  0.009s
1 - 2026-09-24 17:40:11 --> [LedgerCondensed][acc=6463] Q4-OtherAccounts => 43 rows  |  0.0028s
1 - 2026-09-24 17:40:11 --> [LedgerCondensed][acc=6463] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.7%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0090s   ( 65.7%)
   Q4-OtherAccounts            0.0028s   ( 20.8%)
   BuildRecords                0.0002s   (  1.2%)
   TOTAL                       0.0137s   (100%)
[LedgerCondensed][acc=6463] ── FUNCTION END ──
1 - 2026-09-24 17:40:33 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-11-01  to=2025-11-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:40:33 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 425  |  0.0021s
1 - 2026-09-24 17:40:33 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 1138150.8  |  0.0021s
1 - 2026-09-24 17:40:33 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=41  total=41  |  0.01s
1 - 2026-09-24 17:40:33 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 41 rows  |  0.0025s
1 - 2026-09-24 17:40:33 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0021s   ( 12.1%)
   Q2-OpeningBalance           0.0021s   ( 12.3%)
   Q3-MainLedger               0.0100s   ( 57.7%)
   Q4-OtherAccounts            0.0025s   ( 14.2%)
   BuildRecords                0.0002s   (  0.9%)
   TOTAL                       0.0173s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 17:40:40 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:40:40 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:40:40 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0003s
1 - 2026-09-24 17:40:40 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=100  total=622  |  0.0104s
1 - 2026-09-24 17:40:40 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 100 rows  |  0.0024s
1 - 2026-09-24 17:40:40 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.5%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0104s   ( 70.3%)
   Q4-OtherAccounts            0.0024s   ( 16.0%)
   BuildRecords                0.0004s   (  2.6%)
   TOTAL                       0.0148s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 17:40:52 --> [LedgerCondensed][acc=6463] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:40:52 --> [LedgerCondensed][acc=6463] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:40:52 --> [LedgerCondensed][acc=6463] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:40:52 --> [LedgerCondensed][acc=6463] Q3-MainLedger => rows=6  total=6  |  0.009s
1 - 2026-09-24 17:40:52 --> [LedgerCondensed][acc=6463] Q4-OtherAccounts => 6 rows  |  0.0015s
1 - 2026-09-24 17:40:52 --> [LedgerCondensed][acc=6463] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.5%)
   Q2-OpeningBalance           0.0003s   (  2.8%)
   Q3-MainLedger               0.0090s   ( 74.2%)
   Q4-OtherAccounts            0.0015s   ( 12.1%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0122s   (100%)
[LedgerCondensed][acc=6463] ── FUNCTION END ──
1 - 2026-09-24 17:41:23 --> [LedgerCondensed][acc=6462] ── FUNCTION START ── from=2026-03-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:41:23 --> [LedgerCondensed][acc=6462] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:41:23 --> [LedgerCondensed][acc=6462] Q2-OpeningBalance => 0  |  0.001s
1 - 2026-09-24 17:41:23 --> [LedgerCondensed][acc=6462] Q3-MainLedger => rows=1  total=1  |  0.0121s
1 - 2026-09-24 17:41:23 --> [LedgerCondensed][acc=6462] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-09-24 17:41:23 --> [LedgerCondensed][acc=6462] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.5%)
   Q2-OpeningBalance           0.0010s   (  6.5%)
   Q3-MainLedger               0.0121s   ( 77.9%)
   Q4-OtherAccounts            0.0010s   (  6.4%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0155s   (100%)
[LedgerCondensed][acc=6462] ── FUNCTION END ──
1 - 2026-09-24 17:41:37 --> [LedgerCondensed][acc=6462] ── FUNCTION START ── from=2026-03-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:41:37 --> [LedgerCondensed][acc=6462] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:41:37 --> [LedgerCondensed][acc=6462] Q2-OpeningBalance => 0  |  0.001s
1 - 2026-09-24 17:41:37 --> [LedgerCondensed][acc=6462] Q3-MainLedger => rows=0  total=0  |  0.0014s
1 - 2026-09-24 17:41:37 --> [LedgerCondensed][acc=6462] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 17:41:37 --> [LedgerCondensed][acc=6462] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 24.2%)
   Q2-OpeningBalance           0.0010s   ( 27.1%)
   Q3-MainLedger               0.0014s   ( 37.9%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0038s   (100%)
[LedgerCondensed][acc=6462] ── FUNCTION END ──
1 - 2026-09-24 17:42:33 --> [LedgerCondensed][acc=6466] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:42:33 --> [LedgerCondensed][acc=6466] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-24 17:42:33 --> [LedgerCondensed][acc=6466] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:42:33 --> [LedgerCondensed][acc=6466] Q3-MainLedger => rows=3  total=3  |  0.0118s
1 - 2026-09-24 17:42:33 --> [LedgerCondensed][acc=6466] Q4-OtherAccounts => 3 rows  |  0.0014s
1 - 2026-09-24 17:42:33 --> [LedgerCondensed][acc=6466] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  7.1%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0118s   ( 78.8%)
   Q4-OtherAccounts            0.0014s   (  9.1%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0149s   (100%)
[LedgerCondensed][acc=6466] ── FUNCTION END ──
1 - 2026-09-24 17:42:53 --> [LedgerCondensed][acc=6466] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:42:53 --> [LedgerCondensed][acc=6466] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:42:53 --> [LedgerCondensed][acc=6466] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:42:53 --> [LedgerCondensed][acc=6466] Q3-MainLedger => rows=20  total=20  |  0.0123s
1 - 2026-09-24 17:42:53 --> [LedgerCondensed][acc=6466] Q4-OtherAccounts => 20 rows  |  0.0024s
1 - 2026-09-24 17:42:53 --> [LedgerCondensed][acc=6466] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.5%)
   Q2-OpeningBalance           0.0002s   (  1.4%)
   Q3-MainLedger               0.0123s   ( 74.8%)
   Q4-OtherAccounts            0.0024s   ( 14.7%)
   BuildRecords                0.0001s   (  0.7%)
   TOTAL                       0.0165s   (100%)
[LedgerCondensed][acc=6466] ── FUNCTION END ──
1 - 2026-09-24 17:44:49 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:44:49 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:44:49 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0002s
1 - 2026-09-24 17:44:49 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=100  total=622  |  0.0152s
1 - 2026-09-24 17:44:49 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 100 rows  |  0.0043s
1 - 2026-09-24 17:44:49 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  4.8%)
   Q2-OpeningBalance           0.0002s   (  1.1%)
   Q3-MainLedger               0.0152s   ( 70.1%)
   Q4-OtherAccounts            0.0043s   ( 19.7%)
   BuildRecords                0.0004s   (  2.0%)
   TOTAL                       0.0217s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 17:44:57 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-07-01  to=2025-07-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:44:57 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 265  |  0.0019s
1 - 2026-09-24 17:44:57 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 159442.95  |  0.0019s
1 - 2026-09-24 17:44:57 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=34  total=34  |  0.0095s
1 - 2026-09-24 17:44:57 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 34 rows  |  0.0014s
1 - 2026-09-24 17:44:57 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0019s   ( 12.4%)
   Q2-OpeningBalance           0.0019s   ( 12.3%)
   Q3-MainLedger               0.0095s   ( 62.2%)
   Q4-OtherAccounts            0.0014s   (  9.4%)
   BuildRecords                0.0002s   (  1.2%)
   TOTAL                       0.0152s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 17:45:33 --> [LedgerCondensed][acc=6466] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:45:33 --> [LedgerCondensed][acc=6466] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:45:33 --> [LedgerCondensed][acc=6466] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:45:33 --> [LedgerCondensed][acc=6466] Q3-MainLedger => rows=20  total=20  |  0.0122s
1 - 2026-09-24 17:45:33 --> [LedgerCondensed][acc=6466] Q4-OtherAccounts => 20 rows  |  0.0016s
1 - 2026-09-24 17:45:33 --> [LedgerCondensed][acc=6466] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.9%)
   Q2-OpeningBalance           0.0003s   (  1.6%)
   Q3-MainLedger               0.0122s   ( 78.5%)
   Q4-OtherAccounts            0.0016s   ( 10.4%)
   BuildRecords                0.0001s   (  0.5%)
   TOTAL                       0.0156s   (100%)
[LedgerCondensed][acc=6466] ── FUNCTION END ──
1 - 2026-09-24 17:47:39 --> Company is gstin type check => 2
1 - 2026-09-24 17:47:39 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 17:47:39 --> Company ID: 98, Voucher Txn ID (Tax Applied): 145878, Bill Sundry ID: 6722
1 - 2026-09-24 17:47:39 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6722","acc_txn_date":"2025-07-07","acc_txn_dr_cr":2,"acc_txn_amt":230.75,"acc_txn_fcy":0,"vch_txn_id":"145878","txn_id":1023800,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 17:47:39 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 17:47:39 --> Company ID: 98, Voucher Txn ID (Tax Applied): 145878, Bill Sundry ID: 6581
1 - 2026-09-24 17:47:39 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6581","acc_txn_date":"2025-07-07","acc_txn_dr_cr":2,"acc_txn_amt":230.75,"acc_txn_fcy":0,"vch_txn_id":"145878","txn_id":1023801,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 17:47:40 --> [LedgerCondensed][acc=6466] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:47:40 --> [LedgerCondensed][acc=6466] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:47:40 --> [LedgerCondensed][acc=6466] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:47:40 --> [LedgerCondensed][acc=6466] Q3-MainLedger => rows=19  total=19  |  0.0091s
1 - 2026-09-24 17:47:40 --> [LedgerCondensed][acc=6466] Q4-OtherAccounts => 19 rows  |  0.0016s
1 - 2026-09-24 17:47:40 --> [LedgerCondensed][acc=6466] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.3%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0091s   ( 72.9%)
   Q4-OtherAccounts            0.0016s   ( 12.8%)
   BuildRecords                0.0001s   (  0.7%)
   TOTAL                       0.0125s   (100%)
[LedgerCondensed][acc=6466] ── FUNCTION END ──
1 - 2026-09-24 17:47:46 --> [LedgerCondensed][acc=6466] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:47:46 --> [LedgerCondensed][acc=6466] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:47:46 --> [LedgerCondensed][acc=6466] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:47:46 --> [LedgerCondensed][acc=6466] Q3-MainLedger => rows=3  total=3  |  0.009s
1 - 2026-09-24 17:47:46 --> [LedgerCondensed][acc=6466] Q4-OtherAccounts => 3 rows  |  0.0011s
1 - 2026-09-24 17:47:46 --> [LedgerCondensed][acc=6466] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.4%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0090s   ( 77.8%)
   Q4-OtherAccounts            0.0011s   (  9.4%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0116s   (100%)
[LedgerCondensed][acc=6466] ── FUNCTION END ──
1 - 2026-09-24 17:48:05 --> [LedgerCondensed][acc=6467] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:48:05 --> [LedgerCondensed][acc=6467] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:48:05 --> [LedgerCondensed][acc=6467] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:48:05 --> [LedgerCondensed][acc=6467] Q3-MainLedger => rows=0  total=0  |  0.0015s
1 - 2026-09-24 17:48:05 --> [LedgerCondensed][acc=6467] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 17:48:05 --> [LedgerCondensed][acc=6467] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 27.3%)
   Q2-OpeningBalance           0.0003s   (  7.6%)
   Q3-MainLedger               0.0015s   ( 45.3%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0034s   (100%)
[LedgerCondensed][acc=6467] ── FUNCTION END ──
1 - 2026-09-24 17:48:10 --> [LedgerCondensed][acc=6467] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:48:10 --> [LedgerCondensed][acc=6467] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-24 17:48:10 --> [LedgerCondensed][acc=6467] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:48:10 --> [LedgerCondensed][acc=6467] Q3-MainLedger => rows=9  total=9  |  0.0118s
1 - 2026-09-24 17:48:10 --> [LedgerCondensed][acc=6467] Q4-OtherAccounts => 9 rows  |  0.0015s
1 - 2026-09-24 17:48:10 --> [LedgerCondensed][acc=6467] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  7.3%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0118s   ( 77.9%)
   Q4-OtherAccounts            0.0015s   ( 10.1%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0151s   (100%)
[LedgerCondensed][acc=6467] ── FUNCTION END ──
1 - 2026-09-24 17:50:12 --> [LedgerCondensed][acc=6467] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:50:12 --> [LedgerCondensed][acc=6467] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:50:12 --> [LedgerCondensed][acc=6467] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:50:12 --> [LedgerCondensed][acc=6467] Q3-MainLedger => rows=0  total=0  |  0.0015s
1 - 2026-09-24 17:50:12 --> [LedgerCondensed][acc=6467] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 17:50:12 --> [LedgerCondensed][acc=6467] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 30.4%)
   Q2-OpeningBalance           0.0003s   (  8.4%)
   Q3-MainLedger               0.0015s   ( 48.8%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0030s   (100%)
[LedgerCondensed][acc=6467] ── FUNCTION END ──
1 - 2026-09-24 17:50:26 --> [LedgerCondensed][acc=6468] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:50:26 --> [LedgerCondensed][acc=6468] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:50:26 --> [LedgerCondensed][acc=6468] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:50:26 --> [LedgerCondensed][acc=6468] Q3-MainLedger => rows=4  total=4  |  0.0118s
1 - 2026-09-24 17:50:26 --> [LedgerCondensed][acc=6468] Q4-OtherAccounts => 4 rows  |  0.0013s
1 - 2026-09-24 17:50:26 --> [LedgerCondensed][acc=6468] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.4%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0118s   ( 80.3%)
   Q4-OtherAccounts            0.0013s   (  8.6%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0147s   (100%)
[LedgerCondensed][acc=6468] ── FUNCTION END ──
1 - 2026-09-24 17:50:31 --> [LedgerCondensed][acc=6468] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:50:31 --> [LedgerCondensed][acc=6468] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:50:31 --> [LedgerCondensed][acc=6468] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:50:31 --> [LedgerCondensed][acc=6468] Q3-MainLedger => rows=28  total=28  |  0.0094s
1 - 2026-09-24 17:50:31 --> [LedgerCondensed][acc=6468] Q4-OtherAccounts => 28 rows  |  0.0016s
1 - 2026-09-24 17:50:31 --> [LedgerCondensed][acc=6468] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.2%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0094s   ( 74.6%)
   Q4-OtherAccounts            0.0016s   ( 13.0%)
   BuildRecords                0.0001s   (  0.7%)
   TOTAL                       0.0126s   (100%)
[LedgerCondensed][acc=6468] ── FUNCTION END ──
1 - 2026-09-24 17:52:14 --> [LedgerCondensed][acc=6468] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:52:14 --> [LedgerCondensed][acc=6468] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:52:14 --> [LedgerCondensed][acc=6468] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:52:14 --> [LedgerCondensed][acc=6468] Q3-MainLedger => rows=29  total=29  |  0.0098s
1 - 2026-09-24 17:52:14 --> [LedgerCondensed][acc=6468] Q4-OtherAccounts => 29 rows  |  0.0017s
1 - 2026-09-24 17:52:14 --> [LedgerCondensed][acc=6468] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.2%)
   Q2-OpeningBalance           0.0002s   (  1.7%)
   Q3-MainLedger               0.0098s   ( 74.0%)
   Q4-OtherAccounts            0.0017s   ( 13.1%)
   BuildRecords                0.0001s   (  0.9%)
   TOTAL                       0.0133s   (100%)
[LedgerCondensed][acc=6468] ── FUNCTION END ──
1 - 2026-09-24 17:52:25 --> [LedgerCondensed][acc=6468] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:52:25 --> [LedgerCondensed][acc=6468] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:52:25 --> [LedgerCondensed][acc=6468] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:52:25 --> [LedgerCondensed][acc=6468] Q3-MainLedger => rows=4  total=4  |  0.0117s
1 - 2026-09-24 17:52:25 --> [LedgerCondensed][acc=6468] Q4-OtherAccounts => 4 rows  |  0.0012s
1 - 2026-09-24 17:52:25 --> [LedgerCondensed][acc=6468] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.4%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0117s   ( 80.2%)
   Q4-OtherAccounts            0.0012s   (  8.5%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0146s   (100%)
[LedgerCondensed][acc=6468] ── FUNCTION END ──
1 - 2026-09-24 17:52:37 --> [LedgerCondensed][acc=6469] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:52:37 --> [LedgerCondensed][acc=6469] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:52:37 --> [LedgerCondensed][acc=6469] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:52:37 --> [LedgerCondensed][acc=6469] Q3-MainLedger => rows=1  total=1  |  0.0118s
1 - 2026-09-24 17:52:37 --> [LedgerCondensed][acc=6469] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-09-24 17:52:37 --> [LedgerCondensed][acc=6469] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.8%)
   Q2-OpeningBalance           0.0002s   (  1.7%)
   Q3-MainLedger               0.0118s   ( 81.7%)
   Q4-OtherAccounts            0.0010s   (  6.9%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0145s   (100%)
[LedgerCondensed][acc=6469] ── FUNCTION END ──
1 - 2026-09-24 17:52:43 --> [LedgerCondensed][acc=6469] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:52:43 --> [LedgerCondensed][acc=6469] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:52:43 --> [LedgerCondensed][acc=6469] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:52:43 --> [LedgerCondensed][acc=6469] Q3-MainLedger => rows=21  total=21  |  0.0098s
1 - 2026-09-24 17:52:43 --> [LedgerCondensed][acc=6469] Q4-OtherAccounts => 21 rows  |  0.002s
1 - 2026-09-24 17:52:43 --> [LedgerCondensed][acc=6469] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.5%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0098s   ( 72.0%)
   Q4-OtherAccounts            0.0020s   ( 14.5%)
   BuildRecords                0.0001s   (  1.0%)
   TOTAL                       0.0136s   (100%)
[LedgerCondensed][acc=6469] ── FUNCTION END ──
1 - 2026-09-24 17:53:54 --> [LedgerCondensed][acc=6469] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:53:54 --> [LedgerCondensed][acc=6469] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:53:54 --> [LedgerCondensed][acc=6469] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:53:54 --> [LedgerCondensed][acc=6469] Q3-MainLedger => rows=22  total=22  |  0.0092s
1 - 2026-09-24 17:53:54 --> [LedgerCondensed][acc=6469] Q4-OtherAccounts => 22 rows  |  0.0019s
1 - 2026-09-24 17:53:54 --> [LedgerCondensed][acc=6469] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.1%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0092s   ( 71.2%)
   Q4-OtherAccounts            0.0019s   ( 14.8%)
   BuildRecords                0.0001s   (  1.0%)
   TOTAL                       0.0129s   (100%)
[LedgerCondensed][acc=6469] ── FUNCTION END ──
1 - 2026-09-24 17:53:59 --> [LedgerCondensed][acc=6469] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:53:59 --> [LedgerCondensed][acc=6469] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:53:59 --> [LedgerCondensed][acc=6469] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:53:59 --> [LedgerCondensed][acc=6469] Q3-MainLedger => rows=1  total=1  |  0.0091s
1 - 2026-09-24 17:53:59 --> [LedgerCondensed][acc=6469] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-24 17:53:59 --> [LedgerCondensed][acc=6469] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.7%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0091s   ( 78.1%)
   Q4-OtherAccounts            0.0009s   (  7.7%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0117s   (100%)
[LedgerCondensed][acc=6469] ── FUNCTION END ──
1 - 2026-09-24 17:54:30 --> [LedgerCondensed][acc=6474] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:54:30 --> [LedgerCondensed][acc=6474] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:54:30 --> [LedgerCondensed][acc=6474] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:54:30 --> [LedgerCondensed][acc=6474] Q3-MainLedger => rows=1  total=1  |  0.009s
1 - 2026-09-24 17:54:30 --> [LedgerCondensed][acc=6474] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-09-24 17:54:30 --> [LedgerCondensed][acc=6474] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.5%)
   Q2-OpeningBalance           0.0002s   (  2.1%)
   Q3-MainLedger               0.0090s   ( 76.6%)
   Q4-OtherAccounts            0.0010s   (  8.8%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0117s   (100%)
[LedgerCondensed][acc=6474] ── FUNCTION END ──
1 - 2026-09-24 17:54:34 --> [LedgerCondensed][acc=6474] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:54:34 --> [LedgerCondensed][acc=6474] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:54:34 --> [LedgerCondensed][acc=6474] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:54:34 --> [LedgerCondensed][acc=6474] Q3-MainLedger => rows=30  total=30  |  0.0089s
1 - 2026-09-24 17:54:34 --> [LedgerCondensed][acc=6474] Q4-OtherAccounts => 30 rows  |  0.0016s
1 - 2026-09-24 17:54:34 --> [LedgerCondensed][acc=6474] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.8%)
   Q2-OpeningBalance           0.0003s   (  2.6%)
   Q3-MainLedger               0.0089s   ( 72.6%)
   Q4-OtherAccounts            0.0016s   ( 12.9%)
   BuildRecords                0.0001s   (  1.1%)
   TOTAL                       0.0122s   (100%)
[LedgerCondensed][acc=6474] ── FUNCTION END ──
1 - 2026-09-24 17:56:15 --> [LedgerCondensed][acc=6474] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:56:15 --> [LedgerCondensed][acc=6474] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:56:15 --> [LedgerCondensed][acc=6474] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:56:15 --> [LedgerCondensed][acc=6474] Q3-MainLedger => rows=31  total=31  |  0.0096s
1 - 2026-09-24 17:56:15 --> [LedgerCondensed][acc=6474] Q4-OtherAccounts => 31 rows  |  0.0024s
1 - 2026-09-24 17:56:15 --> [LedgerCondensed][acc=6474] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.8%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0096s   ( 69.8%)
   Q4-OtherAccounts            0.0024s   ( 17.1%)
   BuildRecords                0.0001s   (  1.0%)
   TOTAL                       0.0138s   (100%)
[LedgerCondensed][acc=6474] ── FUNCTION END ──
1 - 2026-09-24 17:56:18 --> [LedgerCondensed][acc=6474] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:56:18 --> [LedgerCondensed][acc=6474] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:56:18 --> [LedgerCondensed][acc=6474] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:56:18 --> [LedgerCondensed][acc=6474] Q3-MainLedger => rows=1  total=1  |  0.0091s
1 - 2026-09-24 17:56:18 --> [LedgerCondensed][acc=6474] Q4-OtherAccounts => 1 rows  |  0.0012s
1 - 2026-09-24 17:56:18 --> [LedgerCondensed][acc=6474] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.4%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0091s   ( 75.6%)
   Q4-OtherAccounts            0.0012s   (  9.7%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0120s   (100%)
[LedgerCondensed][acc=6474] ── FUNCTION END ──
1 - 2026-09-24 17:56:47 --> [LedgerCondensed][acc=6475] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:56:47 --> [LedgerCondensed][acc=6475] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:56:47 --> [LedgerCondensed][acc=6475] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:56:47 --> [LedgerCondensed][acc=6475] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-24 17:56:47 --> [LedgerCondensed][acc=6475] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 17:56:47 --> [LedgerCondensed][acc=6475] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 30.7%)
   Q2-OpeningBalance           0.0003s   (  8.3%)
   Q3-MainLedger               0.0016s   ( 49.3%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0032s   (100%)
[LedgerCondensed][acc=6475] ── FUNCTION END ──
1 - 2026-09-24 17:56:51 --> [LedgerCondensed][acc=6475] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:56:51 --> [LedgerCondensed][acc=6475] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:56:51 --> [LedgerCondensed][acc=6475] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:56:52 --> [LedgerCondensed][acc=6475] Q3-MainLedger => rows=19  total=19  |  0.0121s
1 - 2026-09-24 17:56:52 --> [LedgerCondensed][acc=6475] Q4-OtherAccounts => 19 rows  |  0.0017s
1 - 2026-09-24 17:56:52 --> [LedgerCondensed][acc=6475] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.6%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0121s   ( 78.6%)
   Q4-OtherAccounts            0.0017s   ( 10.9%)
   BuildRecords                0.0001s   (  0.5%)
   TOTAL                       0.0154s   (100%)
[LedgerCondensed][acc=6475] ── FUNCTION END ──
1 - 2026-09-24 17:57:59 --> [LedgerCondensed][acc=6475] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:57:59 --> [LedgerCondensed][acc=6475] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 17:57:59 --> [LedgerCondensed][acc=6475] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:57:59 --> [LedgerCondensed][acc=6475] Q3-MainLedger => rows=20  total=20  |  0.0097s
1 - 2026-09-24 17:57:59 --> [LedgerCondensed][acc=6475] Q4-OtherAccounts => 20 rows  |  0.0018s
1 - 2026-09-24 17:57:59 --> [LedgerCondensed][acc=6475] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.1%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0097s   ( 73.5%)
   Q4-OtherAccounts            0.0018s   ( 13.2%)
   BuildRecords                0.0001s   (  0.7%)
   TOTAL                       0.0132s   (100%)
[LedgerCondensed][acc=6475] ── FUNCTION END ──
1 - 2026-09-24 17:58:03 --> [LedgerCondensed][acc=6475] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:58:03 --> [LedgerCondensed][acc=6475] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 17:58:03 --> [LedgerCondensed][acc=6475] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:58:03 --> [LedgerCondensed][acc=6475] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-24 17:58:03 --> [LedgerCondensed][acc=6475] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 17:58:03 --> [LedgerCondensed][acc=6475] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   ( 27.8%)
   Q2-OpeningBalance           0.0002s   (  8.1%)
   Q3-MainLedger               0.0016s   ( 52.9%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0030s   (100%)
[LedgerCondensed][acc=6475] ── FUNCTION END ──
1 - 2026-09-24 17:58:15 --> [LedgerCondensed][acc=6477] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:58:15 --> [LedgerCondensed][acc=6477] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:58:15 --> [LedgerCondensed][acc=6477] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:58:16 --> [LedgerCondensed][acc=6477] Q3-MainLedger => rows=0  total=0  |  0.0017s
1 - 2026-09-24 17:58:16 --> [LedgerCondensed][acc=6477] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 17:58:16 --> [LedgerCondensed][acc=6477] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 30.0%)
   Q2-OpeningBalance           0.0003s   (  7.7%)
   Q3-MainLedger               0.0017s   ( 51.2%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0033s   (100%)
[LedgerCondensed][acc=6477] ── FUNCTION END ──
1 - 2026-09-24 17:58:20 --> [LedgerCondensed][acc=6477] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:58:20 --> [LedgerCondensed][acc=6477] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:58:20 --> [LedgerCondensed][acc=6477] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:58:20 --> [LedgerCondensed][acc=6477] Q3-MainLedger => rows=14  total=14  |  0.0093s
1 - 2026-09-24 17:58:20 --> [LedgerCondensed][acc=6477] Q4-OtherAccounts => 14 rows  |  0.0014s
1 - 2026-09-24 17:58:20 --> [LedgerCondensed][acc=6477] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.6%)
   Q2-OpeningBalance           0.0003s   (  2.7%)
   Q3-MainLedger               0.0093s   ( 74.4%)
   Q4-OtherAccounts            0.0014s   ( 11.0%)
   BuildRecords                0.0001s   (  0.5%)
   TOTAL                       0.0125s   (100%)
[LedgerCondensed][acc=6477] ── FUNCTION END ──
1 - 2026-09-24 17:59:32 --> [LedgerCondensed][acc=6477] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:59:32 --> [LedgerCondensed][acc=6477] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:59:32 --> [LedgerCondensed][acc=6477] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:59:32 --> [LedgerCondensed][acc=6477] Q3-MainLedger => rows=15  total=15  |  0.0098s
1 - 2026-09-24 17:59:32 --> [LedgerCondensed][acc=6477] Q4-OtherAccounts => 15 rows  |  0.0015s
1 - 2026-09-24 17:59:32 --> [LedgerCondensed][acc=6477] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.7%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0098s   ( 75.5%)
   Q4-OtherAccounts            0.0015s   ( 11.2%)
   BuildRecords                0.0001s   (  0.5%)
   TOTAL                       0.0130s   (100%)
[LedgerCondensed][acc=6477] ── FUNCTION END ──
1 - 2026-09-24 17:59:35 --> [LedgerCondensed][acc=6477] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:59:35 --> [LedgerCondensed][acc=6477] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 17:59:35 --> [LedgerCondensed][acc=6477] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 17:59:35 --> [LedgerCondensed][acc=6477] Q3-MainLedger => rows=0  total=0  |  0.0013s
1 - 2026-09-24 17:59:35 --> [LedgerCondensed][acc=6477] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 17:59:35 --> [LedgerCondensed][acc=6477] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   ( 28.6%)
   Q2-OpeningBalance           0.0002s   (  9.1%)
   Q3-MainLedger               0.0013s   ( 49.5%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0027s   (100%)
[LedgerCondensed][acc=6477] ── FUNCTION END ──
1 - 2026-09-24 17:59:54 --> [LedgerCondensed][acc=6478] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:59:54 --> [LedgerCondensed][acc=6478] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-24 17:59:54 --> [LedgerCondensed][acc=6478] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:59:54 --> [LedgerCondensed][acc=6478] Q3-MainLedger => rows=0  total=0  |  0.0018s
1 - 2026-09-24 17:59:54 --> [LedgerCondensed][acc=6478] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 17:59:54 --> [LedgerCondensed][acc=6478] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   ( 30.8%)
   Q2-OpeningBalance           0.0003s   (  7.6%)
   Q3-MainLedger               0.0018s   ( 49.3%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0036s   (100%)
[LedgerCondensed][acc=6478] ── FUNCTION END ──
1 - 2026-09-24 17:59:58 --> [LedgerCondensed][acc=6478] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 17:59:58 --> [LedgerCondensed][acc=6478] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 17:59:58 --> [LedgerCondensed][acc=6478] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 17:59:58 --> [LedgerCondensed][acc=6478] Q3-MainLedger => rows=10  total=10  |  0.0102s
1 - 2026-09-24 17:59:58 --> [LedgerCondensed][acc=6478] Q4-OtherAccounts => 10 rows  |  0.0014s
1 - 2026-09-24 17:59:58 --> [LedgerCondensed][acc=6478] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.5%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0102s   ( 76.3%)
   Q4-OtherAccounts            0.0014s   ( 10.3%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0134s   (100%)
[LedgerCondensed][acc=6478] ── FUNCTION END ──
1 - 2026-09-24 18:01:19 --> [LedgerCondensed][acc=14374] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:01:19 --> [LedgerCondensed][acc=14374] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 18:01:19 --> [LedgerCondensed][acc=14374] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-24 18:01:19 --> [LedgerCondensed][acc=14374] Q3-MainLedger => rows=0  total=0  |  0.0014s
1 - 2026-09-24 18:01:19 --> [LedgerCondensed][acc=14374] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:01:19 --> [LedgerCondensed][acc=14374] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   ( 28.2%)
   Q2-OpeningBalance           0.0002s   (  8.5%)
   Q3-MainLedger               0.0014s   ( 49.8%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0028s   (100%)
[LedgerCondensed][acc=14374] ── FUNCTION END ──
1 - 2026-09-24 18:01:41 --> [LedgerCondensed][acc=14023] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:01:41 --> [LedgerCondensed][acc=14023] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:01:41 --> [LedgerCondensed][acc=14023] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:01:41 --> [LedgerCondensed][acc=14023] Q3-MainLedger => rows=0  total=0  |  0.0015s
1 - 2026-09-24 18:01:41 --> [LedgerCondensed][acc=14023] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:01:41 --> [LedgerCondensed][acc=14023] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 28.8%)
   Q2-OpeningBalance           0.0002s   (  8.3%)
   Q3-MainLedger               0.0015s   ( 51.2%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0030s   (100%)
[LedgerCondensed][acc=14023] ── FUNCTION END ──
1 - 2026-09-24 18:01:55 --> [LedgerCondensed][acc=14374] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:01:55 --> [LedgerCondensed][acc=14374] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 18:01:55 --> [LedgerCondensed][acc=14374] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-24 18:01:55 --> [LedgerCondensed][acc=14374] Q3-MainLedger => rows=2  total=2  |  0.0116s
1 - 2026-09-24 18:01:55 --> [LedgerCondensed][acc=14374] Q4-OtherAccounts => 2 rows  |  0.0012s
1 - 2026-09-24 18:01:55 --> [LedgerCondensed][acc=14374] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  5.6%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0116s   ( 81.5%)
   Q4-OtherAccounts            0.0012s   (  8.1%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0142s   (100%)
[LedgerCondensed][acc=14374] ── FUNCTION END ──
1 - 2026-09-24 18:02:58 --> Company is gstin type check => 2
1 - 2026-09-24 18:02:58 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 18:02:58 --> Company ID: 98, Voucher Txn ID (Tax Applied): 146734, Bill Sundry ID: 6722
1 - 2026-09-24 18:02:58 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6722","acc_txn_date":"2025-10-18","acc_txn_dr_cr":2,"acc_txn_amt":53.25,"acc_txn_fcy":0,"vch_txn_id":"146734","txn_id":1023823,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 18:02:58 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 18:02:58 --> Company ID: 98, Voucher Txn ID (Tax Applied): 146734, Bill Sundry ID: 6581
1 - 2026-09-24 18:02:58 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6581","acc_txn_date":"2025-10-18","acc_txn_dr_cr":2,"acc_txn_amt":53.25,"acc_txn_fcy":0,"vch_txn_id":"146734","txn_id":1023824,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 18:03:05 --> [LedgerCondensed][acc=6478] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:03:05 --> [LedgerCondensed][acc=6478] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 18:03:05 --> [LedgerCondensed][acc=6478] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:03:05 --> [LedgerCondensed][acc=6478] Q3-MainLedger => rows=11  total=11  |  0.0095s
1 - 2026-09-24 18:03:05 --> [LedgerCondensed][acc=6478] Q4-OtherAccounts => 11 rows  |  0.0015s
1 - 2026-09-24 18:03:05 --> [LedgerCondensed][acc=6478] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.3%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0095s   ( 76.2%)
   Q4-OtherAccounts            0.0015s   ( 12.1%)
   BuildRecords                0.0000s   (  0.4%)
   TOTAL                       0.0125s   (100%)
[LedgerCondensed][acc=6478] ── FUNCTION END ──
1 - 2026-09-24 18:03:10 --> [LedgerCondensed][acc=6478] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:03:10 --> [LedgerCondensed][acc=6478] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:03:10 --> [LedgerCondensed][acc=6478] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:03:10 --> [LedgerCondensed][acc=6478] Q3-MainLedger => rows=0  total=0  |  0.0013s
1 - 2026-09-24 18:03:10 --> [LedgerCondensed][acc=6478] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:03:10 --> [LedgerCondensed][acc=6478] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 30.7%)
   Q2-OpeningBalance           0.0003s   (  9.7%)
   Q3-MainLedger               0.0013s   ( 48.0%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0028s   (100%)
[LedgerCondensed][acc=6478] ── FUNCTION END ──
1 - 2026-09-24 18:03:23 --> [LedgerCondensed][acc=6480] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:03:23 --> [LedgerCondensed][acc=6480] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:03:23 --> [LedgerCondensed][acc=6480] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:03:23 --> [LedgerCondensed][acc=6480] Q3-MainLedger => rows=3  total=3  |  0.0093s
1 - 2026-09-24 18:03:23 --> [LedgerCondensed][acc=6480] Q4-OtherAccounts => 3 rows  |  0.0011s
1 - 2026-09-24 18:03:23 --> [LedgerCondensed][acc=6480] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.5%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0093s   ( 77.2%)
   Q4-OtherAccounts            0.0011s   (  8.9%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0121s   (100%)
[LedgerCondensed][acc=6480] ── FUNCTION END ──
1 - 2026-09-24 18:03:28 --> [LedgerCondensed][acc=6480] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:03:28 --> [LedgerCondensed][acc=6480] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:03:28 --> [LedgerCondensed][acc=6480] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:03:28 --> [LedgerCondensed][acc=6480] Q3-MainLedger => rows=28  total=28  |  0.0092s
1 - 2026-09-24 18:03:28 --> [LedgerCondensed][acc=6480] Q4-OtherAccounts => 28 rows  |  0.0019s
1 - 2026-09-24 18:03:28 --> [LedgerCondensed][acc=6480] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.3%)
   Q2-OpeningBalance           0.0003s   (  2.4%)
   Q3-MainLedger               0.0092s   ( 71.8%)
   Q4-OtherAccounts            0.0019s   ( 14.8%)
   BuildRecords                0.0001s   (  0.8%)
   TOTAL                       0.0128s   (100%)
[LedgerCondensed][acc=6480] ── FUNCTION END ──
1 - 2026-09-24 18:05:24 --> [LedgerCondensed][acc=6480] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:05:24 --> [LedgerCondensed][acc=6480] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:05:24 --> [LedgerCondensed][acc=6480] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:05:24 --> [LedgerCondensed][acc=6480] Q3-MainLedger => rows=29  total=29  |  0.0099s
1 - 2026-09-24 18:05:24 --> [LedgerCondensed][acc=6480] Q4-OtherAccounts => 29 rows  |  0.0017s
1 - 2026-09-24 18:05:24 --> [LedgerCondensed][acc=6480] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.7%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0099s   ( 74.3%)
   Q4-OtherAccounts            0.0017s   ( 12.9%)
   BuildRecords                0.0001s   (  1.1%)
   TOTAL                       0.0133s   (100%)
[LedgerCondensed][acc=6480] ── FUNCTION END ──
1 - 2026-09-24 18:05:29 --> [LedgerCondensed][acc=6480] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:05:29 --> [LedgerCondensed][acc=6480] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 18:05:29 --> [LedgerCondensed][acc=6480] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:05:29 --> [LedgerCondensed][acc=6480] Q3-MainLedger => rows=3  total=3  |  0.0097s
1 - 2026-09-24 18:05:29 --> [LedgerCondensed][acc=6480] Q4-OtherAccounts => 3 rows  |  0.0015s
1 - 2026-09-24 18:05:29 --> [LedgerCondensed][acc=6480] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.4%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0097s   ( 76.1%)
   Q4-OtherAccounts            0.0015s   ( 12.1%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0127s   (100%)
[LedgerCondensed][acc=6480] ── FUNCTION END ──
1 - 2026-09-24 18:05:43 --> [LedgerCondensed][acc=6482] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:05:43 --> [LedgerCondensed][acc=6482] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:05:43 --> [LedgerCondensed][acc=6482] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:05:43 --> [LedgerCondensed][acc=6482] Q3-MainLedger => rows=0  total=0  |  0.0015s
1 - 2026-09-24 18:05:43 --> [LedgerCondensed][acc=6482] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:05:43 --> [LedgerCondensed][acc=6482] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 29.5%)
   Q2-OpeningBalance           0.0003s   (  8.4%)
   Q3-MainLedger               0.0015s   ( 48.7%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0030s   (100%)
[LedgerCondensed][acc=6482] ── FUNCTION END ──
1 - 2026-09-24 18:05:47 --> [LedgerCondensed][acc=6482] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:05:47 --> [LedgerCondensed][acc=6482] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:05:47 --> [LedgerCondensed][acc=6482] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:05:47 --> [LedgerCondensed][acc=6482] Q3-MainLedger => rows=17  total=17  |  0.0123s
1 - 2026-09-24 18:05:47 --> [LedgerCondensed][acc=6482] Q4-OtherAccounts => 17 rows  |  0.0016s
1 - 2026-09-24 18:05:47 --> [LedgerCondensed][acc=6482] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.1%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0123s   ( 78.7%)
   Q4-OtherAccounts            0.0016s   ( 10.5%)
   BuildRecords                0.0001s   (  0.5%)
   TOTAL                       0.0156s   (100%)
[LedgerCondensed][acc=6482] ── FUNCTION END ──
1 - 2026-09-24 18:08:11 --> Company is gstin type check => 2
1 - 2026-09-24 18:08:11 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 18:08:11 --> Company ID: 98, Voucher Txn ID (Tax Applied): 145866, Bill Sundry ID: 6722
1 - 2026-09-24 18:08:11 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6722","acc_txn_date":"2025-07-04","acc_txn_dr_cr":2,"acc_txn_amt":71,"acc_txn_fcy":0,"vch_txn_id":"145866","txn_id":1023834,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 18:08:11 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 18:08:11 --> Company ID: 98, Voucher Txn ID (Tax Applied): 145866, Bill Sundry ID: 6581
1 - 2026-09-24 18:08:11 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6581","acc_txn_date":"2025-07-04","acc_txn_dr_cr":2,"acc_txn_amt":71,"acc_txn_fcy":0,"vch_txn_id":"145866","txn_id":1023835,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 18:08:12 --> [LedgerCondensed][acc=6482] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:08:12 --> [LedgerCondensed][acc=6482] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:08:12 --> [LedgerCondensed][acc=6482] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:08:12 --> [LedgerCondensed][acc=6482] Q3-MainLedger => rows=16  total=16  |  0.0092s
1 - 2026-09-24 18:08:12 --> [LedgerCondensed][acc=6482] Q4-OtherAccounts => 16 rows  |  0.0015s
1 - 2026-09-24 18:08:12 --> [LedgerCondensed][acc=6482] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.7%)
   Q2-OpeningBalance           0.0003s   (  2.4%)
   Q3-MainLedger               0.0092s   ( 74.4%)
   Q4-OtherAccounts            0.0015s   ( 12.0%)
   BuildRecords                0.0001s   (  0.5%)
   TOTAL                       0.0123s   (100%)
[LedgerCondensed][acc=6482] ── FUNCTION END ──
1 - 2026-09-24 18:08:21 --> [LedgerCondensed][acc=6482] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:08:21 --> [LedgerCondensed][acc=6482] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:08:21 --> [LedgerCondensed][acc=6482] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:08:21 --> [LedgerCondensed][acc=6482] Q3-MainLedger => rows=0  total=0  |  0.0015s
1 - 2026-09-24 18:08:21 --> [LedgerCondensed][acc=6482] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:08:21 --> [LedgerCondensed][acc=6482] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 29.3%)
   Q2-OpeningBalance           0.0003s   (  8.6%)
   Q3-MainLedger               0.0015s   ( 52.3%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0029s   (100%)
[LedgerCondensed][acc=6482] ── FUNCTION END ──
1 - 2026-09-24 18:08:52 --> [LedgerCondensed][acc=6484] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:08:52 --> [LedgerCondensed][acc=6484] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:08:52 --> [LedgerCondensed][acc=6484] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:08:52 --> [LedgerCondensed][acc=6484] Q3-MainLedger => rows=2  total=2  |  0.0124s
1 - 2026-09-24 18:08:52 --> [LedgerCondensed][acc=6484] Q4-OtherAccounts => 2 rows  |  0.0016s
1 - 2026-09-24 18:08:52 --> [LedgerCondensed][acc=6484] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.2%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0124s   ( 78.6%)
   Q4-OtherAccounts            0.0016s   ( 10.1%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0158s   (100%)
[LedgerCondensed][acc=6484] ── FUNCTION END ──
1 - 2026-09-24 18:08:56 --> [LedgerCondensed][acc=6484] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:08:56 --> [LedgerCondensed][acc=6484] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:08:56 --> [LedgerCondensed][acc=6484] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:08:56 --> [LedgerCondensed][acc=6484] Q3-MainLedger => rows=36  total=36  |  0.0098s
1 - 2026-09-24 18:08:56 --> [LedgerCondensed][acc=6484] Q4-OtherAccounts => 36 rows  |  0.0019s
1 - 2026-09-24 18:08:56 --> [LedgerCondensed][acc=6484] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.9%)
   Q2-OpeningBalance           0.0002s   (  1.9%)
   Q3-MainLedger               0.0098s   ( 73.3%)
   Q4-OtherAccounts            0.0019s   ( 14.2%)
   BuildRecords                0.0001s   (  1.0%)
   TOTAL                       0.0133s   (100%)
[LedgerCondensed][acc=6484] ── FUNCTION END ──
1 - 2026-09-24 18:09:21 --> [LedgerCondensed][acc=14374] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:09:21 --> [LedgerCondensed][acc=14374] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:09:21 --> [LedgerCondensed][acc=14374] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-24 18:09:21 --> [LedgerCondensed][acc=14374] Q3-MainLedger => rows=2  total=2  |  0.0125s
1 - 2026-09-24 18:09:21 --> [LedgerCondensed][acc=14374] Q4-OtherAccounts => 2 rows  |  0.0013s
1 - 2026-09-24 18:09:21 --> [LedgerCondensed][acc=14374] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.4%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0125s   ( 80.2%)
   Q4-OtherAccounts            0.0013s   (  8.3%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0156s   (100%)
[LedgerCondensed][acc=14374] ── FUNCTION END ──
1 - 2026-09-24 18:11:06 --> [LedgerCondensed][acc=6484] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:11:06 --> [LedgerCondensed][acc=6484] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:11:06 --> [LedgerCondensed][acc=6484] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:11:06 --> [LedgerCondensed][acc=6484] Q3-MainLedger => rows=37  total=37  |  0.0119s
1 - 2026-09-24 18:11:06 --> [LedgerCondensed][acc=6484] Q4-OtherAccounts => 37 rows  |  0.0021s
1 - 2026-09-24 18:11:06 --> [LedgerCondensed][acc=6484] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.2%)
   Q2-OpeningBalance           0.0002s   (  1.4%)
   Q3-MainLedger               0.0119s   ( 75.3%)
   Q4-OtherAccounts            0.0021s   ( 13.4%)
   BuildRecords                0.0001s   (  0.8%)
   TOTAL                       0.0158s   (100%)
[LedgerCondensed][acc=6484] ── FUNCTION END ──
1 - 2026-09-24 18:11:14 --> [LedgerCondensed][acc=6484] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:11:14 --> [LedgerCondensed][acc=6484] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:11:14 --> [LedgerCondensed][acc=6484] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:11:14 --> [LedgerCondensed][acc=6484] Q3-MainLedger => rows=2  total=2  |  0.0095s
1 - 2026-09-24 18:11:14 --> [LedgerCondensed][acc=6484] Q4-OtherAccounts => 2 rows  |  0.001s
1 - 2026-09-24 18:11:14 --> [LedgerCondensed][acc=6484] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.9%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0095s   ( 78.0%)
   Q4-OtherAccounts            0.0010s   (  8.5%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0121s   (100%)
[LedgerCondensed][acc=6484] ── FUNCTION END ──
1 - 2026-09-24 18:12:04 --> [LedgerCondensed][acc=6487] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:12:04 --> [LedgerCondensed][acc=6487] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:12:04 --> [LedgerCondensed][acc=6487] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:12:04 --> [LedgerCondensed][acc=6487] Q3-MainLedger => rows=0  total=0  |  0.0018s
1 - 2026-09-24 18:12:04 --> [LedgerCondensed][acc=6487] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:12:04 --> [LedgerCondensed][acc=6487] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 29.0%)
   Q2-OpeningBalance           0.0003s   (  7.5%)
   Q3-MainLedger               0.0018s   ( 53.0%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0034s   (100%)
[LedgerCondensed][acc=6487] ── FUNCTION END ──
1 - 2026-09-24 18:12:09 --> [LedgerCondensed][acc=6487] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:12:09 --> [LedgerCondensed][acc=6487] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:12:09 --> [LedgerCondensed][acc=6487] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:12:09 --> [LedgerCondensed][acc=6487] Q3-MainLedger => rows=9  total=9  |  0.0117s
1 - 2026-09-24 18:12:09 --> [LedgerCondensed][acc=6487] Q4-OtherAccounts => 9 rows  |  0.0015s
1 - 2026-09-24 18:12:09 --> [LedgerCondensed][acc=6487] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.0%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0117s   ( 78.8%)
   Q4-OtherAccounts            0.0015s   ( 10.3%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0149s   (100%)
[LedgerCondensed][acc=6487] ── FUNCTION END ──
1 - 2026-09-24 18:17:04 --> [LedgerCondensed][acc=6487] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:17:04 --> [LedgerCondensed][acc=6487] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-24 18:17:04 --> [LedgerCondensed][acc=6487] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:17:04 --> [LedgerCondensed][acc=6487] Q3-MainLedger => rows=10  total=10  |  0.0096s
1 - 2026-09-24 18:17:04 --> [LedgerCondensed][acc=6487] Q4-OtherAccounts => 10 rows  |  0.0016s
1 - 2026-09-24 18:17:04 --> [LedgerCondensed][acc=6487] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  8.3%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0096s   ( 74.0%)
   Q4-OtherAccounts            0.0016s   ( 12.3%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0130s   (100%)
[LedgerCondensed][acc=6487] ── FUNCTION END ──
1 - 2026-09-24 18:17:12 --> [LedgerCondensed][acc=6487] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:17:12 --> [LedgerCondensed][acc=6487] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:17:12 --> [LedgerCondensed][acc=6487] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:17:12 --> [LedgerCondensed][acc=6487] Q3-MainLedger => rows=0  total=0  |  0.0013s
1 - 2026-09-24 18:17:12 --> [LedgerCondensed][acc=6487] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:17:12 --> [LedgerCondensed][acc=6487] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 33.1%)
   Q2-OpeningBalance           0.0003s   (  9.1%)
   Q3-MainLedger               0.0013s   ( 46.1%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0029s   (100%)
[LedgerCondensed][acc=6487] ── FUNCTION END ──
1 - 2026-09-24 18:17:25 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:17:25 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:17:25 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:17:25 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-24 18:17:25 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:17:25 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 30.2%)
   Q2-OpeningBalance           0.0002s   (  7.4%)
   Q3-MainLedger               0.0016s   ( 51.1%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0032s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:17:29 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:17:29 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:17:29 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:17:29 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=6  total=6  |  0.0122s
1 - 2026-09-24 18:17:29 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => 6 rows  |  0.0014s
1 - 2026-09-24 18:17:29 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.7%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0122s   ( 80.8%)
   Q4-OtherAccounts            0.0014s   (  9.2%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0151s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:19:24 --> [LedgerCondensed][acc=8506] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:19:24 --> [LedgerCondensed][acc=8506] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-24 18:19:24 --> [LedgerCondensed][acc=8506] Q2-OpeningBalance => 1000.00  |  0.0003s
1 - 2026-09-24 18:19:24 --> [LedgerCondensed][acc=8506] Q3-MainLedger => rows=0  total=0  |  0.0017s
1 - 2026-09-24 18:19:24 --> [LedgerCondensed][acc=8506] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:19:24 --> [LedgerCondensed][acc=8506] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   ( 31.5%)
   Q2-OpeningBalance           0.0003s   (  8.2%)
   Q3-MainLedger               0.0017s   ( 49.9%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0035s   (100%)
[LedgerCondensed][acc=8506] ── FUNCTION END ──
1 - 2026-09-24 18:20:10 --> [LedgerCondensed][acc=8090] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:20:10 --> [LedgerCondensed][acc=8090] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:20:10 --> [LedgerCondensed][acc=8090] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:20:10 --> [LedgerCondensed][acc=8090] Q3-MainLedger => rows=0  total=0  |  0.0023s
1 - 2026-09-24 18:20:10 --> [LedgerCondensed][acc=8090] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:20:10 --> [LedgerCondensed][acc=8090] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 22.7%)
   Q2-OpeningBalance           0.0003s   (  6.7%)
   Q3-MainLedger               0.0023s   ( 59.2%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0039s   (100%)
[LedgerCondensed][acc=8090] ── FUNCTION END ──
1 - 2026-09-24 18:20:35 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:20:35 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:20:35 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:20:35 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-24 18:20:35 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:20:35 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 28.9%)
   Q2-OpeningBalance           0.0002s   (  7.6%)
   Q3-MainLedger               0.0016s   ( 51.8%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0031s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:20:52 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:20:52 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:20:52 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:20:52 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-24 18:20:52 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:20:52 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 28.8%)
   Q2-OpeningBalance           0.0002s   (  7.8%)
   Q3-MainLedger               0.0016s   ( 51.1%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0030s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:20:57 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:20:57 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:20:57 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:20:57 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=6  total=6  |  0.0121s
1 - 2026-09-24 18:20:57 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => 6 rows  |  0.0014s
1 - 2026-09-24 18:20:57 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.1%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0121s   ( 80.1%)
   Q4-OtherAccounts            0.0014s   (  9.1%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0151s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:21:22 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:21:22 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:21:22 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:21:22 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=0  total=0  |  0.0015s
1 - 2026-09-24 18:21:22 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:21:22 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 28.6%)
   Q2-OpeningBalance           0.0003s   (  8.1%)
   Q3-MainLedger               0.0015s   ( 49.5%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0031s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:23:34 --> Company is gstin type check => 2
1 - 2026-09-24 18:23:34 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 18:23:34 --> Company ID: 98, Voucher Txn ID (Tax Applied): 145955, Bill Sundry ID: 6722
1 - 2026-09-24 18:23:34 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6722","acc_txn_date":"2025-07-15","acc_txn_dr_cr":2,"acc_txn_amt":17.75,"acc_txn_fcy":0,"vch_txn_id":"145955","txn_id":1023848,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 18:23:34 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 18:23:34 --> Company ID: 98, Voucher Txn ID (Tax Applied): 145955, Bill Sundry ID: 6581
1 - 2026-09-24 18:23:34 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6581","acc_txn_date":"2025-07-15","acc_txn_dr_cr":2,"acc_txn_amt":17.75,"acc_txn_fcy":0,"vch_txn_id":"145955","txn_id":1023849,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 18:23:46 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:23:46 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:23:46 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:23:46 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=0  total=0  |  0.0017s
1 - 2026-09-24 18:23:46 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:23:46 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 28.1%)
   Q2-OpeningBalance           0.0003s   (  7.8%)
   Q3-MainLedger               0.0017s   ( 51.1%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0034s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:23:51 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:23:51 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-24 18:23:51 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:23:51 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=6  total=6  |  0.0116s
1 - 2026-09-24 18:23:51 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => 6 rows  |  0.0013s
1 - 2026-09-24 18:23:51 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  5.4%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0116s   ( 80.7%)
   Q4-OtherAccounts            0.0013s   (  9.2%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0144s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:24:33 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:24:33 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:24:33 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:24:33 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-24 18:24:33 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:24:33 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 31.5%)
   Q2-OpeningBalance           0.0003s   (  7.6%)
   Q3-MainLedger               0.0016s   ( 48.7%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0033s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:24:41 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:24:41 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:24:41 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:24:41 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-24 18:24:41 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:24:41 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 29.8%)
   Q2-OpeningBalance           0.0003s   (  8.4%)
   Q3-MainLedger               0.0016s   ( 51.7%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0031s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:25:24 --> Company is gstin type check => 2
1 - 2026-09-24 18:25:24 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 18:25:24 --> Company ID: 98, Voucher Txn ID (Tax Applied): 145955, Bill Sundry ID: 6722
1 - 2026-09-24 18:25:24 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6722","acc_txn_date":"2025-07-15","acc_txn_dr_cr":2,"acc_txn_amt":17.75,"acc_txn_fcy":0,"vch_txn_id":"145955","txn_id":1023856,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 18:25:24 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 18:25:24 --> Company ID: 98, Voucher Txn ID (Tax Applied): 145955, Bill Sundry ID: 6581
1 - 2026-09-24 18:25:24 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6581","acc_txn_date":"2025-07-15","acc_txn_dr_cr":2,"acc_txn_amt":17.75,"acc_txn_fcy":0,"vch_txn_id":"145955","txn_id":1023857,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 18:25:33 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:25:33 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:25:33 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:25:33 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=7  total=7  |  0.0098s
1 - 2026-09-24 18:25:33 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => 7 rows  |  0.0012s
1 - 2026-09-24 18:25:33 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.0%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0098s   ( 77.7%)
   Q4-OtherAccounts            0.0012s   (  9.8%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0127s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:26:53 --> Company is gstin type check => 2
1 - 2026-09-24 18:26:53 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 18:26:53 --> Company ID: 98, Voucher Txn ID (Tax Applied): 146496, Bill Sundry ID: 6722
1 - 2026-09-24 18:26:53 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6722","acc_txn_date":"2025-09-24","acc_txn_dr_cr":2,"acc_txn_amt":17.75,"acc_txn_fcy":0,"vch_txn_id":"146496","txn_id":1023864,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 18:26:53 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 18:26:53 --> Company ID: 98, Voucher Txn ID (Tax Applied): 146496, Bill Sundry ID: 6581
1 - 2026-09-24 18:26:53 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6581","acc_txn_date":"2025-09-24","acc_txn_dr_cr":2,"acc_txn_amt":17.75,"acc_txn_fcy":0,"vch_txn_id":"146496","txn_id":1023865,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 18:26:58 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:26:58 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:26:58 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:26:58 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=8  total=8  |  0.009s
1 - 2026-09-24 18:26:58 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => 8 rows  |  0.0012s
1 - 2026-09-24 18:26:58 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.4%)
   Q2-OpeningBalance           0.0002s   (  2.1%)
   Q3-MainLedger               0.0090s   ( 75.6%)
   Q4-OtherAccounts            0.0012s   ( 10.5%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0119s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:28:28 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:28:28 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:28:28 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:28:28 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=8  total=8  |  0.0091s
1 - 2026-09-24 18:28:28 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => 8 rows  |  0.0012s
1 - 2026-09-24 18:28:28 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.5%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0091s   ( 75.0%)
   Q4-OtherAccounts            0.0012s   ( 10.2%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0121s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:29:14 --> Company is gstin type check => 2
1 - 2026-09-24 18:29:14 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 18:29:14 --> Company ID: 98, Voucher Txn ID (Tax Applied): 294786, Bill Sundry ID: 6722
1 - 2026-09-24 18:29:14 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6722","acc_txn_date":"2026-02-26","acc_txn_dr_cr":2,"acc_txn_amt":17.75,"acc_txn_fcy":0,"vch_txn_id":"294786","txn_id":1023872,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 18:29:14 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-24 18:29:14 --> Company ID: 98, Voucher Txn ID (Tax Applied): 294786, Bill Sundry ID: 6581
1 - 2026-09-24 18:29:14 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6581","acc_txn_date":"2026-02-26","acc_txn_dr_cr":2,"acc_txn_amt":17.75,"acc_txn_fcy":0,"vch_txn_id":"294786","txn_id":1023873,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-24 18:29:19 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:29:19 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:29:19 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:29:19 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=9  total=9  |  0.0091s
1 - 2026-09-24 18:29:19 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => 9 rows  |  0.0013s
1 - 2026-09-24 18:29:19 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.1%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0091s   ( 74.9%)
   Q4-OtherAccounts            0.0013s   ( 10.8%)
   BuildRecords                0.0000s   (  0.4%)
   TOTAL                       0.0122s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:29:57 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-08-01  to=2025-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:29:57 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 299  |  0.0024s
1 - 2026-09-24 18:29:57 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 98806.95  |  0.0019s
1 - 2026-09-24 18:29:57 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=34  total=34  |  0.0087s
1 - 2026-09-24 18:29:57 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 34 rows  |  0.0015s
1 - 2026-09-24 18:29:57 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0024s   ( 15.9%)
   Q2-OpeningBalance           0.0019s   ( 12.6%)
   Q3-MainLedger               0.0087s   ( 57.2%)
   Q4-OtherAccounts            0.0015s   ( 10.1%)
   BuildRecords                0.0002s   (  1.1%)
   TOTAL                       0.0152s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 18:30:18 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-08-01  to=2025-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:30:18 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 299  |  0.0022s
1 - 2026-09-24 18:30:18 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 98806.95  |  0.0019s
1 - 2026-09-24 18:30:18 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=34  total=34  |  0.0085s
1 - 2026-09-24 18:30:18 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 34 rows  |  0.0016s
1 - 2026-09-24 18:30:18 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0022s   ( 15.1%)
   Q2-OpeningBalance           0.0019s   ( 12.6%)
   Q3-MainLedger               0.0085s   ( 57.4%)
   Q4-OtherAccounts            0.0016s   ( 10.8%)
   BuildRecords                0.0001s   (  0.9%)
   TOTAL                       0.0149s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 18:30:22 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:30:22 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:30:22 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:30:22 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=10  total=10  |  0.0088s
1 - 2026-09-24 18:30:22 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => 10 rows  |  0.0013s
1 - 2026-09-24 18:30:22 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.6%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0088s   ( 74.1%)
   Q4-OtherAccounts            0.0013s   ( 11.0%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0119s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:30:58 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-08-01  to=2025-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:30:58 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 299  |  0.0024s
1 - 2026-09-24 18:30:58 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 98806.95  |  0.0018s
1 - 2026-09-24 18:30:58 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=34  total=34  |  0.0096s
1 - 2026-09-24 18:30:58 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 34 rows  |  0.0016s
1 - 2026-09-24 18:30:58 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0024s   ( 14.9%)
   Q2-OpeningBalance           0.0018s   ( 11.3%)
   Q3-MainLedger               0.0096s   ( 60.3%)
   Q4-OtherAccounts            0.0016s   (  9.8%)
   BuildRecords                0.0001s   (  0.9%)
   TOTAL                       0.0160s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 18:31:05 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:31:05 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:31:05 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:31:05 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=11  total=11  |  0.0104s
1 - 2026-09-24 18:31:05 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => 11 rows  |  0.0015s
1 - 2026-09-24 18:31:05 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.1%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0104s   ( 77.0%)
   Q4-OtherAccounts            0.0015s   ( 10.9%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0135s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:31:40 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-11-01  to=2025-11-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:31:40 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 425  |  0.0023s
1 - 2026-09-24 18:31:40 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 1138150.8  |  0.0022s
1 - 2026-09-24 18:31:40 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=41  total=41  |  0.0114s
1 - 2026-09-24 18:31:40 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 41 rows  |  0.0024s
1 - 2026-09-24 18:31:40 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0023s   ( 12.0%)
   Q2-OpeningBalance           0.0022s   ( 11.5%)
   Q3-MainLedger               0.0114s   ( 60.2%)
   Q4-OtherAccounts            0.0024s   ( 12.9%)
   BuildRecords                0.0002s   (  1.0%)
   TOTAL                       0.0189s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 18:31:55 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-11-01  to=2025-11-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:31:55 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 425  |  0.002s
1 - 2026-09-24 18:31:55 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 1138150.8  |  0.0018s
1 - 2026-09-24 18:31:55 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=41  total=41  |  0.0088s
1 - 2026-09-24 18:31:55 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 41 rows  |  0.0024s
1 - 2026-09-24 18:31:55 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0020s   ( 13.0%)
   Q2-OpeningBalance           0.0018s   ( 11.6%)
   Q3-MainLedger               0.0088s   ( 56.7%)
   Q4-OtherAccounts            0.0024s   ( 15.2%)
   BuildRecords                0.0002s   (  1.1%)
   TOTAL                       0.0156s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-24 18:31:58 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:31:58 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-24 18:31:58 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-24 18:31:58 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=12  total=12  |  0.0093s
1 - 2026-09-24 18:31:58 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => 12 rows  |  0.0013s
1 - 2026-09-24 18:31:58 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.2%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0093s   ( 76.2%)
   Q4-OtherAccounts            0.0013s   ( 10.7%)
   BuildRecords                0.0001s   (  0.6%)
   TOTAL                       0.0122s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:32:05 --> [LedgerCondensed][acc=6488] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-24 18:32:05 --> [LedgerCondensed][acc=6488] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-24 18:32:05 --> [LedgerCondensed][acc=6488] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-24 18:32:05 --> [LedgerCondensed][acc=6488] Q3-MainLedger => rows=0  total=0  |  0.0015s
1 - 2026-09-24 18:32:05 --> [LedgerCondensed][acc=6488] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-24 18:32:05 --> [LedgerCondensed][acc=6488] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 32.1%)
   Q2-OpeningBalance           0.0003s   (  8.4%)
   Q3-MainLedger               0.0015s   ( 48.3%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0030s   (100%)
[LedgerCondensed][acc=6488] ── FUNCTION END ──
1 - 2026-09-24 18:32:10 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 18:32:10 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 18:32:10 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-24 18:32:10 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
