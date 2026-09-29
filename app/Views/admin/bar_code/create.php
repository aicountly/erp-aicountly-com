<?php $header = array( 	'title' => 'Create Barcode' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>

<div class="row mb-2">
<div class="col-md-6 order-1"><h3>Create Barcode</h3></div>
<div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
     <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>   
   <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
    <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 

    <a href="#"><span class="material-symbols-outlined open-comingsoon">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined open-comingsoon">download</span></a>
    <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">CSV</a></li>
    <li><a class="dropdown-item" href="#">Excel</a></li>
    <li><a class="dropdown-item" href="#">Document</a></li>
  </ul>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon">share</span></a> 
      <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Facebook</a></li>
    <li><a class="dropdown-item" href="#">Twitter</a></li>
    <li><a class="dropdown-item" href="#">Instagram</a></li>
  </ul>
     </li>
  <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>  
    </div>
    </div> 

 <div class="col-md-6 order-2 order-md-3">
 
   <a href="<?php echo base_url();?>/admin/bar_code/create_item_barcode"><button class="btn btn-success m-1" type="button">Create Item Barcode</button></a> 
   <a href="<?php echo base_url();?>/admin/bar_code/create_batchwise_barcode"><button class="btn btn-success m-1" type="button">Create Batch Wise Bar Code</button></a>
   <a href="<?php echo base_url();?>/admin/bar_code/create_tracking_barcode"><button class="btn btn-success m-1" type="button">Create Tracking Barcode</button></a>
		
  
 </div>  
  <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
      <button class="btn btn-success m-1" type="button">View</button> 
        <a href="<?php echo history_back();?>" class="btn btn-outline-success showinline-md">Back</a>
  </div> 
</div>


<div class="offcanvas offcanvas-end" tabindex="-1" id="moreoptions" aria-labelledby="moreoptionslabel">
  <div class="offcanvas-header">
    <h4 class="offcanvas-title" id="moreoptionslable">Apps</h4>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
      <div class="row">
       
       <div class="col-sm-6 border-end"> 
       <h5 class="pb-3">Horizontal</h5>
       
      <p class="offcanvaoptions"><i>Condensed</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
      
      <p class="offcanvaoptions"><i>Detailed</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
      
      <p class="offcanvaoptions"><i>All Labels</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
       </div>
       
       <div class="col-sm-6"> 
       <h5 class="pb-3">Verticle</h5>
       
      <p class="offcanvaoptions"><i>Verticle</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
      
      <p class="offcanvaoptions"><i>Schudle</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
       </div>
       
        <div class="col-sm-12 pt-3 border-top"> 
      <p class="offcanvaoptions"><i>Schedule</i>
      <label class="form-check">No<input class="form-check-input mx-1" name="schedule" type="radio" value="no" id="swap"></label>
      <label class="form-check">Yes<input class="form-check-input mx-1" name="schedule" type="radio" value="yes" id="swap"></label>
      </p>
      <p class="offcanvaoptions"><i>Ratio</i>
      <label class="form-check">No<input class="form-check-input mx-1" name="ratio" type="radio" value="no" id="swap"></label>
      <label class="form-check">Yes<input class="form-check-input mx-1" name="ratio" type="radio" value="yes" id="swap"></label>
      </p>
      
      <p class="text-center pt-3"><a  data-bs-toggle="modal" data-bs-target="#moreoptionsmodal" class="btn btn-outline-success">View</a></p>
        
        </div>
       
          
      </div>
   
  </div>
</div>

<!-- Modal -->
<div class="modal fade mt-5" id="moreoptionsmodal" tabindex="-1" aria-labelledby="moreoptionsmodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="moreoptionsmodalLabel">App Options title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>



</div> 

<div id="validation_errors"></div>

<div>
    <span>Filter: </span>
    <div class="form-check-inline">
      <input type="radio" class="form-check-input grid_radio_btn" id="all_barcodes" name="optradio" value="all_barcodes" checked>
      <label class="form-check-label" for="all_barcodes">All Records</label>
    </div>
    <div class="form-check-inline">
      <input type="radio" class="form-check-input grid_radio_btn" id="existing_barcodes" name="optradio" value="existing_barcodes">
      <label class="form-check-label" for="existing_barcodes">Existing Barcodes</label>
    </div>
    <div class="form-check-inline">
      <input type="radio" class="form-check-input grid_radio_btn" id="unmapped_barcodes" name="optradio" value="unmapped_barcodes">
      <label class="form-check-label" for="unmapped_barcodes">Unmapped Barcodes</label>
    </div>
</div>


<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
    

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
    <div class="col-12 text-center">
    
         <br><br>
         <!--<input type="file" class="">-->
         
        <button type="button" id="submitbtn" class="btn btn-success btn-lg" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">Save</button>
        <button type="reset" id="submitbtn" class="btn btn-success btn-lg">Reset</button>
        <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-secondary btn-lg">Quit</a>
        
  
        
    </div>
   
</form>
<?php echo view('includes/footer_scripts'); ?>
<style>
  .boldcell{font-weight:700;}
</style>

<script>

    $('input.grid_radio_btn').change(function() {

        var data_type = this.value;
        $( "#grid_search" ).pqGrid( "option", "dataModel.postData", function( ui ){
            return {data_type: data_type};
        } );

        $( "#grid_search" ).pqGrid( "refreshDataAndView" )
        
    });

    var colModel = [

        { title: "UPC", align:"left", width: 180,   dataIndx: "item_upc" },
        { title: "Item Name", align:"left", width: 180,   dataIndx: "item_name" },
        { title: "No. of Barcodes", align:"left", width: 180,   dataIndx: "no_of_barcodes" },
        { title: "No. of Labels", align:"left", width: 180,   dataIndx: "no_of_labels" },
        { title: "Barcode", align:"left", width: 180,   dataIndx: "barcode" },
        { title: "Barcode Type", align:"left", width: 180,   dataIndx: "barcode_standard" },
    ];
            
    var dataModel = {

        location : "remote",
        dataType : "json",
        method   : "POST",
        postData : {data_type: 'all_barcodes'},
        url: "<?php echo base_url();?>/admin/bar_code/ajax_item_barcodes",
        getData: function (dataJSON) {
            var data = dataJSON.data;
            gridDataModel = dataJSON.data;
            return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
        }
    };
     var newObj = {
        scrollModel: { autoFit: true },
        height: 'flex',
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
        selectionModel: { type: 'row',mode:'single' },
        pageModel: { type: 'local' },
        dataModel: dataModel,
        pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
        filterModel: { mode: 'OR', type: "remote" },
        colModel : colModel,
        editable: false,
        numberCell: { show: false },
         wrap:false,
        showTitle: true,
        create: function (evt, ui) {// make first row auto selected
              var grid = this,
                $select_row = $(".select-row"),
                data = ui.dataModel.data;
                grid.setSelection({ rowIndx: 0, focus: true });

        },
        
        dataReady:function(event,ui) {
          
        },
        
       
    };

    newObj.rowDblClick = function(event, ui) {
        var rowData            = ui.rowData;
        var item_id            = rowData.item_id;
        var barcode_id         = rowData.barcode_id;

        if(!barcode_id){
            alert_notification('Barcode does not exists');
            return false;
        }
        else{
            window.location.href= baseurl+'/admin/bar_code/manage_item_barcode/'+item_id; 
        }

        
    }
         
    newObj.cellKeyDown = function(evt, ui) {
       var rowData          = ui.rowData;
       var item_id          = rowData.item_id;
       var barcode_id       = rowData.barcode_id;
      
       if (evt.keyCode==13){
            if(!barcode_id){
            alert_notification('Barcode does not exists');
                return false;
            }
            else{
                window.location.href= baseurl+'/admin/bar_code/manage_item_barcode/'+item_id; 
            }   
       }
    }
      
         
    var $grid = $("#grid_search").pqGrid(newObj);

</script>
</body>
</html>
