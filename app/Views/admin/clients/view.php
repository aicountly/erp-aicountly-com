<?php $header = array( 	'title' => 'Clients' ); ?>
<?php echo view('includes/header',$header); ?>
<?php echo view('includes/loader'); ?>
<div class="main-wrapper">
<?php echo view('includes/'.$folder_path.'inner_header'); ?>
<?php echo view('includes/'.$folder_path.'menu'); ?>
<div class="page-wrapper page-wrapper-one">
  <div class="content">
   <div class="page-header">
      <div class="page-title">
         <h4>Clients List</h4>
         <h6>Manage your clients</h6>
      </div>
      <div class="page-btn">
       <a href="javascript:void(0);" class="btn btn-secondary btn-sm" id="client_send_emails">Send Emails</a>
      </div>
	  <div class="page-btn">
         <a href="<?php echo $base_url;?>clients/add" class="btn btn-primary btn-sm">Add Client</a>
      </div>
	  
   </div>
   <div class="card">
      <div class="card-body">
	  <div class="table-top">
<div class="search-set">
<div class="search-path">
<a class="btn btn-filter" id="filter_search">
<img src="<?php echo base_url();?>/public/assets/img/icons/filter.svg" alt="img">
<span><img src="<?php echo base_url();?>/public/assets/img/icons/closes.svg" alt="img"></span>
</a>
</div>
<div class="search-input">
<a class="btn btn-searchset"><img src="<?php echo base_url();?>/public/assets/img/icons/search-white.svg" alt="img"></a>
<div id="DataTables_Table_0_filter" class="dataTables_filter"><label> <input name="filter_search_box" id="filter_search_box" type="search" class="form-control form-control-sm" placeholder="Search..." aria-controls="DataTables_Table_0"></label></div></div>
</div>
<div class="wordset">
<ul>

</ul>
</div>
</div>
<div class="card" id="filter_inputs">
   <div class="card-body pb-0">
      <div class="row">
		<div class="col-lg-2 col-sm-6 col-12">
            <div class="form-group">
			<label>Client </label>
			 <input name="search_box" id="search_box" type="search" class="form-control form-control-sm" placeholder="Search..." aria-controls="DataTables_Table_0">
               
            </div>
         </div>
	    
         
         <div class="col-lg-2 col-sm-6 col-12">
            <div class="form-group">
               <a class="btn btn-filters ms-auto"><img src="<?php echo base_url();?>/public/assets/img/icons/search-whites.svg" alt="img"></a>
            </div>
         </div>
      </div>
   </div>
</div>

	   <?php 
	     $attributes = 'id="clntfrm" ';
	   echo form_open( $base_url.'clients/send_emails', $attributes); ?>
         <div class="table-responsive">
		 <?php echo $message_output->run() ;?>
            <table class="table datatables-clients">
               <thead>
               <tr>  
                <th><input type="checkbox" name="selectall[]" id="selectall" ></th>
                <th>Company name</th>
				<th>Name</th>				
				<th>Email</th>
				<th>Phone</th>
				<th>Invoice</th>
				<th>Password</th>
				<th>Status</th>
				<th>Shadow</th>
				<th>Reg Date</th>
               </thead>
               <tbody> 
               </tbody>
            </table>
         </div>
		  <?php echo form_close(); ?>
      </div>
   </div>
</div>
</div>
</div>

</div>

<?php echo view('includes/footer_scripts'); ?>	  
<script src="<?php echo base_url(); ?>/public/js/admins/clients_datatable.js"></script> 
<script>
$('#selectall').click(function(event) {  //on click 
			if(this.checked) { // check select status
				$('.checkbox_ids').each(function() { //loop through each checkbox
					this.checked = true;  //select all checkboxes with class "checkbox1"               
				});
			}else{
				$('.checkbox_ids').each(function() { //loop through each checkbox
					this.checked = false; //deselect all checkboxes with class "checkbox1"     
				});         
			}
		 });
$('#client_send_emails').click(function(){
			  var msg='';
			  var error=0;
			  var chkName='client_id[]';

			if($("input[type='checkbox']:checked").length==0){
				
				
				
				  alert('Please select alteast 1 Client\'s to continue.');
					return false;
			}
			 else
				{
					$("#clntfrm").submit();
					e.preventDefault();
		  }
		 });
</script>	
<?php echo view('includes/footer'); ?>