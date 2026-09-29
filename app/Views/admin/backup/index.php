<?php $header = array( 	'title' => 'Data Backup' ); ?>
<?php echo view('includes/header',$header); ?>
  
<h3 class="pb-3">Data Backup</h3>
<?php echo $message_output->run() ;?>

<div class="row">
    <div class="col-md-6">
        <div class="row m-2  p-4 border h-100 bg-white shadow">
            <div class="col-md-8">
                <h4>Backup Data to Google Drive</h4>
                <p>Backup utility Provides the Facility to Backup data of a company to Google Drive. the data can later be imported from that Google Drive in case of data loss.</p>
            </div>
            <div class="col-md-4">
                <img src="/public/assets/img/google_drive.png">
            </div>
        </div>
        <p class="text-center"><a href="<?= $upload_link ?>" class="btn btn-success">Continue ›</a></p>
    </div>
    
</div>


        

  
<?php echo view('includes/footer_scripts'); ?>
<script>
  
</script>
 </body>
</html>
