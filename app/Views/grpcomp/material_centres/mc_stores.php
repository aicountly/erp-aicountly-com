<?php $header = array( 	'title' => 'Material Centre Stores' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>

<div class="row">
		     <div class="col-6">  <h3 class="pb-3">Material Centre Stores</h3></div>
		     <div class="col-md-6 text-end"><div class="taskmenus">
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a>
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
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
             
             <div class="col-md-8">
               <a href="<?php echo $admin_url;?>material_centres/add_mc_store" class="clickable btn btn-success" target="_blank">Add Store</a>
                 <a href="<?php echo $admin_url;?>material_centres/mc_stores" class="clickable btn btn-success" target="_blank">Edit</a>
				<a href="<?php echo $admin_url;?>material_centres/mc_stores" class="clickable btn btn-success" target="_blank">Delete</a> 
                <a href="<?php echo $base_url;?>material_centres/list_centres" class="btn btn-success">Material Centres</a>
                </div>
               <div class="col-md-4 text-end"> <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a> </div> 
               </div>	  


    <?php if ($session->getFlashdata('message')) { ?>
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php echo $session->getFlashdata('message'); ?>
            </div>
    <?php } ?>
    <?php if ($session->getFlashdata('error_array_message')) { ?>
            <div class="alert alert-danger alert-dismissible fade show mt-2" style="max-height: 150px;overflow-y: auto;">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php $errors = $session->getFlashdata('error_array_message'); ?>
                <ul>
                <?php foreach($errors as $error) { ?>
                    <li><?php echo $error ?></li>
                <?php } ?>
                </ul>
            </div>
    <?php } ?>
   <form class="form" action="" method="get" id="salefrm" autocomplete="off" novalidate>
	<br>
    <?php echo form_dropdown('grpcmpid',$comp_list,$grpcmpid,'id="grpcmpid" class="form_control" ');?>
    <div class="mt-5" id="material_stores_grid"></div>
    </form>
    
<?php echo view('includes/'.$folder_path.'footer_scripts'); ?>	  
<script>
$(".clickable").on("click",function(){
		   if($("#grpcmpid").val()=='')
		   {
			  alert_notification("Choose group company first!!!");
			  return false;
		   }
		   else
			   return true;
	   });
	   
	   $("#grpcmpid").on("change",function(){
		   if($(this).val()!=''){
			  $("#salefrm").submit(); 
    
		   }
	   });

     var colModel = [
           
             { title: "Material Centre Name", width: 140, dataIndx: "mc_name" },
            { title: "Store Name", width: 140, dataIndx: "mc_store_name" },
            { title: "Alias", width: 140, dataIndx: "mc_store_alias" },
            
	    	];
        var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
			url: "<?php echo $base_url;?>material_centres/ajax_mcstores_view",
             getData: function (dataJSON) {
				  var data = dataJSON.data;				
                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              },
			   
           };
        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: {  zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel : colModel,
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".groups_row"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
            },
            load:function(event,ui) {
               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
            },
        };
	    
        var $grid = $("#material_stores_grid").pqGrid(newObj);
     
</script>
