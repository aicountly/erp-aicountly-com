<?php $header = array('title' => 'Stock Ledger');?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>

<div class="row mb-md-0 mb-3">
    <div class="col-6"><h3 class="pb-3">Stock Ledger</h3></div>
    <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></div>
</div>
</div>

<form class="form needs-validation" method="post" id="salefrm"  novalidate autocomplete="off">
 <div class="col-12 mb-5 calccard card m-auto">
  <div class="card-header">
    <div class="row">  

      <div class="col-md-6">
        <label>   Type</label>
        <select class="form-select mt-1 borderdark" name="type" fdprocessedid="9xwps">
          <option value=""></option>
          <?php foreach($dropdown as $key => $value){ ?>
            <option <?= $key == 'Stock Ledger' ? 'selected' : '' ?> value="<?php echo $key; ?>"><?php echo $key; ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="col-md-6">
       <label> Module</label>
       <select class="form-select mt-1 borderdark" name="module" fdprocessedid="9xwps">
        <option value=""></option>
        <?php foreach($dropdown['Stock Ledger'] as $key => $value){ ?>
          <option <?= $value == 'Item Account' ? 'selected' : '' ?> value="<?php echo $value; ?>"><?php echo $value; ?></option>
        <?php } ?>
      </select>    
    </div>
    <div class="col-md-6 mt-2" id="mc_crteria_div">
     <label> Criteria</label>
     <select name="criteria_type"  id="criteria_type" class="form-select">
      <option value="all_mc_cr">All MC</option>
      <option value="one_mc_cr">One MC</option>
      <option value="mc_group_cr">Mc Group</option>
    </select>

  </div>
  <div class="col-md-6 mt-2" id="allmc_div">
   <label> Mc</label>
   <input type="text" class="form-control mt-1 borderdark" name="allmc" disabled="disabled"  fdprocessedid="9xwps">
 </div>
 <div class="col-md-6 mt-2" id="mc_div" style="display:none;">
   <label> Mc</label>
   <select name="mat_cent_id" id="mat_cent_id" class="form-select">
     <?php foreach ($matrcntr_dropdown as $key => $value) { ?>
       <option value="<?= $value['id'] ?>"><?= $value['label'] ?></option>
     <?php } ?>
   </select>
 </div>
 <div class="col-md-6 mt-2" id="mc_grp_div" style="display:none;">
   <label> Mc Group</label>
   <select name="mat_cent_grpid" id="mat_cent_grpid" class="form-select">
     <?php foreach ($matrcntr_grp_dropdown as $key => $value) { ?>
       <option value="<?= $value['id'] ?>"><?= $value['label'] ?></option>
     <?php } ?>
   </select>
 </div>


<!--  <div class="col-md-6 mt-2" id="item_crteria_div" style="display:none;">
   <label> Criteria</label>
   <select name="itm_criteria_type"  id="itm_criteria_type" class="form-select">
    <option value="all_tracking">All Tracking</option>
    <option value="one_tracking">One Tracking</option>                      
  </select>

</div> -->

<!--   <div class="col-md-6 mt-2" id="trackno_div" style="display:none;">
   <label> Tracking No</label>
   <input type="text" class="form-control mt-1 borderdark" name="tracking_no" id="tracking_no" disabled="disabled"  fdprocessedid="9xwps">
  </div> -->

  <div class="col-md-6 mt-2">
    <label> Item</label>
    <input name="item" type="text" class="form-control borderdark"  required>
    <input type="hidden" name="item_id" value="">

  </div>
  <div class="col-md-6 mt-2">
      <label>UOM</label>
      <select name="unit_id" id="unit_id" class="form-select">
       <option value="0">All Units</option>
       <?php foreach ($units_dropdown as $key => $value) { ?>
         <option value="<?= $value['id'] ?>"><?= $value['label'] ?></option>
       <?php } ?>
     </select>
  </div>

</div>
</div>

<div class="row p-4">

  <div class="comp_calender col-md-6">
    <button type="button" data-month="4" class="btn btn-light month_btn">APR</button>
    <button type="button" data-month="7" class="btn btn-light month_btn">JUL</button>
    <button type="button" data-month="10" class="btn btn-light month_btn">OCT</button>
    <button type="button" data-month="1" class="btn btn-light month_btn">JAN</button>
    <button type="button" data-month="5" class="btn btn-light month_btn">MAY</button>
    <button type="button" data-month="8" class="btn btn-light month_btn">AUG</button>   
    <button type="button" data-month="11" class="btn btn-light month_btn">NOV</button> 
    <button type="button" data-month="2" class="btn btn-light month_btn">FEB</button>
    <button type="button" data-month="6" class="btn btn-light month_btn">JUN</button>
    <button type="button" data-month="9" class="btn btn-light month_btn">SEP</button>
    <button type="button" data-month="12" class="btn btn-light month_btn">DEC</button>
    <button type="button" data-month="3" class="btn btn-light month_btn">MAR</button>
    <button type="button" data-quater="1" class="btn btn-qlight quater_btn">Q1</button>
    <button type="button" data-quater="2" class="btn btn-qlight quater_btn">Q2</button>
    <button type="button" data-quater="3" class="btn btn-qlight quater_btn">Q3</button>
    <button type="button" data-quater="4" class="btn btn-qlight quater_btn">Q4</button>
    <button type="button" data-hyear="1" class="btn btn-hlight hyear_btn">H1</button>
    <button type="button" data-hyear="2" class="btn btn-hlight hyear_btn">H2</button>

    <span class="fw-bold d-inline-block px-4"><input class="form-check-input mt-1 me-1" type="checkbox" value="1" id="tillperiod"> TILL PERIOD</span>
  </div>
  <div class="col-md-6">
    <div class="input-group mb-3">
      <button type="button" class="input-group-text" id="prev_year"><span class="material-symbols-outlined">arrow_back_ios</span></button>
      <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:68%; text-align: center; display: block;"></button>
      <button type="button" class="input-group-text" id="next_year"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
    </div>

    <div class="row align-items-center my-2">
      <div class="col-md-2 fw-bold pe-0">From</div>
      <div class="col-md-10">
        <div class="calc-inputgroup">
          <input type="text" class="form-control"  value="<?= date('01-m-Y') ?>" name="from_date" id="fromdate" placeholder="dd-mm-yyyy" required>
        </div>
      </div>
    </div>

    <div class="row align-items-center my-2">
      <div class="col-md-2 fw-bold pe-0">To</div>
      <div class="col-md-10">
        <div class="calc-inputgroup">
          <input type="text" class="form-control"   value="<?= date('d-m-Y') ?>" name="to_date" id="todate" placeholder="dd-mm-yyyy" required>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6 d-grid p-2" >
        <span class="fw-bold d-inline-block px-4">
          <input class="form-check-input mt-1 me-1" type="checkbox" value="1" id="tillperiod"> 
          TILL PERIOD
        </span>
      </div>
      <div class="col-md-6 text-end">
        <button type="button" class="input-group-text fw-bold ms-auto"  id="tilldate" required>TILL DATE</button>
      </div>
    </div>

  </div> 

  <p class="text-center pt-4"><button type="submit" class="btn btn-success btn-lg w-100">GO</button></p>  

</div>
</div>
</div>
</form>
 

<?php echo view('includes/'.$folder_path.'footer_scripts'); ?>
<script>

if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
    window.location.reload();
}

