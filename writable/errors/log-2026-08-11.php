<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-08-11 09:42:35 --> [LedgerCondensed][acc=648] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 09:42:35 --> [LedgerCondensed][acc=648] Q1-VoucherCount => 0  |  0.0026s
1 - 2026-08-11 09:42:35 --> [LedgerCondensed][acc=648] Q2-OpeningBalance => -3338.00  |  0.0003s
1 - 2026-08-11 09:42:36 --> [LedgerCondensed][acc=648] Q3-MainLedger => rows=11  total=11  |  0.0758s
1 - 2026-08-11 09:42:36 --> [LedgerCondensed][acc=648] Q4-OtherAccounts => 11 rows  |  0.0126s
1 - 2026-08-11 09:42:36 --> [LedgerCondensed][acc=648] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0026s   (  2.8%)
   Q2-OpeningBalance           0.0003s   (  0.3%)
   Q3-MainLedger               0.0758s   ( 82.6%)
   Q4-OtherAccounts            0.0126s   ( 13.7%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0918s   (100%)
[LedgerCondensed][acc=648] ── FUNCTION END ──
1 - 2026-08-11 09:49:40 --> [LedgerCondensed][acc=648] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 09:49:40 --> [LedgerCondensed][acc=648] Q1-VoucherCount => 0  |  0.001s
1 - 2026-08-11 09:49:40 --> [LedgerCondensed][acc=648] Q2-OpeningBalance => -3338.00  |  0.0003s
1 - 2026-08-11 09:49:40 --> [LedgerCondensed][acc=648] Q3-MainLedger => rows=11  total=11  |  0.0692s
1 - 2026-08-11 09:49:40 --> [LedgerCondensed][acc=648] Q4-OtherAccounts => 11 rows  |  0.002s
1 - 2026-08-11 09:49:41 --> [LedgerCondensed][acc=648] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  1.4%)
   Q2-OpeningBalance           0.0003s   (  0.4%)
   Q3-MainLedger               0.0692s   ( 94.7%)
   Q4-OtherAccounts            0.0020s   (  2.8%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0731s   (100%)
[LedgerCondensed][acc=648] ── FUNCTION END ──
1 - 2026-08-11 09:54:34 --> [LedgerCondensed][acc=648] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 09:54:34 --> [LedgerCondensed][acc=648] Q1-VoucherCount => 0  |  0.001s
1 - 2026-08-11 09:54:34 --> [LedgerCondensed][acc=648] Q2-OpeningBalance => -3338.00  |  0.0003s
1 - 2026-08-11 09:54:34 --> [LedgerCondensed][acc=648] Q3-MainLedger => rows=11  total=11  |  0.0664s
1 - 2026-08-11 09:54:34 --> [LedgerCondensed][acc=648] Q4-OtherAccounts => 11 rows  |  0.0019s
1 - 2026-08-11 09:54:34 --> [LedgerCondensed][acc=648] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  1.4%)
   Q2-OpeningBalance           0.0003s   (  0.4%)
   Q3-MainLedger               0.0664s   ( 94.9%)
   Q4-OtherAccounts            0.0019s   (  2.7%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0699s   (100%)
[LedgerCondensed][acc=648] ── FUNCTION END ──
1 - 2026-08-11 09:54:36 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 331
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 09:54:36 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 331
AND "cmp_id" = 150
AND "itm_id_unit_id" IN ('21688_2014')
AND "hobo_id" = 201
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 09:54:41 --> [LedgerCondensed][acc=11979] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 09:54:41 --> [LedgerCondensed][acc=11979] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-08-11 09:54:41 --> [LedgerCondensed][acc=11979] Q2-OpeningBalance => 0  |  0.0004s
1 - 2026-08-11 09:54:41 --> [LedgerCondensed][acc=11979] Q3-MainLedger => rows=100  total=122  |  0.0092s
1 - 2026-08-11 09:54:41 --> [LedgerCondensed][acc=11979] Q4-OtherAccounts => 100 rows  |  0.0184s
1 - 2026-08-11 09:54:41 --> [LedgerCondensed][acc=11979] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  3.8%)
   Q2-OpeningBalance           0.0004s   (  1.2%)
   Q3-MainLedger               0.0092s   ( 30.9%)
   Q4-OtherAccounts            0.0184s   ( 61.7%)
   BuildRecords                0.0003s   (  1.0%)
   TOTAL                       0.0299s   (100%)
