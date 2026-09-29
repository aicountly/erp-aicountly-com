<?php $header = array( 	'title' => 'Update Account' ); ?>
<?php echo view('includes/header',$header); 

$local_session    = \Config\Services::session();
$fy_begndt        = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end           = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));


$country_id       = 1;
$state_id         = 0;
$city             = '';
$contact_add1     = '';
$contact_add2     = '';
$pincode          = '';
$contact_email    = '';
$contact_mobile   = '';
$contact_wamobile = '';

if (!empty($account_info['address_info']) && is_array($account_info['address_info'])) {
    $addr = $account_info['address_info'];
    $country_id       = $addr['contact_country']  ?? 1;
    $state_id         = $addr['contact_state']    ?? 0;
    $city             = $addr['contact_city']     ?? '';
    $contact_add1     = $addr['contact_add1']     ?? '';
    $contact_add2     = $addr['contact_add2']     ?? '';
    $pincode          = $addr['contact_pin']      ?? '';
    $contact_email    = $addr['contact_email']    ?? '';
    $contact_mobile   = $addr['contact_mobile']   ?? '';
    $contact_wamobile = $addr['contact_wamobile'] ?? '';
}
$acc_is_restrict=0;
$isfreezed=0;
$taxpayer_category_requird='required';
$acc_is_restrict=$account_info['acc_is_restrict'];

if($acc_is_restrict==2 || $acc_is_restrict==3){
	$isfreezed =1;
$taxpayer_category_requird='';
}

