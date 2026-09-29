<?php
namespace App\Controllers;
use App\Models\LoginModel;
use App\Models\RegisterModel;
use App\Models\CommonModel;
use App\Models\GoogleModel;
use App\Libraries\externaldb;
use App\Libraries\auth_session;

class Login extends BaseController
{
	public function __construct()
    {  
	 helper(['form', 'url', 'mail_helper']);
	 $this->session       = \Config\Services::session();
	 $this->LoginModel    = new LoginModel();
	 $this->RegisterModel = new RegisterModel();	 
	 $this->CommonModel   = new CommonModel();
	 $this->GoogleModel   = new GoogleModel();	 
	 $this->base_url      = base_url().getenv('AdminPath');
	 $this->folder_path   = getenv('AdminPath');
	}

    public function index()
    {	
        if ($this->session->get('logged_in')) {
		    return redirect()->to(base_url('companies'));
		}
		/*
        else{	
          
        	$redirect_uri = base_url();
        	$url = 'https://my.aicountly.com/login/index?redirect_uri='.$redirect_uri;
			return redirect()->to($url);			
        }
       */
      require_once APPPATH."Libraries/vendor/autoload.php";
	 
	   $google_client = new \Google_Client();
	   $google_client->setClientId('17755805151-hobf3kspioi04i7u4ea0k3lvd5o45f5o.apps.googleusercontent.com');
	   $google_client->setClientSecret('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');
	   $google_client->setRedirectUri(base_url().'/');
	   $google_client->addScope('email');
	   $google_client->addScope('profile');
	   
	   if(isset($_GET['code'])){
	       
	       $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
	       
	       if(isset($token['access_token']) && $token['access_token']!=''){
	           
	           $google_client->setAccessToken($token['access_token']);
	           
	           $this->session->set('access_token',$token['access_token']);
			   //$this->session->set('bo_gstin_type',1); //GSTIN Type Regular
	           
	           $google_service = new \Google_Service_Oauth2($google_client);
	           $data =(array)$google_service->userinfo->get();
	           
	           $data = $this->GoogleModel->glogin($data['email'],$data['name']);
	           $this->create_session($data);	           
                
	           return redirect()->to(base_url().'/home/companies'); 
	           
	          
	       }
	       
	   }
	   
	   $login_link = $google_client->createAuthUrl();
	
		   
	     $view_data['message_output']  = $this->message_output;
	  	 $view_data['base_url']        = base_url();	
         $view_data['folder_path']     = $this->folder_path;	
         $view_data['login_link']     = $login_link;
         $view_data['session'] = $this->session;
        return view('login', $view_data);
	
	}

	public function check($auth_code = null)
	{
	    if($auth_code){
	        $response = $this->LoginModel->check_auth($auth_code);
	        if($response['status']){
	            
	            $this->create_session($response['data']);
	            return redirect()->to(base_url().'companies');
	        }
	        
	    }
	    return redirect()->to(base_url().'/login');
	}

	public function authentication($randomString)
    {
        $response = $this->LoginModel->check_auth($randomString);
        
		if($response['status']=="1"){
            $this->create_session($response['data']);
        }

	    return redirect()->to(base_url());
    }