[LedgerCondensed][acc=11979] ── FUNCTION END ──
1 - 2026-08-11 09:54:50 --> [LedgerCondensed][acc=11979] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 09:54:50 --> [LedgerCondensed][acc=11979] Q1-VoucherCount => 0  |  0.0012s
1 - 2026-08-11 09:54:50 --> [LedgerCondensed][acc=11979] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-08-11 09:54:50 --> [LedgerCondensed][acc=11979] Q3-MainLedger => rows=100  total=122  |  0.0098s
1 - 2026-08-11 09:54:50 --> [LedgerCondensed][acc=11979] Q4-OtherAccounts => 100 rows  |  0.0057s
1 - 2026-08-11 09:54:50 --> [LedgerCondensed][acc=11979] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  6.6%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0098s   ( 55.5%)
   Q4-OtherAccounts            0.0057s   ( 32.2%)
   BuildRecords                0.0003s   (  1.7%)
   TOTAL                       0.0177s   (100%)
[LedgerCondensed][acc=11979] ── FUNCTION END ──
1 - 2026-08-11 09:55:22 --> [LedgerCondensed][acc=648] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 09:55:22 --> [LedgerCondensed][acc=648] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-08-11 09:55:22 --> [LedgerCondensed][acc=648] Q2-OpeningBalance => -3338.00  |  0.0003s
1 - 2026-08-11 09:55:22 --> [LedgerCondensed][acc=648] Q3-MainLedger => rows=11  total=11  |  0.0677s
1 - 2026-08-11 09:55:22 --> [LedgerCondensed][acc=648] Q4-OtherAccounts => 11 rows  |  0.0023s
1 - 2026-08-11 09:55:22 --> [LedgerCondensed][acc=648] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  1.5%)
   Q2-OpeningBalance           0.0003s   (  0.4%)
   Q3-MainLedger               0.0677s   ( 94.2%)
   Q4-OtherAccounts            0.0023s   (  3.2%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0719s   (100%)
[LedgerCondensed][acc=648] ── FUNCTION END ──
1 - 2026-08-11 09:55:58 --> [LedgerCondensed][acc=648] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 09:55:58 --> [LedgerCondensed][acc=648] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-08-11 09:55:58 --> [LedgerCondensed][acc=648] Q2-OpeningBalance => -3338.00  |  0.0003s
1 - 2026-08-11 09:55:58 --> [LedgerCondensed][acc=648] Q3-MainLedger => rows=11  total=11  |  0.0695s
1 - 2026-08-11 09:55:58 --> [LedgerCondensed][acc=648] Q4-OtherAccounts => 11 rows  |  0.0024s
1 - 2026-08-11 09:55:58 --> [LedgerCondensed][acc=648] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  1.5%)
   Q2-OpeningBalance           0.0003s   (  0.4%)
   Q3-MainLedger               0.0695s   ( 94.2%)
   Q4-OtherAccounts            0.0024s   (  3.2%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0737s   (100%)
[LedgerCondensed][acc=648] ── FUNCTION END ──
1 - 2026-08-11 09:56:49 --> [LedgerCondensed][acc=648] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 09:56:49 --> [LedgerCondensed][acc=648] Q1-VoucherCount => 0  |  0.001s
1 - 2026-08-11 09:56:49 --> [LedgerCondensed][acc=648] Q2-OpeningBalance => -3338.00  |  0.0003s
1 - 2026-08-11 09:56:49 --> [LedgerCondensed][acc=648] Q3-MainLedger => rows=11  total=11  |  0.0681s
1 - 2026-08-11 09:56:49 --> [LedgerCondensed][acc=648] Q4-OtherAccounts => 11 rows  |  0.0022s
1 - 2026-08-11 09:56:49 --> [LedgerCondensed][acc=648] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  1.4%)
   Q2-OpeningBalance           0.0003s   (  0.4%)
   Q3-MainLedger               0.0681s   ( 94.5%)
   Q4-OtherAccounts            0.0022s   (  3.0%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0720s   (100%)
[LedgerCondensed][acc=648] ── FUNCTION END ──
1 - 2026-08-11 09:57:33 --> [LedgerCondensed][acc=648] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 09:57:33 --> [LedgerCondensed][acc=648] Q1-VoucherCount => 0  |  0.001s
1 - 2026-08-11 09:57:33 --> [LedgerCondensed][acc=648] Q2-OpeningBalance => -3338.00  |  0.0003s
1 - 2026-08-11 09:57:33 --> [LedgerCondensed][acc=648] Q3-MainLedger => rows=11  total=11  |  0.0676s
1 - 2026-08-11 09:57:33 --> [LedgerCondensed][acc=648] Q4-OtherAccounts => 11 rows  |  0.0024s
1 - 2026-08-11 09:57:33 --> [LedgerCondensed][acc=648] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  1.4%)
   Q2-OpeningBalance           0.0003s   (  0.4%)
   Q3-MainLedger               0.0676s   ( 94.1%)
   Q4-OtherAccounts            0.0024s   (  3.4%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0718s   (100%)
[LedgerCondensed][acc=648] ── FUNCTION END ──
1 - 2026-08-11 09:58:13 --> [LedgerCondensed][acc=648] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 09:58:13 --> [LedgerCondensed][acc=648] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-08-11 09:58:13 --> [LedgerCondensed][acc=648] Q2-OpeningBalance => -3338.00  |  0.0003s
1 - 2026-08-11 09:58:13 --> [LedgerCondensed][acc=648] Q3-MainLedger => rows=11  total=11  |  0.0686s
1 - 2026-08-11 09:58:13 --> [LedgerCondensed][acc=648] Q4-OtherAccounts => 11 rows  |  0.0021s
1 - 2026-08-11 09:58:13 --> [LedgerCondensed][acc=648] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  1.3%)
   Q2-OpeningBalance           0.0003s   (  0.4%)
   Q3-MainLedger               0.0686s   ( 94.7%)
   Q4-OtherAccounts            0.0021s   (  2.9%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0725s   (100%)
[LedgerCondensed][acc=648] ── FUNCTION END ──
1 - 2026-08-11 09:58:47 --> [LedgerCondensed][acc=648] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 09:58:47 --> [LedgerCondensed][acc=648] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-08-11 09:58:47 --> [LedgerCondensed][acc=648] Q2-OpeningBalance => -3338.00  |  0.0003s
1 - 2026-08-11 09:58:47 --> [LedgerCondensed][acc=648] Q3-MainLedger => rows=11  total=11  |  0.0686s
1 - 2026-08-11 09:58:47 --> [LedgerCondensed][acc=648] Q4-OtherAccounts => 11 rows  |  0.0021s
1 - 2026-08-11 09:58:47 --> [LedgerCondensed][acc=648] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  1.5%)
   Q2-OpeningBalance           0.0003s   (  0.4%)
   Q3-MainLedger               0.0686s   ( 94.4%)
   Q4-OtherAccounts            0.0021s   (  2.9%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0727s   (100%)
[LedgerCondensed][acc=648] ── FUNCTION END ──
1 - 2026-08-11 09:59:54 --> [LedgerCondensed][acc=648] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 09:59:54 --> [LedgerCondensed][acc=648] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-08-11 09:59:54 --> [LedgerCondensed][acc=648] Q2-OpeningBalance => -3338.00  |  0.0003s
1 - 2026-08-11 09:59:54 --> [LedgerCondensed][acc=648] Q3-MainLedger => rows=11  total=11  |  0.0677s
1 - 2026-08-11 09:59:54 --> [LedgerCondensed][acc=648] Q4-OtherAccounts => 11 rows  |  0.002s
1 - 2026-08-11 09:59:54 --> [LedgerCondensed][acc=648] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  1.5%)
   Q2-OpeningBalance           0.0003s   (  0.5%)
   Q3-MainLedger               0.0677s   ( 94.4%)
   Q4-OtherAccounts            0.0020s   (  2.8%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0717s   (100%)
[LedgerCondensed][acc=648] ── FUNCTION END ──
1 - 2026-08-11 10:00:28 --> [LedgerCondensed][acc=648] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 10:00:28 --> [LedgerCondensed][acc=648] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-08-11 10:00:28 --> [LedgerCondensed][acc=648] Q2-OpeningBalance => -3338.00  |  0.0003s
1 - 2026-08-11 10:00:28 --> [LedgerCondensed][acc=648] Q3-MainLedger => rows=11  total=11  |  0.0669s
1 - 2026-08-11 10:00:28 --> [LedgerCondensed][acc=648] Q4-OtherAccounts => 11 rows  |  0.002s
1 - 2026-08-11 10:00:28 --> [LedgerCondensed][acc=648] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  1.5%)
   Q2-OpeningBalance           0.0003s   (  0.5%)
   Q3-MainLedger               0.0669s   ( 94.5%)
   Q4-OtherAccounts            0.0020s   (  2.8%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0709s   (100%)
[LedgerCondensed][acc=648] ── FUNCTION END ──
1 - 2026-08-11 10:02:38 --> [LedgerCondensed][acc=648] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 10:02:38 --> [LedgerCondensed][acc=648] Q1-VoucherCount => 0  |  0.0012s
1 - 2026-08-11 10:02:38 --> [LedgerCondensed][acc=648] Q2-OpeningBalance => -3338.00  |  0.0004s
1 - 2026-08-11 10:02:38 --> [LedgerCondensed][acc=648] Q3-MainLedger => rows=11  total=11  |  0.0728s
1 - 2026-08-11 10:02:38 --> [LedgerCondensed][acc=648] Q4-OtherAccounts => 11 rows  |  0.0021s
1 - 2026-08-11 10:02:38 --> [LedgerCondensed][acc=648] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  1.6%)
   Q2-OpeningBalance           0.0004s   (  0.5%)
   Q3-MainLedger               0.0728s   ( 94.5%)
   Q4-OtherAccounts            0.0021s   (  2.7%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0771s   (100%)
[LedgerCondensed][acc=648] ── FUNCTION END ──
1 - 2026-08-11 10:56:39 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 334
AND "cmp_id" = 156
AND "itm_id_unit_id" IN ('39211_7','39212_7','39213_4','39214_9','39215_4','39216_3','39217_2124','39217_4','39218_2128','39218_8','39219_3','39220_17','39221_8','39222_17','39222_2128','39222_6','39223_4','39224_4','39225_6','39226_3','39227_4','39228_4','39229_4','39230_4','39231_5','39232_3','39232_8','39233_5','39234_8','39235_8','39236_8','39237_8','39238_7','39239_7','39240_7','39241_7','39242_8','39243_8','39244_3','39244_8','39245_2123','39245_3','39246_4','39247_2123','39247_3','39248_18','39249_8','39250_3','39251_4','39252_8','39253_8','39254_2127','39254_7','39255_8','39256_4','39257_4','39258_8','39259_4','39260_3','39261_9','39262_9','39263_2129','39263_9','39264_7','39265_7','39266_7','39267_7','39268_7','39269_2128','39269_8','39270_2128','39270_8','39271_7','39272_6','39273_4','39274_4','39275_9','39276_4','39277_2124','39277_4','39278_8','39279_4','39280_8','39281_8','39282_17','39282_2137','39285_2127','39285_7','39286_2127','39286_7','39287_2127','39287_7','39288_2127','39288_7','39289_2127','39296_2127','39299_2124','39300_2129','39302_2123','39308_8','39311_2127','39318_2129','75044_2128','75046_2123','75136_2124','75173_2128','75174_2129','75175_2140','75176_2140','75178_2128','75179_2128')
AND "hobo_id" = 211
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 10:56:39 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 334
AND "cmp_id" = 156
AND "itm_id_unit_id" IN ('39211_7','39212_7','39213_4','39214_9','39215_4','39216_3','39217_2124','39217_4','39218_2128','39218_8','39219_3','39220_17','39221_8','39222_17','39222_2128','39222_6','39223_4','39224_4','39225_6','39226_3','39227_4','39228_4','39229_4','39230_4','39231_5','39232_3','39232_8','39233_5','39234_8','39235_8','39236_8','39237_8','39238_7','39239_7','39240_7','39241_7','39242_8','39243_8','39244_3','39244_8','39245_2123','39245_3','39246_4','39247_2123','39247_3','39248_18','39249_8','39250_3','39251_4','39252_8','39253_8','39254_2127','39254_7','39255_8','39256_4','39257_4','39258_8','39259_4','39260_3','39261_9','39262_9','39263_2129','39263_9','39264_7','39265_7','39266_7','39267_7','39268_7','39269_2128','39269_8','39270_2128','39270_8','39271_7','39272_6','39273_4','39274_4','39275_9','39276_4','39277_2124','39277_4','39278_8','39279_4','39280_8','39281_8','39282_17','39282_2137','39285_2127','39285_7','39286_2127','39286_7','39287_2127','39287_7','39288_2127','39288_7','39289_2127','39296_2127','39299_2124','39300_2129','39302_2123','39308_8','39311_2127','39318_2129','75044_2128','75046_2123','75136_2124','75173_2128','75174_2129','75175_2140','75176_2140','75178_2128','75179_2128')
AND "hobo_id" = 211
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 10:56:42 --> [LedgerCondensed][acc=13938] ── FUNCTION START ── from=2026-04-01  to=2027-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 10:56:42 --> [LedgerCondensed][acc=13938] Q1-VoucherCount => 0  |  0.0012s
1 - 2026-08-11 10:56:42 --> [LedgerCondensed][acc=13938] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-08-11 10:56:42 --> [LedgerCondensed][acc=13938] Q3-MainLedger => rows=22  total=22  |  0.1416s
1 - 2026-08-11 10:56:42 --> [LedgerCondensed][acc=13938] Q4-OtherAccounts => 22 rows  |  0.0031s
1 - 2026-08-11 10:56:42 --> [LedgerCondensed][acc=13938] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  0.8%)
   Q2-OpeningBalance           0.0003s   (  0.2%)
   Q3-MainLedger               0.1416s   ( 96.5%)
   Q4-OtherAccounts            0.0031s   (  2.1%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.1468s   (100%)
[LedgerCondensed][acc=13938] ── FUNCTION END ──
1 - 2026-08-11 10:56:50 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 334
AND "cmp_id" = 156
AND "itm_id_unit_id" IN ('39211_7','39212_7','39213_4','39214_9','39215_4','39216_3','39217_2124','39217_4','39218_2128','39218_8','39219_3','39220_17','39221_8','39222_17','39222_2128','39222_6','39223_4','39224_4','39225_6','39226_3','39227_4','39228_4','39229_4','39230_4','39231_5','39232_3','39232_8','39233_5','39234_8','39235_8','39236_8','39237_8','39238_7','39239_7','39240_7','39241_7','39242_8','39243_8','39244_3','39244_8','39245_2123','39245_3','39246_4','39247_2123','39247_3','39248_18','39249_8','39250_3','39251_4','39252_8','39253_8','39254_2127','39254_7','39255_8','39256_4','39257_4','39258_8','39259_4','39260_3','39261_9','39262_9','39263_2129','39263_9','39264_7','39265_7','39266_7','39267_7','39268_7','39269_2128','39269_8','39270_2128','39270_8','39271_7','39272_6','39273_4','39274_4','39275_9','39276_4','39277_2124','39277_4','39278_8','39279_4','39280_8','39281_8','39282_17','39282_2137','39285_2127','39285_7','39286_2127','39286_7','39287_2127','39287_7','39288_2127','39288_7','39289_2127','39296_2127','39299_2124','39300_2129','39302_2123','39308_8','39311_2127','39318_2129','75044_2128','75046_2123','75136_2124','75173_2128','75174_2129','75175_2140','75176_2140','75178_2128','75179_2128')
AND "hobo_id" = 211
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 10:56:50 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 334
AND "cmp_id" = 156
AND "itm_id_unit_id" IN ('39211_7','39212_7','39213_4','39214_9','39215_4','39216_3','39217_2124','39217_4','39218_2128','39218_8','39219_3','39220_17','39221_8','39222_17','39222_2128','39222_6','39223_4','39224_4','39225_6','39226_3','39227_4','39228_4','39229_4','39230_4','39231_5','39232_3','39232_8','39233_5','39234_8','39235_8','39236_8','39237_8','39238_7','39239_7','39240_7','39241_7','39242_8','39243_8','39244_3','39244_8','39245_2123','39245_3','39246_4','39247_2123','39247_3','39248_18','39249_8','39250_3','39251_4','39252_8','39253_8','39254_2127','39254_7','39255_8','39256_4','39257_4','39258_8','39259_4','39260_3','39261_9','39262_9','39263_2129','39263_9','39264_7','39265_7','39266_7','39267_7','39268_7','39269_2128','39269_8','39270_2128','39270_8','39271_7','39272_6','39273_4','39274_4','39275_9','39276_4','39277_2124','39277_4','39278_8','39279_4','39280_8','39281_8','39282_17','39282_2137','39285_2127','39285_7','39286_2127','39286_7','39287_2127','39287_7','39288_2127','39288_7','39289_2127','39296_2127','39299_2124','39300_2129','39302_2123','39308_8','39311_2127','39318_2129','75044_2128','75046_2123','75136_2124','75173_2128','75174_2129','75175_2140','75176_2140','75178_2128','75179_2128')
AND "hobo_id" = 211
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 17:12:27 --> [LedgerCondensed][acc=12765] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 17:12:27 --> [LedgerCondensed][acc=12765] Q1-VoucherCount => 0  |  0.0012s
1 - 2026-08-11 17:12:27 --> [LedgerCondensed][acc=12765] Q2-OpeningBalance => -109776.00  |  0.0003s
1 - 2026-08-11 17:12:27 --> [LedgerCondensed][acc=12765] Q3-MainLedger => rows=18  total=18  |  0.0149s
1 - 2026-08-11 17:12:27 --> [LedgerCondensed][acc=12765] Q4-OtherAccounts => 18 rows  |  0.0054s
1 - 2026-08-11 17:12:27 --> [LedgerCondensed][acc=12765] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  5.5%)
   Q2-OpeningBalance           0.0003s   (  1.5%)
   Q3-MainLedger               0.0149s   ( 66.7%)
   Q4-OtherAccounts            0.0054s   ( 24.1%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0223s   (100%)
[LedgerCondensed][acc=12765] ── FUNCTION END ──
1 - 2026-08-11 17:12:44 --> [LedgerCondensed][acc=12765] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=1  compId=  boId=  fyId=
1 - 2026-08-11 17:12:44 --> [LedgerCondensed][acc=12765] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-08-11 17:12:44 --> [LedgerCondensed][acc=12765] Q2-OpeningBalance => -109776.00  |  0.0001s
1 - 2026-08-11 17:12:44 --> [LedgerCondensed][acc=12765] Q3-MainLedger => rows=18  total=18  |  0.0108s
1 - 2026-08-11 17:12:44 --> [LedgerCondensed][acc=12765] Q4-OtherAccounts => 18 rows  |  0.0033s
1 - 2026-08-11 17:12:44 --> [LedgerCondensed][acc=12765] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  5.8%)
   Q2-OpeningBalance           0.0001s   (  1.0%)
   Q3-MainLedger               0.0108s   ( 69.1%)
   Q4-OtherAccounts            0.0033s   ( 21.0%)
   BuildRecords                0.0001s   (  0.6%)
   TOTAL                       0.0156s   (100%)
