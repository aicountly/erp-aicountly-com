<?php
namespace App\Controllers;

use App\Models\CompanyAccessModel;
use App\Libraries\externaldb;
use App\Models\Admin\BackupModel;
use App\Models\Admin\CommandModel;
use App\Models\Admin\AccountsModel;
use App\Models\Admin\ItemsModel;
use App\Models\Admin\BillsundryModel;
use App\Models\CommonModel;
use App\Helpers\AWSHelper;
use App\Libraries\auth_session;
use App\Models\Admin\CalendarModel;
use App\Traits\TransactionTrait;

class Home extends BaseController
{   use TransactionTrait;	
	public function __construct()
    {  
	 helper(['form', 'url','text','custom']);
	 $this->session 	         = \Config\Services::session();	 
	 $this->auth_session  = new auth_session();			
	 $this->auth_session->user_restrict();
	 $this->CommonModel          = new CommonModel();
	 $this->AccountsModel          = new AccountsModel();
	 $this->ItemsModel          = new ItemsModel();
	 $this->BillsundryModel          = new BillsundryModel();
	 $this->CompanyAccessModel   = new CompanyAccessModel();
	 $this->BackupModel          = new BackupModel();
	 $this->CommandModel         = new CommandModel();	  
	 $this->CalendarModel = new CalendarModel();
	 $this->folder_path          = getenv('AdminPath');
	 $this->base_url             = base_url();
	 $this->externaldb           = new externaldb(); 
	
	}
   
