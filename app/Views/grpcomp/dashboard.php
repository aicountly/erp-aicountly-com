<?php $header = array(  'title' => 'Dashboard' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>

<style>
  .bullet-item2 {
      height: 1rem;
      width: 2.5rem;
      border-radius: 5px;
  }

  select {
    border: none; /* Remove the border from the select element */
    outline: none; /* Remove the outline on focus (optional) */
    padding: 5px; /* Add padding to make it visually appealing */
  }
</style>

<style>
  .toggle-container {
      position: relative;
      width: 60px;
      height: 30px;
      background-color: #ccc;
      border-radius: 15px;
      cursor: pointer;
/*      margin-left:500px;*/
      float: right;
  }

  .toggle-handle {
      position: absolute;
      top: 50%;
      transform: translate(0, -50%);
      width: 30px;
      height: 30px;
      background-color: #fff;
      border-radius: 50%;
      transition: transform 0.3s ease;
  }

  .toggled .toggle-handle {
      transform: translate(100%, -50%);
  }
</style>

<style>
   div.facts-div{
    height: 251px;
    width: 478px;
    overflow-y: overlay;
    display: none;
   }

  /* width */
  div.facts-div::-webkit-scrollbar {
    width: 10px;
  }

  /* Track */
  div.facts-div::-webkit-scrollbar-track {
    box-shadow: inset 0 0 5px grey; 
    border-radius: 5px;
  }
   
  /* Handle */
  div.facts-div::-webkit-scrollbar-thumb {
    background: #aaf1ad;  
    border-radius: 5px;
  }

  /* Handle on hover */
  div.facts-div::-webkit-scrollbar-thumb:hover {
    background: #46bc2a; 
  }

  table.facts-table{
    box-shadow:0px 0px 4px #ccc; 
    padding:5px;
    border-radius:10px; 
    color:#fff;
  }
  table.facts-table tr:first-child th:first-child {
    border-top-left-radius: 10px;
  }
  table.facts-table tr:first-child th:last-child {
      border-top-right-radius: 10px;
  }
  table.facts-table tr:last-child td:first-child {
    border-bottom-left-radius: 10px;
  } 
  table.facts-table tr:last-child td:last-child {
      border-bottom-right-radius: 10px;
  }

  table.facts-table th:nth-child(1){
    background-color: #849583; 
    color: #fff;
  }
  table.facts-table th:nth-child(2){
    background-color: #46bc2a; 
    color: #fff;
  }
  table.facts-table th:nth-child(3){
    background-color: #2f8a9f; 
    color: #fff;
  }

  table.facts-table td:nth-child(1){
    background-color: #e3e3e3; 
    color: black;
  }
  table.facts-table td:nth-child(2){
    background-color: #aaf1ad; 
    color: black;
  }
  table.facts-table td:nth-child(3){
    background-color: #a8d6fa; 
    color: black;
  }

</style>

<style>
  table.key-facts-table{
    box-shadow:0px 0px 4px #ccc; 
    padding:5px;
    border-radius:10px; 
  }
  table.key-facts-table tr:first-child th:first-child {
    border-top-left-radius: 10px;
  }
  table.key-facts-table tr:first-child th:last-child {
      border-top-right-radius: 10px;
  }
  table.key-facts-table tr:last-child td:first-child {
    border-bottom-left-radius: 10px;
  } 
  table.key-facts-table tr:last-child td:last-child {
      border-bottom-right-radius: 10px;
  }

  table.key-facts-table th:nth-child(1){
    background-color: #69A0E0; 
    color: #fff;
  }
  table.key-facts-table th:nth-child(2){
    background-color: #DF8D7F; 
    color: #fff;
  }
  table.key-facts-table th:nth-child(3){
    background-color: #A487E4; 
    color: #fff;
  }
  table.key-facts-table th:nth-child(4){
    background-color: #8CB21F; 
    color: #fff;
  }
  table.key-facts-table th:nth-child(5){
    background-color: #ED7DEB; 
    color: #fff;
  }

  table.key-facts-table tbody tr:nth-child(odd){
    background-color: DADADA; 
    color: black;
  }
  table.key-facts-table tbody tr:nth-child(even){
    background-color: lightgray; 
    color: black;
  }
  table.key-facts-table tbody td{
    padding: 10px 10px;
  }

  table.key-facts-table td:nth-child(even){
    border-right: 2px solid #fff;
  }
  table.key-facts-table th{
    border-right: 2px solid #fff;
  }


</style>

<style>
/* 
.chart_loader_parent {
  align-items: center;
  background-color: #fff;
  display: flex;
  justify-content: center;
  height: 100%;
}

.chart_loader  {
  animation: rotate 1s infinite;  
  height: 50px;
  width: 50px;
}

.chart_loader:before,
.chart_loader:after {   
  border-radius: 50%;
  content: '';
  display: block;
  height: 20px;  
  width: 20px;
}
.chart_loader:before {
  animation: ball1 1s infinite;  
  background-color: #cb2025;
  box-shadow: 30px 0 0 #f8b334;
  margin-bottom: 10px;
}
.chart_loader:after {
  animation: ball2 1s infinite; 
  background-color: #00a096;
  box-shadow: 30px 0 0 #97bf0d;
} */
.table > tbody > tr > td:first-child{padding-left:10px;}
@keyframes rotate {
  0% { 
    -webkit-transform: rotate(0deg) scale(0.8); 
    -moz-transform: rotate(0deg) scale(0.8);
  }
  50% { 
    -webkit-transform: rotate(360deg) scale(1.2); 
    -moz-transform: rotate(360deg) scale(1.2);
  }
  100% { 
    -webkit-transform: rotate(720deg) scale(0.8); 
    -moz-transform: rotate(720deg) scale(0.8);
  }
}

</style>

<div class="row g-4">

  <div class="col-12 col-xxl-8" data-step="1" data-intro="Hello all! :) These are the Shortcuts for your Menus">
    <a href="<?php echo base_url();?>/grpcomp/reports/balance_sheet" title="Balance Sheet">
      <button type="button" class="btn btn-outline-success">BS</button></a>
    <a href="<?php echo base_url();?>/grpcomp/reports/profit_loss" title="Profit & Loss">
      <button type="button" class="btn btn-outline-success">PL</button></a>
    <a href="<?php echo base_url();?>/grpcomp/reports/trial_balance"  title="Trial Balance">
      <button type="button" class="btn btn-outline-success">TB</button></a>
    <a href="#" title="Day Book">
      <button type="button" class="btn btn-outline-success">DB</button></a>
    <a href="<?php echo base_url();?>/grpcomp/reports/account_ledger" title="Accounts Ledger">
      <button type="button" class="btn btn-outline-success">AL</button></a>
    <a href="<?php echo base_url();?>/grpcomp/reports/stock_ledger"  title="Stock Ledger">
      <button type="button" class="btn btn-outline-success">SL</button></a>
    <a href="<?php echo base_url();?>/grpcomp/reports/account_summary" title="Account Summary">
      <button type="button" class="btn btn-outline-success">AS</button></a>
    <a href="<?php echo base_url();?>/grpcomp/stock_summary" title="Stock Summary">
      <button type="button" class="btn btn-outline-success">SS</button></a>
    <a href="<?php echo base_url();?>/grpcomp/reports/stock_status" title="Inventory Status">
      <button type="button" class="btn btn-outline-success">IS</button></a>
  
  </div>
</div>
<br>

<div class="row g-4">

      <div class="col-12 col-xxl-12">
        <div class="row g-3">
          <div class="col-12 col-md-9">

            <div class="row">
              <div class="col-12 col-md-6">
                <div class="card" data-step="2" data-intro="Your Total Earning Amount">
                  <div class="card-body">
                    <div class="d-flex justify-content-between">
                      <div>
                        <h5 class="mb-1">Total Receivables<span class="badge bg-secondary fs--1 ms-2"><span class="badge-label">Latest</span></span></h5>
                        <!-- <h6 class="text-700">Last 30 days</h6> -->
                        <div class="">
                          <label>Last:</label>
                          <select id="trade_receivable_days" onchange="tradeReceivable()">
                              <option value="7">7 days</option>
                              <option value="15">15 days</option>
                              <option value="30" selected>30 days</option>
                              <option value="45">45 days</option>
                              <option value="60">60 days</option>
                              <option value="90">90 days</option>
                              <option value="120">120 days</option>
                              <option value="150">150 days</option>
                          </select>
                        </div>

                      </div>
                      <h4 id="trade_receivable_all"></h4>
                    </div>
                    
                      <div class="progress receivable_progress my-5 mybar" role="progressbar">
                        <div class="progress-bar progress-bar-striped bg-success notdue" style="width:30%"></div>
                        <div class="progress-bar progress-bar-striped bg-warning due" style="width:30%"></div>
                        <div class="progress-bar progress-bar-striped bg-primary overdue" style="width:30%"></div>
                        <div class="progress-bar progress-bar-striped bg-secondary rest" style="width:10%"></div>
                      </div>
          
        
                    <div class="mt-2">
                      <div class="d-flex align-items-center mb-2">
                        <div class="bullet-item2 bg-success me-2"></div>
                        <h5 class="text-900 fw-semi-bold flex-1 mb-0">Receipts Not Due</h5>
                        <h5 class="text-900 fw-semi-bold mb-0" id="trade_receivable_notdue"></h5>
                      </div>
                      <div class="d-flex align-items-center mb-2">
                        <div class="bullet-item2 bg-warning me-2"></div>
                        <h5 class="text-900 fw-semi-bold flex-1 mb-0">Current Receivables</h5>
                        <h5 class="text-900 fw-semi-bold mb-0" id="trade_receivable_due"></h5>
                      </div>
                      <div class="d-flex align-items-center mb-2">
                        <div class="bullet-item2 bg-primary me-2"></div>
                        <h5 class="text-900 fw-semi-bold flex-1 mb-0">Receivables Overdue</h5>
                        <h5 class="text-900 fw-semi-bold mb-0" id="trade_receivable_overdue"></h5>
                      </div>
                      <div class="d-flex align-items-center mb-2">
                        <div class="bullet-item2 bg-secondary me-2"></div>
                        <h5 class="text-900 fw-semi-bold flex-1 mb-0">Advances </h5>
                        <h5 class="text-900 fw-semi-bold mb-0" id="trade_receivable_rest"></h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                  <div class="card" data-step="3" data-intro="Your Total Payed Amount">
                    <div class="card-body">
                      <div class="d-flex justify-content-between">
                        <div>
                          <h5 class="mb-1">Total Payable<span class="badge bg-secondary fs--1 ms-2"><span class="badge-label">Latest</span></span></h5>
                          <!-- <h6 class="text-700">Last 30 days</h6> -->
                          <div class="">
                            <label>Last:</label>
                            <select id="trade_payable_days" onchange="tradePayable()">
                                <option value="7">7 days</option>
                                <option value="15">15 days</option>
                                <option value="30" selected>30 days</option>
                                <option value="45">45 days</option>
                                <option value="60">60 days</option>
                                <option value="90">90 days</option>
                                <option value="120">120 days</option>
                                <option value="150">150 days</option>
                            </select>
                          </div>
                        </div>
                        <h4 id="trade_payable_all"></h4>
                      </div>
                      
                        <div class="progress payable_progress my-5 mybar" role="progressbar">
                          <div class="progress-bar progress-bar-striped bg-success notdue" style="width:30%"></div>
                          <div class="progress-bar progress-bar-striped bg-warning due" style="width:30%"></div>
                          <div class="progress-bar progress-bar-striped bg-primary overdue" style="width:30%"></div>
                          <div class="progress-bar progress-bar-striped bg-secondary rest" style="width:10%"></div>
                        </div>
          
                      <div class="mt-2">
                        <div class="d-flex align-items-center mb-2">
                          <div class="bullet-item2 bg-success me-2"></div>
                          <h5 class="text-900 fw-semi-bold flex-1 mb-0">Payables Not Due</h5>
                          <h5 class="text-900 fw-semi-bold mb-0" id="trade_payable_notdue"></h5>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                          <div class="bullet-item2 bg-warning me-2"></div>
                          <h5 class="text-900 fw-semi-bold flex-1 mb-0">Current Payables</h5>
                          <h5 class="text-900 fw-semi-bold mb-0" id="trade_payable_due"></h5>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                          <div class="bullet-item2 bg-primary me-2"></div>
                          <h5 class="text-900 fw-semi-bold flex-1 mb-0">Payables Overdue</h5>
                          <h5 class="text-900 fw-semi-bold mb-0" id="trade_payable_overdue"></h5>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                          <div class="bullet-item2 bg-secondary me-2"></div>
                          <h5 class="text-900 fw-semi-bold flex-1 mb-0">Advances</h5>
                          <h5 class="text-900 fw-semi-bold mb-0" id="trade_payable_rest"></h5>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          
          <div class="col-12 col-md-3">
            <div class="card">
              <div class="card-header">
                <h3>Quick Assets</h3>
            
              </div>
              <div class="card-body">

                  <div class="row" style="height: 7vh">
                    <div class="col-md-1 mx-0 px-0">
                      <img src="<?php echo base_url();?>/public/assets/img/qa_bank.jpg">
                    </div>
                    <div class="col-md-5 mx-0 px-0">Bank Balance</div>
                    <div class="col-md-6 mx-0 px-0 text-end" id="qa_bank"></div>
                  </div>

                  <div class="row" style="height: 7vh">
                    <div class="col-md-1 mx-0 px-0">
                      <img src="<?php echo base_url();?>/public/assets/img/qa_limit.jpg">
                    </div>
                    <div class="col-md-5 mx-0 px-0">Limit Balance</div>
                    <div class="col-md-6 mx-0 px-0 text-end" id="qa_limit"></div>
                  </div>

                  <div class="row" style="height: 7vh">
                    <div class="col-md-1 mx-0 px-0">
                      <img src="<?php echo base_url();?>/public/assets/img/qa_cash.jpg">
                    </div>
                    <div class="col-md-5 mx-0 px-0">Cash in Hand</div>
                    <div class="col-md-6 mx-0 px-0 text-end" id="qa_cash"></div>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">

    <div class="col-12 col-xl-6"> 
    <div class="card">  
      <div class="card-header">
        <h3>Top 5 Expenses</h3>
        <p class="mb-1 text-700"></p>
      </div>
      <div class="card-body">
        <div class="chart_loader_grand_parent" style="width: 100%;height: 500px; display: block;">
          <div class="chart_loader_parent">
            <div class="chart_loader"></div>
          </div>
        </div>
        <div id="chartdiv" style="width: 100%;height: 500px; font-size: 0.7em; display: none;"></div>
      </div>
    </div>
    </div>

    <div class="col-12 col-xl-6"> 
      <div class="card"> 
        <div class="card-header">
          <h3>Cash and Cash Equivalants</h3>
          <p class="mb-1 text-700"></p>
        </div>
        <div class="card-body">
          <div class="chart_loader_grand_parent" style="width: 100%;height: 500px; display: block;">
            <div class="chart_loader_parent">
              <div class="chart_loader"></div>
            </div>
          </div>
            
            <canvas id="cashEquivalentChart" style="width: 100%;height: 500px; display:none"></canvas>
        </div>
      </div>
    </div>

  </div>

  <div class="row mb-4">

  <div class="col-12 col-xl-6"> 
    <div class="card"> 
      <div class="card-header">
        <h3>Receivable Facts
          <span id="receivable_facts_toggle" class="toggle-container" onclick="toggleDivs('receivable')">
            <span class="toggle-handle"></span>
          </span>
        </h3>
        
      </div>
      <div class="card-body">
        <div class="chart_loader_grand_parent" style="width: 100%;height: 251px; display: block;">
          <div class="chart_loader_parent">
            <div class="chart_loader"></div>
          </div>
        </div>
        <canvas id="receivableFactsChart" width="400" height="210"></canvas>
        <div id="receivableFactsTable" class="facts-div">
          <div id="receivableFactsTableLabel"></div>
          
          <div class="table-responsive">
            <table class="table facts-table">
              <thead>
                <tr>
                  <th>Party</th>
                  <th>Balance</th>
                  <th>Overdue</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-6"> 
    <div class="card"> 
      <div class="card-header">
        <h3>Payable Facts
          <span id="payable_facts_toggle" class="toggle-container" onclick="toggleDivs('payable')">
            <span class="toggle-handle"></span>
          </span>
        </h3>
      </div>
      <div class="card-body">
        <div class="chart_loader_grand_parent" style="width: 100%;height: 251px; display: block;">
          <div class="chart_loader_parent">
            <div class="chart_loader"></div>
          </div>
        </div>
        <canvas id="payableFactsChart" width="400" height="210"></canvas>
        <div id="payableFactsTable" class="facts-div">
          <div id="payableFactsTableLabel"></div>
          <div class="table-responsive">
            <table class="table facts-table">
              <thead>
                <tr>
                  <th>Party</th>
                  <th>Balance</th>
                  <th>Overdue</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

  </div>

    <div class="row mb-4">

  <div class="col-12 col-xl-6"> 
    <div class="card"> 
      <div class="card-header">
        <h3>Tax Payable Chart</h3>
        <p class="mb-1 text-700">dummy</p>
      </div>
      <div class="card-body">
        <img src="<?php echo base_url();?>/public/chart_images/tax.PNG">
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-6"> 
    <div class="card"> 
      <div class="card-header">
        <h3>Ratio Analysis</h3>
        <p class="mb-1 text-700">dummy</p>
      </div>
      <div class="card-body">
        <img src="<?php echo base_url();?>/public/chart_images/ratio.PNG">
      </div>
    </div>
  </div>

  </div>

  <div class="row mb-4">
  <div class="col-12 col-xl-12 "> 
    <div class="card"> 
      <div class="card-header">
        <h3>Key Facts</h3>
        <p class="mb-1 text-700"></p>
      </div>
      <div class="card-body">
        <!-- <img src="<?php //echo base_url();?>/public/chart_images/key_facts.PNG"> -->
        <div class="table-responsive">
            <table class="table key-facts-table">
              <thead>
                <tr>
                  <th colspan="2">Inventory</th>
                  <th colspan="2">TAX Credits</th>
                  <th colspan="2">TAX Payable</th>
                  <th colspan="2">Alerts</th>
                  <th colspan="2">Facts</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Out of Stock</td>
                  <td>0</td>
                  <td>IGST</td>
                  <td>0</td>
                  <td>GST</td>
                  <td>0</td>
                  <td>Compliances</td>
                  <td>0</td>
                  <td>Customers</td>
                  <td id="kf_customers">0</td>
                </tr>
                <tr>
                  <td>Low Stock Items</td>
                  <td>0</td>
                  <td>CGST</td>
                  <td>0</td>
                  <td>TDS/TCS</td>
                  <td>0</td>
                  <td>Due Taxes</td>
                  <td>0</td>
                  <td>Suppliers</td>
                  <td id="kf_suppliers">0</td>
                </tr>
                <tr>
                  <td>Item Groups</td>
                  <td id="kf_total_item_groups">0</td>
                  <td>SGST</td>
                  <td>0</td>
                  <td>Income Tax</td>
                  <td>0</td>
                  <td>Verifications</td>
                  <td>0</td>
                  <td>Missing HSN</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td>No. of Items</td>
                  <td id="kf_total_items">0</td>
                  <td>Cess</td>
                  <td>0</td>
                  <td>EPF/ESI</td>
                  <td>0</td>
                  <td>BRC/FIRC</td>
                  <td>0</td>
                  <td>Stock in Transit</td>
                  <td>0</td>
                </tr>
                <tr>
                  <td>Exceptions</td>
                  <td>0</td>
                  <td>Income Tax</td>
                  <td>0</td>
                  <td>Others</td>
                  <td>0</td>
                  <td>Audit Exceptions</td>
                  <td>0</td>
                  <td>No. of Employees</td>
                  <td>0</td>
                </tr>

              </tbody>
            </table>
          </div>
      </div>
    </div>
  </div>

  </div>
   
  <div class="row mb-4">
    
    <div class="col-12 col-xl-6"> 
      <div class="card">  
        <div class="card-header">
          <h3>Net Worth</h3>
          <p class="mb-1 text-700"></p>
        </div>
        <div class="card-body">
          <div class="chart_loader_grand_parent" style="width: 100%;height: 251px; display: block;">
            <div class="chart_loader_parent">
              <div class="chart_loader"></div>
            </div>
          </div>
          <canvas id="netWorthChart" width="400" height="210"></canvas>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-6"> 
      <div class="card">  
        <div class="card-header">
          <h3>Cash Flow</h3>
          <p class="mb-1 text-700"></p>
        </div>
        <div class="card-body">
          <div class="chart_loader_grand_parent" style="width: 100%;height: 251px; display: block;">
            <div class="chart_loader_parent">
              <div class="chart_loader"></div>
            </div>
          </div>
          <canvas id="cashFlowChart" width="400" height="210"></canvas>
        </div>
      </div>
    </div>

    
  </div>

  <div class="row mb-4">

    <div class="col-12 col-xl-6"> 
      <div class="card">  
        <div class="card-header">
          <h3>Revenue</h3>
          <p class="mb-1 text-700"></p>
        </div>
        <div class="card-body">
          <div class="chart_loader_grand_parent" style="width: 100%;height: 251px; display: block;">
            <div class="chart_loader_parent">
              <div class="chart_loader"></div>
            </div>
          </div>
          <canvas id="revenueChart" width="400" height="210"></canvas>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-6"> 
      <div class="card">  
        <div class="card-header">
          <h3>Profitability</h3>
          <p class="mb-1 text-700"></p>
        </div>
        <div class="card-body">
          <div class="chart_loader_grand_parent" style="width: 100%;height: 251px; display: block;">
            <div class="chart_loader_parent">
              <div class="chart_loader"></div>
            </div>
          </div>
          <canvas id="profitChart" width="400" height="210"></canvas>
        </div>
      </div>
    </div>

  </div>

             
   <div class="modal fade" id="FaqModal" tabindex="-1" aria-labelledby="FaqModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="" id="FaqModalLabel"><img src="<?php echo base_url();?>/public/assets/img/e-sahayak-slogo.png" style="width:80px;" alt=""/> E-Sahayak for Dashboard</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            
          <p>Check your list of Dashboard or modify them</p>
          <ul><li>Add/Edit or Delete a Company</li>
          <li>Manage your entire comapanies at single setp</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="ShortcutModal" tabindex="-1" aria-labelledby="ShortcutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="" id="ShortcutModalLabel"><img src="<?php echo base_url();?>/public/assets/img/icon5.png" style="width:80px;" alt=""/> Shortcuts for Dashboard</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Check your list of Shortcuts to manage your Dashboard</p>
          <ul><li>Add/Edit or Delete a Company</li>
          <li>Manage your entire comapanies at single setp</li>
          </ul>
        </div>
      </div>
    </div>
  </div>            
                

    <script>
        var month_keys =[<?php echo $month_keys;?>];
        var revenue_keys =[<?php echo $revenue_keys;?>];
        var profit_loss_keys =[<?php echo $profit_loss_keys;?>];
    </script>      
<?php $below_js = array('assets/js/Chart.bundle.min.js'); ?>
<?php echo view('includes/'.$folder_path.'footer_scripts',array('below_js' => $below_js)); ?>


<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->

<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<!-- Chart code -->
<script>

    function toggleDivs(facts) {
      if(facts == 'receivable'){
        var container = document.querySelector('#receivable_facts_toggle.toggle-container');
        container.classList.toggle('toggled');

        if (container.classList.contains('toggled')) {
          $('#receivableFactsChart').css('display', 'none');
          $('#receivableFactsTable').css('display', 'block');
        } else {
          $('#receivableFactsChart').css('display', 'block');
          $('#receivableFactsTable').css('display', 'none');
        }
      }
      if(facts == 'payable'){
        var container = document.querySelector('#payable_facts_toggle.toggle-container');
        container.classList.toggle('toggled');

        if (container.classList.contains('toggled')) {
          $('#payableFactsChart').css('display', 'none');
          $('#payableFactsTable').css('display', 'block');
        } else {
          $('#payableFactsChart').css('display', 'block');
          $('#payableFactsTable').css('display', 'none');
        }
      } 
    }
    
        function formatYAxes(range)
        {
            var sign = range < 0 ? -1 : 1;
            range = Math.abs(range);

            if (range >= 1000 && range <= 99999) {
                var result = parseInt(range /1000) * sign + ' K';
            } else if (range >= 100000 && range <= 9999999) {
                var result = parseInt(range /100000) * sign + ' L';
            } else if (range >= 10000000) {
                var result = parseInt(range /10000000) * sign + ' CR';
            }
            else{
              var result = parseInt(range) * sign;
            }

            return result;
        }

        function formatYAxisLabels(minValue, maxValue) {
            var range = maxValue - minValue;

            var sign = range < 0 ? -1 : 1;
            range = Math.abs(range);

            if (range >= 0 && range <= 9999) {
                var result = { min: 0, max: 10000, step: 1000, unit: 'k' };
            } else if (range >= 10000 && range <= 49999) {
                var result = { min: 10000, max: 50000, step: 5000, unit: 'k' };  
            } else if (range >= 50000 && range <= 99999) {
                var result = { min: 50000, max: 100000, step: 10000, unit: 'k' };

            } else if (range >= 100000 && range <= 999999) {
                var result = { min: 100000, max: 1000000, step: 100000, unit: 'lakh' };
            } else if (range >= 1000000 && range <= 4999999) {
                var result = { min: 1000000, max: 5000000, step: 500000, unit: 'lakh' };
            } else if (range >= 5000000 && range <= 9999999) {
                var result = { min: 5000000, max: 10000000, step: 1000000, unit: 'lakh' };

            } else if (range >= 10000000 && range <= 99999999) {
                var result = { min: 10000000, max: 100000000, step: 10000000, unit: 'cr' };
            } else if (range >= 100000000 && range <= 499999999) {
                var result = { min: 100000000, max: 500000000, step: 50000000, unit: 'cr' };
            } else if (range >= 500000000 && range <= 999999999) {
                var result = { min: 500000000, max: 1000000000, step: 100000000, unit: 'cr' };

            } else if (range >= 1000000000 && range <= 9999999999) {
                var result = { min: 1000000000, max: 10000000000, step: 1000000000, unit: 'cr' };
            } else if (range >= 10000000000 && range <= 49999999999) {
                var result = { min: 10000000000, max: 50000000000, step: 5000000000, unit: 'cr' };
            } else if (range >= 50000000000 && range <= 99999999999) {
                var result = { min: 50000000000, max: 100000000000, step: 10000000000, unit: 'cr' };
            } else {
                var result = { min: 100000000000, max: 1000000000000, step: 100000000000, unit: 'cr' };
            }

            result.min = result.min * sign;
            result.max = result.max * sign;
            result.step = result.step;

            return result;
        }

        document.addEventListener('DOMContentLoaded', function () {


        var ctx_cash_flow = document.getElementById('cashFlowChart').getContext('2d');
        var cashFlowChart;

        function fetchCashFlowData() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        var data = JSON.parse(this.responseText);

                        $('#cashFlowChart').siblings('.chart_loader_grand_parent').css('display', 'none');
                        $('#cashFlowChart').css('display', 'block');

                        // Destroy the previous chart instance if it exists
                        if (cashFlowChart) {
                            cashFlowChart.destroy();
                        }

                        // Find min and max values
                        const minValue = Math.min(...data.pink.map(item => item.total_amount), ...data.blue.map(item => item.total_amount));
                        const maxValue = Math.max(...data.pink.map(item => item.total_amount), ...data.blue.map(item => item.total_amount));


                        // Get y-axis labels format
                        const yAxisLabels = formatYAxisLabels(minValue, maxValue);

                        // Create a new chart instance
                        cashFlowChart = new Chart(ctx_cash_flow, {
                            type: 'line',
                            data: {
                                labels: data.pink.map(item => item.month),
                                datasets: [{
                                    label: 'Out Flow ₹',
                                    data: data.pink.map(item => item.total_amount),
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    borderWidth: 2,
                                    fill: 'origin',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    lineTension: 0.3
                                }, {
                                    label: 'In Flow ₹',
                                    data: data.blue.map(item => item.total_amount),
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 2,
                                    fill: 'origin',
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    lineTension: 0.3
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        ticks: {
                                            callback: function (value) {
                                                return '₹' + formatYAxes(value);
                                            },
                                            stepSize: yAxisLabels.step
                                        }
                                    },
                                },
                                interaction: {
                                  intersect: false,
                                },
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';

                                                if (label) {
                                                    label += ': ';
                                                }
                                                if (context.parsed.y !== null) {
                                                    label += formatForChart(context.parsed.y);
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                }
                                
                            }

                        });

                    } else {
                        console.error('Failed to fetch data. Status:', this.status);
                    }
                }
            };

            $('#cashFlowChart').siblings('.chart_loader_grand_parent').css('display', 'none');
            $('#cashFlowChart').css('display', 'block');

            xhr.open('GET', '<?= base_url('admin/dashboard/cashFlowChart'); ?>', true);
            xhr.send();
        }

        // Fetch data initially
        //fetchCashFlowData();

        
        var ctx_cash_equivalent = document.getElementById('cashEquivalentChart').getContext('2d');
        var cashEquivalentChart;

        function fetchCashEquivalentData() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {

                        $('#cashEquivalentChart').siblings('.chart_loader_grand_parent').css('display', 'none');
                        $('#cashEquivalentChart').css('display', 'block');

                        var data = JSON.parse(this.responseText);

                        // Destroy the previous chart instance if it exists
                        if (cashEquivalentChart) {
                            cashEquivalentChart.destroy();
                        }

                        // Find min and max values
                        const minValue = Math.min(...data.pink.map(item => item.total_amount), ...data.blue.map(item => item.total_amount));
                        const maxValue = Math.max(...data.pink.map(item => item.total_amount), ...data.blue.map(item => item.total_amount));


                        // Get y-axis labels format
                        const yAxisLabels = formatYAxisLabels(minValue, maxValue);

                        // Create a new chart instance
                        cashEquivalentChart = new Chart(ctx_cash_equivalent, {
                            type: 'line',
                            data: {
                                labels: data.pink.map(item => item.month),
                                datasets: [
                                 {
                                    label: 'Assets ₹',
                                    data: data.blue.map(item => item.total_amount),
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 2,
                                    fill: 'origin',
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    lineTension: 0.3
                                },
                                {
                                    label: 'Borrowing ₹',
                                    data: data.pink.map(item => item.total_amount),
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    borderWidth: 2,
                                    fill: 'origin',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    lineTension: 0.3
                                },
                              ]
                            },
                            options: {
                                scales: {
                                    y: {
                                        ticks: {
                                            callback: function (value) {
                                                return '₹' + formatYAxes(value);
                                            },
                                            stepSize: yAxisLabels.step
                                        }
                                    },
                                },
                                interaction: {
                                  intersect: false,
                                },
                                plugins: {
                                  tooltip: {
                                    callbacks: {
                                      label: function(context) {
                                        
                                        let label = context.dataset.label || '';

                                        if(label == 'Assets ₹'){
                                          month = context.label;
                                          index = data.blue.findIndex(function(obj) {
                                            return obj.month == month;
                                          });
                                          if(index > -1){
                                            var cash = data.blue[index].cash;
                                            var bank = data.blue[index].bank;
                                            var new_label = [
                                                'Assets ₹: ' + formatForChart(context.parsed.y),
                                                'Cash in hand ₹: ' + formatForChart(cash),
                                                'Bank Balance ₹: ' + formatForChart(bank),
                                              ]

                                            return new_label;
                                          }
                                        }

                                        if (label) {
                                          label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                          label += formatForChart(context.parsed.y);
                                        }
                                        return label;
                                      }
                                    }
                                  }
                                }
                                
                            }

                        });

                    } else {
                        console.error('Failed to fetch data. Status:', this.status);
                    }
                }
            };

            $('#cashEquivalentChart').siblings('.chart_loader_grand_parent').css('display', 'block');
            $('#cashEquivalentChart').css('display', 'none');

            xhr.open('GET', '<?= base_url('admin/dashboard/cashEquivalentChart'); ?>', true);
            xhr.send();
        }

        // Fetch data initially
      //  fetchCashEquivalentData();


       var ctx_revenue = document.getElementById('revenueChart').getContext('2d');
       var revenueChart;

       function fetchRevenueData() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        var data = JSON.parse(this.responseText);

                        $('#revenueChart').siblings('.chart_loader_grand_parent').css('display', 'none');
                        $('#revenueChart').css('display', 'block');

                        // Apply range transformation to data.prices
                      // Apply range transformation to data.prices
                        var range = formatYAxisLabels(
                            Math.min(...data.prices.map(price => parseFloat(price))),
                            Math.max(...data.prices.map(price => parseFloat(price)))
                        );

                        // data.prices = data.prices.map(function (price) {
                        //     return parseFloat(price.replace(',', '')) / range.step;
                        // });


                        // Destroy the previous chart instance if it exists
                        if (revenueChart) {
                            revenueChart.destroy();
                        }

                        // Create a new chart instance
                        revenueChart = new Chart(ctx_revenue, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: ' Revenue  ₹',
                                    data: data.prices,
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 2,
                                    pointRadius: 0,  // Hide data points
                                    fill: true,  // Enable filling below the line
                                    backgroundColor: gradientBackground(ctx_revenue, 'rgba(54, 162, 235, 0.2)', data.prices.length),
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        ticks: {
                                            callback: function (value) {
                                                return '₹' + formatYAxes(value);
                                            },
                                            stepSize: range.step
                                        }
                                    },
                                },
                                elements: {
                                    line: {
                                        tension: 0 // Adjust the tension for the curve
                                    }
                                },
                                interaction: {
                                  intersect: false,
                                },
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';

                                                if (label) {
                                                    label += ': ';
                                                }
                                                if (context.parsed.y !== null) {
                                                    label += formatForChart(context.parsed.y);
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                },
                            }
                        });
                    } else {
                        console.error('Failed to fetch data. Status:', this.status);
                    }
                }
            };

            $('#revenueChart').siblings('.chart_loader_grand_parent').css('display', 'block');
            $('#revenueChart').css('display', 'none');

            xhr.open('GET', '<?= base_url('admin/dashboard/revenueChart'); ?>', true);
            xhr.send();
        }

        // Fetch data initially
       // fetchRevenueData();

        // Function to create gradient background with decreasing opacity
        function gradientBackground(ctx, color, numPoints) {
            var gradient = ctx.createLinearGradient(0, 0, 0, ctx.canvas.height);
            for (var i = 0; i < numPoints; i++) {
                var opacity = 1 - i / (numPoints - 1);
                gradient.addColorStop(i / (numPoints - 1), color.replace('0.2', opacity.toFixed(2)));
            }
            return gradient;
        }


        var ctx_net_worth = document.getElementById('netWorthChart').getContext('2d');
        var netWorthChart;

        function fetchNetWorthData() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        var data = JSON.parse(this.responseText);

                        $('#netWorthChart').siblings('.chart_loader_grand_parent').css('display', 'none');
                        $('#netWorthChart').css('display', 'block');

                        // Destroy the previous chart instance if it exists
                        if (netWorthChart) {
                            netWorthChart.destroy();
                        }

                        // Find the min and max values from the data
                        const minValue = Math.min(...data.data);
                        const maxValue = Math.max(...data.data);

                        // Format the y-axis labels based on the given range
                        const yLabels = formatYAxisLabels(minValue, maxValue);

                        // Create a bar chart
                        netWorthChart = new Chart(ctx_net_worth, {
                            type: 'bar',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Data ₹',
                                    data: data.data,
                                    backgroundColor: [
                                        'rgba(75, 192, 192, 0.2)',
                                   
                                    ],
                                    borderColor: [
                                        'rgba(75, 192, 192, 1)',
                                       
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        ticks: {
                                            callback: function (value) {
                                                return '₹' + formatYAxes(value);
                                            },
                                            stepSize: yLabels.step
                                        }
                                    },

                                },
                                interaction: {
                                  intersect: false,
                                },
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';

                                                if (label) {
                                                    label += ': ';
                                                }
                                                if (context.parsed.y !== null) {
                                                    label += formatForChart(context.parsed.y);
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                },
                            }
                        });
                    } else {
                        console.error('Failed to fetch data. Status:', this.status);
                    }
                }
            };

            $('#netWorthChart').siblings('.chart_loader_grand_parent').css('display', 'block');
            $('#netWorthChart').css('display', 'none');

            xhr.open('GET', '<?= base_url('admin/dashboard/netWorthChart'); ?>', true);
            xhr.send();
        }

        // Fetch data initially
       // fetchNetWorthData();

        

        function fetchPieData() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        var data = JSON.parse(this.responseText);

                        $('#chartdiv').siblings('.chart_loader_grand_parent').css('display', 'none');
                        $('#chartdiv').css('display', 'block');

                        am4core.ready(function () {
                        // Themes begin
                        am4core.useTheme(am4themes_animated);
                        // Themes end

                        var chart = am4core.create("chartdiv", am4charts.PieChart3D);
                        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

                        chart.data = data; // Use PHP to encode the data

                        chart.innerRadius = am4core.percent(40);
                        chart.depth = 120;

                        chart.legend = new am4charts.Legend();

                        var series = chart.series.push(new am4charts.PieSeries3D());
                        series.dataFields.value = "amount";
                        series.dataFields.depthValue = "amount";
                        series.dataFields.category = "group";
                        series.slices.template.cornerRadius = 5;
                        series.colors.step = 3;
                    }); // end am4core.ready()  

                        

                    } else {
                        console.error('Failed to fetch data. Status:', this.status);
                    }
                }
            };

            $('#chartdiv').siblings('.chart_loader_grand_parent').css('display', 'block');
            $('#chartdiv').css('display', 'none');

            xhr.open('GET', '<?= base_url('admin/dashboard/pieChart'); ?>', true);
            xhr.send();
        }

        // Fetch data initially
       // fetchPieData();


        var ctx_profit = document.getElementById('profitChart').getContext('2d');
        var profitChart;

        function fetchProfitData() {

            // Function to determine color based on positive or negative values
            function getBarColor(value) {
                return value >= 0 ? 'rgba(75, 192, 192, 0.6)' : 'rgba(255, 0, 0, 0.6)';
            }
            function getBarColornet(value) {
                return value >= 0 ? 'rgba(0, 255, 0, 0.6)' : 'rgba(255, 0, 0, 0.6)';
            }

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        var data = JSON.parse(this.responseText);

                        $('#profitChart').siblings('.chart_loader_grand_parent').css('display', 'none');
                        $('#profitChart').css('display', 'block');

                        var months = data.months;
                        var grossProfitData = data.grossProfitData;
                        var netProfitData = data.netProfitData;


                        // Destroy the previous chart instance if it exists
                        if (profitChart) {
                            profitChart.destroy();
                        }

                        // Find min and max values
                        const minValue = Math.min(...data.grossProfitData, ...data.netProfitData);
                        const maxValue = Math.max(...data.grossProfitData, ...data.netProfitData);


                        // Get y-axis labels format
                        const yAxisLabels = formatYAxisLabels(minValue, maxValue);


                      var profitChart = new Chart(ctx_profit, {
                        type: 'bar',
                        data: {
                            labels: months,
                            datasets: [
                                {
                                    label: 'Gross Profit ₹',
                                    backgroundColor: grossProfitData.map(getBarColor),
                                    borderColor: 'rgba(75, 192, 192, 0.2)',
                                    borderWidth: 1,
                                    barThickness: 'flex', // Allows bars to start from zero
                                    data: grossProfitData
                                },
                                {
                                    label: 'Net Profit ₹',
                                    backgroundColor: netProfitData.map(getBarColornet),
                                    borderColor: 'rgba(0, 255, 0, 0.2)',
                                    borderWidth: 1,
                                    barThickness: 'flex', // Allows bars to start from zero
                                    data: netProfitData
                                },
                            ]
                        },
                        options: {
                            scales: {
                                x: {
                                    stacked: true,
                                    barPercentage: 10, // Ensure bars start from zero
                                    categoryPercentage: 1.0 // Ensure bars start from zero
                                },
                                y: {
                                  stacked: false,
                                  ticks: {
                                      callback: function (value) {
                                          return '₹' + formatYAxes(value);
                                      },
                                      stepSize: yAxisLabels.step
                                  }
                                },
                                
                                
                            },
                            interaction: {
                              intersect: false,
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';

                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += formatForChart(context.parsed.y);
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                        }
                    });
                    } else {
                        console.error('Failed to fetch data. Status:', this.status);
                    }
                }
            };

            $('#profitChart').siblings('.chart_loader_grand_parent').css('display', 'block');
            $('#profitChart').css('display', 'none');

            xhr.open('GET', '<?= base_url('admin/dashboard/profitChart'); ?>', true);
            xhr.send();
        }

       // // Fetch data initially
       // fetchProfitData();


        var ctx_receivable_facts = document.getElementById('receivableFactsChart').getContext('2d');
        var receivableFactsChart;

        function fetchReceivableFactsData() {

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        var data = JSON.parse(this.responseText);

                        $('#receivableFactsChart').siblings('.chart_loader_grand_parent').css('display', 'none');
                        $('#receivableFactsChart').css('display', 'block');

                        var months = data.months;
                        var from_date = data.from_date;
                        var to_date = data.to_date;

                        var withinDueData = data.withinDueData;
                        var overDueData = data.overDueData;
                        var advancesData = data.advancesData;


                        // Destroy the previous chart instance if it exists
                        if (receivableFactsChart) {
                            receivableFactsChart.destroy();
                        }

                        // Find min and max values
                        const minValue = Math.min(...data.withinDueData, ...data.overDueData, ...data.advancesData);
                        const maxValue = Math.max(...data.withinDueData, ...data.overDueData, ...data.advancesData);


                        // Get y-axis labels format
                        const yAxisLabels = formatYAxisLabels(minValue, maxValue);


                      var receivableFactsChart = new Chart(ctx_receivable_facts, {
                        type: 'bar',
                        data: {
                            labels: months,
                            datasets: [
                                {
                                    label: 'Within Due ₹',
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                    borderColor: 'rgba(75, 192, 192, 0.2)',
                                    borderWidth: 1,
                                    barThickness: 'flex', // Allows bars to start from zero
                                    data: withinDueData
                                },
                                {
                                    label: 'Overdue ₹',
                                    backgroundColor: 'rgba(0, 255, 0, 0.6)',
                                    borderColor: 'rgba(0, 255, 0, 0.2)',
                                    borderWidth: 1,
                                    barThickness: 'flex', // Allows bars to start from zero
                                    data: overDueData
                                },
                                {
                                    label: 'Advances ₹',
                                    backgroundColor: 'rgba(255, 0, 0, 0.6)',
                                    borderColor: 'rgba(255, 0, 0, 0.2)',
                                    borderWidth: 1,
                                    barThickness: 'flex', // Allows bars to start from zero
                                    data: advancesData
                                },
                            ]
                        },
                        options: {
                            scales: {
                                x: {
                                    stacked: true,
                                    barPercentage: 10, // Ensure bars start from zero
                                    categoryPercentage: 1.0 // Ensure bars start from zero
                                },
                                y: {
                                  stacked: false,
                                  ticks: {
                                      callback: function (value) {
                                          return '₹' + formatYAxes(value);
                                      },
                                      stepSize: yAxisLabels.step
                                  }
                                },
                                
                                
                            },
                            interaction: {
                              intersect: false,
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';

                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += formatForChart(context.parsed.y);
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            onClick: (event, elements, chart) => {
                              if (elements[0]) {            
                                 const i = elements[0].index;
                            
                                 // alert(chart.data.labels[i] + ': ' + chart.data.datasets[0].data[i]);
                                 accountsReceivable(chart.data.labels[i], from_date[i], to_date[i]);
                                 toggleDivs('receivable');
                              }
                            }
                        }
                    });
                    } else {
                        console.error('Failed to fetch data. Status:', this.status);
                    }
                }
            };

            $('#receivableFactsChart').siblings('.chart_loader_grand_parent').css('display', 'block');
            $('#receivableFactsChart').css('display', 'none');

            xhr.open('GET', '<?= base_url('admin/dashboard/receivableFactsChart'); ?>', true);
            xhr.send();
        }

        // Fetch data initially
       // fetchReceivableFactsData();


        var ctx_payable_facts = document.getElementById('payableFactsChart').getContext('2d');
        var payableFactsChart;

        function fetchPayableFactsData() {

            // Function to determine color based on positive or negative values
            function getBarColor(value) {
                return value >= 0 ? 'rgba(75, 192, 192, 0.6)' : 'rgba(255, 0, 0, 0.6)';
            }
            function getBarColornet(value) {
                return value >= 0 ? 'rgba(0, 255, 0, 0.6' : 'rgba(255, 0, 0, 0.6)';
            }

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        var data = JSON.parse(this.responseText);

                        $('#payableFactsChart').siblings('.chart_loader_grand_parent').css('display', 'none');
                        $('#payableFactsChart').css('display', 'block');

                        var months = data.months;
                        var from_date = data.from_date;
                        var to_date = data.to_date;

                        var withinDueData = data.withinDueData;
                        var overDueData = data.overDueData;
                        var advancesData = data.advancesData;


                        // Destroy the previous chart instance if it exists
                        if (payableFactsChart) {
                            payableFactsChart.destroy();
                        }

                        // Find min and max values
                        const minValue = Math.min(...data.withinDueData, ...data.overDueData, ...data.advancesData);
                        const maxValue = Math.max(...data.withinDueData, ...data.overDueData, ...data.advancesData);


                        // Get y-axis labels format
                        const yAxisLabels = formatYAxisLabels(minValue, maxValue);


                      var payableFactsChart = new Chart(ctx_payable_facts, {
                        type: 'bar',
                        data: {
                            labels: months,
                            datasets: [
                                {
                                    label: 'Within Due ₹',
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                    borderColor: 'rgba(75, 192, 192, 0.2)',
                                    borderWidth: 1,
                                    barThickness: 'flex', // Allows bars to start from zero
                                    data: withinDueData
                                },
                                {
                                    label: 'Overdue ₹',
                                    backgroundColor: 'rgba(0, 255, 0, 0.6)',
                                    borderColor: 'rgba(0, 255, 0, 0.2)',
                                    borderWidth: 1,
                                    barThickness: 'flex', // Allows bars to start from zero
                                    data: overDueData
                                },
                                {
                                    label: 'Advances ₹',
                                    backgroundColor: 'rgba(255, 0, 0, 0.6)',
                                    borderColor: 'rgba(255, 0, 0, 0.2)',
                                    borderWidth: 1,
                                    barThickness: 'flex', // Allows bars to start from zero
                                    data: advancesData
                                },
                            ]
                        },
                        options: {
                            scales: {
                                x: {
                                    stacked: true,
                                    barPercentage: 10, // Ensure bars start from zero
                                    categoryPercentage: 1.0 // Ensure bars start from zero
                                },
                                y: {
                                  stacked: false,
                                  ticks: {
                                      callback: function (value) {
                                          return '₹' + formatYAxes(value);
                                      },
                                      stepSize: yAxisLabels.step
                                  }
                                },
                                
                                
                            },
                            interaction: {
                              intersect: false,
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';

                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += formatForChart(context.parsed.y);
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            onClick: (event, elements, chart) => {
                              if (elements[0]) {            
                                 const i = elements[0].index;
                                 // alert(chart.data.labels[i] + ': ' + chart.data.datasets[0].data[i]);
                                 accountsPayable(chart.data.labels[i], from_date[i], to_date[i]);
                                 toggleDivs('payable');
                              }
                            }
                        }
                    });
                    } else {
                        console.error('Failed to fetch data. Status:', this.status);
                    }
                }
            };

            $('#payableFactsChart').siblings('.chart_loader_grand_parent').css('display', 'block');
            $('#payableFactsChart').css('display', 'none');

            xhr.open('GET', '<?= base_url('admin/dashboard/payableFactsChart'); ?>', true);
            xhr.send();
        }

        // Fetch data initially
       // fetchPayableFactsData();

        
    });

  function tradeReceivable() {

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status == 200) {
               var data = JSON.parse(this.responseText);

               $('#trade_receivable_all').html(formatAmount(data.all));
               $('#trade_receivable_notdue').html(formatAmount(data.notdue));
               $('#trade_receivable_due').html(formatAmount(data.due));
               $('#trade_receivable_overdue').html(formatAmount(data.overdue));
               $('#trade_receivable_rest').html(formatAmount(data.rest));

               var total = Math.round(data.notdue) + Math.round(data.due) + Math.round(data.overdue) + Math.round(data.rest);

               var notdue_p = Math.round(((data.notdue)/total)*100);
               var due_p = Math.round(((data.due)/total)*100);
               var overdue_p = Math.round(((data.overdue)/total)*100);
               var rest_p = Math.round(((data.rest)/total)*100);

              $('.receivable_progress .notdue').css('width', notdue_p+'%');
              $('.receivable_progress .notdue').text(notdue_p+'%');
              $('.receivable_progress .due').css('width', due_p+'%');
              $('.receivable_progress .due').text(due_p+'%');
              $('.receivable_progress .overdue').css('width', overdue_p+'%');
              $('.receivable_progress .overdue').text(overdue_p+'%');
              $('.receivable_progress .rest').css('width', rest_p+'%');
              $('.receivable_progress .rest').text(rest_p+'%');
            
            } else {
                console.error('Failed to fetch data. Status:', this.status);
            }
        }
    };

    $('#trade_receivable_all').html('');
    $('#trade_receivable_notdue').html('');
    $('#trade_receivable_due').html('');
    $('#trade_receivable_overdue').html('');
    $('#trade_receivable_rest').html('');

    $('.receivable_progress .notdue').css('width', '0%');
    $('.receivable_progress .notdue').text('0%');
    $('.receivable_progress .due').css('width', '0%');
    $('.receivable_progress .due').text('0%');
    $('.receivable_progress .overdue').css('width', '0%');
    $('.receivable_progress .overdue').text('0%');
    $('.receivable_progress .rest').css('width', '0%');
    $('.receivable_progress .rest').text('0%');

    var days = $('#trade_receivable_days').val();

    xhr.open('GET', '<?= base_url('admin/dashboard/tradeReceivable'); ?>?days='+days, true);
    xhr.send();
  }

  // Fetch data initially
 // tradeReceivable();

  function tradePayable() {

      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
              if (this.status == 200) {
                 var data = JSON.parse(this.responseText);

                 $('#trade_payable_all').html(formatAmount(data.all));
                 $('#trade_payable_notdue').html(formatAmount(data.notdue));
                 $('#trade_payable_due').html(formatAmount(data.due));
                 $('#trade_payable_overdue').html(formatAmount(data.overdue));
                 $('#trade_payable_rest').html(formatAmount(data.rest));

                 var total = Math.round(data.notdue) + Math.round(data.due) + Math.round(data.overdue) + Math.round(data.rest);

                 var notdue_p = Math.round(((data.notdue)/total)*100);
                 var due_p = Math.round(((data.due)/total)*100);
                 var overdue_p = Math.round(((data.overdue)/total)*100);
                 var rest_p = Math.round(((data.rest)/total)*100);

                $('.payable_progress .notdue').css('width', notdue_p+'%');
                $('.payable_progress .notdue').text(notdue_p+'%');
                $('.payable_progress .due').css('width', due_p+'%');
                $('.payable_progress .due').text(due_p+'%');
                $('.payable_progress .overdue').css('width', overdue_p+'%');
                $('.payable_progress .overdue').text(overdue_p+'%');
                $('.payable_progress .rest').css('width', rest_p+'%');
                $('.payable_progress .rest').text(rest_p+'%');
              
              } else {
                  console.error('Failed to fetch data. Status:', this.status);
              }
          }
      };

      $('#trade_payable_all').html('');
      $('#trade_payable_notdue').html('');
      $('#trade_payable_due').html('');
      $('#trade_payable_overdue').html('');
      $('#trade_payable_rest').html('');

      $('.payable_progress .notdue').css('width', '0%');
      $('.payable_progress .notdue').text('0%');
      $('.payable_progress .due').css('width', '0%');
      $('.payable_progress .due').text('0%');
      $('.payable_progress .overdue').css('width', '0%');
      $('.payable_progress .overdue').text('0%');
      $('.payable_progress .rest').css('width', '0%');
      $('.payable_progress .rest').text('0%');

      var days = $('#trade_payable_days').val();

      xhr.open('GET', '<?= base_url('admin/dashboard/tradePayable'); ?>?days='+days, true);
      xhr.send();
  }

  //tradePayable();

  //tradeReceivable();

  function quickAssets() {

      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
              if (this.status == 200) {
                 var data = JSON.parse(this.responseText);

                  $('#qa_bank').html(formatAmount(data.qa_bank));
                  $('#qa_limit').html(formatAmount(data.qa_limit));
                  $('#qa_cash').html(formatAmount(data.qa_cash));
              
              } else {
                  console.error('Failed to fetch data. Status:', this.status);
              }
          }
      };

      $('#qa_bank').html('');
      $('#qa_limit').html('');
      $('#qa_cash').html('');

      xhr.open('GET', '<?= base_url('admin/dashboard/quickAssets'); ?>', true);
      xhr.send();
  }

  // Fetch data initially
  //quickAssets();

  function accountsReceivable(month = '', from_date = '', to_date = '') {

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status == 200) {
               var data = JSON.parse(this.responseText);

               var html = ``;
               if(data.length > 0)
               {
                  $.each(data, function(index, object){
                    html += `
                      <tr>
                        <td>${object.account_name}</td>
                        <td>${object.balance}</td>
                        <td>${object.overdue}</td>
                      </tr>
                    `;
                  });

                 $('#receivableFactsTable table tbody').html(html);
               }

               if(month != ''){
                  var label = `<span class="badge bg-secondary ms-2"><span class="badge-label">${month}</span></span>`;
                  $('#receivableFactsTableLabel').html(label);
               }
               
            
            } else {
                console.error('Failed to fetch data. Status:', this.status);
            }
        }
    };

    $('#receivableFactsTable table tbody').html('');
    $('#receivableFactsTableLabel').html('');


    xhr.open('GET', '<?= base_url('admin/dashboard/accountsReceivable'); ?>?from_date='+from_date+'&to_date='+to_date, true);
    xhr.send();
  }

  // Fetch data initially
  //accountsReceivable();

  function accountsPayable(month = '', from_date = '', to_date = '') {

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status == 200) {
               var data = JSON.parse(this.responseText);

               var html = ``;
               if(data.length > 0)
               {
                  $.each(data, function(index, object){
                    html += `
                      <tr>
                        <td>${object.account_name}</td>
                        <td>${object.balance}</td>
                        <td>${object.overdue}</td>
                      </tr>
                    `;
                  });

                 $('#payableFactsTable table tbody').html(html);
               }

               if(month != ''){
                  var label = `<span class="badge bg-secondary ms-2"><span class="badge-label">${month}</span></span>`;
                  $('#payableFactsTableLabel').html(label);
               }
               
               
            
            } else {
                console.error('Failed to fetch data. Status:', this.status);
            }
        }
    };

    $('#payableFactsTable table tbody').html('');
    $('#payableFactsTableLabel').html('');


    xhr.open('GET', '<?= base_url('admin/dashboard/accountsPayable'); ?>?from_date='+from_date+'&to_date='+to_date, true);
    xhr.send();
  }

  // Fetch data initially
  //accountsPayable();

  function keyFacts() {

      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
              if (this.status == 200) {
                 var data = JSON.parse(this.responseText);

                  $('#kf_customers').html(data.kf_customers);
                  $('#kf_suppliers').html(data.kf_suppliers);
                  $('#kf_total_items').html(data.kf_total_items);
                  $('#kf_total_item_groups').html(data.kf_total_item_groups);
              
              } else {
                  console.error('Failed to fetch data. Status:', this.status);
              }
          }
      };

      $('#kf_customers').html('');
      $('#kf_suppliers').html('');
      $('#kf_total_items').html('');
      $('#kf_total_item_groups').html('');

      xhr.open('GET', '<?= base_url('admin/dashboard/keyFacts'); ?>', true);
      xhr.send();
  }

  // Fetch data initially
 // keyFacts();
</script>