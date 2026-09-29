<?php $header = array( 	'title' => 'Bar Code' ); ?>
<?php echo view('includes/header',$header); ?>

<div class="row pb-2 align-items-center">
    <div class="col-sm-6"><h3>Bar Code</h3></div>  
    <div class="col-sm-6 text-end"><div class="taskmenus">
        	<a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a> 
        <a href="#"><span class="material-symbols-outlined">offline_bolt</span></a> 
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">download</span></a>
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
         <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>
    </div>
    </div> 



  <div class="collapse listmenu" id="listmenu">
     <a href="<?php echo base_url();?>/admin/bar_code/create"><button class="btn btn-sm btn-success">Create Barcode</button></a>
      <button class="btn btn-sm btn-success">Create Label</button>
      <a href="<?php echo base_url();?>/admin/bar_code/manage"><button class="btn btn-sm btn-success">Manage Barcode</button></a>
	   <button class="btn btn-sm btn-success">Manage Label</button>
	     <button class="btn btn-sm btn-success">Barcode/Label Template</button>
  <div class="float-md-end d-inline-block"> <a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a> </div>
</div>

<div class="col-md-12">
    <div class="row pt-4">
        <div class="col-md-4 p-0">
            <a href="#">
                <div class="card bg-primary p-4 m-2">
                    <h2 class="text-white pb-3">199</h2>
                    <h4 class="text-white">Bar Code /Label </h4>
                </div>
            </a>
        </div>
        <div class="col-md-4 p-0">
            <a href="#">
                <div class="card bg-success p-4 m-2">
                    <h2 class="text-white pb-3">199</h2>
                    <h4 class="text-white">Active Codes </h4>
                </div>
            </a>
        </div>
        <div class="col-md-4 p-0">
            <a href="#">
                <div class="card bg-secondary p-4 m-2">
                    <h2 class="text-white pb-3">199</h2>
                    <h4 class="text-white">InActive Codes </h4>
                </div>
            </a>
        </div>

        <div class="col-md-4 p-0">
            <a href="#">
                <div class="card bg-warning p-4 m-2">
                    <h2 class="text-white pb-3">199</h2>
                    <h4 class="text-white">Expired Codes </h4>
                </div>
            </a>
        </div>
        <div class="col-md-4 p-0">
            <a href="#">
                <div class="card bg-info p-4 m-2">
                    <h2 class="text-white pb-3">199</h2>
                    <h4 class="text-white">Items </h4>
                </div>
            </a>
        </div>
        <div class="col-md-4 p-0">
            <a href="#">
                <div class="card bg-light p-4 m-2">
                    <h2 class="text-dark pb-3">199</h2>
                    <h4 class="text-dark">Exceptions</h4>
                </div>
            </a>
        </div>
    </div>
</div>
  
<?php echo view('includes/footer_scripts'); ?>


</body>
</html>
