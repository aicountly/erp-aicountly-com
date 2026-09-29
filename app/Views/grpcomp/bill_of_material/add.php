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
             <div class="col-md-6 pb-3"><h3 class="pb-3">Bill of Material (BOM)</h3></div>  <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></div> 
       </div>   
 <div class="row">
  <div class="col-md-6">
			<h5>BOM Name</h5>
			<input type="text" name="bom_name" id="bom_name" class="form-control form-control-sm" required>
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
</body>
</html>