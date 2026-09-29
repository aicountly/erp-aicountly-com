<?php $header = array( 	'title' => 'Office Tools' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>

         
<div class="row pb-2">
  <div class="col-sm-6"><h3>Calendar</h3></div>  
  <div class="col-sm-6 text-end"><div class="taskmenus">

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
    
 </div>
 </div> 
</div>
<div class="row pb-2">
    <div class="col-md-6">
    </div>
  <div class="col-md-6 text-end">

    <a href="#" class="btn btn-success dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
    <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">CSV</a></li>
    <li><a class="dropdown-item" href="#">Excel</a></li>
    <li><a class="dropdown-item" href="#">Document</a></li>
  </ul>  
      
  <button class="btn btn-success m-1" type="button">Templates</button>
   <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-outline-success">Back</a>
  </div> 
</div>

<div class="col-md-12">
<iframe src="https://calendar.google.com/calendar/embed?src=63c48042ced7f94d0fbc117849ebad0a3c700c3a7e8ce03b49090b9f0b3ddee5%40group.calendar.google.com&ctz=Asia%2FKolkata" style="border: 0" width="100%" height="600" frameborder="0" scrolling="no"></iframe>
    </div>

</body>
</html>
