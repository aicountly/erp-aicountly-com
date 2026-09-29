<?php $header = array( 	'title' => 'Update Branch' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>
<?php
$ro_address = '';
$co_address = '';
if($company_addresses){
	foreach($company_addresses as $comprow){
		if($comprow['cmp_addr_type']=='1'){
			$ro_address = array('comp_addr1'=>$comprow['cmp_addr1'],'comp_addr2'=>$comprow['cmp_addr2'],'comp_city'=>$comprow['cmp_city'],
			                    'comp_state'=>$comprow['cmp_state'],'comp_pin'=>$comprow['cmp_pin_zip'],'comp_country'=>$comprow['cmp_country']
								);
		}
		if($comprow['cmp_addr_type']=='2'){
			$co_address = array('comp_addr1'=>$comprow['cmp_addr1'],'comp_addr2'=>$comprow['cmp_addr2'],'comp_city'=>$comprow['cmp_city'],
			                    'comp_state'=>$comprow['cmp_state'],'comp_pin'=>$comprow['cmp_pin_zip'],'comp_country'=>$comprow['cmp_country']
								);
		}
	}
	
}
$ro_address = json_encode($ro_address);
$co_address = json_encode($co_address);
?>	  
	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().$folder_path.'branches/modify/'.$account_id, $attributes);
       ?>
	   <div id="validation_errors"></div>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Update Branch</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-4">
                <div class="card p-4 my-2">
                <h5 class="pb-2">General Info</h5>    
                    
               <div class="col-12"><label>Name <span class="red">*</span></label><div class="input-group w-75"><?php $data = array(
									  'name'        => 'account_name',
									  'id'          => 'account_name',
									  'value'       => $account_info['hobo_name'],
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
									  'value'       =>  $account_info['hobo_alias'],
									  'minlength'   =>  "3",
									  'maxlength'   =>  '255',
									  'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
            
				<div class="col-12"><label>Opening Date</label> 
				<?php $bo_opdate = ($account_info['hobo_op_date']!='' && $account_info['hobo_op_date']!='0000-00-00')?date('d-m-Y',strtotime($account_info['hobo_op_date'])):''; ?>
				<input type="text" id="opening_date" name="opening_date" value="<?php echo $bo_opdate;?>" class="datepicker form-control form-control-sm">
				</div>
				<?php $bcl_opdate = ($account_info['hobo_cl_date']!='' && $account_info['hobo_cl_date']!='0000-00-00')?date('d-m-Y',strtotime($account_info['hobo_cl_date'])):''; ?>
				
				<div class="col-12"><label>Closed On</label> 
				<input type="checkbox" id="closed_on" name="closed_on" class="form-control-sm" <?php echo ($bcl_opdate!='')?'checked':'';?>>
				</div>
						
				<div class="col-12" id="closing_date_div" style="<?php echo ($bcl_opdate!='')?'':'display:none;';?>"><label>Closing Date</label> 				
				<input type="text" id="closing_date" name="closing_date" value="<?php echo $bcl_opdate;?>" class="datepicker form-control form-control-sm">
				</div>	
					<div class="col-12" id="group_div">
               		<label>Zone <span class="red">*</span></label>
					<?php echo form_dropdown('account_zone', $zones, $account_info['hobo_zone'],'id="account_zone" class="form-control select2" required="true" '); ?>      	 	
				</div>	
				</div>
						
			</div>
			
			<div class="col-md-5">
			    <div class="card p-4 my-2">
                  <h5 class="pb-2">Address</h5>
				 <p class="text-end">pick address from <button type="button" class="btn btn-sm btn-outline-success" id="pick_ro_adrs">Ro. Address</button> Or <button type="button" class="btn btn-sm btn-outline-success" id="pick_co_adrs">Co. Address</button></p>	
                     
               <div class="col-12"><label>Address Line 1</label> <?php $data = array(
									  'name'        => 'acc_adrs1',
									  'id'          => 'acc_adrs1',
									  'value'       => $account_info['hobo_addr1'],
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Address Line 2</label> <?php $data = array(
									  'name'        => 'acc_adrs2',
									  'id'          => 'acc_adrs2',
									  'value'       => $account_info['hobo_addr2'],
									  'maxlength'   => '100',
									   'class'       => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>City <span class="red">*</span></label> <?php $data = array(
									  'name'        => 'acc_city',
									  'id'          => 'acc_city',
									  'value'       => $account_info['hobo_city'],
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									 
									  );
									  echo form_input($data);
				 ?></div>
				 <div class="col-12"><label>Pin Code <span class="red">*</span></label> <?php $data = array(
									  'name'        => 'acc_pincode',
									  'id'          => 'acc_pincode',
									  'value'       => $account_info['hobo_pin_zip'],
									  'maxlength'   => '32',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>State <span class="red">*</span></label><div class="state_div">
                  <?php echo form_dropdown('state_id', array(''=>'Choose'), $account_info['hobo_state'],'id="state_id" class="form-control select2 required" '); ?>
						</div></div>
               <div class="col-12"><label>Country <span class="red">*</span></label> <?php echo form_dropdown('acc_country_id', $CountryDropdown, $account_info['hobo_country'],'id="acc_country_id" class="form-control" required'); ?></div>
               
               </div>
			    
			</div>
			
			<div class="col-md-3">
                <div class="card p-4 my-2">
                <h5 class="pb-2">TAN Info</h5>    
                    
               <div class="col-12"><label>TAN</label><div class="input-group w-75">
			   <?php $data = array(
									  'name'        => 'comp_tan',
									  'id'          => 'comp_tan',
									  'value'       => $tan_info['hobo_tan']??'',
									  'maxlength'   => '32',
									   'class'      => 'form-control'
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
				
			
				
               <div class="col-12"><label>TAN Jurd</label><div class="input-group w-75">
			   <input type="text" name="tan_jurisd" class="form-control" value="<?php echo $tan_info['hobo_tan_jurisd']??'';?>">			
                </div></div>
            
				<div class="col-12"><label>WEF Date</label> 
				<?php
				if(isset($tan_info['hobo_tan_wef_act']) && $tan_info['hobo_tan_wef_act']!='0000-00-00')
				 $tan_wef_date = $tan_info['hobo_tan_wef_act'];
			   else
				   $tan_wef_date = '';
				 if($tan_wef_date!=''){
					 $tan_wef_date =date('d-m-Y',strtotime($tan_info['hobo_tan_wef_act']));
				 }else
					 $tan_wef_date = '';	
				$data = array(
									  'name'        => 'tan_wef_date',
									  'id'          => 'tan_wef_date',
									  'value'       => $tan_wef_date,
									  'class'      => 'form-control datepicker'
									  );
									  echo form_input($data);
				?>
				</div>
				
				<div class="col-12"><label>Inactive Date</label> 
				<?php 
				if(isset($tan_info['hobo_tan_inact_date']) && $tan_info['hobo_tan_inact_date']!='0000-00-00')
				   $tan_inactive_date = $tan_info['hobo_tan_inact_date'];
			    else
					$tan_inactive_date = '';
				
				 if($tan_inactive_date!=''){
					 $tan_inactive_date =date('d-m-Y',strtotime($tan_info['hobo_tan_inact_date']));
				 }else
					 $tan_inactive_date = '';	
				$data = array(
									  'name'        => 'tan_inactive_date',
									  'id'          => 'tan_inactive_date',
									  'value'       => $tan_inactive_date,
									   'class'      => 'form-control datepicker'
									  );
									 echo form_input($data);
				?>
				</div> 
				
			
					
				</div>
				  						  
						
						
			</div>
			<div class="col-md-12">
                <div class="card p-4 my-2">
                <h5 class="pb-2">GST Info <a href="javascript:void(0);" class="badge text-white bg-secondary" id="clone_cas" style="float:right;">+ Add more</a> </h5>
				<div class="row">
				<div class="col-12">
				<small><strong>Notes:</strong>&nbsp;<em>Gstin master is being managed financial year wise, any change will be effective for current financial year only.</em></small>
                 </div>
				 </div>
				<div class="row">
				<div class="col-md-12"> 
				<table cellpadding="5" cellspacing="0" width="100%" class="table">
				<tr>
				<th>GSTIN</th>
				<th>GSTIN Type</th>
				<th>GST Jurd State</th>
				<th>Jurd Centre</th>
				<th>State Code</th>
				<th>WEF Date</th>
				<th>Inactive Date</th>
				<th>Legal Name</th>
				<th>Trade Name</th>	
				<th>Action</th>	
				</tr>
				
				<?php
				
				if($branch_gsttins_info){
				foreach($branch_gsttins_info as $gsttin_row){	
				 
				  $comp_gstin          = $gsttin_row['hobo_gstin'];
				  $comp_gstin_type     = $gsttin_row['hobo_gstin_type'];
				  $comp_gst_jurid_st   = $gsttin_row['hobo_gstin_jurisd_st'];
				  $comp_gst_jurid_ct   = $gsttin_row['hobo_gstin_jurisd_ct'];
				  ?>
				<tr>
				<td><?php echo $comp_gstin;?></td>
				<td><?php 
				if(isset($gstintypes_list[$comp_gstin_type]))				
				      echo $gstintypes_list[$comp_gstin_type];
			    ?>
				</td>
				<td><?php echo $comp_gst_jurid_st;?></td>
				<td><?php echo $comp_gst_jurid_ct;?></td>
				<td><?php if(isset($gsttin_row['hobo_gstin_state_code']))
					$gstin_state_code =  $gsttin_row['hobo_gstin_state_code'];
				else
				 $gstin_state_code = '04';
			 
			
			 $gstin_state_code_val='';
			  if(isset($states_lists[$gstin_state_code]))
					 $gstin_state_code_val=$states_lists[$gstin_state_code];
				 
			 echo $gstin_state_code_val;
			 ?></td>
				<td><?php if(isset($gsttin_row['hobo_gstin_wef_act']) && $gsttin_row['hobo_gstin_wef_act']!='0000-00-00'){
				  	$gstin_wefdate =date('d M, Y',strtotime($gsttin_row['hobo_gstin_wef_act']));   
				 }
			   else
				   $gstin_wefdate=''; 
			   echo $gstin_wefdate;
			   ?></td>
				<td><?php
				if(isset($gsttin_row['hobo_gstin_inact_date']) && $gsttin_row['hobo_gstin_inact_date']!='0000-00-00'){
					$gstin_inactive_date =date('d M, Y',strtotime($gsttin_row['hobo_gstin_inact_date']));   
				}			     
			   else
				 $gstin_inactive_date='';
			 echo $gstin_inactive_date;
			 ?></td>
				<td><?php echo $gsttin_row['hobo_gstin_legal_name']??'';?></td>
				<td><?php echo $gsttin_row['hobo_gstin_trade_name']??'';?></td>
				<td>
				<p>
				<a href="<?php echo base_url();?>admin/branches/modify_gstin/<?php echo $account_id;?>/<?php echo $gsttin_row['hobo_gstin_id'];?>" class="badge text-white bg-info" style="float:right;">Edit</a>
				<a href="javascript:void(0);" class="badge text-white bg-danger remove_gstin" data-gstinid="<?php echo $gsttin_row['hobo_gstin_id'];?>" data-boid="<?php echo $account_id;?>" style="float:right;margin-right:10px;">Remove</a>
				
				</p>
				</td>
				
				</tr>
				<?php }} ?>
				</table>
				
				</div>
				</div>
				</div>
		    </div>		
			
			<div class="col-12 text-center">
                 <input type="submit" value="SAVE" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">
                 <a href="<?php echo base_url().$folder_path;?>branches" class="btn btn-secondary mx-2">QUIT</a>
              </div>
            
            
            </div>




			</form> 
			 
			 
		<div class="modal fade pt-5" id="gstin_inactive_modal" tabindex="-1" aria-labelledby="proinfoLabel" aria-hidden="true">
                  <div class="modal-dialog modal-md">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">GSTIN info</h4>
                        <button type="button" class="btn-close close_btn"  data-bs-dismiss="modal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					<?php $attributes = " id='gstinctvimdl_myform' name='gstinctvimdl_myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().$folder_path.'branches/inactive_gstin/', $attributes);
       ?>		
					  
					  <div class="row">
					   <div id="modal_validation_errors"></div>
					   <div class="col-12"><label>GSTIN Inactive Date</label>
						<?php 
						$data = array(
									  'name'        => 'gstin_inactv_date',
									  'id'          => 'gstin_inactv_date',
									  'class'      => 'form-control datepicker',
									   'autocomplete'=>'off',
									   'required'    => true
									  );
									  echo form_input($data);
							?>
						</div><input type="hidden" name="inactvboid" id="inactvboid">
						<div class="col-12 text-center">
                 <input type="button" value="SAVE" id="save_gstin_inactvmodal_btn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">
              </div>
						</div>
						</form>
					  </div>
					</div>  
				</div>	
			</div>


<div class="modal fade pt-5" id="gstin_changestatus_modal" tabindex="-1" aria-labelledby="proinfoLabel" aria-hidden="true">
                  <div class="modal-dialog modal-md">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">GSTIN info</h4>
                        <button type="button" class="btn-close close_btn"  data-bs-dismiss="modal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					<?php $attributes = " id='gstinctvimdl_myform' name='gstinctvimdl_myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().'/'.$folder_path.'branches/change_gstin_status/', $attributes);
       ?>		
					  
					  <div class="row">
					   <div id="modal_validation_errors"></div>
					   <div class="col-12"><label>GSTIN Inactive Date</label>
						<?php 
						$data = array(
									  'name'        => 'gstin_inactv_dates',
									  'id'          => 'gstin_inactv_dates',
									  'class'      => 'form-control datepicker',
									   'autocomplete'=>'off',
									   'required'    => true
									  );
									  echo form_input($data);
							?>
						</div><input type="hidden" name="inactvboid" id="inactvboid">
						<div class="col-12 text-center">
                 <input type="button" value="SAVE" id="save_gstin_status_btn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">
              </div>
						</div>
						</form>
					  </div>
					</div>  
				</div>	
			</div>			
			 
			 
			<div class="modal fade pt-5" id="add_gstin_modal" tabindex="-1" aria-labelledby="proinfoLabel" aria-hidden="true">
                  <div class="modal-dialog modal-md">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">GSTIN info</h4>
                        <button type="button" class="btn-close close_btn"  data-bs-dismiss="modal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					  <?php $attributes = " id='gstinmdl_myform' name='gstinmdl_myform' class='needs-validation myform' autocomplete='off' novalidate";
						echo form_open(base_url().$folder_path.'branches/add_more_gstin/'.$account_id, $attributes);
					   ?>
					  <div class="row">
					   <div id="modal_validation_errors"></div>
					   <div class="col-12"><label>GSTIN Type</label>
                  <?php echo form_dropdown('mdl_gstintype', $gstintypes_list, '','id="mdl_gstintype" class="form-control" required'); ?>
						</div>
					 <div class="col-12" id="composition_supply_wrapper"><label>Composition Supply</label>
                  <?php echo form_dropdown('mdl_prm_cmp_suply', $compositionSupplyOptions, '','id="mdl_prm_cmp_suply" class="form-control"'); ?>
						</div>	
					   <div class="col-12"><label>GSTIN</label><div class="input-group w-75">
			   <?php $data = array(
									  'name'        => 'mdl_comp_gstin',
									  'id'          => 'comp_gstin',
									  'maxlength'   => '32',
									  'class'      => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-success modifyInputBtn">#</div>
                </div></div>					
				
               <div class="col-12"><label>GST Jurd State</label><div class="input-group w-75">
			   <input type="text" name="mdl_comp_gst_jurisd_st" class="form-control" value="">
			   <div data-code="0" class="btn btn-sm btn-success modifyInputBtn">#</div>
                </div></div>
            
			
               	<div class="col-12" id="group_div">
               		<label>Jurd Centre</label>
               	 	<input type="text" name="mdl_comp_gst_jurisd_ct" class="form-control" value="">
               	</div>
				
				 <div class="col-12"><label>State Code</label>
				    <input type="text" class="form-control" id="statecode_value" value="" readonly>
                 </div>
				 
				<div class="col-12"><label>WEF Date</label> 
				<?php 
				$data = array(
									  'name'         => 'mdl_gstin_wef_date',
									  'id'           => 'mdl_gstin_wef_date',
									  'class'        => 'form-control datepicker',
									  'autocomplete' => 'off',
									   'required'    => true
									  );
									  echo form_input($data);
				?>
				</div>
				
				<div class="col-12"><label>Legal Name</label> 
				<?php 
				
				$data = array(
									  'name'        => 'mdl_legal_name',
									  'id'          => 'legal_name',
									  'class'       => 'form-control',
									  'required'    => true
									  
									  );
									  echo form_input($data);
				?>
				</div>
				
				<div class="col-12"><label>Trade Name</label> 
				<?php $data = array(
									  'name'        => 'mdl_trade_name',
									  'id'          => 'trade_name',
									   'class'      => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
				?>
				</div>
				<div class="col-12 text-center">
                 <input type="submit" value="SAVE"  title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-success mx-2">
              </div>
					   </div></form>
					  </div>
					 </div>
                     
                 
                  </div>
                </div>
				
<?php echo view('includes/footer_scripts'); ?>
<script>
var json_states=<?php echo json_encode($states_lists);?>;
function getValueByKey(key) {
    if (json_states.hasOwnProperty(key)) {
     return json_states[key];
    } else {
     return "<?php echo $branch_state_code;?>";
    }
 }
var ro_address ='<?php echo $ro_address;?>';
var co_address ='<?php echo $co_address;?>';
$("#pick_ro_adrs").on("click",function(){var c=$.parseJSON(ro_address);$("#acc_adrs1").val(c.comp_addr1),$("#acc_adrs2").val(c.comp_addr2),$("#acc_city").val(c.comp_city),$("#acc_pincode").val(c.comp_pin),$("#acc_country_id").val(c.comp_country),load_states(c.comp_country,c.comp_state),$("#state_id").val(c.comp_state)}),$("#pick_co_adrs").on("click",function(){var c=$.parseJSON(co_address);$("#acc_adrs1").val(c.comp_addr1),$("#acc_adrs2").val(c.comp_addr2),$("#acc_city").val(c.comp_city),$("#acc_pincode").val(c.comp_pin),$("#state_id").val(c.comp_state),$("#acc_country_id").val(c.comp_country),load_states(c.comp_country,c.comp_state)});
</script>
<script>
$(document).on("blur","#comp_gstin",function(){
	var str = $(this).val();
	 var matches = str.match(/\d+/); // Extract numeric part
    if (matches) {
        var firstTwoDigits = matches[0].substring(0, 2); // Get first two digits
       // console.log(firstTwoDigits); // Output: 07
		$("#statecode_value").val(getValueByKey(firstTwoDigits));
		//console.log(getValueByKey(firstTwoDigits)); // Output: "Maharashtra(27)"
		
    }
});
window.ini = load_states('<?php echo $account_info['hobo_country'];?>','<?php echo $account_info['hobo_state'];?>');

$(document).on('click', '#clone_cas', function(){
	$("#add_gstin_modal").modal("show");
});


$(document).on('click', '.markactive_btn', function(){
	var gstinid = $(this).data("gstinid");
	var bogstin_id = $(this).data("boid");
	var bogstin_id_val ='active,'+gstinid+","+bogstin_id;
	$.ajax({
            url: baseurl+'/admin/branches/change_gstin_status', 
             type: 'POST',
			 contentType: 'application/json',
            data: JSON.stringify({
				inactive_date: "",
				bogstin_id: bogstin_id_val,
				token: '<?php echo date('dYis');?>'
			}),
            processData: false,
            cache: false,
            contentType: false,
            beforeSend: function() {
                show_loader();
            },
            success: function (response) {
                stop_loader();                
              stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if(response.status){
					 stop_loader();
                    alert_success(response.message);
                   window.location.href=baseurl+'/admin/branches/modify/'+<?php echo $account_id;?>; 
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
      <strong>Error!</strong>${list}.
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


$(document).on('click', '.markinactive_btn', function(){
	var gstinid = $(this).data("gstinid");
	var bogstin_id = $(this).data("boid");
	$("#gstin_changestatus_modal #inactvboid").val('inactive,'+gstinid+","+bogstin_id);
	$("#gstin_changestatus_modal").modal("show");	
});
function isValidGSTIN(gstin) {
    const gstinPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{3}$/;
    return gstinPattern.test(gstin.toUpperCase());
}

$(document).on('click', '#save_gstin_status_btn', function(){
	
	var inactive_date = $("#gstin_changestatus_modal #gstin_inactv_dates").val();
	var bogstin_id    = $("#gstin_changestatus_modal #inactvboid").val();
	//console.log(inactive_date);
	$.ajax({
             url: baseurl+'/admin/branches/change_gstin_status', 
             type: 'POST',
			 contentType: 'application/json',
             data: JSON.stringify({
				inactive_date: inactive_date,
				bogstin_id: bogstin_id,
				token: '<?php echo date('dYis');?>'
			 }),
             processData: false,
             cache: false,
             contentType: false,
             beforeSend: function() {
                show_loader();
             },
            success: function (response) {
                stop_loader();                              
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
					 stop_loader();
                    alert_success(response.message);
                   window.location.href=baseurl+'/admin/branches/modify/'+<?php echo $account_id;?>;   
                }
                else{
					 stop_loader();
                    alert_notification(response.message);
                    if(response.errors)
                    {
						$("#gstin_changestatus_modal").modal("hide");
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
            },
            error: function (jqXHR, exception) {
				stop_loader();
				$("#gstin_changestatus_modal").modal("hide");
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
	return false;
});



$(document).on('click', '#save_gstin_inactvmodal_btn', function(){
	
	var inactive_date = $("#gstin_inactive_modal #gstin_inactv_date").val();
	var bogstin_id = $("#gstin_inactive_modal #inactvboid").val();
	//console.log(inactive_date);
	$.ajax({
            url: baseurl+'/admin/branches/inactive_gstin', 
             type: 'POST',
			 contentType: 'application/json',
            data: JSON.stringify({
				inactive_date: inactive_date,
				bogstin_id: bogstin_id,
				token: '<?php echo date('dYis');?>'
			}),
            processData: false,
            cache: false,
            contentType: false,
            beforeSend: function() {
                show_loader();
            },
            success: function (response) {
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
				$("#gstin_inactive_modal").modal("hide");
                if(response.status){
					 stop_loader();
                    alert_success(response.message);
                   window.location.href=baseurl+'/admin/branches/modify/'+<?php echo $account_id;?>;   
                }
                else{
					 stop_loader();
                    alert_notification(response.message);
                    if(response.errors)
                    {
						$("#gstin_changestatus_modal").modal("hide");
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
	return false;
});

$(document).on("click",".remove_gstin",function(){
	var boid = $(this).data("boid");
	var gstinid = $(this).data("gstinid");
	
	$.ajax({
            url: baseurl+'/admin/branches/validate_remove_gstin/'+boid+'/'+gstinid, 
            type: 'GET',
            dataType: "json",
            processData: false,
            cache: false,
            contentType: false,
            beforeSend: function() {
                show_loader();
            },
            success: function (response) {
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if(response.status){
					alert_success(response.message);				   
                   window.location.href=baseurl+'/admin/branches/modify/'+boid; 
				}else{
					Swal.fire({
					title: 'Are you sure?',
					text: "The voucher's exists with in the effective period of gstin, the same can't be deleted but can be marked as inactive.",
					icon: 'error',
					showCancelButton: true,
					confirmButtonText: 'Confirm it!',
					customClass: {
					  confirmButton: 'btn btn-primary',
					  cancelButton: 'btn btn-outline-danger ms-1'
					},
					buttonsStyling: false
					}).then(function (result) {
				      if (result.value) {
						// show modal box to enter inactive date for the selected GSTIn to make it inactive
						$("#gstin_inactive_modal #inactvboid").val(gstinid+","+boid);
						$("#gstin_inactive_modal").modal("show");
						
				     }
			        });
			
			    }				                
                
            },
            complete: function() {
                stop_loader();
               
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

$('#closed_on').on('change',function(){
   if(this.checked) 
	  {
	  $("#closing_date").val('');	  
	  $("#closing_date_div").show();
      $("#closing_date").attr("required",true);		
	  }
   else{
	  $("#closing_date_div").hide();
      $("#closing_date").val('');	
	  $("#closing_date").attr("required",false);	
	  }
    });

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
    
    	var acct_gstin= $("#comp_gstin").val();
    var isvalid = isValidGSTIN(acct_gstin);
		 var error=0;
		if(acct_gstin!=''){
			if (acct_gstin.length !== 15) {
			stop_loader();
			error=1;
			alert_notification("GSTIN NUMBER IS NOT VALID. PLS RECTIFY BEFORE PROCEEDING. ",'Cancel');
			return false;
		}
		if(!isvalid){
			stop_loader();
			error=1;
			alert_notification("GSTIN NUMBER IS NOT VALID. PLS RECTIFY BEFORE PROCEEDING. ",'Cancel');
			return false;
		  }		
		if(stid!=acct_gstin.slice(0, 2))
		{    stop_loader();
		error=1;
			alert_notification("STATE CODE OF ENTERED GSTIN IS "+acct_gstin.slice(0, 2)+" WHEREAS THE STATE SELECTED IN ADDRESS IS DIFFERENT. PLS RECTIFY BEFORE PROCEEDING. ",'Cancel');
			return false;
		}
		
		}
		
		if(error==1)
		return false;
		
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
	
	$(document).on('submit', '#gstinmdl_myform', function(e){
	    
	    	var acct_gstin= $("#comp_gstin").val();
    var isvalid = isValidGSTIN(acct_gstin);
		 var error=0;
		if(acct_gstin!=''){
			if (acct_gstin.length !== 15) {
			stop_loader();
			error=1;
			alert_notification("GSTIN NUMBER IS NOT VALID. PLS RECTIFY BEFORE PROCEEDING. ",'Cancel');
			return false;
		}
		if(!isvalid){
			stop_loader();
			error=1;
			alert_notification("GSTIN NUMBER IS NOT VALID. PLS RECTIFY BEFORE PROCEEDING. ",'Cancel');
			return false;
		  }		
		
		
		}
		
		if(error==1)
		return false;
		
		
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
                $('#gstinmdl_myform').attr('disabled', 'disabled');
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
                   
                    if(response.errors)
                    {
						stop_loader();
						$("#add_gstin_modal").modal("hide");
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
                $('#gstinmdl_myform').attr('disabled', false);
            },
            error: function (jqXHR, exception) {
				$("#add_gstin_modal").modal("hide");
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