<?php $header = array(  'title' => 'Data Import/Export' ); ?>
<?php echo view('includes/header',$header); ?>
  
<style>
    .gridtable .row{ display: grid; grid-template-columns:20% 20% 20% 20% 20%;}
    .gridtable .foot.row{ grid-template-columns:100% ;}
    .list-inline a{color:#000;}
    .list-inline a.active{color:#25b003;}
    .acesstabs{display:flex; position:relative; justify-content: space-around;}
    .acesstabs::after{height:2px; width:100%; background:#1d528c; position:absolute; top:30px; content:'';}
    .acesstabs a{width:60px; height:60px; background:#1d528c; font-weight:bold; z-index:1; color:#fff; border-radius:50%; text-align:center; line-height:60px; font-size:32px; } 
    .acesstabs a.active{ background:#25b003; color:#fff;}

    .myform .col-sm-6{padding-bottom:2px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
    .myform .select2 {width:75%!important; }
</style>

<style>
.ui-autocomplete {
  max-height: 200px;
  overflow-y: auto;
  overflow-x: hidden;
}
.ui-autocomplete {
  z-index:9999!important;
}
</style>

<style>
  .pq-grid-row.bg-lightgreen{
    background-color: lightgreen !important;
  }
  .pq-grid-row.bg-white{
    background-color: white !important;
  }
  .pq-grid-row.bg-off_white{
    background-color: #f9f9f9 !important;
  }
  .pq-grid-row.bg-off_red{
    /*background-color: #ff7f7f66!important;*/
	 background-color: #f9f9f9 !important;
  }
  .pq-grid-cell.bg-off_white_dark{
    background-color: #f1f1f1 !important;
  }
</style>

<div style="width: 150px;float: right;">
  <select name="refresh_masters" class="form-select float-end">
    <option value="">Refresh Masters</option>
    <option value="acc">Account/ Bill Sundry</option>
    <option value="itm">Items Masters</option>
    <option value="unt">Unit Masters</option>
  </select>
</div>
        
<h3 class="pb-3">Import</h3>

<div class="col-12">

 <p class="acesstabs" id="myTab" role="tablist">
  <a class="active" id="acess1-tab"  data-bs-target="#acess1-panel" type="button" role="tab" aria-controls="acess1-panel" aria-selected="true">1</a>

  <a class="" id="acess2-tab"  data-bs-target="#acess2-panel" type="button" role="tab" aria-controls="acess2-panel" aria-selected="false" tabindex="-1" >2</a>

  <a class="" id="acess3-tab"  data-bs-target="#acess3-panel" type="button" role="tab" aria-controls="acess3-panel" aria-selected="false" tabindex="-1" >3</a>

  <a class="" id="acess4-tab"  data-bs-target="#acess4-panel" type="button" role="tab" aria-controls="acess4-panel" aria-selected="false" tabindex="-1" >4</a>

  <a class="" id="acess5-tab"  data-bs-target="#acess5-panel" type="button" role="tab" aria-controls="acess5-panel" aria-selected="false" tabindex="-1" >5</a>

  <a class="" id="acess6-tab"  data-bs-target="#acess6-panel" type="button" role="tab" aria-controls="acess6-panel" aria-selected="false" tabindex="-1" >6</a>
  
  <a class="" id="acess7-tab"  data-bs-target="#acess7-panel" type="button" role="tab" aria-controls="acess7-panel" aria-selected="false" tabindex="-1" >7</a>
  
</p>

<div class="tab-content border-0 accordion form-outline mb-4" id="myTabContent">


 <!-- Access Tab 1 Starts here ---->

<div class="tab-pane border-0 fade accordion-item active show" id="acess1-panel" role="tabpanel" aria-labelledby="acess1-tab" tabindex="0">
  <div class="card p-4 mt-2">
      <h4>Add Module(Date should be in <em>YYYY-MM-DD</em> format)</h4>

      <form id="create_mst_form" action="<?= base_url() ?>/admin/import_export/createMaster" method="post">

        <div class="col-md-12 myform pt-4">

          <div id="crt_mst_val_err"></div>

          <div class="row">
          <p class="col-12 col-md-3">
            <label>Module</label>
            <select name="module" id="module" class="form-select w-75 required">
              <option> </option>
              <option>Accounts</option> 
              <option>Inventory</option>
              <option>Transaction</option>
            </select> 
          </p>

          <p class="col-12 col-md-3">
            <label>Master</label>
            <select name="master" id="master" class="form-select w-75 required">
              <option value=""></option>
            </select> 
          </p>

          <p class="col-12 col-md-3">
            <label>Sub Master</label>
            <select name="sub_master" id="sub_master" class="form-select w-75 required">
              <option value=""></option>
              
            </select> 
          </p>
		   <p class="col-12 col-md-3" id="sub_master_format_div" style="display:none;">
            <label>Format</label>
            <select name="sub_master_format" id="sub_master_format" class="form-select w-75 required">
              <option value=""></option>
              
            </select> 
          </p>
          </div>

          <p class="col-12 text-end">
            <button type="button" id="download_sample" class="btn btn-outline-success m-2">Sample</button>

            <button type="submit" class="btn btn-success">Submit</button>
          </p>

        </div>
      </form>

      <div class="col-md-12">
          <p class="mb-0">
            <b>View Recent Reqeust:</b>
          </p>
          <div id="exp_mst_tbl" class="col-12 gridtable" style="overflow: auto; max-width:100%; height:350px;">
            <div class="row head">
             <div class="col">Module</div>
             <div class="col">Master</div>
             <div class="col">Sub Master</div>
             <div class="col">Requested on</div>
             <div class="col">Status</div>
           </div>
           <div class="row">
             <div class="col">&nbsp;</div>
             <div class="col">&nbsp;</div>
             <div class="col">&nbsp;</div>
             <div class="col">&nbsp;</div>
             <div class="col">&nbsp;</div>
           </div>
           <div class="row">
             <div class="col">&nbsp;</div>
             <div class="col">&nbsp;</div>
             <div class="col">&nbsp;</div>
             <div class="col">&nbsp;</div>
             <div class="col">&nbsp;</div>
           </div>
         </div>  


      </div>

  </div>
</div>

<!-- Access Tab 1 Ends here ---> 




<!-- Access 2 Tab -->
<div class="tab-pane border-0 fade accordion-item" id="acess2-panel" role="tabpanel" aria-labelledby="acess2-tab" tabindex="0">
    
  <div class="card p-4 mt-2">
    <h4>
      <span class="mst_sub_title"></span>
    </h4>
    <div class="col-md-6 myform pt-4 mx-auto">

      <div id="upld_mst_val_err"></div>
      <div id="upld_mst_file_prgs" class="progress" style="height:20px">
        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:0%">0%</div>
      </div>

      <br>

     <div class="col-12">
      <label>File Upload</label>
      <input type="hidden" id="upld_mst_sr_id" name="impexp_sr_id" value="">
      <input type="hidden" id="upld_mst_type" name="impexp_type" value="">
      <input type="hidden" id="upld_status" name="upld_status" value="0">

      <input id="upld_mst_file" type="file" class="form-control w-75" accept=".csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">

      </div>
      <div class="col-md-12 mt-2" id="upld_acc_div" style="display: none">
        <label>Account</label>
        <input name="upld_mst_acc" id="upld_mst_acc" type="text" class="form-control"  required>
      </div>

      <p class="text-end mt-2">
        <button class="btn btn-success" id="upld_mst_file_btn">Upload</button>
      </p>

    </div>
    <div class="col-md-12 pt-4">
      <p class="">
        <a href="javascript:void(0);" class="btn btn-success m-2 btnPrevious">« Back</a> 
        <a id="edt_upld_file" href="#" class="btn btn-success float-end m-2">Edit »</a>
      </p>
    </div>
  </div>
  

</div>
<!-- Access Tab 2 Ends here --->

<!-- Access Tab 3 Starts here ------>
<div class="tab-pane border-0 fade accordion-item" id="acess3-panel" role="tabpanel" aria-labelledby="acess3-tab" tabindex="0">
 <div class="card px-4 pt-4 mt-2">
  <h4>
    <span class="mst_sub_title"></span> 
    Suggestions
  </h4>
    <div class="mt-2">
      <strong>
        <em>
        Total Records: 
        <span id="suggestion_total_records"></span> 
        </em>
      </strong>

      <strong class="float-end">
        <em>
          Records Uploaded:
          <span id="suggestion_upld_records"></span>  
        </em>
      </strong>
    </div>

   <div class="m-1 p-1">
      <div id="suggestion_grid_search" style="margin:auto;"></div>
      
   </div>

   <div class="row">

    <div class="col-md-12">
      <span id="suggestion_desc" class="ms-2">Displaying 1 to 100 of 1000 records</span>
      <button id="suggestion_save_record" class="btn btn-outline-success btn-sm me-2 float-end edt_btns">Update</button>
      <button id="suggestion_next_record" class="btn btn-success btn-sm me-2 float-end edt_btns">Next Page »</button>
       <button  id="suggestion_prev_record" class="btn btn-success btn-sm me-2 float-end edt_btns">« Previous Page</button>
    </div>
       
   </div>

    <p class="text-center pt-4">
      <a href="javascript:void(0)" id="upld_suggestion_new_file" class="btn btn-success m-2">« File Upload</a> 
      <a href="javascript:void(0)" id="prv_suggestion_upld_file" class="btn btn-success m-2">Preview »</a>
    </p>
 </div>
 
 
</div>
<!-- Access Tab 3 Ends here --------->



<!-- Access Tab 4 Starts here ------>
<div class="tab-pane border-0 fade accordion-item" id="acess4-panel" role="tabpanel" aria-labelledby="acess4-tab" tabindex="0">

 <div class="card px-4 pt-4 mt-2">

  <h4>
    <span class="mst_sub_title"></span> 
    Editing
  </h4>

    <div class="mt-2">
      <strong>
        <em>
        Total Records: 
        <span id="edt_total_records"></span> 
        </em>
      </strong>

      <strong class="float-end">
        <em>
          Records Uploaded:
          <span id="edt_upld_records"></span>  
        </em>
      </strong>
    </div>

    <input type="hidden" id="edt_mst_sr_id" name="impexp_sr_id" value="">
    <input type="hidden" id="edt_mst_type" name="impexp_type" value="">

   <div class="m-1 p-1">
      <div id="grid_search" style="margin:auto;"></div>
      
   </div>

   <div class="row">

    <div class="col-md-12">
      <span id="edt_desc" class="ms-2">Displaying 1 to 100 of 1000 records</span>
      <button id="edt_save_record" class="btn btn-outline-success btn-sm me-2 float-end edt_btns">Update</button>
      <button id="edt_next_record" class="btn btn-success btn-sm me-2 float-end edt_btns">Next Page »</button>
       <button  id="edt_prev_record" class="btn btn-success btn-sm me-2 float-end edt_btns">« Previous Page</button>
    </div>
       
   </div>

    <p class="text-center pt-4">
      <a href="javascript:void(0)" id="upld_new_file" class="btn btn-success m-2">« File Upload</a> 
      <a href="javascript:void(0)" id="prv_upld_file" class="btn btn-success m-2">Preview »</a>
    </p>
 </div>
 
 
</div>
<!-- Access Tab 4 Ends here --------->

<!-- Access Tab 5 Starts here ------>
<div class="tab-pane border-0 fade accordion-item" id="acess5-panel" role="tabpanel" aria-labelledby="acess5-tab" tabindex="0">
  
  <div class="card p-4 mt-2">
    <h4> 
      <span class="mst_sub_title"></span>
      Preview
    </h4>
    <div class="mt-2">
      <strong>
        <em>
        Total Records: 
        <span id="prv_total_records"></span> 
        </em>
      </strong>

      <strong class="float-end">
        <em>
          Records Uploaded:
          <span id="prv_upld_records"></span>  
        </em>
      </strong>
    </div>

    <input type="hidden" id="prv_mst_sr_id" name="impexp_sr_id" value="">
    <input type="hidden" id="prv_mst_type" name="impexp_type" value="">

    <div class="row mt-5">
      <div id="previewContainer" class="col-md-12 table-responsive" style="height:350px; overflow:auto">

       
          <table id="previewTable"  class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Column 1</th>
                <th>Column 2</th>
                <th>Column 3</th>
              </tr>
            </thead>

            <tbody>
            </tbody>
          </table>
   
        
      </div>
          
    </div>

    <p class="text-center pt-4">
      <a href="javascript:void(0)" id="edt_prv_records" class="btn btn-success m-2">« Edit</a> 
      <a href="javascript:void(0)" id="prcs_prv_records" class="btn btn-success m-2">Validate »</a>
    </p>

  </div> 

</div>
<!-- Access Tab 5 Ends here --------->



<!-- Access Tab 6 Starts here ------>
<div class="tab-pane border-0 fade accordion-item" id="acess6-panel" role="tabpanel" aria-labelledby="acess6-tab" tabindex="0">

  <div class="card p-4 mt-2">

    <h4><span class="mst_sub_title"></span></h4>
    <div class="row">
      <div class="col-md-12">

        <div class="row">
          <div class="col-md-6">
            <strong>
              <em>Total Records: 
                <span id="prcs_mst_ttl_records"></span>
              </em>
            </strong>
            <br>
            <strong>
              <em>Compiled: 
                <span id="prcs_mst_prcs_records"></span>
              </em>
            </strong>
          </div>
          <div class="col-md-6 text-end">
            <strong class="float-end">
              <em>Previously Imported: 
                <span id="prcs_mst_imp_records"></span>
              </em>
            </strong>
            <br>
            <button class="btn btn-success btn-sm" id="re_prcs_records" disabled>Revalidate</button>
          </div>
        </div>

          <input type="hidden" id="prcs_mst_sr_id" name="impexp_sr_id" value="">
          <input type="hidden" id="prcs_mst_type" name="impexp_sr_id" value="">

          <div id="prcs_mst_prgs" class="progress my-2" style="height:20px">
            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:0%">0%</div>
          </div>
          <div id="prcs_mst_prgs_desc" class="text-center">
            
          </div>
          
      </div>
    </div>

    <div class="row mt-5">
      <div class="col-md-6">
        <h4>Validated
          (<span id="prcs_imp_count">5</span>)
        </h4>
        <ul id="prcs_imp_list" class="list-group" style="height:380px; overflow-y:auto">
          <li class="list-group-item">Group 1</li>
          <li class="list-group-item">Group 2</li>
          <li class="list-group-item">Group 3</li>
          <li class="list-group-item">Group 6 under Group 1</li>
          <li class="list-group-item">Group 7 under Group 2</li>
        </ul>
      </div>
      
      <div class="col-md-6">
        <h4>Errors
          (<span id="prcs_err_count">3</span>)
        </h4>
        <ul id="prcs_err_list" class="list-group" style="height:380px; overflow-y:auto">
          <li class="list-group-item">Group 4 under Group 6
            <button class="btn btn-warning btn-sm float-end">Retry</button>
          </li>
          <li class="list-group-item">Group 5 under Group 7
            <button class="btn btn-warning btn-sm float-end">Retry</button>
          </li>
          <li class="list-group-item">Group 2
            <span class="badge bg-danger float-end">Already Exists</span> 
          </li>
          
        </ul>
      </div>
      
      <p class="text-center pt-4">
        <button  id="prv_prcs_records" class="btn btn-success m-2">« Preview</button> 
        <button  id="fnl_prcs_records" class="btn btn-success m-2" disabled>Process »</button>
      </p>    
    </div>
  </div>

</div>
<!-- Access Tab 6 Ends here --------->


<!-- Access Tab 7 Starts here ------>
<div class="tab-pane border-0 fade accordion-item" id="acess7-panel" role="tabpanel" aria-labelledby="acess7-tab" tabindex="0">

 <div class="card p-4 mt-2">

  <h4><span class="mst_sub_title"></span></h4>
   <div id="prcs_mst_prgs" class="progress my-2" style="height:20px">
            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:0%">0%</div>
          </div>
          <div id="prcs_mst_prgs_desc" class="text-center">
            
          </div>
   <div class="m-5 p-5 text-center m-auto" id="finaldivprc" style="display:none;">
     
      <div class="row">
        <div class="col-md-12 text-center">
          <img style="width: 250px; height: auto" src="<?= base_url() ?>/public/assets/img/green_tick.png" alt="No image">
        </div>
        
      </div>
     <h3>Import Successfull</h3>

     <br>
     
     <a href="javascript:void(0)" onclick="window.location.reload();return false;" class="btn btn-success btn-lg">Add Another Import Module</a>
   </div>
 </div>
 
 
</div>
<!-- Access Tab 7 Ends here --------->






<!-- Manage Access Modal Ends here --->  



</div>
</div>
  
  
  <div id="txn_history_myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" keyboard="true" backdrop="true" class="modal fade text-left">
            <div role="document" class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                 <div class="modal-header">
        <h4 class="modal-title">Validation Summary</h4>
        <button type="button" class="btn-close clostxnmodal" ></button>
      </div>		

					     
                    <div class="modal-body p-0"><br>
					<div class="container">
                     <div class="row justify-center-center">
						<div class="short_txn_buttons col-12 justify-content-center">
						<a href="javascript:void(0);" id="errors_txnbtn" data-vchrtype="" data-grpid=""><button type="button" class="btn btn-outline-danger" fdprocessedid="cx3o3a">ERRORS</button></a>
						<a href="javascript:void(0);" id="taxsummary_txnbtn" data-vchrtype="" data-grpid=""><button type="button" class="btn btn-outline-success" fdprocessedid="cev8o">TAX SUMMARY</button></a>
                       <a href="javascript:void(0);" id="taxsetails_txnbtn" data-vchrtype="" data-grpid=""><button type="button" class="btn btn-outline-success " fdprocessedid="cx3o3a">TAX DETAILS</button></a>
                      
					  </div>
					  	<div class="col-12 justify-content-center">
							<div id="grid_error_tab" style="margin-top:10px;"></div>
							<div id="grid_taxsummary_tab" style="margin-top:10px;"></div>
							<div id="grid_taxsetails_tab" style="margin-top:10px;"></div>
							
					   </div>					  
				</div>
				</div>
				
      
     <br><br><br>
					</div>
                    
                </div>
            </div>
        </div>
<?php echo view('includes/footer_scripts'); ?>
<script>
  var BO_STATE_CODE = <?= intval($bo_state_code) ?>;
  var BO_GSTIN_TYPE = <?= intval($bo_gstin_type) ?>;
  var GOODS_RATE = <?= intval($goods_rate) ?>;
  var SERVICES_RATE = <?= intval($services_rate) ?>;
$("#grid_error_tab").show();
    $("#grid_taxsummary_tab").hide();
	$("#grid_taxsetails_tab").hide();
$(document).on("click", "#errors_txnbtn",function(){
	$("#grid_error_tab").show();
    $("#grid_taxsummary_tab").hide();
	$("#grid_taxsetails_tab").hide();
  });
$(document).on("click", "#taxsummary_txnbtn",function(){
	$("#grid_error_tab").hide();
    $("#grid_taxsummary_tab").show();
	$("#grid_taxsetails_tab").hide();
  });
  $(document).on("click", "#taxsetails_txnbtn",function(){
	$("#grid_error_tab").hide();
    $("#grid_taxsummary_tab").hide();
	$("#grid_taxsetails_tab").show();
  });

  
  $(document).on("click", ".clostxnmodal",function(){
	$("grid_error_tab").html("");
	$("#grid_taxsummary_tab").html("");
	$("#grid_taxsetails_tab").html("");							
	$("#txn_history_myModal").modal("hide");
  });
$(document).ready(function(){
    $('.btnNext').click(function() {
        const nextTabLinkEl = $('.acesstabs .active').closest('a').next('a')[0];
        const nextTab = new bootstrap.Tab(nextTabLinkEl);
        nextTab.show();
    });

$(document).on("click",".btnPrevious",function() {
        const prevTabLinkEl = $('.acesstabs .active').closest('a').prev('a')[0];
	    const prevTab = new bootstrap.Tab(prevTabLinkEl);
        prevTab.show();
    });

});

  $(document).on('change','select[name="module"]', function(){
    var master = $(this).val();
    var html = `<option></option>`;

    if(master == 'Accounts'){
      html += `
        <option>Account Master</option>
      `;
    }
    if(master == 'Inventory'){
      html += `
        <option>Stock Master</option>
      `;
    }
    if(master == 'Transaction'){
      html += `
        <option>Others</option>
        <option>Bank Statement</option>
        <option>Sales</option>
		<option>Purchase</option>
      `;
    }

    $('select[name="master"]').html(html);
    $('select[name="sub_master"]').html(`<option></option>`);
	$('select[name="sub_master_format"]').html(`<option></option>`);
  });

  $(document).on('change','select[name="master"]', function(){
    var master = $(this).val();
    var html = `<option></option>`;

    if(master == 'Account Master'){
      html += `
        <option>Account Master</option>
        <option>Account Group Master</option>
      `;
    }
    if(master == 'Stock Master'){
      html += `
        <option>Item Master</option>
        <option>Item Group Master</option>
        <option>Item Category</option>
      `;
    }
    if(master == 'Others'){
      html += `
        <option>Day Book</option>
      `;
    }
    if(master == 'Bank Statement'){
      html += `
        <option>Other Bank Statement</option>
      `;
    }
    if(master == 'Sales'){
      html += `
	  
        <option>Sales with Item</option>
        <option>Sales w/out Item</option>
		
      `;
    }
	if(master == 'Purchase'){
      html += `
		<option>Purchase with Item</option>
        <option>Purchase w/out Item</option>
		
      `;
    }
    $('#sub_master_format_div').hide();
    $('select[name="sub_master"]').html(html);
	$('select[name="sub_master_format"]').html(`<option></option>`);
  });
  
  $(document).on('change','select[name="sub_master"]', function(){
    var sub_master = $(this).val();
    var html = `<option></option>`;

    if(sub_master == 'Sales with Item'){
      html += ` 
       <option>Multi Row Format</option>
        <option>Single Row Format</option>		
      `;
    }if(sub_master == 'Sales w/out Item'){
      html += `  
        <option>Multi Row Format</option>
        <option>Single Row Format</option>		
      `;
    }
	if(sub_master == 'Purchase with Item'){
      html += `
		<option>Multi Row Format</option>
        <option>Single Row Format</option>
		
      `;
    }
	if(sub_master == 'Purchase w/out Item'){
      html += `
		<option>Multi Row Format</option>
        <option>Single Row Format</option>
		
      `;
    }
    $('#sub_master_format_div').show();
    $('select[name="sub_master_format"]').html(html);
  });
  
  
  
  

  $(document).on('click', '#download_sample', function(){

    var sub_master = $('select[name="sub_master"]').val();
	 var sub_master_format = $('select[name="sub_master_format"]').val();
    sub_master = sub_master.trim();
 sub_master_format = sub_master_format.trim();


    if(sub_master == ''){
      alert_notification(sub_master);
    }

    if(sub_master == 'Account Master'){
      var url = "<?= base_url() ?>/admin/import_export/account_master_sample";
      window.open(url, '_blank');
      return false;
    }
    if(sub_master == 'Account Group Master'){
      var url = "<?= base_url() ?>/admin/import_export/account_group_master_sample";
      window.open(url, '_blank');
      return false;
    }
    if(sub_master == 'Item Master'){
      var url = "<?= base_url() ?>/admin/import_export/item_master_sample";
      window.open(url, '_blank');
      return false;
    }
    if(sub_master == 'Item Group Master'){
      var url = "<?= base_url() ?>/admin/import_export/item_group_master_sample";
      window.open(url, '_blank');
      return false;
    }
    if(sub_master == 'Item Category'){
      var url = "<?= base_url() ?>/admin/import_export/item_category_master_sample";
      window.open(url, '_blank');
      return false;
    }
    if(sub_master == 'Day Book'){
      var url = "<?= base_url() ?>/admin/import_export/day_book_sample";
      window.open(url, '_blank');
      return false;
    }
    if(sub_master == 'Other Bank Statement'){
      var url = "<?= base_url() ?>/admin/import_export/bank_statement_sample";
      window.open(url, '_blank');
      return false;
    }
    if(sub_master == 'Sales w/out Item' && sub_master_format=='Multi Row Format'){
      var url = "<?= base_url() ?>/admin/import_export/sale_non_item_sample";
      window.open(url, '_blank');
      return false;
    }
	
    if(sub_master == 'Sales with Item' && sub_master_format=='Multi Row Format'){
      var url = "<?= base_url() ?>/admin/import_export/sale_item_sample";
      window.open(url, '_blank');
      return false;
    }
	
	if(sub_master == 'Purchase w/out Item' && sub_master_format=='Multi Row Format'){
      var url = "<?= base_url() ?>/admin/import_export/purchase_non_item_sample";
      window.open(url, '_blank');
      return false;
    }
	
    if(sub_master == 'Purchase with Item' && sub_master_format=='Multi Row Format'){
      var url = "<?= base_url() ?>/admin/import_export/purchase_item_sample";
      window.open(url, '_blank');
      return false;
    }
	if(sub_master == 'Sales w/out Item' && sub_master_format=='Single Row Format'){
      var url = "<?= base_url() ?>/admin/import_export/sale_non_item_single_sample";
      window.open(url, '_blank');
      return false;
    }
	
    if(sub_master == 'Sales with Item' & sub_master_format=='Single Row Format'){
      var url = "<?= base_url() ?>/admin/import_export/sale_item_single_sample";
      window.open(url, '_blank');
      return false;
    }
	
	if(sub_master == 'Purchase w/out Item' & sub_master_format=='Single Row Format'){
      var url = "<?= base_url() ?>/admin/import_export/purchase_non_item_single_sample";
      window.open(url, '_blank');
      return false;
    }
	
    if(sub_master == 'Purchase with Item' & sub_master_format=='Single Row Format'){
      var url = "<?= base_url() ?>/admin/import_export/purchase_item_single_sample";
      window.open(url, '_blank');
      return false;
    }

    alert_notification('Invalid Sub Master');
  });

  $(document).on('click', '#upld_mst_file_btn', function()
  {
    var impexp_sr_id  = $('#upld_mst_sr_id').val();
    var impexp_type   = $('#upld_mst_type').val();
    var upload_status = $('#upld_status').val();
    var account       = $('#upld_mst_acc').val();
    if(impexp_sr_id == '' || impexp_type == ''){
      alert("Something went wrong");
      return false;
    }

    if(impexp_type == 7){
      account = account.trim();
      if(account == ''){
        alert("Account Name is required");
        return false;
      }
    }

    if ($('#upld_mst_file')[0].files.length === 0) {
      alert("No files selected.");
      return false;
    }

    if(upload_status == 1){
      if(!confirm('Are you sure? Excel file previously uploaded to the server will be deleted'))
        return false;
    }

    var formData = new FormData(); 
    var files = $('#upld_mst_file')[0].files[0]; 
    formData.append('import_file', files);
    formData.append('impexp_sr_id', impexp_sr_id);
    formData.append('impexp_type', impexp_type);
    formData.append('account', account);

    $('#upld_mst_file_prgs .progress-bar').css('width','0%');
    $('#upld_mst_file_prgs .progress-bar').text('0%');

      $.ajax({
        xhr: function() {
          var xhr = new window.XMLHttpRequest();

          xhr.upload.addEventListener("progress", function(evt) {
            if (evt.lengthComputable) {
              var percentComplete = evt.loaded / evt.total;
              percentComplete = parseInt(percentComplete*100);
              // console.log(percentComplete);
              var p_status = true;

              if (percentComplete === 100) {
                p_status = false;
              }

              if(p_status){
                $('#upld_mst_file_prgs .progress-bar').css('width', percentComplete+'%');
                $('#upld_mst_file_prgs .progress-bar').text(percentComplete+'%');
              }
                

            }
          }, false);

          return xhr;
        },
        url: "<?= base_url() ?>/admin/import_export/upload_master_data",
        type: "POST",
        data: formData, 
        contentType: false, 
        processData: false, 
        dataType: "json",
        beforeSend: function() {
			show_loader();
          $('#upld_mst_file_btn').attr('disabled','disabled');
          $('#upld_mst_val_err').html('');
        },
        success: function(response) {
			stop_loader();
          if(response.status){
            $('#upld_mst_file').val('');
            $('#upld_status').val(1);
            $('#upld_mst_file_prgs .progress-bar').css('width', '100%');
            $('#upld_mst_file_prgs .progress-bar').text('100%');

            get_export_master_list();

            $('#edt_mst_sr_id').val(impexp_sr_id);
            $('#edt_mst_type').val(impexp_type);

            setTimeout(function(){

              $('#upld_mst_file_prgs .progress-bar').css('width', '0%');
              $('#upld_mst_file_prgs .progress-bar').text('0%');

              const nextTabLinkEl = $('#acess3-tab');
              const nextTab =new bootstrap.Tab(nextTabLinkEl);
              nextTab.show();
            }, 1000);
              
          }
          else{
            alert_notification(response.message);

            if(response.errors)
            {
              var list = ``;
              if(response.errors){
                $.each(response.errors, function(index, value){
                    list += `<li>${value}</li>`;
                });

                var html = `
                    <div class="alert alert-danger alert-dismissible">
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      <ul>${list}</ul>
                    </div>
                `;
                
                $('#upld_mst_val_err').html(html);
                window.scrollTo(0,0);
              }
            } 
          }
        },
        complete: function() {
          $('#upld_mst_file_btn').attr('disabled',false);
        }
      });
  });

  $(document).on('click', '#edt_upld_file', function(){

    var impexp_sr_id = $('#upld_mst_sr_id').val();
    var impexp_type = $('#upld_mst_type').val();

    var upload_status = $('#upld_status').val();
    if(upload_status != 1){
      alert('No file has been uploaded yet');
      return false;
    }

    $('#edt_mst_sr_id').val(impexp_sr_id);
    $('#edt_mst_type').val(impexp_type);

    const nextTabLinkEl = $('#acess3-tab');
    const nextTab = new bootstrap.Tab(nextTabLinkEl);
      nextTab.show();
  });
  
  $(document).on('click', '#prv_suggestion_upld_file', function(){

    var impexp_sr_id = $('#edt_mst_sr_id').val();
    var impexp_type = $('#edt_mst_type').val();

    $('#prv_mst_sr_id').val(impexp_sr_id);
    $('#prv_mst_type').val(impexp_type);
    getPreviewRecords();

    const nextTabLinkEl = $('#acess4-tab');
    const nextTab = new bootstrap.Tab(nextTabLinkEl);
      nextTab.show();
  });

  $(document).on('click', '#prv_upld_file', function(){

    var impexp_sr_id = $('#edt_mst_sr_id').val();
    var impexp_type = $('#edt_mst_type').val();

    $('#prv_mst_sr_id').val(impexp_sr_id);
    $('#prv_mst_type').val(impexp_type);
    getPreviewRecords();

    const nextTabLinkEl = $('#acess5-tab');
    const nextTab = new bootstrap.Tab(nextTabLinkEl);
      nextTab.show();
  });

  $(document).on('submit', '#create_mst_form', function(e)
  {
    e.preventDefault(); 

    var form = $(this);
    var actionUrl = form.attr('action');
    
    $.ajax({
        type: "POST",
        url: form.attr('action'),
        data: form.serialize(),
        dataType: 'json',
        beforeSend: function() {
          show_loader();
          $(form).find('button[type="submit"]')
                  .attr('disabled', 'disabled');
          $('#crt_mst_val_err').html('');
        },
        success: function(response)
        {
          if(response.status){
            var sub_master = $('select[name="sub_master"]').val();
            $('select[name="module"]').val('');
            $('select[name="module"]').trigger('change');
            get_export_master_list();

            $('.mst_sub_title').text(sub_master);

            $('#upld_mst_sr_id').val(response.impexp_sr_id);
            $('#upld_mst_type').val(response.impexp_type);
            $('#upld_status').val(0);

            $('#upld_acc_div').css('display','none');
            if(response.impexp_type == 7){
              $('#upld_acc_div').css('display','block');
              set_upld_mst_acc();
            }

            if(response.impexp_type == 8){
              get_acc_bsd_list();
              get_tax_list()
            }

            if(response.impexp_type == 9){
              get_tax_list();
              get_acc_bsd_list();
              get_item_list();
              get_unit_list();
            }

            const nextTabLinkEl = $('#acess2-tab');
            const nextTab = new bootstrap.Tab(nextTabLinkEl);
              nextTab.show();
          }
          else{
            alert_notification(response.message);

            if(response.errors)
            {
              var list = ``;
              if(response.errors){
                $.each(response.errors, function(index, value){
                    list += `<li>${value}</li>`;
                });

                var html = `
                    <div class="alert alert-danger alert-dismissible">
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      <ul>${list}</ul>
                    </div>
                `;
                
                $('#crt_mst_val_err').html(html);
                window.scrollTo(0,0);
              }
            } 
          }
        },
        complete: function() {
          stop_loader();
          $(form).find('button[type="submit"]')
                  .attr('disabled', false);
        },
    });
  });
    
  get_export_master_list();
  function get_export_master_list()
  {

    $.ajax({
        type: "POST",
        url: '<?= base_url() ?>/admin/import_export/get_export_master_list',
        method: 'GET',
        dataType: 'json',
        beforeSend: function() {
          show_loader();
        },
        success: function(response)
        {
          if(response.status){

              var html = ``;
              html += `
              <div class="row head">
                 <div class="col">Module</div>
                 <div class="col">Master</div>
                 <div class="col">Sub Master</div>
                 <div class="col">Requested on</div>
                 <div class="col">Status</div>
              </div>
              `;
              if(response.list){
                $.each(response.list, function(index, obj){

                html += `
                <div class="row">
                   <div class="col">${obj.module}</div>
                   <div class="col">${obj.master}</div>
                   <div class="col">${obj.sub_master}</div>
                   <div class="col">${obj.datetime}</div>`;

                if(obj.impexp_status == 0){
                  html += `
                    <div class="col">
                    <span role="button" data-sub_master="${obj.sub_master}" data-impexp_status="${obj.impexp_status}" data-impexp_sr_id="${obj.impexp_sr_id}" data-impexp_type="${obj.impexp_type}" class="badge bg-info exp_pro_btn">${obj.status}</span>
                    
                      <span role="button" data-impexp_sr_id="${obj.impexp_sr_id}" data-impexp_type="${obj.impexp_type}" class="material-symbols-outlined float-end text-danger delete_mst_btn">
                        delete
                      </span>

                   </div>
                  `;
                }

                if(obj.impexp_status == 1){
                  html += `
                    <div class="col">
                    <span class="badge bg-success exp_pro_btn">${obj.status}</span>

                    <span role="button" data-impexp_sr_id="${obj.impexp_sr_id}" data-impexp_type="${obj.impexp_type}" class="material-symbols-outlined float-end text-danger delete_mst_btn">
                        delete
                      </span>
                   </div>
                  `;
                }

                if(obj.impexp_status == 2){
                  html += `
                    <div class="col">
                    <span role="button" data-sub_master="${obj.sub_master}" data-impexp_status="${obj.impexp_status}" data-impexp_sr_id="${obj.impexp_sr_id}" data-impexp_type="${obj.impexp_type}" class="badge bg-warning exp_pro_btn">${obj.status}</span>

                      <span role="button" data-impexp_sr_id="${obj.impexp_sr_id}" data-impexp_type="${obj.impexp_type}" class="material-symbols-outlined float-end text-danger delete_mst_btn">
                        delete
                      </span>
                   </div>
                  `;
                }
                   
                   
                html += `</div>`;

                });
              }
              $('#exp_mst_tbl').html(html);
          }
          else{
            alert_notification(response.message); 
          }
        },
        complete: function() {
          stop_loader();
        },
    });
  }  

  $(document).on('click', '.exp_pro_btn', function(){
    var impexp_status = $(this).data('impexp_status');
    var impexp_sr_id = $(this).data('impexp_sr_id');
    var impexp_type = $(this).data('impexp_type');
    var sub_master = $(this).data('sub_master');

    $('.mst_sub_title').text(sub_master);

    if(impexp_status == 0){
      $('#upld_mst_sr_id').val(impexp_sr_id);
      $('#upld_mst_type').val(impexp_type);
      $('#upld_status').val(0);

      $('#upld_acc_div').css('display','none');
      if(impexp_type == 7){
        $('#upld_acc_div').css('display','block');
        set_upld_mst_acc();
      }
      if(impexp_type == 8){
        get_acc_bsd_list();
        get_tax_list()
      }
      if(impexp_type == 9){
        get_tax_list();
        get_acc_bsd_list();
        get_item_list();
        get_unit_list();
      }
	  if(impexp_type == 10){
        get_acc_bsd_list();
        get_tax_list()
      }
      if(impexp_type == 11){
        get_tax_list();
        get_acc_bsd_list();
        get_item_list();
        get_unit_list();
      }

      const nextTabLinkEl = $('#acess2-tab');
      const nextTab = new bootstrap.Tab(nextTabLinkEl);
        nextTab.show();
    }

    if(impexp_status == 2){
      $('#prv_mst_sr_id').val(impexp_sr_id);
      $('#prv_mst_type').val(impexp_type);
	  
	  $('#edt_mst_sr_id').val(impexp_sr_id);
      $('#edt_mst_type').val(impexp_type);
	  

      const nextTabLinkEl = $('#acess4-tab');
      const nextTab = new bootstrap.Tab(nextTabLinkEl);
        nextTab.show();

      getPreviewRecords();
    }

  });

  $(document).on('click', '.delete_mst_btn', function(){

    if(!confirm('Are you sure ?')){
      return false;
    }

    var impexp_sr_id = $(this).data('impexp_sr_id');
    var impexp_type = $(this).data('impexp_type');

    $.ajax({
        
        url: '<?= base_url() ?>/admin/import_export/delete_inpexp_master',
        method: 'POST',
        dataType: 'json',
        data: {impexp_sr_id:impexp_sr_id, impexp_type: impexp_type},
        beforeSend: function() {
          show_loader();
        },
        success: function(response)
        {
          if(response.status){
            alert_success(response.message);
            get_export_master_list();
          }
          else{
            alert_notification(response.message); 
          }
        },
        complete: function() {
          stop_loader();
        },
    });

  });



  var processing = false;

  $('#previewContainer').scroll(function(){

    if (processing)
      return false;

    var height = $('#previewContainer').height();
    var scrollTop = $('#previewContainer').scrollTop();
    var heightTable = $('#previewContainer table tbody').height();

    if(scrollTop >= (heightTable * 0.9) - height)
    {
      processing = true;
      prv_rcd_offset += prv_rcd_limit;
      getPreviewRecords(0);
    }

 
  });

  var prv_rcd_total = 0;
  var prv_rcd_offset = 0;
  var prv_rcd_limit = 0;
  function getPreviewRecords(first = 1)
  {
    if(first == 1){
      prv_rcd_total = 0;
      prv_rcd_offset = 0;
      prv_rcd_limit = 0;
    }    
    if(prv_rcd_offset != 0 && prv_rcd_offset >= prv_rcd_total)
      return false;
    

    var impexp_sr_id = $('#prv_mst_sr_id').val();
    var impexp_type = $('#prv_mst_type').val();
 
    $.ajax({
      type: "POST",
      url: '<?= base_url() ?>/admin/import_export/get_preview_records',
      method: 'POST',
      data: {impexp_sr_id: impexp_sr_id, impexp_type: impexp_type, offset: prv_rcd_offset},
      dataType: 'json',
      beforeSend: function() {
        var count = $("#previewTable thead tr th").length;
        var html = `
        <tfoot>
          <tr>
          <td colspan="${count}" style="text-align: center;">
            <div class="spinner-border text-success"></div>
          </td>
          </tr>
        </tfoot>  
        `;
        $("#previewTable").append(html);
      },
      success: function(response)
      { 
        if(response.status){
          processing = false;
          
          var html = ``;

          if(prv_rcd_offset == 0)
          {
            var total = response.result.total;
            var uploaded = response.result.uploaded;
            prv_rcd_limit = response.limit;

            prv_rcd_total = total;
            $('#prv_total_records').text(total);
            $('#prv_upld_records').text(uploaded);
            

            html += `<tr>`;
            $.each(response.result.columns, function(indx,obj){
              html += `<th>${obj}</th>`;
            });
            html += `</tr>`;
            $('#previewTable').find('thead').html(html);
            $('#previewTable').find('tbody').html('');
          }
          
          html = ``;
          var style = ``;
          $.each(response.result.data, function(indx2,obj2){

            style = ``;
            if(obj2['imp_status'] == 1){
              style = `class="table-success"`;
            }

            html += `<tr ${style}>`;

            $.each(response.result.columns, function(indx,obj){
              if(obj2[indx])
                html += `<td>${obj2[indx]}</td>`;
              else
                html += `<td></td>`; 
            });

            html += `</tr>`;

          });
          $('#previewTable').find('tbody').append(html);

        }
        else{
          alert_notification(response.message);
        }
      },
      complete: function() {
        // stop_loader();
        $("#previewTable tfoot").remove();
      },
      error: function (jqXHR, exception) {
          var error_= '';
          if (jqXHR.status === 0) {
              error = 'Not connect.\n Verify Network.';
          } else if (jqXHR.status == 404) {
              error = 'Requested page not found. [404]';
          } else if (jqXHR.status == 500) {
              error = 'Internal Server Error [500].';
          } else if (exception === 'parsererror') {
              error = 'Requested JSON parse failed.';
          } else if (exception === 'timeout') {
              error = 'Time out error.';
          } else if (exception === 'abort') {
              error = 'Ajax request aborted.';
          } else {
              error = 'Uncaught Error.\n' + jqXHR.responseText;
          }
          alert_notification(error);
      },
    });
  }

  
  $(document).on('click', '#edt_prv_records', function()
  {

    var impexp_sr_id = $('#prv_mst_sr_id').val();
    var impexp_type = $('#prv_mst_type').val();

    $('#edt_mst_sr_id').val(impexp_sr_id);
    $('#edt_mst_type').val(impexp_type);

    const nextTabLinkEl = $('#acess4-tab');
    const nextTab = new bootstrap.Tab(nextTabLinkEl);
        nextTab.show();

  });

  $(document).on('click', '#prcs_prv_records', function()
  {

    if(prv_rcd_total <= 0){
      alert('No record to process');
      return false;
    }
    var impexp_sr_id = $('#prv_mst_sr_id').val();
    var impexp_type = $('#prv_mst_type').val();

    $('#prcs_mst_sr_id').val(impexp_sr_id);
    $('#prcs_mst_type').val(impexp_type);

    const nextTabLinkEl = $('#acess6-tab');
    const nextTab = new bootstrap.Tab(nextTabLinkEl);
        nextTab.show();
     validateRecords();
    //processRecords();
  });


  var total_index = 0;
  var point_index = 0;
  var total_records = 0;
  var process_limit = 0;
  
  function validateRecords(first = 1)
  {
    if(first){
      total_index = 0;
      point_index = 0;
      process_limit = 0;

      $('#prcs_imp_count').text('0');
      $('#prcs_err_count').text('0');

      $('#prcs_imp_list').html('');
      $('#prcs_err_list').html('');
    }

    if(point_index != 0 && point_index > total_index){
      $('#prcs_mst_prgs_desc').text('');
      $('#re_prcs_records').attr('disabled', false);
      return false;
    }

    var progress = Math.round((point_index/total_index)*100);

    $('#prcs_mst_prgs .progress-bar').css('width', progress+'%');
    $('#prcs_mst_prgs .progress-bar').text(progress+'%');

    if(point_index > 0){
      var start = (point_index - 1) * process_limit;
      var end = start + process_limit;
      if(end > total_records){ end = total_records;}
      var str = `Processing ${start} - ${end} Records`;
      $('#prcs_mst_prgs_desc').text(str);
    }
    else{
      $('#prcs_mst_prgs_desc').text('');
    }
      

    var impexp_sr_id = $('#prcs_mst_sr_id').val();
    var impexp_type = $('#prcs_mst_type').val();

    if(impexp_sr_id == 0){
      alert('Something went wrong');
      return false;
    }



    $.ajax({
        type: "POST",
        url: '<?= base_url() ?>/admin/import_export/validate_records',
        method: 'POST',
        data: {impexp_sr_id: impexp_sr_id, impexp_type: impexp_type, point_index: point_index},
        dataType: 'json',
        beforeSend: function() {
          // show_loader();
          
        },
        success: function(response)
        {
          if(response.status){

            var data = response.data;

            if(point_index == 0){
              total_index = data.total_index;
              total_records = data.total_records;
              process_limit = data.limit;
              var imported_records = data.imported_records;
				if(imported_records!=''){
					$("#fnl_prcs_records").attr("disabled",false);
				}
              $('#prcs_mst_ttl_records').text(total_records);
              $('#prcs_mst_imp_records').text(imported_records);
              $('#prcs_mst_prcs_records').text('0');
            }
            else{

              var prcs_records = 0;

              var html = ``;
              var imp_count = $('#prcs_imp_count').text();
              imp_count = parseInt(imp_count);
              

              $.each(data.uploaded, function(indx,obj){
                imp_count++;
				if(imp_count>0){
					$("#fnl_prcs_records").attr("disabled",false);
				}
                html+=`<li class="list-group-item">${obj}</li>`;
              });
              prcs_records += imp_count;
              $('#prcs_imp_count').text(imp_count);
              $('#prcs_imp_list').append(html);

              html = ``;
              var err_count = $('#prcs_err_count').text();
              err_count = parseInt(err_count);
              

              $.each(data.errors, function(indx,obj){
                err_count++;
                html+=`<li class="list-group-item">${obj}</li>`;
              });
              prcs_records += err_count;
              $('#prcs_err_count').text(err_count);
              $('#prcs_err_list').append(html);

              $('#prcs_mst_prcs_records').text(prcs_records);
            }
            


            point_index++;
            validateRecords(0);
          }
          else{
            $('#re_prcs_records').attr('disabled', false);
            alert_notification(response.message); 
          }
        },
        complete: function() {
          
        },
    });
  }
  
  function processRecords(first = 1)
  {
    if(first){
      total_index = 0;
      point_index = 0;
      process_limit = 0;

      $('#prcs_imp_count').text('0');
      $('#prcs_err_count').text('0');

      $('#prcs_imp_list').html('');
      $('#prcs_err_list').html('');
    }

    if(point_index != 0 && point_index > total_index){
      $('#prcs_mst_prgs_desc').text('');
      $('#re_prcs_records').attr('disabled', false);
      return false;
    }

    var progress = Math.round((point_index/total_index)*100);

    $('#prcs_mst_prgs .progress-bar').css('width', progress+'%');
    $('#prcs_mst_prgs .progress-bar').text(progress+'%');

    if(point_index > 0){
      var start = (point_index - 1) * process_limit;
      var end = start + process_limit;
      if(end > total_records){ end = total_records;}
      var str = `Processing ${start} - ${end} Records`;
      $('#prcs_mst_prgs_desc').text(str);
    }
    else{
      $('#prcs_mst_prgs_desc').text('');
    }
      

    var impexp_sr_id = $('#prcs_mst_sr_id').val();
    var impexp_type = $('#prcs_mst_type').val();

    if(impexp_sr_id == 0){
      alert('Something went wrong');
      return false;
    }
  


    $.ajax({
        type: "POST",
        url: '<?= base_url() ?>/admin/import_export/process_records',
        method: 'POST',
        data: {impexp_sr_id: impexp_sr_id, impexp_type: impexp_type, point_index: point_index},
        dataType: 'json',
        beforeSend: function() {
          // show_loader();
          
        },
        success: function(response)
        {
          if(response.status){

            var data = response.data;
			
			
			
            if(point_index == 0){
              total_index = data.total_index;
              total_records = data.total_records;
              process_limit = data.limit;
              var imported_records = data.imported_records;

              $('#prcs_mst_ttl_records').text(total_records);
              $('#prcs_mst_imp_records').text(imported_records);
              $('#prcs_mst_prcs_records').text('0');
            }
            else{

              var prcs_records = 0;

              var html = ``;
              var imp_count = $('#prcs_imp_count').text();
              imp_count = parseInt(imp_count);
              

              $.each(data.uploaded, function(indx,obj){
                imp_count++;
                html+=`<li class="list-group-item">${obj}</li>`;
              });
              prcs_records += imp_count;
              $('#prcs_imp_count').text(imp_count);
              $('#prcs_imp_list').append(html);

              html = ``;
              var err_count = $('#prcs_err_count').text();
              err_count = parseInt(err_count);
              

              $.each(data.errors, function(indx,obj){
                err_count++;
                html+=`<li class="list-group-item">${obj}</li>`;
              });
              prcs_records += err_count;
              $('#prcs_err_count').text(err_count);
              $('#prcs_err_list').append(html);

              $('#prcs_mst_prcs_records').text(prcs_records);
            }
            


            point_index++;
            processRecords(0);
          }
          else{
            $('#re_prcs_records').attr('disabled', false);
            alert_notification(response.message); 
          }
        },
        complete: function() {
			
            if(progress=="100"){
				$("#finaldivprc").show();			
			    $("#prcs_mst_prgs").hide();
			    $("#prcs_mst_prgs_desc").hide();
				
			  var impexp_sr_id = $('#prv_mst_sr_id').val();
			  var impexp_type = $('#prv_mst_type').val();

			  $.ajax({
				type: "POST",
				url: '<?= base_url() ?>/admin/import_export/finalize',
				method: 'POST',
				data: {impexp_sr_id: impexp_sr_id, impexp_type: impexp_type},
				dataType: 'json'
				});
				
		      }
        },
    });
  }

  $(document).on('click', '#re_prcs_records', function(){
    $('#re_prcs_records').attr('disabled', 'disabled');
    validateRecords(1);
  });

  $(document).on('click', '#upld_new_file', function(){
      var impexp_sr_id = $('#edt_mst_sr_id').val();
      var impexp_type = $('#edt_mst_type').val();

      $('#upld_mst_sr_id').val(impexp_sr_id);
      $('#upld_mst_type').val(impexp_type);
      $('#upld_status').val(1);

      $('#upld_acc_div').css('display','none');
      if(impexp_type == 7){
        $('#upld_acc_div').css('display','block');
        set_upld_mst_acc();
      }
      if(impexp_type == 8 ||  impexp_type == 10){
        get_acc_bsd_list();
        get_tax_list()
      }

      if(impexp_type == 9 ||  impexp_type == 11){
        get_tax_list();
        get_acc_bsd_list();
        get_item_list();
        get_unit_list();
      }

      const nextTabLinkEl = $('#acess2-tab');
      const nextTab = new bootstrap.Tab(nextTabLinkEl);
          nextTab.show();
  });
  $(document).on('click', '#upld_suggestion_new_file', function(){
      var impexp_sr_id = $('#edt_mst_sr_id').val();
      var impexp_type = $('#edt_mst_type').val();

      $('#upld_mst_sr_id').val(impexp_sr_id);
      $('#upld_mst_type').val(impexp_type);
      $('#upld_status').val(1);

      $('#upld_acc_div').css('display','none');
      if(impexp_type == 7){
        $('#upld_acc_div').css('display','block');
        set_upld_mst_acc();
      }
      if(impexp_type == 8 ||  impexp_type == 10){
        get_acc_bsd_list();
        get_tax_list()
      }

      if(impexp_type == 9 ||  impexp_type == 11){
        get_tax_list();
        get_acc_bsd_list();
        get_item_list();
        get_unit_list();
      }

      const nextTabLinkEl = $('#acess2-tab');
      const nextTab = new bootstrap.Tab(nextTabLinkEl);
          nextTab.show();
  });
  
  $(document).on('click', '#prv_prcs_records', function(){
      var impexp_sr_id = $('#prcs_mst_sr_id').val();
      var impexp_type = $('#prcs_mst_type').val();

      $('#prv_mst_sr_id').val(impexp_sr_id);
      $('#prv_mst_type').val(impexp_type);

      getPreviewRecords();

      const nextTabLinkEl = $('#acess4-tab');
      const nextTab = new bootstrap.Tab(nextTabLinkEl);
          nextTab.show();
  });

  $(document).on('click', '#fnl_prcs_records', function(){
	  const nextTabLinkEl = $('#acess7-tab');
      const nextTab=new bootstrap.Tab(nextTabLinkEl);
      nextTab.show();			
	  processRecords();
  });

  var colModel = [
      { dataIndx: "state", maxWidth: 30, minWidth: 30, align: "center", resizable: false,
          title: "",
          menuIcon: false,
          cls: 'pq-grid-number-cell', 
          sortable: false, 
          
          render: function( ui ) {
              var rd = ui.rowData;
              var grid = this;
         
              return '<input type="checkbox" value="'+rd.project_id+'" class="my_checkbox">';
          }
      },
      { title: "Column 1", align:"left", width: 180,   dataIndx: ""},
      { title: "Column 2", align:"left", width: 180,   dataIndx: "" },
      { title: "Column 3", align:"left", width: 180,   dataIndx: "" },
  ];
            
   var dataModel = { data: []};
   var newObj = {
      scrollModel: { autoFit: true },
      height: 450,
      collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } },
      selectionModel: { type: 'row',mode:'single' },
      pageModel: { type: null },
      dataModel: dataModel,
      filterModel: { mode: 'OR', type: "local" },
      colModel : colModel,
      editable: true,
      numberCell: { show: false },
      wrap:false,
      showTitle: false,
      dataReady: summary,
      // change: summary,
      postRenderInterval: -1, //synchronous post 
      editModel: {
        clicksToEdit: 1,
        keyUpDown: false
      },
      create: function (evt, ui) {
        this.widget().pqTooltip();

        var grid = this,
          $select_row = $(".select-row"),
          data = ui.dataModel.data;
          grid.setSelection({ rowIndx: 0, focus: true });

      }, 
  };

  newObj.cellKeyDown = function(evt, ui) {
     var rowData = ui.rowData;
     var rowIndx = ui.rowIndx;

     if (evt.keyCode == 46){
          return false;
     }
  }

  $("#acess3-tab").on('shown.bs.tab', function () {   
    if($("#suggestion_grid_search").pqGrid('instance')){       
        $("#suggestion_grid_search").pqGrid('refresh');
    }
    else
      $("#suggestion_grid_search").pqGrid(newObj);

    getEditingSuggestionRecords();
  });
  
  $("#acess4-tab").on('shown.bs.tab', function () {  
     if($("#grid_search").pqGrid('instance')){       
        $("#grid_search").pqGrid('refresh');
      }
      else
      $("#grid_search").pqGrid(newObj);
    getEditingRecords();
  });

  var edt_rcd_total = 0;
  var edt_rcd_offset = 0;
  var edt_rcd_limit = 0;

  $(document).on('click','#edt_next_record',function(){
    var records = edt_rcd_offset + edt_rcd_limit;
    if(records < edt_rcd_total){
      edt_rcd_offset += edt_rcd_limit;
      getEditingRecords(0);
    }
  });

  $(document).on('click','#edt_prev_record',function(){
    if(edt_rcd_offset > 0){
      edt_rcd_offset -= edt_rcd_limit;
      getEditingRecords(0);
    }
  });
  
  $(document).on('click','#suggestion_next_record',function(){
    var records = edt_rcd_offset + edt_rcd_limit;
    if(records < edt_rcd_total){
      edt_rcd_offset += edt_rcd_limit;
      getEditingSuggestionRecords(0);
    }
  });

  $(document).on('click','#suggestion_prev_record',function(){
    if(edt_rcd_offset > 0){
      edt_rcd_offset -= edt_rcd_limit;
      getEditingSuggestionRecords(0);
    }
  });

  function getEditingRecords(first = 1)
  {
    if(first == 1){
      edt_rcd_total = 0;
      edt_rcd_offset = 0;
      edt_rcd_limit = 0;
    }
    
    if(edt_rcd_offset != 0 && edt_rcd_offset>=edt_rcd_total)
      return false;
    

    var impexp_sr_id = $('#edt_mst_sr_id').val();
    var impexp_type = $('#edt_mst_type').val();
		if(impexp_sr_id!='' && impexp_type!=''){
    $.ajax({
      type: "POST",
      url: '<?= base_url() ?>/admin/import_export/get_preview_records',
      method: 'POST',
      data: {impexp_sr_id: impexp_sr_id, impexp_type: impexp_type, offset: edt_rcd_offset},
      dataType: 'json',
      beforeSend: function() {
        $("#grid_search").pqGrid('showLoading');
      },
      success: function(response)
      { 
        if(response.status){
          processing = false;
          $("#grid_search").pqGrid('hideLoading');
          
          if(edt_rcd_offset == 0)
          {
            edt_rcd_limit = response.limit;
            var total = response.result.total;
            var uploaded = response.result.uploaded;


            edt_rcd_total = total;
            $('#edt_total_records').text(total);
            $('#edt_upld_records').text(uploaded);
            
            if(impexp_type == 1 ||impexp_type == 2 ||impexp_type == 3 ||impexp_type == 4 ||impexp_type == 5 ){
              set_masters_colModel(response.result.columns);
            }

            if(impexp_type == 6 || impexp_type == 7 || impexp_type == 8 || impexp_type == 9 || impexp_type == 10 || impexp_type == 11){
              set_day_book_colModel(response.result.columns);
            }
          }
          
          set_masters_dataModel(response.result.data);

          if(impexp_type == 6 || impexp_type == 7 || impexp_type == 8 || impexp_type == 9 || impexp_type == 10 || impexp_type == 11){
            daybookSummary();
          }

          var min = edt_rcd_offset + 1;
          var max = edt_rcd_offset + edt_rcd_limit;

          if(max > edt_rcd_total){
            max = edt_rcd_total;
          }

          var desc = `Displaying ${min} to ${max} of ${edt_rcd_total} `;
          $('#edt_desc').text(desc);
        }
        else{
          alert_notification(response.message);
        }
      },
      complete: function() {
        $("#grid_search").pqGrid('hideLoading');
      },
      error: function (jqXHR, exception) {
          var error_= '';
          if (jqXHR.status === 0) {
              error = 'Not connect.\n Verify Network.';
          } else if (jqXHR.status == 404) {
              error = 'Requested page not found. [404]';
          } else if (jqXHR.status == 500) {
              error = 'Internal Server Error [500].';
          } else if (exception === 'parsererror') {
              error = 'Requested JSON parse failed.';
          } else if (exception === 'timeout') {
              error = 'Time out error.';
          } else if (exception === 'abort') {
              error = 'Ajax request aborted.';
          } else {
              error = 'Uncaught Error.\n' + jqXHR.responseText;
          }
          alert_notification(error);
      },
    });
  }
  }
    
  function getEditingSuggestionRecords(first = 1)
  {
    if(first == 1){
      edt_rcd_total = 0;
      edt_rcd_offset = 0;
      edt_rcd_limit = 0;
    }
    
    if(edt_rcd_offset != 0 && edt_rcd_offset>=edt_rcd_total)
      return false;
    

    var impexp_sr_id = $('#edt_mst_sr_id').val();
    var impexp_type = $('#edt_mst_type').val();
 if(impexp_sr_id!='' && impexp_type!=''){
    $.ajax({
      type: "POST",
      url: '<?= base_url() ?>/admin/import_export/get_preview_suggestion_records',
      method: 'POST',
      data: {impexp_sr_id: impexp_sr_id, impexp_type: impexp_type, offset: edt_rcd_offset},
      dataType: 'json',
      beforeSend: function() {
        $("#suggestion_grid_search").pqGrid('showLoading');
      },
      success: function(response)
      { 
        if(response.status){
          processing = false;
          $("#suggestion_grid_search").pqGrid('hideLoading');
          
          if(edt_rcd_offset == 0)
          {
            edt_rcd_limit = response.limit;
            var total = response.result.total;
            var uploaded = response.result.uploaded;


            edt_rcd_total = total;
            $('#suggestion_total_records').text(total);
            $('#suggestion_upld_records').text(uploaded);
            
            if(impexp_type == 1 ||impexp_type == 2 ||impexp_type == 3 ||impexp_type == 4 ||impexp_type == 5){
              set_masters_suggest_colModel(response.result.columns);
            }

            if(impexp_type == 6 || impexp_type == 7 || impexp_type == 8 || impexp_type == 9 || impexp_type == 10 || impexp_type == 11){
              set_day_booksuggest_colModel(response.result.columns);
            }
          }
          
          set_masters_suggestion_dataModel(response.result.data);

          if(impexp_type == 6 || impexp_type == 7 || impexp_type == 8 || impexp_type == 9 || impexp_type == 10 || impexp_type == 11){
            daybookSummary();
          }

          var min = edt_rcd_offset + 1;
          var max = edt_rcd_offset + edt_rcd_limit;

          if(max > edt_rcd_total){
            max = edt_rcd_total;
          }

          var desc = `Displaying ${min} to ${max} of ${edt_rcd_total} `;
          $('#suggestion_desc').text(desc);
        }
        else{
          alert_notification(response.message);
        }
      },
      complete: function() {
        $("#suggestion_grid_search").pqGrid('hideLoading');
      },
      error: function (jqXHR, exception) {
          var error_= '';
          if (jqXHR.status === 0) {
              error = 'Not connect.\n Verify Network.';
          } else if (jqXHR.status == 404) {
              error = 'Requested page not found. [404]';
          } else if (jqXHR.status == 500) {
              error = 'Internal Server Error [500].';
          } else if (exception === 'parsererror') {
              error = 'Requested JSON parse failed.';
          } else if (exception === 'timeout') {
              error = 'Time out error.';
          } else if (exception === 'abort') {
              error = 'Ajax request aborted.';
          } else {
              error = 'Uncaught Error.\n' + jqXHR.responseText;
          }
          alert_notification(error);
      },
    });
  }
  }

  function summary() {
    var data = this.option('dataModel.data');

    data.forEach(function(row,indx){
      if(parseInt(row.imp_id)){
        
        if(row.imp_status == 1){
          data[indx]['pq_rowcls'] = "bg-lightgreen";
          data[indx]['pq_cellattr'] = {
                    "rd_status" : {"title": "Uploaded"},
                  };
          data[indx]['rd_status'] = 0;
        }
		 else if(row.tooltip_exists == 1){
		  data[indx]['pq_rowcls'] = "bg-off_red";		    	
		}
        else{

          data[indx]['pq_rowcls'] = "bg-off_white";
          // data[indx]['pq_cellattr'] = {};
          // data[indx]['rd_status'] = 1;
        }
      }
      else{
        data[indx]['pq_rowcls'] = "bg-white";
        data[indx]['pq_cellattr'] = {};
      }
    });
  }

  function set_masters_colModel(data)
  {
    
    var colModel = [
      { dataIndx: "sno", maxWidth: 50, minWidth: 50, align: "center", resizable: false,
          title: "",
          menuIcon: false, 
          sortable: false, 
          editable: false,
          cls: 'pq-grid-number-cell',
          render: function( ui ) {
              var rd = ui.rowData;
              var grid = this;
         
              return rd.sno + '';
          }
      }
    ]

    $.each(data, function(indx,value){
     
      colModel.push(
      { title: value, align:"left", width: 180, dataIndx: indx, 
        editable: function (ui) {
           var imp_status = ui.rowData['imp_status'];
            if (imp_status != 1) {
              return true;
            }
            return false;
        },
         })
	  
    });

    // colModel.push(
    //   { dataIndx: "rd_status", maxWidth: 50, minWidth: 50, align: "center", resizable: false,
    //     title: "",
    //     menuIcon: false, 
    //     sortable: false,
    //     editable: false,
    //     cls: 'pq-grid-number-cell',
    //     render: function( ui ) {
    //       var rd = ui.rowData;
    //       var grid = this;
          
    //       if(rd.imp_status == 1){
    //         return '<img style="width: 75%" src="<?= base_url() ?>/public/assets/img/icon-database-green.png" >';
    //       }
    //     }
    //   }
    // );

    $("#grid_search").pqGrid('option','colModel',colModel);
    $("#grid_search").pqGrid('refreshCM');
  }
  
  function create_auto_dropdown(rd,dropdown_list,ui,indx){
	  
	if(rd.tooltip_exists=="0"){ 
			   alert_notification("No suggestions exists!!!");
			   return false;
			 } 
			 var impexp_type = $('#edt_mst_type').val();
            var $inp = ui.$cell.find("input");
            $inp.autocomplete({
                source: dropdown_list,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, uis) {
                    event.preventDefault();
                    $(this).val(uis.item.label);  
					if(indx=='impacc_prt_sug'){
					  rd.impacc_prt_sug_id=uis.item.label;  
					}
					if(indx=='impacc_grp_sug'){
					rd.impacc_grp_sug_id=uis.item.label;
					}
					if(indx=='impaccgrp_under_grp_sug'){
					rd.impaccgrp_under_grp_sug_id=uis.item.label;
					}
					if(indx=='impaccgrp_under_prt_sug'){
					rd.impaccgrp_under_prt_sug_id=uis.item.label;
					}
					if(indx=='impitm_cat_sug'){
					rd.impitm_cat_sug_id=uis.item.label;
					}
					if(indx=='impitm_pur_acc_sug'){
					rd.impitm_pur_acc_sug_id=uis.item.label;
					}
					if(indx=='impitm_sale_acc_sug'){
					rd.impitm_sale_acc_sug_id=uis.item.label;
					}
					if(indx=='impitmgrp_under_grp_sug'){
					rd.impitmgrp_under_grp_sug_id=uis.item.label;
					}
					
					if(indx=='imptxnvch_series_sug'){
					  rd.imptxnvch_series_sug_id=uis.item.label;
					}
					
					
					
					
                }
            }).focus(function () {
                $(this).autocomplete("search", "");               
                rd.pq_cellattr = {};
            }).focusout(function () {			
					
					 $("#suggestion_grid_search").pqGrid('refreshDataAndView');	
            });  
	  
  }
  
  function set_masters_suggest_colModel(data)
  {
    
    var colModel = [
      { dataIndx: "sno", maxWidth: 50, minWidth: 50, align: "center", resizable: false,
          title: "",
          menuIcon: false, 
          sortable: false, 
          editable: false,
          cls: 'pq-grid-number-cell',
          render: function( ui ) {
              var rd = ui.rowData;
			 
			  
              var grid = this;
         
              return rd.sno + '';
          }
      } ]

    $.each(data, function(indx,value){	
      if(indx=="impacc_prt_sug" || indx=="impacc_grp_sug" || indx=="impaccgrp_under_prt_sug" || indx=="impaccgrp_under_grp_sug" || indx=="impitmgrp_under_grp_sug" || indx=="impitm_cat_sug" || indx=="impitm_sale_acc_sug" || indx=="impitm_pur_acc_sug"){
		var autoCompleteMaster = function (ui) {
            var rd = ui.rowData;
			if(indx=='impacc_prt_sug'){
			  create_auto_dropdown(rd,rd.impacc_prt_sug_list,ui,indx);					
			}
			if(indx=='impacc_grp_sug'){
				 create_auto_dropdown(rd,rd.impacc_grp_sug_list,ui,indx);							
			}
			
			if(indx=='impaccgrp_under_prt_sug'){
				create_auto_dropdown(rd,rd.impaccgrp_under_prt_sug_list,ui,indx);						
			}
			if(indx=='impaccgrp_under_grp_sug'){
			 create_auto_dropdown(rd,rd.impaccgrp_under_grp_sug_list,ui,indx);							
			}
		 if(indx=='impitmgrp_under_grp_sug'){
			create_auto_dropdown(rd,rd.impitmgrp_under_grp_sug_list,ui,indx);
		 }

		if(indx=='impitm_cat_sug'){
			create_auto_dropdown(rd,rd.impitm_cat_sug_list,ui,indx);				
		 }		
		if(indx=='impitm_sale_acc_sug'){
		  create_auto_dropdown(rd,rd.impitm_sale_acc_list,ui,indx);
		}
		if(indx=='impitm_pur_acc_sug'){
			 create_auto_dropdown(rd,rd.impitm_pur_acc_sug_list,ui,indx);
			}			
		if(indx=='imptxnvch_series_sug'){
			 create_auto_dropdown(rd,rd.imptxnvch_series_sug_list,ui,indx);
			}
				
        }  
		colModel.push(
      { title: value, align:"left", width: 180, dataIndx: indx, 
        editable: function (ui) {
           var imp_status = ui.rowData['imp_status'];
            if (imp_status != 1) {
              return true;
            }
            return false;
        },
		 editor: {                   
                  type: "textbox",
                  init:autoCompleteMaster,
                  options: [],
                 }
      })  
		  
	   }else{
      colModel.push(
        { title: value, align:"left", width: 180, dataIndx: indx, 
        editable: function (ui) {
           var imp_status = ui.rowData['imp_status'];
            if (imp_status != 1) {
              return true;
            }
            return false;
           }		  
         })
	   }
    });

    // colModel.push(
    //   { dataIndx: "rd_status", maxWidth: 50, minWidth: 50, align: "center", resizable: false,
    //     title: "",
    //     menuIcon: false, 
    //     sortable: false,
    //     editable: false,
    //     cls: 'pq-grid-number-cell',
    //     render: function( ui ) {
    //       var rd = ui.rowData;
    //       var grid = this;
          
    //       if(rd.imp_status == 1){
    //         return '<img style="width: 75%" src="<?= base_url() ?>/public/assets/img/icon-database-green.png" >';
    //       }
    //     }
    //   }
    // );

    $("#suggestion_grid_search").pqGrid('option','colModel',colModel);
    $("#suggestion_grid_search").pqGrid('refreshCM');
	 $("#suggestion_grid_search").pqGrid('refreshDataAndView');
  }
  
  function set_masters_dataModel(data)
  {
    $("#grid_search").pqGrid('option','dataModel.data',data);
    $("#grid_search").pqGrid('refreshDataAndView');
  }
  function set_masters_suggestion_dataModel(data)
  {
    $("#suggestion_grid_search").pqGrid('option','dataModel.data',data);
    $("#suggestion_grid_search").pqGrid('refreshDataAndView');
  }
  
  function set_day_booksuggest_colModel(data){
	var colModel = [
      { dataIndx: "sno", maxWidth: 50, minWidth: 50, align: "center", resizable: false,
          title: "",
          menuIcon: false, 
          sortable: false,
          editable: false, 
          cls: 'pq-grid-number-cell',
          render: function( ui ) {
            var rd = ui.rowData;
            var grid = this;
            if(rd.sno)
              return rd.sno + '';

            return '';
          }
      }];

    if(data['imptxnvch_date']){
      colModel.push({ title: data['imptxnvch_date'], align:"left", width: 180, dataIndx: 'imptxnvch_date', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
      });
    }

    if(data['imptxnvch_type']){
      colModel.push({ title: data['imptxnvch_type'], align:"left", width: 180, dataIndx: 'imptxnvch_type', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
        editor: {
          type: 'textbox',
          init: function(ui){
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            var grid = this;

            $inp.on("change", function (evt) {
              var input = $(this).val();
              daybookSummary(rd.imp_id);
            });
          },
        },
      });
    }
	
	if(data['imptxnvch_supplytype']){
      colModel.push({ title: data['imptxnvch_supplytype'], align:"left", width: 180, dataIndx: 'imptxnvch_supplytype', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
        editor: {
          type: 'textbox',
          init: function(ui){
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            var grid = this;

            $inp.on("change", function (evt) {
              var input = $(this).val();
              daybookSummary(rd.imp_id);
            });
          },
        },
      });
    }

    if(data['imptxnvch_series']){
      colModel.push({ title: data['imptxnvch_series'], align:"left", width: 180, dataIndx: 'imptxnvch_series', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
      });
    }
	
	if(data['imptxnvch_series_sug']){
		var autoCompleteMaster = function (ui) {
            var rd = ui.rowData;
			 create_auto_dropdown(rd,rd.imptxnvch_series_sug_list,ui);								
		   }
		
		
      colModel.push({ title: data['imptxnvch_series_sug'], align:"left", width: 180, dataIndx: 'imptxnvch_series_sug', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
		editor: {                   
                  type: "textbox",
                  init:autoCompleteMaster,
                  options: [],
                 }
      });
    }

    if(data['imptxnvch_bill_no']){
      colModel.push({ title: data['imptxnvch_bill_no'], align:"left", width: 180, dataIndx: 'imptxnvch_bill_no', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
      });
    }

    if(data['imptxnvch_uqc']){
      colModel.push({ title: data['imptxnvch_uqc'], align:"left", width: 180, dataIndx: 'imptxnvch_uqc', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(!rd.sno) // first line
                return true;
            }
          }
          return false;
        },
        editor: {                   
            type: "textbox",
            init: function (ui) {
              var rd = ui.rowData;
              var $inp = ui.$cell.find("input");
              var list = get_unit_list();
              var grid = this;

              $inp.autocomplete({
                source: list,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                  event.preventDefault();
                  $(this).val(ui.item.label);
                  rd.imptxnvch_uqc = ui.item.label;
                }
                }).focus(function () {
                  $(this).autocomplete("search", "");
                  rd.imptxnvch_uqc = '';
                }).focusout(function () {              
                  if(rd.imptxnvch_uqc != '')
                  {
                    var index = list.findIndex(function(obj) {

                      var string = obj.label.toLowerCase();
                      var text = rd.imptxnvch_uqc.toLowerCase();
                       
                      return string.includes(text); 
                    });
                    if(index > -1){
                      rd.imptxnvch_uqc = list[index].label;
                    }
                    else{
                      rd.imptxnvch_uqc = '';
                    }
                  }
                  if(rd.imptxnvch_uqc == '')
                  {
                    rd.imptxnvch_acc_bds = '';
                    rd.imptxnvch_amt_dr = '';
                    rd.imptxnvch_amt_cr = '';
                    rd.imptxnvch_qty = '';
                    grid.saveEditCell();
                    grid.refreshRow({rowIndx: ui.rowIndx});
                  }
                  daybookSummary(rd.imp_id);
                });
            }
        },
      });
    }
    
    if(data['imptxnvch_acc_bds']){
      colModel.push({ title: data['imptxnvch_acc_bds'], align:"left", width: 180, dataIndx: 'imptxnvch_acc_bds', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              return true;
            }
          }
          return false;
        },
        editor: {
          type: 'textbox',
          attr: "autocomplete='off'",
          init: function(ui){
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            var grid = this;

            
            if(rd.imp_sub_id >= 0){

              if(rd.imptxnvch_uqc != undefined && rd.imptxnvch_uqc != ''){
                var list = get_item_list();
          
                if(parseValue(rd.imptxnvch_qty) == 0){
                  rd.imptxnvch_qty = 1;
                }
              }
              else{
                var list = get_acc_bsd_list();
                rd.imptxnvch_qty = '';
              }

              $inp.autocomplete({
                source: list,
                selectItem: { on: true },
                highlightText: { on: true },
                minLength:0,
                select: function(event, ui) {
                  event.preventDefault();
                  $(this).val(ui.item.label);   
                }
              }).focus(function () {
                $(this).autocomplete("search", "");
              }).focusout(function () { 
                daybookSummary(rd.imp_id);
              });
 
                
            }
          },
        },
        render: function (ui) {
          var rd = ui.rowData;
          if(rd.imp_id){
            if(rd.imp_sub_id == -1){
              if(rd.pq_cellattr){
                rd.pq_cellattr.imptxnvch_acc_bds = {
                  "title": "Long Description"
                };
              }
              else{
                rd.pq_cellattr = {
                  "imptxnvch_acc_bds" : {"title": "Long Description"}
                };
              }
            }
          }
        },
      });  
    }
    
	if(data['imptxnvch_acc_bds_sug']){
      colModel.push({ title: data['imptxnvch_acc_bds_sug'], align:"left", width: 180, dataIndx: 'imptxnvch_acc_bds_sug', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              return true;
            }
          }
          return false;
        },
        editor: {
          type: 'textbox',
          attr: "autocomplete='off'",
          init: function(ui){
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            var grid = this;
            if(rd.imp_sub_id >= 0){

              if(rd.imptxnvch_uqc != undefined && rd.imptxnvch_uqc != ''){
                var list = get_item_list();
          
                if(parseValue(rd.imptxnvch_qty) == 0){
                  rd.imptxnvch_qty = 1;
                }
              }
              else{
                var list = rd.imptxnvch_acc_bds_sug_list;
                rd.imptxnvch_qty = '';
              }

              $inp.autocomplete({
                source: list,
                selectItem: { on: true },
                highlightText: { on: true },
                minLength:0,
                select: function(event, ui) {
                  event.preventDefault(); 
                  $(this).val(ui.item.label); 
                  rd.imptxnvch_acc_bds_sug_id=ui.item.label;			  
                }
              }).focus(function () {
                $(this).autocomplete("search", "");
              }).focusout(function () { 
                 
              });
 
                
            }else 
				return false;
          },
        },        
      });  
    }
	
    if(data['imptxnvch_amt_dr']){
      colModel.push({ title: data['imptxnvch_amt_dr'], align:"right", width: 180, dataIndx: 'imptxnvch_amt_dr', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.imp_sub_id >= 0)
                return true;
              
            }
          }
          return false;
        },
        editor: {
          type: 'textbox',
          init: function(ui){
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            var grid = this;

            $inp.on("change", function (evt) {
              var amount = $(this).val();
  
              if(parseAmount(amount)){
                if(parseAmount(rd.imptxnvch_amt_cr)){
                  rd.imptxnvch_amt_cr = 0;
                  grid.saveEditCell();
                  grid.refreshRow({rowIndx: ui.rowIndx});
                }

                  daybookSummary(rd.imp_id);
              }
            });
          },
        },
        render: function( ui ) {
          var rd = ui.rowData;
          var grid = this;

          if(rd.imp_id){
            if(rd.imp_sub_id >= 0){
              if(parseAmount(rd.imptxnvch_amt_dr)){
                return formatAmount(rd.imptxnvch_amt_dr);
              }
            }
          }
          return '';
        },
      });
    }
    
    if(data['imptxnvch_amt_cr']){
      colModel.push({ title: data['imptxnvch_amt_cr'], align:"right", width: 180, dataIndx: 'imptxnvch_amt_cr', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.imp_sub_id >= 0)
                return true;
              
            }
          }
          return false;
        },
        editor: {
          type: 'textbox',
          init: function(ui){
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            var grid = this;

            $inp.on("change", function (evt) {
              var amount = $(this).val();
  
              if(parseAmount(amount)){
                if(parseAmount(rd.imptxnvch_amt_dr)){
                  rd.imptxnvch_amt_dr = 0;
                  grid.saveEditCell();
                  grid.refreshRow({rowIndx: ui.rowIndx});
                }

                  daybookSummary(rd.imp_id);
              }
            });
          },
        },
        render: function( ui ) {
          var rd = ui.rowData;
          var grid = this;
          
          if(rd.imp_id){
            if(rd.imp_sub_id >= 0){
              if(parseAmount(rd.imptxnvch_amt_cr)){
                return formatAmount(rd.imptxnvch_amt_cr);
              }
            }
          }
          return '';
        },
      });
    }

    if(data['imptxnvch_qty']){
      colModel.push({ title: data['imptxnvch_qty'], align:"right", width: 180, dataIndx: 'imptxnvch_qty', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return false;
              if(rd.imptxnvch_uqc)
                return true;
            }
          }
          return false;
        },
        editor: {                   
            type: "textbox",
            init: function (ui) {
              var rd = ui.rowData;
              var $inp = ui.$cell.find("input");
           
              var grid = this;

              $inp.on('change', function () {
                var qty = $(this).val();
                if(qty <= 0){
                  alert_notification('Quantity must be greater than 0');
                  rd.imptxnvch_qty = 1;
                  $(this).val('1');
                  grid.saveEditCell();
                  grid.refreshRow({rowIndx: ui.rowIndx});
                }
              });
            }
        },
        render: function( ui ) {
          var rd = ui.rowData;
          var grid = this;
          
          if(rd.imp_id){
            if(rd.imp_sub_id >= 0 && rd.imptxnvch_uqc){
              rd.imptxnvch_qty = parseValue(rd.imptxnvch_qty);
              if(rd.imptxnvch_qty){
                return rd.imptxnvch_qty;
              }
            }
            else{
              rd.imptxnvch_qty = '';
            }
          }
          return '';
        },
      });
    }
    
    if(data['imptxnvch_short_narr']){
      colModel.push({ title: data['imptxnvch_short_narr'], align:"left", width: 180, dataIndx: 'imptxnvch_short_narr', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.imp_sub_id >= 0)
                return true;
            }
          }
          return false;
        },
      });
    }

    if(data['imptxnvch_mc']){
      colModel.push({ title: data['imptxnvch_mc'], align:"left", width: 180, dataIndx: 'imptxnvch_mc', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
      });
    }

    if(data['imptxnvch_pos']){
      colModel.push({ title: data['imptxnvch_pos'], align:"left", width: 180, dataIndx: 'imptxnvch_pos', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
        editor: {
          type: 'textbox',
          init: function(ui){
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            var grid = this;

            $inp.on("change", function (evt) {
              var input = $(this).val();
              daybookSummary(rd.imp_id);
            });
          },
        },
      });
    }

    if(data['imptxnvch_country_code']){
      colModel.push({ title: data['imptxnvch_country_code'], align:"left", width: 180, dataIndx: 'imptxnvch_country_code', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
      });
    }


    $("#suggestion_grid_search").pqGrid('option','colModel',colModel);
    $("#suggestion_grid_search").pqGrid('refreshCM');  
  }
  
  function set_day_book_colModel(data)
  {
    var colModel = [
      { dataIndx: "sno", maxWidth: 50, minWidth: 50, align: "center", resizable: false,
          title: "",
          menuIcon: false, 
          sortable: false,
          editable: false, 
          cls: 'pq-grid-number-cell',
          render: function( ui ) {
            var rd = ui.rowData;
            var grid = this;
            if(rd.sno)
              return rd.sno + '';

            return '';
          }
      }];

    if(data['imptxnvch_date']){
      colModel.push({ title: data['imptxnvch_date'], align:"left", width: 180, dataIndx: 'imptxnvch_date', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
      });
    }

    if(data['imptxnvch_type']){
      colModel.push({ title: data['imptxnvch_type'], align:"left", width: 180, dataIndx: 'imptxnvch_type', 
        editable: function (ui) {
			return false;
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
        editor: {
          type: 'textbox',
          init: function(ui){
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            var grid = this;

            $inp.on("change", function (evt) {
              var input = $(this).val();
              daybookSummary(rd.imp_id);
            });
          },
        },
      });
    }
	
	if(data['imptxnvch_supplytype']){
      colModel.push({ title: data['imptxnvch_supplytype'], align:"left", width: 180, dataIndx: 'imptxnvch_supplytype', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
		editor: {                   
            type: "textbox",
            init: function (ui) {
              var rd = ui.rowData;
              var $inp = ui.$cell.find("input");
              var list = get_supplytype_list();
              var grid = this;

              $inp.autocomplete({
                source: list,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                  event.preventDefault();
                  $(this).val(ui.item.label);
                  rd.imptxnvch_supplytype = ui.item.label;
				  rd.imptxnvch_supplytype_id=ui.item.id;
                }
                }).focus(function () {
                  $(this).autocomplete("search", "");                  
                }).focusout(function () {              
                  if(rd.imptxnvch_supplytype != '')
                  {
                    var index = list.findIndex(function(obj) {

                      var string = obj.label.toLowerCase();
                      var text = rd.imptxnvch_supplytype.toLowerCase();
                       
                      return string.includes(text);
                    });
                    if(index > -1){
                      rd.imptxnvch_supplytype = list[index].label;
                    }
                    else{
                      rd.imptxnvch_supplytype = '';
                    }
                  }
                  if(rd.imptxnvch_supplytype == '')
                  {
                    rd.imptxnvch_acc_bds = '';
                    rd.imptxnvch_amt_dr = '';
                    rd.imptxnvch_amt_cr = '';
                    rd.imptxnvch_qty = '';
                    grid.saveEditCell();
                    grid.refreshRow({rowIndx: ui.rowIndx});
                  }
                  daybookSummary(rd.imp_id);
                });
            }
        },
        
      });
    }

    if(data['imptxnvch_series']){
      colModel.push({ title: data['imptxnvch_series'], align:"left", width: 180, dataIndx: 'imptxnvch_series', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
      });
    }

    if(data['imptxnvch_bill_no']){
      colModel.push({ title: data['imptxnvch_bill_no'], align:"left", width: 180, dataIndx: 'imptxnvch_bill_no', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
      });
    }

    if(data['imptxnvch_uqc']){
      colModel.push({ title: data['imptxnvch_uqc'], align:"left", width: 180, dataIndx: 'imptxnvch_uqc', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(!rd.sno) // first line
                return true;
            }
          }
          return false;
        },
        editor: {                   
            type: "textbox",
            init: function (ui) {
              var rd = ui.rowData;
              var $inp = ui.$cell.find("input");
              var list = get_unit_list();
              var grid = this;

              $inp.autocomplete({
                source: list,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                  event.preventDefault();
                  $(this).val(ui.item.label);
                  rd.imptxnvch_uqc = ui.item.label;
                }
                }).focus(function () {
                  $(this).autocomplete("search", "");
                  rd.imptxnvch_uqc = '';
                }).focusout(function () {              
                  if(rd.imptxnvch_uqc != '')
                  {
                    var index = list.findIndex(function(obj) {

                      var string = obj.label.toLowerCase();
                      var text = rd.imptxnvch_uqc.toLowerCase();
                       
                      return string.includes(text);
                    });
                    if(index > -1){
                      rd.imptxnvch_uqc = list[index].label;
                    }
                    else{
                      rd.imptxnvch_uqc = '';
                    }
                  }
                  if(rd.imptxnvch_uqc == '')
                  {
                    rd.imptxnvch_acc_bds = '';
                    rd.imptxnvch_amt_dr = '';
                    rd.imptxnvch_amt_cr = '';
                    rd.imptxnvch_qty = '';
                    grid.saveEditCell();
                    grid.refreshRow({rowIndx: ui.rowIndx});
                  }
                  daybookSummary(rd.imp_id);
                });
            }
        },
      });
    }
    
    if(data['imptxnvch_acc_bds']){
      colModel.push({ title: data['imptxnvch_acc_bds'], align:"left", width: 180, dataIndx: 'imptxnvch_acc_bds', 
        editable: function (ui) {
          var rd = ui.rowData;
		if(rd.imptxnvch_mst_type=='tax')	
			return false;
          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              return true;
            }
          }
          return false;
        },
        editor: {
          type: 'textbox',
          attr: "autocomplete='off'",
          init: function(ui){
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            var grid = this;

            
            if(rd.imp_sub_id >= 0){

              if(rd.imptxnvch_uqc != undefined && rd.imptxnvch_uqc != ''){
                var list = get_item_list();
          
                if(parseValue(rd.imptxnvch_qty) == 0){
                  rd.imptxnvch_qty = 1;
                }
              }
              else{
                var list = get_acc_bsd_list();
                rd.imptxnvch_qty = '';
              }

              $inp.autocomplete({
                source: list,
                selectItem: { on: true },
                highlightText: { on: true },
                minLength:0,
                select: function(event, ui) {
                  event.preventDefault();
                  $(this).val(ui.item.label);   
                }
              }).focus(function () {
                $(this).autocomplete("search", "");
              }).focusout(function () { 
                daybookSummary(rd.imp_id);
              });
 
                
            }
          },
        },
        render: function (ui) {
          var rd = ui.rowData;
          if(rd.imp_id){
            if(rd.imp_sub_id == -1){
              if(rd.pq_cellattr){
                rd.pq_cellattr.imptxnvch_acc_bds = {
                  "title": "Long Description"
                };
              }
              else{
                rd.pq_cellattr = {
                  "imptxnvch_acc_bds" : {"title": "Long Description"}
                };
              }
            }
          }
        },
      });  
    }
    
    if(data['imptxnvch_amt_dr']){
      colModel.push({ title: data['imptxnvch_amt_dr'], align:"right", width: 180, dataIndx: 'imptxnvch_amt_dr', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.imp_sub_id >= 0)
                return true;
              
            }
          }
          return false;
        },
        editor: {
          type: 'textbox',
          init: function(ui){
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            var grid = this;

            $inp.on("change", function (evt) {
              var amount = $(this).val();
  
              if(parseAmount(amount)){
                if(parseAmount(rd.imptxnvch_amt_cr)){
                  rd.imptxnvch_amt_cr = 0;
                  grid.saveEditCell();
                  grid.refreshRow({rowIndx: ui.rowIndx});
                }

                  daybookSummary(rd.imp_id);
              }
            });
          },
        },
        render: function( ui ) {
          var rd = ui.rowData;
          var grid = this;

          if(rd.imp_id){
            if(rd.imp_sub_id >= 0){
              if(parseAmount(rd.imptxnvch_amt_dr)){
                return formatAmount(rd.imptxnvch_amt_dr);
              }
            }
          }
          return '';
        },
      });
    }
    
    if(data['imptxnvch_amt_cr']){
      colModel.push({ title: data['imptxnvch_amt_cr'], align:"right", width: 180, dataIndx: 'imptxnvch_amt_cr', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.imp_sub_id >= 0)
                return true;
              
            }
          }
          return false;
        },
        editor: {
          type: 'textbox',
          init: function(ui){
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            var grid = this;

            $inp.on("change", function (evt) {
              var amount = $(this).val();
  
              if(parseAmount(amount)){
                if(parseAmount(rd.imptxnvch_amt_dr)){
                  rd.imptxnvch_amt_dr = 0;
                  grid.saveEditCell();
                  grid.refreshRow({rowIndx: ui.rowIndx});
                }

                  daybookSummary(rd.imp_id);
              }
            });
          },
        },
        render: function( ui ) {
          var rd = ui.rowData;
          var grid = this;
          
          if(rd.imp_id){
            if(rd.imp_sub_id >= 0){
              if(parseAmount(rd.imptxnvch_amt_cr)){
                return formatAmount(rd.imptxnvch_amt_cr);
              }
            }
          }
          return '';
        },
      });
    }

    if(data['imptxnvch_qty']){
      colModel.push({ title: data['imptxnvch_qty'], align:"right", width: 180, dataIndx: 'imptxnvch_qty', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return false;
              if(rd.imptxnvch_uqc)
                return true;
            }
          }
          return false;
        },
        editor: {                   
            type: "textbox",
            init: function (ui) {
              var rd = ui.rowData;
              var $inp = ui.$cell.find("input");
           
              var grid = this;

              $inp.on('change', function () {
                var qty = $(this).val();
                if(qty <= 0){
                  alert_notification('Quantity must be greater than 0');
                  rd.imptxnvch_qty = 1;
                  $(this).val('1');
                  grid.saveEditCell();
                  grid.refreshRow({rowIndx: ui.rowIndx});
                }
              });
            }
        },
        render: function( ui ) {
          var rd = ui.rowData;
          var grid = this;
          
          if(rd.imp_id){
            if(rd.imp_sub_id >= 0 && rd.imptxnvch_uqc){
              rd.imptxnvch_qty = parseValue(rd.imptxnvch_qty);
              if(rd.imptxnvch_qty){
                return rd.imptxnvch_qty;
              }
            }
            else{
              rd.imptxnvch_qty = '';
            }
          }
          return '';
        },
      });
    }
    
    if(data['imptxnvch_short_narr']){
      colModel.push({ title: data['imptxnvch_short_narr'], align:"left", width: 180, dataIndx: 'imptxnvch_short_narr', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.imp_sub_id >= 0)
                return true;
            }
          }
          return false;
        },
      });
    }

    if(data['imptxnvch_mc']){
      colModel.push({ title: data['imptxnvch_mc'], align:"left", width: 180, dataIndx: 'imptxnvch_mc', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
      });
    }

    if(data['imptxnvch_pos']){
      colModel.push({ title: data['imptxnvch_pos'], align:"left", width: 180, dataIndx: 'imptxnvch_pos', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
		editor: {                   
            type: "textbox",
            init: function (ui) {
              var rd = ui.rowData;
              var $inp = ui.$cell.find("input");
              var poslist = get_postype_list();
              var grid = this;

              $inp.autocomplete({
                source: poslist,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                  event.preventDefault();
                  $(this).val(ui.item.label);
                  rd.imptxnvch_pos = ui.item.label;
				  rd.imptxnvch_posid=ui.item.id;
                }
                }).focus(function () {
                  $(this).autocomplete("search", "");                  
                }).focusout(function () { 
			
                  if(rd.imptxnvch_pos != '')
                  {
                    var index = poslist.findIndex(function(obj) {

                      var string = obj.label.toLowerCase();
                      var text = rd.imptxnvch_pos.toLowerCase();
                       
                      return string.includes(text);
                    });
                    if(index > -1){
                      rd.imptxnvch_pos = poslist[index].label;
                    }
                    else{
                      rd.imptxnvch_pos = '';
                    }
                  }
                  if(rd.imptxnvch_pos == '')
                  { console.log("dddd");
                    rd.imptxnvch_acc_bds = '';
                    rd.imptxnvch_amt_dr = '';
                    rd.imptxnvch_amt_cr = '';
                    rd.imptxnvch_qty = '';
                    grid.saveEditCell();
                    grid.refreshRow({rowIndx: ui.rowIndx});
                  }
                  daybookSummary(rd.imp_id);
                });
            }
        },
      });
    }

    if(data['imptxnvch_country_code']){
      colModel.push({ title: data['imptxnvch_country_code'], align:"left", width: 180, dataIndx: 'imptxnvch_country_code', 
        editable: function (ui) {
          var rd = ui.rowData;

          if(rd.imp_id){ // not empty row
            if (rd.imp_status != 1) { // not uploaded
              if(rd.sno) // first line
                return true;
            }
          }
          return false;
        },
      });
    }

    colModel.push(
      { dataIndx: "rd_status", maxWidth: 50, minWidth: 50, align: "center", resizable: false,
        title: "",
        menuIcon: false, 
        sortable: false,
        editable: false, 
        cls: 'pq-grid-number-cell',
        render: function( ui ) {
          var rd = ui.rowData;
          var grid = this;

          if(rd.sno){
            if(rd.imp_status == 1){
              return '<img style="width: 75%" src="<?= base_url() ?>/public/assets/img/icon-database-green.png" >';
            }

            if(rd.rd_status == 1){
                return '<img role="button" class="ShowSummaryModal" style="width: 75%" src="<?= base_url() ?>/public/assets/img/icon-check.png" >';
            }
         
            if(rd.rd_status == 0){
              return '<img role="button" class="refreshEdtRow" style="width: 75%" src="<?= base_url() ?>/public/assets/img/icon-error.png" >';
            }
          }
          else{
            if(rd.imp_status != 1){
              if(rd.imp_sub_id == -1){
                return `<img role="button" style="width: 50%" class="addEdtRow" src="<?= base_url() ?>/public/assets/img/icon-add.png" >`;
              }
              if(rd.imp_sub_id >= 0){
                return `<img role="button" style="width: 50%" class="deleteEdtRow" src="<?= base_url() ?>/public/assets/img/icon-delete.png" >`;
              } 
            }
          }
            
          return '';
          
        },
        postRender: function (ui) {
          var rowIndx = ui.rowIndx,
          grid = this,
          $cell = grid.getCell(ui);

          $cell.find(".refreshEdtRow")
          .bind("click", function (evt) {
               ValidationsSummary(ui.rowData.imp_id);
			    daybookSummary(ui.rowData.imp_id);
			   $("#grid_error_tab").show();
			   $("#grid_taxsummary_tab").hide();
			   $("#grid_taxsetails_tab").hide();
			   
               
          });$cell.find(".ShowSummaryModal")
          .bind("click", function (evt) {
               ValidationsSummary(ui.rowData.imp_id);
			    daybookSummary(ui.rowData.imp_id);
			    $("#grid_error_tab").show();
			   $("#grid_taxsummary_tab").hide();
			   $("#grid_taxsetails_tab").hide();
               
          });

          $cell.find(".addEdtRow")
          .bind("click", function (evt) {
              addEdtRow(rowIndx, grid);
          });

          $cell.find(".deleteEdtRow")
          .bind("click", function (evt) {
              deleteEdtRow(rowIndx, grid);
          });
        }
      }
    );

    $("#grid_search").pqGrid('option','colModel',colModel);
    $("#grid_search").pqGrid('refreshCM');
  }

  function addEdtRow(rowIndx, grid)
  {
    rd = grid.getRowData({ rowIndx: rowIndx });
    var imp_id = rd.imp_id;

    grid.addRow({ rowData : {
      imp_id: rd.imp_id, 
      imp_sub_id: 0,
      imp_status: 0,
      imptxnvch_acc_bds: '',
      imptxnvch_amt_cr: 0,
      imptxnvch_amt_dr: 0,
      imptxnvch_short_narr: '',
      imptxnvch_date: '',
      imptxnvch_type: '',
      imptxnvch_series: '',
      imptxnvch_bill_no: '',
      imptxnvch_pos: '',
      imptxnvch_country_code: ''
    }, rowIndx: rowIndx });

    
    daybookSummary(imp_id);
  }


  function deleteEdtRow(rowIndx, grid)
  {
    var rd = grid.getRowData({ rowIndx: rowIndx });
    var imp_id = rd.imp_id;

    grid.deleteRow({ rowIndx: rowIndx });
    daybookSummary(imp_id);
  };

