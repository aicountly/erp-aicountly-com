<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Models\AppCommonModel;
use App\Libraries\externaldb;

class ApiModel extends Model{

    protected $table = 'aicountly_useraictly_univdb';
     public function __construct() {
        parent::__construct();        
        $this->session 	     = \Config\Services::session();	
        $this->db            = \Config\Database::connect();
        $this->externaldb    = new externaldb();
		$this->AppCommonModel= new AppCommonModel();
		$this->aicountly_db  = $this->externaldb->aicountly_db();
		$this->contactaic_db = $this->externaldb->contactaic_db();
		$this->sispl_uuid_db = $this->externaldb->sispl_uuid_db();
    }    
	function login($postinfo)
		{				
		$builder     = $this->aicountly_db->table("aicountly_useraictly_univdb");
		$data        = $builder->where('LOWER(user_regdemail)', strtolower($postinfo['username']))->get()->getRowArray();
        if(empty($data)){
              $data  = $builder->where('LOWER(user_name)', strtolower($postinfo['username']))->get()->getRowArray();  
        }
		if($data){
			$pass          = $data['user_pass'];
			$password      = $postinfo['password'];
			$verify_pass   = password_verify($password, $pass);
			if($data['useraictly_status']!=1){
			    $return_data['success'] = '0';
				$return_data['message'] = 'Account is inactive';
				$return_data['user_verified'] = '0';				
				return $return_data;	
				
			}
			 else if($verify_pass){
				$return_data['success'] = '1';
				$return_data['message'] = 'Account verified';
				$return_data['user_verified'] = '1';
				
				$return_data['f_name'] = $data['user_firstname'];
                $return_data['m_name'] = $data['user_midname'];
				$return_data['l_name'] = $data['user_lastname'];
				
				$return_data['full_name'] = $data['user_firstname'].' '.$data['user_midname'].' '.$data['user_lastname'];
                $return_data['ses_comp_fy_id'] = 0; 
				
				$return_data['uuid']   = $data['uuid'];
				$return_data['email']  = $data['user_regdemail'];
                $return_data['phone']  = $data['user_regdmobile'];				
				$return_data['aitoken']       = obfuscate_link($this->AppCommonModel->token($data['uuid'])); 
				return $return_data;	
				}
			  else{
				$return_data['success'] = '0';
				$return_data['message'] = 'Password is invalid';
				$return_data['user_verified'] = '0';
				return $return_data;
			   }			
		   }
           else{
				$return_data['success'] = '0';
				$return_data['message'] = 'User not found';
				$return_data['user_verified'] = '0';
				return $return_data;
			 }
		}
	public function get_register_email_otp_trigger()
    {
        $shivansh_db = $this->externaldb->get_shivansherp_db();
        return $shivansh_db->table('trigger_type_email')->where('workflow_trigger_id', 118)->get()->getRowArray();
    }
	

	function SentEmailOtp($name,$email){
		$otp = rand(100000, 999999); //generates random otp
		$trigger = $this->get_register_email_otp_trigger();
		if(empty($name))
			$name ='User';
		if($trigger){
		//$email ='bhupimahey@gmail.com';	
		$subject = $trigger['email_subject'];		        
		$css  = '<style>'.$trigger['template_css'].'</style><br>';
		$body = $trigger['email_content'];
		$body = str_replace("[[UUIDFNAME]]",$name,$body); // max 15 characters
		$body = str_replace("[[TOKEN]]",$otp,$body); // max 15 characters
		$html = $css . $body;
        $mailConfig = [
          'mail_from_email'   => env('EMAIL_FROM_ADDRESS'),
          'mail_from_name'    => env('EMAIL_FROM_NAME'),
          'mail_to_email'     => trim($email),
          'mail_to_name'      => $name,
          'mail_subject'      => $subject,
          'mail_body'         => $html,
        ];
		
		$oks=sendEmail($mailConfig);
		if($oks){
		    $return_data['success'] = '1';
			$return_data['message'] = 'OTP sent';				    
			return $return_data;	
		}
		else{
			$return_data['success'] = '0';
			$return_data['message'] = 'Error occured! OTP not sent';				    
			return $return_data;
    		}
		}
	}
	
	function forgot_password($user_data){
		 $data = $this->profile_info_by_email(trim(strtolower($user_data['user_regdemail'])));
		 if($data){
			 $enc_id = obfuscate_link($data['uuid']);
                $message = " 
                    <html> 
                    <head> 
                        <title>AICOUNTLY</title> 
                    </head> 
                    <body> 
                        <h1>AICOUNTLY</h1>
                         <p>Click <a href='".base_url()."/login/resetPassword/".$enc_id."'> here </a> to reset your password</p><br>Or copy below url to browser
						 ".base_url()."/login/resetPassword/".$enc_id."
                    </body> 
                    </html>";
                
                $subject = "Reset Password from AICOUNTLY";
                
                $mailConfig = [
                        'mail_from_email'   => 'noreply-erp-aicountly@botmail.in',
                        'mail_from_name'    => 'AICOUNTLY',
                        'mail_to_email'     => $user_data['user_regdemail'],
                        'mail_to_name'      => $data['user_firstname'],
                        'mail_subject'      => $subject,
                        'mail_body'         => $message,
                    ];
					
				
				$response = sendEmail($mailConfig);	
				if($response){
					$return_data['success'] = '1';
				    $return_data['message'] = 'Link sent on email '.$user_data['user_regdemail'];				    
					return $return_data;				
				}
				else{
					 $return_data['success'] = '0';
				    $return_data['message'] = 'Mail not sent!!';				    
					return $return_data;
				}
		
		}else{
			        $return_data['success'] = '0';
				    $return_data['message'] = 'Details not found!!';				    
					return $return_data;
		}
		
	}
	function profile_info_by_email($email){	 
		$result= $this->aicountly_db->table('aicountly_useraictly_univdb')
									->where('LOWER(user_regdemail)', $email)
									->get()->getRowArray(); 
     return $result;	
	}
	
