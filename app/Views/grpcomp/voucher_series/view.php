<?php $header = array(  'title' => 'Voucher Series' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
.gridtable .row{  display: grid; grid-template-columns:10% 30% 30%  15% 15%;}
.gridtable .foot.row{ grid-template-columns:100% ;}
</style>
<div class="row align-items-center">
    <div class="col-md-6">
        <h3>Voucher Series</h3>
    </div>
    <div class="col-md-6 text-end">
        <div class="taskmenus">
            <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu">
                <span class="material-symbols-outlined">filter_list</span>
            </a>
            <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
                <span class="material-symbols-outlined">offline_bolt</span>
            </a>
            <a href="#">
                <span class="material-symbols-outlined">print</span>
            </a>
            <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="material-symbols-outlined">
                    <span class="material-symbols-outlined">download</span>
                </span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="#">CSV</a>
                </li>
                <li>
                    <a class="dropdown-item" href="#">Excel</a>
                </li>
                <li>
                    <a class="dropdown-item" href="#">Document</a>
                </li>
            </ul>
            <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="material-symbols-outlined">share</span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="#">Facebook</a>
                </li>
                <li>
                    <a class="dropdown-item" href="#">Twitter</a>
                </li>
                <li>
                    <a class="dropdown-item" href="#">Instagram</a>
                </li>
            </ul>
            <a href="<?php echo history_back();?>" class="hideinline-md">
                <span class="material-symbols-outlined">keyboard_double_arrow_left</span>
            </a>
        </div>
    </div>
    
    <div class="collapse listmenu" id="listmenu">
        <a href="<?php echo $base_url;?>voucher_series/add" class="btn btn-success">Add Series</a>
        <a href="javascript:void(0);" class="editbtn btn btn-success">Edit Series</a>
        <a href="javascript:void(0);" class="deletebtn btn btn-success">Delete Series</a>
        
        
        <div class="float-md-end d-inline-block"> 

        <div class="dropdown d-inline-block me-1" style="width:220px;"> 
            <div class="input-group input-group-sm input-group">
                <span class="input-group-text">Voucher</span>
                <select class="form-select" name="voucher_type">
                    <?php foreach ($voucher_types as $key => $value) { ?>
                        <option value="<?= $value['voucher_type_id'] ?>"><?= $value['comp_vch_type'] ?></option>
                    <?php } ?>
                </select>
            </div>  
        </div>

        <a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a>

        </div>
    </div>
</div>



<?php if ($session->getFlashdata('message')) { ?>
<div class="alert alert-success alert-dismissible fade show">
    <button type="button" class="btn-close" data-bs-dismiss="alert">
    </button>
    <?php echo $session->getFlashdata('message'); ?>
</div>
<?php } ?>
<?php if ($session->getFlashdata('error_array_message')) { ?>
<div class="alert alert-danger alert-dismissible fade show mt-2" style="max-height: 150px;overflow-y: auto;">
    <button type="button" class="btn-close" data-bs-dismiss="alert">
    </button>
    <?php $errors = $session->getFlashdata('error_array_message'); ?>
    <ul>
        <?php foreach($errors as $error) { ?>
        <li>
            <?php echo $error ?>
        </li>
        <?php } ?>
    </ul>
</div>
<?php } ?>

<div id="validation_errors"></div>

<div class="mt-5" id="search_grid"  style="margin:auto;">
</div>

<?php
$json_items = array();

?>

<?php echo view('includes/footer_scripts'); ?>
<script>

    $(document).on('change', 'select[name="voucher_type"]', function(){
        var voucher_type_id = $(this).val();

        $( "#search_grid" ).pqGrid( "option", "dataModel.postData", function( ui ){
            return {voucher_type_id: voucher_type_id};
        } );
        $( "#search_grid" ).pqGrid( "refreshDataAndView" );
    });

        function filterhandler(evt, ui) {

            var $toolbar = $grid.find('.pq-toolbar-search'),
                $value = $toolbar.find(".filterValue"),
                value = $value.val(),
                condition = $toolbar.find(".filterCondition").val(),
                dataIndx = '',//$toolbar.find(".filterColumn").val(),
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

            { title: '', width: 100, dataIndx: "checkbox" },
            { title: "Series Name", width: 180, dataIndx: "comp_vch_series"},
            { title: "Voucher Numbering", width: 180, dataIndx: "comp_vch_method"},
            { title: "No. of Vouchers", width: 180, dataIndx: "no_of_vouchers"},
           
        ];
        var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
            postData : {'voucher_type_id': $('select[name="voucher_type"]').val() },
            url: "<?php echo base_url();?>/admin/voucher_series/ajax_series_list",
             getData: function (dataJSON) {
                var data = dataJSON.data;
                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              }
        };

        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            dataModel: dataModel,
            colModel : colModel,
            filterModel: { mode: 'OR' },
            numberCell: { show: false },
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".items_row"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
            },
            load:function(event,ui) {
               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
            },
            toolbar: {
                cls: "pq-toolbar-search",
                items: [  
                    { 
                        type: 'textbox', 
                        label: 'Filter: ',
                        attr: 'placeholder="Enter your keyword"', 
                        cls: "filterValue",
                        listener: { keyup: filterhandler }
                    },
                    { 
                        type: 'select',                         
                        cls: "filterCondition",
                        listener: filterhandler,
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
                            { "great": "Great Than" },
                            { "regexp": "Regex" }
                        ]
                    }
                ]
            }
        };

        newObj.rowDblClick    = function(event, ui) {
            var rowData      = ui.rowData;
            var comp_vch_series_id     = rowData.comp_vch_series_id;
         
            window.location.href= baseurl+'/admin/voucher_series/edit/'+comp_vch_series_id;
        } 

        newObj.cellKeyDown = function(evt, ui) {
            var rowData      = ui.rowData;
            var comp_vch_series_id     = rowData.comp_vch_series_id;
            
            if (evt.keyCode==13){

                window.location.href= baseurl+'/admin/voucher_series/edit/'+comp_vch_series_id;
            }
        }
         
     var $grid = $("#search_grid").pqGrid(newObj);

     

         $("#search_grid").pqGrid('loadState'); 
         $(window).unload( function(){
         $("#search_grid").pqGrid('saveState');
       });
    
        
     $(document).on('click','#select_all',function(){
        if(this.checked){
              $('.checkbox').each(function(){ this.checked = true; });
              $(".editbtn").addClass("disabled");
              $(".duplicatebtn").addClass("disabled");
        }else{
              $('.checkbox').each(function(){ this.checked = false; });
              $(".editbtn").removeClass("disabled");
              $(".duplicatebtn").removeClass("disabled");
           }
       });
    
   $(document).on('click',".editbtn",function(){
     var sel_id = $('.selected_cell').data('id'); 
     var ischeckled =  $('.items_row:checked').length;  
      if(ischeckled==0){
          alert_notification("First select a voucher series to edit!!");
       }
      else{
       if(sel_id!='')
        window.location.href=baseurl+"/admin/voucher_series/edit/"+sel_id;
      else
       return false;    
      }
    })
 
 $(document).on('click',".deletebtn",function(){
    var ischeckled =  $('.items_row:checked').length;  
      if(ischeckled==0){
          alert_notification("First select a voucher series to delete!!");
      }
      else{   
         var checkedVals = [];
         $('.items_row:checked').each(function() {
             if(!checkedVals.includes(this.value))
             {
                 checkedVals.push(this.value)
             }
         });
        
         if(checkedVals.length)
             confirm_delete(baseurl+"/admin/voucher_series/delete/"+checkedVals.join(","));         
         else
            return false;
       }
    })
    

 
     $(document).on('click','.items_row', function(e) {   
            $(':checkbox').prop('checked', false);
            $(".editbtn").removeClass("disabled");
            $(this).addClass('selected_cell').siblings().removeClass('selected_cell');
            if($(this).is(":checked"))
                $(this).prop('checked', false);       
                else
                 $(this).prop('checked', true);                           
            });
</script>
</body>
</html>