    public function upload_file()
	{	   
		$rules = [ 

			'user_logo_file' => [
				'rules'  => 'uploaded[user_logo_file]|ext_in[user_logo_file,png,jpg,jpeg]|max_size[user_logo_file,1024]',
				'errors' => [
					'uploaded' 	=> 'File is required',
					'ext_in' => 'File must be of type jpg, jpeg or png',
					'max_size' 	=> 'Size must be less than 1024kb'
			  ],
			]
		];

		if(!$this->validate($rules)){
			$errors = $this->validator->getErrors();
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
		}

		$user_id = $this->request->getVar('user_id');

		$file = $this->request->getFile('user_logo_file');
			if (!$file->isValid()) {
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Invalid File']]);
		}

		$file_ext = $file->getExtension();

		//(20) digit + (4 or 5)ext, max- 30
		$_name = 'logo_'.rand(1000,9999).'_'.time();
		$file_name = $_name.'.'.$file_ext;
		$original_size = $file->getSize();
		

    $folder_path = WRITEPATH.'uploads/user_logo_temp';
    if(!file_exists($folder_path)) {
		  mkdir($folder_path, 0755);
		}
    $file_path =  $folder_path.'/'.$file_name;
    $file->move($folder_path, $file_name);

    $destination = $folder_path.'/'.$_name.'.jpg';
    $convert_status = convert_jpg2($file_path);
    if($convert_status){
    	// $file_ext = 'jpg';
    	// $file_name = $_name.'.'.$file_ext;
    	// $file_path =  $folder_path.'/'.$file_name;
    }
    $reduced_size = filesize($file_path);

    // echo '<br>original_size '.$original_size;
    // echo '<br>reduced_size '.$reduced_size;
    // exit;
	       
    $bucketName = 'usersetup.aicountly.com.active';
    $s3Key = 'uploads/'.$file_name;

    $aws = new AWSHelper('https://s3.de.perf.cloud.ovh.net/');

    try {
      if(!$aws->uploadFile($bucketName, $file_path, $s3Key))
      {
      	unlink($file_path);
        return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Internal Error']]);
      }
    } catch (\Exception $e) {
    	unlink($file_path);
      return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$e->getMessage()]]);
    } 

    

    unlink($file_path);

    $data = [
        'doc_name' 				=> $file_name,
        'doc_size_org' 			=> $original_size,
        'doc_size_optimize' 	=> $reduced_size,
        'doc_module_shortcode' 	=> 'USRLOGO',
        'doc_status' 			=> 1,
        'doc_file_ext' 			=> $file_ext,
        'uuid' 					=> auth()->id,
        'comp_id'				=> comp()->id,
        'upload_date' 			=> date('Y-m-d'),
        'usr_config_id' 		=> 'ERP001000000'
    ];
    $doc_id = $this->CommonModel->saveUserDocument($data);

   
    $this->CommonModel->updateUserMaster($user_id,['logo' => $doc_id]);
    
    return json_encode(['status' => true, 'message' => 'File Uploaded']);
	}

  function logo($uuid = null)
	{
		if(!$uuid)
			$uuid = auth()->id;
		
		
	 	$file = $this->CommonModel->getUserLogo($uuid);
	
		if($file){
			$file_name = $file['name'];
			$file_ext = $file['ext'];

			$bucketName = 'usersetup.aicountly.com.active';
    	$s3Key = 'uploads/'.$file_name;

    	$aws = new AWSHelper('https://s3.de.perf.cloud.ovh.net/');

    	$response = $aws->download($bucketName, $s3Key);
    	if($response){
				$mime = mime($file_ext);

				header("Content-type: ".$mime);
				echo $response["Body"];
			}
		}
		exit;  
	}
	
    public function contacts()
    {
    	return redirect()->to('https://contacts.aicountly.com/');
    }

    
    public function my_account()
    { if(!$this->session->get('uuid'))
		 redirect(base_url());
		$this->create_mysession();
		return redirect()->to('https://my.aicountly.com/admin/home');
    }

    public function business()
    {	
     if(!$this->session->get('uuid'))
		 redirect(base_url());
		$this->create_mysession();
		return redirect()->to('https://my.aicountly.com/admin/business_id/edit');		
    }

    public function personal_info()
    { if(!$this->session->get('uuid'))
		 redirect(base_url());
		$this->create_mysession();
    	return redirect()->to('https://my.aicountly.com/admin/personal_info');
    }


   public function create_mysession()
	{
		   $data = $_SESSION;
	      /*$this->session->remove('ulogged_in');								
    	  $this->session->remove('uemail');
    	  $this->session->remove('uf_name');
    	  $this->session->remove('ul_name');
    	  $this->session->remove('uuid_aicountly');
    	  $this->session->remove('uuid');
    	  */
    	  
    	  $this->session->set('ulogged_in', TRUE);				
    	  $this->session->set('uuid_aicountly', $data['uuid']);
    	  $this->session->set('uuid', $data['uuid']);	
    	  $this->session->set('uemail',  $data['email']);
    	  $this->session->set('uf_name', $data['f_name']);		 
    	  $this->session->set('ul_name', $data['l_name']);
	}
	
	public  function index()
	 {
	   	$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url_path']    = $this->base_url;
	    return view('home',$data); 
	 } 

	 public  function maintainance()
	 {
	   
	    return view('maintainance'); 
	 } 

	 public function temp()
	 {
	 	$company_id = 1;
		$company_info          =  $this->CommonModel->get_company_info($company_id);	
		$company_fy_info       =  $this->CommonModel->get_company_max_fy_info($company_id);		  
		$comp_fy_id            =  $company_fy_info['comp_fy_id'];		
		$get_company_fy_info   =  $this->CommonModel->get_company_fy_info($company_id);

	 	$this->CommonModel->company_onloadchks($company_id,$comp_fy_id,$company_info['comp_code']);
	 }
	 
	 public  function ajax_select_branch($bo_id)
	 {   
	     if($this->session->get('ses_company_id'))
	       $company_id = $this->session->get('ses_company_id');
	       else
	       $company_id   =0;
	     
		  $adrs_info =  $this->CommonModel->get_comp_ho_adrs_info($company_id,$bo_id);
		  
		  if($adrs_info){
			 $bo_state_code = sprintf( '%02d',($adrs_info['state_code'])); 
			 $ses_boname    = $adrs_info['hobo_name']; 
			 $this->session->set('bo_gstin_type',$adrs_info['hobo_gstin_type']);	
			 $this->session->set('cmp_suply_type',$adrs_info['hobo_gstin_sub_type']);	
		  }else{
			  $bo_state_code = 0;
			  $ses_boname    ="";
			  $this->session->set('bo_gstin_type',1);
			  $this->session->set('cmp_suply_type',0);
		  }
		  
			$this->session->set('ses_boid',$bo_id);
			$this->session->set('ses_bostecd', sprintf( '%02d',$bo_state_code));
			$this->session->set('ses_boname',$ses_boname);
			
			$comp_fy_id = $this->session->get('ses_comp_fy_id');
			
			$accounts_list  = $this->AccountsModel->company_all_accounts();
			CreateJsonFile($comp_fy_id,$company_id,'acc',$accounts_list);
			
			
			$items_list  = $this->ItemsModel->company_all_items();
		    CreateJsonFile($comp_fy_id,$company_id,'itm',$items_list);
			
			$company_all_bsd  = $this->BillsundryModel->company_all_bsd();
			CreateJsonFile($comp_fy_id,$company_id,'bsd',$company_all_bsd);
			 echo '{"message":"Branch Selected","status":"1"}';
			 die();
	 }		 
	
    

	function add_voucher_transaction($comp_id,$voucher_type_id)
	{
		$this->ajax_select_company($comp_id,false);

		$addr = get_add_voucher_addr($voucher_type_id);
		$link = base_url().'admin/'.$addr.'?p=1';
		return redirect()->to($link);
	}

	function edit_voucher_transaction($comp_id,$comp_fy_id,$bo_id,$voucher_type_id,$voucher_txn_id)
	{
		
		$this->ajax_select_company($comp_id,false,$comp_fy_id,$bo_id);

		$addr = get_edit_voucher_addr($voucher_type_id);
		$link = base_url().'admin/'.$addr.$voucher_txn_id;
		return redirect()->to($link);
	}
	
   public function restore_company($compid_info){
     if(!$compid_info) 
       return redirect()->to(base_url().'admin/dashboard');
       
       $compid_info    = unobfuscate_link($compid_info);
       $company_id     = $compid_info['1'];
       $company_info   = $this->CommonModel->get_company_info($company_id);	
       
      if(!$company_info) 
       return redirect()->to(base_url().'admin/dashboard'); 
       
       $this->CommonModel->restore_company_info($company_id);
       return redirect()->to($this->base_url.'companies');
   	   die;    
        
    }
 
  public function finally_remove_comp($compid_info){
      if(!$compid_info) 
       return redirect()->to(base_url().'admin/dashboard');
       
       $compid_info    = unobfuscate_link($compid_info);
       $company_id     = $compid_info['1'];
       $company_info   = $this->CommonModel->get_company_info($company_id);	
       
       if(!$company_info) 
       return redirect()->to(base_url().'admin/dashboard');
       
       $this->CommonModel->remove_company_permanent($company_id);      
       return json_encode(['status' => true, 'message' => 'Company Deleted', 'reload' => 1]);
    }
    
   public function remove_comp($compid_info){
      if(!$compid_info) 
       return redirect()->to(base_url().'admin/dashboard');
       
       $compid_info    = unobfuscate_link($compid_info);
       $company_id     = $compid_info['1'];
       $company_info   = $this->CommonModel->get_company_info($company_id);	       
       if(!$company_info) 
       return redirect()->to(base_url().'admin/dashboard');
       
       $update_data    = array("cmp_id"=>$company_id,"cmp_recycle_date"=>date('Y-m-d'));       
       $this->CommonModel->recycle_company_info($company_id,$update_data);
       return redirect()->to($this->base_url.'companies');
   	   die;
    } 	
    
    public function ajax_all_companies(){	    
	   echo  $this->CommonModel->ajax_all_companies_list();	 
		die();	   
	}
 
    public function companies()
    {	
	   $data['message_output']   = $this->message_output;
	   $data['folder_path']      = $this->folder_path;
	   $data['base_url_path']    = $this->base_url;
	  
	   $user_uuid =  $this->session->get('uuid');
	   $cacheKey = 'company_list_for_user_' . $user_uuid;
	  	$companyList = $this->CommonModel->ajax_all_company_list();
	 
	  return view('companies',$data);	
	}
	
    public function office_tools()
    {	
	   $data['message_output']   = $this->message_output;
	   $data['folder_path']      = $this->folder_path;
	   $data['base_url_path']    = $this->base_url;
	   
	   
	   $user_id = auth()->id;
	   
	   $data['events']    = $this->CalendarModel->all_events($user_id);
	   
	   //echo '<pre>';
	   //print_r($data['events']);
	   //die();
	   
	   return view('office_tools', $data);	
	}

	
	function get_comp_size()
	{
		if($this->request->getMethod() == 'post'){
			$comp_id = $this->request->getVar('comp_id');
			$comp_code = $this->request->getVar('comp_code');

			$comp_size = $this->CommonModel->get_comp_size($comp_id,$comp_code);

			return json_encode(['status' => true, 'comp_size' => $comp_size]); 
		}
	}
	
	public function ajax_my_companies(){
	    
	   echo  $this->CommonModel->ajax_my_companies_list();
	   die();
	    
	}

	public function company_backup()
{
    require_once APPPATH . "Libraries/vendor/autoload.php";

    $google_client = new \Google_Client();
    $google_client->setClientId('17755805151-hobf3kspioi04i7u4ea0k3lvd5o45f5o.apps.googleusercontent.com');
    $google_client->setClientSecret('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');
    $google_client->setRedirectUri('https://sandbox.aicountly.in/home/company_backup');
    $google_client->setScopes(['https://www.googleapis.com/auth/drive']);
    $google_client->setAccessType('offline');

    // Step 1: OAuth callback
    if (isset($_GET['code'])) {
        $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
        $this->session->set('google_token', $token);

        // Perform first-time upload for the first company
        $google_client->setAccessToken($token);
        $driveService = new \Google\Service\Drive($google_client);

        $companies = $this->CommonModel->ajax_my_companies_list_drive();
        if (!empty($companies)) {
            $firstCompany = $companies[0];
            $this->ajax_my_companies_drive($firstCompany['comp_code'], $driveService);
        }

        // Mark that first upload is done
        $this->session->set('first_upload_done', true);

        return redirect()->to('/home/company_backup');
    }

    // Step 2: Setup after token is stored
    if ($this->session->has('google_token')) {
        $google_client->setAccessToken($this->session->get('google_token'));

        if ($google_client->isAccessTokenExpired()) {
            $token = $google_client->fetchAccessTokenWithRefreshToken($google_client->getRefreshToken());
            $this->session->set('google_token', $token);
        }

        $data['drive_ready'] = true;
    } else {
        $data['drive_ready'] = false;
        $data['upload_link'] = $google_client->createAuthUrl();
    }

    $data['message_output'] = $this->message_output;
    $data['folder_path'] = $this->folder_path;
    $data['base_url'] = $this->base_url;
    $data['session'] = $this->session;

    return view($this->folder_path . 'company_backup/backup', $data);
}



	public function upload_all_to_drive()
{
    require_once APPPATH . "Libraries/vendor/autoload.php";

    $google_client = new \Google_Client();
    $google_client->setClientId('17755805151-hobf3kspioi04i7u4ea0k3lvd5o45f5o.apps.googleusercontent.com');
    $google_client->setClientSecret('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');
    $google_client->setRedirectUri('https://sandbox.aicountly.in/home/company_backup');
    $google_client->setScopes(['https://www.googleapis.com/auth/drive']);
    $google_client->setAccessType('offline');

    if (!$this->session->has('google_token')) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Google Drive not authenticated']);
    }

    $google_client->setAccessToken($this->session->get('google_token'));
    if ($google_client->isAccessTokenExpired()) {
        $token = $google_client->fetchAccessTokenWithRefreshToken($google_client->getRefreshToken());
        $this->session->set('google_token', $token);
    }

    $driveService = new \Google\Service\Drive($google_client);
    $companies = $this->CommonModel->ajax_my_companies_list_drive();

    foreach ($companies as $company) {
        $this->ajax_my_companies_drive($company['comp_code'], $driveService);
    }

    return $this->response->setJSON(['status' => 'success', 'message' => 'All backups uploaded to Google Drive']);
}


	public function backup_drive(){

		$companies = $this->CommonModel->ajax_my_companies_list_drive();

		foreach ($companies as $company) {
			$this->ajax_my_companies_drive($company['comp_code']);
		}

	}

	public function ajax_my_companies_drive($comp, $driveService)
{
    helper(['filesystem']);
    $databaseName = 'aicountlyin_' . $comp;

    $comp_db = $this->externaldb->single_company_db($comp);
    $tablesQuery = $comp_db->query("SHOW TABLES")->getResultArray();
    if (empty($tablesQuery)) return;

    $tables = array_map('current', $tablesQuery);
    $sqlContent = "-- Exported from $databaseName on " . date('Y-m-d H:i:s') . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n";

    foreach ($tables as $table) {
        $createStmt = $comp_db->query("SHOW CREATE TABLE `$table`")->getRowArray();
        $sqlContent .= $createStmt['Create Table'] . ";\n\n";

        $rows = $comp_db->table($table)->get()->getResultArray();
        foreach ($rows as $row) {
            $values = array_map(fn($v) => $comp_db->escape($v), array_values($row));
            $sqlContent .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
        }
        $sqlContent .= "\n\n";
    }

    $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";

    $exportsPath = WRITEPATH . 'exports/';
    if (!is_dir($exportsPath)) mkdir($exportsPath, 0777, true);

    $sqlFileName = "$databaseName.sql";
    $sqlFilePath = $exportsPath . $sqlFileName;
    write_file($sqlFilePath, $sqlContent);

    $zipPath = $exportsPath . "$databaseName.zip";
    $zip = new \ZipArchive();
    if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
        $zip->addFile($sqlFilePath, $sqlFileName);
        $zip->close();
    } else {
        echo "ZIP creation failed for $databaseName";
        return;
    }

    // ✅ Upload to Google Drive
    try {
        $driveFile = new \Google\Service\Drive\DriveFile([
            'name' => "$databaseName.zip"
        ]);

        $content = file_get_contents($zipPath);
        $driveService->files->create($driveFile, [
            'data' => $content,
            'mimeType' => 'application/zip',
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);
    } catch (Exception $e) {
        echo "Drive upload failed for $databaseName: " . $e->getMessage();
    }

    // Cleanup local files
    unlink($sqlFilePath);
    unlink($zipPath);
}





	
    public function my_companies()
    {	
    
	   $data['message_output']   = $this->message_output;
	   $data['folder_path']      = $this->folder_path;
	   $data['base_url_path']    = $this->base_url;
	   $data['country_array'] = $this->CommonModel->get_country_array();
       $data['state_array'] = $this->CommonModel->get_state_array();
	   
	   return view('my_companies',$data);	
	}

 
 public function recyclebin()
    {	
 
  
	   $data['message_output']   = $this->message_output;
	   $data['folder_path']      = $this->folder_path;
	   $data['base_url_path']    = $this->base_url;
	   $data['company_list']     = $this->CommonModel->ajax_recyclebin_company_list();
	   return view('recyclebin',$data);	
	}
	

	public function transfer_ownership()
	{
	
	   $data['message_output']   = $this->message_output;
	   $data['folder_path']      = $this->folder_path;
	   $data['base_url_path']    = $this->base_url;
	   $data['base_url']         = $this->base_url;
	   $data['company_list']     = $this->CompanyAccessModel->ajax_all_company_list();
	   $data['business_id']      = $this->CommonModel->get_business_id();
	   $data['profile_link']     = getenv('profile_website').obfuscate_link($this->session->get('uuid'));
	 	
	   
	   return view('transfer_ownership',$data);
	}
	public function get_progress()
	{
		$progress = session()->get('comp_progress') ?? 0;
		echo json_encode(['progress' => $progress]);
		die();
	}
	public function check_table_existence(){
		if ($this->request->getMethod() === 'post') {
		$company_id = $this->session->get('sscomp_id');
		$fy_id      = $this->session->get('ssfy_id');
		$res = $this->CommonModel->get_company_table_info($company_id,$fy_id);		
		echo json_encode(["exists"=>$res]);
		die();
		}
	}

	public function check_crs()
	{
		$keyword = $this->request->getGet('keyword');
		$child = $this->request->getGet('child');
		// echo $keyword;
		// die();
		$crs = $this->CommandModel->crs_types($keyword, $child);
		echo json_encode($crs);
		die();
	}

	public function check_json()
	{
		helper('filesystem');
	    $commands = $this->CommonModel->commands();
		if (!empty($commands)) {
			$filePath = WRITEPATH . 'commands.json';		
			$jsonData = json_encode($commands, JSON_PRETTY_PRINT); 
			if (write_file($filePath, $jsonData)) {

			} else {
			
			}
		}
		
	}
	
	
	public  function ajax_select_company($company_id,$exit=true,$comp_fy_id_=0,$bo_id_=0)
	 {	     
		 $company_info          =  $this->CommonModel->get_company_info($company_id);
		 $CmpProfileInfo		=  $this->CommonModel->GetCmpProfileInfo($company_id);
		 $company_last_fy       =  $this->CommonModel->company_last_fy($company_id);
		 $comp_fy_id            =  $company_last_fy['cmpfymastr_id'];
	     $def_val_method        =  $company_last_fy['def_val_method'];
	
		 $this->session->remove([
						'bo_gstin_type',
						'ses_company_id',
						'ses_company_code',
						'ses_compl_company_code',
						'ses_company_name',
						'ses_company_print_name',
						'ses_company_short_name',
						'ses_company_fy_beginning',
						'ses_company_fy_end',
						'ses_company_email',
						'ses_company_mobile',
						'ses_company_wa_mobile',
						'ses_company_gstin',
						'ses_company_tan_no',
						'ses_company_pan_no',
						'user_type',
						'comp_uuid',
						'ses_boid',
						'ses_boname',
						'ses_bostecd',
						'ses_cmp_prf_id'
					]);
		 $this->session->set('ses_comp_fy_id',$comp_fy_id); 		 
		 $this->session->set('user_type','CS');
		 $this->session->set('ses_company_id', $company_id);								
		 $this->session->set('ses_company_code', erp_compcode_format($company_info['cmp_id']));
		 $this->session->set('ses_company_name', $company_info['cmp_name']);
		 $this->session->set('ses_company_print_name', $company_info['cmp_print_name']);
		 $this->session->set('ses_company_short_name', $company_info['cmp_short_name']);
		 $this->session->set('ses_company_fy_beginning',$company_last_fy['fy_beg_date']);
		 $this->session->set('ses_company_fy_end',$company_last_fy['fy_end_date']);
		 $this->session->set('ses_dflt_val_method',$def_val_method);
		 $this->session->set('ses_cmp_prf_id', $CmpProfileInfo['erp_acs_prof_id']);
		 
		// $this->check_json();
		
		//$commands = $this->CommonModel->commands();
         //   command_line_json($commands);
         
         $adrs_info =  $this->CommonModel->get_comp_ho_adrs_info($company_id,$bo_id_);
        
         
		  if(!empty($adrs_info)){
		      $bo_state_code = sprintf( '%02d',($adrs_info['state_code'])); 
			  $ses_boname    = $adrs_info['hobo_name']; 
			  $bo_id         = $adrs_info['hobo_id'];
			  $this->session->set('bo_gstin_type',$adrs_info['hobo_gstin_type']);
			  $this->session->set('cmp_suply_type',$adrs_info['hobo_gstin_sub_type']);// Composition Sub Type	
		  }else{
			  $bo_state_code = 0;
			  $ses_boname    ="";
			  $bo_id         = 0;
			  $this->session->set('bo_gstin_type',1);
			  $this->session->set('cmp_suply_type',0); // Composition Sub Type
		  }
		  
			$this->session->set('ses_boid',$bo_id);
			$this->session->set('ses_bostecd', sprintf( '%02d',$bo_state_code));
			$this->session->set('ses_boname',$ses_boname);
			
		  
		  try {
				$errors = [];

				$accounts_list = $this->AccountsModel->company_all_accounts();
				$items_list = $this->ItemsModel->company_all_items();
				$company_all_bsd = $this->BillsundryModel->company_all_bsd();
				if (!CreateJsonFile($comp_fy_id, $company_id, 'acc', $accounts_list)) $errors[] = 'accounts';
				if (!CreateJsonFile($comp_fy_id, $company_id, 'itm', $items_list)) $errors[] = 'items';
				if (!CreateJsonFile($comp_fy_id, $company_id, 'bsd', $company_all_bsd)) $errors[] = 'bill_sundry';

				if ($errors) {
					echo json_encode([
						'message' => 'File creation failed: ' . implode(', ', $errors),
						'status'  => 0,
						'success' => 0
					]);
				} else {
					echo json_encode([
						'message' => 'Company Selected',
						'status'  => 1,
						'success' => 1
					]);
				}
				die();
		} catch (\Throwable $e) {
			log_message('error', 'Company select JSON write failed: ' . $e->getMessage());
			echo json_encode([
				'message' => 'Unexpected error: ' . $e->getMessage(),
				'status'  => 0,
				'success' => 0
			]);
			die();
		}
	 }
	 
	 public function save_preferences()
		{
			if ($this->request->getMethod() === 'POST') {
				$config_id = $this->request->getVar('config_id');
				$config_value = $this->request->getVar('config_value');
				$this->CommonModel->save_theme_preferences($config_id,$config_value);
				  return $this->response->setJSON(['status' => 'success']);
			}
			 return $this->response->setJSON(['status' => 'error']);
		}
	 
    public function add_company()
{
    if ($this->request->getMethod() === 'POST') {
        $company_name = $this->request->getVar('comnpany_name');
		$valmethod_id = $this->request->getVar('valmethod_id');

        if (empty($company_name)) {
             echo json_encode([
                'status' => 0,
                'message' => 'Company name is required.'
            ]);
        }
		///$trans_result           = $this->runTransaction(function($db) use ($company_name,$valmethod_id){
		try{
			$fybegin_date = $this->request->getVar('fybegin_date');
			$fyend_date   = get_fy_end_date($fybegin_date);
			$insert_data  = [
				'comp_name'         => $company_name,
				'comp_print_name'   => $this->request->getVar('print_name'),
				'comp_short_name'   => $this->request->getVar('short_name'),
				'ro_add1'           => $this->request->getVar('ro_add1'),
				'ro_add2'           => $this->request->getVar('ro_add2'),
				'ro_city'           => $this->request->getVar('ro_city'),
				'ro_pin'            => $this->request->getVar('ro_pin'),
				'ro_country'        => $this->request->getVar('ro_country'),
				'ro_state'          => $this->request->getVar('ro_state'),
				'co_add1'           => $this->request->getVar('co_add1'),
				'co_add2'           => $this->request->getVar('co_add2'),
				'co_city'           => $this->request->getVar('co_city'),
				'co_state'          => $this->request->getVar('co_state'),
				'co_pin'            => $this->request->getVar('co_pin'),
				'co_country'        => $this->request->getVar('co_country'),
				'comp_industry'     => $this->request->getVar('industry_id') ?? 0,
				'comp_work_nature'  => $this->request->getVar('nature_ofwork') ??0,
				'fy_begndt'         => date("Y-m-d", strtotime($fybegin_date)),
				'fy_enddt'          => date("Y-m-d", strtotime($fyend_date)),
				'valmethod_id'      => $valmethod_id
			];
			
			$company_message = $this->CommonModel->add_comp($insert_data);	
			
			if ($company_message['status'] == 0) {
				echo json_encode([
					'status' => 0,
					'message' => $company_message['message']
				]);
			} else {
				echo json_encode([
					'status' => 1,
					'message' => $company_message['message']
				]);
			}
			die();
		}	
		catch (\Throwable $e) {
		 // Get line, file and stack trace
		 log_message('error', 'DB Error: ' . $e->getMessage());
		 log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());
		 log_message('error', 'Trace: ' . $e->getTraceAsString());	
		 echo  json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
		 die();
		 }
		//});		
       /* if(isset($trans_result['result']['status']) && $trans_result['result']['status']==''){            
					if(isset($trans_result['result']['errors']))
					return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message'],'errors' =>$trans_result['result']['errors']]);
					else
					return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message'],'errors' =>'']);
				}
		   else
         return $this->response->setJSON($trans_result); */ 
    }

    // Not a POST request - render the form
    $data['message_output']      = $this->message_output;
	$data['fin_year_list']       = $this->CommonModel->calculateFiscalYearForDate(date('m'));
     
    $data['folder_path']         = $this->folder_path;
    $data['base_url_path']       = $this->base_url;
    $data['StatesDropdown']      = $this->CommonModel->StatesDropdown();
    $data['CountryDropdown']     = $this->CommonModel->CountryDropdown();
    $data['industry_type_list']  = $this->CommonModel->industry_type_dropdown();
    $data['natureof_work_list']  = $this->CommonModel->natureof_work_dropdown();

    return view('add_company', $data);
}	
    
  	public  function ajax_states_list($country_id,$state_id)
	{
		echo $this->CommonModel->StatesDropdown($country_id,$state_id);	
        die();		
	}  
	 
  	public function view_company(){	  
      $data['message_output']  = $this->message_output;
	  $data['folder_path']     = $this->folder_path;
	  $data['base_url']        = $this->base_url;	  
	  return view('view_company',$data);	 
    }


}