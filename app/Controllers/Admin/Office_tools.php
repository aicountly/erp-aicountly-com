<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Admin\CalendarModel;
use App\Libraries\enc_string;
use App\Models\GoogleModel;

use Google\Service\Calendar as GoogleCalendar;
use Google_Service_Calendar_Event;
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
                        if ( !empty($_POST['eventDates'])) {
                            echo $this->getCalender($_POST['year'], $_POST['month'], $_POST['eventDates']);
                        } else {
                            echo $this->getCalender($_POST['year'], $_POST['month']);
                        }
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
    } else{
        $eventListHTML .= '<ul class="sidebar__list">'; 
        $eventListHTML .= '<li class="sidebar__list-item sidebar__list-item--complete">Events</li>'; 
        $i=0; 
        // foreach($result as $row){ $i++; 
            $eventListHTML .= '<li class="sidebar__list-item"><span class="list-item__time">'.$i.'.</span>'.'0'.'</li>'; 
        // } 
        $eventListHTML .= '</ul>'; 
    }
    return  $eventListHTML; 
}
    
//     public function  getCalender($year = '', $month = '', $event = []){ 
//     $dateYear = ($year != '')?$year:date("Y"); 
//     $dateMonth = ($month != '')?$month:date("m"); 
//     $date = $dateYear.'-'.$dateMonth.'-01'; 
//     $currentMonthFirstDay = date("N",strtotime($date)); 
//     $totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN,$dateMonth,$dateYear); 
//     $totalDaysOfMonthDisplay = ($currentMonthFirstDay == 1)?($totalDaysOfMonth):($totalDaysOfMonth + ($currentMonthFirstDay - 1)); 
//     $boxDisplay = ($totalDaysOfMonthDisplay <= 35)?35:42; 
     
//     $prevMonth = date("m", strtotime('-1 month', strtotime($date))); 
//     $prevYear = date("Y", strtotime('-1 month', strtotime($date))); 
//     $totalDaysOfMonth_Prev = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);   
    
//      $prvy =date("Y",strtotime($date." - 1 Month"));
//      $prm = date("m",strtotime($date." - 1 Month"));
     
//      $nxvy =date("Y",strtotime($date." + 1 Month"));
//      $nxm = date("m",strtotime($date.' + 1 Month'));
//      $div_name ="calendar_div";
     
//    $html = ' <main class="calendar-contain"> 
//         <section class="title-bar"> 
//             <a href="javascript:void(0);" class="title-bar__prev" onclick="getCalendar(\''.$div_name.'\',\''.$prvy.'\',\''.$prm.'\');"></a> 
//             <div class="title-bar__month"> 
//                 <select class="month-dropdown"> 
//                     '.$this->getMonthList($dateMonth).' 
//                 </select> 
//             </div> 
//             <div class="title-bar__year"> 
//                 <select class="year-dropdown"> 
//                     '.$this->getYearList($dateYear).' 
//                 </select> 
//             </div> 
//             <a href="javascript:void(0);" class="title-bar__next" onclick="getCalendar(\''.$div_name.'\',\''.$nxvy.'\',\''.$nxm.'\');"></a> 
//         </section> 
         
//         <aside class="calendar__sidebar" id="event_list"> 
//            '. $this->getEvents().' 
//         </aside> 
         
//         <section class="calendar__days"> 
//             <section class="calendar__top-bar"> 
//                 <span class="top-bar__days">Mon</span> 
//                 <span class="top-bar__days">Tue</span> 
//                 <span class="top-bar__days">Wed</span> 
//                 <span class="top-bar__days">Thu</span> 
//                 <span class="top-bar__days">Fri</span> 
//                 <span class="top-bar__days">Sat</span> 
//                 <span class="top-bar__days">Sun</span> 
//             </section> ';
    
    
//     $dayCount = 1; 
//                 $eventNum = 0; 
                 
//                 $html .= '<section class="calendar__week">'; 
//                 for($cb=1;$cb<=$boxDisplay;$cb++){ 
//                     if(($cb >= $currentMonthFirstDay || $currentMonthFirstDay == 1) && $cb <= ($totalDaysOfMonthDisplay)){ 
//                         // Current date 
//                         $currentDate = $dateYear.'-'.$dateMonth.'-'.$dayCount; 
                         
//                         // Get number of events based on the current date
                        
