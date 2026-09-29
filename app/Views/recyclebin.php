<?php $header = array( 	'title' => 'Recycle Bin' ); ?>
<?php echo view('includes/header2',$header); ?>

    <div class="row mb-4 myalltabs px-3 bg bg-success">
      <div class="col-12" id="tabslistings" style="display:inline-block;width:auto;">
          <a href="<?php echo base_url();?>companies" ><span id="#" class="btn tabslist">All Companies</span></a>
		<a href="<?php echo base_url();?>my_companies" ><span id="#" class="btn tabslist">My Company</span></a>
		<a href="<?php echo base_url();?>sharedwithme" ><span id="#" class="btn tabslist">Shared With Me</span></a>
		   </div>
    </div>

    <div class="content"><div class="pb-5">
      <div class="listmenu pb-4" id="listmenu">  

    <a href="javascript:void(0);" class="btn btn-success rstrcomptbtn">Restore Company</a>
    <a href="javascript:void(0);" class="btn btn-success delprmntbtn">Delete Permanently</a>
  </div>
  <div id="open_company_search"> </div> 
  <?php 
   $json_company = array();
   if($company_list){ 
      foreach($company_list as $company_row){
        $json_company[] = array("encomp_id"=>obfuscate_link($company_row['company_id']),"id"=>$company_row['company_id'],"company_id"=>$company_row['company_id'],"companyname"=>$company_row['companyname'],
		                        "companycode"=>$company_row['companycode'],"finyear"=>$company_row['finyear'],
                                "deletion_date"=>$company_row['recycled_date']
								);
         }
     } 
    $json_company = json_encode($json_company);
  ?>
    </div>
    


   <?php echo view('includes/footer_scripts'); ?>		  
	
 <script>

     $(function () {
        
        function filterhandler(evt, ui) {

            var $toolbar = $grid.find('.pq-toolbar-search'),
                $value = $toolbar.find(".filterValue"),
                value = $value.val(),
                condition = $toolbar.find(".filterCondition").val(),
                dataIndx = $toolbar.find(".filterColumn").val(),
                filterObject;

            if (dataIndx == "") {//search through all fields when no field selected.
                filterObject = [];
                var CM = $grid.pqGrid("getColModel");
                for (var i = 0, len = CM.length; i < len; i++) {
                    var dataIndx = CM[i].dataIndx;
                    filterObject.push({ dataIndx: dataIndx, condition: condition, value: value });
                }
            }
            else {//search through selected field.
                filterObject = [{ dataIndx: dataIndx, condition: condition, value: value}];
            }
            $grid.pqGrid("filter", {
                oper: 'replace',
                data: filterObject
            });
        }
        
        var colModel = [
            { title: "COMPANY CODE",  width: 100, dataIndx: "companycode"},
            { title: "COMPANY NAME",  width: 180, dataIndx: "companyname"},
            { title: "FIN. YEAR(S)",  width: 140, dataIndx: "finyear"},
            { title: "DELETION DATE", width: 100, dataIndx: "deletion_date"}
	    	];
        var dataModel = {"data":<?php echo $json_company;?>}
        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel : colModel,
            filterModel: { mode: 'OR' },
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
            },
            toolbar: {
                cls: "pq-toolbar-search",
                items: [ 
            
                     { 
                        type: 'textbox', 
                        label: 'Filter: ',
                        attr: 'placeholder="Enter your keyword"', 
                        cls: "filterValue",
                        listener: { keyup: filterhandler }
                     },
                     { 
                        type: 'select', cls: "filterColumn",
                        listener: filterhandler,
                        options: function (ui) {
                            var CM = ui.colModel;
                            var opts = [{ '': '[ All Fields ]'}];
                            for (var i = 0; i < CM.length; i++) {
                                var column = CM[i];
                                var obj = {};
                                obj[column.dataIndx] = column.title;
                                opts.push(obj);
                      }
                            return opts;
                        }
                    },
                    { 
                        type: 'select',                         
                        cls: "filterCondition",
                        listener: filterhandler,
                        options: [
                            { "begin": "Begins With" },
                            { "contain": "Contains" },
                            { "end": "Ends With" },
                            { "notcontain": "Does not contain" },
                            { "equal": "Equal To" },
                            { "notequal": "Not Equal To" },
                            { "empty": "Empty" },
                            { "notempty": "Not Empty" },
                            { "less": "Less Than" },
                            { "great": "Great Than" },
                            { "regexp": "Regex" }
                        ]
                    }
                ]
            }
        };
        
      
        var $grid = $("#open_company_search").pqGrid(newObj);
        
       
           
           $(".rstrcomptbtn").on("click",function(){
         
           var selectionArray = $("#open_company_search").pqGrid("selection", {type: 'row', method: 'getSelection'});
           var rowData = selectionArray[0]['rowData'];
           var enc_company_id = rowData.encomp_id;
           
           window.location.href= baseurl+'home/restore_company/'+enc_company_id;
           
           
           })
         
         $(".delprmntbtn").on("click",function(){
              var selectionArray = $("#open_company_search").pqGrid("selection", {type: 'row', method: 'getSelection'});
           var rowData = selectionArray[0]['rowData'];
           var enc_company_id = rowData.encomp_id;
             
            confirm_delete( baseurl+'home/finally_remove_comp/'+enc_company_id);
             
         });
      
    });
</script> 