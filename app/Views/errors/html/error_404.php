<?php $header = array(  'title' => '404 Page Not found' ); ?>
<?php echo view('includes/header',$header);?>
<style>
    .error-container {
      text-align: center;
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .error-code {
      font-size: 96px;
      font-weight: bold;
      color: #dc3545;
    }
    .error-message {
      font-size: 20px;
      margin-bottom: 20px;
      color: #6c757d;
    }
  </style>
<div class="row align-items-center">
  <div class="col-md-12"> 
  
  
  <div class="error-container">
    <div class="error-code">403</div>
    <h2 class="mb-3">Not Authorized</h2>
    <p class="error-message">Sorry, you do not have permission to view this page.</p>
    
  </div>
  </div>
  </div>
<?php echo view('includes/footer_scripts'); ?>

</body>
</html>