//                         $eventNum = $this->CalendarModel->total_events($currentDate);
                         
//                         // Define date cell color 
//                         if(strtotime($currentDate) == strtotime(date("Y-m-d"))){ 
//                              $html .=  ' 
//                                 <div class="calendar__day today" onclick="getEvents(\''.$currentDate.'\');"> 
//                                     <span class="calendar__date">'.$dayCount.'</span> 
//                                     <span class="calendar__task calendar__task--today">'.$eventNum.' Events</span> 
//                                 </div> 
//                             '; 
//                         }elseif($eventNum > 0){ 
//                              $html .=  ' 
//                                 <div class="calendar__day event" onclick="getEvents(\''.$currentDate.'\');"> 
//                                     <span class="calendar__date">'.$dayCount.'</span> 
//                                     <span class="calendar__task">'.$eventNum.' Events</span> 
//                                 </div> 
//                             '; 
//                         }else{ 
//                              $html .=  ' 
//                                 <div class="calendar__day no-event" onclick="getEvents(\''.$currentDate.'\');"> 
//                                     <span class="calendar__date">'.$dayCount.'</span> 
//                                     <span class="calendar__task">'.$eventNum.' Events</span> 
//                                 </div> 
//                             '; 
//                         } 
//                         $dayCount++; 
//                     }else{ 
//                         if($cb < $currentMonthFirstDay){ 
//                             $inactiveCalendarDay = ((($totalDaysOfMonth_Prev-$currentMonthFirstDay)+1)+$cb); 
//                             $inactiveLabel = 'expired'; 
//                         }else{ 
//                             $inactiveCalendarDay = ($cb-$totalDaysOfMonthDisplay); 
//                             $inactiveLabel = 'upcoming'; 
//                         } 
//                          $html .=  ' 
//                             <div class="calendar__day inactive"> 
//                                 <span class="calendar__date">'.$inactiveCalendarDay.'</span> 
//                                 <span class="calendar__task">'.$inactiveLabel.'</span> 
//                             </div> 
//                         '; 
//                     } 
//                      $html .=  ($cb%7 == 0 && $cb != $boxDisplay)?'</section><section class="calendar__week">':''; 
//                 } 
//                  $html .=  '</section>'; 
    
//      $html .=  '</section> 
//     </main> ';
    
//     return $html;
//    } 


