<?php $header = array( 	'title' => 'Modify Material Centres' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
            echo form_open(base_url().$folder_path.'material_centres/modify_centre/'.$centre_id, $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Modify Material Centre</h3></div> 
			 <div class="col-6 text-end"><a href="<?php echo history_back();?>"  class="btn btn-sm btn-outline-success">« Back</a></div> 
            <div class="col-md-6">
               <div class="col-12"><label>Centre Name <span class="red">*</span></label><?php $data = array(
									  'name'        => 'mat_cent_name',
									  'id'          => 'mat_cent_name',
									  'value'       => $centre_info['mat_cent_name'],
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Alias <span class="red">*</span></label> <?php $data = array(
									  'name'        => 'mat_cent_alias',
									  'id'          => 'mat_cent_alias',
									  'value'       => $centre_info['mat_cent_alias'],
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Print Name <span class="red">*</span></label> <?php $data = array(
									  'name'        => 'mat_cent_print',
									  'id'          => 'mat_cent_print',
									  'value'       => $centre_info['mat_cent_print_name'],
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Matertial Centre Group <span class="red">*</span></label> <?php	                  
				   echo form_dropdown('mat_cent_grp', $material_group, $centre_info['under_crs_mst_id'],'id="mat_cent_grp" class="form-control"  ');
						?></div>
              
               <div class="col-12"><label>Address 1</label> <?php $data = array(
								   'name'        => 'mat_cent_add1',								   
								   'maxlength'   => '255',
								    'value'       => $centre_info['mat_cent_addr1'],
								   'class'       => 'form-control'
								   );
								   echo form_input($data);
								  ?></div>
               <div class="col-12"><label>Address 2</label> <?php $data = array( 
								   'name'        => 'mat_cent_add2',								 
								   'maxlength'   => '255',
								    'value'       => $centre_info['mat_cent_addr2'],
								   'class'       => 'form-control'
								   );
								   echo form_input($data);
								  ?></div>
               <div class="col-12"><label>PIN <span class="red">*</span></label> <?php $data = array(
								   'name'        => 'mat_cent_pin',
								   'maxlength'   => '255',
								    'value'       => $centre_info['mat_cent_pin_zip'],
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?>
				</div>
            </div>            
              <div class="col-md-6">
                <div class="col-12"><label>Country <span class="red">*</span></label> <?php echo form_dropdown('mat_cent_country', $CountryDropdown, $centre_info['mat_cent_country'],'id="mat_cent_country" class="form-control" '); ?></div>
                <div class="col-12"><label>State <span class="red">*</span></label><div class="state_div">
                  <?php echo form_dropdown('mat_cent_state', array(''=>'Choose'), '','id="mat_cent_state" class="form-control" '); ?>
						</div></div>
             <div class="col-12"><label>City</label> <?php $data = array(
									  'name'        => 'mat_cent_city',
									  'id'          => 'mat_cent_city',
									  'value'       => $centre_info['mat_cent_city'],
									  'maxlength'   => '255',
									  'class'      => 'form-control'									 
									  );
									  echo form_input($data);
				 ?></div>
               
             </div>
             <div class="col-12 text-center">
                 <input type="submit" value="SAVE" class="btn btn-primary mx-2" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                 <a href="javascript:void(0);" onclick="window.history.go(-1); return false;"  class="btn btn-secondary mx-2">QUIT</a>
              </div>
            </div>  </form> 
<?php echo view('includes/footer_scripts'); ?>
<script>
function load_states(t,a){$(".state_div").html("Loading..."),$.get(baseurl+"home/ajax_states_list/"+t+"/"+a,(function(t){$(".state_div").html(t),$("#state_id").attr("id","mat_cent_state"),$("#mat_cent_state").attr("name","mat_cent_state")}))}window.ini=load_states('<?php echo $centre_info['mat_cent_country'];?>','<?php echo $centre_info['mat_cent_state'];?>'),$("#acc_country_id").on("change",(function(){var t=$(this).val();$(".state_div").html("Loading..."),$.get(baseurl+"home/ajax_states_list/"+t+"/0",(function(t){$(".state_div").html(t),$("#state_id").attr("id","mat_cent_state"),$("#mat_cent_state").attr("name","mat_cent_state")}))}));
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
							<div class="alert-error-custom">
								<i class="bi bi-x-circle-fill"></i>
								<div>
								<strong>Error!</strong><ul>${list}</ul>
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

</script>