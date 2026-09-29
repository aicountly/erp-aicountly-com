<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eGSTR1</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            width: 210mm;
            margin: 0 auto;
            padding: 0;
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
            background: #fff;
        }
        @page { size: A4; margin: 10mm; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 6px 8px; }
        th { border: 1px solid #ddd; text-align: center; }
        /* remove vertical borders for table cells */
        td { border: none; }
        .header-wrap { border: 1px solid #ddd; margin-bottom: 8px; padding: 8px; }
        .header-line1 { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
        .company-block { text-align: left; min-width: 160px; }
        .company-name { font-size: 18px; font-weight: bold; }
        .company-addr { font-size: 11px; line-height: 1.3; }
        .title-block { text-align: center; flex: 1; }
        .title { font-size: 18px; font-weight: bold; margin: 0; }
        .meta { font-size: 11px; margin: 2px 0; }
        .period { font-weight: bold; }
        .small { font-size: 11px; }
        .tbl-head { background: #c9ece1; font-weight: bold; }
        .section-row { background: #e6f6df; font-weight: bold; }
        .sub-row { background: #f7fbf5; }
        .total-row { font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .btn-dwl { display: flex; justify-content: center; gap: 10px; margin: 12px 0; }
        .pdf-download-btn { padding: 8px 16px; background: #28a745; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
        .pdf-download-btn:hover { background: #218838; }
    </style>
</head>
<body>
<div id="egstr1Content">

    <!-- Header with company and period -->
    <div class="header-wrap">
        <div class="header-line1">
            <div class="company-block">
                <div class="company-name">
                    <?= esc($company_info['cmp_name'] ?? '') ?>
                </div>
                <div class="company-addr">
                    <?= esc($company_address['hobo_addr1'] ?? '') ?>
                    <?= !empty($company_address['hobo_addr2']) ? ', '.esc($company_address['hobo_addr2']) : '' ?><br>
                    <?= esc($company_address['hobo_city'] ?? '') ?>
                    <?= esc($company_address['hobo_pin_zip'] ?? '') ?>
                </div>
            </div>
            <div class="title-block">
                <div class="title">eGSTR-1 Summary</div>
                <div class="meta">
                    GSTIN: <?= esc($gstin ?? ($company_info['gstin'] ?? '')) ?>
                </div>
                <div class="meta period">
                    Period: <?= esc(date('d M Y', strtotime($from_date))) ?> - <?= esc(date('d M Y', strtotime($to_date))) ?>
                </div>
            </div>
            <div class="small" style="text-align:right; min-width:120px;">
                Downloaded on:<br><?= date('d/m/Y H:i') ?>
            </div>
        </div>
    </div>

    <table>
        <thead class="tbl-head">
            <tr>
                <th style="width:30%; text-align:left;">Description</th>
                <th style="width:10%;">No. of records</th>
                <th style="width:12%;">Document Type</th>
                <th style="width:12%;">Value</th>
                <th style="width:12%;">Integrated tax</th>
                <th style="width:12%;">Central tax</th>
                <th style="width:12%;">State/UT tax</th>
                <th style="width:10%;">Cess</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($response['data'])): ?>
            <?php foreach ($response['data'] as $row): ?>
                <?php
                    $exploded = explode("_", $row['tableno']);
                    $isChild  = isset($exploded[1]);
                    $docType  = "Invoice";
                    $desc     = $isChild ? $row['table_name'] : ($row['tableno'].'-'.$row['table_name']);

                    $val  = str_replace('₹', '', $row['taxable_value']);
                    $igst = str_replace('₹', '', $row['igst']);
                    $cgst = str_replace('₹', '', $row['cgst']);
                    $sgst = str_replace('₹', '', $row['sgst']);
                    $cess = str_replace('₹', '', $row['cess']);
                ?>

                <?php if (!$isChild): ?>
                    <tr class="section-row">
                        <td colspan="8" style="text-align:left; font-weight:bold;"><?= esc($desc) ?></td>
                    </tr>
                    <tr class="total-row">
                        <td style="text-align:left;">Total</td>
                        <td class="text-center"><?= esc($row['total_records']) ?></td>
                        <td class="text-center"><?= $docType ?></td>
                        <td class="text-right"><?= $val ?></td>
                        <td class="text-right"><?= $igst ?></td>
                        <td class="text-right"><?= $cgst ?></td>
                        <td class="text-right"><?= $sgst ?></td>
                        <td class="text-right"><?= $cess ?></td>
                    </tr>
                <?php else: ?>
                    <tr class="sub-row">
                        <td style="text-align:left;"><?= esc($row['table_name']) ?></td>
                        <td class="text-center"><?= esc($row['total_records']) ?></td>
                        <td class="text-center"><?= $docType ?></td>
                        <td class="text-right"><?= $val ?></td>
                        <td class="text-right"><?= $igst ?></td>
                        <td class="text-right"><?= $cgst ?></td>
                        <td class="text-right"><?= $sgst ?></td>
                        <td class="text-right"><?= $cess ?></td>
                    </tr>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>


<script>
(async () => {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p','mm','a4');
    const fileName = 'eGSTR1_<?= esc(date("Ymd", strtotime($from_date))) ?>_<?= esc(date("Ymd", strtotime($to_date))) ?>.pdf';
    const target = document.getElementById('egstr1Content');

    const canvas = await html2canvas(target, {
        scale: 2.5,
        useCORS: true,
        backgroundColor: '#ffffff'
    });
    const imgData = canvas.toDataURL('image/png');
    pdf.addImage(imgData, 'PNG', 0, 0, 210, 297);
    pdf.save(fileName);

    setTimeout(() => {
        window.close();
    }, 800);
})();
</script>
</body>
</html>