public function getCalender($year = '', $month = '', $events = [])
{
    $dateYear = ($year != '') ? $year : date("Y");
    $dateMonth = ($month != '') ? $month : date("m");
    $date = $dateYear . '-' . $dateMonth . '-01';

    $currentMonthFirstDay = date("N", strtotime($date));
    $totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN, $dateMonth, $dateYear);
    $totalDaysOfMonthDisplay = ($currentMonthFirstDay == 1) ? $totalDaysOfMonth : ($totalDaysOfMonth + ($currentMonthFirstDay - 1));
    $boxDisplay = ($totalDaysOfMonthDisplay <= 35) ? 35 : 42;

    $prevMonth = date("m", strtotime('-1 month', strtotime($date)));
    $prevYear = date("Y", strtotime('-1 month', strtotime($date)));
    $totalDaysOfMonth_Prev = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);

    $prvy = date("Y", strtotime($date . " - 1 Month"));
    $prm = date("m", strtotime($date . " - 1 Month"));
    $nxvy = date("Y", strtotime($date . " + 1 Month"));
    $nxm = date("m", strtotime($date . ' + 1 Month'));

    $div_name = "calendar_div";

    $html = '<main class="calendar-contain"> 
        <section class="title-bar"> 
            <a href="javascript:void(0);" class="title-bar__prev" onclick="getCalendar(\'' . $div_name . '\',\'' . $prvy . '\',\'' . $prm . '\');"></a> 
            <div class="title-bar__month"> 
                <select class="month-dropdown">' . $this->getMonthList($dateMonth) . '</select> 
            </div> 
            <div class="title-bar__year"> 
                <select class="year-dropdown">' . $this->getYearList($dateYear) . '</select> 
            </div> 
            <a href="javascript:void(0);" class="title-bar__next" onclick="getCalendar(\'' . $div_name . '\',\'' . $nxvy . '\',\'' . $nxm . '\');"></a> 
        </section> 

        <aside class="calendar__sidebar" id="event_list">' . $this->getEvents() . '</aside>

        <section class="calendar__days"> 
            <section class="calendar__top-bar"> 
                <span class="top-bar__days">Mon</span> 
                <span class="top-bar__days">Tue</span> 
                <span class="top-bar__days">Wed</span> 
                <span class="top-bar__days">Thu</span> 
                <span class="top-bar__days">Fri</span> 
                <span class="top-bar__days">Sat</span> 
                <span class="top-bar__days">Sun</span> 
            </section>';

    $dayCount = 1;
    $html .= '<section class="calendar__week">';
    for ($cb = 1; $cb <= $boxDisplay; $cb++) {
        if (($cb >= $currentMonthFirstDay || $currentMonthFirstDay == 1) && $cb <= ($totalDaysOfMonthDisplay)) {
            $currentDate = date("Y-m-d", strtotime($dateYear . '-' . $dateMonth . '-' . $dayCount));
            $eventInfo = null;

            // ✅ Match current date with event start dates
            if (!empty($events)) {
                foreach ($events as $ev) {
                    if (!empty($ev['start'])) {
                        $eventDate = date("Y-m-d", strtotime($ev['start']));
                        if ($eventDate === $currentDate) {
                            $eventInfo = $ev;
                            break;
                        }
                    }
                }
            }

      
                        


            if ($eventInfo) {

                $colorPalette = ['#A7510A', '#1D73C0', '#3C995B', '#D7A90B', '#B4436C', '#663399', '#FF8C00'];
                $eventColorMap = [];
                $usedColors = [];
                $eventKey = $eventInfo['title'] . $eventInfo['start']; // unique identifier
    
                if (!isset($eventColorMap[$eventKey])) {
                    // Get remaining unused colors
                    $availableColors = array_diff($colorPalette, $usedColors);
                
                    // Reset if all colors used
                    if (empty($availableColors)) {
                        $usedColors = [];
                        $availableColors = $colorPalette;
                    }
                
                    // Pick a random unused color
                    $pickedColor = $availableColors[array_rand($availableColors)];
                    $eventColorMap[$eventKey] = $pickedColor;
                    $usedColors[] = $pickedColor;
                }
                
                $bgColor = $eventColorMap[$eventKey];

                $bgColor = $colorPalette[array_rand($colorPalette)];

                $html .= '<div class="calendar__day event" onclick="getEvents(\'' . $currentDate . '\');">
                <span class="calendar__date">' . $dayCount . '</span>
                <span class="calendar__task" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background:' . $bgColor . ';">' . htmlspecialchars($eventInfo['title']) . '</span>
            
                <div class="event_modal dropdown-menu" style="background:' . $bgColor . ';">
                    <div class="border-bottom mb-2" style=" display: flex;justify-content: space-between;">
                            <h4 class=" pb-2">' . $currentDate . '</h4>
                            <span class="material-symbols-outlined editEventBtn"  data-id="' . $eventInfo['id'] . '"  data-title="' . htmlspecialchars($eventInfo['title']) . '"
                        data-date="' . $currentDate . '"
                        data-description="' . htmlspecialchars($eventInfo['description'] ?? '') . '"
                        data-location="' . htmlspecialchars($eventInfo['location'] ?? '') . '">
                            edit
                            </span>
                            
                    </div>
                    
                    <h6>' . htmlspecialchars($eventInfo['title']) . '</h6>
                    <p>' . (!empty($eventInfo['description']) ? htmlspecialchars($eventInfo['description']) : 'No description') . '</p>
                    <ul>
                        <li><span class="material-symbols-outlined">badge</span> Google Calendar</li>
                        <li><span class="material-symbols-outlined">location_on</span> ' . (!empty($eventInfo['location']) ? htmlspecialchars($eventInfo['location']) : 'N/A') . '</li>
                        <li><span class="material-symbols-outlined">lock</span> Private</li>
                    </ul>
            
          
                </div>
            </div>';
            
            
                
            } else {
                $html .= '<div class="calendar__day no-event" onclick="getEvents(\'' . $currentDate . '\');">
                            <span class="calendar__date">' . $dayCount . '</span>
                            <span class="calendar__task">no Events</span>
                          </div>';
            }

            $dayCount++;
        } else {
            if ($cb < $currentMonthFirstDay) {
                $inactiveCalendarDay = ((($totalDaysOfMonth_Prev - $currentMonthFirstDay) + 1) + $cb);
                $inactiveLabel = 'expired';
            } else {
                $inactiveCalendarDay = ($cb - $totalDaysOfMonthDisplay);
                $inactiveLabel = 'upcoming';
            }

            $html .= '<div class="calendar__day inactive">
                        <span class="calendar__date">' . $inactiveCalendarDay . '</span>
                        <span class="calendar__task">' . $inactiveLabel . '</span>
                      </div>';
        }

        $html .= ($cb % 7 == 0 && $cb != $boxDisplay) ? '</section><section class="calendar__week">' : '';
    }

    $html .= '</section></section></main>';

    return $html;
}



