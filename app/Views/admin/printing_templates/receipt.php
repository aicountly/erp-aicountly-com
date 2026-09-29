<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Receipt</title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
*{ box-sizing:border-box; }

body{
    width:210mm;
    font-family:"Times New Roman", serif;
    font-size:9pt;
    margin:0 auto;
    padding:0;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logodiv{ padding-left:10px; }

.middle{
    padding-top:10px;
    text-align:center;
}

table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
    border:0.5px solid black;
}

th,td{
    border-right:1px solid black;
    padding:3px 8px;
    text-align:left;
}

th{ font-weight:400; }

.pdf-download-btn{
    display:block;
    margin:15px auto;
    padding:10px 25px;
    background:#28a745;
    color:#fff;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

.content-section{
    min-height:70mm;
}
</style>
</head>

<body>

<?php
$voucher  = $voucher_info;
$company  = $company_info;
$address  = $company_address;
$party    = $party_info;
$txns     = $transactions;
$narr     = $narration ?? 'N/A';

/* Split transactions */
$debitTxns  = array_values(array_filter($txns, fn($t) => ($t['drcr'] ?? '') === 'D'));
$creditTxns = array_values(array_filter($txns, fn($t) => ($t['drcr'] ?? '') === 'C'));

/* Calculate amount */
$totalDebit  = 0;
$totalCredit = 0;

foreach($txns as $t){
    $totalDebit  += (float)$t['debit'];
    $totalCredit += (float)$t['credit'];
}

$amount = ($totalDebit > 0) ? $totalDebit : $totalCredit;

/* Logo */
$logoSrc    = base_url('admin/company/logo');
$defaultSrc = base_url('public/assets/img/logo.png');
?>

<div id="ledgerContent" style="margin:2mm auto;">

<!-- HEADER -->
<div class="header-section">

<div class="header">
    <div class="logodiv">
        <img src="<?= $logoSrc ?>" style="width:80px;"
             onerror="this.onerror=null;this.src='<?= $defaultSrc ?>';">
    </div>

    <div class="middle">
        <p style="font-size:12pt;margin:0;"><b>RECEIPT</b></p>
        <p><b style="font-size:20px;"><?= !empty($company['cmp_print_name']) ? esc($company['cmp_print_name']) : '' ?></b><br>
        <?= !empty($address['hobo_addr1']) ? esc($address['hobo_addr1']) . ' ' : '' ?>
<?= !empty($address['hobo_addr2']) ? esc($address['hobo_addr2']) : '' ?><br>
        <?= !empty($address['hobo_city']) ? esc($address['hobo_city']) : '' ?>
<?= (!empty($address['hobo_city']) && !empty($address['hobo_pin_zip'])) ? ' - ' : '' ?>
<?= !empty($address['hobo_pin_zip']) ? esc($address['hobo_pin_zip']) : '' ?></p>
    </div>

    <div>
        <p>GST: <?= esc($address['hobo_gstin'] ?? '') ?></p>
        <p>CIN :</p>
    </div>
</div>

<table>
<tr>
    <th colspan="16" style="border-bottom:1px solid;">
        Receipt No. : <?= esc($voucher['vch_txn_id']) ?>
    </th>
    <th colspan="16" style="border-bottom:1px solid;border-right:none;">
        Dated : <?= date('d-m-Y', strtotime($voucher['vch_date'])) ?>
    </th>
</tr>

<tr>
    <th colspan="2" style="border:none;">Party :</th>
    <th colspan="14" style="border:none;border-right:1px solid;">
        <?php if (count($creditTxns) === 0): ?>
            <?= esc($party['acc_name']) ?>
        <?php else: ?>
            <?php foreach ($creditTxns as $c): ?>
                <?= esc($c['acc_name']) ?> (<?= formatAmount(parseAmount($c['amount'])) ?>)<br>
            <?php endforeach; ?>
        <?php endif; ?>
    </th>
    <th colspan="8" style="border:none;">Party Identification No</th>
    <th colspan="8" style="border:none;">:</th>
</tr>

<tr>
    <th colspan="16" style="border:none;border-right:1px solid;"></th>
    <th colspan="8" style="border:none;">Place of Supply :</th>
    <th colspan="8" style="border:none;">:</th>
</tr>

<tr>
    <th colspan="16" style="border:none;border-right:1px solid;"></th>
    <th colspan="8" style="border:none;">Series :</th>
    <th colspan="8" style="border:none;">: <?= esc($voucher['vch_series_name']) ?></th>
</tr>

<tr>
    <th colspan="16" style="border:none;border-right:1px solid;"></th>
    <th colspan="8" style="border:none;">Amount(Rs.) :</th>
    <th colspan="8" style="border:none;">: ₹ <?= number_format($amount,2) ?></th>
</tr>
</table>

</div>

<!-- BODY -->
<div class="content-section">

<table style="border-top:none;border-bottom:none;">
<tr>
    <th colspan="32" style="border:none;padding:5px 8px;">
        <?php
        if (count($debitTxns) === 0) {
            echo 'Mode : N/A';
        } elseif (count($debitTxns) === 1) {
            echo 'Mode : ' . esc($debitTxns[0]['acc_name']);
        } else {
            $names = array_map(fn($d) => esc($d['acc_name']), $debitTxns);
            echo 'Mode : ' . implode(', ', $names);
        }
        ?>
    </th>
</tr>

<tr>
    <th colspan="32"
        style="border-left:1px solid black;
               border-top:1px solid black;
               padding:5px 8px;
               height:70mm;
               vertical-align: top;">
        Remarks : <?= esc($narr) ?>
    </th>
</tr>
</table>

</div>

<!-- FOOTER -->
<div class="footer-section">

<table>
<tr>
    <td colspan="16" style="border-left:1px solid black;padding:5px 10px;border-right:none;">
        ₹ <?= number_format($amount,2) ?>
    </td>

    <td colspan="16" style="text-align:right;border-right:none;font-weight:bold;">
        For <?= esc($company['cmp_print_name']) ?>
    </td>
</tr>

<tr>
    <td colspan="32" style="border-left:1px solid black;padding:20px 10px;font-weight:bold;">
        <?= ucwords(getIndianCurrency($amount)) ?> Only
    </td>
</tr>

<tr>
    <td colspan="32" style="border-left:1px solid black;font-style:italic;">
        (Cheque Subject to Realisation)
    </td>
</tr>

<tr>
    <td colspan="32" style="border-left:1px solid black;text-align:right;font-style:italic;font-weight:bold;">
        Authorised Signatory
    </td>
</tr>
</table>

</div>

</div>

<script>
(async function(){

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p','mm','a4');

    /* File name (static now, later you can make dynamic) */
    const fileName = 'Receipt_<?= $voucher_txn_id;?>_<?= date("Ymd_His") ?>.pdf';

    const content = document.getElementById('ledgerContent');

    if (!content) {
        alert('Receipt content not found');
        return;
    }

    /* Capture exactly the half-A4 receipt */
    const canvas = await html2canvas(content, {
        scale: 1.6,                 // sharp but controlled size
        useCORS: true,
        backgroundColor: '#ffffff'
    });

    /* Compressed JPEG for small PDF size */
    const imgData = canvas.toDataURL('image/jpeg', 0.75);

    /* Place image on A4 page (top half used automatically) */
    pdf.addImage(imgData, 'JPEG', 0, 0, 210, 148);

    /* Download */
    pdf.save(fileName);

    /* 🔹 Auto close window after download */
    setTimeout(() => {
        window.close();
    }, 800);

})();
</script>


</body>
</html>