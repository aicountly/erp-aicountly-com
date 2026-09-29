<?php $header = array( 	'title' => 'Add Company' ); ?>
<?php echo view('includes/header',$header); ?>
<h3 class="pb-3">My Company Data Backup</h3>
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
        </div><!--
         <button id="startBtn" class="btn btn-success btn-lg px-4">
                Continue &rsaquo;
            </button>
            -->
        

        <?php if (!$drive_ready){ ?>
    <p class="text-center"><a href="<?= $upload_link ?>" class="btn btn-success">Continue ›</a></p>
<?php }else{ ?>
    <button class="btn btn-primary" id="startBackup">Upload Company Backups to Drive</button>
    <div id="backupStatus" class="mt-3 text-success"></div>
        <?php } ?>
        
        <div class="progress mt-4" style="height:25px;display:none" id="progWrap">
              <div class="progress-bar" id="progBar" style="width:0%">0&nbsp;%</div>
            </div>
    </div>
    
</div>


  
<?php echo view('includes/footer_scripts'); ?>
<script>


// document.getElementById('startBtn').onclick = () => location.href = gLink;

// /* Return-trip after OAuth2: ?code=… */
// if (new URLSearchParams(location.search).has('code')) {
//     document.getElementById('progWrap').style.display = 'block';

//     fetch('<?= base_url('admin/backup/start') ?>' + location.search)
//         .then(r => r.json())
//         .then(j => poll(j.job));
// }

// function poll(job) {
//     const bar = document.getElementById('progBar');
//     const intv = setInterval(() => {
//         fetch('<?= base_url('admin/backup/progress/') ?>' + job)
//             .then(r => r.json())
//             .then(j => {
//                 bar.style.width = j.progress + '%';
//                 bar.textContent = j.progress + ' %';
//                 if (j.status === 'done') {
//                     clearInterval(intv);
//                     alert('Backup completed & uploaded to Google Drive ✔');
//                     location.href = '<?= base_url('admin/backup') ?>';
//                 }
//             });
//     }, 2000);
// }


document.getElementById('startBackup').addEventListener('click', function () {
            const status = document.getElementById('backupStatus');
            status.innerText = 'Uploading...';

            fetch('<?= base_url("home/upload_all_to_drive") ?>')
                .then(res => res.json())
                .then(data => {
                    status.innerText = data.message;
                })
                .catch(err => {
                    status.innerText = 'Upload failed.';
                    console.error(err);
                });
        });
</script>
 </body>
</html>