public function index()
{
    require_once APPPATH . "Libraries/vendor/autoload.php";

    $google_client = new \Google_Client();
    $google_client->setClientId('639335728279-qp892trt43e7uaikj9m5qp1u4a3kf45p.apps.googleusercontent.com');
    $google_client->setClientSecret('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');
    $google_client->setRedirectUri('https://sandbox.aicountly.in/admin/office_tools');
    $google_client->addScope('https://www.googleapis.com/auth/calendar.events');
    $google_client->addScope('https://www.googleapis.com/auth/calendar');
    $google_client->addScope('email');
    $google_client->addScope('profile');
    $google_client->setApprovalPrompt('force');
    $google_client->setAccessType('offline');
    $google_client->setIncludeGrantedScopes(true);

    $data['jsonEvents'] = json_encode([]);
    $data['googleEmail'] = '';

    // Handle redirect after auth
    if ($this->request->getGet('code')) {
        $token = $google_client->fetchAccessTokenWithAuthCode($this->request->getGet('code'));

        if (!isset($token['error'])) {
            $_SESSION['access_token'] = $token;
        }

        return redirect()->to(base_url('admin/office_tools'));
    }

    // Reuse session token if available
    if (isset($_SESSION['access_token'])) {
        $google_client->setAccessToken($_SESSION['access_token']);

        if ($google_client->isAccessTokenExpired() && $google_client->getRefreshToken()) {
            $google_client->fetchAccessTokenWithRefreshToken($google_client->getRefreshToken());
            $_SESSION['access_token'] = $google_client->getAccessToken();
        }

        try {
            // Get user info
            $google_oauth = new \Google_Service_Oauth2($google_client);
            $user_info = $google_oauth->userinfo->get();

            // Get calendar events
            $service = new \Google_Service_Calendar($google_client);
            $events = $service->events->listEvents('primary');

            $allEvents = [];

            while (true) {
                $items = $events->getItems();
                if (!empty($items)) {
                    foreach ($items as $event) {
                        $allEvents[] = $event;
                    }
                }

                $pageToken = $events->getNextPageToken();
                if ($pageToken) {
                    $events = $service->events->listEvents('primary', ['pageToken' => $pageToken]);
                } else {
                    break;
                }
            }

            // echo '<pre>';
            // print_r($allEvents);
            // die();

            $formattedEvents = array_map(function ($value) use ($user_info) {
                return [
                    'id' => $value->getId(), // 🆔 Event ID
                    'title' => $value->getSummary(),
                    'start' => $value->start->dateTime ?? $value->start->date ?? '',
                    'description' => $value->getDescription(),
                    'email' => $user_info->getEmail(),
                    'location' => $value->getLocation() ?? '', // optional
                ];
            }, $allEvents);
            

            $data['jsonEvents'] = json_encode($formattedEvents);
            $data['googleEmail'] = $user_info->getEmail();
        } catch (\Exception $e) {
            // Handle expired/invalid token silently
            unset($_SESSION['access_token']);
        }
    }

    $data['GetCalendar'] = $this->getCalender();
    return view($this->folder_path . 'office_tools/index', $data);
}


        public function googleCalendar()
        {
            require_once APPPATH . "Libraries/vendor/autoload.php";
        
            $google_client = new \Google_Client();
            $google_client->setClientId('639335728279-qp892trt43e7uaikj9m5qp1u4a3kf45p.apps.googleusercontent.com');
            $google_client->setClientSecret('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');
            $google_client->setRedirectUri('https://sandbox.aicountly.in/admin/office_tools');
            $google_client->addScope('https://www.googleapis.com/auth/calendar.events');
            $google_client->addScope('https://www.googleapis.com/auth/calendar');
            $google_client->setApprovalPrompt('force'); 
            $auth_url = $google_client->createAuthUrl();
            $google_client->addScope('https://www.googleapis.com/auth/calendar.events');
            $google_client->addScope('https://www.googleapis.com/auth/calendar');
            $google_client->addScope('email');                                               
            $google_client->addScope('profile');
            $google_client->setIncludeGrantedScopes(true);

            return redirect()->to($google_client->createAuthUrl());
           
        }
        
		public function addGoogleEvent()
        {
            // Get raw JSON data from jQuery
            $request = $this->request->getJSON();

            $title       = $request->title ?? 'Untitled Event';
            $start_date  = $request->start_date;
            $end_date    = $request->end_date;
            $location    = $request->location ?? '';
            $description = $request->description ?? '';
            $color       = $request->color ?? '#1D73C0';
        
            // Build start and end datetime
            $startDateTime = $start_date . 'T10:00:00+05:30';
            $endDateTime   = $end_date . 'T11:00:00+05:30';

            require_once APPPATH . "Libraries/vendor/autoload.php";
            $google_client = new \Google_Client();
            $google_client->setClientId('639335728279-qp892trt43e7uaikj9m5qp1u4a3kf45p.apps.googleusercontent.com');
            $google_client->setClientSecret('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');
            $google_client->setRedirectUri('https://sandbox.aicountly.in/admin/office_tools');
            $google_client->addScope('https://www.googleapis.com/auth/calendar.events');
            $google_client->addScope('https://www.googleapis.com/auth/calendar');
            $google_client->setApprovalPrompt('force'); 
            $auth_url = $google_client->createAuthUrl();
            $google_client->addScope('https://www.googleapis.com/auth/calendar.events');
            $google_client->addScope('https://www.googleapis.com/auth/calendar');
            $google_client->addScope('email');                                               
            $google_client->addScope('profile');
            $google_client->setIncludeGrantedScopes(true);


            // print_r($_SESSION['access_token']);
            // die();

            if (isset($_SESSION['access_token'])) {
                $google_client->setAccessToken($_SESSION['access_token']);
        
                // Refresh if expired
                if ($google_client->isAccessTokenExpired() && $google_client->getRefreshToken()) {
                    $google_client->fetchAccessTokenWithRefreshToken($google_client->getRefreshToken());
                    $_SESSION['access_token'] = $google_client->getAccessToken();
                }
        
                $calendarService = new \Google_Service_Calendar($google_client);
        
                // Build the event
                $event = new \Google_Service_Calendar_Event([
                    'summary'     => $title,
                    'location'    => $location,
                    'description' => $description,
                    'start'       => [
                        'dateTime' => $startDateTime,
                        'timeZone' => 'Asia/Kolkata',
                    ],
                    'end'         => [
                        'dateTime' => $endDateTime,
                        'timeZone' => 'Asia/Kolkata',
                    ],
                ]);
        
                // Insert the event
                $calendarId = 'primary';
                try {
                    $createdEvent = $calendarService->events->insert($calendarId, $event);
                    return $this->response->setJSON(['status' => 'success', 'eventLink' => $createdEvent->htmlLink]);
                } catch (Exception $e) {
                    return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
                }
        
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Not authenticated with Google']);
            }

            // echo "<pre>";
            // print_r($data);
            // echo "</pre>";
            // exit; // Stop execution so you only see the output
        }

        public function delinkGoogle()
        {
            // Unset only the Google token session
            unset($_SESSION['access_token']);

            // Optionally unset other related data
            unset($_SESSION['google_user']);

            // Redirect back to main page
            return redirect()->to(base_url('admin/office_tools'));
        }


        public function updateGoogleEvent()
{
   
    

    require_once APPPATH . "Libraries/vendor/autoload.php";
    $google_client = new \Google_Client();
    $google_client->setClientId('639335728279-qp892trt43e7uaikj9m5qp1u4a3kf45p.apps.googleusercontent.com');
    $google_client->setClientSecret('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');
    $google_client->setRedirectUri('https://sandbox.aicountly.in/admin/office_tools');
    $google_client->addScope('https://www.googleapis.com/auth/calendar.events');
    $google_client->addScope('https://www.googleapis.com/auth/calendar');
    $google_client->setApprovalPrompt('force'); 
    $auth_url = $google_client->createAuthUrl();
    $google_client->addScope('https://www.googleapis.com/auth/calendar.events');
    $google_client->addScope('https://www.googleapis.com/auth/calendar');
    $google_client->addScope('email');                                               
    $google_client->addScope('profile');
    $google_client->setIncludeGrantedScopes(true);

    // Restore access token from session
    if (!isset($_SESSION['access_token'])) {
        return $this->response->setJSON(['error' => 'Not authenticated']);
    }

    $google_client->setAccessToken($_SESSION['access_token']);

    if ($google_client->isAccessTokenExpired()) {
        if ($google_client->getRefreshToken()) {
            $google_client->fetchAccessTokenWithRefreshToken($google_client->getRefreshToken());
            $_SESSION['access_token'] = $google_client->getAccessToken();
        } else {
            return $this->response->setJSON(['error' => 'Access token expired and refresh token missing.']);
        }
    }

    $calendar = new \Google_Service_Calendar($google_client);

    $eventId = $this->request->getPost('event_id');

    try {
        // Get the existing event
        $event = $calendar->events->get('primary', $eventId);

        // Update fields
        $event->setSummary($this->request->getPost('title'));
        $event->setDescription($this->request->getPost('description'));
        $event->setLocation($this->request->getPost('location'));

        $start = new \Google_Service_Calendar_EventDateTime();
        $start->setDate($this->request->getPost('start_date'));
        $event->setStart($start);

        $end = new \Google_Service_Calendar_EventDateTime();
        $end->setDate($this->request->getPost('end_date'));
        $event->setEnd($end);

        // Update event on Google Calendar
        $updatedEvent = $calendar->events->update('primary', $eventId, $event);

        return $this->response->setJSON(['success' => true, 'event' => $updatedEvent]);
    } catch (Exception $e) {
        return $this->response->setJSON(['error' => $e->getMessage()]);
    }
}

