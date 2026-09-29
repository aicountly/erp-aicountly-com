<?php $header = array('title' => 'eGSTR-1');?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));



?>

<div class="row mb-md-0 mb-3">
    <div class="col-6"><h3 class="pb-3">eGSTR-1</h3></div>
    <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></div>
</div>
</div>

 <form class="form needs-validation" method="post" id="salefrm"  novalidate>
    <div class="col-12 mb-5 calccard card m-auto">
        <div class="row p-4">
        <?php $fy_bgn_yr = date('Y',strtotime($local_session->get('ses_company_fy_beginning'))); ?>
        <div class="gst_comp_calender col-md-12">
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
        </div>
        
        
    </div>
    </div>   
</form>
 

<?php echo view('includes/footer_scripts'); ?>
<script>
setgstCalender();
function setgstCalender()
    {
      var months = fy_calender.months;

      var html = ``;
      html += `<div class="row">`;

      var chunkSize = 3;
      for (var i = 0; i < months.length; i += chunkSize) {
          var chunk = months.slice(i, i + chunkSize);
          
          if(chunk.length == 1){
             html += `<div class="col-md-3 d-grid px-1"></div>`;
             html += `<div class="col-md-3 d-grid px-1"></div>`;
             html += `<div class="col-md-3 d-grid px-1"></div>`;
             html += `<div class="col-md-3 d-grid px-1">
                <a  data-id="mn" data-month="${chunk[0].month}" data-year="${chunk[0].year}" class="btn btn-light btn-block mb-1 call_gst_btn">${chunk[0].label}</a>
             </div>`;
          }
          else{
            html += `<div class="col-md-3 d-grid px-1">`;
            chunk.forEach(function(obj){
              html += `<a  data-id="mn" data-month="${obj.month}" data-year="${obj.year}" class="btn btn-light btn-block mb-1 call_gst_btn">${obj.label}</a>`;
            });
            html += `</div>`;
          }
          
      }
      html += `</div>`;

      var quarters = fy_calender.quarters;
      html += `<div class="row">`;
      quarters.forEach(function(obj){
        html += `<div class="col-md-3 d-grid px-1">`;
        html += `<a  data-from_day="${obj.from_day}" data-id="qtr" data-from_month="${obj.from_month}" data-from_year="${obj.from_year}" data-to_day="${obj.to_day}" data-to_month="${obj.to_month}" data-to_year="${obj.to_year}" class="btn btn-qlight btn-block mb-1 call_gst_btn">${obj.label}</a>`;
        html += `</div>`;
      });
      html += `</div>`;

      var half_years = fy_calender.half_years;
      html += `<div class="row">`;
      half_years.forEach(function(obj){
        html += `<div class="col-md-3 d-grid px-1">`;
        html += `<a  data-from_day="${obj.from_day}" data-id="yrs" data-from_month="${obj.from_month}" data-from_year="${obj.from_year}" data-to_day="${obj.to_day}" data-to_month="${obj.to_month}" data-to_year="${obj.to_year}" class="btn btn-hlight btn-block mb-1 call_gst_btn">${obj.label}</a>`;
        html += `</div>`;
      });       
      html += `</div>`;

      $('.gst_comp_calender').html(html);
      $('.gst_comp_calender').attr('data-from_day', fy_calender.from_day);
     $('.gst_comp_calender').attr('data-from_month', fy_calender.from_month);
      $('.gst_comp_calender').attr('data-from_year', fy_calender.from_year);
      $('.gst_comp_calender').attr('data-to_day', fy_calender.to_day);
      $('.gst_comp_calender').attr('data-to_month', fy_calender.to_month);
      $('.gst_comp_calender').attr('data-to_year', fy_calender.to_year);

      set_fy_label();
     
    }
	function firstDateOfMonth(date){	
  return new Date(date.getFullYear(), date.getMonth()+1, 0);
	}
$(".call_gst_btn").on("click",function(){
	
	var method = $(this).data("id");
	
	if($(this).data("id")=="mn" ){
		var fltrtype   = '<?php echo obfuscate_link('mnth');?>';
		var years = $(this).data("year");
	 if(parseInt($(this).data("month")) <= 9)
		var mnths = "0"+$(this).data("month");
	 else
		var mnths = $(this).data("month");
	
	var fromdate = years+'-'+mnths+"-01";	
	var lastDate = firstDateOfMonth(new Date(fromdate)); 
	var todate   = lastDate.getFullYear()+"-"+(lastDate.getMonth()+1)+"-"+lastDate.getDate();	
	}
	if($(this).data("id")=="qtr" ){
		var fltrtype   = '<?php echo obfuscate_link('qtr');?>';
		var frm_years  = $(this).data("to_year");
		var to_years   = $(this).data("to_year");
		var from_month = $(this).data("from_month");	
		var to_month   = $(this).data("to_month");	
		var from_day   = $(this).data("from_day");
		var to_date    = $(this).data("to_day");
		var fromdate   = frm_years+'-'+from_month+"-"+from_day;		 
		var todate     = to_years+"-"+to_month+"-"+to_date;
	}
	if($(this).data("id")=="yrs"){
		var fltrtype   = '<?php echo obfuscate_link('yrs');?>';
	    var frm_years = $(this).data("from_year");
		var to_years   = $(this).data("to_year");
		var from_month = $(this).data("from_month");	
		var to_month   = $(this).data("to_month");	
		var from_day   = $(this).data("from_day");
		var to_date    = $(this).data("to_day");
		var fromdate   = frm_years+'-'+from_month+"-"+from_day;		 
		var todate     = to_years+"-"+to_month+"-"+to_date;
	}	
	window.location.href="<?php base_url();?>/admin/etaxes/report_summary?fromdate="+fromdate+"&todate="+todate+"&fltrtype="+fltrtype;
	return false;
});	
</script>
</body>
</html>
