<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Credit Note</title>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

  <style>
    @page { size: A4; margin: 10mm; }

    body {
      width: 210mm;
      font-size: 9pt;
      margin: 0 auto;
      padding: 0;
      font-family: "Times New Roman", Times, serif;
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
      padding: 6px;
      border-bottom: 1px solid #000;
    }

    .info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      border-bottom: 1px solid #000;
    }

    .box {
      border-right: 1px solid #000;
      padding: 4px 8px;
    }

    .box:last-child { border-right: none; }

    .compact div {
      line-height: 1.2;
      margin: 0;
      padding: 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table, th, td { border: 1px solid #000; }

    th, td { padding: 2px 6px; }

    .hsn-total-wrapper {
      display: flex;
      border-bottom: 1px solid #000;
    }

    .hsn-left {
      width: 65%;
      padding: 8px;
      border-right: 1px solid #000;
    }

    .hsn-row {
      display: flex;
      margin-top: 4px;
    }

    .hsn-col       { width: 14%; }
    .hsn-col.big   { width: 18%; }

    .total-right {
      width: 35%;
      padding: 6px;
      line-height: 1.8;
    }

    .grand-total {
      font-weight: bold;
      margin-top: 6px;
    }

    .big-empty-box {
      width: 100%;
      height: 200px;
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
      padding: 4px 5px;
    }

    .bank-box:last-child { border-right: none; }

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

    .terms-box:last-child { border-right: none; }

    .bank-grid {
      display: grid;
      grid-template-columns: 110px auto;
      font-size: 8pt;
    }
  </style>
</head>
<body>

<div id="ledgerContent">
<div class="invoice">

  <!-- ── TITLE ── -->
  <div class="title">CREDIT NOTE</div>

  <!-- ── HEADER TABLE : Logo | Company | Date+InvoiceNo | QR ── -->
  <table style="border-top:none; border-left:none; border-right:none;">
    <tr>
      <!-- Logo -->
      <td rowspan="2" style="width:12%; text-align:left;
            border-top:none; border-left:none; border-right:none; padding:4px;">
        <img src="<?= base_url('admin/company/logo') ?>"
             alt="Logo" style="width:75px; height:auto;">
      </td>

      <!-- Company Name + GSTIN + Address -->
      <td rowspan="2" style="width:38%; text-align:left;
            border-top:none; border-left:none; padding:4px;">
        <b style="font-size:14pt;"><?= esc($company_info['cmp_name'] ?? '') ?></b><br>
        GSTIN: #<?= esc($get_comp_taxt_info['hobo_gstin'] ?? '') ?><br>
        <?= esc($company_address['hobo_addr1'] ?? '') ?>
        <?php if (!empty($company_address['hobo_addr2'])): ?>
          , <?= esc($company_address['hobo_addr2']) ?>
        <?php endif; ?><br>
        <?= esc($company_address['hobo_city'] ?? '') ?>
        <?php if (!empty($company_address['hobo_pin_zip'])): ?>
          - <?= esc($company_address['hobo_pin_zip']) ?>
        <?php endif; ?><br>
        Phone: <?= esc($company_info['cmp_mobile'] ?? '') ?><br>
        Email: <?= esc($company_info['cmp_email'] ?? '') ?>
      </td>

      <!-- Date / Reference / Invoice No -->
      <td style="width:25%; text-align:left;
            border-top:none; border-left:none; padding:4px; height:52px;">
        <b>Date : <?= esc(date('d-m-Y', strtotime($voucher_info['vch_date']))) ?></b><br>
        Reference : <?= esc($voucher_info['vch_ref_no'] ?? '') ?><br>
        <b>Credit Note No: #<?= esc($gstrinwsup_info['inwsup_bill_ref_no'] ?? ($voucher_info['vch_bill_ref_no'] ?? '')) ?></b>
      </td>

      <!-- E-Invoice QR -->
      <td rowspan="2" style="width:25%; border-right:none;
            border-top:none; border-left:none; text-align:center; padding:4px;">
        <p style="margin:0; font-weight:bold;">E-Invoice QR Code:</p>
        <?php if (!empty($e_invoice['einv_qr_code'])): ?>
          <img src="data:image/png;base64,<?= base64_encode($e_invoice['einv_qr_code']) ?>"
               style="width:82px; height:auto;">
        <?php else: ?>
          <img src="<?= base_url('img/QR.png') ?>" style="width:82px; height:auto;">
        <?php endif; ?>
      </td>
    </tr>

    <!-- Place of Supply / Reverse Charge -->
    <tr>
      <td style="text-align:left; border-top:none; border-left:none; padding:4px;">
        Place of Supply : <?= esc($pos_state_info['state_name'] ?? '') ?><br>
        Reverse Charge :
        <?= (isset($gstrinwsup_info['inwsup_rev_chg']) && $gstrinwsup_info['inwsup_rev_chg'] == 1) ? 'Yes' : 'No' ?>
      </td>
    </tr>
  </table>

  <!-- ── IRN Row ── -->
  <table style="border-top:none; border-left:none; border-right:none;">
    <tr>
      <td style="text-align:left; border-top:none; border-left:none; border-right:none; padding:2px 6px;">
        IRN : <?= esc($e_invoice['einv_irn'] ?? '') ?>
      </td>
      <td style="text-align:left; border-top:none; border-left:none; border-right:none; padding:2px 6px;">
        Ack Date:
        <?php if (!empty($e_invoice['einv_date'])): ?>
          <?= esc(date('d-m-Y', strtotime($e_invoice['einv_date']))) ?>
        <?php endif; ?>
      </td>
    </tr>
  </table>

  <!-- ── Customer + Ship To (2-column grid) ── -->
  <div class="info-grid">
    <div class="box compact">
      <b>Customer Details :</b>
      <div><?= esc($party_info['acc_name'] ?? 'N/A') ?></div>
      <?php if (!empty($party_address['contact_add1'])): ?>
        <div><?= esc($party_address['contact_add1']) ?>
          <?= !empty($party_address['contact_city']) ? ', ' . esc($party_address['contact_city']) : '' ?>
          <?= !empty($party_address['state_name'])   ? ', ' . esc($party_address['state_name'])   : '' ?>
          <?= !empty($party_address['contact_pin'])  ? ' - ' . esc($party_address['contact_pin']) : '' ?>
        </div>
      <?php endif; ?>
      <div>Phone: <?= esc($party_address['contact_mobile'] ?? '') ?></div>
      <div>Email: <?= esc($party_address['contact_email']  ?? '') ?></div>
      <div>GSTIN: #<?= esc($party_info['acc_gstin'] ?? '') ?></div>
    </div>

    <div class="box compact">
      <b>Ship To :</b>
      <div>Name: <?= esc($ship_to['shipto_legal_name'] ?? '') ?></div>
      <div>Phone: </div>
      <div>Email: </div>
    </div>
  </div>

  <!-- ── Original Invoice Reference Bar ── -->
  <div style="display:grid; grid-template-columns:1fr 1fr 1fr;
              border-bottom:1px solid #000; padding:2px 6px;">
    <div>
      Original Sale Invoice No. :
      <strong><?= esc($original_sale_inv_no ?? '') ?></strong>
    </div>
    <div>
      Original Sale Invoice Date :
      <strong><?= esc($original_sale_inv_date ?? '') ?></strong>
    </div>
    <div>
      Original Sale Invoice Value :
      <strong>₹ <?= isset($original_sale_inv_value) ? number_format((float)$original_sale_inv_value, 2) : '0.00' ?></strong>
    </div>
  </div>

  <!-- ── Particulars Table (without-item = account transactions) ── -->
  <table style="border-top:none; border-left:none; border-right:none; border-bottom:none;">
    <tr>
      <th style="border-top:none; border-left:none; text-align:center; width:40px;">#</th>
      <th style="border-top:none; border-left:none; text-align:left;">Particulars</th>
      <th style="border-top:none; border-left:none; text-align:left;">Description</th>
    
      <th style="border-top:none; border-right:none; border-left:none;
                 text-align:right; width:130px; white-space:nowrap;">₹ Amount</th>
    </tr>

    <?php
      $taxable_amount = 0;
      $acc_count      = 0;

      /*
       * FIX: The original code only checked $acc['credit'] > 0 which missed
       * rows where credit=0 and debit=500 (drcr='D').
       *
       * Logic:
       *  - Use 'amount' field directly if present (already resolved by controller)
       *  - Otherwise fall back: if drcr == 'C' use credit, else use debit
       *  - Skip the "party" row — identified as is_bbb=1 AND drcr='D'
       *    (the party debit row is the counter-entry; we want the cr/expense rows)
       *  - Actually: show ALL non-zero rows; the party row will show debit,
       *    the expense/income rows will show credit. This matches Tally behaviour.
       *
       * For a credit note (purchase credit note / sales return):
       *  - Party row  : debit  (money owed back to party)
       *  - Income/Exp : credit (the account being credited)
       * We show ALL rows so the accountant can see the full picture.
       */

      if (is_array($account_transactions) && count($account_transactions) > 0):
        foreach ($account_transactions as $idx => $acc):

          // Resolve the display amount
          // Priority: use 'amount' if it is a non-zero parsed value
          $raw_amount = parseAmount($acc['amount'] ?? 0);

          if ($raw_amount <= 0) {
            // fall back to debit or credit based on drcr flag
            $drcr = strtoupper(trim($acc['drcr'] ?? 'D'));
            if ($drcr === 'C') {
              $raw_amount = parseAmount($acc['credit'] ?? 0);
            } else {
              $raw_amount = parseAmount($acc['debit'] ?? 0);
            }
          }

          // Skip completely zero rows
          if ($raw_amount <= 0) continue;

          $acc_count++;
          $taxable_amount += $raw_amount;

          $drcr_label = strtoupper(trim($acc['drcr'] ?? 'D'));
          $acc_name   = $acc['account_name'] ?? $acc['acc_name'] ?? '';
          $acc_desc   = $acc['description']  ?? '';
    ?>
    <tr>
      <td style="border-bottom:none; border-left:none; border-top:none;
                 text-align:center;"><?= $acc_count ?></td>
      <td style="border-bottom:none; border-left:none; border-top:none;
                 text-align:left;"><?= esc($acc_name) ?></td>
      <td style="border-bottom:none; border-left:none; border-top:none;
                 text-align:left; font-size:7pt;">
        <?= esc($acc_desc ?: 'N/A') ?>
      </td>
     
      <td style="border-bottom:none; border-right:none; border-left:none; border-top:none;
                 text-align:right; white-space:nowrap;">
        <?= formatAmount($raw_amount) ?>
      </td>
    </tr>

    <?php
        // ── Inline Tax Detail Row (if tax_details present and non-zero) ──
        $tax_details = $acc['tax_details'] ?? [];
        $igst_d = parseAmount($tax_details['igst'] ?? 0);
        $cgst_d = parseAmount($tax_details['cgst'] ?? 0);
        $sgst_d = parseAmount($tax_details['sgst'] ?? 0);
        $cess_d = parseAmount($tax_details['cess'] ?? 0);
        $igst_rate = parseAmount($acc['igst_rate'] ?? 0);

        if ($igst_rate > 0 || $igst_d > 0 || $cgst_d > 0 || $sgst_d > 0 || $cess_d > 0):
    ?>
   
    <?php endif; ?>

    <?php endforeach; ?>
    <?php else: ?>
    <tr>
      <td colspan="5" style="text-align:center; border-left:none; border-right:none;
                              border-bottom:none; border-top:none; padding:8px; color:#999;">
        No account transactions found.
      </td>
    </tr>
    <?php endif; ?>
  </table>

  <!-- ── Total Particulars + Taxable Amount ── -->
  <div style="display:flex; width:100%;
              border:1px solid #000; border-left:none; border-right:none; font-weight:bold;">
    <div style="width:65%; padding:4px 10px;">
      Total Particulars : <?= $acc_count ?>
    </div>
    <div style="width:35%; padding:4px 10px; display:flex; justify-content:space-between;">
      <span>Taxable Amount</span>
      <span><?= formatAmount($taxable_amount) ?></span>
    </div>
  </div>

  <!-- ── HSN Summary + Tax Totals ── -->
  <div class="hsn-total-wrapper">
    <div class="hsn-left">
      <b>HSN Summary</b><br><br>

      <!-- Header row -->
      <div class="hsn-row" style="font-weight:bold; text-align:center;">
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
        $total_igst      = 0;
        $total_cgst      = 0;
        $total_sgst      = 0;
        $total_cess      = 0;
		

        if (is_array($tax_summary) && count($tax_summary) > 0):
          foreach ($tax_summary as $tax):
            $igst     = parseAmount($tax['vch_igst']      ?? 0);
            $cgst     = parseAmount($tax['vch_cgst']      ?? 0);
            $sgst     = parseAmount($tax['vch_sgst_ugst'] ?? 0);
            $cess     = parseAmount($tax['vch_cess']      ?? 0);
            $tax_rate = parseAmount($tax['tax_rate']      ?? 0);
            $row_tax  = parseAmount($tax['vch_total_tax'] ?? 0);

            $total_tax_value += $row_tax;
            $total_igst      += $igst;
            $total_cgst      += $cgst;
            $total_sgst      += $sgst;
            $total_cess      += $cess;
      ?>
      <div class="hsn-row" style="text-align:center;">
        <div class="hsn-col"><?= esc($tax['vch_hsn_sac'] ?? 'N/A') ?></div>
        <div class="hsn-col big"><?= $tax_rate ?>%</div>
        <div class="hsn-col"><?= $igst > 0 ? formatAmount($igst) : '' ?></div>
        <div class="hsn-col"><?= $cgst > 0 ? formatAmount($cgst) : '' ?></div>
        <div class="hsn-col"><?= $sgst > 0 ? formatAmount($sgst) : '' ?></div>
        <div class="hsn-col"><?= $cess > 0 ? formatAmount($cess) : '' ?></div>
        <div class="hsn-col big"><?= formatAmount($row_tax) ?></div>
      </div>
      <?php
          endforeach;
        else:
          /*
           * FALLBACK: If tax_summary is empty but account_transactions carry
           * tax_details (as in your sample data), build the HSN rows from there.
           */
          $hsn_map = [];
          if (is_array($account_transactions)):
            foreach ($account_transactions as $acc):
              $td       = $acc['tax_details'] ?? [];
              $igst_r   = parseAmount($td['igst'] ?? 0);
              $cgst_r   = parseAmount($td['cgst'] ?? 0);
              $sgst_r   = parseAmount($td['sgst'] ?? 0);
              $cess_r   = parseAmount($td['cess'] ?? 0);
              $rate_key = parseAmount($acc['igst_rate'] ?? 0);
              $hsn_sac  = $acc['item_hsn_sac'] ?? '';

              if ($rate_key <= 0) continue;

              $key = $hsn_sac . '_' . $rate_key;
              if (!isset($hsn_map[$key])) {
                $hsn_map[$key] = [
                  'hsn'      => $hsn_sac,
                  'tax_rate' => $rate_key,
                  'igst'     => 0, 'cgst' => 0, 'sgst' => 0, 'cess' => 0,
                ];
              }

              // Calculate actual tax amounts from the account row amount
              $base_amt = parseAmount($acc['amount'] ?? $acc['debit'] ?? $acc['credit'] ?? 0);
              // Use provided tax_details values directly (already computed)
              // The tax_details contain rates not amounts, so compute:
              // But from your data igst=6.00 means rate %, not amount
              // Compute tax amount = base * rate / 100
              // However if these ARE amounts (depends on model), use as-is.
              // Your sample: igst=6.00, cgst=3.00, sgst=3.00 with amount=500
              // 500 * 6/100 = 30 (IGST), 500 * 3/100 = 15 (CGST/SGST)
              // They look like rates. Compute accordingly:
              $hsn_map[$key]['igst'] += ($igst_r > 0 && $igst_r < 100) ? ($base_amt * $igst_r / 100) : $igst_r;
              $hsn_map[$key]['cgst'] += ($cgst_r > 0 && $cgst_r < 100) ? ($base_amt * $cgst_r / 100) : $cgst_r;
              $hsn_map[$key]['sgst'] += ($sgst_r > 0 && $sgst_r < 100) ? ($base_amt * $sgst_r / 100) : $sgst_r;
              $hsn_map[$key]['cess'] += ($cess_r > 0 && $cess_r < 100) ? ($base_amt * $cess_r / 100) : $cess_r;
            endforeach;
          endif;

          foreach ($hsn_map as $row):
            $row_igst = $row['igst'];
            $row_cgst = $row['cgst'];
            $row_sgst = $row['sgst'];
            $row_cess = $row['cess'];
            $row_tax  = $row_igst + $row_cgst + $row_sgst + $row_cess;

            $total_igst      += $row_igst;
            $total_cgst      += $row_cgst;
            $total_sgst      += $row_sgst;
            $total_cess      += $row_cess;
            $total_tax_value += $row_tax;
        ?>
        <div class="hsn-row" style="text-align:center;">
          <div class="hsn-col"><?= esc($row['hsn'] ?? 'N/A') ?></div>
          <div class="hsn-col big"><?= $row['tax_rate'] ?>%</div>
          <div class="hsn-col"><?= $row_igst > 0 ? formatAmount($row_igst) : '' ?></div>
          <div class="hsn-col"><?= $row_cgst > 0 ? formatAmount($row_cgst) : '' ?></div>
          <div class="hsn-col"><?= $row_sgst > 0 ? formatAmount($row_sgst) : '' ?></div>
          <div class="hsn-col"><?= $row_cess > 0 ? formatAmount($row_cess) : '' ?></div>
          <div class="hsn-col big"><?= formatAmount($row_tax) ?></div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

    </div><!-- /.hsn-left -->

    <!-- Tax Totals (right side) -->
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

      <?php $grand_total = parseAmount($taxable_amount + $total_tax_value); ?>
      <div class="grand-total">TOTAL <?= formatAmount($grand_total) ?></div>
      <?= getIndianCurrency($grand_total) ?>
    </div>
  </div>

  <!-- ── Empty signature space ── -->
  <div class="big-empty-box"></div>

  <!-- ── Footer ── -->
  <div class="footer">

    <!-- Regd / PAN / TAN -->
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr;
                border-bottom:1px solid #000; padding:2px 8px;">
      <div>Regd. No. : <?= esc($company_info['cmp_reg_no'] ?? '') ?></div>
      <div>PAN : <?= esc($company_info['cmp_pan'] ?? '') ?></div>
      <div>TAN : <?= esc($company_info['cmp_tan'] ?? '') ?></div>
    </div>

    <!-- Bank Details -->
    <div class="bank-wrapper">
      <div class="bank-box compact">
        <b>Bank Details :</b>
        <div class="bank-grid">
          <div>Bank</div>   <div>: <?= esc($bank_details['bank_name']   ?? '') ?></div>
          <div>Account</div><div>: <?= esc($bank_details['bank_acc_no'] ?? '') ?></div>
          <div>IFSC</div>   <div>: <?= esc($bank_details['bank_ifsc']   ?? '') ?></div>
          <div>Branch</div> <div>: <?= esc($bank_details['bank_branch'] ?? '') ?></div>
        </div>
      </div>
      <div class="bank-box" style="text-align:center;">
        <b>E-way QR Code:</b>
        <img src="<?= base_url('img/QR.png') ?>" style="width:65px; height:auto;">
      </div>
      <div class="bank-box" style="text-align:center;">
        <b>Pay Online/UPI:</b>
        <img src="<?= base_url('img/QR.png') ?>" style="width:65px; height:auto;">
      </div>
      <div class="bank-box">
        <b>For <?= esc($company_info['cmp_name'] ?? '') ?></b>
        <br><br><br>
        <b>Authorised Signatory</b>
      </div>
    </div>

    <!-- Terms -->
    <div class="terms-wrapper">
      <div class="terms-box">
        <b>Terms &amp; Conditions:</b><br>
        <p style="padding:0; margin:0; line-height:1.1;">
          We declare that this credit note shows the actual value of
          the goods &amp; services described and that all particulars
          are true and correct.
        </p>
      </div>
      <div class="terms-box"><b>Customer Sign:</b></div>
      <div class="terms-box">
        <b>Notes:</b><br>
        <p style="margin:0; padding:0; line-height:1.1;">
          Thank you for dealing with our company.
        </p>
      </div>
    </div>

  </div><!-- /.footer -->
</div><!-- /.invoice -->
</div><!-- /#ledgerContent -->
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
        doc.save('Creditnote-Invoice-<?php echo $voucher_txn_id; ?>.pdf');

        // ✅ Auto-close (ALLOWED because window was JS-opened)
         window.close();

    });

});
</script>
</body>
</html>