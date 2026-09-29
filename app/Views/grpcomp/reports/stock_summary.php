<?php $header = array( 	'title' => 'Stock Summary' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>

<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>

<form class="myform" method="post">
  <div class=" row">
   <div class="col-6">
    <h3 class="pb-3">Stock Summary</h3>
  </div> 
  <div class="col-6">
    <span class="float-end">
      <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-outline-success">Back</a>
    </span>
  </div> 


  <div class="col-md-6 card p-3 m-auto shadow-lg mt-3">
   <h5 class="pb-5">Financial Year <?= grp_comp()->fy_name ?>
 </h5>
 <div class="col-12">
  <label>Summary Type</label> 
  <select name="summary_type" id="summary_type" class="form-select">
    <option value="stock_summary">Stock Summary</option>
    <option value="mc_summary">Material Centre Summary</option>
    <option value="phtvrf_summary">Physical Verification Summary</option>
    <option value="stcktrnsfr_summary">Stock Transfer Summary</option>
  </select>
</div> 

<div class="col-12 sub_type" id="stock_summary_type_div">
 <label>Sub  Type</label> 
 <select name="stck_summary_type" class="form-select">
  <option value="stock_item">Stock Item</option>
  <option value="item_grp">Item Group</option>
  <option value="stock_catg">Stock Category</option>
</select>
</div> 
<div class="col-12 sub_type" id="mc_summary_type_div" style="display:none;">
 <label>Sub  Type</label> 
 <select name="mc_summary_type" class="form-select">
  <option value="item_summary">Material Receipt / Issue </option>
  <option value="mc_summary">Material Receipt </option>
  <option value="mc_summary">Material Issue  </option>
</select>
</div> 
<div class="col-12 sub_type" id="phys_summary_type_div" style="display:none;">
  <label>Sub  Type</label> 
  <select name="phys_summary_type" class="form-select">
    <option value="item_summary">All MC </option>
    <option value="mc_summary">One MC </option>
    <option value="mc_summary">One Store  </option>
    <option value="mc_summary">MC Groups  </option>
  </select>
</div> 
<div class="col-12 sub_type" id="stcktrn_summary_type_div" style="display:none;">
  <label>Sub  Type</label> 
  <select name="stcktrn_summary_type" class="form-select">
    <option value="item_summary">Stock Transfer (Other MC)</option>
    <option value="mc_summary">Stock Transfer (Other BO)</option>
    <option value="mc_summary">Stock Transfer (In-Transit)</option>
    <option value="mc_summary">Stock Transfer Receipt </option>
  </select>
</div>   


<div class="col-12" id="mc_type2_div" style="display:none;">
  <label>Type 2</label> 
  <select name="mc_type2" class="form-select">
   <option value="all_mc">All MC</option>
   <option value="one_mc">One MC</option>
   <option value="one_store">One Store</option>
   <option value="mc_group">Mc Group</option>
 </select>
</div> 
<div class="col-12" id="stcktrnsfr_type2_div" style="display:none;">
  <label>Type 2</label> 
  <select name="stcktrnsfr_type2" class="form-select">
   <option value="all_mc">All MC</option>
   <option value="one_mc">One MC</option>
   <option value="one_store">One Store</option>
   <option value="mc_group">Mc Group</option>
 </select>
</div>
<div class="col-12" id="criteria_div">
  <label>Criteria</label> 
  <div class="input-group w-75">
    <select name="criteria_type"  id="criteria_type" class="form-select">
      <option value="all_mc_cr">All MC</option>
      <option value="one_mc_cr">One MC</option>
      <option value="mc_group_cr">Mc Group</option>
    </select>

    <div>
    </div>

  </div>

</div>    
<div id="mc_div" class="col-12" style="display:none;">
  <label class="label">Mc</label> 
  <div class="w-75 d-inline-block">
    <select name="mat_cent_id" id="mat_cent_id" class="form-select">
     <?php foreach ($matrcntr_dropdown as $key => $value) { ?>
       <option value="<?= $value['id'] ?>"><?= $value['label'] ?></option>
     <?php } ?>
   </select>
  </div>
</div>
<div id="mc_grp_div" class="col-12" style="display:none;">
  <label class="label">Mc Group</label> 
  <div class="w-75 d-inline-block">
    <select name="mat_cent_grpid" id="mat_cent_grpid" class="form-select">
     <?php foreach ($matrcntr_grp_dropdown as $key => $value) { ?>
       <option value="<?= $value['id'] ?>"><?= $value['label'] ?></option>
     <?php } ?>
   </select>
  </div>
</div> 


<div id="item_div" class="col-12">
  <label class="label">Stock Item</label> 
  <div class="input-group w-75">

    <select name="item_id" id="item_id" style="font-size:18px;border-radius: 6px 0px 0px 6px;" class="form-control" required>
       <option value=""></option>
       <?php foreach ($items_dropdown as $key => $value) { ?>
         <option value="<?= $value['id'] ?>"><?= $value['label'] ?></option>
       <?php } ?>
    </select>


    <select name="unit_id" id="unit_id" style="font-size:18px;border-radius: 6px 0px 0px 6px;" class="form-control" required>
       <option value=""></option>
       <?php foreach ($units_dropdown as $key => $value) { ?>
         <option value="<?= $value['id'] ?>"><?= $value['label'] ?></option>
       <?php } ?>
    </select>


  </div>
</div>



<div id="temp_div" class="col-12" style="display:none;">
 <label class="label">Select</label> 
 <div class="w-75 d-inline-block">
  <select class="selectwidget form-control"  style="border:3px solid #000; font-size:18px;">
    <option value="">
    </option>
  </select>
</div>
</div>



<div class="col-md-12 text-center  my-3">
 <input type="submit" value="GO" class="btn btn-success mr-1">
 <input type="reset" value="QUIT" class="btn btn-secondary">
</div>

</div>
</form>		
<?php echo view('includes/'.$folder_path.'footer_scripts'); ?>
<script>
  $("#mat_cent_id_cr").hide();
   $("#criteria_type").on("change",function(){
        var  criteria_type_val = $(this).val();
		if(criteria_type_val=='one_mc_cr'){
			$("#mc_div").show();
			$("#mc_grp_div").hide();
			
			
		}
		else if(criteria_type_val=='mc_group_cr'){
			$("#mc_div").hide();
			$("#mc_grp_div").show();
		
			
		}
		else{
		$("#mc_div").hide();
			$("#mc_grp_div").hide();	
		}
			
   });
  //   $("#item_id").on("change",function(){
	// 	show_loader();
  //     var itemid = $(this).val();  
	
  //     $.post(baseurl+'/admin/ajax/ajax_item_units_list', {item_id:itemid}, function(response){ 
       
  //       $("#unitid").html(response);
		
	// 	stop_loader();
  //    });

	// });
   
   
   
   

    $("#summary_type").on("change",function(){
        var  summary_type_val = $(this).val();
        if(summary_type_val=='mc_summary'){
            $("#stock_summary_type_div").hide();
            $("#phys_summary_type_div").hide();
            $("#stcktrn_summary_type_div").hide();
            $("#mc_summary_type_div").show();
            
            $("#mc_div").show();
            $("#item_div").hide();
            // type divs
            $("#mc_type2_div").show();
            $("#stcktrnsfr_type2_div").hide();
            $("#stcksmry_type2_div").hide();
            
            $("#temp_div").hide();
            
            var value = $("#mc_summary_type_div select option:selected").text();
            $(".label").text(value);
        }
        else if(summary_type_val=='stock_summary'){
            $("#stock_summary_type_div").show();
            $("#phys_summary_type_div").hide();
            $("#stcktrn_summary_type_div").hide();
            $("#mc_summary_type_div").hide();
            $("#mc_div").hide();
            $("#item_div").show();
            // type divs
            $("#mc_type2_div").hide();
            $("#stcktrnsfr_type2_div").hide();
            $("#stcksmry_type2_div").show();
            
            $("#temp_div").hide();
            
            var value = $("#stock_summary_type_div select option:selected").text();
            $(".label").text(value);
        }
        else if(summary_type_val=='phtvrf_summary'){
            $("#stock_summary_type_div").hide();
            $("#phys_summary_type_div").show();
            $("#stcktrn_summary_type_div").hide();
            $("#mc_summary_type_div").hide();
            $("#mc_div").hide();
            $("#item_div").hide();
            // type divs
            $("#mc_type2_div").hide();
            $("#stcktrnsfr_type2_div").hide();
            $("#stcksmry_type2_div").hide();
            
            $("#temp_div").show();
            
            var value = $("#phys_summary_type_div select option:selected").text();
            $(".label").text('Stock Item');
        }
        else if(summary_type_val=='stcktrnsfr_summary'){
            $("#stock_summary_type_div").hide();
            $("#phys_summary_type_div").hide();
            $("#stcktrn_summary_type_div").show();
            $("#mc_summary_type_div").hide();
            $("#mc_div").hide();
            $("#item_div").hide();
            // type divs
            $("#mc_type2_div").hide();
            $("#stcktrnsfr_type2_div").show();
            $("#stcksmry_type2_div").hide();
            
            $("#temp_div").show();
            
            var value = $("#stcktrn_summary_type_div select option:selected").text();
            $(".label").text(value);
        }
   })
   
   $(document).on('change', '.sub_type select', function(){
       var value = $(this).find('option:selected').text();
       $(".label").text(value);
   });
</script>
</body>
</html>
