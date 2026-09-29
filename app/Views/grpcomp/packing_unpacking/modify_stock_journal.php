<?php $header = array( 	'title' => 'Modify Stock Journal' ); ?>
<?php echo view('includes/header',$header); ?>

<div class="row mb-2">
<div class="col-md-4 col-6"><h3 class="pb-3">Modify Stock Journal</h3></div>
<div class="col-md-6 text-end order-md-2 order-3">
<div class="dropdown d-sm-flex d-block float-end">
  
  <button class="btn btn-success m-1" type="button">View</button>   
   
<div class="taskmenus">
    
    <a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history"><span class="material-symbols-outlined">visibility</span></a>
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
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>



<div class="col-md-2 col-6 text-end order-md-3 order-2">
    <div class="form-check form-check-inline me-2">
        <input class="form-check-input" type="checkbox" value="1" name="oCheck" id="oCheck" form="salefrm" <?= ($oCheck == 1) ? 'checked' : '' ?>>
        <label class="form-check-label" for="oCheck">Optional</label>
    </div>
    <a href="<?php echo history_back();?>" class="btn btn-outline-success">Back</a>
</div>

</div>

<div id="validation_errors"></div>
<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
    <div class="col-12">
        <div class="row m-0 p-0">
      <div class="col-md-3 col-6 card p-2"><label>Voucher Type:</label>
        <select name="voucher_type" class="form-control form-control-sm">
            <option  value="pack">Pack / Assemble</option>
            <option  value="unpack">Unpack / Unassemble</option>
            <option  value="stock_journal" selected>Stock Journal</option>    
        </select>
      </div>
      <div class="col-md-2 col-6 card p-2"><label>Date:</label> <input type="text" id="sale_date" name="sale_date" value="<?php echo date('d-m-Y', strtotime($get_cons_detail['voucher_date']));?>" class="datepicker form-control form-control-sm" readonly></div>  
      <div class="col-md-2 col-6 card p-2"><label>Series:</label>
      <?php	
        echo form_dropdown('voucher_series', $voucher_series_dropdown, $voucher_id,' id="voucher_series" class="selectwidget voucher_series form-control required" ');
		?></div>
      <div class="col-md-2 col-6 card p-2"><label>Voucher No:</label> <input type="text" name="voucher" class="form-control form-control-sm" value="<?php echo $get_cons_detail['comp_vch_no'];?>" disabled></div>
      
      <div class="col-md-3 col-6 card p-2"><label>Material Centre:</label><?php	
        echo form_dropdown('matrcntr_id', $matrcntr_dropdown, $get_cons_detail['mat_cent_id'],' id="matrcntr_id" class="selectwidget form-control required" ');
		?></div>
	
      <div class="col-md-12 col-12 card p-2"><label>Narration:</label>

 <textarea  name="narration" class="form-control"><?php if($get_narration_info){
	 echo $get_narration_info['vch_narr'];
 } ?></textarea>
 
	</div>
      <input type="hidden" name="itmsdatafrom" id="itmsdatafrom">
      <input type="hidden" name="itmsdatato" id="itmsdatato">
    </div></div>
   

   
   
    <br>
    <div class="col-12 "><div class="row">
       <!-- Tax Summary -->
       <div class="col-md-6">
           <h4 class="text-center">Journal Transferred From</h4>
           <div id="grid_search" style="margin:auto;"></div> 
         
  </div>
       <!-- Tax Summary Ends -->
      
      <!-- Bill Summary Starts -->
       <div class="col-md-6">
           <h4 class="text-center">Journal Transferred To</h4>
        <div id="grid_search2" style="margin:auto;"></div> 
  </div>

    </div> 
    
     <div class="col-12 text-center">
         <br><br>
         <!--<input type="file" class="">-->
         
        <button type="button"  class="btn btn-success btn-lg" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">Save</button>
        <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-success btn-lg">Quit</a>
        
    </div>
        
    </div>
   
