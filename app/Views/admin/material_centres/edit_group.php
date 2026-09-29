<?php $header = array( 	'title' => 'Modify Material Centre Group' ); ?>
<?php echo view('includes/header',$header); ?>

<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
<?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().$folder_path.'material_centres/modify_group/'.$group_id, $attributes);
       ?>
           <div class=" row">
             <div class="col-6"><h3 class="pb-3">Modify Material Centre Group</h3></div> 
			 <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></div> 
            <div class="col-md-6">
               <div class="col-12"><label>Name</label> <?php $data = array(
								   'name'        => 'group_name',
								   'value'       => $group_info['mat_cent_grp_name'],
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
               <div class="col-12"><label>Alias</label> <?php $data = array(
								   'name'        => 'group_name_alias',
								   'value'       => $group_info['mat_cent_grp_alias'],
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
 
               <div class="col-12"><label>Primary</label>
		<?php if($group_info['crs_mst_is_primary']==1){ ?>			   
			<input type="radio" class="form-check-input" name="primary_group" id="primary_group_y" value="Y" checked> Yes &nbsp;&nbsp;
			<input type="radio" class="form-check-input" name="primary_group" id="primary_group_n" value="N" > No
		<?php } else if($group_info['crs_mst_is_primary']==0){ ?>
			<input type="radio" class="form-check-input" name="primary_group" id="primary_group_y" value="Y"> Yes &nbsp;&nbsp;
			<input type="radio" class="form-check-input" name="primary_group" id="primary_group_n" value="N" checked> No
		<?php }  else { ?>		   
			<input type="radio" class="form-check-input" name="primary_group" id="primary_group_y" value="Y"> Yes &nbsp;&nbsp;
			<input type="radio" class="form-check-input" name="primary_group" id="primary_group_n" value="N" checked> No
		<?php } ?>			   
		</div>
               <div class="col-12"><label>Main</label>
                <?php	                  
				   echo form_dropdown('mat_cent_main',$presdfnd_main ,trim($group_info['under_main_id']),'id="mat_cent_main" class="form-control"  ');
				?>
				</div>
            
             <div class="col-md-12  my-3"><label>&nbsp;</label> 
                 <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                 <a href="<?php echo $base_url.'material_centres/group_list';?>" class="btn btn-secondary">QUIT</a>
              </div>
            
            </div>
            
              </form> </div>

<?php echo view('includes/footer_scripts'); ?>
<script>
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
								<strong>Error!</strong> <ul>${list}</ul>
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