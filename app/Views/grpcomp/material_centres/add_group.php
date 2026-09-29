<?php $header = array( 	'title' => 'Add Material Centre Group' ); ?>
<?php echo view('includes/header',$header); ?>

<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
<?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().'/'.$folder_path.'material_centres/add_group', $attributes);
       ?>
           <div class=" row">
             <div class="col-6"><h3 class="pb-3">Add Material Centre Group</h3></div> 
			 <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></div> 
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
			   <div class="col-lg-9">
						<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="primary_group" id="primary_group_yes" value="Y"  wtx-context="D9EBA312-CD3D-42F3-89FB-93B24C1B5BC0">
						<label class="form-check-label" for="primary_group_yes">
						Yes
						</label>
						</div>
						<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="primary_group" id="primary_group_no" value="N" checked="" wtx-context="E14EBE92-4969-47B6-8CAE-D24F5BB28733">
						<label class="form-check-label" for="primary_group_no">
						No
						</label>
						</div>
					</div>	
			   
			   
			 </div>
               <div class="col-12"><label>Main</label>
               <?php	                  
				   echo form_dropdown('mat_cent_main', $presdfnd_main, '1','id="mat_cent_main" class="form-control"  ');
				?>
				</div>
            
             <div class="col-md-12  my-3"><label>&nbsp;</label> 
                 <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                 <a href="<?php echo $base_url.'material_centres/group_list';?>" class="btn btn-secondary">QUIT</a>
              </div>
            
            </div>
            
              </form> </div>

<?php echo view('includes/footer_scripts'); ?>