<script>

function isDecimal(num) {
         return (num % 1);
      }
function render_qty(str){	

if(str >0 && isDecimal(str)){
var countDecimals = function (value) {
	
	if(value!=''){	
     if(Math.floor(value) === value) return 0;
      else{
		if(typeof value !== 'undefined' && isDecimal(value)){
			return value.toString().split(".")[1].length || 0; 
		}
       else{		
		return value;	   
	   }
	 
	  }
	}
	else
		return "0";
     }
if(str!='' && str >0){
var ttdecimals = countDecimals(str);
if(ttdecimals > 4)
	   return str.toFixed(4);
    else
		return str;	 
}
else
	return "";
}
else
	return str;
}
</script>
<style>
[data-title-tooltip]:hover:after {
    opacity: 1;
    transition: all 0.1s ease 0.5s;
    display: contents;
}
[data-title-tooltip]:after {
    content: attr(data-title-tooltip);
    position: absolute;
    bottom: 0px;
    left: 80%;
    padding: 2px 4px 4px 8px;
    color: #222;
    white-space: pre; 
    -moz-border-radius: 5px; 
    -webkit-border-radius: 5px;  
    border-radius: 5px;  
    -moz-box-shadow: 0px 0px 4px #222;  
    -webkit-box-shadow: 0px 0px 4px #222;  
    box-shadow: 0px 0px 4px #222;  
    background-image: -moz-linear-gradient(top, #f8f8f8, #cccccc);  
    background-image: -webkit-gradient(linear,left top,left bottom,color-stop(0, #f8f8f8),color-stop(1, #cccccc));
    background-image: -webkit-linear-gradient(top, #f8f8f8, #cccccc);  
    background-image: -moz-linear-gradient(top, #f8f8f8, #cccccc);  
    background-image: -ms-linear-gradient(top, #f8f8f8, #cccccc);  
    background-image: -o-linear-gradient(top, #f8f8f8, #cccccc);
    opacity: 0;
    z-index: 99999;
    display: none;
}
[data-title-tooltip] {
    position: relative;
}    
</style>
<style>
.loading {
  opacity: .5;
}
.hasDatepicker {
    position: relative;
    z-index: 99;
}
#loading-mask {
    position: fixed;
    z-index: 1051;
    width: 100%;
    height: 100%;
    top: 0px;
    left: 0px;
    background-color: rgba(200,200,200,0.8);
  }
</style>

<script>
// document.addEventListener("DOMContentLoaded", function() {   introJs().start(); });


function isValidDate(s) {
  var bits = s.split('-');
  var d = new Date(bits[2] + '-' + bits[1] + '-' + bits[0]);
  return !!(d && (d.getMonth() + 1) == bits[1] && d.getDate() == Number(bits[0]));
}

function LoadingSpinner (form, spinnerHTML) {
  form = form || document;
  var button;
  var spinner = document.createElement('div');
  spinner.innerHTML = spinnerHTML;
  spinner = spinner.firstChild;
  form.addEventListener('click', start);
  form.addEventListener('invalid', stop, true);
  function start (event) {
    if (button) {stop();}
    button = event.target;
    if (button.type === 'submit') {
      //LoadingSpinner.start(button, spinner);
    }
  }
  function stop () {
    LoadingSpinner.stop(button, spinner);
  }
  function destroy () {
    stop();
    form.removeEventListener('click', start);
    form.removeEventListener('invalid', stop, true);
  }
  return {start: start, stop: stop, destroy: destroy};
}
LoadingSpinner.start = function (element, spinner) {
  element.classList.add('loading');
  return document.body.appendChild(spinner);
}

LoadingSpinner.stop = function (element, spinner) {
  element.classList.remove('loading');
  return spinner.remove();
}

var loadingSpinnerHTML = '<div id="loading-mask" class="container-fluid"><div class="row" style="height:100%;"><div id="spinner" class="col d-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;display:block;" width="60px" height="60px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" alt="spinner loading icon" class="img-fluid my-auto"><g transform="translate(78,50)"><g transform="rotate(0)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="1"><animateTransform attributeName="transform" type="scale" begin="-1.75s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1.75s"></animate></circle></g></g><g transform="translate(69.79898987322333,69.79898987322332)"><g transform="rotate(45)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.875"><animateTransform attributeName="transform" type="scale" begin="-1.5s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1.5s"></animate></circle></g></g><g transform="translate(50,78)"><g transform="rotate(90)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.75"><animateTransform attributeName="transform" type="scale" begin="-1.25s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1.25s"></animate></circle></g></g><g transform="translate(30.201010126776673,69.79898987322333)"><g transform="rotate(135)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.625"><animateTransform attributeName="transform" type="scale" begin="-1s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1s"></animate></circle></g></g><g transform="translate(22,50)"><g transform="rotate(180)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.5"><animateTransform attributeName="transform" type="scale" begin="-0.75s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-0.75s"></animate></circle></g></g><g transform="translate(30.201010126776666,30.201010126776673)"><g transform="rotate(225)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.375"><animateTransform attributeName="transform" type="scale" begin="-0.5s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-0.5s"></animate></circle></g></g><g transform="translate(49.99999999999999,22)"><g transform="rotate(270)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.25"><animateTransform attributeName="transform" type="scale" begin="-0.25s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-0.25s"></animate></circle></g></g><g transform="translate(69.79898987322332,30.201010126776666)"><g transform="rotate(315)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.125"><animateTransform attributeName="transform" type="scale" begin="0s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="0s"></animate></circle></g></g></svg></div></div></div>';
var exampleForm = document.querySelector('form');
var exampleLoader = new LoadingSpinner(exampleForm, loadingSpinnerHTML);

function stop_loader(){
    $("#loading-mask").remove();
}

function show_loader(){
    var spinnerHTML = '<div id="loading-mask" class="container-fluid"><div class="row" style="height:100%;"><div id="spinner" class="col d-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;display:block;" width="60px" height="60px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" alt="spinner loading icon" class="img-fluid my-auto"><g transform="translate(78,50)"><g transform="rotate(0)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="1"><animateTransform attributeName="transform" type="scale" begin="-1.75s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1.75s"></animate></circle></g></g><g transform="translate(69.79898987322333,69.79898987322332)"><g transform="rotate(45)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.875"><animateTransform attributeName="transform" type="scale" begin="-1.5s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1.5s"></animate></circle></g></g><g transform="translate(50,78)"><g transform="rotate(90)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.75"><animateTransform attributeName="transform" type="scale" begin="-1.25s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1.25s"></animate></circle></g></g><g transform="translate(30.201010126776673,69.79898987322333)"><g transform="rotate(135)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.625"><animateTransform attributeName="transform" type="scale" begin="-1s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1s"></animate></circle></g></g><g transform="translate(22,50)"><g transform="rotate(180)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.5"><animateTransform attributeName="transform" type="scale" begin="-0.75s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-0.75s"></animate></circle></g></g><g transform="translate(30.201010126776666,30.201010126776673)"><g transform="rotate(225)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.375"><animateTransform attributeName="transform" type="scale" begin="-0.5s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-0.5s"></animate></circle></g></g><g transform="translate(49.99999999999999,22)"><g transform="rotate(270)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.25"><animateTransform attributeName="transform" type="scale" begin="-0.25s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-0.25s"></animate></circle></g></g><g transform="translate(69.79898987322332,30.201010126776666)"><g transform="rotate(315)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.125"><animateTransform attributeName="transform" type="scale" begin="0s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="0s"></animate></circle></g></g></svg></div></div></div>';
       var spinner = document.createElement('div');
       spinner.innerHTML = spinnerHTML;
       spinner = spinner.firstChild;
       document.body.appendChild(spinner);
   }
</script>




<footer class="footer position-absolute">
            <div class="row g-0 justify-content-between align-items-center h-100">
              <div class="col-12 col-sm-auto text-center">
                <p class="mb-0 mt-2 mt-sm-0 text-900"><br class="d-sm-none" />Copyright &copy; 2023 aicountly | All rights reserved</p>
              </div>
              <div class="col-12 col-sm-auto text-center">
                <p class="mb-0 text-600"><a href="#!" class="text-500">Terms & Conditions</a></p>
              </div>
            </div>
          </footer>

          
        </div>
      </div>
      
      <div id="chat-container" class="support-chat-container">
        <div class="container-fluid support-chat">
          <div class="card bg-white">
            <div class="card-header d-flex flex-between-center px-4 py-2 border-bottom">
            <h5 class="mb-0 d-flex align-items-center gap-2"> <img class="rounded-circle " src="https://sandbox.aicountly.in/public/assets/img/user.jpg" alt=""  style="width: 45px; height: 100%;">
             Name of the person</h5>
              <div class="btn-reveal-trigger">
             <a href="#!" class="link-secondary px-2" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">forum</span></a>
             <ul class="dropdown-menu dropdown-end dropdown-md hoverlist">
    <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="https://sandbox.aicountly.in/public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> Person Name <span class="material-symbols-outlined hovermenu float-end">close</span></a></li>              
    <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="https://sandbox.aicountly.in/public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> My Name goes here <span class="material-symbols-outlined hovermenu float-end">close</span></a></a></li>              
    <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="https://sandbox.aicountly.in/public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> New User name <span class="material-symbols-outlined hovermenu float-end">close</span></a></a></li>              
    <li class="dropdown-item  border-top"> Add Participant</a>
    <input type="text" id="namelist" list="namelist" class="form-control">
   </li>
    <li class="dropdown-item  border-top">Start a New Chat</a></li>
  </ul> 
            
             <a href="#!" class="link-secondary position-relative px-2" data-bs-toggle="dropdown" aria-expanded="false">
               <small class="position-absolute p-1 badge rounded-pill bg-danger" style="font-size:60%; right:-3px; top:-9px;">9+ </small><span class="material-symbols-outlined">group</span></a>
               <ul class="dropdown-menu dropdown-end dropdown-md hoverlist">
    <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="https://sandbox.aicountly.in/public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> Person Name <span class="material-symbols-outlined hovermenu float-end">close</span></a></li>              
    <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="https://sandbox.aicountly.in/public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> My Name goes here <span class="material-symbols-outlined hovermenu float-end">close</span></a></a></li>              
    <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="https://sandbox.aicountly.in/public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> New User name <span class="material-symbols-outlined hovermenu float-end">close</span></a></a></li>              
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

               
               
               <a href="#!" class="link-secondary px-2"><span class="material-symbols-outlined">screenshot_monitor</span></a>
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
                                <div class="avatar avatar-m status-online me-3"><img class="rounded-circle" src="https://sandbox.aicountly.in/public/assets/img/30.webp" alt=""></div>
                                <div class="flex-1 me-sm-3">
                                  <h5 class="text-black">Jessie Samson</h5>
                                  <p class="text-800 fs--2 mb-0">Just 1 hour </p>
                                </div>
                                <span class="badge bg-danger">10</span>
                                <a class="chatmenu p-0 dropdown" data-bs-toggle="dropdown"><span class="material-symbols-outlined">more_vert</a>
                <div class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="support-chat-dropdown">
                    <a class="dropdown-item p-1" href="#!">Mark as Unread</a><a class="dropdown-item p-1" href="#!">Pin</a><a class="dropdown-item p-1" href="#!">Show history</a><a class="dropdown-item p-1" href="#!">Report to Admin</a>
                    <a class="dropdown-item p-1" href="#!">Block & Report</a> <a class="dropdown-item p-1" href="#!">Delete Conversation</a></div>
             
                              </div>
                      </li>
                      
                      <li class="p-2 border-bottom">
                            <div class="d-flex align-items-center justify-content-between position-relative">
                                <div class="avatar avatar-m status-away me-3"><img class="rounded-circle" src="https://sandbox.aicountly.in/public/assets/img/30.webp" alt=""></div>
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
                                <div class="avatar avatar-m status-offline me-3"><img class="rounded-circle" src="https://sandbox.aicountly.in/public/assets/img/30.webp" alt=""></div>
                                <div class="flex-1 me-sm-3">
                                  <h5 class="text-black">Richa Kalia</h5>
                                  <p class="text-800 fs--2 mb-0">August 7,2021 / 10:41 AM</p>
                                </div>
                                <a class="chatmenu p-0 dropdown" data-bs-toggle="dropdown"><span class="material-symbols-outlined">more_vert</a>
                <div class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="support-chat-dropdown">
                    <a class="dropdown-item p-1" href="#!">Mark as Unread</a><a class="dropdown-item p-1" href="#!">Pin</a><a class="dropdown-item p-1" href="#!">Show history</a><a class="dropdown-item p-1" href="#!">Report to Admin</a>
                    <a class="dropdown-item p-1" href="#!">Block & Report</a> <a class="dropdown-item p-1" href="#!">Delete Conversation</a></div>
             
                              </div>
                      </li>
                    </ul>
                  </div>

                </div>


              <div class="col-md-6 col-lg-7 col-xl-8">

                <div class="d-flex flex-column-reverse scrollbar h-100 p-3">
                <div class="card-body" data-mdb-perfect-scrollbar="true" style="position: relative; height: 400px; overflow-y:scroll">

            <div class="d-flex flex-row justify-content-start">
              <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3-bg.webp"
                alt="avatar 1" style="width: 45px; height: 100%;">
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
  </ul></div>
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
  </ul></div>
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
  </ul></div>
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
  </ul></div>
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
  </ul></div>
                
                
                <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">00:06</p>
              </div>
              <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava4-bg.webp"
                alt="avatar 1" style="width: 45px; height: 100%;">
            </div>

            <div class="d-flex flex-row justify-content-start mb-4">
              <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3-bg.webp"
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
  </ul></div>
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
  </ul></div>
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
  </ul></div>
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
  </ul></div>
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
  </ul></div>
                <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">00:09</p>
              </div>
              <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava4-bg.webp"
                alt="avatar 1" style="width: 45px; height: 100%;">
            </div>

            <div class="d-flex flex-row justify-content-start mb-4">
              <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3-bg.webp"
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
  </ul></div>
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
  </ul></div>
                <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">00:11</p>
              </div>
              <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava4-bg.webp"
                alt="avatar 1" style="width: 45px; height: 100%;">
            </div>

            <div class="d-flex flex-row justify-content-start mb-4">
              <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3-bg.webp"
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
  </ul></div>
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
              <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava4-bg.webp"
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
        <button id="chat-btn" class="btn p-0 border border-200 btn-support-chat"><span class="fs-0 btn-text ">Chat</span><span class="fa-solid fa-circle text-success fs--1 ms-2"></span><span class="material-symbols-outlined text-primary fs-1">forum</span></button>
      </div>
    </main>
    
    
   <div class="rihgtsticky">
       <a href="<?php base_url() ?>/admin/office_tools" class="position-relative">
  <span class="material-symbols-outlined">calendar_month</span>
  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
    99+
  </span>
</a>
<a href="#" data-bs-toggle="modal" data-bs-target="#notesModal"><span class="material-symbols-outlined">sticky_note_2</span></a>
<a href="#" data-bs-toggle="modal" data-bs-target="#calcModal"><span class="material-symbols-outlined">calculate</span></a>
   </div> 
   
   
 <!-- Modal -->
<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="" id="notesModalLabel">Sticky Notes</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-md-flex">
  <div class="col-md-4 col-lg-3 nav flex-column notespill me-3" id="v-notes-tab" role="tablist" aria-orientation="vertical">
    <a class="note-it active" id="v-note1-tab" data-bs-toggle="pill" data-bs-target="#v-note1" role="tab" aria-controls="v-note1" aria-selected="true" style="background:#DBFFD2">Note 1 goes here</a>
    <a class="note-it" id="v-note2-tab" data-bs-toggle="pill" data-bs-target="#v-note2" role="tab" aria-controls="v-note2" aria-selected="false" style="background:#DBF3FB">Note 2</a>
    <a class="note-it" id="v-note3-tab" data-bs-toggle="pill" data-bs-target="#v-note3" role="tab" aria-controls="v-note3" aria-selected="false" style="background:#FFF8D7">Note 3</a>
    <a class="note-it" id="v-note4-tab" data-bs-toggle="pill" data-bs-target="#v-note4" role="tab" aria-controls="v-note4" aria-selected="false" style="background:#FBE2D2">Note 4</a>
    <a class="note-it" id="v-note5-tab" data-bs-toggle="pill" data-bs-target="#v-note5" role="tab" aria-controls="v-note5" aria-selected="false" style="background:#FFEAED">Note 5</a>
    <a class="btn btn-secondary btn-sm m-2"  id="v-note6-tab" data-bs-toggle="pill" data-bs-target="#v-note6" role="tab" aria-controls="v-note6" aria-selected="false">Add New Notes</a>
    </div>
  <div class="col-md-8 col-lg-9 px-2 tab-content" id="v-notes-tabContent">
    <div class="tab-pane fade show active" id="v-note1" role="tabpanel" aria-labelledby="v-note1-tab" tabindex="0">
            <div class="note-it" style="background:#DBFFD2">
             <div contenteditable="true"><h3>1st Note name goes here</h3></div>
               <div contenteditable="true">This is the 1st line.<br>
See, how the text fits here, also if<br>there is a <strong>linebreak</strong> at the end?
<br>It works nicely.
<br>
<br><span style="color: lightgreen">Great</span>.
</div>
            </div>
    <div class="taskmenus"> <a href="#"><span class="material-symbols-outlined">edit</span></a><a href="#"><span class="material-symbols-outlined">delete</span></a></div>
    </div>
    <div class="tab-pane fade" id="v-note2" role="tabpanel" aria-labelledby="v-note2-tab" tabindex="0">
                <div class="note-it" style="background:#DBF3FB">
                    <div contenteditable="true"><h3>2nd Note name goes here</h3></div>
               <div contenteditable="true">This is the second line.<br>
See, how the text fits here, also if<br>there is a <strong>linebreak</strong> at the end?
<br>It works nicely.
<br>
<br><span style="color: lightgreen">Great</span>.
</div>    
                    </div>
        <div class="taskmenus"> <a href="#"><span class="material-symbols-outlined">edit</span></a><a href="#"><span class="material-symbols-outlined">delete</span></a></div> 
    </div>
    <div class="tab-pane fade" id="v-note3" role="tabpanel" aria-labelledby="v-note3-tab" tabindex="0">
                <div class="note-it" style="background:#FFF8D7">
                    <div contenteditable="true"><h3>My 3rd Note goes here</h3></div>
       <div contenteditable="true">This is the third line.<br>
See, how the text fits here, also if<br>there is a <strong>linebreak</strong> at the end?
<br>It works nicely.
<br>
<br><span style="color: lightgreen">Great</span>.
</div>
    </div>
    <div class="taskmenus"><a href="#"><span class="material-symbols-outlined">edit</span></a><a href="#"><span class="material-symbols-outlined">delete</span></a></div>
    </div>
    <div class="tab-pane fade" id="v-note4" role="tabpanel" aria-labelledby="v-note4-tab" tabindex="0">
                <div class="note-it" style="background:#FBE2D2">
                 <div contenteditable="true"><h3>My 4rd Note goes here</h3></div>
       <div contenteditable="true">This is the 4th line.<br>
See, how the text fits here, also if<br>there is a <strong>linebreak</strong> at the end?
<br>It works nicely.
<br>
<br><span style="color: lightgreen">Great</span>.
</div>
                </div>
       <div class="taskmenus"> <a href="#"><span class="material-symbols-outlined">edit</span></a><a href="#"><span class="material-symbols-outlined">delete</span></a></div>   
    </div>
    <div class="tab-pane fade" id="v-note5" role="tabpanel" aria-labelledby="v-note5-tab" tabindex="0">
      <div class="note-it" style="background:#FFEAED">
       <div contenteditable="true"><h3>My 5th Note goes here</h3></div>
       <div contenteditable="true">This is the 5th line.<br>
See, how the text fits here, also if<br>there is a <strong>linebreak</strong> at the end?
<br>It works nicely.
<br>
<br><span style="color: lightgreen">Great</span>.
</div>
    </div>
    <div class="taskmenus"> <a href="#"><span class="material-symbols-outlined">edit</span></a><a href="#"><span class="material-symbols-outlined">delete</span></a></div>
  </div>
  <div class="tab-pane fade" id="v-note6" role="tabpanel" aria-labelledby="v-note6-tab" tabindex="0">
        <input type="text" class="h3 form-control" readonly value="This is My 5th Note">
       <textarea class="form-control" rows="8" readonly>
       s d sdfd dsfdfvd gddf dgfdg fscxd fefvdx fefvds ef vd<br>a sdscdsxr vefvds refvds rvewf ds rfvef dvs rtefvrdsr efvds f
       g vuyfuyk fuydfjytf 67uyr76ufy6ju tr76uti 78rt76t7i8  uk iuhnk, jh gfuykg 7uyigt 76udyft f6yu7t8iu
        </textarea>
    <p class="bgcolor"><label>Select background color</label>
    <input type="radio" class="btn-check" name="color" id="#FBE2D2" autocomplete="off"><label class="btn" for="#FBE2D2" style="background:#FBE2D2"> </label>
<input type="radio" class="btn-check" name="color" id="#DBF3FB" autocomplete="off"><label class="btn" for="#DBF3FB" style="background:#DBF3FB"> </label>
<input type="radio" class="btn-check" name="color" id="#D7FFCC" autocomplete="off"><label class="btn" for="#D7FFCC" style="background:#D7FFCC"> </label>
<input type="radio" class="btn-check" name="color" id="#E2EEB9" autocomplete="off"><label class="btn" for="#E2EEB9" style="background:#E2EEB9"> </label>
<input type="radio" class="btn-check" name="color" id="#FFF4CA" autocomplete="off"><label class="btn" for="#FFF4CA" style="background:#FFF4CA"> </label>
<input type="radio" class="btn-check" name="color" id="#F1CEFD" autocomplete="off"><label class="btn" for="#F1CEFD" style="background:#F1CEFD"> </label>
<input type="radio" class="btn-check" name="color" id="#D6D6D6" autocomplete="off"><label class="btn" for="#D6D6D6" style="background:#D6D6D6"> </label>
    </p>
    
    <div class="taskmenus"> <a href="#"><span class="material-symbols-outlined">check</span></a><a href="#"><span class="material-symbols-outlined">delete</span></a></div>
  </div>


      </div>
    </div>
  </div>
</div></div>
  
    <!-- Modal -->
<div class="modal fade" id="calcModal" tabindex="-1" aria-labelledby="calcModalLabel" aria-hidden="true">
  <div class="modal-dialog">    <div class="modal-content p-3">
      <div class="calculator" align="center">
      <div class="displayBox">
        <p class="displayText borderdark rounded shadow p-2" id="display">0</p>
      </div>
      <div class="row numberPad">
        <div class="col-md-9 numbers">
          <div class="d-flex">
            <button class="btn btn-hlight clear hvr-back-pulse" id="clear">C</button>
            <button class="btn btn-light btn-calc hvr-radial-out" id="sqrt">√</button>
            <button class="btn btn-light btn-calc hvr-radial-out hvr-radial-out" id="square">x<sup>2</sup></button>
          </div>
          <div class="d-flex">
            <button class="btn btn-light btn-calc hvr-radial-out" id="seven">7</button>
            <button class="btn btn-light btn-calc hvr-radial-out" id="eight">8</button>
            <button class="btn btn-light btn-calc hvr-radial-out" id="nine">9</button>
          </div>
          <div class="d-flex">
            <button class="btn btn-light btn-calc hvr-radial-out" id="four">4</button>
            <button class="btn btn-light btn-calc hvr-radial-out" id="five">5</button>
            <button class="btn btn-light btn-calc hvr-radial-out" id="six">6</button>
          </div>
          <div class="d-flex">
            <button class="btn btn-light btn-calc hvr-radial-out" id="one">1</button>
            <button class="btn btn-light btn-calc hvr-radial-out" id="two">2</button>
            <button class="btn btn-light btn-calc hvr-radial-out" id="three">3</button>
          </div>
          <div class="d-flex">
            <button class="btn btn-qlight btn-calc hvr-radial-out" id="plus_minus">&#177;</button>
            <button class="btn btn-qlight btn-calc hvr-radial-out" id="zero">0</button>
            <button class="btn btn-qlight btn-calc hvr-radial-out" id="decimal">.</button>
          </div>
        </div>
        <div class="col-md-3 ps-md-0 operationSide">
          <button id="divide" class="btn btn-qlight btn-operation hvr-fade">÷</button>
          <button id="multiply" class="btn btn-qlight btn-operation hvr-fade">×</button>
          <button id="subtract" class="btn btn-qlight btn-operation hvr-fade">−</button>
          <button id="add" class="btn btn-qlight btn-operation hvr-fade">+</button>
          <button id="equals" class="btn btn-success btn-operation equals hvr-back-pulse">=</button>
        </div>
      </div></div>
    </div>    
    </div>
</div>


<div class="siteloader" style="display:none;"><div class="loader">
    <div class="progress w-100" id="progressbar" role="progressbar" aria-label="Loader Bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="height: 18px">
  <div class="progress-bar progress-bar-striped bg-success progress-bar-animated"></div>
</div>
</div></div>
    
    
    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    <script src="<?php echo base_url();?>/public/assets/js/jquery.min.js"></script>
	<script src="<?php echo base_url();?>/public/assets/js/popper.min.js"></script>
    <script src="<?php echo base_url();?>/public/assets/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url();?>/public/assets/js/aicountly.js"></script>
    <script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="<?php echo base_url();?>/public/assets/js/selectwidget.js"></script>
    <script src="<?php echo base_url();?>/public/assets/js/calculate.js"></script>    
    <?php
		if(isset($below_js)){
		  foreach($below_js as $below_jsfiles){
		 ?>
		<script type="text/javascript" src="<?php echo base_url();?>/public/<?php echo $below_jsfiles;?>"></script>
		<?php
		   }
		}
	?>
	
		
	<script src="
<?php echo base_url();?>/public/assets/alert/sweetalert2.all.min.js
"></script>
<link href="<?php echo base_url();?>/public/assets/alert/sweetalert2.min.css
" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>/public/assets/js/tour.js"></script>
	<script>
	function showtour(){
		 introJs().start();
		
	}
	
    function alert_notification(aler_message){
	   Swal.fire({
        text: aler_message,
        icon: 'error'
      })
	  return false;	
	}	

   function alert_success(aler_message){
     Swal.fire({
        text: aler_message,
        icon: 'success'
      })
    return false; 
  } 

   function confirm_voucher_delete(vouchersarray){
      Swal.fire({
        title: 'Are you sure?',
        text: "Restore possible not possible.",
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes Delete it!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
        
       show_loader();
         
        $.post(baseurl+"/admin/reports/remove_daybook_vouchers",
          {
            vchrids: vouchersarray,
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
                $("#grid_search").pqGrid('refreshDataAndView');
                $('#trash').text('Enable Trash Mode'); 
                $('#trash').data('type', '0');

                var html = ``;
                if(response.errors.length)
                {
                    var list = ``;
                    $.each(response.errors, function(index, value){
                       list += `
                          <li>${value}</li>
                       `;
                    });
                    html = `
                        <div class="alert alert-danger alert-dismissible fade show mt-2" style="max-height: 150px;overflow-y: auto;">
                          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          <ul>
                              <li>${list}</li>
                          </ul>
                      </div>
                    `;
                }  
                $('#validation_errors').html(html);
             }
          });
        
        }
      });
	  return false;
    }
    
  function confirm_company_delete(path){
      Swal.fire({
        title: 'Are you sure?',
        text: "Company will be moved to recycle bin and can only be restored with in 30 days",
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Delete entire company!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
          window.location.href=path;
        }
      });
	  return false;
    }
    
 function confirm_exception_delete(vouchersarray){
       Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
         
       show_loader();
         
        $.post(baseurl+"/admin/reports/remove_exception_vouchers",
          {
            vchrids: vouchersarray,
            token: "<?php echo date('ssYssHs');?>"
          },
          function(data, status){
             if(data=="1")
                location.reload(); 
          });
        }
      });
	  return false;
	
    }      

  function confirm_delete(path){
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
          // window.location.href=path;

          $.ajax({
            url: path, 
            type: 'GET',
            data: {},
            dataType: "json",
            processData: false,
            cache: false,
            contentType: false,
            beforeSend: function() {
                show_loader();
                $('.deletebtn').attr('disabled', 'disabled');
            },
            success: function (response) {
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    alert_success(response.message);
                    if(response.reload == 1)
                      window.location.reload();
                    else
                      window.history.back();
                }
                else{
                    alert_notification(response.message);
                    if(response.errors)
                    {
                        var list = ``;
                        $.each(response.errors, function(index, value){
                           list += `
                              <li>${value}</li>
                           `;
                        });
                        var html = `
                            <div class="alert alert-danger alert-dismissible fade show mt-2" style="max-height: 150px;overflow-y: auto;">
                              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              <ul>
                                  <li>${list}</li>
                              </ul>
                          </div>
                        `;
                        $('#validation_errors').html(html);
                    } 
                }
                
            },
            complete: function() {
                stop_loader();
                $('.deletebtn').attr('disabled', false);
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
      });
	  return false;
    }

	$(".needs-validation").on('submit', function (event) {
	    
	   

	    $(this).addClass('was-validated');
	    error=0;
	    
	     if ( typeof beforeItemSubmit  === 'function') {
            if(!beforeItemSubmit()){
                error = "1";
            }
        }
     	console.log('flag 1');
        var datevalidate=1;
      if($('input').hasClass('datepicker')){
          
          $(".datepicker ").each(function() {
              
         	  var dateFrom   = '<?= grp_comp()->fy_bgn_date_dmy ?>';
            var dateTo     = '<?= grp_comp()->fy_end_date_dmy ?>';
            
            var dateCheck  =  $(this).val();
            var inputid    =  $(this).attr("id");
            
           
            var d1 = dateFrom.split("-");
            var d2 = dateTo.split("-");
            var c  = dateCheck.split("-");
            
            var from_year = d1[2];
            var to_year   = d2[2];
            var check_year = c[2];
            
            if( (check_year==from_year) || (check_year==to_year))
             var datevalidate=1;
            else
             var datevalidate=0;
             
              if(datevalidate=="0"){
              error= "1";
              $("#"+inputid).css('border-color','#ed2000');
              $("#"+inputid).css({'background-image':'url(<?php echo base_url();?>/public/assets/images/error.svg)','background-repeat':'no-repeat','background-position':'right calc(0.3725em + 0.25rem) center','background-size':'calc(0.745em + 0.5rem) calc(0.745em + 0.5rem)'});
              }else{
              	error="0";
                $("#"+inputid).removeAttr("style");  
               $("#"+inputid).css({'background-image':'url(<?php echo base_url();?>/public/assets/images/success.svg)','background-repeat':'no-repeat','background-position':'right calc(0.3725em + 0.25rem) center','background-size':'calc(0.745em + 0.5rem) calc(0.745em + 0.5rem)'});
              
               }
               
          });
          
      }
      console.log('flag 2');
       if($('select').hasClass('required')){
          $(".required ").each(function() {
           var txtbx_val = $(this).val();
           if(txtbx_val.length=="0"){
              error= "1";
              $(this).css('border-color','#ed2000');
               $(this).css({'background-image':'url(<?php echo base_url();?>/public/assets/images/error.svg)','background-repeat':'no-repeat','background-position':'right calc(0.3725em + 0.25rem) center','background-size':'calc(0.745em + 0.5rem) calc(0.745em + 0.5rem)'});
              }else{
              	error="0";
              	 $(this).css({'background-image':'url(<?php echo base_url();?>/public/assets/images/success.svg)','background-repeat':'no-repeat','background-position':'right calc(0.3725em + 0.25rem) center','background-size':'calc(0.745em + 0.5rem) calc(0.745em + 0.5rem)'});
                 $(this).css('border-color','#25b003');
                $(this).parent('select').removeClass('required');  
                 }
             });
           }
           
       console.log('flag 3'); 
        
	  if ($(this)[0].checkValidity() === false  ||  error=="1" ) {console.log('flag 4');
		event.preventDefault();
		event.stopPropagation();
	
            return false;
       
	  }
	  else if ($(this)[0].checkValidity() === true  && error=="1") {console.log('flag 5');
	       
	    event.preventDefault();
		event.stopPropagation();
            return false;
      
	  }
	  else
	  {console.log('flag 6');
	    show_loader();
	    return true;
	      
	  }
	  console.log('flag 7');
    return false;
	});
