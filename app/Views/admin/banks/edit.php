<?php $header = array( 	'title' => 'Update Bank' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().$folder_path.'banks/modify/'.$bank_id, $attributes);
       ?>
	   <?php echo $message_output->run() ;?> 

            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Update A Bank</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-6">
                <div class="card p-4 my-2">
                <h5 class="pb-2">General Info</h5>    
                    
               <div class="col-12"><label>Bank Name <span class="red">*</span></label><div class="input-group w-75"><?php $data = array(
									  'name'        => 'comp_bank_name',
									  'id'          => 'comp_bank_name',
									  'value'       => $bank_info['bank_name'],
									  'maxlength'   => '255',
									  'minlength'   =>  "3",
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?>
                </div></div>
               <div class="col-12"><label>Account No. <span class="red">*</span></label><div class="input-group w-75"> <?php $data = array(
									  'name'        => 'comp_bank_acc_no',
									  'id'          => 'comp_bank_acc_no',
									  'value'       => $bank_info['bank_acc_no'],
									  'minlength'   =>  "3",
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?>
                </div></div>
            <div class="col-12"><label>IFSC Code <span class="red">*</span></label><div class="input-group w-75"> <?php $data = array(
									  'name'        => 'comp_bank_ifsc',
									  'id'          => 'comp_bank_ifsc',
									  'value'       => $bank_info['bank_ifsc'],
									  'minlength'   =>  "3",
									  'maxlength'   => '255',
									  'class'       => 'form-control'									  
									  );
									  echo form_input($data);
									  ?>
                </div></div>
			<div class="col-12"><label>MICR Code</label><div class="input-group w-75"> <?php $data = array(
									  'name'        => 'comp_bank_micr',
									  'id'          => 'comp_bank_micr',
									  'value'       => $bank_info['bank_micr'],
									  'minlength'   =>  "3",
									  'maxlength'   => '255',
									  'class'       => 'form-control'									  
									  );
									  echo form_input($data);
									  ?>
                </div></div>
				  <div class="col-12"><label>Branch <span class="red">*</span></label><div class="input-group w-75">
				<?php $data = array(
									  'name'        => 'comp_bank_branch',
									  'id'          => 'comp_bank_branch',
									  'value'       => $bank_info['bank_branch'],
									  'minlength'   =>  "3",
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'   => true									  
									  );
									  echo form_input($data);
									  ?>
                </div></div>
			<div class="col-12"><label>Bank Type <span class="red">*</span></label><div class="input-group w-75">
				<?php $data = array(
									  'name'        => 'comp_bank_type',
									  'id'          => 'comp_bank_type',
									  'value'       => $bank_info['bank_acc_type'],
									  'minlength'   =>  "2",
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'   => true									  
									  );
									  echo form_input($data);
									  ?>
                </div></div> 
			 		
				</div>
				  						  
						
						
			</div>
			
			<div class="col-md-6">
			    <div class="card p-4 my-2">
                  <h5 class="pb-2">Address</h5>     
               <div class="col-12"><label>Address</label> <?php $data = array(
									  'name'        => 'comp_bank_adrs',
									  'id'          => 'comp_bank_adrs',
									  'value'       => $bank_info['bank_addr'],
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>City <span class="red">*</span></label> <?php $data = array(
									  'name'        => 'comp_bank_city',
									  'id'          => 'comp_bank_city',
									  'value'       => $bank_info['bank_city'],
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									 
									  );
									  echo form_input($data);
				 ?></div>
				 <div class="col-12"><label>Pin Code <span class="red">*</span></label> <?php $data = array(
									  'name'        => 'comp_bank_pin_code',
									  'id'          => 'comp_bank_pin_code',
									  'value'       => $bank_info['bank_pin_code'],
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>State <span class="red">*</span></label><div class="state_div">
                  <?php echo form_dropdown('comp_bank_state', array(''=>'Choose'), '','id="comp_bank_state" class="form-control select2" '); ?>
						</div></div>
               <div class="col-12"><label>Country <span class="red">*</span></label> <?php echo form_dropdown('comp_bank_country', $CountryDropdown, $bank_info['bank_country'],'id="comp_bank_country" class="form-control" '); ?></div>
               
               </div>			    
			</div>
			
			<div class="col-12 text-center">
                 <input type="submit" value="SAVE" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">
                 <a href="<?php echo base_url().$folder_path;?>banks" class="btn btn-secondary mx-2">QUIT</a>
              </div>
            
            
            </div>  </form> 
<?php echo view('includes/footer_scripts'); ?>
<script>
function isValid_Bank_Acc_Number(bank_account_number) {
    // Regex to check valid
    // BANK ACCOUNT NUMBER CODE
    let regex = new RegExp(/^[0-9]{9,18}$/);
 
    // bank_account_number CODE
    // is empty return false
    if (bank_account_number == null) {
        return "false";
    }
 
    // Return true if the bank_account_number
    // matched the ReGex
    if (regex.test(bank_account_number) == true) {
        return "true";
    }
    else {
        return "false";
    }
  }
  
window.ini = load_states('<?php echo $bank_info['bank_country'];?>','<?php echo ($bank_info['bank_state'])?$bank_info['bank_state']:'0';?>');
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
                $('#submitbtn').attr('disabled', 'disabled');
                $('#validation_errors').html('');
            },
            success: function (response) {
               
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                   alert_success(response.message);
					stop_loader();
                    window.location.href='<?php echo base_url();?>admin/banks';                    
                  }
                else{
                    stop_loader();
                    alert_notification(response.message);
                    if(response.errors)
                    {
                        var list = ``;
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
                
            },
            complete: function() {
                stop_loader();
                $('#submitbtn').attr('disabled', false);
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
	
$(document).on('blur','[name="account_name"]', function(){
    var name = $(this).val().trim();
    if(name){
        if(!$('[name="account_alias"]').val().trim())
        {
           $('[name="account_alias"]').val(name); 
        }
        
    }
});
</script>