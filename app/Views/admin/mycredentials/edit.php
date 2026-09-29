<?php $header = array( 	'title' => 'Modify Credentials' ); ?>
<?php echo view('includes/header',$header); 
    $cred_site_dropdown = GST_Sites_Types();
    $cred_site   = '';
	$cred_type   = '';
	$cred_user   = '';
	$cred_pass   = '';
	$cred_remark = '';
	$cred_clientid ='';
	$cred_secret_key ='';
	$gst_div_display ='style="display:none;"';
	$gst_taxpayer_div_display ='style="display:none;"';
	$cred_clientid   =  '';
	$cred_secret_key =  '';
	
  if($credential_info){
	$cred_site       =  $credential_info['erp_pass_site'] ?? '';
	$cred_type       =  $credential_info['erp_pass_type'] ?? '';
	$cred_user       =  $credential_info['erp_pass_user'] ?? '';
	$cred_pass       =  $credential_info['erp_pass_pwd'] ?? '';
	$cred_clientid   =  $credential_info['erp_client_id'];
	$cred_secret_key =  $credential_info['erp_secret_key'];
	
	if($cred_type==1)
		$gst_taxpayer_div_display='';
	else
		$gst_taxpayer_div_display ='style="display:none;"';
	
	if($cred_site==1){
		$gst_div_display ='';
	}else
		$gst_div_display ='style="display:none;"';
	
	if($cred_site==2){
		$incometax_div_display='';
	}else
		$incometax_div_display ='style="display:none;"';
	
	if($cred_site==3){
		$tds_div_display = '';
	}else
		$tds_div_display = 'style="display:none;"';
}

if($cred_clientid!='')
	$api_credential_display='';
else
	$api_credential_display='style="display:none;"';
