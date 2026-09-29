<?php $header = array( 	'title' => 'Update Stock Item' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
.myform .col-sm-6{padding-bottom:2px;}
.myform label{width:25%; float:left;}
.myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
.myform .select2 {width:75%!important; }
.modal-body{
height: 50vh;
overflow-y: auto;
}
.paging_simple_numbers{display:flow-root!important;text-align: center;}
</style>
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
<?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
echo form_open(base_url().$folder_path.'items/modify_item/'.$item_id, $attributes);
?>

<div class="row">
	<?php echo $message_output->run() ;?>
	<div class="col-6">
		<h3 class="pb-3">Update Stock Item</h3>
	</div>
	<div class="col-6">
		<span class="float-end">
			<a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a>
		</span>
	</div>
	<div class="col-md-6">
		
		<div class="card p-3 my-2">
			<div class="row">
				<h5 class="pb-2">Item Description</h5>
				<p class="col-12">
					<label>Item Name <span class="red">*</span></label> <?php $data = array(
												'name'        => 'item_name',
												'value'       => $item_info['itm_name'],
												'maxlength'   => '255',
												'minlength'   =>  "3",
												'class'       => 'form-control',
												'title'       => 'Type minimum 3 characters',
												'required'    => true
												);
												echo form_input($data);
					?>
					<input type="hidden" name="unitdetail_array" id="unitdetail_array" value="">
				
				</p>
				
				<p class="col-12">
					<label>Item Sku</label> <?php $data = array(
								'name'        => 'item_sku',
								'value'       => $item_info['itm_sku'],
								'maxlength'   => '255',
								'class'       => 'form-control',
								);
								echo form_input($data);
					?>
				</p>
				
				
				<p class="col-12">
					<label>Item UPC</label>
					<?php $data = array(
												'name'        => 'item_shortname',
												'value'       => $item_info['itm_upc'],
												'maxlength'   => '255',
												'class'       => 'form-control',
												'minlength'   =>  "3"												
												);
												echo form_input($data);
					?>
				</p>
				<p class="col-12">
					<label>Alias Name <span class="red">*</span></label> <?php $data = array(
												'name'        => 'item_alias',
												'value'       => $item_info['itm_alias'],
												'maxlength'   => '255',
												'class'       => 'form-control',
												'minlength'   =>  "3",
												'required'    => true
												);
												echo form_input($data);
					?>
					
				</p>
				<p class="col-12">
					<label>Print Name <span class="red">*</span></label> <?php $data = array(
												'name'        => 'item_printname',
												'value'       => $item_info['itm_print_name'],
												'maxlength'   => '255',
												'class'       => 'form-control',
												'minlength'   =>  "3",
												'required'    => true
												);
												echo form_input($data);
					?>
					
				</p>
				<p class="col-12">
					<label>Sales Account <span class="red">*</span></label>
					
					<?php
					echo form_dropdown('item_sales_acc', $sales_acc_dropdown, $item_info['itm_sales_acc_id'],'id="item_sales_acc" class="form-control w-75 selectwidget required" required ');
					?>
					
				</p>
				<p class="col-12">
					<label>Purchase Account <span class="red">*</span></label>
					<?php
					echo form_dropdown('item_pur_acc', $purchase_acc_dropdown, $item_info['itm_pur_acc_id'],'id="item_pur_acc" class="form-control w-75 selectwidget required" required ');
				?>	</p>
			   <!--	
				<p>
					<button type="button" class="btn btn-outline-secondary btn-sm m-1" data-bs-toggle="modal" data-bs-target="#prodimension">Product Dimension</button>
					<button type="button" class="btn btn-outline-secondary btn-sm m-1" data-bs-toggle="modal" data-bs-target="#proinfo">Product Information</button>
					<button type="button" class="btn btn-outline-secondary btn-sm m-1" data-bs-toggle="modal" data-bs-target="#parameters">Parameters</button>
					<button type="button" class="btn btn-outline-secondary btn-sm m-1" data-bs-toggle="modal" data-bs-target="#batchmodel">Item Batch</button>
				</p> -->
			</div>
		</div>
	</div>
	
	
	<div class="col-md-6">
		<div class="card p-3 my-2">
			<div class="row">
				<h5 class="pb-2">Item Mapping</h5>
				<p class="col-12">
					<label>Item Group <span class="red">*</span></label>
					<?php
					echo form_dropdown('item_group_id', $item_group, $item_info['item_grp_id'],'id="item_group_id" class="form-control w-75 selectwidget required" required ');
					?>
					
					
				</p>
				<p class="col-12">
					<label>Item Category <span class="red">*</span></label> <?php
					echo form_dropdown('item_catg_id', $item_category, $item_info['item_cat'],'id="item_catg_id" class="form-control selectwidget required" required ');
					?>
				</p>
				
				<p class="col-12">
					<label>Default Unit <span class="red">*</span></label> <?php
					echo form_dropdown('item_unit_id', $item_units, $item_info['itm_def_unit_id'],'id="item_unit_id" class="form-control selectwidget required" required ');
					?>
				</p>
				
				
			</div> </div>
			
			 <div class="card p-3 my-2">
				      <h5 class="pb-3">GST Details</h5>
				 <div class="col-12"><label>Tax Category <span class="red">*</span></label> 
				 <?php
				 echo form_dropdown('tax_cat_mst_id',$tax_category, $item_info['tax_cat_mst_id'],'id="tax_cat_mst_id" class="form-control" required'); ?></div>
				
				 <div class="col-12 my-2"><label>HSN</label> 
				 <input type="text" name="hsn" class="form-control" value="<?php echo $item_info['itm_hsn'];?>"></div>
				  
				
              
			</div>
	    
			
		
	</div>
	<div class="col-12">
	<div class="card p-3 my-2"> <div class="row">
				<h5 class="pb-2">Unit Details</h5>
				<table id="sortTable" cellspacing="0" cellpadding="5" width="100%">
					<thead>
						<tr>
							<th>Unit</th>
							<th>Op. Qty</th>
							<th>
							</th>
						</tr>
					</thead>
					<tbody>

						<?php
						$i = 1; ?>
						<?php 
					
						foreach ($GetOpnBalanItm as $key => $value) { ?>
							
							<tr id="tr<?= $i ?>">
								<td>
									<?php
									$sel_item_units=[];
									$disable_class='';
								
                                            $unitId = $value['item_unit'];
                                        
                                            if (isset($item_units[$unitId])) {
                                                // we have a match → grab it
                                                $sel_item_units[$unitId] = $item_units[$unitId];
                                                $disable_class="cannotmodify";
                                            }else {
                                            // Case 2️⃣: not matched → show the entire dropdown
                                            $sel_item_units = $item_units;
                                        }
									
									echo form_dropdown('item_qty_wise['.$i.'][unit_id]', $sel_item_units, $value['item_unit'],' class="muom form-control selectwidget '.$disable_class.'" id="mc_qty_unit_'.$i.'" ');
									?>
								</td>
								<td>
								   <span id="lmc_qty_op_<?php echo $i;?>"><?php echo $value['op_bal_qty'];?></span>
									<?php $data = array(
											'name'        => 'item_qty_wise['.$i.'][op_bal_qty]',
											'value'       => $value['op_bal_qty'],
											'type'				=> 'number',
											'maxlength'   => '255',
											'class'       => 'muomqty form-control w-100',
											'id'					=> 'mc_qty_op_'.$i,
											'hidden'  => true
											);
											echo form_input($data);
									?>
								</td>
								<td>
									<button type="button" class="btn btn-outline-secondary btn-sm mcqtywise_modal" data-id="<?php echo $i;?>">MC Qty Wise</button>
									<button type="button" class="btn btn-outline-warning btn-sm empty_mc_qty" data-count="<?php echo $i;?>">Clear Bal</button>
									<button type="button" class="btn btn-outline-danger btn-sm clear_mc_qty" data-status="<?= $value['status'] ?>" data-count="<?php echo $i;?>">Delete</button>
								</td>
								
							</tr>
							<?php $i++; ?>
						<?php } ?>

						<?php for($i=$i; $i<=25; $i++){ ?>

							<tr id="tr<?= $i ?>">
								<td>
									<?php
									echo form_dropdown('item_qty_wise['.$i.'][unit_id]', $item_units, '',' class="muom form-control selectwidget" id="mc_qty_unit_'.$i.'" ');
									?>
								</td>
								<td>
								  <span id="lmc_qty_op_<?php echo $i;?>">0</span>
									<?php $data = array(
											'name'        => 'item_qty_wise['.$i.'][op_bal_qty]',
											'value'       => '',
											'type'		  => 'number',
											'maxlength'   => '255',
											'class'       => 'muomqty form-control w-100',
											'id'		  => 'mc_qty_op_'.$i,
											'hidden'      => true
											);
											echo form_input($data);
									?>
								</td>
								<td>
									<button type="button" class="btn btn-outline-secondary btn-sm mcqtywise_modal" data-id="<?php echo $i;?>">MC Qty Wise</button>
										
								</td>
								
							</tr>

						<?php } ?>

					</tbody>
				</table>

				<?php $i = 1; ?>
				<?php
				foreach ($GetOpnBalanItm as $key => $value) {
					$total_mc_qty=0;
					$total_mc_avg=0;
					$total_mc_fifo=0;
					$total_mc_lifo=0;
					?>
					<div class="modal fade pt-5" id="view_mcqtywise_modal<?= $i?>" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<h4 id="proinfoLabel">MC Wise Qty(<span id="qtyunit_txt<?php echo $i;?>">
									</span>) Information</h4>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									</button>
								</div>
								<div class="modal-body">
								<em><small><strong>IMPORTANT NOTE</strong>: ANY CHANGES MADE WILL REQUIRE TO SAVE ITEM MASTER. DO NOT PRESS BACK BUTTON OR REFRESH TO AVOID MISCALCULATIONS </small></em>
					<br><br>
									<div class="row">
										<p class="col-md-2 col-8 fw-bold">MC</p>
										<p class="col-md-2 col-4 fw-bold">Qty</p>
										<p class="col-md-2 col-4 fw-bold">AVG COST</p>
										<p class="col-md-2 col-4 fw-bold">FIFO</p>
										<p class="col-md- col-4 fw-bold">LIFO</p>
									</div>
									
									<?php foreach($value['list'] as $key2 => $value2){
                                        $total_mc_qty +=($value2['op_bal_qty']>0)?$value2['op_bal_qty']:0;
										$total_mc_avg +=($value2['avg']>0)?$value2['avg']:0;
										$total_mc_fifo +=($value2['fifo']>0)?$value2['fifo']:0;
										$total_mc_lifo +=($value2['lifo']>0)?$value2['lifo']:0;
										?>
									
									<div class="row modalrow<?php echo $i;?>">
										<p class="col-md-2 col-8">
										<?php	echo ucwords($value2['mat_cent_name']);?>
											<input type="hidden" name="item_qty_wise[<?= $i ?>][mc][<?= $value2['mat_cent_id'] ?>][mc_id]" value="<?= $value2['mat_cent_id'] ?>">
										</p>
										<p class="col-md-2 col-4">
											<input type="text" form="myform" data-id="<?php echo $i;?>" data-mcid="<?= $value2['mat_cent_id'] ?>"   name="item_qty_wise[<?= $i ?>][mc][<?= $value2['mat_cent_id'] ?>][op_bal_qty]"  value="<?= $value2['op_bal_qty'] ?>" class="mcqty_change modal_muomqty form-control w-100 mc_qty_op_qty_<?= $i ?>" data-id="<?= $i ?>" maxlength="5" onkeypress="return /^[0-9.]$/.test(event.key) || (event.key === '.' && this.value.indexOf('.') === -1)">
										</p>
										<p class="col-md-2 col-4">
											<input type="text" form="myform" data-id="<?php echo $i;?>" data-mcid="<?= $value2['mat_cent_id'] ?>" placeholder="Value"  name="item_qty_wise[<?= $i ?>][mc][<?= $value2['mat_cent_id'] ?>][avg]" value="<?= $value2['avg'] ?>" class="mcavg_change form-control w-100 mc_qty_avg_<?= $i ?>" onkeypress="return /^[0-9.]$/.test(event.key) || (event.key === '.' && this.value.indexOf('.') === -1)">
										</p>
										<p class="col-md-2 col-4">
											<input type="text" form="myform" data-id="<?php echo $i;?>" data-mcid="<?= $value2['mat_cent_id'] ?>" placeholder="Value"  name="item_qty_wise[<?= $i ?>][mc][<?= $value2['mat_cent_id'] ?>][fifo]" value="<?= $value2['fifo'] ?>" class="mcfifo_change form-control w-100 mc_qty_fifo_<?= $i ?>" onkeypress="return /^[0-9.]$/.test(event.key) || (event.key === '.' && this.value.indexOf('.') === -1)">
										</p>
										<p class="col-md-2 col-4">
											<input type="text" form="myform" data-id="<?php echo $i;?>" data-mcid="<?= $value2['mat_cent_id'] ?>" placeholder="Value"  name="item_qty_wise[<?= $i ?>][mc][<?= $value2['mat_cent_id'] ?>][lifo]"  value="<?= $value2['lifo'] ?>" class="mclifo_change form-control w-100 mc_qty_lifo_<?= $i ?>" onkeypress="return /^[0-9.]$/.test(event.key) || (event.key === '.' && this.value.indexOf('.') === -1)">
										</p>
									</div>

									<?php } ?>
							<div class="row modalrow">
							<p class="col-md-2 col-8">&nbsp;</p> 
							<p class="col-md-2 col-4">
								<span id="total_mc_qty_op_qty<?php echo $i;?>"><?php echo $total_mc_qty;?></span>
							</p>							
							<p class="col-md-2 col-4">
								<span id="total_mc_qty_avg<?php echo $i;?>"><?php echo $total_mc_avg;?></span>
							</p>
							<p class="col-md-2 col-4">
								<span id="total_mc_qty_fifo<?php echo $i;?>"><?php echo $total_mc_fifo;?></span>
							</p>
							<p class="col-md-2 col-4">
								<span id="total_mc_qty_lifo<?php echo $i;?>"><?php echo $total_mc_lifo;?></span>
							</p>
						</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary clear_qty_info" data-id="<?php echo $i;?>" data-modalname="view_mcqtywise_modal<?php echo $i;?>">Clear</button>
									<button type="button" class="btn btn-primary save_mcq_qty_list" data-id="<?php echo $i;?>">Save</button>
								</div>
							</div>
						</div>
					</div>

					<?php $i++; ?>
				<?php } ?>

				<?php for($i=$i; $i<=25; $i++){ ?>

					<div class="modal fade pt-5" id="view_mcqtywise_modal<?= $i?>" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<h4 id="proinfoLabel">MC Wise Qty(<span id="qtyunit_txt<?php echo $i;?>">
									</span>) Information</h4>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									</button>
								</div>
								<div class="modal-body">
								<em><small><strong>IMPORTANT NOTE</strong>: ANY CHANGES MADE WILL REQUIRE TO SAVE ITEM MASTER. DO NOT PRESS BACK BUTTON OR REFRESH TO AVOID MISCALCULATIONS </small></em>
					<br><br>
									<div class="row">
										<p class="col-md-2 col-8 fw-bold">MC</p>
										<p class="col-md-2 col-4 fw-bold">Qty</p>
										<p class="col-md-2 col-4 fw-bold">AVG COST</p>
										<p class="col-md-2 col-4 fw-bold">FIFO</p>
										<p class="col-md- col-4 fw-bold">LIFO</p>
									</div>
									
									<?php foreach($mc_list as $key2 => $value2){ ?>
									
									<div class="row modalrow<?php echo $i;?>">
										<p class="col-md-2 col-8">
										<?php	echo ucwords($value2['mat_cent_name']);?>
											<input type="hidden" name="item_qty_wise[<?= $i ?>][mc][<?= $value2['mat_cent_id'] ?>][mc_id]" value="<?= $value2['mat_cent_id'] ?>">
										</p>
										<p class="col-md-2 col-4">
											<input type="text" form="myform" data-mcid="<?= $value2['mat_cent_id'] ?>" data-id="<?php echo $i;?>"  name="item_qty_wise[<?= $i ?>][mc][<?= $value2['mat_cent_id'] ?>][op_bal_qty]"  value="0" class="mcqty_change modal_muomqty form-control w-100 mc_qty_op_qty_<?= $i ?>" maxlength="5" onkeypress="return /[0-9]/i.test(event.key)">
										</p>
										<p class="col-md-2 col-4">
											<input type="number" form="myform" data-mcid="<?= $value2['mat_cent_id'] ?>" data-id="<?php echo $i;?>" placeholder="Value"  name="item_qty_wise[<?= $i ?>][mc][<?= $value2['mat_cent_id'] ?>][avg]" value="0" class="mcavg_change form-control w-100 mc_qty_avg_<?= $i ?>" onkeypress="return /[0-9]/i.test(event.key)">
										</p>
										<p class="col-md-2 col-4">
											<input type="number" form="myform" data-mcid="<?= $value2['mat_cent_id'] ?>" data-id="<?php echo $i;?>" placeholder="Value"  name="item_qty_wise[<?= $i ?>][mc][<?= $value2['mat_cent_id'] ?>][fifo]" value="0" class="mcfifo_change form-control w-100 mc_qty_fifo_<?= $i ?>" onkeypress="return /[0-9]/i.test(event.key)">
										</p>
										<p class="col-md-2 col-4">
											<input type="number" form="myform" data-mcid="<?= $value2['mat_cent_id'] ?>" data-id="<?php echo $i;?>" placeholder="Value"  name="item_qty_wise[<?= $i ?>][mc][<?= $value2['mat_cent_id'] ?>][lifo]"  value="0" class="mclifo_change form-control w-100 mc_qty_lifo_<?= $i ?>" onkeypress="return /[0-9]/i.test(event.key)">
										</p>
									</div>

									<?php } ?>
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
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary clear_qty_info" data-id="<?php echo $i;?>" data-modalname="view_mcqtywise_modal<?php echo $i;?>">Clear</button>
									<button type="button" class="btn btn-primary save_mcq_qty_list" data-id="<?php echo $i;?>">Save</button>
								</div>
							</div>
						</div>
					</div>

				<?php } ?>

			</div>
		</div>
	</div>
	<div class="col-md-12">
		
						<div class="card p-3 my-2">
							<div class="row">
								<h5 class="pb-2">Item Price Info</h5>
								<p class="col-sm-6">
									<label>MRP</label> <?php $data = array(
																'name'        => 'item_mrp',
																'value'       => "",
																'maxlength'   => '255',
																'class'       => 'form-control'
																);
																echo form_input($data);
									?>
								</p>
							</div>
						</div>
						<div class="card  disablediv  p-3 my-2" style="display:none;">
							
							<div class="row">
								<h5 class="pb-2">Item Price Info</h5>
								<p class="col-sm-6">
									<label>Sales Price Applied on </label> <?php $data = array(
																'name'        => 'item_sale_pinfo',
																'value'       => '',
																'maxlength'   => '255',
																'class'       => 'form-control'
																);
																echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Purchase Price Applied on </label> <?php $data = array(
																'name'        => 'item_purchase_pinfo',
																'value'       => '',
																'maxlength'   => '255',
																'class'       => 'form-control'
																);
																echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Sale Price  </label> <?php $data = array(
																'name'        => 'item_sale_price',
																'value'       => '',
																'maxlength'   => '255',
																'class'       => 'form-control'
																);
																echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Purchase Price </label> <?php $data = array(
																'name'        => 'item_purchase_price',
																'value'       => '',
																'maxlength'   => '255',
																'class'       => 'form-control'
																);
																echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Tax Inclusive Sale Price </label>
									Yes <input class="form-check-input" type="radio" value="Yes" name="item_sale_price"> &nbsp;&nbsp;
									No <input class="form-check-input" type="radio" value="No" name="item_sale_price">
								</p>
								<p class="col-sm-6">
									<label>Tax Inclusive Purchase  Price </label>
									Yes <input class="form-check-input" type="radio" value="Yes" name="item_purchase_price"> &nbsp;&nbsp;
									No <input class="form-check-input" type="radio" value="No" name="item_purchase_price">
								</p>
								<p class="col-sm-6">
									<label>MRP</label> <?php $data = array(
																'name'        => 'item_tax__mrp',
																'value'       => '',
																'maxlength'   => '255',
																'class'       => 'form-control'
																);
																echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Min Sale Price</label> <?php $data = array(
											'name'        => 'item_min_sprice',
											'value'       => '',
											'maxlength'   => '255',
											'class'       => 'form-control'
											);
											echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Self Val Price</label> <?php $data = array(
											'name'        => 'item_self_vprice',
											'value'       => '',
											'maxlength'   => '255',
											'class'       => 'form-control'
											);
											echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Stock Val Method</label> <?php $data = array(
																'name'        => 'item_stock_val_method',
																'value'       => '',
																'maxlength'   => '255',
																'class'       => 'form-control'
																);
																echo form_input($data);
									?>
								</p>
							</div>
						</div>
						
						
						<div class="card disablediv p-3 my-2">
							<div class="row">
								<h5 class="pb-2">Other Description</h5>
								<p class="col-sm-6">
									<label>Sale Discount </label> <?php $data = array(
																'name'        => 'item_sale_discount',
																'value'       => '',
																'maxlength'   => '255',
																'class'       => 'form-control'
																);
																echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Purchase Discount </label> <?php $data = array(
												'name'        => 'item_purchase_discount',
												'value'       => '',
												'maxlength'   => '255',
												'class'       => 'form-control'
												);
												echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Specify Sale Discount Structure </label> <?php $data = array(
													'name'        => 'item_sale_discount',
													'value'       => '',
													'maxlength'   => '255',
													'class'       => 'form-control'
													);
													echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Specify Pur. dis Structure </label> <?php $data = array(
												'name'        => 'item_purchase_discount',
												'value'       => '',
												'maxlength'   => '255',
												'class'       => 'form-control'
												);
												echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Set Critical level </label> <?php $data = array(
												'name'        => 'item_critical_level',
												'value'       => '',
												'maxlength'   => '255',
												'class'       => 'form-control'
												);
												echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Specify Contract </label> <?php $data = array(
													'name'        => 'item_specify_contract',
													'value'       => '',
													'maxlength'   => '255',
													'class'       => 'form-control'
													);
													echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Serial No wise Details </label> <?php $data = array(
												'name'        => 'item_serial_no_details',
												'value'       => '',
												'maxlength'   => '255',
												'class'       => 'form-control'
												);
												echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Parameterized Details </label> <?php $data = array(
												'name'        => 'item_parameterized_details',
												'value'       => '',
												'maxlength'   => '255',
												'class'       => 'form-control'
												);
												echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>MRP Wise Details </label> <?php $data = array(
												'name'        => 'item_mrp_details',
												'value'       => '',
												'maxlength'   => '255',
												'class'       => 'form-control'
												);
												echo form_input($data);
									?>
								</p>
								<p class="col-sm-6">
									<label>Batch Wise Details </label> <?php $data = array(
												'name'        => 'item_batch_details',
												'value'       => '',
												'maxlength'   => '255',
												'class'       => 'form-control'
												);
												echo form_input($data);
									?>
								</p>
							</div>
						</div>
						
						
						
						
						<div class="col-sm-12 text-center my-3">
							<input type="submit" value="SAVE" class="btn btn-primary mx-2" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
							<a href="<?php echo $base_url.'items/list_items';?>" class="btn btn-secondary mx-2" >QUIT</a>
						</div>
						
						
					</div> 
					
					
					
					
					
					
					
	
					
				</form>
				
					<!-- valuation modal start -->
				
				<div class="modal fade" id="VoucherValuationModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
 <input type="hidden" name="vchfrm_itemid" id="vchfrm_itemid" value="">
   <input type="hidden" name="vchfrm_unitid" id="vchfrm_unitid" value="">	
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Calculate Valuation Method</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
	
  <div class="form-group row"><br></div>

   <div class="form-group row" id="prgrloader" style="display:none;">
        <p class="overall_status">Total Vouchers:0</p>
        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>
		<div class="text-center my-2 current_status" style="height:20px">			
		</div>
     </div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	    <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="updateBalancesModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Updating Account Balances</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Accounts: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Capital Account)
		</div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	  <button type="button" class="btn btn-success" id="refreshbtn_bal" data-ids="0" style="display:none;">Refresh</button>&nbsp;
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="DoubleValueVerificationModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Calculate Valuation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Vouchers: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated progress-bar-main" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Sundry Creditors)
		</div>

		<p class="overall_sub_status">Total Records: 215</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated progress-bar-sub" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_sub_status" style="height:20px">
			101 / 215 (Sales 22)
		</div>

		
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	  <button type="button" class="btn btn-success" id="refreshbtn_dbl_val" style="display:none;">Refresh</button>&nbsp;
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- valuation modal  end-->
<style>
/*for autocomplete inside bills grid*/
.ui-autocomplete {
z-index:9999!important;
}
</style>
<?php echo view('includes/footer_scripts'); ?>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js">
</script>
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

