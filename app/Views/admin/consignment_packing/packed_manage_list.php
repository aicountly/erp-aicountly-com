<?php $header = array( 	'title' => 'Manage Packing List' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .gridtable .row{  display: grid; grid-template-columns:10% 30% 30%  15% 15%;}
    .gridtable .foot.row{ grid-template-columns:100% ;}

    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }
    
    .pq-grid-cell.pq-side-icon > div:before {        
        width: 20px;
        height: 20px;
        color: #ccc;
        float: right;
    }
    
    .pq-grid-cell.pq-drop-icon > div:before {
        content: "▼";
    }
    
    .pq-grid-cell.pq-calendar > div:before {
        content: "\01F4C5";
    }
    .pq-select-search-input {
        padding: 1px 2px;
        border-width: 0;
        height:23px;
    }
    .pq-select-search-input {
        box-sizing: border-box;
        width: 100%;
        font-size: inherit;
    }
    
    .ui-datepicker-calendar tr, .ui-datepicker-calendar td, .ui-datepicker-calendar td a, .ui-datepicker-calendar th{
        font-size:inherit;
    }
    div.ui-datepicker{
        font-size:13px;
        width:inherit;
        height:inherit;
    }
    .ui-datepicker-title span{
        font-size:13px;
    }

/*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
}
</style>

<div class="row">
		     <div class="col-md-6 order-1">  <h3 class="pb-3">Manage Packing List(<?php echo $packing_info['list_name'];?>)</h3></div>
		    <div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
     <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>   
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
  <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>  
    </div>
    </div> 

 <div class="col-md-6 order-2 order-md-3">
   <div class="input-group d-inline-flex me-3" style="width:220px;">
  <span class="input-group-text" id="basic-addon1">Packing Level</span>
  <select class="form-select" name="pack_level" id="pack_level">
     <option value="1" <?php echo ($level_id==1)?"selected":"";?>>Level 1</option> 
       <option value="2" <?php echo ($level_id==2)?"selected":"";?>>Level 2</option> 
        <option value="3" <?php echo ($level_id==3)?"selected":"";?>>Level 3</option> 
         <option value="4" <?php echo ($level_id==4)?"selected":"";?>>Level 4</option> 
          <option value="5" <?php echo ($level_id==5)?"selected":"";?>>Level 5</option>  
      </select>
</div>
<div class="d-inline-flex">
    <span class="form-check form-check-inline"><input type="radio" class="form-check-input" name="packing_type" id="packing_type_unpacked" value="u" > Unpacked</span>
     <span class="form-check form-check-inline"><input type="radio" class="form-check-input" name="packing_type" id="packing_type_packed" value="p" checked="">  Packed</span>
</div>

      </div>
			
  <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
				<?php if(count($cupackingn) > 0) { ?>
				<a href="<?php echo base_url();?>/admin/consignment_packing/finalize_packing_status/<?php echo $packing_info['list_id'];?>" class="btn btn-outline-success btn-sm"> Finalize Packing</a> 
				<?php } ?>
          <a href="<?php echo history_back();?>" class="btn btn-outline-success showinline-md">Back</a>
</div> </div>


				
           
    
     <?php if ($session->getFlashdata('message')) { ?>
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php echo $session->getFlashdata('message'); ?>
            </div>
    <?php } ?>
    <?php if ($session->getFlashdata('error_array_message')) { ?>
            <div class="alert alert-danger alert-dismissible fade show mt-2" style="max-height: 150px;overflow-y: auto;">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php $errors = $session->getFlashdata('error_array_message'); ?>
                <ul>
                <?php foreach($errors as $error) { ?>
                    <li><?php echo $error ?></li>
                <?php } ?>
                </ul>
            </div>
    <?php } ?>
    <?php
/* echo'<pre>';
print_r($level_data);
die(); */

	foreach($level_data as $item) { ?>
	<div class="row">
	<div class="col-md-12">
	
	<div id="grid_md<?php echo $item['cu_id'];?>" style="margin:5px auto;"></div>
	<br>
	</div>
	</div>
	<?php } ?>
	<div id="validation_errors"></div>
