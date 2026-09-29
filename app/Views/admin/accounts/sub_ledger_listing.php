<?php $header = array( 	'title' => 'Sub Account Ledger' ); ?>
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

<?php 
$params = http_build_query([
    'account_id' => $account_id,
    'from_date' => $from_date,
    'to_date' => $to_date,
    'm' => $m,
    'view' => $view,
    'consoview' => $consoview,
]);
?>
<div class="row mb-md-0 mb-3">
    <div class="col-md-6"><h3 class="pb-3">Sub Account Ledger</h3></div>
    <div class="col-md-6 text-end"><div class="taskmenus">
        <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 
        <a href="<?= base_url() ?>admin/export/acc_ledger_det?<?= $params ?>" target="_blank">
            <span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li class="open-comingsoon"><a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a></li>
			<li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a></li>
			<li class="open-comingsoon"><a class="dropdown-item" href="javascript:void(0)" onclick="print_pdf();">Document</a></li>
          </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon">share</span></a> 
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
        <div class="input-group">
            <span class="input-group-text px-1">From</span>
            <input type="text" name="from_date" id="from_date" value="<?php echo $from_date;?>" class="datepicker form-control p-2" required  style="width:90px;">
            
            <span class="input-group-text px-1">To</span>
            <input type="text" name="to_date" id="to_date" value="<?php echo $to_date;?>" class="datepicker form-control p-2" required style="width:90px;">
            
         
            
            <input type="submit" id="gofilter" class="btn btn-sm btn-success" value="GO">
        </div>
		<!--
		<div class="form-check form-check-inline me-2">
            <input form="salefrm" class="form-check-input" <?= ($consoview==1) ? 'checked' : '' ?> type="checkbox" value="1" name="consoview" id="consoview">
            <label class="form-check-label" for="consoview">Consolidated In All Branches</label>
        </div>
        <div class="form-check form-check-inline me-2">
            <input form="salefrm" class="form-check-input" <?= ($m==1) ? 'checked' : '' ?> type="checkbox" value="1" name="m" id="m" onchange="this.form.submit()">
            <label class="form-check-label" for="m">Memorandum</label>
        </div> -->
        <div class="form-check form-check-inline me-2">
            <input  class="form-check-input" type="checkbox" value="1" name="fg" id="fg">
            <label class="form-check-label" for="fg">Fixed Grid</label>
        </div> 
        </form>
    </div>
    <div class="col-lg-7 text-end">

        <div class="dropdown d-inline-block me-1" style="width:220px;">
		
          <div class="input-group input-group-sm input-group">
              <span class="input-group-text">View</span>
              <select form="salefrm" class="form-select" id="view" name="view">
                   <option value=""></option>
				  <option <?= ($view==0) ? 'selected' : '' ?> value="0">Condensed</option>
                 <!-- <option <?= ($view==1) ? 'selected' : '' ?> value="1">Detailed</option>
				  <option <?= ($view==3) ? 'selected' : '' ?> value="3">Columnar Report</option>
				 
				  <option  value="acc_sum">Account Summary</option>
				   -->
				 
              </select>
              
          </div>  
        </div>
		 <div class="dropdown d-inline-block me-1" style="width:220px;">
		
          <div class="input-group input-group-sm input-group">
            <span class="input-group-text">Type</span>
            <select form="salefrm" class="form-select" id="type" name="type">
                  <option <?= ($type==1) ? 'selected' : '' ?> value="1">Regular</option>
                 <!-- <option <?= ($type==2) ? 'selected' : '' ?> value="2">Memorandum</option>-->
            </select>
          </div>  
        </div>
        <div class="dropdown float-end">
            <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
            <!--<ul class="dropdown-menu">
                <li><a id="trash" data-type="0" class="dropdown-item" href="javascript:void(0);">Enable Trash Mode</a></li>
            </ul>
            <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Voucher Series </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Action</a></li>
                <li><a class="dropdown-item" href="#">Another action</a></li>
                <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>-->
             <a href="javascript:void(0);" onclick="history.back()" class="btn btn-sm btn-outline-success mt-0 m-1">Back</a>
        </div>
    </div>
