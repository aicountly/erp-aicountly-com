<?php $header = array( 	'title' => 'Add Bill Sundry' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-sm-6{padding-bottom:2px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
    .myform .select2 {width:75%!important; }
</style>	  
      <?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().'/'.$folder_path.'billsundry/add', $attributes);
       ?>
       <div class=" row">
             <div class="col-md-6 pb-3"><h3 class="pb-3">Add Bill Sundary</h3></div>  <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></div> 
       </div>      
        <div class=" row">
               <div class="col-md-6">
                <div class="card p-4 my-2">  
               <p class="d-flex"><label class="w-25">Name</label> <input type="text" name="billsndry_name" id="billsndry_name"  class="form-control w-75" required></p>
               <p class="d-flex"><label class="w-25">Allias</label> <input type="text" name="billsndry_allias" id="billsndry_allias" class="form-control w-75" required ></p>
               <p class="d-flex"><label class="w-25">Print Name</label> <input type="text" name="billsndry_pname" id="billsndry_pname" class="form-control w-75" required ></p>
                </div>

                <div class="card p-4 my-2">
                    <div class="col-12 my-1">
                        <label>Primary</label>
                        <div class="input-group w-75">
                            <div class="input-group-text py-1">
                                <input type="radio" name="account_primary" value="Y" id="primary_yes" class="form-check-input">
                                &nbsp;<label for="primary_yes">Yes</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="account_primary" value="N" id="primary_no" class="form-check-input" checked>
                                &nbsp;<label for="primary_no">No</label>
                            </div> 
                        </div>
                    </div>

                    <div class="col-12 my-1" id="group_div">
                        <label>Group</label>
                        <?php echo form_dropdown('sundry_group', $group_main_dropdown, set_value('sundry_group'),'id="sundry_group" class="form-control select2" required="true" '); ?>
                    </div>

                    <div class="col-12 my-1" id="parent_div" style="display: none;">
                        <label>Parent Group</label>
                        <?php echo form_dropdown('parent_group', $group_primary_dropdown, set_value('parent_group'),'id="parent_group" class="form-control select2" '); ?>
                    </div>

                </div>
              </div>
              <div class="col-md-6">
                <div class="card p-4 my-2">
               <p class="d-flex"><label class="w-25">Bill Sundary Type</label> 
                <?php	
                $billsundarytypes = array(''=>'Choose','A'=>'Additive','D'=>'Substractive');
                   echo form_dropdown('billsundarytype', $billsundarytypes, set_value('billsundarytype'),'id="billsundarytype" class="form-control w-75" required ');
						?>	
						</p>
               <p class="d-flex"><label class="w-25">Bill Sundary Nature</label>
                <?php	
                   echo form_dropdown('billsundarynature', $billsundry_nature, set_value('billsundarynature'),'id="billsundarynature" class="form-control w-75" ');
						?>
					</p>
               <p class="d-flex"><label class="w-25">Default Value</label> <input type="text" name="default_value" class="form-control w-75" ></p>
                
                </div>
             </div>
            </div>
 
        <div class="row">
        <div class="col-md-6">
            <div class="card p-4 my-2">
                 <b>Amount of Bill Sundry to be fed as </b><br>
                <div class="row px-3">
                     <p class="form-check col-md-6">
                        <input type="radio" name="fed" value="1" class="form-check-input"> 
                        <label>Net Bill Amount</label>
                    </p>
                     <p class="form-check col-md-6">
                        <input type="radio" name="fed" value="2" class="form-check-input"> 
                        <label>Taxable Amount</label>
                    </p>
                     <p class="form-check col-md-6">
                        <input type="radio" name="fed" value="3" class="form-check-input"> 
                        <label>Total MRP of Item</label>
                    </p>
                     <p class="form-check col-md-6">
                        <input type="radio" name="fed" value="4" class="form-check-input"> 
                        <label>Previous Sundry Amount</label>
                    </p>
                </div>
            </div>
        </div>
        
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
            </div>
        </div>
        </div>
        

               <div class="col-md-12 text-center my-3">
                <input type="submit" value="SAVE" class="btn btn-primary mx-2">
                <input type="reset" value="QUIT" onclick="window.history.go(-1); return false;"  class="btn btn-secondary mx-2">
              </div>

        </form>       
    
        </div>
	
				
     
<?php echo view('includes/footer_scripts'); ?>
<script>
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
</script>
