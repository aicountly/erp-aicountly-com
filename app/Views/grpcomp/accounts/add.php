<?php $header = array( 	'title' => 'Add Account' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().'/'.$folder_path.'accounts/add', $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Add A Account</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-6">
                <div class="card p-4 my-2">
                <h5 class="pb-2">General Info</h5>    
                    
               <div class="col-12"><label>Name</label><div class="input-group w-75"><?php $data = array(
									  'name'        => 'account_name',
									  'id'          => 'account_name',
									  'value'       => set_value('account_name'),
									  'maxlength'   => '255',
									  'minlength'   =>  "3",
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
               <div class="col-12"><label>Alias</label><div class="input-group w-75"> <?php $data = array(
									  'name'        => 'account_alias',
									  'id'          => 'account_alias',
									  'value'       => set_value('account_alias'),
									  'minlength'   =>  "3",
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
               <div class="col-12"><label>Print Name</label><div class="input-group w-75"> <?php $data = array(
									  'name'        => 'account_print_name',
									  'id'          => 'account_print_name',
									  'value'       => set_value('account_print_name'),
									  'minlength'   =>  "3",
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
				<div class="col-12"><label>Vendor Code</label> <?php $data = array(
									  'name'        => 'vendor_code',
									  'id'          => 'vendor_code',
									  'value'       => set_value('vendor_code'),
									  'maxlength'   => '20',
									   'class'       => 'form-control',
									  );
									  echo form_input($data);
									  ?></div>
				<div class="col-12">
					<label>Primary</label>
					<div class="input-group w-75">
	                    <div class="input-group-text py-1">
		                    <input type="radio" name="account_primary" value="Y" id="primary_yes" class="form-check-input">
		                    &nbsp;<label for="primary_yes">Yes</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                    <input type="radio" name="account_primary" value="N" id="primary_no" class="form-check-input" checked>
		                	&nbsp;<label for="primary_no">No</label>
	                	</div> 
	                </div>
                </div>

               	<div class="col-12" id="group_div">
               		<label>Group</label>
               	 	<?php echo form_dropdown('account_group', $group_main_dropdown, set_value('account_group'),'id="account_group" class="form-control select2" required="true" '); ?>
               	</div>

               	<div class="col-12" id="parent_div" style="display: none;">
               		<label>Parent Group</label>
               	 	<?php echo form_dropdown('parent_group', $group_primary_dropdown, set_value('parent_group'),'id="parent_group" class="form-control select2" '); ?>
               	</div>

				<div class="col-12"><label>Constitution</label> 
				 <?php	                  
				   echo form_dropdown('account_constitution', $constitution, set_value('account_constitution'),'id="account_constitution" class="form-control select2" ');
						?>
				</div>		
					
				</div>
				  <div class="card p-4 my-2">
				      <h5 class="pb-3">Contact Details</h5>
				 <div class="col-12"><label>Email</label> <?php $data = array(
									  'name'        => 'acc_email',
									  'id'          => 'acc_email',
									  'value'       => set_value('acc_email'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Contact No.</label>
               <div class="row w-75">
                   <p class="col-md-6 ps-0">
                <?php $data = array(
									  'name'        => 'acct_mobile',
									  'id'          => 'acct_mobile',
									  'value'       => set_value('acct_mobile'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control w-100'
									  );
									  echo form_input($data);
									  ?>
									  <small>Mobile No.</small></p>
                <p class="col-md-6 pe-0">
                <?php $data = array(
									  'name'        => 'acct_wa_mobile',
									  'id'          => 'acct_wa_mobile',
									  'value'       => set_value('acct_wa_mobile'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control w-100'
									  );
									  echo form_input($data);
									  ?> <small>Whatsapp No.</small></p>
									  
							</div>
			  </div>
			</div>						  
						
						
			</div>
			
			<div class="col-md-6">
			    <div class="card p-4 my-2">
                  <h5 class="pb-2">Address</h5>     
                     
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
				 <div class="col-12"><label>Pin Code</label> <?php $data = array(
									  'name'        => 'acc_pincode',
									  'id'          => 'acc_pincode',
									  'value'       => set_value('acc_pincode'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>State</label><div class="state_div">
                  <?php echo form_dropdown('state_id', array(''=>'Choose'), '','id="state_id" class="form-control select2" '); ?>
						</div></div>
               <div class="col-12"><label>Country</label> <?php echo form_dropdown('acc_country_id', $CountryDropdown, '1','id="acc_country_id" class="form-control" '); ?></div>
               
               </div>
			    
			</div>
			
			
			
			<div class="col-md-12">
                 <div class="card p-4 my-2">
                   <h5 class="pb-2">GST Details</h5> 
                <div class="row formfields">   
               <div class="col-md-6"><label>GST Type</label> 
			   <?php	                  
				   echo form_dropdown('gsttype', $GstTypes, set_value('gsttype'),'id="gsttype" class="form-control" ');
						?>
			   
			   </div>
               <div class="col-md-6"><label>Tax Category</label>
			 <?php	                  
				   echo form_dropdown('tax_catg', $GSTTaxCategory, set_value('tax_catg'),'id="tax_catg" class="form-control" ');
						?>
						
			  </div>
               <div class="col-md-6"><label>HSN / SAC</label> <input type="text" name="hsn" class="form-control"></div>
               <div class="col-md-6"><label>ITC Eligibility</label> <?php	                  
				   echo form_dropdown('itc_eligibility', $itc_eligibility, set_value('itc_eligibility'),'id="itc_eligibility" class="form-control" ');
						?></div>
               <div class="col-md-6"><label>RCM Nature</label> <?php	                  
				   echo form_dropdown('rcm_nature', $rcm_nature, set_value('rcm_nature'),'id="rcm_nature" class="form-control" ');
						?></div>
			  
              
              
              <div class="col-md-6"><label>GSTIN/UIN</label> <?php $data = array(
									  'name'        => 'acct_gstin',
									  'id'          => 'acct_gstin',
									  'value'       => set_value('acct_gstin'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									 
									  );
									  echo form_input($data);
				 ?></div>
				 
				 <div class="col-md-6"><label>Filing Freq</label> <input type="text" name="filing" class="form-control"></div>
              <div class="col-md-6"><label>Type of Dealer</label> <?php	
                   echo form_dropdown('acc_dealer_type', $DealerTypeDropdown, set_value('acc_dealer_type'),'id="acc_dealer_type" class="form-control"  ');
						?></div>
            </div></div>
            
            
            
               
            
            </div>
            
            <div class="col-md-6">
            <div class="card p-4 my-2">
				      
				      <h5 class="pb-2">Pay Details</h5>		
						
               <div class="col-12"><label>Currency</label>
<?php	                  
				   echo form_dropdown('acc_symbol', $CurrencyDropdown, set_value('acc_symbol'),'id="acc_symbol" class="form-control" ');
						?>
</div>
               <div class="col-12"><label>Op. Bal</label> <div class="input-group w-75"><?php $data = array(
									  'name'        => 'acct_opp_bal',
									  'id'          => 'acct_opp_bal',
									   'value'       => '0.00',
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?>
                    <span class="input-group-text"><input type="radio" name="acct_opp_bal_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_opp_bal_dr_cr" value="dr" class="form-check-input" checked>&nbsp;Dr.</span></div></div>
               <div class="col-12"><label>P.Y. Bal</label> <div class="input-group w-75"><?php $data = array(
									  'name'        => 'acct_prv_bal',
									  'id'          => 'acct_prv_bal',
									  'value'       => '0.00',
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?>
                    <span class="input-group-text"><input type="radio" name="acct_prv_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_prv_dr_cr" value="dr" class="form-check-input" checked>&nbsp;Dr.</span> </div></div>
               </div>
        
                
                  <div class="card p-4 my-3">  
                  <h5 class="pb-2">Other Details</h5>
                  
               <div class="col-12"><label>Station</label> <div class="input-group w-75"><?php $data = array(
									  'name'        => 'acct_station',
									  'id'          => 'acct_station',
									  'value'       => set_value('acct_station'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div></div> 
                  
                  
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
               </div>
               
                 </div>
                 
                 
                 <div class="col-md-6">
         
               <div class="card p-4 my-2">
                   <h5 class="pb-2">Bank Details</h5>
                   <div class="col-12"><label>Bank Name</label> 
                   <?php $data = array(
									  'name'        => 'bank_name',
									  'id'          => 'bank_name',
									  'value'       => set_value('bank_name'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
                   <div class="col-12"><label>Bank Account</label> 
                   <?php $data = array(
									  'name'        => 'bank_ac',
									  'id'          => 'bank_ac',
									  'value'       => set_value('bank_ac'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
				<div class="col-12"><label>IFSC</label> 
                   <?php $data = array(
									  'name'        => 'bank_ifsc',
									  'id'          => 'bank_ifsc',
									  'value'       => set_value('bank_ifsc'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>					  
              <div class="col-12"><label>Branch</label> 
                   <?php $data = array(
									  'name'        => 'bank_branch',
									  'id'          => 'bank_branch',
									  'value'       => set_value('bank_branch'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
			 <div class="col-12"><label>City</label> 
                   <?php $data = array(
									  'name'        => 'bank_city',
									  'id'          => 'bank_city',
									  'value'       => set_value('bank_city'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>						  
              <div class="col-12"><label>State</label> 
                   <?php $data = array(
									  'name'        => 'bank_state',
									  'id'          => 'bank_state',
									  'value'       => set_value('bank_state'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>	
              <div class="col-12"><label>State</label> 
                   <?php $data = array(
									  'name'        => 'bank_state',
									  'id'          => 'bank_state',
									  'value'       => set_value('bank_state'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
              <div class="col-12"><label>Country</label> 
                  <?php	                  
				   echo form_dropdown('bank_country', $CurrencyDropdown, set_value('bank_country'),'id="bank_country" class="form-control" ');
						?>
                  
              </div>
              
              </div></div>

              <div class="col-md-12">
              
               <div class="card p-4 formfields my-2">
                   <h5 class="pb-2">Other Tax Details</h5>
               <div class="row">
               <div class="col-md-6"><label>Aadhar No</label><?php $data = array(
									  'name'        => 'acct_aadhar',
									  'id'          => 'acct_aadhar',
									  'value'       => set_value('acct_aadhar'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-md-6"><label>Pan No</label> <?php $data = array(
									  'name'        => 'acct_pan',
									  'id'          => 'acct_pan',
									  'value'       => set_value('acct_pan'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
              <div class="col-md-6"><label>Profs Tax</label> <?php $data = array(
									  'name'        => 'acct_prof_tax',
									  'id'          => 'acct_prof_tax',
									  'value'       => set_value('acct_prof_tax'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-md-6"><label>Tan No</label> <?php $data = array(
									  'name'        => 'acct_tan',
									  'id'          => 'acct_tan',
									  'value'       => set_value('acct_tan'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-md-6"><label>IT Jurd</label> <?php $data = array(
									  'name'        => 'acc_jurisdiction',
									  'id'          => 'acc_jurisdiction',
									  'value'       => set_value('acc_jurisdiction'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-md-6"><label>FAX</label> <?php $data = array(
									  'name'        => 'acct_fax',
									  'id'          => 'acct_fax',
									  'value'       => set_value('acct_fax'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
              
               <div class="col-md-6"><label>Contact Person</label> <input type="text" name="contactperson" class="form-control"></div>
               
               </div>
               
             </div>
             
             </div>
             
             <div class="col-12 text-center">
                 <input type="submit" value="SAVE" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">
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

$('.modifyInputBtn').click(function(){
	var input = $(this).siblings("input:first").val();
	var code = $(this).data('code');
	input = input.trim();
	if(input != ''){

		if(code == 0){
			input = input.toLowerCase();
			$(this).siblings("input:first").val(input);
			$(this).data('code', 1);
		}
		if(code == 1){
			input = input.toLowerCase().replace(/\b[a-z]/g, function(letter) {
			    return letter.toUpperCase();
			});
			$(this).siblings("input:first").val(input);
			$(this).data('code', 2);
		}
		if(code == 2){
			input = input.toUpperCase();
			$(this).siblings("input:first").val(input);
			$(this).data('code', 0);
		}

	}
});

$(document).on('blur','[name="account_name"]', function(){
    var name = $(this).val().trim();
    if(name){
        if(!$('[name="account_alias"]').val().trim())
        {
           $('[name="account_alias"]').val(name); 
        }
        if(!$('[name="account_print_name"]').val().trim())
        {
           $('[name="account_print_name"]').val(name); 
        }
    }
});

$('input[type=radio][name="account_primary"]').change(function() {
    if (this.value == 'Y'){
        $('#group_div').css('display', 'none');
        $('#account_group').attr('required', false);
        $('#parent_div').css('display', 'block');
        $('#parent_group').val('');
        $('#parent_group').attr('required', true);
    }
    else if(this.value == 'N'){
        $('#parent_div').css('display', 'none');
        $('#parent_group').attr('required', false); 
        $('#group_div').css('display', 'block');
        $('#account_group').val('');
        $('#account_group').attr('required', true);
    }
});

</script>