    public function logout()
    {
        $this->session->destroy();
     return redirect()->to(base_url());
    }

    
    public function checkUser()
    {
        if($this->request->getMethod() == 'POST'){

            $model = $this->LoginModel;
            $email = $this->request->getVar('email');

            $data = $model->get_user_by_email($email);
            
            if(empty($data)){
              $data = $model->get_user_by_username($email);  
            }
							
            if($data){
                $response['email'] = $data['user_regdemail'];
                $response['phone'] = $data['user_regdmobile'];
                
                echo json_encode(['status' => 200, 'message' => 'User exists', 'data' => $response]);
            }
            else{
                echo json_encode(['status' => 400, 'message' => 'User not found']);
            }
        }
    } 
    public function checkPassword()
    {
        if($this->request->getMethod() == 'POST'){
		    $model = $this->LoginModel;

		    $value = $this->request->getVar('email');
            $password = $this->request->getVar('password');
            
           $data = $model->get_user_by_email($value);
            
            if(empty($data)){
              $data = $model->get_user_by_username($value);  
            }
            							
            if($data){
                $pass = $data['user_pass'];
			    $verify_pass = password_verify($password, $pass);
			    if($verify_pass){					

                    $this->create_session($data);
                    
                    echo json_encode(['status' => 200, 'message' => 'User exists']); 
				}				 
				else{
					echo json_encode(['status' => 400, 'message' => 'Password not matched']); 					
				}
                
            }
            else{
                echo json_encode(['status' => 400, 'message' => 'User not found']);
            }
        }
    }
    public function forgetPassword()
    {
        if($this->request->getMethod() == 'POST'){
		    $model = $this->LoginModel;
		    
            $user_email = $this->request->getVar('email');
            
            $data = $model->get_user_by_email($user_email);
            
            if($data){
                $enc_id = obfuscate_link($data['uuid']);
                $message = " 
                    <html> 
                    <head> 
                        <title>AICOUNTLY</title> 
                    </head> 
                    <body> 
                        <h1>AICOUNTLY</h1>
                         <p>Click <a href='".base_url()."/login/resetPassword/".$enc_id."'> here </a> to reset your password</p>
                    </body> 
                    </html>";
                
                $subject = "Email verification from AICOUNTLY";
                
                $mailConfig = [
                        'mail_from_email'   => env('EMAIL_FROM_ADDRESS'),
                        'mail_from_name'    => env('EMAIL_FROM_NAME'),
                        'mail_to_email'     => $user_email,
                        'mail_to_name'      => $user_email,
                        'mail_subject'      => $subject,
                        'mail_body'         => $message,
                    ];
                    
                if(sendEmail($mailConfig))
                {
                    $this->session->setFlashdata('message', 'Reset Password Link has been sent to your email address');
                    echo json_encode(['status' => 200, 'message' => 'Email sent successfully']);
                }
                else
    		    {
    		        echo json_encode(['status' => 400, 'message' => 'Error occured! Email not sent']);
    		    }
            }
            else{
                echo json_encode(['status' => 400, 'message' => 'User not found']);
            }
        }
    }
    public function resetPassword($id)
	{
	    $uuid = unobfuscate_link($id,'de')[1];
	    $response = $this->LoginModel->confirm_user($uuid);
	    
	    if($response)
	    {
	        if($this->request->getMethod() == 'post'){
	            
	            
	            $rules = [				
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
						'required' => 'Confirm Password is required',
						'matches' => 'Confirm Password must match Password',
					  ],
				   ],
				  
			    ];
			
                if(!$this->validate($rules)){
                    $this->session->setFlashdata('error_message', $this->validator->listErrors());
                    return redirect()->to($_SERVER['HTTP_REFERER']);
                }
                
                $enc_pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
                 $uuid_aicountly = $this->RegisterModel->add_password($uuid,$enc_pass); 
                 
                 if($uuid_aicountly){
                     $this->session->setFlashdata('message', 'Password Changed');
                     return redirect()->to(base_url().'/login');
                 }
                 else{
                     $this->session->setFlashdata('error_message', 'System Error');
                    return redirect()->to($_SERVER['HTTP_REFERER']);
                 }
	        }
	        
	         $view_data['message_output']   = $this->message_output;
    	  	 $view_data['base_url']         = base_url();	
             $view_data['folder_path']      = $this->folder_path;	
             $view_data['session']          = $this->session;
             $view_data['email']            = $response['uuid_regdemail'];
             $view_data['enc_id']           = $id;
             
