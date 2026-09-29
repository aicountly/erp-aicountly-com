<?php $header = array( 	'title' => 'Modify Journal Voucher' ); ?>
<?php echo view('includes/header',$header); ?>

<div class="row mb-2">
    <div class="col-md-6 order-1"><h3><?php echo $voucher_name;?>  Voucher</h3></div>
	
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
    <select name="type" id="type" class="form-select d-inline-block" style="width:160px;">
        <option value="with_item" selected="selected">With Item</option>
        <option value="without_item">Without Item</option>
    </select>
    <div class="form-check form-check-inline">
<input class="form-check-input" type="checkbox" value="1" id="detailedCheck">
    <label class="form-check-label" for="detailedCheck">Detailed</label>
   </div><div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" value="1" id="ccCheck">
    <label class="form-check-label" for="ccCheck">Cost Centre</label>
    </div>
     </div>  
  <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
    <button class="btn btn-sm btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Action</a></li>
        <li><a class="dropdown-item" href="#">Another action</a></li>
        <li><a class="dropdown-item" href="#">Something else here</a></li>
    </ul>
        <a href="<?php echo history_back();?>" class="btn btn-outline-success showinline-md">Back</a>
</div> </div> 



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
<form class="form" method="post" id="salefrm" autocomplete="off"  novalidate>
    <div class="col-12">
        <div class="row m-0 p-0">
  
     <div class="col-md-2 col-6 card p-2">
	    <div class="input-group">
                <label class="input-group-text">Date:</label>
               <input type="text" name="sale_date" value="<?php echo $get_cons_detail['voucher_date'];?>" class="datepicker form-control form-control-sm" readonly>
            </div>	  
	 </div>  
      <div class="col-md-2 col-6 card p-2">
	  <div class="input-group">
                <label class="input-group-text">Series:</label>
              <?php	
				echo form_dropdown('voucher_series', $voucher_series_dropdown, $sel_voucher_id,' id="voucher_series" class="selectwidget voucher_series form-control required" ');
				?>
            </div>
		</div>
      <div class="col-md-2 col-6 card p-2">
	   <div class="input-group">
                <label class="input-group-text">Voucher No:</label>
               <input type="text" name="voucher" class="form-control form-control-sm" value="<?php echo $voucher_no;?>" disabled>
            </div>
	 </div>
      
    <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">GST Nature:</label>
                <select class="form-control">
                    <option>Choose</option>
                </select>
            </div>
        </div>
	 <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
            <label class="input-group-text">MC:</label> 
            <?php echo form_dropdown('matrcntr_id', $matrcntr_dropdown, $get_cons_detail['mat_cent_id'],' id="matrcntr_id" class="form-select required" '); ?>
        </div>
    </div>	
      <div class="col-md-12 col-12 card p-2"><label>Narration:</label><textarea  name="long_nareration" class="form-control form-control-sm"><?php if($get_narration_info){ echo $get_narration_info['vch_narr'];} ?></textarea></div>
      <input type="hidden" name="itmsdatafrom" id="itmsdatafrom">
      <input type="hidden" name="itmsdatato" id="itmsdatato">
	  
	  <input type="hidden" name="bsditmsdatafrom" id="bsditmsdatafrom">
      <input type="hidden" name="bsditmsdatato" id="bsditmsdatato">
	  
    </div></div>
   

   
   
    <br>
    <div class="col-12"><div class="row">
       <!-- Tax Summary -->
       <div class="col-md-6">
           <h4 class="text-center">Inward Items</h4>
           <div id="grid_search" style="margin:auto;"></div> 
		   <br>
		   <h4 class="text-center">Accounts/Billsundry To Be Debited</h4>
           <div id="billsundry_search_from" style="margin:auto;"></div> 
		   <br>
		   <h4 class="text-center" id="total_left_side">&nbsp;</h4>
         
  </div>
       <!-- Tax Summary Ends -->
      
      <!-- Bill Summary Starts -->
       <div class="col-md-6">
           <h4 class="text-center">Outward Items</h4>
        <div id="grid_search2" style="margin:auto;"></div> 
		<br>
		   <h4 class="text-center"> Accounts/Billsundry To Be Credited</h4>
           <div id="billsundry_search_to" style="margin:auto;"></div> 
		    <br>
		   <h4 class="text-center" id="total_right_side">&nbsp;</h4>
  </div>

    </div> 
   
   

	
     <div class="col-12 text-center">
	    <br><br>
	
         <br><br>
         <!--<input type="file" class="">-->
         
        <button type="button" id="submitbtn" class="btn btn-success btn-lg" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">Save</button>
        <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-success btn-lg">Quit</a>
        
    </div>
        
    </div>
   
