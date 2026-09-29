<?php $header = array(  'title' => 'Settings' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
.gridtable .row{  display: grid; grid-template-columns:10% 30% 30%  15% 15%;}
.gridtable .foot.row{ grid-template-columns:100% ;}
</style>
<div class="row align-items-center">
    <div class="col-md-6">
        <h3>Settings</h3>
    </div>
    <div class="col-md-6 text-end">
        <div class="float-md-end d-inline-block"> 

        </div>
    </div>
    
    
</div>

<br>

  <div class="">
        
      <a class="btn btn-outline-success btn-sm showinline-md" href="<?= base_url() ?>/admin/settings/general">General</a>
    
    
      <a class="btn btn-outline-success btn-sm showinline-md" href="<?= base_url() ?>/admin/settings/access_profile">Access Profile</a>
    
    
      <a class="btn btn-outline-success btn-sm showinline-md" href="<?= base_url() ?>/admin/settings/inventory">Inventory</a>
    
    
      <a class="btn btn-outline-success btn-sm showinline-md active" href="<?= base_url() ?>/admin/settings/advanced_settings">Advanced Settings</a>
    
    
      <a class="btn btn-outline-success btn-sm showinline-md" href="<?= base_url() ?>/admin/settings/transaction_limit">Transaction Limit</a>
    

    <a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md float-end">« Back</a>    

  </div>

  <br>

<div id="validation_errors"></div>

  


<?php echo view('includes/footer_scripts'); ?>
<script>

</script>
</body>
</html>