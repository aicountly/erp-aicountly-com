<?php $header = array( 	'title' => 'Contacts' ); ?>
<?php echo view('includes/header',$header); ?>

<style>.pb-5{padding-bottom:0px!important;}
.notification-card.active {
    background-color: var(--ui-gray-200) !important;
}</style>

<div class="row managepros">
    
    <iframe sandbox="allow-same-origin allow-scripts allow-popups allow-forms" src="https://sandbox.aicountly.in/home/my_account?page=people_sharing/contacts" title="contacts" style="height: 100vh"></iframe>
                  
 
 </div>
<?php echo view('includes/footer_scripts'); ?>


