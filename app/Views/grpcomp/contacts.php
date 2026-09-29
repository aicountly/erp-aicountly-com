<?php $header = array( 	'title' => 'Contacts' ); ?>
<?php echo view('includes/header',$header); ?>

<style>.pb-5{padding-bottom:0px!important;}
.notification-card.active {
    background-color: var(--ui-gray-true) !important;
}</style>

<div class="row managepros">
    
    <?php if ($session->getFlashdata('message')) { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $session->getFlashdata('message'); ?>
        </div>
    <?php } ?>
                  
    <div class="col-md-3 col-lg-2 p-0 border-end" style="height:71vh; overflow:auto; background:#f7f9fb">
        <ul class="list-unstyled contactlist p-2">
            <li class="text-warning text-center" id="multiple_contact_operations" style="visibility: hidden">
                <a class="mx-2 text-warning" href="javascript:void(0)" title="Star" data-type="star">
                  <span class="material-symbols-outlined">star</span>
                </a>
                <a class="mx-2 text-info" href="javascript:void(0)" title="Category" data-type="category">
                  <span class="material-symbols-outlined">category</span>
                </a>
                <a class="mx-2 text-danger" href="javascript:void(0)" title="Delete" data-type="delete">
                  <span class="material-symbols-outlined">delete</span>
                </a>
            </li>
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#addcontact"><span class="material-symbols-outlined">person_add</span> New Contact</a>
            </li> 
            <li>
                <a href="#" data-bs-toggle="modal" data-bs-target="#new_notifications">
                    <span class="material-symbols-outlined">notifications</span> Requests 
                    <span class="badge rounded-pill bg-warning" id="notifications_count"></span>
                </a>
            </li>
            <li class="contact" data-status="">
                <a href="javascript:void(0)"><span class="material-symbols-outlined">group</span> All Contacts</a>
            </li> 
            <li class="contact" data-status="deleted">
                <a href="javascript:void(0)"><span class="material-symbols-outlined">person_remove</span> Delete Contacts</a>
            </li> 
        </ul>  
        <div class="accordion" id="accordionExample">
          <div class="accordion-item">
            <p class="fw-600 mb-0">Category
            <a type="button" class="link-dark float-end" data-bs-toggle="modal" data-bs-target="#addlabel"><b style="background:#ccc; border-radius:50%; width:20px; height:20px; line-height:15px; display:block; text-align:center; color:#111;">+</b></a></p>
          </div>
            <ul class="list-unstyled contactlist p-2" id="category_list">
              
            </ul>  

        </div>

    </div>  
 
 

          
    <!---- 2nd Section Starts here ------------>                
    <div class="col-md-3 col-lg-3 pt-3  border-end" style=" background:#fff">
 
   
     <div class="row">   
          <div class="col-12">
             <input type="search" name="search" placeholder="Search by Name" class="form-control">
          </div>
          <div class="col-12">
             <input type="checkbox" id="select_all_contacts" class="form-check-input float-end">
          </div>
     </div>
      <div style="height:71vh; overflow:auto;" id="all_contacts"></div>
						 
						  
                          
                

                      
    </div>  

    <!--- Contacts Div Ends ------------>
                  
 
 
                   
                    
    <div class="col-md-6 col-lg-7 pt-3 ps-3" style="background:#fff">
        <div class="d-block">
          <div id="contact_operations" class="dropdown float-end taskmenus" style="pointer-events: none">
            <a href="javascript:void(0)" id="star"><span class="material-symbols-outlined">star</span></a>     
            <a href="#"  data-bs-toggle="modal" data-bs-target="#editcontact"><span class="material-symbols-outlined">contract_edit</span></a> 
            <a href="javascript:void(0)" id="delete"><span class="material-symbols-outlined">delete</span></a> 
            <a href="javascript:void(0)" id="restore" class="restore" style="display: none"><span class="material-symbols-outlined">settings_backup_restore</span></a>
            <a href="javascript:void(0)" id="delete_forever" style="display: none"><span class="material-symbols-outlined">delete_forever</span></a> 
          </div>
            
          <?php $colors = ['color1','color2','color3','color4','color5','color6','color7','color8'] ?>

          <div class="avatar avatar-l me-3 float-start">
            <div id="my_avatar" class="avatar-name <?php echo $colors[array_rand($colors,1)]; ?> rounded-circle">
                <span id="user_capitals"></span>
            </div>
          </div>
        	<p class="m-0">
        	    <spam id="user_name">Name</spam>
        	    <br>
        	    <small>Organization: 0112345</small>
        	</p>
        
        </div>
          
        <hr>
        <h5>Contact Information</h5>
        <ul class="nav company-info">
            <li class="nav-item">
                  <a class="nav-link px-3" href="javascript:void(0)"> 
                  <span class="me-2 material-symbols-outlined">store</span>
                  <small>Company Name:</small><br> <spam id="company_name"></spam>
                  </a>
            </li>
      		  <li class="nav-item">
      		    <a class="nav-link px-3" href="#!"> 
          		    <span class="me-2 material-symbols-outlined">group</span>
          		    <small>Job Title:</small><br> <spam id="job_title"></spam>
      		    </a>
      		  </li>
        </ul>
        <hr>
                          
                         
        <ul class="nav nav-underline" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active px-3" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="true">Contact</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link px-3" id="chat-tab" data-bs-toggle="tab" data-bs-target="#chat" type="button" role="tab" aria-controls="chat" aria-selected="false" tabindex="-1">Chat History</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link px-3" id="companies-tab" data-bs-toggle="tab" data-bs-target="#companies" type="button" role="tab" aria-controls="companies" aria-selected="false" tabindex="-1">Companies Shared</button>
          </li>
        </ul>
            <div class="tab-content border-top" id="myTabContent p-3">
                <div class="tab-pane fade show active" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    <div class="row my-3">
                        <div class="col-sm-6 d-flex">
                            <span class="material-symbols-outlined me-2 mt-3">call </span>
                            <p class="m-0"><small class="d-block">Call:</small> 
                            <spam id="user_phone"></spam>
                            </p>
                        </div> 
                        <div class="col-sm-6 d-flex">
                            <span class="material-symbols-outlined me-2 mt-3">mail </span>
                            <p class="m-0"><small class="d-block">Email:</small> 
                            <spam id="user_email"></spam>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <h5 class="border-bottom mb-2 py-2">Category 
                          <a href="javascript:void(0)" id="edit_contact_categories" class="text-black" title="edit">
                            <span class="material-symbols-outlined">edit</span>
                          </a>
                          <a href="javascript:void(0)" data-contact_id="" id="update_contact_categories" class="badge bg-success" style="display: none">
                            <!-- <span class="material-symbols-outlined">save_as</span> -->
                            Save
                          </a>
                        </h5>
                        <div id="contact_categories_select" style="display: none">
                            <select class="js-example-basic-multiple2 form-control" name="categories[]" multiple="multiple">
                            </select>
                        </div>
                        
                        <div id="contact_categories"></div>
                    </div>
                     <div class="col-12 pt-2">
                         <h5 class="border-bottom mb-2 py-2">Notes</h5>
                         <p id="contact_notes"></p>
                    </div>
                </div>
          
          
                <div class="tab-pane fade" id="chat" role="tabpanel" aria-labelledby="chat-tab">
                   <section class="py-5">
                    <ul class="timeline list-unstyled">
                      <li class="timeline-item pb-5">
                        <h5 class="fw-bold">Our company starts its operations</h5>
                        <p class="text-muted mb-2 fw-bold">11 March 2020</p>
                        <p>
                          Lorem ipsum dolor sit amet consectetur adipisicing elit. Sit
                          necessitatibus adipisci
                        </p>
                      </li>
                  
                      <li class="timeline-item pb-5">
                        <h5 class="fw-bold">First customer</h5>
                        <p class="text-muted mb-2 fw-bold">19 March 2020</p>
                        <p>
                          Quisque ornare dui nibh, sagittis egestas nisi luctus nec. Sed
                          aliquet laoreet sapien
                        </p>
                      </li>
                  
                      <li class="timeline-item pb-5">
                        <h5 class="fw-bold">Our team exceeds 10 people</h5>
                        <p class="text-muted mb-2 fw-bold">24 June 2020</p>
                        <p>
                          Orci varius natoque penatibus et magnis dis parturient montes,
                          nascetur ridiculus mus.
                        </p>
                      </li>
                  
                      <li class="timeline-item pb-5">
                        <h5 class="fw-bold">Earned the first million $!</h5>
                        <p class="text-muted mb-2 fw-bold">15 October 2020</p>
                        <p>
                         Name pharetra libero nibh, id feugiat tortor rhoncus vitae. Ut suscipit
                          vulputate mattis.
                        </p>
                      </li>
                    </ul>
                  </section>
                </div>
                
        
                <div class="tab-pane fade" id="companies" role="tabpanel" aria-labelledby="companies-tab">
                  <div id="companies">
                      
                      <div class="row p-2">
                          <div class="col-md-6">
                              <h6 class="text-center">Companies shared by me</h6>
                              <table class="table table-striped">
                                  <thead>
                                      <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                      </tr>
                                  </thead>
                                  <tbody id="shared_by_me">
                                      
                                  </tbody>
                              </table>
                          </div>
                          <div class="col-md-6">
                              <h6 class="text-center">Companies shared by contact</h6>
                              <table class="table table-striped">
                                  <thead>
                                      <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                      </tr>
                                  </thead>
                                  <tbody id="shared_by_contact">
                                      
                                  </tbody>
                              </table>
                          </div>
                          
                      </div>
                      
                  </div>
                </div>
            </div>             
        </div>
    </div>

 
 <!--- Add Contact Modal ----------->
 
 <div class="modal fade modal-lg" id="addcontact" tabindex="-1" aria-labelledby="addcontactTitle" aria-hidden="true" style="z-index: 9999;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Contact</h5>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label for="search_email">Enter Email Address</label>
        <div class="input-group">
          <input name="search_email" id="search_email" type="text" class="form-control" placeholder="Search">
          <button id="search_email_go" class="btn btn-success" type="submit">Go</button>
          <button id="search_email_clear" class="btn btn-danger" type="button">Clear</button>
        </div>
      

        <div class="col-md-12" id="search_email_result" style="min-height: 100px">
 
        </div>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div> -->
    </div>
  </div>
