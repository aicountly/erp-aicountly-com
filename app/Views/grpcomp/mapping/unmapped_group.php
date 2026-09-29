<?php $header = array( 	'title' => 'Mapped Group Masters' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>

<div class="row mb-2">
<div class="col-md-6 order-1"><h3><?= $title ?></h3></div>
<div class="col-md-6 order-3 order-md-2 text-end">
</div> 

<div class="col-md-9 order-2 order-md-3">
   <a href="<?php echo $base_url;?>mapping/mapped_group/<?php echo $type;?>"><button class="btn btn-success m-1" type="button">Mapped Group Masters</button></a> 
   <a href="<?php echo $base_url;?>mapping/unmapped_group/<?php echo $type;?>"><button class="btn btn-success m-1 active" type="button">Unmapped Group Masters</button></a>
   <a href="<?php echo $base_url;?>mapping/mapped_member/<?php echo $type;?>"><button class="btn btn-success m-1" type="button">Mapped Member Masters</button></a>
	 <a href="<?php echo $base_url;?>mapping/unmapped_member/<?php echo $type;?>"><button class="btn btn-success m-1" type="button">Unmapped Member Masters</button></a> 
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
  
		 
<?php echo view('includes/'.$folder_path.'footer_scripts'); ?>
<style>
  .boldcell{font-weight:700;}
</style>

<script>

  var unmapped_master_list = <?= json_encode($unmapped_master_list) ?>;

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



      var autoCompleteEditor = function (ui) {
        var $inp = ui.$cell.find("input");
        var rd = ui.rowData;

        $inp.autocomplete({
          source:  <?= json_encode($comp_list) ?>,
          selectItem: { on: true }, //custom option
          highlightText: { on: true }, //custom option
          minLength: 0,
          select: function(event, ui) {
            event.preventDefault();         
            rd.comp_id = ui.item.comp_id;
            rd.comp_name = ui.item.value;
            $(this).val(ui.item.value);
          }  
        }).focus(function () {               
            $(this).autocomplete("search", "");
            rd.comp_id = '';
            rd.comp_name = '';              
        }).focusout(function () {
          if(!rd.comp_id){
            rd.comp_id = '';
            rd.comp_name = '';
          } 
        })   
      }

      var autoCompleteEditor2 = function (ui) {
        var $inp = ui.$cell.find("input");
        var rd = ui.rowData;

        if(!rd.comp_id){
          return false;
        }

        $inp.autocomplete({
          source: unmapped_master_list[rd.comp_id],
          selectItem: { on: true }, //custom option
          highlightText: { on: true }, //custom option
          minLength: 0,
          select: function(event, ui) {
            event.preventDefault();         
            rd.grpmp_id = ui.item.grpmp_id;
            rd.comp_master_name = ui.item.value;
            $(this).val(ui.item.value);
          }  
        }).focus(function () {               
            $(this).autocomplete("search", "");
            rd.grpmp_id = '';
            rd.comp_master_name = '';              
        }).focusout(function () {
          if(!rd.grpmp_id){
            rd.grpmp_id = '';
            rd.comp_master_name = '';
          } 
        })   
      }

      var colModel = [

        { title: "MASTER ID", dataIndx: "crs_master_id", width: 20,editable:false},
        { title: "GROUP COMPANY MASTER NAME", dataIndx: "crs_master_name", width: 100},
        { title: "MEMBER COMPANY NAME", dataIndx: "comp_name", width: 100, 
          editor: {             
            type: "textbox",
            init: autoCompleteEditor,
            options: []
          },
        },
        { title: "MEMBER COMPANY MASTER NAME",dataIndx: "comp_master_name", width: 100, 
          editor: {             
            type: "textbox",
            init: autoCompleteEditor2,
            options: []
          }
        },
        { title: "ACTION", dataIndx: "action", width: 20,editable:false,
          render: function( ui ) {
            var rd = ui.rowData;

            return `
              <a href="javascript:void(0);" class="update_btn"><img src="<?= base_url() ?>/public/assets/img/icon-done.png"></a>
              &nbsp;
              <a href="javascript:void(0);" class="delete_btn"><img src="<?= base_url() ?>/public/assets/img/icon-close.png"></a>`;   
          },
          postRender: function (ui) {
            var rowIndx = ui.rowIndx,
                grid = this,
                $cell = grid.getCell(ui);

            $cell.find(".update_btn")
            .bind("click", function (evt) {
                update_mapping(rowIndx, grid);
            });

            $cell.find(".delete_btn")
            .bind("click", function (evt) {
                delete_mapping(rowIndx, grid);
            });
          }
        }      
        ];    

                
      var dataModel = {"data": <?= json_encode($master_lists) ?>}       

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
      postRenderInterval: -1, //synchronous post rendering.
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

  function update_mapping(rowIndx, grid) {
    rowData = grid.getRowData({ rowIndx: rowIndx });

    var crs_master_id = rowData.crs_master_id;
    var comp_id = rowData.comp_id;
    var grpmp_id = rowData.grpmp_id;

    if(!comp_id || !grpmp_id){
      alert_notification('Something went wrong');
      return false;
    }

    $.ajax({
      url: '<?php echo $base_url; ?>mapping/update_master_mapping', 
      type: 'POST',
      data: {crs_master_id: crs_master_id, comp_id: comp_id, grpmp_id: grpmp_id},
      dataType: "json",
      beforeSend: function() {
        $('.pq-grid').pqGrid( "showLoading" );
      },
      success: function (response) {
       
        if (typeof response === 'string') {
          response = JSON.parse(response);
        }
        if(response.status){
          alert_success(response.message);

          var list = unmapped_master_list[comp_id];
          
          index = list.findIndex(x => x.grpmp_id == grpmp_id);
          if(index >= 0){
            list.splice(index, 1);
            unmapped_master_list[comp_id] = list;
          }

          rowData.comp_id = '';
          rowData.comp_name = '';
          rowData.grpmp_id = '';
          rowData.comp_master_name = '';
          grid.refreshRow({ rowIndx: rowIndx });
        }
        else{
          alert_notification(response.message);
          grid.refreshRow({ rowIndx: rowIndx });
        }
          
      },
      complete: function() {
        $('.pq-grid').pqGrid( "hideLoading" );
      },
      error: function (jqXHR, exception) {
        var error_= '';
        if (jqXHR.status === 0) {
            error = 'Not connect.\n Verify Network.';
        } else if (jqXHR.status == 404) {
            error = 'Requested page not found. [404]';
        } else if (jqXHR.status == 500) {
            error = 'Internal Server Error [500].';
        } else if (exception === 'parsererror') {
            error = 'Requested JSON parse failed.';
        } else if (exception === 'timeout') {
            error = 'Time out error.';
        } else if (exception === 'abort') {
            error = 'Ajax request aborted.';
        } else {
            error = 'Uncaught Error.\n' + jqXHR.responseText;
        }
        alert_notification(error);
        grid.refreshRow({ rowIndx: rowIndx });
      },
    });
  }

  function delete_mapping(rowIndx, grid) {
    rowData = grid.getRowData({ rowIndx: rowIndx });

    rowData.comp_id = '';
    rowData.comp_name = '';
    rowData.grpmp_id = '';
    rowData.comp_master_name = '';
    grid.refreshRow({ rowIndx: rowIndx });
  }

 </script>	
</body>
</html>
