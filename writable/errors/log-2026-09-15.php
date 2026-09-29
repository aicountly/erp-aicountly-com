<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-09-15 11:40:20 --> [LedgerCondensed][acc=21626] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 11:40:20 --> [LedgerCondensed][acc=21626] Q1-VoucherCount => 0  |  0.0015s
1 - 2026-09-15 11:40:20 --> [LedgerCondensed][acc=21626] Q2-OpeningBalance => 13000.00  |  0.0003s
1 - 2026-09-15 11:40:20 --> [LedgerCondensed][acc=21626] Q3-MainLedger => rows=0  total=0  |  0.0015s
1 - 2026-09-15 11:40:20 --> [LedgerCondensed][acc=21626] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-15 11:40:20 --> [LedgerCondensed][acc=21626] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0015s   ( 38.3%)
   Q2-OpeningBalance           0.0003s   (  8.0%)
   Q3-MainLedger               0.0015s   ( 39.6%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0038s   (100%)
[LedgerCondensed][acc=21626] ── FUNCTION END ──
1 - 2026-09-15 11:40:23 --> [LedgerCondensed][acc=21626] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 11:40:23 --> [LedgerCondensed][acc=21626] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-15 11:40:23 --> [LedgerCondensed][acc=21626] Q2-OpeningBalance => 13000.00  |  0.0003s
1 - 2026-09-15 11:40:23 --> [LedgerCondensed][acc=21626] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-15 11:40:23 --> [LedgerCondensed][acc=21626] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-15 11:40:23 --> [LedgerCondensed][acc=21626] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   ( 32.1%)
   Q2-OpeningBalance           0.0003s   (  7.8%)
   Q3-MainLedger               0.0016s   ( 47.9%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0034s   (100%)
[LedgerCondensed][acc=21626] ── FUNCTION END ──
1 - 2026-09-15 11:40:54 --> [LedgerCondensed][acc=21626] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 11:40:54 --> [LedgerCondensed][acc=21626] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 11:40:54 --> [LedgerCondensed][acc=21626] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 11:40:54 --> [LedgerCondensed][acc=21626] Q3-MainLedger => rows=6  total=6  |  0.0211s
1 - 2026-09-15 11:40:54 --> [LedgerCondensed][acc=21626] Q4-OtherAccounts => 6 rows  |  0.0022s
1 - 2026-09-15 11:40:54 --> [LedgerCondensed][acc=21626] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  4.2%)
   Q2-OpeningBalance           0.0003s   (  1.0%)
   Q3-MainLedger               0.0211s   ( 83.8%)
   Q4-OtherAccounts            0.0022s   (  8.9%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0251s   (100%)
[LedgerCondensed][acc=21626] ── FUNCTION END ──
1 - 2026-09-15 12:52:57 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:52:57 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:52:58 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:52:58 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:52:58 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:52:58 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:53:10 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:53:10 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:53:11 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:53:11 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:53:12 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:53:12 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:58:01 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:58:01 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:58:01 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:58:01 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:58:06 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 12:58:06 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1418','1764_17','1764_8','1765_3','1766_1409','1766_8','1767_8','1768_1409','1768_8','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:06:24 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:06:24 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:06:24 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:06:24 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:06:48 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:06:48 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:15:34 --> [LedgerCondensed][acc=6528] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 13:15:34 --> [LedgerCondensed][acc=6528] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 13:15:34 --> [LedgerCondensed][acc=6528] Q2-OpeningBalance => -19300.00  |  0.0003s
1 - 2026-09-15 13:15:34 --> [LedgerCondensed][acc=6528] Q3-MainLedger => rows=0  total=0  |  0.0018s
1 - 2026-09-15 13:15:34 --> [LedgerCondensed][acc=6528] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-15 13:15:34 --> [LedgerCondensed][acc=6528] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 29.8%)
   Q2-OpeningBalance           0.0003s   (  7.7%)
   Q3-MainLedger               0.0018s   ( 50.5%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0035s   (100%)
[LedgerCondensed][acc=6528] ── FUNCTION END ──
1 - 2026-09-15 13:16:02 --> [LedgerCondensed][acc=6634] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 13:16:02 --> [LedgerCondensed][acc=6634] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 13:16:02 --> [LedgerCondensed][acc=6634] Q2-OpeningBalance => 2000.00  |  0.0002s
1 - 2026-09-15 13:16:02 --> [LedgerCondensed][acc=6634] Q3-MainLedger => rows=0  total=0  |  0.0013s
1 - 2026-09-15 13:16:02 --> [LedgerCondensed][acc=6634] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-15 13:16:02 --> [LedgerCondensed][acc=6634] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   ( 29.7%)
   Q2-OpeningBalance           0.0002s   (  9.1%)
   Q3-MainLedger               0.0013s   ( 48.2%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0027s   (100%)
[LedgerCondensed][acc=6634] ── FUNCTION END ──
1 - 2026-09-15 13:18:18 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:18 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:18 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:18 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:23 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:23 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:23 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:23 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:44 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:44 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6407 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6407 P&L Appropriation nextFY opening INSERTED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6407 acc_name="GST PAID A/C" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6408 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6408 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6409 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6409 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6409 acc_name="Capital Account" parent_id=1 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6410 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6410 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6410 acc_name="Cash In Hand" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=14518.91 movement=3077.24 closing_to_next_fy=17596.15 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6411 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6411 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6411 acc_name="Sales Account" | skipped due to restricted parent_id=8
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6412 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6412 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6412 acc_name="Purchase Account" | skipped due to restricted parent_id=7
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6413 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6413 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6413 acc_name="VESTA INDUSTRIES INDIA PVT LTD" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6414 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6414 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6414 acc_name="THE FRAMES" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-183000 movement=155274 closing_to_next_fy=-27726 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6415 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6415 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6415 acc_name="GUGLANI GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6416 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6416 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6416 acc_name="RAJU FRAME WORKS MUKATSAR" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6417 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6417 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6417 acc_name="BHOLA LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-174912 movement=191146 closing_to_next_fy=16234 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6418 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6418 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6418 acc_name="DABBU LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=-30864 closing_to_next_fy=-30864 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6419 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6419 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6419 acc_name="GURPREET FRAME MAKERS" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-112900 movement=123550 closing_to_next_fy=10650 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6420 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6420 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6420 acc_name="KANCHAN STUDIO" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6421 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6421 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6421 acc_name="JAWAHAR PICTURES" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6422 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6422 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6422 acc_name="SACHIN GIFT GALLERY" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6423 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6423 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6423 acc_name="FANCY GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-118897 movement=154547 closing_to_next_fy=35650 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6424 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6424 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6424 acc_name="KRISHAN GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-95200 movement=81735 closing_to_next_fy=-13465 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6425 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6425 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6425 acc_name="PUNJAB LAMINATION HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6426 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6426 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6426 acc_name="ASIAN GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-88700 movement=65150 closing_to_next_fy=-23550 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6427 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6427 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6427 acc_name="ROYAL FOTO FRAME" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6428 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6428 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6428 acc_name="SANJAY ARTS" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-48650 movement=48650 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6429 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6429 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6429 acc_name="SWASTIKA GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6430 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6430 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6430 acc_name="RAHUL LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6431 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6431 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6431 acc_name="BARNAL PHOTO COLOUR LAB" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6432 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6432 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6432 acc_name="RAJU LAMINATION & FRAMES" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6433 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6433 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6433 acc_name="GURBACHAN SINGH SHESSHA" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6434 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6434 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6434 acc_name="SONU GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-25000 movement=35650 closing_to_next_fy=10650 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6435 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6435 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6435 acc_name="SAI PHOTO FRAMING" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6436 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6436 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6436 acc_name="GAGAN ARTS" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-141730 movement=98530 closing_to_next_fy=-43200 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6437 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6437 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6437 acc_name="KARTAR FOTO FRAME & LAMINATION HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6438 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6438 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6438 acc_name="SHANKY PHOTO FRAME" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-1200 movement=1200 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6439 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6439 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6439 acc_name="ANSH PHOTO FRAME & LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6440 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6440 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6440 acc_name="SURYA GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=63900 closing_to_next_fy=63900 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6441 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6441 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6441 acc_name="GURU ART GALLERY" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6442 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6442 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6442 acc_name="NIRMAL GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-55150 movement=55150 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6443 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6443 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6443 acc_name="SWASTIKA PICTURE CORNER" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-19800 movement=19800 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6444 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6444 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6444 acc_name="IQBAL FOTO FRAME" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-53350 movement=53350 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6445 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6445 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6445 acc_name="BEDI ART SERVICE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6446 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6446 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6446 acc_name="HARISH LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6447 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6447 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6447 acc_name="VESTA PROFILES INDUSTRIES LLP" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-2801647 movement=2801647 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6448 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6448 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6448 acc_name="HARJEET SINGH" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6449 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6449 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6449 acc_name="NAND LAL FAQIR CHAND" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-55850 movement=55850 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6450 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6450 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6450 acc_name="SANT KRIPA ART GALLERY" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-6250 movement=14400 closing_to_next_fy=8150 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6451 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6451 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6451 acc_name="NEW CHANDER GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6452 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6452 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6452 acc_name="KHWAJA LAMINATION HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6453 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6453 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6453 acc_name="SANTOSH KUMAR" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-27364 movement=27364 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6454 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6454 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6454 acc_name="SHREE GURU RAM RAI PHOTO FRAMING" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-36940 movement=36940 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6455 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6455 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6455 acc_name="SEHGAL GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6456 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6456 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6456 acc_name="ARORA ART GALARY" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6457 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6457 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6457 acc_name="GURUKRIPA PHOTO FRAME" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6458 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6458 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6458 acc_name="BOBBY KIRPAN & PICTURE HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6459 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6459 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6459 acc_name="ANSH BEHAL" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6460 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6460 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6460 acc_name="PAL LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6461 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6461 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6461 acc_name="BANSAL ENTERPRISES" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=85200 movement=-54050 closing_to_next_fy=31150 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6462 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6462 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6462 acc_name="MITTAL GLASS WORKS" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-180100 movement=165470 closing_to_next_fy=-14630 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6463 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6463 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6463 acc_name="SAI GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6464 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6464 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6464 acc_name="AARTI PHOTO FRAMING" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-37900 movement=37900 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6465 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6465 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6465 acc_name="NATIONAL GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-72500 movement=72500 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6466 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6466 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6466 acc_name="ANANT GLASS" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6467 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6467 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6467 acc_name="GR KATHURIA GLASS WORKS AMRITSAR" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6468 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6468 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6468 acc_name="RAJINDERA GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-38460 movement=38460 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6469 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6469 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6469 acc_name="SHREE BALAJI FRAME HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6470 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6470 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6470 acc_name="VIKAS RAKWAL" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6471 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6471 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6471 acc_name="SATISH KUMAR" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-38700 movement=38700 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6472 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6472 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6472 acc_name="VIRDHI GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6473 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6473 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6473 acc_name="AVTAR LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-54550 movement=54550 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6474 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6474 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6474 acc_name="MANGAL GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-27640 movement=27640 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6475 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6475 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6475 acc_name="VEE KAY LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6476 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6476 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6476 acc_name="KALIA PHOTO LAB" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6477 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6477 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6477 acc_name="NARINDER SINGH" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6478 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6478 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6478 acc_name="SONU LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-32570 movement=32570 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6479 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6479 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6479 acc_name="SHIV JOYTI PHOTO FRAMES AND LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6480 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6480 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6480 acc_name="SAKSHAM GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6481 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6481 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6481 acc_name="ANAND LAMINATION & FRAMING" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6482 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6482 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6482 acc_name="SONU LAMINATION & FRAME" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6483 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6483 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6483 acc_name="KALIA PHOTO FRAMING" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6484 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6484 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6484 acc_name="GAGAN MAHAJAN" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-91323 movement=91323 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6485 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6485 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6485 acc_name="ARJUN GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6486 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6486 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6486 acc_name="R.B TRADERS" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6487 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6487 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6487 acc_name="SUKHDEV PICTURE HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6488 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6488 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6488 acc_name="GANESH LAMINATIOM" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6489 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6489 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6489 acc_name="SOOD PHOTO LAMINATION & FRAMING" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6490 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6490 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6490 acc_name="S.K. SURI & SONS" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-1450 movement=1450 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6491 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6491 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6491 acc_name="R.K FOTO FRAME" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6492 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6492 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6492 acc_name="Jain Art Gallery" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6493 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6493 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6493 acc_name="BABA FOTO FRAMING" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6494 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6494 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6494 acc_name="SHANKAR FRAMES WORKS" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6495 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6495 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6495 acc_name="SONU GLASS HOUSE 2" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6496 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6496 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6496 acc_name="LAKHAN GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-350 movement=350 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6497 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6497 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6497 acc_name="JASSAL GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6498 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6498 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6498 acc_name="NARINDER KUMAR ANEJA" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6499 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6499 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6499 acc_name="HAZOOR LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6500 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6500 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6500 acc_name="JASSAL PHOTO FRAME" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-8256 movement=8256 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6501 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6501 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6501 acc_name="HARMANDIP SINGH" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6502 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6502 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6502 acc_name="MANI LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6503 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6503 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6503 acc_name="RAJINDER PHOTO FRAME" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6504 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6504 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6504 acc_name="VANSH LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6505 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6505 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6505 acc_name="JAIN PHOTO FRAME" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-84120 movement=84120 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6506 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6506 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:44 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6506 acc_name="SANT ART GALLERY" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-13900 movement=13900 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:45 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6507 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6507 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6507 acc_name="MALHAN GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-27580 movement=2550 closing_to_next_fy=-25030 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6508 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6508 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6508 acc_name="VIJAY GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6509 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6509 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6509 acc_name="BABAJI PICTURE CORNER" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6510 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6510 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6510 acc_name="S P PICTURE HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6511 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6511 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6511 acc_name="S.K PHOTO LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6512 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6512 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6512 acc_name="SONY LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6513 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6513 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6513 acc_name="SONI LAMINATION ARTS" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-2250 movement=2250 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6514 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6514 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6514 acc_name="PURI THREAD HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6515 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6515 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6515 acc_name="VISHKARMA FOTO FRAME" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-22900 movement=22900 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6516 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6516 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6516 acc_name="KHALSA FOTO FRAMING" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6517 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6517 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6517 acc_name="ARTIFICATION STUDIO" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6518 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6518 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6518 acc_name="JAIN PHOTO LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6519 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6519 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6519 acc_name="ASHWI INDUSTRIES" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6520 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6520 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6520 acc_name="PUNJAB GLASS HOUSE GOBINDGARH" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6521 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6521 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6521 acc_name="Tarsem Lamination" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-21600 movement=21600 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6522 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6522 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6522 acc_name="SANDHU GENERAL STORE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6523 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6523 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6523 acc_name="BHINDER LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6524 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6524 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6524 acc_name="SHRI GURU RAM RAI PHOTO FRAMING" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6525 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6525 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6525 acc_name="FATEH SINGH" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6526 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6526 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6526 acc_name="ICICI BANK 0390" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6527 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6527 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6527 acc_name="KARAN GUPTA CURRENT AC" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6528 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6528 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6528 acc_name="Trade Era Filings LLP" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-9150 movement=-16650 closing_to_next_fy=-25800 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6529 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6529 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6529 acc_name="GST PAYABLE" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6530 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6530 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6530 acc_name="TRAVELLING EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6531 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6531 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6531 acc_name="POWER & FUEL EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6532 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6532 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6532 acc_name="TELEPHONE EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6533 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6533 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6533 acc_name="RAHUL B GUPTA JAL. TAX" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6534 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6534 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6534 acc_name="RAHUL B GUPTA & CO" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=-14160 closing_to_next_fy=-14160 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6535 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6535 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6535 acc_name="GST PAID" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6536 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6536 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6536 acc_name="SERVICE CHAREGES" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6537 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6537 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6537 acc_name="ICICI BANK A/C 00416" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=15890.67 movement=-708 closing_to_next_fy=15182.67 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6538 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6538 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6538 acc_name="Bank Charges" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6539 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6539 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6539 acc_name="KANAV GUPTA CAPITAL A/C" parent_id=1 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6540 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6540 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6540 acc_name="KARAN GUPTA" parent_id=1 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-3000 movement=0 closing_to_next_fy=-3000 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6541 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6541 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6541 acc_name="RESERVE & SURPLUS" parent_id=1 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-10152.19 movement=0 closing_to_next_fy=-10152.19 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6542 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6542 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6542 acc_name="KANAV GUPTA CURRENT A/C" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6543 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6543 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6543 acc_name="COMMISSION PAYABLE" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6544 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6544 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6544 acc_name="Income Tax Refund" | skipped due to restricted parent_id=12
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6545 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6545 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6545 acc_name="MUSKAN GUPTA CAPITAL A/C" parent_id=1 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-3000 movement=0 closing_to_next_fy=-3000 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6546 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6546 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6546 acc_name="LEENA GUPTA" parent_id=1 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-3000 movement=0 closing_to_next_fy=-3000 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6547 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6547 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6547 acc_name="RENU BALA Current A/c" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6548 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6548 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6548 acc_name="RAHUL GUPTA CURRENT A/C" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=683983 closing_to_next_fy=683983 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6549 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6549 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6549 acc_name="AMANDEEP SINGH" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6550 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6550 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6550 acc_name="DEEPAK SHARMA" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6551 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6551 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6551 acc_name="MUSKAN GUPTA CURRENT A/C" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6552 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6552 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6552 acc_name="LEENA GUPTA CURRENT A/C" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6553 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6553 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6553 acc_name="RENU BALA CAPITAL A/C" parent_id=1 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-1000 movement=0 closing_to_next_fy=-1000 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6554 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6554 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6554 acc_name="Kanav Gupta USL A/C" parent_id=3 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6555 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6555 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6555 acc_name="WAGES EXPENSE" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6556 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6556 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6556 acc_name="PACKING EXPENSE" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6557 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6557 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6557 acc_name="WORKFORCE CONNECT" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6558 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6558 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6558 acc_name="ONE STEP SOLUTION" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6559 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6559 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6559 acc_name="DYNAMIC SOLUTIONS" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=82501.63 movement=-82501.63 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6560 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6560 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6560 acc_name="GYTRI TRADERS" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6561 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6561 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6561 acc_name="UNIVERSAL TRADING CO." parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6562 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6562 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6562 acc_name="BRAND CRAFT" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6563 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6563 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6563 acc_name="CORNER LINK" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6564 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6564 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6564 acc_name="KARAN GUPTA ENDORSEMENT" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6565 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6565 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6565 acc_name="MUSKAN ENDORSEMENT" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6566 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6566 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6566 acc_name="MUKESH GUPTA ENDORSEMENT" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6567 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6567 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6567 acc_name="LEENA GUPTA ENDORSEMENT" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6568 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6568 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6568 acc_name="Cgst" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6569 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6569 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6569 acc_name="Igst" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6570 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6570 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6570 acc_name="Cess On Gst" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6571 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6571 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6571 acc_name="It Tcs" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6572 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6572 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6572 acc_name="It Tds" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6573 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6573 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6573 acc_name="Gst Tcs" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6574 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6574 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6574 acc_name="Gst Tds" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6575 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6575 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6575 acc_name="Discount" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6576 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6576 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6576 acc_name="Excise Duty" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6577 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6577 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6577 acc_name="Vat" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6578 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6578 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6578 acc_name="Cst" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6579 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6579 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6579 acc_name="Round Off" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=-0.02 closing_to_next_fy=-0.02 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6580 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6580 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6580 acc_name="CGST INPUT" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=409857.85 closing_to_next_fy=409857.85 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6581 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6581 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6581 acc_name="SGST INPUT" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6582 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6582 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6582 acc_name="Integrated Tax (IGST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=-1278 closing_to_next_fy=-1278 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6583 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6583 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6583 acc_name="UT Tax (UGST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6584 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6584 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6584 acc_name="VESTA IND INDIA PVT LTD" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6585 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6585 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6585 acc_name="GANESH LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6586 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6586 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6586 acc_name="ICICI BANK A/C 0390" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=85469.61 movement=8483.34 closing_to_next_fy=93952.95 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6587 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6587 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6587 acc_name="MIDDA GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6588 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6588 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6588 acc_name="EXCLUSIVE PHOTO FRAME" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6589 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6589 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6589 acc_name="AFGAN STUDIO" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6590 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6590 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6590 acc_name="OMIKA STUDIO" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6591 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6591 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6591 acc_name="HARPREET SINGH" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6592 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6592 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6592 acc_name="CHOPRA GENERAL STORE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6593 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6593 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6593 acc_name="Rahul Gupta" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6594 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6594 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6594 acc_name="SABHARWAL PHOTO FRAMES" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6595 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6595 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6595 acc_name="NATIONAL GLASS WORKS" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6596 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6596 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6596 acc_name="AASHVI INDUSTRIES" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6597 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6597 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6597 acc_name="G.R. KATHURIA GLASS WORKS AMRITSAR" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=-125 closing_to_next_fy=-125 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6598 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6598 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6598 acc_name="BHUPINDER SINGH" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6599 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6599 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6599 acc_name="ANIL TIWARI" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6600 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6600 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6600 acc_name="PUNJAB POSTERS" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6601 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6601 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6601 acc_name="RAVINDER GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6602 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6602 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6602 acc_name="VARUN SURI" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6603 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6603 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6603 acc_name="PREET PICTURE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6604 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6604 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6604 acc_name="SALARY EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6605 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6605 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6605 acc_name="S.P PICTURE HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-1300 movement=1300 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6606 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6606 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6606 acc_name="SUDHA FRAMING WORKS" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:45 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6607 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6607 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6607 acc_name="RAHUL B GUPTA& CO" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6608 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6608 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6608 acc_name="GST PAID IN RETURNS" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6609 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6609 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6609 acc_name="KARAN GUPTA CAPITAL" parent_id=1 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6610 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6610 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6610 acc_name="PROFIT & LOSS A/C" parent_id=1 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=304428.27 movement=0 closing_to_next_fy=304428.27 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6611 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6611 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6611 acc_name="GST PAYABLE A/C" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-17714 movement=7525 closing_to_next_fy=-10189 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6612 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6612 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6612 acc_name="GST PAID (1)" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6613 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6613 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6613 acc_name="BANK CHARGES (1)" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6614 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6614 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6614 acc_name="MOHIT KUMAR VERMA" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6615 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6615 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6615 acc_name="BINDER LAMINATION" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6616 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6616 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6616 acc_name="JUNEJA ENTERPRISES" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6617 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6617 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6617 acc_name="GURUNANAK PICTURE HOUSE (PATIALA)" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6618 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6618 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6618 acc_name="RAJU GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6619 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6619 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6619 acc_name="BAWEJA GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6620 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6620 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6620 acc_name="JINDER STUDIO" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6621 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6621 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6621 acc_name="RAMAN PHOTO ART GALLERY" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6622 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6622 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6622 acc_name="ROSEWOOD RESORT" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6623 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6623 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6623 acc_name="GREEN LEAF COMPUTER SYSTEMS" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6624 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6624 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6624 acc_name="ENTERTAINMENT EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6625 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6625 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6625 acc_name="KAHAN SINGH SOHAN SINGH" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6626 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6626 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6626 acc_name="SUSPENSE" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6627 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6627 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6627 acc_name="Manmohan Goyal" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6628 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6628 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6628 acc_name="Professional Fees" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6629 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6629 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6629 acc_name="Professional Fees Taxable" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6630 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6630 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6630 acc_name="ProfsIndia" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6631 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6631 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6631 acc_name="Fees And Taxes" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6632 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6632 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6632 acc_name="IT TDS PAYABLE" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=-3500 closing_to_next_fy=-3500 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6633 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6633 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6633 acc_name="ICICI BANK CREDTORS" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6634 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6634 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6634 acc_name="TRADE ERA FILING LLP TAX" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6635 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6635 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6635 acc_name="Refreshment Expenses" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6636 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6636 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6636 acc_name="VISHAVKARMA GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6637 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6637 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6637 acc_name="GIAN SINGH ENTERPRISES" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6638 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6638 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6638 acc_name="NAGPAL GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6639 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6639 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6639 acc_name="DASHMESH PICTURE HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6640 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6640 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6640 acc_name="JAIN GLASS HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6641 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6641 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6641 acc_name="AMRITSAR GIFT HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6642 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6642 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6642 acc_name="KRISHNA PICTURE HOUSE" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6643 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6643 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6643 acc_name="PHOTO HUB" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6644 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6644 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6644 acc_name="MUKESH KUMAR GUPTA" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6645 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6645 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6645 acc_name="JYOTI KUMARI" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6646 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6646 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6646 acc_name="INSEEM ANGLAR PRIVATE LIMITED" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=-1639966.37 closing_to_next_fy=-1639966.37 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6647 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6647 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6647 acc_name="BUSINESS PROMOTION" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6648 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6648 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6648 acc_name="MUSKAN GUPTA ENDORSEMENT" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6649 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6649 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6649 acc_name="CGST PAID (INSEEM)" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6650 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6650 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6650 acc_name="SGST PAID ( INSEEM)" | skipped due to restricted parent_id=13
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6651 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6651 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6651 acc_name="Central Tax (CGST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6652 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6652 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6652 acc_name="State Tax (SGST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6653 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6653 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6653 acc_name="SGST (INPUT)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=409857.85 closing_to_next_fy=409857.85 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6654 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6654 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6654 acc_name="IGST (INPUT)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=353.64 closing_to_next_fy=353.64 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6655 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6655 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6655 acc_name="Cess (GST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6656 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6656 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6656 acc_name="TDS 194" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6657 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6657 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6657 acc_name="BHASIN GLASS HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6658 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6658 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6658 acc_name="Sharma Glass House" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6659 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6659 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6659 acc_name="TARSEM SINGH CEEMA HARIANA" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6660 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6660 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6660 acc_name="SHIV SHAKTI TRADERS" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6661 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6661 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6661 acc_name="RITIKA GIFT HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6662 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6662 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6662 acc_name="SANJAY KUMAR" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6663 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6663 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6663 acc_name="SK PHOTO LAMINATION" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6664 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6664 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6664 acc_name="Surindera Photo Frame" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6665 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6665 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6665 acc_name="SINGH STUDIO" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6666 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6666 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6666 acc_name="MANISH BAJAJ C/O BAJAJ CREATIONS" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6667 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6667 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6667 acc_name="BALWANT GENERAL STORE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6668 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6668 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6668 acc_name="AMRIK SINGH" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6669 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6669 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6669 acc_name="R K FOTO FRAME" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6670 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6670 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6670 acc_name="HONEY PHOTO FRAME" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6671 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6671 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6671 acc_name="WAGES" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6672 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6672 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6672 acc_name="PACKING EXPENSES" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6673 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6673 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6673 acc_name="SACHDEVA FRAME HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6674 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6674 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6674 acc_name="HAPPY PHOTO FRAME" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6675 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6675 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6675 acc_name="S K SURI & SONS" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6676 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6676 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6676 acc_name="R .K PHOTO FRAME" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6677 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6677 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6677 acc_name="JITENDER" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6678 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6678 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6678 acc_name="SIGMA ELECTRONICS" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6679 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6679 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6679 acc_name="PHOTO GEM" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6680 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6680 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6680 acc_name="NOOR PHOTO LAB" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6681 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6681 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6681 acc_name="BHAMBRA GLASS HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6682 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6682 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6682 acc_name="Lady With Ideas" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6683 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6683 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6683 acc_name="MITTAL GLASS WORKS 2" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6684 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6684 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6684 acc_name="SONY LAMINATION ARTS" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6685 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6685 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6685 acc_name="THE B CALLIGRAPHY CO" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6686 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6686 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6686 acc_name="RAMAN GLASS HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6687 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6687 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6687 acc_name="Packing Material" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6688 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6688 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6688 acc_name="SHARMA LAMINATION HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6689 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6689 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6689 acc_name="AMAR ARTIST" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6690 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6690 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6690 acc_name="DATTA GENERAL STORE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6691 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6691 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6691 acc_name="SURI PICTURE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6692 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6692 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6692 acc_name="ASHOKA GLASS HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6693 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6693 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6693 acc_name="GURKANWAL" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6694 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6694 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6694 acc_name="CHAWLA GLASS HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6695 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6695 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6695 acc_name="SANJIV SINGLA C/O SINGLA PAINTS" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6696 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6696 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6696 acc_name="NARIDNER STUDIO" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6697 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6697 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6697 acc_name="NARINDER STUDIO" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6698 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6698 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6698 acc_name="ANAND INTERIOR" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6699 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6699 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6699 acc_name="DECENT GLASS HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6700 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6700 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6700 acc_name="BHUPINDER PAL SINGH" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6701 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6701 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6701 acc_name="BANSAL GLASS STORE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6702 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6702 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6702 acc_name="SUNDER KUMAR" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6703 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6703 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6703 acc_name="JAIN PHOTO GRAPHER" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6704 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6704 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6704 acc_name="SEHDEV GLASS" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6705 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6705 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6705 acc_name="SACHIN KUMAR" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6706 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6706 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:45 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6706 acc_name="BALVIR ARTS" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:46 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 159
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1763_8','1764_1409','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6707 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6707 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6707 acc_name="MAKHAN SINGH" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6708 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6708 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6708 acc_name="VICKY KUMAR" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6709 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6709 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6709 acc_name="GAURAV PICTURE HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6710 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6710 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6710 acc_name="HARRY PHOTO FRAME" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6711 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6711 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6711 acc_name="HARSHIT BANSAL" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6712 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6712 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6712 acc_name="SONU PICTURE HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6713 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6713 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6713 acc_name="SATISH GLASS HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6714 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6714 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6714 acc_name="Amit Jeet Singh" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6715 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6715 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6715 acc_name="CHANDAN KUMAR" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6716 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6716 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6716 acc_name="Spring Bells Day Boarding School" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6717 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6717 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6717 acc_name="MUKESH KUMAR" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6720 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6720 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6720 acc_name="IGST OUTPUT A/C" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6721 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6721 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6721 acc_name="IGST INPUT A/C" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6722 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6722 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6722 acc_name="CGST OUTPUT A/C" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6723 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6723 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6723 acc_name="CGST INPUT A/C" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6724 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6724 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6724 acc_name="SGST OUTPUT A/C" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6725 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6725 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6725 acc_name="SGST INPUT A/C" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6726 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6726 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6726 acc_name="UGST OUTPUT A/C" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6727 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6727 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6727 acc_name="UGST INPUT A/C" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6728 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6728 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6728 acc_name="CESS (GST) OUTPUT A/C" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6729 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6729 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=6729 acc_name="CESS (GST) INPUT A/C" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=7476 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=7476 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=7476 acc_name="KOMAL PHOTO FRAMING" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=8884 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=8884 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=8884 acc_name="Gurcharan Singh" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=9971 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=9971 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=9971 acc_name="NANAK SINGH KIRPANA WALE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=9972 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=9972 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=9972 acc_name="RAJESH TROPHY HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=10192 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=10192 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=10192 acc_name="VK PHOTO LAMINATION" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=10337 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=10337 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=10337 acc_name="RANA LAMINATION & PHOTO FRAMING" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=10371 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=10371 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=10371 acc_name="Bitta Studio" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=17716 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=17716 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=17716 acc_name="GORAV PHOTO FRAME" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=17717 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=17717 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=17717 acc_name="RB TRADERS" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20003 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20003 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20003 acc_name="Noor Photo Frame" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20004 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20004 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20004 acc_name="Bhag Charan Trading Company" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20005 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20005 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20005 acc_name="SONI" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20006 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20006 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20006 acc_name="Bhushan Lamination" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20007 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20007 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20007 acc_name="SODI GLASS HOUSE C/O MAHANT CHARN PRAKASH" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20011 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20011 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=20011 acc_name="Sanjay Singh" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=21945 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=21945 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=21945 acc_name="AMRIK GLASS HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=21946 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=21946 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=21946 acc_name="MODERN PICTURE HOUSE" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=21947 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=6408 openingPL=957182.48 netMovePL=0 closingPL=957182.48 netProfitLoss=9078034.44 carryForwardPL=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=21947 P&L Appropriation nextFY opening UPDATED | plAccId=6408 bal=10035216.92
1 - 2026-09-15 13:18:46 --> [FY MIGRATION] cmp_id=98 hobo_id=151 hobo_name="HO" curr_fy=159 next_fy=160 acc_id=21947 acc_name="TALWINDER SINGH" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER START | item_id=1762 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER units found: [{"itm_id_unit_id":"1762_1412"}]

1 - 2026-09-15 13:18:49 --> UNIT BAL START | itm_id_unit_id=1762_1412

1 - 2026-09-15 13:18:49 --> UNIT BAL openings | itm_id_unit_id=1762_1412 | []

1 - 2026-09-15 13:18:49 --> UNIT BAL movements | itm_id_unit_id=1762_1412 | [{"mat_cent_id":"98","net_qty":"-1804.00"}]

1 - 2026-09-15 13:18:49 --> UNIT BAL INSERT | itm_id_unit_id=1762_1412 | center=98 | opening=0 | net=-1804 | closing=-1804 | next_fy=160

1 - 2026-09-15 13:18:49 --> UNIT BAL DONE | itm_id_unit_id=1762_1412

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER SUCCESS | item_id=1762 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER START | item_id=1763 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER units found: [{"itm_id_unit_id":"1763_8"}]

1 - 2026-09-15 13:18:49 --> UNIT BAL START | itm_id_unit_id=1763_8

1 - 2026-09-15 13:18:49 --> UNIT BAL openings | itm_id_unit_id=1763_8 | {"98":0}

1 - 2026-09-15 13:18:49 --> UNIT BAL movements | itm_id_unit_id=1763_8 | []

1 - 2026-09-15 13:18:49 --> UNIT BAL INSERT | itm_id_unit_id=1763_8 | center=98 | opening=0 | net=0 | closing=0 | next_fy=160

1 - 2026-09-15 13:18:49 --> UNIT BAL DONE | itm_id_unit_id=1763_8

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER SUCCESS | item_id=1763 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER START | item_id=1764 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER units found: [{"itm_id_unit_id":"1764_1409"},{"itm_id_unit_id":"1764_8"}]

1 - 2026-09-15 13:18:49 --> UNIT BAL START | itm_id_unit_id=1764_1409

1 - 2026-09-15 13:18:49 --> UNIT BAL openings | itm_id_unit_id=1764_1409 | []

1 - 2026-09-15 13:18:49 --> UNIT BAL movements | itm_id_unit_id=1764_1409 | [{"mat_cent_id":"98","net_qty":"0.00"}]

1 - 2026-09-15 13:18:49 --> UNIT BAL INSERT | itm_id_unit_id=1764_1409 | center=98 | opening=0 | net=0 | closing=0 | next_fy=160

1 - 2026-09-15 13:18:49 --> UNIT BAL DONE | itm_id_unit_id=1764_1409

1 - 2026-09-15 13:18:49 --> UNIT BAL START | itm_id_unit_id=1764_8

1 - 2026-09-15 13:18:49 --> UNIT BAL openings | itm_id_unit_id=1764_8 | {"98":0}

1 - 2026-09-15 13:18:49 --> UNIT BAL movements | itm_id_unit_id=1764_8 | []

1 - 2026-09-15 13:18:49 --> UNIT BAL INSERT | itm_id_unit_id=1764_8 | center=98 | opening=0 | net=0 | closing=0 | next_fy=160

1 - 2026-09-15 13:18:49 --> UNIT BAL DONE | itm_id_unit_id=1764_8

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER SUCCESS | item_id=1764 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER START | item_id=1765 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER units found: [{"itm_id_unit_id":"1765_1404"},{"itm_id_unit_id":"1765_3"}]

1 - 2026-09-15 13:18:49 --> UNIT BAL START | itm_id_unit_id=1765_1404

1 - 2026-09-15 13:18:49 --> UNIT BAL openings | itm_id_unit_id=1765_1404 | []

1 - 2026-09-15 13:18:49 --> UNIT BAL movements | itm_id_unit_id=1765_1404 | [{"mat_cent_id":"98","net_qty":"0.00"}]

1 - 2026-09-15 13:18:49 --> UNIT BAL INSERT | itm_id_unit_id=1765_1404 | center=98 | opening=0 | net=0 | closing=0 | next_fy=160

1 - 2026-09-15 13:18:49 --> UNIT BAL DONE | itm_id_unit_id=1765_1404

1 - 2026-09-15 13:18:49 --> UNIT BAL START | itm_id_unit_id=1765_3

1 - 2026-09-15 13:18:49 --> UNIT BAL openings | itm_id_unit_id=1765_3 | {"98":0}

1 - 2026-09-15 13:18:49 --> UNIT BAL movements | itm_id_unit_id=1765_3 | []

1 - 2026-09-15 13:18:49 --> UNIT BAL INSERT | itm_id_unit_id=1765_3 | center=98 | opening=0 | net=0 | closing=0 | next_fy=160

1 - 2026-09-15 13:18:49 --> UNIT BAL DONE | itm_id_unit_id=1765_3

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER SUCCESS | item_id=1765 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER START | item_id=1766 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER units found: [{"itm_id_unit_id":"1766_1409"},{"itm_id_unit_id":"1766_8"}]

1 - 2026-09-15 13:18:49 --> UNIT BAL START | itm_id_unit_id=1766_1409

1 - 2026-09-15 13:18:49 --> UNIT BAL openings | itm_id_unit_id=1766_1409 | []

1 - 2026-09-15 13:18:49 --> UNIT BAL movements | itm_id_unit_id=1766_1409 | [{"mat_cent_id":"98","net_qty":"1.00"}]

1 - 2026-09-15 13:18:49 --> UNIT BAL INSERT | itm_id_unit_id=1766_1409 | center=98 | opening=0 | net=1 | closing=1 | next_fy=160

1 - 2026-09-15 13:18:49 --> UNIT BAL DONE | itm_id_unit_id=1766_1409

1 - 2026-09-15 13:18:49 --> UNIT BAL START | itm_id_unit_id=1766_8

1 - 2026-09-15 13:18:49 --> UNIT BAL openings | itm_id_unit_id=1766_8 | {"98":0}

1 - 2026-09-15 13:18:49 --> UNIT BAL movements | itm_id_unit_id=1766_8 | []

1 - 2026-09-15 13:18:49 --> UNIT BAL INSERT | itm_id_unit_id=1766_8 | center=98 | opening=0 | net=0 | closing=0 | next_fy=160

1 - 2026-09-15 13:18:49 --> UNIT BAL DONE | itm_id_unit_id=1766_8

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER SUCCESS | item_id=1766 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER START | item_id=1767 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER units found: [{"itm_id_unit_id":"1767_1409"},{"itm_id_unit_id":"1767_8"}]

1 - 2026-09-15 13:18:49 --> UNIT BAL START | itm_id_unit_id=1767_1409

1 - 2026-09-15 13:18:49 --> UNIT BAL openings | itm_id_unit_id=1767_1409 | []

1 - 2026-09-15 13:18:49 --> UNIT BAL movements | itm_id_unit_id=1767_1409 | [{"mat_cent_id":"98","net_qty":"0.00"}]

1 - 2026-09-15 13:18:49 --> UNIT BAL INSERT | itm_id_unit_id=1767_1409 | center=98 | opening=0 | net=0 | closing=0 | next_fy=160

1 - 2026-09-15 13:18:49 --> UNIT BAL DONE | itm_id_unit_id=1767_1409

1 - 2026-09-15 13:18:49 --> UNIT BAL START | itm_id_unit_id=1767_8

1 - 2026-09-15 13:18:49 --> UNIT BAL openings | itm_id_unit_id=1767_8 | {"98":0}

1 - 2026-09-15 13:18:49 --> UNIT BAL movements | itm_id_unit_id=1767_8 | []

1 - 2026-09-15 13:18:49 --> UNIT BAL INSERT | itm_id_unit_id=1767_8 | center=98 | opening=0 | net=0 | closing=0 | next_fy=160

1 - 2026-09-15 13:18:49 --> UNIT BAL DONE | itm_id_unit_id=1767_8

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER SUCCESS | item_id=1767 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER START | item_id=1768 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER units found: []

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER no units to process for item_id=1768

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER START | item_id=1769 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER units found: []

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER no units to process for item_id=1769

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER START | item_id=39525 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER units found: []

1 - 2026-09-15 13:18:49 --> ITEM BAL ROLLOVER no units to process for item_id=39525

1 - 2026-09-15 13:18:51 --> ITEM VAL ROLLOVER START | item_id=1762 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:51 --> ITEM VAL ROLLOVER units found: [{"itm_id_unit_id":"1762_1412"}]

1 - 2026-09-15 13:18:51 --> UNIT VAL START | itm_id_unit_id=1762_1412

1 - 2026-09-15 13:18:51 --> UNIT VAL opening | 1762_1412 | opQty=0 | opVal=0 | opRate=0

1 - 2026-09-15 13:18:51 --> UNIT VAL RESULT | 1762_1412 | closingVal=4590450 | closingQty=-1804

1 - 2026-09-15 13:18:51 --> UNIT VAL DELETED old rows | 1762_1412 | next_fy=160

1 - 2026-09-15 13:18:51 --> UNIT VAL INSERT | 1762_1412 | center=0 | val=4590450 | method=AVG | next_fy=160

1 - 2026-09-15 13:18:51 --> UNIT VAL DONE | 1762_1412

1 - 2026-09-15 13:18:51 --> ITEM VAL ROLLOVER SUCCESS | item_id=1762 | next_fy=160

1 - 2026-09-15 13:18:51 --> ITEM VAL ROLLOVER START | item_id=1763 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:51 --> ITEM VAL ROLLOVER units found: [{"itm_id_unit_id":"1763_8"}]

1 - 2026-09-15 13:18:51 --> UNIT VAL START | itm_id_unit_id=1763_8

1 - 2026-09-15 13:18:51 --> UNIT VAL opening | 1763_8 | opQty=0 | opVal=0 | opRate=0

1 - 2026-09-15 13:18:51 --> UNIT VAL RESULT | 1763_8 | closingVal=0 | closingQty=0

1 - 2026-09-15 13:18:51 --> UNIT VAL DELETED old rows | 1763_8 | next_fy=160

1 - 2026-09-15 13:18:51 --> UNIT VAL INSERT | 1763_8 | center=0 | val=0 | method=AVG | next_fy=160

1 - 2026-09-15 13:18:51 --> UNIT VAL DONE | 1763_8

1 - 2026-09-15 13:18:51 --> ITEM VAL ROLLOVER SUCCESS | item_id=1763 | next_fy=160

1 - 2026-09-15 13:18:51 --> ITEM VAL ROLLOVER START | item_id=1764 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:51 --> ITEM VAL ROLLOVER units found: [{"itm_id_unit_id":"1764_1409"},{"itm_id_unit_id":"1764_8"}]

1 - 2026-09-15 13:18:51 --> UNIT VAL START | itm_id_unit_id=1764_1409

1 - 2026-09-15 13:18:51 --> UNIT VAL opening | 1764_1409 | opQty=0 | opVal=0 | opRate=0

1 - 2026-09-15 13:18:51 --> UNIT VAL RESULT | 1764_1409 | closingVal=0 | closingQty=0

1 - 2026-09-15 13:18:51 --> UNIT VAL DELETED old rows | 1764_1409 | next_fy=160

1 - 2026-09-15 13:18:51 --> UNIT VAL INSERT | 1764_1409 | center=0 | val=0 | method=AVG | next_fy=160

1 - 2026-09-15 13:18:51 --> UNIT VAL DONE | 1764_1409

1 - 2026-09-15 13:18:51 --> UNIT VAL START | itm_id_unit_id=1764_8

1 - 2026-09-15 13:18:51 --> UNIT VAL opening | 1764_8 | opQty=0 | opVal=0 | opRate=0

1 - 2026-09-15 13:18:51 --> UNIT VAL RESULT | 1764_8 | closingVal=0 | closingQty=0

1 - 2026-09-15 13:18:51 --> UNIT VAL DELETED old rows | 1764_8 | next_fy=160

1 - 2026-09-15 13:18:51 --> UNIT VAL INSERT | 1764_8 | center=0 | val=0 | method=AVG | next_fy=160

1 - 2026-09-15 13:18:51 --> UNIT VAL DONE | 1764_8

1 - 2026-09-15 13:18:51 --> ITEM VAL ROLLOVER SUCCESS | item_id=1764 | next_fy=160

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER START | item_id=1765 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER units found: [{"itm_id_unit_id":"1765_1404"},{"itm_id_unit_id":"1765_3"}]

1 - 2026-09-15 13:18:52 --> UNIT VAL START | itm_id_unit_id=1765_1404

1 - 2026-09-15 13:18:52 --> UNIT VAL opening | 1765_1404 | opQty=0 | opVal=0 | opRate=0

1 - 2026-09-15 13:18:52 --> UNIT VAL RESULT | 1765_1404 | closingVal=1700 | closingQty=0

1 - 2026-09-15 13:18:52 --> UNIT VAL DELETED old rows | 1765_1404 | next_fy=160

1 - 2026-09-15 13:18:52 --> UNIT VAL INSERT | 1765_1404 | center=0 | val=1700 | method=AVG | next_fy=160

1 - 2026-09-15 13:18:52 --> UNIT VAL DONE | 1765_1404

1 - 2026-09-15 13:18:52 --> UNIT VAL START | itm_id_unit_id=1765_3

1 - 2026-09-15 13:18:52 --> UNIT VAL opening | 1765_3 | opQty=0 | opVal=0 | opRate=0

1 - 2026-09-15 13:18:52 --> UNIT VAL RESULT | 1765_3 | closingVal=0 | closingQty=0

1 - 2026-09-15 13:18:52 --> UNIT VAL DELETED old rows | 1765_3 | next_fy=160

1 - 2026-09-15 13:18:52 --> UNIT VAL INSERT | 1765_3 | center=0 | val=0 | method=AVG | next_fy=160

1 - 2026-09-15 13:18:52 --> UNIT VAL DONE | 1765_3

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER SUCCESS | item_id=1765 | next_fy=160

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER START | item_id=1766 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER units found: [{"itm_id_unit_id":"1766_1409"},{"itm_id_unit_id":"1766_8"}]

1 - 2026-09-15 13:18:52 --> UNIT VAL START | itm_id_unit_id=1766_1409

1 - 2026-09-15 13:18:52 --> UNIT VAL opening | 1766_1409 | opQty=0 | opVal=0 | opRate=0

1 - 2026-09-15 13:18:52 --> UNIT VAL RESULT | 1766_1409 | closingVal=408.53658536585 | closingQty=1

1 - 2026-09-15 13:18:52 --> UNIT VAL DELETED old rows | 1766_1409 | next_fy=160

1 - 2026-09-15 13:18:52 --> UNIT VAL INSERT | 1766_1409 | center=0 | val=408.53658536585 | method=AVG | next_fy=160

1 - 2026-09-15 13:18:52 --> UNIT VAL DONE | 1766_1409

1 - 2026-09-15 13:18:52 --> UNIT VAL START | itm_id_unit_id=1766_8

1 - 2026-09-15 13:18:52 --> UNIT VAL opening | 1766_8 | opQty=0 | opVal=0 | opRate=0

1 - 2026-09-15 13:18:52 --> UNIT VAL RESULT | 1766_8 | closingVal=0 | closingQty=0

1 - 2026-09-15 13:18:52 --> UNIT VAL DELETED old rows | 1766_8 | next_fy=160

1 - 2026-09-15 13:18:52 --> UNIT VAL INSERT | 1766_8 | center=0 | val=0 | method=AVG | next_fy=160

1 - 2026-09-15 13:18:52 --> UNIT VAL DONE | 1766_8

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER SUCCESS | item_id=1766 | next_fy=160

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER START | item_id=1767 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER units found: [{"itm_id_unit_id":"1767_1409"},{"itm_id_unit_id":"1767_8"}]

1 - 2026-09-15 13:18:52 --> UNIT VAL START | itm_id_unit_id=1767_1409

1 - 2026-09-15 13:18:52 --> UNIT VAL opening | 1767_1409 | opQty=0 | opVal=0 | opRate=0

1 - 2026-09-15 13:18:52 --> UNIT VAL RESULT | 1767_1409 | closingVal=0 | closingQty=0

1 - 2026-09-15 13:18:52 --> UNIT VAL DELETED old rows | 1767_1409 | next_fy=160

1 - 2026-09-15 13:18:52 --> UNIT VAL INSERT | 1767_1409 | center=0 | val=0 | method=AVG | next_fy=160

1 - 2026-09-15 13:18:52 --> UNIT VAL DONE | 1767_1409

1 - 2026-09-15 13:18:52 --> UNIT VAL START | itm_id_unit_id=1767_8

1 - 2026-09-15 13:18:52 --> UNIT VAL opening | 1767_8 | opQty=0 | opVal=0 | opRate=0

1 - 2026-09-15 13:18:52 --> UNIT VAL RESULT | 1767_8 | closingVal=0 | closingQty=0

1 - 2026-09-15 13:18:52 --> UNIT VAL DELETED old rows | 1767_8 | next_fy=160

1 - 2026-09-15 13:18:52 --> UNIT VAL INSERT | 1767_8 | center=0 | val=0 | method=AVG | next_fy=160

1 - 2026-09-15 13:18:52 --> UNIT VAL DONE | 1767_8

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER SUCCESS | item_id=1767 | next_fy=160

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER START | item_id=1768 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER units found: []

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER no units for item_id=1768

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER START | item_id=1769 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER units found: []

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER no units for item_id=1769

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER START | item_id=39525 | cmp_id=98 | curr_fy=159 | next_fy=160

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER units found: []

1 - 2026-09-15 13:18:52 --> ITEM VAL ROLLOVER no units for item_id=39525

1 - 2026-09-15 13:19:02 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 13:19:02 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:10 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:10 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:10 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:10 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:14 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:14 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:14 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:14 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:24 --> [LedgerCondensed][acc=6722] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 14:01:24 --> [LedgerCondensed][acc=6722] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 14:01:24 --> [LedgerCondensed][acc=6722] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 14:01:24 --> [LedgerCondensed][acc=6722] Q3-MainLedger => rows=100  total=163  |  0.0134s
1 - 2026-09-15 14:01:24 --> [LedgerCondensed][acc=6722] Q4-OtherAccounts => 100 rows  |  0.0052s
1 - 2026-09-15 14:01:24 --> [LedgerCondensed][acc=6722] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.6%)
   Q2-OpeningBalance           0.0003s   (  1.3%)
   Q3-MainLedger               0.0134s   ( 65.2%)
   Q4-OtherAccounts            0.0052s   ( 25.3%)
   BuildRecords                0.0003s   (  1.6%)
   TOTAL                       0.0206s   (100%)
[LedgerCondensed][acc=6722] ── FUNCTION END ──
1 - 2026-09-15 14:01:34 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:34 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:34 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:34 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:39 --> [LedgerCondensed][acc=6723] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 14:01:39 --> [LedgerCondensed][acc=6723] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 14:01:39 --> [LedgerCondensed][acc=6723] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 14:01:39 --> [LedgerCondensed][acc=6723] Q3-MainLedger => rows=8  total=8  |  0.0093s
1 - 2026-09-15 14:01:39 --> [LedgerCondensed][acc=6723] Q4-OtherAccounts => 8 rows  |  0.0015s
1 - 2026-09-15 14:01:39 --> [LedgerCondensed][acc=6723] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.2%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0093s   ( 74.9%)
   Q4-OtherAccounts            0.0015s   ( 12.3%)
   BuildRecords                0.0001s   (  0.5%)
   TOTAL                       0.0124s   (100%)
[LedgerCondensed][acc=6723] ── FUNCTION END ──
1 - 2026-09-15 14:01:40 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:40 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:40 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:40 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:47 --> [LedgerCondensed][acc=6725] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 14:01:47 --> [LedgerCondensed][acc=6725] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 14:01:47 --> [LedgerCondensed][acc=6725] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 14:01:47 --> [LedgerCondensed][acc=6725] Q3-MainLedger => rows=8  total=8  |  0.0093s
1 - 2026-09-15 14:01:47 --> [LedgerCondensed][acc=6725] Q4-OtherAccounts => 8 rows  |  0.001s
1 - 2026-09-15 14:01:47 --> [LedgerCondensed][acc=6725] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  7.0%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0093s   ( 78.9%)
   Q4-OtherAccounts            0.0010s   (  8.6%)
   BuildRecords                0.0000s   (  0.4%)
   TOTAL                       0.0117s   (100%)
[LedgerCondensed][acc=6725] ── FUNCTION END ──
1 - 2026-09-15 14:01:48 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:48 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:49 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:01:49 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:02:34 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:02:34 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:02:38 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:02:38 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:02:53 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:02:53 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:04:09 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:04:09 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:27:54 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:27:54 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:32:03 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:32:03 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:32:08 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:32:08 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:32:14 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:32:14 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:34:56 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:34:56 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:35:28 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:35:28 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:36:49 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:36:49 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:36:49 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:36:49 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:38:36 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:38:36 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:38:36 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:38:36 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:39:22 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:39:22 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:39:22 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:39:22 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:39:55 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:39:55 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:39:55 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:39:55 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:42:02 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:42:02 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:42:02 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:42:02 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:42:21 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:42:21 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:42:21 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:42:21 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:42:36 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:42:36 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:42:43 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:42:43 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:44:45 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:44:45 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:44:47 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:44:47 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:45:36 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:45:36 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:45:46 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 14:45:46 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1766_8','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:03:24 --> [LedgerCondensed][acc=6537] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:03:24 --> [LedgerCondensed][acc=6537] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:03:24 --> [LedgerCondensed][acc=6537] Q2-OpeningBalance => 15182.67  |  0.0003s
1 - 2026-09-15 15:03:24 --> [LedgerCondensed][acc=6537] Q3-MainLedger => rows=0  total=0  |  0.0017s
1 - 2026-09-15 15:03:24 --> [LedgerCondensed][acc=6537] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-15 15:03:24 --> [LedgerCondensed][acc=6537] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 28.5%)
   Q2-OpeningBalance           0.0003s   (  7.9%)
   Q3-MainLedger               0.0017s   ( 50.9%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0033s   (100%)
[LedgerCondensed][acc=6537] ── FUNCTION END ──
1 - 2026-09-15 15:22:45 --> [LedgerCondensed][acc=6584] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:22:45 --> [LedgerCondensed][acc=6584] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-15 15:22:45 --> [LedgerCondensed][acc=6584] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:22:45 --> [LedgerCondensed][acc=6584] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-15 15:22:45 --> [LedgerCondensed][acc=6584] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-15 15:22:45 --> [LedgerCondensed][acc=6584] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   ( 32.1%)
   Q2-OpeningBalance           0.0003s   (  8.5%)
   Q3-MainLedger               0.0016s   ( 48.0%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0034s   (100%)
[LedgerCondensed][acc=6584] ── FUNCTION END ──
1 - 2026-09-15 15:23:30 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:23:30 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:23:30 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-15 15:23:30 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0129s
1 - 2026-09-15 15:23:30 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0035s
1 - 2026-09-15 15:23:30 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.7%)
   Q2-OpeningBalance           0.0002s   (  1.2%)
   Q3-MainLedger               0.0129s   ( 71.0%)
   Q4-OtherAccounts            0.0035s   ( 19.1%)
   BuildRecords                0.0003s   (  1.4%)
   TOTAL                       0.0182s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:24:39 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35081
AND "vch_type_id" = 23
1 - 2026-09-15 15:24:39 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:24:39 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:24:39 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301651, Bill Sundry ID: 6723
1 - 2026-09-15 15:24:39 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-04-22","acc_txn_dr_cr":2,"acc_txn_amt":57037.5,"acc_txn_fcy":0,"vch_txn_id":301651,"txn_id":1023421,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:24:39 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:24:39 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301651, Bill Sundry ID: 6725
1 - 2026-09-15 15:24:39 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-04-22","acc_txn_dr_cr":2,"acc_txn_amt":57037.5,"acc_txn_fcy":0,"vch_txn_id":301651,"txn_id":1023422,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:24:40 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:24:40 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 15:24:40 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:24:40 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0093s
1 - 2026-09-15 15:24:40 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0026s
1 - 2026-09-15 15:24:40 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.1%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0093s   ( 67.2%)
   Q4-OtherAccounts            0.0026s   ( 18.9%)
   BuildRecords                0.0002s   (  1.5%)
   TOTAL                       0.0138s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:25:20 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35082
AND "vch_type_id" = 23
1 - 2026-09-15 15:25:20 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:25:20 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:25:20 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301652, Bill Sundry ID: 6723
1 - 2026-09-15 15:25:20 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-05-15","acc_txn_dr_cr":2,"acc_txn_amt":52650,"acc_txn_fcy":0,"vch_txn_id":301652,"txn_id":1023429,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:25:20 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:25:20 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301652, Bill Sundry ID: 6725
1 - 2026-09-15 15:25:20 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-05-15","acc_txn_dr_cr":2,"acc_txn_amt":52650,"acc_txn_fcy":0,"vch_txn_id":301652,"txn_id":1023430,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:25:21 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:25:21 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:25:21 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:25:21 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0094s
1 - 2026-09-15 15:25:21 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0025s
1 - 2026-09-15 15:25:21 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.6%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0094s   ( 68.5%)
   Q4-OtherAccounts            0.0025s   ( 18.3%)
   BuildRecords                0.0003s   (  1.8%)
   TOTAL                       0.0137s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:25:40 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35083
AND "vch_type_id" = 23
1 - 2026-09-15 15:25:40 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:25:40 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:25:40 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301653, Bill Sundry ID: 6723
1 - 2026-09-15 15:25:40 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-05-26","acc_txn_dr_cr":2,"acc_txn_amt":47385,"acc_txn_fcy":0,"vch_txn_id":301653,"txn_id":1023437,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:25:40 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:25:40 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301653, Bill Sundry ID: 6725
1 - 2026-09-15 15:25:40 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-05-26","acc_txn_dr_cr":2,"acc_txn_amt":47385,"acc_txn_fcy":0,"vch_txn_id":301653,"txn_id":1023438,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:25:41 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:25:41 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-15 15:25:41 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:25:41 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0095s
1 - 2026-09-15 15:25:41 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0025s
1 - 2026-09-15 15:25:41 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  8.0%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0095s   ( 66.8%)
   Q4-OtherAccounts            0.0025s   ( 17.4%)
   BuildRecords                0.0003s   (  2.2%)
   TOTAL                       0.0143s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:26:09 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35084
AND "vch_type_id" = 23
1 - 2026-09-15 15:26:09 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:26:09 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:26:09 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301654, Bill Sundry ID: 6723
1 - 2026-09-15 15:26:09 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-06-03","acc_txn_dr_cr":2,"acc_txn_amt":16672.5,"acc_txn_fcy":0,"vch_txn_id":301654,"txn_id":1023445,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:26:09 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:26:09 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301654, Bill Sundry ID: 6725
1 - 2026-09-15 15:26:09 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-06-03","acc_txn_dr_cr":2,"acc_txn_amt":16672.5,"acc_txn_fcy":0,"vch_txn_id":301654,"txn_id":1023446,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:26:10 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:26:10 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 15:26:10 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:26:10 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0091s
1 - 2026-09-15 15:26:10 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0022s
1 - 2026-09-15 15:26:10 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.5%)
   Q2-OpeningBalance           0.0003s   (  2.5%)
   Q3-MainLedger               0.0091s   ( 68.6%)
   Q4-OtherAccounts            0.0022s   ( 16.5%)
   BuildRecords                0.0002s   (  1.7%)
   TOTAL                       0.0132s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:26:32 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35085
AND "vch_type_id" = 23
1 - 2026-09-15 15:26:32 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:26:32 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:26:32 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301655, Bill Sundry ID: 6723
1 - 2026-09-15 15:26:32 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-07-08","acc_txn_dr_cr":2,"acc_txn_amt":17550,"acc_txn_fcy":0,"vch_txn_id":301655,"txn_id":1023453,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:26:32 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:26:32 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301655, Bill Sundry ID: 6725
1 - 2026-09-15 15:26:32 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-07-08","acc_txn_dr_cr":2,"acc_txn_amt":17550,"acc_txn_fcy":0,"vch_txn_id":301655,"txn_id":1023454,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:26:33 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:26:33 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:26:33 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-15 15:26:33 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0097s
1 - 2026-09-15 15:26:33 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0022s
1 - 2026-09-15 15:26:33 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.8%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0097s   ( 70.9%)
   Q4-OtherAccounts            0.0022s   ( 16.2%)
   BuildRecords                0.0002s   (  1.5%)
   TOTAL                       0.0136s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:27:03 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35086
AND "vch_type_id" = 23
1 - 2026-09-15 15:27:03 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:27:03 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:27:03 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301656, Bill Sundry ID: 6723
1 - 2026-09-15 15:27:03 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-08-27","acc_txn_dr_cr":2,"acc_txn_amt":3685.5,"acc_txn_fcy":0,"vch_txn_id":301656,"txn_id":1023461,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:27:03 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:27:03 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301656, Bill Sundry ID: 6725
1 - 2026-09-15 15:27:03 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-08-27","acc_txn_dr_cr":2,"acc_txn_amt":3685.5,"acc_txn_fcy":0,"vch_txn_id":301656,"txn_id":1023462,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:27:04 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:27:04 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:27:04 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:27:04 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0095s
1 - 2026-09-15 15:27:04 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0023s
1 - 2026-09-15 15:27:04 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.5%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0095s   ( 70.0%)
   Q4-OtherAccounts            0.0023s   ( 16.9%)
   BuildRecords                0.0002s   (  1.6%)
   TOTAL                       0.0135s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:28:19 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35087
AND "vch_type_id" = 23
1 - 2026-09-15 15:28:19 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:28:19 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:28:19 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301657, Bill Sundry ID: 6723
1 - 2026-09-15 15:28:19 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-09-09","acc_txn_dr_cr":2,"acc_txn_amt":17550,"acc_txn_fcy":0,"vch_txn_id":301657,"txn_id":1023469,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:28:19 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:28:19 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301657, Bill Sundry ID: 6725
1 - 2026-09-15 15:28:19 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-09-09","acc_txn_dr_cr":2,"acc_txn_amt":17550,"acc_txn_fcy":0,"vch_txn_id":301657,"txn_id":1023470,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:28:20 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:28:20 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:28:20 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:28:20 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0096s
1 - 2026-09-15 15:28:20 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0023s
1 - 2026-09-15 15:28:20 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.8%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0096s   ( 70.2%)
   Q4-OtherAccounts            0.0023s   ( 16.9%)
   BuildRecords                0.0002s   (  1.6%)
   TOTAL                       0.0137s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:28:37 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35088
AND "vch_type_id" = 23
1 - 2026-09-15 15:28:37 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:28:37 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:28:37 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301658, Bill Sundry ID: 6723
1 - 2026-09-15 15:28:37 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-09-09","acc_txn_dr_cr":2,"acc_txn_amt":17550,"acc_txn_fcy":0,"vch_txn_id":301658,"txn_id":1023477,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:28:37 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:28:37 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301658, Bill Sundry ID: 6725
1 - 2026-09-15 15:28:37 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-09-09","acc_txn_dr_cr":2,"acc_txn_amt":17550,"acc_txn_fcy":0,"vch_txn_id":301658,"txn_id":1023478,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:28:38 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:28:38 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:28:38 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:28:38 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0098s
1 - 2026-09-15 15:28:38 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0025s
1 - 2026-09-15 15:28:38 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.4%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0098s   ( 69.3%)
   Q4-OtherAccounts            0.0025s   ( 17.5%)
   BuildRecords                0.0002s   (  1.6%)
   TOTAL                       0.0141s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:28:59 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35089
AND "vch_type_id" = 23
1 - 2026-09-15 15:28:59 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:28:59 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:28:59 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301659, Bill Sundry ID: 6723
1 - 2026-09-15 15:28:59 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-09-10","acc_txn_dr_cr":2,"acc_txn_amt":52650,"acc_txn_fcy":0,"vch_txn_id":301659,"txn_id":1023485,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:28:59 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:28:59 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301659, Bill Sundry ID: 6725
1 - 2026-09-15 15:28:59 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-09-10","acc_txn_dr_cr":2,"acc_txn_amt":52650,"acc_txn_fcy":0,"vch_txn_id":301659,"txn_id":1023486,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:29:00 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:29:00 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:29:00 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:29:00 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.01s
1 - 2026-09-15 15:29:00 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0024s
1 - 2026-09-15 15:29:00 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.5%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0100s   ( 69.6%)
   Q4-OtherAccounts            0.0024s   ( 16.8%)
   BuildRecords                0.0002s   (  1.7%)
   TOTAL                       0.0144s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:29:36 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35090
AND "vch_type_id" = 23
1 - 2026-09-15 15:29:36 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:29:36 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:29:36 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301660, Bill Sundry ID: 6723
1 - 2026-09-15 15:29:36 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-10-06","acc_txn_dr_cr":2,"acc_txn_amt":52650,"acc_txn_fcy":0,"vch_txn_id":301660,"txn_id":1023493,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:29:36 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:29:36 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301660, Bill Sundry ID: 6725
1 - 2026-09-15 15:29:36 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-10-06","acc_txn_dr_cr":2,"acc_txn_amt":52650,"acc_txn_fcy":0,"vch_txn_id":301660,"txn_id":1023494,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:29:37 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:29:37 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 15:29:37 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:29:37 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0095s
1 - 2026-09-15 15:29:37 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0025s
1 - 2026-09-15 15:29:37 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.4%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0095s   ( 68.5%)
   Q4-OtherAccounts            0.0025s   ( 17.9%)
   BuildRecords                0.0002s   (  1.6%)
   TOTAL                       0.0138s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:30:00 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35091
AND "vch_type_id" = 23
1 - 2026-09-15 15:30:00 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:30:00 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:30:00 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301661, Bill Sundry ID: 6723
1 - 2026-09-15 15:30:00 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-10-15","acc_txn_dr_cr":2,"acc_txn_amt":52650,"acc_txn_fcy":0,"vch_txn_id":301661,"txn_id":1023501,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:30:00 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:30:00 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301661, Bill Sundry ID: 6725
1 - 2026-09-15 15:30:00 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-10-15","acc_txn_dr_cr":2,"acc_txn_amt":52650,"acc_txn_fcy":0,"vch_txn_id":301661,"txn_id":1023502,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:30:02 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:30:02 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:30:02 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:30:02 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0097s
1 - 2026-09-15 15:30:02 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0023s
1 - 2026-09-15 15:30:02 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.6%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0097s   ( 70.5%)
   Q4-OtherAccounts            0.0023s   ( 16.8%)
   BuildRecords                0.0002s   (  1.6%)
   TOTAL                       0.0137s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:30:22 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35092
AND "vch_type_id" = 23
1 - 2026-09-15 15:30:22 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:30:22 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:30:22 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301662, Bill Sundry ID: 6723
1 - 2026-09-15 15:30:22 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-11-14","acc_txn_dr_cr":2,"acc_txn_amt":35100,"acc_txn_fcy":0,"vch_txn_id":301662,"txn_id":1023509,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:30:22 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:30:22 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301662, Bill Sundry ID: 6725
1 - 2026-09-15 15:30:22 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-11-14","acc_txn_dr_cr":2,"acc_txn_amt":35100,"acc_txn_fcy":0,"vch_txn_id":301662,"txn_id":1023510,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:30:23 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:30:23 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:30:23 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:30:23 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0094s
1 - 2026-09-15 15:30:23 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0025s
1 - 2026-09-15 15:30:23 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.4%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0094s   ( 68.8%)
   Q4-OtherAccounts            0.0025s   ( 18.0%)
   BuildRecords                0.0002s   (  1.4%)
   TOTAL                       0.0137s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:30:38 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35093
AND "vch_type_id" = 23
1 - 2026-09-15 15:30:38 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:30:38 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:30:38 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301663, Bill Sundry ID: 6723
1 - 2026-09-15 15:30:38 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-11-26","acc_txn_dr_cr":2,"acc_txn_amt":17550,"acc_txn_fcy":0,"vch_txn_id":301663,"txn_id":1023517,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:30:38 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:30:38 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301663, Bill Sundry ID: 6725
1 - 2026-09-15 15:30:38 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-11-26","acc_txn_dr_cr":2,"acc_txn_amt":17550,"acc_txn_fcy":0,"vch_txn_id":301663,"txn_id":1023518,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:30:39 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:30:39 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:30:39 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:30:39 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0094s
1 - 2026-09-15 15:30:39 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0026s
1 - 2026-09-15 15:30:39 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.7%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0094s   ( 67.8%)
   Q4-OtherAccounts            0.0026s   ( 19.1%)
   BuildRecords                0.0002s   (  1.6%)
   TOTAL                       0.0138s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:31:02 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35094
AND "vch_type_id" = 23
1 - 2026-09-15 15:31:02 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:31:02 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:31:02 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301664, Bill Sundry ID: 6723
1 - 2026-09-15 15:31:02 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-12-09","acc_txn_dr_cr":2,"acc_txn_amt":71406.89999999999417923390865325927734375,"acc_txn_fcy":0,"vch_txn_id":301664,"txn_id":1023529,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:31:02 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:31:02 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301664, Bill Sundry ID: 6725
1 - 2026-09-15 15:31:02 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-12-09","acc_txn_dr_cr":2,"acc_txn_amt":71406.89999999999417923390865325927734375,"acc_txn_fcy":0,"vch_txn_id":301664,"txn_id":1023530,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:31:03 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:31:03 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 15:31:03 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-15 15:31:03 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0095s
1 - 2026-09-15 15:31:03 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0023s
1 - 2026-09-15 15:31:03 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.1%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0095s   ( 69.8%)
   Q4-OtherAccounts            0.0023s   ( 16.6%)
   BuildRecords                0.0002s   (  1.8%)
   TOTAL                       0.0137s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:31:28 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35095
AND "vch_type_id" = 23
1 - 2026-09-15 15:31:28 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:31:28 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:31:28 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301665, Bill Sundry ID: 6723
1 - 2026-09-15 15:31:28 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2026-01-01","acc_txn_dr_cr":2,"acc_txn_amt":39487.5,"acc_txn_fcy":0,"vch_txn_id":301665,"txn_id":1023537,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:31:28 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:31:28 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301665, Bill Sundry ID: 6725
1 - 2026-09-15 15:31:28 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2026-01-01","acc_txn_dr_cr":2,"acc_txn_amt":39487.5,"acc_txn_fcy":0,"vch_txn_id":301665,"txn_id":1023538,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:31:28 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:31:28 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:31:28 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:31:28 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0095s
1 - 2026-09-15 15:31:28 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0023s
1 - 2026-09-15 15:31:28 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.9%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0095s   ( 70.1%)
   Q4-OtherAccounts            0.0023s   ( 16.7%)
   BuildRecords                0.0002s   (  1.5%)
   TOTAL                       0.0136s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:31:40 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:31:40 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:31:40 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:31:40 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0091s
1 - 2026-09-15 15:31:40 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0022s
1 - 2026-09-15 15:31:40 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.7%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0091s   ( 70.3%)
   Q4-OtherAccounts            0.0022s   ( 16.9%)
   BuildRecords                0.0002s   (  1.4%)
   TOTAL                       0.0130s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:33:59 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:33:59 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 15:33:59 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-15 15:33:59 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0092s
1 - 2026-09-15 15:33:59 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0024s
1 - 2026-09-15 15:33:59 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.2%)
   Q2-OpeningBalance           0.0002s   (  1.8%)
   Q3-MainLedger               0.0092s   ( 69.0%)
   Q4-OtherAccounts            0.0024s   ( 18.3%)
   BuildRecords                0.0002s   (  1.6%)
   TOTAL                       0.0134s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:34:18 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35096
AND "vch_type_id" = 23
1 - 2026-09-15 15:34:18 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:34:18 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:34:18 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301666, Bill Sundry ID: 6723
1 - 2026-09-15 15:34:18 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2026-02-03","acc_txn_dr_cr":2,"acc_txn_amt":38727,"acc_txn_fcy":0,"vch_txn_id":301666,"txn_id":1023549,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:34:18 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:34:18 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301666, Bill Sundry ID: 6725
1 - 2026-09-15 15:34:18 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2026-02-03","acc_txn_dr_cr":2,"acc_txn_amt":38727,"acc_txn_fcy":0,"vch_txn_id":301666,"txn_id":1023550,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:34:24 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:34:24 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 15:34:24 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-15 15:34:24 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0091s
1 - 2026-09-15 15:34:24 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0021s
1 - 2026-09-15 15:34:24 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.1%)
   Q2-OpeningBalance           0.0002s   (  1.9%)
   Q3-MainLedger               0.0091s   ( 71.2%)
   Q4-OtherAccounts            0.0021s   ( 16.5%)
   BuildRecords                0.0002s   (  1.3%)
   TOTAL                       0.0128s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:34:50 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35097
AND "vch_type_id" = 23
1 - 2026-09-15 15:34:50 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:34:50 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:34:50 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301667, Bill Sundry ID: 6723
1 - 2026-09-15 15:34:50 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2026-02-14","acc_txn_dr_cr":2,"acc_txn_amt":26851.5,"acc_txn_fcy":0,"vch_txn_id":301667,"txn_id":1023557,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:34:50 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:34:50 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301667, Bill Sundry ID: 6725
1 - 2026-09-15 15:34:50 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2026-02-14","acc_txn_dr_cr":2,"acc_txn_amt":26851.5,"acc_txn_fcy":0,"vch_txn_id":301667,"txn_id":1023558,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:34:51 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:34:51 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:34:51 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:34:51 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0092s
1 - 2026-09-15 15:34:51 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0023s
1 - 2026-09-15 15:34:51 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.8%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0092s   ( 68.7%)
   Q4-OtherAccounts            0.0023s   ( 17.3%)
   BuildRecords                0.0002s   (  1.7%)
   TOTAL                       0.0133s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:35:13 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35098
AND "vch_type_id" = 23
1 - 2026-09-15 15:35:13 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:35:13 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:35:13 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301668, Bill Sundry ID: 6723
1 - 2026-09-15 15:35:13 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2026-03-07","acc_txn_dr_cr":2,"acc_txn_amt":17550,"acc_txn_fcy":0,"vch_txn_id":301668,"txn_id":1023565,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:35:13 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:35:13 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301668, Bill Sundry ID: 6725
1 - 2026-09-15 15:35:13 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2026-03-07","acc_txn_dr_cr":2,"acc_txn_amt":17550,"acc_txn_fcy":0,"vch_txn_id":301668,"txn_id":1023566,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:35:14 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:35:14 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:35:14 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:35:14 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0095s
1 - 2026-09-15 15:35:14 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0022s
1 - 2026-09-15 15:35:14 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.7%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0095s   ( 70.3%)
   Q4-OtherAccounts            0.0022s   ( 16.3%)
   BuildRecords                0.0002s   (  1.6%)
   TOTAL                       0.0136s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:35:28 --> SELECT *
FROM "vchtxnconso"
WHERE "cmp_id" = '98'
AND "hobo_id" = '151'
AND "vch_series_id" = '2007'
AND "draft_vch_rec_id" = 35099
AND "vch_type_id" = 23
1 - 2026-09-15 15:35:28 --> Saving Comp id ->43 gstinType is : 2
1 - 2026-09-15 15:35:28 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:35:28 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301669, Bill Sundry ID: 6723
1 - 2026-09-15 15:35:28 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2026-03-07","acc_txn_dr_cr":2,"acc_txn_amt":8950.5,"acc_txn_fcy":0,"vch_txn_id":301669,"txn_id":1023573,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:35:28 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:35:28 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301669, Bill Sundry ID: 6725
1 - 2026-09-15 15:35:28 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2026-03-07","acc_txn_dr_cr":2,"acc_txn_amt":8950.5,"acc_txn_fcy":0,"vch_txn_id":301669,"txn_id":1023574,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:35:28 --> [LedgerCondensed][acc=6413] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:35:28 --> [LedgerCondensed][acc=6413] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 15:35:28 --> [LedgerCondensed][acc=6413] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:35:28 --> [LedgerCondensed][acc=6413] Q3-MainLedger => rows=54  total=54  |  0.0096s
1 - 2026-09-15 15:35:28 --> [LedgerCondensed][acc=6413] Q4-OtherAccounts => 54 rows  |  0.0024s
1 - 2026-09-15 15:35:28 --> [LedgerCondensed][acc=6413] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.1%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0096s   ( 69.2%)
   Q4-OtherAccounts            0.0024s   ( 17.5%)
   BuildRecords                0.0002s   (  1.3%)
   TOTAL                       0.0139s   (100%)
[LedgerCondensed][acc=6413] ── FUNCTION END ──
1 - 2026-09-15 15:35:48 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:35:48 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:36:59 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:36:59 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:36:59 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:36:59 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:37:01 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:37:01 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:37:02 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:37:02 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:37:11 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:37:11 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:38:27 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:38:27 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:38:30 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:38:30 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:39:20 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:39:20 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:39:21 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:39:21 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:39:27 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:39:27 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:39:27 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:39:27 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:39:33 --> [LedgerCondensed][acc=6723] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:39:33 --> [LedgerCondensed][acc=6723] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 15:39:33 --> [LedgerCondensed][acc=6723] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-15 15:39:33 --> [LedgerCondensed][acc=6723] Q3-MainLedger => rows=19  total=19  |  0.0126s
1 - 2026-09-15 15:39:33 --> [LedgerCondensed][acc=6723] Q4-OtherAccounts => 19 rows  |  0.0014s
1 - 2026-09-15 15:39:33 --> [LedgerCondensed][acc=6723] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.1%)
   Q2-OpeningBalance           0.0002s   (  1.5%)
   Q3-MainLedger               0.0126s   ( 79.0%)
   Q4-OtherAccounts            0.0014s   (  8.9%)
   BuildRecords                0.0001s   (  0.6%)
   TOTAL                       0.0160s   (100%)
