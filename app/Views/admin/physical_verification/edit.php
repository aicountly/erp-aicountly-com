<?php $header = array( 	'title' => 'Modify Physical Verification' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('Y-m-d',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('Y-03-31', strtotime($fy_begndt. ' + 1 year'));
?>

<div class="row mb-2">
<div class="col-md-6 order-1"><h3>Modify Physical Verification</h3></div>
<div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
     <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>
    <a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history"><span class="material-symbols-outlined">visibility</span></a>
    <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 
    <a href="#"><span class="material-symbols-outlined">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></a>
    <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">CSV</a></li>
    <li><a class="dropdown-item" href="#">Excel</a></li>
    <li><a class="dropdown-item" href="#">Document</a></li>
  </ul>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">share</span></a> 
      <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Facebook</a></li>
    <li><a class="dropdown-item" href="#">Twitter</a></li>
    <li><a class="dropdown-item" href="#">Instagram</a></li>
  </ul>
       <a href="https://sandbox.aicountly.in/admin/bulk_updation" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>
    </div>
    </div>
             
   <div class="col-md-6 order-2 order-md-3">
    <div class="form-check form-check-inline me-2">
        <input class="form-check-input" type="checkbox" value="1" name="oCheck" id="oCheck" form="salefrm" <?= ($oCheck == 1) ? 'checked' : '' ?>>
        <label class="form-check-label" for="oCheck">Optional</label>
    </div></div>
     <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
      <button class="btn btn-success m-1" type="button">View</button> 
      <a href="https://sandbox.aicountly.in/admin/bulk_updation"  class="btn btn-outline-success btn-sm showinline-md">« Back</a>
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
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

</div>


<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
    <div class="col-12">
        <div class="row m-0 p-0">
      <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Series:</label>
      <?php	
      echo form_dropdown('voucher_series', $voucher_series_dropdown, $comp_vch_info['comp_vch_series_id'],' id="voucher_series" class="selectwidget voucher_series form-control required" ');
		?></div></div>
      <div class="col-md-3 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Date:<span id="cleardate"></span></label> <input type="text" id="voucher_date" name="voucher_date" value="<?php echo date('d-m-Y', strtotime($comp_vch_info['voucher_date']));?>" class="datepicker form-control form-control-sm" readonly></div></div>  
      <div class="col-md-3 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Voucher No:</label> <input type="text" name="voucher" class="form-control form-control-sm" value="<?php echo $voucher_auto_no;?>" disabled></div></div>
      <div class="col-md-4 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Material Centre:</label><?php	
        echo form_dropdown('matrcntr_id', $matrcntr_dropdown,  $mc_id,' id="matrcntr_id" class="selectwidget form-control required" ');
		?></div></div>

      <div class="col-md-12 col-12 card p-2"><div class="input-group">
              <label class="input-group-text">Narration:</label>
			<textarea  name="narration" class="form-control"><?php if($get_voucher_narration_info){
				echo $get_voucher_narration_info['vch_narr'];
			} ?></textarea>

			</div></div>
      <input type="hidden" name="itmsdata" id="itmsdata">
      <input type="hidden" name="vchrdate" id="vchrdate" readonly>
    </div></div>
   

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br> 
    
     <div class="col-12 text-center">
         <br><br>
         
            <button type="button" id="submitbtn" class="btn btn-success mx-2" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">Save</button>
   
        <a href="javascript:main(0)"  onclick="window.history.go(-1); return false;" class="btn btn-secondary mx-2">QUIT</a>
        <a href="javascript:main(0)"  class="deletebtn btn btn-danger mx-2">Delete</a>
        
    </div>
        
   
   
</form>
<script>
    var itemlist      = [];  
    var itemsobj      = new Map();
    var itemsobj_sock = new Map();
    </script>
<?php echo view('includes/footer_scripts'); 
$json_data=array();
if($transactions){
     
    
     foreach($transactions as $tranrow){
          $json_data[] = array("item_id"=>$tranrow['item_id'],"item_unit_id"=>$tranrow['item_unit_id'],"item_unit"=>$tranrow['item_unit'],"voucher_date"=>$tranrow['voucher_date'],"bill_no"=>$tranrow["bill_no"],"item_name"=>$tranrow["item_name"],'physical_stock'=>$tranrow["phy_stock"],'book_stock'=>$tranrow["book_stock"],'stock_diff'=>$tranrow["stock_diff"],'short_narration'=>$tranrow["narration"]);
    ?>
    <script>
       itemsobj.set('<?php echo $tranrow["item_name"];?>','<?php echo $tranrow["item_id"];?>'); 
        itemsobj_sock.set('<?php echo $tranrow["item_name"];?>','<?php echo $tranrow["book_stock"];?>');
    </script>
    <?php
    
     }
     
 }
 
 
//for($i=1;$i<=50;$i++)
//$json_data[] =array("id"=>'',"item_name"=>'','item_qty'=>'','short_narator'=>'','item_unit'=>'','item_price'=>'','item_amount'=>'');

?>



<style>
    .boldcell{font-weight:700;}
</style>

<script>
$(".deletebtn").on('click',function(){
    confirm_delete(baseurl+"/admin/physical_verification/delete/<?php echo $voucher_txn_id;?>");		    
   return false
})
    
   $("#voucher_date").on("change",function(){
   
    $(this).attr('readonly',true);
    $(this).attr('disabled',true); 
    $(this).removeClass('datepicker');
    $(this).removeClass('hasDatepicker');
    $("#vchrdate").val($(this).val());
    
     $("#cleardate").html('<a href="javascript:void(0);" onclick="location.reload();">clear</a>');
    
   }); 
   
   
    $("#submitbtn").on("click",function(){
     var item_checked = [];
     var final_item_id =[];
     var data = $("#grid_search").pqGrid('option', 'dataModel.data');

     var final_item_amouint ="0";
     var final_physical_stock_amouint ="0";
    
      var sdateFrom = '<?php echo $fy_begndt;?>';
    var sdateTo   = '<?php echo $fy_end;?>';
    var sdateCheck  =  $("#voucher_date").val();
    var sd1 = sdateFrom.split("-");
    var sd2 = sdateTo.split("-");
    var sc  = sdateCheck.split("-");
    
    var sfrom_year = sd1[2];  // -1 because months are from 0 to 11
    var sto_year   = sd2[2];
    var scheck_year = sc[2];//, parseInt(c[1])-1, c[0]);
    
    if( (scheck_year==sfrom_year) || (scheck_year==sto_year))
     var sdatevalidate=1;
    else
     var sdatevalidate=0;
     
   
       for (var i = 0; i < data.length; i++) {
            var item_unit_id  = data[i]['item_unit_id'];
           var item_id       = data[i]['item_id'];
           var item_name   = data[i]['item_name'];
           var physical_stock  = data[i]['physical_stock'];
           var book_stock    = data[i]['book_stock'];
           var stock_diff   = data[i]['stock_diff'];
           var short_narration = data[i]['short_narration'];
           
           short_narration = short_narration ? short_narration : '';
           if(!physical_stock)
             physical_stock_amount =0;
             else
             physical_stock_amount = physical_stock;
             final_physical_stock_amouint=parseInt(final_physical_stock_amouint)+parseInt(physical_stock_amount);
             
             
            if(item_name!=''){
           
                item_checked.push({
                    "item_unit_id": item_unit_id,
                    "item_id": item_id,
                    "physical_stock": physical_stock,
                    "book_stock" :book_stock,
                    "stock_diff" :stock_diff,
                    "short_narration" :short_narration,
                   
                 });
             
            }
         }
      
      var selerror=0;
      if($('select').hasClass('required')){
          $(".required ").each(function() {
           var txtbx_val = $(this).val();
           if(txtbx_val.length=="0"){
              selerror=1;
              $(this).css('border-color','#ed2000');
              }else{
                $('.custom-combobox input').removeAttr("style");  
                $(this).parent('select').removeClass('required');  
                $(this).css("border-color","#cccccc");
               }
             });
           }
      
      if($("#voucher_series").val()==''  || selerror=='1'){
          alert_notification("Kindly fill the form properly!!!");
          return false;   
       }
       else if(item_checked.length==0 || $("#voucher_series").val()==''  || final_physical_stock_amouint=='' || final_physical_stock_amouint=='0'){
          alert_notification("Kindly fill the items data!!!");
          return false;   
       }
      // else if(sdatevalidate==0){
      //     alert_notification("voucher date is worng!!!");
      //     return false;    
      // } 
      else{
        $("#itmsdata").val(JSON.stringify(item_checked));
        show_loader();
        $("#salefrm").submit();
        }

});

    $(document).on('submit', '#salefrm', function(e){
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
                // show_loader();
                $('#submitbtn').attr('disabled', 'disabled');
                $('#validation_errors').html('');
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    alert_success(response.message);
                    window.history.back();
                }
                else{
                    stop_loader();
                    alert_notification(response.message);
                    if(response.errors)
                    {
                        var list = ``;
                        $.each(response.errors, function(index, value){
                            list += `<li>${value}</li>`;
                        });

                        var html = `
                            <div class="alert alert-danger alert-dismissible">
                              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              <ul>${list}</ul>
                            </div>
                        `;
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
    var itembalance ='';

     $(function () {
       function disableTextRenderer(ui) {
                grid = this,
                rowData = ui.rowData,
                rowIndx = ui.rowIndx,
                dataIndx = ui.dataIndx;
            if (grid.isEditableCell({ rowIndx: rowIndx, dataIndx: dataIndx }) == false) {
                grid.addClass({ rowIndx: rowIndx, dataIndx: dataIndx, cls: 'disabled' });
            }
            else {
                grid.removeClass({ rowIndx: rowIndx, dataIndx: dataIndx, cls: 'disabled' });
            }
        };
         
         var autoCompleteEditor = function (ui) {
        var $inp = ui.$cell.find("input");
        var element= {};

		$inp.autocomplete({
                source:  "<?php echo base_url();?>/admin/sales/ajax_company_items",
                selectItem:{ on: true }, 
                highlightText:{ on: true }, 
                minLength: 3,
                select: function(event, ui) {
              	  event.preventDefault();
				  var itemlabel =  ui.item.label;
				  var itemunts  =  ui.item.units;
				  var itmval    =  ui.item.value;
				  itemsobj.set(itemlabel,itmval);
				  if(itmval>0) {
				       var voucher_date = $("#voucher_date").val();
				
				 
				   var itm_book_stock_val = function () {
                    var tmp = null;
                    $.ajax({
                        'async': false,
                        'type': "GET",
                        'global': false,
                        'dataType': 'html',
                        'url': "<?php echo base_url();?>/admin/physical_verification/ajax_item_balance",
                        'data': { 'itmid':itmval, 'voucher_date': voucher_date },
                        'success': function (data) {
                            tmp = data;
                        }
                    });
                    return tmp;
                }();

			      if(itm_book_stock_val){
				        itemsobj_sock.set(itemlabel, itm_book_stock_val);
				        
				         $("#voucher_date").attr('readonly',true);
                         $("#voucher_date").attr('disabled',true); 
                         $("#voucher_date").removeClass('datepicker');
                         $("#voucher_date").removeClass('hasDatepicker');
                         $("#vchrdate").val($("#voucher_date").val());
                         $("#cleardate").html('<a href="javascript:void(0);" onclick="location.reload();">clear</a>');
				        
				        
				       }
				    }
				    $(this).val(ui.item.label);
			     }
		    }).focus(function () {
                //open the autocomplete upon focus               
                $(this).autocomplete("search", "");
             });
          }
        
        var colModel = [
                         {  title: "ITEM NAME", dataIndx: "item_name", width: 100,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		    type: "textbox",
                            init: autoCompleteEditor,
                            options: itemlist
                        },
                        validations: [
                         { 
                      type: function (ui) {
                       var value = ui.value;
                        if(typeof value === 'undefined'){
                            ui.msg = "item is not valid";
                         return false;
                        }
                        
                        }, icon: 'ui-icon-info'
                      }
                       ],
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
                { title: "UOM", dataIndx: "item_unit", width: 20,cls: 'pq-drop-icon pq-side-icon'
            },
               { title: "Physical Stock", align: "center", width: 100, dataIndx: "physical_stock",dataType: "float",format: '##,###.00'},
               { title: "Book Stock", align: "center", width: 100, dataIndx: "book_stock", dataType: "float",format: '##,###.00',formula: function (ui) {  
                    var rd = ui.rowData;
                    if(rd.item_name)
                      return itemsobj_sock.get(rd.item_name);
                    }
               },
               { title: "Diff", align: "center", dataIndx: "stock_diff", width: 100,dataType: "float",format: '##,###.00'
                   
               },
               { title: "Short Narration", width: 20, align: "center",dataIndx: "short_narration"}
	 	    ];
	 	    
        var dataModel = {"data":<?php echo json_encode($json_data);?>}
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel: colModel,  
            numberCell: { show: true },
            editable: false,
            wrap:false,
            cellSave: function(evt, ui){
                   this.refresh();
               },
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: true });
              }
        };
        var $grid = $("#grid_search").pqGrid(newObj);
    
        
     });
 </script>	
</body>
</html>