<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
  
   <input type="hidden" name="itmsdataUnpacked" id="itmsdataUnpacked">
 
	</form>
   <?php 
   
  
      for($i=1;$i<=50;$i++){ 
		$json_data[] =array("item_unit_id"=>"","batch_no"=>"","specification_no"=>"","item_id"=>"","id"=>'',"pq_cellattr"=>array("item_name"=>array("title"=>"")),"item_name"=>'','item_qty'.$i=>'','item_unit'=>'',"to_carrying_unit_id"=>"","to_carrying_unit"=>"",'to_qty_packed'=>'','to_cu_save'=>'');
       }    
      ?>
  
<?php echo view('includes/footer_scripts'); ?>

<script>
 
	
	
	




$("#pack_level").on("change",function(){
    var pack_level = $(this).val();
    var packing_type =  $('input[name="packing_type"]:checked').val();
    if (packing_type == 'u') {
      window.location.href="<?php echo base_url();?>/admin/consignment_packing/manage_list/<?php echo $packing_list_id;?>/unpacked/"+pack_level;
    }
    else if (packing_type == 'p') {
         window.location.href="<?php echo base_url();?>/admin/consignment_packing/manage_list/<?php echo $packing_list_id;?>/packed/"+pack_level;
    }
    
    
});


$('input[type=radio][name=packing_type]').change(function() {
     var pack_level = $("#pack_level").val();
    if (this.value == 'u') {
    
      window.location.href="<?php echo base_url();?>/admin/consignment_packing/manage_list/<?php echo $packing_list_id;?>/unpacked/"+pack_level;
    }
    else if (this.value == 'p') {
        
         window.location.href="<?php echo base_url();?>/admin/consignment_packing/manage_list/<?php echo $packing_list_id;?>/packed/"+pack_level;
    }
});


function finalize_cu_status(packing_list_id,cuid){    
    var msg = confirm("Are you sure to finalize CU??");
      if(msg){        
         var rowData= {"cuid":cuid,"packing_list_id":'<?php echo $packing_list_id;?>',"pack_level":$("#pack_level").val()};         
		 $.post('<?php echo base_url();?>/admin/consignment_packing/finalize_cu_status', rowData, function(response) { 
				location.reload();
           });
	   
    }
    else
    return false;
}

function save_row(rowindex){
	$('.pq-grid').pqGrid( "showLoading" );
 
 var rowData = $('.pq-grid').pqGrid( "getRowData", {rowIndx: rowindex} );
 rowData.pack_level=$("#pack_level").val();
 rowData.packing_type=$("input[name='packing_type']:checked").val();
 rowData.packing_list_id='<?php echo $packing_list_id;?>';
 console.log(rowindex);
 $.post('<?php echo base_url();?>/admin/consignment_packing/save_packing', rowData, function(response) { 
 if(response=='0'){
	 $.each(rowData, function(index,obj){
            rowData[index] = '';
        });
		
 }
	// $('.pq-grid').pqGrid( "deleteRow", { rowIndx: rowindex } );
 
  //console.log(response);
  //console.log(rowindex);
    $('.pq-grid').pqGrid( "updateRow",{ rowIndx: rowindex, row: {'item_qty': response,'to_carrying_unit': '','to_qty_packed': '','to_cu_label': ''  }});  
   
   
   var rowDatasss = $('.pq-grid').pqGrid( "getRowData", {rowIndx: rowindex} );
    console.log(rowDatasss);
	rowDatasss.to_carrying_unit_id='';
	
	$( ".pq-grid" ).pqGrid( "refreshDataAndView"); 

    $('.pq-grid').pqGrid( "hideLoading" );		
	
  });

}

