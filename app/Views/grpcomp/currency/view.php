<?php $header = array(  'title' => 'Currency' ); ?>

<?php if(!session()->get('ses_company_id')){ ?>

<?php echo view('includes/header2',$header); ?>

<div class="row mb-4 myalltabs px-3 bg bg-success">
  <div class="col-12" id="tabslistings" style="display:inline-block;width:auto;">
    <a href="<?php echo base_url();?>/home/open_company" ><span id="#" class="btn tabslist">My Company</span></a>
    <a href="<?php echo base_url();?>/sharedwithme" ><span id="#" class="btn tabslist">Shared With Me</span></a>
    <a href="<?php echo base_url();?>/archivecompany" ><span id="#" class="btn tabslist">Archive Company</span></a>
  </div>
</div>

<div class="content"><div class="pb-5">

<?php } else { ?>

<?php echo view('includes/header',$header); ?>

<?php } ?>

<style>
.gridtable .row{  display: grid; grid-template-columns:10% 30% 30%  15% 15%;}
.gridtable .foot.row{ grid-template-columns:100% ;}
</style>

<div class="row align-items-center">
    <div class="col-md-6">
        <h3>Currency</h3>
    </div>
    <div class="col-md-6 text-end">
        <div class="taskmenus">
            <a href="<?php echo history_back();?>" class="hideinline-md">
                <span class="material-symbols-outlined">keyboard_double_arrow_left</span>
            </a>
        </div>
    </div>
    
    <div class="collapse listmenu" id="listmenu">
        <a href="<?php echo $base_url;?>currency/add/<?= $company_id ?>" class="btn btn-success">Add Currency</a>
        <a href="javascript:void(0);" class="editbtn btn btn-success">Edit Currency</a>
        <a href="javascript:void(0);" class="deletebtn btn btn-success">Delete Currency</a>
        <a href="javascript:void(0);" class="managebtn btn btn-success">Manage Forex Rates</a>
        
        <div class="float-md-end d-inline-block"> 

        <a href="javascript:void();" onclick="window.history.back()"  class="btn btn-outline-success btn-sm showinline-md">« Back</a>

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

</div></div>

<?php
$json_items = array();

?>

<?php echo view('includes/footer_scripts'); ?>
<script>

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
            { title: "Name", width: 180, dataIndx: "curr_name"},
            { title: "Symbol", width: 180, dataIndx: "curr_symbol"},
            { title: "Initial", width: 180, dataIndx: "curr_initial"},
            { title: "String", width: 180, dataIndx: "curr_string"},
            { title: "Sub String", width: 180, dataIndx: "curr_sub_string"},
           
        ];
        var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
            postData : {'company_id': '<?= $company_id ?>' },
            url: "<?php echo base_url();?>/admin/currency/ajax_list",
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
            var comp_currency_id     = rowData.comp_currency_id;
         
            window.location.href= baseurl+'/admin/currency/edit/<?= $company_id ?>/'+comp_currency_id;
        } 

        newObj.cellKeyDown = function(evt, ui) {
            var rowData      = ui.rowData;
            var comp_currency_id     = rowData.comp_currency_id;
            
            if (evt.keyCode==13){

                window.location.href= baseurl+'/admin/currency/edit/<?= $company_id ?>/'+comp_currency_id;
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
              $(".managebtn").addClass("disabled");
              $(".duplicatebtn").addClass("disabled");
        }else{
              $('.checkbox').each(function(){ this.checked = false; });
              $(".editbtn").removeClass("disabled");
              $(".managebtn").removeClass("disabled");
              $(".duplicatebtn").removeClass("disabled");
           }
       });
    
   $(document).on('click',".editbtn",function(){
     var sel_id = $('.selected_cell').data('id'); 
     var ischeckled =  $('.items_row:checked').length;  
      if(ischeckled==0){
          alert_notification("First select a currency to edit!!");
       }
      else{
       if(sel_id!='')
        window.location.href=baseurl+"/admin/currency/edit/<?= $company_id ?>/"+sel_id;
      else
       return false;    
      }
    })

   $(document).on('click',".managebtn",function(){
     var sel_id = $('.selected_cell').data('id'); 
     var ischeckled =  $('.items_row:checked').length;  
      if(ischeckled==0){
          alert_notification("First select a currency to manage!!");
       }
      else{
       if(sel_id!=''){
        if(sel_id == 1){
            alert_notification("Forex rates for INR does not exists!!");
            return false;
        }
        else
            window.location.href=baseurl+"/admin/currency/manage_forex_rates/<?= $company_id ?>/"+sel_id;
       }
       
      else
       return false;    
      }
    })
 
 $(document).on('click',".deletebtn",function(){
    var ischeckled =  $('.items_row:checked').length;  
      if(ischeckled==0){
          alert_notification("First select a currency to delete!!");
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
             confirm_delete(baseurl+"/admin/currency/delete/<?= $company_id ?>/"+checkedVals.join(","));         
         else
            return false;
       }
    })
    

 
    $(document).on('click','.items_row', function(e) {   
        $(':checkbox').prop('checked', false);
        $(".editbtn").removeClass("disabled");

        $('.items_row').removeClass('selected_cell');
        $(this).addClass('selected_cell');

        if($(this).is(":checked"))
            $(this).prop('checked', false);       
        else
         $(this).prop('checked', true);                           
    });

</script>
</body>
</html>