 <h5 class="pb-3">Group Master Mapping Details</h5>
					  <?php if($load_group_companies){ foreach($load_group_companies as $compinfo){ 
					 $sub_master= array();
					  $sub_master['']= 'Choose';
					 foreach($compinfo['member_master_lists'] as $sbrow){
						 $sub_master[$sbrow['crs_master_id']]= ucwords($sbrow['crs_master_name']);
					    }
					  ?>
				 <div class="col-12"><label><?php echo $compinfo['grp_name'];?></label> 
				 <?php echo form_dropdown('crs_master_id[]',$sub_master,'','class="selectwidget" ');?>
				 </div>
					  <?php } } ?>	