function remove_row(rowindex){
	$('.pq-grid').pqGrid( "showLoading" );

 
  
 var rowData = $('.pq-grid').pqGrid( "getRowData", {rowIndx: rowindex} );
 rowData.pack_level=$("#pack_level").val();
 rowData.packing_type=$("input[name='packing_type']:checked").val();
 rowData.packing_list_id='<?php echo $packing_list_id;?>';
 $.post('<?php echo base_url();?>/admin/consignment_packing/remove_packing', rowData, function(response) {   
   
    $('.pq-grid').pqGrid( "updateRow",{ rowIndx: rowindex, row: { 'item_qty': response }});  
    $('.pq-grid').pqGrid( "hideLoading" );	
	$( ".pq-grid" ).pqGrid( "refreshDataAndView");

  });

}


var item_qty_balance = {};
function get_item_balances(item_id,item_unit_id,rd){
	 console.log(item_id +","+item_unit_id);
	 var voucher_date = '<?php echo date('d-m-Y');?>';	
	 if(item_qty_balance[item_id] == undefined){
         item_qty_balance[item_id] = {};					
	 }
	 
	 if(item_qty_balance[item_id][item_unit_id] == undefined){	     
	   var balance_response = function (){
	    $.ajax({
						  'async': false,
						 'type': "GET",
						'global': false,
					   'dataType': 'html',
					  'url': "<?php echo $base_url; ?>ajax/itemqtybalance/"+item_id+"/"+voucher_date+"/"+item_unit_id,
					  'success': function (data) {
						accbalance = data;
					   }
					   });
					 return accbalance;
					 }();					
                   item_qty_balance[item_id][item_unit_id] =balance_response;
     }
     else{
       var balance_response = item_qty_balance[item_id][item_unit_id];
     }
	if(balance_response){		
	var parseitembalance = $.parseJSON(balance_response);
	if(typeof parseitembalance !=="undefined"){
		AvailQty = parseitembalance[rd.item_id].AvailQty;
		var PackQty = parseitembalance[rd.item_id].PackQty;
		var ObseQty = parseitembalance[rd.item_id].ObseQty;
		var IntrsQty = parseitembalance[rd.item_id].IntrsQty;
		var UnitName = parseitembalance[rd.item_id].unit_name;
		rd.AvailQty  = AvailQty;
		var balancetable = "<table style='width:100%;'><tr><td colspan='2'>"+UnitName+"</td></tr><tr><td>Available</td><td align='right'>"+AvailQty+"</td></tr><tr><td>Packed</td><td align='right'>"+PackQty+"</td></tr><tr><td>Obselete</td><td align='right'>"+ObseQty+"</td></tr><tr><td>In Transit</td><td align='right'>"+IntrsQty+"</td></tr></table>";	
		rd.pq_cellattr ={
			"item_name" : { "title":balancetable }
		};
		$("#grid_search").pqGrid('refreshDataAndView');		
		return balancetable;
	}
	else
	  return "";
	}  
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
  var item_qty_balance_from = {};
  var item_qty_balance_b_from = {};
  var item_qty_balance_to = {};
  var item_qty_balance_b_to = {};
  
 
  function update_grid_item_balances(gridname) {
        var data = gridname.pqGrid('option', 'dataModel.data');

        $.each(data, function(index,obj){
            if(obj.item_id != '' && obj.item_id != undefined && obj.item_unit_id != '' && obj.item_unit_id != undefined)
            {
                data[index]['item_balance'] = item_qty_balance_from[obj.item_id][[obj.item_unit_id]];
            }
        });


        gridname.pqGrid('option', 'dataModel.data', data);
        gridname.pqGrid('refreshDataAndView');
   }
   
var unitslist = <?php echo json_encode($units_list);?>; 
var intRegex = /^\d+$/;
var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;

    var itemlist  = [];
  
	
   $("#submitbtn").on("click",function(){
     var item_checked = [];
      var item_checked_to = [];
     var final_item_id =[];
     
     var grid1 = $("#grid_search").pqGrid('option', 'dataModel.data');
     var grid2 = $("#grid_search2").pqGrid('option', 'dataModel.data');
     
     
   
     
     var response_grid1 = grid_from_data(grid1,item_checked);
     
     var response_grid1_info = $.parseJSON(response_grid1);
     var final_item_amouint_grid1 = response_grid1_info['to_amount'];
     if(final_item_amouint_grid1=="0")
     var grid1_items  =[];
     else
     var grid1_items  = response_grid1_info['item_checked'];
  
      var selerror=0;
        var stock_qtyalert=0;
       var item_total_qty={};
        for (var i = 0; i < grid1_items.length; i++) {
           var item_id     = grid1_items[i]['item_id'];
           var item_qty    = grid1_items[i]['item_qty'];
           var item_unit   = grid1_items[i]['item_unit'];
           var item_unit_id   = grid1_items[i]['item_unit_id'];
		   var avail_item_qty = grid1_items[i]['AvailQty'];
		   
		   
		    if(item_total_qty[item_id] == undefined){
                    item_total_qty[item_id] = {};
                    item_total_qty[item_id][item_unit_id] = item_qty;
                }
                else{
                    if(item_total_qty[item_id][item_unit_id] == undefined) 
                        item_total_qty[item_id][item_unit_id] = item_qty;
                    else
                        item_total_qty[item_id][item_unit_id] += item_qty; 
                }
            
				if(item_total_qty[item_id][item_unit_id] > avail_item_qty)
			       stock_qtyalert++; // should be less than or equal to available stock
		   
		   
        }
       
   
      
      if(stock_qtyalert >0 ){
		  alert("Qty should be less than or equal to stock available!!!");
          return false;  
	  }
       else if(grid1_items.length==0){
          alert_notification("Kindly fill the items data!!!");
          return false;   
       }
       else{
         $("#itmsdataUnpacked").val(JSON.stringify(grid1_items));        
      show_loader();
      $("#salefrm").submit();     
       }
});
   $(document).on('submit', '#salefrm', function(e){
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(this);

        $.ajax({
            url: form.attr('action'), 
            type: 'POST',
            data: formData,
            dataType: "json",
            processData: false,
            cache: false,
            contentType: false,
            beforeSend: function() {
                show_loader();
                $('#submitbtn').attr('disabled', 'disabled');
                $('#validation_errors').html('');
            },
            success: function (response) {
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    alert_success(response.message);
                    window.location.reload();
                }
                else{
                    stop_loader();
                    alert_notification(response.message);
                    if(response.errors)
                    {
                        var list = ``;
                        $.each(response.errors, function(index, value){
                            list += `<li>${value}</li>`;
                        });

                        var html = `
                            <div class="alert alert-danger alert-dismissible">
                              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              <ul>${list}</ul>
                            </div>
                        `;
                        $('#validation_errors').html(html);
                        window.scrollTo(0,0);
                    }  
                }
                
            },
            complete: function() {
                stop_loader();
                $('#submitbtn').attr('disabled', false);
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
            },
        });

    });
