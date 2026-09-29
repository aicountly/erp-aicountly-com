<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-08-13 09:55:31 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 185
AND "cmp_id" = 126
AND "itm_id_unit_id" IN ('19194_1540','19194_8','19195_8','19196_8','19197_8','19198_1547','19198_8','19199_8','19200_10','19200_1542','19200_17','19200_8','19201_1540','19201_1542','19201_1549','19202_3','19202_8','19203_8','19204_10','19204_1542','19204_1549','19204_17','19204_8','19205_10','19205_1540','19205_1542','19205_1549','19205_17','19205_8','19206_1535','19206_1542','19206_3','19206_8','19207_10','19207_1542','19207_8','19209_17','19209_8','19210_10','19210_1542','19210_8','19211_3','19211_8','19212_1535','19212_3','19212_6','19212_8','19213_6','19213_8','19214_3','19214_8','19215_1535','19215_3','19215_8','19216_1540','19216_8','19217_3','19217_8','19218_6','19218_8','19219_8','19220_8','19221_8','19222_8','19223_1540','19224_8','19225_1540','19225_8','19226_1540','19226_1549','19226_17','19226_1825','19226_8','19227_8','19228_8','19229_1540','19229_8','19230_1540','19230_8','19231_1540','19231_1550','19231_18','19231_8','19232_8','19233_8','19234_8','19235_8','19236_1535','19236_1542','19236_3','19236_8','19237_8','19238_1535','19238_3','19238_8','19240_1535','19240_3','19240_8','19241_8','19242_1535','19242_4','19242_8','19243_1535','19243_3','19243_8','19245_1538','19245_1540','19245_6','19245_8','19246_1535','19246_3','19246_8','19247_8','19248_3','19248_8','19249_8','19250_15','19250_8','19251_1535','19251_3','19251_8','19252_8','19253_6','19253_8','19254_8','19255_6','19255_8','19256_8','19257_10','19257_8','19258_8','19260_1550','19260_18','19260_6','19260_8','19261_15','19261_8','19262_8','19263_8','19264_8','19265_1550','19265_18','19265_8','19266_8','19267_8','19268_3','19268_8','19269_1550','19269_18','19269_8','19270_5','19270_8','19272_8','19273_5','19273_8','19274_10','19274_1535','19274_1542','19274_3','19274_4','19274_8','19275_5','19275_8','19276_8','19277_8','19278_17','19278_8','19279_5','19279_8','19280_8','19281_8','19282_6','19282_8','19283_8','19284_1550','19284_18','19284_8','19285_8','19286_8','19287_8','19288_8','19289_8','19290_8','19291_8','19292_8','19293_8','19294_8','19295_1535','19295_17','19295_3','19295_8','19296_1535','19296_3','19296_8','19297_1535','19297_3','19297_8','19298_5','19298_8','19299_15','19299_1540','19299_1547','19299_8','19300_1540','19300_8','19301_8','19302_8','19303_1535','19303_1540','19303_3','19303_8','19304_1540','19304_8','19305_1538','19305_1550','19305_18','19305_6','19306_8','19307_8','19308_1540','19308_8','19309_3','19310_1549','19310_17','19311_8','19312_8','19313_18','19314_5','19315_1535','19315_3','19316_10','19316_1542','19317_1543','20056_1825','40038_1540')
AND "hobo_id" = 167
GROUP BY "itm_id_unit_id"
1 - 2026-08-13 09:55:31 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 185
AND "cmp_id" = 126
AND "itm_id_unit_id" IN ('19194_1540','19194_8','19195_8','19196_8','19197_8','19198_1547','19198_8','19199_8','19200_10','19200_1542','19200_17','19200_8','19201_1540','19201_1542','19201_1549','19202_3','19202_8','19203_8','19204_10','19204_1542','19204_1549','19204_17','19204_8','19205_10','19205_1540','19205_1542','19205_1549','19205_17','19205_8','19206_1535','19206_1542','19206_3','19206_8','19207_10','19207_1542','19207_8','19209_17','19209_8','19210_10','19210_1542','19210_8','19211_3','19211_8','19212_1535','19212_3','19212_6','19212_8','19213_6','19213_8','19214_3','19214_8','19215_1535','19215_3','19215_8','19216_1540','19216_8','19217_3','19217_8','19218_6','19218_8','19219_8','19220_8','19221_8','19222_8','19223_1540','19224_8','19225_1540','19225_8','19226_1540','19226_1549','19226_17','19226_1825','19226_8','19227_8','19228_8','19229_1540','19229_8','19230_1540','19230_8','19231_1540','19231_1550','19231_18','19231_8','19232_8','19233_8','19234_8','19235_8','19236_1535','19236_1542','19236_3','19236_8','19237_8','19238_1535','19238_3','19238_8','19240_1535','19240_3','19240_8','19241_8','19242_1535','19242_4','19242_8','19243_1535','19243_3','19243_8','19245_1538','19245_1540','19245_6','19245_8','19246_1535','19246_3','19246_8','19247_8','19248_3','19248_8','19249_8','19250_15','19250_8','19251_1535','19251_3','19251_8','19252_8','19253_6','19253_8','19254_8','19255_6','19255_8','19256_8','19257_10','19257_8','19258_8','19260_1550','19260_18','19260_6','19260_8','19261_15','19261_8','19262_8','19263_8','19264_8','19265_1550','19265_18','19265_8','19266_8','19267_8','19268_3','19268_8','19269_1550','19269_18','19269_8','19270_5','19270_8','19272_8','19273_5','19273_8','19274_10','19274_1535','19274_1542','19274_3','19274_4','19274_8','19275_5','19275_8','19276_8','19277_8','19278_17','19278_8','19279_5','19279_8','19280_8','19281_8','19282_6','19282_8','19283_8','19284_1550','19284_18','19284_8','19285_8','19286_8','19287_8','19288_8','19289_8','19290_8','19291_8','19292_8','19293_8','19294_8','19295_1535','19295_17','19295_3','19295_8','19296_1535','19296_3','19296_8','19297_1535','19297_3','19297_8','19298_5','19298_8','19299_15','19299_1540','19299_1547','19299_8','19300_1540','19300_8','19301_8','19302_8','19303_1535','19303_1540','19303_3','19303_8','19304_1540','19304_8','19305_1538','19305_1550','19305_18','19305_6','19306_8','19307_8','19308_1540','19308_8','19309_3','19310_1549','19310_17','19311_8','19312_8','19313_18','19314_5','19315_1535','19315_3','19316_10','19316_1542','19317_1543','20056_1825','40038_1540')
AND "hobo_id" = 167
GROUP BY "itm_id_unit_id"
1 - 2026-08-13 09:55:34 --> [LedgerCondensed][acc=7803] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-13 09:55:34 --> [LedgerCondensed][acc=7803] Q1-VoucherCount => 0  |  0.0014s
1 - 2026-08-13 09:55:34 --> [LedgerCondensed][acc=7803] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-13 09:55:34 --> [LedgerCondensed][acc=7803] Q3-MainLedger => rows=100  total=141  |  0.015s
1 - 2026-08-13 09:55:34 --> [LedgerCondensed][acc=7803] Q4-OtherAccounts => 100 rows  |  0.0143s
1 - 2026-08-13 09:55:34 --> [LedgerCondensed][acc=7803] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0014s   (  4.4%)
   Q2-OpeningBalance           0.0003s   (  1.1%)
   Q3-MainLedger               0.0150s   ( 47.2%)
   Q4-OtherAccounts            0.0143s   ( 44.8%)
   BuildRecords                0.0004s   (  1.1%)
   TOTAL                       0.0318s   (100%)
