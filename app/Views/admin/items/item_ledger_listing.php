<?php $header = array(  'title' => 'Item Ledger' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
.myform .col-12{padding:6px 0px;}
.myform label{width:25%; float:left;}
.myform .form-control, .myform select {width:75%;}
</style>

<style>
/*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
}
</style>
<style>
.dropdown-menu li {
position: relative;
}
.dropdown-menu .dropdown-submenu {
display: none;
position: absolute;
left: 100%;
top: -10px;
}
.dropdown-menu .dropdown-submenu-left {
display: none;
position: absolute;
right: 100%;
top: -10px;
}
.dropdown-menu > li:hover > .dropdown-submenu {
display: block;
}
.dropdown-menu > li:hover > .dropdown-submenu-left {
display: block;
}
.pq-sb-horiz-t .pq-sb-slider, .pq-sb-vert-t .pq-sb-slider, .pq-sb-horiz-t .pq-sb-btn, .pq-sb-vert-t .pq-sb-btn{background-color: rgb(220, 254, 211) !important;}
</style>

<?php 
$params = http_build_query([
  'item_id'               => $item_id,
  'from_date'             => $from_date,
  'to_date'               => $to_date,
  'unit_id'               => $unit_id,
  'mc_grp_id'             => $mc_grp_id,
  'mc_id'                 => $mc_id
]);
?>


<div class="row pb-2 align-items-center">
    <div class="col-sm-6">
        <h3>Item Ledger</h3>
    </div>
    <div class="col-sm-6 text-end">
        <div class="taskmenus">
            <a href="javascript:void(0);" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
            <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a>
            <a href="<?= base_url() ?>admin/export/item_ledger_print?<?= $params ?>"><span class="material-symbols-outlined ">print</span></a>
            <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_pdf();">Document</a></li>
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

<div class="row mb-4 align-items-top">
<div class="col-md-6 py-3">
<form class="form needs-validation" method="get" id="salefrm"  novalidate>
    <input type="hidden" name="item_id"  value="<?php echo $item_id ?>">
    <input type="hidden" name="unit_id"  value="<?php echo $unit_id; ?>">
    <input type="hidden" name="mc_id"  value="<?php echo $mc_id; ?>">
    <input type="hidden" name="mc_grp_id"  value="<?php echo $mc_grp_id; ?>">

    <input type="hidden" name="module" value="Item Account">
    <div class="input-group">
        <span class="input-group-text px-1">From</span>
        <input type="text" name="from_date" value="<?= $from_date ?>" class="datepicker form-control p-2" required  style="width:90px;">
        
        <span class="input-group-text px-1">To</span>
        <input type="text" name="to_date" value="<?= $to_date ?>" class="datepicker form-control p-2" required style="width:90px;">
        
        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#calendarmodal">
        <span class="material-symbols-outlined">event</span>
        </button>
        
        <input type="submit" class="btn btn-sm btn-success" value="GO">
    </div>
</form>
 <div class="form-check d-inline-block py-3 me-2">
        <input class="form-check-input" type="checkbox" value="1" id="InwardRateCheck">
        <label class="form-check-label" for="InwardRateCheck">Inward Rate</label>
    </div>
	 <div class="form-check d-inline-block me-2">
        <input class="form-check-input" type="checkbox" value="1" id="OutwardRateCheck">
        <label class="form-check-label" for="OutwardRateCheck">Outward Rate</label>
    </div>
    <div class="form-check d-inline-block me-2">
        <input class="form-check-input" type="checkbox" value="1" id="InwardAmountCheck">
        <label class="form-check-label" for="InwardAmountCheck">Inward Amount</label>
    </div>
    <div class="form-check d-inline-block me-2">
        <input class="form-check-input" type="checkbox" value="1" id="OutwardAmountCheck">
        <label class="form-check-label" for="OutwardAmountCheck">Outward Amount</label>
    </div>
    <div class="form-check d-inline-block me-2">
        <input class="form-check-input" type="checkbox" value="1" id="BalanceCheck">
        <label class="form-check-label" for="BalanceCheck">Value</label>
    </div>
    <div class="form-check d-inline-block me-2">
        <input class="form-check-input" type="checkbox" value="1" id="PnLCheck">
        <label class="form-check-label" for="PnLCheck">Profit</label>
    </div>
</div>
<div class="col-md-6">
<div class="dropdown float-end">
    
    
    <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">View</button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Stock Query </a></li>
        <li><a class="dropdown-item" href="#">Daily breakup</a></li>
        <li><a class="dropdown-item" href="#">Monthly Summary</a></li>
        <li><a class="dropdown-item" href="#">Movement Analysis</a></li>
    </ul>
    <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">Add On</button>
    <ul class="dropdown-menu">
        <li><a data-type="item" class="dropdown-item view_computation" href="javascript:void(0);">View Computation</a></li>
    </ul>
    <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success ms-2">« Back</a>

    </div>
    <div class="dropdown float-end">
     
 
  <div class="dropdown d-inline-block me-1 py-3" style="width:220px;">
		
          <div class="input-group input-group-sm input-group">
              <span class="input-group-text">View</span>
              <select form="salefrm" class="form-select" id="view" name="view">
                   <option value=""></option>
				  <option <?= ($view==0) ? 'selected' : '' ?> value="0">Condensed</option>
                  <!--<option <?= ($view==1) ? 'selected' : '' ?> value="1">Detailed</option>
				  <option <?= ($view==3) ? 'selected' : '' ?> value="3">Columnar Report</option>-->
              </select>
              
          </div>  
        </div>
    <div class="dropdown d-inline-block" style="width:220px;">
      <div class="input-group">
          <span class="input-group-text">Valuation</span>
          <select form="salefrm" name="val" id="val" class="form-select" onchange="this.form.submit()">

            <?php foreach ($valuation_list as $key => $value) {  ?>
                <option value="<?= $value ?>" <?= ($value==$valuation_id)?"selected":"";?> ><?= $value ?></option>
            <?php } ?>
             
           </select>
      </div>  
    </div>
	
</div>
</div>
</div>
<div class="row">
<div class="col-md-6">
<p><em>Item: <strong><?php echo $item_name;?></strong>(<strong><?php echo $unit_name;?></strong>)</em></p>
</div>
<div class="col-md-6 text-end py-3">
 <button id="totalopeningbtn" type="button" class="btn btn-success btn-sm">
          Opening Balance
      </button>
</div>

</div>


<div id="grid_search" style="margin:auto;"> </div>

<div class="row">
    <div class="col-md-12 text-end py-3">
 
    <button id="totalamountbtn" type="button" class="btn btn-success btn-sm">
          Total Amount
      </button> 
	  <button id="totalclosingbtn" type="button" class="btn btn-success btn-sm">
          Closing Balance
      </button>


</div>
</div>

<div class="modal fade modal-lg" id="calendarmodal" tabindex="-1" aria-labelledby="calendarmodallabel" style="display: none;" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    
    <div class="col-12 calccard card m-auto">
        <form class="form" method="get" id="salefrm2" autocomplete="off">
            <input type="hidden" name="val"  value="<?php echo $val ?? 0; ?>">

            <div class="row p-1">
                <div class="col-md-6 mt-2" id="mc_crteria">
                   <label> Criteria</label>
                   <select name="mc_type"  id="mc_type" class="form-select">
                        <option value="all_mc">All MC</option>
                        <option value="one_mc" <?= $mc_id != 0 && $mc_grp_id == 0 ? 'selected' : '' ?>>One MC</option>
                        <option value="mc_group" <?= $mc_id == 0 && $mc_grp_id != 0 ? 'selected' : '' ?>>Mc Group</option>
                    </select>
               </div>
                <div class="col-md-6 mt-2" id="mc_div" <?= $mc_id != 0 && $mc_grp_id == 0 ? '' : 'style="display:none;"' ?>>
                    <label> MC </label>
                    <?php echo form_dropdown('mc_id', $matrcntr_dropdown, $mc_id,' id="mat_cent_id" class="form-select"'); ?>
                </div>
                <div class="col-md-6 mt-2" id="mc_grp_div" <?= $mc_id == 0 && $mc_grp_id != 0 ? '' : 'style="display:none;"' ?>>
                    <label> Mc Group</label>
                    <?php echo form_dropdown('mc_grp_id', $matrcntr_grp_dropdown, $mc_grp_id,' id="mat_cent_grpid" class="form-select"'); ?>
                </div>
            </div>

            <div class="row p-1">
                <div class="col-md-6 mt-2">
                    <label> Item</label>
                    <input id="item" type="text" class="form-control borderdark" value="<?php echo $item_name ?>" required>
                    <input type="hidden" name="item_id" value="<?php echo $item_id ?>">

               </div>
               <div class="col-md-6 mt-2">
                   <label>UOM</label>
                   <select name="unit_id" class="form-control" style="font-size:18px;border-radius: 6px 0px 0px 6px;">
                        <?php if($item_unit_list){ ?>
                        <?php foreach($item_unit_list as $key =>$value){ ?>
                            <option <?= $value['unit_id'] == $unit_id ? 'selected' : '' ?> value="<?= $value['unit_id'] ?>"><?= $value['unit_name'] ?></option>
                        <?php }  ?>
                        <?php }  ?>
                   </select>
               </div>
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
                                <input type="text" class="form-control" fdprocessedid="cw7ydk" name="from_date" id="fromdate" value="<?php echo $from_date;?>" placeholder="dd-mm-yyyy" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row align-items-center my-2">
                        <div class="col-md-2 fw-bold pe-0">To</div>
                        <div class="col-md-10">
                            <div class="calc-inputgroup">
                                <input type="text" class="form-control"  fdprocessedid="b20o4z" name="to_date" id="todate" value="<?php echo $to_date;?>" placeholder="dd-mm-yyyy" required>
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



<!-- The Modal -->
<div class="modal fade" id="openingBalanceModel">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Opening Balances</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">

                <thead>
                    <tr>
                        <th style="text-align: center;">Material Center</th>
                        <th style="text-align: center;">Unit</th>
                        <th style="text-align: center;">Quantity</th>
                        <th style="text-align: center;">Value</th>
                        <th style="text-align: center;">Method</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
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

<!-- The Modal -->
<div class="modal fade" id="closingBalanceModel">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Closing Balances</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">

                <thead>
                    <tr>
                        <th style="text-align: center;">Material Center</th>
                        <th style="text-align: center;">Unit</th>
                        <th style="text-align: center;">Quantity</th>
                        <th style="text-align: center;">Value</th>
                        <th style="text-align: center;">Method</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
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

<!-- The Modal -->
<div class="modal fade" id="totalAmountModel">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Total Amount</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">

                <thead>
                    <tr>
                        <th style="text-align: center;">Material Center</th>
                        <th style="text-align: center;">Unit</th>
                        <th style="text-align: center;">Qty In</th>
                        <th style="text-align: center;">Amt In</th>
                        <th style="text-align: center;">Qty Out</th>
                        <th style="text-align: center;">Amt Out</th>
                    </tr>
                </thead>
                <tbody>                                     
                </tbody>
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



<!-- The Modal -->
<div class="modal fade" id="ValuationLogModal">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
       
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
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
<div class="modal fade" id="VoucherValuationModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
 <input type="hidden" name="vchfrm_itemid" id="vchfrm_itemid" value="">
   <input type="hidden" name="vchfrm_unitid" id="vchfrm_unitid" value="">	
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Calculate Valuation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
	
  <div class="form-group row"><br></div>

   <div class="form-group row" id="prgrloader" style="display:none;">
        <p class="overall_status">Total Records:0</p>
        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>
		<div class="text-center my-2 current_status" style="height:20px">			
		</div>
     </div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	  <button type="button" class="btn btn-success" id="refreshbtn" style="display:none;">Refresh</button>&nbsp;
	    <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>


<?php echo view('includes/footer_scripts'); ?>
<script>
var item_id    ='<?php echo $item_id;?>';
var pageview   ='<?php echo $view;?>';
var mc_id   ='<?php echo $mc_id;?>';
var unit_id   ='<?php echo $unit_id;?>';

var from_date   ='<?php echo $from_date;?>';
var to_date   ='<?php echo $to_date;?>';
var itmnameunitname = '<strong><?php echo $item_name;?></strong>(<strong><?php echo $unit_name;?></strong>)';


/************** Start Of  View Computation   ***************************/
$(document).on("click",".view_computation",function(){
	var data = $("#grid_search").pqGrid("selection", { type:'row', method:'getSelection'});
      console.log(data);
	  return false;
	  
	  if (Array.isArray(data) && data.length > 0) {
		 var rowData = data[0].rowData;

		// Extract variables
		var voucher_txn_id = rowData.voucher_txn_id;  // "4"
		var txn_id         = rowData.txn_id;          // "19"
		var valuation_log  = rowData.valuation_log;
		var voucherno      = rowData.voucher;

		// Access the log
		var log = (valuation_log && valuation_log[txn_id] && valuation_log[txn_id][voucher_txn_id]) 
			? valuation_log[txn_id][voucher_txn_id] 
			: "";
		if(log==''){
			 alert_notification("Log not exists.");
			 return false;
		 }
		 else{
		     $("#ValuationLogModal .modal-title").html('<pre style="white-space: pre-wrap;">'+itmnameunitname+"-"+voucherno+ '</pre>');
            // Insert log in modal body, preserve line breaks
            $("#ValuationLogModal .modal-body").html('<pre style="white-space: pre-wrap;">' + log + '</pre>');
            // Show modal using Bootstrap
            $("#ValuationLogModal").modal('show');	 
		 }
		  
	  } else{
		  alert_notification("Select row first to check computation log");
		  return false;
		  
	  }
	
});

/************** End Of  View Computaion   ***************************/



/************** Start Of  Show Total Amount Button   ***************************/
$(document).on("click","#totalamountbtn",function(){
	show_loader();
	fetchItemSummaryTotals(item_id,from_date, to_date, mc_id, unit_id);
});

async function fetchItemSummaryTotals(item_id,from_date, to_date, mc_id, unit_id) {
    try {
        const response = await fetch(`<?php echo base_url();?>admin/items/get_item_summary_totals/${item_id}?from_date=${from_date}&to_date=${to_date}&mc_id=${mc_id}&unit_id=${unit_id}`);
        const result = await response.json();
		stop_loader();
        if (result.status === 'success') {
            populateTotalAmountModal(result.data);
            $('#totalAmountModel').modal('show');  // Show the modal
        } else {
            console.error('Failed to fetch summary totals');
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}
function populateTotalAmountModal(data) {
    let tbody = $('#totalAmountModel tbody');
    tbody.empty();  // Clear existing rows

    // Group data by mat_cent_name
    let groupedData = data.reduce((acc, row) => {
        if (!acc[row.mat_cent_name]) {
            acc[row.mat_cent_name] = [];
        }
        acc[row.mat_cent_name].push(row);
        return acc;
    }, {});

    // Iterate grouped data and build rows
    for (const [matCentName, rows] of Object.entries(groupedData)) {
        rows.forEach((row, index) => {
            let tr = '<tr>';
            
            // Add MC name with rowspan only on first unit of each MC
            if (index === 0) {
                tr += `<td style="text-align: center;" rowspan="${rows.length}">${matCentName}</td>`;
            }

            tr += `
                <td style="text-align: center;">${row.unit_name}</td>
                <td style="text-align: right;">${parseAmount(row.total_in_qty)}</td>
                <td style="text-align: right;">${formatAmount(row.total_in_amount)}</td>
                <td style="text-align: right;">${parseAmount(row.total_out_qty)}</td>
                <td style="text-align: right;">${formatAmount(row.total_out_amount)}</td>
            </tr>`;

            tbody.append(tr);
        });
    }
}
/**************  End Of  Show Total Amount Button   ***************************/

/************** Start Of  Show Total Opening Button   ***************************/
$(document).on("click","#totalopeningbtn",function(){
	show_loader();
	var valuation_method = $("#val").val();
	fetchItemOpeningTotals(item_id,from_date, to_date, mc_id, unit_id,valuation_method);
}); 

async function fetchItemOpeningTotals(item_id,from_date, to_date, mc_id, unit_id,valuation_method) {
    try {
        const response = await fetch(`<?php echo base_url();?>admin/items/get_item_opening_totals/${item_id}?from_date=${from_date}&to_date=${to_date}&mc_id=${mc_id}&unit_id=${unit_id}&val_id=${valuation_method}`);
        const result = await response.json();
		stop_loader();
        if (result.status === 'success') {
            populateOpeningModal(result.data);
            $('#openingBalanceModel').modal('show');  // Show the modal
        } else {
            console.error('Failed to fetch summary totals');
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}
function populateOpeningModal(data) {
    let tbody = $('#openingBalanceModel tbody');
    tbody.empty();  // Clear existing rows

    // Group data by mat_cent_name
    let groupedData = data.reduce((acc, row) => {
        if (!acc[row.mat_cent_name]) {
            acc[row.mat_cent_name] = [];
        }
        acc[row.mat_cent_name].push(row);
        return acc;
    }, {});

    // Iterate grouped data and build rows
    for (const [matCentName, rows] of Object.entries(groupedData)) {
        rows.forEach((row, index) => {
            let tr = '<tr>';
            
            // Add MC name with rowspan only on first unit of each MC
            if (index === 0) {
                tr += `<td style="text-align: center;" rowspan="${rows.length}">${matCentName}</td>`;
            }
            tr += `
                <td style="text-align: center;">${row.unit_name}</td>
                <td style="text-align: right;">${parseAmount(row.opening_qty)}</td>
                <td style="text-align: right;">${formatAmount(row.opening_valuation_value)}</td>
                <td style="text-align: right;">${row.valuation_method_name}</td>
            </tr>`;

            tbody.append(tr);
        });
    }
}
/**************  End Of  Show Opening Balance Button   ***************************/

/************** Start Of  Show Total Closing Button   ***************************/
$(document).on("click","#totalclosingbtn",function(){
	show_loader();
	var valuation_method = $("#val").val();
	fetchItemClosingTotals(item_id,from_date, to_date, mc_id, unit_id,valuation_method);
}); 

async function fetchItemClosingTotals(item_id,from_date, to_date, mc_id, unit_id,valuation_method) {
    try {
        const response = await fetch(`<?php echo base_url();?>admin/items/get_item_closing_totals/${item_id}?from_date=${from_date}&to_date=${to_date}&mc_id=${mc_id}&unit_id=${unit_id}&val_id=${valuation_method}`);
        const result = await response.json();
		stop_loader();
        if (result.status === 'success') {
            populateClosingModal(result.data);
            $('#closingBalanceModel').modal('show');  // Show the modal
        } else {
            console.error('Failed to fetch summary totals');
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}
function populateClosingModal(data) {
    let tbody = $('#closingBalanceModel tbody');
    tbody.empty();  // Clear existing rows

    // Group data by mat_cent_name
    let groupedData = data.reduce((acc, row) => {
        if (!acc[row.mat_cent_name]) {
            acc[row.mat_cent_name] = [];
        }
        acc[row.mat_cent_name].push(row);
        return acc;
    }, {});

    // Iterate grouped data and build rows
    for (const [matCentName, rows] of Object.entries(groupedData)) {
        rows.forEach((row, index) => {
            let tr = '<tr>';
            
            // Add MC name with rowspan only on first unit of each MC
            if (index === 0) {
                tr += `<td style="text-align: center;" rowspan="${rows.length}">${matCentName}</td>`;
            }
            tr += `
                <td style="text-align: center;">${row.unit_name}</td>
                <td style="text-align: right;">${parseAmount(row.closing_qty)}</td>
                <td style="text-align: right;">${formatAmount(row.closing_value)}</td>
                <td style="text-align: right;">${row.method_name}</td>
            </tr>`;

            tbody.append(tr);
        });
    }
}
/**************  End Of  Show Closing Balance Button   ***************************/

function refresh_grid(){
	   var pq_grids = $('.pq-grid'); 
      if(pq_grids.length > 0){
        $.each(pq_grids, function(index, pq_grid){

          var grid_id = $(pq_grid).attr('id');
          localStorage.removeItem("pq-grid"+grid_id);
   
          $(pq_grid).pqGrid( "reset", { group: true, filter: true, sort: true } );

            var CM = $(pq_grid).pqGrid('option', 'colModel');
            for(var i=0, len = CM.length; i < len; i++){
                var column = CM[i];
                if(column.filter){
                    column.filter.value = null;
                    column.filter.value2 = null;
                    column.filter.cache = null;
                }
            }
            
            $(pq_grid).pqGrid('filter', {
              oper: 'replace',
              data: []
            });
            $('.filterValue').val('');
            $(pq_grid).pqGrid('refreshHeader');

            $(pq_grid).pqGrid( "setSelection", { rowIndx: 0 });

        })
      }
}



 $(document).on('change', 'select[name="mc_type"]', function(){
        var type = $(this).val();
        $('#mc_div').css('display', 'none');
        $('#mc_grp_div').css('display', 'none');

        if(type == 'one_mc'){
            $('#mc_div').css('display', 'block');
        }
        if(type == 'mc_group'){
            $('#mc_grp_div').css('display', 'block');
        }
    });

    var item_list = <?= $items_dropdown ?>;

    getList(item_list);

    function getList(list) 
    {
        $('input[id="item"]').off('blur'); // unbind event first
        
        if($('input[id="item"]').hasClass('ui-autocomplete-input')) {
            $('input[id="item"]').autocomplete("destroy");
        }
        
        $('input[id="item"]').autocomplete({
            source: list,
            minLength: 0,
            select: function( event, ui ) {
                $(this).val(ui.item.label);
                $('input[name="item_id"]').val(ui.item.item_id);
                get_item_units(ui.item.item_id);
            }
        })
        .on('focus', function(){
            $(this).autocomplete( "search", "" );
            $('input[id="item"]').val('');
            $('input[name="item_id"]').val('');
            reset_item_units();
        })
        .on('blur', function(){
            autoSetItem(list);
        })
    }

    function autoSetItem(list)
    {
        if($('input[id="item"]').val() != '' && $('input[name="item_id"]').val() == '')
        {
            var acc = $('input[id="item"]').val();
            var index = list.findIndex(function(obj) {
                var string = obj.label.toLowerCase();
                var text = acc.toLowerCase();
               return  string.includes(text);
            });
            if(index > -1){
                $('input[id="item"]').val(list[index].label);
                $('input[name="item_id"]').val(list[index].item_id);
                get_item_units(list[index].item_id);
            }
            else{
                $('input[id="item"]').val('');
                $('input[name="item_id"]').val('');
           
            }
        }
        if($('input[name="item_id"]').val() == '')
        {
            $('input[id="item"]').val('');
            $('input[name="item_id"]').val('');
            
        }
    }

    function reset_item_units()
    {
        var html = ``;
        $('select[name="unit_id"]').html(html);
    }

    function get_item_units(item_id)
    {  
        reset_item_units();

        if(item_id)
        {
            $.ajax({
                url: '<?php echo $base_url; ?>items/ajax_get_item_units_list', 
                type: 'POST',
                data: {item_id: item_id},
                dataType: "json",
                beforeSend: function() {
                    
                },
                success: function (response) {
                 
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    if(response.status){
                        if(response.list.length > 0){
                            var html = '';
                            $.each(response.list, function(index, obj){
                                html += `<option value="${obj.unit_id}">${obj.unit_name}</option>`;
                            });
                            $('select[name="unit_id"]').html(html);
                        }
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

     $(function () {         
         function calculateSummary() { 		 
                const url = new URL(window.location.href);
                if(url.searchParams.has('rowIndx')){
                    var rowIndx = url.searchParams.get('rowIndx');
                    url.searchParams.delete('rowIndx');
                    window.history.replaceState(null, null, url);
                    this.setSelection({ rowIndx: parseInt(rowIndx), focus: true });
                }
                else{
                    this.setSelection({ rowIndx: this.rowIndxOffset, focus: true });
                } 
				
        var inwardQtyTotoal = 0,
            inwardAmountTotoal = 0,
            outwardQtyTotoal = 0,
            outwardAmountTotoal = 0,          
            pnlTotal = 0,
            data = this.option('dataModel.data'),
            len = data.length;
        data.forEach(function(row){            
            inwardQtyTotoal += parseQty(row.inward_qty);
            inwardAmountTotoal += parseAmount(row.inward_amount);
            outwardQtyTotoal += parseQty(row.outward_qty);
            outwardAmountTotoal += parseAmount(row.outward_amount);
            pnlTotal += parseAmount(row.profit);           
        })

        var totalData = {
                voucher_date: "Total",
                item_txn_drcr: '',
                inward_qty: inwardQtyTotoal,
                inward_amount : inwardAmountTotoal,
                outward_qty: outwardQtyTotoal,
                outward_amount : outwardAmountTotoal,
                balance_qty: '',
                balance_amount : '',
                profit : pnlTotal,
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
       
        function sanitize(s){           // → a safe dataIndx (no spaces, commas…)
    return s.replace(/[^a-z0-9_]/gi,'');
}

/* hold the last extra-column list so we know if we must rebuild cm */
let lastExtraCols = [];

/*********************************************************************/
/* 2.  remote datamodel                                              */
/*********************************************************************/
var dataModel = {
    location : "remote",
    dataType : "json",
    method   : "POST",
    postData : {  // your existing postData params
        from_date : "<?php echo $from_date; ?>",
        to_date   : "<?php echo $to_date;   ?>",
        item_id   : "<?php echo $item_id;   ?>",
        unit_id   : "<?php echo $unit_id;   ?>",
        mc_id     : "<?php echo $mc_id;     ?>",
        mc_grp_id : "<?php echo $mc_grp_id; ?>",
        val_id    : "<?php echo $valuation_id;?>",
        view      : pageview
    },
    url : "<?php echo base_url(); ?>admin/items/ajax_item_ledger",

    /*****************************************************************/
    /* 3.  transform the payload before pqGrid consumes it           */
    /*****************************************************************/
    getData : function (resp) {

        /* ---------- 1) make sure we have the extra-column list */
        const extraCols = resp.extra_columns || [];

        /* ---------- 2) if the set changed, rebuild the colModel */
        if ( JSON.stringify(extraCols) !== JSON.stringify(lastExtraCols) ) {

            /* start with whatever static columns you already had */
            const baseCols = [
             { title: "DATE", dataIndx: "voucher_date", width: 120, hidden:false },
             { title: "VCH TYPE", width: 120, dataIndx: "voucher_type",hidden:false},			
			 { title: "VOUCHER", width: 100, dataIndx: "voucher",hidden:false},
             { title: "INWARD", width: 130, dataType: "float",dataIndx: "inward_qty",hidden:false},
			 { title: "OUTWARD", width: 130, dataType: "float", dataIndx: "outward_qty",hidden:false},
			 { title: "CLOSING", width: 130, dataType: "float", dataIndx: "balance_qty",hidden:false},
			 { title: "UOM", width: 100, dataIndx: "unit_name",hidden: false}			 
            ];

            /* add a qty column for each extra item */
            extraCols.forEach((name, idx) => {
    const key = sanitize(name) + '_' + idx;

    baseCols.push({
        title     : name,
        dataIndx  : key,
        width     : 160,
        align     : 'right',
        summary   : { type: 'sum' },

        /* use render – this can be a function */
        render    : function (ui) {
            return ui.cellData !== '' ? ui.cellData : '';
        }

        // If you only need a number mask, replace render with:
        // format : "##,##0.###"        // <- string, no function
    });
});

            $("#grid_search").pqGrid("option", "colModel", baseCols);
            $("#grid_search").pqGrid("refreshCM");     // re-draw header
            lastExtraCols = extraCols.slice(0);        // remember
        }

        /* ---------- 3) flatten each row’s extra_columns object */
        resp.data.forEach(row => {
            const extras = row.extra_columns || {};
            extraCols.forEach((name, idx) => {
                const key = sanitize(name)+'_'+idx;
                row[key]  = extras.hasOwnProperty(name) ? extras[name] : '';
            });
            delete row.extra_columns;   // pqGrid shouldn't see the object
        });

        /* ---------- 4) hand modified payload to pqGrid */
        return {
            curPage      : resp.curPage,
            totalRecords : resp.totalRecords,
            data         : resp.data
        };
    }
};

        var colModel = [
             { title: "DATE", dataIndx: "voucher_date", width: 90, hidden:false },
             { title: "VCH TYPE", width: 100, dataIndx: "voucher_type",hidden:false},			
			 { title: "VOUCHER", width: 100, dataIndx: "voucher",hidden:false},
             { title: "INWARD", width: 100, dataType: "float",dataIndx: "inward_qty",hidden:false,
                 render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.inward_qty!='' && rd.item_txn_drcr != 'c')
                        return formatQty(rd.inward_qty);
                    else 
                        return '';   

                },},
				{ title: "INWARD RATE", width: 100, align: "right", dataIndx: "inward_rate",hidden:true,
                 render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.inward_rate!='' && rd.item_txn_drcr != 'c')
                        return formatAmount(rd.inward_rate,'',4);
                    else 
                        return '';   

                },},
             { title: "INWARD AMOUNT", width: 100, align: "right", dataIndx: "inward_amount",hidden:true,
                 render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.inward_amount!='' && rd.item_txn_drcr != 'c')
                        return formatAmount(rd.inward_amount);
                    else 
                        return '';   

                },},

             { title: "OUTWARD", width: 100, dataType: "float", dataIndx: "outward_qty",hidden:false,
                 render: function( ui ) {
                    var rd = ui.rowData;
					if(rd.voucher_type_id=='20'){ //Stock Journal show both in and out qty 
						if(rd.outward_qty!='')
                        return formatQty(rd.outward_qty);
                    else 
                        return ''; 
					}else{
					if(rd.outward_qty!='' && rd.item_txn_drcr != 'd')
                        return formatQty(rd.outward_qty);
                    else 
                        return '';  	
					}
                      

                },},
				
				{ title: "OUTWARD RATE", width: 100, align: "right",  dataIndx: "outward_rate",hidden:true,
                 render: function( ui ) {
                    var rd = ui.rowData;
					if(rd.voucher_type_id=='20'){
					if(rd.outward_rate!='')
                        return formatAmount(rd.outward_rate,'',4);
                    else 
                        return ''; 	
					}
					else{
						if(rd.outward_rate!='' && rd.item_txn_drcr != 'd')
                        return formatAmount(rd.outward_rate,'',4);
                    else 
                        return ''; 
					}
                    

                },},
				
             { title: "OUTWARD AMOUNT", width: 100, align: "right",  dataIndx: "outward_amount",hidden:true,
                 render: function( ui ) {
                    var rd = ui.rowData;
					if(rd.voucher_type_id=='20'){
					if(rd.outward_amount!='')
                        return formatAmount(rd.outward_amount);
                    else 
                        return ''; 
	
					}
					else{
						if(rd.outward_amount!='' && rd.item_txn_drcr != 'd')
                        return formatAmount(rd.outward_amount);
                    else 
                        return ''; 

					}
                    
                },},

             
             { title: "CLOSING QTY", width: 100, dataType: "float", dataIndx: "balance_qty",hidden:false,
                 render: function( ui ) {
                    var rd = ui.rowData;                   
                    return rd.balance_qty;
                     
                },},
             { title: "UOM", width: 80, dataIndx: "unit_name",hidden: false},
             { title: "VALUATION", width: 100, align: "right", dataType: "string",dataIndx: "balance_amount",hidden:true,
                 render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.balance_amount != '')
                        return rd.balance_amount;
                    return '';

                },
            },
            { title: "Profit", width: 100, align: "right", dataType: "string",dataIndx: "profit",hidden:true,
                 render: function( ui ) {
                    var rd = ui.rowData;
                    return rd.profit;   
                   
                },
            },
            { title: "Profit String", width: 100, align: "right", dataType: "string",dataIndx: "profit_string",hidden:true},
            { title: "COGS", width: 100, align: "right", dataType: "string",dataIndx: "valuation_calc"},
            
			];
		
		if(pageview=='3'){	
		 var minWidth=600;
		 var scrollModel={ autoFit: false };
		 }else{
			var minWidth='flex'; 
			var scrollModel={ autoFit: true };
		 }
        var newObj = {
            scrollModel: scrollModel,
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: "flex",
			minWidth: minWidth,
			resizable: true,
            autoResize: true,
		    width:'100%',
            selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
            dataReady: calculateSummary,
            colModel: colModel,  
            numberCell: { show: true },
            filterModel: { on: true, mode: "OR", header: true, type:'remote' },
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            editable: false,
            wrap:false,
            showTitle: false,         
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
                            var opts = [];
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
                    }
                ]
            }
        };
        
        
        newObj.rowDblClick = function(event, ui) {
                var rowData           = ui.rowData;
                var voucher_txn_id    = rowData.voucher_txn_id;
                var voucher_type_id   = rowData.voucher_type_id;
            
              
                $("#grid_search").pqGrid('saveState');
				 var select_rowindx =  set_page();
				edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx);
               
                
         }
         
        newObj.cellKeyDown= function(evt, ui) {
                var rowData           = ui.rowData;               
                var voucher_txn_id    = rowData.voucher_txn_id;
                var voucher_type_id   = rowData.voucher_type_id;
               if (evt.keyCode==13){                   
               $("#grid_search").pqGrid('saveState');
				 var select_rowindx =  set_page();
				edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx);
               }
           
           }
        
        var $grid = $("#grid_search").pqGrid(newObj);
		
		/************* Columnar Report     ****************/	  

 if(pageview==3){	 
	<?php if($all_columns){ ?> 
	var all_columns = <?php echo json_encode($all_columns);?>; 
	<?php }else{ ?>
	var all_columns =[];
	<?php } ?>
	 	
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
	$("#grid_search").pqGrid( "option", "colModel", colModel ); 
		$("#grid_search").pqGrid("refreshCM");
		$("#grid_search").pqGrid("refresh");
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
            }if(url.searchParams.has('duplc')){
                url.searchParams.delete('duplc');
                window.history.replaceState(null, null, url);  
            }
			return 0;
        }  		
    }
	// Generic function to toggle grid column visibility
    function toggleGridColumn(checkboxSelector, columnIndex) {
        $(checkboxSelector).on('change', function(evt) {
            var checked = evt.target.checked;
            colModel[columnIndex].hidden = !checked;
            colModel[columnIndex].width = checked ? 200 : 100;

            $("#grid_search").pqGrid("option", "colModel", colModel);
            $("#grid_search").pqGrid({ autoFit: true });
            $("#grid_search").pqGrid("refreshCM");
            $("#grid_search").pqGrid("refresh");
        });
    }

    // Apply toggles for individual checkboxes
    toggleGridColumn("#InwardAmountCheck", 5);
    toggleGridColumn("#InwardRateCheck", 4);
    toggleGridColumn("#OutwardAmountCheck", 8);
    toggleGridColumn("#OutwardRateCheck", 7);
    toggleGridColumn("#BalanceCheck", 11);
    toggleGridColumn("#ValCalcCheck", 14);

    // Special case for PnL checkbox
    $("#PnLCheck").on('change', function(evt) {
        var checked = evt.target.checked;
        colModel[12].hidden = !checked;
        colModel[12].width  = checked ? 200 : 100;
        colModel[13].hidden = true;  // Always hidden as per original logic
        colModel[13].width  = 100;
        $("#grid_search").pqGrid("option", "colModel", colModel);
        $("#grid_search").pqGrid({ autoFit: true });
        $("#grid_search").pqGrid("refreshCM");
        $("#grid_search").pqGrid("refresh");
       });   
    
    });
 		
function getParameterByName(name, url = window.location.href) {
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return '';
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }
</script>
</body></html>