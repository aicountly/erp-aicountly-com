<?php
namespace App\Controllers;
use App\Models\CommonModel;
use App\Models\CompanyAccessModel;
use App\Models\RegisterModel;
use App\Models\Admin\ContactModel;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Libraries\auth_session;
use App\Helper\custom_helper;

class Company_access extends BaseController
{
	public function __construct()
    {  
	  helper(['form', 'url']);
	 $this->session 	         = \Config\Services::session();	 
	 $this->auth_session  = new auth_session();			
	 $this->auth_session->user_restrict();
	 $this->auth_session->role_restrict('CS');
	 $this->CommonModel          = new CommonModel();
	 $this->CompanyAccessModel   = new CompanyAccessModel();
	 $this->RegisterModel   	 = new RegisterModel();
     $this->ContactModel         = new ContactModel();
	 $this->folder_path          = getenv('AdminPath');
	 $this->base_url             = base_url();
	 $this->externaldb           = new externaldb();	 
	 $this->enc_string           = new enc_string();
	}
	
	public function index()
	{
        
	   $data['message_output']   = $this->message_output;
	   $data['folder_path']      = $this->folder_path;
	   $data['base_url']         = $this->base_url;
	   $data['company_list']     = $this->CompanyAccessModel->ajax_all_companies_list();
	   $data['company_profiles']     = [];
	   $data['business_id']      = $this->CommonModel->get_business_id();
	   $data['profile_link']     = getenv('profile_website').obfuscate_link($this->session->get('uuid'));
	   
	   return view('company_access',$data);
	}
	public function contacts()
	{
	  if($this->request->getMethod() == 'POST'){
      $search = $this->request->getVar('search');
      $comp_id = $this->request->getVar('comp_id');
      $comp_type = $this->request->getVar('comp_type');

           $data = [];
           if (filter_var($search, FILTER_VALIDATE_EMAIL)) {
  			    $user = $this->CompanyAccessModel->get_by_email($search);
				if(!empty($user))
				{
					$status = $this->CompanyAccessModel->check_company_shared($user['uuid'],$comp_id);
					if($status=='shared')
							return json_encode(['status' => false, 'message' => 'The user already has access to this company.']);   
					
					$user['company_status'] = $status;
					$user['type']           = 'email';
					$data[]                 = $user;
				}
			}
			else if (preg_match('/^[0-9]{10,}+$/', $search)) {
		  	   $users = $this->CompanyAccessModel->get_by_phone($search);
				if(!empty($users))
				{
					if(count($users) == 1)
						$user_lastname = ' User';
					else
						$user_lastname = ' Users';

					$data[] = [
								'company_status' => "mobile",
								'user_firstname' => count($users),
								'user_lastname'  => $user_lastname,
								'user_regdemail' => '',
								'user_regdmobile' => $search,
								'uuid' 			  	 => "0",
							];
				}
			}
			/* else {
				$users = $this->CompanyAccessModel->get_by_category($search);
				if($users)
				{
					foreach ($users as $key => $user) {
						 $status = $this->CompanyAccessModel->check_company_status($user['uuid'],$comp_id,$comp_type);
						 $user['company_status'] = $status;
						 $data[] = $user;
					   }
				 }
			} */
		
			if(empty($data) && filter_var($search, FILTER_VALIDATE_EMAIL))
			{
				$data[] = [
					'company_status' => "unregistered",
					'user_firstname' => "",
					'user_lastname'  => "",
					'user_regdemail' => $search,
					'user_regdmobile' => "",
					'uuid' 			 		 => "-1",
				];
			}
			else if (empty($data) && preg_match('/^[0-9]{10,}+$/', $search)) {
				$data[] = [
					'company_status' => "unregistered",
					'user_firstname' => "",
					'user_lastname'  => "",
					'user_regdemail' => "",
					'user_regdmobile' => $search,
					'uuid' 			 		 => "-2",
				];
			}
            
      if(count($data))
      	return json_encode(['status' => true, 'data' => $data]);
      else                
      	return json_encode(['status' => false, 'message' => 'No registered contact found']);      
    }
	}
	
