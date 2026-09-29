<?php
namespace App\Controllers\Admin;
use App\Models\Admin\ContactModel;
use App\Models\CommonModel;
use App\Models\RegisterModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;


class Contacts extends BaseController
{
    var	$folder_path;
	function __construct()
    {  
	    helper(['form', 'url', 'mail']);
	     
		$this->ContactModel = new ContactModel();
    $this->CommonModel = new CommonModel();
        $this->RegisterModel = new RegisterModel();		
		$this->auth_session   = new auth_session();
	    $this->folder_path   = getenv('AdminPath');
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
        $this->session    	 = \Config\Services::session();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->user_id       =   $this->session->get('uuid_aicountly');

		
    }
    
    public function index()
    {
        $data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		
	    return view($this->folder_path.'contacts',$data);	
    }

    public function api($method)
    {

        if($this->request->getMethod() == 'post'){

          $uuid_aicountly = $this->session->get('uuid_aicountly');

          $_POST['uuid_aicountly'] = $uuid_aicountly;
          $_POST['method'] = $method;

          $randomString = randomString(); // Authentication
          $this->CommonModel->my_account($randomString,time());
          $_POST['randomString'] = $randomString;

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,"https://my.aicountly.com/admin/people_sharing/api");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            return trim($response);

        }
    }

    public function send_email($uuid_aicountly, $uuid_contactid, $trigger_id)
    {

        return ['status' => false, 'message' => 'function to be modified'];

        $contact = $this->ContactModel->get_user($uuid_aicountly);
        $reg_email = $contact['user_regdemail'];

        $contact = $this->ContactModel->get_user($uuid_contactid);
        $email = $contact['user_regdemail'];
        $name = $contact['user_firstname'];
                
        $trigger = $this->ContactModel->get_email_trigger($trigger_id);
        if($trigger)
        {
            if($trigger['status'] == 'a')
            {
                $subject = $trigger['email_subject'];
                $subject = str_replace("[[UUIDREGEM]]]",$reg_email,$subject);
                
                $css  = '<style>'.$trigger['template_css'].'</style><br>';
                $body = $trigger['email_content'];
                $body = str_replace("[[UUIDREGEM]]",$reg_email,$body);
                $body = str_replace("[[UUIDFNAME]]",$name,$body); 
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
                    return ['status' => true, 'message' => 'OTP Sent'];
                }
                else{
                    return ['status' => false, 'message' => 'Failed!', 'hidden_message' => 'Mail not sent'];
                }
            }   
            else{
                return ['status' => false, 'message' => 'Failed!', 'hidden_message' => 'Trigger not approved'];
            }
        }
        else{
            return ['status' => false, 'message' => 'Failed!', 'hidden_message' => 'Trigger not found'];
        }
    }

    	
}
