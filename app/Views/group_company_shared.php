<?php $header = array( 	'title' => 'Group Shared with me' ); ?>
<?php echo view('includes/header2',$header); ?>

    <div class="row mb-4 myalltabs px-3 bg bg-success">
      <div class="col-12" id="tabslistings" style="display:inline-block;width:auto;">
        <a href="<?php echo base_url();?>/home/companies" ><span id="#" class="btn tabslist">All Companies</span></a>
		<a href="<?php echo base_url();?>/home/open_company" ><span id="#" class="btn tabslist">My Company</span></a>
		<a href="<?php echo base_url();?>/sharedwithme" ><span id="#" class="btn tabslist active">Shared With Me</span></a>
		<a href="<?php echo base_url();?>/archivecompany" ><span id="#" class="btn tabslist">Archive Company</span></a>
		<a href="<?php echo base_url();?>/groupcompany" ><span id="#" class="btn tabslist">Group Company</span></a>
      </div>
    </div>

    <div class="content"><div class="pb-3">
        <h3>Group Companies Shared</h3> 
        <br>
      <div class="listmenu pb-4" id="listmenu">	
    <a href="javascript:void(0);" class="btn btn-success opencompanybtn">Remove Access</a>
    <a href="javascript:void(0);" class="btn btn-success opencompanybtn" id="opencompanybtn">Open Company</a>

    <a href="<?= base_url() ?>/sharedwithme" class="btn btn-success">Shared Companies</a>
    </div>
    <!--<a href="javascript:void(0);" class="btn btn-success editcompanybtn">Edit Company</a>
    <a href="javascript:void(0);" class="btn btn-success delcompanybtn">Delete Company</a>-->
  <div id="open_company_search"> </div> 
 
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
            { title: "SHARED BY", width: 180, dataIndx: "shared_by" },
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
  	     	var rowData    = ui.rowData;
		    var company_id = rowData.comp_id;
		    
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
            e.preventDefault();
       
         let response = await fetch('<?= base_url() ?>/groupcompany/select_company/'+company_id+'/0');
            
            let result = await response.json();
            requestTime = performance.now();
           if(result.status){
            clearInterval(interval);
            $('#progressbar').css("width","100%");
            $('#progressbar .progress-bar').css("width", "100%");
            $('#progressbar .progress-bar').text("100%");
             window.open('<?= base_url() ?>/grpcomp/dashboard',"_self").focus();
           }
           
		   // window.location.href= baseurl+'/groupcompany/shadowlogin/'+company_id;
	     }
	     
	    newObj.cellKeyDown= async function(evt, ui) {
	           var rowData    = ui.rowData;
		       var company_id = rowData.comp_id;
		       if (evt.keyCode==13){
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
            e.preventDefault();
       
         let response = await fetch('<?= base_url() ?>/groupcompany/select_company/'+company_id+'/0');
            
            let result = await response.json();
            requestTime = performance.now();
           if(result.status){
            clearInterval(interval);
            $('#progressbar').css("width","100%");
            $('#progressbar .progress-bar').css("width", "100%");
            $('#progressbar .progress-bar').text("100%");
            window.open('<?= base_url() ?>/grpcomp/dashboard', '_self').focus();
           }
                   
		           
		           
		       }
	          
	       }
	     
        var $grid = $("#open_company_search").pqGrid(newObj);
        
         opencompanybtn.onclick = async (e) => {
         
           var selectionArray = $("#open_company_search").pqGrid("selection", {type: 'row', method: 'getSelection'});
           var rowData = selectionArray[0]['rowData'];
           var company_id = rowData.comp_id;
           
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
            e.preventDefault();
       
         let response = await fetch('<?= base_url() ?>/groupcompany/select_company/'+company_id+'/0');
            
            let result = await response.json();
            requestTime = performance.now();
           if(result){
            clearInterval(interval);
            $('#progressbar').css("width","100%");
            $('#progressbar .progress-bar').css("width", "100%");
            $('#progressbar .progress-bar').text("100%");
            window.open('<?= base_url() ?>/grpcomp/dashboard', '_self').focus();
           }
          // window.location.href= baseurl+'/groupcompany/shadowlogin/'+company_id;
           
           
           };
           
           $(".editcompanybtn").on("click",function(){
         
           var selectionArray = $("#open_company_search").pqGrid("selection", {type: 'row', method: 'getSelection'});
           var rowData = selectionArray[0]['rowData'];
           var company_id = rowData.company_id;
           
           window.location.href= '/admin/company/modify/'+company_id;
           
           
           })
    });
</script> 