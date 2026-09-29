<?php $header = array('title' => 'Other Stock Registers');?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>


<div class="row mb-md-0 mb-3">
    <div class="col-6"><h3 class="pb-3">Other Stock Registers</h3></div>
    <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></div>
</div>


<form method="POST">
    <div class="col-12 mb-5 calccard card m-auto">
      <div class="card-header"><label>Register Type</label>
      <select class="form-select mt-2 borderdark" name="register_type" fdprocessedid="6p3szr" required="true">
          <option valu="">Choose Type</option>
          <option value="Memorandum Register">Memorandum Register</option>
          <option value="Optional Register">Optional Register</option>
          
          </select></div>    
        
        
        <div class="row p-4">
        <?php $fy_bgn_yr = date('Y',strtotime($local_session->get('ses_company_fy_beginning'))); ?>
        <div class="calc col-md-6">
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
                <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:70%; text-align: center; display: block;">FY: <?= $fy_bgn_yr ?> - <?= ($fy_bgn_yr+1) ?></button>
                <button type="button" class="input-group-text" id="next_year"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">From</div>
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control" value="<?= date('01-m-Y') ?>" name="from_date" id="fromdate" placeholder="dd-mm-yyyy" required>
                    </div>
                </div>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">To</div> 
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control"  value="<?= date('d-m-Y') ?>" name="to_date" id="todate" placeholder="dd-mm-yyyy" required>
                    </div>
                </div>
            </div>
            <p class="text-end"><button type="button" class="input-group-text fw-bold ms-auto"  id="tilldate" required>TILL DATE</button></p>
        
        </div> 
        
        <p class="text-center pt-4"><button type="submit" class="btn btn-success btn-lg w-100">GO</button></p>  
        
    </div>
  </div>   
</form>
          
          




<?php echo view('includes/footer_scripts'); ?>
<style>
    .ui-datepicker{z-index: 99999!important;}
</style>

</body>
</html>
