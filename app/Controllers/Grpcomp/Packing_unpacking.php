<?php
namespace App\Controllers\Admin;
use App\Models\Admin\InvoiceModel;
use App\Models\Admin\TransactionModel;
use App\Models\Admin\StockjournalModel;
use App\Models\Admin\BalancesModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Packing_unpacking  extends BaseController
{
  function __construct()
    {   
	    helper(['form', 'url','text']);
		$this->InvoiceModel  = new InvoiceModel();	
		$this->TransactionModel  = new TransactionModel();	
		$this->StockjournalModel  = new StockjournalModel();
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
   
   	public function stock_journal()
    {
		$ses_comp_fy_id   = $this->session->get('ses_comp_fy_id');
		$voucher_type_id  = "20"; // voucher_type_id
		$vch_subtype_id   = 18;
		
		$voucher_detail = $this->InvoiceModel->get_voucher_info($voucher_type_id,$this->company_id);  
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
						'itmsdatafrom' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Data From is required'
						  ],
					  ],
					  'itmsdatato' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Data To is required'
						  ],
					  ],

					 ];
			
        if(!$this->validate($rules)){
        	$errors = $this->validator->getErrors();
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
        }

		     $sale_date        = $this->request->getVar('sale_date');
		     $voucher_series   = $this->request->getVar('voucher_series'); 
		     $voucher_no       = $this->StockjournalModel->get_voucher_no($voucher_type_id); 
		     $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		     $narration        = $this->request->getVar('narration'); 
		     $from_itmsdata    = json_decode($this->request->getVar('itmsdatafrom'),true); 
		     $to_itmsdata      = json_decode($this->request->getVar('itmsdatato'),true); 		   
		     $sale_date        = date("Y-m-d", strtotime($sale_date));

		     $oCheck     = $this->request->getVar('oCheck');

				if(isset($oCheck) && $oCheck == 1) // Optional Voucher
				{
						$voucher_date = $sale_date;
						$voucher_tag = 'OPTIONL';

						$voucher_date = date("Y-m-d", strtotime($voucher_date));
						$voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);

						// add voucher consolidated entry
						$insert_data  = array(
								"comp_id"               => $this->company_id,
								"comp_vch_series_id"    => $voucher_series,
								"comp_vch_no"           => $voucher_no,
								"voucher_type_id"       => $voucher_type_id,
								"voucher_date"          => $voucher_date,
								"mat_cent_id"           => $matrcntr_id,
								"vch_subtype_id"        => $vch_subtype_id,
								"voucher_tag"           => $voucher_tag,
						);
						$voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);
						

						foreach($from_itmsdata as $item_row)
						{
								$txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $item_row['item_id'],
										'master_id_type' 			=> 'ito'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								$insert_data   = array(
										'comp_id'              => $this->company_id,
										'item_oth_txn_date'    => $voucher_date,
										'item_oth_txn_amount'  => $item_row['item_total_amount'],
										'item_oth_txn_drcr'    => 'c',
										'item_oth_txn_qty'     => $item_row['item_qty'],
										'description'          => '',
										'comp_vch_series_no'   => $voucher_no,
										'item_id'              => $item_row['item_id'],
										'voucher_txn_id'       => $voucher_txn_id,
										'voucher_type_id'      => $voucher_type_id,
										'mat_cent_id'          => $matrcntr_id,
										'item_oth_txn_tag'     => $voucher_tag,
										'bo_id'			   				 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
								);
								$this->TransactionModel->add_itm_oth_data($insert_data);
								// no need to update item qty balance

						} //for loop 

						foreach($to_itmsdata as $item_row)
						{
								$txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $item_row['item_id'],
										'master_id_type' 			=> 'ito'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								$insert_data   = array(
										'comp_id'              => $this->company_id,
										'item_oth_txn_date'    => $voucher_date,
										'item_oth_txn_amount'  => $item_row['item_total_amount'],
										'item_oth_txn_drcr'    => 'd',
										'item_oth_txn_qty'     => $item_row['item_qty'],
										'description'          => '',
										'comp_vch_series_no'   => $voucher_no,
										'item_id'              => $item_row['item_id'],
										'voucher_txn_id'       => $voucher_txn_id,
										'voucher_type_id'      => $voucher_type_id,
										'mat_cent_id'          => $matrcntr_id,
										'item_oth_txn_tag'     => '',
										'bo_id'			   				 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
								);
								$this->TransactionModel->add_itm_oth_data($insert_data);
								// no need to update item qty balance

						} //for loop 

						$insert_data = [
		              "comp_id"             => $this->company_id,
		              "comp_vch_series_id"  => $voucher_series,
		              "voucher_txn_id"      => $voucher_txn_id,
		              "master_id"           => 0,
		              'master_id_type'      => 'nrr'
            		];

			    	$txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
			    	$this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

						// return redirect()->to($this->base_url.'physical_verification/add');
						return json_encode(['status' => true, 'message' => 'Voucher Inserted']);
				}
		     
		     // add voucher consolidated entry
			 $voucher_txn_data     = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_type_id, "vch_subtype_id"=>$vch_subtype_id,
				                            "voucher_date"=>$sale_date,"mat_cent_id"=>$matrcntr_id);
			 $voucher_txn_id        = $this->StockjournalModel->add_voucher_cons_data($voucher_txn_data);
			 
			 // $this->StockjournalModel->save_voucher_narration($voucher_txn_id,0,'long',$narration);
			 
			 $sale_total            = 0;	
			 $items_data            = array();
		     $item_sale_acounts     = array();
		     $item_sale_acounts_sum = array();
		     
		     $voucher_txn_data  = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_type_id,"voucher_date"=>$sale_date);
			 
		     if($from_itmsdata){
		         foreach($from_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		          
		           $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				   $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		           $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
				 }else{
					 $item_open_qty =0;
					 $item_open_value =0;
				     }
				 
				   $item_unit_id       = 	$item_row['item_unit_id'];
				 
		            // Journal From 
		           $from_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_total_amount'],
						'item_txn_drcr'       => 'c',
						'item_txn_qty'        => $item_row['item_qty'], 						
						'item_id'             => $item_row['item_id'],
						'voucher_type_id'     => $voucher_type_id,
                        'voucher_txn_id'      => $voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			       $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$from_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
			     }
		     }
		     
		     if($to_itmsdata){
		         foreach($to_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		           
		           
		          $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				        $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		                $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
    				 }else{
    					 $item_open_qty =0;
    					 $item_open_value =0;
    				 }
				   $item_unit_id       = 	$item_row['item_unit_id'];
		           
			       //Journal To  
			       $to_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_total_amount'],
						'item_txn_drcr'       => 'd',
						'item_txn_qty'        => $item_row['item_qty'], 						
						'item_id'             => $item_row['item_id'],
						'voucher_type_id'     => $voucher_type_id,
                        'voucher_txn_id'      => $voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			       $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$to_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
					   
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
		       // return redirect()->to($this->base_url.'packing_unpacking/stock_journal');
		     return json_encode(['status' => true, 'message' => 'Voucher Inserted']);
		  }
		
		$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url; 
		$data['voucher_id']       = $voucher_type_id;
		$data['voucher_auto_no']  = $this->StockjournalModel->get_voucher_no($voucher_type_id);
		$data['folder_path']      = $this->folder_path;	
		$data['items_list']       = $this->StockjournalModel->items_list($this->company_id);	
		$data['units_list']       = $this->StockjournalModel->units_dropdown($this->company_id);
		$data['voucher_series_dropdown']  = $this->StockjournalModel->comp_voucher_series($this->company_id,$voucher_type_id);
		$data['matrcntr_dropdown']  = $this->StockjournalModel->matrcntr_dropdown($this->company_id);
		$data['voucher_date']                    = $voucher_detail['last_entry'];
        $data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
       $data['voucher_type_id'] = $voucher_type_id;
		return view($this->folder_path.'packing_unpacking/stock_journal',$data);		
     } 
     
     
    public function ajax_stockjournal_register()
	 {
	     $_POST['from_date']     = date('Y-m-d',strtotime($_POST['from_date']));
		 $_POST['to_date']       = date('Y-m-d',strtotime($_POST['to_date']));
		echo $response =  $this->StockjournalModel->ajax_stockjournal_register_list();	
		
	 }  
   
   	public function modify_stock_journal($voucher_txn_id,$voucher_type_id)
   {
         if(!$voucher_type_id)
		   throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 	
	
	      $get_cons_detail   = $this->StockjournalModel->get_voucher_cons_info($voucher_txn_id,$this->company_id);	
	      
		 if(!$get_cons_detail)
		   throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

		 $oCheck = 0;
    if($get_cons_detail['voucher_tag'] == 'OPTIONL')
    	$oCheck = 1;
		

		if($voucher_type_id=='20' && $get_cons_detail['vch_subtype_id']=='20'){
		return redirect()->to($this->base_url.'packing_unpacking/modify_unpack/'.$voucher_txn_id.'/'.$voucher_type_id);				
		}
		if($voucher_type_id=='20' && $get_cons_detail['vch_subtype_id']=='19'){
		return redirect()->to($this->base_url.'packing_unpacking/modify_pack/'.$voucher_txn_id.'/'.$voucher_type_id);				
		}
		
		

		
		$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
		
		$voucher_type_id         = "20"; // voucher_type_id
		$vch_subtype_id = 18;
		
		if($oCheck){
			$transactions = ['from' => [], 'to' => []];
			$transactions_b = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
     	foreach ($transactions_b as $key => $value) {
     		if($value['item_oth_txn_drcr'] == 'c'){
     			 $transactions['from'][] = [
     			 		'item_unit_id' => $value['item_unit_id'],
              'item_unit' => $value['item_unit'],
              'item_id' => $value['item_id'],
              'postion' => 'l',
              'Lgroup_name' => 'from',
              'voucher_txn_id' => '',
              'voucher_type_id' => '',
              'material_centre' => '',
              'bill_no' => '',
              'voucher_date' => '',
              'from_txn_id' => '',
              'from_item_name' => $value['item_name'],
              'from_item_qty' => $value['item_qty'],
              'from_item_price' => $value['item_price'],
              'from_item_amount' => $value['item_amount'],
     			 ];
     		}
     		if($value['item_oth_txn_drcr'] == 'd'){
     			 $transactions['to'][] = [
     			 		'item_unit_id' => $value['item_unit_id'],
              'item_unit' => $value['item_unit'],
              'item_id' => $value['item_id'],
              'postion' => 'r',
              'Lgroup_name' => 'to',
              'voucher_txn_id' => '',
              'voucher_type_id' => '',
              'material_centre' => '',
              'bill_no' => '',
              'voucher_date' => '',
              'to_txn_id' => '',
              'to_item_name' => $value['item_name'],
              'to_item_qty' => $value['item_qty'],
              'to_item_price' => $value['item_price'],
              'to_item_amount' => $value['item_amount'],
     			 ];
     		}
     	}
		}

		else{
			$transactions       = $this->StockjournalModel->stockjournal_register_listings($voucher_txn_id,$voucher_type_id,$vch_subtype_id);
		}
	    $get_narration_info = $this->StockjournalModel->get_voucher_narration_info($voucher_txn_id,'long',0);
		if($this->request->getMethod() == 'post'){
		    
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
						'itmsdatafrom' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Data From is required'
						  ],
					  ],
					  'itmsdatato' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Data To is required'
						  ],
					  ],

					 ];
			
        if(!$this->validate($rules)){
        	$errors = $this->validator->getErrors();
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
        }
		   
		     $sale_date        = $this->request->getVar('sale_date');
		     $voucher_series   = $this->request->getVar('voucher_series'); 
		     $voucher_no       = $get_cons_detail['comp_vch_no']; 
		     $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		     $narration        = $this->request->getVar('narration'); 
		     $from_itmsdata    = json_decode($this->request->getVar('itmsdatafrom'),true); 
		     $to_itmsdata      = json_decode($this->request->getVar('itmsdatato'),true); 
		   
		     
		     $sale_date = date("Y-m-d", strtotime($sale_date));
			 
			
			  // delete items transaction
		    if(isset($transactions['from']) && count($transactions['from']) >0){
		    foreach($transactions['from'] as $trkey => $transrow_val) {	
				      $transc_txn[$trkey]  = $transrow_val['from_txn_id'];
				      $item_txn_list[$transrow_val['item_id']] = $transrow_val['from_txn_id'];
				      $sel_item_id    = $transrow_val['item_id'];
				      $tn             = $this->company_id.'_itemtxnnnn_'.$sel_item_id.'_'.$ses_comp_fy_id;
				      
				      $this->StockjournalModel->delete_item_transaction_byid($tn,$transrow_val['from_txn_id'],$this->company_id,$sel_item_id);
				  }
		      }
		  
		  if(isset($transactions['to']) && count($transactions['to']) >0){
		    foreach($transactions['to'] as $trkey => $transrow_val) {	
				      $transc_txn[$trkey]  = $transrow_val['to_txn_id'];
				      $item_txn_list[$transrow_val['item_id']] = $transrow_val['to_txn_id'];
				      $sel_item_id    = $transrow_val['item_id'];
				      $tn             = $this->company_id.'_itemtxnnnn_'.$sel_item_id.'_'.$ses_comp_fy_id;
				      
				      $this->StockjournalModel->delete_item_transaction_byid($tn,$transrow_val['to_txn_id'],$this->company_id,$sel_item_id);
				  }
		      }


		  	$this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
		    $this->TransactionModel->delete_all_narrations($voucher_txn_id);

		    $oCheck     = $this->request->getVar('oCheck');

				if(isset($oCheck) && $oCheck == 1) // Optional Voucher
				{
						$voucher_date = $sale_date;
						$voucher_tag = 'OPTIONL';

						$voucher_date = date("Y-m-d", strtotime($voucher_date));
						$voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);


						$insert_data  = array(
                    "comp_vch_series_id"        => $voucher_series,
                    "voucher_date"              => $voucher_date,
                    "voucher_tag"              	=> $voucher_tag,
                    "mat_cent_id"           		=> $matrcntr_id,
                  	"vch_subtype_id"        		=> $vch_subtype_id,
            );
            $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);
            

						foreach($from_itmsdata as $item_row)
						{
								$txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $item_row['item_id'],
										'master_id_type' 			=> 'ito'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								$insert_data   = array(
										'comp_id'              => $this->company_id,
										'item_oth_txn_date'    => $voucher_date,
										'item_oth_txn_amount'  => $item_row['item_total_amount'],
										'item_oth_txn_drcr'    => 'c',
										'item_oth_txn_qty'     => $item_row['item_qty'],
										'description'          => '',
										'comp_vch_series_no'   => $voucher_no,
										'item_id'              => $item_row['item_id'],
										'voucher_txn_id'       => $voucher_txn_id,
										'voucher_type_id'      => $voucher_type_id,
										'mat_cent_id'          => $matrcntr_id,
										'item_oth_txn_tag'     => $voucher_tag,
										'bo_id'			   				 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
								);
								$this->TransactionModel->add_itm_oth_data($insert_data);
								// no need to update item qty balance

						} //for loop 

						foreach($to_itmsdata as $item_row)
						{
								$txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $item_row['item_id'],
										'master_id_type' 			=> 'ito'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								$insert_data   = array(
										'comp_id'              => $this->company_id,
										'item_oth_txn_date'    => $voucher_date,
										'item_oth_txn_amount'  => $item_row['item_total_amount'],
										'item_oth_txn_drcr'    => 'd',
										'item_oth_txn_qty'     => $item_row['item_qty'],
										'description'          => '',
										'comp_vch_series_no'   => $voucher_no,
										'item_id'              => $item_row['item_id'],
										'voucher_txn_id'       => $voucher_txn_id,
										'voucher_type_id'      => $voucher_type_id,
										'mat_cent_id'          => $matrcntr_id,
										'item_oth_txn_tag'     => $voucher_tag,
										'bo_id'			   				 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
								);
								$this->TransactionModel->add_itm_oth_data($insert_data);
								// no need to update item qty balance

						} //for loop 

						$insert_data = [
			              "comp_id"             => $this->company_id,
			              "comp_vch_series_id"  => $voucher_series,
			              "voucher_txn_id"      => $voucher_txn_id,
			              "master_id"           => 0,
			              'master_id_type'      => 'nrr'
              		];

				    $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
				    $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

						// return redirect()->to($this->base_url.'physical_verification/add');
						return json_encode(['status' => true, 'message' => 'Voucher Updated']);
				}
		
			$voucher_txn_data  = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_type_id,"vch_subtype_id"=>$vch_subtype_id,"voucher_date"=>$sale_date, "mat_cent_id"=>$matrcntr_id, "voucher_tag" => "");
			 $this->StockjournalModel->update_voucher_cons_data($voucher_txn_id,$voucher_txn_data);

			 // $this->StockjournalModel->update_voucher_narration($voucher_txn_id,0,'long',$narration);
			 
			 $sale_total            = 0;	
			 $items_data            = array();
		     $item_sale_acounts     = array();
		     $item_sale_acounts_sum = array();
		     
		     $voucher_txn_data  = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_type_id,"voucher_date"=>$sale_date);
			
		     if($from_itmsdata){
		         foreach($from_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		          
		           $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				        $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		                $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
    				 }else{
    					 $item_open_qty =0;
    					 $item_open_value =0;
    				 }
				   $item_unit_id       = 	$item_row['item_unit_id'];
				   
		       
		           
		            // Journal From 
		           $from_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_total_amount'],
						'item_txn_drcr'       => 'c',
						'item_txn_qty'        => $item_row['item_qty'], 
						'item_id'             => $item_row['item_id'],
						'voucher_type_id'     => $voucher_type_id,
                        'voucher_txn_id'      => $voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			  
			        $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$from_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
			     }
		     }
		     
		     if($to_itmsdata){
		         foreach($to_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		          
		           $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				        $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		                $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
    				 }else{
    					 $item_open_qty =0;
    					 $item_open_value =0;
    				 }
				   $item_unit_id       = 	$item_row['item_unit_id'];
		           
			       //Journal To  
			       $to_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_total_amount'],
						'item_txn_drcr'       => 'd',
						'item_txn_qty'        => $item_row['item_qty'], 
						'item_id'             => $item_row['item_id'],
						'voucher_type_id'     => $voucher_type_id,
                        'voucher_txn_id'      => $voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			       
			        $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$to_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
					   
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

		       // return redirect()->to($this->base_url.'packing_unpacking/stock_journal');
		     return json_encode(['status' => true, 'message' => 'Voucher Updated']);
		  }
	
		 
		$data['message_output']       = $this->message_output;
		$data['get_narration_info']   = $get_narration_info;
		$data['base_url']             = $this->base_url; 
		$data['voucher_id']           = $voucher_type_id;
		$data['voucher_auto_no']      = $get_cons_detail['comp_vch_no'];
		$data['folder_path']          = $this->folder_path;	
		$data['items_list']           = $this->StockjournalModel->items_list($this->company_id);	
		$data['units_list']           = $this->StockjournalModel->units_dropdown($this->company_id);
		$data['voucher_series_dropdown']  = $this->StockjournalModel->comp_voucher_series($this->company_id,$voucher_type_id);
		$data['matrcntr_dropdown']  = $this->StockjournalModel->matrcntr_dropdown($this->company_id);
		$data['transactions']       = $transactions;
		$data['get_cons_detail']    = $get_cons_detail;
		$data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
		$data['voucher_type_id'] = $voucher_type_id;
		$data['oCheck'] = $oCheck;
		return view($this->folder_path.'packing_unpacking/modify_stock_journal',$data);		
     } 
     
	public function pack()
  {
		$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
		$voucher_type_id         = "20"; // subtype id
		$vch_subtype_id = 19;
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
						'itmsdatafrom' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Data From is required'
						  ],
					  ],
					  'itmsdatato' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Data To is required'
						  ],
					  ],

					 ];
			
        if(!$this->validate($rules)){
        	$errors = $this->validator->getErrors();
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
        }

		     $sale_date        = $this->request->getVar('sale_date');
		     $voucher_series   = $this->request->getVar('voucher_series'); 
		     $voucher_no       = $this->StockjournalModel->get_voucher_no($voucher_type_id); 
		     $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		     $narration        = $this->request->getVar('narration'); 
		     $from_itmsdata    = json_decode($this->request->getVar('itmsdatafrom'),true); 
		     $to_itmsdata      = json_decode($this->request->getVar('itmsdatato'),true); 		     
		     $sale_date        = date("Y-m-d", strtotime($sale_date));
		   
		     $oCheck     = $this->request->getVar('oCheck');

				if(isset($oCheck) && $oCheck == 1) // Optional Voucher
				{
						$voucher_date = $sale_date;
						$voucher_tag = 'OPTIONL';

						$voucher_date = date("Y-m-d", strtotime($voucher_date));
						$voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);

						// add voucher consolidated entry
						$insert_data  = array(
								"comp_id"               => $this->company_id,
								"comp_vch_series_id"    => $voucher_series,
								"comp_vch_no"           => $voucher_no,
								"voucher_type_id"       => $voucher_type_id,
								"voucher_date"          => $voucher_date,
								"mat_cent_id"           => $matrcntr_id,
								"vch_subtype_id"        => $vch_subtype_id,
								"voucher_tag"           => $voucher_tag,
						);
						$voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);
						

						foreach($from_itmsdata as $item_row)
						{
								$txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $item_row['item_id'],
										'master_id_type' 			=> 'ito'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								$insert_data   = array(
										'comp_id'              => $this->company_id,
										'item_oth_txn_date'    => $voucher_date,
										'item_oth_txn_amount'  => $item_row['item_total_amount'],
										'item_oth_txn_drcr'    => 'c',
										'item_oth_txn_qty'     => $item_row['item_qty'],
										'description'          => '',
										'comp_vch_series_no'   => $voucher_no,
										'item_id'              => $item_row['item_id'],
										'voucher_txn_id'       => $voucher_txn_id,
										'voucher_type_id'      => $voucher_type_id,
										'mat_cent_id'          => $matrcntr_id,
										'item_oth_txn_tag'     => $voucher_tag,
										'bo_id'			   				 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
								);
								$this->TransactionModel->add_itm_oth_data($insert_data);
								// no need to update item qty balance

						} //for loop 

						foreach($to_itmsdata as $item_row)
						{
								$txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $item_row['item_id'],
										'master_id_type' 			=> 'ito'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								$insert_data   = array(
										'comp_id'              => $this->company_id,
										'item_oth_txn_date'    => $voucher_date,
										'item_oth_txn_amount'  => $item_row['item_total_amount'],
										'item_oth_txn_drcr'    => 'd',
										'item_oth_txn_qty'     => $item_row['item_qty'],
										'description'          => '',
										'comp_vch_series_no'   => $voucher_no,
										'item_id'              => $item_row['item_id'],
										'voucher_txn_id'       => $voucher_txn_id,
										'voucher_type_id'      => $voucher_type_id,
										'mat_cent_id'          => $matrcntr_id,
										'item_oth_txn_tag'     => '',
										'bo_id'			   				 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
								);
								$this->TransactionModel->add_itm_oth_data($insert_data);
								// no need to update item qty balance

						} //for loop 

						$insert_data = [
			              "comp_id"             => $this->company_id,
			              "comp_vch_series_id"  => $voucher_series,
			              "voucher_txn_id"      => $voucher_txn_id,
			              "master_id"           => 0,
			              'master_id_type'      => 'nrr'
              		];

				    $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
				    $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

						// return redirect()->to($this->base_url.'physical_verification/add');
						return json_encode(['status' => true, 'message' => 'Voucher Inserted']);
				}

		   
		     // add voucher consolidated entry
			 $voucher_txn_data     = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_type_id,"vch_subtype_id"=>$vch_subtype_id,
				                            "voucher_date"=>$sale_date,"mat_cent_id"=>$matrcntr_id, 'voucher_tag' => '');
			 $voucher_txn_id       = $this->StockjournalModel->add_voucher_cons_data($voucher_txn_data);
			 
			 // $this->StockjournalModel->save_voucher_narration($voucher_txn_id,0,'long',$narration);
			 
			 $sale_total            = 0;	
			 $items_data            = array();
		     $item_sale_acounts     = array();
		     $item_sale_acounts_sum = array();
		     
		     $voucher_txn_data      = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_type_id,"voucher_date"=>$sale_date);
			
		     if($from_itmsdata){
		         foreach($from_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		            $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				   $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		           $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
				 }else{
					 $item_open_qty =0;
					 $item_open_value =0;
				 }
				  $item_unit_id       = 	$item_row['item_unit_id'];
		           
		           
		            // Journal From 
		           $from_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_total_amount'],
						'item_txn_drcr'       => 'c',
						'item_txn_qty'        => $item_row['item_qty'], 
						'item_id'             => $item_row['item_id'],
						'voucher_type_id'     => $voucher_type_id,
                        'voucher_txn_id'      => $voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			       $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$from_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
			       
			     }
		     }
		     
		     if($to_itmsdata){
		         foreach($to_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		           $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				   $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		           $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
				 }else{
					 $item_open_qty =0;
					 $item_open_value =0;
				 }
				  $item_unit_id       = 	$item_row['item_unit_id'];
		           
		          
			       //Journal To  
			       $to_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_total_amount'],
						'item_txn_drcr'       => 'd',
						'item_txn_qty'        => $item_row['item_qty'], 
						'item_id'             => $item_row['item_id'],
						'voucher_type_id'     => $voucher_type_id,
                        'voucher_txn_id'      => $voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			       $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$to_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
					   
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

		       // return redirect()->to($this->base_url.'packing_unpacking/pack');
		     return json_encode(['status' => true, 'message' => 'Voucher Inserted']);
		  }
		  
		 $voucher_detail = $this->InvoiceModel->get_voucher_info($voucher_type_id,$this->company_id);  
		  
		$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url; 
		$data['voucher_auto_no']  = $this->InvoiceModel->get_voucher_no($voucher_type_id);
		$data['folder_path']      = $this->folder_path;	
		$data['voucher_id']      = $voucher_type_id;	
		
		
		
		$data['items_list']       = $this->InvoiceModel->items_list($this->company_id);	
		$data['units_list']       = $this->InvoiceModel->units_dropdown($this->company_id);
		$data['voucher_series_dropdown']  = $this->StockjournalModel->comp_voucher_series($this->company_id,$voucher_type_id);
		$data['party_dropdown']  = $this->InvoiceModel->party_dropdown($this->company_id);
		$data['matrcntr_dropdown']  = $this->InvoiceModel->matrcntr_dropdown($this->company_id);
		
		$data['voucher_date']                    = $voucher_detail['last_entry'];
        $data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
       $data['voucher_type_id'] = $voucher_type_id;
		return view($this->folder_path.'packing_unpacking/pack',$data);		
  }
     

	public function modify_pack($voucher_txn_id,$voucher_type_id)
  {
		if(!$voucher_type_id)
		   throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 	
	
	      $get_cons_detail   = $this->StockjournalModel->get_voucher_cons_info($voucher_txn_id,$this->company_id);	
	      
		 if(!$get_cons_detail)
		   throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

		$oCheck = 0;
    if($get_cons_detail['voucher_tag'] == 'OPTIONL')
    	$oCheck = 1; 
		   
		$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
		$voucher_type_id         = "20"; // voucher_type_id
		$vch_subtype_id = 19;

		if($oCheck){
			$transactions = ['from' => [], 'to' => []];
			$transactions_b = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
     	foreach ($transactions_b as $key => $value) {
     		if($value['item_oth_txn_drcr'] == 'c'){
     			 $transactions['from'][] = [
     			 		'item_unit_id' => $value['item_unit_id'],
              'item_unit' => $value['item_unit'],
              'item_id' => $value['item_id'],
              'postion' => 'l',
              'Lgroup_name' => 'from',
              'voucher_txn_id' => '',
              'voucher_type_id' => '',
              'material_centre' => '',
              'bill_no' => '',
              'voucher_date' => '',
              'from_txn_id' => '',
              'from_item_name' => $value['item_name'],
              'from_item_qty' => $value['item_qty'],
              'from_item_price' => $value['item_price'],
              'from_item_amount' => $value['item_amount'],
     			 ];
     		}
     		if($value['item_oth_txn_drcr'] == 'd'){
     			 $transactions['to'][] = [
     			 		'item_unit_id' => $value['item_unit_id'],
              'item_unit' => $value['item_unit'],
              'item_id' => $value['item_id'],
              'postion' => 'r',
              'Lgroup_name' => 'to',
              'voucher_txn_id' => '',
              'voucher_type_id' => '',
              'material_centre' => '',
              'bill_no' => '',
              'voucher_date' => '',
              'to_txn_id' => '',
              'to_item_name' => $value['item_name'],
              'to_item_qty' => $value['item_qty'],
              'to_item_price' => $value['item_price'],
              'to_item_amount' => $value['item_amount'],
     			 ];
     		}
     	}
		}

		else{
			$transactions       = $this->StockjournalModel->stockjournal_register_listings($voucher_txn_id,$voucher_type_id,$vch_subtype_id);
		}


		// echo "<pre>";print_r($transactions);exit;

		$get_narration_info = $this->StockjournalModel->get_voucher_narration_info($voucher_txn_id,'long',0);
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
						'itmsdatafrom' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Data From is required'
						  ],
					  ],
					  'itmsdatato' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Data To is required'
						  ],
					  ],

					 ];
			
        if(!$this->validate($rules)){
        	$errors = $this->validator->getErrors();
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
        }
		   
		     $sale_date        = $this->request->getVar('sale_date');
		     $voucher_series   = $this->request->getVar('voucher_series'); 
		     $voucher_no       = $get_cons_detail['comp_vch_no']; 
		     $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		     $narration        = $this->request->getVar('narration'); 
		     $from_itmsdata    = json_decode($this->request->getVar('itmsdatafrom'),true); 
		     $to_itmsdata      = json_decode($this->request->getVar('itmsdatato'),true); 
		   
		     $sale_date = date("Y-m-d", strtotime($sale_date));

		     
			
			  // delete items transaction
		    if(isset($transactions['from']) && count($transactions['from']) >0){
		    foreach($transactions['from'] as $trkey => $transrow_val) {	
				      $transc_txn[$trkey]  = $transrow_val['from_txn_id'];
				      $item_txn_list[$transrow_val['item_id']] = $transrow_val['from_txn_id'];
				      $sel_item_id    = $transrow_val['item_id'];
				      $tn             = $this->company_id.'_itemtxnnnn_'.$sel_item_id.'_'.$ses_comp_fy_id;
				      
				      $this->StockjournalModel->delete_item_transaction_byid($tn,$transrow_val['from_txn_id'],$this->company_id,$sel_item_id);
				  }
		      }
		  
		  if(isset($transactions['to']) && count($transactions['to']) >0){
		    foreach($transactions['to'] as $trkey => $transrow_val) {	
				      $transc_txn[$trkey]  = $transrow_val['to_txn_id'];
				      $item_txn_list[$transrow_val['item_id']] = $transrow_val['to_txn_id'];
				      $sel_item_id    = $transrow_val['item_id'];
				      $tn             = $this->company_id.'_itemtxnnnn_'.$sel_item_id.'_'.$ses_comp_fy_id;
				      
				      $this->StockjournalModel->delete_item_transaction_byid($tn,$transrow_val['to_txn_id'],$this->company_id,$sel_item_id);
				  }
		      }

		    $this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
		    $this->TransactionModel->delete_all_narrations($voucher_txn_id);

		    $oCheck     = $this->request->getVar('oCheck');

				if(isset($oCheck) && $oCheck == 1) // Optional Voucher
				{
						$voucher_date = $sale_date;
						$voucher_tag = 'OPTIONL';

						$voucher_date = date("Y-m-d", strtotime($voucher_date));
						$voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);


						$insert_data  = array(
                    "comp_vch_series_id"        => $voucher_series,
                    "voucher_date"              => $voucher_date,
                    "voucher_tag"              	=> $voucher_tag,
                    "mat_cent_id"           		=> $matrcntr_id,
                  	"vch_subtype_id"        		=> $vch_subtype_id,
            );
            $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);
            

						foreach($from_itmsdata as $item_row)
						{
								$txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $item_row['item_id'],
										'master_id_type' 			=> 'ito'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								$insert_data   = array(
										'comp_id'              => $this->company_id,
										'item_oth_txn_date'    => $voucher_date,
										'item_oth_txn_amount'  => $item_row['item_total_amount'],
										'item_oth_txn_drcr'    => 'c',
										'item_oth_txn_qty'     => $item_row['item_qty'],
										'description'          => '',
										'comp_vch_series_no'   => $voucher_no,
										'item_id'              => $item_row['item_id'],
										'voucher_txn_id'       => $voucher_txn_id,
										'voucher_type_id'      => $voucher_type_id,
										'mat_cent_id'          => $matrcntr_id,
										'item_oth_txn_tag'     => $voucher_tag,
										'bo_id'			   				 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
								);
								$this->TransactionModel->add_itm_oth_data($insert_data);
								// no need to update item qty balance

						} //for loop 

						foreach($to_itmsdata as $item_row)
						{
								$txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $item_row['item_id'],
										'master_id_type' 			=> 'ito'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								$insert_data   = array(
										'comp_id'              => $this->company_id,
										'item_oth_txn_date'    => $voucher_date,
										'item_oth_txn_amount'  => $item_row['item_total_amount'],
										'item_oth_txn_drcr'    => 'd',
										'item_oth_txn_qty'     => $item_row['item_qty'],
										'description'          => '',
										'comp_vch_series_no'   => $voucher_no,
										'item_id'              => $item_row['item_id'],
										'voucher_txn_id'       => $voucher_txn_id,
										'voucher_type_id'      => $voucher_type_id,
										'mat_cent_id'          => $matrcntr_id,
										'item_oth_txn_tag'     => $voucher_tag,
										'bo_id'			   				 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
								);
								$this->TransactionModel->add_itm_oth_data($insert_data);
								// no need to update item qty balance

						} //for loop 

						$insert_data = [
			              "comp_id"             => $this->company_id,
			              "comp_vch_series_id"  => $voucher_series,
			              "voucher_txn_id"      => $voucher_txn_id,
			              "master_id"           => 0,
			              'master_id_type'      => 'nrr'
              		];

				    $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
				    $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

						// return redirect()->to($this->base_url.'physical_verification/add');
						return json_encode(['status' => true, 'message' => 'Voucher Updated']);
				}
		   
			 $voucher_txn_data  = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_type_id,"vch_subtype_id"=>$vch_subtype_id,"voucher_date"=>$sale_date, "mat_cent_id"=>$matrcntr_id, "voucher_tag" => "");
			 $this->StockjournalModel->update_voucher_cons_data($voucher_txn_id,$voucher_txn_data);
			 // $this->StockjournalModel->update_voucher_narration($voucher_txn_id,0,'long',$narration);
		
			 
			 $sale_total            = 0;	
			 $items_data            = array();
		     $item_sale_acounts     = array();
		     $item_sale_acounts_sum = array();
		     
		     $voucher_txn_data  = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_type_id,"voucher_date"=>$sale_date);
			
		     if($from_itmsdata){
		         foreach($from_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		          
		           $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				        $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		                $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
    				 }else{
    					 $item_open_qty =0;
    					 $item_open_value =0;
    				 }
				   $item_unit_id       = 	$item_row['item_unit_id'];
				   
		            // Journal From 
		           $from_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_total_amount'],
						'item_txn_drcr'       => 'c',
						'item_txn_qty'        => $item_row['item_qty'], 
						'item_id'             => $item_row['item_id'],
						'voucher_type_id'     => $voucher_type_id,
                        'voucher_txn_id'      => $voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			  
			        $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$from_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
			     }
		     }
		     
		     if($to_itmsdata){
		         foreach($to_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		          
		           $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				        $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		                $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
    				 }else{
    					 $item_open_qty =0;
    					 $item_open_value =0;
    				 }
				   $item_unit_id       = 	$item_row['item_unit_id'];
		           
			       //Journal To  
			       $to_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_total_amount'],
						'item_txn_drcr'       => 'd',
						'item_txn_qty'        => $item_row['item_qty'], 
						'item_id'             => $item_row['item_id'],
						'voucher_type_id'     => $voucher_type_id,
                        'voucher_txn_id'      => $voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			       
			        $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$to_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
					   
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

		       // return redirect()->to($this->base_url.'packing_unpacking/pack');
		     return json_encode(['status' => true, 'message' => 'Voucher Updated']);
		  }
	
		 
		$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url; 
		$data['voucher_id']       = $voucher_type_id;
		$data['voucher_auto_no']  = $get_cons_detail['comp_vch_no'];
		$data['folder_path']      = $this->folder_path;	
		$data['items_list']       = $this->StockjournalModel->items_list($this->company_id);	
		$data['units_list']       = $this->StockjournalModel->units_dropdown($this->company_id);
		$data['voucher_series_dropdown']  = $this->StockjournalModel->comp_voucher_series($this->company_id,$voucher_type_id);
		$data['matrcntr_dropdown']  = $this->StockjournalModel->matrcntr_dropdown($this->company_id);
		$data['transactions']       = $transactions;
		$data['get_cons_detail']    = $get_cons_detail;
		$data['get_narration_info'] = $get_narration_info;
		$data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
		$data['voucher_type_id'] = $voucher_type_id;
		$data['oCheck'] = $oCheck;
		return view($this->folder_path.'packing_unpacking/modify_pack',$data);		
  }
     
  public function unpack()
  {
		$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
			$voucher_type_id         = "20"; // subtype id
			$vch_subtype_id = 20;
		 $voucher_detail = $this->InvoiceModel->get_voucher_info($voucher_type_id,$this->company_id); 	
		if($this->request->getMethod() == 'post'){

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
						'itmsdatafrom' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Data From is required'
						  ],
					  ],
					  'itmsdatato' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Data To is required'
						  ],
					  ],

					 ];
			
        if(!$this->validate($rules)){
        	$errors = $this->validator->getErrors();
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
        }
		    
		     $sale_date        = $this->request->getVar('sale_date');
		     $voucher_series   = $this->request->getVar('voucher_series'); 
		     $voucher_no       = $this->StockjournalModel->get_voucher_no($voucher_type_id); 
		     $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		     $narration        = $this->request->getVar('narration'); 
		     $from_itmsdata    = json_decode($this->request->getVar('itmsdatafrom'),true); 
		     $to_itmsdata      = json_decode($this->request->getVar('itmsdatato'),true); 
		     
		     $sale_date = date("Y-m-d", strtotime($sale_date));

		     $oCheck     = $this->request->getVar('oCheck');

				if(isset($oCheck) && $oCheck == 1) // Optional Voucher
				{
						$voucher_date = $sale_date;
						$voucher_tag = 'OPTIONL';

						$voucher_date = date("Y-m-d", strtotime($voucher_date));
						$voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);

						// add voucher consolidated entry
						$insert_data  = array(
								"comp_id"               => $this->company_id,
								"comp_vch_series_id"    => $voucher_series,
								"comp_vch_no"           => $voucher_no,
								"voucher_type_id"       => $voucher_type_id,
								"voucher_date"          => $voucher_date,
								"mat_cent_id"           => $matrcntr_id,
								"vch_subtype_id"        => $vch_subtype_id,
								"voucher_tag"           => $voucher_tag,
						);
						$voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);
						

						foreach($from_itmsdata as $item_row)
						{
								$txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $item_row['item_id'],
										'master_id_type' 			=> 'ito'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								$insert_data   = array(
										'comp_id'              => $this->company_id,
										'item_oth_txn_date'    => $voucher_date,
										'item_oth_txn_amount'  => $item_row['item_total_amount'],
										'item_oth_txn_drcr'    => 'c',
										'item_oth_txn_qty'     => $item_row['item_qty'],
										'description'          => '',
										'comp_vch_series_no'   => $voucher_no,
										'item_id'              => $item_row['item_id'],
										'voucher_txn_id'       => $voucher_txn_id,
										'voucher_type_id'      => $voucher_type_id,
										'mat_cent_id'          => $matrcntr_id,
										'item_oth_txn_tag'     => $voucher_tag,
										'bo_id'			   				 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
								);
								$this->TransactionModel->add_itm_oth_data($insert_data);
								// no need to update item qty balance

						} //for loop 

						foreach($to_itmsdata as $item_row)
						{
								$txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $item_row['item_id'],
										'master_id_type' 			=> 'ito'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								$insert_data   = array(
										'comp_id'              => $this->company_id,
										'item_oth_txn_date'    => $voucher_date,
										'item_oth_txn_amount'  => $item_row['item_total_amount'],
										'item_oth_txn_drcr'    => 'd',
										'item_oth_txn_qty'     => $item_row['item_qty'],
										'description'          => '',
										'comp_vch_series_no'   => $voucher_no,
										'item_id'              => $item_row['item_id'],
										'voucher_txn_id'       => $voucher_txn_id,
										'voucher_type_id'      => $voucher_type_id,
										'mat_cent_id'          => $matrcntr_id,
										'item_oth_txn_tag'     => '',
										'bo_id'			   				 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
								);
								$this->TransactionModel->add_itm_oth_data($insert_data);
								// no need to update item qty balance

						} //for loop 

						$insert_data = [
			              "comp_id"             => $this->company_id,
			              "comp_vch_series_id"  => $voucher_series,
			              "voucher_txn_id"      => $voucher_txn_id,
			              "master_id"           => 0,
			              'master_id_type'      => 'nrr'
              		];

				    $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
				    $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

						// return redirect()->to($this->base_url.'physical_verification/add');
						return json_encode(['status' => true, 'message' => 'Voucher Inserted']);
				}
		  
		   
		     // add voucher consolidated entry
			 $voucher_txn_data     = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_type_id, "vch_subtype_id"=>$vch_subtype_id,
				                            "voucher_date"=>$sale_date,"mat_cent_id"=>$matrcntr_id);
			 $voucher_txn_id        = $this->StockjournalModel->add_voucher_cons_data($voucher_txn_data);
			 
			 $this->StockjournalModel->save_voucher_narration($voucher_txn_id,0,'long',$narration);
			 
			 $sale_total            = 0;	
			 $items_data            = array();
		     $item_sale_acounts     = array();
		     $item_sale_acounts_sum = array();
		     
		     $voucher_txn_data  = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_type_id,"voucher_date"=>$sale_date);
			
		     if($from_itmsdata){
		         foreach($from_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		          $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				   $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		           $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
				 }else{
					 $item_open_qty =0;
					 $item_open_value =0;
				 }
				  $item_unit_id       = 	$item_row['item_unit_id'];
		           
		           
		            // Journal From 
		           $from_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_total_amount'],
						'item_txn_drcr'       => 'c',
						'item_txn_qty'        => $item_row['item_qty'],
						'item_id'             => $item_row['item_id'],
						'voucher_type_id'     => $voucher_type_id,
                        'voucher_txn_id'      => $voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			       $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$from_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
			     }
		     }
		     
		     if($to_itmsdata){
		         foreach($to_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		           $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				   $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		           $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
				 }else{
					 $item_open_qty =0;
					 $item_open_value =0;
				 }
				  $item_unit_id       = 	$item_row['item_unit_id'];
		           
		          
			       //Journal To  
			       $to_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_total_amount'],
						'item_txn_drcr'       => 'd',
						'item_txn_qty'        => $item_row['item_qty'], 
						'item_id'             => $item_row['item_id'],
						'voucher_type_id'     => $voucher_type_id,
                        'voucher_txn_id'      => $voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			       $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$to_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
					   
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

		       // return redirect()->to($this->base_url.'packing_unpacking/unpack');
		     return json_encode(['status' => true, 'message' => 'Voucher Inserted']);
		  }
		  
		$data['message_output']   = $this->message_output;
		$data['voucher_id']      = $voucher_type_id;	
		$data['base_url']         = $this->base_url; 
		$data['voucher_auto_no']  = $this->InvoiceModel->get_voucher_no($voucher_type_id);
		$data['folder_path']      = $this->folder_path;	
		$data['items_list']       = $this->InvoiceModel->items_list($this->company_id);	
		$data['units_list']       = $this->InvoiceModel->units_dropdown($this->company_id);
		$data['voucher_series_dropdown']  = $this->StockjournalModel->comp_voucher_series($this->company_id,$voucher_type_id);
		$data['party_dropdown']  = $this->InvoiceModel->party_dropdown($this->company_id);
		$data['matrcntr_dropdown']  = $this->InvoiceModel->matrcntr_dropdown($this->company_id);
		
		$data['voucher_date']                    = $voucher_detail['last_entry'];
        $data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json'); 
        $data['voucher_type_id'] = $voucher_type_id;
		return view($this->folder_path.'packing_unpacking/unpack',$data);		
  }
	
  public function modify_unpack($voucher_txn_id,$voucher_type_id)
  {
		if(!$voucher_type_id)
		   throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 	
	
	      $get_cons_detail   = $this->StockjournalModel->get_voucher_cons_info($voucher_txn_id,$this->company_id);	
	      
		 if(!$get_cons_detail)
		   throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

		$oCheck = 0;
    if($get_cons_detail['voucher_tag'] == 'OPTIONL')
    	$oCheck = 1;  
		
    $get_narration_info = $this->StockjournalModel->get_voucher_narration_info($voucher_txn_id,'long',0);
		
		$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
		$voucher_type_id         = "20"; // voucher_type_id
		$vch_subtype_id = 20;
		if($oCheck){
			$transactions = ['from' => [], 'to' => []];
			$transactions_b = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
     	foreach ($transactions_b as $key => $value) {
     		if($value['item_oth_txn_drcr'] == 'c'){
     			 $transactions['from'][] = [
     			 		'item_unit_id' => $value['item_unit_id'],
              'item_unit' => $value['item_unit'],
              'item_id' => $value['item_id'],
              'postion' => 'l',
              'Lgroup_name' => 'from',
              'voucher_txn_id' => '',
              'voucher_type_id' => '',
              'material_centre' => '',
              'bill_no' => '',
              'voucher_date' => '',
              'from_txn_id' => '',
              'from_item_name' => $value['item_name'],
              'from_item_qty' => $value['item_qty'],
              'from_item_price' => $value['item_price'],
              'from_item_amount' => $value['item_amount'],
     			 ];
     		}
     		if($value['item_oth_txn_drcr'] == 'd'){
     			 $transactions['to'][] = [
     			 		'item_unit_id' => $value['item_unit_id'],
              'item_unit' => $value['item_unit'],
              'item_id' => $value['item_id'],
              'postion' => 'r',
              'Lgroup_name' => 'to',
              'voucher_txn_id' => '',
              'voucher_type_id' => '',
              'material_centre' => '',
              'bill_no' => '',
              'voucher_date' => '',
              'to_txn_id' => '',
              'to_item_name' => $value['item_name'],
              'to_item_qty' => $value['item_qty'],
              'to_item_price' => $value['item_price'],
              'to_item_amount' => $value['item_amount'],
     			 ];
     		}
     	}
		}

		else{
			$transactions       = $this->StockjournalModel->stockjournal_register_listings($voucher_txn_id,$voucher_type_id,$vch_subtype_id);
		}
		
		if($this->request->getMethod() == 'post'){

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
						'itmsdatafrom' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Data From is required'
						  ],
					  ],
					  'itmsdatato' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Data To is required'
						  ],
					  ],

					 ];
			
        if(!$this->validate($rules)){
        	$errors = $this->validator->getErrors();
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
        }
		   
		     $sale_date        = $this->request->getVar('sale_date');
		     $voucher_series   = $this->request->getVar('voucher_series'); 
		     $voucher_no       = $get_cons_detail['comp_vch_no']; 
		     $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		     $narration        = $this->request->getVar('narration'); 
		     $from_itmsdata    = json_decode($this->request->getVar('itmsdatafrom'),true); 
		     $to_itmsdata      = json_decode($this->request->getVar('itmsdatato'),true); 
		   
		   
		     $sale_date = date("Y-m-d", strtotime($sale_date));
		   
			     
			
			  // delete items transaction
		    if(isset($transactions['from']) && count($transactions['from']) >0){
		    foreach($transactions['from'] as $trkey => $transrow_val) {	
				      $transc_txn[$trkey]  = $transrow_val['from_txn_id'];
				      $item_txn_list[$transrow_val['item_id']] = $transrow_val['from_txn_id'];
				      $sel_item_id    = $transrow_val['item_id'];
				      $tn             = $this->company_id.'_itemtxnnnn_'.$sel_item_id.'_'.$ses_comp_fy_id;
				      
				      $this->StockjournalModel->delete_item_transaction_byid($tn,$transrow_val['from_txn_id'],$this->company_id,$sel_item_id);
				  }
		      }
		  
		  if(isset($transactions['to']) && count($transactions['to']) >0){
		    foreach($transactions['to'] as $trkey => $transrow_val) {	
				      $transc_txn[$trkey]  = $transrow_val['to_txn_id'];
				      $item_txn_list[$transrow_val['item_id']] = $transrow_val['to_txn_id'];
				      $sel_item_id    = $transrow_val['item_id'];
				      $tn             = $this->company_id.'_itemtxnnnn_'.$sel_item_id.'_'.$ses_comp_fy_id;
				      
				      $this->StockjournalModel->delete_item_transaction_byid($tn,$transrow_val['to_txn_id'],$this->company_id,$sel_item_id);
				  }
		      }

		  	$this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
		    $this->TransactionModel->delete_all_narrations($voucher_txn_id);

		    $oCheck     = $this->request->getVar('oCheck');

				if(isset($oCheck) && $oCheck == 1) // Optional Voucher
				{
						$voucher_date = $sale_date;
						$voucher_tag = 'OPTIONL';

						$voucher_date = date("Y-m-d", strtotime($voucher_date));
						$voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);


						$insert_data  = array(
                    "comp_vch_series_id"        => $voucher_series,
                    "voucher_date"              => $voucher_date,
                    "voucher_tag"              	=> $voucher_tag,
                    "mat_cent_id"           		=> $matrcntr_id,
                  	"vch_subtype_id"        		=> $vch_subtype_id,
            );
            $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);
            

						foreach($from_itmsdata as $item_row)
						{
								$txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $item_row['item_id'],
										'master_id_type' 			=> 'ito'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								$insert_data   = array(
										'comp_id'              => $this->company_id,
										'item_oth_txn_date'    => $voucher_date,
										'item_oth_txn_amount'  => $item_row['item_total_amount'],
										'item_oth_txn_drcr'    => 'c',
										'item_oth_txn_qty'     => $item_row['item_qty'],
										'description'          => '',
										'comp_vch_series_no'   => $voucher_no,
										'item_id'              => $item_row['item_id'],
										'voucher_txn_id'       => $voucher_txn_id,
										'voucher_type_id'      => $voucher_type_id,
										'mat_cent_id'          => $matrcntr_id,
										'item_oth_txn_tag'     => $voucher_tag,
										'bo_id'			   				 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
								);
								$this->TransactionModel->add_itm_oth_data($insert_data);
								// no need to update item qty balance

						} //for loop 

						foreach($to_itmsdata as $item_row)
						{
								$txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $item_row['item_id'],
										'master_id_type' 			=> 'ito'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								$insert_data   = array(
										'comp_id'              => $this->company_id,
										'item_oth_txn_date'    => $voucher_date,
										'item_oth_txn_amount'  => $item_row['item_total_amount'],
										'item_oth_txn_drcr'    => 'd',
										'item_oth_txn_qty'     => $item_row['item_qty'],
										'description'          => '',
										'comp_vch_series_no'   => $voucher_no,
										'item_id'              => $item_row['item_id'],
										'voucher_txn_id'       => $voucher_txn_id,
										'voucher_type_id'      => $voucher_type_id,
										'mat_cent_id'          => $matrcntr_id,
										'item_oth_txn_tag'     => $voucher_tag,
										'bo_id'			   				 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
								);
								$this->TransactionModel->add_itm_oth_data($insert_data);
								// no need to update item qty balance

						} //for loop 

						$insert_data = [
			              "comp_id"             => $this->company_id,
			              "comp_vch_series_id"  => $voucher_series,
			              "voucher_txn_id"      => $voucher_txn_id,
			              "master_id"           => 0,
			              'master_id_type'      => 'nrr'
              		];

				    $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
				    $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

						// return redirect()->to($this->base_url.'physical_verification/add');
						return json_encode(['status' => true, 'message' => 'Voucher Updated']);
				}
		
				$voucher_txn_data  = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_type_id,"vch_subtype_id"=>$vch_subtype_id,"voucher_date"=>$sale_date, "mat_cent_id"=>$matrcntr_id, "voucher_tag" => "");
			 	$this->StockjournalModel->update_voucher_cons_data($voucher_txn_id,$voucher_txn_data);
			 
			 	// $this->StockjournalModel->update_voucher_narration($voucher_txn_id,0,'long',$narration);
			 
			 $sale_total            = 0;	
			 $items_data            = array();
		     $item_sale_acounts     = array();
		     $item_sale_acounts_sum = array();
		     
		     $voucher_txn_data  = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_type_id,"voucher_date"=>$sale_date);
			
		     if($from_itmsdata){
		         foreach($from_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		          
		           $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				        $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		                $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
    				 }else{
    					 $item_open_qty =0;
    					 $item_open_value =0;
    				 }
				   $item_unit_id       = 	$item_row['item_unit_id'];
				   
		            // Journal From 
		           $from_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_total_amount'],
						'item_txn_drcr'       => 'c',
						'item_txn_qty'        => $item_row['item_qty'], 
						'item_id'             => $item_row['item_id'],
						'voucher_type_id'     => $voucher_type_id,
                        'voucher_txn_id'      => $voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			  
			        $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$from_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
			     }
		     }
		     
		     if($to_itmsdata){
		         foreach($to_itmsdata as $item_row){
		           $itemstxn_table     = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id;  
		           $sale_total         =  $sale_total+$item_row['item_total_amount'];
		           $item_sale_account  =  $this->StockjournalModel->item_sale_account_info($item_row['item_id']);
		          
		           $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				        $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		                $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
    				 }else{
    					 $item_open_qty =0;
    					 $item_open_value =0;
    				 }
				   $item_unit_id       = 	$item_row['item_unit_id'];
		           
			       //Journal To  
			       $to_mc_items_data   = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $sale_date,
						'item_txn_amount'     => $item_row['item_total_amount'],
						'item_txn_drcr'       => 'd',
						'item_txn_qty'        => $item_row['item_qty'], 
						'item_id'             => $item_row['item_id'],
						'voucher_type_id'     => $voucher_type_id,
                        'voucher_txn_id'      => $voucher_txn_id,
                        'mat_cent_id'         => $matrcntr_id,
					   );	
			       
			        $this->StockjournalModel->add_itemstxn_transactions($itemstxn_table,$to_mc_items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
					   
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
				    
		       // return redirect()->to($this->base_url.'packing_unpacking/unpack');
		     return json_encode(['status' => true, 'message' => 'Voucher Updated']);
		  }
	
		 
		$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url; 
		$data['voucher_id']       = $voucher_type_id;
		$data['voucher_auto_no']  = $get_cons_detail['comp_vch_no'];
		$data['folder_path']      = $this->folder_path;	
		$data['items_list']       = $this->StockjournalModel->items_list($this->company_id);	
		$data['units_list']       = $this->StockjournalModel->units_dropdown($this->company_id);
		$data['voucher_series_dropdown']  = $this->StockjournalModel->comp_voucher_series($this->company_id,$voucher_type_id);
		$data['matrcntr_dropdown']  = $this->StockjournalModel->matrcntr_dropdown($this->company_id);
		$data['transactions']       = $transactions;
		$data['get_cons_detail']    = $get_cons_detail;
		$data['get_narration_info'] = $get_narration_info;
		$data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
		$data['voucher_type_id'] = $voucher_type_id;
		$data['oCheck'] = $oCheck;
		return view($this->folder_path.'packing_unpacking/modify_unpack',$data);
		
  }
}