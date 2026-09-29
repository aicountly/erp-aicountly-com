<?php
namespace App\Controllers\Admin;
use App\Models\Admin\BillsundryModel;
use App\Models\Admin\ERPLogModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
use App\Models\Admin\TransactionModel;


class Billsundry extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url','text']);
		$this->BillsundryModel = new BillsundryModel();
		$this->TransactionModel  = new TransactionModel();		
		$this->LogModel        = new ERPLogModel();
		$this->auth_session    = new auth_session();
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->enc_string    = new enc_string();
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
		$data['enc_string']      = $this->enc_string;	
 
		return view($this->folder_path.'billsundry/view',$data);		
    } 
    

   public function remove($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'billsundry');
			
		
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 foreach($ids2 as $item_id){
		     
		    $stat = true;
		    $name = $this->BillsundryModel->get_name($item_id);;
		     
		     if ($this->BillsundryModel->check_billsundry_with_voucher($item_id)){ 
		         $stat = false;
		         array_push($errors, 'Failed! Billsundry "'.$name.'" has one or more associated Vouchers');
		     }
		     
		     if($stat)
		     {
    		     $log = [
    		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    		            'log_date' => date('Y-m-d'),
    		            'log_time' => date('H:i:s'),
    		            'log_action_tags' => 'delete',
    		            'log_field_id' => $item_id,
    		            'log_field_name' => $name,
    		            'log_field_type' => 'billsundry',
    		            ];
    		     $this->LogModel->add_log($log);
    		     
    		     $this->BillsundryModel->remove_billsundry($item_id);
		     }
		     
		 }
		 
		  $file  =     WRITEPATH.'comp'.$this->company_id.'/bsd.json';
			    if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
			        file_put_contents($file, ""); 
		            $accounts_list  = $this->BillsundryModel->company_all_bsd();
		            $f = fopen($file, 'a');
                    fwrite($f,$accounts_list);
			     }
			     
		 if(count($errors))
		 {
             $this->session->setFlashdata('error_array_message', $errors);
		 }
		 
		 // return redirect()->to($this->base_url.'billsundry');
		 return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  die;		
	  }

	  
   public function add()
    {
		 if($this->request->getMethod() == 'post'){	
		    $billsndry_name       = $this->request->getVar('billsndry_name'); 
		    $billsndry_allias     = $this->request->getVar('billsndry_allias'); 
		    $billsndry_pname      = $this->request->getVar('billsndry_pname');
			$billsundarytype      = $this->request->getVar('billsundarytype');
		    $billsundarynature    = $this->request->getVar('billsundarynature');
			$default_value        = $this->request->getVar('default_value');
			$account_primary    = $this->request->getVar('account_primary');
			$parent_group       = $this->request->getVar('parent_group');
			$sundry_group         = $this->request->getVar('sundry_group');
			$fed                  = $this->request->getVar('fed');

			$bsd_op_bal                = $this->request->getVar('bsd_op_bal');
			$bsd_op_bal_drcr          = $this->request->getVar('bsd_op_bal_drcr');
			$bsd_py_bal                = $this->request->getVar('bsd_py_bal');
			$bsd_py_bal_drcr          = $this->request->getVar('bsd_py_bal_drcr');
			
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
              $this->message_output->set_error($this->validator->listErrors());
            }else{

            	if($account_primary == 'Y'){
								$acc_grp_parent_id = $parent_group;
								$acc_grp_id = 0;
							}
							else{
								$acc_grp_parent_id = 0;
								$acc_grp_id = $sundry_group;
							}

          $insert_data   = [
						'bill_sundry_name'     => $billsndry_name,
						'bill_sundry_alias'    => $billsndry_allias,
						'sundry_print_name'    => $billsndry_pname,
						'sundry_type'          => $billsundarytype,
						'sundry_nature'        => $billsundarynature,
						'sundry_def_value'     => $default_value,
						'sundry_calc_type'     => '',
						'sundry_calc_subtype'  => $fed,
						'acc_grp_id'           => $acc_grp_id,
						'acc_grp_parent_id'    => $acc_grp_parent_id,
						'bo_id'                => ''
					  ];					 
			    	$response = $this->BillsundryModel->add($insert_data);	 
					$billsundry_id = $response['billsundry_id'];

							if($billsundry_id)
							{


								if($bsd_op_bal_drcr=='dr'){
										$bsd_op_bal = $bsd_op_bal;
								}
								else if($bsd_op_bal_drcr=='cr'){
										$bsd_op_bal = '-'.$bsd_op_bal;
								}else
										$bsd_op_bal = '0';

								if($bsd_py_bal_drcr=='dr'){
										$bsd_py_bal = $bsd_py_bal;
								}
								else if($bsd_py_bal_drcr=='cr'){
										$bsd_py_bal = '-'.$bsd_py_bal;
								}else
										$bsd_py_bal = '0';


								$insert_data = [
										'bill_sundry_id' => $billsundry_id,
										'bsd_op_bal' => $bsd_op_bal,
										'bsd_py_bal' => $bsd_py_bal,
								];

								$this->BillsundryModel->addBalance($insert_data);


									$ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
									$billsundry_txn_table_name =  $this->company_id.'_sundrytxnn_'.$billsundry_id.'_'.$ses_comp_fy_id;
									$this->BillsundryModel->CreateBillsundryTxnTable($billsundry_txn_table_name);

									$log = [
											'uuid_aicountly' => $this->session->get('uuid_aicountly'),
											'log_date' => date('Y-m-d'),
											'log_time' => date('H:i:s'),
											'log_action_tags' => 'add',
											'log_field_id' => $billsundry_id,
											'log_field_name' => $billsndry_name,
											'log_field_type' => 'billsundry',
									];
									$this->LogModel->add_log($log);

									$file  =     WRITEPATH.'comp'.$this->company_id.'/bsd.json';
									if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
											file_put_contents($file, ""); 
											$accounts_list  = $this->BillsundryModel->company_all_bsd();
											$f = fopen($file, 'a');
											fwrite($f,$accounts_list);
									}

									return redirect()->to($this->base_url.'billsundry');


							}
					}					 									
		   }							 
		$data['message_output']           = $this->message_output;
		$data['base_url']                 = $this->base_url;	
		$data['folder_path']              = $this->folder_path;	
        $data['billsundry_nature']        = billsundry_nature();
		$data['group_main_dropdown']      = $this->BillsundryModel->group_main_dropdown();
		$data['group_primary_dropdown']   = $this->BillsundryModel->group_primary_dropdown();
		 
	    return view($this->folder_path.'billsundry/add',$data);		
    }	
  
   public function modify($billsundry_id)
    {
        	if(!$billsundry_id)
						throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();		
        
		 if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
		    $billsndry_name          = $this->request->getVar('billsndry_name'); 
		    $billsndry_allias        = $this->request->getVar('billsndry_allias'); 
		    $billsndry_pname         = $this->request->getVar('billsndry_pname');
			$billsundarytype         = $this->request->getVar('billsundarytype');
		    $billsundarynature       = $this->request->getVar('billsundarynature');
			$default_value           = $this->request->getVar('default_value');
		    $account_primary    = $this->request->getVar('account_primary');
				$parent_group       = $this->request->getVar('parent_group');
				$sundry_group         = $this->request->getVar('sundry_group');
			$fed                     = $this->request->getVar('fed');

			$bsd_op_bal                = $this->request->getVar('bsd_op_bal');
			$bsd_op_bal_drcr          = $this->request->getVar('bsd_op_bal_drcr');
			$bsd_py_bal                = $this->request->getVar('bsd_py_bal');
			$bsd_py_bal_drcr          = $this->request->getVar('bsd_py_bal_drcr');

			$rules = [				
				'billsndry_name' => [
					'label'  => 'Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter billsundry name',
					   ]],
				  			   
			       ];
			
            if(!$this->validate($rules)){
              // $this->message_output->set_error($this->validator->listErrors());
              return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $this->validator->getErrors()]);
            }else{

            	if($account_primary == 'Y'){
								$acc_grp_parent_id = $parent_group;
								$acc_grp_id = 0;
							}
							else{
								$acc_grp_parent_id = 0;
								$acc_grp_id = $sundry_group;
							}

							if($bsd_op_bal_drcr=='dr'){
										$bsd_op_bal = $bsd_op_bal;
								}
								else if($bsd_op_bal_drcr=='cr'){
										$bsd_op_bal = -$bsd_op_bal;
								}else
										$bsd_op_bal = 0;

								if($bsd_py_bal_drcr=='dr'){
										$bsd_py_bal = $bsd_py_bal;
								}
								else if($bsd_py_bal_drcr=='cr'){
										$bsd_py_bal = -$bsd_py_bal;
								}else
										$bsd_py_bal = 0;

								$stat = $this->BillsundryModel->check_op_change_pnl($acc_grp_id, $acc_grp_parent_id, $bsd_op_bal);
								if(!$stat){
									return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Opening balance for accounts under Profit & Loss is restricted']]);
								}


           $update_data   = [
						'bill_sundry_name'    => $billsndry_name,
						'bill_sundry_alias'   => $billsndry_allias,
						'sundry_print_name'   => $billsndry_pname,
						'sundry_type'         => $billsundarytype,
						'sundry_nature'       => $billsundarynature,
						'sundry_def_value'    => $default_value,
						'sundry_calc_type'    => '',
						'sundry_calc_subtype' => $fed,
				    	'acc_grp_id'           => $acc_grp_id,
				    	'acc_grp_parent_id'    => $acc_grp_parent_id,
					  ];					 
			    	$response = $this->BillsundryModel->update_billsundry($update_data,$billsundry_id);

			    	


						$insert_data = [
								'bsd_op_bal' => $bsd_op_bal,
								'bsd_py_bal' => $bsd_py_bal,
						];

						$this->BillsundryModel->updateBalance($billsundry_id,$insert_data);
						$this->TransactionModel->update_bill_sundry_balance($billsundry_id); 
			   
			        
			        $log = [
				            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
				            'log_date' => date('Y-m-d'),
				            'log_time' => date('H:i:s'),
				            'log_action_tags' => 'edit',
				            'log_field_id' => $billsundry_id,
				            'log_field_name' => $billsndry_name,
				            'log_field_type' => 'billsundry',
				        ];
				    $this->LogModel->add_log($log);
				    
				 $file  =     WRITEPATH.'comp'.$this->company_id.'/bsd.json';
			    if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
			        file_put_contents($file, ""); 
		            $accounts_list  = $this->BillsundryModel->company_all_bsd();
		            $f = fopen($file, 'a');
                    fwrite($f,$accounts_list);
			     }    
				    // return redirect()->to($this->base_url.'billsundry');
						return json_encode(['status' => true, 'message' => 'Data Updated']);
				 			 
		    	}					 									
		   }							 
		   
		 $billsundry_info = $this->BillsundryModel->billsundry_info($billsundry_id);
		 if(!$billsundry_info)
		 		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

		  $billsundry_balance = $this->BillsundryModel->billsundry_balance($billsundry_id);
		  if($billsundry_balance){
		  	if($billsundry_balance['bsd_op_bal'] < 0){
		  		 $billsundry_info['bsd_op_bal'] = abs($billsundry_balance['bsd_op_bal']);
		  		 $billsundry_info['bsd_op_bal_drcr'] = 'cr'; 
		  	}
		  	else{
		  			$billsundry_info['bsd_op_bal'] = $billsundry_balance['bsd_op_bal'];
		  		 	$billsundry_info['bsd_op_bal_drcr'] = 'dr';
		  	}
		  	if($billsundry_balance['bsd_py_bal'] < 0){
		  		 $billsundry_info['bsd_py_bal'] = abs($billsundry_balance['bsd_py_bal']);
		  		 $billsundry_info['bsd_py_bal_drcr'] = 'cr'; 
		  	}
		  	else{
		  			$billsundry_info['bsd_py_bal'] = $billsundry_balance['bsd_py_bal'];
		  		 	$billsundry_info['bsd_py_bal_drcr'] = 'dr';
		  	}
		  }
		  else{
		  		$billsundry_info['bsd_op_bal'] = '0.00';
		  		$billsundry_info['bsd_op_bal_drcr'] = 'dr';
		  		$billsundry_info['bsd_py_bal'] = '0.00';
		  		$billsundry_info['bsd_py_bal_drcr'] = 'dr';
		  }

		$data['billsundry_info']          = $billsundry_info;   
		$data['message_output']           = $this->message_output;
		$data['base_url']                 = $this->base_url;	
		$data['folder_path']              = $this->folder_path;	
		$data['billsundry_id']            = $billsundry_id;	
    	$data['billsundry_nature']        = billsundry_nature();
		$data['group_main_dropdown']      = $this->BillsundryModel->group_main_dropdown();
		$data['group_primary_dropdown']   = $this->BillsundryModel->group_primary_dropdown();

	    return view($this->folder_path.'billsundry/edit',$data);		
    }

    public function update_all_op_balances()
    {
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