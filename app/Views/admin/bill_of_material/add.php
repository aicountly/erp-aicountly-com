<?php $header = array( 	'title' => 'Add Bill Of Material' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .salefrm .col-sm-6{padding-bottom:2px;}
    .salefrm label{width:25%; float:left;}
    .salefrm .form-control, .myform select,input.custom-combobox-input {width:75%;}
    .salefrm .select2 {width:75%!important; }
</style>	  
      <?php $attributes = " id='salefrm' name='salefrm' class='needs-validation salefrm' novalidate";
             echo form_open(base_url().'/'.$folder_path.'billofmaterial/add', $attributes);
       ?>
       <div class=" row">
             <div class="col-md-6 pb-3">
			 <h3 class="pb-3">Bill of Material (BOM)</h3>
			 </div>

			 <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></div> 
       </div>   
 <div class="row">
  <div class="col-md-3">
			<h5>BOM Name</h5>
			<input type="text" name="bom_name" id="bom_name" class="form-control form-control-sm" required>
			</div>
			<div class="col-md-3">
			<h5>BOM Group</h5>
			<?php	
			echo form_dropdown('bom_group', $bom_group, set_value('bom_group'),'id="bom_group" class="form-control w-75" ');
			?>
			</div>
			 <div class="col-md-3">
			 <h5>Consumption Pricing</h5>
			 <select class="form-control" name="consumption_pricing" id="consumption_pricing">
			 <option value="f">Fixed Pricing</option>
			 <option value="a">Auto Valuation</option>
			 </select>
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
$itemn_units_labels = array();
for($i=1;$i<=500;$i++)
   $item_consumed_json_data[] =array("id"=>'',"item_name"=>'','item_qty'=>'','short_narator'=>'','item_unit'=>'','item_price'=>'','item_amount'=>'');


for($i=1;$i<=50;$i++)
   $byproductproduced_json_data[] =array("id"=>'',"item_name"=>'','item_qty'=>'','item_unit'=>'','item_price'=>'','item_amount'=>'','item_price'=>'');

for($i=1;$i<=50;$i++)
   $itemproduced_json_data[] =array("id"=>'',"item_name"=>'','item_qty'=>'','item_unit'=>'','item_price'=>'','item_amount'=>'','item_price'=>'');

for($i=1;$i<=50;$i++)
   $additionalcost_json_data[] =array("id"=>'',"expense_name"=>'','expense_type'=>'','expense_basis'=>'','expense_amount'=>'');

 ?>
<script>
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

var itemsobj_itmcns = new Map();
var itemsobj_itmprd = new Map();
var itemsobj_byprd = new Map();
var itemsobj_adcst = new Map();
var intRegex = /^\d+$/;
var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
<?php if($expense_heads_list){ ?>
var expense_heads = <?php echo json_encode($expense_heads_list);?>; 
<?php } else { ?>
 var expense_heads = []; 
<?php } ?>
   
    <?php if($itemn_units_labels!=''){ ?>
    var unitslist = <?php echo json_encode($units_list);?>; 
    <?php } else{ ?>
    var unitslist = []; 
    <?php } ?>
    
    
    <?php if($itemn_units_labels!=''){ ?>
    var unitslablesr1 =<?php echo json_encode($itemn_units_labels);?>;
	 var unitslablesr2 =<?php echo json_encode($itemn_units_labels);?>;
	  var unitslablesr3 =<?php echo json_encode($itemn_units_labels);?>;
    <?php } else { ?>
     var unitslablesr1 = [];
	 var unitslablesr2 = [];
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
	
	$(document).on("change", "#consumption_pricing", function () {
    var grid = $("#itemconsumed_grid").pqGrid("getInstance").grid;
    var colModel = grid.option('colModel');

    if ($(this).val() == 'a') {
        [3, 4, 5].forEach(function (index) {
            if (colModel[index]) {
                colModel[index].editable = false;

                // Add custom render to change background color for readonly cells
                colModel[index].render = function (ui) {
                    return {
                        text: 'Auto',
                        style: "background-color:#f5f5f5;" // light gray background for readonly
                    };
                };
            }
        });

        grid.refresh();
    }
    else if ($(this).val() == 'f') {
        [3, 4, 5].forEach(function (index) {
            if (colModel[index]) {
                colModel[index].editable = true;

                // Remove render function to reset styling to normal
                delete colModel[index].render;
            }
        });

        grid.refresh();
    }
});
	 
$("#submitbtn").on("click",function(){
	
	var isvalid1 = itemconsumed_func();
	var isvalid2 = itemproduced_func();
	byproducts_produced_func();
	additional_cost_func(); // Just calling it, no validation needed?

	if ($("#bom_name").val().trim() == '') {
		alert_notification("BOM name is required!!!");
		return false;
	}

	if (isvalid1.status == '0') {
		alert_notification(isvalid1.message);
		return false;
	}

	if (isvalid2 == '0') {
		alert_notification("Please add Item Produced details!");
		return false;
	}
	// If all checks pass
	$("#salefrm").submit();
	return false;
});
</script>
</body>
</html>