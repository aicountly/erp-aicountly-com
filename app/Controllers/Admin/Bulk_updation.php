<?php
namespace App\Controllers\Admin;

use App\Models\Admin\BulkUpdationModel;
use App\Controllers\BaseController;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\AccountsModel;
use App\Models\Admin\BillsundryModel;
use App\Models\CommonModel;
use App\Libraries\auth_session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class Bulk_updation extends BaseController{
  function __construct(){  
	    helper(['form', 'url','text']);

	    $this->BulkUpdationModel =  new BulkUpdationModel();
		$this->VouchersModel     =  new VouchersModel();
		$this->BillsundryModel   =  new BillsundryModel();
		$this->AccountsModel     =  new AccountsModel();
		$this->auth_session      =  new auth_session();
		$this->CommonModel       =  new CommonModel();	
	    $this->auth_session->user_restrict();
	    $this->auth_session->is_company_opened();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->bo_id         = $this->session->get('ses_boid');
	    $this->fy_id         = $this->session->get('ses_comp_fy_id');
	    $this->company_id    = $this->session->get('ses_company_id');
    }
    
   public function index()
   {        $bo_id = $this->session->get('ses_boid');
			$data['base_url']           = $this->base_url;	
			$data['message_output']     = $this->message_output;
			$data['folder_path']        = $this->folder_path;			
			$data['session']            = $this->session;
			$data['bo_id']              = $bo_id; 
			$data['branch_dropdown']    = [];//$this->CommonModel->all_bo_lists();
			$data['tax_catgry_source']  = [];//$this->BulkUpdationModel->ajax_tax_category();
			
			$data['sales_acc_source']  = [];
			$data['purchase_acc_source']  = [];
			
			$data['all_sales_accounts']  = [];
			$data['all_purchase_accounts']  = [];
			
		
      return view($this->folder_path.'bulk_updation/view',$data);  
   }
   
   public function load_accounts(){
	   
	echo $this->AccountsModel->ajax_accounts_address_list(1);	
   }
   
public function UpdateAccountAddress()
{
    if (!$this->request->is('post')) {
        return redirect()->back();
    }

    try {
        $json = $this->request->getJSON(true);

        // AJAX JSON chunked request
        if (is_array($json) && isset($json['items']) && is_array($json['items'])) {
            $items       = $json['items'];
            $chunkIndex  = (int)($json['chunk_index'] ?? 1);
            $totalChunks = (int)($json['total_chunks'] ?? 1);
            $updated     = 0;

            foreach ($items as $item) {
                $didUpdate = $this->processAccountAddressRow($item);
                if ($didUpdate) {
                    $updated++;
                }
            }

            // rebuild json file only after last chunk
            if ($chunkIndex >= $totalChunks) {
                $accounts_list = $this->AccountsModel->company_all_accounts();
                CreateJsonFile($this->fy_id, $this->company_id, 'acc', $accounts_list);
            }

            return $this->response->setJSON([
                'success'      => true,
                'updated'      => $updated,
                'chunk_index'  => $chunkIndex,
                'total_chunks' => $totalChunks,
                'message'      => "Chunk {$chunkIndex} of {$totalChunks} saved successfully"
            ]);
        }

        // fallback normal form post
        $post = $this->request->getPost();

        $account_ids    = (array)($post['account_id'] ?? []);
        $account_names  = (array)($post['account_name'] ?? []);
        $addr1          = (array)($post['addr1'] ?? []);
        $addr2          = (array)($post['addr2'] ?? []);
        $city           = (array)($post['city'] ?? []);
        $pincode        = (array)($post['pincode'] ?? []);
        $state_ids      = (array)($post['state_id'] ?? []);
        $country_ids    = (array)($post['country_id'] ?? []);
        $emails         = (array)($post['email'] ?? []);
        $mobiles        = (array)($post['mobile'] ?? []);
        $wamobiles      = (array)($post['wamobile'] ?? []);
        $acc_aadhaar    = (array)($post['acc_aadhaar'] ?? []);
        $acc_pan        = (array)($post['acc_pan'] ?? []);
        $acc_tan        = (array)($post['acc_tan'] ?? []);
        $acc_it_jurisd  = (array)($post['acc_it_jurisd'] ?? []);
        $acc_gstin      = (array)($post['gstin'] ?? []);
        $acc_sac        = (array)($post['acc_sac'] ?? []);
        $acc_is_sys_acc = (array)($post['acc_is_sys_acc'] ?? []);
        $acc_is_sez     = (array)($post['acc_is_sez'] ?? []);

        $total   = count($account_ids);
        $updated = 0;

        for ($i = 0; $i < $total; $i++) {
            $item = [
                'account_id'      => $account_ids[$i] ?? '',
                'account_name'    => $account_names[$i] ?? '',
                'addr1'           => $addr1[$i] ?? '',
                'addr2'           => $addr2[$i] ?? '',
                'city'            => $city[$i] ?? '',
                'pincode'         => $pincode[$i] ?? '',
                'state_id'        => $state_ids[$i] ?? '',
                'country_id'      => $country_ids[$i] ?? '',
                'email'           => $emails[$i] ?? '',
                'mobile'          => $mobiles[$i] ?? '',
                'wamobile'        => $wamobiles[$i] ?? '',
                'acc_aadhaar'     => $acc_aadhaar[$i] ?? '',
                'acc_pan'         => $acc_pan[$i] ?? '',
                'acc_tan'         => $acc_tan[$i] ?? '',
                'acc_it_jurisd'   => $acc_it_jurisd[$i] ?? '0',
                'gstin'           => $acc_gstin[$i] ?? '',
                'acc_sac'         => $acc_sac[$i] ?? '',
                'acc_is_sys_acc'  => $acc_is_sys_acc[$i] ?? '0',
                'acc_is_sez'      => $acc_is_sez[$i] ?? '0',
            ];

            $didUpdate = $this->processAccountAddressRow($item);
            if ($didUpdate) {
                $updated++;
            }
        }

        $accounts_list = $this->AccountsModel->company_all_accounts();
        CreateJsonFile($this->fy_id, $this->company_id, 'acc', $accounts_list);

        session()->setFlashdata('success', $updated . ' record(s) updated.');
        return redirect()->back();

    } catch (\Throwable $e) {
        log_message('error', 'UpdateAccountAddress error: ' . $e->getMessage());
        log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());
        log_message('error', 'Trace: ' . $e->getTraceAsString());

        if ($this->request->isAJAX()) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
        }

        session()->setFlashdata('error', $e->getMessage());
        return redirect()->back();
    }
}

