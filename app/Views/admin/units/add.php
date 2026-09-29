<?php $header = array( 	'title' => 'Add Units' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
<?php  $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
       echo form_open(base_url().$folder_path.'units/add', $attributes);
       ?>
          <div class=" row">
             <div class="col-6"><h3 class="pb-3">Add A Unit</h3></div>  
             <div class="col-6 text-end"><a href="<?php echo $base_url.'units/list';?>" class="btn btn-sm btn-outline-success">« Back</a></div> 
            <div class="col-md-6">
               <div class="col-12"><label>Unit Name <span class="red">*</span></label> <?php $data = array(
									  'name'        => 'item_unit',
									  'id'          => 'item_unit',
									  'value'       => set_value('item_unit'),
									  'maxlength'   => '100',
									  'class'       =>  'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Unit Alias <span class="red">*</span></label> <?php $data = array(
									  'name'        => 'item_unit_alias',
									  'id'          => 'item_unit_alias',
									  'value'       => set_value('item_unit_alias'),
									  'maxlength'   => '100',
									  'class'       =>  'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Print Name <span class="red">*</span></label> <?php $data = array(
                                      'name'        => 'Item_unit_print',
                                      'id'          => 'Item_unit_print',
                                      'value'       => set_value('Item_unit_print'),
                                      'maxlength'   => '50',
									  'class'       => 'form-control',
									  'required'    => true
                                      );
                                      echo form_input($data);
                                      ?></div>
              <div class="col-12"><label>UQC <small>For GST Returns</small></label>
			<?php $data = array(
                                      'name'        => 'Item_unit_uqc',
                                      'id'          => 'Item_unit_uqc',
                                      'value'       => set_value('Item_unit_uqc'),
                                      'maxlength'   => '50',
									  'class'       => 'form-control'									 
                                      );
                                      echo form_input($data);
                                      ?></div>              
              <div class="col-md-12  my-3"><label>&nbsp;</label> 
                 <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                <a href="<?php echo $base_url.'units/list';?>" class="btn btn-secondary mx-2" >QUIT</a>
              </div>            
            </div>            
              </form> 
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