<?php $header = array( 	'title' => 'Add Company' ); ?>
<?php echo view('includes/header2',$header); ?>
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

    <div class="content"><div class="pb-5">
  
          <?php $attributes = " id='form1' name='form1' class='needs-validation myform' autocomplete='off' novalidate";
             echo form_open(base_url().'home/add_company', $attributes);
        ?>
			
			<div class="row">                
                <div class="col-6"><h3 class="pb-3">Add A Company</h3></div>  <div class="col-6"><span class="float-end"></span></div>  
                                
               <div class="col-md-6">
			   <div class="row m-0">
                   
                 <div class="card col-12 pt-3 my-3">  
				  <h5 class="py-2">&nbsp;</h5>
               <p><label>Company Name <span class="red">*</span></label> <?php $data = array(
									  'name'        => 'comnpany_name',
									  'id'          => 'comnpany_name',
									  'value'       => set_value('comnpany_name'),
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></p>
               <p><label>Print Name <span class="red">*</span></label> <?php $data = array(
									  'name'        => 'print_name',
									  'id'          => 'print_name',
									  'value'       => set_value('print_name'),
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?></p>
               <p><label>Short Name <span class="red">*</span></label> <?php $data = array(
									  'name'        => 'short_name',
									  'id'          => 'short_name',
									  'value'       => set_value('short_name'),
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true									   
									  );
									  echo form_input($data);
									  ?></p>
             

									  <div class="col-sm-12">
									  	<label>Fy. Year</label> 
									  	<div class="w-75 d-flex">

									  		<input type="text" name="fybegin_date" class="form-control fy_datepicker required" placeholder="Begining Date" value="<?= $fin_year_list['start_date'] ?>">
&nbsp;&nbsp;
									  		<input id="end_date" type="text" name="fyend_date" class="form-control" placeholder="End Date" value="<?= $fin_year_list['end_date'] ?>" readonly>

									  	</div>
									  </div>
             </div>


            <div class="card p-3 my-3">
                <h5 class="py-2">General Info</h5>
				 <div class="col-sm-12">
                	<label for="">Default Stock Valuation</label>
                	<select name="valmethod_id" id="valmethod_id" class="form-control required" required="">
						<option value="AVG" selected="selected">AVG COST</option>
						<option value="FIFO">FIFO</option>
						<option value="LIFO">LIFO</option>
					</select>				
                </div>
                <div class="col-sm-12">
                	<label for="">Industry Type</label>
                	<select name="comp_industry" id="" class="form-control">
                		<option value=""></option>
                		<?php foreach ($industry_type_list as $key => $value) { ?>
                			<option value="<?= $value['industry_type_id'] ?>"><?= $value['industry_type'] ?></option>
                		<?php } ?>
                	</select>
                </div>

                <div class="col-sm-12">
                	<label for="">Nature of Work</label>
                	<select name="comp_work_nature" id="" class="form-control">
                		<option value=""></option>
                		<?php foreach ($natureof_work_list as $key => $value) { ?>
                			<option value="<?= $value['work_nature_id'] ?>"><?= $value['work_nature'] ?></option>
                		<?php } ?>
                	</select>
                </div>
             </div>
             
             
             
              
             
            
             
             
              </div>
			  </div> 
			  
			  <div class="col-md-6">
			   <div class="row m-0">
			  <div class="card p-3 my-3">
                 <h5 class="py-2">Ro. Address</h5>
               <div class="col-sm-12"><label>Address line 1</label> <?php $data = array(
									  'name'        =>  'ro_add1',									 
									  'value'       =>  set_value('ro_add1'),
									  'maxlength'   =>  '32',
									   'class'      =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>  
               <div class="col-sm-12"><label>Address line 2</label> <?php $data = array(
									  'name'        => 'ro_add2',									 
									  'value'       => set_value('ro_add2'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-sm-12"><label>Country/State <span class="red">*</span></label> <div class="w-75 d-flex">
                <?php		echo form_dropdown('ro_country', $CountryDropdown, '1','id="ro_country" class="form-select w-50" ');
						?> &nbsp;&nbsp; <div class="ro_state_div w-50">
                  <?php echo form_dropdown('ro_state', array(''=>'Choose'), '','id="ro_state" class="form-select w-100" '); ?>
						</div></div></div>
               <div class="col-sm-12"><label>City/Pin <span class="red">*</span></label>
			   <div class="input-group w-75">
			   <?php $data = array(
									  'name'        => 'ro_city',
									  'value'       => set_value('ro_city'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control w-50',
									   'placeholder' => 'City',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?>&nbsp;&nbsp;
				<?php $data = array(
									  'name'        => 'ro_pin',
									  'value'       => set_value('ro_pin'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control w-50',
									   'placeholder' => 'Pin code',
									   'required' => true
									  );
									  echo form_input($data);
									  ?></div></div>
               
             </div>
             
              <div class="card p-3 my-3">
                 <h5 class="py-2">Co. Address</h5>
              <div class="col-sm-12"><label>Address line 1</label><?php $data = array(
									  'name'        =>  'co_add1',									 
									  'value'       =>  set_value('co_add1'),
									  'maxlength'   =>  '32',
									   'class'      =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>  
               <div class="col-sm-12"><label>Address line 2</label> <?php $data = array(
									  'name'        => 'co_add2',									 
									  'value'       => set_value('co_add2'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control'
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-sm-12"><label>Country/State <span class="red">*</span></label> <div class="w-75 d-flex">
              <?php		echo form_dropdown('co_country', $CountryDropdown, '1','id="co_country" class="form-select w-50" ');
						?>&nbsp;&nbsp; <div class="co_state_div  w-50">
                  <?php echo form_dropdown('co_state', array(''=>'Choose'), '','id="co_state" class="form-select w-100" '); ?>
						</div></div></div>
               <div class="col-sm-12"><label>City/Pin <span class="red">*</span></label> <div class="input-group w-75">
			   <?php $data = array(
									  'name'        => 'co_city',
									  'value'       => set_value('co_city'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control w-50',
									  'placeholder' => 'City',
									  'required'=>true
									  );
									  echo form_input($data);
									  ?>
				<?php $data = array(
									  'name'        => 'co_pin',
									  'value'       => set_value('co_pin'),
									  'maxlength'   => '32',
									   'class'       =>  'form-control w-50',
									   'placeholder' => 'Pin code',
									   'required'=>true
									  );
						echo form_input($data);
					?></div></div>
               
             </div>
			   </div>
			   </div>  
               
                
              </div>

                 <div class="col-sm-12 text-center"><input id="submitbtn" type="submit" value="SAVE" class="btn btn-primary mx-2">
                 <a href="<?php echo base_url();?>my_companies" class="btn btn-secondary mx-2" >QUIT</a>
              </div>
            
            
              </div> </form>

<?php echo view('includes/footer_scripts'); ?>	 

<script>
form1.onsubmit = async (e) => {
	e.preventDefault();

	if ($("#form1")[0].checkValidity() === true) {
		$('#progressbar').show();
		$(".siteloader").show();
		let start_time = performance.now();

		let progressInterval = setInterval(() => {
			let currenttime = performance.now();
			let pct = ((currenttime - start_time) / currenttime * 100).toFixed(2);
			$('#progressbar').css("width", pct + "%");
			$('#progressbar .progress-bar').css("width", pct + "%").text(pct + "%");
			$('#progressbar').attr('aria-valuenow', pct);
		}, 1000);

		$('body').css('pointer-events', 'none');
		document.querySelector("body").style.setProperty('opacity', '0.7', 'important');

		let formData = new FormData(form1);
		// Fire main API call
		let mainApiPromise = fetch('<?php echo base_url(); ?>home/add_company', {
			method: 'POST',
			body: formData
		}).then(res => res.json());

		// Process main API result
		try {
			let result = await mainApiPromise;

			clearInterval(progressInterval);
			
			$('#progressbar').css("width", "100%");
			$('#progressbar .progress-bar').css("width", "100%").text("100%");

			if (result.status == 1) {
				alert_success(result.message);
				 window.location.href = '<?php echo base_url(); ?>companies';
			} else {
				throw new Error(result.message);
			}
		} catch (err) {
			alert_notification(err.message);
			clearInterval(progressInterval);			
			$('#progressbar, #progressbar .progress-bar').css("width", "").text("");
			stop_loader();
			$('#progressbar').hide();
			$('body').css('pointer-events', 'auto');
			document.querySelector("body").style.removeProperty('opacity');
		}
	}
};

</script>


<script>

	// (2 DIGIT NUMERIC + 5 CHARACTERS + 4 NUMERIC + 3 ALPHANUMERIC) 
	$(".gstinMask").inputmask('Regex', { 
    regex: "^[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z0-9]{3}$",
    casing:'upper'
	});

	$(".fy_datepicker").inputmask("99/99/9999", {
      mask: "99-99-9999",
      alias: "date",
      placeholder: "DD-MM-YYYY",
      insertMode: false,
  });

  $('.fy_datepicker').datepicker({
      altFormat: "dd-mm-yyyy",
      dateFormat: "dd-mm-yy",
      changeMonth: true,
      changeYear: true,
      minDate:'01-01-2010',
   

  }).on("change", function () {
      var date = $(this).val();
      if(!isValidDate(date)){
        $(this).val("");
        return false;
      }

      var arr = date.split('-');
      
      var day 	= parseInt(arr[0]);
      var month = parseInt(arr[1]);
      var year 	= parseInt(arr[2]);

      if(day == 1){
      	if(month == 1){
	      	var end_year = year;
	      	var end_month = 12;
	      	var end_day = 31;
	      }
	      else{
	      	var end_year = year + 1;
	      	var end_month = month - 1;
	      	var end_day = new Date(end_year, end_month, 0).getDate();
	      }
      }
	  	else{
	  		var end_year = year + 1;
	  		var end_month = month;
      	var end_day = day - 1;
	  	}
      
	  	var end_month = end_month < 10 ? '0'+end_month : end_month;
	  	var end_day = end_day < 10 ? '0'+end_day : end_day;
	  	var end_date = end_day + '-' + end_month + '-' + end_year;
      $('#end_date').val(end_date);

  });



window.ini = load_ro_states('1','18');
function load_ro_states(country_id,state_id){
 $(".ro_state_div").html("Loading...");	
 $.get(baseurl+'home/ajax_states_list/'+country_id+'/'+state_id, 
      function (data) {  
         $(".ro_state_div").html(data);	
		 $("#state_id").attr("name","ro_state");
		 $("#state_id").attr("id","ro_state");  
     });	
 
load_co_states(country_id,state_id); 
	 
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
	
	$(document).on('blur','[name="comnpany_name"]', function(){
    var name = $(this).val().trim();
    if(name){
        if(!$('[name="print_name"]').val().trim())
        {
           $('[name="print_name"]').val(name); 
        }
        if(!$('[name="short_name"]').val().trim())
        {
            var short = name.replace(/ .*/,''); //first word
            if(short.length < 3){
                var count = name.trim().split(/\s+/).length;
                if(count > 1){
                   short = name.split(' ').slice(0,2).join(' '); //first two words
                } 
            }
            $('[name="short_name"]').val(short);
        }
    }
});
</script>














