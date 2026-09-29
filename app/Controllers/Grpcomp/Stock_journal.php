<?php
namespace App\Controllers\Admin;
use App\Models\Admin\StockjournalModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Stock_journal  extends BaseController
{
  function __construct()
    {   
	    helper(['form', 'url','text']);
		$this->StockjournalModel  = new StockjournalModel();		
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
   
	public function index()
    {
		$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
		$voucher_id         = "20";
		if($this->request->getMethod() == 'post'){
		    
		     $sale_date        = $this->request->getVar('sale_date');
		     $voucher_series   = $this->request->getVar('voucher_series'); 
		     $voucher_no       = $this->StockjournalModel->get_voucher_no($voucher_id); 
		     $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		     $narration        = $this->request->getVar('narration'); 
		     $from_itmsdata    = json_decode($this->request->getVar('itmsdatafrom'),true); 
		     $to_itmsdata      = json_decode($this->request->getVar('itmsdatato'),true); 
		   
		   
		     // add voucher consolidated entry
			 $voucher_txn_data     = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_id,
				                            "voucher_date"=>$sale_date,"mat_cent_id"=>$matrcntr_id);
			 $voucher_txn_id        = $this->StockjournalModel->add_voucher_cons_data($voucher_txn_data);
			 
			 	$this->StockjournalModel->save_voucher_narration($voucher_txn_id,0,'long',$narration);
			 	
			 	
			 $sale_total            = 0;	
			 $items_data            = array();
		     $item_sale_acounts     = array();
		     $item_sale_acounts_sum = array();
		     
		     $voucher_txn_data  = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_id,"voucher_date"=>$sale_date);
			
		     if($from_itmsdata){
		         foreach($from_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		           $get_item_info      =  $this->StockjournalModel->get_item_info($item_row['item_id']);
		           $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		           $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
		           
		           
		            // Journal From 
		           $from_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_price'],
						'item_txn_drcr'       => 'c',
						'item_txn_qty'        => $item_row['item_qty'],
						'comp_vch_series_no'  => $voucher_no,
						'item_id'             => $item_row['item_id'],
						'item_qty_bal'        => 0,
						'item_value_bal'      => 0,
						'voucher_type_id'     => $voucher_id,
                        'voucher_txn_id'      => $voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			       $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$from_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value); 
			     }
		     }
		     
		     if($to_itmsdata){
		         foreach($to_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		           $get_item_info      =  $this->StockjournalModel->get_item_info($item_row['item_id']);
		           $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		           $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
		           
			       $to_voucher_txn_data     = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_id,
				                            "voucher_date"=>$sale_date,"mat_cent_id"=>$matrcntr_id);
			       $to_voucher_txn_id        = $this->StockjournalModel->add_voucher_cons_data($to_voucher_txn_data);
			       
			       //Journal To  
			       $to_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_price'],
						'item_txn_drcr'       => 'd',
						'item_txn_qty'        => $item_row['item_qty'], 
						'comp_vch_series_no'  => $voucher_no,
						'item_id'             => $item_row['item_id'],
						'item_qty_bal'        => 0,
						'item_value_bal'      => 0,
						'voucher_type_id'     => $voucher_id,
                        'voucher_txn_id'      => $to_voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			       $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$to_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value); 
					   
		         }
		     }
		       return redirect()->to($this->base_url.'stock_journal');
		  }
		
		$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url; 
		$data['voucher_id']       = $voucher_id;
		$data['voucher_auto_no']  = $this->StockjournalModel->get_voucher_no($voucher_id);
		$data['folder_path']      = $this->folder_path;	
		$data['items_list']       = $this->StockjournalModel->items_list($this->company_id);	
		$data['units_list']       = $this->StockjournalModel->units_dropdown($this->company_id);
		$data['voucher_series_dropdown']  = $this->StockjournalModel->comp_voucher_series($this->company_id,$voucher_id);
		$data['matrcntr_dropdown']  = $this->StockjournalModel->matrcntr_dropdown($this->company_id);
		return view($this->folder_path.'stock_journal/add',$data);		
     }
     
    
	
}