[LedgerCondensed][acc=6723] ── FUNCTION END ──
1 - 2026-09-15 15:39:46 --> [LedgerCondensed][acc=6723] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:39:46 --> [LedgerCondensed][acc=6723] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:39:46 --> [LedgerCondensed][acc=6723] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:39:46 --> [LedgerCondensed][acc=6723] Q3-MainLedger => rows=19  total=19  |  0.0095s
1 - 2026-09-15 15:39:46 --> [LedgerCondensed][acc=6723] Q4-OtherAccounts => 19 rows  |  0.0011s
1 - 2026-09-15 15:39:46 --> [LedgerCondensed][acc=6723] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.6%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0095s   ( 77.0%)
   Q4-OtherAccounts            0.0011s   (  9.0%)
   BuildRecords                0.0001s   (  0.7%)
   TOTAL                       0.0124s   (100%)
[LedgerCondensed][acc=6723] ── FUNCTION END ──
1 - 2026-09-15 15:39:53 --> [LedgerCondensed][acc=6723] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:39:53 --> [LedgerCondensed][acc=6723] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 15:39:53 --> [LedgerCondensed][acc=6723] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:39:53 --> [LedgerCondensed][acc=6723] Q3-MainLedger => rows=19  total=19  |  0.0095s
1 - 2026-09-15 15:39:53 --> [LedgerCondensed][acc=6723] Q4-OtherAccounts => 19 rows  |  0.001s
1 - 2026-09-15 15:39:53 --> [LedgerCondensed][acc=6723] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.8%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0095s   ( 78.8%)
   Q4-OtherAccounts            0.0010s   (  8.6%)
   BuildRecords                0.0001s   (  0.7%)
   TOTAL                       0.0120s   (100%)