private function processAccountAddressRow(array $item): bool
{
    $accId   = trim((string)($item['account_id'] ?? ''));
    $accName = trim((string)($item['account_name'] ?? ''));

    if ($accId === '') {
        return false;
    }

    $a1   = trim((string)($item['addr1'] ?? ''));
    $a2   = trim((string)($item['addr2'] ?? ''));
    $cty  = trim((string)($item['city'] ?? ''));
    $pin  = trim((string)($item['pincode'] ?? ''));
    $st   = trim((string)($item['state_id'] ?? ''));
    $ctry = trim((string)($item['country_id'] ?? ''));
    $em   = trim((string)($item['email'] ?? ''));
    $mob  = trim((string)($item['mobile'] ?? ''));
    $wam  = trim((string)($item['wamobile'] ?? ''));

    $acc_aadhaars    = trim((string)($item['acc_aadhaar'] ?? ''));
    $acc_pans        = trim((string)($item['acc_pan'] ?? ''));
    $acc_tans        = trim((string)($item['acc_tan'] ?? ''));
    $acc_it_jurisds  = trim((string)($item['acc_it_jurisd'] ?? '0'));
    $acc_gstins      = trim((string)($item['gstin'] ?? ''));
    $acc_sacs        = trim((string)($item['acc_sac'] ?? ''));
    $acc_is_sys_accs = trim((string)($item['acc_is_sys_acc'] ?? '0'));
    $acc_is_sezs     = trim((string)($item['acc_is_sez'] ?? '0'));

    if (
        $a1 === '' && $a2 === '' && $cty === '' && $pin === '' &&
        $em === '' && $mob === '' && $wam === '' && $accName === '' &&
        $acc_aadhaars === '' && $acc_pans === '' && $acc_tans === '' &&
        $acc_gstins === '' && $acc_sacs === ''
    ) {
        return false;
    }

    $anyAddress = ($a1 !== '' || $a2 !== '' || $cty !== '' || $pin !== '');
    if ($anyAddress && ($st === '' || $ctry === '')) {
        return false;
    }

    $adrs_data = [
        'acc_name'     => $accName,
        'acc_email'    => $em,
        'acc_wamobile' => $wam,
        'acc_mobile'   => $mob,
        'acc_add1'     => $a1,
        'acc_add2'     => $a2,
        'acc_country'  => ($ctry === '') ? 0 : (int)$ctry,
        'acc_state'    => ($st === '') ? 0 : (int)$st,
        'acc_city'     => $cty,
        'acc_pin'      => ($pin === '') ? 0 : (int)$pin,
    ];

    $this->AccountsModel->update_account_adrs_info($adrs_data, $accId);

    $other_data = [
        'acc_aadhaar'    => $acc_aadhaars,
        'acc_pan'        => $acc_pans,
        'acc_tan'        => $acc_tans,
        'acc_it_jurisd'  => $acc_it_jurisds,
        'acc_gstin'      => $acc_gstins,
        'acc_sac'        => $acc_sacs,
        'acc_is_sys_acc' => $acc_is_sys_accs,
        'acc_is_sez'     => $acc_is_sezs,
    ];

    $this->AccountsModel->update_account_other_info($other_data, $accId);

    return true;
}

   public function account_address_update(){
	  $data['base_url']           = $this->base_url;	
	  $data['CountryDropdown']    = $this->CommonModel->CountryDropdown();
   	  $data['StatesDropdown']     = $this->CommonModel->StatesDropdown();
	  return view($this->folder_path.'bulk_updation/update_account_address',$data);   
   }
   
   public function sales_voucher_resave(){
			$bo_id = $this->session->get('ses_boid');
			$data['base_url']           = $this->base_url;	
			$data['message_output']     = $this->message_output;
			$data['folder_path']        = $this->folder_path;
			$data['base_url']           = $this->base_url;
			$data['session']            = $this->session;
			$data['bo_id']              = $this->bo_id; 
			$data['branch_dropdown']    = [];//$this->CommonModel->all_bo_lists();
			$data['tax_catgry_source']  = [];//$this->BulkUpdationModel->ajax_tax_category();
			$data['voucher_series_dropdown'] = $this->VouchersModel->comp_voucher_result($this->company_id,18);
			
      return view($this->folder_path.'bulk_updation/sales_voucher_resave',$data); 
   }
   
   public function purchase_voucher_resave(){
			$bo_id = $this->session->get('ses_boid');
			$data['base_url']           = $this->base_url;	
			$data['message_output']     = $this->message_output;
			$data['folder_path']        = $this->folder_path;
			$data['base_url']           = $this->base_url;
			$data['session']            = $this->session;
			$data['bo_id']              = $bo_id; 
			$data['branch_dropdown']    = $this->CommonModel->all_bo_lists();
			$data['tax_catgry_source']  = $this->BulkUpdationModel->ajax_tax_category();
		    $voucher_series_dropdown    = $this->TransactionModel->voucher_series_dropdowns(11);
			$data['voucher_series_dropdown'] = $voucher_series_dropdown;
			
      return view($this->folder_path.'bulk_updation/purchase_voucher_resave',$data);   
	   
   }
   public function ajax_sales_transactions()  {
       
       if($this->request->getMethod() == 'POST' && $this->request->isAjax()){ 
       
            $pq_curPage = (int)$_POST["pq_curpage"];
            $limit     = 100;
            $search = '';
            $pq_filter    = $this->request->getVar('pq_filter');
            if(!empty($pq_filter)){
                $pq_filter = json_decode($pq_filter);
                $search = $pq_filter->data[0]->value;
            }
			
			$main_cond = $this->request->getVar('main_cond');
			$voucher_series = $this->request->getVar('voucher_series');
			$series_cond = $this->request->getVar('series_cond');
			$billno = $this->request->getVar('billno');
			
			
			
            $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
		    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));
		    $view          = 1;		    
			
			$filter_data = array("from_date"=>$from_date,"to_date"=>$to_date,"main_cond"=>$main_cond,
			                     "voucher_series"=>$voucher_series,"series_cond"=>$series_cond,"billno"=>$billno);
            echo $this->BulkUpdationModel->load_sale_condensed($from_date, $to_date,$filter_data);
        }
      }
   public function ajax_purchase_transactions() {
            $pq_curPage = (int)$_POST["pq_curpage"];
            $limit     = 100;
            $search = '';
            $pq_filter    = $this->request->getVar('pq_filter');
            if(!empty($pq_filter)){
                $pq_filter = json_decode($pq_filter);
                $search = $pq_filter->data[0]->value;
            }
			
			$main_cond = $this->request->getVar('main_cond');
			$voucher_series = $this->request->getVar('voucher_series');
			$series_cond = $this->request->getVar('series_cond');
			$billno = $this->request->getVar('billno');
			
			
			
            $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
		    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));
		    $view          = 1;		    
			
			$filter_data = array("from_date"=>$from_date,"to_date"=>$to_date,"main_cond"=>$main_cond,
			                     "voucher_series"=>$voucher_series,"series_cond"=>$series_cond,"billno"=>$billno);
            echo $this->BulkUpdationModel->load_purchase_condensed($pq_curPage, $limit,$filter_data);
      }
   public function save_transactions(){
	   if($this->request->getMethod() == 'post'){	
		  $voucherdata            = json_decode($this->request->getVar('voucherdata'),true);
		  
		   foreach($voucherdata as $item_row){
			  $voucher_data   = array("voucher_txn_id"=>$item_row['voucher_txn_id'],
			                          "master_id"=>$item_row['master_id'],
									  "txn_id" => $item_row['txn_id'],
									  "voucher_date"=>$item_row['date'],
									  "voucher_sub_type"=>$item_row['voucher_sub_type'],
									  "from_date"=>$item_row['from_date'],"to_date"=>$item_row['to_date']);
			 $this->BulkUpdationModel->reedit_sale($voucher_data); 
		  } 
		  echo json_encode(array('status'=>1));		
		}
	  die(); 	
   }	  
   public function ajax_items(){
	   echo $response =  $this->BulkUpdationModel->ajax_items();	
   }
   
  public function ajax_accounts(){
	   echo $response =  $this->BulkUpdationModel->ajax_accounts_list();	
   }
   
  public function ajax_bsd(){
	   echo $response =  $this->BulkUpdationModel->ajax_bsd_list();	
   }
   
  public function UpdateBSDTaxCatg(){
	 if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
		$taxcatgdata = json_decode($this->request->getVar('taxcatgdata'),true);
		
		//echo'<pre>';print_r($_POST);exit;
		foreach($taxcatgdata as $tax_row){
			if(isset($tax_row['tax_cat_id']) && $tax_row['tax_cat_id'] > 0){
				$item_tax      = $tax_row['tax_cat_id'];
			    $billsundry_id = $tax_row['item_id'];
				
			if($item_tax >0){		
				  // fetch data from tax category
				  // fetch data from tax category
				 $tax_info =  $this->TransactionModel->get_tax_master_info($item_tax);
				 if($tax_info){
					     $tax_cat_act      = $tax_info['tax_cat_act'];
						 $tax_cat_name     = $tax_info['tax_cat_name'];
						 $tax_cat_section  = $tax_info['tax_cat_section'];
						 $tax_cat_basis    = $tax_info['tax_cat_basis'];
						 $tax_basis        = $tax_info['tax_basis'];
						 $tax_cat_type     = $tax_info['tax_cat_type'];						 
						 $igst_rate        = $tax_info['igst_rate'];
						 $cess_rate        = $tax_info['cess_rate'];
						 $wef              = $tax_info['wef'];
						 $tax_short_code   = $tax_info['tax_short_code']; 
						 
						 if($tax_cat_type=='gsttaxcatd'){							
							 $cmp_tax_cat_type = 'cmpgstcatn'; 	// table name						
						  }	
						 
						 // step 1 add data in cmptaxcatm table
						 $comp_tax_mst_data = array(
						                 "comp_id"=>$this->company_id,
						                 "cmp_tax_cat_act"=>$tax_cat_act,
										 "cmp_tax_cat_name"=>$tax_cat_name,
										 'cmp_tax_cat_section'=>$tax_cat_section,
										 "cmp_tax_cat_basis"=>$tax_basis,
										 "cmp_tax_cat_type"=>$cmp_tax_cat_type
										);
						$cmp_tax_cat_idw = $this->BillsundryModel->add_comp_tax_mst($comp_tax_mst_data); 
						
						//  step 2  add data in cmpgstcatn table	
						
						$comp_catg_data   = array("cmp_tax_cat_id"=>$cmp_tax_cat_idw,
												  "comp_id"=>$this->company_id,
												  "cmp_tax_cat_igst"=>$igst_rate,
												  "cmp_tax_cat_cess"=>$cess_rate,
												  "cmp_tax_cat_wef"=>date('Y-m-d',strtotime($wef)),
												  "cmp_tax_short_code"=>$tax_short_code,
												  "tax_cat_id"=>$item_tax
												  );
						$this->BillsundryModel->add_compcatg_tax_mst($cmp_tax_cat_type,$comp_catg_data); 
				     } 
				$bdstaxmstn_data = array("cmp_tax_cat_id"=>$cmp_tax_cat_idw,"tax_cat_id"=>$item_tax);
				$this->BulkUpdationModel->update_bdstaxmstn($billsundry_id,$bdstaxmstn_data); 
			  }
		   }
	    }
		$ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
		$bsd_file       = 'bsd'.$ses_comp_fy_id.'.json';
		$file           = WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file;
		if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
			file_put_contents($file, ""); 
			$accounts_list  = $this->BillsundryModel->company_all_bsd();
			$f = fopen($file, 'a');
			fwrite($f,$accounts_list);
		 }
		return json_encode(['status' => true, 'message' => '']);
      }
   }
   
   
   public function UpdateAccountOpBalBranchWise(){
	 if($this->request->getMethod() == 'post' && $this->request->isAjax()){		 
	    $accountsdata = json_decode($this->request->getVar('taxcatgdata'),true);
	 	foreach($accountsdata as $acc_row){
			if(isset($acc_row['item_id']) && $acc_row['item_id'] > 0 ){
				$item_tax          = $acc_row['tax_cat_id'];
			    $account_id        = $acc_row['item_id'];
				$branch_balances   = $acc_row['branch_balances'];
				if($branch_balances){
					foreach($branch_balances as $row){
						$branchid     = trim($row['branchid']);
						$op_bal       = trim($row['op_bal']);
						$bal_type     = trim($row['bal_type']);
						
						if($bal_type =='CR.')
						$op_branch_drcr ='cr';
						else if($bal_type =='DR.')
							$op_branch_drcr ='dr';
	
						if($bal_type =='CR.')
							$op_bal = -$op_bal;
				
						$accdata = array("acc_id"=>$account_id,"acc_op_bal"=>$op_bal,"bo_id"=>$branchid);
						$this->BulkUpdationModel->update_account_balance($accdata);
						
						$account_txn_table_name =  $this->company_id.'_accnttxnnn_'.$account_id.'_'.$this->session->get('ses_comp_fy_id');
						$this->BulkUpdationModel->update_txn_entries($account_txn_table_name, $op_bal, $op_branch_drcr);
				
				
					}
				}
				
			}
		}	  
	 }
	 return json_encode(['status' => true, 'message' => '']);
   }
   
  public function UpdateAccountTaxCatg(){
	 if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
		$taxcatgdata = json_decode($this->request->getVar('taxcatgdata'),true);
		if(is_array($taxcatgdata)){
		foreach($taxcatgdata as $tax_row){
			if(isset($tax_row['tax_cat_id']) && $tax_row['tax_cat_id'] > 0){
				$item_tax     = $tax_row['tax_cat_id'];
			    $account_id      = $tax_row['item_id'];
				
			if($item_tax >0){		
				  // fetch data from tax category
				 $tax_info =  $this->TransactionModel->get_tax_master_info($item_tax);
				 if($tax_info){
					     $tax_cat_act      = $tax_info['tax_cat_act'];
						 $tax_cat_name     = $tax_info['tax_cat_name'];
						 $tax_cat_section  = $tax_info['tax_cat_section'];
						 $tax_cat_basis    = $tax_info['tax_cat_basis'];
						 $tax_basis        = $tax_info['tax_basis'];
						 $tax_cat_type     = $tax_info['tax_cat_type'];						 
						 $igst_rate        = $tax_info['igst_rate'];
						 $cess_rate        = $tax_info['cess_rate'];
						 $wef              = $tax_info['wef'];
						 $tax_short_code   = $tax_info['tax_short_code']; 
						 
						 if($tax_cat_type=='gsttaxcatd'){							
							 $cmp_tax_cat_type = 'cmpgstcatn'; 	// table name						
						  }	
						 
						 // step 1 add data in cmptaxcatm table
						 $comp_tax_mst_data = array(
						                 "comp_id"=>$this->company_id,
						                 "cmp_tax_cat_act"=>$tax_cat_act,
										 "cmp_tax_cat_name"=>$tax_cat_name,
										 'cmp_tax_cat_section'=>$tax_cat_section,
										 "cmp_tax_cat_basis"=>$tax_basis,
										 "cmp_tax_cat_type"=>$cmp_tax_cat_type
										);
						$cmp_tax_cat_id = $this->AccountsModel->add_comp_tax_mst($comp_tax_mst_data); 
						
						//  step 2  add data in cmpgstcatn table	
						
						$comp_catg_data   = array("cmp_tax_cat_id"=>$cmp_tax_cat_id,
												  "comp_id"=>$this->company_id,
												  "cmp_tax_cat_igst"=>$igst_rate,
												  "cmp_tax_cat_cess"=>$cess_rate,
												  "cmp_tax_cat_wef"=>date('Y-m-d',strtotime($wef)),
												  "cmp_tax_short_code"=>$tax_short_code,
												  "tax_cat_id"=>$item_tax,
												  "cmp_tax_cat_cess_basis"=> 1
												  );
						$this->AccountsModel->add_compcatg_tax_mst($cmp_tax_cat_type,$comp_catg_data); 
				
				$itemtaxmst_data = array("comp_id"=>$this->company_id,"acc_id"=>$account_id,
										 "tax_cat_id"=>$item_tax,'cmp_tax_cat_id'=>$cmp_tax_cat_id,
										 "acc_tax_short_code"=>""
										);
			    $this->BulkUpdationModel->update_acctaxmstn($itemtaxmst_data);

				} 
				 
			  }
		   }
	    }
		
		$ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
		$acc_file_name    = 'acc'.$ses_comp_fy_id.'.json';		
	    $file             =  WRITEPATH.'comp'.$this->company_id.'/'.$acc_file_name;
		if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
		file_put_contents($file, ""); 
		$accounts_list  = $this->AccountsModel->company_all_accounts();
		$comp_folder    = 'comp'.$this->company_id;
		$f = fopen($file, 'a');
        fwrite($f,$accounts_list);
		  }
		return json_encode(['status' => true, 'message' => '']);
	  }
	  else
		  return json_encode(['status' => false, 'message' => '']);
      }
   }
   
   public function UpdateItemSalePurchaseAccount(){
	   if($this->request->getMethod() == 'post' && $this->request->isAjax()){
		 $taxcatgdata = json_decode($this->request->getVar('taxcatgdata'),true);
		
		 foreach($taxcatgdata as $tax_row){
			if(isset($tax_row['sale_acc_id']) && $tax_row['sale_acc_id'] > 0){
			 $item_id     = $tax_row['item_id'];
			 $sale_acc_id = $tax_row['sale_acc_id'];
			 $update_data = array("item_sales_acc"=>$sale_acc_id); 
			 $this->BulkUpdationModel->update_item($item_id,$update_data);
			}
			if(isset($tax_row['purchase_acc_id']) && $tax_row['purchase_acc_id'] > 0){
		     $item_id = $tax_row['item_id'];
			 $purchase_acc_id = $tax_row['purchase_acc_id'];
			 $update_data = array("item_pur_acc"=>$purchase_acc_id); 
			 $this->BulkUpdationModel->update_item($item_id,$update_data);
			}	
		}
		 return json_encode(['status' => true, 'message' => '']);
	   }
   }
   public function UpdateItemUPCPrintNameAlias(){
	    if($this->request->getMethod() == 'post' && $this->request->isAjax()){
		 $taxcatgdata = json_decode($this->request->getVar('taxcatgdata'),true);
		 foreach($taxcatgdata as $tax_row){
            if(isset($tax_row['item_upc']) && $tax_row['item_upc'] !=''){
			 $item_id     = $tax_row['item_id'];
			 $item_upc = $tax_row['item_upc'];
			 $update_data = array("item_upc"=>$item_upc); 
			 $this->BulkUpdationModel->update_item($item_id,$update_data);
		    }
		    if(isset($tax_row['item_printnme']) && $tax_row['item_printnme']!=''){
		     $item_id = $tax_row['item_id'];
			 $item_print = $tax_row['item_printnme'];
			 $update_data = array("item_print"=>$item_print); 
			 $this->BulkUpdationModel->update_item($item_id,$update_data);
		    }
		    if(isset($tax_row['item_aliasname']) && $tax_row['item_aliasname']!=''){
		     $item_id = $tax_row['item_id'];
			 $item_alias = $tax_row['item_aliasname'];
			 $update_data = array("item_alias"=>$item_alias); 
			 $this->BulkUpdationModel->update_item($item_id,$update_data);
		    }		
		 }
		 return json_encode(['status' => true, 'message' => '']);		 
	   }
   }
   
   public function UpdateItemDefaultValuationMethod(){
	    if($this->request->getMethod() == 'post' && $this->request->isAjax()){
		 $taxcatgdata = json_decode($this->request->getVar('taxcatgdata'),true);
		 foreach($taxcatgdata as $tax_row){
            if(isset($tax_row['item_upc']) && $tax_row['item_upc'] !=''){
			$item_id     = $tax_row['item_id'];
			$method_id   = $tax_row['method_id'];			 	 
			$update_data = array("valmethod_id"=>$method_id); 
			$this->BulkUpdationModel->update_item($item_id,$update_data);
			$this->TransactionModel->update_item_balances_only($item_id);
			$this->TransactionModel->update_item_txnval_only($item_id,$method_id);
		    }
		    		
		 }
		 return json_encode(['status' => true, 'message' => '']);		 
	   }
   }
   
   public function UpdateItemTaxCatg(){
	 if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
		$taxcatgdata = json_decode($this->request->getVar('taxcatgdata'),true);
		if(is_array($taxcatgdata)){
		foreach($taxcatgdata as $tax_row){
			if(isset($tax_row['tax_cat_id']) && $tax_row['tax_cat_id'] > 0){
				$item_tax     = $tax_row['tax_cat_id'];
			    $item_id      = $tax_row['item_id'];
				if(isset($tax_row['supplytype']))
				$item_supply_type  = $tax_row['supplytype']; 
			    else
				$item_supply_type =1;	// goods
			if($item_tax >0){
				$item_info  = $this->ItemsModel->get_item_info($item_id, $this->company_id);
				$item_hsn    = $item_info['item_upc'];
				  // fetch data from tax category
				 $tax_info =  $this->TransactionModel->get_tax_master_info($item_tax);
				 if($tax_info){
					     $tax_cat_act      = $tax_info['tax_cat_act'];
						 $tax_cat_name     = $tax_info['tax_cat_name'];
						 $tax_cat_section  = $tax_info['tax_cat_section'];
						 $tax_cat_basis    = $tax_info['tax_cat_basis'];
						 $tax_basis        = $tax_info['tax_basis'];
						 $tax_cat_type     = $tax_info['tax_cat_type'];						 
						 $igst_rate        = $tax_info['igst_rate'];
						 $cess_rate        = $tax_info['cess_rate'];
						 $wef              = $tax_info['wef'];
						 $tax_short_code   = $tax_info['tax_short_code']; 
						 
						 if($tax_cat_type=='gsttaxcatd'){							
							 $cmp_tax_cat_type = 'cmpgstcatn'; 	// table name						
						  }	
						 
						 // step 1 add data in cmptaxcatm table
						 $comp_tax_mst_data = array(
						                 "comp_id"=>$this->company_id,
						                 "cmp_tax_cat_act"=>$tax_cat_act,
										 "cmp_tax_cat_name"=>$tax_cat_name,
										 'cmp_tax_cat_section'=>$tax_cat_section,
										 "cmp_tax_cat_basis"=>$tax_basis,
										 "cmp_tax_cat_type"=>$cmp_tax_cat_type
										);
						$cmp_tax_cat_id = $this->ItemsModel->add_comp_tax_mst($comp_tax_mst_data); 
						
						//  step 2  add data in cmpgstcatn table	
						
						$comp_catg_data   = array("cmp_tax_cat_id"=>$cmp_tax_cat_id,
												  "comp_id"=>$this->company_id,
												  "cmp_tax_cat_igst"=>$igst_rate,
												  "cmp_tax_cat_cess"=>$cess_rate,
												  "cmp_tax_cat_wef"=>date('Y-m-d',strtotime($wef)),
												  "cmp_tax_short_code"=>$tax_short_code,
												  "tax_cat_id"=>$item_tax
												  );
						$this->ItemsModel->add_compcatg_tax_mst($cmp_tax_cat_type,$comp_catg_data); 
						
						
						$itemtaxmst_data = array("comp_id"=>$this->company_id,"item_id"=>$item_id,
										 "tax_cat_id"=>$item_tax,'cmp_tax_cat_id'=>$cmp_tax_cat_id,
										 "item_tax_short_code"=>""
										);
						$this->BulkUpdationModel->add_itemtaxmstn($itemtaxmst_data);
			   
						
				 } 
				$update_data = array("tax_id"=>$item_tax); 
				$this->BulkUpdationModel->update_item($item_id,$update_data);	 
			 }
			}
			
					
			
		}	
		$ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
		$item_file_name   = 'item'.$ses_comp_fy_id.'.json';
		$file  =     WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name;
		if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
		file_put_contents($file, ""); 
		$item_list  = $this->ItemsModel->company_all_items();
		$comp_folder    = 'comp'.$this->company_id;
		$f = fopen($file, 'a');
        fwrite($f,$item_list);
		 }
		return json_encode(['status' => true, 'message' => '']);
		}else
		return json_encode(['status' => false, 'message' => '']);	
	  }
   }   
   
   public function createMaster()
    {
    if($this->request->getMethod() == 'POST')
    {
      $rules = [        
        'module' => [
          'rules'  => 'required',
          'errors' => [
            'required' => 'Module is required',
          ],
        ],
        'master' => [
          'rules'  => 'required',
          'errors' => [
            'required' => 'Master is required',
          ],
        ],
        'sub_master' => [
          'rules'  => 'required',
          'errors' => [
            'required' => 'Sub Master is required',
          ],
        ],
      ];
      if(!$this->validate($rules)){
        $errors = $this->validator->getErrors();
        return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
      }
 
      $post = $this->request->getPost();
	 
/*	  if($post['module']=='Inventory' && $post['master']=='Item' && $post['sub_master']=='Tax Category')
	   {
		 $call_model="items_tax";  
	   }
	   else if($post['module']=='Inventory' && $post['master']=='Item' && $post['sub_master']=='Sale/Purchase Mapping')
	   {
		 $call_model="sale_purchase_mapping";  
	   }
	   else if($post['module']=='Inventory' && $post['master']=='Item' && $post['sub_master']=='UPC/Print Name/Alias')
	   {
		 $call_model="upc_printname_alias_mapping";  
	   }
	   else if($post['module']=='Inventory' && $post['master']=='Item' && $post['sub_master']=='Valuation Method')
	   {
		 $call_model="item_valuation_method";  
	   }
	   else if($post['module']=='Account' && $post['master']=='Account Master' && $post['sub_master']=='Tax Category')
	   {
		 $call_model="account_tax";  
	   }
	   else if($post['module']=='Account' && $post['master']=='Account Master' && $post['sub_master']=='Op. Balances')
	   {
		 $call_model="account_opbal";  
	   }
	  else  if($post['module']=='Bill Sundry' && $post['master']=='Bill Sundry Taxable Master' && $post['sub_master']=='Tax Category')
	   {
		 $call_model="bsd_tax";  
	   }
      else 
      */
      
      if($post['module']=='Vouchers' && $post['master']=='Voucher Refresh' && $post['sub_master']=='Sales Voucher')
	   {
		 $call_model="sales_voucher_resave";  
	   } 
	   else if($post['module']=='Account' && $post['master']=='Account Master' && $post['sub_master']=='Op. Balances')
	   {
		 $call_model="account_opbal";  
	   }else if($post['module']=='Account' && $post['master']=='Account Master' && $post['sub_master']=='Address')
	   {
		 $call_model="account_address";  
	   }
	  /*
	   else if($post['module']=='Vouchers' && $post['master']=='Voucher Refresh' && $post['sub_master']=='Purchase Voucher')
	   {
		 $call_model="purchase_voucher_resave";  
	   } */
      return json_encode(['status' => true, 'call_model' => $call_model,'master_title'=>$post['master'],'submaster_title'=>$post['sub_master']]);
    }
  }


}