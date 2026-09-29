<?php
namespace App\Controllers\Grpcomp;
use App\Models\Grpcomp\DashboardModel;

use App\Models\Grpcomp\ChartModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Grpcomp\ERPLogModel;

class Master_mapping extends BaseController
{
	var	$folder_path;
	function __construct()
	{  
		helper(['form', 'url']);

		$this->DashboardModel = new DashboardModel();

		$this->ChartModel = new ChartModel();		
		$this->auth_session   = new auth_session();
		$this->folder_path    = getenv('GroupPath');
		$this->auth_session->group_restrict();
		$this->auth_session->role_restrict('CS');
		$this->base_url      =  base_url().'/'.getenv('GroupPath');
		$this->session    	 =  \Config\Services::session();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->user_id       =  $this->session->get('uuid_aicountly');
		$this->LogModel      =  new ERPLogModel();
	}
	public function ajax_comp_data($comp_id,$master_type){
		$response      = [];//$this->DashboardModel->member_master_lists($comp_id,$master_type);

		echo json_encode($response);
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

		$masters = [
				'branch'									=> 'Branch',
				'currency'								=> 'Currency',
				'voucher_series' 					=> 'Voucher Series',
				'account_groups' 					=> 'Account Groups',
				'accounts' 								=> 'Accounts',
				'bill_sundry' 						=> 'Bill Sundry',
				'project_groups' 					=> 'Project Groups',
				'project' 								=> 'Project',
				'item_groups' 						=> 'Item Groups',
				'item_categories' 				=> 'Item Categories',
				'item' 										=> 'Item',
				'item_batch' 							=> 'Item Batch',
				'units' 									=> 'Units',
				'material_center_groups' 	=> 'Material Center Groups',
				'material_center' 				=> 'Material Center',
				'material_center_stores' 	=> 'Material Center Stores',
				'barcode' 								=> 'Barcode',
				'labels' 									=> 'Labels',
				'cost_center_groups' 			=> 'Cost Center Groups',
				'cost_center' 						=> 'Cost Center',
				'bill_by_bill' 						=> 'Bill By Bill',
				'bill_of_material' 				=> 'Bill Of Material',
				'prints' 									=> 'Prints',
		];
		
		$data['masters']					 = $masters;
		$data['sel_criteria']      = $sel_criteria;
		$data['message_output']    = $this->message_output;
		$data['folder_path']       = $this->folder_path;
		$data['base_url']          = $this->base_url;
		$data['criteria_dropdown'] = array(''=>'Choose','acc'=>'Accounts','bsd'=>'Bill Sundry','itm'=>'Items',
			'itemcat'=>'Stock Category','mcmst'=>'Material Centre',
			'costct'=>'Cost Centre');
		$data['session']         = $this->session;		
		return view($this->folder_path.'master_mapping/index',$data);		
	}
	public function update_member_masters(){
		if($this->request->getMethod() == 'post'){	
			$master_id   = $this->request->getVar('master_id');
			$grpmpid     = $this->request->getVar('grpmpid');			  
			$data = array("crs_master_id"=>$master_id);
			$this->DashboardModel->UpdateMemberMaster($data,$grpmpid);	
		}
		echo "1";	
		
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
	
	public function unmap_member_master(){
		if($this->request->getMethod() == 'post'){	
			$grpmpid       = $this->request->getVar('grpmpid');
			$data = array("crs_master_id"=>"");
			$this->DashboardModel->UpdateMemberMaster($data,$grpmpid);	
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
			$member_comp_master_id   = $this->request->getVar('member_comp_master_id');


			  // check if already mapped then unmap first and map then
			$is_mapped = [];//$this->DashboardModel->already_mapped($grpco_id,$member_comp_master_id,$master_type,$comp_id);
			if($is_mapped ){
				$grpmpid = $is_mapped['grpmpid'];
				$dddata= array("crs_master_id"=>"0");
				$this->DashboardModel->UnMapGroupWithCompany($dddata,$member_comp_master_id,$comp_id,$master_type,$grpmpid); 
				$data = array("grpco_id"=>$grpco_id,"master_id"=>$member_comp_master_id,
					"master_type"=>$master_type,"crs_master_id"=>$crs_master_id,"comp_id"=>$comp_id);
				$this->DashboardModel->InsertMapGroupWithCompany($data);
			}else{			  
				$data = array("grpco_id"=>$grpco_id,"master_id"=>$member_comp_master_id,
					"master_type"=>$master_type,"crs_master_id"=>$crs_master_id,"comp_id"=>$comp_id);
				$this->DashboardModel->InsertMapGroupWithCompany($data);	
			}
		}		  
		echo "1";
	}
	
	public function remove_group_masters(){
		if($this->request->getMethod() == 'post'){	
			$crs_master_id         = $this->request->getVar('crs_master_id');
			$grpmpid               = $this->request->getVar('grpmpid');
			$master_type           = $this->request->getVar('master_type');
			$comp_id               = $this->request->getVar('comp_id'); 			  
			$this->DashboardModel->RemoveMappingGroup($grpmpid,$crs_master_id,$master_type,$comp_id);	
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
			$master_lists = [];//$this->DashboardModel->MappedMemberData($sel_criteria);			
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
			$master_lists = [];//$this->DashboardModel->UnMappedMemberData($sel_criteria);			
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
