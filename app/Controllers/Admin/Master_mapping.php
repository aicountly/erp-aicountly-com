<?php
namespace App\Controllers\Admin;
use App\Models\Admin\DashboardModel;

use App\Models\Admin\ChartModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Admin\ERPLogModel;

class Master_mapping extends BaseController
{
    var	$folder_path;
	function __construct()
    {  
	    helper(['form', 'url']);
	     
		$this->DashboardModel = new DashboardModel();

		$this->ChartModel = new ChartModel();		
		$this->auth_session   = new auth_session();
	    $this->folder_path    = getenv('AdminPath');
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      =  base_url().'/'.getenv('AdminPath');
        $this->session    	 =  \Config\Services::session();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->user_id       =  $this->session->get('uuid_aicountly');
		$this->LogModel      =  new ERPLogModel();
    }
    
	public function index(){
		
		if(isset($_GET['criteria']) && $_GET['criteria']!='' && isset($_GET['allmasterchk']) && $_GET['allmasterchk']!='' ){
			return redirect()->to($this->base_url.'master_mapping/mapped_group_masters?criteria='.$_GET['criteria']); 
			 die();
		 } 
		 
		else if(isset($_GET['criteria'])){
			$sel_criteria = $_GET['criteria'];
			//load criteria master lists
			$master_lists = [];//$this->DashboardModel->MasterCriteriaLists($sel_criteria);			
			$data['master_lists']  = $master_lists;
			
		   }
		  
		  else{
			  $sel_criteria = ''; 
			  $data['master_lists']    = array();
			  
		  }
		
         
		$data['sel_criteria']      = $sel_criteria;
		$data['message_output']    = $this->message_output;
		$data['folder_path']       = $this->folder_path;
		$data['base_url']          = $this->base_url;
		$data['criteria_dropdown'] = array(''=>'Choose','acc'=>'Accounts','accgrp'=>'Accounts Group','itm'=>'Items',
		                                   'itmgrp'=>'Items Group','itemcat'=>'Stock Category','mcmst'=>'Material Centre',
										   'mcgrp'=>'Material Centre Group','costct'=>'Cost Centre',
										   'costctgrp'=>'Cost Centre Group');
		$data['session']         = $this->session;		
	  return view($this->folder_path.'master_mapping/index',$data);		
	}
	public function update_group_masters(){
		 if($this->request->getMethod() == 'post'){	
		      $group_company_master_name   = $this->request->getVar('group_company_master_name');
              $crs_master_id               = $this->request->getVar('crs_master_id');			  
		      $data = array("crs_master_name"=>$group_company_master_name);
		      $this->DashboardModel->UpdateMasterGroup($data,$crs_master_id);	
		  }
		  echo "1";
	}
	public function map_group_master(){
		 if($this->request->getMethod() == 'post'){	
		      $comp_id       = $this->request->getVar('comp_id');
              $crs_master_id = $this->request->getVar('crs_master_id');
			  $grpco_id      = $this->request->getVar('grpco_id');
			  $master_type   = $this->request->getVar('master_type');
			  $master_name   = $this->request->getVar('group_company_master_name');
		      $member_masterid_info =  $this->DashboardModel->member_masterid_info($master_name,$comp_id,$master_type);
			  $data = array("grpco_id"=>$grpco_id,"master_id"=>$member_masterid_info['member_company_master_id'],
			                 "master_type"=>$master_type,"crs_master_id"=>$crs_master_id,"comp_id"=>$comp_id);
		      $this->DashboardModel->InsertMapGroupWithCompany($data);	
		  }
		  echo "1";
	}
	
	public function remove_group_masters(){
		 if($this->request->getMethod() == 'post'){	
		      $crs_master_id               = $this->request->getVar('crs_master_id');
			  $grpmpid               = $this->request->getVar('grpmpid');			  
		      $this->DashboardModel->RemoveMappingGroup($grpmpid);	
		  }
		  echo "1";
	}
	
	public function remove_master(){  // removed from aictlyerp_grpcomstid_univdb if not mapped
		 if($this->request->getMethod() == 'post'){	
		      $crs_master_id               = $this->request->getVar('crs_master_id');			  
		      $this->DashboardModel->RemoveMaster($crs_master_id);	
		  }
		  echo "1";
	}
	
