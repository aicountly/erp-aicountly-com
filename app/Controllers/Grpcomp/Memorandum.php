<?php
namespace App\Controllers\Admin;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\TransactionModel;
use App\Models\Admin\BalancesModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Memorandum extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url','text','custom_hepler']);
		$this->VouchersModel = new VouchersModel();
        $this->TransactionModel  = new TransactionModel();	
        $this->BalancesModel  = new BalancesModel();		
		$this->auth_session  = new auth_session();			
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
    
 
    public function invoice()
    {
        $voucher_type_id = 8;
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
            
            $voucher_data       = json_decode($voucherdata,true);
            $voucher_date       = $this->request->getVar('voucher_date');
            $voucher_series     = $this->request->getVar('voucher_series');
            $currency_id        = $this->request->getVar('currency_id');
         
            $voucher_date = date("Y-m-d", strtotime($voucher_date));
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
                "voucher_tag"           => '',
                "currency_id"           => $currency_id,
            );
            $voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);
            

            if($voucher_data)
            {
                foreach($voucher_data as $value)
                {

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
                        'acc_id'             => $value['acc_id'],
                        'acc_type'           => $value['acc_type'],
                        'acc_txn_date'       => $voucher_date,
                        'acc_txn_amount'     => $acc_txn_amount,                        
                        'acc_txn_drcr'       => $acc_txn_drcr,
                        'acc_txn_narr'       => $value['description'],
                        'comp_vch_series_no' => $voucher_no,                        
                        'voucher_txn_id'     => $voucher_txn_id,
                        'acc_bal'            => 0
                    ];  
                           
                    $this->TransactionModel->add_acc_memo_data($insert_data);
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
        $data['accounts_json_file']       = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/acc.json');
        $data['currency_list']            = $this->TransactionModel->get_currency_list();

        return view($this->folder_path.'memorandum/invoice',$data);       
    }

    

    public function edit($voucher_txn_id) // remove and delete method
    {  
        $voucher_type_id = 8; 
        $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id,$voucher_type_id);
        if(empty($voucher_info)){
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

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
            $currency_id = $this->request->getVar('currency_id');
            
            $voucher_date = date("Y-m-d", strtotime($voucher_date));
            $voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);

            $this->TransactionModel->delete_acc_memo_data($voucher_txn_id);
            $this->TransactionModel->delete_all_narrations($voucher_txn_id);
            
            $insert_data  = array(
                    "comp_vch_series_id"        => $voucher_series,
                    "voucher_date"              => $voucher_date,
                    "currency_id"               => $currency_id,
            );
            $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);
            

            if($voucher_data)
            {
                foreach($voucher_data as $value)
                {

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
                        'acc_id'             => $value['acc_id'],
                        'acc_type'           => $value['acc_type'],
                        'acc_txn_date'       => $voucher_date,
                        'acc_txn_amount'     => $acc_txn_amount,                        
                        'acc_txn_drcr'       => $acc_txn_drcr,
                        'acc_txn_narr'       => $value['description'],
                        'comp_vch_series_no' => $voucher_no,                        
                        'voucher_txn_id'     => $voucher_txn_id,
                        'acc_bal'            => 0
                    ];  
                           
                    $this->TransactionModel->add_acc_memo_data($insert_data);
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

            return json_encode(['status' => true, 'message' => 'Voucher Updated']);
            
                         
        }

        $data['account_transactions'] = $this->TransactionModel->get_memo_account_transactions($voucher_txn_id);
        

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

        $data['accounts_json_file']       = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/acc.json');
        $data['currency_list']            = $this->TransactionModel->get_currency_list();
        $data['currency_id']              = $voucher_info['currency_id'];

        return view($this->folder_path.'memorandum/edit',$data);
        

    }

    public function delete($voucher_txn_id)
    {
        $voucher_type_id = 8;
        $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);

        if(!empty($voucher_info)){

            $this->TransactionModel->delete_acc_memo_data($voucher_txn_id);
            $this->TransactionModel->delete_voucher_conso_data($voucher_txn_id);                 
            $this->TransactionModel->delete_all_narrations($voucher_txn_id);

        }

        return json_encode(['status' => true, 'message' => 'Voucher Deleted']);
    } 

 
}