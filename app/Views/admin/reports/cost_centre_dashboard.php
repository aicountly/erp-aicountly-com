<?php $header = array('title' => 'Cost Centre');?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>


<div class="pb-5"> 
    <div class="row mb-md-0 mb-3">
        <div class="col-6">
            <h3>Cost Centre Report</h3>
        </div>
        <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></div>
    </div>
</div>

<form class="form needs-validation" method="post" id="salefrm"  novalidate autocomplete="off">
<div class="col-12 mb-5 calccard card m-auto">
    <div class="card-header fw-bold">
    <div class="row">
        <div class="col-md-6">
            <label>Report Type</label>
            <select name="report_type" class="form-control mt-2 borderdark required" required>
                <option></option>
                <option>ACCOUNT WISE REPORT</option>              
                <option>COST CENTRE LEDGER</option>
               
            </select>
        </div>
        <div class="col-md-6">
            <label>Sub Type</label>
            <select name="sub_type" class="form-control mt-2 borderdark" disabled required>
                <option></option>
            </select>
        </div>
 
        <div class="col-md-12">
            <label>Item</label>
            <input name="item" type="text" class="form-control borderdark"  disabled="disabled" required>
            <input type="hidden" name="id" value="">
        </div>

    </div> 
    </div>   
    
    <div class="row p-4">
        <?php $fy_bgn_yr = date('Y',strtotime($local_session->get('ses_company_fy_beginning'))); ?>
        <div class="comp_calender col-md-6">
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
                <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:68%; text-align: center; display: block;">FY: <?= $fy_bgn_yr ?> - <?= ($fy_bgn_yr+1) ?></button>
                <button type="button" class="input-group-text" id="next_year"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">From</div>
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control"  value="<?= date('01-m-Y') ?>" name="from_date" id="fromdate" placeholder="dd-mm-yyyy" required>
                    </div>
                </div>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">To</div> 
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control"   value="<?= date('d-m-Y') ?>" name="to_date" id="todate" placeholder="dd-mm-yyyy" required>
                    </div>
                </div>
            </div>
            <p class="text-end"><button type="button" class="input-group-text fw-bold ms-auto"  id="tilldate" required>TILL DATE</button></p>
        
        </div> 
        
        <p class="text-center pt-4"><button type="submit" class="btn btn-success btn-lg w-100">GO</button></p>  
        
    </div>
</div>
</form>
 

<?php echo view('includes/footer_scripts'); ?>


<script>
if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
    window.location.reload();
}

    var cc_list = <?php echo json_encode($cc_list) ?>;
    var cc_groups = <?php echo json_encode($cc_groups) ?>;
    
    $(document).on('change', 'select[name="report_type"]', function(){
        var report_type = $(this).val();
        $('select[name="sub_type"]').html('').attr('disabled','disabled');
        
        $('input[name="item"]').val('').attr('disabled', 'disabled');
        $('input[name="id"]').val('');
        
        if(report_type == 'ACCOUNT WISE REPORT')
        {
            html = `
                <option></option>
                <option>COST CENTRE-ACCOUNT WISE</option>
                <option>ACCOUNT WISE-COST CENTRE</option>
            `;
            $('select[name="sub_type"]').html(html);
            $('select[name="sub_type"]').attr('disabled', false);
        }
        if(report_type == 'COST CENTRE TRIAL')
        {
            html = `
                <option></option>
                <option>ALL COST CENTRES</option>
                <option>GROUP</option>
            `;
            $('select[name="sub_type"]').html(html);
            $('select[name="sub_type"]').attr('disabled', false);
        }
        if(report_type == 'COST CENTRE LEDGER')
        {
            html = `
                <option></option>
                <option>COST CENTRE</option>
            `;
            $('select[name="sub_type"]').html(html);
            $('select[name="sub_type"]').attr('disabled', false);
        }
        if(report_type == 'COST CENTRE SUMMARY')
        {
            html = `
                <option></option>
                <option>COST CENTRE GROUP SUMMARY</option>
                <option>COST CENTRE SUMMARY</option>
            `;
            $('select[name="sub_type"]').html(html);
            $('select[name="sub_type"]').attr('disabled', false);
        }
    });
    
    $(document).on('change', 'select[name="sub_type"]', function(){
        var sub_type = $(this).val();
        
        $('input[name="item"]').val('').attr('disabled', 'disabled');
        $('input[name="id"]').val('');
        
        $('input[name="from_date"]').attr('disabled', false);
        $('input[name="to_date"]').attr('disabled', false);
        
        if(sub_type == 'GROUP')
        {
            getList(cc_groups);
            $('input[name="item"]').attr('disabled', false);
        }
        if(sub_type == 'COST CENTRE GROUP')
        {
            getList(cc_groups);
            $('input[name="item"]').attr('disabled', false);
        }
        if(sub_type == 'COST CENTRE')
        {
            getList(cc_list);
            $('input[name="item"]').attr('disabled', false);
        }
        if(sub_type == 'COST CENTRE SUMMARY')
        {
            getList(cc_list);
            $('input[name="item"]').attr('disabled', false);
            $('input[name="from_date"]').val('').attr('disabled', 'disabled');
            $('input[name="to_date"]').val('').attr('disabled', 'disabled');
        }
        if(sub_type == 'COST CENTRE GROUP SUMMARY')
        {
            getList(cc_groups);
            $('input[name="item"]').attr('disabled', false);
            $('input[name="from_date"]').val('').attr('disabled', 'disabled');
            $('input[name="to_date"]').val('').attr('disabled', 'disabled');
        }
    });
    
    function getList(list) 
    {
        $('input[name="item"]').off('blur'); // unbind event first
        
        if($('input[name="item"]').hasClass('ui-autocomplete-input')) {
            $('input[name="item"]').autocomplete("destroy");
        }
        
        $( 'input[name="item"]' ).autocomplete({
            source: list,
            minLength: 0,
            select: function( event, ui ) {
                $(this).val(ui.item.label);
                $('input[name="id"]').val(ui.item.id);
            }
        })
        .on('focus', function(){
            $(this).autocomplete( "search", "" );
            $('input[name="item"]').val('');
            $('input[name="id"]').val('');
        })
        .on('blur', function(){
            
            if($(this).val() != '' && $('input[name="id"]').val() == '')
            {
                var acc = $(this).val();
                var index = list.findIndex(function(obj) {
                   return obj.label.toLowerCase() == acc.toLowerCase();
                });
                if(index > -1){
                    $(this).val(list[index].label);
                    $('input[name="id"]').val(list[index].id);
                    
                }
                else{
                    $('input[name="item"]').val('');
                    $('input[name="id"]').val('');
                }
            }
        });
    }
    
    
</script>
</body>
</html>
