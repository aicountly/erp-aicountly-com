<?php $header = array( 	'title' => 'Data Import/Export' ); ?>
<?php echo view('includes/header',$header); ?>
  
<h3 class="pb-3">Data Import / Export</h3>

<div class="row">
    <div class="col-md-6">
        <div class="row m-2  p-4 border h-100 bg-white shadow">
            <div class="col-md-8">
                <h4>Import</h4>
                <p>Import Utility helps you to import data from excel sheet to Aicountly.  it Provide you facility to import new Master, items, voucher to Aicountly.  For any modification of Existing data, go to Bulkupdation  </p>
            </div>
            <div class="col-md-4">
                <img src="/public/assets/img/data-import.png">
            </div>
        </div>
        <p class="text-center"><a href="<?= $base_url ?>import_export/import" class="btn btn-success">Continue Import ›</a></p>
    </div>
    
    
     <div class="col-md-6">
        <div class="row m-2  p-4 border h-100 bg-white shadow">
            <div class="col-md-8">
                <h4>Export</h4>
                <p>Export utility Provides the Facility to Export data of a company to a file. the data can later be imported from that file to another company.</p>
            </div>
            <div class="col-md-4">
                <img src="/public/assets/img/data-export.png">
            </div>
        </div>
         <p class="text-center"><a href="<?= $base_url ?>import_export/export" class="btn btn-success">Continue Export ›</a></p>
    </div>
    
    
</div>


        

  
<?php echo view('includes/footer_scripts'); ?>
<script>
  
</script>
 </body>
</html>
