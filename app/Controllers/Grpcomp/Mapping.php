<?php
namespace App\Controllers\Grpcomp;
use App\Models\Grpcomp\MappingModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;


class Mapping extends BaseController
{
	var	$folder_path;
	function __construct()
	{  
		helper(['form', 'url']);

		$this->MappingModel = new MappingModel();
		
		$this->auth_session   = new auth_session();
		$this->folder_path    = getenv('GroupPath');
		$this->auth_session->group_restrict();
		$this->auth_session->role_restrict('CS');
		$this->base_url      =  base_url().'/'.getenv('GroupPath');
		$this->session    	 =  \Config\Services::session();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->user_id       =  $this->session->get('uuid_aicountly');

	}

	
	public function index(){
		
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
				'barcode' 								=> 'Barcode',
				'labels' 									=> 'Labels',
				'cost_center_groups' 			=> 'Cost Center Groups',
				'cost_center' 						=> 'Cost Center',
				'bill_by_bill' 						=> 'Bill By Bill',
				'bill_of_material' 				=> 'Bill Of Material',
				'prints' 									=> 'Prints',
		];
		
		$data['masters']					 = $masters;
		$data['message_output']    = $this->message_output;
		$data['folder_path']       = $this->folder_path;
		$data['base_url']          = $this->base_url;
		$data['session']           = $this->session;