public function stickyNote()
{
    
        
   

         $id = $this->request->getPost('id');
         $note_type = $this->request->getPost('note_type');
         $user_id = auth()->id;
         
         $company_id = $this->company_id;
         
         if($note_type == 'company'){
              echo json_encode($this->CalendarModel->sticky_notes_list_company($id,$company_id ));
         }else{
             
             echo json_encode($this->CalendarModel->sticky_notes_list_my($id, $user_id));
         }


}

public function saveNote()
{
    
  
    $id = $this->request->getPost('id');
    $heading = $this->request->getPost('heading');
    $details = $this->request->getPost('details');
    $note_type = $this->request->getPost('note_type');
    $user_id = auth()->id;
    $company_id = $this->company_id;
    
    if($note_type == 'company'){
        
        
          $insertData = [
        'erp_notes_heading' => $heading,
        'erp_notes_details' => $details,
    ];
    
    // print_r($insertData);
    // die();
        
        $this->CalendarModel->update_note_company($id, $insertData, $company_id);
        echo json_encode($this->CalendarModel->sticky_notes_list_company($id,$company_id ));
    }else{
        
        $insertData = [
        'aic_notes_heading' => $heading,
        'aic_notes_details' => $details,
    ];
    
        
        $this->CalendarModel->update_note_my($id, $insertData, $user_id);
        echo json_encode($this->CalendarModel->sticky_notes_list_my($id, $user_id));
    }

    
    // echo json_encode($this->CalendarModel->sticky_notes_list($id));
}

