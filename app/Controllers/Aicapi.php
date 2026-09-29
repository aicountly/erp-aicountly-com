<?php
namespace App\Controllers;
use App\Models\ApiModel;
use App\Models\AppCommonModel;
use App\Libraries\externaldb;

class Aicapi extends BaseController
{
	public function __construct(){
	 helper(['form', 'url', 'mail_helper']);
	 $this->ApiModel = new ApiModel();
	 $this->AppCommonModel= new AppCommonModel();
	 $this->ListingPerPage=10;
	}
	
	function array2json($array){
		 ini_set('precision', 10);
         ini_set('serialize_precision', 10);
	
			ob_start('ob_gzhandler');
			return json_encode($array,JSON_UNESCAPED_SLASHES);
		
		}
			 ///////////						///////////			
		     	  /////  CONTACTS API LISTS  //////
			//////////                          ////////// 
	function ValidateUserLogin(){ 
	 if($this->request->getMethod() == 'post'){		 
		$username     = $this->request->getVar('LoginName');
		$password     = $this->request->getVar('Password');
		$postinfo     = array("username"=>$username,"password"=>$password);		 
		$output_array = $this->ApiModel->login($postinfo); 
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	    }else{
		 echo "Method not exists";			
		}
	 die();	
	}
	
	
	function PageDetail(){
	  if($this->request->getMethod() == 'post'){	
             $page_name          = $this->request->getVar('page_name');
			 $postinfo     = array("page_name"=>$page_name);		 
			 $output_array = $this->ApiModel->page_info($postinfo); 
			 header('Content-type: text/html');
			 echo $output_array; 
			 die();	
			
	    }else{
		 echo "Method not exists";			
		}
	    die();  
	    
	}
	
	function GoogleLogin(){
	  if($this->request->getMethod() == 'post'){	
             $email_id     = $this->request->getVar('email_id');
             $full_name    = $this->request->getVar('full_name');
			 $postinfo     = array("email_id"=>$email_id,"full_name"=>$full_name);		 
			 $output_array = $this->ApiModel->google_login_info($postinfo); 
			 header('Content-type: text/json');
         	 echo $this->array2json($output_array); 
         	 die();
			
	    }else{
		 echo "Method not exists";			
		}
	    die();  
	    
	}
	
