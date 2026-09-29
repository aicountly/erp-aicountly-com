<?php $header = array( 	'title' => 'Dashboard' ); ?>
<?php echo view('includes/header',$header); ?>
<div class="row g-4">

              <div class="col-12 col-xxl-8">
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <div class="card">
                      <div class="card-body">
                        <div class="d-flex justify-content-between">
                          <div>
                            <h5 class="mb-1">Total Receivables<span class="badge bg-secondary fs--1 ms-2"><span class="badge-label">Latest</span></span></h5>
                            <h6 class="text-700">Total Paid</h6>
                          </div>
                          <h4>₹ 16,247</h4>
                        </div>
                        
                          <div class="progress mybar my-5" role="progressbar" aria-label="Default striped" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar progress-bar-striped" style="width:30%">₹ 52</div>
                            </div>
							
						
                        <div class="mt-2">
                          <div class="d-flex align-items-center mb-2">
                            <div class="bullet-item bg-primary me-2"></div>
                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">Current</h6>
                            <h6 class="text-900 fw-semi-bold mb-0">₹ 52</h6>
                          </div>
                          <div class="d-flex align-items-center">
                            <div class="bullet-item bg-warning-100 me-2"></div>
                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">Pending payment</h6>
                            <h6 class="text-900 fw-semi-bold mb-0">₹ 4,446</h6>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                    <div class="col-12 col-md-6">
                    <div class="card">
                      <div class="card-body">
                        <div class="d-flex justify-content-between">
                          <div>
                            <h5 class="mb-1">Total Payable<span class="badge bg-secondary fs--1 ms-2"><span class="badge-label">Latest</span></span></h5>
                            <h6 class="text-700">Last 15 days</h6>
                          </div>
                          <h4>₹ 16,247</h4>
                        </div>
                        
                          <div class="progress mybar my-5" role="progressbar" aria-label="Default striped" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar progress-bar-striped bg-success" style="width:50%">₹ 5200</div>
                            </div>
						
                        <div class="mt-2">
                          <div class="d-flex align-items-center mb-2">
                            <div class="bullet-item bg-primary me-2"></div>
                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">Current</h6>
                            <h6 class="text-900 fw-semi-bold mb-0">₹ 5200</h6>
                          </div>
                          <div class="d-flex align-items-center">
                            <div class="bullet-item bg-warning-100 me-2"></div>
                            <h6 class="text-900 fw-semi-bold flex-1 mb-0">Overdue</h6>
                            <h6 class="text-900 fw-semi-bold mb-0">₹ 4,446</h6>
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
		   <div class="col-12 col-xl-6"> <div class="card"> <div class="card-header">
                    <h3>Cash Flow</h3>
                    <p class="mb-1 text-700">Actual earnings vs projected earnings</p>
                  </div>
				  <div class="card-body">
                  <img src="<?php echo base_url();?>/public/assets/img/chart.jpg">
                </div></div>
				</div>
				  <div class="col-12 col-xl-6"> <div class="card">  <div class="card-header">
                    <h3>Our Top Expenses</h3>
                    <p class="mb-1 text-700">Actual earnings vs projected earnings</p>
                  </div>
				  <div class="card-body">
                  <img src="<?php echo base_url();?>/public/assets/img/trendline.jpg">
                </div></div>
				</div>
		  </div>

    
		  
          <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 pt-6 pb-9 bg-white border-top border-300">
            <div class="row g-6">
              <div class="col-12 col-xl-6">
                <div class="me-xl-4">
                  <div>
                    <h3>Incomes & Expenses</h3>
                    <p class="mb-1 text-700">Actual earnings vs projected earnings</p>
                  </div>
                  <img src="<?php echo base_url();?>/public/assets/img/chart.jpg">
                </div>
              </div>
              <div class="col-12 col-xl-6">
                <div>
                  <h3>Profilts and Loss</h3>
                  <p class="mb-1 text-700">Rate of customers returning to your shop over time</p>
                </div>
                <img src="<?php echo base_url();?>/public/assets/img/trendline.jpg">
              </div>

          

<?php echo view('includes/footer_scripts'); ?>