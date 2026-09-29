<?php
namespace App\Controllers;
use App\Models\RegisterModel;
use App\Models\LogModel;
use App\Models\CommonModel;
use App\Models\GoogleModel;
use App\Helpers\custom_helper;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
class Register extends BaseController
{
	public function __construct()
    {  
	 helper(['form', 'url','email_helper','mail_helper']);
	 $this->RegisterModel    = new RegisterModel();
	 $this->LogModel      = new LogModel();
	 $this->CommonModel   = new CommonModel();
	 $this->GoogleModel   = new GoogleModel();
	 $this->session       = \Config\Services::session();
	 $this->base_url      = base_url().'/'.getenv('AdminPath');
	 $this->folder_path   = getenv('AdminPath');
	 $this->enc_string    = new enc_string();
	}

    public function index()
    {
        $view_data['country_array'] = $this->RegisterModel->get_country_array();
        $view_data['state_array'] = $this->RegisterModel->get_state_array();
        $view_data['qualification_array'] = qualification_array();
		   
	     $view_data['message_output']  = $this->message_output;
	  	 $view_data['base_url']        = base_url();	
         $view_data['folder_path']     = $this->folder_path;
         $view_data['session']         = $this->session;
         
        if($this->request->getMethod() == 'post'){
		    
		    $rules = [				
				'user_firstname' => [
					'label'  => 'First Name',
					'rules'  => 'required',
					'errors' => [
						'required' => 'First Name is required',
					    ],
				      ],
				'user_lastname' => [
					'label'  => 'Last Name',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Last Name is required',
					  ],
				   ],
				   'user_regdmobile' => [
					'label'  => 'Regd Mobile',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Regd Mobile is required'
					  ],
				   ],
				   'user_regdemail' => [
					'label'  => 'Regd Email',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Regd Email is required',
						'is_unique' => 'Email already exists',
					  ],
				   ],
				   'user_wamobile' => [
					'label'  => 'Whatsapp No',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Whatsapp No is required',
					  ],
				   ],
				   'password' => [
					'label'  => 'Password',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Password is required',
					  ],
				   ],
				   'confirm_password' => [
					'label'  => 'Confirm Password',
					'rules'  => 'required|matches[password]',
					'errors' => [
						'required' => 'Confirm Password No is required',
						'matches'  =>  'Confirm Password does not match Password'
					  ],
				   ]
			    ];
			
            if(!$this->validate($rules)){
              $this->message_output->set_error( $this->validator->listErrors());
              return view('register', $view_data);
            }
            /* if(!$this->RegisterModel->is_email_unique($_POST['user_regdemail'])){ 
                $this->message_output->set_error('Email already exits on aicountly');
                return view('register', $view_data);
            } */

            $_POST['user_name'] = $_POST['user_regdemail'];

            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $_POST['user_pass'] = $password;

            unset($_POST['password']);
            unset($_POST['confirm_password']);

            
            $uuid = $this->RegisterModel->add_user($_POST);
			
			

            if($uuid){
                $enc_id = obfuscate_link($uuid);
                // $response = sendAccountConfirmationEmail($enc_id, $_POST['user_regdemail']);
                // $this->session->setFlashdata('message', $response);

                $response1 = $this->send_message($_POST['user_firstname'], $_POST['user_regdmobile']);
                $response2 = $this->send_email($_POST['user_firstname'], $_POST['user_regdemail']);

                // echo "<pre>";
                // print_r($response1);
                // print_r($response2);
                // exit;
                $this->message_output->set_error('Registration is successful. Please proceed to login');
            }
            else{
                $this->message_output->set_error('Error in registration process');
            }
            
            return redirect()->to(base_url().'/register');
           
        }
        	
         
        return view('register', $view_data);
	
	}
	
	

	public function send_message($name, $mobile)
    {
    	// $mobile = '8054115977';
        $table = $this->RegisterModel->get_sms_trigger();

        if($table)
        {
            if($table['status'] == 'a')
            {
                $tp_key         = trim($table['sms_tp_key']);
                $sender_id      = trim($table['sms_senderid']);
                $content        = trim($table['sms_content']);
                $api            = trim($table['sms_api']);
               
               	$name = trim($name);
		        
		        if(strlen($name) > 15){
		            $name = substr($name,0,15); // 15 characters
		        }
		        
		        $content = str_replace("[[UUIDFNAME]]",$name,$content); // max 15 characters
		        $content = rawurlencode($content); //encodeing is important
		        
		        $api = str_replace("[[TPKEY]]",$tp_key,$api);
		        $api = str_replace("[[SENDERID]]",$sender_id,$api);
		        $api = str_replace("[[MOBILE]]",$mobile,$api);
		        $api = str_replace("[[CONTENT]]",$content,$api);
		        $api = trim($api);

		      	
            	$ch = curl_init($api);
            	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            	$response = curl_exec($ch);
            	curl_close($ch);
            	
            	$response = json_decode($response);
        
                if(isset($response->status) && $response->status == 'success'){
                    return ['status' => 200, 'message' => 'Message Sent'];
                }
                else{
                    return ['status' => 400, 'message' => 'Failed!', 'hidden_message' => 'API error'];
                }
		        
            }
            else{
                return ['status' => 400, 'message' => 'Failed!', 'hidden_message' => 'Trigger not approved'];
            }
        }
        else{
            return ['status' => 400, 'message' => 'Failed!', 'hidden_message' => 'Trigger not found'];
        }
    }

    public function send_email($name, $email)
    {
        // $email = 'talwinder7749@gmail.com';
        $trigger = $this->RegisterModel->get_email_trigger();
        if($trigger)
        {
            if($trigger['status'] == 'a')
            {
            	$subject = $trigger['email_subject'];
		        
		        $css  = '<style>'.$trigger['template_css'].'</style><br>';
		        $body = $trigger['email_content'];
		        $body = str_replace("[[UUIDFNAME]]",$name,$body); // max 15 characters
		        $html = $css . $body;

		        

                $mailConfig = [
                        'mail_from_email'   => env('EMAIL_FROM_ADDRESS'),
                        'mail_from_name'    => env('EMAIL_FROM_NAME'),
                        'mail_to_email'     => $email,
                        'mail_to_name'      => $name,
                        'mail_subject'      => $subject,
                        'mail_body'         => $html,
                    ];
                    
                if(sendEmail($mailConfig)){
                    return ['status' => 200, 'message' => 'OTP Sent'];
                }
                else{
                    return ['status' => 400, 'message' => 'Failed!', 'hidden_message' => 'Mail not sent'];
                }
         	}	
            else{
                return ['status' => 400, 'message' => 'Failed!', 'hidden_message' => 'Trigger not approved'];
            }
        }
        else{
            return ['status' => 400, 'message' => 'Failed!', 'hidden_message' => 'Trigger not found'];
        }
    }
	
	public function confirm($id)
	{
	    $uuid = unobfuscate_link($id,'de')[1];
	    $response = $this->RegisterModel->confirm_user($uuid);
	    
	    if($response)
	    {
	        $pass = getRandomString();
	        $enc_pass = password_hash($pass, PASSWORD_BCRYPT);
	        
            $uuid_aicountly = $this->RegisterModel->add_password($uuid,$enc_pass);
            
            $email = $this->RegisterModel->get_user_email($uuid_aicountly);
            
            sendPasswordEmail($pass, $email);
            
	        $this->session->setFlashdata('message', 'User Account confirmed. Please check email for password or use otp');
	    }
	    else{
	        $this->session->setFlashdata('message', 'User Account already confirmed. Please proceed to login');
	    }
	    
	    
	    return redirect()->to(base_url().'/login');
	}
    
    
}
