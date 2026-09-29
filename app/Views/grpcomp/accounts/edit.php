<?php $header = array( 	'title' => 'Update Account' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>

	<div id="validation_errors"></div>

	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().'/'.$folder_path.'accounts/modify/'.$account_id, $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Update Account</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-6">
                <div class="card p-4 my-2">
                <h5 class="pb-2">General Info</h5>    
                    
               <div class="col-12">
               	<label>Name</label>
               	<div class="input-group w-75">
               		<?php $data = array(
					  	'name'        => 'account_name',
					  	'id'          => 'account_name',
					    'value'       => $account_info['acc_name'],
					  	'maxlength'   => '255',
					  	'minlength'   =>  "3",
					  	'class'       => 'form-control',
					  	'required'    => true
				  	);
					  echo form_input($data);
					?>	
               	
               		<div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div>
				</div>
               <div class="col-12"><label>Alias</label> 
               	<div class="input-group w-75">
               		<?php $data = array(
									  'name'        => 'account_alias',
									  'id'          => 'account_alias',
									  'value'       => $account_info['acc_name_alias'],
									  'maxlength'   => '255',
									  'minlength'   =>  "3",
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?>
				<div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div>
									  </div>
               <div class="col-12"><label>Print Name</label>
               	<div class="input-group w-75">
               	 <?php $data = array(
									  'name'        => 'account_print_name',
									  'id'          => 'print_name',
									  'value'       => $account_info['acc_name_print'],
									  'maxlength'   => '100',
									  'minlength'   =>  "3",
									   'class'       => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?>
				<div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div>
            		</div>

				<div class="col-12"><label>Vendor Code</label> <?php $data = array(
									  'name'        => 'vendor_code',
									  'id'          => 'vendor_code',
									  'value'       => $account_info['vendor_code'],
									  'maxlength'   => '20',
									   'class'       => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
				<div class="col-12">
					<label>Primary</label>
					<div class="input-group w-75">
	                    <div class="input-group-text py-1">
		                    <input type="radio" name="account_primary" value="Y" id="primary_yes" class="form-check-input" <?= $account_info['acc_grp_parent_id'] != 0 ? 'checked' : '' ?>>
		                    &nbsp;<label for="primary_yes">Yes</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                    <input type="radio" name="account_primary" value="N" id="primary_no" class="form-check-input" <?= $account_info['acc_grp_id'] != 0 ? 'checked' : '' ?>>
		                	&nbsp;<label for="primary_no">No</label>
	                	</div> 
	                </div>
                </div>

               	<div class="col-12" id="group_div" <?= $account_info['acc_grp_id'] == 0 ? 'style="display: none;"' : '' ?>>
               		<label>Group</label>
               	 	<?php echo form_dropdown('account_group', $group_main_dropdown, $account_info['acc_grp_id'],'id="account_group" class="form-control select2" ($account_info["acc_grp_id"] != 0 ? "required" : "") '); ?>
               	</div>

               	<div class="col-12" id="parent_div" <?= $account_info['acc_grp_parent_id'] == 0 ? 'style="display: none;"' : '' ?>>
               		<label>Parent Group</label>
               	 	<?php echo form_dropdown('parent_group', $group_primary_dropdown, $account_info['acc_grp_parent_id'],'id="parent_group" class="form-control select2" ($account_info["acc_grp_parent_id"] != 0 ? "required" : "") '); ?>
               	</div>


				<div class="col-12"><label>Constitution</label> 
				 <?php	                  
				   echo form_dropdown('account_constitution', $constitution, $account_info['acc_const'],'id="account_constitution" class="form-control select2" ');
						?>
				</div>		
					
				</div>
				  <div class="card p-4 my-2">
				      <h5 class="pb-3">Contact Details</h5>
				 <div class="col-12"><label>Email</label> <?php $data = array(
									  'name'        => 'acc_email',
									  'id'          => 'acc_email',
									  'value'       => $enc_string->nc_string($account_info['acc_email'],'de'),
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
									  'value'       => $enc_string->nc_string($account_info['acc_mobile'],'de'),
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
									 'value'       => $enc_string->nc_string($account_info['acc_wamobile'],'de'),
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
									  'value'       => $enc_string->nc_string($account_info['acc_add1'],'de'),
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Address Line 2</label> <?php $data = array(
									  'name'        => 'acc_adrs2',
									  'id'          => 'acc_adrs2',
									  'value'       => $enc_string->nc_string($account_info['acc_add2'],'de'),
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>City</label> <?php $data = array(
									  'name'        => 'acc_city',
									  'id'          => 'acc_city',
									  'value'       => $enc_string->nc_string($account_info['acc_city'],'de'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									 
									  );
									  echo form_input($data);
				 ?></div>
				 <div class="col-12"><label>Pin Code</label> <?php $data = array(
									  'name'        => 'acc_pincode',
									  'id'          => 'acc_pincode',
									  'value'       => $enc_string->nc_string($account_info['acc_pin'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>State</label><div class="state_div">
                  <?php echo form_dropdown('state_id', array(''=>'Choose'), '','id="state_id" class="form-control select2" '); ?>
						</div></div>
               <div class="col-12"><label>Country</label> <?php echo form_dropdown('acc_country_id', $CountryDropdown, $account_info['acc_country'],'id="acc_country_id" class="form-control" '); ?></div>
               
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
				   echo form_dropdown('tax_catg', $GSTTaxCategory, $account_info['acc_prof_tax'],'id="tax_catg" class="form-control" ');
						?>
						
			  </div>
               <div class="col-md-6"><label>HSN / SAC</label> <input type="text" name="hsn" class="form-control"></div>
               <div class="col-md-6"><label>ITC Eligibility</label> <?php	                  
				   echo form_dropdown('itc_eligibility', $itc_eligibility, $account_info['itc_eligibility'],'id="itc_eligibility" class="form-control" ');
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
									   'value'       => $sel_acc_op_bal,
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?>
                    <span class="input-group-text">
                        <?php if($sel_acc_op_type=='cr'){ ?>
                         <input type="radio" name="acct_opp_bal_dr_cr" value="cr" class="form-check-input" checked>&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_opp_bal_dr_cr" value="dr" class="form-check-input">&nbsp;Dr.
                        <?php } elseif($sel_acc_op_type=='dr'){ ?>
                        <input type="radio" name="acct_opp_bal_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_opp_bal_dr_cr" value="dr" class="form-check-input" checked>&nbsp;Dr.
                        
                        <?php } else{?>
                        <input type="radio" name="acct_opp_bal_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_opp_bal_dr_cr" value="dr" class="form-check-input">&nbsp;Dr.
                          <?php } ?>
                        </span>
                       </div></div>
               <div class="col-12"><label>P.Y. Bal</label> <div class="input-group w-75"><?php $data = array(
									  'name'        => 'acct_prv_bal',
									  'id'          => 'acct_prv_bal',
									  'value'       => $sel_acc_py_bal,
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?>
				  <span class="input-group-text">
                        <?php if($sel_acc_py_type=='cr'){ ?>
                         <input type="radio" name="acct_prv_dr_cr" value="cr" class="form-check-input" checked>&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_prv_dr_cr" value="dr" class="form-check-input">&nbsp;Dr.
                        <?php } elseif($sel_acc_py_type=='dr'){ ?>
                        <input type="radio" name="acct_prv_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_prv_dr_cr" value="dr" class="form-check-input" checked>&nbsp;Dr.
                        
                        <?php } else{?>
                        <input type="radio" name="acct_prv_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_prv_dr_cr" value="dr" class="form-check-input">&nbsp;Dr.
                          <?php } ?>
                  </span>

						
                    </div></div>
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
									  'value'       => $enc_string->nc_string($account_info['acc_aadhar'],'de'),
									  'maxlength'   => '255',
									   'class'      => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-md-6"><label>Pan No</label> <?php $data = array(
									  'name'        => 'acct_pan',
									  'id'          => 'acct_pan',
									  'value'       => $enc_string->nc_string($account_info['acc_pan'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
              <div class="col-md-6"><label>Profs Tax</label> <?php $data = array(
									  'name'        => 'acct_prof_tax',
									  'id'          => 'acct_prof_tax',
									  'value'       => $enc_string->nc_string($account_info['acc_prof_tax'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-md-6"><label>Tan No</label> <?php $data = array(
									  'name'        => 'acct_tan',
									  'id'          => 'acct_tan',
									  'value'       => $enc_string->nc_string($account_info['acc_tan'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-md-6"><label>IT Jurd</label> <?php $data = array(
									  'name'        => 'acc_jurisdiction',
									  'id'          => 'acc_jurisdiction',
									 'value'       => $enc_string->nc_string($account_info['acc_jurisd'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-md-6"><label>FAX</label> <?php $data = array(
									  'name'        => 'acct_fax',
									  'id'          => 'acct_fax',
									  'value'       => $enc_string->nc_string($account_info['acc_fax'],'de'),
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
                 <input type="submit" value="SAVE" class="btn btn-primary mx-2" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                 <a href="<?php echo base_url().'/'.$folder_path;?>accounts/list" class="btn btn-secondary mx-2">QUIT</a>
              </div>
            
            
            </div>  </form> 
<?php echo view('includes/footer_scripts'); ?>
<script>
window.ini = load_states('<?php echo $account_info['acc_country'];?>','<?php echo ($account_info['acc_state'])?$account_info['acc_state']:'0';?>');
function load_states(country_id,state_id){
 $(".state_div").html("Loading...");	
 $.get(baseurl+'/home/ajax_states_list/'+country_id+'/'+state_id, 
      function (data) {  
         $(".state_div").html(data);	
         $("#state_id").attr("name","acc_state_id");
		 $("#state_id").attr("id","acc_state_id");		 
     });	
}
$("#acc_country_id").on("change",function(){
var country_id = $(this).val();	
 $(".state_div").html("Loading...");	
 $.get(baseurl+'/home/ajax_states_list/'+country_id+'/0', 
      function (data) {  
         $(".state_div").html(data);
		 $("#state_id").attr("name","acc_state_id");
		 $("#state_id").attr("id","acc_state_id");
		 
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
                $('#myform').attr('disabled', 'disabled');
                $('#validation_errors').html('');
            },
            success: function (response) {
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if(response.status){
                    alert_success(response.message);
                    window.history.back();
                }
                else{
                    alert_notification(response.message);
                    if(response.errors)
                    {
                        var list = ``;
                        if(response.errors.length > 0){
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
                }
                
            },
            complete: function() {
                stop_loader();
                $('#myform').attr('disabled', false);
            },
            error: function (jqXHR, exception) {
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
                } else {
                    error = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert_notification(error);
            },
        });

    });
</script>