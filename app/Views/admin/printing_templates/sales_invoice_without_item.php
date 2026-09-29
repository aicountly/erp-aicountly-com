<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tax Invoice</title>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

  <style>
    @page {
      size: A4;
      margin: 10mm;
    }

    body {
           width:210mm;  
            font-size:9pt;  
            margin:0 auto;  
            padding:0; 
    }

    .invoice {
      width: 210mm;
   
      background: #fff;
      margin: auto;
      border: 1px solid #000;
      box-sizing: border-box;
    }

    .title {
      text-align: center;
      font-weight: bold;
      font-size: 13pt;
      padding: 10px;
      border-bottom: 1px solid #000;
    }

     .top-grid {
    display: grid;
    grid-template-columns: 2.2fr 1.6fr 1fr;
    width: 100%;
    border-bottom: 1px solid #000;
  }

  .box {
    border-right: 1px solid #000;
    padding: 10px;
    line-height: 1.5;
  }

  .middle-box {
    display: grid;
    grid-template-rows: 1fr 1fr;
    border-right: 1px solid #000;
  }

  .right-box {
    display: grid;
    grid-template-rows: 1fr 1fr;
  }

  .top-grid .box:last-child,
  .right-box div:last-child {
    border-right: none;
  }

  .row {
    border-bottom: 1px solid #000;
    padding: 10px;
  }

  .row:last-child {
    border-bottom: none;
  }

    .info-grid {
      display: grid;
      grid-template-columns: 2fr 2fr ;
      border-bottom: 1px solid #000;
    }

    .box {
      border-right: 1px solid #000;
      padding: 6px;
     
      line-height: 1.5;
    }

    .compact div {
  line-height: 1.2;
  margin: 0;
  padding: 0;
}

    .box:last-child {
      border-right: none;
    }

    table {
      width: 100%;
      border-collapse: collapse;
   
    }

    table, th, td {
      border: 1px solid #000;
    }

    th, td {
      padding: 2px 6px;
      text-align: center;
    }

    .hsn-total-wrapper {
      display: flex;
      border-bottom: 1px solid #000;
    }

    .hsn-left {
      width: 65%;
      padding: 10px;
      border-right: 1px solid #000;
    
    }

    .hsn-row {
      display: flex;
      margin-top: 6px;
    }

    .hsn-col { width: 14%; }
    .hsn-col.big { width: 18%; }

    .total-right {
      width: 35%;
      padding: 6px;
     
      line-height: 1.8;
    }

    .grand-total {
   
      font-weight: bold;
      margin-top: 10px;
    }

    .big-empty-box {
      width: 100%;
      height: 470px;
      border-bottom: 1px solid #000;
    }

    .bank-wrapper {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1.5fr;
      width: 100%;
      border-bottom: 1px solid #000;
     
    }

    .bank-box {
      border-right: 1px solid #000;
      padding: 5px;
      line-height: 1.2;
    }

    .bank-box:last-child {
      border-right: none;
      text-align: right;
    }

    .terms-wrapper {
      display: grid;
      grid-template-columns: 2fr 2fr 1.5fr;
      width: 100%;
     
    }

    .terms-box {
      border-right: 1px solid #000;
      padding: 5px;
      line-height: 1.6;
    }

    .terms-box:last-child {
      border-right: none;
    }

 

  /* Style for the download button */
              .pdf-download-btn {
            display: block;
            margin: 10px auto;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            font-size: 14px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .pdf-download-btn:hover {
            background-color: #218838;
        }



        
  table {
    width: 100%;
    border-collapse: collapse;
  }
  td {
    border: 1px solid #000;
    vertical-align: top;
  }


  .compact div {
  line-height: 1.2;
  margin: 0;
  padding: 0;
}
  </style>
</head>

<body>

<div id="ledgerContent">

<div class="invoice"  >

  <div class="title">TAX INVOICE</div>

  <!-- ✅ TOP DETAILS -->


<table style="border-top: none; border-left: none; border-right: none;">

  <!-- ROW 1 -->
  <tr>
    <!-- LEFT BIG BOX -->
    <td rowspan="2" style="width: 15%; text-align: left; border-top: none; border-left: none; border-right: none; ">
      <img src="https://erp.aicountly.com/admin/company/logo" alt="Company Logo" style="width:80px; height:auto; margin-top:4px;">
    </td>
    <td rowspan="2" style="width: 35%; text-align: left; border-top: none; border-left: none; ">
      <b style="font-size:20px;"><?= esc($company_info['cmp_name'] ?? '') ?></b><br>
     GSTIN: #<?= esc($get_comp_taxt_info['hobo_gstin'] ?? '') ?><br>
	<?= esc($company_address['hobo_addr1'] ?? '') ?>, <?= esc($company_address['hobo_addr2'] ?? '') ?><br>
      <?= esc($company_address['hobo_city'] ?? '') ?> - <?= esc($company_address['hobo_pin_zip'] ?? '') ?><br>
      Phone: <?= esc($company_info['cmp_mobile'] ?? '') ?><br>
      Email: <?= esc($company_info['cmp_email'] ?? '') ?>
    </td>

    <!-- MIDDLE TOP BOX -->
       <td style="width: 25%; text-align: left; border-top: none; border-left: none; height: 57px;">
     <b> Date : <?= esc(date('d-m-Y', strtotime($voucher_info['vch_date']))) ?></b><br>
      Reference :<br>
      <b> Invoice No: #<?= esc($gstroutsup_info['outsup_bill_ref_no'] ?? '') ?></b>
    </td>
  <!-- RIGHT TOP BOX (empty box) -->
   <td  rowspan="2"  style="width: 25%; border-right: none;  border-top: none; border-left: none; text-align: center;">
       <p style="margin: 0; font-weight: bold;"> E-Invoice QR Code:</p>  <img src="img/QR.png" alt="" style="width:82px; height:auto; ">
    </td>
  </tr>


  <!-- ROW 2 -->
  <tr>
    <!-- MIDDLE BOTTOM BOX -->
    <td style="text-align: left;   border-top: none; border-left: none;">
      Place of Supply : <?= esc($pos_state_info['state_name'] ?? '') ?> <br>
      Reverse Charge : <?= (isset($gstroutsup_info['outsup_rev_chg']) && $gstroutsup_info['outsup_rev_chg'] == 1) ? 'Yes' : 'No' ?>
    </td>
  </tr>

</table>

 <table style="border-top: none; border-left: none; border-right: none;">

  <!-- ROW 1 -->
  <tr>
    <!-- MIDDLE BOTTOM BOX -->
    <td style="text-align: left;   border-top: none; border-left: none;  border-right: none; ">
      IRN : <?= esc($e_invoice['env_irn'] ?? '') ?>
    </td>
     <td style="text-align: left;   border-top: none; border-left: none; border-right: none; ">
      Ack Date:
     </td>
  </tr>
</table>

  <!-- ✅ CUSTOMER GRID -->
<div class="info-grid">
  <div class="box compact">
    <b>Customer Details :</b>
    <div><?= esc($party_info['acc_name'] ?? 'N/A') ?></div>
	 <div><?= esc($party_address['contact_add1'] ?? 'N/A') ?>, <?= esc($party_address['contact_city'] ?? 'N/A') ?>, <?= esc($party_address['state_name'] ?? 'N/A') ?>,<?= esc($party_address['contact_pin'] ?? 'N/A') ?></div>
    <div>Phone: <?= esc($party_address['contact_mobile'] ?? 'N/A') ?></div>
    <div>Email: <?= esc($party_address['contact_email'] ?? 'N/A') ?></div>
    <div>GSTIN: #<?= esc($party_info['acc_gstin'] ?? '') ?></div>
  </div>
  <div class="box compact">
    <b>Ship To :</b>
    <div>Name: <?= esc($ship_to['shipto_legal_name'] ?? 'N/A') ?></div>
    <div>Phone: </div>
    <div>Email: </div>
  </div>
</div>
  <!-- ✅ ITEMS -->
  <table style="border-top: none; border-left: none; border-right: none; border-bottom: none;">
    <tr>
      <th style="border-top: none; border-left: none;">#</th>
	  <th style="border-top: none; border-left: none; text-align: left;">Particulars</th>
	  <th style="border-top: none; border-left: none;  text-align: left;">Description</th>
      <th style="border-top: none; border-right: none; border-left: none; text-align: right;">₹ Amount</th>
    </tr>
	<?php 	
	 $item_count = is_array($account_transactions) ? count($account_transactions) : 0;
     $taxable_amount = 0;
     $total_qty = 0;
     for ($i = 0; $i < $item_count; $i++){
         $item = $account_transactions[$i];
         $taxable_amount += $item['amount'];
     ?>
    <tr>
      <td style="border-bottom: none; border-left: none ; border-top: none;"><?= $i + 1 ?></td>
	  <td style="border-bottom: none;border-left: none ; border-top: none; text-align: left;"><?= esc($item['account_name']) ?></td>
	  <td style="border-bottom: none; border-left: none ; border-top: none; text-align: left;"><?= esc($item['description']) ?></td>
      <td style="border-bottom: none; border-right: none; border-left: none ; border-top: none; text-align: right;"><?= formatAmount($item['amount']) ?></td>
    </tr>
	<?php } ?>    
  </table>

  <!-- ✅ TOTAL ITEMS + TAXABLE AMOUNT FIXED -->
  <div style="
    display:flex;
    width:100%;
    border:1px solid #000;
    border-left: none;
    border-right: none;
    font-weight:bold;
  ">
    <div style="width:70%; padding:6px 10px;">
      Total Particulars :<?= $item_count ?>
    </div>

    <div style="width:30%; padding:6px 10px; display:flex; justify-content:space-between;">
      <span>Taxable Amount</span>
      <span><?= formatAmount($taxable_amount) ?></span>
    </div>
  </div>

  <!-- ✅ HSN + TOTAL -->
  <div class="hsn-total-wrapper">
    <div class="hsn-left">
      <b>HSN Summary</b><br><br>

      <div class="hsn-row" style="font-weight:bold; text-align: center;">
        <div class="hsn-col">HSN/SAC</div>
        <div class="hsn-col big">TAX RATE</div>
        <div class="hsn-col">IGST</div>
        <div class="hsn-col">CGST</div>
        <div class="hsn-col">SGST</div>
        <div class="hsn-col">CESS</div>
        <div class="hsn-col big">TOTAL TAX</div>
      </div>
	<?php 
		$total_tax_value = 0;
		$total_igst = 0;
		$total_cgst = 0;
		$total_sgst = 0;
		$total_cess = 0;
                    if(is_array($tax_summary)){
                        foreach($tax_summary as $tax){ 
                            $igst = parseAmount($tax['vch_igst'] ?? 0);
							$cgst = parseAmount($tax['vch_cgst'] ?? 0);
							$sgst = parseAmount($tax['vch_sgst_ugst'] ?? 0);
							$cess = parseAmount($tax['vch_cess'] ?? 0);
							$tax_rate = parseAmount($tax['tax_rate'] ?? 0);
							$total_tax_value += parseAmount($tax['vch_total_tax'] ?? 0);
							$total_igst += $igst;
						    $total_cgst += $cgst;
							$total_sgst += $sgst;
							$total_cess += $cess;
							?>	
      <div class="hsn-row" style="text-align: center;">
            <div class="hsn-col"><?= esc($tax['vch_hsn_sac'] ?? '') ?></div>
            <div class="hsn-col" style="text-align: center;"><?= parseAmount($tax_rate) ?>%</div>
            <div class="hsn-col" style="text-align: center;">
                <?= $igst > 0 ? formatAmount($igst) : '' ?>
            </div>
            <div class="hsn-col" style="text-align: center;">
                <?= $cgst > 0 ? formatAmount($cgst) : '' ?>
            </div>
            <div class="hsn-col" style="text-align: center;">
                <?= $sgst > 0 ? formatAmount($sgst) : '' ?>
            </div> 
			<div class="hsn-col" style="text-align: center;">
                <?= $total_cess > 0 ? formatAmount($cess) : '' ?>
            </div>
            <div class="hsn-col big" style="text-align: center;">
                <?= formatAmount($tax['vch_total_tax']) ?>
            </div>
        </div>
	   <?php }} ?>
    </div>

    <div class="total-right">
    <?php if ($total_igst > 0): ?>
        IGST : ₹<?= number_format($total_igst, 2) ?><br>
    <?php endif; ?>
    <?php if ($total_cgst > 0): ?>
        CGST : ₹<?= number_format($total_cgst, 2) ?><br>
    <?php endif; ?>
    <?php if ($total_sgst > 0): ?>
        SGST : ₹<?= number_format($total_sgst, 2) ?><br>
    <?php endif; ?>
	<?php if ($total_cess > 0): ?>
        CESS : ₹<?= number_format($total_cess, 2) ?><br>
    <?php endif; ?>

    <div class="grand-total">TOTAL  <?= formatAmount($taxable_amount + $total_tax_value) ?></div>
    <?php echo  getIndianCurrency(parseAmount($taxable_amount + $total_tax_value));?>
</div>
  </div>

  <!-- ✅ BIG EMPTY -->
  <div class="big-empty-box"></div>

<div class="footer">
    <!-- ✅ BANK -->
  <div class="bank-wrapper">
     <div class="bank-box compact">
    <b>Bank Details :</b>
    <div>Bank: <?= esc($bank_details['bank_name'] ?? '') ?></div>
    <div>Account: <?= esc($bank_details['bank_acc_no'] ?? '') ?></div>
    <div>IFSC: <?= esc($bank_details['bank_ifsc'] ?? '') ?></div>
    <div>Branch: <?= esc($bank_details['bank_branch'] ?? '') ?></div>
  </div>
     <div class="bank-box" style="text-align:center;"><b>E-way QR Code:</b> <img src="img/QR.png" alt="" style="width:68px; height:auto; "></div>
    <div class="bank-box" style="text-align:center;"><b>Pay Online/UPI:</b>  <img src="img/QR.png" alt="" style="width:68px; height:auto; "></div>
    <div class="bank-box"> <b>For <?= esc($company_info['cmp_name'] ?? '') ?></b><br><br><br><b>Authorised Signatory</b></div>
  </div>

  <!-- ✅ TERMS -->
  <div class="terms-wrapper">
    <div class="terms-box"> 
      <b>Terms & Conditions:</b><br>
   <p style="padding: 0; margin: 0; line-height: 1.1;">  We declare that this invoice shows the actual price of
      the goods & services described and that all
      particulars are true and correct.</p>
    </div>
    <div class="terms-box"><b>Customer Sign:</b></div>
    <div class="terms-box"><b>Notes:</b><br><p style="margin: 0; padding: 0; line-height: 1.1;">Thankyou for dealing with our company.</p></div>
  </div>
</div>


</div>

</div>
  
<script>
 window.addEventListener('load', function () {

    const { jsPDF } = window.jspdf;
    let doc = new jsPDF('p', 'mm', 'a4');

    html2canvas(document.getElementById('ledgerContent'), {
        scale: 2,
        useCORS: true
    }).then(canvas => {

        let imgData = canvas.toDataURL('image/png');
        let imgWidth = 210;   // A4 width in mm
        let pageHeight = 297; // A4 height in mm
        let imgHeight = (canvas.height * imgWidth) / canvas.width;

        let heightLeft = imgHeight;
        let position = 0;

        // First page
        doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // Extra pages if required
        while (heightLeft > 0) {
            position = heightLeft - imgHeight;
            doc.addPage();
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        // ✅ Auto-download
       doc.save('Sale-Invoice-<?php echo $voucher_txn_id; ?>.pdf');

        // ✅ Auto-close (ALLOWED because window was JS-opened)
        window.close();

    });

});
</script>

</body>
</html>