</script>
<style>
    .grid_footer_color{background: #DEE9EF;font-weight:bold}
    .yellowcolor{background-color:yellow;}
</style>
<script type='text/javascript'>


  

        $(document).ready(function(){
            $("#email").focus();
            
             var displayBox = document.getElementById('display')
  var hasEvaluated = false

            
            $(document).keyup(function(e) {
                
                 if(e.keyCode == '27'){
                        
                      
                          if($('body').hasClass('modal-open')==true && $('#calcModal').hasClass('show') ){
                              displayBox.innerHTML = '0';
                         }
                      else{
						  
						  $( ".btn-outline-success" ).each(function( index ) {
							    //console.log($(this).text());
								 if($(this).text()=="Back" || $(this).text()=="« Back"){
							       window.open($(this).attr("href"), '_self').focus();							
						           }
					            });
                        //history.back();
                      }
                     e.stopPropagation();
                    }
                    
                    
           //  alert(e.which);
             if($('body').hasClass('modal-open')==true && $('#calcModal').hasClass('show') ){
                 if(e.which=='97'){
                      checkLength(displayBox.innerHTML)
                      clickNumbers(1);
                     
                 }
               if(e.which=='98'){
                      checkLength(displayBox.innerHTML)
                      clickNumbers(2);
                     
                 }
           if(e.which=='99'){
                      checkLength(displayBox.innerHTML)
                      clickNumbers(3);
                     
                 }         
          if(e.which=='100'){
                      checkLength(displayBox.innerHTML)
                      clickNumbers(4);
                     
                 }
          
           if(e.which=='101'){
                      checkLength(displayBox.innerHTML)
                      clickNumbers(5);
                     
                 }
          
        if(e.which=='102'){
                      checkLength(displayBox.innerHTML)
                      clickNumbers(6);
                     
                 }
                 
     if(e.which=='103'){
                      checkLength(displayBox.innerHTML)
                      clickNumbers(7);
                     
                 }
     if(e.which=='104'){
                      checkLength(displayBox.innerHTML)
                      clickNumbers(8);
                     
                 }
                 
     if(e.which=='105'){
                      checkLength(displayBox.innerHTML)
                      clickNumbers(9);
                     
                 }             
     if(e.which=='96'){
                      checkLength(displayBox.innerHTML)
                      clickNumbers(0);
                     
                 }  
                 
     if(e.which=='111'){
                  evaluate()
    checkLength(displayBox.innerHTML)
    displayBox.innerHTML += '÷'
                     
                 } 
    if(e.which=='106'){
                     evaluate()
    checkLength(displayBox.innerHTML)
    displayBox.innerHTML += '×'
                     
                 }             
   if(e.which=='109'){
                     evaluate()
    checkLength(displayBox.innerHTML)
    displayBox.innerHTML += '-'
                     
                 }  
      
     if(e.which=='13'){
                   
                    evaluate()
    hasEvaluated = true
                     
                 }    
                 
     if(e.which=='107'){
                   
                     evaluate()
    checkLength(displayBox.innerHTML)
    displayBox.innerHTML += '+'
                     
                 }   
     if(e.which=='110'){
                      checkLength(displayBox.innerHTML)
                       if (displayBox.innerHTML.indexOf('.') === -1 ||
      (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('+') !== -1) ||
      (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('-') !== -1) ||
      (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('×') !== -1) ||
      (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('÷') !== -1)) {
      clickNumbers('.')
    }
                     
                 }    
                 
                 
                 
             }
             
         });
         
            
        /*   hot keys to menu */
        
       
        
        
       /*
        var keys = {};

        $(document).keydown(function(e) {
        
            keys[e.which] = true;
          
            if (e.which=='17') { 
                   if($(".topnavbar li").hasClass("hotkeys"))
                    $(".topnavbar li").removeClass("hotkeys");
                else{   
                for(var i=2;i<=8;i++){
                    $(".topnavbar li:nth-child("+i+")").addClass("hotkeys");
                   }
                }
            }
             
            if(e.which=='77' && e.altKey) { // Transaction div on press alt+m
                 $(".topnavbar #masterdiv_menu").show();
             }
             else
                $(".topnavbar #masterdiv_menu").hide();
            
            if(e.which=='84' && e.altKey) { // Transaction div on press alt+t
                $(".topnavbar #transactionsdiv_menu").show();
             }
             else
               $(".topnavbar #transactionsdiv_menu").hide();
            
            if(e.which=='88' && e.altKey) { // Taxes div on press alt+x
               $(".topnavbar #taxesdiv_menu").show();
             }
             else
               $(".topnavbar #taxesdiv_menu").hide();
            
            
            if(e.which=='84' && e.altKey) { // MIS div on press alt+x
               $(".topnavbar #misdiv_menu").show();
             }
            else
               $(".topnavbar #misdiv_menu").hide();
               
           if(e.which=='84' && e.altKey) { // Reports div on press alt+x
               $(".topnavbar #reportsdiv_menu").show();
             }
           else
               $(".topnavbar #reportsdiv_menu").hide();
               
          if(e.which=='84' && e.altKey) { // Register div on press alt+x
             $(".topnavbar #registerdiv_menu").show();
             }
          else
            $(".topnavbar #registerdiv_menu").hide();
               
          if(e.which=='84' && e.altKey) { // Audit div on press alt+x
               $(".topnavbar #auditdiv_menu").show();
             }
          else
              $(".topnavbar #auditdiv_menu").hide();      
               
                   
            if($(".topnavbar li").hasClass("hotkeys")){
              if(e.which=='77')
                 $(".topnavbar #masterdiv_menu").show();
              else
                 $(".topnavbar #masterdiv_menu").hide();
             }
            
            //if alt key on and then press any key to open div   
            if(e.altKey==true){
                if(e.which=='77')
                    $(".topnavbar #masterdiv_menu").show();
                 else
                 $(".topnavbar #masterdiv_menu").hide();
               
               if(e.which=='84')
                    $(".topnavbar #transactionsdiv_menu").show();
                 else
                 $(".topnavbar #transactionsdiv_menu").hide(); 
            }
        });
        /*
        $(document).keyup(function(e) {
          delete keys[e.which];
          $(".topdropnav").removeAttr('style');
        });
        */

  function clickNumbers (val) {
    if (displayBox.innerHTML === '0' || (hasEvaluated === true && !isNaN(displayBox.innerHTML))) {
      displayBox.innerHTML = val
    } else {
      displayBox.innerHTML += val
    }
    hasEvaluated = false
  }

        
$(document).ready(function () {
    
    
  // CHECK IF 0 IS PRESENT. IF IT IS, OVERRIDE IT, ELSE APPEND VALUE TO DISPLAY

  // PLUS MINUS
  $('#plus_minus').click(function () {
    if (eval(displayBox.innerHTML) > 0) {
      displayBox.innerHTML = '-' + displayBox.innerHTML
    } else {
      displayBox.innerHTML = displayBox.innerHTML.replace('-', '')
    }
  })

  // ON CLICK ON NUMBERS
  $('#clear').click(function () {
    displayBox.innerHTML = '0'
  //  $('#display').css('font-size', '80px')
  //  $('#display').css('margin-top', '110px')
    $('button').prop('disabled', false)
  })
  $('#one').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(1)
  })
  $('#two').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(2)
  })
  $('#three').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(3)
  })
  $('#four').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(4)
  })
  $('#five').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(5)
  })
  $('#six').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(6)
  })
  $('#seven').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(7)
  })
  $('#eight').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(8)
  })
  $('#nine').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(9)
  })
  $('#zero').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(0)
  })
  $('#decimal').click(function () {
    if (displayBox.innerHTML.indexOf('.') === -1 ||
      (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('+') !== -1) ||
      (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('-') !== -1) ||
      (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('×') !== -1) ||
      (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('÷') !== -1)) {
      clickNumbers('.')
    }
  })

  // OPERATORS
  $('#add').click(function () {
    evaluate()
    checkLength(displayBox.innerHTML)
    displayBox.innerHTML += '+'
  })
  $('#subtract').click(function () {
    evaluate()
    checkLength(displayBox.innerHTML)
    displayBox.innerHTML += '-'
  })
  $('#multiply').click(function () {
    evaluate()
    checkLength(displayBox.innerHTML)
    displayBox.innerHTML += '×'
  })
  $('#divide').click(function () {
    evaluate()
    checkLength(displayBox.innerHTML)
    displayBox.innerHTML += '÷'
  })
  $('#square').click(function () {
    var num = Number(displayBox.innerHTML)
    num = num * num
    checkLength(num)
    displayBox.innerHTML = num
  })
  $('#sqrt').click(function () {
    var num = parseFloat(displayBox.innerHTML)
    num = Math.sqrt(num)
    displayBox.innerHTML = Number(num.toFixed(5))
  })
  $('#equals').click(function () {
    evaluate()
    hasEvaluated = true
  })

  
  
  
  
  
  
    
})

