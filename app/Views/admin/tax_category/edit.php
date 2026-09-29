<?php $header = array( 	'title' => 'Update Tax Category' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}

    .myform .input-group .form-control, .myform .input-group select{width:100%!important;}
</style>	  
	   <?php $attributes = " id='myform' name='myform' class='needs-validation myform' autocomplete='off' novalidate";
            echo form_open(base_url().'/'.$folder_path.'taxcategory/modify/'.$tax_cat_id, $attributes);
       ?>
	  <div id="validation_errors"></div> 
            <div class=" row">
             <div class="col-6"><h3 class="pb-3">Update Tax Category</h3></div> 
			 <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
            <div class="col-md-6">
                <div class="card p-4 my-2">
                <h5 class="pb-2">General Info</h5>    
                    
               <div class="col-12"><label>Category Name</label><div class="input-group w-75"><?php $data = array(
									  'name'        => 'tax_cat_name',
									  'id'          => 'tax_cat_name',
									  'value'       => $category_info['tax_cat_name'],
									  'maxlength'   => '255',
									  'minlength'   =>  "3",
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?><div data-code="0" class="btn btn-sm btn-primary modifyInputBtn">#</div>
                </div></div>
               <div class="col-12"><label>Category Type</label><div class="input-group w-75"> <?php	                  
				   echo form_dropdown('tax_cat_type', $GSTTaxCategory, $category_info['tax_cat_type'],'id="tax_cat_type" class="form-control required" required');
						?>
                </div></div>
				
				 <div class="col-12"><label>Sub Type</label><div class="input-group w-75"> <?php	                  
				   echo form_dropdown('sub_type_id', $sub_types, $category_info['sub_type_id'],'id="sub_type_id" class="form-control required" required');
						?>
                </div></div>
				
				<div class="col-12"><label>Category Section</label><div class="input-group w-75">
				  <?php $data = array(
									  'name'        => 'tax_cat_section',
									  'id'          => 'tax_cat_section',
									  'value'       => $category_info['tax_cat_section'],
									  'maxlength'   => '255',
									  'minlength'   =>  "3",
									  'class'       => 'form-control',
									  'required'    => true
									  );
									  echo form_input($data);
									  ?>
				  
                </div></div>
			   <div class="col-12">
                 <div id="subTypeContainer"></div>
              </div>
                
               
				</div>
				
			</div>
			
		
         
		 <div class="col-12 text-center">
                 <input type="submit" value="SAVE" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-primary mx-2">
                 <a href="<?php echo base_url().'/'.$folder_path;?>taxcategory" class="btn btn-secondary mx-2">QUIT</a>
              </div>
            
            
            </div>  </form> 
<?php echo view('includes/footer_scripts'); ?>
<script>
    const subTypes = {
  1: {1:"IGST",2:"CGST",3:"SGST",4:"UT GST",5:"Cess (GST)"},
  2: {1:"TDS IGST",2:"TDS CGST",3:"TDS SGST",4:"TDS UT GST"},
  3: {1:"TCS IGST",2:"TCS CGST",3:"TCS SGST",4:"TCS UT GST"},
  4: {1:"TDS"},
  5: {1:"TCS"},
  99:{0:"Other Taxes"}
};

$(document).ready(function () {
  let selectedCat   = "<?= $category_info['tax_cat_type'] ?>";
  let existingRates = <?= json_encode($category_info['tax_cat_rates'] ?? []) ?>; 
  let existingWef   = <?= json_encode($category_info['tax_cat_wef'] ?? []) ?>; 

  // 🔹 Load Sub Type Table
  function loadSubTypeTable(catType, rates, wef) {
    let $container = $('#subTypeContainer');
    $container.empty();

    if (catType && subTypes[catType]) {
      // Header
      $container.append(`
        <div class="row fw-bold mb-2">
          <div class="col-md-4">WEF</div>
          <div class="col-md-4">Sub Category</div>
          <div class="col-md-4">Rate</div>
        </div>
      `);

      $.each(subTypes[catType], function (val, label) {
        let $row = $('<div class="row mb-2"></div>');
        let savedRate = rates && rates[val] ? rates[val] : '';
        let savedWef  = wef && wef[val] ? wef[val] : '';

        // Col 1: WEF
        if (catType == 1 && (val == 2 || val == 3 || val == 4)) {
          $row.append('<div class="col-md-4">&nbsp;</div>');
        } else {
          $row.append(`
            <div class="col-md-4">
              <input type="text" class="datepicker form-control form-control-sm" 
                     name="tax_cat_wef[${val}]" value="${savedWef}" />
            </div>
          `);
          initDatepickers($row[0]);
        }

        // Col 2: Label
        $row.append('<div class="col-md-4"><label class="form-control-plaintext">' + label + '</label></div>');

        // Col 3: Rate
        if (catType == 1 && (val == 2 || val == 3 || val == 4)) {
          $row.append(`
            <div class="col-md-4">
              <input type="text" readonly class="form-control bg-light" 
                     name="tax_cat_rates[${val}]" value="${savedRate}" />
            </div>
          `);
        } else {
          $row.append(`
            <div class="col-md-4">
              <input type="text" class="form-control" 
                     name="tax_cat_rates[${val}]" placeholder="0.00" 
                     value="${savedRate}" 
                     onkeypress="return validateTwoDigits(event, this)" />
            </div>
          `);
        }

        $container.append($row);
      });

      // GST auto-fill logic
      if (catType == 1) {
        let $igst  = $container.find('input[name="tax_cat_rates[1]"]');
        let $cgst  = $container.find('input[name="tax_cat_rates[2]"]');
        let $sgst  = $container.find('input[name="tax_cat_rates[3]"]');
        let $utgst = $container.find('input[name="tax_cat_rates[4]"]');

        $igst.on('input.subtype', function () {
          let val = parseFloat($(this).val()) || 0;
          let half = (val / 2).toFixed(2);
          $cgst.val(half);
          $sgst.val(half);
          $utgst.val(half);
        });
      }

      // Apply sub type freeze logic immediately
      $('#sub_type_id').trigger('change');
    }
  }

  // 🔸 Category change → redraw table
  $('#tax_cat_type').on('change', function () {
    loadSubTypeTable($(this).val(), {}, {}); 
  });

  // 🔸 Sub Type change → Freeze / Unfreeze logic
  $('#sub_type_id').on('change', function () {
    let subType = parseInt($(this).val());
    let $container = $('#subTypeContainer');
    let $rates = $container.find('input[name^="tax_cat_rates["]');

    if (subType === 2 || subType === 3 || subType === 4) {
      // EXEMPT / NIL RATED / NON GST → Freeze & empty
      $rates.each(function () {
        $(this).val('0.00').prop('readonly', true).addClass('bg-light');
      });
    } else {
      // TAXABLE → Unfreeze & allow input
      $rates.each(function () {
        $(this).prop('readonly', false).removeClass('bg-light');
      });

      // Rebind GST autofill if GST category
      let catType = parseInt($('#tax_cat_type').val());
      if (catType === 1) {
        let $igst  = $container.find('input[name="tax_cat_rates[1]"]');
        let $cgst  = $container.find('input[name="tax_cat_rates[2]"]');
        let $sgst  = $container.find('input[name="tax_cat_rates[3]"]');
        let $utgst = $container.find('input[name="tax_cat_rates[4]"]');

        $igst.off('input.subtype').on('input.subtype', function () {
          let val = parseFloat($(this).val()) || 0;
          let half = (val / 2).toFixed(2);
          $cgst.val(half);
          $sgst.val(half);
          $utgst.val(half);
        });
      }
    }
  });

  // 🔸 On edit page load
  if (selectedCat) {
    loadSubTypeTable(selectedCat, existingRates, existingWef);
  }
});


// 🔸 Tempus Dominus Datepicker Init
document.addEventListener('DOMContentLoaded', function () {
  const luxon = tempusDominus?.DateTime;

  function initDatepickers(context = document) {
    context.querySelectorAll('.datepicker').forEach((input) => {
      if (input.dataset.hasPicker) return;

      let defaultDate = new Date();
      if (/^\d{2}-\d{2}-\d{4}$/.test(input.value)) {
        const [dd, mm, yyyy] = input.value.split('-');
        const parsed = luxon?.fromFormat?.(`${dd}-${mm}-${yyyy}`, 'dd-MM-yyyy');
        if (parsed?.isValid) defaultDate = parsed.toJSDate();
      }

      const picker = new tempusDominus.TempusDominus(input, {
        defaultDate,
        allowInputToggle: true,
        display: {
          viewMode: 'calendar',
          components: { calendar: true, date: true, month: true, year: true, decades: true, clock: false },
          buttons: { today: true, clear: true, close: true }
        },
        localization: { locale: 'en-GB', format: 'dd-MM-yyyy' }
      });

      input.addEventListener('pointerup', () => {
        if (!picker.isOpen) requestAnimationFrame(() => picker.show());
      });

      input.addEventListener('change.td', (e) => {
        const jsDate = e.detail?.date?.toJSDate?.();
        if (jsDate instanceof Date && !isNaN(jsDate)) {
          const dd = String(jsDate.getDate()).padStart(2, '0');
          const mm = String(jsDate.getMonth() + 1).padStart(2, '0');
          const yyyy = jsDate.getFullYear();
          input.value = `${dd}-${mm}-${yyyy}`;
        }
      });

      input.dataset.hasPicker = "1";
    });
  }

  window.initDatepickers = initDatepickers;
  initDatepickers();
});


// 🔸 Allow only two digits in rate input
function validateTwoDigits(e, el) {
  const char = String.fromCharCode(e.which);
  if (!/[0-9]/.test(char)) return false;
  if (el.value.length >= 2) return false;
  return true;
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
                $('#myform').attr('disabled', 'disabled');
                $('#validation_errors').html('');
            },
            success: function (response) {
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if(response.status){
					 stop_loader();
                    alert_success(response.message);
                    history.back();
                }
                else{
					 stop_loader();
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
								<strong>Error!</strong>  <ul>${list}</ul>
								</div>
								<button type="button" class="btn-close" aria-label="Close"></button>
							</div>
	                        `;
	                        $('#validation_errors').html(html);
							$('#validation_errors').show();
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