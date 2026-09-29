<?php $header = array('title' => 'All Credentials'); ?>
<?php echo view('includes/header',$header); 
if($type==1)
 $credential_label ='GST ';	
else if($type==2)
 $credential_label ='TRACES ';		
else if($type==3)
 $credential_label ='INCOME TAX ';	
else
 $credential_label ='ALL ';	
?>
<div class="row align-items-center">
  <div class="col-md-6"> <h3><?php echo $credential_label;?>CREDENTIALS</h3>
  </div>
  <div class="col-md-6 text-end">
    <div class="taskmenus">
      <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>
      <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
      <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a>
      <a href="#"><span class="material-symbols-outlined">print</span></a>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">download</span></a>
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
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">share</span></a>
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
      <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>
    </div>
  </div>
  
  <div class="collapse listmenu" id="listmenu">
    <a href="<?php echo $base_url;?>my_credentials/add" class="btn btn-success">Add</a>
    <a href="javascript:void(0);" class="deletebtn btn btn-success">Delete</a>
    <div class="float-md-end d-inline-block">
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
  <br><br>
  <div id="accounts_grid" style="margin:auto;"> </div>
  
<?php echo view('includes/footer_scripts'); ?>

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
            
         var changeStatus = function (ui) { 

                 var rd = ui.rowData;
                 var $inp = ui.$cell.find("input");
                 ui.rowData.isedited= '1' ;

        }
      
        var colModel = [
            {
                        title: '<label><input id="select_all" type="checkbox"/> Select All </label>',
                        dataIndx: "chkbx",
                        maxWidth: 120,
                        minWidth: 120,
                        type: 'checkbox',
                        cb: {
                            all: false,
                            header: true,
                            check: "YES",
                            uncheck: "NO"
                        },
                        render: function (ui) { 
                            var rowdata = ui.rowData;
                            var cb = ui.column.cb,
                                cellData = ui.cellData,
                                checked = cb.check === cellData ? 'checked' : '',
                                disabled = this.isEditableCell(ui) ? "" : "disabled",
                                text = cb.check === cellData ? 'TRUE' : (cb.uncheck === cellData ? 'FALSE' : '<i>unknown</i>');
                            return {
                                text: "<label><input name='cred_ids[]' class='accounts_row' data-id='"+rowdata.cred_id+"' value='"+rowdata.cred_id+"' type='checkbox' " + checked + " /></label>",
                                style: (disabled ? "background:lightgray" : "")
                            };
                        },
                        editor: false,
                        editable: function (ui) {
                            return !ui.rowData.disabled;
                        }
                    },
          { title: "SITE NAME", width: 100, dataIndx: "cred_site_name",editable: false,filterable:"yes"}, 
          { title: "TYPE", width: 100, dataIndx: "cred_type",editable: false,filterable:"yes"  },
          { title: "USER", width: 100, dataIndx: "cred_user",editable: false, filterable:"no"},
		  { title: "PASSWORD", width: 100, dataIndx: "cred_password",editable: false, filterable:"no"},
          { title: "CLIENT ID", width: 100, dataIndx: "clientid",editable: false, filterable:"no"},
          { title: "SECRET KEY", width: 100, dataIndx: "secret_key",editable: false, filterable:"no"},
            
        ];
         var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
			postData:{'type':'<?php echo $type;?>'},
            url: "<?php echo base_url();?>admin/my_credentials/ajax_credentials_view",
             getData: function (dataJSON) {
          var data = dataJSON.data;       
                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              },
         
           };
        var newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            dataModel: dataModel,
            colModel : colModel,
            numberCell: { show: true},
            filterModel: { mode: 'OR', type: "remote" },
            editable: true,
            editModel: { clicksToEdit: 1},
            showTitle: true,
            
            load:function(event,ui) {
               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
            },            
        };
       
    newObj.rowDblClick    = function(event, ui) {
      var rowData      = ui.rowData;
      var acc_id     = rowData.cred_id;
      // set_page();
      window.location.href= baseurl+'admin/my_credentials/modify/'+acc_id;
    } 
       
    newObj.cellKeyDown = function(evt, ui) {
      var rowData      = ui.rowData;
      var acc_id     = rowData.cred_id;
      //console.log(rowData);
      if (evt.keyCode==13){
        //set_page();
        window.location.href= baseurl+'admin/my_credentials/modify/'+acc_id;
      }
    }

    $grid =  $("#accounts_grid").pqGrid(newObj);
       $("#accounts_grid").pqGrid('loadState'); 
       $(window).unload( function(){
       $("#accounts_grid").pqGrid('saveState');
    });
   });
  
    $(document).on('click','#select_all',function(){
        if(this.checked){
            $('.checkbox').each(function(){
                this.checked = true;        
            });
      $(".editbtn").addClass("disabled");
        }else{
             $('.checkbox').each(function(){
                this.checked = false;
            });
      $(".editbtn").removeClass("disabled");
           }
      });
  

$(document).on('click', '.deletebtn', function () {
  const checked = $('input[name="cred_ids[]"]:checked');
  if (!checked.length) {
    alert_notification("First select a credential to delete!!");
    return;
  }

  const ids = [...new Set(checked.map((_, el) => $(el).val()).get())];
  if (!ids.length) return;

  // URL-safe Base64 (no padding, - and _ instead of + /)
  const raw = ids.join(',');
  const encoded = btoa(raw)
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=+$/, '');

  confirm_delete(baseurl + "admin/my_credentials/remove/" + encoded);
});

$(document).on('click','.accounts_row', function(e) { 
    
            $(':checkbox').prop('checked', false);
        $(".editbtn").removeClass("disabled");
            $(this).addClass('selected_cell').siblings().removeClass('selected_cell');
        if($(this).is(":checked"))
        $(this).prop('checked', false);     
      else
         $(this).prop('checked', true);                       
            });
       var pq_grids = $('.pq-grid');
       $(pq_grids[0]).pqGrid('setSelection', null);   
</script>
</body>
</html>