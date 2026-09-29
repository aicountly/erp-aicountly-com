<?php $header = array( 	'title' => 'Office Tools' ); ?>

<?php
  echo view('includes/header',$header); 	

?>
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



    <!--<a href="#" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addEvent">Add New Event</a>-->

   <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-outline-success">Back</a>
  </div> 
</div>


<!-- Modal -->
<!--<div class="modal fade" id="addEvent" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addEventLabel" aria-hidden="true">-->
<!--  <div class="modal-dialog">-->
<!--    <div class="modal-content">-->
<!--      <div class="modal-header">-->
<!--        <h3 class="" id="addEventLabel">Add New Event</h3>-->
<!--        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
<!--      </div>-->
<!--      <div class="modal-body">-->
<!--       <form id="googleEventForm">-->
<!--           <div class="row">-->
<!--               <p class="col-md-6"><label>Start Date</label><input type="date" name="start_date" class="form-control form-control-sm" id="event_start"></p>-->
<!--               <p class="col-md-6"><label>End Date</label><input type="date" name="end_date" class="form-control form-control-sm" id="event_end"></p>-->
<!--               <p class="col-md-6"><label>Name</label><input type="text" name="title" class="form-control form-control-sm" id="eventname"></p>-->
<!--               <p class="col-md-6"><label>Type</label>-->
<!--               <select name="type" name="eventtype" class="form-select form-select-sm"><option>One Day Event</option><option>Long Event</option><option>Seminar or Conference</option><option>Presentation</option>-->
<!--               <option>Project Highlight</option><option>Accounting Meet</option><option>others</option></select></p>-->
<!--               <p class="col-md-6"><label>Location</label><input type="text" name="location" class="form-control form-control-sm" id="eventplace"></p> -->
<!--               <p class="col-md-6"><label>Privacy</label><select name="type" class="form-select form-select-sm"><option>Public</option><option>Private</option><option>Invited Only</option> </select></p>-->
<!--              <p class="col-md-12"><label>Desription</label><textarea name="description" class="form-control form-control-sm" rows="2"></textarea></p>-->
<!--              <p class="col-12 colors">Label Color<br>-->
<!--            <label><input type="radio" name="category_color" value="#A7510A;" checked="checked"><span class="checkmark" style="background-color:#A7510A;"></span></label>-->
<!--            <label><input type="radio" name="category_color" value="#1D73C0"><span class="checkmark" style="background-color:#1D73C0;"></span></label>-->
<!--            <label><input type="radio" name="category_color" value="#3C995B"><span class="checkmark" style="background-color:#3C995B;"></span></label>-->
<!--            <label><input type="radio" name="category_color" value="#D7A90B"><span class="checkmark" style="background-color:#D7A90B;"></span></label>-->
<!--            <label><input type="radio" name="category_color" value="#DF4BEB"><span class="checkmark" style="background-color:#DF4BEB;"></span></label>-->
<!--            <label><input type="radio" name="category_color" value="#F0396B"><span class="checkmark" style="background-color:#F0396B;"></span></label>-->
<!--            <label><input type="radio" name="category_color" value="#5865C0"><span class="checkmark" style="background-color:#5865C0;"></span></label>-->
<!--            <label><input type="radio" name="category_color" value="#418C9C"><span class="checkmark" style="background-color:#418C9C;"></span></label>-->
<!--            <label><input type="radio" name="category_color" value="#958948"><span class="checkmark" style="background-color:#958948;"></span></label>-->
<!--            <label><input type="radio" name="category_color" value="#2291BB"><span class="checkmark" style="background-color:#2291BB;"></span></label>-->
<!--           <label><input type="radio" name="category_color" value="#333333"><span class="checkmark" style="background-color:#333333;"></span></label>-->
<!--        </p>-->
<!--           </div>-->
<!--       </form>-->
<!--      </div>-->
<!--      <div class="modal-footer">-->
<!--        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
<!--        <button type="button" class="btn btn-success eventAdd">Add Event</button>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--</div>-->

<!--<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">-->
<!--  <div class="modal-dialog">-->
<!--    <div class="modal-content">-->
<!--      <div class="modal-header">-->
<!--        <h3 id="editEventModalLabel">Edit Event</h3>-->
<!--        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
<!--      </div>-->
<!--      <div class="modal-body">-->
<!--        <form id="editGoogleEventForm">-->
<!--          <input type="hidden" id="edit_event_id" name="event_id">-->
<!--          <div class="row">-->
<!--            <p class="col-md-6"><label>Start Date</label><input type="date" id="edit_event_start" name="start_date" class="form-control form-control-sm"></p>-->
<!--            <p class="col-md-6"><label>End Date</label><input type="date" id="edit_event_end" name="end_date" class="form-control form-control-sm"></p>-->
<!--            <p class="col-md-6"><label>Name</label><input type="text" id="edit_eventname" name="title" class="form-control form-control-sm"></p>-->
<!--            <p class="col-md-6"><label>Location</label><input type="text" id="edit_location" name="location" class="form-control form-control-sm"></p>-->
<!--            <p class="col-md-12"><label>Description</label><textarea id="edit_description" name="description" class="form-control form-control-sm" rows="2"></textarea></p>-->
<!--          </div>-->
<!--        </form>-->
<!--      </div>-->
<!--      <div class="modal-footer">-->
<!--        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>-->
<!--        <button type="button" class="btn btn-primary" id="editEventForm">Save Changes</button>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--</div>-->






 
                  
<div id="calendar_div" class="mt-4">

                 
                 
<div class="cal-top">

  <button id="todayBtn" class="cal-btn">Today</button>

  <div class="cal-pill" style="margin-left:6px">
    <button id="prevBtn" class="cal-icon-btn" title="Previous"><i class="fa-solid fa-chevron-left cal-ico"></i></button>
    <button id="nextBtn" class="cal-icon-btn" title="Next"><i class="fa-solid fa-chevron-right cal-ico"></i></button>
  </div>

  <div id="rangeTitle" style="font-weight:700;margin-left:8px">Loading…</div>

  <div class="cal-search">
    <div class="cal-searchbox">
      <i class="fa-solid fa-filter cal-ico cal-s16" style="color:#64748b"></i>
      <input id="globalSearch" placeholder="Search events/people"/>
      <button id="clearSearch" class="cal-icon-btn cal-close-icon-btn" title="Clear"><i class="fa-solid fa-xmark cal-ico cal-s16"></i></button>
    </div>
    <div class="cal-views" id="viewSeg">
      <button data-view="dayGridMonth" >Month</button>
      <button data-view="timeGridWeek">Week</button>
      <button data-view="timeGridDay">Day</button>
      <button data-view="listWeek">List</button>
    </div>
    <!--<button id="openFilters" class="cal-icon-btn" title="Calendars & Filters"><i class="fa-solid fa-filter cal-ico"></i></button>-->
    <!--<button id="openSettings" class="cal-icon-btn" title="Settings"><i class="fa-solid fa-gear cal-ico"></i></button>-->
  </div>
</div>

<div class="cal-container">
  <div class="cal-card"><div id="calendar"></div></div>
</div>

<!-- Quick View -->
<div id="quickView" class="cal-qv" aria-hidden="true"></div>

<!-- Create/Edit Modal -->
<div id="itemModal" class="cal-modal"><div class="cal-mcard">
  <div class="cal-mhd"><div id="itemModalTitle">Create</div><button class="cal-icon-btn" id="itemClose"><i class="fa-solid fa-xmark cal-ico"></i></button></div>
  <form id="itemForm">
    <div class="cal-mbd create-mbd">
        
           
        
      <div class="cal-row"><div class="cal-seg2" id="typeSeg"><button type="button" data-type="event" class="cal-active ">   <i class="fa-solid fa-calendar-day fa-fw"></i> Event</button><button type="button" data-type="task">     <i class="fa-solid fa-square-check fa-fw"></i>Task</button></div></div>
     
     <div class="cal-col">
          <label class="cal-lab">Calendar</label>
          <select class="cal-sel" id="f_calendars"></select>
        </div>
     
      <div class="cal-row"><div class="cal-col"><label class="cal-lab">Title</label><input class="cal-inp" id="f_title" required></div><div class="cal-col"><label class="cal-lab">Calendar Event Types</label><select class="cal-sel" id="f_calendar"></select></div></div>

     


      <div id="eventFields">
        <div class="cal-chk"><input type="checkbox" id="f_allDay"> <label for="f_allDay">All day</label></div>
        <div class="cal-row"><div class="cal-col"><label class="cal-lab">Start</label><input type="datetime-local" class="cal-inp" id="f_start"></div><div class="cal-col"><label class="cal-lab">End</label><input type="datetime-local" class="cal-inp" id="f_end"></div></div>
        <div class="cal-row"><div class="cal-col"><label class="cal-lab">Repeat</label><select class="cal-sel" id="f_repeat"><option value="none">Doesn’t repeat</option><option value="daily">Daily</option><option value="weekly">Weekly</option><option value="monthly">Monthly</option><option value="yearly">Yearly</option></select></div><div class="cal-col"><label class="cal-lab">Visibility</label><select class="cal-sel" id="f_vis"><option>Public</option><option>Private</option></select></div></div>
        <div class="cal-row"><div class="cal-col"><label class="cal-lab">Attendees</label><input class="cal-inp" id="f_attendees" placeholder="Guest A, Guest B"></div><div class="cal-col"><label class="cal-lab">Location</label><input class="cal-inp" id="f_location"></div></div>
        <label class="cal-lab">Description</label><textarea class="cal-txt" id="f_desc"></textarea>
      </div>
      
      <input type="hidden" class="cal-inp" id="edit_id">

      <div id="taskFields" style="display:none">
        <div class="cal-row"><div class="cal-col"><label class="cal-lab">Due</label><input type="datetime-local" class="cal-inp" id="t_due"></div><div class="cal-col"><label class="cal-lab">Status</label><select class="cal-sel" id="t_status"><option value="todo">To-Do</option><option value="doing">Doing</option><option value="done">Done</option></select></div></div>
        <div class="cal-row"><div class="cal-col"><label class="cal-lab">Priority</label><select class="cal-sel" id="t_priority"><option>Normal</option><option>High</option><option>Low</option></select></div><div class="cal-col"><label class="cal-lab">Assignee</label><input class="cal-inp" id="t_assignee" placeholder="Owner"></div></div>
        <label class="cal-lab">Notes</label><textarea class="cal-txt" id="t_notes"></textarea>
      </div>
      <div id="tagsWrap" style="margin-top:6px;"></div>
    </div>
    <div class="cal-mft">
      <button type="button" class="cal-btn" id="btnHistory" style="margin-right:auto;display:none">View history</button>
      <!--<button type="button" class="cal-btn" id="btnDelete" style="display:none;color:#b91c1c;border-color:#ffd7d7">Delete</button>-->
      <button type="button" class="cal-btn" id="itemCancel">Cancel</button>
      <button type="submit" class="cal-btn bg-success cal-active">Save</button>
    </div>
  </form>
