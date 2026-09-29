<?php $header = array( 	'title' => 'Update Company' ); ?>
<?php echo view('includes/header',$header); ?>
<div class="main-wrapper">
<?php echo view('includes/'.$folder_path.'inner_header'); ?>
<div class="page-wrapper page-wrapper-one">
 <div class="content">
   <div class="page-header">
      <div class="page-title">
         <h4>Company Management</h4>
         <h6>Add/Update Company</h6>
      </div>	 
   </div>
   <div class="card">
      <div class="card-body">
	  <?php $attributes = " id='form1' name='form1' class='needs-validation' novalidate";
             echo form_open(base_url().'/company/edit/'.$company_id.'', $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
<div class="row">
 <div class="col-lg-6">
 	<div class="row">
	<div class="page-header">
			<div class="page-title">
         <h4>Basic Details</h4>
		 </div> </div>
		    <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Name </label>
				  <?php $data = array(
									  'name'        => 'comnpany_name',
									  'id'          => 'comnpany_name',
									  'value'       => $enc_string->nc_string($company_info['comp_name'],'de'),
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?>									                    				  
               </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Print Name</label>
				 <?php $data = array(
									  'name'        => 'print_name',
									  'id'          => 'print_name',
									  'value'       => $enc_string->nc_string($company_info['comp_print_name'],'de'),
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?>
               </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Short Name </label>
				  <?php $data = array(
									  'name'        => 'short_name',
									  'id'          => 'short_name',
									  'value'       => $enc_string->nc_string($company_info['comp_short_name'],'de'),
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true									   
									  );
									  echo form_input($data);
									  ?>
               </div>
            </div>
		
			<div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Industry Type </label>
                   <?php	
                   $industry_type_list = industry_type_list();
				   echo form_dropdown('industry_id', $industry_type_list, $company_info['comp_industry'],'id="industry_id" class="form-control select"  ');
						?>
				   </div>
            </div>
			<div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Nature Of Work </label>
                  <?php	
                   $natureof_work_lists = natureof_work_lists();
				   echo form_dropdown('nature_ofwork', $natureof_work_lists, $company_info['comp_work_nature'],'id="nature_ofwork" class="form-control select"  ');
						?>
				   </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>F.Y Beginning From </label>
                  <?php $data = array(
									  'name'        => 'fybegin_date',
									  'id'          => 'fybegin_date',
									  'value'       => $enc_string->nc_string($company_info['fy_begndt'],'de'),
									  'maxlength'   => '255',
									   'class'      => 'form-control',
									   'type'       =>  'date'
									  );
									  echo form_input($data);
									  ?>
				   </div>
            </div>
           <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>CIN </label>                 
				  <?php $data = array(
									  'name'        => 'comp_cin',
									  'id'          => 'comp_cin',
									  'value'       => $enc_string->nc_string($company_info['cin'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
				   </div>
            </div>
			
		  </div>
	
 
 </div>


<div class="col-lg-6">
 	<div class="row">
	<div class="page-header">
			<div class="page-title">
         <h4>Registered Office</h4>
		 </div> </div>
		  <div class="row">
		 <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Address1 </label>                 
				  <?php $data = array(
									  'name'        =>  'ro_add1',									 
									  'value'       =>  $enc_string->nc_string($company_info['ro_add1'],'de'),
									  'maxlength'   =>  '32',
									   'class'      =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
				   </div>
				 
				   
            </div> 
		 <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Address2 </label>                 
				  <?php $data = array(
									  'name'        => 'ro_add2',									 
									  'value'       => $enc_string->nc_string($company_info['ro_add2'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
				   </div>
				 
				   
            </div> 
		 <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Country </label>                 
				  <?php		echo form_dropdown('ro_country', $CountryDropdown, $company_info['ro_country'],'id="ro_country" class="form-control" ');
						?>
				   </div>
				 
				   
            </div> 
		 <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>State </label>                 
				    <?php		echo form_dropdown('ro_state', $StatesDropdown, '25','id="ro_state" class="form-control" ');
						?>
				   </div>
				 
				   
            </div>
			<div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>City </label>                 
				  <?php $data = array(
									  'name'        => 'ro_city',
									  'value'       => $enc_string->nc_string($company_info['ro_city'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
				   </div>
				 
				   
            </div> 	
			<div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Pin </label>                 
				  <?php $data = array(
									  'name'        => 'ro_pin	',
									  'value'       => $enc_string->nc_string($company_info['ro_pin'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
				   </div>
				 
				   
            </div> 	
			
			</div>
			
			
			
	<div class="page-header">
			<div class="page-title">
         <h4>Corporate Office</h4>
		 </div> </div>
		   <div class="row">
		 <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Address1 </label>                 
				  <?php $data = array(
									  'name'        =>  'co_add1',									 
									  'value'       =>  $enc_string->nc_string($company_info['co_add1'],'de'),
									  'maxlength'   =>  '32',
									   'class'      =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
				   </div>
				 
				   
            </div> 
		 <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Address2 </label>                 
				  <?php $data = array(
									  'name'        => 'co_add2',									 
									  'value'       => $enc_string->nc_string($company_info['co_add2'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
				   </div>
				 
				   
            </div> 
		 <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Country </label>                 
				  <?php		echo form_dropdown('co_country', $CountryDropdown, '1','id="co_country" class="form-control" ');
						?>
				   </div>
				 
				   
            </div> 
		 <div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>State </label>                 
				    <?php		echo form_dropdown('co_state', $StatesDropdown, '25','id="co_state" class="form-control" ');
						?>
				   </div>
				 
				   
            </div>
			<div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>City </label>                 
				  <?php $data = array(
									  'name'        => 'co_city',
									  'value'       => $enc_string->nc_string($company_info['co_city'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
				   </div>
				 
				   
            </div> 	
			<div class="col-lg-3 col-sm-6 col-12">
               <div class="form-group">
                  <label>Pin </label>                 
				  <?php $data = array(
									  'name'        => 'co_pin',
									  'value'       => $enc_string->nc_string($company_info['co_pin'],'de'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?>
				   </div>
				 
				   
            </div> 	
			
			</div>
		
	
          
				   
            </div> 
  </div>
</div>  
 <div class="col-lg-12">
               <button type="submit" class="btn btn-submit me-2">Submit</button>
               <a href="<?php echo $base_url_path;?>company" class="btn btn-cancel">Cancel</a>
            </div>
</div>        
 <?php echo form_close(); ?>
      </div>
   </div>
</div>
</div>
</div>

<?php echo view('includes/footer_scripts'); ?>	 
<script>

var imgrow_counter=2;
$("#clone_accountants").on('click',	function() {
	var result = $(".cloned-row-accountant").first().clone();
		$(".cloned-row-accountant").last().after(result);	
		$(".cloned-row-accountant").last().attr("id","acttr"+imgrow_counter);	
		var btnvalue='<img src="<?php echo base_url();?>/public/assets/img/icons/delete.svg" style="margin-top:35px;" class="remove_accountant" alt="img" data-id="'+imgrow_counter+'">';
		$(".cloned-row-accountant div").last().replaceWith( btnvalue )
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
$("#clone_cas").on('click',	function() {
	var result = $(".cloned-row-ca").first().clone();
		$(".cloned-row-ca").last().after(result);	
		$(".cloned-row-ca").last().attr("id","catr"+imgrow_counter1);	
		var btnvalue='<img src="<?php echo base_url();?>/public/assets/img/icons/delete.svg" style="margin-top:35px;" class="remove_ca" alt="img" data-id="'+imgrow_counter1+'">';
		$(".cloned-row-ca div").last().replaceWith( btnvalue )
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
		var btnvalue='<img src="<?php echo base_url();?>/public/assets/img/icons/delete.svg" style="margin-top:35px;" class="remove_currency" alt="img" data-id="'+imgrow_counter2+'">';
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
<?php //echo view('includes/footer'); ?>
