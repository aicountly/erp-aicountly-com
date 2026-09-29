<?php $header = array( 	'title' => 'Manage Item Barcode' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>

<style>
 .myform .col-sm-6{padding-bottom:2px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
    .myform .select2 {width:75%!important; }
</style>      
      <?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().'/'.$folder_path.'bar_code/manage_item_barcode/'.$item_barcode['item_id'], $attributes);
       ?>
       <div class=" row">
             <div class="col-md-6 pb-3"><h3 class="pb-3">Create Item Barcode</h3></div>  <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></div> 
       </div>      
        <div class=" row">
            <div class="col-md-6">
                <div class="card p-4 my-2">  
                <p class="d-flex">
                    <label class="w-25">Item</label>
                    <input id="item" type="text" class="form-control" value="<?= $item_barcode['item_name'] ?>" readonly>
                    <input type="hidden" name="item_id" value="<?= $item_barcode['item_id'] ?>">
                </p>
                <!-- <p class="d-flex">
                    <label class="w-25">Unit</label>
                    <select name="unit_id" class="form-control">
                        <option value=""></option>
                   </select>
                </p> -->
               <p class="d-flex"><label class="w-25">Barcode Type</label>
             <select class="form-select required" name="barcode_standard" required>
   
                <?php foreach ($types as $key => $value) { ?>
                    <option <?= $item_barcode['barcode_standard'] == $value ? 'selected' : '' ?> value="<?= $value ?>"><?= $value ?></option>
                <?php } ?>          
             </select>  
             </p>
             <p class="d-flex"><label class="w-25">Barcode Value</label>                
               <input type="text" name="barcode" id="barcode_value" class="form-control w-75" value="<?= $item_barcode['barcode'] ?>" required >
             </p>
               </div>                
              </div>             
            </div>
            <div class="col-md-12 text-center my-3">
                <input type="submit" value="SAVE" class="btn btn-primary mx-2">
                <input type="reset" value="QUIT" onclick="window.history.go(-1); return false;"  class="btn btn-secondary mx-2">
            </div>

        </form>       
    
        </div>       

</div>

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
                $('#submitbtn').attr('disabled', 'disabled');
                $('#validation_errors').html('');
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    alert_success(response.message);
                    window.history.back();
                }
                else{
                    stop_loader();
                    alert_notification(response.message);
                    if(response.errors)
                    {
                        var list = ``;
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
             },
             complete: function() {
                stop_loader();
                $('#submitbtn').attr('disabled', false);
            },
        });
    });

 </script>	
</body>
</html>
