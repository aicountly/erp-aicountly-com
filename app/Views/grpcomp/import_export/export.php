<?php $header = array( 	'title' => 'Data Import/Export' ); ?>
<?php echo view('includes/header',$header); ?>
  
<style>
    .gridtable .row{ display: grid; grid-template-columns:20% 20% 20% 20% 20%;}
    .gridtable .foot.row{ grid-template-columns:100% ;}
    .myform .col-sm-6{padding-bottom:2px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
    .myform .select2 {width:75%!important; }
</style>

<h3 class="pb-3">Data Export</h3>

<div class="col-md-6 myform pb-4">
    <div class="card p-4 mt-2">
    <p class="col-12"><label>Module</label>
    <select name="data_module" id="item_sales_acc" class="form-control w-75 selectwidget required">
    <option value="" selected="selected"></option>
    <option value="4">Select Module</option>
    </select> </p>

                     <p class="col-12"><label>Master</label>
                   <select name="data_master" id="item_sales_acc" class="form-control w-75 selectwidget required">
    <option value="" selected="selected"></option>
    <option value="4">Select Master</option>
    </select> </p>

                     <p class="col-12"><label>Sub Master</label>
                   <select name="data_submaster" id="item_sales_acc" class="form-control w-75 selectwidget required">
    <option value="" selected="selected"></option>
    <option value="4">Select Sub Master</option>
    </select> </p>

     <p class="col-12"><label>Download Format</label>
                   <select name="data_format" id="item_sales_acc" class="form-control w-75 selectwidget required">
    <option value="" selected="selected"></option>
    <option value="4">Select Format</option>
    </select> </p>

    <p class="col-12"><button class="btn btn-success">Export</button></p>
         </div>     
</div>    

<div class="col-md-12">
        <p class="mb-0"><b>View Recent Reqeust:</b></p>
         <div class="col-12 gridtable focus" style="overflow: auto; max-width:100%">
    <div class="row head selected">
     <div class="col">Module</div>
      <div class="col">Master</div>
     <div class="col">Sub Master</div>
     <div class="col">Requested on</div>
     <div class="col">Status</div>
  </div>
     <div class="row">
     <div class="col">&nbsp;</div>
     <div class="col">&nbsp;</div>
      <div class="col">&nbsp;</div>
     <div class="col">&nbsp;</div>
     <div class="col">&nbsp;</div>
  </div>
     <div class="row">
     <div class="col">&nbsp;</div>
     <div class="col">&nbsp;</div>
      <div class="col">&nbsp;</div>
     <div class="col">&nbsp;</div>
     <div class="col">&nbsp;</div>
  </div>
    </div>  
     
    
</div>
  
<?php echo view('includes/footer_scripts'); ?>
<script>
  
</script>
 </body>
</html>
