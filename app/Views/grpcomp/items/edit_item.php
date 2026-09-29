<?php $header = array( 	'title' => 'Update Stock Item' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-sm-6{padding-bottom:2px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
    .myform .select2 {width:75%!important; }
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
             echo form_open(base_url().'/'.$folder_path.'items/modify_item/'.$item_id, $attributes);
       ?>
	  
        <div class="row">
            <?php echo $message_output->run() ;?>
             <div class="col-6"><h3 class="pb-3">Update Stock Item</h3></div>  
             <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
         <div class="col-md-6">
          
          <div class="card p-3 my-2">
              <div class="row">
              <h5 class="pb-2">Item Description</h5>
               <p class="col-12"><label>Item Name</label> <?php $data = array(
								   'name'        => 'item_name',
								   'value'       => $item_info['item_name'],
								   'maxlength'   => '255',
								    'minlength'   =>  "3",
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?><input type="hidden" name="unitdetail_array" id="unitdetail_array" value=""></p>
								  
				 <p class="col-12"><label>Item Sku</label> <?php $data = array(
								   'name'        => 'item_sku',
								   'value'       => $item_info['item_sku'],
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   );
								   echo form_input($data);
								  ?></p>
								  		
								  						  
              	<p class="col-12"><label>Item UPC</label><?php $data = array(
								   'name'        => 'item_shortname',
								    'value'       => $item_info['item_upc'],
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								    'minlength'   =>  "3",
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></p>
               <p class="col-12"><label>Alias Name</label> <?php $data = array(
								   'name'        => 'item_alias',
								   'value'       => $item_info['item_alias'],
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								    'minlength'   =>  "3",
								   'required'    => true
								   );
								   echo form_input($data);
								  ?><input type="hidden" name="batchinfo" id="batchinfo" value=""></p>
                <p class="col-12"><label>Print Name</label> <?php $data = array(
								   'name'        => 'item_printname',
								   'value'       => $enc_string->nc_string($item_info['item_print'],'de'),
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								    'minlength'   =>  "3",
								   'required'    => true
								   );
								   echo form_input($data);
								  ?><input type="hidden" name="batchinfo_array" id="batchinfo_array" value=""></p>
				 <p class="col-12"><label>Sales Account</label>
               
               <?php	
                   echo form_dropdown('item_sales_acc', $sales_acc_dropdown, $item_info['item_sales_acc'],'id="item_sales_acc" class="form-control w-75 selectwidget required" required ');
						?>	
						
              </p>
               <p class="col-12"><label>Purchase Account</label>
                 <?php	
                   echo form_dropdown('item_pur_acc', $purchase_acc_dropdown, $item_info['item_pur_acc'],'id="item_pur_acc" class="form-control w-75 selectwidget required" required ');
						?>	</p>
						
				<p>
				    <button type="button" class="btn btn-outline-secondary btn-sm m-1" data-bs-toggle="modal" data-bs-target="#prodimension">Product Dimension</button>
	                <button type="button" class="btn btn-outline-secondary btn-sm m-1" data-bs-toggle="modal" data-bs-target="#proinfo">Product Information</button>
	                 <button type="button" class="btn btn-outline-secondary btn-sm m-1" data-bs-toggle="modal" data-bs-target="#parameters">Parameters</button>
                     <button type="button" class="btn btn-outline-secondary btn-sm m-1" data-bs-toggle="modal" data-bs-target="#batchmodel">Item Batch</button>				
				</p>
				</div></div>
				</div>
				
				<div class="modal fade pt-5" id="prodimension" tabindex="-1" aria-labelledby="prodimensionLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="prodimenstionLabel">Product Dimension</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                          <div class="row">
                                <p class="col-md-4">&nbsp;</p>  <p class="col-md-5 col-8 fw-bold">Value</p> 
                                <p class="col-md-3 col-4 fw-bold">Units</p>
                            </div>
							<?php 
							$all_dimenstions_edit=array();
							if($item_itemdimens_info){
								foreach($item_itemdimens_info as $dimrow){
									$all_dimenstions_edit[$dimrow['item_dmns_id']]= array('item_dmns_val'=>$dimrow['item_dmns_val'],'item_dmns_unit_id'=>$dimrow['item_dmns_unit_id']);
								}
							}
							
							$all_iteminfonn_edit=array();
							if($iteminfonn_info){
								foreach($iteminfonn_info as $inforow){
									$all_iteminfonn_edit[]= array('iteminfo_line'=>$inforow['iteminfo_line'],
									                               'iteminfo_val'=>$inforow['iteminfo_val'],
																   'iteminfo_label'=>$inforow['iteminfo_label']);
								}
							}
							
							if($product_dimensions){ foreach($product_dimensions as $dimension_row){
								 $dimension_id = $dimension_row['id'];
								 
								  if(isset($all_dimenstions_edit[$dimension_id])){
								  $dimension_txt_val  = $all_dimenstions_edit[$dimension_id]['item_dmns_val'];
								  $dimension_unit_val = $all_dimenstions_edit[$dimension_id]['item_dmns_unit_id'];
								  }
							    else{
								  $dimension_txt_val = '';	
								  $dimension_unit_val ='';
								}
								?>
                            <div class="row">
                                <p class="col-md-4"><?php echo $dimension_row['field_value'];?></p>  
                                <p class="col-md-4 col-8"><input  name="dimension_txt[<?php echo $dimension_id;?>]" value="<?php echo $dimension_txt_val;?>" type="number" min="0" step="any" class="form-control w-100"></p> 
                                <p class="col-md-4 col-4">
                                    <?php echo form_dropdown('dimension_unit['.$dimension_id.']', $item_units, $dimension_unit_val,'id="item_height_unit_id'.$dimension_id.'" class="form-select w-100" '); ?>
                                </p>
                            </div>
							<?php }} ?>
                           
                              </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="clear_dimensions" data-bs-dismiss="modal">Clear</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
                      </div>
                    </div>
                  </div>
                </div>
				
				<div class="modal fade pt-5" id="proinfo" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Product Information</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                          <div class="row">
                                <p class="col-md-3">&nbsp;</p>  <p class="col-md-6 col-8 fw-bold">Name</p> 
                                <p class="col-md-3 col-4 fw-bold">Value</p>
                            </div>
							<?php if(count($all_iteminfonn_edit)>0){
									$ik = count($all_iteminfonn_edit)+1;
								foreach($all_iteminfonn_edit as $iteminfrow){	
								?>
							 <div class="row">
                                <p class="col-md-3"><?php echo $iteminfrow['iteminfo_label'];?></p>  
                                <p class="col-md-6 col-8"><input  name="iteminfo_tag[]" type="text" value="<?php echo $iteminfrow['iteminfo_line'];?>" class="form-control w-100"></p> 
                                <p class="col-md-3 col-4"><input  name="iteminfo_tag_value[]" value="<?php echo $iteminfrow['iteminfo_val'];?>" type="text" class="form-control w-100"></p>
                            </div>							
								<?php } }
								   else 
								     $ik =1;?>						
							
							<?php for($i=$ik;$i<=5;$i++){ ?>
                            <div class="row">
                                <p class="col-md-3">Label <?php echo $i;?></p>  
                                <p class="col-md-6 col-8"><input value="" name="iteminfo_tag[]" type="text" class="form-control w-100"></p> 
                                <p class="col-md-3 col-4"><input value="" name="iteminfo_tag_value[]" type="text" class="form-control w-100"></p>
                            </div>
							<?php } ?>
                            </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="clear_info" data-bs-dismiss="modal">Clear</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
                      </div>
                    </div>
                  </div>
                </div>
                
				<div class="modal fade pt-5" id="parameters" tabindex="-1" aria-labelledby="parametersLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="parametersLabel">Parameters</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
					  
                      <div class="modal-body d-md-flex">
						  <div class="col-md-6 col-lg-6 nav flex-column notespill me-3" id="v-prm-tab" role="tablist" aria-orientation="vertical">
							
							<?php
					       if(count($itmparamtr_info)>0){
							   $ik =count($itmparamtr_info)+1;
							   
							   foreach($itmparamtr_info as $key => $itprrow){ if(($key+1)==1) $activeclass='active'; else $activeclass=''; ?>
							   
							   <a class="text-dark d-flex mb-2 <?php echo $activeclass;?>" id="v-prm<?php echo ($key+1);?>-tab" data-bs-toggle="pill" data-bs-target="#v-prm<?php echo ($key+1);?>" role="tab" aria-controls="v-prm<?php echo ($key+1);?>" aria-selected="false" tabindex="-1">
								<input type="text" class="form-control clickparameter" data-id="<?php echo ($key+1);?>" placeholder="Parameter <?php echo ($key+1);?>" value="<?php echo $itprrow['paramtr_name'];?>" name="parameters[<?php echo ($key+1);?>]" /><span class="material-symbols-outlined pt-2">double_arrow</span>
							</a>
							   <?php
							   
							   } 
						   }
						   else
							   $ik =1;
					        ?>
					  
							
							
							<?php for($i=$ik;$i<=5;$i++){ if($i==1) $activeclass='active'; else $activeclass=''; ?>	
							<a class="text-dark d-flex mb-2 <?php echo $activeclass;?>" id="v-prm<?php echo $i;?>-tab" data-bs-toggle="pill" data-bs-target="#v-prm<?php echo $i;?>" role="tab" aria-controls="v-prm<?php echo $i;?>" aria-selected="false" tabindex="-1">
								<input type="text" class="form-control clickparameter" data-id="<?php echo $i;?>" placeholder="Parameter <?php echo $i;?>" name="parameters[<?php echo $i;?>]" /><span class="material-symbols-outlined pt-2">double_arrow</span>
							</a>
							<?php } ?>
							
						</div>
						<div class="col-md-6 col-lg-6 pe-3 tab-content" id="v-prm-tabContent">
						<?php 
							if(count($itmparamtr_info)>0){
							   $ikl =count($itmparamtr_info)+1;
							   
							   foreach($itmparamtr_info as $key1 => $itprrow){ if(($key1+1)==1) $activeclass='show active'; else $activeclass='';
							       $valueshere = $itprrow['paramtr_values'];
								   ?>
								 <div class="tab-pane fade <?php echo $activeclass;?>" id="v-prm<?php echo ($key1+1);?>" role="tabpane<?php echo ($key1+1);?>" aria-labelledby="v-prm<?php echo $i;?>-tab" tabindex="0">
								<p class="mb-0">Prameter <?php echo ($key1+1);?> values here</p>
								
								<?php if(count($valueshere) >0){
									$incc=count($valueshere)+1;
									foreach($valueshere as $inrkey => $valrow){
									?>
									<input type="text" class="form-control w-100" name="parameter_val[<?php echo ($inrkey+1);?>][]" value="<?php echo $valrow['paramtr_val'];?>" placeholder="value 1" />
								
									<?php
									}
								}
								else {$incc =1;}
								?>
								
								<?php for($j=$incc;$j<=5;$j++){?>
								<input type="text" class="form-control w-100" name="parameter_val[<?php echo $i;?>][]" value="" placeholder="value 1" />
								<?php } ?>		
								<div class="taskmenus">
									<a href="#"><span class="material-symbols-outlined">done</span></a><a href="#"><span class="material-symbols-outlined">close</span></a>
								</div>
							</div>	
								 
								   <?php
								   
							   }
							}
							else
							  $ikl =1;	
							
?>								   
						 
						 <?php for($i=$ikl;$i<=5;$i++){ if($i==1) $activeclass='show active'; else $activeclass=''; ?>	
						  <div class="tab-pane fade <?php echo $activeclass;?>" id="v-prm<?php echo $i;?>" role="tabpane<?php echo $i;?>" aria-labelledby="v-prm<?php echo $i;?>-tab" tabindex="0">
								<p class="mb-0">Prameter <?php echo $i;?> values here</p>
								<?php for($j=1;$j<=5;$j++){?>
								<input type="text" class="form-control w-100" name="parameter_val[<?php echo $i;?>][]" placeholder="value 1" />
								<?php } ?>		
								<div class="taskmenus">
									<a href="#"><span class="material-symbols-outlined">done</span></a><a href="#"><span class="material-symbols-outlined">close</span></a>
								</div>
							</div>
							
							
						 
						 
						 
						 <?php } ?>
						  
						</div>
    </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="clear_info" data-bs-dismiss="modal">Clear</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
                      </div>
                    </div>
                  </div>
                </div>
				
				
				<!-- The Modal -->
<div class="modal fade pt-5" id="batchmodel" tabindex="-1" aria-labelledby="batchmodelLabel" style="display: none;" aria-hidden="true">
                
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Item Batch Details</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">      
        <div id="item_batch_grid"></div>
        
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
            <button type="button" class="btn btn-success" id="validate_uom">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button> 
      </div>

    </div>
  </div>
</div>		


				<div class="col-md-6">
				<div class="card p-3 my-2"><div class="row">
              <h5 class="pb-2">Item Mapping</h5>
              <p class="col-12"><label>Item Group</label>
              <?php	
                   echo form_dropdown('item_group_id', $item_group, $item_info['item_grp_id'],'id="item_group_id" class="form-control w-75 selectwidget required" required ');
						?>	
              
              
              </p>
			<p class="col-12"><label>Item Category</label> <?php	
                   echo form_dropdown('item_catg_id', $item_category, $item_info['item_cat'],'id="item_catg_id" class="form-control selectwidget required" required ');
						?></p>	
						
			<p class="col-12"><label>Default Unit</label> <?php	
                   echo form_dropdown('item_unit_id', $item_units, $item_info['item_unit'],'id="item_unit_id" class="form-control selectwidget required" required ');
						?></p>	
				<p class="col-12"><label>Default Stock Valuation</label> <?php
                     $stockvaluation_list=array('0'=>'AVG COST','1'=>'FIFO','2'=>'LIFFO');	
                   echo form_dropdown('valmethod_id', $stockvaluation_list,$item_info['valmethod_id'],'id="valmethod_id" class="form-control selectwidget required" required ');
						?></p>			
              </div> </div>    
				    
					
					<div class="card p-3 my-2"> <div class="row">
              <h5 class="pb-2">Unit Details</h5>
			   <table id="sortTable" class="display compact" cellspacing="0">
			  <thead>
				 <tr>
					<th>Unit</th>
					<th>Op. Qty</th>
					<th></th>					
				 </tr>
			  </thead>
			<tbody>
			<?php
			    $all_units_values=array();
				$all_units_opbalance=array();
				
				if($GetOpnBalanItm){
				   foreach($GetOpnBalanItm as $kyc =>$keyrow){
					   $all_units_opbalance[$keyrow['mat_cent_id']][$keyrow['item_unit']][]=$keyrow['op_bal_qty'];
					   if(isset($all_units_values[$keyrow['item_unit']]))
					      $all_units_values[$keyrow['item_unit']] +=$keyrow['op_bal_qty'];
				        else
						  $all_units_values[$keyrow['item_unit']] =$keyrow['op_bal_qty'];	
					 }
				}
			
				$il=500;
			 if($all_units_values){
				 foreach($all_units_values as $unitid => $unit_op_bala){$il++;
					 ?>
				<tr id="tr<?php echo $il;?>">
			<td><?php	
                   echo form_dropdown('item_unit_idm[]', $item_units, $unitid,' class="muom form-control selectwidget" ');
						?></td>
			<td><?php $data = array(
								   'name'        => 'item_op_bal_qtym[]',
								   'value'       => $unit_op_bala,
								   'maxlength'   => '255',
								   'class'       => 'muomqty form-control w-100'
								   );
								   echo form_input($data);
								  ?></td>			
			<td>
			<button type="button" class="btn btn-outline-secondary btn-sm mcqtywise_modal" data-id="<?php echo $il;?>">MC Qty Wise</button>
			</td>
			
             </tr>
					<?php				
				 }
				 
				 
			 }


			for($i=1;$i<=25;$i++){ ?>
			<tr id="tr<?php echo $i;?>">
			<td><?php	
                   echo form_dropdown('item_unit_idm[]', $item_units, set_value('item_unit_idm'),' class="muom form-control selectwidget" ');
						?></td>
			<td><?php $data = array(
								   'name'        => 'item_op_bal_qtym[]',
								   'value'       => set_value("item_op_bal_qty"),
								   'maxlength'   => '255',
								   'class'       => 'muomqty form-control w-100'
								   );
								   echo form_input($data);
								  ?></td>			
			<td>
			<button type="button" class="btn btn-outline-secondary btn-sm mcqtywise_modal" data-id="<?php echo $i;?>">MC Qty Wise</button>
			</td>
			
             </tr>	
			<?php } ?>			 
			</tbody>
		</table>
		<?php
		$il=500;
		if($all_units_values){
				 foreach($all_units_values as $unitid => $unit_op_bala){$il++;?>
			<div class="modal fade pt-5" id="view_mcqtywise_modal<?php echo $il;?>" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">MC Wise Qty(<span id="qtyunit_txt<?php echo $il;?>"></span>) Information</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                          <div class="row">
                                <p class="col-md-2 col-8 fw-bold">MC</p> 
                                <p class="col-md-2 col-4 fw-bold">Qty</p>
								<p class="col-md-2 col-4 fw-bold">AVG COST</p>
								<p class="col-md-2 col-4 fw-bold">FIFO</p>
								<p class="col-md- col-4 fw-bold">LIFO</p>
                            </div>
							<?php $bcounter=0;foreach($material_centre_dropdown as $mcid => $mcname){ if($mcname!=''){ $bcounter++;

								if(isset($GetOpnValueItm[$mcid][$unitid][0]))
									  $mcqtywise_avg = $GetOpnValueItm[$mcid][$unitid][0];
								  else
									$mcqtywise_avg = 0;  
								
								if(isset($GetOpnValueItm[$mcid][$unitid][1]))
									  $mcqtywise_fifo = $GetOpnValueItm[$mcid][$unitid][1];
								  else
									$mcqtywise_fifo = 0; 
								
								if(isset($GetOpnValueItm[$mcid][$unitid][2]))
									  $mcqtywise_lifo = $GetOpnValueItm[$mcid][$unitid][2];
								  else
									$mcqtywise_lifo = 0; 
							?>
                            <div class="row modalrow<?php echo $il;?>">
                            <p class="col-md-2 col-8"><?php	echo ucwords($mcname);?>	</p> 
							<?php
							if(isset($all_units_opbalance[$mcid][$unitid][0]))
								 $uopbalance=$all_units_opbalance[$mcid][$unitid][0];
							 else
								 $uopbalance=0;
							?>
                            <p class="col-md-2 col-4"><input type="text" form="myform"   name="mcqtywise_qty[<?php echo $mcid;?>][]"  value="<?php echo $uopbalance;?>" class="modal_muomqty form-control w-100"></p>							
							<p class="col-md-2 col-4"><input type="text" form="myform" placeholder="Value"  name="mcqtywise_avg[<?php echo $mcid;?>][]" value="<?php echo $mcqtywise_avg;?>" class="form-control w-100"></p>
							<p class="col-md-2 col-4"><input type="text" form="myform" placeholder="Value"  name="mcqtywise_fifo[<?php echo $mcid;?>][]" value="<?php echo $mcqtywise_fifo;?>" class="form-control w-100"></p>
							<p class="col-md-2 col-4"><input type="text" form="myform" placeholder="Value"  name="mcqtywise_lifo[<?php echo $mcid;?>][]"  value="<?php echo $mcqtywise_lifo;?>" class="form-control w-100"></p>
							</div>
							<?php } }?>
                            </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="clear_info" data-bs-dismiss="modal">Clear</button>
                        <button type="button" class="btn btn-primary save_mcq_qty_list" data-id="<?php echo $il;?>">Save</button>
                      </div>
                    </div>
                  </div>
                </div>	 
				 
	<?php	}}
		
		
		
		?>
		
		
		
		
		<?php for($i=1;$i<=25;$i++){ ?>
		<div class="modal fade pt-5" id="view_mcqtywise_modal<?php echo $i;?>" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">MC Wise Qty(<span id="qtyunit_txt<?php echo $i;?>"></span>) Information</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
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
                            <p class="col-md-2 col-4"><input type="text" form="myform"   name="mcqtywise_qty[<?php echo $mcid;?>][]" class="modal_muomqty form-control w-100"></p>							
							<p class="col-md-2 col-4"><input type="text" form="myform" placeholder="Value"  name="mcqtywise_avg[<?php echo $mcid;?>][]" class="form-control w-100"></p>
							<p class="col-md-2 col-4"><input type="text" form="myform" placeholder="Value"  name="mcqtywise_fifo[<?php echo $mcid;?>][]" class="form-control w-100"></p>
							<p class="col-md-2 col-4"><input type="text" form="myform" placeholder="Value"  name="mcqtywise_lifo[<?php echo $mcid;?>][]"  class="form-control w-100"></p>
							</div>
							<?php } }?>
                            </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="clear_info" data-bs-dismiss="modal">Clear</button>
                        <button type="button" class="btn btn-primary save_mcq_qty_list" data-id="<?php echo $i;?>">Save</button>
                      </div>
                    </div>
                  </div>
                </div>
			  <?php } ?>
			   
              </div></div>
			  
			  
              
              
              </div>
              
              <div class="col-md-12">
              <div class="card p-3 disablediv my-2"><div class="row">
              <h5 class="pb-2">Tax Details</h5>
               
               <p class="col-sm-6"><label>Tax Category</label> <?php	
                   echo form_dropdown('item_tax', $tax_category, set_value('item_tax'),'id="item_tax" class="form-control"  ');
						?>	</p>
               <p class="col-sm-6"><label>HSN / SAC</label> <?php $data = array(
								   'name'        => 'item_hsn',
								   'value'       => '',
								   'maxlength'   => '255',
								   'class'       => 'form-control'
								   );
								   echo form_input($data);
								  ?></p>
                 </div> </div>     
               
             <div class="card disablediv p-3 my-2"><div class="row">
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
                 <input type="submit" value="SAVE" class="btn btn-primary mx-2" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                <a href="<?php echo $base_url.'items/list_items';?>" class="btn btn-secondary mx-2" >QUIT</a>
              </div>
            
            
              </div> </form>
			  <style>
			  /*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
}
			  </style>
<?php echo view('includes/footer_scripts'); ?>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
   <script>
   
   $(document).on("click",".save_mcq_qty_list",function(){
	var modalid = $(this).data("id");
     var tablerow = '#tr'+modalid;


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

$(".mcqtywise_modal").on("click",function(){
  var modalid = $(this).data("id");	
  var tablerow = '#tr'+modalid;
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
	
	 if(emptyuom >0){
		 alert_notification("Kindly fill Unit Details first!!!");
		 
	 }else{	
	   //  alert($(tablerow+' .muom').find(":selected").val());
		// alert($(tablerow+' .muom').find(":selected").text());
	  var txn = $(tablerow+' .muom').find(":selected").text()+"-"+$(tablerow+' .muomqty').val();
	    $("#view_mcqtywise_modal"+modalid+" #qtyunit_txt"+modalid).html(txn);
	 
		 $("#view_mcqtywise_modal"+modalid).modal("show");
	 }
 
	
});


   var batchjson = [];
   $('#sortTable').DataTable({"pageLength":5,"bLengthChange": false,info:false,searching: false});</script>
   <?php
$all_units=array();
if($item_units){
	foreach($item_units as $unit_id => $name){
	  $all_units[] = array("label"=>$name,"value"=>$name,"id"=>$unit_id);	
	}
	}
$all_batches=array();	
 if($get_item_batch){
	 foreach($get_item_batch as $batchrow){
		 if($batchrow['batch_mfr'])
			 $batch_mfr     = date('d-m-Y',strtotime($batchrow['batch_mfr']));
		 else
			 $batch_mfr = '';
		 if($batchrow['batch_expiry'])
		     $batch_expry   = date('d-m-Y',strtotime($batchrow['batch_expiry']));
		 else
			 $batch_expry   = '';
		 
			 $batch_qty     = $batchrow['batch_qty'];
			 $batch_unit    = $batchrow['batch_unit'];		
             $batch_unit_name = $batchrow['batch_unit_name'];			 
		     $all_batches[] = array('batch_id'=>$batchrow['batch_id'],'batch_no'=> $batchrow['batch_no'], 'manufacturing_date'=> $batch_mfr,'batch_qty'=>'','batch_uom'=>$batch_unit_name, 'expiry_date'=> $batch_expry,
		                            'startDate'=>$fy_begndt,'endDate'=>$fy_end,'batch_qty'=>$batch_qty,'batch_uom_id'=>$batch_unit);
	 }
	 
 }	

?>
<script>
var batchjson = <?php echo json_encode($all_batches);?>; 
var unitslist = <?php echo json_encode($all_units);?>; 

function units_validation_fun(){
	var multiuol_sums=[];
	var batch_item_checked=[];
	var units_item_checked=[];
    var unit_details_arry=[];
    var errros=0;
	var difference=0;
	 var batchdata = batchjson;//$("#item_batch_grid").pqGrid('option', 'dataModel.data');
		   for (var j = 0; j < batchdata.length; j++) {
				var batch_uom_id     = batchdata[j]['batch_uom_id'];
				if(typeof batch_uom_id !=="undefined" && batch_uom_id!=''){
				var batch_qty     = parseFloat(batchdata[j]['batch_qty']);
				if(typeof multiuol_sums[batch_uom_id]=="undefined")
				multiuol_sums[batch_uom_id]=batch_qty;
			    else{				
				multiuol_sums[batch_uom_id] +=batch_qty;
				}
				
				var batch_no = batchdata[j]['batch_no'];
				var expiry_date = batchdata[j]['expiry_date'];
				var manufacturing_date = batchdata[j]['manufacturing_date'];
				
				}
			batch_item_checked.push({"batch_uom_id":batch_uom_id,"batch_qty":batch_qty,"batch_no":batch_no,"expiry_date":expiry_date,
									 "manufacturing_date":manufacturing_date});	
		   }		 
		
		$('.muom').each(function(key){
			var master_unit_id = $(this).val();
			  var index = $(this).index();	      
		      var opqty =  parseFloat($("input[name='item_op_bal_qtym[]").eq(key).val());
		      
			  if(typeof unit_details_arry[master_unit_id]==="undefined")
				unit_details_arry[master_unit_id]=opqty;
			    else{				
				unit_details_arry[master_unit_id] +=opqty;
				}
			
			units_item_checked.push({"master_unit_id":master_unit_id,"opqty":opqty});
			
			
			
			if(typeof multiuol_sums[master_unit_id] != "undefined"){
				//console.log(multiuol_sums[master_unit_id] +">"+opqty);
		    // case1 ENSURE THAT THE TOTAL OF ITEM MASTER OP. BALANCE UNIT WISE TO NOT TO EXCEED THE TOTAL UNIT WISE IN ITEM BATCH MASTER. 
		     if(parseFloat(multiuol_sums[master_unit_id]) > parseFloat(opqty))
			   errros=errros+1;
			 //case 2 IF THE TOTAL OF ITEM IN ITEM BATCH MASTER IS LESS THAN THE ITEM MASTER OPENING UNIT WISE BALANCE, THAN THE DIFFERENCE WILL BE MARKED AS UNDEFINED. 	
		     else if(parseFloat(multiuol_sums[master_unit_id]) < parseFloat(opqty) ){
			   difference=1;
		       }
			   
			   var i = batch_item_checked.findIndex(function(o) {
						   return o.batch_uom_id == master_unit_id;
						});
						console.log(i);
						if(i >= 0){
							batch_item_checked[i].batch_difference = (parseFloat(multiuol_sums[master_unit_id])-parseFloat(opqty));
						}   
						
			}
		   });	

			//console.log(units_item_checked);		   
		   $("#unitdetail_array").val(JSON.stringify(units_item_checked));	
		   if(errros >0 ){
			alert_notification("Unit Qty mismatch!!!! ");
			return false;
			}else if(difference==1){				
			$("#batchinfo_array").val(JSON.stringify(batch_item_checked));
			
		 	}else{
			$("#batchinfo_array").val('');
			$("#unitdetail_array").val('');		
			}
	$("#batchmodel").modal("hide");		
	}
$("#validate_uom").on("click",function(){	
	units_validation_fun();
});

			
$(".call_batch_modal").on("click",function(){	
var emptyuom=0;
var counter=0;
$('.muom').each(function(){
	if(counter==0 && $(this).val()==''){
       emptyuom=emptyuom+1;
    }
	counter=counter+1;
});
 if(emptyuom >0){
	 alert_notification("Kindly fill Unit Details first!!!");
	 
 }else{	
    $("#batchmodel").modal("show");	
 }
});	
function dateEditor(ui) {
            
            var $inp = ui.$cell.find("input"),
                di = ui.dataIndx,
                rd = ui.rowData,
                minDate, maxDate,
                startDate = rd.startDate,
                endDate = rd.endDate,                
                grid = this,
                validate = function (that) {
                    var valid = grid.isValid({
                        dataIndx: ui.dataIndx,
                        value: $inp.val(),
                        rowIndx: ui.rowIndx
                    }).valid;
                    if (!valid) {
                        that.firstOpen = false;
                    }
                };

            //calculate minDate and maxDate.
            if(di == "startDate"){
                maxDate = rd.endDate;
            }
            else if(di == "endDate"){
                minDate = rd.startDate;
            }
			
			  $inp.on("focusout", function (e) {
				var expiry_date = rd.expiry_date;
				console.log(expiry_date);
				if(!isValidDate(expiry_date)){
				  rd.expiry_date = '';
				  e.preventDefault();
				}
			});
	
            //initialize the editor
            $inp.inputmask("99/99/9999", {
				mask: "99-99-9999",
				alias: "date",
				placeholder: "dd-mm-yyyy",
				insertMode: false,
			})
            .datepicker({
				altFormat: "dd-mm-yyyy",
                dateFormat: "dd-mm-yy",
			    minDate: minDate,
                maxDate: maxDate,
                changeMonth: true,
                changeYear: true,
                showAnim: '',
                onSelect: function () {
                    this.firstOpen = true;
                    //validate(this);
                },
                beforeShow: function (input, inst) {
                    return !this.firstOpen;
                },
                onClose: function () {
                    this.focus();
                }
            });
        };
 
    for(var i=0;i<10;i++){
        batchjson.push({'batch_id':'','batch_no': '', 'manufacturing_date': '','batch_qty':'','batch_uom':'', 'expiry_date': '','startDate':'<?php echo $fy_begndt;?>','endDate':'<?php echo $fy_end;?>'});
    }
	var units_auto_complete = function (ui) { 
        var rd = ui.rowData;
        var $inp = ui.$cell.find("input");
        $inp.autocomplete({
            source: unitslist,
            selectItem: { on: true }, //custom option
            highlightText: { on: true }, //custom option
            minLength: 0,
            select: function(event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
                rd.batch_uom = ui.item.label;
                rd.batch_uom_id =ui.item.id;
            }
        }).focus(function () {
            $(this).autocomplete("search", "");
            rd.batch_uom = '';
            rd.batch_uom_id = '';
        }).focusout(function () {   
            if(rd.batch_uom_id == '')
            {
                var index = unitslist.findIndex(function(obj) {

                    var string = obj.label.toLowerCase();
                    var text = rd.batch_uom.toLowerCase();
                     
                    return text != '' ? string.includes(text) : false;
                });
                if(index > -1){
                    rd.batch_uom = unitslist[index].label;
                    rd.batch_uom_id = unitslist[index].id;
                    
                }
                else{
                    rd.batch_uom = '';
                    rd.batch_uom_id = '';
                }
            }
        });
    }	
	
var batch_dataModel = {'data':batchjson}; 
var date_column = { 
            dataType: 'string',          
		    editor: {
		        type: 'textbox',
		        init: dateEditor
		    }
		};
var batch_colModel = [
    { title: "BATCH NO", dataIndx: "batch_no", width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: "textbox",
        }
    },
	$.extend( true, {title: "MANUFACTURING DATE", width: 100,dataIndx: "manufacturing_date" ,cls: 'pq-drop-icon pq-side-icon',}, date_column),      
    $.extend( true, {title: "EXPIRY DATE", width: 100, dataIndx: "expiry_date" ,cls: 'pq-drop-icon pq-side-icon',}, date_column),
    { title: "QTY", dataIndx: "batch_qty", dataType: "float",width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: "textbox",
        },render: function (ui) {
				            var rd = ui.rowData;
							/* if(ui.cellData>0 && ui.cellData!=''){
				            var cellData=render_qty(ui.cellData); 
							rd.item_qty=cellData;
							}
						     else
							rd.item_qty=cellData;
						    return cellData; */
						  },
	validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],					  
    },
	{ title: "UOM", dataIndx: "batch_uom", width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: "textbox",
			init:units_auto_complete,
            options: [],
        },render: function (ui) {
                          var options = ui.column.editor.options,
                          cellData = ui.cellData;
                          for (var i = 0; i < options.length; i++) {
                              var option = options[i];
                                if (option.label == cellData) {
                                  return option.label;
                                }
                          }
                      },
    },
   
];

