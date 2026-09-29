<?php $header = array('title' => 'Add Project Group' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  

<div id="validation_errors"></div>

<form action="" method="post" id="myform" name="myform" class="needs-validation myform" novalidate>
    
<div class=" row">
    
    <div class="col-6"><h3 class="pb-3">Add Project Group</h3></div> 
    <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
    
    <div class="col-md-8 mx-auto">
        <div class="card p-4 my-2">
            
            <div class="col-12">
                <label>Name</label>
                <input type="text" name="project_grp_name" value="" class="form-control" required>
            </div>
            <div class="col-12">
                <label>Alias</label>
                <input type="text" name="project_grp_alias" value="" class="form-control">
            </div>
            
            <div class="col-12">
                <label>Under Group</label>
                <!-- <div class="w-75 d-inline-block"> -->
                    <select class="form-select" name="under_project_grp_id">
                        <option value=""></option>
                        <?php foreach ($project_groups as $key => $value) { ?>
                            <option value="<?= $value['project_grp_id'] ?>"><?= $value['project_grp_name'] ?></option>
                        <?php } ?>
                    </select>	
                <!-- </div>  -->
            </div>

        </div>
    </div>

    
    <div class="col-md-12  text-center">
        <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
        <a href="<?php echo $base_url.'project/groups';?>" class="btn btn-secondary">QUIT</a>
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
                stop_loader();

                if(response.status){
                    alert_success(response.message);
                    window.location.reload();
                }
                else{
                   
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
								<strong>Error!</strong> ${list}
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