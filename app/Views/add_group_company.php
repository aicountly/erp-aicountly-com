<?php $header = array(  'title' => 'Add Group Company' ); ?>
<?php echo view('includes/header2',$header); ?>
<style>
.myform .col-sm-12{padding-bottom:10px;}
.myform label{width:25%; float:left;}
.myform .form-control, .myform select {width:75%;}
</style>
<div class="row mb-4 myalltabs px-3 bg bg-success">
  <div class="col-12" id="tabslistings" style="display:inline-block;width:auto;">
    <a href="<?php echo base_url();?>/home/open_company" >
      <span id="#" class="btn tabslist">My Company</span>
    </a>
    <a href="<?php echo base_url();?>/sharedwithme" >
      <span id="#" class="btn tabslist">Shared With Me</span>
    </a>
    <a href="<?php echo base_url();?>/archivecompany" >
      <span id="#" class="btn tabslist">Archive Company</span>
    </a>
    <a href="<?php echo base_url();?>/groupcompany" >
      <span id="#" class="btn tabslist active">Group Company</span>
    </a>
  </div>
</div>
<div class="content">
  <div class="pb-5">
    <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
    echo form_open(base_url().'/groupcompany/add', $attributes);
    ?>
    <div class="row">
      <div class="col-6">
        <h3 class="pb-3">New Company Group</h3>
      </div>  
      <div class="col-6">
      <span class="float-end">
      </span>
    </div>
    <div class="col-md-6">
      <div class="row m-0">
        <div class="card col-12 pt-3 my-3">
          <p>
            <label>Company Name</label> <?php $data = array(
            'name'        => 'group_name',
            'id'          => 'group_name',
            'value'       => set_value('group_name'),
            'maxlength'   => '255',
            'class'       => 'form-control',
            'required'    => true
            );
            echo form_input($data);
            ?>
          </p>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="row m-0">
        <div class="card col-12 pt-3 my-3">
          <p>
            <label>Alias Name</label> <?php $data = array(
            'name'        => 'group_alias',
            'id'          => 'group_alias',
            'value'       => set_value('group_alias'),
            'maxlength'   => '100',
            'class'       => 'form-control',
            'required'    => true
            );
            echo form_input($data);
            ?>
          </p>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-12">
    <div id="grid_search" style="margin:auto;"> </div>
    <input type="hidden" name="groupdata" id="groupdata">
    <br>
    <br>
  </div>
  <div class="col-sm-12 text-center">
    <input id="submitbtn" type="button" value="SAVE" class="btn btn-primary mx-2">
    <a href="<?php echo base_url();?>/home/open_company" class="btn btn-secondary mx-2" >QUIT</a>
  </div>
</div> 
</form>

<?php echo view('includes/footer_scripts');
for($i=1;$i<=20;$i++)
$json_data[] =array("encomp_id"=>'',"comp_name"=>'','comp_code'=>'','bgn_date' => '','end_date' => '','fy_id' => '','fy_list' => []);
?>

