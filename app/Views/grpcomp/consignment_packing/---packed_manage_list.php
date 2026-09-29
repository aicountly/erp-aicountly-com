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
		     <div class="col-6">  <h3 class="pb-3">Manage Packing List(<?php echo $packing_info['list_name'];?>)</h3></div>
		     <div class="col-md-6 text-end"><div class="taskmenus">
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
             
            <div class="col-sm-6">
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
			
               <div class="col-md-4 text-end">
				<?php if(count($cupackingn) > 0) { ?>
				<a href="<?php echo base_url();?>/admin/consignment_packing/finalize_packing_status/<?php echo $packing_info['list_id'];?>" class="btn btn-outline-success btn-sm"> Finalize Packing</a> 
				<?php } ?>
			   <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a> </div> 
             

			 </div>


				
           
    
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
    
    function buildList(array $array): string
{
    $menu = "<ul>";
    foreach($array as $item) {
        if(isset($item['name']))
        $menu .= "<li><a href='#'>{$item['name']}</a></li>";
       if (isset($item['data'])) {
            $menu .= buildList($item['data']);
       }
    }
    $menu .= "</ul>";

    return $menu;
}
  //  echo buildList($level_data);
  
 // echo '<pre>';
 // print_r($level_data);
    ?>
	
	<div class="accordion accordion-flush" id="accordionFlushExample">
  
  
  <div class="accordion-item">
  <?php  foreach($level_data as $item) { ?>
    <h2 class="accordion-header" id="flush-headingOne">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne<?php echo $item['cu_id'];?>" aria-expanded="false" aria-controls="flush-collapseOne">
       <?php echo $item['name'];?>
      </button>
    </h2>
    <div id="flush-collapseOne<?php echo $item['cu_id'];?>" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
      <div class="accordion-body">
	  <div id="grid_md<?php echo $item['cu_id'];?>" style="margin:5px auto;"></div>
	  
	  </div>
    </div>
  <?php } ?>
  </div>

</div>
	
   <?php 
     for($i=1;$i<=50;$i++){ 
		$l1_grid_data[] =array("item_unit_id"=>"","batch_no"=>"","specification_no"=>"","item_id"=>"","id"=>'',"pq_cellattr"=>array("item_name"=>array("title"=>"")),"item_name"=>'','item_qty'.$i=>'','item_unit'=>'',"to_carrying_unit_id"=>"","to_carrying_unit"=>"",'to_qty_packed'=>'','to_cu_save'=>'');
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
 if(response=='0')
	 $('.pq-grid').pqGrid( "deleteRow", { rowIndx: rowindex } );
 
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
		  
	

	
	
	});
</script>

<script>
<?php  foreach($level_data as $item) { ?>
	  var data = <?php echo json_encode($item['data']);?>;

        //common options used in all grids.
        var options = {
            fillHandle: '',
            blur: function () {
                this.Selection().removeAll();
            },
			  scrollModel: { autoFit: true },
            height: 600,
            numberCell: { show: false }
        };
        
        //initialize main grid
        $("#grid_md<?php echo $item['cu_id'];?>").pqGrid(Object.assign({}, options, {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 600,
            freezeCols: 1,
            dataModel: { data: data },            
            colModel: [
                { title: "", minWidth: 30, maxWidth: 30, type: "detail", resizable: false, editable:false, sortable: false },
                { title: 'Continent', dataIndx: 'name'}
            ],            
            title: "",
            resizable: true, 
			postRenderInterval: 0,	
            detailModel: {
                header: true,        
                collapseIcon: "ui-icon-plus",
                expandIcon: "ui-icon-minus",				
                init: function (ui) {        
			       console.log(ui.rowData);
				
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
                    { title: "", minWidth: 32, maxWidth: 32, type: "detail", resizable: false, editable: false, sortable: false },
                    { title: 'Sub Level', dataIndx: 'name',resizable: false, editable: false, sortable: false },					
                ],                                
                freezeCols: 1, 
			    height: 600,			
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
                     { title: "ITEM NAME", dataIndx: "item_name", align: "left",width: 100,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox",
                          init: autoCompleteEditor,
                          options: itemlist
                      }
					},
					
					 { title: "BATCH NO", dataIndx: "batch_no", align: "left",width: 60,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox"
                      }
					},
				{ title: "SPECIFICATION NO", dataIndx: "specification_no", align: "left",width: 60,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox"
                      }
					},	
					
          { title: "QTY",dataIndx: "item_qty", width: 20, dataType: "float",
                 
                validations: [{ type: 'gt', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
                   var item_id = ui.rowData['item_id'];
                    if (item_id != '')  
                        return true;              
                    return false;
                },
              },
          { title: "UOM", dataIndx: "item_unit", width: 20,editable: function (ui) {
               var item_id = ui.rowData['item_id'];
                if (item_id != '') {
                    return true;
                }
                return false;
            },cls: 'pq-drop-icon pq-side-icon',editor: {                   
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
                            return '<button type="button" onClick="callfunction(\''+ui.rowIndx+'\',\''+rd.cu_id+'\')" class="edit_btn">Edit</button>\
                                <button type="button">Delete</button>';
                        }
                       
                    }
            
                ],
                showTop: false
            });
        };
<?php } ?>
	  </script>
	  
	  
</body>
</html>
