<?php $header = array( 	'title' => 'Add Voucher Type' ); ?>
<?php echo view('includes/header',$header); ?>
<?php echo view('includes/loader'); ?>
<div class="main-wrapper">
<?php echo view('includes/'.$folder_path.'inner_header'); ?>
<?php echo view('includes/'.$folder_path.'menu'); ?>
<div class="page-wrapper page-wrapper-one">
 <div class="content">
   <div class="page-header">
      <div class="page-title">
         <h4>Voucher Type</h4>
         <h6>Add/Update Voucher Type</h6>
      </div>	 
   </div>
   <div class="card">
      <div class="card-body">
	  <?php $attributes = " id='form1' name='form1' class='needs-validation' novalidate";
             echo form_open(base_url().'/'.$folder_path.'vouchers/add_type', $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
         <div class="row">
            <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Voucher Type Name </label>
				 <?php $data = array(
								   'name'        => 'comp_vch_type',
								   'value'       => set_value('comp_vch_type'),
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?>		
               </div>
            </div>
            
			</div>  
          <div class="row">
		      <div class="col-lg-12">
               <button type="submit" class="btn btn-success btn-submit me-2">Submit</button>              
            </div>
			</div>
         </div>
		 <?php echo form_close(); ?>
      </div>
   </div>
</div>
</div>
</div>

<?php echo view('includes/footer_scripts'); ?>	 
<?php echo view('includes/footer'); ?>