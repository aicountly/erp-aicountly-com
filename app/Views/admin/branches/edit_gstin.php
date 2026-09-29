<?php $header = array( 	'title' => 'Update Branch' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>
  
	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().'/'.$folder_path.'branches/modify_gstin/'.$branch_id.'/'.$gstin_id, $attributes);
       ?>
	   <div id="validation_errors"></div>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Update GSTIN</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <?php
	         if (!empty($gsttin_info)) {    
				$gstin_row = $gsttin_info[0];
				$comp_gstin          = $gstin_row['hobo_gstin'];
				$hobo_gstin_sub_type = $gstin_row['hobo_gstin_sub_type'];
				$comp_gstin_type     = $gstin_row['hobo_gstin_type'];
				$comp_gst_jurid_st   = $gstin_row['hobo_gstin_jurisd_st'];
				$comp_gst_jurid_ct   = $gstin_row['hobo_gstin_jurisd_ct'];
				if(isset($gstin_row['hobo_gstin_state_code']))
					$gstin_state_code =  $gstin_row['hobo_gstin_state_code'];
				else
				 $gstin_state_code = '';
			    $gstin_state_code_val='';
			 
				 if(isset($states_lists[$gstin_state_code]))
					 $gstin_state_code_val=$states_lists[$gstin_state_code];
				 if(isset($gstin_row['hobo_gstin_wef_act']) && $gstin_row['hobo_gstin_wef_act']!='0000-00-00'){
				  	$gstin_wefdate =date('d-m-Y',strtotime($gstin_row['hobo_gstin_wef_act']));   
				 }
			   else
				   $gstin_wefdate=''; 
			   
				if(isset($gstin_row['hobo_gstin_inact_date']) && $gstin_row['hobo_gstin_inact_date']!='0000-00-00'){
				 $gstin_inactive_date =date('d-m-Y',strtotime($gstin_row['hobo_gstin_inact_date']));   
				}
			     
			   else
				 $gstin_inactive_date='';
			 
			$hobo_gstin_legal_name =  $gstin_row['hobo_gstin_legal_name'];
			$hobo_gstin_trade_name =  $gstin_row['hobo_gstin_trade_name'];

			
			}else{
				  $comp_gstin          = '';
				  $comp_gstin_type     = 1;
				  $comp_gst_jurid_st   = '';
				  $comp_gst_jurid_ct   = '';
				  $gstin_state_code_val  ='';
				  $gstin_wefdate='';
				  $gstin_inactive_date='';
				  $hobo_gstin_legal_name = '';
				  $hobo_gstin_trade_name='';
				  $hobo_gstin_sub_type='';

				  }
			
				  
				   ?>
			<div class="col-md-4">
                <div class="card p-4 my-2">
                <h5 class="pb-2">GST Info </h5>
				<?php if($comp_gstin_type==2){?>
				<div class="col-12" id="composition_supply_wrapper"><label>Composition Supply</label>
                  <?php echo form_dropdown('mdl_prm_cmp_suply', $compositionSupplyOptions, $hobo_gstin_sub_type,'id="mdl_prm_cmp_suply" class="form-control"'); ?>
				</div>
				<?php } ?>
               <div class="col-12"><label>GSTIN</label><div class="input-group w-75">
			   <?php $data = array(
									  'name'        => 'comp_gstin',
									  'id'          => 'comp_gstin',
									  'value'       => $comp_gstin,
									  'maxlength'   => '32',
									   'class'      => 'form-control',
									    'required'    => true
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
			
               <div class="col-12"><label>GST Jurd State</label><div class="input-group w-75">
			   <input type="text" name="comp_gst_jurisd_st" class="form-control" value="<?php echo $comp_gst_jurid_st;?>">
			   <div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
            
			
               	<div class="col-12" id="group_div">
               		<label>Jurd Centre</label>
               	 	<input type="text" name="comp_gst_jurisd_ct" class="form-control" value="<?php echo $comp_gst_jurid_ct;?>">
               	</div>
				
					<div class="col-12" id="group_div">
               		<label>State Code</label>
               	 	 <input type="text" class="form-control" id="statecode_value" value="<?php echo $gstin_state_code_val;?>" readonly> 
                	</div>
				  
				<div class="col-12"><label>WEF Date</label> 
				<?php 
				
				
				$data = array(
									  'name'        => 'gstin_wef_date',
									  'id'          => 'gstin_wef_date',
									  'value'       => $gstin_wefdate,
									  'class'      => 'form-control datepicker',
									   'required'    => true
									  );
									  echo form_input($data);
				?>
				</div>
				
				 <div class="col-12"><label>Inactive Date</label> 
				<?php
				  
				 
				$data = array(
									  'name'        => 'gstin_inactive_date',
									  'id'          => 'gstin_inactive_date',
									  'value'       => $gstin_inactive_date,
									   'class'      => 'form-control datepicker'
									  );
									  echo form_input($data);
				?>
				</div>	
				<div class="col-12"><label>Legal Name</label> 
				<?php 
				
				$data = array(
									  'name'        => 'legal_name',
									  'id'          => 'legal_name',
									  'value'       => $hobo_gstin_legal_name,
									  'class'      => 'form-control',
									  'required'    => true
									  
									  );
									  echo form_input($data);
				?>
				</div>
				
				<div class="col-12"><label>Trade Name</label> 
				<?php $data = array(
									  'name'        => 'trade_name',
									  'id'          => 'trade_name',
									  'value'       => $hobo_gstin_trade_name,
									   'class'      => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
				?>
				</div>
					
				</div>
						
			</div>
			
			<div class="col-12 text-center">
                 <input type="submit" value="SAVE" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">
                 <a href="<?php echo base_url().'/'.$folder_path;?>branches" class="btn btn-secondary mx-2">QUIT</a>
              </div>
            
            </div>

			</form> 
			
<?php echo view('includes/footer_scripts'); ?>

<script>
function isValidGSTIN(gstin) {
    const gstinPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{3}$/;
    return gstinPattern.test(gstin.toUpperCase());
}

var json_states=<?php echo json_encode($states_lists);?>;
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
        $("#statecode_value").val(getValueByKey(firstTwoDigits));
		
		
    }
});
$("#clone_cas").on("click",function(){
	$("#add_gstin_modal").modal("show");
});

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
        
    }
});

$(document).on('submit', '#myform', function(e){
    
    var acct_gstin= $("#comp_gstin").val();
    	var stid = $('select[name="acc_state_id"] option:selected').data("id");
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
                    alert_success(response.message);
                    window.location.href='<?php echo base_url().'/'.$folder_path.'branches/modify/';?><?php echo $branch_id;?>';
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
	
	
</script>