// EVAL FUNCTION
  function evaluate () {
    displayBox.innerHTML = displayBox.innerHTML.replace(',', '')
    displayBox.innerHTML = displayBox.innerHTML.replace('×', '*')
    displayBox.innerHTML = displayBox.innerHTML.replace('÷', '/')
    if (displayBox.innerHTML.indexOf('/0') !== -1) {
     // $('#display').css('font-size', '70px')
      $('button').prop('disabled', false)
      $('.clear').attr('disabled', false)
      displayBox.innerHTML = 'Division by 0 is undefined!' 
    }
    var evaluate = eval(displayBox.innerHTML)
    if (evaluate.toString().indexOf('.') !== -1) {
      evaluate = evaluate.toFixed(5)
    }
    checkLength(evaluate)
    displayBox.innerHTML = evaluate
  }

  // CHECK FOR LENGTH & DISABLING BUTTONS
  function checkLength (num) {
    if (num.toString().length > 7 && num.toString().length < 14) {
     // $('#display').css('font-size', '35px')
    } else if (num.toString().length > 16) {
      num = 'Infinity'
      $('button').prop('disabled', true)
      $('.clear').attr('disabled', false)
    }
  }

  // TRIM IF NECESSARY
  function trimIfNecessary () {                                                 file = 'standard ignore' // eslint-disable-line
    var length = displayBox.innerHTML.length
    if (length > 7 && length < 14) {
      //$('#display').css('font-size', '35px')
    } else if (length > 14) {
      displayBox.innerHTML = 'Infinity'
      $('button').prop('disabled', true)
      $('.clear').attr('disabled', false)
    }
  }

     $(document).on("click",".tabslist",function(){
            var tbid  =  $(this).data("id");   
            var tburl = $(this).data("url"); 
			 
			 window.location.replace(baseurl+"/admin/dashboard/makeactive/"+tbid);
          
             })   
        
           
		
		 $('form input').keydown(function(e){
             if(e.keyCode==13){       

                if($(':input:eq(' + ($(':input').index(this) + 1) + ')').attr('type')=='submit'){// check for submit button and submit form on enter press
                 return true;
                }

                $(':input:eq(' + ($(':input').index(this) + 1) + ')').focus();

               return false;
             }

            });
        });
   
       
   
        /*
     $(document).on("click",".dropdown-menu a",function(){
         show_loader();
        var pageclicked = $(this).attr('href');
        var pagetitle   = $(this).text();
        
        $.post(baseurl+"/admin/dashboard/update_tab",
          {
            pageclicked: pageclicked,
            pagetitle: pagetitle
          },
          function(data, status){
         stop_loader();
          });
  
  
      
     }); */
     
        
    </script>
    <script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>     
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>/public/assets/js/jquery.inputmask.bundle.min.js" ></script> 
    <script type="text/javascript" src="<?php echo base_url();?>/public/assets/grid/jsZip-2.5.0/jszip.min.js" ></script>
    <script type="text/javascript" src="<?php echo base_url();?>/public/assets/grid/pqgrid.dev.js" ></script>  
    <script src="<?php echo base_url();?>/public/assets/grid/localize/pq-localize-en.js"></script>
    <link rel="stylesheet" href="<?php echo base_url();?>/public/assets/grid/pqselect.dev.css" /> 
    <script src="<?php echo base_url();?>/public/assets/grid/pqselect.dev.js"></script> 
    
    <script>
    // datepicker validation //https://codepen.io/deepakmahakale/pen/jvGEjv

    $(".datepicker").inputmask("99/99/9999", {
        mask: "99-99-9999",
        alias: "date",
        placeholder: "DD-MM-YYYY",
        insertMode: false,
    });

    $('.datepicker').datepicker({
        altFormat: "dd-mm-yyyy",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
        minDate:'<?= grp_comp()->fy_bgn_date_dmy ?>',
        maxDate:'<?= grp_comp()->fy_end_date_dmy ?>',

    }).on("change", function () {
        var date = $(this).val();
        if(!isValidDate(date)){
          $(this).val("");
        }
    });

    $(".datepickerReverseJournal").inputmask("99/99/9999", {
        mask: "99-99-9999",
        alias: "date",
        placeholder: "dd-mm-yyyy",
        insertMode: false,
    });

    $('.datepickerReverseJournal').datepicker({
        altFormat: "dd-mm-yyyy",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
        minDate:'<?php echo date("d-m-Y", strtotime("+1 day", time()));?>',
        maxDate:'<?= grp_comp()->fy_end_date_dmy ?>',

    }).on("change", function () {
        var date = $(this).val();

        if(!isValidDate(date)){
          console.log('date not valid');
          $(this).val("");
        }
    });

    $(".datepicker2").inputmask("99/99/9999", {
        mask: "99-99-9999",
        alias: "date",
        placeholder: "DD-MM-YYYY",
        insertMode: false,
    });

    $('.datepicker2').datepicker({
        altFormat: "dd-mm-yyyy",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,

    }).on("change", function () {
        var date = $(this).val();
        if(!isValidDate(date)){
          $(this).val("");
        }
    });



    function isValidDate(dateString)
    {
        // First check for the pattern
        if(!/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(dateString))
            return false;

        // Parse the date parts to integers
        var parts = dateString.split("-");
        var month = parseInt(parts[1], 10);
        var day = parseInt(parts[0], 10);
        var year = parseInt(parts[2], 10);

        // Check the ranges of month and year
        if(year < 1000 || year > 3000 || month == 0 || month > 12)
            return false;

        var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

        // Adjust for leap years
        if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
            monthLength[1] = 29;

        // Check the range of the day
        return day > 0 && day <= monthLength[month - 1];
    };
    
    $(".datepicker").on('keydown', function (e) {
            IsNumeric(this, e.keyCode);
        });
        
    var isShift = false;
        var seperator = "/";
        function IsNumeric(input, keyCode) {
            if (keyCode == 16) {
                isShift = true;
            }
            //Allow only Numeric Keys.
            if (((keyCode >= 48 && keyCode <= 57) || keyCode == 8 || keyCode <= 37 || keyCode <= 39 || (keyCode >= 96 && keyCode <= 105)) && isShift == false) {
                if ((input.value.length == 2 || input.value.length == 5) && keyCode != 8) {
                    input.value += seperator;
                }
                return true;
            }
            else {
                return false;
            }
        };    
    $(document).ready(function(){
        $(".dropmenu").click(function(){
               $("body").toggleClass("sidetoogle");
         });
    });


    

