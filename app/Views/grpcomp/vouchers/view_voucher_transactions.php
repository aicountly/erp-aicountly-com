<?php $header = array( 	'title' => 'List of '. $voucher_name .' Vouchers' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>
 <h3 class="pb-3"><?php echo 'List of '. $voucher_name .' Vouchers';?></h3>
  <div class="col-12 my-5 gridtable" style="overflow: auto; max-width:100%">    
	<div class="row head">
      <div class="col">S No.</div>
      <div class="col">Date</div>
      <div class="col">Account</div>
      <div class="col">Debit</div>
      <div class="col">Credit</div>
    </div>
 <?php
$cc_counter=0; 
$all_debit=0;
$all_credit=0;

 if($transactions){ 
    foreach($transactions as $row){ $cc_counter++;  
	
	$all_debit =$all_debit+$row['debit'];
	$all_credit =$all_credit+$row['credit'];
	?>
   <div class="row">
     <div class="col"><?php echo $cc_counter;?></div>
     <div class="col"><?php echo date('d-m-Y',strtotime($row['txn_date']));?></div>
     <div class="col"><?php echo ucwords($row['account_name']);?></div>
     <div class="col"><?php echo number_format($row['debit'],2);?></div>
     <div class="col"><?php echo number_format($row['credit'],2);?></div>    
  </div>
 <?php } } ?>
 
  <div class="row">
     <div class="col"></div>
     <div class="col"></div>
     <div class="col"></div>
     <div class="col"><?php echo number_format($all_debit,2);?></div>
     <div class="col"><?php echo number_format($all_credit,2);?></div>    
  </div>
    </div>  

<?php echo view('includes/footer_scripts'); ?>	