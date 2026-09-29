<?php $header = array(  'title' => 'Chat' ); ?>
<?php echo view($header_file,$header); ?>
<div class="container-fluid p-0">
    <div class="card bg-white">
      <div class="card-header d-flex flex-between-center px-4 py-2 border-bottom">
        <h5 class="mb-0 d-flex align-items-center gap-2"> <img class="rounded-circle " src="<?php echo base_url();?>/public/assets/img/user.jpg" alt=""  style="width: 45px; height: 100%;">
        Name of the person</h5>
        <div class="btn-reveal-trigger">
          <a href="#!" class="link-secondary px-2" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">forum</span></a>
          <ul class="dropdown-menu dropdown-end dropdown-md hoverlist">
            <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="<?php echo base_url();?>/public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> Person Name <span class="material-symbols-outlined hovermenu float-end">close</span></a></li>
            <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="<?php echo base_url();?>/public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> My Name goes here <span class="material-symbols-outlined hovermenu float-end">close</span></a></a></li>
            <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="<?php echo base_url();?>/public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> New User name <span class="material-symbols-outlined hovermenu float-end">close</span></a></a></li>
          <li class="dropdown-item  border-top"> Add Participant</a>
          <input type="text" id="namelist" list="namelist" class="form-control">
        </li>
        <li class="dropdown-item  border-top">Start a New Chat</a></li>
      </ul>
      
      <a href="#!" class="link-secondary position-relative px-2" data-bs-toggle="dropdown" aria-expanded="false">
        <small class="position-absolute p-1 badge rounded-pill bg-danger" style="font-size:60%; right:-3px; top:-9px;">9+ </small><span class="material-symbols-outlined">group</span></a>
        <ul class="dropdown-menu dropdown-end dropdown-md hoverlist">
          <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="<?php echo base_url();?>/public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> Person Name <span class="material-symbols-outlined hovermenu float-end">close</span></a></li>
          <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="<?php echo base_url();?>/public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> My Name goes here <span class="material-symbols-outlined hovermenu float-end">close</span></a></a></li>
          <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="<?php echo base_url();?>/public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> New User name <span class="material-symbols-outlined hovermenu float-end">close</span></a></a></li>
        <li class="dropdown-item  border-top"> Add Participant</a>
        <input type="text" id="namelist" list="namelist" class="form-control">
      </li>
    </ul>
    
    <a href="#!" class="link-secondary px-2" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">apps</span></a>
    <ul class="dropdown-menu">
      <li><a class="dropdown-item p-2" href="#">Clear Chat</a></li><li><a class="dropdown-item p-2" href="#">Download Chat</a></li>
    </ul>
    <a href="#!" class="link-secondary px-2" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">search</span></a>
    <div class="dropdown-menu p-0">
      <input type="text" class="form-control">
    </div>
    
    
    <a href="https://sandbox.aicountly.in/admin/dashboard" class="link-secondary px-2"><span class="material-symbols-outlined">screenshot_monitor</span></a>
    <a href="#!" class="link-secondary pin-chatbtn"><span class="material-symbols-outlined">push_pin</span></a>
  </div>
