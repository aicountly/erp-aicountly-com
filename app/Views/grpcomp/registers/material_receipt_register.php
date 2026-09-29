<?php $header = array('title' => 'Material Receipt Register');?>
<?php echo view('includes/header',$header); ?>

<div class="row mb-md-0 mb-3">
    <div class="col-md-6"><h3 class="pb-3">Material Receipt Register</h3></div>
    <div class="col-md-6 text-end"><div class="taskmenus">
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a>
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
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
       
    </div>
    </div>
</div>
<div class="row mb-2 align-items-top mb-3">
    <div class="col-lg-5">
        <form class="form needs-validation" method="get" id="salefrm" autocomplete="off" novalidate>
        <div class="input-group">
            <span class="input-group-text px-1">From</span>
            <input type="text" name="fromdate" value="<?php echo $from_date;?>" class="datepicker form-control p-2" required  style="width:90px;">
            
            <span class="input-group-text px-1">To</span>
            <input type="text" name="todate" value="<?php echo $to_date;?>" class="datepicker form-control p-2" required style="width:90px;">
            
            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#calendarmodal">
             <span class="material-symbols-outlined">event</span>
            </button>
            
            <input type="submit" class="btn btn-sm btn-success" value="GO">
        </div>
        </form>
    </div>
    <div class="col-lg-7 text-end">
	<div class="dropdown float-end">
  <div class="dropdown d-inline-block me-1" style="width:220px;"> 
          <div class="input-group input-group-sm input-group">
             <span class="input-group-text">View</span>
			 <?php 
			   $view_type_dropdown = array("material_receipt_register"=>"Material Receipt Register","inward_challan_due"=>"Inward Challan Due");
			   echo form_dropdown("view_type",$view_type_dropdown,'material_receipt_register','id="view_type" class="form-select" ' );
			 ?>
           </div> 
		</div>
		  
            <button class="btn btn-sm btn-success mt-0 m-1" type="button"> Column </button>
           
            <button class="btn btn-sm btn-success dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Voucher Series </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Action</a></li>
                <li><a class="dropdown-item" href="#">Another action</a></li>
                <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
             <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">Back</a>
 </div><div class="dropdown float-end" style="width:180px;">
          <div class="input-group">
              <span class="input-group-text">Branch</span>
             <?php              
					echo form_dropdown('bo_id',$bo_dropdown,$ses_boid,' id="bo_id" form="salefrm" onchange="this.form.submit()" class="form-select"');
					?>
          </div>  
        </div>
        
    </div>
</div>
<div class="modal fade modal-lg" id="calendarmodal" tabindex="-1" aria-labelledby="calendarmodallabel" style="display: none;" aria-hidden="true">
     <div class="modal-dialog">
        <div class="modal-content">
  
            <div class="col-12 calccard card m-auto">
                <form class="form p-0" method="get" id="salefrm2" autocomplete="off">
                <div class="row p-4">
        <?php $fy_bgn_yr = date('Y',strtotime(session()->get('ses_company_fy_beginning'))); ?>
        <div class="calc col-md-6">
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
                <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:70%; text-align: center; display: block;">FY: <?= $fy_bgn_yr ?> - <?= ($fy_bgn_yr+1) ?></button>
                <button type="button" class="input-group-text" id="next_year"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">From</div>
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control" fdprocessedid="cw7ydk" name="fromdate" id="fromdate" value="<?= $from_date ?>" placeholder="dd-mm-yyyy" required>
                    </div>
                </div>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">To</div>
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control"  fdprocessedid="b20o4z" name="todate" id="todate" value="<?= $to_date ?>" placeholder="dd-mm-yyyy" required>
                    </div>
                </div>
            </div>
            <p class="text-end"><button type="button" class="input-group-text fw-bold ms-auto"  id="tilldate" required>TILL DATE</button></p>
        
        </div> 
        
        <p class="text-center pt-4">
            <button type="submit" class="btn btn-lg btn-success">GO</button>
            <button type="button" class="btn btn-lg btn-secondary" data-bs-dismiss="modal">Quit</button>
        </p>  
        
    </div>
                 </form>
            </div> 
        </div>
    </div>
</div>



<!-- Modal -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="moreoptions" aria-labelledby="moreoptionslabel" aria-modal="true" role="dialog">
      <div class="offcanvas-header">
        <h5 class="modal-title" id="moreoptsmodalLabel">App Options title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="list-group list-group-flush">
  <li class="list-group-item"><input type="checkbox"  class="form-check-input"> An item</li>
  <li class="list-group-item"><input type="checkbox"  class="form-check-input"> A second item</li>
  <li class="list-group-item"><input type="checkbox"  class="form-check-input"> A third item</li>
  <li class="list-group-item"><input type="checkbox"  class="form-check-input"> A fourth item</li>
  <li class="list-group-item"><input type="checkbox"  class="form-check-input"> And a fifth one</li>
