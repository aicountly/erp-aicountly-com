<?php $header = array('title' => 'Add Item Group' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
<?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
        echo form_open(base_url().'/'.$folder_path.'items/add_group', $attributes);
       ?>
           <div class=" row">
             <div class="col-6"><h3 class="pb-3">Add A Item Group</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
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
            
              <div class="col-12" id="primaryyes" style="display:none;"><label>Under</label>
              
				</div>
				
              <div class="col-12" id="primaryno"><label>Under</label>
			 <div class="w-75 d-inline-block"><?php	echo form_dropdown('no_group_under', $user_groups_dropdown, '','id="no_group_under" class="form-control selectwidget required"  ');
						?>	
			  </div> </div>
			  
			  
             <div class="col-md-12  my-3"><label>&nbsp;</label> 
                 <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                 <a href="<?php echo $base_url.'items/list_group';?>" class="btn btn-secondary">QUIT</a>
              </div>
            
            </div>
            
              </form> </div>

<?php echo view('includes/footer_scripts'); ?>
<script>
 $("input[name='primary_group']").click(function(){
     if($(this).is(':checked')) 
       {
          if($(this).val()=='Y'){
              
              $("#no_group_under").removeClass('required');
              $("#primaryyes").hide();
              
              $("#primaryno").hide();}
          else if($(this).val()=='N'){
             $("#primaryno").show()
             $("#primaryyes").hide();
             
             $("#no_group_under").addClass('required');
          }
          else{
            $("#primaryno").hide();
            $("#primaryyes").hide();
            }
       }
  });
</script>