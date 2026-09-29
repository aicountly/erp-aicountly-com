<?php
namespace App\Controllers\Admin;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\AccountsModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Traits\TransactionTrait;
class Vouchers extends BaseController
{
  use TransactionTrait;
  function __construct(){  
   helper(['form', 'url','text','custom_hepler']);
   $this->session    	 = \Config\Services::session();   
   $this->VouchersModel  =  new VouchersModel();
   $this->AccountsModel  =  new AccountsModel();
   $this->auth_session   =  new auth_session();		
   $this->CommonModel    =  new CommonModel();				
   $this->auth_session->user_restrict();   
   $this->base_url       =  base_url().getenv('AdminPath');
   $this->folder_path    =  getenv('AdminPath');   
   $this->auth_session->is_company_opened();
   $this->comp_code      =  $this->session->get('ses_company_code');
   $this->company_id     =  $this->session->get('ses_company_id');
   $this->bo_id          =  $this->session->get('ses_boid');
   $this->fy_id          =  $this->session->get('ses_comp_fy_id');
   $this->profileid      =  $this->session->get('ses_cmp_prf_id');
   $this->is_valid_url   =  validate_web_url(current_url())['allowed'];    
	if (!$this->is_valid_url) {
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
     }   	
  }
  
  public function ValidateDateEntry(){
	  if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
		   $series_id     = $this->request->getVar('series_id_val');
		   $dateposted        = date("Y-m-d",strtotime($this->request->getVar('dateposted')));
		  echo $this->VouchersModel->checkDateEntryAllowed($series_id,$dateposted );	
	  }
  }
  public function GetAutoBillNo(){
	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
	    $voucher_type_id =  (int)$this->request->getVar('voucher_type_id');
		$series_id       =  (int)$this->request->getVar('series_id');
		$voucher_date    =  $this->request->getVar('voucher_date');
		$voucher_txn_id  =  (int) $this->request->getVar('voucher_txn_id') ?? 0;
	
		$billno          =  $this->VouchersModel->get_billno_format($voucher_type_id,$series_id,$voucher_date,1,'counter');	
	    
	    $response        =  array("billno"=>$billno);
	    echo json_encode($response);
	   }
    }
  
  public function load_recent_items()
{
	$cmp_id = $this->company_id;
	$fy_id  = $this->fy_id;
    $item_file = WRITEPATH . "comp{$cmp_id}/itm{$fy_id}.json";    
    $items = file_exists($item_file) ? json_decode(file_get_contents($item_file), true) : [];
    
    return $this->response->setJSON([
        'items'    => $items
    ]); 
}
 public function load_recent_accounts()
{
    $cmp_id = $this->company_id;
	$fy_id  = $this->fy_id;
	$acc_file  = WRITEPATH . "comp{$cmp_id}/acc{$fy_id}.json";

    $accs  = file_exists($acc_file)  ? json_decode(file_get_contents($acc_file), true)  : [];

    return $this->response->setJSON([
        'accounts' => $accs
    ]);
}

  public function GetTaxInfo(){
	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
		$voucher_date    =  $this->request->getVar('voucher_date');
		$tax_id          =  $this->request->getVar('tax_id');
	    $taxinfo         =  $this->VouchersModel->get_gst_taxinfo($tax_id,$voucher_date);	
	    $response        =  array('rates'=>$taxinfo);
	    echo json_encode($response);
	   }
    }
  public function invoice($voucher_type_id){
	$voucher_type_array = [1,5,9,13];
    if(!in_array($voucher_type_id, $voucher_type_array)){
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
    }	
    $voucher_detail            = $this->VouchersModel->get_vouchertype_info($voucher_type_id);
	$Voucher_TxnApproval_Info  = $this->VouchersModel->Voucher_TxnApproval();
	$Voucher_TxnApproval       = $Voucher_TxnApproval_Info['approval_amt'] ?? '' ;
	$Voucher_TxnApproval_UUID  = $Voucher_TxnApproval_Info['uuid_to'] ?? 0; // who will approve, reject voucher
    if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
       $ip = $_SERVER['REMOTE_ADDR'];
	   if($ip=='103.172.223.178' || $ip=='103.172.223.179'){
	  // echo "<pre>";print_r($_POST);die();
	   } 
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

      $long_narration     = $this->request->getVar('narration');
      $voucherdata        = $this->request->getVar('voucherdata');
      $voucher_data       = json_decode($voucherdata,true);
      $voucher_date       = $this->request->getVar('voucher_date');
      $voucher_series     = $this->request->getVar('voucher_series');	  
	  $FromSaleVch        = $this->request->getVar('FromSaleVch');// Auto Recipt Voucher From Sale Voucher
	  $SaleVchTxnId       = $this->request->getVar('SaleVchTxnId');
	  $btnid              = $this->request->getVar('btnid');// submitbtn default save button
	
	  if(!$FromSaleVch)
		  $FromSaleVch =0;
	  
	  $FromPurchaseVch     = $this->request->getVar('FromPurchaseVch');// Auto Payment Voucher From Purchase Voucher
	  $PurchaseVchTxnId    = $this->request->getVar('PurchaseVchTxnId');
	  if(!$FromPurchaseVch)
		  $FromPurchaseVch =0;
      $VchOthrBo          = $this->request->getVar('VchOthrBo');
      if(!$VchOthrBo)
       $VchOthrBo =0;

      $voucher_date   = validate_date_by_fy($voucher_date);
      $currency_id    = $this->request->getVar('currency_id');
      $fcy_forex_rate = $this->request->getVar('fcy_forex_rate');
      $oCheck         = $this->request->getVar('oCheck'); // OPTIONAL VOUCHER TAG
      $rCheck         = $this->request->getVar('rCheck');
	  $ccdata = [];
      if(!empty($this->request->getVar('ccdata')))
        $ccdata = json_decode($this->request->getVar('ccdata'),true);

      $bbbdata = [];
      if(!empty($this->request->getVar('bbbdata')))
        $bbbdata = json_decode($this->request->getVar('bbbdata'),true);

      $prdata = [];
      if(!empty($this->request->getVar('prdata')))
        $prdata = json_decode($this->request->getVar('prdata'),true);
	
	 $sblgrdata = [];
      if(!empty($this->request->getVar('sblgr_data')))
        $sblgrdata = json_decode($this->request->getVar('sblgr_data'),true);
	
	  /**********  Save data to postgr_db as a draft  **************/
	    $total_debit     = 0;
		$total_fcy_debit = 0;
		$credit_acc_id   = 0;
		foreach ($voucher_data as $row) {
			if ($row['drcr'] === 'C') {
				$credit_acc_id = $row['account_id'];
			}
			if ($row['drcr'] === 'D') {
				$total_debit += $row['debit'] ?? 0;
				if(isset($row['debitfc']) && $row['debitfc']!='')
				$total_fcy_debit += $row['debitfc'];
			}
		}
		$vch_particulars='';
		$draft_vch_rec_id =0;
		$show_approval_message=0;
		if($credit_acc_id){
			$acc_info = $this->AccountsModel->account_info($credit_acc_id);
			$vch_particulars = $acc_info['acc_name']; 
		}
				
		$isoptional =FALSE;
		if($oCheck==1)
			$isoptional =TRUE;
		
		$is_cc=FALSE;
		if($ccdata)
           $is_cc=TRUE;			
	   
	   $is_bbb=FALSE;
		if($bbbdata)
           $is_bbb=TRUE;			
	   
	   $is_pr=FALSE;
		if($prdata)
           $is_pr=TRUE;
		
		$is_sblgr=FALSE;
		if($sblgrdata)
           $is_sblgr=TRUE;
	   
	    $pgrdata_drft    = array("cmp_id"=>$this->company_id,"hobo_id"=>$this->bo_id,
		                         "log_date_time"=>$voucher_date,"vch_type_id"=>$voucher_type_id,
					             "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($total_debit),"vch_fcy_amt"=>parseAmount($total_fcy_debit),"vch_fcy_rate"=>(float)$fcy_forex_rate,
								 "vch_particulars"=>$vch_particulars,"uuid_aictly"=>$this->session->get('uuid'),
								 "long_narr"=>$long_narration,"vch_series_id"=>(int)$voucher_series,"isoptional"=>$isoptional,
								 "vch_fcy_id"=>(int)$currency_id,"is_cc"=>(int)$is_cc,"is_bbb"=>(int)$is_bbb,"is_pr"=>(int)$is_pr,"is_sblgr"=>(int)$is_sblgr
					            ); 
						
		$draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);			  
	  
	   /********************************************************************/
	   if($btnid=='submitbtn_drft'){
		   return json_encode(['status' => true, 'message' => 'Voucher saved in draft mode']); 
	   }
 
      if($voucher_type_id == 5 && isset($rCheck) && $rCheck == 1) // Reverse Journal Voucher
      {
        $voucher_type_id = 16;
        
        $reversal_date     = $this->request->getVar('reversal_date');
        $reversal_date     = validate_date_by_fy($reversal_date);
        $voucher_tag       = 'REVJRNL';

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
		
        $voucher_txn_id          = $this->TransactionModel->add_voucher_cons_data($insert_data,$VchOthrBo);
        $insert_data = [
          'acct_txn_id'         => $voucher_txn_id,
          'acct_crs_id_type'    => 'vhtxnconso',
          'comp_id'             => $this->company_id,
          'txn_id'              => 0,
          'voucher_txn_id'      => $voucher_txn_id,
          'bo_id'               => $this->session->get('ses_boid'),
          'acc_cross_ref_type'  => 'REVJRNL',
          'acc_cross_ref_data'  => $reversal_date,
          'acc_cross_logdate'   => date('Y-m-d'),
        ];
        $this->TransactionModel->add_acc_crsref_data($insert_data,$VchOthrBo);
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
			    'acc_txn_fcy'        => ($fcy_forex_rate >0)?$acc_txn_amount*$fcy_forex_rate:0, 				
                'acc_txn_drcr'       => $acc_txn_drcr,
                'acc_txn_narr'       => $value['description'],
                'comp_vch_series_no' => $voucher_no,
                'posted_on'          => date('Y-m-d H:i:s'),                        
                'voucher_txn_id'     => $voucher_txn_id,
                'voucher_type_id'    => $voucher_type_id,
                'txn_id'             => $txn_id,
                'acc_bal'            => 0
              ];  

              $this->TransactionModel->add_acc_txn_data($insert_data,$VchOthrBo);
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
				'sundry_txn_fcy'            => ($fcy_forex_rate>0)?$acc_txn_amount*$fcy_forex_rate:0,
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
              $this->TransactionModel->add_sundry_txn_data($insert_data,$VchOthrBo);
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
        $voucher_txn_id2       = $this->TransactionModel->add_voucher_cons_data($insert_data,$VchOthrBo);
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

              if($VchOthrBo>0)
              $bo_id = $VchOthrBo;
              else
              $bo_id = $this->bo_id;

              $trigger_action .= "INSERT INTO `${db_name}`.`${comp_id}_accnttxnnn_${value['account_id']}_${fy_id}` (comp_id, acc_txn_date, acc_txn_amount, acc_txn_drcr, comp_vch_series_no, acc_id,txn_id, voucher_type_id, acc_bal, voucher_txn_id, bo_id, posted_on) VALUES ('${comp_id}', '${reversal_date}',${acc_txn_amount}, '${acc_txn_drcr}','${voucher_no2}', '${value['account_id']}', '${txn_id}', '${voucher_type_id}', '0', '${voucher_txn_id2}', '${bo_id}', '[[SYSTEM_DATE]]'); ";
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
              
              if($VchOthrBo>0)
              $bo_id = $VchOthrBo;
              else
              $bo_id = $this->bo_id;

              $trigger_action .= "INSERT INTO `${db_name}`.`${comp_id}_sundrytxnn_${value['account_id']}_${fy_id}` (comp_id, sundry_txn_date, sundry_txn_amount, sundry_txn_drcr, comp_vch_name,comp_vch_series_no, bill_sundry_id,txn_id, voucher_type_id, sundry_bal, voucher_txn_id, sundry_tag_rate, bo_id) VALUES ('${comp_id}', '${reversal_date}',${acc_txn_amount}, '${acc_txn_drcr}','${voucher_no2}','${voucher_no2}', '${value['account_id']}', '${txn_id}', '${voucher_type_id}', '0', '${voucher_txn_id2}', '0', '${bo_id}'); ";
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
          'domain'                => 'https://erp.aicountly.com/',
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
          'bo_id'               => $this->session->get('ses_boid'),
          'acc_cross_ref_type'  => 'RJVHTXN',
          'acc_cross_ref_data'  => $voucher_txn_id,
          'acc_cross_logdate'   => date('Y-m-d'),
        ];
        $this->TransactionModel->add_acc_crsref_data($insert_data);

        return json_encode(['status' => true, 'message' => 'Voucher Inserted']); 
      }

      // check migration
      $migrate_voucher_txn_id = $this->request->getVar('migrate_voucher_txn_id');            
      if($migrate_voucher_txn_id)
      {
        $voucher_txn_id = $migrate_voucher_txn_id;

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
        $this->TransactionModel->delete_pr_txn($voucher_txn_id);
        $this->TransactionModel->delete_bills_txn($voucher_txn_id);                 
        $this->TransactionModel->delete_all_narrations($voucher_txn_id);
        $insert_data  = array(
          "comp_vch_series_id"    => $voucher_series,
          "comp_vch_no"           => $voucher_no,
          "voucher_type_id"       => $voucher_type_id,
          "voucher_date"          => $voucher_date,
          "mat_cent_id"           => 0,
          "vch_subtype_id"        => 0,
          "voucher_tag"           => '',
          "currency_id"           => $currency_id,
        );
        $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);
      }
       
	   
	    $trans_result = $this->runTransaction(function($db) use ($Voucher_TxnApproval_UUID,$show_approval_message,$Voucher_TxnApproval,$sblgrdata,$ccdata,$bbbdata,$prdata,$currency_id,$fcy_forex_rate,$oCheck,$draft_vch_rec_id,$migrate_voucher_txn_id,$long_narration,$voucher_data,$voucher_type_id, $voucher_series, $voucher_date, $VchOthrBo,$FromPurchaseVch,$FromSaleVch,$SaleVchTxnId,$PurchaseVchTxnId)
		{
		if(!$migrate_voucher_txn_id){
		
		/************  Code for Voucher Payment, Receipt, Contra, Journal without item ********/ 
        $insert_data  = array(
          "cmp_id"           => $this->company_id,		  
          "vch_series_id"    => $voucher_series,          
          "vch_type_id"      => $voucher_type_id,
		  "vch_sub_type_id"  => 0,		   
          "vch_date"         => $voucher_date,
          "mat_cent_id"      => 0,
		  "draft_vch_rec_id" => $draft_vch_rec_id
         );		
        $voucher_txn_id = $this->VouchersModel->add_voucher_cons_data($insert_data,$VchOthrBo);
						   
		if($fcy_forex_rate >0){
		    $fcy_data = array("cmp_id"=>$this->company_id,"vch_txn_id"=>$voucher_txn_id,
			                   "vch_fcy_rate"=>$fcy_forex_rate,"cmp_fcy_mst_id"=>$currency_id
							  );	 
		    $this->VouchersModel->save_voucher_fcyrate($fcy_data);	
		    }		 		 
		}	
		
        if($voucher_data)
         {
		    $acc_cr_txn_total_amount =0;
			$acc_dr_txn_total_amount =0;
			$first_account_id = NULL;
			foreach($voucher_data as $key => $value)
			{
			  if($value['acc_type'] == 'acc' || $value['acc_type'] == 'bsd')
			  {
				if ($first_account_id == NULL && $value['drcr'] == 'D' && $voucher_type_id==9 ) {
					$first_account_id = $value['account_id']; 
				}
				if ($first_account_id == NULL && $value['drcr'] == 'C' && $voucher_type_id==13 ) {
					$first_account_id = $value['account_id']; 
				}
				if ($first_account_id == NULL && $value['drcr'] == 'C' && ($voucher_type_id==1 || $voucher_type_id==5) ) {
					$first_account_id = $value['account_id']; 
				}		
				$acc_grp_id = 0;
				$acc_parent_id =0;
				$under_main_grp_id = 0;
				if($value['account_id']){
					$account_info       = $this->AccountsModel->account_info($value['account_id'],'acc');  
					//SaveErrorLog("accid->".$value['account_id'].'---'.json_encode($account_info));
					
					if($account_info){
						$acc_grp_id    = $account_info['under_crs_mst_id'];
						$acc_parent_id = $account_info['crs_mst_parent_id'];
						if($acc_grp_id==0)
							$under_main_grp_id = 0;
						else{
							$group_info    = $this->AccountsModel->main_group_info($acc_grp_id);
							if($group_info)
								$under_main_grp_id = $group_info['under_acc_gp_id'] ?? 0;
							}				     
					}
				}
				
				$insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $value['account_id'],
				  'master_id_type'  => 'acc'
				];
				
				$acc_txn_amount      = 0;
				$acc_txn_fcy_amount  = 0;
				$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
				
				if($value['drcr'] == 'C'){
				  $acc_cr_txn_amount  = parseAmount($value["credit"]);
				  $acc_txn_amount     = parseAmount($value["credit"]);
				  $acc_txn_fcy_amount = parseAmount($value["creditfc"]); 
				  $acc_txn_drcr       = 2;
				  $acc_dr_txn_amount  = 0;				  
				  $acc_cr_txn_total_amount +=$acc_cr_txn_amount;
				}
				else{
				  $acc_dr_txn_amount = parseAmount($value["debit"]);
				  $acc_txn_amount    = parseAmount($value["debit"]); 
				  $acc_txn_fcy_amount = parseAmount($value["debitfc"]); 
				  $acc_txn_drcr      = 1;
				  $acc_cr_txn_amount = 0;				  
				  $acc_dr_txn_total_amount +=$acc_dr_txn_amount;				 				  
				}
				
			
				if($Voucher_TxnApproval!='' && parseAmount($acc_txn_amount) > parseAmount($Voucher_TxnApproval)){
				 $acc_txn_type = 4; // voucher is pending for approval
				}
				 else
				 $acc_txn_type = ($oCheck==1)?2:1;
				
				
				$insert_data  = [
				  'cmp_id'           => (int)$this->company_id,
				  'acc_id'           => (int)$value['account_id'],
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => $acc_txn_drcr,
				  'acc_txn_amt'       => $acc_txn_amount, 
				  'acc_txn_fcy'       => $acc_txn_fcy_amount,
				  'vch_txn_id'        => (int)$voucher_txn_id,
				  'txn_id'            => (int)$txn_id,
				  'hobo_id'           => ($VchOthrBo==0)?$this->bo_id:$VchOthrBo,
				  'acc_txn_type'      => (int)$acc_txn_type
				];  				
				$this->VouchersModel->add_acc_txn_data($insert_data);
				
				$register_insert_data  = [
				  'acct_vch_type'     => (int)$voucher_type_id,
				  'cmp_id'            => (int)$this->company_id,
				  'vch_txn_id'        => (int)$voucher_txn_id,
				  'txn_id'            => (int)$txn_id,
				  'vch_date'          => $voucher_date,				 
				  'acc_id'            => (int)$value['account_id'],
				  'acc_txn_cr_amt'    => $acc_cr_txn_amount, 
				  'acc_txn_dr_amt'    => $acc_dr_txn_amount, 
				  'vch_narr'          => $value['description'],
				  'hobo_id'           => (int)($VchOthrBo==0) ? $this->bo_id:$VchOthrBo,
				  'acc_txn_type'      => (int)$acc_txn_type
				]; 								
				$this->VouchersModel->add_register_txn_data($register_insert_data);				
				$narration            = $value['description'];
				$this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'short',$narration);
			   }
			   
			   
			   /*
    --------------------------------
    BILL BY BILL (Correct Mapping)
    --------------------------------
    */

		if (!empty($bbbdata)) {

			$filtered_bbb = [];

			foreach ($bbbdata as $bbb) {

				// ✅ MATCH USING row_index (from frontend fix)
				if (isset($bbb['row_index']) && $bbb['row_index'] == $key) {
					$filtered_bbb[] = $bbb;
				}
			}

			if (!empty($filtered_bbb)) {

				$this->VouchersModel->SaveBillByBillData(
					$filtered_bbb,
					$voucher_date,
					$voucher_txn_id,
					$txn_id
				);
			}
		}


		// Cost Centre
		if (!empty($ccdata)) {
			$filtered_cc = [];
			foreach ($ccdata as $cc) {
				if ($cc['row_index'] == $key) {
					$filtered_cc[] = $cc;
				}
			}

			if (!empty($filtered_cc)) {
				$this->VouchersModel->SaveCostCentreData($filtered_cc, $voucher_date, $voucher_txn_id, $txn_id);
			}
		}

		// Subledger
		if (!empty($sblgrdata)) {
			$filtered_sb = [];
			foreach ($sblgrdata as $sb) {
				if ($sb['row_index'] == $key) {
					$filtered_sb[] = $sb;
				}
			}

			if (!empty($filtered_sb)) {
				$this->VouchersModel->SaveSubLedgerData($filtered_sb, $voucher_date, $voucher_txn_id, $txn_id);
			}
		}

		// Project
		if (!empty($prdata)) {
			$filtered_pr = [];
			foreach ($prdata as $pr) {
				if ($pr['row_index'] == $key) {
					$filtered_pr[] = $pr;
				}
			}

			if (!empty($filtered_pr)) {
				$this->VouchersModel->SaveProjectReportingData($filtered_pr, $voucher_date, $voucher_txn_id, $txn_id);
			}
		}

	
			   
            }
			if ($first_account_id && $acc_cr_txn_total_amount > 0) {
			   
				if($Voucher_TxnApproval!='' &&  parseAmount($acc_cr_txn_total_amount) > parseAmount($Voucher_TxnApproval)){
				 $show_approval_message=1;    
				 $acc_txn_type = 4; // voucher is pending for approval
				}
				 else
				 $acc_txn_type = ($oCheck==1)?2:1;
			    
				$long_register_insert = [
					'acct_vch_type'   => (int)$voucher_type_id,
					'cmp_id'          => (int)$this->company_id,
					'vch_txn_id'      => (int)$voucher_txn_id,
					'txn_id'          => NULL,
					'vch_date'        => $voucher_date,					
					'acc_id'          => (int)$first_account_id,
					'acc_txn_cr_amt'  => $acc_cr_txn_total_amount,
					'acc_txn_dr_amt'  => 0,
					'vch_narr'        => $long_narration ?? '',
					'hobo_id'         => (int)($VchOthrBo == 0) ? $this->bo_id : $VchOthrBo,
					'acc_txn_type'    => (int)$acc_txn_type
				];
				$this->VouchersModel->add_register_txn_data($long_register_insert);				
			}
			
          }
	
		$insert_data = [
			"cmp_id"         => (int)$this->company_id,
			"vch_txn_id"     => (int)$voucher_txn_id,
			"vch_series_id"  => (int)$voucher_series,
			"master_id"      => (int)0,
			'master_id_type' => 'nrr'
		  ];
        $txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
        $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_narration);
      
       if($FromSaleVch==1){
		 //$SaleVchTxnId; 
		 $insert_data = [
					'acct_crs_id_type'		=> '',
					'acct_txn_id'           => $SaleVchTxnId,
					'comp_id'				=> $this->company_id,
					'txn_id'				=> 0,
					'voucher_txn_id'		=> $SaleVchTxnId,
					'bo_id'					=> $this->session->get('ses_boid'),
					'acc_cross_ref_type'	=> 'RCPTVCH',
					'acc_cross_ref_data'	=> $voucher_txn_id,
					'acc_cross_logdate'   	=> $voucher_date,
				    ];					
				$this->TransactionModel->add_acc_crsref_data($insert_data); 
	     }
	    if($FromPurchaseVch==1){
		 //$SaleVchTxnId; 
		 $insert_data = [
					'acct_crs_id_type'		=> '',
					'acct_txn_id'           => $PurchaseVchTxnId,
					'comp_id'				=> $this->company_id,
					'txn_id'				=> 0,
					'voucher_txn_id'		=> $PurchaseVchTxnId,
					'bo_id'					=> $this->session->get('ses_boid'),
					'acc_cross_ref_type'	=> 'PYMTVCH',
					'acc_cross_ref_data'	=> $voucher_txn_id,
					'acc_cross_logdate'   	=> $voucher_date,
				    ];					
				$this->TransactionModel->add_acc_crsref_data($insert_data); 
	      } 

	  $this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
	  $this->VouchersModel->UpdateConso($voucher_txn_id,["draft_vch_rec_id"=>0]);
	  /************ Save Voucher Activity Log **********/
	    $voucher_messages = [
			9  => "New payment voucher has been added by [USERNAME]([UUID])",
			13 => "New receipt voucher has been added by [USERNAME]([UUID])",
			1  => "New contra voucher has been added by [USERNAME]([UUID])",
			5  => "New journal voucher has been added by [USERNAME]([UUID])",
			8  => "New memorandum voucher has been added by [USERNAME]([UUID])"
		];
		$notification_approval_messages = [
			9  => "Payment voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])",
			13 => "Receipt voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])",
			1  => "Contra voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])",
			5  => "Journal voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])",
			8  => "Memorandum voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])"
		];
		$notification_rejected_messages = [
			9  => "Payment voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			13 => "Receipt voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			1  => "Contra voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			5  => "Journal voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			8  => "Memorandum voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])"
		];
		$erp_activity_log = $voucher_messages[$voucher_type_id] ?? "Voucher has been added by [USERNAME]([UUID])";
		$this->VouchersModel->SaveUserActivity($erp_activity_log,$voucher_txn_id);
		
		if ($show_approval_message == 1) {			
			$erp_notify_log = $notification_approval_messages[$voucher_type_id] ?? "";
			$this->VouchersModel->SaveNotifications($erp_notify_log,$voucher_txn_id,$Voucher_TxnApproval_UUID,$acc_cr_txn_total_amount);
            
			$aprvdata = array("cmp_id"=> $this->company_id,"vch_txn_id"=>$voucher_txn_id,
		               "uuid_by"=>$this->session->get('uuid'),"uuid_to"=>$Voucher_TxnApproval_UUID,
					   "erp_txn_aprv_status"=>4
					   );
			$this->VouchersModel->SaveTxnApprvLog($aprvdata,$voucher_txn_id,$Voucher_TxnApproval_UUID,4);
            
			
			return [
                'message' => 'Voucher has been saved and is pending approval',
                'data'    => [] // optional: any data you want to return
            ];
        }
		
		return [
            'message' => 'Voucher saved successfully.',
            'data'    => []
        ];
	  
	 });
		 return $this->response->setJSON($trans_result);
		
		
	             
    }

    $data['allbos']                   = $this->VouchersModel->all_mig_bo_lists();
	$data['migrate_voucher_txn_id']   = 0; 
    $data['migrate_account_transactions'] = [];
    $data['migrate_cc_data']          = [];
    $data['migrate_bbb_data']         = [];
    $data['migrate_pr_data']          = [];
	$data['migrate_subledger_data']   = [];
    $data['narration']                = ''; 	
    $data['message_output']           = $this->message_output;
    $data['base_url']                 = $this->base_url; 
    $data['folder_path']              = $this->folder_path; 
    $data['voucher_name']             = $voucher_detail['vch_name'];    
    $data['voucher_type_id']          = $voucher_type_id;      
    $data['voucher_series_dropdown']  = $this->VouchersModel->comp_voucher_series($this->company_id,$voucher_type_id);
   
   if($voucher_type_id == 5 ){
    $data['reverse_voucher_series_dropdown']  = $this->VouchersModel->comp_voucher_series($this->company_id,16);
	$data['voucher_no'] =0;
	$data['reverse_voucher_no'] =0;
  }
   
    $data['bills_method_list']        = ['','New Ref.','Adjustment'];
 	$accounts_list    = GetJsonFileContent($this->fy_id,$this->company_id,'acc');

	$bsd_accounts     = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
	
    if($accounts_list!='' &&  $bsd_accounts!='')
        $company_all_acc_bsd    = array_merge(json_decode($accounts_list,true),json_decode($bsd_accounts,true));
	else
	    $company_all_acc_bsd    = json_decode($accounts_list,true);	
     $data['acc_bsd_json_file']  = json_encode($company_all_acc_bsd);
	
	
    if(isset($_GET['v'])){
      $voucher_info = $this->VouchersModel->get_voucher_cons_info($_GET['v']);
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
	else{
	
		$data['voucher_date']  =$this->VouchersModel->getLastVoucherDate($voucher_type_id);
	}
     $data['currency_list'] = $this->VouchersModel->get_currency_list();
     $data['bo_gstin_type'] = $this->session->get('bo_gstin_type');
     $format = isset($_GET['format']) ? $_GET['format'] : 1;
     $data['data_groups']  =  $this->VouchersModel->ajax_receipt_accounts_list();
	
	 $data['format']       =  $format;
    if($data['voucher_type_id'] == 9){
      if($format == 1)
      return view($this->folder_path.'vouchers/classic_invoice_payment',$data);   
      if($format == 2)
      return view($this->folder_path.'vouchers/invoice',$data);  

    }else if( $data['voucher_type_id'] == 13){
      if($format == 1)
      return view($this->folder_path.'vouchers/classic_invoice_receipt',$data);   
      if($format == 2)
      return view($this->folder_path.'vouchers/invoice',$data);  

    }else{
      return view($this->folder_path.'vouchers/invoice',$data); 
    }
  }

  public function memorandum($voucher_type_id){
	$voucher_type_array = [8];
    if(!in_array($voucher_type_id, $voucher_type_array)){
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
    }
	
    $voucher_detail = $this->VouchersModel->get_vouchertype_info($voucher_type_id);
    if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
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

      $long_narration     = $this->request->getVar('narration');
      $voucherdata        = $this->request->getVar('voucherdata');
      $voucher_data       = json_decode($voucherdata,true);
      $voucher_date       = $this->request->getVar('voucher_date');
      $voucher_series     = $this->request->getVar('voucher_series');	  
	  $btnid              = $this->request->getVar('btnid');// submitbtn default save button	 
      $voucher_date       = validate_date_by_fy($voucher_date);
      $currency_id        = $this->request->getVar('currency_id');
      $fcy_forex_rate     = $this->request->getVar('fcy_forex_rate') ?? 1;      
	  
      
	    $trans_result = $this->runTransaction(function($db) use ($btnid,$long_narration,$voucher_data,$voucherdata,$voucher_type_id, $voucher_series, $voucher_date)
		{	
        /**********  Save data to postgr_db as a draft  **************/
	    $total_debit   = 0;
		$credit_acc_id = 0;
		foreach ($voucher_data as $row) {
			if ($row['drcr'] === 'C') {
				$credit_acc_id = $row['acc_id'];
			}
			if ($row['drcr'] === 'D') {
				$total_debit += $row['debit'];
			}
		}
		$vch_particulars='';
		$draft_vch_rec_id =0;
		if($credit_acc_id){
			$acc_info = $this->AccountsModel->account_info($credit_acc_id);
			$vch_particulars = $acc_info['acc_name']; 
		}
		$isoptional =FALSE;
		$pgrdata_drft    = array("cmp_id"=>$this->company_id,"hobo_id"=>$this->bo_id,
		                         "log_date_time"=>$voucher_date,"vch_type_id"=>$voucher_type_id,
					             "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($total_debit),
								 "vch_particulars"=>$vch_particulars,"uuid_aictly"=>$this->session->get('uuid'),
								 "long_narr"=>$long_narration,"vch_series_id"=>$voucher_series,"isoptional"=>$isoptional
								 
					            ); 
		$draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);			  
	  
	   /********************************************************************/
	   if($btnid=='submitbtn_drft'){
		   return json_encode(['status' => true, 'message' => 'Voucher saved in draft mode']); 
	   } 		
		$insert_data  = array(
          "cmp_id"           => $this->company_id,		  
          "vch_series_id"    => $voucher_series,          
          "vch_type_id"      => $voucher_type_id,
		  "vch_sub_type_id"  => 0,		   
          "vch_date"         => $voucher_date,
          "mat_cent_id"      => 0,
		  "draft_vch_rec_id" => $draft_vch_rec_id
         );		
         $voucher_txn_id = $this->VouchersModel->add_voucher_cons_data($insert_data,0);
			
        if($voucher_data)
         {
		    $acc_cr_txn_total_amount =0;
			$acc_dr_txn_total_amount =0;
			$first_account_id = NULL;
			foreach($voucher_data as $key => $value)
			{
			  if($value['acc_type'] == 'acc')
			  {
				if ($first_account_id == NULL && $value['drcr'] == 'D' && $voucher_type_id==8 ) {
					$first_account_id = $value['acc_id']; 
				}
				
		
				$account_info       = $this->AccountsModel->account_info($value['acc_id']);  
				$acc_grp_id = 0;
				if($account_info){
					$acc_grp_id = $account_info['under_crs_mst_id'];
				}
				$insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $value['acc_id'],
				  'master_id_type'  => 'acc'
				];
				
				$acc_txn_amount      = 0;
				$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
				
				if($value['drcr'] == 'C'){
				  $acc_cr_txn_amount = parseAmount($value["credit"]);
				  $acc_txn_amount    = parseAmount($value["credit"]); 
				  $acc_txn_drcr      = 2;
				  $acc_dr_txn_amount = 0;				  
				  $acc_cr_txn_total_amount +=$acc_cr_txn_amount;
				}
				else{
				  $acc_dr_txn_amount = parseAmount($value["debit"]);
				  $acc_txn_amount    = parseAmount($value["debit"]); 
				  $acc_txn_drcr      = 1;
				  $acc_cr_txn_amount = 0;				  
				  $acc_dr_txn_total_amount +=$acc_dr_txn_amount;				 				  
				}
				
				$insert_data  = [
				  'cmp_id'           => $this->company_id,
				  'acc_id'           => $value['acc_id'],
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => $acc_txn_drcr,
				  'acc_txn_amt'       => $acc_txn_amount, 
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $txn_id,
				  'hobo_id'           => $this->bo_id,
				  'acc_txn_type'      => 3
				];  				
				$this->VouchersModel->add_acc_txn_data($insert_data);
				
				$register_insert_data  = [
				  'acct_vch_type'     => $voucher_type_id,
				  'cmp_id'            => $this->company_id,
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $txn_id,
				  'vch_date'          => $voucher_date,				 
				  'acc_id'            => $value['acc_id'],
				  'acc_txn_cr_amt'    => $acc_cr_txn_amount, 
				  'acc_txn_dr_amt'    => $acc_dr_txn_amount, 
				  'vch_narr'          => $value['description'],
				  'hobo_id'           => $this->bo_id,
				  'acc_txn_type'      => 3 
				]; 								
				$this->VouchersModel->add_register_txn_data($register_insert_data);				
				$narration            = $value['description'];
				$this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'short',$narration);
			   }
            }
			if ($first_account_id && $acc_cr_txn_total_amount > 0) {
				$long_register_insert = [
					'acct_vch_type'   => $voucher_type_id,
					'cmp_id'          => $this->company_id,
					'vch_txn_id'      => $voucher_txn_id,
					'txn_id'          => NULL,
					'vch_date'        => $voucher_date,					
					'acc_id'          => $first_account_id,
					'acc_txn_cr_amt'  => $acc_cr_txn_total_amount,
					'acc_txn_dr_amt'  => 0,
					'vch_narr'        => $long_narration ?? '',
					'hobo_id'         => $this->bo_id,
					'acc_txn_type'    => 3
				];
				$this->VouchersModel->add_register_txn_data($long_register_insert);				
			}
			
          }
			
		$insert_data = [
			"cmp_id"         => $this->company_id,
			"vch_txn_id"     => $voucher_txn_id,
			"vch_series_id"  => $voucher_series,
			"master_id"      => 0,
			'master_id_type' => 'nrr'
		  ];
        $txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
        $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_narration);
        $this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
	    $this->VouchersModel->UpdateConso($voucher_txn_id,["draft_vch_rec_id"=>0]);
	  });
      return $this->response->setJSON($trans_result);
    }
	$data['narration']                = '';
    $data['message_output']           = $this->message_output;
    $data['base_url']                 = $this->base_url; 
    $data['folder_path']              = $this->folder_path; 
    $data['voucher_name']             = $voucher_detail['vch_name'];    
    $data['voucher_type_id']          = $voucher_type_id;      
    $data['voucher_series_dropdown']  = $this->VouchersModel->comp_voucher_series($this->company_id,$voucher_type_id);
    $accounts_list                    = GetJsonFileContent($this->fy_id,$this->company_id,'acc');	
    $company_all_acc_bsd              = json_decode($accounts_list,true);	
    $data['accounts_json_file']       = json_encode($company_all_acc_bsd);	
    $data['currency_list']            = $this->VouchersModel->get_currency_list();
    $data['bo_gstin_type']            = $this->session->get('bo_gstin_type');
	$data['voucher_date']             = $this->VouchersModel->getLastVoucherDate($voucher_type_id);
    return view($this->folder_path.'memorandum/invoice',$data); 
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

  public function receipt_pdf($voucher_txn_id){
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

  public function getIndianCurrency(float $number){
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

  public function withitem(){
    $voucher_type_id          = 5;
    $vch_subtype_id           = 23;
	$taxes_list               = $this->VouchersModel->GetGSTTaxesList();
	$voucher_detail           = $this->VouchersModel->get_vouchertype_info($voucher_type_id);
	$Voucher_TxnApproval_Info = $this->VouchersModel->Voucher_TxnApproval();
	$Voucher_TxnApproval      = $Voucher_TxnApproval_Info['approval_amt'];
	$Voucher_TxnApproval_UUID = $Voucher_TxnApproval_Info['uuid_to']; // who will approve, reject voucher
	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
	  
	  if($_SERVER['REMOTE_ADDR']=='103.172.223.179' || $_SERVER['REMOTE_ADDR']=='103.172.223.178'){
		 // echo '<pre>';print_r($_POST);die();
	  }
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
    $trans_result = $this->runTransaction(function($db) use ($vch_subtype_id,$taxes_list,$voucher_type_id){		 
     try{
	  $voucher_date         = $this->request->getVar('voucher_date');
      $voucher_date         = validate_date_by_fy($voucher_date);
      $voucher_series       = $this->request->getVar('voucher_series');       
      $matrcntr_id          = $this->request->getVar('matrcntr_id');   
      $long_narration       = $this->request->getVar('narration');
      $currency_id          = $this->request->getVar('currency_id');
	  $btnid                = $this->request->getVar('btnid');// submitbtn default save button
      $fcy_forex_rate       = 0;
	  $in_batchdata = [];
	   if(!empty($this->request->getVar('inbatchdata')))
	  $in_batchdata = json_decode($this->request->getVar('inbatchdata'),true);
	  
	   $out_batchdata = [];
	   if(!empty($this->request->getVar('outbatchdata')))
	  $out_batchdata = json_decode($this->request->getVar('outbatchdata'),true);
  
	  $item_data_from       = [];
      if(!empty($this->request->getVar('item_data_from')))
        $item_data_from = json_decode($this->request->getVar('item_data_from'),true);

      $item_data_to     = [];
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
	 
      $prdata = [];
      if(!empty($this->request->getVar('prdata')))
        $prdata = json_decode($this->request->getVar('prdata'),true);
	
	 $is_batch=FALSE;
	 if($in_batchdata)
     $is_batch=TRUE; 
 
 
     $is_bbb=FALSE;
	 if($bbbdata)
     $is_bbb=TRUE;			
	 
	 $is_pr=FALSE;
	  if($prdata)
       $is_pr=TRUE;
   
     $is_cc=FALSE;
	  if($ccdata)
       $is_cc=TRUE;
      $vch_particulars = '';		  
	  
	  // Mark Dirty Transactions
		    $voucherDate = new \DateTime($voucher_date);
			$today       = new \DateTime('today');
           	 if($voucherDate < $today){ 
				 // Call your dirty recalculation function
				// $this->VouchersModel->markSnapshotDirty($voucher_date);
			 }
			 
      $voucherdata     = json_encode(["in_batchdata"=>$in_batchdata,"out_batchdata"=>$out_batchdata,"is_batch"=>$is_batch,"mat_cent_id"=>$matrcntr_id,"item_data_from"=>$item_data_from,"item_data_to"=>$item_data_to,
	                                  "acc_data_from"=>$acc_data_from,"acc_data_to"=>$acc_data_to]);
	  $pgrdata_drft    = array("cmp_id"=>$this->company_id,"hobo_id"=>$this->bo_id,
		                       "log_date_time"=>$voucher_date,"vch_type_id"=>$voucher_type_id,
					           "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount(0),"vch_fcy_amt"=>parseAmount(0),"vch_fcy_rate"=>parseAmount(0),
							   "vch_particulars"=>"N/A","uuid_aictly"=>$this->session->get('uuid'),
							   "long_narr"=>$long_narration,"vch_series_id"=>(int)$voucher_series,"isoptional"=>FALSE,
							   "vch_fcy_id"=>(int)$currency_id,"is_cc"=>(int)$is_cc,"is_bbb"=>(int)$is_bbb,"is_pr"=>(int)$is_pr,"is_sblgr"=>0
					            );
	    $draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);
	    if($btnid=='submitbtn_drft'){
		  return ['status' => true, 'message' => 'Voucher saved in draft mode']; 
	     }
		$insert_data  = array(
			  "cmp_id"           => $this->company_id,		  
			  "vch_series_id"    => $voucher_series,          
			  "vch_type_id"      => $voucher_type_id,
			  "vch_sub_type_id"  => $vch_subtype_id,		   
			  "vch_date"         => $voucher_date,
			  "mat_cent_id"      => $matrcntr_id,
			  "draft_vch_rec_id" => $draft_vch_rec_id
			 );		
        $voucher_txn_id = $this->VouchersModel->add_voucher_cons_data($insert_data);
		if($fcy_forex_rate >0){
		    $fcy_data = array("cmp_id"=>$this->company_id,"vch_txn_id"=>$voucher_txn_id,
			                   "vch_fcy_rate"=>$fcy_forex_rate,"cmp_fcy_mst_id"=>$currency_id
							  );	 
		    $this->VouchersModel->save_voucher_fcyrate($fcy_data);	
		}
	   $itm_txn_type = 1;	//1 for Regular 	
       if($item_data_from){			
			$item_account_array = [];
        foreach($item_data_from as $item_row){
          $txn_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $item_row['item_id'],
				  'master_id_type'  => 'itm'
				 ];
          $main_txn_id  = $this->VouchersModel->add_comp_txn_data($txn_data);
          if(isset($item_row['item_qty']) && $item_row['item_qty'] >0){
			$insert_data   = array(
				'cmp_id'             => $this->company_id,
				'itm_id_unit_id'     => $item_row['item_id'].'_'.$item_row['item_unit_id'],
				'itm_txn_qty'        => $item_row['item_qty'],
				'itm_txn_date'       => $voucher_date,
				'itm_txn_dr_cr'      => 1,
				'itm_txn_rate'       => parseAmountPrice($item_row['item_price'],4),
				'itm_txn_amt'        => parseAmount($item_row['item_total_amount']),
				'itm_txn_fcy'        => 0,
				'vch_txn_id'         => $voucher_txn_id,
				'txn_id'             => $main_txn_id,
				'mat_cent_id'        => $matrcntr_id,
				'hobo_id'            => $this->bo_id,
				'itm_txn_type'		 => $itm_txn_type		
			    );	
			 $this->VouchersModel->add_itm_txn_data($insert_data); 
			 $register_insert_data  = [
                				  'itm_vch_type'     => $voucher_type_id,
                				  'cmp_id'            => $this->company_id,
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $main_txn_id,
                				  'vch_date'          => $voucher_date,				 
                				  'itm_id_unit_id'    => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                				  'vch_narr'          => $long_narration ?? '',
                				  'hobo_id'           => $this->bo_id,
                				  'itm_txn_type'      => $itm_txn_type
                				]; 								
            $itm_vch_reg_id =  $this->VouchersModel->add_item_register_txn_data($register_insert_data);	
			/************  Insert Register Data In Sub Table Also ************/
			$register_sub_insert_data  = [
                				  'itm_vch_reg_id'    => $itm_vch_reg_id,
								  'mat_cent_id'       => $matrcntr_id,
                				  'itm_txn_dr_qty'    => $item_row['item_qty'],
                				  'itm_txn_dr_rate'   => parseAmountPrice($item_row['item_price'],4),
                				  'itm_txn_dr_amt'    => parseAmount($item_row['item_total_amount']),
                				  'itm_txn_cr_qty'    => 0,				 
                				  'itm_txn_cr_rate'   => 0,
                				  'itm_txn_cr_amt'    => 0,
                				  'vch_narr'          => $long_narration ?? '',              				 
                				]; 								
            $this->VouchersModel->add_item_sub_register_txn_data($register_sub_insert_data);	
            
			$account_id  =  $item_row['item_pur_acc'];
            if(isset($item_account_array[$account_id]))
            $item_account_array[$account_id] += parseAmount($item_row['item_total_amount']);
           else
            $item_account_array[$account_id] = parseAmount($item_row['item_total_amount']);
			
			}
			
			/***********   Save Item Batch  Data  *************/
			if(count($in_batchdata)){		
			   $this->VouchersModel->SaveBatchData($in_batchdata,$voucher_date,$voucher_txn_id,$main_txn_id,$matrcntr_id);
			}
		  }
		   $acc_from_txn_id = []; // ✅ ADD THIS LINE
		   $acc_to_txn_id = []; // ✅ ADD THIS LINE
		 foreach ($item_account_array as $account_id => $amount) {	
			$insert_data = [
							  "cmp_id"          => $this->company_id,
							  "vch_series_id"   => $voucher_series,
							  "vch_txn_id"      => $voucher_txn_id,
							  "master_id"       => $account_id,
							  'master_id_type'  => 'acc'
							 ];
			$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
			// ✅ ADD THIS LINE:
            $acc_from_txn_id[$account_id] = $txn_id; // Map account to its txn_id
			$acc_txn_data  = [
							  'cmp_id'            => $this->company_id,
							  'acc_id'            => $account_id,
							  'acc_txn_date'      => $voucher_date,
							  'acc_txn_dr_cr'     => 1,
							  'acc_txn_amt'       => $amount, 
							  'acc_txn_fcy'       => 0,
							  'vch_txn_id'        => $voucher_txn_id,
							  'txn_id'            => $txn_id,
							  'hobo_id'           => $this->bo_id,
							  'acc_txn_type'      => 1
							];  				
			$this->VouchersModel->add_acc_txn_data($acc_txn_data);	
		 }
		  
	   }

      if($item_data_to){
	    $item_account_array = [];
        foreach($item_data_to as $item_row){	
		 $txn_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $item_row['item_id'],
				  'master_id_type'  => 'itm'
				 ];
          $txn_id  = $this->VouchersModel->add_comp_txn_data($txn_data);
          if(isset($item_row['item_qty']))
             $item_qty = $item_row['item_qty'];
           else
             $item_qty =  0;    
		 
		   $insert_data   = array(
				'cmp_id'             => $this->company_id,
				'itm_id_unit_id'     => $item_row['item_id'].'_'.$item_row['item_unit_id'],
				'itm_txn_qty'        => $item_row['item_qty'],
				'itm_txn_date'       => $voucher_date,
				'itm_txn_dr_cr'      => 2,
				'itm_txn_rate'       => parseAmountPrice($item_row['item_price'],4),
				'itm_txn_amt'        => parseAmount($item_row['item_total_amount']),
				'itm_txn_fcy'        => 0,
				'vch_txn_id'         => $voucher_txn_id,
				'txn_id'             => $main_txn_id,
				'mat_cent_id'        => $matrcntr_id,
				'hobo_id'            => $this->bo_id,
				'itm_txn_type'		 => $itm_txn_type		
			    );	
			$this->VouchersModel->add_itm_txn_data($insert_data);
			
			$register_insert_data  = [
                				  'itm_vch_type'     => $voucher_type_id,
                				  'cmp_id'            => $this->company_id,
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $main_txn_id,
                				  'vch_date'          => $voucher_date,				 
                				  'itm_id_unit_id'    => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                				  'vch_narr'          => $long_narration ?? '',
                				  'hobo_id'           => $this->bo_id,
                				  'itm_txn_type'      => $itm_txn_type
                				]; 								
            $itm_vch_reg_id =  $this->VouchersModel->add_item_register_txn_data($register_insert_data);	
			/************  Insert Register Data In Sub Table Also ************/
			$register_sub_insert_data  = [
                				  'itm_vch_reg_id'    => $itm_vch_reg_id,
								  'mat_cent_id'       => $matrcntr_id,
                				  'itm_txn_dr_qty'    => 0,
                				  'itm_txn_dr_rate'   => 0,
                				  'itm_txn_dr_amt'    => 0,
                				  'itm_txn_cr_qty'    => $item_row['item_qty'],				 
                				  'itm_txn_cr_rate'   => parseAmountPrice($item_row['item_price'],4),
                				  'itm_txn_cr_amt'    => parseAmount($item_row['item_total_amount']),
                				  'vch_narr'          => $long_narration ?? '',              				 
                				]; 								
            $this->VouchersModel->add_item_sub_register_txn_data($register_sub_insert_data);
			$account_id  =  $item_row['item_sales_acc'];

			 if(isset($item_account_array[$account_id]))
			  $item_account_array[$account_id] += parseAmount($item_row['item_total_amount']);
			else
			  $item_account_array[$account_id] = parseAmount($item_row['item_total_amount']);

			/***********   Save Item Batch  Data  *************/
		 if(count($out_batchdata)){		
		  $this->VouchersModel->SaveBatchData($out_batchdata,$voucher_date,$voucher_txn_id,$main_txn_id,$matrcntr_id);
		 }
		}
		foreach ($item_account_array as $account_id => $amount) {
				$insert_data = [
							  "cmp_id"          => $this->company_id,
							  "vch_series_id"   => $voucher_series,
							  "vch_txn_id"      => $voucher_txn_id,
							  "master_id"       => $account_id,
							  'master_id_type'  => 'acc'
							 ];
				$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);	
				 $acc_to_txn_id[$account_id] = $txn_id; // Map account to its txn_id
				$acc_txn_data  = [
				  'cmp_id'            => $this->company_id,
				  'acc_id'            => $account_id,
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => 2,
				  'acc_txn_amt'       => $amount, 
				  'acc_txn_fcy'       => 0,
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $txn_id,
				  'hobo_id'           => $this->bo_id,
				  'acc_txn_type'      => 1
				];  				
				$this->VouchersModel->add_acc_txn_data($acc_txn_data);	 
		   }
	  }
	    // Process 'from' accounts (debit)
		$this->process_acc_data($acc_data_from, 1,$voucher_series,$voucher_txn_id,$voucher_date); 
		// Process 'to' accounts (credit)
		$this->process_acc_data($acc_data_to, 2 , $voucher_series,$voucher_txn_id,$voucher_date);		
		/******************* Start of Save Voucher Naration  ****************/
			 $nr_insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => 0,
				  'master_id_type'  => 'nrr'
				 ];
			 $txn_id = $this->VouchersModel->add_comp_txn_data($nr_insert_data);
			 $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_narration);
			/******************* End of Save Voucher Naration  ****************/
       
	    /***********   Save Bill By Bill Data  *************/
		if(count($bbbdata)){		
			$filtered_bbb = [];
			foreach ($bbbdata as $bbb) {
				$acc_id = $bbb['account_id'];
				if(isset($acc_to_txn_id[$acc_id])) {
					$filtered_bbb[] = array_merge($filtered_bbb, [
						$bbb,
						'row_index' => null,
						'txn_id'    => $acc_to_txn_id[$acc_id]
					]);
				}
			}
			
			if (!empty($filtered_bbb)) {
				foreach ($filtered_bbb as $bbb_row) {
					$this->VouchersModel->SaveBillByBillData([$bbb_row],$voucher_date,$voucher_txn_id,$bbb_row['txn_id']);
				}
			}
		}

		/***********   Save Cost Center  Data  *************/
		if(count($ccdata)){		
			$filtered_cc = [];
			foreach ($ccdata as $cc) {
				$acc_id = $cc['acc_id'];
				if(isset($acc_to_txn_id[$acc_id])) {
					$filtered_cc[] = array_merge($filtered_cc, [
						$cc,
						'row_index' => null,
						'txn_id'    => $acc_to_txn_id[$acc_id]
					]);
				}
			}
			if (!empty($filtered_cc)) {
				foreach ($filtered_cc as $cc_row) {
					$this->VouchersModel->SaveCostCentreData([$cc_row],$voucher_date,$voucher_txn_id,$cc_row['txn_id']);
				}
			}
		}
		/***********   Save Project Reporting  Data  *************/
	if(count($prdata)){		
		$filtered_pr = [];
		foreach ($prdata as $pr) {
			$acc_id = $pr['acc_id'];
			if(isset($acc_to_txn_id[$acc_id])) {
				$filtered_pr[] = array_merge($filtered_pr, [
					$pr,
					'row_index' => null,
					'txn_id'    => $acc_to_txn_id[$acc_id]
				]);
			}
		}
		if (!empty($filtered_pr)) {
			foreach ($filtered_pr as $pr_row) {
				$this->VouchersModel->SaveProjectReportingData([$pr_row],$voucher_date,$voucher_txn_id,$pr_row['txn_id']);
			}
		}
		}
		
	  $this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
	  $this->VouchersModel->UpdateConso($voucher_txn_id,["draft_vch_rec_id"=>0]);	
	  
      return ['status' => true, 'message' => 'Voucher Inserted'];
       }
	  catch (\Throwable $e) {
				return  ['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()];
				// Get line, file and stack trace
                log_message('error', 'DB Error: ' . $e->getMessage());
                log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());
                log_message('error', 'Trace: ' . $e->getTraceAsString());
    	}
	});
	if(isset($trans_result['result']['status']) && $trans_result['result']['status']=='')
          	return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message']]);
       else
         return $this->response->setJSON($trans_result); 
    }

    $data['message_output']           = $this->message_output;
	$data['taxes_list']               = $taxes_list;
    $data['base_url']                 = $this->base_url; 
    $data['folder_path']              = $this->folder_path; 
    $data['voucher_name']             = $voucher_detail['vch_name']; 
    $data['voucher_date']             = '';
    $data['voucher_type_id']          = $voucher_type_id;      
    $data['voucher_no']               = $this->VouchersModel->get_voucher_no($voucher_type_id);
    $data['voucher_series_dropdown']  = $this->VouchersModel->comp_voucher_series($this->company_id,$voucher_type_id);
    $data['bills_method_list']        = ['','New Ref.','Adjustment'];
    $data['units_list']               = $this->VouchersModel->units_grid();
    $data['matrcntr_dropdown']        = $this->VouchersModel->material_centre_dropdown();	
	$accounts_list                    = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
	$bsd_accounts                     = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
	$items_list                       = GetJsonFileContent($this->fy_id,$this->company_id,'itm');
	$data['item_json_file']           = $items_list;
    $company_all_acc_bsd              = array_merge(json_decode($accounts_list,true),json_decode($bsd_accounts,true));
    $data['acc_bsd_json_file']        = json_encode($company_all_acc_bsd);
    $data['currency_list']            = $this->VouchersModel->get_currency_list();

    return view($this->folder_path.'vouchers/item',$data);
  }
  
  function process_acc_data($acc_data, $dr_cr_flag,$voucher_series,$voucher_txn_id,$voucher_date) {
    if (!$acc_data) return;

    foreach ($acc_data as $value) {
		if($value['acc_id'] >0){
        $master_id_type = $value['acc_type'];  // either 'acc' or 'bsd'

        $insert_data = [
            "cmp_id"          => $this->company_id,
            "vch_series_id"   => $voucher_series,
            "vch_txn_id"      => $voucher_txn_id,
            "master_id"       => $value['acc_id'],
            'master_id_type'  => $master_id_type
        ];
        
        $txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
         // ✅ ADD THIS LINE:
		 if($dr_cr_flag==1)
            $acc_from_txn_id[$value['acc_id']] = $txn_id; // Map account to its txn_id
		if($dr_cr_flag==2)
            $acc_to_txn_id[$value['acc_id']] = $txn_id; // Map account to its txn_id
		
        $acc_txn_data = [
            'cmp_id'            => $this->company_id,
            'acc_id'            => $value['acc_id'],
            'acc_txn_date'      => $voucher_date,
            'acc_txn_dr_cr'     => $dr_cr_flag,  // 1 for FROM, 2 for TO
            'acc_txn_amt'       => $value['acc_amount'],
            'acc_txn_fcy'       => 0,
            'vch_txn_id'        => $voucher_txn_id,
            'txn_id'            => $txn_id,
            'hobo_id'           => $this->bo_id,
            'acc_txn_type'      => 1
        ];

        $this->VouchersModel->add_acc_txn_data($acc_txn_data);
		}
      }
   }

  public function edit($vchtype_id,$voucher_txn_id,$acc_id = 0){   
    $uuid               = $this->session->get('uuid');
	$taxes_list         = $this->VouchersModel->GetGSTTaxesList();
	$voucher_type_array = [1,5,9,13];	
    $voucher_info       = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
    $voucher_detail     = $this->VouchersModel->get_vouchertype_info($vchtype_id);
	
	if(empty($voucher_info)){
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }	 	
	$Voucher_TxnApproval_Info = $this->VouchersModel->Voucher_TxnApproval();
	
	
	$Voucher_TxnApproval = $Voucher_TxnApproval_Info['approval_amt'] ?? '';
	$Voucher_TxnApproval_UUID = $Voucher_TxnApproval_Info['uuid_to'] ?? 0; // who will approve, reject voucher
	
	if($acc_id=='notifread'){	    
	    // User read the notification
	    $this->VouchersModel->UpdateNotificationStatus($voucher_txn_id);	    
	}
	
    $voucher_tag              = '';
	$show_approval_message    = 0;
    $voucher_type_id          = $voucher_info['vch_type_id'];  
    $vch_subtype_id           = $voucher_info['vch_sub_type_id'];
    $voucher_ed_date          = $voucher_info['vch_date'];			
	$data['vch_subtype_id']   = $vch_subtype_id;
	$data['voucher_type_id']  = $voucher_type_id;
	$data['draft_vch_rec_id'] = 0;
	$data['party_dropdown']   = $this->VouchersModel->party_dropdown();    
	$data['allbos']           = $this->VouchersModel->all_mig_bo_lists();
	
	/**************  Duplicate Voucher Code ***************/
	if(isset($_GET['duplc']) && $_GET['duplc']=='1'){
				$data['duplc'] 		     = 1;
				$page_label              = 'Duplicate';		
				
	 }else{
				 $data['duplc'] 	 = 0;
                 $page_label         = 'Update';  					     			  
	}
			
	$data['page_label'] = $page_label;	
	/**************  Duplicate Voucher Code ***************/
  // if($_SERVER['REMOTE_ADDR']=='103.172.223.179')
     // echo '<pre>';print_r($voucher_info);die();
   
   
   
    if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
      //  if($_SERVER['REMOTE_ADDR']=='103.172.223.178' || $_SERVER['REMOTE_ADDR']=='103.172.223.179')
	  // echo "<pre>";print_r($_POST);exit; 	
	  
	   $FromSaleVch        = $this->request->getVar('FromSaleVch');// Auto Recipt Voucher From Sale Voucher
	   $SaleVchTxnId       = $this->request->getVar('SaleVchTxnId');
	   $paymentvia_txn     = $this->request->getVar('paymentvia_txn'); // in classic mode 
	   if(!$FromSaleVch)
		  $FromSaleVch =0;	  
	   $FromPurchaseVch    = $this->request->getVar('FromPurchaseVch');// Auto Recipt Voucher From Sale Voucher
	   $PurchaseVchTxnId   = $this->request->getVar('PurchaseVchTxnId');
	   $btnid              = $this->request->getVar('btnid');// submitbtn default save button
	   $draft_vch_rec_id   = $this->request->getVar('draft_vch_rec_id');
	   $oCheck             = $this->request->getVar('oCheck'); // OPTIONAL VOUCHER TAG
	  
	  
	  $ccdata = [];
      if(!empty($this->request->getVar('ccdata')))
        $ccdata = json_decode($this->request->getVar('ccdata'),true);

      $bbbdata = [];
      if(!empty($this->request->getVar('bbbdata')))
        $bbbdata = json_decode($this->request->getVar('bbbdata'),true);

      $prdata = [];
      if(!empty($this->request->getVar('prdata')))
        $prdata = json_decode($this->request->getVar('prdata'),true);
	   
	  $sblgrdata = [];
      if(!empty($this->request->getVar('sblgr_data')))
        $sblgrdata = json_decode($this->request->getVar('sblgr_data'),true);
	  
	  if(!$FromPurchaseVch)
		  $FromPurchaseVch =0;
	  
	  $is_cc=FALSE;
		if($ccdata)
           $is_cc=TRUE;			
	   
	   $is_bbb=FALSE;
		if($bbbdata)
           $is_bbb=TRUE;			
	   
	   $is_pr=FALSE;
		if($prdata)
           $is_pr=TRUE;
	   
	   $is_sblgr=FALSE;
		if($sblgrdata)
           $is_sblgr=TRUE;
	  
    if(in_array($voucher_type_id, $voucher_type_array) && $vch_subtype_id == 0){
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
		$isdplc_vch =  $this->request->getVar('isdplc_vch');
		if($isdplc_vch==1){	
			if($vch_subtype_id == 0)	
                echo $this->invoice($voucher_type_id);			
			else 
			   echo $this->item();
			die();
		}
		$long_narration     = $this->request->getVar('narration');
        $voucherdata        = $this->request->getVar('voucherdata');
        $voucher_data       = json_decode($voucherdata,true);
        $voucher_date       = $this->request->getVar('voucher_date');
        $voucher_series     = $this->request->getVar('voucher_series');
        $currency_id        = $this->request->getVar('currency_id');
        $fcy_forex_rate     = $this->request->getVar('fcy_forex_rate');       
	    $voucher_date       = validate_date_by_fy($voucher_date);
	  
	  /**********  Save data to postgr_db as a draft  **************/
	   $isoptional =FALSE;
		if($oCheck==1)
			$isoptional =TRUE;
	    $total_debit   = 0;
		$total_fcy_debit   = 0;
		$credit_acc_id = 0;
		foreach ($voucher_data as $row) {
			if ($row['drcr'] === 'C') {
				$credit_acc_id = $row['account_id'];
			}
			if ($row['drcr'] === 'D') {
				$total_debit += $row['debit'];
				if(isset($row['debitfc']) && $row['debitfc']!='')
				$total_fcy_debit += $row['debitfc'];
			}
		}
		$vch_particulars='';		
		if($credit_acc_id){
			$acc_info = $this->AccountsModel->account_info($credit_acc_id);
			$vch_particulars = $acc_info['acc_name']; 
		}
		
		if($draft_vch_rec_id >0){
	     $pgrdata_drft    = array("log_date_time"=>$voucher_date,
					             "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($total_debit),"vch_fcy_amt"=>parseAmount($total_fcy_debit),"vch_fcy_rate"=>(float)$fcy_forex_rate,
								 "vch_particulars"=>$vch_particulars,"long_narr"=>$long_narration,
								 "vch_series_id"=>$voucher_series,"isoptional"=>$isoptional,"vch_fcy_id"=>(int)$currency_id,
								 "is_cc"=>(int)$is_cc,"is_bbb"=>(int)$is_bbb,"is_pr"=>(int)$is_pr,"is_sblgr"=>(int)$is_sblgr
					            ); 
		 $this->VouchersModel->UpdateDrftVoucher($draft_vch_rec_id,$pgrdata_drft);
		}else{		
		 $pgrdata_drft    = array("cmp_id"=>$this->company_id,"hobo_id"=>$this->bo_id,"long_narr"=>$long_narration,"vch_series_id"=>$voucher_series,
		                         "log_date_time"=>$voucher_date,"vch_type_id"=>$voucher_type_id,
					             "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($total_debit),"vch_fcy_amt"=>parseAmount($total_fcy_debit),"vch_fcy_rate"=>(float)$fcy_forex_rate,
								 "vch_particulars"=>$vch_particulars,"uuid_aictly"=>$this->session->get('uuid'),
								 "isoptional"=>$isoptional,"vch_fcy_id"=>(int)$currency_id,"is_cc"=>(int)$is_cc,"is_bbb"=>(int)$is_bbb,"is_pr"=>(int)$is_pr,"is_sblgr"=>(int)$is_sblgr
					            ); 
		 $draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);
		}
		
        /********************************************************************/
		if($btnid=='submitbtn_drft'){
		   return json_encode(['status' => true, 'message' => 'Voucher saved in draft mode']); 
	    }
	   
	  $trans_result = $this->runTransaction(function($db) use ($Voucher_TxnApproval_UUID,$Voucher_TxnApproval,$show_approval_message,$btnid,$sblgrdata,$bbbdata,$prdata,$ccdata,$currency_id,$fcy_forex_rate,$oCheck,$draft_vch_rec_id,$voucher_data,$voucher_txn_id,$long_narration, $voucher_type_id, $voucher_series, $voucher_date,$FromPurchaseVch,$FromSaleVch,$SaleVchTxnId,$PurchaseVchTxnId)
		{
	   /* Remove Old Entries using vch_txn_id */
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cmptxnmstn');	
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'accttxnmst');
       $this->VouchersModel->clear_table_narrations_data($voucher_txn_id);
	   $this->VouchersModel->clear_register_txn_data($voucher_txn_id,$voucher_type_id);
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'billtxnmst');
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cctxnmstnn');
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'prjtxnmstn');
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'subacctxnm');
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchfcyrate');
	   
	   
	   /******************     ***********/
		$voucher_tag  = '';
        
        $update_data  = array(
          "vch_series_id"   => $voucher_series,
          "vch_date"         => $voucher_date,
          "draft_vch_rec_id" => $draft_vch_rec_id		 
        );
        $this->VouchersModel->update_voucher_cons_data($update_data,$voucher_txn_id,$this->company_id);
         
		if($fcy_forex_rate >0){
			$fcy_data = array("cmp_id"=>$this->company_id,"vch_txn_id"=>$voucher_txn_id,
			                   "vch_fcy_rate"=>$fcy_forex_rate,"cmp_fcy_mst_id"=>$currency_id
							  );	
		    $this->VouchersModel->save_voucher_fcyrate($fcy_data);	
		    } else{				
			$this->VouchersModel->clear_voucher_fcyrate($voucher_txn_id);
			} 
			
        if($voucher_data){		
		    $acc_cr_txn_total_amount =0;
			$acc_dr_txn_total_amount =0;
			$first_account_id = NULL;
			
			foreach($voucher_data as $key => $value)
			{
			  if($value['acc_type'] == 'acc' || $value['acc_type'] == 'bsd')
			  {
				 if ($first_account_id == NULL && $value['drcr'] == 'D' && $voucher_type_id==9 ) {
					$first_account_id = $value['account_id']; 
				}
				if ($first_account_id == NULL && $value['drcr'] == 'C' && $voucher_type_id==13 ) {
					$first_account_id = $value['account_id']; 
				}
				if ($first_account_id == NULL && $value['drcr'] == 'C' && ($voucher_type_id==1 || $voucher_type_id==5) ) {
					$first_account_id = $value['account_id']; 
				}
		
				$account_info       = $this->AccountsModel->account_info($value['account_id'],$value['acc_type']); 
				
				$acc_grp_id         = 0;
				if($account_info){
					$acc_grp_id    = $account_info['under_crs_mst_id'];
					$acc_parent_id = $account_info['crs_mst_parent_id'];
					if($acc_grp_id==0)
						$under_main_grp_id = 0;
				    else{
						$group_info    = $this->AccountsModel->main_group_info($acc_grp_id);
						if($group_info)
							$under_main_grp_id = $group_info['under_acc_gp_id'] ?? 0;
						}				     
				}
				$insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $value['account_id'],
				  'master_id_type'  => 'acc'
				];
				
				$acc_txn_amount      = 0;
				$acc_txn_fcy_amount  = 0;
				$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);			
				if($value['drcr'] == 'C'){
				  $acc_cr_txn_amount  = parseAmount($value["credit"]);
				  $acc_txn_amount     = parseAmount($value["credit"]); 
				  $acc_txn_fcy_amount = parseAmount($value["creditfc"]); 
				  $acc_txn_drcr       = 2;
				  $acc_dr_txn_amount  = 0;				  
				  $acc_cr_txn_total_amount +=$acc_cr_txn_amount;				  
				}
				else{
				  $acc_dr_txn_amount  = parseAmount($value["debit"]);
				  $acc_txn_amount     = parseAmount($value["debit"]); 
				  $acc_txn_fcy_amount = parseAmount($value["debitfc"]); 
				  $acc_txn_drcr =1;
				  $acc_cr_txn_amount=0;				  
				  $acc_dr_txn_total_amount +=$acc_dr_txn_amount;				  
				}				
				if($btnid =='submitbtn' && $Voucher_TxnApproval!='' && (parseAmount($acc_txn_amount) > parseAmount($Voucher_TxnApproval))){
				  $show_approval_message=1;    
				 $acc_txn_type = 4; // voucher is pending for approval
				}
				else if($btnid=='rejectsubmitbtn')
					$acc_txn_type = 5;
				else if($btnid=='approvesubmitbtn')
					$acc_txn_type = 1;
				 else{
					$acc_txn_type = ($oCheck==1)?2:1;
				 }
				
				$insert_data  = [
				  'cmp_id'           =>  $this->company_id,
				  'acc_id'           =>  $value['account_id'],
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => $acc_txn_drcr,
				  'acc_txn_amt'       => $acc_txn_amount,
				  'acc_txn_fcy'       => $acc_txn_fcy_amount, 
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $txn_id,
				  'hobo_id'           => $this->bo_id,
				  'acc_txn_type'      => $acc_txn_type
				];  				
				$this->VouchersModel->add_acc_txn_data($insert_data);
				
				$register_insert_data  = [
				  'acct_vch_type'     => $voucher_type_id,
				  'cmp_id'            => $this->company_id,
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $txn_id,
				  'vch_date'          => $voucher_date,				 
				  'acc_id'            => $value['account_id'],
				  'acc_txn_cr_amt'    => $acc_cr_txn_amount, 
				  'acc_txn_dr_amt'    => $acc_dr_txn_amount, 
				  'vch_narr'          => $value['description'],
				  'hobo_id'           => $this->bo_id,
				  'acc_txn_type'      => $acc_txn_type
				]; 								
				$this->VouchersModel->add_register_txn_data($register_insert_data);				
				$narration            = $value['description'];
				$this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'short',$narration);
			   }
			   
			   /*
    --------------------------------
    BILL BY BILL (Correct Mapping)
    --------------------------------
    */

		if (!empty($bbbdata)) {

			$filtered_bbb = [];

			foreach ($bbbdata as $bbb) {

				// ✅ MATCH USING row_index (from frontend fix)
				if (isset($bbb['row_index']) && $bbb['row_index'] == $key) {
					$filtered_bbb[] = $bbb;
				}
			}

			if (!empty($filtered_bbb)) {

				$this->VouchersModel->SaveBillByBillData(
					$filtered_bbb,
					$voucher_date,
					$voucher_txn_id,
					$txn_id
				);
			}
		}


		// Cost Centre
		if (!empty($ccdata)) {
			$filtered_cc = [];
			foreach ($ccdata as $cc) {
				if ($cc['row_index'] == $key) {
					$filtered_cc[] = $cc;
				}
			}

			if (!empty($filtered_cc)) {
				$this->VouchersModel->SaveCostCentreData($filtered_cc, $voucher_date, $voucher_txn_id, $txn_id);
			}
		}

		// Subledger
		if (!empty($sblgrdata)) {
			$filtered_sb = [];
			foreach ($sblgrdata as $sb) {
				if ($sb['row_index'] == $key) {
					$filtered_sb[] = $sb;
				}
			}

			if (!empty($filtered_sb)) {
				$this->VouchersModel->SaveSubLedgerData($filtered_sb, $voucher_date, $voucher_txn_id, $txn_id);
			}
		}

		// Project
		if (!empty($prdata)) {
			$filtered_pr = [];
			foreach ($prdata as $pr) {
				if ($pr['row_index'] == $key) {
					$filtered_pr[] = $pr;
				}
			}

			if (!empty($filtered_pr)) {
				$this->VouchersModel->SaveProjectReportingData($filtered_pr, $voucher_date, $voucher_txn_id, $txn_id);
			}
		}

            }
						
			if ($first_account_id && $acc_cr_txn_total_amount > 0) {
				$long_register_insert = [
					'acct_vch_type'   => $voucher_type_id,
					'cmp_id'          => $this->company_id,
					'vch_txn_id'      => $voucher_txn_id,
					'txn_id'          => NULL,
					'vch_date'        => $voucher_date,					
					'acc_id'          => $first_account_id,
					'acc_txn_cr_amt'  => $acc_cr_txn_total_amount,
					'acc_txn_dr_amt'  => 0,
					'vch_narr'        => $long_narration ?? '',
					'hobo_id'         => $this->bo_id,
					'acc_txn_type'      => $acc_txn_type
				];
				$this->VouchersModel->add_register_txn_data($long_register_insert);				
			}
		
         }
		 
		
		  $insert_data = [
			"cmp_id"         => $this->company_id,
			"vch_txn_id"     => $voucher_txn_id,
			"vch_series_id"  => $voucher_series,
			"master_id"      => 0,
			'master_id_type' => 'nrr'
		  ];
        $txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
        $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_narration);
        
         if($FromSaleVch==1){
		 //$SaleVchTxnId; 
		 $insert_data = [
					'acct_crs_id_type'		=> '',
					'acct_txn_id'           => $SaleVchTxnId,
					'comp_id'				=> $this->company_id,
					'txn_id'				=> 0,
					'voucher_txn_id'		=> $SaleVchTxnId,
					'bo_id'					=> $this->session->get('ses_boid'),
					'acc_cross_ref_type'	=> 'RCPTVCH',
					'acc_cross_ref_data'	=> $voucher_txn_id,
					'acc_cross_logdate'   	=> $voucher_date,
				    ];					
				$isexists = $this->TransactionModel->get_crsref($voucher_txn_id,'RCPTVCH');
				if($isexists=='')
				$this->TransactionModel->add_acc_crsref_data($insert_data); 
	  }
	  
	   if($FromPurchaseVch==1){
		 //$PurchaseVchTxnId; 
		 $insert_data = [
					'acct_crs_id_type'		=> '',
					'acct_txn_id'           => $PurchaseVchTxnId,
					'comp_id'				=> $this->company_id,
					'txn_id'				=> 0,
					'voucher_txn_id'		=> $PurchaseVchTxnId,
					'bo_id'					=> $this->session->get('ses_boid'),
					'acc_cross_ref_type'	=> 'PYMTVCH',
					'acc_cross_ref_data'	=> $voucher_txn_id,
					'acc_cross_logdate'   	=> $voucher_date,
				    ];					
				$isexists = $this->TransactionModel->get_crsref($voucher_txn_id,'PYMTVCH');
				if($isexists=='')
				$this->TransactionModel->add_acc_crsref_data($insert_data); 
	     }
			 $this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
		     $this->VouchersModel->UpdateConso($voucher_txn_id,["draft_vch_rec_id"=>0]);
	  
	  /************ Save Voucher Activity Log **********/
	    $voucher_messages = [
			9  => "Payment voucher has been updated by [USERNAME]([UUID])",
			13 => "Receipt voucher has been updated by [USERNAME]([UUID])",
			1  => "Contra voucher has been updated by [USERNAME]([UUID])",
			5  => "Journal voucher has been updated by [USERNAME]([UUID])",
			8  => "Memorandum voucher has been updated by [USERNAME]([UUID])"
		];
		$notification_approval_messages = [
			9  => "Payment voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])",
			13 => "Receipt voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])",
			1  => "Contra voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])",
			5  => "Journal voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])",
			8  => "Memorandum voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])"
		];
		$notification_rejected_messages = [
			9  => "Payment voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			13 => "Receipt voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			1  => "Contra voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			5  => "Journal voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			8  => "Memorandum voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])"
		];
		$erp_activity_log = $voucher_messages[$voucher_type_id] ?? "Voucher has been updated by [USERNAME]([UUID])";
		$this->VouchersModel->SaveUserActivity($erp_activity_log,$voucher_txn_id);
		
		if ($btnid=='submitbtn' && $show_approval_message == 1) {
			$erp_notify_log = $notification_approval_messages[$voucher_type_id] ?? "";
			$this->VouchersModel->SaveNotifications($erp_notify_log,$voucher_txn_id,$Voucher_TxnApproval_UUID,$acc_cr_txn_total_amount);
            $aprvdata = array("cmp_id"=> $this->company_id,"vch_txn_id"=>$voucher_txn_id,
		               "uuid_by"=>$this->session->get('uuid'),"uuid_to"=>$Voucher_TxnApproval_UUID,
					   "erp_txn_aprv_status"=>4
					   );
			$this->VouchersModel->SaveTxnApprvLog($aprvdata,$voucher_txn_id,$Voucher_TxnApproval_UUID,4);
            
			return [
                'message' => 'Voucher has been saved and is pending approval',
                'data'    => [] // optional: any data you want to return
            ];
        }
		else if($btnid=='rejectsubmitbtn'){
			$erp_notify_log = $notification_rejected_messages[$voucher_type_id] ?? "";
			$this->VouchersModel->SaveNotifications($erp_notify_log,$voucher_txn_id,$Voucher_TxnApproval_UUID,$acc_cr_txn_total_amount);
            /* $data = array("cmp_id"=> $this->company_id,"vch_txn_id"=>$voucher_txn_id,
		               "uuid_by"=>$this->session->get('uuid'),"uuid_to"=>$uuid_to,
					   "erp_txn_aprv_status"=>$erp_txn_aprv_status
					   );
			$this->VouchersModel->SaveTxnApprvLog($voucher_txn_id,$Voucher_TxnApproval_UUID,5);
             */
			 return [
                'message' => 'Voucher has been rejected',
                'data'    => [] // optional: any data you want to return
            ];
		}
		else{
		return [
            'message' => 'Voucher saved successfully.',
            'data'    => []
        ];
		}
		
        });
        return $this->response->setJSON($trans_result);
      }

    if($voucher_type_id == 5 && $vch_subtype_id == 23){   // with item
		  
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
    $trans_result = $this->runTransaction(function($db) use ($voucher_txn_id,$vch_subtype_id,$taxes_list,$voucher_type_id,$voucher_ed_date){
       try{  
	    $voucher_date         = $this->request->getVar('voucher_date');
        $voucher_date         = validate_date_by_fy($voucher_date);
        $voucher_series       = $this->request->getVar('voucher_series');       
        $matrcntr_id          = $this->request->getVar('matrcntr_id');   
        $long_narration        = $this->request->getVar('narration');
        $currency_id          = $this->request->getVar('currency_id');
		$fcy_forex_rate       = $this->request->getVar('fcy_forex_rate');
		$btnid                = $this->request->getVar('btnid');// submitbtn default save button
		$draft_vch_rec_id     = $this->request->getVar('draft_vch_rec_id');
	  if(!$fcy_forex_rate)
		  $fcy_forex_rate=0;

       $in_batchdata = [];
	   if(!empty($this->request->getVar('inbatchdata')))
	  $in_batchdata = json_decode($this->request->getVar('inbatchdata'),true);
	  
	   $out_batchdata = [];
	   if(!empty($this->request->getVar('outbatchdata')))
	    $out_batchdata = json_decode($this->request->getVar('outbatchdata'),true);	
         
		$is_batch=FALSE;
	   if($in_batchdata)
        $is_batch=TRUE; 

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

        $prdata = [];
        if(!empty($this->request->getVar('prdata')))
          $prdata = json_decode($this->request->getVar('prdata'),true);
        
		$is_bbb=FALSE;
		 if($bbbdata)
		 $is_bbb=TRUE;			
		 
		 $is_pr=FALSE;
		  if($prdata)
		   $is_pr=TRUE;
	   
		 $is_cc=FALSE;
		  if($ccdata)
		   $is_cc=TRUE;
		  $vch_particulars = '';
		  
		  // Mark Dirty Transactions
		    $voucherDate = new \DateTime($voucher_ed_date);
			$today       = new \DateTime('today');
           	 if($voucherDate < $today){ 
				 // Call your dirty recalculation function
				 //$this->VouchersModel->markSnapshotDirty($voucher_ed_date);
			 }
			 
	    $voucherdata     = json_encode(["is_batch"=>$is_batch,"in_batchdata"=>$in_batchdata,"out_batchdata"=>$out_batchdata,"mat_cent_id"=>$matrcntr_id,"item_data_from"=>$item_data_from,"item_data_to"=>$item_data_to,
	                                    "acc_data_from"=>$acc_data_from,"acc_data_to"=>$acc_data_to]);
		if($draft_vch_rec_id >0){
			
		$pgrdata_drft    = array(
									   "log_date_time"=>$voucher_date,
									   "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount(0),"vch_fcy_amt"=>parseAmount(0),"vch_fcy_rate"=>parseAmount(0),
									   "vch_particulars"=>"N/A","uuid_aictly"=>$this->session->get('uuid'),
									   "long_narr"=>$long_narration,"vch_series_id"=>(int)$voucher_series,"isoptional"=>FALSE,
									   "vch_fcy_id"=>(int)$currency_id,"is_cc"=>(int)$is_cc,"is_bbb"=>(int)$is_bbb,"is_pr"=>(int)$is_pr,"is_sblgr"=>0
										);	
		$this->VouchersModel->UpdateDrftVoucher($draft_vch_rec_id,$pgrdata_drft);	
		}
		else{		
				$pgrdata_drft    = array("cmp_id"=>$this->company_id,"hobo_id"=>$this->bo_id,
									   "log_date_time"=>$voucher_date,"vch_type_id"=>$voucher_type_id,
									   "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount(0),"vch_fcy_amt"=>parseAmount(0),"vch_fcy_rate"=>parseAmount(0),
									   "vch_particulars"=>"N/A","uuid_aictly"=>$this->session->get('uuid'),
									   "long_narr"=>$long_narration,"vch_series_id"=>(int)$voucher_series,"isoptional"=>FALSE,
									   "vch_fcy_id"=>(int)$currency_id,"is_cc"=>(int)$is_cc,"is_bbb"=>(int)$is_bbb,"is_pr"=>(int)$is_pr,"is_sblgr"=>0
										);
				$draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);

		  }
			/********************************************************************/
		if($btnid=='submitbtn_drft'){
		   return json_encode(['status' => true, 'message' => 'Voucher saved in draft mode']); 
	    }
		
	   /* Remove Old Entries using vch_txn_id */
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cmptxnmstn');	
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'accttxnmst');
       $this->VouchersModel->clear_table_narrations_data($voucher_txn_id);
	   $this->VouchersModel->clear_register_txn_data($voucher_txn_id,$voucher_type_id);
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'billtxnmst');
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cctxnmstnn');
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'prjtxnmstn');
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'subacctxnm');
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchfcyrate');	   
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'itemtxnmst');
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'itmvchregn');
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'batchtxnmt');
	   
	   $update_data  = array(
          "vch_series_id"   => $voucher_series,
          "vch_date"         => $voucher_date,
          "draft_vch_rec_id" => $draft_vch_rec_id,
		  "mat_cent_id"      => $matrcntr_id,	
        );
        $this->VouchersModel->update_voucher_cons_data($update_data,$voucher_txn_id,$this->company_id);
         
		if($fcy_forex_rate >0){
			$fcy_data = array("cmp_id"=>$this->company_id,"vch_txn_id"=>$voucher_txn_id,
			                   "vch_fcy_rate"=>$fcy_forex_rate,"cmp_fcy_mst_id"=>$currency_id
							  );	
		    $this->VouchersModel->save_voucher_fcyrate($fcy_data);	
		    } else{				
			$this->VouchersModel->clear_voucher_fcyrate($voucher_txn_id);
			} 
	   $itm_txn_type = 1;	//1 for Regular 	
       if($item_data_from){			
			$item_account_array = [];
        foreach($item_data_from as $item_row){
          $txn_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $item_row['item_id'],
				  'master_id_type'  => 'itm'
				 ];
          $main_txn_id  = $this->VouchersModel->add_comp_txn_data($txn_data);
          if(isset($item_row['item_qty']) && $item_row['item_qty'] >0){
			$insert_data   = array(
				'cmp_id'             => $this->company_id,
				'itm_id_unit_id'     => $item_row['item_id'].'_'.$item_row['item_unit_id'],
				'itm_txn_qty'        => $item_row['item_qty'],
				'itm_txn_date'       => $voucher_date,
				'itm_txn_dr_cr'      => 1,
				'itm_txn_rate'       => parseAmountPrice($item_row['item_price'],4),
				'itm_txn_amt'        => parseAmount($item_row['item_total_amount']),
				'itm_txn_fcy'        => 0,
				'vch_txn_id'         => $voucher_txn_id,
				'txn_id'             => $main_txn_id,
				'mat_cent_id'        => $matrcntr_id,
				'hobo_id'            => $this->bo_id,
				'itm_txn_type'		 => $itm_txn_type		
			    );	
			 $this->VouchersModel->add_itm_txn_data($insert_data); 
			 $register_insert_data  = [
                				  'itm_vch_type'     => $voucher_type_id,
                				  'cmp_id'            => $this->company_id,
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $main_txn_id,
                				  'vch_date'          => $voucher_date,				 
                				  'itm_id_unit_id'    => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                				  'vch_narr'          => $long_narration ?? '',
                				  'hobo_id'           => $this->bo_id,
                				  'itm_txn_type'      => $itm_txn_type
                				]; 								
            $itm_vch_reg_id =  $this->VouchersModel->add_item_register_txn_data($register_insert_data);	
			/************  Insert Register Data In Sub Table Also ************/
			$register_sub_insert_data  = [
                				  'itm_vch_reg_id'    => $itm_vch_reg_id,
								  'mat_cent_id'       => $matrcntr_id,
                				  'itm_txn_dr_qty'    => $item_row['item_qty'],
                				  'itm_txn_dr_rate'   => parseAmountPrice($item_row['item_price'],4),
                				  'itm_txn_dr_amt'    => parseAmount($item_row['item_total_amount']),
                				  'itm_txn_cr_qty'    => 0,				 
                				  'itm_txn_cr_rate'   => 0,
                				  'itm_txn_cr_amt'    => 0,
                				  'vch_narr'          => $long_narration ?? '',              				 
                				]; 								
            $this->VouchersModel->add_item_sub_register_txn_data($register_sub_insert_data);	
            
			$account_id  =  $item_row['item_pur_acc'];
            if(isset($item_account_array[$account_id]))
            $item_account_array[$account_id] += parseAmount($item_row['item_total_amount']);
           else
            $item_account_array[$account_id] = parseAmount($item_row['item_total_amount']);
			
			}
		 /***********   Save Item Batch  Data  *************/
			if(count($in_batchdata)){		
			   $this->VouchersModel->SaveBatchData($in_batchdata,$voucher_date,$voucher_txn_id,$main_txn_id,$matrcntr_id);
			}	
		  }
		 foreach ($item_account_array as $account_id => $amount) {	
			$insert_data = [
							  "cmp_id"          => $this->company_id,
							  "vch_series_id"   => $voucher_series,
							  "vch_txn_id"      => $voucher_txn_id,
							  "master_id"       => $account_id,
							  'master_id_type'  => 'acc'
							 ];
			$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
			$acc_txn_data  = [
							  'cmp_id'            => $this->company_id,
							  'acc_id'            => $account_id,
							  'acc_txn_date'      => $voucher_date,
							  'acc_txn_dr_cr'     => 1,
							  'acc_txn_amt'       => $amount, 
							  'acc_txn_fcy'       => 0,
							  'vch_txn_id'        => $voucher_txn_id,
							  'txn_id'            => $txn_id,
							  'hobo_id'           => $this->bo_id,
							  'acc_txn_type'      => 1
							];  				
			$this->VouchersModel->add_acc_txn_data($acc_txn_data);	
		 }
		  
	   }

      if($item_data_to){
	    $item_account_array = [];
        foreach($item_data_to as $item_row){	
		 $txn_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $item_row['item_id'],
				  'master_id_type'  => 'itm'
				 ];
          $txn_id  = $this->VouchersModel->add_comp_txn_data($txn_data);
          if(isset($item_row['item_qty']))
             $item_qty = $item_row['item_qty'];
           else
             $item_qty =  0;    
		 
		   $insert_data   = array(
				'cmp_id'             => $this->company_id,
				'itm_id_unit_id'     => $item_row['item_id'].'_'.$item_row['item_unit_id'],
				'itm_txn_qty'        => $item_row['item_qty'],
				'itm_txn_date'       => $voucher_date,
				'itm_txn_dr_cr'      => 2,
				'itm_txn_rate'       => parseAmountPrice($item_row['item_price'],4),
				'itm_txn_amt'        => parseAmount($item_row['item_total_amount']),
				'itm_txn_fcy'        => 0,
				'vch_txn_id'         => $voucher_txn_id,
				'txn_id'             => $main_txn_id,
				'mat_cent_id'        => $matrcntr_id,
				'hobo_id'            => $this->bo_id,
				'itm_txn_type'		 => $itm_txn_type		
			    );	
			$this->VouchersModel->add_itm_txn_data($insert_data);
			
			$register_insert_data  = [
                				  'itm_vch_type'     => $voucher_type_id,
                				  'cmp_id'            => $this->company_id,
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $main_txn_id,
                				  'vch_date'          => $voucher_date,				 
                				  'itm_id_unit_id'    => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                				  'vch_narr'          => $long_narration ?? '',
                				  'hobo_id'           => $this->bo_id,
                				  'itm_txn_type'      => $itm_txn_type
                				]; 								
            $itm_vch_reg_id =  $this->VouchersModel->add_item_register_txn_data($register_insert_data);	
			/************  Insert Register Data In Sub Table Also ************/
			$register_sub_insert_data  = [
                				  'itm_vch_reg_id'    => $itm_vch_reg_id,
								  'mat_cent_id'       => $matrcntr_id,
                				  'itm_txn_dr_qty'    => 0,
                				  'itm_txn_dr_rate'   => 0,
                				  'itm_txn_dr_amt'    => 0,
                				  'itm_txn_cr_qty'    => $item_row['item_qty'],				 
                				  'itm_txn_cr_rate'   => parseAmountPrice($item_row['item_price'],4),
                				  'itm_txn_cr_amt'    => parseAmount($item_row['item_total_amount']),
                				  'vch_narr'          => $long_narration ?? '',              				 
                				]; 								
            $this->VouchersModel->add_item_sub_register_txn_data($register_sub_insert_data);
			$account_id  =  $item_row['item_sales_acc'];

			 if(isset($item_account_array[$account_id]))
			  $item_account_array[$account_id] += parseAmount($item_row['item_total_amount']);
			else
			  $item_account_array[$account_id] = parseAmount($item_row['item_total_amount']);

		  /***********   Save Item Batch  Data  *************/
			if(count($in_batchdata)){		
			   $this->VouchersModel->SaveBatchData($out_batchdata,$voucher_date,$voucher_txn_id,$main_txn_id,$matrcntr_id);
			}
		}
		foreach ($item_account_array as $account_id => $amount) {
				$insert_data = [
							  "cmp_id"          => $this->company_id,
							  "vch_series_id"   => $voucher_series,
							  "vch_txn_id"      => $voucher_txn_id,
							  "master_id"       => $account_id,
							  'master_id_type'  => 'acc'
							 ];
				$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);	
				$acc_txn_data  = [
				  'cmp_id'            => $this->company_id,
				  'acc_id'            => $account_id,
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => 2,
				  'acc_txn_amt'       => $amount, 
				  'acc_txn_fcy'       => 0,
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $txn_id,
				  'hobo_id'           => $this->bo_id,
				  'acc_txn_type'      => 1
				];  				
				$this->VouchersModel->add_acc_txn_data($acc_txn_data);	 
		   }
	  }
	    // Process 'from' accounts (debit)
		$this->process_acc_data($acc_data_from, 1,$voucher_series,$voucher_txn_id,$voucher_date); 
		// Process 'to' accounts (credit)
		$this->process_acc_data($acc_data_to, 2 , $voucher_series,$voucher_txn_id,$voucher_date);		
		/******************* Start of Save Voucher Naration  ****************/
			 $nr_insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => 0,
				  'master_id_type'  => 'nrr'
				 ];
			 $txn_id = $this->VouchersModel->add_comp_txn_data($nr_insert_data);
			 $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_narration);
			/******************* End of Save Voucher Naration  ****************/
       
	    /***********   Save Bill By Bill Data  *************/
		if(count($bbbdata)){		
		 $this->VouchersModel->SaveBillByBillData($bbbdata,$voucher_date,$voucher_txn_id,$main_txn_id);
		}
		/***********   Save Cost Center  Data  *************/
		if(count($ccdata)){		
		 $this->VouchersModel->SaveCostCentreData($ccdata,$voucher_date,$voucher_txn_id,$main_txn_id);
		}
		/***********   Save Project Reporting  Data  *************/
		if(count($prdata)){		
		 $this->VouchersModel->SaveProjectReportingData($prdata,$voucher_date,$voucher_txn_id,$main_txn_id);
		}
	  $this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
	  $this->VouchersModel->UpdateConso($voucher_txn_id,["draft_vch_rec_id"=>0]);	
	  
      return ['status' => true, 'message' => 'Voucher Updated'];
       }
	  catch (\Throwable $e) {
				return  ['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()];
				// Get line, file and stack trace
                log_message('error', 'DB Error: ' . $e->getMessage());
                log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());
                log_message('error', 'Trace: ' . $e->getTraceAsString());
    	}
	});
		
	if(isset($trans_result['result']['status']) && $trans_result['result']['status']=='')
          	return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message']]);
       else
         return $this->response->setJSON($trans_result);              
      }
	
	
	}// post condition end 
   if(in_array($voucher_type_id, $voucher_type_array) && $vch_subtype_id == 0){


      if($voucher_tag == 'OPTIONL'){
        $data['account_transactions'] = $this->TransactionModel->get_all_account_oth_transactions($voucher_txn_id, true);
        $data['cc_data']   = [];
        $data['bbb_data']  = [];
        $data['pr_data']   = [];
        $data['oCheck']    = 1;
      }
      else{	
		
        $data['account_transactions'] = $this->VouchersModel->get_all_account_transactions($voucher_txn_id);
      
		
		if(in_array(4, array_column($data['account_transactions'], 'vch_txn_type'))) {
			$data['pending_vch'] = 1;			
		}else
			$data['pending_vch'] = 0;
		if(in_array(5, array_column($data['account_transactions'], 'vch_txn_type'))) {
			$data['rejected_vch'] = 1;
		}else
			$data['rejected_vch'] = 0;  
		// Voucher can be approved/rejected by user(whose uuid is set in approver (Voucher_TxnApproval_UUID)
		if(($data['pending_vch']==1 || $data['rejected_vch']==1)){
			$is_voucher_editable="false";
		}
		else 
			$is_voucher_editable="true";
		
		$data['Voucher_TxnApproval_UUID'] = $Voucher_TxnApproval_UUID ?? 0;
		$data['current_uuid']             = $this->session->get('uuid');		
		$data['is_voucher_editable'] = $is_voucher_editable;
		$data['cc_data']     = $this->VouchersModel->get_cc_txn_data($voucher_txn_id);
        $data['bbb_data']    = $this->VouchersModel->get_bills_txn_data($voucher_txn_id);
        $data['pr_data']     = $this->VouchersModel->get_pr_txn_data($voucher_txn_id);
		$data['sblgr_data']  = $this->VouchersModel->get_sblgr_txn_data($voucher_txn_id);
        $data['oCheck']      = 0;
       }
      $voucher_detail     = $this->VouchersModel->get_vouchertype_info($voucher_type_id);
      $get_narration_info = $this->VouchersModel->get_voucher_long_narration($voucher_txn_id);
	  $data['migrate_voucher_txn_id'] = 0; 	 
	  $data['migrate_voucher_txn_id'] = 0; 
	  $data['migrate_account_transactions'] = [];
	  $data['migrate_cc_data'] = [];
	  $data['migrate_bbb_data'] = [];
	  $data['migrate_pr_data'] = [];
	  if(isset($_GET['v'])){  // when migrated
		  $voucher_info = $this->TransactionModel->get_voucher_cons_info($_GET['v']);
		  if(!empty($voucher_info)){
			$data['migrate_voucher_txn_id'] = $_GET['v'];
			$data['migrate_account_transactions'] = $this->TransactionModel->get_all_account_transactions($_GET['v']);
			$data['migrate_cc_data'] = $this->TransactionModel->get_cc_txn_data($_GET['v']);
			$data['migrate_bbb_data'] = $this->TransactionModel->get_bills_txn_data($_GET['v']);

			$get_narration_info = $this->VouchersModel->get_voucher_long_narration($_GET['v']);
			$data['narration'] = !empty($get_narration_info) ? $get_narration_info['vch_long_narr'] : '';

			$data['voucher_date'] = date('d-m-Y', strtotime($voucher_info['vch_date']));
		  }
		}
	 
	  $data['data_groups']              = $this->VouchersModel->ajax_receipt_accounts_list();
      $data['message_output']           = $this->message_output;
	  $data['voucher_txn_id']           = $voucher_txn_id;
      $data['base_url']                 = $this->base_url; 
	  $data['voucher_tag']              = $voucher_tag; 
      $data['folder_path']              = $this->folder_path; 
      $data['voucher_name']             = $voucher_detail['vch_name'];
      $data['voucher_type_id']          = $voucher_type_id;
      $data['voucher_series_dropdown']  = $this->VouchersModel->comp_voucher_series($this->company_id,$voucher_type_id);
      $data['narration']                = $this->VouchersModel->get_voucher_long_narration($voucher_txn_id);
      $data['voucher_series']           = $voucher_info['vch_series_id'];     
      $data['voucher_date']             = date('d-m-Y', strtotime($voucher_info['vch_date']));      
      $data['bills_method_list']        = ['','New Ref.','Adjustment'];

  	  $accounts_list    = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
	   $bsd_accounts     = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
	
     if($accounts_list!='' &&  $bsd_accounts!='')
        $company_all_acc_bsd    = array_merge(json_decode($accounts_list,true),json_decode($bsd_accounts,true));
	 else
	    $company_all_acc_bsd      = json_decode($accounts_list,true);	
      $data['acc_bsd_json_file']  = json_encode($company_all_acc_bsd);
      $data['currency_list']      = $this->VouchersModel->get_currency_list();
      $data['currency_id']        = $voucher_info['currency_id'];
	  $data['forexcrncy_rate']    = $voucher_info['forexcrncy_rate'];
      $data['bo_gstin_type']  = $this->session->get('bo_gstin_type');
	  /* if($voucher_type_id==13){		  
	      $info = array('uuid'=>$uuid,'usr_config_id'=>266,'usr_config_value'=>array(100,126));
	      $data['get_default_template'] = $this->TransactionModel->check_default_prnttheme_info($info);
	  }
	  else */
     $data['get_default_template'] =0;	  
	$format = isset($_GET['format']) ? $_GET['format'] : 1;
	$data['format'] =$format;
	
	
	if($data['voucher_type_id'] == 9){
      if($format == 1)
      return view($this->folder_path.'vouchers/edit_classic_invoice_payment',$data);   
      if($format == 2)
      return view($this->folder_path.'vouchers/edit_invoice',$data); 

    }else if( $data['voucher_type_id'] == 13){
      if($format == 1)
      return view($this->folder_path.'vouchers/edit_classic_invoice_receipt',$data);   
      if($format == 2)
      return view($this->folder_path.'vouchers/edit_invoice',$data);  
    }else
      return view($this->folder_path.'vouchers/edit_invoice',$data);
    }
    if($voucher_type_id==22 ){
	  
	  $bridge_info = $this->VouchersModel->get_voucher_gstpaid_bridge_info($voucher_txn_id);
		$vch_txn_id_src = $bridge_info['vch_txn_id_src'] ?? 0;
		 $getVoucherInfoTxnId = $this->VouchersModel->getVoucherInfoTxnId($vch_txn_id_src);
		 $vch_type_id = $getVoucherInfoTxnId['vch_type_id'] ?? 0;
		 
		 $data['vch_name'] = $getVoucherInfoTxnId['vch_name'] ?? '';
		
        $data['account_transactions'] = $this->VouchersModel->get_all_account_transactions($voucher_txn_id);
        $data['cc_data'] = $this->VouchersModel->get_cc_txn_data($voucher_txn_id);
        $data['bbb_data'] = $this->VouchersModel->get_bills_txn_data($voucher_txn_id);
        $data['pr_data'] = $this->VouchersModel->get_pr_txn_data($voucher_txn_id);
        $data['oCheck'] = 0;
       // echo "<pre>";print_r($data['cc_data']);exit;
      $voucher_detail     = $this->VouchersModel->get_voucher_cons_info($voucher_type_id);
      $get_narration_info = $this->VouchersModel->get_voucher_long_narration($voucher_txn_id);

 $data['vch_txn_id_src']           = $vch_txn_id_src;
      $data['message_output']           = $this->message_output;
	  $data['voucher_txn_id']           = $voucher_txn_id;
      $data['base_url']                 = $this->base_url; 
      $data['folder_path']              = $this->folder_path; 
      $data['voucher_name']             = 'System Generated';
      $data['voucher_type_id']          = $voucher_type_id;      
     
      $data['voucher_series_dropdown']  = $this->VouchersModel->comp_voucher_series($this->company_id,$voucher_type_id);
      $data['narration']                = $get_narration_info;
      $data['vch_series_id']           = $voucher_info['vch_series_id'];
      $data['voucher_no']               = '';
      $data['voucher_date']             = date('d-m-Y', strtotime($voucher_info['vch_date']));
      $data['voucher_txn_id']           = $voucher_txn_id;
      $data['bills_method_list']        = ['','New Ref.','Adjustment'];
	  $accounts_list                  = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
		 $bsd_accounts                   = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
		 if($bsd_accounts){
		 $filter_bsd_accounts    	    = array_filter(json_decode($bsd_accounts,true), function($row) {
											return $row['is_tax_account'] != 1;
											});
		 }
		 else{
			$filter_bsd_accounts   =[];
		 }
		  $data['accounts_json_file']    = $accounts_list;
		   $data['bill_ref_no'] ='';
		  if($vch_type_id==18 || $vch_type_id==3){
	  $gstroutsup_info               = $this->VouchersModel->get_gstroutsup_info($vch_txn_id_src);
      $data['bill_ref_no']           = $gstroutsup_info['outsup_bill_ref_no'];
      
	  }
	  if($vch_type_id==11 || $vch_type_id==2){
	  $gstroutsup_info                = $this->VouchersModel->get_gstrinwsup_info($vch_txn_id_src);
      $data['bill_ref_no']            =$gstroutsup_info['inwsup_bill_ref_no'];
	  }
	  
	  $data['acc_bsd_json_file']         = json_encode($filter_bsd_accounts);	
     $data['currency_list']         = $this->VouchersModel->get_currency_list();
      $data['currency_id']            =  $voucher_info['currency_id'];

      // echo "<pre>";print_r($data);exit;
      return view($this->folder_path.'vouchers/edit_gstpaid_invoice',$data);
   }
   if($voucher_type_id==23 ){
	   
		$bridge_info = $this->VouchersModel->get_voucher_gstpaid_bridge_info($voucher_txn_id);
		$vch_txn_id_src = $bridge_info['vch_txn_id_src'] ?? 0;
		 $getVoucherInfoTxnId = $this->VouchersModel->getVoucherInfoTxnId($vch_txn_id_src);
		 $vch_type_id=0;
		 $data['vch_name']='';
		  $data['bill_ref_no']='';
		 if($getVoucherInfoTxnId){
		 $vch_type_id = $getVoucherInfoTxnId['vch_type_id'];
		 $data['vch_name'] = $getVoucherInfoTxnId['vch_name'];
		 }
		
        $data['account_transactions']   = $this->VouchersModel->grid_gstpaid_account_transactions($voucher_txn_id);
      
		$data['cc_data'] = [];
        $data['bbb_data'] = [];
        $data['pr_data'] = [];
        $data['oCheck'] = 0;
     
	   $voucher_info       = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
      // echo "<pre>";print_r($voucher_info);exit;
	   $data['voucher_date']             = date('d-m-Y', strtotime($voucher_info['vch_date']));
      $data['message_output']           = $this->message_output;
	  $data['vch_txn_id_src']           = $vch_txn_id_src;
	  $data['voucher_txn_id']           = $voucher_txn_id;
      $data['base_url']                 = $this->base_url; 
      $data['folder_path']              = $this->folder_path; 
      $data['voucher_name']             = 'System Journal';
      $data['voucher_type_id']          = $voucher_type_id;      
      $data['voucher_series_dropdown']  = $this->VouchersModel->comp_voucher_series($this->company_id,$voucher_type_id);
      if($vch_type_id==18 || $vch_type_id==3){
	  $gstroutsup_info               = $this->VouchersModel->get_gstroutsup_info($vch_txn_id_src);
      $data['bill_ref_no']           = $gstroutsup_info['outsup_bill_ref_no'];
      
	  }
	  if($vch_type_id==11 || $vch_type_id==2){
	  $gstroutsup_info                = $this->VouchersModel->get_gstrinwsup_info($vch_txn_id_src);
      $data['bill_ref_no']            =$gstroutsup_info['inwsup_bill_ref_no'];
	  }
	  
	  $data['narration']                = $this->VouchersModel->get_voucher_long_narration($voucher_txn_id);
      $data['vch_series_id']            =  $voucher_info['vch_series_id'];
      $data['voucher_date']             = date('d-m-Y', strtotime($voucher_info['vch_date']));
      $data['voucher_txn_id']           = $voucher_txn_id;
      $data['bills_method_list']        = ['','New Ref.','Adjustment'];
	  $accounts_list                  = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
	 $bsd_accounts                   = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
	 if($bsd_accounts){
	 $filter_bsd_accounts    	    = array_filter(json_decode($bsd_accounts,true), function($row) {
										return $row['is_tax_account'] != 1;
										});
	 }
	 else{
	    $filter_bsd_accounts   =[];
	 }
	  $data['accounts_json_file']    = $accounts_list;
	  $data['acc_bsd_json_file']         = json_encode($filter_bsd_accounts);	
      $data['currency_list']         = $this->VouchersModel->get_currency_list();
      $data['currency_id']            =  $voucher_info['currency_id'];

      // echo "<pre>";print_r($data);exit;
      return view($this->folder_path.'vouchers/edit_gstpaid_invoice',$data);
   }
    if($voucher_type_id == 5 && $vch_subtype_id == 23){
      $account_transactions 			= $this->VouchersModel->get_journal_account_transactions($voucher_txn_id);     
      $data['l_accounts']   			= $account_transactions['l_accounts'];
      $data['r_accounts']  				= $account_transactions['r_accounts'];
      $item_transactions   				= $this->VouchersModel->get_journal_item_transactions($voucher_txn_id);
      $data['l_items']     				= $item_transactions['l_items'];
      $data['r_items']      			= $item_transactions['r_items'];
      $data['cc_data']    			    = $this->VouchersModel->get_cc_txn_data($voucher_txn_id);
      $data['bbb_data']  			    = $this->VouchersModel->get_bills_txn_data($voucher_txn_id);
      $data['pr_data']   				= $this->VouchersModel->get_pr_txn_data($voucher_txn_id);
      $data['item_batch_data']          = $this->VouchersModel->get_itembatch_txn_data($voucher_txn_id);
	  $data['message_output']           = $this->message_output;
      $data['base_url']                 = $this->base_url; 
      $data['folder_path']              = $this->folder_path; 
      $data['voucher_name']             = $voucher_detail['vch_name'];     
      $data['voucher_type_id']          = $voucher_type_id;      
      $data['voucher_series_dropdown']  = $this->VouchersModel->comp_voucher_series($this->company_id,$voucher_type_id);
      $data['units_list']               = $this->VouchersModel->units_dropdown();
      $data['matrcntr_dropdown']        = $this->VouchersModel->material_centre_dropdown();
      $data['narration']                = $this->VouchersModel->get_voucher_long_narration($voucher_txn_id);
      $data['voucher_series']           = $voucher_info['vch_series_id'];
      $data['voucher_no']               = '';//$voucher_info['comp_vch_no'];
      $data['matrcntr_id']              = $voucher_info['mat_cent_id'];
      $data['voucher_date']             = date('d-m-Y', strtotime($voucher_info['vch_date']));
      $data['voucher_txn_id']           = $voucher_txn_id;
      $data['bills_method_list']        = ['','New Ref.','Adjustment'];
	  $data['taxes_list']               = $taxes_list;	  
	  $accounts_list                    = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
	  $bsd_accounts                     = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
  	  $items_list                       = GetJsonFileContent($this->fy_id,$this->company_id,'itm');
	  $data['item_json_file']           = $items_list;
      $company_all_acc_bsd              = array_merge(json_decode($accounts_list,true),json_decode($bsd_accounts,true));
      $data['acc_bsd_json_file']        = json_encode($company_all_acc_bsd);
      $data['currency_list']            = $this->VouchersModel->get_currency_list();
      $data['currency_id']       		= $voucher_info['currency_id'];
	  $data['forexcrncy_rate']   		= $voucher_info['forexcrncy_rate'];
	  $data['bo_gstin_type']            = $this->session->get('bo_gstin_type');
      return view($this->folder_path.'vouchers/edit_item',$data);
    }
  }
  
  public function finalize_draft($draft_vch_rec_id){   
    $uuid               = $this->session->get('uuid');
	$voucher_type_array = [1,5,9,13];
	
    /**********  Get Voucher Details From Draft postgr_db  *************/
	$voucher_info       = $this->VouchersModel->GetDrftVoucher($draft_vch_rec_id);
    if(empty($voucher_info)){
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }	 
	
	$voucher_tag             ='';
    $voucher_type_id         = $voucher_info['vch_type_id']; 
	$long_narration          = $voucher_info['long_narr'];	
    $vch_subtype_id          = 0;	
	/**************  Duplicate Voucher Code ***************/
	if(isset($_GET['duplc']) && $_GET['duplc']=='1'){
				$data['duplc'] 		     = 1;
				$page_label              = 'Duplicate';		
				
	 }else{
				 $data['duplc'] 	 = 0;
                 $page_label         = 'Update';  					     			  
	}
			
	$data['page_label'] = $page_label;	
	/**************  Duplicate Voucher Code ***************/

    if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
       //echo "<pre>";print_r($_POST);exit; 
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

      $long_narration     = $this->request->getVar('narration');
      $voucherdata        = $this->request->getVar('voucherdata');
      $voucher_data       = json_decode($voucherdata,true);
      $voucher_date       = $this->request->getVar('voucher_date');
      $voucher_series     = $this->request->getVar('voucher_series');	  
	  $FromSaleVch        = $this->request->getVar('FromSaleVch');// Auto Recipt Voucher From Sale Voucher
	  $SaleVchTxnId       = $this->request->getVar('SaleVchTxnId');
	  $btnid              = $this->request->getVar('btnid');// submitbtn default save button
	  $oCheck             = $this->request->getVar('oCheck'); // OPTIONAL VOUCHER TAG
	  if(!$FromSaleVch)
		  $FromSaleVch =0;
	  
	  $FromPurchaseVch        = $this->request->getVar('FromPurchaseVch');// Auto Payment Voucher From Purchase Voucher
	  $PurchaseVchTxnId       = $this->request->getVar('PurchaseVchTxnId');
	  if(!$FromPurchaseVch)
		  $FromPurchaseVch =0;
      $VchOthrBo          = $this->request->getVar('VchOthrBo');
      if(!$VchOthrBo)
       $VchOthrBo =0;

      $voucher_date   = validate_date_by_fy($voucher_date);
      $currency_id    = $this->request->getVar('currency_id');
      $fcy_forex_rate = $this->request->getVar('fcy_forex_rate');      
      $rCheck         = $this->request->getVar('rCheck');
	  $isoptional =FALSE;
		if($oCheck==1)
			$isoptional =TRUE; 
	  $trans_result = $this->runTransaction(function($db) use ($isoptional,$oCheck,$draft_vch_rec_id,$long_narration,$voucher_data,$voucher_type_id, $voucher_series, $voucher_date, $VchOthrBo,$FromPurchaseVch,$FromSaleVch,$SaleVchTxnId,$PurchaseVchTxnId)
		{		
		/************  Code for Voucher Payment, Receipt, Contra, Journal without item ********/ 
        $insert_data  = array(
          "cmp_id"           => $this->company_id,		  
          "vch_series_id"    => $voucher_series,          
          "vch_type_id"      => $voucher_type_id,
		  "vch_sub_type_id"  => 0,		   
          "vch_date"         => $voucher_date,
          "mat_cent_id"      => 0,
		  "draft_vch_rec_id" => $draft_vch_rec_id
         );		
         $voucher_txn_id = $this->VouchersModel->add_voucher_cons_data($insert_data,$VchOthrBo);
			
        if($voucher_data)
         {
		    $acc_cr_txn_total_amount =0;
			$acc_dr_txn_total_amount =0;
			$first_account_id = NULL;
			foreach($voucher_data as $key => $value)
			{
			  if($value['acc_type'] == 'acc')
			  {
				if ($first_account_id == NULL && $value['drcr'] == 'D' && $voucher_type_id==9 ) {
					$first_account_id = $value['account_id']; 
				}
				if ($first_account_id == NULL && $value['drcr'] == 'C' && $voucher_type_id==13 ) {
					$first_account_id = $value['account_id']; 
				}
				if ($first_account_id == NULL && $value['drcr'] == 'C' && ($voucher_type_id==1 || $voucher_type_id==5) ) {
					$first_account_id = $value['account_id']; 
				}
		
				$account_info       = $this->AccountsModel->account_info($value['account_id']);  
				$acc_grp_id = 0;
				if($account_info){
					$acc_grp_id = $account_info['under_crs_mst_id'];
				}
				$insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $value['account_id'],
				  'master_id_type'  => 'acc'
				];
				
				$acc_txn_amount      = 0;
				$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
				
				if($value['drcr'] == 'C'){
				  $acc_cr_txn_amount = parseAmount($value["credit"]);
				  $acc_txn_amount    = parseAmount($value["credit"]); 
				  $acc_txn_drcr      = 2;
				  $acc_dr_txn_amount = 0;				  
				  $acc_cr_txn_total_amount +=$acc_cr_txn_amount;
				}
				else{
				  $acc_dr_txn_amount = parseAmount($value["debit"]);
				  $acc_txn_amount    = parseAmount($value["debit"]); 
				  $acc_txn_drcr      = 1;
				  $acc_cr_txn_amount = 0;				  
				  $acc_dr_txn_total_amount +=$acc_dr_txn_amount;				 				  
				}
				
				$insert_data  = [
				  'cmp_id'            => $this->company_id,
				  'acc_id'            => $value['account_id'],
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => $acc_txn_drcr,
				  'acc_txn_amt'       => $acc_txn_amount, 
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $txn_id,
				  'hobo_id'           => ($VchOthrBo==0)?$this->bo_id:$VchOthrBo,
				  'acc_txn_type'      => ($oCheck==1)?2:1
				];  				
				
				$this->VouchersModel->add_acc_txn_data($insert_data);				
				$register_insert_data  = [
				  'acct_vch_type'     => $voucher_type_id,
				  'cmp_id'            => $this->company_id,
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $txn_id,
				  'vch_date'          => $voucher_date,				 
				  'acc_id'            => $value['account_id'],
				  'acc_txn_cr_amt'    => $acc_cr_txn_amount, 
				  'acc_txn_dr_amt'    => $acc_dr_txn_amount, 
				  'vch_narr'          => $value['description'],
				  'hobo_id'           => ($VchOthrBo==0) ? $this->bo_id:$VchOthrBo
				]; 								
				$this->VouchersModel->add_register_txn_data($register_insert_data);				
				$narration            = $value['description'];
				$this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'short',$narration);
			   }
            }
			if ($first_account_id && $acc_cr_txn_total_amount > 0) {
				$long_register_insert = [
					'acct_vch_type'   => $voucher_type_id,
					'cmp_id'          => $this->company_id,
					'vch_txn_id'      => $voucher_txn_id,
					'txn_id'          => NULL,
					'vch_date'        => $voucher_date,					
					'acc_id'          => $first_account_id,
					'acc_txn_cr_amt'  => $acc_cr_txn_total_amount,
					'acc_txn_dr_amt'  => 0,
					'vch_narr'        => $long_narration ?? '',
					'hobo_id'         => ($VchOthrBo == 0) ? $this->bo_id : $VchOthrBo
				];
				$this->VouchersModel->add_register_txn_data($long_register_insert);				
			}
			
          }
			
		$insert_data = [
			"cmp_id"         => $this->company_id,
			"vch_txn_id"     => $voucher_txn_id,
			"vch_series_id"  => $voucher_series,
			"master_id"      => 0,
			'master_id_type' => 'nrr'
		  ];
        $txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
        $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_narration);
      
       if($FromSaleVch==1){
		 //$SaleVchTxnId; 
		 $insert_data = [
					'acct_crs_id_type'		=> '',
					'acct_txn_id'           => $SaleVchTxnId,
					'comp_id'				=> $this->company_id,
					'txn_id'				=> 0,
					'voucher_txn_id'		=> $SaleVchTxnId,
					'bo_id'					=> $this->session->get('ses_boid'),
					'acc_cross_ref_type'	=> 'RCPTVCH',
					'acc_cross_ref_data'	=> $voucher_txn_id,
					'acc_cross_logdate'   	=> $voucher_date,
				    ];					
				$this->TransactionModel->add_acc_crsref_data($insert_data); 
	     }
	    if($FromPurchaseVch==1){
		 //$SaleVchTxnId; 
		 $insert_data = [
					'acct_crs_id_type'		=> '',
					'acct_txn_id'           => $PurchaseVchTxnId,
					'comp_id'				=> $this->company_id,
					'txn_id'				=> 0,
					'voucher_txn_id'		=> $PurchaseVchTxnId,
					'bo_id'					=> $this->session->get('ses_boid'),
					'acc_cross_ref_type'	=> 'PYMTVCH',
					'acc_cross_ref_data'	=> $voucher_txn_id,
					'acc_cross_logdate'   	=> $voucher_date,
				    ];					
				$this->TransactionModel->add_acc_crsref_data($insert_data); 
	      } 

	  $this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
	  $this->VouchersModel->UpdateConso($voucher_txn_id,["draft_vch_rec_id"=>0]);
	  });
       return $this->response->setJSON($trans_result);
    }
  }
  
  public function draft_edit($voucher_txn_id,$acc_id = 0){   
    $uuid               = $this->session->get('uuid');
	$voucher_type_array = [1,5,9,13];
	
	/**********  Get Voucher Details From Draft postgr_db  *************/
	$voucher_info       = $this->VouchersModel->GetDrftVoucher($voucher_txn_id);
    if(empty($voucher_info)){
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }	
	
	$voucher_tag             ='';
    $voucher_type_id         = $voucher_info['vch_type_id']; 
	$long_narration          = $voucher_info['long_narr'];	
    $vch_subtype_id          = 0;
	$data['vch_subtype_id']  = $vch_subtype_id;
	$data['draft_vch_rec_id']  = $voucher_txn_id;
	$data['voucher_type_id'] = $voucher_type_id;
	$data['party_dropdown']  = $this->VouchersModel->party_dropdown();    
	$data['allbos']          = $this->VouchersModel->all_mig_bo_lists();
	/**************  Duplicate Voucher Code ***************/
	if(isset($_GET['duplc']) && $_GET['duplc']=='1'){
				$data['duplc'] 		     = 1;
				$page_label              = 'Duplicate';							
	 }else{
				 $data['duplc'] 	 = 0;
                 $page_label         = 'Update';  			    		  
	}
			
	$data['page_label'] = $page_label;	
	
     if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
       //echo "<pre>";print_r($_POST);exit;	
	  
	   $FromSaleVch        = $this->request->getVar('FromSaleVch');// Auto Recipt Voucher From Sale Voucher
	   $SaleVchTxnId       = $this->request->getVar('SaleVchTxnId');
	   $paymentvia_txn     = $this->request->getVar('paymentvia_txn'); // in classic mode 
	   if(!$FromSaleVch)
		  $FromSaleVch =0;	  
	   $FromPurchaseVch    = $this->request->getVar('FromPurchaseVch');// Auto Recipt Voucher From Sale Voucher
	   $PurchaseVchTxnId   = $this->request->getVar('PurchaseVchTxnId');
	   $btnid              = $this->request->getVar('btnid');// submitbtn default save button
	   $draft_vch_rec_id   = $this->request->getVar('draft_vch_rec_id');
	   if(!$FromPurchaseVch)
		  $FromPurchaseVch =0;
	  
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
		$isdplc_vch =  $this->request->getVar('isdplc_vch');
		if($isdplc_vch==1){	
			if($vch_subtype_id == 0)	
                echo $this->invoice($voucher_type_id);			
			else 
			   echo $this->item();
			die();
		}
		$long_narration     = $this->request->getVar('narration');
        $voucherdata        = $this->request->getVar('voucherdata');
        $voucher_data       = json_decode($voucherdata,true);
        $voucher_date       = $this->request->getVar('voucher_date');
        $voucher_series     = $this->request->getVar('voucher_series');
		$oCheck             = $this->request->getVar('oCheck'); // OPTIONAL VOUCHER TAG
	  
        $currency_id        = $this->request->getVar('currency_id');
        $fcy_forex_rate     = $this->request->getVar('fcy_forex_rate');
        $ccdata             = [];
        if(!empty($this->request->getVar('ccdata')))
          $ccdata = json_decode($this->request->getVar('ccdata'),true);

        $bbbdata = [];
        if(!empty($this->request->getVar('bbbdata')))
          $bbbdata = json_decode($this->request->getVar('bbbdata'),true);

        $prdata = [];
        if(!empty($this->request->getVar('prdata')))
       
	  $prdata       = json_decode($this->request->getVar('prdata'),true);
	  $voucher_date = validate_date_by_fy($voucher_date);
	  
	  /**********  Save data to postgr_db as a draft  **************/
	   $isoptional =FALSE;
		if($oCheck==1)
			$isoptional =TRUE; 
	    $total_debit   = 0;
		$credit_acc_id = 0;
		$total_fcy_debit   = 0;

		foreach ($voucher_data as $row) {
			if ($row['drcr'] === 'C') {
				$credit_acc_id = $row['account_id'];
			}
			if ($row['drcr'] === 'D') {
				$total_debit += $row['debit'];
				$total_fcy_debit += $row['debitfc'];
			}
		}
		$vch_particulars='';		
		if($credit_acc_id){
			$acc_info = $this->AccountsModel->account_info($credit_acc_id);
			$vch_particulars = $acc_info['acc_name']; 
		}
		
		if($draft_vch_rec_id>0){
	     $pgrdata_drft    = array("log_date_time"=>$voucher_date,
					             "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($total_debit),
								 "vch_particulars"=>$vch_particulars,"long_narr"=>$long_narration,
								 "vch_series_id"=>$voucher_series,"isoptional"=>$isoptional,
								 "vch_fcy_amt"=>parseAmount($total_fcy_debit),
								 "vch_fcy_rate"=>$fcy_forex_rate,"vch_fcy_id"=>$currency_id

					            ); 
		 $this->VouchersModel->UpdateDrftVoucher($draft_vch_rec_id,$pgrdata_drft);
		}else{		
		 $pgrdata_drft    = array("cmp_id"=>$this->company_id,"hobo_id"=>$this->bo_id,"long_narr"=>$long_narration,"vch_series_id"=>$voucher_series,
		                         "log_date_time"=>$voucher_date,"vch_type_id"=>$voucher_type_id,
					             "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($total_debit),
								 "vch_particulars"=>$vch_particulars,"uuid_aictly"=>$this->session->get('uuid'),
								 "isoptional"=>$isoptional,"vch_fcy_amt"=>parseAmount($total_fcy_debit),
								 "vch_fcy_rate"=>$fcy_forex_rate,"vch_fcy_id"=>$currency_id
					            ); 
		 $draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);	
		}
		
        /********************************************************************/
		if($btnid=='submitbtn_drft'){
		   return json_encode(['status' => true, 'message' => 'Voucher saved in draft mode']); 
	    }
		
	  }
	 }
  
    if(in_array($voucher_type_id, $voucher_type_array) && $vch_subtype_id == 0){
		$account_transactions =  json_decode($voucher_info['vch_json_data'],true);
         $data['account_transactions'] = $account_transactions; 
		 
		 if(in_array(4, array_column($data['account_transactions'], 'vch_txn_type'))) {
			$data['pending_vch'] = 1;			
		}else
			$data['pending_vch'] = 0;
		if(in_array(5, array_column($data['account_transactions'], 'vch_txn_type'))) {
			$data['rejected_vch'] = 1;
		}else
			$data['rejected_vch'] = 0;  
		
		if(($data['pending_vch']==1 || $data['rejected_vch']==1)){
			$is_voucher_editable="false";
		}
		else 
			$is_voucher_editable="true";	
        //$data['account_transactions'] = $this->VouchersModel->get_all_account_transactions($voucher_txn_id);
        $data['cc_data']  = [];//$this->TransactionModel->get_cc_txn_data($voucher_txn_id);
        $data['bbb_data'] = [];//$this->TransactionModel->get_bills_txn_data($voucher_txn_id);
        $data['pr_data']  = [];//$this->TransactionModel->get_pr_txn_data($voucher_txn_id);
        $data['oCheck']   = 0;
		 $data['is_voucher_editable']   = $is_voucher_editable;
       
      $voucher_detail     = $this->VouchersModel->get_vouchertype_info($voucher_type_id);
      $data['migrate_voucher_txn_id'] = 0; 	 
	  $data['migrate_voucher_txn_id'] = 0; 
	  $data['migrate_account_transactions'] = [];
	  $data['migrate_cc_data'] = [];
	  $data['migrate_bbb_data'] = [];
	  $data['migrate_pr_data'] = [];
      $data['sblgr_data']  = $this->VouchersModel->get_sblgr_txn_data($voucher_txn_id);	  
	 
	  $data['data_groups']              = $this->VouchersModel->ajax_receipt_accounts_list();
      $data['message_output']           = $this->message_output;
	  $data['voucher_txn_id']           = $voucher_txn_id;
      $data['base_url']                 = $this->base_url; 
	  $data['voucher_tag']              = $voucher_tag; 
      $data['folder_path']              = $this->folder_path; 
      $data['voucher_name']             = $voucher_detail['vch_name'];
      $data['voucher_type_id']          = $voucher_type_id;
      $data['voucher_series_dropdown']  = $this->VouchersModel->comp_voucher_series($this->company_id,$voucher_type_id);
      $data['narration']                = $long_narration;
      $data['voucher_series']           = $voucher_info['vch_series_id'];     
      $data['voucher_date']             = date('d-m-Y', strtotime($voucher_info['log_date_time']));      
      $data['bills_method_list']        = ['','New Ref.','Adjustment'];

  	  $accounts_list    = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
	  $bsd_accounts     = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
	
     if($accounts_list!='' &&  $bsd_accounts!='')
        $company_all_acc_bsd    = array_merge(json_decode($accounts_list,true),json_decode($bsd_accounts,true));
	 else
	    $company_all_acc_bsd      = json_decode($accounts_list,true);	
      $data['acc_bsd_json_file']  = json_encode($company_all_acc_bsd);
      
	  $data['currency_list'] = $this->VouchersModel->get_currency_list();
	  $data['currency_id']     = $voucher_info['vch_fcy_id'] ?? 1;
	  // Get Forex Currency Rate		
	  $data['forexcrncy_rate'] = $voucher_info['vch_fcy_rate'] ?? 1;
      $data['bo_gstin_type']  = $this->session->get('bo_gstin_type');
	 
      $data['get_default_template'] =0;
	  
	  $format = isset($_GET['format']) ? $_GET['format'] : 1;
	  $data['format'] =$format;
	
	
	if($data['voucher_type_id'] == 9){
      if($format == 1)
      return view($this->folder_path.'vouchers/edit_classic_invoice_payment',$data);   
      if($format == 2)
      return view($this->folder_path.'vouchers/edit_invoice',$data); 

    }else if( $data['voucher_type_id'] == 13){
      if($format == 1)
      return view($this->folder_path.'vouchers/edit_classic_invoice_receipt',$data);   
      if($format == 2)
      return view($this->folder_path.'vouchers/edit_invoice',$data);  
    }else
      return view($this->folder_path.'vouchers/edit_invoice',$data);
    }
    
	if($voucher_type_id==22 ){
	  
        $data['account_transactions'] = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
        $data['cc_data'] = $this->TransactionModel->get_cc_txn_data($voucher_txn_id);
        $data['bbb_data'] = $this->TransactionModel->get_bills_txn_data($voucher_txn_id);
        $data['pr_data'] = $this->TransactionModel->get_pr_txn_data($voucher_txn_id);
        $data['oCheck'] = 0;
       // echo "<pre>";print_r($data['cc_data']);exit;
      $voucher_detail     = $this->TransactionModel->get_voucher_info($voucher_type_id);
      $get_narration_info = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);


      $data['message_output']           = $this->message_output;
	  $data['voucher_txn_id']           = $voucher_txn_id;
      $data['base_url']                 = $this->base_url; 
      $data['folder_path']              = $this->folder_path; 
      $data['voucher_name']             = $voucher_detail['comp_vch_type'];
      $data['voucher_type_id']          = $voucher_type_id;      
      $data['voucher_no']               = $this->TransactionModel->get_voucher_no($voucher_type_id);     
      $data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
      $data['narration']                = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['voucher_series']           = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']               = $voucher_info['comp_vch_no'];
      $data['voucher_date']             = date('d-m-Y', strtotime($voucher_info['voucher_date']));
      $data['voucher_txn_id']           = $voucher_txn_id;
      $data['bills_method_list']        = ['','New Ref.','Adjustment'];
	  $acc_file_name    = 'acc'.$this->session->get('ses_comp_fy_id').'.json';
      $bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';
      $accounts_list = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$acc_file_name),true);
      $bsd_accounts  = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name),true);

      $company_all_acc_bsd  = array_merge($accounts_list,$bsd_accounts);
      $data['acc_bsd_json_file']  = json_encode($company_all_acc_bsd);
    

      $data['currency_list'] = $this->TransactionModel->get_currency_list();
      $data['currency_id'] = $voucher_info['currency_id'];

      // echo "<pre>";print_r($data);exit;
      return view($this->folder_path.'vouchers/edit_gstpaid_invoice',$data);
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
      $data['pr_data'] = $this->TransactionModel->get_pr_txn_data($voucher_txn_id);
      // ECHO "<pre>";print_r($data['pr_data']);exit;

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
	  
	  $item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';		
      $acc_file_name    = 'acc'.$this->session->get('ses_comp_fy_id').'.json';
      $bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';

      $data['item_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);

      $accounts_list = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$acc_file_name),true);
      $bsd_accounts  = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name),true);

      $company_all_acc_bsd  = array_merge($accounts_list,$bsd_accounts);
      $data['acc_bsd_json_file']        = json_encode($company_all_acc_bsd);

      $data['currency_list'] = $this->TransactionModel->get_currency_list();
      $data['currency_id'] = $voucher_info['currency_id'];
	  $data['bo_gstin_type']                 = $this->session->get('bo_gstin_type');

      return view($this->folder_path.'vouchers/edit_item',$data);
    }
  }

  public function delete_draft($voucher_txn_id){
	
		/* $voucher_info       = $this->VouchersModel->GetDrftVoucher($voucher_txn_id);
		if(empty($voucher_info)){
		  return $this->response->setJSON(["status"=>false,"message"=>"Voucher does not exists!"]);
		}	 */
	     $this->VouchersModel->RemoveDrftVoucher($voucher_txn_id); 		   		  
		 $trans_result=[];
		
		  $trans_result['message'] ='Voucher Deleted';
		  $trans_result['status']  = true;
       return $this->response->setJSON($trans_result);			
  }
  
  public function delete($vch_type_id,$voucher_txn_id){
	$trans_result = $this->runTransaction(function($db) use ($voucher_txn_id)
		{
		 $voucher_info = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
		 if(!empty($voucher_info)){		
		   if($voucher_info['vch_type_id']==22)
				return json_encode(['status' => false, 'message' => 'THIS IS A SYSTEM GENERATED A/C']);
		  
		  
		  $voucher_date = $voucher_info['vch_date'];
		  /************ Save Voucher Activity Log **********/
		  // Mark Dirty Transactions
		    $voucherDate = new \DateTime($voucher_date);
			$today       = new \DateTime('today');
           	 if($voucherDate < $today){ 
				 // Call your dirty recalculation function
				// $this->VouchersModel->markSnapshotDirty($voucher_date);
			 }
		  
		  
	    $voucher_messages = [
			9  => "Payment voucher has been removed by [USERNAME]([UUID])",
			13 => "Receipt voucher has been removed by [USERNAME]([UUID])",
			1  => "Contra voucher has been removed by [USERNAME]([UUID])",
			5  => "Journal voucher has been removed by [USERNAME]([UUID])",
			8  => "Memorandum voucher has been removed by [USERNAME]([UUID])"
		];
		$erp_activity_log = $voucher_messages[$voucher_info['vch_type_id']] ?? "Voucher has been removed by [USERNAME]([UUID])";
		$this->VouchersModel->SaveUserActivity($erp_activity_log,$voucher_txn_id);
		  
		  
		  $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'accttxnmst');
		  $this->VouchersModel->clear_table_narrations_data($voucher_txn_id);
		  $this->VouchersModel->clear_register_txn_data($voucher_txn_id,$voucher_info['vch_type_id']);		  		  
	      $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'billtxnmst');
	      $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cctxnmstnn');
	      $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'prjtxnmstn');
	      $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'subacctxnm');
		  $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'itemtxnmst');
	      $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'itmvchregn');
		  $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchfcyrate');
		  $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'batchtxnmt');
          $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cmptxnmstn');	
		  $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchtxnconso');
		  		  
		  
		  if($voucher_info['draft_vch_rec_id']>0)
		   $this->VouchersModel->RemoveDrftVoucher($voucher_info['draft_vch_rec_id']); 		   		  
		  }	
		  
		
		});
		 if($trans_result['status'])
		  $trans_result['message'] ='Voucher Deleted';
	   
       return $this->response->setJSON($trans_result); 		
  }

  public  function view_vouchers($voucher_type_id){
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

  public function ajax_company_accounts(){
    echo  $this->VouchersModel->get_company_accounts($this->company_id);
  }  
  
  public function memorandum_edit($voucher_txn_id,$acc_id = 0){   
    $uuid               = $this->session->get('uuid');
	$voucher_type_array = [8];
	
    $voucher_info       = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
    if(empty($voucher_info)){
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }	 
	
	$voucher_tag             ='';
    $voucher_type_id         = $voucher_info['vch_type_id'];  
    $vch_subtype_id          = $voucher_info['vch_sub_type_id'];
	
	$data['vch_subtype_id']  = $vch_subtype_id;
	$data['voucher_type_id'] = $voucher_type_id;
	$data['draft_vch_rec_id'] = 0;
	$data['party_dropdown']  = $this->VouchersModel->party_dropdown();    
	$data['allbos']          = $this->VouchersModel->all_mig_bo_lists();
	
    if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
      // echo "<pre>";print_r($_POST);exit;	
	   $btnid              = $this->request->getVar('btnid');// submitbtn default save button
	   $draft_vch_rec_id   = $this->request->getVar('draft_vch_rec_id');
	   
    if(in_array($voucher_type_id, $voucher_type_array) )
      {
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
		
		$long_narration     = $this->request->getVar('narration');
        $voucherdata        = $this->request->getVar('voucherdata');
        $voucher_data       = json_decode($voucherdata,true);
        $voucher_date       = $this->request->getVar('voucher_date');
        $voucher_series     = $this->request->getVar('voucher_series');
        $currency_id        = $this->request->getVar('currency_id');
        $fcy_forex_rate     = 1;//$this->request->getVar('fcy_forex_rate');
        $voucher_date       = validate_date_by_fy($voucher_date);
	  
	  /**********  Save data to postgr_db as a draft  **************/
	    $isoptional =FALSE;
		$total_debit   = 0;
		$credit_acc_id = 0;
		foreach ($voucher_data as $row) {
			if ($row['drcr'] === 'C') {
				$credit_acc_id = $row['acc_id'];
			}
			if ($row['drcr'] === 'D') {
				$total_debit += $row['debit'];
			}
		}
		$vch_particulars='';		
		if($credit_acc_id){
			$acc_info = $this->AccountsModel->account_info($credit_acc_id);
			$vch_particulars = $acc_info['acc_name']; 
		}
		
		if($draft_vch_rec_id >0){
	     $pgrdata_drft    = array("log_date_time"=>$voucher_date,
					             "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($total_debit),
								 "vch_particulars"=>$vch_particulars,"long_narr"=>$long_narration,
								 "vch_series_id"=>$voucher_series,"isoptional"=>$isoptional
					            ); 
		 $this->VouchersModel->UpdateDrftVoucher($draft_vch_rec_id,$pgrdata_drft);
		}else{		
		 $pgrdata_drft    = array("cmp_id"=>$this->company_id,"hobo_id"=>$this->bo_id,"long_narr"=>$long_narration,"vch_series_id"=>$voucher_series,
		                         "log_date_time"=>$voucher_date,"vch_type_id"=>$voucher_type_id,
					             "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($total_debit),
								 "vch_particulars"=>$vch_particulars,"uuid_aictly"=>$this->session->get('uuid'),
								 "isoptional"=>$isoptional
					            ); 
		 $draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);
		}
        /********************************************************************/
		if($btnid=='submitbtn_drft'){
		   return json_encode(['status' => true, 'message' => 'Voucher saved in draft mode']); 
	    }
	   
	  $trans_result = $this->runTransaction(function($db) use ($draft_vch_rec_id,$voucher_data,$voucher_txn_id,$long_narration, $voucher_type_id, $voucher_series, $voucher_date)
		{
	   /* Remove Old Entries using vch_txn_id */
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cmptxnmstn');	
	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'accttxnmst');
       $this->VouchersModel->clear_table_narrations_data($voucher_txn_id);
	   $this->VouchersModel->clear_register_txn_data($voucher_txn_id,$voucher_type_id);	
	   	  	
		/******************     ***********/
		$voucher_tag  = '';
        
        $update_data  = array(
          "vch_series_id"   => $voucher_series,
          "vch_date"         => $voucher_date,
          "draft_vch_rec_id" => $draft_vch_rec_id		  
        );
        $this->VouchersModel->update_voucher_cons_data($update_data,$voucher_txn_id,$this->company_id);
        	   
        if($voucher_data){		
		    $acc_cr_txn_total_amount =0;
			$acc_dr_txn_total_amount =0;
			$first_account_id = NULL;
			foreach($voucher_data as $key => $value)
			{
			  if($value['acc_type'] == 'acc')
			  {
				 if ($first_account_id == NULL && $value['drcr'] == 'D' && $voucher_type_id==8 ) {
					$first_account_id = $value['acc_id']; 
				}
				
		
				$account_info       = $this->AccountsModel->account_info($value['acc_id']);  
				$acc_grp_id         = 0;
				if($account_info){
					$acc_grp_id = $account_info['under_crs_mst_id'];
				}
				$insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $value['acc_id'],
				  'master_id_type'  => 'acc'
				];
				
				$acc_txn_amount      = 0;
				$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);			
				if($value['drcr'] == 'C'){
				  $acc_cr_txn_amount = parseAmount($value["credit"]);
				  $acc_txn_amount    = parseAmount($value["credit"]); 
				  $acc_txn_drcr      = 2;
				  $acc_dr_txn_amount = 0;				  
				  $acc_cr_txn_total_amount +=$acc_cr_txn_amount;
				 
				  
				}
				else{
				  $acc_dr_txn_amount = parseAmount($value["debit"]);
				  $acc_txn_amount    = parseAmount($value["debit"]); 
				  $acc_txn_drcr =1;
				  $acc_cr_txn_amount=0;
				  
				  $acc_dr_txn_total_amount +=$acc_dr_txn_amount;				 
				  
				}
				$insert_data  = [
				  'cmp_id'            => $this->company_id,
				  'acc_id'            => $value['acc_id'],
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => $acc_txn_drcr,
				  'acc_txn_amt'       => $acc_txn_amount, 
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $txn_id,
				  'hobo_id'           => $this->bo_id,
				  'acc_txn_type'      => 3
				];  				
				$this->VouchersModel->add_acc_txn_data($insert_data);
				
				$register_insert_data  = [
				  'acct_vch_type'     => $voucher_type_id,
				  'cmp_id'            => $this->company_id,
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $txn_id,
				  'vch_date'          => $voucher_date,				 
				  'acc_id'            => $value['acc_id'],
				  'acc_txn_cr_amt'    => $acc_cr_txn_amount, 
				  'acc_txn_dr_amt'    => $acc_dr_txn_amount, 
				  'vch_narr'          => $value['description'],
				  'hobo_id'           => $this->bo_id,
				  'acc_txn_type'      => 3
				]; 								
				$this->VouchersModel->add_register_txn_data($register_insert_data);				
				$narration            = $value['description'];
				$this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'short',$narration);
			   }
            }
						
			if ($first_account_id && $acc_cr_txn_total_amount > 0) {
				$long_register_insert = [
					'acct_vch_type'   => $voucher_type_id,
					'cmp_id'          => $this->company_id,
					'vch_txn_id'      => $voucher_txn_id,
					'txn_id'          => NULL,
					'vch_date'        => $voucher_date,					
					'acc_id'          => $first_account_id,
					'acc_txn_cr_amt'  => $acc_cr_txn_total_amount,
					'acc_txn_dr_amt'  => 0,
					'vch_narr'        => $long_narration ?? '',
					'hobo_id'         => $this->bo_id,
					'acc_txn_type'      =>3
				];
				$this->VouchersModel->add_register_txn_data($long_register_insert);				
			}
		
         }
			
		  $insert_data = [
			"cmp_id"         => $this->company_id,
			"vch_txn_id"     => $voucher_txn_id,
			"vch_series_id"  => $voucher_series,
			"master_id"      => 0,
			'master_id_type' => 'nrr'
		  ];
		  
        $txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
        $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_narration);
        
        $this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
		$this->VouchersModel->UpdateConso($voucher_txn_id,["draft_vch_rec_id"=>0]);
	  
        });

        return $this->response->setJSON($trans_result);
      }
    }
  
    if(in_array($voucher_type_id, $voucher_type_array)){
      $data['account_transactions'] = $this->VouchersModel->get_all_account_transactions($voucher_txn_id);
      $voucher_detail     = $this->VouchersModel->get_vouchertype_info($voucher_type_id);
      $get_narration_info = $this->VouchersModel->get_voucher_long_narration($voucher_txn_id);
	  $data['message_output']           = $this->message_output;
	  $data['voucher_txn_id']           = $voucher_txn_id;
      $data['base_url']                 = $this->base_url; 
	  $data['voucher_tag']              = $voucher_tag; 
      $data['folder_path']              = $this->folder_path; 
      $data['voucher_name']             = $voucher_detail['vch_name'];
      $data['voucher_type_id']          = $voucher_type_id;
      $data['voucher_series_dropdown']  = $this->VouchersModel->comp_voucher_series($this->company_id,$voucher_type_id);
      $data['narration']                = $this->VouchersModel->get_voucher_long_narration($voucher_txn_id);
      $data['voucher_series']           = $voucher_info['vch_series_id'];     
      $data['voucher_date']             = date('d-m-Y', strtotime($voucher_info['vch_date']));      
      $data['bills_method_list']        = ['','New Ref.','Adjustment'];

  	  $accounts_list    = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
	  $company_all_acc_bsd      = json_decode($accounts_list,true);	
      $data['accounts_json_file']  = json_encode($company_all_acc_bsd);
      $data['currency_list'][] = ['comp_currency_id'=>"1",'curr_name'=>'Rupee','curr_symbol'=>'₹'];//$this->TransactionModel->get_currency_list();
      $data['currency_id']   = 1;//$voucher_info['currency_id'];
	  $data['forexcrncy_rate'] = 1;//$this->TransactionModel->get_crsref($voucher_txn_id,'FOREXRT');
      $data['bo_gstin_type']  = $this->session->get('bo_gstin_type');
	  
      return view($this->folder_path.'memorandum/edit',$data);
    }
    
  }
  
  public function getAccountBillRefs(){
	   if($this->request->getMethod() == 'POST'){
		$account_id_array = $_POST['account_id_array'];
		$data = [];
		foreach($account_id_array as $account_id){
		  $data[] = $this->VouchersModel->get_account_bill_refs($account_id);
		}
		echo json_encode(['status' => true, 'data' => $data]);
	   }
   }
   public function getItemBatch(){
        if($this->request->getMethod() == 'POST'){
            $items_id_array = $_POST['items_id_array'];
			$data = [];
            foreach($items_id_array as $rows){
				$item_id_unit_id = $rows['item_id'].'_'.$rows['item_unit_id'];
                $data[] = $this->VouchersModel->get_item_batch_list($item_id_unit_id);
            }            
         echo json_encode(['status' => true, 'data' => $data]);
        }
    }
	 
  public function getAccountSblgrRefs(){
	   if($this->request->getMethod() == 'POST'){
		$account_id_array = $_POST['account_id_array'];
		$data = [];
		foreach($account_id_array as $account_id){
		  $data[] = $this->VouchersModel->get_account_subledger_refs($account_id);
		}
		echo json_encode(['status' => true, 'data' => $data]);
	   }
   }
   
  public function getPurchaseCc(){
	   if($this->request->getMethod() == 'POST'){
		  $itmsdata = $this->request->getVar('itmsdata');  
		  $data = $this->VouchersModel->get_purchase_cc($itmsdata);
		  echo json_encode(['status' => true, 'data' => $data]); 
	   }
   } 
   
   public function getCc(){
	   if($this->request->getMethod() == 'POST'){
		  $data = $this->VouchersModel->get_cc();
		  echo json_encode(['status' => true, 'data' => $data]); 
	   }
   } 
  
  public function getPr(){
    if($this->request->getMethod() == 'POST'){
        
        $items = $this->request->getPost('item_data');
        $type = $this->request->getPost('type'); // 'SALE' or 'PURCHASE'
        
        $item_accounts = [];
        $account_map = [];
        
        if(is_array($items)) {
            foreach($items as $item) {
                
                $acc_id = '';
                $acc_name = '';
                $is_direct_account = false;
                
                // ---------------------------------------------------------
                // 1. DETECT SOURCE: Direct Account OR Item
                // ---------------------------------------------------------
                if(isset($item['account_id']) && !empty($item['account_id'])) {
                    // WITHOUT ITEMS: Direct Ledger Account
                    $is_direct_account = true;
                    $acc_id = $item['account_id'];
                    $acc_name = isset($item['account_name']) ? $item['account_name'] : '';
                } else {
                    // WITH ITEMS: Extract from Item Master fields
                    if(strtoupper($type) === 'SALE') {
                        $acc_id = isset($item['item_sales_acc']) ? $item['item_sales_acc'] : '';
                    } else {
                        $acc_id = isset($item['item_pur_acc']) ? $item['item_pur_acc'] : '';
                    }
                    
                    // Fetch Account Name if not in payload
                    if(!empty($acc_id)) {
                        $acc_info = $this->AccountsModel->account_info($acc_id);
                        $acc_name = isset($acc_info['acc_name']) ? $acc_info['acc_name'] : 'Account '.$acc_id;
                    }
                }
                
                // ---------------------------------------------------------
                // 2. GET AMOUNTS (Handles both Item & Account payload keys)
                // ---------------------------------------------------------
                $amount = isset($item['amount']) ? floatval($item['amount']) : 0;
                if ($amount == 0) {
                    // Fallback for items payload
                    $amount = isset($item['item_total_amount']) ? floatval($item['item_total_amount']) : 0;
                }
                
                $amountfc = isset($item['amountfc']) ? floatval($item['amountfc']) : 0;
                if ($amountfc == 0) {
                    // Fallback for items payload
                    $amountfc = isset($item['item_total_fcy_amount']) ? floatval($item['item_total_fcy_amount']) : 0;
                }
                
                // ---------------------------------------------------------
                // 3. DETERMINE DR/CR BASED ON TYPE & SOURCE
                // ---------------------------------------------------------
                if ($is_direct_account) {
                    // Direct Ledger in Sale = Debit (Dr)
                    // Direct Ledger in Purchase = Credit (Cr)
                    $drcr = (strtoupper($type) === 'SALE') ? 'D' : 'C';
                } else {
                    // Item Sales Account = Credit (Cr)
                    // Item Purchase Account = Debit (Dr)
                    $drcr = (strtoupper($type) === 'SALE') ? 'C' : 'D';
                }
                
                // ---------------------------------------------------------
                // 4. GROUP & PUSH
                // ---------------------------------------------------------
                if(!empty($acc_id) && $amount > 0) {
                    
                    if(isset($account_map[$acc_id])) {
                        // If same account is used twice, sum the amounts
                        $account_map[$acc_id]['amount'] += $amount;
                        $account_map[$acc_id]['amountfc'] += $amountfc;
                    } else {
                        $account_map[$acc_id] = [
                            'account_id'   => $acc_id,
                            'account_name' => $acc_name, 
                            'amount'       => $amount,
                            'amountfc'     => $amountfc,
                            'drcr'         => $drcr,
                            'acc_type'     => 'acc'
                        ];
                    }
                }
            }
            $item_accounts = array_values($account_map);
        }
        
        // 5. Fetch Project List from your existing model method
        $project_list = $this->VouchersModel->get_pr();
        
        // 6. Return exactly what JavaScript expects
        echo json_encode([
            'status'        => true, 
            'item_accounts' => $item_accounts, 
            'data'          => $project_list
        ]); 
        exit;
    }
}

}