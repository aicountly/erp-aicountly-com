<?php $header = array(  'title' => 'All Companies' ); ?>
<?php echo view('includes/header2',$header); ?>

<div class="row mb-4 myalltabs px-3 bg bg-success">
  <div class="col-12" id="tabslistings" style="display:inline-block;width:auto;">
    <a href="<?php echo base_url();?>/home/companies" >
      <span id="#" class="btn tabslist active">All Companies</span>
    </a>
    <a href="<?php echo base_url();?>/home/open_company" >
      <span id="#" class="btn tabslist">My Company</span>
    </a>
    <a href="<?php echo base_url();?>/sharedwithme" >
      <span id="#" class="btn tabslist">Shared With Me</span>
    </a>
    <a href="<?php echo base_url();?>/archivecompany" >
      <span id="#" class="btn tabslist">Archive Company</span>
    </a>
    <a href="<?php echo base_url();?>/groupcompany" >
      <span id="#" class="btn tabslist">Group Company</span>
    </a>
  </div>
</div>

<div class="content">
  <div class="pb-3">

    <div class="listmenu pb-4" id="listmenu">
      <a href="<?php echo base_url();?>/groupcompany/add" class="btn btn-success">New Company</a>
      <a href="javascript:void(0);" class="btn btn-success opencompanybtn" id="opencompanybtn">Open Company</a>
      <a href="javascript:void(0);" class="btn btn-success editcompanybtn">Edit Company</a>
      <a href="javascript:void(0);" class="btn btn-success delcompanybtn">Delete Company</a>
      <a href="<?= base_url() ?>/home/companies" class="btn btn-success">All Companies</a>
    </div>

    <div id="open_company_search"></div> 

  </div>


  <div class="modal fade" id="FaqModal" tabindex="-1" aria-labelledby="FaqModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="" id="FaqModalLabel">
            <img src="<?php echo base_url();?>/public/assets/img/e-sahayak-slogo.png" style="width:80px;" alt=""/> E-Sahayak for Campanies</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">

            <p>Check your list of companies or modify them</p>
            <ul>
              <li>Add/Edit or Delete a Company</li>
              <li>Manage your entire comapanies at single setp</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="ShortcutModal" tabindex="-1" aria-labelledby="ShortcutModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="" id="ShortcutModalLabel">
              <img src="<?php echo base_url();?>/public/assets/img/icon5.png" style="width:80px;" alt=""/> Shortcuts for Campanies</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
              </button>
            </div>
            <div class="modal-body">
              <p>Check your list of Shortcuts to manage your comapny</p>
              <ul>
                <li>Add/Edit or Delete a Company</li>
                <li>Manage your entire comapanies at single setp</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

    


<?php echo view('includes/footer_scripts'); ?>     
  
 <script>

  $(function () {
         
    function filterhandler(evt, ui) {

        var $toolbar = $grid.find('.pq-toolbar-search'),
            $value = $toolbar.find(".filterValue"),
            value = $value.val(),
            condition = $toolbar.find(".filterCondition").val(),
            dataIndx = '',
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
      { title: "COMPANY CODE", width: 100, dataIndx: "comp_code" },
      { title: "COMPANY NAME", width: 180, dataIndx: "comp_name" },
      { title: "COMPANY SHORT NAME", width: 180, dataIndx: "comp_alias" },
      { title: "FIN YEAR(S)", width: 180, dataIndx: "comp_fy_str" },
      { title: "MEMBER", width: 40, dataIndx: "count",
        render: function( ui ) {
          var rd = ui.rowData;
            return '<span class="badge rounded-pill bg-success">'+rd.count+'</span>';
        }
      },
    ];

    var dataModel = { data : <?= json_encode($data) ?>};
    var newObj = {
        scrollModel: { autoFit: true },
        height: 'flex',
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
        selectionModel: { type: 'row',mode:'single' },
         pageModel: { type: "local", rPP: 10, },
        dataModel: dataModel,
        colModel : colModel,
        filterModel: { mode: 'OR', type: "local" },
        editable: false,
        showTitle: true,
        create: function (evt, ui) {// make first row auto selected
              var grid = this,
                $select_row = $(".select-row"),
                data = ui.dataModel.data;
               grid.setSelection({ rowIndx: 0, focus: true });
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
                
            ]
        }
    };
      
    newObj.rowDblClick = async function(e, ui) {
      var rowData      = ui.rowData;
      var encomp_id = rowData.encomp_id;
       
      select_company(encomp_id);
    };
     
    newObj.cellKeyDown= async function(evt, ui) {
      var rowData    = ui.rowData;
      var encomp_id = rowData.encomp_id;

      if (evt.keyCode==13){
       select_company(encomp_id)
      }
    };
     
    var $grid = $("#open_company_search").pqGrid(newObj);
      
    $(".opencompanybtn").on("click",function(){
       
      var selectionArray = $("#open_company_search").pqGrid("selection", {type: 'row', method: 'getSelection'});
      var rowData = selectionArray[0]['rowData'];
      var encomp_id = rowData.encomp_id;
       
      select_company(encomp_id);
    });

    $(".editcompanybtn").on("click",function(){
       
      var selectionArray = $("#open_company_search").pqGrid("selection", {type: 'row', method: 'getSelection'});

      if(selectionArray.length==0){
        alert_notification("First select a group to edit!!");
      }else{    
        var rowData = selectionArray[0]['rowData'];
        var encomp_id = rowData.encomp_id;

        window.location.href= baseurl+'/groupcompany/modify/'+encomp_id;
      }
    });

    $(".delcompanybtn").on("click",function(){
      var selectionArray = $("#open_company_search").pqGrid("selection", {type: 'row', method: 'getSelection'});
      if(selectionArray.length==0){
        alert_notification("First select a group to delete!!");
      }
      else{   
        var rowData = selectionArray[0]['rowData'];
        var encomp_id = rowData.encomp_id;

        window.location.href= baseurl+'/groupcompany/remove_group/'+encomp_id;
      }
    });
    

    async function select_company(encomp_id)
    {
      $(".siteloader").show();
      let start_time = performance.now();

      var interval   = setInterval(function(){
      var currenttime  = performance.now();
      var pct =((currenttime - start_time) / currenttime*100).toFixed(2);
      $('#progressbar').css({width: pct+"%"}, {duration: 2000, easing: 'linear'});
      $('#progressbar').attr('aria-valuenow',pct);
      $('#progressbar .progress-bar').css("width", pct+"%");
      $('#progressbar .progress-bar').text(pct+"%");

      }, 1000); 

      let response = await fetch(baseurl+'/groupcompany/select_company/'+encomp_id);

      let result = await response.json();
      console.log(result);
      requestTime = performance.now();
      if(result){
        clearInterval(interval);
        $('#progressbar').css("width","100%");
        $('#progressbar .progress-bar').css("width", "100%");
        $('#progressbar .progress-bar').text("100%");

        if(result.status)
          window.open(baseurl+'/grpcomp', '_self').focus();
        else
          alert_notification(result.message);
      }
    }

  });
    
</script> 