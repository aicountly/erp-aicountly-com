<?php $header = array( 	'title' => 'Add Clients' ); ?>
<?php echo view('includes/header',$header); ?>
<?php echo view('includes/loader'); ?>
<div class="main-wrapper">
<?php echo view('includes/'.$folder_path.'inner_header'); ?>
<?php echo view('includes/'.$folder_path.'menu'); ?>
<div class="page-wrapper page-wrapper-one">
 <div class="content">
   <div class="page-header">
      <div class="page-title">
         <h4>Clients Management</h4>
         <h6>Add/Update Clients</h6>
      </div>
	  <h6>Note : Default Password : (123456)</h6>
   </div>
   <div class="card">
      <div class="card-body"> 
	  <?php  $attributes = " id='form1' name='form1' class='needs-validation' novalidate";
             echo form_open(base_url().'/'.$folder_path.'clients/add', $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
         <div class="row">
            <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Email address </label>
				  <?php $data = array(
									  'name'        => 'email',
									  'id'          => 'email',
									  'value'       => set_value('email'),
									  'maxlength'   => '100',
									  'class'       =>  'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?>									                    				  
               </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Enter password</label>
				  <div class="pass-group">
                 <?php $data = array(
									  'name'        => 'userpass',
									  'id'          => 'userpass',
									  'value'       => '123456',
									  'maxlength'   => '32',
									  'type'   => 'password',
									  'class'       =>  'pass-input'
									  );
									  echo form_input($data);
									  ?><span class="fas toggle-password fa-eye-slash"></span>
</div>			
               </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Enter Confirm password</label>
				  <div class="pass-group">

                  <?php $data = array(
									  'name'        => 'compass',
									  'id'          => 'compass',
									  'value'       => '123456',
									  'maxlength'   => '32',
									  'type'        => 'password',
									  'class'       =>  ' pass-inputs'
									  );
									  echo form_input($data);
									  ?><span class="fas toggle-passworda fa-eye-slash"></span>
</div>
               </div>
            </div>
			 <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Company Name </label>
                      <?php $data = array(
									  'name'        => 'buss_org_name',
									  'id'          => 'buss_org_name',
									  'value'       => set_value('buss_org_name'),
									  'maxlength'   => '255',
									   'class'       =>  'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?>
            </div>
			</div>
			<div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Contact First Name </label>
                      <?php $data = array(
									  'name'        => 'first_name',
									  'id'          => 'first_name',
									  'value'       => set_value('first_name'),
									  'maxlength'   => '100',
									   'class'       =>  'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?>
                                </div>
            </div>
			<div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Contact Last Name </label>
                      <?php $data = array(
									  'name'        => 'last_name',
									  'id'          => 'last_name',
									  'value'       => set_value('last_name'),
									  'maxlength'   => '100',
									   'class'      =>  'form-control',
									   'required'   => true
									  );
									  echo form_input($data);
									  ?>
                                </div>
            </div>
			
            <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>City </label>
                      <?php $data = array(
									  'name'        => 'city_name',
									  'id'          => 'city_name',
									  'value'       => set_value('city_name'),
									  'maxlength'   => '100',
									   'class'       =>  'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?>
                                </div>
            </div>
			
			 <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>State </label>
                      <?php		echo form_dropdown('state_id', $StatesDropdown, set_value('state_id'),'id="state_id" class="form-control select" required ');
						?>
					<div class="invalid-feedback">
				Please select an option.
					</div>
                                </div>
            </div>
			
			 <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Postal code </label>
                     <?php $data = array(
									  'name'        => 'postal_code',
									  'id'          => 'postal_code',
									  'value'       => set_value('postal_code'),
									  'maxlength'   => '8',
									   'class'       =>  'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?>
                                </div>
            </div>
			 <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Phone </label>
                     <?php $data = array(
									  'name'        => 'primary_phone',
									  'id'          => 'primary_phone',
									  'value'       => set_value('primary_phone'),
								      'data-mask'   => '(999) 999-9999',
									  'maxlength'   => '15',
									   'class'       =>  'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?>
					  </div>
            </div>
			
			 <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Address </label>
                    <?php $data = array(
									  'name'        => 'address',
									  'id'          => 'address',
									  'value'       => set_value('address'),
									  'rows'          => '3',
									   'class'       =>  'form-control',
									   'required'    => true
									  );
									  echo form_textarea($data);
									  ?>
					  </div>
            </div>
			 <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Special Notes </label>
                    <?php $data = array(
									  'name'        => 'notes',
									  'id'          => 'notes',
									  'value'       => set_value('notes'),
									  'rows'        => '8',
									  'class'       =>  'form-control',
									   'required'    => true
									  );
									  echo form_textarea($data);
									  ?>
					  </div>
             </div>
             <div class="col-lg-12 col-sm-6 col-12">
               <div class="form-group">
                  <label>Companies/Source </label>
                   <div style="overflow:auto; width:100%; position:relative;  height:250px; border:1px solid #BDBDBD; padding-left:10px;">
                                   <?php									
									unset($all_company[""]);
									foreach($all_company as $key=> $val)
									{
									?> 
                                   <div class="checkbox">
                                        <label>
                                          <input type="checkbox" name="link_user_id[]" id="link_user_id"  value="<?php echo $key; ?>" class="checkbox style-0">
                                          <span><?php echo $val; ?></span>
                                        </label>
                                   </div>
                                <?php } ?>
                                </div>
				   </div>
            </div>
           
           
            <div class="col-lg-12">
               <button type="submit" class="btn btn-submit me-2">Submit</button>
               <a href="<?php echo base_url().$folder_path;?>clients" class="btn btn-cancel">Cancel</a>
            </div>
         </div>
		 <?php echo form_close(); ?>
      </div>
   </div>
</div>
</div>
</div>

<?php echo view('includes/footer_scripts'); ?>	  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
<script>
$(document).ready(function() {
$("#primary_phone").inputmask("(999) 999-9999");
});
</script>	
<?php echo view('includes/footer'); ?>