<?php $header = array( 	'title' => 'Bulk Updation Data' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$bo_array = array();
if(count($branch_dropdown) >1){ 
$bo_array = array();
 foreach($branch_dropdown as $brow){
   $bo_array[$brow['bo_id']] = $brow['bo_name']; 
 }
}
?>  
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
	.ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }

.pq-grid-cell.pq-side-icon > div:before {
    width: 20px;
    height: 20px;
    color: #ccc;
    float: right;
}

.pq-grid-cell.pq-drop-icon > div:before {
content: "▼";
}

.pq-grid-cell.pq-calendar > div:before {
content: "\01F4C5";
}
.pq-select-search-input {
padding: 1px 2px;
border-width: 0;
height:23px;
}
.pq-select-search-input {
box-sizing: border-box;
width: 100%;
font-size: inherit;
}

.ui-datepicker-calendar tr, .ui-datepicker-calendar td, .ui-datepicker-calendar td a, .ui-datepicker-calendar th{
font-size:inherit;
}
div.ui-datepicker{
font-size:13px;
width:inherit;
height:inherit;
}
.ui-datepicker-title span{
font-size:13px;
}
.pq-sb-horiz-t .pq-sb-slider, .pq-sb-vert-t .pq-sb-slider, .pq-sb-horiz-t .pq-sb-btn, .pq-sb-vert-t .pq-sb-btn{background-color: rgb(220, 254, 211) !important;}

</style>
        
<h3 class="pb-3">Bulk Updation</h3>

<div class="col-12">

 <p class="acesstabs" id="myTab" role="tablist" style="pointer-events: none;">

  <a class="active" id="acess1-tab"  data-bs-toggle="tab" data-bs-target="#acess1-panel" type="button" role="tab" aria-controls="acess1-panel" aria-selected="true">1</a>
  <a class="" id="acess2-tab" data-bs-toggle="tab" data-bs-target="#acess2-panel" type="button" role="tab" aria-controls="acess2-panel" aria-selected="false" tabindex="-1">2</a>
  <a class="" id="acess3-tab" data-bs-toggle="tab" data-bs-target="#acess3-panel" type="button" role="tab" aria-controls="acess3-panel" aria-selected="false" tabindex="-1">3</a>
</p>

<div class="tab-content border-0 accordion form-outline mb-4" id="myTabContent">


 <!-- Access Tab 1 Starts here ---->

<div class="tab-pane border-0 fade accordion-item active show" id="acess1-panel" role="tabpanel" aria-labelledby="acess1-tab" tabindex="0">
  <div class="card p-4 mt-2">
      <h4>Add Module</h4>

      <form id="create_mst_form" action="<?= base_url() ?>admin/bulk_updation/createMaster" method="post">

        <div class="col-md-12 myform pt-4">

          <div id="crt_mst_val_err"></div>

          <div class="row">
          <p class="col-12 col-md-4">
            <label>Module</label>
            <select name="module" id="module" class="form-select w-75 required">
              <option> </option>
              <!--<option>Account</option> 
              <option>Inventory</option>
			  <option>Bill Sundry</option>-->
			  <option>Account</option> 
			  <option>Vouchers</option>
            </select> 
          </p>

          <p class="col-12 col-md-4">
            <label>Master</label>
            <select name="master" id="master" class="form-select w-75 required">
              <option value=""></option>
            </select> 
          </p>

          <p class="col-12 col-md-4">
            <label>Sub Master</label>
            <select name="sub_master" id="sub_master" class="form-select w-75 required">
              <option value=""></option>
              
            </select> 
          </p>
          </div>

          <p class="col-12 text-end">
            <button type="submit" class="btn btn-success">Submit</button>
          </p>

        </div>
      </form>

  </div>
</div>

<!-- Access Tab 1 Ends here ---> 




<!-- Access 2 Tab -->
<div class="tab-pane border-0 fade accordion-item" id="acess2-panel" role="tabpanel" aria-labelledby="acess2-tab" tabindex="0">
    
  <div class="card p-4 mt-2">
    <h4>Bulk Updation for 
      <span id="upld_mst_sub_title"></span>
    </h4> <div class="col-md-12 myform pt-4 mx-auto">
	<form id="bulkfrm" action="#" method="post">
		 <input type="hidden" name="taxcatgdata" id="taxcatgdata">
		  <input type="hidden" name="ctabid" id="ctabid">
      <div class="mt-5" id="items_grid"  style="margin:auto;"></div>
	  <div class="mt-5" id="accounts_grid"  style="margin:auto;"></div>	 
      <div class="mt-5" id="bsd_grid"  style="margin:auto;"></div>	 
	  
	  </form>
    </div>
	<div class="row">

    <div class="col-md-12">
      <span id="edt_desc" class="ms-2">Displaying 1 to 100 of 1000 records</span>
       </div>
       
   </div>
   
   <div class="col-md-12 pt-4">
    <p class="d-flex justify-content-between align-items-center">
        <a href="javascript:void(0);" onclick="window.location.reload();return false;" class="btn btn-success m-2">« Back</a>        
        <a href="javascript:void(0);" id="updategrid_changes" class="btn btn-success m-2">Update</a>        
        <span>
          <a href="javascript:void(0);" id="prevrecords" class="btn btn-success m-2">Prev</a>
          <a href="javascript:void(0);" id="nextrecords" class="btn btn-success m-2">Next</a>
        </span>
    </p>
</div>

<div class="col-md-12 pt-4">
	<em><strong>Note:</strong> all updates will be made page wise. Pls click on update button in case of any changes to current page view.</em>
   </div>
   
  </div>
  

</div>
<!-- Access Tab 2 Ends here --->




<!-- Access Tab 5 Starts here ------>
<div class="tab-pane border-0 fade accordion-item" id="acess3-panel" role="tabpanel" aria-labelledby="acess3-tab" tabindex="0">

 <div class="card p-4 mt-2">
   <div class="m-5 p-5 text-center m-auto">

      <div class="row">
        <div class="col-md-12 text-center">
          <img style="width: 250px; height: auto" src="<?= base_url() ?>public/assets/img/green_tick.png" alt="No image">
        </div>
        
      </div>
     <h3>Bulk Updation Successfull</h3>

     <br>
     
     <a href="javascript:void(0)" onclick="window.location.reload();return false;" class="btn btn-success btn-lg">Add another Module</a>
   </div>
 </div>
 
 
</div>
<!-- Access Tab 5 Ends here --------->



<!-- Manage Access Modal Ends here --->  



</div>
</div>
  
<?php echo view('includes/footer_scripts'); ?>
<script>
var taxcatglist = <?php echo json_encode($tax_catgry_source);?>;
 var salesaccountslist = <?php echo json_encode($sales_acc_source);?>;
 var purchaseaccountslist = <?php echo json_encode($purchase_acc_source);?>; 
 var all_sales_accounts  = <?php echo json_encode($all_sales_accounts);?>;
  var all_purchase_accounts  = <?php echo json_encode($all_purchase_accounts);?>;