</form>
<?php echo view('includes/footer_scripts');  
$json_data = $inward_items;
$json_to_data = $outward_items;
 for($i=1;$i<=50;$i++){ 
  $json_data[] =array("item_unit_id"=>"","item_id"=>"","id"=>'',"item_name"=>'','item_qty'=>'','short_narator'=>'','item_unit'=>'','item_price'=>'','item_amount'=>'');
  $json_to_data[] =array("to_item_unit_id"=>"","to_item_id"=>"","id"=>'',"to_item_name"=>'','to_item_qty'=>'','to_short_narator'=>'','to_item_unit'=>'','to_item_price'=>'','to_item_amount'=>'');
 }
 
 $sundry_grid_data = $purchase_acc_list;
 $sundry_grid_to_data = $sale_acc_list;
 for($i=1;$i<=10;$i++){ 
  $sundry_grid_data[] =array("acc_id"=>"","acc_name"=>"","acc_type"=>'',"acc_amount"=>'');
  $sundry_grid_to_data[] =array("acc_id_to"=>"","acc_name_to"=>"","acc_type_to"=>'',"acc_amount_to"=>'');
 }
?>
<script src="https://malsup.github.io/jquery.form.js"></script> 
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
var unitslist = <?php echo json_encode($units_list);?>; 
var intRegex = /^\d+$/;
var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
var itemsobj = new Map();
var itemsobj_to = new Map();
 var itemunirsobj = new Map();
  var itemunirsobj_to = new Map();
    var itemlist  = [];
	
	function grid_side_balances(){
		var item_checked = [];
     var item_checked_to = [];
     var final_item_id =[];
	 
	  var bsdfrom = [];
     var bsd_to = [];
	 
     
     var purchase_sum=0;
	 var sale_sum=0;
     var grid1 = $("#grid_search").pqGrid('option', 'dataModel.data');
     var grid2 = $("#grid_search2").pqGrid('option', 'dataModel.data');
     
     var response_grid1 = grid_from_data(grid1,item_checked);
     var response_grid2 = grid_to_data(grid2,item_checked_to);
	 
	//console.log(grid1);
	//  console.log(grid2);
	// return false;
	 
	 var bsdgrid1 = $("#billsundry_search_from").pqGrid('option', 'dataModel.data');
     var bsdgrid2 = $("#billsundry_search_to").pqGrid('option', 'dataModel.data');
	 
	 var response_bsdgrid1 = bsdgrid_from_data(bsdgrid1,bsdfrom);
     var response_bsdgrid2 = bsdgrid_to_data(bsdgrid2,bsd_to);
	 
	 // console.log(response_bsdgrid1);
	  //  console.log(response_bsdgrid2);
		
		
     var response_grid1_info = $.parseJSON(response_grid1);
	 
     var final_item_amouint_grid1 = response_grid1_info['from_amount'];
     if(final_item_amouint_grid1=="0"){
	  purchase_sum +=0;	 
     var grid1_items  =[];
	 
	 }
     else{
     var grid1_items  = response_grid1_info['item_checked'];
	 purchase_sum += parseAmount(response_grid1_info['from_amount']);
	 }
     
     var response_grid2_info = $.parseJSON(response_grid2);
     var final_item_amouint_grid2 = response_grid2_info['to_amount'];
      if(final_item_amouint_grid2=="0"){
		 sale_sum +=0;
     var grid2_items  =[];
	  }
     else{
     var grid2_items              = response_grid2_info['item_checked'];
	 sale_sum += parseAmount(response_grid2_info['to_amount']);
     }
	 
	 var response_bsdgrid1_info = $.parseJSON(response_bsdgrid1);
     var final_item_amouint_bsdgrid1 = response_bsdgrid1_info['from_amount'];
     if(final_item_amouint_bsdgrid1=="0"){
		 purchase_sum +=0;
     var bsdgrid1_items  =[];
	 }
     else{
     var bsdgrid1_items  = response_bsdgrid1_info['item_checked'];
	  purchase_sum += parseAmount(response_bsdgrid1_info['from_amount']);
	 }
	var response_bsdgrid2_info = $.parseJSON(response_bsdgrid2);
     var final_item_amouint_bsdgrid2 = response_bsdgrid2_info['to_amount'];
     if(final_item_amouint_bsdgrid2=="0"){
		 sale_sum +=0;
     var bsdgrid2_items  =[];
	 }
     else{
     var bsdgrid2_items  = response_bsdgrid2_info['item_checked']; 
	  sale_sum += parseAmount(response_bsdgrid2_info['to_amount']);
	 }
	  
		
		
		$("#total_right_side").html(sale_sum);
		$("#total_left_side").html(purchase_sum);
	}
	
   
    <?php if($itemn_units_labels!=''){ ?>
    var unitslables =<?php echo json_encode($itemn_units_labels);?>;
    var unitslables_to =<?php echo json_encode($itemn_units_labels);?>;
    <?php } else { ?>
     var unitslables = [];
     var unitslables_to = [];
    <?php } ?>
	
	
	function get_sundry_grid()
    {
        var data = []
        for(var i=1; i<=10; i++){
            data.push({"acc_id": '',"acc_name": '','acc_type': '','acc_amount': ''});
        }
        return data;
    } 
	
	function get_sundry_grid_to()
    {
        var data = []
        for(var i=1; i<=10; i++){
            data.push({"acc_id_to": '',"acc_name_to": '','acc_type_to': '','acc_amount_to': ''});
        }
        return data;
    }
	
	

	
	/* $(function() {


    $('#salefrm').ajaxForm({
        beforeSend: function() {
   
        },        
        complete: function(xhr) {
			console.log(">>>>>");
			percent.html(100);
		    status.html(xhr.responseText);
        }
    });
});  */

   $("#submitbtn").on("click",function(){
     var item_checked = [];
     var item_checked_to = [];
     var final_item_id =[];
	 
	  var bsdfrom = [];
     var bsd_to = [];
	 
     
     var purchase_sum=0;
	 var sale_sum=0;
     var grid1 = $("#grid_search").pqGrid('option', 'dataModel.data');
     var grid2 = $("#grid_search2").pqGrid('option', 'dataModel.data');
     
     var response_grid1 = grid_from_data(grid1,item_checked);
     var response_grid2 = grid_to_data(grid2,item_checked_to);
	 
	//console.log(grid1);
	//  console.log(grid2);
	// return false;
	 
	 var bsdgrid1 = $("#billsundry_search_from").pqGrid('option', 'dataModel.data');
     var bsdgrid2 = $("#billsundry_search_to").pqGrid('option', 'dataModel.data');
	 
	 var response_bsdgrid1 = bsdgrid_from_data(bsdgrid1,bsdfrom);
     var response_bsdgrid2 = bsdgrid_to_data(bsdgrid2,bsd_to);
	 
	 // console.log(response_bsdgrid1);
	  //  console.log(response_bsdgrid2);
		
		
     var response_grid1_info = $.parseJSON(response_grid1);
	 
     var final_item_amouint_grid1 = response_grid1_info['from_amount'];
     if(final_item_amouint_grid1=="0"){
	  purchase_sum +=0;	 
     var grid1_items  =[];
	 
	 }
     else{
     var grid1_items  = response_grid1_info['item_checked'];
	 purchase_sum += parseAmount(response_grid1_info['from_amount']);
	 }
     
     var response_grid2_info = $.parseJSON(response_grid2);
     var final_item_amouint_grid2 = response_grid2_info['to_amount'];
      if(final_item_amouint_grid2=="0"){
		 sale_sum +=0;
     var grid2_items  =[];
	  }
     else{
     var grid2_items              = response_grid2_info['item_checked'];
	 sale_sum += parseAmount(response_grid2_info['to_amount']);
     }
	 
	 var response_bsdgrid1_info = $.parseJSON(response_bsdgrid1);
     var final_item_amouint_bsdgrid1 = response_bsdgrid1_info['from_amount'];
     if(final_item_amouint_bsdgrid1=="0"){
		 purchase_sum +=0;
     var bsdgrid1_items  =[];
	 }
     else{
     var bsdgrid1_items  = response_bsdgrid1_info['item_checked'];
	  purchase_sum += parseAmount(response_bsdgrid1_info['from_amount']);
	 }
	var response_bsdgrid2_info = $.parseJSON(response_bsdgrid2);
     var final_item_amouint_bsdgrid2 = response_bsdgrid2_info['to_amount'];
     if(final_item_amouint_bsdgrid2=="0"){
		 sale_sum +=0;
     var bsdgrid2_items  =[];
	 }
     else{
     var bsdgrid2_items  = response_bsdgrid2_info['item_checked']; 
	  sale_sum += parseAmount(response_bsdgrid2_info['to_amount']);
	 }
	  
	 //  console.log("purchase sum =>"+purchase_sum);
	 //  console.log("sale sum =>"+sale_sum);
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
      
       else if(grid1_items.length==0 ||  $("#voucher_series").val()=='' ||  final_item_amouint_grid1=='' || final_item_amouint_grid1=='0'){
          alert_notification("Kindly fill the items data!!!");
          return false;   
       }
	   else if(parseAmount(purchase_sum)!=parseAmount(sale_sum)){
        alert_notification("voucher totals wrong!!!");
        return false;    
    } 
       else{
         $("#itmsdatafrom").val(JSON.stringify(grid1_items));
         $("#itmsdatato").val(JSON.stringify(grid2_items));
		 
		 $("#bsditmsdatafrom").val(JSON.stringify(bsdgrid1_items));
         $("#bsditmsdatato").val(JSON.stringify(bsdgrid2_items));
		 
		 
      show_loader();
	  //  $('.pq-grid').pqGrid( "showLoading" );
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
             final_item_amouint=parseFloat(final_item_amouint)+parseFloat(item_amount);
            
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
function bsdgrid_from_data(billsundry_data,billsundryfrm_item_checked){
     
     var final_item_amouint ="0";
       for (var i = 0; i < billsundry_data.length; i++) {
           var acc_id          = billsundry_data[i]['acc_id'];
			var acc_name       = billsundry_data[i]['acc_name'];
			var acc_type       = billsundry_data[i]['acc_type'];
			var acc_amount     = billsundry_data[i]['acc_amount'];
			
			 if(!acc_amount)
             acc_amount =0;
             final_item_amouint=parseFloat(final_item_amouint)+parseFloat(acc_amount);
			 
			if(acc_id){
				billsundryfrm_item_checked.push({
						"acc_id": acc_id,
						"acc_name": acc_name,
						"acc_type": acc_type,
						"acc_amount" :acc_amount,
					 });
			}
         }
    var final_response={item_checked:billsundryfrm_item_checked,from_amount:final_item_amouint};  
    
 //console.log("=---->"+final_response);
    return JSON.stringify(final_response);     
    
}
function bsdgrid_to_data(billsundry_data,billsundryto_item_checked){
     
     var final_item_amouint_to ="0";
       for (var j = 0; j < billsundry_data.length; j++) {
           var acc_id          = billsundry_data[j]['acc_id_to'];
			var acc_name       = billsundry_data[j]['acc_name_to'];
			var acc_type       = billsundry_data[j]['acc_type_to'];
			var acc_amount     = billsundry_data[j]['acc_amount_to'];
			
			if(!acc_amount)
             acc_amount =0;
             final_item_amouint_to=parseFloat(final_item_amouint_to)+parseFloat(acc_amount);
			 
			if(acc_id){
				billsundryto_item_checked.push({
						"acc_id": acc_id,
						"acc_name": acc_name,
						"acc_type": acc_type,
						"acc_amount" :acc_amount,
					 });
			}
         }
    var final_response={item_checked:billsundryto_item_checked,to_amount:final_item_amouint_to};  
    
 
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
             final_item_amouint = parseFloat(final_item_amouint)+parseFloat(item_amount);
            
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
		
       function billsundry_calculateSummary_frm() {
        var itempriceTotal = 0,
            itemamountTotal = 0,
            itemqtyTotal=0,
            data = this.option('dataModel.data'),
            len  = data.length;

        data.forEach(function(row){
           
            if(row.acc_amount == '' || typeof row.acc_amount == 'undefined'){
                var acc_amount =0;
            }else
             var acc_amount =  row.acc_amount;
             
           
            itemamountTotal += parseFloat(acc_amount);
           
        })

        var totalData = {
                acc_name: "Total",
                acc_type  : "",
                acc_amount: itemamountTotal,
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            this.option('summaryData', [totalData]);
			grid_side_balances();
          }   
   
   function billsundry_calculateSummary_frmready() {
        var itempriceTotal = 0,
            itemamountTotal = 0,
            itemqtyTotal=0,
            data = this.option('dataModel.data'),
            len  = data.length;

        data.forEach(function(row){
           
            if(row.acc_amount == '' || typeof row.acc_amount == 'undefined'){
                var acc_amount =0;
            }else
             var acc_amount =  row.acc_amount;
             
           
            itemamountTotal += parseFloat(acc_amount);
           
        })

        var totalData = {
                acc_name: "Total",
                acc_type  : "",
                acc_amount: itemamountTotal,
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            this.option('summaryData', [totalData]);			
          }
		  
   function billsundry_calculateSummary_to() {
        var itemamountTotal_to = 0,           
            data = this.option('dataModel.data'),
            len  = data.length;

        data.forEach(function(row){
           
            if(row.acc_amount_to == '' || typeof row.acc_amount_to == 'undefined'){
                var acc_amount =0;
            }else
             var acc_amount =  row.acc_amount_to;
             
           
            itemamountTotal_to += parseFloat(acc_amount);
           
        })

        var totalData = {
                acc_name_to: "Total",
                acc_type_to  : "",
                acc_amount_to: itemamountTotal_to,
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            this.option('summaryData', [totalData]);
			grid_side_balances();
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
             
            itempriceTotal  += parseFloat(item_price);
            itemamountTotal += parseFloat(item_amount);
            itemqtyTotal    += parseInt(item_qty);
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
			grid_side_balances();
          }
      
function calculateSummaryReady() {
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
            itemqtyTotal    += parseInt(item_qty);
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
             
            itempriceTotal  += parseFloat(item_price);
            itemamountTotal += parseFloat(item_amount);
            itemqtyTotal    += parseInt(item_qty);
        })

        var totalData_to = {
                to_item_name: "Total",
                to_item_qty  : itemqtyTotal,
                to_item_price: itempriceTotal,
                to_item_amount: itemamountTotal,
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            this.option('summaryData', [totalData_to]);
			grid_side_balances();
          }
      
function calculateSummary_toReady() {
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
             
            itempriceTotal  += parseFloat(item_price);
            itemamountTotal += parseFloat(item_amount);
            itemqtyTotal    += parseInt(item_qty);
        })

        var totalData_to = {
                to_item_name: "Total",
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
                source:  "<?php echo base_url();?>/admin/ajax/get_items",
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 3,
                select: function(event, ui) {
                    console.log(ui.item);
					event.preventDefault();
				   
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
                    event.preventDefault();
                    $(this).val(ui.item.label);
                    rd.item_unit = ui.item.label;
                    rd.item_unit_id =ui.item.value;
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.item_unit = '';
                rd.item_unit_id = '';
            }).focusout(function () {              
                if(rd.item_unit_id == '')
                {
                    rd.item_unit = '';
                    rd.item_unit_id = '';
                }
            });
          }
         var autoCompleteEditor_to = function (ui) {
        var $inp = ui.$cell.find("input");
        var element= {};
		var rdto = ui.rowData;
		$inp.autocomplete({
                source:  "<?php echo base_url();?>/admin/ajax/get_items",
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 3,
                select: function(event, ui) {
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
                if(rdto.to_item_id == '')
                {
                    rdto.to_item_id = '';
                    rdto.to_item_name = '';
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
                if(rdto.to_item_unit_id == '')
                {
                    rdto.to_item_unit = '';
                    rdto.to_item_unit_id = '';
                }
            });
          }
  
        var colModel = [
                     { title: "ITEM NAME", dataIndx: "item_name", align: "left",width: 100,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox",
                          init: autoCompleteEditor,
                          options: itemlist
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
          { title: "QTY", width: 20, dataType: "integer", dataIndx: "item_qty"},
          { title: "UOM", dataIndx: "item_unit", width: 20,cls: 'pq-drop-icon pq-side-icon',editor: {                   
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
              
            { title: "PRICE", width: 20, align: "right",dataIndx: "item_price",dataType: "float",format: '##,###.00'},
            { title: "AMOUNT", width: 20, align: "right",dataIndx: "item_amount",dataType: "float",format: '##,###.00',
		       formula: function (ui) {
                    var rd = ui.rowData;
                    if(rd.item_price >0){
                        var calamount = (rd.item_qty * rd.item_price);
                        
                            return calamount; 
                             
                         }
                }
		      },
	 	    ];
	 	    
        var dataModel = {"data":<?php echo json_encode($json_data);?>}
        
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 450,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel: colModel,  
            numberCell: { show: true },
            editable: true,
			showSummary:true,
			change:calculateSummary,
			dataReady: calculateSummaryReady, 
            cellSave: function(evt, ui){
                   this.refresh();
               },
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
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
          { title: "QTY", width: 20, dataType: "integer", dataIndx: "to_item_qty"},
          { title: "UOM", dataIndx: "to_item_unit", width: 20,cls: 'pq-drop-icon pq-side-icon',editor: {                   
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
              
            { title: "PRICE", width: 20, align: "right",dataIndx: "to_item_price",dataType: "float",format: '##,###.00'},
            { title: "AMOUNT", width: 20, align: "right",dataIndx: "to_item_amount",dataType: "float",format: '##,###.00',
		       formula: function (ui) {
                    var rd = ui.rowData;
                    if(rd.to_item_price >0){
                        var calamount = (rd.to_item_qty * rd.to_item_price);
                            return calamount; 
                         }
                }
		      },
	 	    ];
	 	var dataModel2 = {"data":<?php echo json_encode($json_to_data);?>}    
        var newObj2 = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 450,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel2,
            colModel: colModel2,  
            numberCell: { show: true },
            editable: true,
			change: calculateSummary_to,
			dataReady: calculateSummary_toReady, 
			showSummary:true,
            cellSave: function(evt, ui){
                   this.refresh();
               },
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: true });
              }
        };
        
        var $grid = $("#grid_search").pqGrid(newObj);
        var $grid2 = $("#grid_search2").pqGrid(newObj2);
		$("#grid_search").pqGrid( {wrap:false} );
		$("#grid_search2").pqGrid( {wrap:false} );
		 var billsundry_autoComplete1 = function (ui) {
        var rd = ui.rowData;
        var $inp = ui.$cell.find("input");
        var element= {};

        $inp.autocomplete({
                source:  "<?php echo base_url();?>/admin/ajax/get_acc_bsd_accounts",
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 3,
                select: function(event, ui) {
                    event.preventDefault();
                    rd.acc_name = ui.item.label;
                    rd.acc_id = ui.item.id;
					if(ui.item.is_sundry==1)					
                    rd.acc_type = 'bsd';
				   else if(ui.item.is_acc==1)					
                    rd.acc_type = 'acc';
				
                    $(this).val(ui.item.label);
                 }
                
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.acc_name = '';
                rd.acc_id = '';
            }).focusout(function () {              
                if(rd.acc_id == '')
                {
                    rd.acc_name = '';
                    rd.acc_id = '';
                }
            });
           
        }
        
         var billsundry_dataModel = {"data":<?php echo json_encode($sundry_grid_data);?>}
         var billsundry_colModel = [
             { title: "PARTICULARS", width: 100, dataType: "string", align: "left",dataIndx: "acc_name" ,
                editor: {                   
                  type: "textbox",
                  init: billsundry_autoComplete1,
                  options: []
              },
                     
                      
             },
             { title: "AMOUNT", width: 20, align: "right",dataIndx: "acc_amount",dataType: "float",format: '##,###.00',
                  validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
             },
            ];
          
          var bsdfrm_newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
           height: 400,
            selectionModel: { type: 'row' },
            scrollModel: { autoFit: true },
            dataModel: billsundry_dataModel,
            colModel: billsundry_colModel,  
              pageModel: { type: 'local' },
            numberCell: { show: true },
            change: billsundry_calculateSummary_frm, 
			dataReady: billsundry_calculateSummary_frmready, 
			editable: true,
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
                    grid.setSelection({ rowIndx: 0, focus: true });
                }
           };
          
          $("#billsundry_search_from").pqGrid(bsdfrm_newObj);
		  $("#billsundry_search_from").pqGrid( {wrap:false} );
		  
		  
		   var billsundry_autoComplete2 = function (ui) {
        var rd = ui.rowData;
        var $inp = ui.$cell.find("input");
        var element= {};

        $inp.autocomplete({
                source:  "<?php echo base_url();?>/admin/ajax/get_acc_bsd_accounts",
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 3,
                select: function(event, ui) {
                    event.preventDefault();
                    rd.acc_name_to = ui.item.label;
                    rd.acc_id_to = ui.item.id;
                    if(ui.item.is_sundry==1)					
                    rd.acc_type_to = 'bsd';
				   else if(ui.item.is_acc==1)					
                    rd.acc_type_to = 'acc'; 
				
                    $(this).val(ui.item.label);
                 }
                
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.acc_name_to = '';
                rd.acc_id_to = '';
            }).focusout(function () {              
                if(rd.acc_id_to == '')
                {
                    rd.acc_name_to = '';
                    rd.acc_id_to = '';
                }
            });
           
        }
        
         var billsundry_dataModelTo = {"data":<?php echo json_encode($sundry_grid_to_data);?>}
         var billsundry_colModelTo = [
             { title: "PARTICULARS", width: 100, dataType: "string", align: "left",dataIndx: "acc_name_to" ,
                editor: {                   
                  type: "textbox",
                  init: billsundry_autoComplete2,
                  options: []
                  },
              },
              { title: "AMOUNT", width: 20, align: "right",dataIndx: "acc_amount_to",dataType: "float",format: '##,###.00',
                  validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
             },
            ];
          
          var bsdto_newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
           height: 400,
            selectionModel: { type: 'row' },
            scrollModel: { autoFit: true },
            dataModel: billsundry_dataModelTo,
            colModel: billsundry_colModelTo,  
              pageModel: { type: 'local' },
            numberCell: { show: true },
            change: billsundry_calculateSummary_to,
			dataReady: billsundry_calculateSummary_to, 
			showSummary: [true, true],
            editable: true,
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
                    grid.setSelection({ rowIndx: 0, focus: true });
                }
           };
          
          $("#billsundry_search_to").pqGrid(bsdto_newObj);
		   $("#billsundry_search_to").pqGrid( {wrap:false} );
     
});

$(document).on('change', '[name="type"]', function(e){
         var type = $(this).val();
         e.preventDefault();
         if(type == 'with_item'){
             window.location.href = "<?php echo $base_url; ?>/vouchers/add_journal_item";
         }
         else if(type == 'without_item'){
             $(this).val('with_item');
             window.location.href = "<?php echo $base_url; ?>/vouchers/add_journal";
         }
        
     })
 </script>	
</body>
</html>
