<?php $header = array('title' => 'Add BOM Group' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  

	<div id="validation_errors"></div>
	
<?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
        echo form_open(base_url().'/'.$folder_path.'billofmaterial/add_group', $attributes);
       ?>
           <div class=" row">
             <div class="col-6"><h3 class="pb-3">Add A BOM Group</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
             <div class="col-md-6">
               <div class="col-12"><label>Name</label> <?php $data = array(
								   'name'        => 'group_name',
								   'value'       => '',
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
               <div class="col-12"><label>Alias</label> <?php $data = array(
								   'name'        => 'group_name_alias',
								   'value'       => '',
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
 
               <div class="col-12"><label>Primary</label> 
			   <input type="radio" class="form-check-input" name="primary_group" id="primary_group_y" value="1"> Yes &nbsp;&nbsp;
               <input type="radio" class="form-check-input" name="primary_group" id="primary_group_n" value="0" checked> No
			 </div>
            
              <div class="col-12" id="primaryyes" style="display:none;"><label>Under</label>
              
				</div>
				
              <div class="col-12" id="primaryno"><label>Under</label>
			 <div class="w-75 d-inline-block"><?php	echo form_dropdown('no_group_under', $user_groups_dropdown, '','id="no_group_under" class="form-control selectwidget required"  ');
						?>	
			  </div> </div>
			  
			  
           
            
            </div>
			
			 
            
               </div>
			<div class=" row">
 <div class="col-md-6">  <div class="col-md-12  my-3"><label>&nbsp;</label> 
                 <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                 <a href="<?php echo $base_url.'billofmaterial/list_group';?>" class="btn btn-secondary">QUIT</a>
              </div>
 </div>
</div>
			
			   </form>

<?php echo view('includes/footer_scripts'); ?>
<script>
 $("input[name='primary_group']").click(function(){
     if($(this).is(':checked')) 
       {
          if($(this).val()=='1'){
              
              $("#no_group_under").removeClass('required');
              $("#primaryyes").hide();
              
              $("#primaryno").hide();}
          else if($(this).val()=='0'){
             $("#primaryno").show()
             $("#primaryyes").hide();
             
             $("#no_group_under").addClass('required');
          }
          else{
            $("#primaryno").hide();
            $("#primaryyes").hide();
            }
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