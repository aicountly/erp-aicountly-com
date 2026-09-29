<?php $header = array(  'title' => 'Dashboard' ); ?>
<?php echo view('includes/header',$header);?>
<style>
.introjs-tooltip {
  background: #ffffff;
  border: 1px solid #ccc;
  border-radius: 10px;
  padding: 20px;
  max-width: 400px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  color: #333;
  font-family: 'Segoe UI', sans-serif;
  font-size: 15px;
}
.introjs-tooltiptext {
  margin-bottom: 15px;
  font-weight: 500;
}
.introjs-tooltipbuttons {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
.introjs-button {
  padding: 6px 14px;
  font-size: 14px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
  border: none;
}

.introjs-button:hover {
  opacity: 0.85;
}

.introjs-skipbutton {
  background-color: #e0e0e0;
  color: #333;
}

.introjs-prevbutton {
  background-color: #d0e2ff;
  color: #25b003;
}

.introjs-nextbutton {
  background-color: #d0e2ff;
  color: #25b003;
}
.introjs-arrow.top{
     border-bottom-color: #fff!important; 
}
.introjs-helperNumberLayer {
  position: absolute;
  top: -18px !important;
  left: -16px !important;
  z-index: 999999999 !important;
  width: 29px;
  height: 30px;
  padding:0;
  background: #25b003;
  color: #fff;
  border-radius: 50%;
  font-family: 'Segoe UI', sans-serif;
  font-size: 14px;
  font-weight: 600;
  text-align: center;
  line-height: 28px;
  box-shadow: 0 0 6px rgba(0, 0, 0, 0.3);
  border: 2px solid #ffffff;
  transition: all 0.3s ease-in-out;
}
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
   div.facts-div{
    height: 250px;
    width: 100%;
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
    background-color: #939c9c; 
    color: #fff;
  }
  table.facts-table th:nth-child(2){
    background-color:#00f400; 
    color: #fff;
  }
  table.facts-table th:nth-child(3){
    background-color: #85d3d3; 
    color: #fff;
  }

  table.facts-table td:nth-child(1){
    background-color:#d1d6d6; 
    color: black;
  }
  table.facts-table td:nth-child(2){
    background-color: #66ff66; 
    color: black;
  }
  table.facts-table td:nth-child(3){
    background-color: #93d9d9; 
    color: black;
  }

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
table.facts-table th{position:relative;}
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
}
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

/* Style the table cell */
.hover-cell {
    position: relative;  /* Positioning the icon inside the cell */
    padding-left: 20px;  /* Ensure space for the icon */
}

/* The icon that will be added on hover */
.hover-cell i {
    position: absolute;
    left: 100px;  /* Position the icon on the left side */
    top: 80%;
    transform: translateY(-50%);
    opacity: 0;  /* Initially hidden */
    transition: opacity 0.3s;  /* Smooth transition */
	
}

/* Make the icon visible on hover */
.hover-cell:hover i {
    opacity: 1;
	background-image: url(<?php echo base_url();?>public/assets/img/icon-view.png);
    background-repeat: no-repeat;
    background-position: 0px;
    width: 24px;
}

.eye-icon {
    position: absolute;
    right: 10px;
    top: 10px;
    z-index: 1000;
}
.eye-icon {
    pointer-events: auto;
    cursor: pointer;
}
.tooltip {
    pointer-events: none;
}
</style>