function grid_from_data(data,item_checked){
     
     var final_item_amouint ="0";
       for (var i = 0; i < data.length; i++) {
           var item_unit_id  = data[i]['item_unit_id'];
           var item_id       = data[i]['item_id'];
           var item_name     = data[i]['item_name'];
           var batch_no    = data[i]['batch_no'];
           var item_qty      = data[i]['item_qty'];
           var item_unit     = data[i]['item_unit'];
           var specification_no   = data[i]['specification_no'];
           var AvailQty         =data[i]["AvailQty"];			
		   var carrying_unit_id  = data[i]['to_carrying_unit_id'];
           var qty_packed    = data[i]['to_qty_packed'];
           var to_uom      = data[i]['to_item_unit'];
		   var cu_label     = data[i]['to_cu_label'];
		   
           
          
            if(item_name!=''){
           
		  item_checked.push({
		            "item_unit_id": item_unit_id,
		            "item_id": item_id,
                    "batch_no": batch_no,
                    "item_qty" :item_qty,
                    "item_unit" :item_unit,
                    "specification_no" :specification_no,
                    "AvailQty" : AvailQty,
					"carrying_unit_id": carrying_unit_id,
        		    "qty_packed" :qty_packed,
                    "cu_label" :cu_label
                 });
             
            }
         }
    var final_response={item_checked:item_checked};     
    return JSON.stringify(final_response);     
    
}

   $(function () {
          
         function disableTextRenderer(ui) {
                grid = this,
                rowData = ui.rowData,
                rowIndx = ui.rowIndx,
                dataIndx = ui.dataIndx;
            if (grid.isEditableCell({ rowIndx: rowIndx, dataIndx: dataIndx }) == false) {
                grid.addClass({ rowIndx: rowIndx, dataIndx: dataIndx, cls: 'disabled' });
            }
            else {
                grid.removeClass({ rowIndx: rowIndx, dataIndx: dataIndx, cls: 'disabled' });
            }
        };
         
          var autoCompleteEditor = function (ui) {
        var $inp = ui.$cell.find("input");
        var element= {};
         var rd = ui.rowData;
		$inp.autocomplete({
               source:  <?php echo $packed_items;?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
					
					console.log(ui);
                    rd.item_id      = ui.item.item_id;
                    rd.item_name    = ui.item.label;
                    rd.item_unit    = ui.item.item_unit;
                    rd.item_unit_id = ui.item.item_unit_id;
                    $(this).val(ui.item.label);	
			     }
				
		     }).focus(function () {               
                $(this).autocomplete("search", "");
                rd.item_id = '';
                rd.item_name = '';
            }).focusout(function () { 
                if(rd.item_id!='' && rd.item_unit_id!=''){					
					get_item_balances(rd.item_id,rd.item_unit_id,rd);
				} 
                        
                if(rd.item_id == '')
                {
                    rd.item_id = '';
                    rd.item_name = '';
                }
            });
           
        }
          var autoCompleteEditor2 = function (ui) {
            var rd = ui.rowData;
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            $inp.autocomplete({
                source: unitslist,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
					console.log(ui); 
                    event.preventDefault();
                    $(this).val(ui.item.label);
                    rd.item_unit = ui.item.label;
                    rd.item_unit_id =ui.item.id;
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.to_item_unit = '';
                rd.to_item_unit_id = '';
            }).focusout(function () {     
                if(rd.item_id!='' && rd.item_unit_id!=''){	
					//console.log("unit selected---");			   
					get_item_balances(rd.item_id,rd.item_unit_id,rd);
				} 
                    
                if(rd.item_unit_id == '')
                {
                    var index = unitslist.findIndex(function(obj) {
    
                        var string = obj.label.toLowerCase();
                        var text = rd.item_unit.toLowerCase();
                         
                        return text != '' ? string.includes(text) : false;
                    });
                    if(index > -1){
                        rd.item_unit = unitslist[index].label;
                        rd.item_unit_id = unitslist[index].id;
                        
                    }
                    else{
                        rd.item_unit = '';
                        rd.item_unit_id = '';
                    }
                }
            });
          }
          var autoCompleteEditor_to = function (ui) {
        var $inp = ui.$cell.find("input");
        var element= {};
        var rdto = ui.rowData;
		$inp.autocomplete({
                source:  <?php echo $items_cu_dropdown;?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                   console.log(ui.item);
					event.preventDefault();
				    rdto.to_item_id = ui.item.item_id;
                    rdto.to_item_name = ui.item.label;
                    rdto.to_item_unit = ui.item.item_unit;
                    rdto.to_item_unit_id = ui.item.item_unit_id;
                    $(this).val(ui.item.label);	
			     }
				
            }).focus(function () {               
                $(this).autocomplete("search", "");
                rdto.to_item_id = '';
                rdto.to_item_name = '';
            }).focusout(function () { 
                 if(rdto.to_item_id!='' && rdto.to_item_unit_id!=''){	
					//console.log("unit selected---");			   
					get_item_balances(rdto.to_item_id,rdto.to_item_unit_id,rdto);
				}        
                if(rdto.to_item_id == '')
                {
                    rdto.to_item_id = '';
                    rdto.item_name = '';
                }
            });
           
        }
          var autoCompleteEditor3 = function (ui) {
            var rd = ui.rowData;
            var $inp = ui.$cell.find("input");
            var rdto = ui.rowData;
            $inp.autocomplete({
                source: unitslist,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                    rdto.to_item_unit = ui.item.label;
                    rdto.to_item_unit_id =ui.item.value;
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rdto.to_item_unit = '';
                rdto.to_item_unit_id = '';
            }).focusout(function () {     
                if(rdto.to_item_id!='' && rdto.to_item_unit_id!=''){	
					//console.log("unit selected---");			   
					get_item_balances(rdto.to_item_id,rdto.to_item_unit_id,rdto);
				}   
                    
                if(rdto.to_item_unit_id == '')
                {
                    var index = unitslist.findIndex(function(obj) {
    
                        var string = obj.label.toLowerCase();
                        var text = rdto.item_unit.toLowerCase();
                         
                        return text != '' ? string.includes(text) : false;
                    });
                    if(index > -1){
                        rdto.item_unit = unitslist[index].label;
                        rdto.item_unit_id = unitslist[index].id;
                        
                    }
                    else{
                        rdto.to_item_unit_id = '';
                        rdto.to_item_unit_id = '';
                    }
                }
            });
          }
		  
		  var autoCaryyingUnit = function (ui) {
            var rd = ui.rowData;
            var $inp = ui.$cell.find("input");
            var rdto = ui.rowData;
            $inp.autocomplete({
                source: unitslist,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                    rdto.to_carrying_unit    = ui.item.label;
                    rdto.to_carrying_unit_id = ui.item.id;
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rdto.to_carrying_unit = '';
                rdto.to_carrying_unit_id = '';
            }).focusout(function () {     
                
                    
                if(rdto.to_carrying_unit_id == '')
                {
                    var index = unitslist.findIndex(function(obj) {
    
                        var string = obj.label.toLowerCase();
                        var text = rdto.item_unit.toLowerCase();
                         
                        return text != '' ? string.includes(text) : false;
                    });
                    if(index > -1){
                       
                        rdto.to_carrying_unit_id = unitslist[index].id;
                        
                    }
                    else{
                       
                        rdto.to_carrying_unit_id = '';
                    }
                }
            });
          }
		  
		  var CalQtyPacked =function (ui){
			       var rdto = ui.rowData;
				 var $inp = ui.$cell.find("input");
				  $inp.on("change",function(){
					    var rowindex = ui.rowIndx;
				  });
		  }
		  
		  var autoUOMUnit = function (ui) {
            var rd = ui.rowData;
            var $inp = ui.$cell.find("input");
            var rdto = ui.rowData;
            $inp.autocomplete({
                source: unitslist,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                    rdto.to_item_unit    = ui.item.label;
                    rdto.to_item_unit_id = ui.item.id;
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rdto.to_item_unit = '';
                rdto.to_item_unit_id = '';
            }).focusout(function () {     
                
                    
                if(rdto.to_item_unit_id == '')
                {
                    var index = unitslist.findIndex(function(obj) {
    
                        var string = obj.label.toLowerCase();
                        var text = rdto.item_unit.toLowerCase();
                         
                        return text != '' ? string.includes(text) : false;
                    });
                    if(index > -1){
                       
                        rdto.to_item_unit_id = unitslist[index].id;
                        
                    }
                    else{
                       
                        rdto.to_item_unit_id = '';
                    }
                }
            });
          }
		  
		  
		  
	
        //nested data.
		<?php  foreach($level_data as $item) { ?>
		
		var sdata = [
            {
                "name": "<?php echo 'items ('.count($item['data']).')';?>",
                "data": <?php echo json_encode($item['data']);?>
			}
                   
        ];
		
		
     
        //common options used in all grids.
        var options = {
            fillHandle: '',
            blur: function () {
                this.Selection().removeAll();
            },
			  scrollModel: { autoFit: true },
            height: 300,
            numberCell: { show: false }
        };
        
        //initialize main grid
        $("#grid_md<?php echo $item['cu_id'];?>").pqGrid(Object.assign({}, options, {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 300,
            freezeCols: 1,
            dataModel: { data: sdata },            
            colModel: [
                { title: "", minWidth: 30, maxWidth: 30, type: "detail", resizable: false, editable:false, sortable: false },
                { title: '<?php echo $item['name'];?><span style="float:right;"><a href="javascript:void(0);" onClick="finalize_cu_status(<?php echo $item['packing_id'];?>,<?php echo $item['cu_id'];?>)"  class="btn btn-sm btn-outline-success">Finalize</a></span>', dataIndx: 'name',pq_detail:{"show":true}}
            ],            
            title: "",
            resizable: true, 
			postRenderInterval: 0,
           pq_detail: { 'show': true },			
            detailModel: {
                header: true,        
                collapseIcon: "ui-icon-plus",
                expandIcon: "ui-icon-minus",
                pq_detail: { 'show': true },				
                init: function (ui) {        
			      var rowData = ui.rowData,
					   rdata = rowData.data,
                        model = (rdata[0] && rdata[0].data) ? subContinentModel(rdata) : countryModel(rdata),
                        $grid = $("<div/>").pqGrid(model);

                    return $grid;
                }
            }
        }));
        
        function subContinentModel(data) {
			
            return Object.assign({}, options, {
                dataModel: { data: data },
                colModel: [
                    { title: "", minWidth: 32, maxWidth: 32, type: "detail", resizable: false, editable: false, sortable: false,pq_detail: { 'show': true }, },
                    { title: 'Sub Level', dataIndx: 'name',resizable: false, editable: false, sortable: false},					
                ],                                
                freezeCols: 1, 
			    height: 300,			
                showTop: false,
                detailModel: {
                    header: true,

                    //conditionally hide expand icon for rows who have no children.
                    hasChild: function (rowData) {
                        return (rowData.data || []).length > 0;
                    },
                    init: function (ui) {
						console.log(ui.rowData);
                        var rowData = ui.rowData,
                            rdata = rowData.data,
                            model = (rdata[0] && rdata[0].data) ? subContinentModel(rdata) : countryModel(rdata),
                            $grid = $("<div/>").pqGrid(model);

                        return $grid;
                    }
                }

            });
        };

        function countryModel(data) {
			
			
            return Object.assign({}, options, {
                dataModel: { data: data },
                colModel: [                    
                     { title: "ITEM NAME", dataIndx: "item_name", align: "left",width: 100,editable:false,pq_detail:{"show":true},cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox",
                          init: autoCompleteEditor,
                          options: itemlist
                      }
					},
					
					 { title: "BATCH NO", dataIndx: "batch_no", align: "left",width: 60,editable:false,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox"
                      }
					},
				{ title: "SPECIFICATION NO", dataIndx: "specification_no", align: "left",editable:false,width: 60,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox"
                      }
					},	
					
          { title: "QTY",dataIndx: "item_qty", width: 20, dataType: "float",                 
                validations: [{ type: 'gt', value: 0, msg: "should be > 0"}],               
              },
          { title: "UOM", dataIndx: "item_unit", width: 20,editable: false,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox",
                          init:autoCompleteEditor2,
                          options: [],
                      },
                      render: function (ui) {
                          var options = ui.column.editor.options,
                          cellData = ui.cellData;
                          for (var i = 0; i < options.length; i++) {
                              var option = options[i];
                                if (option.label == cellData) {
                                  return option.label;
                                }
                          }
                      },
            },
            
         { title: "REVISED QTY", width: 20, dataType: "float",editor: {                   
                		  type: "textbox",
                          init:CalQtyPacked,
                          options: [],
                      },render: function (ui) {
				            var rd = ui.rowData;
				            var cellData=render_qty(ui.cellData);  
							rd.to_item_qty=cellData;
						    return cellData;
						  }, dataIndx: "to_qty_packed"
          
               
          }, 
             { title: "", editable: false, minWidth: 165, sortable: false,
                        render: function (ui) {
							var rd = ui.rowData;
                            return '<img src="<?php echo base_url();?>/public/assets/img/icon-done.png" onClick="callfunction(\''+ui.rowIndx+'\',\''+rd.cu_id+'\')">\<img src="<?php echo base_url();?>/public/assets/img/icon-close.png" onClick="unpack_item(\''+ui.rowIndx+'\',\''+rd.cu_id+'\')">';
                        }
                       
                    }
            
                ],
                showTop: false
            });
        };
		<?php } ?>
	
});


