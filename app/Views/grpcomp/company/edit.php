<?php $header = array( 	'title' => 'Open Company' ); ?>
<?php if(isset($sel_company_id)){
 echo view('includes/header2',$header);
 ?>
 <style>
 .myform .col-sm-12{padding-bottom:10px;}
    .myform label{width:25%; float:left;}

    .myform .form-control, .myform select {width:75%;}
</style>
  <div class="row mb-4 myalltabs px-3 bg bg-success">
      <div class="col-12" id="tabslistings" style="display:inline-block;width:auto;">
		<a href="<?php echo base_url();?>/home/open_company" ><span id="#" class="btn tabslist active">My Company</span></a>
		<a href="<?php echo base_url();?>/sharedwithme" ><span id="#" class="btn tabslist">Shared With Me</span></a>
		<a href="<?php echo base_url();?>/archivecompany" ><span id="#" class="btn tabslist">Archive Company</span></a>
      </div>
    </div>

 <?php
 } 
 else
 echo view('includes/header',$header);
 ?>

   <?php if(isset($sel_company_id)){ ?>
    <div class="content"><div class="pb-5"> 
    <?php } ?>
          <?php 
          $attributes = " id='form1' name='form1' class='needs-validation myform' autocomplete='off' novalidate";
          if(isset($sel_company_id))
          {
             echo form_open($base_url.'company/modify/'.$sel_company_id, $attributes);  
          }
          else{
             echo form_open($base_url.'company/modify', $attributes);  
          }
          
        ?>
			<div class="row">                
                <div class="col-6"><h3 class="pb-3">Update Company</h3></div>  <div class="col-6"><span class="float-end"><a href="<?php echo base_url();?>/admin/branches" class="markhobtn btn btn-success">Branch Master</a></span></div>  
                                
               <div class="col-md-6"><div class="row m-0">
                   
                 <div class="card col-12 my-3 pt-3">  
                
               <p><label>Company Name</label> <?php $data = array(
									  'name'        => 'comnpany_name',
									  'id'          => 'comnpany_name',
									  'value'       => $company_info['comp_name'],
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></p>
               <p><label>Print Name</label> <?php $data = array(
									  'name'        => 'print_name',
									  'id'          => 'print_name',
									  'value'       => $company_info['comp_print_name'],
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?></p>
               <p><label>Short Name</label> <?php $data = array(
									  'name'        => 'short_name',
									  'id'          => 'short_name',
									  'value'       => $company_info['comp_short_name'],
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true									   
									  );
									  echo form_input($data);
									  ?></p>
               <p><label>Fy. Begining</label> <?php $data = array(
									  'name'        => 'fybegin_date',
									  'id'          => 'fybegin_date',
									  'value'       => date('d-m-Y',strtotime($company_info['fy_begndt'])),
									  'maxlength'   => '255',
									  'class'       => 'form-control datepicker',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></p>
             </div>
             
             <div class="card p-3 my-3">
                 <h5 class="py-2">Ro. Address</h5>
               <div class="col-sm-12"><label>Country/State</label> <div class="d-flex w-75">
                <?php		echo form_dropdown('ro_country', $CountryDropdown, $company_info['ro_country'],'id="ro_country" class="form-control w-50" ');
						?>  <div class="ro_state_div  w-50">
                  <?php echo form_dropdown('ro_state', array(''=>'Choose'), '','id="ro_state" class="form-control" '); ?>
						</div></div></div>
               <div class="col-sm-12"><label>City/Pin</label> <div class="input-group w-75">
			   <?php $data = array(
									  'name'        => 'ro_city',
									  'value'       => $enc_string->nc_string($company_info['ro_city'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control w-50'
									  );
									  echo form_input($data);
									  ?>
				<?php $data = array(
									  'name'        => 'ro_pin	',
									  'value'       => $enc_string->nc_string($company_info['ro_pin'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control w-50',
									   'placeholder' => 'Pin code'
									  );
									  echo form_input($data);
									  ?></div></div>
               <div class="col-sm-12"><label>Address line 1</label> <?php $data = array(
									  'name'        =>  'ro_add1',									 
									  'value'       =>  $enc_string->nc_string($company_info['ro_add1'],'de'),
									  'maxlength'   =>  '32',
									   'class'      =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>  
               <div class="col-sm-12"><label>Address line 2</label> <?php $data = array(
									  'name'        => 'ro_add2',									 
									  'value'       => $enc_string->nc_string($company_info['ro_add2'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
             </div>
             
              <div class="card p-3 my-3">
                 <h5 class="py-2">Co. Address</h5>
               <div class="col-sm-12"><label>Country/State</label> <div class="d-flex w-75">
              <?php		echo form_dropdown('co_country', $CountryDropdown, '1','id="co_country" class="form-control w-50" ');
						?> <div class="co_state_div  w-50">
                  <?php echo form_dropdown('co_state', array(''=>'Choose'), '','id="co_state" class="form-control" '); ?>
						</div></div></div>
               <div class="col-sm-12"><label>City/Pin</label> <div class="input-group w-75">
			   <?php $data = array(
									  'name'        => 'co_city',
									  'value'       => $enc_string->nc_string($company_info['co_city'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control w-50'
									  );
									  echo form_input($data);
									  ?>
				<?php $data = array(
									  'name'        => 'co_pin',
									  'value'       => $enc_string->nc_string($company_info['co_pin'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control w-50',
									   'placeholder' => 'Pin code'
									  );
									  echo form_input($data);
									  ?></div></div>
               <div class="col-sm-12"><label>Address line 1</label><?php $data = array(
									  'name'        =>  'co_add1',									 
									  'value'       =>  $enc_string->nc_string($company_info['co_add1'],'de'),
									  'maxlength'   =>  '32',
									   'class'      =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>  
               <div class="col-sm-12"><label>Address line 2</label> <?php $data = array(
									  'name'        => 'co_add2',									 
									  'value'       => $enc_string->nc_string($company_info['co_add2'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
             </div>
             
              <div class="card p-3 my-3">
             <div class="row py-2 "><h5 class="col-6">Currency Symbol</h5><p class="col-6 text-end">

             
             	<a href="<?= base_url() ?>/admin/currency/index/<?= $company_id ?>" class="btn btn-sm btn-success">Manage</a>
           

             </p></div>
               
			   
			    <div class="cloned-row-currency">
			   <div class="col-sm-12"><label>Symbol/Font</label>
			   <div class="input-group w-75">
			   <?php
			    if(isset($currency_info['curr_symbol']))
			     $curr_symbol = $currency_info['curr_symbol'];
			     else
			     $curr_symbol = '';
			     
			   $data = array(
									  'name'         =>  'curr_symbol[]',									 
									  'value'        =>  $curr_symbol,
									  'maxlength'    =>  '32',
									   'class'       =>  'form-control w-50',
									   'placeholder' =>  'Symbol'
									  );
									  echo form_input($data);
									  ?>
				<?php
				 if(isset($currency_info['curr_symbol']))
			     $curr_symbol = $currency_info['curr_symbol'];
			     else
			     $curr_symbol = '';
				
				$data = array(
									  'name'        => 'curr_font[]',
									  'value'       => $curr_symbol,
									  'maxlength'   => '32',
									  'class'       => 'form-control w-25',
									  'placeholder' => 'Font' 
									  );
									  echo form_input($data);
									  ?>					  
                </div></div>
               <div class="col-sm-12"><label>String</label> 
			   <?php 
			    if(isset($currency_info['curr_string']))
			     $curr_string = $currency_info['curr_string'];
			     else
			     $curr_string = '';
			   
			   $data = array(
									  'name'        => 'curr_string[]',									 
									  'value'       => $curr_string,
									  'maxlength'   => '32',
									  'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>									  
			   </div>
               <div class="col-sm-12"><label>Sub String</label> 
			   <?php 
			    if(isset($currency_info['curr_sub_string']))
			     $curr_sub_string = $currency_info['curr_sub_string'];
			     else
			     $curr_sub_string = '';
			   
			   $data = array(
									  'name'        => 'curr_sub_string[]',									
									  'value'       => $curr_sub_string,
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
									  </div> 
               <div class="col-sm-12 text-right"></div>
             </div> </div>
             
             
              </div></div> 
               
                
               <div class="col-md-6"><div class="row m-0">
                
                
                <div class="card p-3 my-3">
                 <h5 class="py-2 mb-3">GST Information</h5>
                 <div class="row"> 
                 <div class="col-sm-6">
                 <p><label>GSTIN</label>  <?php $data = array(
									  'name'        => 'gstin',
									  'id'          => 'gstin',
									  'value'       => $enc_string->nc_string($company_info['ho_gstin'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 <p><label>PAN</label>  <?php $data = array(
									  'name'        => 'pan_no',
									  'id'          => 'pan_no',
									  'value'       =>  $enc_string->nc_string($company_info['ho_pan'],'de'),
									  'maxlength'   => '32',
									   'class'      => 'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 <p><label>TAN</label>  <?php $data = array(
									  'name'        => 'tan_no',
									  'id'          => 'tan_no',
									  'value'       =>  $enc_string->nc_string($company_info['ho_tan'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 <p><label>CIN</label>  <?php $data = array(
									  'name'        => 'comp_cin',
									  'id'          => 'comp_cin',
									  'value'       => $enc_string->nc_string($company_info['cin'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 <p><label style="line-height:18px;">Comp. Email</label>  <?php $data = array(
									  'name'        => 'corp_email',
									  'id'          => 'corp_email',
									  'value'       =>  $enc_string->nc_string($company_info['corp_email'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 <p><label style="line-height:18px;">Comp. Tel</label>  <?php $data = array(
									  'name'        => 'corp_tel',
									  'id'          => 'corp_tel',
									  'value'       =>  $enc_string->nc_string($company_info['corp_mobile'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 </div>
                 <div class="col-sm-6">
                 <p><label style="line-height:18px;">GST Jurd State</label>  <input type="text" name="jurdstate" class="form-control" value="<?php echo $enc_string->nc_string($company_info['ho_gst_jurid_st'],'de');?>"></p>
                 <p><label style="line-height:18px;">Jurd Centre</label>  <input type="text" name="jurdcentre" class="form-control" value="<?php echo $enc_string->nc_string($company_info['ho_gst_jurid_ct'],'de');?>"></p>
                 <p><label style="line-height:18px;">IT Jurd</label>  <input type="text" name="itjurd" class="form-control" value="<?php echo $enc_string->nc_string($company_info['ho_it_jurid'],'de');?>"></p>
                 <p><label>W/A No</label>  <?php $data = array(
									  'name'        => 'comp_wa_mobile',
									  'id'          => 'comp_wa_mobile',
									  'value'       => $enc_string->nc_string($company_info['comp_wa_mobile'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 <p><label>Industry</label> <?php	
                   $industry_type_list = industry_type_list();
				   echo form_dropdown('industry_id', $industry_type_list, $company_info['comp_industry'],'id="industry_id" class="form-control select"  ');
						?></p>
                 <p><label style="line-height:18px;">Nature of Work</label>  <?php	
                   $natureof_work_lists = natureof_work_lists();
				   echo form_dropdown('nature_ofwork', $natureof_work_lists,$company_info['comp_work_nature'],'id="nature_ofwork" class="form-control select"  ');
						?></p>
                 </div>
                 
                 </div></div>
                 
                 
                   <div class="card p-3 my-3">
                  <div class="row py-2 "><h5 class="col-6">Auditor Details</h5><p class="col-6 text-end"></p></div>      
                 <div class="row cloned-row-ca"> 
                 <p class="col-sm-6"><label>First Name</label>  <?php 
                 
                  if(isset($auditor_info['comp_ca_name']))
                  $comp_ca_name= $auditor_info['comp_ca_name'];
                  else
                  $comp_ca_name='';
                  
                  
                  $data = array(
									  'name'        => 'comp_ca_fname[]',									 
									  'value'       => $comp_ca_name,
									  'maxlength'   => '60',
									   'class'       =>  'form-control'
									  );
							echo form_input($data);
					?></p>
                 <p class="col-sm-6"><label>Last Name</label>  <?php
                   if(isset($auditor_info['comp_ca_name2']))
                  $comp_ca_name2= $auditor_info['comp_ca_name2'];
                  else
                  $comp_ca_name2='';
                 
                 $data = array(
									  'name'        => 'comp_ca_lname[]',									 
									  'value'       => $comp_ca_name2,
									  'maxlength'   => '60',
									   'class'       =>  'form-control'
									  );
							echo form_input($data);
					?></p>
                 <p class="col-sm-6"><label>Email</label>  <?php
                   if(isset($auditor_info['comp_ca_email']))
                  $comp_ca_email= $auditor_info['comp_ca_email'];
                  else
                  $comp_ca_email='';
                 $data = array(
									  'name'        => 'comp_ca_email[]',									 
									  'value'       => $comp_ca_email,
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 <p class="col-sm-6"><label>W/A No</label>  <?php 
                 if(isset($auditor_info['comp_ca_wamobile']))
                  $comp_ca_wamobile= $auditor_info['comp_ca_wamobile'];
                  else
                  $comp_ca_wamobile='';
                 
                 $data = array(
									  'name'        => 'comp_ca_wamobile[]',									
									  'value'       => $comp_ca_wamobile,
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 <p class="col-sm-6"><label>Mobile</label>  <?php 
                 
                 if(isset($auditor_info['comp_ca_mobile']))
                  $comp_ca_mobile= $auditor_info['comp_ca_mobile'];
                  else
                  $comp_ca_mobile='';
                 $data = array(
									  'name'        => 'comp_ca_mobile[]',									
									  'value'       => $comp_ca_mobile,
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 <p class="col-sm-6"><label>MRN</label> 
								<?php 
								
							 if(isset($auditor_info['comp_ca_mrn']))
                  $comp_ca_mrn= $auditor_info['comp_ca_mrn'];
                  else
                  $comp_ca_mrn='';	
								$data = array(
									  'name'        => 'comp_ca_mrn[]',									
									  'value'       => $comp_ca_mrn,
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 <p class="col-sm-6"><label>Frn Name</label> 
								<?php 
							 if(isset($auditor_info['comp_ca_frn_name']))
                  $comp_ca_frn_name= $auditor_info['comp_ca_frn_name'];
                  else
                  $comp_ca_frn_name='';		
								
								$data = array(
									  'name'        => 'comp_ca_frnname[]',									
									  'value'       => $comp_ca_frn_name,
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
									 </p>
                    <p class="col-sm-6"><label>Frn No</label>
								<?php 
								 if(isset($auditor_info['comp_ca_frn']))
                  $comp_ca_frn= $auditor_info['comp_ca_frn'];
                  else
                  $comp_ca_frn='';		
								
								$data = array(
									  'name'        => 'comp_ca_frnno[]',									
									  'value'       => $comp_ca_frn,
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>

				        </p>
				<p class="col-sm-6">

				        </p>		
                 </div>
             
				 </div>
                 
                 <div class="card p-3 my-3">
                <div class="row py-2 "><h5 class="col-6">Accountant Details</h5><p class="col-6 text-end"></p></div> 
                 <div class="row cloned-row-accountant"> 
                 <p class="col-sm-6"><label>First Name</label>  <?php
                  if(isset($accountant_info['comp_acct_name1']))
                  $comp_acct_name1= $accountant_info['comp_acct_name1'];
                  else
                  $comp_acct_name1='';	
                  
                 $data = array(
									  'name'        => 'comp_acc_fname[]',
									  'value'       => $comp_acct_name1,
									  'maxlength'   => '60',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 <p class="col-sm-6"><label>Last Name</label> <?php 
                 
                  if(isset($accountant_info['comp_acct_name2']))
                  $comp_acct_name2= $accountant_info['comp_acct_name2'];
                  else
                  $comp_acct_name2='';	
                  
                 $data = array(
									  'name'      => 'comp_acc_lname[]',									 
									  'value'     =>$comp_acct_name2,
									  'maxlength' => '60',
									  'class'     => 'form-control'
									   );
									  echo form_input($data);
									  ?></p>
                 <p class="col-sm-6"><label>Email</label>  <?php
                 
                   if(isset($accountant_info['comp_acct_email']))
                  $comp_acct_email= $accountant_info['comp_acct_email'];
                  else
                  $comp_acct_email='';
                  
                 $data = array(
									  'name'        => 'comp_acc_email[]',
									  'id'          => 'comp_acc_email',
									  'value'       =>$comp_acct_email,
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 <p class="col-sm-6"><label>W/A No</label>  <?php
                   if(isset($accountant_info['comp_acct_wamobile']))
                  $comp_acct_wamobile= $accountant_info['comp_acct_wamobile'];
                  else
                  $comp_acct_wamobile='';
                 
                 $data = array(
									  'name'        => 'comp_acc_wamobile[]',
									  'id'          => 'comp_acc_wamobile',
									  'value'       => $comp_acct_wamobile,
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
                 <p class="col-sm-6"><label>Mobile</label>  <?php 
                  if(isset($accountant_info['comp_acct_mobile']))
                  $comp_acct_mobile= $accountant_info['comp_acct_mobile'];
                  else
                  $comp_acct_mobile='';
                 
                 $data = array(
									  'name'        => 'comp_acc_mobile[]',
									  'id'          => 'comp_acc_mobile',
									  'value'       => $comp_acct_mobile,
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
									  
				<p class="col-sm-12"></p>
                   </div>                                 
                     </div>
				 </div>
               
               </div></div>
         <div class="col-sm-12 text-center"><input type="submit" value="SAVE" class="btn btn-primary mx-2">  </div>
            
            
              </div> </form>

<?php echo view('includes/footer_scripts'); ?>	 
<script>
<?php if(isset($company_info['ro_country']) && $company_info['ro_country']!=''){ ?>
window.ini = load_ro_states('<?php echo $company_info['ro_country'];?>','<?php echo $company_info['ro_state'];?>'),load_co_states('<?php echo $company_info['co_country'];?>','<?php echo $company_info['co_state'];?>');

<?php } else { ?>
window.ini = load_ro_states('1','0');
<?php } ?>
function load_ro_states(country_id,state_id){
 $(".ro_state_div").html("Loading...");	
 $.get(baseurl+'/home/ajax_states_list/'+country_id+'/'+state_id, 
      function (data) {  
         $(".ro_state_div").html(data);	
		 $("#state_id").attr("name","ro_state");
		 $("#state_id").attr("id","ro_state");  
     });	
 
//load_co_states(country_id,state_id); 
	 
}

function load_co_states(country_id,state_id){
 $(".co_state_div").html("Loading...");	
 $.get(baseurl+'/home/ajax_states_list/'+country_id+'/'+state_id, 
      function (data) {  
         $(".co_state_div").html(data);		
          $("#state_id").attr("name","co_state");
		 $("#state_id").attr("id","co_state"); 		 
     });	
}
 
$("#ro_country").on("change",function(){
var country_id = $(this).val();	
 $(".ro_state_div").html("Loading...");	
 $.get(baseurl+'/home/ajax_states_list/'+country_id+'/0', 
      function (data) {  
         $(".ro_state_div").html(data);
		 $("#state_id").attr("name","ro_state");
		 $("#state_id").attr("id","ro_state");
     });	
})
$("#co_country").on("change",function(){
var country_id = $(this).val();	
 $(".co_state_div").html("Loading...");	
 $.get(baseurl+'/home/ajax_states_list/'+country_id+'/0', 
      function (data) {  
         $(".co_state_div").html(data);
		 $("#state_id").attr("name","co_state");
		 $("#state_id").attr("id","co_state");
     });	
})



var imgrow_counter=2;
$("#clone_accountants").on('click',	function() {
	var result = $(".cloned-row-accountant").first().clone();
		$(".cloned-row-accountant").last().after(result);	
		$(".cloned-row-accountant").last().attr("id","acttr"+imgrow_counter);	
		var btnvalue='<p class="col-12 text-end"><a href="javascript:void(0);"  class="badge text-white bg-danger remove_accountant" data-id="'+imgrow_counter+'">- remove</a></p>';
		$(".cloned-row-accountant p").last().replaceWith( btnvalue )
	    $("#acttr"+imgrow_counter+" input[type=text]").val("");
		$("#acttr"+imgrow_counter+" textarea").val("");			
		imgrow_counter=imgrow_counter+1;			
	});	
	$(document).on("click",".remove_accountant", function (event) {						   
		event.preventDefault();	
		var colorid = $(this).attr("data-id");
		if(colorid!=''){
		  $("#acttr"+colorid+"").remove(); 
		 }									
	});
    var imgrow_counter1=2;
$  ("#clone_cas").on('click',	function() {
	var result = $(".cloned-row-ca").first().clone();
		$(".cloned-row-ca").last().after(result);	
		$(".cloned-row-ca").last().attr("id","catr"+imgrow_counter1);	
		var btnvalue='<p class="col-6 text-end"><a href="javascript:void(0);" class="badge text-white bg-danger remove_ca" data-id="'+imgrow_counter1+'">- remove</a></p>';
		$(".cloned-row-ca p").last().replaceWith( btnvalue )
	    $("#catr"+imgrow_counter1+" input[type=text]").val("");
		$("#catr"+imgrow_counter1+" textarea").val("");			
		imgrow_counter1=imgrow_counter1+1;			
	});	
	$(document).on("click",".remove_ca", function (event) {						   
		event.preventDefault();	
		var colorid = $(this).attr("data-id");
		if(colorid!=''){
		  $("#catr"+colorid+"").remove(); 
		 }									
	});	
	
	var imgrow_counter2=2;
  $("#clone_currency").on('click',	function() {
	var result = $(".cloned-row-currency").first().clone();
		$(".cloned-row-currency").last().after(result);	
		$(".cloned-row-currency").last().attr("id","curtr"+imgrow_counter2);	
		var btnvalue='<p class="col-6 text-end"><a href="javascript:void(0);" class="badge text-white bg-danger remove_currency" data-id="'+imgrow_counter2+'">- remove</a></p>';
		$(".cloned-row-currency div").last().replaceWith( btnvalue )
	    $("#curtr"+imgrow_counter2+" input[type=text]").val("");
		$("#curtr"+imgrow_counter2+" textarea").val("");			
		imgrow_counter2=imgrow_counter2+1;			
	});	
	$(document).on("click",".remove_currency", function (event) {						   
		event.preventDefault();	
		var colorid = $(this).attr("data-id");
		if(colorid!=''){
		  $("#curtr"+colorid+"").remove(); 
		 }									
	});	
</script>
 </body>
</html>