</ul>

      <div class="col-md-12 pt-2">
         <p class="fw-bold px-2"><label class="starcheck" style="top:-3px;"><input type="checkbox" checked="checked"> <b class="checkmark">★</b></label> Set Favourite</p>
        <button type="button" class="btn btn-secondary">Load Default</button>
        <button type="button" class="btn btn-success">Save changes</button>
      </div></div>
</div>

<div id="grid_search" style="margin:auto;"></div>



<?php echo view('includes/footer_scripts');
$json_data=array();
?>
<style>
 .boldcell{font-weight:700;}
</style>
<script>
$("#view_type").on("change",function(){	
	var viewtype= $(this).val();
	if(viewtype=='material_receipt_register')
		window.location.href= baseurl+'/admin/registerlog/material_receipt_register';
	else if(viewtype=='inward_challan_due')
	 window.location.href = baseurl+'/admin/registerlog/inward_challan_due_register';	
  });
  
function setdate(){
    $( "#todate" ).val('<?php echo date("d-m-Y");?>');
     $( "#fromdate" ).val('<?php echo date("d-m-Y");?>');
 return false;   
}


$("#todate").datepicker({
            showOn: 'button',
            buttonImageOnly: true,
            buttonImage: '<?php echo base_url();?>/public/assets/images/caldender-icon.png',
            dateFormat: 'dd-mm-yy'
        });    
$("#fromdate").datepicker({
            showOn: 'button',
            buttonImageOnly: true,
            buttonImage: '<?php echo base_url();?>/public/assets/images/caldender-icon.png',
            dateFormat: 'dd-mm-yy'
        });     
</script>
<script>
    var itemlist  = '';  
     $(function () {
          
     
       function disableTextRenderer(ui) {
                grid = this,
                rowData  = ui.rowData,
                rowIndx  = ui.rowIndx,
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
		
        //initialize the editor
        $inp.autocomplete({
                source: itemlist,
                selectItem: { on: true }, //custom option
                highlightText: { on: false }, //custom option
                minLength: 0,
                select: function(event, ui) {
					event.preventDefault();
					$(this).val(ui.item.label);
				}
            }).focus(function () {
                //open the autocomplete upon focus               
                $(this).autocomplete("search", "");
            });
           
        }
     
        var colModel = [
                     { title: "DATE", dataIndx: "voucher_date", align: "center", width: 100,cls: 'pq-drop-icon pq-side-icon'},
                     { title: "VOUCHER BILL NO", width: 100, align: "center", dataIndx: "comp_vch_no"},
                     { title: "ACCOUNT", dataIndx: "account_name", align: "center", width: 100},
                     { title: "ITEM", width: 20, align: "center",dataIndx: "item_name"},
                     { title: "MATERIAL CENTRE", width: 20, align: "center", dataIndx: "material_centre"},
                     { title: "QTY RECEIVED", width: 20, align: "center", dataIndx: "quantity"},
                     { title: "UNIT", width: 20, align: "center", dataIndx: "item_unit"},
		             { title: "VALUE", width: 20, align: "right",dataIndx: "amount"},
		             { title: "SHORT NARRATION", align: "center", width: 20, dataIndx: "short_narration"},
	 	    ];
	 	    
        var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
            postData:{from_date:"<?php echo $from_date;?>",to_date:"<?php echo $to_date;?>"},
            url: "<?php echo base_url();?>/admin/registerlog/ajax_material_receipt_register", 
             getData: function (dataJSON) {
                var data = dataJSON.data;
                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              }
           };
        
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel: colModel,  
            numberCell: { show: false },
            editable: false,
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            showTitle: true,
            load:function(event,ui) {
               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
            },
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: true });
                }
            };
        
          newObj.rowDblClick    = function(event, ui) {
            var rowData      = ui.rowData;
            var voucher_txn_id     = rowData.voucher_txn_id;
            var voucher_type_id     = rowData.voucher_type_id;
            window.location.href= baseurl+'/admin/inward_challan/edit/'+voucher_txn_id;
         } 
         
        newObj.cellKeyDown= function(evt, ui) {
            var rowData          = ui.rowData;
            var voucher_txn_id   = rowData.voucher_txn_id;
            if (evt.keyCode==13)
              window.location.href= baseurl+'/admin/inward_challan/edit/'+voucher_txn_id;
           }
	       
        var $grid = $("#grid_search").pqGrid(newObj);
        $("#grid_search").pqGrid('loadState'); 
         $(window).unload( function(){
         $("#grid_search").pqGrid('saveState');
       });
     });
 </script>	
</body>
</html>