	function UserProfile(){ 
	 if($this->request->getMethod() == 'post'){	
        $uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
			if(trim($actual_token)!=trim($token)){
				$output_array['success'] = '0';	
				$output_array['message'] = 'Token Mismatch';	
				$output_array['listings'] = array();	
				header('Content-type: text/json');
				echo $this->array2json($output_array); 
				die();
			}
			else{
			 $uuid     = $this->request->getVar('uuid');
			 $postinfo     = array("uuid"=>$uuid);		 
			 $output_array = $this->ApiModel->profile_info($postinfo); 
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			 }
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}
	
	
	
	function UserCompanies(){ 
	 if($this->request->getMethod() == 'post'){	
        $uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
			if(trim($actual_token)!=trim($token)){
				$output_array['success'] = '0';	
				$output_array['message'] = 'Token Mismatch';	
				$output_array['listings'] = array();	
				header('Content-type: text/json');
				echo $this->array2json($output_array); 
				die();
			}
			else{
			 $uuid     = $this->request->getVar('uuid');
			 $postinfo     = array("uuid"=>$uuid);		 
			 $output_array = $this->ApiModel->user_company_lists($postinfo); 
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			 }
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}
	
	function ContactDetail(){ 
	 if($this->request->getMethod() == 'post'){	
        $uuid          = $this->request->getVar('uuid');
		$contact_id    = $this->request->getVar('contact_id');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
			if(trim($actual_token)!=trim($token)){
				$output_array['success'] = '0';	
				$output_array['message'] = 'Token Mismatch';	
				$output_array['listings'] = array();	
				header('Content-type: text/json');
				echo $this->array2json($output_array); 
				die();
			}
			else{
			 $uuid         = $this->request->getVar('uuid');
			 $postinfo     = array("uuid"=>$uuid,"contact_id"=>$contact_id);		 
			 $output_array = $this->ApiModel->contact_info($postinfo); 
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			 }
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}
	
	function AddCategory(){ 
	 if($this->request->getMethod() == 'post'){		 
		$category_name  = $this->request->getVar('category_name');
		$category_color = $this->request->getVar('category_color');
		$uuid           = $this->request->getVar('uuid');
		$token          = $this->AppCommonModel->token($uuid);
		$utokeninfo     = unobfuscate_link($this->request->getVar('token'));
		$actual_token   = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{
		$postinfo     = array("category_name"=>$category_name,"category_color"=>$category_color,'uuid'=>$uuid);		 
		$output_array = $this->ApiModel->add_category($postinfo); 
		
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	    }}else{
		 echo "Method not exists";			
		}
	 die();	
	}
	function UpdateCategory(){ 
	 if($this->request->getMethod() == 'post'){		 
	    $category_id    = $this->request->getVar('category_id');
		$category_name  = $this->request->getVar('category_name');
		$category_color = $this->request->getVar('category_color');
		$uuid           = $this->request->getVar('uuid');
		$token          = $this->AppCommonModel->token($uuid);
		$utokeninfo     = unobfuscate_link($this->request->getVar('token'));
		$actual_token   = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{
		$postinfo     = array("category_id"=>$category_id,"category_name"=>$category_name,"category_color"=>$category_color,'uuid'=>$uuid);		 
		$output_array = $this->ApiModel->update_category($postinfo); 
		
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	    }}else{
		 echo "Method not exists";			
		}
	 die();	
	}
	
	function DeleteCategory(){ 
	 if($this->request->getMethod() == 'post'){
		$category_id   = $this->request->getVar('category_id');
		$uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{		 
		$postinfo     = array("category_id"=>$category_id);
		$output_array = $this->ApiModel->delete_category($postinfo); 
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	     } }else{
		 echo "Method not exists";			
		}
	 die();	
	}
	
	function ContactCategories(){ 
	 if($this->request->getMethod() == 'post'){	
        $uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
			if(trim($actual_token)!=trim($token)){
				$output_array['success'] = '0';	
				$output_array['message'] = 'Token Mismatch';	
				$output_array['listings'] = array();	
				header('Content-type: text/json');
				echo $this->array2json($output_array); 
				die();
			}
			else{
			 $uuid         = $this->request->getVar('uuid');
			 $postinfo     = array("uuid"=>$uuid);		 
			 $output_array = $this->ApiModel->contact_categories($postinfo); 
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			 }
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}
	
	function CountryStates(){ 
	 if($this->request->getMethod() == 'post'){	
        $uuid          = $this->request->getVar('uuid');
		$country_id    = $this->request->getVar('country_id');
			 
			 $postinfo     = array("uuid"=>$uuid,"country_id"=>$country_id);		 
			 $output_array = $this->ApiModel->country_states($postinfo); 
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}
	
	function CountryLists(){ 
	 if($this->request->getMethod() == 'post'){	
        $uuid          = $this->request->getVar('uuid');
		
			 
			 $postinfo     = array("uuid"=>$uuid);		 
			 $output_array = $this->ApiModel->country_lists($postinfo); 
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
		
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}
	
	function AddContact(){ 
	 if($this->request->getMethod() == 'post'){		 
		$email_id      = $this->request->getVar('email_id');
		$first_name    = $this->request->getVar('first_name');
		$last_name     = $this->request->getVar('last_name');
		$mobile_no     = $this->request->getVar('mobile_no');
		$wa_mobile     = $this->request->getVar('wa_mobile');
		$uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{
		$postinfo     = array("email_id"=>$email_id,"first_name"=>$first_name,
		                      "last_name"=>$last_name,"mobile_no"=>$mobile_no,
							  "wa_mobile"=>$wa_mobile,'uuid'=>$uuid);		 
		$output_array = $this->ApiModel->add_contact($postinfo); 
		
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	    }}else{
		 echo "Method not exists";			
		}
	 die();	
	}
	
	function UpdateContact(){ 
	 if($this->request->getMethod() == 'post'){		 
	    $update_type  = $this->request->getVar('update_type');
		
		$uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$mark_fav      = $this->request->getVar('mark_fav');
		$contact_id   = $this->request->getVar('contact_id');
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{
		
		if($update_type=='primary'){
		 $company_name = $this->request->getVar('company_name');
		 $job_title    = $this->request->getVar('job_title');
		 $categories   = $this->request->getVar('categories');
		 $notes        = $this->request->getVar('notes');
		 
		  if(!empty($categories)){
			    $contact_cat_id = $categories;
		  }
            else
                $contact_cat_id = ''; 	
		 $postinfo     = array("company_name"=>$company_name,"job_title"=>$job_title,
		                       "contact_cat_id"=>$contact_cat_id,
							   "notes"=>$notes,'uuid'=>$uuid,"mark_fav"=>$mark_fav,
							   "contact_id"=>$contact_id);	
		 $output_array = $this->ApiModel->update_primary_contact($postinfo); 		
		}
		if($update_type=='communication'){
		 $first_name     = $this->request->getVar('first_name');
		 $last_name      = $this->request->getVar('last_name');
		 $email          = $this->request->getVar('email');
		 $mobile         = $this->request->getVar('mobile');
		 $wamobile       = $this->request->getVar('wamobile');
		 $landline       = $this->request->getVar('landline');		 
         $second_email   = $this->request->getVar('second_email');
	     $second_mobile  = $this->request->getVar('second_mobile');
		 $website        = $this->request->getVar('website');
		 $postinfo       = array("first_name"=>$first_name,"last_name"=>$last_name,
		                         "email"=>$email,"mobile"=>$mobile,
							     "wamobile"=>$wamobile,'landline'=>$landline,
								 "second_email"=>$second_email,"second_mobile"=>$second_mobile,
								 "website"=>$website,'uuid'=>$uuid,"contact_id"=>$contact_id);		 
		$output_array = $this->ApiModel->update_communication_contact($postinfo); 	
		}
		if($update_type=='address'){
		 $address1      = $this->request->getVar('address1');
		 $address2      = $this->request->getVar('address2');
		 $country       = $this->request->getVar('country');
		 $state         = $this->request->getVar('state');
		 $pincode       = $this->request->getVar('pincode');
		 $city          = $this->request->getVar('city');		 
         $address_type  = $this->request->getVar('address_type');
	     $default       = $this->request->getVar('default'); //y or n
		 
		 $postinfo     = array("contact_add1"=>$address1,"contact_add2"=>$address2,
		                      "contact_city"=>$city,"contact_state"=>$state,
							  "contact_country"=>$country,'contact_pin'=>$pincode,
							  "contact_addtype"=>$address_type,"contact_id"=>$contact_id,
							  "contact_default"=>$default);		
		 $output_array = $this->ApiModel->update_address_contact($postinfo); 

			
		}
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();		
	   }
   	  }else{
		 echo "Method not exists";			
	 }
	 die();	
	}
	
	function DeleteContact(){ 
	 if($this->request->getMethod() == 'post'){
		$contact_id    = $this->request->getVar('contact_id');
		$uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{		 
		$postinfo     = array("contact_id"=>$contact_id);
		$output_array = $this->ApiModel->delete_contact($postinfo); 
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	     } }else{
		 echo "Method not exists";			
		}
	 die();	
	}
	
	function UserContactsList(){
	 if($this->request->getMethod() == 'post'){	
	    $limit = $this->ListingPerPage;	
		$page  = $this->request->getVar('page');
		$cat_id  = $this->request->getVar('cat_id');
    	if ($page) $start = ($page - 1) * $limit;
		else $start = '0';
		
		$uuid         = $this->request->getVar('uuid');
		$token        = $this->AppCommonModel->token($uuid);
		
		$search       = $this->request->getVar('search_txt');
		$utokeninfo   = unobfuscate_link($this->request->getVar('token'));
		$actual_token = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}else{
		$postinfo     = array("uuid"=>$uuid,"search"=>$search,"cat_id"=>$cat_id);		 
		
		$total_contacts = $this->ApiModel->GetUserContactsTotal($postinfo);
		$total_pages    = ceil($total_contacts / $limit);
		
		$output_array = $this->ApiModel->GetUserContacts($postinfo,$start, $limit);		
		$output_array['total_pages']=$total_pages;
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	    }}else{
		 echo "Method not exists";			
		}
	 die();	
	}
	
			///////////						     ///////////			
			   //////////      ERP API LISTS  //////////
			//////////                          ////////// 
	
    function CompanyInfo(){ 
	 if($this->request->getMethod() == 'post'){
		$company_id    = $this->request->getVar('comp_id');
		$uuid          = $this->request->getVar('uuid');
		$comp_code     = $this->request->getVar('comp_code');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{		 
		$postinfo     = array("company_id"=>$company_id,"uuid"=>$uuid,"comp_code"=>$comp_code);
		$output_array = $this->ApiModel->companyselection($postinfo); 
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	     } }else{
		 echo "Method not exists";			
		}
	 die();	
	}
	
  function CompanyFYChange(){ 
	 if($this->request->getMethod() == 'post'){
		$company_id    = $this->request->getVar('comp_id');
		$uuid          = $this->request->getVar('uuid');
		$comp_code     = $this->request->getVar('comp_code');
		$comp_fy_id    = $this->request->getVar('comp_fy_id');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{		 
		$postinfo     = array("company_id"=>$company_id,"uuid"=>$uuid,"comp_code"=>$comp_code,"comp_fy_id"=>$comp_fy_id);
		$output_array = $this->ApiModel->company_fy_selection($postinfo); 
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	     } }else{
		 echo "Method not exists";			
		}
	 die();	
	}
  function CompanyBOChange(){ 
	 if($this->request->getMethod() == 'post'){
		$company_id    = $this->request->getVar('comp_id');
		$uuid          = $this->request->getVar('uuid');
		$comp_code     = $this->request->getVar('comp_code');
		$comp_fy_id    = $this->request->getVar('comp_fy_id');
		$bo_id         = $this->request->getVar('bo_id');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{		 
		$postinfo     = array("company_id"=>$company_id,"uuid"=>$uuid,"comp_code"=>$comp_code,"comp_fy_id"=>$comp_fy_id,"bo_id"=>$bo_id);
		$output_array = $this->ApiModel->company_bo_selection($postinfo); 
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	     } }else{
		 echo "Method not exists";			
		}
	 die();	
	}	
	
  function ItemUnits(){ 
	 if($this->request->getMethod() == 'post'){
		$company_id    = $this->request->getVar('comp_id');
		$uuid          = $this->request->getVar('uuid');
		$comp_code     = $this->request->getVar('comp_code');
		$comp_fy_id    = $this->request->getVar('comp_fy_id');
		$item_id       = $this->request->getVar('item_id');
		$boid          = $this->request->getVar('boid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{		 
		$postinfo     = array("company_id"=>$company_id,"uuid"=>$uuid,"comp_code"=>$comp_code,"comp_fy_id"=>$comp_fy_id,"item_id"=>$item_id,"bo_id"=>$boid);
		$output_array = $this->ApiModel->ItemUnits($postinfo); 
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	     } }else{
		 echo "Method not exists";			
		}
	 die();	
	}
	
  function CompanyFYLists(){ 
	 if($this->request->getMethod() == 'post'){
		$company_id    = $this->request->getVar('comp_id');
		$uuid          = $this->request->getVar('uuid');
		$comp_code     = $this->request->getVar('comp_code');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{		 
		$postinfo     = array("company_id"=>$company_id,"uuid"=>$uuid,"comp_code"=>$comp_code);
		$output_array = $this->ApiModel->GetCompFYLists($postinfo); 
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	     } }else{
		 echo "Method not exists";			
		}
	 die();	
	}	
	
  function BranchLists(){ 
	 if($this->request->getMethod() == 'post'){
		$company_id    = $this->request->getVar('comp_id');
		$uuid          = $this->request->getVar('uuid');
		$comp_code     = $this->request->getVar('comp_code');
		$comp_fy_id    = $this->request->getVar('comp_fy_id');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{		 
		$postinfo     = array("company_id"=>$company_id,"uuid"=>$uuid,"comp_code"=>$comp_code,"comp_fy_id"=>$comp_fy_id);
		$output_array = $this->ApiModel->BranchLists($postinfo); 
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	     } }else{
		 echo "Method not exists";			
		}
	 die();	
	}	
	
  function AllMcs(){ 
	 if($this->request->getMethod() == 'post'){
		$company_id    = $this->request->getVar('comp_id');
		$uuid          = $this->request->getVar('uuid');
		$comp_code     = $this->request->getVar('comp_code');
		$comp_fy_id    = $this->request->getVar('comp_fy_id');
		$boid          = $this->request->getVar('boid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{		 
		$postinfo     = array("company_id"=>$company_id,"uuid"=>$uuid,"comp_code"=>$comp_code,"comp_fy_id"=>$comp_fy_id,"bo_id"=>$boid);
		$output_array = $this->ApiModel->McDropdown($postinfo); 
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	     } }else{
		 echo "Method not exists";			
		}
	 die();	
	}

  function AllMcsGroup(){ 
	 if($this->request->getMethod() == 'post'){
		$company_id    = $this->request->getVar('comp_id');
		$uuid          = $this->request->getVar('uuid');
		$comp_code     = $this->request->getVar('comp_code');
		$comp_fy_id    = $this->request->getVar('comp_fy_id');
		$boid          = $this->request->getVar('boid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{		 
		$postinfo     = array("company_id"=>$company_id,"uuid"=>$uuid,"comp_code"=>$comp_code,"comp_fy_id"=>$comp_fy_id,"bo_id"=>$boid);
		$output_array = $this->ApiModel->McGroupDropdown($postinfo); 
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	     } }else{
		 echo "Method not exists";			
		}
	 die();	
	}	
	
  function AccountGroups(){ 
	 if($this->request->getMethod() == 'post'){
		$limit = $this->ListingPerPage;	
		$page  = $this->request->getVar('page');
    	if ($page) $start = ($page - 1) * $limit;
		else $start = '0'; 
		$company_id    = $this->request->getVar('comp_id');
		$uuid          = $this->request->getVar('uuid');
		$comp_fy_id    = $this->request->getVar('comp_fy_id');
		$comp_code     = $this->request->getVar('comp_code');
		$boid          = $this->request->getVar('boid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{	
        $postinfo     = array("boid"=>$boid,"company_id"=>$company_id,"comp_fy_id"=>$comp_fy_id,"comp_code"=>$comp_code);
		$total_groups = $this->ApiModel->GetAccountGroupTotal($postinfo);
		$total_pages  = ceil($total_groups / $limit);
		$output_array = $this->ApiModel->AccountGroups($postinfo,$start, $limit); 
		header('Content-type: text/json');
		$output_array['total_pages']=$total_pages;
		echo $this->array2json($output_array); 
		die();
	     } }else{
		 echo "Method not exists";			
		}
	 die();	
	}
	
	function ReceivableInfo(){ 
	 if($this->request->getMethod() == 'post'){
		$company_id    = $this->request->getVar('comp_id');
		$uuid          = $this->request->getVar('uuid');
		$comp_fy_id    = $this->request->getVar('comp_fy_id');
		$comp_code     = $this->request->getVar('comp_code');
		$boid          = $this->request->getVar('boid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{		 
		$postinfo     = array("boid"=>$boid,"company_id"=>$company_id,"comp_fy_id"=>$comp_fy_id,"comp_code"=>$comp_code);
		$output_array = $this->ApiModel->receivableInfo($postinfo); 
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	     } }else{
		 echo "Method not exists";			
		}
	 die();	
	}
   
	function DayBook(){ 
	 if($this->request->getMethod() == 'post'){	
	    $limit = $this->ListingPerPage;	
		$page  = $this->request->getVar('page');
    	if ($page) $start = ($page - 1) * $limit;
		else $start = '0'; 
        $uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
			if(trim($actual_token)!=trim($token)){
				$output_array['success'] = '0';	
				$output_array['message'] = 'Token Mismatch';	
				$output_array['listings'] = array();	
				header('Content-type: text/json');
				echo $this->array2json($output_array); 
				die();
			}
			else{
			 $fromdate     = $this->request->getVar('fromdate');
			 $todate       = $this->request->getVar('todate');
			 $company_id   = $this->request->getVar('comp_id');
			 $comp_fy_id   = $this->request->getVar('comp_fy_id');
			 $bo_id        = $this->request->getVar('boid');
			 $comp_code    = $this->request->getVar('comp_code');
			 $postinfo     = array("todate"=>$todate,"fromdate"=>$fromdate,'company_id'=>$company_id,
			                       "comp_fy_id"=>$comp_fy_id,"bo_id"=>$bo_id,
								   "comp_code"=>$comp_code);			 
			 $total_records  = $this->ApiModel->GetDayBookTotal($postinfo);
		     $total_pages    = ceil($total_records / $limit);
			 $output_array   = $this->ApiModel->GetDayBook($postinfo,$start, $limit); 
			 $output_array['total_pages']=$total_pages;
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			 }
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}

   function AccountLedger(){ 
	 if($this->request->getMethod() == 'post'){	
	    $limit = $this->ListingPerPage;	
		$page  = $this->request->getVar('page');
    	if ($page) $start = ($page - 1) * $limit;
		else $start = '0'; 
        $uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
			if(trim($actual_token)!=trim($token)){
				$output_array['success'] = '0';	
				$output_array['message'] = 'Token Mismatch';	
				$output_array['listings'] = array();	
				header('Content-type: text/json');
				echo $this->array2json($output_array); 
				die();
			}
			else{
			 $fromdate     = $this->request->getVar('fromdate');
			 $todate       = $this->request->getVar('todate');
			 $company_id   = $this->request->getVar('comp_id');
			 $comp_fy_id   = $this->request->getVar('comp_fy_id');
			 $bo_id        = $this->request->getVar('boid');
			 $comp_code    = $this->request->getVar('comp_code');
			 $account_id   = $this->request->getVar('account_id');
			 $postinfo     = array("todate"=>$todate,"fromdate"=>$fromdate,'company_id'=>$company_id,
			                       "comp_fy_id"=>$comp_fy_id,"bo_id"=>$bo_id,
								   "comp_code"=>$comp_code,"account_id"=>$account_id);			 
			 
			 $total_info      = $this->ApiModel->GetAccountLedgerTotal($postinfo);
		     $opening_balance = $total_info['op_balance'];
			 $closing_balance = $total_info['cl_balance'];
			 $total_records   = $total_info['total_records'];
		     $total_pages     = ceil($total_records / $limit);
			 $output_array    = $this->ApiModel->GetAccountLedger($postinfo,$start, $limit); 
			 $output_array['total_pages']=$total_pages;
			 $output_array['opening_balance']=$opening_balance;
			  $output_array['closing_balance']=$closing_balance;
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			 }
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}

   function AccountGroupListing(){ 
	 if($this->request->getMethod() == 'post'){	
	    $limit = $this->ListingPerPage;	
		$page  = $this->request->getVar('page');
    	if ($page) $start = ($page - 1) * $limit;
		else $start = '0'; 
        $uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
			if(trim($actual_token)!=trim($token)){
				$output_array['success'] = '0';	
				$output_array['message'] = 'Token Mismatch';	
				$output_array['listings'] = array();	
				header('Content-type: text/json');
				echo $this->array2json($output_array); 
				die();
			}
			else{
			 $fromdate     = $this->request->getVar('fromdate');
			 $todate       = $this->request->getVar('todate');
			 $company_id   = $this->request->getVar('comp_id');
			 $comp_fy_id   = $this->request->getVar('comp_fy_id');
			 $bo_id        = $this->request->getVar('boid');
			 $comp_code    = $this->request->getVar('comp_code');
			 $group_id     = $this->request->getVar('group_id');
			 $parent_id    = $this->request->getVar('parent_id');
			 
			 $nill_balance     = $this->request->getVar('nill_balance');
			 $nill_transaction    = $this->request->getVar('nill_transaction');
			 
			 $postinfo     = array("todate"=>$todate,"fromdate"=>$fromdate,'company_id'=>$company_id,
			                       "comp_fy_id"=>$comp_fy_id,"boid"=>$bo_id,"parent_id"=>$parent_id,
								   "comp_code"=>$comp_code,"group_id"=>$group_id,"nill_balance"=>$nill_balance,
								   "nill_transaction"=>$nill_transaction
								   );			 
			 
			 //$total_info      = $this->ApiModel->GetAccountOrGroupListings($postinfo);
		     //$opening_balance = $total_info['op_balance'];
			 //$closing_balance = $total_info['cl_balance'];
			 //$total_records   = $total_info['total_records'];
		     //$total_pages     = ceil($total_records / $limit);
			 $output_array    = $this->ApiModel->GetAccountOrGroupListings($postinfo); 
			 //$output_array['total_pages']      = $total_pages;
			// $output_array['opening_balance']  = $opening_balance;
			 // $output_array['closing_balance'] = $closing_balance;
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			 }
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}	
	
   function AccountSummary(){ 
	 if($this->request->getMethod() == 'post'){		
	    $fromdate      = $this->request->getVar('fromdate');
		$todate        = $this->request->getVar('todate');
		$company_id    = $this->request->getVar('comp_id');
		$uuid          = $this->request->getVar('uuid');
		$comp_fy_id    = $this->request->getVar('comp_fy_id');
		$comp_code     = $this->request->getVar('comp_code');
		$boid          = $this->request->getVar('boid');
		$account_id    = $this->request->getVar('account_id');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
		if(trim($actual_token)!=trim($token)){
		    $output_array['success'] = '0';	
			$output_array['message'] = 'Token Mismatch';	
			$output_array['listings'] = array();	
			header('Content-type: text/json');
			echo $this->array2json($output_array); 
			die();
		}
		else{	
        $postinfo     = array("todate"=>$todate,"fromdate"=>$fromdate,"account_id"=>$account_id,"boid"=>$boid,"company_id"=>$company_id,"comp_fy_id"=>$comp_fy_id,"comp_code"=>$comp_code);
		$output_array = $this->ApiModel->GetAccountSummary($postinfo); 
		header('Content-type: text/json');
		
		echo $this->array2json($output_array); 
		die();
	     } }else{
		 echo "Method not exists";			
		}
	 die();	
	}  
			
  function StockLedger(){ 
	 if($this->request->getMethod() == 'post'){	
	    $limit = $this->ListingPerPage;	
		$page  = $this->request->getVar('page');
    	if ($page) $start = ($page - 1) * $limit;
		else $start = '0'; 
        $uuid          = $this->request->getVar('uuid');
		
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
			if(trim($actual_token)!=trim($token)){
				$output_array['success'] = '0';	
				$output_array['message'] = 'Token Mismatch';	
				$output_array['listings'] = array();	
				header('Content-type: text/json');
				echo $this->array2json($output_array); 
				die();
			}
			else{
			 $fromdate     = $this->request->getVar('fromdate');
			 $todate       = $this->request->getVar('todate');
			 $company_id   = $this->request->getVar('comp_id');
			 $comp_fy_id   = $this->request->getVar('comp_fy_id');
			 $bo_id        = $this->request->getVar('boid');
			 $comp_code    = $this->request->getVar('comp_code');
			 $item_id      = $this->request->getVar('item_id');
			 $unit_id      = $this->request->getVar('unit_id');
			 $mc_id        = $this->request->getVar('mc_id');
			 $mc_type      = $this->request->getVar('mc_type');
			 $val_method   = $this->request->getVar('val_method');			 
			 $mc_grp_id    = $this->request->getVar('mc_grp_id');
			 //all_mc,one_mc,mc_group
			 $postinfo     = array("todate"=>$todate,"fromdate"=>$fromdate,'company_id'=>$company_id,
			                       "comp_fy_id"=>$comp_fy_id,"bo_id"=>$bo_id,
								   "comp_code"=>$comp_code,"item_id"=>$item_id,
								   "unit_id"=>$unit_id,"mc_id"=>$mc_id,"val_method"=>$val_method,
								   "mc_type"=>$mc_type,"mc_grp_id"=>$mc_grp_id);

			$ItemExists           = $this->ApiModel->ItemExists($postinfo);							
			if($ItemExists >0){
			 $total_info           = $this->ApiModel->GetItemLedgerTotal($postinfo);
		     $amount_list          = $total_info['amount_list'];
			 $opening_balance_list = $total_info['opening_balance_list'];
			 $closing_balance_list = $total_info['closing_balance_list'];
			 $total_records   = $total_info['total_records'];
		     $total_pages     = ceil($total_records / $limit);
			 $output_array    = $this->ApiModel->GetItemLedger($postinfo,$start, $limit); 
			 $output_array['total_pages']=$total_pages;
			 $output_array['amount_list']=$amount_list;	
			 $output_array['opening_balance_list']=$opening_balance_list;	
			 $output_array['closing_balance_list']=$closing_balance_list;	
             $output_array['valuation_list'] = ['DEFAULT', 'AVG', 'FIFO', 'LIFO'];
			 }
			else{
			  $output_array['success'] = 0;
			  $output_array['message'] = 'Item not found';
			  $output_array['listings'] = [];
			}	
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			 }
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}
  function StockSummary(){ 
	 if($this->request->getMethod() == 'post'){	
	    $limit = $this->ListingPerPage;	
		$page  = $this->request->getVar('page');
    	if ($page) $start = ($page - 1) * $limit;
		else $start = '0'; 
        $uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
			if(trim($actual_token)!=trim($token)){
				$output_array['success'] = '0';	
				$output_array['message'] = 'Token Mismatch';	
				$output_array['listings'] = array();	
				header('Content-type: text/json');
				echo $this->array2json($output_array); 
				die();
			}
			else{
			 $company_id   = $this->request->getVar('comp_id');
			 $comp_fy_id   = $this->request->getVar('comp_fy_id');
			 $bo_id        = $this->request->getVar('boid');
			 $comp_code    = $this->request->getVar('comp_code');
			 $item_id      = $this->request->getVar('item_id');
			 $unit_id      = $this->request->getVar('unit_id');
			 $mc_id        = $this->request->getVar('mc_id');
			 $mc_type      = $this->request->getVar('mc_type');
			 $val_method   = $this->request->getVar('val_method');
			 $fromdate     = $this->request->getVar('fromdate');
			 $todate       = $this->request->getVar('todate');
			 $mc_grp_id    = $this->request->getVar('mc_grp_id');
			 
			 $postinfo     = array("todate"=>$todate,"fromdate"=>$fromdate,'company_id'=>$company_id,
			                       "comp_fy_id"=>$comp_fy_id,"bo_id"=>$bo_id,
								   "comp_code"=>$comp_code,"item_id"=>$item_id,
								   "unit_id"=>$unit_id,"mc_id"=>$mc_id,"val_method"=>$val_method,
								   "mc_type"=>$mc_type,"mc_grp_id"=>$mc_grp_id);			 
			 
			 
			 $ItemExists           = $this->ApiModel->ItemExists($postinfo);
			 if($ItemExists>0){
			 //all_mc,one_mc,mc_group
			 
			 $total_info           = $this->ApiModel->GetItemLedgerTotal($postinfo);
		 	 $opening_balance_list = $total_info['opening_balance_list'];
			 $closing_balance_list = $total_info['closing_balance_list'];
			 $output_array         = $this->ApiModel->GetItemSummary($postinfo,$start, $limit); 
			 $output_array['opening_balance_list']=$opening_balance_list;	
			 $output_array['closing_balance_list']=$closing_balance_list;	
             $output_array['valuation_list'] = ['DEFAULT', 'AVG', 'FIFO', 'LIFO'];
			 }
			else{
			$output_array['success'] = 0;
			$output_array['message'] = 'Item not found';
			$output_array['listings'] = [];
			}			 
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			 }
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}

function BalanceSheet(){ 
	 if($this->request->getMethod() == 'post'){	
        $uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
			if(trim($actual_token)!=trim($token)){
				$output_array['success'] = '0';	
				$output_array['message'] = 'Token Mismatch';	
				$output_array['listings'] = array();	
				header('Content-type: text/json');
				echo $this->array2json($output_array); 
				die();
			}
			else{
			 $company_id   = $this->request->getVar('comp_id');
			 $comp_fy_id   = $this->request->getVar('comp_fy_id');
			 $bo_id        = $this->request->getVar('boid');
			 $comp_code    = $this->request->getVar('comp_code');
			 $fromdate     = $this->request->getVar('fromdate');
			 $todate       = $this->request->getVar('todate');
			 $format         = $this->request->getVar('format');
			 $view         = $this->request->getVar('view');
			 $nil_type     = $this->request->getVar('nil_type');
			 
			 $postinfo     = array("todate"=>$todate,"fromdate"=>$fromdate,'company_id'=>$company_id,
			                       "comp_fy_id"=>$comp_fy_id,"bo_id"=>$bo_id,"format"=>$format,
								   "comp_code"=>$comp_code,"view"=>$view,"nil_type"=>$nil_type
								   );		 
			 $output_array = $this->ApiModel->balance_sheet_info($postinfo); 
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			 }
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}
function ProfitLoss(){ 
	 if($this->request->getMethod() == 'post'){	
        $uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
			if(trim($actual_token)!=trim($token)){
				$output_array['success'] = '0';	
				$output_array['message'] = 'Token Mismatch';	
				$output_array['listings'] = array();	
				header('Content-type: text/json');
				echo $this->array2json($output_array); 
				die();
			}
			else{
			 $company_id   = $this->request->getVar('comp_id');
			 $comp_fy_id   = $this->request->getVar('comp_fy_id');
			 $bo_id        = $this->request->getVar('boid');
			 $comp_code    = $this->request->getVar('comp_code');
			 $fromdate     = $this->request->getVar('fromdate');
			 $todate       = $this->request->getVar('todate');
			 $format       = $this->request->getVar('format');
			 $view         = $this->request->getVar('view');
			 $nil_type     = $this->request->getVar('nil_type');			 
			 $postinfo     = array("todate"=>$todate,"fromdate"=>$fromdate,'company_id'=>$company_id,
			                       "comp_fy_id"=>$comp_fy_id,"bo_id"=>$bo_id,"format"=>$format,
								   "comp_code"=>$comp_code,"view"=>$view,"nil_type"=>$nil_type
								   );		 
			 $output_array = $this->ApiModel->profit_loss_info($postinfo); 
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			 }
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}

public function TrialBalance(){ 
	 if($this->request->getMethod() == 'post'){	
        $uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
			if(trim($actual_token)!=trim($token)){
				$output_array['success'] = '0';	
				$output_array['message'] = 'Token Mismatch';	
				$output_array['listings'] = array();	
				header('Content-type: text/json');
				echo $this->array2json($output_array); 
				die();
			}
			else{
			 $company_id   = $this->request->getVar('comp_id');
			 $comp_fy_id   = $this->request->getVar('comp_fy_id');
			 $bo_id        = $this->request->getVar('boid');
			 $comp_code    = $this->request->getVar('comp_code');
			 $fromdate     = $this->request->getVar('fromdate');
			 $todate       = $this->request->getVar('todate');
			 $view         = $this->request->getVar('view');
			 $nil_type     = $this->request->getVar('nil_type');			 
			 $postinfo     = array("todate"=>$todate,"fromdate"=>$fromdate,'company_id'=>$company_id,
			                       "comp_fy_id"=>$comp_fy_id,"bo_id"=>$bo_id,
								   "comp_code"=>$comp_code,"view"=>$view,"nil_type"=>$nil_type
								   );		 
			 $output_array = $this->ApiModel->trial_balance_info($postinfo); 
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			 }
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}	
	
function StockStatus(){ 
	 if($this->request->getMethod() == 'post'){	
	    $limit = $this->ListingPerPage;	
		$page  = $this->request->getVar('page');
    	if ($page) $start = ($page - 1) * $limit;
		else $start = '0'; 
        $uuid          = $this->request->getVar('uuid');
		$token         = $this->AppCommonModel->token($uuid);
		$utokeninfo    = unobfuscate_link($this->request->getVar('token'));
		$actual_token  = '';
		if(isset($utokeninfo[1]))
			$actual_token = $utokeninfo['1'];
			if(trim($actual_token)!=trim($token)){
				$output_array['success'] = '0';	
				$output_array['message'] = 'Token Mismatch';	
				$output_array['listings'] = array();	
				header('Content-type: text/json');
				echo $this->array2json($output_array); 
				die();
			}
			else{
			 $company_id   = $this->request->getVar('comp_id');
			 $comp_fy_id   = $this->request->getVar('comp_fy_id');
			 $bo_id        = $this->request->getVar('boid');
			 $comp_code    = $this->request->getVar('comp_code');
			 $item_id      = $this->request->getVar('item_id');
			 $unit_id      = $this->request->getVar('unit_id');
			 $mc_id        = $this->request->getVar('mc_id');
			 $mc_type      = $this->request->getVar('mc_type');
			 $val_method   = $this->request->getVar('val_method');
			 $fromdate     = $this->request->getVar('fromdate');
			 $todate       = $this->request->getVar('todate');
			 $mc_grp_id    = 0;
			 //all_mc,one_mc,mc_group
			 $postinfo     = array("todate"=>$todate,"fromdate"=>$fromdate,'company_id'=>$company_id,
			                       "comp_fy_id"=>$comp_fy_id,"bo_id"=>$bo_id,
								   "comp_code"=>$comp_code,"item_id"=>$item_id,
								   "unit_id"=>$unit_id,"mc_id"=>$mc_id,"val_method"=>$val_method,
								   "mc_type"=>$mc_type,"mc_grp_id"=>$mc_grp_id);	
									
			 $ItemExists           = $this->ApiModel->ItemExists($postinfo);							
			 if($ItemExists >0){
			 $total_records   = $this->ApiModel->GetStockStatusTotal($postinfo);
		 	 $total_pages     = ceil($total_records / $limit);
			 $output_array    = $this->ApiModel->GetStockStatus($postinfo,$start, $limit); 
			 $output_array['total_pages']    = $total_pages;	
             $output_array['valuation_list'] = ['DEFAULT', 'AVG', 'FIFO', 'LIFO'];
			 }else{
			  $output_array['success'] = 0;
			  $output_array['message'] = 'Item not found';
			  $output_array['listings'] = [];
			  }
			 
			 header('Content-type: text/json');
			 echo $this->array2json($output_array); 
			 die();	
			 }
	    }else{
		 echo "Method not exists";			
		}
	    die();	
	}	
	
			 ///////////						///////////			
		     	  /////  REGISTER,FORGOT PASSWORD API LISTS  //////
			//////////                          ////////// 
	function ValidateUserSignup(){ 
	 if($this->request->getMethod() == 'post'){		 
		$FirstName       = $this->request->getVar('FirstName');
		$LastName        = $this->request->getVar('LastName');
		$RegMobile       = $this->request->getVar('RegMobile');
		$RegEmail        = $this->request->getVar('RegEmail');
		$RegWAMobile     = $this->request->getVar('RegWAMobile');
		$UserProfile     = $this->request->getVar('UserProfile');
		$Addrs1          = $this->request->getVar('Addrs1');
		$Addrs2          = $this->request->getVar('Addrs2');
		$Country         = $this->request->getVar('Country');
		$State           = $this->request->getVar('State');
		$PinCode         = $this->request->getVar('PinCode');
		$City            = $this->request->getVar('City');
		$Password        = $this->request->getVar('Password');
		$ConfirmPassword = $this->request->getVar('ConfirmPassword');
        $SendOTP         = $this->request->getVar('SendOTP');		
		$user_password   = password_hash($Password, PASSWORD_BCRYPT);
					
		$postinfo       = array("user_aicountly_business_id"=>"","user_midname"=>"","user_firstname"=>$FirstName,"user_lastname"=>$LastName,'user_regdmobile'=>$RegMobile,
		                     "user_name"=>$RegEmail,'user_regdemail'=>$RegEmail,'user_wamobile'=>$RegWAMobile,'user_type_profs'=>$UserProfile,
							 'Addrs1'=>$Addrs1,'Addrs2'=>$Addrs2,'Country'=>$Country,'State'=>$State,
							 'SendOTP'=>$SendOTP,'user_gender'=>'','user_dob'=>'','auth_code'=>'','auth_time'=>'','user_pin'=>$PinCode,'City'=>$City,'user_pass'=>$user_password,'Password'=>$Password,'ConfirmPassword'=>$ConfirmPassword);		 
		$output_array   = $this->ApiModel->register($postinfo); 
		
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	    }else{
		 echo "Method not exists";			
		}
	 die();	
	} 
	
   function ForgotUserPassword(){ 
	 if($this->request->getMethod() == 'post'){		 
		$RegEmail        = $this->request->getVar('RegEmail');		
		$postinfo       = array('user_regdemail'=>$RegEmail);		 
		$output_array   = $this->ApiModel->forgot_password($postinfo); 		
		header('Content-type: text/json');
		echo $this->array2json($output_array); 
		die();
	    }else{
		 echo "Method not exists";			
		}
	 die();	
	}		
			
}