<?php
namespace App\Controllers\Admin;
use App\Models\Admin\PhysicalvfnModel;
use App\Models\Admin\BalancesModel;
use App\Models\Admin\TransactionModel;
use App\Models\Admin\ERPLogModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Physical_verification  extends BaseController
{
  function __construct()
    {   
	    helper(['form', 'url','text']);
		$this->PhysicalvfnModel  = new PhysicalvfnModel();	
		$this->LogModel        = new ERPLogModel();
		$this->BalancesModel  = new BalancesModel();
		$this->TransactionModel  = new TransactionModel();
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
   
   public function ajax_item_balance(){
        if(isset($_GET['itmid']) && $_GET['itmid']!=''){
            $item_id       = $_GET['itmid'];
            $unitid        = $_GET['unitid'];
            $voucher_date  = $_GET['voucher_date'];
			$mcid          = $_GET['mcid'];
            $voucher_date = date("Y-m-d", strtotime($voucher_date));
         echo $this->PhysicalvfnModel->get_item_balance($this->company_id,$item_id,$unitid,$mcid,$voucher_date);
        } 
    }
  
	public function add()
    {     $voucher_id       = "10";
         $ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
        if($this->request->getMethod() == 'post'){
        		// echo "<pre>";print_r($_POST);exit;	
             $long_narration   = $this->request->getVar('narration');
             $voucher_series   = $this->request->getVar('voucher_series'); 
		     $voucher_no       = $this->PhysicalvfnModel->get_voucher_no($voucher_id); 
		     $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		     $narration        = $this->request->getVar('narration'); 
		     $itmsdata         = json_decode($this->request->getVar('itmsdata'),true);
		     
		     if($this->request->getVar('vchrdate')!='')
		     $voucher_date     = $this->request->getVar('vchrdate');
		     else
		     $voucher_date     = $this->request->getVar('voucher_date');
		     
		     $voucher_date = date("Y-m-d", strtotime($voucher_date));

		     $oCheck     = $this->request->getVar('oCheck');

            	if(isset($oCheck) && $oCheck == 1) // Optional Voucher
            	{
            		$voucher_type_id = $voucher_id;
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
	                    "vch_subtype_id"        => 0,
	                    "voucher_tag"           => $voucher_tag,
	               );
	               $voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);
	              

	               foreach($itmsdata as $item_row)
			     {
					$txn_data = array(
						"comp_id"				=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_txn_id" 		=> $voucher_txn_id,
						"master_id" 			=> $item_row['item_id'],
						'master_id_type' 		=> 'ito'
					);
					$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

					$physical_stock = $item_row['physical_stock'];
					$book_stock     = $item_row['book_stock'];
					$stock_diff     = $item_row['stock_diff'];
					if($stock_diff >= 0){
						$item_oth_txn_drcr ='d';
						$item_oth_txn_qty = $stock_diff;
					}
					else if($stock_diff < 0){
						$item_oth_txn_drcr ='c';
						$item_oth_txn_qty = abs($stock_diff);
					}


					$insert_data   = array(
						'comp_id'              => $this->company_id,
						'item_oth_txn_date'    => $voucher_date,
						'item_oth_txn_amount'  => 0,
						'item_oth_txn_drcr'    => $item_oth_txn_drcr,
						'item_oth_txn_qty'     => $item_oth_txn_qty,
						'description'          => $item_row['short_narration'],
						'comp_vch_series_no'   => $voucher_no,
						'item_id'              => $item_row['item_id'],
						'voucher_txn_id'       => $voucher_txn_id,
						'voucher_type_id'      => $voucher_type_id,
						'mat_cent_id'          => $matrcntr_id,
						'item_oth_txn_tag'     => '',
						'bo_id'			   => 0,
						'txn_id'			   => $txn_id,
						'item_unit'            => $item_row['item_unit_id'],
					);
					$this->TransactionModel->add_itm_oth_data($insert_data);
					// no need to update item qty balance

					$item_remarks = "bookstock_".$physical_stock."_".$book_stock."_".$stock_diff;
					$this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$item_remarks);
					
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
			$voucher_cons_data = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_id,
				                        "voucher_date"=>$voucher_date,"mat_cent_id"=>$matrcntr_id, "voucher_tag" => '');
			$voucher_txn_id    = $this->PhysicalvfnModel->add_voucher_cons_data($voucher_cons_data);
			
			 
			 $log = [
		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
		            'log_date' => date('Y-m-d'),
		            'log_time' => date('H:i:s'),
		            'log_action_tags' => 'add',
		            'log_field_id' => $voucher_txn_id,
		            'log_field_name' => 'Physical Verification',
		            'log_field_type' => 'Voucher',
		        ];
		    $this->LogModel->add_log($log);
		    
			 $voucher_txn_data  = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_id,"voucher_date"=>$voucher_date, "voucher_tag" => "");
			
		     if($itmsdata){
		         foreach($itmsdata as $item_row){
		             $physical_stock = $item_row['physical_stock'];
		             $book_stock     = $item_row['book_stock'];
		             $stock_diff     = $item_row['stock_diff'];
		             if($stock_diff >= 0){
		              $item_txn_drcr ='d';
		              $show_qty_stock = $stock_diff;
		             }
		             else if($stock_diff < 0){
		             $item_txn_drcr ='c';
		              $show_qty_stock = str_replace("-",'',$stock_diff);
		             }
		             
		             $item_unit_id       = 	$item_row['item_unit_id'];
		             
		             
		             if(isset($item_row['short_narration']))
		               $short_narration = $item_row['short_narration'];
		               else
		               $short_narration = '';
		               
		          $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				   $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		           $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
				 }else{
					 $item_open_qty =0;
					 $item_open_value =0;
				 }     
		           
        			$items_data    = array(
                						'comp_id'             => $this->company_id,
                						'item_txn_date'       => $voucher_date,
                						'item_txn_amount'     => '',
                						'item_txn_drcr'       => $item_txn_drcr,
                						'item_txn_qty'        => $show_qty_stock,
                						'short_narration'     => $short_narration,
                						'item_id'             => $item_row['item_id'],
                						'voucher_type_id'     => $voucher_id,
                                        'voucher_txn_id'      => $voucher_txn_id,
                                        'mat_cent_id'         => $matrcntr_id,
                                        'item_remarks'        => "bookstock_".$book_stock."_".$show_qty_stock."_".$voucher_txn_id.",",
                                        // 'item_remarks_id'     => $voucher_txn_id.','
        					          );										
			$itemstxn_table = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id; 	   
			$itemtxnbal_id = $this->PhysicalvfnModel->add_itemstxn_transactions($itemstxn_table,$items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
		       
            $this->TransactionModel->CalculateValuation($item_row['item_id'],$item_row['item_unit_id'],$matrcntr_id,0,$itemtxnbal_id,$item_txn_drcr,$voucher_date);
	        $this->TransactionModel->CalculateValuation($item_row['item_id'],$item_row['item_unit_id'],$matrcntr_id,1,$itemtxnbal_id,$item_txn_drcr,$voucher_date);
            $this->TransactionModel->backdatevaluation_calculation($item_row['item_id'],$voucher_date);


			   }}

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
		  	  die;  
        }
        
        $voucher_detail = $this->PhysicalvfnModel->get_voucher_info($voucher_id,$this->company_id);
		$ses_comp_fy_id           = $this->session->get('ses_comp_fy_id');
		$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url; 
		$data['voucher_auto_no']  = $this->PhysicalvfnModel->get_voucher_no($voucher_id);
		$data['folder_path']      = $this->folder_path;	
		$data['voucher_series_dropdown']  = $this->PhysicalvfnModel->voucher_series_list($this->company_id,$voucher_id);
		$data['matrcntr_dropdown']       = $this->PhysicalvfnModel->matrcntr_dropdown($this->company_id);
		$data['units_list']     	 	 = $this->PhysicalvfnModel->units_dropdown();
			
		$data['voucher_date']     = $voucher_detail['last_entry'];
        $data['item_json_file']   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
        $data['bsd_json_file']    = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');	
		$data['voucher_type_id']                = $voucher_id;	
			
		return view($this->folder_path.'physical_verification/add',$data);		
     }
     
 public function remove_voucher($voucher_type_id,$ids){
   	 	if(!$ids)
		 return redirect()->to($this->base_url.'physical_verification/add');
		 $this->PhysicalvfnModel->remove_vouchers($voucher_type_id,$ids,$this->company_id);
		 // return redirect()->to($this->base_url.'physical_verification/add');
		 return json_encode(['status' => true, 'message' => 'Voucher Deleted']);
		 
	  }
     
   	public function edit($voucher_txn_id,$voucher_type_id)
    {     $voucher_id       = "10";
         $ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
         
          if(!$voucher_type_id)
		   throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 	
	      
		  $get_voucher_narration_info  = $this->PhysicalvfnModel->get_voucher_narration_info($voucher_txn_id,'long',0);
	      $get_cons_detail   = $this->PhysicalvfnModel->get_voucher_cons_info($voucher_txn_id,$this->company_id);

	      if(!$get_cons_detail)
		    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

	      $voucher_date      = $get_cons_detail['voucher_date'];
	      $oCheck = 0;
	      if($get_cons_detail['voucher_tag'] == 'OPTIONL')
	      	$oCheck = 1;
	      

	     if($oCheck){
	     	$transactions = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
	     	foreach ($transactions as $key => $value) {

	     		$phy_stock = 0;
	     		$book_stock = 0;
	     		$stock_diff = 0;
	     		$get_item_remarks  = $this->PhysicalvfnModel->get_voucher_narration_info($voucher_txn_id,'long',$value['txn_id']);
	     		if($get_item_remarks){
	     			$item_remarks = $get_item_remarks['vch_narr'];
	     			$remarks = explode('_', $item_remarks);
	     			if(isset($item_remarks[1])){
	     				$phy_stock = $remarks[1];
	     				$book_stock = $remarks[2];
	     				$stock_diff = $remarks[3];
	     			}
	     		}

	     		$transactions[$key]['phy_stock'] = $phy_stock;
	     		$transactions[$key]['book_stock'] =  $book_stock;
	     		$transactions[$key]['stock_diff'] =  $stock_diff;
	     		$transactions[$key]['voucher_date'] =  date('d-m-Y', strtotime($voucher_date));
	     		$transactions[$key]['bill_no'] =  $get_cons_detail['comp_vch_no'];
	     		$transactions[$key]['narration'] =  $transactions[$key]['description'];
	     	}
	     	
	     }
	     else{
	      	$transactions       =  $this->PhysicalvfnModel->physical_transactions_list($voucher_txn_id,$voucher_type_id,$voucher_date);
	     }

	      // echo "<pre>";print_r($transactions);exit;
	    
	      
		
       
        if($this->request->getMethod() == 'post'){	
             
             $long_narration   = $this->request->getVar('narration');
             $voucher_series   = $this->request->getVar('voucher_series'); 
		     $voucher_no       = $this->PhysicalvfnModel->get_voucher_no($voucher_id); 
		     $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		     $narration        = $this->request->getVar('narration'); 
		     $itmsdata         = json_decode($this->request->getVar('itmsdata'),true);
		     
		     if($this->request->getVar('vchrdate')!='')
		     $voucher_date     = $this->request->getVar('vchrdate');
		     else
		     $voucher_date     = $this->request->getVar('voucher_date');
		     
		     $voucher_date = date("Y-m-d", strtotime($voucher_date));
		     
		    // add voucher consolidated entry
			 $voucher_cons_data     = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_id,
				                            "voucher_date"=>$voucher_date, "mat_cent_id"=>$matrcntr_id);
			 $this->PhysicalvfnModel->update_voucher_cons_data($voucher_txn_id,$voucher_cons_data);
			 
		    
		     $voucher_txn_data  = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_id,"voucher_date"=>$voucher_date);
			
			$log = [
		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
		            'log_date' => date('Y-m-d'),
		            'log_time' => date('H:i:s'),
		            'log_action_tags' => 'update',
		            'log_field_id' => $voucher_txn_id,
		            'log_field_name' => 'Inward Challan',
		            'log_field_type' => 'Voucher',
		        ];
		    $this->LogModel->add_log($log);
			
			
		     if($itmsdata){
		         
		         // delete items transaction
    		    if(isset($transactions) && count($transactions) >0){
    		    foreach($transactions as $transrow_val) {	
    				      $transc_txn[$transrow_val['txn_id']] = $transrow_val['txn_id'];
    				      $item_txn_list[$transrow_val['item_id']] = $transrow_val['txn_id'];
    				      $sel_item_id    = $transrow_val['item_id'];
    				      $tn             = $this->company_id.'_itemtxnnnn_'.$sel_item_id.'_'.$ses_comp_fy_id;
    				      $this->PhysicalvfnModel->delete_item_transaction_byid($tn,$transrow_val['txn_id'],$this->company_id,$sel_item_id);
    				  }
    		    }
		    $this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
		    $this->TransactionModel->delete_all_narrations($voucher_txn_id);    
		      
		    $oCheck     = $this->request->getVar('oCheck');

            	if(isset($oCheck) && $oCheck == 1) // Optional Voucher
            	{
            		$voucher_type_id = $voucher_id;
            		$voucher_tag = 'OPTIONL';

            		$voucher_date = date("Y-m-d", strtotime($voucher_date));
	               $voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);

	               $insert_data  = array(
	                        "comp_vch_series_id"        => $voucher_series,
	                        "voucher_date"              => $voucher_date,
	                        "voucher_tag"              => $voucher_tag,
	                        "mat_cent_id"           => $matrcntr_id,
		                    "vch_subtype_id"        => 0,
	                );
	                $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);
	                

	               foreach($itmsdata as $item_row)
			     {
					$txn_data = array(
						"comp_id"				=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_txn_id" 		=> $voucher_txn_id,
						"master_id" 			=> $item_row['item_id'],
						'master_id_type' 		=> 'ito'
					);
					$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

					$physical_stock = $item_row['physical_stock'];
					$book_stock     = $item_row['book_stock'];
					$stock_diff     = $item_row['stock_diff'];
					if($stock_diff >= 0){
						$item_oth_txn_drcr ='d';
						$item_oth_txn_qty = $stock_diff;
					}
					else if($stock_diff < 0){
						$item_oth_txn_drcr ='c';
						$item_oth_txn_qty = abs($stock_diff);
					}


					$insert_data   = array(
						'comp_id'              => $this->company_id,
						'item_oth_txn_date'    => $voucher_date,
						'item_oth_txn_amount'  => 0,
						'item_oth_txn_drcr'    => $item_oth_txn_drcr,
						'item_oth_txn_qty'     => $item_oth_txn_qty,
						'description'          => $item_row['short_narration'],
						'comp_vch_series_no'   => $voucher_no,
						'item_id'              => $item_row['item_id'],
						'voucher_txn_id'       => $voucher_txn_id,
						'voucher_type_id'      => $voucher_type_id,
						'mat_cent_id'          => $matrcntr_id,
						'item_oth_txn_tag'     => $voucher_tag,
						'bo_id'			   => 0,
						'txn_id'			   => $txn_id,
						'item_unit'            => $item_row['item_unit_id'],
					);
					$this->TransactionModel->add_itm_oth_data($insert_data);
					// no need to update item qty balance

					$item_remarks = "bookstock_".$physical_stock."_".$book_stock."_".$stock_diff;
					$this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$item_remarks);
					
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
			$voucher_cons_data = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_id,
				                        "voucher_date"=>$voucher_date,"mat_cent_id"=>$matrcntr_id, "voucher_tag" => '');
			
			$this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $voucher_cons_data);
			

			// $this->PhysicalvfnModel->save_voucher_narration($voucher_txn_id,0,'long',$long_narration);
			 
			 $log = [
		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
		            'log_date' => date('Y-m-d'),
		            'log_time' => date('H:i:s'),
		            'log_action_tags' => 'add',
		            'log_field_id' => $voucher_txn_id,
		            'log_field_name' => 'Physical Verification',
		            'log_field_type' => 'Voucher',
		        ];
		    $this->LogModel->add_log($log);
		    
			 $voucher_txn_data  = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_id,"voucher_date"=>$voucher_date, "voucher_tag" => "");
			
		     if($itmsdata){
		         foreach($itmsdata as $item_row){
		             $physical_stock = $item_row['physical_stock'];
		             $book_stock     = $item_row['book_stock'];
		             $stock_diff     = $item_row['stock_diff'];
		             if($stock_diff >= 0){
		              $item_txn_drcr ='d';
		              $show_qty_stock = $stock_diff;
		             }
		             else if($stock_diff < 0){
		             $item_txn_drcr ='c';
		              $show_qty_stock = str_replace("-",'',$stock_diff);
		             }
		             
		             $item_unit_id       = 	$item_row['item_unit_id'];
		             
		             
		             if(isset($item_row['short_narration']))
		               $short_narration = $item_row['short_narration'];
		               else
		               $short_narration = '';
		               
		          $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
				   if($get_item_info){
				   $item_open_qty      =  $get_item_info['op_bal_qty'] ? $get_item_info['op_bal_qty'] : 0;
		           $item_open_value    =  $get_item_info['op_bal_val'] ? $get_item_info['op_bal_val'] : 0;
				 }else{
					 $item_open_qty =0;
					 $item_open_value =0;
				 }     
		           
        	$items_data    = array(
                						'comp_id'             => $this->company_id,
                						'item_txn_date'       => $voucher_date,
                						'item_txn_amount'     => '',
                						'item_txn_drcr'       => $item_txn_drcr,
                						'item_txn_qty'        => $show_qty_stock,
                						'short_narration'     => $short_narration,
                						'item_id'             => $item_row['item_id'],
                						'voucher_type_id'     => $voucher_id,
                                        'voucher_txn_id'      => $voucher_txn_id,
                                        'mat_cent_id'         => $matrcntr_id,
                                        'item_remarks'        => "bookstock_".$book_stock."_".$show_qty_stock."_".$voucher_txn_id.",",
                                        // 'item_remarks_id'     => $voucher_txn_id.','
        					          );										
			$itemstxn_table = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$ses_comp_fy_id; 	   
			$this->PhysicalvfnModel->add_itemstxn_transactions($itemstxn_table,$items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id); 
		        }}
		        
		        $insert_data = [
			              "comp_id"             => $this->company_id,
			              "comp_vch_series_id"  => $voucher_series,
			              "voucher_txn_id"      => $voucher_txn_id,
			              "master_id"           => 0,
			              'master_id_type'      => 'nrr'
              		];

			    $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
			    $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

		   }

             // return redirect()->to($this->base_url.'physical_verification/add');
		        return json_encode(['status' => true, 'message' => 'Voucher Inserted']);
		  
        }
       
      
        $data['voucher_type_id']                = $voucher_id;
       	$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url; 
		$data['voucher_auto_no']  = $get_cons_detail['comp_vch_no'];
		$data['mc_id']            = $get_cons_detail['mat_cent_id'];
		$data['folder_path']      = $this->folder_path;	
		$data['items_list']       = $this->PhysicalvfnModel->items_list($this->company_id);	
		$data['units_list']       = $this->PhysicalvfnModel->units_dropdown($this->company_id);
		$data['voucher_series_dropdown']  = $this->PhysicalvfnModel->comp_voucher_series($this->company_id);
		
		$data['voucher_id']          = $voucher_id;
		$data['get_voucher_narration_info']          = $get_voucher_narration_info;
		$data['matrcntr_dropdown']   = $this->PhysicalvfnModel->matrcntr_dropdown($this->company_id);
		
		$data['comp_vch_info']           = $get_cons_detail;
		$data['transactions']            = $transactions;
		$data['voucher_txn_id']          = $voucher_txn_id;
		$data['voucher_type_id']         = $voucher_type_id;
		$data['oCheck']	= $oCheck;
	
		return view($this->folder_path.'physical_verification/edit',$data);		
     }     
	
}