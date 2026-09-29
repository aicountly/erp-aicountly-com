<?php $header = array( 	'title' => 'MC Ledger' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .gridtable .row{ display: grid; grid-template-columns:10% 10% 30% 30% 10% 10%;}
    .gridtable .foot.row{ grid-template-columns:100% ;}
</style>
<div class="row mb-md-0 mb-3">
    <div class="col-md-6"><h3 class="pb-3">MC Ledger</h3></div>
    <div class="col-md-6 text-end">
       <div class="taskmenus">
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">CSV</a></li>
            <li><a class="dropdown-item" href="#">Excel</a></li>
            <li><a class="dropdown-item" href="#">Document</a></li>
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
<div class="row mb-2 align-items-top">
<div class="col-lg-6">


<form class="form needs-validation" method="post" id="itemfrm"  novalidate>
<div class="input-group">
    
  <span class="input-group-text px-1">MC</span>
  <?php	
     echo form_dropdown('mat_cent_id', $matrcntr_dropdown, set_value('mat_cent_id'),' style="width:90px;" id="mat_cent_id" class="required selectwidget form-control p-2" required');
   ?>
    
  <span class="input-group-text px-1">From</span>
  <input type="text" name="fromdate" id="fromdate" class="datepicker form-control p-2" placeholder="From" style="width:90px;" autocomplete="off" required>
  
    <span class="input-group-text px-1">To</span>
  <input type="text" name="todate" id="todate" autocomplete="off" class="datepicker form-control p-2" required style="width:90px;" placeholder="To">
  
  <input type="submit" class="btn btn-sm btn-success" value="GO">
</div>
</form>

</div>

<div class="col-lg-6 text-end">
    <div class="dropdown float-end">
      <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> View </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Action</a></li>
        <li><a class="dropdown-item" href="#">Another action</a></li>
        <li><a class="dropdown-item" href="#">Something else here</a></li>
      </ul>
     <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Action</a></li>
        <li><a class="dropdown-item" href="#">Another action</a></li>
        <li><a class="dropdown-item" href="#">Something else here</a></li>
      </ul>
      <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Voucher Series </button>
      <ul class="dropdown-menu" style="">
        <li><a class="dropdown-item" href="#">Action</a></li>
        <li><a class="dropdown-item" href="#">Another action</a></li>
        <li><a class="dropdown-item" href="#">Something else here</a></li>
      </ul>
    
       <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">Back</a>

    </div>
</div>

</div>

<?php echo view('includes/footer_scripts'); ?>

 </body>
</html>
