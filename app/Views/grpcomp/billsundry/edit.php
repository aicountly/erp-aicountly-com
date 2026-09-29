<?php $header = array( 	'title' => 'Modify Bill Sundry' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-sm-6{padding-bottom:2px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
    .myform .select2 {width:75%!important; }
</style>

        <div id="validation_errors"></div>
      <?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().'/'.$folder_path.'billsundry/modify/'.$billsundry_id, $attributes);
       ?>
       <div class=" row">
             <div class="col-md-6 pb-3"><h3 class="pb-3">Modify Bill Sundary</h3></div>  <div class="col-6 text-end"><a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm">« Back</a></div> 
       </div>      
       <div class=" row">
               <div class="col-md-6">  
               <p class="d-flex"><label class="w-25">Name</label> <input type="text" name="billsndry_name" id="billsndry_name"  class="form-control w-75" value="<?php echo $billsundry_info['bill_sundry_name'];?>" required></p>
               <p class="d-flex"><label class="w-25">Allias</label> <input type="text" name="billsndry_allias" id="billsndry_allias" class="form-control w-75" value="<?php echo $billsundry_info['bill_sundry_alias'];?>" required ></p>
               <p class="d-flex"><label class="w-25">Print Name</label> <input type="text" name="billsndry_pname" id="billsndry_pname" class="form-control w-75" value="<?php echo $billsundry_info['sundry_print_name'];?>" required ></p>
              </div>
              <div class="col-md-6">  
               <p class="d-flex"><label class="w-25">Bill Sundary Type</label> 
                <?php	
                $billsundarytypes = array(''=>'Choose','A'=>'Additive','D'=>'Substractive');
                   echo form_dropdown('billsundarytype', $billsundarytypes, $billsundry_info['sundry_type'],'id="billsundarytype" class="form-control w-75" required ');
						?>	
						</p>
               <p class="d-flex"><label class="w-25">Bill Sundary Nature</label>
                <?php	
                   echo form_dropdown('billsundarynature', $billsundry_nature, $billsundry_info['sundry_nature'],'id="billsundarynature" class="form-control w-75" ');
						?>
					</p>
               <p class="d-flex"><label class="w-25">Default Value</label> <input type="text" name="default_value" class="form-control w-75" value="<?php echo $billsundry_info['sundry_def_value'];?>"></p>
             </div>
              
              <div class="col-md-6">
                <div class="card p-4 my-2">
                    <div class="col-12 my-1">
                        <label>Primary</label>
                        <div class="input-group w-75">
                            <div class="input-group-text py-1">
                                <input type="radio" name="account_primary" value="Y" <?= $billsundry_info['acc_grp_parent_id'] != 0 ? 'checked' : '' ?> id="primary_yes" class="form-check-input">
                                &nbsp;<label for="primary_yes">Yes</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="account_primary" value="N" <?= $billsundry_info['acc_grp_id'] != 0 ? 'checked' : '' ?> id="primary_no" class="form-check-input">
                                &nbsp;<label for="primary_no">No</label>
                            </div> 
                        </div>
                    </div>

                    <div class="col-12 my-1" id="group_div" <?= $billsundry_info['acc_grp_id'] == 0 ? 'style="display: none;"' : '' ?>>
                        <label>Group</label>
                        <?php echo form_dropdown('sundry_group', $group_main_dropdown, $billsundry_info['acc_grp_id'],'id="sundry_group" class="form-control select2" ($billsundry_info["acc_grp_id"] != 0 ? "required" : "") '); ?>
                    </div>

                    <div class="col-12 my-1" id="parent_div" <?= $billsundry_info['acc_grp_parent_id'] == 0 ? 'style="display: none;"' : '' ?>>
                        <label>Parent Group</label>
                        <?php echo form_dropdown('parent_group', $group_primary_dropdown, $billsundry_info['acc_grp_parent_id'],'id="parent_group" class="form-control select2" ($billsundry_info["acc_grp_parent_id"] != 0 ? "required" : "") '); ?>
                    </div>

                </div>
            </div>
             
             <div class="col-md-12">
                 <b>Amount of Bill Sundry to be fed as </b><br>
               <div class="row px-3">
                <p class="form-check col-md-6">
                    <?php if($billsundry_info['sundry_calc_subtype']=='1')
                              $type1=' checked="true"';
                         else
                             $type1='';
                    if($billsundry_info['sundry_calc_subtype']=='2')
                              $type2=' checked="true"';
                         else
                             $type2='';
                             
                    if($billsundry_info['sundry_calc_subtype']=='3')
                              $type3=' checked="true"';
                         else
                             $type3='';
                             
                    if($billsundry_info['sundry_calc_subtype']=='4')
                              $type4=' checked="true"';
                         else
                             $type4='';
                
                    ?>
                    <input type="radio" name="fed" value="1" class="form-check-input"<?php echo $type1;?>> <label>Net Bill Amount</label></p>
                 <p class="form-check col-md-6"><input type="radio" name="fed" value="2"  class="form-check-input"<?php echo $type2;?>> <label>Taxable Amount </label></p>
                 <p class="form-check col-md-6"><input type="radio" name="fed" value="3"  class="form-check-input"<?php echo $type3;?>> <label>Total MRP of Item </label></p>
                 <p class="form-check col-md-6"><input type="radio" name="fed" value="4"  class="form-check-input"<?php echo $type4;?>> <label>Previous Sundry Amount </label></p>
             </div></div><br>

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
                                    'value'     => $billsundry_info['bsd_op_bal'],
                                );
                                echo form_input($data);
                            ?>
                            <span class="input-group-text">
                                <input type="radio" name="bsd_op_bal_drcr" value="cr" class="form-check-input" <?= ($billsundry_info['bsd_op_bal_drcr'] == 'cr') ? 'checked' : '' ?>>
                                &nbsp;Cr. &nbsp;&nbsp;&nbsp;

                                <input type="radio" name="bsd_op_bal_drcr" value="dr" class="form-check-input" <?= ($billsundry_info['bsd_op_bal_drcr'] == 'dr') ? 'checked' : '' ?>>
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
                                    'value'       => $billsundry_info['bsd_py_bal'],
                                );
                                echo form_input($data);
                            ?>
                            <span class="input-group-text">
                                <input type="radio" name="bsd_py_bal_drcr" value="cr" class="form-check-input" <?= ($billsundry_info['bsd_py_bal_drcr'] == 'cr') ? 'checked' : '' ?>>
                                &nbsp;Cr. &nbsp;&nbsp;&nbsp;

                                <input type="radio" name="bsd_py_bal_drcr" value="dr" class="form-check-input" <?= ($billsundry_info['bsd_py_bal_drcr'] == 'dr') ? 'checked' : '' ?>>
                                &nbsp;Dr.
                            </span> 
                        </div>
                    </div>
                </div>
            </div>
           
            
             <div class="col-md-12 text-center my-3">
                <input type="submit" value="SAVE" id="submitbtn" class="btn btn-primary mx-2">
                 <input type="reset" value="QUIT" onclick="window.history.go(-1); return false;" class="btn btn-secondary mx-2">
              </div>
            
         </div>    
         </form>       
    
        </div>
	
				
     
<?php echo view('includes/footer_scripts'); ?>
<script>

    $('input[type=radio][name="account_primary"]').change(function() {
        if (this.value == 'Y'){
            $('#group_div').css('display', 'none');
            $('#account_group').attr('required', false);
            $('#parent_div').css('display', 'block');
            $('#parent_group').val('');
            $('#parent_group').attr('required', true);
        }
        else if(this.value == 'N'){
            $('#parent_div').css('display', 'none');
            $('#parent_group').attr('required', false); 
            $('#group_div').css('display', 'block');
            $('#account_group').val('');
            $('#account_group').attr('required', true);
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
                    window.history.back();
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
</script>