<?php $header = array( 	'title' => 'Create Item Barcode' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-sm-6{padding-bottom:2px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
    .myform .select2 {width:75%!important; }
</style>	  
      <?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
             echo form_open(base_url().'/'.$folder_path.'bar_code/create_item_barcode/add', $attributes);
       ?>
       <div class=" row">
             <div class="col-md-6 pb-3"><h3 class="pb-3">Create Item Barcode</h3></div>  <div class="col-6 text-end"><a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a></div> 
       </div>      
        <div class=" row">
               <div class="col-md-6">
                <div class="card p-4 my-2">  
               <p class="d-flex"><label class="w-25">Item</label>
			   <select name="item_id" id="item_id" class="form-control selectwidget required" required>
			   <option value="">Choose</option>
			   <?php
			   $item_json_file = json_decode($item_json_file,true);
			   foreach($item_json_file as $item_row){?>
				<option value="<?php echo $item_row['item_id'];?>"><?php echo $item_row['label'];?></option>			   
			   <?php } ?>
			   </select></p>
               <p class="d-flex"><label class="w-25">Barcode Type</label>
			 <select class="form-control selectwidget required" name="barcode_type" required>
				<option></option>
				<option value="ean-13">EAN-13</option>
				<option value="vpc-a">VPC-A</option>
				<option value="isbn">ISBN</option>
				<option value="issn">ISSN</option>
				<option value="gs1-128">GS1-128</option>
				<option value="qr">QR</option>
				<option value="sscc">SSCC</option>				
			 </select>	
			 </p>
             <p class="d-flex"><label class="w-25">Barcode Value</label>				
			   <input type="text" name="barcode_value" id="barcode_value" class="form-control w-75" required >
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
                    window.location.reload();
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
		
		
    $(document).on('blur','[name="billsndry_name"]', function(){
        var name = $(this).val().trim();
        if(name){
            if(!$('[name="billsndry_allias"]').val().trim())
            {
               $('[name="billsndry_allias"]').val(name); 
            }
            if(!$('[name="billsndry_pname"]').val().trim())
            {
               $('[name="billsndry_pname"]').val(name); 
            }
        }
    });
    $('input[type=radio][name="account_primary"]').change(function() {
        if (this.value == 'Y'){
            $('#group_div').css('display', 'none');
            $('#sundry_group').attr('required', false);
            $('#parent_div').css('display', 'block');
            $('#parent_group').val('');
            $('#parent_group').attr('required', true);
        }
        else if(this.value == 'N'){
            $('#parent_div').css('display', 'none');
            $('#parent_group').attr('required', false); 
            $('#group_div').css('display', 'block');
            $('#sundry_group').val('');
            $('#sundry_group').attr('required', true);
        }
    });
</script>