[LedgerCondensed][acc=6723] ── FUNCTION END ──
1 - 2026-09-15 15:40:06 --> [LedgerCondensed][acc=6723] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:40:06 --> [LedgerCondensed][acc=6723] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:40:06 --> [LedgerCondensed][acc=6723] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:40:06 --> [LedgerCondensed][acc=6723] Q3-MainLedger => rows=19  total=19  |  0.0093s
1 - 2026-09-15 15:40:06 --> [LedgerCondensed][acc=6723] Q4-OtherAccounts => 19 rows  |  0.0013s
1 - 2026-09-15 15:40:06 --> [LedgerCondensed][acc=6723] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.1%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0093s   ( 75.8%)
   Q4-OtherAccounts            0.0013s   ( 10.9%)
   BuildRecords                0.0001s   (  0.7%)
   TOTAL                       0.0123s   (100%)
[LedgerCondensed][acc=6723] ── FUNCTION END ──
1 - 2026-09-15 15:40:06 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:40:06 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:40:07 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:40:07 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:41:34 --> [LedgerCondensed][acc=6607] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:41:34 --> [LedgerCondensed][acc=6607] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:41:34 --> [LedgerCondensed][acc=6607] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:41:34 --> [LedgerCondensed][acc=6607] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-15 15:41:34 --> [LedgerCondensed][acc=6607] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-15 15:41:34 --> [LedgerCondensed][acc=6607] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 28.6%)
   Q2-OpeningBalance           0.0003s   (  8.3%)
   Q3-MainLedger               0.0016s   ( 49.1%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0033s   (100%)
[LedgerCondensed][acc=6607] ── FUNCTION END ──
1 - 2026-09-15 15:41:43 --> [LedgerCondensed][acc=6534] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:41:43 --> [LedgerCondensed][acc=6534] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:41:43 --> [LedgerCondensed][acc=6534] Q2-OpeningBalance => -14160.00  |  0.0003s
1 - 2026-09-15 15:41:43 --> [LedgerCondensed][acc=6534] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-15 15:41:43 --> [LedgerCondensed][acc=6534] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-15 15:41:43 --> [LedgerCondensed][acc=6534] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 27.9%)
   Q2-OpeningBalance           0.0003s   (  8.2%)
   Q3-MainLedger               0.0016s   ( 51.5%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0032s   (100%)
[LedgerCondensed][acc=6534] ── FUNCTION END ──
1 - 2026-09-15 15:43:04 --> [LedgerCondensed][acc=14160] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:43:04 --> [LedgerCondensed][acc=14160] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-15 15:43:04 --> [LedgerCondensed][acc=14160] Q2-OpeningBalance => -6740.00  |  0.0003s
1 - 2026-09-15 15:43:04 --> [LedgerCondensed][acc=14160] Q3-MainLedger => rows=1  total=1  |  0.0123s
1 - 2026-09-15 15:43:04 --> [LedgerCondensed][acc=14160] Q4-OtherAccounts => 1 rows  |  0.0015s
1 - 2026-09-15 15:43:04 --> [LedgerCondensed][acc=14160] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  7.2%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0123s   ( 78.4%)
   Q4-OtherAccounts            0.0015s   (  9.5%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0157s   (100%)
[LedgerCondensed][acc=14160] ── FUNCTION END ──
1 - 2026-09-15 15:45:16 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:45:16 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301670, Bill Sundry ID: 6723
1 - 2026-09-15 15:45:16 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":1350,"acc_txn_fcy":0,"vch_txn_id":301670,"txn_id":1023580,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:45:16 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:45:16 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301670, Bill Sundry ID: 6725
1 - 2026-09-15 15:45:16 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":1350,"acc_txn_fcy":0,"vch_txn_id":301670,"txn_id":1023581,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:45:23 --> [LedgerCondensed][acc=6534] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:45:23 --> [LedgerCondensed][acc=6534] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:45:23 --> [LedgerCondensed][acc=6534] Q2-OpeningBalance => -14160.00  |  0.0002s
1 - 2026-09-15 15:45:23 --> [LedgerCondensed][acc=6534] Q3-MainLedger => rows=1  total=1  |  0.0093s
1 - 2026-09-15 15:45:23 --> [LedgerCondensed][acc=6534] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-15 15:45:23 --> [LedgerCondensed][acc=6534] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.7%)
   Q2-OpeningBalance           0.0002s   (  2.1%)
   Q3-MainLedger               0.0093s   ( 78.6%)
   Q4-OtherAccounts            0.0009s   (  7.8%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0118s   (100%)
[LedgerCondensed][acc=6534] ── FUNCTION END ──
1 - 2026-09-15 15:45:49 --> 📌 items_data_val[{"account_id":"6629","account_name":"Professional Fees Taxable","amount":15000,"memo_amount":0,"amountfc":0,"description":"N/A","txinc_amount":0,"cess_basis":0,"igst_rate":18,"tax_details":{"igst":18,"cgst":"9.00","sgst":"9.00","ut_tax":"9.00","cess":"0.00"},"item_hsn_sac":"","tax_cat_id":"418","supply_type_id":3,"supply_type_name":"B2CS"}]
1 - 2026-09-15 15:45:49 --> 📌 sale_total15000====[{"account_id":"6629","account_name":"Professional Fees Taxable","amount":15000,"memo_amount":0,"amountfc":0,"description":"N\/A","txinc_amount":0,"cess_basis":0,"igst_rate":18,"tax_details":{"igst":18,"cgst":"9.00","sgst":"9.00","ut_tax":"9.00","cess":"0.00"},"item_hsn_sac":"","tax_cat_id":"418","supply_type_id":3,"supply_type_name":"B2CS"}]
1 - 2026-09-15 15:45:49 --> 📌 add_comp_txn_data{"cmp_id":"98","vch_series_id":"2018","vch_txn_id":"301671","master_id":"6534","master_id_type":"acc"}
1 - 2026-09-15 15:45:49 --> 📌 add_acc_txn_data{"cmp_id":"98","acc_id":"6534","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":15000,"acc_txn_fcy":0,"vch_txn_id":"301671","txn_id":1023585,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:45:49 --> 📌memo_insert_data add_acc_txn_data{"cmp_id":"98","acc_id":"6534","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":0,"acc_txn_fcy":0,"vch_txn_id":"301671","txn_id":1023585,"hobo_id":"151","acc_txn_type":3}
1 - 2026-09-15 15:45:49 --> 📌add_register_txn_data add_acc_txn_data{"acct_vch_type":11,"cmp_id":"98","vch_txn_id":"301671","txn_id":null,"vch_date":"2025-08-01","acc_id":"6534","acc_txn_dr_amt":15000,"acc_txn_cr_amt":0,"vch_narr":"N\/A","hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:45:49 --> 📌add_comp_txn_data{"cmp_id":"98","vch_series_id":"2018","vch_txn_id":"301671","master_id":"6629","master_id_type":"acc"}
1 - 2026-09-15 15:45:49 --> 📌tax_cat_id  tax_details418---{"igst":18,"cgst":"9.00","sgst":"9.00","ut_tax":"9.00","cess":"0.00"}
1 - 2026-09-15 15:45:49 --> 📌add_taxsummary_data{"cmp_id":"98","vch_txn_id":"301671","txn_id":1023586,"acc_bsd_id":"6629","acc_bsd_type":1,"vch_igst":0,"vch_igst_rate":0,"vch_cgst":1350,"vch_cgst_rate":9,"vch_sgst_ugst":1350,"vch_sgst_ugst_rate":9,"vch_cess":0,"vch_cess_rate":0,"vch_taxable_value":15000,"vch_total_tax":2700}
1 - 2026-09-15 15:45:49 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:45:49 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301670, Bill Sundry ID: 6723
1 - 2026-09-15 15:45:49 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":1350,"acc_txn_fcy":0,"vch_txn_id":"301670","txn_id":1023587,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:45:49 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:45:49 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301670, Bill Sundry ID: 6725
1 - 2026-09-15 15:45:49 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":1350,"acc_txn_fcy":0,"vch_txn_id":"301670","txn_id":1023588,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:45:50 --> [LedgerCondensed][acc=6534] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:45:50 --> [LedgerCondensed][acc=6534] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 15:45:50 --> [LedgerCondensed][acc=6534] Q2-OpeningBalance => -14160.00  |  0.0003s
1 - 2026-09-15 15:45:50 --> [LedgerCondensed][acc=6534] Q3-MainLedger => rows=1  total=1  |  0.0096s
1 - 2026-09-15 15:45:50 --> [LedgerCondensed][acc=6534] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-15 15:45:50 --> [LedgerCondensed][acc=6534] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.2%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0096s   ( 79.2%)
   Q4-OtherAccounts            0.0009s   (  7.4%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0121s   (100%)
[LedgerCondensed][acc=6534] ── FUNCTION END ──
1 - 2026-09-15 15:46:52 --> 📌 items_data_val[{"account_id":"6628","account_name":"Professional Fees","amount":15000,"memo_amount":0,"amountfc":0,"description":"N/A","txinc_amount":0,"cess_basis":1,"igst_rate":18,"tax_details":{"igst":"18.00","sgst":"9.00","cgst":"9.00","ut_tax":"0","cess":"0"},"item_hsn_sac":"","tax_cat_id":"418","supply_type_id":3,"supply_type_name":"B2CS"}]
1 - 2026-09-15 15:46:52 --> 📌 sale_total15000====[{"account_id":"6628","account_name":"Professional Fees","amount":15000,"memo_amount":0,"amountfc":0,"description":"N\/A","txinc_amount":0,"cess_basis":1,"igst_rate":18,"tax_details":{"igst":"18.00","sgst":"9.00","cgst":"9.00","ut_tax":"0","cess":"0"},"item_hsn_sac":"","tax_cat_id":"418","supply_type_id":3,"supply_type_name":"B2CS"}]
1 - 2026-09-15 15:46:52 --> 📌 add_comp_txn_data{"cmp_id":"98","vch_series_id":"2018","vch_txn_id":"301671","master_id":"6534","master_id_type":"acc"}
1 - 2026-09-15 15:46:52 --> 📌 add_acc_txn_data{"cmp_id":"98","acc_id":"6534","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":15000,"acc_txn_fcy":0,"vch_txn_id":"301671","txn_id":1023592,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:46:52 --> 📌memo_insert_data add_acc_txn_data{"cmp_id":"98","acc_id":"6534","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":0,"acc_txn_fcy":0,"vch_txn_id":"301671","txn_id":1023592,"hobo_id":"151","acc_txn_type":3}
1 - 2026-09-15 15:46:52 --> 📌add_register_txn_data add_acc_txn_data{"acct_vch_type":11,"cmp_id":"98","vch_txn_id":"301671","txn_id":null,"vch_date":"2025-08-01","acc_id":"6534","acc_txn_dr_amt":15000,"acc_txn_cr_amt":0,"vch_narr":"N\/A","hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:46:52 --> 📌add_comp_txn_data{"cmp_id":"98","vch_series_id":"2018","vch_txn_id":"301671","master_id":"6628","master_id_type":"acc"}
1 - 2026-09-15 15:46:52 --> 📌tax_cat_id  tax_details418---{"igst":"18.00","sgst":"9.00","cgst":"9.00","ut_tax":"0","cess":"0"}
1 - 2026-09-15 15:46:52 --> 📌add_taxsummary_data{"cmp_id":"98","vch_txn_id":"301671","txn_id":1023593,"acc_bsd_id":"6628","acc_bsd_type":1,"vch_igst":0,"vch_igst_rate":0,"vch_cgst":1350,"vch_cgst_rate":9,"vch_sgst_ugst":1350,"vch_sgst_ugst_rate":9,"vch_cess":0,"vch_cess_rate":0,"vch_taxable_value":15000,"vch_total_tax":2700}
1 - 2026-09-15 15:46:52 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:46:52 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301670, Bill Sundry ID: 6723
1 - 2026-09-15 15:46:52 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":1350,"acc_txn_fcy":0,"vch_txn_id":"301670","txn_id":1023594,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:46:52 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:46:52 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301670, Bill Sundry ID: 6725
1 - 2026-09-15 15:46:52 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":1350,"acc_txn_fcy":0,"vch_txn_id":"301670","txn_id":1023595,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:46:52 --> [LedgerCondensed][acc=6534] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:46:52 --> [LedgerCondensed][acc=6534] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 15:46:52 --> [LedgerCondensed][acc=6534] Q2-OpeningBalance => -14160.00  |  0.0002s
1 - 2026-09-15 15:46:52 --> [LedgerCondensed][acc=6534] Q3-MainLedger => rows=1  total=1  |  0.0093s
1 - 2026-09-15 15:46:52 --> [LedgerCondensed][acc=6534] Q4-OtherAccounts => 1 rows  |  0.0011s
1 - 2026-09-15 15:46:52 --> [LedgerCondensed][acc=6534] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.6%)
   Q2-OpeningBalance           0.0002s   (  2.1%)
   Q3-MainLedger               0.0093s   ( 78.8%)
   Q4-OtherAccounts            0.0011s   (  8.9%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0118s   (100%)
[LedgerCondensed][acc=6534] ── FUNCTION END ──
1 - 2026-09-15 15:46:59 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:46:59 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:47:18 --> 📌 items_data_val[{"account_id":"6628","account_name":"Professional Fees","amount":15000,"memo_amount":0,"amountfc":0,"description":"N/A","txinc_amount":0,"cess_basis":0,"igst_rate":18,"tax_details":{"igst":18,"cgst":"9.00","sgst":"9.00","ut_tax":"9.00","cess":"0.00"},"item_hsn_sac":"","tax_cat_id":"418","supply_type_id":3,"supply_type_name":"B2CS"}]
1 - 2026-09-15 15:47:18 --> 📌 sale_total15000====[{"account_id":"6628","account_name":"Professional Fees","amount":15000,"memo_amount":0,"amountfc":0,"description":"N\/A","txinc_amount":0,"cess_basis":0,"igst_rate":18,"tax_details":{"igst":18,"cgst":"9.00","sgst":"9.00","ut_tax":"9.00","cess":"0.00"},"item_hsn_sac":"","tax_cat_id":"418","supply_type_id":3,"supply_type_name":"B2CS"}]
1 - 2026-09-15 15:47:18 --> 📌 add_comp_txn_data{"cmp_id":"98","vch_series_id":"2018","vch_txn_id":"301671","master_id":"6534","master_id_type":"acc"}
1 - 2026-09-15 15:47:18 --> 📌 add_acc_txn_data{"cmp_id":"98","acc_id":"6534","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":15000,"acc_txn_fcy":0,"vch_txn_id":"301671","txn_id":1023599,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:47:18 --> 📌memo_insert_data add_acc_txn_data{"cmp_id":"98","acc_id":"6534","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":0,"acc_txn_fcy":0,"vch_txn_id":"301671","txn_id":1023599,"hobo_id":"151","acc_txn_type":3}
1 - 2026-09-15 15:47:18 --> 📌add_register_txn_data add_acc_txn_data{"acct_vch_type":11,"cmp_id":"98","vch_txn_id":"301671","txn_id":null,"vch_date":"2025-08-01","acc_id":"6534","acc_txn_dr_amt":15000,"acc_txn_cr_amt":0,"vch_narr":"N\/A","hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:47:18 --> 📌add_comp_txn_data{"cmp_id":"98","vch_series_id":"2018","vch_txn_id":"301671","master_id":"6628","master_id_type":"acc"}
1 - 2026-09-15 15:47:18 --> 📌tax_cat_id  tax_details418---{"igst":18,"cgst":"9.00","sgst":"9.00","ut_tax":"9.00","cess":"0.00"}
1 - 2026-09-15 15:47:18 --> 📌add_taxsummary_data{"cmp_id":"98","vch_txn_id":"301671","txn_id":1023600,"acc_bsd_id":"6628","acc_bsd_type":1,"vch_igst":0,"vch_igst_rate":0,"vch_cgst":1350,"vch_cgst_rate":9,"vch_sgst_ugst":1350,"vch_sgst_ugst_rate":9,"vch_cess":0,"vch_cess_rate":0,"vch_taxable_value":15000,"vch_total_tax":2700}
1 - 2026-09-15 15:47:18 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:47:18 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301670, Bill Sundry ID: 6723
1 - 2026-09-15 15:47:18 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":1350,"acc_txn_fcy":0,"vch_txn_id":"301670","txn_id":1023601,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:47:18 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:47:18 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301670, Bill Sundry ID: 6725
1 - 2026-09-15 15:47:18 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":1350,"acc_txn_fcy":0,"vch_txn_id":"301670","txn_id":1023602,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:47:18 --> [LedgerCondensed][acc=6534] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:47:18 --> [LedgerCondensed][acc=6534] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:47:18 --> [LedgerCondensed][acc=6534] Q2-OpeningBalance => -14160.00  |  0.0003s
1 - 2026-09-15 15:47:18 --> [LedgerCondensed][acc=6534] Q3-MainLedger => rows=1  total=1  |  0.0091s
1 - 2026-09-15 15:47:18 --> [LedgerCondensed][acc=6534] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-15 15:47:18 --> [LedgerCondensed][acc=6534] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  8.0%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0091s   ( 78.4%)
   Q4-OtherAccounts            0.0009s   (  7.7%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0115s   (100%)
[LedgerCondensed][acc=6534] ── FUNCTION END ──
1 - 2026-09-15 15:47:35 --> 📌 items_data_val[{"account_id":"6628","account_name":"Professional Fees","amount":15000,"memo_amount":0,"amountfc":0,"description":"N/A","txinc_amount":0,"cess_basis":0,"igst_rate":18,"tax_details":{"igst":18,"cgst":"9.00","sgst":"9.00","ut_tax":"9.00","cess":"0.00"},"item_hsn_sac":"","tax_cat_id":"418","supply_type_id":3,"supply_type_name":"B2CS"}]
1 - 2026-09-15 15:47:35 --> 📌 sale_total15000====[{"account_id":"6628","account_name":"Professional Fees","amount":15000,"memo_amount":0,"amountfc":0,"description":"N\/A","txinc_amount":0,"cess_basis":0,"igst_rate":18,"tax_details":{"igst":18,"cgst":"9.00","sgst":"9.00","ut_tax":"9.00","cess":"0.00"},"item_hsn_sac":"","tax_cat_id":"418","supply_type_id":3,"supply_type_name":"B2CS"}]
1 - 2026-09-15 15:47:35 --> 📌 add_comp_txn_data{"cmp_id":"98","vch_series_id":"2018","vch_txn_id":"301671","master_id":"6534","master_id_type":"acc"}
1 - 2026-09-15 15:47:35 --> 📌 add_acc_txn_data{"cmp_id":"98","acc_id":"6534","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":15000,"acc_txn_fcy":0,"vch_txn_id":"301671","txn_id":1023606,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:47:35 --> 📌memo_insert_data add_acc_txn_data{"cmp_id":"98","acc_id":"6534","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":0,"acc_txn_fcy":0,"vch_txn_id":"301671","txn_id":1023606,"hobo_id":"151","acc_txn_type":3}
1 - 2026-09-15 15:47:35 --> 📌add_register_txn_data add_acc_txn_data{"acct_vch_type":11,"cmp_id":"98","vch_txn_id":"301671","txn_id":null,"vch_date":"2025-08-01","acc_id":"6534","acc_txn_dr_amt":15000,"acc_txn_cr_amt":0,"vch_narr":"N\/A","hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:47:35 --> 📌add_comp_txn_data{"cmp_id":"98","vch_series_id":"2018","vch_txn_id":"301671","master_id":"6628","master_id_type":"acc"}
1 - 2026-09-15 15:47:35 --> 📌tax_cat_id  tax_details418---{"igst":18,"cgst":"9.00","sgst":"9.00","ut_tax":"9.00","cess":"0.00"}
1 - 2026-09-15 15:47:35 --> 📌add_taxsummary_data{"cmp_id":"98","vch_txn_id":"301671","txn_id":1023607,"acc_bsd_id":"6628","acc_bsd_type":1,"vch_igst":0,"vch_igst_rate":0,"vch_cgst":1350,"vch_cgst_rate":9,"vch_sgst_ugst":1350,"vch_sgst_ugst_rate":9,"vch_cess":0,"vch_cess_rate":0,"vch_taxable_value":15000,"vch_total_tax":2700}
1 - 2026-09-15 15:47:35 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:47:35 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301670, Bill Sundry ID: 6723
1 - 2026-09-15 15:47:35 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6723","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":1350,"acc_txn_fcy":0,"vch_txn_id":"301670","txn_id":1023608,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:47:35 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 1
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '98'
1 - 2026-09-15 15:47:35 --> Company ID: 98, Voucher Txn ID (Tax Applied): 301670, Bill Sundry ID: 6725
1 - 2026-09-15 15:47:35 --> Saving tax account entry: {"cmp_id":"98","acc_id":"6725","acc_txn_date":"2025-08-01","acc_txn_dr_cr":2,"acc_txn_amt":1350,"acc_txn_fcy":0,"vch_txn_id":"301670","txn_id":1023609,"hobo_id":"151","acc_txn_type":1}
1 - 2026-09-15 15:47:36 --> [LedgerCondensed][acc=6534] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:47:36 --> [LedgerCondensed][acc=6534] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 15:47:36 --> [LedgerCondensed][acc=6534] Q2-OpeningBalance => -14160.00  |  0.0002s
1 - 2026-09-15 15:47:36 --> [LedgerCondensed][acc=6534] Q3-MainLedger => rows=1  total=1  |  0.0092s
1 - 2026-09-15 15:47:36 --> [LedgerCondensed][acc=6534] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-15 15:47:36 --> [LedgerCondensed][acc=6534] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  7.1%)
   Q2-OpeningBalance           0.0002s   (  2.2%)
   Q3-MainLedger               0.0092s   ( 79.9%)
   Q4-OtherAccounts            0.0009s   (  7.4%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0116s   (100%)
[LedgerCondensed][acc=6534] ── FUNCTION END ──
1 - 2026-09-15 15:47:47 --> [LedgerCondensed][acc=14160] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:47:47 --> [LedgerCondensed][acc=14160] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:47:47 --> [LedgerCondensed][acc=14160] Q2-OpeningBalance => -6740.00  |  0.0003s
1 - 2026-09-15 15:47:47 --> [LedgerCondensed][acc=14160] Q3-MainLedger => rows=1  total=1  |  0.0096s
1 - 2026-09-15 15:47:47 --> [LedgerCondensed][acc=14160] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-15 15:47:47 --> [LedgerCondensed][acc=14160] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.4%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0096s   ( 79.9%)
   Q4-OtherAccounts            0.0009s   (  7.2%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0120s   (100%)
[LedgerCondensed][acc=14160] ── FUNCTION END ──
1 - 2026-09-15 15:48:07 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:48:07 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:48:07 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 15:48:07 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=100  total=1163  |  0.0143s
1 - 2026-09-15 15:48:07 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 100 rows  |  0.0022s
1 - 2026-09-15 15:48:07 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  4.7%)
   Q2-OpeningBalance           0.0002s   (  1.3%)
   Q3-MainLedger               0.0143s   ( 77.7%)
   Q4-OtherAccounts            0.0022s   ( 12.0%)
   BuildRecords                0.0003s   (  1.8%)
   TOTAL                       0.0184s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 15:48:19 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-08-01  to=2025-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:48:19 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 317  |  0.0023s
1 - 2026-09-15 15:48:19 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 668441.5  |  0.0021s
1 - 2026-09-15 15:48:19 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=95  total=95  |  0.0092s
1 - 2026-09-15 15:48:19 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 95 rows  |  0.0021s
1 - 2026-09-15 15:48:19 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0023s   ( 13.8%)
   Q2-OpeningBalance           0.0021s   ( 12.8%)
   Q3-MainLedger               0.0092s   ( 56.2%)
   Q4-OtherAccounts            0.0021s   ( 13.0%)
   BuildRecords                0.0003s   (  1.6%)
   TOTAL                       0.0164s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 15:49:29 --> [LedgerCondensed][acc=6534] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:49:29 --> [LedgerCondensed][acc=6534] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 15:49:29 --> [LedgerCondensed][acc=6534] Q2-OpeningBalance => -14160.00  |  0.0002s
1 - 2026-09-15 15:49:29 --> [LedgerCondensed][acc=6534] Q3-MainLedger => rows=2  total=2  |  0.0091s
1 - 2026-09-15 15:49:29 --> [LedgerCondensed][acc=6534] Q4-OtherAccounts => 2 rows  |  0.001s
1 - 2026-09-15 15:49:29 --> [LedgerCondensed][acc=6534] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.8%)
   Q2-OpeningBalance           0.0002s   (  2.1%)
   Q3-MainLedger               0.0091s   ( 78.5%)
   Q4-OtherAccounts            0.0010s   (  8.9%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0116s   (100%)
[LedgerCondensed][acc=6534] ── FUNCTION END ──
1 - 2026-09-15 15:49:58 --> [LedgerCondensed][acc=6633] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:49:58 --> [LedgerCondensed][acc=6633] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 15:49:58 --> [LedgerCondensed][acc=6633] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:49:58 --> [LedgerCondensed][acc=6633] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-15 15:49:58 --> [LedgerCondensed][acc=6633] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-15 15:49:58 --> [LedgerCondensed][acc=6633] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 30.6%)
   Q2-OpeningBalance           0.0003s   (  8.5%)
   Q3-MainLedger               0.0016s   ( 49.8%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0033s   (100%)
[LedgerCondensed][acc=6633] ── FUNCTION END ──
1 - 2026-09-15 15:50:19 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:50:19 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:52:08 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:52:08 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:52:14 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:52:14 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:52:14 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-15 15:52:14 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=100  total=1163  |  0.013s
1 - 2026-09-15 15:52:14 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 100 rows  |  0.0023s
1 - 2026-09-15 15:52:14 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.4%)
   Q2-OpeningBalance           0.0003s   (  1.6%)
   Q3-MainLedger               0.0130s   ( 75.8%)
   Q4-OtherAccounts            0.0023s   ( 13.2%)
   BuildRecords                0.0003s   (  1.7%)
   TOTAL                       0.0172s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 15:52:39 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:52:39 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:52:39 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 15:52:39 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=100  total=1163  |  0.0123s
1 - 2026-09-15 15:52:39 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 100 rows  |  0.0021s
1 - 2026-09-15 15:52:39 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.7%)
   Q2-OpeningBalance           0.0002s   (  1.5%)
   Q3-MainLedger               0.0123s   ( 75.3%)
   Q4-OtherAccounts            0.0021s   ( 13.1%)
   BuildRecords                0.0003s   (  1.7%)
   TOTAL                       0.0163s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 15:52:43 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:52:43 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 15:52:43 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 15:52:43 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=100  total=1163  |  0.0124s
1 - 2026-09-15 15:52:43 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 100 rows  |  0.002s
1 - 2026-09-15 15:52:43 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.9%)
   Q2-OpeningBalance           0.0002s   (  1.5%)
   Q3-MainLedger               0.0124s   ( 76.6%)
   Q4-OtherAccounts            0.0020s   ( 12.6%)
   BuildRecords                0.0002s   (  1.3%)
   TOTAL                       0.0161s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 15:52:50 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:52:50 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:52:50 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-15 15:52:50 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=100  total=1163  |  0.0127s
1 - 2026-09-15 15:52:50 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 100 rows  |  0.0022s
1 - 2026-09-15 15:52:50 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.2%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0127s   ( 76.0%)
   Q4-OtherAccounts            0.0022s   ( 13.3%)
   BuildRecords                0.0003s   (  1.6%)
   TOTAL                       0.0167s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 15:52:53 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:52:53 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 15:52:53 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 15:52:53 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=100  total=1163  |  0.0124s
1 - 2026-09-15 15:52:53 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 100 rows  |  0.0021s
1 - 2026-09-15 15:52:53 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  5.0%)
   Q2-OpeningBalance           0.0002s   (  1.5%)
   Q3-MainLedger               0.0124s   ( 77.0%)
   Q4-OtherAccounts            0.0021s   ( 12.9%)
   BuildRecords                0.0002s   (  1.4%)
   TOTAL                       0.0160s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 15:53:01 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:53:01 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:53:04 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:53:04 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 15:53:04 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 15:53:04 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=100  total=1163  |  0.0123s
1 - 2026-09-15 15:53:04 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 100 rows  |  0.0021s
1 - 2026-09-15 15:53:04 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  4.9%)
   Q2-OpeningBalance           0.0002s   (  1.5%)
   Q3-MainLedger               0.0123s   ( 76.2%)
   Q4-OtherAccounts            0.0021s   ( 13.0%)
   BuildRecords                0.0003s   (  1.7%)
   TOTAL                       0.0161s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 15:53:38 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:53:38 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 15:53:38 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-15 15:53:38 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=100  total=1163  |  0.0126s
1 - 2026-09-15 15:53:38 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 100 rows  |  0.0022s
1 - 2026-09-15 15:53:38 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.7%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0126s   ( 75.1%)
   Q4-OtherAccounts            0.0022s   ( 13.3%)
   BuildRecords                0.0003s   (  1.6%)
   TOTAL                       0.0168s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 15:53:42 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:53:42 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:53:56 --> [LedgerCondensed][acc=6580] ── FUNCTION START ── from=2025-05-01  to=2025-05-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:53:56 --> [LedgerCondensed][acc=6580] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-15 15:53:56 --> [LedgerCondensed][acc=6580] Q2-OpeningBalance => -3168.75  |  0.0012s
1 - 2026-09-15 15:53:56 --> [LedgerCondensed][acc=6580] Q3-MainLedger => rows=2  total=2  |  0.0091s
1 - 2026-09-15 15:53:56 --> [LedgerCondensed][acc=6580] Q4-OtherAccounts => 2 rows  |  0.001s
1 - 2026-09-15 15:53:56 --> [LedgerCondensed][acc=6580] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  8.6%)
   Q2-OpeningBalance           0.0012s   (  9.5%)
   Q3-MainLedger               0.0091s   ( 70.8%)
   Q4-OtherAccounts            0.0010s   (  8.0%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0129s   (100%)
[LedgerCondensed][acc=6580] ── FUNCTION END ──
1 - 2026-09-15 15:54:01 --> [LedgerCondensed][acc=6580] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:54:01 --> [LedgerCondensed][acc=6580] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:54:01 --> [LedgerCondensed][acc=6580] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 15:54:01 --> [LedgerCondensed][acc=6580] Q3-MainLedger => rows=100  total=228  |  0.0097s
1 - 2026-09-15 15:54:01 --> [LedgerCondensed][acc=6580] Q4-OtherAccounts => 100 rows  |  0.0028s
1 - 2026-09-15 15:54:01 --> [LedgerCondensed][acc=6580] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.0%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0097s   ( 67.0%)
   Q4-OtherAccounts            0.0028s   ( 19.2%)
   BuildRecords                0.0005s   (  3.3%)
   TOTAL                       0.0145s   (100%)
[LedgerCondensed][acc=6580] ── FUNCTION END ──
1 - 2026-09-15 15:54:06 --> [LedgerCondensed][acc=6580] ── FUNCTION START ── from=2025-05-01  to=2025-05-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 15:54:06 --> [LedgerCondensed][acc=6580] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 15:54:06 --> [LedgerCondensed][acc=6580] Q2-OpeningBalance => -3168.75  |  0.0012s
1 - 2026-09-15 15:54:06 --> [LedgerCondensed][acc=6580] Q3-MainLedger => rows=2  total=2  |  0.009s
1 - 2026-09-15 15:54:06 --> [LedgerCondensed][acc=6580] Q4-OtherAccounts => 2 rows  |  0.001s
1 - 2026-09-15 15:54:06 --> [LedgerCondensed][acc=6580] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.4%)
   Q2-OpeningBalance           0.0012s   (  9.5%)
   Q3-MainLedger               0.0090s   ( 71.7%)
   Q4-OtherAccounts            0.0010s   (  8.3%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0125s   (100%)
[LedgerCondensed][acc=6580] ── FUNCTION END ──
1 - 2026-09-15 15:54:11 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 15:54:11 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:02:04 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:02:04 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:02:07 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:02:07 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:02:07 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 16:02:07 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=100  total=1163  |  0.0159s
1 - 2026-09-15 16:02:07 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 100 rows  |  0.0026s
1 - 2026-09-15 16:02:07 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  4.8%)
   Q2-OpeningBalance           0.0002s   (  1.2%)
   Q3-MainLedger               0.0159s   ( 77.9%)
   Q4-OtherAccounts            0.0026s   ( 12.5%)
   BuildRecords                0.0003s   (  1.5%)
   TOTAL                       0.0205s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 16:02:14 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=1  compId=  boId=  fyId=
1 - 2026-09-15 16:02:14 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 16:02:14 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0004s
1 - 2026-09-15 16:02:14 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=1163  total=1163  |  0.0134s
1 - 2026-09-15 16:02:14 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 1163 rows  |  0.0501s
1 - 2026-09-15 16:02:14 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  1.3%)
   Q2-OpeningBalance           0.0004s   (  0.5%)
   Q3-MainLedger               0.0134s   ( 19.6%)
   Q4-OtherAccounts            0.0501s   ( 73.6%)
   BuildRecords                0.0028s   (  4.1%)
   TOTAL                       0.0681s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 16:02:40 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:02:40 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:02:42 --> [LedgerCondensed][acc=6411] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:02:42 --> [LedgerCondensed][acc=6411] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:02:42 --> [LedgerCondensed][acc=6411] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 16:02:42 --> [LedgerCondensed][acc=6411] Q3-MainLedger => rows=100  total=1132  |  0.0129s
1 - 2026-09-15 16:02:42 --> [LedgerCondensed][acc=6411] Q4-OtherAccounts => 100 rows  |  0.0025s
1 - 2026-09-15 16:02:42 --> [LedgerCondensed][acc=6411] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.7%)
   Q2-OpeningBalance           0.0002s   (  1.4%)
   Q3-MainLedger               0.0129s   ( 74.2%)
   Q4-OtherAccounts            0.0025s   ( 14.4%)
   BuildRecords                0.0004s   (  2.1%)
   TOTAL                       0.0174s   (100%)
[LedgerCondensed][acc=6411] ── FUNCTION END ──
1 - 2026-09-15 16:03:33 --> [LedgerCondensed][acc=6411] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:03:33 --> [LedgerCondensed][acc=6411] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:03:33 --> [LedgerCondensed][acc=6411] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-15 16:03:33 --> [LedgerCondensed][acc=6411] Q3-MainLedger => rows=100  total=1132  |  0.0126s
1 - 2026-09-15 16:03:33 --> [LedgerCondensed][acc=6411] Q4-OtherAccounts => 100 rows  |  0.0024s
1 - 2026-09-15 16:03:33 --> [LedgerCondensed][acc=6411] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.6%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0126s   ( 73.7%)
   Q4-OtherAccounts            0.0024s   ( 14.0%)
   BuildRecords                0.0004s   (  2.1%)
   TOTAL                       0.0171s   (100%)
[LedgerCondensed][acc=6411] ── FUNCTION END ──
1 - 2026-09-15 16:03:47 --> [LedgerCondensed][acc=6411] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:03:47 --> [LedgerCondensed][acc=6411] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-15 16:03:47 --> [LedgerCondensed][acc=6411] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-15 16:03:47 --> [LedgerCondensed][acc=6411] Q3-MainLedger => rows=100  total=1132  |  0.0123s
1 - 2026-09-15 16:03:47 --> [LedgerCondensed][acc=6411] Q4-OtherAccounts => 100 rows  |  0.0024s
1 - 2026-09-15 16:03:47 --> [LedgerCondensed][acc=6411] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  6.5%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0123s   ( 72.8%)
   Q4-OtherAccounts            0.0024s   ( 14.1%)
   BuildRecords                0.0004s   (  2.3%)
   TOTAL                       0.0169s   (100%)
[LedgerCondensed][acc=6411] ── FUNCTION END ──
1 - 2026-09-15 16:04:13 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:04:13 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:04:13 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:04:13 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:04:17 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:04:17 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:04:17 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:04:17 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:04:27 --> [LedgerCondensed][acc=6611] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:04:27 --> [LedgerCondensed][acc=6611] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:04:27 --> [LedgerCondensed][acc=6611] Q2-OpeningBalance => -10189.00  |  0.0003s
1 - 2026-09-15 16:04:27 --> [LedgerCondensed][acc=6611] Q3-MainLedger => rows=0  total=0  |  0.0015s
1 - 2026-09-15 16:04:27 --> [LedgerCondensed][acc=6611] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-15 16:04:27 --> [LedgerCondensed][acc=6611] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   ( 31.5%)
   Q2-OpeningBalance           0.0003s   (  8.2%)
   Q3-MainLedger               0.0015s   ( 49.2%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0031s   (100%)
[LedgerCondensed][acc=6611] ── FUNCTION END ──
1 - 2026-09-15 16:04:29 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:04:29 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:04:29 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:04:29 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:08:56 --> [LedgerCondensed][acc=7844] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:08:56 --> [LedgerCondensed][acc=7844] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-15 16:08:56 --> [LedgerCondensed][acc=7844] Q2-OpeningBalance => -83048.00  |  0.0003s
1 - 2026-09-15 16:08:56 --> [LedgerCondensed][acc=7844] Q3-MainLedger => rows=17  total=17  |  0.0126s
1 - 2026-09-15 16:08:56 --> [LedgerCondensed][acc=7844] Q4-OtherAccounts => 17 rows  |  0.0027s
1 - 2026-09-15 16:08:56 --> [LedgerCondensed][acc=7844] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  6.6%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0126s   ( 73.4%)
   Q4-OtherAccounts            0.0027s   ( 15.8%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0172s   (100%)
[LedgerCondensed][acc=7844] ── FUNCTION END ──
1 - 2026-09-15 16:09:52 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 185
AND "cmp_id" = 126
AND "itm_id_unit_id" IN ('19194_1540','19194_8','19195_8','19196_8','19197_8','19198_1547','19198_8','19199_8','19200_10','19200_1542','19200_17','19200_8','19201_1540','19201_1542','19201_1549','19202_3','19202_8','19203_8','19204_10','19204_1542','19204_1549','19204_17','19204_8','19205_10','19205_1540','19205_1542','19205_1549','19205_17','19205_8','19206_1535','19206_1542','19206_3','19206_8','19207_10','19207_1542','19207_8','19209_17','19209_8','19210_10','19210_1542','19210_8','19211_3','19211_8','19212_1535','19212_3','19212_6','19212_8','19213_6','19213_8','19214_3','19214_8','19215_1535','19215_3','19215_8','19216_1540','19216_8','19217_3','19217_8','19218_6','19218_8','19219_8','19220_8','19221_8','19222_8','19223_1540','19224_8','19225_1540','19225_8','19226_1540','19226_1549','19226_17','19226_1825','19226_8','19227_8','19228_8','19229_1540','19229_8','19230_1540','19230_8','19231_1540','19231_1550','19231_18','19231_8','19232_8','19233_8','19234_8','19235_8','19236_1535','19236_1542','19236_3','19236_8','19237_8','19238_1535','19238_3','19238_8','19240_1535','19240_3','19240_8','19241_8','19242_1535','19242_4','19242_8','19243_1535','19243_3','19243_8','19245_1538','19245_1540','19245_6','19245_8','19246_1535','19246_3','19246_8','19247_8','19248_3','19248_8','19249_8','19250_15','19250_8','19251_1535','19251_3','19251_8','19252_8','19253_6','19253_8','19254_8','19255_6','19255_8','19256_8','19257_10','19257_8','19258_8','19260_1550','19260_18','19260_6','19260_8','19261_15','19261_8','19262_8','19263_8','19264_8','19265_1550','19265_18','19265_8','19266_8','19267_8','19268_3','19268_8','19269_1550','19269_18','19269_8','19270_5','19270_8','19272_8','19273_5','19273_8','19274_10','19274_1535','19274_1542','19274_3','19274_4','19274_8','19275_5','19275_8','19276_8','19277_8','19278_17','19278_8','19279_5','19279_8','19280_8','19281_8','19282_6','19282_8','19283_8','19284_1550','19284_18','19284_8','19285_8','19286_8','19287_8','19288_8','19289_8','19290_8','19291_8','19292_8','19293_8','19294_8','19295_1535','19295_17','19295_3','19295_8','19296_1535','19296_3','19296_8','19297_1535','19297_3','19297_8','19298_5','19298_8','19299_15','19299_1540','19299_1547','19299_8','19300_1540','19300_8','19301_8','19302_8','19303_1535','19303_1540','19303_3','19303_8','19304_1540','19304_8','19305_1538','19305_1550','19305_18','19305_6','19306_8','19307_8','19308_1540','19308_8','19309_3','19310_1549','19310_17','19311_8','19312_8','19313_18','19314_5','19315_1535','19315_3','19316_10','19316_1542','19317_1543','20056_1825','40038_1540')
AND "hobo_id" = 167
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:09:52 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 185
AND "cmp_id" = 126
AND "itm_id_unit_id" IN ('19194_1540','19194_8','19195_8','19196_8','19197_8','19198_1547','19198_8','19199_8','19200_10','19200_1542','19200_17','19200_8','19201_1540','19201_1542','19201_1549','19202_3','19202_8','19203_8','19204_10','19204_1542','19204_1549','19204_17','19204_8','19205_10','19205_1540','19205_1542','19205_1549','19205_17','19205_8','19206_1535','19206_1542','19206_3','19206_8','19207_10','19207_1542','19207_8','19209_17','19209_8','19210_10','19210_1542','19210_8','19211_3','19211_8','19212_1535','19212_3','19212_6','19212_8','19213_6','19213_8','19214_3','19214_8','19215_1535','19215_3','19215_8','19216_1540','19216_8','19217_3','19217_8','19218_6','19218_8','19219_8','19220_8','19221_8','19222_8','19223_1540','19224_8','19225_1540','19225_8','19226_1540','19226_1549','19226_17','19226_1825','19226_8','19227_8','19228_8','19229_1540','19229_8','19230_1540','19230_8','19231_1540','19231_1550','19231_18','19231_8','19232_8','19233_8','19234_8','19235_8','19236_1535','19236_1542','19236_3','19236_8','19237_8','19238_1535','19238_3','19238_8','19240_1535','19240_3','19240_8','19241_8','19242_1535','19242_4','19242_8','19243_1535','19243_3','19243_8','19245_1538','19245_1540','19245_6','19245_8','19246_1535','19246_3','19246_8','19247_8','19248_3','19248_8','19249_8','19250_15','19250_8','19251_1535','19251_3','19251_8','19252_8','19253_6','19253_8','19254_8','19255_6','19255_8','19256_8','19257_10','19257_8','19258_8','19260_1550','19260_18','19260_6','19260_8','19261_15','19261_8','19262_8','19263_8','19264_8','19265_1550','19265_18','19265_8','19266_8','19267_8','19268_3','19268_8','19269_1550','19269_18','19269_8','19270_5','19270_8','19272_8','19273_5','19273_8','19274_10','19274_1535','19274_1542','19274_3','19274_4','19274_8','19275_5','19275_8','19276_8','19277_8','19278_17','19278_8','19279_5','19279_8','19280_8','19281_8','19282_6','19282_8','19283_8','19284_1550','19284_18','19284_8','19285_8','19286_8','19287_8','19288_8','19289_8','19290_8','19291_8','19292_8','19293_8','19294_8','19295_1535','19295_17','19295_3','19295_8','19296_1535','19296_3','19296_8','19297_1535','19297_3','19297_8','19298_5','19298_8','19299_15','19299_1540','19299_1547','19299_8','19300_1540','19300_8','19301_8','19302_8','19303_1535','19303_1540','19303_3','19303_8','19304_1540','19304_8','19305_1538','19305_1550','19305_18','19305_6','19306_8','19307_8','19308_1540','19308_8','19309_3','19310_1549','19310_17','19311_8','19312_8','19313_18','19314_5','19315_1535','19315_3','19316_10','19316_1542','19317_1543','20056_1825','40038_1540')
AND "hobo_id" = 167
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:10:02 --> [LedgerCondensed][acc=7853] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:10:02 --> [LedgerCondensed][acc=7853] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:10:02 --> [LedgerCondensed][acc=7853] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 16:10:02 --> [LedgerCondensed][acc=7853] Q3-MainLedger => rows=94  total=94  |  0.0126s
1 - 2026-09-15 16:10:02 --> [LedgerCondensed][acc=7853] Q4-OtherAccounts => 94 rows  |  0.0072s
1 - 2026-09-15 16:10:02 --> [LedgerCondensed][acc=7853] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  4.5%)
   Q2-OpeningBalance           0.0003s   (  1.2%)
   Q3-MainLedger               0.0126s   ( 57.6%)
   Q4-OtherAccounts            0.0072s   ( 33.0%)
   BuildRecords                0.0004s   (  1.8%)
   TOTAL                       0.0219s   (100%)
[LedgerCondensed][acc=7853] ── FUNCTION END ──
1 - 2026-09-15 16:18:05 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:18:05 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:18:38 --> [LedgerCondensed][acc=6411] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:18:38 --> [LedgerCondensed][acc=6411] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:18:38 --> [LedgerCondensed][acc=6411] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 16:18:38 --> [LedgerCondensed][acc=6411] Q3-MainLedger => rows=100  total=1132  |  0.0155s
1 - 2026-09-15 16:18:38 --> [LedgerCondensed][acc=6411] Q4-OtherAccounts => 100 rows  |  0.0027s
1 - 2026-09-15 16:18:38 --> [LedgerCondensed][acc=6411] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  4.8%)
   Q2-OpeningBalance           0.0002s   (  1.2%)
   Q3-MainLedger               0.0155s   ( 76.1%)
   Q4-OtherAccounts            0.0027s   ( 13.3%)
   BuildRecords                0.0005s   (  2.3%)
   TOTAL                       0.0204s   (100%)
[LedgerCondensed][acc=6411] ── FUNCTION END ──
1 - 2026-09-15 16:18:43 --> [LedgerCondensed][acc=6411] ── FUNCTION START ── from=2025-04-01  to=2025-06-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:18:43 --> [LedgerCondensed][acc=6411] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 16:18:43 --> [LedgerCondensed][acc=6411] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 16:18:43 --> [LedgerCondensed][acc=6411] Q3-MainLedger => rows=100  total=218  |  0.0092s
1 - 2026-09-15 16:18:43 --> [LedgerCondensed][acc=6411] Q4-OtherAccounts => 100 rows  |  0.0027s
1 - 2026-09-15 16:18:43 --> [LedgerCondensed][acc=6411] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.0%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0092s   ( 67.2%)
   Q4-OtherAccounts            0.0027s   ( 19.7%)
   BuildRecords                0.0004s   (  2.7%)
   TOTAL                       0.0137s   (100%)
[LedgerCondensed][acc=6411] ── FUNCTION END ──
1 - 2026-09-15 16:19:18 --> [LedgerCondensed][acc=6411] ── FUNCTION START ── from=2025-04-01  to=2025-06-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:19:18 --> [LedgerCondensed][acc=6411] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:19:18 --> [LedgerCondensed][acc=6411] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-15 16:19:18 --> [LedgerCondensed][acc=6411] Q3-MainLedger => rows=18  total=218  |  0.01s
1 - 2026-09-15 16:19:18 --> [LedgerCondensed][acc=6411] Q4-OtherAccounts => 18 rows  |  0.0015s
1 - 2026-09-15 16:19:18 --> [LedgerCondensed][acc=6411] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.5%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0100s   ( 75.3%)
   Q4-OtherAccounts            0.0015s   ( 11.0%)
   BuildRecords                0.0001s   (  0.8%)
   TOTAL                       0.0133s   (100%)
[LedgerCondensed][acc=6411] ── FUNCTION END ──
1 - 2026-09-15 16:19:51 --> [LedgerCondensed][acc=6411] ── FUNCTION START ── from=2025-04-01  to=2025-06-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:19:51 --> [LedgerCondensed][acc=6411] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-15 16:19:51 --> [LedgerCondensed][acc=6411] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-15 16:19:51 --> [LedgerCondensed][acc=6411] Q3-MainLedger => rows=100  total=218  |  0.009s
1 - 2026-09-15 16:19:51 --> [LedgerCondensed][acc=6411] Q4-OtherAccounts => 100 rows  |  0.0024s
1 - 2026-09-15 16:19:51 --> [LedgerCondensed][acc=6411] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  8.1%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0090s   ( 66.5%)
   Q4-OtherAccounts            0.0024s   ( 17.4%)
   BuildRecords                0.0004s   (  2.8%)
   TOTAL                       0.0135s   (100%)
[LedgerCondensed][acc=6411] ── FUNCTION END ──
1 - 2026-09-15 16:19:58 --> [LedgerCondensed][acc=6411] ── FUNCTION START ── from=2025-04-01  to=2025-06-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:19:58 --> [LedgerCondensed][acc=6411] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:19:58 --> [LedgerCondensed][acc=6411] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 16:19:58 --> [LedgerCondensed][acc=6411] Q3-MainLedger => rows=100  total=218  |  0.0093s
1 - 2026-09-15 16:19:58 --> [LedgerCondensed][acc=6411] Q4-OtherAccounts => 100 rows  |  0.0026s
1 - 2026-09-15 16:19:58 --> [LedgerCondensed][acc=6411] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.1%)
   Q2-OpeningBalance           0.0002s   (  1.7%)
   Q3-MainLedger               0.0093s   ( 67.1%)
   Q4-OtherAccounts            0.0026s   ( 18.8%)
   BuildRecords                0.0004s   (  2.7%)
   TOTAL                       0.0138s   (100%)
[LedgerCondensed][acc=6411] ── FUNCTION END ──
1 - 2026-09-15 16:20:01 --> [LedgerCondensed][acc=6411] ── FUNCTION START ── from=2025-04-01  to=2025-06-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:20:01 --> [LedgerCondensed][acc=6411] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 16:20:01 --> [LedgerCondensed][acc=6411] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-15 16:20:01 --> [LedgerCondensed][acc=6411] Q3-MainLedger => rows=18  total=218  |  0.0093s
1 - 2026-09-15 16:20:01 --> [LedgerCondensed][acc=6411] Q4-OtherAccounts => 18 rows  |  0.0011s
1 - 2026-09-15 16:20:01 --> [LedgerCondensed][acc=6411] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.3%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0093s   ( 78.0%)
   Q4-OtherAccounts            0.0011s   (  9.1%)
   BuildRecords                0.0001s   (  0.6%)
   TOTAL                       0.0120s   (100%)
[LedgerCondensed][acc=6411] ── FUNCTION END ──
1 - 2026-09-15 16:37:09 --> [LedgerCondensed][acc=6593] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:37:09 --> [LedgerCondensed][acc=6593] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:37:09 --> [LedgerCondensed][acc=6593] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 16:37:09 --> [LedgerCondensed][acc=6593] Q3-MainLedger => rows=1  total=1  |  0.0125s
1 - 2026-09-15 16:37:09 --> [LedgerCondensed][acc=6593] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-09-15 16:37:09 --> [LedgerCondensed][acc=6593] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.6%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0125s   ( 82.2%)
   Q4-OtherAccounts            0.0010s   (  6.5%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0152s   (100%)
[LedgerCondensed][acc=6593] ── FUNCTION END ──
1 - 2026-09-15 16:37:12 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:37:12 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:37:12 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:37:12 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:37:45 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:37:45 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:37:45 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:37:45 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:37:52 --> [LedgerCondensed][acc=6633] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:37:52 --> [LedgerCondensed][acc=6633] Q1-VoucherCount => 0  |  0.0012s
1 - 2026-09-15 16:37:52 --> [LedgerCondensed][acc=6633] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 16:37:52 --> [LedgerCondensed][acc=6633] Q3-MainLedger => rows=1  total=1  |  0.0122s
1 - 2026-09-15 16:37:52 --> [LedgerCondensed][acc=6633] Q4-OtherAccounts => 1 rows  |  0.0011s
1 - 2026-09-15 16:37:52 --> [LedgerCondensed][acc=6633] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  7.7%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0122s   ( 80.2%)
   Q4-OtherAccounts            0.0011s   (  7.1%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0152s   (100%)
[LedgerCondensed][acc=6633] ── FUNCTION END ──
1 - 2026-09-15 16:38:03 --> [LedgerCondensed][acc=6633] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:38:03 --> [LedgerCondensed][acc=6633] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:38:03 --> [LedgerCondensed][acc=6633] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 16:38:03 --> [LedgerCondensed][acc=6633] Q3-MainLedger => rows=1  total=1  |  0.0093s
1 - 2026-09-15 16:38:03 --> [LedgerCondensed][acc=6633] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-15 16:38:03 --> [LedgerCondensed][acc=6633] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.2%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0093s   ( 78.3%)
   Q4-OtherAccounts            0.0009s   (  7.3%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0119s   (100%)
[LedgerCondensed][acc=6633] ── FUNCTION END ──
1 - 2026-09-15 16:38:05 --> [LedgerCondensed][acc=7853] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=1  compId=  boId=  fyId=
1 - 2026-09-15 16:38:05 --> [LedgerCondensed][acc=7853] Q1-VoucherCount => 0  |  0.0007s
1 - 2026-09-15 16:38:05 --> [LedgerCondensed][acc=7853] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-15 16:38:05 --> [LedgerCondensed][acc=7853] Q3-MainLedger => rows=94  total=94  |  0.0096s
1 - 2026-09-15 16:38:05 --> [LedgerCondensed][acc=7853] Q4-OtherAccounts => 94 rows  |  0.0041s
1 - 2026-09-15 16:38:05 --> [LedgerCondensed][acc=7853] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0007s   (  4.9%)
   Q2-OpeningBalance           0.0002s   (  1.0%)
   Q3-MainLedger               0.0096s   ( 62.5%)
   Q4-OtherAccounts            0.0041s   ( 26.7%)
   BuildRecords                0.0003s   (  2.2%)
   TOTAL                       0.0154s   (100%)
[LedgerCondensed][acc=7853] ── FUNCTION END ──
1 - 2026-09-15 16:38:09 --> [LedgerCondensed][acc=7853] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=1  compId=  boId=  fyId=
1 - 2026-09-15 16:38:09 --> [LedgerCondensed][acc=7853] Q1-VoucherCount => 0  |  0.0007s
1 - 2026-09-15 16:38:09 --> [LedgerCondensed][acc=7853] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 16:38:09 --> [LedgerCondensed][acc=7853] Q3-MainLedger => rows=94  total=94  |  0.0094s
1 - 2026-09-15 16:38:09 --> [LedgerCondensed][acc=7853] Q4-OtherAccounts => 94 rows  |  0.0037s
1 - 2026-09-15 16:38:09 --> [LedgerCondensed][acc=7853] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0007s   (  4.7%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0094s   ( 63.6%)
   Q4-OtherAccounts            0.0037s   ( 25.3%)
   BuildRecords                0.0003s   (  1.9%)
   TOTAL                       0.0147s   (100%)
[LedgerCondensed][acc=7853] ── FUNCTION END ──
1 - 2026-09-15 16:38:24 --> [LedgerCondensed][acc=6535] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:38:24 --> [LedgerCondensed][acc=6535] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 16:38:24 --> [LedgerCondensed][acc=6535] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-15 16:38:24 --> [LedgerCondensed][acc=6535] Q3-MainLedger => rows=2  total=2  |  0.0092s
1 - 2026-09-15 16:38:24 --> [LedgerCondensed][acc=6535] Q4-OtherAccounts => 2 rows  |  0.001s
1 - 2026-09-15 16:38:24 --> [LedgerCondensed][acc=6535] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.8%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0092s   ( 78.1%)
   Q4-OtherAccounts            0.0010s   (  8.2%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0117s   (100%)
[LedgerCondensed][acc=6535] ── FUNCTION END ──
1 - 2026-09-15 16:38:33 --> [LedgerCondensed][acc=6535] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:38:33 --> [LedgerCondensed][acc=6535] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 16:38:33 --> [LedgerCondensed][acc=6535] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 16:38:33 --> [LedgerCondensed][acc=6535] Q3-MainLedger => rows=2  total=2  |  0.0093s
1 - 2026-09-15 16:38:33 --> [LedgerCondensed][acc=6535] Q4-OtherAccounts => 2 rows  |  0.001s
1 - 2026-09-15 16:38:33 --> [LedgerCondensed][acc=6535] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.3%)
   Q2-OpeningBalance           0.0002s   (  2.1%)
   Q3-MainLedger               0.0093s   ( 79.1%)
   Q4-OtherAccounts            0.0010s   (  8.3%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0117s   (100%)
[LedgerCondensed][acc=6535] ── FUNCTION END ──
1 - 2026-09-15 16:38:50 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:38:50 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:38:50 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-15 16:38:50 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=100  total=1163  |  0.013s
1 - 2026-09-15 16:38:50 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 100 rows  |  0.0023s
1 - 2026-09-15 16:38:50 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.9%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0130s   ( 75.1%)
   Q4-OtherAccounts            0.0023s   ( 13.2%)
   BuildRecords                0.0003s   (  1.7%)
   TOTAL                       0.0173s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 16:38:56 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-08-01  to=2025-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:38:56 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 317  |  0.0022s
1 - 2026-09-15 16:38:56 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 668441.5  |  0.002s
1 - 2026-09-15 16:38:56 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=95  total=95  |  0.0092s
1 - 2026-09-15 16:38:56 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 95 rows  |  0.0022s
1 - 2026-09-15 16:38:56 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0022s   ( 13.6%)
   Q2-OpeningBalance           0.0020s   ( 12.4%)
   Q3-MainLedger               0.0092s   ( 56.1%)
   Q4-OtherAccounts            0.0022s   ( 13.3%)
   BuildRecords                0.0003s   (  1.6%)
   TOTAL                       0.0164s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 16:40:14 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:40:14 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:40:19 --> [LedgerCondensed][acc=6538] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:40:19 --> [LedgerCondensed][acc=6538] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:40:19 --> [LedgerCondensed][acc=6538] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 16:40:19 --> [LedgerCondensed][acc=6538] Q3-MainLedger => rows=3  total=3  |  0.0095s
1 - 2026-09-15 16:40:19 --> [LedgerCondensed][acc=6538] Q4-OtherAccounts => 3 rows  |  0.001s
1 - 2026-09-15 16:40:19 --> [LedgerCondensed][acc=6538] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.5%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0095s   ( 77.8%)
   Q4-OtherAccounts            0.0010s   (  8.4%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0122s   (100%)
[LedgerCondensed][acc=6538] ── FUNCTION END ──
1 - 2026-09-15 16:40:27 --> [LedgerCondensed][acc=6538] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:40:27 --> [LedgerCondensed][acc=6538] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 16:40:27 --> [LedgerCondensed][acc=6538] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 16:40:27 --> [LedgerCondensed][acc=6538] Q3-MainLedger => rows=3  total=3  |  0.0092s
1 - 2026-09-15 16:40:27 --> [LedgerCondensed][acc=6538] Q4-OtherAccounts => 3 rows  |  0.0011s
1 - 2026-09-15 16:40:27 --> [LedgerCondensed][acc=6538] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.5%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0092s   ( 78.6%)
   Q4-OtherAccounts            0.0011s   (  9.6%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0117s   (100%)
[LedgerCondensed][acc=6538] ── FUNCTION END ──
1 - 2026-09-15 16:40:46 --> [LedgerCondensed][acc=6538] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:40:46 --> [LedgerCondensed][acc=6538] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:40:46 --> [LedgerCondensed][acc=6538] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-15 16:40:46 --> [LedgerCondensed][acc=6538] Q3-MainLedger => rows=3  total=3  |  0.0092s
1 - 2026-09-15 16:40:46 --> [LedgerCondensed][acc=6538] Q4-OtherAccounts => 3 rows  |  0.0011s
1 - 2026-09-15 16:40:46 --> [LedgerCondensed][acc=6538] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.2%)
   Q2-OpeningBalance           0.0003s   (  2.4%)
   Q3-MainLedger               0.0092s   ( 76.3%)
   Q4-OtherAccounts            0.0011s   (  9.5%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0121s   (100%)
[LedgerCondensed][acc=6538] ── FUNCTION END ──
1 - 2026-09-15 16:40:49 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:40:49 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:41:01 --> [LedgerCondensed][acc=6535] ── FUNCTION START ── from=2025-08-01  to=2025-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:41:01 --> [LedgerCondensed][acc=6535] Q1-VoucherCount => 0  |  0.0012s
1 - 2026-09-15 16:41:01 --> [LedgerCondensed][acc=6535] Q2-OpeningBalance => 0  |  0.0008s
1 - 2026-09-15 16:41:01 --> [LedgerCondensed][acc=6535] Q3-MainLedger => rows=3  total=3  |  0.0087s
1 - 2026-09-15 16:41:01 --> [LedgerCondensed][acc=6535] Q4-OtherAccounts => 3 rows  |  0.001s
1 - 2026-09-15 16:41:01 --> [LedgerCondensed][acc=6535] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  9.6%)
   Q2-OpeningBalance           0.0008s   (  6.3%)
   Q3-MainLedger               0.0087s   ( 72.4%)
   Q4-OtherAccounts            0.0010s   (  8.0%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0121s   (100%)
[LedgerCondensed][acc=6535] ── FUNCTION END ──
1 - 2026-09-15 16:41:06 --> [LedgerCondensed][acc=6535] ── FUNCTION START ── from=2025-08-01  to=2025-08-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:41:06 --> [LedgerCondensed][acc=6535] Q1-VoucherCount => 0  |  0.0012s
1 - 2026-09-15 16:41:06 --> [LedgerCondensed][acc=6535] Q2-OpeningBalance => 0  |  0.0008s
1 - 2026-09-15 16:41:06 --> [LedgerCondensed][acc=6535] Q3-MainLedger => rows=3  total=3  |  0.009s
1 - 2026-09-15 16:41:06 --> [LedgerCondensed][acc=6535] Q4-OtherAccounts => 3 rows  |  0.001s
1 - 2026-09-15 16:41:06 --> [LedgerCondensed][acc=6535] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  9.7%)
   Q2-OpeningBalance           0.0008s   (  6.2%)
   Q3-MainLedger               0.0090s   ( 72.6%)
   Q4-OtherAccounts            0.0010s   (  8.1%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0124s   (100%)
[LedgerCondensed][acc=6535] ── FUNCTION END ──
1 - 2026-09-15 16:41:24 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:41:24 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:41:42 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:41:42 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:41:42 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-15 16:41:42 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=100  total=1163  |  0.0132s
1 - 2026-09-15 16:41:42 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 100 rows  |  0.0023s
1 - 2026-09-15 16:41:42 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.5%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0132s   ( 76.0%)
   Q4-OtherAccounts            0.0023s   ( 13.2%)
   BuildRecords                0.0002s   (  1.4%)
   TOTAL                       0.0173s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 16:43:35 --> [LedgerCondensed][acc=6528] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:43:35 --> [LedgerCondensed][acc=6528] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 16:43:35 --> [LedgerCondensed][acc=6528] Q2-OpeningBalance => -25800.00  |  0.0002s
1 - 2026-09-15 16:43:35 --> [LedgerCondensed][acc=6528] Q3-MainLedger => rows=0  total=0  |  0.0016s
1 - 2026-09-15 16:43:35 --> [LedgerCondensed][acc=6528] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-15 16:43:35 --> [LedgerCondensed][acc=6528] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 29.4%)
   Q2-OpeningBalance           0.0002s   (  7.7%)
   Q3-MainLedger               0.0016s   ( 51.2%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0032s   (100%)
[LedgerCondensed][acc=6528] ── FUNCTION END ──
1 - 2026-09-15 16:43:36 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:43:36 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:43:36 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:43:36 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:44:16 --> [LedgerCondensed][acc=6656] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:44:16 --> [LedgerCondensed][acc=6656] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 16:44:16 --> [LedgerCondensed][acc=6656] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-15 16:44:16 --> [LedgerCondensed][acc=6656] Q3-MainLedger => rows=1  total=1  |  0.0094s
1 - 2026-09-15 16:44:16 --> [LedgerCondensed][acc=6656] Q4-OtherAccounts => 1 rows  |  0.0008s
1 - 2026-09-15 16:44:16 --> [LedgerCondensed][acc=6656] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.8%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0094s   ( 79.5%)
   Q4-OtherAccounts            0.0008s   (  7.1%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0118s   (100%)
[LedgerCondensed][acc=6656] ── FUNCTION END ──
1 - 2026-09-15 16:44:30 --> [LedgerCondensed][acc=6656] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 16:44:30 --> [LedgerCondensed][acc=6656] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 16:44:30 --> [LedgerCondensed][acc=6656] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 16:44:30 --> [LedgerCondensed][acc=6656] Q3-MainLedger => rows=1  total=1  |  0.0092s
1 - 2026-09-15 16:44:30 --> [LedgerCondensed][acc=6656] Q4-OtherAccounts => 1 rows  |  0.0008s
1 - 2026-09-15 16:44:30 --> [LedgerCondensed][acc=6656] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.2%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0092s   ( 78.7%)
   Q4-OtherAccounts            0.0008s   (  7.2%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0117s   (100%)
[LedgerCondensed][acc=6656] ── FUNCTION END ──
1 - 2026-09-15 16:48:02 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:02 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:02 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:02 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:06 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:06 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:10 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:10 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:34 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:34 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:34 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:34 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:37 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:37 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:37 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:48:37 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:58:22 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:58:22 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:58:22 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:58:22 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:58:24 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:58:24 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:58:24 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:58:24 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:58:26 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:58:26 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:58:26 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 16:58:26 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:08:05 --> [LedgerCondensed][acc=6723] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:08:05 --> [LedgerCondensed][acc=6723] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 17:08:05 --> [LedgerCondensed][acc=6723] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 17:08:05 --> [LedgerCondensed][acc=6723] Q3-MainLedger => rows=20  total=20  |  0.0124s
1 - 2026-09-15 17:08:05 --> [LedgerCondensed][acc=6723] Q4-OtherAccounts => 20 rows  |  0.0012s
1 - 2026-09-15 17:08:05 --> [LedgerCondensed][acc=6723] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.6%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0124s   ( 80.3%)
   Q4-OtherAccounts            0.0012s   (  8.0%)
   BuildRecords                0.0001s   (  0.5%)
   TOTAL                       0.0154s   (100%)
[LedgerCondensed][acc=6723] ── FUNCTION END ──
1 - 2026-09-15 17:08:12 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:08:12 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:08:12 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:08:12 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:37:59 --> [LedgerCondensed][acc=6722] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:37:59 --> [LedgerCondensed][acc=6722] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 17:37:59 --> [LedgerCondensed][acc=6722] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 17:37:59 --> [LedgerCondensed][acc=6722] Q3-MainLedger => rows=100  total=163  |  0.0124s
1 - 2026-09-15 17:37:59 --> [LedgerCondensed][acc=6722] Q4-OtherAccounts => 100 rows  |  0.0026s
1 - 2026-09-15 17:37:59 --> [LedgerCondensed][acc=6722] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.0%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0124s   ( 72.8%)
   Q4-OtherAccounts            0.0026s   ( 15.1%)
   BuildRecords                0.0003s   (  1.9%)
   TOTAL                       0.0170s   (100%)
[LedgerCondensed][acc=6722] ── FUNCTION END ──
1 - 2026-09-15 17:38:07 --> [LedgerCondensed][acc=6722] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:38:07 --> [LedgerCondensed][acc=6722] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 17:38:07 --> [LedgerCondensed][acc=6722] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 17:38:07 --> [LedgerCondensed][acc=6722] Q3-MainLedger => rows=63  total=163  |  0.0095s
1 - 2026-09-15 17:38:07 --> [LedgerCondensed][acc=6722] Q4-OtherAccounts => 63 rows  |  0.0021s
1 - 2026-09-15 17:38:07 --> [LedgerCondensed][acc=6722] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.4%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0095s   ( 71.2%)
   Q4-OtherAccounts            0.0021s   ( 16.2%)
   BuildRecords                0.0002s   (  1.6%)
   TOTAL                       0.0133s   (100%)
[LedgerCondensed][acc=6722] ── FUNCTION END ──
1 - 2026-09-15 17:38:11 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:38:11 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:38:11 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:38:11 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:38:24 --> [LedgerCondensed][acc=6580] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:38:24 --> [LedgerCondensed][acc=6580] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 17:38:24 --> [LedgerCondensed][acc=6580] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-15 17:38:24 --> [LedgerCondensed][acc=6580] Q3-MainLedger => rows=1  total=1  |  0.0123s
1 - 2026-09-15 17:38:24 --> [LedgerCondensed][acc=6580] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-09-15 17:38:24 --> [LedgerCondensed][acc=6580] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.2%)
   Q2-OpeningBalance           0.0002s   (  1.6%)
   Q3-MainLedger               0.0123s   ( 82.1%)
   Q4-OtherAccounts            0.0010s   (  6.9%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0150s   (100%)
[LedgerCondensed][acc=6580] ── FUNCTION END ──
1 - 2026-09-15 17:38:30 --> [LedgerCondensed][acc=6580] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:38:30 --> [LedgerCondensed][acc=6580] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 17:38:30 --> [LedgerCondensed][acc=6580] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-15 17:38:30 --> [LedgerCondensed][acc=6580] Q3-MainLedger => rows=100  total=228  |  0.0093s
1 - 2026-09-15 17:38:30 --> [LedgerCondensed][acc=6580] Q4-OtherAccounts => 100 rows  |  0.0024s
1 - 2026-09-15 17:38:30 --> [LedgerCondensed][acc=6580] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.1%)
   Q2-OpeningBalance           0.0002s   (  1.7%)
   Q3-MainLedger               0.0093s   ( 68.4%)
   Q4-OtherAccounts            0.0024s   ( 17.3%)
   BuildRecords                0.0003s   (  2.5%)
   TOTAL                       0.0137s   (100%)
[LedgerCondensed][acc=6580] ── FUNCTION END ──
1 - 2026-09-15 17:39:50 --> [LedgerCondensed][acc=6580] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:39:50 --> [LedgerCondensed][acc=6580] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 17:39:50 --> [LedgerCondensed][acc=6580] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-15 17:39:50 --> [LedgerCondensed][acc=6580] Q3-MainLedger => rows=1  total=1  |  0.0095s
1 - 2026-09-15 17:39:50 --> [LedgerCondensed][acc=6580] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-15 17:39:50 --> [LedgerCondensed][acc=6580] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.4%)
   Q2-OpeningBalance           0.0002s   (  2.0%)
   Q3-MainLedger               0.0095s   ( 79.6%)
   Q4-OtherAccounts            0.0009s   (  7.3%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0120s   (100%)
[LedgerCondensed][acc=6580] ── FUNCTION END ──
1 - 2026-09-15 17:39:56 --> [LedgerCondensed][acc=6580] ── FUNCTION START ── from=2025-04-01  to=2025-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:39:56 --> [LedgerCondensed][acc=6580] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 17:39:56 --> [LedgerCondensed][acc=6580] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-15 17:39:56 --> [LedgerCondensed][acc=6580] Q3-MainLedger => rows=1  total=1  |  0.0092s
1 - 2026-09-15 17:39:56 --> [LedgerCondensed][acc=6580] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-15 17:39:56 --> [LedgerCondensed][acc=6580] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  7.3%)
   Q2-OpeningBalance           0.0002s   (  2.1%)
   Q3-MainLedger               0.0092s   ( 79.2%)
   Q4-OtherAccounts            0.0009s   (  7.5%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0116s   (100%)
[LedgerCondensed][acc=6580] ── FUNCTION END ──
1 - 2026-09-15 17:40:04 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:40:04 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:40:08 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:40:08 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 17:40:08 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-15 17:40:08 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=100  total=1163  |  0.0125s
1 - 2026-09-15 17:40:08 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 100 rows  |  0.0025s
1 - 2026-09-15 17:40:08 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.4%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0125s   ( 73.4%)
   Q4-OtherAccounts            0.0025s   ( 14.9%)
   BuildRecords                0.0003s   (  1.9%)
   TOTAL                       0.0171s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 17:40:17 --> [LedgerCondensed][acc=6407] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:40:17 --> [LedgerCondensed][acc=6407] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 17:40:17 --> [LedgerCondensed][acc=6407] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-15 17:40:17 --> [LedgerCondensed][acc=6407] Q3-MainLedger => rows=63  total=1163  |  0.0126s
1 - 2026-09-15 17:40:17 --> [LedgerCondensed][acc=6407] Q4-OtherAccounts => 63 rows  |  0.0022s
1 - 2026-09-15 17:40:17 --> [LedgerCondensed][acc=6407] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.7%)
   Q2-OpeningBalance           0.0002s   (  1.5%)
   Q3-MainLedger               0.0126s   ( 76.0%)
   Q4-OtherAccounts            0.0022s   ( 13.2%)
   BuildRecords                0.0002s   (  1.3%)
   TOTAL                       0.0166s   (100%)
[LedgerCondensed][acc=6407] ── FUNCTION END ──
1 - 2026-09-15 17:40:25 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:40:25 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:56:13 --> [LedgerCondensed][acc=6548] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:56:13 --> [LedgerCondensed][acc=6548] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 17:56:13 --> [LedgerCondensed][acc=6548] Q2-OpeningBalance => 683983.00  |  0.0003s
1 - 2026-09-15 17:56:13 --> [LedgerCondensed][acc=6548] Q3-MainLedger => rows=0  total=0  |  0.0017s
1 - 2026-09-15 17:56:13 --> [LedgerCondensed][acc=6548] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-15 17:56:13 --> [LedgerCondensed][acc=6548] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 28.6%)
   Q2-OpeningBalance           0.0003s   (  8.7%)
   Q3-MainLedger               0.0017s   ( 52.2%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0032s   (100%)
[LedgerCondensed][acc=6548] ── FUNCTION END ──
1 - 2026-09-15 17:56:15 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:56:15 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:56:15 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:56:15 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:56:25 --> [LedgerCondensed][acc=6534] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:56:25 --> [LedgerCondensed][acc=6534] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 17:56:25 --> [LedgerCondensed][acc=6534] Q2-OpeningBalance => -14160.00  |  0.0002s
1 - 2026-09-15 17:56:25 --> [LedgerCondensed][acc=6534] Q3-MainLedger => rows=2  total=2  |  0.0119s
1 - 2026-09-15 17:56:25 --> [LedgerCondensed][acc=6534] Q4-OtherAccounts => 2 rows  |  0.0011s
1 - 2026-09-15 17:56:25 --> [LedgerCondensed][acc=6534] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.7%)
   Q2-OpeningBalance           0.0002s   (  1.7%)
   Q3-MainLedger               0.0119s   ( 81.2%)
   Q4-OtherAccounts            0.0011s   (  7.4%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0147s   (100%)
[LedgerCondensed][acc=6534] ── FUNCTION END ──
1 - 2026-09-15 17:56:28 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:56:28 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:56:28 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:56:28 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:56:33 --> [LedgerCondensed][acc=6593] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:56:33 --> [LedgerCondensed][acc=6593] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 17:56:33 --> [LedgerCondensed][acc=6593] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-15 17:56:33 --> [LedgerCondensed][acc=6593] Q3-MainLedger => rows=1  total=1  |  0.0089s
1 - 2026-09-15 17:56:33 --> [LedgerCondensed][acc=6593] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-09-15 17:56:33 --> [LedgerCondensed][acc=6593] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  6.6%)
   Q2-OpeningBalance           0.0003s   (  2.2%)
   Q3-MainLedger               0.0089s   ( 78.0%)
   Q4-OtherAccounts            0.0010s   (  9.0%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0114s   (100%)
[LedgerCondensed][acc=6593] ── FUNCTION END ──
1 - 2026-09-15 17:56:34 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:56:34 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:56:34 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:56:34 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 17:56:42 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:56:42 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-15 17:56:42 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0003s
1 - 2026-09-15 17:56:42 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=100  total=622  |  0.0102s
1 - 2026-09-15 17:56:42 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 100 rows  |  0.0024s
1 - 2026-09-15 17:56:42 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  7.3%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0102s   ( 69.3%)
   Q4-OtherAccounts            0.0024s   ( 16.4%)
   BuildRecords                0.0004s   (  2.5%)
   TOTAL                       0.0147s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-15 17:56:51 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:56:51 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 17:56:51 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0003s
1 - 2026-09-15 17:56:51 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=100  total=622  |  0.0102s
1 - 2026-09-15 17:56:51 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 100 rows  |  0.0023s
1 - 2026-09-15 17:56:51 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.9%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0102s   ( 70.2%)
   Q4-OtherAccounts            0.0023s   ( 16.0%)
   BuildRecords                0.0004s   (  2.5%)
   TOTAL                       0.0146s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-15 17:57:02 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:57:02 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 17:57:02 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0003s
1 - 2026-09-15 17:57:02 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=100  total=622  |  0.01s
1 - 2026-09-15 17:57:02 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 100 rows  |  0.0025s
1 - 2026-09-15 17:57:02 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.8%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0100s   ( 68.7%)
   Q4-OtherAccounts            0.0025s   ( 17.1%)
   BuildRecords                0.0004s   (  2.8%)
   TOTAL                       0.0146s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-15 17:57:16 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:57:16 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 17:57:16 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0003s
1 - 2026-09-15 17:57:16 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=100  total=622  |  0.0101s
1 - 2026-09-15 17:57:16 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 100 rows  |  0.0026s
1 - 2026-09-15 17:57:16 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.0%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0101s   ( 67.6%)
   Q4-OtherAccounts            0.0026s   ( 17.5%)
   BuildRecords                0.0004s   (  3.0%)
   TOTAL                       0.0149s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-15 17:57:30 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:57:30 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-15 17:57:30 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0003s
1 - 2026-09-15 17:57:30 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=22  total=622  |  0.0102s
1 - 2026-09-15 17:57:30 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 22 rows  |  0.0014s
1 - 2026-09-15 17:57:30 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.4%)
   Q2-OpeningBalance           0.0003s   (  2.0%)
   Q3-MainLedger               0.0102s   ( 75.9%)
   Q4-OtherAccounts            0.0014s   ( 10.1%)
   BuildRecords                0.0001s   (  1.0%)
   TOTAL                       0.0135s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-15 17:57:40 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:57:40 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-15 17:57:40 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0002s
1 - 2026-09-15 17:57:40 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=100  total=622  |  0.0098s
1 - 2026-09-15 17:57:40 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 100 rows  |  0.0031s
1 - 2026-09-15 17:57:40 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  5.2%)
   Q2-OpeningBalance           0.0002s   (  1.7%)
   Q3-MainLedger               0.0098s   ( 67.1%)
   Q4-OtherAccounts            0.0031s   ( 21.5%)
   BuildRecords                0.0003s   (  2.0%)
   TOTAL                       0.0146s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-15 17:57:50 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:57:50 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 17:57:50 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0003s
1 - 2026-09-15 17:57:50 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=100  total=622  |  0.0103s
1 - 2026-09-15 17:57:50 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 100 rows  |  0.0027s
1 - 2026-09-15 17:57:50 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.3%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0103s   ( 69.2%)
   Q4-OtherAccounts            0.0027s   ( 18.0%)
   BuildRecords                0.0004s   (  2.5%)
   TOTAL                       0.0149s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-15 17:58:00 --> [LedgerCondensed][acc=6586] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-15 17:58:00 --> [LedgerCondensed][acc=6586] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-15 17:58:00 --> [LedgerCondensed][acc=6586] Q2-OpeningBalance => 93952.95  |  0.0003s
1 - 2026-09-15 17:58:00 --> [LedgerCondensed][acc=6586] Q3-MainLedger => rows=100  total=622  |  0.0101s
1 - 2026-09-15 17:58:00 --> [LedgerCondensed][acc=6586] Q4-OtherAccounts => 100 rows  |  0.0026s
1 - 2026-09-15 17:58:00 --> [LedgerCondensed][acc=6586] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.1%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0101s   ( 69.0%)
   Q4-OtherAccounts            0.0026s   ( 18.0%)
   BuildRecords                0.0004s   (  2.6%)
   TOTAL                       0.0147s   (100%)
[LedgerCondensed][acc=6586] ── FUNCTION END ──
1 - 2026-09-15 18:03:00 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 18:03:00 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 18:03:00 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
1 - 2026-09-15 18:03:00 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 160
AND "cmp_id" = 98
AND "itm_id_unit_id" IN ('1762_1412','1762_1418','1763_8','1764_1409','1764_1418','1764_8','1765_1404','1765_3','1766_1409','1767_1409','1767_8','1768_1409','1769_1409','39525_1409')
AND "hobo_id" = 151
GROUP BY "itm_id_unit_id"