[LedgerCondensed][acc=7803] ── FUNCTION END ──
1 - 2026-08-13 10:59:19 --> [LedgerCondensed][acc=7803] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-13 10:59:19 --> [LedgerCondensed][acc=7803] Q1-VoucherCount => 0  |  0.0012s
1 - 2026-08-13 10:59:19 --> [LedgerCondensed][acc=7803] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-13 10:59:19 --> [LedgerCondensed][acc=7803] Q3-MainLedger => rows=100  total=141  |  0.0128s
1 - 2026-08-13 10:59:19 --> [LedgerCondensed][acc=7803] Q4-OtherAccounts => 100 rows  |  0.0049s
1 - 2026-08-13 10:59:19 --> [LedgerCondensed][acc=7803] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  6.0%)
   Q2-OpeningBalance           0.0003s   (  1.6%)
   Q3-MainLedger               0.0128s   ( 63.4%)
   Q4-OtherAccounts            0.0049s   ( 24.2%)
   BuildRecords                0.0004s   (  2.1%)
   TOTAL                       0.0202s   (100%)
[LedgerCondensed][acc=7803] ── FUNCTION END ──
1 - 2026-08-13 12:19:22 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-13 12:19:22 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301594, Bill Sundry ID: 10188
1 - 2026-08-13 12:19:22 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-08-10","acc_txn_dr_cr":2,"acc_txn_amt":1306.859999999999899955582804977893829345703125,"acc_txn_fcy":0,"vch_txn_id":301594,"txn_id":1022854,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-13 12:19:22 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-13 12:19:22 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301594, Bill Sundry ID: 10189
1 - 2026-08-13 12:19:22 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-08-10","acc_txn_dr_cr":2,"acc_txn_amt":1306.859999999999899955582804977893829345703125,"acc_txn_fcy":0,"vch_txn_id":301594,"txn_id":1022855,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-13 12:20:16 --> [LedgerCondensed][acc=10344] ── FUNCTION START ── from=2026-08-01  to=2026-08-10  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-13 12:20:16 --> [LedgerCondensed][acc=10344] Q1-VoucherCount => 17  |  0.0018s
1 - 2026-08-13 12:20:16 --> [LedgerCondensed][acc=10344] Q2-OpeningBalance => 978797.99  |  0.001s
1 - 2026-08-13 12:20:16 --> [LedgerCondensed][acc=10344] Q3-MainLedger => rows=1  total=1  |  0.0119s
1 - 2026-08-13 12:20:16 --> [LedgerCondensed][acc=10344] Q4-OtherAccounts => 1 rows  |  0.0013s
1 - 2026-08-13 12:20:16 --> [LedgerCondensed][acc=10344] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0018s   ( 11.0%)
   Q2-OpeningBalance           0.0010s   (  6.2%)
   Q3-MainLedger               0.0119s   ( 72.3%)
   Q4-OtherAccounts            0.0013s   (  7.7%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0164s   (100%)
[LedgerCondensed][acc=10344] ── FUNCTION END ──
1 - 2026-08-13 12:45:51 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-13 12:45:51 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301595, Bill Sundry ID: 10188
1 - 2026-08-13 12:45:51 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-08-10","acc_txn_dr_cr":2,"acc_txn_amt":861.1799999999999499777914024889469146728515625,"acc_txn_fcy":0,"vch_txn_id":301595,"txn_id":1022877,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-13 12:45:51 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-13 12:45:51 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301595, Bill Sundry ID: 10189
1 - 2026-08-13 12:45:51 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-08-10","acc_txn_dr_cr":2,"acc_txn_amt":861.1799999999999499777914024889469146728515625,"acc_txn_fcy":0,"vch_txn_id":301595,"txn_id":1022878,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-13 12:47:28 --> [LedgerCondensed][acc=10029] ── FUNCTION START ── from=2026-08-01  to=2026-08-10  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-13 12:47:28 --> [LedgerCondensed][acc=10029] Q1-VoucherCount => 7  |  0.0016s
1 - 2026-08-13 12:47:28 --> [LedgerCondensed][acc=10029] Q2-OpeningBalance => 269519.19  |  0.0009s
1 - 2026-08-13 12:47:28 --> [LedgerCondensed][acc=10029] Q3-MainLedger => rows=1  total=1  |  0.0123s
1 - 2026-08-13 12:47:28 --> [LedgerCondensed][acc=10029] Q4-OtherAccounts => 1 rows  |  0.0014s
1 - 2026-08-13 12:47:28 --> [LedgerCondensed][acc=10029] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0016s   (  9.7%)
   Q2-OpeningBalance           0.0009s   (  5.4%)
   Q3-MainLedger               0.0123s   ( 73.7%)
   Q4-OtherAccounts            0.0014s   (  8.3%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0167s   (100%)
[LedgerCondensed][acc=10029] ── FUNCTION END ──
1 - 2026-08-13 12:48:09 --> [LedgerCondensed][acc=10029] ── FUNCTION START ── from=2026-08-01  to=2026-08-10  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-13 12:48:09 --> [LedgerCondensed][acc=10029] Q1-VoucherCount => 7  |  0.0015s
1 - 2026-08-13 12:48:09 --> [LedgerCondensed][acc=10029] Q2-OpeningBalance => 269519.19  |  0.0009s
1 - 2026-08-13 12:48:09 --> [LedgerCondensed][acc=10029] Q3-MainLedger => rows=1  total=1  |  0.0115s
1 - 2026-08-13 12:48:09 --> [LedgerCondensed][acc=10029] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-08-13 12:48:09 --> [LedgerCondensed][acc=10029] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0015s   (  9.8%)
   Q2-OpeningBalance           0.0009s   (  5.9%)
   Q3-MainLedger               0.0115s   ( 75.1%)
   Q4-OtherAccounts            0.0010s   (  6.4%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0153s   (100%)
[LedgerCondensed][acc=10029] ── FUNCTION END ──
1 - 2026-08-13 12:49:54 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 2
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-13 12:49:54 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301596, Bill Sundry ID: 10188
1 - 2026-08-13 12:49:54 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10188","acc_txn_date":"2026-08-06","acc_txn_dr_cr":2,"acc_txn_amt":68.75,"acc_txn_fcy":0,"vch_txn_id":301596,"txn_id":1022883,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-13 12:49:54 --> Getting Tax Account Entry->SELECT "acctmaster"."acc_id"
FROM "acctmaster"
JOIN "billsundry" ON "billsundry"."bsd_id"="acctmaster"."bsd_id"
WHERE "billsundry"."bsd_type" = 1
AND "billsundry"."bsd_input_output" = 2
AND "billsundry"."tax_cat_type" = 1
AND "billsundry"."tax_cat_sub_type" = 3
AND "acctmaster"."cmp_id" = '140'
1 - 2026-08-13 12:49:54 --> Company ID: 140, Voucher Txn ID (Tax Applied): 301596, Bill Sundry ID: 10189
1 - 2026-08-13 12:49:54 --> Saving tax account entry: {"cmp_id":"140","acc_id":"10189","acc_txn_date":"2026-08-06","acc_txn_dr_cr":2,"acc_txn_amt":68.75,"acc_txn_fcy":0,"vch_txn_id":301596,"txn_id":1022884,"hobo_id":"184","acc_txn_type":1}
1 - 2026-08-13 12:50:58 --> [LedgerCondensed][acc=10357] ── FUNCTION START ── from=2026-08-01  to=2026-08-06  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-13 12:50:58 --> [LedgerCondensed][acc=10357] Q1-VoucherCount => 12  |  0.0015s
1 - 2026-08-13 12:50:58 --> [LedgerCondensed][acc=10357] Q2-OpeningBalance => 650328.54  |  0.001s
1 - 2026-08-13 12:50:58 --> [LedgerCondensed][acc=10357] Q3-MainLedger => rows=1  total=1  |  0.0115s
1 - 2026-08-13 12:50:58 --> [LedgerCondensed][acc=10357] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-08-13 12:50:58 --> [LedgerCondensed][acc=10357] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0015s   (  9.8%)
   Q2-OpeningBalance           0.0010s   (  6.4%)
   Q3-MainLedger               0.0115s   ( 74.8%)
   Q4-OtherAccounts            0.0010s   (  6.4%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0154s   (100%)
[LedgerCondensed][acc=10357] ── FUNCTION END ──
1 - 2026-08-13 15:28:07 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-13 15:28:07 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-13 15:28:08 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-13 15:28:08 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-13 15:28:08 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-13 15:28:08 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 212
AND "cmp_id" = 136
AND "itm_id_unit_id" IN ('20422_11')
AND "hobo_id" = 197
GROUP BY "itm_id_unit_id"
1 - 2026-08-13 15:28:18 --> [LedgerCondensed][acc=11449] ── FUNCTION START ── from=2024-04-01  to=2024-04-30  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-13 15:28:18 --> [LedgerCondensed][acc=11449] Q1-VoucherCount => 0  |  0.0013s
1 - 2026-08-13 15:28:18 --> [LedgerCondensed][acc=11449] Q2-OpeningBalance => 5156.39  |  0.0003s
1 - 2026-08-13 15:28:18 --> [LedgerCondensed][acc=11449] Q3-MainLedger => rows=9  total=9  |  0.0623s
1 - 2026-08-13 15:28:18 --> [LedgerCondensed][acc=11449] Q4-OtherAccounts => 9 rows  |  0.0031s
1 - 2026-08-13 15:28:18 --> [LedgerCondensed][acc=11449] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0013s   (  1.9%)
   Q2-OpeningBalance           0.0003s   (  0.4%)
   Q3-MainLedger               0.0623s   ( 92.4%)
   Q4-OtherAccounts            0.0031s   (  4.6%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0675s   (100%)
[LedgerCondensed][acc=11449] ── FUNCTION END ──
1 - 2026-08-13 15:28:26 --> [LedgerCondensed][acc=11449] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-13 15:28:26 --> [LedgerCondensed][acc=11449] Q1-VoucherCount => 0  |  0.001s
1 - 2026-08-13 15:28:26 --> [LedgerCondensed][acc=11449] Q2-OpeningBalance => 5156.39  |  0.0003s
1 - 2026-08-13 15:28:29 --> [LedgerCondensed][acc=11449] Q3-MainLedger => rows=100  total=499  |  3.0133s
1 - 2026-08-13 15:28:29 --> [LedgerCondensed][acc=11449] Q4-OtherAccounts => 100 rows  |  0.0036s
1 - 2026-08-13 15:28:29 --> [LedgerCondensed][acc=11449] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  0.0%)
   Q2-OpeningBalance           0.0003s   (  0.0%)
   Q3-MainLedger               3.0133s   ( 99.8%)
   Q4-OtherAccounts            0.0036s   (  0.1%)
   BuildRecords                0.0004s   (  0.0%)
   TOTAL                       3.0190s   (100%)
[LedgerCondensed][acc=11449] ── FUNCTION END ──
1 - 2026-08-13 15:28:58 --> [LedgerCondensed][acc=11449] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-13 15:28:58 --> [LedgerCondensed][acc=11449] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-08-13 15:28:58 --> [LedgerCondensed][acc=11449] Q2-OpeningBalance => 5156.39  |  0.0003s
1 - 2026-08-13 15:29:01 --> [LedgerCondensed][acc=11449] Q3-MainLedger => rows=99  total=499  |  3.0296s
1 - 2026-08-13 15:29:01 --> [LedgerCondensed][acc=11449] Q4-OtherAccounts => 99 rows  |  0.0035s
1 - 2026-08-13 15:29:01 --> [LedgerCondensed][acc=11449] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  0.0%)
   Q2-OpeningBalance           0.0003s   (  0.0%)
   Q3-MainLedger               3.0296s   ( 99.8%)
   Q4-OtherAccounts            0.0035s   (  0.1%)
   BuildRecords                0.0004s   (  0.0%)
   TOTAL                       3.0354s   (100%)
[LedgerCondensed][acc=11449] ── FUNCTION END ──
1 - 2026-08-13 15:29:36 --> [LedgerCondensed][acc=11449] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-13 15:29:36 --> [LedgerCondensed][acc=11449] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-08-13 15:29:36 --> [LedgerCondensed][acc=11449] Q2-OpeningBalance => 5156.39  |  0.0003s
1 - 2026-08-13 15:29:39 --> [LedgerCondensed][acc=11449] Q3-MainLedger => rows=100  total=499  |  3.0138s
1 - 2026-08-13 15:29:39 --> [LedgerCondensed][acc=11449] Q4-OtherAccounts => 100 rows  |  0.003s
1 - 2026-08-13 15:29:39 --> [LedgerCondensed][acc=11449] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  0.0%)
   Q2-OpeningBalance           0.0003s   (  0.0%)
   Q3-MainLedger               3.0138s   ( 99.8%)
   Q4-OtherAccounts            0.0030s   (  0.1%)
   BuildRecords                0.0004s   (  0.0%)
   TOTAL                       3.0190s   (100%)
[LedgerCondensed][acc=11449] ── FUNCTION END ──
1 - 2026-08-13 16:23:28 --> [LedgerCondensed][acc=11470] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-13 16:23:28 --> [LedgerCondensed][acc=11470] Q1-VoucherCount => 0  |  0.0013s
1 - 2026-08-13 16:23:28 --> [LedgerCondensed][acc=11470] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-13 16:23:28 --> [LedgerCondensed][acc=11470] Q3-MainLedger => rows=1  total=1  |  0.0108s
1 - 2026-08-13 16:23:28 --> [LedgerCondensed][acc=11470] Q4-OtherAccounts => 1 rows  |  0.0011s
1 - 2026-08-13 16:23:28 --> [LedgerCondensed][acc=11470] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0013s   (  9.1%)
   Q2-OpeningBalance           0.0003s   (  1.9%)
   Q3-MainLedger               0.0108s   ( 77.9%)
   Q4-OtherAccounts            0.0011s   (  8.1%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0138s   (100%)
[LedgerCondensed][acc=11470] ── FUNCTION END ──
1 - 2026-08-13 16:23:33 --> [LedgerCondensed][acc=11470] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-13 16:23:33 --> [LedgerCondensed][acc=11470] Q1-VoucherCount => 0  |  0.001s
1 - 2026-08-13 16:23:33 --> [LedgerCondensed][acc=11470] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-08-13 16:23:33 --> [LedgerCondensed][acc=11470] Q3-MainLedger => rows=1  total=1  |  0.0091s
1 - 2026-08-13 16:23:33 --> [LedgerCondensed][acc=11470] Q4-OtherAccounts => 1 rows  |  0.0014s
1 - 2026-08-13 16:23:33 --> [LedgerCondensed][acc=11470] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.3%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0091s   ( 75.0%)
   Q4-OtherAccounts            0.0014s   ( 11.2%)
   BuildRecords                0.0000s   (  0.3%)
   TOTAL                       0.0122s   (100%)
[LedgerCondensed][acc=11470] ── FUNCTION END ──
1 - 2026-08-13 16:23:59 --> [LedgerCondensed][acc=11463] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-08-13 16:23:59 --> [LedgerCondensed][acc=11463] Q1-VoucherCount => 0  |  0.001s
1 - 2026-08-13 16:23:59 --> [LedgerCondensed][acc=11463] Q2-OpeningBalance => -675610.00  |  0.0003s
1 - 2026-08-13 16:23:59 --> [LedgerCondensed][acc=11463] Q3-MainLedger => rows=1  total=1  |  0.0094s
1 - 2026-08-13 16:23:59 --> [LedgerCondensed][acc=11463] Q4-OtherAccounts => 1 rows  |  0.0011s
1 - 2026-08-13 16:23:59 --> [LedgerCondensed][acc=11463] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  7.9%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0094s   ( 76.8%)
   Q4-OtherAccounts            0.0011s   (  9.1%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0123s   (100%)
[LedgerCondensed][acc=11463] ── FUNCTION END ──
