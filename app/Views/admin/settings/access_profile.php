<?php $header = array(  'title' => 'Settings' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
.gridtable .row{  display: grid; grid-template-columns:10% 30% 30%  15% 15%;}
.gridtable .foot.row{ grid-template-columns:100% ;}
</style>
<div class="row align-items-center">
    <div class="col-md-6">
        <h3>Settings</h3>
    </div>
    <div class="col-md-6 text-end">        
    </div>   
</div>
  <br>
   <?php echo view('admin/settings/setting_links.php'); ?>
  <br>
<div id="validation_errors"></div>
<!-- Access Model -->
<div class="modal fade" id="templateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="templateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="templateForm" method="post" action="<?= base_url('admin/settings/add_access_profile') ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="templateModalLabel">Create Access Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <p class="d-flex py-2">
            <label class="w-50">Name</label>
            <input type="text" name="name" class="form-control" required>
          </p>

          <p class="d-flex py-2">
            <label class="w-50">Designation</label>
            <input type="text" name="designation" class="form-control" required>
          </p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Save Profile</button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade modal-xl" id="advance" tabindex="-1" aria-labelledby="advanceLabel" aria-hidden="true" style="z-index: 9999">
      <div class="modal-dialog">
        <div class="modal-content">
		<div class="modal-header d-flex justify-content-between align-items-center">
		  <!-- Left: Title -->
		  <div class="flex-grow-1 text-start">
			<h4 class="modal-title">ACCESS MANAGEMENT</h4>
		  </div>

		  <!-- Center: Warning -->
		  <div class="flex-grow-1 text-center">
			<div class="text-warning fw-bold" id="access_mngmnt_warning">
			  <!-- Optional: Warning text goes here -->
			</div>
		  </div>

		  <!-- Right: Close Button -->
		  <div class="flex-grow-1 text-end">
			<button type="button" class="btn-close" data-bs-dismiss="modal">
		  </div>
		</div>

          <div class="modal-body">
            <div class="row">
              <div class="col-lg-2 col-6 border-end">
                <h5 class="pb-2"> Level 1</h5>
                <ul class="list-inline" id="myTab" role="tablist">
                  <?php 
                  foreach ($access_manage as $i => $parent): 
                     $activeClass = $i === 0 ? 'active text-success fw-bold' : '';

                      $targetId = 'lvl1-' . $parent['erp_acs_menu_id'];
                  ?>
                      <li>
                          <a class="<?= $activeClass ?>" style="    font-size: 15px; text-decoration: none; color: black;" id="<?= $targetId ?>-tab" data-bs-toggle="tab" data-bs-target="#<?= $targetId ?>-panel" type="button" role="tab" aria-controls="<?= $targetId ?>-panel" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>">
                              <?= $parent['erp_acs_prof_label'] ?>
                          </a>
                      </li>
                  <?php endforeach; ?>
                  </ul>
              </div>
              
              <div class="col-lg-2 col-6 border-end">
                <div class="tab-content border-0 accordion form-outline p-0" id="myTabContent">
					<?php foreach ($access_manage as $i => $parent): 
						$activePane = $i === 0 ? 'show active' : '';
						$targetId = 'lvl1-' . $parent['erp_acs_menu_id'];
					?>
						<div class="tab-pane fade <?= $activePane ?>" id="<?= $targetId ?>-panel" role="tabpanel" aria-labelledby="<?= $targetId ?>-tab" tabindex="0">
							<h5 class="pb-2"><?= $parent['erp_acs_prof_label'] ?> </h5>
							<ul class="list-inline" role="tablist">
							<?php foreach ($parent['children'] as $j => $child): 
							  $childTargetId = 'lvl2-' . $child['erp_acs_menu_id'];
							  $childActive = ''; // Remove default 'active'
							?>
							<li>
							  <a href="javascript:void(0);"
								 class="level-2-tab <?= $childActive ?>"
								 data-id="<?= $child['erp_acs_menu_id'] ?>">
								 <?= $child['erp_acs_prof_label'] ?>
							  </a>
							</li>
							<?php endforeach; ?>
					     </ul>

					<!-- Optional: you can render inner content here per level 2 -->
					<div class="tab-content">
					<?php foreach ($parent['children'] as $j => $child): 
						$childTargetId = 'lvl2-' . $child['erp_acs_menu_id'];
						$childActivePane = $j === 0 ? 'show active' : '';
					?>
						<!-- <div class="tab-pane fade <?= $childActivePane ?>" id="<?= $childTargetId ?>-panel" role="tabpanel" aria-labelledby="<?= $childTargetId ?>-tab" tabindex="0">
							<p><strong><?= $child['erp_acs_prof_label'] ?> content here</strong></p>
						</div> -->
					<?php endforeach; ?>
					</div>
    </div>
<?php endforeach; ?>
</div>
              </div>
              <div class="col-lg-8">
			   <form id="permissionForm">
			    <input type="hidden" id="access_type"/>				 
            <div class="tab-content border-0 accordion form-outline p-0 dynamicSubPanels">
              <!-- DYNAMIC SUBPANELS WILL LOAD HERE VIA AJAX -->
              <div class="alert alert-info">Please select a permission tab to load access options.</div>
            </div>
			</form>
          </div>
            </div>


            <div class="modal-footer">
              <!--<button type="button" class="btn btn-secondary">Copy Link</button>-->
              <button type="button" class="btn btn-success" id="savePermissionsBtn" style="display:none;">SAVE</button>
            </div>
     

          </div>
        </div>
      </div>
    </div>


    <div class="modal fade modal-xl" id="advance1" tabindex="-1" aria-labelledby="advanceLabel" aria-hidden="true" style="z-index: 9999">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">BACK DATE ENTRY</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">


         
            <div class="row">
              <div class="col-lg-2 col-6 border-end">
                <h5 class="pb-2"> Level 1</h5>
 

                <ul class="list-inline" id="myTab1" role="tablist">
                  <?php foreach ($back_date as $i => $parent): 
                     $activeClass = $i === 0 ? 'active text-success fw-bold' : '';

                      $targetId = 'lvl1-' . $parent['erp_acs_menu_id'];
                  ?>
                      <li>
                          <a class="<?= $activeClass ?>" style="    font-size: 15px; text-decoration: none; color: black;" id="<?= $targetId ?>-tab" data-bs-toggle="tab" data-bs-target="#<?= $targetId ?>-panel" type="button" role="tab" aria-controls="<?= $targetId ?>-panel" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>">
                              <?= $parent['erp_acs_prof_label'] ?>
                          </a>
                      </li>
                  <?php endforeach; ?>
                  </ul>
              </div>
              
              <div class="col-lg-2 col-6 border-end">
                
            
                

                <div class="tab-content border-0 accordion form-outline p-0" id="myTabContent">
<?php foreach ($back_date as $i => $parent): 
    $activePane = $i === 0 ? 'show active' : '';
    $targetId = 'lvl1-' . $parent['erp_acs_menu_id'];
?>
    <div class="tab-pane fade <?= $activePane ?>" id="<?= $targetId ?>-panel" role="tabpanel" aria-labelledby="<?= $targetId ?>-tab" tabindex="0">
        <h5 class="pb-2"><?= $parent['erp_acs_prof_label'] ?> </h5>
        <ul class="list-inline" role="tablist">
        <?php foreach ($parent['children'] as $j => $child): 
  $childTargetId = 'lvl2-' . $child['erp_acs_menu_id'];
  $childActive = ''; // Remove default 'active'
?>
<li>
  <a href="javascript:void(0);"
     class="level-2-tab <?= $childActive ?>"
     data-id="<?= $child['erp_acs_menu_id'] ?>">
     <?= $child['erp_acs_prof_label'] ?>
  </a>
</li>



        <?php endforeach; ?>
        </ul>

        <!-- Optional: you can render inner content here per level 2 -->
        <div class="tab-content">
        <?php foreach ($parent['children'] as $j => $child): 
            $childTargetId = 'lvl2-' . $child['erp_acs_menu_id'];
            $childActivePane = $j === 0 ? 'show active' : '';
        ?>
            <!-- <div class="tab-pane fade <?= $childActivePane ?>" id="<?= $childTargetId ?>-panel" role="tabpanel" aria-labelledby="<?= $childTargetId ?>-tab" tabindex="0">
                <p><strong><?= $child['erp_acs_prof_label'] ?> content here</strong></p>
            </div> -->
        <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
              </div>
              <div class="col-lg-8">
			  
			  <form id="BckDateEntryForm">
			 	  <input type="hidden" id="prfltype"/>
            <div class="tab-content border-0 accordion form-outline p-0 dynamicSubPanels">
              <!-- DYNAMIC SUBPANELS WILL LOAD HERE VIA AJAX -->
              <div class="alert alert-info">Please select a permission tab to load access options.</div>
            </div>
			</form>
			
          </div>
            </div>


            <div class="modal-footer">
              <button type="button" class="btn btn-secondary">Copy Link</button>
              <button type="button" class="btn btn-primary" id="saveBckDtEntryBtn">SAVE</button>
            </div>
     

          </div>
        </div>
      </div>
    </div>
    

    <div class="modal fade modal-xl" id="advance2" tabindex="-1" aria-labelledby="advanceLabel" aria-hidden="true" style="z-index: 9999">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">TRANSACTION APPROVAL</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">


         
            <div class="row">
              <div class="col-lg-2 col-6 border-end">
                <h5 class="pb-2"> Level 1</h5>
 

                <ul class="list-inline" id="myTab2" role="tablist">
                  <?php foreach ($transaction as $i => $parent): 
                     $activeClass = $i === 0 ? 'active text-success fw-bold' : '';

                      $targetId = 'lvl1-' . $parent['erp_acs_menu_id'];
                  ?>
                      <li>
                          <a class="<?= $activeClass ?>" style="    font-size: 15px; text-decoration: none; color: black;" id="<?= $targetId ?>-tab" data-bs-toggle="tab" data-bs-target="#<?= $targetId ?>-panel" type="button" role="tab" aria-controls="<?= $targetId ?>-panel" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>">
                              <?= $parent['erp_acs_prof_label'] ?>
                          </a>
                      </li>
                  <?php endforeach; ?>
                  </ul>
              </div>
              
              <div class="col-lg-2 col-6 border-end">
                
            
                

                <div class="tab-content border-0 accordion form-outline p-0" id="myTabContent">
<?php foreach ($transaction as $i => $parent): 
    $activePane = $i === 0 ? 'show active' : '';
    $targetId = 'lvl1-' . $parent['erp_acs_menu_id'];
?>
    <div class="tab-pane fade <?= $activePane ?>" id="<?= $targetId ?>-panel" role="tabpanel" aria-labelledby="<?= $targetId ?>-tab" tabindex="0">
        <h5 class="pb-2"><?= $parent['erp_acs_prof_label'] ?> </h5>
        <ul class="list-inline" role="tablist">
        <?php foreach ($parent['children'] as $j => $child): 
  $childTargetId = 'lvl2-' . $child['erp_acs_menu_id'];
  $childActive = ''; // Remove default 'active'
?>
<li>
  <a href="javascript:void(0);"
     class="level-2-tab <?= $childActive ?>"
     data-id="<?= $child['erp_acs_menu_id'] ?>">
     <?= $child['erp_acs_prof_label'] ?>
  </a>
</li>



        <?php endforeach; ?>
        </ul>

        <!-- Optional: you can render inner content here per level 2 -->
        <div class="tab-content">
        <?php foreach ($parent['children'] as $j => $child): 
            $childTargetId = 'lvl2-' . $child['erp_acs_menu_id'];
            $childActivePane = $j === 0 ? 'show active' : '';
        ?>
            <!-- <div class="tab-pane fade <?= $childActivePane ?>" id="<?= $childTargetId ?>-panel" role="tabpanel" aria-labelledby="<?= $childTargetId ?>-tab" tabindex="0">
                <p><strong><?= $child['erp_acs_prof_label'] ?> content here</strong></p>
            </div> -->
        <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
              </div>
              <div class="col-lg-8">
			   <form id="TxnAprvForm">
						  <input type="hidden" id="prfltype"/>
            <div class="tab-content border-0 accordion form-outline p-0 dynamicSubPanels">
              <!-- DYNAMIC SUBPANELS WILL LOAD HERE VIA AJAX -->
              <div class="alert alert-info">Please select a permission tab to load access options.</div>
            </div>
			</form>
          </div>
            </div>


            <div class="modal-footer">
              <button type="button" class="btn btn-secondary">Copy Link</button>
              <button type="button" class="btn btn-primary" id="saveTxnAprvlEntryBtn">SAVE</button>
            </div>
     

          </div>
        </div>
      </div>
    </div>


<div class="card mt-2">
    <div class="card-body">

      <div class="collapse listmenu" id="listmenu">
        <!-- <a href="javascript:void(0);" class="btn btn-success">Create Template</a> -->
        <a href="javascript:void(0);" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#templateModal">Create Access Profile</a>

        
        <?php if($is_comp_owner>0){?>
        <a href="javascript:void(0);" class="editbtn btn btn-success">Delete Profile</a>
        <a href="javascript:void(0);" class="editbtn btn btn-success" id="load_default_profiles">Reset Profiles</a>
        <?php } ?>
      </div>

      <div class="mt-5" id="search_grid"  style="margin:auto;"></div>

      

   
    </div>
</div>

  


<?php echo view('includes/footer_scripts'); ?>
<script>
const erp_usr_right_type = {
    1: "NO ACCESS",
    2: "VIEW ONLY",
    3: "CREATE ONLY",
    4: "VIEW & CREATE",
    5: "FULL ACCESS"
};
$(document).ready(function() {
  $('#advance').on('shown.bs.modal', function () {
    $('.dynamicSubPanels').html('<div class="alert alert-info">Please select a permission tab to load access options.</div>');
  
    $('#myTab a').removeClass('active text-success fw-bold');
    $('.level-2-tab').removeClass('active text-success fw-bold');


    const firstLevel1 = $('#myTab a:first');
    if (firstLevel1.length > 0) {
      firstLevel1.addClass('active text-success fw-bold').trigger('click');
    }

  });
});


$(document).ready(function() {
  $('#advance1').on('shown.bs.modal', function () {
    $('.dynamicSubPanels').html('<div class="alert alert-info">Please select a permission tab to load access options.</div>');

    $('#myTab1 a').removeClass('active text-success fw-bold');
    $('.level-2-tab').removeClass('active text-success fw-bold');


    const firstLevel1 = $('#myTab1 a:first');

  
    if (firstLevel1.length > 0) {
      firstLevel1.addClass('active text-success fw-bold').trigger('click');
    }

  });
});


$(document).ready(function() {
  $('#advance2').on('shown.bs.modal', function () {
    $('.dynamicSubPanels').html('<div class="alert alert-info">Please select a permission tab to load access options.</div>');

    $('#myTab2 a').removeClass('active text-success fw-bold');
    $('.level-2-tab').removeClass('active text-success fw-bold');

    // Trigger first Level 1 tab manually
    const firstLevel1 = $('#myTab2 a:first');

  
    if (firstLevel1.length > 0) {
      firstLevel1.addClass('active text-success fw-bold').trigger('click');
    }

    
  });


 
});



$(document).on("click","#load_default_profiles",function(){
    
    Swal.fire({
        title: 'Are you sure?',
        text: "All saved company profiles will be deleted, and the default profiles will be restored.",
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
         show_loader();
         $.post(baseurl+"admin/settings/load_default_profiles",
          {
            token: "<?php echo date('ssYssHs');?>"
          },
          function(response, status){
            // console.log(response);
            if(typeof response == 'string'){
              response = JSON.parse(response);
            }
            stop_loader();
            if(response.status){
                alert_success(response.message);
				//location.reload();
                $("#search_grid").pqGrid('refreshDataAndView');
                
             }else{
				 alert_notification(response.message);
				 return false;
				 
			 }
          });
         
         
        }
      });
	  return false;
})

