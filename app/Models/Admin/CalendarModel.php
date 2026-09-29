<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;

class CalendarModel extends Model	{
	 public function __construct() {
       parent::__construct();        
       $this->session        = \Config\Services::session();	 
	   $this->common        = \Config\Database::connect();	
	   $this->externaldb    = new externaldb();	 
	   $this->db            = $this->externaldb->get_company_db();
	   $this->aicountly_db  = $this->externaldb->aicountly_db();
	   $this->univerpaic_db  = $this->externaldb->univerpaic_db();
	   $this->univaictly    =  $this->externaldb->univaictly_db();
	   $this->company_id    =  $this->session->get('ses_company_id');
    }
 
   public function total_events($currentDate){
		/* $result = $this->db->query("SELECT COUNT(*) as `total_events` FROM `events` WHERE `date`='".$currentDate."'   ")->getRowArray();
		if(isset($result))
		  $total_events =$result['total_events'];
         else
          $total_events =0;	
          
       return $total_events; */	
return "0";	   
 	}
 	
 	public function all_events($uuid)
{
    $rows = $this->univaictly->table('aiccalendr')
        ->select('aiccalendr.*, aiccaldetn.*')
        ->join('aiccaldetn', 'aiccaldetn.aic_calendar_id = aiccalendr.aic_calendar_id')
        ->where('aiccalendr.uuid', $uuid)
        ->get()
        ->getResultArray();

    $events = [];

    foreach ($rows as $row) {
        $events[] = [  
            "id"    => $row['aic_cal_event_id'],
            "title" => $row['aic_cal_event_name'],
            "start" => '2025-10-01T10:00:00',
            "end"   => '2025-10-01T11:00:00',
            
            "backgroundColor" => "blue",  
            "borderColor"     => "blue",
            "textColor"       => "#0b1728",

            // extended props for front-end logic
            "extendedProps" => [
                "id"  => $row['aic_cal_event_id'],
                "calendar"  => "tasks", 
                "type"      => "task",
                "status"    => "todo",
                "priority"  => "Normal",
                "attendees" => ["Guest"],
                "history"   => ["title" => $row['aic_cal_event_name']],
                "desc"      => $row['aic_cal_event_desc'],
            ]
        ];
    }

    return $events;
}


	 public function sticky_notes_list($id) {
		$itemrepmcn_table = $this->company_id . '_compnotesn_' . $this->session->get('ses_comp_fy_id');
		$builder = $this->db->table($itemrepmcn_table);
		$existingNote = $builder->where('note_master_id', $id)->get()->getRowArray();
	
		// If it doesn't exist, insert a new record
		if (!$existingNote) {
			$insertData = [
				'note_master_id' => $id,
				'note_master_type' => 1, // or default value
				'note_heading' => '',
				'note_details' => '',
				// Add other fields as per your table schema
			];
	
			$builder->insert($insertData);
	
			// Optionally, fetch the inserted data
			$existingNote = $builder->where('note_master_id', $id)->get()->getRowArray();
		}
	
		return $existingNote;
	}
	
public function sticky_notes_list_my($id, $user_id) {
    // Reference to the table
    $builder = $this->aicountly_db->table('aicountly_aicnotesnn_univdb');

    // Check if the note already exists
    $existingNote = $builder
        ->where('uuid_aictly', $user_id)
        ->where('note_master_id', $id)
        ->get()
        ->getRowArray();

    // If not exists, insert a blank note
    if (!$existingNote) {
        $insertData = [
            'note_master_id' => $id,
            'uuid_aictly' => $user_id,
            'aic_notes_heading' => '',
            'aic_notes_details' => '',
            'aic_notes_order' => $id
        ];

        $builder->insert($insertData);

        // Re-fetch the newly inserted row
        $existingNote = $builder
            ->where('uuid_aictly', $user_id)
            ->where('note_master_id', $id)
            ->get()
            ->getRowArray();
    }

    return $existingNote;
}

	
public function sticky_notes_list_company($id, $company_id) {
    // Reference to the table
    $builder = $this->univerpaic_db->table('erpnotesnn');

    // Check if the note already exists
    $existingNote = $builder
        ->where('cmp_id', $company_id)
        ->where('note_master_id', $id)
        ->get()
        ->getRowArray();

    // If not exists, insert a blank note
    if (!$existingNote) {
        $insertData = [
            'note_master_id' => $id,
            'cmp_id' => $company_id,
            'erp_notes_heading' => '',
            'erp_notes_details' => '',
            'erp_notes_order' => $id
        ];

        $builder->insert($insertData);

        // Re-fetch the newly inserted row
        $existingNote = $builder
            ->where('cmp_id', $company_id)
            ->where('note_master_id', $id)
            ->get()
            ->getRowArray();
    }

    return $existingNote;
}


	public function update_note_company($id, $insertData, $company_id) {
	    
	   
	
	 $builder = $this->univerpaic_db->table('erpnotesnn')->where('cmp_id', $company_id)->where('note_master_id', $id)->update($insertData);
	 
	  

		return true;

	}
	
