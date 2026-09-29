<?php $header = array('title' => 'Stock Ledger');?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('Y-m-d',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('Y-03-31', strtotime($fy_begndt. ' + 1 year'));
?>


<div class="row mb-md-0 mb-3">
    <div class="col-6"><h3 class="pb-3">Stock Ledger</h3></div>
</div>
</div>

 <form class="form needs-validation" method="post" id="salefrm"  novalidate>
    <div class="col-12 mb-5 calccard card m-auto">
      <div class="card-header">
          <label>Item</label>
     <?php	
                   echo form_dropdown('item_id', $items_dropdown, set_value('item_id'),' id="item_id" class="selectwidget form-select borderdark my-3"');
		?>
		</div>    
        
        
        <div class="row p-4">
         <div class="comp_calender col-md-6">
             <button class="btn btn-light" fdprocessedid="1e1fx7">APR</button>
             <button class="btn btn-light" fdprocessedid="97vzxa">JUL</button>
              <button class="btn btn-light" fdprocessedid="wow37">OCT</button>
             <button class="btn btn-light" fdprocessedid="qqiqts">JAN</button>
             <button class="btn btn-light" fdprocessedid="m5i8zr">MAY</button>
             <button class="btn btn-light" fdprocessedid="mmqhnj">AUG</button>   
             <button class="btn btn-light" fdprocessedid="2vbqv5">NOV</button> 
             <button class="btn btn-light" fdprocessedid="iwk0qe">FEB</button>
             <button class="btn btn-light" fdprocessedid="302lwu">JUN</button>
             <button class="btn btn-light" fdprocessedid="hpygl">SEP</button>
              <button class="btn btn-light" fdprocessedid="bqlesk">DEC</button>
              <button class="btn btn-light" fdprocessedid="xk07ub">MAR</button>
              <button class="btn btn-qlight" fdprocessedid="go0h7f">Q1</button>
              <button class="btn btn-qlight" fdprocessedid="iglzh">Q2</button>
              <button class="btn btn-qlight" fdprocessedid="pudtqc">Q3</button>
              <button class="btn btn-qlight" fdprocessedid="ezcx1s">Q4</button>
              <button class="btn btn-hlight" fdprocessedid="0eixlg">H1</button>
              <button class="btn btn-hlight" fdprocessedid="vf5ufo">H2</button>
              <span class="fw-bold d-inline-block px-4"><input class="form-check-input mt-1 me-1" type="checkbox" value="" id="tilldate"> TILL PERIOD</span>
         </div>
         <div class="col-md-6">
             <div class="input-group mb-3">
  <button class="input-group-text" id="basic-addon1" fdprocessedid="b354u"><span class="material-symbols-outlined">arrow_back_ios</span></button>
  <button type="button" class="input-group-text fw-bold" style="width:70%; text-align: center; display: block;" fdprocessedid="z442">FY: 2023 - 2024</button>
  <button class="input-group-text" id="basic-addon1" fdprocessedid="spn9m"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
</div>

<div class="row align-items-center my-2">
    <div class="col-md-2 fw-bold pe-0">From</div>
    <div class="col-md-10">
    <div class="calc-inputgroup">
  <input type="text" class="form-control" fdprocessedid="cw7ydk" name="fromdate" id="fromdate" required>
</div></div>

</div>

<div class="row align-items-center my-2">
    <div class="col-md-2 fw-bold pe-0">To</div>
    <div class="col-md-10">
    <div class="calc-inputgroup">
  <input type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1" fdprocessedid="b20o4z" name="todate" id="todate">

</div></div>
</div>
<p class="text-end"><button class="input-group-text fw-bold ms-auto"  id="tilldate" onClick="return setdate();" required>TILL DATE</button></p>

         </div> 
 
       <p class="text-center pt-4"><button type="submit" class="btn btn-lg btn-success w-100" fdprocessedid="9ztof">GO</button></p>  
       
       </div>
  </div>   
</form>
 

<?php echo view('includes/footer_scripts'); ?>

<script>
function setdate(){
    $( "#todate" ).val('<?php echo date("Y-m-d");?>');
     $( "#fromdate" ).val('<?php echo date("Y-m-d");?>');
 return false;   
}


$("#todate").datepicker({
            showOn: 'button',
            buttonImageOnly: true,
            buttonImage: '<?php echo base_url();?>/public/assets/images/caldender-icon.png',
            dateFormat: 'yy-mm-dd'
        });    
$("#fromdate").datepicker({
            showOn: 'button',
            buttonImageOnly: true,
            buttonImage: '<?php echo base_url();?>/public/assets/images/caldender-icon.png',
            dateFormat: 'yy-mm-dd'
        });     
</script>
</body>
</html>
