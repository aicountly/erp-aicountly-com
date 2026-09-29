<?php $header = array( 	'title' => 'Add Branch' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().'/'.$folder_path.'branches/add', $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Add A Branch</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-6">
                <div class="card p-4 my-2">
                <h5 class="pb-2">General Info</h5>    
                    
               <div class="col-12"><label>Name</label><div class="input-group w-75"><?php $data = array(
									  'name'        => 'account_name',
									  'id'          => 'account_name',
									  'value'       => set_value('account_name'),
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
									  'value'       => set_value('account_alias'),
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
               	 	<?php echo form_dropdown('account_group', $group_main_dropdown, set_value('account_group'),'id="account_group" class="form-control select2" required="true" '); ?>
               	</div>

				<div class="col-12"><label>Opening Date</label> 
				<input type="text" id="opening_date" name="opening_date" value="<?php echo date('d-m-Y');?>" class="datepicker form-control form-control-sm">
				</div>		
				<div class="col-12"><label>Closing Date</label> 
				<input type="text" id="closing_date" name="closing_date" value="<?php echo date('d-m-Y');?>" class="datepicker form-control form-control-sm">
				</div>	
					<div class="col-12" id="group_div">
               		<label>Zone</label>
               	 	<?php echo form_dropdown('account_zone', $zones, set_value('account_zone'),'id="account_zone" class="form-control select2" required="true" '); ?>
               	</div>	
				</div>
				  						  
						
						
			</div>
			
			<div class="col-md-6">
			    <div class="card p-4 my-2">
                  <h5 class="pb-2">Address</h5>     
                     
               <div class="col-12"><label>Address Line 1</label> <?php $data = array(
									  'name'        => 'acc_adrs1',
									  'id'          => 'acc_adrs1',
									  'value'       => set_value('adrs1'),
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Address Line 2</label> <?php $data = array(
									  'name'        => 'acc_adrs2',
									  'id'          => 'acc_adrs2',
									  'value'       => set_value('adrs2'),
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>City</label> <?php $data = array(
									  'name'        => 'acc_city',
									  'id'          => 'acc_city',
									  'value'       => set_value('city'),
									  'maxlength'   => '255',
									   'class'       => 'form-control'
									 
									  );
									  echo form_input($data);
				 ?></div>
				 <div class="col-12"><label>Pin Code</label> <?php $data = array(
									  'name'        => 'acc_pincode',
									  'id'          => 'acc_pincode',
									  'value'       => set_value('acc_pincode'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>State</label><div class="state_div">
                  <?php echo form_dropdown('state_id', array(''=>'Choose'), '','id="state_id" class="form-control select2" '); ?>
						</div></div>
               <div class="col-12"><label>Country</label> <?php echo form_dropdown('acc_country_id', $CountryDropdown, '1','id="acc_country_id" class="form-control" '); ?></div>
               
               </div>
			    
			</div>
			<div class="col-12 text-center">
                 <input type="submit" value="SAVE" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">
                 <a href="<?php echo base_url().'/'.$folder_path;?>accounts/list" class="btn btn-secondary mx-2">QUIT</a>
              </div>
            
            
            </div>  </form> 
<?php echo view('includes/footer_scripts'); ?>

<script>
window.ini = load_states('1','0');
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

</script>