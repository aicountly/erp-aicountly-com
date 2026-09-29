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
     $json_company[] = array("log_date"=>$value['log_date'],"log_time"=>$value['log_time'],"log"=>$value['user_name'].' has '.$value['log_action_tags'].(($value['log_action_tags'] == 'add') ? 'ed' : 'd').' '.$value['log_field_type'].' "'.$value['log_field_name'].'" ');
    }
  } 
  $json_company = json_encode($json_company);
  ?>
      
   <?php echo view('includes/footer_scripts'); ?>		  
	
 <script>

     $(function () {
        
        var colModel = [
           
            { title: "Log Date", width: 25, dataIndx: "log_date" },
            { title: "Log Time", width: 25, dataIndx: "log_time" },
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