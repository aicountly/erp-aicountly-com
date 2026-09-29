<?php $header = array( 	'title' => 'Add Branch' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
	.ui-datepicker {
      z-index: 9999;
    }
	.ui-datepicker-div{top:0!important;}
</style>	  
<div id="validation_errors"></div>
	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().$folder_path.'branches/add', $attributes);
       ?>
	   <?php echo $message_output->run() ;?> 
<?php
$ro_address = '';
$co_address = '';
if($company_addresses){
	foreach($company_addresses as $comprow){
		if($comprow['cmp_addr_type']=='1'){
			$ro_address = array('comp_addr1'=>$comprow['cmp_addr1'],'comp_addr2'=>$comprow['cmp_addr2'],'comp_city'=>$comprow['cmp_city'],
			                    'comp_state'=>$comprow['cmp_state'],'comp_pin'=>$comprow['cmp_pin_zip'],'comp_country'=>$comprow['cmp_country']
								);
		}
		if($comprow['cmp_addr_type']=='2'){
			$co_address = array('comp_addr1'=>$comprow['cmp_addr1'],'comp_addr2'=>$comprow['cmp_addr2'],'comp_city'=>$comprow['cmp_city'],
			                    'comp_state'=>$comprow['cmp_state'],'comp_pin'=>$comprow['cmp_pin_zip'],'comp_country'=>$comprow['cmp_country']
								);
		}
	}
	
}
$ro_address = json_encode($ro_address);
$co_address = json_encode($co_address);
?>	   

            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Add A Branch</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-4">
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
									  ?><div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
               <div class="col-12"><label>Alias</label><div class="input-group w-75"> <?php $data = array(
									  'name'        => 'account_alias',
									  'id'          => 'account_alias',
									  'value'       => set_value('account_alias'),
									  'minlength'   =>  "3",
									  'maxlength'   => '255',
									  'class'       => 'form-control'
									
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
            

				<div class="col-12"><label>Opening Date</label> 
				<input type="text" id="opening_date" name="opening_date" value="<?php echo date('d-m-Y');?>" class="datepicker form-control form-control-sm">
				</div>	
				<div class="col-12"><label>Closed On</label> 
				<input type="checkbox" id="closed_on" name="closed_on" class="form-control-sm">
				</div>			
				<div class="col-12" id="closing_date_div" style="display:none;"><label>Closing Date</label> 
				<input type="text" id="closing_date" name="closing_date" value="" class="datepicker form-control form-control-sm">
				</div>	
					<div class="col-12" id="group_div">
               		<label>Zone <span class="red">*</span></label>
               	 	<?php echo form_dropdown('account_zone', $zones, set_value('account_zone'),'id="account_zone" class="form-control select2" required="true" '); ?>
               	</div>	
				</div>
				  						  
						
						
			</div>
			
			<div class="col-md-5">
			    <div class="card p-4 my-2">
                  <h5 class="pb-2">Address</h5>     
                   <p class="text-end">pick address from <button type="button" class="btn btn-sm btn-outline-success" id="pick_ro_adrs">Ro. Address</button> Or <button type="button" class="btn btn-sm btn-outline-success" id="pick_co_adrs">Co. Address</button></p>  
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
			
                <div class="col-12"><label>City <span class="red">*</span></label><?php $data = array(
									  'name'        => 'acc_city',
									  'id'          => 'acc_city',
									  'value'       => set_value('city'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									 
									  );
									  echo form_input($data);
				 ?></div>
               <div class="col-12"><label>Pin Code <span class="red">*</span></label> 
			   <?php $data = array(
									  'name'        => 'acc_pincode',
									  'id'          => 'acc_pincode',
									  'value'       => set_value('acc_pincode'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
			
				
                <div class="col-12"><label>State <span class="red">*</span></label><div class="state_div">
                  <?php echo form_dropdown('state_id', array(''=>'Choose'), '','id="state_id" class="form-control select2" '); ?>
						</div></div>
               <div class="col-12"><label>Country <span class="red">*</span></label> <?php echo form_dropdown('acc_country_id', $CountryDropdown, '1','id="acc_country_id" class="form-control" '); ?></div>
							
               
               
               </div>
			    
			</div>
			
			
			<div class="col-md-3">
                <div class="card p-4 my-2">
                <h5 class="pb-2">TAN Info</h5>    
                    
               <div class="col-12"><label>TAN</label><div class="input-group w-75">
			   <?php $data = array(
									  'name'        => 'comp_tan',
									  'id'          => 'comp_tan',
									  'value'       => '',
									  'maxlength'   => '32',
									   'class'      => 'form-control'
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
				
			
				
               <div class="col-12"><label>TAN Jurd</label><div class="input-group w-75">
			   <input type="text" name="tan_jurisd" class="form-control" value="">			
                </div></div>
            
				<div class="col-12"><label>WEF Date</label> 
				<?php $data = array(
									  'name'        => 'tan_wef_date',
									  'id'          => 'tan_wef_date',
									  'value'       => '',
									  'class'      => 'form-control datepicker'
									  );
									  echo form_input($data);
				?>
				</div>
				
				<div class="col-12"><label>Inactive Date</label> 
				<?php $data = array(
									  'name'        => 'tan_inactive_date',
									  'id'          => 'tan_inactive_date',
									  'value'       => '',
									   'class'      => 'form-control datepicker'
									  );
									  echo form_input($data);
				?>
				</div> 
				
			
					
				</div>
				  						  
						
						
			</div>
			<div class="col-md-4">
                <div class="card p-4 my-2">
                <h5 class="pb-2">GST Info  </h5>    
				<div class="col-12">
				<small><strong>Notes:</strong><br><em>Gstin master is being managed financial year wise, any change will be effective for current financial year only.</em></small>
                 </div>
                 	 <div class="col-12"><label>GSTIN Type <span class="red">*</span></label><br>
				 <?php echo form_dropdown('gstintype[]', $gstintypes_list, '1',' id="mdl_gstintype" class="form-control" '); ?>
						</div>  
				<div class="col-12" id="composition_supply_wrapper"><label>Composition Supply</label>
                  <?php echo form_dropdown('mdl_prm_cmp_suply[]', $compositionSupplyOptions, '','id="mdl_prm_cmp_suply" class="form-control"'); ?>
						</div>		
               <div class="col-12"><label>GSTIN <span class="red">*</span></label><div class="input-group w-75">
			   <?php $data = array(
									  'name'        => 'comp_gstin[]',									
									  'value'       => '',
									  'id'          => 'comp_gstin',
									  'maxlength'   => '32',
									   'class'      => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
				
			
						
				
               <div class="col-12"><label>GST Jurd State</label><div class="input-group w-75">
			   <input type="text" name="comp_gst_jurisd_st[]" class="form-control" value="">
			   <div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
            
			
               	<div class="col-12" id="group_div">
               		<label>Jurd Centre</label>
               	 	<input type="text" name="comp_gst_jurisd_ct[]" class="form-control" value="">
               	</div>

				<div class="col-12"><label>IT Jurd</label> 
				<input type="text" name="comp_tan_jurisd[]" class="form-control" value="">
				</div>	
				
				 <div class="col-12"><label>State Code <span class="red">*</span></label>
				 <input type="text" class="form-control" id="statecode_value" value="" required readonly>                  
				</div>
				<div class="col-12"><label>WEF Date</label> 
				<?php $data = array(
									  'name'        => 'gstin_wef_date[]',									
									  'value'       => '',
									  'class'       => 'form-control datepicker',
									  );
									  echo form_input($data);
				?>
				</div>
				
				
				<div class="col-12"><label>Legal Name <span class="red">*</span></label> 
				<?php $data = array(
									  'name'        => 'legal_name[]',									
									  'value'       => '',
									  'class'      => 'form-control',
									  'required'    => true
									 
									  );
									  echo form_input($data);
				?>
				</div>
				
				<div class="col-12"><label>Trade Name <span class="red">*</span></label> 
				<?php $data = array(
									  'name'        => 'trade_name[]',									 
									  'value'       => '',
									   'class'      => 'form-control',
									   'required'    => true
									   
									  );
									  echo form_input($data);
				?>
				</div>
				
			
					
				</div>
				  						  
						
						
			</div>
			
			</div>
			
			
			<div class="col-12 text-center">
                 <input type="submit" value="SAVE" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">
                 <a href="<?php echo base_url();?>admin/branches" class="btn btn-secondary mx-2">QUIT</a>
              </div>
            
            
            </div>  </form> 
<?php echo view('includes/footer_scripts'); ?>
<script>

var json_states=<?php echo $states_json_array;?>;
function getValueByKey(key) {
    if (json_states.hasOwnProperty(key)) {
        return json_states[key]; // Return value if key exists
    } else {
        return "<?php echo $branch_state_code;?>";
    }
}
$(document).on("blur","#comp_gstin",function(){
	var str = $(this).val();
	 var matches = str.match(/\d+/); // Extract numeric part
    if (matches) {
        var firstTwoDigits = matches[0].substring(0, 2); // Get first two digits
        console.log(firstTwoDigits); // Output: 07
		$("#statecode_value").val(getValueByKey(firstTwoDigits));
		console.log(getValueByKey(firstTwoDigits)); // Output: "Maharashtra(27)"
		
    }
});

$(document).on("change","#state_id",function(){
	var statecode = $(this).attr("data-id");
	
});
var ro_address ='<?php echo $ro_address;?>';
var co_address ='<?php echo $co_address;?>';
$("#pick_ro_adrs").on("click",function(){var c=$.parseJSON(ro_address);$("#acc_adrs1").val(c.comp_addr1),$("#acc_adrs2").val(c.comp_addr2),$("#acc_city").val(c.comp_city),$("#acc_pincode").val(c.comp_pin),$("#acc_country_id").val(c.comp_country),load_states(c.comp_country,c.comp_state),$("#state_id").val(c.comp_state)}),$("#pick_co_adrs").on("click",function(){var c=$.parseJSON(co_address);$("#acc_adrs1").val(c.comp_addr1),$("#acc_adrs2").val(c.comp_addr2),$("#acc_city").val(c.comp_city),$("#acc_pincode").val(c.comp_pin),$("#state_id").val(c.comp_state),$("#acc_country_id").val(c.comp_country),load_states(c.comp_country,c.comp_state)});
</script>
<script>
window.ini = load_states('1','0');
$('#closed_on').on('change',function(){
   if(this.checked) 
	  {
	  $("#closing_date").val('');	  
	  $("#closing_date_div").show();
      $("#closing_date").attr("required",true);		
	  }
   else{
	  $("#closing_date_div").hide();
      $("#closing_date").val('');	
	  $("#closing_date").attr("required",false);	
	  }
    });


function load_states(country_id,state_id){
 $(".state_div").html("Loading...");	
 $.get(baseurl+'home/ajax_states_list/'+country_id+'/'+state_id, 
      function (data) {  
         $(".state_div").html(data);		 
     });	
}
$("#acc_country_id").on("change",function(){
var country_id = $(this).val();	
 $(".state_div").html("Loading...");	
 $.get(baseurl+'home/ajax_states_list/'+country_id+'/0', 
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
        
    }
});

function isValidGSTIN(gstin) {
    const gstinPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{3}$/;
    return gstinPattern.test(gstin.toUpperCase());
}

$(document).on('submit', '#myform', function(e){
    
    	var acct_gstin= $("#comp_gstin").val();
    var isvalid = isValidGSTIN(acct_gstin);
		 var error=0;
		if(acct_gstin!=''){
			if (acct_gstin.length !== 15) {
			stop_loader();
			error=1;
			alert_notification("GSTIN NUMBER IS NOT VALID. PLS RECTIFY BEFORE PROCEEDING. ",'Cancel');
			return false;
		}
		if(!isvalid){
			stop_loader();
			error=1;
			alert_notification("GSTIN NUMBER IS NOT VALID. PLS RECTIFY BEFORE PROCEEDING. ",'Cancel');
			return false;
		  }		
		
		
		}
		
		if(error==1)
		return false;
		
		
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
                   window.location.href=response.redirectto;
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
      <strong>Error!</strong>${list}.
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
$(document).ready(function() {

    // --- Function to check the GSTIN type and show/hide the composition field ---
    function toggleCompositionSupply() {
        // Get the selected value from the GSTIN Type dropdown
        var gstinType = $('#mdl_gstintype').val();

        // Find the wrapper div for the composition supply dropdown
        var compositionWrapper = $('#composition_supply_wrapper');

        // Check if the selected value is '2' (for Composition)
        if (gstinType === '2') {
            // If it is, show the composition field with a smooth slide-down animation
            compositionWrapper.slideDown();
        } else {
            // Otherwise, hide the composition field with a slide-up animation
            compositionWrapper.slideUp();
        }
    }

    // --- Run the function once on page load ---
    // This handles the initial state, especially if the form is reloaded with a value already selected
    toggleCompositionSupply();

    // --- Attach the function to the 'change' event of the GSTIN Type dropdown ---
    // This will run the function every time the user selects a different option
    $('#mdl_gstintype').on('change', function() {
        toggleCompositionSupply();
    });

});
</script>