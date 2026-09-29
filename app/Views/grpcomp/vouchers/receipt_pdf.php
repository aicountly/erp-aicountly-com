<html>
<head>
<title><?= $company_name ?></title>

<style>
     body { width: 230mm;   margin: 0 auto;   padding: 0;    line-height:30px;   background: rgb(204,204,204); }
     * { margin:0; padding:0; box-sizing: border-box; -moz-box-sizing: border-box;     }
     p{padding:2px 0px;}
	 .invoice { width: 210mm;   margin:10mm auto;    background:white;  }
     .sub-page {  padding:1cm; }
     @page {   size: A4;  margin: 0; }
     @media print {
	 html, body { 	width:210mm;  }
       .invoice { margin:0;  border: initial; border-radius:initial; width:initial; 	min-height:initial; box-shadow:initial;  	background:initial; page-break-after: always;    }
     }
	 table{border-collapse: collapse;}
	 table.maintable td{border:1px solid #000; padding:6px;}
	  table.inner-table td{border:0px; padding:0px;}
</style>
<!--<script-->
<!--			src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"-->
<!--			integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="-->
<!--			crossorigin="anonymous"-->
<!--			referrerpolicy="no-referrer"-->
<!--		></script>-->

</head>
<body>

<div id="invoice" class="invoice">
<div class="sub-page">
    <table border="0" class="maintable" width="100%" style="width:100%; font-family:'Arial';   font-size: 14px;">
    <tbody>
        <tr>
            <td colspan="2">
                 <div style="text-align:left; width:48%; padding-left:5px; float:left; font-weight:600">GST : <span style="padding-left:10px;"><?= $gst ?></span></div>
	       <div style="text-align:right; width:52%; padding-right:5px; float:right; font-weight:600">CIN : <span style="padding-left:10px;"><?= $cin ?></span></div>
                
                
                <p style="text-align:center; font-weight:bold; padding:0; margin:0;">
                    <u>R E C E I P T</u>
                </p>
                <p style="font-size:24px; font-weight:bold; line-height:42px; padding:0; margin:0; text-align:center;">
                    <?= $company_name ?>
                </p>
                <p style="text-align:center; padding:0 0 8px 0; font-size:14px; line-height:20px; margin:0;">
                    <small><?= $company_address ?></small> 
                </p>
            </td>
        </tr>
        <tr>
            <td style="width:44%;">
                Receipt No. : <span style="padding-left:18px;"><?php echo $voucher_no; ?></span> 
            </td>
            <td style="width:56%;"> 
                Dated : <span style="padding-left:18px;"> <?php echo $voucher_date; ?></span> 
            </td>
        </tr>
        <tr>
            <td style="width:44%; vertical-align:top">
                <table class="inner-table" border="0" width="100%" style="font-size:14px;">
                    <?php foreach ($credit_transactions as $key => $credit_transaction): ?>
                    <tr>
                        <td style="vertical-align:top; padding-right:10px;"><?= $key == 0 ? 'Party&nbsp;:' : '' ?></td>
                        <td style="font-weight:600;"><?= $credit_transaction ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </td>
            <td style="width:56%;">
                <p><span style="width:150px; float:left;">Party identification No </span>:&nbsp;&nbsp; </p>

                <p><span style="width:150px; float:left;">Place of Supply </span>:&nbsp;&nbsp;</p>

                <p style="margin:10px 0px;">
                    <span style="width:150px; float:left;">Series :</span>
                    &nbsp;&nbsp; <?php echo $voucher_series; ?>
                </p>
                
                <p>
                    <span style="width:150px; float:left;">Amount </span> 
                    :   &nbsp;&nbsp;
                    <img style="width: 12px;" src="<?php echo base_url();?>/public/assets/images/currency_rupee.png">
                    <b> 
                        <?= $amount ?>
                    </b>
                </p>
            </td>
        </tr>
        
        
        <tr>
            <td colspan="2">Mode : 
                <span style="padding-left:29px;">
                    <?= $debit_transactions ?>
                </span>
            </td>
        </tr>
        <tr>
            <td colspan="2">Remarks : <span style="padding-left:8px;">
                <?= $narration ?>
            </span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p style="text-align:right; padding-right:8px;">
                   FOR <strong style="text-align:right;"><?= $company_name ?></strong>
                </p>
                <h4 style="padding:6px 0px">
                  
                        <img style="width: 12px;" src="<?php echo base_url();?>/public/assets/images/currency_rupee.png">
                        <?= $amount ?>
                    
                </h4>
                <p>
                    <strong><?= $amount_words ?></strong>
                </p>
                <p style="padding:6px 0px">
                    <small>(Cheque Subject to Realisation)</small>
                </p>
                <p style="text-align:right; padding-right:8px;">
                    <strong style="text-align:right;">Authorised Signatory</strong>
                </p>
            </td>
        </tr>
    </tbody>
    </table>
</div>
</div>

<!-- <button type="button" onclick="downloadPDF()"style="padding:8px; display:inherit; margin:10px auto">Download PDF</button> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js "></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>

var voucher_no = '<?= $voucher_no ?>';

<?php if(isset($_GET['p'])){ ?>
    window.print();
<?php } else { ?>

window.jsPDF = window.jspdf.jsPDF;
var docPDF = new jsPDF();

function downloadPDF(){

    var elementHTML = document.querySelector("#invoice");
    docPDF.html(elementHTML, {
        callback: function(docPDF) {
            docPDF.save('receipt-'+voucher_no+'.pdf');
        },
        x: 0,
        y: 0,
        width: 170,
        windowWidth: 650
    });
}

downloadPDF();
<?php } ?>


</script>
</body>
</html>