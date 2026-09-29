<?php $header = array( 	'title' => 'Add Account' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
            echo form_open(base_url().'/'.$folder_path.'accounts/add', $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Add A Account</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo $base_url;?>accounts/list" class="badge text-dark">« Back</a></span></div> 
            <div class="col-md-6">
               <div class="col-12"><label>Name</label><?php $data = array(
									  'name'        => 'account_name',
									  'id'          => 'account_name',
									  'value'       => set_value('account_name'),
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Alias</label> <?php $data = array(
									  'name'        => 'account_alias',
									  'id'          => 'account_alias',
									  'value'       => set_value('account_alias'),
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Print Name</label> <?php $data = array(
									  'name'        => 'account_print_name',
									  'id'          => 'print_name',
									  'value'       => set_value('print_name'),
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Group</label> <?php	                  
				   echo form_dropdown('account_group', $group_main_dropdown, set_value('account_group'),'id="account_group" class="form-control" required="true" ');
						?></div>
               <div class="col-12"><label>Currency</label>
<?php	                  
				   echo form_dropdown('acc_symbol', $CurrencyDropdown, set_value('acc_symbol'),'id="acc_symbol" class="form-control" ');
						?>
</div>
               <div class="col-12"><label>Op. Bill</label> <div class="input-group w-75"><?php $data = array(
									  'name'        => 'acct_opp_bal',
									  'id'          => 'acct_opp_bal',
									   'value'       => '0.00',
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?>
                    <span class="input-group-text"><input type="radio" name="acct_opp_bal_dr_cr" value="cr" class="form-check-input"> Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_opp_bal_dr_cr" value="dr" class="form-check-input"> Dr.</span></div></div>
               <div class="col-12"><label>P.Y. Bill</label> <div class="input-group w-75"><?php $data = array(
									  'name'        => 'acct_prv_bal',
									  'id'          => 'acct_prv_bal',
									  'value'       => '0.00',
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?>
                    <span class="input-group-text"><input type="radio" name="acct_prv_dr_cr" value="cr" class="form-check-input"> Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_prv_dr_cr" value="dr" class="form-check-input"> Dr.</span></div></div>
               <div class="col-12"><label>GST Type</label> 
			   <?php	                  
				   echo form_dropdown('gsttype', $GstTypes, set_value('gsttype'),'id="gsttype" class="form-control" ');
						?>
			   
			   </div>
               <div class="col-12"><label>Tax Category</label>
			 <?php	                  
				   echo form_dropdown('tax_catg', $GSTTaxCategory, set_value('tax_catg'),'id="tax_catg" class="form-control" ');
						?>
						
			  </div>
               <div class="col-12"><label>HSN / SAC</label> <input type="text" name="hsn" class="form-control"></div>
               <div class="col-12"><label>ITC Eligibility</label> <?php	                  
				   echo form_dropdown('itc_eligibility', $itc_eligibility, set_value('itc_eligibility'),'id="itc_eligibility" class="form-control" ');
						?></div>
               <div class="col-12"><label>RCM Nature</label> <?php	                  
				   echo form_dropdown('rcm_nature', $rcm_nature, set_value('rcm_nature'),'id="rcm_nature" class="form-control" ');
						?></div>
               <div class="col-12"><label>Email</label> <?php $data = array(
									  'name'        => 'acc_email',
									  'id'          => 'acc_email',
									  'value'       => set_value('acc_email'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Mobile</label> <?php $data = array(
									  'name'        => 'acct_mobile',
									  'id'          => 'acct_mobile',
									  'value'       => set_value('acct_mobile'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Tel</label> <?php $data = array(
									  'name'        => 'acct_tel',
									  'id'          => 'acct_tel',
									  'value'       => set_value('acct_tel'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Fax</label> <?php $data = array(
									  'name'        => 'acct_fax',
									  'id'          => 'acct_fax',
									  'value'       => set_value('acct_fax'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Wa no.</label> <?php $data = array(
									  'name'        => 'acct_wa_mobile',
									  'id'          => 'acct_wa_mobile',
									  'value'       => set_value('acct_wa_mobile'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
			   <div class="col-12"><label>Pin Code</label> <?php $data = array(
									  'name'        => 'acc_pincode',
									  'id'          => 'acc_pincode',
									  'value'       => set_value('acc_pincode'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>						  
               <div class="col-12"><label>Station</label> <div class="input-group w-75"><?php $data = array(
									  'name'        => 'acct_station',
									  'id'          => 'acct_station',
									  'value'       => set_value('acct_station'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div></div>
              
            </div>
            
              <div class="col-md-6">
                <div class="col-12"><label>Transport</label> <?php $data = array(
									  'name'        => 'acct_transport',
									  'id'          => 'acct_transport',
									  'value'       => set_value('acct_transport'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Distance</label> <input type="text" name="distance" class="form-control"></div>  
               <div class="col-12"><label>GSTIN/UIN</label> <?php $data = array(
									  'name'        => 'acct_gstin',
									  'id'          => 'acct_gstin',
									  'value'       => set_value('acct_gstin'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									 
									  );
									  echo form_input($data);
				 ?></div>
               <div class="col-12"><label>Address Line 1</label> <?php $data = array(
									  'name'        => 'acc_adrs1',
									  'id'          => 'acc_adrs1',
									  'value'       => set_value('adrs1'),
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Address Line 2</label> <?php $data = array(
									  'name'        => 'acc_adrs2',
									  'id'          => 'acc_adrs2',
									  'value'       => set_value('adrs2'),
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>City</label> <?php $data = array(
									  'name'        => 'acc_city',
									  'id'          => 'acc_city',
									  'value'       => set_value('city'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									 
									  );
									  echo form_input($data);
				 ?></div>
               <div class="col-12"><label>State</label><div class="state_div">
                  <?php echo form_dropdown('state_id', array(''=>'Choose'), '','id="state_id" class="form-control" '); ?>
						</div></div>
               <div class="col-12"><label>Country</label> <?php echo form_dropdown('acc_country_id', $CountryDropdown, '1','id="acc_country_id" class="form-control" '); ?></div>
               <div class="col-12"><label>Type of Dealer</label> <?php	
                   echo form_dropdown('acc_dealer_type', $DealerTypeDropdown, set_value('acc_dealer_type'),'id="acc_dealer_type" class="form-control"  ');
						?></div>
               <div class="col-12"><label>Filing Freq</label> <input type="text" name="filing" class="form-control"></div>
               <div class="col-12"><label>Aadhar No</label><?php $data = array(
									  'name'        => 'acct_aadhar',
									  'id'          => 'acct_aadhar',
									  'value'       => set_value('acct_aadhar'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Pan No</label> <?php $data = array(
									  'name'        => 'acct_pan',
									  'id'          => 'acct_pan',
									  'value'       => set_value('acct_pan'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Tan No</label> <?php $data = array(
									  'name'        => 'acct_tan',
									  'id'          => 'acct_tan',
									  'value'       => set_value('acct_tan'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>IT Jurd</label> <?php $data = array(
									  'name'        => 'acc_jurisdiction',
									  'id'          => 'acc_jurisdiction',
									  'value'       => set_value('acc_jurisdiction'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Contact Person</label> <input type="text" name="contactperson" class="form-control"></div>
               <div class="col-12"><label>Bank Details</label> <input type="text" name="bank" class="form-control"></div>
               <div class="col-12"><label>IEC</label> <?php $data = array(
									  'name'        => 'acct_iec',
									  'id'          => 'acct_iec',
									  'value'       => set_value('acct_iec'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Profs Tax</label> <?php $data = array(
									  'name'        => 'acct_prof_tax',
									  'id'          => 'acct_prof_tax',
									  'value'       => set_value('acct_prof_tax'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
             </div>
             <div class="col-12 text-center">
                 <input type="submit" value="SAVE" class="btn btn-primary mx-2">
                 <a href="<?php echo base_url().'/'.$folder_path;?>accounts/list" class="btn btn-secondary mx-2">QUIT</a>
              </div>
            
            
            </div>  </form> 
<?php echo view('includes/footer_scripts'); ?>
<script>
window.ini = load_states('1','0');
function load_states(country_id,state_id){
 $(".state_div").html("Loading...");	
 $.get(baseurl+'/home/ajax_states_list/'+country_id+'/'+state_id, 
      function (data) {  
         $(".state_div").html(data);		 
     });	
}
$("#acc_country_id").on("change",function(){
var country_id = $(this).val();	
 $(".state_div").html("Loading...");	
 $.get(baseurl+'/home/ajax_states_list/'+country_id+'/0', 
      function (data) {  
         $(".state_div").html(data);
     });	
})
</script>