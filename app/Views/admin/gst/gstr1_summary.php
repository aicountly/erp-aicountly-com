<?php $header = array( 	'title' => 'GSTR1 Summary' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
 .myform label{width:25%; float:left;}
 .myform .form-control, .myform select {width:75%;}
</style>
<style>
/*for autocomplete */
.ui-autocomplete {
    z-index:9999!important;
}
.pq-sb-horiz-t .pq-sb-slider, .pq-sb-vert-t .pq-sb-slider, .pq-sb-horiz-t .pq-sb-btn, .pq-sb-vert-t .pq-sb-btn{background-color: rgb(220, 254, 211) !important;}
.antiqueWhite {
    background-color: #f9f9f9;
    color: #254475;
    font-weight: bold;
}

.ng-cloak, .x-ng-cloak, .ng-hide:not(.ng-hide-animate) {
    display: none !important;
}
</style>
<div class="row mb-md-0 mb-3">
    <div class="col-md-6"><h3 class="pb-3">GSTR1 Summary</h3></div>
    <div class="col-md-6 text-end"><div class="taskmenus">
        <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a></li>
			<li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a></li>
			<li><a class="dropdown-item" href="javascript:void(0)" onclick="print_pdf();">Document</a></li>
          </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">share</span></a> 
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Facebook</a></li>
            <li><a class="dropdown-item" href="#">Twitter</a></li>
            <li><a class="dropdown-item" href="#">Instagram</a></li>
        </ul>
       
    </div>
    </div>
</div>



<div class="row">
    <div class="col-md-6">
        <p><em>For: <strong><?php echo $show_date;?></strong></em></p>
    </div><div class="col-md-6 text-end">
       <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a>
    </div>
     
</div>

<div class="row">
 <div class="col-md-1">
 </div>
    <div class="col-md-10">
      <table class="table table-bordered table-responsive" cellspacing="5" cellpadding="5">

                            <thead style="background:#F8F8F8 ">
                                <tr style="background-color:#c9ece1; color: black;">

                                    <th class="text-center verticalCenter" style="display: table-cell;" rowspan="2" colspan="2">Description
                                       
                                    </th>
                                    <th class="text-center verticalCenter" rowspan="2" data-ng-bind="trans.LBL_PDF_TABLE_HEAD1">No. of records</th>
                                    <th class="text-center verticalCenter" style="padding-left: 19px;padding-right: 19px;" rowspan="2" data-ng-bind="trans.HEAD_DOC_TYPE">Document Type</th>

                                    <th class="text-center verticalCenter" rowspan="2" data-ng-bind="trans.HEAD_TAX_VALUE">Value (₹)</th>
                                    <th class="text-center verticalCenter" data-ng-bind="trans.LBL_INT_TAX">Integrated tax (₹)</th>
                                    <th class="text-center verticalCenter" data-ng-bind="trans.LBL_CENTR_TAX">Central tax (₹)</th>
                                    <th class="text-center verticalCenter" data-ng-bind="trans.LBL_STATE_UAT_TAX">State/UT tax (₹)</th>
                                    <th class="text-center verticalCenter" data-ng-bind="trans.LBL_CESS_TAX">Cess (₹)</th>
                                </tr>
                            </thead>
                            <tbody>

                               <?php if(isset($response['data'])){
								foreach($response['data'] as $row){ ?><!---->
								<?php 
									  $exploded = explode("_",$row['tableno']);
									if(isset($exploded[1]))
									  $tname=  $row['table_name'];
								   else{
									  $tname =  $row['tableno'].'-'.$row['table_name'];								
									 echo ' <tr>
                                    <td class="antiqueWhite" colspan="9">'.$tname.'</td>
                                </tr>';
								   }
								   
								   if(isset($exploded[1])){
									  $tname=  $row['table_name'];
									  ?>
									  <tr class="">
                                    <td colspan="2"><?php echo $tname;?></td>
                                    <td class="verticalCenter text-center" data-ng-bind="b2b4adat.ttl_rec"><?php echo $row['total_records'];?></td>
                                    <td class="verticalCenter text-center">Invoice</td>
									<td class="verticalCenter text-right"><?php echo $row['invoice_value'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['igst'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['cgst'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['sgst'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['cess'];?></td>
                                    
                                </tr>
									  <?php
									  
								   }
								   else{
								?> 		
								
								<tr class="">
                                    <td colspan="2">Total</td>
                                    <td class="verticalCenter text-center" data-ng-bind="b2b4adat.ttl_rec"><?php echo $row['total_records'];?></td>
                                    <td class="verticalCenter text-center">Invoice</td>
									<td class="verticalCenter text-right"><?php echo $row['invoice_value'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['igst'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['cgst'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['sgst'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['cess'];?></td>
                                    
                                </tr>
								   <?php } ?>
								
							   <?php }} ?>
								
                            </tbody>
                        </table>
	  
    </div>
  <div class="col-md-1">
  </div>  
</div>

<?php echo view('includes/footer_scripts'); ?>
</body></html>