</div>   
 <!-- Add Contact Modal Ends ----------->
 
  <!--- Edit Contact Modal ----------->
 
 <div class="modal fade modal-lg" id="editcontact" tabindex="-1" aria-labelledby="editcontactTitle" aria-hidden="true" style="z-index: 9999;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Contact</h5>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <form id="update_contact_form" method="post" action="<?php $base_url; ?>contacts/api/update_contact">
              <input type="hidden" name="uuid_contactid" value="">
        <p>Company Name
            <input name="company_name" placeholder="Company Name" type="text" class="form-control">
        </p>
        <p>Job Title
            <input name="job_title" placeholder="Job Title" type="text" class="form-control">
        </p>
        <p>Category
            <select class="js-example-basic-multiple form-control" name="categories[]" multiple="multiple">
            </select>
        </p>
        <p>Notes
            <textarea class="form-control" name="cantact_notes" placeholder="Notes ..."></textarea>
        </p>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" form="update_contact_form" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>   
<!-- Edit Contact Modal Ends ----------->
 
<!--- New Notification Modal ----------->
 
<div class="modal fade modal-lg" id="new_notifications" tabindex="-1" aria-labelledby="addNotificationTitle" aria-hidden="true" style="z-index: 9999;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Requests</h5>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
       
        <div class="col-md-12" id="new_notification_result">
            
        </div>
      </div>
      <!--<div class="modal-footer">-->
      <!--  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
        <!--<button type="button" class="btn btn-primary">DONE</button>-->
      <!--</div>-->
    </div>
  </div>
</div>   
<!-- New Notification Modal Ends ----------->
 
 
         
<!--Add Category       -->
<div class="modal fade" id="addlabel" tabindex="-1" aria-labelledby="addlabelTitle" aria-hidden="true" style="z-index: 9999">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Category</h5>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <form id="category_form" method="POST" action="<?php echo $base_url ?>contacts/api/add_category">
        <p>Category Name<input name="category_name" type="text" class="form-control" required></p>
        <p class="colors"> Label Color<br>
            <label><input type="radio" name="category_color" value="#3874ff;" checked="checked">
              <span class="checkmark" style="background-color: #c8c2bb;"></span>
            </label>
            <label><input type="radio" name="category_color" value="#6a97ff">
              <span class="checkmark" style="background-color:#6a97ff;"></span>
            </label>
            <label><input type="radio" name="category_color" value="#04aa6d">
              <span class="checkmark" style="background-color:#04aa6d;"></span>
            </label>
            <label><input type="radio" name="category_color" value="#ffc0c7">
              <span class="checkmark" style="background-color:#ffc0c7;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#ef89e4">
              <span class="checkmark" style="background-color:#ef89e4;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#b3de72">
              <span class="checkmark" style="background-color:#b3de72;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#ed898e">
              <span class="checkmark" style="background-color:#ed898e;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#49f5cf">
              <span class="checkmark" style="background-color:#49f5cf;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#d3b8eb">
              <span class="checkmark" style="background-color:#d3b8eb;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#b5886a">
              <span class="checkmark" style="background-color:#b5886a;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#bcac6d">
              <span class="checkmark" style="background-color:#bcac6d;"></span>
            </label>
        </p>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" form="category_form" id="submit_category_btn" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>         
<!--Add Category       --> 
    