	function register($user_data)
		{				
		$nstatus = true;		
		
		/* if(empty($user_data['user_firstname'])){          	 
					$return_data['success'] = '0';
				    $return_data['message'] = 'First name is required';				    
					return $return_data;	              
	            
            } */
		if(empty($user_data['Password'])){          	 
					$return_data['success'] = '0';
				    $return_data['message'] = 'Password is required';				    
					return $return_data;	              
	            
            }
		if(($user_data['Password']!=$user_data['ConfirmPassword'])){          	 
					$return_data['success'] = '0';
				    $return_data['message'] = 'Password & Confirm password should be same';				    
					return $return_data;	              
	            
            }
        			
		
		if(!empty($user_data['user_regdemail'])){
          	if(!$this->AppCommonModel->is_email_unique($user_data['user_regdemail'])){ 
					$return_data['success'] = '0';
				    $return_data['message'] = 'Email already exits on aicountly';				    
					return $return_data;	              
	            }
            }
       /*  else{
           	if(!$this->AppCommonModel->is_email_empty_unique($user_data['user_regdmobile'])){
					$return_data['success'] = '0';
				    $return_data['message'] = 'Email is required';				   
					return $return_data;
	        }
        } */
      
	  if($user_data['SendOTP']==1){
		  $return_data = $this->SentEmailOtp($user_data['user_firstname'],$user_data['user_regdemail']); 	
          return $return_data;
		}
		
		
	   if(!empty($user_data['user_regdemail']))
	   {
	   	  $builder = $this->sispl_uuid_db->table('sispluuid_uuidgenert_univdb');
	      $builder->where('uuid_regdemail', $user_data['user_regdemail']);
	      $builder->where('uuid_project', 1);
	      $result = $builder->get()->getRowArray();
	   }
	  /*  else{
	   	  $builder = $this->sispl_uuid_db->table('sispluuid_uuidgenert_univdb');
	      $builder->where('uuid_regdemail', '');
	      $builder->where('uuid_regdmobile', $user_data['user_regdmobile']);
	      $builder->where('uuid_project', 1);
	      $result = $builder->get()->getRowArray();
	   } */      
      if(!empty($result)){
      	$uuid = $result['uuid'];
      	$result2 = $this->aicountly_db->table('aicountly_useraictly_univdb')
      								->where('uuid', $uuid)
      								->whereIn('useraictly_status',[3,5])
      								->get()->getRowArray();
      	if(empty($result2))
      	{
					$return_data['success'] = '0';
				    $return_data['message'] = 'Error in registration process';
				    $return_data['user_verified'] = '0';
					return $return_data;	
		}

      	$nstatus = false;
      	
      }
      else{
      	$data = [
            'uuid_regdemail' 	=> $user_data['user_regdemail'],
            'uuid_regdmobile' 	=> $user_data['user_regdmobile'],
            'uuid_regdwa' 		=> $user_data['user_wamobile'],
            'uuid_project' 		=> 1,
        ]; 
        
	   	$this->sispl_uuid_db->table('sispluuid_uuidgenert_univdb')->insert($data);
	   	$uuid = $this->sispl_uuid_db->insertID();

	   	$nstatus = true;
      }

	    $useraictly_data = [
	   		'uuid'				=> $uuid,
	   		'user_firstname' 	=> $user_data['user_firstname'] ?? '',
	   		'user_midname' 		=> $user_data['user_midname'] ?? '',
	   		'user_lastname' 	=> $user_data['user_lastname'] ?? '',
	   		'user_name' 		=> $user_data['user_name'] ?? '',
	   		'user_pass' 		=> $user_data['user_pass'] ?? '',
	   		'user_regdmobile' 	=> $user_data['user_regdmobile'] ?? '',
	   		'user_regdemail' 	=> $user_data['user_regdemail'] ?? '',
	   		'user_wamobile' 	=> $user_data['user_wamobile'] ?? '',
	   		'user_aicountly_business_id' => $user_data['user_aicountly_business_id'] ?? '',
	   		'user_pin' 			=> $user_data['user_pin'] ?? '',
	   		'user_gender' 		=> $user_data['user_gender'] ?? '',
	   		'user_type_profs' 	=> $user_data['user_type_profs'] ?? '',
	   		'user_dob' 			=> $user_data['user_dob'] ?? '',
	   		'auth_code' 		=> $user_data['auth_code'] ?? '',
	   		'auth_time' 		=> $user_data['auth_time'] ?? '',
	   		'useraictly_status' => 1,
	   ];

	   if($nstatus){
	   		$this->aicountly_db->table('aicountly_useraictly_univdb')
	   							->insert($useraictly_data);
	   }
	   else{
	   		$this->aicountly_db->table('aicountly_useraictly_univdb')
	   							->where('uuid', $uuid)
	   							->update($useraictly_data);
	   }
	   	
	   $useraicdet_data = [
	   		'uuid'				=> $uuid,
	   		'user_email2' 		=> $user_data['user_email2'] ?? '',
	   		'user_mobile2' 		=> $user_data['user_mobile2'] ?? '',
	   		'user_landline' 	=> $user_data['user_landline'] ?? '',
	   		'user_website' 		=> $user_data['user_website'] ?? '',
	   		'user_add1' 		=> $user_data['user_add1'] ?? '',
	   		'user_add2' 		=> $user_data['user_add2'] ?? '',
	   		'user_city' 		=> $user_data['user_city'] ?? '',
	   		'user_state' 		=> $user_data['user_state'] ?? '',
	   		'user_country' 		=> $user_data['user_country'] ?? '',
	   ];

	   $this->aicountly_db->table('aicountly_useraicdet_univdb')->insert($useraicdet_data);
		
	   $return_data['success'] = '1';
	   $return_data['message'] = 'Registration is successful. Please proceed to login';
	   $return_data['user_verified'] = '1';	
	   return $return_data;			
	}
    
    function page_info($postinfo){
        if($postinfo['page_name']=='about_us'){
		  
          $page_content = file_get_contents(base_url().'/about_us.txt'); 
            
        }
        if($postinfo['page_name']=='pricing_policy'){
		   $page_content = fetchDivContentByClass("https://aicountly.com/pricing_policy", "fetchpagecontent");
          $pageinfo='';
		  foreach($page_content as $text)
             $pageinfo .=	$text . "\n";	
         // $page_content = file_get_contents(base_url().'/pricing_policy.txt'); 
            
        }
       if($postinfo['page_name']=='refund_policy'){
		  $page_content = fetchDivContentByClass("https://aicountly.com/refund_policy", "fetchpagecontent");
          $pageinfo='';
		  foreach($page_content as $text)
             $pageinfo .=	$text . "\n"; 
         // $page_content = file_get_contents(base_url().'/refund_policy.txt'); 
            
        } 
      if($postinfo['page_name']=='terms_of_use'){
		  $page_content = fetchDivContentByClass("https://aicountly.com/terms_of_use", "fetchpagecontent");
          $pageinfo='';
		  foreach($page_content as $text)
             $pageinfo .=	$text . "\n";	  
		
		 // $page_content = file_get_contents(base_url().'/terms_of_use.txt'); 
            
        }  
        return $pageinfo;
    }	
    
    function google_login_info($postinfo){
        $data        = $this->AppCommonModel->glogin($postinfo['email_id'],$postinfo['full_name']);
       	$return_data['success'] = '1';
		$return_data['message'] = 'Account verified';
		$return_data['user_verified'] = '1';
				
		$return_data['f_name'] = $data['user_firstname'];
        $return_data['m_name'] = $data['user_midname'];
		$return_data['l_name'] = $data['user_lastname'];
				
		$return_data['full_name'] = $data['user_firstname'].' '.$data['user_midname'].' '.$data['user_lastname'];
        $return_data['ses_comp_fy_id'] = 0; 
				
		$return_data['uuid']   = $data['uuid'];
		$return_data['email']  = $data['user_regdemail'];
        $return_data['phone']  = $data['user_regdmobile'];				
		$return_data['aitoken']       = obfuscate_link($this->AppCommonModel->token($data['uuid']));
		return $return_data;
    }
		
