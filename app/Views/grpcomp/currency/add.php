<?php $header = array('title' => 'Add Currency' ); ?>
<?php if(!session()->get('ses_company_id')){ ?>

<?php echo view('includes/header2',$header); ?>

<div class="row mb-4 myalltabs px-3 bg bg-success">
  <div class="col-12" id="tabslistings" style="display:inline-block;width:auto;">
    <a href="<?php echo base_url();?>/home/open_company" ><span id="#" class="btn tabslist">My Company</span></a>
    <a href="<?php echo base_url();?>/sharedwithme" ><span id="#" class="btn tabslist">Shared With Me</span></a>
    <a href="<?php echo base_url();?>/archivecompany" ><span id="#" class="btn tabslist">Archive Company</span></a>
  </div>
</div>

<div class="content"><div class="pb-5">

<?php } else { ?>

<?php echo view('includes/header',$header); ?>

<?php } ?>

<style>
    .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>      


<div id="validation_errors"></div>

<form action="" method="post" id="myform" name="myform" class="needs-validation myform" novalidate>

    
<div class=" row">
    
    <div class="col-6"><h3 class="pb-3">Add Currency</h3></div> 
    <div class="col-6">
        <span class="float-end">
        <a href="javascript:void();" onclick="window.history.back()" class="btn btn-sm btn-outline-success">« Back</a>
        </span>
    </div> 
    
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
        
        <div class="row">
            <div class="col-md-6">
                <div class="col-12">
                    <label>Name</label>
                    <input type="text" name="curr_name" value="" class="form-control" required>
                </div>
                
                <div class="col-12">
                    <label>Symbol</label>
                    <select class="form-select" name="curr_symbol" required>
                        <option value=""></option>
                        <?php foreach ($currency_symbols as $key => $value) { ?>
                            <option value="<?= $value ?>"><?= $value ?></option>
                        <?php } ?>
                    </select>   
                </div>

                <div class="col-12">
                    <label>Initials</label>
                    <input type="text" name="curr_initial" value="" class="form-control" required>
                </div>

            </div>
            <div class="col-md-6">
                
                <div class="col-12">
                    <label>String</label>
                    <input type="text" name="curr_string" value="" class="form-control">
                </div>

                <div class="col-12">
                    <label>Sub-String</label>
                    <input type="text" name="curr_sub_string" value="" class="form-control">
                </div>

                <div class="col-12">
                    <label>Fetch Auto Forex Rate</label>
                    <div class="input-group w-75">
                        <div class="input-group-text py-1">
                            <input type="radio" name="forex_type" value="Y" id="forex_type_yes" class="form-check-input"  checked="">&nbsp;
                            <label for="forex_type_yes">Yes</label>

                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                            <input type="radio" name="forex_type" value="N" id="forex_type_no" class="form-check-input">&nbsp;
                            <label for="forex_type_no">No</label>
                        </div> 
                    </div>
                </div>

            </div>
        </div>
            
            

        </div>
    </div>

    <div class="col-md-12  text-center">
        <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
        <a href="javascript:void();" onclick="window.history.back()" class="btn btn-secondary">QUIT</a>
    </div>
    
    
    
</div>
</form>

</div></div>

<?php echo view('includes/footer_scripts'); ?>
<script>

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