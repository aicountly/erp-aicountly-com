<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Admin\CalendarModel;
use App\Libraries\enc_string;

class Office_tools  extends BaseController
{
  function __construct()
    {   
	    helper(['form', 'url','text']);
				
			$this->auth_session  = new auth_session();			
			$this->auth_session->user_restrict();
			$this->CalendarModel = new CalendarModel();
			$this->auth_session->role_restrict('CS');
			$this->base_url      =  base_url().'/'.getenv('AdminPath');
			$this->folder_path   =  getenv('AdminPath');
			$this->session    	 = \Config\Services::session();
			$this->auth_session->is_company_opened();
			$this->comp_code     =  $this->session->get('ses_company_code');
			$this->company_id    =  $this->session->get('ses_company_id');
			
			$this->getReferrer   =  \Config\Services::request()->getUserAgent()->getReferrer();
    }
   
     public function showcalendar(){
         if($this->request->getMethod() == 'post'){ 
              if(isset($_POST['func']) && !empty($_POST['func'])){ 
                 switch($_POST['func']){ 
                        case 'getCalender': 
                           echo $this->getCalender($_POST['year'],$_POST['month']); 
                            break; 
                        case 'getEvents': 
                          echo  $this->getEvents($_POST['date']); 
                            break; 
                        default: 
                            break; 
                    } 
               }   
         }
        
    }
  public function  getMonthList($selected = ''){ 
    $options = ''; 
    for($i=1;$i<=12;$i++) 
    { 
        $value = ($i < 10)?'0'.$i:$i; 
        $selectedOpt = ($value == $selected)?'selected':''; 
        $options .= '<option value="'.$value.'" '.$selectedOpt.' >'.date("F", mktime(0, 0, 0, $i+1, 0, 0)).'</option>'; 
    } 
    return $options; 
}    

 public function getYearList($selected = ''){ 
    $yearInit = !empty($selected)?$selected:date("Y"); 
    $yearPrev = ($yearInit - 5); 
    $yearNext = ($yearInit + 5); 
    $options = ''; 
    for($i=$yearPrev;$i<=$yearNext;$i++){ 
        $selectedOpt = ($i == $selected)?'selected':''; 
        $options .= '<option value="'.$i.'" '.$selectedOpt.' >'.$i.'</option>'; 
    } 
    return $options; 
} 

public function getEvents($date = ''){ 
    $date = $date?$date:date("Y-m-d"); 
     
    $eventListHTML = '<h2 class="sidebar__heading">'.date("l", strtotime($date)).'<br>'.date("F d", strtotime($date)).'</h2>'; 
     
    // Fetch events based on the specific date 
  
     $result = $this->CalendarModel->all_events($date);
    
    if(count($result) > 0){ 
        $eventListHTML .= '<ul class="sidebar__list">'; 
        $eventListHTML .= '<li class="sidebar__list-item sidebar__list-item--complete">Events</li>'; 
        $i=0; 
        foreach($result as $row){ $i++; 
            $eventListHTML .= '<li class="sidebar__list-item"><span class="list-item__time">'.$i.'.</span>'.$row['title'].'</li>'; 
        } 
        $eventListHTML .= '</ul>'; 
    } 
    return  $eventListHTML; 
}
    
    public function  getCalender($year = '', $month = ''){ 
    $dateYear = ($year != '')?$year:date("Y"); 
    $dateMonth = ($month != '')?$month:date("m"); 
    $date = $dateYear.'-'.$dateMonth.'-01'; 
    $currentMonthFirstDay = date("N",strtotime($date)); 
    $totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN,$dateMonth,$dateYear); 
    $totalDaysOfMonthDisplay = ($currentMonthFirstDay == 1)?($totalDaysOfMonth):($totalDaysOfMonth + ($currentMonthFirstDay - 1)); 
    $boxDisplay = ($totalDaysOfMonthDisplay <= 35)?35:42; 
     