</div>
<div class="card-body chat p-0">
  <section class="p-0">
    <div class="row m-0">
      <div class="col-md-6 col-lg-5 col-xl-4 my-3">
        <div class="input-group mb-3">
          <input type="search" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="search-addon" />
          <span class="input-group-text border-0" id="search-addon"><span class="material-symbols-outlined">search</span></span>
        </div>
        <div data-mdb-perfect-scrollbar="true" style="position: relative; height:330px">
          <ul class="list-unstyled chatlist mb-0">
            <li class="p-2 border-bottom">
              <div class="d-flex align-items-center justify-content-between position-relative">
                <div class="avatar avatar-m status-online me-3"><img class="rounded-circle" src="<?php echo base_url();?>/public/assets/img/30.webp" alt=""></div>
                <div class="flex-1 me-sm-3">
                  <h5 class="text-black">Jessie Samson</h5>
                  <p class="text-800 fs--2 mb-0">Just 1 hour </p>
                </div>
                <span class="badge bg-danger">10</span>
                <a class="chatmenu p-0 dropdown" data-bs-toggle="dropdown"><span class="material-symbols-outlined">more_vert</a>
                <div class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="support-chat-dropdown">
                  <a class="dropdown-item p-1" href="#!">Mark as Unread</a><a class="dropdown-item p-1" href="#!">Pin</a><a class="dropdown-item p-1" href="#!">Show history</a><a class="dropdown-item p-1" href="#!">Report to Admin</a>
                  <a class="dropdown-item p-1" href="#!">Block & Report</a><a class="dropdown-item p-1" href="#!">Delete Conversation</a></div>
                  
              </div>
            </li>
              
            <li class="p-2 border-bottom">
              <div class="d-flex align-items-center justify-content-between position-relative">
              <div class="avatar avatar-m status-away me-3"><img class="rounded-circle" src="<?php echo base_url();?>/public/assets/img/30.webp" alt=""></div>
              <div class="flex-1 me-sm-3">
                <h5 class="text-black">Harman Singh Chadda</h5>
                <p class="text-800 fs--2 mb-0">2 Days</p>
              </div>
              <a class="chatmenu p-0 dropdown" data-bs-toggle="dropdown"><span class="material-symbols-outlined">more_vert</a>
              <div class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="support-chat-dropdown">
                <a class="dropdown-item p-1" href="#!">Mark as Unread</a><a class="dropdown-item p-1" href="#!">Pin</a><a class="dropdown-item p-1" href="#!">Show history</a><a class="dropdown-item p-1" href="#!">Report to Admin</a>
                <a class="dropdown-item p-1" href="#!">Block & Report</a> <a class="dropdown-item p-1" href="#!">Delete Conversation</a></div>
                
              </div>
            </li>
                  
            <li class="p-2 border-bottom">
              <div class="d-flex align-items-center justify-content-between position-relative">
                <div class="avatar avatar-m status-offline me-3"><img class="rounded-circle" src="<?php echo base_url();?>/public/assets/img/30.webp" alt=""></div>
                <div class="flex-1 me-sm-3">
                  <h5 class="text-black">Richa Kalia</h5>
                  <p class="text-800 fs--2 mb-0">August 7,2021 / 10:41 AM</p>
                </div>
                <a class="chatmenu p-0 dropdown" data-bs-toggle="dropdown"><span class="material-symbols-outlined">more_vert</a>
                <div class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="support-chat-dropdown">
                  <a class="dropdown-item p-1" href="#!">Mark as Unread</a>
                  <a class="dropdown-item p-1" href="#!">Pin</a>
                  <a class="dropdown-item p-1" href="#!">Show history</a>
                  <a class="dropdown-item p-1" href="#!">Report to Admin</a>
                  <a class="dropdown-item p-1" href="#!">Block & Report</a> 
                  <a class="dropdown-item p-1" href="#!">Delete Conversation</a>
                </div>
                        
              </div>
            </li>
          </ul>
      </div>
    </div>
    <div class="col-md-6 col-lg-7 col-xl-8">
      <div class="d-flex flex-column-reverse scrollbar h-100 p-3">
        <div class="card-body" data-mdb-perfect-scrollbar="true" style="position: relative; height: 400px; overflow-y:scroll">
          <div class="d-flex flex-row justify-content-start">
            <img src="<?php echo base_url();?>/public/assets/img/ava3-bg.webp" alt="avatar 1" style="width: 45px; height: 100%;">
            <div class="msg_receive">
              <div class="msg bg-light">Hi
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done">done</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-light">How are you ...???
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-light">What are you doing tomorrow? Can we come up a bar?
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all text-primary">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small ms-3 mb-3 rounded-3 text-muted">23:58</p>
            </div>
          </div>
          <div class="divider d-flex align-items-center mb-4">
            <p class="text-center mx-3 mb-0">Today</p>
          </div>
          <div class="d-flex flex-row justify-content-end mb-4 pt-1">
            <div class="msg_send">
              <div class="msg bg-success">Hiii, I'm good.
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-success">How are you doing?
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-success">Long time no see! Tomorrow   office. will be free on sunday.
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all text-primary">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              
              
              <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">00:06</p>
            </div>
            <img src="<?php echo base_url();?>/public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
          </div>
          <div class="d-flex flex-row justify-content-start mb-4">
            <img src="<?php echo base_url();?>/public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
            <div class="msg_receive">
              <div class="msg bg-light">Okay
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-light">We will go on  Sunday?
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small ms-3 mb-3 rounded-3 text-muted">00:07</p>
            </div>
          </div>
          <div class="d-flex flex-row justify-content-end mb-4">
            <div class="msg_send">
              <div class="msg bg-success">That's awesome!
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-success">I will meet you Sandon Square  sharp at 10 AM
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-success">Is that okay?
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all text-primary">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">00:09</p>
            </div>
            <img src="<?php echo base_url();?>/public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
          </div>
          <div class="d-flex flex-row justify-content-start mb-4">
            <img src="<?php echo base_url();?>/public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
            <div class="msg_receive">
              <div class="msg bg-light">Okay i will meet you on  Sandon Square
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small ms-3 mb-3 rounded-3 text-muted">00:11</p>
            </div>
          </div>
          <div class="d-flex flex-row justify-content-end mb-4">
            <div class="msg_send">
              <div class="msg bg-success">Do you have pictures of Matley Marriage?
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">00:11</p>
            </div>
            <img src="<?php echo base_url();?>/public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
          </div>
          <div class="d-flex flex-row justify-content-start mb-4">
            <img src="<?php echo base_url();?>/public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
            <div class="msg_receive">
              <div class="msg bg-light">Sorry I don't have. i changed my phone.
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small ms-3 mb-3 rounded-3 text-muted">00:13</p>
            </div>
          </div>
          <div class="d-flex flex-row justify-content-end">
            <div class="msg_send">
              <div class="msg bg-success">Okay then see you on sunday!!
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all text-primary">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">00:15</p>
            </div>
            <img src="<?php echo base_url();?>/public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
          </div>
          
        </div>
      </div>
    </div>
  </section>
</div>
    <div class="card-footer d-flex align-items-center gap-2 border-top ps-3 pe-4 py-3">
      <div class="d-flex align-items-center flex-1 gap-3 border rounded-pill px-4">
        <input class="form-control outline-none border-0 flex-1 fs--1 px-0" type="text" placeholder="Write message" />
        <input type="file"  accept="image/*" name="image" id="file"  onchange="loadFile(event)" style="display: none;">
        <label for="file" style="cursor: pointer;"><i id="chatfile" class="material-symbols-outlined" />attachment</i></label>
        </div><button class="btn p-0 border-0 send-btn"><span class="material-symbols-outlined">send</span></button>
      </div>
    </div>
  </div>
  <!--<button id="chat-btn" class="btn p-0 border border-200 btn-support-chat"><span class="fs-0 btn-text ">Chat</span><span class="fa-solid fa-circle text-success fs--1 ms-2"></span><span class="material-symbols-outlined text-primary fs-1">forum</span></button>-->
</div>
<?php echo view('includes/footer_scripts');?>

