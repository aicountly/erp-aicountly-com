<?php
namespace App\Controllers\Admin;
use App\Models\Admin\BackupModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;


class Backup extends BaseController
{
	function __construct()
	{  
		helper(['form', 'url','text']);
		$this->BackupModel     = new BackupModel();
		$this->auth_session    = new auth_session();
	
		$this->auth_session->user_restrict();
		$this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->enc_string    =  new enc_string();
	}
    
	public function index()
	{
		require_once APPPATH."Libraries/vendor/autoload.php";
        
    $google_client = new \Google_Client();
    $google_client->setClientId('17755805151-hobf3kspioi04i7u4ea0k3lvd5o45f5o.apps.googleusercontent.com');
    $google_client->setClientSecret('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');

    $google_client->setRedirectUri(base_url().'/admin/backup');
    $google_client->setScopes(array('https://www.googleapis.com/auth/drive'));
    
    if(isset($_GET['code'])){
     
	     $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
	     
	     if(isset($token['access_token']) && $token['access_token']!=''){
	         
         	$google_client->setAccessToken($token['access_token']);
         	$google_service = new \Google_Service_Drive($google_client);
         
          $data = $this->BackupModel->db_backup();
          $_name = 'db_'.rand(1000,9999).'_'.time();
          $file_name = $_name.'.sql';
          
          $folder_path = WRITEPATH.'uploads/db_backup_temp'; 
          if(!file_exists($folder_path)) {
      	    mkdir($folder_path, 0755);
      		}
          $file_path =  $folder_path.'/'.$file_name;
          
          file_put_contents($file_path, $data);
           
          $zip = new \ZipArchive();
		  
          $zip_file_path =  $folder_path.'/'.$_name.'.zip';
          if ($zip->open($zip_file_path, \ZipArchive::CREATE) == TRUE) {
             if( $zip->addFile($file_path,$file_name)){
			  $zip->setPassword('aic_zip_19846'); 
			  $zip->setEncryptionName($file_name, \ZipArchive::EM_AES_256); // AES encryption (for file1)
			  $zip->close();
			 }else{
				echo 'Error: Failed to add file to ZIP archive.';
				return;				
			 }
				 
		  }else{
			   echo 'Error: Cannot create ZIP archive.';
              return;
			  
		  }
	          
         
         unlink($file_path);
         
           $fileMetadata = new \Google_Service_Drive_DriveFile(array(
              'name' => $_name.'.zip'));
          $content = file_get_contents($zip_file_path);
	          
	          
          $file = $google_service->files->create($fileMetadata, array(
              'data' => $content,
              'mimeType' => 'application/zip ',
              'uploadType' => 'media')
          );
          
          unlink($zip_file_path); 
          $this->message_output->set_success( 'Database Exported to Google Drive.');
          return redirect()->to(base_url().'/admin/backup');
	     }
	     $this->message_output->set_error( 'Something went wrong');
	     return redirect()->to(base_url().'/admin/backup');
    }
    
         $data['upload_link']            = $google_client->createAuthUrl();
	 
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;
		
		return view($this->folder_path.'backup/index',$data);		
	}


	function test()
	{
		echo rand(0,1);
	}
}