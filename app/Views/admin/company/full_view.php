<?php $header = array( 	'title' => 'Company Details' ); ?>
<?php echo view('includes/header',$header); ?>
<?php echo view('includes/loader'); ?>
<div class="main-wrapper">
<?php echo view('includes/'.$folder_path.'inner_header'); ?>
<?php echo view('includes/'.$folder_path.'menu'); ?>
<style>
.btn-adds:hover {
    background: #5d8f1c!important;
    color: #fff!important;
}
.btn-adds {
    border: 2px solid #5d8f1c!important;
    color: #5d8f1c!important;
}
</style>
<div class="page-wrapper page-wrapper-one">
   <div class="content">
      <div class="row">
         <div class="col-lg-6 col-sm-12">
            <div class="page-header ">
               <div class="page-title">
                  <h4><?php echo $company_info['buss_org_name'];?></h4>
                  <h6><?php echo $company_info['address'];?></h6>
				  <?php if($company_info['website_url']!=''){ ?>
				  <h6><a href="<?php echo $company_info['website_url'];?>"><?php echo $company_info['website_url'];?></a></h6>
				  <?php } ?>				  
               </div>
            </div>
           <div class="row">
                     <div class="col-lg-4 col-sm-6 d-flex">
					 
                        <div class="productset">
                         
                           <div class="productsetcontent">
                              <h3>Lifetime Spend</h3>   
							<a href="<?php echo $base_url_path;?>campaigns_report/index/<?php echo $company_id;?>" target="_blank">		
                              <h6><?php 
							  if($lifetime_spend>0)
							  echo price_format($lifetime_spend);
						     else
								 echo '$0';
						  ?></h6>
							  </a>
                           </div>
                        </div>
						
                     </div>
                     <div class="col-lg-4 col-sm-6 d-flex ">
                        <div class="productset">
                         
                         <div class="productsetcontent">
                              <h3>YTD Spend</h3> 
							<a href="<?php echo $base_url_path;?>campaigns_report/index/<?php echo $company_id;?>/<?php echo $ytd_start;?>/<?php echo $ytd_end;?>" target="_blank">			
                              <h6><?php 
							  if($ytd_spend >0)
							  echo price_format($ytd_spend);
						     else
								  echo '$0';
						  ?></h6> </a>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4 col-sm-6 d-flex ">
                        <div class="productset flex-fill">
                         
                           <div class="productsetcontent">
                              <h3>MTD Spend</h3>    
                             <a href="<?php echo $base_url_path;?>campaigns_report/index/<?php echo $company_id;?>/<?php echo $mtd_start;?>/<?php echo $mtd_end;?>" target="_blank">								  
                              <h6><?php 
							  if($mtd_spend>0)
							  echo price_format($mtd_spend);
						     else
								  echo '$0';
						  ?></h6></a>
                           </div>
                        </div>
                     </div>
                    
                  </div>
				  <div class="split-card"></div>
				 
				  <div class="page-title">
                  <h4>Contacts</h4>
               </div>  
			    <div class="card card-order">
			   <div class="row ">
                     <div class="col-lg-12 col-sm-12">
					 <table class="table datatables-clients">
               <thead>
               <tr>  
                <th>Name</th>
				<th>Email</th>	
				<th>City</th>
				<th>Postal Code</th>				
               </thead>
               <tbody> 
			   <tr>
			   <td><?php echo $company_info['first_name'];?> <?php echo $company_info['last_name'];?></td>
			   <td><?php echo $company_info['email'];?></td>
			   <td><?php echo $company_info['city_name'];?></td>
			   <td><?php echo $company_info['postal_code'];?></td>
			   </tr>
               </tbody>
            </table>
                     </div>
                 </div>	</div>				 
			  <div class="split-card"></div>
			 
			<div class="page-title">
                  <h4>Campaigns</h4>
               </div>  
			    <div class="card card-order">
			   <div class="row ">
                     <div class="col-lg-12 col-sm-12">
					 <table class="table datatables-campaigns">
					  <thead>
					   <tr>  
						<th><strong>Name</strong></th>					           				
					</tr>		
				
                </thead>
               <tbody>
			   <?php
			   if($campaigns_lists){
				foreach($campaigns_lists as $campaign_row){ ?>
               <tr>  
                <td><a href="<?php echo $base_url_path;?>campaigns/details/<?php echo $campaign_row['campaign_id'];?>" target="_blank"><?php echo $campaign_row['campaign_name'];?></a></td>		
			</tr>
			   <?php } } ?>
				
             </tbody>
            </table>
                     </div>
                 </div>	 </div> 
			<div class="split-card"></div>
			
			<div class="page-title">
                  <h4>Headlines</h4>
               </div>  
			    <div class="card card-order">
			   <div class="row ">
                     <div class="col-lg-12 col-sm-12">
					 <table class="table datatables-headlines">
					  <thead>
					   <tr>  
						<th><strong>Title</strong></th>
						<th><strong>Caption</strong></th>					           				
					</tr>		
				
                </thead>
               <tbody>
			   <?php
			   if($headlines_lists){
				foreach($headlines_lists as $headline_row){ ?>
               <tr>  
                <td><a href="<?php echo $base_url_path;?>headlines/edit/<?php echo $headline_row['headline_id'];?>" target="_blank"><?php echo $headline_row['headline_title'];?></a></td>	
			<td><?php echo '<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#largeModal'.$headline_row['headline_id'].'"><span class="badges bg-lightgrey">View</span></a><div class="modal fade" id="largeModal'.$headline_row['headline_id'].'" tabindex="-1" aria-labelledby="showpayment" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel'.$headline_row['headline_id'].'">'.$headline_row['headline_type'].'</h5>
               <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
              </div>
              <div class="modal-body">
                <p>'.nl2br($headline_row['headline_caption']).'</p>
                
              </div>
              
            </div>
          </div>
        </div>'; ?></td>			
			</tr>
			   <?php } } ?>
				
             </tbody>
            </table>
                     </div>
                 </div>		 
                 </div>	
         </div>
         <div class="col-lg-6 col-sm-12 ">
            <div class="order-list">
               <div class="orderid">
                  <h4>Username : <?php echo $company_info['email'];?></h4>
                  <h4>Password : ********</h4>
               </div>               
            </div>
            <div class="card card-order">
               <div class="card-body">
                  <div class="row">
                     <div class="col-12">
                        <a href="<?php echo base_url();?>" class="btn btn-adds"><i class="fa fa-lock me-2"></i>Login</a>
                     </div>
                    
                  </div>
               </div>
               <div class="split-card"></div>
              
			<div class="page-title">
                  <h4>Landing Pages</h4>
               </div>  
			   <div class="row ">
                     <div class="col-lg-12 col-sm-12">
					 <table class="table datatables-ads">
               <thead>
			   <tr>  
                <th><strong>Title</strong></th>					           				
			</tr>		
				
             </thead>
			 <tbody>
			 <?php if($ads_lists){ foreach($ads_lists as $ad_row){ ?>
			 <tr>
			   <td><?php echo '<a href="'.$ad_row['ad_url'].'" target="_blank" >'.$ad_row['ad_title'].'</a>';?></td>
			 </tr>
			 <?php }} ?>
			 </tbody>
            </table>
                     </div>
                 </div>	
				  <div class="split-card"></div>
				  	<div class="page-title">
                  <h4>Gallery</h4>
               </div>
				<div class="row">
				
			
					
					<?php if($gallery_lists){ foreach($gallery_lists as $gallery_row){
						$image_caption = $gallery_row['image_caption'];
					 if($gallery_row['image_path']!=''){
						$image_path = '<img src="'.base_url().'/writable/uploads/images_gallery/original/'.$gallery_row['image_path'].'" alt="img" >';
					 }
					 if($gallery_row['image_url']!=''){
		               $image_path = '<a href="'.$gallery_row['image_url'].'" target="_blank"><img src="'.base_url().'/public/assets/img/googledrive.png"  alt="img" ></a>';
		                 }
		
					?>
						<div class="col-lg-3 col-sm-6 d-flex ">
							<div class="productset flex-fill">
							<div class="productsetimg">
							<?php echo $image_path;?>
							
							
							</div>
							<div class="productsetcontent">
							
							<h4><?php echo $image_caption;?></h4>
							
							</div>
							</div>
					</div>
					<?php }} ?>
					
					
				</div>  
				  
            </div>
         </div>
      </div>
   </div>
