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

<style>
.ui-autocomplete {
  max-height: 200px;
  overflow-y: auto;
  overflow-x: hidden;
}
.ui-autocomplete {
  z-index:9999!important;
}
</style>

<style>
  .pq-grid-row.bg-lightgreen{
    background-color: lightgreen !important;
  }
  .pq-grid-row.bg-white{
    background-color: white !important;
  }
  .pq-grid-row.bg-off_white{
    background-color: #f9f9f9 !important;
  }
  .pq-grid-row.bg-off_red{
    /*background-color: #ff7f7f66!important;*/
	 background-color: #f9f9f9 !important;
  }
  .pq-grid-cell.bg-off_white_dark{
    background-color: #f1f1f1 !important;
  }
</style>

<h3 class="pb-3">Data Export</h3>

<div class="col-md-6 myform pb-4">
    <div id="validation_errors"></div>
    <form name="exportfrm" method="post">
    <div class="card p-4 mt-2">
    <p class="col-12"><label>Module</label>
    <select name="module" id="module" class="form-control w-75" required>
    <option value=""></option>
    <option value="GST">GST</option>
    </select> </p>

                     <p class="col-12"><label>Master</label>
                   <select name="master" id="master" class="form-control w-75 " required>
    <option value=""></option>
    
    </select> </p>

                     <p class="col-12"><label>Sub Master</label>
                   <select name="sub_master" id="sub_master" class="form-control w-75" required>
    
    <option value=""></option>
    </select> </p>

     <p class="col-12"><label>Download Format</label>
                   <select name="sub_master_format" id="sub_master_format" class="form-control w-75 " required>

    <option value=""></option>
    </select> </p>
    
    
    <p class="col-lg-5 col-md-8 order-2 order-lg-3" >
    
      <div class="input-group input-group-sm" style="display:none;" id="export_period">
        <span class="input-group-text px-1">From</span>
        <input type="text" name="fromdate" id="mfromdate" value="<?php echo date('01-m-Y')  ;?>" class="datepicker form-control" required="" style="width:90px;" fdprocessedid="wpcyoh">
        
        <span class="input-group-text px-1">To</span>
        <input type="text" name="todate" id="mtodate" value="<?php echo date('d-m-Y')  ;?>" class="datepicker form-control" required="" style="width:90px;" fdprocessedid="gjzv6r">
        
       
      </div>
    
  </p>
    

    <p class="col-12"><button class="btn btn-success" id="submitbtn" type="submit">Export</button></p>
         </div>  
         </form>
</div>    

<div class="col-md-12">
        <p class="mb-0"><b>View Recent Requests:</b></p>
         <div id="exp_mst_tbl" class="col-12 gridtable" style="overflow: auto; max-width:100%; height:350px;">
            <div class="row head">
             <div class="col">Module</div>
             <div class="col">Master</div>
             <div class="col">Sub Master</div>
             <div class="col">Requested on</div>
             
           </div>
           <div class="row">
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
             
           </div>
         </div>  
     
    
</div>
  