</script>
<script>

$("#todate").datepicker({
            showOn: 'both',
            buttonImageOnly: true,
            buttonImage: '<?php echo base_url();?>/public/assets/images/caldender-icon.png',
            dateFormat: 'dd-mm-yy',
        });    
$("#fromdate").datepicker({
            showOn: 'both',
            buttonImageOnly: true,
            buttonImage: '<?php echo base_url();?>/public/assets/images/caldender-icon.png',
            dateFormat: 'dd-mm-yy',
        });     
</script>

<script> //calander 

  var grp_fy_calender = <?= json_encode(grp_fy_calender_js()) ?>;
  console.log(grp_fy_calender);
  var fy_id = <?= grp_comp()->fy_id ?>;

  var fy_indx = grp_fy_calender.findIndex((obj) => {
    return obj.fy_id == fy_id;
  })
  if(fy_indx > -1){
    setCalender()
  }

  $(document).on("click","#next_year",function(){
    var indx = fy_indx;
    indx++;
    if(grp_fy_calender[indx]){
      fy_indx = indx;
      setCalender()
    }
  });

  $(document).on("click","#prev_year",function(){
    var indx = fy_indx;
    indx--;
    if(grp_fy_calender[indx]){
      fy_indx = indx;
      setCalender()
    }
  });

  function setCalender()
  {
    var calender = grp_fy_calender[fy_indx];
    $('#fy_year').text(calender.fy_name);
    var months  = calender.fy_months;
    var type    = calender.type;

    var html = ``;

    if(type == 1)
    {
      html += `<div class="row">`;
      $.each(months, function(ind,obj){
          html += `<div class="col-md-12 text-success"><small class="float-end"><em>${ind}</em></small></div>`;

          for (var i=1; i<=4; i++) {

            if(obj[i]){
              html += `<div class="col-md-3 d-grid px-1">`;

              for (var j=1; j<=3; j++) {
                if(obj[i][j]){
                  obj3 = obj[i][j];
                  html += `<button type="button" data-month="${obj3.month}" data-year="${obj3.year}" class="btn btn-light btn-sm mb-1 month_btn">${obj3.label}</button>`;
                }
                else{
                  html += `<button type="button" data-month="" data-year="" class="btn btn-light btn-sm mb-1 month_btn invisible"></button>`;
                }
              }
              html += `</div>`;
            }
            else{
              html += `<div class="col-md-3 d-grid px-1"></div>`;
            }
          }
      });
      html += `</div>`;
    }
      
    if(type == 2)
    {
      html += `<div class="row">`;
      $.each(months, function(ind,obj){

        html += `<div class="col-md-3 d-grid px-1">`;
          $.each(obj, function(ind2,obj2){

            if(ind2 == 4){
              html += `<button type="button" data-from_month="${obj2.from_month}" data-to_month="${obj2.to_month}" data-from_year="${obj2.from_year}" data-to_year="${obj2.to_year}" class="btn btn-qlight btn-sm mb-1 quarter_btn">${obj2.label}</button>`;
            }
            else if(ind2 == 5)
            {
              console.log(obj[ind2])
              if(obj[ind2])
                html += `<button type="button" data-from_month="${obj2.from_month}" data-to_month="${obj2.to_month}" data-from_year="${obj2.from_year}" data-to_year="${obj2.to_year}" class="btn btn-hlight btn-sm mb-1 hyear_btn">${obj2.label}</button>`;
              else
                html += `<button type="button" data-month="" data-year="" class="btn btn-light btn-sm my-2 invisible"></button>`;
            }
            else{
              html += `<button type="button" data-month="${obj2.month}" data-year="${obj2.year}" class="btn btn-light btn-sm mb-1 month_btn">${obj2.label}</button>`;
            }
 
          });

        html += `</div>`;

      });
      html += `</div>`;
    }

    $('.comp_calender').html(html);
    $('.comp_calender').attr('data-from_day', calender.from_day);
    $('.comp_calender').attr('data-from_month', calender.from_month);
    $('.comp_calender').attr('data-from_year', calender.from_year);
    $('.comp_calender').attr('data-to_day', calender.to_day);
    $('.comp_calender').attr('data-to_month', calender.to_month);
    $('.comp_calender').attr('data-to_year', calender.to_year);

    set_fy_date();
  }

  $(document).on("click",".month_btn",function(){
      var month = $(this).attr('data-month');
      var year = $(this).attr('data-year');

      var fd = '01';
      var ld = new Date(year, month, 0).getDate();
      
      var y = year; 
      var m = month < 10 ? '0'+month : month;
      
      var from_date = fd + '-' + m + '-' + y;
      var to_date = ld + '-' + m + '-' + y;
      
      if($('#tillperiod').is(":checked")){
      
        var td = today();
        var arr = td.split('-');
        var ld = new Date(arr[2], arr[1], 0).getDate();
        to_date = ld+ '-' + arr[1] + '-' + arr[2];
      }

      from_date = validate_from_date(from_date);
      to_date = validate_to_date(to_date);
      
      $("#fromdate").val(from_date);
      $("#todate").val(to_date);
  });

  $(document).on("click",".quarter_btn",function(){
     
    var from_month = $(this).attr('data-from_month');
    var from_year = $(this).attr('data-from_year');

    var to_month = $(this).attr('data-to_month');
    var to_year = $(this).attr('data-to_year');

    var from_day = 1;
    var to_day =new Date(to_year, to_month, 0).getDate();

    from_day = parseInt(from_day) < 10 ? '0'+from_day : from_day;
    from_month = parseInt(from_month) < 10 ? '0'+from_month : from_month;
    to_day = parseInt(to_day) < 10 ? '0'+to_day : to_day;
    to_month = parseInt(to_month) < 10 ? '0'+to_month : to_month;

    var from_date = from_day+'-'+from_month+'-'+from_year;
    var to_date = to_day+'-'+to_month+'-'+to_year;
    
    if($('#tillperiod').is(":checked")){
    
      var td = today();
      var arr = td.split('-');
      var ld = new Date(arr[2], arr[1], 0).getDate();
      to_date = ld+ arr[1] +'-' + arr[2];
    }

    from_date = validate_from_date(from_date);
    to_date = validate_to_date(to_date);
    
    $( "#fromdate" ).val(from_date);
    $( "#todate" ).val(to_date);
  });
    
  $(document).on("click",".hyear_btn",function(){
    var from_month = $(this).attr('data-from_month');
    var from_year = $(this).attr('data-from_year');

    var to_month = $(this).attr('data-to_month');
    var to_year = $(this).attr('data-to_year');

    var from_day = 1;
    var to_day =new Date(to_year, to_month, 0).getDate();

    from_day = parseInt(from_day) < 10 ? '0'+from_day : from_day;
    from_month = parseInt(from_month) < 10 ? '0'+from_month : from_month;
    to_day = parseInt(to_day) < 10 ? '0'+to_day : to_day;
    to_month = parseInt(to_month) < 10 ? '0'+to_month : to_month;

    var from_date = from_day+'-'+from_month+'-'+from_year;
    var to_date = to_day+'-'+to_month+'-'+to_year;
    
    if($('#tillperiod').is(":checked")){
    
      var td = today();
      var arr = td.split('-');
      var ld = new Date(arr[2], arr[1], 0).getDate();
      to_date = ld+ arr[1] +'-' + arr[2];
    }

    from_date = validate_from_date(from_date);
    to_date = validate_to_date(to_date);
    
    $( "#fromdate" ).val(from_date);
    $( "#todate" ).val(to_date);
  });

  $(document).on("click","#tilldate",function(){

      $('#tillperiod').prop('checked', false);
      
      if($("#fromdate").val() == '' || checkdate($("#fromdate").val())){
        var from_day = $('.comp_calender').attr('data-from_day');
        var from_month = $('.comp_calender').attr('data-from_month');
        var from_year = $('.comp_calender').attr('data-from_year');
        var from_date = from_day + '-' + from_month + '-' + from_year;
        $( "#fromyear" ).val(from_year);
      }
       
      var to_date = today();
      to_date = validate_to_date(to_date);
      $( "#todate" ).val(to_date);
  });
    
  function set_fy_date()
  {
    var from_day = $('.comp_calender').attr('data-from_day');
    var from_month = $('.comp_calender').attr('data-from_month');
    var from_year = $('.comp_calender').attr('data-from_year');
    var from_date = from_day + '-' + from_month + '-' + from_year;

    var to_day = $('.comp_calender').attr('data-to_day');
    var to_month = $('.comp_calender').attr('data-to_month');
    var to_year = $('.comp_calender').attr('data-to_year');
    var to_date = to_day + '-' + to_month + '-' + to_year;

    $("#fromdate").val(from_date);
    $("#todate").val(to_date);
  }
    
  function checkdate(datee)
  {
      var d = datee.split("-");

      var varDate = new Date(d[2], d[1]-1, d[0]);
      var today = new Date();
      
      varDate.setHours(0,0,0,0);
      today.setHours(0,0,0,0);

      if(varDate > today) {
          return true;   
      }
      return false;
  }
  
  function validate_from_date(datee){

      var d = datee.split("-");
      var from_date = new Date(d[2], d[1]-1, d[0]);

      var fy_from_day = $('.comp_calender').attr('data-from_day');
      var fy_from_month = $('.comp_calender').attr('data-from_month');
      var fy_from_year = $('.comp_calender').attr('data-from_year');

      var fy_from_date = new Date(parseInt(fy_from_year),(parseInt(fy_from_month)-1),parseInt(fy_from_day));
      
      from_date.setHours(0,0,0,0);
      fy_from_date.setHours(0,0,0,0);

      if(from_date > fy_from_date) {
          return datee;   
      }
      return fy_from_day+'-'+fy_from_month+'-'+fy_from_year;
  }
  function validate_to_date(datee){
      var d = datee.split("-");
      var to_date = new Date(d[2], d[1]-1, d[0]);

      var fy_to_day = $('.comp_calender').attr('data-to_day');
      var fy_to_month = $('.comp_calender').attr('data-to_month');
      var fy_to_year = $('.comp_calender').attr('data-to_year');
      var fy_to_date = new Date(parseInt(fy_to_year),(parseInt(fy_to_month)-1),parseInt(fy_to_day));
      
      to_date.setHours(0,0,0,0);
      fy_to_date.setHours(0,0,0,0);

      if(to_date < fy_to_date) {
          return datee;   
      }
      return fy_to_day+'-'+fy_to_month+'-'+fy_to_year;
  } 
 
  function today()
  { 
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = today.getFullYear();
      
      today = dd + '-' + mm + '-' + yyyy;
      return today;
  }
