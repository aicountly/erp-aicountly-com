<?php
namespace App\Controllers\Admin;
use App\Models\Admin\AccountsModel;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\ERPLogModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;

class Accounts extends BaseController{
  function __construct(){  
	helper(['form', 'url','text','custom']);
	$this->AccountsModel     = new AccountsModel();
	$this->VouchersModel     = new VouchersModel();
	$this->LogModel          = new ERPLogModel();
	$this->CommonModel       = new CommonModel();		
	$this->auth_session      = new auth_session();
	$this->auth_session->user_restrict();
	$this->auth_session->is_company_opened();
	$this->auth_session->role_restrict('CS');
	$this->base_url      = base_url().getenv('AdminPath');
	$this->folder_path   = getenv('AdminPath');
	$this->session    	 = \Config\Services::session();
	$this->bo_id         = $this->session->get('ses_boid');
	$this->fy_id         = $this->session->get('ses_comp_fy_id');
	$this->company_id    = $this->session->get('ses_company_id');
	$this->comp_code     = $this->session->get('ses_company_code');
	$this->item_supply_type = array("1"=>"Goods","2"=>"Services","3"=>"Capital Goods");
	$this->item_tax_basis   = array("1"=>"Taxable Value(%)","2"=>"MRP(Rs)");
    $this->is_valid_url     = validate_web_url(current_url())['allowed'];
    
	if (!$this->is_valid_url) {
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
     }   	
    }
    
	public function party_gst_info($party_id){
  	  $response = $this->VouchersModel->party_gst_info($party_id);
	  echo json_encode($response);  
   }
   public function mc_info($mc_id){
  	  $response = $this->VouchersModel->mc_info($mc_id);
	  echo json_encode($response);  
   }
   
     public  function ajax_accounts_view()
	   {
		echo $response =  $this->AccountsModel->ajax_accounts_list();
	   } 
	   
     public function getAccountBills(){
		if($this->request->getMethod() == 'POST'){
		$data = [];
		$data = $this->AccountsModel->get_account_bill_refs();
		echo json_encode(['status' => true, 'data' => $data]);
	   } 
		 
	 }
	 
	 public function getAccountSblgrRefs(){
		if($this->request->getMethod() == 'POST'){
		$data = [];
		$data = $this->AccountsModel->get_account_sublgr_refs();
		echo json_encode(['status' => true, 'data' => $data]);
	   } 
		 
	 }
	 
	 public function ValidateAccountBillRefs(){
		if($this->request->getMethod() == 'POST'){
			$account_names_array = $_POST['account_names_array'];
			echo  $this->AccountsModel->validate_account_bill_refs($account_names_array);
			die();
	   } 
		 
	 }
	 
	 public function ValidateAccountSblgrRefs(){
		if($this->request->getMethod() == 'POST'){
			$account_names_array = $_POST['account_names_array'];
			echo  $this->AccountsModel->validate_account_sblgr_refs($account_names_array);
			die();
	   } 
	 }
	 
	 public  function ajax_search_accounts()
	   {
		   if(!isset($_GET['type']))
			    $type='acc';
		      else
              	$type=$_GET['type'];

		   if(isset($_GET['term']) )
		     echo $response =  $this->AccountsModel->ajax_accounts_search($type,$_GET['term']);
	   } 
	
	public function list(){   
	    $bo_id = $this->session->get('ses_boid');
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		$data['bo_id']           = $bo_id; 
		$data['accounts_list']   = array();
		$data['branch_dropdown'] = $this->CommonModel->all_bo_lists();		
		return view($this->folder_path.'accounts/view',$data);		
    } 
	
	public function ajax_list_groups()
	{
	 echo  $this->AccountsModel->load_list_groups();
	}	
	