<?php echo view('includes/footer_scripts'); ?>
<script>
get_export_master_list();
  function get_export_master_list()
  {

    $.ajax({
        type: "POST",
        url: '<?= base_url() ?>/admin/import_export/get_export_master_list/exp',
        method: 'GET',
        dataType: 'json',
        beforeSend: function() {
          show_loader();
        },
        success: function(response)
        {
          if(response.status){

              var html = ``;
              html += `
              <div class="row head">
                 <div class="col">Module</div>
                 <div class="col">Master</div>
                 <div class="col">Sub Master</div>
                 <div class="col">Requested on</div>
                 <div class="col">Action</div>
              </div>
              `;
              if(response.list){
                $.each(response.list, function(index, obj){

                html += `
                <div class="row">
                   <div class="col">${obj.module}</div>
                   <div class="col">${obj.master}</div>
                   <div class="col">${obj.sub_master}</div>
                   <div class="col">${obj.datetime}</div>`;

                if(obj.impexp_status == 0){
                  html += `
                    <div class="col">
                      <span role="button" data-impexp_sr_id="${obj.impexp_sr_id}" data-impexp_type="${obj.impexp_type}" class="material-symbols-outlined float-end text-danger delete_mst_btn">
                        delete
                      </span>

                   </div>
                  `;
                }

                if(obj.impexp_status == 1){
                  html += `
                    <div class="col">
                    <span class="badge bg-success exp_pro_btn">${obj.status}</span>

                    <span role="button" data-impexp_sr_id="${obj.impexp_sr_id}" data-impexp_type="${obj.impexp_type}" class="material-symbols-outlined float-end text-danger delete_mst_btn">
                        delete
                      </span>
                   </div>
                  `;
                }

                if(obj.impexp_status == 2){
                  html += `
                    <div class="col">
                    <span role="button" data-sub_master="${obj.sub_master}" data-impexp_status="${obj.impexp_status}" data-impexp_sr_id="${obj.impexp_sr_id}" data-impexp_type="${obj.impexp_type}" class="badge bg-warning exp_pro_btn">${obj.status}</span>

                      <span role="button" data-impexp_sr_id="${obj.impexp_sr_id}" data-impexp_type="${obj.impexp_type}" class="material-symbols-outlined float-end text-danger delete_mst_btn">
                        delete
                      </span>
                   </div>
                  `;
                }
                   
                   
                html += `</div>`;

                });
              }
              $('#exp_mst_tbl').html(html);
          }
          else{
            alert_notification(response.message); 
          }
        },
        complete: function() {
          stop_loader();
        },
    });
  } 
  
  $(document).on('click', '.delete_mst_btn', function(){

    if(!confirm('Are you sure ?')){
      return false;
    }

    var impexp_sr_id = $(this).data('impexp_sr_id');
    var impexp_type = $(this).data('impexp_type');

    $.ajax({
        
        url: '<?= base_url() ?>/admin/import_export/delete_inpexp_master',
        method: 'POST',
        dataType: 'json',
        data: {impexp_sr_id:impexp_sr_id, impexp_type: impexp_type},
        beforeSend: function() {
          show_loader();
        },
        success: function(response)
        {
          if(response.status){
            alert_success(response.message);
            get_export_master_list();
          }
          else{
            alert_notification(response.message); 
          }
        },
        complete: function() {
          stop_loader();
        },
    });

  });
  
  $(document).on('change','select[name="module"]', function(){
    var master = $(this).val();
    var html = `<option></option>`;

    if(master == 'GST'){
      html += `
        <option>Outward Supplies</option>
      `;
    }
    

    $('select[name="master"]').html(html);
    $('select[name="sub_master"]').html(`<option></option>`);
	$('select[name="sub_master_format"]').html(`<option></option>`);
  });

  $(document).on('change','select[name="master"]', function(){
    var master = $(this).val();
    var html = `<option></option>`;

    if(master == 'Outward Supplies'){
      html += `
        <option>GSTR-1</option>
      `;
    }
    
    $('#sub_master_format_div').hide();
    $('select[name="sub_master"]').html(html);
	$('select[name="sub_master_format"]').html(`<option></option>`);
  });
  
  $(document).on('change','select[name="sub_master"]', function(){
    var sub_master = $(this).val();
    var html = `<option></option>`;

    if(sub_master == 'GSTR-1'){
      html += ` 
       <option>JSON Format</option>		
      `;
    }
    $('#sub_master_format_div').show();
    $('select[name="sub_master_format"]').html(html);
  });
  
  
    $(document).on('change','select[name="sub_master_format"]', function(){
    var sub_master = $(this).val();
    var html = `<option></option>`;

    if(sub_master == 'JSON Format'){
     $("#export_period").show();
    }
    else
    $("#export_period").hide();
    
  });
  
  $(document).on('click', '#submitbtn', function(e) {
    e.preventDefault();
    show_loader();

    var fromdate = $("#mfromdate").val();
    var todate = $("#mtodate").val();
    var frmdata = { "fromdate": fromdate, "todate": todate, "wenc": "0" };

    var module = $("[name='module']").val();
    var master = $("[name='master']").val();
    var sub_master = $("[name='sub_master']").val();
    var sub_master_format = $("[name='sub_master_format']").val();

    // Validation check
    if (!module || !master || !sub_master || !sub_master_format) {
        stop_loader();
        let errors = '<ul>';
        if (!module) errors += '<li>Module is required.</li>';
        if (!master) errors += '<li>Master is required.</li>';
        if (!sub_master) errors += '<li>Sub Master is required.</li>';
		if (!sub_master_format) errors += '<li>Sub Master Format is required.</li>';
        errors += '</ul>';

        $('#validation_errors').html(`
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                ${errors}
            </div>
        `);
        window.scrollTo(0, 0);
        return false;
    }

    var masterfrmdata = {
        module: module,
        master: master,
        sub_master: sub_master,
        sub_master_format: sub_master_format
    };

    // Save master data
    save_master_data(masterfrmdata).done(function(response) {
        if (typeof response === 'string') {
            response = JSON.parse(response);
        }

        if (response.status) {
            // Proceed to generate_gstrone
            $.ajax({
                type: "POST",
                url: baseurl + "/admin/etaxes/generate_gstrone",
                data: frmdata,
                dataType: "json",
                success: function(response) {
                    stop_loader();

                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }

                    if (response.status) {
                        alert_success(response.message);
                        var blob = new Blob([response.jsonfile], {
                            type: 'application/json'
                        });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = response.filename;
                        link.click();
						get_export_master_list();
                    } else {
                        showValidationErrors(response);
                    }
                },
                error: function(jqXHR, exception) {
                    stop_loader();
                    alert_notification(getAjaxError(jqXHR, exception));
                }
            });
        } else {
            stop_loader();
            showValidationErrors(response);
        }
    }).fail(function(jqXHR, exception) {
        stop_loader();
        alert_notification(getAjaxError(jqXHR, exception));
    });

    return false;
});
function save_master_data(frmdata) {
    return $.ajax({
        type: "POST",
        url: baseurl + "/admin/import_export/createMaster",
        data: frmdata,
        dataType: "json"
    });
}
  
</script>
 </body>
</html>
