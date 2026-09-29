<?php $header = array('title' => 'Stock Journal Register');?>
<?php echo view('includes/header',$header); ?>
 
 <div class="row mb-md-0 mb-3">
    <div class="col-md-6"><h3 class="pb-3">Stock Journal Register</h3></div>
   <div class="col-md-6 text-end">
       <div class="taskmenus">
    <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 

    <a href="#"><span class="material-symbols-outlined">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
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
</div>
 
</div>

</div>
 
 <div class="row mb-2 align-items-top">
    
<div class="col-md-4">
<div class="input-group">
  
    <span class="input-group-text px-1">From</span>
    <input type="text" name="fromdate" value="<?php echo $from_date;?>" class="datepicker form-control p-2" required  style="width:90px;">
    
    <span class="input-group-text px-1">To</span>
    <input type="text" name="todate" value="<?php echo $to_date;?>" class="datepicker form-control p-2" required style="width:90px;">
            
  <input type="button" class="btn btn-sm btn-success" value="GO" onClick="redirectpage();" fdprocessedid="6tq9et">
  </div>


</div>
<div class="col-md-8">
<div class="dropdown d-sm-flex d-block float-end">
  <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" fdprocessedid="cq5rpm"> View </button>
  <ul class="dropdown-menu" style="">
    <li><a class="dropdown-item" href="#">Action</a></li>
    <li><a class="dropdown-item" href="#">Another action</a></li>
    <li><a class="dropdown-item" href="#">Something else here</a></li>
  </ul>
  <p class="m-0"><a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-sm btn-outline-success">Back</a></p>
</div><div class="dropdown float-end" style="width:180px;">
          <div class="input-group">
              <span class="input-group-text">Branch</span>
             <?php              
					echo form_dropdown('bo_id',$bo_dropdown,$ses_boid,' id="bo_id" form="salefrm" onchange="this.form.submit()" class="form-select"');
					?>
          </div>  
        </div></div>

</div>
 
<br>
   <div id="grid_search" style="margin:auto;"> </div>  
   <?php
   $array=array();
   $json = json_encode($array);
   ?>
<?php echo view('includes/footer_scripts'); ?>
<script>
 var item_id    = '';
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
            { title: "Date", align:"left", width: 180,   dataIndx: "voucher_date" },
             { title: "MC", align:"left", width: 180,   dataIndx: "material_centre" },
              { title: "Voucher No.", align:"left", width: 180,   dataIndx: "bill_no" },
              
            { title: "Jornal Transfered From", width: 140, align: "center", colModel: [
                  { title: "Item", width: 180,align: "left",dataIndx: "from_item_name" }, 
                  { title: "Qty", width: 180,align: "left",dataIndx: "from_item_qty" }, 
                  { title: "Price", width: 180,align: "right",dataIndx: "from_item_price",format: '##,###.00' }, 
                  { title: "Amount",width: 180, align: "right",dataIndx: "from_item_amount",format: '##,###.00'}
                ] },
            { title: "Jornal Transfered To", width: 140, align: "center", colModel: [
                { title: "Item", width: 180,align: "left",dataIndx: "to_item_name" }, 
                  { title: "Qty", width: 180,align: "left",dataIndx: "to_item_qty" }, 
                  { title: "Price", width: 180,align: "right",dataIndx: "to_item_price",format: '##,###.00' }, 
                  { title: "Amount", width: 180,align: "right",dataIndx: "to_item_amount",format: '##,###.00'}
                ] },
           { title: "BOM", align:"left", width: 180,   dataIndx: "bom" },
             { title: "Narrations", align:"left", width: 180,   dataIndx: "narration" }, 
	    	];
	    	
        var dataModel = {
            location: "remote",
            dataType: "json",
            postData: {item_id:item_id},
            method: "POST",
             postData:{from_date:"<?php echo $from_date;?>",to_date:"<?php echo $to_date;?>"},
            url: "<?php echo base_url();?>/admin/registerlog/ajax_stock_journal_register",
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
            dataModel: dataModel,
            colModel : colModel,
            numberCell: { show: false },
            filterModel: { mode: 'OR' },
            editable: false,
             pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            showTitle: true,
            load:function(event,ui) {
               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
            },
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
        
          newObj.rowDblClick = function(event, ui) {
  	           var rowData       = ui.rowData;
  	           var voucher_type_id  = rowData.voucher_type_id;
		       var voucher_txn_id    = rowData.voucher_txn_id;
		      
		    window.location.href= baseurl+'/admin/packing_unpacking/modify_stock_journal/'+voucher_txn_id+'/'+voucher_type_id;
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	           var rowData     = ui.rowData;
		      var voucher_type_id  = rowData.voucher_type_id;
		       var voucher_txn_id    = rowData.voucher_txn_id;
		        
		       if (evt.keyCode==13){
	              window.location.href= baseurl+'/admin/packing_unpacking/modify_stock_journal/'+voucher_txn_id+'/'+voucher_type_id;
		       }
		   
	       }
	     
        var $grid = $("#grid_search").pqGrid(newObj);
        $("#grid_search").pqGrid('loadState'); 
         $(window).unload( function(){
         $("#grid_search").pqGrid('saveState');
       });
        

 function redirectpage(){
     var fromdate = $("#fromdate").val();
     var todate = $("#todate").val();
     window.location.href='<?php echo $base_url?>registerlog/others?register_type=stock_journal&fromdate='+fromdate+'&todate='+todate;
   }
</script>
 </body>
</html>