	function unique_multi_array($array, $keys) {
    $temp = [];
    $unique = [];

    foreach ($array as $item) {
        // Create a combined key string from the keys to check uniqueness
        $keyStr = '';
        foreach ($keys as $key) {
            $keyStr .= $item[$key] . '|';
        }

        if (!isset($temp[$keyStr])) {
            $temp[$keyStr] = true;
            $unique[] = $item;
        }
    }

    return $unique;
}

public function get_company_profiles(){
    if ($this->request->getMethod() == 'POST') { 
       $company_id =     $this->request->getVar('comp_id');
       $profiles   =     $this->CompanyAccessModel->company_profiles($company_id);
       
       $final= array();
       if($profiles){
           foreach($profiles as $row){
               $final[] = array("profile_id"=>$row['erp_acs_prof_id'],"profile_name"=>$row['erp_acs_prof_name']);
           }
       }
       
       echo json_encode($final);
    }
    
}

	public function add_access()
	{
if ($this->request->getMethod() == 'POST') { 
 
    $user_id     = $this->session->get('uuid');
    $business_id = $this->CommonModel->get_business_id();

    if ($business_id) {
        $company_id_array = $this->request->getVar('company_id_array');
        $contacts_array   = $this->request->getVar('contacts_array');
        $erp_acs_prof_id   = $this->request->getVar('prflid');

        $uniqueData = $this->unique_multi_array($company_id_array, ['comp_id', 'comp_type']);

        // Initialize counters
        $success_count = 0;
        $already_shared_count = 0;
        $self_share_count = 0;
        $owner_error_count = 0;

        foreach ($uniqueData as $company) { 
            $company_info = $this->CompanyAccessModel->getCompanyDetails($company['comp_id']);

            if ($company_info) {
				
                if ($company_info['uuid']==$user_id) { // owner
                    $uuid_reference = 0;
                    $uuid = $user_id;
                } else { // shared
                    $uuid_reference = $user_id;
                    $uuid = $company_info['uuid'];
                }

                $comp_type = 0;

                foreach ($contacts_array as $contact) {
                    $uuid_access = $contact['uuid'];

                    // Case: register contact
                    if ($uuid_access < 0) {
                        $uuid_access = $this->CompanyAccessModel->createAccount($contact['user_regdemail'], $contact['user_regdmobile']);

                        $data = [
                            'cmp_id'            => $company['comp_id'],
                            'uuid_acs_type'     => $comp_type,
                            'uuid_aictly_acs'   => $uuid_access,
                            'uuid_aictly_by'    => $uuid,
                            'uuid_acs_datetime' => date('Y-m-d H:i:s'),
                            'erp_acs_prof_id'   => $erp_acs_prof_id
                        ];

                        $result = $this->CompanyAccessModel->add_company_access($data);
						SaveErrorLog('uuid_access < 0 => '.$result);
						if ($result > 0) {
                                $success_count++;
                            } elseif ($result == 0) {
                                $already_shared_count++;
                            } elseif ($result == -1) {
                                $self_share_count++;
                            } elseif ($result == -2) {
                                $owner_error_count++;
                            }
                    }

                    // Case: multiple users with same mobile
                    elseif ($uuid_access == 0) {
                        $users_array = $this->CompanyAccessModel->get_by_phone($contact['user_regdmobile']);
                        foreach ($users_array as $user) {
                            $data = [
                                'cmp_id'            => $company['comp_id'],
                                'uuid_acs_type'     => $comp_type,
                                'uuid_aictly_acs'   => $user['uuid'],
                                'uuid_aictly_by'    => $uuid_reference,
                                'uuid_acs_datetime' => date('Y-m-d H:i:s'),
                                'erp_acs_prof_id'   => $erp_acs_prof_id
                            ];

                            $result = $this->CompanyAccessModel->add_company_access($data);
							SaveErrorLog('uuid_access == 0 => '.$result);
                            // Handle result inside loop
                            if ($result > 0) {
                                $success_count++;
                            } elseif ($result == 0) {
                                $already_shared_count++;
                            } elseif ($result == -1) {
                                $self_share_count++;
                            } elseif ($result == -2) {
                                $owner_error_count++;
                            }
                        }
                        continue; // skip rest of logic, already handled inside loop
                    }

                    // Case: valid UUID
                    else {
						
                        $data = [
                            'cmp_id'            => $company['comp_id'],
                            'uuid_acs_type'     => $comp_type,
                            'uuid_aictly_acs'   => $uuid_access,
                            'uuid_aictly_by'    => $uuid,
                            'uuid_acs_datetime' => date('Y-m-d H:i:s'),
                            'erp_acs_prof_id'   => $erp_acs_prof_id
                        ];

                        $result = $this->CompanyAccessModel->add_company_access($data);
						SaveErrorLog('Case: valid UUID => '.$result);
                    }

                    // Handle result (for non-multiple case)
                    if ($result > 0) {
                        $success_count++;
                    } elseif ($result == 0) {
                        $already_shared_count++;
                    } elseif ($result == -1) {
                        $self_share_count++;
                    } elseif ($result == -2) {
                        $owner_error_count++;
                    }
                }
            }
        }

/* echo 'success_count => '.$success_count;
echo '<br>';
echo 'already_shared_count => '.$already_shared_count;
echo '<br>';
echo 'self_share_count => '.$self_share_count;
echo '<br>';
echo 'owner_error_count => '.$owner_error_count;
echo '<br>';
die(); */
        // Final Response
        if ($success_count > 0) {
            echo json_encode([
                'status' => 200,
                'message' => "Company access granted to $success_count contact(s).",
                'details' => [
                    'shared' => $success_count,
                    'already_shared' => $already_shared_count,
                    'self_share_ignored' => $self_share_count,
                    'not_owner' => $owner_error_count
                ]
            ]);
        }
		else {
            echo json_encode([
                'status' => 400,
                'message' => 'No access granted. All entries were either already shared, self-shared, or unauthorized.',
                'details' => [
                    'already_shared' => $already_shared_count,
                    'self_share_ignored' => $self_share_count,
                    'not_owner' => $owner_error_count
                ]
            ]);
        }
    } else {
        echo json_encode([
            'status' => 400,
            'message' => 'Failed! Business ID not created'
        ]);
    }
}	}



public function change_user_profile(){
    if($this->request->getMethod() == 'POST'){
        $profile_id = $this->request->getVar('idaccess');
        $uu_id = $this->request->getVar('uid');
        
        $this->CompanyAccessModel->update_user_profile($uu_id,$profile_id);

	    return json_encode(['status' => true]);
        
    }
    else
    return json_encode(['status' => false]);
    
    
}
	public function get_company_access_users()
	{
	    if($this->request->getMethod() == 'POST'){
			
			//echo '<pre>';print_r($_POST);exit;
	    	$comp_id = $this->request->getVar('comp_id');
	    	$comp_type = $this->request->getVar('comp_type');
	    	if($comp_type=='owner') 
				$company_type=1;
			 else 
				$company_type=0; 
	    	$users = $this->CompanyAccessModel->get_company_access_users($comp_id,$company_type);

	    	return json_encode(['status' => true, 'data' => $users]);
	    }
	}

	public function remove_access()
	{
	    if($this->request->getMethod() == 'POST'){
	    	$idaccess = $this->request->getVar('idaccess');

	    	$this->CompanyAccessModel->remove_access($idaccess);

	    	return json_encode(['status' => true, 'message' => 'Aceess Removed']);
	    }
	}
	
	public function manage()
	{
	    if($this->session->has('ses_company_id') )
        {return redirect()->to(base_url().'/admin/dashboard');
        }
        
	   $data['message_output']   = $this->message_output;
	   $data['folder_path']      = $this->folder_path;
	   $data['base_url_path']    = $this->base_url;
	   $data['company_list']     = $this->CommonModel->ajax_company_list();
	 	
	   
	   return view('manage_company_access',$data);
	}

	
    	  	
}