var item_list = <?php echo json_encode($items_dropdown) ?>;

getList(item_list);

function getList(list) 
{
    $('input[name="item"]').off('blur'); // unbind event first
    
    if($('input[name="item"]').hasClass('ui-autocomplete-input')) {
        $('input[name="item"]').autocomplete("destroy");
    }
    
    $('input[name="item"]').autocomplete({
        source: list,
        minLength: 0,
        select: function( event, ui ) {
            $(this).val(ui.item.label);
            $('input[name="item_id"]').val(ui.item.id);
            // get_item_units(ui.item.id);
        }
    })
    .on('focus', function(){
        $(this).autocomplete( "search", "" );
        $('input[name="item"]').val('');
        $('input[name="item_id"]').val('');
    })
    .on('blur', function(){
        autoSetItem(list)
    })
}

    function autoSetItem(list)
    {
        if($('input[name="item"]').val() != '' && $('input[name="item_id"]').val() == '')
        {
            var acc = $('input[name="item"]').val();
            var index = list.findIndex(function(obj) {
                var string = obj.label.toLowerCase();
                var text = acc.toLowerCase();
               return  string.includes(text);
            });
            if(index > -1){
                $('input[name="item"]').val(list[index].label);
                $('input[name="item_id"]').val(list[index].id);
                // get_item_units(list[index].id);
            }
            else{
                $('input[name="item"]').val('');
                $('input[name="item_id"]').val('');
            }
        }
        if($('input[name="item_id"]').val() == '')
        {
            $('input[name="item"]').val('');
            $('input[name="item_id"]').val('');
        }
    }

    function get_item_units(item_id)
    {
        var itemid = item_id;  
    
          $.post(baseurl+'/admin/ajax/ajax_item_units_list', {item_id:itemid}, function(response){ 
           
            $("#unitid").html(response);
            
            stop_loader();
         });
    }

    
    $("#tracking_no").on("blur",function(){
        show_loader();
      var tracking_no = $(this).val();  
    
      $.post(baseurl+'/admin/ajax/ajax_tracking_items_list', {tracking_no:tracking_no}, function(response){ 
      if(response!='')
        $("#item_id").val(response); 
         $.post(baseurl+'/admin/ajax/ajax_item_units_list', {item_id:response}, function(response){ 
       
        $("#unitid").html(response);
        
        
     });
     
        stop_loader();
     });

    });
    
    
    
  $("#itm_criteria_type").on("change",function(){
       var  criteria_type_val = $(this).val();
       if(criteria_type_val=='all_tracking'){
       $("#tracking_no").attr('disabled', 'disabled'); 
        $("#item_id").attr('disabled', 'disabled');
        $("#unitid").attr('disabled', 'disabled');
       
       }
     else{
         $("#tracking_no").attr('disabled', false); 
          $("#item_id").attr('disabled', false);
        $("#unitid").attr('disabled', false);
         
     }
      
  });   
    
  $("#criteria_type").on("change",function(){
        var  criteria_type_val = $(this).val();
        $('#mat_cent_id').val('');
        $('#mat_cent_grpid').val('');

        if(criteria_type_val=='one_mc_cr'){
            $("#mc_div").show();
            $("#mc_grp_div").hide();
            $("#allmc_div").hide();
            
        }
        else if(criteria_type_val=='mc_group_cr'){
            $("#mc_div").hide();
            $("#mc_grp_div").show();
             $("#allmc_div").hide();
            
        }
        else{
        $("#mc_div").hide();
            $("#mc_grp_div").hide();
            $("#allmc_div").show();         
        }
            
   });
   
   