public function fetchNotes()
{

    $user_id = auth()->id;
    $note_type = $this->request->getPost('note_type');
    $company_id = $this->company_id;
    
    // echo $this->company_id ;
    // die();
    
    if($note_type == 'my'){
        
        echo json_encode($this->CalendarModel->fetchNotes_list_my($user_id));
    }else{
        
        echo json_encode($this->CalendarModel->fetchNotes_list_company($company_id));
        
    }

    
}

public function updateNoteOrder()
{
    
    $orderedIds = $this->request->getPost('ordered_ids');  
    $noteType = $this->request->getPost('note_type'); 
    $user_id = auth()->id;
    $company_id = $this->company_id;
    
    if($noteType == 'company'){
        
      foreach ($orderedIds as $index => $noteId) {
            $this->CalendarModel->update_company_order($company_id, $noteId, $index + 1); // +1 for 1-based order
        }
        
    }else{
         foreach ($orderedIds as $index => $noteId) {
            $this->CalendarModel->update_my_order($user_id, $noteId, $index + 1);
        }
    }
    
      return $this->response->setJSON([
        'status' => 'success',
        'message' => 'Note order updated successfully'
    ]);

}

public function deleteEvent()
{
    $id = $this->request->getPost('id');
     $this->CalendarModel->delete_event($id);
  
    
}