</div>
<div class="modal fade modal-lg" id="calendarmodal" tabindex="-1" aria-labelledby="calendarmodallabel" style="display: none;" aria-hidden="true">
     <div class="modal-dialog">
        <div class="modal-content">
  
            <div class="col-12 calccard card m-auto">
                <form class="form" method="post" action ="<?= base_url() ?>admin/reports/account_subledger" id="salefrm2" autocomplete="off">

                <input type="hidden" name="type" value="Account Sub Ledger">
                <input type="hidden" name="detail" value="Account">

                <div class="col-md-12 p-2">
                    <label>Account</label>
					<input type="text" name="item" id="search-box" class="form-control borderdark" value="<?= $account_name ?>" required placeholder="Search name...">
                    <input type="hidden" name="id" value="<?= $account_id ?>">
                </div>

                <div class="row p-4">
        <?php $fy_bgn_yr = date('Y',strtotime(session()->get('ses_company_fy_beginning'))); ?>
        <div class="comp_calender col-md-6">
            <button type="button" data-month="4" class="btn btn-light month_btn">APR</button>
            <button type="button" data-month="7" class="btn btn-light month_btn">JUL</button>
            <button type="button" data-month="10" class="btn btn-light month_btn">OCT</button>
            <button type="button" data-month="1" class="btn btn-light month_btn">JAN</button>
            <button type="button" data-month="5" class="btn btn-light month_btn">MAY</button>
            <button type="button" data-month="8" class="btn btn-light month_btn">AUG</button>   
            <button type="button" data-month="11" class="btn btn-light month_btn">NOV</button> 
            <button type="button" data-month="2" class="btn btn-light month_btn">FEB</button>
            <button type="button" data-month="6" class="btn btn-light month_btn">JUN</button>
            <button type="button" data-month="9" class="btn btn-light month_btn">SEP</button>
            <button type="button" data-month="12" class="btn btn-light month_btn">DEC</button>
            <button type="button" data-month="3" class="btn btn-light month_btn">MAR</button>
            <button type="button" data-quater="1" class="btn btn-qlight quater_btn">Q1</button>
            <button type="button" data-quater="2" class="btn btn-qlight quater_btn">Q2</button>
            <button type="button" data-quater="3" class="btn btn-qlight quater_btn">Q3</button>
            <button type="button" data-quater="4" class="btn btn-qlight quater_btn">Q4</button>
            <button type="button" data-hyear="1" class="btn btn-hlight hyear_btn">H1</button>
            <button type="button" data-hyear="2" class="btn btn-hlight hyear_btn">H2</button>
            
            <span class="fw-bold d-inline-block px-4"><input class="form-check-input mt-1 me-1" type="checkbox" value="1" id="tillperiod"> TILL PERIOD</span>
        </div>
        <div class="col-md-6">
            <div class="input-group mb-3">
                <button type="button" class="input-group-text" id="prev_year"><span class="material-symbols-outlined">arrow_back_ios</span></button>
                <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:68%; text-align: center; display: block;">FY: <?= $fy_bgn_yr ?> - <?= ($fy_bgn_yr+1) ?></button>
                <button type="button" class="input-group-text" id="next_year"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">From</div>
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control datepicker" fdprocessedid="cw7ydk" name="fromdate" id="fromdate" value="<?= $from_date ?>" placeholder="dd-mm-yyyy" required>
                    </div>
                </div>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">To</div>
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control datepicker"  fdprocessedid="b20o4z" name="todate" id="todate" value="<?= $to_date ?>" placeholder="dd-mm-yyyy" required>
                    </div>
                </div>
            </div>
            <p class="text-end"><button type="button" class="input-group-text fw-bold ms-auto"  id="tilldate" required>TILL DATE</button></p>
        
        </div> 
        
        <p class="text-center pt-4">
            <button type="submit" class="btn btn-lg btn-success">GO</button>
            <button type="button" class="btn btn-lg btn-secondary" data-bs-dismiss="modal">Quit</button>
        </p>  
        
    </div>
                 </form>
                 
            </div> 
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
        <button type="button" class="btn btn-success">Save changes</button>
      </div>
    </div>
  </div>
</div>
<div class="row">
    <div class="col-md-6">
        <p><em>Account: <strong><?php echo $account_name;?></strong></em></p>
    </div>
      <div class="col-md-6 text-end">
       <p class="text-end pe-3"><em>Opening Balance: <strong><?php echo $opening_balance;?></strong></em></p>
    </div>
</div>

<div id="validation_errors"></div>
<br>
<div id="grid_search" style="margin:auto;"> </div> 

