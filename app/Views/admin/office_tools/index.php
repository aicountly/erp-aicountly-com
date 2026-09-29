<?php $header = array( 	'title' => 'Office Tools' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>


<div class="row pb-2">
  <div class="col-sm-6"><h3>Calendar</h3></div>  
  <div class="col-sm-6 text-end"><div class="taskmenus">

      <a href="#"><span class="material-symbols-outlined">offline_bolt</span></a> 

    <a href="#"><span class="material-symbols-outlined">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">download</span></a>
    <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">CSV</a></li>
    <li><a class="dropdown-item" href="#">Excel</a></li>
    <li><a class="dropdown-item" href="#">Document</a></li>
  </ul>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">share</span></a> 
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Facebook</a></li>
    <li><a class="dropdown-item" href="#">Twitter</a></li>
    <li><a class="dropdown-item" href="#">Instagram</a></li>
  </ul>
    
 </div>
 </div> 
</div>
<div class="row pb-2">
    <div class="col-md-6">
    </div>
  <div class="col-md-6 text-end">

  <?php if (!empty($googleEmail)): ?>
    <a class="btn btn-success me-2">
    <img src="https://my.aicountly.com/public/assets/img/icon-google.png" width="20">
        <strong><?= $googleEmail ?></strong>
    </a>

    <a href="<?= base_url('admin/office_tools/delinkGoogle'); ?>" class="btn btn-danger me-2">
    Delink
    </a>

<?php else: ?>
    <a href="<?= base_url(); ?>/admin/office_tools/googleCalendar" class="btn btn-success me-2" id="google-calendar-btn">
        Integrate with Google
    </a>
<?php endif; ?>



    <a href="#" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addEvent">Add New Event</a>

   <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-outline-success">Back</a>
  </div> 
</div>


<!-- Modal -->
<div class="modal fade" id="addEvent" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addEventLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="" id="addEventLabel">Add New Event</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
       <form id="googleEventForm">
           <div class="row">
               <p class="col-md-6"><label>Start Date</label><input type="date" name="start_date" class="form-control form-control-sm" id="event_start"></p>
               <p class="col-md-6"><label>End Date</label><input type="date" name="end_date" class="form-control form-control-sm" id="event_end"></p>
               <p class="col-md-6"><label>Name</label><input type="text" name="title" class="form-control form-control-sm" id="eventname"></p>
               <p class="col-md-6"><label>Type</label>
               <select name="type" name="eventtype" class="form-select form-select-sm"><option>One Day Event</option><option>Long Event</option><option>Seminar or Conference</option><option>Presentation</option>
               <option>Project Highlight</option><option>Accounting Meet</option><option>others</option></select></p>
               <p class="col-md-6"><label>Location</label><input type="text" name="location" class="form-control form-control-sm" id="eventplace"></p> 
               <p class="col-md-6"><label>Privacy</label><select name="type" class="form-select form-select-sm"><option>Public</option><option>Private</option><option>Invited Only</option> </select></p>
              <p class="col-md-12"><label>Desription</label><textarea name="description" class="form-control form-control-sm" rows="2"></textarea></p>
              <p class="col-12 colors">Label Color<br>
            <label><input type="radio" name="category_color" value="#A7510A;" checked="checked"><span class="checkmark" style="background-color:#A7510A;"></span></label>
            <label><input type="radio" name="category_color" value="#1D73C0"><span class="checkmark" style="background-color:#1D73C0;"></span></label>
            <label><input type="radio" name="category_color" value="#3C995B"><span class="checkmark" style="background-color:#3C995B;"></span></label>
            <label><input type="radio" name="category_color" value="#D7A90B"><span class="checkmark" style="background-color:#D7A90B;"></span></label>
            <label><input type="radio" name="category_color" value="#DF4BEB"><span class="checkmark" style="background-color:#DF4BEB;"></span></label>
            <label><input type="radio" name="category_color" value="#F0396B"><span class="checkmark" style="background-color:#F0396B;"></span></label>
            <label><input type="radio" name="category_color" value="#5865C0"><span class="checkmark" style="background-color:#5865C0;"></span></label>
            <label><input type="radio" name="category_color" value="#418C9C"><span class="checkmark" style="background-color:#418C9C;"></span></label>
            <label><input type="radio" name="category_color" value="#958948"><span class="checkmark" style="background-color:#958948;"></span></label>
            <label><input type="radio" name="category_color" value="#2291BB"><span class="checkmark" style="background-color:#2291BB;"></span></label>
           <label><input type="radio" name="category_color" value="#333333"><span class="checkmark" style="background-color:#333333;"></span></label>
        </p>
           </div>
       </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success eventAdd">Add Event</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="editEventModalLabel">Edit Event</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editGoogleEventForm">
          <input type="hidden" id="edit_event_id" name="event_id">
          <div class="row">
            <p class="col-md-6"><label>Start Date</label><input type="date" id="edit_event_start" name="start_date" class="form-control form-control-sm"></p>
            <p class="col-md-6"><label>End Date</label><input type="date" id="edit_event_end" name="end_date" class="form-control form-control-sm"></p>
            <p class="col-md-6"><label>Name</label><input type="text" id="edit_eventname" name="title" class="form-control form-control-sm"></p>
            <p class="col-md-6"><label>Location</label><input type="text" id="edit_location" name="location" class="form-control form-control-sm"></p>
            <p class="col-md-12"><label>Description</label><textarea id="edit_description" name="description" class="form-control form-control-sm" rows="2"></textarea></p>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="editEventForm">Save Changes</button>
      </div>
    </div>
  </div>
</div>




  <style>
#calendar_div{display:flex;}
.calendar__days section{padding:0px 0px!important;}
.calendar__date:foucs{background:#3C995B !important;}
  
   @media (min-width: 992px){
.calendar__days section {  padding-top:0px;  padding-bottom:0px;} 
}

.calendar-contain {
  position: relative;
  left: 0;
  right: 0;
  border-radius: 8px;
  width: 100%;
  /*overflow: hidden;
  max-width: 1020px;*/
  
  margin: 1rem auto;
  background-color: #f5f7f6;
  box-shadow: 5px 5px 72px rgba(30, 46, 50, 0.5);
  color: #040605;
}
@media screen and (min-width: 55em) {
  .calendar-contain {
    margin: auto;
    top: 5%;
  }
}

.title-bar {
  position: relative;
  width: 100%; float:right;
  text-align:center;
  display: table;
  background: #fff;
  padding:1.2rem 0.5rem;
  margin-bottom: 0;
}
.title-bar:after {
  display: table;
  clear: both;
}

.title-bar__prev{
  position: relative;
  float: left;
  text-align: left;
  cursor: pointer;
  width: 22px;
  height: 30px;
}
.title-bar__prev:after {
    content: "";
    display: inline;
    position: absolute;
    width: 14px;
    height: 14px;
    right: 0;
    left: 2px;
    top: 7px;
    margin: auto;
    border-top: 1.5px solid black;
    border-right: 1.5px solid black;
    -webkit-transform: rotate(224deg);
    transform: rotate(224deg);
}
.title-bar__next{
  position: relative;
  float: right;
  text-align: right;
  cursor: pointer;
  width: 22px;
  height: 30px;
}
.title-bar__next:after {
    content: "";
    display: inline;
    position: absolute;
    width: 14px;
    height: 14px;
    right: 2px;
    top: 7px;
    margin: auto;
    border-top: 1.5px solid black;
    border-right: 1.5px solid black;
    -webkit-transform: rotate(44deg);
    transform: rotate(44deg);
}
.title-bar__year {
  display: block;
  position: relative;
  display:inline-block;
  font-size: 1rem;
  line-height: 30px;
  padding: 0 0.5rem;
}
.title-bar__year select{
  padding: 2px 6px;
  font-size: 16px;
}
@media screen and (min-width: 55em) {
  .title-bar__year {

  }
}

.title-bar__month {
  position: relative;
  font-size: 1rem;
  line-height: 30px;
  display:inline-block;
  padding: 0 0.5rem;
}
.title-bar__month select{
  padding: 2px 6px;
  font-size: 16px;
}
/* @media screen and (min-width: 55em) {
  .title-bar__month
  }
} */

.calendar__sidebar {
  width: 100%;
  margin: 0 auto;
  float: none;
  background: #e3e3e3; borde-right:1px solid #ccc;
  padding-bottom: 0.7rem;
}
@media screen and (min-width: 55em) {
  .calendar__sidebar {
    position: absolute;
    height: 100%; background:#e3e3e3;
    width: 30%;
    float: left;
    margin-bottom: 0;
  }
}

.calendar__sidebar .content {
  padding: 2rem 1.5rem 2rem 4rem;
  color: #040605;
}

.sidebar__list {
  list-style: none;
  margin: 0;
  padding-left: 1rem;
  padding-right: 1rem;
}

.sidebar__list-item {
  margin: 1.2rem 0;
  color: #2d4338;
  font-weight: 100;
  font-size: 1rem;
}

.list-item__time {
  display: inline-block;
  /*width: 60px;*/
}
@media screen and (min-width: 55em) {
  .list-item__time {
    margin-right: 1rem;
  }
}

.sidebar__list-item--complete {
  color: rgba(4, 6, 5, 0.3);
}
.sidebar__list-item--complete .list-item__time {
  color: rgba(4, 6, 5, 0.3);
}

.sidebar__heading {
  font-size: 2.2rem; line-height:3rem;
  font-weight: bold;
  padding-left: 1rem;
  padding-right: 1rem;
  margin-bottom: 3rem;
  margin-top: 1rem;
}
.sidebar__heading span {
  float: right;
  font-weight: 300;
}

.calendar__heading-highlight {
  color: #2d444a;
  font-weight: 900;
}

.calendar__days {
  display: -webkit-box;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
          flex-flow: column wrap;
  -webkit-box-align: stretch;
          align-items: stretch;
  width: 100%;
  float: none;
  /*min-height: 520px;*/
  height: 100%;
  font-size: 12px;
  padding:1rem;
  background: #fff; overflow:scroll;
}
@media screen and (min-width: 55em) {
}

.calendar__top-bar {
  display: -webkit-box;
  display: flex;
  background:#E3E9FF;
  -webkit-box-flex: 32px;
          flex: 32px 0 0;
}

.top-bar__days {
  width: 100%;
  padding: 0 5px;
  color: #2d4338;
  font-weight:600;
  -webkit-font-smoothing: subpixel-antialiased;
  font-size: 1rem;
}

.calendar__week {
  display: -webkit-box;
  display: flex;
  -webkit-box-flex: 1;
          flex: 1 1 0;
}
.no-event .calendar__task,.inactive .calendar__task{color:#888;}
.calendar__day {
  display: -webkit-box;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
          flex-flow: column wrap;
  -webkit-box-pack: justify;
          /*justify-content: space-between;*/
  width: 100%;
  padding:.3rem;
  cursor: pointer; border:1px solid #E3E9FF;
}

.calendar__day.event{position:relative;}
.calendar__day.event .calendar__date, .calendar__day.event .calendar__task{
  z-index:9;
}

.event_modal{padding:10px; width:250px; color:#fff; box-shadow: 0px 1px 5px 3px rgba(60,64,67,0.3),0px 1px 6px 3px rgba(60,64,67,0.15);}
.event_modal h4, .event_modal h6{color:#fff;}
.event_modal ul{padding-left:2px;}
.event_modal ul li{list-style:none;color:#fff; min-height:25px; border-bottom:1px dashed rgba(255,255,255,.4);}
.event_modal ul li span{float:left; margin-right:3px;}

.calendar__date {
  color: #040605;
  font-size: 1.1rem; 
  font-weight: 400;
  line-height: 0.7; margin-bottom:20px;
}

.calendar__week .inactive .calendar__date,
.calendar__week .inactive .task-count {
  color: #c6c6c6;
}
.calendar__week .today .calendar__date {
  color:#82C63E;
}

.event .calendar__task {
  color: #fff; background:#aaa; border-radius:4px; padding:4px; margin-bottom:4px;
  display: -webkit-box;
  display: flex;
  font-size: 0.8rem;
}
@media screen and (min-width: 55em) {
}
@media screen and (max-width: 55em) {
.title-bar{width:100%;}
    
}
.calendar__task.calendar__task--today {  color:#83C442;}
  </style>

 
                  
<div id="calendar_div" class="mt-4">
                 <main class="calendar-contain"> 
        <section class="title-bar"> 
            <a href="javascript:void(0);" class="title-bar__prev" onclick="getCalendar('calendar_div','2023','12');"></a> 
            <div class="title-bar__month"> 
                <select class="month-dropdown"> 
                    <option value="01" selected="">January</option><option value="02">February</option><option value="03">March</option><option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option><option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option> 
                </select> 
            </div> 
            <div class="title-bar__year"> 
                <select class="year-dropdown"> 
                    <option value="2019">2019</option><option value="2020">2020</option><option value="2021">2021</option><option value="2022">2022</option><option value="2023">2023</option><option value="2024" selected="">2024</option><option value="2025">2025</option><option value="2026">2026</option><option value="2027">2027</option><option value="2028">2028</option><option value="2029">2029</option> 
                </select> 
            </div> 
            <a href="javascript:void(0);" class="title-bar__next" onclick="getCalendar('calendar_div','2024','02');"></a> 
        </section> 
         
        <!--<aside class="calendar__sidebar" id="event_list"><h2 class="sidebar__heading">Monday<br>January 01</h2>-->
        <!--<ul class="sidebar__list">-->
        <!--    <li class="sidebar__list-item"><span class="list-item__time">4:00 PM</span> My list name goes here</li>-->
        <!--    <li class="sidebar__list-item"><span class="list-item__time">2:00 PM</span> My list name goes here</li>-->
        <!--    <li class="sidebar__list-item"><span class="list-item__time">1:00 AM</span> Fully responsive javascript calendar with events for mobile and desktop</li>-->
        <!--    </ul>-->
        <!--</aside> -->
         
        <section class="calendar__days"> 
            <section class="calendar__top-bar"> 
                <span class="top-bar__days">Mon</span> 
                <span class="top-bar__days">Tue</span> 
                <span class="top-bar__days">Wed</span> 
                <span class="top-bar__days">Thu</span> 
                <span class="top-bar__days">Fri</span> 
                <span class="top-bar__days">Sat</span> 
                <span class="top-bar__days">Sun</span> 
            </section> 
            <section class="calendar__week"> 
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-1');"> 
                                    <span class="calendar__date">1</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-2');"> 
                                    <span class="calendar__date">2</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-3');"> 
                                    <span class="calendar__date">3</span> 
                                    <span class="calendar__task">no Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-4');"> 
                                    <span class="calendar__date">4</span> 
                                    <span class="calendar__task">no Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-5');"> 
                                    <span class="calendar__date">5</span> 
                                    <span class="calendar__task">no Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-6');"> 
                                    <span class="calendar__date">6</span> 
                                    <span class="calendar__task">0 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-7');"> 
                                    <span class="calendar__date">7</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                            </section><section class="calendar__week"> 
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-8');"> 
                                    <span class="calendar__date">8</span> 
                                    <span class="calendar__task">no Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-9');"> 
                                    <span class="calendar__date">9</span> 
                                    <span class="calendar__task">np Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-10');"> 
                                    <span class="calendar__date">10</span> 
                                    <span class="calendar__task">0 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-11');"> 
                                    <span class="calendar__date">11</span> 
                                    <span class="calendar__task">0 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-12');"> 
                                    <span class="calendar__date">12</span> 
                                    <span class="calendar__task">0 Events</span> 
                                </div> 
                             
                                <div class="calendar__day event" onclick="getEvents('2024-01-13');"> 
                                    <span class="calendar__date">13</span> 
                                    <span class="calendar__task" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background:#D7A90B;">13 Date Events</span> 
    
    <div class="event_modal dropdown-menu" style="background:#D7A90B;">                           <!-- Modal -->
          <h4 class="border-bottom pb-2">2024-13-01</h4>
          <h6>13 Date Event Name goes here</h6>
        <p>Any short or description about the event</p>  
       <ul><li><span class="material-symbols-outlined">badge</span> All Day EVent</li>
       <li><span class="material-symbols-outlined">location_on</span> Event Place</li>
       <li><span class="material-symbols-outlined">lock</span> Public</li>
       </ul>
</div>
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-14');"> 
                                    <span class="calendar__date">14</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                            </section>
                            <section class="calendar__week"> 
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-15');"> 
                                    <span class="calendar__date">15</span> 
                                    <span class="calendar__task">0 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-16');"> 
                                    <span class="calendar__date">16</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-17');"> 
                                    <span class="calendar__date">17</span> 
                                    <span class="calendar__task">0 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-18');"> 
                                    <span class="calendar__date">18</span> 
                                    <span class="calendar__task">0 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-19');"> 
                                    <span class="calendar__date">19</span> 
                                    <span class="calendar__task">0 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-20');"> 
                                    <span class="calendar__date">20</span> 
                                    <span class="calendar__task">0 Events</span> 
                                </div> 
                             
                                <div class="calendar__day event" onclick="getEvents('2024-01-21');"> 
                                    <span class="calendar__date">21</span> 
                                    <span class="calendar__task" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background:#1D73C0;">My  Events</span> 
    
    <div class="event_modal dropdown-menu" style="background:#1D73C0;">                           <!-- Modal -->
          <h4 class="border-bottom pb-2">2024-01-21</h4>
          <h6>My Event Name goes here</h6>
        <p>Any short or description about the event</p>  
       <ul><li><span class="material-symbols-outlined">badge</span> Seminar & Conference</li>
       <li><span class="material-symbols-outlined">location_on</span> Event Place</li>
       <li><span class="material-symbols-outlined">lock</span> Public</li>
       </ul>
</div>
      <span class="calendar__task" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background:#3C995B;">My 2nd Event</span> 
    
    <div class="event_modal dropdown-menu" style="background:#3C995B;">                           <!-- Modal -->
          <h4 class="border-bottom pb-2">2024-01-21</h4>
          <h6>Event 2nd Name</h6>
        <p>Any short or description about the event</p>  
       <ul><li><span class="material-symbols-outlined">badge</span> Business Meet</li>
       <li><span class="material-symbols-outlined">location_on</span> Event Location</li>
       <li><span class="material-symbols-outlined">lock</span> Private</li>
       </ul>
</div>                              
                                    
                                    
                                </div> 
                            </section><section class="calendar__week"> 
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-22');"> 
                                    <span class="calendar__date">22</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-23');"> 
                                    <span class="calendar__date">23</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-24');"> 
                                    <span class="calendar__date">24</span> 
                                    <span class="calendar__task">0 Events</span> 
                                </div> 
                             
                                <div class="calendar__day event" onclick="getEvents('2024-01-25');"> 
                                    <span class="calendar__date">25</span> 
                                   <span class="calendar__task" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background:#A7510A;">Indepence Day</span> 
    
    <div class="event_modal dropdown-menu" style="background:#A7510A;">                           <!-- Modal -->
          <h4 class="border-bottom pb-2">2024-01-25</h4>
          <h6>Indepence Day</h6>
        <p>Any short or description about the event</p>  
       <ul><li><span class="material-symbols-outlined">badge</span> Event name goes here</li>
       <li><span class="material-symbols-outlined">location_on</span> Event name goes here</li>
       <li><span class="material-symbols-outlined">lock</span> Public</li>
       </ul>
</div>
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-26');"> 
                                    <span class="calendar__date">26</span> 
                                    <span class="calendar__task">0 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-27');"> 
                                    <span class="calendar__date">27</span> 
                                    <span class="calendar__task">0 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-28');"> 
                                    <span class="calendar__date">28</span> 
                                    <span class="calendar__task">0 Events</span> 
                                </div> 
                            </section><section class="calendar__week"> 
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-29');"> 
                                    <span class="calendar__date">29</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-30');"> 
                                    <span class="calendar__date">30</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-31');"> 
                                    <span class="calendar__date">31</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                            <div class="calendar__day inactive"> 
                                <span class="calendar__date">1</span> 
                                <span class="calendar__task">upcoming</span> 
                            </div> 
                         
                            <div class="calendar__day inactive"> 
                                <span class="calendar__date">2</span> 
                                <span class="calendar__task">upcoming</span> 
                            </div> 
                         
                            <div class="calendar__day inactive"> 
                                <span class="calendar__date">3</span> 
                                <span class="calendar__task">upcoming</span> 
                            </div> 
                         
                            <div class="calendar__day inactive"> 
                                <span class="calendar__date">4</span> 
                                <span class="calendar__task">upcoming</span> 
                            </div> 
                        </section></section> 
    </main>
                 
                 
                 <!--< ?php echo $GetCalendar;?>-->
             </div>
          
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>

  $(document).ready(function() {
    $.ajax({
        url: "<?= base_url('admin/Office_tools/check_calendar_token') ?>", // adjust path
        method: "GET",
        success: function(response) {
            if (response.status === 'exists') {
                console.log("Google Calendar is already connected:", response.email);

             
                $('#google-calendar-btn').hide();

                autoLoadGoogleCalendar(response.access_token);
            } else {
                console.log("Calendar not yet connected. Button remains visible.");
            }
        },
        error: function(err) {
            console.error("Error checking token:", err);
        }
    });
});

function autoLoadGoogleCalendar(accessToken) {
    // Add your logic here to load calendar, display events, etc.
    console.log("Google Calendar ready with access token:", accessToken);
}

$(document).on('click', '.editEventBtn', function () {
    const eventId = $(this).data('id');
    const title = $(this).data('title');
    const startDate = $(this).data('date');

    const description = $(this).data('description');
    const location = $(this).data('location');

    // console.log(title);
    // console.log(startDate);
    // console.log(endDate);
    // console.log(description);
    // console.log(location);
    

    // Pre-fill modal fields
    $('#edit_event_id').val(eventId);
    $('#edit_eventname').val(title);
    $('#edit_event_start').val(startDate.split('T')[0]); // assuming ISO date
    $('#edit_event_end').val(startDate.split('T')[0]); // if exists
    $('#edit_description').val(description);
    $('#edit_location').val(location);

    // Show modal
    $('#editEventModal').modal('show');
});

$('#editEventForm').on('click', function (e) {
    e.preventDefault();

    const formData = {
        event_id: $('#edit_event_id').val(),
        title: $('#edit_eventname').val(),
        start_date: $('#edit_event_start').val(),
        end_date: $('#edit_event_end').val(),
        description: $('#edit_description').val(),
        location: $('#edit_location').val()
    };

    console.log(formData);
    

    $.ajax({
        url: '<?= base_url('admin/office_tools/updateGoogleEvent') ?>',
        type: 'POST',
        data: formData,
        success: function (res) {
            if (res.success) {
                alert('Event updated!');
                location.reload();
            } else {
                alert('Error: ' + res.error);
            }
        }
    });
});


$(document).ready(function () {
    $('.eventAdd').on('click', function () {
        var formData = {
            title: $('#eventname').val(),
            start_date: $('#event_start').val(),
            end_date: $('#event_end').val(),
            location: $('#eventplace').val(),
            description: $('textarea[name="description"]').val(),
            color: $('input[name="category_color"]:checked').val()
        };

        $.ajax({
            url: '<?= base_url('admin/office_tools/addGoogleEvent') ?>',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function (res) {
                alert('Event created on Google Calendar!');
                $('#addEvent').modal('hide');
                // Optional: reload calendar view
                location.reload();
            },
            error: function (err) {
                console.error(err);
                alert('Failed to create event.');
            }
        });
    });
});


// document.addEventListener('DOMContentLoaded', function () {
//     // Option 1: Use current month and year
//     const today = new Date();
//     const year = today.getFullYear();
//     const month = String(today.getMonth() + 1).padStart(2, '0'); // JS months are 0-indexed


//     getCalendar('calendar_div', year, month);
// });

function getAllGoogleEventDates() {
    if (!Array.isArray(googleEvents)) return [];

    const dates = googleEvents
        .filter(ev => ev.start) // only events with a start date
        .map(ev => {
            const date = new Date(ev.start);
            const y = date.getFullYear();
            const m = ('0' + (date.getMonth() + 1)).slice(-2); // 2-digit month
            const d = ('0' + date.getDate()).slice(-2);        // 2-digit day
            return `${y}-${m}-${d}`; // Format: YYYY-MM-DD
        });

    return [...new Set(dates)]; // remove duplicates if any
}




document.addEventListener('DOMContentLoaded', function () {
    // Extract first event date from googleEvents
    const firstEventDate = getAllGoogleEventDates();

    console.log(firstEventDate);
    

    // Load the calendar (optional, if not already done)
  

    // Call getEvents() with the first Google event date
    if (Array.isArray(firstEventDate) && firstEventDate.length > 0) {

      console.log(333);
      

      const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    getCalendar('calendar_div', year, month); // optional if already handled
        getEvents(firstEventDate);
    }
});



const googleEvents = <?= isset($jsonEvents) && !empty($jsonEvents) ? $jsonEvents : '[]' ?>;
console.log(googleEvents);


function getGoogleEventDates() {
    const dateSet = new Set();

    googleEvents.forEach(ev => {
        if (!ev.start) return;
        const date = new Date(ev.start);
        const y = date.getFullYear();
        const m = ('0' + (date.getMonth() + 1)).slice(-2);
        const d = date.getDate(); // no leading zero
        dateSet.add(`${y}-${m}-${d}`);
    });

    return Array.from(dateSet); // convert Set to array
}


function getCalendar(target_div, year, month){ 
    const eventDates = googleEvents;

    $.ajax({ 
        type: 'POST', 
        url: '<?php echo base_url();?>/admin/office_tools/showcalendar', 
        data: {
            func: 'getCalender',
            year: year,
            month: month,
            eventDates: eventDates // ← passing event dates array
        },
        success: function(html){ 
            $('#' + target_div).html(html); 
        } 
    }); 
}

         
        // function getEvents(date){ 
        //     $.ajax({ 
        //         type:'POST', 
        //         url:'<?php echo base_url();?>/admin/office_tools/showcalendar',  
        //         data:'func=getEvents&date='+date, 
        //         success:function(html){ 
        //             $('#event_list').html(html); 
        //         } 
        //     }); 
        // } 
         
        $(document).ready(function(){ 
            $('.month-dropdown').on('change',function(){ 
                getCalendar('calendar_div', $('.year-dropdown').val(), $('.month-dropdown').val()); 
            }); 
            $('.year-dropdown').on('change',function(){ 
                getCalendar('calendar_div', $('.year-dropdown').val(), $('.month-dropdown').val()); 
            }); 
        }); 
        
	</script>


<?php echo view('includes/footer_scripts'); ?>

</body>
</html>