    $prevMonth = date("m", strtotime('-1 month', strtotime($date))); 
    $prevYear = date("Y", strtotime('-1 month', strtotime($date))); 
    $totalDaysOfMonth_Prev = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);   
    
     $prvy =date("Y",strtotime($date." - 1 Month"));
     $prm = date("m",strtotime($date." - 1 Month"));
     
     $nxvy =date("Y",strtotime($date." + 1 Month"));
     $nxm = date("m",strtotime($date.' + 1 Month'));
     $div_name ="calendar_div";
     
   $html = ' <main class="calendar-contain"> 
        <section class="title-bar"> 
            <a href="javascript:void(0);" class="title-bar__prev" onclick="getCalendar(\''.$div_name.'\',\''.$prvy.'\',\''.$prm.'\');"></a> 
            <div class="title-bar__month"> 
                <select class="month-dropdown"> 
                    '.$this->getMonthList($dateMonth).' 
                </select> 
            </div> 
            <div class="title-bar__year"> 
                <select class="year-dropdown"> 
                    '.$this->getYearList($dateYear).' 
                </select> 
            </div> 
            <a href="javascript:void(0);" class="title-bar__next" onclick="getCalendar(\''.$div_name.'\',\''.$nxvy.'\',\''.$nxm.'\');"></a> 
        </section> 
         
        <aside class="calendar__sidebar" id="event_list"> 
           '. $this->getEvents().' 
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
            </section> ';
    
    
    $dayCount = 1; 
                $eventNum = 0; 
                 
                $html .= '<section class="calendar__week">'; 
                for($cb=1;$cb<=$boxDisplay;$cb++){ 
                    if(($cb >= $currentMonthFirstDay || $currentMonthFirstDay == 1) && $cb <= ($totalDaysOfMonthDisplay)){ 
                        // Current date 
                        $currentDate = $dateYear.'-'.$dateMonth.'-'.$dayCount; 
                         
                        // Get number of events based on the current date
                        
                        $eventNum = $this->CalendarModel->total_events($currentDate);
                         
                        // Define date cell color 
                        if(strtotime($currentDate) == strtotime(date("Y-m-d"))){ 
                             $html .=  ' 
                                <div class="calendar__day today" onclick="getEvents(\''.$currentDate.'\');"> 
                                    <span class="calendar__date">'.$dayCount.'</span> 
                                    <span class="calendar__task calendar__task--today">'.$eventNum.' Events</span> 
                                </div> 
                            '; 
                        }elseif($eventNum > 0){ 
                             $html .=  ' 
                                <div class="calendar__day event" onclick="getEvents(\''.$currentDate.'\');"> 
                                    <span class="calendar__date">'.$dayCount.'</span> 
                                    <span class="calendar__task">'.$eventNum.' Events</span> 
                                </div> 
                            '; 
                        }else{ 
                             $html .=  ' 
                                <div class="calendar__day no-event" onclick="getEvents(\''.$currentDate.'\');"> 
                                    <span class="calendar__date">'.$dayCount.'</span> 
                                    <span class="calendar__task">'.$eventNum.' Events</span> 
                                </div> 
                            '; 
                        } 
                        $dayCount++; 
                    }else{ 
                        if($cb < $currentMonthFirstDay){ 
                            $inactiveCalendarDay = ((($totalDaysOfMonth_Prev-$currentMonthFirstDay)+1)+$cb); 
                            $inactiveLabel = 'expired'; 
                        }else{ 
                            $inactiveCalendarDay = ($cb-$totalDaysOfMonthDisplay); 
                            $inactiveLabel = 'upcoming'; 
                        } 
                         $html .=  ' 
                            <div class="calendar__day inactive"> 
                                <span class="calendar__date">'.$inactiveCalendarDay.'</span> 
                                <span class="calendar__task">'.$inactiveLabel.'</span> 
                            </div> 
                        '; 
                    } 
                     $html .=  ($cb%7 == 0 && $cb != $boxDisplay)?'</section><section class="calendar__week">':''; 
                } 
                 $html .=  '</section>'; 
    
     $html .=  '</section> 
    </main> ';
    
    return $html;
   } 
    public function index()
		{
			$data['GetCalendar']     = $this->getCalender();
				return view($this->folder_path.'office_tools/index',$data);
		}
		
}