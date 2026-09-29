<?php $header = array('title' => 'List of '. $voucher_name .' Vouchers' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>
 <h3 class="pb-3"><?php echo 'List of '. $voucher_name .' Vouchers';?></h3>
 <?php if($voucher_trans){ ?>
  <a href="javascript:void(0);" class="deletebtn btn btn-success">Delete</a>  
  <?php } ?>
  <br><br>
 
      <div id="grid_checkbox" style="margin:auto;"> </div>  
  	<?php
	$cc_counter=0;
	$vouchers_list = array();
	if($voucher_trans){ 
    foreach($voucher_trans as $row){ 
	$cc_counter++;  
	
	$vouchers_list[]=array("voucher_txn_id"=>$row['voucher_txn_id'],"voucher_date"=>$row['txn_date'],'account_name'=>ucwords($row['account_name']),'debit'=>number_format($row['debit'],2),'credit'=>number_format($row['credit'],2),"Discontinued"=> "NO");
	
	  }
	}
	$json_data =  json_encode($vouchers_list);
	?>

<?php echo view('includes/footer_scripts');?>	

<script>

 $(function () {
        
        var obj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single', column: true },
            pageModel: { type: "local", rPP: 10 },
             editable: true,
             create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
            },
            colModel: [{
                colModel: [
                     {
                        //custom title.    
                        title: "<label><input type='checkbox'/>Select All </label>",
                        dataIndx: "Discontinued",
                        maxWidth: 120,
                        minWidth: 120,
                        type: 'checkbox', //required property.
                        cb: {
                            all: false, //header checkbox to affect checkboxes on all pages.
                            header: true, //for header checkbox.
                            check: "YES", //check the checkbox when cell value is "YES".
                            uncheck: "NO" //uncheck when "NO".
                        },
                        //renderLabel is optional.
                        renderLabel: function (ui) {                            
                            var cb = ui.column.cb,
                                cellData = ui.cellData,                                
                                disabled = this.isEditableCell(ui) ? "" : "disabled",
                                text = cb.check === cellData ? 'TRUE' : (cb.uncheck === cellData ? 'FALSE' : '<i>unknown</i>');
                            return text;                                                           
                        },
                        editor: true, //cell renderer i.e., checkbox serves as editor, so no separate editor.
                        editable: function (ui) {
                            //to make checkboxes editable selectively.
                            return !ui.rowData.disabled;
                        },
                        useLabel: true
                    },
                    
                  
                     { title: "DATE", dataIndx: "voucher_date", width: 100,},
                    { 
                        title: "ACCOUNT", 
                        width: 220,                         
                        dataIndx: "account_name"
                      
                    },
                    //column to store checkbox state of Voucher ID.
                    {
                        dataIndx: 'chk',
                        dataType: 'bool',
                        cb: { header: true },
                        hidden: true
                        
                    },
                    { title: "DEBIT(RS.)", width: 180, dataIndx: "debit" },
                     { title: "CREDIT(RS.)", width: 180, dataIndx: "credit" },
                   
                ]
            }],
            dataModel: {
                data: <?php echo $json_data;?>
            }
        };
        
        
         obj.rowDblClick    = function(event, ui) {
  	     	var rowData      = ui.rowData;
		    var voucher_txn_id     = rowData.voucher_txn_id;
		    window.location.href= baseurl+'/admin/vouchers/edit_voucher_transactions/'+voucher_txn_id+"/"+<?php echo $voucher_type_id;?>;
	     } 
	     
	    obj.cellKeyDown= function(evt, ui) {
	        var rowData          = ui.rowData;
		    var voucher_txn_id   = rowData.voucher_txn_id;
		    window.location.href = baseurl+'/admin/vouchers/edit_voucher_transactions/'+voucher_txn_id+"/"+<?php echo $voucher_type_id;?>;
		   
	       }  
	       
        $("#grid_checkbox").pqGrid(obj);
        
       
        
    });    
  
</script>

<script>

 $(".deletebtn").on('click',function(){
      var item_checked     = [];
     var data             = $("#grid_checkbox").pqGrid('option', 'dataModel.data');
      for (var i = 0; i < data.length; i++) {
          var ischecked =  data[i]['Discontinued'];
          var vchrxn_ids = data[i]['voucher_txn_id'];
          if(ischecked=='YES'){
               item_checked.push(vchrxn_ids);
              
          }
          
      }
      
      if(item_checked.length==-0){
        alert_notification("First select a voucher to delete!!");  
          
      }
      else{
       confirm_delete(baseurl+"/admin/vouchers/remove_voucher/"+<?php echo $voucher_type_id;?>+"/"+item_checked.join(","));		    
      }
    return false
    }) 
  </script>    