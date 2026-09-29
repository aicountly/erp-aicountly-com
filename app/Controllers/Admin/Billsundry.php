<?php
namespace App\Controllers\Admin;
use App\Models\Admin\BillsundryModel;
use App\Models\Admin\ERPLogModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\CommonModel;
use App\Traits\TransactionTrait;
class Billsundry extends BaseController
{
  use TransactionTrait;	
  function __construct()
    {  
	    helper(['form', 'url','text']);
		$this->BillsundryModel = new BillsundryModel();
		$this->LogModel        = new ERPLogModel();
		$this->auth_session    = new auth_session();
		$this->CommonModel     =  new CommonModel();
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');		
		$this->session    	 = \Config\Services::session();
		$this->bo_id         = $this->session->get('ses_boid');
    	$this->fy_id         = $this->session->get('ses_comp_fy_id');
	    $this->company_id    = $this->session->get('ses_company_id');
	    $this->billsundry_nature_no =[''=>'Choose',1=>'Round off (+)',2=>'Round off (-)',3=>'Discount (-)',4=>'Others'];
	    $this->catTypes = [
	        ''  => '',
            1   => 'GST Taxpayer',
            2   => 'TDS (GST)',
            3   => 'TCS (GST)',
            4   => 'TDS (IT)',
            5   => 'TCS (IT)',
            99  => 'Other Taxes'
        ];
    }
   
    public  function ajax_billsundry()
	 {
		echo $response =  $this->BillsundryModel->ajax_billsundry_list();	
		
	 } 
  