<div class="row">
<div class="col-md-4 text-end">
<em>Total Debit: <strong id="total_debit_counter"></strong></em></p>
</div>
<div class="col-md-4 text-end">
<em>Total Credit: <strong id="total_credit_counter"></strong></em></p>
</div>
      <div class="col-md-4 text-end">
       <p class="text-end pe-3"><em>Closing Balance: <strong id="total_closing_balance"></strong></em></p>
    <?php if($view==3){ ?><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#totalAmountModel">Total Amount</button><?php } ?>
	</div>
	 
</div> 

 <!-- The Modal -->
<div class="modal" id="voucherCreationModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Create Voucher</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        
        <div class="row p-2">
            <div class="col-md-6">
                <h4>Sales-</h4>
                <div class="list-group">
                  <a href="<?= base_url() ?>admin/sales/item?p=1" class="list-group-item list-group-item-action">Sales Invoice</a>
                  <a href="<?= base_url() ?>admin/sales_order/item?p=1" class="list-group-item list-group-item-action">Sales Order</a>
                  <a href="<?= base_url() ?>admin/credit_note/item?p=1" class="list-group-item list-group-item-action">Credit Note</a>
                  <a href="<?= base_url() ?>admin/delivery_challan/add?p=1" class="list-group-item list-group-item-action">Delivery Challan</a>
                  <a href="<?= base_url() ?>admin/quotations/item?p=1" class="list-group-item list-group-item-action">Quotations</a>
                </div>
            </div>
            <div class="col-md-6">
                <h4>Purchases-</h4>
                <div class="list-group">
                  <a href="<?= base_url() ?>admin/purchase/item?p=1" class="list-group-item list-group-item-action">Purchase Invoice</a>
                  <a href="<?= base_url() ?>admin/purchase_order/item?p=1" class="list-group-item list-group-item-action">Purchase Order</a>
                  <a href="<?= base_url() ?>admin/debit_note/item?p=1" class="list-group-item list-group-item-action">Debit Note</a>
                  <a href="<?= base_url() ?>admin/inward_challan/add?p=1" class="list-group-item list-group-item-action">Inward Challan</a>
                  <a href="<?= base_url() ?>admin/purchase_requisition/with_amount?p=1" class="list-group-item list-group-item-action">Purchase Requisition</a>
                </div>
            </div>

            
        </div>

        <div class="row p-2">
            <div class="col-md-6">
                <h4>Banking-</h4>
                <div class="list-group">
                  <a href="<?= base_url() ?>admin/vouchers/invoice/9?p=1" class="list-group-item list-group-item-action">Payments</a>
                  <a href="<?= base_url() ?>admin/vouchers/invoice/13?p=1" class="list-group-item list-group-item-action">Receipts</a>
                  <a href="<?= base_url() ?>admin/vouchers/invoice/1?p=1" class="list-group-item list-group-item-action">Contra</a>
                  <a href="<?= base_url() ?>admin/vouchers/invoice/5?p=1" class="list-group-item list-group-item-action">Journal</a>
                  <a href="<?= base_url() ?>admin/memorandum/invoice?p=1" class="list-group-item list-group-item-action">Memorandum</a>
                </div>
            </div>
            <div class="col-md-6">
                <h4>Items-</h4>
                <div class="list-group">
                  <a href="<?= base_url() ?>admin/stock_transfer/mc?p=1" class="list-group-item list-group-item-action">Stock Transfer</a>
                  <a href="<?= base_url() ?>admin/physical_verification/add?p=1" class="list-group-item list-group-item-action">Physical Verification</a>
                  <a href="<?= base_url() ?>admin/production/add?p=1" class="list-group-item list-group-item-action">Production Voucher</a>
                  <a href="<?= base_url() ?>admin/stock_journal/invoice/19?p=1" class="list-group-item list-group-item-action">Stock Journal</a>
                  <a href="<?= base_url() ?>admin/consignment_packing/?p=1" class="list-group-item list-group-item-action">Consignment Packing</a>
                </div>
            </div>
            
        </div>

      </div>

    </div>
  </div>
</div>
 

<!-- The Modal -->
<div class="modal fade" id="totalAmountModel">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Account Total(<?php echo $from_date;?>--<?php echo $to_date;?>)</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="table-responsive ">
            <table class="table table-bordered table-hover table-sm">
    
					<?php $arrsm=array();if($all_columns){ ?>
                    <?php foreach($all_columns as $account_key =>$accval){
							   $acckery =str_replace(" ","",$accval).''.$account_key;
							   if(isset($ledger_list['acc_columnar_total'])){
								 foreach($ledger_list['acc_columnar_total'] as $key => $value){   
							       if(isset($value[$acckery])){	
							        $arrsm[$acckery][] = $value[$acckery]['actbal'];
								
								}}
							   }
							
						if(isset($arrsm[$acckery]))
								$acc_column_sum = array_sum($arrsm[$acckery]);
							    else
								$acc_column_sum = 0;
							
							    if($acc_column_sum<0)
									$acc_column_sum_lbs='Cr.';
								else
                                    $acc_column_sum_lbs='Dr.';
						?>
                       <tr> 
					   <td style="text-align: left;padding:6px;" nowrap><?php echo $accval;?></td>
					   <td style="text-align: right;padding:6px;"><?php echo formatAmount(abs($acc_column_sum)).' '.$acc_column_sum_lbs;?></td>
					   
					   </tr>                       
					<?php } } ?>
                    
            </table>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<?php echo view('includes/footer_scripts'); ?>
<script>
var pageview ='<?php echo $view;?>';
var consoview ='<?php echo $consoview;?>';

$(document).on("change","#view",function(){
    	if($(this).val()=="acc_sum"){
    	    window.location.href="<?php echo base_url();?>admin/accounts/monthly_detail/<?php echo $account_id;?>";
    	    
    	    
    	}else{
    
	if($(this).val()==3)
	$("#consoview").attr("checked",false);

 $("#salefrm").submit();
    	}
	
});

$(document).on("change","#type",function(){
 $("#salefrm").submit();
});

    $(document).keydown(function(e) {
        if (e.which == 45) { 
            $('#voucherCreationModal').modal('show');
        }
    });
    function getList(list) 
    {
        $('input[name="item"]').off('blur'); // unbind event first
        
        if($('input[name="item"]').hasClass('ui-autocomplete-input')) {
            $('input[name="item"]').autocomplete("destroy");
        }
        
        $( 'input[name="item"]' ).autocomplete({
            source: list,
            minLength: 0,
            select: function( event, ui ) {
                $(this).val(ui.item.label);
                $('input[name="id"]').val(ui.item.id);
            }
        })
        .on('focus', function(){
            $(this).autocomplete( "search", "" );
            $('input[name="item"]').val('');
            $('input[name="id"]').val('');
        })
        .on('blur', function(){
            
            if($(this).val() != '' && $('input[name="id"]').val() == '')
            {
                var acc = $(this).val();
                var index = list.findIndex(function(obj) {
                    var string = obj.label.toLowerCase();
                    var text = acc.toLowerCase();
                   return  string.includes(text);
                });
                if(index > -1){
                    $(this).val(list[index].label);
                    $('input[name="id"]').val(list[index].id);
                    
                }
                else{
                    $('input[name="item"]').val('');
                    $('input[name="id"]').val('');
                }
            }
            if($('input[name="id"]').val() == '')
            {
                $('input[name="item"]').val('');
                $('input[name="id"]').val('');
            }
        });
    }

    $(document).on('change', '#delete_all_checkbox', function(){
        if(this.checked) 
            $('.delete_one_checkbox').prop("checked", true);
        else
            $('.delete_one_checkbox').prop("checked", false);
    });

    $(document).on('change', '.delete_one_checkbox', function(){
        $('#delete_all_checkbox').prop("checked", false);
    });

    function show_trash_checkbox()
    {
        var colModel = $("#grid_search").pqGrid('option', 'colModel');
        colModel[0].hidden = false;

        $("#grid_search").pqGrid( "option", "colModel", colModel );
        $("#grid_search").pqGrid( {autoFit: true} );
        $("#grid_search").pqGrid("refreshCM");
        $("#grid_search").pqGrid("refresh");

        $('.delete_checkbox').prop("checked", false); 
    }

    function hide_trash_checkbox() // written in footer
    {
        var colModel = $("#grid_search").pqGrid('option', 'colModel');
        colModel[0].hidden = true;

        $("#grid_search").pqGrid( "option", "colModel", colModel );
        $("#grid_search").pqGrid("refreshCM");
        $("#grid_search").pqGrid("refresh");

        $('.delete_checkbox').prop("checked", false); 
    }

    $(document).on("click","#trash",function(){
        var type = $(this).data('type');
        if(type == 0){
            $(this).text('Delete Vouchers'); 
            $(".hidden").show();
            $(this).data('type', '1');
            alert_success("Trash mode enabled");
            show_trash_checkbox();
        }
        if(type == 1){
            
            if( $('.delete_one_checkbox:checked').length=='0'){
                alert_notification("First select voucher to delete!!");
                return false;
            }
            else{
                var vouchers_array=[];
                $('.delete_one_checkbox:checked').each(function(){
                    vouchers_array.push($(this).val());
                });
                
                confirm_voucher_delete(vouchers_array);
            }
        }
     });

	
     $(function () {
       
         
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
	
	   function calculateSummary(){
		   var grid = this;
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
				
			var debitTotal = 0,
            creditTotal = 0,
            balanceTotal = 0,
           data = $("#grid_search").pqGrid( "pageData" ),
			len = data.length;
			
        data.forEach(function(row){             
            debitTotal +=  parseAmount(row.debit_total);
            creditTotal +=  parseAmount(row.credit_total);

        })

        var totalData = {
                txn_date: "Total",
                voucher_type :"",
                voucher_no:"",
				account_name:"",
				short_narration:"",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            
       
        this.option('summaryData', [totalData]);	
	   }
    
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
			     if(dataIndx=='amount')
					   dataIndx= 'amount_total'; 
			
                filterObject = [{ dataIndx: dataIndx, condition: condition, value: value}];
            }
            $grid.pqGrid("filter", {
                oper: 'replace',
                data: filterObject
            });
        }
       
        var colModel = [
             { dataIndx: "state", maxWidth: 30, minWidth: 30, sortable:false ,align: "center", resizable: false, hidden:true,
                title: '<input type="checkbox" value="" class="delete_checkbox" id="delete_all_checkbox">',
                menuIcon: false,
                cls: 'pq-grid-number-cell', 
                sortable: false,                 
                render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
               
                    return '<input type="checkbox" value="'+rd.voucher_txn_id+'" class="delete_checkbox delete_one_checkbox">';
                }
            },
            { title: "DATE", dataIndx: "txn_date", width: 100,sortable:false },
            <?php if($consoview==1){ ?>
			{ title: "BRANCH", width: 60, dataIndx: "branch_name",sortable:false },
			<?php }if($view==3){  ?>
			 { title: "POS", width: 100, dataIndx: "pos_name",sortable:false }, 
			<?php } ?>
            { title: "NARRATION", width: 150, dataIndx: "long_narration",sortable:false },  
            { title: "DEBIT "+ "("+ SYSTEM_CURRENCY + ")", width: 100, align: "right", dataIndx: "debit",sortable:false },
            { title: "CREDIT "+ "("+ SYSTEM_CURRENCY + ")", width: 100, align: "right", dataIndx: "credit",sortable:false },
			{ title: "AMOUNT "+ "("+ SYSTEM_CURRENCY + ")", width: 150, align: "right", dataIndx: "account_txn_amount",hidden:true,sortable:false },
			{ title: "BALANCE "+ "("+ SYSTEM_CURRENCY + ")", width: 150, align: "right", dataIndx: "balance",sortable:false },
            { title: "", width: 20, dataType: "string", dataIndx: "balance_type",sortable:false },		   
	 	    ];
       
         var loadStateSuccess;
		 if(pageview=='3'){	
		 var minWidth=600;
		 var scrollModel={ autoFit: false };
		 }else{
			var minWidth='flex'; 
			var scrollModel={ autoFit: true };
		 }
		 
		 var dataModel = {
			location: "remote",
			dataType: "json",
			method: "POST",
			postData:{
				from_date:"<?php echo $from_date;?>",
				to_date:"<?php echo $to_date;?>",
				m:"<?php echo $m;?>",
				view:"<?php echo $view;?>",
				id:"<?php echo $account_id;?>",
				consoview:"<?php echo $consoview;?>",
				type:"<?php echo $type;?>"
				},
			url: "<?php echo base_url();?>admin/accounts/ajax_subledger_detail", 
			beforeSend: function (jqXHR, settings) {
			jqXHR.withCredentials = true;
			},
           getData: function (dataJSON) {
            var data = dataJSON.data;
            return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
            }
    };
		
		load_account_totals('<?php echo $account_id;?>','<?php echo $from_date;?>','<?php echo $to_date;?>');
        
        var newObj = {
            scrollModel: scrollModel,
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: "flex",
			minWidth: minWidth,
			selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
            dataReady: calculateSummary,
			columnTemplate: { render: commentRender },
			colModel: colModel,  
			wrap:false,
            numberCell: { show: true },
            filterModel: { on: true,mode: "OR", header: true, type:'remote' },			
            pageModel: { type: "remote", rPP: 100, strRpp: "{0}" },
            editable: false,
            showTitle: false,
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
                            var opts = [];
                             for (var i = 0; i < CM.length; i++) {
                                 var column = CM[i];
                                 var obj = {};
                                 if(column.dataIndx!='' && column.dataIndx!='chkbx' && column.dataIndx!='checkbox' && column.dataIndx!='state'){   
                                obj[column.dataIndx] = column.title;
                                 opts.push(obj);
                                 }
                             }
                             return opts;
                         }
                     },
                     { 
                         type: 'select',                         
                         cls: "filterCondition",
                         listener: filterhandler,
                         options: [
							 { "contain": "Contains" },
                             { "begin": "Begins With" },
                            
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
                     },
					 { 
                        type: 'button', 
						align:'right',						
                        label: '<div id="cfiltermode">Filter Mode : <strong>Current Page</strong></div>',
						style: 'float:right;width:15%;border: none;background-color: floralwhite;',
                        listener: { }
                    }
                ]
            }
        };
        
        newObj.rowDblClick = function(event, ui) {
             
  	            var rowData    = ui.rowData;
				var txn_bo_id = rowData.bo_id;
  	            var col_type = rowData.col_type;
  	            var ajax   = rowData.ajax;
		        var voucher_txn_id   = rowData.voucher_txn_id;
		        var voucher_type_id   = rowData.voucher_type_id;
var 			select_rowindx =  set_page();
                hide_trash_checkbox();
                 $("#grid_search").pqGrid('saveState');
				// edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx);
				 
				
		       
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	        
	             var rowData           = ui.rowData;
		         var txn_bo_id         = rowData.bo_id;
		         var ajax              = rowData.ajax;
		         var col_type          = rowData.col_type;
		         var voucher_txn_id    = rowData.voucher_txn_id;
		         var voucher_type_id   = rowData.voucher_type_id;
		       if (evt.keyCode==13){
                 hide_trash_checkbox();
                 $("#grid_search").pqGrid('saveState');				
                 var select_rowindx =  set_page();
		         // edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx); 
		       }
		   
	       }
        
    var $grid = $("#grid_search").pqGrid(newObj);
	$("#grid_search").pqGrid('loadState');

      $(window).unload( function(){
      $("#grid_search").pqGrid('saveState');
    });
    
