<?php $header = array( 	'title' => 'Update Packing List' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
if($local_session->get('ses_company_id')!=''){
    $fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
	$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
} 
else{
   $fy_begndt  =  date('01-04-Y');
   $fy_end     =  date('01-04-Y');
  }
  ?>
<style>
 .myform .col-sm-6{padding-bottom:2px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
    .myform .select2 {width:75%!important; }
</style>	  
      <?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().'/'.$folder_path.'consignment_packing/edit/'.$packing_id, $attributes);
       ?>
        <div class="row">
            <?php echo $message_output->run() ;?>
             <div class="col-6"><h3 class="pb-3">Update Packing List</h3></div>  
             <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
         <div class="col-md-6">
          
            
          <div class="card p-3 my-2">
              <div class="row">
              <p class="col-12"><label>Packing List ID</label> 
			  
					<?php echo $PackingListId;?>
              </p>
			   <p class="col-12"><label>Series:</label>  <?php echo form_dropdown('voucher_series', $voucher_series_dropdown, '4',' id="voucher_series" class="voucher_series form-select required" required '); ?></p>
				 <p class="col-12"><label>Voucher Date</label>
				 <input type="text"  id="voucher_date" name="voucher_date" value="<?php echo date('d-m-Y',strtotime($Consoinfo['voucher_date']));?>" class="datepicker form-control form-control-sm"></p>				  
			  <p class="col-12"><label>MC:</label> <?php echo form_dropdown('matrcntr_id', $matrcntr_dropdown, $Consoinfo["mat_cent_id"],' id="matrcntr_id" class="form-select required" '); ?></p>
			   
						
				</div></div>
				</div><div class="col-md-6">
				<div class="card p-3 my-2"><div class="row">
				  <p class="col-12"><label>Packing list Name</label> <?php $data = array(
								   'name'        => 'pack_list_name',
								   'value'       => $packing_info['list_name'],
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								    'minlength'   =>  "3",
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></p>

				<p class="col-12"><label>Consignment To</label> <?php
			 	    echo form_dropdown("consignment_to",$consignment_to,$packing_info['list_consignee'],' id="consignment_to" class="selectwidget form-control required" required' );
				?></p>							  
								  
              	<p class="col-12"><label>Tentative Delivery</label>
				<input type="text"  id="list_delivery" name="list_delivery" value="<?php echo date('d-m-Y',strtotime($packing_info['list_delivery']));?>" class="datepicker form-control"></p>
			  </div> 
			  
			  </div>    
              
              </div> 
			  
			  <div class="col-12 text-center"><br> <br> 
                 <input type="submit" value="SAVE" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">
                 <a href="<?php echo history_back();?>" class="btn btn-secondary mx-2">QUIT</a>
              </div>
			  </form>
			  <style>
			  /*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
}
			  </style>
<?php echo view('includes/footer_scripts'); ?>
<script>
$('input[type=radio][name="packing_type"]').change(function() {
    if (this.value == 'n'){
        $('#against_div').css('display', 'none');  
        $("#against_ref").val("");		
    }
    else if(this.value == 'a'){
        $('#against_div').css('display', '');
      
    }
});
</script>