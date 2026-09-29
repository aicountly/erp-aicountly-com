<?php $header = array( 	'title' => 'Add Credentials' ); ?>
<?php echo view('includes/header',$header); ?>
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
			  </style>
		<div id="validation_errors"></div>

	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().$folder_path.'my_credentials/add', $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Add Credentials</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-6">
                <div class="card p-4 my-2">
                <h5 class="pb-2">General Info</h5>    
                    
               <div class="col-12"><label>Choose Site</label><div class="input-group w-75">
			   <?php 
			   $cred_site_dropdown= GST_Sites_Types();
			   echo form_dropdown('cred_id', $cred_site_dropdown, set_value('cred_id'),'id="cred_id" class="form-control" required '); ?>
             
                </div></div>
				
				<div class="col-12" id="gst_div" style="display:none;"><label>Choose Type</label><div class="input-group w-75">
			   <?php 
			   $gst_type_dropdown= array(''=>'Choose','1'=>'Tax Payer','2'=>'TCS (E-Commerce)','3'=>'TDS','4'=>'Non Resident');
			   echo form_dropdown('cred_type_gst', $gst_type_dropdown, set_value('cred_type'),'id="cred_id_gst" class="form-control select2" '); ?>
             
                </div></div>
				
				<div class="col-12" id="incometax_div" style="display:none;"><label>Choose Type</label><div class="input-group w-75">
			   <?php 
			   $incometax_type_dropdown= array(''=>'Choose','1'=>'PAN','2'=>'TAN');
			   echo form_dropdown('cred_type_incometax', $incometax_type_dropdown, set_value('cred_type'),'id="cred_id_incometax" class="form-control select2" '); ?>
             
                </div></div>
				<div class="col-12" id="tds_div" style="display:none;"><label>Choose Type</label>
				<div class="input-group w-75">
			   <?php 
			   $tds_type_dropdown= array(''=>'Choose','1'=>'Deductor','2'=>'Taxpayer');
			   echo form_dropdown('cred_type_tds', $tds_type_dropdown, set_value('cred_type'),'id="cred_id_tds" class="form-control select2" '); ?>
             
                </div>
				
				</div>
				
               <div class="col-12"><label>User</label><div class="input-group w-75"> <?php $data = array(
									  'name'        => 'cred_user',
									  'id'          => 'cred_user',
									  'value'       => set_value('cred_user'),
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
									  'value'       => set_value('cred_pass'),
									  'minlength'   =>  "3",
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true,
									   'type'        => 'password'
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-success modifyInputBtn">#</div>
                </div></div>
				
               
				</div>		
			</div>
			<div class="col-md-6" id="gst_apikeys_div" style="display:none;">
                <div class="card p-4 my-2">
                <h5 class="pb-2">API Credentials</h5>    
                    
              
				<div class="col-12 gst_taxpayer_div"  style="display:none;"><label>Client ID</label><div class="input-group w-75">
			   
			   <?php $data = array(
									  'name'        => 'client_id',
									  'id'          => 'client_id',
									  'value'       => set_value('client_id'),
									  'minlength'   =>  "3",
									  'maxlength'   => '255',
									  'class'       => 'form-control',									  
									  );
									  echo form_input($data);
									  ?>
             
                </div></div>
				<div class="col-12 gst_taxpayer_div"  style="display:none;"><label>Secret Key</label><div class="input-group w-75">
			   
			    <?php $data = array(
									  'name'        => 'secret_key',
									  'id'          => 'secret_key',
									  'value'       => set_value('secret_key'),
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
                 <a href="<?php echo base_url().'/'.$folder_path;?>my_credentials" class="btn btn-secondary mx-2">QUIT</a>
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
	else{
	 $(".gst_taxpayer_div").hide();
	 $("#gst_apikeys_div").hide();
	}
	
});

$("#cred_id").on("change",function(){
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
	else if($(this).val()=='4' || $(this).val()=='5'){
	  $("#gst_div").hide();		
		$("#incometax_div").hide();		
		$("#tds_div").hide();
		$('select[name="cred_type_gst"]').attr("required",false);
		$('select[name="cred_type_incometax"]').attr("required",false);
		$('select[name="cred_type_tds"]').attr("required",false);	
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
                    
                		window.location.reload();
                	
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