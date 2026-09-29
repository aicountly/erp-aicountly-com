<?php $header = array(  'title' => 'Dashboard' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>



<div class="row g-4">


</div>
<br>

<div class="row">
  <div class="col-6">
    <h3 class="pb-3">New Financial Year</h3>
  </div>  
  <div class="col-6">
    
  </div>
</div>

<form id='myform' name='myform' action="<?= base_url() ?>/grpcomp/company/nextfy" method="post" class="needs-validation myform" autocomplete='off' novalidate>

<div class="row">
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

<?php $below_js = array('assets/js/Chart.bundle.min.js'); ?>
<?php echo view('includes/'.$folder_path.'footer_scripts',array('below_js' => $below_js)); ?>


<script>

 $(function () {

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
    var error = 0;

    data.forEach(function(rd){
      if(rd.comp_fy_id)
      {
        if(rd.bgn_date && rd.end_date)
        {
          minDate.push(rd.bgn_date);
          maxDate.push(rd.end_date);
        }
      }
      else{
        error++;
      }
    });


    var str = '';
    var min = '';
    var max = '';

    if(error > 0){
      str = error' FY missing';
    }
    else{
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
    }
    console.log(str);

    var totalData = {
        comp_name : str,
        bgn_date  : min,
        end_date  : max,
        pq_rowcls : 'grid_footer_color',
        summaryRow: true
    }
    
    this.option('summaryData', [totalData]);
    
  };

  var colModel = [
    { title: "COMPANY", dataIndx: "comp_name", width: 100,dataType: "string"},
    { title: "COMPANY CODE", width: 100, dataType: "string", dataIndx: "comp_code", editable: false , cls: 'disabled',},
    { title: "Financial Year", dataIndx: "fy_name", width: 40},
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

  var dataModel = {"data":<?php echo json_encode($comp_list);?>}


  var newObj = {
    collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
    height:420,
    selectionModel: { type: 'cell' },
    scrollModel: { autoFit: true },
    pageModel: { type: 'local' },
    dataModel: dataModel,         
    colModel: colModel,
    dataReady: calculateFY,
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