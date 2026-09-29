<?php $header = array( 	'title' => 'Modify Bill Sundry' ); ?>
<?php echo view('includes/header',$header);

$acc_is_restrict=0;
$isfreezed='';
$taxpayer_category_requird='required';
if($billsundry_info['acc_is_restrict'])
	$acc_is_restrict=$billsundry_info['acc_is_restrict'];
if($acc_is_restrict==1){
	$isfreezed ='readonly';
$taxpayer_category_requird='';
}

?>
<style> 
 .myform .col-sm-6{padding-bottom:2px;}
 .myform label{width:25%; float:left;}
 .myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
 .myform .select2 {width:75%!important; }
</style>	 
    <div id="validation_errors"></div> 
      <?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().$folder_path.'billsundry/modify/'.$billsundry_id, $attributes);
       ?>
       <div class=" row">
             <div class="col-md-6 pb-3"><h3 class="pb-3">Modify Bill Sundary</h3></div>  <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></div> 
       </div>   
       <div class="row"><div class="col-md-12" id="labelhint">&nbsp;</div></div>
        <div class=" row">
               <div class="col-md-6">
                <div class="card p-4 my-2">  
               <p class="d-flex"><label class="w-25">Name <span class="red">*</span></label> <input type="text" name="billsndry_name" id="billsndry_name"  class="form-control w-75 restrict-field" value="<?php echo $billsundry_info['acc_name'];?>" <?php echo $isfreezed;?> required></p>
               <p class="d-flex"><label class="w-25">Allias <span class="red">*</span></label> <input type="text" name="billsndry_allias" id="billsndry_allias" class="form-control w-75 restrict-field" value="<?php echo $billsundry_info['acc_alias'];?>" <?php echo $isfreezed;?> required ></p>
               <p class="d-flex"><label class="w-25">Print Name <span class="red">*</span></label> <input type="text" name="billsndry_pname" id="billsndry_pname" class="form-control w-75 restrict-field" value="<?php echo $billsundry_info['acc_print_name'];?>" <?php echo $isfreezed;?> required ></p>
                </div>

                <div class="card p-4 my-2">
                    <div class="col-12 my-1">
                        <label>Primary <span class="red">*</span></label>
                        <div class="input-group w-75">
                            <div class="input-group-text py-1">
                                <input type="radio" name="account_primary" value="Y" id="primary_yes" class="form-check-input restrict-field" <?= $billsundry_info['crs_mst_parent_id'] != 0 ? 'checked' : '' ?>>
                                &nbsp;<label for="primary_yes">Yes</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="account_primary" value="N" id="primary_no" class="form-check-input restrict-field" <?= $billsundry_info['under_crs_mst_id'] != 0 ? 'checked' : '' ?>>
                                &nbsp;<label for="primary_no">No</label>
                            </div> 
                        </div>
                    </div>

                    <div class="col-12 my-1" id="group_div" <?php echo ($billsundry_info['under_crs_mst_id'] == 0) ? 'style="display: none;"' : ''; ?>>
                        <label>Group</label>
                        <select name="sundry_group" id="sundry_group" class="form-control select2 restrict-field" <?php echo (isset($crsmaster_info) && $crsmaster_info["under_crs_mst_id"] != 0 ? "required" : "");?>>
						<option value="">Choose</option>
						<?php foreach($group_main_droplist as $grprow){ ?>
						<option data-id="<?php echo $grprow['acc_grp_parent_id'];?>" value="<?php echo $grprow['acc_grp_id'];?>" <?php echo (isset($crsmaster_info) && $crsmaster_info['under_crs_mst_id']==$grprow['acc_grp_id'])?"selected":"";?>><?php echo $grprow['acc_grp_name'];?></option>
						<?php } ?>
						</select>
                    </div>

                    <div class="col-12 my-1" id="parent_div" <?php echo ($billsundry_info['under_crs_mst_id']!=0) ? 'style="display: none;"':''; ?>>
                        <label>Parent Group</label>
                        <?php echo form_dropdown('parent_group', $group_primary_dropdown, ($crsmaster_info!='')?$crsmaster_info['crs_mst_parent_id']:'','id="parent_group" class="form-control select2 restrict-field" '.(isset($crsmaster_info) && $crsmaster_info["crs_mst_parent_id"] != 0 ? "required" : "").''); ?>
                    </div>

                </div>			
				
              </div>
              <div class="col-md-6">
                <div class="card p-4 my-2">
              
				<p class="d-flex"><label class="w-25">Tax Account</label>
				<label class="radio-inline">
				  <input type="radio" name="bsd_type" id="bsd_type1" class="restrict-field" value="1" <?= ($billsundry_info['bsd_type'] == 1) ? 'checked' : '' ?>> Yes
				</label>
				<label class="radio-inline">
				  <input type="radio" name="bsd_type" id="bsd_type0" class="restrict-field" value="0" <?= ($billsundry_info['bsd_type'] == 0) ? 'checked' : '' ?>> No
				</label>
	
					</p>		
               <p class="d-flex">
                <span class="w-75" id="billsundarynature_yes_div" style="display:none;" >
				</span>
				<span class="w-100" id="billsundarynature_no_div" <?= ($billsundry_info['bsd_type'] == 0) ? '' : 'style="display:none;"' ?> >	
				<label class="w-40">Bill Sundary Nature</label>
				<?php	
			       
                    echo form_dropdown('billsundarynature_no', $billsundry_nature_no, $billsundry_info['bsd_nature'], 'id="billsundarynature_no" class="form-control w-75 " ');

				?>	</span>	
					</p>
				
				<span id="supply_type_div" <?php echo ($billsundry_info['bsd_type'] == '0') ? '' : 'style="display:none;"' ?>>
				<p class="d-flex"><label class="w-25">Supply Type</label>
                <?php
				   $bill_supply_type='';
				   $bill_hsn_sac ='';
				   $sel_sundry_calc_base ='';
				   if($billsundry_info['bsdconfign_info']){
					 $bill_supply_type = $billsundry_info['bsdconfign_info']['bsd_taxable_type'];  
				     $bill_hsn_sac = $billsundry_info['bsdconfign_info']['bsd_hsn_sac']; 
					 $sel_sundry_calc_base = $billsundry_info['bsdconfign_info']['bsd_base'];  		
				   }
					$supplytype_ar = array(''=>'Choose','1'=>'Goods','2'=>'Services','3'=>'Capital Goods');				
				   echo form_dropdown('bill_supply_type', $supplytype_ar,$bill_supply_type,'id="bill_supply_type" class="form-control w-75"');
						?>
					</p>
					</span>
				<span id="supplylabel_type_div" <?= ($billsundry_info['bsd_type'] == 0) ? '' : 'style="display:none;"' ?>>
				<p class="d-flex"><label class="w-25" id="supplylabel_type"></label>
                <input type="text" name="bill_hsn_sac" id="bill_hsn_sac"  class="form-control w-75" value="<?php echo $bill_hsn_sac;?>">
			    </p>
			    </span>
				<span id="calbase_div" <?= ($billsundry_info['bsd_type'] == 0) ? '' : 'style="display:none;"' ?>>	
				<p class="d-flex"><label class="w-25">Bill Sundary Cal Base</label>
                <?php
					$sundry_calc_base = array(1=>'Perc (%)',2=>'Absolute Amount');				
                   echo form_dropdown('sundry_calc_base', $sundry_calc_base, $sel_sundry_calc_base,'id="sundry_calc_base" class="form-control w-75" ');
						?>
					</p>				
					</span>
				<span id="iotypes_div">
				<p class="d-flex"><label class="w-25">IO Types</label>
                <?php
					$supplytype_ar = array('1'=>'Input','2'=>'Output');				
                   echo form_dropdown('bill_io_type', $supplytype_ar, $billsundry_info["bsd_input_output"],'id="bill_io_type" class="form-control w-75 restrict-field" ');
						?>
					</p>	</span>
			    <span id="taxcatgtypes_div" <?= ($billsundry_info['bsd_type'] == 1) ? '' : 'style="display:none;"' ?>>
				<p class="d-flex"><label class="w-25">Tax Catgory Types</label>
                <?php
                $is_cat_type_required = ($billsundry_info['bsd_type'] == 1) ? ' required' : '';
                   echo form_dropdown('cat_type', $catTypes, $billsundry_info['tax_cat_type'],'id="cat_type" class="form-control w-75 restrict-field" '.$is_cat_type_required.' ');
						?>
					</p>	</span>	
			
				<span id="taxsubcatgtypes_div" <?= ($billsundry_info['bsd_type'] == 1) ? '' : 'style="display:none;"' ?>>
				<p class="d-flex"><label class="w-25">Sub Type</label>
                <?php
					$sub_type_ar = array();			
					$is_sub_type_required = ($billsundry_info['bsd_type'] == 1) ? ' required' : '';
                   echo form_dropdown('sub_type', $sub_type_ar,'','id="sub_type" class="form-control w-75 restrict-field" '.$is_sub_type_required.' ');
						?>
					</p>	</span>	
			 
			   </div>
			
				<div class="card p-4 my-2" id="taxcatg_div" <?php echo ($billsundry_info['bsd_type'] == '0') ? '' : 'style="display:none;"' ?>>
				      <h5 class="pb-3">GST Details</h5>
				<div class="col-12"><label>GST TaxPayer Category <?php if($taxpayer_category_requird=='required'){?><span class="red">*</span><?php } ?></label> 
				 <?php echo form_dropdown('tax_cat_mst_id', $tax_category, $billsundry_info["tax_cat_mst_id"],'id="tax_cat_mst_id" class="form-control" '.$taxpayer_category_requird); ?></div>
              
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
                                'class'     => 'form-control',
								'value'     => $billsundry_info['acc_op_bal']
                            );
                            echo form_input($data);
                        ?>
                        <span class="input-group-text">
                            <input type="radio" name="bsd_op_bal_drcr" value="cr" class="form-check-input" <?= ($billsundry_info['acc_op_bal_drcr'] == 'cr') ? 'checked' : '' ?>>
                            &nbsp;Cr. &nbsp;&nbsp;&nbsp;

                            <input type="radio" name="bsd_op_bal_drcr" value="dr" class="form-check-input" <?= ($billsundry_info['acc_op_bal_drcr'] == 'dr') ? 'checked' : '' ?>>
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
                                'class'       => 'form-control',
								'value'       => $billsundry_info['acc_py_bal'],
                            );
                            echo form_input($data);
                        ?>
                        <span class="input-group-text">
                            <input type="radio" name="bsd_py_bal_drcr" value="cr" class="form-check-input" <?= ($billsundry_info['acc_py_bal_drcr'] == 'cr') ? 'checked' : '' ?>>
                            &nbsp;Cr. &nbsp;&nbsp;&nbsp;

                            <input type="radio" name="bsd_py_bal_drcr" value="dr" class="form-check-input" <?= ($billsundry_info['acc_py_bal_drcr'] == 'dr') ? 'checked' : '' ?>>
                            &nbsp;Dr.
                        </span> 
                    </div>
                </div>
                <div class="col-12"><label>MEMO OP. Bal</label> <div class="input-group w-75"><?php $data = array(
									  'name'        => 'memo_opp_bal',
									  'id'          => 'memo_opp_bal',
									  'value'       => '0.00',
									  'maxlength'   => '100',
									  'class'       => 'form-control',
									  'value'       => $billsundry_info['acc_memo_bal'] ?? '',									   
									  );
									  echo form_input($data);
									  
									  $memo_opn_dr_cr = $billsundry_info['memo_opn_dr_cr'] ?? '';
									  ?>
                    <span class="input-group-text"><input type="radio" name="memo_opn_dr_cr" value="cr" class="form-check-input" <?= ($memo_opn_dr_cr == 'cr') ? 'checked' : '' ?>>&nbsp;Cr. &nbsp;&nbsp;&nbsp;<input type="radio" name="memo_opn_dr_cr" value="dr" class="form-check-input" <?= ($memo_opn_dr_cr == 'dr') ? 'checked' : '' ?>>&nbsp;Dr.</span> </div>
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
function applyTaxRestriction(){
    var isTax = $("input[name='bsd_type']:checked").val();

    if(isTax == '1'){
        $(".restrict-field").prop("readonly", true).prop("disabled", true);

        $("input[type=radio]").not(
            "[name='bsd_op_bal_drcr'], [name='bsd_py_bal_drcr'], [name='memo_opn_dr_cr'], [name='bsd_type']"
        ).prop("disabled", true);
    } else {
        $(".restrict-field").prop("readonly", false).prop("disabled", false);
        $("input[type=radio]").prop("disabled", false);
    }
}

