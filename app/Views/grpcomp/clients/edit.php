<?php $header = array( 	'title' => 'Update Clients' ); ?>
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
             echo form_open(base_url().'/'.$folder_path.'clients/edit/'.$client_id, $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
         <div class="row">
            <div class="col-lg-4 col-sm-6 col-12">
               <div class="form-group">
                  <label>Email address </label>
				  <?php $data = array(
									  'name'        => 'email',
									  'id'          => 'email',
									  'value'       => $client_info['email'],
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
                  <label>Company Name </label>
                      <?php $data = array(
									  'name'        => 'buss_org_name',
									  'id'          => 'buss_org_name',
									  'value'       => $client_info['buss_org_name'],
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
									  'value'       =>  $client_info['first_name'],
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
									  'value'       =>  $client_info['last_name'],
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
                  <label>City </label>
                      <?php $data = array(
									  'name'        => 'city_name',
									  'id'          => 'city_name',
									  'value'       =>  $client_info['city_name'],
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
                      <?php
						
									echo form_dropdown('state_id', $StatesDropdown,  $client_info['state_id'],'id="state_id" class="select form-control" required');
									?>
									<div class="invalid-feedback">
				Please select an option.
					</div>
                                </div>
            </div>
			
			 <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Postal code </label>
                     <?php $data = array(
									  'name'        => 'postal_code',
									  'id'          => 'postal_code',
									  'value'       =>  $client_info['postal_code'],
									  'maxlength'   => '8',
									   'class'       =>  'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?>
                                </div>
            </div>
			 <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Phone </label>
                     <?php $data = array(
									  'name'        => 'primary_phone',
									  'id'          => 'primary_phone',
									  'value'       =>  $client_info['primary_phone'],
								      'data-mask'   => '(999) 999-9999',
									  'maxlength'   => '15',
									   'class'       =>  'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?>
					  </div>
            </div>
			<div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Prepay </label>
                     <div class="inline-group">
                                <label class="radio">
                                <?php
								$data = array(
										'name'        => 'prepay',
										'id'          => 'prepay',
										'value'       => 1,
										'checked'     => ($client_info['prepay'] === '1' ? TRUE : FALSE),
									);
									echo form_radio($data);
								?>
                                <i></i>Prepay</label>
                                <label class="radio">
                                <?php
								$data = array(
										'name'        => 'prepay',
										'id'          => 'prepay',
										'value'       => 0,
										'checked'     => ($client_info['prepay'] === '0' ? TRUE : FALSE),
									);
									echo form_radio($data);
								?>
                                <i></i>Terms</label>
                            </div>
					  </div>
            </div>
			<div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Status </label>
                     <div class="inline-group">
                                <label class="radio">
                                <?php
								$data = array(
										'name'        => 'is_active',
										'id'          => 'is_active',
										'value'       => 1,
										'checked'     => ($client_info['is_active'] === '1' ? TRUE : FALSE),
									);
									echo form_radio($data);
								?>
                                <i></i>Active</label>
                                <label class="radio">
                                <?php
								$data = array(
										'name'        => 'is_active',
										'id'          => 'is_active',
										'value'       => 0,
										'checked'     => ($client_info['is_active'] === '0' ? TRUE : FALSE),
									);
									echo form_radio($data);
								?>
                                <i></i>De-active</label>
                            </div>
					  </div>
            </div>
			
			 <div class="col-lg-6 col-sm-6 col-12">
               <div class="form-group">
                  <label>Address </label>
                    <?php $data = array(
									  'name'        => 'address',
									  'id'          => 'address',
									  'value'       =>  $client_info['address'],
									  'rows'          => '3',
									   'class'       =>  'form-control',
									  'required'    => true
									  );
									  echo form_textarea($data);
					?>
					  </div>
            </div>
			 <div class="col-lg-6 col-sm-6 col-12">
               <div class="form-group">
                  <label>Special Notes </label>
                    <?php $data = array(
									  'name'        => 'notes',
									  'id'          => 'notes',
									  'value'       =>  $client_info['notes'],
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
				  
				  <?php
				  $editdata = array();
										foreach($assign_data as $key => $val)
										{	
											$editdata[$val['link_user_id']]= $val['link_user_id'];
											
										}
				    $companytraffic_array= array();
				  foreach($all_company as $key => $value)
				    $companytraffic_array[$key]=$value;
						
									echo form_dropdown('link_user_id[]', $companytraffic_array, $editdata,'id="link_user_id" class="select form-control" multiple');
									?>
									
                   <!--<div style="overflow:auto; width:100%; position:relative;  height:250px; border:1px solid #BDBDBD; padding-left:10px;">
                        <?php
						$assign_data=array();
									 if(isset($assign_data))
									 { 
										$editdata = array();
										foreach($assign_data as $key => $val)
										{	
											$editdata[$val['link_user_id']]= $val['link_user_id'];
											
										}									  
									  foreach($all_company as $key => $value) : if($key!=''): ?>
                                           <label class="checkbox">
                                          <?php
										  if(array_key_exists($key,$editdata))
										  {?>
    										 <input type="checkbox" name="link_user_id[]" id="link_user_id" value="<?php echo $key;?>" checked>&nbsp;<?php echo $value;?>
                                             <?php } else
											 {?>
                                              <input type="checkbox" name="link_user_id[]" id="link_user_id" value="<?php //echo $key;?>">&nbsp;<?php //echo $value;
											 }?>
                                            <i></i> </label>
									 <?php endif; endforeach;
									 }
									 else
									 {
									    foreach($all_company as $key => $value) : if($key!=''): 
										 ?>
                                            <label class="checkbox">
    										 <input type="checkbox" name="link_user_id[]" id="link_user_id" value="<?php //echo $key;?>">&nbsp;<?php //echo $value;?>
                                            <i></i> </label>
									 <?php endif; endforeach; }?> 
                                </div>-->
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