</script>
<script>
   
    var key = {};
    var pq_index = 0;
    $(document).keydown(function(e) {
        key[e.which] = true;
        // console.log(e.which);
        
            
        if (key[16] && key[17]) { 
            
            var pq_grids = $('.pq-grid');
            if(pq_grids.length > 0){
                var pq_last_index = pq_index > 0 ? pq_index -1 : pq_grids.length;
                $(pq_grids[pq_last_index]).pqGrid('setSelection', null);
                
                $(pq_grids[pq_index]).pqGrid('setSelection', { rowIndx: 0, focus: true });
                $('html, body').scrollTop($(pq_grids[pq_index]).offset().top - 50);
                
                pq_index++;
                if(pq_index == pq_grids.length){
                    pq_index = 0;
                }
                e.preventDefault();
            }
        }
        if (e.which == 120) {

            if($('#billsModal').length && $('#billsModal').hasClass('show')){
              $('#save_bill_by_bill').trigger('click');
            }
            else if($('#ccModal').length && $('#ccModal').hasClass('show')){
              $('#save_cc').trigger('click');
            }
            else{
              $('#submitbtn').trigger('click');
              $('.swal2-confirm').focus();
            }
            
            e.preventDefault();
            e.stopPropagation();
        }

        if (key[16] && key[78]) { 
            bbbModel = $('#billsModal');
            if(bbbModel.length && bbbModel.hasClass('show')){
              $('#billsModal .nextBtn').trigger('click');
              e.preventDefault();
            }

            ccModel = $('#ccModal');
            if(ccModel.length && ccModel.hasClass('show')){
              $('#ccModal .nextBtn').trigger('click');
              e.preventDefault();
            }
        }
        if (key[16] && key[66]) {

            bbbModel = $('#billsModal');
            if(bbbModel.length && bbbModel.hasClass('show')){
              $('#billsModal .prevBtn').trigger('click');
              e.preventDefault();
            }

            ccModel = $('#ccModal');
            if(ccModel.length && ccModel.hasClass('show')){
              $('#ccModal .prevBtn').trigger('click');
              e.preventDefault();
            }
        }
        
    });
    $(document).keyup(function(e) {
        delete key[e.which];
    });

    function disabledEventPropagation(e){
    if(e){
      if(e.stopPropagation){
        e.stopPropagation();
      } else if(window.event){
        window.event.cancelBubble = true;
      }
    }
  }
