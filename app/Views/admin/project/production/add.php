<?php $header = array( 	'title' => 'Production Voucher' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
if($bom_info){
 $show_consumption_pricing_tag = '(Fixed Pricing)';	
 if($bom_info['bom_consm_pricing']=='f')	
 $show_consumption_pricing_tag = '(Fixed Pricing)';
 else if($bom_info['bom_consm_pricing']=='a')	
 $show_consumption_pricing_tag = '(Auto Valuation)';
 
}else
	$show_consumption_pricing_tag = '';
if($bom_info && isset($bom_info['item_consumed_data'])){
	$item_consumed_json_data = $bom_info['item_consumed_data'];
}

if($bom_info && isset($bom_info['item_produced_data'])){
	$itemproduced_json_data = $bom_info['item_produced_data'];
}

if($bom_info && isset($bom_info['byproduct_produced_data'])){
	$byproductproduced_json_data = $bom_info['byproduct_produced_data'];
}

if($bom_info && isset($bom_info['additional_cost_data'])){
	$additionalcost_json_data = $bom_info['additional_cost_data'];
}

if($bom_info && isset($bom_info['expensetypes_labels'])){
	$expensetypes_json_data = $bom_info['expensetypes_labels'];
}

$itemn_units_labels=array();
if($bom_info['item_consumed_unit_labels']){
    foreach($bom_info['item_consumed_unit_labels'] as $itmrr){
       $itemn_units_labels[trim($itmrr['label'])]= $itmrr['value'];
    }
}


$itempr_units_labels=array();
if($bom_info['item_produced_unit_labels']){
    foreach($bom_info['item_produced_unit_labels'] as $itmrr){
       $itempr_units_labels[trim($itmrr['label'])]= $itmrr['value'];
    }
}

$itembyprd_units_labels=array();
if($bom_info['byproduct_produced_unit_labels']){
    foreach($bom_info['byproduct_produced_unit_labels'] as $itmrr){
       $itembyprd_units_labels[trim($itmrr['label'])]= $itmrr['value'];
    }
}

?>
<style>
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

    
</style>

<div class="row mb-2">
<div class="col-md-6 order-1"><h3 class="pb-3">Production Voucher<?php echo $show_consumption_pricing_tag;?></h3></div>

<div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
<a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>
<a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history"><span class="material-symbols-outlined">visibility</span></a>
 <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a>
    <a href="#"><span class="material-symbols-outlined open-comingsoon">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon"><span class="material-symbols-outlined">download</span></a>
    <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">CSV</a></li>
    <li><a class="dropdown-item" href="#">Excel</a></li>
    <li><a class="dropdown-item" href="#">Document</a></li>
  </ul>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon">share</span></a> 
   <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Facebook</a></li>
    <li><a class="dropdown-item" href="#">Twitter</a></li>
    <li><a class="dropdown-item" href="#">Instagram</a></li>
  </ul>
      <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>
    </div>
    </div>
             
      <div class="col-md-6 order-2 order-md-3">
          </div>
 <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">          
     <button class="btn btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Action</a></li>
    <li><a class="dropdown-item" href="#">Another action</a></li>
    <li><a class="dropdown-item" href="#">Something else here</a></li>
  </ul>
  <button class="btn btn-success m-1" type="button">Templates</button> 
 <button type="button" class="btn btn-success m-1">Party Dashboard</button>
   <a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a>
</div>

</div>



<div class="offcanvas offcanvas-end" tabindex="-1" id="moreoptions" aria-labelledby="moreoptionslabel">
  <div class="offcanvas-header">
    <h4 class="offcanvas-title" id="moreoptionslable">Apps</h4>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
      <div class="row">
       
       <div class="col-sm-6 border-end"> 
       <h5 class="pb-3">Horizontal</h5>
       
      <p class="offcanvaoptions"><i>Condensed</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
      
      <p class="offcanvaoptions"><i>Detailed</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
      
      <p class="offcanvaoptions"><i>All Labels</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
       </div>
       
       <div class="col-sm-6"> 
       <h5 class="pb-3">Verticle</h5>
       
      <p class="offcanvaoptions"><i>Verticle</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
      
      <p class="offcanvaoptions"><i>Schudle</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
       </div>
       
        <div class="col-sm-12 pt-3 border-top"> 
      <p class="offcanvaoptions"><i>Schedule</i>
      <label class="form-check">No<input class="form-check-input mx-1" name="schedule" type="radio" value="no" id="swap"></label>
      <label class="form-check">Yes<input class="form-check-input mx-1" name="schedule" type="radio" value="yes" id="swap"></label>
      </p>
      <p class="offcanvaoptions"><i>Ratio</i>
      <label class="form-check">No<input class="form-check-input mx-1" name="ratio" type="radio" value="no" id="swap"></label>
      <label class="form-check">Yes<input class="form-check-input mx-1" name="ratio" type="radio" value="yes" id="swap"></label>
      </p>
      
      <p class="text-center pt-3"><a  data-bs-toggle="modal" data-bs-target="#moreoptionsmodal" class="btn btn-outline-success">View</a></p>
        
        </div>
       
          
      </div>
   
  </div>
</div>

<!-- Modal -->
<div class="modal fade mt-5" id="moreoptionsmodal" tabindex="-1" aria-labelledby="moreoptionsmodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="moreoptionsmodalLabel">App Options title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success">Save changes</button>
      </div>
    </div>
  </div>
</div>



<form class="form" method="post" id="salefrm">
   
        <div class="row m-0 mb-2 p-0">
      
         <div class="col-md-3 col-6 card p-2">
    <div class="input-group">
     <label class="input-group-text">BOM:<?php if($bom_id!=''){ ?><span id="clearbom">&nbsp;<a href="javascript:void(0);" onclick="window.location.href='<?php echo base_url();?>/admin/production/add';">clear</a></span><?php } ?></label>
     <?php 	 
	 if($bom_id!=''){
		 if(isset($bom_dropdown[$bom_id])){
		 echo '<label class="input-group-text">'.$bom_dropdown[$bom_id].'</label>';
		 echo '<input type="hidden" name="bom_dropdown" id="bom_dropdown" value="'.$bom_id.'">';
		 }
	 }else
     echo form_dropdown('bom_dropdown', $bom_dropdown, $bom_id,' id="bom_dropdown" class="form-select selectwidget required" required '); ?>
    </div>
   </div>
   <div class="col-md-3 col-6 card p-2">
    <div class="input-group">
     <span class="input-group-text">No. of Batches Produced:</span>
     <input type="text" name="total_batches_produced" id="total_batches_produced" class="form-control form-control-sm" value="<?php echo $total_batches;?>">
	 <button class="btn btn-success btn-sm" id="fetchbom">GO</button>
    </div>
   </div>
     <div class="col-md-3 col-6 card p-2">
          <div class="input-group">
          <label class="input-group-text">Date: <?php if($bom_id!=''){ ?><span id="cleardate">&nbsp;<a href="javascript:void(0);" id="clearedate">clear</a></span><?php } ?></label>
          <?php if($bom_id!=''){ ?>
		  <input type="text"  id="production_date" name="production_date" value="<?php echo $voucher_date;?>" class="form-control form-control-sm" readonly>
		  
		  <?php } else { ?>
		  <input type="text"  id="production_date" name="production_date" value="<?php echo $voucher_date;?>" class="datepicker form-control form-control-sm" readonly>
		  
		  <?php }?>
		  
		  </div> 
		  </div>  
   
      <div class="col-md-3 col-6 card p-2"><div class="input-group"><label class="input-group-text">Series:</label>
      <?php	
        echo form_dropdown('voucher_series', $voucher_series_dropdown, $voucher_id,' id="voucher_series" class="selectwidget voucher_series form-control" required ');
		?></div></div>
    
      <div class="col-md-6 col-6 card p-2"><div class="input-group"><label class="input-group-text">Material Centre:</label><?php	
        echo form_dropdown('matrcntr_id', $matrcntr_dropdown, "1",' id="matrcntr_id" class="selectwidget form-control required" ');
		?></div></div>
		
		 <div class="col-md-6 col-6 card p-2"><div class="input-group">
		<select form="salefrm" name="currency_id" class="form-select d-inline-block" style="width:160px;">
<?php foreach ($currency_list as $value) { ?>
<option value="<?= $value['comp_currency_id'] ?>"><?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
<?php } ?>
</select></div></div>

      
     
    </div>
   
<div class=" row">
              
            <div class="col-md-6">
			<h5>Item Consumed</h5>
			 <div id="itemconsumed_grid" style="margin:auto;"></div>  
			</div>
			
			<div class="col-md-6">
			
			<div class=" row">
			
			<div class="col-md-12">
			<h5>Item Generated</h5>
			 <div id="itemproduced_grid" style="margin:auto;"></div>  
			</div>
			
			<div class="col-md-12"><br />
			<h5>By Products Generated</h5>
			 <div id="byproductproduced_grid" style="margin:auto;"></div>  
			</div>
			
			
			<div class="col-md-12"><br />
			<h5>Additional Cost</h5>
			 <div id="additionalcost_grid" style="margin:auto;"></div>  
			</div>
					<input type="hidden" name="salevchtxn" id="salevchtxn" value="<?php echo $salevchtxn;?>">	
			</div>
			 <input type="hidden" name="grd1data" id="grd1data">
             <input type="hidden" name="grd2data" id="grd2data">
	         <input type="hidden" name="grd3data" id="grd3data">
	         <input type="hidden" name="grd4data" id="grd4data">
			</div>
			
               <div class="col-md-12 text-center my-3">
                <input type="button" value="SAVE" id="submitbtn" class="btn btn-primary mx-2">
                <input type="reset" value="QUIT" onclick="window.history.go(-1); return false;" class="btn btn-secondary mx-2">
              </div>
            
         </div>  
   
</form>
<?php echo view('includes/footer_scripts'); 

for($i=1;$i<=500;$i++)
   $item_consumed_json_data[] =array("item_unit_id"=>"","id"=>'',"item_id"=>'',"item_name"=>'','item_qty'=>'','short_narator'=>'','item_unit'=>'','item_unit_id'=>'','item_price'=>'','item_amount'=>'');


for($i=1;$i<=50;$i++)
   $byproductproduced_json_data[] =array("item_unit_id"=>"","id"=>'',"item_id"=>'',"item_name"=>'','item_qty'=>'','item_unit'=>'','item_unit_id'=>'','item_price'=>'','item_amount'=>'','item_price'=>'');

for($i=1;$i<=50;$i++)
   $itemproduced_json_data[] =array("item_unit_id"=>"","id"=>'',"item_id"=>'',"item_name"=>'','item_qty'=>'','item_unit'=>'','item_unit_id'=>'','item_price'=>'','item_amount'=>'','item_price'=>'');

for($i=1;$i<=50;$i++)
   $additionalcost_json_data[] =array("item_unit_id"=>"","id"=>'',"expense_name"=>'','expense_type'=>'','expense_basis'=>'','expense_amount'=>'');

?>
<style>
    .boldcell{font-weight:700;}
</style>
?>
<script>
var itemsobj_itmcns  = new Map();
var itemsobj_itmprd  = new Map();
var itemsobj_byprd   = new Map();
var itemsobj_adcst   = new Map();
var items_types_obj  = new Map();
var consprcoptn      = '<?php echo $bom_info['bom_consm_pricing'];?>';
<?php
if(isset($item_consumed_json_data)){ 
foreach($item_consumed_json_data as $itemrow1){ if(isset($itemrow1['item_id']) && $itemrow1['item_id']!=''){
?>
itemsobj_itmcns.set('<?php echo $itemrow1['item_name'];?>', '<?php echo $itemrow1['item_id'];?>');
<?php } } } ?>
<?php  
if(isset($itemproduced_json_data)){ 
foreach($itemproduced_json_data as $itemrow2){ if(isset($itemrow2['item_id']) && $itemrow2['item_id']!=''){
?>
itemsobj_itmprd.set('<?php echo $itemrow2['item_name'];?>', '<?php echo $itemrow2['item_id'];?>');
<?php } } } ?>

<?php 
if(isset($byproductproduced_json_data)){ 
foreach($byproductproduced_json_data as $itemrow3){ if(isset($itemrow3['item_id']) && $itemrow3['item_id']!=''){
?>
itemsobj_byprd.set('<?php echo $itemrow3['item_name'];?>', '<?php echo $itemrow3['item_id'];?>');
<?php } } } ?>

<?php 
if(isset($additionalcost_json_data)){ 
foreach($additionalcost_json_data as $itemrow4){ if(isset($itemrow4['id']) && $itemrow4['id']!=''){
?>
itemsobj_adcst.set('<?php echo $itemrow4['expense_name'];?>', '<?php echo $itemrow4['id'];?>');
<?php } } } ?>


<?php 
if(isset($expensetypes_json_data)){ 
foreach($expensetypes_json_data as $itemrow5){ if(isset($itemrow5['label']) && $itemrow5['label']!=''){
?>
items_types_obj.set('<?php echo $itemrow5['label'];?>', '<?php echo $itemrow5['value'];?>');
<?php } } } ?>




var intRegex = /^\d+$/;
var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
<?php if($expense_heads_list){ ?>
var expense_heads = <?php echo json_encode($expense_heads_list);?>; 
<?php } else { ?>
 var expense_heads = []; 
<?php } ?>
   
   var unitslist = new Map();
   var unitslablesr1 = new Map();
   var unitslablesr2 = new Map();
    var unitslablesr3 = new Map();
     <?php if($units_list!=''){ ?>
    var unitslist = <?php echo json_encode($units_list);?>; 
    <?php } else{ ?>
    var unitslist = []; 
    <?php } ?>
    
    
    <?php if($itemn_units_labels!=''){ ?>
    var unitslablesr1 =<?php echo json_encode($itemn_units_labels);?>;
    <?php } else { ?>
     var unitslablesr1 = [];
    <?php } ?>
	
	<?php if($itempr_units_labels!=''){ ?>
    var unitslablesr2 =<?php echo json_encode($itempr_units_labels);?>;
    <?php } else { ?>
     var unitslablesr2 = [];
    <?php } ?>
	
	
	<?php if($itembyprd_units_labels!=''){ ?>
    var unitslablesr3 =<?php echo json_encode($itembyprd_units_labels);?>;
    <?php } else { ?>
     var unitslablesr3 = [];
    <?php } ?>
    var item_json_file              = <?php echo $item_json_file;?>;
       var item_consumed_json_data  = <?php echo json_encode($item_consumed_json_data);?>;
	   var item_produced_json_data  = <?php echo json_encode($itemproduced_json_data);?>;
	   var byproductproduced_grid   = <?php echo json_encode($byproductproduced_json_data);?>;
	   var additionalcost_grid      = <?php echo json_encode($additionalcost_json_data);?>;
</script>
   <script type="text/javascript" src="<?php echo base_url();?>/public/grid_js/item_consumed_grid.js" ></script>
   <script type="text/javascript" src="<?php echo base_url();?>/public/grid_js/item_produced_grid.js" ></script>
   <script type="text/javascript" src="<?php echo base_url();?>/public/grid_js/byproducts_produced_grid.js" ></script>
   <script type="text/javascript" src="<?php echo base_url();?>/public/grid_js/additional_cost_grid.js" ></script>
   <script>
	$(".pq-pager-msg").hide();
	cmmngridfncy(consprcoptn);
	$("#clearedate").on("click",function(){
		$(this).hide();
	 $(this).addClass("datepicker");
	 $("#production_date").datepicker({
        altFormat: "dd-mm-yy",
        dateFormat: "dd-mm-yy",		
	});
	 
	});
	
	$('#production_date').on('change', function () {
    $('#salefrm').submit();
  });
	
   function cmmngridfncy(consprcing){
	   var grid = $("#itemconsumed_grid").pqGrid("getInstance").grid;
    var colModel = grid.option('colModel');

    if (consprcing == 'a') {
        [3, 4, 5].forEach(function (index) {
            if (colModel[index]) {
                colModel[index].editable = false;

                // Add custom render to change background color for readonly cells
                colModel[index].render = function (ui) {
                    return {
                        text: ui.cellData,
                        style: "background-color:#f5f5f5;" // light gray background for readonly
                    };
                };
            }
        });

        grid.refresh();
    }
    else if (consprcing == 'f') {
        [3, 4, 5].forEach(function (index) {
            if (colModel[index]) {
                colModel[index].editable = true;

                // Remove render function to reset styling to normal
                delete colModel[index].render;
            }
        });

        grid.refresh();
    }
   }
	
	 $("#bom_dropdown").on("change",function(){
		 if($(this).val()=='')
			location.reload();
	 });
	 
 $("#fetchbom").on("click",function(){
	 var total_batches_produced = $("#total_batches_produced").val();
	 var bom_dropdown = $("#bom_dropdown").val();
	 if(bom_dropdown==''){
		 alert_notification("BOM is required!!");
		 return false;
	 }
    if(total_batches_produced==''){
		 alert_notification("No. of Batches is required!!");
		 return false;
	 }
	 
	 if(total_batches_produced=='' || total_batches_produced=='0')
		  total_batches_produced='1';
	 
	 if(bom_dropdown!=''){
	    window.location.href= baseurl+'/admin/production/add?bom_id='+bom_dropdown+'&total_batches='+total_batches_produced;
	 }
	 return false;
 });	 
	 
$("#submitbtn").on("click",function(){
	var isvalid1 = itemconsumed_func();
	var isvalid2 = itemproduced_func();
	var isvalid3 = byproducts_produced_func();
	var isvalid4 =additional_cost_func();
	
   if($("#bom_dropdown").val()==''){
	 alert_notification("BOM is required!!!");   
	 return false;  
   }	
   else if(itemconsumed_func() =='0' && itemproduced_func() =='0' && byproducts_produced_func() =='0')
     alert_notification("Fill data first!!!");
    else
	{
	show_loader();
    $.ajax(
			{  data: {"pstaction":"add","tbatch":$("#total_batches_produced").val(),"bom_id":$("#bom_dropdown").val(),"matrcntr_id":$("#matrcntr_id").val(),"voucher_series":$("#voucher_series").val(),"FromSaleVch":$("#salevchtxn").val(),"production_date":$("#production_date").val(), "itemconsumed_grid":isvalid1, "itemproduced_grid": isvalid2 ,"byproductproduced_grid": isvalid3 ,"additionalcost_grid": isvalid4 },
			   type: "POST",
			   url: baseurl+'/admin/production/ajax_post_grid'
			})
			.done(function (response) { 	
                if(typeof response == 'string')
                    response = JSON.parse(response);
				if(response.status==true){
                alert_success(response.message);
				 <?php if(isset($_GET['p']) && $_GET['p'] == 1){ ?>
                        window.history.back();
                    <?php } else { ?>
                        window.location.reload();
                    <?php } ?>
				}
			   else
				  alert_notification(response.message);  
              
              


			// window.location.href = baseurl+"/admin/production/add";
			stop_loader(); })
			.error(function (objAjaxRequest, strError) {
				 alert_notification("There is an error , try again!!!");
				 stop_loader();
					//var respText = objAjaxRequest.responseText;
                 });		
	}	
});
</script>
</body>
</html>