/************* Columnar Report     ****************/	  
    $("#total_debit_counter").html('loading...');
	$("#total_credit_counter").html('loading...');
	$("#total_closing_balance").html('loading...');
	async function load_account_totals(account_id,from_date,to_date) {
		 var view   = $('select[name="view"]').val(); 
       let response = await fetch(baseurl+'admin/accounts/LoadAccountSubLedgerTotals?account_id='+account_id+'&from_date='+from_date+'&to_date='+to_date+'&view='+view,{
			method: 'GET', // or 'POST' if needed
			credentials: 'include' 
		});
       let result = await response.json();
       if(result){
		   
          $("#total_debit_counter").html(result.total_debit);
		  $("#total_credit_counter").html(result.total_credit);
		  $("#total_closing_balance").html(result.closing_balance);
         } 
  }
  
 if(pageview==3){	 
	<?php if($all_columns){ ?> 
	var all_columns = <?php echo json_encode($all_columns);?>; 
	<?php }else{ ?>
	var all_columns =[];
	<?php } ?>
	
	colModel[7].hidden=true;
	colModel[8].hidden=true; 
    colModel[9].hidden=false;  	
	var extra_columns=[];
	 $.each(all_columns, function(colindex,col_name){
		var dataindex_key = col_name.replace(/\s/g, ''); 
		colModel.push({title: col_name, dataIndx: dataindex_key+""+colindex, width: 180});
		colModel.push({title: col_name, dataIndx: dataindex_key+""+colindex+"_s", width: 180,hidden:true });
	 });
	 
	 $("#grid_search").pqGrid( "option", "colModel", colModel ); 
	 $("#grid_search").pqGrid("refreshCM");
     $("#grid_search").pqGrid("refresh"); 	 

 }else{ 	
 
	if(consoview==1){
	 colModel[7].hidden=false;
	 colModel[7].width=150;	 
	 colModel[8].hidden=false;
	 colModel[8].width=150;
	 colModel[9].hidden=true; 	
	 colModel[9].width=150;	
	 $("#grid_search").pqGrid( "option", "colModel", colModel ); 
	$("#grid_search").pqGrid("refreshCM");
    $("#grid_search").pqGrid("refresh");
	}else{
		
		colModel[6].hidden=false;
	colModel[6].width=150;
	colModel[7].hidden=false;
	colModel[7].width=150;
	colModel[8].hidden=true; 	
	colModel[8].width=150;	
	
$("#grid_search").pqGrid( "option", "colModel", colModel ); 
	$("#grid_search").pqGrid("refreshCM");
    $("#grid_search").pqGrid("refresh");	 
	}		
	
 }