   public function index()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		
      	return view($this->folder_path.'billsundry/view',$data);		
    } 
  
   public function change_status(){
   	     $status_ids    = $this->request->getVar('pss_status_val'); 
		 $txcatg_id      = $this->request->getVar('accidids');
		 $errors        =[];
		 if($txcatg_id >0){
		     $stat      = true;
		     $bsdinfo   = $this->BillsundryModel->billsundry_info($txcatg_id);
		     
		     $name  = $bsdinfo['acc_name'];
		     $acc_id  = $bsdinfo['acc_id'];
			  $acc_is_restrict  = $bsdinfo['acc_is_restrict'];
			  
		     $status = $status_ids[0];
		     if($name != ''){			
					if($status==1){
					    
					    if($acc_is_restrict==1){
					          $stat = false;
					         array_push($errors, 'Failed! Account "'.$name.'" can not be changed ,it is a system generated account.');
					         
					     }
					     if($bsdinfo['bsd_nature']==NULL || $bsdinfo['bsd_nature']==0){
					          $stat = false;
					         array_push($errors, 'Failed! Account "'.$name.'" can not be changed.');
					         
					     }
					      if ($this->BillsundryModel->check_billsundry_exists($txcatg_id,$name)){
						    $stat = false;
						   array_push($errors, 'Failed! Account "'.$name.'" already exists');
					     } 
				   	   }
					 }
			     if($stat) {	   
		           $this->BillsundryModel->changestatus_single_account($txcatg_id,$acc_id,$status);
			       }
		        }
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Status changed', 'reload' => 1]);
	  }    
	  
   public function remove($id){
		if(!$id)
			 return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Page not found']]);
	         $billsundry_info = $this->BillsundryModel->billsundry_info($id);
		    if(!$billsundry_info)
			 return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Page not found']]);
			  $name = $billsundry_info['acc_name'];
			  $acc_id = $billsundry_info['acc_id'];
			  $acc_is_restrict = $billsundry_info['acc_is_restrict'];
			  if($acc_is_restrict==1)
			   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['THIS IS A SYSTEM GENERATED A/C.']]);
		   
		     if ($this->BillsundryModel->check_billsundry_with_voucher($acc_id)){ 
		           return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Failed! Billsundry "'.$name.'" has one or more associated Vouchers']]);
		     }	
			
		     
		     if ($this->BillsundryModel->check_billsundry_opn_exists($acc_id)){ 
		           return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Failed! Billsundry "'.$name.'" has opening balance']]);
		     }
		     	     
    		$this->BillsundryModel->remove_billsundry($id);
		 	
			$final_result  = $this->BillsundryModel->company_all_bsd();
									CreateJsonFile($this->fy_id,$this->company_id,'bsd',$final_result);
		 
		 return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  die;		
	  }
	  
	public function createDefaultTaxMasters()
{
    try {

        $sundry_group = $this->BillsundryModel->get_dutiestaxes_group_info('Duties & Taxes');

        if ($sundry_group == 0) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Duties & Taxes group does not exist!!!'
            ]);
        }

        $defaultAccounts = [
            [1, 'IGST OUTPUT A/C', 2],
            [1, 'IGST INPUT A/C', 1],
            [2, 'CGST OUTPUT A/C', 2],
            [2, 'CGST INPUT A/C', 1],
            [3, 'SGST OUTPUT A/C', 2],
            [3, 'SGST INPUT A/C', 1],
            [4, 'UGST OUTPUT A/C', 2],
            [4, 'UGST INPUT A/C', 1],
            [5, 'CESS (GST) OUTPUT A/C', 2],
            [5, 'CESS (GST) INPUT A/C', 1],
        ];

        $main_group_info = $this->BillsundryModel->main_group_info($sundry_group);

        $under_acc_grp_id = $main_group_info['acc_grp_id'] ?? 0;
        $acc_grp_parent_id = $main_group_info['crs_mst_parent_id'] ?? 0;
        $under_main_grp_id = ($main_group_info['crs_mst_is_primary'] ?? 0)
            ? $under_acc_grp_id
            : ($main_group_info['under_main_id'] ?? 0);

        $created = [];
        $missing = [];

        foreach ($defaultAccounts as $acc) {

            $tax_sub_type = $acc[0];
            $name         = $acc[1];
            $io_type      = $acc[2];

            // ✅ STRICT CHECK
            $exists = $this->BillsundryModel->checkTaxMasterBSD($io_type, $tax_sub_type);

            if (empty($exists)) {

                $missing[] = $name; // track missing

                $insert_data = [
                    'cmp_id'           => $this->company_id,
                    'bsd_name'         => $name,
                    'bsd_alias'        => $name,
                    'bsd_print_name'   => $name,
                    'bsd_type'         => 1,
                    'bsd_input_output' => $io_type,
                    'tax_cat_type'     => 1,
                    'tax_cat_sub_type' => $tax_sub_type,
                    'acc_is_restrict'  => 1
                ];

                $response = $this->BillsundryModel->addbsd_data_vouchersave($insert_data);

                if ($response['status']) {

                    $account_id = $response['account_id'];

                    $this->BillsundryModel->add_undercrsmt([
                        "cmp_id"            => $this->company_id,
                        "crs_mst_type"      => 14,
                        "crs_mst_id"        => $account_id,
                        "under_crs_mst_id"  => $under_acc_grp_id,
                        "crs_mst_parent_id" => $acc_grp_parent_id,
                        "under_main_id"     => $under_main_grp_id,
                        "cmpfymastr_id"     => $this->fy_id,
                        "crs_mst_is_primary"=> 0
                    ]);

                    $created[] = $name;
                }
            }
        }
		$final_result  = $this->BillsundryModel->company_all_bsd();
		CreateJsonFile($this->fy_id,$this->company_id,'bsd',$final_result);

        return $this->response->setJSON([
            'status'  => true,
            'created' => $created,
            'missing_before_create' => $missing,
            'message' => count($created) > 0
                ? count($created) . " tax accounts created"
                : "All taxable tax accounts already exist"
        ]);

    } catch (\Throwable $e) {

        return $this->response->setJSON([
            'status' => false,
            'message' => $e->getMessage()
        ]);
    }
}
	  
	  
   public function createDefaultTaxMastersllll()
{
    try {

        $sundry_group = $this->BillsundryModel->get_dutiestaxes_group_info('Duties & Taxes');

        if ($sundry_group == 0) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Duties & Taxes group does not exist!!!'
            ]);
        }

        // tax_sub_type => [name, io_type]
        $defaultAccounts = [
            [1, 'IGST OUTPUT A/C', 2],
            [1, 'IGST INPUT A/C', 1],
            [2, 'CGST OUTPUT A/C', 2],
            [2, 'CGST INPUT A/C', 1],
            [3, 'SGST OUTPUT A/C', 2],
            [3, 'SGST INPUT A/C', 1],
            [4, 'UGST OUTPUT A/C', 2],
            [4, 'UGST INPUT A/C', 1],
            [5, 'CESS (GST) OUTPUT A/C', 2],
            [5, 'CESS (GST) INPUT A/C', 1],
        ];

        $bsd_type = 1;
        $tax_cat_type = 1;

        // GROUP INFO
        $crs_mst_is_primary = 0;
        $under_acc_grp_id = 0;
        $acc_grp_parent_id = 0;
        $under_main_grp_id = 0;

        $main_group_info = $this->BillsundryModel->main_group_info($sundry_group);

        if ($main_group_info) {
            $acc_grp_parent_id = $main_group_info['crs_mst_parent_id'];
            $under_acc_grp_id  = $main_group_info['acc_grp_id'];

            $under_main_grp_id = ($main_group_info['crs_mst_is_primary'] == 1)
                ? $main_group_info['acc_grp_id']
                : $main_group_info['under_main_id'];
        }

        $created = 0;

        foreach ($defaultAccounts as $acc) {

            $tax_sub_type = $acc[0];
            $name         = $acc[1];
            $io_type      = $acc[2];

            // ✅ CHECK BY TYPE (IMPORTANT)
            $exists = $this->BillsundryModel->checkTaxMasterByType(
                $bsd_type,
                $io_type,
                $tax_cat_type,
                $tax_sub_type
            );

            if ($exists) {
                continue; // skip existing
            }

            // ✅ INSERT
            $insert_data = [
                'cmp_id'           => $this->company_id,
                'bsd_name'         => $name,
                'bsd_alias'        => $name,
                'bsd_print_name'   => $name,
                'bsd_type'         => 1,
                'bsd_input_output' => $io_type,
                'tax_cat_type'     => 1,
                'tax_cat_sub_type' => $tax_sub_type,
                'acc_is_restrict'  => 1
            ];

            $response = $this->BillsundryModel->addbsd_data($insert_data);

            if ($response['status']) {

                $account_id    = $response['account_id'];

                $mst_insert_data = [
                    "cmp_id"            => $this->company_id,
                    "crs_mst_type"      => 14,
                    "crs_mst_id"        => $account_id,
                    "under_crs_mst_id"  => $under_acc_grp_id,
                    "crs_mst_parent_id" => $acc_grp_parent_id,
                    "under_main_id"     => $under_main_grp_id,
                    "cmpfymastr_id"     => $this->fy_id,
                    "crs_mst_is_primary"=> $crs_mst_is_primary
                ];

                $this->BillsundryModel->add_undercrsmt($mst_insert_data);

                $created++;
            }
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => $created > 0
                ? "$created tax accounts created successfully"
                : "All tax accounts already exist"
        ]);

    } catch (\Throwable $e) {

        log_message('error', 'DB Query Error: ' . $e->getMessage());

        return $this->response->setJSON([
            'status' => false,
            'message' => 'Exception at line ' . $e->getLine() .
                ' in ' . $e->getFile() . ': ' . $e->getMessage()
        ]);
    }
}
   public function createDefaultTaxMasters_06_april_2026(){
	   try{
	   $sundry_group =$this->BillsundryModel->get_dutiestaxes_group_info('Duties & Taxes');  // Billsundry auto account under Duties & Taxes  group 
		if($sundry_group==0)
		return $this->response->setJSON(['status' => false, 'message' => 'Duties & Taxes  group does not exist!!!']);
		
       $defaultAccounts = [
        ['IGST OUTPUT A/C', 'IGST OUTPUT A/C', 'IGST OUTPUT A/C', 1],
        ['IGST INPUT A/C', 'IGST INPUT A/C', 'IGST INPUT A/C', 1],
        ['CGST OUTPUT A/C', 'CGST OUTPUT A/C', 'CGST OUTPUT A/C', 2],
        ['CGST INPUT A/C', 'CGST INPUT A/C', 'CGST INPUT A/C', 2],
        ['SGST OUTPUT A/C', 'SGST OUTPUT A/C', 'SGST OUTPUT A/C', 3],
        ['SGST INPUT A/C', 'SGST INPUT A/C', 'SGST INPUT A/C', 3],
        ['UGST OUTPUT A/C', 'UGST OUTPUT A/C', 'UGST OUTPUT A/C', 4],
        ['UGST INPUT A/C', 'UGST INPUT A/C', 'UGST INPUT A/C', 4],
        ['CESS (GST) OUTPUT A/C', 'CESS (GST) OUTPUT A/C', 'CESS (GST) OUTPUT A/C', 5],
        ['CESS (GST) INPUT A/C', 'CESS (GST) INPUT A/C', 'CESS (GST) INPUT A/C', 5],
    ];
    $flag=0;
    foreach ($defaultAccounts as $acc) {
        $bill_io_type = 0;
        if (stripos($acc[0], 'OUTPUT') !== false) {
            $bill_io_type = 2; // OUTPUT found
        } elseif (stripos($acc[0], 'INPUT') !== false) {
            $bill_io_type = 1; // INPUT found
        }
    
		
		$crs_mst_is_primary = 0;
		$under_acc_grp_id   = 0;
		$acc_grp_parent_id  = 0;
		$under_main_grp_id  = 0;
		$main_group_info     = $this->BillsundryModel->main_group_info($sundry_group);		
		if($main_group_info){
				$acc_grp_parent_id   = $main_group_info['crs_mst_parent_id'];
				$under_acc_grp_id  = $main_group_info['acc_grp_id'];

				if($main_group_info['crs_mst_is_primary']==1)
						$under_main_grp_id= $main_group_info['acc_grp_id'];
				else
						$under_main_grp_id= $main_group_info['under_main_id'];
		}
		
        $insert_data   = [
        		'cmp_id'           => $this->company_id,
        		'bsd_name'         => $acc[0],
        		'bsd_alias'        => $acc[1],
        		'bsd_print_name'   => $acc[2],
        		'bsd_type'         => 1,
        		'bsd_nature'       => NULL,
        		'tax_cat_mst_id'   => NULL,
        		'bsd_input_output' => $bill_io_type,
        		"bsd_base"         => '',
        		"bsd_taxable_type" => '',
        		"bsd_hsn_sac"      => '',
        		"tax_cat_type"     => 1,
        		"tax_cat_sub_type" => $acc[3] ?? 0,
				'acc_is_restrict'  => 1
        		];
        $response = $this->BillsundryModel->addbsd_data($insert_data);
		
		if($response['status']){
			$account_id      = $response['account_id'];
			$billsundry_id   = $response['billsundry_id'];
			$mst_insert_data = array("cmp_id"=>$this->company_id,"crs_mst_type"=>14,"crs_mst_id"=>$account_id,"under_crs_mst_id"=>$under_acc_grp_id,
						             "crs_mst_parent_id"=>$acc_grp_parent_id,"under_main_id"=>$under_main_grp_id,
						             "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>$crs_mst_is_primary);
			$this->BillsundryModel->add_undercrsmt($mst_insert_data);
			$flag++;
	       }
	     }
	   if($flag>0)
		return $this->response->setJSON(['status' => true, 'message' => '']);		
	   }
	  catch (\Throwable $e){
		log_message('error', 'DB Query Error: ' . $e->getMessage());
		return $this->response->setJSON(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
	   }	   
   }
   
public function checkTaxMasters()
{
    $requiredMasters = [
        1 => ['IGST OUTPUT A/C', 'IGST INPUT A/C'],
        2 => ['CGST OUTPUT A/C', 'CGST INPUT A/C'],
        3 => ['SGST OUTPUT A/C', 'SGST INPUT A/C'],
        4 => ['UGST OUTPUT A/C', 'UGST INPUT A/C'],
        5 => ['CESS (GST) OUTPUT A/C', 'CESS (GST) INPUT A/C'],
    ];

    $missing = [];

    foreach ($requiredMasters as $tax_sub_type => $names) {

        // OUTPUT
        $outputExists = $this->BillsundryModel->checkTaxMasterByType(
            1, 2, 1, $tax_sub_type
        );

        if (empty($outputExists)) {
            $missing[] = $names[0];
        }

        // INPUT
        $inputExists = $this->BillsundryModel->checkTaxMasterByType(
            1, 1, 1, $tax_sub_type
        );

        if (empty($inputExists)) {
            $missing[] = $names[1];
        }
    }

    return json_encode([
        "missing" => array_values(array_unique($missing))
    ]);
}

   public function checkTaxMasters_olde()
{
    $requiredMasters = [
        1 => ['IGST OUTPUT A/C', 'IGST INPUT A/C'],
        2 => ['CGST OUTPUT A/C', 'CGST INPUT A/C'],
        3 => ['SGST OUTPUT A/C', 'SGST INPUT A/C'],
        4 => ['UGST OUTPUT A/C', 'UGST INPUT A/C'],
        5 => ['CESS (GST) OUTPUT A/C', 'CESS (GST) INPUT A/C'],
    ];

    // Collect all names to check in a single query
    $allNames = [];
    foreach ($requiredMasters as $names) {
        foreach ($names as $name) {
            $allNames[] = strtolower($name);
        }
    }

    // Single query: find which of these already exist
    $existingRows = $this->BillsundryModel->getExistingBillSundryNames($allNames);
    $existingNames = array_map('strtolower', array_column($existingRows, 'acc_name'));

    // Whatever is NOT in DB = missing
    $missing = [];
    foreach ($requiredMasters as $names) {
        foreach ($names as $bill_sundry_name) {
            if (!in_array(strtolower($bill_sundry_name), $existingNames, true)) {
                $missing[] = $bill_sundry_name;
            }
        }
    }

    return json_encode(["missing" => $missing]);
}	  

   public function add()
    {
	   if($this->request->getMethod() == 'POST'){	
		    // echo '<pre>';print_r($_POST);die();
		
		 
		    $billsndry_name       = clean($this->request->getVar('billsndry_name')); 
		    $billsndry_allias     = clean($this->request->getVar('billsndry_allias')); 
		    $billsndry_pname      = clean($this->request->getVar('billsndry_pname'));
			$billsundarytype      = $this->request->getVar('billsundarytype');
			$sundry_calc_type     = $this->request->getVar('bsd_type');
			$cat_type             = $this->request->getVar('cat_type');
			
			
			
			if($sundry_calc_type=='1'){
		    $billsundarynature    = NULL;
		    $bill_tax_account     =1;
			$bill_io_type         = $this->request->getVar('bill_io_type');
			$sub_type             = $this->request->getVar('sub_type');
			}
		    else{
			 $billsundarynature    = $this->request->getVar('billsundarynature_no');
			 $bill_tax_account     = 0;
			 $bill_io_type         = 0;
			 $sub_type             = 0;
		    }
			
			$default_value         = 0;
			$account_primary       = $this->request->getVar('account_primary');
			$parent_group          = $this->request->getVar('parent_group');
			$sundry_group          = $this->request->getVar('sundry_group');
			$fed                   = $this->request->getVar('fed');
			$bsd_op_bal            = (float)$this->request->getVar('bsd_op_bal');
			$bsd_op_bal_drcr       = $this->request->getVar('bsd_op_bal_drcr');
			$bsd_py_bal            = (float)$this->request->getVar('bsd_py_bal');
			$bsd_py_bal_drcr       = $this->request->getVar('bsd_py_bal_drcr');
			$bill_hsn_sac          = $this->request->getVar('bill_hsn_sac');
			$item_tax              = $this->request->getVar('tax_cat_mst_id');			
			$bill_supply_type      = $this->request->getVar('bill_supply_type');
			$sundry_calc_base      = $this->request->getVar('sundry_calc_base');			
			$billsundarynature_no  = $this->request->getVar('billsundarynature_no');
			$billsundarynature_yes = $this->request->getVar('billsundarynature_yes');
			
			$memo_opp_bal          = (float)$this->request->getVar('memo_opp_bal');
		    if($memo_opp_bal=='')
			     $memo_opp_bal=0;
			if($bsd_op_bal=='')
				 $bsd_op_bal=0;
				 
		  	$memo_opp_bal_dr_cr    = $this->request->getVar('memo_opn_dr_cr');
			$rules = [				
				'billsndry_name' => [
					'label'  => 'Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter billsundry name',
					     ]
					   ],   
			       ];
			
            if(!$this->validate($rules)){
                $errors_list = $this->validator->getErrors();
				$errors      = array_values($errors_list);
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);

               }else{
                 try{
                   $response = $this->BillsundryModel->validateBillSundryMaster($billsndry_name,$billsundarynature, $bill_tax_account,$bill_supply_type,$item_tax,$bill_io_type,$cat_type,$sub_type);  
                  
				   if($response['status']==''){
				    	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
				      }
					else{
						if($account_primary == 'Y'){
							$crs_mst_is_primary=1;
							$acc_grp_parent_id = $parent_group;
							$under_acc_grp_id     = 0;
							$acc_grp_id        = 0;
							$under_main_grp_id = 0;
							$group_info        = $this->BillsundryModel->main_group_info($parent_group);
							$acc_parent_id     = $group_info['crs_mst_parent_id'] ?? $acc_grp_parent_id;
							if(isset($group_info) && $group_info['crs_mst_is_primary']==1)
								  $under_main_grp_id = $parent_group;
								 else 
								   $under_main_grp_id = 0;
						}
						else{	
							$crs_mst_is_primary=0;
							$main_group_info     = $this->BillsundryModel->main_group_info($sundry_group);
							
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
									'cmp_id'           => $this->company_id,
									'bsd_name'         => $billsndry_name,
									'bsd_alias'        => $billsndry_allias,
									'bsd_print_name'   => $billsndry_pname,
									'bsd_type'         => $sundry_calc_type,
									'bsd_nature'       => $billsundarynature,
									'tax_cat_mst_id'   => $item_tax,
									'bsd_input_output' => $bill_io_type,
									"bsd_base"         => $sundry_calc_base,
									"bsd_taxable_type" => $bill_supply_type,
									"bsd_hsn_sac"      => $bill_hsn_sac,
									"tax_cat_type"     => $cat_type,
									"tax_cat_sub_type" => $sub_type,
									'acc_is_restrict' => ($sundry_calc_type === 1) ? 1 : 0	
									];
								 $response = $this->BillsundryModel->addbsd_data($insert_data);	 
								 if(!$response['status']){
									return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
								  }
								 else{ 
								  
									$account_id   = $response['account_id'];
									$billsundry_id   = $response['billsundry_id'];
									$mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>14,"crs_mst_id"=>$account_id,"under_crs_mst_id"=>$under_acc_grp_id,
													  "crs_mst_parent_id"=>$acc_grp_parent_id,"under_main_id"=>$under_main_grp_id,
													  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>$crs_mst_is_primary);
									$this->BillsundryModel->add_undercrsmt($mst_insert_data);
									
									$acc_op_bal_val   = ($bsd_op_bal_drcr === 'dr') ? $bsd_op_bal : (($bsd_op_bal_drcr === 'cr') ? -$bsd_op_bal : 0);
									$acc_pr_bal_val   = ($bsd_py_bal_drcr === 'dr') ? $bsd_py_bal : (($bsd_op_bal_drcr === 'cr') ? -$bsd_py_bal : 0);
									$acc_op_txn_dr_cr = ($bsd_op_bal_drcr === 'dr') ? 1 : (($bsd_op_bal_drcr === 'cr') ? 2 : 0);
									
									$memo_op_bal_val   = ($memo_opp_bal_dr_cr === 'dr') ? $memo_opp_bal : (($memo_opp_bal_dr_cr === 'cr') ? -$memo_opp_bal : 0);
									$memo_op_txn_dr_cr = ($memo_opp_bal_dr_cr === 'dr') ? 1 : (($memo_opp_bal_dr_cr === 'cr') ? 2 : 0);
									
									$opn_bal_data = array("cmp_id"=>$this->company_id,"cmpfymastr_id"=>$this->fy_id,"acc_id"=>$account_id,"acc_op_bal"=>$acc_op_bal_val,
														  "acc_py_bal"=>$acc_pr_bal_val,"hobo_id"=>$this->bo_id,"acc_memo_bal"=>$memo_op_bal_val,'bsd_id'=>$billsundry_id
														 );
									$this->BillsundryModel->insert_acc_op_bal($opn_bal_data);
									
									/* $acc_txn_date = date('Y-m-d',strtotime(validate_fy_from_date('')));
									$txn_data     = array("cmp_id"=>$this->company_id,"acc_id"=>$account_id,"acc_txn_date"=>$acc_txn_date,
													      "acc_txn_dr_cr"=>$acc_op_txn_dr_cr,"acc_txn_amt"=>abs($bsd_op_bal),"vch_txn_id"=>NULL,"hobo_id"=>$this->bo_id,
													      "acc_txn_type"=>1
											             );
									$this->BillsundryModel->insert_acc_txn_entry($txn_data); */
							
									/*** for memo balance entry ***/
									/* $memo_txn_data = array("cmp_id"=>$this->company_id,"acc_id"=>$account_id,"acc_txn_date"=>$acc_txn_date,
														   "acc_txn_dr_cr"=>$acc_op_txn_dr_cr,"acc_txn_amt"=>abs($bsd_op_bal),"vch_txn_id"=>NULL,"hobo_id"=>$this->bo_id,
														   "acc_txn_type"=>3
											  );
									$this->BillsundryModel->insert_acc_txn_entry($memo_txn_data); */
									$final_result  = $this->BillsundryModel->company_all_bsd();
									CreateJsonFile($this->fy_id,$this->company_id,'bsd',$final_result);
									return json_encode(['status' => true, 'message' => 'Data Inserted']);
							    }
			          }
					
			      }
				 catch (\Throwable $e) {
					helper('error');
					$error = formatDbException($e);
					log_message('error', 'DB Error: ' . json_encode($error));
					return json_encode($error);
				} 
			   }
	   
		
        }					
		   
		$data['message_output']           = $this->message_output;
		$data['base_url']                 = $this->base_url;	
		$data['folder_path']              = $this->folder_path;			
		$data['tax_category']             = $this->BillsundryModel->tax_category_dropdown(1);
		$data['billsundry_nature_no']     = $this->billsundry_nature_no;
		$data['group_main_dropdown']      = $this->BillsundryModel->group_main_dropdown();
		$data['group_primary_dropdown']   = $this->BillsundryModel->group_primary_dropdown();
		$data['catTypes']                 = $this->catTypes;
	    return view($this->folder_path.'billsundry/add',$data);		
    }	
  
   public function modify($billsundry_id)
    {
        $account_opn_not_allowed=[6,7,11,13,8,10,9,12]; // Opening balance not allowed in Parent Group
        	if(!$billsundry_id)
						throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();		
        $opening_balances = $this->BillsundryModel->acc_opn_balance_info($billsundry_id);
        $billsundry_info = $this->BillsundryModel->billsundry_info($billsundry_id);
		
		if(!$billsundry_info)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		
		// 🔐 FORCE OLD TYPE (IMPORTANT SECURITY)
       $sundry_calc_type = $billsundry_info['bsd_type'];
	
		$account_id = $billsundry_info['acc_id'];
		if($opening_balances){
		 $opnbalance       = $opening_balances['acc_op_bal'];
		 $pybalance        = $opening_balances['acc_py_bal'];
		 $memobalance      = $opening_balances['acc_memo_bal'];
		}else{
		 $opnbalance       = 0;
		 $pybalance        = 0;
         $memobalance      = 0;		 
		}
		 if($this->request->getMethod() == 'POST' && $this->request->isAjax()){	
		 
		    // ===============================
			// 🚨 TAX ACCOUNT = YES (LOCK MODE)
			// ===============================
		  if($sundry_calc_type==1){
			  // ❌ SKIP ALL NORMAL FIELD UPDATE
             // Only balances will update
		
			 $bsd_op_bal            = (float)$this->request->getVar('bsd_op_bal') ?? 0.00;
			 $bsd_op_bal_drcr       = $this->request->getVar('bsd_op_bal_drcr');
			 $bsd_py_bal            = (float)$this->request->getVar('bsd_py_bal') ?? 0.00;
			 $bsd_py_bal_drcr       = $this->request->getVar('bsd_py_bal_drcr'); 
			 $memo_opp_bal          = (float)$this->request->getVar('memo_opp_bal') ?? 0.00;		    
		  	 $memo_opp_bal_dr_cr    = $this->request->getVar('memo_opn_dr_cr'); 
			 
			$acc_op_bal_val   = ($bsd_op_bal_drcr === 'dr') ? $bsd_op_bal : (($bsd_op_bal_drcr === 'cr') ? -$bsd_op_bal : 0);
			$acc_pr_bal_val   = ($bsd_py_bal_drcr === 'dr') ? $bsd_py_bal : (($bsd_op_bal_drcr === 'cr') ? -$bsd_py_bal : 0);
			$acc_op_txn_dr_cr = ($bsd_op_bal_drcr === 'dr') ? 1 : (($bsd_op_bal_drcr === 'cr') ? 2 : 0);

			$memo_op_bal_val   = ($memo_opp_bal_dr_cr === 'dr') ? $memo_opp_bal : (($memo_opp_bal_dr_cr === 'cr') ? -$memo_opp_bal : 0);
			$memo_op_txn_dr_cr = ($memo_opp_bal_dr_cr === 'dr') ? 1 : (($memo_opp_bal_dr_cr === 'cr') ? 2 : 0);

			$opn_bal_data = array("acc_op_bal"=>$acc_op_bal_val,"acc_py_bal"=>$acc_pr_bal_val,
			                      "acc_memo_bal"=>$memo_op_bal_val);
			$this->BillsundryModel->update_opbal_entry($opn_bal_data,$account_id,$billsundry_id);
			return json_encode(['status' => true, 'message' => 'Data Updated']);
		   }
			
		
			$billsndry_name       = clean($this->request->getVar('billsndry_name')); 
		    $billsndry_allias     = clean($this->request->getVar('billsndry_allias')); 
		    $billsndry_pname      = clean($this->request->getVar('billsndry_pname'));
			$billsundarytype      = $this->request->getVar('billsundarytype');
			$sundry_calc_type     = $this->request->getVar('bsd_type');
			$cat_type             = $this->request->getVar('cat_type');
			$tax_cat_mst_id        = $this->request->getVar('tax_cat_mst_id');
			
			
			if($sundry_calc_type=='1'){
		    $billsundarynature    = NULL;
		    $bill_tax_account     = 1;
			$sub_type             = $this->request->getVar('sub_type');
			$bill_io_type         = $this->request->getVar('bill_io_type');
			}
		    else{
			 $billsundarynature    = $this->request->getVar('billsundarynature_no');
			 $sub_type             = 0;
			 $bill_tax_account     = 0;
			 $bill_io_type         = 0;
		    }
			
			$default_value         = 0;
			$account_primary       = $this->request->getVar('account_primary');
			$parent_group          = $this->request->getVar('parent_group');
			$sundry_group          = $this->request->getVar('sundry_group');
			$fed                   = $this->request->getVar('fed');
			$bsd_op_bal            = (float)$this->request->getVar('bsd_op_bal');
			$bsd_op_bal_drcr       = $this->request->getVar('bsd_op_bal_drcr');
			$bsd_py_bal            = (float)$this->request->getVar('bsd_py_bal');
			$bsd_py_bal_drcr       = $this->request->getVar('bsd_py_bal_drcr');
			$bill_hsn_sac          = $this->request->getVar('bill_hsn_sac');
			$item_tax              = $this->request->getVar('item_tax');			
			$bill_supply_type      = $this->request->getVar('bill_supply_type');
			$sundry_calc_base      = $this->request->getVar('sundry_calc_base');			
			$billsundarynature_no  = $this->request->getVar('billsundarynature_no');
			$billsundarynature_yes = $this->request->getVar('billsundarynature_yes');
			
			$memo_opp_bal          = (float)$this->request->getVar('memo_opp_bal');
		    if($memo_opp_bal=='')
			     $memo_opp_bal=0;
			if($bsd_op_bal=='')
				 $bsd_op_bal=0;
				 
		  	$memo_opp_bal_dr_cr    = $this->request->getVar('memo_opn_dr_cr');
			$rules = [				
				'billsndry_name' => [
					'label'  => 'Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter billsundry name',
					     ]
					   ],   
			       ];
			
            if(!$this->validate($rules)){
                $errors_list = $this->validator->getErrors();
				$errors      = array_values($errors_list);
				if($errors)
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);

               }else{
				  
				   
					$response = $this->BillsundryModel->validateBillSundryMaster($billsndry_name,$billsundarynature, $bill_tax_account,$bill_supply_type,$item_tax,$bill_io_type,$cat_type,$sub_type,$billsundry_info['acc_id'],$billsundry_id);  
                  
                    if(!$response['status']){
				    	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
				      }
					else{
					try{
						if($account_primary == 'Y'){
							$crs_mst_is_primary=1;
							$acc_grp_parent_id = $parent_group;
							$under_acc_grp_id     = 0;
							$acc_grp_id        = 0;
							$under_main_grp_id = 0;
							$group_info        = $this->BillsundryModel->main_group_info($parent_group);
							$acc_parent_id     = $group_info['crs_mst_parent_id'] ?? $acc_grp_parent_id;
							if(isset($group_info) && $group_info['crs_mst_is_primary']==1)
								  $under_main_grp_id = $parent_group;
								 else 
								   $under_main_grp_id = 0;
					   }
					   else{					
							$crs_mst_is_primary=0;
							$acc_grp_id        = $sundry_group;
							$main_group_info        = $this->BillsundryModel->main_group_info($acc_grp_id);
							
							$acc_grp_parent_id   = $main_group_info['crs_mst_parent_id'];
							$under_acc_grp_id  = $main_group_info['acc_grp_id'];
							$under_main_grp_id = 0;
							if($main_group_info['crs_mst_is_primary']==1)
								$under_main_grp_id= $main_group_info['acc_grp_id'];
							else
								$under_main_grp_id= $main_group_info['under_main_id'];
				
				  
					 }
						
							$upd_data   = [
									'cmp_id'           => $this->company_id,
									'bsd_name'         => $billsndry_name,
									'bsd_alias'        => $billsndry_allias,
									'bsd_print_name'   => $billsndry_pname,
									'bsd_type'         => $sundry_calc_type,
									'bsd_nature'       => $billsundarynature,
									'bsd_input_output' => $bill_io_type,
									"bsd_base"         => $sundry_calc_base,
									"bsd_taxable_type" => $bill_supply_type,
									"bsd_hsn_sac"      => $bill_hsn_sac,
									"tax_cat_type"     => $cat_type,
									"tax_cat_sub_type" => $sub_type ?? 0,
									'tax_cat_mst_id'  => $tax_cat_mst_id,
									];
								 $response = $this->BillsundryModel->update_billsundry($upd_data,$billsundry_id,$account_id);	 						
								   
								 
								 if(!$response['status']){
									return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
								  }
								 else{ 								  
									$account_id       = $billsundry_info['acc_id'];
									$billsundry_id    = $billsundry_info['bsd_id'];
									$mst_update_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>14,"crs_mst_id"=>$account_id,"under_crs_mst_id"=>$acc_grp_id,
														"crs_mst_parent_id"=>$acc_grp_parent_id,"under_main_id"=>$under_main_grp_id,
														"cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>$crs_mst_is_primary);
									$this->BillsundryModel->update_undercrsmt($mst_update_data,$account_id,14);	
									
									$acc_op_bal_val   = ($bsd_op_bal_drcr === 'dr') ? $bsd_op_bal : (($bsd_op_bal_drcr === 'cr') ? -$bsd_op_bal : 0);
									$acc_pr_bal_val   = ($bsd_py_bal_drcr === 'dr') ? $bsd_py_bal : (($bsd_op_bal_drcr === 'cr') ? -$bsd_py_bal : 0);
									$acc_op_txn_dr_cr = ($bsd_op_bal_drcr === 'dr') ? 1 : (($bsd_op_bal_drcr === 'cr') ? 2 : 0);
									
									$memo_op_bal_val   = ($memo_opp_bal_dr_cr === 'dr') ? $memo_opp_bal : (($memo_opp_bal_dr_cr === 'cr') ? -$memo_opp_bal : 0);
									$memo_op_txn_dr_cr = ($memo_opp_bal_dr_cr === 'dr') ? 1 : (($memo_opp_bal_dr_cr === 'cr') ? 2 : 0);
									
									$opn_bal_data = array("acc_op_bal"=>$acc_op_bal_val,"acc_py_bal"=>$acc_pr_bal_val,"acc_memo_bal"=>$memo_op_bal_val);
									$this->BillsundryModel->update_opbal_entry($opn_bal_data,$account_id,$billsundry_id);
									
									$acc_txn_date = date('Y-m-d',strtotime(validate_fy_from_date('')));
																		
									/* $txn_data = array("acc_txn_dr_cr"=>$acc_op_txn_dr_cr,"acc_txn_amt"=>abs($acc_op_bal_val));
									$this->BillsundryModel->update_acc_txn_opbal_entry($txn_data,$account_id,1);
									
									$memo_txn_data = array("acc_txn_dr_cr"=>$memo_op_txn_dr_cr,"acc_txn_amt"=>abs($memo_op_bal_val));
									$this->BillsundryModel->update_acc_txn_opbal_entry($memo_txn_data,$account_id,3);
									  */
									$final_result  = $this->BillsundryModel->company_all_bsd();
									CreateJsonFile($this->fy_id,$this->company_id,'bsd',$final_result);
									return json_encode(['status' => true, 'message' => 'Data Updated']);
							    }
								
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
		 
		 if(!$billsundry_info)
		 		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

		$acc_grp_parent_id = $billsundry_info['crs_mst_parent_id'];
		if($acc_grp_parent_id==0){
			$main_group_info   = $this->BillsundryModel->main_group_info($billsundry_info['under_crs_mst_id']);
			if($main_group_info)
			$acc_grp_parent_id = $main_group_info['crs_mst_parent_id'];
		}
	
		if(in_array($acc_grp_parent_id,$account_opn_not_allowed))
			$disable_opening_balances=1;
		  else
			$disable_opening_balances=0;
			
			
		  if($opening_balances){
		  	if($opnbalance < 0){
		  		 $billsundry_info['acc_op_bal'] = parseAmount(abs($opnbalance));
		  		 $billsundry_info['acc_op_bal_drcr'] = 'cr'; 
		  	}
		  	else{
		  			$billsundry_info['acc_op_bal'] = parseAmount($opnbalance);
		  		 	$billsundry_info['acc_op_bal_drcr'] = 'dr';
		  	}
		  	if($pybalance < 0){
		  		 $billsundry_info['acc_py_bal'] =  parseAmount(abs($pybalance));
		  		 $billsundry_info['acc_py_bal_drcr'] = 'cr'; 
		  	}
		  	else{
		  			$billsundry_info['acc_py_bal'] =  parseAmount($pybalance);
		  		 	$billsundry_info['acc_py_bal_drcr'] = 'dr';
		  	}
		  	
		  	
		  	if($memobalance < 0){
		  		 $billsundry_info['acc_memo_bal'] =  parseAmount(abs($memobalance));
		  		 $billsundry_info['memo_opn_dr_cr'] = 'cr'; 
		  	}
		  	else{
		  			$billsundry_info['acc_memo_bal'] =  parseAmount($memobalance);
		  		 	$billsundry_info['memo_opn_dr_cr'] = 'dr';
		  	}
		  	
		  }
		  else{
		  		$billsundry_info['acc_op_bal'] = '0.00';
		  		$billsundry_info['acc_op_bal_drcr'] = 'dr';
		  		$billsundry_info['acc_py_bal'] = '0.00';
		  		$billsundry_info['acc_py_bal_drcr'] = 'dr';
		  }
       
		$data['billsundry_info']          = $billsundry_info;   
		$data['message_output']           = $this->message_output;
		$data['base_url']                 = $this->base_url;	
		$data['folder_path']              = $this->folder_path;	
		$data['billsundry_id']            = $billsundry_id;	
		$data['group_main_dropdown']      = $this->BillsundryModel->group_main_dropdown();
		$data['group_main_droplist']      = $this->BillsundryModel->group_main_dropdownnn();
		$data['group_primary_dropdown']   = $this->BillsundryModel->group_primary_dropdown();
		$data['billsundry_nature_no']     = $this->billsundry_nature_no;
		$data['catTypes']                 = $this->catTypes;
		$data['tax_category']              = $this->BillsundryModel->tax_category_dropdown(1);
		$data['crsmaster_info']            = $this->BillsundryModel->crsmst_info($billsundry_info['acc_id']);
		return view($this->folder_path.'billsundry/edit',$data);		
    }

    public function update_all_op_balances(){
    		$bsd_array = $this->request->getVar('bsd_array');
    		foreach ($bsd_array as $key => $value) {

    				if($value['bsd_op_bal_drcr']=='DR'){
								$value['bsd_op_bal'] = $value['bsd_op_bal'];
						}
						else if($value['bsd_op_bal_drcr']=='CR'){
								$value['bsd_op_bal'] = '-'.$value['bsd_op_bal'];
						}else
								$value['bsd_op_bal'] = '0';

						$update_data = [
								'bsd_op_bal' => $value['bsd_op_bal'],
						];

						$this->BillsundryModel->updateBalance($value['bill_sundry_id'],$update_data);
						$this->TransactionModel->update_bill_sundry_balance($value['bill_sundry_id']);
    		}

    		return json_encode(['status' => true, 'message' => 'Data Updated']);
    }
}