<?php $header = array( 	'title' => 'Add Bill Sundry' ); ?>
<?php echo view('includes/header',$header); ?>
<style> 
 .myform .col-sm-6{padding-bottom:2px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
    .myform .select2 {width:75%!important; }
</style>	 
    <div id="validation_errors"></div> 
      <?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().'/'.$folder_path.'billsundry/add', $attributes);
       ?>
       <div class=" row">
             <div class="col-md-6 pb-3"><h3 class="pb-3">Add Bill Sundary</h3></div>  <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></div> 
       </div>    
       <div class="row"><div class="col-md-12" id="labelhint">&nbsp;</div></div>
        <div class=" row">
               <div class="col-md-6">
                <div class="card p-4 my-2">  
               <p class="d-flex"><label class="w-25">Name <span class="red">*</span></label> <input type="text" name="billsndry_name" id="billsndry_name"  class="form-control w-75" required></p>
               <p class="d-flex"><label class="w-25">Allias <span class="red">*</span></label> <input type="text" name="billsndry_allias" id="billsndry_allias" class="form-control w-75" required ></p>
               <p class="d-flex"><label class="w-25">Print Name <span class="red">*</span></label> <input type="text" name="billsndry_pname" id="billsndry_pname" class="form-control w-75" required ></p>
                </div>

                <div class="card p-4 my-2">
                    <div class="col-12 my-1">
                        <label>Primary <span class="red">*</span></label>
                        <div class="input-group w-75">
                            <div class="input-group-text py-1">
                                <input type="radio" name="account_primary" value="Y" id="primary_yes" class="form-check-input" checked>
                                &nbsp;<label for="primary_yes">Yes</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="account_primary" value="N" id="primary_no" class="form-check-input" >
                                &nbsp;<label for="primary_no">No</label>
                            </div> 
                        </div>
                    </div>

                    <div class="col-12 my-1" id="group_div" style="display: none;">
                        <label>Group</label>
                        <?php echo form_dropdown('sundry_group', $group_main_dropdown, set_value('sundry_group'),'id="sundry_group" class="form-control select2" '); ?>
                    </div>

                    <div class="col-12 my-1" id="parent_div">
                        <label>Parent Group</label>
                        <?php echo form_dropdown('parent_group', $group_primary_dropdown, set_value('parent_group'),'id="parent_group" class="form-control select2" required="true" '); ?>
                    </div>

                </div>			
				
              </div>
              <div class="col-md-6">
                <div class="card p-4 my-2">
              
				<p class="d-flex"><label class="w-25">Tax Account</label>
				<label class="radio-inline">
				  <input type="radio" name="bsd_type" id="bsd_type1" value="1" checked> Yes
				</label>
				<label class="radio-inline">
				  <input type="radio" name="bsd_type" id="bsd_type0" value="0" > No
				</label>
	
					</p>		
               <p class="d-flex">
                <span class="w-75" id="billsundarynature_yes_div" style="display:none;" >
				</span>
				<span class="w-100" id="billsundarynature_no_div" style="display:none;">	
				<label class="w-40">Bill Sundary Nature</label>
				<?php	
			       
                    echo form_dropdown('billsundarynature_no', $billsundry_nature_no, set_value('billsundarynature_no'), 'id="billsundarynature_no" class="form-control w-75 " ');

				?>	</span>	
					</p>
				<span id="supply_type_div" style="display:none;">
				<p class="d-flex"><label class="w-25">Supply Type</label>
                <?php
					$supplytype_ar = array(''=>'Choose','1'=>'Goods','2'=>'Services','3'=>'Capital Goods');				
				   echo form_dropdown('bill_supply_type', $supplytype_ar, "0",'id="bill_supply_type" class="form-control w-75"');
						?>
					</p>
					</span>
				<span id="supplylabel_type_div" style="display:none;">
				<p class="d-flex"><label class="w-25" id="supplylabel_type"></label>
                <input type="text" name="bill_hsn_sac" id="bill_hsn_sac"  class="form-control w-75">
			    </p>
			    </span>
				<span id="calbase_div" style="display:none;">	
				<p class="d-flex"><label class="w-25">Bill Sundary Cal Base</label>
                <?php
					$sundry_calc_base = array(1=>'Perc (%)',2=>'Absolute Amount');				
                   echo form_dropdown('sundry_calc_base', $sundry_calc_base, set_value('sundry_calc_base'),'id="sundry_calc_base" class="form-control w-75" ');
						?>
					</p>				
					</span>
				<span id="iotypes_div">
				<p class="d-flex"><label class="w-25">IO Types</label>
                <?php
					$supplytype_ar = array('1'=>'Input','2'=>'Output');				
                   echo form_dropdown('bill_io_type', $supplytype_ar, 1,'id="bill_io_type" class="form-control w-75" ');
						?>
					</p>	</span>
					
					<span id="taxcatgtypes_div">
				<p class="d-flex"><label class="w-25">Tax Catgory Types</label>
                <?php
                   echo form_dropdown('cat_type', $catTypes, '','id="cat_type" class="form-control w-75" required');
						?>
					</p>	</span>	
			
				<span id="taxsubcatgtypes_div">
				<p class="d-flex"><label class="w-25">Sub Type</label>
                <?php
					$sub_type_ar = array();				
                   echo form_dropdown('sub_type', $sub_type_ar,'','id="sub_type" class="form-control w-75" required');
						?>
					</p>	</span>		
			  </span>
			   </div>
			    <div class="card p-4 my-2" id="taxcatg_div" style="display:none;">
				      <h5 class="pb-3">GST Details</h5>
				 <div class="col-12"><label>GST TaxPayer Category <span class="red">*</span></label> 
				 <?php
					 echo form_dropdown('tax_cat_mst_id', $tax_category,"",'id="tax_cat_mst_id" class="form-control w-75 required" required');
				   ?>
				 </div>
              
			</div>
				
             </div>
			
            </div>
 
        <div class="row">
	 <div class="col-md-6">
            <div class="card p-4 my-2">
                      
                <h5 class="pb-2">Pay Details</h5>     
                        
                
               <div class="col-12">
                    <label>Op. Bal</label> 
                    <div class="input-group w-75">
                        <?php 
                            $data = array(
                                'name'      => 'bsd_op_bal', 
                                'id'        => 'bsd_op_bal',
                                'value'     => '0.00',
                                'maxlength' => '100',
                                'class'     => 'form-control'
                            );
                            echo form_input($data);
                        ?>
                        <span class="input-group-text">
                            <input type="radio" name="bsd_op_bal_drcr" value="cr" class="form-check-input">
                            &nbsp;Cr. &nbsp;&nbsp;&nbsp;

                            <input type="radio" name="bsd_op_bal_drcr" value="dr" class="form-check-input" checked>
                            &nbsp;Dr.
                        </span>
                    </div>
                </div>
               <div class="col-12">
                    <label>P.Y. Bal</label> 
                    <div class="input-group w-75">
                        <?php 
                            $data = array(
                                'name'        => 'bsd_py_bal',
                                'id'          => 'bsd_py_bal',
                                'value'       => '0.00',
                                'maxlength'   => '100',
                                'class'       => 'form-control'
                            );
                            echo form_input($data);
                        ?>
                        <span class="input-group-text">
                            <input type="radio" name="bsd_py_bal_drcr" value="cr" class="form-check-input">
                            &nbsp;Cr. &nbsp;&nbsp;&nbsp;

                            <input type="radio" name="bsd_py_bal_drcr" value="dr" class="form-check-input" checked>
                            &nbsp;Dr.
                        </span> 
                    </div>
                </div>
                <div class="col-12"><label>MEMO OP. Bal</label> <div class="input-group w-75"><?php $data = array(
									  'name'        => 'memo_opp_bal',
									  'id'          => 'memo_opp_bal',
									  'value'       => '0.00',
									  'maxlength'   => '100',
									   'class'       => 'form-control'									   
									  );
									  echo form_input($data);
									  ?>
                    <span class="input-group-text"><input type="radio" name="memo_opn_dr_cr" value="cr" class="form-check-input">&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="memo_opn_dr_cr" value="dr" class="form-check-input" checked>&nbsp;Dr.</span> </div>
					</div>
            </div>
        </div>
        </div>
       



				 <div class="col-12 text-center">
                   <input type="submit" value="SAVE" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">
                   <a href="<?php echo base_url().'/'.$folder_path;?>accounts/list" class="btn btn-secondary mx-2">QUIT</a>
                </div>
        </form>       
    
        </div>
	
				
     
