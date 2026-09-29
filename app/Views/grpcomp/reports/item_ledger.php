<?php $header = array(  'title' => 'Item Ledger' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>
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

<div class="row pb-2 align-items-center">
    <div class="col-sm-6">
        <h3>Item Ledger</h3>
    </div>
    <div class="col-sm-6 text-end">
        <div class="taskmenus">
            <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
            <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a>
            <a href="#"><span class="material-symbols-outlined">print</span></a>
            <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_pdf();">Document</a></li>
            </ul>
            <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">share</span></a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Facebook</a></li>
                <li><a class="dropdown-item" href="#">Twitter</a></li>
                <li><a class="dropdown-item" href="#">Instagram</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="row mb-4 align-items-top">
<div class="col-md-5">
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
</div>
<div class="col-md-7">
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
        <li><a class="dropdown-item" href="#">Vouch No</a></li>
        <li><a class="dropdown-item" href="#">Optional Voucher </a></li>
        <li><a class="dropdown-item" href="#">Cancelled Voucher</a></li>
        <li><a class="dropdown-item" href="#">Post dated Voucher </a></li>
        <li><a class="dropdown-item" href="#">Narration</a></li>
        <li><a class="dropdown-item" href="#">Quantity</a></li>
        <li><a class="dropdown-item" href="#">Rate</a></li>
        <li><a class="dropdown-item" href="#">Value</a></li>
        <li><a class="dropdown-item" href="#">Gross value for outward</a></li>
        <li><a class="dropdown-item" href="#">Gross Value For Inward</a></li>
        <li><a class="dropdown-item" href="#">Gross Profit</a></li>
    </ul>
    <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success ms-2">« Back</a>

    </div>
    <div class="dropdown float-end">

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
        <label class="form-check-label" for="BalanceCheck">Balance</label>
    </div>
    <div class="form-check d-inline-block me-2">
        <input class="form-check-input" type="checkbox" value="1" id="PnLCheck">
        <label class="form-check-label" for="PnLCheck">Profit</label>
    </div>

    <div class="dropdown d-inline-block" style="width:220px;">
      <div class="input-group">
          <span class="input-group-text">Valuation</span>
          <select form="salefrm" name="val" id="val" class="form-select" onchange="this.form.submit()">

            <?php foreach ($valuation_list as $key => $value) {  ?>
                <option value="<?= $key ?>" <?= ($key==$val)?"selected":"";?> ><?= $value ?></option>
            <?php } ?>
             
           </select>
      </div>  
    </div>
</div>
</div>
</div>
<div class="row">
<div class="col-md-6">
<p><em>Item: <strong><?php echo $item_name;?></strong></em></p>
</div>
<div class="col-md-6 text-end pe-3">
  
  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#openingBalanceModel">
      Opening Balance
  </button>

</div>
</div>


<div id="grid_search" style="margin:auto;"> </div>

<div class="row">
    <div class="col-md-12 text-end pe-3">

    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#totalAmountModel">
          Total Amount
      </button>

    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#closingBalanceModel">
          Closing Balance
    </button>
 
</div>
</div>

<div class="modal fade modal-lg" id="calendarmodal" tabindex="-1" aria-labelledby="calendarmodallabel" style="display: none;" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    
    <div class="col-12 calccard card m-auto">
      <form class="form" method="get" id="salefrm2" autocomplete="off">
        <input type="hidden" name="val"  value="<?php echo $val; ?>">

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
                            <option value="0">All Units</option>
                        <?php if($item_unit_list){ ?>
                        <?php foreach($item_unit_list as $key =>$value){ ?>
                            <option <?= $value['id'] == $unit_id ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['label'] ?></option>
                        <?php }  ?>
                        <?php }  ?>
                   </select>
               </div>
            </div>
            

            <div class="row p-4">
           
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
                        <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:68%; text-align: center; display: block;"></button>
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
                    <div class="row">
                      <div class="col-md-6 d-grid p-2" >
                          <span class="fw-bold d-inline-block px-4">
                              <input class="form-check-input mt-1 me-1" type="checkbox" value="1" id="tillperiod"> 
                              TILL PERIOD
                          </span>
                      </div>
                      <div class="col-md-6 text-end">
                          <button type="button" class="input-group-text fw-bold ms-auto"  id="tilldate" required>TILL DATE</button>
                      </div>
                  </div>
                    
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
                        <th style="text-align: center;">Company</th>
                        <th style="text-align: center;">Material Center</th>
                        <th style="text-align: center;">Unit</th>
                        <th style="text-align: center;">Quantity</th>
                        <th style="text-align: center;">Value</th>
                        <th style="text-align: center;">Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($opening_balance_list){ ?>
                    <?php foreach($opening_balance_list as $key =>$value){ ?>

                        <tr>
                            <td><?= $value['comp_name'] ?></td>
                            <td><?= $value['mc_name'] ?></td>
                            <td><?= $value['unit_name'] ?></td>
                            <td style="text-align: right;"><?= $value['balance'] ?></td>
                            <td style="text-align: right;"><?= $value['item_value'] ?></td>
                            <td><?= $value['method'] ?></td>
                        </tr>
                    
                    <?php }  ?>
                    <?php }  ?>

                    
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
                    <?php if($closing_balance_list){ ?>
                    <?php foreach($closing_balance_list as $key =>$value){ ?>

                        <tr>
                            <td><?= $value['mc_name'] ?></td>
                            <td><?= $value['unit_name'] ?></td>
                            <td style="text-align: right;"><?= $value['balance'] ?></td>
                            <td style="text-align: right;"><?= $value['item_value'] ?></td>
                            <td><?= $value['method'] ?></td>
                        </tr>
                    
                    <?php }  ?>
                    <?php }  ?>

                    
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
                        <th style="text-align: center;">Company</th>
                        <th style="text-align: center;">Material Center</th>
                        <th style="text-align: center;">Unit</th>
                        <th style="text-align: center;">Qty In</th>
                        <th style="text-align: center;">Amt In</th>

                        <th style="text-align: center;">Qty Out</th>
                        <th style="text-align: center;">Amt Out</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($amount_list){ ?>
                    <?php foreach($amount_list as $key =>$value){ ?>

                        <tr <?= ($value['item_id'] == 0) ? 'class="table-secondary"' : ''; ?>>

                            <td><?= $value['comp_name'] ?></td>
                            <td><?= $value['mc_name'] ?></td>
                            <td><?= $value['unit_name'] ?></td>

                            <td style="text-align: right;"><?= $value['debit_qty'] ?></td>
                            <td style="text-align: right;"><?= $value['debit_amount'] ?></td>

                            <td style="text-align: right;"><?= $value['credit_qty'] ?></td>
                            <td style="text-align: right;"><?= $value['credit_amount'] ?></td>
                       
                        </tr>
                    
                    <?php }  ?>
                    <?php }  ?>

                    
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
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="18">Sales Invoice</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="19">Sales Order</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="2">Credit Note</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="7">Delivery Challan</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="17">Quotations</a>
            </div>
          </div>
          <div class="col-md-6">
            <h4>Purchases-</h4>
            <div class="list-group">
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="11">Purchase Invoice</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="12">Purchase Order</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="3">Debit Note</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="6">Inward Challan</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="21">Purchase Requisition</a>
            </div>
          </div>


        </div>

        <div class="row p-2">
          <div class="col-md-6">
            <h4>Banking-</h4>
            <div class="list-group">
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="9">Payments</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="13">Receipts</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="1">Contra</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="5">Journal</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="8">Memorandum</a>
            </div>
          </div>
          <div class="col-md-6">
            <h4>Items-</h4>
            <div class="list-group">
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="15">Stock Transfer</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="10">Physical Verification</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="14">Production Voucher</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="20">Stock Journal</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="4">Consignment Packing</a>
            </div>
          </div>

        </div>

      </div>

    </div>
  </div>
</div>

<?php echo view('includes/'.$folder_path.'footer_scripts'); ?>

<script>

    $(document).keydown(function(e) {
        if (e.which == 45) { 
            $('#voucherCreationModal').modal('show');
        }
    });

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

    var item_list = <?= json_encode($items_dropdown) ?>;

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
        var html = `<option value="0">All Units</option>`;
        $('select[name="unit_id"]').html(html);
    }

    function get_item_units(item_id)
    {  
        reset_item_units();

        if(item_id)
        {
            $.ajax({
                url: '<?php echo $base_url; ?>ajax/get_item_unit_list', 
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
                            var html = `<option value="0">All Units</option>`;
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
        var inwardQtyTotoal = 0,
            inwardAmountTotoal = 0,
            outwardQtyTotoal = 0,
            outwardAmountTotoal = 0,
            // balanceQtyTotoal = 0,
            // balanceAmountTotoal = 0,
            pnlTotal = 0,
            data = this.option('dataModel.data'),
            len = data.length;


        data.forEach(function(row){
            
            inwardQtyTotoal += parseQty(row.inward_qty);
            inwardAmountTotoal += parseAmount(row.inward_amount);
            outwardQtyTotoal += parseQty(row.outward_qty);
            outwardAmountTotoal += parseAmount(row.outward_amount);
            // balanceQtyTotoal += parseQty(row.balance_qty);
            // balanceAmountTotoal += parseValue(row.balance_amount);
            pnlTotal += parseAmount(row.profit);
           
        })

        var totalData = {

                comp_name: "Total",
                voucher_date: 'empty',
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
            
            // console.log(totalData);

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
       


        var dataModel = { data: <?= json_encode($transactions) ?>};

        var colModel = [
          { title: "COMPANY", dataIndx: "comp_name", width: 50,hidden:false},
          { title: "DATE", dataIndx: "voucher_date", width: 50,hidden:false,
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.voucher_date == 'empty'){
                  return '';
                }
                var arr = rd.voucher_date.split("-");
                return arr[2]+'-'+arr[1]+'-'+arr[0];  
              
            }
          },
            
             { title: "PARTICULARS", width: 200, dataIndx: "particulars",hidden:false},
             // { title: "VCH No.", width: 100, dataIndx: "voucher_no",hidden:false},   
             // { title: "VCH TYPE", width: 100, dataIndx: "voucher_type",hidden:false},
             { title: "VOUCHER", width: 100, dataIndx: "voucher",hidden:false},
             

             { title: "INWARD", width: 100, dataType: "float",dataIndx: "inward_qty",hidden:false,
                 render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.item_txn_drcr != 'c')
                        return formatQty(rd.inward_qty);
                    else 
                        return '';   

                },},
             { title: "INWARD AMOUNT", width: 100, align: "right", dataIndx: "inward_amount",hidden:true,
                 render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.item_txn_drcr != 'c')
                        return formatAmount(rd.inward_amount);
                    else 
                        return '';   

                },},

             { title: "OUTWARD", width: 100, dataType: "float", dataIndx: "outward_qty",hidden:false,
                 render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.item_txn_drcr != 'd')
                        return formatQty(rd.outward_qty);
                    else 
                        return '';    

                },},
             { title: "OUTWARD AMOUNT", width: 100, align: "right",  dataIndx: "outward_amount",hidden:true,
                 render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.item_txn_drcr != 'd')
                        return formatAmount(rd.outward_amount);
                    else 
                        return ''; 

                },},

             
             { title: "CLOSING", width: 100, dataType: "float", dataIndx: "balance_qty",hidden:false,
                 render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.balance_qty != '')
                        return formatQty(rd.balance_qty);
                    return '';  
                },},
             { title: "UOM", width: 80, dataIndx: "unit_name",hidden: false},
             { title: "BALANCE", width: 100, align: "right", dataType: "string",dataIndx: "balance_amount",hidden:true,
                 render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.balance_amount != '')
                        return formatValue(rd.balance_amount);
                    return '';

                },
            },
            { title: "Profit", width: 100, align: "right", dataType: "string",dataIndx: "profit",hidden:true,
                 render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.item_txn_drcr != 'd')
                        return formatAmount(rd.profit);   
                    else 
                        return ''; 
                },
            },
            { title: "Profit String", width: 100, align: "right", dataType: "string",dataIndx: "profit_string",hidden:true},
            ];
        var newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 450,//'flex',
            selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
            dataReady: calculateSummary,
            colModel: colModel,  
            numberCell: { show: true },
            pageModel: { type: 'local' },
            filterModel: { mode: 'OR', type: "local" },
            editable: false,
            showTitle: false,            
            create: function (evt, ui) {// make first row auto selected
             var grid = this,
                $select_row = $(".select-row"),
                data = ui.dataModel.data;
                grid.setSelection({ rowIndx: 0, focus: true });
                
                
                
            },
            load:function(event,ui) {
               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
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
                            var opts = [{ '': '[ All Fields ]'}];
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
                            { "begin": "Begins With" },
                            { "contain": "Contains" },
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
          var rd    = ui.rowData;
          var voucher_txn_id    = rd.voucher_txn_id;
          var voucher_type_id   = rd.voucher_type_id;
          var comp_id           = rd.comp_id;

          edit_voucher(voucher_txn_id, voucher_type_id, comp_id);
        }
         
        newObj.cellKeyDown= function(evt, ui) {

          var rd    = ui.rowData;
          var voucher_txn_id    = rd.voucher_txn_id;
          var voucher_type_id   = rd.voucher_type_id;
          var comp_id           = rd.comp_id;
          if (evt.keyCode==13){
            edit_voucher(voucher_txn_id, voucher_type_id, comp_id);
          }
        }
        
        var grid = $("#grid_search").pqGrid(newObj);
     
        $("#InwardAmountCheck").click(function(evt){
            var val = evt.target.checked;
            if(val==true){


              colModel[5].hidden=false;
              colModel[5].width= 200;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
             
              $("#grid_search").pqGrid( {autoFit: true} );
              $("#grid_search").pqGrid("refreshCM");
               $("#grid_search").pqGrid("refresh");
              
            }
          else{
              colModel[5].hidden=true;
              colModel[5].width= 100;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
               $("#grid_search").pqGrid("refreshCM");
               $("#grid_search").pqGrid("refresh");
             }
        });
        
        $("#OutwardAmountCheck").click(function(evt){
            var val = evt.target.checked;
            if(val==true){
              colModel[7].hidden=false;
              colModel[7].width= 200;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
             
              $("#grid_search").pqGrid( {autoFit: true} );
              $("#grid_search").pqGrid("refreshCM");
              $("#grid_search").pqGrid("refresh");
            }
          else{
              colModel[7].hidden=true;
              colModel[7].width= 100;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
               $("#grid_search").pqGrid("refreshCM");
               $("#grid_search").pqGrid("refresh");
             }
        });
        
        $("#BalanceCheck").click(function(evt){
            var val = evt.target.checked;
            if(val==true){
              colModel[10].hidden=false;
              colModel[10].width= 200;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
             
              $("#grid_search").pqGrid( {autoFit: true} );
              $("#grid_search").pqGrid("refreshCM");
              $("#grid_search").pqGrid("refresh");
            }
          else{
              colModel[10].hidden=true;
              colModel[10].width= 100;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
               $("#grid_search").pqGrid("refreshCM");
               $("#grid_search").pqGrid("refresh");
             }
        });
        $("#PnLCheck").click(function(evt){
            var val = evt.target.checked;
            if(val==true){
              colModel[12].hidden=false;
              colModel[12].width= 200;
              colModel[11].hidden=false;
              colModel[11].width= 200;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
             
              $("#grid_search").pqGrid( {autoFit: true} );
              $("#grid_search").pqGrid("refreshCM");
              $("#grid_search").pqGrid("refresh");
            }
          else{
              colModel[12].hidden=true;
              colModel[12].width= 100;
              colModel[11].hidden=true;
              colModel[11].width= 100;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
               $("#grid_search").pqGrid("refreshCM");
               $("#grid_search").pqGrid("refresh");
             }
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

function print_excel()
        {
            var item_id     = <?= $item_id ?>; 
            var from_date   = <?= $from_date ?>; 
            var to_date     = <?= $to_date ?>;  
            var unit_id     = <?= $unit_id ?>;
            var mc_id       = <?= $mc_id ?>;
            var mc_grp_id   = <?= $mc_grp_id ?>;
            var val_id      = <?= $val_id ?>;  
         
                
            var stringparameters = "export_type=excel&item_id="+item_id+"&from_date="+from_date+"&to_date="+to_date+"&unit_id="+unit_id+"&mc_id="+mc_id+"&mc_grp_id="+mc_grp_id+"&val_id="+val_id;
            
            window.location.href= baseurl+"/admin/export/item_ledger?"+stringparameters;
           
        }
function print_csv()
        {
           var item_id     = <?= $item_id ?>; 
            var from_date   = <?= $from_date ?>; 
            var to_date     = <?= $to_date ?>;  
            var unit_id     = <?= $unit_id ?>;
            var mc_id       = <?= $mc_id ?>;
            var mc_grp_id   = <?= $mc_grp_id ?>;
            var val_id      = <?= $val_id ?>;  
         
                
            var stringparameters = "export_type=csv&item_id="+item_id+"&from_date="+from_date+"&to_date="+to_date+"&unit_id="+unit_id+"&mc_id="+mc_id+"&mc_grp_id="+mc_grp_id+"&val_id="+val_id;
            
            window.location.href= baseurl+"/admin/export/item_ledger?"+stringparameters;
           
        }       
          </script>
</body></html>