</div>
<?php echo view('includes/footer_scripts'); ?>	 
<script>
$('.datatables-ads').DataTable({
	"bProcessing": false,
    "serverSide": false,
    "bStateSave": false,
	"ordering": false,
	bFilter: false,
	"bLengthChange": false,
	pagingType: "numbers",
	"pageLength": 10,
	sDom: "fBtlpi",
	'columnDefs': [
			{ 'sortable': true }
		],
		 "aoColumns": [
        {"bSortable": true}       
        
        ]
});
$('.datatables-campaigns').DataTable({
	"bProcessing": false,
    "serverSide": false,
    "bStateSave": false,
	"ordering": false,
	bFilter: false,
	"bLengthChange": false,
	pagingType: "numbers",
	"pageLength": 10,
	sDom: "fBtlpi",
	'columnDefs': [
			{ 'sortable': true }
		],
		 "aoColumns": [
        {"bSortable": true}       
        
        ]
});
$('.datatables-headlines').DataTable({
	"bProcessing": false,
    "serverSide": false,
    "bStateSave": false,
	"ordering": false,
	bFilter: false,
	"bLengthChange": false,
	pagingType: "numbers",
	"pageLength": 10,
	sDom: "fBtlpi",
	'columnDefs': [
			{ 'sortable': true }
		],
		 "aoColumns": [
        {"bSortable": true},
        {"bSortable": false}  		
        
        ]
});
</script>  	
<?php echo view('includes/footer'); ?>