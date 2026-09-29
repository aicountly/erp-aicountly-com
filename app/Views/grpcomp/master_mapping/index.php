<?php $header = array( 	'title' => 'Master Mapping' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
	   <?php $attributes = " id='myform' method='get' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().'/'.$folder_path.'master_mapping', $attributes);
       ?>
	   <?php echo $message_output->run() ;?>  
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Master Mapping</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-6">
                <div class="card p-4 my-2">
                <h5 class="pb-2">General Info</h5>    
               <div class="col-12"><label>Criteria</label>	
				<?php 
			   echo form_dropdown("criteria",$criteria_dropdown,$sel_criteria,'id="criteria" class="form-control"')
			   ?>	
			   
               </div>     
              <!-- <div class="col-12"><label>Master Mapping</label>
			   <?php 
			    $final_array     = array();
				 $final_array[''] = 'choose';
				 if( $master_lists){
					 foreach( $master_lists as $row){
						 $final_array[$row['crs_master_id']]= ucwords(strtolower($row['crs_master_name']));
					 }
					 
				 }
			   
			   //echo form_dropdown("mapcomp_id",$final_array,'','id="mapcomp_id" class="selectwidget"')
			   ?>
			   </div>-->
               <div class="col-12"><label>All Masters</label>
			   <input type="checkbox" name="allmasterchk" id="allmasterchk" value="1" checked>
			   </div>
				</div>				 	
						
			</div>
		<div class="col-12 text-center">
                 <input type="submit" value="GO" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">                
              </div>
            
            
            </div>  </form> 

    <div class="row">
  		<div class="col-md-8">
		   	<div class="card p-4 my-2">
    	
    			<div class="list-group">
		    		<?php foreach ($masters as $key => $value) { ?>

		    		
		    			<a href="<?= base_url().'/grpcomp/mapped_group_masters?criteria='.$key ?>" class="list-group-item list-group-item-action">
		    				<?= $value ?>
		    			</a>
		    		<?php } ?>
					</div>
    		</div>
    	</div>
  	</div>

<?php echo view('includes/'.$folder_path.'footer_scripts'); ?>

<script>
$("#criteria").on("change",function(){
	//$("#myform").submit();
});
/* $( "#criteria" ).autocomplete({
               source: [
                  { label: "Accounts", value: "acc" },
                  { label: "Accounts Group", value: "accgrp" },
				  { label: "Items", value: "itm" },
                  { label: "Items Group", value: "itmgrp" },
				  { label: "Stock Category", value: "itemcat" },
                  { label: "Material Centre", value: "mcmst" },
				  { label: "Material Centre Group", value: "mcgrp" },
                  { label: "Cost Centre", value: "costct" },
				  { label: "Cost Centre Group", value: "costctgrp" }
               ],
			   select:function(event,ui){
			   $("#criteria").val(ui.item.label);return false;
			}
            }); */
			
//$("body").on('autocompleteselect', 'input#criteria', function (event,ui)     {
    //alert(ui.item.label);
   // alert(ui.item.value);

//});
</script>