var supplytypes = [{"label":"Goods","id":"1"},{"label":"Services","id":"2"},{"label":"Capital Goods","id":"3"}];
var itemvaluationtags =[{"label":'AVG',"id":'1',"value":'AVG'},{"label":'FIFO',"id":'2',"value":'FIFO'},{"label":'LIFO',"id":'3',"value":'LIFO'}];
function pagination_records(nxtpage,buttonid,button_action){
	var ctabid = $("#ctabid").val();
	
	if(ctabid=="item_valuation_method" || ctabid=="item" || ctabid=="item_salepurchase_acc" || ctabid=="item_upc_printname_alias"){
			 $.ajax({
				  type: "POST",
				  url: '<?php echo base_url();?>admin/bulk_updation/ajax_items',
				  method: 'POST',
				  data: {"pq_curpage": nxtpage},
				  dataType: 'json',
				  beforeSend: function() {
					$("#grid_search").pqGrid('showLoading');
				  },
				  success: function(dataJSON)
				   { 
				    $("#items_grid").pqGrid('option','dataModel.data',dataJSON.data);
				     stop_loader();
					 
					 
					if(button_action=='prev' && (parseInt(dataJSON.curPage)==1 || parseInt(dataJSON.curPage)==0 )){
						$("#prevrecords").hide();	
						$("#nextrecords").show();						
						$("#nextrecords").attr("data-ajax",dataJSON.totalPages);
						$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);								
					 }	
					 else if(button_action=='prev' && (parseInt(dataJSON.curPage)< parseInt(dataJSON.totalPages))){
					   $("#nextrecords").attr("data-ajax",dataJSON.totalPages);
						$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);
						$("#nextrecords").show();
					 }						
					   else
						    $("#prevrecords").show();
							if(dataJSON.curPage==dataJSON.totalPages){								
								$("#"+buttonid).attr("data-ajax",0);
								$("#"+buttonid).attr("data-role",0);
								$("#nextrecords").hide();																
								$("#prevrecords").attr("data-ajax",dataJSON.totalPages);
								$("#prevrecords").attr("data-role",parseInt(dataJSON.curPage)-1);								
							}
							else{								
								if(button_action=='next'){
								 $("#"+buttonid).html("Next »");
								}
								$("#"+buttonid).attr("data-ajax",dataJSON.totalPages);
								$("#"+buttonid).attr("data-role",parseInt(dataJSON.curPage)+1);
								if(parseInt(dataJSON.curPage)==1 || parseInt(dataJSON.curPage)==0){
									$("#prevrecords").hide();	
								}
								else
								 $("#prevrecords").show();								
							
								$("#prevrecords").attr("data-ajax",dataJSON.totalPages);
								$("#prevrecords").attr("data-role",parseInt(dataJSON.curPage)-1);
								
							}
							
							  var min = parseInt(dataJSON.offset) + 1;
							  var max = parseInt(dataJSON.offset) + parseInt(dataJSON.limit);
							  if(max > dataJSON.totalRecords){
								max = dataJSON.totalRecords;
							  }
		  
							if(dataJSON.curPage>1)
							  var nextpage =parseInt(dataJSON.renderRecods)+parseInt(1);
							else
							var nextpage =	parseInt(dataJSON.curPage);
						
							$("#edt_desc").html("Displaying "+min+" to "+max+" of "+dataJSON.totalRecords+" records");
					
					
					 $("#items_grid").pqGrid('refreshDataAndView');
					
					}
			    });	
			}
		   else if(ctabid=="account"){
			$.ajax({
				  type: "POST",
				  url: '<?php echo base_url();?>admin/bulk_updation/ajax_accounts',
				  method: 'POST',
				  data: {"pq_curpage": nxtpage},
				  dataType: 'json',
				  beforeSend: function() {
					$("#grid_search").pqGrid('showLoading');
				  },
				  success: function(dataJSON)
				   { 
				    $("#accounts_grid").pqGrid('option','dataModel.data',dataJSON.data);
				     stop_loader();
					 if(button_action=='prev' && (parseInt(dataJSON.curPage)==1 || parseInt(dataJSON.curPage)==0 )){
						$("#prevrecords").hide();	
						$("#nextrecords").show();						
						$("#nextrecords").attr("data-ajax",dataJSON.totalPages);
						$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);								
					 }	
					 else if(button_action=='prev' && (parseInt(dataJSON.curPage)< parseInt(dataJSON.totalPages))){
					   $("#nextrecords").attr("data-ajax",dataJSON.totalPages);
						$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);
						$("#nextrecords").show();
					 }						
					   else
						    $("#prevrecords").show();
							if(dataJSON.curPage==dataJSON.totalPages){								
								$("#"+buttonid).attr("data-ajax",0);
								$("#"+buttonid).attr("data-role",0);
								$("#nextrecords").hide();																
								$("#prevrecords").attr("data-ajax",dataJSON.totalPages);
								$("#prevrecords").attr("data-role",parseInt(dataJSON.curPage)-1);								
							}
							else{								
								if(button_action=='next'){
								 $("#"+buttonid).html("Next »");
								}
								$("#"+buttonid).attr("data-ajax",dataJSON.totalPages);
								$("#"+buttonid).attr("data-role",parseInt(dataJSON.curPage)+1);
								if(parseInt(dataJSON.curPage)==1 || parseInt(dataJSON.curPage)==0){
									$("#prevrecords").hide();	
								}
								else
								 $("#prevrecords").show();								
							
								$("#prevrecords").attr("data-ajax",dataJSON.totalPages);
								$("#prevrecords").attr("data-role",parseInt(dataJSON.curPage)-1);
								
							}
							
							  var min = parseInt(dataJSON.offset) + 1;
							  var max = parseInt(dataJSON.offset) + parseInt(dataJSON.limit);
							  if(max > dataJSON.totalRecords){
								max = dataJSON.totalRecords;
							  }
		  
							if(dataJSON.curPage>1)
							  var nextpage =parseInt(dataJSON.renderRecods)+parseInt(1);
							else
							var nextpage =	parseInt(dataJSON.curPage);
						
							$("#edt_desc").html("Displaying "+min+" to "+max+" of "+dataJSON.totalRecords+" records");
					
					
					 $("#accounts_grid").pqGrid('refreshDataAndView');
					
					}
			    });   
			   

		   }
		  else  if(ctabid=="bsd"){
			$.ajax({
				  type: "POST",
				  url: '<?php echo base_url();?>admin/bulk_updation/ajax_bsd',
				  method: 'POST',
				  data: {"pq_curpage": nxtpage},
				  dataType: 'json',
				  beforeSend: function() {
					
				  },
				  success: function(dataJSON)
				   { 
				    $("#bsd_grid").pqGrid('option','dataModel.data',dataJSON.data);
				     stop_loader();
					 if(button_action=='prev' && (parseInt(dataJSON.curPage)==1 || parseInt(dataJSON.curPage)==0 )){
						$("#prevrecords").hide();	
						$("#nextrecords").show();						
						$("#nextrecords").attr("data-ajax",dataJSON.totalPages);
						$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);								
					 }	
					 else if(button_action=='prev' && (parseInt(dataJSON.curPage)< parseInt(dataJSON.totalPages))){
					   $("#nextrecords").attr("data-ajax",dataJSON.totalPages);
						$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);
						$("#nextrecords").show();
					 }						
					   else
						    $("#prevrecords").show();
							if(dataJSON.curPage==dataJSON.totalPages){								
								$("#"+buttonid).attr("data-ajax",0);
								$("#"+buttonid).attr("data-role",0);
								$("#nextrecords").hide();																
								$("#prevrecords").attr("data-ajax",dataJSON.totalPages);
								$("#prevrecords").attr("data-role",parseInt(dataJSON.curPage)-1);								
							}
							else{								
								if(button_action=='next'){
								 $("#"+buttonid).html("Next »");
								}
								$("#"+buttonid).attr("data-ajax",dataJSON.totalPages);
								$("#"+buttonid).attr("data-role",parseInt(dataJSON.curPage)+1);
								if(parseInt(dataJSON.curPage)==1 || parseInt(dataJSON.curPage)==0){
									$("#prevrecords").hide();	
								}
								else
								 $("#prevrecords").show();								
							
								$("#prevrecords").attr("data-ajax",dataJSON.totalPages);
								$("#prevrecords").attr("data-role",parseInt(dataJSON.curPage)-1);
								
							}
							
							  var min = parseInt(dataJSON.offset) + 1;
							  var max = parseInt(dataJSON.offset) + parseInt(dataJSON.limit);
							  if(max > dataJSON.totalRecords){
								max = dataJSON.totalRecords;
							  }
		  
							if(dataJSON.curPage>1)
							  var nextpage =parseInt(dataJSON.renderRecods)+parseInt(1);
							else
							var nextpage =	parseInt(dataJSON.curPage);
						
							$("#edt_desc").html("Displaying "+min+" to "+max+" of "+dataJSON.totalRecords+" records");
					
					
					 $("#bsd_grid").pqGrid('refreshDataAndView');
					
					}
			    });		
	     	}
	
}
function commoncode(){
	
	   var ctabid = $("#ctabid").val();
	   
		if(ctabid=="item"){
		   var pq_grids = $("#items_grid");
        var data = pq_grids.pqGrid('option', 'dataModel.data'); 
        var taxcatg_data = [];
        for (var j = 0; j < data.length; j++) {
              var tax_cat_id  = data[j]['tax_cat_id'];
              var item_id     = data[j]['item_id'];			   
              if(tax_cat_id!='' && item_id!=''){
                taxcatg_data.push({
                        "tax_cat_id": tax_cat_id,
                        "item_id": item_id 
                        						
                   });
                
               }   
           }
	   }
	   else if(ctabid=="item_salepurchase_acc"){
		   var pq_grids = $("#items_grid");
        var data = pq_grids.pqGrid('option', 'dataModel.data'); 
		console.table(data);
        var taxcatg_data = [];
        for (var j = 0; j < data.length; j++) {
              var sales_acc_id  = data[j]['sales_acc_id'];
			  var purchase_acc_id  = data[j]['purchase_acc_id'];
              var item_id     = data[j]['item_id'];			   
              if(item_id!=''){
                taxcatg_data.push({
                        "sale_acc_id": sales_acc_id,
						"purchase_acc_id": purchase_acc_id,
						"item_id": item_id 
                        						
                   });
                
               }  
			  
           }
	   }
	   else if(ctabid=="item_upc_printname_alias"){
		   var pq_grids = $("#items_grid");
        var data = pq_grids.pqGrid('option', 'dataModel.data'); 
        var taxcatg_data = [];
        for (var j = 0; j < data.length; j++) {
              var item_upc  = data[j]['item_upc'];
			  var item_printnme  = data[j]['item_printnme'];
			  var item_aliasname  = data[j]['item_aliasname'];
              var item_id     = data[j]['item_id'];			   
              if(item_id!=''){
                taxcatg_data.push({
                        "item_upc": item_upc,
						"item_printnme": item_printnme,
						"item_aliasname": item_aliasname,
                        "item_id": item_id 
                        						
                   });
                
               }   
           }
	   }
	   else if(ctabid=="item_valuation_method"){
		   var pq_grids = $("#items_grid");
        var data = pq_grids.pqGrid('option', 'dataModel.data'); 
        var taxcatg_data = [];
        for (var j = 0; j < data.length; j++) {
              var item_upc  = data[j]['item_upc'];
			  var item_printnme  = data[j]['item_printnme'];
			  var item_aliasname  = data[j]['item_aliasname'];
              var item_id     = data[j]['item_id'];	
			   var item_valuation_method     = data[j]['valuation_id'];	
              if(item_id!=''){
                taxcatg_data.push({
                        "item_upc": item_upc,
						"item_printnme": item_printnme,
						"item_aliasname": item_aliasname,
                        "item_id": item_id, "method_id": item_valuation_method 
                        						
                   });
                
               }   
           }
	   }
	  else if(ctabid=="account"){
		   var pq_grids = $("#accounts_grid");
           var data = pq_grids.pqGrid('option', 'dataModel.data'); 
		   var allcolumns = <?php echo json_encode($bo_array);?>;
          		   
           var taxcatg_data = [];
           for (var j = 0; j < data.length; j++) {
				 var master_opp_bal=0;
              var tax_cat_id  = data[j]['tax_cat_id'];
              var acc_id      = data[j]['acc_id'];
			  var supplytype  = '';//data[j]['supplytype_id'];
			 
			  var branch_balances =[];
			  $.each(allcolumns, function(branchid,value) {				
			  if(typeof data[j]['op_bal_'+branchid] =='undefined'){
				  var op_bal=0;
					master_opp_bal += parseFloat(op_bal);
				}
			  else{
				var op_bal=data[j]['op_bal_'+branchid];
			    if(data[j]['bal_type_'+branchid]=='CR.')
					master_opp_bal -= parseFloat(op_bal);
				if(data[j]['bal_type_'+branchid]=='DR.')
					master_opp_bal += parseFloat(op_bal);
				}
			
			   
			 if(typeof data[j]['bal_type_'+branchid] =='undefined')
				  var bal_type='Dr.';
			  else
				var bal_type=data[j]['bal_type_'+branchid];
				
     		  branch_balances.push({'branchid':branchid,'bal_type':bal_type,'op_bal':op_bal});
			  });
			  data[j]['op_bal']=Math.abs(master_opp_bal);
			  if(parseFloat(master_opp_bal)<0)	
              data[j]['bal_type']='Cr.';
              else
              data[j]['bal_type']='Dr.';
			  var branchid    = $('select[name="branchid"] option:selected').val();
			 if( typeof branchid =="undefined"){
				 var branchid = '<?php echo $bo_id;?>';  
			  }			  
              if(acc_id!=''){
                taxcatg_data.push({
                        "tax_cat_id" : tax_cat_id,
                        "item_id"    : acc_id,
						"supplytype" : supplytype,																
						"branch_balances":branch_balances
                      });
                 }   
              }
				$("#accounts_grid").pqGrid('refreshDataAndView');	
			  
	   }
	 else  if(ctabid=="bsd"){
		  var pq_grids = $("#bsd_grid");
           var data = pq_grids.pqGrid('option', 'dataModel.data');
		   var taxcatg_data = [];
           for (var j = 0; j < data.length; j++) {
              var tax_cat_id  = data[j]['tax_cat_id'];
              var bill_sundry_id     = data[j]['bill_sundry_id'];			 
              if(tax_cat_id!='' && bill_sundry_id!=''){
                taxcatg_data.push({
                        "tax_cat_id": tax_cat_id,
                        "item_id": bill_sundry_id	
                   });
                
                }   
             }
	   }
             
   $("#taxcatgdata").val(JSON.stringify(taxcatg_data));
   
}
 $(document).on("click","#nextrecords",function(){
	 show_loader();
	var tpages = $(this).attr("data-ajax");
	var nxtpage = $(this).attr("data-role");
	commoncode();
	pagination_records(nxtpage,'nextrecords','next');
	 
});

 $(document).on("click","#prevrecords",function(){
	 show_loader();
	var tpages = $(this).attr("data-ajax");
	var nxtpage = $(this).attr("data-role");
	 commoncode();
	 pagination_records(nxtpage,'prevrecords','prev');
	 
});
  
 
 $(document).on("click","#updategrid_changes",function(){
 	var tpages = $(this).attr("data-ajax");
    var nxtpage = $(this).attr("data-role");
	   commoncode();
    var form = $("#bulkfrm");
   $.ajax({
            url: form.attr('action'), 
            type: 'POST',
            data: form.serialize(),   
			dataType: "json",	            
            cache: false,
            beforeSend: function() {
                show_loader();               
            },
            success: function (response) {
				stop_loader(); 				
				 alert_success("Data updated successfully");
			 
				
			}
   });
  
  })
  
  $(document).on('submit', '#bulkfrm', function(e){
	  return false;
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(this);
        $.ajax({
            url: form.attr('action'), 
            type: 'POST',
            data: formData,
            dataType: "json",
            processData: false,
            cache: false,
            contentType: false,
            beforeSend: function() {
                show_loader();
                $('#submitbtn').attr('disabled', 'disabled');
                $('#validation_errors').html('');
            },
            success: function (response) {
               
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
					 stop_loader();
                   const nextTabLinkEl = $('#acess3-tab');
              const nextTab =new bootstrap.Tab(nextTabLinkEl);
              nextTab.show();	
                   
                }
                else{
                    stop_loader();
                    alert_notification(response.message);
                    if(response.errors)
                    {
                        var list = '';
                        $.each(response.errors, function(index, value){
                            list += '<li>'+value+'</li>';
                        });

                        var html = '<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button><ul>'+list+'</ul></div>';
                        $('#validation_errors').html(html);
                        window.scrollTo(0,0);
                    }  
                }
                
            },
            complete: function() {
                stop_loader();
                $('#submitbtn').attr('disabled', false);
            },
            error: function (jqXHR, exception) {
				 stop_loader();
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
  
$(document).ready(function(){
    $('.btnNext').click(function() {
        const nextTabLinkEl = $('.acesstabs .active').closest('a').next('a')[0];
        const nextTab = new bootstrap.Tab(nextTabLinkEl);
        nextTab.show();
    });

    $('.btnPrevious').click(function() {
        const prevTabLinkEl = $('.acesstabs .active').closest('a').prev('a')[0];
        const prevTab = new bootstrap.Tab(prevTabLinkEl);
        prevTab.show();
    });

});


  $(document).on('change','select[name="module"]', function(){
    var master = $(this).val();
    var html = '<option></option>';

    if(master == 'Account'){
      html += '<option>Account Master</option>';
    }
    if(master == 'Inventory'){
      html += '<option>Item</option>';
    }
	if(master == 'Bill Sundry'){
      html +='<option>Bill Sundry Taxable Master</option>';
    }
    if(master == 'Vouchers'){
      html += '<option>Voucher Refresh</option>';
	  //html += '<option>Replace Inventory</option>';
	  //html += '<option>Replace Account</option>';
    }
    $('select[name="master"]').html(html);
    $('select[name="sub_master"]').html('<option></option>');
  });

  $(document).on('change','select[name="master"]', function(){
    var master = $(this).val();
    var html = '<option></option>';

    if(master == 'Account Master'){
      html += '<option>Tax Category</option><option>Op. Balances</option><option>Address</option>';
    }
    if(master == 'Item'){
      html += '<option>Tax Category</option>';
	  html += '<option>Sale/Purchase Mapping</option>';
	  html += '<option>UPC/Print Name/Alias</option>';
	  html += '<option>Valuation Method</option>';
    } 
	if(master == 'Bill Sundry Taxable Master'){
      html +='<option>Tax Category</option>';
    }
	if(master == 'Voucher Refresh'){
      html +='<option>Sales Voucher</option>';
	 // html +='<option>Purchase Voucher</option>';
    }
	if(master == 'Replace Inventory'){
      html +='<option>Item & Unit</option>';
    }
	if(master == 'Replace Account'){
      html +='<option>Account Ledger</option>';
    }
    $('select[name="sub_master"]').html(html);
  });

$(function () {
	
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
			  if(response.call_model=="sales_voucher_resave"){
				   window.location.href="<?php echo base_url();?>admin/bulk_updation/sales_voucher_resave";
				   
			   }
			   if(response.call_model=="account_address"){
				   window.location.href="<?php echo base_url();?>admin/bulk_updation/account_address_update";
				   
			   }
			    if(response.call_model=="account_opbal"){	
                $("#opbal_branch_div").show();			  
                accounts_opbal_master_list();
				$("#ctabid").val("account");
				$("#bulkfrm").attr("action","<?= base_url() ?>admin/bulk_updation/UpdateAccountOpBalBranchWise");
			  }
			  
			  /*
			   else if(response.call_model=="purchase_voucher_resave"){
				   window.location.href="<?php echo base_url();?>admin/bulk_updation/purchase_voucher_resave";
				   
			   }
			   else if(response.call_model=="inventory_replace_units"){
				   window.location.href="<?php echo base_url();?>admin/bulk_updation/inventory_replace_units";
				   
			   }else if(response.call_model=="accounts_replace"){
				   window.location.href="<?php echo base_url();?>admin/bulk_updation/accounts_replace";
				   
			   }
			   else{
			  $('select[name="module"]').val();
              $('select[name="module"]').trigger('change');
			  var master_title = response.master_title;
			  var sub_title = response.submaster_title;
			   $("#upld_mst_sub_title").html(master_title+"&nbsp;"+sub_title);
			   const nextTabLinkEl = $('#acess2-tab');
               const nextTab =new bootstrap.Tab(nextTabLinkEl);
               nextTab.show();	
			   
			  if(response.call_model=="items_tax"){			  
                items_master_list('tax_category');
				$("#opbal_branch_div").hide();	
				$("#ctabid").val("item");
				$("#bulkfrm").attr("action","<?= base_url() ?>admin/bulk_updation/UpdateItemTaxCatg");
			  }
			  if(response.call_model=="sale_purchase_mapping"){			  
                items_master_list('sale_purchase_accounts');
				$("#opbal_branch_div").hide();	
				$("#ctabid").val("item_salepurchase_acc");
				$("#bulkfrm").attr("action","<?= base_url() ?>admin/bulk_updation/UpdateItemSalePurchaseAccount");
			  }
			  if(response.call_model=="upc_printname_alias_mapping"){			  
                items_master_list('upc_printname_alias');
				$("#opbal_branch_div").hide();	
				$("#ctabid").val("item_upc_printname_alias");
				$("#bulkfrm").attr("action","<?= base_url() ?>admin/bulk_updation/UpdateItemUPCPrintNameAlias");
			  }
			  if(response.call_model=="item_valuation_method"){			  
                items_master_list('item_valuation_method');
				$("#opbal_branch_div").hide();	
				$("#ctabid").val("item_valuation_method");
				$("#bulkfrm").attr("action","<?= base_url() ?>admin/bulk_updation/UpdateItemDefaultValuationMethod");
			  }
			  
			  if(response.call_model=="account_tax"){
				 $("#opbal_branch_div").hide();			
                accounts_master_list();
				$("#ctabid").val("account");
				$("#bulkfrm").attr("action","<?= base_url() ?>admin/bulk_updation/UpdateAccountTaxCatg");
			  }
			  if(response.call_model=="account_opbal"){	
                $("#opbal_branch_div").show();			  
                accounts_opbal_master_list();
				$("#ctabid").val("account");
				$("#bulkfrm").attr("action","<?= base_url() ?>admin/bulk_updation/UpdateAccountOpBalBranchWise");
			  }
			  if(response.call_model=="bsd_tax"){
                $("#opbal_branch_div").hide();					  
                bsd_master_list();
				$("#ctabid").val("bsd");
				$("#bulkfrm").attr("action","<?= base_url() ?>admin/bulk_updation/UpdateBsdTaxCatg");
			  }   
				   
			   }
			  */
			  
            
          }
          else{
            alert_notification(response.message);

            if(response.errors)
            {
              var list = '';
              if(response.errors){
                $.each(response.errors, function(index, value){
                    list += '<li>'+value+'</li>';
                });

                var html = '<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button><ul>'+list+'</ul></div>';
                
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
    var $grid='';
     var supplytype_dropdown = function(ui){
		 var rd = ui.rowData;
            var $inp = ui.$cell.find("input");
            var grid = this;
			
            $inp.autocomplete({
                source: supplytypes,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                     rd.supplytype_id = ui.item.supplyid; 
					 rd.supplytype = ui.item.label;						 
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.supplytype = ''; 
				rd.supplytype='';				
                rd.pq_cellattr = {};
            }).focusout(function () {       
                if(rd.supplytype == '')
                {
                    var index = supplytypes.findIndex(function(obj) {
    
                        var string = obj.label.toLowerCase();
                        var text = rd.supplytype.toLowerCase();
                         
                        return text != '' ? string.includes(text) : false;
                    });
                    if(index > -1){                       
                        rd.supplytype = supplytypes[index].id;                        
                    }
                    else{                       
                        rd.supplytype = '';
						rd.supplytype='';		
                        rd.pq_cellattr = {};
                    }
                }else{
					grid.saveEditCell();
					var rowIndx = parseInt(ui.rowIndx)+parseInt(1);
                    var dataIndx     = ui.dataIndx;
				}
            });
			 $inp.keydown(function(e) {
               if(e.which == 13) 
               {
                    var event = $.Event( "keydown", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keypress", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keyup", { which: 9 } );
                    $inp.trigger(event);
                    
                    grid.isValid( { rowData: rd } );
              }
			  
					var rowIndx = parseInt(ui.rowIndx)+parseInt(1);
                    var dataIndx     = ui.dataIndx;
            }); 
	 }
	 
	 var taxcategory_dropdown = function (ui) {
	       var rd = ui.rowData;
            var $inp = ui.$cell.find("input");
            var grid = this;
			
            $inp.autocomplete({
                source: taxcatglist,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                     rd.tax_cat_id = ui.item.id; 
					 rd.item_tax_catg = ui.item.label;	
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.tax_cat_id = ''; 
				rd.item_tax_catg='';				
                rd.pq_cellattr = {};
            }).focusout(function () {       
                if(rd.tax_cat_id == '')
                {
                    var index = taxcatglist.findIndex(function(obj) {
    
                        var string = obj.label.toLowerCase();
                        var text = rd.item_tax_catg.toLowerCase();
                         
                        return text != '' ? string.includes(text) : false;
                    });
                    if(index > -1){                       
                        rd.tax_cat_id = taxcatglist[index].id;                        
                    }
                    else{                       
                        rd.tax_cat_id = '';
						rd.item_tax_catg='';		
                        rd.pq_cellattr = {};
                    }
                }else{
					grid.saveEditCell();
					var rowIndx = parseInt(ui.rowIndx)+parseInt(1);
                    var dataIndx     = ui.dataIndx;
				}
            });
			 $inp.keydown(function(e) {
               if(e.which == 13) 
               {
                    var event = $.Event( "keydown", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keypress", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keyup", { which: 9 } );
                    $inp.trigger(event);
                    
                    grid.isValid( { rowData: rd } );
              }
			  
					var rowIndx = parseInt(ui.rowIndx)+parseInt(1);
                    var dataIndx     = ui.dataIndx;
            });
       }
	   
	   var item_valuations_dropdown = function (ui) {
	       var rd = ui.rowData;
            var $inp = ui.$cell.find("input");
            var grid = this;
			
            $inp.autocomplete({
                source: itemvaluationtags,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                     rd.valuation_id = ui.item.id; 
					 rd.valuation_name = ui.item.label;	
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.valuation_id = ''; 
				rd.valuation_name='';				
                rd.pq_cellattr = {};
            }).focusout(function () {       
                if(rd.valuation_id == '')
                {
                    var index = taxcatglist.findIndex(function(obj) {
    
                        var string = obj.label.toLowerCase();
                        var text = rd.valuation_name.toLowerCase();
                         
                        return text != '' ? string.includes(text) : false;
                    });
                    if(index > -1){                       
                        rd.valuation_id = itemvaluationtags[index].id;                        
                    }
                    else{                       
                        rd.valuation_id = '';
						rd.valuation_name='';		
                        rd.pq_cellattr = {};
                    }
                }else{
					grid.saveEditCell();
					var rowIndx = parseInt(ui.rowIndx)+parseInt(1);
                    var dataIndx     = ui.dataIndx;
				}
            });
			 $inp.keydown(function(e) {
               if(e.which == 13) 
               {
                    var event = $.Event( "keydown", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keypress", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keyup", { which: 9 } );
                    $inp.trigger(event);
                    
                    grid.isValid( { rowData: rd } );
              }
			  
					var rowIndx = parseInt(ui.rowIndx)+parseInt(1);
                    var dataIndx     = ui.dataIndx;
            });
       }
	   
	   var salesaccount_dropdown = function (ui) {
	       var rd = ui.rowData;
            var $inp = ui.$cell.find("input");
            var grid = this;
			
            $inp.autocomplete({
                source: salesaccountslist,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                     rd.sales_acc_id = ui.item.sid; 
					 rd.item_sales_label = ui.item.label;	
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.sales_acc_id = ''; 
				rd.item_sales_label='';				
                rd.pq_cellattr = {};
            }).focusout(function () {       
                if(rd.sales_acc_id == '')
                {
                    var index = salesaccountslist.findIndex(function(obj) {
    
                        var string = obj.label.toLowerCase();
                        var text = rd.item_sales_label.toLowerCase();
                         
                        return text != '' ? string.includes(text) : false;
                    });
                    if(index > -1){                       
                        rd.sales_acc_id = salesaccountslist[index].sid;                        
                    }
                    else{                       
                        rd.sales_acc_id = '';
						rd.item_sales_label='';		
                        rd.pq_cellattr = {};
                    }
                }else{
					grid.saveEditCell();
					var rowIndx = parseInt(ui.rowIndx)+parseInt(1);
                    var dataIndx     = ui.dataIndx;
				}
            });
			 $inp.keydown(function(e) {
               if(e.which == 13) 
               {
                    var event = $.Event( "keydown", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keypress", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keyup", { which: 9 } );
                    $inp.trigger(event);
                    
                    grid.isValid( { rowData: rd } );
              }
			  
					var rowIndx = parseInt(ui.rowIndx)+parseInt(1);
                    var dataIndx     = ui.dataIndx;
            });
       }
	   var purchaseaccounts_dropdown = function (ui) {
	       var rd = ui.rowData;
            var $inp = ui.$cell.find("input");
            var grid = this;			
            $inp.autocomplete({
                source: purchaseaccountslist,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                     rd.purchase_acc_id = ui.item.pid;
					 rd.item_purchase_label = ui.item.label;
					 
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.purchase_acc_id = ''; 
				rd.item_purchase_label = ''; 
							
                rd.pq_cellattr = {};
            }).focusout(function () {       
                if(rd.purchase_acc_id == '')
                {
                    var index = purchaseaccountslist.findIndex(function(obj) {
    
                        var string = obj.label.toLowerCase();
                        var text = rd.item_purchase_label.toLowerCase();
                         
                        return text != '' ? string.includes(text) : false;
                    });
                    if(index > -1){                       
                        rd.purchase_acc_id = purchaseaccountslist[index].pid;                        
                    }
                    else{                       
                        rd.purchase_acc_id = '';
						rd.item_tax_catg='';		
                        rd.pq_cellattr = {};
                    }
                }else{
					grid.saveEditCell();
					var rowIndx = parseInt(ui.rowIndx)+parseInt(1);
                    var dataIndx     = ui.dataIndx;
				}
            });
			 $inp.keydown(function(e) {
               if(e.which == 13) 
               {
                    var event = $.Event( "keydown", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keypress", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keyup", { which: 9 } );
                    $inp.trigger(event);
                    
                    grid.isValid( { rowData: rd } );
              }
			  
					var rowIndx = parseInt(ui.rowIndx)+parseInt(1);
                    var dataIndx     = ui.dataIndx;
            });
       }
	   
	   var bsd_taxcategory_dropdown = function (ui) {
	       var rd = ui.rowData;
            var $inp = ui.$cell.find("input");
            var grid = this;
			
			
			if(rd.account_type != '0'){
                grid.saveEditCell();
                alert_notification("Only For Non Taxable Account's");
                return false
            }
			
            $inp.autocomplete({
                source: taxcatglist,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                     rd.tax_cat_id = ui.item.id; 
					 rd.item_tax_catg = ui.item.label;	
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.tax_cat_id = ''; 
				rd.item_tax_catg='';				
                rd.pq_cellattr = {};
            }).focusout(function () {       
                if(rd.tax_cat_id == '')
                {
                    var index = taxcatglist.findIndex(function(obj) {
    
                        var string = obj.label.toLowerCase();
                        var text = rd.item_tax_catg.toLowerCase();
                         
                        return text != '' ? string.includes(text) : false;
                    });
                    if(index > -1){                       
                        rd.tax_cat_id = taxcatglist[index].id;                        
                    }
                    else{                       
                        rd.tax_cat_id = '';
						rd.item_tax_catg='';		
                        rd.pq_cellattr = {};
                    }
                }else{
					grid.saveEditCell();
					var rowIndx = parseInt(ui.rowIndx)+parseInt(1);
                    var dataIndx     = ui.dataIndx;
				}
            });
			 $inp.keydown(function(e) {
               if(e.which == 13) 
               {
                    var event = $.Event( "keydown", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keypress", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keyup", { which: 9 } );
                    $inp.trigger(event);
                    
                    grid.isValid( { rowData: rd } );
              }
			  
					var rowIndx = parseInt(ui.rowIndx)+parseInt(1);
                    var dataIndx     = ui.dataIndx;
            });
       }

function findValueByKey(outputArray, key) {
    const result = outputArray.find(item => item[key] !== undefined);
    return result ? result[key] : null;  // Return the value if found, otherwise null
}	
const outputArray = [
			{ "": "Mark All Records" }, // Add empty value item with label "Choose Tax Category"
				...taxcatglist.map(item => ({ [item.id]: item.value })) // Map the input array to the desired format
			];

const Valuation_outputArray = [
			{ "": "Mark All Records" }, // Add empty value item with label "Choose Tax Category"
				...itemvaluationtags.map(item => ({ [item.id]: item.value })) // Map the input array to the desired format
			];	
const SalesAcc_outputArray = [
			{ "": "Mark All Sales Records" }, // Add empty value item with label "Choose Tax Category"
				...all_sales_accounts.map(item => ({ [item.id]: item.value })) // Map the input array to the desired format
			];	
const PurchaseAcc_outputArray = [
			{ "": "Mark All Purchase Records" }, // Add empty value item with label "Choose Tax Category"
				...all_purchase_accounts.map(item => ({ [item.id]: item.value })) // Map the input array to the desired format
			];			
  function items_master_list(filter_type='')
   {	
   if(filter_type=='tax_category'){
	    $(".filterConditionTax").show();
		 setTimeout(function() {
		   $(".filterConditionValuation").hide();	
		   $(".filterConditionSalesAcc").hide();
		   $(".filterConditionPurchaseAcc").hide();		
		}, 100);   
		   
		   
	   var taxcategory_column_editable=true;
	    var sale_column_editable=false;
		var purchase_column_editable=false;
		var itemnameinfo_column_editable=false;
		var itemvaluation_column_editable=false;
   }
   if(filter_type=='sale_purchase_accounts'){
	   var taxcategory_column_editable=false;
	    var sale_column_editable=true;
		var purchase_column_editable=true;
		var itemnameinfo_column_editable=false;
		var itemvaluation_column_editable=false;
		
		 setTimeout(function() {
			 $(".filterConditionSalesAcc").show();
		   $(".filterConditionPurchaseAcc").show();	
		   $(".filterConditionValuation").hide();
		  $(".filterConditionTax").hide();		   
		}, 100);   
		
   }
   if(filter_type=='upc_printname_alias'){
	   var taxcategory_column_editable=false;
	    var sale_column_editable=false;
		var purchase_column_editable=false;
		var itemnameinfo_column_editable=true;
		var itemvaluation_column_editable=false;
		
		 setTimeout(function() {
			 $(".filterConditionSalesAcc").hide();
		   $(".filterConditionPurchaseAcc").hide();	
		   $(".filterConditionValuation").hide();
		  $(".filterConditionTax").hide();		   
		}, 100);
   }
   if(filter_type=='item_valuation_method'){
	  
	   setTimeout(function() {
		   $(".filterConditionTax").hide();
			$(".filterConditionSalesAcc").hide();
		   $(".filterConditionPurchaseAcc").hide();			   
			
		}, 100);   
		   
	   
	   var taxcategory_column_editable=false;
	    var sale_column_editable=false;
		var purchase_column_editable=false;
		var itemnameinfo_column_editable=false;
		var itemvaluation_column_editable=true;
   }
   $.ajax({
				  type: "POST",
				  url: '<?php echo base_url();?>admin/bulk_updation/ajax_items',
				  method: 'POST',
				  data: {"pq_curpage": 1},
				  dataType: 'json',
				  beforeSend: function() {},
				  success: function(dataJSON)
				   { 
				   if(dataJSON.totalRecords>100){ $("#nextrecords").show();$("#prevrecords").show();}else{$("#nextrecords").hide();$("#prevrecords").hide();}
					
				    $("#items_grid").pqGrid('option','dataModel.data',dataJSON.data);
				     stop_loader();
				          if(dataJSON.curPage==dataJSON.totalPages){								
								$("#nextrecords").attr("data-ajax",0);
								$("#nextrecords").attr("data-role",0);
							}
							else{								
								$("#nextrecords").attr("data-ajax",dataJSON.totalPages);
								$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);
								$("#prevrecords").hide();
							}
							$("#edt_desc").html("Displaying "+dataJSON.curPage+" to "+dataJSON.renderRecods+" of "+dataJSON.totalRecords+" records");
					
					
					 $("#items_grid").pqGrid('refreshDataAndView');
					
					}
			    });
    var colModel = [
            { title: "ITEM NAME", width: 180, dataIndx: "item_name",editable:false,focus:false},
            { title: "ITEM GROUP", width: 140, dataIndx: "item_grp",editable:false,focus:false },
            { title: "UNIT", width: 140, dataIndx: "item_unit",editable:false,focus:false },
            { title: "SKU", width: 140, dataIndx: "item_sku",editable:false,focus:false },
			{ title: "UPC", width: 180, dataIndx: "item_upc",editable:itemnameinfo_column_editable,focus:false}, 
			{ title: "PRINT NAME", width: 180, dataIndx: "item_printnme",editable:itemnameinfo_column_editable,focus:false}, 
			{ title: "ALIAS NAME", width: 180, dataIndx: "item_aliasname",editable:itemnameinfo_column_editable,focus:false}, 
			{ title: "SALES ACCOUNT", width: 180, dataIndx: "item_sale_account",editable:sale_column_editable,focus:false,editor: {                   
                      type: "textbox",
                      attr: "autocomplete='off'",
                          init: salesaccount_dropdown,
                          options: [],
                      },					  
			 render: function (ui) {
                          var options = ui.column.editor.options,
                          cellData = ui.cellData;
                          for (var i = 0; i < options.length; i++) {
                              var option = options[i];
                                if (option.label == cellData) {
                                  return option.label;
                                }
                          }
                      },}, 
			{ title: "PURCHASE ACCOUNT", width: 180, dataIndx: "item_purchase_account",editable:purchase_column_editable,focus:false,editor: {                   
                      type: "textbox",
                      attr: "autocomplete='off'",
                          init: purchaseaccounts_dropdown,
                          options: [],
                      },					  
			 render: function (ui) {
                          var options = ui.column.editor.options,
                          cellData = ui.cellData;
                          for (var i = 0; i < options.length; i++) {
                              var option = options[i];
                                if (option.label == cellData) {
                                  return option.label;
                                }
                          }
                      },}, 
			{ title: "TAX CATEGORY", width: 180, dataIndx: "item_tax_catg",editable:taxcategory_column_editable,focus:true,editor: {                   
                      type: "textbox",
                      attr: "autocomplete='off'",
                          init: taxcategory_dropdown,
                          options: [],
                      },					  
			 render: function (ui) {
                          var options = ui.column.editor.options,
                          cellData = ui.cellData;
                          for (var i = 0; i < options.length; i++) {
                              var option = options[i];
                                if (option.label == cellData) {
                                  return option.label;
                                }
                          }
                      },
			}, 
			{ title: "VALUATION METHOD", width: 180, dataIndx: "item_valuation_method",editable:itemvaluation_column_editable,focus:true,editor: {                   
                      type: "textbox",
                      attr: "autocomplete='off'",
                          init: item_valuations_dropdown,
                          options: [],
                      },					  
			 render: function (ui) {
                          var options = ui.column.editor.options,
                          cellData = ui.cellData;
                          for (var i = 0; i < options.length; i++) {
                              var option = options[i];
                                if (option.label == cellData) {
                                  return option.label;
                                }
                          }
                      },
			}	
	    	];
		
			


        var dataModel = [];
        var newObj = {
            scrollModel: { autoFit: true },
			height: 450,
			collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } },
			selectionModel: { type: 'cell',mode:'single' },
			pageModel: { type: null },
            dataModel: dataModel,
            colModel : colModel,
            filterModel: { mode: 'OR', type: "local" },
            numberCell: { show: false },
            editable: true,
            showTitle: false,
            wrap:false,
			editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            create: function (evt, ui) {
                  var grid = this,
                  $select_row = $(".items_row"),
                  data = ui.dataModel.data;
                  grid.setSelection({ rowIndx: 0, focus: true });
            },
            load:function(event,ui) {               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
            },
            toolbar: {
                cls: "pq-toolbar-search",
                items: [  
            
                    { 
                        type: 'textbox', 
                        label: 'Filter: ',
                        attr: 'placeholder="Enter your keyword"', 
                        cls: "filterValue",
                        listener: { change: filterhandler_items }
                    },
                    { 
                        type: 'select', cls: "filterColumn",
                        listener: filterhandler_items,
                        options: function (ui) {
                            var CM = ui.colModel;
                            
                           // console.log(CM);
                            var opts =[];// [{ '': '[ All Fields ]'}];
                            for (var i = 0; i < CM.length; i++) {
                                var column = CM[i];
                                var obj = {};
                              if(column.dataIndx!='chkbx'){ 

                                
                                  obj[column.dataIndx] = column.title;
                                  opts.push(obj);
                               
                                  
                              }
                            }
                            return opts;
                        }
                    },
                    { 
                        type: 'select',                         
                        cls: "filterCondition",
                        listener: filterhandler_items,
                        options: [
                            { "begin": "Begins With" },
                            { "contain": "Contains" },
                            { "end": "Ends With" },
                            { "notcontain": "Does not contain" },
                            { "equal": "Equal To" },
                            { "notequal": "Not Equal To" },
                            { "less": "Less Than" },
                            { "great": "Great Than" },
                            
                        ]
                    },{ 
                        type: 'select',                         
                        cls: "filterConditionTax",
                        listener: filterhandler_taxcategory,
                        options: outputArray
                    },{ 
                        type: 'select',                         
                        cls: "filterConditionValuation",
                        listener: filterhandler_valuation,
                        options: Valuation_outputArray
                    },{ 
                        type: 'select',                         
                        cls: "filterConditionSalesAcc",
                        listener: filterhandler_SalesAcc,
                        options: SalesAcc_outputArray
                    },{ 
                        type: 'select',                         
                        cls: "filterConditionPurchaseAcc",
                        listener: filterhandler_PurchaseAcc,
                        options: PurchaseAcc_outputArray
                    }
                ]
            }
        };
	 $grid = $("#items_grid").pqGrid(newObj);	

 function filterhandler_items(evt, ui) {
    $grid = $("#items_grid");
      var $toolbar = $grid.find('.pq-toolbar-search'),
          $value = $toolbar.find(".filterValue"),
          value = $value.val(),
          condition = '',
          dataIndx = '',
          filterObject;

      if (dataIndx == "") {
          filterObject = [];
          var CM = $grid.pqGrid("getColModel");
          for (var i = 0, len = CM.length; i < len; i++) {
              var dataIndx = CM[i].dataIndx;
              filterObject.push({ dataIndx: dataIndx, condition: condition, value: value });
          }
      }
      else {//search through selected field.
          filterObject = [{ dataIndx: dataIndx, condition: condition, value: value}];
      }
      $grid.pqGrid("filter", {
          oper: 'replace',
          data: filterObject
      });
    }
	
	function filterhandler_taxcategory(evt, ui) {
     $grid = $("#items_grid");
	 var $toolbar = $grid.find('.pq-toolbar-search'),
          $value = $toolbar.find(".filterConditionTax"),
          value = $value.val(),
          condition = '',
          dataIndx = '',
          filterObject;
	if(value!=''){	  
      var data = $grid.pqGrid('option', 'dataModel.data');
	
		const tax_catg_name_arr = findValueByKey(outputArray, value);
        $.each(data, function(index,obj){
          data[index]['tax_cat_id'] = value;    
          data[index]['item_tax_catg'] = tax_catg_name_arr;		  
        });
        $grid.pqGrid('option', 'dataModel.data', data);
        $grid.pqGrid('refreshDataAndView');
	   }
    }
	function filterhandler_valuation(evt, ui) {
     $grid = $("#items_grid");
	 var $toolbar = $grid.find('.pq-toolbar-search'),
          $value = $toolbar.find(".filterConditionValuation"),
          value = $value.val(),
          condition = '',
          dataIndx = '',
          filterObject;
	if(value!=''){	  
      var data = $grid.pqGrid('option', 'dataModel.data');
	  
	  //console.log(data);
	  
		const item_valuation_method_arr = findValueByKey(Valuation_outputArray, value);
        $.each(data, function(index,obj){
          data[index]['valuation_id'] = value;    
          data[index]['item_valuation_method'] = item_valuation_method_arr;		  
        });
        $grid.pqGrid('option', 'dataModel.data', data);
        $grid.pqGrid('refreshDataAndView');
	   }
    }
	
	function filterhandler_SalesAcc(evt, ui) {
     $grid = $("#items_grid");
	 var $toolbar = $grid.find('.pq-toolbar-search'),
          $value = $toolbar.find(".filterConditionSalesAcc"),
          value = $value.val(),
          condition = '',
          dataIndx = '',
          filterObject;
	if(value!=''){	  
      var data = $grid.pqGrid('option', 'dataModel.data');
	  
	  //console.log(data);
	  
		const sales_method_arr = findValueByKey(SalesAcc_outputArray, value);
        $.each(data, function(index,obj){
          data[index]['sales_acc_id'] = value;    
          data[index]['item_sale_account'] = sales_method_arr;		  
        });
        $grid.pqGrid('option', 'dataModel.data', data);
        $grid.pqGrid('refreshDataAndView');
	   }
    }
  function filterhandler_PurchaseAcc(evt, ui) {
     $grid = $("#items_grid");
	 var $toolbar = $grid.find('.pq-toolbar-search'),
          $value = $toolbar.find(".filterConditionPurchaseAcc"),
          value = $value.val(),
          condition = '',
          dataIndx = '',
          filterObject;
	if(value!=''){	  
      var data = $grid.pqGrid('option', 'dataModel.data');
	  
	  //console.log(data);
	  
		const purchase_method_arr = findValueByKey(PurchaseAcc_outputArray, value);
        $.each(data, function(index,obj){
          data[index]['purchase_acc_id'] = value;    
          data[index]['item_purchase_account'] = purchase_method_arr;		  
        });
        $grid.pqGrid('option', 'dataModel.data', data);
        $grid.pqGrid('refreshDataAndView');
	   }
    }
  }
  
  function accounts_master_list()
   {	
   var dataModel=[];
     var branch_id = $('select[name="branchid"] option:selected').val();	
         $.ajax({
				  type: "POST",
				  url: '<?php echo base_url();?>admin/bulk_updation/ajax_accounts',
				  method: 'POST',
				  data: {"pq_curpage": 1,"branch_id":branch_id},
				  dataType: 'json',
				  beforeSend: function() {
					
				  },
				  success: function(dataJSON)
				   { 
				   
				   if(dataJSON.totalRecords>100){ $("#nextrecords").show();$("#prevrecords").show();}else{$("#nextrecords").hide();$("#prevrecords").hide();}
				    $("#accounts_grid").pqGrid('option','dataModel.data',dataJSON.data);
				     stop_loader();
				          if(dataJSON.curPage==dataJSON.totalPages){								
								$("#nextrecords").attr("data-ajax",0);
								$("#nextrecords").attr("data-role",0);
							}
							else{								
								$("#nextrecords").attr("data-ajax",dataJSON.totalPages);
								$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);
								$("#prevrecords").hide();
							}
							$("#edt_desc").html("Displaying "+dataJSON.curPage+" to "+dataJSON.renderRecods+" of "+dataJSON.totalRecords+" records");
					
					
					 $("#accounts_grid").pqGrid('refreshDataAndView');
					
					}
			    }); 
    var colModel = [
          { title: "ACCOUNT ID", width: 100, dataIndx: "acc_id",editable: false}, 
          { title: "ACCOUNT NAME", width: 100, dataIndx: "account_name",editable: false,filterable:"yes"  },
          { title: "VENDOR CODE", width: 100, dataIndx: "vendor_code",editable: false,filterable:"yes"  },
          { title: "GROUP", width: 100, dataIndx: "group_name",editable: false, filterable:"no"},
          { title: "BALANCE", width: 100, dataIndx: "op_bal",dataType: "float" ,filterable:"no",editable: false},
          { title: "DR/CR", width: 100, dataIndx: "bal_type",editable: false},
		  { title: "TAX CATEGORY", width: 100, dataIndx: "item_tax_catg",focus:true,
			  editor: {                   
                      type: "textbox",
                      attr: "autocomplete='off'",
                          init: taxcategory_dropdown,
                          options: [],
                      },					  
			 render: function (ui) {
                          var options = ui.column.editor.options,
                          cellData = ui.cellData;
                          for (var i = 0; i < options.length; i++) {
                              var option = options[i];
                                if (option.label == cellData) {
                                  return option.label;
                                }
						 }
                      },
			}, 				
	    	];
		   
		 //  console.log(dataModel);
        var newObj = {
            scrollModel: { autoFit: true },
			height: 450,
			collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } },
			selectionModel: { type: 'cell',mode:'single' },
			pageModel: { type: null },
            dataModel: dataModel,
            colModel : colModel,
           filterModel: { on: true, mode: "OR", header: true, type:'local' },
            numberCell: { show: false },
            editable: true,
            showTitle: true,
            wrap:false,
			editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            create: function (evt, ui) {
                  var grid = this,
                  $select_row = $(".items_row"),
                  data = ui.dataModel.data;
                  grid.setSelection({ rowIndx: 0, focus: true });
            },
            load:function(event,ui) {               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
            },
            toolbar: {
                cls: "pq-toolbar-search",
                items: [  
            
                    { 
                        type: 'textbox', 
                        label: 'Filter: ',
                        attr: 'placeholder="Enter your keyword"', 
                        cls: "filterValue",
                        listener: { keyup: filterhandler_items }
                    },
                    { 
                        type: 'select', cls: "filterColumn",
                        listener: filterhandler_items,
                        options: function (ui) {
                            var CM = ui.colModel;
                            
                           // console.log(CM);
                            var opts =[];// [{ '': '[ All Fields ]'}];
                            for (var i = 0; i < CM.length; i++) {
                                var column = CM[i];
                                var obj = {};
                              if(column.dataIndx!='chkbx'){ 

                                
                                  obj[column.dataIndx] = column.title;
                                  opts.push(obj);
                               
                                  
                              }
                            }
                            return opts;
                        }
                    },
                    { 
                        type: 'select',                         
                        cls: "filterCondition",
                        listener: filterhandler_items,
                        options: [
                            { "begin": "Begins With" },
                            { "contain": "Contains" },
                            { "end": "Ends With" },
                            { "notcontain": "Does not contain" },
                            { "equal": "Equal To" },
                            { "notequal": "Not Equal To" },
                            { "less": "Less Than" },
                            { "great": "Great Than" },
                            
                        ]
                    },{ 
                        type: 'select',                         
                        cls: "filterConditionTax",
                        listener: filterhandler_taxcategory,
                        options: outputArray
                    }
                ]
            }
        };
	 $grid = $("#accounts_grid").pqGrid(newObj);	
	
	function filterhandler_items(evt, ui) {
   
      var $toolbar = $grid.find('.pq-toolbar-search'),
          $value = $toolbar.find(".filterValue"),
          value = $value.val(),
          condition = $toolbar.find(".filterCondition").val(),
          dataIndx = $toolbar.find(".filterColumn").val(),
          filterObject;

      if (dataIndx == "") {
          filterObject = [];
          var CM = $grid.pqGrid("getColModel");
          for (var i = 0, len = CM.length; i < len; i++) {
              var dataIndx = CM[i].dataIndx;
              filterObject.push({ dataIndx: dataIndx, condition: condition, value: value });
          }
      }
      else {//search through selected field.
          filterObject = [{ dataIndx: dataIndx, condition: condition, value: value}];
      }
	 $("#accounts_grid").pqGrid("filter", {
                oper: 'replace',
                data: filterObject
            }); 
		
    $("#accounts_grid").pqGrid("refreshDataAndView");  // Refresh grid with filtered data
//	console.log("run searcgh");
    }
	function filterRender(ui) {
            var val = ui.cellData,
                filter = ui.column.filter;
            if (filter && filter.on && filter.value) {
                var condition = filter.condition,
                    valUpper = val.toUpperCase(),
                    txt = filter.value,
                    txt = (txt == null) ? "" : txt.toString(),
                    txtUpper = txt.toUpperCase(),
                    indx = -1;
                if (condition == "end") {
                    indx = valUpper.lastIndexOf(txtUpper);
                    //if not at the end
                    if (indx + txtUpper.length != valUpper.length) {
                        indx = -1;
                    }
                }
                else if (condition == "contain") {
                    indx = valUpper.indexOf(txtUpper);
                }
                else if (condition == "begin") {
                    indx = valUpper.indexOf(txtUpper);
                    //if not at the beginning.
                    if (indx > 0) {
                        indx = -1;
                    }
                }
                if (indx >= 0) {
                    var txt1 = val.substring(0, indx);
                    var txt2 = val.substring(indx, indx + txt.length);
                    var txt3 = val.substring(indx + txt.length);
                    return txt1 + "<span style='background:yellow;color:#333;'>" + txt2 + "</span>" + txt3;
                }
                else {
                    return val;
                }
            }
            else {
                return val;
            }
        }
	
	
function filterhandler_taxcategory(evt, ui) {
     $grid = $("#accounts_grid");
	 var $toolbar = $grid.find('.pq-toolbar-search'),
          $value = $toolbar.find(".filterConditionTax"),
          value = $value.val(),
          condition = '',
          dataIndx = '',
          filterObject;
	if(value!=''){	  
      var data = $grid.pqGrid('option', 'dataModel.data');
	  //console.log(data);	  
		const tax_catg_name_arr = findValueByKey(outputArray, value);
        $.each(data, function(index,obj){
          data[index]['tax_cat_id'] = value;    
          data[index]['item_tax_catg'] = tax_catg_name_arr;		  
        });
        $grid.pqGrid('option', 'dataModel.data', data);
        $grid.pqGrid('refreshDataAndView');
	   }
    }	
  }
 
 function countbalance(rd){
      commoncode();
  
  }

function accounts_opbal_master_list()
   {	
         $.ajax({
				  type: "POST",
				  url: '<?php echo base_url();?>admin/bulk_updation/ajax_accounts',
				  method: 'POST',
				  data: {"pq_curpage": 1},
				  dataType: 'json',
				  beforeSend: function() {
					
				  },
				  success: function(dataJSON)
				   { 
				   if(dataJSON.totalRecords>100){ $("#nextrecords").show();$("#prevrecords").show();}else{$("#nextrecords").hide();$("#prevrecords").hide();}
				    $("#accounts_grid").pqGrid('option','dataModel.data',dataJSON.data);
				     stop_loader();
				          if(dataJSON.curPage==dataJSON.totalPages){								
								$("#nextrecords").attr("data-ajax",0);
								$("#nextrecords").attr("data-role",0);
								
								
							}
							else{								
								$("#nextrecords").attr("data-ajax",dataJSON.totalPages);
								$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);
								$("#prevrecords").hide();
							}
							$("#edt_desc").html("Displaying "+dataJSON.curPage+" to "+dataJSON.renderRecods+" of "+dataJSON.totalRecords+" records");
					
					
					 $("#accounts_grid").pqGrid('refreshDataAndView');
					
					}
			    }); 
	var changeStatus = function (ui) { 

                 var rd = ui.rowData;
                 var $inp = ui.$cell.find("input");
                 ui.rowData.isedited= '1' ;
				countbalance(rd);
        }
	
		
        var drcrlist     = [{"CR.":"CR."},{"DR.":"DR."}];			
    var colModel = [
          { title: "ACCOUNT ID", width: 150, dataIndx: "acc_id",editable: false}, 
          { title: "ACCOUNT NAME", width: 150, dataIndx: "account_name",editable: false,filterable:"yes"  },
          { title: "VENDOR CODE", width: 150, dataIndx: "vendor_code",editable: false,filterable:"yes"  },
          { title: "GROUP", width: 150, dataIndx: "group_name",editable: false, filterable:"no"},
          { title: "BALANCE", width: 150, dataIndx: "op_bal",dataType: "float" ,filterable:"no", 
		   editable: function (ui) {
                   var cannotselected = ui.rowData['cannotselected'];
                    if (cannotselected != '1')  
                        return false;           
                    alert_notification("This is a restricted account."); 					
                    return false;
                }
			},
          { title: "DR/CR", width: 150, dataIndx: "bal_type",editable: false,editor: {
              type: 'select',
              init: changeStatus,
              options: drcrlist
          } },
		   				
	    	];
		var dataModel=[];   
        var newObj = {
            scrollModel: { autoFit: false },
			height: 600,
			minWidth: 600,
			collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } },
			selectionModel: { type: 'cell',mode:'single' },
			pageModel: { type: null },
            dataModel: dataModel,
            colModel : colModel,
            filterModel: { on: true, mode: "OR", header: true, type:'local' },
            numberCell: { show: false },
            editable: true,
            showTitle: true,
            wrap:false,
			editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            create: function (evt, ui) {
                  var grid = this,
                  $select_row = $(".items_row"),
                  data = ui.dataModel.data;
                  grid.setSelection({ rowIndx: 0, focus: true });
            },
            load:function(event,ui) {               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
            },
            toolbar: {
                cls: "pq-toolbar-search",
                items: [  
            
                    { 
                        type: 'textbox', 
                        label: 'Filter: ',
                        attr: 'placeholder="Enter your keyword"', 
                        cls: "filterValue",
                        listener: { change: filterhandler_items }
                    },
                    { 
                        type: 'select', cls: "filterColumn",
                        listener: filterhandler_items,
                        options: function (ui) {
                            var CM = ui.colModel;
                            
                           // console.log(CM);
                            var opts =[];// [{ '': '[ All Fields ]'}];
                            for (var i = 0; i < CM.length; i++) {
                                var column = CM[i];
                                var obj = {};
                              if(column.dataIndx!='chkbx'){ 

                                
                                  obj[column.dataIndx] = column.title;
                                  opts.push(obj);
                               
                                  
                              }
                            }
                            return opts;
                        }
                    },
                    { 
                        type: 'select',                         
                        cls: "filterCondition",
                        listener: filterhandler_items,
                        options: [
                            { "begin": "Begins With" },
                            { "contain": "Contains" },
                            { "end": "Ends With" },
                            { "notcontain": "Does not contain" },
                            { "equal": "Equal To" },
                            { "notequal": "Not Equal To" },
                            { "less": "Less Than" },
                            { "great": "Great Than" },
                            
                        ]
                    }
                ]
            }
        };
		
	 $grid = $("#accounts_grid").pqGrid(newObj);

		
	 var all_columns = <?php echo json_encode($bo_array);?>; 
	 $.each(all_columns, function(colindex,col_name){
		var dataindex_key = col_name.replace(/\s/g, ''); 
		colModel.push({title: col_name, dataIndx: dataindex_key+""+colindex,align:"center", width: 180,colModel: [{ title: "BALANCE",dataIndx: "op_bal_"+colindex,width: 100,editor:{                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        $inp.on("change", function (evt) {

						rd.brnch_balance= $(this).val();
						$("#accounts_grid").pqGrid("refresh");
						$("#accounts_grid").pqGrid("refreshDataAndView");
							
                        countbalance(rd);
							
                         });
                  }
                  },render: function(ui){
      	return ui.cellData;
 }}]}, { title: "DR/CR",dataIndx: "bal_type_"+colindex,width: 100,editor: {
              type: 'select',
              init: changeStatus,
              options: drcrlist
               }});
		
	 });	
	 $("#accounts_grid").pqGrid( "option", "colModel", colModel ); 
	 $("#accounts_grid").pqGrid("refreshCM");
     $("#accounts_grid").pqGrid("refresh");
		
		
	
	function filterhandler_items(evt, ui) {
   
      var $toolbar = $grid.find('.pq-toolbar-search'),
          $value = $toolbar.find(".filterValue"),
          value = $value.val(),
          condition = '',
          dataIndx = '',
          filterObject;

      if (dataIndx == "") {
          filterObject = [];
          var CM = $grid.pqGrid("getColModel");
          for (var i = 0, len = CM.length; i < len; i++) {
              var dataIndx = CM[i].dataIndx;
              filterObject.push({ dataIndx: dataIndx, condition: condition, value: value });
          }
      }
      else {//search through selected field.
          filterObject = [{ dataIndx: dataIndx, condition: condition, value: value}];
      }
      $grid.pqGrid("filter", {
          oper: 'replace',
          data: filterObject
      });
    }
  }
  
  function accounts_address_master_list()
   {	
         $.ajax({
				  type: "POST",
				  url: '<?php echo base_url();?>admin/bulk_updation/ajax_accounts',
				  method: 'POST',
				  data: {"pq_curpage": 1},
				  dataType: 'json',
				  beforeSend: function() {
					
				  },
				  success: function(dataJSON)
				   { 
				   if(dataJSON.totalRecords>100){ $("#nextrecords").show();$("#prevrecords").show();}else{$("#nextrecords").hide();$("#prevrecords").hide();}
				    $("#accounts_grid").pqGrid('option','dataModel.data',dataJSON.data);
				     stop_loader();
				          if(dataJSON.curPage==dataJSON.totalPages){								
								$("#nextrecords").attr("data-ajax",0);
								$("#nextrecords").attr("data-role",0);
								
								
							}
							else{								
								$("#nextrecords").attr("data-ajax",dataJSON.totalPages);
								$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);
								$("#prevrecords").hide();
							}
							$("#edt_desc").html("Displaying "+dataJSON.curPage+" to "+dataJSON.renderRecods+" of "+dataJSON.totalRecords+" records");
					
					
					 $("#accounts_grid").pqGrid('refreshDataAndView');
					
					}
			    }); 
	var changeStatus = function (ui) { 

                 var rd = ui.rowData;
                 var $inp = ui.$cell.find("input");
                 ui.rowData.isedited= '1' ;
				countbalance(rd);
        }
	
		
        var drcrlist     = [{"CR.":"CR."},{"DR.":"DR."}];			
        var colModel = [
          { title: "ACCOUNT ID", width: 150, dataIndx: "acc_id",editable: false}, 
          { title: "ACCOUNT NAME", width: 150, dataIndx: "account_name",editable: false,filterable:"yes"  },
          { title: "VENDOR CODE", width: 150, dataIndx: "vendor_code",editable: false,filterable:"yes"  },
          { title: "GROUP", width: 150, dataIndx: "group_name",editable: false, filterable:"no"},
          { title: "BALANCE", width: 150, dataIndx: "op_bal",dataType: "float" ,filterable:"no", 
		   editable: function (ui) {
                   var cannotselected = ui.rowData['cannotselected'];
                    if (cannotselected != '1')  
                        return false;           
                    alert_notification("This is a restricted account."); 					
                    return false;
                }
			},
          { title: "DR/CR", width: 150, dataIndx: "bal_type",editable: false,editor: {
              type: 'select',
              init: changeStatus,
              options: drcrlist
          } },
		   				
	    	];
		var dataModel=[];   
        var newObj = {
            scrollModel: { autoFit: false },
			height: 600,
			minWidth: 600,
			collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } },
			selectionModel: { type: 'cell',mode:'single' },
			pageModel: { type: null },
            dataModel: dataModel,
            colModel : colModel,
            filterModel: { on: true, mode: "OR", header: true, type:'local' },
            numberCell: { show: false },
            editable: true,
            showTitle: true,
            wrap:false,
			editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            create: function (evt, ui) {
                  var grid = this,
                  $select_row = $(".items_row"),
                  data = ui.dataModel.data;
                  grid.setSelection({ rowIndx: 0, focus: true });
            },
            load:function(event,ui) {               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
            },
            toolbar: {
                cls: "pq-toolbar-search",
                items: [  
            
                    { 
                        type: 'textbox', 
                        label: 'Filter: ',
                        attr: 'placeholder="Enter your keyword"', 
                        cls: "filterValue",
                        listener: { change: filterhandler_items }
                    },
                    { 
                        type: 'select', cls: "filterColumn",
                        listener: filterhandler_items,
                        options: function (ui) {
                            var CM = ui.colModel;
                            
                           // console.log(CM);
                            var opts =[];// [{ '': '[ All Fields ]'}];
                            for (var i = 0; i < CM.length; i++) {
                                var column = CM[i];
                                var obj = {};
                              if(column.dataIndx!='chkbx'){ 

                                
                                  obj[column.dataIndx] = column.title;
                                  opts.push(obj);
                               
                                  
                              }
                            }
                            return opts;
                        }
                    },
                    { 
                        type: 'select',                         
                        cls: "filterCondition",
                        listener: filterhandler_items,
                        options: [
                            { "begin": "Begins With" },
                            { "contain": "Contains" },
                            { "end": "Ends With" },
                            { "notcontain": "Does not contain" },
                            { "equal": "Equal To" },
                            { "notequal": "Not Equal To" },
                            { "less": "Less Than" },
                            { "great": "Great Than" },
                            
                        ]
                    }
                ]
            }
        };
		
	 $grid = $("#accounts_grid").pqGrid(newObj);

		
	 var all_columns = <?php echo json_encode($bo_array);?>; 
	 $.each(all_columns, function(colindex,col_name){
		var dataindex_key = col_name.replace(/\s/g, ''); 
		colModel.push({title: col_name, dataIndx: dataindex_key+""+colindex,align:"center", width: 180,colModel: [{ title: "BALANCE",dataIndx: "op_bal_"+colindex,width: 100,editor:{                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        $inp.on("change", function (evt) {

						rd.brnch_balance= $(this).val();
						$("#accounts_grid").pqGrid("refresh");
						$("#accounts_grid").pqGrid("refreshDataAndView");
							
                        countbalance(rd);
							
                         });
                  }
                  },render: function(ui){
      	return ui.cellData;
 }}]}, { title: "DR/CR",dataIndx: "bal_type_"+colindex,width: 100,editor: {
              type: 'select',
              init: changeStatus,
              options: drcrlist
               }});
		
	 });	
	 $("#accounts_grid").pqGrid( "option", "colModel", colModel ); 
	 $("#accounts_grid").pqGrid("refreshCM");
     $("#accounts_grid").pqGrid("refresh");
		
		
	
	function filterhandler_items(evt, ui) {
   
      var $toolbar = $grid.find('.pq-toolbar-search'),
          $value = $toolbar.find(".filterValue"),
          value = $value.val(),
          condition = '',
          dataIndx = '',
          filterObject;

      if (dataIndx == "") {
          filterObject = [];
          var CM = $grid.pqGrid("getColModel");
          for (var i = 0, len = CM.length; i < len; i++) {
              var dataIndx = CM[i].dataIndx;
              filterObject.push({ dataIndx: dataIndx, condition: condition, value: value });
          }
      }
      else {//search through selected field.
          filterObject = [{ dataIndx: dataIndx, condition: condition, value: value}];
      }
      $grid.pqGrid("filter", {
          oper: 'replace',
          data: filterObject
      });
    }
  }
  
  function bsd_master_list()
   {	
   $.ajax({
				  type: "POST",
				  url: '<?php echo base_url();?>admin/bulk_updation/ajax_bsd',
				  method: 'POST',
				  data: {"pq_curpage": 1},
				  dataType: 'json',
				  beforeSend: function() {
					
				  },
				  success: function(dataJSON)
				   { 
				   if(dataJSON.totalRecords>100){ $("#nextrecords").show();$("#prevrecords").show();}else{$("#nextrecords").hide();$("#prevrecords").hide();}
				    $("#bsd_grid").pqGrid('option','dataModel.data',dataJSON.data);
				     stop_loader();
				          if(dataJSON.curPage==dataJSON.totalPages){								
								$("#nextrecords").attr("data-ajax",0);
								$("#nextrecords").attr("data-role",0);
							}
							else{								
								$("#nextrecords").attr("data-ajax",dataJSON.totalPages);
								$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);
								$("#prevrecords").hide();
							}
							$("#edt_desc").html("Displaying "+dataJSON.curPage+" to "+dataJSON.renderRecods+" of "+dataJSON.totalRecords+" records");
					
					
					 $("#bsd_grid").pqGrid('refreshDataAndView');
					
					}
			    });
    var colModel = [
            { title: "NAME", width: 180, dataIndx: "billsndry_name",editable: false,filterable:"no"},
			{ title: "GROUP", width: 180, dataIndx: "billsundry_group_name",editable: false,filterable:"no"},
            { title: "TYPE", width: 140, dataIndx: "billsndry_type",editable: false,filterable:"no"},
            { title: "NATURE", width: 140, dataIndx: "billsndry_nature",editable: false,filterable:"no"} ,
            { title: "OP.BAL.", width: 140, dataIndx: "bsd_op_bal",editable: false,filterable:"no",dataType:"float"} ,
            { title: "DR/CR", width: 140, dataIndx: "bsd_op_bal_drcr",editable: false,filterable:"no",},
			{ title: "TAX CATEGORY", width: 180, dataIndx: "item_tax_catg",focus:true,
			  editor: {                   
                      type: "textbox",
                      attr: "autocomplete='off'",
                          init: bsd_taxcategory_dropdown,
                          options: [],
                      },					  
			 render: function (ui) {
                          var options = ui.column.editor.options,
                          cellData = ui.cellData;
                          for (var i = 0; i < options.length; i++) {
                              var option = options[i];
                                if (option.label == cellData) {
                                  return option.label;
                                }
                          }
                      },
				}, 	
	    	];
        var dataModel = [];
        var newObj = {
            scrollModel: { autoFit: true },
			height: 450,
			collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } },
			selectionModel: { type: 'cell',mode:'single' },
			pageModel: { type: null },
            dataModel: dataModel,
            colModel : colModel,
            filterModel: { mode: 'OR', type: "remote" },
            numberCell: { show: false },
            editable: true,
            showTitle: false,
            wrap:false,
			editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            create: function (evt, ui) {
                  var grid = this,
                  $select_row = $(".items_row"),
                  data = ui.dataModel.data;
                  grid.setSelection({ rowIndx: 0, focus: true });
            },
            load:function(event,ui) {               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
            },
            toolbar: {
                cls: "pq-toolbar-search",
                items: [  
            
                    { 
                        type: 'textbox', 
                        label: 'Filter: ',
                        attr: 'placeholder="Enter your keyword"', 
                        cls: "filterValue",
                        listener: { change: filterhandler_items }
                    },
                    { 
                        type: 'select', cls: "filterColumn",
                        listener: filterhandler_items,
                        options: function (ui) {
                            var CM = ui.colModel;
                            
                           // console.log(CM);
                            var opts =[];// [{ '': '[ All Fields ]'}];
                            for (var i = 0; i < CM.length; i++) {
                                var column = CM[i];
                                var obj = {};
                              if(column.dataIndx!='chkbx'){                                 
                                  obj[column.dataIndx] = column.title;
                                  opts.push(obj);                              
                                  
                              }
                            }
                            return opts;
                        }
                    },
                    { 
                        type: 'select',                         
                        cls: "filterCondition",
                        listener: filterhandler_items,
                        options: [
                            { "begin": "Begins With" },
                            { "contain": "Contains" },
                            { "end": "Ends With" },
                            { "notcontain": "Does not contain" },
                            { "equal": "Equal To" },
                            { "notequal": "Not Equal To" },
                            { "less": "Less Than" },
                            { "great": "Great Than" },
                            
                        ]
                    }
                ]
            }
        };
	 $grid = $("#bsd_grid").pqGrid(newObj);	
	
	function filterhandler_items(evt, ui) {
   
      var $toolbar = $grid.find('.pq-toolbar-search'),
          $value = $toolbar.find(".filterValue"),
          value = $value.val(),
          condition = '',
          dataIndx = '',
          filterObject;

      if (dataIndx == "") {
          filterObject = [];
          var CM = $grid.pqGrid("getColModel");
          for (var i = 0, len = CM.length; i < len; i++) {
              var dataIndx = CM[i].dataIndx;
              filterObject.push({ dataIndx: dataIndx, condition: condition, value: value });
          }
      }
      else {//search through selected field.
          filterObject = [{ dataIndx: dataIndx, condition: condition, value: value}];
      }
      $grid.pqGrid("filter", {
          oper: 'replace',
          data: filterObject
      });
    }
  }  
  
  

	});
  var processing = false;

  

</script>
 </body>
</html>