/************* Columnar Report     ****************/	  

  
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
					
            }
			return 0;
        } 
    }
     
    });
    
  $(document).on('change', '#fg', function(){
    if($(this).is(":checked")) {
      $("#grid_search").pqGrid('option', 'height', 420);
    }
    else{
      $("#grid_search").pqGrid('option', 'height', 'flex');
    }
    $("#grid_search").pqGrid('refreshDataAndView');
  });

  /* $(document).on('change', '#consoview', function(){
    if($(this).is(":checked")) {
      var curentview = $('select[name="view"]').val();
      if(curentview==1)	  
	    window.location.href=baseurl+"/admin/accounts/ledger_detail/<?php echo $account_id;?>?from_date=<?php echo $from_date;?>&to_date=<?php echo $to_date;?>&consoview=1&view=1";
      else
         window.location.href=baseurl+"/admin/accounts/ledger_detail/<?php echo $account_id;?>?from_date=<?php echo $from_date;?>&to_date=<?php echo $to_date;?>&consoview=1&view=0";
      		  
    }
    else{
      window.location.href=baseurl+"/admin/accounts/ledger_detail/<?php echo $account_id;?>?from_date=<?php echo $from_date;?>&to_date=<?php echo $to_date;?>&consoview=0&view=0";
    }
    
  }); */
		
