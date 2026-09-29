<?php $header = array( 	'title' => 'Memorandum Voucher' ); ?>
<?php echo view('includes/header',$header); ?>
<?php  $comp_vch_series_no ='0'; 
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>
<style>
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

</style>
<style>
/*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
}
</style>

<div class="row mb-2">
    <div class="col-md-6 order-1"><h3>Memorandum Voucher</h3></div>
<div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
     <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>   
            <a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history"><span class="material-symbols-outlined">visibility</span></a>    
            <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 
            
            <a href="#"><span class="material-symbols-outlined open-comingsoon ">print</span></a>
            <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined open-comingsoon">download</span></span></a>
            <ul class="dropdown-menu ">
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
  <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>  
    </div>
    </div> 

 <div class="col-md-6 order-2 order-md-3">
    <select form="form1" name="currency_id" class="form-select d-inline-block" style="width:160px;">
       <?php 
	   foreach ($currency_list as $value) { ?>
            <option value="<?php echo $value['comp_currency_id']; ?>"><?php echo $value['curr_name']; ?> (<?php echo $value['curr_symbol']; ?>)</option>
       <?php } ?>
   </select>
   </div>  
  <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
    <button class="btn btn-sm btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Action</a></li>
        <li><a class="dropdown-item" href="#">Another action</a></li>
        <li><a class="dropdown-item" href="#">Something else here</a></li>
    </ul>
     <a href="<?php echo history_back();?>" class="btn btn-outline-success showinline-md">Back</a>
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
        <p class="text-center pt-3">
            <a data-bs-toggle="modal" data-bs-target="#moreoptionsmodal" class="btn btn-outline-success">View</a>
        </p>
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

</div>
    <div id="validation_errors"></div>

     <?php 
	   $attributes = " id='form1' name='form1' class='needs-validation' novalidate";
       echo form_open(base_url().$folder_path.'vouchers/memorandum/'.$voucher_type_id, $attributes);
     ?>      
	  <?php echo $message_output->run() ;?> 	  
      <input type="hidden" name="voucherdata" id="voucherdata">
      <input type="hidden" name="bbbdata" id="bbbdata">
      <input type="hidden" name="ccdata" id="ccdata">
	   <input type="hidden" name="btnid" id="btnid">
    <div class="col-md-12">
	<div class="row mx-0 p-0 mb-2">
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Date: </label>
                <input type="text" name="voucher_date" id="voucher_date" class="datepicker form-control" placeholder="dd-mm-yyyy" value="<?php echo $voucher_date;?>" autocomplete="off" required>
            </div>
        </div>
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Voucher Series:</label> 
                <?php
                  $sel_sersval = arrayfrstval($voucher_series_dropdown);
                   echo form_dropdown('voucher_series', $voucher_series_dropdown,$sel_sersval,' id="voucher_series" class="voucher_series form-control" required ');
		        ?>
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
    
        <div class="col-md-9 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Narration: </label>
                <textarea  name="narration" class="form-control"></textarea>
            </div>
        </div>
		
		<div class="col-md-3 col-6 card p-2">
      <table class="table-sm" id="account_details_label">
        <tbody>
          <tr>
            <td>Name</td>
            <td class="acc_name"></td>
          </tr>
          <tr>
            <td>Balance</td>
            <td class="acc_bal"></td>
          </tr>
        </tbody>
      </table>
    </div>
    </div>
    
    <div id="grid_search" style="margin:auto;"></div>  

    </div>
    
    <div class="col-12 text-center pt-2">
        <input type="button" id="submitbtn" name="submitbtn" value="SAVE" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-lg btn-success mx-2">
       <input type="button" id="submitbtn_drft" name="submitbtn_drft" value="SAVE AS DRAFT"   class="btn btn-lg btn-success mx-2">

		<a href="javascript:main(0)"  onclick="window.history.go(-1); return false;" class="btn btn-lg btn-secondary mx-2">QUIT</a>
    </div>
 </form>

   
<?php echo view('includes/footer_scripts');
for($i=1;$i<=50;$i++)
 $json_data[] = array("drcr"=>'','acc_name'=>'','description'=>'','debit'=>'','credit'=>'','acc_id'=>'','acc_type'=> '');

?>

<script>


var intRegex = /^\d+$/;
var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
var drcrlist     = [{"C":"C"},{"D":"D"}];
$("#submitbtn,#submitbtn_drft").on("click",function(){
    var item_checked    = [];
    var final_acc_id    = [];
    var data            = $("#grid_search").pqGrid('option', 'dataModel.data');   
    var dateFrom        = '<?php echo $fy_begndt;?>';
    var dateTo          = '<?php echo $fy_end;?>';
    var dateCheck       =  $("#voucher_date").val();
    var d1              = dateFrom.split("-");
    var d2              = dateTo.split("-");
    var c               = dateCheck.split("-");    
    var from_year       = d1[2];  // -1 because months are from 0 to 11
    var to_year         = d2[2];
    var check_year      = c[2];//, parseInt(c[1])-1, c[0]);
	var btn_id =  $(this).attr("id");
    
    if( (check_year==from_year) || (check_year==to_year))
        var datevalidate=1;
    else
        var datevalidate=0;    
    
    var debitsum=0;
    var creditsum=0;
    
    for (var i = 0; i < data.length; i++) {

        var drcr         = data[i]['drcr'];
        var acc_id       = data[i]['acc_id'];
        var acc_name     = data[i]['acc_name'];
        var acc_type     = data[i]['acc_type'];
        var description  = data[i]['description'];
        var debit        = data[i]['debit'];
        var credit       = data[i]['credit'];        
        if(acc_id != '' && acc_name != ''){
            debitsum  += parseAmount(debit);
            creditsum += parseAmount(credit);          
            
            var item_data = {
                "drcr": drcr,
                "acc_id":  acc_id,
                "acc_type":  acc_type,
                "description" :description,
                "debit" : parseAmount(debit),
                "credit" : parseAmount(credit)
            }
                        
            item_checked.push(item_data);
        
        }
    } // end for loop
	
    if(item_checked.length==0 || $("#voucher_series").val()==''){
        alert_notification("Kindly fill the voucher data!!!");
        return false;   
    }
    else if(datevalidate==0){
        alert_notification("voucher date is worng!!!");
        return false;    
    }   

    else if(debitsum != creditsum){
        alert_notification("voucher totals worng!!!");
        return false;    
    } 
    else{
        show_loader();
		$("#btnid").val(btn_id);
        $("#voucherdata").val(JSON.stringify(item_checked));
        $("#form1").submit();    
    }
   
});

    $(document).on('submit', '#form1', function(e){
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
					stop_loader();
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
						<div class="alert-error-custom">
							<i class="bi bi-x-circle-fill"></i>
							<div>
							<strong>Error!</strong><ul>${list}</ul>
							</div>
							<button type="button" class="btn-close" aria-label="Close"></button>
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
				stop_loader();
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
 var rd_tm_obj;    
    function set_row_details_label()
    {
      if(rd_tm_obj){
        clearTimeout(rd_tm_obj);
      }	 
      rd_tm_obj = setTimeout(function(){
          var select_row = $("#grid_search").pqGrid("selection", { type:'cell', method:'getSelection'});
          if(select_row && select_row[0])
          {
              var rd = select_row[0].rowData;
              if(rd.acc_id){
                var acc_bal = rd.account_balance;
                if(acc_bal){
                  if(acc_bal < 0)
                    acc_bal = formatAmount(Math.abs(acc_bal)) + ' Cr';
                  else
                    acc_bal = formatAmount(acc_bal) + ' Dr';
                }
                else{
                  acc_bal = '0 Dr';
                }

                 $('#account_details_label .acc_name').text(rd.acc_name);
                 $('#account_details_label .acc_bal').text(rd.account_balance);
              }
              else{
                  $('#account_details_label .acc_name').text('');
                 $('#account_details_label .acc_bal').text('');
              }
          }
          else{
                $('#account_details_label .acc_name').text('');
               $('#account_details_label .acc_bal').text('');
            }
      },500);   
    }
 
 $(function () {
 
   var accnt_balances = {};
   $(document).on('change', '#voucher_date', function(e){

        var voucher_date = $("#voucher_date").val();
        var acc_id_array = [];

        $.each(accnt_balances, function(index, value){
            acc_id_array.push(index);
        });
        
       /*  $.ajax({
            url: '<?php echo $base_url; ?>ajax/get_all_account_balances', 
            type: 'POST',
            data: {voucher_date: voucher_date, acc_id_array: acc_id_array},
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
                    if(response.account_balances.length > 0){
                        $.each(response.account_balances, function(index, obj){
                            if(accnt_balances.hasOwnProperty(obj.acc_id)) {
                                accnt_balances[obj.acc_id] = obj.balance;
                            }
                        });
                        update_grid_account_balances();
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
        }); */
		set_row_details_label();

   });

   function update_grid_account_balances()
   {
        var data = $("#grid_search").pqGrid('option', 'dataModel.data');

        $.each(data, function(index,obj){
            if(obj.acc_id != '' && obj.acc_id != undefined)
            {
                data[index]['account_balance'] = accnt_balances[obj.acc_id];
            }
        });


        $("#grid_search").pqGrid('option', 'dataModel.data', data);
        $("#grid_search").pqGrid('refreshDataAndView');
   }


     function calculateSummary() {

        var debitTotal = 0,
            creditTotal = 0,
            data = this.option('dataModel.data'),
            len  = data.length;

        data.forEach(function(row){

            if(row.acc_id != '' && row.acc_name != '')
            {
                if(row.debit == '' || typeof row.debit == 'undefined'){
                    var debit =0;
                }else
                 var debit =  row.debit;
                
                if(row.credit == '' || typeof row.credit == 'undefined'){
                    var credit =0;
                }else
                var  credit =  row.credit;
             
                 
                debitTotal  += parseAmount(debit);
                creditTotal += parseAmount(credit);  
            }
        })

        var totalData = {
                drcr      : "Total",
                debit     : formatAmount(debitTotal),
                credit    : formatAmount(creditTotal),
                pq_rowcls : 'grid_footer_color',
                summaryRow: true
            }
            
            this.option('summaryData', [totalData]);
          }
         
         function disableTextRenderer(ui) {
            var //grid = $(this).pqGrid('getInstance').grid,
                grid = this,
                rowData = ui.rowData,
                rowIndx = ui.rowIndx,
                dataIndx = ui.dataIndx;

            if (grid.isEditableCell({ rowIndx: rowIndx, dataIndx: dataIndx }) == false) {
                //inject disabled class into read only cells.                
                grid.addClass({ rowIndx: rowIndx, dataIndx: dataIndx, cls: 'disabled' });
                
            }
            else {
                grid.removeClass({ rowIndx: rowIndx, dataIndx: dataIndx, cls: 'disabled' });
            }
        };
        
        var autoCompleteEditor = function (uimain) {
            
            var $inp = uimain.$cell.find("input");
            var rd = uimain.rowData;
            var grid = this;
            
           var colindex = uimain.column.dataIndx;
           
            $inp.autocomplete({
                source: <?php echo $accounts_json_file; ?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength:0,
                select: function(event, ui) {
                    event.preventDefault();
                   // console.log(ui.item);
                    rd.acc_short_code = ui.item.acc_short_code;
					
                    $(this).val(ui.item.label);
                    rd.acc_id = ui.item.acc_id;

                    if(ui.item.is_sundry==1)                    
                        rd.acc_type = 'bsd';
                    else if(ui.item.is_acc==1)                   
                        rd.acc_type = 'acc';
          
                    }
                })./* focus(function () {
                    
                    $(this).autocomplete("search", "");
                    rd.acc_name = '';
                    rd.acc_id = '';
                    rd.acc_type = '';
                }). */
				focusout(function () {  
				var enteredValue = $(this).val().toLowerCase();
			var  source_data=<?php echo $accounts_json_file; ?>;				  
			const isValid = source_data.some(item => item.label.toLowerCase() === enteredValue);
			if(rd.acc_id == '' || isValid==false)
                    {	$(this).val('');
						$(this).autocomplete("search", "");
                        rd.account_balance = 0;
                        rd.acc_name = '';
                        rd.acc_id = '';
                        rd.is_cc = '';
                        rd.is_bbb = '';
                        rd.is_cash = '';
                        rd.acc_type = '';
                    }
					
                    if(rd.acc_short_code=="sagstpd" || rd.acc_short_code=="sapnlap"){				
						alert_notification("This Is A System Generated A/C");	
						rd.acc_name = '';
						rd.acc_id = '';				
						return false;
					}
                    if(rd.acc_id != '')
                    {     
                     if(accnt_balances.hasOwnProperty(rd.acc_id) ==false ) {
                      var voucher_date = $("#voucher_date").val();
                      var return_response = async function () {
    try {
        // Use a Promise to wrap the AJAX request
        const accbalance = await new Promise((resolve, reject) => {
            $.ajax({
                type: "GET",
                dataType: "html",
                url: "<?php echo $base_url; ?>ajax/accountbalance/" + rd.acc_id + "/" + voucher_date,
                success: function (data) {
                    resolve(data);  // Resolve the Promise with the response data
                },
                error: function () {
                    reject("Error in AJAX request");  // Reject the Promise on error
                }
            });
        });

        // Once the response is resolved, you can use accbalance here
        //console.log("Account balance is: " + accbalance);
		
		
        return accbalance;
    } catch (error) {
        // Handle any errors that occur during the async operation
        console.error(error);
        return null;  // Return null or handle it as needed
    }
};

 // Usage example
async function fetchBalance() {
    const balance = await return_response();
	rd.account_balance = balance;    
}

//fetchBalance(); 
                        
                     }
                 
                    }
                   
                         
                    
					set_row_details_label();
            });
            
            $inp.keydown(function(e) {
               if(e.which == 13) 
               {
                    var event = $.Event( "keydown", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keypress", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keyup", { which: 9 } );
                    $inp.trigger(event);
                    
                    grid.isValid( { rowData: rd } );
              }
            });
        }
        
        function crdrEditor(ui) {
        
            var $inp = ui.$cell.find("select"),
                di = ui.dataIndx,
                rd = ui.rowData,               
                grid = this,
                rowIndx = ui.rowIndx;

            
            
            $inp.on("change", function (evt) {
                var crdr = $(this).val();
                
                if(crdr == '')
                {
                    rd.acc_name = '';
                    rd.acc_id = '';
                    rd.acc_type = '';
                    rd.description = '';
                    rd.debit = '';
                    rd.credit = '';
                    grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'credit', cls: 'grid_footer_color' });
                     grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'debit', cls: 'grid_footer_color' });
                    
                }
                if(crdr == 'C'){
                   var amount = expected_drcr(grid,crdr);
                    rd.credit = amount > 0 ? amount : '';
                    rd.debit = '';
                    rd.acc_name = '';
                    rd.acc_id = '';
                    rd.acc_type = '';
                    rd.description = '';

                   grid.addClass({ rowIndx: ui.rowIndx, dataIndx: 'debit', cls: 'grid_footer_color' });
                   grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'credit', cls: 'grid_footer_color' });

                    // grid.saveEditCell();
                    // grid.setSelection( {rowIndx: rowIndx,colIndx: 4} );
                }
                if(crdr == 'D'){
                    var amount = expected_drcr(grid,crdr);
                    rd.credit = '';
                    rd.debit = amount > 0 ? amount : '';
                    rd.acc_name = '';
                    rd.acc_id = '';
                    rd.acc_type = '';
                    rd.description = '';

                   grid.addClass({ rowIndx: ui.rowIndx, dataIndx: 'credit', cls: 'grid_footer_color' });
                   grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'debit', cls: 'grid_footer_color' });

                   // grid.saveEditCell();
                   // grid.setSelection( {rowIndx: rowIndx,colIndx: 3} );
                }
                
            })
        };

        function expected_drcr(grid,drcr)
        {
            var debitTotal = 0;
            var creditTotal = 0;
            var data = grid.option('dataModel.data');


            data.forEach(function(row){
                if(row.debit == '' || typeof row.debit == 'undefined'){
                    var debit =0;
                }else
                 var debit =  row.debit;
                
                if(row.credit == '' || typeof row.credit == 'undefined'){
                    var credit =0;
                }else
                var  credit =  row.credit;
             
                 
                debitTotal  += parseAmount(debit);
                creditTotal += parseAmount(credit);
            });

            if(drcr == 'C'){
                return debitTotal - creditTotal;
            }
            if(drcr == 'D'){
                return creditTotal - debitTotal;
            }
            return 0;
        }
       
        var colModel = [
            
             { title: "DR/CR",sortable:false, dataIndx: "drcr", width: 20,cls: 'pq-drop-icon pq-side-icon',editor: {
                    type: 'select',
                    options: drcrlist,
                    init: crdrEditor
                }
            },
            
            { title: "ACCOUNT",sortable:false, dataIndx: "acc_name", width: 100,dataType: "text",cls: 'pq-drop-icon pq-side-icon',
                editor: {                   
                		  type: "textbox",
                		  attr: "autocomplete='off'",
                          init: autoCompleteEditor,
                          options: [],
                      },
                      
                render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    var account_balance = '0'; 
                    if(typeof rd.acc_id !== "undefined" && rd.acc_id != '' && rd.acc_name != '')
                    {      
                       // var account_balance = rd.account_balance;
                       // grid.addClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'pq-cell-red-tr pq-has-tooltip' });
						 return rd.acc_name;
                       // return '<span data-title-tooltip="  '+account_balance+' ">'+ui.cellData+'</span>';
                    }
					return '';
                }          
                       
            },
            
            { title: "SHORT NARRATION",sortable:false, width: 100, dataType: "string", dataIndx: "description",
                editable: function (ui) {
                   var acc_id = ui.rowData['acc_id'];
                    if (acc_id != '') {
                        return true;
                    }
                    return false;
                },
            },
            { title: "DEBIT",sortable:false, width: 20, align: "left",dataIndx: "debit",dataType: "float",
                validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
                   var drcrval = ui.rowData['drcr'];
                   var acc_id = ui.rowData['acc_id'];
                    if (drcrval=='D' && acc_id != '') {
                        return true;
                    }
                    return false;
                },
                render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    if(rd.drcr == 'D')
                    {
                        grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'disabled' });
                        rd.credit = '';
                        if(rd.debit != ''){
                            rd.debit = parseAmount(rd.debit);
                            return formatAmount(rd.debit);   
                        }
                        return '';
                    }
                }
                
            },
            { title: "CREDIT",sortable:false, width: 20, align: "left",dataIndx: "credit",dataType: "float",
                validations: [{ type: 'gt', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
                   var drcrval = ui.rowData['drcr'];
                   var acc_id = ui.rowData['acc_id'];
                    if (drcrval=='C' && acc_id != '') {
                        return true;
                    }
                    return false;
                },
                render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    if(rd.drcr == 'C')
                    {
                        grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'disabled' });
                        rd.debit = '';
                        if(rd.credit != ''){
                            rd.credit = parseAmount(rd.credit);
                            return formatAmount(rd.credit);   
                        }
                        return '';
                    }
                }
             }
	 	    ];
	 	    
	 	    
        var dataModel = {"data":<?php echo json_encode($json_data);?>}
      
        var newObj = {
           
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            hoverMode:'cell',
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel: colModel,  
            change: calculateSummary, 
            numberCell: { show: true,width: 30, title: "#" },
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

        newObj.cellKeyDown = function(evt, ui) {
			 set_row_details_label();
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
		
		newObj.cellClick = function( event, ui ) {
            set_row_details_label();
          
        };
        var $grid = $("#grid_search").pqGrid(newObj);
        
        
     });
     
</script>

</body>
</html>