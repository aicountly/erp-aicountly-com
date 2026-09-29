<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Ledger</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body {
            width: 210mm;
            font-size: 9pt;
            margin: 0 auto;
            padding: 0;
            font-family: "Times New Roman", Times, serif;
        }
        @page { size: A4; margin: 10mm; }
        table { border-collapse: collapse; width: 100%; }
        th { border: 1px solid; padding: 8px; text-overflow: ellipsis; }
        td { padding: 2px 6px; border-left: 1px solid; border-right: 1px solid; text-align: center; vertical-align: top; }
        th.particulars, td.particulars { text-align: left; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: none; padding: 10px; }
        .header h4 { margin: 0; }
        .date-account-row th { text-align: left; padding: 5px 0 5px 5px; }
        .footer-account-row th { text-align: right; padding: 8px 5px; }
        th.debit, th.credit, th.balance { font-weight: bold; }
        td.debit, td.credit, td.balance { text-align: right; }
        .pdf-download-btn { display: block; padding: 10px 20px; background-color: #28a745; color: white; font-size: 14px; border: none; cursor: pointer; border-radius: 5px; }
        .pdf-download-btn:hover { background-color: #218838; }
        .btn-dwl { display: flex; justify-content: center; gap: 10px; margin: 12px 0; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: auto; }
		/* Fixed page height container */
.page {
    height: 297mm;
    overflow: hidden;
}

/* Keep footer always at bottom */
.page-footer {
    margin-top: auto;
}
    </style>
</head>
<body>
<div id="ledgerContent">
    <?php
        $logoSrc    = base_url('admin/company/logo');
        $defaultSrc = base_url('public/assets/img/logo.png');

        $rowsPerPage = $rows_per_page ?? 25;
        $pages = array_chunk($records, $rowsPerPage);

        $runningDr = 0;
        $runningCr = 0;

        $renderHeader = function($page_no, $total_pages) use ($company_info, $company_address, $gstin, $from_date, $to_date, $account_info, $logoSrc, $defaultSrc) {
            ?>
            <div class="header">
                <div>
                    <img
                      src="<?= $logoSrc ?>"
                      alt="Company Logo"
                      style="width:80px; height:auto; margin-top:4px;"
                      onerror="this.onerror=null; this.src='<?= $defaultSrc ?>';"
                    />
                </div>
                <div>
                    <span style="text-align: center;">
                        <b style="font-size: 20px;"><?= esc($company_info['cmp_name'] ?? '') ?></b><br>
                        <?= esc($company_address['hobo_addr1'] ?? '') ?><?= isset($company_address['hobo_addr2']) ? ', '.esc($company_address['hobo_addr2']) : '' ?><br>
                        <?= esc($company_address['hobo_city'] ?? '') ?> <?= esc($company_address['hobo_pin_zip'] ?? '') ?>
                    </span>
                    <p style="margin: 5px 0; text-align: center;"><strong style="font-size: 12pt;">Account Statement</strong></p>
                </div>
                <div style="margin-right: 30px;">
				  <?php if ($page_no == 1): ?>
                    <span>GSTIN: <?= esc($gstin) ?></span><br>
                    <span>CIN: <?= esc($company_info['cin'] ?? '') ?></span>
					<?php else: ?>
					<span>
						Page <?= $page_no ?> of <?= $total_pages ?>
					</span>

				<?php endif; ?>
                </div>
            </div>
            <table>
                <thead>
                    <tr class="date-account-row">
                        <th colspan="21" style="border: none; font-size: 10pt;">
                            From <?= esc(date('d/m/Y', strtotime($from_date))) ?> to <?= esc(date('d/m/Y', strtotime($to_date))) ?>
                        </th>
                        <th colspan="15" style="border: none; font-size: 11.5pt; padding-left: 80px;">
                            Account: <?= esc($account_info['acc_name'] ?? '') ?>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4"  class="debit">Date</th>
                        <th colspan="3"  class="debit"   style="border-left: none;">Type</th>
                        <th colspan="3"  class="debit"   style="border-left: none;">Vch No.</th>
                        <th colspan="12" class="particulars debit" style="border-left: none;">Particulars</th>
                        <th colspan="3"  class="debit"   style="border-left: none;">Bill Ref No.</th>
                        <th colspan="3"  class="debit"   style="border-left: none;">Narration</th>
                        <th colspan="3"  class="debit"   style="border-left: none;">Debit(₹)</th>
                        <th colspan="3"  class="credit"  style="border-left: none;">Credit(₹)</th>
                        <th colspan="3"  class="balance" style="border-left: none;">Balance(₹)</th>
                    </tr>
                </thead>
                <tbody>
            <?php
        };
    ?>

    <?php foreach ($pages as $pageIndex => $pageRows): ?>
        <div class="page">
            <?php $renderHeader($pageIndex + 1, count($pages)); ?>
            <?php foreach ($pageRows as $row): ?>
                <?php
                    $runningDr += (float) ($row['debit_total'] ?? 0);
                    $runningCr += (float) ($row['credit_total'] ?? 0);
                    $cumBalance = abs($runningDr - $runningCr);
                ?>
                <tr>
                    <td colspan="4"><?= esc($row['txn_date'] ?? '') ?></td>
                    <td colspan="3" style="border-left: none;"><?= esc($row['voucher_type'] ?? '') ?></td>
                    <td colspan="3" style="border-left: none;"><?= esc($row['voucher_no'] ?? '') ?></td>
                    <td colspan="12" class="particulars" style="border-left: none;">
                        <?= esc($row['account_name'] ?? '') ?>
                        <?php if (!empty($row['long_narration'])): ?>
                            <br><span style="font-style: italic; padding-left: 5px;"><?= esc($row['long_narration']) ?></span>
						 <?php else: ?>	
						<br><span style="font-style: italic; padding-left: 5px;"></span>						 
					   <?php endif; ?>
                    </td>
                    <td colspan="3" style="border-left: none;"><?= esc($row['bill_ref_no'] ?? '') ?></td>
                    <td colspan="3" style="border-left: none;">
                        <?php if (!empty($row['short_narration'])): ?>
                            <span style="font-style: italic; padding-left: 5px;"><?= esc($row['short_narration']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td colspan="3" class="debit"  style="border-left: none;"><?= formatAmount($row['debit_total'] ?? 0) ?></td>
                    <td colspan="3" class="credit" style="border-left: none;"><?= formatAmount($row['credit_total'] ?? 0) ?></td>
                    <td colspan="3" class="balance" style="border-left: none;"><?= formatAmount(abs($row['balance_total'] ?? 0)) ?> <?= esc($row['balance_type'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>

            <!-- Totals c/f on every page -->
            <tr class="footer-account-row">
                <th colspan="25"></th>
                <th colspan="3" class="debit" style="border-left: none;">Totals c/f</th>
                <th colspan="3" class="debit" style="border-left: none;"><?= formatAmount($runningDr) ?></th>
                <th colspan="3" class="debit" style="border-left: none;"><?= formatAmount($runningCr) ?></th>
                <th colspan="3" class="debit" style="border-left: none;"><?= formatAmount($cumBalance) ?></th>
            </tr>

            <?php if ($pageIndex === count($pages) - 1): ?>
                <!-- Only on last page: Balance and Grand Total -->
                <tr class="footer-account-row">
                    <th colspan="28" style="border-right: none;"></th>
                    <th colspan="3" style="border-left: none;">Balance</th>
                    <th colspan="3" style="border-left: none;"><?= formatAmount($closing_balance_abs) ?></th>
                    <th colspan="3" style="border-left: none;"><?= esc($closing_balance_type) ?></th>
                </tr>
                <tr class="footer-account-row">
                    <th colspan="28" style="border-right: none;"></th>
                    <th colspan="3" style="border-left: none;">Grand Total</th>
                    <th colspan="3" style="border-left: none;"><?= formatAmount($total_debit) ?></th>
                    <th colspan="3" style="border-left: none;"><?= formatAmount($total_credit) ?></th>
                </tr>
            <?php endif; ?>

            </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>

<div class="btn-dwl">
    <button class="pdf-download-btn" id="downloadPdf">Download PDF</button>
</div>

<script>
(async function(){

const { jsPDF } = window.jspdf;
const pdf = new jsPDF('p','mm','a4');
const fileName = 'Ledger_<?= esc($account_info['acc_name'] ?? 'Account') ?>_<?= esc(date("Ymd", strtotime($from_date))) ?>_<?= esc(date("Ymd", strtotime($to_date))) ?>.pdf';

/* Capture already-created pages (from PHP) */
const pages = document.querySelectorAll('.page');

if (!pages.length) {
    alert('No pages found for PDF generation');
    return;
}

/* Export each page exactly as layout */
for (let i = 0; i < pages.length; i++) {

    const canvas = await html2canvas(pages[i], {
        scale: 1.4,                 // balanced quality vs size
        useCORS: true,
        backgroundColor: '#ffffff'
    });

    /* Compressed JPEG */
    const imgData = canvas.toDataURL('image/jpeg', 0.75);

    if (i > 0) pdf.addPage();

    pdf.addImage(imgData, 'JPEG', 0, 0, 210, 297);
}

/* Download */
pdf.save(fileName);

/* 🔹 Auto close window after small delay */
setTimeout(() => {
    window.close();
}, 800);

})();
</script>
</body>
</html>