function getParameterByName(name, url = window.location.href) {
		name = name.replace(/[\[\]]/g, '\\$&');
		var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
			results = regex.exec(url);
		if (!results) return '';
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, ' '));
	}

function print_excel(){
	return false;
	var from_date = $('#from_date').val(); 
	var to_date   = $('#to_date').val();
	var view   = $('select[name="view"]').val(); 
	var m      = $('#m').prop('checked') ? 1 : 0;
	var consoview      = $('#consoview').prop('checked') ? 1 : 0;
	if(consoview==0 ){
	if(view == 0){
		var stringparameters = "view="+view+"&accid=<?php echo $account_id;?>&m="+m+"&from_date="+from_date+"&to_date="+to_date;
		window.location.href= baseurl+"/admin/export/account_ledger?"+stringparameters;
	}
	if( view == 3){
		var stringparameters = "view="+view+"&accid=<?php echo $account_id;?>&m="+m+"&from_date="+from_date+"&to_date="+to_date;
		window.location.href= baseurl+"/admin/export/account_ledger_condensed_columnar?"+stringparameters;
	}	
	if(view == 1){
		var stringparameters = "view="+view+"&m="+m+"&from_date="+from_date+"&to_date="+to_date;
		window.location.href= baseurl+"/admin/export/account_ledger_detailed/<?php echo $account_id;?>?"+stringparameters;
	}
	
	}
	if(consoview==1 ){
		if(view == 0){
		var stringparameters = "view="+view+"&accid=<?php echo $account_id;?>&m="+m+"&from_date="+from_date+"&to_date="+to_date;
		window.location.href= baseurl+"/admin/export/account_ledger_condensed_conso?"+stringparameters;
	}
	if(view == 1){
		var stringparameters = "view="+view+"&accid=<?php echo $account_id;?>&m="+m+"&from_date="+from_date+"&to_date="+to_date;
		window.location.href= baseurl+"/admin/export/account_ledger_detailed_conso/<?php echo $account_id;?>?"+stringparameters;
	}
	}
			
        }
		
		
		</script>	<style>.hidden{display:none;}</style></body></html>