function ValidationsSummary(imp_id_ = 0) {
	  // show modal box with tabs Error,Tax Details,Tax Summary,
	  $("#txn_history_myModal").modal("show");
	  
}

  function daybookSummary(imp_id_ = 0) {
	
    var data = $("#grid_search").pqGrid('option','dataModel.data');
    const validValues = [3, 5, 14, 15, 16];
    var imp_id_arr = [];
    if(imp_id_ != 0){
      imp_id_arr.push(imp_id_);
    }
    else{
      data.forEach(function(row,indx){

        if(parseInt(row.imp_id) && row.imp_status == 0){
          if(row.sno){
              imp_id_arr.push(row.imp_id);
          }
        }
      });
    }

    var impexp_type = $('#edt_mst_type').val();

    imp_id_arr.forEach(function(imp_id){

      var final = [];
      var error_list = [];
      var i = 0;
      var li = 0
   
      data.forEach(function(row,indx){
        if(row.imp_id == imp_id){
          if(row.sno){i = indx;} // first row
          final[indx] = row;
          li = indx; // last row
        }
      });
      
      var credit_total = 0;
      var debit_total = 0;
      var acc_name_err = 0;
	  var tax_cr_total=0;
	  var tax_dr_total=0;
	  

      final.forEach(function(row,indx){
		if(row.imptxnvch_mst_type=='tax'){
		tax_cr_total += parseAmount(row.imptxnvch_amt_cr);
            tax_dr_total += parseAmount(row.imptxnvch_amt_dr);	
		}
        if(row.imp_sub_id > -1){
          if(row.imptxnvch_acc_bds.trim() == ''){
            acc_name_err++;
          }
          else{
           credit_total += parseAmount(row.imptxnvch_amt_cr);
            debit_total += parseAmount(row.imptxnvch_amt_dr);
          } 
        }
      });

      if(final[i]['imptxnvch_date'] == ''){
        error_list.push(`Voucher Date is required`);
      }

      if(final[i]['imptxnvch_type'] == ''){
        error_list.push(`Voucher Type is required`);
      }

      if(final[i]['imptxnvch_series'] == ''){
        error_list.push(`Voucher Series is required`);
      }

      if(acc_name_err > 0){
        error_list.push(`Account Name Missing`);
      }

      var balance = parseAmount(debit_total) - parseAmount(credit_total)
      if(balance != 0){

        if(impexp_type == 8 || impexp_type == 9)
        {
		  	
          data[i]['imptxnvch_amt_dr'] = credit_total;
          data[i]['imptxnvch_amt_cr'] = '';
        }
		else if(impexp_type == 10 || impexp_type == 11)
        {
			
          data[i]['imptxnvch_amt_dr'] = '';
          data[i]['imptxnvch_amt_cr'] = debit_total;
        }
        else{
          // if(acc_name_err == 0){

          //   $("#grid_search").pqGrid('addRow',
          //     { rowData: {
          //       imp_id: imp_id, 
          //       imp_sub_id: 0,
          //       imp_status: 0,
          //       imptxnvch_acc_bds: '',
          //       imptxnvch_amt_cr: 0,
          //       imptxnvch_amt_dr: 0,
          //       imptxnvch_short_narr: '',
          //       imptxnvch_date: '',
          //       imptxnvch_type: '',
          //       imptxnvch_series: '',
          //       imptxnvch_bill_no: '',
          //       imptxnvch_pos: '',
          //       imptxnvch_country_code: ''
          //     }, rowIndx: li }
          //   );

          //   if(balance > 0){
          //     data[li]['imptxnvch_amt_cr'] = balance;
          //   }
          //   else{
          //     data[li]['imptxnvch_amt_dr'] = Math.abs(balance);
          //   }
          //   $("#grid_search").pqGrid('refreshRow',{rowIndx:li}); 

          //   error_list.push(`Account Name Missing`);
          // }

          var htm = ``;
          htm += `Total Mismatch<br>`;
          htm += `<ul>`;
          htm += `<li>DR- ${debit_total}</li>`;
          htm += `<li>CR- ${credit_total}</li>`;
          htm += `</ul>`;
          error_list.push(htm);
        }
      }


      var brief_list = [];
      var tax_summary = '';

      

      if(impexp_type == 8){

        var v_status = true;
		
        var v_status = true;
        if(validValues.includes(Number(supply_type_id)) && (tax_cr_total>0 || tax_dr_total>0)){
		  v_status = false;
          error_list.push(`Tax is not allowed`);	
		}
        if(final[i]['imptxnvch_type'] != 'SALE'){
          v_status = false;
          error_list.push(`Voucher Type must be SALE`);
        }
		if(final[i]['imptxnvch_supplytype'] == ''){
          v_status = false;
          error_list.push(`Supply Type is required`);
        }
        if(final[i]['imptxnvch_pos'] == ''){
          v_status = false;
          error_list.push(`POS is required`);
        }

        if(final[i]['imptxnvch_country_code'] == ''){
          v_status = false;
          error_list.push(`Country Code is required`);
        }

        if(parseAmount(final[i]['imptxnvch_amt_dr'])==0){
          v_status = false;
          error_list.push(`Party Account must be debited`);
        }

        if(final[i]['imptxnvch_acc_bds'] != ''){
          var acc_bds = final[i]['imptxnvch_acc_bds'];
          acc_bds = acc_bds.toLowerCase().trim();

          var acc_bsd_list = get_acc_bsd_list();

          var index = acc_bsd_list.findIndex(function(obj) {
            return obj.label.toLowerCase().trim() == acc_bds;
          });

          if(index > -1){
            var obj = acc_bsd_list[index];
  
            if(obj.is_acc != 1){
              v_status = false;
              error_list.push(`Party must be Account`);
            }
            if(v_status && obj.is_bbb != 1 && obj.is_cash != 1){
              v_status = false;
              error_list.push(`Invalid Party Account "${obj.label}"`);
            }
          }
          else{
            v_status = false;
            error_list.push(`Invalid Party Account Name`);
          }

        }

        if(v_status){
          var resp = sale_non_item_valid(i,li,final);

          resp['error_list'].forEach(function(err){
            error_list.push(err);
          });
          resp['brief_list'].forEach(function(brief){
            brief_list.push(brief);
          });

          tax_summary = resp['tax_summary'];
        }
      }

      if(impexp_type == 9){
		var supply_type_id =final[i]['imptxnvch_supplytype_id'];
		//tax_cr_total
		
        var v_status = true;
        if(validValues.includes(Number(supply_type_id)) && (tax_cr_total>0 || tax_dr_total>0)){
		  v_status = false;
          error_list.push(`Tax is not allowed`);	
		}
		
		if(final[i]['imptxnvch_type'] != 'SALE'){
          v_status = false;
          error_list.push(`Voucher Type must be SALE`);
        }
		
		if(final[i]['imptxnvch_supplytype'] == ''){
          v_status = false;
          error_list.push(`Supply Type is required`);
        }

        if(final[i]['imptxnvch_pos'] == ''){
          v_status = false;
          error_list.push(`POS is required`);
        }

        if(final[i]['imptxnvch_country_code'] == ''){
          v_status = false;
          error_list.push(`Country Code is required`);
        }

        if(parseAmount(final[i]['imptxnvch_amt_dr'])==0){
          v_status = false;
          error_list.push(`Party Account must be debited`);
        }

        if(final[i]['imptxnvch_acc_bds'] != ''){
          var acc_bds = final[i]['imptxnvch_acc_bds'];
          acc_bds = acc_bds.toLowerCase().trim();

          var acc_bsd_list = get_acc_bsd_list();

          var index = acc_bsd_list.findIndex(function(obj) {
            return obj.label.toLowerCase().trim() == acc_bds;
          });

          if(index > -1){
            var obj = acc_bsd_list[index];
		    if(obj.is_acc != 1){
              v_status = false;
              error_list.push(`Party must be Account`);
            }
            if(v_status && obj.is_bbb != 1 && obj.is_cash != 1){
              v_status = false;
              error_list.push(`Invalid Party Account "${obj.label}"`);
            }
          }
          else{
            v_status = false;
            error_list.push(`Invalid Party Account Name`);
          }

        }

        if(v_status){
          var resp = sale_item_valid(i,li,final);

          resp['error_list'].forEach(function(err){
            error_list.push(err);
          });
          resp['brief_list'].forEach(function(brief){
            brief_list.push(brief);
          });

          tax_summary = resp['tax_summary'];
        }
      }


      var html = ``;
	var error_html = ``;
      brief_list.forEach(function(obj){
        html = ``;
        var indx = obj.indx;
        var brief = obj.brief;

        html = `<ul class="text-primary">`;
        brief.forEach(function(brf){
          html += `<li>${brf}</li>`;
        });
        html += `</ul>`;

        if(data[indx]){
         /*  data[indx]['pq_cellattr'] = {
            "imptxnvch_acc_bds" : {"title": html},
          }; */
          $("#grid_search").pqGrid('refreshRow',{rowIndx: indx});
        }
      });
      
	  
	  
      if(error_list.length > 0){
		
        error_html = `<ul class="text-danger">`;
        error_list.forEach(function(err){
          error_html += `<li>${err}</li>`;
        });
        error_html += `</ul>`;
		
		  
        html = ``;
        html = `<ul class="text-danger">`;
        error_list.forEach(function(err){
          html += `<li>${err}</li>`;
        });
        html += `</ul>`;
        
        if(tax_summary != '')
          html += `<br>`+tax_summary;

        data[i]['rd_status'] = 0;
		/* hide tooltip in case of erro show on click modal box*/
       /*  data[i]['pq_cellattr'] = {
                "rd_status" : {"title": html},
              }; */
      }else{
		//  console.log(error_list);
		 //  html = `<span class="text-success">Validatedo</span>`;
		  
     /*  if(tax_summary != '')
        html += `<br>`+tax_summary; */
      
      data[i]['rd_status'] = 1;
      data[i]['pq_cellattr'] = {
              "rd_status" : {"title": ""},
            };
	  }
	  if(error_html=='')
       $("#grid_error_tab").html('No Error Found!!');
      else 
		 $("#grid_error_tab").html(error_html); 
	  
	   
	   $("#grid_taxsummary_tab").hide();
      $("#grid_search").pqGrid('refreshRow',{rowIndx: i});
    });
  }
  
  $(document).on('click','#edt_save_record',function(){
   
    var final = [];
    var data = $("#grid_search").pqGrid('option','dataModel.data');
	
    data.forEach(function(rd){
      if(rd.imp_id && rd.imp_status == 0){
        final.push(rd);
      }
    });

    final = JSON.stringify(final);

    if(final.length == 0){
      alert_notification('No record to update');
      return false;
    }

    var impexp_sr_id = $('#edt_mst_sr_id').val();
    var impexp_type = $('#edt_mst_type').val();

    $.ajax({
      type: "POST",
      url: '<?= base_url() ?>/admin/import_export/save_editing_records',
      method: 'POST',
      data: {impexp_sr_id: impexp_sr_id, impexp_type: impexp_type, data: final},
      dataType: 'json',

      beforeSend: function() {
        $("#grid_search").pqGrid('showLoading');
        $(".edt_btns").attr('disabled', 'disabled');
      },
      success: function(response)
      { 
        if(response.status){
          alert_success(response.message);
          getEditingRecords(0);
        }
        else{
          alert_notification(response.message);
        }
      },
      complete: function() {
        $("#grid_search").pqGrid('hideLoading');
        $(".edt_btns").attr('disabled', false);
      },
      error: function (jqXHR, exception) {
          var error_= '';
          if (jqXHR.status === 0) {
              error = 'Not connect.\n Verify Network.';
          } else if (jqXHR.status == 404) {
              error = 'Requested page not found. [404]';
          } else if (jqXHR.status == 500) {
              error = 'Internal Server Error [500].';
          } else if (exception === 'parsererror') {
              error = 'Requested JSON parse failed.';
          } else if (exception === 'timeout') {
              error = 'Time out error.';
          } else if (exception === 'abort') {
              error = 'Ajax request aborted.';
          } else {
              error = 'Uncaught Error.\n' + jqXHR.responseText;
          }
          alert_notification(error);
      },
    });
    
  });
  
  
  $(document).on('click','#suggestion_save_record',function(){
   
    var final = [];
    var data = $("#suggestion_grid_search").pqGrid('option','dataModel.data');
    
	
    data.forEach(function(rd){
		
		/* if(rd.impacc_prt_sug!='')
			rd.impacc_prt_sug_id=rd.impacc_prt_sug;
		if(rd.impacc_grp_sug!='')
			rd.impacc_grp_sug_id=rd.impacc_grp_sug;
		
		if(rd.impaccgrp_under_prt_sug!='')
			rd.impaccgrp_under_prt_sug_id=rd.impaccgrp_under_prt_sug;
		if(rd.impaccgrp_under_grp_sug!='')
			rd.impaccgrp_under_grp_sug_id=rd.impaccgrp_under_grp_sug;
		
		if(rd.impitmgrp_under_grp_sug!='')
			rd.impitmgrp_under_grp_sug_id=rd.impitmgrp_under_grp_sug;
		
		if(rd.impitm_cat_sug!='')
			rd.impitm_cat_sug_id=rd.impitm_cat_sug;
		if(rd.impitm_sale_acc_sug!='')
			rd.impitm_sale_acc_sug_id=rd.impitm_sale_acc_sug;
		if(rd.impitm_pur_acc_sug!='')
			rd.impitm_pur_acc_sug_id=rd.impitm_pur_acc_sug;
		
         if(rd.imptxnvch_acc_bds_sug!='')
			rd.imptxnvch_acc_bds_sug_id=rd.imptxnvch_acc_bds_sug;
		
		 if(rd.imptxnvch_series_sug_list!='')
			rd.imptxnvch_series_sug_list_id=rd.imptxnvch_series_sug_list; */
		
      if(rd.imp_id && rd.imp_status == 0){
        final.push(rd);
      }
    });

    final = JSON.stringify(final);
	
    if(final.length == 0){
      alert_notification('No record to update');
      return false;
    }

    var impexp_sr_id = $('#edt_mst_sr_id').val();
    var impexp_type = $('#edt_mst_type').val();

    $.ajax({
      type: "POST",
      url: '<?= base_url() ?>/admin/import_export/save_suggestion_editing_records',
      method: 'POST',
      data: {impexp_sr_id: impexp_sr_id, impexp_type: impexp_type, data: final},
      dataType: 'json',

      beforeSend: function() {
        $("#suggestion_grid_search").pqGrid('showLoading');
        $(".edt_btns").attr('disabled', 'disabled');
      },
      success: function(response)
      { 
        if(response.status){
          alert_success(response.message);
          getEditingSuggestionRecords(0);
        }
        else{
          alert_notification(response.message);
        }
      },
      complete: function() {
        $("#suggestion_grid_search").pqGrid('hideLoading');
        $(".edt_btns").attr('disabled', false);
      },
      error: function (jqXHR, exception) {
          var error_= '';
          if (jqXHR.status === 0) {
              error = 'Not connect.\n Verify Network.';
          } else if (jqXHR.status == 404) {
              error = 'Requested page not found. [404]';
          } else if (jqXHR.status == 500) {
              error = 'Internal Server Error [500].';
          } else if (exception === 'parsererror') {
              error = 'Requested JSON parse failed.';
          } else if (exception === 'timeout') {
              error = 'Time out error.';
          } else if (exception === 'abort') {
              error = 'Ajax request aborted.';
          } else {
              error = 'Uncaught Error.\n' + jqXHR.responseText;
          }
          alert_notification(error);
      },
    });
    
  });

  var acc_bsd_list = {};
  var get_supplytype = {};
   function get_supplytype_list()
  {
	  get_supplytype = <?php echo $SupplyTypesLabels;?>;
   return get_supplytype;
  }
  var get_postype = {};
   function get_postype_list()
  {
	  get_postype = <?php echo $StatesLabels;?>;
   return get_postype;
  }
  
  function get_acc_bsd_list()
  {

    if(!isEmptyObject(acc_bsd_list)){
      return acc_bsd_list;
    }

    $.ajax({
      url: '<?= base_url() ?>/admin/import_export/get_acc_bsd_list',
      method: 'GET',
      data: {},
      dataType: 'json',
      async : false,
      beforeSend: function() {
        show_loader();
      },
      success: function(response)
      { 
        if(response.status){
          acc_bsd_list = response.data;
        }
        else{
          alert_notification(response.message);
        }
      },
      complete: function() {
        stop_loader();
      },
      error: function (jqXHR, exception) {
          var error_= '';
          if (jqXHR.status === 0) {
              error = 'Not connect.\n Verify Network.';
          } else if (jqXHR.status == 404) {
              error = 'Requested page not found. [404]';
          } else if (jqXHR.status == 500) {
              error = 'Internal Server Error [500].';
          } else if (exception === 'parsererror') {
              error = 'Requested JSON parse failed.';
          } else if (exception === 'timeout') {
              error = 'Time out error.';
          } else if (exception === 'abort') {
              error = 'Ajax request aborted.';
          } else {
              error = 'Uncaught Error.\n' + jqXHR.responseText;
          }
          stop_loader();
          alert_notification(error);
      },
    });

    return acc_bsd_list;
  }

  function set_upld_mst_acc() 
  {
    var list = get_acc_bsd_list();
    list = structuredClone(list);
    var non_cash_list = list.filter(function (el) {

      var label = el.label.toLowerCase();
      return !label.includes('cash') ? true : false;
    });

    $('#upld_mst_acc').off('blur'); // unbind event first
    
    if($('#upld_mst_acc').hasClass('ui-autocomplete-input')) {
        $('#upld_mst_acc').autocomplete("destroy");
    }
    
    $('#upld_mst_acc').autocomplete({
        source: non_cash_list,
        minLength: 0,
        select: function( event, ui ) {
          $(this).val(ui.item.label);
        }
    })
    .on('focus', function(){
        $(this).autocomplete("search", "" );
        $('#upld_mst_acc').val('');
    })
    .on('blur', function(){
      if($(this).val() != '')
      {
        var acc = $(this).val();
        var index = list.findIndex(function(obj) {
          var string = obj.label.toLowerCase();
          var text = acc.toLowerCase();
          return  string.includes(text);
        });
        if(index > -1){
          $(this).val(list[index].label);
        }
        else{
          $('#upld_mst_acc').val('');
        }
      }
    });
  }

  var tax_list = {};
  function get_tax_list()
  {
    if(!isEmptyObject(tax_list)){
      return tax_list;
    }
   
    $.ajax({
      url: '<?= base_url() ?>/admin/import_export/get_tax_list',
      method: 'GET',
      data: {},
      dataType: 'json',
      async : false,
      beforeSend: function() {
        show_loader();
      },
      success: function(response)
      { 
        if(response.status){
          tax_list = response.data;
        }
        else{
          alert_notification(response.message);
        }
      },
      complete: function() {
        stop_loader();
      },
      error: function (jqXHR, exception) {
          var error_= '';
          if (jqXHR.status === 0) {
              error = 'Not connect.\n Verify Network.';
          } else if (jqXHR.status == 404) {
              error = 'Requested page not found. [404]';
          } else if (jqXHR.status == 500) {
              error = 'Internal Server Error [500].';
          } else if (exception === 'parsererror') {
              error = 'Requested JSON parse failed.';
          } else if (exception === 'timeout') {
              error = 'Time out error.';
          } else if (exception === 'abort') {
              error = 'Ajax request aborted.';
          } else {
              error = 'Uncaught Error.\n' + jqXHR.responseText;
          }
          alert_notification(error);
      },
    });

    return tax_list;
  }

  function sale_non_item_valid(i,li,final)
  {
	
    var error_list = [];
    var brief_list = [];
	var item_taxsummary_json = {};

    var acc_bsd_list = get_acc_bsd_list();
    var tax_list = get_tax_list();
    tax_list = structuredClone(tax_list);
    
    var tax_accounts = [];
    var dr_acc_err = 0;
    
    final.forEach(function(row,indx){
      if(indx != i && indx != li){ // skip first n last
			var supply_type_id = row.imptxnvch_supplytype_id;
			const validValues = [3, 5, 14, 15, 16];
        var acc_bds = row.imptxnvch_acc_bds.toLowerCase().trim();
        if(acc_bds != '')
        {

        brief = [];
        var pos = parseInt(final[i]['imptxnvch_posid']);

        var index = acc_bsd_list.findIndex(function(obj) {
          return obj.label.toLowerCase().trim() == acc_bds;
        });

        if(index > -1){

          var obj = acc_bsd_list[index];
		  
		//  console.log(obj)
          var drcr = 'c';

          if(parseAmount(row.imptxnvch_amt_cr) >= 0){
            drcr = 'c';
          }
          if(parseAmount(row.imptxnvch_amt_dr) > 0){
            drcr = 'd';
          }
          // tax if credit side
          
          if(drcr == 'c')
          {
            if(obj.is_acc == 1 && obj.cannotselected == 1){
              brief.push('Invalid Credit Account');
              error_list.push(`Invalid Credit Account "${obj.label}"`);
            }

            var amount = parseAmount(row.imptxnvch_amt_cr);

            var tax_status = false;
            var igst_rate = 0;
            var cess_rate = 0;
            var cess_basis = 0;

            if(obj.is_acc == 1 && !validValues.includes(Number(supply_type_id))){
              tax_status = true;
              igst_rate = parseAmount(obj.igst_rate);
              cess_rate = parseAmount(obj.cess_rate);
              cess_basis = parseInt(obj.cess_basis);
            }

            if(obj.is_sundry==1 && obj.is_tax_account==0 && !validValues.includes(Number(supply_type_id.trim()))){
              tax_status = true;
              igst_rate = parseAmount(obj.bl_tx_igst_rte);
              cess_rate = parseAmount(obj.b_tx_cess_rte);
              cess_basis = parseInt(obj.cess_basis);
            }

            if(obj.is_sundry==1 && obj.is_tax_account==1){
              tax_accounts.push({
                indx: indx,
                id: obj.id,
                name: obj.label,
                amt: amount,
              });
            }

            if(tax_status){
				var total_tax_value=0;
              if(pos != BO_STATE_CODE)// 2 taxes IGST n CESS
              {   
                var igst = (amount * igst_rate)/100;
                igst = parseAmount(igst);
				total_tax_value +=igst;
                tax_list['IGST']['status'] = 1;
                tax_list['IGST']['value'] += igst; 
                brief.push(`IGST (${igst_rate}%): `+formatAmount(igst));

                var cess = 0
                if(cess_basis == 1){
                  cess = (amount * cess_rate)/100;
                  cess = parseAmount(cess);
				 	
                  tax_list['CESS']['status'] = 1;
                  tax_list['CESS']['value'] += cess;
                  brief.push(`CESS (${cess_rate}%): `+formatAmount(cess));
                }
                else{ //error 
                  var err = 'TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
                  tax_list['CESS']['status'] = 2;
                  tax_list['CESS']['error'] = err;
                  brief.push(`CESS (${cess_rate}%): `+err);
                }
              }
              else{// 3 taxes CESS,CGST, SGST or UT-TAX
                var gst_rate = parseAmount(igst_rate/2);
                var cgst = (amount * gst_rate) / 100;
                cgst = parseAmount(cgst);
				total_tax_value +=cgst;
                tax_list['CGST']['status'] = 1;
                tax_list['CGST']['value'] += cgst;
                brief.push(`CGST (${gst_rate}%): `+formatAmount(cgst));
				var gst_rate_string=`${gst_rate}%`;

                var ut_arr = [35,4,26,25,31,38,34,97];
                if(pos != 35 && pos != 4 && pos != 26 && pos != 25 && pos != 31 && pos != 38 && pos != 34 && pos != 97){
                  
                  var sgst = cgst;
				  total_tax_value +=sgst;
                  tax_list['SGST']['status'] = 1;
                  tax_list['SGST']['value'] += sgst;
                  brief.push(`SGST (${gst_rate}%): `+formatAmount(sgst));
				  var gst_rate_string=`${gst_rate}%`;
                }
                else{
                  var ut_tax = cgst;
				  total_tax_value +=ut_tax;
                  tax_list['UT-TAX']['status'] = 1;
                  tax_list['UT-TAX']['value'] += ut_tax;
                  brief.push(`UT-TAX (${gst_rate}%): `+formatAmount(ut_tax));
				  var gst_rate_string=`${igst_rate}%`;
                }

                var cess = 0
                if(cess_basis == 1){
                  cess = (amount * cess_rate)/100;
                  cess = parseAmount(cess);
					total_tax_value +=cess;
                  tax_list['CESS']['status'] = 1;
                  tax_list['CESS']['value'] += cess;
                  brief.push(`CESS (${cess_rate}%): `+formatAmount(cess));
				  var gst_rate_string=`${cess_rate}%`;
                }
                else{
                  var err = 'TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
                  tax_list['CESS']['status'] = 2;
                  tax_list['CESS']['error'] = err;
                  brief.push(`CESS (${cess_rate}%): `+err);
				   var gst_rate_string=`${cess_rate}%`;
                } 
              }
			 if(item_taxsummary_json[obj.tax_cat_id]){
                    if(item_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac]){						
						item_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac].push({'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.acc_id,'tax_item_name':obj.label,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':amount,'tax_rate':igst,'tax_rate_string':gst_rate_string,'cess_basis':obj.cess_basis,'cess_rate':obj.cess_rate,'igst':igst,'cess':cess,'cgst':cgst,'sgst':sgst,'total_tax':parseAmount(total_tax_value)});						
					  }
					  else{
					   item_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac]=[{'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.acc_id,'tax_item_name':obj.label,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':amount,'tax_rate':igst,'tax_rate_string':gst_rate_string,'cess_basis':obj.cess_basis,'cess_rate':obj.cess_rate,'igst':igst,'cess':cess,'cgst':cgst,'sgst':sgst,'total_tax':parseAmount(total_tax_value)}];                          
					  }
				}else{
				    item_taxsummary_json[obj.tax_cat_id]={
                                [obj.item_hsn_sac] : [{'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.acc_id,'tax_item_name':obj.label,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':amount,'tax_rate':igst,'tax_rate_string':gst_rate_string,'cess_rate':obj.cess_rate,'cess_basis':obj.cess_basis,'igst':igst,'cess':cess,'cgst':cgst,'sgst':sgst,'total_tax':parseAmount(total_tax_value)}]
                     };		
				} 
			  
            }
            else{
              brief.push('No Tax');
            }
          }
          if(drcr == 'd')
          {
            if(obj.is_acc == 1){
              dr_acc_err++;
              brief.push('Account must be on credit side');
            }
            else
              brief.push('No Tax');
          }
        }
        else{
          brief.push('Invalid Account Name');
          error_list.push(`Invalid Account "${acc_bds}" `);
        }
        

        }
        else{
          brief.push('Account Name Required');
        }

        brief_list.push({indx: indx, brief: brief});
      }
    });
  
    if(dr_acc_err > 0){
      error_list.push('Only one party account is allowed');
    }
  
    var cess_err = '';
    var tax_summary=`<span class="text-warning">Tax Summary<span>`;
    tax_summary += `<ul class="text-warning">`;

    $.each(tax_list, function(indx, tax){
      
      if(tax.status == 1){
        tax_summary += '<li>' + indx + ' ' + formatAmount(tax.value) +' CR</li>';

        if(tax.id > 0){
          var index = tax_accounts.findIndex(function(obj) {
             return obj.id == tax.id;
          });

          if(index > -1){
            var obj = tax_accounts[index];
            if(parseAmount(obj.amt) < parseAmount(tax.value) || parseAmount(obj.amt) > parseAmount(tax.value))
            {
              error_list.push(tax.name + ' should be  equal to '+ formatAmount(tax.value) +' CR');
            }
            brief_list.push({indx: obj.indx, brief: ['MIN '+ formatAmount(tax.value) +' CR']});
          }
          else{
            error_list.push(tax.name + ' is required<br> MIN '+ formatAmount(tax.value) +' CR');
          }
        }
        else{
          error_list.push(indx +' Master is not created<br> MIN '+ formatAmount(tax.value) +' CR');
        }
      }
      if(tax.status == 2){
        cess_err = indx + '' + tax.error;
        error_list.push(indx + '' + tax.error);
      }
    });

    if(cess_err != '')
      tax_summary += '<li>'+cess_err+'</li>';

    tax_summary += '</ul>';

