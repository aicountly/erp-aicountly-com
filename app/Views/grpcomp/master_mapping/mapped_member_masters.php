<?php $header = array( 	'title' => 'Mapped Member Masters' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>

<div class="row mb-2">
<div class="col-md-6 order-1"><h3>Mapped Member Masters</h3></div>
<div class="col-md-6 order-3 order-md-2 text-end">
    </div> 

 <div class="col-md-9 order-2 order-md-3">
 
   <a href="<?php echo $base_url;?>master_mapping/mapped_group_masters?criteria=<?php echo $sel_criteria;?>"><button class="btn btn-success m-1 " type="button">Mapped Group Masters</button></a> 
   <a href="<?php echo $base_url;?>master_mapping/ummapped_group_masters?criteria=<?php echo $sel_criteria;?>"><button class="btn btn-success m-1 " type="button">Unmapped Group Masters</button></a>
   <a href="<?php echo $base_url;?>master_mapping/mapped_member_masters?criteria=<?php echo $sel_criteria;?>"><button class="btn btn-success m-1 active" type="button">Mapped Member Masters</button></a>
	<a href="<?php echo $base_url;?>master_mapping/unmapped_member_masters?criteria=<?php echo $sel_criteria;?>"><button class="btn btn-success m-1" type="button">Unmapped Member Masters</button></a>
		
  
 </div>  
  <div class="col-md-3 text-md-end order-4 collapse listmenu" id="listmenu">
     
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
  
		 
<?php echo view('includes/'.$folder_path.'footer_scripts'); 

$json_data = $master_lists;  
 
?>
<style>
  .boldcell{font-weight:700;}
</style>

<script>

     $(function () {
       function filterhandler(evt, ui) {

            var $toolbar = $grid.find('.pq-toolbar-search'),
                $value = $toolbar.find(".filterValue"),
                value = $value.val(),
                condition = $toolbar.find(".filterCondition").val(),
                dataIndx = $toolbar.find(".filterColumn").val(),
                filterObject;

            if (dataIndx == "") {//search through all fields when no field selected.
                filterObject = [];
                var CM = $grid.pqGrid("getColModel");
                for (var i = 0, len = CM.length; i < len; i++) {
                    var dataIndx = CM[i].dataIndx;
                    filterObject.push({ dataIndx: dataIndx, condition: condition, value: value });
                }
            }
            else {//search through selected field.
                filterObject = [{ dataIndx: dataIndx, condition: condition, value: value}];
            }
            $grid.pqGrid("filter", {
                oper: 'replace',
                data: filterObject
            });
        }
        var colModel = [
		 { title: "MEMBER COMPANY NAME", dataIndx: "member_company_name", width: 100,editable:false},
		 { title: "MEMBER COMPANY MASTER NAME",dataIndx: "member_company_master_name", width: 100,editable:false},		   
		 { title: "MASTER ID", dataIndx: "master_id", width: 100,editable:false},
         { title: "GROUP COMPANY MASTER NAME", dataIndx: "group_company_master_name", width: 100},
         { title: "ACTION", dataIndx: "action", width: 100,editable:false}      
		         
         ];            
        var dataModel = {"data": <?= json_encode($json_data) ?>}       
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            scrollModel: { autoFit: true },
           pageModel: { type: 'local' },
			filterModel: { mode: 'OR', type: "local" },
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
              },
			  toolbar: {
                cls: "pq-toolbar-search",
                items: [
                    { type: "<span style='margin:5px;'>Filter</span>" },
                    { type: 'textbox', attr: 'placeholder="Enter your keyword"', cls: "filterValue", listeners: [{ 'change': filterhandler}] },
                    { type: 'select', cls: "filterColumn",
                        listeners: [{ 'change': filterhandler}],
                        options: function (ui) {
                            var CM = ui.colModel;
                            var opts = [{ '': '[ All Fields ]'}];
                            for (var i = 0; i < CM.length; i++) {
                                var column = CM[i];
                                var obj = {};
                                obj[column.dataIndx] = column.title;
                                opts.push(obj);
                            }
                            return opts;
                        }
                    },
                    { type: 'select', style: "margin:0px 5px;", cls: "filterCondition",
                        listeners: [{ 'change': filterhandler}],
                        options: [
                        { "begin": "Begins With" },
                        { "contain": "Contains" },
                        { "end": "Ends With" },
                        { "notcontain": "Does not contain" },
                        { "equal": "Equal To" },
                        { "notequal": "Not Equal To" },
                        { "empty": "Empty" },
                        { "notempty": "Not Empty" },
                        { "less": "Less Than" },
                        { "great": "Great Than" }    
                        ]
                    }
                ]
            },
        };
       
        var $grid = $("#grid_search").pqGrid(newObj);
     
          
        
     });
function update_group_master_row(rowindex,grpmpid,crs_master_id){
	$('.pq-grid').pqGrid( "showLoading" );

  var selectionArray = $('.pq-grid').pqGrid("selection", {type: 'row', method: 'getSelection'});
           var rowData = selectionArray[0]['rowData'];
 
 rowData.grpmpid=grpmpid;
 rowData.crs_master_id=crs_master_id;

 
 $.post('<?php echo base_url();?>/admin/master_mapping/update_group_masters', rowData, function(response) {
	 
	$( ".pq-grid" ).pqGrid( "refreshDataAndView"); 

    $('.pq-grid').pqGrid( "hideLoading" );	 
 });
}

function remove_member_master_row(rowindex,grpmpid,crs_master_id){
	$('.pq-grid').pqGrid( "showLoading" );

 
   var selectionArray = $('.pq-grid').pqGrid("selection", {type: 'row', method: 'getSelection'});
           var rowData = selectionArray[0]['rowData'];
 rowData.grpmpid=grpmpid; 
  $.post('<?php echo $base_url;?>master_mapping/unmap_member_master', rowData, function(response) {
	  $.each(rowData, function(index,obj){
            rowData[index] = '';
        }); 
	$( ".pq-grid" ).pqGrid( "refreshDataAndView"); 

    $('.pq-grid').pqGrid( "hideLoading" );	 
 });

}
 </script>	
</body>
</html>
