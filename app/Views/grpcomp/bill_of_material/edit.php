<?php $header = array( 	'title' => 'Update Bill Of Material' ); ?>
<?php echo view('includes/header',$header); 



$itemn_units_labels = array();

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
?>
<style>
 .salefrm .col-sm-6{padding-bottom:2px;}
    .salefrm label{width:25%; float:left;}
    .salefrm .form-control, .myform select,input.custom-combobox-input {width:75%;}
    .salefrm .select2 {width:75%!important; }
</style>	  
      <?php $attributes = " id='salefrm' name='salefrm' class='needs-validation salefrm' novalidate";
             echo form_open(base_url().'/'.$folder_path.'billofmaterial/modify/'.$billofmaterial_id, $attributes);
       ?>
       <div class=" row">
             <div class="col-md-6 pb-3"><h3 class="pb-3">Bill of Material (BOM)</h3></div>  <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></div> 
       </div>   
 <div class="row">
  <div class="col-md-6">
			<h5>BOM Name</h5>
			<?php 
			if($bom_info && isset($bom_info['bom_name'])){
				$bom_name = $bom_info['bom_name'];
			}
			else
				$bom_name =='';
				?>
			
			<input type="text" name="bom_name" id="bom_name" class="form-control form-control-sm" value="<?php echo $bom_name;?>" required>
			</div>
</div>	   
       <div class=" row">
              
            <div class="col-md-6">
			<h5>Item Consumed</h5>
			 <div id="itemconsumed_grid" style="margin:auto;"></div>  
			</div>
			
			<div class="col-md-6">
			
			<div class=" row">
			
			<div class="col-md-12">
			<h5>Item Produced</h5>
			 <div id="itemproduced_grid" style="margin:auto;"></div>  
			</div>
			
			<div class="col-md-12"><br />
			<h5>By Products Produced</h5>
			 <div id="byproductproduced_grid" style="margin:auto;"></div>  
			</div>
			
			
			<div class="col-md-12"><br />
			<h5>Additional Cost</h5>
			 <div id="additionalcost_grid" style="margin:auto;"></div>  
			</div>
						
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
    
        </div>
	
				
     
<?php echo view('includes/footer_scripts');



for($i=1;$i<=50;$i++)
   $item_consumed_json_data[] =array("id"=>'',"item_name"=>'','item_qty'=>'','short_narator'=>'','item_unit'=>'','item_price'=>'','item_amount'=>'');


for($i=1;$i<=50;$i++)
   $byproductproduced_json_data[] =array("id"=>'',"item_name"=>'','item_qty'=>'','item_unit'=>'','item_price'=>'','item_amount'=>'','item_price'=>'');

for($i=1;$i<=50;$i++)
   $itemproduced_json_data[] =array("id"=>'',"item_name"=>'','item_qty'=>'','item_unit'=>'','item_price'=>'','item_amount'=>'','item_value'=>'');

for($i=1;$i<=50;$i++)
   $additionalcost_json_data[] =array("id"=>'',"expense_name"=>'','expense_type'=>'','expense_basis'=>'','expense_amount'=>'');

 ?>
<script>
var itemsobj_itmcns = new Map();
var itemsobj_itmprd = new Map();
var itemsobj_byprd = new Map();
var itemsobj_adcst = new Map();

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




    $(document).on('blur','[name="billsndry_name"]', function(){
    var name = $(this).val().trim();
    if(name){
        if(!$('[name="billsndry_allias"]').val().trim())
        {
           $('[name="billsndry_allias"]').val(name); 
        }
        if(!$('[name="billsndry_pname"]').val().trim())
        {
           $('[name="billsndry_pname"]').val(name); 
        }
    }
});



var intRegex = /^\d+$/;
var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
<?php if($expense_heads_list){ ?>
var expense_heads = <?php echo json_encode($expense_heads_list);?>; 
<?php } else { ?>
 var expense_heads = []; 
<?php } ?>
   
   <?php
$itemn_units_labels=array();
if($bom_info['item_consumed_unit_labels']){
    foreach($bom_info['item_consumed_unit_labels'] as $itmrr){
       $itemn_units_labels[trim($itmrr['label'])]= $itmrr['value'];
    }
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
	 
$("#submitbtn").on("click",function(){
	var isvalid1 = itemconsumed_func();
	var isvalid2 = itemproduced_func();
    var isvalid3 = byproducts_produced_func();
	additional_cost_func();
	
	//show_loader();
   if($("#bom_name").val()==''){
	 alert_notification("BOM name is required!!!");   
	 return false;  
   }	
   else if(itemconsumed_func() =='0' && itemproduced_func() =='0' && byproducts_produced_func() =='0')
     return false;
    else
	$("#salefrm").submit();
	
});
</script>