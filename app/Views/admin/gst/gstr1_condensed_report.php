<?php $header = array( 	'title' => 'GSTR1 Report' ); ?>
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
<div class="row mb-md-0 mb-3">
    <div class="col-md-6"><h3 class="pb-3">GSTR1 Report</h3></div>
    <div class="col-md-6 text-end"><div class="taskmenus">
        <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a></li>
			<li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a></li>
			<li><a class="dropdown-item" href="javascript:void(0)" onclick="print_pdf();">Document</a></li>
          </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">share</span></a> 
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
        <div class="form-check form-check-inline me-2">
            <input  class="form-check-input" type="checkbox" value="1" name="fg" id="fg">
            <label class="form-check-label" for="fg">Fixed Grid</label>
        </div> 
		<input type="hidden" name="fromdate" value="<?php echo date('Y-m-d',strtotime($from_date));?>">
		<input type="hidden" name="todate" value="<?php echo date('Y-m-d',strtotime($to_date));?>">
       <input type="hidden" name="fltrtype" value="<?php echo $fltrtype;?>">
		
	   </form>
    </div>
    <div class="col-lg-7 text-end">

        <div class="dropdown d-inline-block me-1" style="width:220px;">
		
          <div class="input-group input-group-sm input-group">
              <span class="input-group-text">View</span>
              <select form="salefrm" class="form-select" name="view" onchange="this.form.submit()">
                   <option value=""></option>
				  <option <?= ($view==0) ? 'selected' : '' ?> value="0">Condensed</option>
                  <option <?= ($view==1) ? 'selected' : '' ?> value="1">Detailed</option>				 
              </select>
              
          </div>  
        </div>
		 
        <div class="dropdown float-end">
            
            <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Voucher Series </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Action</a></li>
                <li><a class="dropdown-item" href="#">Another action</a></li>
                <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
             <a href="<?php echo history_back();?>"  class="btn btn-sm btn-outline-success mt-0 m-1">Back</a>
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


<div class="row">
    <div class="col-md-6">
	<?php
	$preview_valid=0;
	$fltrtype_info = unobfuscate_link($fltrtype);
	 if($fltrtype_info[1]=='mnth'){
		 $preview_valid=1;
		 $filter_labels = date('M, Y',strtotime($from_date));
	 }
	else if($fltrtype_info[1]=='qtr'){
		$preview_valid=1;
		 $filter_labels = date('M, Y',strtotime($from_date)) .' To '.date('M, Y',strtotime($to_date));
	 }
	else if($fltrtype_info[1]=='yrs'){
		$preview_valid=0;
		 $filter_labels = date('M, Y',strtotime($from_date)) .' To '.date('M, Y',strtotime($to_date));
	 }
  else if($from_date==$to_date){
	  $filter_labels = date('M, Y',strtotime($from_date));
	  $preview_valid=0;
  }
   else{
	   $filter_labels = date('M, Y',strtotime($from_date)) .' To '.date('M, Y',strtotime($to_date)); 
	   $preview_valid=0;
   }
    $filter_labels = date('d M, Y',strtotime($from_date)) .' To '.date('d M, Y',strtotime($to_date)); 
	?>
       <p><em>For: <strong><?php echo $filter_labels;?></strong></em></p>
    </div>
     
</div>

<div id="validation_errors"></div>
<br>
<div id="grid_search" style="margin:auto;"> </div> 
<div class="col-12 text-center">
         <br><br> 
        <a href="javascript:void(0);"  class="btn btn-success btn-lg" onClick="return isvalid_dates();">PREVIEW GSTR-1</a>
      
    </div>

<?php echo view('includes/footer_scripts'); ?>
<script>
function periodIsMonthOrQuarterISO(fromStr, toStr) {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(fromStr) ||
      !/^\d{4}-\d{2}-\d{2}$/.test(toStr))  return false;

  const [y1, m1, d1] = fromStr.split('-').map(Number); // m1 1‒12
  const [y2, m2, d2] = toStr.split('-').map(Number);

  if (Date.parse(fromStr) > Date.parse(toStr)) return false; // reversed

  /* ----------  one full month  ---------- */
  if (d1 === 1 && y1 === y2 && m1 === m2) {
    const lastDay = new Date(y1, m1, 0).getDate();          // last day of month
    if (d2 === lastDay) return true;
  }

  /* ----------  one full quarter  ---------- */
  const quarters = {
    1: {endM: 3,  endD: 31},   // Jan-Mar
    4: {endM: 6,  endD: 30},   // Apr-Jun
    7: {endM: 9,  endD: 30},   // Jul-Sep
   10: {endM: 12, endD: 31}    // Oct-Dec
  };

  if (d1 === 1 && quarters[m1]) {
    const {endM, endD} = quarters[m1];
    if (y1 === y2 && m2 === endM && d2 === endD) return true;
  }

  return false;
}