</script>

<script>

function parseAmount(amount)
{
  if(typeof amount == 'string')
    amount = amount.replace(/,/g, '');

  if(amount == '' || amount == null || amount == undefined || isNaN(amount))
    return 0;

  var num = amount;
  num = parseFloat(num);
  num = Math.round(num * 100) / 100;
  num = num.toFixed(2);
  num = parseFloat(num);

  return num;
}

function formatAmount(amount)
{
  var num = parseAmount(amount);

    var sign = '';
    if(num < 0){
        sign = '-';
        num = Math.abs(num);
    }
    num = num.toString();
     
    var final_string = '0.00';
    if(num.includes("."))
    {
 
      var arr = num.split(".");
        var num1 = arr[0];
        var num2 = arr[1];
        
        var final1 = num1;
      if(num1.length > 3){
          var string = '';
          var last3String = num1.substring(num1.length-3, num1.length);
          var restString = num1.substring(0, num1.length-3);

          restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

          for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
            if(i ==0)
              string += parseInt(restString.substr(o, 2))+',';
            else
              string += restString.substr(o, 2)+',';
          }
          final1 = string + last3String;
       }
       
       var final2 = num2;
       if(num2.length == 1){
          final2 = num2+'0';
       }

    final_string = final1 + '.' + final2;
    }
    else
    {
      final_string = num + '.00';
      if(num.length > 3){
          var string = '';
          var last3String = num.substring(num.length-3, num.length);
          var restString = num.substring(0, num.length-3);

          restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

          for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
            if(i ==0)
              string += parseInt(restString.substr(o, 2))+',';
            else
              string += restString.substr(o, 2)+',';
          }
          final_string = string + last3String + '.00';
       }
    }
    
    
    return '&#8377; ' + sign + final_string;
}

function formatForChart(amount)
{
  var num = parseAmount(amount);
  num = Math.round(num);

    var sign = '';
    if(num < 0){
        sign = '-';
        num = Math.abs(num);
    }
    num = num.toString();
    
    var final_string = num;
    if(num.length > 3){
        var string = '';
        var last3String = num.substring(num.length-3, num.length);
        var restString = num.substring(0, num.length-3);

        restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

        for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
          if(i ==0)
            string += parseInt(restString.substr(o, 2))+',';
          else
            string += restString.substr(o, 2)+',';
        }
        final_string = string + last3String;
     }
    
    return sign + final_string;
}

var user_comp_list = <?= json_encode(user_company_list()) ?>;

