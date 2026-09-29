<?php $header = array( 	'title' => 'Add Account Group' ); ?>
<?php echo view('includes/header',$header); ?>

<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
<?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().'/'.$folder_path.'accounts/add_group', $attributes);
       ?>
           <div class=" row">
             <div class="col-6"><h3 class="pb-3">Add A Group</h3></div> 
			 <div class="col-6"><span class="float-end"></span></div> 
            <div class="col-md-6">
               <div class="col-12"><label>Name</label> <?php $data = array(
								   'name'        => 'group_name',
								   'value'       => '',
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
               <div class="col-12"><label>Alias</label> <?php $data = array(
								   'name'        => 'group_name_alias',
								   'value'       => '',
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
 
               <div class="col-12"><label>Primary</label> 
			   <input type="radio" class="form-check-input" name="primary_group" id="primary_group_y" value="Y"> Yes &nbsp;&nbsp;
               <input type="radio" class="form-check-input" name="primary_group" id="primary_group_n" value="N" checked> No
			   
			   
			 </div>
              <div class="col-12"><label>Under</label>
			 <?php		echo form_dropdown('group_under', $group_main, '25','id="group_under" class="form-control"  ');
						?>	
			  </div>              
            
            
             <div class="col-md-12  my-3"><label>&nbsp;</label> 
                 <input type="submit" value="SAVE" class="btn btn-primary mr-1">
                 <a href="<?php echo $base_url.'accounts/list_group';?>" class="btn btn-secondary">QUIT</a>
              </div>
            
            </div>
            
              </form> </div>

<?php echo view('includes/footer_scripts'); ?>