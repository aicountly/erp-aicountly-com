<?php $header = array( 	'title' => 'Mapped Member Masters' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>

<div class="row mb-2">
<div class="col-md-6 order-1"><h3>Mapped Member Masters</h3></div>
<div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
     <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>   
   
    <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 

    <a href="#"><span class="material-symbols-outlined">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></a>
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
     </li>
  <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>  
    </div>
    </div> 

 <div class="col-md-9 order-2 order-md-3">
 
   <a href="<?php echo base_url();?>/admin/master_mapping/mapped_group_masters?criteria=<?php echo $sel_criteria;?>"><button class="btn btn-success m-1 " type="button">Mapped Group Masters</button></a> 
   <a href="<?php echo base_url();?>/admin/master_mapping/ummapped_group_masters?criteria=<?php echo $sel_criteria;?>"><button class="btn btn-success m-1 " type="button">Unmapped Group Masters</button></a>
   <a href="<?php echo base_url();?>/admin/master_mapping/mapped_member_masters?criteria=<?php echo $sel_criteria;?>"><button class="btn btn-success m-1 active" type="button">Mapped Member Masters</button></a>
	<a href="<?php echo base_url();?>/admin/master_mapping/unmapped_member_masters?criteria=<?php echo $sel_criteria;?>"><button class="btn btn-success m-1" type="button">Unmapped Member Masters</button></a>
		
  
 </div>  
  <div class="col-md-3 text-md-end order-4 collapse listmenu" id="listmenu">
      <button class="btn btn-success m-1" type="button">View</button> 
        <a href="<?php echo history_back();?>" class="btn btn-outline-success showinline-md">Back</a>
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
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>



</div> 

<div id="validation_errors"></div>
<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
    

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
   
</form>
  
		 
<?php echo view('includes/footer_scripts'); 

$json_data = $master_lists;  
 
?>
<style>
  .boldcell{font-weight:700;}
</style>

<script>
var item_qty_balance = {};
function get_item_balances(item_id,item_unit_id,rd){
	 console.log(item_id +","+item_unit_id);
	 var voucher_date = $("#sale_date").val();	
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

  var item_qty_balance = {};
  var item_qty_balance_b = {};
  $(document).on('change', '#sale_date', function(e){

        var voucher_date = $("#sale_date").val();
      

        $.each(item_qty_balance, function(index, value){
              $.each(value, function(indexn, valuen){
             item_qty_balance_b[index]=indexn
              });
        });
        
    
        $.ajax({
            url: '<?php echo $base_url; ?>ajax/get_all_item_balances', 
            type: 'POST',
            data: {voucher_date: voucher_date, item_id_array: item_qty_balance_b},
            dataType: "json",
            beforeSend: function() {
                show_loader();
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    if(response.item_balances.length > 0){
                        $.each(response.item_balances, function(index, obj){
                            if(item_qty_balance.hasOwnProperty(obj.item_id)) {
                                item_qty_balance[obj.item_id][obj.item_unit_id] = obj.balance;
                            }
                        });
                        update_grid_item_balances();
                    }
                }
                
            },
            complete: function() {
                stop_loader();
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
  function update_grid_item_balances() {
        var data = $("#grid_search").pqGrid('option', 'dataModel.data');

        $.each(data, function(index,obj){
            if(obj.item_id != '' && obj.item_id != undefined && obj.item_unit_id != '' && obj.item_unit_id != undefined)
            {
                data[index]['item_balance'] = item_qty_balance[obj.item_id][[obj.item_unit_id]];
            }
        });


        $("#grid_search").pqGrid('option', 'dataModel.data', data);
        $("#grid_search").pqGrid('refreshDataAndView');
   }
   
   
    var intRegex = /^\d+$/;
var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;


var itemlist  = [];

    
   $("#submitbtn").on("click",function(){
     var item_checked = [];
     var billsundry_item_checked = [];
     var final_item_id =[];
     var data = $("#grid_search").pqGrid('option', 'dataModel.data');
     var billsundry_data = $("#billsundry_search").pqGrid('option', 'dataModel.data');
     
     
     var final_item_amouint ="0";
     var sdateFrom = '<?php echo $fy_begndt;?>';
    var sdateTo   = '<?php echo $fy_end;?>';
    var sdateCheck  =  $("#sale_date").val();
    var sd1 = sdateFrom.split("-");
    var sd2 = sdateTo.split("-");
    var sc  = sdateCheck.split("-");
    
    var sfrom_year = sd1[2];  // -1 because months are from 0 to 11
    var sto_year   = sd2[2];
    var scheck_year = sc[2];//, parseInt(c[1])-1, c[0]);
    
    if( (scheck_year==sfrom_year) || (scheck_year==sto_year))
     var sdatevalidate=1;
    else
     var sdatevalidate=0;
     
   
   for (var j = 0; j < billsundry_data.length; j++) {
        var billsundry_id     = billsundry_data[j]['billsundry_id'];
        var billsundry_name     = billsundry_data[j]['billsundry_name'];
        var billsundry_rate     = billsundry_data[j]['billsundry_rate'];
        var billsundry_amount     = billsundry_data[j]['billsundry_amount'];
        
        if(billsundry_id != '' && billsundry_name != ''){
            billsundry_item_checked.push({
                    "billsundry_id": billsundry_id,
                    "billsundry_name": billsundry_name,
                    "billsundry_rate": parseAmount(billsundry_rate),
                    "billsundry_amount" : parseAmount(billsundry_amount),
                 });
        }
        
       
   }
   
      var stock_qtyalert=0;
       var item_total_qty={};
   
       for (var i = 0; i < data.length; i++) {
           var item_id     = data[i]['item_id'];
           var item_name     = data[i]['item_name'];
           var item_price  = data[i]['item_price'];
           var item_qty    = data[i]['item_qty'];
           var item_unit   = data[i]['item_unit'];
           var item_unit_id   = data[i]['item_unit_id'];
           var description = data[i]['description'];
           var item_amount = data[i]['item_amount'];
           var voucher_type_id = data[i]['voucher_type_id'];
           var voucher_txn_id = data[i]['voucher_txn_id'];
            var avail_item_qty = data[i]['AvailQty'];
           
           if(!item_amount)
             item_amount =0;
             final_item_amouint += parseAmount(item_amount);
            
            if(item_id != '' && item_name != ''){
              
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
			       
                item_checked.push({
                   "item_id": item_id,
                    "item_price": parseAmount(item_price),
                    "item_qty" :item_qty,
                    "item_unit" :item_unit,
                    "item_unit_id" :item_unit_id,
                    "item_total_amount" :parseAmount(item_amount),
                    "description" :description,
                    "voucher_type_id":voucher_type_id,
                    "voucher_txn_id" : voucher_txn_id
                });
             
            }
         }
      
      var selerror=0;
      if($('select').hasClass('required')){
          $(".required ").each(function() {
           var txtbx_val = $(this).val();
           if(txtbx_val.length=="0"){
              selerror=1;
              $(this).css('border-color','#ed2000');
              }else{
                $('.custom-combobox input').removeAttr("style");  
                $(this).parent('select').removeClass('required');  
                $(this).css("border-color","#cccccc");
               }
             });
           }
      
      if( $("#voucher_series").val()==''  || selerror=='1'){
          alert_notification("Kindly fill the form properly!!!");
          return false;   
       }
       else if(stock_qtyalert >0 ){
		  alert("Qty should be less than or equal to stock available!!!");
          return false;  
	  }
      
       else if(item_checked.length==0 || $("#voucher_series").val()=='' || final_item_amouint=='' || final_item_amouint=='0'){
          alert_notification("Kindly fill the items data!!!");
          return false;   
       }
        else if(sdatevalidate==0){
      alert_notification("voucher date is worng!!!");
          return false;    
      } 
       else{
           
       //    console.log(item_checked);
        $("#itmsdata").val(JSON.stringify(item_checked));
         $("#billsndrydata").val(JSON.stringify(billsundry_item_checked));
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
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    alert_success(response.message);
                    window.location.reload();
                }
                else{
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
         
        
        var colModel = [
		 { title: "MEMBER COMPANY NAME", dataIndx: "member_company_name", width: 100,editable:false},
		 { title: "MEMBER COMPANY MASTER NAME",dataIndx: "member_company_master_name", width: 100,editable:false},		   
		 { title: "MASTER ID", dataIndx: "master_id", width: 100,editable:false},
         { title: "GROUP COMPANY MASTER NAME", dataIndx: "group_company_master_name", width: 100},
         { title: "ACTION", dataIndx: "action", width: 100,editable:false}      
		         
         ];            
        var dataModel = {"data": <?= json_encode($json_data) ?>}       
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 470,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel: colModel,  
            numberCell: { show: false },
            editable: true,
            cellSave: function(evt, ui){
                   this.refresh();
               },
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            wrap:false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
            this.widget().pqTooltip();
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: true });
              }
        };
        newObj.cellKeyDown = function(evt, ui) {
           var rowData = ui.rowData;
           var rowIndx = ui.rowIndx;

           if (evt.keyCode == 46){
                $.each(rowData, function(index,obj){
                    rowData[index] = '';
                });
                this.refreshDataAndView();
                return false;
           }
        }
        var $grid = $("#grid_search").pqGrid(newObj);
     
          
        
     });
function update_group_master_row(rowindex,grpmpid,crs_master_id){
	$('.pq-grid').pqGrid( "showLoading" );

 var rowData = $('.pq-grid').pqGrid( "getRowData", {rowIndx: rowindex} );
 console.log(rowData);
 rowData.grpmpid=grpmpid;
 rowData.crs_master_id=crs_master_id;

 
 $.post('<?php echo base_url();?>/admin/master_mapping/update_group_masters', rowData, function(response) {
	 
	$( ".pq-grid" ).pqGrid( "refreshDataAndView"); 

    $('.pq-grid').pqGrid( "hideLoading" );	 
 });
	
 
  //console.log(response);
  //console.log(rowindex);
  //  $('.pq-grid').pqGrid( "updateRow",{ rowIndx: rowindex, row: {'item_qty': response,'to_carrying_unit': '','to_qty_packed': '','to_cu_label': ''  }});  
   
   
 //  var rowDatasss = $('.pq-grid').pqGrid( "getRowData", {rowIndx: rowindex} );
   // console.log(rowDatasss);
	
	
		


}

function remove_group_master_row(rowindex,grpmpid,crs_master_id){
	$('.pq-grid').pqGrid( "showLoading" );
  
 var rowData = $('.pq-grid').pqGrid( "getRowData", {rowIndx: rowindex} );
console.log(rowData);
 return false;
/*

 $.post('<?php echo base_url();?>/admin/consignment_packing/remove_packing', rowData, function(response) {   
   
    $('.pq-grid').pqGrid( "updateRow",{ rowIndx: rowindex, row: { 'item_qty': response }});  
    $('.pq-grid').pqGrid( "hideLoading" );	
	$( ".pq-grid" ).pqGrid( "refreshDataAndView");

  }); */

}

 </script>	
</body>
</html>