public function saveCalendar()
{
    
    $user_id = auth()->id;
     $cal_id = $this->CalendarModel->insertCalender1([
        'uuid' => $user_id
    ]);
    
    
    $name = $this->request->getPost('name');
       $this->CalendarModel->create_calendar($name,$cal_id);
    
}


public function allCalendars()
{
    $user_id = auth()->id;
    $calendars = $this->CalendarModel->calendars($user_id);
     return $this->response->setJSON([
        'status' => 'success',
        'data'   => $calendars
    ]);
    
}

public function updateEvent()
{
    
    // echo 333;
    // die();
 
 $id = $this->request->getPost('edit_id');
 
 $start = $this->request->getPost('f_start'); // e.g. 2025-10-01T10:00
$end   = $this->request->getPost('f_end');

// Convert to MySQL DATETIME format
$start = date('Y-m-d H:i:s', strtotime($start));
$end   = date('Y-m-d H:i:s', strtotime($end));

$mst_id = $this->request->getPost('f_calendars');
    
    $event_type = $this->request->getPost('f_calendar');
    
     $insert3 = array(
         'aic_cal_event_type' => $event_type,
        'aic_cal_mst_id' => $mst_id,
        'aic_cal_event_type' => 1,
        'aic_cal_event_start_date' => $start,
        'aic_cal_event_end_date' => $end,
        'aic_cal_event_freq' => 1,
        );
        
        
        $event_id =  $this->CalendarModel->edit_event1($insert3, $id);
        
        
        $insert4 = array(
        'aic_cal_mst_id' => $mst_id,
        'aic_cal_event_name' => $this->request->getPost('f_title'),
        'aic_cal_event_start_date_time' => $start,
        'aic_cal_event_end_date_time' => $end,
        'aic_cal_event_desc' => $this->request->getPost('f_desc'),
        );
        $this->CalendarModel->edit_event2($insert4, $id);
    
}

public function saveEvent()
{
    
    // echo 333;
    // die();
   
    $user_id = auth()->id;
    
     $title   = $this->request->getPost('f_title');
    $start   = $this->request->getPost('f_start');
    $end     = $this->request->getPost('f_end');
    $desc    = $this->request->getPost('f_desc');
    
    $mst_id = $this->request->getPost('f_calendars');
    
    $type = $this->request->getPost('f_calendar');
    
    $start = date('Y-m-d H:i:s', strtotime($start));
$end   = date('Y-m-d H:i:s', strtotime($end));

    
//   $cal_id = $this->CalendarModel->insertCalender1([
//         'uuid' => $user_id
//     ]);

//     $mst_id = $this->CalendarModel->insertCalender2([
//         'aic_calendar_id' => $cal_id,
//         'aic_cal_name'    => $title,
//         'aic_cal_default' => 1,
//     ]);
    
    $insert3 = array(
        'aic_cal_event_type' => $type,
        'aic_cal_mst_id' => $mst_id,
        'aic_cal_event_start_date' => $start,
        'aic_cal_event_end_date' => $end,
        'aic_cal_event_freq' => 1,
        );
        
        $event_id =  $this->CalendarModel->insertCalender3($insert3);
        
        
        $insert4 = array(
        'aic_cal_event_id' => $event_id,
        'aic_cal_mst_id' => $mst_id,
        'aic_cal_event_name' => $this->request->getPost('f_title'),
        'aic_cal_event_start_date_time' => $start,
        'aic_cal_event_end_date_time' => $end,
        'aic_cal_event_desc' => $this->request->getPost('f_desc'),
        );
        $this->CalendarModel->insertCalender4($insert4);
        
}

public function selectedCalendars()
{
   $calendars   = $this->request->getPost('calendars');
   
    $events =  $this->CalendarModel->all_calendars($calendars);
    
 return $this->response->setJSON([
        'status' => 'success',
        'data' => $events
    ]);
    
}

}