$(document).on('keyup', '#searchCompaniesFromHeader', function(){
  var search = $(this).val();
  if(search != '')
  {
    var search_comp_list = user_comp_list.filter(function(obj){
      var string1 = obj.comp_name.toLowerCase();
      var string2 = obj.comp_code.toLowerCase();
      var text = search.toLowerCase();
       
      return string1.includes(text) || string2.includes(text);
    });
  }
  else{
    search_comp_list = user_comp_list;
  }
  
  
  var html = ``;
  $.each(search_comp_list, function(index, object){
    html += `
      <li class="nav-item">
        <a class="nav-link px-3 selcompany_link" data-comp_id="${object.comp_id}" data-comp_type="${object.comp_type}" href="javascript:void(0);">
          <h5>${object.comp_name}</h5>
          Organization: ${object.comp_code}
        </a>
      </li>
    `;
  });

  $('#searchCompaniesFromHeader_ul').html(html);

});

$(document).on("click",'.selbo_link',function(){
    
    var bo_id = $(this).data("id");
    
    load_bolink(bo_id);
    
     async function load_bolink(bo_id) {
         $(".siteloader").show();
           let start_time = performance.now();
        
          var interval   = setInterval(function(){
                var currenttime  = performance.now();
                var pct =((currenttime - start_time) / currenttime*100).toFixed(2);
                 $('#progressbar').css({width: pct+"%"}, {duration: 2000, easing: 'linear'});
                 $('#progressbar').attr('aria-valuenow',pct);
                 $('#progressbar .progress-bar').css("width", pct+"%");
                 $('#progressbar .progress-bar').text(pct+"%");
              
          }, 1000); 
          
       
         let response = await fetch(baseurl+'/admin/choose_branch/'+bo_id);
            
            let result = await response.json();
            requestTime = performance.now();
           if(result){
            clearInterval(interval);
            $('#progressbar').css("width","100%");
            $('#progressbar .progress-bar').css("width", "100%");
            $('#progressbar .progress-bar').text("100%");
           window.open(baseurl+'/admin/dashboard', '_self').focus();
           } 
       }
  });
  
  

  $(document).on("click",'.selcompany_link',function(){
    
    var comp_id = $(this).data("comp_id");
    var comp_type = $(this).data("comp_type");
    
    if(comp_type == 'cmp')
      load_companylink(comp_id);

    if(comp_type == 'grp')
      load_group_companylink(comp_id);
    
     
  });

  async function load_companylink(company_id) {
       $(".siteloader").show();
         let start_time = performance.now();
      
        var interval   = setInterval(function(){
              var currenttime  = performance.now();
              var pct =((currenttime - start_time) / currenttime*100).toFixed(2);
               $('#progressbar').css({width: pct+"%"}, {duration: 2000, easing: 'linear'});
               $('#progressbar').attr('aria-valuenow',pct);
               $('#progressbar .progress-bar').css("width", pct+"%");
               $('#progressbar .progress-bar').text(pct+"%");
            
        }, 1000); 
        
     
       let response = await fetch(baseurl+'/admin/choose_company/'+company_id);
          
          let result = await response.json();
          requestTime = performance.now();
         if(result){
          clearInterval(interval);
          $('#progressbar').css("width","100%");
          $('#progressbar .progress-bar').css("width", "100%");
          $('#progressbar .progress-bar').text("100%");
          window.open(baseurl+'/admin/dashboard', '_self').focus();
         } 
  }

  async function load_group_companylink(company_id) {
       $(".siteloader").show();
         let start_time = performance.now();
      
        var interval   = setInterval(function(){
              var currenttime  = performance.now();
              var pct =((currenttime - start_time) / currenttime*100).toFixed(2);
               $('#progressbar').css({width: pct+"%"}, {duration: 2000, easing: 'linear'});
               $('#progressbar').attr('aria-valuenow',pct);
               $('#progressbar .progress-bar').css("width", pct+"%");
               $('#progressbar .progress-bar').text(pct+"%");
            
        }, 1000); 
        
     
       let response = await fetch('<?= base_url() ?>/groupcompany/select_company/'+company_id+'/0');
          
          let result = await response.json();
          requestTime = performance.now();
         if(result){
          clearInterval(interval);
          $('#progressbar').css("width","100%");
          $('#progressbar .progress-bar').css("width", "100%");
          $('#progressbar .progress-bar').text("100%");
          window.open(baseurl+'/grpcomp/dashboard', '_self').focus();
         } 
  }


    
    async function load_txn_history(vchrdate,vchrtypeid) {
       let response = await fetch('<?php echo base_url();?>/admin/reports/load_vouher_txn_history/'+vchrdate+'/'+vchrtypeid);
      let result = await response.json();

      if(result)
      {
        
        if($("#voucher_txn_grid").pqGrid('instance')){       
          $("#voucher_txn_grid").pqGrid('refresh');
          $("#voucher_txn_grid").pqGrid('option', 'dataModel.data', result.data);
          $("#voucher_txn_grid").pqGrid('refreshDataAndView');
        }
        else{
          $("#voucher_txn_grid").pqGrid(historyObj);
          $("#voucher_txn_grid").pqGrid('option', 'dataModel.data', result.data);
          $("#voucher_txn_grid").pqGrid('refreshDataAndView');
        }
      }
    
}

$(document).on("click","#voucher_txn_btn",function(){
    
    var vchrdate = $(this).data("vchrdate")
     var vchrtypeid = $(this).data("vchrid"); 
    
     $("#voucher_txn_btn button").removeClass("active");
      $("#allvoucher_txn_btn button").removeClass("active");
      
      
       $("#voucher_txn_btn button").addClass("active");
       
       
      load_txn_history(vchrdate,vchrtypeid);
      
      
      
});


$(document).on("click","#allvoucher_txn_btn",function(){
    
    var vchrdate = $(this).data("vchrdate");
     var vchrtypeid = '0'; 
    
    
    $("#voucher_txn_btn button").removeClass("active");
      $("#allvoucher_txn_btn button").removeClass("active");
      
      
       $("#allvoucher_txn_btn button").addClass("active");
     
      load_txn_history(vchrdate,vchrtypeid);
      
      
      
});




$(document).on("click",".open_voucher_txn_history",function(){
    
    $("#voucher_txn_historyModal").modal('show');
    var voucher_date = $("#voucher_date").val();
     var sale_date = $("#sale_date").val();
      var purchase_date = $("#purchase_date").val();
      var production_date = $("#production_date").val();
  
    if (typeof voucher_date === "undefined" && typeof sale_date !== "undefined") {
        var vchrdate = $("#sale_date").val();
        }
   else if (typeof voucher_date === "undefined" && typeof purchase_date !== "undefined") {
        var vchrdate = $("#purchase_date").val();
        }
else if (typeof voucher_date === "undefined" && typeof production_date !== "undefined") {
        var vchrdate = $("#production_date").val();
        }        
    else
    var vchrdate = $("#voucher_date").val();
    
    
     var vchrtypeid = $(this).data("id"); 
     
      $("#voucher_txn_btn button").removeClass("active");
      $("#allvoucher_txn_btn button").removeClass("active");
      
      
       $("#voucher_txn_btn button").addClass("active");
    
     $("#voucher_txn_btn").attr({"data-vchrid":vchrtypeid,"data-vchrdate":vchrdate});    
     $("#allvoucher_txn_btn").attr({"data-vchrid":vchrtypeid,"data-vchrdate":vchrdate});  
    
   
    
    load_txn_history(vchrdate,vchrtypeid);
    
    
})


var jsonvchr = [];
for(var i=0;i<2;i++){
    jsonvchr.push({'date': '', 'particulars': '', 'voucher_type': '', 'voucher_no': '', 'debit': '', 'credit': ''});
}

var colModel = [
        { title: "DATE", align:"left", width: 180,   dataIndx: "date" },
        { title: "PARTICULARS", align:"left", width: 180,   dataIndx: "particulars" },
        { title: "VOUCHER TYPE", align:"left", width: 180,   dataIndx: "voucher_type" },
        { title: "VOUCHER NO.", align:"left", width: 180,   dataIndx: "voucher_no" },
        { title: "DEBIT", align:"right", width: 180,   dataIndx: "debit" },
        { title: "CREDIT", align:"right", width: 180,   dataIndx: "credit" },
    ];
