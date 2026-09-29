<?php $header = array( 	'title' => 'Stock Transfer' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>

<div class="row mb-2">
<div class="col-md-6 order-1"><h3>Stock Transfer (other MC)</h3></div>
<div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
     <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>   
    <a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history"><span class="material-symbols-outlined">visibility</span></a>
    <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 

    <a href="#"><span class="material-symbols-outlined open-comingsoon">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined open-comingsoon">download</span></a>
    <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">CSV</a></li>
    <li><a class="dropdown-item" href="#">Excel</a></li>
    <li><a class="dropdown-item" href="#">Document</a></li>
  </ul>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon">share</span></a> 
      <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Facebook</a></li>
    <li><a class="dropdown-item" href="#">Twitter</a></li>
    <li><a class="dropdown-item" href="#">Instagram</a></li>
  </ul>
     </li>
  <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>  
    </div>
    </div> 

 <div class="col-md-6 order-2 order-md-3">
     
    <select form="salefrm" name="currency_id" class="form-select d-inline-block" style="width:160px;">
       <?php foreach ($currency_list as $value) { ?>
            <option value="<?= $value['comp_currency_id'] ?>"><?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
       <?php } ?>
    </select>

    <div class="form-check d-inline-block me-2">
      <input class="form-check-input" type="checkbox" value="1" id="nsCheck">
      <label class="form-check-label" for="nsCheck">Negative Stock</label>
    </div>

 </div>  
  <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
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
    <div class="col-12">
        <div class="row m-0 p-0">

      <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Transfer Type:</label>
        <select name="transfer_type" class="form-control form-control-sm">
            <option selected value="type1">Stock Transfer (other MC)</option>
            <!--<option value="type2">Stock Transfer Receipt</option>
            <option value="type3">Stock Transfer (other BO)</option>-->
        </select>
      </div></div>
	 <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Date:</label> <input type="text" name="sale_date" id="sale_date" value="<?php echo $voucher_date;?>" class="datepicker form-control form-control-sm" readonly></div></div>  
      <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Series:</label>
      <?php	
        echo form_dropdown('voucher_series', $voucher_series_dropdown, '15',' id="voucher_series" class="selectwidget voucher_series form-control required" ');
		?></div></div>
      <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Voucher No:</label> <input type="text" name="voucher" class="form-control form-control-sm" value="<?php echo $voucher_auto_no;?>" disabled></div></div>
      
       <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Transporter:</label> <input type="text" name="transporter" class="form-control form-control-sm"></div></div>
      
      <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">E-Way:</label> <input type="text" name="e-way" class="form-control form-control-sm"></div></div>
      
      <div class="col-md-4 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">From MC:</label>  <?php	
        echo form_dropdown('frommc', $matrcntr_dropdown, '',' id="frommc" class="form-select required" ');
		?></div></div>
      
      <div class="col-md-4 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">To MC:</label> <?php	
        echo form_dropdown('tomc', $matrcntr_dropdown, '',' id="tomc" class="selectwidget form-control required" ');
		?></div></div>
      
      <div class="col-md-12 col-12 card p-2"><div class="input-group">
              <label class="input-group-text">Long Narration:</label>
			    <textarea  name="long_narration" class="form-control"></textarea>	

			 </div>
      <input type="hidden" name="itmsdata" id="itmsdata">
      <input type="hidden" name="billsndrydata" id="billsndrydata">

    </div></div>
    
    </div>
   

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
    <div class="col-12 "><div class="row">
       <!-- Tax Summary -->
       <div class="col-md-6">
           <h4 class="text-center">Tax Summary</h4>
           <div id="taxgrid_search" style="margin:auto;"></div>  
         
  </div>
       <!-- Tax Summary Ends -->
      
      <!-- Bill Summary Starts -->
       <div class="col-md-6">
           <h4 class="text-center">Bill Sundry</h4>
        <div id="billsundry_search" style="margin:auto;"></div> 
  </div>

    </div> 
    
     <div class="col-12 text-center">
         <br><br>
         <!--<input type="file" class="">-->
         
        <button type="button" id="submitbtn" class="btn btn-success btn-lg" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">Save</button>
        <button type="reset" id="submitbtn" class="btn btn-success btn-lg">Reset</button>
        <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-secondary btn-lg">Quit</a>
        
    </div>
        
    </div>
   
</form>
<?php echo view('includes/footer_scripts'); 
for($i=1;$i<=50;$i++)
    $json_data[] =array("item_id"=>'',"item_name"=>'',"pq_cellattr"=>array("item_name"=>array("title"=>"")),'item_qty'=>'','description'=>'','item_unit'=>'','item_unit_id'=>'','item_price'=>'','item_amount'=>'');

for($i=1;$i<=10;$i++)
    $tax_json_data[] =array("id"=>'',"tax_rate"=>'','tax_amt'=>'','igst'=>'','cgst'=>'','sgst'=>'');

for($i=1;$i<=10;$i++)
    $billsundry_json_data[] = array("billsundry_id"=>'',"billsundry_name"=>'','billsundry_rate'=>'','billsundry_amount'=>'');
?>

<style>
    .boldcell{font-weight:700;}
</style>
<?php
$itemn_units_labels=array();
?>
<script>
var item_qty_balance_json = {};

    function get_item_last_balance(rowData)
    {
        var status = true;
        if(item_qty_balance_json[rowData.item_id]){
            if(item_qty_balance_json[rowData.item_id][rowData.item_unit_id]){
                rowData.pq_cellattr ={
                    "item_name" : { "title":get_tooltip_html(item_qty_balance_json[rowData.item_id][rowData.item_unit_id]) },
                    "item_unit" : { "title":get_tooltip_html(item_qty_balance_json[rowData.item_id][rowData.item_unit_id]) },
                };
                status = false;
            }
        }
        
        if(status)
        {
            rowData.pq_cellattr ={
                "item_name" : { "title":"Fetching quantity, please wait!" },
                "item_unit" : { "title":"Fetching quantity, please wait!" },
            };

            var mc_id = $("#frommc").val();
            var date = $("#sale_date").val();

            $.ajax({
                url: '<?php echo $base_url; ?>ajax/get_item_last_qty_balance', 
                type: 'POST',
                data: {item_id: rowData.item_id, unit_id: rowData.item_unit_id, mc_id: mc_id, date: date, voucher_txn_id: <?= $voucher_txn_id ?? 0 ?>},
                dataType: "json",
				async:true,
                beforeSend: function() {
                    
                },
                success: function (response) {

                    if(response.status){

                        if(item_qty_balance_json[rowData.item_id]){
                            item_qty_balance_json[rowData.item_id][rowData.item_unit_id] = response.balance;
                        }
                        else{
                            item_qty_balance_json[rowData.item_id] = {
                                [rowData.item_unit_id] : response.balance,
                            };
                        }
                     
                        rowData.pq_cellattr ={
                            "item_name" : { "title":get_tooltip_html(response.balance) },
                            "item_unit" : { "title":get_tooltip_html(response.balance) },
                        };

                        $("#grid_search").pqGrid('refreshDataAndView');
                        
                    }
                    
                },
                complete: function() {
                    
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

        }
    }

    function get_tooltip_html(obj){
        var html = `
            <table style='width:100%;'>
                <tr>
                    <td colspan='2'>${obj.unit_name}</td>
                </tr>
                <tr>
                    <td>Available</td>
                    <td align='right'>${obj.AvailQty}</td>
                </tr>
                <tr>
                    <td>Packed</td>
                    <td align='right'>${obj.PackQty}</td>
                </tr>
                <tr>
                    <td>Obselete</td>
                    <td align='right'>${obj.ObseQty}</td>
                </tr>
                <tr>
                    <td>In Transit</td>
                    <td align='right'>${obj.IntrsQty}</td>
                </tr>
            </table>`;
        return html;
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

  
    get_all_item_last_qty_balances();

    function get_all_item_last_qty_balances()
    {

        var date = $("#sale_date").val();
        var mc_id = $("#frommc").val();
        console.log('dfdfd');
      
        var items_data = [];
        $.each(item_qty_balance_json, function(index, obj){
            $.each(obj, function(index2, obj2){
                items_data.push({
                    "item_id" : index,
                    "unit_id" : index2,
                    "mc_id" : mc_id,
                    "date" : date,
                });
            });
        });

        if(items_data.length > 0){
            $.ajax({
                url: '<?php echo $base_url; ?>ajax/get_all_item_last_qty_balances', 
                type: 'POST',
                data: {items_data: items_data, voucher_txn_id: <?= $voucher_txn_id ?? 0 ?>},
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
                                if(item_qty_balance_json.hasOwnProperty(obj.item_id)) {
                                    item_qty_balance_json[obj.item_id][obj.unit_id] = obj.balance;
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
        }
    }

    $('#sale_date').change(function(e){
        get_all_item_last_qty_balances();
    });

    $('#frommc').change(function(e){
        get_all_item_last_qty_balances();
    });

    function update_grid_item_balances() {
        var data = $('#grid_search').pqGrid('option', 'dataModel.data');

        $.each(data, function(index,obj){
            if(obj.item_id != '' && obj.item_id != undefined && obj.item_unit_id != '' && obj.item_unit_id != undefined)
            {
                if(item_qty_balance_json[obj.item_id]){
                    if(item_qty_balance_json[obj.item_id][obj.item_unit_id]){

                        data[index]['pq_cellattr']  = {
                            "item_name" : { "title":get_tooltip_html(item_qty_balance_json[obj.item_id][obj.item_unit_id]) },
                            "item_unit" : { "title":get_tooltip_html(item_qty_balance_json[obj.item_id][obj.item_unit_id]) },
                        };
                    }
                }
               
            }
        });


        $('#grid_search').pqGrid('option', 'dataModel.data', data);
        $('#grid_search').pqGrid('refreshDataAndView');
    }


var itemlist  = [];
var unitslist = <?php echo json_encode($units_list);?>; 
    
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

        var stock_status_errors = 0;

        if(!$('#nsCheck').is(":checked"))
        {
            var final_item_qty_json = {};
            $.each(item_checked, function(index, object){
                if(final_item_qty_json[object.item_id]){
                    if(final_item_qty_json[object.item_id][object.item_unit_id])
                        final_item_qty_json[object.item_id][object.item_unit_id] += parseFloat(object.item_qty);
                    else
                        final_item_qty_json[object.item_id][object.item_unit_id] = parseFloat(object.item_qty);
                }
                else
                    final_item_qty_json[object.item_id] = {[object.item_unit_id] : parseFloat(object.item_qty)};
            });
            
            $.each(final_item_qty_json, function(index, object){
                $.each(object, function(index2, object2){
                    var item_id = index;
                    var unit_id = index2;
                    var quantity = object2;

                    if(item_qty_balance_json[item_id]){
                        if(item_qty_balance_json[item_id][unit_id]){
                            if(item_qty_balance_json[item_id][unit_id]['AvailQty'] >= quantity){
                                
                            }
                            else
                                stock_status_errors++;
                        }
                        else
                            stock_status_errors++;
                    }
                    else
                        stock_status_errors++;
                })
            });
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
       else if(stock_status_errors >0 ){
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
                    <?php if(isset($_GET['p']) && $_GET['p'] == 1){ ?>
                        window.history.back();
                    <?php } else { ?>
                        window.location.reload();
                    <?php } ?>
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
       function billsundry_calculateSummary() {
        var itempriceTotal = 0,
            itemamountTotal = 0,
            itemqtyTotal=0,
            data = this.option('dataModel.data'),
            len  = data.length;

        data.forEach(function(row){
           
            if(row.billsundry_amount == '' || typeof row.billsundry_amount == 'undefined'){
                var billsundry_amount =0;
            }else
             var billsundry_amount =  row.billsundry_amount;
             
           
            itemamountTotal += parseAmount(billsundry_amount);
           
        })

        var totalData = {
                billsundry_name: "Total",
                billsundry_rate  : "",
                billsundry_amount: itemamountTotal,
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            this.option('summaryData', [totalData]);
          }     
       function calculateSummary() {
        var itempriceTotal = 0,
            itemamountTotal = 0,
            itemqtyTotal=0,
            data = this.option('dataModel.data'),
            len  = data.length;

        data.forEach(function(row){
            if(row.item_price == '' || typeof row.item_price == 'undefined'){
                var item_price =0;
            }else
             var item_price =  row.item_price;
            
            if(row.item_qty == '' || typeof row.item_qty == 'undefined'){
                var item_qty =0;
            }else
            var  item_qty =  row.item_qty;
            
            if(row.item_amount == '' || typeof row.item_amount == 'undefined'){
                var item_amount =0;
            }else
             var item_amount =  row.item_amount;
             
            itempriceTotal  += parseAmount(item_price);
            itemamountTotal += parseAmount(item_amount);
            itemqtyTotal    += parseFloat(item_qty); 
        })

        var totalData = {
                item_name: "Total",
                item_qty  : itemqtyTotal,
                item_price: itempriceTotal,
                item_amount: itemamountTotal,
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            this.option('summaryData', [totalData]);
          }
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
        var rd = ui.rowData;
        var element= {};
        var grid = this;

        var voucher_date = $("#sale_date").val();
        var mc_id = $("#frommc").val();
        if(voucher_date == ''){
            grid.saveEditCell();
            alert_notification('Enter Voucher Date first!');

            return false
        }
        if(mc_id == ''){
            grid.saveEditCell();
            alert_notification('Enter Material Center first!');

            return false
        }

        $inp.autocomplete({
                source:  <?php echo $item_json_file;?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();

                    rd.item_id = ui.item.item_id;
                    rd.item_name = ui.item.label;
                    rd.item_unit = ui.item.item_unit;
                    rd.item_unit_id = ui.item.item_unit_id;
                    $(this).val(ui.item.label);
                    get_item_last_balance(rd); 
                 }  
            }).focus(function () {               
                $(this).autocomplete("search", "");
                rd.item_id = '';
                rd.item_name = '';
                rd.item_unit = '';
                rd.item_unit_id = '';
            }).focusout(function () {                   
       
                if(rd.item_id == '')
                {
                   rd.item_id = '';
                rd.item_name = '';
                rd.item_unit = '';
                rd.item_unit_id = '';
                }
            });
           
        }
        var autoCompleteEditor2 = function (ui) {
            var rd = ui.rowData;
            var $inp = ui.$cell.find("input");
            var grid = this;

            var voucher_date = $("#sale_date").val();
            var mc_id = $("#frommc").val();
            if(voucher_date == ''){
                grid.saveEditCell();
                alert_notification('Enter Voucher Date first!');

                return false
            }
            if(mc_id == ''){
                grid.saveEditCell();
                alert_notification('Enter Material Center first!');

                return false
            }

            $inp.autocomplete({
                source: unitslist,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                    rd.item_unit = ui.item.label;
                    rd.item_unit_id =ui.item.id;
                    get_item_last_balance(rd);
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.item_unit = '';
                rd.item_unit_id = '';
            }).focusout(function () {        
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
                        get_item_last_balance(rd);
                    }
                    else{
                        rd.item_unit = '';
                        rd.item_unit_id = '';
                    }
                }
            });
          }

        var colModel = [
                     { title: "ITEM NAME",sortable:false, dataIndx: "item_name", width: 100,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                          type: "textbox",
                          init: autoCompleteEditor,
                          options: []
                      }
              },
              { title: "QUANTITY",sortable:false,dataIndx: "item_qty", width: 100, dataType: "float",
                 
                validations: [{ type: 'gt', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
                   var item_id = ui.rowData['item_id'];
                    if (item_id != '')  
                        return true;              
                    return false;
                },
                editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        $inp.on("change", function (evt) {
                            if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_price != '' && parseAmount(rd.item_price) >= 0){
                                var amount = rd.item_qty * parseAmount(rd.item_price);
                                rd.item_amount = amount;  
                                grid.refreshDataAndView();     
                            } 
                        })
                    }
                },
                render: function (ui) {
                    var rd = ui.rowData;
                    var cellData=render_qty(ui.cellData); 
                    rd.item_qty=cellData;
                    
                    return cellData;
                },
              },
              { title: "SHORT NARRATION",sortable:false, width: 100, dataType: "string",editable: function (ui) {
               var item_id = ui.rowData['item_id'];
                if (item_id != '') {
                    return true;
                }
                return false;
            }, dataIndx: "description"},
              { title: "UNIT", sortable:false,dataIndx: "item_unit", width: 100,editable: function (ui) {
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
            { title: "PRICE", sortable:false,width: 20, align: "right",dataIndx: "item_price",dataType: "float",
                validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
                   var item_id = ui.rowData['item_id'];
                    if (item_id != '') {
                        return true;
                    }
                    return false;
                },
                editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        
                        $inp.on("change", function (evt) {
                            if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_price != '' && parseAmount(rd.item_price) >= 0){
                                var amount = rd.item_qty * parseAmount(rd.item_price);
                                rd.item_amount = amount;  
                                grid.refreshDataAndView(); 

                            }
                            if(rd.item_qty == '' && rd.item_price != '' && parseAmount(rd.item_price) >= 0){
                                rd.item_qty = 1;
                                rd.item_amount = parseAmount(rd.item_price);  
                                grid.refreshDataAndView();     
                            }
                        })
                    }
                },
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.item_price != ''){
                        rd.item_price = parseAmount(rd.item_price);
                        return formatAmount(rd.item_price);   
                    }
                    return '';
                }
            },
            { title: "AMOUNT", sortable:false,width: 20, align: "right",dataIndx: "item_amount",dataType: "float",
                validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
                   var item_id = ui.rowData['item_id'];
                    if (item_id != '') {
                        return true;
                    }
                    return false;
                },
                editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        
                        $inp.on("change", function (evt) {
                            if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_amount != '' && parseAmount(rd.item_amount) >= 0){
                                var price = parseAmount(rd.item_amount) / rd.item_qty;
                                rd.item_price = price; 
                                grid.refreshDataAndView();        
                            }
                            if(rd.item_qty == '' && rd.item_amount != '' && parseAmount(rd.item_amount) >= 0){
                                rd.item_qty = 1;;
                                rd.item_price = parseAmount(rd.item_amount); 
                                grid.refreshDataAndView();        
                            }
                        });
                    }
                },
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.item_amount != ''){
                        rd.item_amount = parseAmount(rd.item_amount);
                        return formatAmount(rd.item_amount);   
                    }
                    return '';
                }
              },
            ];
            
        var dataModel = {"data": <?= json_encode($json_data) ?>}
        
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            change: calculateSummary,
            dataReady: calculateSummary,   
            columnTemplate: { render: commentRender },
            colModel: colModel,  
            numberCell: { show: true },
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
     



        
        var tax_dataModel = {"data":<?php echo json_encode($tax_json_data);?>}
         var tax_colModel = [
            { title: "TAX RATE", dataIndx: "tax_rate",editable: false, width: 100},
            { title: "AMT", width: 100, dataType: "float", dataIndx: "tax_amt"},
            { title: "IGST", width: 100,  dataIndx: "igst" ,dataType: "float",format: '##,###.00'},
            { title: "CGST", width: 100,  dataIndx: "cgst" ,dataType: "float",format: '##,###.00'},
            { title: "SGST", width: 100,  dataIndx: "sgst" ,dataType: "float",format: '##,###.00'}
            ];
         var tax_newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            dataModel: tax_dataModel,
             pageModel: { type: 'local' },
            colModel: tax_colModel,  
            numberCell: { show: true },
            editable: false,
            wrap:false,
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: false });
                }
           };
           
           
           
          $("#taxgrid_search").pqGrid(tax_newObj);
          
    var billsundry_autoComplete = function (ui) {
        var rd = ui.rowData;
        var $inp = ui.$cell.find("input");
        var element= {};

        $inp.autocomplete({
                source:  <?php echo $bsd_json_file;?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    rd.billsundry_name = ui.item.label;
                    rd.billsundry_id = ui.item.id;
                     
                    $(this).val(ui.item.label);
                 }
                
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.billsundry_name = '';
                rd.billsundry_id = '';
            }).focusout(function () {              
                if(rd.billsundry_id == '')
                {
                    rd.billsundry_name = '';
                    rd.billsundry_id = '';
                }
            });
           
        }
        
         var billsundry_dataModel = {"data":<?= json_encode($billsundry_json_data) ?>}
         var billsundry_colModel = [
             { title: "BILL SUNDRY", width: 100, dataType: "string", align: "left",dataIndx: "billsundry_name" ,
                editor: {                   
                  type: "textbox",
                  init: billsundry_autoComplete,
                  options: []
              },
                     
                      
             },
             { title: "RATE", width: 100,  align: "right",dataIndx: "billsundry_rate" ,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.billsundry_rate != ''){
                        rd.billsundry_rate = parseAmount(rd.billsundry_rate);
                        return formatAmount(rd.billsundry_rate);   
                    }
                    return '';
                }
             },
             { title: "AMOUNT", width: 100, align: "right", dataIndx: "billsundry_amount" ,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.billsundry_amount != ''){
                        rd.billsundry_amount = parseAmount(rd.billsundry_amount);
                        return formatAmount(rd.billsundry_amount);   
                    }
                    return '';
                }
             },
            ];
          
          var tax_newObj2 = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'row' },
            scrollModel: { autoFit: true },
            dataModel: billsundry_dataModel,
            colModel: billsundry_colModel,  
              pageModel: { type: 'local' },
            numberCell: { show: true },
            change: billsundry_calculateSummary, 
            editable: true,
            wrap:false,
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            cellSave: function(evt, ui){
                   this.refresh();
               },
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: false });
                }
           };
          
          $("#billsundry_search").pqGrid(tax_newObj2); 
          
        
     });
     
     $(document).on('change', '[name="transfer_type"]', function(){
         var type = $(this).val();
         if(type == 'type1'){
             window.location.href = "<?php echo $base_url ?>stock_transfer/add";
         }
         if(type == 'type2'){
             window.location.href = "<?php echo $base_url ?>stock_transfer/add2";
         }
         if(type == 'type3'){
             window.location.href = "<?php echo $base_url ?>stock_transfer/add3";
         }
     });
 </script>	
</body>
</html>
