<?php $header = array( 	'title' => 'Physical Verification' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>

<div class="row mb-2">
<div class="col-md-6 col-6"><h3 class="pb-3">Physical Verification</h3></div>
<div class="col-md-6 text-end">
<div class="dropdown d-sm-flex d-block float-end">

   
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


<div class="col-md-12 text-end">
        <div class="form-check form-check-inline me-2">
            <input class="form-check-input" type="checkbox" value="1" name="oCheck" id="oCheck" form="salefrm">
            <label class="form-check-label" for="oCheck">Optional</label>
        </div>

      <button class="btn btn-success m-1" type="button">View</button> 
      <a href="<?php echo history_back();?>" class="btn btn-outline-success">Back</a>
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


<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
    <div class="col-12">
        <div class="row m-0 p-0">
      <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Series:</label>
      <?php	
        echo form_dropdown('voucher_series', $voucher_series_dropdown, '10',' id="voucher_series" class="selectwidget voucher_series form-control required" ');
		?></div></div>
      <div class="col-md-3 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Date:<span id="cleardate"></span></label> <input type="text" id="voucher_date" name="voucher_date" value="<?php echo $voucher_date;?>" class="datepicker form-control form-control-sm" readonly></div></div>  
      <div class="col-md-3 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Voucher No:</label> <input type="text" name="voucher" class="form-control form-control-sm" value="<?php echo $voucher_auto_no;?>" disabled></div></div>
      
      <div class="col-md-4 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Material Centre:</label><?php	
        echo form_dropdown('matrcntr_id', $matrcntr_dropdown, set_value('matrcntr_id'),' id="matrcntr_id" class="selectwidget form-control required" ');
		?></div></div>

      <div class="col-md-12 col-12 card p-2 mb-2"><div class="input-group">
              <label class="input-group-text">Narration:</label>
              <textarea  name="narration" class="form-control"></textarea>
		</div></div>
      <input type="hidden" name="itmsdata" id="itmsdata">
<input type="hidden" name="vchrdate" id="vchrdate" readonly>
    </div></div>
   

   <div id="grid_search" class="py-3" style="margin:auto;"></div>  
   
    <br> 
    
     <div class="col-12 text-center">
        
         <!--<input type="file" class="">-->
         
        <button type="button" id="submitbtn" class="btn btn-success btn-lg" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">Save</button>
        <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-secondary btn-lg">Quit</a>
        
    </div>
        
   
   
</form>
<?php echo view('includes/footer_scripts'); 
for($i=1;$i<=50;$i++)
$json_data[] =array("item_unit_id"=>"","pq_cellattr"=>array("item_name"=>array("title"=>"")),"item_id"=>"","id"=>'',"item_name"=>'','item_qty'=>'','short_narator'=>'','item_unit'=>'','item_price'=>'','item_amount'=>'');

?>

<style>
    .boldcell{font-weight:700;}
</style>

<script>
var item_qty_balance = {};
function get_item_balances(item_id,item_unit_id,rd){
	 console.log(item_id +","+item_unit_id);
	 var voucher_date = $("#voucher_date").val();	
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
 var item_qty_balance = {};
  var item_qty_balance_b = {};
  $(document).on('change', '#voucher_date', function(e){

        var voucher_date = $("#voucher_date").val();
      

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
   
    var itemlist      = [];  
    var itemsobj      = new Map();
    var itemsobj_sock = new Map();
    var unitslist = <?php echo json_encode($units_list);?>; 
    
   $("#voucher_date").on("change",function(){   
    $(this).attr('readonly',true);
    $(this).attr('disabled',true); 
    $(this).removeClass('datepicker');
    $(this).removeClass('hasDatepicker');
    $("#vchrdate").val($(this).val());
    $("#cleardate").html('<a href="javascript:void(0);" onclick="location.reload();">clear</a>');
    var data=<?php echo json_encode($json_data);?>;
    $("#grid_search").pqGrid('option', 'dataModel.data', data).pqGrid('refreshDataAndView')    
   }); 
   
   $("#submitbtn").on("click",function(){
     var item_checked = [];
     var final_item_id =[];
     var data = $("#grid_search").pqGrid('option', 'dataModel.data');
     var final_item_amouint ="0";
     var final_physical_stock_amouint ="0";
    
      var sdateFrom = '<?php echo $fy_begndt;?>';
    var sdateTo   = '<?php echo $fy_end;?>';
    var sdateCheck  =  $("#voucher_date").val();
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
     
   
       for (var i = 0; i < data.length; i++) {
            var item_unit_id  = data[i]['item_unit_id'];
           var item_id       = data[i]['item_id'];
           var item_name   = data[i]['item_name'];
           var physical_stock  = data[i]['physical_stock'];
           var book_stock    = data[i]['book_stock'];
           var stock_diff   = data[i]['stock_diff'];
           var short_narration = data[i]['short_narration'];
           
           short_narration = short_narration ? short_narration : '';
           if(!physical_stock)
             physical_stock_amount =0;
             else
             physical_stock_amount = physical_stock;
             final_physical_stock_amouint=parseInt(final_physical_stock_amouint)+parseInt(physical_stock_amount);
             
             
            if(item_name!=''){
           
		        item_checked.push({
		            "item_unit_id": item_unit_id,
		            "item_id": item_id,
                    "physical_stock": physical_stock,
                    "book_stock" :book_stock,
                    "stock_diff" :stock_diff,
                    "short_narration" :short_narration,
                   
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
      
      if($("#voucher_series").val()==''  || selerror=='1'){
          alert_notification("Kindly fill the form properly!!!");
          return false;   
       }
       else if(item_checked.length==0 || $("#voucher_series").val()==''  || final_physical_stock_amouint=='' || final_physical_stock_amouint=='0'){
          alert_notification("Kindly fill the items data!!!");
          return false;   
       }
      else if(sdatevalidate==0){
          alert_notification("voucher date is worng!!!");
          return false;    
      } 
      else{
        $("#itmsdata").val(JSON.stringify(item_checked));
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
                // show_loader();
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
    var itembalance ='';

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
                source: <?php echo $item_json_file;?>,
                selectItem:{ on: true }, 
                highlightText:{ on: true }, 
                minLength: 0,
                select: function(event, ui) {
              	  	event.preventDefault();
				   
                    rd.item_id = ui.item.item_id;
                    rd.item_name = ui.item.label;
                    rd.item_unit = ui.item.item_unit;
                    rd.item_unit_id = ui.item.item_unit_id;
                    $(this).val(ui.item.label);
                    
                 
				 var voucher_date = $("#voucher_date").val();
				 var mcid = $("#matrcntr_id").val();
				 
					var urlpath = "<?php echo base_url();?>/admin/physical_verification/ajax_item_balance?mcid="+mcid+"&itmid="+ui.item.item_id+"&unitid="+ui.item.item_unit_id+"&voucher_date="+voucher_date;
                    show_loader();
				    var itm_book_stock_val = function () {
                    var tmp = null;
                    $.ajax({
                        'async': false,
                        'type': "GET",
                        'global': false,
                        'dataType': 'html',
                        'url': urlpath,
                        'success': function (data) {
                            tmp = data;
							stop_loader();
                        }
                    });
					
                    return tmp;
                }();

			    rd.book_stock =  itm_book_stock_val;
				    
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
            var rdunits = ui.rowData;
            var $inp = ui.$cell.find("input");
            
            $inp.autocomplete({
                source: unitslist,
                selectItem: { on: true }, 
                highlightText: { on: true }, 
                minLength: 0,
                select: function(event, ui) {
                event.preventDefault();
                    rdunits.item_unit = ui.item.label;
                    rdunits.item_unit_id =ui.item.value;
                    $(this).val(ui.item.label);
                    var voucher_date = $("#voucher_date").val();
					 var mcid = $("#matrcntr_id").val();
					var urlpath = "<?php echo base_url();?>/admin/physical_verification/ajax_item_balance?mcid="+mcid+"&itmid="+rdunits.item_id+"&unitid="+ui.item.value+"&voucher_date="+voucher_date;
                    show_loader();
				    var itm_book_stock_val = function () {
                    var tmp = null;
                    $.ajax({
                        'async': false,
                        'type': "GET",
                        'global': false,
                        'dataType': 'html',
                        'url': urlpath,
                        'success': function (data) {
                            tmp = data;
							stop_loader();
                        }
                    });
					
                    return tmp;
                }();

			    rdunits.book_stock =  itm_book_stock_val;
				$("#voucher_date").attr('readonly',true);
               $("#voucher_date").attr('disabled',true); 
                $("#voucher_date").removeClass('datepicker');
               $("#voucher_date").removeClass('hasDatepicker');
               $("#vchrdate").val($("#voucher_date").val());
               $("#cleardate").html('<a href="javascript:void(0);" onclick="location.reload();">clear</a>');
				     
                    
                    
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rdunits.to_item_unit = '';
                rdunits.to_item_unit_id = '';
            }).focusout(function () {     
                
                   if(rdunits.item_id!='' && rdunits.item_unit_id!=''){	
					//console.log("unit selected---");			   
					get_item_balances(rdunits.item_id,rdunits.item_unit_id,rdunits);
				}   
                    
                if(rdunits.item_unit_id == '')
                {
                    var index = unitslist.findIndex(function(obj) {
    
                        var string = obj.label.toLowerCase();
                        var text = rdunits.item_unit.toLowerCase();
                         
                        return text != '' ? string.includes(text) : false;
                    });
                    if(index > -1){
                        rdunits.item_unit = unitslist[index].label;
                        rdunits.item_unit_id = unitslist[index].id;
                        
                    }
                    else{
                        rdunits.item_unit = '';
                        rdunits.item_unit_id = '';
                    }
                }
            });
          }
          
          
        var colModel = [
                         { title: "ITEM NAME", dataIndx: "item_name", align: "left",width: 100,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox",
                          init: autoCompleteEditor,
                          options: itemlist
                      },                       
                 },
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
               { title: "Physical Stock", align: "center", width: 100, dataIndx: "physical_stock",	validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],},
               { title: "Book Stock", align: "center", width: 100, dataIndx: "book_stock"},
               { title: "Diff", align: "center", dataIndx: "stock_diff", width: 100,formula: function (ui) {  
                    var rd = ui.rowData;
                    if(rd.physical_stock){

                        if(rd.book_stock<0){
                         var nbook_stock =   rd.book_stock.replace('-', '');
                      return (parseInt(rd.physical_stock)-parseInt(nbook_stock));
                        }
                      else
                      return (parseInt(rd.physical_stock)-parseInt(rd.book_stock));
                      
                    }
                    }},
               { title: "Short Narration", width: 20, align: "center",dataIndx: "short_narration"}
	 	    ];
	 	    
        var dataModel = {"data":<?php echo json_encode($json_data);?>}
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height:420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel: colModel,  
             columnTemplate: { render: commentRender },
            numberCell: { show: true },
            editable: true,
            cellSave: function(evt, ui){
                   this.refresh();
               },
            wrap:false,   
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
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
 </script>	
</body>
</html>