var vchr_dataModel = {"data":jsonvchr} 
var historyObj = {
      scrollModel: { autoFit: true },
      height: 'flex',
      collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
      selectionModel: { type: 'row',mode:'single' },
      dataModel: vchr_dataModel,
      colModel : colModel,
      editable: false,
      numberCell: { show: false },
      wrap:false,
      showTitle: false,
      create: function (evt, ui) {// make first row auto selected
            var grid = this,
              $select_row = $(".select-row"),
              data = ui.dataModel.data;
              grid.setSelection({ rowIndx: 0, focus: true });
      },
     
  };

  historyObj.rowDblClick = function(event, ui) {
    var rowData             = ui.rowData;
    var col_type            = rowData.vch_type;
    var ajax                = rowData.ajax;
    var voucher_txn_id      = rowData.voucher_txn_id;
    var voucher_type_id     = rowData.voucher_type_id;
    var bom_id            = rowData.bom_id;
    var bom_batches       = rowData.bom_batches;
    if(voucher_type_id=='18')
      window.location.href= baseurl+'/admin/sales/edit/'+voucher_txn_id;
    else if(voucher_type_id=='11')
      window.location.href= baseurl+'/admin/purchase/edit/'+voucher_txn_id; 
    else if(voucher_type_id=='10')
      window.location.href= baseurl+'/admin/physical_verification/edit/'+voucher_txn_id+'/'+voucher_type_id;
    else if(voucher_type_id=='14')
      window.location.href= baseurl+'/admin/production/modify/'+voucher_txn_id+'/?bom_id='+bom_id;
    else if(voucher_type_id=='6')
      window.location.href= baseurl+'/admin/inward_challan/edit/'+voucher_txn_id;
    else if(voucher_type_id=='7')
      window.location.href= baseurl+'/admin/delivery_challan/edit/'+voucher_txn_id;
    else if(voucher_type_id=='2')
      window.location.href= baseurl+'/admin/credit_note/edit/'+voucher_txn_id;
    else if(voucher_type_id=='3')
      window.location.href= baseurl+'/admin/debit_note/edit/'+voucher_txn_id;
    else if(voucher_type_id=='12')
      window.location.href= baseurl+'/admin/purchase_order/edit/'+voucher_txn_id;
    else if(voucher_type_id=='15')
      window.location.href= baseurl+'/admin/stock_transfer/edit/'+voucher_txn_id;
    else if(voucher_type_id=='17')
      window.location.href= baseurl+'/admin/quotations/edit/'+voucher_txn_id
    else if(voucher_type_id=='19')
      window.location.href= baseurl+'/admin/sales_order/edit/'+voucher_txn_id;
    else if(voucher_type_id=='21')
      window.location.href= baseurl+'/admin/purchase_requisition/edit/'+voucher_txn_id;
    else if(voucher_type_id=='20')
      window.location.href= baseurl+'/admin/packing_unpacking/modify_stock_journal/'+voucher_txn_id+"/"+voucher_type_id;

    else if(voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13')
      window.location.href= baseurl+'/admin/vouchers/edit/'+voucher_txn_id;

    else if(voucher_type_id=='8')
      window.location.href= baseurl+'/admin/memorandum/edit/'+voucher_txn_id;

  }
       
  historyObj.cellKeyDown = function(evt, ui) {
    var rowData           = ui.rowData;
    var ajax              = rowData.ajax;
    var col_type          = rowData.col_type;
    var voucher_txn_id    = rowData.voucher_txn_id;
    var voucher_type_id   = rowData.voucher_type_id;
    var bom_id            = rowData.bom_id;
    var bom_batches       = rowData.bom_batches;
    if (evt.keyCode==13){

    if(voucher_type_id=='18')
      window.location.href= baseurl+'/admin/sales/edit/'+voucher_txn_id;
    else if(voucher_type_id=='11')
      window.location.href= baseurl+'/admin/purchase/edit/'+voucher_txn_id;  
    else if(voucher_type_id=='10')
      window.location.href= baseurl+'/admin/physical_verification/edit/'+voucher_txn_id+"/"+voucher_type_id;
    else if(voucher_type_id=='14')
      window.location.href= baseurl+'/admin/production/modify/'+voucher_txn_id+'/?bom_id='+bom_id;
    else if(voucher_type_id=='6')
      window.location.href= baseurl+'/admin/inward_challan/edit/'+voucher_txn_id;
    else if(voucher_type_id=='7')
      window.location.href= baseurl+'/admin/delivery_challan/edit/'+voucher_txn_id;
    else if(voucher_type_id=='2')
      window.location.href= baseurl+'/admin/credit_note/edit/'+voucher_txn_id;
    else if(voucher_type_id=='3')
      window.location.href= baseurl+'/admin/debit_note/edit/'+voucher_txn_id;
    else if(voucher_type_id=='12')
      window.location.href= baseurl+'/admin/purchase_order/edit/'+voucher_txn_id;
    else if(voucher_type_id=='15')
      window.location.href= baseurl+'/admin/stock_transfer/edit/'+voucher_txn_id;
    else if(voucher_type_id=='17')
      window.location.href= baseurl+'/admin/quotations/edit/'+voucher_txn_id;
    else if(voucher_type_id=='19')
      window.location.href= baseurl+'/admin/sales_order/edit/'+voucher_txn_id;
    else if(voucher_type_id=='21')
      window.location.href= baseurl+'/admin/purchase_requisition/edit/'+voucher_txn_id;
    else if(voucher_type_id=='20')
      window.location.href= baseurl+'/admin/packing_unpacking/modify_stock_journal/'+voucher_txn_id+"/"+voucher_type_id;

    else  if(voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13')
      window.location.href= baseurl+'/admin/vouchers/edit/'+voucher_txn_id;

    else if(voucher_type_id=='8')
      window.location.href= baseurl+'/admin/memorandum/edit/'+voucher_txn_id;
    
    }

  }

  function parseValue(amount)
{
  if(typeof amount == 'string')
    amount = amount.replace(/,/g, '');

  if(amount == '' || amount == null || amount == undefined || isNaN(amount))
    return 0;

  var num = amount;
  num = parseFloat(num);
  num = Math.round(num * 10000) / 10000;
  num = num.toFixed(4);
  num = parseFloat(num);

  return num;
}

function formatValue(amount)
{
  var num = parseValue(amount);

    var sign = '';
    if(num < 0){
        sign = '-';
        num = Math.abs(num);
    }
    num = num.toString();
     
    var final_string = '0.0000';
    if(num.includes("."))
    {
 
      var arr = num.split(".");
        var num1 = arr[0];
        var num2 = arr[1];
        
        var final1 = num1;
      if(num1.length > 3){
          var string = '';
          var last3String = num1.substring(num1.length-3, num1.length);
          var restString = num1.substring(0, num1.length-3);

          restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

          for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
            if(i ==0)
              string += parseInt(restString.substr(o, 2))+',';
            else
              string += restString.substr(o, 2)+',';
          }
          final1 = string + last3String;
       }
       
       var final2 = '0000';
       if(num2.length == 1){
          final2 = num2+'000';
       }
       else if(num2.length == 2){
          final2 = num2+'00';
       }
       else if(num2.length == 3){
          final2 = num2+'0';
       }
       else{
          final2 = num2;
       }

    final_string = final1 + '.' + final2;
    }
    else
    {
      final_string = num + '.0000';
      if(num.length > 3){
          var string = '';
          var last3String = num.substring(num.length-3, num.length);
          var restString = num.substring(0, num.length-3);

          restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

          for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
            if(i ==0)
              string += parseInt(restString.substr(o, 2))+',';
            else
              string += restString.substr(o, 2)+',';
          }
          final_string = string + last3String + '.0000';
       }
    }
    
    
    return '&#8377; ' + sign + final_string;
}

function parseQty(amount)
{
  if(typeof amount == 'string')
    amount = amount.replace(/,/g, '');

  if(amount == '' || amount == null || amount == undefined || isNaN(amount))
    return 0;

  var num = amount;
  num = parseFloat(num);
  // num = Math.round(num * 100) / 100;
  // num = num.toFixed(2);
  // num = parseFloat(num);

  return num;
}

function formatQty(amount)
{
  var num = parseAmount(amount);

    var sign = '';
    if(num < 0){
        sign = '-';
        num = Math.abs(num);
    }
    num = num.toString();
     
    var final_string = '0';
    if(num.includes("."))
    {
 
      var arr = num.split(".");
        var num1 = arr[0];
        var num2 = arr[1];
        
      var final1 = num1;
      if(num1.length > 3){
          var string = '';
          var last3String = num1.substring(num1.length-3, num1.length);
          var restString = num1.substring(0, num1.length-3);

          restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

          for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
            if(i ==0)
              string += parseInt(restString.substr(o, 2))+',';
            else
              string += restString.substr(o, 2)+',';
          }
          final1 = string + last3String;
       }
       
       var final2 = num2;

      final_string = final1 + '.' + final2;
    }
    else
    {
      final_string = num;
      if(num.length > 3){
          var string = '';
          var last3String = num.substring(num.length-3, num.length);
          var restString = num.substring(0, num.length-3);

          restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

          for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
            if(i ==0)
              string += parseInt(restString.substr(o, 2))+',';
            else
              string += restString.substr(o, 2)+',';
          }
          final_string = string + last3String;
       }
    }
    
    
    return sign + final_string;
}

  $('#refresh_grid').on('click', function(){

      var pq_grids = $('.pq-grid');
      if(pq_grids.length > 0){
          $.each(pq_grids, function(index, pq_grid){

            var grid_id = $(pq_grid).attr('id');
            localStorage.removeItem("pq-grid"+grid_id);

            $(pq_grid).pqGrid( "reset", { group: true, filter: true, sort: true } );

              var CM = $(pq_grid).pqGrid('option', 'colModel');
              for(var i=0, len = CM.length; i < len; i++){
                  var column = CM[i];
                  if(column.filter){
                      column.filter.value = null;
                      column.filter.value2 = null;
                      column.filter.cache = null;
                  }
              }
              
              $(pq_grid).pqGrid('filter', {
                oper: 'replace',
                data: []
              });
              $('.filterValue').val('');
              $(pq_grid).pqGrid('refreshHeader');

              $(pq_grid).pqGrid( "setSelection", { rowIndx: 0 });

          })
      }

      $('#trash').text('Enable Trash Mode'); 
      $('#trash').data('type', '0');
  })
       
// $(".validlink").on("click",function(){
// 	if(is_unmpd_cmp_exs>0){
// 		$("#reportsdiv_menu").removeClass('show');
// 	  alert_notification("Mapped Member Companies First!!!")
// 		return false;	
// 	}else
// 		return true;
	
// })

  function edit_voucher(voucher_txn_id, voucher_type_id, comp_id,comp_fy_id,bo_id)
  {
    window.location.href="<?= base_url() ?>/home/edit_voucher_transaction/"+comp_id+"/"+comp_fy_id+"/"+bo_id+"/"+voucher_type_id+"/"+voucher_txn_id;
  }

function initTooltip()
{
  $("body").tooltip({ selector: '[data-toggle=tooltip]' });

  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  });
}

initTooltip();


  $(document).on('click', '.voucher_transaction', function(){
    var id = $(this).data('id');
    $('#selectCompanyModal .add_voucher_transaction').data('voucher_type_id', id);
    $('#selectCompanyModal').modal('show');
  });

  $(document).on('click', '.add_voucher_transaction', function(){
    var comp_id = $(this).data('comp_id');
    var voucher_type_id = $(this).data('voucher_type_id');

    window.location.href="<?= base_url() ?>/home/add_voucher_transaction/"+comp_id+"/"+voucher_type_id;
  });
   
</script>

<script src="<?= base_url() ?>/public/assets/js/datatables.js"></script>

<script>
  $('#grp_comp_list').DataTable({
    dom: 'rtp',
    pageLength: 5,
    pagingType: 'simple_numbers'
  });

</script>

<!-- List View The Modal Starts here -->
<div class="modal" id="voucher_txn_historyModal">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Voucher Transactions</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <div class="row">
        <div class="col-12 col-xxl-8">
               <a href="javascript:void(0);" id="voucher_txn_btn" data-vchrdate="" data-vchrid=""><button type="button" class="btn btn-outline-success active " fdprocessedid="cx3o3a">Recent Transactions</button></a>
                    <a href="javascript:void(0);" id="allvoucher_txn_btn" data-vchrdate="" data-vchrid=""><button type="button" class="btn btn-outline-success" fdprocessedid="cev8o">All Voucher Transactions</button></a>
                      </div>
        </div> 
        <br>
       <div id="voucher_txn_grid"></div>
      
      </div>

     

    </div>
  </div>
</div>

<!-- List View The Modal Starts here -->
<div class="modal" id="selectCompanyModal">
  <div class="modal-dialog  modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Select Company</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

        <div class="list-group" style="min-height: 50vh;">

          <?php foreach (grp_comp()->list as $key => $value) { ?>
          <a href="javascript:void(0)" data-voucher_type_id="0" data-comp_id="<?= $value->comp_id ?>" class="list-group-item list-group-item-action add_voucher_transaction">

            <?= $value->comp_code ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?= $value->comp_name ?>

            <span class="float-end me-2">
              <?= $value->fy_name ?>
            </span>
          </a>
          <?php } ?>

        </div>

      </div>
    </div>
  </div>
</div>