var billsObj = {
    collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, //disable maximize,toggle button.
    height: 400,
    selectionModel: { type: 'cell' },
    scrollModel: { autoFit: true },
    pageModel: { type: 'local' },
    wrap:false,
    numberCell: { show: true },
    dataModel: batch_dataModel,
    colModel: batch_colModel,    
    editable: true,
    cellSave: function(evt, ui){
       this.refresh();
    },
    editModel: {
        clicksToEdit: 1,
            keyUpDown: false
        },
    showTitle: true,
    create: function (evt, ui) {// make first row auto selected
        var grid = this,
        $select_row = $(".select-row"),
        data = ui.dataModel.data;
        grid.setSelection({ rowIndx: 0, focus: false });
    }
};
            
            

$("#batchmodel").on('shown.bs.modal', function () {   
    if($("#item_batch_grid").pqGrid('instance')){     
        $("#item_batch_grid").pqGrid('refresh');
    }
    else
        $("#item_batch_grid").pqGrid(billsObj);
});

 $(document).on('submit', '#myform', function(e){
	   
	    e.preventDefault();
		var billsundry_item_checked=[];
		 if($("#item_batch_grid").pqGrid('instance')){
		 var batchdata = $("#item_batch_grid").pqGrid('option', 'dataModel.data');
		   for (var j = 0; j < batchdata.length; j++) {
				var batch_no     = batchdata[j]['batch_no'];
				var manufacturing_date     = batchdata[j]['manufacturing_date'];
				var expiry_date     = batchdata[j]['expiry_date'];
				
				var batch_uom     = batchdata[j]['batch_uom'];
				var batch_uom_id     = batchdata[j]['batch_uom_id'];
				var batch_id     = batchdata[j]['batch_id'];
				var batch_qty    = batchdata[j]['batch_qty'];
				
        
				if(batch_no != '' && manufacturing_date != '' && expiry_date != ''){
					billsundry_item_checked.push({
							"batch_no": batch_no,
							"manufacturing_date": manufacturing_date,
							"expiry_date" : expiry_date,
							"batch_uom" :batch_uom,
							"batch_uom_id":batch_uom_id,
							"batch_id" : batch_id,
							"batch_qty" : batch_qty
							
						 });
					}       
         }
   
      $("#batchinfo").val(JSON.stringify(billsundry_item_checked));
	  
}
	 units_validation_fun();
   
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
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    alert_success(response.message);
                    window.location.reload();
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

                        var html = `
                            <div class="alert alert-danger alert-dismissible">
                              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              <ul>${list}</ul>
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
