<?php
namespace App\Controllers\Admin;


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
	
		$this->LogModel        = new ERPLogModel();
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
   

  
	public function add()
    {     $voucher_type_id       = "10";
	         $ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
	        if($this->request->getMethod() == 'post'){
        		// echo "<pre>";print_r($_POST);exit;	
             $long_narration   = $this->request->getVar('narration');
             $voucher_series   = $this->request->getVar('voucher_series'); 
		     $voucher_no       = $this->TransactionModel->get_voucher_no($voucher_type_id); 
		     $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		     $narration        = $this->request->getVar('narration'); 
		     $itmsdata         = json_decode($this->request->getVar('itmsdata'),true);
		     
		     if($this->request->getVar('vchrdate')!='')
		     $voucher_date     = $this->request->getVar('vchrdate');
		     else
		     $voucher_date     = $this->request->getVar('voucher_date');
		     
		     $voucher_date = validate_date_by_fy($voucher_date);

		     $oCheck     = $this->request->getVar('oCheck');

            	if(isset($oCheck) && $oCheck == 1) // Optional Voucher
            	{
            		$voucher_type_id = $voucher_type_id;
            		$voucher_tag = 'OPTIONL';

            		$voucher_date = validate_date_by_fy($voucher_date);
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
	                    "currency_id"           => 1,
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
			$insert_data = [
				"comp_id"				=> $this->company_id,
				"comp_vch_series_id"	=> $voucher_series,
				"comp_vch_no"			=> $voucher_no,
				"voucher_type_id"		=> $voucher_type_id,
				"voucher_date"			=> $voucher_date,
				"mat_cent_id"			=> $matrcntr_id, 
				"voucher_tag" 			=> '', 
				"currency_id"            =>  1,
			];
			$voucher_txn_id    = $this->TransactionModel->add_voucher_cons_data($insert_data);
			
			foreach($itmsdata as $item_row)
			{
				$txn_data = array(
					"comp_id"			    => $this->company_id,
					"comp_vch_series_id"	=> $voucher_series,
					"voucher_txn_id" 	    => $voucher_txn_id,
					"master_id" 			=> $item_row['item_id'],
					'master_id_type' 		=> 'itm'
				);
				$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

				$physical_stock = $item_row['physical_stock'];
				$book_stock     = $item_row['book_stock'];
				$stock_diff     = $item_row['stock_diff'];
				if($stock_diff >= 0){
					$item_txn_drcr ='d';
					$item_txn_qty = $stock_diff;
				}
				else if($stock_diff < 0){
					$item_txn_drcr ='c';
					$item_txn_qty = abs($stock_diff);
				}

				$insert_data   = [
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $voucher_date,
						'item_txn_amount'     => 0,
						'item_txn_drcr'       => $item_txn_drcr,
						'item_txn_qty'        => $item_txn_qty,
						'description'         => $item_row['short_narration'],
						'item_id'             => $item_row['item_id'],
						'voucher_txn_id'      => $voucher_txn_id,
						'voucher_type_id'     => $voucher_type_id,
						'mat_cent_id'         => $matrcntr_id,
						'bo_id'			  => 0,
						'txn_id'			  => $txn_id,
						"item_unit"		  => $item_row['item_unit_id'],
						"item_bal_qty"		  => 0,
						"item_avail"  		  => 1,
						"batch_id"  		  => 0,										

				];	
				$this->TransactionModel->add_itm_txn_data($insert_data);

				$item_remarks = "bookstock_".$physical_stock."_".$book_stock."_".$stock_diff;
				$this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$item_remarks);
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
        
		$voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);
		$ses_comp_fy_id           = $this->session->get('ses_comp_fy_id');
		$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url; 
		$data['voucher_auto_no']  = $this->TransactionModel->get_voucher_no($voucher_type_id);
		$data['folder_path']      = $this->folder_path;	
		$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
		$data['matrcntr_dropdown']       = $this->TransactionModel->matrcntr_dropdown();
		$data['units_list']     	 	 = $this->TransactionModel->units_dropdown();

		$data['voucher_date']     = $voucher_detail['last_entry'];
		$item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
        $bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';

		$data['item_json_file']   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);
		$data['bsd_json_file']    = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);	
		$data['voucher_type_id']                = $voucher_type_id;	
			
		return view($this->folder_path.'physical_verification/add',$data);		
     }
     
   	public function edit($voucher_txn_id,$voucher_type_id)
    {     $voucher_type_id       = "10";
         $ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
         
          if(!$voucher_type_id)
		   throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 	
	      
		  $get_voucher_narration_info  = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long', 0);
	      $get_cons_detail   = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);

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
	     		$get_item_remarks  = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',$value['txn_id']);
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
	      	$transactions = $this->TransactionModel->get_item_transactions($voucher_txn_id);
	     	foreach ($transactions as $key => $value) {

	     		$phy_stock = 0;
	     		$book_stock = 0;
	     		$stock_diff = 0;
	     		$get_item_remarks  = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',$value['txn_id']);
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

	      // echo "<pre>";print_r($transactions);exit;
	    
	      
		
       
        if($this->request->getMethod() == 'post'){	
             
             $long_narration   = $this->request->getVar('narration');
             $voucher_series   = $this->request->getVar('voucher_series'); 
		     $voucher_no       = $get_cons_detail['comp_vch_no'];
		     $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		     $narration        = $this->request->getVar('narration'); 
		     $itmsdata         = json_decode($this->request->getVar('itmsdata'),true);
		     
		     if($this->request->getVar('vchrdate')!='')
		     $voucher_date     = $this->request->getVar('vchrdate');
		     else
		     $voucher_date     = $this->request->getVar('voucher_date');
		     
		     $voucher_date = validate_date_by_fy($voucher_date);
		     
		    // add voucher consolidated entry
			 $voucher_cons_data     = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_type_id,
				                            "voucher_date"=>$voucher_date, "mat_cent_id"=>$matrcntr_id);
			
			 $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $voucher_cons_data);
		    
  
    		    
    		    $data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
			foreach ($data as $key => $value) {
					
				if($value['master_id_type'] == 'itm'){
					$this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
				}
			}
		    $this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
		    $this->TransactionModel->delete_all_narrations($voucher_txn_id);    
		      
		    $oCheck     = $this->request->getVar('oCheck');

            	if(isset($oCheck) && $oCheck == 1) // Optional Voucher
            	{
            		$voucher_type_id = $voucher_type_id;
            		$voucher_tag = 'OPTIONL';

            		$voucher_date = validate_date_by_fy($voucher_date);
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
		    

			$insert_data  = array(
					"comp_vch_series_id"	=> $voucher_series,
					"voucher_date"			=> $voucher_date,
					'mat_cent_id'			=> $matrcntr_id,
					"currency_id"			=> 1,
					"voucher_tag"			=> ''
			);
	  		$this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);
			

			foreach($itmsdata as $item_row)
			{
				$txn_data = array(
					"comp_id"			    => $this->company_id,
					"comp_vch_series_id"	=> $voucher_series,
					"voucher_txn_id" 	    => $voucher_txn_id,
					"master_id" 			=> $item_row['item_id'],
					'master_id_type' 		=> 'itm'
				);
				$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

				$physical_stock = $item_row['physical_stock'];
				$book_stock     = $item_row['book_stock'];
				$stock_diff     = $item_row['stock_diff'];
				if($stock_diff >= 0){
					$item_txn_drcr ='d';
					$item_txn_qty = $stock_diff;
				}
				else if($stock_diff < 0){
					$item_txn_drcr ='c';
					$item_txn_qty = abs($stock_diff);
				}

				$insert_data   = [
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $voucher_date,
						'item_txn_amount'     => 0,
						'item_txn_drcr'       => $item_txn_drcr,
						'item_txn_qty'        => $item_txn_qty,
						'description'         => $item_row['short_narration'],
						'item_id'             => $item_row['item_id'],
						'voucher_txn_id'      => $voucher_txn_id,
						'voucher_type_id'     => $voucher_type_id,
						'mat_cent_id'         => $matrcntr_id,
						'bo_id'			  => 0,
						'txn_id'			  => $txn_id,
						"item_unit"		  => $item_row['item_unit_id'],
						"item_bal_qty"		  => 0,
						"item_avail"  		  => 1,
						"batch_id"  		  => 0,										

				];	
				$this->TransactionModel->add_itm_txn_data($insert_data);

				$item_remarks = "bookstock_".$physical_stock."_".$book_stock."_".$stock_diff;
				$this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$item_remarks);
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
       
      
        $data['voucher_type_id']                = $voucher_type_id;
       	$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url; 
		$data['voucher_auto_no']  = $get_cons_detail['comp_vch_no'];
		$data['mc_id']            = $get_cons_detail['mat_cent_id'];
		$data['folder_path']      = $this->folder_path;	
		$data['items_list']     					= $this->TransactionModel->items_list();	
		$data['units_list']     					= $this->TransactionModel->units_dropdown();
		$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
		
		$data['voucher_id']          = $voucher_type_id;
		$data['get_voucher_narration_info']          = $get_voucher_narration_info;
		$data['matrcntr_dropdown']   = $this->TransactionModel->matrcntr_dropdown();
		
		$data['comp_vch_info']           = $get_cons_detail;
		$data['transactions']            = $transactions;
		$data['voucher_txn_id']          = $voucher_txn_id;
		$data['voucher_type_id']         = $voucher_type_id;
		$data['oCheck']	= $oCheck;
	
		return view($this->folder_path.'physical_verification/edit',$data);		
     }  

     public function delete($voucher_txn_id)
	{
 		$voucher_type_id    = "10";

		$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);

		if(!empty($voucher_info)){

			$data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
			foreach ($data as $key => $value) {
					if($value['master_id_type'] == 'itm'){
							$this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
					}
			}
			$this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
			$this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
			$this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
			$this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
			$this->TransactionModel->delete_voucher_conso_data($voucher_txn_id);
			$this->TransactionModel->delete_all_narrations($voucher_txn_id);
		}

		
		// return redirect()->to($this->base_url.'stock_transfer/mc');
		return json_encode(['status' => true, 'message' => 'Voucher Deleted']);
	}   
	
}