[LedgerCondensed][acc=12765] ── FUNCTION END ──
1 - 2026-08-11 17:17:05 --> [LedgerCondensed][acc=12680] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-11 17:17:05 --> [LedgerCondensed][acc=12680] Q1-VoucherCount => 0  |  0.001s
1 - 2026-08-11 17:17:05 --> [LedgerCondensed][acc=12680] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-11 17:17:05 --> [LedgerCondensed][acc=12680] Q3-MainLedger => rows=12  total=12  |  0.0118s
1 - 2026-08-11 17:17:05 --> [LedgerCondensed][acc=12680] Q4-OtherAccounts => 12 rows  |  0.0025s
1 - 2026-08-11 17:17:05 --> [LedgerCondensed][acc=12680] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  5.9%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0118s   ( 72.6%)
   Q4-OtherAccounts            0.0025s   ( 15.5%)
   BuildRecords                0.0001s   (  0.4%)
   TOTAL                       0.0163s   (100%)
[LedgerCondensed][acc=12680] ── FUNCTION END ──
1 - 2026-08-11 18:09:04 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:09:04 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:09:16 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:09:16 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:20:31 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:20:31 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:20:31 --> Profit loss id: -> 11485
1 - 2026-08-11 18:20:40 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:20:40 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:22:54 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:22:54 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:26:25 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:26:25 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:26:25 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:26:25 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:26:35 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:26:35 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:26:35 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:26:35 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:26:38 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:26:38 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:26:38 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:26:38 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:27:07 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:27:07 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11436 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11436 P&L Appropriation nextFY opening INSERTED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11436 acc_name="Capital Account" parent_id=1 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11437 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11437 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11437 acc_name="Cash In Hand" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=28556.66 movement=-392 closing_to_next_fy=28164.66 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11438 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11438 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11438 acc_name="HO" parent_id=14 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11439 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11439 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11439 acc_name="Sales Account" | skipped due to restricted parent_id=8
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11440 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11440 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11440 acc_name="Purchase Account" | skipped due to restricted parent_id=7
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11441 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11441 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11441 acc_name="GST PAID A/C" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11442 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11442 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11442 acc_name="PARTAP KATARIA CAPITAL A/C" parent_id=1 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-1190123.57 movement=821936.46 closing_to_next_fy=-368187.11 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11443 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11443 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11443 acc_name="ACTIVA" parent_id=3 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=17509.79 movement=-2626.35 closing_to_next_fy=14883.44 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11444 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11444 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11444 acc_name="AIR CONDITIONER" parent_id=3 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=19961.95 movement=-2994.3 closing_to_next_fy=16967.65 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11445 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11445 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11445 acc_name="COMPUTER DATA AND PROCESSING UNIT" parent_id=3 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=1191.6 movement=-476.8 closing_to_next_fy=714.8 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11446 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11446 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11446 acc_name="FURNITURE & FITTINGS" parent_id=3 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=61117.74 movement=-6111.8 closing_to_next_fy=55005.94 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11447 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11447 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11447 acc_name="OFFICE EQUIPMENT" parent_id=3 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=11759.41 movement=-1764 closing_to_next_fy=9995.41 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11448 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11448 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11448 acc_name="DEPRECIATION A/C" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11449 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11449 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11449 acc_name="SBI CA A/C NO. 3374" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=5156.39 movement=24293.97 closing_to_next_fy=29450.36 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11450 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11450 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11450 acc_name="SUSPENSE A/C" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11451 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11451 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11451 acc_name="Sharda Traders" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11452 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11452 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11452 acc_name="INTERNET EXPENSE" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11453 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11453 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11453 acc_name="BANK CHARGES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11454 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11454 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11454 acc_name="BANK INTEREST" | skipped due to restricted parent_id=12
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11455 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11455 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11455 acc_name="REFRESHMENT EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11456 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11456 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11456 acc_name="PARTAP KATARIA UNSECURED LOAN A/C" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11457 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11457 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11457 acc_name="ELECTRICITY EXPENSE" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11458 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11458 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11458 acc_name="ENTERTAINMENT EXPENSE" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11459 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11459 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11459 acc_name="TRAVELLING EXPENSE" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11460 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11460 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11460 acc_name="PETROL & DIESEL EXPENSE" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11461 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11461 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11461 acc_name="BIMALJOT KAUR" parent_id=2 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-555644 movement=0 closing_to_next_fy=-555644 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11462 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11462 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11462 acc_name="RAMA KATARIA" parent_id=2 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-39570 movement=0 closing_to_next_fy=-39570 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11463 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11463 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11463 acc_name="SHARDA TRADERS ( CHAND KATARIA )" parent_id=2 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-675610 movement=-75000 closing_to_next_fy=-750610 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11464 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11464 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11464 acc_name="AUDIT EXPENSE PAYABLE" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-24000 movement=9000 closing_to_next_fy=-15000 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11465 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11465 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11465 acc_name="SALARY & BONUS PAYABLE" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-25400 movement=-1320 closing_to_next_fy=-26720 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11466 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11466 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11466 acc_name="ADVERTISEMENT EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11467 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11467 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11467 acc_name="COLD STORAGE EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11468 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11468 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11468 acc_name="CONSUMABLES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11469 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11469 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11469 acc_name="CONVEYNACE EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11470 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11470 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11470 acc_name="AUDIT FEES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11471 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11471 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11471 acc_name="FESTIVAL EXPENSE" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11472 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11472 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11472 acc_name="LABOUR EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11473 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11473 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11473 acc_name="LEGAL EXPENSE" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11474 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11474 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11474 acc_name="MISC. EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11475 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11475 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11475 acc_name="POSTAGE EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11476 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11476 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11476 acc_name="PRINTING & STATIONARY EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11477 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11477 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11477 acc_name="REPAIR & MAINTANEANCE EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11478 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11478 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11478 acc_name="SALE PROMOTION EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11479 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11479 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11479 acc_name="STAFF WELFARE EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11480 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11480 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11480 acc_name="TELEPHONE EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11481 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11481 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11481 acc_name="SALARY EXPENSES" | skipped due to restricted parent_id=13
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11482 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11482 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11482 acc_name="FRIEGHT EXPENSES" parent_id=11 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=28858.03 closing_to_next_fy=28858.03 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11483 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11483 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11483 acc_name="SALES COMMISSION BY ARTIYA" | skipped due to restricted parent_id=12
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11484 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11484 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11484 acc_name="MIS. INCOME" | skipped due to restricted parent_id=12
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11485 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11485 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11486 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11486 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11486 acc_name="Round Off (+)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11487 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11487 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11487 acc_name="Round Off (-)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11488 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11488 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11488 acc_name="Discount (-)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11489 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11489 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11489 acc_name="Taxable Sundry" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11490 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11490 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11490 acc_name="Central Tax (CGST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11491 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11491 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11491 acc_name="State Tax (SGST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11492 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11492 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11492 acc_name="Integrated Tax (IGST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11493 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11493 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11493 acc_name="Cess (GST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11494 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11494 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11494 acc_name="TCS (IT)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11495 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11495 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11495 acc_name="TDS (IT)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11496 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11496 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11496 acc_name="TCS (GST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11497 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11497 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11497 acc_name="TDS (GST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11498 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=11485 openingPL=-611176.97 netMovePL=0 closingPL=-611176.97 netProfitLoss=764545.18 carryForwardPL=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11498 P&L Appropriation nextFY opening UPDATED | plAccId=11485 bal=153368.21
1 - 2026-08-11 18:27:08 --> [FY MIGRATION] cmp_id=136 hobo_id=197 hobo_name="HO" curr_fy=212 next_fy=350 acc_id=11498 acc_name="UT Tax (UGST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-08-11 18:27:10 --> ITEM BAL ROLLOVER START | item_id=20422 | cmp_id=136 | curr_fy=212 | next_fy=350

1 - 2026-08-11 18:27:10 --> ITEM BAL ROLLOVER units found: [{"itm_id_unit_id":"20422_11"}]

1 - 2026-08-11 18:27:10 --> UNIT BAL START | itm_id_unit_id=20422_11

1 - 2026-08-11 18:27:10 --> UNIT BAL openings | itm_id_unit_id=20422_11 | {"130":1}

1 - 2026-08-11 18:27:10 --> UNIT BAL movements | itm_id_unit_id=20422_11 | []

1 - 2026-08-11 18:27:10 --> UNIT BAL INSERT | itm_id_unit_id=20422_11 | center=130 | opening=1 | net=0 | closing=1 | next_fy=350

1 - 2026-08-11 18:27:10 --> UNIT BAL DONE | itm_id_unit_id=20422_11

1 - 2026-08-11 18:27:10 --> ITEM BAL ROLLOVER SUCCESS | item_id=20422 | next_fy=350

1 - 2026-08-11 18:27:13 --> ITEM VAL ROLLOVER START | item_id=20422 | cmp_id=136 | curr_fy=212 | next_fy=350

1 - 2026-08-11 18:27:13 --> ITEM VAL ROLLOVER units found: [{"itm_id_unit_id":"20422_11"}]

1 - 2026-08-11 18:27:13 --> UNIT VAL START | itm_id_unit_id=20422_11

1 - 2026-08-11 18:27:13 --> UNIT VAL opening | 20422_11 | opQty=1 | opVal=0 | opRate=0

1 - 2026-08-11 18:27:13 --> UNIT VAL RESULT | 20422_11 | closingVal=0 | closingQty=1

1 - 2026-08-11 18:27:13 --> UNIT VAL DELETED old rows | 20422_11 | next_fy=350

1 - 2026-08-11 18:27:13 --> UNIT VAL INSERT | 20422_11 | center=0 | val=0 | method=AVG | next_fy=350

1 - 2026-08-11 18:27:13 --> UNIT VAL DONE | 20422_11

1 - 2026-08-11 18:27:13 --> ITEM VAL ROLLOVER SUCCESS | item_id=20422 | next_fy=350

1 - 2026-08-11 18:27:19 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:27:19 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:27:19 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:27:19 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:27:24 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:27:24 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:27:24 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:27:24 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:27:30 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-11 18:27:30 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 350
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