<script>

 $(function () {

   $(document).on('blur','[name="group_name"]', function(){
      var name = $(this).val().trim();
      if(name){
       $('[name="group_alias"]').val(name); 
     }
   });
   $(document).on('change','[name="group_name"]', function(){
    var name = $(this).val().trim();
    if(name){
     $('[name="group_alias"]').val(name); 
    }
   });
   

  $("#submitbtn").on("click",function(){

    var group_name  = $("#group_name").val();
    var group_alias = $("#group_alias").val();

    var final = [];
    var data = $("#grid_search").pqGrid('option', 'dataModel.data');

    var minDate = [];
    var maxDate = [];

    data.forEach(function(rd){
      if(rd.encomp_id)
      {
        if(rd.bgn_date && rd.end_date)
        {
          minDate.push(rd.bgn_date);
          maxDate.push(rd.end_date);

          final.push({
            'encomp_id' : rd.encomp_id,
            'comp_fy_id': rd.fy_id,
            'bgn_date'  : rd.bgn_date,
            'end_date'  : rd.end_date,
          });
        }
      }
    });

    var error = 1;

    if(final.length >= 1){
      var max2 = maxDate.reduce(function(a,b){ return a < b ? a: b;});
      var min2 = minDate.reduce(function(a,b){ return a > b ? a: b;});
      var days2 = countDays(min2, max2);
      if(days2 > 0)
        error = 0;
    }

    if($("#group_name").val()=='' || $("#group_alias").val()=='' ){
      alert_notification("Kindly fill the data!!!");
      return false;   
    }
    else if(final.length < 1){
      alert_notification("Kindly fill the companies!!!");
      return false;
    }
    else if(error){
      alert_notification("No common days between companies!!!");
      return false;
    }
    else{        
      show_loader();
      $("#groupdata").val(JSON.stringify(final));
      $("#myform").submit();
    }  

  });


   function countDays(minDate, maxDate)
   {
      var arr1 = minDate.split('-');
      var min = new Date(arr1[0], arr1[1] - 1, arr1[2]);

      var arr2 = maxDate.split('-');
      var max = new Date(arr2[0], arr2[1] - 1, arr2[2]);

      return Math.round((max - min) / (1000 * 60 * 60 * 24));
   }


  function calculateFY(ui) {

    var data = this.option('dataModel.data');
    var minDate = [];
    var maxDate = [];

    data.forEach(function(rd){
      if(rd.encomp_id)
      {
        if(rd.bgn_date && rd.end_date)
        {
          minDate.push(rd.bgn_date);
          maxDate.push(rd.end_date);
        }
      }
    });

    var str = '';
    var min = '';
    var max = '';
    if(minDate.length && maxDate.length)
    {
      min = minDate.reduce(function(a,b){ return a < b ? a: b;});
      max = maxDate.reduce(function(a,b){ return a > b ? a: b;});

      var max2 = maxDate.reduce(function(a,b){ return a < b ? a: b;});
      var min2 = minDate.reduce(function(a,b){ return a > b ? a: b;});

      var days = countDays(min, max);
      var days2 = countDays(min2, max2);

      
      if(days2 > 0)
        str = "Common: " + days2 + " / " + days + " days";
      else
        str = "No common day";
    }
      

    var totalData = {
        comp_name : str,
        bgn_date  : min,
        end_date  : max,
        pq_rowcls : 'grid_footer_color',
        summaryRow: true
    }
    
    this.option('summaryData', [totalData]);
    
  };


  var autoCompleteEditor = function (ui) {
    var colModel = this.option('colModel');
    var $inp = ui.$cell.find("input");
    var rd = ui.rowData;
    var grid = this;
    rd.company_error=0;
   
    $inp.autocomplete({
      source: <?php echo json_encode($company_list);?>,
      selectItem: { on: true }, //custom option
      highlightText: { on: false }, //custom option
      minLength: 0,
      select: function(event, ui) {
        event.preventDefault();
        if(ui.item.encomp_id!=''){
          rd.comp_code    = ui.item.comp_code;         
          rd.comp_name    = ui.item.label;
          rd.encomp_id    = ui.item.encomp_id;
          rd.fy_list      = ui.item.fy_list;
     
          $(this).val(ui.item.label);
        }       
      }
      }).focus(function () {
        $(this).autocomplete("search", "");            
      }).focusout(function () {   

       if(rd.encomp_id != '')
       {         
         var count = 0;
         var mcounter=0;
         var data=$("#grid_search").pqGrid('option','dataModel.data');

         for (var i = 0; i < data.length; i++) {          
          if(count < 2 && (data[i]['encomp_id'] == rd.encomp_id)) {
            count++; 
          }
        }
        if(count >= 2) { 
          rd.comp_code = '';         
          rd.comp_name = '';
          rd.encomp_id = '';
          rd.fy_list   = [];
          rd.fy_name = '';
          rd.bgn_date = '';
          rd.end_date = '';
          $(this).val('');
          grid.saveEditCell();
        }
       }
       else{
        rd.comp_code = '';         
        rd.comp_name = '';
        rd.encomp_id = '';
        rd.fy_list   = [];
        rd.fy_name = ''; 
        rd.bgn_date = '';
        rd.end_date = '';
       }
    });
  }

  function fyEditor(ui) {
        
    var $inp = ui.$cell.find("select"),
        di = ui.dataIndx,
        rd = ui.rowData,               
        grid = this,
        rowIndx = ui.rowIndx;   

      $inp.on("change", function (evt) {
          var fy_name = $(this).val();
          if(rd.fy_list){
            index = rd.fy_list.findIndex(x => x.name == fy_name);
            if(index >= 0){
              rd.fy_id = rd.fy_list[index]['id'];
              rd.bgn_date = rd.fy_list[index]['bgn_date'];
              rd.end_date = rd.fy_list[index]['end_date'];
            }
          }
      })
  };

  var colModel = [
    { title: "COMPANY", dataIndx: "comp_name", width: 100,dataType: "string",cls: 'pq-drop-icon pq-side-icon',
      editor: {                   
        type: "textbox",
        attr: "autocomplete='off'",
        init: autoCompleteEditor,
        options: [],
      },                    
    },
    { title: "COMPANY CODE", width: 100, dataType: "string", dataIndx: "comp_code", editable: false , cls: 'disabled',},
    { title: "Financial Year", dataIndx: "fy_name", width: 40,cls: 'pq-drop-icon pq-side-icon',editor: {
            type: 'select', 
            options: function( ui ) {
              var rd = ui.rowData;
              if(rd.fy_list)
                return rd.fy_list;
              return [];
            },
            init: fyEditor,
            valueIndx: "name",
            labelIndx: "name",
        }
    },
    { title: "Begin", dataIndx: "bgn_date", width: 10, editable: false, cls: 'disabled', 
      render: function( ui ) {
            var rd = ui.rowData;
            var grid = this;
            if(rd.bgn_date){
              var arr = rd.bgn_date.split("-");
              return arr[2]+'-'+arr[1]+'-'+arr[0];  
            }
            return '';
        }
    },
    { title: "End", dataIndx: "end_date", width: 10, editable: false, cls: 'disabled',
      render: function( ui ) {
            var rd = ui.rowData;
            var grid = this;
            if(rd.end_date){
              var arr = rd.end_date.split("-");
              return arr[2]+'-'+arr[1]+'-'+arr[0];  
            }
            return '';
        }
    },
  ];

  var dataModel = {"data":<?php echo json_encode($json_data);?>}


  var newObj = {
    collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
    height:420,
    selectionModel: { type: 'cell' },
    scrollModel: { autoFit: true },
    pageModel: { type: 'local' },
    dataModel: dataModel,         
    colModel: colModel,
    change: calculateFY,
    numberCell: { show: true },
    editable: true,
    wrap:false,
    cellSave: function(evt, ui){
     this.refresh();
    },
    editModel: {
      clicksToEdit: 1,
      keyUpDown: false
    },
    showTitle: false,
      create: function (evt, ui) {// make first row auto selected
        var grid = this,
        $select_row = $(".select-row"),
        data = ui.dataModel.data;
        grid.setSelection({ rowIndx: 0, focus: true });
      }
    };

    newObj.cellKeyDown = function(evt, ui) {
      var rd = ui.rowData;

      if (evt.keyCode == 46){ 
    
        rd.comp_code = '';         
        rd.comp_name = '';
        rd.encomp_id = '';
        rd.fy_list   = [];
        rd.fy_name = '';
        rd.bgn_date = '';
        rd.end_date = '';

        this.refreshDataAndView();
        return false;
      }
    }
    var $grid = $("#grid_search").pqGrid(newObj);

  });


  $(document).on('submit', '#myform', function(e){
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
              show_loader();
              $('#myform').attr('disabled', 'disabled');
              $('#validation_errors').html('');
          },
          success: function (response) {
            stop_loader();
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }

            if(response.status){
              alert_success(response.message);
              window.history.back();
              if(response.errors)
                console.log(errors);
              else
                window.history.back();
            }
            else{
              alert_notification(response.message);
              if(response.errors)
              {
                var list = ``;
                if(response.errors.length > 0){
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
            }
          },
          complete: function() {
              stop_loader();
              $('#myform').attr('disabled', false);
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
</script>