		return view($this->folder_path.'mapping/index',$data);		
	}

	function mapped_group($type = '')
	{
		
		$masters = [
			'branch'					=> 
					['title' => 'Branch', 'master_type' => 'hobomaster'],
			'currency'				=> 
					['title' => 'Currency', 'master_type' => 'compcurrcy'],
			'voucher_series' 	=> 
					['title' => 'Voucher Series', 'master_type' => 'cmpvchseri'],
			'account_groups' 	=> 
					['title' => 'Account Groups', 'master_type' => 'acctgroupn'],
			'accounts' 				=> 
					['title' => 'Accounts', 'master_type' => 'acctmaster'],
			'bill_sundry' 		=> 
					['title' => 'Bill Sundry', 'master_type' => 'billsundry'],
			'project_groups' 	=> 
					['title' => 'Project Groups', 'master_type' => 'projectgrp'],
			'project' 				=> 
					['title' => 'Project', 'master_type' => 'projectmst'],
			'item_groups' 		=> 
					['title' => 'Item Groups', 'master_type' => 'itemgrpmst'],
			'item_categories' => 
					['title' => 'Item Categories', 'master_type' => 'itemcatmst'],
			'item' 						=> 
					['title' => 'Item', 'master_type' => 'itemmaster'],
			'item_batch' 			=> 
					['title' => 'Item Batch', 'master_type' => 'itmbatchmt'],
			'units' 					=> 
					['title' => 'Units', 'master_type' => 'itmunitmst'],
			'material_center_groups' 	=> ['title' => 'Material Center Groups', 'master_type' => 'mcgrpmstnn'],
			'material_center' => 
					['title' => 'Material Center', 'master_type' => 'mcmasternn'],
			'barcode' 				=> 
					['title' => 'Barcode', 'master_type' => 'barcodemst'],
			'labels' 					=> 
					['title' => 'Labels', 'master_type' => 'labelmastr'],
			'cost_center_groups' 			=> ['title' => 'Cost Center Groups', 'master_type' => 'costctgrup'],
			'cost_center' 		=> 
					['title' => 'Cost Center', 'master_type' => 'costctmstr'],
			'bill_by_bill' 		=> 
					['title' => 'Bill By Bill', 'master_type' => 'billmaster'],
			'bill_of_material' => 
					['title' => 'Bill Of Material', 'master_type' => 'billofmatn'],
			'prints' 					=> 
					['title' => 'Prints', 'master_type' => 'prntconfig'],
		];

		if(!isset($masters[$type])){
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
		}
			
		$data['master_lists'] = $this->MappingModel->mappedGroupData($masters[$type]['master_type']);
			
		$data['title'] = ucwords($masters[$type]['title']).' Mapped Group Masters';
		$data['type']	= $type;
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		
		return view($this->folder_path.'mapping/mapped_group',$data);	
	}

	function update_master_name()
	{
		if($this->request->getMethod() == 'post'){
			$crs_master_id = $this->request->getVar('crs_master_id');
			$master_name = $this->request->getVar('master_name');

			$this->MappingModel->update_master_name($crs_master_id,$master_name);

			return json_encode(['status' => true, 'message' => 'Data Updated']);
		}
	}

	function delete_master_mapping()
	{
		if($this->request->getMethod() == 'post'){
			$crs_master_id = $this->request->getVar('crs_master_id');
			$comp_id = $this->request->getVar('comp_id');

			$this->MappingModel->delete_master_mapping($crs_master_id,$comp_id);

			return json_encode(['status' => true, 'message' => 'Data Updated']);
		}
	}

	function unmapped_group($type = '') 
	{

		$masters = [
			'branch'					=> 
					['title' => 'Branch', 'master_type' => 'hobomaster'],
			'currency'				=> 
					['title' => 'Currency', 'master_type' => 'compcurrcy'],
			'voucher_series' 	=> 
					['title' => 'Voucher Series', 'master_type' => 'cmpvchseri'],
			'account_groups' 	=> 
					['title' => 'Account Groups', 'master_type' => 'acctgroupn'],
			'accounts' 				=> 
					['title' => 'Accounts', 'master_type' => 'acctmaster'],
			'bill_sundry' 		=> 
					['title' => 'Bill Sundry', 'master_type' => 'billsundry'],
			'project_groups' 	=> 
					['title' => 'Project Groups', 'master_type' => 'projectgrp'],
			'project' 				=> 
					['title' => 'Project', 'master_type' => 'projectmst'],
			'item_groups' 		=> 
					['title' => 'Item Groups', 'master_type' => 'itemgrpmst'],
			'item_categories' => 
					['title' => 'Item Categories', 'master_type' => 'itemcatmst'],
			'item' 						=> 
					['title' => 'Item', 'master_type' => 'itemmaster'],
			'item_batch' 			=> 
					['title' => 'Item Batch', 'master_type' => 'itmbatchmt'],
			'units' 					=> 
					['title' => 'Units', 'master_type' => 'itmunitmst'],
			'material_center_groups' 	=> ['title' => 'Material Center Groups', 'master_type' => 'mcgrpmstnn'],
			'material_center' => 
					['title' => 'Material Center', 'master_type' => 'mcmasternn'],
			'barcode' 				=> 
					['title' => 'Barcode', 'master_type' => 'barcodemst'],
			'labels' 					=> 
					['title' => 'Labels', 'master_type' => 'labelmastr'],
			'cost_center_groups' 			=> ['title' => 'Cost Center Groups', 'master_type' => 'costctgrup'],
			'cost_center' 		=> 
					['title' => 'Cost Center', 'master_type' => 'costctmstr'],
			'bill_by_bill' 		=> 
					['title' => 'Bill By Bill', 'master_type' => 'billmaster'],
			'bill_of_material' => 
					['title' => 'Bill Of Material', 'master_type' => 'billofmatn'],
			'prints' 					=> 
					['title' => 'Prints', 'master_type' => 'prntconfig'],
		];

		if(!isset($masters[$type])){
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
		}
			
		$data['master_lists'] = $this->MappingModel->unmappedGroupData($masters[$type]['master_type']);

		$data['comp_list'] = $this->MappingModel->group_comp_list();
		$data['unmapped_master_list'] = $this->MappingModel->unmapped_master_list($masters[$type]['master_type']);
			
		$data['title'] = ucwords($masters[$type]['title']).' Unmapped Group Masters';
		$data['type']	= $type;
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session; 
		
		return view($this->folder_path.'mapping/unmapped_group',$data);	
	}

	function update_master_mapping()
	{
		if($this->request->getMethod() == 'post'){
			$crs_master_id = $this->request->getVar('crs_master_id');
			$grpmp_id = $this->request->getVar('grpmp_id');
			$comp_id = $this->request->getVar('comp_id');

			$this->MappingModel->update_master_mapping($crs_master_id,$comp_id,$grpmp_id);

			return json_encode(['status' => true, 'message' => 'Data Updated']);
		}
	}

	function mapped_member($type = '')
	{
		$masters = [
			'branch'					=> 
					['title' => 'Branch', 'master_type' => 'hobomaster'],
			'currency'				=> 
					['title' => 'Currency', 'master_type' => 'compcurrcy'],
			'voucher_series' 	=> 
					['title' => 'Voucher Series', 'master_type' => 'cmpvchseri'],
			'account_groups' 	=> 
					['title' => 'Account Groups', 'master_type' => 'acctgroupn'],
			'accounts' 				=> 
					['title' => 'Accounts', 'master_type' => 'acctmaster'],
			'bill_sundry' 		=> 
					['title' => 'Bill Sundry', 'master_type' => 'billsundry'],
			'project_groups' 	=> 
					['title' => 'Project Groups', 'master_type' => 'projectgrp'],
			'project' 				=> 
					['title' => 'Project', 'master_type' => 'projectmst'],
			'item_groups' 		=> 
					['title' => 'Item Groups', 'master_type' => 'itemgrpmst'],
			'item_categories' => 
					['title' => 'Item Categories', 'master_type' => 'itemcatmst'],
			'item' 						=> 
					['title' => 'Item', 'master_type' => 'itemmaster'],
			'item_batch' 			=> 
					['title' => 'Item Batch', 'master_type' => 'itmbatchmt'],
			'units' 					=> 
					['title' => 'Units', 'master_type' => 'itmunitmst'],
			'material_center_groups' 	=> ['title' => 'Material Center Groups', 'master_type' => 'mcgrpmstnn'],
			'material_center' => 
					['title' => 'Material Center', 'master_type' => 'mcmasternn'],
			'barcode' 				=> 
					['title' => 'Barcode', 'master_type' => 'barcodemst'],
			'labels' 					=> 
					['title' => 'Labels', 'master_type' => 'labelmastr'],
			'cost_center_groups' 			=> ['title' => 'Cost Center Groups', 'master_type' => 'costctgrup'],
			'cost_center' 		=> 
					['title' => 'Cost Center', 'master_type' => 'costctmstr'],
			'bill_by_bill' 		=> 
					['title' => 'Bill By Bill', 'master_type' => 'billmaster'],
			'bill_of_material' => 
					['title' => 'Bill Of Material', 'master_type' => 'billofmatn'],
			'prints' 					=> 
					['title' => 'Prints', 'master_type' => 'prntconfig'],
		];

		if(!isset($masters[$type])){
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
		}
			
		$data['master_lists'] = $this->MappingModel->mappedMemberData($masters[$type]['master_type']);
			
		$data['title'] = ucwords($masters[$type]['title']).' Mapped Group Masters';
		$data['type']	= $type;
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		
		return view($this->folder_path.'mapping/mapped_member',$data);	
	}

	function unmapped_member($type = '') 
	{
		$masters = [
			'branch'					=> 
					['title' => 'Branch', 'master_type' => 'hobomaster'],
			'currency'				=> 
					['title' => 'Currency', 'master_type' => 'compcurrcy'],
			'voucher_series' 	=> 
					['title' => 'Voucher Series', 'master_type' => 'cmpvchseri'],
			'account_groups' 	=> 
					['title' => 'Account Groups', 'master_type' => 'acctgroupn'],
			'accounts' 				=> 
					['title' => 'Accounts', 'master_type' => 'acctmaster'],
			'bill_sundry' 		=> 
					['title' => 'Bill Sundry', 'master_type' => 'billsundry'],
			'project_groups' 	=> 
					['title' => 'Project Groups', 'master_type' => 'projectgrp'],
			'project' 				=> 
					['title' => 'Project', 'master_type' => 'projectmst'],
			'item_groups' 		=> 
					['title' => 'Item Groups', 'master_type' => 'itemgrpmst'],
			'item_categories' => 
					['title' => 'Item Categories', 'master_type' => 'itemcatmst'],
			'item' 						=> 
					['title' => 'Item', 'master_type' => 'itemmaster'],
			'item_batch' 			=> 
					['title' => 'Item Batch', 'master_type' => 'itmbatchmt'],
			'units' 					=> 
					['title' => 'Units', 'master_type' => 'itmunitmst'],
			'material_center_groups' 	=> ['title' => 'Material Center Groups', 'master_type' => 'mcgrpmstnn'],
			'material_center' => 
					['title' => 'Material Center', 'master_type' => 'mcmasternn'],
			'barcode' 				=> 
					['title' => 'Barcode', 'master_type' => 'barcodemst'],
			'labels' 					=> 
					['title' => 'Labels', 'master_type' => 'labelmastr'],
			'cost_center_groups' 			=> ['title' => 'Cost Center Groups', 'master_type' => 'costctgrup'],
			'cost_center' 		=> 
					['title' => 'Cost Center', 'master_type' => 'costctmstr'],
			'bill_by_bill' 		=> 
					['title' => 'Bill By Bill', 'master_type' => 'billmaster'],
			'bill_of_material' => 
					['title' => 'Bill Of Material', 'master_type' => 'billofmatn'],
			'prints' 					=> 
					['title' => 'Prints', 'master_type' => 'prntconfig'],
		];

		if(!isset($masters[$type])){
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
		}
			
		$data['master_lists'] = $this->MappingModel->unmappedMemberData($masters[$type]['master_type']);

		$data['crs_master_lists'] = $this->MappingModel->crs_master_list($masters[$type]['master_type']);
			
		$data['title'] = ucwords($masters[$type]['title']).' Unmapped Group Masters';
		$data['type']	= $type;
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session; 
		
		return view($this->folder_path.'mapping/unmapped_member',$data);	
	}

}