<div class="row g-4">

  <div class="col-12 col-xxl-8" data-step="1" data-intro="Hello all! :) These are the Shortcuts for your Menus">
    <a href="<?php echo base_url();?>admin/reports/balance_sheet" title="Balance Sheet"><button type="button" class="btn btn-outline-success">BS</button></a>
    <a href="<?php echo base_url();?>admin/reports/profit_loss" title="Profit & Loss"><button type="button" class="btn btn-outline-success">PL</button></a>
    <a href="<?php echo base_url();?>admin/reports/trial_balance"  title="Trial Balance"><button type="button" class="btn btn-outline-success">TB</button></a>
    <a href="<?php echo base_url();?>admin/reports/day_book" title="Day Book"><button type="button" class="btn btn-outline-success">DB</button></a>
    <a href="<?php echo base_url();?>admin/reports/account_ledger" title="Accounts Ledger"><button type="button" class="btn btn-outline-success">AL</button></a>
    <a href="<?php echo base_url();?>admin/reports/stock_ledger"  title="Stock Ledger"><button type="button" class="btn btn-outline-success">SL</button></a>
    <a href="<?php echo base_url();?>admin/reports/account_summary" title="Account Summary"><button type="button" class="btn btn-outline-success">AS</button></a>
    <a href="<?php echo base_url();?>admin/stock_summary" title="Stock Summary"><button type="button" class="btn btn-outline-success">SS</button></a>
    <a href="<?php echo base_url();?>admin/reports/stock_status" title="Inventory Status"><button type="button" class="btn btn-outline-success">IS</button></a>
  
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
                        <div class="bullet-item2 bg-primary me-2"></div>
                        <h5 class="text-900 fw-semi-bold flex-1 mb-0">Receivables Overdue</h5>
                        <h5 class="text-900 fw-semi-bold mb-0" id="trade_receivable_overdue"></h5>
                      </div>
                      <div class="d-flex align-items-center mb-2">
                        <div class="bullet-item2 bg-warning me-2"></div>
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
                          <div class="bullet-item2 bg-primary me-2"></div>
                          <h5 class="text-900 fw-semi-bold flex-1 mb-0">Payables Overdue</h5>
                          <h5 class="text-900 fw-semi-bold mb-0" id="trade_payable_overdue"></h5>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                          <div class="bullet-item2 bg-warning me-2"></div>
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
              
              <div class="card-body">
                <h5 class="text-center">Quick Assets</h5>

                  <div class="row mt-3" style="">
                    <div class="col-md-2 m-0 p-0">
                      <img src="<?php echo base_url();?>public/assets/img/qa_bank.jpg">
                    </div>
                    <div class="col-md-10 m-0 p-0 hover-cell" data-id="bankbalance_div" data-bs-placement="bottom" data-bs-toggle="tooltip" title="">
                      <div>Bank Balance</div>
                      <div class="text-end" id="qa_bank">0.00</div>
                    </div>
                    
                  </div>
                  <hr>
                  <div class="row" style="">
                    <div class="col-md-2 mx-0 px-0">
                      <img src="<?php echo base_url();?>public/assets/img/qa_limit.jpg">
                    </div>
                    <div class="col-md-10 m-0 p-0 hover-cell" data-id="limitbalance_div"  data-bs-placement="bottom" data-bs-toggle="tooltip" title="">
                      <div>Limit Balance</div>
                      <div class="text-end" id="qa_limit">0.00</div>
                    </div>
                  </div>
                  <hr>
                  <div class="row" style="">
                    <div class="col-md-2 m-0 p-0">
                      <img src="<?php echo base_url();?>public/assets/img/qa_cash.jpg">
                    </div>
                    <div class="col-md-10 m-0 p-0 hover-cell" data-id="cashinhand_div" data-bs-placement="bottom" data-bs-toggle="tooltip" title="">
                      <div>Cash in Hand</div>
                      <div class="text-end" id="qa_cash">0.00</div>
                    </div>
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

  </div>

  <div class="row mb-4" style="display:none;">

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
                  <th role="button" class="sort_column" data-column="balance" data-order="">Balance
                    <div class="d-flex flex-column sort_icons">
                      <span class="material-symbols-outlined sort_asc">arrow_drop_up</span>
                      <span class="material-symbols-outlined sort_desc">arrow_drop_down</span> 
                    </div>
                  </th>
                  <th role="button" class="sort_column" data-column="overdue" data-order="">Overdue
                    <div class="d-flex flex-column sort_icons">
                      <span class="material-symbols-outlined sort_asc">arrow_drop_up</span>
                      <span class="material-symbols-outlined sort_desc">arrow_drop_down</span> 
                    </div>
                  </th>
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
                  <th role="button" class="sort_column" data-column="balance" data-order="">Balance
                    <div class="d-flex flex-column sort_icons">
                      <span class="material-symbols-outlined sort_asc">arrow_drop_up</span>
                      <span class="material-symbols-outlined sort_desc">arrow_drop_down</span> 
                    </div>
                  </th>
                  <th role="button" class="sort_column" data-column="overdue" data-order="">Overdue
                    <div class="d-flex flex-column sort_icons">
                      <span class="material-symbols-outlined sort_asc">arrow_drop_up</span>
                      <span class="material-symbols-outlined sort_desc">arrow_drop_down</span> 
                    </div>
                  </th>
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
          <h4 class="" id="FaqModalLabel"><img src="<?php echo base_url();?>public/assets/img/e-sahayak-slogo.png" style="width:80px;" alt=""/> E-Sahayak for Dashboard</h4>
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
          <h4 class="" id="ShortcutModalLabel"><img src="<?php echo base_url();?>public/assets/img/icon5.png" style="width:80px;" alt=""/> Shortcuts for Dashboard</h4>
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
             

