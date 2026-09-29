<?php $header = array('title' => 'Edit Cost Centre' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  

<?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
    echo form_open(base_url().$folder_path.'cost_centres/modify_cc/'.$cc_id, $attributes);
?>

<input type="hidden" name="cc_id" value="<?= $cc_id ?>">
    
<div class=" row">
    
    <div class="col-6"><h3 class="pb-3">Edit Cost Centre</h3></div> 
    <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
    <div class="col-md-12">	
        <?php if (session()->getFlashdata('error_message')) { ?>
            <div class="alert-error-custom">
			<i class="bi bi-x-circle-fill"></i>
			<div>
			<strong>Error!</strong> <?php echo session()->getFlashdata('error_message'); ?>. 
                
			</div>
			<button type="button" class="btn-close" aria-label="Close"></button>
		</div>
        <?php } ?>
    </div>    
    
    <div class="col-md-6">
        <div class="card p-4 my-2">
        <h5 class="pb-2">General Info</h5>
    
        
        <div class="col-12">
            <label>Name <span class="red">*</span></label>
            <input type="text" name="cc_name" value="<?= set_value("cc_name") ? set_value("cc_name") : $cc_info['cc_name'] ?>" class="form-control" required>
        </div>
        <div class="col-12">
            <label>Alias</label> 
            <input type="text" name="cc_alias" value="<?= set_value("cc_alias") ? set_value("cc_alias") : $cc_info['cc_alias'] ?>" class="form-control" >
        </div>
        <div class="col-12">
            <label>Print</label> 
            <input type="text" name="cc_print" value="<?= set_value("cc_print") ? set_value("cc_print") : $cc_info['cc_print_name'] ?>" class="form-control" >
        </div>
        
        <div class="col-12">
            <label>Under</label>
            <div class="w-75 d-inline-block">
            <?php	echo form_dropdown('cc_grp_id', $user_groups_dropdown, set_value("cc_grp_id") ? set_value("cc_grp_id") : $cc_info['under_crs_mst_id'] ,'id="cc_grp_id" class="form-control selectwidget" ');?>	
            </div> 
        </div>
        
        
        
    
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card p-4 my-2">
            <h5 class="pb-2">Payment Info</h5>
            
            <div class="col-12">
                <label>OP. Bal</label>
                <div class="input-group w-75">
                    <input type="number" step="0.01" name="cc_op_bal" id="cc_op_bal" value="<?= set_value("cc_op_bal") ? set_value("cc_op_bal") : abs($cc_info['cc_op_bal']) ?>" class="form-control" >
                    
                    <span class="input-group-text">
                        <input type="radio" name="cc_op_drcr" value="cr" class="form-check-input" <?= $cc_info['cc_op_bal'] <0 ? 'checked' : '' ?> >&nbsp;Cr. &nbsp;&nbsp;&nbsp;
                        <input type="radio" name="cc_op_drcr" value="dr" class="form-check-input" <?= $cc_info['cc_op_bal'] >0 ? 'checked' : '' ?> >&nbsp;Dr.
                    </span>
                </div>
            </div>
            <div class="col-12">
                <label>PY. Bal</label>
                <div class="input-group w-75">
                    <input type="number" step="0.01" name="cc_py_bal" id="cc_py_bal" value="<?= set_value("cc_py_bal") ? set_value("cc_py_bal") : abs($cc_info['cc_py_bal']) ?>" class="form-control" >
                    
                    <span class="input-group-text">
                        <input type="radio" name="cc_py_drcr" value="cr" class="form-check-input" <?= $cc_info['cc_py_bal'] < 0 ? 'checked' : '' ?> >&nbsp;Cr. &nbsp;&nbsp;&nbsp;
                        <input type="radio" name="cc_py_drcr" value="dr" class="form-check-input" <?= $cc_info['cc_py_bal'] > 0 ? 'checked' : '' ?> >&nbsp;Dr.
                    </span>
                </div>
            </div>
        </div>

    </div>
    
    <div class="col-md-12  text-center">
        <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
        <a href="<?php echo $base_url.'cost_centres/list_group';?>" class="btn btn-secondary">QUIT</a>
    </div>
    
    
    
</div>
</form>

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

                if(response.status){
                    alert_success(response.message);
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
							 <div class="alert-error-custom">
								<i class="bi bi-x-circle-fill"></i>
								<div>
								<strong>Error!</strong> <ul>${list}</ul>
								</div>
								<button type="button" class="btn-close" aria-label="Close"></button>
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