?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
    .form-check-label {
  white-space: nowrap;
}
</style>

	<div id="validation_errors"></div>

	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().$folder_path.'accounts/modify/'.$account_id, $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Update Account</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
              
            </div> 
			  <!--  <div class=" row">
			      <div class="col-md-6 order-2 order-md-3">    
					<div class="form-check form-check-inline bbbccCheck bbbccoCheck">
					  <input class="form-check-input" type="checkbox" value="1" id="bbbCheck">
					  <label class="form-check-label" for="bbbCheck">Update Bill By Bill Balance</label>
					</div>
					<div class="form-check form-check-inline bbbccCheck bbbccoCheck">
					  <input class="form-check-input" type="checkbox" value="1" id="sblgrCheck">
					  <label class="form-check-label" for="sblgrCheck">Update Sub Ledger Balance</label>
					</div>
				 </div>
			   </div> -->
            
            
            
            <div class=" row"> 
            <div class="col-md-6">
                <div class="card p-4 my-2">
                <h5 class="pb-2">General Info</h5>    
                    
               <div class="col-12">
               	<label>Name <span class="red">*</span></label>
               	<div class="input-group w-75">
               		<?php 
					if($isfreezed >0){
					$data = array(
					  	'name'        => 'account_name',
					  	'id'          => 'account_name',
					    'value'       => html_entity_decode($account_info['acc_name']),
					  	'maxlength'   => '255',
					  	'minlength'   =>  "3",
					  	'class'       => 'form-control',
					  	'required'    => true,
						'readonly'    => true
				  	);	
					}
					else{
					$data = array(
					  	'name'        => 'account_name',
					  	'id'          => 'account_name',
					    'value'       => html_entity_decode($account_info['acc_name']),
					  	'maxlength'   => '255',
					  	'minlength'   =>  "3",
					  	'class'       => 'form-control',
					  	'required'    => true
				  	);	
					}
					
					  echo form_input($data);
					?>	
               	
               		<div data-code="0" class="btn btn-sm btn-success modifyInputBtn">#</div>
                </div>
				</div>
               <div class="col-12"><label>Alias <span class="red">*</span></label> 
               	<div class="input-group w-75">
               		<?php 
					if($isfreezed >0){
					$data = array(
									  'name'        => 'account_alias',
									  'id'          => 'account_alias',
									  'value'       => html_entity_decode($account_info['acc_alias']),
									  'maxlength'   => '255',
									  'minlength'   =>  "3",
									  'class'       => 'form-control',
									  'required'    => true,
									  'readonly'    => true
									  );
					}
					else{
					$data = array(
									  'name'        => 'account_alias',
									  'id'          => 'account_alias',
									  'value'       => html_entity_decode($account_info['acc_alias']),
									  'maxlength'   => '255',
									  'minlength'   =>  "3",
									  'class'       => 'form-control',
									  'required'    => true,
									
									  );	
					}
									  echo form_input($data);
									  ?>
				<div data-code="0" class="btn btn-sm btn-success modifyInputBtn">#</div>
                </div>
									  </div>
               <div class="col-12"><label>Print Name <span class="red">*</span></label>
               	<div class="input-group w-75">
               	 <?php 
				if($isfreezed >0){ 
				 $data = array(
									  'name'        => 'account_print_name',
									  'id'          => 'print_name',
									  'value'       => html_entity_decode($account_info['acc_print_name']),
									  'maxlength'   => '100',
									  'minlength'   =>  "3",
									  'class'       => 'form-control',
									  'required'    => true,
									  'readonly'    =>true
									  );
				}
				else{
				$data = array(
									  'name'        => 'account_print_name',
									  'id'          => 'print_name',
									  'value'       => html_entity_decode($account_info['acc_print_name']),
									  'maxlength'   => '100',
									  'minlength'   =>  "3",
									  'class'       => 'form-control',
									  'required'    => true
									 
									  );	
				}
									  echo form_input($data);
									  ?>
				<div data-code="0" class="btn btn-sm btn-success modifyInputBtn">#</div>
                </div>
            		</div>

				<div class="col-12">
					<label>Primary <span class="red">*</span></label>
					<div class="input-group w-75">
	                    <div class="input-group-text py-1">
		                    <input type="radio" name="account_primary" value="Y" id="primary_yes" class="form-check-input" <?= $account_info['crs_mst_parent_id'] != 0 ? 'checked' : '' ?>>
		                    &nbsp;<label for="primary_yes">Yes</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                    <input type="radio" name="account_primary" value="N" id="primary_no" class="form-check-input" <?= $account_info['under_crs_mst_id'] != 0 ? 'checked' : '' ?>>
		                	&nbsp;<label for="primary_no">No</label>
	                	</div> 
						
						
	                </div>
                </div>
                
                
               	<div class="col-12" id="group_div" <?php echo ($account_info['under_crs_mst_id'] == 0) ? 'style="display: none;"' : ''; ?>>
               		<label>Group </label>
               		
               		 <input type="hidden" name="sblgr_data" id="sblgr_data">
               		
					<select name="account_group" id="account_group" class="form-control select2" <?php echo (isset($crsmaster_info) && $crsmaster_info["under_crs_mst_id"] != 0 ? "required" : "");?>>
					<option value="">Choose</option>
					<?php foreach($group_main_droplist as $grprow){ ?>
					<option data-id="<?php echo $grprow['acc_grp_parent_id'];?>" value="<?php echo $grprow['acc_grp_id'];?>" <?php echo (isset($crsmaster_info) && $crsmaster_info['under_crs_mst_id']==$grprow['acc_grp_id'])?"selected":"";?>><?php echo $grprow['acc_grp_name'];?></option>
					<?php } ?>
					</select>
               	 	
               	</div>
				<div class="col-12" id="parent_div" <?php echo ($account_info['under_crs_mst_id']!=0) ? 'style="display: none;"':''; ?>>
               		<label>Parent Group</label>
               	 	<?php echo form_dropdown('parent_group', $group_primary_dropdown, ($crsmaster_info!='')?$crsmaster_info['crs_mst_parent_id']:'','id="parent_group" class="form-control select2" '.(isset($crsmaster_info) && $crsmaster_info["crs_mst_parent_id"] != 0 ? "required" : "").''); ?>
               	</div>


						
					
				</div>
				  <div class="card p-4 my-2">
				      <h5 class="pb-3">Contact Details</h5>
				 <div class="col-12"><label>Email</label> <?php $data = array(
									  'name'        => 'acc_email',
									  'id'          => 'acc_email',
									  'value'       => $contact_email,
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
									  'value'       => $contact_mobile,
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
									 'value'        => $contact_wamobile,
									  'maxlength'   => '32',
									   'class'      =>  'form-control w-100'
									  );
									  echo form_input($data);
									  ?> <small>Whatsapp No.</small></p>
									  
							</div>
			  </div>
			</div>						  
						
						
			</div><input type="hidden" name="bbbdata" id="bbbdata">
			
			<div class="col-md-6">
			    <div class="card p-4 my-2" id="addresscontainer">
                  <h5 class="pb-2">Address</h5>     
                     
                  <div class="col-12"><label>Address Line 1</label> <?php
			   	$acc_tax_catg = $account_info['tax_cat_mst_id'] ?? '';
					$data = array(
									  'name'        => 'acc_adrs1',
									  'id'          => 'acc_adrs1',
									  'value'       => $contact_add1,
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Address Line 2</label> <?php $data = array(
									  'name'        => 'acc_adrs2',
									  'id'          => 'acc_adrs2',
									  'value'       =>  $contact_add2,
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>City</label> <?php $data = array(
									  'name'        => 'acc_city',
									  'id'          => 'acc_city',
									  'value'       => $city,
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									 
									  );
									  echo form_input($data);
				 ?></div>
				 <div class="col-12"><label>Pin Code</label> <?php $data = array(
									  'name'        => 'acc_pincode',
									  'id'          => 'acc_pincode',
									  'value'       => $pincode,
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>State</label><div class="state_div">
                  <?php echo form_dropdown('state_id', array(''=>'Choose'), '','id="state_id" class="form-control select2" required'); ?>
						</div></div>
               <div class="col-12"><label>Country</label> <?php echo form_dropdown('acc_country_id', $CountryDropdown, $country_id,'id="acc_country_id" class="form-control" required'); ?></div>
               
               </div>
			    <div class="card p-4 my-2">
				      <h5 class="pb-3">GST Details</h5>
				 <div class="col-12"><label>TaxPayer Category <?php if($taxpayer_category_requird=='required'){?><span class="red">*</span><?php } ?></label> 
				 <?php echo form_dropdown('tax_cat_mst_id', $tax_category, $acc_tax_catg,'id="tax_cat_mst_id" class="form-control" '.$taxpayer_category_requird); ?></div>
				 
				 <div class="col-12"><label>HSN</label> 
				 <input type="text" name="hsn" class="form-control" value="<?php echo $account_info['acc_sac'];?>"></div>
				  
				 <div class="col-12"><label>GSTIN/UIN</label> 
				<input type="text" name="acct_gstin" id="acct_gstin" maxlength="255"  value="<?php echo $account_info['acc_gstin'];?>" class="form-control gstinMask">
				</div>
				 <div class="col-12"><label>SEZ UNIT </label> 
				
				<div class="form-check form-check-inline">
				  <input class="form-check-input" type="radio" name="sezunit" id="sezunityes" value="1" <?php echo ($account_info['acc_is_sez']==1)?'checked':''; ?>>
				  <label class="form-check-label" for="sezunityes">Yes</label>
				</div>
				<div class="form-check form-check-inline">
				  <input class="form-check-input" type="radio" name="sezunit" id="sezunitno" value="0" <?php echo ($account_info['acc_is_sez']==0)?'checked':''; ?>>
				  <label class="form-check-label" for="sezunitno">No</label>
				</div>

				
				
				</div>
              
			</div>
			</div>
			
			
            
            <div class="col-md-6">
            <div class="card p-4 my-2">
				      
				      <h5 class="pb-2">Pay Details</h5>		
				
<?php 
	$disabled_opval=abs($opnbalance);
	$disabled_pyval=abs($pybalance);		 
    if($dis_opng_bal==1){ 
	 $disable_input="true";      
    }
    else{
		$disable_input="false";			 
	  }
	?>
               <div class="col-12"><label>Op. Bal</label>
			   <div class="input-group w-75">
			   
			   <?php 
			   if($dis_opng_bal==1 ){ 
			   $data = array(
									  'name'        => 'acct_opp_bal',
									  'id'          => 'acct_opp_bal',
									   'value'       => $disabled_opval,
									  'maxlength'   => '100',
									  'disabled'   => $disable_input,
									   'class'       => 'form-control'
									  );
									  
			   }else{
				
				$data = array(
									  'name'        => 'acct_opp_bal',
									   'id'          => 'acct_opp_bal',
									   'value'       => $disabled_opval,
									   'maxlength'   => '100',
									   'class'       => 'form-control'
									  );	 
				
				   
			   }
			   
			   echo form_input($data);
									  ?>
                    <span class="input-group-text">
                        <?php if($sel_acc_op_type=='cr'){ ?>
						<?php if($dis_opng_bal==1 ){ ?>
						  <input type="radio" name="acct_opp_bal_dr_cr" value="cr" class="form-check-input" disabled="<?php echo $disable_input;?>" checked>&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_opp_bal_dr_cr" value="dr" class="form-check-input" disabled="<?php echo $disable_input;?>">&nbsp;Dr.
                       
						<?php } else { ?>
						  <input type="radio" name="acct_opp_bal_dr_cr" value="cr" class="form-check-input" checked>&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_opp_bal_dr_cr" value="dr" class="form-check-input">&nbsp;Dr.
                       
						<?php } ?>
                        <?php } elseif($sel_acc_op_type=='dr'){ ?>
						<?php if($dis_opng_bal==1 ){ ?>
						<input type="radio" name="acct_opp_bal_dr_cr" value="cr" class="form-check-input" disabled="<?php echo $disable_input;?>">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_opp_bal_dr_cr" value="dr" class="form-check-input" disabled="<?php echo $disable_input;?>" checked>&nbsp;Dr.
                        
						<?php } else { ?>
						<input type="radio" name="acct_opp_bal_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_opp_bal_dr_cr" value="dr" class="form-check-input" checked>&nbsp;Dr.
                        
						<?php } ?>
						
                        
                        <?php } else{?>
                        <input type="radio" name="acct_opp_bal_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_opp_bal_dr_cr" value="dr" class="form-check-input">&nbsp;Dr.
                          <?php } ?>
                        </span>
					
					<div data-code="0" class="btn btn-sm btn-success modifyOpnBtn">Update Op. Bal</div>	
					
                       </div></div>
               <div class="col-12"><label>P.Y. Bal</label> 
			   <div class="input-group w-75">
			   <?php 
			    if($dis_opng_bal==1 ){ 
			   
			   $data = array(
									  'name'        => 'acct_prv_bal',
									  'id'          => 'acct_prv_bal',
									  'value'       => $disabled_pyval,
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'disabled'   => $disable_input
									  );
				}
				else{
				
				   $data = array(
									  'name'        => 'acct_prv_bal',
									  'id'          => 'acct_prv_bal',
									  'value'       => $disabled_pyval,
									  'maxlength'   => '100',
									   'class'       => 'form-control'									  
									  );		 
				
				
				}
									  echo form_input($data);
									  ?>
				  <span class="input-group-text">
                        <?php if($sel_acc_py_type=='cr'){ ?>
						<?php if($dis_opng_bal==1 ){ ?>
						<input type="radio" name="acct_prv_dr_cr" value="cr" class="form-check-input" disabled="<?php echo $disable_input;?>" checked>&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_prv_dr_cr" value="dr" class="form-check-input" disabled="<?php echo $disable_input;?>"> &nbsp;Dr.
						<?php } else { ?>
						<input type="radio" name="acct_prv_dr_cr" value="cr" class="form-check-input" checked>&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_prv_dr_cr" value="dr" class="form-check-input"> &nbsp;Dr.
						<?php } ?>
                         
                        <?php } elseif($sel_acc_py_type=='dr'){ ?>
						<?php if($dis_opng_bal==1 ){?>
						<input type="radio" name="acct_prv_dr_cr" value="cr" class="form-check-input" disabled="<?php echo $disable_input;?>">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_prv_dr_cr" value="dr" class="form-check-input" disabled="<?php echo $disable_input;?>" checked>&nbsp;Dr.
                        
						<?php } else{?>
						<input type="radio" name="acct_prv_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_prv_dr_cr" value="dr" class="form-check-input" checked>&nbsp;Dr.
                        
						<?php } ?>
						
                        
                        <?php } else{?>
                        <input type="radio" name="acct_prv_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_prv_dr_cr" value="dr" class="form-check-input">&nbsp;Dr.
                          <?php } ?>
                  </span>
				
					<div data-code="0" class="btn btn-sm btn-success modifyOpnBtn">Update P.Y. Bal</div>	
				
                    </div></div>
					
					
				 <div class="col-12"><label>MEMO OP. Bal</label> 
			   <div class="input-group w-75">
			   <?php 
			    $data = array(
									  'name'        => 'memo_opp_bal',
									  'id'          => 'memo_opp_bal',
									  'value'       => abs($sel_memo_bal),
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
				
				 echo form_input($data);
				?>
				       <span class="input-group-text">
                        <?php if($sel_memo_type=='cr'){ ?>						
						<input type="radio" name="memo_opn_dr_cr" value="cr" class="form-check-input" checked>&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="memo_opn_dr_cr" value="dr" class="form-check-input"> &nbsp;Dr.
						 <?php } elseif($sel_memo_type=='dr'){ ?>						
						<input type="radio" name="memo_opn_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="memo_opn_dr_cr" value="dr" class="form-check-input" checked>&nbsp;Dr.
                        <?php } else{?>
                        <input type="radio" name="memo_opn_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="memo_opn_dr_cr" value="dr" class="form-check-input">&nbsp;Dr.
                        <?php } ?>
                      </span>				
					<div data-code="0" class="btn btn-sm btn-success modifyOpnBtn">Update OP. Bal</div>	
				
                    </div></div>	
					
               </div>
        
                
                 
               
                 </div>
                 
                 
                 <div class="col-md-6">
         
               </div>

              <div class="col-md-12">
              
               <div class="card p-4 formfields my-2">
                   <h5 class="pb-2">Other Tax Details</h5>
               <div class="row">
               <div class="col-md-6"><label>Aadhar No</label><?php $data = array(
									  'name'        => 'acct_aadhar',
									  'id'          => 'acct_aadhar',
									  'value'       => $account_info['acc_aadhaar'],
									  'maxlength'   => '255',
									   'class'      => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-md-6"><label>Pan No</label> <?php $data = array(
									  'name'        => 'acct_pan',
									  'id'          => 'acct_pan',
									  'value'       => $account_info['acc_pan'],
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
              
               <div class="col-md-6"><label>Tan No</label> <?php $data = array(
									  'name'        => 'acct_tan',
									  'id'          => 'acct_tan',
									  'value'       => $account_info['acc_tan'],
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-md-6"><label>IT Jurd</label> <?php $data = array(
									  'name'        => 'acc_jurisdiction',
									  'id'          => 'acc_jurisdiction',
									 'value'       => ($account_info['acc_it_jurisd']>0) ? $account_info['acc_it_jurisd']:'',
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
			  </div>
               
              
               </div>
               
             </div>
             
             </div>
             
             <div class="col-12 text-center">
                 <input type="button" value="SAVE" class="btn btn-success mx-2" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                 <a href="<?php echo base_url().$folder_path;?>accounts/list" class="btn btn-secondary mx-2">QUIT</a>
              </div>
            
            
            </div> 


<!-- The Modal -->
<div class="modal" id="billsModal" style="z-index: 9999">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
        <div class="modal-header d-flex justify-content-between align-items-center">
  <!-- Left: Title -->
  <div class="flex-grow-1 text-start">
    <h4 class="modal-title">
      <span id="bills_page">
      </span> Bill by Bill Opening</h4>
  </div>

  <!-- Center: Warning -->
  <div class="flex-grow-1 text-center">
    <div class="text-warning fw-bold" id="bill_warning">
      <!-- Optional: Warning text goes here -->
    </div>
  </div>

  <!-- Right: Close Button -->
  <div class="flex-grow-1 text-end">
    <button type="button" class="btn-close" data-bs-dismiss="modal">
  </div>
</div>

        <!-- Modal body -->
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 text-center">
              <h4>Account: <span id="bills_account">
              </span>
            </h4>
          </div>
          <div class="col-md-6 text-center">
            <h4>Total: <span id="bills_total">
            </span>&nbsp;<span id="bills_drcr">
            </span>
          </h4>
        </div>
      </div>
      <div id="bill_by_bill_grid">
      </div>

    </div>

    <!-- Modal footer -->
    <div class="modal-footer">
      <button type="button" class="btn btn-success" id="save_bill_by_bill">Save</button>
      <button type="button" class="btn btn-danger" id="skip_bill_by_bill">Skip</button> 
    </div>

  </div>
</div>
</div> 
<!-- The Modal -->
<div class="modal" id="sblgrModal" style="z-index: 9999">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header d-flex justify-content-between align-items-center">
  <!-- Left: Title -->
  <div class="flex-grow-1 text-start">
    <h4 class="modal-title">
      <span id="sblgr_page">
      </span> Sub Ledger</h4>
  </div>

  <!-- Center: Warning -->
  <div class="flex-grow-1 text-center">
    <div class="text-warning fw-bold" id="sblgr_warning">
      <!-- Optional: Warning text goes here -->
    </div>
  </div>

  <!-- Right: Close Button -->
  <div class="flex-grow-1 text-end">
    <button type="button" class="btn-close" data-bs-dismiss="modal">
  </div>
</div>
    <!-- Modal body -->
    <div class="modal-body">
      <div class="row">
        <div class="col-md-6 text-center">
          <h4>Account: <span id="sblgr_account">
          </span>
          </h4>
        </div>
        <div class="col-md-6 text-center">
          <h4>Total: <span id="sblgr_total">
          </span>&nbsp;<span id="sblgr_drcr">
        </span>
        </h4>
        </div>
      </div>
      <div id="sblgr_grid">
      </div>
      
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="save_sblgr">Save</button>
     <button type="button" class="btn btn-danger" id="skip_sblgr">Skip</button> 
      </div>
    </div>
  </div>
</div>

			</form> 
<?php echo view('includes/footer_scripts'); ?>
<script>
$(".datepickerT").datepicker();

$('select[name="parent_group"]').change(function() {
	
	if (this.value == '6' || this.value == '7' || this.value == '11' || this.value == '13' || this.value == '8' || this.value == '10' || this.value == '9' || this.value == '12'){
		$("#acct_opp_bal").attr("disabled",true);
		$("#acct_opp_bal").val(0);
		$("#acct_prv_bal").val(0);
		$("#acct_prv_bal").attr("disabled",true);
		$('[name="acct_opp_bal_dr_cr"]').attr("disabled",true);
		$('[name="acct_prv_dr_cr"]').attr("disabled",true);
		
	}
	else{
		$("#acct_opp_bal").attr("disabled",false);		
		$("#acct_prv_bal").attr("disabled",false);
		$('[name="acct_opp_bal_dr_cr"]').attr("disabled",false);
		$('[name="acct_prv_dr_cr"]').attr("disabled",false);
	  }
});

$('select[name="account_group"]').change(function() {
	
	  var chkprnt =  $('select[name="account_group"] option:selected').attr("data-id");	
	 if (chkprnt == '6' || chkprnt == '7' || chkprnt == '11' || chkprnt == '13' || chkprnt == '8' || chkprnt == '10' || chkprnt == '9' || chkprnt == '12'){
		$("#acct_opp_bal").attr("disabled",true);
		$("#acct_opp_bal").val(0);
		$("#acct_prv_bal").val(0);
		$("#acct_prv_bal").attr("disabled",true);
		$('[name="acct_opp_bal_dr_cr"]').attr("disabled",true);
		$('[name="acct_prv_dr_cr"]').attr("disabled",true);
		
	}
	else{
		$("#acct_opp_bal").attr("disabled",false);		
		$("#acct_prv_bal").attr("disabled",false);
		$('[name="acct_opp_bal_dr_cr"]').attr("disabled",false);
		$('[name="acct_prv_dr_cr"]').attr("disabled",false);
	  }
});



function checksum(g){
    let regTest = /\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z\d]{1}[Z]{1}[A-Z\d]{1}/.test(g)
     if(regTest){
        let a=65,b=55,c=36;
        return Array['from'](g).reduce((i,j,k,g)=>{ 
           p=(p=(j.charCodeAt(0)<a?parseInt(j):j.charCodeAt(0)-b)*(k%2+1))>c?1+(p-c):p;
           return k<14?i+p:j==((c=(c-(i%c)))<10?c:String.fromCharCode(c+b));
        },0); 
    }
    return regTest
}
window.ini = load_states('<?php echo $country_id;?>','<?php echo ($state_id)?$state_id:'0';?>');
function load_states(country_id,state_id){
 $(".state_div").html("Loading...");	
 $.get(baseurl+'home/ajax_states_list/'+country_id+'/'+state_id, 
      function (data) {  
         $(".state_div").html(data);	
         $("#state_id").attr("name","acc_state_id");
		 $("#state_id").attr("id","acc_state_id");		 
     });	
}
$("#acc_country_id").on("change",function(){
var country_id = $(this).val();	
 $(".state_div").html("Loading...");	
 $.get(baseurl+'home/ajax_states_list/'+country_id+'/0', 
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
function isValidGSTIN(gstin) {
    const gstinPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{3}$/;
    return gstinPattern.test(gstin.toUpperCase());
}

var saved_bill_txns = <?= json_encode($bbb_data) ?>;
var saved_sblgr_txns = <?= json_encode($sblgr_data) ?>;
  var hasBills = Object.keys(saved_bill_txns).some(function(key) {
  if (key !== "acc_id" && saved_bill_txns[key].bills_txn_list?.length > 0) {
    return true;
  }
  return false;
});
  /* if(hasBills){
      $('#bbbCheck').prop('checked', true);
  } */


	$("#submitbtn").on("click",function(){
		
		var account_primary = $('input[name="account_primary"]:checked').val();
		var account_drcr = $('input[name="acct_opp_bal_dr_cr"]:checked').val();
		
		if(account_primary=='N'){
			var account_group = $("#account_group").val();
			
			
			var account_group_name = $("#account_group").text();
			
			
			let selectedText = $("#account_group option:selected").text().trim().toUpperCase();
			
			if (selectedText === "TRADE PAYABLE" || selectedText === "TRADE RECEIVABLES") {
				if($('#bbbCheck').is(":checked")){ 
                   readyBills();
				}
				else if($('#sblgrCheck').is(":checked")){
					readySblgr();
				}
			    else{
					$("#myform").submit();
					}			
				
			} else {
				$("#myform").submit();
				//alert_notification("Bill By Bill applies to TRADE PAYABLE and TRADE RECEIVABLE accounts.");
				//return false;
			}			
		}else
			$("#myform").submit();
	});
	
	
	/***********  Bill By Bill Opening Balance Start***************/
	
	var op_balance = 0;
    var op_drcr = 'D';
    var bills_ref_list = []; 
    var bbb_data      = [];	

    function readyBills()
    {
        show_loader();
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>accounts/getAccountBills",
            data: {},
            datatype: "json",
            success: function(response){
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    stop_loader();                    
                    op_balance = $("#acct_opp_bal").val();
                    op_drcr = $('input[name="acct_opp_bal_dr_cr"]:checked').val();
                   
				     $.each(response.data, function(index,obj){
						 bills_ref_list[index] = { ref_list: obj };                    
                    });
					 
                    $('#bills_account').text($("#account_name").val());
                    $('#bills_total').html(formatAmount(op_balance));
                    $('#bills_drcr').text(op_drcr);
                    

                    $('#billsModal').modal('show');
                    
                    $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data', generateBillData());
                    $("#bill_by_bill_grid").pqGrid('refreshDataAndView');

                    $('input[type="command-line"]').focus();//tempararily shift focus
                    $("#bill_by_bill_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 0, focus: true });
                }
            }
        });          
    }

    function generateBillData()
    {
        var account_id = '<?php echo $account_id;?>';
        var json = [];	  	       
      if(account_id>0){		
		 var i = saved_bill_txns.findIndex(function(o) {
             return o.acc_id == account_id;
          });
		  if(i >= 0){
		   $.each(saved_bill_txns[i].bills_txn_list, function(index, obj){
                  json.push({'method': 'Adjustment', 'bills_ref_name': obj.bill_ref_name, 'bills_ref_id': obj.bill_ref_id, 'bills_op_bal': obj.bill_txn_amt, 'bill_op_drcr': obj.bill_txn_dr_cr,
                              'bill_due_date': obj.bill_due_date});
              });    
		  }			  
      }  
      
        for(var i=0;i<25;i++){
            json.push({'method': '', 'bills_ref_id': '', 'bills_ref_name': '', 'bills_op_bal': '', 'bill_op_drcr': '', 'bill_due_date': ''});
        }
        return json;
    }

    function calculateBillSummary() {
        var total = 0,
            sub = 0,
            data = this.option('dataModel.data');
        
        data.forEach(function(row){
            
            if(row.bills_op_bal != '' && row.bill_op_drcr != '')
            {
                sub = 0;
                if(row.bill_op_drcr == 'D'){
                    sub = parseAmount(row.bills_op_bal);
                }
                if(row.bill_op_drcr == 'C'){
                    sub = -parseAmount(row.bills_op_bal);
                }
                total  += sub;
            }

        })
      
        var drcr = '';
        if(total > 0){
            drcr = 'D';
        }
        if(total < 0){
            drcr = 'C';
            total = Math.abs(total);
        }
        
        
        var totalData = {
            bills_ref_name : 'Total',
            bills_op_bal : total,
            bill_op_drcr : drcr,
            pq_rowcls : 'grid_footer_color',
            summaryRow: true
        }
        this.option('summaryData', [totalData]);
    }

    function dateEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,                
            grid = this;

        $inp.on("focusout", function (e) {
            var date = rd.due_date;
            
            if(!isValidDate(date)){
              rd.due_date = '';
              e.preventDefault();
            }
        });
        
        $inp.inputmask("99/99/9999", {
            mask: "99-99-9999",
            alias: "date",
            placeholder: "dd-mm-yyyy",
            insertMode: false,
        }).datepicker({
            altFormat: "dd-mm-yyyy",
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            changeYear: true,
            minDate:'<?php echo $fy_begndt;?>',
            maxDate:'<?php echo $fy_end;?>',
            onClose: function () {
                this.focus();
            }

        });
    };

    function referenceEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,
            grid = this;

        if(rd.method == 'Adjustment')
        {
            var data = this.option('dataModel.data');
            var grid_ref_ids = data.map(function(obj) { return obj.bills_ref_id; });

            var modified_ref_list = bills_ref_list.filter(function (el) {
              return !grid_ref_ids.includes(el.bills_ref_id) || rd.bills_ref_id == el.bills_ref_id;
            });
			
			var sourceData = modified_ref_list.map(function(item) {
    return {
        label: item.ref_list.label,
        value: item.ref_list.value,
        full: item.ref_list,
		bills_ref_id:item.ref_list.id,
		bill_due_date:item.ref_list.due_date,
    };
});
         
            $inp.autocomplete({
                source:  sourceData,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    rd.bills_ref_id = ui.item.bills_ref_id;
                    rd.bills_ref_name = ui.item.label;
                    rd.bill_due_date = ui.item.bill_due_date;

                    // var expected = expected_bills_amount(grid);
                    // rd.bills_op_bal = expected.amount;
                    // rd.bill_op_drcr = expected.drcr;
                }

            }).focus(function () {              
                $(this).autocomplete("search", "");
                rd.bills_ref_id = '';
                rd.bills_ref_name = '';
                rd.bill_due_date = '';
                // rd.bills_op_bal = '';
                // rd.bill_op_drcr = '';
            }).focusout(function () {              
                if(rd.bills_ref_name != '' && rd.bills_ref_id == '')
                {
                    var index = modified_ref_list.findIndex(function(obj) {
                       return obj.bills_ref_name.toLowerCase() == rd.bills_ref_name.toLowerCase();
                    });
                    if(index > -1){
                        
                        var downKeyEvent = $.Event("keydown");
                        downKeyEvent.keyCode = $.ui.keyCode.DOWN;  // event for pressing "down" key
                    
                        var enterKeyEvent = $.Event("keydown");
                        enterKeyEvent.keyCode = $.ui.keyCode.ENTER;  // event for pressing "enter" key

                        $inp.val(modified_ref_list.bills_ref_name); 
                        $inp.trigger(downKeyEvent); 
                        $inp.trigger(downKeyEvent);
                        $inp.trigger(enterKeyEvent);
                    }
                    else{
                        rd.bills_ref_id = '';
                        rd.bills_ref_name = '';
                        rd.bill_due_date = '';
                    }
                }
            });

            $inp.on("change", function (evt) {
                grid.refreshDataAndView();
            });

        }
        else
        {
            $inp.on("focusout", function () {
                var reference = $(this).val();
                if(reference)
                {
                    var index = bills_ref_list.findIndex(function(obj) {
                       return obj.bills_ref_name.toLowerCase() == reference.toLowerCase();
                    });
                    if(index > -1){
                        alert('Refernce alredy exists. To use this reference change method to adjustment');
                        rd.bills_ref_id = '';
                        rd.bills_ref_name = '';
                    }
                }

            });
        }
    }

    function expected_bills_amount(grid)
    {
        var amount = 0;
        var data = grid.option('dataModel.data');

        var master_amount = op_balance;
        var master_drcr = op_drcr;

        if(master_drcr == 'D')
            amount = parseAmount(master_amount);
        if(master_drcr == 'C')
            amount = -parseAmount(master_amount);

        data.forEach(function(row){
            if(row.bills_op_bal != '' && row.bill_op_drcr != ''){
                if(row.bill_op_drcr == 'C')
                    amount  += parseAmount(row.bills_op_bal);
                if(row.bill_op_drcr == 'D')
                    amount  -= parseAmount(row.bills_op_bal);
            }
        });

        if(amount < 0){
            return {amount: Math.abs(amount), drcr: 'C'};
        }
        if(amount > 0){
            return {amount: amount, drcr: 'D'};
        }

        return {amount: '', drcr: ''};
    }
    
	function validateBills(callback) {
    var sum = 0;
    bbb_data = [];    
    var data = $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data');
    var error = 0;
    var count = 0;

    for (var i = 0; i < data.length; i++) {
        var method        = data[i]['method'];
        var reference     = data[i]['bills_ref_name'];
        var reference_id  = data[i]['bills_ref_id'];
        var amount        = data[i]['bills_op_bal'];
        var drcr          = data[i]['bill_op_drcr'];
        var due_date      = data[i]['bill_due_date'];

        if(reference !== ''){
            bbb_data.push({
                "method"    : method,
                "reference" : reference,
                "reference_id" : reference_id,
                "amount"    : parseAmount(amount),
                "amountfc"  : "",
                "drcr"      : drcr,
                "due_date"  : due_date,
                "narration" : ""
            });
        }

        if(method !== '' && reference !== '') {
            count++;
            if(reference === '' || drcr === '' || amount === '' || amount <= 0){
                error = 1;
            }
            var sub = (drcr === 'D') ? parseAmount(amount) : -parseAmount(amount);
            sum += sub;
        }
    }

    var account_names_array = bbb_data
        .filter(obj => obj.method === 'New Ref.')
        .map(obj => obj.reference);

    var errcounter = 0;
    if(count === 0){
        $('#bill_warning').text("Kindly fill the details first !!!");
        errcounter++;
    }
    if(error){
        $('#bill_warning').text("Kindly fill the details correctly !!!");
        errcounter++;
    }

    var final_amount = $('input[name="acct_opp_bal"]').val();
    var final_drcr = $('input[name="acct_opp_bal_dr_cr"]:checked').val();
    if(final_drcr === 'cr'){
        final_amount = -final_amount;
    }
    if(sum !== parseAmount(final_amount) && count > 0){
        $('#bill_warning').text("Total Mismatch");
        errcounter++;
    } else {
        $('#bill_warning').text("");
    }

    if(errcounter > 0){
        callback(false);
        return;
    }

    if(count > 0 && account_names_array.length >0){
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>accounts/ValidateAccountBillRefs",
            data: { account_names_array: account_names_array },
            dataType: "json",
            success: function(response) {
                if (!response) {
                    $('#bill_warning').text("Reference detail already exists !!!");
                    callback(false);
                } else {
                    callback(true);
                }
            }
        });
    } else {
        callback(true);
    }
}



$(document).on("click", "#skip_bill_by_bill", function() {
	$('#billsModal').modal('hide');
    $("#bbbdata").val(JSON.stringify(bbb_data));
    $("#myform").submit();
});



$(document).on("click", "#save_bill_by_bill", function() {
    validateBills(function(isValid) {
        if (isValid) {
            $('#billsModal').modal('hide');
            $("#bbbdata").val(JSON.stringify(bbb_data));

            if($('#sblgrCheck').is(":checked")){
                readySblgr();
            } else {
                show_loader();
                $("#myform").submit();
            }
        } else {
            return false;
        }
    });
});
	
    function methodEditor(ui) {
        var $inp = ui.$cell.find("select"),
            di = ui.dataIndx,
            rd = ui.rowData,               
            grid = this;
        
        $inp.on("change", function (evt) {
            var method = $(this).val();
            
            rd.bills_ref_name = '';
            rd.bills_ref_id = '';
            rd.bills_op_bal = '';
            rd.bill_op_drcr = '';
            rd.bill_due_date = '';

            if(method != ''){
                var expected = expected_bills_amount(grid);
                rd.bills_op_bal = expected.amount;
                rd.bill_op_drcr = expected.drcr;
            }
        })
    };

    var drcrlist    = [{"":""},{"C":"C"},{"D":"D"}];
    var methods     = [{"":""},{"New Ref.":"New Ref."},{"Adjustment":"Adjustment"}];


    var bill_dataModel = {"data": []} 
    var bill_colModel = [

        { title: "METHOD", dataIndx: "method", width: 100, cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: methods,
                init: methodEditor
            },
        },
       
        { title: "REFERENCE", width: 100, dataIndx: "bills_ref_name" ,cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
              type: "textbox",
              init: referenceEditor
            },
        },

        { title: "AMOUNT", width: 100,  dataIndx: "bills_op_bal" ,dataType: "float",
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.bills_op_bal != ''){
                    rd.bills_op_bal = parseAmount(rd.bills_op_bal);
                    return formatAmount(rd.bills_op_bal);   
                }
                return '';
            },
        },
        { title: "Dr/Cr", dataIndx: "bill_op_drcr", width: 100, cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: drcrlist
            },
        },
        { title: "DUE DATE", dataIndx: "bill_due_date", width: 100 ,dataType: 'string',
            editor: {
                type: 'textbox',
                init: dateEditor
            },
        },
    ];

    var billsObj = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, //disable maximize,toggle button.
        height: 'flex',
        selectionModel: { type: 'cell' }, 
        scrollModel: { autoFit: true },
        dataModel: bill_dataModel,
        colModel: bill_colModel,  
        pageModel: { type: 'local', rPP: 5 },
        numberCell: { show: true },
        change: calculateBillSummary,
        dataReady: calculateBillSummary,
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
            grid.setSelection({ rowIndx: 0, focus: true });
        }
    };
          
    billsObj.cellKeyDown = function(evt, ui) {
       var rowData = ui.rowData;
       var rowIndx = ui.rowIndx;

       if (evt.keyCode == 46){
            $.each(rowData, function(index,obj){
                rowData[index] = '';
            });
            this.refreshDataAndView();
            return false;
       }
    }      
                

    $("#billsModal").on('shown.bs.modal', function () {   
        if($("#bill_by_bill_grid").pqGrid('instance')){    
            $("#bill_by_bill_grid").pqGrid('refresh');
        }
        else{
            $("#bill_by_bill_grid").pqGrid(billsObj);
        }
    });
	
	/***********  Bill By Bill Opening Balance End***************/
	
	/************  Sub Ledger Opening Balance Start ***********/
	var sblgrIndex = 0;
	var sublgr_ref_list = []; 
    var sblgr_data      = [];
    function readySblgr(){
		$.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>accounts/getAccountSblgrRefs",
            data: {},
            datatype: "json",
            success: function(response){
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    stop_loader();                    
                    op_balance = $("#acct_opp_bal").val();
                    op_drcr    = $('input[name="acct_opp_bal_dr_cr"]:checked').val();                   
				     $.each(response.data, function(index,obj){
						 sublgr_ref_list[index] = { ref_list: obj };                    
                    });					 
                    $('#sblgr_account').text($("#account_name").val());
                    $('#sblgr_total').html(formatAmount(op_balance));
                    $('#sblgr_drcr').text(op_drcr);
                    $('#sblgrModal').modal('show');
                    $("#sblgr_grid").pqGrid('option', 'dataModel.data', generateSblgrData());
                    $("#sblgr_grid").pqGrid('refreshDataAndView');
                    $('input[type="command-line"]').focus();//tempararily shift focus
                    $("#sblgr_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 0, focus: true });
                }
            }
        });     
    }

           
    function generateSblgrData()
    {
	  var account_id = '<?php echo $account_id;?>';
        var json = [];	  	       
      if(account_id>0){		
		 var i = saved_sblgr_txns.findIndex(function(o) {
             return o.acc_id == account_id;
          });
		  if(i >= 0){
		   $.each(saved_sblgr_txns[i].sblgr_txn_list, function(index, obj){
                  json.push({'sblgr_method': 'Adjustment', 'sblgr_reference': obj.bill_ref_name, 'sblgr_reference_id': obj.bill_ref_id, 'sblgr_op_bal': obj.bill_txn_amt, 'sblgr_op_drcr': obj.bill_txn_dr_cr,
                              'sblgr_due_date': obj.bill_due_date});
              });    
		  }			  
      }  
      
        for(var i=0;i<25;i++){
            json.push({'sblgr_method': '', 'sblgr_reference_id': '', 'sblgr_reference': '', 'sblgr_op_bal': '', 'sblgr_op_drcr': '', 'sblgr_due_date': ''});
        }
        return json;
    }
                
    function sblgr_dateEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,                
            grid = this;

        $inp.on("focusout", function (e) {
            var date = rd.sblgr_due_date;
            
        });
        
         $inp.inputmask("99/99/9999", {
            mask: "99-99-9999",
            alias: "date",
            placeholder: "dd-mm-yyyy",
            insertMode: false,
        }).datepicker({
            altFormat: "dd-mm-yyyy",
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            changeYear: true,
            minDate:'<?php //echo $fy_begndt;?>',
            maxDate:'<?php //echo $fy_end;?>',
            onClose: function () {
                this.focus();
            }

        }); 
    };
                
    function sblgr_methodEditor(ui) {
        var $inp = ui.$cell.find("select"),
            di = ui.dataIndx,
            rd = ui.rowData,               
            grid = this;
        
        $inp.on("change", function (evt) {
            var method = $(this).val();
            
            rd.sblgr_reference = '';
            rd.sblgr_reference_id = '';
            rd.sblgr_amount = '';
            rd.sblgr_drcr = '';
            rd.sblgr_due_date = '';
            rd.sblgr_narration = '';

            if(method != ''){
                var expected = expected_sblgr_amount(grid);
                rd.sblgr_amount = expected.sblgr_amount;
                rd.sblgr_drcr = expected.sblgr_drcr;
            }
        })
    };

    function expected_sblgr_amount(grid){
        var amount = 0;
        var data = grid.option('dataModel.data');

        var master_amount = op_balance // 1000
        var master_drcr = op_drcr;

        if(master_drcr == 'D')
            amount = parseAmount(master_amount);
        if(master_drcr == 'C')
            amount = -parseAmount(master_amount);

        data.forEach(function(row){
            if(row.sblgr_amount != '' && row.sblgr_drcr != ''){
                if(row.sblgr_drcr == 'C')
                    amount  += parseAmount(row.sblgr_amount);
                if(row.sblgr_drcr == 'D')
                    amount  -= parseAmount(row.sblgr_amount);
            }
        });

        if(amount < 0){
            return {sblgr_amount: Math.abs(amount), sblgr_drcr: 'C'};
        }
        if(amount > 0){
            return {sblgr_amount: amount, sblgr_drcr: 'D'};
        }

        return {sblgr_amount: '', sblgr_drcr: ''};
    }
                
    function sblgr_referenceEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,
            grid = this;
            
        if(rd.sblgr_method == 'Adjustment')
        {    var data = this.option('dataModel.data');
			var grid_sblgr_ref_ids = data.map(function(obj) { return obj.sblgr_reference_id; });

            var modified_sblgr_ref_list = sublgr_ref_list.filter(function (el) {
              return !grid_sblgr_ref_ids.includes(el.sblgr_reference_id) || rd.sblgr_reference_id == el.sblgr_reference_id;
            });
			
			var sourceData = modified_sblgr_ref_list.map(function(item) {
				return {
					label: item.ref_list.label,
					value: item.ref_list.value,
					full: item.ref_list,
					sblgr_ref_id:item.ref_list.id,
					sblgr_due_date:item.ref_list.due_date,
				};
			});
			
            $inp.autocomplete({
                source:  sourceData,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    rd.sblgr_reference_id = ui.item.id;
                    rd.sblgr_reference = ui.item.label;
                    rd.sblgr_due_date = ui.item.due_date;
                }

            }).focus(function () {              
                $(this).autocomplete("search", "");
                rd.sblgr_reference = '';
                rd.sblgr_reference_id = '';
                rd.sblgr_due_date = '';
            }).focusout(function () {              
                if(rd.sblgr_reference != '' && rd.sblgr_reference_id == '')
                {
                    var index = modified_sblgr_ref_list.findIndex(function(obj) {
                       return obj.sblgr_reference.toLowerCase() == rd.sblgr_reference.toLowerCase();
                    });
                    if(index > -1){
                        
                    }
                    else{
                        rd.sblgr_reference = '';
                        rd.sblgr_reference_id = '';
                        rd.sblgr_due_date = '';
                    }
                }
            });
        }
        if(rd.sblgr_method == 'New Ref.')
        {
            $inp.on("focusout", function () {
                var reference = $(this).val();
                if(reference)
                {
                    var index = modified_sblgr_ref_list.findIndex(function(obj) {
                       return obj.sblgr_reference.toLowerCase() == reference.toLowerCase();
                    });
                    if(index > -1){
                        alert('Refernce alredy exists. To use this reference change method to adjustment');
                        rd.sblgr_reference = '';
                        
                    }
                }

            });
        }

        $inp.on("change", function (evt) {
            grid.refreshDataAndView();
        });
    }
    
	function validateSblgr(callback) {
    var sum = 0;
    sblgr_data = [];    
    var data = $("#sblgr_grid").pqGrid('option', 'dataModel.data');
    var sblgr_error = 0;
    var sblgr_count = 0;
  var validate_name_counter=0;
    for (var i = 0; i < data.length; i++) {
        var method     = data[i]['sblgr_method'];
            var reference  = data[i]['sblgr_reference'];
            var reference_id  = data[i]['sblgr_reference_id'];
            var amount        = data[i]['sblgr_op_bal'];
            var drcr          = data[i]['sblgr_op_drcr'];
            var due_date      = data[i]['sblgr_due_date'];
			
			if(method=='New Ref.'&& reference!='' ){
				validate_name_counter=validate_name_counter+1;
			}
        if(reference !== ''){
            sblgr_data.push({
                "method"    : method,
                "reference" : reference,
                "reference_id" : reference_id,
                "amount"    : parseAmount(amount),
                "drcr"      : drcr,
                "due_date"  : due_date,
                "narration" : ""
            });
        }
		
		console.log(method +"!=='' && "+reference+" !== ''");

        if(method !== '' && reference !== '') {
            sblgr_count++;
            if(reference === '' || drcr === '' || amount === '' || amount <= 0){
                sblgr_error = 1;
            }
            var sub = (drcr === 'D') ? parseAmount(amount) : -parseAmount(amount);
            sum += sub;
        }
    }

    var account_names_array = sblgr_data
        .filter(obj => obj.method === 'New Ref.')
        .map(obj => obj.reference);
	
    var sberrcounter = 0;
    

    var final_amount = $('input[name="acct_opp_bal"]').val();
    var final_drcr = $('input[name="acct_opp_bal_dr_cr"]:checked').val();
    if(final_drcr === 'cr'){
        final_amount = -final_amount;
    }
	
	if(sblgr_count ==0){
        $('#sblgrModal #sblgr_warning').html("Kindly fill the details first !!!");
        sberrcounter++;
    }
    else if(sblgr_error){
        $('#sblgrModal #sblgr_warning').html("Kindly fill the details correctly !!!");
        sberrcounter++;
    }
   else if(sum !== parseAmount(final_amount) && sblgr_count > 0){
        $('#sblgrModal #sblgr_warning').html("Total Mismatch");
        sberrcounter++;
    } else {
        $('#sblgrModal #sblgr_warning').html("");
    }

    if(sberrcounter > 0){
        callback(false);
        return;
    }

    if(validate_name_counter > 0){
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>accounts/ValidateAccountSblgrRefs",
            data: { account_names_array: account_names_array },
            dataType: "json",
            success: function(response) {
                if (!response) {
                    $('#sblgr_warning').text("Reference detail already exists !!!");
                    callback(false);
                } else {
                    callback(true);
                }
            }
        });
    } else {
        callback(true);
    }
}
$(document).on("click", "#skip_sblgr", function() {
	$('#sblgrModal').modal('hide');
    $("#sblgr_data").val(JSON.stringify(sblgr_data));
    $("#myform").submit();
});

	$(document).on("click", "#save_sblgr", function() {
  		 validateSblgr(function(isValid) {
        if (isValid) {
            $('#sblgrModal').modal('hide');
            $("#sblgr_data").val(JSON.stringify(sblgr_data));

            if($('#bbbCheck').is(":checked")){
                readyBills();
            } else {
                show_loader();
                $("#myform").submit();
            }
        } else {
            return false;
        }
    });
		
    });
	
    function calculateSblgrSummary() {
        var total = 0,		   
            sub = 0,			
            data = this.option('dataModel.data');
        
        data.forEach(function(row){
            
            if(row.sblgr_method != '' && row.sblgr_reference != '' && row.sblgr_amount != '' && row.sblgr_drcr != '')
            {
                sub = 0;				
                if(row.sblgr_drcr == 'D'){
                    sub = parseAmount(row.sblgr_op_bal);					
                }
                if(row.sblgr_drcr == 'C'){
                    sub = -parseAmount(row.sblgr_op_bal);					
                }
                total  += sub;
				
            }
        })
        var drcr = 'Dr';
        if(total < 0){
            drcr = 'Cr';
            total = -total;
        }
        var totalData = {
            sblgr_reference : 'Total',
            sblgr_op_bal : total,
            sblgr_op_drcr : drcr,
            pq_rowcls : 'grid_footer_color',
            summaryRow: true
        }
        this.option('summaryData', [totalData]);
    }
               
    var sblgr_dataModel = {"data": []}
    var sblgr_colModel = [
        { title: "METHOD", dataIndx: "sblgr_method", width: 100, cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: methods,
                init: sblgr_methodEditor
            },
        },
        { title: "REFERENCE", width: 100, dataIndx: "sblgr_reference" ,cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
                  type: "textbox",
                  init: sblgr_referenceEditor
            },
            editable: function (ui) {
               var method = ui.rowData['sblgr_method'];
                if (method != '') {
                    return true;
                }
                return false;
            },
        },
		
        { title: "AMOUNT", width: 100,  dataIndx: "sblgr_op_bal" ,dataType: "float",
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.sblgr_op_bal != ''){
                    rd.sblgr_op_bal = parseAmount(rd.sblgr_op_bal);
                    return formatAmount(rd.sblgr_op_bal);   
                }
                return '';
            },
            editable: function (ui) {
               var reference = ui.rowData['sblgr_reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "Dr/Cr", dataIndx: "sblgr_op_drcr", width: 100, cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: drcrlist
            },
            editable: function (ui) {
               var reference = ui.rowData['sblgr_reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "DUE DATE", dataIndx: "sblgr_due_date", width: 100 ,dataType: 'string',
            editor: {
                type: 'textbox',
                init: sblgr_dateEditor
            },
            editable: function (ui) {
               var reference = ui.rowData['sblgr_reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        }
        
    ];

    var sblgrObj = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, 
        height: 'flex',
        selectionModel: { type: 'cell' }, 
        scrollModel: { autoFit: true },
        dataModel: sblgr_dataModel,
        colModel: sblgr_colModel,  
        pageModel: { type: 'local', rPP: 5 },
        numberCell: { show: true },
        change: calculateSblgrSummary,
        dataReady: calculateSblgrSummary,
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
            grid.setSelection({ rowIndx: 0, focus: true });
        }
    };
                
    sblgrObj.cellKeyDown = function(evt, ui) {
       var rowData = ui.rowData;
       var rowIndx = ui.rowIndx;

       if (evt.keyCode == 46){
            $.each(rowData, function(index,obj){
                rowData[index] = '';
            });
            this.refreshDataAndView();
            return false;
       }
    }        

    $("#sblgrModal").on('shown.bs.modal', function () {   
        if($("#sblgr_grid").pqGrid('instance')){     
            $("#sblgr_grid").pqGrid('refresh');
        }
        else
            $("#sblgr_grid").pqGrid(sblgrObj);
    });
	
	
	
	/************  Sub Ledger Opening Balance End ***********/
	
	
	
	$(document).on('submit', '#myform', function(e){
		
		var item_tax= $("#item_tax").val();
		var acct_gstin= $("#acct_gstin").val();
		if(item_tax=='0'){
			var local_tax= $("#local_tax").val();
			if(local_tax==''){
				$("#local_tax").attr("required","required");
				 stop_loader();
			 alert_notification("Other tax category is required!!!");
			 return false;
			}else
				$("#local_tax").removeAttr("required");
		 
		}
		var gstinh = $('select[name="acc_state_id"] option:selected').data("gstinh");
		var stid = $('select[name="acc_state_id"] option:selected').data("id");
		
		 var error=0;
			
		
		
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
					 stop_loader();
                    alert_success(response.message);
                    history.back();
                }
                else{
					stop_loader();
                    alert_notification(response.message);
                    if(response.errors)
                    {
                        var list = ``;
                        if(response.errors.length > 0){
                        	$.each(response.errors, function(index, value){
	                            list += `<li>${value}</li>`;
	                        });

	                        var html = `
							 <div class="alert-error-custom">
								<i class="bi bi-x-circle-fill"></i>
								<div>
								<strong>Error!</strong>  <ul>${list}</ul>
								</div>
								<button type="button" class="btn-close" aria-label="Close"></button>
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
				stop_loader();
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
	
$(function() {
  const targetNames = [
    "INDIRECT EXPENSES",
    "INDIRECT INCOME",
    "DIRECT INCOME",
    "DIRECT EXPENSES",
    "DUTIES & TAXES"
  ];

  function shouldHideAddress() {
    // Get visible text of the selected option from both dropdowns
    const groupText  = ($("#account_group option:selected").text() || "").trim().toUpperCase();
    const parentText = ($("#parent_group option:selected").text() || "").trim().toUpperCase();

    // Check if either selection matches any of the target names
    return targetNames.includes(groupText) || targetNames.includes(parentText);
  }

  function updateAddressVisibility() {
    if (shouldHideAddress()) {
      $("#addresscontainer").hide();
    } else {
      $("#addresscontainer").show();
    }
  }

  // On change of either dropdown (works with Select2 too)
  $("#account_group, #parent_group").on("change", updateAddressVisibility);

  // On submit button click, enforce the rule before submit
  $("#submitbtn").on("click", function (e) {
    updateAddressVisibility();
    // If you need to prevent submit when hidden, add logic here.
    // Example: if(shouldHideAddress()) { e.preventDefault(); }
  });

  // Initial check in case there’s a preselected value
  updateAddressVisibility();
});		
</script>