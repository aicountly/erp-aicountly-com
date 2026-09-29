<?php $header = array( 	'title' => 'Add Stock Item' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding-bottom:10px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select,.myform .select2 {width:75%;}
</style>	  
      <?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().'/'.$folder_path.'items/add_item', $attributes);
       ?>
        <div class="row">
             <div class="col-6"><h3 class="pb-3">Add Stock Item</h3></div>  <div class="col-6"><span class="float-end">
			 </span></div> 
      <div class="col-md-6">
               <div class="col-12"><label>Item Name</label> <?php $data = array(
								   'name'        => 'item_name',
								   'value'       => '',
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
               <div class="col-12"><label>Item Alias</label> <?php $data = array(
								   'name'        => 'item_alias',
								   'value'       => '',
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
               <div class="col-12"><label>Print Name</label> <?php $data = array(
								   'name'        => 'item_printname',
								   'value'       => '',
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?>
				</div>
               <div class="col-12"><label>Group</label> <?php	
                   echo form_dropdown('item_group_id', $item_group, set_value('item_group_id'),'id="item_group_id" class="form-control"  ');
						?>	
			   </div>
			   
               <div class="col-12"><label>Tax Category</label> <?php	
                   echo form_dropdown('item_tax', $tax_category, set_value('item_tax'),'id="item_tax" class="form-control"  ');
						?>	</div>
               <div class="col-12"><label>HSN / SAC</label> <?php $data = array(
								   'name'        => 'item_hsn',
								   'value'       => '',
								   'maxlength'   => '255',
								   'class'       => 'form-control'
								   );
								   echo form_input($data);
								  ?></div>
               <div class="col-12"><label>Unit</label>  <?php	
                   echo form_dropdown('item_unit_id', $item_units, set_value('item_unit_id'),'id="item_unit_id" class="form-control"  ');
						?></div>            
               <div class="col-12"><label>Op Stock Qty</label> <?php $data = array(
								   'name'        => 'item_op_bal',
								   'value'       => '',
								   'maxlength'   => '255',
								   'class'       => 'form-control'
								   );
								   echo form_input($data);
								  ?></div>
             
               <div class="col-12"><label>Item Sales Account</label>
               
               <?php	
                   echo form_dropdown('item_sales_acc', $sales_acc_dropdown, set_value('item_sales_acc'),'id="item_sales_acc" class="form-control select2" required ');
						?>	
						
              </div>
               <div class="col-12"><label>Item Purchase Account</label>
                 <?php	
                   echo form_dropdown('item_pur_acc', $purchase_acc_dropdown, set_value('item_pur_acc'),'id="item_pur_acc" class="form-control select2" required  ');
						?>	
               
              </div>
      </div>
	  
       <div class="col-md-6">
               <div class="col-12"><label>Short Name</label><?php $data = array(
								   'name'        => 'item_shortname',
								   'value'       => '',
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
			
               <div class="col-12"><label>Item Category</label><?php	
                   echo form_dropdown('item_catg_id', $item_category, set_value('item_catg_id'),'id="item_catg_id" class="form-control select"  ');
						?>
			   </div> 
			  <div class="col-12"><label>Item CES</label><?php $data = array(
								   'name'        => 'item_ces',
								   'value'       => '',
								   'maxlength'   => '255',
								   'class'       => 'form-control'
								   );
								   echo form_input($data);
								  ?>
			   </div> 
				<div class="col-12"><label>Material Center</label><?php	
                   echo form_dropdown('item_mat_cent_name', $material_centre_dropdown, set_value('item_mat_cent_name'),'id="item_mat_cent_name" class="form-control select"  ');
				?>
			   </div> 
          </div>
		  
             <div class="col-12 text-center my-3">
                 <input type="submit" value="SAVE" class="btn btn-primary mx-2">
                <a href="<?php echo $base_url.'items/list_items';?>" class="btn btn-secondary mx-2" >QUIT</a>
              </div>
            
            
              </div> </form>
<?php echo view('includes/footer_scripts'); ?>
 <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 <script>
$(function() {
    $('.select2').select2({ width: '100%'});
});
</script>