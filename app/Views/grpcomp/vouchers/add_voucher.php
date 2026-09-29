<?php $header = array( 	'title' => 'Add Voucher' ); ?>
<?php echo view('includes/header',$header); ?>
<?php echo view('includes/loader'); ?>
<div class="main-wrapper">
<?php echo view('includes/'.$folder_path.'inner_header'); ?>
<?php echo view('includes/'.$folder_path.'menu'); ?>
<div class="page-wrapper page-wrapper-one">
 <div class="content">
   <div class="page-header">
      <div class="page-title">
         <h4>Vouchers</h4>
         <h6>Add/Update Vouchers</h6>
      </div>	 
   </div>
   <div class="card">
      <div class="card-body">
	  <?php $attributes = " id='form1' name='form1' class='needs-validation' novalidate";
             echo form_open(base_url().'/'.$folder_path.'vouchers/add_voucher', $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
         <div class="row mx-0 p-0 mb-2">
            <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Voucher Type</label>
				 <?php	
                   echo form_dropdown('comp_vch_type', $vch_type_dropdown, set_value('comp_vch_type'),'id="comp_vch_type" class="form-control select" required ');
						?>		
               </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Voucher Name </label>
				 <?php $data = array(
								   'name'        => 'comp_vch_name',
								   'value'       => set_value('comp_vch_name'),
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?>		
               </div>
            </div>
			<div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Series Prefix </label>
				 <?php $data = array(
								   'name'        => 'comp_vch_series_prefix',
								   'value'       => set_value('comp_vch_series_prefix'),
								   'maxlength'   => '255',
								   'class'       => 'form-control'
								   
								   );
								   echo form_input($data);
								  ?>		
               </div>
            </div>
			<div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Series No. </label>
				 <?php $data = array(
								   'name'        => 'comp_vch_series_no',
								   'value'       => set_value('comp_vch_series_no'),
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?>		
               </div>
            </div>
			<div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Series Suffix </label>
				  <?php $data = array(
								   'name'        => 'comp_vch_series_suffix',
								   'value'       => set_value('comp_vch_series_suffix'),
								   'maxlength'   => '255',
								   'class'       => 'form-control'
								   
								   );
								   echo form_input($data);
								  ?>	
               </div>
            </div>
			
			</div>  
          <div class="row">
		      <div class="col-lg-12">
               <button type="submit" class="btn btn-success btn-submit me-2">Submit</button>
               <a href="<?php echo base_url().$folder_path;?>ads" class="btn btn-success btn-cancel">Cancel</a>
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