<?php $header = array('title' => 'List of '. $voucher_name .' Vouchers' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>
 <h3 class="pb-3"><?php echo 'List of '. $voucher_name .' Vouchers';?></h3>
  <a href="javascript:void(0);" class="deletebtn btn btn-success">Delete</a>  
  <div class="col-12 my-5 gridtable" style="overflow: auto; max-width:100%">    
	<div class="row head">
     <div class="col"><input name="select_all" id="select_all" value="1" type="checkbox"></div>
      <div class="col">Date</div>
	  <div class="col">Account</div>
      <div class="col">Debit</div>
      <div class="col">Credit</div>
     
    </div>
	<?php
	$cc_counter=0;
	if($voucher_trans){ 
    foreach($voucher_trans as $row){ 
	$cc_counter++;  
	?>
	<div class="row view_voucher_txn" data-id="<?php echo $row['voucher_txn_id'];?>">
    	 <div class="col"><input name="vchrxn_ids[]" class="checkbox" type="checkbox" value="<?php echo $row['voucher_txn_id'];?>"></div>
     <div class="col"><?php echo $row['txn_date'];?></div>
     <div class="col"><?php echo ucwords($row['account_name']);?></div>
     <div class="col"><?php echo number_format($row['debit'],2);?></div>
     <div class="col"><?php echo number_format($row['credit'],2);?></div> 
     
    </div>	
	<?php 
	  }
	}
	?>
 </div>  
<?php echo view('includes/footer_scripts'); ?>	
<script>

$(".deletebtn").on('click',function(){
	var ischeckled =  $('.view_voucher_txn  input:checkbox:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a voucher to delete!!");
	  }
      else{	  
			 var checkedVals = $('input[name="vchrxn_ids[]"]:checked').map(function() {
			return this.value;
		}).get();
		
		 if(checkedVals!='')
			 confirm_delete(baseurl+"/admin/vouchers/remove_voucher/"+<?php echo $voucher_type_id;?>+"/"+checkedVals.join(","));			
		 else
			return false;
	   }
    }) 
    
$('#select_all').on('click',function(){
        if(this.checked){
            $('.checkbox').each(function(){
                this.checked = true;				
            });
			$(".editbtn").addClass("disabled");
        }else{
             $('.checkbox').each(function(){
                this.checked = false;
            });
			$(".editbtn").removeClass("disabled");
           }
      });
  </script>    
 <script>
	  $('.view_voucher_txn').on('dblclick', function(e) {  
          var dataid = $(this).data('id');
	       window.location.href = baseurl+'/admin/vouchers/edit_voucher_transactions/'+dataid+"/"+<?php echo $voucher_type_id;?>;			  		  
         });
 </script>