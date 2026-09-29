<?php $header = array( 	'title' => 'Modify Material Centre Group' ); ?>
<?php echo view('includes/header',$header); ?>

<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
<?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().'/'.$folder_path.'material_centres/modify_group/'.$group_id, $attributes);
       ?>
           <div class=" row">
             <div class="col-6"><h3 class="pb-3">Modify Material Centre Group</h3></div> 
			 <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></div> 
            <div class="col-md-6">
               <div class="col-12"><label>Name</label> <?php $data = array(
								   'name'        => 'group_name',
								   'value'       => $enc_string->nc_string($group_info['mc_grp_name'],'de'),
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
               <div class="col-12"><label>Alias</label> <?php $data = array(
								   'name'        => 'group_name_alias',
								   'value'       => $enc_string->nc_string($group_info['mc_alias'],'de'),
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
 
               <div class="col-12"><label>Primary</label>
		<?php if($group_info['mc_primary']=='Y'){ ?>			   
			<input type="radio" class="form-check-input" name="primary_group" id="primary_group_y" value="Y" checked> Yes &nbsp;&nbsp;
			<input type="radio" class="form-check-input" name="primary_group" id="primary_group_n" value="N" > No
		<?php } else if($group_info['mc_primary']=='N'){ ?>
			<input type="radio" class="form-check-input" name="primary_group" id="primary_group_y" value="Y"> Yes &nbsp;&nbsp;
			<input type="radio" class="form-check-input" name="primary_group" id="primary_group_n" value="N" checked> No
		<?php }  else { ?>		   
			<input type="radio" class="form-check-input" name="primary_group" id="primary_group_y" value="Y"> Yes &nbsp;&nbsp;
			<input type="radio" class="form-check-input" name="primary_group" id="primary_group_n" value="N" checked> No
		<?php } ?>			   
		</div>
               <div class="col-12"><label>Main</label>
                <?php	                  
				   echo form_dropdown('mat_cent_main',$presdfnd_main ,trim($group_info['under_mc_grp_id']),'id="mat_cent_main" class="form-control"  ');
				?>
				</div>
            
             <div class="col-md-12  my-3"><label>&nbsp;</label> 
                 <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                 <a href="<?php echo $base_url.'material_centres/group_list';?>" class="btn btn-secondary">QUIT</a>
              </div>
            
            </div>
            
              </form> </div>

<?php echo view('includes/footer_scripts'); ?>