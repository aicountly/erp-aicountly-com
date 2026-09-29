<?php $header = array('title' => 'Inward Supply 2A/2B Summary');?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>
<div class="row mb-md-0 mb-3">
    <div class="col-6"><h3 class="pb-3">Inward Supply 2A/2B Summary</h3></div>
    <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></div>
</div>
</div>

 <form class="form needs-validation" method="post" id="salefrm"  novalidate>
    <div class="col-12 mb-5 calccard card m-auto">
	
	<div class="row p-4">
        <?php $fy_bgn_yr = date('Y',strtotime($local_session->get('ses_company_fy_beginning'))); ?>
        <div class="gst_comp_calender col-md-6">
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
            
            <span class="fw-bold d-inline-block px-4"><input class="form-check-input mt-1 me-1" type="checkbox" value="1" id="tillperiod"> TILL PERIOD</span>
        </div>
        <div class="col-md-6">
            <div class="input-group mb-3">
                <button type="button" class="input-group-text" id="prev_year"><span class="material-symbols-outlined">arrow_back_ios</span></button>
                <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:70%; text-align: center; display: block;">FY: <?= $fy_bgn_yr ?> - <?= ($fy_bgn_yr+1) ?></button>
                <button type="button" class="input-group-text" id="next_year"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">From</div>
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control" name="fromdate" value="<?= date('01-m-Y') ?>" id="fromdate" placeholder="dd-mm-yyyy"  required>
                    </div>
                </div>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">To</div>
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control" name="todate" value="<?= date('d-m-Y') ?>" id="todate" placeholder="dd-mm-yyyy" required>
                    </div>
                </div>
            </div>
            <p class="text-end"><button type="button" class="input-group-text fw-bold ms-auto"  id="tilldate" required>TILL DATE</button></p>
        
        </div> 
        
        <p class="text-center pt-4"><button type="button" data-url="" id="gobtn" class="btn btn-success btn-lg w-100">GO</button></p>  
        
    </div>
	    
    </div>   
</form>
 