</form>
<?php echo view('includes/footer_scripts');  ?>
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
  
  $(document).on('change', '#sale_date', function(e){

        var voucher_date = $("#sale_date").val();
      

        $.each(item_qty_balance_from, function(index, value){
              $.each(value, function(indexn, valuen){
             item_qty_balance_b_from[index]=indexn
              });
        });
        
    
        $.ajax({
            url: '<?php echo $base_url; ?>ajax/get_all_item_balances', 
            type: 'POST',
            data: {voucher_date: voucher_date, item_id_array: item_qty_balance_b_from},
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
                            if(item_qty_balance_from.hasOwnProperty(obj.item_id)) {
                                item_qty_balance_from[obj.item_id][obj.item_unit_id] = obj.balance;
                            }
                        });
                        update_grid_item_balances($("#grid_search"));
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
        
        
          $.each(item_qty_balance_to, function(index, value){
              $.each(value, function(indexn, valuen){
             item_qty_balance_b_to[index]=indexn
              });
        });
        
    
        $.ajax({
            url: '<?php echo $base_url; ?>ajax/get_all_item_balances', 
            type: 'POST',
            data: {voucher_date: voucher_date, item_id_array: item_qty_balance_b_to},
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
                            if(item_qty_balance_to.hasOwnProperty(obj.item_id)) {
                                item_qty_balance_to[obj.item_id][obj.item_unit_id] = obj.balance;
                            }
                        });
                        update_grid_item_balances($("#grid_search2"));
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
 var itemsobj = new Map();
 var itemsobj_to = new Map();
 var itemunirsobj = new Map();
 var itemunirsobj_to = new Map();
 var itemlist  = [];
</script>
<?php 
   if(isset($transactions['from'])){
     foreach($transactions['from'] as $tranrow_from){
          $json_data[] = array("AvailQty"=>$tranrow_from['AvailQty'],"pq_cellattr"=>array("item_name"=>array("title"=>$tranrow_from["pq_cellattr"]["item_name"]["title"])),"item_unit_id"=>$tranrow_from['item_unit_id'],"item_id"=>$tranrow_from['item_id'],"id"=>$tranrow_from['item_id'],"item_name"=>$tranrow_from['from_item_name'],'item_qty'=>$tranrow_from['from_item_qty'],'short_narator'=>'','item_unit'=>$tranrow_from['item_unit'],'item_price'=>$tranrow_from['from_item_price'],'item_amount'=>$tranrow_from['from_item_amount']);
    ?>
    <script>
       itemsobj.set('<?php echo $tranrow_from["from_item_name"];?>','<?php echo $tranrow_from["item_id"];?>'); 
        itemunirsobj.set('<?php echo $tranrow_from["from_item_name"];?>','<?php echo $tranrow_from["item_unit"];?>');
    </script>
    <?php
     }
 }


 if(isset($transactions['to'])){
     foreach($transactions['to'] as $tranrow_to){
          $json_to_data[] = array("to_item_unit_id"=>$tranrow_to['item_unit_id'],"to_item_id"=>$tranrow_to['item_id'],"id"=>$tranrow_to['item_id'],"to_item_name"=>$tranrow_to['to_item_name'],'to_item_qty'=>$tranrow_to['to_item_qty'],'to_short_narator'=>'','to_item_unit'=>$tranrow_to['item_unit'],'to_item_price'=>$tranrow_to['to_item_price'],'to_item_amount'=>$tranrow_to['to_item_amount']);
    ?>
    <script>
       itemsobj_to.set('<?php echo $tranrow_to["to_item_name"];?>','<?php echo $tranrow_to["item_id"];?>'); 
       itemunirsobj_to.set('<?php echo $tranrow_to["to_item_name"];?>','<?php echo $tranrow_to["item_unit"];?>');
    </script>
    <?php
     }
 }
 
 
 for($i=1;$i<=50;$i++){ 
  $json_data[] =array("item_unit_id"=>"","AvailQty"=>"0","AvailQtyS"=>"0","item_id"=>"","id"=>'',"item_name"=>'','item_qty'=>'','short_narator'=>'','item_unit'=>'','item_price'=>'','item_amount'=>'');
  $json_to_data[] =array("to_item_unit_id"=>"","to_item_id"=>"","id"=>'',"to_item_name"=>'','to_item_qty'=>'','to_short_narator'=>'','to_item_unit'=>'','to_item_price'=>'','to_item_amount'=>'');
 }

?>
<style>
    .boldcell{font-weight:700;}
</style>
<?php
$itemn_units_labels=array();
if($items_list['items_array']){
    foreach($items_list['items_array'] as $itmrr){
       $itemn_units_labels[trim($itmrr['label'])]= $itmrr['item_unit'];
    }
}

?>
<script>
    <?php if($itemn_units_labels!=''){ ?>
    var unitslables =<?php echo json_encode($itemn_units_labels);?>;
    var unitslables_to =<?php echo json_encode($itemn_units_labels);?>;
    <?php } else { ?>
     var unitslables = [];
     var unitslables_to = [];
    <?php } ?>
   $("#submitbtn").on("click",function(){
     var item_checked = [];
      var item_checked_to = [];
     var final_item_id =[];
     
     var grid1 = $("#grid_search").pqGrid('option', 'dataModel.data');
     var grid2 = $("#grid_search2").pqGrid('option', 'dataModel.data');
     
     var response_grid1 = grid_from_data(grid1,item_checked);
     var response_grid2 = grid_to_data(grid2,item_checked_to);
  
     var response_grid1_info = $.parseJSON(response_grid1);
     var final_item_amouint_grid1 = response_grid1_info['to_amount'];
     if(final_item_amouint_grid1=="0")
     var grid1_items  =[];
     else
     var grid1_items  = response_grid1_info['item_checked'];
     
     var response_grid2_info = $.parseJSON(response_grid2);
     var final_item_amouint_grid2 = response_grid2_info['from_amount'];
      if(final_item_amouint_grid2=="0")
     var grid2_items  =[];
     else
     var grid2_items              = response_grid2_info['item_checked'];
     
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
       else if(grid1_items.length==0 || grid2_items.length==0 || $("#voucher_series").val()=='' || final_item_amouint_grid2=='' || final_item_amouint_grid2=='0' || final_item_amouint_grid1=='' || final_item_amouint_grid1=='0'){
          alert_notification("Kindly fill the items data!!!");
          return false;   
       }
        else{
         $("#itmsdatafrom").val(JSON.stringify(grid1_items));
         $("#itmsdatato").val(JSON.stringify(grid2_items));
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
                    window.history.back();
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
           var item_price    = data[i]['item_price'];
           var item_qty      = data[i]['item_qty'];
           var item_unit     = data[i]['item_unit'];
           var item_amount   = data[i]['item_amount'];
           
           if(!item_amount)
             item_amount =0;
             final_item_amouint=parseInt(final_item_amouint)+parseInt(item_amount);
            
            if(item_name!=''){
           
		  item_checked.push({
		            "item_unit_id": item_unit_id,
		           "item_id": item_id,
                    "item_price": item_price,
                    "item_qty" :item_qty,
                    "item_unit" :item_unit,
                    "item_total_amount" :item_amount
                 });
             
            }
         }
    var final_response={item_checked:item_checked,from_amount:final_item_amouint};     
    return JSON.stringify(final_response);     
    
}
    function grid_to_data(data,item_checked_to){
     
     var final_item_amouint ="0";
       for (var i = 0; i < data.length; i++) {
          var item_unit_id     = data[i]['to_item_unit_id'];
           var item_id        = data[i]['to_item_id'];
           var item_name     = data[i]['to_item_name'];
           var item_price    = data[i]['to_item_price'];
           var item_qty      = data[i]['to_item_qty'];
           var item_unit     = data[i]['to_item_unit'];
           var item_amount   = data[i]['to_item_amount'];
           
           if(!item_amount)
             item_amount =0;
             final_item_amouint = parseInt(final_item_amouint)+parseInt(item_amount);
            
            if(item_name!=''){
        		  item_checked_to.push({
        		           "item_id": item_id,
                            "item_price": item_price,
                            "item_qty" :item_qty,
                            "item_unit" :item_unit,
                            "item_unit_id" :item_unit_id,
                            "item_total_amount" :item_amount
                            
                         });
               }
         }  
    var final_response={item_checked:item_checked_to,to_amount:final_item_amouint};     
    return JSON.stringify(final_response);       
    
}

     $(function () {
          
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
             
            itempriceTotal  += parseFloat(item_price);
            itemamountTotal += parseFloat(item_amount);
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
          
       function calculateSummary_to() {
        var itempriceTotal = 0,
            itemamountTotal = 0,
            itemqtyTotal=0,
            data_to = this.option('dataModel.data'),
            len  = data_to.length;

        data_to.forEach(function(row){
            if(row.to_item_price == '' || typeof row.to_item_price == 'undefined'){
                var item_price =0;
            }else
             var item_price =  row.to_item_price;
            
            if(row.to_item_qty == '' || typeof row.to_item_qty == 'undefined'){
                var item_qty =0;
            }else
            var  item_qty =  row.to_item_qty;
            
            if(row.to_item_amount == '' || typeof row.to_item_amount == 'undefined'){
                var item_amount =0;
            }else
             var item_amount =  row.to_item_amount;
             
            itempriceTotal  += parseAmount(item_price);
            itemamountTotal += parseAmount(item_amount);
            itemqtyTotal    += parseInt(item_qty);
        })

        var totalData_to = {
                item_name: "Total",
                to_item_qty  : itemqtyTotal,
                to_item_price: itempriceTotal,
                to_item_amount: itemamountTotal,
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            this.option('summaryData', [totalData_to]);
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
        var element= {};
         var rd = ui.rowData;
		$inp.autocomplete({
                source:  <?php echo $item_json_file;?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
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
					//console.log("unit selected---");			   
					get_item_balances(rd.item_id,rd.item_unit_id,rd);
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
                source:  <?php echo $item_json_file;?>,
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
                        
                if(rdto.item_id == '')
                {
                    rdto.item_id = '';
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
                    rdto.to_item_unit_id =ui.item.id;
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
                        rdto.to_item_unit = '';
                    }
                }
            });
          }
        var colModel = [
                     { title: "ITEM NAME", dataIndx: "item_name", align: "left",width: 100,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox",
                          init: autoCompleteEditor,
                          options: itemlist
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
              
            { title: "PRICE", width: 20, align: "right",dataIndx: "item_price",dataType: "float",
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
            { title: "AMOUNT", width: 20, align: "right",dataIndx: "item_amount",dataType: "float",
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
	 	    
        var dataModel = {"data":<?php echo json_encode($json_data);?>}
        
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel: colModel,  
            numberCell: { show: true },
			columnTemplate: { render: commentRender },
            editable: true,
            cellSave: function(evt, ui){
                   this.refresh();
               },
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            showTitle: true,
            wrap:false,
            create: function (evt, ui) {// make first row auto selected
			 this.widget().pqTooltip();
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: true });
              }
        };
        
        
        
        var colModel2 = [
                     { title: "ITEM NAME", dataIndx: "to_item_name", align: "left",width: 100,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox",
                          init: autoCompleteEditor_to,
                          options: itemlist
                      },
                      render: function( ui ) {
                    var rdto = ui.rowData;
                    var grid = this;
                    var item_qty_balance = '0';
                    if(typeof rdto.to_item_id !== "undefined" && rdto.to_item_id != '' && rdto.to_item_name != '')
                    {      var item_qty_balance = rdto.item_balance_to;
                    
                        if(typeof item_qty_balance !=="undefined"){
                     grid.addClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'pq-cell-red-tr pq-has-tooltip' });
                    return '<span data-title-tooltip="  '+item_qty_balance+' ">'+ui.cellData+'</span>';
                    }
                    else 
                    return ui.cellData;
                    }
                },
            },
          { title: "QTY",dataIndx: "to_item_qty", width: 20, dataType: "float",
                 
                validations: [{ type: 'gt', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
                   var item_id = ui.rowData['to_item_id'];
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
                            if(rd.to_item_qty != '' && rd.to_item_qty > 0 && rd.to_item_price != '' && parseAmount(rd.to_item_price) >= 0){
                                var amount = rd.to_item_qty * parseAmount(rd.to_item_price);
                                rd.to_item_amount = amount;  
                                grid.refreshDataAndView();     
                            } 
                        })
                    }
                },
                render: function (ui) {
                    var rd = ui.rowData;
                    var cellData=render_qty(ui.cellData); 
                    rd.to_item_qty=cellData;
                    
                    return cellData;
                },
              },
          { title: "UOM", dataIndx: "to_item_unit", width: 20,editable: function (ui) {
               var item_id = ui.rowData['to_item_id'];
                if (item_id != '') {
                    return true;
                }
                return false;
            },cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox",
                          init:autoCompleteEditor3,
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
              
            { title: "PRICE", width: 20, align: "right",dataIndx: "to_item_price",dataType: "float",
                validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
                   var item_id = ui.rowData['to_item_id'];
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
                            if(rd.to_item_qty != '' && rd.to_item_qty > 0 && rd.to_item_price != '' && parseAmount(rd.to_item_price) >= 0){
                                var amount = rd.to_item_qty * parseAmount(rd.to_item_price);
                                rd.to_item_amount = amount;  
                                grid.refreshDataAndView(); 

                            }
                            if(rd.to_item_qty == '' && rd.to_item_price != '' && parseAmount(rd.to_item_price) >= 0){
                                rd.to_item_qty = 1;
                                rd.to_item_amount = parseAmount(rd.to_item_price);  
                                grid.refreshDataAndView();     
                            }
                        })
                    }
                },
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.to_item_price != ''){
                        rd.to_item_price = parseAmount(rd.to_item_price);
                        return formatAmount(rd.to_item_price);   
                    }
                    return '';
                }
            },
            { title: "AMOUNT", width: 20, align: "right",dataIndx: "to_item_amount",dataType: "float",
                validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
                   var item_id = ui.rowData['to_item_id'];
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
                            if(rd.to_item_qty != '' && rd.to_item_qty > 0 && rd.to_item_amount != '' && parseAmount(rd.to_item_amount) >= 0){
                                var price = parseAmount(rd.to_item_amount) / rd.to_item_qty;
                                rd.to_item_price = price; 
                                grid.refreshDataAndView();        
                            }
                            if(rd.to_item_qty == '' && rd.to_item_amount != '' && parseAmount(rd.to_item_amount) >= 0){
                                rd.to_item_qty = 1;;
                                rd.to_item_price = parseAmount(rd.to_item_amount); 
                                grid.refreshDataAndView();        
                            }
                        });
                    }
                },
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.to_item_amount != ''){
                        rd.to_item_amount = parseAmount(rd.to_item_amount);
                        return formatAmount(rd.to_item_amount);   
                    }
                    return '';
                }
              },
	 	    ];
	 	var dataModel2 = {"data":<?php echo json_encode($json_to_data);?>}    
        var newObj2 = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel2,
            colModel: colModel2,
            change: calculateSummary_to,
            dataReady: calculateSummary_to,
            numberCell: { show: true },
            editable: true,
            cellSave: function(evt, ui){
                   this.refresh();
               },
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            showTitle: true,
            wrap:false,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: false });
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
        newObj2.cellKeyDown = function(evt, ui) {
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
        var $grid2 = $("#grid_search2").pqGrid(newObj2);
     
});

$(document).on('change','[name="voucher_type"]', function(e){
    var type = $(this).val();
     if(type == 'pack'){
         window.location.href = "<?php echo $base_url ?>packing_unpacking/pack";
     }
     else if(type == 'unpack'){
         window.location.href = "<?php echo $base_url ?>packing_unpacking/unpack";
     }
      else if(type == 'stock_journal'){
         window.location.href = "<?php echo $base_url ?>packing_unpacking/stock_journal";
     }
     else{
         e.preventDefault();
     }
})
 </script>	
</body>
</html>