	function user_company_lists($postinfo){
	   $user_companies        = $this->AppCommonModel->user_company_list($postinfo['uuid']);
	  
	   if($user_companies){
		 $return_data['success'] = '1';
		 $return_data['company_lists'] = $user_companies;				
		 return $return_data;  
	   }
		   else{
					$return_data['success'] = '0';
					$return_data['message'] = 'not found';				
					return $return_data;
				 }	   
	}
	
	function balance_sheet_info($postinfo){
		
	   /*  if($postinfo['format'] == 1)
        $balance_sheet = $this->AppCommonModel->load_balance_sheet($postinfo['view'],$postinfo['fromdate'],$postinfo['todate'],$postinfo['nil_type'],$postinfo);

       if($postinfo['format'] == 2) 
        $balance_sheet = $this->AppCommonModel->load_balance_sheet2($postinfo['view'],$postinfo['fromdate'],$postinfo['todate'],$postinfo['nil_type'],$postinfo);
		 */
	   $balance_sheet = $this->AppCommonModel->balance_sheet_tabs($postinfo['view'],$postinfo['fromdate'],$postinfo['todate'],$postinfo['nil_type'],$postinfo);
		
	   if($balance_sheet){
		 $return_data['success'] = '1';
		 $return_data['lists'] = $balance_sheet;				
		 return $return_data;  
	   }
	   else{
		$return_data['success'] = '0';
		$return_data['message'] = 'not found';				
		return $return_data;
	   }	   
	}
	function profit_loss_info($postinfo){
      $profitloss = $this->AppCommonModel->profit_loss_tabs($postinfo['view'],$postinfo['fromdate'],$postinfo['todate'],$postinfo['nil_type'],$postinfo);
	  if($profitloss){
		 $return_data['success'] = '1';
		 $return_data['lists'] = $profitloss;				
		 return $return_data;  
	   }
	   else{
		$return_data['success'] = '0';
		$return_data['message'] = 'not found';				
		return $return_data;
	   }	   
	}
	
	function trial_balance_info($postinfo){
		
	    if($postinfo['view'] == 0)
        $trialbal = $this->AppCommonModel->load_trial_balance($postinfo['fromdate'],$postinfo['todate'],$postinfo['nil_type'],$postinfo);
       else if($postinfo['view'] == 1)
        $trialbal = $this->AppCommonModel->load_trial_balance1($postinfo['fromdate'],$postinfo['todate'],$postinfo['nil_type'],$postinfo);
  	   else 
        $trialbal = $this->AppCommonModel->load_trial_balance2($postinfo['fromdate'],$postinfo['todate'],$postinfo['nil_type'],$postinfo);
		
       $final_array= array();		
	   if($trialbal){
		  foreach($trialbal as $rows){
			  if($rows['debit_total'] >=0 && $rows['credit_total']==0)
				 $final_array['debit'][]=$rows;
			 if($rows['credit_total'] >=0 && $rows['debit_total']==0)
				 $final_array['credit'][]=$rows;
		  } 
		   
		 $return_data['success'] = '1';
		 $return_data['lists'] = $final_array;				
		 return $return_data;  
	   }
	   else{
		$return_data['success'] = '0';
		$return_data['message'] = 'not found';				
		return $return_data;
	   }	   
	}
	
    function country_states($postinfo){
		$state_list  = $this->AppCommonModel->get_state_array($postinfo['country_id']);
		if($state_list){
		 $return_data['success'] = '1';
		 $return_data['lists']   = $state_list;	
		}
		else{
		$return_data['success'] = '0';
		$return_data['lists'] = [];			
		}
	return $return_data;		
	}
	
	function country_lists($postinfo){
		$country_list  = $this->AppCommonModel->get_country_array();
		if($country_list){
		 $return_data['success'] = '1';
		 $return_data['lists']   = $country_list;	
		}
		else{
		$return_data['success'] = '0';
		$return_data['lists'] = [];			
		}
	return $return_data;		
	}
	
