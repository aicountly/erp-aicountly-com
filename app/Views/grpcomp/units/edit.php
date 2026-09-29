<?php $header = array( 	'title' => 'Modify Units' ); ?>
<?php echo view('includes/header',$header); ?>

<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
<?php  $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().'/'.$folder_path.'units/modify/'.$unit_id, $attributes);
       ?>
          <div class=" row">
             <div class="col-6"><h3 class="pb-3">Modify Unit</h3></div>
             <div class="col-6 text-end"><a href="<?php echo $base_url.'units/list';?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-6">
               <div class="col-12"><label>Unit Name</label> <?php $data = array(
									  'name'        => 'item_unit',
									  'id'          => 'item_unit',
									  'value'       => $enc_string->nc_string($get_info['item_unit'],'de'),
									  'maxlength'   => '100',
									  'class'       =>  'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Unit Alias</label> <?php $data = array(
									  'name'        => 'item_unit_alias',
									  'id'          => 'item_unit_alias',
									  'value'       => $enc_string->nc_string($get_info['item_unit_alias'],'de'),
									  'maxlength'   => '100',
									  'class'       =>  'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?></div>
               <div class="col-12"><label>Print Name</label> <?php $data = array(
                                      'name'        => 'Item_unit_print',
                                      'id'          => 'Item_unit_print',
                                      'value'       => $enc_string->nc_string($get_info['item_unit_alias'],'de'),
                                      'maxlength'   => '50',
									  'class'       => 'form-control',
									  'required'    => true
                                      );
                                      echo form_input($data);
                                      ?></div>
              <div class="col-12"><label>UQC <small>For GST Returns</small></label>
			<?php $data = array(
                                      'name'        => 'Item_unit_uqc',
                                      'id'          => 'Item_unit_uqc',
                                      'value'       => $enc_string->nc_string($get_info['item_unit_print'],'de'),
                                      'maxlength'   => '50',
									  'class'       => 'form-control'									 
                                      );
                                      echo form_input($data);
                                      ?></div>              
              <div class="col-md-12  my-3"><label>&nbsp;</label> 
                 <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                    <a href="<?php echo $base_url.'units/list';?>" class="btn btn-secondary mx-2" >QUIT</a>
              </div>            
            </div>            
              </form> 
<?php echo view('includes/footer_scripts'); ?>	 			  