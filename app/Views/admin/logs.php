<?php $header = array( 	'title' => 'Logs' ); ?>
<?php echo view('includes/header',$header); ?>
<div class="row g-4">

  <div class="col-12 col-xxl-8">
        <div id="open_company_search"> </div> 
  </div>
</div>
            
            
            
            
            
</div>
   
	  
  <?php 
  $json_company = array();
  if($loglist){ foreach($loglist as $value){
     $json_company[] = array("log_date"=>date("d M, Y h:i A",strtotime($value['erp_activity_date_time'])),"log"=>$value["erp_activity_log"]);
    }
  } 
  $json_company = json_encode($json_company);
  ?>
      
   <?php echo view('includes/footer_scripts'); ?>		  
	
 <script>

     $(function () {
        
        var colModel = [
           
            { title: "Log Date & Time", width: 25, dataIndx: "log_date" },
            { title: "Log", width: 300, dataIndx: "log" },
            
	    	];
        var dataModel = {"data":<?php echo $json_company;?>}
        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel : colModel,
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
            }
        };
	     
        var $grid = $("#open_company_search").pqGrid(newObj);
        
          
           
      
    });
    
    

</script> 	  