<?php $header = array( 	'title' => 'Reverse Jounal Voucher' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));

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

</style>
<style>
    /*for autocomplete inside bills grid*/
    .ui-autocomplete {
        z-index:9999!important;
    }
</style>


<div class="row pb-3">
    <div class="col-md-6 col-6"> 
        <h3>Reverse Jounal Voucher</h3> 
    </div>
    <div class="col-md-6 text-end">
        <div class="dropdown d-sm-flex d-block float-end">
            <div class="taskmenus">
                <a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history"><span class="material-symbols-outlined">visibility</span></a> 
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

    <div class="col-md-6">
        <input type="text" class="form-control d-inline" value="<?php echo $currency;?>" style="width:160px;" readonly>
    </div>  

    <div class="col-md-6 text-end">
       

        <button class="btn btn-sm btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
        </ul>
        <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success m-1">Back</a>
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
            <p class="text-center pt-3">
                <a data-bs-toggle="modal" data-bs-target="#moreoptionsmodal" class="btn btn-outline-success">View</a>
            </p>
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
</div>
<div id="validation_errors"></div>
<?php 
    $attributes = " id='form1' name='form1' class='needs-validation' novalidate";
    echo form_open(base_url().'/'.$folder_path.'vouchers/edit/'.$voucher_txn_id, $attributes);
?>      
    <?php echo $message_output->run() ;?>  
    
    <input type="hidden" name="voucherdata" id="voucherdata">
    <input type="hidden" name="bbbdata" id="bbbdata">
    <input type="hidden" name="ccdata" id="ccdata">

    <div class="col-md-12">
	<div class="row mx-0 p-0 mb-2">
    <div class="row m-0 p-0">
	<div class="col-md-3 col-6 card p-2">
	   <div class="input-group">
        <label class="input-group-text">Date: </label>
        <input type="text" name="voucher_date" id="voucher_date" class="form-control" value="<?php echo $voucher_date;?>" readonly>
        </div>
	  </div>
    <div class="col-md-3 col-6 card p-2">
	  <div class="input-group">
                <label class="input-group-text">Voucher Series: </label>
                <input type="text" name="voucher_series" id="voucher_series" class="form-control" value="<?php echo $voucher_series_dropdown[$voucher_series];?>" readonly>
            </div>
	  </div>
  <div class="col-md-3 col-6 card p-2">
	  <div class="input-group">
                <label class="input-group-text">Voucher No: </label>
				<input type="text" name="voucher_no" class="form-control" value="<?php echo $voucher_no;?>" readonly>
                
            </div>
	  </div>
  <div class="col-md-3 col-6 card p-2">
	  <div class="input-group">
                <label class="input-group-text">GST Nature: </label>
                <select class="form-control"><option>Choose</option></select>
            </div>
	  </div> 
        
    </div>
    <?php if($reversal_date != ''): ?>
    <div class="col-md-3 col-6 card p-2">
       <div class="input-group">
        <label class="input-group-text">Reversal Date: </label>
        <input type="text" name="reversal_date" id="reversal_date" class="form-control" value="<?php echo $reversal_date;?>" readonly>
        </div>
    </div>
    <?php endif; ?>
     <div class="col-md-12 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Narration: </label>
                <textarea  name="narration" class="form-control" readonly><?php echo $narration ?></textarea>
            </div>
        </div>
 </div>
 <div id="grid_search" style="margin:auto;"></div>  
    </div>
 

   
 
 <div class="col-12 text-center pt-2">
        
        <a href="javascript:main(0)"  onclick="window.history.go(-1); return false;" class="btn btn-lg btn-secondary mx-2">QUIT</a>
        <a href="javascript:main(0)"  class="deletebtn btn btn-lg btn-danger mx-2">Delete</a>
 </div>
</form>
 


<?php echo view('includes/footer_scripts'); 

     
$json_data = $account_transactions;

for($i=1;$i<=20;$i++)
 $json_data[] = array("drcr"=>'','account_name'=>'','description'=>'','debit'=>'','credit'=>'','account_id'=>'','is_bbb'=>'','is_cc'=>'','is_cash'=>'');

?>
<script>

var voucher_type_id = <?= $voucher_type_id ?>;
  
$(".deletebtn").on('click',function(){
   confirm_delete(baseurl+"/admin/vouchers/delete_reverse_journal/<?php echo $voucher_txn_id;?>");		    
   return false
})

 
 $(function () {
         
        function calculateSummary() {

            var debitTotal = 0,
                creditTotal = 0,
                data = this.option('dataModel.data'),
                len  = data.length;

            data.forEach(function(row){

                if(row.account_id != '' && row.account_name != '')
                {
                    if(row.debit == '' || typeof row.debit == 'undefined'){
                        var debit =0;
                    }else
                     var debit =  row.debit;
                    
                    if(row.credit == '' || typeof row.credit == 'undefined'){
                        var credit =0;
                    }else
                    var  credit =  row.credit;
                 
                     
                    debitTotal  += parseAmount(debit);
                    creditTotal += parseAmount(credit);  
                }
            })

            var totalData = {
                    drcr      : "Total",
                    debit     : formatAmount(debitTotal),
                    credit    : formatAmount(creditTotal),
                    pq_rowcls : 'grid_footer_color',
                    summaryRow: true
                }
                
            this.option('summaryData', [totalData]);
        }
          
      
         
         

        
        var colModel = [
        { title: "DR/CR", dataIndx: "drcr",cls: 'pq-drop-icon pq-side-icon'
        },
        { title: "ACCOUNT", dataIndx: "account_name", cls: 'pq-drop-icon pq-side-icon' 
        },
        
        { title: "SHORT NARRATION",  dataType: "string", dataIndx: "description"
        },
        { title: "DEBIT", width: 20, align: "left",dataIndx: "debit",dataType: "float",
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.debit != ''){
                    rd.debit = parseAmount(rd.debit);
                    return formatAmount(rd.debit);   
                }
                return ''; 
            }   
        },
        { title: "CREDIT", width: 20, align: "left",dataIndx: "credit",dataType: "float",
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.credit != ''){
                    rd.credit = parseAmount(rd.credit);
                    return formatAmount(rd.credit);   
                }
                return ''; 
            }
         }
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
            change: calculateSummary, 
            dataReady: calculateSummary,
            numberCell: { show: true },
            editable: false,
            cellSave: function(evt, ui){
                   this.refresh();
               },
            wrap:false,
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
//$('#voucher_date').focus();
$("#voucher_date").prop("selectedIndex", 1);


</script>