$(document).ready(function(){
    applyTaxRestriction();
});

$("input[name='bsd_type']").change(function(){
    applyTaxRestriction();
});
 

const subTypes = {
  1: {1:"IGST",2:"CGST",3:"SGST",4:"UT GST",5:"Cess (GST)"},
  2: {1:"TDS IGST",2:"TDS CGST",3:"TDS SGST",4:"TDS UT GST"},
  3: {1:"TCS IGST",2:"TCS CGST",3:"TCS SGST",4:"TCS UT GST"},
  4: {1:"TDS"},
  5: {1:"TCS"},
  99:{0:"Other Taxes"}
};

$(document).ready(function() {
    let selectedCat = "<?= $billsundry_info['tax_cat_type'] ?>";
    let selectedSub = "<?= $billsundry_info['tax_cat_sub_type'] ?>";

    function loadSubTypes(catType, selectedSub) {
        let $subType = $('#sub_type');
        $subType.empty().append('<option value="">-- Select Sub Type --</option>');

        if (catType && subTypes[catType]) {
            $.each(subTypes[catType], function(val, label) {
                let isSelected = (val == selectedSub) ? 'selected' : '';
                $subType.append(`<option value="${val}" ${isSelected}>${label}</option>`);
            });

            // auto select if only one option (like Other Taxes)
            if (Object.keys(subTypes[catType]).length === 1) {
                $subType.prop('selectedIndex', 1);
            }
        }
    }

    // On category change
    $('#cat_type').on('change', function() {
        loadSubTypes($(this).val(), '');
    });

    // On page load, pre-fill
    if (selectedCat) {
        loadSubTypes(selectedCat, selectedSub);
    }
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




$(document).ready(function () {
    // set default selection
    $('[name="bill_supply_type"]').val('<?php echo $bill_supply_type;?>');  // auto select SAC
    
    // then run your logic
    $('[name="bill_supply_type"]').trigger('change');
	
   
    $("input[name='bsd_type'][value='<?php echo $billsundry_info['bsd_type'];?>']"). trigger('change');    

});



$(document).on('change','[name="bill_supply_type"]', function(){
	
	 var bsdTypeVal = $("input[name='bsd_type']:checked").val();
	if(bsdTypeVal==0){
	$("#supply_type_div").show();
	$("#supplylabel_type_div").show();
	$("#supplylabel_type").show();
	}else{
		$("#supply_type_div").hide();
	$("#supplylabel_type_div").hide();
	$("#supplylabel_type").hide();
	}
	
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

// 🔥 FLAG to detect real user change (not page load)
window.bsdTypeChanged = false;

$("input[name='bsd_type']").change(function(){

    window.bsdTypeChanged = true; // mark user interaction

    $("select[name=billsundarynature_no]").val(<?php echo $billsundry_info['bsd_nature'] ?? "";?>);

    var radvalue = '0';

    // 🔥 RESET ALL REQUIRED FIRST (CRITICAL FIX)
    $("#myform select, #myform input").removeAttr("required").removeClass("required");

    if($(this).val()=='1'){	// ===== Tax Account YES =====
    
        $("#supply_type_div").hide();
        $("#supplylabel_type_div").hide();
        $("#supplylabel_type").hide();
        
        if($("#item_tax").val()!=''){
            $("#labelhint").html(showlabel_message);
        } else {
            $("#labelhint").html("");
        }

        $("#taxcatg_div").hide();
        $("#calbase_div").hide();
        $("#supplytype_div").hide();	

        $("#billsundarynature_yes_div").show();
        $("#billsundarynature_no_div").hide();

        $("input[name=fed][value=" + radvalue + "]").attr("disabled",true);			

        // ✅ CLEAR ONLY IF USER CHANGED (NOT ON PAGE LOAD)
        if(window.bsdTypeChanged){
            $("#bill_supply_type").val('');
            $("#bill_hsn_sac").val('');
            $("#tax_cat_mst_id").val('');
            $("#billsundarynature_no").val('');
        }

        $("#iotypes_div").show();	
        
        $('#taxcatgtypes_div').show();
        $('#taxsubcatgtypes_div').show();
        
        // ✅ REQUIRED FIELDS
        $("select[name=cat_type]").attr("required",true);
        $("select[name=sub_type]").attr("required",true);

    } else { // ===== Tax Account NO =====

        $("#supply_type_div").show();
        $("#supplylabel_type_div").show();
        $("#supplylabel_type").show();
        
        $('#taxcatgtypes_div').hide();
        $('#taxsubcatgtypes_div').hide();

        $("#calbase_div").show();
        $("#taxcatg_div").show();		

        $("#billsundarynature_yes_div").hide();
        $("#billsundarynature_no_div").show();

        $("#iotypes_div").hide();				
        $("#supplytype_div").hide();	

        $("input[name=fed][value=" + radvalue + "]").attr("disabled",false);

        // ✅ CLEAR ONLY IF USER CHANGED
        if(window.bsdTypeChanged){
            $("#cat_type").val('');
            $("#sub_type").val('');
        }

        // ✅ REQUIRED FIELDS (FIXED BUG HERE)
        $("select[name=billsundarynature_no]").attr("required",true);
        $("select[name=bill_supply_type]").attr("required",true);
        $("#tax_cat_mst_id").attr("required",true);

    }

});

// 🔥 IMPORTANT: run once on page load WITHOUT triggering reset
$("input[name='bsd_type']:checked").each(function(){
    $(this).triggerHandler('change'); // 🔥 NOT trigger()
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
stop_loader();
    // ✅ PRIMARY VALIDATION (FIX ADDED)
    if (!$('input[name="account_primary"]:checked').length) {
        $('#validation_errors').show().html(`
            <div class="alert-error-custom">
                <i class="bi bi-x-circle-fill"></i>
                <div>
                    <strong>Error!</strong>
                    <ul><li>Please select Primary (Yes / No).</li></ul>
                </div>
                <button type="button" class="btn-close" aria-label="Close"></button>
            </div>
        `);
        window.scrollTo(0,0);
        return false;
    }

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
                window.history.back();
            }
            else{
                stop_loader();
                alert_notification(response.message);

                if(response.errors){
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
                        $('#validation_errors').show().html(html);
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
            var error = '';
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
        }
    });
});

	</script>