function isvalid_dates(){
    const from  = '<?php echo date('Y-m-d',strtotime($from_date));?>'; // yyyy-mm-dd
  const to    ='<?php echo date('Y-m-d',strtotime($to_date));?>';
  
 if (periodIsMonthOrQuarterISO(from, to)) {
      var linkurl ='<?php echo base_url();?>admin/etaxes/preview_gstr1_report?fromdate=<?php echo $from_date;?>&todate=<?php echo $to_date;?>&fltrtype=<?php echo $fltrtype;?>';
      window.location.href=linkurl;
  } else {
    	alert_notification("GSTR-1 Preview not allowed for this period!!!");
		return false;
  } 
  

}
var pageview ='<?php echo $view;?>';
$(function () {
       function calculateSummary() { 
          		
        // calculate summary from page wise data  
        var invoiceTotal = 0,
            taxableTotal = 0,
            igstTotal = 0,
            cgstTotal = 0,
            sgstTotal = 0,
            cessTotal = 0,
            taxTotal = 0,
           data = $("#grid_search").pqGrid( "pageData" ),
			len = data.length;
			
        data.forEach(function(row){             
            invoiceTotal +=  parseAmount(row.sm_invoice_value);
            taxableTotal +=  parseAmount(row.sm_taxable_value);
            igstTotal +=  parseAmount(row.sm_igst);
            cgstTotal +=  parseAmount(row.sm_cgst);
            sgstTotal +=  parseAmount(row.sm_sgst);
            cessTotal +=  parseAmount(row.sm_cess);
             taxTotal +=  parseAmount(row.sm_total_tax);

        })

        var totalData = {
                tableno: "Total",
                table_name :"",
                total_records:"",
				invoice_value:formatAmount(invoiceTotal),
				taxable_value:formatAmount(taxableTotal),
				igst :formatAmount(igstTotal),
                cgst:formatAmount(cgstTotal),
				sgst:formatAmount(sgstTotal),
				cess:formatAmount(cessTotal),
                total_tax: formatAmount(taxTotal),
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            
       
        this.option('summaryData', [totalData]);
		
    }  
         
    
    
        function filterhandler(evt, ui) {

            var $toolbar = $grid.find('.pq-toolbar-search'),
                $value = $toolbar.find(".filterValue"),
                value = $value.val(),
                condition = '',//$toolbar.find(".filterCondition").val(),
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
        //filterRender to highlight matching cell text.
        function filterRender(ui) {
            var val = ui.cellData,
                filter = ui.column.filter;
            if (filter && filter.on && filter.value) {
                var condition = filter.condition,
                    valUpper = val.toUpperCase(),
                    txt = filter.value,
                    txt = (txt == null) ? "" : txt.toString(),
                    txtUpper = txt.toUpperCase(),
                    indx = -1;
                if (condition == "end") {
                    indx = valUpper.lastIndexOf(txtUpper);
                    //if not at the end
                    if (indx + txtUpper.length != valUpper.length) {
                        indx = -1;
                    }
                }
                else if (condition == "contain") {
                    indx = valUpper.indexOf(txtUpper);
                }
                else if (condition == "begin") {
                    indx = valUpper.indexOf(txtUpper);
                    //if not at the beginning.
                    if (indx > 0) {
                        indx = -1;
                    }
                }
                if (indx >= 0) {
                    var txt1 = val.substring(0, indx);
                    var txt2 = val.substring(indx, indx + txt.length);
                    var txt3 = val.substring(indx + txt.length);
                    return txt1 + "<span style='background:yellow;color:#333;'>" + txt2 + "</span>" + txt3;
                }
                else {
                    return val;
                }
            }
            else {
                return val;
            }
        }
        var colModel = [
            { title: "TABLE NO", dataIndx: "tableno", width: 100,sortable:false},
            { title: "TABLE NAME", width: 250, dataIndx: "table_name",sortable:false },
            { title: "NO. OF RECORDS", width: 100, dataIndx: "total_records",sortable:false},             
            { title: "INVOICE VALUE", width: 100, dataIndx: "invoice_value",sortable:false},
            { title: "TAXABLE VALUE", width: 100, dataIndx: "taxable_value",sortable:false},  
            { title: "IGST", width: 100, align: "right", dataIndx: "igst",sortable:false},
            { title: "CGST", width: 100, align: "right", dataIndx: "cgst",sortable:false},
			{ title: "SGST", width: 100, align: "right", dataIndx: "sgst",sortable:false},
			{ title: "CESS", width: 100, align: "right", dataIndx: "cess",sortable:false},
            { title: "TOTAL TAX", width: 100, dataType: "string", dataIndx: "total_tax",sortable:false},		   
	 	    ];
      
         var loadStateSuccess;
		 var minWidth='flex'; 
		 var scrollModel={ autoFit: true };
		
        var newObj = {
            scrollModel: scrollModel,
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: "flex",
			minWidth: minWidth,
			selectionModel: { type: 'row',mode:'single' },
            dataModel: <?php echo $response;?>,
            dataReady: calculateSummary,
			colModel: colModel,  
			wrap:false,
            numberCell: { show: false },
            filterModel: { mode: 'OR', type: "local" },
            pageModel: { type: "local", rPP: 100, strRpp: "{0}" },
            editable: false,
            showTitle: false,
            create: function (evt, ui) {// make first row auto selected
			var grid = this,
                $select_row = $(".select-row"),
                data = ui.dataModel.data;
				var rowIndx =0;
             loadStateSuccess = this.loadState({ refresh: false });
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
        
        newObj.rowDblClick = function(event, ui) {
             $("#grid_search").pqGrid('saveState');
  	            var rowData    = ui.rowData;
				var txn_bo_id = rowData.bo_id;
  	            var col_type = rowData.col_type;
  	            var ajax   = rowData.ajax;
		        var htableno   = rowData.htableno;
		        var from_date   = rowData.from_date;
		        var to_date   = rowData.to_date;
				
				var select_rowindx =  set_page();
                window.location.href= baseurl+"admin/gstr1/ledger?fromdate="+from_date+"&todate="+to_date+"&tableno="+htableno+"&rowIndx="+select_rowindx;
                      
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	        
	           var rowData     = ui.rowData;
		    var txn_bo_id = rowData.bo_id;
		        var ajax   = rowData.ajax;
		        var col_type = rowData.col_type;
		        var voucher_txn_id   = rowData.voucher_txn_id;
		        var voucher_type_id   = rowData.voucher_type_id;
		        if (evt.keyCode==13){               
                $("#grid_search").pqGrid('saveState');
				var txn_bo_id  = rowData.bo_id;
  	            var col_type   = rowData.col_type;
  	            var ajax       = rowData.ajax;
		        var htableno   = rowData.htableno;
		        var from_date  = rowData.from_date;
		        var to_date    = rowData.to_date;
				var select_rowindx =  set_page();
                window.location.href= baseurl+"admin/gstr1/ledger?fromdate="+from_date+"&todate="+to_date+"&tableno="+htableno+"&rowIndx="+select_rowindx;
                }
		   
	       }
        
    var $grid = $("#grid_search").pqGrid(newObj);

    $("#grid_search").pqGrid('loadState');

    $(window).unload( function(){
      $("#grid_search").pqGrid('saveState');
    });
    
     
    });
	
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
            }if(url.searchParams.has('duplc')){
                url.searchParams.delete('duplc');
                window.history.replaceState(null, null, url);  
            }
			return 0;
        }  		
    }
    
  $(document).on('change', '#fg', function(){
    if($(this).is(":checked")) {
      $("#grid_search").pqGrid('option', 'height', 420);
    }
    else{
      $("#grid_search").pqGrid('option', 'height', 'flex');
    }
    $("#grid_search").pqGrid('refreshDataAndView');
  });

  
  function print_excel(){
			var from_date = '<?php echo $from_date;?>'; 
			var to_date   = '<?php echo $to_date;?>';
            var view      = '<?php echo $view;?>'; 
			
            if(view == 0){
                var stringparameters = "view="+view+"&from_date="+from_date+"&to_date="+to_date;
                window.location.href= baseurl+"admin/export/gstr_report_detail?"+stringparameters;
            }
			if(view == 1){
                var stringparameters = "view="+view+"&from_date="+from_date+"&to_date="+to_date;
                window.location.href= baseurl+"admin/export/gstr_report_detail?"+stringparameters;
            }
        }

</script>	<style>.hidden{display:none;}</style></body></html>