var voucher_seriesarr = <?php echo $voucher_series;?>;
var comp_acces_users = <?php echo $comp_acces_users;?>;
$(document).ready(function () {

  $(document).on('change', '.set-limit-checkbox', function () {
  const $checkbox = $(this);
  const $parent = $checkbox.closest('.text-center');
  const $inputBox = $parent.find('.limit-box');

  console.log('Checkbox toggled:', $checkbox.data('index'));

  if ($checkbox.is(':checked')) {
    $inputBox.removeClass('d-none hidden').css('display', 'block');
  } else {
    $inputBox.css('display', 'none');
  }
});

$(document).on('change', '.joint-approval-checkbox', function () {
  const $checkbox = $(this);
  const $parent = $checkbox.closest('.text-center');
  const $inputBox = $parent.find('.joint-approval-box');

  console.log('Joint approval checkbox toggled');

  if ($checkbox.is(':checked')) {
    $inputBox.removeClass('d-none hidden').css('display', 'block');
  } else {
    $inputBox.css('display', 'none');
  }
});



$(document).on("click", "#saveBckDtEntryBtn", function () {
	const formData  = $("#BckDateEntryForm").find(":input:enabled").serialize();
	const user_id   = $('#access_type').val();	
    const fullData  = formData + 
    '&user_id=' + encodeURIComponent(user_id);
    
	 
	let valid = false;
    // Loop through all permission selects
    $("input[name^='vchseries']").each(function () {
        if ($(this).val() !== "") {
            valid = true; // at least one selected
            return false; // break out of loop
        }
    });

    if (!valid) {
        alert("Please fill no. of days before saving.");
        return false; // stop form submit
    }else{
	  $.ajax({
			url: baseurl+"admin/settings/save_backdate_entry",
			type: "POST",
			data: fullData,
			success: function (res) {				
				$('#advance1').modal('hide');
				alert_success(res.message || 'Details saved successfully!');
			
		  },
		  error: function (xhr, status, error) {
		    alert("Failed to save permissions.");
		    console.error(error);
		   }
	    });
	}
});


$(document).on("click", "#saveTxnAprvlEntryBtn", function () {
	const formData  = $("#TxnAprvForm").find(":input:enabled").serialize();
	const user_id   = $('#access_type').val();	
    const fullData  = formData + 
    '&user_id=' + encodeURIComponent(user_id);
    /* let valid = false;
    $("input[name^='usraccids']").each(function () {
        if ($(this).val() !== "") {
            valid = true; // at least one selected
            return false; // break out of loop
        }
    });

    if (!valid) {
        alert("Please choose user before saving.");
        return false; 
    }else{ */
	  $.ajax({
			url: baseurl+"admin/settings/save_txnaprvl",
			type: "POST",
			data: fullData,
			success: function (res) {				
				$('#advance2').modal('hide');
				alert_success(res.message || 'Details saved successfully!');
			
		  },
		  error: function (xhr, status, error) {
		    alert("Failed to save permissions.");
		    console.error(error);
		   }
	    });
	
});


$(document).on("click", "#savePermissionsBtn", function () {
	const formData  = $("#permissionForm").find(":input:enabled").serialize();
	const user_id   = $('#access_type').val();	
    const fullData  = formData + 
    '&user_id=' + encodeURIComponent(user_id);
    
	 
	let valid = false;
    // Loop through all permission selects
    $("select[name^='permissions']").each(function () {
        if ($(this).val() !== "") {
            valid = true; // at least one selected
            return false; // break out of loop
        }
    });

    if (!valid) {
        alert("Please select at least one Access Type before saving.");
        return false; // stop form submit
    }else{
	  $.ajax({
			url: baseurl+"admin/settings/save_access_permissions",
			type: "POST",
			data: fullData,
			success: function (res) {				
				$('#advance').modal('hide');
				alert_success(res.message || 'Permissions saved successfully!');
			
		  },
		  error: function (xhr, status, error) {
		    alert("Failed to save permissions.");
		    console.error(error);
		   }
	    });
	}
});

$(document).on('click', '.level-2-tab', function () {
  const childId = $(this).data('id');
  const container = $('.dynamicSubPanels');

  $('.level-2-tab').removeClass('active text-success fw-bold'); // remove from all
  $(this).addClass('active text-success fw-bold'); // add to clicked one

  container.html('<p class="text-muted text-center"><h6>Loading permissions...</h6></p>');
  var access_type = $('#access_type').val();
  var prfltype = $('#prfltype').val();
  $.ajax({
      url: "<?= base_url('admin/settings/ajax_major_level_3') ?>",
      type: "POST",
      data: { id: childId,accesstype:access_type},
      dataType: 'json',
	  timeout: 10000, // 10 seconds
      success: function (response) {
        
        if (response.length === 0) {
          container.html('<p class="text-danger">No Permissions Found</p>');
		  $("#savePermissionsBtn").hide();
          return;
        }
		let menuId = response[0].erp_acs_menu_id;
     $("#savePermissionsBtn").show();
     const grouped = {};
	
if(prfltype==1){
	let SavedBackEnriesList = response[0].saved_bckentries;
		let html = `
		  <table class="table table-bordered align-middle text-center">
			<thead class="table-light">
			  <tr>
				<th class="text-start">Voucher Series</th>
				<th>No. Of. Days</th>
			  </tr>
			</thead>
			<tbody><input type="hidden" name="menuid" value="${menuId}">`;	
		// loop through object and create rows
		Object.entries(voucher_seriesarr).forEach(([id, name]) => {
			let days = SavedBackEnriesList[id] !== undefined ? SavedBackEnriesList[id] : "";
		  html += `
			  <tr>
				<td class="text-start">${name}</td>
				<td>
				  <input type="text" class="form-control text-center" name="vchseries[${id}]" value="${days}" placeholder="Enter days">
				</td>
			  </tr>`;
		});

html += `
    </tbody>
  </table>`;	
		container.html(html);	
		}
	else if(prfltype==3){
	let SavedTxnAprvlsList = response[0].saved_txn_aprvals;
	
	let html = `
			  <table class="table table-bordered align-middle text-center">
				<thead class="table-light">
				  <tr>
					<th class="text-start">Level 3</th>
					<th> Action</th>
				  </tr>
				</thead>
				<tbody><input type="hidden" name="menuid" value="${menuId}">`;	
			  html += `
				  <tr>
					<td class="text-start">APPROVAL REQUIRED</td>
					<td>	
					<div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio" name="txnapprv" id="txnapprv_y" value="1" ${yes_checkboxchkd}>
					  <label class="form-check-label" for="txnapprv_y">YES</label>
					</div>

					<div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio" name="txnapprv" id="txnapprv_n" value="0" ${no_checkboxchkd}>
					  <label class="form-check-label" for="txnapprv_n">NO</label>
					</div>	
					</td>
				  </tr>`;
			

	html += `
		</tbody>
	  </table>`;
	
		 html += `
		  <table class="table table-bordered align-middle text-center" id="aprvtable" style="display:none;">
			<thead class="table-light">
			  <tr>
				<th class="text-start">Approver</th>
				<th>Txn Min. Limit</th>
			  </tr>
			</thead>
			<tbody><input type="hidden" name="menuid" value="${menuId}">`;	
		// loop through object and create rows
		Object.entries(comp_acces_users).forEach(([id, name]) => {
			let txnlimit = SavedTxnAprvlsList[id] !== undefined ? SavedTxnAprvlsList[id] : "";
		  html += `
			  <tr>
				<td class="text-start">${name}</td>
				<td>
				  <input type="text" class="form-control" maxlength="10" text-center" onkeypress="return /^[0-9.]$/.test(event.key) || (event.key === '.' && this.value.indexOf('.') === -1)" name="usraccids[${id}]" value="${txnlimit}" placeholder="Enter Amount">
				</td>
			  </tr>`;
		});

html += `
    </tbody>
  </table>`;	
		container.html(html);

	let hasApproval = Object.values(SavedTxnAprvlsList).some(val => val > 0);
	console.log(hasApproval);
	var yes_checkboxchkd = '';
	var no_checkboxchkd='';
	if (hasApproval) {		
		 $('#advance2 input[name="txnapprv"][value="1"]').prop('checked', true).trigger('change');
		 $("#advance2 #aprvtable").show();
		 yes_checkboxchkd = "checked";
		 no_checkboxchkd = '';
	}else{
		$('#advance2 input[name="txnapprv"][value="0"]').prop('checked', true).trigger('change');
		$("#advance2 #aprvtable").hide();
		yes_checkboxchkd = "";
		 no_checkboxchkd = 'checked';
	}
			
	}	
    else{
		// Step 1: Group by menu_id
			response.forEach(field => {
				const erp_acs_prof_type = field.erp_acs_prof_type;
			   const  saved_acctypes = 	field.saved_profile_types;
			  const menuId = field.erp_acs_menu_id;
			  const erp_usr_right_menu_id = field.erp_usr_right_menu_id;
			  if (!grouped[menuId]) {
				grouped[menuId] = {
				  label: field.erp_acs_prof_label,
				  type: field.erp_acs_prof_type,
				  rights: [],
				  idss : [],
				  selected: [],
				  rightmenuids : field.erp_usr_right_menu_id,
				};
			  }
			  
			   if (saved_acctypes &&
					saved_acctypes.erp_usr_right_menu_id == erp_usr_right_menu_id
				  ) {
					field.select_type = saved_acctypes.erp_usr_right_type_id;
				} else {
					field.select_type = null; // or default value
				}
				

			  grouped[menuId].rights.push(erp_usr_right_type[field.erp_usr_right_type_id]);
			  grouped[menuId].idss.push(field.erp_usr_right_type_id);
			  grouped[menuId].selected.push(field.select_type);
			});
			let html = `
			  <table class="table table-bordered align-middle text-center">
				<thead class="table-light">
				  <tr>
					<th class="text-start">Level 3</th>
					<th>Access Type</th>
				  </tr>
				</thead>
				<tbody>`;

			Object.entries(grouped).forEach(([menuId, data], index) => {
			  const label = data.label;
			  const rights = [...new Set(data.rights)];
			  const idss = [...new Set(data.idss)];
			  const selectName = `permissions[${menuId}]`;
			   const selectedTypeId = [...new Set(data.selected)].reverse();
			 
				const rightmenuids = data.rightmenuids;
				

			//   const configId = response[0].erp_config_id || '';

			  html += `
				<tr class="${index % 2 === 1 ? 'bg-light' : ''}">
				  <td class="text-start fw-semibold">${label}</td>
				  <td>
					<select class="form-select form-select-sm w-auto mx-auto" name="${selectName}">
					  <option value="">-- Select Access --</option>`;

				 
			 rights.forEach((right, i) => {
				const id = idss[i] || '';
				const optionLabel = right.replace(/_/g, ' ').toUpperCase();
				 const selected = selectedTypeId.includes(id) ? 'selected' : '';
				html += `<option value="${id}" ${selected}>${optionLabel}</option>`;
			  });

			  html += `</select>
					<input type="hidden" class="config-input" name="menu_id[${menuId}]" value="${rightmenuids}">
				  </td>
				</tr>`;
			});

			html += `</tbody></table>`;
			container.html(html);

}

      },
	  error: function (xhr, status, error) {
        if (status === "timeout") {
            // Handle timeout separately
            container.html('<p class="text-danger">Request timed out. Please try again later.</p>');
        } else {
         
            container.html('<p class="text-danger">Failed to load permissions.</p>');
        }
		setTimeout(function () {
        $("#advance").modal("hide");  // replace with your modal ID
    }, 3000);
    }
	
    });

});


$(document).on('change', '#advance2 input[name="txnapprv"]', function() {
	if ($(this).val() === "1") {
        $("#advance2 #aprvtable").show();   // show table
    } else {
        $("#advance2 #aprvtable").hide();   // hide table
    }
});


$(document).on('shown.bs.tab', 'a[data-bs-toggle="tab"]', function (e) {
  if ($(this).closest('#level1Tabs').length > 0) {
    const targetPanel = $(this).attr('data-bs-target');
    const firstLevel2 = $(targetPanel).find('a.level-2-tab').first();

    if (firstLevel2.length > 0) {
      $('.level-2-tab').removeClass('active');
      firstLevel2.addClass('active').trigger('click');
    }
  }
});



});


