<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Balance Sheet</title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
body{
    width:210mm;
    margin:0 auto;
    font-family:"Times New Roman", serif;
    font-size:10pt;
    color:#000;
}

table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
}

/* ================= HEADER ================= */

.header-table td{
    border:none;
    vertical-align:top;
}

.title{
    font-size:15pt;
    font-weight:bold;
    text-align:center;
}

.sub-title{
    text-align:center;
    font-size:11pt;
    line-height:1.4;
}

/* ================= MAIN TABLE ================= */

.bs-table{
    border:1px solid #000;   /* OUTER BORDER ONLY */
}

.bs-table th{
    border-bottom:1px solid #000;
    border-right:1px solid #000;
    font-weight:bold;
    text-align:center;
    padding:6px 4px;
}

.bs-table th:last-child{ border-right:none; }

.bs-table td{
    padding:5px 8px;
    vertical-align:top;
}

/* vertical separators only */
.col-left{ border-right:1px solid #000; }
.col-mid{ border-right:1px solid #000; text-align:right; white-space:nowrap; }
.col-right{ border-right:1px solid #000; }

.amount{
    text-align:right;
    white-space:nowrap;
}

.section{ font-weight:bold; }
.sub{ padding-left:16px; }

/* Total row */
.total-row td{
    border-top:1px solid #000;
    font-weight:bold;
    padding-top:7px;
    padding-bottom:7px;
}

/* ================= FOOTER ================= */

.footer{
    margin-top:12px;
}

.footer td{
    border:none;
    padding:12px 5px;
}
</style>
</head>

<body>

<div id="ledgerContent">

<!-- ================= HEADER ================= -->
<table class="header-table">
<tr>
    <td width="15%">
        <img src="<?= base_url('admin/company/logo') ?>" style="width:90px;"
             onerror="this.src='<?= base_url('public/assets/img/logo.png') ?>';">
    </td>

    <td width="70%" class="sub-title">
        <div class="title">BALANCE SHEET</div>
        <div><b><?= esc($company_info['cmp_name'] ?? '') ?></b></div>
        <div>
            <?= esc($company_address['hobo_addr1'] ?? '') ?>
            <?= isset($company_address['hobo_addr2']) ? ', '.esc($company_address['hobo_addr2']) : '' ?><br>
            <?= esc($company_address['hobo_city'] ?? '') ?>
            <?= esc($company_address['hobo_pin_zip'] ?? '') ?>
        </div>
        <div style="margin-top:8px;">At the end of <?= date('d/m/Y', strtotime($to_date)) ?></div>
    </td>

    <td width="15%" style="text-align:right;">
        GSTIN : <?= esc($gstin ?? '') ?><br>
        B.O. : <?= esc($bo_name ?? '') ?><br>
        CIN : <?= esc($company_info['cin'] ?? '') ?>
    </td>
</tr>
</table>

<br>

<!-- ================= MAIN BALANCE SHEET ================= -->
<table class="bs-table">
<thead>
<tr>
    <th width="38%" class="col-left">LIABILITIES</th>
    <th width="12%" class="col-mid">Amt. ₹</th>
    <th width="38%" class="col-right">ASSETS</th>
    <th width="12%">Amt. ₹</th>
</tr>
</thead>
<tbody>

<?php
$final_l_total = 0;
$final_r_total = 0;

foreach($data as $row):

    $l_amt  = (float)($row['l_balance_total'] ?? 0);
    $r_amt  = (float)($row['r_balance_total'] ?? 0);

    // Detect the INTERNAL BALANCE ROW (both sides filled but no group name)
    if($l_amt != 0 && $r_amt != 0 && empty(trim($row['l_group_name'])) && empty(trim($row['r_group_name']))){
        // Store final total but DO NOT PRINT THIS ROW
        $final_l_total = $l_amt;
		 $final_r_total = $r_amt;
        continue;
    }
	

    // clean names
    $l_name = trim(str_replace(['&nbsp;', '»'], '', $row['l_group_name'] ?? ''));
    $r_name = trim(str_replace(['&nbsp;', '»'], '', $row['r_group_name'] ?? ''));

    $l_name = preg_replace('/<.*?>/', '', $l_name);
    $r_name = preg_replace('/<.*?>/', '', $r_name);

    $l_disp = $l_amt != 0 ? formatAmount($l_amt,2) : '';
    $r_disp = $r_amt != 0 ? formatAmount($r_amt,2) : '';

    $l_class = in_array($row['l_type'] ?? '', ['prt','pl']) ? 'section' : 'sub';
    $r_class = in_array($row['r_type'] ?? '', ['prt','pl']) ? 'section' : 'sub';
?>

<tr>
    <td class="col-left <?= $l_class ?>"><?= esc($l_name) ?></td>
    <td class="col-mid amount"><?= $l_disp ?></td>
    <td class="col-right <?= $r_class ?>"><?= esc($r_name) ?></td>
    <td class="amount"><?= $r_disp ?></td>
</tr>

<?php endforeach; ?>

<!-- ================= FINAL TOTAL (ONLY ONCE) ================= -->
<tr class="total-row">
    <td class="col-left">Total</td>
    <td class="col-mid amount"><?= formatAmount($final_l_total,2) ?></td>
    <td class="col-right">Total</td>
    <td class="amount"><?= formatAmount($final_r_total,2) ?></td>
</tr>

</tbody>
</table>

<!-- ================= FOOTER ================= -->
<table class="footer">
<tr>
    <td width="50%">
        Date : <?= date('d/m/Y') ?><br><br>
        Place :
    </td>
    <td width="50%" style="text-align:right;">
        For <?= esc($company_info['cmp_name'] ?? 'Company Name') ?><br><br><br>
        <i>Authorised Signatory</i>
    </td>
</tr>
</table>

</div>

<!-- ================= AUTO PDF ================= -->
<script>
(async function(){

 const { jsPDF } = window.jspdf;
 let doc = new jsPDF('p', 'mm', 'a4');

 html2canvas(document.getElementById('ledgerContent'), {
     scale: 2
 }).then(canvas => {
     let imgData = canvas.toDataURL('image/png');
     let imgWidth = 210;
     let pageHeight = 297;
     let imgHeight = (canvas.height * imgWidth) / canvas.width;
     let heightLeft = imgHeight;
     let position = 0;

     doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
     heightLeft -= pageHeight;

     while (heightLeft > 0) {
         position = heightLeft - imgHeight;
         doc.addPage();
         doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
         heightLeft -= pageHeight;
     }

     doc.save('Balance_Sheet.pdf');
 });

})();
</script>

</body>
</html>