<?php echo view('includes/footer_scripts'); ?>
<script>

const subTypes = {
  1: { // GST Taxpayer
    1: "IGST",
    2: "CGST",
    3: "SGST",
    4: "UT GST",
    5: "Cess (GST)"
  },
  2: { // TDS (GST)
    1: "TDS IGST",
    2: "TDS CGST",
    3: "TDS SGST",
    4: "TDS UT GST"
  },
  3: { // TCS (GST)
    1: "TCS IGST",
    2: "TCS CGST",
    3: "TCS SGST",
    4: "TCS UT GST"
  },
  4: { // TDS (IT)
    1: "TDS"
  },
  5: { // TCS (IT)
    1: "TCS"
  },
  99: { // Other Taxes
    0: "Other Taxes"
  }
};

$("#submitbtn").on("click",function(){
	$("#myform").submit();
	
});
$(document).ready(function() {
  $('#cat_type').on('change', function() {
    let catType = $(this).val();
    let $subType = $('#sub_type');

    // clear existing
    $subType.empty().append('<option value="">-- Select Sub Type --</option>');

    if (catType && subTypes[catType]) {
      $.each(subTypes[catType], function(val, label) {
        $subType.append($('<option>', {
          value: val,
          text: label
        }));
      });

      // auto-select if only one option exists (like Other Taxes)
      if (Object.keys(subTypes[catType]).length === 1) {
        $subType.prop('selectedIndex', 1); // select the only option
      }
    }
  });
});

$(".datepickerT").datepicker();

