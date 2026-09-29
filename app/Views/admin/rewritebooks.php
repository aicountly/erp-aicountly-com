<?php $header = array( 	'title' => 'Rewrite Books' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
.myform .col-12{padding:6px 0px;}
.myform label{width:25%; float:left;}
.myform .form-control, .myform select {width:75%;}


.update_fy_balance {
  min-width: 200px;
  width: 100%;
  max-width: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  white-space: nowrap;
  text-align: center;
  padding: 0.5rem 1rem;
  box-sizing: border-box;
}

.row.m-2 {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}


</style>
<div class="row">
	<div class="col-6">
		<h3 class="pb-3">Rewrite Books</h3>
	</div>
	<div class="col-md-6 text-end">
		<div class="taskmenus">
			<a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
				<span class="material-symbols-outlined">offline_bolt</span>
			</a>
			<a href="#">
				<span class="material-symbols-outlined">print</span>
			</a>
			<a href="#" data-bs-toggle="dropdown" aria-expanded="false">
				<span class="material-symbols-outlined">
					<span class="material-symbols-outlined">download</span>
				</span>
			</a>
			<ul class="dropdown-menu">
				<li>
					<a class="dropdown-item" href="#">CSV</a>
				</li>
				<li>
					<a class="dropdown-item" href="#">Excel</a>
				</li>
				<li>
					<a class="dropdown-item" href="#">Document</a>
				</li>
			</ul>
			<a href="#" data-bs-toggle="dropdown" aria-expanded="false">
				<span class="material-symbols-outlined">share</span>
			</a>
			<ul class="dropdown-menu">
				<li>
					<a class="dropdown-item" href="#">Facebook</a>
				</li>
				<li>
					<a class="dropdown-item" href="#">Twitter</a>
				</li>
				<li>
					<a class="dropdown-item" href="#">Instagram</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="col-6">
		

	</div>
	<div class="col-6 text-end">
		<a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-outline-success btn-sm">« Back</a>
	</div>
</div>
<!--
<h4>Update Balances</h4>
<div class="row m-2">

	<button data-type="account" class="btn btn-success btn-sm mx-2 update_balance" style="max-width: 200px">Account Balances</button>
	<button data-type="bill_sundry" class="btn btn-success btn-sm mx-2 update_balance" style="max-width: 200px">Bill Sundry Balances</button>
	<button data-type="bill_by_bill" class="btn btn-success btn-sm mx-2 update_balance" style="max-width: 200px">Bill By Bill Balances</button>
	<button data-type="cost_center" class="btn btn-success btn-sm mx-2 update_balance" style="max-width: 200px">Cost Centre Balances</button>
	<button data-type="item" class="btn btn-success btn-sm mx-2 update_balance" style="max-width: 200px">Item Balances</button>
	<button data-type="project" class="btn btn-success btn-sm mx-2 update_balance" style="max-width: 200px">Project Balances</button>
	
</div>
<br>
<h4>Reporting Tables</h4>
<div class="row m-2">
	<button data-type="item" class="btn btn-success btn-sm mx-2 clear_reporting_tables" style="max-width: 200px">Clear Inventory Reports</button>
	
</div>
<br>

<h4>Calculate Valuation</h4>
<div class="row m-2">
	<button id="avgbtn" data-method_id="1" data-type_name="AVG" class="btn btn-success btn-sm mx-2 calc_valuation_process" style="max-width: 200px">AVG</button>
	<button id="fifobtn" data-method_id="2" data-type_name="FIFO" class="btn btn-success btn-sm mx-2 calc_valuation_process" style="max-width: 200px">FIFO</button>
	<button id="lifobtn" data-method_id="3" data-type_name="LIFO" class="btn btn-success btn-sm mx-2 calc_valuation_process" style="max-width: 200px">LIFO</button>
	<button id="defaultbtn" data-method_id="0" data-type_name="Default" class="btn btn-success btn-sm mx-2 calc_valuation_process" style="max-width: 200px">DEFAULT</button>
	
</div>
<br>


<h4>Diagnostic Tools</h4>
<div class="row m-2">
   <button id="voucher_verification" class="btn btn-success btn-sm mx-2" style="max-width: 200px">Voucher Verification</button>

	<button id="bbb_voucher_verification" class="btn btn-success btn-sm mx-2" style="max-width: 200px">Bill By Bill Voucher Verification</button>

	<button id="db_repair" class="btn btn-success btn-sm mx-2" style="max-width: 200px">Repair Database</button>

</div>
<div class="row m-2">

	<button data-type="account" class="btn btn-success btn-sm mx-2 master_varification" style="max-width: 200px">Account Masters</button>
	<button data-type="bill_sundry" class="btn btn-success btn-sm mx-2 master_varification" style="max-width: 200px">Bill Sundry Masters</button>
	<button data-type="item" class="btn btn-success btn-sm mx-2 master_varification" style="max-width: 200px">Item Masters</button>
	<button data-type="item" class="btn btn-success btn-sm mx-2 item_rep_calculation" style="max-width: 200px">Item TXN REP CALC</button>

</div>
<br>

<h4>Carryforward FY Masters</h4>

<div class="row m-2">
	<button data-type="branch" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Branch</button>
	<button data-type="currency" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Currency</button>
	<button data-type="voucher_series" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Voucher Series</button>
</div>

<div class="row m-2">
	
	<button data-type="account_group" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Account Groups</button>
	<button data-type="account" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Accounts</button>
	<button data-type="bill_sundry" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Bill Sundry</button>
	<button data-type="project_group" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Project Groups</button>
	<button data-type="project" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Projects</button>
	
</div>

<div class="row m-2">
	<button data-type="item_group" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Item Groups</button>
	<button data-type="item_category" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Item Categories</button>
	<button data-type="item" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Items</button>
	<button data-type="item_batch" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Item Batch</button>
	<button data-type="unit" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Units</button>
	
</div>

<div class="row m-2">
	<button data-type="mc_group" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>MC Groups</button>
	<button data-type="mc" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Material Center</button>
	<button data-type="barcode" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Barcode</button>
	<button data-type="label" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Labels</button>
	<button data-type="gsttinmaster" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>TIN Masters</button>
	
</div>

<div class="row m-2">
	<button data-type="cost_center_group" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Cost Center Groups</button>
	<button data-type="cost_center" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Cost Center</button>
	<button data-type="bill_by_bill" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Bill By Bill</button>
	<button data-type="bill_of_material" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>BOM</button>
	<button data-type="print" class="btn btn-success btn-sm mx-2 update_fy_masters" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Prints</button>

</div>

<br>
-->
<h4>Carryforward FY Balances</h4>
<div class="row m-2">
	<button data-type="account" class="btn btn-success btn-sm mx-2 update_fy_balance" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Account Balances</button>
	<!--<button data-type="bill_sundry" class="btn btn-success btn-sm mx-2 update_fy_balance" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Bill Sundry Balances</button>
	<button data-type="bill_by_bill" class="btn btn-success btn-sm mx-2 update_fy_balance" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Bill By Bill Balances</button>
	<button data-type="cost_center" class="btn btn-success btn-sm mx-2 update_fy_balance" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Cost Centre Balances</button>
	<button data-type="project" class="btn btn-success btn-sm mx-2  update_fy_balance" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Project Balances</button>
	-->
	
	<button data-type="item" class="btn btn-success btn-sm mx-2 update_fy_balance" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Item Balances</button>
	
    <button data-type="item_valuation" class="btn btn-success btn-sm mx-2 update_fy_valuation" style="max-width: 200px" <?= ($NextFyExists>0) ? '' : 'disabled' ?>>Item Valuation</button>

</div>


<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bbbVoucherVerificationModal">
  Open modal
</button> -->

<!-- The Modal  DEfault Valuation -->
<div class="modal fade" id="updateDFValuationModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Calculate Valuation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
	  	<p class="overall_status">Total Accounts: 25</p>

        <div class="progress"  style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Capital Account)
		</div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	     <button type="button" class="btn btn-success" id="refreshbtndf" style="display:none;">Refresh</button>&nbsp;
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>
<div class="modal fade" id="updateValuationModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Calculate Valuation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
	  	<p class="overall_status">Total Accounts: 25</p>

        <div class="progress"  style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Capital Account)
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


<div class="modal fade" id="updateBalancesModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Updating Account Balances</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Accounts: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Capital Account)
		</div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	  <button type="button" class="btn btn-success" id="refreshbtn_bal" data-ids="0" style="display:none;">Refresh</button>&nbsp;
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="voucherVerificationModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Voucher Verification</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
      	<button class="btn btn-danger btn-sm float-end" id="delete_all_verf_vch">Delete All</button>
      	
      		<p class="overall_status">Total Voucher Types: 25</p>
      		
      	
      	

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Sales)
		</div>

		<div>
			<p>Faults: <span id="faults">0</span></p>
			<ul class="list-group voucher_list" id="voucher_verification_list" style="max-height: 200px;overflow-y: auto">
			  
			</ul>
		</div>	

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	    <button type="button" class="btn btn-success" id="refreshbtn_vch_crfy" style="display:none;">Refresh</button>&nbsp;
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="bbbVoucherVerificationModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Bill By Bill Voucher Verification</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Accounts: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated progress-bar-main" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Sundry Creditors)
		</div>

		<p class="overall_sub_status">Total Vouchers: 215</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated progress-bar-sub" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_sub_status" style="height:20px">
			101 / 215 (Sales 22)
		</div>

		<div>
			<p>Faults: <span id="bbb_faults">0</span></p>
			<ul class="list-group voucher_list" id="bbb_voucher_verification_list" style="max-height: 200px;overflow-y: auto">
			  
			</ul>
		</div>	

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>


<!-- The Modal -->
<div class="modal fade" id="DoubleValueVerificationModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Calculate Valuation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Vouchers: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated progress-bar-main" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Sundry Creditors)
		</div>

		<p class="overall_sub_status">Total Records: 215</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated progress-bar-sub" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_sub_status" style="height:20px">
			101 / 215 (Sales 22)
		</div>

		
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	  <button type="button" class="btn btn-success" id="refreshbtn_dbl_val" style="display:none;">Refresh</button>&nbsp;
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="dbRepairModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Database Repair</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Tables: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25
		</div>

		<div>
			<p>Faults: <span id="faults">0</span></p>
			<ul class="list-group" id="db_repair_list" style="max-height: 200px;overflow-y: auto">
			  
			</ul>
		</div>	

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	    <button type="button" class="btn btn-success" id="refreshbtndb" style="display:none;">Refresh</button>&nbsp;
	   
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="ClearRepotingTablesModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Clear Inventory Reports</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Tables: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25
		</div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	    <button type="button" class="btn btn-success" id="refreshbtnrpt" style="display:none;">Refresh</button>&nbsp;
	   
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="masterVerificationModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Master Verification</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Masters: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Sales)
		</div>

		<div>
			<p>Faults: <span class="faults">0</span></p>
			<ul class="list-group voucher_list" id="master_verification_list" style="max-height: 200px;overflow-y: auto">
			  
			</ul>
		</div>	

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="updateFYMasterModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Update Masters</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Masters: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
		</div>
			
			<p>Errors: <span class="faults">0</span></p>
			<ul class="list-group voucher_list" id="fy_masters_list" style="max-height: 200px;overflow-y: auto">

    </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="updateFYBalancesModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Updating Account Balances</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Accounts: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Capital Account)
		</div>

			<p>Errors: <span class="faults">0</span></p>
			<ul class="list-group voucher_list" style="max-height: 200px;overflow-y: auto">

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>


