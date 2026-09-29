<?php $header = array( 	'title' => 'Set Default Template' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
.content{position:relative;}
.preview_window{position:relative; background:#fff; padding:10px;}
.preview_window .btn{position:absolute; right:10px; top:0px; opacity:0; transition:all .4s ease-in-out;}
.preview_window:hover .btn{top:20px; opacity:1}
</style>
<style>
.content{position:relative;}
.preview_window{position:relative; background:#fff; padding:10px;}
.preview_window .btn{position:absolute; right:10px; top:0px; opacity:0; transition:all .4s ease-in-out;}
.preview_window:hover .btn{top:20px; opacity:1}

.rimgbtn{position:relative; width:auto; margin-bottom:15px; color:#555; font-weight:600; padding:2px 32px 2px 50px; display:inline-block; height:44px; line-height:36px; border-radius:25px; 
 border:1px solid #000; text-decoration:none;}
.rimgbtn i{height:38px; width:38px; border-radius:50%; overflow:hidden; background:#fff; position:absolute; left:2px; top:2px;text-align:center;}
.rimgbtn img{height:26px; opacity:.8;} 
.rimgbtn:hover{ border-color:#ccc; background: rgb(29,82,140);
background: linear-gradient(0deg, rgba(29,82,140,1) 0%, rgba(41,98,161,1) 100%); color:#fff; text-decoration:none;}
.rimgbtn:hover img{opacity:1;}

.rimgbtn span{position:absolute; right:6px; top:10px; color:#333; border-color: rgb(29,82,140);;}
.rimgbtn:hover span{color:#fff;}
@media screen and (min-width: 768px){
.rimgbtn{width:240px; display:block;}
}
.activetab{border-color:#ccc; background: rgb(29,82,140);
background: linear-gradient(0deg, rgba(29,82,140,1) 0%, rgba(41,98,161,1) 100%); color:#fff; text-decoration:none;}


.activetab span{color:#fff;}
</style>
<div class="row align-items-center mb-3">
		     <div class="col-md-8"><h3>Printing Configuration(<?php echo ucwords($type);?>-<?php echo ucwords($master_id);?>-<?php echo ucwords($submaster_id);?>)</h3></div>
		     <div class="col-md-4 text-end">
			    <div class="taskmenus">
					<a class="hideinline-md collapsed" data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>       
					<a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a>
					<a href="#"><span class="material-symbols-outlined">print</span></a>
					<a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">download</span></a>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" href="#">CSV</a></li>
						<li><a class="dropdown-item" href="#">Excel</a></li>
						<li><a class="dropdown-item" href="#">Document</a></li>
					</ul>
					<a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">share</span></a> 
					  <ul class="dropdown-menu">
						<li><a class="dropdown-item" href="#">Facebook</a></li>
						<li><a class="dropdown-item" href="#">Twitter</a></li>
						<li><a class="dropdown-item" href="#">Instagram</a></li>
					  </ul>       
                </div>
            </div>             
            <div class="listmenu collapse" id="listmenu" style="">
               <div class="float-md-end d-inline-block">               
			   <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm showinline-md">« Back</a>

				 </div> 
              
			  
			  
			  
			  </div> 
			  <?php if(strtolower($master_id)=='sales' || strtolower($master_id)=='purchases'){?>
			  <div class="listmenu collapse" id="listmenu" style="">
			     <div class="float-md-end d-inline-block"> 
				  <br>Filter themes by: <select name="itemfilter" class="form-control" id="itemfilter">
				  <?php if($set_template=='with_item'){ ?>
				  <option value="withitem" selected>With Item</option>
				  <option value="withoutitem">Without Item</option></select>
			  	
				  <?php } elseif($set_template=='without_item'){  ?>
				  <option value="withitem">With Item</option>
				  <option value="withoutitem" selected>Without Item</option></select>
			  	
				  
				  <?php } else { ?>
				  <option value="withitem" selected>With Item</option>
				  <option value="withoutitem">Without Item</option></select>
			  	
				  
				  <?php } ?>
				  
				  
				  	
				 </div>
			  </div>
			  <?php } ?>

    </div>

<div class="row"><!--
<div class="col-md-3 mt-md-1">
   <?php //if($masters_array){ foreach($masters_array as $row){ ?>
     <a href="javascript:void(0);" data-id="div-<?php //echo strtolower($row);?>" class="openmaster_div btn btn-primary d-block m-1"><?php //echo ucwords($row);?></a>

   <?php //} } ?>       
</div>
-->


<div class="col-md-12">
   <div class="row align-items-start my-3">
		<div class="col-md-3 pt-md-2 listmenu collapse" id="listmenu">
   <?php
   
   if($masters_array){ foreach($masters_array as $row){
   	  	   if(strtolower(trim($master_id))==strtolower($row)){
		 	$stylecss='background: linear-gradient(0deg, rgba(29, 82, 140, 1) 0%, rgba(41, 98, 161, 1) 100%);color: #fff;';
		    $spancolor = 'color: #fff;'; 
		   }else
			   $stylecss=$spancolor='';

	   ?>
   <a href="<?php echo $base_url;?>printing_config/master_configuration/transactions" data-id="div-<?php echo strtolower($row);?>" class="openmaster_div rimgbtn i1" style="<?php echo $stylecss;?>"><i><img src="<?php echo $icons_array[$row];?>"></i> <?php echo ucwords($row);?> <span class="material-symbols-outlined" style="<?php echo $spancolor;?>">chevron_right</span></a>
   <?php } } ?>       
</div>

<div class="col-md-9 my-3">
 <div class="row">
 <?php
 $show_default_template='';
 if(isset($get_default_template['usr_config_value']))
   $show_default_template=$get_default_template['usr_config_value'];
  	  
 if(strtolower($master_id)=='banking'){?>
  <div class="col-md-6 my-3">
      <center><h5>Traditional Theme</h5></center>
	  <?php if($show_default_template==100){ ?>
	   <div class="shadow-lg"><center><a data-id="100" href="javascript:void(0);" class="btn btn-sm btn-success">Default</a></center>
         <a href="javascript:void(0);" title="Traditional Theme" class="changesettings_btn" data-id="100" data-ajax="Traditional Theme"><img src="<?php echo base_url();?>/public/invoice/invoice1.jpg"></a>
      </div>
	  <?php } else { ?>
	   <div class="preview_window shadow-lg"><a data-id="100" href="javascript:void(0);" class="markedefault_btn btn btn-sm btn-success">Set as Default</a>
         <a href="javascript:void(0);" title="Traditional Theme" class="changesettings_btn" data-id="100" data-ajax="Traditional Theme"><img src="<?php echo base_url();?>/public/invoice/invoice1.jpg"></a>
      </div>
	  <?php } ?>
     
    </div>

  <div class="col-md-6 my-3">
     <center><h5>Modern Theme</h5></center>
      <?php if($show_default_template==126){ ?>
	   <div class="shadow-lg"><center><a data-id="126" href="javascript:void(0);" class="btn btn-sm btn-success">Default</a></center>
           <a href="javascript:void(0);" title="Modern Theme" class="changesettings_btn" data-id="126" data-ajax="Modern Theme"><img src="<?php echo base_url();?>/public/invoice/invoice2.jpg"></a>
      </div>
	  <?php } else{ ?>
	   <div class="preview_window shadow-lg"><a data-id="126" href="javascript:void(0);" class="markedefault_btn btn btn-sm btn-success">Set as Default</a>
           <a href="javascript:void(0);" title="Modern Theme" class="changesettings_btn" data-id="126" data-ajax="Modern Theme"><img src="<?php echo base_url();?>/public/invoice/invoice2.jpg"></a>
      </div>
	  
	  <?php } ?>
	  
	 
    </div>
 <?php } ?>
 <?php if(strtolower($master_id)=='sales' && $set_template=='with_item'){?>
  <div class="col-md-6 my-3 withitem_theme">
     <center><h5>MODERN THEME WITH ITEM</h5></center>
	 <?php if($show_default_template==270){ ?>
	   <div class="shadow-lg"><center><a data-id="270" href="javascript:void(0);" class="btn btn-sm btn-success">Default</a></center>
           <a href="javascript:void(0);" title="Modern Theme With Item" class="changesettings_btn" data-id="270" data-ajax="Modern Theme With Item"><img src="<?php echo base_url();?>/public/invoice/invoice3.jpg"></a>
      </div>
	  <?php } else{ ?>
	   <div class="preview_window shadow-lg"><a data-id="270" href="javascript:void(0);" class="markedefault_btn btn btn-sm btn-success">Set as Default</a>
           <a href="javascript:void(0);" title="Modern Theme With Item" class="changesettings_btn" data-id="270" data-ajax="Modern Theme With Item"><img src="<?php echo base_url();?>/public/invoice/invoice3.jpg"></a>
      </div>
	  
	  <?php } ?>
	  
    </div>	
 <?php } ?>
 <?php if(strtolower($master_id)=='sales' && $set_template=='without_item'){?>
	 <div class="col-md-6 my-3 withoutitem_theme" >
     <center><h5>MODERN THEME WITHOUT ITEM</h5></center>
	<?php if($show_default_template==271){ ?>
	   <div class="shadow-lg"><center><a data-id="271" href="javascript:void(0);" class="btn btn-sm btn-success">Default</a></center>
           <a href="javascript:void(0);" title="Modern Theme Without Item" class="changesettings_btn" data-id="271" data-ajax="Modern Theme Without Item"><img src="<?php echo base_url();?>/public/invoice/invoice4.jpg"></a>
      </div>
	  <?php } else{ ?>
	   <div class="preview_window shadow-lg"><a data-id="271" href="javascript:void(0);" class="markedefault_btn btn btn-sm btn-success">Set as Default</a>
           <a href="javascript:void(0);" title="Modern Theme Without Item" class="changesettings_btn" data-id="271" data-ajax="Modern Theme Without Item"><img src="<?php echo base_url();?>/public/invoice/invoice4.jpg"></a>
      </div>	  
	  <?php } ?>
	
    </div>
 <?php } ?>
	</div>
  </div>  

</div>
  
 </div></div>

  
<?php echo view('includes/footer_scripts'); ?>
<script>
load_theme();

function load_theme(){
	var themeval = $("#itemfilter").val();
	if(themeval=='withitem'){
	  $(".withitem_theme").show();
      $(".withoutitem_theme").hide();
	 }
    if(themeval=='withoutitem'){
		$(".withitem_theme").hide();
		$(".withoutitem_theme").show();
	 }	
}
$("#itemfilter").on("change",function(){
	if($(this).val()=='withitem'){
	 
window.location.href='<?php echo base_url();?>/admin/printing_config/setdefault_template/transactions/sales/sale invoice';	  
	 }
	 if($(this).val()=='withoutitem'){
	
		window.location.href='<?php echo base_url();?>/admin/printing_config/setdefault_template/transactions/sales/sale invoice without';
	 }
});
$(".markedefault_btn").on("click",function(){
  var userconfigid = $(this).data("id");	  
  window.location.href="<?php echo $base_url;?>printing_config/setdefault_template/<?php echo $type;?>/<?php echo $master_id;?>/<?php echo $submaster_id;?>/"+userconfigid;
return false;
});

$(".changesettings_btn").on("click",function(){
  var userconfigid = $(this).data("id");	  
  window.location.href="<?php echo $base_url;?>printing_config/print_settings/<?php echo $type;?>/<?php echo $master_id;?>/<?php echo $submaster_id;?>/"+userconfigid;
return false;
});
</script>
</body>
</html>
