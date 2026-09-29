<?php $header = array( 	'title' => 'Add Stock Item' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
if($local_session->get('ses_company_id')!=''){
	$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
	$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
} 
else{
	$fy_begndt  =  date('01-04-Y');
	$fy_end     =  date('01-04-Y');
}
?>
<style>
	/*for autocomplete inside bills grid*/
	.ui-autocomplete {
		z-index:9999!important;
	}
	.paging_simple_numbers{display:flow-root!important;text-align: center;}
</style>
<style>
	.myform .col-sm-6{padding-bottom:2px;}
	.myform label{width:25%; float:left;}
	.myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
	.myform .select2 {width:75%!important; }
	.myform .input-group .form-control, .myform .input-group select{width:100%!important;}
</style>	  

<div id="validation_errors"></div>

<?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
echo form_open(base_url().$folder_path.'items/add_item', $attributes);
?>
<div class="row">
	<?php echo $message_output->run() ;?>
	<div class="col-6"><h3 class="pb-3">Add Stock Item</h3></div>  
	<div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
	<div class="col-md-6">


		<div class="card p-3 my-2">
			<div class="row">
				<h5 class="pb-2">Item Description</h5>
				<p class="col-12">
				<label>Item Name <span class="red">*</span></label>
				<?php $data = array(
					'name'        => 'item_name',
					'value'       => set_value("item_name"),
					'maxlength'   => '255',
					'class'       => 'form-control',
					'minlength'   =>  "3",
					'required'    => true
				);
				echo form_input($data);
			?>
			
			</p>

			<p class="col-12"><label>Item Sku </label> <?php $data = array(
				'name'        => 'item_sku',
				'value'       => set_value("item_sku"),
				'maxlength'   => '255',
				'class'       => 'form-control',
			);
			echo form_input($data);
		?></p>




		<p class="col-12"><label>Item UPC</label><?php $data = array(
			'name'        => 'item_shortname',
			'value'       => set_value("item_shortname"),
			'maxlength'   => '255',
			'class'       => 'form-control',
			'minlength'   =>  "3"			
		);
		echo form_input($data);
	?></p>
	<p class="col-12"><label>Alias Name <span class="red">*</span></label> <?php $data = array(
		'name'        => 'item_alias',
		'value'       => set_value("item_alias"),
		'maxlength'   => '255',
		'class'       => 'form-control',
		'minlength'   =>  "3",
		'required'    => true
	);
	echo form_input($data);
?></p>
<p class="col-12"><label>Print Name <span class="red">*</span></label> <?php $data = array(
	'name'        => 'item_printname',
	'value'       => set_value("item_printname"),
	'maxlength'   => '255',
	'class'       => 'form-control',
	'minlength'   =>  "3",
	'required'    => true
);
echo form_input($data);
?><input type="hidden" name="unitdetail_array" id="unitdetail_array" value=""></p>
<p class="col-12"><label>Sales Account <span class="red">*</span></label>

	<?php	
	echo form_dropdown('item_sales_acc', $sales_acc_dropdown, set_value('item_sales_acc'),'id="item_sales_acc" class="form-control w-75 selectwidget required" required ');
	?>	

</p>
<p class="col-12"><label>Purchase Account <span class="red">*</span></label>
	<?php	
	echo form_dropdown('item_pur_acc', $purchase_acc_dropdown, set_value('item_pur_acc'),'id="item_pur_acc" class="form-control w-75 selectwidget required" required ');
?>	</p>
<!--<p>
	<button type="button" class="btn btn-outline-secondary btn-sm m-1" data-bs-toggle="modal" data-bs-target="#prodimension">Product Dimension</button>
	<button type="button" class="btn btn-outline-secondary btn-sm m-1" data-bs-toggle="modal" data-bs-target="#proinfo">Product Information</button>
	<button type="button" class="btn btn-outline-secondary btn-sm m-1" data-bs-toggle="modal" data-bs-target="#parameters">Parameters</button>
	<button type="button" class="btn btn-outline-secondary btn-sm m-1 call_batch_modal">Item Batch</button>

</p>-->		

</div></div>




	
	
</div>



<div class="col-md-6">
<div class="card p-3 my-2"> <div class="row">
	<h5 class="pb-2">Unit Details</h5>
	<table id="sortTable" cellspacing="0" cellpadding="5"  width="100%">
		<thead>
			<tr>
				<th>Unit</th>
				<th>Op. Qty</th>
				<th></th>					
			</tr>
		</thead>
		<tbody>
			<?php for($i=1;$i<=25;$i++){ ?>
				<tr id="tr<?php echo $i;?>">
					<td><?php	
					echo form_dropdown('item_unit_idm[]', $item_units, set_value('item_unit_idm'),' class="muom form-control selectwidget" id="mc_qty_unit_'.$i.'" ');
				?></td>
				<td>
				<span id="lmc_qty_op_<?php echo $i;?>">0</span>
				
				<?php $data = array(
					'name'        => 'item_op_bal_qtym[]',
					'value'       => set_value("item_op_bal_qty"),
					'maxlength'   => '255',
					'class'       => 'muomqty form-control w-75',
					'id'		 => 'mc_qty_op_'.$i,
					'hidden'  => true
				);
				echo form_input($data);
			?></td>			
			<td>
				<button type="button" class="btn btn-outline-secondary btn-sm mcqtywise_modal" data-id="<?php echo $i;?>">MC Qty Wise</button>
				<button type="button" class="btn btn-outline-danger btn-sm clear_mc_qty" data-count="<?php echo $i;?>">Delete</button>
			</td>
			
		</tr>	
	<?php } ?>			 
</tbody>
</table>





<?php for($i=1;$i<=25;$i++){ ?>
	<div class="modal fade pt-5" id="view_mcqtywise_modal<?php echo $i;?>" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 id="proinfoLabel">MC Wise Qty(<span id="qtyunit_txt<?php echo $i;?>"></span>) Information</h4><br>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
				<em><small><strong>IMPORTANT NOTE</strong>: ANY CHANGES MADE WILL REQUIRE TO SAVE ITEM MASTER. DO NOT PRESS BACK BUTTON OR REFRESH TO AVOID MISCALCULATIONS </small></em>
					<br><br>
				<form id="mcqtyfrm<?php echo $i;?>">
					<div class="row">
						<p class="col-md-2 col-8 fw-bold">MC</p> 
						<p class="col-md-2 col-4 fw-bold">Qty</p>
						<p class="col-md-2 col-4 fw-bold">AVG COST</p>
						<p class="col-md-2 col-4 fw-bold">FIFO</p>
						<p class="col-md- col-4 fw-bold">LIFO</p>
					</div>
					<?php $bcounter=0;foreach($material_centre_dropdown as $mcid => $mcname){ if($mcname!=''){ $bcounter++; ?>
						<div class="row modalrow<?php echo $i;?>">
							<p class="col-md-2 col-8"><?php	echo ucwords($mcname);?></p> 
							<p class="col-md-2 col-4">
								<input type="text" form="myform" data-id="<?php echo $i;?>"  name="mcqtywise_qty[<?php echo $mcid;?>][]" class="mcqty_change modal_muomqty form-control w-100  mc_qty_op_qty_<?= $i ?>" maxlength="5" onkeypress="return /[0-9]/i.test(event.key)" value="0">
							</p>							
							<p class="col-md-2 col-4">
								<input type="text" form="myform" data-id="<?php echo $i;?>" placeholder="Value"  name="mcqtywise_avg[<?php echo $mcid;?>][]" class="mcavg_change form-control w-100  mc_qty_avg_<?= $i ?>" onkeypress="return /[0-9]/i.test(event.key)" value="0">
							</p>
							<p class="col-md-2 col-4">
								<input type="text" form="myform" data-id="<?php echo $i;?>" placeholder="Value"  name="mcqtywise_fifo[<?php echo $mcid;?>][]" class="mcfifo_change form-control w-100 mc_qty_fifo_<?= $i ?>" onkeypress="return /[0-9]/i.test(event.key)" value="0">
							</p>
							<p class="col-md-2 col-4">
								<input type="text" form="myform" data-id="<?php echo $i;?>"  placeholder="Value"  name="mcqtywise_lifo[<?php echo $mcid;?>][]"  class="mclifo_change form-control w-100 mc_qty_lifo_<?= $i ?>" onkeypress="return /[0-9]/i.test(event.key)" value="0">
							</p>
						</div>
					<?php } }?>
					<div class="row modalrow">
							<p class="col-md-2 col-8">&nbsp;</p> 
							<p class="col-md-2 col-4">
								<span id="total_mc_qty_op_qty<?php echo $i;?>">0</span>
							</p>							
							<p class="col-md-2 col-4">
								<span id="total_mc_qty_avg<?php echo $i;?>">0</span>
							</p>
							<p class="col-md-2 col-4">
								<span id="total_mc_qty_fifo<?php echo $i;?>">0</span>
							</p>
							<p class="col-md-2 col-4">
								<span id="total_mc_qty_lifo<?php echo $i;?>">0</span>
							</p>
						</div>
						
				</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary clear_qty_info"  data-id="<?php echo $i;?>" data-modalname="view_mcqtywise_modal<?php echo $i;?>">Clear</button>
					<button type="button" class="btn btn-primary save_mcq_qty_list" data-id="<?php echo $i;?>">Save</button>
				</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>

</div></div>


</div>
<div class="col-md-6">
<div class="card p-3 my-2"><div class="row">
		<h5 class="pb-2">Item Mapping</h5>
		<p class="col-12"><label>Item Group <span class="red">*</span></label> <?php	
		echo form_dropdown('item_group_id', $item_group, set_value('item_group_id'),'id="item_group_id" class="form-control w-75 selectwidget required" required ');
		?>
		<input type="hidden" name="batchinfo" id="batchinfo" value="">
	</p>
	<p class="col-12"><label>Item Category <span class="red">*</span></label> <?php	
	echo form_dropdown('item_catg_id', $item_category, set_value('item_catg_id'),'id="item_catg_id" class="form-control w-75 selectwidget required" required ');
?></p>	

<p class="col-12"><label>Default Unit <span class="red">*</span></label> <?php	
echo form_dropdown('item_unit_id', $item_units, set_value('item_unit_id'),'id="item_unit_id" class="form-control selectwidget required" required ');
?><input type="hidden" name="batchinfo_array" id="batchinfo_array" value=""></p>		


</div> </div> 
</div>

<div class="col-md-6">
    
    <div class="card p-3 my-2">
				      <h5 class="pb-3">GST Details</h5>
				 <div class="col-12"><label>Tax Category <span class="red">*</span></label> 
				 <?php
				 echo form_dropdown('tax_cat_mst_id',$tax_category, '','id="tax_cat_mst_id" class="form-control" required'); ?></div>
				
				 <div class="col-12 my-2"><label>HSN</label> 
				 <input type="text" name="hsn" class="form-control"></div>
				  
				
              
			</div>
	    
		<div class="card p-3 my-2"><div class="row">
			<h5 class="pb-2">Item Price Info</h5>
			<p class="col-sm-6"><label>MRP</label> <?php $data = array(
				'name'        => 'item_mrp',
				'value'       => '',
				'maxlength'   => '255',
				'class'       => 'form-control'
			);
			echo form_input($data);
		?></p>
	</div>  
</div> 
</div>
<div class="col-md-12">
<div class="card disablediv p-3 my-2" style="display:none;"><div class="row">
	<h5 class="pb-2">Item Price Info</h5>
	<p class="col-sm-6"><label>Sales Price Applied on </label> <?php $data = array(
		'name'        => 'item_sale_pinfo',
		'value'       => '',
		'maxlength'   => '255',
		'class'       => 'form-control'
	);
	echo form_input($data);
?></p>
<p class="col-sm-6"><label>Purchase Price Applied on </label> <?php $data = array(
	'name'        => 'item_purchase_pinfo',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>
<p class="col-sm-6"><label>Sale Price  </label> <?php $data = array(
	'name'        => 'item_sale_price',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>
<p class="col-sm-6"><label>Purchase Price </label> <?php $data = array(
	'name'        => 'item_purchase_price',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>
<p class="col-sm-6"><label>Tax Inclusive Sale Price </label> 
	Yes <input class="form-check-input" type="radio" value="Yes" name="item_sale_price"> &nbsp;&nbsp;
	No <input class="form-check-input" type="radio" value="No" name="item_sale_price">
</p>
<p class="col-sm-6"><label>Tax Inclusive Purchase  Price </label>  
	Yes <input class="form-check-input" type="radio" value="Yes" name="item_purchase_price"> &nbsp;&nbsp;
	No <input class="form-check-input" type="radio" value="No" name="item_purchase_price">
</p>
<p class="col-sm-6"><label>MRP</label> <?php $data = array(
	'name'        => 'item_tax__mrp',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>
<p class="col-sm-6"><label>Min Sale Price</label> <?php $data = array(
	'name'        => 'item_min_sprice',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>			  
<p class="col-sm-6"><label>Self Val Price</label> <?php $data = array(
	'name'        => 'item_self_vprice',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>			  
<p class="col-sm-6"><label>Stock Val Method</label> <?php $data = array(
	'name'        => 'item_stock_val_method',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>
</div></div>


<div class="card disablediv p-3 my-2"><div class="row">
	<h5 class="pb-2">Other Description</h5>
	<p class="col-sm-6"><label>Sale Discount </label> <?php $data = array(
		'name'        => 'item_sale_discount',
		'value'       => '',
		'maxlength'   => '255',
		'class'       => 'form-control'
	);
	echo form_input($data);
?></p>
<p class="col-sm-6"><label>Purchase Discount </label> <?php $data = array(
	'name'        => 'item_purchase_discount',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>
<p class="col-sm-6"><label>Specify Sale Discount Structure </label> <?php $data = array(
	'name'        => 'item_sale_discount',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>
<p class="col-sm-6"><label>Specify Pur. dis Structure </label> <?php $data = array(
	'name'        => 'item_purchase_discount',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>				  
<p class="col-sm-6"><label>Set Critical level </label> <?php $data = array(
	'name'        => 'item_critical_level',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>
<p class="col-sm-6"><label>Specify Contract </label> <?php $data = array(
	'name'        => 'item_specify_contract',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>
<p class="col-sm-6"><label>Serial No wise Details </label> <?php $data = array(
	'name'        => 'item_serial_no_details',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>				  
<p class="col-sm-6"><label>Parameterized Details </label> <?php $data = array(
	'name'        => 'item_parameterized_details',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>
<p class="col-sm-6"><label>MRP Wise Details </label> <?php $data = array(
	'name'        => 'item_mrp_details',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>
<p class="col-sm-6"><label>Batch Wise Details </label> <?php $data = array(
	'name'        => 'item_batch_details',
	'value'       => '',
	'maxlength'   => '255',
	'class'       => 'form-control'
);
echo form_input($data);
?></p>
</div></div>				  




<div class="col-sm-12 text-center my-3">
	<input type="submit" value="SAVE" class="btn btn-success mx-2" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
	<a href="<?php echo $base_url.'items/list_items';?>" class="btn btn-secondary mx-2" >QUIT</a>
</div>


</div> 


	
</form>
<style>
	/*for autocomplete inside bills grid*/
	.ui-autocomplete {
		z-index:9999!important;
	}
</style>
<?php echo view('includes/footer_scripts');
$all_units=array();
if($item_units){
	foreach($item_units as $unit_id => $name){
		$all_units[] = array("label"=>$name,"value"=>$name,"id"=>$unit_id);	
	}
}

?>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script>$('#sortTable').DataTable({"pageLength":5,"bLengthChange": false,info:false,searching: false});</script>

<script>
$(document).on("change",".mcqty_change",function(){
	var modalid = $(this).data("id");
	var modalname = "view_mcqtywise_modal"+modalid;
	var total_qty= parseInt(0);
	$('#'+modalname+' .mcqty_change').each(function() {
		if($(this).val()!='')
          total_qty += parseInt($(this).val());
     });
 $("#lmc_qty_op_"+modalid).html(parseInt(total_qty));
 $("#mc_qty_op_"+modalid).val(parseInt(total_qty));
 $("#total_mc_qty_op_qty"+modalid).html(parseInt(total_qty));	
});

$(document).on("change",".mcavg_change",function(){
	var modalid = $(this).data("id");
	var modalname = "view_mcqtywise_modal"+modalid;
	var qty_avg= parseFloat(0);
	$('#'+modalname+' .mcavg_change').each(function() {
		if($(this).val()!='')
          qty_avg += parseFloat($(this).val());
     });
 $("#total_mc_qty_avg"+modalid).html(parseFloat(qty_avg));	
});

$(document).on("change",".mcfifo_change",function(){
	var modalid = $(this).data("id");
	var modalname = "view_mcqtywise_modal"+modalid;
	var qty_fifo= parseFloat(0);
	$('#'+modalname+' .mcfifo_change').each(function() {
		if($(this).val()!='')
          qty_fifo += parseFloat($(this).val());
     });
 $("#total_mc_qty_fifo"+modalid).html(parseFloat(qty_fifo));	
});

$(document).on("change",".mclifo_change",function(){
	var modalid = $(this).data("id");
	var modalname = "view_mcqtywise_modal"+modalid;
	var qty_lifo= parseFloat(0);
	$('#'+modalname+' .mclifo_change').each(function() {
		if($(this).val()!='')
          qty_lifo += parseFloat($(this).val());
     });
 $("#total_mc_qty_lifo"+modalid).html(parseFloat(qty_lifo));	
});

$(document).on("click",".clear_qty_info",function(){
	var modalname = $(this).data("modalname");
	var modalid   = $(this).data("id"); 
	
	$("#"+modalname+" input[type=text]").val("");
	
	$("#total_mc_qty_op_qty"+modalid).html('0');
		$("#total_mc_qty_avg"+modalid).html('0');
		$("#total_mc_qty_fifo"+modalid).html('0');
		$("#total_mc_qty_lifo"+modalid).html('0');
		
		$('#lmc_qty_op_'+modalid).html('');
		
		$('#mc_qty_op_'+modalid).val('');
		$('#mc_qty_unit_'+modalid).val('');		
		$('#lmc_qty_op_'+modalid).val('');		
		$('#mc_qty_unit_'+modalid).siblings('.custom-combobox').find('input').val('');
		$('#view_mcqtywise_modal'+modalid).find('.mc_qty_op_qty_'+modalid).val('');
		$('#view_mcqtywise_modal'+modalid).find('.mc_qty_avg_'+modalid).val('');
		$('#view_mcqtywise_modal'+modalid).find('.mc_qty_fifo_'+modalid).val('');
		$('#view_mcqtywise_modal'+modalid).find('.mc_qty_lifo_'+modalid).val('');
		
	$("#"+modalname).modal("hide");	
});

$(".hist_closemodal_window").on("click",function(){
		   
		  $("#view_taxhistory_modal").modal('hide');
		  $("#modify_comp_taxmst").prop("disabled", false);
	       
		 
	   });
$(".closemodal_window").on("click",function(){
		   
		  $("#view_taxhistory_modal").modal('hide');
		  $("#modify_comp_taxmst").prop("disabled", false);
	     	  
		  $("#item_tax").val("");
	   });

   

	$(document).on("click",".save_mcq_qty_list",function(){
		var modalid = $(this).data("id");
		var tablerow = '#tr'+modalid;
         var selected_unitid = $("#mc_qty_unit_"+modalid).val();
         
		 var isValidQty = true;
		 var $currentModal = $('#view_mcqtywise_modal' + modalid);

    // Loop through all AVG, FIFO, and LIFO fields
    $currentModal.find('input[name^="mcqtywise_avg"]:visible, input[name^="mcqtywise_fifo"]:visible, input[name^="mcqtywise_lifo"]:visible').each(function() {
         var nameAttr = $(this).attr('name');
        var match = nameAttr.match(/mcqtywise_(avg|fifo|lifo)\[(\d+)\]\[(\d*)\]/);

        if (match) {
            var mcId = match[2];
            var index = match[3];

            var avgField = $currentModal.find('input[name="mcqtywise_avg[' + mcId + '][' + index + ']"]:visible');
            var fifoField = $currentModal.find('input[name="mcqtywise_fifo[' + mcId + '][' + index + ']"]:visible');
            var lifoField = $currentModal.find('input[name="mcqtywise_lifo[' + mcId + '][' + index + ']"]:visible');
            var qtyField = $currentModal.find('input[name="mcqtywise_qty[' + mcId + '][' + index + ']"]:visible');

            // If any of avg, fifo, lifo has value
            if (avgField.val().trim() !== '' || fifoField.val().trim() !== '' || lifoField.val().trim() !== '') {
                // Check if qty is filled
                if (qtyField.val().trim() === '') {
                    isValidQty = false;
                    qtyField.addClass('required-highlight');
                } else {
                    qtyField.removeClass('required-highlight');
                }
            } else {
                qtyField.removeClass('required-highlight');
            }
        }
    });
         var base_unit_qty=0;
		$(tablerow+' .muomqty').each(function(){
			if(parseFloat($(this).val())!=''){
				base_unit_qty=parseFloat(base_unit_qty)+parseFloat($(this).val());
			}

		});
		var modal_unit_qty=0;
		$("#view_mcqtywise_modal"+modalid).find('.modal_muomqty').each(function(){
			if( $(this).val()!=''){
				modal_unit_qty=parseFloat(modal_unit_qty)+parseFloat($(this).val());
			}

		});

		if (!isValidQty) {       
        alert_notification('Please fill Qty where AVG COST, FIFO or LIFO is entered.');
		return false;
        }
	
		if(base_unit_qty != modal_unit_qty){
			alert_notification("Kindly check qty!!!");
			$("#view_mcqtywise_modal"+modalid).find('.modal_muomqty').each(function(){
				if( $(this).val()!=''){
					$(this).val('0');
				}		
			});
			return false;
		}
		else
			$("#view_mcqtywise_modal"+modalid).modal("hide");
		
		return true;
	});

	$(document).on('click','.clear_mc_qty', function(){
		
        
		var count = $(this).data('count');
		$('#lmc_qty_op_'+count).html('0');
		$('#mc_qty_op_'+count).val('0');
		$('#mc_qty_unit_'+count).val('');		
		$("#total_mc_qty_op_qty"+count).html('0');
		$("#total_mc_qty_avg"+count).html('0');
		$("#total_mc_qty_fifo"+count).html('0');
		$("#total_mc_qty_lifo"+count).html('0');	
		$('#mc_qty_unit_'+count).siblings('.custom-combobox').find('input').val('');
		$('#view_mcqtywise_modal'+count).find('.mc_qty_op_qty_'+count).val('');
		$('#view_mcqtywise_modal'+count).find('.mc_qty_avg_'+count).val('');
		$('#view_mcqtywise_modal'+count).find('.mc_qty_fifo_'+count).val('');
		$('#view_mcqtywise_modal'+count).find('.mc_qty_lifo_'+count).val('');
		
		var selected_unitid = $("#mc_qty_unit_"+count).val();
		

	});
 var countersel=0;
 	$(document).on('click','.mcqtywise_modal', function(){
	    var modalid = $(this).data("id");	
		var tablerow = '#tr'+modalid;
	    var countersel=0;
	    var selectedunit = $('#mc_qty_unit_'+modalid).val();
	    
	    
	    $(' .muom').each(function(){
			if( $(this).val()!='' && selectedunit!='' && $(this).val()==selectedunit ){
				countersel+=1;
			}
		
		});
		
		if(countersel>1){
		    $('#mc_qty_unit_'+modalid).val('');
		    $('#mc_qty_unit_'+modalid).siblings('.custom-combobox').find('input').val('');
		    alert("Unit already selected!!!");
		    
		    return false;
		}
		
		var emptyuom=0;
		var counter=0;
		$(tablerow+' .muom').each(function(){
			if(counter==0 && $(this).val()==''){
				emptyuom=emptyuom+1;
			}
			counter=counter+1;
		});

		$(tablerow+' .muomqty').each(function(){
			if(counter==0 && $(this).val()==''){
				emptyuom=emptyuom+1;
			}
			counter=counter+1;
		});

		var mc_qty_op_c = $('#mc_qty_op_'+modalid).val();
		mc_qty_op_c = parseFloat(mc_qty_op_c);


		if(emptyuom >0){
			alert_notification("Kindly fill Unit Details first!!!");
		}
		/* else if(isNaN(mc_qty_op_c) || mc_qty_op_c < 0){
			alert_notification("Kindly fill Qty Details first!!!");
		} */
		else{	

			var txn = $(tablerow+' .muom').find(":selected").text()+"-"+$(tablerow+' .muomqty').val();
			$("#view_mcqtywise_modal"+modalid+" #qtyunit_txt"+modalid).html(txn);

			$("#view_mcqtywise_modal"+modalid).modal("show");
		}


	});



  $(document).on('submit', '#myform', function(e){	   
  	e.preventDefault();
  	
  	var form = $(this);
  	var formData = new FormData(this);

  	$.ajax({
  		url: form.attr('action'), 
  		type: 'POST',
  		data: formData,
  		dataType: "json",
  		processData: false,
  		cache: false,
  		contentType: false,
  		beforeSend: function() {
  			show_loader();
  			$('#submitbtn').attr('disabled', 'disabled');
  			$('#validation_errors').html('');
  		},
  		success: function (response) {
			stop_loader();
                // console.log(response);
  			if (typeof response === 'string') {
  				response = JSON.parse(response);
  			}
  			if(response.status){
  				alert_success(response.message);
  				window.location.href='<?php echo history_back();?>';
  			}
  			else{ 
  				stop_loader();
  				alert_notification(response.message);
  				if(response.errors)
  				{
  					var list = ``;
  					$.each(response.errors, function(index, value){
  						list += `<li>${value}</li>`;
  					});

  					var html = `<div class="alert-error-custom">
								<i class="bi bi-x-circle-fill"></i>
								<div>
								<strong>Error!</strong> <ul>${list}</ul>
								</div>
								<button type="button" class="btn-close" aria-label="Close"></button>
							  </div>
							`;
  					$('#validation_errors').html(html);
  					window.scrollTo(0,0);
  				}
  			}
  		},
  		complete: function() {		
  			stop_loader();
  			$('#submitbtn').attr('disabled', false);
  		},
		error: function (jqXHR, exception) {
				stop_loader();
                var error_= '';
                if (jqXHR.status === 0) {
                    error = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    error = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    error = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    error = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    error = 'Time out error.';
                } else if (exception === 'abort') {
                    error = 'Ajax request aborted.';
                } else {
                    error = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert_notification(error);
            },
  	});
  });


  $(document).on('blur','[name="item_name"]', function(){
  	var name = $(this).val().trim();
  	if(name){
  		if(!$('[name="item_shortname"]').val().trim())
  		{
  			$('[name="item_shortname"]').val(name); 
  		}
  		if(!$('[name="item_alias"]').val().trim())
  		{
  			$('[name="item_alias"]').val(name); 
  		}
  		if(!$('[name="item_printname"]').val().trim())
  		{
  			$('[name="item_printname"]').val(name); 
  		}
  	}
  });




</script>