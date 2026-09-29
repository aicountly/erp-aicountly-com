<?php $header = array( 	'title' => 'Modify MC Store' ); ?>
<?php echo view('includes/header',$header); ?>

<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
<?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().'/'.$folder_path.'material_centres/modify_mcstore/'.$store_id, $attributes);
       ?>
           <div class=" row">
             <div class="col-6"><h3 class="pb-3">Modify MC Store</h3></div> 
			 <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></div> 
			  
            <div class="col-md-6">
                <div class="col-12"><label>Choose Material Centre</label>
               <?php	                  
              	   echo form_dropdown('mat_cent_id', $mc_centres_dropdown, $mcstore_info['mat_cent_id'],'id="mat_cent_id" class="form-control"  ');
				?>
				</div>
               <div class="col-12"><label>Name</label> <?php $data = array(
								   'name'        => 'mc_store_name',
								   'value'       => $mcstore_info['mc_store_name'],
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
               <div class="col-12"><label>Alias</label> <?php $data = array(
								   'name'        => 'mc_store_alias',
								   'value'       => $mcstore_info['mc_store_alias'],
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
 	
			   
			   
			 </div>
     
            
             <div class="col-md-12  my-3"><label>&nbsp;</label> 
                 <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                 <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-secondary mr-1">QUIT</a>
                 
              </div>
            
            </div>
            
              </form> </div>

<?php echo view('includes/footer_scripts'); ?>