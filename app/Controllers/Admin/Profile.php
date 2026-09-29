<?php
namespace App\Controllers\Admin;
use App\Models\Admin\DashboardModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;

class Profile extends BaseController
{
    var	$folder_path;
	function __construct()
    {  
	    helper(['form', 'url']);
		$this->DashboardModel = new DashboardModel();		
		$this->auth_session   = new auth_session();
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('A');
	    $this->folder_path    = getenv('AdminPath');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
    }
    public function index()
    {	
		if($this->request->getMethod() == 'post'){
			$first_name      = $this->request->getVar('first_name');		   
		    $last_name       = $this->request->getVar('last_name');
			$email           = $this->request->getVar('email');
		    $buss_org_name   = $this->request->getVar('buss_org_name');
	        $primary_phone   = $this->request->getVar('primary_phone'); 
		    $address         = $this->request->getVar('address');			
			$city_name       = $this->request->getVar('city_name');			
		    $state_id        = $this->request->getVar('state_id');			
	        $postal_code     = $this->request->getVar('postal_code');
			$email_id        = $this->request->getVar('email_id'); 
			
			$rules = [				
				'first_name' => [
					'label'  => 'First Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter first name',
					   ]],				
				  
				'last_name' => [
					'label'  => 'Last Name',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter last name',
					], 
				   ],
				  'buss_org_name' => [
					'label'  => 'Company Name',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter company name',
					], 
				   ],
				  'primary_phone' => [
					'label'  => 'Phone No.',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter phone no.',
					], 
				   ], 
				   
				'email' => [
					'label'  => 'Email',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter email',
					], 
				   ], 
				'address' => [
					'label'  => 'Address',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter address',
					], 
				   ], 
				'city_name' => [
					'label'  => 'City',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter city',
					], 
				   ],	
				'state_id' => [
					'label'  => 'State',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please choose state',
					], 
				   ],	   
				'postal_code' => [
					'label'  => 'Postal code',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter postal code',
					], 
				   ],   				   
					   
			];

            if(!$this->validate($rules)){
               $this->message_output->set_error($this->validator->listErrors());
            }else{				
                $update_data = [
							'buss_org_name' => $buss_org_name,
							'first_name'    => $first_name,
							'last_name'     => $last_name,
							'email'         => $email,
							'primary_phone' => $primary_phone,
							'address'       => $address,
							'city_name'     => $city_name,
							'state_id'      => $state_id,
							'postal_code'   => $postal_code
							  ];		
					  
			       $updated = $this->DashboardModel->update_profile($update_data,$email_id);
				   if ($email_id != $email) {
					    $this->message_output->set_success("Login instructions have been sent to the new email address.");

						$this->session->set('lock', true);
						$this->session->remove('user_id');
						$this->session->remove('email');
						$this->session->remove('user_type');
						redirect($this->base_url . 'lock'); 
				   }				   
                   else{	                 
			           $this->message_output->set_success('Record Updated successfully');
			           return redirect()->to($this->base_url.'profile'); 
			           die;		
				      }					   
			     }			
		     }
		
		$data['folder_path']     = $this->folder_path;
		$data['message_output']  = $this->message_output;
		$data['states_array']    = $this->DashboardModel->StatesDropdown();	
		$data['profile_info']    = $this->DashboardModel->get_client_info($this->session->get('user_id'));	
	    return view($this->folder_path.'profile',$data);	
    }
	
	
}