    public function list_group()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;		
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;	
		return view($this->folder_path.'accounts/list_group',$data);		
    } 
	
	public function add()
     {   
        $account_opn_not_allowed=[6,7,11,13,8,10,9,12]; // Opening balance not allowed in Parent Group
	    $bo_id = $this->session->get('ses_boid');
		
	     $bo_address = $this->CommonModel->get_comp_ho_adrs_info($this->company_id,$bo_id);
		 if($this->request->getMethod() == 'POST'){		
	        //echo '<pre>';print_r($_POST);exit;
			$account_name       = clean($this->request->getVar('account_name')); 
			$account_alias      = clean($this->request->getVar('account_alias'));
			$account_print_name = clean($this->request->getVar('account_print_name'));
			$account_primary    = $this->request->getVar('account_primary');
			$account_group      = $this->request->getVar('account_group');
			$parent_group       = $this->request->getVar('parent_group');			
			$acct_opp_bal       = (float)$this->request->getVar('acct_opp_bal');
			$memo_opp_bal       = (float)$this->request->getVar('memo_opp_bal');
			$bbbdata            = $this->request->getVar('bbbdata');
			$sezunit            = $this->request->getVar('sezunit');
			
			$bill_by_bill_data  = [];
			if($bbbdata){
			  $bill_by_bill_data = json_decode($bbbdata,true);	
			}
			$sblgr_data            = $this->request->getVar('sblgr_data');
			$sublgr_data  = [];
			if($sblgr_data){
			  $sublgr_data = json_decode($sblgr_data,true);	
			}
			
			if($memo_opp_bal=='')
				 $memo_opp_bal=0;
			$memo_opp_bal_dr_cr = $this->request->getVar('memo_opn_dr_cr');
			
			
			if($acct_opp_bal=='')
				 $acct_opp_bal=0;
			$acct_opp_bal_dr_cr = $this->request->getVar('acct_opp_bal_dr_cr');
			$acct_prv_bal       = (float)$this->request->getVar('acct_prv_bal');
			if($acct_prv_bal=='')			
				$acct_prv_bal=0;
			$acct_prv_dr_cr     = $this->request->getVar('acct_prv_dr_cr');			
			$adrs1              = clean($this->request->getVar('acc_adrs1'));
			$adrs2              = clean($this->request->getVar('acc_adrs2'));
			$country_id         = $this->request->getVar('acc_country_id');	 
			$state_id           = $this->request->getVar('acc_state_id');
			$city               = clean($this->request->getVar('acc_city'));
			$pincode            = clean($this->request->getVar('acc_pincode'));           
			$acct_aadhar        = clean($this->request->getVar('acct_aadhar'));
			$acct_tan           = clean($this->request->getVar('acct_tan'));
			$acct_pan           = clean($this->request->getVar('acct_pan'));
			$acc_jurisdiction   = $this->request->getVar('acc_jurisdiction');
			$acc_email          = clean($this->request->getVar('acc_email'));
			$acct_tel           = clean($this->request->getVar('acct_tel'));						
			$acct_wa_mobile     = clean($this->request->getVar('acct_wa_mobile'));
			$acct_mobile        = clean($this->request->getVar('acct_mobile'));
			$tax_cat_mst_id     = $this->request->getVar('tax_cat_mst_id');
			$acc_gstin          = clean($this->request->getVar('acct_gstin'));
			$acc_sac            = clean($this->request->getVar('hsn'));
			
			$rules = [				
				'account_name' => [
					'label'  => 'Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter name',
					   ]]				  			   
			       ];			
            if(!$this->validate($rules)){
             	$errors_list = $this->validator->getErrors();
				$errors      = array_values($errors_list);
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{

				if($account_primary == 'Y'){
					$crs_mst_is_primary=1;
					$acc_grp_parent_id = $parent_group;
					$under_acc_grp_id     = 0;
					$acc_grp_id        = 0;
					$under_main_grp_id = 0;
					$group_info        = $this->AccountsModel->main_group_info($parent_group);
					$acc_parent_id     = $group_info['acc_grp_parent_id'] ?? $acc_grp_parent_id;
				    if(isset($group_info) && $group_info['crs_mst_is_primary']==1)
						  $under_main_grp_id = $parent_group;
					     else 
						   $under_main_grp_id = 0;
				}
				else{			
				    
					    $crs_mst_is_primary=0;
						$main_group_info     = $this->AccountsModel->main_group_info($account_group);
						
						if($main_group_info){
						    	$acc_grp_parent_id   = $main_group_info['crs_mst_parent_id'];
								$under_acc_grp_id  = $main_group_info['acc_grp_id'];

						    	if($main_group_info['crs_mst_is_primary']==1)
										$under_main_grp_id= $main_group_info['acc_grp_id'];
								else
										$under_main_grp_id= $main_group_info['under_main_id'];
						}

				}
				
				$insert_data   = [
						'comp_id'         => $this->company_id,
						'acc_name'        => ucwords(html_entity_decode(clean($account_name))), //remove special characters
						'acc_name_alias'  => html_entity_decode($account_alias),
						'acc_name_print'  => html_entity_decode($account_print_name),
						'acc_add1'        => $adrs1,
						'acc_add2'        => $adrs2,
						'acc_country'     => $country_id,
						'acc_state'       => $state_id,
						'acc_city'        => $city,						
						'acc_pin'         => $pincode,
						'acc_aadhar'      => $acct_aadhar,
						'acc_tan'         => $acct_tan,
						'acc_pan'         => $acct_pan,
						'acc_jurisd'      => $acc_jurisdiction,
						'acc_email'       => $acc_email,
						'acc_tel'         => $acct_tel,
						'acc_wamobile'    => $acct_wa_mobile,
						'acc_mobile'      => $acct_mobile,
						'tax_cat_mst_id'  => $tax_cat_mst_id,
						'acc_gstin'       => $acc_gstin,
						'acc_sac'         => $acc_sac,
						'sezunit'         => $sezunit
					    ];						
						
			 $response = $this->AccountsModel->add_account($insert_data);		
			if(!$response['status']){
				return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
			}
			else{
				try{ 				
			    $account_id       = $response['account_id'];				
				$mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>1,"crs_mst_id"=>$account_id,"under_crs_mst_id"=>$under_acc_grp_id,
										  "crs_mst_parent_id"=>$acc_grp_parent_id,"under_main_id"=>$under_main_grp_id,
										  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>$crs_mst_is_primary);
				$this->AccountsModel->add_undercrsmt($mst_insert_data);
				SaveErrorLog(json_encode($mst_insert_data));
				
				$acc_op_bal_val   = ($acct_opp_bal_dr_cr === 'dr') ? $acct_opp_bal : (($acct_opp_bal_dr_cr === 'cr') ? -$acct_opp_bal : 0);
			    $acc_pr_bal_val   = ($acct_prv_dr_cr === 'dr') ? $acct_prv_bal : (($acct_prv_dr_cr === 'cr') ? -$acct_prv_bal : 0);
				$acc_op_txn_dr_cr = ($acct_opp_bal_dr_cr === 'dr') ? 1 : (($acct_opp_bal_dr_cr === 'cr') ? 2 : 0);
				
				$memo_op_bal_val   = ($memo_opp_bal_dr_cr === 'dr') ? $memo_opp_bal : (($memo_opp_bal_dr_cr === 'cr') ? -$memo_opp_bal : 0);
			    $memo_op_txn_dr_cr = ($memo_opp_bal_dr_cr === 'dr') ? 1 : (($memo_opp_bal_dr_cr === 'cr') ? 2 : 0);
								
				$opn_bal_data = array("cmp_id"=>$this->company_id,"cmpfymastr_id"=>$this->fy_id,"acc_id"=>$account_id,"acc_op_bal"=>$acc_op_bal_val,
				                      "acc_py_bal"=>$acc_pr_bal_val,"hobo_id"=>$bo_id,"acc_memo_bal"=>$memo_op_bal_val
									  );
				$this->AccountsModel->insert_acc_op_bal($opn_bal_data);
				$acc_txn_date = date('Y-m-d',strtotime(validate_fy_from_date('')));
				$txn_data = array("cmp_id"=>$this->company_id,"acc_id"=>$account_id,"acc_txn_date"=>$acc_txn_date,
				                  "acc_txn_dr_cr"=>$acc_op_txn_dr_cr,"acc_txn_amt"=>abs($acct_opp_bal),"vch_txn_id"=>NULL,"hobo_id"=>$bo_id,
				                  "acc_txn_type"=>1
							      );
				$this->AccountsModel->insert_acc_txn_entry($txn_data);
				/*** for memo balance entry ***/
				$memo_txn_data = array("cmp_id"=>$this->company_id,"acc_id"=>$account_id,"acc_txn_date"=>$acc_txn_date,
				                  "acc_txn_dr_cr"=>$memo_op_txn_dr_cr,"acc_txn_amt"=>abs($memo_opp_bal),"vch_txn_id"=>NULL,"hobo_id"=>$bo_id,
				                 "acc_txn_type"=>3
							      );
				$this->AccountsModel->insert_acc_txn_entry($memo_txn_data);
				
				$this->AccountsModel->Save_BillByBill_OnBalance($account_id,$bill_by_bill_data);
				$this->AccountsModel->Save_SubLedger_OnBalance($account_id,$sublgr_data);
				
				$log = [
			            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
			            'log_date' => date('Y-m-d'),
			            'log_time' => date('H:i:s'),
			            'log_action_tags' => 'add',
			            'log_field_id' => $account_id,
			            'log_field_name' => $account_name,
			            'log_field_type' => 'accounts',
			        ];
			   // $this->LogModel->add_log($log);
				
				
				$accounts_list  = $this->AccountsModel->company_all_accounts();
				CreateJsonFile($this->fy_id,$this->company_id,'acc',$accounts_list);
				return json_encode(['status' => true, 'message' => 'Data Inserted']);
				 }
				catch (\Throwable $e) {
					helper('error');
					$error = formatDbException($e);
					log_message('error', 'DB Error: ' . json_encode($error));
					return json_encode($error);
				} 
				 	
			    }				 
	    	}					 									
	   }	 
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;	
        $data['group_primary_dropdown'] = $this->AccountsModel->group_primary_dropdown();
		$data['group_main_droplist']    = $this->AccountsModel->group_main_dropdownnn();
        $data['CountryDropdown']        = $this->CommonModel->CountryDropdown();
		$data['StatesDropdown']         = $this->CommonModel->StatesDropdown();
		$data['constitution']           = CompanyConstitutions();
		$data['DealerTypeDropdown']     = DealerTypes();
		$data['tax_category']           = $this->AccountsModel->tax_category_dropdown(1);
		$data['base_url']               = $this->base_url;
		$data['bo_address']             = $bo_address;
		return view($this->folder_path.'accounts/add',$data);		
    } 
	
	public function modify($account_id)
    {
		$all_bo_lists  =[];
		$opening_balances = $this->AccountsModel->opening_balances_info($account_id);
        if($opening_balances){
		 $opnbalance       = $opening_balances['acc_op_bal'];
		 $pybalance        = $opening_balances['acc_py_bal'];
		 $memobalance      = $opening_balances['acc_memo_bal'];
		}else{
		 $opnbalance       = 0;
		 $pybalance        = 0;
         $memobalance      = 0;		 
		}
		$account_opn_not_allowed=[6,7,11,13,8,10,9,12]; // Opening balance not allowed in Parent Group
		
		if(!$account_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		$account_info = $this->AccountsModel->account_info($account_id);
		if(!$account_info)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
				
		$acc_grp_parent_id = $account_info['crs_mst_parent_id'];
		if($acc_grp_parent_id==0){
			$main_group_info   = $this->AccountsModel->main_group_info($account_info['under_crs_mst_id']);
			
			if($main_group_info)
			$acc_grp_parent_id = $main_group_info['crs_mst_parent_id'];
		}
	
		if(in_array($acc_grp_parent_id,$account_opn_not_allowed))
			$disable_opening_balances=1;
		  else
			$disable_opening_balances=0;  
		
		$bo_id = $this->session->get('ses_boid');			   
	    $bo_address = $this->CommonModel->get_comp_ho_adrs_info($this->company_id,$bo_id);
	  	if($this->request->getMethod() == 'POST'  && $this->request->isAjax()){	
	  	    
	  	    //echo "<pre>";print_r($_POST);exit;
			$account_name       = clean($this->request->getVar('account_name')); 
			$account_alias      = clean($this->request->getVar('account_alias'));
			$account_print_name = clean($this->request->getVar('account_print_name'));
			$account_primary    = $this->request->getVar('account_primary');
			$account_group      = $this->request->getVar('account_group');
			$parent_group       = $this->request->getVar('parent_group');			
			$acct_symbol        = $this->request->getVar('acct_symbol'); 
			$acct_opp_bal       = (float)$this->request->getVar('acct_opp_bal');
			$acct_opp_bal_dr_cr = $this->request->getVar('acct_opp_bal_dr_cr');
			$acct_prv_bal       = (float)$this->request->getVar('acct_prv_bal');	
			$acct_prv_dr_cr     = $this->request->getVar('acct_prv_dr_cr');			
			$adrs1              = clean($this->request->getVar('acc_adrs1'));
			$adrs2              = clean($this->request->getVar('acc_adrs2'));
			$country_id         = $this->request->getVar('acc_country_id');	 
			$state_id           = $this->request->getVar('acc_state_id');
			$city               = $this->request->getVar('acc_city');
			$pincode            = clean($this->request->getVar('acc_pincode'));           
			$acct_aadhar        = clean($this->request->getVar('acct_aadhar'));
			$acct_tan           = clean($this->request->getVar('acct_tan'));
			$acct_pan           = clean($this->request->getVar('acct_pan'));
			$acc_jurisdiction   = $this->request->getVar('acc_jurisdiction');
			$acc_email          = clean($this->request->getVar('acc_email'));
			$acct_tel           = clean($this->request->getVar('acct_tel'));						
			$acct_wa_mobile     = clean($this->request->getVar('acct_wa_mobile'));
			$acct_mobile        = clean($this->request->getVar('acct_mobile'));	
			$memo_opp_bal       = (float)$this->request->getVar('memo_opp_bal');
			$tax_cat_mst_id     = $this->request->getVar('tax_cat_mst_id');
			$acc_gstin          = clean($this->request->getVar('acct_gstin'));
			$acc_sac            = clean($this->request->getVar('hsn'));			
			$bbbdata            = $this->request->getVar('bbbdata');
			$sezunit            = $this->request->getVar('sezunit');
			
			$bill_by_bill_data  = [];
			if($bbbdata){
			  $bill_by_bill_data = json_decode($bbbdata,true);	
			}
			
			$sblgr_data            = $this->request->getVar('sblgr_data');
			$Sublgr_data  = [];
			if($sblgr_data){
			  $Sublgr_data = json_decode($sblgr_data,true);	
			}
			
			
			if($memo_opp_bal=='')
				 $memo_opp_bal=0;
			$memo_opp_bal_dr_cr = $this->request->getVar('memo_opn_dr_cr');	
			$rules = [				
				'account_name' => [
					'label'  => 'Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter name',
					   ]]
				  			   
			       ];
			
            if(!$this->validate($rules)){
                $errors_list = $this->validator->getErrors();
				$errors      = array_values($errors_list);
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
             }else{			
			   $exists_data=array('acc_name' => strtolower(html_entity_decode(clean($account_name))),
								 'acc_name_alias'  => strtolower(html_entity_decode(clean($account_alias)))
						        );
			   $exists_response = $this->AccountsModel->exists_account($exists_data,$account_id);
		       if(!$exists_response['status']){        				
        		 return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$exists_response['message']]]);
        	    }			
			    if($account_primary == 'Y'){
					$crs_mst_is_primary=1;
					$acc_grp_parent_id = $parent_group;
					$under_acc_grp_id   =0;
					$acc_grp_id        = 0;
					$under_main_grp_id = 0;
					$group_info    = $this->AccountsModel->main_group_info($parent_group);
					$acc_parent_id =0;
					if($group_info){						
						if($group_info['crs_mst_is_primary']==1)
						  $under_main_grp_id = $parent_group;
					     else 
						   $under_main_grp_id = $group_info['under_crs_mst_id'] ?? 0;	 
					}
				}
				else{					
					$crs_mst_is_primary=0;
					$acc_grp_id        = $account_group;
					$main_group_info        = $this->AccountsModel->main_group_info($acc_grp_id);
					$acc_grp_parent_id   = $main_group_info['crs_mst_parent_id'];
					$under_acc_grp_id  = $main_group_info['acc_grp_id'];
					$under_main_grp_id = 0;
					if($main_group_info['crs_mst_is_primary']==1)
						$under_main_grp_id= $main_group_info['acc_grp_id'];
					else
					    $under_main_grp_id= $main_group_info['under_main_id'];
				
				  
					 }
				
					try{
						$acc_op_bal_val   = ($acct_opp_bal_dr_cr === 'dr') ? $acct_opp_bal : (($acct_opp_bal_dr_cr === 'cr') ? -$acct_opp_bal : 0);
						$acc_pr_bal_val   = ($acct_prv_dr_cr === 'dr') ? $acct_prv_bal : (($acct_prv_dr_cr === 'cr') ? -$acct_prv_bal : 0);
						$acc_op_txn_dr_cr = ($acct_opp_bal_dr_cr === 'dr') ? 1 : (($acct_opp_bal_dr_cr === 'cr') ? 2 : 0);

						$memo_op_bal_val   = ($memo_opp_bal_dr_cr === 'dr') ? $memo_opp_bal : (($memo_opp_bal_dr_cr === 'cr') ? -$memo_opp_bal : 0);
						$memo_op_txn_dr_cr = ($memo_opp_bal_dr_cr === 'dr') ? 1 : (($memo_opp_bal_dr_cr === 'cr') ? 2 : 0);
				
						$update_acc_data   = [
								'acc_name'          => $account_name,
								'acc_alias'         => $account_alias,
								'acc_print_name'    => $account_print_name,
								'tax_cat_mst_id'    => $tax_cat_mst_id,
								'contact_id'        => 0						
								];
						   $response = $this->AccountsModel->update_account($update_acc_data,$account_id);
						    $mst_update_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>1,"crs_mst_id"=>$account_id,"under_crs_mst_id"=>$acc_grp_id,
														"crs_mst_parent_id"=>$acc_grp_parent_id,"under_main_id"=>$under_main_grp_id,
														"cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>$crs_mst_is_primary);
							$this->AccountsModel->update_undercrsmt($mst_update_data,$account_id,1);				   
						  if(!$response['status']){        				
								  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
							}
							else{
								$account_id = $response['account_id'];
								
								$adrs_data   = [
								        'acc_name'        => $account_name,
										'acc_email'       => $acc_email,
										'acc_wamobile'    => $acct_wa_mobile,
										'acc_mobile'      => $acct_mobile,
										'acc_add1'        => $adrs1,
										'acc_add2'        => $adrs2,
										'acc_country'     => (int)$country_id,
										'acc_state'       => (int)$state_id,
										'acc_city'        => $city,						
										'acc_pin'         => (int)$pincode,
										];	
								$this->AccountsModel->update_account_adrs_info($adrs_data,$account_id);				
								
								$update_details_data   = [
								'acc_aadhaar'        => $acct_aadhar,
								'acc_tan'            => $acct_tan,
								'acc_pan'            => $acct_pan,
								'acc_it_jurisd'      => (int)$acc_jurisdiction,
								'acc_gstin'          => $acc_gstin,
					         	'acc_sac'            => $acc_sac,
								'acc_is_sys_acc'     => (int)0,
						         'acc_is_sez'        => (int)$sezunit ?? 0 
								];
								$this->AccountsModel->update_account_details($update_details_data,$account_id);
								 
                                $opn_data = array(
										      "acc_op_bal"=>$acc_op_bal_val,"acc_py_bal"=>$acc_pr_bal_val,"acc_memo_bal"=>$memo_op_bal_val
										     );
								$this->AccountsModel->update_opbal_entry($opn_data,$account_id);
								
								$txn_data = array("acc_txn_dr_cr"=>$acc_op_txn_dr_cr,"acc_txn_amt"=>abs($acc_op_bal_val));
								$this->AccountsModel->update_acc_txn_opbal_entry($txn_data,$account_id,1);
								
								$memo_txn_data = array("acc_txn_dr_cr"=>$memo_op_txn_dr_cr,"acc_txn_amt"=>abs($memo_op_bal_val));
								$this->AccountsModel->update_acc_txn_opbal_entry($memo_txn_data,$account_id,3);
						         
						        $this->AccountsModel->Save_BillByBill_OnBalance($account_id,$bill_by_bill_data);
						        $this->AccountsModel->Save_SubLedger_OnBalance($account_id,$Sublgr_data);
						        
								
								$fy_id      = $this->fy_id;						 
								$log = [
										'uuid_aicountly' => $this->session->get('uuid_aicountly'),
										'log_date' => date('Y-m-d'),
										'log_time' => date('H:i:s'),
										'log_action_tags' => 'update',
										'log_field_id' => $account_id,
										'log_field_name' => $account_name,
										'log_field_type' => 'accounts',
									];
							   // $this->LogModel->add_log($log);
							   $accounts_list  = $this->AccountsModel->company_all_accounts();
							   CreateJsonFile($fy_id,$this->company_id,'acc',$accounts_list);					   
							   return json_encode(['status' => true, 'message' => 'Data Updated']);    				
							}				 		
						 }catch (\Throwable $e) {
							log_message('error', 'DB Query Error: ' . $e->getMessage());
							return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
						} 
			 }					 									
		  }	 		
		if(!$account_info){
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
		$get_opn_balance_info = $this->AccountsModel->acc_opn_balance_info($account_id);
		if(!$get_opn_balance_info){
		  $sel_acc_op_bal = '0';
		  $sel_acc_op_type = '';
		  
		  $sel_acc_py_bal = '0';
		  $sel_acc_py_type = '';
		  
		  $sel_memo_bal = '0';
		  $sel_memo_type = '';
		  
	     }
	   else{
		if(isset($get_opn_balance_info['acc_op_bal']) && $get_opn_balance_info['acc_op_bal']!='')   
		$sel_acc_op_bal  = abs($get_opn_balance_info['acc_op_bal']);
	    else
		$sel_acc_op_bal  =0;	
		$sel_acc_op_type = ($get_opn_balance_info['acc_op_bal']<0)?'cr':'dr';
		
		if(isset($get_opn_balance_info['acc_py_bal']) && $get_opn_balance_info['acc_py_bal']!='')   
		$sel_acc_py_bal  = abs($get_opn_balance_info['acc_py_bal']);
	    else
		$sel_acc_py_bal  =0;	
		$sel_acc_py_type = ($get_opn_balance_info['acc_py_bal']<0)?'cr':'dr';		
	    
		if(isset($get_opn_balance_info['acc_memo_bal']) && $get_opn_balance_info['acc_memo_bal']!='')   
		$sel_memo_bal  = abs($get_opn_balance_info['acc_memo_bal']);
	    else
		$sel_memo_bal  =0;	
		$sel_memo_type = ($get_opn_balance_info['acc_memo_bal']<0)?'cr':'dr';
		
		} 
		$data['sel_memo_type']         = $sel_memo_type;
		$data['sel_memo_bal']          = $sel_memo_bal;		
		$data['all_bo_lists']          = count($all_bo_lists);
		$data['bo_lists']              = $all_bo_lists;
        $data['sel_acc_op_bal']        = $sel_acc_op_bal;
		$data['dis_opng_bal']          = $disable_opening_balances;
		$data['sel_acc_op_type']       = $sel_acc_op_type;
        $data['sel_acc_py_bal']        = $sel_acc_py_bal;
        $data['sel_acc_py_type']       = $sel_acc_py_type;
		$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;	
        $data['group_main_dropdown']   = $this->AccountsModel->group_main_dropdown();
		$data['group_main_droplist']   = $this->AccountsModel->group_main_dropdownnn();
        $data['group_primary_dropdown']= $this->AccountsModel->group_primary_dropdown();		
		$data['account_info']          = $this->AccountsModel->account_info($account_id);
		
		$data['tax_category']           = $this->AccountsModel->tax_category_dropdown(1);
        $data['account_addrs_info']    = [];//$this->AccountsModel->account_adrs_info($account_id);
		$data['CountryDropdown']       = $this->CommonModel->CountryDropdown();
		$data['StatesDropdown']        = $this->CommonModel->StatesDropdown();
		$data['base_url']              = $this->base_url;
		$data['account_id']            = $account_id;		
		$data['bo_address']            = $bo_address;
		$data['opnbalance']            = $opnbalance;
		$data['pybalance']             = $pybalance;
		$data['memobalance']           = $memobalance;
		$data['crsmaster_info']        = $this->AccountsModel->crsmst_info($account_id);
		$data['bbb_data']              = $this->AccountsModel->bill_by_bill_txn($account_id);
		$data['sblgr_data']            = $this->AccountsModel->sblgr_txn($account_id);
		$data['bills_method_list']     = ['','New Ref.','Adjustment'];
		return view($this->folder_path.'accounts/edit',$data);		
    }
	
	public function remove_accounts($ids_info){
		if(!$ids_info)
			return redirect()->to($this->base_url.'accounts'); 
		
		 $ids = base64_decode($ids_info);  
		 
		 $default_groups = [];//[1,2,3,4,5];
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 $ids2  = array_unique($ids2);
		 foreach($ids2 as $account_id){
		     if($account_id >0){
		     $stat = true;
		     $name = $this->AccountsModel->get_account_name($account_id);						 
		     if($name != ''){				
					if (in_array($account_id, $default_groups)){ 
					 $stat = false;
					 array_push($errors, 'Failed! Account "'.$name.'" belongs to Default Accounts');
					 }
					 if ($this->AccountsModel->check_account_isrectricted($account_id)){
						 $stat = false;
						 array_push($errors, 'Failed! Account "'.$name.'" is a system generated A/C');
					 }
					 
					 if ($this->AccountsModel->check_account_with_voucher($account_id)){
						 $stat = false;
						 array_push($errors, 'Failed! Account "'.$name.'" has one or more associated Vouchers');
					 } 
					 if ($this->AccountsModel->check_account_with_bbb($account_id)){
						 $stat = false;
						 array_push($errors, 'Failed! Account "'.$name.'" has one or more associated Bill By Bill');
					 } 
					 if ($this->AccountsModel->check_account_with_sublger($account_id)){
						 $stat = false;
						 array_push($errors, 'Failed! Account "'.$name.'" has one or more associated Sub Ledger');
					 }
			    
			     if($stat)
			     {
			         $log = [
			            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
			            'log_date' => date('Y-m-d'),
			            'log_time' => date('H:i:s'),
			            'log_action_tags' => 'delete',
			            'log_field_id' => $account_id,
			            'log_field_name' => $name,
			            'log_field_type' => 'account group',
			        ];
		            // $this->LogModel->add_log($log);		            
		            $this->AccountsModel->remove_single_accounts($account_id);
					
			       }
		        }
			 }			 
		 }
		 
		  $fy_id          = $this->fy_id;
		  $accounts_list  = $this->AccountsModel->company_all_accounts();
		  CreateJsonFile($fy_id,$this->company_id,'acc',$accounts_list);
		 	     
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Not Deleted', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	  }
	  
	  
   	public function change_status(){
   	    $status_ids    = $this->request->getVar('pss_status_val'); 
		$ids_info      = $this->request->getVar('accidids');
		
		 $ids = base64_decode($ids_info);  
		 
		 // when activate master then check is it any master exisst ofthe same name or not 
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 $ids2  = array_unique($ids2);
		 
		
		 foreach($ids2 as $key => $account_id){
		     if($account_id >0){
		     $stat = true;
		     $name = $this->AccountsModel->get_account_name($account_id);
		     $status = $status_ids[0];
		     if($name != ''){		
					if ($this->AccountsModel->check_account_isrectricted($account_id)){
						 $stat = false;
						 array_push($errors, 'Failed! Account "'.$name.'" is a system generated A/C');
					 }	
					if($status==1){
					 if ($this->AccountsModel->check_account_exists($account_id,$name)){
						 $stat = false;
						 array_push($errors, 'Failed! Account "'.$name.'" already exists');
					 } 
					}
					 if($status==0){					  
					 if ($this->AccountsModel->check_account_txn_exists($account_id)){
						 $stat = false;
						 array_push($errors, 'Failed! Account "'.$name.'" has one or more associated vouchers');
					 } 
					 
					 if ($this->AccountsModel->check_account_opn_exists($account_id)){
						 $stat = false;
						 array_push($errors, 'Failed! Account "'.$name.'" has default balances');
					 } 
					 }
					 
			     if($stat)
			     {	   
		           $this->AccountsModel->changestatus_single_accounts($account_id,$status);
					
			       }
		        }
			 }			 
		 }
	
		  
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	  }	  
	  
	public function group_change_status(){
   	    $status_ids    = $this->request->getVar('pss_status_val'); 
		$ids_info      = $this->request->getVar('accidids');
		
		 $ids = base64_decode($ids_info);  
		 
		 // when activate master then check is it any master exisst ofthe same name or not 
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 $ids2  = array_unique($ids2);
		 
		
		 foreach($ids2 as $key => $account_id){
		     if($account_id >0){
		     $stat = true;
		     $name = $this->AccountsModel->get_account_group_name($account_id);
		     $status = $status_ids[0];
		     if($name != ''){		
					if($status==1){
					  if ($this->AccountsModel->check_account_group_exists($account_id,$name)){
						 $stat = false;
						 array_push($errors, 'Failed! Group "'.$name.'" already exists');
					   } 
					}
			     if($stat)
			     {	   
		           $this->AccountsModel->changestatus_single_group($account_id,$status);
					
			       }
		        }
			 }			 
		 }
	
		  
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	  }	 
	  
	
	public function add_group()
    {
		  if($this->request->getMethod() == 'POST'){	
		 		$rules = [				
					'group_name' => [
						'label'  => 'Group Name',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Group Name is required',
					   ],
				  ],
					'group_name_alias' => [
						'label'  => 'Alias Name',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Alias Name is required',
					  ],
				  ],
				  'primary_group' => [
						'label'  => 'Primary',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Primary is required'
					  ],
				  	],
			  ];
				$errors = [];
				if(!$this->validate($rules)){
						$errors_list = $this->validator->getErrors();
						$errors  =array_values($errors_list);						
						return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
				}
				$group_name       = clean($this->request->getVar('group_name')); 
				$group_name_alias =  clean($this->request->getVar('group_name_alias'));
				$primary_group    = $this->request->getVar('primary_group');
				if($primary_group=='Y'){
						$rules = [				
							'yes_group_under' => [
								'label'  => 'Under',
								'rules'  => 'required',
								'errors' => [
									'required' => 'Under is required',
							   ],
						  ],
						];
				}
				else{
						$rules = [				
							'no_group_under' => [
								'label'  => 'Under',
								'rules'  => 'required',
								'errors' => [
									'required' => 'Under is required',
							   ],
						  ],
						];
				}

				 if(!$this->validate($rules)){
						$errors_list = $this->validator->getErrors();
						$errors  =array_values($errors_list);
	        	       return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
                 }
				if($primary_group=='Y'){
					    $crs_mst_is_primary=1;
						$acc_grp_parent_id    = $this->request->getVar('yes_group_under');
						$under_acc_grp_id     = 0;
						$group_info           = $this->AccountsModel->main_group_info($acc_grp_parent_id);
					    if(isset($group_info) && $group_info['crs_mst_is_primary']==1)
						  $under_main_grp_id = $acc_grp_parent_id;
					    else 
						   $under_main_grp_id = 0;					   
						
						if($this->AccountsModel->check_parent_restriction($acc_grp_parent_id, 1)){
							$primary_groups = $this->AccountsModel->get_parimary_groups($acc_grp_parent_id);
							if(!empty($primary_groups) && count($primary_groups) > 0)
									return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Parent Group can not have more than one Primary Groups']]);
						}
				}
				else{
						$crs_mst_is_primary=0;
						$no_group_under    = $this->request->getVar('no_group_under');
						$main_group_info     = $this->AccountsModel->main_group_info($no_group_under);
						if($main_group_info){
								$acc_grp_parent_id   = $main_group_info['crs_mst_parent_id'];
								$under_acc_grp_id    = $main_group_info['acc_grp_id'];
								
								if($main_group_info['crs_mst_is_primary']==1)
										$under_main_grp_id= $main_group_info['acc_grp_id'];
								else
										$under_main_grp_id= $main_group_info['under_main_id'];
								
						}
						else{
						
							$errors = $this->validator->getErrors();
	        				return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Something went wrong with Under Group']]);
						}
						
				}
    
	
        	        	$insert_data   = [
							'cmp_id'         => $this->company_id,
							'acc_grp_name'   => clean($group_name),
							'acc_grp_alias'  => clean($group_name_alias)							
				  	    ];					
				  	   $group_id = $this->AccountsModel->add_group($insert_data);		
						if($group_id=='-1'){
								
								return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Group name already exists.']]);			 
						}
						else if($group_id=='-2'){
								
								return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Group alias already exists.']]);			 
						}
						else{		
							$mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>2,"crs_mst_id"=>$group_id,"under_crs_mst_id"=>$under_acc_grp_id,
										  "crs_mst_parent_id"=>$acc_grp_parent_id,"under_main_id"=>$under_main_grp_id,
										  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>$crs_mst_is_primary);
			            	$this->AccountsModel->add_undercrsmt($mst_insert_data);
						
								$log = [
										'uuid_aicountly' => $this->session->get('uuid_aicountly'),
										'log_date' => date('Y-m-d'),
										'log_time' => date('H:i:s'),
										'log_action_tags' => 'add',
										'log_field_id' => $group_id,
										'log_field_name' => $group_name,
										'log_field_type' => 'account group',
								];
							//	$this->LogModel->add_log($log);
					     	return json_encode(['status' => true, 'message' => 'Data Inserted']);
			         	   } 									
			}			
				 
			$data['message_output']             = $this->message_output;
			$data['session']                    = $this->session;
			$data['folder_path']                = $this->folder_path;	
			$data['group_primary_dropdown']     = $this->AccountsModel->group_primary_dropdown();
			$data['base_url']        		    = $this->base_url;
			$data['group_main']      		    = $this->AccountsModel->group_main_dropdown();
			$data['user_groups_dropdown']       = $this->AccountsModel->group_main_dropdown();
			return view($this->folder_path.'accounts/add_group',$data);		
    }
	
	public function remove_groups($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'accounts/list_group'); 
		 
		 $default_groups = [];
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 foreach($ids2 as $account_id){
		     
		     $stat = true;
		     if (in_array($account_id, $default_groups)){ //check if group belongs to defaults
		         $stat = false;
		         array_push($errors, 'Failed! Account Group ID = '.$account_id.' belongs to Default Group');
		      }
		      if($this->AccountsModel->check_group_restriction($account_id, 1)){
							$stat = false;
		         	$account_name = $this->AccountsModel->get_account_group_name($account_id);
		         	array_push($errors, 'Failed! Group "'.$account_name.'" has been restricted to delete');
					}
					if($this->AccountsModel->sub_group_info($account_id)){
							$stat = false;
		         	$account_name = $this->AccountsModel->get_account_group_name($account_id);
		         	array_push($errors, 'Failed! Group "'.$account_name.'" has one or more associated sub groups');
					}
		      if ($this->AccountsModel->check_account_with_group($account_id)){ //check if account exist with that group
		         $stat = false;
		         $account_name = $this->AccountsModel->get_account_group_name($account_id);
		         array_push($errors, 'Failed! Account Group "'.$account_name.'" has one or more associated accounts');
		       }/*
		       if ($this->AccountsModel->check_bill_sundry_with_group($account_id)){ //check if account exist with that group
		         $stat = false;
		         $account_name = $this->AccountsModel->get_account_group_name($account_id);
		         array_push($errors, 'Failed! Account Group "'.$account_name.'" has one or more associated bill sundry accounts');
		       }*/
		      
		     if($stat)
		     {
		         $log = [
		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
		            'log_date' => date('Y-m-d'),
		            'log_time' => date('H:i:s'),
		            'log_action_tags' => 'delete',
		            'log_field_id' => $account_id,
		            'log_field_name' => $this->AccountsModel->get_account_group_name($account_id),
		            'log_field_type' => 'account group',
		        ];
	          //  $this->LogModel->add_log($log);
	            
	            $this->AccountsModel->remove_single_groups($account_id);				
		     }
		 }
		 if(count($errors)){
		         $this->session->setFlashdata('error_array_message', $errors);
		         
		  }
		 // return redirect()->to($this->base_url.'accounts/list_group');
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
	  }
	  
	public function modify_group($group_id)
    { 

		if(!$group_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();	
		  $main_group_info = $this->AccountsModel->main_group_info($group_id);
			if(!$main_group_info){
				throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
			}

		if($this->request->getMethod() == 'POST' && $this->request->isAjax()){	
		 		$rules = [				
					'group_name' => [
						'label'  => 'Group Name',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Group Name is required',
					   ],
				  ],
					'group_name_alias' => [
						'label'  => 'Alias Name',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Alias Name is required',
					  ],
				  ],
				  'primary_group' => [
						'label'  => 'Primary',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Primary is required'
					  ],
				  	],
			  ];
				$errors = [];
	        if(!$this->validate($rules)){
	        	$errors_list = $this->validator->getErrors();
				$errors      = array_values($errors_list);
        		return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

				$group_name       =  clean($this->request->getVar('group_name')); 
				$group_name_alias =  clean($this->request->getVar('group_name_alias'));
				$primary_group    =  $this->request->getVar('primary_group');

				if($primary_group=='Y'){
						$rules = [				
							'yes_group_under' => [
								'label'  => 'Under',
								'rules'  => 'required',
								'errors' => [
									'required' => 'Under is required',
							   ],
						  ],
						];
				}
				else{
						$rules = [				
							'no_group_under' => [
								'label'  => 'Under',
								'rules'  => 'required',
								'errors' => [
									'required' => 'Under is required',
							   ],
						  ],
						];
				}

				if(!$this->validate($rules)){
						$errors_list = $this->validator->getErrors();
						$errors  =array_values($errors_list);						
	        }

        	    $old_group_data = $this->AccountsModel->main_group_info($group_id);
				if($primary_group=='Y'){
					    $crs_mst_is_primary   = 1;
						$acc_grp_parent_id    = $this->request->getVar('yes_group_under');
						$under_acc_grp_id     = 0;
						$group_info           = $this->AccountsModel->main_group_info($acc_grp_parent_id);
					    if(isset($group_info) && $group_info['crs_mst_is_primary']==1)
						  $under_main_grp_id = $acc_grp_parent_id;
					    else 
						   $under_main_grp_id = 0;

						/* if($old_group_data['crs_mst_is_primary'] == 1 && $old_group_data['crs_mst_parent_id'] == $acc_grp_parent_id)
						{
							// no problem
						}
						else
						{
							// check sub groups
							$sub_group_data = $this->AccountsModel->sub_group_info($group_id);
							if($sub_group_data){
									$errors[] = 'One or more Sub Group exists under this Group';
							}
						} */

						if($this->AccountsModel->check_parent_restriction($acc_grp_parent_id, 1)){
								$primary_groups = $this->AccountsModel->get_parimary_groups($acc_grp_parent_id, $group_id);
								if(!empty($primary_groups) && count($primary_groups) > 0)
									$errors[] = 'Parent Group can not have more than one Primary Groups';
						}
				}
				else{
						$crs_mst_is_primary=0;
						$no_group_under    = $this->request->getVar('no_group_under');
						$main_group_info     = $this->AccountsModel->main_group_info($no_group_under);
						
						if($main_group_info){
								$acc_grp_parent_id   = $main_group_info['crs_mst_parent_id'];
								$under_acc_grp_id  = $main_group_info['acc_grp_id'];

								if($main_group_info['crs_mst_is_primary']==1)
										$under_main_grp_id= $main_group_info['acc_grp_id'];
								else
										$under_main_grp_id= $main_group_info['under_main_id'];

								/* if($old_group_data['crs_mst_is_primary'] == 0 && $old_group_data['under_crs_mst_id'] == $under_acc_grp_id)
								{
									// no problem
								}
								else
								{
									// check sub groups
									$sub_group_data = $this->AccountsModel->sub_group_info($group_id);
									if($sub_group_data){
											$errors[] = 'One or more Sub Group exists under this Group';
									}
								} */
						}
				}

				
	        if(empty($errors))
	        {

						$update_data = [
							'acc_grp_name'      => ucwords($group_name),
							'acc_grp_alias'    => $group_name_alias
						];			
           
            	$this->AccountsModel->update_group($update_data,$group_id);	
				
				// if group Under is changed then auto change all the masters 	
				$this->AccountsModel->moveGroupSubtree(
				$old_group_data['crs_mst_parent_id'], // GRP-1 being moved
				$acc_grp_parent_id,    // CAPITAL A/C
				$acc_grp_parent_id,    // ultimate root for the whole subtree
				($primary_group == 'Y') ? 1 : 0     // 2544 is no longer primary
			);
							 
		        $mst_update_data  = array("cmp_id"=>$this->company_id,"crs_mst_id"=>$group_id,"crs_mst_type"=>2,"under_crs_mst_id"=>$under_acc_grp_id,
														"crs_mst_parent_id"=>$acc_grp_parent_id,"under_main_id"=>$under_main_grp_id,
														"cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>$crs_mst_is_primary);
				
				$this->AccountsModel->update_undercrsmt($mst_update_data,$group_id,2);
				
		        $log = [
			            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
			            'log_date' => date('Y-m-d'),
			            'log_time' => date('H:i:s'),
			            'log_action_tags' => 'update',
			            'log_field_id' => $group_id,
			            'log_field_name' => $group_name,
			            'log_field_type' => 'account group',
			      ];
			    	//$this->LogModel->add_log($log);

					
			    	return json_encode(['status' => true, 'message' => 'Data Updated']);
			    	
	        }
	        else{
	        	
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }
		   
		}	 
		   
			$data['get_info']         = $main_group_info;
			$data['message_output']   = $this->message_output;
			$data['session']          = $this->session;
			$data['folder_path']      = $this->folder_path;
			$data['group_id']         = $group_id;		
			$data['group_main']       = $this->AccountsModel->group_main_dropdown($group_id);
			$data['group_primary_dropdown']   = $this->AccountsModel->group_primary_dropdown();
			$data['base_url']         = $this->base_url;		
	    return view($this->folder_path.'accounts/edit_group',$data);		
    }
	
	public function LoadAccountTotals(){
		$view            = $this->request->getGet("view");		
		$account_id      = $this->request->getGet("account_id");
		$isconsoview     = $this->request->getGet("isconsoview");		
		$from_date       = date("Y-m-d",strtotime($this->request->getGet("from_date")));
		$to_date         = date("Y-m-d",strtotime($this->request->getGet("to_date")));
        echo $response    = $this->AccountsModel->load_accounts_totals($account_id,$from_date, $to_date,$isconsoview);
	    die();
	 }
	
	public function LoadAccountSubLedgerTotals(){
		$view            = $this->request->getGet("view");		
		$account_id      = $this->request->getGet("account_id");		
		$from_date       = date("Y-m-d",strtotime($this->request->getGet("from_date")));
		$to_date         = date("Y-m-d",strtotime($this->request->getGet("to_date")));
       echo $response = $this->AccountsModel->load_accounts_sblgr_totals($account_id,$from_date, $to_date);
	   die();
	}
	
	public function ajax_ledger_detail()
	{
		$m          = (int) ($this->request->getVar('m') ?? 0);
		$consoview  = (int) ($this->request->getVar('consoview') ?? 0);
		$view       = (int) ($this->request->getVar('view') ?? 0);
		$account_id = (int) $this->request->getVar('id');
		$type       = (int) ($this->request->getVar('type') ?? 1);

		// Safe date parsing with fallbacks
		$from_raw = $this->request->getVar('from_date');
		$to_raw   = $this->request->getVar('to_date');
		$from_date = $from_raw ? date('Y-m-d', strtotime($from_raw)) : date('Y-01-01');
		$to_date   = $to_raw   ? date('Y-m-d', strtotime($to_raw))   : date('Y-m-d');

		$is_export = 0;

		if ($m === 1) {
			$response = $this->AccountsModel->load_accounts_memo_ledger($account_id, '-1', $from_date, $to_date, 1, '');
		} else {
			if ($consoview === 1) {
				$response = $this->AccountsModel->load_accounts_ledger_consolidated($account_id, $from_date, $to_date, $is_export, $type);
			} else {
				if ($view === 1) {
					$response = $this->AccountsModel->load_accounts_ledger_detailed($account_id, $from_date, $to_date, $is_export, $type);
				} elseif ($view === 0) {
					$response = $this->AccountsModel->load_accounts_ledger_condensed($account_id, $from_date, $to_date, $is_export, $type);
				} elseif ($view === 3) {
					$response = $this->AccountsModel->load_accounts_ledger_condensed_with_columns(
						$account_id,
						$from_date,
						$to_date,
						$is_export,
						$type
					);
				} else {
					// Fallback to condensed
					$response = $this->AccountsModel->load_accounts_ledger_condensed($account_id, $from_date, $to_date, $is_export, $type);
				}
			}
		}

		return $this->response->setJSON($response);
   }
	
	public function ajax_subledger_detail(){
		$m               = $this->request->getVar("m");
		$consoview       = $this->request->getVar("consoview");
		$view            = $this->request->getVar("view") ?? 0;
		$account_id      = $this->request->getVar("id");
		$type            = $this->request->getVar("type") ?? 1;	
		$from_date       = date("Y-m-d",strtotime($this->request->getVar("from_date")));
		$to_date         = date("Y-m-d",strtotime($this->request->getVar("to_date")));
		$is_export       = 0;
		
		$response = $this->AccountsModel->load_accounts_subledger_condensed($account_id,$from_date, $to_date,$is_export,$type);
	    /*  if($m == 1){
			$response    = $this->AccountsModel->load_accounts_memo_ledger($account_id, '-1', $from_date, $to_date,1,'');
		}
		else{
			 if($view == 1)
				$response = $this->AccountsModel->load_accounts_ledger_detailed($account_id,$from_date, $to_date,$is_export,$type);
			 else if($view==0) 
				$response = $this->AccountsModel->load_accounts_ledger_condensed($account_id,$from_date, $to_date,$is_export,$type);
		  }	 */

		echo $response;
	}
	
	public function ledger_detail($account_id){
        $all_columns = array();
		$m           = !empty($_GET['m']) ? $_GET['m'] : 0;
		$view        = !empty($_GET['view']) ? $_GET['view'] : 0;		
		$consoview   = !empty($_GET['consoview']) ? $_GET['consoview'] : 0;
		$type        = !empty($_GET['type']) ? $_GET['type'] : 1;		
		$from_date   = $_GET['from_date'] ?? '';
		$to_date     = $_GET['to_date'] ?? '';
		$from_date   = validate_from_date($from_date);
		$to_date     = validate_to_date($to_date);		
		$from_datem  = date('Y-m-d',strtotime($from_date));
		$to_datem    = date('Y-m-d',strtotime($to_date));
		$total_credit=$total_debit=0;
		$master_type_info  = $this->AccountsModel->get_acc_master_type($account_id); 
	   
	    if(!$master_type_info)
		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
	  $mster_type ='acc';
	  if((int)$master_type_info['bsd_id'] >0)
		  $mster_type ='bsd';
	  
	    $account_info = $this->AccountsModel->account_info($account_id,$mster_type);
		
	    if(empty($account_info)){
	  	   throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
	    } 
		
	    $account_name = $account_info['acc_name'];	  
        if($view==2){
		 $acc_op_bal          = 0;//$this->AccountsModel->account_opening_balance_ho($account_id,date('Y-m-d', strtotime($from_date)));	
		 $clo_acc_op_bal      = 0;//$this->AccountsModel->account_closing_balance_ho($account_id,date('Y-m-d', strtotime($to_date)));
		}else{
		 $acc_op_bal          = $this->AccountsModel->account_opening_balance($account_id,date('Y-m-d', strtotime($from_date)),$type);
		}
		
		if($view==3){
			$response = json_decode($this->AccountsModel->load_accounts_ledger_condensed_with_columns($account_id,date('Y-m-d', strtotime($from_date)),date('Y-m-d', strtotime($to_date)),0,$type),true);
			foreach($response['acc_columns'] as $kk => $cols){
				  $all_columns[$kk] = $cols['acc_name'];	
				}
		  }
			  
		if($acc_op_bal < 0)
          $opening_balance = formatAmount(abs($acc_op_bal)).' CR';
        else
          $opening_balance = formatAmount($acc_op_bal).' DR';
		

		if($m == 1){
			$opening_balance = '0.00 DR';
			$clo_balance = '0.00 DR';
		}
		
		$accounts_list   = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
	    $data = [
			'account_name'		=> $account_name,
			'account_id'	    => $account_id,
			'opening_balance'   => $opening_balance,
			'from_date'			=> $from_date,
			'to_date'			=> $to_date,
			'm'					=> $m,
			'view'			    => $view,
			'consoview'			=> $consoview,
			'all_columns'       => $all_columns,
			'bo_id'             => $this->bo_id,
			'type'              => $type,
			'accounts_list'     => $accounts_list
	       ];			  
	   return view($this->folder_path.'accounts/account_ledger_listing',$data);      
   }
   
   
    public function bsd_ledger_detail($account_id){
        $all_columns = array();
		$m           = !empty($_GET['m']) ? $_GET['m'] : 0;
		$view        = !empty($_GET['view']) ? $_GET['view'] : 0;		
		$consoview   = !empty($_GET['consoview']) ? $_GET['consoview'] : 0;
		$type        = !empty($_GET['type']) ? $_GET['type'] : 1;		
		$from_date   = $_GET['from_date'] ?? '';
		$to_date     = $_GET['to_date'] ?? '';
		$from_date   = validate_from_date($from_date);
		$to_date     = validate_to_date($to_date);		
		$from_datem  = date('Y-m-d',strtotime($from_date));
		$to_datem    = date('Y-m-d',strtotime($to_date));
		$total_credit=$total_debit=0;
	    $account_info = $this->AccountsModel->account_info($account_id,'bsd');
	   
	    if(empty($account_info)){
	  	   throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
	    }
	    $account_name = $account_info['acc_name'];	  
        if($view==2){
		 $acc_op_bal          = 0;//$this->AccountsModel->account_opening_balance_ho($account_id,date('Y-m-d', strtotime($from_date)));	
		 $clo_acc_op_bal      = 0;//$this->AccountsModel->account_closing_balance_ho($account_id,date('Y-m-d', strtotime($to_date)));
		}else{
		 $acc_op_bal          = $this->AccountsModel->account_opening_balance($account_id,date('Y-m-d', strtotime($to_date)),$type);
		}
		
		if($acc_op_bal < 0)
          $opening_balance = formatAmount(abs($acc_op_bal)).' CR';
        else
          $opening_balance = formatAmount($acc_op_bal).' DR';
		

		if($m == 1){
			$opening_balance = '0.00 DR';
			$clo_balance = '0.00 DR';
		}
	    $data = [
			'account_name'		=> $account_name,
			'account_id'	    => $account_id,
			'opening_balance'   => $opening_balance,
			'from_date'			=> $from_date,
			'to_date'			=> $to_date,
			'm'					=> $m,
			'view'			    => $view,
			'consoview'			=> $consoview,
			'all_columns'       => $all_columns,
			'bo_id'             => $this->bo_id,
			'type'              => $type
	       ];			  
	   return view($this->folder_path.'accounts/billsundry_ledger_listing',$data);      
   }
   
   
   
   public function acc_subledger_detail($account_id){
        $all_columns = array();
		$m           = !empty($_GET['m']) ? $_GET['m'] : 0;
		$view        = !empty($_GET['view']) ? $_GET['view'] : 0;		
		$consoview   = !empty($_GET['consoview']) ? $_GET['consoview'] : 0;
		$type        = !empty($_GET['type']) ? $_GET['type'] : 1;		
		$from_date   = $_GET['from_date'] ?? '';
		$to_date     = $_GET['to_date'] ?? '';
		$from_date   = validate_from_date($from_date);
		$to_date     = validate_to_date($to_date);		
		$from_datem  = date('Y-m-d',strtotime($from_date));
		$to_datem    = date('Y-m-d',strtotime($to_date));
		$total_credit=$total_debit=0;
	
	    $account_info = $this->AccountsModel->sublgr_account_info($account_id);
	    if(empty($account_info)){
	  	   throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
	    }
	    $account_name = $account_info['sub_acc_name'];	  
        if($view==2){
		 $acc_op_bal          = 0;//$this->AccountsModel->account_opening_balance_ho($account_id,date('Y-m-d', strtotime($from_date)));	
		 $clo_acc_op_bal      = 0;//$this->AccountsModel->account_closing_balance_ho($account_id,date('Y-m-d', strtotime($to_date)));
		}else{
		 $acc_op_bal          = $this->AccountsModel->sblgr_account_opening_balance($account_id,date('Y-m-d', strtotime($to_date)),$type);
		}
		
		if($acc_op_bal < 0)
          $opening_balance = formatAmount(abs($acc_op_bal)).' CR';
        else
          $opening_balance = formatAmount($acc_op_bal).' DR';
		

		if($m == 1){
			$opening_balance = '0.00 DR';
			$clo_balance = '0.00 DR';
		}
	    $data = [
			'account_name'		=> $account_name,
			'account_id'	    => $account_id,
			'opening_balance'   => $opening_balance,
			'from_date'			=> $from_date,
			'to_date'			=> $to_date,
			'm'					=> $m,
			'view'			    => $view,
			'consoview'			=> $consoview,
			'all_columns'       => $all_columns,
			'bo_id'             => $this->bo_id,
			'type'              => $type
	       ];			  
	   return view($this->folder_path.'accounts/sub_ledger_listing',$data);      
   }


   public function monthly_detail($acc_id){
	    $master_type_info  = $this->AccountsModel->get_acc_master_type($acc_id); 	   
	    if(!$master_type_info)
		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
	    $mster_type ='acc';
	    if((int)$master_type_info['bsd_id'] >0)
		  $mster_type ='bsd';		  
	   $account_info = $this->AccountsModel->account_info($acc_id,$mster_type); 
	   if(!$account_info)
		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 	    
	   
		$account_name = $account_info['acc_name'];
		$view         = isset($_GET['view']) ? $_GET['view'] : 1;
		$to_date      = validate_to_date('');
        $from_date    = validate_from_date('');		
		$acc_op_bal   = $this->AccountsModel->account_opening_balance($acc_id,date('Y-m-d',strtotime(validate_fy_from_date(''))),1);
		
	 	if($acc_op_bal < 0)
	 	  $acc_opn_balance = formatAmount(abs($acc_op_bal)).' CR';
	 	else
	 	  $acc_opn_balance = formatAmount($acc_op_bal).' DR';	
				
		if($view==1)
	     	$summary = $this->AccountsModel->accounts_monthly_details($acc_id,$acc_op_bal);
	    if($view==2)
	     	$summary = $this->AccountsModel->accounts_quaterly_balance($acc_id,$acc_op_bal);
	    if($view==3)
	     	$summary = $this->AccountsModel->accounts_yearly_balance($acc_id,$acc_op_bal);
	
	    $data               = array(
								'acc_opn_balance'	=>	$acc_opn_balance,
								'account_name'		=>	$account_name,
								'summary'			=>	$summary,
								'view'              =>  $view,
								'account_id'        =>  $acc_id,
								'to_date'           =>  date('Y-m-d', strtotime($to_date)),
								'from_date'         =>  date('Y-m-d', strtotime($from_date))
							 ); 	
	return view($this->folder_path.'accounts/accounts_monthly',$data);
	} 
	
	public function account_balance_info($account_id,$from_date){
		$acc_op_bal   = $this->AccountsModel->account_opening_balance($account_id,date('Y-m-d',strtotime($from_date)),1);
		
	 	if($acc_op_bal < 0)
	 	  $acc_opn_balance = formatAmount(abs($acc_op_bal)).' CR';
	 	else
	 	  $acc_opn_balance = formatAmount($acc_op_bal).' DR';
	  
	   echo html_entity_decode("&nbsp;".$acc_opn_balance);
	   die();
	}
	
	public function accounts_trial($group_id, $parent = 0)
{
    $consoview   = !empty($_GET['consoview']) ? $_GET['consoview'] : 0;
    $finyear     = $this->CommonModel->calculateFiscalYearForDate(date('m'));
    $from_date   = !empty($_GET['from_date']) ? $_GET['from_date'] : $finyear['start_date'];
    $to_date     = !empty($_GET['to_date'])   ? $_GET['to_date']   : $finyear['end_date'];

    $from_date_ymd = date('Y-m-d', strtotime($from_date));
    $to_date_ymd   = date('Y-m-d', strtotime($to_date));

    // Try to detect whether this ID is a parent group when parent flag not provided
    $group_info       = null;
    $is_parent_group  = false;

    if ($parent == 1) {
        $group_info      = $this->AccountsModel->group_parent_info($group_id);
        $is_parent_group = true;
    } else {
        // First try as a normal/main group
        $group_info = $this->AccountsModel->main_group_info($group_id);

        // If no main group found, see if it is actually a parent group
        if (empty($group_info)) {
            $maybe_parent = $this->AccountsModel->group_parent_info($group_id);
            if (!empty($maybe_parent)) {
                $group_info      = $maybe_parent;
                $is_parent_group = true;
            }
        }
    }

    // If still nothing found, return an empty view with basic params
    if (empty($group_info)) {
        $data = [
            'name'    => '',
            'ac_trial_balance_list' => [],
            'from_date' => $from_date,
            'to_date'   => $to_date,
            'group_id'  => $group_id,
            'parent'    => $parent,
            'consoview' => $consoview,
        ];
        return view($this->folder_path.'accounts/accounts_trial', $data);
    }

    if ($is_parent_group) {
        // Parent flow
        $data['name']                 = $group_info['acc_grp_parent_name'];
        $data['ac_trial_balance_list']= $this->AccountsModel->load_accounts_trial_balance_parents(
            $group_id,
            $from_date_ymd,
            $to_date_ymd,
            1,
            1,
            $consoview
        );
        $data['parent'] = 1;
    } else {
        // Normal group flow
        $data['name']                 = $group_info['acc_grp_name'];
        $data['ac_trial_balance_list']= $this->AccountsModel->load_accounts_trial_balance_groups(
            $group_id,
            $from_date_ymd,
            $to_date_ymd,
            1,
            1,
            $consoview
        );
        $data['parent'] = 0;
    }

    $data['from_date']  = $from_date;
    $data['to_date']    = $to_date;
    $data['group_id']   = $group_id;
    $data['consoview']  = $consoview;

    return view($this->folder_path.'accounts/accounts_trial', $data);
} 


}