var arr = <?php echo json_encode($dropdown) ?>;
        
$('[name="type"]').change(function(){
  var value = $(this).val();
  
  $('[name="module"]').html('');
  $('[name="item_id"]').val('');
  $('[name="item_name"]').val('');
  $('[name="item_name"]').attr('disabled', 'disabled');
  
  if(value != ""){
    $.each(arr, function( index, val ) {
      if(index == value){
          var html = `<option value=""></option>`;
          $.each(val, function(i,v){
              html += `<option value="${v}">${v}</option>`;
          })
          $('[name="module"]').html(html);
      }
      
    });
 }
 
 
});

$('[name="module"]').change(function(){
  var module = $(this).val();
  
  $('[name="item_id"]').val('');
  $('[name="item_name"]').val('');
  $('[name="item_name"]').attr('disabled', 'disabled');
  if(module != ""){
    $('[name="item_name"]').attr('disabled', false);
 }
 if(module == "Item Tracking"){
    $('[name="item_name"]').attr('disabled', false);
     $("#item_id").attr('disabled', 'disabled');
        $("#unitid").attr('disabled', 'disabled');
        
    $("#item_crteria_div").show();
    $("#trackno_div").show();
    $("#mc_crteria_div").hide();
    $("#allmc_div").hide();
    
    
 }
 else{
      $("#item_id").attr('disabled', false);
        $("#unitid").attr('disabled', false);
     
    $("#item_crteria_div").hide();
    $("#trackno_div").hide();
    $("#mc_crteria_div").show();
    $("#allmc_div").show();  
 }
 
});

$( '[name="item_name"]' ).autocomplete({
    source: function( request, response ) {
      var type = $('[name="type"]').val();
      var module = $('[name="module"]').val();
        
      $.ajax({
          url: "<?php echo base_url();?>/admin/reports/get_items",
          dataType: "json",
          data: {
            type: type, module: module, term: request.term
          },
          success: function( data ) {
            response( data );
          }
        });
      },
    //   source:  "<?php echo base_url();?>/admin/reports/get_items",
      delay: 250,
      minLength: 3,
      select: function( event, ui ) {
        event.preventDefault();
        $(this).val(ui.item.label);
        $('[name="item_id"]').val(ui.item.id);
      }
    });


</script>
</body>
</html>