	        return view('reset_password', $view_data);
	    }
	    else{
	        return redirect('404', 'refresh');
	    }   
	}
	
	public function sendEmailOtp()
    {
        if($this->request->getMethod() == 'post'){
		    $model = $this->LoginModel;
		    $email = $this->request->getVar('email');
		    
		    $data = $model->where('user_regdemail', $email)->first();

            if($data)
            {
		        $otp = rand(100000, 999999); //generates random otp
		        $this->session->remove('session_otp');
		        $this->session->set('session_otp', $otp);
   
                $response = $this->send_email($email, $otp);
                echo json_encode($response);
            }
		    else
		    {
		        echo json_encode(['status' => 400, 'message' => 'Something went wrong']);
		    }
        }
    }
    public function reSendEmailOtp()
    {
        if($this->request->getMethod() == 'post')
        {
		    $model = $this->LoginModel;
		    $email = $this->request->getVar('email');
		    
		    $data = $model->where('user_regdemail', $email)->first();

            if($data)
            {
                if($this->session->has('session_otp'))
                {
		            $otp = $this->session->get('session_otp');
       
                    $response = $this->send_email($email, $otp);
                	echo json_encode($response);
                }
    		    else
    		    {
    		        echo json_encode(['status' => 400, 'message' => 'Something went wrong']);
    		    }
            }
		    else
		    {
		        echo json_encode(['status' => 400, 'message' => 'Something went wrong']);
		    }
        }
    }
    public function sendPhoneOtp()
    {
        if($this->request->getMethod() == 'post'){
		    $model = $this->LoginModel;
		    $email = $this->request->getVar('email');
		    $phone = $this->request->getVar('phone');
		    $data = $model->where('user_regdemail', $email)->where('user_regdmobile', $phone)->first();
		    
		    if($data)
		    {
		        if($this->session->has('session_otp'))
                {
    		        $otp = rand(100000, 999999); // 6 characters not more than 8
    		        $this->session->remove('session_otp');
    		        $this->session->set('session_otp', $otp);
    		        
    		        $response = $this->send_message($phone, $otp);
    		        echo json_encode($response);
                }
    		    else
    		    {
    		        echo json_encode(['status' => 400, 'message' => 'Something went wrong']);
    		    }
		    }
		    else
		    {
		        echo json_encode(['status' => 400, 'message' => 'Something went wrong']);
		    }
        }
    }
    public function reSendPhoneOtp()
    {
        if($this->request->getMethod() == 'post'){
		    $model = $this->LoginModel;
		    $email = $this->request->getVar('email');
		    $phone = $this->request->getVar('phone');
		    $data = $model->where('user_regdemail', $email)->where('user_regdmobile', $phone)->first();
		    
		    if($data)
		    {
		        $otp = $this->session->get('session_otp');
		        
		        $response = $this->send_message($phone, $otp);
		        echo json_encode($response);
		    }
		    else
		    {
		        echo json_encode(['status' => 400, 'message' => 'Something went wrong']);
		    }
        }
    }
    
    public function matchEmailOtp()
    {
        if($this->request->getMethod() == 'post'){
		    $model = $this->LoginModel;
		    $email = $this->request->getVar('email');
		    $otp = $this->request->getVar('otp');
		    
		    $session_otp = $this->session->get('session_otp');
		    if($otp == $session_otp)
		    {
                $data = $model->where('user_regdemail', $email)->first();
                
                if($data){
                    $this->create_session($data);

                    echo json_encode(['status' => 200, 'message' => 'OTP matched, user logged in']);
                }
                else{
                    echo json_encode(['status' => 400, 'message' => 'Otp matched but something wrong']);
                }
		    }
		    else{
		        echo json_encode(['status' => 400, 'message' => 'Invalid verification code '.$session_otp]);
		    }
        }
    }
    
    public function matchPhoneOtp()
    {
        if($this->request->getMethod() == 'post'){
		    $model = $this->LoginModel;
		    $email = $this->request->getVar('email');
		    $phone = $this->request->getVar('phone');
		    $otp = $this->request->getVar('otp');
		    
		    $session_otp = $this->session->get('session_otp');
		    if($otp == $session_otp){
                $data = $model->where('user_regdemail', $email)->where('user_regdmobile', $phone)->first();
                
                if($data){
                    $this->create_session($data);

                    echo json_encode(['status' => 200, 'message' => 'OTP matched, user logged in']);
                }
                else{
                    echo json_encode(['status' => 400, 'message' => 'Otp matched but something wrong']);
                }
                
		    }
		    else{
		        echo json_encode(['status' => 400, 'message' => 'Invalid OTP']);
		    }
        }
    }
    
    public function sendMobileOtp()
    {
        if($this->request->getMethod() == 'post'){
		    $model = $this->LoginModel;
		    $value = $this->request->getVar('phone');
		    $data = $model->where('user_regdmobile', $value)->get()->getResultArray();
            if(count($data) > 0){
                $otp = rand(100000, 999999); // 6 characters not more than 8
                $this->session->remove('session_otp');
                $this->session->set('session_otp', $otp);
                
                $response = $this->send_message($value, $otp);
		        echo json_encode($response);
            }
		    else{
		        echo json_encode(['status' => 400, 'message' => 'User not found']);
		    }
        }
    }
    
    public function reSendMobileOtp()
    {
        if($this->request->getMethod() == 'post'){
		    $model = $this->LoginModel;
		    $value = $this->request->getVar('phone');
		    $data = $model->where('user_regdmobile', $value)->get()->getResultArray();
            if(count($data) > 0){
                
                if($this->session->has('session_otp')){
        		    $otp = $this->session->get('session_otp');
        		    
                    $response = $this->send_message($value, $otp);
        		    echo json_encode($response);
                }
                else{
                    echo json_encode(['status' => 400, 'message' => 'Something went wrong']);
                }
            }
		    else{
		        echo json_encode(['status' => 400, 'message' => 'User not found']);
		    }
        }
    }
    
    public function matchMobileOtp()
    {
        if($this->request->getMethod() == 'post'){
		    $model = $this->LoginModel;
		    $value = $this->request->getVar('phone');
		    $otp = $this->request->getVar('phone_otp');
		    
		    $session_otp = $this->session->get('session_otp');
		    if($otp == $session_otp){
		        
                $data = $model->where('user_regdmobile', $value)->get()->getResultArray();
                if(count($data) > 0){
                    
                    if(count($data) == 1){
                        $this->create_session($data[0]);
                        echo json_encode(['status' => 200, 'type' => 1, 'message' => 'OTP matched, user logged in']);
                    }
                    else{
                        foreach($data as $key => $value)
                        {
                            $user_data[] = [
                                'user_firstname'      => $value['user_firstname'],
                                'user_lastname'       => $value['user_lastname'],
                                'name'                => $value['user_firstname'].' '.$value['user_lastname'],
                                'email'               => $value['user_regdemail'],
                                'id'                  => obfuscate_link($value['uuid_aicountly'])
                            ]; 
                        }
                        echo json_encode(['status' => 200, 'type' => 2, 'message' => 'OTP matched, select account', 'data' => $user_data]);
                    }
                }
                else{
                    echo json_encode(['status' => 400, 'message' => 'Otp matched but something went wrong']);
                }
		    }
		    else{
		        echo json_encode(['status' => 400, 'message' => 'Invalid OTP']);
		    }
        }
    }
    
    public function loginUser()
    {
        if($this->request->getMethod() == 'post'){
		    $model = $this->LoginModel;
		    $enc_id = $this->request->getVar('id');
		    $otp = $this->request->getVar('phone_otp');
		    
		    $session_otp = $this->session->get('session_otp');
		    if($otp == $session_otp){
		        $enc = unobfuscate_link($enc_id);
		        if(!empty($enc[1])){
		            $uuid_aicountly = $enc[1];
		            $data = $model->where('uuid_aicountly', $uuid_aicountly)->first();
		            if($data){   
		                $this->create_session($data);
		                echo json_encode(['status' => 200, 'message' => 'Success! User logged in']);
		            }
		            else{
    		            echo json_encode(['status' => 400, 'message' => 'Failed! Something went wrong']);
    		        }
		        }
		        else{
		            echo json_encode(['status' => 400, 'message' => 'Failed! Something went wrong']);
		        }
		    }
		    else{
		        echo json_encode(['status' => 400, 'message' => 'Failed! Something went wrong']);
		    }
        }
    }
    
	
    
    public function send_email($email, $otp)
    {
    	$data   = $this->LoginModel->where('user_regdemail', $email)->first();
        $name   = $data['user_firstname'];
                
        $trigger = $this->LoginModel->get_email_trigger();
        if($trigger)
        {
            if($trigger['status'] == 'a')
            {
            	$subject = $trigger['email_subject'];
		        
		        $css  = '<style>'.$trigger['template_css'].'</style><br>';
		        $body = $trigger['email_content'];
		        $body = str_replace("[[UUIDFNAME]]",$name,$body); // max 15 characters
		        $body = str_replace("[[TOKEN]]",$otp,$body); // max 8 characters
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
    public function send_message($phone, $otp)
    {
        $table = $this->LoginModel->get_sms_trigger();
        if($table)
        {
            if($table['status'] == 'a')
            {
                $tp_key         = $table['sms_tp_key'];
                $sender_id      = $table['sms_senderid'];
                $content        = $table['sms_content'];
                $api            = $table['sms_api'];
                
                $data   = $this->LoginModel->where('user_regdmobile', $phone)->first();
		        $name   = $data['user_firstname'];
		        $mobile = $data['user_regdmobile'];
		        
		        if(strlen($name) > 15){
		            $name = substr($name,0,15); // 15 characters
		        }
		        
		        $content = str_replace("[[UUIDFNAME]]",$name,$content); // max 15 characters
		        $content = str_replace("[[TOKEN]]",$otp,$content); // max 8 characters
		        $content = rawurlencode($content); //encodeing is important
		        
		        $api = str_replace("[[TPKEY]]",$tp_key,$api);
		        $api = str_replace("[[SENDERID]]",$sender_id,$api);
		        $api = str_replace("[[MOBILE]]",$mobile,$api);
		        $api = str_replace("[[CONTENT]]",$content,$api);
		      
            	$ch = curl_init($api);
            	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            	$response = curl_exec($ch);
            	curl_close($ch);
            	
            	$response = json_decode($response);
        
                if($response->status == 'success'){
                    return ['status' => 200, 'message' => 'OTP Sent'];
                }
                else{
                    return ['status' => 400, 'message' => 'Failed!', 'hidden_message' => 'API error', 'response' => $response];
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
    
    public function create_session($data)
    {
       
	  
        $this->session->set('ulogged_in', TRUE);				
    	  $this->session->set('uuid_aicountly', $data['uuid']);
    	  $this->session->set('uuid', $data['uuid']);	
    	  $this->session->set('uemail',  $data['user_regdemail']);
    	  $this->session->set('uf_name', $data['user_firstname']);		 
    	  $this->session->set('ul_name', $data['user_lastname']);
        $this->session->set('user_type','CS');	
        $this->session->set('ses_comp_fy_id','0');               		  
		$this->session->set('ses_boid',0);  
        
        //$this->LogModel->add_log();
     }
}