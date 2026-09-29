
<html>
<head>
    <title>Auto Repeat Thead</title>
</head>
    <style>
     body {margin:0px; font-family:'Segoe UI';  font-size:8.5pt; padding:0;  line-height:30px;  }
     * { margin:0; padding:0; box-sizing: border-box; -moz-box-sizing: border-box;     }
     p{padding:2px 0px;}
	 #maintable{position:relative; margin:0 auto;}
     .partyinfo td{height:55px;}
     .partyinfo p{padding-left:22px;}
	 table{border-collapse: collapse;width:100%; font-family:'Segoe UI';   font-size:8.5pt;}
	 table.content tr:nth-child(even){background:#f3f3f3;}
	 table.border td{border:1px solid #000; padding:6px;}
	  @page { size: A4;  margin: 0; }
     @media print {
	 html, body { 	width:210mm;  	height: 297mm;   font-family:'Segoe UI';   font-size: 14px;}
       .main-page {}
     #maintable{position:relative; margin:0 auto;}
     .footer{width:100%; right:10px; left:10px;}
     .content{padding-bottom:350px;}
   
}

  </style>
<body>
    <div id="invoice" class="main-page">
    <table id="maintable" style="width: 780px; border:1px solid #000" class="header">
        <!-- table header to be repeated on each PDF page -->
        <thead align="left" style="display: table-header-group">
            <tr>
                <th>
                 <table border="0" width="100%" style="font-family:'Segoe UI','Arial'; font-size:8.5pt;">
       <tr>
           <td colspan="2"><table style="text-align:center; margin-top:-2px; font-family:'Segoe UI','Arial'; color:#000;   font-size:8.5pt;">
	   <tr>
	       <td style="padding:6px; border-top:6px solid #0fae2c;"></td>
	       <td style="padding:6px; border-top:10px solid #1d5182; position:relative;"></td>
	   </tr>
	   </table></td>
       </tr>    
	   <tr style="">
	   <td style="padding:0 .3cm;">
<img style="height:70px; width:auto" src="<?php echo base_url();?>/public/assets/img/logo.png">
	        <h2 style="font-size:20px; margin-top:5px; line-height:42px;"><?= $company_name ?></h2>
	   <p><small> <?= $company_address ?></small> </p>
	   <p>Phone: <?= $phone ?></p>
	   <p>Email: <?= $email ?></p>
	   </td> 
	     <td style="padding:0 .3cm; text-align:right;">
	       <h5 style="font-size:26px; text-decoration:underline; color:#0fae2c; font-weight:700">RECEIPT</h5><br>
	        <h4>#<?= $voucher_no ?> </h4>
	        <p>Dated No. : <span style="padding-left:12px;"><?= $voucher_date ?></span> </p>
	   </td> 
	  </tr>
	  <tr>
	      <td colspan="2"><table border="0" width="100%" style="margin-top:8px; font-family:'Segoe UI','Arial'; color:#1d5182; font-size:9.2pt; font-weight:bold; border-top:1px solid #000;">
	       <tr>
	           <td style="padding:.1cm .3cm;">GST : <span style="padding-left:10px; "><?= $gst ?></span></td>
	           <td colspan="2" style="padding:.1cm .3cm; text-align:right;">CIN : <span style="padding-left:10px;"><?= $cin ?></span></td>
	       </tr>
	       <tr style="font-weight:bold; background: #000; color:#fff;">
	           <td style="border-right:1px solid #000;padding:6px; width:56%">Party</td>
	           <td style="border-right:1px solid #000;padding:6px; width:24%">Place of Supply</td>
	           <td style="padding:6px; text-align:right; width:20%;">Amount</td>
	       </tr>
	   </table></td>
	  </tr>
	   </table>
                </th>
            </tr>
        </thead>
        <!-- table footer to be repeated on each PDF page -->
        <tfoot class="footer" align="left" style="display: table-footer-group">
            <tr>
                <td>
                    <table style="margin:0px 0px 0px 1%; width:98%; border-top:1px solid #000; font-family:'Segoe UI','Arial';  font-size:8.5pt;">
	       <tr>
	       <td colspan="2" style="padding:.1cm .3cm;">
	           <p>Mode : <span style="padding-left:29px;"><?= $debit_transactions ?></span></p>
	           <p>Remarks : <span style="padding-left:8px;"><?= $narration ?></span></p>
	       </td>
	       </tr>
	       <tr><td>&nbsp;<br></td></tr>
	       <tr style="padding-top:30px;">
	           <td style="padding-bottom:20px; border-bottom:3px solid #1d5182;"> <h2 style="padding:6px 0px; font-size:15pt; color:#1d5182;"><u><img src="<?php echo base_url();?>/public/assets/images/currency_rupee.png"> <?= $amount ?></u></h2>
	   <p><strong><?= $amount_words ?></strong></p>
	    <p style="padding:6px 0px"><small>(Cheque Subject to Realisation)</small></p>
	    </td>
	   <td style="padding-bottom:20px; border-bottom:3px solid #1d5182;"> <p style="text-align:right; padding-right:8px;"><strong style="text-align:right;">For <?= $company_name ?></strong></p>
	  <p style="text-align:right; padding-top:50px;"><strong style="text-align:right;">Authorised Signatory</strong></p>
	  </td>
	       </tr>
	   </table>
                </td>
            </tr>
        </tfoot>
        <!-- table body -->
        <tbody>
            <tr>
                <td class="contentbody">
                   <table class="content" border="0" width="98%" style="margin-top:4px; width:98%; margin-left:1%; font-family:'Segoe UI','Arial';  font-size:8.5pt; border-bottom:1px solid #000;">
	      
	       <?php foreach ($credit_transactions as $key => $credit_transaction): ?>
           
            <tr class="partyinfo">
	           <td style="border-right:1px solid #000;padding:6px; width:56%;"><?= $credit_transaction['party'] ?></td>
	           <td style="border-right:1px solid #000;padding:6px; width:24%;"></td>
	           <td style="padding:6px; text-align:right; width:20%;"><?= $credit_transaction['amount'] ?></td>
	       </tr>
            <?php endforeach; ?>

	      <!--  <tr class="partyinfo">
	           <td style="border-right:1px solid #000;padding:6px;">Pay U / E Commers <p>- ID No: UN222 xxxx</p></td>
	           <td style="border-right:1px solid #000;padding:6px;">Mumbai</td>
	           <td style="padding:6px; text-align:right;">&#8377; 2580.00</td>
	       </tr> -->
	      
	       <tr style="font-weight:bold;border-top:1px solid #000; border-bottom:1px solid #000;">
	       <td colspan="2" style="padding:6px;">Total :</td>
	       <td style="text-align:right; padding:6px;"><img src="<?php echo base_url();?>/public/assets/images/currency_rupee.png" style="height:12px; width:auto"> <?= $amount ?></td>
	       </tr>
	       
	   </table>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    <p style="text-align:center; margin:8px;"><button id="printbtn" type="button" style="padding:6px 12px; font-weight:bold">Download PDF</button></p>
 
<script>
document.addEventListener("DOMContentLoaded", () => {
    let printLink = document.getElementById("printbtn");
    let container = document.getElementById("invoice");

    printLink.addEventListener("click", event => {
        event.preventDefault();
        printLink.style.display = "none";
        window.print();
    }, false);

    container.addEventListener("click", event => {
        printLink.style.display = "flex";
    }, false);

}, false);

</script>
</body>
</html>

                                        
                                                            