</div></div>

<!-- History Modal -->
<div id="historyModal" class="cal-modal"><div class="cal-mcard">
  <div class="cal-mhd"><div>History</div><button class="cal-icon-btn" id="histClose"><i class="fa-solid fa-xmark cal-ico"></i></button></div>
  <div class="cal-mbd" id="histList"></div>
  <div class="cal-mft"><button class="cal-btn" id="histOk">Close</button></div>
</div></div>

<!-- Filters Modal -->
<div id="filtersModal" class="cal-modal"><div class="cal-mcard cal-filt-card">
  <div class="cal-mhd"><div>Calendars & Filters</div><button class="cal-icon-btn" id="filtersClose"><i class="fa-solid fa-xmark cal-ico"></i></button></div>
  <div class="cal-mbd" style="max-height: 100vh;">
    <div class="cal-create-line" id="createBtn"><i class="fa-solid fa-plus cal-ico cal-s16"></i> Create</div>
    <div class="cal-search-people"><i class="fa-solid fa-users cal-ico cal-s16" style="color:#64748b"></i><input id="peopleQuery" class="cal-inp" placeholder="Search for people (matches attendees)" ></div>

    <div class="cal-f-head"><i class="fa-solid fa-calendar-day cal-ico cal-s16"></i> My calendars <button class="cal-icon-btn" id="openNewCal" title="Create other calendar" style="margin-left:20px"><i class="fa-solid fa-plus cal-ico cal-s16"></i></button></div>
    <div id="myCals"></div>

    <div class="cal-f-head" style="margin-top:14px"><i class="fa-regular fa-calendar-plus cal-ico cal-s16"></i> Other calendars <button class="cal-icon-btn" id="openNewCal" title="Create other calendar" style="margin-left:20px"><i class="fa-solid fa-plus cal-ico cal-s16"></i></button></div>
    <div id="otherCals"></div>

    <div style="height:6px"></div>
    <div style="font-size:12px;color:var(--muted)">Terms – Privacy</div>
  </div>
</div></div>

<!-- New Calendar Modal -->
<div id="newCalModal" class="cal-modal"><div class="cal-mcard">
  <div class="cal-mhd"><div>Create New calendar</div><button class="cal-icon-btn" id="ncClose"><i class="fa-solid fa-xmark cal-ico"></i></button></div>
  <div class="cal-mbd">
    <label class="cal-lab">Calendar name</label><input id="nc_name" class="cal-inp" placeholder="Side Project">
    <div style="height:8px"></div>
    <label class="cal-lab">Color</label>
    <div id="nc_palette" class="cal-palette"></div>
    <div id="nc_sel_line" class="cal-sel-line"></div>
  </div>
  <div class="cal-mft"><button class="cal-btn" id="ncCancel">Cancel</button><button class="cal-btn  bg-success cal-active" id="ncCreate">Create</button></div>
</div></div>


 </div>
             
             
             
<style>
:root{
  --accent:#3b82f6; --accent-50:#eef4ff; --accent-100:#dbeafe; --accent-700:#1d4ed8;
  --ink:#101114; --muted:#656a72; --line:#e7eaf1; --card:#fff; --bg:#f6f8fb;
  --radius:14px; --shadow-lg:0 18px 40px rgba(16,17,20,.08), 0 2px 8px rgba(16,17,20,.05);
  --shadow:0 8px 22px rgba(16,17,20,.06), 0 1px 4px rgba(16,17,20,.05);
}


#calendar_div{
    position: relative;
    z-index: 0;
    
    margin: 0;
    font: 14px / 1.35 Inter, system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
    color: var(--ink);
}
 button.cal-active{
     
    color: #fff!important;
}


