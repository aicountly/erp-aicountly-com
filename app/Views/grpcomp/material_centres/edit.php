<?php $header = array( 	'title' => 'Modify Material Centres' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
            echo form_open(base_url().'/'.$folder_path.'material_centres/modify_centre/'.$centre_id, $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Modify Material Centre</h3></div> 
			 <div class="col-6 text-end"><a href="<?php echo history_back();?>"  class="btn btn-sm btn-outline-success">« Back</a></div> 
            <div class="col-md-6">
               <div class="col-12"><label>Centre Name</label><?php $data = array(
									  'name'        => 'mat_cent_name',
									  'id'          => 'mat_cent_name',
									  'value'       => $enc_string->nc_string($centre_info['mat_cent_name'],'de'),
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Alias</label> <?php $data = array(
									  'name'        => 'mat_cent_alias',
									  'id'          => 'mat_cent_alias',
									  'value'       => $enc_string->nc_string($centre_info['mat_cent_alias'],'de'),
									  'maxlength'   => '255',
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Print Name</label> <?php $data = array(
									  'name'        => 'mat_cent_print',
									  'id'          => 'mat_cent_print',
									  'value'       => $enc_string->nc_string($centre_info['mat_cent_print'],'de'),
									  'maxlength'   => '100',
									   'class'       => 'form-control',
									   'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Matertial Centre Group</label> <?php	                  
				   echo form_dropdown('mat_cent_grp', $material_group, $centre_info['mat_cent_grp_id'],'id="mat_cent_grp" class="form-control"  ');
						?></div>
              
               <div class="col-12"><label>Address 1</label> <?php $data = array(
								   'name'        => 'mat_cent_add1',								   
								   'maxlength'   => '255',
								    'value'       => $enc_string->nc_string($centre_info['mat_cent_add1'],'de'),
								   'class'       => 'form-control'
								   );
								   echo form_input($data);
								  ?></div>
               <div class="col-12"><label>Address 2</label> <?php $data = array(
								   'name'        => 'mat_cent_add2',								 
								   'maxlength'   => '255',
								    'value'       => $enc_string->nc_string($centre_info['mat_cent_add2'],'de'),
								   'class'       => 'form-control'
								   );
								   echo form_input($data);
								  ?></div>
               <div class="col-12"><label>PIN</label> <?php $data = array(
								   'name'        => 'mat_cent_pin',
								   'maxlength'   => '255',
								    'value'       => $centre_info['mat_cent_pin'],
								   'class'       => 'form-control'
								   );
								   echo form_input($data);
								  ?>
				</div>
            </div>            
              <div class="col-md-6">
                <div class="col-12"><label>Country</label> <?php echo form_dropdown('mat_cent_country', $CountryDropdown, $centre_info['mat_cent_country'],'id="mat_cent_country" class="form-control" '); ?></div>
                <div class="col-12"><label>State</label><div class="state_div">
                  <?php echo form_dropdown('mat_cent_state', array(''=>'Choose'), '','id="mat_cent_state" class="form-control" '); ?>
						</div></div>
             <div class="col-12"><label>City</label> <?php $data = array(
									  'name'        => 'mat_cent_city',
									  'id'          => 'mat_cent_city',
									  'value'       => $enc_string->nc_string($centre_info['mat_cent_city'],'de'),
									  'maxlength'   => '255',
									  'class'      => 'form-control'									 
									  );
									  echo form_input($data);
				 ?></div>
               
             </div>
             <div class="col-12 text-center">
                 <input type="submit" value="SAVE" class="btn btn-primary mx-2" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                 <a href="javascript:void(0);" onclick="window.history.go(-1); return false;"  class="btn btn-secondary mx-2">QUIT</a>
              </div>
            </div>  </form> 
<?php echo view('includes/footer_scripts'); ?>
<script>
function load_states(t,a){$(".state_div").html("Loading..."),$.get(baseurl+"/home/ajax_states_list/"+t+"/"+a,(function(t){$(".state_div").html(t),$("#state_id").attr("id","mat_cent_state"),$("#mat_cent_state").attr("name","mat_cent_state")}))}window.ini=load_states('<?php echo $centre_info['mat_cent_country'];?>','<?php echo $centre_info['mat_cent_state'];?>'),$("#acc_country_id").on("change",(function(){var t=$(this).val();$(".state_div").html("Loading..."),$.get(baseurl+"/home/ajax_states_list/"+t+"/0",(function(t){$(".state_div").html(t),$("#state_id").attr("id","mat_cent_state"),$("#mat_cent_state").attr("name","mat_cent_state")}))}));
</script>