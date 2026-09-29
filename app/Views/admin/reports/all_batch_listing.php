<?php $header = array( 	'title' => 'All Batches Ledger' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
 .myform label{width:25%; float:left;}
 .myform .form-control, .myform select {width:75%;}
</style>
<style>
/*for autocomplete */
.ui-autocomplete {
    z-index:9999!important;
}
.pq-sb-horiz-t .pq-sb-slider, .pq-sb-vert-t .pq-sb-slider, .pq-sb-horiz-t .pq-sb-btn, .pq-sb-vert-t .pq-sb-btn{background-color: rgb(220, 254, 211) !important;}
</style>
<?php 
$params = http_build_query([
    'from_date' => $from_date,
    'to_date' => $to_date   
]);
?>
<div class="row mb-md-0 mb-3">
    <div class="col-md-6"><h3 class="pb-3">Batches Ledger</h3></div>
    <div class="col-md-6 text-end"><div class="taskmenus">
        <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 
        <a href="<?= base_url() ?>admin/export/acc_ledger_det?<?= $params ?>" target="_blank">
            <span class="material-symbols-outlined">print</span>
        </a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li class="open-comingsoon"><a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a></li>
			<li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a></li>
			<li class="open-comingsoon"><a class="dropdown-item" href="javascript:void(0)" onclick="print_pdf();">Document</a></li>
          </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon">share</span></a> 
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Facebook</a></li>
            <li><a class="dropdown-item" href="#">Twitter</a></li>
            <li><a class="dropdown-item" href="#">Instagram</a></li>
        </ul>
       
    </div>
    </div>
</div>
<div class="row mb-2 align-items-top">
    <div class="col-lg-5">
        <form class="form needs-validation" method="get" id="salefrm" autocomplete="off" novalidate>
        <div class="input-group">
            <span class="input-group-text px-1">From</span>
            <input type="text" name="from_date" id="from_date" value="<?php echo $from_date;?>" class="datepicker form-control p-2" required  style="width:90px;">
            
            <span class="input-group-text px-1">To</span>
            <input type="text" name="to_date" id="to_date" value="<?php echo $to_date;?>" class="datepicker form-control p-2" required style="width:90px;">
            
            
            <input type="submit" id="gofilter" class="btn btn-sm btn-success" value="GO">
        </div>
	 
        <div class="form-check form-check-inline me-2">
            <input  class="form-check-input" type="checkbox" value="1" name="fg" id="fg">
            <label class="form-check-label" for="fg">Fixed Grid</label>
        </div> 
        </form>
    </div>
    <div class="col-lg-7 text-end">

        <div class="dropdown float-end">
            <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
            <ul class="dropdown-menu">
              <!--  <li><a id="trash" data-type="0" class="dropdown-item" href="javascript:void(0);">Enable Trash Mode</a></li>-->
               
            </ul>
            <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Voucher Series </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Action</a></li>
                <li><a class="dropdown-item" href="#">Another action</a></li>
                <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
             <a href="javascript:void(0);" onclick="history.back()" class="btn btn-sm btn-outline-success mt-0 m-1">Back</a>
        </div>
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
        <button type="button" class="btn btn-success">Save changes</button>
      </div>
    </div>
  </div>
</div>

<div id="validation_errors"></div>
<br>
<div id="grid_search" style="margin:auto;"> </div> 


 

<?php echo view('includes/footer_scripts'); ?>
<script>

$(document).on("change","#type",function(){
 $("#salefrm").submit();
});

     $(function () {
       function calculateSummary() { 
        var grid = this;

                const url = new URL(window.location.href);
                if(url.searchParams.has('rowIndx')){
                    var rowIndx = url.searchParams.get('rowIndx');
                    url.searchParams.delete('rowIndx');
                    window.history.replaceState(null, null, url);
                    grid.setSelection({ rowIndx: parseInt(rowIndx), focus: true });
                }
                else{
                    grid.setSelection({ rowIndx: grid.rowIndxOffset, focus: true });
                }   		
        // calculate summary from page wise data  
        var debitTotal = 0,
            creditTotal = 0,
            balanceTotal = 0,
           data = $("#grid_search").pqGrid( "pageData" ),
			len = data.length;
			
        data.forEach(function(row){             
            debitTotal +=  parseAmount(row.debit_total);
            creditTotal +=  parseAmount(row.credit_total);

        })

        var totalData = {
                txn_date: "Total",
                voucher_type :"",
                voucher_no:"",
				account_name:"",
				short_narration:"",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            
       
        this.option('summaryData', [totalData]);
		
    }  
         
    function commentRender(ui) {
        if (this.attr({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, attr: 'title' }).attr) {
            if (ui.column.align == 'right') {
                return { cls: 'pq-comment pq-comment-left' };
            }
            else {
                return { cls: 'pq-comment' };
            }
        }
    };
    
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
			     if(dataIndx=='amount')
					   dataIndx= 'amount_total'; 
			
                filterObject = [{ dataIndx: dataIndx, condition: condition, value: value}];
            }
            $grid.pqGrid("filter", {
                oper: 'replace',
                data: filterObject
            });
        }
       
        var colModel = [
            
            { title: "BATCH NO ", dataIndx: "batch_no", width: 100,sortable:false },
            { title: "ITEM NAME", width: 50, dataIndx: "item_name",sortable:false  },
            { title: "UNIT NAME", width: 90, dataIndx: "unit_name",sortable:false },             
            { title: "MFR DATE", width: 150, dataIndx: "mfr_date",sortable:false },
            { title: "EXPIRY DATE", width: 150, dataIndx: "exp_date",sortable:false },  
            { title: "BALANCE QTY", width: 150, align: "right", dataIndx: "balance",sortable:false },
            ];
       
         var loadStateSuccess;
		 var minWidth='flex'; 
		 var scrollModel={ autoFit: true };
		 
		 var dataModel = {
			location: "remote",
			dataType: "json",
			method: "POST",
			postData:{
				from_date:"<?php echo $from_date;?>",
				to_date:"<?php echo $to_date;?>"				
				},
			url: "<?php echo base_url();?>admin/reports/ajax_batch_ledger_detail", 
			beforeSend: function (jqXHR, settings) {
			jqXHR.withCredentials = true;
			},
           getData: function (dataJSON) {
            var data = dataJSON.data;
            return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
            }
    };
	    var newObj = {
            scrollModel: scrollModel,
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: "flex",
			minWidth: minWidth,
			selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
            dataReady: calculateSummary,
			columnTemplate: { render: commentRender },
			colModel: colModel,  
			wrap:false,
            numberCell: { show: true },
            filterModel: { on: true,mode: "OR", header: true, type:'remote' },			
            pageModel: { type: "remote", rPP: 100, strRpp: "{0}" },
            editable: false,
            showTitle: false,
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
                     { 
                         type: 'select', cls: "filterColumn",
                         listener: filterhandler,
                        options: function (ui) {
                             var CM = ui.colModel;
                            var opts = [];
                             for (var i = 0; i < CM.length; i++) {
                                 var column = CM[i];
                                 var obj = {};
                                 if(column.dataIndx!='' && column.dataIndx!='chkbx' && column.dataIndx!='checkbox' && column.dataIndx!='state'){   
                                obj[column.dataIndx] = column.title;
                                 opts.push(obj);
                                 }
                             }
                             return opts;
                         }
                     },
                     { 
                         type: 'select',                         
                         cls: "filterCondition",
                         listener: filterhandler,
                         options: [
							 { "contain": "Contains" },
                             { "begin": "Begins With" },
                            
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
        
        newObj.rowDblClick = function(event, ui) {
             
  	            var rowData    = ui.rowData;
				var batch_id = rowData.batch_id;
  	            var select_rowindx =  set_page();
                $("#grid_search").pqGrid('saveState');
				window.location.href="<?php echo base_url();?>admin/reports/batchwise/"+batch_id+"?from_date=<?php echo $from_date;?>&end_date=<?php echo $to_date;?>"; 
				
		       
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	        
	           var rowData     = ui.rowData;
		    var txn_bo_id = rowData.bo_id;
		        var ajax   = rowData.ajax;
		        var col_type = rowData.col_type;
		         var voucher_txn_id   = rowData.voucher_txn_id;
		         var voucher_type_id   = rowData.voucher_type_id;
		       if (evt.keyCode==13){

                hide_trash_checkbox();
                 $("#grid_search").pqGrid('saveState');
				//if(txn_bo_id=='<?php //echo $bo_id; ?>'){
               var select_rowindx =  set_page();
		        edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx); 
				//}	else{
					//alert_notification("You can edit from branch!!!");
					//return false;
				//}			
		         
		       }
		   
	       }
        
    var $grid = $("#grid_search").pqGrid(newObj);

    function set_page()
    {
       var select_row = $("#grid_search").pqGrid("selection", { type:'row', method:'getSelection'});
        if(select_row){
            const url = new URL(window.location.href);
            url.searchParams.set('rowIndx', select_row[0].rowIndx);
            window.history.replaceState(null, null, url);
			return select_row[0].rowIndx;
        }
        else{
            const url = new URL(window.location.href);
            if(url.searchParams.has('rowIndx')){
                url.searchParams.delete('rowIndx');
                window.history.replaceState(null, null, url); 
					
            }
			return 0;
        } 
    }
     
    });
    
  $(document).on('change', '#fg', function(){
    if($(this).is(":checked")) {
      $("#grid_search").pqGrid('option', 'height', 420);
    }
    else{
      $("#grid_search").pqGrid('option', 'height', 'flex');
    }
    $("#grid_search").pqGrid('refreshDataAndView');
  });

  
function getParameterByName(name, url = window.location.href) {
		name = name.replace(/[\[\]]/g, '\\$&');
		var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
			results = regex.exec(url);
		if (!results) return '';
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, ' '));
	}


		</script>	<style>.hidden{display:none;}</style></body></html>