		public function update_note_my($id, $insertData, $user_id) {

	     $builder = $this->aicountly_db->table('aicountly_aicnotesnn_univdb')->where('uuid_aictly', $user_id)->where('note_master_id', $id)->update($insertData);
		return true;
	}
	
	public function fetchNotes_list_my($user_id) {
	
		return $this->aicountly_db->table('aicountly_aicnotesnn_univdb')
                ->where('uuid_aictly', $user_id)
                ->get()
                ->getResultArray();

	}
 
    
    public function fetchNotes_list_company($company_id) {
	
// 		return $this->univerpaic_db->table('erpnotesnn')
//                 ->where('cmp_id', $company_id)
//                 ->get()
//                 ->getResultArray();
                
                return  $this->univerpaic_db->table('erpnotesnn')
                ->where('cmp_id', $company_id)
                ->get()
                ->getResultArray();

	}
	
public function update_company_order($company_id, $noteId, $order)
{
    return $this->univerpaic_db->table('erpnotesnn')
        ->where('note_master_id', $noteId)
            ->where('cmp_id', $company_id)
        ->update(['erp_notes_order' => $order, 'erp_notes_order' => $order]);
}

public function insertCalender1($data)
{
   
    $row = $this->univaictly->table('aiccalendr')
                            ->where('uuid', $data['uuid'])
                            ->get()
                            ->getRow();

    if ($row) {
        return $row->aic_calendar_id; 
    }

   
    $this->univaictly->table('aiccalendr')->insert($data);
    return $this->univaictly->insertID();
}


public function insertCalender2($data)
{
    $row = $this->univaictly->table('aiccalmstn')
                            ->where('aic_calendar_id', $data['aic_calendar_id'])
                            ->get()
                            ->getRow();

    if ($row) {
        return $row->aic_cal_mst_id; 
    }

    // else insert new
    $this->univaictly->table('aiccalmstn')->insert($data);
    return $this->univaictly->insertID();
}


public function insertCalender3($data)
{
    
   $builder = $this->univaictly->table('aiccalevnt');
    $result  = $builder->insert($data);
    
    return $this->univaictly->insertID();
    
}

public function insertCalender4($data)
{
    
   $builder = $this->univaictly->table('aiccaldetn');
    $result  = $builder->insert($data);
    
    return $this->univaictly->insertID();
    
}

public function edit_event1($data, $id)
{
    
   $builder = $this->univaictly->table('aiccalevnt')->where('aic_cal_event_id',$id);
    $result  = $builder->update($data);
 
   return true;
    
}

public function edit_event2($data, $id)
{
    
   $builder = $this->univaictly->table('aiccaldetn')->where('aic_cal_event_id',$id);
    $result  = $builder->update($data);
 
   return true;
    
}

public function create_calendar($name,$cal_id)
{
   return $this->univaictly->table('aiccalmstn')->insert([ 'aic_calendar_id'=>$cal_id,'aic_cal_name'=>$name]);
    
}

public function delete_event( $id)
{
     $this->univaictly->table('aiccalevnt')->where('aic_cal_event_id',$id)->delete();
    $this->univaictly->table('aiccaldetn')->where('aic_cal_event_id',$id)->delete();
    
    return true;
    
}

public function calendars($id)
{
     return $this->univaictly
        ->table('aiccalendr e') 
        ->select('e.*, m.*')   
        ->join('aiccalmstn m', 'm.aic_calendar_id = e.aic_calendar_id')
        ->where('e.uuid', $id)
        ->get()
        ->getResultArray();
    
}

public function all_calendars($calendars)
{
    $rows = $this->univaictly->table('aiccalevnt')
        ->select('aiccalevnt.*, aiccaldetn.*')
        ->join('aiccaldetn', 'aiccaldetn.aic_cal_event_id = aiccalevnt.aic_cal_event_id')
        ->whereIn('aiccalevnt.aic_cal_mst_id', $calendars)
        ->get()
        ->getResultArray();
        
         $events = [];

    foreach ($rows as $row) {
        $events[] = [  
            "id"    => $row['aic_cal_event_id'],
            "title" => $row['aic_cal_event_name'],
            "start" => $row['aic_cal_event_start_date_time'],
            "end"   => $row['aic_cal_event_end_date_time'],
            
            "backgroundColor" => "blue",  
            "borderColor"     => "blue",
            "textColor"       => "#0b1728",

           
            "extendedProps" => [
                "id"  => $row['aic_cal_event_id'],
                "calendar"  => "tasks", 
                "type"      => "task",
                "status"    => "todo",
                "priority"  => "Normal",
                "attendees" => ["Guest"],
                "history"   => ["title" => $row['aic_cal_event_name']],
                "desc"      => $row['aic_cal_event_desc'],
                "cal_id"      => $row['aic_cal_mst_id'],
                "type"      => $row['aic_cal_event_type'],
            ]
        ];
    }

    return $events;
    
}

}