<div id="txn_myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" keyboard="true" backdrop="true" class="modal fade text-left">
            <div role="document" class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                 <div class="modal-header">
        <h4 class="modal-title">Voucher Transactions</h4>
        <button type="button" class="btn-close clostxnmodal" ></button>
      </div>		

					     
                    <div class="modal-body p-0"><br>
					<div class="container">
                     <div class="row justify-center-center">
					
					  	<div class="col-12 justify-content-center">
							<div id="grid_tab_wise" style="margin-top:10px;"></div>
					   </div>
					  
				</div>
				</div>
				
      
     <br><br><br>
					</div>
                    
                </div>
            </div>
        </div>			 

    <script>
        var month_keys =[];
        var revenue_keys =[];
        var profit_loss_keys =[];
    </script>      
<?php $below_js = array('assets/js/Chart.bundle.min.js'); ?>
<?php echo view('includes/footer_scripts',array('below_js' => $below_js)); ?>

<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<!-- Chart code -->

<script>
let abortController;
$(document).on("click", ".clostxnmodal",function(){
	$("#txn_myModal").modal("hide");
	if (abortController) {
        abortController.abort(); 
      
    }
  });	
$(document).on("mouseenter", ".hover-cell", function() {
    if($("#qa_bank").html().trim() !== "0.00"){
		var dataIndx = $(this).data("id");
	if(dataIndx=='bankbalance_div'){
		var groupname     = 'Bank Balance';
		var type          = 'bank';
	
		}
	if(dataIndx=='limitbalance_div'){
		var groupname     = 'Limit Balance';
		var type          = 'limit';
	
	}
	if(dataIndx=='cashinhand_div'){
		var groupname     = 'Cash In Hand';
		var type          = 'cash';
		
	}
		
   $(this).prepend('<i class="show_txn_modal eye-icon" style="cursor:pointer"  data-groupname="'+groupname+'" data-type="'+type+'" data-dataIndx="'+dataIndx+'">&nbsp;</i>'); 		

        
	}
});


$(document).on("click", ".show_txn_modal", function(e) {
	 e.stopPropagation();
	var dataIndx      = $(this).attr("data-dataIndx");	
	var type          = $(this).attr("data-type");
	var groupname     = $(this).attr("data-groupname");	
	$("#txn_myModal .modal-title").html(groupname);
	$("#txn_myModal").modal("show");
	var fromdate  ='<?php echo $from_date;?>';
	var todate    ='<?php echo $to_date;?>';
	// load_account_group_list_info(type,fromdate,todate,0);
	 
  });
  
$(document).on("mouseleave", ".hover-cell", function() {
      $(this).find("i").remove();
})

/* function AccountGrpLst_calculateSummary() {
        var opTotal = 0,
            debitTotal = 0,
            creditTotal = 0,
            balanceTotal = 0,
            balanceType = '',
            data = this.option('dataModel.data'),
            len = data.length;

        data.forEach(function(row){
            
            debitTotal += row.debit_total;
            creditTotal += row.credit_total;
            balanceTotal += row.balance_total;
            opTotal += row.op_balance_total;
            
        })

        if(balanceTotal >= 0)
            balanceType = 'DR';
        if(balanceTotal < 0)
            balanceType = 'CR';

        var opBalance = '';
        if(opTotal >= 0)
            opBalance = formatAmount(opTotal) + ' DR';
        if(opTotal < 0)
            opBalance = formatAmount(Math.abs(opTotal)) + ' CR';

        balanceTotal = Math.abs(balanceTotal)

        var totalData = {
                entity_name: "Total",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                balance: formatAmount(balanceTotal),
                balance_type: balanceType,
                op_balance: opBalance,
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
var AccountGrpLst_colModel = [
            { title: "ACCOUNT", dataIndx: "entity_name", render: filterRender},
            { title: "DEBIT",  align: "right",dataIndx: "debit"},
            { title: "CREDIT",  align: "right", dataIndx: "credit"},
            { title: "BALANCE", align: "right", dataIndx: "balance"},
            { title: "",  dataType: "string", dataIndx: "balance_type"}		   
	 	    ];
        var AccountGrpLst_dataModel = {"data":[]}
        
        var AccountGrpLst_newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            dataModel: AccountGrpLst_dataModel,
            dataReady: AccountGrpLst_calculateSummary,
            colModel: AccountGrpLst_colModel,  
            numberCell: { show: false },
            filterModel: { mode: 'OR' },
            editable: false,
            showTitle: true,            
        };
        
        
        AccountGrpLst_newObj.rowDblClick = function(event, ui) {
  	           var rowData      = ui.rowData;
		       var from_date         = rowData.from_date;
               var to_date         = rowData.to_date;
		       var account_id   = rowData.account_id;
		       
	     }
	     
	    AccountGrpLst_newObj.cellKeyDown= function(evt, ui) {
	             var rowData     = ui.rowData;
		         var from_date         = rowData.from_date;
                 var to_date         = rowData.to_date;
		         var account_id   = rowData.account_id;
		         if(evt.keyCode==13){
		           
		         }
		   
	       }	 */
