<!DOCTYPE html>
<html>
<head>
    <title>Voucher Debug</title>
	<!-- jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

</head>
<body>

<h2>Voucher Viewer</h2>
<style>
  
#toolbarContainer{display:none!important;}
#logTable {
    font-size: 14px;
}
.query-cell-right{text-align:right;}

.query-cell {
    max-width: 600px;
    width:300px;
    white-space: pre-wrap;
    word-break: break-word;
    font-family: monospace;
    background: #f8f9fa;
    padding: 8px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.query-cell.collapsed {
    max-height: 80px;
    overflow: hidden;
    position: relative;
}
.query-cell.collapsed::after {
    content: '▼';
    position: absolute;
    bottom: 5px;
    right: 10px;
    background: #fff;
    padding-left: 5px;
}
.query-cell.expanded::after {
    content: '▲';
}

    </style>
<div>
<form method="post" id="myForm">
    <label>Company:</label>
    <select id="filter_company" name="filter_company" class="form_control">
        <option value="">Choose</option>
    </select>
    <label>Voucher Txn ID:</label>
    <input type="hidden" name="btnval" id="btnval" value="" />
    <input type="textbox" id="filter_vchtxnid" name="filter_vchtxnid">
   <input type="button" class="button" id="gofilters" value="Open Voucher">
   <input type="button" class="button" id="gofilters_show" value="Show Voucher Txn Id">
	</form>
	<br>
	<span id="vchtxn_id_div"></span>
</div>
<script>
const baseurl = "<?= base_url(); ?>";

$(function(){
 
  $('#gofilters').on('click', function(){
    $('#btnval').val(this.id);    // put button-ID into hidden field
    $('#myForm').trigger('submit');      // trigger the form’s submit event
  });
  
  
  
  $('#gofilters_show').on('click', function(){
    $('#btnval').val(this.id);    // put button-ID into hidden field
   
                 // stop normal form submit
   
    var url   = baseurl+"/debugvoucher/index"     // your controller URL
    var data = {"filter_vchtxnid":$("#filter_vchtxnid").val(),"filter_company":$("#filter_company").val(),"btnval":$("#btnval").val()};

    $.ajax({
      url:        url,
      type:       'POST',
      data:       data,
      dataType:   'json',                 // expect JSON back
     success: function(response){
       $("#vchtxn_id_div").html("");
        if (response.status) {
         
          if(response.btn=='gofilters')
           window.open(response.url, '_blank', 'noopener');
           else
             $("#vchtxn_id_div").html("Voucher Txn Id: "+response.vouchertxnid);
          
           
        } else {
         
          alert('Error: could not process request.');
        }
       
      }, 
      error: function(xhr, status, err){
        // network or server error
       // console.error('AJAX Error:', status, err);
        //alert('An unexpected error occurred.');
      },
      complete: function(){
        // optional: hide loader/spinner
      }
    });
   
  })
 
  
 
  $('#myForm').on('submit', function(e){
      
    e.preventDefault();                   // stop normal form submit
    var $form = $(this);
    var url   = $form.attr('action');     // your controller URL
    var data = $form.serialize();

    $.ajax({
      url:        url,
      type:       'POST',
      data:       data,
      dataType:   'json',                 // expect JSON back
      beforeSend: () => console.log('   → AJAX beforeSend'),
      success:   (resp) => console.log('   → AJAX success', resp),
      error:     (xhr,s,e) => console.log('   → AJAX error', s, e),
      complete:  () => console.log('   → AJAX complete'),
        success: function(response){
       
        if (response.status) {
         
          if(response.btn=='gofilters')
           window.open(response.url, '_blank', 'noopener');
           else
             $("#vchtxn_id_div").html("Voucher Txn Id: "+response.vouchertxnid);
          
           
        } else {
         
          alert('Error: could not process request.');
        }
       
      }, 
      error: function(xhr, status, err){
        // network or server error
       // console.error('AJAX Error:', status, err);
        //alert('An unexpected error occurred.');
      },
      complete: function(){
        // optional: hide loader/spinner
      }
    });
  });
});
function loadCompanies() {

    /*   Bring the element into jQuery for convenience */
    const $sel = $('#filter_company');

    /*   Fetch company list */
    $.ajax({
        url: baseurl + '/debugvoucher/ajax_all_companies',
        method: 'POST',           // ← POST request
        dataType: 'json',         // server must return JSON
        data: {
        pq_datatype: 'json',
        pq_curpage : 1,
        pq_rpp     : 1000,
        pq_filter  : JSON.stringify({
            mode: 'OR',
            data: [{
                dataIndx : 'companycode',
                value    : '',
                dataType : 'string',
                cbFn     : ''
            }]
        })
    }
    })
    .done(function (resp) {
		
		const $sel  = $('#filter_company');   // your <select> element
        $sel.empty();  

        if (resp && Array.isArray(resp.data)) {

            const seen = new Set();          // in case the response has duplicates

            resp.data.forEach(function (row) {
                if (!seen.has(row.company_id)) {
                    seen.add(row.company_id);

                    $sel.append(
                        $('<option>', {
                            value : row.company_id,
                            text  : row.companyname
                        })
                    );
                }
            });

        } else {
            $sel.append('<option value="">No companies found</option>');
        }

    })
    .fail(function (xhr, status, error) {
        // Optional: fallback UI
        console.error('Company load failed:', error);
        $sel.html('<option value="">Unable to load companies</option>');
    });
} 

/*   Kick it off once DOM is ready */
$(document).ready(loadCompanies);
</script>
</body>
</html>