var showlabel_message='<span style="color:red;">ONCE THE TAX CATEGORY IS MAPPED, CAN\'T BE ALTERED IN FUTURE. YOU NEED TO EITHER DELETE OR DEACTIVE THE BILLSUNDRY</span>';
$(document).on('change','[name="item_tax"]', function(){
   
  var bsdTypeVal = $("input[name='bsd_type']:checked").val();
  if(bsdTypeVal==1){
      var showlabel = showlabel_message
  }
    else
    var showlabel ='';
    $("#labelhint").html(showlabel);
    
});




$(document).on('change','[name="bill_supply_type"]', function(){
	
	$("#supply_type_div").show();
	$("#supplylabel_type_div").show();
	$("#supplylabel_type").show();
	
	if($(this).val()=='1'){
		$("#supplylabel_type").html("HSN");
	}
	else if($(this).val()=='2'){
		$("#supplylabel_type").html("SAC");
	}
	else if($(this).val()=='3'){
		$("#supplylabel_type").html("HSN / SAC");
	}else
		$("#supplylabel_type").html("HSN");
	
});

$(document).ready(function(){
    
    $("input[name='bsd_type'][value='1']"). trigger('change');
    
});

$("input[name='bsd_type']").change(function(){
   $("#labelhint").html("");
   
   
	$("select[name=billsundarynature_no]").val("");
	var radvalue = '0';
	if($(this).val()=='1'){	// Means Tax Account Yes
	   
	   if($("#item_tax").val()!=''){
	    
	    $("#labelhint").html(showlabel_message);
	   }
	   else
	     $("#labelhint").html("");
	    $("select[name=item_tax]").removeClass("required");	
		 $("select[name=item_tax]").removeAttr("required");	
		
	
        $("select[name=billsundarynature_no]").removeClass("required");		
		 $("select[name=billsundarynature_no]").removeAttr("required");
		 
		 $("select[name=bill_supply_type]").removeAttr("required");
		  $("select[name=bill_supply_type]").removeClass("required");	
		
	    $("#taxcatg_div").hide();
	    $("#calbase_div").hide();
		$("#supplytype_div").hide();	
	    $("#billsundarynature_yes_div").show();
		$("#billsundarynature_no_div").hide();
		$("input[name=fed][value=" + radvalue + "]").attr("disabled",true);			
			
		$("#bill_supply_type").val('');
		$("#supplylabel_type_div").hide();
		$("#supply_type_div").hide();
		$("#bill_hsn_sac").val('');	
		$("#iotypes_div").show();	
		
		$('#taxcatgtypes_div').show();
	    $('#taxsubcatgtypes_div').show();
	    
	     $("select[name=cat_type]").attr("required",true);
		   $("select[name=sub_type]").attr("required",true);
	}else{
	    
	    $('#taxcatgtypes_div').hide();
	    $('#taxsubcatgtypes_div').hide();
	    
	     $("select[name=cat_type]").attr("required",false);
		   $("select[name=sub_type]").attr("required",false);
		  
	    
	    
	    $("select[name=bill_supply_type]").attr("required",true);
		  $("select[name=bill_supply_type]").addClass("required");
		  
	    $("#calbase_div").show();
	    $("#supply_type_div").show();
		$("select[name=item_tax]").addClass("required");	
		$("select[name=billsundarynature_yes]").removeClass("required");	
        $("select[name=billsundarynature_no]").removeClass("required");		
		
		
		$("select[name=billsundarynature_no]").addClass("required");		
		$("select[name=billsundarynature_no]").attr("required",true);


		$("#iotypes_div").hide();				
		$("#supplytype_div").hide();	
		$("#taxcatg_div").show();		
		$("#item_tax").val("");
		$("#billsundarynature_yes_div").hide();
		$("#billsundarynature_no_div").show();
		$("input[name=fed][value=" + radvalue + "]").attr("disabled",false);	
	   } 
});

    $(document).on('blur','[name="billsndry_name"]', function(){
        var name = $(this).val().trim();
        if(name){
            if(!$('[name="billsndry_allias"]').val().trim())
            {
               $('[name="billsndry_allias"]').val(name); 
            }
            if(!$('[name="billsndry_pname"]').val().trim())
            {
               $('[name="billsndry_pname"]').val(name); 
            }
        }
    });
    $('input[type=radio][name="account_primary"]').change(function() {
        if (this.value == 'Y'){
            $('#group_div').css('display', 'none');
            $('#sundry_group').attr('required', false);
            $('#parent_div').css('display', 'block');
            $('#parent_group').val('');
            $('#parent_group').attr('required', true);
        }
        else if(this.value == 'N'){
            $('#parent_div').css('display', 'none');
            $('#parent_group').attr('required', false); 
            $('#group_div').css('display', 'block');
            $('#sundry_group').val('');
            $('#sundry_group').attr('required', true);
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
                    <?php if(isset($_GET['p']) && $_GET['p'] == 1){ ?>
                        window.history.back();
                    <?php } else { ?>
                        window.location.reload();
                    <?php } ?>
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
								<strong>Error!</strong>  <ul>${list}</ul>
								</div>
								<button type="button" class="btn-close" aria-label="Close"></button>
							</div>
	                        `;
							$('#validation_errors').show();
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