	function profile_info($postinfo){
	        $data = [];
            $id = $postinfo['uuid'];
            $builder = $this->aicountly_db->table('aicountly_useraictly_univdb');
            $builder->select('*');
            $builder->where('uuid', $id);
            $result = $builder->get()->getRowArray();

            $builder = $this->aicountly_db->table('aicountly_useraicdet_univdb');
            $builder->select('*');
            $builder->select('countryname, state_name');
            $builder->join('aicountly_countrylst_univdb', 'aicountly_countrylst_univdb.countryid = aicountly_useraicdet_univdb.user_country', 'left');
            $builder->join('aicountly_stateslist_univdb', 'aicountly_stateslist_univdb.state_id = aicountly_useraicdet_univdb.user_state', 'left');
            $builder->where('uuid', $id);
            $result2 = $builder->get()->getRowArray();
            $data = [
                'uuid'              => $id,
                'user_firstname'    => $result['user_firstname'] ?? '',
                'user_midname'      => $result['user_midname'] ?? '',
                'user_lastname'     => $result['user_lastname'] ?? '',
                'user_name'         => $result['user_name'] ?? '',
                'user_pass'         => $result['user_pass'] ?? '',
                'user_regdmobile'   => $result['user_regdmobile'] ?? '',
                'user_regdemail'    => $result['user_regdemail'] ?? '',
                'user_wamobile'     => $result['user_wamobile'] ?? '',
                'user_aicountly_business_id' => $result['user_aicountly_business_id'] ?? '',
                'user_pin'          => $result['user_pin'] ?? '',
                'user_gender'       => $result['user_gender'] ?? '',
                'user_type_profs'   => $result['user_type_profs'] ?? '',
                'user_dob'          => $result['user_dob'] ?? '',
                'user_dob'          => !empty($result['user_dob']) ? date('d M, Y', strtotime($result['user_dob'])) : '',
                'user_email2'       => $result2['user_email2'] ?? '',
                'user_mobile2'      => $result2['user_mobile2'] ?? '',
                'user_landline'     => $result2['user_landline'] ?? '',
                'user_website'      => $result2['user_website'] ?? '',
                'user_add1'         => $result2['user_add1'] ?? '',
                'user_add2'         => $result2['user_add2'] ?? '',
                'user_city'         => $result2['user_city'] ?? '',
                'user_state'        => $result2['user_state'] ?? '',
                'user_country'      => $result2['user_country'] ?? '',
                'state_name'        => $result2['state_name'] ?? '',
                'countryname'       => $result2['countryname'] ?? '',
             ];	
		if($data){
		 $return_data['success'] = '1';
		 $return_data['info']   = $data;	
		}
		else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}
	return $return_data;	
	}
	
	function contact_info($postinfo){
	 $builder = $this->contactaic_db->table('contactaic_contactaic_univdb2');
     $builder->select('contactaic_contactaic_univdb2.*, contactaic_contactaic_univdb2.contact_id, contactaic_contactaic_univdb2.contact_uuid');
     $builder->join('contactaic_mycontacts_univdb', 'contactaic_mycontacts_univdb.contact_id = contactaic_contactaic_univdb2.contact_id', 'left');
     $builder->select('contact_firstname, contact_lastname, CONCAT(contact_firstname," ",contact_lastname) as contact_name, contact_email, contact_mobile, contact_wamobile, contact_email2, contact_mobile2, contact_landline, contact_website');
     $builder->join('contactaic_mycontaddr_univdb', 'contactaic_mycontaddr_univdb.contact_id = contactaic_contactaic_univdb2.contact_id','left');
     $builder->select('contact_add1, contact_add2, contact_city, contact_state, contact_country, contact_pin, contact_addtype, contact_default');
     $builder->where('contactaic_contactaic_univdb2.contact_id',$postinfo['contact_id']);
     $result = $builder->get()->getRowArray();
	 if($result){
        $result['category'] = [];
        $result['cat_ids']  = [];
        if(!empty($result['contact_cat_id'])){
            $arr     = explode(",",$result['contact_cat_id']);
            $cat_arr = [];
            foreach($arr as $cat_id){
                $category = $this->AppCommonModel->get_category_by_id($cat_id);
                if(!empty($category)){
                    array_push($cat_arr, [
                           'category_id'    => $category['contact_cat_id'],
                           'category_name'  => $category['contact_category'],
                           'category_color' => $category['contact_category_color']
                        ]);
                    }
                }
            $result['cat_ids'] = $arr;
            $result['category'] = $cat_arr;
        }
		
		$return_data['success']           = '1';
		$return_data['info']              = $result;	
        $return_data['shared_by_me']      = $this->AppCommonModel->get_shared_companies($result['uuid'],$result['contact_uuid']);
        $return_data['shared_by_contact'] = $this->AppCommonModel->get_shared_companies($result['contact_uuid'],$result['uuid']);
    }	
	else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}  
	return $return_data;
	}
	
	function add_contact($postinfo)
		{				
		    if(!empty($postinfo['email_id'])){
                if(!$this->AppCommonModel->is_email_unique($postinfo['email_id']))
                {
			      $return_data['success'] = '0';
				  $return_data['message'] = 'Email already exists on aicountly';				  		
				  return $return_data;
                }else{
				 $contact_uuid = $this->AppCommonModel->add_user($postinfo);				
				if($contact_uuid){
					$this->AppCommonModel->add_contact($postinfo['uuid'],$contact_uuid);	
					$return_data['success'] = '1';
					$return_data['message'] = 'Contact Addedd Successfully';
					return $return_data;
				}else{
					$return_data['success'] = '0';
					$return_data['message'] = 'Something went wrong';
					return $return_data;
				  }
				
			   }
            }
            else{
                if(!$this->AppCommonModel->is_email_empty_unique($postinfo['mobile_no']))
                {
				  $return_data['success'] = '0';
				  $return_data['message'] = 'Email is required';				 		
				  return $return_data;
			}else{
				$contact_uuid = $this->AppCommonModel->add_user($postinfo);				
				if($contact_uuid){
					$this->AppCommonModel->add_contact($postinfo['uuid'],$contact_uuid);	
					$return_data['success'] = '1';
					$return_data['message'] = 'Contact Addedd Successfully';
					return $return_data;
				}else{
					$return_data['success'] = '0';
					$return_data['message'] = 'Something went wrong';
					return $return_data;
				  }
				
			   }
            }
			    
				
			
	}
	
  function update_primary_contact($postinfo){
	$return_data=array();	
	if(isset($postinfo["contact_cat_id"]) && !isset($postinfo["company_name"]) && !isset($data["job_title"])){
		$updata = array("contact_cat_id"=>$postinfo["contact_cat_id"]);        
        $this->contactaic_db->table('contactaic_contactaic_univdb2')
                            ->where('contact_id', $postinfo['contact_id'])
                            ->update($updata);
		
	}
    else if(isset($postinfo["mark_fav"])){	
	  $data = [
                    'contact_star' => $postinfo["mark_fav"],
                    'contact_id'   => $postinfo['contact_id'],
                ];	
      $this->contactaic_db->table('contactaic_contactaic_univdb2')
                            ->where('contact_id', $postinfo['contact_id'])
                            ->update($data);
	
      $updata = array("contact_compname"=>$postinfo["company_name"],"contact_jobtitle"=>$postinfo["job_title"],"contact_notes"=>$postinfo["notes"],
                      "contact_cat_id"=>$postinfo["contact_cat_id"]);
        
        $this->contactaic_db->table('contactaic_contactaic_univdb2')
                            ->where('contact_id', $postinfo['contact_id'])
                            ->update($updata);
	 
     }
    else{
		$updata = array("contact_compname"=>$postinfo["company_name"],"contact_jobtitle"=>$postinfo["job_title"],"contact_notes"=>$postinfo["notes"],
                      "contact_cat_id"=>$postinfo["contact_cat_id"]);
        
        $this->contactaic_db->table('contactaic_contactaic_univdb2')
                            ->where('contact_id', $postinfo['contact_id'])
                            ->update($updata);
		
	}	
	$return_data['success'] = '1';
    $return_data['message'] = 'Updated';	
	return $return_data;	
	}
	
	function contact_categories($postinfo){
		$data  = $this->AppCommonModel->get_categories($postinfo['uuid']);
		if($data){
		$return_data['success'] = '1';
		$return_data['lists'] = $data;	
		}
		else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}
		return $return_data;
	}
	
	function McDropdown($postinfo){
		$data  = $this->AppCommonModel->get_mcs_list($postinfo);
		if($data){
		$return_data['success'] = '1';
		$return_data['lists'] = $data;	
		}
		else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}
		return $return_data;
	}
	function McGroupDropdown($postinfo){
		$data  = $this->AppCommonModel->get_mcs_grp_list($postinfo);
		if($data){
		$return_data['success'] = '1';
		$return_data['lists'] = $data;	
		}
		else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}
		return $return_data;
	}
	
	function GetAccountGroupTotal($postinfo){		
		$data        = $this->AppCommonModel->GetAccountGroupTotal($postinfo);
		return $data;	
	}
	function AccountGroups($postinfo,$start, $limit){			
		$result        = $this->AppCommonModel->GetAccountGroupListing($postinfo,$start, $limit);
		if($result){		   	
		    $return_data['success'] = '1';
			$return_data['listings'] = $result;
		}		
		else{
			$return_data['success'] = '0';	
			$return_data['listings'] = array();			
		  }
		  return $return_data;
	}
	
	function companyselection($postinfo){
			
		$company_info      = $this->AppCommonModel->get_company_info($postinfo['company_id']);
		
		$company_gen_info  = $this->AppCommonModel->get_company_gen_info($postinfo['company_id']);
        $company_last_fy   = $this->AppCommonModel->company_last_fy($postinfo['company_id']);
			
		if($company_last_fy){
		   $comp_fy_id        = $company_last_fy['comp_fy_id'];
		  }
		  else{
		   $company_last_fy   = $company_info;
           $comp_fy_id        = $company_last_fy['comp_fy_id'];		 
		  }
	   
		
		$comp_code         = $postinfo['comp_code'];
		$adrs_info         = $this->AppCommonModel->get_comp_ho_adrs_info($postinfo['company_id'],$comp_fy_id,$comp_code);
		if($adrs_info){			  
			 $bo_state_code = ($adrs_info['bo_state_code'])?$adrs_info['bo_state_code']:4; 
			 $boid          = $adrs_info['bo_id']; 
			 $boname        = $adrs_info['bo_name']; 
		  }else{
			  $bo_state_code = 4;		  
			  $boid          = 1;
			  $boname        = "N/A";
		     }
		$items_list       = $this->AppCommonModel->company_all_items($postinfo['company_id'],$comp_fy_id,$comp_code);		 
		$accounts_list    = $this->AppCommonModel->company_all_accounts($postinfo['company_id'],$comp_fy_id,$comp_code);			 
		$company_all_bsd  = $this->AppCommonModel->company_all_bsd($postinfo['company_id'],$comp_fy_id,$comp_code);	 
		
		$item_file_name   = 'item'.$comp_fy_id.'.json';
		$items_json_path  = base_url().'/writable/comp'.$postinfo['company_id'].'/'.$item_file_name;
		
		 $company_db_size  = $this->AppCommonModel->company_db_size($company_info['comp_code']);	 
		
		
		$data  = array('comp_uuid'          => $company_gen_info['uuid'],
		             'comp_fy_id'           => $comp_fy_id,
					 'company_id'           => $postinfo['company_id'],
					 'company_code'         => $company_info['comp_code'],
					 'company_name'         => $company_info['comp_name'],
					 'company_print_name'   => $company_info['comp_print_name'],
					 'company_short_name'   => $company_info['comp_short_name'],
					 'company_fy_beginning' => date('d-m-Y',strtotime($company_last_fy['fy_begndt'])),
					 'company_fy_end'       => date('d-m-Y',strtotime($company_last_fy['fy_end'])),					
					 'fy_start'             => date('y',strtotime($company_last_fy['fy_begndt'])),
					 'fy_end'               => date('y',strtotime($company_last_fy['fy_end'])),					 
					 'company_email'        => $company_info['corp_email'],
					 'company_mobile'       => $company_info['corp_tel'],
					 'company_wa_mobile'    => $company_info['comp_wa_mobile'],
					 'bo_state_code'        => $bo_state_code,'boid'=>$boid,
					 'boname'               => $boname,					 
					 'accounts_list'        => $accounts_list,
					 'company_all_bsd'      => $company_all_bsd,
					 'items_json_path'      => $items_json_path,
					 'company_size'         => $company_db_size
					 );
		
	 if($data){
		$return_data['success'] = '1';
		$return_data['info'] = $data;	
		}
	 else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}
		return $return_data;
	}
	
	function company_fy_selection($postinfo){		
		$company_info        = $this->AppCommonModel->get_company_info($postinfo['company_id'],$postinfo['comp_fy_id']);
		$company_last_fy     =  $this->AppCommonModel->company_last_fy($postinfo['company_id'],$postinfo['comp_fy_id']);
		$comp_fy_id          = $postinfo['comp_fy_id'];
		$company_gen_info    = $this->AppCommonModel->get_company_gen_info($postinfo['company_id']);
		$comp_code           = $postinfo['comp_code'];
		$adrs_info           = $this->AppCommonModel->get_comp_ho_adrs_info($postinfo['company_id'],$comp_fy_id,$comp_code);
		if($adrs_info){			  
			 $bo_state_code  = ($adrs_info['bo_state_code'])?$adrs_info['bo_state_code']:4; 
			 $boid           = $adrs_info['bo_id']; 
			 $boname         = $adrs_info['bo_name']; 
		  }else{
			  $bo_state_code = 4;		  
			  $boid          = 1;
			  $boname        = "N/A";
		     }
		$items_list       = $this->AppCommonModel->company_all_items($postinfo['company_id'],$comp_fy_id,$comp_code);		 
		$accounts_list    = $this->AppCommonModel->company_all_accounts($postinfo['company_id'],$comp_fy_id,$comp_code);			 
		$company_all_bsd  = $this->AppCommonModel->company_all_bsd($postinfo['company_id'],$comp_fy_id,$comp_code);		
		$item_file_name   = 'item'.$comp_fy_id.'.json';
		$items_json_path  = base_url().'/writable/comp'.$postinfo['company_id'].'/'.$item_file_name;
		
		$data  = array('comp_uuid'          => $company_gen_info['uuid'],
		             'comp_fy_id'           => $comp_fy_id,
					 'company_id'           => $postinfo['company_id'],
					 'company_code'         => $company_info['comp_code'],
					 'company_name'         => $company_info['comp_name'],
					 'company_print_name'   => $company_info['comp_print_name'],
					 'company_short_name'   => $company_info['comp_short_name'],
					 'company_fy_beginning' => date('d-m-Y',strtotime($company_last_fy['fy_begndt'])),
					 'company_fy_end'       => date('d-m-Y',strtotime($company_last_fy['fy_end'])),					
					 'fy_start'             => date('y',strtotime($company_last_fy['fy_begndt'])),
					 'fy_end'               => date('y',strtotime($company_last_fy['fy_end'])),					 
					 'company_email'        => $company_info['corp_email'],
					 'company_mobile'       => $company_info['corp_tel'],
					 'company_wa_mobile'    => $company_info['comp_wa_mobile'],
					 'bo_state_code'        => $bo_state_code,
					 'boid'                 => $boid,
					 'boname'               => $boname,					 
					 'accounts_list'        => $accounts_list,
					 'company_all_bsd'      => $company_all_bsd,
					 'items_json_path'      => $items_json_path
					 );
		
	 if($data){
		$return_data['success'] = '1';
		$return_data['info'] = $data;	
		}
	 else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}
		return $return_data;
	}
	
	function GetAccountOrGroupListings($postinfo){
		$company_id         = $postinfo['company_id'];
	    $comp_fy_id         = $postinfo['comp_fy_id'];  
		$group_id           = $postinfo['group_id'];
		$parent_id           = $postinfo['parent_id'];
	    $external_db        = $this->externaldb->single_company_db($postinfo['comp_code']); 
	
		if($parent_id==1){
    		$group_info  = $this->AppCommonModel->group_parent_info($group_id,$external_db,$postinfo);
			
			if(!$group_info)
			$grp_name    = 'N/A';
            else		
    		$grp_name    = $group_info['acc_grp_parent'];
    		$data_list   = $this->AppCommonModel->load_accounts_trial_balance3($external_db,$postinfo);
    	}
    	else{
    		$group_info  = $this->AppCommonModel->main_group_info($group_id,$external_db,$postinfo);
			
    		$grp_name    = $group_info['acc_grp_name'];				
    		$data_list   = $this->AppCommonModel->load_accounts_trial_balance2($external_db,$postinfo);
    	}
		$final_array = array();
	    if($data_list){
			foreach($data_list  as $rows){				
					$final_array['cr_dr'][]=array("entity_type"=>$rows['entity_type'],"entity_name"=>$rows["entity_name"],"credit"=>$rows["credit"],"debit"=>$rows["debit"],"entity_id"=>$rows["entity_id"]);
					$final_array['balance'][]=array("entity_id"=>$rows["entity_id"],"entity_type"=>$rows['entity_type'],"entity_name"=>$rows["entity_name"],"balance_total"=>(abs($rows["balance_total"])==0)?"0.0":parseAmount(abs($rows["balance_total"])),"balance_type"=>$rows["balance_type"],"op_balance"=>$rows["op_balance"],"op_balance_total"=>(abs($rows["balance_total"])==0)?"0.0":parseAmount($rows["op_balance_total"]));
				
			}
		}
		$return_data['success'] = '1';
		$return_data['grp_name'] = $grp_name;	
		$return_data['listings'] = $final_array;	
		
		
	
		return $return_data;
	}
	
	function company_bo_selection($postinfo){		
		$company_info        = $this->AppCommonModel->get_company_info($postinfo['company_id'],$postinfo['comp_fy_id']);
		
		$company_last_fy     = $this->AppCommonModel->company_last_fy($postinfo['company_id'],$postinfo['comp_fy_id']);
		$comp_fy_id          = $postinfo['comp_fy_id'];
		$company_gen_info    = $this->AppCommonModel->get_company_gen_info($postinfo['company_id']);
		$comp_code           = $postinfo['comp_code'];
		$adrs_info           = $this->AppCommonModel->get_bo_info($postinfo['company_id'],$comp_fy_id,$comp_code,$postinfo['bo_id']);
		if($adrs_info){			  
			 $bo_state_code  = ($adrs_info['bo_state_code'])?$adrs_info['bo_state_code']:4; 
			 $boid           = $adrs_info['bo_id']; 
			 $boname         = $adrs_info['bo_name']; 
		  }else{
			  $bo_state_code = 4;		  
			  $boid          = 1;
			  $boname        = "N/A";
		     }
		$items_list       = $this->AppCommonModel->company_all_items($postinfo['company_id'],$comp_fy_id,$comp_code);		 
		$accounts_list    = $this->AppCommonModel->company_all_accounts($postinfo['company_id'],$comp_fy_id,$comp_code);			 
		$company_all_bsd  = $this->AppCommonModel->company_all_bsd($postinfo['company_id'],$comp_fy_id,$comp_code);		
		$item_file_name   = 'item'.$comp_fy_id.'.json';
		$items_json_path  = base_url().'/writable/comp'.$postinfo['company_id'].'/'.$item_file_name;
		
		$data  = array('comp_uuid'          => $company_gen_info['uuid'],
		             'comp_fy_id'           => $comp_fy_id,
					 'company_id'           => $postinfo['company_id'],
					 'company_code'         => $company_info['comp_code'],
					 'company_name'         => $company_info['comp_name'],
					 'company_print_name'   => $company_info['comp_print_name'],
					 'company_short_name'   => $company_info['comp_short_name'],
					 'company_fy_beginning' => date('d-m-Y',strtotime($company_last_fy['fy_begndt'])),
					 'company_fy_end'       => date('d-m-Y',strtotime($company_last_fy['fy_end'])),					
					 'fy_start'             => date('y',strtotime($company_last_fy['fy_begndt'])),
					 'fy_end'               => date('y',strtotime($company_last_fy['fy_end'])),					 
					 'company_email'        => $company_info['corp_email'],
					 'company_mobile'       => $company_info['corp_tel'],
					 'company_wa_mobile'    => $company_info['comp_wa_mobile'],
					 'bo_state_code'        => $bo_state_code,
					 'boid'                 => $boid,
					 'boname'               => $boname,					 
					 'accounts_list'        => $accounts_list,
					 'company_all_bsd'      => $company_all_bsd,
					 'items_json_path'      => $items_json_path
					 );
		
	 if($data){
		$return_data['success'] = '1';
		$return_data['info'] = $data;	
		}
	 else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}
		return $return_data;
	}
	
	function GetDayBookTotal($postinfo){	 
	   $data   = $this->AppCommonModel->GetDayBookTotal($postinfo);
	   return $data;	       			
	}
	
	function GetCompFYLists($postinfo){	 
	   $data   = $this->AppCommonModel->company_all_fy_lists($postinfo);
	   if($data){
		$return_data['success'] = '1';
		$return_data['listings'] = $data;	
		}
	 else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}
		return $return_data;	       			
	}
	
	function GetDayBook($postinfo,$start, $limit){	 
	   $result   = $this->AppCommonModel->GetDayBook($postinfo,$start, $limit);
	   if($result){		   	
		    $return_data['success'] = '1';
			$return_data['listings'] = $result;
		}		
		else{
			$return_data['success'] = '0';	
			$return_data['listings'] = array();			
		  }
		  return $return_data;	       			
	}
	function ItemUnits($postinfo){
	  $result   = $this->AppCommonModel->GetItemUnits($postinfo);
	  if($result){		   	
		    $return_data['success'] = '1';
			$return_data['listings'] = $result;
		}		
		else{
			$return_data['success'] = '0';	
			$return_data['listings'] = array();			
		  }
		  return $return_data;	
		
	}
	
	function BranchLists($postinfo){
	  $result   = $this->AppCommonModel->GetBranchLists($postinfo);
	  if($result){		   	
		    $return_data['success'] = '1';
			$return_data['listings'] = $result;
		}		
		else{
			$return_data['success'] = '0';	
			$return_data['listings'] = array();			
		  }
		  return $return_data;	
		
	}
	
	function GetItemLedgerTotal($postinfo){	 
	   $data   = $this->AppCommonModel->ItemLedgerTotal($postinfo);
	   return $data;	       			
	}
	function ItemExists($postinfo){	 
	   $data   = $this->AppCommonModel->ItemExists($postinfo);
	   return $data;	       			
	}
	
	function GetItemLedger($postinfo,$start, $limit){	
       $item_info = $this->AppCommonModel->item_single_info($postinfo); 
	   $item_name = $item_info['item_name'];	
	   $result    = $this->AppCommonModel->ItemLedger($postinfo,$start, $limit);
	   if($result){		   	
		    $return_data['success']   = '1';
			$return_data['listings']  = $result;
			$return_data['item_name'] = $item_name;
		}		
		else{
			$return_data['success']  = '0';	
			$return_data['listings'] = array();			
		  }
		  return $return_data;	       			
	  }
	  
   function GetItemSummary($postinfo,$start, $limit){	
       $item_info = $this->AppCommonModel->item_single_info($postinfo); 
	   $item_name = $item_info['item_name'];	
	   $result    = $this->AppCommonModel->ItemSummary($postinfo);
	   if($result){		   	
		    $return_data['success']   = '1';
			$return_data['listings']  = $result;
			$return_data['item_name'] = $item_name;
		}		
		else{
			$return_data['success']  = '0';	
			$return_data['listings'] = array();			
		  }
		  return $return_data;	       			
	  }
	
	function GetAccountLedgerTotal($postinfo){	 
	   $data   = $this->AppCommonModel->AccountLedgerTotal($postinfo);
	   return $data;	       			
	}
	
	function GetStockStatusTotal($postinfo){	 
	   $data   = $this->AppCommonModel->StockStatusTotal($postinfo);
	   return $data;	       			
	}
	function GetStockStatus($postinfo,$start, $limit){	
       $result    = $this->AppCommonModel->StockStatus($postinfo,$start, $limit);
	   if($result){		   	
		    $return_data['success']   = '1';
			$return_data['listings']  = $result;
		}		
		else{
			$return_data['success']  = '0';	
			$return_data['listings'] = array();			
		  }
		  return $return_data;	       			
	  }
	function GetAccountLedger($postinfo,$start, $limit){	
       $account_info = $this->AppCommonModel->account_single_info($postinfo); 
	   $account_name       = $account_info['acc_name'];	
	   $result   = $this->AppCommonModel->AccountLedger($postinfo,$start, $limit);
	   if($result){		   	
		    $return_data['success']      = '1';
			$return_data['listings']     = $result;
			$return_data['account_name'] = $account_name;
		}		
		else{
			$return_data['success'] = '0';	
			$return_data['listings'] = array();			
		  }
		  return $return_data;	       			
	}
	
   function GetAccountSummary($postinfo){	   
	 $account_info = $this->AppCommonModel->account_single_info($postinfo); 
	 $account_name       = $account_info['acc_name'];
	 $acc_op_bal = 0;
	 $get_opn_balance_info = $this->AppCommonModel->acc_opn_balance_info($postinfo);	  	
	 if($get_opn_balance_info)
	   $acc_op_bal = floatval($get_opn_balance_info['acc_op_bal']);	 	
	 if($acc_op_bal < 0)
	   $acc_opn_balance = formatAmount(abs($acc_op_bal)).' CR';
	  else
	   $acc_opn_balance = formatAmount($acc_op_bal).' DR';
	 $data = $this->AppCommonModel->accounts_monthly_balance($postinfo);
	 if($data){ 
		$return_data['success'] = '1';
		$return_data['info'] = $data;
		$return_data['account_name'] = $account_name;	
		}
	 else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}
		return $return_data;
	}
	
	function receivableInfo($postinfo){
	  $company_id  = $postinfo['company_id'];	
	  $comp_fy_id  = $postinfo['comp_fy_id'];
	  $comp_code   = $postinfo['comp_code'];
	  $boid        = $postinfo['boid'];
	  $fy_calender = fy_calender();
      $fy_months   = $fy_calender->months;
      $total_year  = $fy_calender->total_year; // 1 or 2 
      $months      = [];
      $within_due  = [];
      $overdue     = [];
      $advances    = [];
      $from        = [];
      $to          = [];
      foreach ($fy_months as $fy_month){
        $from_date    = $fy_month['from_date'];
        $to_date      = $fy_month['to_date'];
        $month        = $fy_month['month_short'];
        $within_due[] = round($this->AppCommonModel->get_trade_total(22, $to_date, '', false, $to_date,$company_id,$comp_fy_id,$comp_code,$boid));
        $overdue[]    = round($this->AppCommonModel->get_trade_total(22, '', $to_date, false, $to_date,$company_id,$comp_fy_id,$comp_code,$boid));
        $advances[]   = round($this->AppCommonModel->get_trade_total(16, '', '', true, $to_date,$company_id,$comp_fy_id,$comp_code,$boid));
																 
        $months[]     = $month;
        $from[]       = $from_date;
        $to[]         = $to_date;
       }
      $data = array('months' => $months, 'overdue' => $overdue, 'within_due' => $within_due, 'advances' => $advances, 'from_date' => $from, 'to_date' => $to);
   	  if($data){
		$return_data['success'] = '1';
		$return_data['info'] = $data;	
		}
	  else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}
	  return $return_data;
	}
	
	function update_communication_contact($postinfo){
	   $uuid = $this->session->get('uuid_aicountly');
	   // check if any user registered on aicountly with changed email or wa mobile or mobile
	   // then no need to change uuid tables just update in contacts tables
	   // if no user exists then create first uuid with status code 3 and then add entry in contacts
	   $email  = $postinfo['email'];
	   $mobile = $postinfo['mobile'];
	   
	   $data1  = $this->AppCommonModel->search_by_email($uuid,$email);
	   if(empty($data1)){
		  // means fresh email id then create uuid with status 3 
		$uusdata = [
            'uuid_regdemail'    => $email,
            'uuid_regdmobile'   => '',
            'uuid_regdwa'       => '',
            'uuid_project'      => 1,
        ]; 
        
        $this->sispl_uuid_db->table('sispluuid_uuidgenert_univdb')->insert($uusdata);
        $uuid = $this->sispl_uuid_db->insertID();
		$useraictly_data = [
            'uuid'                          => $uuid,
            'user_firstname'                => '',
            'user_midname'                  => '',
            'user_lastname'                 => '',
            'user_name'                     => '',
            'user_pass'                     => '',
            'user_regdmobile'               => '',
            'user_regdemail'                => $email,
            'user_wamobile'                 => '',
            'user_aicountly_business_id'    => '',
            'user_pin'                      => '',
            'user_gender'                   => '',
            'user_type_profs'               => '',
            'user_dob'                      => '',
            'auth_code'                     => '',
            'auth_time'                     => '', 
            'useraictly_status'             => 3,
           ];
		$this->aicountly_db->table('aicountly_useraictly_univdb')->insert($useraictly_data);
		$useraicdet_tbl  =array("uuid"=>$uuid,
								"user_email2"=>$postinfo['second_email'],"user_mobile2"=>$postinfo['second_mobile'],"user_landline"=>$postinfo['landline'],
								"user_website"=>$postinfo['website'],"user_add1"=>"","user_add2"=>"",
								"user_city"=>"","user_state"=>"","user_country"=>"");
		$this->aicountly_db->table('aicountly_useraicdet_univdb')->insert($useraicdet_tbl);						
       }
	   $data2  = $this->AppCommonModel->MobileExists($uuid,$mobile);
		if(empty($data2)){
		  // means fresh mobile then create uuid with status 3 
		$uudata = [
            'uuid_regdemail'    => '',
            'uuid_regdmobile'   => $mobile,
            'uuid_regdwa'       => $mobile,
            'uuid_project'      => 1,
            ]; 
        
        $this->sispl_uuid_db->table('sispluuid_uuidgenert_univdb')->insert($uudata);
        $uuid = $this->sispl_uuid_db->insertID();
		$useraictly_data = [
            'uuid'                          => $uuid,
            'user_firstname'                => '',
            'user_midname'                  => '',
            'user_lastname'                 => '',
            'user_name'                     => '',
            'user_pass'                     => '',
            'user_regdmobile'               => $mobile,
            'user_regdemail'                => '',
            'user_wamobile'                 => $mobile,
            'user_aicountly_business_id'    => '',
            'user_pin'                      => '',
            'user_gender'                   => '',
            'user_type_profs'               => '',
            'user_dob'                      => '',
            'auth_code'                     => '',
            'auth_time'                     => '', 
            'useraictly_status'             => 3,
           ];
		$this->aicountly_db->table('aicountly_useraictly_univdb')
                           ->insert($useraictly_data); 
		$useraicdet_tbl  =array("uuid"=>$uuid,
								"user_email2"=>$postinfo['second_email'],"user_mobile2"=>$postinfo['second_mobile'],"user_landline"=>$postinfo['landline'],
								"user_website"=>$postinfo['website'],"user_add1"=>"","user_add2"=>"",
								"user_city"=>"","user_state"=>"","user_country"=>"");
		$this->aicountly_db->table('aicountly_useraicdet_univdb')->insert($useraicdet_tbl);
		
	     }
	   $updata = array("contact_uuid"=>$postinfo['uuid'],"contact_firstname"=>$postinfo['first_name'],"contact_lastname"=>$postinfo['last_name'],
						"contact_email"=>$postinfo['email'],"contact_mobile"=>$postinfo['mobile'],"contact_wamobile"=>$postinfo['wamobile'],
						"contact_email2"=>$postinfo['second_email'],"contact_mobile2"=>$postinfo['second_mobile'],"contact_landline"=>$postinfo['landline'],
						"contact_website"=>$postinfo['website'],"contact_cat_id"=>0);
	   $this->contactaic_db->table('contactaic_mycontacts_univdb')
                            ->where('contact_id', $postinfo['contact_id'])
                            ->update($updata);	
	$return_data['success'] = '1';
    $return_data['message'] = 'Updated';	
	return $return_data;						
		
	}
	
	function delete_contact($postinfo){
		$contact = $this->AppCommonModel->get_contact_info($postinfo['contact_id']);
		if($contact){
		$uuid         = $contact['uuid'];
        $contact_uuid = $contact['contact_uuid'];	
		$this->contactaic_db->table('contactaic_contactaic_univdb2')
                            ->where('uuid', $uuid)
                            ->where('contact_uuid', $contact_uuid)
                            ->delete();
        $this->contactaic_db->table('contactaic_contactaic_univdb2')
                             ->where('uuid', $contact_uuid)
                            ->where('contact_uuid', $uuid)
                            ->delete();	
		$return_data['success'] = '1';
		$return_data['message'] = 'Deleted';	
		}else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}
		return $return_data;
	}
	
	function add_category($postinfo){
	 $exists = $this->contactaic_db->table('contactaic_contaiccat_univdb')->where('LOWER(contact_category)',strtolower($postinfo['category_name']))->where('uuid',$postinfo['uuid'])->get()->getRowArray(); 
      if($exists){
        $return_data['success'] = '0';
        $return_data['message'] = 'Name Exists';
    	}
      else{
		$data = array(
                    'uuid'                     => $postinfo['uuid'],
                    'contact_category'         => $postinfo['category_name'],
                    'contact_category_color'   => $postinfo['category_color']
                   );  
        $this->contactaic_db->table('contactaic_contaiccat_univdb')->insert($data);
		$return_data['success'] = '1';
        $return_data['message'] = 'Added';					
	   }	
		
	return $return_data; 	
	}
	
	function update_category($postinfo){
		$data = array(
                    'contact_category'         => $postinfo['category_name'],
                    'contact_category_color'   => $postinfo['category_color']
                   );  
         $this->contactaic_db->table('contactaic_contaiccat_univdb')
                            ->where('contact_cat_id', $postinfo['category_id'])
                            ->update($data);
		$return_data['success'] = '1';
        $return_data['message'] = 'Updated';					
	 
	    return $return_data;	
	}
	
	function delete_category($postinfo){
		$category = $this->AppCommonModel->get_category_by_id($postinfo['category_id']);
		if($category){
		$this->contactaic_db->table('contactaic_contaiccat_univdb')
                            ->where('contact_cat_id', $postinfo['category_id'])
                            ->delete();
		$return_data['success'] = '1';
		$return_data['message'] = 'Deleted';	
		}else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}
		return $return_data;
	}
	
	function update_address_contact($postinfo){
	 $exists = $this->contactaic_db->table('contactaic_mycontaddr_univdb ')
               ->where('contact_id', $postinfo['contact_id'])
               ->get()->getRowArray();
      if($exists){
        $this->contactaic_db->table('contactaic_mycontaddr_univdb ')
                            ->where('contact_id', $postinfo['contact_id'])
                            ->update($postinfo);
    	}
      else{
        $this->contactaic_db->table('contactaic_mycontaddr_univdb ')
                            ->insert($postinfo);
	   }	
	$return_data['success'] = '1';
    $return_data['message'] = 'Updated';	
	return $return_data;	
	}
		
	function GetUserContactsTotal($postinfo){
		$builder = $this->contactaic_db->table('contactaic_contactaic_univdb2');
        $builder->select('contactaic_contactaic_univdb2.*, contactaic_contactaic_univdb2.contact_id, contactaic_contactaic_univdb2.contact_uuid');
        $builder->join('contactaic_mycontacts_univdb', 'contactaic_mycontacts_univdb.contact_id = contactaic_contactaic_univdb2.contact_id');
        $builder->select('contact_firstname, contact_lastname, CONCAT(contact_firstname," ",contact_lastname) as contact_name, contact_email, contact_mobile');		
		$builder->where('contactaic_contactaic_univdb2.uuid',$postinfo['uuid']);
        if($postinfo['cat_id'] >0){
		$builder->like('contactaic_contactaic_univdb2.contact_cat_id',$postinfo['cat_id']); 	
		}
		$result = $builder->get()->getResultArray();
		if($result)
		return count($result);
	    return 0;
	}
	
	
    function GetUserContacts($postinfo,$start, $limit){
		$builder = $this->contactaic_db->table('contactaic_contactaic_univdb2');
        $builder->select('contactaic_contactaic_univdb2.*, contactaic_contactaic_univdb2.contact_id, contactaic_contactaic_univdb2.contact_uuid');
        $builder->join('contactaic_mycontacts_univdb', 'contactaic_mycontacts_univdb.contact_id = contactaic_contactaic_univdb2.contact_id');
        $builder->select('contact_firstname, contact_lastname, CONCAT(contact_firstname," ",contact_lastname) as contact_name, contact_email, contact_mobile');		
		$builder->where('contactaic_contactaic_univdb2.uuid',$postinfo['uuid']);
        if($postinfo['cat_id'] >0){
		$builder->like('contactaic_contactaic_univdb2.contact_cat_id',$postinfo['cat_id']); 	
		}
		$builder->limit($limit,$start);	
		$result = $builder->get()->getResultArray();
		if($result){		   	
		    $return_data['success'] = '1';
			$return_data['listings'] = $result;
		}		
		else{
			$return_data['success'] = '0';	
			$return_data['listings'] = array();			
		  }
		  return $return_data;
	}		
 
}