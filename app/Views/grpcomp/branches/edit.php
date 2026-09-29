<?php $header = array( 	'title' => 'Update Branch' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().'/'.$folder_path.'branches/modify/'.$account_id, $attributes);
       ?>
	   <div id="validation_errors"></div>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Update Branch</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-6">
                <div class="card p-4 my-2">
                <h5 class="pb-2">General Info</h5>    
                    
               <div class="col-12"><label>Name</label><div class="input-group w-75"><?php $data = array(
									  'name'        => 'account_name',
									  'id'          => 'account_name',
									  'value'       => $account_info['bo_name'],
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
									  'value'       => $account_info['bo_alias'],
									  'minlength'   =>  "3",
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
            
			
               	<div class="col-12" id="group_div">
               		<label>Group</label>
               	 	<?php echo form_dropdown('account_group', $group_main_dropdown, $account_info['acc_grp_id'],'id="account_group" class="form-control select2" required="true" '); ?>
               	</div>

				<div class="col-12"><label>Opening Date</label> 
				<input type="text" id="opening_date" name="opening_date" value="<?php echo date('d-m-Y',strtotime($account_info['bo_opdate']));?>" class="datepicker form-control form-control-sm">
				</div>		
				<div class="col-12"><label>Closing Date</label> 
				<input type="text" id="closing_date" name="closing_date" value="<?php echo date('d-m-Y',strtotime($account_info['bo_cldate']));?>" class="datepicker form-control form-control-sm">
				</div>	
					<div class="col-12" id="group_div">
               		<label>Zone</label>
					<?php echo form_dropdown('account_zone', $zones, $account_info['bo_zone'],'id="account_zone" class="form-control select2" required="true" '); ?>      	 	
				</div>	
				</div>
						
			</div>
			
			<div class="col-md-6">
			    <div class="card p-4 my-2">
                  <h5 class="pb-2">Address</h5>     
                     
               <div class="col-12"><label>Address Line 1</label> <?php $data = array(
									  'name'        => 'acc_adrs1',
									  'id'          => 'acc_adrs1',
									  'value'       => $account_info['bo_add1'],
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Address Line 2</label> <?php $data = array(
									  'name'        => 'acc_adrs2',
									  'id'          => 'acc_adrs2',
									  'value'       => $account_info['bo_add2'],
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>City</label> <?php $data = array(
									  'name'        => 'acc_city',
									  'id'          => 'acc_city',
									  'value'       => $account_info['bo_city'],
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									 
									  );
									  echo form_input($data);
				 ?></div>
				 <div class="col-12"><label>Pin Code</label> <?php $data = array(
									  'name'        => 'acc_pincode',
									  'id'          => 'acc_pincode',
									  'value'       => $account_info['bo_pin'],
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>State</label><div class="state_div">
                  <?php echo form_dropdown('state_id', array(''=>'Choose'), $account_info['bo_state'],'id="state_id" class="form-control select2" '); ?>
						</div></div>
               <div class="col-12"><label>Country</label> <?php echo form_dropdown('acc_country_id', $CountryDropdown, $account_info['bo_country'],'id="acc_country_id" class="form-control" '); ?></div>
               
               </div>
			    
			</div>
			<div class="col-12 text-center">
                 <input type="submit" value="SAVE" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">
                 <a href="<?php echo base_url().'/'.$folder_path;?>branches" class="btn btn-secondary mx-2">QUIT</a>
              </div>
            
            
            </div>  </form> 
<?php echo view('includes/footer_scripts'); ?>

<script>
window.ini = load_states('<?php echo $account_info['bo_country'];?>','<?php echo $account_info['bo_state'];?>');
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
                    window.location.href='<?php echo base_url().'/'.$folder_path.'branches';?>';
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