<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

1 - 2026-09-19 10:54:44 --> [LedgerCondensed][acc=10866] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 10:54:44 --> [LedgerCondensed][acc=10866] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-19 10:54:44 --> [LedgerCondensed][acc=10866] Q2-OpeningBalance => 0  |  0.0003s
1 - 2026-09-19 10:54:44 --> [LedgerCondensed][acc=10866] Q3-MainLedger => rows=21  total=21  |  0.1337s
1 - 2026-09-19 10:54:44 --> [LedgerCondensed][acc=10866] Q4-OtherAccounts => 21 rows  |  0.0019s
1 - 2026-09-19 10:54:44 --> [LedgerCondensed][acc=10866] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  0.7%)
   Q2-OpeningBalance           0.0003s   (  0.2%)
   Q3-MainLedger               0.1337s   ( 97.3%)
   Q4-OtherAccounts            0.0019s   (  1.4%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.1375s   (100%)
[LedgerCondensed][acc=10866] ── FUNCTION END ──
1 - 2026-09-19 10:58:08 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 10:58:08 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-19 10:58:08 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => -3470.00  |  0.0002s
1 - 2026-09-19 10:58:08 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0091s
1 - 2026-09-19 10:58:08 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-19 10:58:08 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  8.1%)
   Q2-OpeningBalance           0.0002s   (  2.1%)
   Q3-MainLedger               0.0091s   ( 78.1%)
   Q4-OtherAccounts            0.0009s   (  8.1%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0116s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 10:59:21 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 10:59:21 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-19 10:59:21 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => -3470.00  |  0.0003s
1 - 2026-09-19 10:59:21 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0094s
1 - 2026-09-19 10:59:21 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.0008s
1 - 2026-09-19 10:59:21 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  8.0%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0094s   ( 79.3%)
   Q4-OtherAccounts            0.0008s   (  7.2%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0118s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 11:00:49 --> [LedgerCondensed][acc=10866] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 11:00:49 --> [LedgerCondensed][acc=10866] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-19 11:00:49 --> [LedgerCondensed][acc=10866] Q2-OpeningBalance => 0  |  0.0002s
1 - 2026-09-19 11:00:49 --> [LedgerCondensed][acc=10866] Q3-MainLedger => rows=21  total=21  |  0.1258s
1 - 2026-09-19 11:00:49 --> [LedgerCondensed][acc=10866] Q4-OtherAccounts => 21 rows  |  0.0016s
1 - 2026-09-19 11:00:49 --> [LedgerCondensed][acc=10866] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  0.7%)
   Q2-OpeningBalance           0.0002s   (  0.2%)
   Q3-MainLedger               0.1258s   ( 97.4%)
   Q4-OtherAccounts            0.0016s   (  1.2%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.1291s   (100%)
[LedgerCondensed][acc=10866] ── FUNCTION END ──
1 - 2026-09-19 11:08:09 --> 1. Check if current user owns the company
1 - 2026-09-19 11:08:09 --> SELECT *
FROM "cmpacsmstr"
WHERE "cmp_id" = '89'
AND "uuid_acs_type" = 1
AND "uuid_aictly_by" = '7'
1 - 2026-09-19 11:08:09 --> 2. Prevent sharing to self
1 - 2026-09-19 11:08:09 --> 3. Check if company already shared to this user
1 - 2026-09-19 11:08:09 --> SELECT *
FROM "cmpacsmstr"
WHERE "cmp_id" = '89'
AND "uuid_acs_type" = 0
AND "uuid_aictly_acs" = '304'
AND "uuid_aictly_by" = '7'
1 - 2026-09-19 11:08:09 --> 4. Insert new share record
1 - 2026-09-19 11:08:09 --> INSERT INTO "cmpacsmstr" ("cmp_id", "uuid_acs_type", "uuid_aictly_acs", "uuid_aictly_by", "uuid_acs_datetime", "erp_acs_prof_id") VALUES ('89', 0, '304', '7', '2026-09-19 11:08:09', '97')
1 - 2026-09-19 11:08:09 --> Case: valid UUID => 406
1 - 2026-09-19 11:09:18 --> Profit loss id: -> 4801
1 - 2026-09-19 11:09:42 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4785 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:42 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4785 P&L Appropriation nextFY opening INSERTED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:42 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4785 acc_name="Capital Account" parent_id=0 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:42 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4786 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:42 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4786 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:42 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4786 acc_name="Cash In Hand" parent_id=5 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=100000 movement=0 closing_to_next_fy=100000 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:42 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4787 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:42 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4787 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:42 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4787 acc_name="HO" parent_id=14 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:42 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4788 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4788 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4788 acc_name="Sales Account" | skipped due to restricted parent_id=8
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4789 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4789 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4789 acc_name="Purchase Account" | skipped due to restricted parent_id=7
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4791 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4791 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4791 acc_name="Rahul B Gupta & Co.- CHD" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-3338.94 movement=3838.74 closing_to_next_fy=499.8 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4792 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4792 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4792 acc_name="Company Law Expenses" | skipped due to restricted parent_id=13
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4793 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4793 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4793 acc_name="Rahul B Gupta & Co.- Jal" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=-3237.76 closing_to_next_fy=-3237.76 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4794 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4794 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4794 acc_name="Suspense Account" parent_id=2 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4795 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4795 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4795 acc_name="Trade Era Filings LLP" parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=7200 movement=-4500 closing_to_next_fy=2700 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4796 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4796 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4796 acc_name="Professional Fees A/c." | skipped due to restricted parent_id=13
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4797 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4797 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4797 acc_name="Atul Madan Capital A/c." parent_id=1 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-50000 movement=0 closing_to_next_fy=-50000 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4798 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4798 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4798 acc_name="Mona Madan Capital A/c." parent_id=1 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-50000 movement=0 closing_to_next_fy=-50000 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4799 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4799 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4799 acc_name="Rahul Gupta Current A/c." parent_id=2 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4800 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4800 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4800 acc_name="Atul Madan Current A/c." parent_id=4 is_bsd=0 fy_range=2024-04-01 to 2025-03-31 opening=-13200 movement=-15506 closing_to_next_fy=-28706 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4801 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4801 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4802 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4802 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4802 acc_name="Round Off (+)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4803 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4803 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4803 acc_name="Round Off (-)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4804 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4804 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4804 acc_name="Discount (-)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4805 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4805 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4805 acc_name="Taxable Sundry" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4806 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4806 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4806 acc_name="Central Tax (CGST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4807 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4807 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4807 acc_name="State Tax (SGST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4808 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4808 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4808 acc_name="Integrated Tax (IGST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4809 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4809 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4809 acc_name="Cess (GST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4810 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4810 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4810 acc_name="TCS (IT)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4811 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4811 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4811 acc_name="TDS (IT)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4812 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4812 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4812 acc_name="TCS (GST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4813 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4813 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4813 acc_name="TDS (GST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4814 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4814 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=4814 acc_name="UT Tax (UGST)" parent_id=0 is_bsd=1 fy_range=2024-04-01 to 2025-03-31 opening=0 movement=0 closing_to_next_fy=0 memo_opening=0 memo_movement=0 memo_closing=0 action=INSERTED
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=6380 P&L Appropriation ensure-nextFY | plExistsInCurr=1 plAccIdCurr=4801 openingPL=9338.94 netMovePL=0 closingPL=9338.94 netProfitLoss=-19405.02 carryForwardPL=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=6380 P&L Appropriation nextFY opening UPDATED | plAccId=4801 bal=-10066.08
1 - 2026-09-19 11:09:43 --> [FY MIGRATION] cmp_id=89 hobo_id=139 hobo_name="HO" curr_fy=135 next_fy=353 acc_id=6380 acc_name="GST PAID A/C" | skipped due to restricted parent_id=13
1 - 2026-09-19 11:10:07 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 11:10:07 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-19 11:10:07 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => -3470.00  |  0.0003s
1 - 2026-09-19 11:10:07 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.012s
1 - 2026-09-19 11:10:07 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-09-19 11:10:07 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.0%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0120s   ( 82.6%)
   Q4-OtherAccounts            0.0010s   (  7.1%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0146s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 11:10:17 --> [LedgerCondensed][acc=10928] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 11:10:17 --> [LedgerCondensed][acc=10928] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-19 11:10:17 --> [LedgerCondensed][acc=10928] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-19 11:10:17 --> [LedgerCondensed][acc=10928] Q3-MainLedger => rows=0  total=0  |  0.0015s
1 - 2026-09-19 11:10:17 --> [LedgerCondensed][acc=10928] Q4-OtherAccounts => SKIPPED (no voucher rows)
1 - 2026-09-19 11:10:17 --> [LedgerCondensed][acc=10928] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   ( 30.2%)
   Q2-OpeningBalance           0.0003s   (  8.4%)
   Q3-MainLedger               0.0015s   ( 50.8%)
   BuildRecords                0.0000s   (  0.0%)
   TOTAL                       0.0030s   (100%)
[LedgerCondensed][acc=10928] ── FUNCTION END ──
1 - 2026-09-19 11:10:45 --> [LedgerCondensed][acc=10922] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 11:10:45 --> [LedgerCondensed][acc=10922] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-19 11:10:45 --> [LedgerCondensed][acc=10922] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-19 11:10:45 --> [LedgerCondensed][acc=10922] Q3-MainLedger => rows=1  total=1  |  0.0116s
1 - 2026-09-19 11:10:45 --> [LedgerCondensed][acc=10922] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-19 11:10:45 --> [LedgerCondensed][acc=10922] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  7.4%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0116s   ( 81.1%)
   Q4-OtherAccounts            0.0009s   (  6.6%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0143s   (100%)
[LedgerCondensed][acc=10922] ── FUNCTION END ──
1 - 2026-09-19 11:10:56 --> [LedgerCondensed][acc=10922] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 11:10:56 --> [LedgerCondensed][acc=10922] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-19 11:10:56 --> [LedgerCondensed][acc=10922] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-19 11:10:56 --> [LedgerCondensed][acc=10922] Q3-MainLedger => rows=1  total=1  |  0.0095s
1 - 2026-09-19 11:10:56 --> [LedgerCondensed][acc=10922] Q4-OtherAccounts => 1 rows  |  0.0012s
1 - 2026-09-19 11:10:56 --> [LedgerCondensed][acc=10922] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.1%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0095s   ( 77.9%)
   Q4-OtherAccounts            0.0012s   (  9.5%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0122s   (100%)
[LedgerCondensed][acc=10922] ── FUNCTION END ──
1 - 2026-09-19 11:23:32 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 11:23:32 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-19 11:23:32 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => -3470.00  |  0.0003s
1 - 2026-09-19 11:23:32 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0124s
1 - 2026-09-19 11:23:32 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.0011s
1 - 2026-09-19 11:23:32 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  6.3%)
   Q2-OpeningBalance           0.0003s   (  1.8%)
   Q3-MainLedger               0.0124s   ( 81.7%)
   Q4-OtherAccounts            0.0011s   (  7.0%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0151s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 11:23:37 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 11:23:37 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-19 11:23:37 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => -3470.00  |  0.0003s
1 - 2026-09-19 11:23:37 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0088s
1 - 2026-09-19 11:23:37 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-09-19 11:23:37 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  7.0%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0088s   ( 78.5%)
   Q4-OtherAccounts            0.0010s   (  9.1%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0112s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 11:58:19 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 230
AND "cmp_id" = 145
AND "itm_id_unit_id" IN ('20213_5','20214_16','20215_16','20216_16','20217_16','20218_16','20219_5','20220_16','20221_5')
AND "hobo_id" = 189
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 11:58:19 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 230
AND "cmp_id" = 145
AND "itm_id_unit_id" IN ('20213_5','20214_16','20215_16','20216_16','20217_16','20218_16','20219_5','20220_16','20221_5')
AND "hobo_id" = 189
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 11:58:19 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 230
AND "cmp_id" = 145
AND "itm_id_unit_id" IN ('20213_5','20214_16','20215_16','20216_16','20217_16','20218_16','20219_5','20220_16','20221_5')
AND "hobo_id" = 189
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 11:58:19 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 230
AND "cmp_id" = 145
AND "itm_id_unit_id" IN ('20213_5','20214_16','20215_16','20216_16','20217_16','20218_16','20219_5','20220_16','20221_5')
AND "hobo_id" = 189
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 11:58:32 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 229
AND "cmp_id" = 145
AND "itm_id_unit_id" IN ('20213_5')
AND "hobo_id" = 189
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 11:58:32 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 229
AND "cmp_id" = 145
AND "itm_id_unit_id" IN ('20213_5')
AND "hobo_id" = 189
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 11:58:32 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 229
AND "cmp_id" = 145
AND "itm_id_unit_id" IN ('20213_5')
AND "hobo_id" = 189
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 11:58:32 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 229
AND "cmp_id" = 145
AND "itm_id_unit_id" IN ('20213_5')
AND "hobo_id" = 189
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 11:58:42 --> [LedgerCondensed][acc=10728] ── FUNCTION START ── from=2024-03-01  to=2024-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 11:58:42 --> [LedgerCondensed][acc=10728] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-19 11:58:42 --> [LedgerCondensed][acc=10728] Q2-OpeningBalance => 0  |  0.001s
1 - 2026-09-19 11:58:42 --> [LedgerCondensed][acc=10728] Q3-MainLedger => rows=7  total=7  |  0.0464s
1 - 2026-09-19 11:58:42 --> [LedgerCondensed][acc=10728] Q4-OtherAccounts => 7 rows  |  0.0026s
1 - 2026-09-19 11:58:42 --> [LedgerCondensed][acc=10728] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  1.6%)
   Q2-OpeningBalance           0.0010s   (  2.0%)
   Q3-MainLedger               0.0464s   ( 90.2%)
   Q4-OtherAccounts            0.0026s   (  5.1%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.0514s   (100%)
[LedgerCondensed][acc=10728] ── FUNCTION END ──
1 - 2026-09-19 11:58:46 --> [LedgerCondensed][acc=10728] ── FUNCTION START ── from=2023-04-01  to=2024-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 11:58:46 --> [LedgerCondensed][acc=10728] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-19 11:58:46 --> [LedgerCondensed][acc=10728] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-19 11:58:46 --> [LedgerCondensed][acc=10728] Q3-MainLedger => rows=7  total=7  |  0.0426s
1 - 2026-09-19 11:58:46 --> [LedgerCondensed][acc=10728] Q4-OtherAccounts => 7 rows  |  0.002s
1 - 2026-09-19 11:58:46 --> [LedgerCondensed][acc=10728] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  1.8%)
   Q2-OpeningBalance           0.0002s   (  0.5%)
   Q3-MainLedger               0.0426s   ( 92.2%)
   Q4-OtherAccounts            0.0020s   (  4.3%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0462s   (100%)
[LedgerCondensed][acc=10728] ── FUNCTION END ──
1 - 2026-09-19 12:00:16 --> [LedgerCondensed][acc=10728] ── FUNCTION START ── from=2023-04-01  to=2024-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 12:00:16 --> [LedgerCondensed][acc=10728] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-19 12:00:16 --> [LedgerCondensed][acc=10728] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-19 12:00:16 --> [LedgerCondensed][acc=10728] Q3-MainLedger => rows=7  total=7  |  0.0426s
1 - 2026-09-19 12:00:16 --> [LedgerCondensed][acc=10728] Q4-OtherAccounts => 7 rows  |  0.0026s
1 - 2026-09-19 12:00:16 --> [LedgerCondensed][acc=10728] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  2.0%)
   Q2-OpeningBalance           0.0002s   (  0.5%)
   Q3-MainLedger               0.0426s   ( 90.9%)
   Q4-OtherAccounts            0.0026s   (  5.6%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0468s   (100%)
[LedgerCondensed][acc=10728] ── FUNCTION END ──
1 - 2026-09-19 13:31:30 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 266
AND "cmp_id" = 164
AND "itm_id_unit_id" IN ('39483_7','39483_8','39484_8','39485_11','39485_9','39486_11','39486_8','39487_11','39487_8','39488_11','39488_8','39489_11','39489_8','39490_11','39490_8','39491_11','39491_5','39492_8','39493_8','39494_8','39495_8','39496_8','39497_3','39498_8','39499_8','39500_8','39501_8','39502_8','39503_8','39504_8','39505_8','39506_8','39507_8','39508_8','39509_8','39510_8','39511_8','39512_8','39513_8','39514_11','39515_11','39516_11','39517_9','39518_11','39519_11','39520_4','39521_11','39522_9','39531_11','39532_11','39533_11','39534_11','39535_11','39536_11','39537_11','39538_11','39539_11','39539_8','39540_11','39541_0','39541_11','39541_8','39542_11','39543_11','39544_11','39545_11','39546_11','39547_11','39548_11','39549_11','39550_11','39551_11','39552_2196','39561_2196','39563_2196','39565_11','39565_8','39566_2196','39566_2199','39567_11','39567_2196','39567_8','39568_11','39568_2196','39568_8','39571_2196','39571_2199','39574_11','39574_2196','39574_8','39575_0','39575_11','39575_2196','39575_8','39576_11','39577_11','39577_2196','39577_8','39578_11','39579_0','39579_11','39580_1','39580_11','39580_15','39581_11','39582_2196','39583_11','39584_11','39585_0','39585_11','39586_0','39586_11','39586_8','39587_11','39587_8','39588_11','39588_8','39589_11','39589_8','39590_11','39591_11','39592_11','39592_8','39593_0','39593_11','39593_2196','39593_8','39594_11','39594_2196','39594_8','39595_11','39596_11','39597_0','39597_11','39598_11','39598_8','39599_11','39599_2196','39599_8','39600_11','39600_2196','39600_8','39602_11','39603_0','39603_11','39603_2199','39603_8','39604_11','39605_0','39605_11','39606_11','39606_8','39607_11','39608_0','39608_11','39608_2','39608_8','39609_11','39610_0','39610_11','39610_8','39611_11','39612_11','39613_11','39615_11','39616_11','39616_8','39617_11','39618_11','39619_11','39620_11','39621_11','39621_8','39622_11','39622_8','39623_11','39624_11','39624_8','39625_8','39626_11','39626_8','39627_8','39628_8','39629_8','39630_8','39631_8','39632_8','39633_8','39634_2196','39634_8','39635_8','39636_8','39638_8','39639_8','39640_8','39641_8','39642_8','39643_8','39644_8','39645_8','39646_8','39647_8','39648_8','39652_8','39653_8','39654_8','39655_8','39656_8','39657_8','39658_8','39659_8','39660_8','39661_8','39662_8','39663_8','39664_8','39665_8','39666_8','39667_8','39668_8','39669_8','39670_8','39671_8','39672_8','39673_8','39674_8','39675_8','39676_3','39677_11','39678_11','39679_11','39680_11','39681_11','39682_11','39683_11','39684_2196','39684_8','39685_2196','39685_8','39686_2196','39686_8','39687_2196','39687_8','39688_2196','39688_8','39689_2196','39689_8','39690_2196','39690_8','39691_2196','39691_8','39692_2196','39692_8','39693_2196','39693_8','39694_2196','39694_8','39695_2196','39695_8','39696_2196','39696_8','39697_2196','39697_8','39698_8','39699_2196','39699_8','39700_2196','39700_8','39701_2196','39701_8')
AND "hobo_id" = 219
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 13:31:30 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 266
AND "cmp_id" = 164
AND "itm_id_unit_id" IN ('39483_7','39483_8','39484_8','39485_11','39485_9','39486_11','39486_8','39487_11','39487_8','39488_11','39488_8','39489_11','39489_8','39490_11','39490_8','39491_11','39491_5','39492_8','39493_8','39494_8','39495_8','39496_8','39497_3','39498_8','39499_8','39500_8','39501_8','39502_8','39503_8','39504_8','39505_8','39506_8','39507_8','39508_8','39509_8','39510_8','39511_8','39512_8','39513_8','39514_11','39515_11','39516_11','39517_9','39518_11','39519_11','39520_4','39521_11','39522_9','39531_11','39532_11','39533_11','39534_11','39535_11','39536_11','39537_11','39538_11','39539_11','39539_8','39540_11','39541_0','39541_11','39541_8','39542_11','39543_11','39544_11','39545_11','39546_11','39547_11','39548_11','39549_11','39550_11','39551_11','39552_2196','39561_2196','39563_2196','39565_11','39565_8','39566_2196','39566_2199','39567_11','39567_2196','39567_8','39568_11','39568_2196','39568_8','39571_2196','39571_2199','39574_11','39574_2196','39574_8','39575_0','39575_11','39575_2196','39575_8','39576_11','39577_11','39577_2196','39577_8','39578_11','39579_0','39579_11','39580_1','39580_11','39580_15','39581_11','39582_2196','39583_11','39584_11','39585_0','39585_11','39586_0','39586_11','39586_8','39587_11','39587_8','39588_11','39588_8','39589_11','39589_8','39590_11','39591_11','39592_11','39592_8','39593_0','39593_11','39593_2196','39593_8','39594_11','39594_2196','39594_8','39595_11','39596_11','39597_0','39597_11','39598_11','39598_8','39599_11','39599_2196','39599_8','39600_11','39600_2196','39600_8','39602_11','39603_0','39603_11','39603_2199','39603_8','39604_11','39605_0','39605_11','39606_11','39606_8','39607_11','39608_0','39608_11','39608_2','39608_8','39609_11','39610_0','39610_11','39610_8','39611_11','39612_11','39613_11','39615_11','39616_11','39616_8','39617_11','39618_11','39619_11','39620_11','39621_11','39621_8','39622_11','39622_8','39623_11','39624_11','39624_8','39625_8','39626_11','39626_8','39627_8','39628_8','39629_8','39630_8','39631_8','39632_8','39633_8','39634_2196','39634_8','39635_8','39636_8','39638_8','39639_8','39640_8','39641_8','39642_8','39643_8','39644_8','39645_8','39646_8','39647_8','39648_8','39652_8','39653_8','39654_8','39655_8','39656_8','39657_8','39658_8','39659_8','39660_8','39661_8','39662_8','39663_8','39664_8','39665_8','39666_8','39667_8','39668_8','39669_8','39670_8','39671_8','39672_8','39673_8','39674_8','39675_8','39676_3','39677_11','39678_11','39679_11','39680_11','39681_11','39682_11','39683_11','39684_2196','39684_8','39685_2196','39685_8','39686_2196','39686_8','39687_2196','39687_8','39688_2196','39688_8','39689_2196','39689_8','39690_2196','39690_8','39691_2196','39691_8','39692_2196','39692_8','39693_2196','39693_8','39694_2196','39694_8','39695_2196','39695_8','39696_2196','39696_8','39697_2196','39697_8','39698_8','39699_2196','39699_8','39700_2196','39700_8','39701_2196','39701_8')
AND "hobo_id" = 219
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 13:31:35 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 266
AND "cmp_id" = 164
AND "itm_id_unit_id" IN ('39483_7','39483_8','39484_8','39485_11','39485_9','39486_11','39486_8','39487_11','39487_8','39488_11','39488_8','39489_11','39489_8','39490_11','39490_8','39491_11','39491_5','39492_8','39493_8','39494_8','39495_8','39496_8','39497_3','39498_8','39499_8','39500_8','39501_8','39502_8','39503_8','39504_8','39505_8','39506_8','39507_8','39508_8','39509_8','39510_8','39511_8','39512_8','39513_8','39514_11','39515_11','39516_11','39517_9','39518_11','39519_11','39520_4','39521_11','39522_9','39531_11','39532_11','39533_11','39534_11','39535_11','39536_11','39537_11','39538_11','39539_11','39539_8','39540_11','39541_0','39541_11','39541_8','39542_11','39543_11','39544_11','39545_11','39546_11','39547_11','39548_11','39549_11','39550_11','39551_11','39552_2196','39561_2196','39563_2196','39565_11','39565_8','39566_2196','39566_2199','39567_11','39567_2196','39567_8','39568_11','39568_2196','39568_8','39571_2196','39571_2199','39574_11','39574_2196','39574_8','39575_0','39575_11','39575_2196','39575_8','39576_11','39577_11','39577_2196','39577_8','39578_11','39579_0','39579_11','39580_1','39580_11','39580_15','39581_11','39582_2196','39583_11','39584_11','39585_0','39585_11','39586_0','39586_11','39586_8','39587_11','39587_8','39588_11','39588_8','39589_11','39589_8','39590_11','39591_11','39592_11','39592_8','39593_0','39593_11','39593_2196','39593_8','39594_11','39594_2196','39594_8','39595_11','39596_11','39597_0','39597_11','39598_11','39598_8','39599_11','39599_2196','39599_8','39600_11','39600_2196','39600_8','39602_11','39603_0','39603_11','39603_2199','39603_8','39604_11','39605_0','39605_11','39606_11','39606_8','39607_11','39608_0','39608_11','39608_2','39608_8','39609_11','39610_0','39610_11','39610_8','39611_11','39612_11','39613_11','39615_11','39616_11','39616_8','39617_11','39618_11','39619_11','39620_11','39621_11','39621_8','39622_11','39622_8','39623_11','39624_11','39624_8','39625_8','39626_11','39626_8','39627_8','39628_8','39629_8','39630_8','39631_8','39632_8','39633_8','39634_2196','39634_8','39635_8','39636_8','39638_8','39639_8','39640_8','39641_8','39642_8','39643_8','39644_8','39645_8','39646_8','39647_8','39648_8','39652_8','39653_8','39654_8','39655_8','39656_8','39657_8','39658_8','39659_8','39660_8','39661_8','39662_8','39663_8','39664_8','39665_8','39666_8','39667_8','39668_8','39669_8','39670_8','39671_8','39672_8','39673_8','39674_8','39675_8','39676_3','39677_11','39678_11','39679_11','39680_11','39681_11','39682_11','39683_11','39684_2196','39684_8','39685_2196','39685_8','39686_2196','39686_8','39687_2196','39687_8','39688_2196','39688_8','39689_2196','39689_8','39690_2196','39690_8','39691_2196','39691_8','39692_2196','39692_8','39693_2196','39693_8','39694_2196','39694_8','39695_2196','39695_8','39696_2196','39696_8','39697_2196','39697_8','39698_8','39699_8','39700_2196','39700_8','39701_2196','39701_8')
AND "hobo_id" = 219
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 13:31:35 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 266
AND "cmp_id" = 164
AND "itm_id_unit_id" IN ('39483_7','39483_8','39484_8','39485_11','39485_9','39486_11','39486_8','39487_11','39487_8','39488_11','39488_8','39489_11','39489_8','39490_11','39490_8','39491_11','39491_5','39492_8','39493_8','39494_8','39495_8','39496_8','39497_3','39498_8','39499_8','39500_8','39501_8','39502_8','39503_8','39504_8','39505_8','39506_8','39507_8','39508_8','39509_8','39510_8','39511_8','39512_8','39513_8','39514_11','39515_11','39516_11','39517_9','39518_11','39519_11','39520_4','39521_11','39522_9','39531_11','39532_11','39533_11','39534_11','39535_11','39536_11','39537_11','39538_11','39539_11','39539_8','39540_11','39541_0','39541_11','39541_8','39542_11','39543_11','39544_11','39545_11','39546_11','39547_11','39548_11','39549_11','39550_11','39551_11','39552_2196','39561_2196','39563_2196','39565_11','39565_8','39566_2196','39566_2199','39567_11','39567_2196','39567_8','39568_11','39568_2196','39568_8','39571_2196','39571_2199','39574_11','39574_2196','39574_8','39575_0','39575_11','39575_2196','39575_8','39576_11','39577_11','39577_2196','39577_8','39578_11','39579_0','39579_11','39580_1','39580_11','39580_15','39581_11','39582_2196','39583_11','39584_11','39585_0','39585_11','39586_0','39586_11','39586_8','39587_11','39587_8','39588_11','39588_8','39589_11','39589_8','39590_11','39591_11','39592_11','39592_8','39593_0','39593_11','39593_2196','39593_8','39594_11','39594_2196','39594_8','39595_11','39596_11','39597_0','39597_11','39598_11','39598_8','39599_11','39599_2196','39599_8','39600_11','39600_2196','39600_8','39602_11','39603_0','39603_11','39603_2199','39603_8','39604_11','39605_0','39605_11','39606_11','39606_8','39607_11','39608_0','39608_11','39608_2','39608_8','39609_11','39610_0','39610_11','39610_8','39611_11','39612_11','39613_11','39615_11','39616_11','39616_8','39617_11','39618_11','39619_11','39620_11','39621_11','39621_8','39622_11','39622_8','39623_11','39624_11','39624_8','39625_8','39626_11','39626_8','39627_8','39628_8','39629_8','39630_8','39631_8','39632_8','39633_8','39634_2196','39634_8','39635_8','39636_8','39638_8','39639_8','39640_8','39641_8','39642_8','39643_8','39644_8','39645_8','39646_8','39647_8','39648_8','39652_8','39653_8','39654_8','39655_8','39656_8','39657_8','39658_8','39659_8','39660_8','39661_8','39662_8','39663_8','39664_8','39665_8','39666_8','39667_8','39668_8','39669_8','39670_8','39671_8','39672_8','39673_8','39674_8','39675_8','39676_3','39677_11','39678_11','39679_11','39680_11','39681_11','39682_11','39683_11','39684_2196','39684_8','39685_2196','39685_8','39686_2196','39686_8','39687_2196','39687_8','39688_2196','39688_8','39689_2196','39689_8','39690_2196','39690_8','39691_2196','39691_8','39692_2196','39692_8','39693_2196','39693_8','39694_2196','39694_8','39695_2196','39695_8','39696_2196','39696_8','39697_2196','39697_8','39698_8','39699_8','39700_2196','39700_8','39701_2196','39701_8')
AND "hobo_id" = 219
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 13:31:35 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 266
AND "cmp_id" = 164
AND "itm_id_unit_id" IN ('39483_7','39483_8','39484_8','39485_11','39485_9','39486_11','39486_8','39487_11','39487_8','39488_11','39488_8','39489_11','39489_8','39490_11','39490_8','39491_11','39491_5','39492_8','39493_8','39494_8','39495_8','39496_8','39497_3','39498_8','39499_8','39500_8','39501_8','39502_8','39503_8','39504_8','39505_8','39506_8','39507_8','39508_8','39509_8','39510_8','39511_8','39512_8','39513_8','39514_11','39515_11','39516_11','39517_9','39518_11','39519_11','39520_4','39521_11','39522_9','39531_11','39532_11','39533_11','39534_11','39535_11','39536_11','39537_11','39538_11','39539_11','39539_8','39540_11','39541_0','39541_11','39541_8','39542_11','39543_11','39544_11','39545_11','39546_11','39547_11','39548_11','39549_11','39550_11','39551_11','39552_2196','39561_2196','39563_2196','39565_11','39565_8','39566_2196','39566_2199','39567_11','39567_2196','39567_8','39568_11','39568_2196','39568_8','39571_2196','39571_2199','39574_11','39574_2196','39574_8','39575_0','39575_11','39575_2196','39575_8','39576_11','39577_11','39577_2196','39577_8','39578_11','39579_0','39579_11','39580_1','39580_11','39580_15','39581_11','39582_2196','39583_11','39584_11','39585_0','39585_11','39586_0','39586_11','39586_8','39587_11','39587_8','39588_11','39588_8','39589_11','39589_8','39590_11','39591_11','39592_11','39592_8','39593_0','39593_11','39593_2196','39593_8','39594_11','39594_2196','39594_8','39595_11','39596_11','39597_0','39597_11','39598_11','39598_8','39599_11','39599_2196','39599_8','39600_11','39600_2196','39600_8','39602_11','39603_0','39603_11','39603_2199','39603_8','39604_11','39605_0','39605_11','39606_11','39606_8','39607_11','39608_0','39608_11','39608_2','39608_8','39609_11','39610_0','39610_11','39610_8','39611_11','39612_11','39613_11','39615_11','39616_11','39616_8','39617_11','39618_11','39619_11','39620_11','39621_11','39621_8','39622_11','39622_8','39623_11','39624_11','39624_8','39625_8','39626_11','39626_8','39627_8','39628_8','39629_8','39630_8','39631_8','39632_8','39633_8','39634_2196','39634_8','39635_8','39636_8','39638_8','39639_8','39640_8','39641_8','39642_8','39643_8','39644_8','39645_8','39646_8','39647_8','39648_8','39652_8','39653_8','39654_8','39655_8','39656_8','39657_8','39658_8','39659_8','39660_8','39661_8','39662_8','39663_8','39664_8','39665_8','39666_8','39667_8','39668_8','39669_8','39670_8','39671_8','39672_8','39673_8','39674_8','39675_8','39676_3','39677_11','39678_11','39679_11','39680_11','39681_11','39682_11','39683_11','39684_2196','39684_8','39685_2196','39685_8','39686_2196','39686_8','39687_2196','39687_8','39688_2196','39688_8','39689_2196','39689_8','39690_2196','39690_8','39691_2196','39691_8','39692_2196','39692_8','39693_2196','39693_8','39694_2196','39694_8','39695_2196','39695_8','39696_2196','39696_8','39697_2196','39697_8','39698_8','39699_8','39700_2196','39700_8','39701_2196','39701_8')
AND "hobo_id" = 219
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 13:31:35 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 266
AND "cmp_id" = 164
AND "itm_id_unit_id" IN ('39483_7','39483_8','39484_8','39485_11','39485_9','39486_11','39486_8','39487_11','39487_8','39488_11','39488_8','39489_11','39489_8','39490_11','39490_8','39491_11','39491_5','39492_8','39493_8','39494_8','39495_8','39496_8','39497_3','39498_8','39499_8','39500_8','39501_8','39502_8','39503_8','39504_8','39505_8','39506_8','39507_8','39508_8','39509_8','39510_8','39511_8','39512_8','39513_8','39514_11','39515_11','39516_11','39517_9','39518_11','39519_11','39520_4','39521_11','39522_9','39531_11','39532_11','39533_11','39534_11','39535_11','39536_11','39537_11','39538_11','39539_11','39539_8','39540_11','39541_0','39541_11','39541_8','39542_11','39543_11','39544_11','39545_11','39546_11','39547_11','39548_11','39549_11','39550_11','39551_11','39552_2196','39561_2196','39563_2196','39565_11','39565_8','39566_2196','39566_2199','39567_11','39567_2196','39567_8','39568_11','39568_2196','39568_8','39571_2196','39571_2199','39574_11','39574_2196','39574_8','39575_0','39575_11','39575_2196','39575_8','39576_11','39577_11','39577_2196','39577_8','39578_11','39579_0','39579_11','39580_1','39580_11','39580_15','39581_11','39582_2196','39583_11','39584_11','39585_0','39585_11','39586_0','39586_11','39586_8','39587_11','39587_8','39588_11','39588_8','39589_11','39589_8','39590_11','39591_11','39592_11','39592_8','39593_0','39593_11','39593_2196','39593_8','39594_11','39594_2196','39594_8','39595_11','39596_11','39597_0','39597_11','39598_11','39598_8','39599_11','39599_2196','39599_8','39600_11','39600_2196','39600_8','39602_11','39603_0','39603_11','39603_2199','39603_8','39604_11','39605_0','39605_11','39606_11','39606_8','39607_11','39608_0','39608_11','39608_2','39608_8','39609_11','39610_0','39610_11','39610_8','39611_11','39612_11','39613_11','39615_11','39616_11','39616_8','39617_11','39618_11','39619_11','39620_11','39621_11','39621_8','39622_11','39622_8','39623_11','39624_11','39624_8','39625_8','39626_11','39626_8','39627_8','39628_8','39629_8','39630_8','39631_8','39632_8','39633_8','39634_2196','39634_8','39635_8','39636_8','39638_8','39639_8','39640_8','39641_8','39642_8','39643_8','39644_8','39645_8','39646_8','39647_8','39648_8','39652_8','39653_8','39654_8','39655_8','39656_8','39657_8','39658_8','39659_8','39660_8','39661_8','39662_8','39663_8','39664_8','39665_8','39666_8','39667_8','39668_8','39669_8','39670_8','39671_8','39672_8','39673_8','39674_8','39675_8','39676_3','39677_11','39678_11','39679_11','39680_11','39681_11','39682_11','39683_11','39684_2196','39684_8','39685_2196','39685_8','39686_2196','39686_8','39687_2196','39687_8','39688_2196','39688_8','39689_2196','39689_8','39690_2196','39690_8','39691_2196','39691_8','39692_2196','39692_8','39693_2196','39693_8','39694_2196','39694_8','39695_2196','39695_8','39696_2196','39696_8','39697_2196','39697_8','39698_8','39699_8','39700_2196','39700_8','39701_2196','39701_8')
AND "hobo_id" = 219
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 13:31:43 --> [LedgerCondensed][acc=18846] ── FUNCTION START ── from=2025-10-01  to=2025-10-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 13:31:43 --> [LedgerCondensed][acc=18846] Q1-VoucherCount => 270  |  0.0023s
1 - 2026-09-19 13:31:43 --> [LedgerCondensed][acc=18846] Q2-OpeningBalance => -6205651.07  |  0.0012s
1 - 2026-09-19 13:31:43 --> [LedgerCondensed][acc=18846] Q3-MainLedger => rows=59  total=59  |  0.0121s
1 - 2026-09-19 13:31:43 --> [LedgerCondensed][acc=18846] Q4-OtherAccounts => 59 rows  |  0.0087s
1 - 2026-09-19 13:31:43 --> [LedgerCondensed][acc=18846] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0023s   (  9.1%)
   Q2-OpeningBalance           0.0012s   (  4.8%)
   Q3-MainLedger               0.0121s   ( 48.3%)
   Q4-OtherAccounts            0.0087s   ( 34.6%)
   BuildRecords                0.0003s   (  1.2%)
   TOTAL                       0.0250s   (100%)
[LedgerCondensed][acc=18846] ── FUNCTION END ──
1 - 2026-09-19 13:31:45 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 266
AND "cmp_id" = 164
AND "itm_id_unit_id" IN ('39483_7','39483_8','39484_8','39485_11','39485_9','39486_11','39486_8','39487_11','39487_8','39488_11','39488_8','39489_11','39489_8','39490_11','39490_8','39491_11','39491_5','39492_8','39493_8','39494_8','39495_8','39496_8','39497_3','39498_8','39499_8','39500_8','39501_8','39502_8','39503_8','39504_8','39505_8','39506_8','39507_8','39508_8','39509_8','39510_8','39511_8','39512_8','39513_8','39514_11','39515_11','39516_11','39517_9','39518_11','39519_11','39520_4','39521_11','39522_9','39531_11','39532_11','39533_11','39534_11','39535_11','39536_11','39537_11','39538_11','39539_11','39539_8','39540_11','39541_0','39541_11','39541_8','39542_11','39543_11','39544_11','39545_11','39546_11','39547_11','39548_11','39549_11','39550_11','39551_11','39552_2196','39561_2196','39563_2196','39565_11','39565_8','39566_2196','39566_2199','39567_11','39567_2196','39567_8','39568_11','39568_2196','39568_8','39571_2196','39571_2199','39574_11','39574_2196','39574_8','39575_0','39575_11','39575_2196','39575_8','39576_11','39577_11','39577_2196','39577_8','39578_11','39579_0','39579_11','39580_1','39580_11','39580_15','39581_11','39582_2196','39583_11','39584_11','39585_0','39585_11','39586_0','39586_11','39586_8','39587_11','39587_8','39588_11','39588_8','39589_11','39589_8','39590_11','39591_11','39592_11','39592_8','39593_0','39593_11','39593_2196','39593_8','39594_11','39594_2196','39594_8','39595_11','39596_11','39597_0','39597_11','39598_11','39598_8','39599_11','39599_2196','39599_8','39600_11','39600_2196','39600_8','39602_11','39603_0','39603_11','39603_2199','39603_8','39604_11','39605_0','39605_11','39606_11','39606_8','39607_11','39608_0','39608_11','39608_2','39608_8','39609_11','39610_0','39610_11','39610_8','39611_11','39612_11','39613_11','39615_11','39616_11','39616_8','39617_11','39618_11','39619_11','39620_11','39621_11','39621_8','39622_11','39622_8','39623_11','39624_11','39624_8','39625_8','39626_11','39626_8','39627_8','39628_8','39629_8','39630_8','39631_8','39632_8','39633_8','39634_2196','39634_8','39635_8','39636_8','39638_8','39639_8','39640_8','39641_8','39642_8','39643_8','39644_8','39645_8','39646_8','39647_8','39648_8','39652_8','39653_8','39654_8','39655_8','39656_8','39657_8','39658_8','39659_8','39660_8','39661_8','39662_8','39663_8','39664_8','39665_8','39666_8','39667_8','39668_8','39669_8','39670_8','39671_8','39672_8','39673_8','39674_8','39675_8','39676_3','39677_11','39678_11','39679_11','39680_11','39681_11','39682_11','39683_11','39684_2196','39684_8','39685_2196','39685_8','39686_2196','39686_8','39687_2196','39687_8','39688_2196','39688_8','39689_2196','39689_8','39690_2196','39690_8','39691_2196','39691_8','39692_2196','39692_8','39693_2196','39693_8','39694_2196','39694_8','39695_2196','39695_8','39696_2196','39696_8','39697_2196','39697_8','39698_8','39699_8','39700_2196','39700_8','39701_2196','39701_8')
AND "hobo_id" = 219
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 13:31:45 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 266
AND "cmp_id" = 164
AND "itm_id_unit_id" IN ('39483_7','39483_8','39484_8','39485_11','39485_9','39486_11','39486_8','39487_11','39487_8','39488_11','39488_8','39489_11','39489_8','39490_11','39490_8','39491_11','39491_5','39492_8','39493_8','39494_8','39495_8','39496_8','39497_3','39498_8','39499_8','39500_8','39501_8','39502_8','39503_8','39504_8','39505_8','39506_8','39507_8','39508_8','39509_8','39510_8','39511_8','39512_8','39513_8','39514_11','39515_11','39516_11','39517_9','39518_11','39519_11','39520_4','39521_11','39522_9','39531_11','39532_11','39533_11','39534_11','39535_11','39536_11','39537_11','39538_11','39539_11','39539_8','39540_11','39541_0','39541_11','39541_8','39542_11','39543_11','39544_11','39545_11','39546_11','39547_11','39548_11','39549_11','39550_11','39551_11','39552_2196','39561_2196','39563_2196','39565_11','39565_8','39566_2196','39566_2199','39567_11','39567_2196','39567_8','39568_11','39568_2196','39568_8','39571_2196','39571_2199','39574_11','39574_2196','39574_8','39575_0','39575_11','39575_2196','39575_8','39576_11','39577_11','39577_2196','39577_8','39578_11','39579_0','39579_11','39580_1','39580_11','39580_15','39581_11','39582_2196','39583_11','39584_11','39585_0','39585_11','39586_0','39586_11','39586_8','39587_11','39587_8','39588_11','39588_8','39589_11','39589_8','39590_11','39591_11','39592_11','39592_8','39593_0','39593_11','39593_2196','39593_8','39594_11','39594_2196','39594_8','39595_11','39596_11','39597_0','39597_11','39598_11','39598_8','39599_11','39599_2196','39599_8','39600_11','39600_2196','39600_8','39602_11','39603_0','39603_11','39603_2199','39603_8','39604_11','39605_0','39605_11','39606_11','39606_8','39607_11','39608_0','39608_11','39608_2','39608_8','39609_11','39610_0','39610_11','39610_8','39611_11','39612_11','39613_11','39615_11','39616_11','39616_8','39617_11','39618_11','39619_11','39620_11','39621_11','39621_8','39622_11','39622_8','39623_11','39624_11','39624_8','39625_8','39626_11','39626_8','39627_8','39628_8','39629_8','39630_8','39631_8','39632_8','39633_8','39634_2196','39634_8','39635_8','39636_8','39638_8','39639_8','39640_8','39641_8','39642_8','39643_8','39644_8','39645_8','39646_8','39647_8','39648_8','39652_8','39653_8','39654_8','39655_8','39656_8','39657_8','39658_8','39659_8','39660_8','39661_8','39662_8','39663_8','39664_8','39665_8','39666_8','39667_8','39668_8','39669_8','39670_8','39671_8','39672_8','39673_8','39674_8','39675_8','39676_3','39677_11','39678_11','39679_11','39680_11','39681_11','39682_11','39683_11','39684_2196','39684_8','39685_2196','39685_8','39686_2196','39686_8','39687_2196','39687_8','39688_2196','39688_8','39689_2196','39689_8','39690_2196','39690_8','39691_2196','39691_8','39692_2196','39692_8','39693_2196','39693_8','39694_2196','39694_8','39695_2196','39695_8','39696_2196','39696_8','39697_2196','39697_8','39698_8','39699_8','39700_2196','39700_8','39701_2196','39701_8')
AND "hobo_id" = 219
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 13:31:45 --> Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
FROM "itmoppybal"
WHERE "cmpfymastr_id" = 266
AND "cmp_id" = 164
AND "itm_id_unit_id" IN ('39483_7','39483_8','39484_8','39485_11','39485_9','39486_11','39486_8','39487_11','39487_8','39488_11','39488_8','39489_11','39489_8','39490_11','39490_8','39491_11','39491_5','39492_8','39493_8','39494_8','39495_8','39496_8','39497_3','39498_8','39499_8','39500_8','39501_8','39502_8','39503_8','39504_8','39505_8','39506_8','39507_8','39508_8','39509_8','39510_8','39511_8','39512_8','39513_8','39514_11','39515_11','39516_11','39517_9','39518_11','39519_11','39520_4','39521_11','39522_9','39531_11','39532_11','39533_11','39534_11','39535_11','39536_11','39537_11','39538_11','39539_11','39539_8','39540_11','39541_0','39541_11','39541_8','39542_11','39543_11','39544_11','39545_11','39546_11','39547_11','39548_11','39549_11','39550_11','39551_11','39552_2196','39561_2196','39563_2196','39565_11','39565_8','39566_2196','39566_2199','39567_11','39567_2196','39567_8','39568_11','39568_2196','39568_8','39571_2196','39571_2199','39574_11','39574_2196','39574_8','39575_0','39575_11','39575_2196','39575_8','39576_11','39577_11','39577_2196','39577_8','39578_11','39579_0','39579_11','39580_1','39580_11','39580_15','39581_11','39582_2196','39583_11','39584_11','39585_0','39585_11','39586_0','39586_11','39586_8','39587_11','39587_8','39588_11','39588_8','39589_11','39589_8','39590_11','39591_11','39592_11','39592_8','39593_0','39593_11','39593_2196','39593_8','39594_11','39594_2196','39594_8','39595_11','39596_11','39597_0','39597_11','39598_11','39598_8','39599_11','39599_2196','39599_8','39600_11','39600_2196','39600_8','39602_11','39603_0','39603_11','39603_2199','39603_8','39604_11','39605_0','39605_11','39606_11','39606_8','39607_11','39608_0','39608_11','39608_2','39608_8','39609_11','39610_0','39610_11','39610_8','39611_11','39612_11','39613_11','39615_11','39616_11','39616_8','39617_11','39618_11','39619_11','39620_11','39621_11','39621_8','39622_11','39622_8','39623_11','39624_11','39624_8','39625_8','39626_11','39626_8','39627_8','39628_8','39629_8','39630_8','39631_8','39632_8','39633_8','39634_2196','39634_8','39635_8','39636_8','39638_8','39639_8','39640_8','39641_8','39642_8','39643_8','39644_8','39645_8','39646_8','39647_8','39648_8','39652_8','39653_8','39654_8','39655_8','39656_8','39657_8','39658_8','39659_8','39660_8','39661_8','39662_8','39663_8','39664_8','39665_8','39666_8','39667_8','39668_8','39669_8','39670_8','39671_8','39672_8','39673_8','39674_8','39675_8','39676_3','39677_11','39678_11','39679_11','39680_11','39681_11','39682_11','39683_11','39684_2196','39684_8','39685_2196','39685_8','39686_2196','39686_8','39687_2196','39687_8','39688_2196','39688_8','39689_2196','39689_8','39690_2196','39690_8','39691_2196','39691_8','39692_2196','39692_8','39693_2196','39693_8','39694_2196','39694_8','39695_2196','39695_8','39696_2196','39696_8','39697_2196','39697_8','39698_8','39699_8','39700_2196','39700_8','39701_2196','39701_8')
AND "hobo_id" = 219
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 13:31:45 --> Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>SELECT "itm_id_unit_id", COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
FROM "itmoppyval"
WHERE "cmpfymastr_id" = 266
AND "cmp_id" = 164
AND "itm_id_unit_id" IN ('39483_7','39483_8','39484_8','39485_11','39485_9','39486_11','39486_8','39487_11','39487_8','39488_11','39488_8','39489_11','39489_8','39490_11','39490_8','39491_11','39491_5','39492_8','39493_8','39494_8','39495_8','39496_8','39497_3','39498_8','39499_8','39500_8','39501_8','39502_8','39503_8','39504_8','39505_8','39506_8','39507_8','39508_8','39509_8','39510_8','39511_8','39512_8','39513_8','39514_11','39515_11','39516_11','39517_9','39518_11','39519_11','39520_4','39521_11','39522_9','39531_11','39532_11','39533_11','39534_11','39535_11','39536_11','39537_11','39538_11','39539_11','39539_8','39540_11','39541_0','39541_11','39541_8','39542_11','39543_11','39544_11','39545_11','39546_11','39547_11','39548_11','39549_11','39550_11','39551_11','39552_2196','39561_2196','39563_2196','39565_11','39565_8','39566_2196','39566_2199','39567_11','39567_2196','39567_8','39568_11','39568_2196','39568_8','39571_2196','39571_2199','39574_11','39574_2196','39574_8','39575_0','39575_11','39575_2196','39575_8','39576_11','39577_11','39577_2196','39577_8','39578_11','39579_0','39579_11','39580_1','39580_11','39580_15','39581_11','39582_2196','39583_11','39584_11','39585_0','39585_11','39586_0','39586_11','39586_8','39587_11','39587_8','39588_11','39588_8','39589_11','39589_8','39590_11','39591_11','39592_11','39592_8','39593_0','39593_11','39593_2196','39593_8','39594_11','39594_2196','39594_8','39595_11','39596_11','39597_0','39597_11','39598_11','39598_8','39599_11','39599_2196','39599_8','39600_11','39600_2196','39600_8','39602_11','39603_0','39603_11','39603_2199','39603_8','39604_11','39605_0','39605_11','39606_11','39606_8','39607_11','39608_0','39608_11','39608_2','39608_8','39609_11','39610_0','39610_11','39610_8','39611_11','39612_11','39613_11','39615_11','39616_11','39616_8','39617_11','39618_11','39619_11','39620_11','39621_11','39621_8','39622_11','39622_8','39623_11','39624_11','39624_8','39625_8','39626_11','39626_8','39627_8','39628_8','39629_8','39630_8','39631_8','39632_8','39633_8','39634_2196','39634_8','39635_8','39636_8','39638_8','39639_8','39640_8','39641_8','39642_8','39643_8','39644_8','39645_8','39646_8','39647_8','39648_8','39652_8','39653_8','39654_8','39655_8','39656_8','39657_8','39658_8','39659_8','39660_8','39661_8','39662_8','39663_8','39664_8','39665_8','39666_8','39667_8','39668_8','39669_8','39670_8','39671_8','39672_8','39673_8','39674_8','39675_8','39676_3','39677_11','39678_11','39679_11','39680_11','39681_11','39682_11','39683_11','39684_2196','39684_8','39685_2196','39685_8','39686_2196','39686_8','39687_2196','39687_8','39688_2196','39688_8','39689_2196','39689_8','39690_2196','39690_8','39691_2196','39691_8','39692_2196','39692_8','39693_2196','39693_8','39694_2196','39694_8','39695_2196','39695_8','39696_2196','39696_8','39697_2196','39697_8','39698_8','39699_8','39700_2196','39700_8','39701_2196','39701_8')
AND "hobo_id" = 219
GROUP BY "itm_id_unit_id"
1 - 2026-09-19 16:26:44 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 16:26:44 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-19 16:26:44 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => -3470.00  |  0.0003s
1 - 2026-09-19 16:26:44 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0128s
1 - 2026-09-19 16:26:44 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.0011s
1 - 2026-09-19 16:26:44 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  6.0%)
   Q2-OpeningBalance           0.0003s   (  1.7%)
   Q3-MainLedger               0.0128s   ( 81.7%)
   Q4-OtherAccounts            0.0011s   (  7.2%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0156s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 16:27:00 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 16:27:00 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-19 16:27:00 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => -3470.00  |  0.0003s
1 - 2026-09-19 16:27:00 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0092s
1 - 2026-09-19 16:27:00 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-19 16:27:00 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.4%)
   Q2-OpeningBalance           0.0003s   (  2.1%)
   Q3-MainLedger               0.0092s   ( 78.8%)
   Q4-OtherAccounts            0.0009s   (  7.3%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0117s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 16:27:31 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-05-01  to=2024-05-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 16:27:31 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-19 16:27:31 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => -3470  |  0.0008s
1 - 2026-09-19 16:27:31 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0098s
1 - 2026-09-19 16:27:31 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-19 16:27:31 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.2%)
   Q2-OpeningBalance           0.0008s   (  6.0%)
   Q3-MainLedger               0.0098s   ( 76.1%)
   Q4-OtherAccounts            0.0009s   (  7.4%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0128s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 16:28:03 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-05-01  to=2024-05-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 16:28:03 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-19 16:28:03 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => -3470  |  0.0008s
1 - 2026-09-19 16:28:03 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0088s
1 - 2026-09-19 16:28:03 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-19 16:28:03 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.9%)
   Q2-OpeningBalance           0.0008s   (  6.7%)
   Q3-MainLedger               0.0088s   ( 73.8%)
   Q4-OtherAccounts            0.0009s   (  7.9%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0119s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 16:30:08 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-05-01  to=2024-05-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 16:30:08 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.0012s
1 - 2026-09-19 16:30:08 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => -3470  |  0.0009s
1 - 2026-09-19 16:30:08 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0088s
1 - 2026-09-19 16:30:08 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-19 16:30:08 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  9.5%)
   Q2-OpeningBalance           0.0009s   (  7.1%)
   Q3-MainLedger               0.0088s   ( 72.2%)
   Q4-OtherAccounts            0.0009s   (  7.7%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0122s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 16:36:19 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-05-01  to=2024-05-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 16:36:19 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-19 16:36:19 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => -3470  |  0.0008s
1 - 2026-09-19 16:36:19 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0086s
1 - 2026-09-19 16:36:19 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-09-19 16:36:19 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  8.9%)
   Q2-OpeningBalance           0.0008s   (  7.0%)
   Q3-MainLedger               0.0086s   ( 72.3%)
   Q4-OtherAccounts            0.0010s   (  8.3%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0119s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 16:39:35 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2023-04-01  to=2024-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 16:39:35 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.0007s
1 - 2026-09-19 16:39:35 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => 0.00  |  0.0002s
1 - 2026-09-19 16:39:35 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0088s
1 - 2026-09-19 16:39:35 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.001s
1 - 2026-09-19 16:39:35 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0007s   (  6.4%)
   Q2-OpeningBalance           0.0002s   (  2.2%)
   Q3-MainLedger               0.0088s   ( 78.8%)
   Q4-OtherAccounts            0.0010s   (  8.9%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0111s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 16:41:14 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2023-04-01  to=2024-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 16:41:14 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-19 16:41:14 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-19 16:41:14 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0087s
1 - 2026-09-19 16:41:14 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-19 16:41:14 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.8%)
   Q2-OpeningBalance           0.0003s   (  2.4%)
   Q3-MainLedger               0.0087s   ( 76.7%)
   Q4-OtherAccounts            0.0009s   (  8.0%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0114s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 16:41:29 --> [LedgerCondensed][acc=10866] ── FUNCTION START ── from=2023-04-01  to=2024-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 16:41:29 --> [LedgerCondensed][acc=10866] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-19 16:41:29 --> [LedgerCondensed][acc=10866] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-19 16:41:29 --> [LedgerCondensed][acc=10866] Q3-MainLedger => rows=6  total=6  |  0.0413s
1 - 2026-09-19 16:41:29 --> [LedgerCondensed][acc=10866] Q4-OtherAccounts => 6 rows  |  0.0014s
1 - 2026-09-19 16:41:29 --> [LedgerCondensed][acc=10866] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  1.9%)
   Q2-OpeningBalance           0.0003s   (  0.6%)
   Q3-MainLedger               0.0413s   ( 93.3%)
   Q4-OtherAccounts            0.0014s   (  3.2%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0443s   (100%)
[LedgerCondensed][acc=10866] ── FUNCTION END ──
1 - 2026-09-19 16:41:54 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-03-01  to=2024-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 16:41:54 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.0008s
1 - 2026-09-19 16:41:54 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => 0  |  0.0008s
1 - 2026-09-19 16:41:54 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0085s
1 - 2026-09-19 16:41:54 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-19 16:41:54 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0008s   (  7.1%)
   Q2-OpeningBalance           0.0008s   (  6.8%)
   Q3-MainLedger               0.0085s   ( 74.1%)
   Q4-OtherAccounts            0.0009s   (  8.0%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0115s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 16:42:11 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-03-01  to=2024-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 16:42:11 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.001s
1 - 2026-09-19 16:42:11 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => 0  |  0.0009s
1 - 2026-09-19 16:42:11 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0086s
1 - 2026-09-19 16:42:11 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.0011s
1 - 2026-09-19 16:42:11 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0010s   (  8.3%)
   Q2-OpeningBalance           0.0009s   (  7.2%)
   Q3-MainLedger               0.0086s   ( 71.7%)
   Q4-OtherAccounts            0.0011s   (  8.9%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0120s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 16:44:45 --> [LedgerCondensed][acc=10922] ── FUNCTION START ── from=2024-04-01  to=2025-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 16:44:45 --> [LedgerCondensed][acc=10922] Q1-VoucherCount => 0  |  0.0009s
1 - 2026-09-19 16:44:45 --> [LedgerCondensed][acc=10922] Q2-OpeningBalance => 0.00  |  0.0003s
1 - 2026-09-19 16:44:45 --> [LedgerCondensed][acc=10922] Q3-MainLedger => rows=1  total=1  |  0.0089s
1 - 2026-09-19 16:44:45 --> [LedgerCondensed][acc=10922] Q4-OtherAccounts => 1 rows  |  0.0009s
1 - 2026-09-19 16:44:45 --> [LedgerCondensed][acc=10922] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0009s   (  7.6%)
   Q2-OpeningBalance           0.0003s   (  2.3%)
   Q3-MainLedger               0.0089s   ( 78.4%)
   Q4-OtherAccounts            0.0009s   (  7.8%)
   BuildRecords                0.0000s   (  0.2%)
   TOTAL                       0.0113s   (100%)
[LedgerCondensed][acc=10922] ── FUNCTION END ──
1 - 2026-09-19 16:47:41 --> [LedgerCondensed][acc=10886] ── FUNCTION START ── from=2024-05-01  to=2024-05-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 16:47:41 --> [LedgerCondensed][acc=10886] Q1-VoucherCount => 0  |  0.0012s
1 - 2026-09-19 16:47:41 --> [LedgerCondensed][acc=10886] Q2-OpeningBalance => -3470  |  0.001s
1 - 2026-09-19 16:47:41 --> [LedgerCondensed][acc=10886] Q3-MainLedger => rows=1  total=1  |  0.0128s
1 - 2026-09-19 16:47:41 --> [LedgerCondensed][acc=10886] Q4-OtherAccounts => 1 rows  |  0.0013s
1 - 2026-09-19 16:47:41 --> [LedgerCondensed][acc=10886] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0012s   (  7.1%)
   Q2-OpeningBalance           0.0010s   (  6.0%)
   Q3-MainLedger               0.0128s   ( 76.2%)
   Q4-OtherAccounts            0.0013s   (  7.8%)
   BuildRecords                0.0000s   (  0.1%)
   TOTAL                       0.0167s   (100%)
[LedgerCondensed][acc=10886] ── FUNCTION END ──
1 - 2026-09-19 17:50:33 --> [LedgerCondensed][acc=10402] ── FUNCTION START ── from=2025-04-01  to=2026-03-31  type=1  is_export=0  compId=  boId=  fyId=
1 - 2026-09-19 17:50:33 --> [LedgerCondensed][acc=10402] Q1-VoucherCount => 0  |  0.0011s
1 - 2026-09-19 17:50:33 --> [LedgerCondensed][acc=10402] Q2-OpeningBalance => -69801.00  |  0.0003s
1 - 2026-09-19 17:50:34 --> [LedgerCondensed][acc=10402] Q3-MainLedger => rows=23  total=23  |  0.1492s
1 - 2026-09-19 17:50:34 --> [LedgerCondensed][acc=10402] Q4-OtherAccounts => 23 rows  |  0.0021s
1 - 2026-09-19 17:50:34 --> [LedgerCondensed][acc=10402] ── TIMING SUMMARY ──
   Q1-VoucherCount             0.0011s   (  0.7%)
   Q2-OpeningBalance           0.0003s   (  0.2%)
   Q3-MainLedger               0.1492s   ( 97.4%)
   Q4-OtherAccounts            0.0021s   (  1.4%)
   BuildRecords                0.0001s   (  0.1%)
   TOTAL                       0.1532s   (100%)
[LedgerCondensed][acc=10402] ── FUNCTION END ──
