<?php $header = array( 	'title' => 'Update Company' ); ?>
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
		<a href="<?php echo base_url();?>my_companies" ><span id="#" class="btn tabslist active">My Company</span></a>
		<a href="<?php echo base_url();?>sharedwithme" ><span id="#" class="btn tabslist">Shared With Me</span></a>
		<a href="<?php echo base_url();?>archivecompany" ><span id="#" class="btn tabslist">Archive Company</span></a>
      </div>
    </div>

 <?php
 } 
 else
 echo view('includes/header',$header);

  if($company_other_info){	  
	  $cmp_email =$company_other_info['cmp_email'];
	  $cmp_industry=$company_other_info['cmp_industry'];
	  $cmp_work_nature=$company_other_info['cmp_work_nature'];
	  $cmp_wa_mobile =$company_other_info['cmp_wa_mobile'];
	  $cmp_mobile =$company_other_info['cmp_mobile'];
	  $cmp_tel =$company_other_info['cmp_tel'];
  }else{
	  $cmp_email ='';
	  $cmp_industry='';
	  $cmp_work_nature='';
	  $cmp_wa_mobile ='';
	  $cmp_mobile ='';
	  $cmp_tel ='';
     }
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
                <div class="col-6"><h3 class="pb-3">Manage Company Master</h3></div>  <div class="col-6"><span class="float-end">
                	<?php if(isset($company_id)){ ?>
					<a href="<?php echo base_url();?>admin/banks" class="markhobtn btn btn-success">Bank Master</a>
					<a href="<?php echo base_url();?>admin/branches" class="markhobtn btn btn-success">Branch Master</a>
                	<a href="<?php echo base_url();?>admin/currency" class="markhobtn btn btn-success">Currency Master</a>
                	
					<?php
					}
					if( ($last_comp_fy_id==$current_fy_id) && count($all_fy_list)>1){ ?>
					<a href="javascript:void(0);" class="markhobtn btn btn-warning" id="delfybtn">Delete FY</a>
					<?php } ?>
					
                </span></div>  
                                
               <div class="col-md-6"><div class="row m-0">
                   
                 <div class="card col-12 my-3 pt-3">  
                 <h5 class="py-2">&nbsp;</h5>
               <p><label>Company Name</label> <?php $data = array(
									  'name'        => 'comnpany_name',
									  'id'          => 'comnpany_name',
									  'value'       => $company_info['cmp_name'],
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></p>
					<div class="row">						
				 <div class="col-sm-6">	
						<p><label>Print Name</label> <?php $data = array(
									  'name'        => 'print_name',
									  'id'          => 'print_name',
									  'value'       => $company_info['cmp_print_name'],
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?></p>

			</div>
			 <div class="col-sm-6">	
			<p><label>Short Name</label> <?php $data = array(
									  'name'        => 'short_name',
									  'id'          => 'short_name',
									  'value'       => $company_info['cmp_short_name'],
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true									   
									  );
									  echo form_input($data);
									  ?></p>

			</div>
			</div>		 
               
               
               <p><label>Fy. Begining</label> 
               		<input type="text" class="form-control" value="<?= date('d-m-Y',strtotime($company_last_fy['fy_beg_date'])) ?>" disabled>
								</p>
				  <p><label>Default Stock Valuation</label> 
               		<select name="valmethod_id" id="valmethod_id" class="form-control required" required="">
						<option value="AVG" <?php echo ($company_info['def_val_method']=='AVG')?'selected="selected"' :'';?>>AVG COST</option>
						<option value="FIFO" <?php echo ($company_info['def_val_method']=='FIFO')?'selected="selected"' :'';?>>FIFO</option>
						<option value="LIFO" <?php echo ($company_info['def_val_method']=='LIFO')?'selected="selected"' :'';?>>LIFO</option>
					</select>	
								</p>				
								<p></p>
             </div>
             
             <div class="card p-3 my-3">
                 <h5 class="py-2">Ro. Address</h5>
               <div class="col-sm-12"><label>Country/State</label> <div class="d-flex w-75">
                <?php
			   if(isset($company_ro_info['cmp_country']))
					   $ro_country = $company_ro_info['cmp_country'];
				   else
					   $ro_country = 0;
				   
				   if(isset($company_ro_info['cmp_city']))
					   $ro_city = $company_ro_info['cmp_city'];
				   else
					   $ro_city ='';
				   
					if(isset($company_ro_info['cmp_state']))
					   $ro_state = $company_ro_info['cmp_state'];
				   else
					   $ro_state ='0';
	   
	   
				echo form_dropdown('ro_country', $CountryDropdown, $ro_country,'id="ro_country" class="form-control w-50" ');
						?>  <div class="ro_state_div  w-50">
                  <?php echo form_dropdown('ro_state', array(''=>'Choose'), '','id="ro_state" class="form-control" '); ?>
						</div></div></div>
               <div class="col-sm-12"><label>City/Pin</label> <div class="input-group w-75">
			   <?php $data = array(
									  'name'        => 'ro_city',
									  'value'       => $ro_city,
									  'maxlength'   => '32',
									   'class'      => 'form-control w-50'
									  );
									  echo form_input($data);
									  ?>
				<?php
				   if(isset($company_ro_info['cmp_pin_zip']))
					   $ro_pin = $company_ro_info['cmp_pin_zip'];
				   else
					   $ro_pin ='';


				$data = array(
									  'name'        => 'ro_pin',
									  'value'       => $ro_pin,
									  'maxlength'   => '32',
									   'class'       =>  'form-control w-50',
									   'placeholder' => 'Pin code'
									  );
									  echo form_input($data);
									  ?></div></div>
               <div class="col-sm-12"><label>Address line 1</label> <?php

				   if(isset($company_ro_info['cmp_addr1']))
						   $ro_add1 = $company_ro_info['cmp_addr1'];
					   else
						   $ro_add1 ='';
					   
					   if(isset($company_ro_info['cmp_addr2']))
						   $ro_add2 = $company_ro_info['cmp_addr2'];
					   else
						   $ro_add2 ='';
					   
							   $data = array(
												'name'        => 'ro_add1',									 
												'value'       => $ro_add1,
												'maxlength'   => '32',
												'class'       => 'form-control'
											);
										echo form_input($data);
									  ?></div>  
               <div class="col-sm-12"><label>Address line 2</label> <?php $data = array(
									  'name'        => 'ro_add2',									 
									  'value'       => $ro_add2,
									  'maxlength'   => '32',
									   'class'      => 'form-control'
									  );
									  echo form_input($data);
									  ?></div>
             </div>
             
              </div></div> 
                
               <div class="col-md-6">
			   
			   <div class="row m-0">
                <div class="card p-3 my-3">
                 <h5 class="py-2 mb-3">Contact Information</h5>
                 <div class="row"> 
                 <div class="col-sm-6">
                
                 <p><label>Comp. Email</label>  <?php
		
				 $data = array(
									  'name'        => 'cmp_email',
									  'id'          => 'cmp_email',
									  'value'       =>  $cmp_email,
									  'maxlength'   => '100',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
								</div>
				 <div class="col-sm-6">						
				 <p><label>Comp. Mobile</label>  <?php $data = array(
									  'name'        => 'cmp_mobile',
									  'id'          => 'cmp_mobile',
									  'value'       =>  $cmp_mobile,
									  'maxlength'   => '15',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>					  
					</div>				  
                
                 <div class="col-sm-6">
                  <p><label>W/A No</label>  <?php $data = array(
									  'name'        => 'cmp_wa_mobile',
									  'id'          => 'cmp_wa_mobile',
									  'value'       => $cmp_wa_mobile,
									  'maxlength'   => '15',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
									  </div>
				<div class="col-sm-6">					  
				<p><label>Comp. Tel</label>  <?php $data = array(
									  'name'        => 'cmp_tel',
									  'id'          => 'cmp_tel',
									  'value'       =>  $cmp_tel,
									  'maxlength'   => '15',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></p>
</div>									  
				 <div class="col-sm-6">	
				 <p><label>Nature of Work</label>
				 	<select name="nature_ofwork" id="" class="form-control">
        		<option value=""></option>
        		<?php foreach ($natureof_work_list as $key => $value) { ?>
        			<option <?php echo ($cmp_work_nature==$value['work_nature_id'])?'selected':'';?> value="<?= $value['work_nature_id'] ?>"><?= $value['work_nature'] ?></option>
        		<?php } ?>
        	</select>
   				</p>					  
                 </div>	
				 <div class="col-sm-6">			
                 <p><label>Industry</label>
                 <select name="industry_id" id="" class="form-control">
                		<option value=""></option>
                		<?php foreach ($industry_type_list as $key => $value) { ?>
                			<option <?php echo ($cmp_industry==$value['industry_type_id'])?'selected':'';?> value="<?= $value['industry_type_id'] ?>"><?= $value['industry_type'] ?></option>
                		<?php } ?>
                	</select>
                </p>
                
                 </div>
                 
                 </div></div>
				 
				 <div class="card p-3 my-3">
                 <h5 class="py-2">Co. Address</h5>
               <div class="col-sm-12"><label>Country/State</label> <div class="d-flex w-75">
              <?php
				 if(isset($company_co_info['cmp_country']))
						   $co_country = $company_co_info['cmp_country'];
					   else
						   $co_country ='0';

				 if(isset($company_co_info['cmp_city']))
						   $co_city = $company_co_info['cmp_city'];
					   else
						   $co_city ='';
					  
				 if(isset($company_co_info['cmp_state']))
						   $co_state = $company_co_info['cmp_state'];
					   else
						   $co_state ='0';
	   
			  echo form_dropdown('co_country', $CountryDropdown, $co_country,'id="co_country" class="form-control w-50" ');
						?> <div class="co_state_div  w-50">
                  <?php echo form_dropdown('co_state', array(''=>'Choose'), '','id="co_state" class="form-control" '); ?>
						</div></div></div>
               <div class="col-sm-12"><label>City/Pin</label> <div class="input-group w-75">
			   <?php $data = array(
									  'name'        => 'co_city',
									  'value'       =>$co_city,
									  'maxlength'   => '32',
									   'class'       =>  'form-control w-50'
									  );
									  echo form_input($data);
									  ?>
				<?php
			if(isset($company_co_info['cmp_pin_zip']))
					   $co_pin = $company_co_info['cmp_pin_zip'];
				   else
					   $co_pin ='';

				$data = array(
									  'name'        => 'co_pin',
									  'value'       => $co_pin,
									  'maxlength'   => '32',
									   'class'       =>  'form-control w-50',
									   'placeholder' => 'Pin code'
									  );
									  echo form_input($data);
									  ?></div></div>
               <div class="col-sm-12"><label>Address line 1</label><?php
				 if(isset($company_co_info['cmp_addr1']))
						   $co_add1 = $company_co_info['cmp_addr1'];
					   else
						   $co_add1 ='';
					   
					   if(isset($company_co_info['cmp_addr2']))
						   $co_add2 = $company_co_info['cmp_addr2'];
					   else
						   $co_add2 ='';
	   

			   $data = array(
									  'name'        =>  'co_add1',									 
									  'value'       =>  $co_add1,
									  'maxlength'   =>  '32',
									   'class'      =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>  
               <div class="col-sm-12"><label>Address line 2</label> <?php $data = array(
									  'name'        => 'co_add2',									 
									  'value'       => $co_add2,
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
             </div>
                  
				 </div>
               
               </div></div>
         <div class="col-sm-12 text-center"><input type="submit" value="SAVE" class="btn btn-primary mx-2">  </div>
            
            
              </div> </form>

 <!-- The Modal -->
<div class="modal" id="logoModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Logo</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<div class="row p-2">
      		<div class="col-md-12 text-center">
      			<img id="logo" src="<?= base_url().'admin/company/logo/'.$company_id ?>" alt="No Logo"  onerror="this.onerror=null;this.src='<?= base_url() ?>public/assets/img/default_logo.png';" style="width: 250px; height: 250px;">
      		</div>
      	</div>

      	<div id="logo_file_validation"></div>

      	

        <form id="logo_form" action="<?= base_url() ?>admin/company/upload_file" method="post" enctype="multipart/form-data">
				  <input type="hidden" name="comp_id" value="<?= $company_id ?>">
				  <input type="file" id="logo_file" name="logo_file" class="form-control" accept="image/*">
				  
				</form>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button form="logo_form" id="logo_form_btn" class="btn btn-sm btn-success" type="submit">Upload</button>
      </div>

    </div>
  </div>
</div>


<div class="modal" id="FYDelModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Confirm OTP</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
       
      	<div id="delfy_validation"></div>
        <form id="fydel_form" action="<?= base_url() ?>admin/company/confirmotp" method="post">
				  <input type="hidden" name="comp_id" value="<?= $company_id ?>">
				  <input type="text" id="delotp" name="delotp" placeholder="fill otp" class="form-control" minlength="6" maxlength="6"  onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))">
				  <br>
				  OTP Sent To : <span id="delfy_otp_email"></span>
				</form>

      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <button form="fydel_form" id="delfy_form_btn" class="btn btn-sm btn-success" type="submit">Verify</button>
      </div>

    </div>
  </div>
</div>

<?php echo view('includes/footer_scripts'); ?>

<script>
	$("#delfybtn").on("click",function(){
	 Swal.fire({
        title: 'Are you sure to delete?',
        text: "Company FY(<?php echo $startendfy;?>) data will be lost.",
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes Delete!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
          show_loader();
		    var otpres = calldel_otp();	
             
			
        }
      });
	  return false;	
	});
	
	async function calldel_otp() {
		const settings = {
        method: 'POST',
		headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        }
    };
		var resp = await fetch(baseurl+'admin/company/sending_delfy_otp', settings);
		let result = await resp.json();
		stop_loader();
			 $("#delfy_otp_email").html(result.otp_send_to);
			 $('#delfy_form_btn').attr('disabled', false);
            $("#FYDelModal").modal("show");
			
		
	}
	
	async function DelCompFY() {
		show_loader();
               $(".siteloader").show();
           let start_time = performance.now();
        
          var interval   = setInterval(function(){
                var currenttime  = performance.now();
                var pct =((currenttime - start_time) / currenttime*100).toFixed(2);
                 $('#progressbar').css({width: pct+"%"}, {duration: 2000, easing: 'linear'});
                 $('#progressbar').attr('aria-valuenow',pct);
                 $('#progressbar .progress-bar').css("width", pct+"%");
                 $('#progressbar .progress-bar').text(pct+"%");
              
          }, 1000); 
          
       
         let response = await fetch(baseurl+'admin/company/remove_company_fy');
            
            let result = await response.json();
            requestTime = performance.now();
           if(result){
			   
            clearInterval(interval);
			if(result.status=="0"){
				alert_notification(result.message);
			}else{
			alert_success(result.message);
            $('#progressbar').css("width","100%");
            $('#progressbar .progress-bar').css("width", "100%");
            $('#progressbar .progress-bar').text("100%");
            window.location.href=baseurl+"/admin/dashboard";
			}
			
           } 		
       }
			
	
$(document).on('submit', '#fydel_form', function(event){
		event.preventDefault();

		var form = $(this);
		var formData = new FormData(form[0]);
		
		$.ajax({
			url : form.attr('action'),
			type : 'POST',
			data : formData,
			dataType : 'json',
			processData: false,
			contentType: false,
      beforeSend: function() {
          $('#delfy_form_btn').attr('disabled', 'disabled');
          $('#delfy_validation').html('');
      },
      success : function(response) {       
			if(response.status==true){
				$("#FYDelModal").modal("hide");
       		    $('#fydel_form').trigger("reset");				
				//alert_success(response.message);
       		    DelCompFY();
			}
			else{
			  alert_notification(response.message);	
			}
      },
      complete: function() {
        $('#delfy_form_btn').attr('disabled', false);
      },

		});
	});
	
	
	$(document).on('submit', '#logo_form', function(event){
		event.preventDefault();

		var form = $(this);
		var formData = new FormData(form[0]);
		
		$.ajax({
			url : form.attr('action'),
			type : 'POST',
			data : formData,
			dataType : 'json',
			processData: false,
			contentType: false,
      beforeSend: function() {
          show_loader();
          $('#logo_form_btn').attr('disabled', 'disabled');
          $('#logo_file_validation').html('');
      },
      success : function(response) {
       	if(response.status){
       		alert(response.message);
       		$('#logo_form').trigger("reset");
       		$('#logo').attr('src', '<?= base_url() ?>admin/company/logo/<?= $company_id  ?>');
       	}
       	else{
	        alert(response.message);
	        
	        if(response.errors)
	        {
            var list = ``;
            $.each(response.errors, function(index, value){
                list += `<li>${value}</li>`;
            });

            var html = `
                <div class="alert alert-danger alert-dismissible">
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  <ul>${list}</ul>
                </div>
            `;
            $('#logo_file_validation').html(html);
	        }  
        }
      },
      complete: function() {
        stop_loader();
        $('#logo_form_btn').attr('disabled', false);
      },

		});
	});
</script>	 
<script>
<?php if(isset($ro_country) && $ro_country!=''){ ?>
window.ini = load_ro_states('<?php echo $ro_country;?>','<?php echo $ro_state;?>'),load_co_states('<?php echo $co_country;?>','<?php echo $co_state;?>');

<?php } else { ?>
window.ini = load_ro_states('1','0');
<?php } ?>
function load_ro_states(country_id,state_id){
 $(".ro_state_div").html("Loading...");	
 $.get(baseurl+'home/ajax_states_list/'+country_id+'/'+state_id, 
      function (data) {  
         $(".ro_state_div").html(data);	
		 $("#state_id").attr("name","ro_state");
		 $("#state_id").attr("id","ro_state");  
     });	
 
//load_co_states(country_id,state_id); 
	 
}

function load_co_states(country_id,state_id){
 $(".co_state_div").html("Loading...");	
 $.get(baseurl+'home/ajax_states_list/'+country_id+'/'+state_id, 
      function (data) {  
         $(".co_state_div").html(data);		
          $("#state_id").attr("name","co_state");
		 $("#state_id").attr("id","co_state"); 		 
     });	
}
 
$("#ro_country").on("change",function(){
var country_id = $(this).val();	
 $(".ro_state_div").html("Loading...");	
 $.get(baseurl+'home/ajax_states_list/'+country_id+'/0', 
      function (data) {  
         $(".ro_state_div").html(data);
		 $("#state_id").attr("name","ro_state");
		 $("#state_id").attr("id","ro_state");
     });	
})
$("#co_country").on("change",function(){
var country_id = $(this).val();	
 $(".co_state_div").html("Loading...");	
 $.get(baseurl+'home/ajax_states_list/'+country_id+'/0', 
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