var item_id= '<?php echo $item_id;?>';
var back_url_link='<?php echo history_back();?>';


  
	   $(".hist_closemodal_window").on("click",function(){
		   
		  $("#view_taxhistory_modal").modal('hide');
	       
		 
	   });
	   
	   $(".closemodal_window").on("click",function(){
		   
		  $("#view_taxhistory_modal").modal('hide');
	     	  
		  $("#item_tax").val("");
	   });
	   
	   
 $(document).on("click",".save_mcq_qty_list",function(){
		 var modalid = $(this).data("id");
		var $currentModal = $('#view_mcqtywise_modal' + modalid);
		var tablerow = '#tr'+modalid;
		var isValidQty = 0;

		// Just log to confirm modal is opening correctly
		console.log('Checking modal:', modalid);

		// Loop all visible AVG/FIFO/LIFO inputs inside modal
		$currentModal.find('input[name*="[mc]"][name$="[avg]"], input[name*="[mc]"][name$="[fifo]"], input[name*="[mc]"][name$="[lifo]"]').filter(':visible').each(function() {

    var $this = $(this);
    var nameAttr = $this.attr('name');

    // Match the name pattern
    var match = nameAttr.match(/item_qty_wise\[(\d+)\]\[mc\]\[(\d+)\]\[(avg|fifo|lifo)\]/);

    if (match) {
        var itemId = match[1];
        var mcId = match[2];

        var baseName = 'item_qty_wise[' + itemId + '][mc][' + mcId + ']';

        // Find the related fields
        var avgField = $currentModal.find('input[name="' + baseName + '[avg]"]:visible');
        var fifoField = $currentModal.find('input[name="' + baseName + '[fifo]"]:visible');
        var lifoField = $currentModal.find('input[name="' + baseName + '[lifo]"]:visible');
        var qtyField = $currentModal.find('input[name="' + baseName + '[op_bal_qty]"]:visible');
		// If any of avg, fifo, lifo has value
		//alert(avgField.val() +"---"+qtyField.val());
		const qty = qtyField.val().trim();
    const avg = avgField.val().trim();
    const fifo = fifoField.val().trim();
    const lifo = lifoField.val().trim();
	
		const anyCostEntered = (avg !== '' && avg !== '0') || (fifo !== '' && fifo !== '0') || (lifo !== '' && lifo !== '0');
    const qtyIsEmptyOrZero = qty === '' || qty === '0';
	if (anyCostEntered && qtyIsEmptyOrZero) {
        isValidQty++;
        qtyField.focus();
        return false; // break out of `.each()`
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
		if (isValidQty>0) {       
        alert_notification('Please fill Qty where AVG COST, FIFO or LIFO is entered.');
		return false;
        }
		
	var mcqty_data= [];
			$("#view_mcqtywise_modal"+modalid+" .mc_qty_op_qty_"+modalid).each(function() {
				var mcid      = $(this).attr("data-mcid");
				var opqty     = $(this).val();		
                var item_group_id = $("#item_group_id").val();	
				var item_catg_id = $("#item_catg_id").val();				
				var avgcost   = $('input[name="item_qty_wise['+modalid+'][mc]['+mcid+'][avg]"]').val();
				var fifocost  = $('input[name="item_qty_wise['+modalid+'][mc]['+mcid+'][fifo]"]').val();
				var lifocost  = $('input[name="item_qty_wise['+modalid+'][mc]['+mcid+'][lifo]"]').val();				
				var unitid    = $('select[name="item_qty_wise['+modalid+'][unit_id]"]').val();				
				var mcqtydata = {"item_catg_id":item_catg_id,"item_group_id":item_group_id,"item_id":item_id,"mcid":mcid,"unitid":unitid,"avgcost":avgcost,"fifocost":fifocost,"lifocost":lifocost,"opqty":opqty};
				mcqty_data.push(mcqtydata);
			});	
		/* var valmethod_id = $("#valmethod_id").val();	
	var frmdata   ={"valmethod_id":valmethod_id,"item_id":item_id,"mcqty_data":mcqty_data};	
		 show_loader();
		 $.ajax({
		  type: "POST",
		  url: baseurl+"/admin/items/ajax_updt_opn_bon",
		  data: frmdata,
		  success:function(response){
			  stop_loader();			  		  
		    },
		  error: function (jqXHR, exception){
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
                } 
                alert_notification(error);
            },		  
    }); commented on 08-05-2025 */ 

	
		/*  var valmethod_id = $("#valmethod_id").val();		
       if(valmethod_id!=''){
	   if(tttxns>0){
		   Swal.fire({
			title: 'Are you sure?',
			text: "To recalculate valuation",
			icon: 'error',
			showCancelButton: true,
			confirmButtonText: 'Yes!',
			customClass: {
			  confirmButton: 'btn btn-primary',
			  cancelButton: 'btn btn-outline-danger ms-1'
			},
			buttonsStyling: false
      }).then(function (result) {		  
        if (result.isConfirmed==false) {
           location.reload(); 
        }
		else{
			$("#isvalclcultd").val("1");
			$("#view_mcqtywise_modal"+modalid).modal("hide");
			var mcqty_data= [];
			$("#view_mcqtywise_modal"+modalid+" .mc_qty_op_qty_"+modalid).each(function() {
				var mcid      = $(this).attr("data-mcid");
				var opqty     = $(this).val();		
                var item_group_id = $("#item_group_id").val();	
				var item_catg_id = $("#item_catg_id").val();				
				var avgcost   = $('input[name="item_qty_wise['+modalid+'][mc]['+mcid+'][avg]"]').val();
				var fifocost  = $('input[name="item_qty_wise['+modalid+'][mc]['+mcid+'][fifo]"]').val();
				var lifocost  = $('input[name="item_qty_wise['+modalid+'][mc]['+mcid+'][lifo]"]').val();				
				var unitid    = $('select[name="item_qty_wise['+modalid+'][unit_id]"]').val();				
				var mcqtydata = {"item_catg_id":item_catg_id,"item_group_id":item_group_id,"item_id":item_id,"mcid":mcid,"unitid":unitid,"avgcost":avgcost,"fifocost":fifocost,"lifocost":lifocost,"opqty":opqty};
				mcqty_data.push(mcqtydata);
			});
		var frmdata   ={"item_id":item_id};	
		 show_loader();
		 $.ajax({
		  type: "POST",
		  url: baseurl+"/admin/items/calc_empty_itemval_repbon",
		  data: frmdata,
		  success:function(response){
			  stop_loader();
			  if(response>100){
				  Swal.fire({
			title: '',
			text: "The no. Of records for computation of item value is "+response+" in nos, it may be time consuming, pls confirm to proceed ",
			icon: 'error',
			showCancelButton: true,
			confirmButtonText: 'Yes!',
			customClass: {
			  confirmButton: 'btn btn-primary',
			  cancelButton: 'btn btn-outline-danger ms-1'
			},
			buttonsStyling: false
		}).then(function (result) {		  
			 if (result.isConfirmed==false) {
			   location.reload(); 
			} else{
			   $("#VoucherValuationModal").modal("show");
			   $("#VoucherValuationModal #vchfrm_itemid").val(item_id);			 
			   start_valuation_process(item_id,valmethod_id,mcqty_data);
			}
		});
				  
			  }else{
			     $("#VoucherValuationModal").modal("show");
			     $("#VoucherValuationModal #vchfrm_itemid").val(item_id); 			 
				start_valuation_process(item_id,valmethod_id,mcqty_data);    
			  }
			  		  
		    },
		  error: function (jqXHR, exception){
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
                } 
                alert_notification(error);
            },		  
    }); 
			 
		}
      });
	  return false;
	   }}  */
   
		 
		 $("#view_mcqtywise_modal"+modalid).modal("hide");
		 
	
		return true;
	});

	$(document).on('click','.clear_mc_qty', function(){
		var count = $(this).data('count');
		var status = $(this).data('status');
		
		
		if(status){
			alert_notification('Item has one or more transactions with this unit');
			return false;
		}
		$("#tr"+count).hide();
		 $("#total_mc_qty_op_qty"+count).html('0');
		$("#total_mc_qty_avg"+count).html('0');
		$("#total_mc_qty_fifo"+count).html('0');
		$("#total_mc_qty_lifo"+count).html('0');
		$('#mc_qty_op_'+count).val('');
		$('#mc_qty_unit_'+count).val('');
		$('#lmc_qty_op_'+count).html('0');
		$('#mc_qty_unit_'+count).siblings('.custom-combobox').find('input').val('');
		$('#view_mcqtywise_modal'+count).find('.mc_qty_op_qty_'+count).val('');
		$('#view_mcqtywise_modal'+count).find('.mc_qty_avg_'+count).val('');
		$('#view_mcqtywise_modal'+count).find('.mc_qty_fifo_'+count).val('');
		$('#view_mcqtywise_modal'+count).find('.mc_qty_lifo_'+count).val(''); 
	});

	$(document).on('click','.empty_mc_qty', function(){

		var count = $(this).data('count');

		$('#mc_qty_op_'+count).val('0');
		$("#total_mc_qty_op_qty").html('0');
		$("#total_mc_qty_avg").html('0');
		$("#total_mc_qty_fifo").html('0');
		$("#total_mc_qty_lifo").html('0');
		$('#lmc_qty_op_'+count).html('0');

		$('#view_mcqtywise_modal'+count).find('.mc_qty_op_qty_'+count).val('0');
		$('#view_mcqtywise_modal'+count).find('.mc_qty_avg_'+count).val('0');
		$('#view_mcqtywise_modal'+count).find('.mc_qty_fifo_'+count).val('0');
		$('#view_mcqtywise_modal'+count).find('.mc_qty_lifo_'+count).val('0');
 
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
		  //  alert($(tablerow+' .muom').find(":selected").val());
			// alert($(tablerow+' .muom').find(":selected").text());
		  var txn = $(tablerow+' .muom').find(":selected").text()+"-"+$(tablerow+' .muomqty').val();
		    $("#view_mcqtywise_modal"+modalid+" #qtyunit_txt"+modalid).html(txn);
		 
			 $("#view_mcqtywise_modal"+modalid).modal("show");
		 }
	 
		
	});


	   var batchjson = [];
	   $('#sortTable').DataTable({"ordering":false,"pageLength":5,"bLengthChange": false,info:false,searching: false});
	   </script>
	   
	   <?php
	$all_units=array();
	if($item_units){
		foreach($item_units as $unit_id => $name){
		  $all_units[] = array("label"=>$name,"value"=>$name,"id"=>$unit_id);	
		}
		}
	

	?>
	<script>	
	var unitslist = <?php echo json_encode($all_units);?>; 
	

$(function(){
  // After $("select.selectwidget").combobox() has run…
  $('select.cannotmodify').each(function(){
    var $sel     = $(this),
        $widget  = $sel.next('.custom-combobox'),
        $input   = $widget.find('input.custom-combobox-input'),
        $toggle  = $widget.find('.custom-combobox-toggle');

    // 1) Read-only + remove tabindex so you can’t focus via keyboard
    $input
      .prop('readonly', true)
      .removeAttr('tabindex')
      // optional: give it the “disabled” look
      .css({ backgroundColor: '#eee', cursor: 'default' });

    // 2) Hide the button so there’s no click target
    $toggle.remove();

    // 3) Unbind autocomplete events so nothing can open it
    $sel.removeClass('selectwidget');
    $input
      .off('focus.autocomplete')    // remove the focus handler
      .off('keydown.autocomplete')  // remove arrow‐key handling
      .autocomplete('destroy');     // destroy autocomplete instance
  });
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
	                // console.log(response);
					
					stop_loader();
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
	                    {stop_loader();
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
			});
	});   	
	
	</script>