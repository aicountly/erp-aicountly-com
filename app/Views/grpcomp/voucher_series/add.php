<?php $header = array('title' => 'Add Voucher Series' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  

<div id="validation_errors"></div>

<form action="" method="post" id="myform" name="myform" class="needs-validation myform" novalidate>
    
<div class=" row">
    
    <div class="col-6"><h3 class="pb-3">Voucher Series</h3></div> 
    <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
    
    <div class="col-md-12">
        <?php if (session()->getFlashdata('error_message')) { ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <?php echo session()->getFlashdata('error_message'); ?>
                </div>
        <?php } ?>
    </div>
    
    
    <div class="col-md-12">
        <div class="card p-4 my-2">
            <h5 class="pb-2">General Info</h5>
        
            
            <div class="col-12">
                <label>Series Name</label>
                <input type="text" name="comp_vch_series" value="" class="form-control" required>
            </div>
            

            
            <div class="col-12">
                <label>Voucher Type</label>
                <!-- <div class="w-75 d-inline-block"> -->
                    <select class="form-select" name="voucher_type_id" required>
                        <option value=""></option>
                        <?php foreach ($voucher_types as $key => $value) { ?>
                            <option value="<?= $value['voucher_type_id'] ?>"><?= $value['comp_vch_type'] ?></option>
                        <?php } ?>
                    </select>	
                <!-- </div>  -->
            </div>

            <div class="col-12">
                <label>Voucher Numbering</label>
                <div class="input-group w-75">
                    <div class="input-group-text py-1">
                        <input type="radio" name="comp_vch_method" value="A" id="automatic" class="form-check-input"  checked="">&nbsp;
                        <label for="automatic">Automatic</label>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        <input type="radio" name="comp_vch_method" value="M" id="manual" class="form-check-input">&nbsp;
                        <label for="manual">Manual</label>
                    </div> 
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-12 numbering_config" id="manual_numbering_config" style="display: none;">
        <div class="card p-4 my-2">
            <h5 class="pb-2">Manual Numbering  Configuration</h5>
        
            <div class="col-12">
                <label>Warning on Duplicate No.</label>
                <div class="input-group w-75">
                    <div class="input-group-text py-1">
                        <input type="radio" name="comp_vch_warning_no" value="Y" id="comp_vch_warning_no_yes" class="form-check-input">&nbsp;
                        <label for="comp_vch_warning_no_yes">Yes</label>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        <input type="radio" name="comp_vch_warning_no" value="N" id="comp_vch_warning_no_no" class="form-check-input" checked="">&nbsp;
                        <label for="comp_vch_warning_no_no">no</label>
                    </div> 
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-12 numbering_config" id="automatic_numbering_config">
        <div class="card p-4 my-2">
            <h5 class="pb-2">Automatic Numbering  Configuration</h5>
        
            <div class="col-12">
                <label>Renumbering Frequency</label>
                <div class="input-group w-75">
                    <div class="input-group-text py-1">
                        <input type="radio" name="comp_vch_renum_freq" value="D" id="comp_vch_renum_freq_daily" class="form-check-input">&nbsp;
                        <label for="comp_vch_renum_freq_daily">Daily</label>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        <input type="radio" name="comp_vch_renum_freq" value="M" id="comp_vch_renum_freq_monthly" class="form-check-input">&nbsp;
                        <label for="comp_vch_renum_freq_monthly">Monthly</label>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        <input type="radio" name="comp_vch_renum_freq" value="Y" id="comp_vch_renum_freq_yearly" class="form-check-input" checked="">&nbsp;
                        <label for="comp_vch_renum_freq_yearly">Yearly</label>
                    </div> 
                </div>
            </div>

            <div class="col-12">
                <label>Embed Year/Month/Date in Voucher No.</label>
                <div class="input-group w-75">
                    <div class="input-group-text py-1">
                        <input type="radio" name="embed_type" value="N" id="embed_type_no" class="form-check-input" checked="">&nbsp;
                        <label for="embed_type_no">Not Required</label>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        <input type="radio" name="embed_type" value="P" id="embed_type_prefix" class="form-check-input">&nbsp;
                        <label for="embed_type_prefix">As a Prefix</label>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        <input type="radio" name="embed_type" value="S" id="embed_type_suffix" class="form-check-input">&nbsp;
                        <label for="embed_type_suffix">As a Suffix</label>
                    </div> 
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="col-12">
                        <label>Embedded Format</label>
                        <input type="text" name="embed_format" value="" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-12">
                        <label>Starting No.</label>
                        <input type="text" name="comp_vch_start" value="" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="col-12">
                        <label>Padding</label>
                        <input type="text" name="comp_vch_no_padding" value="" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-12">
                        <label>Fixed Length of Numeric Part</label>
                        <input type="text" name="comp_vch_no_length" value="" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="col-12">
                        <label>Prefix</label>
                        <input type="text" name="comp_vch_prefix" value="" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-12">
                        <label>Suffix</label>
                        <input type="text" name="comp_vch_suffix" value="" class="form-control">
                    </div>
                </div>
            </div>

        </div>
    </div>
    

    
    <div class="col-md-12  text-center">
        <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
        <a href="<?php echo $base_url.'voucher_series';?>" class="btn btn-secondary">QUIT</a>
    </div>
    
    
    
</div>
</form>

<?php echo view('includes/footer_scripts'); ?>
<script>
    $(document).on('change', 'input[name="comp_vch_method"]', function(){
        var method = $(this).val();

        if(method == 'A'){
            $('.numbering_config').css('display', 'none');
            $('#automatic_numbering_config').css('display', 'block');
        }
        if(method == 'M'){
            $('.numbering_config').css('display', 'none');
            $('#manual_numbering_config').css('display', 'block');
        }
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
                stop_loader();

                if(response.status){
                    alert_success(response.message);
                    window.location.reload();
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
                stop_loader();
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