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

  <?php echo view('admin/settings/setting_links.php'); ?>

  <br>

<div id="validation_errors"></div>

  


<?php echo view('includes/footer_scripts'); ?>
<script>

</script>
</body>
</html>