/* async function load_account_group_list_info(type,fromdate,todate,parent) {
	 
    try {
		if ($("#grid_tab_wise").pqGrid('instance')) {
		  $("#grid_tab_wise").pqGrid('showLoading');
		}
        abortController = new AbortController();
        const { signal } = abortController;
        let response = await fetch('<?php //echo base_url();?>admin/dashboard/ajax_quickassets_data_info/'+type+'/'+fromdate+'/'+todate+'/'+parent,{ signal });
        if (!response.ok) {
			$("#grid_tab_wise").pqGrid('hideLoading');
            throw new Error('Network response was not ok');
        }
        let result = await response.json();
        if (result && result.data) {	
			
            if ($("#grid_tab_wise").pqGrid('instance')) {                  			
                $("#grid_tab_wise").pqGrid('refresh');
				$("#grid_tab_wise").pqGrid('option', 'height', 420);
				$("#grid_tab_wise").pqGrid('option', 'colModel', AccountGrpLst_colModel);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
            } else {					
                $("#grid_tab_wise").pqGrid(AccountGrpLst_newObj);
				$("#grid_tab_wise").pqGrid('option', 'height', 420);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
              }
			
        } else {
            console.log('No data available for this group.');
        }
    } catch (error) {
        console.error('Error loading account group monthly info:', error);
    } finally {
      $("#grid_tab_wise").pqGrid('hideLoading');
    }
}
 */





  var xhrObjJson = {};

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
                var result = parseInt(range /1000) * sign + ' k';
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

     
        var ctx_cash_flow = document.getElementById('cashFlowChart').getContext('2d');
        var cashFlowChart;

      function fetchCashFlowData() {
            xhrObjJson['xhrCashFlow']= new XMLHttpRequest();

            xhrObjJson['xhrCashFlow'].onreadystatechange = function () {
				
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

            xhrObjJson['xhrCashFlow'].open('GET', '<?= base_url('admin/dashboard/cashFlowChart'); ?>', true);
            xhrObjJson['xhrCashFlow'].send();
        }

       

        
        var ctx_cash_equivalent = document.getElementById('cashEquivalentChart').getContext('2d');
        var cashEquivalentChart;

        function fetchCashEquivalentData() {
            xhrObjJson['xhrCashExqui'] = new XMLHttpRequest();
            xhrObjJson['xhrCashExqui'].onreadystatechange = function () {
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

            xhrObjJson['xhrCashExqui'].open('GET', '<?= base_url('admin/dashboard/cashEquivalentChart'); ?>', true);
            xhrObjJson['xhrCashExqui'].send();
        }

        


        var ctx_revenue = document.getElementById('revenueChart').getContext('2d');
var revenueChart;

function fetchRevenueData() {
    xhrObjJson['xhrRevenue'] = new XMLHttpRequest();
    xhrObjJson['xhrRevenue'].onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status == 200) {
                var data = JSON.parse(this.responseText);

                $('#revenueChart').siblings('.chart_loader_grand_parent').css('display', 'none');
                $('#revenueChart').css('display', 'block');

                // Apply range transformation to get step size
                var range = formatYAxisLabels(
                    Math.min(...data.prices.map(price => parseFloat(price))),
                    Math.max(...data.prices.map(price => parseFloat(price)))
                );

                // Destroy previous chart if it exists
                if (revenueChart) {
                    revenueChart.destroy();
                }

                const expenses = Array.isArray(data.pricesExpense)
    ? data.pricesExpense.map(p => -Math.abs(parseFloat(p)))
    : new Array(data.labels.length).fill(0);

                // Create new Chart.js instance with segment coloring
                revenueChart = new Chart(ctx_revenue, {
    type: 'line',
    data: {
        labels: data.labels,
        datasets: [
            {
                label: 'Revenue ₹',
                data: data.prices.map(p => parseFloat(p)),
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 2,
                pointRadius: 0,
                fill: true,
                tension: 0.3
            },
            {
                label: 'Expense ₹',
                data: expenses,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderWidth: 2,
                pointRadius: 0,
                fill: true,
                tension: 0.3
            }
        ]
    },
    options: {
        scales: {
            y: {
                ticks: {
                    callback: function (value) {
                        return '₹' + formatYAxes(value);
                    },
                    beginAtZero: true
                }
            }
        },
        elements: {
            line: {
                tension: 0
            }
        },
        interaction: {
            intersect: false,
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function (context) {
                        let label = context.dataset.label || '';
                        if (label) label += ': ';
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

    $('#revenueChart').siblings('.chart_loader_grand_parent').css('display', 'block');
    $('#revenueChart').css('display', 'none');

    xhrObjJson['xhrRevenue'].open('GET', '<?= base_url('admin/dashboard/revenueChart'); ?>', true);
    xhrObjJson['xhrRevenue'].send();
}


        

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
            xhrObjJson['xhrNetWorth'] = new XMLHttpRequest();
            xhrObjJson['xhrNetWorth'].onreadystatechange = function () {
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

            xhrObjJson['xhrNetWorth'].open('GET', '<?= base_url('admin/dashboard/netWorthChart'); ?>', true);
            xhrObjJson['xhrNetWorth'].send();
        }

        

        

     function fetchPieData() {
            xhrObjJson['xhrPie'] = new XMLHttpRequest();
            xhrObjJson['xhrPie'].onreadystatechange = function () {
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
						
						chart.seriesContainer.zIndex = -1;

						

                        chart.innerRadius = am4core.percent(40);
                        chart.depth = 120;

                        chart.legend = new am4charts.Legend();

                        var series = chart.series.push(new am4charts.PieSeries3D());
                        series.dataFields.value = "amount";
                        series.dataFields.depthValue = "amount";
                        series.dataFields.category = "group";
                        series.slices.template.cornerRadius = 5;
                        series.colors.step = 3;

                        $('[aria-labelledby="id-66-title"]').css('display','none');
                    }); // end am4core.ready()  

                        

                    } else {
                        console.error('Failed to fetch data. Status:', this.status);
                    }
                }
            };

            $('#chartdiv').siblings('.chart_loader_grand_parent').css('display', 'block');
            $('#chartdiv').css('display', 'none');

            xhrObjJson['xhrPie'].open('GET', '<?= base_url('admin/dashboard/pieChart'); ?>', true);
            xhrObjJson['xhrPie'].send();
        }

        


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

            xhrObjJson['xhrProfit'] = new XMLHttpRequest();
            xhrObjJson['xhrProfit'].onreadystatechange = function () {
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

            xhrObjJson['xhrProfit'].open('GET', '<?= base_url('admin/dashboard/profitChart'); ?>', true);
            xhrObjJson['xhrProfit'].send();
        }

        


        var ctx_receivable_facts = document.getElementById('receivableFactsChart').getContext('2d');
        var receivableFactsChart;

      function fetchReceivableFactsData() {

            xhrObjJson['xhrRecFacts'] = new XMLHttpRequest();
            xhrObjJson['xhrRecFacts'].onreadystatechange = function () {
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
                      $('#receivable_facts_toggle').css('pointer-events', 'auto');
                      
                    } else {
                        console.error('Failed to fetch data. Status:', this.status);
                    }
                }
            };

            $('#receivableFactsChart').siblings('.chart_loader_grand_parent').css('display', 'block');
            $('#receivableFactsChart').css('display', 'none');
            $('#receivable_facts_toggle').css('pointer-events', 'none');

            xhrObjJson['xhrRecFacts'].open('GET', '<?= base_url('admin/dashboard/receivableFactsChart'); ?>', true);
            xhrObjJson['xhrRecFacts'].send();
        }

        


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

            xhrObjJson['xhrPayFacts'] = new XMLHttpRequest();
            xhrObjJson['xhrPayFacts'].onreadystatechange = function () {
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

                      $('#payable_facts_toggle').css('pointer-events', 'auto');

                    } else {
                        console.error('Failed to fetch data. Status:', this.status);
                    }
                }
            };

            $('#payableFactsChart').siblings('.chart_loader_grand_parent').css('display', 'block');
            $('#payableFactsChart').css('display', 'none');
            $('#payable_facts_toggle').css('pointer-events', 'none');

            xhrObjJson['xhrPayFacts'].open('GET', '<?= base_url('admin/dashboard/payableFactsChart'); ?>', true);
            xhrObjJson['xhrPayFacts'].send();
        }

        

        
    //});

   function tradeReceivable() {

    xhrObjJson['xhrTradeRec'] = new XMLHttpRequest();
    xhrObjJson['xhrTradeRec'].onreadystatechange = function () {
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

    xhrObjJson['xhrTradeRec'].open('GET', '<?= base_url('admin/dashboard/tradeReceivable'); ?>?days='+days, true);
    xhrObjJson['xhrTradeRec'].send();
  }

  

   function tradePayable() {

      xhrObjJson['xhrTradePay'] = new XMLHttpRequest();
      xhrObjJson['xhrTradePay'].onreadystatechange = function () {
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

      xhrObjJson['xhrTradePay'].open('GET', '<?= base_url('admin/dashboard/tradePayable'); ?>?days='+days, true);
      xhrObjJson['xhrTradePay'].send();
  }

  

  

  function quickAssets() {

      xhrObjJson['xhrQuickAssets'] = new XMLHttpRequest();
      xhrObjJson['xhrQuickAssets'].onreadystatechange = function () {
          if (this.readyState == 4) {
              if (this.status == 200) {
                 var data = JSON.parse(this.responseText);

                  $('#qa_bank').html(labelAmount(data.qa_bank));
                  $('#qa_bank').parent().attr('title',formatAmount(data.qa_bank));
                  $('#qa_limit').html(labelAmount(data.qa_limit));
                  $('#qa_limit').parent().attr('title',formatAmount(data.qa_limit));
                  $('#qa_cash').html(labelAmount(data.qa_cash));
                  $('#qa_cash').parent().attr('title',formatAmount(data.qa_cash));

                  initTooltip();
              
              } else {
                  console.error('Failed to fetch data. Status:', this.status);
              }
          }
      };

      xhrObjJson['xhrQuickAssets'].open('GET', '<?= base_url('admin/dashboard/quickAssets'); ?>', true);
      xhrObjJson['xhrQuickAssets'].send();
  }

  

window.addEventListener("DOMContentLoaded", function(){
	//tradeReceivable();
	//tradePayable();
    quickAssets();
    fetchRevenueData();
	fetchPieData();
	
	
	
});
var lastScrollTop = 0;
var Step1 = 0;
var Step2 = 0;
var Step3 = 0;
$(document).scroll(function(e){   
    var scrollAmount = $(window).scrollTop();
    var documentHeight = $(document).height();
    var scrollPercent = (scrollAmount / documentHeight) * 100;
	if (scrollAmount > lastScrollTop){
	if(scrollPercent > 25 && scrollPercent < 35 && Step1==0) {
		//fetchPayableFactsData();
		//fetchReceivableFactsData();


		Step1=1;
	}
	
	if(scrollPercent > 25 && scrollPercent < 35 && Step2==0) {
		fetchNetWorthData();
		fetchCashFlowData();
		
		Step2=1;
	}
	
	if(scrollPercent > 35 && Step3==0) {
    fetchCashEquivalentData();
		// fetchRevenueData();
		 fetchProfitData();
		 
		 Step3=1;
	  }
	}
	lastScrollTop = scrollAmount;
});

  function initTooltip()
  {
    $("body").tooltip({ selector: '[data-toggle=tooltip]' });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });
  }

  var receivable_facts_arr = [];

  function accountsReceivable(month = '', from_date = '', to_date = '') {

    xhrObjJson['xhrAccRec'] = new XMLHttpRequest();
    xhrObjJson['xhrAccRec'].onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status == 200) {
               var data = JSON.parse(this.responseText);
               receivable_facts_arr = data;

               var html = ``;
               if(data.length > 0)
               {
                  $.each(data, function(index, object){
                    html += `
                      <tr>
                        <td data-bs-toggle="tooltip" data-bs-placement="bottom" title="${object.account_name}">${labelString(object.account_name)}</td>
                        <td data-bs-toggle="tooltip" data-bs-placement="bottom" title="${object.balance}">${labelAmount(object.balance_total,true)}</td>
                        <td data-bs-toggle="tooltip" data-bs-placement="bottom" title="${object.overdue}">${labelAmount(object.overdue_total,true)}</td>
                      </tr>
                    `;
                  });

                 $('#receivableFactsTable table tbody').html(html);
                 initTooltip();
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


    xhrObjJson['xhrAccRec'].open('GET', '<?= base_url('admin/dashboard/accountsReceivable'); ?>?from_date='+from_date+'&to_date='+to_date, true);
    xhrObjJson['xhrAccRec'].send();
  }

  // Fetch data initially
  //accountsReceivable();

  var payable_facts_arr = [];

  function accountsPayable(month = '', from_date = '', to_date = '') {

    xhrObjJson['xhrAccPay'] = new XMLHttpRequest();
    xhrObjJson['xhrAccPay'].onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status == 200) {
               var data = JSON.parse(this.responseText);
               payable_facts_arr = data;

               var html = ``;
               if(data.length > 0)
               {
                  $.each(data, function(index, object){
                    html += `
                      <tr>
                        <td data-bs-toggle="tooltip" data-bs-placement="bottom" title="${object.account_name}">${labelString(object.account_name)}</td>
                        <td data-bs-toggle="tooltip" data-bs-placement="bottom" title="${object.balance}">${labelAmount(object.balance_total,true)}</td>
                        <td data-bs-toggle="tooltip" data-bs-placement="bottom" title="${object.overdue}">${labelAmount(object.overdue_total,true)}</td>
                      </tr>
                    `;
                  });

                 $('#payableFactsTable table tbody').html(html);
                 initTooltip();
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


    xhrObjJson['xhrAccPay'].open('GET', '<?= base_url('admin/dashboard/accountsPayable'); ?>?from_date='+from_date+'&to_date='+to_date, true);
    xhrObjJson['xhrAccPay'].send();
  }

  // Fetch data initially
  //accountsPayable();

  function keyFacts() {

      xhrObjJson['xhrKeyFacts'] = new XMLHttpRequest();
      xhrObjJson['xhrKeyFacts'].onreadystatechange = function () {
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

      xhrObjJson['xhrKeyFacts'].open('GET', '<?= base_url('admin/dashboard/keyFacts'); ?>', true);
      xhrObjJson['xhrKeyFacts'].send();
  }

  // Fetch data initially
  //keyFacts();

  function labelAmount(amount, drcr=false)
  {
    var sign = amount < 0 ? -1 : 1;
    amount = Math.abs(amount);

      if(amount >= 10000000){
        amount = amount / 10000000;
        amount = Math.round(amount * 100) / 100;

        if(drcr){
          var drcrs = sign < 0 ? ' CR' : ' DR'; 
          amount = formatAmount(amount) + ' Cr.' + drcrs;
        }
        else{
          amount = sign * amount;
          amount = formatAmount(amount) + ' Cr.';
        }
      }
      else if(amount >= 100000){
        amount = amount / 100000;
        amount = Math.round(amount * 100) / 100;
        if(drcr){
          var drcrs = sign < 0 ? ' CR' : ' DR'; 
          amount = formatAmount(amount) + ' Lac.' + drcrs;
        }
        else{
          amount = sign * amount;
          amount = formatAmount(amount) + ' Lac.';
        }
      }
      else if(amount >= 1000){
        amount = amount / 1000;
        amount = Math.round(amount * 100) / 100;
        if(drcr){
          var drcrs = sign < 0 ? ' CR' : ' DR'; 
          amount = formatAmount(amount) + ' k' + drcrs;
        }
        else{
          amount = sign * amount;
          amount = formatAmount(amount) + ' k';
        }
      }
      else{
        if(drcr){
          var drcrs = sign < 0 ? ' CR' : ' DR'; 
          amount = formatAmount(amount) + drcrs;
        }
        else{
          amount = sign * amount;
          amount = formatAmount(amount);
        }
      }

      return amount;
  }

  function labelString(string, strlen = 15)
  {
    return string.length > strlen ? string.substr(0,12) + '...' : string;
  }

  $(document).on('click','#receivableFactsTable .sort_column',function(){

    var column = $(this).attr('data-column');
    var order = $(this).attr('data-order');
    var data = structuredClone(receivable_facts_arr); 

    if(data.length > 0){

      if(column == 'balance'){

        $('#receivableFactsTable .sort_column[data-column="overdue"]').attr('data-order', '');
        $('#receivableFactsTable .sort_column[data-column="overdue"]').attr('data-order', '').find('.sort_asc').removeClass('active');
        $('#receivableFactsTable .sort_column[data-column="overdue"]').attr('data-order', '').find('.sort_desc').removeClass('active');

        if(order == ''){
          data.sort((a, b) => {
            return b.balance_total - a.balance_total;
          });
          $(this).attr('data-order', 'asc');
          $(this).find('.sort_desc').removeClass('active');
          $(this).find('.sort_asc').addClass('active');
        }
        if(order == 'asc'){
          data.sort((a, b) => {
            return a.balance_total - b.balance_total;
          });
          $(this).attr('data-order', 'desc');
          $(this).find('.sort_asc').removeClass('active');
          $(this).find('.sort_desc').addClass('active');
        }
        if(order == 'desc'){
          $(this).attr('data-order', '');
          $(this).find('.sort_asc').removeClass('active');
          $(this).find('.sort_desc').removeClass('active');
        }
      }

      if(column == 'overdue'){

        $('#receivableFactsTable .sort_column[data-column="balance"]').attr('data-order', '');
        $('#receivableFactsTable .sort_column[data-column="balance"]').attr('data-order', '').find('.sort_asc').removeClass('active');
        $('#receivableFactsTable .sort_column[data-column="balance"]').attr('data-order', '').find('.sort_desc').removeClass('active');

        if(order == ''){
          data.sort((a, b) => {
            return b.overdue_total - a.overdue_total;
          });
          $(this).attr('data-order', 'asc');
          $(this).find('.sort_desc').removeClass('active');
          $(this).find('.sort_asc').addClass('active');
        }
        if(order == 'asc'){
          data.sort((a, b) => {
            return a.overdue_total - b.overdue_total;
          });
          $(this).attr('data-order', 'desc');
          $(this).find('.sort_asc').removeClass('active');
          $(this).find('.sort_desc').addClass('active');
        }
        if(order == 'desc'){
          $(this).attr('data-order', '');
          $(this).find('.sort_asc').removeClass('active');
          $(this).find('.sort_desc').removeClass('active');
        }
      }



      var html = ``;
      $.each(data, function(index, object){
        html += `
          <tr>
            <td data-bs-toggle="tooltip" data-bs-placement="bottom" title="${object.account_name}">${labelString(object.account_name)}</td>
            <td data-bs-toggle="tooltip" data-bs-placement="bottom" title="${object.balance}">${labelAmount(object.balance_total,true)}</td>
            <td data-bs-toggle="tooltip" data-bs-placement="bottom" title="${object.overdue}">${labelAmount(object.overdue_total,true)}</td>
          </tr>
        `;
      });

     $('#receivableFactsTable table tbody').html(html);
     initTooltip();

    }
  });

  $(document).on('click','#payableFactsTable .sort_column',function(){

    var column = $(this).attr('data-column');
    var order = $(this).attr('data-order');
    var data = structuredClone(payable_facts_arr); 

    if(data.length > 0){

      if(column == 'balance'){

        $('#payableFactsTable .sort_column[data-column="overdue"]').attr('data-order', '');
        $('#payableFactsTable .sort_column[data-column="overdue"]').attr('data-order', '').find('.sort_asc').removeClass('active');
        $('#payableFactsTable .sort_column[data-column="overdue"]').attr('data-order', '').find('.sort_desc').removeClass('active');

        if(order == ''){
          data.sort((a, b) => {
            return a.balance_total - b.balance_total;
          });
          $(this).attr('data-order', 'asc');
          $(this).find('.sort_desc').removeClass('active');
          $(this).find('.sort_asc').addClass('active');
        }
        if(order == 'asc'){
          data.sort((a, b) => {
            return b.balance_total - a.balance_total;
          });
          $(this).attr('data-order', 'desc');
          $(this).find('.sort_asc').removeClass('active');
          $(this).find('.sort_desc').addClass('active');
        }
        if(order == 'desc'){
          $(this).attr('data-order', '');
          $(this).find('.sort_asc').removeClass('active');
          $(this).find('.sort_desc').removeClass('active');
        }
      }

      if(column == 'overdue'){

        $('#payableFactsTable .sort_column[data-column="balance"]').attr('data-order', '');
        $('#payableFactsTable .sort_column[data-column="balance"]').attr('data-order', '').find('.sort_asc').removeClass('active');
        $('#payableFactsTable .sort_column[data-column="balance"]').attr('data-order', '').find('.sort_desc').removeClass('active');

        if(order == ''){
          data.sort((a, b) => {
            return a.overdue_total - b.overdue_total;
          });
          $(this).attr('data-order', 'asc');
          $(this).find('.sort_desc').removeClass('active');
          $(this).find('.sort_asc').addClass('active');
        }
        if(order == 'asc'){
          data.sort((a, b) => {
            return b.overdue_total - a.overdue_total;
          });
          $(this).attr('data-order', 'desc');
          $(this).find('.sort_asc').removeClass('active');
          $(this).find('.sort_desc').addClass('active');
        }
        if(order == 'desc'){
          $(this).attr('data-order', '');
          $(this).find('.sort_asc').removeClass('active');
          $(this).find('.sort_desc').removeClass('active');
        }
      }



      var html = ``;
      $.each(data, function(index, object){
        html += `
          <tr>
            <td data-bs-toggle="tooltip" data-bs-placement="bottom" title="${object.account_name}">${labelString(object.account_name)}</td>
            <td data-bs-toggle="tooltip" data-bs-placement="bottom" title="${object.balance}">${labelAmount(object.balance_total,true)}</td>
            <td data-bs-toggle="tooltip" data-bs-placement="bottom" title="${object.overdue}">${labelAmount(object.overdue_total,true)}</td>
          </tr>
        `;
      });

     $('#payableFactsTable table tbody').html(html);
     initTooltip();

    }
  });  
</script>