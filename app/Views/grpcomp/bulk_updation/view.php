<?php $header = array( 	'title' => 'Data Import/Export' ); ?>
<?php echo view('includes/header',$header); ?>
  
<style>
    .gridtable .row{ display: grid; grid-template-columns:20% 20% 20% 20% 20%;}
    .gridtable .foot.row{ grid-template-columns:100% ;}
    .list-inline a{color:#000;}
    .list-inline a.active{color:#25b003;}
    .acesstabs{display:flex; position:relative; justify-content: space-around;}
    .acesstabs::after{height:2px; width:100%; background:#1d528c; position:absolute; top:30px; content:'';}
    .acesstabs a{width:60px; height:60px; background:#1d528c; font-weight:bold; z-index:1; color:#fff; border-radius:50%; text-align:center; line-height:60px; font-size:32px; } 
    .acesstabs a.active{ background:#25b003; color:#fff;}

    .myform .col-sm-6{padding-bottom:2px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
    .myform .select2 {width:75%!important; }
</style>
        
<h3 class="pb-3">Bulk Updation</h3>

<div class="col-12">

   <p class="acesstabs" id="myTab" role="tablist">
    <a class="active" id="acess1-tab" data-bs-toggle="tab" data-bs-target="#acess1-panel" type="button" role="tab" aria-controls="acess1-panel" aria-selected="true">1</a>
    <a class="" id="acess2-tab" data-bs-toggle="tab" data-bs-target="#acess2-panel" type="button" role="tab" aria-controls="acess2-panel" aria-selected="false" tabindex="-1">2</a>
    <a class="" id="acess3-tab" data-bs-toggle="tab" data-bs-target="#acess3-panel" type="button" role="tab" aria-controls="acess3-panel" aria-selected="false" tabindex="-1">3</a>
    <a class="" id="acess4-tab" data-bs-toggle="tab" data-bs-target="#acess4-panel" type="button" role="tab" aria-controls="acess4-panel" aria-selected="false" tabindex="-1">4</a>
    <a class="" id="acess5-tab" data-bs-toggle="tab" data-bs-target="#acess5-panel" type="button" role="tab" aria-controls="acess5-panel" aria-selected="false" tabindex="-1">5</a>
  </p>

<div class="tab-content border-0 accordion form-outline mb-4" id="myTabContent">

  
   <!-- Access Tab 1 Starts here ---->

          <div class="tab-pane border-0 fade accordion-item active show" id="acess1-panel" role="tabpanel" aria-labelledby="acess1-tab" tabindex="0">
<div class="card p-4 mt-2">
    <h4>Add Module</h4>
<div class="col-md-6 myform pt-4">
                 <p class="col-12"><label>Module</label>
               <select name="data_module" id="item_sales_acc" class="form-select w-75 selectwidget required">
<option value="" selected="selected"></option>
<option value="Account">Account</option> <option value="Stock">Stock</option>
</select> </p>

                 <p class="col-12"><label>Master</label>
               <select name="data_master" id="item_sales_acc" class="form-select w-75 selectwidget required">
<option value="" selected="selected"></option>
<option value="Item">Item</option> <option value="Item Group">Item Group</option>  <option value="Stock Category">Stock Category</option>
<option value="Material Center">Material Center</option> <option value="Bar Code">Bar Code</option>  <option value="Label">Label</option>
</select> </p>

                 <p class="col-12"><label>Sub Master</label>
               <select name="data_submaster" id="item_sales_acc" class="form-select w-75 selectwidget required">
<option value="" selected="selected"></option>
<option value="Item Master">Item Master</option> <option value="Item Group Master">Item Group Master</option> <option value="Stock Category Master">Stock Category Master</option>
<option value="MC Master">MC Master</option> <option value="Item bar Code Label">Item bar Code Label</option>
</select> </p>
</div>
<p> <button class="btn btn-outline-success m-2">Download Master</button> <a href="#" class="btn btn-success float-end btnNext m-2">Skip Next »</a></p>



    <div class="col-md-12 pt-4">
        <p class="mb-0"><b>View Recent Reqeust:</b></p>
         <div class="col-12 gridtable" style="overflow: auto; max-width:100%">
    <div class="row head">
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
              


     </div>
         </div>

<!-- Access Tab 1 Ends here ---> 
  
   

 
    <!-- Access 2 Tab -->
  <div class="tab-pane border-0 fade accordion-item" id="acess2-panel" role="tabpanel" aria-labelledby="acess2-tab" tabindex="0">
 <div class="card p-4 mt-2">
    <h4>Upload File</h4>
<div class="col-md-6 myform pt-4">
                 <p class="col-12"><label>File Upload</label>
               <input type="file" class="form-control w-75">
               </p>


  <p class="text-center"><a href="#" class="btn btn-success m-2 btnPrevious">« Back</a> <a href="#" class="btn btn-success btnNext m-2">Skip Next »</a></p>  
</div>
   
 </div>
       
      
  </div>
  <!-- Access Tab 2 Ends here --->
  
  
  
  <!-- Access Tab 3 Starts here ------>
  <div class="tab-pane border-0 fade accordion-item" id="acess3-panel" role="tabpanel" aria-labelledby="acess3-tab" tabindex="0">
 <div class="card p-4 mt-2">
<div class="row">
    <div class="col-md-6">
        <h4>Errors</h4>
        <ul class="list-group list-group-flush" style="height:380px; overflow-y:auto">
        <li class="list-group-item">Error 1 goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">2nd Error goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">2nd Error goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">2nd Error goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">Error 1 goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">2nd Error goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">2nd Error goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">2nd Error goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        </ul>
    </div>
    
    <div class="col-md-6">
        <h4>Warning / Information</h4>
        <ul class="list-group list-group-flush" style="height:380px; overflow-y:auto">
        <li class="list-group-item">Error 1 goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">2nd Error goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">2nd Error goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">2nd Error goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">Error 1 goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">2nd Error goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">2nd Error goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        <li class="list-group-item">2nd Error goes here : aS dsadsfewd d dfv c ewfvxc vrd gvxc gergbvc fvdxergfvfxc gfvregvxc fgerfg vcxf re gvcx gfd</li>
        </ul>
    </div>
    
         <p class="text-center pt-4"><a href="#" class="btn btn-success m-2 btnPrevious">« Back</a> <a href="#" class="btn btn-success btnNext m-2">Preview »</a></p>    
</div>

     </div>
     
     </div>
<!-- Access Tab 3 Ends here --------->
  
  
  
  <!-- Access Tab 4 Starts here ------>
  <div class="tab-pane border-0 fade accordion-item" id="acess4-panel" role="tabpanel" aria-labelledby="acess4-tab" tabindex="0">
 <div class="card p-4 mt-2">
              <h4>Data Table</h4>
      <div class="col-12 my-5" style="max-width:96%;">
    <div class="row head h5 border-bottom mb-2 pb-2">
     <div class="col">Compnay Code</div>
     <div class="col">Name</div>
     <div class="col">Ownered by</div>
  </div>
   <div class="row border-bottom py-2 selected">
     <div class="col">AI 0001</div>
     <div class="col">Manpreet Saini</div>
     <div class="col">Harry Peamber - A little description goes here</div>
  </div>
   <div class="row border-bottom py-2">
     <div class="col">CA 100203 </div>
     <div class="col">Manpreet Saini</div>
      <div class="col">Harry Peamber - A little description goes here</div>
  </div>
   <div class="row border-bottom py-2">
     <div class="col">PROFS 00212</div>
     <div class="col"> Manpreet Saini</div>
     <div class="col">Harry Peamber - A little description goes here</div>
  </div>

    </div> 
    
     <p class="text-center"><a href="#" class="btn btn-success m-2 btnPrevious">« Back</a> <a href="#" class="btn btn-success btnNext m-2">Confirm Updation »</a></p>  
     </div>
     
     
     </div>


<!-- Access Tab 5 Starts here ------>
  <div class="tab-pane border-0 fade accordion-item" id="acess5-panel" role="tabpanel" aria-labelledby="acess5-tab" tabindex="0">

 <div class="card p-4 mt-2">
     <div class="m-5 p-5 text-center m-auto">
         <h2>Bulk Updation Successfull</h2>
         <p class="py-4">
         Note : Any error or notes  regarding bulk updation goes here
         </p>
         <a href="#" class="btn btn-success btn-lg">Go To Dashboard</a>
     </div>
     </div>
     
     
     </div>
<!-- Access Tab 5 Ends here --------->


   
  <!-- Manage Access Modal Ends here --->  

          
          
        </div>
      </div>
  
<?php echo view('includes/footer_scripts'); ?>
<script>
$(document).ready(function(){
    $('.btnNext').click(function() {
        const nextTabLinkEl = $('.acesstabs .active').closest('a').next('a')[0];
        const nextTab = new bootstrap.Tab(nextTabLinkEl);
        nextTab.show();
    });

    $('.btnPrevious').click(function() {
        const prevTabLinkEl = $('.acesstabs .active').closest('a').prev('a')[0];
        const prevTab = new bootstrap.Tab(prevTabLinkEl);
        prevTab.show();
    });

});
</script>
 </body>
</html>
