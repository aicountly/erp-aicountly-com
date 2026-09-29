<?php $header = array( 	'title' => 'Sale Register' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
  
 
</style>	  
	<h3 class="pb-3">Sale Register</h3>            
   <form class="form needs-validation" method="post" id="salefrm"  novalidate>
	  
  <div class="col-md-12">
	<div class="row">
      <div class="col-md-3 col-6 mb-3"><div class="card p-3">From: <b>
		<input type="text" name="fromdate" id="fromdate" class="datepicker form-control" autocomplete="off" required>
		</b></div></div>  
      <div class="col-md-3 col-6 mb-3"><div class="card p-3">To: <b><input type="text" name="todate" id="todate" autocomplete="off" class="datepicker form-control" required></b></div></div>
      <div class="col-md-3 col-6 mb-3"><input type="submit" value="GO" class="btn btn-primary mx-2"></div>
    </div>
</div>
</form>
<?php echo view('includes/footer_scripts'); ?>

 </body>
</html>
