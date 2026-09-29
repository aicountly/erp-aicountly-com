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

    <a href="#" class="btn btn-success dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
    <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">CSV</a></li>
    <li><a class="dropdown-item" href="#">Excel</a></li>
    <li><a class="dropdown-item" href="#">Document</a></li>
  </ul>  
      
  <button class="btn btn-success m-1" type="button">Templates</button>
   <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-outline-success">Back</a>
  </div> 
</div>

  <style>

.calendar__days section{padding:0px 0px!important;}
  
  
   @media (min-width: 992px){
.calendar__days section {  padding-top:0px;  padding-bottom:0px;} 
}

.calendar-contain {
  -webkit-font-smoothing: antialiased;
  text-rendering: optimizeLegibility;
  font-family: -apple-system, BlinkMacSystemFont, system-ui, "Segoe UI", Roboto, Oxygen, Ubuntu, "Helvetica Neue", sans-serif;
  position: relative;
  left: 0;
  right: 0;
  border-radius: 8px;
  width: 100%;
  overflow: hidden;
  max-width: 1020px;
  
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
  width: 70%; float:right;
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
@media screen and (min-width: 55em) {
  .title-bar__month
  }
}

.calendar__sidebar {
  width: 100%;
  margin: 0 auto;
  float: none;
  background: #CEE9B4;
  padding-bottom: 0.7rem;
}
@media screen and (min-width: 55em) {
  .calendar__sidebar {
    position: absolute;
    height: 100%; background:#CEE9B4;
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
  min-height: 580px;
  height: 100%;
  font-size: 12px;
  padding:1rem;
  background: #fff;
}
@media screen and (min-width: 55em) {
  .calendar__days {
    width: 70%;
    float: right;
  }
}

.calendar__top-bar {
  display: -webkit-box;
  display: flex;
  background:#f3f3f3;
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
.no-event .calendar__task,.inactive .calendar__task{color:#aaa;}
.calendar__day {
  display: -webkit-box;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
          flex-flow: column wrap;
  -webkit-box-pack: justify;
          justify-content: space-between;
  width: 100%;
  padding: 1.9rem 0.2rem 0.2rem;
  cursor: pointer; border:1px solid #f3f3f3;
}

.calendar__day.event{position:relative;}
.calendar__day.event::before{position:absolute; content:''; z-index:1; background:#CEE9B4; border-radius:50%; left:0%; top:0%; right:0; bottom:0%;}
.calendar__day.event .calendar__date, .calendar__day.event .calendar__task{
  color: #000; z-index:9;
}

.calendar__date {
  color: #040605;
  font-size: 1.7rem; 
  font-weight: 600;
  line-height: 0.7;
}

.calendar__week .inactive .calendar__date,
.calendar__week .inactive .task-count {
  color: #c6c6c6;
}
.calendar__week .today .calendar__date {
  color:#82C63E;
}

.calendar__task {
  color: #040605;
  display: -webkit-box;
  display: flex;
  font-size: 0.8rem;
}
@media screen and (min-width: 55em) {
    .calendar__date {
    font-size: 2rem;
  }
  .calendar__task {
    font-size: 1rem;
  }
}
@media screen and (max-width: 55em) {
.title-bar{width:100%;}
}
.calendar__task.calendar__task--today {
  color:#83C442;
}

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
         
        <aside class="calendar__sidebar" id="event_list"><h2 class="sidebar__heading">Monday<br>January 01</h2>
        <ul class="sidebar__list">
            <li class="sidebar__list-item"><span class="list-item__time">4:00 PM</span> My list name goes here</li>
            <li class="sidebar__list-item"><span class="list-item__time">2:00 PM</span> My list name goes here</li>
            <li class="sidebar__list-item"><span class="list-item__time">1:00 AM</span> Fully responsive javascript calendar with events for mobile and desktop</li>
            </ul>
        </aside> 
         
        <section class="calendar__days"> 
            <section class="calendar__top-bar"> 
                <span class="top-bar__days">Mon</span> 
                <span class="top-bar__days">Tue</span> 
                <span class="top-bar__days">Wed</span> 
                <span class="top-bar__days">Thu</span> 
                <span class="top-bar__days">Fri</span> 
                <span class="top-bar__days">Sat</span> 
                <span class="top-bar__days">Sun</span> 
            </section> <section class="calendar__week"> 
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
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-4');"> 
                                    <span class="calendar__date">4</span> 
                                    <span class="calendar__task">no Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-5');"> 
                                    <span class="calendar__date">5</span> 
                                    <span class="calendar__task">1 Events</span> 
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
                                <div class="calendar__day event" onclick="getEvents('2024-01-8');"> 
                                    <span class="calendar__date">8</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-9');"> 
                                    <span class="calendar__date">9</span> 
                                    <span class="calendar__task">2 Events</span> 
                                </div> 
                             
                                <div class="calendar__day event" onclick="getEvents('2024-01-10');"> 
                                    <span class="calendar__date">10</span> 
                                    <span class="calendar__task">7 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-11');"> 
                                    <span class="calendar__date">11</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day event" onclick="getEvents('2024-01-12');"> 
                                    <span class="calendar__date">12</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day event" onclick="getEvents('2024-01-13');"> 
                                    <span class="calendar__date">13</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-14');"> 
                                    <span class="calendar__date">14</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                            </section><section class="calendar__week"> 
                                <div class="calendar__day event" onclick="getEvents('2024-01-15');"> 
                                    <span class="calendar__date">15</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-16');"> 
                                    <span class="calendar__date">16</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-17');"> 
                                    <span class="calendar__date">17</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-18');"> 
                                    <span class="calendar__date">18</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-19');"> 
                                    <span class="calendar__date">19</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day event" onclick="getEvents('2024-01-20');"> 
                                    <span class="calendar__date">20</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day event" onclick="getEvents('2024-01-21');"> 
                                    <span class="calendar__date">21</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                            </section><section class="calendar__week"> 
                                <div class="calendar__day event" onclick="getEvents('2024-01-22');"> 
                                    <span class="calendar__date">22</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-23');"> 
                                    <span class="calendar__date">23</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-24');"> 
                                    <span class="calendar__date">24</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day event" onclick="getEvents('2024-01-25');"> 
                                    <span class="calendar__date">25</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day event" onclick="getEvents('2024-01-26');"> 
                                    <span class="calendar__date">26</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-27');"> 
                                    <span class="calendar__date">27</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                             
                                <div class="calendar__day no-event" onclick="getEvents('2024-01-28');"> 
                                    <span class="calendar__date">28</span> 
                                    <span class="calendar__task">5 Events</span> 
                                </div> 
                            </section><section class="calendar__week"> 
                                <div class="calendar__day event" onclick="getEvents('2024-01-29');"> 
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
          


<script>
	function getCalendar(target_div, year, month){ 
            $.ajax({ 
                type:'POST', 
                url:'<?php echo base_url();?>/admin/office_tools/showcalendar', 
                data:'func=getCalender&year='+year+'&month='+month, 
                success:function(html){ 
                    $('#'+target_div).html(html); 
                } 
            }); 
        } 
         
        function getEvents(date){ 
            $.ajax({ 
                type:'POST', 
                url:'<?php echo base_url();?>/admin/office_tools/showcalendar',  
                data:'func=getEvents&date='+date, 
                success:function(html){ 
                    $('#event_list').html(html); 
                } 
            }); 
        } 
         
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



<script>


 </script>
</body>
</html>
