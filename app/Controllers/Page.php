<?php
namespace App\Controllers;;

class Page extends BaseController
{
	public function __construct()
    {  
	  helper(['form', 'url']);
	 $this->session 	         = \Config\Services::session();     
	 $this->folder_path          = getenv('AdminPath');
	 $this->base_url             = base_url();;
	}
	
	function bhupi($submit=NULL){
	    
	   if($submit!=NULL && $this->request->getMethod() == 'post'){
	       sleep(5);
	       
	       
	      echo '{"message":"User saved"}';
	      die();
	   }
	    $data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url_path']    = $this->base_url; 
	    
	   return view('bhupi',$data);   
	    
	}
	
	public  function pricing()
	 {
	   	$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url_path']    = $this->base_url;
	    return view('pricing_policy',$data); 
	 }
	 public  function ipr()
	 {
	   	$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url_path']    = $this->base_url;
	    return view('ipr_policy',$data); 
	 }
	 public  function refund()
	 {
	   	$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url_path']    = $this->base_url;
	    return view('refund_policy',$data); 
	 }
	 public  function security()
	 {
	   	$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url_path']    = $this->base_url;
	    return view('security_policy',$data); 
	 }
	 public  function delivery()
	 {
	   	$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url_path']    = $this->base_url;
	    return view('delivery_policy',$data); 
	 }
}