<!--Edit Category       -->
<div class="modal fade" id="editlabel" tabindex="-1" aria-labelledby="editlabelTitle" aria-hidden="true" style="z-index: 9999">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Category</h5>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <form id="update_category_form" method="POST" action="<?php echo $base_url ?>contacts/api/update_category">
              <input type="hidden" name="contact_cat_id" value="">
        <p>Category Name<input name="category_name" type="text" class="form-control" required></p>
        <p class="colors"> Label Color<br>
            <label><input type="radio" name="category_color" value="#3874ff;" checked="checked">
              <span class="checkmark" style="background-color: #c8c2bb;"></span>
            </label>
            <label><input type="radio" name="category_color" value="#6a97ff">
              <span class="checkmark" style="background-color:#6a97ff;"></span>
            </label>
            <label><input type="radio" name="category_color" value="#04aa6d">
              <span class="checkmark" style="background-color:#04aa6d;"></span>
            </label>
            <label><input type="radio" name="category_color" value="#ffc0c7">
              <span class="checkmark" style="background-color:#ffc0c7;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#ef89e4">
              <span class="checkmark" style="background-color:#ef89e4;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#b3de72">
              <span class="checkmark" style="background-color:#b3de72;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#ed898e">
              <span class="checkmark" style="background-color:#ed898e;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#49f5cf">
              <span class="checkmark" style="background-color:#49f5cf;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#d3b8eb">
              <span class="checkmark" style="background-color:#d3b8eb;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#b5886a">
              <span class="checkmark" style="background-color:#b5886a;"></span>
            </label>
            
            <label><input type="radio" name="category_color" value="#bcac6d">
              <span class="checkmark" style="background-color:#bcac6d;"></span>
            </label>
        </p>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" form="update_category_form" id="submit_update_category_btn" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>         
<!--Edit Category       --> 

<!--Select Categories       -->
<div class="modal fade" id="selectCategories" tabindex="-1" aria-labelledby="addlabelTitle" aria-hidden="true" style="z-index: 9999">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Categories</h5>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <ul class="list-unstyled contactlist p-2" id="select_category_list">
          </ul>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
        <button type="button" id="submit_select_category_btn" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>         
<!--Select Categories       --> 

