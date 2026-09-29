<?php $header = array( 	'title' => 'Voucher Types List' ); ?>
<?php echo view('includes/header',$header); ?>
<?php echo view('includes/loader'); ?>
<div class="main-wrapper">
<?php echo view('includes/'.$folder_path.'inner_header'); ?>
<?php echo view('includes/'.$folder_path.'menu'); ?>
<div class="page-wrapper page-wrapper-one">
  <div class="content">
   <div class="page-header">
      <div class="page-title">
         <h4>Voucher Types List</h4>
         <h6>Manage your voucher types</h6>
      </div>
      
   </div>
   <div class="card">
      <div class="card-body">
	  
         <div class="table-responsive">
		 <?php echo $message_output->run() ;?>
            <table class="table datatables-vouchertypes">
               <thead>
                  <tr>   
                      <th> Sr.No.</th>				  
                     <th>Voucher Type Name</th>                              
               </thead>
               <tbody></tbody>
            </table>
         </div>
      </div>
   </div>
</div>
</div>
</div>

</div>
</div>
<?php echo view('includes/footer_scripts'); ?>	  
<script src="<?php echo base_url(); ?>/public/js/admins/voucher_types_datatable.js"></script> 	
<?php echo view('includes/footer'); ?>