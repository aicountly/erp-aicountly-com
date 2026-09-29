<?php $header = array( 	'title' => 'Trial Balance ' ); ?>
<?php echo view('includes/header',$header); ?>
<?php echo view('includes/loader'); ?>
<div class="main-wrapper">
   <?php echo view('includes/'.$folder_path.'inner_header'); ?>
   <?php echo view('includes/'.$folder_path.'menu'); ?>
   <div class="page-wrapper page-wrapper-one">
      <div class="content">
         <div class="page-header">
            <div class="page-title">
               <h4>Trial Balance</h4>
               <h6>As On <?php echo date('d-m-Y');?></h6>               
            </div>
         </div>
         <div class="card">
            <div class="card-body">
               
               
               <div class="table-responsive">
                  <?php echo $message_output->run() ;?>
                  <table class="table datatables-trial-balance">
                     <thead>
                        <tr>
                           <th>Account</th>
                           <th>Parent Group</th>
                           <th>Debit Bal.</th>
                           <th>Credit Bal.</th>                           
                     </thead>
                     <tbody>
                     </tbody>   
					 <tfoot>
                         <tr>
							<td></td>
                            <td></td>
                            <td></td>	
							<td></td>
						</tr>	
					</tfoot>
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
<style>
   .buttons-excel{display:none;}
   .buttons-pdf{display:none;}
</style>
<script src="<?php echo base_url(); ?>/public/js/admins/trial_balance_datatable.js"></script> 
<?php echo view('includes/footer'); ?>