var taxsummary_json = [];		
		$.each(item_taxsummary_json, function(index,obj){
			var tax_hsn_sac='';
			var tax_rate ='';
			var tax_rate_string='';
			var cess_rate ='';
			var cgst ='';
			var sgst ='';
			var igst ='';	
            var cess ='';			
			var tax_amt='';
			var cess_basis  = '';
			var tax_cat_id ='';
			var tax_item_id = '';var tax_item_name = '';
			var total_tax   = 0;
			$.each(obj, function(index1,obj1){//categ
				var sum_tax_amt=0;
				var sum_igst=0;
				var sum_sgst=0;
				var sum_cgst=0;
				var sum_tax_rate=0;
				var sum_tax_string=0;
				var sum_cess=0;
				var sum_tax=0;
				
				$.each(obj1, function(index2,obj2){ //hsn
				tax_item_id = obj2.tax_item_id;
				tax_item_name = obj2.tax_item_name;
				tax_amt  = obj2.tax_amt;				
				igst     = obj2.igst;
				sgst     = obj2.sgst;
				cgst     = obj2.cgst;
				tax_rate = obj2.tax_rate;
				tax_rate_string = obj2.tax_rate_string;
				cess_rate = obj2.cess_rate;
				total_tax = obj2.total_tax;
				cess      = obj2.cess;
				cess_basis = obj2.cess_basis;
				tax_cat_id = obj2.tax_cat_id,
				
				sum_tax_amt   += parseFloat(tax_amt);
				sum_igst      += parseFloat(igst);
				sum_sgst      += parseFloat(sgst);
				sum_cgst      += parseFloat(cgst);
				sum_tax_rate  += parseFloat(tax_rate);				
				tax_hsn_sac   = obj2.tax_hsn_sac;
				sum_cess      += parseFloat(cess);
				sum_tax       += parseFloat(total_tax);
				})
						
				taxsummary_json.push({'tax_cat_id':index,'tax_item_name':tax_item_name,'tax_item_id':tax_item_id,"tax_hsn_sac":tax_hsn_sac,"tax_amt":sum_tax_amt,"cess_rate":cess_rate,"cess_basis":cess_basis,"tax_rate":tax_rate,"tax_rate_string":tax_rate_string,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
				});
			
		});
var tax_summary_table='';
var tax_details_summary_table='';
var tt_total_tax=0;
tax_summary_table +='<table width="100%" class="table table-responsive"><thead><tr><th>HSN/SAC</th><th>TAX RATE</th><th>TAXABLE VALUE</th><th>IGST</th><th>CGST</th><th>SGST/UGST</th><th>CESS</th><th>TOTAL TAX</th></tr></thead><tbody>';
tax_details_summary_table +='<table width="100%" class="table table-responsive"><thead><tr><th>PARTICULARS</th><th>TAX RATE</th><th>TAXABLE VALUE</th><th>IGST</th><th>CGST</th><th>SGST/UGST</th><th>CESS</th><th>TOTAL TAX</th></tr></thead><tbody>';
$.each(taxsummary_json, function(index,obj){
	tt_total_tax += parseAmount(obj.total_tax);
	tax_summary_table +='<tr><td>'+obj.tax_hsn_sac+'</td><td>'+obj.tax_rate_string+'</td><td>'+formatAmount(obj.tax_amt)+'</td><td>'+formatAmount(obj.igst)+'</td><td>'+formatAmount(obj.cgst)+'</td><td>'+formatAmount(obj.sgst)+'</td><td>'+formatAmount(obj.cess)+'</td><td>'+formatAmount(obj.total_tax)+'</td></tr>';
tax_details_summary_table +='<tr><td>'+obj.tax_item_name+'</td><td>'+obj.tax_rate_string+'</td><td>'+formatAmount(obj.tax_amt)+'</td><td>'+formatAmount(obj.igst)+'</td><td>'+formatAmount(obj.cgst)+'</td><td>'+formatAmount(obj.sgst)+'</td><td>'+formatAmount(obj.cess)+'</td><td>'+formatAmount(obj.total_tax)+'</td></tr>';


});
	tax_summary_table +='<tr><td colspan="7"></td><td><strong>'+formatAmount(tt_total_tax)+'</strong></td></tr>';
tax_details_summary_table +='<tr><td colspan="7"></td><td><strong>'+formatAmount(tt_total_tax)+'</strong></td></tr>';

tax_summary_table +='</tbody></table>';
tax_details_summary_table +='</tbody></table>';

$("#grid_taxsummary_tab").html(tax_summary_table);
$("#grid_taxsetails_tab").html(tax_details_summary_table);
    return {
      error_list: error_list,
      brief_list: brief_list,
      tax_summary: tax_summary_table,
    }
  }


  var item_list = {};
  function get_item_list()
  {
    if(!isEmptyObject(item_list)){
      return item_list;
    }
   
    $.ajax({
      url: '<?= base_url() ?>/admin/import_export/get_item_list',
      method: 'GET',
      data: {},
      dataType: 'json',
      async : false,
      beforeSend: function() {
        show_loader();
      },
      success: function(response)
      { 
        if(response.status){
          item_list = response.data;
        }
        else{
          alert_notification(response.message);
        }
      },
      complete: function() {
        stop_loader();
      },
      error: function (jqXHR, exception) {
          var error_= '';
          if (jqXHR.status === 0) {
              error = 'Not connect.\n Verify Network.';
          } else if (jqXHR.status == 404) {
              error = 'Requested page not found. [404]';
          } else if (jqXHR.status == 500) {
              error = 'Internal Server Error [500].';
          } else if (exception === 'parsererror') {
              error = 'Requested JSON parse failed.';
          } else if (exception === 'timeout') {
              error = 'Time out error.';
          } else if (exception === 'abort') {
              error = 'Ajax request aborted.';
          } else {
              error = 'Uncaught Error.\n' + jqXHR.responseText;
          }
          stop_loader();
          alert_notification(error);
      },
    });

    return item_list;
  }

  var unit_list = {};
  function get_unit_list()
  {
    if(!isEmptyObject(unit_list)){
      return unit_list;
    }
   
    $.ajax({
      url: '<?= base_url() ?>/admin/import_export/get_unit_list',
      method: 'GET',
      data: {},
      dataType: 'json',
      async : false,
      beforeSend: function() {
        show_loader();
      },
      success: function(response)
      { 
        if(response.status){
          unit_list = response.data;
        }
        else{
          alert_notification(response.message);
        }
      },
      complete: function() {
        stop_loader();
      },
      error: function (jqXHR, exception) {
          var error_= '';
          if (jqXHR.status === 0) {
              error = 'Not connect.\n Verify Network.';
          } else if (jqXHR.status == 404) {
              error = 'Requested page not found. [404]';
          } else if (jqXHR.status == 500) {
              error = 'Internal Server Error [500].';
          } else if (exception === 'parsererror') {
              error = 'Requested JSON parse failed.';
          } else if (exception === 'timeout') {
              error = 'Time out error.';
          } else if (exception === 'abort') {
              error = 'Ajax request aborted.';
          } else {
              error = 'Uncaught Error.\n' + jqXHR.responseText;
          }
          stop_loader();
          alert_notification(error);
      },
    });

    return unit_list;
  }

  function sale_item_valid(i,li,final)
  {
	//  console.log(final);
	 var item_taxsummary_json = {};  
    var error_list = [];
    var brief_list = [];

    var acc_bsd_list = get_acc_bsd_list();
    var item_list = get_item_list();

    var tax_list = get_tax_list();
    tax_list = structuredClone(tax_list);
    
    var tax_accounts = [];
    var dr_acc_err = 0;
    var dr_itm_err = 0;
    
    final.forEach(function(row,indx){
      if(indx != i && indx != li){ // skip first n last
		var supply_type_id = row.imptxnvch_supplytype_id;
		// No tax will applied on the following  supply types 3,5,14,15,16
		const validValues = [3, 5, 14, 15, 16];
		
        var uqc = row.imptxnvch_uqc ?? '';
        if(uqc == '') // Account or Bill Sundry
        {
          var acc_bds = row.imptxnvch_acc_bds.toLowerCase().trim();
          if(acc_bds != '')
          {
            brief = [];
            var pos = parseInt(final[i]['imptxnvch_posid']);

            var index = acc_bsd_list.findIndex(function(obj) {
              return obj.label.toLowerCase().trim() == acc_bds;
            });

            if(index > -1){

              var obj = acc_bsd_list[index];
			 
              var drcr = 'c';

              if(parseAmount(row.imptxnvch_amt_cr) >= 0){
                drcr = 'c';
              }
              if(parseAmount(row.imptxnvch_amt_dr) > 0){
                drcr = 'd';
              }
              // tax if credit side
              
              if(drcr == 'c')
              {
                if(obj.is_acc == 1 && obj.cannotselected == 1){
                  brief.push('Invalid Credit Account');
                  error_list.push(`Invalid Credit Account "${obj.label}"`);
                }

                var amount = parseAmount(row.imptxnvch_amt_cr);

                var tax_status = false;
                var igst_rate = 0;
                var cess_rate = 0;
                var cess_basis = 0;

                if(obj.is_acc == 1 && !validValues.includes(Number(supply_type_id))){
                  tax_status = true;
                  igst_rate = parseAmount(obj.igst_rate);
                  cess_rate = parseAmount(obj.cess_rate);
                  cess_basis = parseInt(obj.cess_basis);
                }

                if(obj.is_sundry==1 && obj.is_tax_account==0 && !validValues.includes(Number(supply_type_id.trim()))){
                  tax_status = true;
                  igst_rate = parseAmount(obj.bl_tx_igst_rte);
                  cess_rate = parseAmount(obj.b_tx_cess_rte);
                  cess_basis = parseInt(obj.cess_basis);
                }

                if(obj.is_sundry==1 && obj.is_tax_account==1 ){
                  tax_accounts.push({
                    indx: indx,
                    id: obj.id,
                    name: obj.label,
                    amt: amount,
                  });
                }
		//console.log(">><<<>>"+tax_status);

                if(tax_status){
                  if(pos != BO_STATE_CODE)// 2 taxes IGST n CESS
                  {   
                    var igst = (amount * igst_rate)/100;
                    igst = parseAmount(igst);

                    tax_list['IGST']['status'] = 1;
                    tax_list['IGST']['value'] += igst; 
                    brief.push(`IGST (${igst_rate}%): `+formatAmount(igst));

                    var cess = 0
                    if(cess_basis == 1){
                      cess = (amount * cess_rate)/100;
                      cess = parseAmount(cess);

                      tax_list['CESS']['status'] = 1;
                      tax_list['CESS']['value'] += cess;
                      brief.push(`CESS (${cess_rate}%): `+formatAmount(cess));
                    }
                    else{ //error 
                      var err = 'TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
                      tax_list['CESS']['status'] = 2;
                      tax_list['CESS']['error'] = err;
                      brief.push(`CESS (${cess_rate}%): `+err);
                    }
                  }
                  else{// 3 taxes CESS,CGST, (SGST or UT-TAX)
                    var gst_rate = parseAmount(igst_rate/2);
                    var cgst = (amount * gst_rate) / 100;
                    cgst = parseAmount(cgst);

                    tax_list['CGST']['status'] = 1;
                    tax_list['CGST']['value'] += cgst;
                    brief.push(`CGST (${gst_rate}%): `+formatAmount(cgst));

                    var ut_arr = [35,4,26,25,31,38,34,97];
                    if(pos != 35 && pos != 4 && pos != 26 && pos != 25 && pos != 31 && pos != 38 && pos != 34 && pos != 97){
                      
                      var sgst = cgst;
                      tax_list['SGST']['status'] = 1;
                      tax_list['SGST']['value'] += sgst;
                      brief.push(`SGST (${gst_rate}%): `+formatAmount(sgst));
                    }
                    else{
                      var ut_tax = cgst;
                      tax_list['UT-TAX']['status'] = 1;
                      tax_list['UT-TAX']['value'] += ut_tax;
                      brief.push(`UT-TAX (${gst_rate}%): `+formatAmount(ut_tax));
                    }

                    var cess = 0
                    if(cess_basis == 1){
                      cess = (amount * cess_rate)/100;
                      cess = parseAmount(cess);

                      tax_list['CESS']['status'] = 1;
                      tax_list['CESS']['value'] += cess;
                      brief.push(`CESS (${cess_rate}%): `+formatAmount(cess));
                    }
                    else{
                      var err = 'TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
                      tax_list['CESS']['status'] = 2;
                      tax_list['CESS']['error'] = err;
                      brief.push(`CESS (${cess_rate}%): `+err);
                    } 
                  }
                }
                else{
                  brief.push('No Tax');
                }
              }
              if(drcr == 'd')
              {
                if(obj.is_acc == 1){
                  dr_acc_err++;
                  brief.push('Account '+acc_bds+' must be on credit side');
                }
                else
                  brief.push('No Tax');
              }
            }
            else{
              brief.push('Invalid Account Name');
            }
          }
          else{
            brief.push('Account/Item Name Required');
            error_list.push(`Invalid Account "${acc_bds}" `);
          }
        }
        else // Item
        {
			//console.table(row);
          var item = row.imptxnvch_acc_bds.toLowerCase().trim();
		  var supply_type_id = row.imptxnvch_supplytype_id;
          if(item != '')
          {
            brief = [];
            var pos = parseInt(final[i]['imptxnvch_posid']);

            var index = item_list.findIndex(function(obj) {
              return obj.label.toLowerCase().trim() == item;
            });

            if(index > -1){

              var obj = item_list[index];
			 // console.log(obj);
              var drcr = 'c';

              if(parseAmount(row.imptxnvch_amt_cr) >= 0){
                drcr = 'c';
              }
              if(parseAmount(row.imptxnvch_amt_dr) > 0){
                drcr = 'd';
              }

              // tax if credit side
              if(drcr == 'c')
              {
				   var amount = parseAmount(row.imptxnvch_amt_cr);
				   
                 

                  var igst_rate = parseAmount(obj.igst_rate);
                  var cess_rate = parseAmount(obj.cess_rate);
                  var cess_basis = parseInt(obj.cess_basis);
                  var item_mrp = parseAmount(obj.item_mrp);
				  if(obj.tax_exempted == 'n' && !validValues.includes(Number(supply_type_id))){
					

                  //calculate IGST
                  if(BO_GSTIN_TYPE == 1){
                    igst_rate = igst_rate // by tax cat id
                  }
                  else if(BO_GSTIN_TYPE == 2){

                    var supply_type = parseInt(obj.supply_type);
                    if(supply_type == 1 || supply_type == 3){
                      igst_rate = GOODS_RATE;
                    }
                    else if(supply_type == 2){
                      igst_rate = SERVICES_RATE;
                    }
                    else{
                      brief.push('Invalid Supply Type');
                      error_list.push(item+' Invalid Supply Type');
                    }
                  }
                  else{
                    brief.push('Invalid GSTIN Type');
                    error_list.push('Invalid GSTIN Type');
                  }

                  
					var total_tax_value=0;
					var gst_rate_string=`${igst_rate}%`;
                  if(pos != BO_STATE_CODE)// 2 taxes IGST n CESS
                  {
                    var igst = (amount * igst_rate)/100;
                    igst = parseAmount(igst);
					total_tax_value +=igst;
                    tax_list['IGST']['status'] = 1;
                    tax_list['IGST']['value'] += igst; 
                    brief.push(`IGST (${igst_rate}%): `+formatAmount(igst));
					
                  }
                  else
                  {
                    var gst_rate = parseAmount(igst_rate/2);
                    var cgst = (amount * gst_rate) / 100;
                    cgst = parseAmount(cgst);

                    tax_list['CGST']['status'] = 1;
                    tax_list['CGST']['value'] += cgst;
                    brief.push(`CGST (${gst_rate}%): `+formatAmount(cgst));

                    var ut_arr = [35,4,26,25,31,38,34,97];
                    if(pos != 35 && pos != 4 && pos != 26 && pos != 25 && pos != 31 && pos != 38 && pos != 34 && pos != 97){
                      total_tax_value +=cgst;
                      var sgst = cgst;
                      tax_list['SGST']['status'] = 1;
                      tax_list['SGST']['value'] += sgst;
                      brief.push(`SGST (${gst_rate}%): `+formatAmount(sgst));
                    }
                    else{
                      var ut_tax = cgst;
					  total_tax_value +=ut_tax;
                      tax_list['UT-TAX']['status'] = 1;
                      tax_list['UT-TAX']['value'] += ut_tax;
                      brief.push(`UT-TAX (${gst_rate}%): `+formatAmount(ut_tax));
                    }
                  }

                  //calculate CESS
                  var cess = 0;
                  if(cess_basis == 1){
                    cess = (amount * cess_rate)/100;
                    cess = parseAmount(cess);
					total_tax_value +=cess;
                    tax_list['CESS']['status'] = 1;
                    tax_list['CESS']['value'] += cess;
                    brief.push(`CESS (AMT-${cess_rate}%): `+formatAmount(cess));
                  }
                  else{
                    cess = (item_mrp * cess_rate)/100;
                    cess = parseAmount(cess);

                    tax_list['CESS']['status'] = 1;
                    tax_list['CESS']['value'] += cess;
                    brief.push(`CESS (MRP-${cess_rate}%): `+formatAmount(cess));
                  }
				  
				if(item_taxsummary_json[obj.tax_cat_id]){
                    if(item_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac]){						
						item_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac].push({'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'item_name':obj.item_name,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':amount,'tax_rate':igst,'tax_rate_string':gst_rate_string,'cess_basis':obj.cess_basis,'cess_rate':obj.cess_rate,'igst':igst,'cess':cess,'cgst':cgst,'sgst':sgst,'total_tax':parseAmount(total_tax_value)});						
					  }
					  else{
					   item_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac]=[{'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'item_name':obj.item_name,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':amount,'tax_rate':igst,'tax_rate_string':gst_rate_string,'cess_basis':obj.cess_basis,'cess_rate':obj.cess_rate,'igst':igst,'cess':cess,'cgst':cgst,'sgst':sgst,'total_tax':parseAmount(total_tax_value)}];                          
					  }
				}else{
				    item_taxsummary_json[obj.tax_cat_id]={
                                [obj.item_hsn_sac] : [{'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'item_name':obj.item_name,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':amount,'tax_rate':igst,'tax_rate_string':gst_rate_string,'cess_rate':obj.cess_rate,'cess_basis':obj.cess_basis,'igst':igst,'cess':cess,'cgst':cgst,'sgst':sgst,'total_tax':parseAmount(total_tax_value)}]
                     };		
				}
				
                }
                else{
                  brief.push('No Tax');
				  if(item_taxsummary_json[obj.tax_cat_id]){
                    if(item_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac]){						
						item_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac].push({'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'item_name':obj.item_name,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':amount,'tax_rate':0,'tax_rate_string':'NILL RATED','cess_basis':1,'cess_rate':0,'igst':0,'cess':0,'cgst':0,'sgst':0,'total_tax':0});						
					  }
					  else{
					   item_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac]=[{'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'item_name':obj.item_name,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':amount,'tax_rate':0,'tax_rate_string':'NILL RATED','cess_basis':1,'cess_rate':0,'igst':0,'cess':0,'cgst':0,'sgst':0,'total_tax':0}];                          
					  }
				}else{
				    item_taxsummary_json[obj.tax_cat_id]={
                                [obj.item_hsn_sac] : [{'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'item_name':obj.item_name,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':amount,'tax_rate':0,'tax_rate_string':'NILL RATED','cess_rate':0,'cess_basis':1,'igst':0,'cess':0,'cgst':0,'sgst':0,'total_tax':0}]
                     };		
				}
                }
              }
              if(drcr == 'd')
              {
                dr_itm_err++;
                brief.push('Item `'+item+'` must be on credit side');
              }
            }
            else{
              brief.push('Invalid Item Name');
              error_list.push(`Invalid Item "${item}" `);
            }
          }
          else{
            brief.push('Item Name Required');
          }
        }

        brief_list.push({indx: indx, brief: brief});
      }
    });
  
    if(dr_acc_err > 0){
      error_list.push('Only one party account is allowed');
    }
    if(dr_itm_err > 0){
      error_list.push('Item are allowed only on credit side');
    }
  
    var cess_err = '';
    var tax_summary=`<span class="text-warning">Tax Summary<span>`;
    tax_summary += `<ul class="text-warning">`;

    $.each(tax_list, function(indx, tax){
      
      if(tax.status == 1){
        tax_summary += '<li>' + indx + ' ' + formatAmount(tax.value) +' CR</li>';

        if(tax.id > 0){
          var index = tax_accounts.findIndex(function(obj) {
             return obj.id == tax.id;
          });

          if(index > -1){
            var obj = tax_accounts[index];
            if(parseAmount(obj.amt) < parseAmount(tax.value) || parseAmount(obj.amt) > parseAmount(tax.value))
            {
              error_list.push(tax.name + ' should be  equal to '+ formatAmount(tax.value) +' CR');
            }
            brief_list.push({indx: obj.indx, brief: ['MIN '+ formatAmount(tax.value) +' CR']});
          }
          else{
            error_list.push(tax.name + ' is required<br> MIN '+ formatAmount(tax.value) +' CR');
          }
        }
        else{
          error_list.push(indx +' Master is not created<br> MIN '+ formatAmount(tax.value) +' CR');
        }
      }
      if(tax.status == 2){
        cess_err = indx + '' + tax.error;
        error_list.push(indx + '' + tax.error);
      }
    });

    if(cess_err != '')
      tax_summary += '<li>'+cess_err+'</li>';

    tax_summary += '</ul>';
	
	var taxsummary_json = [];		
		$.each(item_taxsummary_json, function(index,obj){
			var tax_hsn_sac='';
			var tax_rate ='';
			var tax_rate_string='';
			var cess_rate ='';
			var cgst ='';
			var sgst ='';
			var igst ='';	
            var cess ='';			
			var tax_amt='';
			var cess_basis  = '';
			var tax_cat_id ='';
			var tax_item_id = '';var tax_item_name = '';
			var total_tax   = 0;
			$.each(obj, function(index1,obj1){//categ
				var sum_tax_amt=0;
				var sum_igst=0;
				var sum_sgst=0;
				var sum_cgst=0;
				var sum_tax_rate=0;
				var sum_tax_string=0;
				var sum_cess=0;
				var sum_tax=0;
				
				$.each(obj1, function(index2,obj2){ //hsn
				tax_item_id = obj2.tax_item_id;
				tax_item_name = obj2.item_name;
				tax_amt  = obj2.tax_amt;				
				igst     = obj2.igst;
				sgst     = obj2.sgst;
				cgst     = obj2.cgst;
				tax_rate = obj2.tax_rate;
				tax_rate_string = obj2.tax_rate_string;
				cess_rate = obj2.cess_rate;
				total_tax = obj2.total_tax;
				cess      = obj2.cess;
				cess_basis = obj2.cess_basis;
				tax_cat_id = obj2.tax_cat_id,
				
				sum_tax_amt   += parseFloat(tax_amt);
				sum_igst      += parseFloat(igst);
				sum_sgst      += parseFloat(sgst);
				sum_cgst      += parseFloat(cgst);
				sum_tax_rate  += parseFloat(tax_rate);				
				tax_hsn_sac   = obj2.tax_hsn_sac;
				sum_cess      += parseFloat(cess);
				sum_tax       += parseFloat(total_tax);
				})
						
				taxsummary_json.push({'tax_cat_id':index,'tax_item_id':tax_item_id,'tax_item_name':tax_item_name,"tax_hsn_sac":tax_hsn_sac,"tax_amt":sum_tax_amt,"cess_rate":cess_rate,"cess_basis":cess_basis,"tax_rate":tax_rate,"tax_rate_string":tax_rate_string,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
				});
			
		});
	
var tax_summary_table='';var taxdetails_summary_table='';
var tt_total_tax=0;
tax_summary_table +='<table width="100%" class="table table-responsive"><thead><tr><th>HSN/SAC</th><th>TAX RATE</th><th>TAXABLE VALUE</th><th>IGST</th><th>CGST</th><th>SGST/UGST</th><th>CESS</th><th>TOTAL TAX</th></tr></thead><tbody>';
taxdetails_summary_table +='<table width="100%" class="table table-responsive"><thead><tr><th>PARTICULARS</th><th>TAX RATE</th><th>TAXABLE VALUE</th><th>IGST</th><th>CGST</th><th>SGST/UGST</th><th>CESS</th><th>TOTAL TAX</th></tr></thead><tbody>';
$.each(taxsummary_json, function(index,obj){
	tt_total_tax += parseAmount(obj.total_tax);
	tax_summary_table +='<tr><td>'+obj.tax_hsn_sac+'</td><td>'+obj.tax_rate_string+'</td><td>'+formatAmount(obj.tax_amt)+'</td><td>'+formatAmount(obj.igst)+'</td><td>'+formatAmount(obj.cgst)+'</td><td>'+formatAmount(obj.sgst)+'</td><td>'+formatAmount(obj.cess)+'</td><td>'+formatAmount(obj.total_tax)+'</td></tr>';
	taxdetails_summary_table +='<tr><td>'+obj.tax_item_name+'</td><td>'+obj.tax_rate_string+'</td><td>'+formatAmount(obj.tax_amt)+'</td><td>'+formatAmount(obj.igst)+'</td><td>'+formatAmount(obj.cgst)+'</td><td>'+formatAmount(obj.sgst)+'</td><td>'+formatAmount(obj.cess)+'</td><td>'+formatAmount(obj.total_tax)+'</td></tr>';

});
	tax_summary_table +='<tr><td colspan="7"></td><td><strong>'+formatAmount(tt_total_tax)+'</strong></td></tr>';
taxdetails_summary_table +='<tr><td colspan="7"></td><td><strong>'+formatAmount(tt_total_tax)+'</strong></td></tr>';

tax_summary_table +='</tbody></table>';
taxdetails_summary_table +='</tbody></table>';

$("#grid_taxsummary_tab").html(tax_summary_table);
$("#grid_taxsetails_tab").html(taxdetails_summary_table);
    return {
      error_list: error_list,
      brief_list: brief_list,
      tax_summary: tax_summary_table,
    }
  }

  function isEmptyObject(obj) {
    for (const prop in obj) {
      if (Object.hasOwn(obj, prop)) {
        return false;
      }
    }
    return true;
  }

  $(document).on('change', 'select[name="refresh_masters"]', function(){
    var master = $(this).val();
    if(master != ''){
      $(this).val('');
    }

    if(master == 'itm'){
      item_list = {};
      get_item_list()
    }

    if(master == 'unt'){
      unit_list = {};
      get_unit_list();
    }

    if(master == 'acc'){
      acc_bsd_list = {};
      get_acc_bsd_list();

      tax_list = {};
      get_tax_list();
    }


  });
</script>
 </body>
</html>