?>
<style>
.myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
.myform label{width:25%; float:left;}
.myform .form-control, .myform select {width:75%;}
/*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
 }
</style>
		<div id="validation_errors"></div>

	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().$folder_path.'my_credentials/modify/'.$credid, $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Modify Credentials</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-6">
                <div class="card p-4 my-2">
                <h5 class="pb-2">General Info</h5>    
                    
               <div class="col-12"><label>Choose Site</label><div class="input-group w-75">
			   <?php 
			  
			   echo form_dropdown('cred_id', $cred_site_dropdown, $cred_site,'id="cred_id" class="form-control" required '); ?>
             
                </div></div>
				
				<div class="col-12" id="gst_div" <?php echo $gst_div_display;?>><label>Choose Type</label><div class="input-group w-75">
			   <?php 
			   $gst_type_dropdown= array(''=>'Choose','1'=>'Tax Payer','2'=>'TCS (E-Commerce)','3'=>'TDS','4'=>'Non Resident');
			   echo form_dropdown('cred_type_gst', $gst_type_dropdown, $cred_type,'id="cred_id_gst" class="form-control select2" '); ?>
             
                </div></div>
				
				
				<div class="col-12" id="incometax_div" <?php echo $incometax_div_display;?>><label>Choose Type</label><div class="input-group w-75">
			   <?php 
			   $incometax_type_dropdown= array(''=>'Choose','1'=>'PAN','2'=>'TAN');
			   echo form_dropdown('cred_type_incometax', $incometax_type_dropdown, set_value('cred_type'),'id="cred_id_incometax" class="form-control select2" '); ?>
             
                </div></div>
				<div class="col-12" id="tds_div" <?php echo $tds_div_display;?>><label>Choose Type</label>
				<div class="input-group w-75">
			   <?php 
			   $tds_type_dropdown= array(''=>'Choose','1'=>'Deductor','2'=>'Taxpayer');
			   echo form_dropdown('cred_type_tds', $tds_type_dropdown, set_value('cred_type'),'id="cred_id_tds" class="form-control select2" '); ?>
             
                </div>
				
				</div>
				
               <div class="col-12"><label>User</label><div class="input-group w-75"> <?php $data = array(
									  'name'        => 'cred_user',
									  'id'          => 'cred_user',
									  'value'       => $cred_user,
									  'minlength'   =>  "3",
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-success modifyInputBtn">#</div>
                </div></div>
               <div class="col-12"><label>Password</label><div class="input-group w-75"> <?php $data = array(
									  'name'        => 'cred_pass',
									  'id'          => 'cred_pass',
									  'value'       => '',
									  'minlength'   =>  "3",
									  'maxlength'   => '100',
									  'class'       => 'form-control',
									  'type'        => 'password'
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-success modifyInputBtn">#</div>
                </div></div>
				
				</div>		
			</div>
			<div class="col-md-6" id="gst_apikeys_div" <?php echo $api_credential_display;?>>
                <div class="card p-4 my-2">
                <h5 class="pb-2">API Credentials</h5>    
				<div class="col-12 gst_taxpayer_div"  <?php echo $api_credential_display;?>><label>Client ID</label><div class="input-group w-75">
			    <?php $data = array(
									  'name'        => 'client_id',
									  'id'          => 'client_id',
									  'value'       => $cred_clientid,
									  'minlength'   =>  "3",
									  'maxlength'   => '255',
									  'class'       => 'form-control',									  
									  );
									  echo form_input($data);
									  ?>
             
                </div></div>
				<div class="col-12 gst_taxpayer_div"  <?php echo $api_credential_display;?>><label>Secret Key</label><div class="input-group w-75">
			    <?php $data = array(
									  'name'        => 'secret_key',
									  'id'          => 'secret_key',
									  'value'       => $cred_secret_key,
									  'minlength'   =>  "3",
									  'maxlength'   => '255',
									  'class'       => 'form-control',									  
									  );
									  echo form_input($data);
									  ?>
             
                </div>
				</div>
				
				
				</div>		
			</div>
			</div>
          <div class="col-12 text-center">
                 <input type="submit" value="SAVE" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">
                 <a href="<?php echo base_url().$folder_path;?>my_credentials" class="btn btn-secondary mx-2">QUIT</a>
              </div>
            
						
            
            </div> 






			</form> 
<?php echo view('includes/footer_scripts'); ?>

<script>
$(document).on('change', 'select[name="cred_type_gst"]', function(e){
	$('input[name="client_id"]').attr("required",false);
	$('input[name="secret_key"]').attr("required",false);
	$("#gst_apikeys_div").hide();
	
	if($(this).val()==''){
		$(".gst_taxpayer_div").hide();
	}
	else if($(this).val()=='1'){
		$(".gst_taxpayer_div").show();
		$("#gst_apikeys_div").show();
		$('input[name="client_id"]').attr("required",true);
	    $('input[name="secret_key"]').attr("required",true);
	}
	else
	 $(".gst_taxpayer_div").hide();
	 $("#gst_apikeys_div").hide();
	
});

$("#cred_id").on("change",function(){
	$("#gst_apikeys_div").hide();
	$(".gst_taxpayer_div").hide();
	$('select[name="cred_type_gst"]').val("");
	$('select[name="cred_type_gst"]').attr("required",false);
	$('select[name="cred_type_incometax"]').attr("required",false);
	$('select[name="cred_type_tds"]').attr("required",false);
	if($(this).val()==''){
		$("#gst_div").hide();		
		$("#incometax_div").hide();
		$("#tds_div").hide();
	}
	else if($(this).val()=='1'){
		$("#gst_apikeys_div").show();
		$("#gst_div").show();		
		$("#incometax_div").hide();
		$("#tds_div").hide();
		$('select[name="cred_type_gst"]').attr("required",true);
		$('select[name="cred_type_incometax"]').attr("required",false);
		$('select[name="cred_type_tds"]').attr("required",false);
	}
	else if($(this).val()=='2'){
		$("#gst_div").hide();		
		$("#incometax_div").show();
		$("#tds_div").hide();
		$('select[name="cred_type_incometax"]').attr("required",true);
		$('select[name="cred_type_gst"]').attr("required",false);
		$('select[name="cred_type_tds"]').attr("required",false);
	}
	else if($(this).val()=='3'){
		$("#gst_div").hide();		
		$("#incometax_div").hide();
		$("#tds_div").show();
		$('select[name="cred_type_gst"]').attr("required",false);
		$('select[name="cred_type_incometax"]').attr("required",false);
		$('select[name="cred_type_tds"]').attr("required",true);
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
                    <?php if(isset($_GET['p']) && $_GET['p'] == 1){ ?>
                    	window.history.back();
                	<?php } else { ?>
                		window.location.reload();
                	<?php } ?>
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