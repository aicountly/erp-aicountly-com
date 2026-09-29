<?php $header = array( 	'title' => 'Modify Category' ); ?>
<?php echo view('includes/header',$header); ?>

<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
 <?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().'/'.$folder_path.'items/modify_category/'.$category_id, $attributes);
       ?>
           <div class=" row">
             <div class="col-6"><h3 class="pb-3">Modify Stock Category</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-6">
               <div class="col-12"><label>Category Name</label> <?php $data = array(
								   'name'        => 'item_cat',
								   'value'       => $enc_string->nc_string($category_info['item_cat'],'de'),
								   'maxlength'   => '255',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
              <div class="col-12"><label>Category Alias</label> <?php $data = array(
								   'name'        => 'item_cat_alias',
								   'value'       => $category_info['item_cat_alias'],
								   'maxlength'   => '60',
								   'class'       => 'form-control',
								   'required'    => true
								   );
								   echo form_input($data);
								  ?></div>
            
             <div class="col-md-12  my-3"><label>&nbsp;</label> 
                 <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
                 <a href="<?php echo $base_url.'items/stock_category';?>" class="btn btn-secondary">QUIT</a>
              </div>
            
            </div>
            
              </form> </div>

<?php echo view('includes/footer_scripts'); ?>