$(document).ready(function () {

  $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    
 
    $('a[data-bs-toggle="tab"]').removeClass('text-success fw-bold');
    $('a[data-bs-toggle="tab"] i').removeClass('bi-check-circle-fill text-success').addClass('bi-circle text-muted');


    $(this).addClass('text-success fw-bold');
    $(this).find('i').removeClass('bi-circle text-muted').addClass('bi-check-circle-fill text-success');
  });
});






      function callmodal(tablid){   
	    $('#access_type').val(tablid);
		$('#prfltype').val(2);
        $('#advance').modal('show');
       }

       function backDate(tablid){   
            $('#access_type').val(tablid);
			$('#prfltype').val(1);
        $('#advance1').modal('show');

        // alert();
       }

       function trans_appr(tablid){   
	    $('#access_type').val(tablid);
        $('#advance2').modal('show');
		$('#prfltype').val(3);

       }
        
        
    var colModel = [

        
            { title: "Access Profile", width: 180, dataIndx: "cmpacsprof_name"},
            { title: "Designation", width: 180, dataIndx: "cmpacsprof_desg"},
            { title: "Manage", width: 180, dataIndx: "action"},
           
        ];
       
var dataModel = {
  location: "remote",
  dataType: "json",
  method: "POST",
  postData: {
    domainid: ""
  },
  url: "<?php echo base_url(); ?>admin/settings/ajax_major_access_profile", // static/default URL
  getData: function (dataJSON) {
    var data = dataJSON.data;
    return {
      curPage: dataJSON.curPage,
      totalRecords: dataJSON.totalRecords,
      data: data
    };
  }
};
        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            dataModel: dataModel,
            colModel : colModel,
            filterModel: { mode: 'OR' },
            numberCell: { show: false },
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".items_row"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
            },
            load:function(event,ui) {
               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
            },
           
        };

        /* newObj.rowDblClick    = function(event, ui) {
            var rowData      = ui.rowData;
            var comp_vch_series_id     = rowData.comp_vch_series_id;
         
            window.location.href= baseurl+'admin/voucher_series/edit/'+comp_vch_series_id;
        }  */

        /* newObj.cellKeyDown = function(evt, ui) {
            var rowData      = ui.rowData;
            var comp_vch_series_id     = rowData.comp_vch_series_id;
            
             if (evt.keyCode==13){

                window.location.href= baseurl+'admin/voucher_series/edit/'+comp_vch_series_id;
            } 
        } */
         
     var $grid = $("#search_grid").pqGrid(newObj);



     $(document).on('click', '.col1_items', function(){

        $('.col1_items').removeClass('text-success');
        $(this).addClass('text-success');

        var text = $(this).text().trim();

        $('.col2_div').addClass('d-none');
        $('.col3_div').addClass('d-none');

        if(text == 'View Rights'){
          $('#view_rights_div').removeClass('d-none');
          $('#view_rights_div :first-child').trigger('click');
        }

        if(text == 'Back Date Entry'){
          $('#back_date_entry_div').removeClass('d-none');
          $('#back_date_entry_div :first-child').trigger('click');
        }

        if(text == 'Printing & Sharing'){
          $('#printing_sharing_div').removeClass('d-none');
          $('#printing_sharing_div :first-child').trigger('click');
        }

        if(text == 'Transaction Limit'){
          $('#transaction_limit_div').removeClass('d-none');
          $('#transaction_limit_div :first-child').trigger('click');
        }
     });

     $(document).on('click', '.col2_items', function(){

        $('.col2_items').removeClass('text-success');
        $(this).addClass('text-success');

        var utext = $('.col1_items.text-success').text().trim();
        var text = $(this).text().trim();

        $('.col3_div').addClass('d-none');

        if(utext == 'View Rights'){
          if(text == 'Masters'){
            $('#view_rights_masters_div').removeClass('d-none');
          }
        }

        //---------------------

        if(utext == 'Back Date Entry'){
          if(text == 'Sales'){
            $('#back_date_entry_sales_div').removeClass('d-none');
          }
        }

        //---------------------

        if(utext == 'Printing & Sharing'){
          if(text == 'Transactions'){
            $('#printing_sharing_transactions_div').removeClass('d-none');
          }
        }

        //---------------------

        if(utext == 'Transaction Limit'){
          if(text == 'Sales'){
            $('#transaction_limit_sales_div').removeClass('d-none');
          }
        }
        
     });


</script>
</body>
</html>