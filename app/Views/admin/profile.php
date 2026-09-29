<?php $header = array( 	'title' => 'Update Profile' ); ?>
<?php echo view('includes/header',$header); ?>
<?php echo view('includes/loader'); ?>
<div class="main-wrapper">
<?php echo view('includes/'.$folder_path.'inner_header'); ?>
<?php echo view('includes/'.$folder_path.'menu'); ?>
<div class="page-wrapper page-wrapper-one">
 <div class="content">
   <div class="page-header">
      <div class="page-title">
         <h4>Profile Management</h4>
         <h6>Update Profile</h6>
      </div>	 
   </div>
   <div class="card">
      <div class="card-body">
	  <?php  $attributes = array('id' => 'form1', 'name' => 'form1', 'autocomplete' => 'off');
             echo form_open(base_url().'/'.$folder_path.'profile', $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
         <div class="row">
            <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Company Name </label>
				  <?php $data = array(
									  'name'        => 'buss_org_name',
									  'id'          => 'buss_org_name',
									  'value'       => $profile_info['buss_org_name'],
									  'maxlength'   => '255',
									  'class'       => 'form-control'
									  );
									  echo form_input($data);
									   echo form_hidden('email_id', $profile_info['email']);
									  ?>									                    				  
               </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Contact First Name</label>
				 <?php $data = array(
									  'name'        => 'first_name',
									  'id'          => 'first_name',
									  'value'       => $profile_info['first_name'],
									  'maxlength'   => '100',
									   'class'       => 'form-control'
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
									  'value'       => $profile_info['last_name'],
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?>
               </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Email address </label>
                     <?php $data = array(
									  'name'        => 'email',
									  'id'          => 'email',
									  'value'       => $profile_info['email'],
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?>
                                </div>
            </div>
          
            <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Contact Phone No. </label>
                  <?php $data = array(
									  'name'        => 'primary_phone',
									  'id'          => 'primary_phone',
									  'value'       => $profile_info['primary_phone'],
									  'maxlength'   => '255',
									   'class'       => 'form-control'
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
									  'value'       => $profile_info['city_name'],
									  'maxlength'   => '255',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
                
				   </div>
            </div>
			 <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>State </label>
                 <?php
					 echo form_dropdown('state_id', $states_array, $profile_info['state_id'],'id="state_id" class="select form-control" ');
					 ?>
				 
				   </div>
            </div>
			<div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Postal Code </label>
                 
				  <?php $data = array(
									  'name'        => 'postal_code',
									  'id'          => 'postal_code',
									  'value'       => $profile_info['postal_code'],
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
             

				   </div>
            </div>
			
			<div class="col-lg-12 col-sm-6 col-12">
               <div class="form-group">
                  <label>Address </label>
                 
				  <?php $data = array(
									  'name'        => 'address',
									  'id'          => 'address',
									  'value'       => $profile_info['address'],
									  'class'       =>  'form-control'
									  );
									  echo form_textarea($data);
									  ?>             

				   </div>
            </div>
			
            <div class="col-lg-12">
               <button type="submit" class="btn btn-submit me-2">Submit</button>
               <a href="<?php echo base_url().$folder_path;?>company" class="btn btn-cancel">Cancel</a>
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