.fc .fc-list-day-cushion, .fc .fc-list-table td {
    padding: 8px 14px !important;
}
.create-mbd{
        max-height: 40vh!important;
}
/* icon helpers */
.cal-ico{font-size:18px; color:#374151; line-height:1}
.cal-ico.cal-s16{font-size:16px}

/* top bar */
.cal-top{
  display:flex;align-items:center;gap:10px;padding:12px 16px;position:sticky;top:0;z-index:10;
 backdrop-filter:saturate(1.15) blur(6px);border-bottom:1px solid var(--line)
}
.cal-brand{display:flex;align-items:center;gap:10px;font-weight:700}
.cal-logo{width:30px;height:30px;border-radius:9px;display:grid;place-items:center;background:linear-gradient(145deg,var(--accent),#8b5cf6);color:#fff;box-shadow:var(--shadow)}
.cal-btn,.cal-chip{border:1px solid var(--line);background:#fff;border-radius:12px;padding:8px 12px;cursor:pointer}
.cal-btn:hover{background:#f5f8ff}
.cal-icon-btn{
  width:36px;height:36px;min-width:36px;min-height:36px;
  border:1px solid var(--line);background:#fff;border-radius:10px;cursor:pointer;
  display:grid;place-items:center
}
.cal-icon-btn:hover{background:#f5f8ff}
.cal-pill{border:1px solid var(--line);display:flex;border-radius:999px;overflow:hidden;background:#fff}
.cal-pill button{border:0;background:transparent;padding:0;min-width:36px}
.cal-pill .cal-icon-btn{border:0;border-radius:0}

/* search + views */
.cal-search{margin-left:auto;display:flex;gap:10px;align-items:center}
.cal-search .cal-searchbox{display:flex;align-items:center;gap:8px;border:1px solid var(--line);border-radius:999px;padding:8px 12px;background:#fff;min-width:260px;height:36px}
.cal-search input{border:0;outline:0;width:180px}
.cal-views{display:flex;border:1px solid var(--line);border-radius:999px;overflow:hidden;background:#fff}
.cal-views button{border:0;background:transparent;padding:8px 12px;min-width:56px;cursor:pointer}
.cal-views button.cal-active{background:var(--accent-100);color:var(--accent-700);font-weight:600}

/* main card */
.cal-container{max-width:1400px;margin:0 auto;}
.cal-card{background:#ffffffcc;border-radius:18px;padding:10px}

/* FullCalendar skin */

.fc .fc-timegrid-body,.fc .fc-timegrid-axis-frame ,.fc.fc-theme-standard .fc-daygrid-day {
    background-color:#fff!important;
}
.fc.fc-theme-standard .fc-list,.fc .fc-scrollgrid{
        border: 1px solid #ddd;
        border-color: #ddd!important;
}
.fc.fc-theme-standard th,.fc.fc-theme-standard td{
    border: 1px solid #ddd;
}
.fc.fc-theme-standard .fc-daygrid-day.fc-day-fri,.fc.fc-theme-standard .fc-daygrid-day.fc-day-sat{
   color:#000!important; 
}
.fc .fc-daygrid-day-top {
    display: flex;
    flex-direction: row-reverse!important;
    justify-content: flex-start;
}
.fc .fc-list,.fc .fc-list-table{
        border-radius: 0px!important;
}
.fc{--fc-now-indicator-color:#ef4444;--fc-page-bg-color:#fff!important;}
.fc .fc-col-header{position:sticky;top:0;z-index:2;background:linear-gradient(0deg,#fff,#fbfcff)}
.fc .fc-col-header-cell-cushion{padding:10px 0;font-weight:600;color:#3f4248}
.fc .fc-scrollgrid,.fc-theme-standard .fc-scrollgrid{border-color:#edf0f5;overflow:hidden}
.fc .fc-daygrid-day-frame{padding:6px}
.fc .fc-daygrid-day:hover{background:#f8fbff}
.fc .fc-daygrid-day.fc-day-today{background:var(--accent-50);box-shadow:inset 0 0 0 2px var(--accent-100)}
.fc .fc-daygrid-event,.fc .fc-timegrid-event{
  border-radius:999px;border:1px solid rgba(16,17,20,.06);padding:4px 8px;background:linear-gradient(180deg,#fff,#f7f9ff);
  color:#0b1728;font-weight:500;transition:transform .08s,box-shadow .08s
}
.fc .fc-daygrid-event:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(16,17,20,.12)}
.fc .fc-list,.fc .fc-list-table{border-radius:12px;border-color:#edf0f5}

/* quick view + modals */
.cal-qv{position:fixed;background:#fff;border:1px solid #25b003;border-radius:16px;box-shadow:var(--shadow-lg);
  width:min(560px,92vw);opacity:0;transform:translateY(6px) scale(.98);transition:.14s;z-index:60;display:none}
.cal-qv.cal-open{display:block;opacity:1;transform:translateY(0) scale(1)}
.cal-qv:after{content:"";position:absolute;top:-8px;left:24px;width:14px;height:14px;background:#fff;border-left:1px solid var(--line);border-top:1px solid var(--line);transform:rotate(45deg)}
.cal-qv-hd{display:flex;align-items:center;gap:10px;padding:14px 16px 6px}
.cal-dot{width:10px;height:10px;border-radius:3px}
.cal-qv-ttl{font-weight:700}
.cal-qv-icns{margin-left:auto;display:flex;gap:8px}
.cal-qv-bd{padding:6px 16px 12px;color:var(--muted)}
.cal-qv-li{display:flex;align-items:center;gap:10px;margin:8px 0}
.cal-qv-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:6px}
.cal-tag{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;background:#eff4ff;color:#1d4ed8;border:1px solid #d7e3ff}
.cal-qv-ft{padding:10px 16px;border-top:1px solid var(--line);display:flex;justify-content:flex-end}

.cal-modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(16,17,20,.42);backdrop-filter:blur(4px);z-index:70}
.cal-modal.cal-open{display:flex}
.cal-mcard{width:min(560px,94vw);background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow-lg)}
.cal-mhd{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid var(--line);font-weight:700}
.cal-mbd{padding:14px 16px;max-height:50vh;overflow:auto}
.cal-mft{display:flex;gap:10px;justify-content:flex-end;padding:14px 16px;border-top:1px solid var(--line)}
.cal-row{display:flex;gap:12px;margin-bottom:12px}
.cal-col{flex:1}
.cal-lab{display:block;font-size:12px;color:var(--muted);margin-bottom:6px}
.cal-inp,.cal-sel,.cal-txt{width:100%;border:1px solid var(--line);border-radius:12px;padding:10px 12px;font:inherit;background:#fff}
.cal-txt{min-height:88px}
.cal-chk{display:flex;align-items:center;gap:8px;margin-top:6px}
.cal-seg2{display:flex;border:1px solid var(--line);border-radius:999px;overflow:hidden;width:max-content;background:#fff}
.cal-seg2 button{border:0;background:#fff;padding:8px 14px;cursor:pointer}
.cal-seg2 button.cal-active{background:var(--accent-100);color:var(--accent-700);font-weight:600}
.cal-btn.cal-pri{background:var(--accent);border-color:var(--accent);color:#fff}

/* Filters modal (sidebar replacement) */
.cal-filt-card .cal-create-line{display:flex;align-items:center;justify-content:center;gap:8px;border:1px dashed var(--line);border-radius:12px;padding:10px 12px;margin-bottom:12px;cursor:pointer}
.cal-filt-card .cal-create-line:hover{background:#f9fbff}
.cal-search-people{display:flex;align-items:center;gap:8px;border:1px solid var(--line);border-radius:12px;padding:10px 12px;margin-bottom:14px}
.cal-f-head{display:flex;align-items:center;gap:8px;color:#1f2937;font-weight:700;margin:12px 2px 8px}
.cal-item{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px}
.cal-item:hover{background:#f8fafc}
.cal-dot-lg{width:14px;height:14px;border-radius:4px;border:2px solid currentColor;box-shadow:inset 0 0 0 2px #fff}
.cal-dot-lg.cal-filled{background:currentColor;border-color:currentColor}

/* -------- Palette (fixed) -------- */
.cal-palette{display:grid;grid-template-columns:repeat(9,36px);gap:12px}
@media (max-width:600px){.cal-palette{grid-template-columns:repeat(6,36px)}}
.cal-palette button.cal-swatch{
  width:32px;height:32px;border-radius:50%;
  border:2px solid #fff;background:var(--c,#3b82f6);
  box-shadow:0 2px 6px rgba(16,17,20,.18);
  cursor:pointer;position:relative;outline:0;
  display:inline-grid;place-items:center;
  transition:transform .08s,box-shadow .08s;
}
.cal-palette button.cal-swatch:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(16,17,20,.18)}
.cal-palette button.cal-swatch.cal-sel{box-shadow:0 0 0 3px #dbeafe,0 0 0 6px var(--c,#3b82f6)}
.cal-tick{
  position:absolute;inset:0;display:grid;place-items:center;
  color:#fff;font-weight:700;opacity:0;pointer-events:none;
}
.cal-sel .cal-tick{opacity:1}

.cal-sel-line{display:flex;align-items:center;gap:8px;margin-top:8px;color:var(--muted);font-size:13px}
.cal-sel-dot{width:14px;height:14px;border-radius:50%;border:2px solid #fff;box-shadow:0 1px 2px rgba(0,0,0,.2);background:var(--c,#3b82f6)}

.cal-close-icon-btn{height:28px!important;border:none!important;min-height:28px!important;}


/* --- Drawer mode (smooth) --- */
#filtersModal.as-drawer{
  /* right aligned */
  align-items: stretch;
  justify-content: flex-end;

  /* allow animation even before .cal-open */
  display: flex;                 /* override base display:none */
  opacity: 0;
  pointer-events: none;
  transition: opacity .28s ease;
}

#filtersModal.as-drawer .cal-mcard{
  width: 420px;
  max-width: 92vw;
  height: 100vh;
  margin-top: 102px;
  border-radius: 12px 0 0 12px;

  /* start off-screen to the right */
  transform: translateX(100%);
  transition: transform .32s cubic-bezier(.22,.61,.36,1); /* smoother curve */
  will-change: transform;
}

/* animate to visible + slide in */
#filtersModal.cal-open.as-drawer{
  opacity: 1;
  pointer-events: auto;
}
#filtersModal.cal-open.as-drawer .cal-mcard{
  transform: translateX(0);
}

/* Day cells par overlay */
.fc .fc-daygrid-day{
  position: relative; /* overlay ko position dene ke liye */
}

.fc .fc-daygrid-day::after{
  content: "";
  position: absolute;
  inset: 0;                 /* full cell cover */
  background: transparent;  /* default: no hover */
  transition: background .18s ease;
  pointer-events: none;     /* clicks drag etc. unaffected */
  border-radius: 4px;       /* optional: thoda soft */
}

/* Hover tint – yahan apna color do */
.fc .fc-daygrid-day:hover::after{
  background: rgba(232, 244, 255, 0.9); /* #f8fbff approx */
}

/* Today cell ka tint preserve/adjust karo (optional) */
.fc .fc-daygrid-day.fc-day-today::after{
  background: rgba(59, 130, 246, 0.10); /* --accent-50 jaisa */
}


</style>       
      
      
      
      
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/rrule@2.7.2/dist/es5/rrule.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/rrule@6.1.11/index.global.min.js"></script>       
             
    <script>
    
    
    
    
 document.addEventListener("DOMContentLoaded", function () {
    // Selector for both groups
    const groups = [".cal-views", ".cal-seg2"];

    groups.forEach(sel => {
        const btns = document.querySelectorAll(`${sel} button`);
        if (btns.length) {
            // ✅ Pehle button ko active + bg-success
            btns[0].classList.add("cal-active", "bg-success");

            // ✅ Click par active switch
            btns.forEach(btn => {
                btn.addEventListener("click", function () {
                    btns.forEach(b => b.classList.remove("cal-active", "bg-success"));
                    this.classList.add("cal-active", "bg-success");
                });
            });
        }
    });
});






    
(function(){
  "use strict";

  /* ---------------- Helpers ---------------- */
//   var calendars = {
//     work:{label:'Work',color:'#3b82f6',group:'my',enabled:false},
//     birthdays:{label:'Birthdays',color:'#25b003',group:'my',enabled:false},
//     tasks:{label:'Tasks',color:'#3b82f6',group:'my',enabled:true},
//     holidays:{label:'Holidays in India',color:'#25b003',group:'other',enabled:true}
//   };

var calendars = {
    1:{label:'Events',color:'#3b82f6',group:'my',enabled:false},
    2:{label:'Tasks',color:'#3b82f6',group:'my',enabled:false},
    3:{label:'Appointment',color:'#25b003',group:'my',enabled:false},
    4:{label:'Meeting',color:'#3b82f6',group:'my',enabled:true},
    5:{label:'Scheduling',color:'#25b003',group:'other',enabled:true}
  };

  var palette=['#3b82f6','#25b003','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#16a34a','#6b7280'];
  var paletteNames={'#3b82f6':'Blue','#25b003':'Green','#f59e0b':'Amber','#ef4444':'Red','#8b5cf6':'Purple','#06b6d4':'Cyan','#f97316':'Orange','#16a34a':'Emerald','#6b7280':'Gray'};

  function fmtLocal(d){return d?new Date(d.getTime()-d.getTimezoneOffset()*60000).toISOString().slice(0,16):'';}
  function parseLocal(s){ return s ? new Date(s) : null; }
  function now(){return new Date();}
  function slug(s){return String(s).toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'')||'cal';}
  function typeLabel(t){return t==='task'?'Task':'Event';}
  function escapeHTML(s){return String(s).replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]);}
  function dateOnly(d){
    var y=d.getFullYear(), m=('0'+(d.getMonth()+1)).slice(-2), da=('0'+d.getDate()).slice(-2);
    return y+'-'+m+'-'+da;
  }
  function historyPush(ev,action,changes){
    var h = Array.isArray(ev.extendedProps.history)? ev.extendedProps.history.slice() : [];
    h.push({ts:new Date().toISOString(),action:action,changes:changes||{}});
    ev.setExtendedProp('history',h);
  }
  function seedHistory(item){return [{ts:new Date().toISOString(),action:'created',changes:item}];}
  function snapshot(ev){return {title:ev.title,start:ev.start,end:ev.end,allDay:ev.allDay,xp:Object.assign({},ev.extendedProps)};}
  function diff(b,ev){
    var o={}, k, a, c;
    if(b.title!==ev.title) o.title=[b.title,ev.title];
    if(+b.start!==+(ev.start||0)) o.start=[b.start,ev.start];
    if(+b.end!==+(ev.end||0)) o.end=[b.end,ev.end];
    if(b.allDay!==ev.allDay) o.allDay=[b.allDay,ev.allDay];
    var keys = {};
    for(k in (b.xp||{})) keys[k]=1;
    for(k in (ev.extendedProps||{})) keys[k]=1;
    for(k in keys){
      a=b.xp?b.xp[k]:undefined; c=ev.extendedProps?ev.extendedProps[k]:undefined;
      if(JSON.stringify(a)!==JSON.stringify(c)) o['xp.'+k]=[a,c];
    }
    return o;
  }
  function bindModal(id){
    var el=document.getElementById(id);
    return { open:function(){el.classList.add('cal-open');}, close:function(){el.classList.remove('cal-open');} };
  }

  /* ---------------- Seed events ---------------- */
  function plus(dy,time){
    var d=new Date(); d.setDate(d.getDate()+dy);
    if(time){var sp=time.split(':'),h=+sp[0],m=+sp[1]; d.setHours(h,m,0,0);} else {d.setHours(0,0,0,0);}
    return d.toISOString();
  }
  
//   var eventsSeed=[
//     {id:'task1',title:'Task: Prepare report',start:'2025-10-01T10:00:00',end:'2025-10-01T11:00:00',
//      backgroundColor:calendars.tasks.color,borderColor:calendars.tasks.color,textColor:'#0b1728',
//      extendedProps:{calendar:'tasks',type:'task',status:'todo',priority:'Normal',attendees:['Guest'],history:seedHistory({title:'Task: Prepare report'})}},
//  {id:'task2',title:'Task: report',start:'2025-10-02T10:00:00',end:'2025-10-02T11:00:00',
//      backgroundColor:calendars.tasks.color,borderColor:calendars.tasks.color,textColor:'#0b1728',
//      extendedProps:{calendar:'tasks',type:'task',status:'todo',priority:'Normal',attendees:['Guest'],history:seedHistory({title:'Task: Prepare report'})}},
 
//   ];
  
  
var eventsSeed = [];
    
  
  console.log(eventsSeed);

  /* ---------------- Main calendar ---------------- */
  var calEl=document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calEl, {
  initialView: 'dayGridMonth',
  height: 'auto',
  timeZone: 'local',
  selectable: true,
  editable: true,
  dayMaxEventRows: true,
  headerToolbar: false,
  moreLinkClick: 'popover',
  events: eventsSeed,
  slotMinTime: '06:00:00',
  slotMaxTime: '22:00:00',

  // ✅ Highlight day title if event exists
  dayCellDidMount: function(info) {
    let dateStr = info.date.toISOString().split('T')[0]; // format YYYY-MM-DD
    let hasEvent = eventsSeed.some(ev => ev.start.startsWith(dateStr));

    if (hasEvent) {
      // make date bold or colored
      info.el.querySelector('.fc-daygrid-day-number').style.color = 'red';
      info.el.querySelector('.fc-daygrid-day-number').style.fontWeight = 'bold';
    }
  },

  eventDidMount: function(info) {
    info.el.style.boxShadow = '0 1px 2px rgba(16,17,20,.05), 0 1px 8px rgba(16,17,20,.06)';
  },

  select: function(info) {
    openItemModal({mode:'create',type:'event',start:info.start,end:info.end,allDay:info.allDay});
    calendar.unselect();
  },
  eventClick: function(info) {   openQuickView(info.event, info.el); },
  eventDrop: function(info) { historyPush(info.event,'moved',{start:info.event.start,end:info.event.end}); },
  eventResize: function(info) { historyPush(info.event,'resized',{start:info.event.start,end:info.event.end}); },
  datesSet: function() {
    document.getElementById('rangeTitle').textContent = calendar.view.title;
  }
});

  calendar.render();
  window.calendar = calendar;
  document.getElementById('rangeTitle').textContent=calendar.view.title;

  /* ---------------- Top actions ---------------- */
  document.getElementById('prevBtn').onclick=function(){ calendar.prev(); };
  document.getElementById('nextBtn').onclick=function(){ calendar.next(); };
  document.getElementById('todayBtn').onclick=function(){ calendar.today(); };
  var viewBtns=document.querySelectorAll('#viewSeg button');
  for(var i=0;i<viewBtns.length;i++){
    viewBtns[i].onclick=(function(btn){
      return function(){
        for(var j=0;j<viewBtns.length;j++) viewBtns[j].classList.remove('cal-active');
        btn.classList.add('cal-active');
        calendar.changeView(btn.getAttribute('data-view'));
      };
    })(viewBtns[i]);
  }

  /* ---------------- Filters modal ---------------- */
//   var filtersModal=bindModal('filtersModal');
//   document.getElementById('openFilters').onclick=function(){ renderSidebar(); filtersModal.open(); };
//   document.getElementById('filtersClose').onclick=function(){ filtersModal.close(); };
  
  // openFilters (filter icon) => normal center modal (drawer class hata do just in case)
// document.getElementById('openFilters').onclick = function(){
//   document.getElementById('filtersModal').classList.remove('as-drawer');
//   renderSidebar();
//   filtersModal.open();
// };



/* ---------------- Filters modal ---------------- */
var filtersModal = bindModal('filtersModal');

// Safe binding: button ho ya na ho, error nahi ayega
var openFiltersBtn = document.getElementById('openFilters');
if (openFiltersBtn) {
  openFiltersBtn.addEventListener('click', function () {
    var fm = document.getElementById('filtersModal');
    fm.classList.remove('as-drawer'); // center modal mode
    renderSidebar();
    filtersModal.open();
  });
}

// Close button as-is
document.getElementById('filtersClose').onclick = function () {
  filtersModal.close();
};


// Office Tools (offline_bolt) par same Filters ko drawer ki tarah khol do
(function(){
  const officeBtn = Array.from(document.querySelectorAll('.taskmenus a'))
    .find(a => (a.querySelector('.material-symbols-outlined') || {}).textContent?.trim() === 'offline_bolt');
  if (!officeBtn) return;

  officeBtn.addEventListener('click', function(e){
    e.preventDefault();

    const fm = document.getElementById('filtersModal');
    fm.classList.add('as-drawer');   // drawer mode ON
    renderSidebar();

    // smooth open: ek frame pehle initial state apply, next frame me .cal-open add
    fm.classList.remove('cal-open'); // ensure clean state
    void fm.offsetWidth;             // force reflow
    requestAnimationFrame(() => {
      // agar bindModal ka instance use karna hai:
      if (window.filtersModal && typeof filtersModal.open === 'function') {
        filtersModal.open();         // internally .cal-open add hoti hai
      } else {
        fm.classList.add('cal-open');
      }
    });
  });
})();


// ---- Filters drawer closing ----
(function () {
  var fm   = document.getElementById('filtersModal');
  var card = fm.querySelector('.cal-mcard');

  // X button
  document.getElementById('filtersClose').onclick = function () {
    filtersModal.close();
    fm.classList.remove('as-drawer');   // reset (normal modal ke liye)
  };

  // Backdrop click (card ke bahar click)
  fm.addEventListener('click', function (e) {
    if (!card.contains(e.target)) {
      filtersModal.close();
      fm.classList.remove('as-drawer');
    }
  });

  // ESC key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && fm.classList.contains('cal-open')) {
      filtersModal.close();
      fm.classList.remove('as-drawer');
    }
  });
})();

  

  // Create button inside Filters -> opens create modal
  document.getElementById('createBtn').onclick=function(){
    filtersModal.close();
    var base = calendar.getDate();
    var s = new Date(base.getFullYear(), base.getMonth(), base.getDate(), 10, 0, 0, 0);
    var e = new Date(base.getFullYear(), base.getMonth(), base.getDate(), 11, 0, 0, 0);
    openItemModal({mode:'create',type:'event',start:s,end:e,allDay:false});
  };

  function renderSidebar(){
    // var my=document.getElementById('myCals'), ot=document.getElementById('otherCals');
    // my.innerHTML=''; ot.innerHTML='';
    // function add(k,c,root){
    //   var el=document.createElement('label'); el.className='cal-item'; el.style.color=c.color;
    //   el.innerHTML='<input type="checkbox" class="calToggle" data-cal="'+k+'" '+(c.enabled?'checked':'')+'>'+
    //               '<span class="cal-dot-lg '+(c.enabled?'cal-filled':'')+'"></span>'+escapeHTML(c.label);
    //   root.appendChild(el);
    // }
    // for(var key in calendars){ if(calendars[key].group==='my') add(key,calendars[key],my); }
    // for(var key2 in calendars){ if(calendars[key2].group!=='my') add(key2,calendars[key2],ot); }
    // var toggles=document.querySelectorAll('.calToggle');
    // for(var t=0;t<toggles.length;t++){
    //   toggles[t].addEventListener('change',function(e){
    //     var k=e.target.getAttribute('data-cal');
    //     calendars[k].enabled=e.target.checked;
    //     e.target.nextElementSibling.classList.toggle('cal-filled',e.target.checked);
    //     applyFilters();
    //   });
    // }
  }
  function applyFilters(){
    var enabled={}; for(var k in calendars){ if(calendars[k].enabled) enabled[k]=1; }
    var evs=calendar.getEvents();
    
    for(var i=0;i<evs.length;i++){
      var key=(evs[i].extendedProps && evs[i].extendedProps.calendar) || '';
      evs[i].setProp('display', enabled[key] ? 'auto' : 'none');
    }
  }
  renderSidebar(); applyFilters();

  function renderCalendarSelect(){
    var sel=document.getElementById('f_calendar'); sel.innerHTML='';
    for(var k in calendars){ var o=document.createElement('option'); o.value=k; o.textContent=calendars[k].label; sel.appendChild(o); }
  }
  renderCalendarSelect();

  /* People search */
  document.getElementById('peopleQuery').addEventListener('input',function(){
    var q=this.value.trim().toLowerCase();
    var evs=calendar.getEvents();
    for(var i=0;i<evs.length;i++){
      if(!q){ evs[i].setProp('display','auto'); continue; }
      var at=(evs[i].extendedProps && evs[i].extendedProps.attendees) ? evs[i].extendedProps.attendees : [];
      var ok=false; for(var a=0;a<at.length;a++){ if(String(at[a]).toLowerCase().indexOf(q)>-1){ ok=true; break; } }
      evs[i].setProp('display', ok ? 'auto' : 'none');
    }
    if(!q) applyFilters();
  });

  /* Global search */
  var gq=document.getElementById('globalSearch');
  document.getElementById('clearSearch').onclick=function(){ gq.value=''; applyFilters(); };
  gq.addEventListener('input',function(){
    var q=gq.value.replace(/^\s+|\s+$/g,'').toLowerCase();
    if(!q){ applyFilters(); return; }
    var evs=calendar.getEvents();
    
    for(var i=0;i<evs.length;i++){
      var title=(evs[i].title||'').toLowerCase();
      var at=(evs[i].extendedProps && evs[i].extendedProps.attendees) ? evs[i].extendedProps.attendees : [];
      var match = title.indexOf(q)>-1;
      if(!match){
        for(var a=0;a<at.length;a++){ if(String(at[a]).toLowerCase().indexOf(q)>-1){ match=true; break; } }
      }
      evs[i].setProp('display', match ? 'auto' : 'none');
    }
  });

  /* ---------------- Create/Edit modal ---------------- */
  var itemModal=bindModal('itemModal'), histModal=bindModal('historyModal');
  var currentMode='create', editingEvent=null, currentType='event';
  var typeBtns=document.querySelectorAll('#typeSeg button');
  for(var tb=0;tb<typeBtns.length;tb++){
    typeBtns[tb].onclick=(function(btn){ return function(){ setType(btn.getAttribute('data-type')); };})(typeBtns[tb]);
  }
  function setType(t){
    currentType=t;
    for(var tb2=0;tb2<typeBtns.length;tb2++){ typeBtns[tb2].classList.toggle('cal-active', typeBtns[tb2].getAttribute('data-type')===t); }
    document.getElementById('eventFields').style.display = (t==='event') ? '' : 'none';
    document.getElementById('taskFields').style.display  = (t==='task')  ? '' : 'none';
  }

  var f_title=document.getElementById('f_title'), f_calendar=document.getElementById('f_calendar'), f_allDay=document.getElementById('f_allDay');
  var f_start=document.getElementById('f_start'), f_end=document.getElementById('f_end'), f_attendees=document.getElementById('f_attendees');
  var f_location=document.getElementById('f_location'), f_desc=document.getElementById('f_desc');
  var f_repeat=document.getElementById('f_repeat'), f_vis=document.getElementById('f_vis');
  var t_due=document.getElementById('t_due'), t_status=document.getElementById('t_status'), t_priority=document.getElementById('t_priority');
  var t_assignee=document.getElementById('t_assignee'), t_notes=document.getElementById('t_notes');
  var tagsWrap=document.getElementById('tagsWrap');

  function tagRender(xp){
    tagsWrap.innerHTML='';
    var html='';
    html+='<span class="cal-tag"><i class="fa-solid fa-tag cal-ico cal-s16"></i>'+typeLabel(xp.type||'event')+'</span>';
    if(xp.status)   html+='<span class="cal-tag"><i class="fa-solid fa-check cal-ico cal-s16"></i>Status: '+xp.status+'</span>';
    if(xp.priority) html+='<span class="cal-tag"><i class="fa-regular fa-flag cal-ico cal-s16"></i>'+xp.priority+'</span>';
    if(xp.assignee) html+='<span class="cal-tag"><i class="fa-regular fa-user cal-ico cal-s16"></i>'+escapeHTML(xp.assignee)+'</span>';
    if(xp.location) html+='<span class="cal-tag"><i class="fa-solid fa-location-dot cal-ico cal-s16"></i>'+escapeHTML(xp.location)+'</span>';
    if(xp.attendees && xp.attendees.length) html+='<span class="cal-tag"><i class="fa-solid fa-users cal-ico cal-s16"></i>'+xp.attendees.length+' attendee(s)</span>';
    if(xp.visibility) html+='<span class="cal-tag"><i class="fa-solid fa-earth-asia cal-ico cal-s16"></i>'+xp.visibility+'</span>';
    if(xp.rrule) html+='<span class="cal-tag"><i class="fa-solid fa-rotate cal-ico cal-s16"></i>'+xp.rrule+'</span>';
    tagsWrap.innerHTML=html;
  }

  function openItemModal(opts){
    closeQuickView();
    var mode=opts.mode, type=opts.type||'event', start=opts.start||null, end=opts.end||null, allDay=!!opts.allDay, event=opts.event||null;
    currentMode=mode; editingEvent=event;
    document.getElementById('itemModalTitle').textContent=(mode==='create'?'Create':'Edit');
    // document.getElementById('btnDelete').style.display=(mode==='edit'?'':'none');
    document.getElementById('btnHistory').style.display=(mode==='edit'?'':'none');
    setType(type); renderCalendarSelect();

    f_title.value=''; f_calendar.value='tasks'; f_allDay.checked=allDay;
    f_start.value=fmtLocal(start||now()); f_end.value=fmtLocal(end||new Date(now().getTime()+60*60*1000));
    f_repeat.value='none'; f_vis.value='Public'; f_attendees.value=''; f_location.value=''; f_desc.value='';
    t_due.value=''; t_status.value='todo'; t_priority.value='Normal'; t_assignee.value=''; t_notes.value='';
    tagsWrap.innerHTML='';

    if (mode === 'edit' && event) {
    var xp = event.extendedProps || {};
    console.log(event);
    
    
    
    
    // if (xp.type === 'event') {
        document.getElementById("eventFields").style.display = "block";
        document.getElementById("taskFields").style.display = "none";
    // }
    // If type is "task"
    // else if (xp.type === 'task') {
    //     document.getElementById("taskFields").style.display = "block";
    //     document.getElementById("eventFields").style.display = "none";
    // }

    // Fill form values
    f_title.value = event.title || '';
    f_calendar.value = xp.calendar || 'tasks';
    f_allDay.checked = !!event.allDay;
    if (event.start) f_start.value = fmtLocal(event.start);
    if (event.end) f_end.value = fmtLocal(event.end);
    f_attendees.value = (xp.attendees || []).join(', ');
    f_location.value = xp.location || '';
    f_desc.value = xp.desc || '';
    f_vis.value = xp.visibility || 'Public';
    if (xp.rrule) f_repeat.value = xp.rrule;
    
    document.getElementById('edit_id').value = event.id;
    
    document.getElementById('f_calendars').value = xp.cal_id;
    document.getElementById('f_calendar').value = xp.type;

    // Task-specific fields
    t_status.value = xp.status || 'todo';
    t_priority.value = xp.priority || 'Normal';
    t_assignee.value = xp.assignee || '';
    t_notes.value = xp.desc || '';

    tagRender(xp);
}

    itemModal.open();
  }

  document.getElementById('itemForm').addEventListener('submit',function(e){
    e.preventDefault();
    var title=(f_title.value||'').trim(); if(!title) return;
    var calKey=f_calendar.value, color=(calendars[calKey]&&calendars[calKey].color)||'#3b82f6';
    var attendees=f_attendees.value.split(',').map(function(s){return s.trim();}).filter(function(x){return !!x;});
    var vis=f_vis.value;

    if(currentType==='event'){
      var allDay=!!f_allDay.checked;
      var s=parseLocal(f_start.value), en=parseLocal(f_end.value);
      var repeat=f_repeat.value;

      if(currentMode==='create'){
        if(repeat!=='none'){
          var mins=Math.max(30,Math.round((en-s)/60000));
          var ev=calendar.addEvent({
            title:title, allDay:allDay,
            rrule:{freq:repeat,dtstart: allDay ? new Date(s.getFullYear(),s.getMonth(),s.getDate()) : s},
            duration: allDay ? 'P1D' : ('PT'+mins+'M'),
            backgroundColor:color, borderColor:color, textColor:'#0b1728',
            extendedProps:{calendar:calKey,type:'event',attendees:attendees,location:f_location.value.trim(),description:f_desc.value.trim(),visibility:vis,rrule:repeat,history:seedHistory({title:title})}
          });
          historyPush(ev,'created',{title:title});
        }else{
          var ev2=calendar.addEvent({
            title:title,
            start: allDay ? dateOnly(s) : s,
            end:   allDay ? dateOnly(en) : en,
            allDay:allDay,
            backgroundColor:color, borderColor:color, textColor:'#0b1728',
            extendedProps:{calendar:calKey,type:'event',attendees:attendees,location:f_location.value.trim(),description:f_desc.value.trim(),visibility:vis,history:seedHistory({title:title})}
          });
          historyPush(ev2,'created',{title:title});
        }
      }else{
        var ev3=editingEvent, before=snapshot(ev3);
        var wantsRepeat=(repeat!=='none'); var hadRepeat=!!(ev3.extendedProps && ev3.extendedProps.rrule);
        if(hadRepeat || wantsRepeat){
          ev3.remove();
          var mins2=Math.max(30,Math.round((en-s)/60000));
          var newEv = calendar.addEvent(wantsRepeat ? {
            title:title, allDay:allDay,
            rrule:{freq:repeat,dtstart: allDay ? new Date(s.getFullYear(),s.getMonth(),s.getDate()) : s},
            duration: allDay?'P1D':('PT'+mins2+'M'),
            backgroundColor:color, borderColor:color, textColor:'#0b1728',
            extendedProps:{calendar:calKey,type:'event',attendees:attendees,location:f_location.value.trim(),description:f_desc.value.trim(),visibility:vis,rrule:repeat,history:(before.xp&&before.xp.history)||[]}
          } : {
            title:title, start:allDay?dateOnly(s):s, end:allDay?dateOnly(en):en, allDay:allDay,
            backgroundColor:color, borderColor:color, textColor:'#0b1728',
            extendedProps:{calendar:calKey,type:'event',attendees:attendees,location:f_location.value.trim(),description:f_desc.value.trim(),visibility:vis,history:(before.xp&&before.xp.history)||[]}
          });
          editingEvent=newEv;
        }else{
          ev3.setProp('title',title); ev3.setAllDay(allDay);
          ev3.setStart(allDay?dateOnly(s):s); ev3.setEnd(allDay?dateOnly(en):en);
          ev3.setProp('backgroundColor',color); ev3.setProp('borderColor',color);
          ev3.setExtendedProp('calendar',calKey); ev3.setExtendedProp('type','event');
          ev3.setExtendedProp('attendees',attendees); ev3.setExtendedProp('location',f_location.value.trim());
          ev3.setExtendedProp('description',f_desc.value.trim()); ev3.setExtendedProp('visibility',vis); ev3.setExtendedProp('rrule',null);
        }
        tagRender((editingEvent||ev3).extendedProps);
        historyPush(editingEvent||ev3,'updated',diff(before,editingEvent||ev3));
      }
    }else{
      var due=t_due.value?parseLocal(t_due.value):null;
      var xp={calendar:calKey,type:'task',status:t_status.value,priority:t_priority.value,assignee:t_assignee.value.trim(),notes:t_notes.value.trim(),attendees:attendees,visibility:vis};
      if(currentMode==='create'){
        var st=due||parseLocal(f_start.value), en2=new Date((st||now()).getTime()+60*60*1000);
        var tev=calendar.addEvent({title:title,start:st,end:en2,allDay:false,backgroundColor:color,borderColor:color,textColor:'#0b1728',extendedProps:Object.assign({},xp,{history:seedHistory({title:title})})});
        historyPush(tev,'created',{title:title});
      }else{
        var ev4=editingEvent, before2=snapshot(ev4);
        ev4.setProp('title',title); ev4.setAllDay(false);
        if(due){ ev4.setStart(due); ev4.setEnd(new Date(due.getTime()+60*60*1000)); }
        ev4.setProp('backgroundColor',color); ev4.setProp('borderColor',color);
        ev4.setExtendedProp('calendar',calKey); ev4.setExtendedProp('type','task');
        ev4.setExtendedProp('status',xp.status); ev4.setExtendedProp('priority',xp.priority);
        ev4.setExtendedProp('assignee',xp.assignee); ev4.setExtendedProp('notes',xp.notes);
        ev4.setExtendedProp('attendees',attendees); ev4.setExtendedProp('visibility',vis);
        tagRender(ev4.extendedProps); historyPush(ev4,'updated',diff(before2,ev4));
      }
    }
    itemModal.close(); applyFilters();
  });

//   document.getElementById('btnDelete').onclick=function(){ if(editingEvent){ historyPush(editingEvent,'deleted',{title:editingEvent.title}); editingEvent.remove(); itemModal.close(); closeQuickView(); } };
  document.getElementById('btnHistory').onclick=function(){
    var ev=editingEvent; if(!ev) return;
    var list=document.getElementById('histList'); list.innerHTML='';
    var hist=ev.extendedProps.history||[];
    if(!hist.length){ list.innerHTML='<div>No history.</div>'; }
    for(var i=hist.length-1;i>=0;i--){
      var h=hist[i], when=new Date(h.ts), d=document.createElement('div'); d.style.margin='10px 0';
      var block='<div><strong>'+h.action+'</strong> <span style="color:#6b7280">– '+when.toLocaleString()+'</span></div>';
      if(h.changes && Object.keys(h.changes).length){
        block+='<pre style="white-space:pre-wrap;background:#f6f9fe;border:1px solid var(--line);padding:8px;border-radius:8px;margin-top:6px">'+escapeHTML(JSON.stringify(h.changes,null,2))+'</pre>';
      }
      d.innerHTML=block; list.appendChild(d);
    }
    histModal.open();
  };
  document.getElementById('itemClose').onclick=function(){ itemModal.close(); };
  document.getElementById('itemCancel').onclick=function(){ itemModal.close(); };
  document.getElementById('histClose').onclick=function(){ histModal.close(); };
  document.getElementById('histOk').onclick=function(){ histModal.close(); };

  /* ---------------- Quick View ---------------- */
  var qv=document.getElementById('quickView'), qvEvent=null;
  function openQuickView(event,anchor){
    qvEvent=event;
    
    console.log(qvEvent);
    
    var calKey=event.extendedProps?event.extendedProps.calendar:''; 
    var calName=(calendars[calKey]&&calendars[calKey].label)||'Calendar';
    var color=(calendars[calKey]&&calendars[calKey].color)||(event.backgroundColor||'#3b82f6');
    var type=(event.extendedProps&&event.extendedProps.type)||'event';
    var rangeText=(function(s,e,allDay){
      if(allDay){return new Intl.DateTimeFormat(undefined,{weekday:'long',month:'long',day:'numeric'}).format(new Date(s));}
      var d1=new Intl.DateTimeFormat(undefined,{weekday:'short',month:'short',day:'numeric'}).format(new Date(s));
      var t1=new Intl.DateTimeFormat(undefined,{hour:'numeric',minute:'2-digit'}).format(new Date(s));
      var t2=e?new Intl.DateTimeFormat(undefined,{hour:'numeric',minute:'2-digit'}).format(new Date(e)):''; return d1+' • '+t1+(t2?('–'+t2):'');
    })(event.start||event.startStr,event.end||event.endStr,event.allDay);

    var html='';
    html+='<div class="cal-qv-hd"><div class="cal-dot" style="background:'+color+'"></div>';
    html+='<div class="cal-qv-ttl">'+escapeHTML(event.title||'(No title)')+'</div>';
    html+='<div class="cal-qv-icns"><button id="qvHist" class="cal-icon-btn" title="History"><i class="fa-regular fa-clock cal-ico"></i></button><button id="qvEdit" class="cal-icon-btn" title="Edit"><i class="fa-regular fa-pen-to-square cal-ico"></i></button><button id="qvDel" data-id="'+event.id+'" class="cal-icon-btn" title="Delete"><i class="fa-solid fa-trash cal-ico"></i></button><button id="qvClose" class="cal-icon-btn" title="Close"><i class="fa-solid fa-xmark cal-ico"></i></button></div></div>';
    html+='<div class="cal-qv-bd"><div class="cal-qv-li"><i class="fa-regular fa-clock cal-ico cal-s16"></i>'+escapeHTML(rangeText)+'</div><div class="cal-qv-li"><i class="fa-solid fa-calendar-day cal-ico cal-s16"></i>'+escapeHTML(calName)+'</div>';
    html+='<div class="cal-qv-tags"><span class="cal-tag"><i class="fa-solid fa-tag cal-ico cal-s16"></i>'+typeLabel(type)+'</span><span class="cal-tag"><i class="fa-solid fa-earth-asia cal-ico cal-s16"></i>'+escapeHTML((event.extendedProps&&event.extendedProps.visibility)||'Public')+'</span></div></div>';
    html+='<div class="cal-qv-ft"><button class="cal-btn" id="qvMore">More details</button></div>';
    qv.innerHTML=html;

    qv.style.visibility='hidden';
    qv.classList.add('cal-open'); qv.setAttribute('aria-hidden','false');
    var r=anchor.getBoundingClientRect(), w=qv.offsetWidth;
    var left=Math.min(window.innerWidth-w-12,Math.max(12,r.left-20));
    var top=Math.max(12,r.bottom+8);
    qv.style.left=left+'px'; qv.style.top=top+'px'; qv.style.visibility='visible';

    document.getElementById('qvClose').onclick=closeQuickView;
    document.getElementById('qvEdit').onclick=function(){ openItemModal({mode:'edit',event:qvEvent}); };
    document.getElementById('qvDel').onclick=function(){ if(!qvEvent) return; historyPush(qvEvent,'deleted',{title:qvEvent.title}); qvEvent.remove(); closeQuickView(); };
    document.getElementById('qvHist').onclick=function(){
      editingEvent=qvEvent;
      var list=document.getElementById('histList'); list.innerHTML='';
      var hist=(qvEvent.extendedProps&&qvEvent.extendedProps.history)||[];
      if(!hist.length){ list.innerHTML='<div>No history.</div>'; }
      for(var i=hist.length-1;i>=0;i--){
        var h=hist[i], when=new Date(h.ts), d=document.createElement('div'); d.style.margin='10px 0';
        var block='<div><strong>'+h.action+'</strong> <span style="color:#6b7280">– '+when.toLocaleString()+'</span></div>';
        if(h.changes && Object.keys(h.changes).length){
          block+='<pre style="white-space:pre-wrap;background:#f6f9fe;border:1px solid var(--line);padding:8px;border-radius:8px;margin-top:6px">'+escapeHTML(JSON.stringify(h.changes,null,2))+'</pre>';
        }
        d.innerHTML=block; list.appendChild(d);
      }
      histModal.open();
    };
    document.getElementById('qvMore').onclick=function(){ openItemModal({mode:'edit',event:qvEvent}); };

    var away=function(e){ if(!qv.contains(e.target) && !anchor.contains(e.target)) closeQuickView(); };
    var esc=function(e){ if(e.key==='Escape') closeQuickView(); };
    window.addEventListener('click',away); window.addEventListener('keydown',esc);
    window.addEventListener('scroll',closeQuickView,{once:true}); window.addEventListener('resize',closeQuickView,{once:true});
    qv._cleanup=function(){ window.removeEventListener('click',away); window.removeEventListener('keydown',esc); };
  }
  function closeQuickView(){ qv.classList.remove('cal-open'); qv.setAttribute('aria-hidden','true'); if(qv._cleanup){ qv._cleanup(); } }

  /* ---------------- New Calendar palette + Settings ---------------- */
  var newCalModal=bindModal('newCalModal'), palNode=document.getElementById('nc_palette'), palSel=palette[0];
  function buildSwatchGrid(container, colors, selected, onSelect){
    container.setAttribute('role','radiogroup');
    container.innerHTML = '';
    var buttons = [];
    function setSelected(hex){
      for(var i=0;i<buttons.length;i++){
        var b = buttons[i], isSel = b.dataset.hex === hex;
        b.classList.toggle('cal-sel', isSel);
        b.setAttribute('aria-checked', isSel ? 'true' : 'false');
      }
      onSelect(hex);
    }
    colors.forEach(function(hex){
      var b=document.createElement('button');
      b.type='button'; b.className='cal-swatch'; b.style.setProperty('--c', hex);
      b.dataset.hex = hex; b.setAttribute('role','radio'); b.setAttribute('aria-checked','false');
      var tick=document.createElement('span'); tick.className='cal-tick'; tick.innerHTML='&#10003;'; b.appendChild(tick);
      b.addEventListener('click', function(){ setSelected(this.dataset.hex); });
      container.appendChild(b); buttons.push(b);
    });
    setSelected(selected);
    return setSelected;
  }
  function renderPalette(){
    var line = document.getElementById('nc_sel_line');
    function updateLine(hex){
      line.style.setProperty('--c', hex);
      var name = paletteNames[hex] || hex;
      line.innerHTML = '<span class="cal-sel-dot"></span> Selected: <strong>'+ name +'</strong> <span style="opacity:.75">'+hex+'</span>';
      palSel = hex;
    }
    buildSwatchGrid(palNode, palette, palSel, updateLine);
    updateLine(palSel);
  }
  renderPalette();
  document.getElementById('openNewCal').onclick=function(){ document.getElementById('nc_name').value=''; palSel=palette[0]; renderPalette(); newCalModal.open(); };
  document.getElementById('ncClose').onclick=function(){ newCalModal.close(); };
  document.getElementById('ncCancel').onclick=function(){ newCalModal.close(); };
  document.getElementById('ncCreate').onclick=function(){
    var name=document.getElementById('nc_name').value.replace(/^\s+|\s+$/g,'');
    if(!name) return;
    var key=slug(name), i=1; while(calendars[key]){ key=slug(name)+'-'+(++i); }
    calendars[key]={label:name,color:palSel,group:'other',enabled:true};
    renderSidebar(); renderCalendarSelect(); applyFilters(); newCalModal.close();
  };

  var setModal=bindModal('settingsModal'), themePal=document.getElementById('theme_palette'), themeSel=palette[0];
  function renderTheme(){
    var line = document.getElementById('theme_sel_line');
    function updateLine(hex){
      line.style.setProperty('--c', hex);
      var name = paletteNames[hex] || hex;
      line.innerHTML = '<span class="cal-sel-dot"></span> Selected: <strong>'+ name +'</strong> <span style="opacity:.75)">'+hex+'</span>';
      themeSel = hex;
    }
    themePal.innerHTML='';
    palette.forEach(function(hex){
      var b=document.createElement('button');
      b.type='button'; b.className='cal-swatch'+(hex===themeSel?' cal-sel':''); b.style.setProperty('--c',hex);
      var tick=document.createElement('span'); tick.className='cal-tick'; tick.innerHTML='&#10003;'; b.appendChild(tick);
      b.addEventListener('click', function(){
        var all=themePal.querySelectorAll('.cal-swatch'); all.forEach(function(x){x.classList.remove('cal-sel');});
        b.classList.add('cal-sel'); updateLine(hex);
      });
      themePal.appendChild(b);
    });
    updateLine(themeSel);
  }
  renderTheme();
  document.getElementById('openSettings').onclick=function(){ setModal.open(); };
  document.getElementById('setClose').onclick=function(){ setModal.close(); };
  document.getElementById('setApply').onclick=function(){ document.documentElement.style.setProperty('--accent',themeSel); setModal.close(); };
  document.getElementById('setDensity').onchange=function(e){
    var compact=e.target.value==='compact';
    var els=document.querySelectorAll('.fc .fc-daygrid-event');
    for(var i=0;i<els.length;i++){ els[i].style.padding= compact?'2px 6px':'4px 8px'; }
  };

  // expose
  window.openItemModal=openItemModal;
})();


</script>         
          
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>


function selectedCalendars() {
  // ✅ Collect all selected calendar IDs automatically
  const selected = [];
  document.querySelectorAll('.calToggle:checked').forEach(cb => {
    selected.push(cb.getAttribute('data-cal'));
  });

  console.log("Selected calendar IDs:", selected);

  // ✅ Make AJAX request
  $.ajax({
    url: '/admin/office_tools/selectedCalendars/',
    type: 'POST',
    dataType: 'json',
    data: { calendars: selected },
    beforeSend: function() {
      console.log("Fetching events for calendars:", selected);
      // (optional) show loader here
    },
    success: function(res) {
      console.log("Response from server:", res);

      if (res.status === 'success' && Array.isArray(res.data) && window.calendar) {
        // ✅ Clear old events
        window.calendar.getEvents().forEach(ev => ev.remove());

        // ✅ Add new events
        window.calendar.addEventSource(res.data);
        window.calendar.refetchEvents();
        
         const calSelect = document.getElementById('f_calendars');
        calSelect.innerHTML = "";
        
         selected.forEach(calId => {
          const label = document.querySelector(`.calToggle[data-cal="${calId}"]`)
                          ?.parentElement?.textContent.trim() || ("Calendar " + calId);

          const opt = document.createElement("option");
          opt.value = calId;
          opt.textContent = label;
          calSelect.appendChild(opt);
        });

        if (calSelect.options.length > 0) {
          calSelect.selectedIndex = 0;
        }

      } else {
        console.warn("⚠️ No event data or calendar not found:", res);
      }
    },
    error: function() {
      console.error("❌ Error updating calendars");
    }
  });
}

function renderSidebar1(data) {
  const my = document.getElementById('myCals');
  const ot = document.getElementById('otherCals');
  my.innerHTML = '';
  ot.innerHTML = '';

  function add(k, c, root) {
    const el = document.createElement('label');
    el.className = 'cal-item';
    el.style.color = c.color;
    el.innerHTML =
      `<input type="checkbox" class="calToggle" data-cal="${k}" ${c.enabled ? 'checked' : ''}>
       <span class="cal-dot-lg ${c.enabled ? 'cal-filled' : ''}"></span>
       ${c.label}`;
    root.appendChild(el);
  }

  // Loop over response
  data.forEach(item => {
    const calObj = {
      label: item.aic_cal_name,
      color: "#2196f3",
      enabled: item.aic_cal_default === "1",
      group: "my"
    };
    add(item.aic_cal_mst_id, calObj, my);
  });

  // Attach listeners
  document.querySelectorAll('.calToggle').forEach(toggle => {
    toggle.addEventListener('change', e => {
      e.target.nextElementSibling.classList.toggle('cal-filled', e.target.checked);
      selectedCalendars(); // run on change
    });
  });

  selectedCalendars();
}


selectedCalendars();


function loadCalendars(){
  $.ajax({
    url: '/admin/office_tools/allCalendars/',  
    type: 'GET',                
    dataType: 'json',
    success: function(res){
      if(res.status === 'success'){
        renderSidebar1(res.data); // pass data into function
      } else {
        $('#myCals').html('<p>No calendars found</p>');
      }
    },
    error: function(){
      $('#calendarList').html('<p>Error loading calendars.</p>');
    }
  });
}



$(function(){
    loadCalendars();
});

$(function(){

  // when "Create" button is clicked
  $('#ncCreate').on('click', function(e){
    e.preventDefault();

    let calName  = $('#nc_name').val();
    let calColor = $('#nc_palette .selected').data('color');

    $.ajax({
      url: '/admin/office_tools/saveCalendar/',   
      type: 'POST',
      data: { 
        name: calName, 
        color: calColor 
      },
      success: function(response){
        alert("Calendar created ");
        $('#newCalModal').hide(); 
        location.reload();
      },
      error: function(){
        alert("Error while saving calendar");
      }
    });
  });

  // close buttons
  $('#ncClose, #ncCancel').on('click', function(){
    $('#newCalModal').hide();
  });

});

$(document).on("click", "#qvDel", function () {
  let eventId = $(this).data("id"); // get event ID from button
  if (!eventId) {
    alert("No event ID found!");
    return;
  }

  if (!confirm("Are you sure you want to delete this event?")) return;

  $.ajax({
    url: "/admin/office_tools/deleteEvent/",
    type: "POST", 
    data: { id: eventId },
    success: function (response) {
      console.log("Deleted:", response);
      alert("Event deleted successfully!");
      location.reload(); 
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
      alert("Something went wrong, please try again.");
    }
  });
});


// $(document).ready(function () {
//   $("#itemForm").on("submit", function (e) {
//     e.preventDefault(); // stop normal form submit

//     let data = {};

//     // Collect all inputs, selects, and textareas
//     $("#itemForm").find("input, select, textarea").each(function () {
//       let id = $(this).attr("id");
//       if (!id) return; // skip if no id

//       if ($(this).attr("type") === "checkbox") {
//         data[id] = $(this).is(":checked");
//       } else {
//         data[id] = $(this).val();
//       }
//     });

//     // Check if Event or Task is active
//     const isEvent = $('#typeSeg button[data-type="event"]').hasClass("cal-active");
//     data.type = isEvent ? "event" : "task";

//     // Send AJAX request
//     $.ajax({
//       url: "/admin/office_tools/saveEvent", 
//       type: "POST",
//       data: data,
//       success: function (response) {
//         console.log("Success:", response);
//         alert("Saved successfully!");
//         $("#itemModal").hide(); // close modal
//       },
//       error: function (xhr, status, error) {
//         console.error("Error:", error);
//         alert("Something went wrong, please try again.");
//       }
//     });
//   });
// });

$(document).ready(function () {
  $("#itemForm").on("submit", function (e) {
    e.preventDefault();

    let data = {};

    // Collect form values
    $("#itemForm").find("input, select, textarea").each(function () {
      let id = $(this).attr("id");
      if (!id) return;

      if ($(this).attr("type") === "checkbox") {
        data[id] = $(this).is(":checked");
      } else {
        data[id] = $(this).val();
      }
    });

    // Determine mode from modal title
    let titleText = $("#itemModalTitle").text().trim().toLowerCase();
    let mode = (titleText === "edit") ? "edit" : "create";
    data.mode = mode;

    // If edit, attach event ID
    // if (mode === "edit" && editingEvent) {
    //   data.id = editingEvent.id;
    // }

    // Choose URL
    let url = (mode === "edit") 
                ? "/admin/office_tools/updateEvent/" 
                : "/admin/office_tools/saveEvent";

    // AJAX call
    $.ajax({
      url: url,
      type: "POST",
      data: data,
      success: function (response) {
     
        alert("Saved successfully!");
        $("#itemModal").hide();
        
        // location.reload();
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
        alert("Something went wrong, please try again.");
      }
    });
  });
});


//   $(document).ready(function() {
//     $.ajax({
//         url: "<?= base_url('admin/Office_tools/check_calendar_token') ?>", // adjust path
//         method: "GET",
//         success: function(response) {
//             if (response.status === 'exists') {
//                 console.log("Google Calendar is already connected:", response.email);

             
//                 $('#google-calendar-btn').hide();

//                 autoLoadGoogleCalendar(response.access_token);
//             } else {
//                 console.log("Calendar not yet connected. Button remains visible.");
//             }
//         },
//         error: function(err) {
//             console.error("Error checking token:", err);
//         }
//     });
// });

function autoLoadGoogleCalendar(accessToken) {
    // Add your logic here to load calendar, display events, etc.
    console.log("Google Calendar ready with access token:", accessToken);
}

// $(document).on('click', '.editEventBtn', function () {
//     const eventId = $(this).data('id');
//     const title = $(this).data('title');
//     const startDate = $(this).data('date');

//     const description = $(this).data('description');
//     const location = $(this).data('location');

//     // console.log(title);
//     // console.log(startDate);
//     // console.log(endDate);
//     // console.log(description);
//     // console.log(location);
    

//     // Pre-fill modal fields
//     $('#edit_event_id').val(eventId);
//     $('#edit_eventname').val(title);
//     $('#edit_event_start').val(startDate.split('T')[0]); // assuming ISO date
//     $('#edit_event_end').val(startDate.split('T')[0]); // if exists
//     $('#edit_description').val(description);
//     $('#edit_location').val(location);

//     // Show modal
//     $('#editEventModal').modal('show');
// });

// $('#editEventForm').on('click', function (e) {
//     e.preventDefault();

//     const formData = {
//         event_id: $('#edit_event_id').val(),
//         title: $('#edit_eventname').val(),
//         start_date: $('#edit_event_start').val(),
//         end_date: $('#edit_event_end').val(),
//         description: $('#edit_description').val(),
//         location: $('#edit_location').val()
//     };

//     console.log(formData);
    

//     $.ajax({
//         url: '<?= base_url('admin/office_tools/updateGoogleEvent') ?>',
//         type: 'POST',
//         data: formData,
//         success: function (res) {
//             if (res.success) {
//                 alert('Event updated!');
//                 location.reload();
//             } else {
//                 alert('Error: ' + res.error);
//             }
//         }
//     });
// });


// $(document).ready(function () {
//     $('.eventAdd').on('click', function () {
//         var formData = {
//             title: $('#eventname').val(),
//             start_date: $('#event_start').val(),
//             end_date: $('#event_end').val(),
//             location: $('#eventplace').val(),
//             description: $('textarea[name="description"]').val(),
//             color: $('input[name="category_color"]:checked').val()
//         };

//         $.ajax({
//             url: '<?= base_url('admin/office_tools/addGoogleEvent') ?>',
//             type: 'POST',
//             contentType: 'application/json',
//             data: JSON.stringify(formData),
//             success: function (res) {
//                 alert('Event created on Google Calendar!');
//                 $('#addEvent').modal('hide');
//                 // Optional: reload calendar view
//                 location.reload();
//             },
//             error: function (err) {
//                 console.error(err);
//                 alert('Failed to create event.');
//             }
//         });
//     });
// });


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
