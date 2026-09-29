<?php $header = array( 	'title' => 'Conversions Report' ); ?>
<?php echo view('includes/header',$header); ?>
<?php echo view('includes/loader'); ?>
<div class="main-wrapper">
<?php echo view('includes/'.$folder_path.'inner_header'); ?>
<?php echo view('includes/'.$folder_path.'menu'); ?>
<div class="page-wrapper page-wrapper-one">
  <div class="content">
   <div class="page-header">
      <div class="page-title">
         <h4>Conversions Report List</h4>
		 <?php if($campaign_info) { ?>
		  <h6><?php echo $campaign_info['campaign_name']; ?>  </h6> 
		 <?php } else { ?>
		<h6>Manage your conversions report</h6>
		 <?php } ?>
		 
         
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
					 <li>
<a data-bs-toggle="tooltip" class="download_pdf_file" data-bs-placement="top" title="" data-bs-original-title="pdf" aria-label="pdf"><img src="<?php echo base_url();?>/public/assets/img/icons/pdf.svg" alt="img"></a>
</li>
					 <li>
					 
<a data-bs-toggle="tooltip" class="download_excel_file" data-bs-placement="top" title="" data-bs-original-title="excel" aria-label="excel"><img src="<?php echo base_url();?>/public/assets/img/icons/excel.svg" alt="img"></a>
</li>
                     </ul>
                  </div>
</div>

<div class="card" id="filter_inputs">
   <div class="card-body pb-0">
      <div class="row">
	    <div class="col-lg-2 col-sm-6 col-12">
            <div class="form-group">
			<label>Client </label>
			<?php
            	$data='id="filter_client_id" class="select form-control select"';
				echo form_dropdown('filter_client_id', $clients_dropdown,$sel_client_id,$data);
			 ?>
               
            </div>
         </div>
		   <div class="col-lg-2 col-sm-6 col-12">
            <div class="form-group">
			<label>Campaign </label>
			<div id="campaign_div">
			<?php
            	$data='id="filter_campaign_id" class="select form-control select"';
				echo form_dropdown('filter_campaign_id', array(""=>"Choose"),'',$data);
			 ?>
              </div> 
            </div>
         </div>
		 
		  <div class="col-lg-2 col-sm-6 col-12">
            <div class="form-group">
			<label>Traffic Source / Company </label>
			
			<?php
            	$data='id="filter_company_id" class="select form-control select"';
				echo form_dropdown('filter_company_id',$company_dropdown,$sel_company_id,$data);
			 ?>
              
            </div>
         </div>
       
	    <div class="col-lg-2 col-sm-6 col-12">
            <div class="form-group">
			   <label>Conversion Start Date </label>
               <input type="date" class="form-control" placeholder="Choose Start Date" name="filter_start_date" id="filter_start_date" value="<?php echo $sel_start_date;?>">
            </div>
         </div>
       
         <div class="col-lg-2 col-sm-6 col-12">
            <div class="form-group">
			   <label>Conversion End Date </label>
               <input type="date" class="form-control" placeholder="Choose End Date" name="filter_end_date" id="filter_end_date" value="<?php echo $sel_end_date;?>">
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
         <div class="table-responsive">
		 <?php echo $message_output->run() ;?>
            <table class="table datatables-conversions_report">
               <thead>
                  <tr>                    
                     <th>Client</th>
					 <th>Campaign</th>
					 <th>Traffic Source / Company</th>
					 <th>Spend</th>
                     <th>Conversions</th>
                     <th>Conversion date</th>
                     <th>CPA</th>
                     <th>Clicks</th>
                     <th>Revenue</th>
					 <th>Impressions</th>
                     <th>CPP</th>				 
               </thead>
               <tbody>
			   </tbody>
			   <tfoot>
			   <tr>
			   <td></td>
			   <td></td>
			   <td></td>
			   <td></td>
			   <td></td>
			   <td></td>
			   <td></td>
			   <td></td>
			   <td></td>
			   <td></td>
			   <td></td>
			   </tr>
			   <tfoot>
			   
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

<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="<?php echo base_url(); ?>/public/js/admins/conversions_report_datatable.js"></script> 
<script>

<?php if($sel_client_id >0){ ?>
window.ini = load_campaign('<?php echo $sel_client_id;?>','<?php echo $sel_campaign_id;?>');

<?php } ?>
function load_campaign(client_id,campaign_id){
	$.get(baseurl+'/admin/conversions_report/ajax_get_campaigns/'+client_id+'/'+campaign_id, 
      function (data, textStatus, jqXHR) {  
         $("#campaign_div").html(data);
		 $("#campaign_id").select2();
    });
}
$("#filter_client_id").on("change",function(){
	$("#campaign_div").html("Loading...");
	var client_id = $(this).val();
	$.get(baseurl+'/admin/conversions_report/ajax_get_campaigns/'+client_id+'/0', 
      function (data, textStatus, jqXHR) {  
         $("#campaign_div").html(data);
		 $("#campaign_id").select2();
		 
		 
		 
    });
	
	
});
</script>	
<?php echo view('includes/footer'); ?>