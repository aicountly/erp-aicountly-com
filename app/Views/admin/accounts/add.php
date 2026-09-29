<?php $header = array( 	'title' => 'Add Account' ); ?>
<?php echo view('includes/header',$header); 
$local_session    = \Config\Services::session();
$fy_begndt        = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end           = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
$default_country  = 1;
$default_state    = 31;
?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	 
<style>
			  /*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
}
.form-check-label {
  white-space: nowrap;
}
.pq-grid-cell.pq-side-icon > div:before {        
        width: 20px;
        height: 20px;
        color: #ccc;
        float: right;
    }
    
    .pq-grid-cell.pq-drop-icon > div:before {
        content: "▼";
    }
    
    .pq-grid-cell.pq-calendar > div:before {
        content: "\01F4C5";
    }
    .pq-select-search-input {
        padding: 1px 2px;
        border-width: 0;
        height:23px;
    }
    .pq-select-search-input {
        box-sizing: border-box;
        width: 100%;
        font-size: inherit;
    }
    
    .ui-datepicker-calendar tr, .ui-datepicker-calendar td, .ui-datepicker-calendar td a, .ui-datepicker-calendar th{
        font-size:inherit;
    }
    div.ui-datepicker{
        font-size:13px;
        width:inherit;
        height:inherit;
    }
    .ui-datepicker-title span{
        font-size:13px;
    }
			  </style>
		<div id="validation_errors"></div>

	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().$folder_path.'accounts/add', $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Add A Account</h3></div> 
			 <div class="col-6">
			 <span class="float-end">
			 <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span>
			 </div> 
			 
			  </div> 
			   <div class=" row">
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
			   </div>
			  
			  
			  
			  <div class=" row">
            <div class="col-md-6">
                <div class="card p-4 my-2">
                <h5 class="pb-2">General Info</h5>    
                    
               <div class="col-12"><label>Name <span class="red">*</span></label><div class="input-group w-75"><?php $data = array(
									  'name'        => 'account_name',
									  'id'          => 'account_name',
									  'value'       => set_value('account_name'),
									  'maxlength'   => '255',
									  'minlength'   =>  "3",
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-success modifyInputBtn">#</div>
                </div></div>
               <div class="col-12"><label>Alias <span class="red">*</span></label><div class="input-group w-75"> <?php $data = array(
									  'name'        => 'account_alias',
									  'id'          => 'account_alias',
									  'value'       => set_value('account_alias'),
									  'minlength'   =>  "3",
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-success modifyInputBtn">#</div>
                </div></div>
				 <input type="hidden" name="sblgr_data" id="sblgr_data">
               <div class="col-12"><label>Print Name <span class="red">*</span></label><div class="input-group w-75"> <?php $data = array(
									  'name'        => 'account_print_name',
									  'id'          => 'account_print_name',
									  'value'       => set_value('account_print_name'),
									  'minlength'   =>  "3",
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-success modifyInputBtn">#</div>
                </div></div> <input type="hidden" name="bbbdata" id="bbbdata">
			
				<div class="col-12">
					<label>Primary <span class="red">*</span></label>
					<div class="input-group w-75">
	                    <div class="input-group-text py-1">
		                    <input type="radio" name="account_primary" value="Y" id="primary_yes" class="form-check-input" required checked>
		                    &nbsp;<label for="primary_yes">Yes</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                    <input type="radio" name="account_primary" value="N" id="primary_no" class="form-check-input" required>
		                	&nbsp;<label for="primary_no">No</label>
	                	</div> 
	                </div>
                </div>

               	<div class="col-12" id="group_div"  style="display: none;">
               		<label>Group</label>
					<select name="account_group" id="account_group" class="form-control select2">
					<option value="">Choose</option>
					<?php foreach($group_main_droplist as $grprow){ ?>
					<option data-id="<?php echo $grprow['acc_grp_parent_id'];?>" value="<?php echo $grprow['acc_grp_id'];?>"><?php echo $grprow['acc_grp_name'];?></option>
					<?php } ?>
					</select>
               	</div>

               	<div class="col-12" id="parent_div">
               		<label>Parent Group</label>

               	 	<?php echo form_dropdown('parent_group', $group_primary_dropdown, set_value('parent_group'),'id="parent_group" class="form-control select2" required="true" '); ?>
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
			    <div class="card p-4 my-2" id="addresscontainer">
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
                  <?php echo form_dropdown('state_id', array(''=>'Choose'), '','id="state_id" class="form-control select2" required'); ?>
						</div></div>
               <div class="col-12"><label>Country</label> <?php			   
			   
				  if($bo_address){
					  $default_country = $bo_address['hobo_country'];
					  $default_state = $bo_address['hobo_state'];
				  }
				 
				
				$all_countries=array();
				foreach($CountryDropdown as $key => $value){
					if($key)
					$all_countries[$key]=$value;
				}				
			   echo form_dropdown('acc_country_id', $all_countries, $default_country ?? 1,'id="acc_country_id" class="form-control" required'); ?></div>
               
               </div>
			   <div class="card p-4 my-2">
				      <h5 class="pb-3">GST Details</h5>
				 <div class="col-12"><label>TaxPayer Category <span class="red">*</span></label> 
				 <?php echo form_dropdown('tax_cat_mst_id', $tax_category, '','id="tax_cat_mst_id" class="form-control" required'); ?></div>
				 
			
				 <div class="col-12"><label>HSN</label> 
				 <input type="text" name="hsn" class="form-control"></div>
				  
				 <div class="col-12"><label>GSTIN/UIN</label> 
				<input type="text" name="acct_gstin" value="" id="acct_gstin" maxlength="255" class="form-control gstinMask">
				</div>
				
				 <div class="col-12"><label>SEZ UNIT </label> 
				
				<div class="form-check form-check-inline">
				  <input class="form-check-input" type="radio" name="sezunit" id="sezunityes" value="1">
				  <label class="form-check-label" for="sezunityes">Yes</label>
				</div>
				<div class="form-check form-check-inline">
				  <input class="form-check-input" type="radio" name="sezunit" id="sezunitno" value="0" checked>
				  <label class="form-check-label" for="sezunitno">No</label>
				</div>

				
				
				</div>
              
			</div>
			</div>
			
            
            </div>
            
            <div class="col-md-6">
            <div class="card p-4 my-2">
				      
				      <h5 class="pb-2">Pay Details</h5>		
						
             
               <div class="col-12"><label>OP. Bal</label> <div class="input-group w-75"><?php $data = array(
									  'name'        => 'acct_opp_bal',
									  'id'          => 'acct_opp_bal',
									   'value'       => '0.00',
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'disabled'    => 'disabled'
									  );
									  echo form_input($data);
									  ?>
                    <span class="input-group-text"><input type="radio" name="acct_opp_bal_dr_cr" value="cr" class="form-check-input" disabled="disabled">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_opp_bal_dr_cr" value="dr" class="form-check-input" disabled="disabled" checked>&nbsp;Dr.</span></div>
					</div>
               <div class="col-12"><label>P.Y. Bal</label> <div class="input-group w-75"><?php $data = array(
									  'name'        => 'acct_prv_bal',
									  'id'          => 'acct_prv_bal',
									  'value'       => '0.00',
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'disabled'    => 'disabled'
									  );
									  echo form_input($data);
									  ?>
                    <span class="input-group-text"><input type="radio" name="acct_prv_dr_cr" value="cr" class="form-check-input" disabled="disabled">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="acct_prv_dr_cr" value="dr" class="form-check-input" disabled="disabled" checked>&nbsp;Dr.</span> </div>
					</div>
					<div class="col-12"><label>MEMO OP. Bal</label> <div class="input-group w-75"><?php $data = array(
									  'name'        => 'memo_opp_bal',
									  'id'          => 'memo_opp_bal',
									  'value'       => '0.00',
									  'maxlength'   => '100',
									   'class'       => 'form-control'									   
									  );
									  echo form_input($data);
									  ?>
                    <span class="input-group-text"><input type="radio" name="memo_opn_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="memo_opn_dr_cr" value="dr" class="form-check-input" checked>&nbsp;Dr.</span> </div>
					</div>
               </div>
        
                
                  
                 </div>
                 
                 
                 

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
               
                </div>
               
             </div>
             
             </div>
             
             <div class="col-12 text-center">
                 <input type="button" value="SAVE" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-success mx-2">
                 <a href="<?php echo base_url().'/'.$folder_path;?>accounts/list" class="btn btn-secondary mx-2">QUIT</a>
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
      <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button> 
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
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

			</form> 
<?php echo view('includes/footer_scripts'); ?>

<script>
$(".datepickerT").datepicker();

window.ini = load_states('<?php echo $default_country;?>','<?php echo $default_state;?>');

function load_states(country_id,state_id){
   if(country_id=='')
      var country_id=1;
if(state_id=='')
      var state_id=31;


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

	$('select[name="parent_group"]').change(function () {
		const disabledGroups = ['6', '7', '8', '9', '10', '11', '12', '13'];
		const isDisabled = disabledGroups.includes(this.value);

		$("#acct_opp_bal, #acct_prv_bal").prop("disabled", isDisabled);
		$('[name="acct_opp_bal_dr_cr"], [name="acct_prv_dr_cr"]').prop("disabled", isDisabled);

		if (isDisabled) {
			$("#acct_opp_bal, #acct_prv_bal").val(0);
		}
	});

	$('select[name="account_group"]').change(function () {
		const disabledGroups = ['6', '7', '8', '9', '10', '11', '12', '13'];
		const selectedGroupId = $('select[name="account_group"] option:selected').data("id"); // use .data()

		const isDisabled = disabledGroups.includes(String(selectedGroupId)); // ensure string comparison

		$("#acct_opp_bal, #acct_prv_bal").prop("disabled", isDisabled);
		$('[name="acct_opp_bal_dr_cr"], [name="acct_prv_dr_cr"]').prop("disabled", isDisabled);

		if (isDisabled) {
			$("#acct_opp_bal, #acct_prv_bal").val(0);
		}
	});

	$('input[type=radio][name="account_primary"]').change(function () {
		const isPrimary = this.value === 'Y';

		$('#group_div').toggle(!isPrimary);
		$('#parent_div').toggle(isPrimary);

		$('#account_group').prop('required', !isPrimary).val('');
		$('#parent_group').prop('required', isPrimary).val('');
	});
	
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
				
			}  else {
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
        var json = [];
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

    if(count > 0){
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

    

    function readySblgrModel(step = '')
    {
        if(step == ''){
            sblgrIndex = 0;
        }
        else{
            var status = validateSblgr();
            if(!status){
                return;
            }
        }
        if(step == 'next'){
            if(sblgrIndex < (sblgr_accounts.length - 1)){
                sblgrIndex = sblgrIndex + 1;
            }
        }
        if(step == 'prev'){
            if(sblgrIndex > 0){
                sblgrIndex = sblgrIndex - 1;
            }
        }
        if(sblgr_accounts[sblgrIndex])
        {   var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
	      
            var account_name = sblgr_accounts[sblgrIndex].account_name;
            var amount = sblgr_accounts[sblgrIndex].amount;
			var amountfc = sblgr_accounts[sblgrIndex].amountfc;
            var drcr = sblgr_accounts[sblgrIndex].drcr;
            var page = (sblgrIndex + 1) + '/' + sblgr_accounts.length;
            
            $('#sblgr_account').text(account_name);
           if($("#currency_id").val() >1)
          $('#sblgr_total').html(formatAmount(amountfc,currency_symbol)+ " &nbsp; "+formatAmount(amount));
	      else 
		 $('#sblgr_total').html(formatAmount(amount));
	     
            $('#sblgr_drcr').text(drcr + 'r');
            $('#bills_page').text(page);
            

            $('#sblgrModal').modal('show');
			if($("#currency_id").val() >1){
	            sblgr_colModel[2].hidden=false;
                sblgr_colModel[2].width= 150;
				sblgr_colModel[2].title='AMOUNT('+currency_symbol+')';
			    $("#sblgr_grid").pqGrid( "option", "colModel", sblgr_colModel );             
                $("#sblgr_grid").pqGrid( {autoFit: true} );
                $("#sblgr_grid").pqGrid("refreshCM");
                $("#sblgr_grid").pqGrid("refresh");
				
			  }
			  else{
				sblgr_colModel[2].hidden=true;
				sblgr_colModel[2].width= 150;
				sblgr_colModel[2].title='AMOUNT('+currency_symbol+')'; 				
				$("#sblgr_grid").pqGrid( "option", "colModel", sblgr_colModel );             
				$("#sblgr_grid").pqGrid( {autoFit: true} );
				$("#sblgr_grid").pqGrid("refreshCM");
				$("#sblgr_grid").pqGrid("refresh");
			  }
          
			$("#sblgr_grid").pqGrid('option', 'dataModel.data', sblgr_accounts[sblgrIndex].grid);
			$("#sblgr_grid").pqGrid('refreshDataAndView');            
            $("#sblgr_grid").pqGrid('option', 'dataModel.data', sblgr_accounts[sblgrIndex].grid);
            $("#sblgr_grid").pqGrid('refreshDataAndView');

            $('input[type="command-line"]').focus();//tempararily shift focus
            $("#sblgr_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 0, focus: true });
        }
    }
                
    function generateSblgrData()
    {
        var json = [];        
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

    function expected_sblgr_amount(grid)
    {
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
		var gstinh = $('select[name="acc_state_id"] option:selected').data("gstinh");
		var stid = $('select[name="acc_state_id"] option:selected').data("id");
	
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
							$('#validation_errors').show();
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