<!-- The Modal -->
<div class="modal fade" id="updateFYValuationModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Updating Item Valuation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Accounts: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Capital Account)
		</div>

			<p>Errors: <span class="faults">0</span></p>
			<ul class="list-group voucher_list" style="max-height: 200px;overflow-y: auto">

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<?php echo view('includes/footer_scripts'); ?>
<script src="<?php echo base_url();?>/public/js/jquery.progressBarTimer.js" type="text/javascript" charset="utf-8">
</script>
<script>
$(document).ready(function() {
	$('#updateBalancesModal').modal({backdrop: 'static', keyboard: false});
	$('#updateValuationModal').modal({backdrop: 'static', keyboard: false});
	$('#DoubleValueVerificationModal').modal({backdrop: 'static', keyboard: false});
});

/**************/

var get_containers_xhr;
var containers_list  = [];
var containers_index = 0;
$(document).on('click', '.calc_valuation_process', function(){
	containers_list = [];
	containers_index = 0;	
	var method_id = $(this).data('method_id');	
	
	
	$('#DoubleValueVerificationModal .close_btn').text('Cancel');
    $('#DoubleValueVerificationModal .close_btn').removeClass('btn-success').addClass('btn-danger');
	$('#DoubleValueVerificationModal .overall_status').text('Please wait while details are being fetched...');
	$('#DoubleValueVerificationModal .current_status').text('');
	$('#DoubleValueVerificationModal .overall_sub_status').text('');
	$('#DoubleValueVerificationModal .current_sub_status').text('');
	$('#DoubleValueVerificationModal .progress-bar').css('width', '0%');
	$('#DoubleValueVerificationModal .progress-bar').text('0%');
	$('#DoubleValueVerificationModal').modal('show');
	get_containers_xhr = $.ajax({
        url: baseurl+'/admin/rewritebooks/load_container_details', 
        type: 'POST',
        data: {"method_id":method_id},
        dataType: "json",
        beforeSend: function() {},
        success: function (container_response) {            
            if(container_response.status){
                containers_list = container_response.list;
                //console.table(containers_list);
                var overall_sub_status = 'Total Containers: ' + containers_list.length;
                $('#DoubleValueVerificationModal .overall_sub_status').text(overall_sub_status);
                load_container_vouchers();
            }
            else{
            	$('#DoubleValueVerificationModal .overall_sub_status').text('Something Went wrong');
            	$('#DoubleValueVerificationModal .current_sub_status').text(container_response.message);
            }
        },
        complete: function() {},
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
            alert(error);
        },
    });
});

var container_vouchers_xhr;
var container_vouchers_list = [];
var container_vouchers_index = 0;