function unpack_item(rowindex,cu2){
	var rowData = 	$('#grid_md'+cu2).pqGrid( "getRowData", {rowIndx: 0} ); 
		 var childgrid = rowData.pq_detail.child;
		 $(childgrid).pqGrid( "showLoading" );
		var subrow=  $(childgrid).pqGrid( "getRowData", {rowIndx: rowindex} ); 
		
		
		 
		 subrow.pack_level=$("#pack_level").val();
 subrow.packing_type=$("input[name='packing_type']:checked").val();
 subrow.packing_list_id='<?php echo $packing_list_id;?>';
 //console.log(subrow);
 $(childgrid).pqGrid( "deleteRow", { rowIndx: rowindex } );
 
  $.post('<?php echo base_url();?>/admin/consignment_packing/unpack_cu_item', subrow, function(response) { 
	
	    $(childgrid).pqGrid( "refreshDataAndView");
    	$(childgrid).pqGrid( "hideLoading" );
  });
	
	
}
 
function callfunction(rowindex,cu2){
		
		 var rowData = 	$('#grid_md'+cu2).pqGrid( "getRowData", {rowIndx: 0} ); 
		 var childgrid = rowData.pq_detail.child;
		
		var subrow=  $(childgrid).pqGrid( "getRowData", {rowIndx: rowindex} ); 
		
		
		
		
		subrow.item_qty.editable = true;
		
		if(subrow.to_qty_packed=='' || subrow.to_qty_packed=='0'){
			alert("revised qty required!!");
		
			 $(childgrid).pqGrid( "updateRow",{ rowIndx: rowindex, row: {'to_qty_packed': '' }}); 
			  $(childgrid).pqGrid( "refreshDataAndView");
			return false;
		}
		
		else if(subrow.to_qty_packed > subrow.qty_available){
		   alert(subrow.item_name + ' Qty should be less than or equal to '+subrow.qty_available);	
			return false;
		}
		else{
			
			
			
			
		 $(childgrid).pqGrid( "showLoading" );
		 
 subrow.pack_level=$("#pack_level").val();
 subrow.packing_type=$("input[name='packing_type']:checked").val();
 subrow.packing_list_id='<?php echo $packing_list_id;?>';
  $.post('<?php echo base_url();?>/admin/consignment_packing/revised_cu_qty', subrow, function(response) { 
 if(response=='0'){
	 
	  $.each(subrow, function(index,obj){
            rowData[index] = '';
        });
	 
       }
	
     $(childgrid).pqGrid( "updateRow",{ rowIndx: rowindex, row: {'item_qty': response,'to_qty_packed': '' }});      
	    $(childgrid).pqGrid( "refreshDataAndView");
    	$(childgrid).pqGrid( "hideLoading" );
		
		subrow.item_qty.editable = false;
  });
}
}

function update_revised_qty_row(rowindex,gridindex){
    
  
     
  //	$('#grid_md').pqGrid( "showLoading" );
 

 var rowData = 	$('#grid_md').pqGrid( "getRowData", {rowIndx: rowindex} ); 
 
 console.log(rowData);
 
 
 rowData.pack_level=$("#pack_level").val();
 rowData.packing_type=$("input[name='packing_type']:checked").val();
 rowData.packing_list_id='<?php echo $packing_list_id;?>';

 $.post('<?php echo base_url();?>/admin/consignment_packing/revised_cu_qty', rowData, function(response) { 
 if(response=='0'){
	  $.each(rowData, function(index,obj){
            rowData[index] = '';
        });
	 
 }
	 	//$('#grid_md').pqGrid( "deleteRow", { rowIndx: rowindex } );
 
 
    	$('#grid_md').pqGrid( "updateRow",{ rowIndx: rowindex, row: {'item_qty': response,'to_qty_packed': '' }});  
   
	
		$('#grid_md').pqGrid( "refreshDataAndView"); 

    	$('#grid_md').pqGrid( "hideLoading" );		
	
  });  
    
}



</script>
</body>
</html>