<?php echo view('includes/footer_scripts'); ?>
<script>
setgstCalender();
$(document).on("click","#gobtn",function(){
	var FromDate = $('#fromdate').datepicker('getDate');
	var ToDate = $('#todate').datepicker('getDate');
	
	const fday = String(FromDate.getDate()).padStart(2, '0');  // Ensure two digits
   const fmonth = String(FromDate.getMonth() + 1).padStart(2, '0');  // Months are 0-indexed
   const fyear = FromDate.getFullYear();
   const formattedFromDate = `${fday}-${fmonth}-${fyear}`;
   
   
   const tday = String(ToDate.getDate()).padStart(2, '0');  // Ensure two digits
   const tmonth = String(ToDate.getMonth() + 1).padStart(2, '0');  // Months are 0-indexed
   const tyear = ToDate.getFullYear();
   const formattedToDate = `${tday}-${tmonth}-${tyear}`;
	
	
	var url = $(this).data("url");
	
	url = url.replace(/fromdate=\d{2}-\d{2}-\d{4}/, `fromdate=${formattedFromDate}`);
    url = url.replace(/todate=\d{2}-\d{2}-\d{4}/, `todate=${formattedToDate}`);

	
	console.log(url);

window.location.href=url;	
	
});

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
                <button type="button" data-month="${chunk[0].month}" data-year="${chunk[0].year}" class="btn btn-light btn-block mb-1 month_btn">${chunk[0].label}</button>
             </div>`;
          }
          else{
            html += `<div class="col-md-3 d-grid px-1">`;
            chunk.forEach(function(obj){
              html += `<button type="button" data-month="${obj.month}" data-year="${obj.year}" class="btn btn-light btn-block mb-1 month_btn">${obj.label}</button>`;
            });
            html += `</div>`;
          }
          
      }
      html += `</div>`;

      var quarters = fy_calender.quarters;
      html += `<div class="row">`;
      quarters.forEach(function(obj){
        html += `<div class="col-md-3 d-grid px-1">`;
        html += `<button type="button" data-from_day="${obj.from_day}" data-from_month="${obj.from_month}" data-from_year="${obj.from_year}" data-to_day="${obj.to_day}" data-to_month="${obj.to_month}" data-to_year="${obj.to_year}" class="btn btn-qlight btn-block mb-1 quarter_btn">${obj.label}</button>`;
        html += `</div>`;
      });
      html += `</div>`;

      var half_years = fy_calender.half_years;
      html += `<div class="row">`;
      half_years.forEach(function(obj){
        html += `<div class="col-md-3 d-grid px-1">`;
        html += `<button type="button" data-from_day="${obj.from_day}" data-from_month="${obj.from_month}" data-from_year="${obj.from_year}" data-to_day="${obj.to_day}" data-to_month="${obj.to_month}" data-to_year="${obj.to_year}" class="btn btn-hlight btn-block mb-1 hyear_btn">${obj.label}</button>`;
        html += `</div>`;
      }); 
      html += `<div class="col-md-6 d-grid p-2 tillprdiv" id="tillprdiv"><span class="fw-bold d-inline-block px-4"><input class="form-check-input mt-1 me-1" type="checkbox" value="1" id="tillperiod"> TILL PERIOD</span></div>`;
      html += `</div>`;

      $('.gst_comp_calender').html(html);
      $('.gst_comp_calender').attr('data-from_day', fy_calender.from_day);
     $('.gst_comp_calender').attr('data-from_month', fy_calender.from_month);
      $('.gst_comp_calender').attr('data-from_year', fy_calender.from_year);
      $('.gst_comp_calender').attr('data-to_day', fy_calender.to_day);
      $('.gst_comp_calender').attr('data-to_month', fy_calender.to_month);
      $('.gst_comp_calender').attr('data-to_year', fy_calender.to_year);

      set_fy_label_gst();
      set_fy_date_gst();
    }
	
  
    function validate_from_date_gst(datee){

        var d = datee.split("-");
        var from_date = new Date(d[2], d[1]-1, d[0]);

        var fy_from_day = $('.gst_comp_calender').attr('data-from_day');
        var fy_from_month = $('.gst_comp_calender').attr('data-from_month');
        var fy_from_year = $('.gst_comp_calender').attr('data-from_year');

        var fy_from_date = new Date(parseInt(fy_from_year),(parseInt(fy_from_month)-1),parseInt(fy_from_day));
        
        from_date.setHours(0,0,0,0);
        fy_from_date.setHours(0,0,0,0);

        if(from_date > fy_from_date) {
            return datee;   
        }
        return fy_from_day+'-'+fy_from_month+'-'+fy_from_year;
    }
	
	
    function validate_to_date_gst(datee){
        var d = datee.split("-");
        var to_date = new Date(d[2], d[1]-1, d[0]);

        var fy_to_day = $('.gst_comp_calender').attr('data-to_day');
        var fy_to_month = $('.gst_comp_calender').attr('data-to_month');
        var fy_to_year = $('.gst_comp_calender').attr('data-to_year');
        var fy_to_date = new Date(parseInt(fy_to_year),(parseInt(fy_to_month)-1),parseInt(fy_to_day));
        
        to_date.setHours(0,0,0,0);
        fy_to_date.setHours(0,0,0,0);

        if(to_date < fy_to_date) {
            return datee;   
        }
        return fy_to_day+'-'+fy_to_month+'-'+fy_to_year;
    } 
   
   

    function set_fy_label_gst()
    {
      var fy_from_year = $('.gst_comp_calender').attr('data-from_year');
      var fy_to_year = $('.gst_comp_calender').attr('data-to_year');

      var from_year = parseInt(fy_from_year);
      var to_year = parseInt(fy_to_year);

      if(from_year == to_year)
        $('#fy_year').text(`FY: ${from_year}`);
      else{
        var sub_from = from_year.toString().substr(0,2);
        var sub_to = to_year.toString().substr(0,2);
        if(sub_from == sub_to){
          $('#fy_year').text(`FY: ${from_year} - ${to_year.toString().substr(2,2)}`);
        }
        else
          $('#fy_year').text(`FY: ${from_year} - ${to_year}`);
      }
    }

    function set_fy_date_gst()
    {
      var from_day = $('.gst_comp_calender').attr('data-from_day');
      var from_month = $('.gst_comp_calender').attr('data-from_month');
      var from_year = $('.gst_comp_calender').attr('data-from_year');
      var from_date = from_day + '-' + from_month + '-' + from_year;

      var to_day = $('.gst_comp_calender').attr('data-to_day');
      var to_month = $('.gst_comp_calender').attr('data-to_month');
      var to_year = $('.gst_comp_calender').attr('data-to_year');
      var to_date = to_day + '-' + to_month + '-' + to_year;

      $("#fromdate").val(from_date);
      $("#todate").val(to_date);
	  
	  
var fltrtype   = '<?php echo obfuscate_link('date');?>';
     var pageurl ="<?php base_url();?>/admin/gst/inwrdspl_2a2b_detail?fromdate="+from_date+"&todate="+to_date+"&fltrtype="+fltrtype;
	 $("#gobtn").attr("data-url",pageurl);	 
	 
    }
    
    $(document).on("click","#next_year",function(){
        
      var fy_from_year = $('.gst_comp_calender').attr('data-from_year');
      var fy_to_year = $('.gst_comp_calender').attr('data-to_year');
        
      var from_year = parseInt(fy_from_year);
      var to_year = parseInt(fy_to_year);

      from_year++;
      to_year++;

      $('.gst_comp_calender').attr('data-from_year', from_year);
      $('.gst_comp_calender').attr('data-to_year', to_year);

        $(".month_btn").each(function() {
            var year = $(this).attr('data-year');
            year = parseInt(year);
            year++;
            $(this).attr('data-year',year);
        });

        $(".quarter_btn").each(function() {
            var from_year = $(this).attr('data-from_year');
            var to_year = $(this).attr('data-to_year');
            from_year = parseInt(from_year);
            to_year = parseInt(to_year);
            from_year++;
            to_year++;
            $(this).attr('data-from_year',from_year);
            $(this).attr('data-to_year',to_year);
        });

        $(".hyear_btn").each(function() {
            var from_year = $(this).attr('data-from_year');
            var to_year = $(this).attr('data-to_year');
            from_year = parseInt(from_year);
            to_year = parseInt(to_year);
            from_year++;
            to_year++;
            $(this).attr('data-from_year',from_year);
            $(this).attr('data-to_year',to_year);
        });

        set_fy_label_gst();
        set_fy_date_gst();
    });

    $(document).on("click","#prev_year",function(){
      var fy_from_year = $('.gst_comp_calender').attr('data-from_year');
      var fy_to_year = $('.gst_comp_calender').attr('data-to_year');
        
      var from_year = parseInt(fy_from_year);
      var to_year = parseInt(fy_to_year);

      from_year--;
      to_year--;

      $('.gst_comp_calender').attr('data-from_year', from_year);
      $('.gst_comp_calender').attr('data-to_year', to_year);

        $(".month_btn").each(function() {
            var year = $(this).attr('data-year');
            year = parseInt(year);
            year--;
            $(this).attr('data-year',year);
        });

        $(".quarter_btn").each(function() {
            var from_year = $(this).attr('data-from_year');
            var to_year = $(this).attr('data-to_year');
            from_year = parseInt(from_year);
            to_year = parseInt(to_year);
            from_year--;
            to_year--;
            $(this).attr('data-from_year',from_year);
            $(this).attr('data-to_year',to_year);
        });

        $(".hyear_btn").each(function() {
            var from_year = $(this).attr('data-from_year');
            var to_year = $(this).attr('data-to_year');
            from_year = parseInt(from_year);
            to_year = parseInt(to_year);
            from_year--;
            to_year--;
            $(this).attr('data-from_year',from_year);
            $(this).attr('data-to_year',to_year);
        });


	 
        set_fy_label_gst();
        set_fy_date_gst();
    });

    $(document).on("click","#fy_year",function(){
        
        set_fy_date_gst();
    })
    
    $(document).on("click",".month_btn",function(){
        var month = $(this).attr('data-month');
        var year = $(this).attr('data-year');

        var fd = '01';
        var ld = new Date(year, month, 0).getDate();
        //console.log("==="+ld);
        var y = year; 
        var m = month < 10 ? '0'+month : month;
        
        var from_date = fd + '-' + m + '-' + y;
        var to_date = ld + '-' + m + '-' + y;
        
        if($('#tillperiod').is(":checked")){
        var ldmonth = new Date(year, month, 0).getMonth();
		var ldyear = new Date(year, month, 0).getFullYear();
		if(ldmonth<9)
			var show_ldmonth = "0"+(parseInt(ldmonth)+parseInt(1));
		 else
			var show_ldmonth = (parseInt(ldmonth)+parseInt(1));
          var td = today();
          var arr = td.split('-');
          var ld = new Date(arr[2], arr[1], 0).getDate();
          //to_date = ld+ '-' + arr[1] + '-' + arr[2];
		 
		
		 from_dates = $("#fromdate").val();
		 
		 to_dates = ld+ '-' + show_ldmonth + '-' + ldyear;
		 
		 //to_date = validate_to_date(to_date);
		//console.log(to_dates);
		
		var d = to_dates.split("-");
        var to_date = new Date(d[2], d[1]-1, d[0]);
		
		var dd = from_dates.split("-");
        var from_date = new Date(dd[2], dd[1]-1, dd[0]);

		  if(new Date(to_date)>new Date(from_date)){
			
		    $("#fromdate").val(from_dates);		
		    $("#todate").val(to_dates);  
			
			
			$('#warning_message').remove();
		  }else{
			  // console.log('invalid');
			$("#fromdate").val(from_dates);	 	
		    $("#todate").val('');
		
		if($("#warning_message").length > 0 ){
		$('#warning_message').remove();
		}else{
		   $('#tilldate').after('<span id="warning_message" style="color:red;">Invalid To Date</span>');
		}
	  }
		  
        }else{
        from_date = validate_from_date_gst(from_date);
		$("#fromdate").val(from_date);
		 to_date = validate_to_date_gst(to_date);
		 $("#todate").val(to_date);
		}
	
		
		
     var fltrtype   = '<?php echo obfuscate_link('mnth');?>';
     var pageurl ="<?php base_url();?>/admin/gst/inwrdspl_2a2b_detail?fromdate="+from_date+"&todate="+to_date+"&fltrtype="+fltrtype;
	 $("#gobtn").attr("data-url",pageurl);	 
        
        
    });
    
    $(document).on("click",".quarter_btn",function(){
        var from_day = $(this).attr('data-from_day');
        var from_month = $(this).attr('data-from_month');
        var from_year = $(this).attr('data-from_year');
        var to_day = $(this).attr('data-to_day');
        var to_month = $(this).attr('data-to_month');
        var to_year = $(this).attr('data-to_year');

        from_day < 10 ? '0'+from_day : from_day;
        from_month < 10 ? '0'+from_month : from_month;
        to_day < 10 ? '0'+to_day : to_day;
        to_month < 10 ? '0'+to_month : to_month;

        var from_date = from_day+'-'+from_month+'-'+from_year;
        var to_date = to_day+'-'+to_month+'-'+to_year;
        
        if($('#tillperiod').is(":checked")){
        
          var td = today();
          var arr = td.split('-');
          var ld = new Date(arr[2], arr[1], 0).getDate();
          to_date = ld+ arr[1] +'-' + arr[2];
        }

        from_date = validate_from_date_gst(from_date);
        to_date = validate_to_date_gst(to_date);
        
        $( "#fromdate" ).val(from_date);
        $( "#todate" ).val(to_date);
		
	var fltrtype   = '<?php echo obfuscate_link('qtr');?>';
     var pageurl ="<?php base_url();?>/admin/gst/inwrdspl_2a2b_detail?fromdate="+from_date+"&todate="+to_date+"&fltrtype="+fltrtype;
	 $("#gobtn").attr("data-url",pageurl);	
		
    });
    
    $(document).on("click",".hyear_btn",function(){
        var from_day = $(this).attr('data-from_day');
        var from_month = $(this).attr('data-from_month');
        var from_year = $(this).attr('data-from_year');
        var to_day = $(this).attr('data-to_day');
        var to_month = $(this).attr('data-to_month');
        var to_year = $(this).attr('data-to_year');

        from_day < 10 ? '0'+from_day : from_day;
        from_month < 10 ? '0'+from_month : from_month;
        to_day < 10 ? '0'+to_day : to_day;
        to_month < 10 ? '0'+to_month : to_month;

        var from_date = from_day+'-'+from_month+'-'+from_year;
        var to_date = to_day+'-'+to_month+'-'+to_year;
        
        if($('#tillperiod').is(":checked")){
        
          var td = today();
          var arr = td.split('-');
          var ld = new Date(arr[2], arr[1], 0).getDate();
          to_date = ld+ arr[1] +'-' + arr[2];
        }

        from_date = validate_from_date_gst(from_date);
        to_date = validate_to_date_gst(to_date);
        
        $( "#fromdate" ).val(from_date);
        $( "#todate" ).val(to_date);
		
		var fltrtype   = '<?php echo obfuscate_link('yrs');?>';
     var pageurl ="<?php base_url();?>/admin/gst/inwrdspl_2a2b_detail?fromdate="+from_date+"&todate="+to_date+"&fltrtype="+fltrtype;
	 $("#gobtn").attr("data-url",pageurl);	
    });
    
    $(document).on("click","#tilldate",function(){

        $('#tillperiod').prop('checked', false);
        
        if($("#fromdate").val() == '' || checkdate($("#fromdate").val())){
          var from_day = $('.gst_comp_calender').attr('data-from_day');
          var from_month = $('.gst_comp_calender').attr('data-from_month');
          var from_year = $('.gst_comp_calender').attr('data-from_year');
          var from_date = from_day + '-' + from_month + '-' + from_year;
          $( "#fromyear" ).val(from_year);
        }
         
        var to_date = today();
        to_date = validate_to_date_gst(to_date);
        $( "#todate" ).val(to_date);
    });
    
    
    

</script>
</body>
</html>
