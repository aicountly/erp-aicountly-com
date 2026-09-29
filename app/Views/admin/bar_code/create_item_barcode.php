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
                <p class="d-flex">
                    <label class="w-25">Item</label>
			        <input id="item" type="text" class="form-control" value="" required>
                    <input type="hidden" name="item_id" value="">
			    </p>
                <!-- <p class="d-flex">
                    <label class="w-25">Unit</label>
                    <select name="unit_id" class="form-control">
                        <option value=""></option>
                   </select>
                </p> -->
               <p class="d-flex"><label class="w-25">Barcode Type</label>
			 <select class="form-select required" name="barcode_standard" required>
				<option value=""></option>
                <?php foreach ($types as $key => $value) { ?>
                    <option value="<?= $value ?>"><?= $value ?></option>
                <?php } ?>			
			 </select>	
			 </p>
             <p class="d-flex"><label class="w-25">Barcode Value</label>				
			   <input type="text" name="barcode" id="barcode_value" class="form-control w-75" required >
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

    var item_list = <?= $item_json_file ?>;

    getList(item_list);

    function getList(list) 
    {
        $('input[id="item"]').off('blur'); // unbind event first
        
        if($('input[id="item"]').hasClass('ui-autocomplete-input')) {
            $('input[id="item"]').autocomplete("destroy");
        }
        
        $('input[id="item"]').autocomplete({
            source: list,
            minLength: 0,
            select: function( event, ui ) {
                $(this).val(ui.item.label);
                $('input[name="item_id"]').val(ui.item.item_id);
                get_item_units(ui.item.item_id);
            }
        })
        .on('focus', function(){
            $(this).autocomplete( "search", "" );
            $('input[id="item"]').val('');
            $('input[name="item_id"]').val('');
            // $('select[name="unit_id"]').html('<option value=""></option>');
        })
        .on('blur', function(){
            autoSetItem(list);
        })
    }

    function autoSetItem(list)
    {
        if($('input[id="item"]').val() != '' && $('input[name="item_id"]').val() == '')
        {
            var acc = $('input[id="item"]').val();

            var index = list.findIndex(function(obj) {
                var string = obj.label.toLowerCase();
                var text = acc.toLowerCase();
               return  string.includes(text);
            });

            if(index > -1){
                $('input[id="item"]').val(list[index].label);
                $('input[name="item_id"]').val(list[index].item_id);
                // get_item_units(list[index].item_id);
            }
            else{
                $('input[id="item"]').val('');
                $('input[name="item_id"]').val('');
                // $('select[name="unit_id"]').html('<option value=""></option>');
            }
        }
        if($('input[name="item_id"]').val() == '')
        {
            $('input[id="item"]').val('');
            $('input[name="item_id"]').val('');
            // $('select[name="unit_id"]').html('<option value=""></option>');
        }
    }

    function get_item_units(item_id)
    {  
        $('select[name="unit_id"]').html('<option value=""></option>');

        if(item_id)
        {
            $.ajax({
                url: '<?php echo $base_url; ?>ajax/get_item_unit_list', 
                type: 'POST',
                data: {item_id: item_id},
                dataType: "json",
                beforeSend: function() {
                    
                },
                success: function (response) {
                 
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    if(response.status){
                        if(response.list.length > 0){
                            var html = `<option value=""></option>`;
                            $.each(response.list, function(index, obj){
                                html += `<option value="${obj.unit_id}">${obj.unit_name}</option>`;
                            });
                            $('select[name="unit_id"]').html(html);
                        }
                    }
                    
                },
                complete: function() {
                    
                },
                error: function (jqXHR, exception) {
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
        }   
    }

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
		
		
  
</script>
