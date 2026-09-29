<?php $header = array( 	'title' => 'Unmapped Group Masters' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>

<div class="row mb-2">
<div class="col-md-6 order-1"><h3>Unmapped Group Masters</h3></div>
<div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
     <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>   
   
    <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 

    <a href="#"><span class="material-symbols-outlined">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></a>
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
     </li>
  <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>  
    </div>
    </div> 

 <div class="col-md-9 order-2 order-md-3">
 
   <a href="<?php echo base_url();?>/admin/master_mapping/mapped_group_masters?criteria=<?php echo $sel_criteria;?>"><button class="btn btn-success m-1 " type="button">Mapped Group Masters</button></a> 
   <a href="<?php echo base_url();?>/admin/master_mapping/ummapped_group_masters?criteria=<?php echo $sel_criteria;?>"><button class="btn btn-success m-1 active" type="button">Unmapped Group Masters</button></a>
   <a href="<?php echo base_url();?>/admin/master_mapping/mapped_member_masters?criteria=<?php echo $sel_criteria;?>"><button class="btn btn-success m-1" type="button">Mapped Member Masters</button></a>
	<a href="<?php echo base_url();?>/admin/master_mapping/unmapped_member_masters?criteria=<?php echo $sel_criteria;?>"><button class="btn btn-success m-1" type="button">Unmapped Member Masters</button></a>
		
  
 </div>  
  <div class="col-md-3 text-md-end order-4 collapse listmenu" id="listmenu">
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
<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
    

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
   
</form>
  
		 
<?php echo view('includes/footer_scripts'); 

$json_data = $master_lists;  
 
?>
<style>
  .boldcell{font-weight:700;}
</style>

<script>

     $(function () {
	 var autoCompleteEditor = function (ui) {
        var $inp = ui.$cell.find("input");
        var rd = ui.rowData;
		
		console.log(rd);
        var element= {};
      
        $inp.autocomplete({
                source:  <?php echo json_encode($companies_list);?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    rd.comp_id    = ui.item.comp_id;
                    rd.company_name = ui.item.label;               
                    
                    $(this).val(ui.item.label); 
                 }  
            }).focus(function () {               
                $(this).autocomplete("search", "");
                rd.comp_id = '';
                rd.company_name = '';
            }).focusout(function () {})		
	 }
	 
	 var autoCompleteEditor2 = function (ui) {
        var $inp = ui.$cell.find("input");
        var rd = ui.rowData;
		
		console.log(rd);
        var element= {};
      
        $inp.autocomplete({
                source:  <?php echo json_encode($companies_list);?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    rd.comp_id    = ui.item.comp_id;
                    rd.company_name = ui.item.label;               
                    
                    $(this).val(ui.item.label); 
                 }  
            }).focus(function () {               
                $(this).autocomplete("search", "");
                rd.comp_id = '';
                rd.company_name = '';
            }).focusout(function () {})		
	 }
       function disableTextRenderer(ui) {
                grid = this,
                rowData = ui.rowData,
                rowIndx = ui.rowIndx,
                dataIndx = ui.dataIndx;
            if (grid.isEditableCell({ rowIndx: rowIndx, dataIndx: dataIndx }) == false) {
                grid.addClass({ rowIndx: rowIndx, dataIndx: dataIndx, cls: 'disabled' });
            }
            else {
                grid.removeClass({ rowIndx: rowIndx, dataIndx: dataIndx, cls: 'disabled' });
            }
        };
         
        
        var colModel = [
		
		 { title: "MASTER ID", dataIndx: "master_id", width: 100,editable:false},
         { title: "GROUP COMPANY MASTER NAME", dataIndx: "group_company_master_name", width: 100,editable:false},
		  { title: "MEMBER COMPANY NAME", dataIndx: "member_company_name", width: 100,editor: {             
                          type: "textbox",
                          init: autoCompleteEditor,
                          options: []
                    },},
         { title: "MEMBER COMPANY MASTER NAME",dataIndx: "member_company_master_name", width: 100},
         { title: "ACTION", dataIndx: "action", width: 100,editable:false}      
		         
         ];            
        var dataModel = {"data": <?= json_encode($json_data) ?>}       
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
             height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel: colModel,  
            numberCell: { show: false },
            editable: true,
            cellSave: function(evt, ui){
                   this.refresh();
               },
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            wrap:false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
            this.widget().pqTooltip();
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: true });
              }
        };
        newObj.cellKeyDown = function(evt, ui) {
           var rowData = ui.rowData;
           var rowIndx = ui.rowIndx;

           if (evt.keyCode == 46){
                $.each(rowData, function(index,obj){
                    rowData[index] = '';
                });
                this.refreshDataAndView();
                return false;
           }
        }
        var $grid = $("#grid_search").pqGrid(newObj);
     
          
        
     });
function map_group_master_row(rowindex,crs_master_id){
	$('.pq-grid').pqGrid( "showLoading" );

  var selectionArray = $('.pq-grid').pqGrid("selection", {type: 'row', method: 'getSelection'});
    var rowData = selectionArray[0]['rowData'];

 rowData.crs_master_id=crs_master_id; 
  console.log(rowData);
  return false;
 $.post('<?php echo base_url();?>/admin/master_mapping/map_group_master', rowData, function(response) {
	 
	//$( ".pq-grid" ).pqGrid( "refreshDataAndView"); 

   // $('.pq-grid').pqGrid( "hideLoading" );	 
 });

}

function remove_master_row(rowindex,crs_master_id){
$('.pq-grid').pqGrid( "showLoading" );  
  var selectionArray = $('.pq-grid').pqGrid("selection", {type: 'row', method: 'getSelection'});
    var rowData = selectionArray[0]['rowData'];
 rowData.crs_master_id=crs_master_id; 
 
 console.log(rowData);
 $.post('<?php echo base_url();?>/admin/master_mapping/remove_master', rowData, function(response) {   
    $.each(rowData, function(index,obj){
            rowData[index] = '';
        });    
    $('.pq-grid').pqGrid( "hideLoading" );	
	$( ".pq-grid" ).pqGrid( "refreshDataAndView");
  }); 
}
 </script>	
</body>
</html>
