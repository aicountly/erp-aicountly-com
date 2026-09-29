<?php $header = array( 	'title' => 'Add Bank' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().$folder_path.'banks/add', $attributes);
       ?>
	   <?php echo $message_output->run() ;?> 

            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Add A Bank</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-6">
                <div class="card p-4 my-2">
                <h5 class="pb-2">General Info</h5>    
                    
               <div class="col-12"><label>Bank Name <span class="red">*</span></label><div class="input-group w-75"><?php $data = array(
									  'name'        => 'comp_bank_name',
									  'id'          => 'comp_bank_name',
									  'value'       => set_value('comp_bank_name'),
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
									  'value'       => set_value('comp_bank_acc_no'),
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
									  'value'       => set_value('comp_bank_ifsc'),
									  'minlength'   =>  "3",
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'   => true												  
									  );
									  echo form_input($data);
									  ?>
                </div></div>
			<div class="col-12"><label>MICR Code</label><div class="input-group w-75"> <?php $data = array(
									  'name'        => 'comp_bank_micr',
									  'id'          => 'comp_bank_micr',
									  'value'       => set_value('comp_bank_micr'),
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
									  'value'       => set_value('comp_bank_branch'),
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
									  'value'       => set_value('comp_bank_type'),
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
									  'value'       => set_value('comp_bank_adrs'),
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>City <span class="red">*</span></label> <?php $data = array(
									  'name'        => 'comp_bank_city',
									  'id'          => 'comp_bank_city',
									  'value'       => set_value('comp_bank_city'),
									  'maxlength'   => '255',
									   'class'       => 'form-control',
									   'required'   => true
									 
									  );
									  echo form_input($data);
				 ?></div>
				 <div class="col-12"><label>Pin Code <span class="red">*</span></label> <?php $data = array(
									  'name'        => 'comp_bank_pin_code',
									  'id'          => 'comp_bank_pin_code',
									  'value'       => set_value('comp_bank_pin_code'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control',
									   'required'   => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>State <span class="red">*</span></label><div class="state_div">
                  <?php echo form_dropdown('comp_bank_state', array(''=>'Choose'), '','id="comp_bank_state" class="form-control select2" required'); ?>
						</div></div>
               <div class="col-12"><label>Country <span class="red">*</span></label> <?php echo form_dropdown('comp_bank_country', $CountryDropdown, '1','id="comp_bank_country" class="form-control" required'); ?></div>
               
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
window.ini = load_states('1','0');

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

</script>