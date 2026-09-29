<?php
namespace App\Controllers\Admin;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\TransactionModel;
use App\Models\Admin\BalancesModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Vouchers extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url','text','custom_hepler']);
		$this->VouchersModel = new VouchersModel();
        $this->TransactionModel  = new TransactionModel();	
        $this->BalancesModel  = new BalancesModel();		
		$this->auth_session  = new auth_session();		
		$this->CommonModel       =  new CommonModel();				
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      =  base_url().'/'.getenv('AdminPath');
		$this->folder_path   =  getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
	    $this->auth_session->is_company_opened();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->enc_string    =  new enc_string();
		$this->getReferrer   =  \Config\Services::request()->getUserAgent()->getReferrer();
    }
    
    public function getAccountBillRefs()
    {
        if($this->request->getMethod() == 'post'){
            $account_id_array = $_POST['account_id_array'];
            $data = [];
            foreach($account_id_array as $account_id)
            {
                $data[] = $this->TransactionModel->get_account_bill_refs($account_id);
            }
            
            echo json_encode(['status' => true, 'data' => $data]);
        }
    }
    public function getCc()
    {
        if($this->request->getMethod() == 'post'){
            
            $data = $this->TransactionModel->get_cc();
            echo json_encode(['status' => true, 'data' => $data]);
        }
    }
    public function getPurchaseCc()
    {
        if($this->request->getMethod() == 'post'){

            $itmsdata = $this->request->getVar('itmsdata'); 

            $cc_accounts = [];
            foreach($itmsdata as $key => $value) {

                $item_info  =  $this->TransactionModel->get_item_info($value['item_id']);
                $account_id  =  $item_info['item_pur_acc'];
                $amount = $value['item_total_amount'];

                $index = array_search($account_id, array_column($cc_accounts, 'account_id'));
                if($index != ''){
                    $cc_accounts[$index]['amount'] += $amount;
                }
                else{
                    $account = $this->TransactionModel->get_account_info($account_id);
                    $account_name = $account['acc_name'];

                    $cc_accounts[] = [
                        "account_id"    => $account_id,
                        "account_name"  => $account_name,
                        "drcr"          => 'D',
                        "amount"        => $amount,
                    ];
                }                
            }
            $data['cc_accounts'] = $cc_accounts;
            $data['cc_list'] = $this->TransactionModel->get_cc();
            echo json_encode(['status' => true, 'data' => $data]);
      }
    }

    public function invoice($voucher_type_id)
    {
        $voucher_type_array = [1,5,9,13];
        if(!in_array($voucher_type_id, $voucher_type_array)){
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
        }
        
        $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);


        if($this->request->getMethod() == 'post' && $this->request->isAjax()){          
            // echo "<pre>";print_r($_POST);exit;

            $rules = [              
                'voucher_date' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Voucher Date is required',
                   ],
                ],
                'voucher_series' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Voucher Series is required',
                  ],
                ],
                'voucherdata' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Data is required'
                  ],
                ],
            ];
            
            if(!$this->validate($rules)){
                $errors = $this->validator->getErrors();
                return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }

            $narration     = $this->request->getVar('narration');
            $voucherdata        = $this->request->getVar('voucherdata');
            
            $voucher_data     = json_decode($voucherdata,true);
            $voucher_date       = $this->request->getVar('voucher_date');
            $voucher_series     = $this->request->getVar('voucher_series');

            $voucher_date = date("Y-m-d", strtotime($voucher_date));
            $voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);
            $currency_id = $this->request->getVar('currency_id');

            $oCheck     = $this->request->getVar('oCheck');
            $rCheck     = $this->request->getVar('rCheck');

            if($voucher_type_id == 5 && isset($rCheck) && $rCheck == 1) // Reverse Journal Voucher
            {
                $voucher_type_id = 16;
                $voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);

                $reversal_date     = $this->request->getVar('reversal_date');
                $reversal_date = date("Y-m-d", strtotime($reversal_date));

                $voucher_tag = 'REVJRNL';

                // add voucher consolidated entry
                $insert_data  = array(
                    "comp_id"               => $this->company_id,
                    "comp_vch_series_id"    => $voucher_series,
                    "comp_vch_no"           => $voucher_no,
                    "voucher_type_id"       => $voucher_type_id,
                    "voucher_date"          => $voucher_date,
                    "mat_cent_id"           => 0,
                    "vch_subtype_id"        => 0,
                    "voucher_tag"           => $voucher_tag,
                    "currency_id"           => $currency_id,
                );
                $voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);
                

                $insert_data = [
                    'acct_txn_id'         => $voucher_txn_id,
                    'acct_crs_id_type'    => 'vhtxnconso',
                    'comp_id'             => $this->company_id,
                    'txn_id'              => 0,
                    'voucher_txn_id'      => $voucher_txn_id,
                    'bo_id'               => 0,
                    'acc_cross_ref_type'  => 'REVJRNL',
                    'acc_cross_ref_data'  => $reversal_date,
                    'acc_cross_logdate'   => date('Y-m-d'),
                ];
                $this->TransactionModel->add_acc_crsref_data($insert_data);

                if($voucher_data)
                {
                    foreach($voucher_data as $value)
                    {
                        if($value['acc_type'] == 'acc')
                        {
                       
                            $insert_data = [
                              "comp_id"             => $this->company_id,
                              "comp_vch_series_id"  => $voucher_series,
                              "voucher_txn_id"      => $voucher_txn_id,
                              "master_id"           => $value['account_id'],
                              'master_id_type'      => 'acc'
                            ];

                            $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);

                            if($value['drcr'] == 'C'){
                                $acc_txn_amount = $value["credit"];
                                $acc_txn_drcr ='c';
                            }
                            else{
                                $acc_txn_amount = $value["debit"];
                                $acc_txn_drcr ='d';
                            }


                            $insert_data  = [
                                'comp_id'            => $this->company_id,
                                'acc_id'             => $value['account_id'],
                                'acc_txn_date'       => $voucher_date,
                                'acc_txn_amount'     => $acc_txn_amount,                        
                                'acc_txn_drcr'       => $acc_txn_drcr,
                                'acc_txn_narr'       => $value['description'],
                                'comp_vch_series_no' => $voucher_no,
                                'posted_on'          => date('Y-m-d H:i:s'),                        
                                'voucher_txn_id'     => $voucher_txn_id,
                                'voucher_type_id'    => $voucher_type_id,
                                'txn_id'             => $txn_id,
                                'acc_bal'            => 0
                            ];  
                                   
                            $this->TransactionModel->add_acc_txn_data($insert_data);
                            $this->TransactionModel->update_account_balance($value['account_id']);
                        }
                        if($value['acc_type'] == 'bsd')
                        {
                            $txn_data = array(
                                "comp_id"               => $this->company_id,
                                "comp_vch_series_id"    => $voucher_series,
                                "voucher_txn_id"        => $voucher_txn_id,
                                "master_id"             => $value['account_id'],
                                'master_id_type'        => 'bsd'
                            );
                            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

                            if($value['drcr'] == 'C'){
                                $acc_txn_amount = $value["credit"];
                                $acc_txn_drcr ='c';
                            }
                            else{
                                $acc_txn_amount = $value["debit"];
                                $acc_txn_drcr ='d';
                            }

                            $insert_data  = array(
                                "comp_id"                   => $this->company_id,
                                "sundry_txn_date"           => $voucher_date,
                                "sundry_txn_amount"         => $acc_txn_amount,
                                "sundry_txn_drcr"           => $acc_txn_drcr,                           
                                "comp_vch_name"             => $voucher_no, // ?
                                "comp_vch_series_no"        => $voucher_no,
                                "bill_sundry_id"            => $value['account_id'],
                                "sundry_txn_narr"           => $value['description'],
                                "sundry_bal"                => 0,
                                "voucher_txn_id"            => $voucher_txn_id, 
                                "voucher_type_id"           => $voucher_type_id,
                                'txn_id'                    => $txn_id,
                                'sundry_tag_rate'           => ''
                            );
                            $this->TransactionModel->add_sundry_txn_data($insert_data);
                        }
                    }
                }

                $insert_data = [
                      "comp_id"             => $this->company_id,
                      "comp_vch_series_id"  => $voucher_series,
                      "voucher_txn_id"      => $voucher_txn_id,
                      "master_id"           => 0,
                      'master_id_type'      => 'nrr'
                ];

                $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
                $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

                $voucher_no2 = $this->TransactionModel->get_voucher_no($voucher_type_id);
                $voucher_tag = 'RJVHTXN';
                $db_name = $this->TransactionModel->getDBName();
                $comp_id = $this->company_id;
                $fy_id = $this->session->get('ses_comp_fy_id');

                // add voucher consolidated entry
                $insert_data  = array(
                    "comp_id"               => $this->company_id,
                    "comp_vch_series_id"    => $voucher_series,
                    "comp_vch_no"           => $voucher_no2,
                    "voucher_type_id"       => $voucher_type_id,
                    "voucher_date"          => $reversal_date,
                    "mat_cent_id"           => 0,
                    "vch_subtype_id"        => 0,
                    "voucher_tag"           => 'RJVHTXP',
                    "currency_id"           => $currency_id,
                );
                $voucher_txn_id2       = $this->TransactionModel->add_voucher_cons_data($insert_data);
                $this->TransactionModel->save_voucher_narration($voucher_txn_id2,0,'long',$narration);

                $trigger_action = "";
                if($voucher_data)
                {
                    foreach($voucher_data as $value)
                    {
                        if($value['acc_type'] == 'acc')
                        {

                            $txn_data = array(
                                "comp_id"               => $this->company_id,
                                "comp_vch_series_id"    => $voucher_series,
                                "voucher_txn_id"        => $voucher_txn_id2,
                                "master_id"             => $value['account_id'],
                                'master_id_type'        => 'acc'
                            );
                            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);     

                            if($value['drcr'] == 'C'){
                                $acc_txn_amount = $value["credit"];
                                $acc_txn_drcr ='d';
                            }
                            else{
                                $acc_txn_amount = $value["debit"];
                                $acc_txn_drcr ='c';
                            }

                            $trigger_action .= "INSERT INTO `${db_name}`.`${comp_id}_accnttxnnn_${value['account_id']}_${fy_id}` (comp_id, acc_txn_date, acc_txn_amount, acc_txn_drcr, comp_vch_series_no, acc_id,txn_id, voucher_type_id, acc_bal, voucher_txn_id, bo_id, posted_on) VALUES ('${comp_id}', '${reversal_date}',${acc_txn_amount}, '${acc_txn_drcr}','${voucher_no2}', '${value['account_id']}', '${txn_id}', '${voucher_type_id}', '0', '${voucher_txn_id2}', '0', '[[SYSTEM_DATE]]'); ";
                        }
                        if($value['acc_type'] == 'bsd')
                        {
                            $txn_data = array(
                                "comp_id"               => $this->company_id,
                                "comp_vch_series_id"    => $voucher_series,
                                "voucher_txn_id"        => $voucher_txn_id,
                                "master_id"             => $value['account_id'],
                                'master_id_type'        => 'bsd'
                            );
                            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

                            if($value['drcr'] == 'C'){
                                $acc_txn_amount = $value["credit"];
                                $acc_txn_drcr ='c';
                            }
                            else{
                                $acc_txn_amount = $value["debit"];
                                $acc_txn_drcr ='d';
                            }

                            $trigger_action .= "INSERT INTO `${db_name}`.`${comp_id}_sundrytxnn_${value['account_id']}_${fy_id}` (comp_id, sundry_txn_date, sundry_txn_amount, sundry_txn_drcr, comp_vch_name,comp_vch_series_no, bill_sundry_id,txn_id, voucher_type_id, sundry_bal, voucher_txn_id, sundry_tag_rate) VALUES ('${comp_id}', '${reversal_date}',${acc_txn_amount}, '${acc_txn_drcr}','${voucher_no2}','${voucher_no2}', '${value['account_id']}', '${txn_id}', '${voucher_type_id}', '0', '${voucher_txn_id2}', '0'); ";
                        }
                    } //for loop
                }

                $insert_data = [
                      "comp_id"             => $this->company_id,
                      "comp_vch_series_id"  => $voucher_series,
                      "voucher_txn_id"      => $voucher_txn_id2,
                      "master_id"           => 0,
                      'master_id_type'      => 'nrr'
                ];

                $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
                $this->TransactionModel->save_voucher_narration($voucher_txn_id2,$txn_id,'long',$narration);

                $trigger_action .= "UPDATE `${comp_id}_vhtxnconso_${fy_id}` SET `voucher_tag` = 'RJVHTXN' WHERE `voucher_txn_id` = ${voucher_txn_id2};";

                
                $trigger = "select * from `${db_name}`.`${comp_id}_acctcrsref_${fy_id}` where DATE(acc_cross_ref_data) <= '[[SYSTEM_DATE]]' and voucher_txn_id = ${voucher_txn_id} and acc_cross_ref_type = 'REVJRNL'; ";

                $insert_data = [
                    'onloadchk_type'        => 'REVJRNL',
                    'voucher_txn_id'        => $voucher_txn_id,
                    'db_chk'                => $db_name,
                    'tb_chk'                => $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id'),
                    'domain'                => 'https://erp.aicountly.in/',
                    'field_chk'             => 'acc_cross_ref_data',
                    'chk_trigger'           => $trigger,
                    'chk_trigger_action'    => $trigger_action,
                    'chk_crs_id'            => 0,
                    'chk_status'            => 0,
                ];
                $id = $this->TransactionModel->add_onloadchks_data($insert_data);

                $insert_data = [
                    'acct_txn_id'         => $id,
                    'acct_crs_id_type'    => 'onloadchks',
                    'comp_id'             => $this->company_id,
                    'txn_id'              => 0,
                    'voucher_txn_id'      => $voucher_txn_id2,
                    'bo_id'               => 0,
                    'acc_cross_ref_type'  => 'RJVHTXN',
                    'acc_cross_ref_data'  => $voucher_txn_id,
                    'acc_cross_logdate'   => date('Y-m-d'),
                ];
                $this->TransactionModel->add_acc_crsref_data($insert_data);

                return json_encode(['status' => true, 'message' => 'Voucher Inserted']); 
            }

            if(isset($oCheck) && $oCheck == 1) // Optional Voucher
            {
                $voucher_tag = 'OPTIONL';

                $voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);

                // add voucher consolidated entry
                $insert_data  = array(
                    "comp_id"               => $this->company_id,
                    "comp_vch_series_id"    => $voucher_series,
                    "comp_vch_no"           => $voucher_no,
                    "voucher_type_id"       => $voucher_type_id,
                    "voucher_date"          => $voucher_date,
                    "mat_cent_id"           => 0,
                    "vch_subtype_id"        => 0,
                    "voucher_tag"           => $voucher_tag,
                    "currency_id"           => $currency_id,
                );
                $voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);
                
                
                if($voucher_data)
                {
                    foreach($voucher_data as $value)
                    {
                        if($value['acc_type'] == 'acc')
                        {
                            $txn_data = array(
                                "comp_id"               => $this->company_id,
                                "comp_vch_series_id"    => $voucher_series,
                                "voucher_txn_id"        => $voucher_txn_id,
                                "master_id"             => $value['account_id'],
                                'master_id_type'        => 'aco'
                            );
                            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);     

                            if($value['drcr'] == 'C'){
                                $acc_txn_amount = $value["credit"];
                                $acc_txn_drcr ='c';
                            }
                            else{
                                $acc_txn_amount = $value["debit"];
                                $acc_txn_drcr ='d';
                            }
                            $insert_data  = [
                                'comp_id'                => $this->company_id,
                                'acc_oth_txn_date'       => $voucher_date,
                                'acc_oth_txn_amount'     => $acc_txn_amount,                        
                                'acc_oth_txn_drcr'       => $acc_txn_drcr,
                                'acc_oth_txn_narr'       => $value['description'],
                                'voucher_type_id'        => $voucher_type_id,
                                'comp_vch_series_no'     => $voucher_series,
                                'acc_id'                 => $value['account_id'],                        
                                'voucher_txn_id'         => $voucher_txn_id,
                                'acc_oth_txn_status'     => $voucher_no,
                                'bo_id'                  => 0,
                                'txn_id'                 => $txn_id,
                                'acc_oth_txn_duedate'    => '',
                                'acc_oth_txn_tag'        => $voucher_tag,
                            ];
                            $this->TransactionModel->add_acc_oth_data($insert_data);
                        }

                        if($value['acc_type'] == 'bsd')
                        {
                            $txn_data = array(
                                "comp_id"               => $this->company_id,
                                "comp_vch_series_id"    => $voucher_series,
                                "voucher_txn_id"        => $voucher_txn_id,
                                "master_id"             => $value['account_id'],
                                'master_id_type'        => 'bso'
                            );
                            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);     

                            if($value['drcr'] == 'C'){
                                $acc_txn_amount = $value["credit"];
                                $acc_txn_drcr ='c';
                            }
                            else{
                                $acc_txn_amount = $value["debit"];
                                $acc_txn_drcr ='d';
                            }
                            $insert_data  = [
                                'comp_id'                => $this->company_id,
                                'acc_oth_txn_date'       => $voucher_date,
                                'acc_oth_txn_amount'     => $acc_txn_amount,                        
                                'acc_oth_txn_drcr'       => $acc_txn_drcr,
                                'acc_oth_txn_narr'       => $value['description'],
                                'voucher_type_id'        => $voucher_type_id,
                                'comp_vch_series_no'     => $voucher_series,
                                'acc_id'                 => $value['account_id'],                        
                                'voucher_txn_id'         => $voucher_txn_id,
                                'acc_oth_txn_status'     => $voucher_no,
                                'bo_id'                  => 0,
                                'txn_id'                 => $txn_id,
                                'acc_oth_txn_duedate'    => '',
                                'acc_oth_txn_tag'        => $voucher_tag,
                            ];
                            $this->TransactionModel->add_acc_oth_data($insert_data);
                        }
                    } //for loop
                }

                $insert_data = [
                      "comp_id"             => $this->company_id,
                      "comp_vch_series_id"  => $voucher_series,
                      "voucher_txn_id"      => $voucher_txn_id,
                      "master_id"           => 0,
                      'master_id_type'      => 'nrr'
                ];

                $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
                $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

                return json_encode(['status' => true, 'message' => 'Voucher Inserted']); 
            }

            $ccdata = [];
            if(!empty($this->request->getVar('ccdata')))
                $ccdata = json_decode($this->request->getVar('ccdata'),true);

            $bbbdata = [];
            if(!empty($this->request->getVar('bbbdata')))
                $bbbdata = json_decode($this->request->getVar('bbbdata'),true);
            
            // add voucher consolidated entry
            $insert_data  = array(
                "comp_id"               => $this->company_id,
                "comp_vch_series_id"    => $voucher_series,
                "comp_vch_no"           => $voucher_no,
                "voucher_type_id"       => $voucher_type_id,
                "voucher_date"          => $voucher_date,
                "mat_cent_id"           => 0,
                "vch_subtype_id"        => 0,
                "voucher_tag"           => '',
                "currency_id"           => $currency_id,
            );
            $voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);
            

            if($voucher_data)
            {
                foreach($voucher_data as $value)
                {
                    if($value['acc_type'] == 'acc')
                    {
                        $insert_data = [
                              "comp_id"             => $this->company_id,
                              "comp_vch_series_id"  => $voucher_series,
                              "voucher_txn_id"      => $voucher_txn_id,
                              "master_id"           => $value['account_id'],
                              'master_id_type'      => 'acc'
                        ];

                        $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);

                        if($value['drcr'] == 'C'){
                            $acc_txn_amount = $value["credit"];
                            $acc_txn_drcr ='c';
                        }
                        else{
                            $acc_txn_amount = $value["debit"];
                            $acc_txn_drcr ='d';
                        }


                        $insert_data  = [
                            'comp_id'            => $this->company_id,
                            'acc_id'             => $value['account_id'],
                            'acc_txn_date'       => $voucher_date,
                            'acc_txn_amount'     => $acc_txn_amount,                        
                            'acc_txn_drcr'       => $acc_txn_drcr,
                            'acc_txn_narr'       => $value['description'],
                            'comp_vch_series_no' => $voucher_no,
                            'posted_on'          => date('Y-m-d H:i:s'),                        
                            'voucher_txn_id'     => $voucher_txn_id,
                            'voucher_type_id'    => $voucher_type_id,
                            'txn_id'             => $txn_id,
                            'acc_bal'            => 0
                        ];  
                               
                        $this->TransactionModel->add_acc_txn_data($insert_data);
                        $this->TransactionModel->update_account_balance($value['account_id']);
                       
                        if(empty($bbbdata))
                        {
                            if(isset($value['is_bbb']) && $value['is_bbb'] == 1)
                            {
                                $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['account_id']);
                                if($bill_ref_id)
                                {
                                    if($value['drcr'] == 'C'){
                                        $acc_txn_amount = $value["credit"];
                                        $acc_txn_drcr ='C'; //capital
                                    }
                                    else{
                                        $acc_txn_amount = $value["debit"];
                                        $acc_txn_drcr ='D'; //capital
                                    }

                                    $bill_txn_data = [
                                        'bills_ref_id'       => $bill_ref_id,
                                        'comp_id'            => $this->company_id,
                                        'acc_id'             => $value['account_id'],
                                        'voucher_txn_id'     => $voucher_txn_id,
                                        'voucher_type_id'    => $voucher_type_id,
                                        'comp_vch_series_id' => $voucher_series,
                                        'bills_txn_date'     => $voucher_date,
                                        'bills_txn_drcr'     => $acc_txn_drcr,
                                        'bills_txn_amt'      => $acc_txn_amount,
                                        'bills_txn_bal'      => 0,
                                        'bills_txn_narr'     => '',
                                    ];
                                    $this->TransactionModel->add_bill_txn($bill_txn_data);
                                }
                            }
                        }

                        if(empty($ccdata))
                        {
                            if(isset($value['is_cc']) && $value['is_cc'] == 1)
                            {
                                
                                if($value['drcr'] == 'C'){
                                    $acc_txn_amount = $value["credit"];
                                    $acc_txn_drcr ='C';  //capital
                                }
                                else{
                                    $acc_txn_amount = $value["debit"];
                                    $acc_txn_drcr ='D';  //capital
                                }

                                $cc_txn_data = [
                                    'cc_id'              => 1,
                                    'comp_id'            => $this->company_id,
                                    'acc_id'             => $value['account_id'],
                                    'voucher_txn_id'     => $voucher_txn_id,
                                    'voucher_type_id'    => $voucher_type_id,
                                    'comp_vch_series_id' => $voucher_series,
                                    'cc_txn_date'        => $voucher_date,
                                    'cc_txn_drcr'        => $acc_txn_drcr,
                                    'cc_txn_amt'         => $acc_txn_amount,
                                    'cc_txn_bal'         => 0,
                                    'cc_txn_narr'        => '',
                                ];
                                $this->TransactionModel->add_cc_txn($cc_txn_data);
                                
                            }
                        }
                    }
                    
                    if($value['acc_type'] == 'bsd')
                    {
                        $txn_data = array(
                            "comp_id"               => $this->company_id,
                            "comp_vch_series_id"    => $voucher_series,
                            "voucher_txn_id"        => $voucher_txn_id,
                            "master_id"             => $value['account_id'],
                            'master_id_type'        => 'bsd'
                        );
                        $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

                        if($value['drcr'] == 'C'){
                            $acc_txn_amount = $value["credit"];
                            $acc_txn_drcr ='c';
                        }
                        else{
                            $acc_txn_amount = $value["debit"];
                            $acc_txn_drcr ='d';
                        }

                        $insert_data  = array(
                            "comp_id"                   => $this->company_id,
                            "sundry_txn_date"           => $voucher_date,
                            "sundry_txn_amount"         => $acc_txn_amount,
                            "sundry_txn_drcr"           => $acc_txn_drcr,                           
                            "comp_vch_name"             => $voucher_no, // ?
                            "comp_vch_series_no"        => $voucher_no,
                            "bill_sundry_id"            => $value['account_id'],
                            "sundry_txn_narr"           => $value['description'],
                            "sundry_bal"                => 0,
                            "voucher_txn_id"            => $voucher_txn_id, 
                            "voucher_type_id"           => $voucher_type_id,
                            'txn_id'                    => $txn_id,
                            'sundry_tag_rate'           => ''
                        );
                        $this->TransactionModel->add_sundry_txn_data($insert_data);
                    }
                }
            }

            $insert_data = [
                  "comp_id"             => $this->company_id,
                  "comp_vch_series_id"  => $voucher_series,
                  "voucher_txn_id"      => $voucher_txn_id,
                  "master_id"           => 0,
                  'master_id_type'      => 'nrr'
            ];

            $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
            $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

            if(count($bbbdata))
            {
                foreach($bbbdata as $key => $value)
                {
                    $bill_ref_id = 0;
                    $bill_due_date = $value['due_date'] != '' ? date("Y-m-d", strtotime($value['due_date'])) : $voucher_date;

                    if($value['method'] == 'New Ref.')
                    {
                        $bill_master_data = [
                            'bills_ref_name' => $value['reference'],
                            'acc_id'         => $value['account_id'],
                            'bills_status'   => 'pending',
                            'bill_due_date'  => $bill_due_date,
                        ];
                        $bill_ref_id = $this->TransactionModel->add_bill_master($bill_master_data);
                    }
                    if($value['method'] == 'Adjustment')
                    {
                        if($value['reference_id'] == 0 && $value['reference'] == 'UNDEFINED'){
                            $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['account_id']);  
                        }
                        else{
                            $bill_ref_id = $value['reference_id'];
                            $bill_master_data = [
                                'bill_due_date'  => $bill_due_date
                            ];
                            $this->TransactionModel->update_bill_master($bill_ref_id, $bill_master_data);
                        }
                        
                    }
                    if($bill_ref_id)
                    {
                        $bill_txn_data = [
                            'bills_ref_id'       => $bill_ref_id,
                            'comp_id'            => $this->company_id,
                            'acc_id'             => $value['account_id'],
                            'voucher_txn_id'     => $voucher_txn_id,
                            'voucher_type_id'    => $voucher_type_id,
                            'comp_vch_series_id' => $voucher_series,
                            'bills_txn_date'     => $voucher_date,
                            'bills_txn_drcr'     => $value['drcr'],
                            'bills_txn_amt'      => $value['amount'],
                            'bills_txn_bal'      => 0,
                            'bills_txn_narr'     => $value['narration'],
                        ];
                        $this->TransactionModel->add_bill_txn($bill_txn_data);
                    }
                }
            }
                
            if(count($ccdata))
            {
                foreach($ccdata as $value)
                {
                    $cc_txn_data = [
                        'cc_id'              => $value['cc_id'],
                        'comp_id'            => $this->company_id,
                        'acc_id'             => $value['account_id'],
                        'voucher_txn_id'     => $voucher_txn_id,
                        'voucher_type_id'    => $voucher_type_id,
                        'comp_vch_series_id' => $voucher_series,
                        'cc_txn_date'        => $voucher_date,
                        'cc_txn_drcr'        => $value['cc_txn_drcr'],
                        'cc_txn_amt'         => $value['cc_txn_amt'],
                        'cc_txn_bal'         => 0,
                        'cc_txn_narr'        => $value['cc_txn_narr'],
                    ];
                    $this->TransactionModel->add_cc_txn($cc_txn_data);
                }
            }

            $migrate_voucher_txn_id = $this->request->getVar('migrate_voucher_txn_id');           
            if($migrate_voucher_txn_id != 0)
            {
                $this->delete($migrate_voucher_txn_id);
            }
            
            return json_encode(['status' => true, 'message' => 'Voucher Inserted']);                
        }

        $data['migrate_voucher_txn_id'] = 0; 
        $data['migrate_account_transactions'] = [];
        $data['migrate_cc_data'] = [];
        $data['migrate_bbb_data'] = [];
        $data['narration'] = '';

        $data['message_output']           = $this->message_output;
        $data['base_url']                 = $this->base_url; 
        $data['folder_path']              = $this->folder_path; 
        $data['voucher_name']             = $voucher_detail['comp_vch_type'];
        $data['voucher_date']             = $voucher_detail['last_entry'];
        $data['voucher_type_id']          = $voucher_type_id;      
        $data['voucher_no']               = $this->TransactionModel->get_voucher_no($voucher_type_id);     
        $data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
        $data['reverse_voucher_no']               = $this->TransactionModel->get_voucher_no(16);     
        $data['reverse_voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series(16);
        $data['bills_method_list']        = ['','New Ref.','Adjustment'];
        
        $accounts_list = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/acc.json'),true);
        $bsd_accounts  = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json'),true);
         
        $company_all_acc_bsd  = array_merge($accounts_list,$bsd_accounts);
        
        $data['acc_bsd_json_file']        = json_encode($company_all_acc_bsd);


        if(isset($_GET['v'])){
            $voucher_info = $this->TransactionModel->get_voucher_cons_info($_GET['v']);
            if(!empty($voucher_info)){
                $data['migrate_voucher_txn_id'] = $_GET['v'];
                $data['migrate_account_transactions'] = $this->TransactionModel->get_all_account_transactions($_GET['v']);
                $data['migrate_cc_data'] = $this->TransactionModel->get_cc_txn_data($_GET['v']);
                $data['migrate_bbb_data'] = $this->TransactionModel->get_bills_txn_data($_GET['v']);

                $get_narration_info = $this->TransactionModel->get_voucher_narration_info($_GET['v'],'long',0);
                $data['narration'] = !empty($get_narration_info) ? $get_narration_info['vch_narr'] : '';

                $data['voucher_date'] = date('d-m-Y', strtotime($voucher_info['voucher_date']));
            }
        }
        $data['currency_list'] = $this->TransactionModel->get_currency_list();

        return view($this->folder_path.'vouchers/invoice',$data);       
    }

    public function download_pdf($voucherTxnId=NULL){
		
		$company_info = $this->CommonModel->get_company_info($this->company_id);
		$company_name = $company_info['comp_name'];
		$company_adrs1 = $this->enc_string->nc_string($company_info['ro_add1'],'de');
		$company_adrs2 = $this->enc_string->nc_string($company_info['ro_add2'],'de');
		$company_adrs  = $company_adrs1;
		
		if($voucherTxnId!=NULL){
			// Fetch data from the first table
        $s1 = $this->VouchersModel->get_voucher_cons_info($voucherTxnId,$this->company_id);

        $a1 = ['amo' => 0]; // Initialize $a1 to avoid undefined variable error

        $s3 = [];

            // Fetch data from the second table
            $s2 = $this->VouchersModel->getMasterData($voucherTxnId);

            foreach ($s2 as $singleS2) {
                // Fetch data from the third table
                $queryB1 = $this->VouchersModel->getAccntTxnData($singleS2['master_id'],$voucherTxnId);

                foreach ($queryB1 as $row) {
                    $a1['amo'] += $row->total_amount;
                }
            }

            $accountIds = array_column($s2, 'master_id');

            // Fetch data from the third table
            $s3 = array_merge($s3, $this->VouchersModel->getAccountData($accountIds));


      return view($this->folder_path.'vouchers/pdftable', ['company_name'=>$company_name,'company_adrs1'=>$company_adrs1,'s1' => $s1, 's3' => $s3, 'a1' => $a1]);
		
		
		
			
			
		}
		
	}

    public function receipt_pdf($voucher_txn_id)
    {
        $company_info = $this->CommonModel->get_company_info($this->company_id);
        $data['company_name'] = $company_info['comp_name'];

        $company_adrs1 = $this->enc_string->nc_string($company_info['ro_add1'],'de');
        $company_adrs2 = $this->enc_string->nc_string($company_info['ro_add2'],'de');
        $data['company_address']  = $company_adrs1;

        $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
        $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
        $data['voucher_no']        = $voucher_info['comp_vch_no'];
        $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));

        $get_voucher_series = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
        $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

        $get_narration_info = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',0);
        $data['narration'] = $get_narration_info['vch_narr'] ?? '';

        $account_transactions = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
        $credit_transactions = [];
        $debit_transactions = [];
        $amount = 0;

        foreach ($account_transactions as $key => $value) {

            if($value['credit'] != ''){
                $amount += parseAmount($value['credit']);
                $string = $value['account_name'];

                if(!empty(trim($value['description']))){
                    $short_narration = trim($value['description']);
                    $string .= " (${short_narration})";
                }

                $credit_transactions[] = $string; 
            }
            if($value['debit'] != ''){
                $string = $value['account_name'];

                if(!empty(trim($value['description']))){
                    $short_narration = trim($value['description']);
                    $string .= " (${short_narration})";
                }

                $debit_transactions[] = $string; 
            }
           
        }

        $data['credit_transactions'] = $credit_transactions;
        $data['debit_transactions'] = implode(', ', $debit_transactions);

        $data['amount'] = formatAmount($amount);
        $data['amount_words'] = $this->getIndianCurrency(parseAmount($amount));

        return view($this->folder_path.'vouchers/receipt_pdf', $data);
    }

    public function getIndianCurrency(float $number)
    {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(0 => '', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');

        $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? (($decimal == 0) ?'and ' : null ) : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $Rupees = trim($Rupees);

        $paise = '';
        if($decimal > 0){
            $Rupees = $Rupees != '' ? $Rupees . ' and ' : '';
            $paise = 'Paise ';

            if($decimal < 10){
                $paise .= $words[$decimal * 10];
            }
            else{
                $paise .= ($words[round($decimal / 10) * 10] . " " . $words[$decimal % 10]);
            }
        }
        
        return ($Rupees != '' ? 'Rupees ' .$Rupees : '') . $paise . ' Only';
    }
	
	
    public function item()
    {
        $voucher_type_id    = 5;
        $vch_subtype_id     = 23;

        $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);

        if($this->request->getMethod() == 'post'){  
           
            // echo "<pre>";print_r($_POST);exit;
            $rules = [              
                'sale_date' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Voucher Date is required',
                   ],
                ],
                'voucher_series' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Voucher Series is required',
                  ],
                ],
                'matrcntr_id' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Material Center is required'
                  ],
                ],
            ];
            
            if(!$this->validate($rules)){
                $errors = $this->validator->getErrors();
                return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }

            $voucher_date         = date('Y-m-d',strtotime($this->request->getVar('sale_date')));
            $voucher_series       = $this->request->getVar('voucher_series');       
            $matrcntr_id          = $this->request->getVar('matrcntr_id');   
            $narration            = $this->request->getVar('narration');
            $currency_id          = $this->request->getVar('currency_id');
            
            $item_data_from = [];
            if(!empty($this->request->getVar('item_data_from')))
                $item_data_from = json_decode($this->request->getVar('item_data_from'),true);
            
            $item_data_to = [];
            if(!empty($this->request->getVar('item_data_to')))
                $item_data_to = json_decode($this->request->getVar('item_data_to'),true);

            $acc_data_from = [];
            if(!empty($this->request->getVar('acc_data_from')))
                $acc_data_from = json_decode($this->request->getVar('acc_data_from'),true);
            
            $acc_data_to = [];
            if(!empty($this->request->getVar('acc_data_to')))
                $acc_data_to  = json_decode($this->request->getVar('acc_data_to'),true);

            $ccdata = [];
            if(!empty($this->request->getVar('ccdata')))
                $ccdata = json_decode($this->request->getVar('ccdata'),true);

            $bbbdata = [];
            if(!empty($this->request->getVar('bbbdata')))
                $bbbdata = json_decode($this->request->getVar('bbbdata'),true);

            $voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);

            // add voucher consolidated entry
            $insert_data     = array(
                "comp_id"           => $this->company_id,
                "comp_vch_series_id"=> $voucher_series,
                "comp_vch_no"       => $voucher_no,
                "voucher_type_id"   => $voucher_type_id,
                "voucher_date"      => $voucher_date,
                "mat_cent_id"       => $matrcntr_id,
                "vch_subtype_id"    => $vch_subtype_id,
                "voucher_tag"       => '',
                "currency_id"       => $currency_id,
            );
            $voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);
            

            if($item_data_from){

                $ccdata_undefined = [];
                $item_account_array = [];

                foreach($item_data_from as $value)
                {

                    $txn_data = array(
                        "comp_id"               => $this->company_id,
                        "comp_vch_series_id"    => $voucher_series,
                        "voucher_txn_id"        => $voucher_txn_id,
                        "master_id"             => $value['item_id'],
                        'master_id_type'        => 'itm'
                    );
                    $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);
                    if(isset($value['item_qty']) && $value['item_qty'] >0){
                    $insert_data   = array(
                            'comp_id'             => $this->company_id,
                            'item_txn_date'       => $voucher_date,
                            'item_txn_amount'     => $value['item_total_amount'],
                            'item_txn_drcr'       => 'd',
                            'item_txn_qty'        => $value['item_qty'],
                            'description'         => '',                                          
                            'item_id'             => $value['item_id'],
                            'voucher_txn_id'      => $voucher_txn_id,
                            'voucher_type_id'     => $voucher_type_id,
                            'mat_cent_id'         => $matrcntr_id,
                            'bo_id'               => 0,
                            'txn_id'              => $txn_id,                                         

                       );   
                    $item_txn_id = $this->TransactionModel->add_itm_txn_data($insert_data);

                    $get_item_info      =  $this->BalancesModel->get_item_balance_info($value['item_id']);
                    $item_open_qty      =  !empty($get_item_info['op_bal_qty']) ? $get_item_info['op_bal_qty'] : 0;
                    $item_open_value    =  !empty($get_item_info['op_bal_val']) ? $get_item_info['op_bal_val'] : 0;
                            

                    $insert_data = array(
                            "item_txn_date"       => $voucher_date,
                            "item_id"             => $value['item_id'],
                            'item_txn_drcr'       => 'd',
                            "txn_id"              => $txn_id,
                            "item_txn_id"         => $item_txn_id,
                            "voucher_txn_id"      => $voucher_txn_id,
                            "bo_id"               => 0,
                            "mat_cent_id"         => $matrcntr_id,
                            "item_unit"           => $value['item_unit_id'],
                            "item_bal_qty"        => $value['item_qty'],
                    );
                    $this->BalancesModel->add_itemtxnbal($value['item_id'],$insert_data,'d',$item_open_qty,$item_open_value);

                    // $this->TransactionModel->update_item_balance($party_id);  //function not created in model

                    $item_info  =  $this->TransactionModel->get_item_info($value['item_id']);
                    $account_id  =  $item_info['item_pur_acc'];

                    if(isset($item_account_array[$account_id]))
                        $item_account_array[$account_id] += parseAmount($value['item_total_amount']);
                    else
                        $item_account_array[$account_id] = parseAmount($value['item_total_amount']);

                    if(empty($ccdata))
                    {
                        $amount = $value['item_total_amount'];

                        $index = array_search($account_id, array_column($ccdata_undefined, 'account_id'));
                        if($index != ''){
                            $ccdata_undefined[$index]['cc_txn_amt'] += $amount;
                        }
                        else{
                            $ccdata_undefined[] = [
                                "cc_id"       => 1,
                                "account_id"  => $account_id,
                                "cc_name"     => 'UNDEFINED',
                                "cc_txn_amt"  => $amount,
                                "cc_txn_drcr" => 'D',
                                "cc_txn_narr" => ''
                            ];
                        } 
                    }
               } } //for loop


                foreach ($item_account_array as $account_id => $amount) {

                    $txn_data = array(
                        "comp_id"               => $this->company_id,
                        "comp_vch_series_id"    => $voucher_series,
                        "voucher_txn_id"        => $voucher_txn_id,
                        "master_id"             => $account_id,
                        'master_id_type'        => 'acc'
                    );
                    $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);    

                    $insert_data  = [
                        'comp_id'            => $this->company_id,
                        'acc_id'             => $account_id,
                        'acc_txn_date'       => $voucher_date,
                        'acc_txn_amount'     => $amount,                        
                        'acc_txn_drcr'       => 'd',
                        'comp_vch_series_no' => $voucher_no,
                        'posted_on'          => date('Y-m-d H:i:s'),                        
                        'voucher_txn_id'     => $voucher_txn_id,
                        'voucher_type_id'    => $voucher_type_id,
                        'txn_id'             => $txn_id,
                        'acc_bal'            => 0
                    ];  
                    $this->TransactionModel->add_acc_txn_data($insert_data);
                    $this->TransactionModel->update_account_balance($account_id);
                }

                if(count($ccdata_undefined))
                {
                    foreach($ccdata_undefined as $value)
                    {
                        $cc_txn_data = [
                            'cc_id'              => $value['cc_id'],
                            'comp_id'            => $this->company_id,
                            'acc_id'             => $value['account_id'],
                            'voucher_txn_id'     => $voucher_txn_id,
                            'voucher_type_id'    => $voucher_type_id,
                            'comp_vch_series_id' => $voucher_series,
                            'cc_txn_date'        => $voucher_date,
                            'cc_txn_drcr'        => $value['cc_txn_drcr'],
                            'cc_txn_amt'         => $value['cc_txn_amt'],
                            'cc_txn_bal'         => 0,
                            'cc_txn_narr'        => $value['cc_txn_narr'],
                        ];
                        $this->TransactionModel->add_cc_txn($cc_txn_data);
                    }
                }
            }

            if($item_data_to){

                $item_account_array = [];

                foreach($item_data_to as $value)
                {

                    $txn_data = array(
                        "comp_id"               => $this->company_id,
                        "comp_vch_series_id"    => $voucher_series,
                        "voucher_txn_id"        => $voucher_txn_id,
                        "master_id"             => $value['item_id'],
                        'master_id_type'        => 'itm'
                    );
                    $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);
                    if(isset($value['item_qty']))
                     $item_qty = $value['item_qty'];
                     else
                     $item_qty =  0;    
                    $insert_data   = array(
                            'comp_id'             => $this->company_id,
                            'item_txn_date'       => $voucher_date,
                            'item_txn_amount'     => $value['item_total_amount'],
                            'item_txn_drcr'       => 'c',
                            'item_txn_qty'        => $item_qty,
                            'description'         => '',
                            'item_id'             => $value['item_id'],
                            'voucher_txn_id'      => $voucher_txn_id,
                            'voucher_type_id'     => $voucher_type_id,
                            'mat_cent_id'         => $matrcntr_id,
                            'bo_id'               => 0,
                            'txn_id'              => $txn_id
                       );   
                    $item_txn_id = $this->TransactionModel->add_itm_txn_data($insert_data);

                    $get_item_info      =  $this->BalancesModel->get_item_balance_info($value['item_id']);
                    $item_open_qty      =  !empty($get_item_info['op_bal_qty']) ? $get_item_info['op_bal_qty'] : 0;
                    $item_open_value    =  !empty($get_item_info['op_bal_val']) ? $get_item_info['op_bal_val'] : 0;
                    

                    $insert_data = array(
                            "item_txn_date"         => $voucher_date,
                            "item_id"               => $value['item_id'],
                            'item_txn_drcr'         => 'c',
                            "txn_id"                => $txn_id,
                            "item_txn_id"           => $item_txn_id,
                            "voucher_txn_id"        => $voucher_txn_id,
                            "bo_id"                 => 0,
                            "mat_cent_id"           => $matrcntr_id,
                            "item_unit"             => $value['item_unit_id'],
                            "item_bal_qty"          => $item_qty,
                   );
                    $this->BalancesModel->add_itemtxnbal($value['item_id'],$insert_data,'c',$item_open_qty,$item_open_value);

                    // $this->TransactionModel->update_item_balance($party_id);  //function not created in model

                    $item_info  =  $this->TransactionModel->get_item_info($value['item_id']);
                    $account_id  =  $item_info['item_sales_acc'];

                    if(isset($item_account_array[$account_id]))
                        $item_account_array[$account_id] += parseAmount($value['item_total_amount']);
                    else
                        $item_account_array[$account_id] = parseAmount($value['item_total_amount']);

                } //for loop

                foreach ($item_account_array as $account_id => $amount) {

                    $txn_data = array(
                        "comp_id"               => $this->company_id,
                        "comp_vch_series_id"    => $voucher_series,
                        "voucher_txn_id"        => $voucher_txn_id,
                        "master_id"             => $account_id,
                        'master_id_type'        => 'acc'
                    );
                    $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);    

                    $insert_data  = [
                        'comp_id'            => $this->company_id,
                        'acc_id'             => $account_id,
                        'acc_txn_date'       => $voucher_date,
                        'acc_txn_amount'     => $amount,                        
                        'acc_txn_drcr'       => 'c',
                        'comp_vch_series_no' => $voucher_no,
                        'posted_on'          => date('Y-m-d H:i:s'),                        
                        'voucher_txn_id'     => $voucher_txn_id,
                        'voucher_type_id'    => $voucher_type_id,
                        'txn_id'             => $txn_id,
                        'acc_bal'            => 0
                    ];  
                    $this->TransactionModel->add_acc_txn_data($insert_data);
                    $this->TransactionModel->update_account_balance($account_id);
                } 
            }
            
            if($acc_data_from){
                foreach($acc_data_from as $value)
                {
                    if($value['acc_type'] == 'acc')
                    {
                        $insert_data = [
                            "comp_id"             => $this->company_id,
                            "comp_vch_series_id"  => $voucher_series,
                            "voucher_txn_id"      => $voucher_txn_id,
                            "master_id"           => $value['acc_id'],
                            'master_id_type'      => 'acc'
                        ];

                        $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);

                        $insert_data  = [
                            'comp_id'            => $this->company_id,
                            'acc_id'             => $value['acc_id'],
                            'acc_txn_date'       => $voucher_date,
                            'acc_txn_amount'     => $value['acc_amount'],                        
                            'acc_txn_drcr'       => 'd',
                            'acc_txn_narr'       => '',
                            'comp_vch_series_no' => $voucher_no,
                            'posted_on'          => date('Y-m-d H:i:s'),                        
                            'voucher_txn_id'     => $voucher_txn_id,
                            'voucher_type_id'    => $voucher_type_id,
                            'txn_id'             => $txn_id,
                            'acc_bal'            => 0
                        ];  

                        $this->TransactionModel->add_acc_txn_data($insert_data);
                        $this->TransactionModel->update_account_balance($value['acc_id']);

                        if(empty($bbbdata))
                        {
                            if(isset($value['is_bbb']) && $value['is_bbb'] == 1)
                            {
                                $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['acc_id']);
                                if($bill_ref_id)
                                {

                                    $bill_txn_data = [
                                        'bills_ref_id'       => $bill_ref_id,
                                        'comp_id'            => $this->company_id,
                                        'acc_id'             => $value['acc_id'],
                                        'voucher_txn_id'     => $voucher_txn_id,
                                        'voucher_type_id'    => $voucher_type_id,
                                        'comp_vch_series_id' => $voucher_series,
                                        'bills_txn_date'     => $voucher_date,
                                        'bills_txn_drcr'     => 'D',
                                        'bills_txn_amt'      => $value['acc_amount'],
                                        'bills_txn_bal'      => 0,
                                        'bills_txn_narr'     => '',
                                    ];
                                    $this->TransactionModel->add_bill_txn($bill_txn_data);
                                }
                            }
                        }

                        if(empty($ccdata))
                        {
                            if(isset($value['is_cc']) && $value['is_cc'] == 1)
                            {

                                $cc_txn_data = [
                                    'cc_id'              => 1,
                                    'comp_id'            => $this->company_id,
                                    'acc_id'             => $value['acc_id'],
                                    'voucher_txn_id'     => $voucher_txn_id,
                                    'voucher_type_id'    => $voucher_type_id,
                                    'comp_vch_series_id' => $voucher_series,
                                    'cc_txn_date'        => $voucher_date,
                                    'cc_txn_drcr'        => 'D',
                                    'cc_txn_amt'         => $value['acc_amount'],
                                    'cc_txn_bal'         => 0,
                                    'cc_txn_narr'        => '',
                                ];
                                $this->TransactionModel->add_cc_txn($cc_txn_data);
                                
                            }
                        }
                    }
                    if($value['acc_type'] == 'bsd')
                    {
                        $txn_data = array(
                            "comp_id"               => $this->company_id,
                            "comp_vch_series_id"    => $voucher_series,
                            "voucher_txn_id"        => $voucher_txn_id,
                            "master_id"             => $value['acc_id'],
                            'master_id_type'        => 'bsd'
                        );
                        $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

                        $insert_data  = array(
                            "comp_id"                   => $this->company_id,
                            "sundry_txn_date"           => $voucher_date,
                            "sundry_txn_amount"         => $value['acc_amount'],
                            "sundry_txn_drcr"           => 'd',                           
                            "comp_vch_name"             => $voucher_no, // ?
                            "comp_vch_series_no"        => $voucher_no,
                            "bill_sundry_id"            => $value['acc_id'],
                            "sundry_bal"                => 0,
                            "voucher_txn_id"            => $voucher_txn_id, 
                            "voucher_type_id"           => $voucher_type_id,
                            'txn_id'                    => $txn_id,
                            'sundry_tag_rate'           => ''
                        );
                        $this->TransactionModel->add_sundry_txn_data($insert_data);
                    }
                }
            }

            if($acc_data_to){
                foreach($acc_data_to as $value)
                {
                    if($value['acc_type'] == 'acc')
                    {
                        $insert_data = [
                            "comp_id"             => $this->company_id,
                            "comp_vch_series_id"  => $voucher_series,
                            "voucher_txn_id"      => $voucher_txn_id,
                            "master_id"           => $value['acc_id'],
                            'master_id_type'      => 'acc'
                        ];

                        $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);

                        $insert_data  = [
                            'comp_id'            => $this->company_id,
                            'acc_id'             => $value['acc_id'],
                            'acc_txn_date'       => $voucher_date,
                            'acc_txn_amount'     => $value['acc_amount'],                        
                            'acc_txn_drcr'       => 'c',
                            'acc_txn_narr'       => '',
                            'comp_vch_series_no' => $voucher_no,
                            'posted_on'          => date('Y-m-d H:i:s'),                        
                            'voucher_txn_id'     => $voucher_txn_id,
                            'voucher_type_id'    => $voucher_type_id,
                            'txn_id'             => $txn_id,
                            'acc_bal'            => 0
                        ];  

                        $this->TransactionModel->add_acc_txn_data($insert_data);
                        $this->TransactionModel->update_account_balance($value['acc_id']);

                        if(empty($bbbdata))
                        {
                            if(isset($value['is_bbb']) && $value['is_bbb'] == 1)
                            {
                                $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['acc_id']);
                                if($bill_ref_id)
                                {

                                    $bill_txn_data = [
                                        'bills_ref_id'       => $bill_ref_id,
                                        'comp_id'            => $this->company_id,
                                        'acc_id'             => $value['acc_id'],
                                        'voucher_txn_id'     => $voucher_txn_id,
                                        'voucher_type_id'    => $voucher_type_id,
                                        'comp_vch_series_id' => $voucher_series,
                                        'bills_txn_date'     => $voucher_date,
                                        'bills_txn_drcr'     => 'C',
                                        'bills_txn_amt'      => $value['acc_amount'],
                                        'bills_txn_bal'      => 0,
                                        'bills_txn_narr'     => '',
                                    ];
                                    $this->TransactionModel->add_bill_txn($bill_txn_data);
                                }
                            }
                        }

                        if(empty($ccdata))
                        {
                            if(isset($value['is_cc']) && $value['is_cc'] == 1)
                            {

                                $cc_txn_data = [
                                    'cc_id'              => 1,
                                    'comp_id'            => $this->company_id,
                                    'acc_id'             => $value['acc_id'],
                                    'voucher_txn_id'     => $voucher_txn_id,
                                    'voucher_type_id'    => $voucher_type_id,
                                    'comp_vch_series_id' => $voucher_series,
                                    'cc_txn_date'        => $voucher_date,
                                    'cc_txn_drcr'        => 'C',
                                    'cc_txn_amt'         => $value['acc_amount'],
                                    'cc_txn_bal'         => 0,
                                    'cc_txn_narr'        => '',
                                ];
                                $this->TransactionModel->add_cc_txn($cc_txn_data);
                                
                            }
                        }
                    }
                    if($value['acc_type'] == 'bsd')
                    {
                        $txn_data = array(
                            "comp_id"               => $this->company_id,
                            "comp_vch_series_id"    => $voucher_series,
                            "voucher_txn_id"        => $voucher_txn_id,
                            "master_id"             => $value['acc_id'],
                            'master_id_type'        => 'bsd'
                        );
                        $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

                        $insert_data  = array(
                            "comp_id"                   => $this->company_id,
                            "sundry_txn_date"           => $voucher_date,
                            "sundry_txn_amount"         => $value['acc_amount'],
                            "sundry_txn_drcr"           => 'c',                           
                            "comp_vch_name"             => $voucher_no, // ?
                            "comp_vch_series_no"        => $voucher_no,
                            "bill_sundry_id"            => $value['acc_id'],
                            "sundry_bal"                => 0,
                            "voucher_txn_id"            => $voucher_txn_id, 
                            "voucher_type_id"           => $voucher_type_id,
                            'txn_id'                    => $txn_id,
                            'sundry_tag_rate'           => ''
                        );
                        $this->TransactionModel->add_sundry_txn_data($insert_data);
                    }
                }
            }

            $insert_data = [
                  "comp_id"             => $this->company_id,
                  "comp_vch_series_id"  => $voucher_series,
                  "voucher_txn_id"      => $voucher_txn_id,
                  "master_id"           => 0,
                  'master_id_type'      => 'nrr'
            ];

            $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
            $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);
            
            if(count($bbbdata))
            {
                foreach($bbbdata as $key => $value)
                {
                    $bill_ref_id = 0;
                    $bill_due_date = $value['due_date'] != '' ? date("Y-m-d", strtotime($value['due_date'])) : $voucher_date;

                    if($value['method'] == 'New Ref.')
                    {
                        $bill_master_data = [
                            'bills_ref_name' => $value['reference'],
                            'acc_id'         => $value['account_id'],
                            'bills_status'   => 'pending',
                            'bill_due_date'  => $bill_due_date,
                        ];
                        $bill_ref_id = $this->TransactionModel->add_bill_master($bill_master_data);
                    }
                    if($value['method'] == 'Adjustment')
                    {
                        if($value['reference_id'] == 0 && $value['reference'] == 'UNDEFINED'){
                            $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['account_id']);  
                        }
                        else{
                            $bill_ref_id = $value['reference_id'];
                            $bill_master_data = [
                                'bill_due_date'  => $bill_due_date
                            ];
                            $this->TransactionModel->update_bill_master($bill_ref_id, $bill_master_data);
                        }
                        
                    }
                    if($bill_ref_id)
                    {
                        $bill_txn_data = [
                            'bills_ref_id'       => $bill_ref_id,
                            'comp_id'            => $this->company_id,
                            'acc_id'             => $value['account_id'],
                            'voucher_txn_id'     => $voucher_txn_id,
                            'voucher_type_id'    => $voucher_type_id,
                            'comp_vch_series_id' => $voucher_series,
                            'bills_txn_date'     => $voucher_date,
                            'bills_txn_drcr'     => $value['drcr'],
                            'bills_txn_amt'      => $value['amount'],
                            'bills_txn_bal'      => 0,
                            'bills_txn_narr'     => $value['narration'],
                        ];
                        $this->TransactionModel->add_bill_txn($bill_txn_data);
                    }
                }
            }
                
            if(count($ccdata))
            {
                foreach($ccdata as $value)
                {
                    $cc_txn_data = [
                        'cc_id'              => $value['cc_id'],
                        'comp_id'            => $this->company_id,
                        'acc_id'             => $value['account_id'],
                        'voucher_txn_id'     => $voucher_txn_id,
                        'voucher_type_id'    => $voucher_type_id,
                        'comp_vch_series_id' => $voucher_series,
                        'cc_txn_date'        => $voucher_date,
                        'cc_txn_drcr'        => $value['cc_txn_drcr'],
                        'cc_txn_amt'         => $value['cc_txn_amt'],
                        'cc_txn_bal'         => 0,
                        'cc_txn_narr'        => $value['cc_txn_narr'],
                    ];
                    $this->TransactionModel->add_cc_txn($cc_txn_data);
                }
            }


            return json_encode(['status' => true, 'message' => 'Voucher Inserted']);

        }

        $data['message_output']           = $this->message_output;
        $data['base_url']                 = $this->base_url; 
        $data['folder_path']              = $this->folder_path; 
        $data['voucher_name']             = $voucher_detail['comp_vch_type']; 
        $data['voucher_date']             = $voucher_detail['last_entry'];
        $data['voucher_type_id']          = $voucher_type_id;      
        $data['voucher_no']               = $this->TransactionModel->get_voucher_no($voucher_type_id);     
        $data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
        $data['bills_method_list']        = ['','New Ref.','Adjustment'];
        // $data['items_list']            = $this->TransactionModel->items_list();
        $data['units_list']               = $this->TransactionModel->units_dropdown();
        $data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
        $data['matrcntr_dropdown']        = $this->TransactionModel->matrcntr_dropdown();
        $data['item_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
        
         $accounts_list = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/acc.json'),true);
         $bsd_accounts  = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json'),true);
		 
		$company_all_acc_bsd  = array_merge($accounts_list,$bsd_accounts);
        
        $data['acc_bsd_json_file']        = json_encode($company_all_acc_bsd);
        $data['currency_list'] = $this->TransactionModel->get_currency_list();

        return view($this->folder_path.'vouchers/item',$data);
    }

    public function edit($voucher_txn_id,$acc_id = 0) // remove and delete method
    {   
        $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
        if(empty($voucher_info)){
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $voucher_type_id = $voucher_info['voucher_type_id'];
        $vch_subtype_id = $voucher_info['vch_subtype_id'];
        $voucher_tag = $voucher_info['voucher_tag'];

        $voucher_type_array = [1,5,9,13];

        if($this->request->getMethod() == 'post' && $this->request->isAjax()){
            // echo "<pre>";print_r($_POST);exit;

            if(in_array($voucher_type_id, $voucher_type_array) && $vch_subtype_id == 0)
            {
                // echo "<pre>";print_r($_POST);exit;
                $rules = [
                    'voucher_date' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required' => 'Voucher Date is required',
                       ],
                    ],
                    'voucher_series' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required' => 'Voucher Series is required',
                      ],
                    ],
                    'voucherdata' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required' => 'Data is required'
                      ],
                    ],
                ];
                
                if(!$this->validate($rules)){
                    $errors = $this->validator->getErrors();
                    return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
                }

                $narration          = $this->request->getVar('narration');
                $voucherdata        = $this->request->getVar('voucherdata');
                
                $voucher_data       = json_decode($voucherdata,true);
                $voucher_date       = $this->request->getVar('voucher_date');
                $voucher_series     = $this->request->getVar('voucher_series');
                $currency_id        = $this->request->getVar('currency_id');

                $ccdata = [];
                if(!empty($this->request->getVar('ccdata')))
                    $ccdata = json_decode($this->request->getVar('ccdata'),true);

                $bbbdata = [];
                if(!empty($this->request->getVar('bbbdata')))
                    $bbbdata = json_decode($this->request->getVar('bbbdata'),true);
                
                $voucher_date = date("Y-m-d", strtotime($voucher_date));
                $voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);

            

                $data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
                foreach ($data as $key => $value) {
                    if($value['master_id_type'] == 'acc'){
                        $this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                    }
                    if($value['master_id_type'] == 'itm'){
                        $this->TransactionModel->delete_itm_bal_data($value['master_id'],$voucher_txn_id);
                        $this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                    }
                    if($value['master_id_type'] == 'bsd'){
                        $this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
                    }
                }
                $this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
                $this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
                $this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
                $this->TransactionModel->delete_cc_txn($voucher_txn_id);
                $this->TransactionModel->delete_bills_txn($voucher_txn_id);
                
                $this->TransactionModel->delete_all_narrations($voucher_txn_id);
                $this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);

                $oCheck     = $this->request->getVar('oCheck');
                $voucher_tag = '';
                if(isset($oCheck) && $oCheck == 1){
                    $voucher_tag = 'OPTIONL';
                }
                
                $insert_data  = array(
                        "comp_vch_series_id"        => $voucher_series,
                        "voucher_date"              => $voucher_date,
                        "voucher_tag"              => $voucher_tag,
                        "currency_id"               => $currency_id,
                );
                $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);
                


                if(isset($oCheck) && $oCheck == 1) // Optional Voucher
                { 
                    if($voucher_data)
                    {
                        foreach($voucher_data as $value)
                        {
                            if($value['acc_type'] == 'acc')
                            {
                                $txn_data = array(
                                    "comp_id"               => $this->company_id,
                                    "comp_vch_series_id"    => $voucher_series,
                                    "voucher_txn_id"        => $voucher_txn_id,
                                    "master_id"             => $value['account_id'],
                                    'master_id_type'        => 'aco'
                                );
                                $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);     

                                if($value['drcr'] == 'C'){
                                    $acc_txn_amount = $value["credit"];
                                    $acc_txn_drcr ='c';
                                }
                                else{
                                    $acc_txn_amount = $value["debit"];
                                    $acc_txn_drcr ='d';
                                }
                                $insert_data  = [
                                    'comp_id'                => $this->company_id,
                                    'acc_oth_txn_date'       => $voucher_date,
                                    'acc_oth_txn_amount'     => $acc_txn_amount,                        
                                    'acc_oth_txn_drcr'       => $acc_txn_drcr,
                                    'acc_oth_txn_narr'       => $value['description'],
                                    'voucher_type_id'        => $voucher_type_id,
                                    'comp_vch_series_no'     => $voucher_series,
                                    'acc_id'                 => $value['account_id'],                        
                                    'voucher_txn_id'         => $voucher_txn_id,
                                    'acc_oth_txn_status'     => $voucher_no,
                                    'bo_id'                  => 0,
                                    'txn_id'                 => $txn_id,
                                    'acc_oth_txn_duedate'    => '',
                                    'acc_oth_txn_tag'        => $voucher_tag,
                                ];
                                $this->TransactionModel->add_acc_oth_data($insert_data);
                            }

                            if($value['acc_type'] == 'bsd')
                            {
                                $txn_data = array(
                                    "comp_id"               => $this->company_id,
                                    "comp_vch_series_id"    => $voucher_series,
                                    "voucher_txn_id"        => $voucher_txn_id,
                                    "master_id"             => $value['account_id'],
                                    'master_id_type'        => 'bso'
                                );
                                $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);     

                                if($value['drcr'] == 'C'){
                                    $acc_txn_amount = $value["credit"];
                                    $acc_txn_drcr ='c';
                                }
                                else{
                                    $acc_txn_amount = $value["debit"];
                                    $acc_txn_drcr ='d';
                                }
                                $insert_data  = [
                                    'comp_id'                => $this->company_id,
                                    'acc_oth_txn_date'       => $voucher_date,
                                    'acc_oth_txn_amount'     => $acc_txn_amount,                        
                                    'acc_oth_txn_drcr'       => $acc_txn_drcr,
                                    'acc_oth_txn_narr'       => $value['description'],
                                    'voucher_type_id'        => $voucher_type_id,
                                    'comp_vch_series_no'     => $voucher_series,
                                    'acc_id'                 => $value['account_id'],                        
                                    'voucher_txn_id'         => $voucher_txn_id,
                                    'acc_oth_txn_status'     => $voucher_no,
                                    'bo_id'                  => 0,
                                    'txn_id'                 => $txn_id,
                                    'acc_oth_txn_duedate'    => '',
                                    'acc_oth_txn_tag'        => $voucher_tag,
                                ];
                                $this->TransactionModel->add_acc_oth_data($insert_data);
                            }
                        } //for loop
                    }

                    $insert_data = [
                          "comp_id"             => $this->company_id,
                          "comp_vch_series_id"  => $voucher_series,
                          "voucher_txn_id"      => $voucher_txn_id,
                          "master_id"           => 0,
                          'master_id_type'      => 'nrr'
                    ];

                    $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
                    $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

                    return json_encode(['status' => true, 'message' => 'Voucher Inserted']); 
                }

                if($voucher_data)
                {
                    foreach($voucher_data as $value)
                    {
                        if($value['acc_type'] == 'acc')
                        {
                            $insert_data = [
                              "comp_id"             => $this->company_id,
                              "comp_vch_series_id"  => $voucher_series,
                              "voucher_txn_id"      => $voucher_txn_id,
                              "master_id"           => $value['account_id'],
                              'master_id_type'      => 'acc'
                            ];

                            $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);

                            if($value['drcr'] == 'C'){
                                $acc_txn_amount = $value["credit"];
                                $acc_txn_drcr ='c';
                            }
                            else{
                                $acc_txn_amount = $value["debit"];
                                $acc_txn_drcr ='d';
                            }


                            $insert_data  = [
                                'comp_id'            => $this->company_id,
                                'acc_id'             => $value['account_id'],
                                'acc_txn_date'       => $voucher_date,
                                'acc_txn_amount'     => $acc_txn_amount,                        
                                'acc_txn_drcr'       => $acc_txn_drcr,
                                'acc_txn_narr'       => $value['description'],
                                'comp_vch_series_no' => $voucher_no,
                                'posted_on'          => date('Y-m-d H:i:s'),                        
                                'voucher_txn_id'     => $voucher_txn_id,
                                'voucher_type_id'    => $voucher_type_id,
                                'txn_id'             => $txn_id,
                                'acc_bal'            => 0
                            ];  
                                   
                            $this->TransactionModel->add_acc_txn_data($insert_data);
                            $this->TransactionModel->update_account_balance($value['account_id']);

                            if(empty($bbbdata))
                            {
                                if(isset($value['is_bbb']) && $value['is_bbb'] == 1)
                                {
                                    $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['account_id']);
                                    if($bill_ref_id)
                                    {
                                        if($value['drcr'] == 'C'){
                                            $acc_txn_amount = $value["credit"];
                                            $acc_txn_drcr ='C'; //capital
                                        }
                                        else{
                                            $acc_txn_amount = $value["debit"];
                                            $acc_txn_drcr ='D'; //capital
                                        }

                                        $bill_txn_data = [
                                            'bills_ref_id'       => $bill_ref_id,
                                            'comp_id'            => $this->company_id,
                                            'acc_id'             => $value['account_id'],
                                            'voucher_txn_id'     => $voucher_txn_id,
                                            'voucher_type_id'    => $voucher_type_id,
                                            'comp_vch_series_id' => $voucher_series,
                                            'bills_txn_date'     => $voucher_date,
                                            'bills_txn_drcr'     => $acc_txn_drcr,
                                            'bills_txn_amt'      => $acc_txn_amount,
                                            'bills_txn_bal'      => 0,
                                            'bills_txn_narr'     => '',
                                        ];
                                        $this->TransactionModel->add_bill_txn($bill_txn_data);
                                    }
                                }
                            }

                            if(empty($ccdata))
                            {
                                if(isset($value['is_cc']) && $value['is_cc'] == 1)
                                {
                                    
                                    if($value['drcr'] == 'C'){
                                        $acc_txn_amount = $value["credit"];
                                        $acc_txn_drcr ='C';  //capital
                                    }
                                    else{
                                        $acc_txn_amount = $value["debit"];
                                        $acc_txn_drcr ='D';  //capital
                                    }

                                    $cc_txn_data = [
                                        'cc_id'              => 1,
                                        'comp_id'            => $this->company_id,
                                        'acc_id'             => $value['account_id'],
                                        'voucher_txn_id'     => $voucher_txn_id,
                                        'voucher_type_id'    => $voucher_type_id,
                                        'comp_vch_series_id' => $voucher_series,
                                        'cc_txn_date'        => $voucher_date,
                                        'cc_txn_drcr'        => $acc_txn_drcr,
                                        'cc_txn_amt'         => $acc_txn_amount,
                                        'cc_txn_bal'         => 0,
                                        'cc_txn_narr'        => '',
                                    ];
                                    $this->TransactionModel->add_cc_txn($cc_txn_data);
                                    
                                }
                            }
                        }
                            

                        if($value['acc_type'] == 'bsd')
                        {
                            $txn_data = array(
                                "comp_id"               => $this->company_id,
                                "comp_vch_series_id"    => $voucher_series,
                                "voucher_txn_id"        => $voucher_txn_id,
                                "master_id"             => $value['account_id'],
                                'master_id_type'        => 'bsd'
                            );
                            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

                            if($value['drcr'] == 'C'){
                                $acc_txn_amount = $value["credit"];
                                $acc_txn_drcr ='c';
                            }
                            else{
                                $acc_txn_amount = $value["debit"];
                                $acc_txn_drcr ='d';
                            }

                            $insert_data  = array(
                                "comp_id"                   => $this->company_id,
                                "sundry_txn_date"           => $voucher_date,
                                "sundry_txn_amount"         => $acc_txn_amount,
                                "sundry_txn_drcr"           => $acc_txn_drcr,                           
                                "comp_vch_name"             => $voucher_no, // ?
                                "comp_vch_series_no"        => $voucher_no,
                                "bill_sundry_id"            => $value['account_id'],
                                "sundry_txn_narr"           => $value['description'],
                                "sundry_bal"                => 0,
                                "voucher_txn_id"            => $voucher_txn_id, 
                                "voucher_type_id"           => $voucher_type_id,
                                'txn_id'                    => $txn_id,
                                'sundry_tag_rate'           => ''
                            );
                            $this->TransactionModel->add_sundry_txn_data($insert_data);
                        }
                    }
                }

                $insert_data = [
                      "comp_id"             => $this->company_id,
                      "comp_vch_series_id"  => $voucher_series,
                      "voucher_txn_id"      => $voucher_txn_id,
                      "master_id"           => 0,
                      'master_id_type'      => 'nrr'
                ];

                $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
                $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

                if(count($bbbdata))
                {
                    foreach($bbbdata as $key => $value)
                    {
                        $bill_ref_id = 0;
                        $bill_due_date = $value['due_date'] != '' ? date("Y-m-d", strtotime($value['due_date'])) : $voucher_date;

                        if($value['method'] == 'New Ref.')
                        {
                            $bill_master_data = [
                                'bills_ref_name' => $value['reference'],
                                'acc_id'         => $value['account_id'],
                                'bills_status'   => 'pending',
                                'bill_due_date'  => $bill_due_date,
                            ];
                            $bill_ref_id = $this->TransactionModel->add_bill_master($bill_master_data);
                        }
                        if($value['method'] == 'Adjustment')
                        {
                            if($value['reference_id'] == 0 && $value['reference'] == 'UNDEFINED'){
                                $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['account_id']);  
                            }
                            else{
                                $bill_ref_id = $value['reference_id'];
                                $bill_master_data = [
                                    'bill_due_date'  => $bill_due_date
                                ];
                                $this->TransactionModel->update_bill_master($bill_ref_id, $bill_master_data);
                            }
                            
                        }
                        if($bill_ref_id)
                        {
                            $bill_txn_data = [
                                'bills_ref_id'       => $bill_ref_id,
                                'comp_id'            => $this->company_id,
                                'acc_id'             => $value['account_id'],
                                'voucher_txn_id'     => $voucher_txn_id,
                                'voucher_type_id'    => $voucher_type_id,
                                'comp_vch_series_id' => $voucher_series,
                                'bills_txn_date'     => $voucher_date,
                                'bills_txn_drcr'     => $value['drcr'],
                                'bills_txn_amt'      => $value['amount'],
                                'bills_txn_bal'      => 0,
                                'bills_txn_narr'     => $value['narration'],
                            ];
                            $this->TransactionModel->add_bill_txn($bill_txn_data);
                        }
                    }
                }
                
                if(count($ccdata))
                {
                    foreach($ccdata as $value)
                    {
                        $cc_txn_data = [
                            'cc_id'              => $value['cc_id'],
                            'comp_id'            => $this->company_id,
                            'acc_id'             => $value['account_id'],
                            'voucher_txn_id'     => $voucher_txn_id,
                            'voucher_type_id'    => $voucher_type_id,
                            'comp_vch_series_id' => $voucher_series,
                            'cc_txn_date'        => $voucher_date,
                            'cc_txn_drcr'        => $value['cc_txn_drcr'],
                            'cc_txn_amt'         => $value['cc_txn_amt'],
                            'cc_txn_bal'         => 0,
                            'cc_txn_narr'        => $value['cc_txn_narr'],
                        ];
                        $this->TransactionModel->add_cc_txn($cc_txn_data);
                    }
                }

                return json_encode(['status' => true, 'message' => 'Voucher Updated']);
            }
            
            if($voucher_type_id == 5 && $vch_subtype_id == 23)
            {
                $rules = [              
                    'sale_date' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required' => 'Voucher Date is required',
                       ],
                    ],
                    'voucher_series' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required' => 'Voucher Series is required',
                      ],
                    ],
                    'matrcntr_id' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required' => 'Material Center is required'
                      ],
                    ],
                ];
                
                if(!$this->validate($rules)){
                    $errors = $this->validator->getErrors();
                    return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
                }

                $voucher_date         = date('Y-m-d',strtotime($this->request->getVar('sale_date')));
                $voucher_series       = $this->request->getVar('voucher_series');       
                $matrcntr_id          = $this->request->getVar('matrcntr_id');   
                $narration            = $this->request->getVar('narration');
                $currency_id          = $this->request->getVar('currency_id');
                
                $item_data_from = [];
                if(!empty($this->request->getVar('item_data_from')))
                    $item_data_from = json_decode($this->request->getVar('item_data_from'),true);
                
                $item_data_to = [];
                if(!empty($this->request->getVar('item_data_to')))
                    $item_data_to = json_decode($this->request->getVar('item_data_to'),true);

                $acc_data_from = [];
                if(!empty($this->request->getVar('acc_data_from')))
                    $acc_data_from = json_decode($this->request->getVar('acc_data_from'),true);
                
                $acc_data_to = [];
                if(!empty($this->request->getVar('acc_data_to')))
                    $acc_data_to  = json_decode($this->request->getVar('acc_data_to'),true);

                $ccdata = [];
                if(!empty($this->request->getVar('ccdata')))
                    $ccdata = json_decode($this->request->getVar('ccdata'),true);

                $bbbdata = [];
                if(!empty($this->request->getVar('bbbdata')))
                    $bbbdata = json_decode($this->request->getVar('bbbdata'),true);

                $voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);

                $data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
                foreach ($data as $key => $value) {
                    if($value['master_id_type'] == 'acc'){
                        $this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                    }
                    if($value['master_id_type'] == 'itm'){
                        $this->TransactionModel->delete_itm_bal_data($value['master_id'],$voucher_txn_id);
                        $this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                    }
                    if($value['master_id_type'] == 'bsd'){
                        $this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
                    }
                }
                $this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
                $this->TransactionModel->delete_cc_txn($voucher_txn_id);
                $this->TransactionModel->delete_bills_txn($voucher_txn_id);
                
                $this->TransactionModel->delete_all_narrations($voucher_txn_id);
                
                $insert_data  = array(
                        "comp_vch_series_id"        => $voucher_series,
                        "voucher_date"              => $voucher_date,
                        "currency_id"               => $currency_id,
                );
                $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);
                

                if($item_data_from){

                    $ccdata_undefined = [];
                    $item_account_array = [];

                    foreach($item_data_from as $value)
                    {

                        $txn_data = array(
                            "comp_id"               => $this->company_id,
                            "comp_vch_series_id"    => $voucher_series,
                            "voucher_txn_id"        => $voucher_txn_id,
                            "master_id"             => $value['item_id'],
                            'master_id_type'        => 'itm'
                        );
                        $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

                        $insert_data   = array(
                                'comp_id'             => $this->company_id,
                                'item_txn_date'       => $voucher_date,
                                'item_txn_amount'     => $value['item_total_amount'],
                                'item_txn_drcr'       => 'd',
                                'item_txn_qty'        => $value['item_qty'],
                                'description'         => '',                                          
                                'item_id'             => $value['item_id'],
                                'voucher_txn_id'      => $voucher_txn_id,
                                'voucher_type_id'     => $voucher_type_id,
                                'mat_cent_id'         => $matrcntr_id,
                                'bo_id'               => 0,
                                'txn_id'              => $txn_id,                                         

                           );   
                        $item_txn_id = $this->TransactionModel->add_itm_txn_data($insert_data);

                        $get_item_info      =  $this->BalancesModel->get_item_balance_info($value['item_id']);
                        $item_open_qty      =  !empty($get_item_info['op_bal_qty']) ? $get_item_info['op_bal_qty'] : 0;
                        $item_open_value    =  !empty($get_item_info['op_bal_val']) ? $get_item_info['op_bal_val'] : 0;
                                

                        $insert_data = array(
                                "item_txn_date"       => $voucher_date,
                                "item_id"             => $value['item_id'],
                                'item_txn_drcr'       => 'd',
                                "txn_id"              => $txn_id,
                                "item_txn_id"         => $item_txn_id,
                                "voucher_txn_id"      => $voucher_txn_id,
                                "bo_id"               => 0,
                                "mat_cent_id"         => $matrcntr_id,
                                "item_unit"           => $value['item_unit_id'],
                                "item_bal_qty"        => $value['item_qty'],
                        );
                        $this->BalancesModel->add_itemtxnbal($value['item_id'],$insert_data,'d',$item_open_qty,$item_open_value);

                        // $this->TransactionModel->update_item_balance($party_id);  //function not created in model

                        $item_info  =  $this->TransactionModel->get_item_info($value['item_id']);
                        $account_id  =  $item_info['item_pur_acc'];

                        if(isset($item_account_array[$account_id]))
                            $item_account_array[$account_id] += parseAmount($value['item_total_amount']);
                        else
                            $item_account_array[$account_id] = parseAmount($value['item_total_amount']);

                        if(empty($ccdata))
                        {
                            $amount = $value['item_total_amount'];

                            $index = array_search($account_id, array_column($ccdata_undefined, 'account_id'));
                            if($index != ''){
                                $ccdata_undefined[$index]['cc_txn_amt'] += $amount;
                            }
                            else{
                                $ccdata_undefined[] = [
                                    "cc_id"       => 1,
                                    "account_id"  => $account_id,
                                    "cc_name"     => 'UNDEFINED',
                                    "cc_txn_amt"  => $amount,
                                    "cc_txn_drcr" => 'D',
                                    "cc_txn_narr" => ''
                                ];
                            } 
                        }
                    } //for loop

                    foreach ($item_account_array as $account_id => $amount) {

                        $txn_data = array(
                            "comp_id"               => $this->company_id,
                            "comp_vch_series_id"    => $voucher_series,
                            "voucher_txn_id"        => $voucher_txn_id,
                            "master_id"             => $account_id,
                            'master_id_type'        => 'acc'
                        );
                        $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);    

                        $insert_data  = [
                            'comp_id'            => $this->company_id,
                            'acc_id'             => $account_id,
                            'acc_txn_date'       => $voucher_date,
                            'acc_txn_amount'     => $amount,                        
                            'acc_txn_drcr'       => 'd',
                            'comp_vch_series_no' => $voucher_no,
                            'posted_on'          => date('Y-m-d H:i:s'),                        
                            'voucher_txn_id'     => $voucher_txn_id,
                            'voucher_type_id'    => $voucher_type_id,
                            'txn_id'             => $txn_id,
                            'acc_bal'            => 0
                        ];  
                        $this->TransactionModel->add_acc_txn_data($insert_data);
                        $this->TransactionModel->update_account_balance($account_id);
                    }

                    if(count($ccdata_undefined))
                    {
                        foreach($ccdata_undefined as $value)
                        {
                            $cc_txn_data = [
                                'cc_id'              => $value['cc_id'],
                                'comp_id'            => $this->company_id,
                                'acc_id'             => $value['account_id'],
                                'voucher_txn_id'     => $voucher_txn_id,
                                'voucher_type_id'    => $voucher_type_id,
                                'comp_vch_series_id' => $voucher_series,
                                'cc_txn_date'        => $voucher_date,
                                'cc_txn_drcr'        => $value['cc_txn_drcr'],
                                'cc_txn_amt'         => $value['cc_txn_amt'],
                                'cc_txn_bal'         => 0,
                                'cc_txn_narr'        => $value['cc_txn_narr'],
                            ];
                            $this->TransactionModel->add_cc_txn($cc_txn_data);
                        }
                    }
                }

                if($item_data_to){
                    $item_account_array = [];
                    foreach($item_data_to as $value)
                    {

                        $txn_data = array(
                            "comp_id"               => $this->company_id,
                            "comp_vch_series_id"    => $voucher_series,
                            "voucher_txn_id"        => $voucher_txn_id,
                            "master_id"             => $value['item_id'],
                            'master_id_type'        => 'itm'
                        );
                        $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

                        $insert_data   = array(
                                'comp_id'             => $this->company_id,
                                'item_txn_date'       => $voucher_date,
                                'item_txn_amount'     => $value['item_total_amount'],
                                'item_txn_drcr'       => 'c',
                                'item_txn_qty'        => $value['item_qty'],
                                'description'         => '',
                                'item_id'             => $value['item_id'],
                                'voucher_txn_id'      => $voucher_txn_id,
                                'voucher_type_id'     => $voucher_type_id,
                                'mat_cent_id'         => $matrcntr_id,
                                'bo_id'               => 0,
                                'txn_id'              => $txn_id
                           );   
                        $item_txn_id = $this->TransactionModel->add_itm_txn_data($insert_data);

                        $get_item_info      =  $this->BalancesModel->get_item_balance_info($value['item_id']);
                        $item_open_qty      =  !empty($get_item_info['op_bal_qty']) ? $get_item_info['op_bal_qty'] : 0;
                        $item_open_value    =  !empty($get_item_info['op_bal_val']) ? $get_item_info['op_bal_val'] : 0;
                        

                        $insert_data = array(
                                "item_txn_date"         => $voucher_date,
                                "item_id"               => $value['item_id'],
                                'item_txn_drcr'         => 'c',
                                "txn_id"                => $txn_id,
                                "item_txn_id"           => $item_txn_id,
                                "voucher_txn_id"        => $voucher_txn_id,
                                "bo_id"                 => 0,
                                "mat_cent_id"           => $matrcntr_id,
                                "item_unit"             => $value['item_unit_id'],
                                "item_bal_qty"          => $value['item_qty'],
                       );
                        $this->BalancesModel->add_itemtxnbal($value['item_id'],$insert_data,'c',$item_open_qty,$item_open_value);

                        // $this->TransactionModel->update_item_balance($party_id);  //function not created in model

                        $item_info  =  $this->TransactionModel->get_item_info($value['item_id']);
                        $account_id  =  $item_info['item_sales_acc'];

                        if(isset($item_account_array[$account_id]))
                            $item_account_array[$account_id] += parseAmount($value['item_total_amount']);
                        else
                            $item_account_array[$account_id] = parseAmount($value['item_total_amount']);

                        $txn_data = array(
                            "comp_id"               => $this->company_id,
                            "comp_vch_series_id"    => $voucher_series,
                            "voucher_txn_id"        => $voucher_txn_id,
                            "master_id"             => $account_id,
                            'master_id_type'        => 'acc'
                        );
                        
                    } //for loop

                    foreach ($item_account_array as $account_id => $amount) {

                        $txn_data = array(
                            "comp_id"               => $this->company_id,
                            "comp_vch_series_id"    => $voucher_series,
                            "voucher_txn_id"        => $voucher_txn_id,
                            "master_id"             => $account_id,
                            'master_id_type'        => 'acc'
                        );
                        $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);    

                        $insert_data  = [
                            'comp_id'            => $this->company_id,
                            'acc_id'             => $account_id,
                            'acc_txn_date'       => $voucher_date,
                            'acc_txn_amount'     => $amount,                        
                            'acc_txn_drcr'       => 'c',
                            'comp_vch_series_no' => $voucher_no,
                            'posted_on'          => date('Y-m-d H:i:s'),                        
                            'voucher_txn_id'     => $voucher_txn_id,
                            'voucher_type_id'    => $voucher_type_id,
                            'txn_id'             => $txn_id,
                            'acc_bal'            => 0
                        ];  
                        $this->TransactionModel->add_acc_txn_data($insert_data);
                        $this->TransactionModel->update_account_balance($account_id);
                    } 
                }
                
                if($acc_data_from){
                    foreach($acc_data_from as $value)
                    {
                        if($value['acc_type'] == 'acc')
                        {
                            $insert_data = [
                                "comp_id"             => $this->company_id,
                                "comp_vch_series_id"  => $voucher_series,
                                "voucher_txn_id"      => $voucher_txn_id,
                                "master_id"           => $value['acc_id'],
                                'master_id_type'      => 'acc'
                            ];

                            $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);

                            $insert_data  = [
                                'comp_id'            => $this->company_id,
                                'acc_id'             => $value['acc_id'],
                                'acc_txn_date'       => $voucher_date,
                                'acc_txn_amount'     => $value['acc_amount'],                        
                                'acc_txn_drcr'       => 'd',
                                'acc_txn_narr'       => '',
                                'comp_vch_series_no' => $voucher_no,
                                'posted_on'          => date('Y-m-d H:i:s'),                        
                                'voucher_txn_id'     => $voucher_txn_id,
                                'voucher_type_id'    => $voucher_type_id,
                                'txn_id'             => $txn_id,
                                'acc_bal'            => 0
                            ];  

                            $this->TransactionModel->add_acc_txn_data($insert_data);
                            $this->TransactionModel->update_account_balance($value['acc_id']);

                            if(empty($bbbdata))
                            {
                                if(isset($value['is_bbb']) && $value['is_bbb'] == 1)
                                {
                                    $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['acc_id']);
                                    if($bill_ref_id)
                                    {

                                        $bill_txn_data = [
                                            'bills_ref_id'       => $bill_ref_id,
                                            'comp_id'            => $this->company_id,
                                            'acc_id'             => $value['acc_id'],
                                            'voucher_txn_id'     => $voucher_txn_id,
                                            'voucher_type_id'    => $voucher_type_id,
                                            'comp_vch_series_id' => $voucher_series,
                                            'bills_txn_date'     => $voucher_date,
                                            'bills_txn_drcr'     => 'D',
                                            'bills_txn_amt'      => $value['acc_amount'],
                                            'bills_txn_bal'      => 0,
                                            'bills_txn_narr'     => '',
                                        ];
                                        $this->TransactionModel->add_bill_txn($bill_txn_data);
                                    }
                                }
                            }

                            if(empty($ccdata))
                            {
                                if(isset($value['is_cc']) && $value['is_cc'] == 1)
                                {

                                    $cc_txn_data = [
                                        'cc_id'              => 1,
                                        'comp_id'            => $this->company_id,
                                        'acc_id'             => $value['acc_id'],
                                        'voucher_txn_id'     => $voucher_txn_id,
                                        'voucher_type_id'    => $voucher_type_id,
                                        'comp_vch_series_id' => $voucher_series,
                                        'cc_txn_date'        => $voucher_date,
                                        'cc_txn_drcr'        => 'D',
                                        'cc_txn_amt'         => $value['acc_amount'],
                                        'cc_txn_bal'         => 0,
                                        'cc_txn_narr'        => '',
                                    ];
                                    $this->TransactionModel->add_cc_txn($cc_txn_data);
                                    
                                }
                            }
                        }
                        if($value['acc_type'] == 'bsd')
                        {
                            $txn_data = array(
                                "comp_id"               => $this->company_id,
                                "comp_vch_series_id"    => $voucher_series,
                                "voucher_txn_id"        => $voucher_txn_id,
                                "master_id"             => $value['acc_id'],
                                'master_id_type'        => 'bsd'
                            );
                            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

                            $insert_data  = array(
                                "comp_id"                   => $this->company_id,
                                "sundry_txn_date"           => $voucher_date,
                                "sundry_txn_amount"         => $value['acc_amount'],
                                "sundry_txn_drcr"           => 'd',                           
                                "comp_vch_name"             => $voucher_no, // ?
                                "comp_vch_series_no"        => $voucher_no,
                                "bill_sundry_id"            => $value['acc_id'],
                                "sundry_bal"                => 0,
                                "voucher_txn_id"            => $voucher_txn_id, 
                                "voucher_type_id"           => $voucher_type_id,
                                'txn_id'                    => $txn_id,
                                'sundry_tag_rate'           => ''
                            );
                            $this->TransactionModel->add_sundry_txn_data($insert_data);
                        }
                    }
                }

                if($acc_data_to){
                    foreach($acc_data_to as $value)
                    {
                        if($value['acc_type'] == 'acc')
                        {
                            $insert_data = [
                                "comp_id"             => $this->company_id,
                                "comp_vch_series_id"  => $voucher_series,
                                "voucher_txn_id"      => $voucher_txn_id,
                                "master_id"           => $value['acc_id'],
                                'master_id_type'      => 'acc'
                            ];

                            $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);

                            $insert_data  = [
                                'comp_id'            => $this->company_id,
                                'acc_id'             => $value['acc_id'],
                                'acc_txn_date'       => $voucher_date,
                                'acc_txn_amount'     => $value['acc_amount'],                        
                                'acc_txn_drcr'       => 'c',
                                'acc_txn_narr'       => '',
                                'comp_vch_series_no' => $voucher_no,
                                'posted_on'          => date('Y-m-d H:i:s'),                        
                                'voucher_txn_id'     => $voucher_txn_id,
                                'voucher_type_id'    => $voucher_type_id,
                                'txn_id'             => $txn_id,
                                'acc_bal'            => 0
                            ];  

                            $this->TransactionModel->add_acc_txn_data($insert_data);
                            $this->TransactionModel->update_account_balance($value['acc_id']);

                            if(empty($bbbdata))
                            {
                                if(isset($value['is_bbb']) && $value['is_bbb'] == 1)
                                {
                                    $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['acc_id']);
                                    if($bill_ref_id)
                                    {

                                        $bill_txn_data = [
                                            'bills_ref_id'       => $bill_ref_id,
                                            'comp_id'            => $this->company_id,
                                            'acc_id'             => $value['acc_id'],
                                            'voucher_txn_id'     => $voucher_txn_id,
                                            'voucher_type_id'    => $voucher_type_id,
                                            'comp_vch_series_id' => $voucher_series,
                                            'bills_txn_date'     => $voucher_date,
                                            'bills_txn_drcr'     => 'C',
                                            'bills_txn_amt'      => $value['acc_amount'],
                                            'bills_txn_bal'      => 0,
                                            'bills_txn_narr'     => '',
                                        ];
                                        $this->TransactionModel->add_bill_txn($bill_txn_data);
                                    }
                                }
                            }

                            if(empty($ccdata))
                            {
                                if(isset($value['is_cc']) && $value['is_cc'] == 1)
                                {

                                    $cc_txn_data = [
                                        'cc_id'              => 1,
                                        'comp_id'            => $this->company_id,
                                        'acc_id'             => $value['acc_id'],
                                        'voucher_txn_id'     => $voucher_txn_id,
                                        'voucher_type_id'    => $voucher_type_id,
                                        'comp_vch_series_id' => $voucher_series,
                                        'cc_txn_date'        => $voucher_date,
                                        'cc_txn_drcr'        => 'C',
                                        'cc_txn_amt'         => $value['acc_amount'],
                                        'cc_txn_bal'         => 0,
                                        'cc_txn_narr'        => '',
                                    ];
                                    $this->TransactionModel->add_cc_txn($cc_txn_data);
                                    
                                }
                            }
                        }
                        if($value['acc_type'] == 'bsd')
                        {
                            $txn_data = array(
                                "comp_id"               => $this->company_id,
                                "comp_vch_series_id"    => $voucher_series,
                                "voucher_txn_id"        => $voucher_txn_id,
                                "master_id"             => $value['acc_id'],
                                'master_id_type'        => 'bsd'
                            );
                            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

                            $insert_data  = array(
                                "comp_id"                   => $this->company_id,
                                "sundry_txn_date"           => $voucher_date,
                                "sundry_txn_amount"         => $value['acc_amount'],
                                "sundry_txn_drcr"           => 'c',                           
                                "comp_vch_name"             => $voucher_no, // ?
                                "comp_vch_series_no"        => $voucher_no,
                                "bill_sundry_id"            => $value['acc_id'],
                                "sundry_bal"                => 0,
                                "voucher_txn_id"            => $voucher_txn_id, 
                                "voucher_type_id"           => $voucher_type_id,
                                'txn_id'                    => $txn_id,
                                'sundry_tag_rate'           => ''
                            );
                            $this->TransactionModel->add_sundry_txn_data($insert_data);
                        }
                    }
                }

                $insert_data = [
                      "comp_id"             => $this->company_id,
                      "comp_vch_series_id"  => $voucher_series,
                      "voucher_txn_id"      => $voucher_txn_id,
                      "master_id"           => 0,
                      'master_id_type'      => 'nrr'
                ];

                $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
                $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

                if(count($bbbdata))
                {
                    foreach($bbbdata as $key => $value)
                    {
                        $bill_ref_id = 0;
                        $bill_due_date = $value['due_date'] != '' ? date("Y-m-d", strtotime($value['due_date'])) : $voucher_date;
                        
                        if($value['method'] == 'New Ref.')
                        {
                            $bill_master_data = [
                                'bills_ref_name' => $value['reference'],
                                'acc_id'         => $value['account_id'],
                                'bills_status'   => 'pending',
                                'bill_due_date'  => $bill_due_date,
                            ];
                            $bill_ref_id = $this->TransactionModel->add_bill_master($bill_master_data);
                        }
                        if($value['method'] == 'Adjustment')
                        {
                            if($value['reference_id'] == 0 && $value['reference'] == 'UNDEFINED'){
                                $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['account_id']);  
                            }
                            else{
                                $bill_ref_id = $value['reference_id'];
                                $bill_master_data = [
                                    'bill_due_date'  => $bill_due_date
                                ];
                                $this->TransactionModel->update_bill_master($bill_ref_id, $bill_master_data);
                            }
                            
                        }
                        if($bill_ref_id)
                        {
                            $bill_txn_data = [
                                'bills_ref_id'       => $bill_ref_id,
                                'comp_id'            => $this->company_id,
                                'acc_id'             => $value['account_id'],
                                'voucher_txn_id'     => $voucher_txn_id,
                                'voucher_type_id'    => $voucher_type_id,
                                'comp_vch_series_id' => $voucher_series,
                                'bills_txn_date'     => $voucher_date,
                                'bills_txn_drcr'     => $value['drcr'],
                                'bills_txn_amt'      => $value['amount'],
                                'bills_txn_bal'      => 0,
                                'bills_txn_narr'     => $value['narration'],
                            ];
                            $this->TransactionModel->add_bill_txn($bill_txn_data);
                        }
                    }
                }
                    
                if(count($ccdata))
                {
                    foreach($ccdata as $value)
                    {
                        $cc_txn_data = [
                            'cc_id'              => $value['cc_id'],
                            'comp_id'            => $this->company_id,
                            'acc_id'             => $value['account_id'],
                            'voucher_txn_id'     => $voucher_txn_id,
                            'voucher_type_id'    => $voucher_type_id,
                            'comp_vch_series_id' => $voucher_series,
                            'cc_txn_date'        => $voucher_date,
                            'cc_txn_drcr'        => $value['cc_txn_drcr'],
                            'cc_txn_amt'         => $value['cc_txn_amt'],
                            'cc_txn_bal'         => 0,
                            'cc_txn_narr'        => $value['cc_txn_narr'],
                        ];
                        $this->TransactionModel->add_cc_txn($cc_txn_data);
                    }
                }


                return json_encode(['status' => true, 'message' => 'Voucher Updated']); 
            }              
        }


        
        if(in_array($voucher_type_id, $voucher_type_array) && $vch_subtype_id == 0){

            if($voucher_tag == 'OPTIONL')
            {
                $data['account_transactions'] = $this->TransactionModel->get_all_account_oth_transactions($voucher_txn_id, true);
                $data['cc_data'] = [];
                $data['bbb_data'] = [];
                $data['oCheck'] = 1;
            }
            else
            {
                $data['account_transactions'] = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
                $data['cc_data'] = $this->TransactionModel->get_cc_txn_data($voucher_txn_id);
                $data['bbb_data'] = $this->TransactionModel->get_bills_txn_data($voucher_txn_id);
                $data['oCheck'] = 0;
            }
            
            $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);
            $get_narration_info = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',0);


            $data['message_output']           = $this->message_output;$data['voucher_txn_id']           = $voucher_txn_id;
            $data['base_url']                 = $this->base_url; 
            $data['folder_path']              = $this->folder_path; 
            $data['voucher_name']             = $voucher_detail['comp_vch_type'];
            $data['voucher_type_id']          = $voucher_type_id;      
            $data['voucher_no']               = $this->TransactionModel->get_voucher_no($voucher_type_id);     
            $data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
            $data['narration']                = !empty($get_narration_info) ? $get_narration_info['vch_narr'] : '';
            $data['voucher_series']           = $voucher_info['comp_vch_series_id'];
            $data['voucher_no']               = $voucher_info['comp_vch_no'];
            $data['voucher_date']             = date('d-m-Y', strtotime($voucher_info['voucher_date']));
            $data['voucher_txn_id']           = $voucher_txn_id;
            $data['bills_method_list']        = ['','New Ref.','Adjustment'];

            $accounts_list = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/acc.json'),true);
            $bsd_accounts  = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json'),true);
             
            $company_all_acc_bsd  = array_merge($accounts_list,$bsd_accounts);
            $data['acc_bsd_json_file']        = json_encode($company_all_acc_bsd);
			
            $data['currency_list'] = $this->TransactionModel->get_currency_list();
            $data['currency_id'] = $voucher_info['currency_id'];
			// echo "<pre>";print_r($data);exit;
            return view($this->folder_path.'vouchers/edit_invoice',$data);
        }

        if($voucher_type_id == 5 && $vch_subtype_id == 23)
        {
            $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);
            $get_narration_info = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',0);

            $account_transactions = $this->TransactionModel->get_journal_account_transactions($voucher_txn_id);
            $data['l_accounts'] = $account_transactions['l_accounts'];
            $data['r_accounts'] = $account_transactions['r_accounts'];

            $item_transactions = $this->TransactionModel->get_journal_item_transactions($voucher_txn_id);
            $data['l_items'] = $item_transactions['l_items'];
            $data['r_items'] = $item_transactions['r_items'];

            $data['cc_data'] = $this->TransactionModel->get_cc_txn_data($voucher_txn_id);
            $data['bbb_data'] = $this->TransactionModel->get_bills_txn_data($voucher_txn_id);
            

            $data['message_output']           = $this->message_output;
            $data['base_url']                 = $this->base_url; 
            $data['folder_path']              = $this->folder_path; 
            $data['voucher_name']             = $voucher_detail['comp_vch_type'];     
            $data['voucher_type_id']          = $voucher_type_id;      
            $data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
            // $data['items_list']             = $this->TransactionModel->items_list();
            $data['units_list']               = $this->TransactionModel->units_dropdown();
            $data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
            $data['matrcntr_dropdown']        = $this->TransactionModel->matrcntr_dropdown();
            $data['narration']                = !empty($get_narration_info) ? $get_narration_info['vch_narr'] : '';
            $data['voucher_series']           = $voucher_info['comp_vch_series_id'];
            $data['voucher_no']               = $voucher_info['comp_vch_no'];
            $data['matrcntr_id']                = $voucher_info['mat_cent_id'];
            $data['voucher_date']             = date('d-m-Y', strtotime($voucher_info['voucher_date']));
            $data['voucher_txn_id']           = $voucher_txn_id;
            $data['bills_method_list']        = ['','New Ref.','Adjustment'];
            $data['item_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
        
            $accounts_list = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/acc.json'),true);
            $bsd_accounts  = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json'),true);
		 
		    $company_all_acc_bsd  = array_merge($accounts_list,$bsd_accounts);
            $data['acc_bsd_json_file']        = json_encode($company_all_acc_bsd);

            $data['currency_list'] = $this->TransactionModel->get_currency_list();
            $data['currency_id'] = $voucher_info['currency_id'];

            return view($this->folder_path.'vouchers/edit_item',$data);
        }
    }

    public function reverse_journal($voucher_txn_id)
    {
        $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
        if(empty($voucher_info)){
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $voucher_type_id = $voucher_info['voucher_type_id'];
        $vch_subtype_id = $voucher_info['vch_subtype_id'];

        $data['account_transactions'] = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);

        $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);
        $get_narration_info = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',0);


        $data['message_output']           = $this->message_output;
        $data['base_url']                 = $this->base_url; 
        $data['folder_path']              = $this->folder_path; 
        $data['voucher_name']             = $voucher_detail['comp_vch_type'];
        $data['voucher_type_id']          = $voucher_type_id;      
        $data['voucher_no']               = $this->TransactionModel->get_voucher_no($voucher_type_id);     
        $data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
        $data['narration']                = !empty($get_narration_info) ? $get_narration_info['vch_narr'] : '';
        $data['voucher_series']           = $voucher_info['comp_vch_series_id'];
        $data['voucher_no']               = $voucher_info['comp_vch_no'];
        $data['voucher_date']             = date('d-m-Y', strtotime($voucher_info['voucher_date']));
        $data['voucher_txn_id']           = $voucher_txn_id;

        $reversal_date = '';
        if($voucher_info['voucher_tag'] == 'REVJRNL')
        {
            $csrf_data = $this->TransactionModel->get_crsref($voucher_txn_id, 'REVJRNL');
            if($csrf_data)
                $reversal_date = date('d-m-Y', strtotime($csrf_data));;
        }
        
        $data['reversal_date'] = $reversal_date;
        $data['currency'] = $this->TransactionModel->get_currency($voucher_info['currency_id']);
        
        return view($this->folder_path.'vouchers/reverse_journal',$data);
    }

    public function delete($voucher_txn_id)
    {

        $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);

        if(!empty($voucher_info)){

            $data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
            foreach ($data as $key => $value) {
                if($value['master_id_type'] == 'acc'){
                    $this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                }
                if($value['master_id_type'] == 'itm'){
                    $this->TransactionModel->delete_itm_bal_data($value['master_id'],$voucher_txn_id);
                    $this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                }
                if($value['master_id_type'] == 'bsd'){
                    $this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
                }
            }
            $this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
            $this->TransactionModel->delete_itm_oth_data($voucher_txn_id);

            $this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
            $this->TransactionModel->delete_voucher_conso_data($voucher_txn_id);

            $this->TransactionModel->delete_cc_txn($voucher_txn_id);
            $this->TransactionModel->delete_bills_txn($voucher_txn_id);                 
            $this->TransactionModel->delete_all_narrations($voucher_txn_id);

        }

        return json_encode(['status' => true, 'message' => 'Voucher Deleted']);
    }

    public function delete_reverse_journal($voucher_txn_id)
    {

        $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);

        if(!empty($voucher_info)){
            
            if($voucher_info['voucher_tag'] == 'REVJRNL')
            {
                $voucher_txn_id2 = $this->TransactionModel->get_crsref_reverse($voucher_txn_id, 'RJVHTXN');
                if($voucher_txn_id2 != ''){
                    $voucher_info2 = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id2);
                    
                    if(!empty($voucher_info2)){
                        $data2 = $this->TransactionModel->get_comp_txn_data($voucher_txn_id2);
                        foreach ($data2 as $key => $value) {
                            if($value['master_id_type'] == 'acc'){
                                $this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id2);
                            }
                            if($value['master_id_type'] == 'itm'){
                                $this->TransactionModel->delete_itm_bal_data($value['master_id'],$voucher_txn_id2);
                                $this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id2);
                            }
                            if($value['master_id_type'] == 'bsd'){
                                $this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id2);
                            }
                        }


                        $this->TransactionModel->delete_onloadchks_data($voucher_txn_id2);
                        $this->TransactionModel->delete_acc_crsref_data($voucher_txn_id2);
                        $this->TransactionModel->delete_comp_txn_data($voucher_txn_id2);
                        $this->TransactionModel->delete_voucher_conso_data($voucher_txn_id2);                
                        $this->TransactionModel->delete_all_narrations($voucher_txn_id2);
                    }
                }
            }

            if($voucher_info['voucher_tag'] == 'RJVHTXN')
            {
                $voucher_txn_id2 = $this->TransactionModel->get_crsref($voucher_txn_id, 'RJVHTXN');
                if($voucher_txn_id2 != ''){
                    
                    $voucher_info2 = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id2);
                    
                    if(!empty($voucher_info2)){
                        $data2 = $this->TransactionModel->get_comp_txn_data($voucher_txn_id2);
                        foreach ($data2 as $key => $value) {
                            if($value['master_id_type'] == 'acc'){
                                $this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id2);
                            }
                            if($value['master_id_type'] == 'itm'){
                                $this->TransactionModel->delete_itm_bal_data($value['master_id'],$voucher_txn_id2);
                                $this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id2);
                            }
                            if($value['master_id_type'] == 'bsd'){
                                $this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id2);
                            }
                        }


                        $this->TransactionModel->delete_onloadchks_data($voucher_txn_id2);
                        $this->TransactionModel->delete_acc_crsref_data($voucher_txn_id2);
                        $this->TransactionModel->delete_comp_txn_data($voucher_txn_id2);
                        $this->TransactionModel->delete_voucher_conso_data($voucher_txn_id2);                
                        $this->TransactionModel->delete_all_narrations($voucher_txn_id2);
                    }
                }
            }

            if($voucher_info['voucher_tag'] == 'RJVHTXP')
            {
                $voucher_txn_id2 = $this->TransactionModel->get_crsref($voucher_txn_id, 'RJVHTXN');
                if($voucher_txn_id2 != ''){
                    
                    $voucher_info2 = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id2);
                    
                    if(!empty($voucher_info2)){
                        $data2 = $this->TransactionModel->get_comp_txn_data($voucher_txn_id2);
                        foreach ($data2 as $key => $value) {
                            if($value['master_id_type'] == 'acc'){
                                $this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id2);
                            }
                            if($value['master_id_type'] == 'itm'){
                                $this->TransactionModel->delete_itm_bal_data($value['master_id'],$voucher_txn_id2);
                                $this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id2);
                            }
                            if($value['master_id_type'] == 'bsd'){
                                $this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id2);
                            }
                        }


                        $this->TransactionModel->delete_onloadchks_data($voucher_txn_id2);
                        $this->TransactionModel->delete_acc_crsref_data($voucher_txn_id2);
                        $this->TransactionModel->delete_comp_txn_data($voucher_txn_id2);
                        $this->TransactionModel->delete_voucher_conso_data($voucher_txn_id2);                
                        $this->TransactionModel->delete_all_narrations($voucher_txn_id2);
                    }
                }
            }

            $data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
            foreach ($data as $key => $value) {
                if($value['master_id_type'] == 'acc'){
                    $this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                }
                if($value['master_id_type'] == 'itm'){
                    $this->TransactionModel->delete_itm_bal_data($value['master_id'],$voucher_txn_id);
                    $this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                }
                if($value['master_id_type'] == 'bsd'){
                    $this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
                }
            }


            $this->TransactionModel->delete_onloadchks_data($voucher_txn_id);
            $this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
            $this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
            $this->TransactionModel->delete_voucher_conso_data($voucher_txn_id);                
            $this->TransactionModel->delete_all_narrations($voucher_txn_id);

        }

        return json_encode(['status' => true, 'message' => 'Voucher Deleted']);
    } 

   
   public  function view_vouchers($voucher_type_id)
	 {
		$get_voucher_detail         = $this->VouchersModel->get_voucher_info($voucher_type_id,$this->company_id);	
		if(!$get_voucher_detail)
		   return redirect()->to($this->base_url.'admin/vouchers/view_vouchers');
	    $voucher_name               = $get_voucher_detail['comp_vch_type'];
	   
	    
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
        $data['get_voucher_detail'] = $get_voucher_detail;		
		$data['voucher_name']       = $voucher_name;		
        $data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['voucher_trans']      = $this->VouchersModel->ajax_vouchers_list($voucher_type_id,$this->company_id);
	    return view($this->folder_path.'vouchers/view',$data);
	   
	 } 

    function ajax_company_accounts(){
        echo  $this->VouchersModel->get_company_accounts($this->company_id);
    }
    
    public function item_valuation()
    {
        $this->BalancesModel->update_item_balance(1,6,1);
    }

 
}