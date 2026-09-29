<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>BALANCE SHEET DETAILED</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            width: 210mm;
            font-family: "Times New Roman", Times, serif;
            font-size: 8.5pt;
            margin: 0 auto;
            padding: 0;
            color:#000;
            background:#fff;
        }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; }
        .header div { display: flex; flex-direction: column; justify-content: center; }
        .date { text-align: center; padding: 2px 0; }

        /* Top headings (LIABILITIES/ASSETS) 5-column grid with a 0-width center separator */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border-left: 1px solid #000;   /* outer frame start */
            border-right: 1px solid #000;  /* outer frame end */
            background:#fff;
        }
        .main-table th {
            border-top: 1px solid #000;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 0;
            padding: 4px 8px;
            vertical-align: top;
            font-weight: bold;
            text-align: center;
            background:#fff;
        }

        /* Body grid (keeps outer verticals + bottom border under totals) */
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border-left: 1px solid #000;    /* outer frame */
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;  /* bottom frame (under totals row) */
            background:#fff;
        }
        .grid-table td, .grid-table th {
            border-left: 1px solid #000;    /* keep verticals continuous (outer columns) */
            border-right: 1px solid #000;
            padding: 0;
            vertical-align: top;
            background:#fff;
        }

        /* THINNER single center separator line (avoid double borders) */
        .center-sep {
            padding:0 !important;
            width:0 !important;                 /* visually a line only */
            border-left: 0.6px solid #000 !important;  /* thinner line */
            border-right: 0 !important;         /* ensure single line (not doubled) */
        }
        .no-right { border-right: 0 !important; }
        .no-left  { border-left: 0 !important; }

        /* Each side (left/right) gets its OWN full-height inner divider at 70% (name/amount split) */
        .side { position: relative; }
        .side::after {
            content: "";
            position: absolute;
            top: 0; bottom: 0;
            left: 70%;                   /* 35% name vs 15% amount inside 50% width => 70% split */
            border-left: 0.6px solid #000; /* inner vertical (slightly thinner) */
            pointer-events: none;
        }

        /* Totals row (one real table row -> borders align and continue) */
        .totals-row td {
            padding: 3px 8px !important;
            border-top: 1px solid #000;    /* line above totals */
            background:#fff;
        }
        .label-cell  { text-align: left; }
        .amount-cell { text-align: right; font-weight: bold; }

        /* Inner listing tables */
        .sub-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .sub-table td { padding: 2px 8px; border: none; vertical-align: top; }
        .sub-table td:nth-child(2) { text-align: right; }

        /* Group headings styling (primary parents bold only; groups/bal/pl/inv underlined) */
        .title-marker   { font-weight: bold; text-decoration: none; padding-right: 2px; }
        .title-bold     { font-weight: bold; text-decoration: none; color: #000; white-space: pre-wrap; }
        .title-underline{ font-weight: bold; text-decoration: underline; color: #000; white-space: pre-wrap; }

        /* Account lines: small and indented with italic amount */
        .acc-name { font-size: 7pt; padding-left: 14px; white-space: pre-wrap; }
        .acc-amt  { font-style: italic; }

        /* Footer signature block (outside grid bottom border) */
        .footer { display: flex; justify-content: space-between; }
        .child-footer { display: flex; flex-direction: column; padding: 10px; width: 50%; gap: 10px; }
        .right { text-align: right; }

        /* Download button */
        .pdf-download-btn {
            display: block; padding: 10px 20px; background-color: #28a745;
            color: #fff; font-size: 14px; border: none; cursor: pointer; border-radius: 5px;
        }
        .pdf-download-btn:hover { background-color: #218838; }

        @media print { table { border: 0.5px solid #000 !important; } }
        .btn-dwl { display: flex; justify-content: center; gap: 10px; }
        p { margin: 0; }
    </style>
</head>
<body>
<?php
    /* Amount formatter */
    if (!function_exists('fmtAmt')) {
        function fmtAmt($rawStr, $rawNum = null) {
            if ($rawStr !== null && $rawStr !== '') return $rawStr;               // already formatted (₹..)
            if ($rawNum === null || $rawNum === '' || !is_numeric($rawNum)) return '';
            return number_format((float)$rawNum, 2, '.', ',');
        }
    }
    /* Decode/clean names; keep arrows/nbsp spacing */
    if (!function_exists('renderName')) {
        function renderName($s) {
            $s = html_entity_decode((string)$s, ENT_QUOTES, 'UTF-8');
            $s = strip_tags($s);
            return $s;
        }
    }
    /* Split leading marker (» »») from the rest so marker is NOT underlined */
    if (!function_exists('splitTitleMarker')) {
        function splitTitleMarker($s) {
            $s = renderName($s);
            if (preg_match('/^\s*(»+)\s*(.*)$/u', $s, $m)) {
                return [$m[1] . ' ', $m[2]];
            }
            return ['', $s];
        }
    }

    // Resolve incoming rows (support both $data and $data['data'])
    $rows = [];
    if (isset($data) && is_array($data)) {
        $rows = array_key_exists(0, $data) ? $data : (is_array($data['data'] ?? null) ? $data['data'] : []);
    }

    // Split into Left (Liabilities) and Right (Assets)
    $leftRows  = [];
    $rightRows = [];
    foreach ($rows as $r) {
        if (!empty($r['l_group_name']) || !empty($r['l_type'])) { $leftRows[]  = $r; }
        if (!empty($r['r_group_name']) || !empty($r['r_type'])) { $rightRows[] = $r; }
    }

    // Totals
    $sumTypes   = ['prt', 'grp', 'bal', 'pl', 'inv'];
    $leftTotal  = 0.0;
    $rightTotal = 0.0;
    foreach ($leftRows as $lr)  { if (in_array($lr['l_type'] ?? '', $sumTypes, true))  { $leftTotal  += (float)($lr['l_balance_total'] ?? 0); } }
    foreach ($rightRows as $rr) { if (in_array($rr['r_type'] ?? '', $sumTypes, true)) { $rightTotal += (float)($rr['r_balance_total'] ?? 0); } }

    // Header info
    $from_date = $from_date ?? ($data['from_date'] ?? '');
    $to_date   = $to_date   ?? ($data['to_date']   ?? '');
    $cmpName   = $company_info['cmp_name'] ?? '';
    $addr1     = $company_address['hobo_addr1'] ?? '';
    $addr2     = $company_address['hobo_addr2'] ?? '';
    $city      = $company_address['hobo_city'] ?? '';
    $pin       = $company_address['hobo_pin_zip'] ?? '';
    $gstinVal  = $gstin ?? '';
    $boName    = $bo_name ?? '';
    $cinVal    = $company_info['cmp_cin'] ?? '';
?>
<div id="ledgerContent" style="margin: 2mm auto;">

    <!-- Header -->
    <div class="header">
        <div>
            <?php
                $logoSrc    = base_url('admin/company/logo');
                $defaultSrc = base_url('public/assets/img/logo.png');
            ?>
            <img src="<?= $logoSrc ?>" alt="Company Logo" style="width:80px; height:auto; margin-top:4px;"
                 onerror="this.onerror=null; this.src='<?= $defaultSrc ?>';" />
        </div>
        <div style="text-align:center;">
            <div style="font-size: 12pt; font-weight: bold;">BALANCE SHEET</div>
            <p style="margin: 10px;">
                <b style="font-size: 20px;"><?= esc($cmpName) ?></b><br/>
                <?= esc($addr1) ?><?= $addr2 ? ', ' . esc($addr2) : '' ?><br/>
                <?= esc($city) ?> <?= esc($pin) ?>
            </p>
        </div>
        <div style="margin-right:50px; text-align:right;">
            <div>GSTIN: <?= esc($gstinVal) ?></div>
            <div>B.O.: <?= esc($boName) ?></div>
            <div>CIN: <?= esc($cinVal) ?></div>
        </div>
    </div>

    <div class="date">
        <p>At the end of <?= $to_date ? esc(date('d/m/Y', strtotime($to_date))) : '' ?></p>
    </div>

    <!-- Column headers (5 columns including a 0-width center separator) -->
    <table class="main-table">
        <colgroup>
            <col style="width:35%">
            <col style="width:15%">
            <col style="width:0%">     <!-- center separator -->
            <col style="width:35%">
            <col style="width:15%">
        </colgroup>
        <thead>
            <tr>
                <th>LIABILITIES</th>
                <th class="no-right">Amt. ₹</th>
                <th class="center-sep"></th>
                <th class="no-left">ASSETS</th>
                <th>Amt. ₹</th>
            </tr>
        </thead>
    </table>

    <!-- Body grid with same 5-column structure -->
    <table class="grid-table">
        <colgroup>
            <col style="width:35%">
            <col style="width:15%">
            <col style="width:0%">     <!-- center separator -->
            <col style="width:35%">
            <col style="width:15%">
        </colgroup>
        <tbody>
            <tr>
                <!-- LEFT two columns -->
                <td colspan="2" class="side no-right" style="padding:0;">
                    <table class="sub-table">
                        <?php foreach ($leftRows as $row): ?>
                            <?php
                                $lType   = $row['l_type'] ?? '';
                                $rawName = $row['l_group_name'] ?? '';
                                [$marker,$plain] = splitTitleMarker($rawName);
                                $lStyle  = (string)($row['l_style'] ?? ($row['pq_cellattr']['l_group_name']['style'] ?? ''));

                                $accStr  = (string)($row['l_detail'] ?? $row['l_balance'] ?? '');
                                $grpStr  = (string)($row['l_balance'] ?? '');
                                $grpNum  = $row['l_balance_total'] ?? null;

                                $isHeader   = in_array($lType, ['prt','grp','bal','pl','inv'], true);
                                $isPrimary  = ($lType === 'prt');              // primary parent -> bold only (no underline)
                                $isUnderline= in_array($lType, ['grp','bal','pl','inv'], true); // underline for groups/pl/bal/inv
                                $isAcc      = ($lType === 'acc');
                            ?>
                            <?php if ($isHeader): ?>
                                <tr>
                                    <td style="padding:5px 8px; <?= esc($lStyle) ?>">
                                        <?php if ($marker): ?><span class="title-marker"><?= $marker ?></span><?php endif; ?>
                                        <span class="<?= $isUnderline ? 'title-underline' : 'title-bold' ?>"><?= $plain ?></span>
                                    </td>
                                    <td style="padding:5px 8px;"></td>
                                    <td style="padding:5px 8px; text-align:right;"><strong><?= fmtAmt($grpStr, $grpNum) ?></strong></td>
                                </tr>
                            <?php elseif ($isAcc): ?>
                                <tr>
                                    <td class="acc-name"><?= renderName($rawName) ?></td>
                                    <td class="acc-amt"><?= $accStr ?></td>
                                    <td></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td style="padding:2px 8px; <?= esc($lStyle) ?>"><?= renderName($rawName) ?></td>
                                    <td></td>
                                    <td style="text-align:right;"><?= fmtAmt($grpStr, $grpNum) ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </table>
                </td>

                <!-- CENTER separator cell (0-width, renders as single thin vertical line) -->
                <td class="center-sep"></td>

                <!-- RIGHT two columns -->
                <td colspan="2" class="side no-left" style="padding:0;">
                    <table class="sub-table">
                        <?php foreach ($rightRows as $row): ?>
                            <?php
                                $rType   = $row['r_type'] ?? '';
                                $rawName = $row['r_group_name'] ?? '';
                                [$marker,$plain] = splitTitleMarker($rawName);
                                $rStyle  = (string)($row['r_style'] ?? ($row['pq_cellattr']['r_group_name']['style'] ?? ''));

                                $accStr  = (string)($row['r_detail'] ?? $row['r_balance'] ?? '');
                                $grpStr  = (string)($row['r_balance'] ?? '');
                                $grpNum  = $row['r_balance_total'] ?? null;

                                $isHeader   = in_array($rType, ['prt','grp','bal','pl','inv'], true);
                                $isPrimary  = ($rType === 'prt');
                                $isUnderline= in_array($rType, ['grp','bal','pl','inv'], true);
                                $isAcc      = ($rType === 'acc');
                            ?>
                            <?php if ($isHeader): ?>
                                <tr>
                                    <td style="padding:5px 8px; <?= esc($rStyle) ?>">
                                        <?php if ($marker): ?><span class="title-marker"><?= $marker ?></span><?php endif; ?>
                                        <span class="<?= $isUnderline ? 'title-underline' : 'title-bold' ?>"><?= $plain ?></span>
                                    </td>
                                    <td style="padding:5px 8px;"></td>
                                    <td style="text-align:right;"><strong><?= fmtAmt($grpStr, $grpNum) ?></strong></td>
                                </tr>
                            <?php elseif ($isAcc): ?>
                                <tr>
                                    <td class="acc-name"><?= renderName($rawName) ?></td>
                                    <td class="acc-amt" style="text-align:right;"><?= $accStr ?></td>
                                    <td></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td style="padding:2px 8px; <?= esc($rStyle) ?>"><?= renderName($rawName) ?></td>
                                    <td></td>
                                    <td style="text-align:right;"><?= fmtAmt($grpStr, $grpNum) ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </table>
                </td>
            </tr>
        </tbody>

        <!-- Bottom totals row with full borders (includes center separator cell) -->
        <tfoot>
            <tr class="totals-row">
                <td class="label-cell">Total</td>
                <td class="amount-cell no-right"><?= fmtAmt('', $leftTotal) ?></td>
                <td class="center-sep"></td>
                <td class="label-cell no-left">Total</td>
                <td class="amount-cell"><?= fmtAmt('', $rightTotal) ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer (below the grid bottom border) -->
    <table class="sub-table" style="width:100%; margin-top:6px;">
        <tr>
            <td style="padding:0;">
                <div class="footer">
                    <div class="child-footer" style="border-right:none;">
                        <div>Date: </div>
                        <div>Place: </div>
                    </div>
                    <div class="child-footer right" style="line-height:25px;">
                        <div style="font-size:9.5pt; font-weight:bold;">For <?= esc($cmpName ?: 'Company Name') ?></div>
                        <div style="font-size:9.5pt; font-style:italic; font-weight:bold; margin-top:15px;">Authorised Signatory</div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="btn-dwl">
    <button class="pdf-download-btn" id="downloadPdf">Download PDF</button>
</div>

<script>
//document.getElementById('downloadPdf').addEventListener('click', function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');

    html2canvas(document.getElementById('ledgerContent'), { scale: 2 }).then(canvas => {
        const imgData    = canvas.toDataURL('image/png');
        const imgWidth   = 210;
        const pageHeight = 297;
        const imgHeight  = (canvas.height * imgWidth) / canvas.width;
        let heightLeft   = imgHeight;
        let position     = 0;

        doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            doc.addPage();
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }
        doc.save('Balance_Sheet.pdf');
    });
//});
</script>
</body>
</html>