	public function mapped_group_masters(){
		if(isset($_GET['criteria'])){
			$sel_criteria = $_GET['criteria'];
			//load criteria master lists
			$master_lists = [];//$this->DashboardModel->MasterMappedData($sel_criteria);			
			$data['master_lists']  = $master_lists; 
			
		   }
		  else{
			  $sel_criteria = '';
			  $data['master_lists']    = array();
			  
		  }
		$data['sel_criteria']         = $sel_criteria;
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['criteria_dropdown'] = array(''=>'Choose','acc'=>'Accounts','accgrp'=>'Accounts Group','itm'=>'Items',
		                                   'itmgrp'=>'Items Group','itemcat'=>'Stock Category','mcmst'=>'Material Centre',
										   'mcgrp'=>'Material Centre Group','costct'=>'Cost Centre',
										   'costctgrp'=>'Cost Centre Group');
		$data['session']         = $this->session;		
	  return view($this->folder_path.'master_mapping/mapped_group_masters',$data);		
	}
	
	public function ummapped_group_masters(){
		if(isset($_GET['criteria'])){
			$sel_criteria = $_GET['criteria'];
			//load criteria master lists
			$master_lists = [];//$this->DashboardModel->MasterUnMappedData($sel_criteria);			
			$data['master_lists']  = $master_lists;
			
		   }
		  else{
			  $sel_criteria = '';
			  $data['master_lists']    = array();
			  
		  }
		$data['sel_criteria']         = $sel_criteria;
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['criteria_dropdown'] = array(''=>'Choose','acc'=>'Accounts','accgrp'=>'Accounts Group','itm'=>'Items',
		                                   'itmgrp'=>'Items Group','itemcat'=>'Stock Category','mcmst'=>'Material Centre',
										   'mcgrp'=>'Material Centre Group','costct'=>'Cost Centre',
										   'costctgrp'=>'Cost Centre Group');
		$data['session']         = $this->session;
        $data['companies_list'] = [];//$this->DashboardModel->group_companies_list();		
	  return view($this->folder_path.'master_mapping/ummapped_group_masters',$data);		
	}
  public function mapped_member_masters(){
		if(isset($_GET['criteria'])){
			$sel_criteria = $_GET['criteria'];
			//load criteria master lists
			$master_lists = [];//$this->DashboardModel->MasterMappedData($sel_criteria);			
			$data['master_lists']  = $master_lists;
			
		   }
		  else{
			  $sel_criteria = '';
			  $data['master_lists']    = array();
			  
		  }
		$data['sel_criteria']         = $sel_criteria;
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['criteria_dropdown'] = array(''=>'Choose','acc'=>'Accounts','accgrp'=>'Accounts Group','itm'=>'Items',
		                                   'itmgrp'=>'Items Group','itemcat'=>'Stock Category','mcmst'=>'Material Centre',
										   'mcgrp'=>'Material Centre Group','costct'=>'Cost Centre',
										   'costctgrp'=>'Cost Centre Group');
		$data['session']         = $this->session;		
	  return view($this->folder_path.'master_mapping/mapped_member_masters',$data);		
	}
	public function unmapped_member_masters(){
		if(isset($_GET['criteria'])){
			$sel_criteria = $_GET['criteria'];
			//load criteria master lists
			$master_lists = [];//$this->DashboardModel->MasterMappedData($sel_criteria);			
			$data['master_lists']  = $master_lists;
			
		   }
		  else{
			  $sel_criteria = '';
			  $data['master_lists']    = array();
			  
		  }
		$data['sel_criteria']         = $sel_criteria;
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['criteria_dropdown'] = array(''=>'Choose','acc'=>'Accounts','accgrp'=>'Accounts Group','itm'=>'Items',
		                                   'itmgrp'=>'Items Group','itemcat'=>'Stock Category','mcmst'=>'Material Centre',
										   'mcgrp'=>'Material Centre Group','costct'=>'Cost Centre',
										   'costctgrp'=>'Cost Centre Group');
		$data['session']         = $this->session;		
	  return view($this->folder_path.'master_mapping/unmapped_member_masters',$data);		
	} 
}