function load_container_vouchers(){
	container_vouchers_list = [];
	container_vouchers_index = 0;

	if(containers_index < containers_list.length)
	{
		var current_status = `${(containers_index+1)} / ${containers_list.length} (Container: ${containers_list[containers_index].name})`;
		$('#DoubleValueVerificationModal .current_sub_status').text(current_status);
		$('#DoubleValueVerificationModal .overall_status').text('Please wait while details are being fetched...');
		$('#DoubleValueVerificationModal .progress-bar-sub').css('width', '0%');
		$('#DoubleValueVerificationModal .progress-bar-sub').text('0%'); 
		container_vouchers_xhr = $.ajax({
	        url: baseurl+'/admin/rewritebooks/get_container_vouchers_listing', 
	        type: 'POST',
	        data: {"item_id_unit_id": containers_list[containers_index].item_id_unit_id,"method_id":containers_list[containers_index].method_id},
	        dataType: "json",
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            
	            if(response.status){
	                container_vouchers_list = response.list;
	                var overall_status = 'Total Records: ' + container_vouchers_list.length;
	                $('#DoubleValueVerificationModal .overall_status').text(overall_status);
	              
	               verify_bbbval_vouchers();
	          
	    			
	            }
	            
	            
	        },
	        complete: function() {
	            // stop_loader();
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
	            alert(error);
	        },
	    });
	}
	else
	{
		$('#DoubleValueVerificationModal .current_sub_status').text('All container processed');

		$('#DoubleValueVerificationModal .progress-bar-sub').css('width', '100%');
		$('#DoubleValueVerificationModal .progress-bar-sub').text('100%');

        $('#DoubleValueVerificationModal .close_btn').text('Done');
        $('#DoubleValueVerificationModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}


var verify_bbbval_vouchers_xhr;

function verify_bbbval_vouchers()
{	

if(container_vouchers_list.length >0){
	var progress = Math.round((container_vouchers_index/container_vouchers_list.length)*100);
	$('#DoubleValueVerificationModal .progress-bar-main').css('width', progress+'%');
	$('#DoubleValueVerificationModal .progress-bar-main').text(progress+'%');
	
}

    if(container_vouchers_index < container_vouchers_list.length)
	{
	
		var current_status = `${(container_vouchers_index+1)} / ${container_vouchers_list.length} (${container_vouchers_list[container_vouchers_index].voucher_name})`;
		$('#DoubleValueVerificationModal .current_status').text(current_status);

		verify_bbbval_vouchers_xhr = $.ajax({
	        url: baseurl+'/admin/rewritebooks/verify_item_valuation', 
	        type: 'POST',
	        data: {method_id: container_vouchers_list[container_vouchers_index].method_id, voucher_txn_id:container_vouchers_list[container_vouchers_index].voucher_txn_id,itmiduntid: container_vouchers_list[container_vouchers_index].itmiduntid},
	        dataType: "json",
			timeout: 10000, // 10 secods
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            
	            if(response.status){
	                container_vouchers_index++;
	                verify_bbbval_vouchers();
	            }	            
	        },
	        complete: function() {
	            // stop_loader();
	        },
	        error: function (jqXHR, exception) {

	            var error_= '';
	            if (jqXHR.status == 404) {
	                error = 'Requested page not found. [404]';
	            } else if (jqXHR.status == 500) {
	                error = 'Internal Server Error [500].';
	            } else if (exception === 'parsererror') {
	                error = 'Requested JSON parse failed.';
	            } else if (exception === 'timeout') {
					var getlast_index = $("#DoubleValueVerificationModal #refreshbtn_dbl_val").attr("data-ids");
					$("#DoubleValueVerificationModal #refreshbtn_dbl_val").attr("data-ids",container_vouchers_index);								
					$("#DoubleValueVerificationModal #refreshbtn_dbl_val").show();
	                error = 'Time out error.';
	            } else if (jqXHR.status === 0 && exception === 'abort') {
	                error = 'Ajax request aborted.';
	            } else if (jqXHR.status === 0) {
					var getlast_index = $("#DoubleValueVerificationModal #refreshbtn_dbl_val").attr("data-ids");
					$("#DoubleValueVerificationModal #refreshbtn_dbl_val").attr("data-ids",container_vouchers_index);								
					$("#DoubleValueVerificationModal #refreshbtn_dbl_val").show();
	                error = 'Not connect.\n Verify Network.';
	            } else {
	                error = 'Uncaught Error.\n' + jqXHR.responseText;
	            }
	            alert(error);
	        },
	    });
	}
	else{
		
		containers_index++;	   
		load_container_vouchers();
		
        $('#DoubleValueVerificationModal .current_status').text("Valuation calculated");
	var progress2 = Math.round((containers_index / containers_list.length) * 100);
	
	  $('#DoubleValueVerificationModal .progress-bar-sub').css('width', progress2+'%');
	$('#DoubleValueVerificationModal .progress-bar-sub').text(progress2+'%');
	}


	
}

$(document).on("click","#refreshbtn_dbl_val", function(){
	var dd_val_index = $(this).data("id");
	
	verify_bbbval_vouchers(dd_val_index);
	$(this).hide();
	
});

$("#DoubleValueVerificationModal").on('hide.bs.modal', function () {
	if(get_containers_xhr)
		get_containers_xhr.abort();
	if(container_vouchers_xhr)
		container_vouchers_xhr.abort();  
    if(verify_bbbval_vouchers_xhr)	
    	verify_bbbval_vouchers_xhr.abort();
});





/*****************/
var list = [];
var index = 0;
var type = '';

function get_modal_title(type)
{
	var title = '';
	if(type == 'account')
		title = 'Updating Account Balances';
	if(type == 'bill_sundry')
		title = 'Updating Bill Sundry Balances';
	if(type == 'bill_by_bill')
		title = 'Updating Bill By Bill Balances';
	if(type == 'cost_center')
		title = 'Updating Cost Center Balances';
	if(type == 'item')
		title = 'Updating Item Balances';
    if(type == 'item_valuation')
		title = 'Updating Item Valuation';
	if(type == 'item_rep_cal')
		title = 'Updating Item TXN REP CALC';
	return title;
}

var get_details_xhr;
$(document).on('click', '.update_balance', function(){
	type = $(this).data('type');
	list = [];
	index = 0;
	$('#updateBalancesModal .modal-title').text(get_modal_title(type));
	$('#updateBalancesModal .close_btn').text('Cancel');
    $('#updateBalancesModal .close_btn').removeClass('btn-success').addClass('btn-danger');
	$('#updateBalancesModal .overall_status').text('Please wait while details are being fetched...');
	$('#updateBalancesModal .current_status').text('');
	$('#updateBalancesModal .progress-bar').css('width', '0%');
	$('#updateBalancesModal .progress-bar').text('0%');
	$('#updateBalancesModal').modal('show');
	get_details_xhr = $.ajax({
        url: baseurl+'/admin/rewritebooks/load_details', 
        type: 'POST',
        data: {type: type},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {            
            if(response.status){
                list = response.list;
                var overall_status = 'Total Accounts: ' + list.length;
                $('#updateBalancesModal .overall_status').text(overall_status);
                update_balance(index);
            }
            else{
            	$('#updateBalancesModal .overall_status').text('Something Went wrong');
            	$('#updateBalancesModal .current_status').text(response.message);
            }           
            
        },
        complete: function() {
            // stop_loader();
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
            alert(error);
        },
    });
});

var update_balance_xhr;

$(document).on("click","#refreshbtn_bal", function(){
	var val_index = $(this).attr("data-ids");	
	update_balance(val_index);
	
	//console.log("on refreehs button --->"+val_index);
	$(this).hide();
	
});

function update_balance(indexss)
{
	var progress = Math.round((indexss/list.length)*100);
	$('#updateBalancesModal .progress-bar').css('width', progress+'%');
	$('#updateBalancesModal .progress-bar').text(progress+'%');

	if(indexss < list.length)
	{
		var current_status = `${(indexss+1)} / ${list.length} (${list[indexss].name})`;
		// $("#updateBalancesModal #refreshbtn_bal").attr("data-ids",(indexss+1));		
		$('#updateBalancesModal .current_status').text(current_status);
		
		update_balance_xhr = $.ajax({
	        url: baseurl+'/admin/rewritebooks/update_balance', 
	        type: 'POST',
	        data: {type: type, id: list[indexss].id},
	        dataType: "json",
			timeout: 10000, // 10 secods
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            
	            if(response.status){
	                indexss++;					
	                update_balance(indexss);
					
	            }
	            else{
	            	alert(response.message);
	            }
	        },
	        complete: function() {
	            // stop_loader();
	        },
	        error: function (jqXHR, exception) {

	            var error_= '';
	            if (jqXHR.status == 404) {
	                error = 'Requested page not found. [404]';
	            } else if (jqXHR.status == 500) {
	                error = 'Internal Server Error [500].';
	            } else if (exception === 'parsererror') {
	                error = 'Requested JSON parse failed.';
	            } else if (exception === 'timeout') {
					var getlast_index = $("#updateBalancesModal #refreshbtn_bal").attr("data-ids");
					$("#updateBalancesModal #refreshbtn_bal").attr("data-ids",(indexss+1));								
					$("#updateBalancesModal #refreshbtn_bal").show();
	                error = 'Time out error.';
	            } else if (jqXHR.status === 0 && exception === 'abort') {
					
	                error = 'Ajax request aborted.';
	            } else if (jqXHR.status === 0) {
					//var getlast_index = $("#updateBalancesModal #refreshbtn_bal").attr("data-ids");
					
					 $("#updateBalancesModal #refreshbtn_bal").attr("data-ids",(indexss+1));
					$("#updateBalancesModal #refreshbtn_bal").show();
	                error = 'Not connect.\n Verify Network.';
	            } else {
	                error = 'Uncaught Error.\n' + jqXHR.responseText;
	            }
	            alert(error);
	        },
	    });
	}
	else{
        $('#updateBalancesModal .current_status').text('All accounts are updated');
		 $("#updateBalancesModal #refreshbtn_bal").attr("data-ids",0);
        $('#updateBalancesModal .close_btn').text('Done');
        $('#updateBalancesModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}


$("#updateBalancesModal").on('hide.bs.modal', function () {
	if(update_balance_xhr)
		update_balance_xhr.abort();  
    if(get_details_xhr)	
    	get_details_xhr.abort();
	
	$("#updateBalancesModal #refreshbtn").attr("data-id",'');
	$("#updateBalancesModal #refreshbtn").attr("data-methodid",'');	
					
});



$(document).on('click', '#valuation_calculation', function(){
$('#updateValuationModal .overall_status').hide();
$('#updateValuationModal .current_status').hide();
$('#updateValuationModal .progress-bar').hide();
$('#updateValuationModal .progress').hide();
	
$('#updateValuationModal').modal("show");	
$('#updateValuationModal .modal-title').text('Calculate  Valuation');
});


var valuation_type_list = [];
var error_valuation_list = [];
var valuation_index = 0;
var get_valuation_details_xhr;
$(document).on('click', '.update_valuation_process', function(){
	$('#updateValuationModal .overall_status').show();
    $('#updateValuationModal .current_status').show();
    $('#updateValuationModal .progress-bar').show();
    $('#updateValuationModal .progress').show();
	
	var type_name = $(this).data("type_name");
	var method_id = $(this).data("method_id");
	
	$("#avgbtn").attr("disabled",true);
	$("#fifobtn").attr("disabled",true);
	$("#lifobtn").attr("disabled",true);
	
	$('#updateValuationModal .close_btn').text('Cancel');
    $('#updateValuationModal .close_btn').removeClass('btn-success').addClass('btn-danger');
	
	valuation_type_list = [];
	error_valuation_list = [];
	valuation_index = 0;
	$('#voucher_verification_list').html('');

	$('#updateValuationModal .modal-title').text('Calculating '+type_name+' Valuation');

	$('#updateValuationModal .overall_status').text('Please wait while details are being fetched...');
	$('#updateValuationModal .current_status').text('');

	$('#updateValuationModal .progress-bar').css('width', '0%');
	$('#updateValuationModal .progress-bar').text('0%');

	$('#faults').text('0');

	$('#updateValuationModal').modal('show');

	 get_valuation_details_xhr = $.ajax({
        url: baseurl+'/admin/rewritebooks/load_voucheritms_details', 
        type: 'POST',
        data: {"method_id":method_id},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {
            
            if(response.status){
                voucher_val_list = response.list;
				var method_id = response.method_id;
                var overall_status = 'Total Vouchers: ' + voucher_val_list.length;
                $('#updateValuationModal .overall_status').text(overall_status);
                calc_valuation(valuation_index,method_id);
            }
            else{
            	$('#updateValuationModal .overall_status').text('Something Went wrong');
            	$('#updateValuationModal .current_status').text(response.message);
            }
            
            
        },
        complete: function() {
            // stop_loader();
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
            alert(error);
        },
    }); 
});
var verify_valuation_xhr;

$(document).on("click","#refreshbtn", function(){
	var dd_val_index = $(this).data("id");
	var dd_methodid = $(this).data("methodid");
	
	calc_valuation(dd_val_index,dd_methodid);
	$(this).hide();
	
});

function calc_valuation(valuation_index,method_id)
{
	var progress = Math.round((valuation_index/voucher_val_list.length)*100);
	$('#updateValuationModal .progress-bar').css('width', progress+'%');
	$('#updateValuationModal .progress-bar').text(progress+'%');

	if(valuation_index < voucher_val_list.length)
	{
		var current_status = `${(valuation_index+1)} / ${voucher_val_list.length} (Voucher No.: ${voucher_val_list[valuation_index].name})`;
		$('#updateValuationModal .current_status').text(current_status);

		verify_valuation_xhr = $.ajax({
	        url: baseurl+'/admin/rewritebooks/verify_item_valuation', 
	        type: 'POST',
	        data: {"method_id":method_id,"voucher_txn_id": voucher_val_list[valuation_index].voucher_txn_id,"item_id": voucher_val_list[valuation_index].item_id},
	        dataType: "json",
			timeout: 10000, // 10 secods
	        beforeSend: function(jqXHR, settings){
	           
	        },
	        success: function (response) {	
					valuation_index++;
				   if(response.status){	            	
	                	$("#updateValuationModal #refreshbtn").attr("data-id",valuation_index);
					$("#updateValuationModal #refreshbtn").attr("data-methodid",method_id);
	         
	                calc_valuation(valuation_index,method_id);
						
	            }
	            else{
	            	alert(response.message);
	            }
	        },
	        complete: function() {
	            // stop_loader();
	        },
	        error: function (jqXHR, exception) {
				var error_= '';
	            if (jqXHR.status == 404) {
	                error = 'Requested page not found. [404]';
	            } else if (jqXHR.status == 500) {
	                error = 'Internal Server Error [500].';
	            } else if (exception === 'parsererror') {
	                error = 'Requested JSON parse failed.';
	            } else if (exception === 'timeout') {	
				console.log("timeout==> "+valuation_index);
                    $("#updateValuationModal #refreshbtn").attr("data-id",valuation_index);
					$("#updateValuationModal #refreshbtn").attr("data-methodid",method_id);				
					$("#updateValuationModal #refreshbtn").show();
	                error = 'Time out error.';
	            } else if (jqXHR.status === 0 && exception === 'abort') {
	                error = 'Ajax request aborted.';
	            } else if (jqXHR.status === 0) {
					console.log("not connect==> "+valuation_index);
                    $("#updateValuationModal #refreshbtn").attr("data-id",valuation_index);
					$("#updateValuationModal #refreshbtn").attr("data-methodid",method_id);				
					$("#updateValuationModal #refreshbtn").show();
	                error = 'Not connect.\n Verify Network.';
	            } else {
	                error = 'Uncaught Error.\n' + jqXHR.responseText;
	            }
	           // alert(error);
	        },
	    });
	}
	else{
		$("#avgbtn").attr("disabled",false);
		$("#fifobtn").attr("disabled",false);
		$("#lifobtn").attr("disabled",false);
$("#updateValuationModal #refreshbtn").attr("data-id",'');
					$("#updateValuationModal #refreshbtn").attr("data-methodid",'');		
        $('#updateValuationModal .current_status').text('Valuation Completed');
        $('#updateValuationModal .close_btn').text('Done');
        $('#updateValuationModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}


$("#updateValuationModal").on('hide.bs.modal', function () {
	if(get_valuation_details_xhr)
		get_valuation_details_xhr.abort();  
    if(verify_valuation_xhr)	
    	verify_valuation_xhr.abort();
	
	$("#updateValuationModal #refreshbtn").attr("data-id",'');
					$("#updateValuationModal #refreshbtn").attr("data-methodid",'');	
					
	$("#avgbtn").attr("disabled",false);
		$("#fifobtn").attr("disabled",false);
		$("#lifobtn").attr("disabled",false);
});


/***************   START CALCULATE DEFAULT VALUATION            *******************/


var df_valuation_type_list = [];
var df_error_valuation_list = [];
var valuation_index = 0;

$(document).on("click","#refreshbtndf", function(){
	var val_index = $(this).data("id");	
	calc_item_valuation(val_index);
	$(this).hide();
	
});

var get_rpt_tables_xhr='';
$(document).on('click', '.clear_reporting_tables', function(){
	$('#ClearRepotingTablesModal .overall_status').show();
    $('#ClearRepotingTablesModal .current_status').show();
    $('#ClearRepotingTablesModal .progress-bar').show();
    $('#ClearRepotingTablesModal .progress').show();
	
	$('#ClearRepotingTablesModal .close_btn').text('Cancel');
    $('#ClearRepotingTablesModal .close_btn').removeClass('btn-success').addClass('btn-danger');
	
	rpt_tables_list = [];
	df_error_valuation_list = [];
	rpt_tables_index = 0;
	
	$('#ClearRepotingTablesModal .modal-title').text('Clear Reporting Tables');
	$('#ClearRepotingTablesModal .overall_status').text('Please wait while details are being fetched...');
	$('#ClearRepotingTablesModal .current_status').text('');
	$('#ClearRepotingTablesModal .progress-bar').css('width', '0%');
	$('#ClearRepotingTablesModal .progress-bar').text('0%');

	$('#ClearRepotingTablesModal').modal('show');
	 get_rpt_tables_xhr = $.ajax({
        url: baseurl+'/admin/rewritebooks/load_reporting_tables', 
        type: 'POST',
        data: {"type":"0"},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {            
            if(response.status){
                rpt_tables_list = response.list;				
                var overall_status = 'Total Records: ' + rpt_tables_list.length;
                $('#ClearRepotingTablesModal .overall_status').text(overall_status);
                clear_reprting_tables(rpt_tables_index);
            }
            else{
            	$('#ClearRepotingTablesModal .overall_status').text('Something Went wrong');
            	$('#ClearRepotingTablesModal .current_status').text(response.message);
            }
        },
        complete: function() {
            // stop_loader();
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
            alert(error);
        },
    });
});
var verify_rpt_tables_xhr;
function clear_reprting_tables(rpt_tables_index)
{
	var progress = Math.round((rpt_tables_index/rpt_tables_list.length)*100);
	$('#ClearRepotingTablesModal .progress-bar').css('width', progress+'%');
	$('#ClearRepotingTablesModal .progress-bar').text(progress+'%');

	if(rpt_tables_index < rpt_tables_list.length)
	{
		var current_status = `${(rpt_tables_index+1)} / ${rpt_tables_list.length} (Table : ${rpt_tables_list[rpt_tables_index].name})`;
		$('#ClearRepotingTablesModal .current_status').text(current_status);
		verify_rpt_tables_xhr = $.ajax({
	        url: baseurl+'/admin/rewritebooks/clear_inventory_reporting_tables', 
	        type: 'POST',
	        data: {"type":0,"table_id": rpt_tables_list[rpt_tables_index].table_id},
	        dataType: "json",
			timeout: 10000, // 10 secods
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {	            
	            if(response.status){	 
				
	                rpt_tables_index++;							
	                clear_reprting_tables(rpt_tables_index);
	            }
	            else{
	            	alert(response.message);
	            }
	        },
	        complete: function() {
	            // stop_loader();
	        },
	        error: function (jqXHR, exception) {

	            var error_= '';
	            if (jqXHR.status == 404) {
	                error = 'Requested page not found. [404]';
	            } else if (jqXHR.status == 500) {
	                error = 'Internal Server Error [500].';
	            } else if (exception === 'parsererror') {
	                error = 'Requested JSON parse failed.';
	            } else if (exception === 'timeout') {
					$("#ClearRepotingTablesModal #refreshbtnrpt").attr("data-id",rpt_tables_index);								
					$("#ClearRepotingTablesModal #refreshbtnrpt").show();
	                error = 'Time out error.';
	            } else if (jqXHR.status === 0 && exception === 'abort') {
	                error = 'Ajax request aborted.';
	            } else if (jqXHR.status === 0) {
					$("#ClearRepotingTablesModal #refreshbtnrpt").attr("data-id",rpt_tables_index);								
					$("#ClearRepotingTablesModal #refreshbtnrpt").show();
	                error = 'Not connect.\n Verify Network.';
	            } else {
	                error = 'Uncaught Error.\n' + jqXHR.responseText;
	            }
	            alert(error);
	        },
	    });
	}
	else{
	
		$("#ClearRepotingTablesModal #refreshbtnrpt").attr("data-id",'');					
        $('#ClearRepotingTablesModal .current_status').text('Reporting Tables Cleared');
        $('#ClearRepotingTablesModal .close_btn').text('Done');
        $('#ClearRepotingTablesModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}
$("#ClearRepotingTablesModal").on('hide.bs.modal', function () {
	if(get_rpt_tables_xhr)
		get_rpt_tables_xhr.abort();  
    if(verify_rpt_tables_xhr)	
    	verify_rpt_tables_xhr.abort();	
	$("#ClearRepotingTablesModal #refreshbtnrpt").attr("data-id",'');
});



$(document).on('click', '.default_valuation_process', function(){
	$('#updateDFValuationModal .overall_status').show();
    $('#updateDFValuationModal .current_status').show();
    $('#updateDFValuationModal .progress-bar').show();
    $('#updateDFValuationModal .progress').show();
	
	var type_name = $(this).data("type_name");
	var method_id = $(this).data("method_id");
	
	$('#updateDFValuationModal .close_btn').text('Cancel');
    $('#updateDFValuationModal .close_btn').removeClass('btn-success').addClass('btn-danger');
	
	df_valuation_type_list = [];
	df_error_valuation_list = [];
	df_valuation_index = 0;
	
	$('#updateDFValuationModal .modal-title').text('Calculating '+type_name+' Valuation');
	$('#updateDFValuationModal .overall_status').text('Please wait while details are being fetched...');
	$('#updateDFValuationModal .current_status').text('');
	$('#updateDFValuationModal .progress-bar').css('width', '0%');
	$('#updateDFValuationModal .progress-bar').text('0%');

	$('#updateDFValuationModal').modal('show');
	 get_df_valuation_details_xhr = $.ajax({
        url: baseurl+'/admin/rewritebooks/load_voucheritms_details', 
        type: 'POST',
        data: {"method_id":"0"},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {            
            if(response.status){
                df_valuation_type_list = response.list;				
                var overall_status = 'Total Records: ' + df_valuation_type_list.length;
                $('#updateDFValuationModal .overall_status').text(overall_status);
                calc_item_valuation(df_valuation_index);
            }
            else{
            	$('#updateDFValuationModal .overall_status').text('Something Went wrong');
            	$('#updateDFValuationModal .current_status').text(response.message);
            }
        },
        complete: function() {
            // stop_loader();
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
            alert(error);
        },
    }); 
});


var verify_df_valuation_xhr;
function calc_item_valuation(df_valuation_indexs)
{
	var progress = Math.round((df_valuation_indexs/df_valuation_type_list.length)*100);
	$('#updateDFValuationModal .progress-bar').css('width', progress+'%');
	$('#updateDFValuationModal .progress-bar').text(progress+'%');

	if(df_valuation_indexs < df_valuation_type_list.length)
	{
		var current_status = `${(df_valuation_indexs+1)} / ${df_valuation_type_list.length} (Voucher No: ${df_valuation_type_list[df_valuation_indexs].name})`;
		$('#updateDFValuationModal .current_status').text(current_status);

		verify_df_valuation_xhr = $.ajax({
	        url: baseurl+'/admin/rewritebooks/verify_item_default_valuation', 
	        type: 'POST',
	        data: {"method_id":0,"voucher_txn_id": df_valuation_type_list[df_valuation_indexs].voucher_txn_id,"item_id": df_valuation_type_list[df_valuation_indexs].item_id},
	        dataType: "json",
			timeout: 10000, // 10 secods
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {	            
	            if(response.status){	 
				
	                df_valuation_indexs++;
					$("#updateDFValuationModal #refreshbtndf").attr("data-id",df_valuation_indexs);
					$("#updateDFValuationModal #refreshbtndf").attr("data-methodid",0);
	                calc_item_valuation(df_valuation_indexs);
	            }
	            else{
	            	alert(response.message);
	            }
	        },
	        complete: function() {
	            // stop_loader();
	        },
	        error: function (jqXHR, exception) {

	            var error_= '';
	            if (jqXHR.status == 404) {
	                error = 'Requested page not found. [404]';
	            } else if (jqXHR.status == 500) {
	                error = 'Internal Server Error [500].';
	            } else if (exception === 'parsererror') {
	                error = 'Requested JSON parse failed.';
	            } else if (exception === 'timeout') {
					$("#updateDFValuationModal #refreshbtndf").attr("data-id",df_valuation_indexs);								
					$("#updateDFValuationModal #refreshbtndf").show();
	                error = 'Time out error.';
	            } else if (jqXHR.status === 0 && exception === 'abort') {
	                error = 'Ajax request aborted.';
	            } else if (jqXHR.status === 0) {
					$("#updateDFValuationModal #refreshbtndf").attr("data-id",df_valuation_indexs);								
					$("#updateDFValuationModal #refreshbtndf").show();
	                error = 'Not connect.\n Verify Network.';
	            } else {
	                error = 'Uncaught Error.\n' + jqXHR.responseText;
	            }
	            alert(error);
	        },
	    });
	}
	else{
		$("#avgbtn").attr("disabled",false);
		$("#fifobtn").attr("disabled",false);
		$("#lifobtn").attr("disabled",false);
		
		$("#updateDFValuationModal #refreshbtndf").attr("data-id",'');
		$("#updateDFValuationModal #refreshbtndf").attr("data-methodid",'');						
        $('#updateDFValuationModal .current_status').text('Valuation Completed');
        $('#updateDFValuationModal .close_btn').text('Done');
        $('#updateDFValuationModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}


$("#updateDFValuationModal").on('hide.bs.modal', function () {
	if(get_df_valuation_details_xhr)
		get_df_valuation_details_xhr.abort();  
    if(verify_df_valuation_xhr)	
    	verify_df_valuation_xhr.abort();	
	$("#updateDFValuationModal #refreshbtndf").attr("data-id",'');
	$("#updateDFValuationModal #refreshbtndf").attr("data-methodid",'');	
});


/***************     END CALCULATE DEFAULT VALUATION          *******************/

var voucher_type_list = [];
var error_voucher_list = [];
var voucher_index = 0;

var get_voucher_type_details_xhr;
$(document).on('click', '#voucher_verification', function(){

	$('#voucherVerificationModal .close_btn').text('Cancel');
    $('#voucherVerificationModal .close_btn').removeClass('btn-success').addClass('btn-danger');
	
	voucher_type_list = [];
	error_voucher_list = [];
	voucher_index = 0;
	$('#voucher_verification_list').html('');

	$('#voucherVerificationModal .modal-title').text('Voucher Verification');

	$('#voucherVerificationModal .overall_status').text('Please wait while details are being fetched...');
	$('#voucherVerificationModal .current_status').text('');

	$('#voucherVerificationModal .progress-bar').css('width', '0%');
	$('#voucherVerificationModal .progress-bar').text('0%');

	$('#faults').text('0');

	$('#voucherVerificationModal').modal('show');

	get_voucher_type_details_xhr = $.ajax({
        url: baseurl+'/admin/rewritebooks/load_voucher_type_details', 
        type: 'POST',
        data: {},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {
            
            if(response.status){
                voucher_type_list = response.list;
                var overall_status = 'Total Voucher Types: ' + voucher_type_list.length;
                $('#voucherVerificationModal .overall_status').text(overall_status);
                verify_vouchers();
            }
            else{
            	$('#voucherVerificationModal .overall_status').text('Something Went wrong');
            	$('#voucherVerificationModal .current_status').text(response.message);
            }
            
            
        },
        complete: function() {
            // stop_loader();
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
            alert(error);
        },
    });
});



var verify_vouchers_xhr;
function verify_vouchers()
{
	var progress = Math.round((voucher_index/voucher_type_list.length)*100);
	$('#voucherVerificationModal .progress-bar').css('width', progress+'%');
	$('#voucherVerificationModal .progress-bar').text(progress+'%');

	if(voucher_index < voucher_type_list.length)
	{
		var current_status = `${(voucher_index+1)} / ${voucher_type_list.length} (${voucher_type_list[voucher_index].name})`;
		$('#voucherVerificationModal .current_status').text(current_status);

		verify_vouchers_xhr = $.ajax({
	        url: baseurl+'/admin/rewritebooks/verify_vouchers', 
	        type: 'POST',
	        data: {id: voucher_type_list[voucher_index].id},
	        dataType: "json",
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            
	            if(response.status){

	            	if(response.list.length > 0){
	            		$.each(response.list, function(index,object){
	            			error_voucher_list.push(object);
	            			$('#faults').text(error_voucher_list.length);

	            			var html = `
	            				<li class="list-group-item" data-id="${object.voucher_txn_id}">
								  	${voucher_type_list[voucher_index].name} Voucher No. ${object.comp_vch_no}

								  	<a href="javascript:void(0)" data-id="${object.voucher_txn_id}" data-string="${voucher_type_list[voucher_index].id}||${object.voucher_txn_id}" class="btn btn-sm btn-danger float-end mx-1 deleteVoucher">Delete</a>
								  	<a href="/admin/rewritebooks/edit/${object.voucher_txn_id}" target="_blank" data-id="${object.voucher_txn_id}" data-type="all" class="btn btn-sm btn-primary float-end mx-1 editVoucher">Edit</a>
								  	<span class="float-end mx-1">${object.voucher_date}</span>

								</li>
	            			`;
	            			$('#voucher_verification_list').prepend(html);
	            		});
	            	}

	                voucher_index++;
	                verify_vouchers();
	            }
	            else{
	            	alert(response.message);
	            }
	        },
	        complete: function() {
	            // stop_loader();
	        },
	        error: function (jqXHR, exception) {

	            var error_= '';
	            if (jqXHR.status == 404) {
	                error = 'Requested page not found. [404]';
	            } else if (jqXHR.status == 500) {
	                error = 'Internal Server Error [500].';
	            } else if (exception === 'parsererror') {
	                error = 'Requested JSON parse failed.';
	            } else if (exception === 'timeout') {
	                error = 'Time out error.';
	            } else if (jqXHR.status === 0 && exception === 'abort') {
	                error = 'Ajax request aborted.';
	            } else if (jqXHR.status === 0) {
	                error = 'Not connect.\n Verify Network.';
	            } else {
	                error = 'Uncaught Error.\n' + jqXHR.responseText;
	            }
	            alert(error);
	        },
	    });
	}
	else{
        $('#voucherVerificationModal .current_status').text('All Voucher are Verified');

        $('#voucherVerificationModal .close_btn').text('Done');
        $('#voucherVerificationModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}

$("#voucherVerificationModal").on('hide.bs.modal', function () {
	if(get_voucher_type_details_xhr)
		get_voucher_type_details_xhr.abort();  
    if(verify_vouchers_xhr)	
    	verify_vouchers_xhr.abort();
});

$(document).on('click','#delete_all_verf_vch', function(){
	var objs = $('#voucher_verification_list .deleteVoucher');
	var i = 1;
	$.each(objs, function(ind,obj){
		$(obj).trigger('click');
		i++;
		if(i>100){ return false;}
	});
})


$(document).on('click', '.editVoucher', function(){
	var id = $(this).data('id');
	var account_id = $(this).data('account_id');
	var type = $(this).data('type');
	
	if($('.voucher_list').find('li[data-id="'+id+'"] button.refreshVoucher').length == 0){
		var html = `<button class="btn btn-sm btn-success float-end refreshVoucher" data-id="${id}" data-account_id="${account_id}" data-type="${type}">Refresh</button>`;
		$('.voucher_list').find('li[data-id="'+id+'"]').prepend(html);
	}
	

});

$(document).on('click', '.refreshVoucher', function(){
	var id = $(this).data('id');
	var type = $(this).data('type');
	var account_id = $(this).data('account_id');

	$.ajax({
        url: baseurl+'/admin/rewritebooks/verify_single_voucher', 
        type: 'POST',
        data: {id: id, type: type, account_id: account_id},
        dataType: "json",
        beforeSend: function() {
            $('.refreshVoucher').css('pointer-events', 'none');
        },
        success: function (response) {
            
            if(response.status == 2){
  
        		$('.voucher_list').find('li[data-id="'+id+'"]').find('a').remove();
        		$('.voucher_list').find('li[data-id="'+id+'"]').find('button').remove();
				var html = `<span class="badge bg-danger float-end">Deleted</span>`;
				$('.voucher_list').find('li[data-id="'+id+'"]').prepend(html);

				var faults = parseInt($("#faults").text());
				if (faults > 0)
					$("#faults").text((faults-1));
				else
					$("#faults").text('0');
            }
            else if(response.status == 1){
  
        		$('.voucher_list').find('li[data-id="'+id+'"]').find('a').remove();
        		$('.voucher_list').find('li[data-id="'+id+'"]').find('button').remove();
				var html = `<span class="badge bg-success float-end">Resolved</span>`;
				$('.voucher_list').find('li[data-id="'+id+'"]').prepend(html);

				var faults = parseInt($("#faults").text());
				if (faults > 0)
					$("#faults").text((faults-1));
				else
					$("#faults").text('0');
            }
            else{
            	$('.voucher_list').find('li[data-id="'+id+'"]').find('button').remove();
            }
            
        },
        complete: function() {
            $('.refreshVoucher').css('pointer-events', 'auto');
        },
        error: function (jqXHR, exception) {

            var error_= '';
            if (jqXHR.status == 404) {
                error = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                error = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                error = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                error = 'Time out error.';
            } else if (jqXHR.status === 0 && exception === 'abort') {
                error = 'Ajax request aborted.';
            } else if (jqXHR.status === 0) {
                error = 'Not connect.\n Verify Network.';
            } else {
                error = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            alert(error);
        },
    });
});

$(document).on('click', '.deleteVoucher', function(){
	var id = $(this).data('id');
	var string = $(this).data('string');

	$.ajax({
        url: baseurl+'/admin/reports/remove_daybook_vouchers', 
        type: 'POST',
        data: {vchrids: [id], token: "<?php echo date('ssYssHs');?>"},
        dataType: "json",
        beforeSend: function() {
            $('.deleteVoucher').css('pointer-events', 'none');
        },
        success: function (response) {
            
            if(response.status){
            	if(response.errors.length == 0)
            	{
            		$('.voucher_list').find('li[data-id="'+id+'"]').find('a').remove();
					var html = `<span class="badge bg-danger float-end">Deleted</span>`;
					$('.voucher_list').find('li[data-id="'+id+'"]').prepend(html);

					var faults = parseInt($("#faults").text());
					if (faults > 0)
						$("#faults").text((faults-1));
					else
						$("#faults").text('0');
            	}

            	if(response.errors.length == 1)
            		alert(response.errors[0]);
            }
            
        },
        complete: function() {
            $('.deleteVoucher').css('pointer-events', 'auto');
        },
        error: function (jqXHR, exception) {

            var error_= '';
            if (jqXHR.status == 404) {
                error = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                error = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                error = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                error = 'Time out error.';
            } else if (jqXHR.status === 0 && exception === 'abort') {
                error = 'Ajax request aborted.';
            } else if (jqXHR.status === 0) {
                error = 'Not connect.\n Verify Network.';
            } else {
                error = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            alert(error);
        },
    });
});




var table_list = [];
var error_table_list = [];
var table_index = 0;

var get_db_details_xhr;
$(document).on('click', '#db_repair', function(){

	$('#dbRepairModal .close_btn').text('Cancel');
    $('#dbRepairModal .close_btn').removeClass('btn-success').addClass('btn-danger');
	
	table_list = [];
	tabledb_list = '';
	error_table_list = [];
	table_index = 0;
	$('#voucher_verification_list').html('');

	$('#dbRepairModal .modal-title').text('Database Diagnose');

	$('#dbRepairModal .overall_status').text('Please wait while DB details are being fetched...');
	$('#dbRepairModal .current_status').text('');

	$('#dbRepairModal .progress-bar').css('width', '0%');
	$('#dbRepairModal .progress-bar').text('0%');

	$('#faults').text('0');

	$('#dbRepairModal').modal('show');

	get_db_details_xhr = $.ajax({
        url: baseurl+'/admin/rewritebooks/load_db_details', 
        type: 'POST',
        data: {},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {
            
            if(response.status){
                table_list         = response.list; 
				tabledb_list         = response.dstrng;                
                var overall_status = 'Total Tables: ' + table_list.length;
                $('#dbRepairModal .overall_status').text(overall_status);
                diagnose_db(table_index);
            }
            else{
            	$('#dbRepairModal .overall_status').text('Something Went wrong');
            	$('#dbRepairModal .current_status').text(response.message);
              }
        },
        complete: function() {
            // stop_loader();
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
            alert(error);
        },
    });
});

$(document).on("click","#refreshbtndb", function(){
	var val_index = $(this).data("id");	
	diagnose_db(val_index);
	$(this).hide();
	
});

var diagnose_db_xhr;
function diagnose_db(table_index)
{
	var progress = Math.round((table_index/table_list.length)*100);
	$('#dbRepairModal .progress-bar').css('width', progress+'%');
	$('#dbRepairModal .progress-bar').text(progress+'%');

	if(table_index < table_list.length)
	{
		var current_status = `${(table_index+1)} / ${table_list.length}`;
		$('#dbRepairModal .current_status').text(current_status);

		diagnose_db_xhr = $.ajax({
	        url: baseurl+'/admin/rewritebooks/diagnose_db', 
	        type: 'POST',
	        data: {table_name: table_list[table_index],dstring: tabledb_list},
	        dataType: "json",
			timeout: 10000, // 10 secods
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            console.table(response.queryes);
	            if(response.status){
	                table_index++;
					$("#dbRepairModal #refreshbtndb").attr("data-id",table_index);
	                diagnose_db(table_index);
	            }
	            else{
	            	//alert(response.message);
					if(response.errors)
                    {
                        var list = ``;
                        $.each(response.errors, function(index, value){
                           list += `
                              <li>${value}</li>
                           `;
                        });
                        var html = `
                            <div class="alert alert-danger alert-dismissible fade show mt-2" style="max-height: 150px;overflow-y: auto;">
                              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              <ul>
                                  <li>${list}</li>
                              </ul>
                          </div>
                        `;
                       alert_notification(html);
                    } 
                   }
	          
	        },
	        complete: function() {
	            // stop_loader();
	        },
	        error: function (jqXHR, exception) {

	            var error_= '';
	            if (jqXHR.status == 404) {
	                error = 'Requested page not found. [404]';
	            } else if (jqXHR.status == 500) {
	                error = 'Internal Server Error [500].';
	            } else if (exception === 'parsererror') {
	                error = 'Requested JSON parse failed.';
	            } else if (exception === 'timeout') {
					$("#dbRepairModal #refreshbtndb").attr("data-id",table_index);								
					$("#dbRepairModal #refreshbtndb").show();
	                error = 'Time out error.';
	            } else if (jqXHR.status === 0 && exception === 'abort') {
	                error = 'Ajax request aborted.';
	            } else if (jqXHR.status === 0) {
					$("#dbRepairModal #refreshbtndb").attr("data-id",table_index);								
					$("#dbRepairModal #refreshbtndb").show();
	                error = 'Not connect.\n Verify Network.';
	            } else {
	                error = 'Uncaught Error.\n' + jqXHR.responseText;
	            }
	            alert(error);
	        },
	    });
	}
	else{
        $('#dbRepairModal .current_status').text('All Tables are Verified');
		$("#dbRepairModal #refreshbtndb").attr("data-id",'');	
        $('#dbRepairModal .close_btn').text('Done');
        $('#dbRepairModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	  }
}

$("#dbRepairModal").on('hide.bs.modal', function () {
	if(get_db_details_xhr)
		get_db_details_xhr.abort();  
    if(diagnose_db_xhr)	
    	diagnose_db_xhr.abort();
	$("#dbRepairModal #refreshbtndb").attr("data-id",'');	
});

var get_bbb_accounts_xhr;
var bbb_accounts_list = [];
var bbb_accounts_index = 0;

var bbb_error_voucher_list = [];

$(document).on('click', '#bbb_voucher_verification', function(){
	bbb_accounts_list = [];
	bbb_accounts_index = 0;
	bbb_error_voucher_list = [];
	$('#bbb_faults').text('0');
	$('.voucher_list').html('');

	$('#bbbVoucherVerificationModal .close_btn').text('Cancel');
    $('#bbbVoucherVerificationModal .close_btn').removeClass('btn-success').addClass('btn-danger');

	$('#bbbVoucherVerificationModal .overall_status').text('Please wait while details are being fetched...');
	$('#bbbVoucherVerificationModal .current_status').text('');

	$('#bbbVoucherVerificationModal .overall_sub_status').text('');
	$('#bbbVoucherVerificationModal .current_sub_status').text('');

	$('#bbbVoucherVerificationModal .progress-bar').css('width', '0%');
	$('#bbbVoucherVerificationModal .progress-bar').text('0%');

	$('#bbbVoucherVerificationModal').modal('show');

	get_bbb_accounts_xhr = $.ajax({
        url: baseurl+'/admin/rewritebooks/get_bbb_accounts', 
        type: 'POST',
        data: {},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {
            
            if(response.status){
                bbb_accounts_list = response.list;
                var overall_status = 'Total Accounts: ' + bbb_accounts_list.length;
                $('#bbbVoucherVerificationModal .overall_status').text(overall_status);
                get_bbb_vouchers_list();
            }
            else{
            	$('#bbbVoucherVerificationModal .overall_status').text('Something Went wrong');
            	$('#bbbVoucherVerificationModal .current_status').text(response.message);
            }
        },
        complete: function() {
            // stop_loader();
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
            alert(error);
        },
    });
});

var get_bbb_vouchers_xhr;
var bbb_vouchers_list = [];
var bbb_vouchers_index = 0;

function get_bbb_vouchers_list()
{
	bbb_vouchers_list = [];
	bbb_vouchers_index = 0;

	var progress = Math.round((bbb_accounts_index/bbb_accounts_list.length)*100);
	$('#bbbVoucherVerificationModal .progress-bar-main').css('width', progress+'%');
	$('#bbbVoucherVerificationModal .progress-bar-main').text(progress+'%');

	if(bbb_accounts_index < bbb_accounts_list.length)
	{
		var current_status = `${(bbb_accounts_index+1)} / ${bbb_accounts_list.length} (${bbb_accounts_list[bbb_accounts_index].name})`;
		$('#bbbVoucherVerificationModal .current_status').text(current_status);

		$('#bbbVoucherVerificationModal .overall_sub_status').text('Please wait while details are being fetched...');
		$('#bbbVoucherVerificationModal .current_sub_status').text('');

		$('#bbbVoucherVerificationModal .progress-bar-sub').css('width', '0%');
		$('#bbbVoucherVerificationModal .progress-bar-sub').text('0%');

		get_bbb_vouchers_xhr = $.ajax({
	        url: baseurl+'/admin/rewritebooks/get_bbb_vouchers', 
	        type: 'POST',
	        data: {account_id: bbb_accounts_list[bbb_accounts_index].id},
	        dataType: "json",
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            
	            if(response.status){
	                bbb_vouchers_list = response.list;
	                var overall_status = 'Total Vouchers: ' + bbb_vouchers_list.length;
	                $('#bbbVoucherVerificationModal .overall_sub_status').text(overall_status);
	                verify_bbb_vouchers();
	            }
	            else{
	            	$('#bbbVoucherVerificationModal .overall_sub_status').text('Total Vouchers: 0');
	            	$('#bbbVoucherVerificationModal .current_sub_status').text('All Voucher are Verified');

	            	bbb_accounts_index++;
	    			get_bbb_vouchers_list();
	            }
	            
	            
	        },
	        complete: function() {
	            // stop_loader();
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
	            alert(error);
	        },
	    });
	}
	else
	{
		$('#bbbVoucherVerificationModal .current_status').text('All accounts are updated');

		$('#bbbVoucherVerificationModal .progress-bar-sub').css('width', '100%');
		$('#bbbVoucherVerificationModal .progress-bar-sub').text('100%');

        $('#bbbVoucherVerificationModal .close_btn').text('Done');
        $('#bbbVoucherVerificationModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}

var verify_bbb_vouchers_xhr;


function verify_bbb_vouchers()
{
	var progress = Math.round((bbb_vouchers_index/bbb_vouchers_list.length)*100);
	$('#bbbVoucherVerificationModal .progress-bar-sub').css('width', progress+'%');
	$('#bbbVoucherVerificationModal .progress-bar-sub').text(progress+'%');

	var progress2 = Math.round((bbb_accounts_index/bbb_accounts_list.length)*100);
	var progress3 = progress2 + Math.round(progress/bbb_accounts_list.length)

	$('#bbbVoucherVerificationModal .progress-bar-main').css('width', progress3+'%');
	$('#bbbVoucherVerificationModal .progress-bar-main').text(progress3+'%');

	if(bbb_vouchers_index < bbb_vouchers_list.length)
	{
		var current_status = `${(bbb_vouchers_index+1)} / ${bbb_vouchers_list.length} (${bbb_vouchers_list[bbb_vouchers_index].name})`;
		$('#bbbVoucherVerificationModal .current_sub_status').text(current_status);

		verify_bbb_vouchers_xhr = $.ajax({
	        url: baseurl+'/admin/rewritebooks/verify_bbb_vouchers', 
	        type: 'POST',
	        data: {account_id: bbb_accounts_list[bbb_accounts_index].id, voucher_txn_id: bbb_vouchers_list[bbb_vouchers_index].id},
	        dataType: "json",
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            
	            if(response.status == 1 || response.status == 2){
	                bbb_vouchers_index++;
	                verify_bbb_vouchers();
	            }
	            if(response.status == 0){
        			bbb_error_voucher_list.push(bbb_vouchers_list[bbb_vouchers_index].id);
        			$('#bbb_faults').text(bbb_error_voucher_list.length);

        			var html = `
        				<li class="list-group-item" data-id="${bbb_vouchers_list[bbb_vouchers_index].id}">
						  	${bbb_vouchers_list[bbb_vouchers_index].name}

						  	<a href="javascript:void(0)" data-id="${bbb_vouchers_list[bbb_vouchers_index].id}" data-string="${bbb_vouchers_list[bbb_vouchers_index].voucher_type_id}||${bbb_vouchers_list[bbb_vouchers_index].id}" class="btn btn-sm btn-danger float-end mx-1 deleteVoucher">Delete</a>
						  	<a href="/admin/rewritebooks/edit/${bbb_vouchers_list[bbb_vouchers_index].id}" target="_blank" data-id="${bbb_vouchers_list[bbb_vouchers_index].id}" data-account_id="${bbb_accounts_list[bbb_accounts_index].id}" data-type="bbb" class="btn btn-sm btn-primary float-end mx-1 editVoucher">Edit</a>
						  	<span class="float-end mx-1">${bbb_vouchers_list[bbb_vouchers_index].voucher_date}</span>

						</li>
        			`;
        			$('#bbb_voucher_verification_list').prepend(html);

        			bbb_vouchers_index++;
	                verify_bbb_vouchers();
	            }
	        },
	        complete: function() {
	            // stop_loader();
	        },
	        error: function (jqXHR, exception) {

	            var error_= '';
	            if (jqXHR.status == 404) {
	                error = 'Requested page not found. [404]';
	            } else if (jqXHR.status == 500) {
	                error = 'Internal Server Error [500].';
	            } else if (exception === 'parsererror') {
	                error = 'Requested JSON parse failed.';
	            } else if (exception === 'timeout') {
	                error = 'Time out error.';
	            } else if (jqXHR.status === 0 && exception === 'abort') {
	                error = 'Ajax request aborted.';
	            } else if (jqXHR.status === 0) {
	                error = 'Not connect.\n Verify Network.';
	            } else {
	                error = 'Uncaught Error.\n' + jqXHR.responseText;
	            }
	            alert(error);
	        },
	    });
	}
	else{
        $('#bbbVoucherVerificationModal .current_sub_status').text('All Voucher are Verified');

        bbb_accounts_index++;
	    get_bbb_vouchers_list();
	}
}

$("#bbbVoucherVerificationModal").on('hide.bs.modal', function () {
	if(get_bbb_accounts_xhr)
		get_bbb_accounts_xhr.abort();
	if(get_bbb_vouchers_xhr)
		get_bbb_vouchers_xhr.abort();  
    if(verify_bbb_vouchers_xhr)	
    	verify_bbb_vouchers_xhr.abort();
});


var verify_master_list = [];
var error_master_list = [];
var verify_master_index = 0;
var verify_master_type = '';
var verify_master_error = 0;

var get_verify_master_xhr;
$(document).on('click', '.master_varification', function(){

	var type = $(this).data('type');
	verify_master_type = type;
	verify_master_error = 0;

	$('#masterVerificationModal .close_btn').text('Cancel');
    $('#masterVerificationModal .close_btn').removeClass('btn-success').addClass('btn-danger');
	
	verify_master_list = [];
	error_master_list = [];
	verify_master_index = 0;
	$('#master_verification_list').html('');

	$('#masterVerificationModal .modal-title').text(type.toUpperCase() + ' Master Verification');

	$('#masterVerificationModal .overall_status').text('Please wait while details are being fetched...');
	$('#masterVerificationModal .current_status').text('');

	$('#masterVerificationModal .progress-bar').css('width', '0%');
	$('#masterVerificationModal .progress-bar').text('0%');

	$('#masterVerificationModal .faults').text('0');

	$('#masterVerificationModal').modal('show');

	get_verify_master_xhr = $.ajax({
        url: baseurl+'/admin/rewritebooks/load_details', 
        type: 'POST',
        data: {type: type},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {
            
            if(response.status){
                verify_master_list = response.list;
                var overall_status = 'Total Masters: ' + verify_master_list.length;
                $('#masterVerificationModal .overall_status').text(overall_status);
                verify_masters();
            }
            else{
            	$('#masterVerificationModal .overall_status').text('Something Went wrong');
            	$('#masterVerificationModal .current_status').text(response.message);
            }
            
            
        },
        complete: function() {
            // stop_loader();
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
            alert(error);
        },
    });
});

var verify_master_xhr;
function verify_masters() 
{
	var progress = Math.round((verify_master_index/verify_master_list.length)*100);
	$('#masterVerificationModal .progress-bar').css('width', progress+'%');
	$('#masterVerificationModal .progress-bar').text(progress+'%');

	if(verify_master_index < verify_master_list.length)
	{
		var current_status = `${(verify_master_index+1)} / ${verify_master_list.length} (${verify_master_list[verify_master_index].name})`;
		$('#masterVerificationModal .current_status').text(current_status);

		var errors_count = 0;
		var master_id = verify_master_list[verify_master_index].id;
		var master_name = verify_master_list[verify_master_index].name;

		verify_master_xhr = $.ajax({
	        url: baseurl+'/admin/rewritebooks/verify_masters', 
	        type: 'POST',
	        data: {id: master_id, type: verify_master_type},
	        dataType: "json",
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            
	            if(response.status){

	            	if(response.error){
	            		verify_master_error++;
            			$('#masterVerificationModal .faults').text(verify_master_error);

            			var html = `
            				<li class="list-group-item" data-id="${master_id}">
							  	${master_name} 
							  	
							  	<a href="javascript:void(0)" data-id="${master_id}" data-type="${verify_master_type}" class="btn btn-sm btn-primary float-end mx-1 rectifyMaster">Rectify</a>
							</li>
            			`;
            			$('#master_verification_list').prepend(html);
	            		
	            	}

	                verify_master_index++;
	                verify_masters();
	            }
	            else{
	            	alert(response.message);
	            }
	        },
	        complete: function() {
	            // stop_loader();
	        },
	        error: function (jqXHR, exception) {

	            var error_= '';
	            if (jqXHR.status == 404) {
	                error = 'Requested page not found. [404]';
	            } else if (jqXHR.status == 500) {
	                error = 'Internal Server Error [500].';
	            } else if (exception === 'parsererror') {
	                error = 'Requested JSON parse failed.';
	            } else if (exception === 'timeout') {
	                error = 'Time out error.';
	            } else if (jqXHR.status === 0 && exception === 'abort') {
	                error = 'Ajax request aborted.';
	            } else if (jqXHR.status === 0) {
	                error = 'Not connect.\n Verify Network.';
	            } else {
	                error = 'Uncaught Error.\n' + jqXHR.responseText;
	            }
	            alert(error);
	        },
	    });
	}
	else{
        $('#masterVerificationModal .current_status').text('All Voucher are Verified');

        $('#masterVerificationModal .close_btn').text('Done');
        $('#masterVerificationModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}

$("#masterVerificationModal").on('hide.bs.modal', function () {
	if(get_verify_master_xhr)
		get_verify_master_xhr.abort();  
    if(verify_master_xhr)	
    	verify_master_xhr.abort();
});

$(document).on('click', '.rectifyMaster', function(){
	var obj = $(this);
	var id = $(this).data('id');
	var type = $(this).data('type');


	$.ajax({
        url: baseurl+'/admin/rewritebooks/rectifyMaster', 
        type: 'POST',
        data: {id: id, type: type},
        dataType: "json",
        beforeSend: function() {
            $(obj).css('pointer-events', 'none');
        },
        success: function (response) {
            
            if(response.status){
                $(obj).removeClass('rectifyMaster');
                $(obj).removeClass('btn-primary');
                $(obj).addClass('btn-success');
                $(obj).text('Rectified');

                verify_master_error -= 1
                $('#masterVerificationModal .faults').text(verify_master_error);
                
            }
            else{
            	alert('something went wrong');
            	console.log(response.error);
            }
        },
        complete: function() {
            // stop_loader();
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
            alert(error);
        },
    });
});


var get_fy_master_details_xhr;

$(document).on('click', '.update_fy_masters', function(){
	var isnextfyexists = '<?= ($NextFyExists>0) ? '1' : '0' ?>';
	if(isnextfyexists==0){
	  alert_notification("Carryforward FY Masters Denied!!");
	  return false;
	}else{
	var type = $(this).data('type');
	$('#updateFYMasterModal .modal-title').text(get_modal_title(type));
	$('#updateFYMasterModal .close_btn').text('Cancel');
    $('#updateFYMasterModal .close_btn').removeClass('btn-success').addClass('btn-danger');
	$('#updateFYMasterModal .overall_status').text('Please wait while details are being fetched...');
	$('#updateFYMasterModal .current_status').text('');
	$('#updateFYMasterModal .progress-bar').css('width', '0%');
	$('#updateFYMasterModal .progress-bar').text('0%');
	$('#updateFYMasterModal').modal('show');
	$('#fy_masters_list').html('');
	$('#updateFYMasterModal .faults').text('0');
	var current_time = 1;
    var interval = setInterval(function(){
		current_time++;
		var pct =Math.round(((current_time - 1) / current_time)*100);
			$('#updateFYMasterModal .progress-bar').css("width", pct+"%");
     	$('#updateFYMasterModal .progress-bar').text(pct+"%");  
    }, 1000);

	get_fy_master_details_xhr = $.ajax({
        url: baseurl+'/admin/rewritebooks/load_fy_master_details', 
        type: 'POST',
        data: {type: type},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {
            
          if(response.status){  

            var list = response.list;
            var overall_status = 'Total Accounts: ' + list.length;
            $('#updateFYMasterModal .overall_status').text(overall_status);
            if(list.length > 0){
        			$('#updateFYMasterModal .faults').text(0);

        			var html = ``;
        			$.each(list, function(index,object){
        				html += `
	        				<li class="list-group-item" data-id="${object.id}">
						  			${object.name} 
						  	
						  			<a href="javascript:void(0)" data-id="${object.id}" data-type="${type}" class="btn btn-sm btn-primary float-end mx-1 migrateFYmaster">Migrate</a>
									</li>
	        			`;
        			});
        			$('#fy_masters_list').html(html);
	            		
	          }
          }
          else{
          	$('#updateFYMasterModal .overall_status').text('All accounts have already migrated');
          	$('#updateFYMasterModal .current_status').text(response.message);
          }  
        },
        complete: function() {
            // stop_loader();
        	clearInterval(interval);
        	$('#updateFYMasterModal .progress-bar').css("width", "100%");
     			$('#updateFYMasterModal .progress-bar').text("100%");

        	$('#updateFYMasterModal .close_btn').text('Done');
        	$('#updateFYMasterModal .close_btn').removeClass('btn-danger').addClass('btn-success');
        },
        error: function (jqXHR, exception) {
        		clearInterval(interval);
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
            alert(error);
        },
    });
  }
});

$("#updateFYMasterModal").on('hide.bs.modal', function () {
	if(get_fy_master_details_xhr)
		get_fy_master_details_xhr.abort();  
});


$(document).on('click', '.migrateFYmaster', function(){
	var obj = $(this);
	var id = $(this).data('id');
	var type = $(this).data('type');


	$.ajax({
        url: baseurl+'/admin/rewritebooks/update_fy_masters', 
        type: 'POST',
        data: {id: id, type: type},
        dataType: "json",
        beforeSend: function() {
            $(obj).css('pointer-events', 'none');
        },
        success: function (response) {
            
            if(response.status){
                $(obj).removeClass('migrateFYmaster');
                $(obj).removeClass('btn-primary');
                $(obj).addClass('btn-success');
                $(obj).text('Migrated');

                var count = $('#updateFYMasterModal .faults').text();
                count = parseInt(count);count++;

                $('#updateFYMasterModal .faults').text(count);

                if(response.errors){

                	var html = `<ul class="text-warning">`;
                	$.each(response.errors, function(i,error){
			          		html += `<li>${error}</li>`;
			        		});
			        		html += `</ul>`;
			        		$(obj).parent().append(html);
                }
                
            }
            else{
            	alert(response.errors);
            }
        },
        complete: function() {
            $(obj).css('pointer-events', 'auto');
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
            alert(error);
        },
    });
});

// ===== Accounts (existing globals stay as-is) =====
var fy_bal_index = 0;
var fy_bal_list = [];

var get_fy_details_xhr;      // can be shared
var update_fy_balance_xhr;   // used by account flow

// ===== Items (separate variables) =====
const ITEM_BATCH_SIZE = 500;
var item_total = 0;             // total items to process
var item_processed = 0;         // processed so far
var item_batch_xhr = null;      // xhr handle for batch endpoint
var item_total_fetch_xhr = null;// xhr handle for initial count

const ACC_BATCH_SIZE = 100;
var acc_total = 0;
var acc_processed = 0;
var acc_batch_xhr = null;
var acc_total_fetch_xhr = null;

// ===== Click handler (routes by type) =====
$(document).on('click', '.update_fy_balance', function () {
  var type = $(this).data('type'); // "account" or "item"

  // Reset UI
  $('#updateFYBalancesModal .modal-title').text(get_modal_title(type));
  $('#updateFYBalancesModal .close_btn').text('Cancel')
    .removeClass('btn-success').addClass('btn-danger');
  $('#updateFYBalancesModal .overall_status').text('Please wait while details are being fetched...');
  $('#updateFYBalancesModal .current_status').text('');
  $('#updateFYBalancesModal .progress-bar').css('width', '0%').text('0%');
  $('#updateFYBalancesModal .faults').text('0');
  $('#updateFYBalancesModal .voucher_list').html('');
  $('#updateFYBalancesModal').modal('show');

  if (type === 'account') {
    // Reset account-only state
    fy_bal_index = 0;
    fy_bal_list = [];
    acc_total = 0;
    acc_processed = 0;
    get_fy_details_xhr = $.ajax({
      url: baseurl + 'admin/rewritebooks/load_details',
      type: 'POST',
      data: { type: 'account' },
      dataType: 'json',
      success: function (resp) {
        if (resp.status) {
			acc_total = parseInt(resp.total || 0, 10);
          fy_bal_list = resp.list || [];
		  $('#updateFYBalancesModal .overall_status')
                    .text('Total Accounts: ' + acc_total);

                process_account_batch(0);
				
         // $('#updateFYBalancesModal .overall_status').text('Total Accounts: ' + fy_bal_list.length);
        //  update_fy_balance_account();
        } else {
          $('#updateFYBalancesModal .overall_status').text('Something went wrong');
          $('#updateFYBalancesModal .current_status').text(resp.message || '');
        }
      },
      error: xhrErrorHandler
    });
    return;
  }

  if (type === 'item') {
    // Reset item-only state
    item_total = 0;
    item_processed = 0;

    item_total_fetch_xhr = $.ajax({
      url: baseurl + 'admin/rewritebooks/load_details',
      type: 'POST',
      data: { type: 'item' },
      dataType: 'json',
      success: function (resp) {
        if (resp.status) {
          item_total = parseInt(resp.total || 0, 10);
          $('#updateFYBalancesModal .overall_status').text('Total Items: ' + item_total);
          process_item_batch(0); // start batches
        } else {
          $('#updateFYBalancesModal .overall_status').text('Something went wrong');
          $('#updateFYBalancesModal .current_status').text(resp.message || '');
        }
      },
      error: xhrErrorHandler
    });
  }
});

// ===== Accounts flow (unchanged) =====
function update_fy_balance_account() {
  var progress = fy_bal_list.length === 0
    ? 100
    : Math.round((fy_bal_index / fy_bal_list.length) * 100);
  $('#updateFYBalancesModal .progress-bar').css('width', progress + '%').text(progress + '%');

  if (fy_bal_index >= fy_bal_list.length) {
    $('#updateFYBalancesModal .current_status').text('All accounts are updated');
    $('#updateFYBalancesModal .close_btn').text('Done')
      .removeClass('btn-danger').addClass('btn-success');
    return;
  }

  var row = fy_bal_list[fy_bal_index];
  $('#updateFYBalancesModal .current_status').text(
    (fy_bal_index + 1) + ' / ' + fy_bal_list.length + ' (' + row.name + ')'
  );

  update_fy_balance_xhr = $.ajax({
    url: baseurl + 'admin/rewritebooks/update_fy_balance',
    type: 'POST',
    data: { type: 'account', id: row.id },
    dataType: 'json',
    success: function (resp) {
      if (resp.status) {
        if (resp.errors && resp.errors.length > 0) {
          incrementFaultsAndList(row.name, resp.errors);
        }
        fy_bal_index++;
        update_fy_balance_account();
      } else {
        alert(resp.message || 'Unknown error');
      }
    },
    error: xhrErrorHandler
  });
}

// ============ Account Flow Batch Wise ===========

function process_account_batch(offset)
{
    var progress = acc_total === 0 ? 100 :
        Math.round((acc_processed / acc_total) * 100);

    $('#updateFYBalancesModal .progress-bar')
        .css('width', progress + '%')
        .text(progress + '%');

    if (acc_total === 0 || acc_processed >= acc_total) {
        $('#updateFYBalancesModal .current_status')
            .text('All accounts are updated');

        $('#updateFYBalancesModal .close_btn')
            .text('Done')
            .removeClass('btn-danger')
            .addClass('btn-success');

        return;
    }

    $('#updateFYBalancesModal .current_status').text(
        'Processed ' + acc_processed + ' / ' + acc_total +
        ' (Batch of ' + ACC_BATCH_SIZE + ')'
    );

    acc_batch_xhr = $.ajax({
        url: baseurl + 'admin/rewritebooks/update_account_batch',
        type: 'POST',
        data: { offset: offset, limit: ACC_BATCH_SIZE },
        dataType: 'json',

        success: function (resp) {

			if (!resp.status) {
				alert(resp.message);
				return;
			}

			var processed = parseInt(resp.processed || 0, 10);
			acc_processed += processed;

			// ✅ Update progress immediately
			var progress = acc_total === 0 ? 100 :
				Math.round((acc_processed / acc_total) * 100);

			$('#updateFYBalancesModal .progress-bar')
				.css('width', progress + '%')
				.text(progress + '%');

			if (resp.done) {
				$('#updateFYBalancesModal .progress-bar')
					.css('width', '100%')
					.text('100%');

				$('#updateFYBalancesModal .current_status')
					.text('All accounts are updated');

				$('#updateFYBalancesModal .close_btn')
					.text('Done')
					.removeClass('btn-danger')
					.addClass('btn-success');

				return;
			}

			process_account_batch(offset + ACC_BATCH_SIZE);
		},

        error: xhrErrorHandler
    });
}



// ===== Items flow (batched; uses item_* variables only) =====
function process_item_batch(offset) {
  var progress = item_total === 0 ? 100 : Math.round((item_processed / item_total) * 100);
  $('#updateFYBalancesModal .progress-bar').css('width', progress + '%').text(progress + '%');

  if (item_total === 0 || item_processed >= item_total) {
    $('#updateFYBalancesModal .current_status').text('All items are updated');
    $('#updateFYBalancesModal .close_btn').text('Done')
      .removeClass('btn-danger').addClass('btn-success');
    return;
  }

  $('#updateFYBalancesModal .current_status').text(
    'Processed ' + item_processed + ' / ' + item_total + ' (Batch of ' + ITEM_BATCH_SIZE + ')'
  );

  item_batch_xhr = $.ajax({
    url: baseurl + 'admin/rewritebooks/update_item_batch',
    type: 'POST',
    data: { offset: offset, limit: ITEM_BATCH_SIZE },
    dataType: 'json',
    success: function (resp) {
      if (!resp.status) {
        alert(resp.message || 'Unknown error');
        return;
      }

      if (resp.errors && resp.errors.length > 0) {
        resp.errors.forEach(function (e) {
          var label = (typeof e === 'string') ? '' : (e.name || '');
          var msg = (typeof e === 'string') ? e : (e.error || '');
          incrementFaultsAndList(label, [msg]);
        });
      }

      var processed = parseInt(resp.processed || 0, 10);
      item_processed += processed;

      var nextOffset = offset + ITEM_BATCH_SIZE;

      var p2 = item_total === 0 ? 100 : Math.round((item_processed / item_total) * 100);
      $('#updateFYBalancesModal .progress-bar').css('width', p2 + '%').text(p2 + '%');

      if (item_processed >= item_total || resp.done) {
        $('#updateFYBalancesModal .current_status').text('All items are updated');
        $('#updateFYBalancesModal .close_btn').text('Done')
          .removeClass('btn-danger').addClass('btn-success');
      } else {
        process_item_batch(nextOffset);
      }
    },
    error: xhrErrorHandler
  });
}

// ===== Shared helpers =====
function incrementFaultsAndList(name, errs) {
  var count = parseInt($('#updateFYBalancesModal .faults').text() || '0', 10);
  count += errs.length;
  $('#updateFYBalancesModal .faults').text(count);

  errs.forEach(function (error) {
    var label = name ? (name + ' - ') : '';
    var html = '<li class="list-group-item">' + label + error + '</li>';
    $('#updateFYBalancesModal .voucher_list').prepend(html);
  });
}

function xhrErrorHandler(jqXHR, exception) {
  var error = '';
  if (jqXHR.status === 0 && exception === 'abort') error = 'Ajax request aborted.';
  else if (jqXHR.status === 0) error = 'Not connect.\n Verify Network.';
  else if (jqXHR.status == 404) error = 'Requested page not found. [404]';
  else if (jqXHR.status == 500) error = 'Internal Server Error [500].';
  else if (exception === 'parsererror') error = 'Requested JSON parse failed.';
  else if (exception === 'timeout') error = 'Time out error.';
  else error = 'Uncaught Error.\n' + jqXHR.responseText;
  alert(error);
}

// Cancel correct XHRs for both modes
$('#updateFYBalancesModal').on('hide.bs.modal', function () {
  if (get_fy_details_xhr) get_fy_details_xhr.abort();
  if (update_fy_balance_xhr) update_fy_balance_xhr.abort();  // account flow
  if (item_total_fetch_xhr) item_total_fetch_xhr.abort();    // items
  if (item_batch_xhr) item_batch_xhr.abort();                // items
});

// ================== Item Valuation (BATCHED) ==================
const ITEM_VAL_BATCH_SIZE = 500;
var fy_val_index = 0;              // keep your old vars (unused in batch mode)
var fy_val_list  = [];             // keep for compatibility

var get_fy_valuation_details_xhr  = null;  // initial "total" fetch
var update_fy_valuation_xhr       = null; // per-batch xhr

var val_total     = 0;   // total items to value
var val_processed = 0;   // already processed

// Open the Valuation modal and start batched processing
$(document).on('click', '.update_fy_valuation', function () {
  const type = $(this).data('type'); // should be "item_valuation"

  // Reset UI
  $('#updateFYValuationModal .modal-title').text(get_modal_title(type));
  $('#updateFYValuationModal .close_btn').text('Cancel')
    .removeClass('btn-success').addClass('btn-danger');
  $('#updateFYValuationModal .overall_status').text('Please wait while details are being fetched...');
  $('#updateFYValuationModal .current_status').text('');
  $('#updateFYValuationModal .progress-bar').css('width', '0%').text('0%');
  $('#updateFYValuationModal .faults').text('0');
  $('#updateFYValuationModal .voucher_list').html('');
  $('#updateFYValuationModal').modal('show');

  // Reset counters
  val_total = 0;
  val_processed = 0;

  // Fetch ONLY the total count (do not load all IDs in browser)
  get_fy_valuation_details_xhr = $.ajax({
    url: baseurl + 'admin/rewritebooks/load_details',
    type: 'POST',
    data: { type: 'item_valuation' },
    dataType: 'json',
    success: function (resp) {
      if (!resp.status) {
        $('#updateFYValuationModal .overall_status').text('Something went wrong');
        $('#updateFYValuationModal .current_status').text(resp.message || '');
        return;
      }
      val_total = parseInt(resp.total || 0, 10);
      $('#updateFYValuationModal .overall_status').text('Total Items: ' + val_total);

      // start first batch
      process_item_valuation_batch(0);
    },
    error: valuationXHRErrorHandler
  });
});

// Batch worker
function process_item_valuation_batch(offset) {
  const progress = val_total === 0 ? 100 : Math.round((val_processed / val_total) * 100);
  $('#updateFYValuationModal .progress-bar').css('width', progress + '%').text(progress + '%');

  if (val_total === 0 || val_processed >= val_total) {
    $('#updateFYValuationModal .current_status').text('All items are valued');
    $('#updateFYValuationModal .close_btn').text('Done')
      .removeClass('btn-danger').addClass('btn-success');
    return;
  }

  $('#updateFYValuationModal .current_status').text(
    'Processed ' + val_processed + ' / ' + val_total + ' (Batch of ' + ITEM_VAL_BATCH_SIZE + ')'
  );

  update_fy_valuation_xhr = $.ajax({
    url: baseurl + 'admin/rewritebooks/update_item_valuation_batch',
    type: 'POST',
    data: { offset: offset, limit: ITEM_VAL_BATCH_SIZE },
    dataType: 'json',
    success: function (resp) {
      if (!resp.status) {
        alert(resp.message || 'Unknown error');
        return;
      }

      if (resp.errors && resp.errors.length > 0) {
        resp.errors.forEach(function (e) {
          // e can be "string" or {name, error}
          const label = (typeof e === 'string') ? '' : (e.name || '');
          const msg   = (typeof e === 'string') ? e : (e.error || '');
          incrementValFaults(label, [msg]);
        });
      }

      const processed = parseInt(resp.processed || 0, 10);
      val_processed += processed;

      const nextOffset = offset + ITEM_VAL_BATCH_SIZE;

      const p2 = val_total === 0 ? 100 : Math.round((val_processed / val_total) * 100);
      $('#updateFYValuationModal .progress-bar').css('width', p2 + '%').text(p2 + '%');

      if (val_processed >= val_total || resp.done) {
        $('#updateFYValuationModal .current_status').text('All items are valued');
        $('#updateFYValuationModal .close_btn').text('Done')
          .removeClass('btn-danger').addClass('btn-success');
      } else {
        process_item_valuation_batch(nextOffset);
      }
    },
    error: valuationXHRErrorHandler
  });
}

// Error & faults helpers
function incrementValFaults(name, errs) {
  let count = parseInt($('#updateFYValuationModal .faults').text() || '0', 10);
  count += errs.length;
  $('#updateFYValuationModal .faults').text(count);

  errs.forEach(function (error) {
    const label = name ? (name + ' - ') : '';
    const html = '<li class="list-group-item">' + label + error + '</li>';
    $('#updateFYValuationModal .voucher_list').prepend(html);
  });
}

function valuationXHRErrorHandler(jqXHR, exception) {
  let error = '';
  if (jqXHR.status === 0 && exception === 'abort') error = 'Ajax request aborted.';
  else if (jqXHR.status === 0) error = 'Not connect.\n Verify Network.';
  else if (jqXHR.status == 404) error = 'Requested page not found. [404]';
  else if (jqXHR.status == 500) error = 'Internal Server Error [500].';
  else if (exception === 'parsererror') error = 'Requested JSON parse failed.';
  else if (exception === 'timeout') error = 'Time out error.';
  else error = 'Uncaught Error.\n' + jqXHR.responseText;
  alert(error);
}

// Abort in-flight xhrs if modal closed
$('#updateFYValuationModal').on('hide.bs.modal', function () {
  if (get_fy_valuation_details_xhr) get_fy_valuation_details_xhr.abort();
  if (update_fy_valuation_xhr)      update_fy_valuation_xhr.abort();
});



// Update Item TXN REP CALCULATION 
/*****************/
var list = [];
var index = 0;
var type = '';

var get_itmtxnrep_xhr;
$(document).on('click', '.item_rep_calculation', function(){
	type = $(this).data('type');
	list = [];
	index = 0;
	$('#updateBalancesModal .modal-title').text(get_modal_title(type));
	$('#updateBalancesModal .close_btn').text('Cancel');
    $('#updateBalancesModal .close_btn').removeClass('btn-success').addClass('btn-danger');
	$('#updateBalancesModal .overall_status').text('Please wait while details are being fetched...');
	$('#updateBalancesModal .current_status').text('');
	$('#updateBalancesModal .progress-bar').css('width', '0%');
	$('#updateBalancesModal .progress-bar').text('0%');
	$('#updateBalancesModal').modal('show');
	get_itmtxnrep_xhr = $.ajax({
        url: baseurl+'/admin/rewritebooks/load_details', 
        type: 'POST',
        data: {type: type},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {            
            if(response.status){
                list = response.list;
                var overall_status = 'Total Items: ' + list.length;
                $('#updateBalancesModal .overall_status').text(overall_status);
                update_itemtxnrep_calculation(index);
            }
            else{
            	$('#updateBalancesModal .overall_status').text('Something Went wrong');
            	$('#updateBalancesModal .current_status').text(response.message);
            }           
            
        },
        complete: function() {
            // stop_loader();
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
            alert(error);
        },
    });
});

var update_itmtxncalc_xhr;

$(document).on("click","#refreshbtn_bal", function(){
	var val_index = $(this).attr("data-ids");	
	update_itemtxnrep_calculation(val_index);
	
	//console.log("on refreehs button --->"+val_index);
	$(this).hide();
	
});

function update_itemtxnrep_calculation(indexss)
{
	var progress = Math.round((indexss/list.length)*100);
	$('#updateBalancesModal .progress-bar').css('width', progress+'%');
	$('#updateBalancesModal .progress-bar').text(progress+'%');

	if(indexss < list.length)
	{
		var current_status = `${(indexss+1)} / ${list.length} (${list[indexss].name})`;
		// $("#updateBalancesModal #refreshbtn_bal").attr("data-ids",(indexss+1));		
		$('#updateBalancesModal .current_status').text(current_status);
		
		update_itmtxncalc_xhr = $.ajax({
	        url: baseurl+'/admin/rewritebooks/update_itemtxnrep_calculation', 
	        type: 'POST',
	        data: {type: type, id: list[indexss].id},
	        dataType: "json",
			timeout: 10000, // 10 secods
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            
	            if(response.status){
	                indexss++;					
	                update_itemtxnrep_calculation(indexss);
					
	            }
	            else{
	            	alert(response.message);
	            }
	        },
	        complete: function() {
	            // stop_loader();
	        },
	        error: function (jqXHR, exception) {

	            var error_= '';
	            if (jqXHR.status == 404) {
	                error = 'Requested page not found. [404]';
	            } else if (jqXHR.status == 500) {
	                error = 'Internal Server Error [500].';
	            } else if (exception === 'parsererror') {
	                error = 'Requested JSON parse failed.';
	            } else if (exception === 'timeout') {
					var getlast_index = $("#updateBalancesModal #refreshbtn_bal").attr("data-ids");
					$("#updateBalancesModal #refreshbtn_bal").attr("data-ids",(indexss+1));								
					$("#updateBalancesModal #refreshbtn_bal").show();
	                error = 'Time out error.';
	            } else if (jqXHR.status === 0 && exception === 'abort') {
					
	                error = 'Ajax request aborted.';
	            } else if (jqXHR.status === 0) {
					//var getlast_index = $("#updateBalancesModal #refreshbtn_bal").attr("data-ids");
					
					 $("#updateBalancesModal #refreshbtn_bal").attr("data-ids",(indexss+1));
					$("#updateBalancesModal #refreshbtn_bal").show();
	                error = 'Not connect.\n Verify Network.';
	            } else {
	                error = 'Uncaught Error.\n' + jqXHR.responseText;
	            }
	            alert(error);
	        },
	    });
	}
	else{
        $('#updateBalancesModal .current_status').text('All items are updated');
		 $("#updateBalancesModal #refreshbtn_bal").attr("data-ids",0);
        $('#updateBalancesModal .close_btn').text('Done');
        $('#updateBalancesModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}


$("#updateBalancesModal").on('hide.bs.modal', function () {
	if(update_itmtxncalc_xhr)
		update_itmtxncalc_xhr.abort();  
    if(get_itmtxnrep_xhr)	
    	get_itmtxnrep_xhr.abort();
	
	$("#updateBalancesModal #refreshbtn").attr("data-id",'');
	$("#updateBalancesModal #refreshbtn").attr("data-methodid",'');	
					
});
	
			
</script>
</body>
</html>