<?php echo view('includes/footer_scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(function() {
        $('.js-example-basic-multiple').select2({ width: '100%', dropdownParent: $("#editcontact")});
        $('.js-example-basic-multiple2').select2({ width: '100%', dropdownParent: $("body")});
    });

    
    function get_random_color() {
        var colors = ['color1','color2','color3','color4','color5','color6','color7','color8'];
         return colors[Math.floor((Math.random()*colors.length))];
    }
    function get_capitals(first_name, second_name)
    {
        var first = first_name != '' ? first_name.charAt(0) : '';
        var second = second_name != '' ? second_name.charAt(0) : '';
        return (first+second).toUpperCase();
    }
    function validateEmail(email) {
      var re = /\S+@\S+\.\S+/;
      return re.test(email);
    }
    
    function protect_name(fname, lname) {
        var str = fname + ' ' + lname;
        if(str.length > 23){
            str = str.substring(0, 20) + '...';
        }
        return str;
    }
    function protect_email(email) {
        var str = email;
        if(str.length > 23){
            str = str.substring(0, 20) + '...';
        }
        return str;
    }
        
    $(document).on('click', '.category', function(){
        $('.category').removeClass('active');
        $('.contact').removeClass('active');
        $(this).addClass('active');
        
        $('[name="search"]').val('');
        var id = $(this).data('id');
        all_contact(id);
    });
    $(document).on('click', '.contact', function(){
        $('.contact').removeClass('active');   
        $('.category').removeClass('active')
        $(this).addClass('active');
        
        $('[name="search"]').val('');
        var status = $(this).data('status');
        all_contact(0,status);
    });
    
    $('.contact[data-status=""]').trigger('click'); // execute on page load written below on click function
    
    
    var $request = null;
    var timeoutID = null;
    $(document).on('keyup search','[name="search"]', function(){
        var search = $(this).val().trim();

        clearTimeout(timeoutID);
        timeoutID = setTimeout(function(){
            if($request != null){
                $request.abort();
            }
        
            if($('.contact').hasClass('active')){
                var status = $('.contact.active').data('status');
                all_contact(0,status,search);
            }
            else if($('.category').hasClass('active')){
                var id = $('.category.active').data('id');
                all_contact(id,'',search);
            }
            else{
                all_contact(0,'',search);
            }
            
        }, 250);
        
    })
    
    function all_contact(category_id = 0, status = '', search = '')
    {
        
        $request = $.ajax({
            url: '<?php echo $base_url ?>/contacts/api/all_contacts', 
            type: 'post',
            data: {category_id: category_id, status: status, search: search},
            // datatype: "json",
            cache: false,
            beforeSend: function() {
                $('.contact').css('pointer-events', 'none');
                $('.category').css('pointer-events', 'none');
            },
            success: function (response) {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status == true){
                   var html = ``;
                   var color_class = '';
                   $.each(response.data, function (key, value) 
                   {
                       color_class = get_random_color();
                       html += `
                            <div role="button" class="py-3 notification-card" data-color_class="${color_class}" data-uuid_contactid="${value.uuid_contactid}">

                                <span class="float-end"><input type="checkbox" name="contact_checkbox" class="form-check-input" value="${value.uuid_contactid}" ></span>
                                <div class="d-flex align-items-center justify-content-between position-relative">
                                    <div class="avatar avatar-l me-3">
                                      <div class="avatar-name ${color_class} rounded-circle">
                                          <span>${get_capitals(value.user_firstname, value.user_lastname)}</span>
                                      </div>
                                    </div>

                                     <div class="flex-1 me-sm-3">
                                      <h4 class="fs--1 text-black">
                                          ${protect_name(value.user_firstname , value.user_lastname)}
                                      `;
                                      if(value.contact_status == 'pending'){
                                         html += `<span  style="float: right" class="badge rounded-pill bg-warning">Pending</span>`;
                                      }
                                      if(value.contact_status == 'rejected'){
                                         html += `<span  style="float: right" class="badge rounded-pill bg-danger">Rejected</span>`;
                                      }
                                      if(value.contact_status == 'deleted by other'){
                                         html += `<span  style="float: right" class="badge rounded-pill bg-danger">Removed</span>`;
                                      } 
                                        
                                    html += `</h4>
                                      <p class="mb-0">${protect_email(value.user_regdemail)}</p>

                                      
                                    </div>
                                </div>
                            </div>
                       `;
                    });
                    $('#all_contacts').html(html);
                    $('#all_contacts :first-child').first().trigger('click');
                    $('#multiple_contact_operations').css('visibility', 'hidden');
                    $('#select_all_contacts').prop('checked', false);
                }
                if(response.status == false){
                    var html = `
                        <p>No registered user found</p>
                    `;
                    $('#all_contacts').html(html);
                }
                
            },
            complete: function() {
                $('.contact').css('pointer-events', 'auto');
                $('.category').css('pointer-events', 'auto');
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0 && exception === 'error') {
                    console.log('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    console.log('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    console.log('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    console.log('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    console.log('Time out error.');
                } else if (exception === 'abort') {
                    console.log('Ajax request aborted.');
                } else {
                    console.log('Uncaught Error.\n' + jqXHR.responseText);
                }          
            },
        }); 
        
    }
    
    $(document).on('click','.notification-card',function(){
        
        $(".notification-card").removeClass("active");
        $(this).addClass('active');

        $('#update_contact_categories').css('display', 'none');
        $('#contact_categories_select').css('display', 'none');
        $('#edit_contact_categories').css('display', 'inline-block');
        
        
        var uuid_contactid = $(this).data('uuid_contactid');
        var color_class = $(this).data('color_class');
        if(uuid_contactid)
        {
            $.ajax({
            url: '<?php echo $base_url ?>/contacts/api/contact_details', 
            type: 'post',
            data: {uuid_contactid: uuid_contactid},
            // dataType: "json",
            cache: false,
            beforeSend: function() {
                $(".notification-card").css('pointer-events','none');
            },
            success: function (response) {
                
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                // console.log(response); return;
                if(response.status == true){
                    
                    //edit form
                   $('#contact_operations').css('pointer-events','auto');
                   $('[name="uuid_contactid"]').val(response.data.uuid_contactid);
                   $('[name="company_name"]').val(response.data.contact_compname);
                   $('[name="job_title"]').val(response.data.contact_jobtitle);
                   $('[name="cantact_notes"]').val(response.data.contact_notes);
                   
                   $('[name="categories[]').val([]).trigger("change");
                   if(response.data.cat_ids.length){
                       $('[name="categories[]').val(response.data.cat_ids).trigger("change");
                   }
                  
                   
                   $('#my_avatar').removeClass();
                   $('#my_avatar').addClass('avatar-name rounded-circle '+ color_class);
                   
                   $('#user_name').text(response.data.user_firstname + ' ' + response.data.user_lastname);
                   $('#company_name').text(response.data.contact_compname);
                   $('#job_title').text(response.data.contact_jobtitle);
                   $('#contact_notes').text(response.data.contact_notes);
                   
                    $('#user_phone').text(response.data.user_regdmobile);
                    
                    var htm = `<a href="mailto:${response.data.user_regdemail}">${response.data.user_regdemail}</a>`;
                    $('#user_email').html(htm);
                    
                    var cap = get_capitals(response.data.user_firstname, response.data.user_lastname);
                    $('#user_capitals').text(cap);
                    
                    $('#star').attr('data-id', response.data.uuid_contactid);
                    $('#star').attr('data-star', response.data.contact_star);
                    if(response.data.contact_star == 1)
                        $('#star span').addClass('text-warning');
                    else
                        $('#star span').removeClass('text-warning');
                    
                    $('#delete').attr('data-id', response.data.uuid_contactid);
                    $('#delete_forever').attr('data-id', response.data.uuid_contactid);
                    $('#restore').attr('data-id', response.data.uuid_contactid);

                    if(response.data.contact_status == 'deleted'){
                        $('#delete').css('display', 'none');
                        $('#delete_forever').css('display', 'inline-block');
                        $('#restore').css('display', 'inline-block');
                    }
                        
                    else{
                        $('#delete').css('display', 'inline-block');
                        $('#delete_forever').css('display', 'none');
                        $('#restore').css('display', 'none');
                    }
                        
                    
                    html = ``;
                    if(response.data.category.length > 0)
                    {
                        $.each(response.data.category, function (key, value) 
                        {
                            html += `<div class="labelbtns ps-3 mx-2" style="background:${value.category_color}">${value.category_name} 
                                        <a href="javascript:void" data-contact_id="${response.data.uuid_contactid}" data-category_id="${value.category_id}" class="labelclose remove_single_category">X</a>
                                    </div>`;
                                    
                        });
                    }
                    $('#contact_categories').html(html);

                    $('#update_contact_categories').data('contact_id', response.data.uuid_contactid);
                    
                    html = ``;
                    if(response.data.companies_shared_by_me.length > 0)
                    {
                        $.each(response.data.companies_shared_by_me, function (key, value) 
                        {
                            html += `<tr>
                                        <td>${value.company_code}</td>
                                        <td>${value.company_name}</td>
                                    </tr>`;
                                    
                        });
                    }
                    $('#shared_by_me').html(html);
                    html = ``;
                    if(response.data.companies_shared_by_contact.length > 0)
                    {
                        $.each(response.data.companies_shared_by_contact, function (key, value) 
                        {
                            html += `<tr>
                                        <td>${value.company_code}</td>
                                        <td>${value.company_name}</td>
                                    </tr>`;
                                    
                        });
                    }
                    $('#shared_by_contact').html(html);
                    
                    
                }
                if(response.status == false){
                    
                }
                
            },
            complete: function() {
                $(".notification-card").css('pointer-events','auto');
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }          
            },
        });
        }
        
        
    });
    
    $('[name="search_email"]').keypress(function (e) {
     var key = e.which;
     if(key == 13)  // the enter key code
      {
         searh_email();
      }
    });

    $(document).on('click', '#search_email_go', function() {
        searh_email();
    });

    $(document).on('click', '#search_email_clear', function() {
        $('[name="search_email"]').val('');
        $('#search_email_result').html('');
    });

    function searh_email()
    {
        var email = $('[name="search_email"]').val();
        // return false; 
        if(validateEmail(email))
        {
           $.ajax({
              url: '<?php echo $base_url ?>/contacts/api/contact_by_email', 
              type: 'post',
              data: {email: email},
              // datatype: "json",
              cache: false,
              beforeSend: function() {
                  $('[name="search_email"]').attr('disabled', 'disabled');
              },
              success: function (response) {
                  
                  if (typeof response === 'string') {
                      response = JSON.parse(response);
                  }
                  // console.log(response); return;
                  if(response.status == true){
                     var html = `
                          <div class="py-3 notification-card">
                              <div class="d-flex align-items-center justify-content-between position-relative">
                                  <div class="avatar avatar-l me-3">
                                    <div class="avatar-name ${get_random_color()} rounded-circle"><span>${get_capitals(response.data.user_firstname, response.data.user_lastname)}</span></div>
                                  </div>
                                  <div class="flex-1 me-sm-3">
                                    <h4 class="fs--1 text-black">${response.data.user_firstname + ' ' + response.data.user_lastname}
                                    `;
                                    if(response.data.status1 == 'pending'){
                                       html += `<span class="badge rounded-pill bg-warning">Request Pending</span>`;
                                    }
                                    if(response.data.status2 == 'pending'){
                                       html += `<span class="badge rounded-pill bg-warning">Request Pending (Check Requests)</span>`;
                                    }

                                    if(response.data.status1 == 'accepted' && response.data.status2 == 'accepted'){
                                       html += `<span class="badge rounded-pill bg-success">Already Member</span>`;
                                    }
                                    if(response.data.status1 == 'rejected' && response.data.status2 == 'rejected'){
                                       html += `<span class="badge rounded-pill bg-danger">Request Rejected</span>`;
                                    }
                                    if(response.data.status1 == 'deleted' && response.data.status2 == 'deleted'){
                                       html += `<span class="badge rounded-pill bg-danger">Deleted</span>`;
                                    }

                                    if(response.data.status1 == '' && response.data.status2 == 'rejected'){
                                       html += `<span class="badge rounded-pill bg-danger">Request Rejected by you</span>`;
                                    }
                                    if(response.data.status1 == 'rejected' && response.data.status2 == ''){
                                       html += `<span class="badge rounded-pill bg-danger">Request Rejected by him/her</span>`;
                                    }

                                    if(response.data.status1 == 'deleted' && response.data.status2 == 'deleted by other'){
                                       html += `<span class="badge rounded-pill bg-warning">Deleted by you</span>`;
                                    }
                                    if(response.data.status1 == 'deleted by other' && response.data.status2 == 'deleted'){
                                       html += `<span class="badge rounded-pill bg-danger">Deleted by him/her</span>`;
                                    }

                                    if(response.data.status1 == 'deleted' && response.data.status2 == ''){
                                       html += `<span class="badge rounded-pill bg-danger">Deleted</span>`;
                                    }
                                    if(response.data.status1 == '' && response.data.status2 == 'deleted'){
                                       html += `<span class="badge rounded-pill bg-danger">Deleted</span>`;
                                    }
                                    
                                    if(response.data.status1 == 'deleted by other' && response.data.status2 == ''){
                                       html += `<span class="badge rounded-pill bg-danger">Deleted by him/her</span>`;
                                    }
                                    if(response.data.status1 == '' && response.data.status2 == 'deleted by other'){
                                       html += `<span class="badge rounded-pill bg-danger">Deleted by you</span>`;
                                    }
                                    
                                    if(response.data.status1 == 'deleted by other' && response.data.status2 == 'rejected'){
                                       html += `<span class="badge rounded-pill bg-danger">Deleted</span>`;
                                    }
                                    if(response.data.status1 == 'rejected' && response.data.status2 == 'deleted by other'){
                                       html += `<span class="badge rounded-pill bg-danger">Deleted</span>`;
                                    }

                                    if(response.data.status1 == 'deleted' && response.data.status2 == 'rejected'){
                                       html += `<span class="badge rounded-pill bg-danger">Deleted</span>`;
                                    }
                                    if(response.data.status1 == 'rejected' && response.data.status2 == 'deleted'){
                                       html += `<span class="badge rounded-pill bg-danger">Deleted</span>`;
                                    }
                                    if(response.data.status1 == 'unregistered' && response.data.status2 == 'unregistered'){
                                       html += `<span class="badge rounded-pill bg-danger">Not Registered</span>`;
                                    }
                                    
                                    html += `</h4>
                                    <p class="mb-0">${response.data.user_regdemail}</p>
                                    <p class="mb-0">
                                   `;
                                   if(response.data.status1 == '' && response.data.status2 == ''){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }
                                   if(response.data.status1 == 'rejected' && response.data.status2 == 'rejected'){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }
                                   if(response.data.status1 == 'deleted' && response.data.status2 == 'deleted'){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }

                                   if(response.data.status1 == '' && response.data.status2 == 'rejected'){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }
                                   if(response.data.status1 == 'rejected' && response.data.status2 == ''){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }

                                   if(response.data.status1 == 'deleted' && response.data.status2 == 'deleted by other'){
                                       html += `<button data-id=${response.data.uuid_aicountly} class="btn btn-sm btn-primary restore">Restore</button>`
                                   }
                                   if(response.data.status1 == 'deleted by other' && response.data.status2 == 'deleted'){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }

                                   if(response.data.status1 == 'deleted by other' && response.data.status2 == ''){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }
                                   if(response.data.status1 == '' && response.data.status2 == 'deleted by other'){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }

                                   if(response.data.status1 == 'deleted' && response.data.status2 == ''){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }
                                   if(response.data.status1 == '' && response.data.status2 == 'deleted'){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }

                                   if(response.data.status1 == 'deleted by other' && response.data.status2 == 'rejected'){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }
                                   if(response.data.status1 == 'rejected' && response.data.status2 == 'deleted by other'){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }

                                   if(response.data.status1 == 'deleted' && response.data.status2 == 'rejected'){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }
                                   if(response.data.status1 == 'rejected' && response.data.status2 == 'deleted'){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Send Invite</button>`
                                   }
                                   if(response.data.status1 == 'unregistered' && response.data.status2 == 'unregistered'){
                                       html += `<button data-uuid_aicountly=${response.data.uuid_aicountly} data-email="${email}" class="btn btn-sm btn-success send_invite">Create Account & Send Invite</button>`
                                    }
                                    html += `  
                                    </p>
                                  </div>
                                
                                  <div class="float-end">
                                    <a href="#" class="dropdown dropdown-toggle link-secondary" data-bs-toggle="dropdown" aria-expanded="false">Category</a>
                                      <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                                        <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                                      </ul>   
                                  </div>
                              </div>
                          </div>
                     `;
                      $('#search_email_result').html(html);
                  }
                  if(response.status == false){
                      var html = `
                          <p>No registered user found</p>
                      `;
                      $('#search_email_result').html(html);
                  }
                  
              },
              complete: function() {
                  $('[name="search_email"]').attr('disabled', false);
              },
              error: function (jqXHR, exception) {
                  if (jqXHR.status === 0) {
                      alert('Not connect.\n Verify Network.');
                  } else if (jqXHR.status == 404) {
                      alert('Requested page not found. [404]');
                  } else if (jqXHR.status == 500) {
                      alert('Internal Server Error [500].');
                  } else if (exception === 'parsererror') {
                      alert('Requested JSON parse failed.');
                  } else if (exception === 'timeout') {
                      alert('Time out error.');
                  } else if (exception === 'abort') {
                      alert('Ajax request aborted.');
                  } else {
                      alert('Uncaught Error.\n' + jqXHR.responseText);
                  }          
              },
          }); 
        }
        else{
            alert('Please enter valid email address');
        }
    }
    
    $(document).on('click', '.send_invite', function() {
        var uuid_contactid = $(this).data('uuid_aicountly');
        var email = $(this).data('email');
  
        $.ajax({
            url: '<?php echo $base_url ?>/contacts/api/add_contact', 
            type: 'post',
            data: {uuid_contactid: uuid_contactid, email: email},
            // datatype: "json",
            cache: false,
            beforeSend: function() {
                $('.send_invite').attr('disabled', 'disabled');
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if(response.status == true){
                   $('.send_invite').text('Request Send');
                    $('.contact[data-status=""]').trigger('click');
                }
                if(response.status == false){
                    $('.send_invite').attr('disabled', false);
                }
                
            },
            complete: function() {
                // $('.send_invite').attr('disabled', false);
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }          
            },
        });
    })
    
    check_requests();
    setInterval(check_requests, 1000 * 10); //every 10 seconds
    function check_requests()
    {
        $.ajax({
            url: '<?php echo $base_url ?>/contacts/api/check_requests', 
            type: 'post',
            // datatype: "json",
            cache: false,
            beforeSend: function() {
                
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if(response.status == true){
                   var count = response.data.length;
                   $('#notifications_count').text(count);
                   var html = ``;
                   $.each(response.data, function (key, value) 
                   {
                       html += `
                        <div class="py-3 notification-card">
                            <div class="d-flex align-items-center justify-content-between position-relative">
                                <div class="avatar avatar-l me-3">
                                  <div class="avatar-name ${get_random_color()} rounded-circle"><span>${get_capitals(value.user_firstname, value.user_lastname)}</span></div>
                                </div>
                                <div class="flex-1 me-sm-3">
                                  <h4 class="fs--1 text-black">${value.user_firstname + ' ' + value.user_lastname}</h4>
                                  <p class="mb-0">${value.user_regdemail}</p>
                                </div>
                                <div class="float-end">
                                  <button data-status="accept" data-uuid_aicountly=${value.uuid_aicountly} class="btn btn-sm btn-outline-success process_request">Accept</button>
                                  <button data-status="reject" data-uuid_aicountly=${value.uuid_aicountly} class="btn btn-sm btn-outline-danger process_request">Reject</button>
            
                                </div>
                            </div>
                        </div>
                     `;
                    });
                   
                   
                   
                   $('#new_notification_result').html(html);
                   
                }
                if(response.status == false){
                    $('#notifications_count').text('');
                    $('#new_notification_result').html('<p>No Requests </p>');
                }
                
            },
            complete: function() {
                
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }          
            },
        });
    }
    
    $(document).on('click','.process_request', function(){
        var uuid_aicountly = $(this).data('uuid_aicountly');
        var status = $(this).data('status');
        var obj = $(this);
        
        $.ajax({
            url: '<?php echo $base_url ?>/contacts/api/change_request_status', 
            type: 'post',
            data: {uuid_contactid: uuid_aicountly, status: status},
            // datatype: "json",
            cache: false,
            beforeSend: function() {
                $('.process_request').attr('disabled', 'disabled');
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if(response.status == true){
                   if(status == 'accept'){
                       var html = `<span class="badge bg-success">Request Accepted</span>`;
                   }
                   if(status == 'reject'){
                       var html = `<span class="badge bg-danger">Request Rejected</span>`;
                   }
                    $(obj).parent().closest('div').html(html);
                   all_contact();
                }
                if(response.status == false){
                    
                }
                
            },
            complete: function() {
                $('.process_request').attr('disabled', false);
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }          
            },
        });
    });
    
    $(document).on('submit','#category_form',function(event){
        event.preventDefault();
        var form= $(this);
        
        $.ajax({
            url: form.attr('action'), 
            type: form.attr('method'),
            data: form.serialize(),
            // datatype: "json",
            cache: false,
            beforeSend: function() {
                $('#submit_category_btn').attr('disabled', 'disabled');
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                // console.log(response);return;
                if(response.status == true){
                   alert('Category Added');
                   $('#addlabel').modal('hide');
                   get_categories();
                }
                if(response.status == false){
                    
                }
                
            },
            complete: function() {
                $('#submit_category_btn').attr('disabled', false);
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }          
            },
        }); 
    });
    
    get_categories(); 

    function get_categories(id = 0)
    {
        $.ajax({
            url: '<?php echo $base_url ?>contacts/api/get_categories', 
            type: 'post',
            data: {},
            // datatype: "json",
            cache: false,
            beforeSend: function() {
                
            },
            success: function (response) {
                
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                // console.log(response);return;
                if(response.status == true){

                   set_category_dropdown(response.data);
                   set_select_categories_model(response.data);

                   var html = ``;
                   $.each(response.data, function (key, value) 
                   {
                       html += `
                        <li class="category" data-id="${value.contact_cat_id}">
                            <a href="javascript:void(0)">
                            <span style="color: ${value.contact_category_color}" class="material-symbols-outlined">sell</span>
                            <spam style="color: ${value.contact_category_color}">${value.contact_category}</spam>
                            <span data-id="${value.contact_cat_id}" class="delete_category material-symbols-outlined float-end">Delete</span>
                            <span data-id="${value.contact_cat_id}" data-name="${value.contact_category}" data-color="${value.contact_category_color}" class="edit_category material-symbols-outlined float-end">Edit</span>
                            </a>
                        </li>
                       `;
                       
                   });
                   $('#category_list').html(html);
                   if(id != 0){
                       $('.category[data-id="'+id+'"]').trigger('click');
                   }
                }
                if(response.status == false){
                    $('#category_list').html('');
                }
                
            },
            complete: function() {
                
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }          
            },
        });
    }
    function set_category_dropdown(data)
    {
        var html = ``;
        $.each(data, function (key, value) 
        {
            html += `<option value="${value.contact_cat_id}">${value.contact_category}</option>`;
        });
        $('[name="categories[]"]').html(html);
    }

    function set_select_categories_model(data)
    {
         var html = ``;
         $.each(data, function (key, value) 
         {
             html += `
              <li>
                  
                  <label class="d-block" for="cat_check_${value.contact_cat_id}">
                    <span style="color: ${value.contact_category_color}" class="material-symbols-outlined">sell</span>
                    <spam style="color: ${value.contact_category_color}">${value.contact_category}</spam>
                  
                  <input type="checkbox" name="category_checkbox" class="form-check-input float-end" id="cat_check_${value.contact_cat_id}" value="${value.contact_cat_id}">
                  </lable>
              </li>
             `;
             
         });
         $('#select_category_list').html(html);
    }
    $(document).on('submit','#update_contact_form',function(event){
        event.preventDefault();
        var form= $(this);
        var id = $('[name="uuid_contactid"]').val();
        $.ajax({
            url: form.attr('action'), 
            type: form.attr('method'),
            data: form.serialize(),
            // datatype: "json",
            cache: false,
            beforeSend: function() {
                $('button[form="update_contact_form"]').attr('disabled', 'disabled');
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if(response.status == true){
                   alert('Contact Updated');
                  $('#editcontact').modal('hide');
                  $('.notification-card[data-uuid_contactid="'+id+'"]').trigger('click');
                }
                if(response.status == false){
                    
                }
                
            },
            complete: function() {
                $('button[form="update_contact_form"]').attr('disabled', false);
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }          
            },
        });    
    });
    
    $(document).on('click', '.edit_category', function(){
        var id = $(this).data('id');
        var name = $(this).data('name');
        var color = $(this).data('color');
        
        $('#update_category_form [name="contact_cat_id"]').val(id);
        $('#update_category_form [name="category_name"]').val(name);
        $('#update_category_form [name="category_color"][value="'+color+'"]').prop('checked', true);
        
        $('#editlabel').modal('show');
        
        
    });
    
    $(document).on('click', '.delete_category', function(){
        var id = $(this).data('id');
        
        if(confirm('Are you sure you want to delete this category?')) {
            $.ajax({
                url: '<?php echo $base_url; ?>contacts/api/delete_category', 
                type: 'post',
                data: {id: id},
                // datatype: "json",
                cache: false,
                beforeSend: function() {
                    $('.delete_category').css('pointer-events', 'none');
                },
                success: function (response) {
                    // console.log(response);
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    // console.log(response);return;
                    if(response.status == true){
                       alert('Category Deleted');
                       get_categories();
                       $('.contact[data-status=""]').trigger('click');
                    }
                    if(response.status == false){
                        
                    }
                    
                },
                complete: function() {
                    $('.delete_category').css('pointer-events', 'auto');
                },
                error: function (jqXHR, exception) {
                    if (jqXHR.status === 0) {
                        alert('Not connect.\n Verify Network.');
                    } else if (jqXHR.status == 404) {
                        alert('Requested page not found. [404]');
                    } else if (jqXHR.status == 500) {
                        alert('Internal Server Error [500].');
                    } else if (exception === 'parsererror') {
                        alert('Requested JSON parse failed.');
                    } else if (exception === 'timeout') {
                        alert('Time out error.');
                    } else if (exception === 'abort') {
                        alert('Ajax request aborted.');
                    } else {
                        alert('Uncaught Error.\n' + jqXHR.responseText);
                    }          
                },
            });
        }  
    });
    
    $(document).on('submit','#update_category_form',function(event){
        event.preventDefault();
        var form= $(this);
        
        $.ajax({
            url: form.attr('action'), 
            type: form.attr('method'),
            data: form.serialize(),
            // datatype: "json",
            cache: false,
            beforeSend: function() {
                $('#submit_update_category_btn').attr('disabled', 'disabled');
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                // console.log(response);return;
                if(response.status == true){
                   alert('Category Update');
                   $('#editlabel').modal('hide');
                   
                   var id = $('#update_category_form [name="contact_cat_id"]').val();
                   get_categories(id);
                   
                }
                if(response.status == false){
                    
                }
                
            },
            complete: function() {
                $('#submit_update_category_btn').attr('disabled', false);
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }          
            },
        });  
    });
    
    $(document).on('click', '[data-bs-target="#addlabel"]', function(){
        $('#category_form [name="category_name"]').val('');
    });
    
    
    
    $(document).on('click','#star',function(){
        var id = $(this).attr('data-id');
        var star = $(this).attr('data-star');
        
        $.ajax({
            url: '<?php echo $base_url; ?>contacts/api/star', 
            type: 'post',
            data: {id: id, star: star},
            // datatype: "json",
            cache: false,
            beforeSend: function() {
                $('#star').css('pointer-events', 'none');
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status == true){
                   $('.notification-card[data-uuid_contactid="'+id+'"]').trigger('click');
                }
                if(response.status == false){
                    
                }
                
            },
            complete: function() {
                $('#star').css('pointer-events', 'auto');
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }          
            },
        });
    });
    
    $(document).on('click','#delete',function(){
        var id = $(this).attr('data-id');
        if(confirm('Are you sure you want to delete this contact?')) {
            $.ajax({
            url: '<?php echo $base_url; ?>contacts/api/delete_contact', 
            type: 'post',
            data: {id: id},
            // datatype: "json",
            cache: false,
            beforeSend: function() {
                $('#delete').css('pointer-events', 'none');
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status == true){
                   alert(response.message);
                   $('.contact[data-status=""]').trigger('click');
                }
                if(response.status == false){
                    alert(response.message);
                }
                
            },
            complete: function() {
                $('#delete').css('pointer-events', 'auto');
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }          
            },
        });
        }
    });

    $(document).on('click','#delete_forever',function(){
        var id = $(this).attr('data-id');
        if(confirm('Are you sure you want to delete this contact permanently?')) {
            $.ajax({
            url: '<?php echo $base_url; ?>contacts/api/delete_forever', 
            type: 'post',
            data: {id: id},
            // datatype: "json",
            cache: false,
            beforeSend: function() {
                $('#delete_forever').css('pointer-events', 'none');
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status == true){
                   alert(response.message);
                   $('.contact[data-status="deleted"]').trigger('click');
                }
                if(response.status == false){
                    alert(response.message);
                }
                
            },
            complete: function() {
                $('#delete_forever').css('pointer-events', 'auto');
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }          
            },
        });
        }
    });

    $(document).on('click','.restore',function(){
        var id = $(this).attr('data-id');
        if(confirm('Are you sure you want to restore this contact?')) {
            $.ajax({
              url: '<?php echo $base_url; ?>contacts/api/restore', 
              type: 'post',
              data: {id: id},
              // datatype: "json",
              cache: false,
              beforeSend: function() {
                  $('#restore').css('pointer-events', 'none');
              },
              success: function (response) {
                  // console.log(response);
                  if (typeof response === 'string') {
                      response = JSON.parse(response);
                  }
                  if(response.status == true){
                     alert(response.message);
                     $('button.restore').text('Restored').attr('disabled', 'disabled');
                     $('.contact[data-status=""]').trigger('click');
                  }
                  if(response.status == false){
                      alert(response.message);
                  }
                  
              },
              complete: function() {
                  $('#restore').css('pointer-events', 'auto');
              },
              error: function (jqXHR, exception) {
                  if (jqXHR.status === 0) {
                      alert('Not connect.\n Verify Network.');
                  } else if (jqXHR.status == 404) {
                      alert('Requested page not found. [404]');
                  } else if (jqXHR.status == 500) {
                      alert('Internal Server Error [500].');
                  } else if (exception === 'parsererror') {
                      alert('Requested JSON parse failed.');
                  } else if (exception === 'timeout') {
                      alert('Time out error.');
                  } else if (exception === 'abort') {
                      alert('Ajax request aborted.');
                  } else {
                      alert('Uncaught Error.\n' + jqXHR.responseText);
                  }          
              },
          });
        }
    });

    $(document).on('change','#select_all_contacts',function(e){
        $('input[name="contact_checkbox"]').not(this).prop('checked', this.checked);

        var count = $('input[name="contact_checkbox"]:checked').length;
        if(count > 0)
            $('#multiple_contact_operations').css('visibility', 'visible');
        else
            $('#multiple_contact_operations').css('visibility', 'hidden');

    });
    $(document).on('click','input[name="contact_checkbox"]',function(e){
        e.stopPropagation();
    });

    $(document).on('change','input[name="contact_checkbox"]',function(e){
        
        var count = $('input[name="contact_checkbox"]:checked').length;
        if(count > 0)
            $('#multiple_contact_operations').css('visibility', 'visible');
        else
            $('#multiple_contact_operations').css('visibility', 'hidden');
        
    });

    $(document).on('click', '#multiple_contact_operations a[data-type="star"]', function(){

        var contact_id_array = [];
        $('input:checkbox[name="contact_checkbox"]:checked').each(function(){
            contact_id_array.push($(this).val());
        });
        
        if(contact_id_array.length > 0)
        {
            $.ajax({
              url: '<?php echo $base_url; ?>contacts/api/multiple_star', 
              type: 'post',
              data: {contact_id_array: contact_id_array},
              dataType: 'json',
              beforeSend: function() {
                  $('#multiple_contact_operations').css('pointer-events', 'none');
              },
              success: function (response) {
                  // console.log(response);
                  if (typeof response === 'string') {
                      response = JSON.parse(response);
                  }
                  if(response.status == true){
                     alert(response.message);
                     $('.contact[data-status=""]').trigger('click');
                  }
                  if(response.status == false){
                      alert(response.message);
                  }
                  
              },
              complete: function() {
                  $('#multiple_contact_operations').css('pointer-events', 'auto');
              },
              error: function (jqXHR, exception) {
                  if (jqXHR.status === 0) {
                      alert('Not connect.\n Verify Network.');
                  } else if (jqXHR.status == 404) {
                      alert('Requested page not found. [404]');
                  } else if (jqXHR.status == 500) {
                      alert('Internal Server Error [500].');
                  } else if (exception === 'parsererror') {
                      alert('Requested JSON parse failed.');
                  } else if (exception === 'timeout') {
                      alert('Time out error.');
                  } else if (exception === 'abort') {
                      alert('Ajax request aborted.');
                  } else {
                      alert('Uncaught Error.\n' + jqXHR.responseText);
                  }          
              },
          });
        }
    });

    $(document).on('click', '#multiple_contact_operations a[data-type="delete"]', function(){

        var contact_id_array = [];
        $('input:checkbox[name="contact_checkbox"]:checked').each(function(){
            contact_id_array.push($(this).val());
        });
        
        if(contact_id_array.length > 0)
        {
          $.ajax({
              url: '<?php echo $base_url; ?>contacts/api/multiple_delete', 
              type: 'post',
              data: {contact_id_array: contact_id_array},
              dataType: 'json',
              beforeSend: function() {
                  $('#multiple_contact_operations').css('pointer-events', 'none');
              },
              success: function (response) {
                  // console.log(response);
                  if (typeof response === 'string') {
                      response = JSON.parse(response);
                  }
                  if(response.status == true){
                     alert(response.message);
                     $('.contact[data-status=""]').trigger('click');
                  }
                  if(response.status == false){
                      alert(response.message);
                  }
                  
              },
              complete: function() {
                  $('#multiple_contact_operations').css('pointer-events', 'auto');
              },
              error: function (jqXHR, exception) {
                  if (jqXHR.status === 0) {
                      alert('Not connect.\n Verify Network.');
                  } else if (jqXHR.status == 404) {
                      alert('Requested page not found. [404]');
                  } else if (jqXHR.status == 500) {
                      alert('Internal Server Error [500].');
                  } else if (exception === 'parsererror') {
                      alert('Requested JSON parse failed.');
                  } else if (exception === 'timeout') {
                      alert('Time out error.');
                  } else if (exception === 'abort') {
                      alert('Ajax request aborted.');
                  } else {
                      alert('Uncaught Error.\n' + jqXHR.responseText);
                  }          
              },
          });
        }
    });

    $(document).on('click', '#multiple_contact_operations a[data-type="category"]', function(){
        $('input[name="category_checkbox"]').prop('checked', false);
        var count = $('input[name="contact_checkbox"]:checked').length;
        if(count > 0){
            $('#selectCategories').modal('show');
        }
    });

    $(document).on('click', '#submit_select_category_btn', function(){
        var contact_id_array = [];
        $('input:checkbox[name="contact_checkbox"]:checked').each(function(){
            contact_id_array.push($(this).val());
        });
        if(contact_id_array.length > 0)
        {
            var category_id_array = [];
            $('input:checkbox[name="category_checkbox"]:checked').each(function(){
                category_id_array.push($(this).val());
            });
            if(category_id_array.length > 0)
            {
                $.ajax({
                  url: '<?php echo $base_url; ?>contacts/api/multiple_categories', 
                  type: 'post',
                  data: {contact_id_array: contact_id_array, category_id_array: category_id_array},
                  dataType: 'json',
                  beforeSend: function() {
                      $('#submit_select_category_btn').attr('disabled', 'disabled');
                  },
                  success: function (response) {
                      // console.log(response);
                      if (typeof response === 'string') {
                          response = JSON.parse(response);
                      }
                      if(response.status == true){
                         alert(response.message);
                         $('#selectCategories').modal('hide');
                         $('.contact[data-status=""]').trigger('click');
                      }
                      if(response.status == false){
                          alert(response.message);
                      }
                      
                  },
                  complete: function() {
                      $('#submit_select_category_btn').attr('disabled', false);
                  },
                  error: function (jqXHR, exception) {
                      if (jqXHR.status === 0) {
                          alert('Not connect.\n Verify Network.');
                      } else if (jqXHR.status == 404) {
                          alert('Requested page not found. [404]');
                      } else if (jqXHR.status == 500) {
                          alert('Internal Server Error [500].');
                      } else if (exception === 'parsererror') {
                          alert('Requested JSON parse failed.');
                      } else if (exception === 'timeout') {
                          alert('Time out error.');
                      } else if (exception === 'abort') {
                          alert('Ajax request aborted.');
                      } else {
                          alert('Uncaught Error.\n' + jqXHR.responseText);
                      }          
                  },
                });
            }
            else
            {
                alert('Please select some categories first');
            }
        }
        else
        {
          alert('something went wrong');
        }
    });

    $(document).on('click', '.remove_single_category', function(){
      var contact_id = $(this).data('contact_id');
      var category_id = $(this).data('category_id');
      
      $.ajax({
        url: '<?php echo $base_url; ?>contacts/api/remove_single_category', 
        type: 'post',
        data: {contact_id: contact_id, category_id: category_id},
        dataType: 'json',
        beforeSend: function() {
            $('#remove_single_category').css('pointer-events', 'none');
        },
        success: function (response) {
            // console.log(response);
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }
            if(response.status == true){
               alert(response.message);
               $('.notification-card[data-uuid_contactid="'+contact_id+'"]').trigger('click');
            }
            if(response.status == false){
                alert(response.message);
            }
            
        },
        complete: function() {
            $('#remove_single_category').css('pointer-events', 'auto');
        },
        error: function (jqXHR, exception) {
            if (jqXHR.status === 0) {
                alert('Not connect.\n Verify Network.');
            } else if (jqXHR.status == 404) {
                alert('Requested page not found. [404]');
            } else if (jqXHR.status == 500) {
                alert('Internal Server Error [500].');
            } else if (exception === 'parsererror') {
                alert('Requested JSON parse failed.');
            } else if (exception === 'timeout') {
                alert('Time out error.');
            } else if (exception === 'abort') {
                alert('Ajax request aborted.');
            } else {
                alert('Uncaught Error.\n' + jqXHR.responseText);
            }          
        },
      });
    });

    $(document).on('click', '#edit_contact_categories', function(){
        $(this).css('display', 'none');
        $('#contact_categories_select').css('display', 'block');
        $('#update_contact_categories').css('display', 'inline-block');
    });

    $(document).on('click', '#update_contact_categories', function(){
        var uuid_contactid = $(this).data('contact_id');
        var categories = $('#contact_categories_select select[name="categories[]"]').val();
        
        $.ajax({
            url: '<?php echo $base_url; ?>contacts/api/update_contact_categories', 
            type: 'post',
            data: {uuid_contactid: uuid_contactid, categories: categories},
            dataType: 'json',
            beforeSend: function() {
                $('#remove_single_category').css('pointer-events', 'none');
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status == true){
                    alert(response.message);
                    $('#update_contact_categories').css('display', 'none');
                    $('#contact_categories_select').css('display', 'none');
                    $('#edit_contact_categories').css('display', 'inline-block');
                    $('.notification-card[data-uuid_contactid="'+uuid_contactid+'"]').trigger('click');
                }
                if(response.status == false){
                    alert(response.message);
                }
                
            },
            complete: function() {
                $('#remove_single_category').css('pointer-events', 'auto');
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connect.\n Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }          
            },
          });   
    });


</script>

