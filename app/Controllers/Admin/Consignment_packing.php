<?php
namespace App\Controllers\Admin;
use App\Models\Admin\TransactionModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Consignment_packing  extends BaseController
{
  function __construct()
    {   
	    helper(['form', 'url','text']);
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
		$this->against_ref_arary = array(""=>"Choose","1"=>"Sales","2"=>"Sales Order","3"=>"Purchase Order","4"=>"Delivery Challan",
		                                  "5"=>"Stock Transfer","6"=>"Credit Notes","7"=>"Debit Notes");
    }
 	public function index()
    {
	    $data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		return view($this->folder_path.'consignment_packing/view',$data);
	}
    public  function ajax_packing_list() 
	 {
		echo $response =  $this->TransactionModel->ajax_packing_list();			
	 } 
	 
   public function add()
    {
		$voucher_type_id =4;
		 if($this->request->getMethod() == 'post'){	
		    $pack_list_name  = $this->request->getVar('pack_list_name'); 
			$consignment_to  = $this->request->getVar('consignment_to');
			$list_delivery   = $this->request->getVar('list_delivery');
			$packing_type    = $this->request->getVar('packing_type');
			$against_ref     = $this->request->getVar('against_ref');
			$voucher_series     = $this->request->getVar('voucher_series');
			$voucher_date     = $this->request->getVar('voucher_date');
			$matrcntr_id     = $this->request->getVar('matrcntr_id');
			$voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);
			
			$rules = [				
				'pack_list_name' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please Enter Packing list Name',
					   ]],
				'consignment_to' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please Enter Consignment To',
					   ]]	   
				  			   
			       ];
			
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{			
			    
				// add voucher consolidated entry
                $insert_data  = array(
                    "comp_id"               => $this->company_id,
                    "comp_vch_series_id"    => $voucher_series,
                    "comp_vch_no"           => $voucher_no,
                    "voucher_type_id"       => $voucher_type_id,
                    "voucher_date"          => validate_date_by_fy($voucher_date),
                    "mat_cent_id"           => $matrcntr_id,
                    "vch_subtype_id"        => 0,
                    "voucher_tag"           => '',
                );
                $voucher_txn_id             = $this->TransactionModel->add_voucher_cons_data($insert_data);
				
			    $insert_data   = [
				        'list_name'        => $pack_list_name,
						'comp_id'          => $this->company_id,
						'list_delivery'    => validate_date_by_fy($list_delivery),
						'list_status'      => '0',
						'list_consignee'   => $consignment_to,
						'list_type'        => $packing_type,
						'list_against_ref' => '',
						'mc_id'            => $matrcntr_id
						 ];
				$table_name	= $this->company_id.'_pcklistmst_'.$this->session->get('ses_comp_fy_id');	 
			    $list_id   = $this->TransactionModel->add_list($table_name,$insert_data);
				
				$this->TransactionModel->update_voucher_cons_data($voucher_txn_id,array('voucher_tag'=>$list_id));
                return redirect()->to($this->base_url.'consignment_packing');
				 die;					
			}
			
			
		 }
		
	    $data['message_output']           = $this->message_output;
		$data['base_url']                 = $this->base_url;	
		$data['folder_path']              = $this->folder_path;	
		$data['against_ref_arary']        = $this->against_ref_arary;	  		
        $data['PackingListId']	          = $this->TransactionModel->GetPackingListNumber();
		$data['consignment_to']	          = $this->TransactionModel->consignmentto_dropdown();	
		$data['voucher_no']               = $this->TransactionModel->get_voucher_no($voucher_type_id);     
        $data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
		$data['voucher_date']             = date('d-m-Y');
		$data['matrcntr_dropdown']        = $this->TransactionModel->matrcntr_dropdown();
		return view($this->folder_path.'consignment_packing/add_packing_list',$data);
	}	
	
	public function delete_packing($packing_id){
		$Consoinfo = $this->TransactionModel->delete_consignment_packing($packing_id);
		return redirect()->to($this->base_url.'consignment_packing');
				 die;
	}
	
	public function edit($packing_id)
    {
		$voucher_type_id =4;
		$Consoinfo = $this->TransactionModel->GetConsoPackinginfo($packing_id);
		 if($this->request->getMethod() == 'post'){	
		    $pack_list_name  = $this->request->getVar('pack_list_name'); 
			$consignment_to  = $this->request->getVar('consignment_to');
			$list_delivery   = $this->request->getVar('list_delivery');
			$packing_type    = $this->request->getVar('packing_type');
			$against_ref     = $this->request->getVar('against_ref');
			$voucher_series  = $this->request->getVar('voucher_series');
			$voucher_date    = $this->request->getVar('voucher_date');
			$matrcntr_id     = $this->request->getVar('matrcntr_id');
			$voucher_no      = $this->TransactionModel->get_voucher_no($voucher_type_id);
			
			$rules = [				
				'pack_list_name' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please Enter Packing list Name',
					   ]],
				'consignment_to' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please Enter Consignment To',
					   ]]	   
				  			   
			       ];
			
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{			
			    $update_data   = [
				        'list_name'        => $pack_list_name,
						'list_delivery'    => validate_date_by_fy($list_delivery),
						'list_consignee'   => $consignment_to,
						'mc_id'            => $matrcntr_id
						 ];
				$table_name	= $this->company_id.'_pcklistmst_'.$this->session->get('ses_comp_fy_id');	 
			    $this->TransactionModel->update_pcklistmst($table_name,$update_data,$packing_id);
				
				$voucher_txn_id = $Consoinfo['voucher_txn_id'];
				$conso_update   = array("voucher_date"=>validate_date_by_fy($voucher_date),"mat_cent_id"=>$matrcntr_id);
				$this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$conso_update);
                return redirect()->to($this->base_url.'consignment_packing');
				 die;					
			}
			
			
		 }
		
	    $data['message_output']           = $this->message_output;
		$data['base_url']                 = $this->base_url;	
		$data['folder_path']              = $this->folder_path;	
		$data['against_ref_arary']        = $this->against_ref_arary;	  		
        $data['PackingListId']	          = $this->TransactionModel->GetPackingListNumber();
		$data['consignment_to']	          = $this->TransactionModel->consignmentto_dropdown();	
		$data['voucher_no']               = $this->TransactionModel->get_voucher_no($voucher_type_id);     
        $data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
		$data['voucher_date']             = date('d-m-Y');
		$data['matrcntr_dropdown']        = $this->TransactionModel->matrcntr_dropdown();
		$data['packing_id']               =  $packing_id;
		$data['packing_info']             = $this->TransactionModel->consignment_packing_info($packing_id);	
		$data['Consoinfo']                = $Consoinfo;
		return view($this->folder_path.'consignment_packing/edit_packing_list',$data);
	}
	
	public function remove_packing(){
		 if ($this->request->isAJAX()) {
			 $pack_level         = $this->request->getVar('pack_level');
		     $packing_type       = $this->request->getVar('packing_type');	
			 $unit_id            = $this->request->getVar('item_unit_id');
			 $item_id            = $this->request->getVar('item_id');
			 $batch_no           = $this->request->getVar('batch_no');
			 $item_qty           = $this->request->getVar('item_qty');
			 $specification_no   = $this->request->getVar('specification_no');
			 $AvailQty           = $this->request->getVar('AvailQty');
			 $packing_list_id    = $this->request->getVar('packing_list_id');
			 $carrying_unit_id   = $this->request->getVar('to_carrying_unit_id');
			 $to_uom_id          = $unit_id;			
			 $qty_packed         = $this->request->getVar('to_qty_packed');
			 $cu_label           = $this->request->getVar('to_cu_label'); 
			 
			 
			  $table_name	 = $this->company_id.'_listpacked_'.$this->session->get('ses_comp_fy_id');	 
			  $this->TransactionModel->delete_listpacked($table_name,$pack_level,$unit_id,$item_id,$packing_list_id); 
			  // fetch item availability and update to grid			
			 echo $this->TransactionModel->pcklistqty_availability($packing_list_id,$item_id,$unit_id);
			 die();
		 }
	}
	
	
	public function revised_cu_qty(){
	    if ($this->request->isAJAX()) {
	        $item_id            = $this->request->getVar('item_id');
	        $qty_packed         = $this->request->getVar('to_qty_packed');
	        $packing_list_id    = $this->request->getVar('packing_list_id');
	        $cu_id              = $this->request->getVar('cu_id');
	        $unit_id            = $this->request->getVar('item_unit_id');
	       
	        $table_name	 = $this->company_id.'_listpacked_'.$this->session->get('ses_comp_fy_id');
	        $update_data = array("item_qty_packed" =>$qty_packed );
	        $this->TransactionModel->revised_pcklistqty($table_name,$item_id,$packing_list_id,$cu_id,$unit_id,$update_data);
	        
	       $this->TransactionModel->pcklistqty_availability($packing_list_id,$item_id,$unit_id);
		   echo $qty_packed;
		   
	        die();
	    }
	    
	}
	
	public function finalize_packing_status($packing_list_id){
	   
	        $table_name	 = $this->company_id.'_listpacked_'.$this->session->get('ses_comp_fy_id');	 
		    $list_all_cu   = $this->TransactionModel->GetListAllCu($packing_list_id);	
		    if($list_all_cu){
				foreach($list_all_cu as $crow){
					$cu_id      = $crow['cu_id'];
					$pack_level = $crow['list_level_id'];
					$this->TransactionModel->finalize_cu($packing_list_id,$cu_id,$pack_level);
				}
				
			}
		
	return redirect()->to($this->base_url.'consignment_packing');
				 die;		 
	}
	
	public function finalize_cu_status(){
	     if ($this->request->isAJAX()) {
	          $packing_list_id    = $this->request->getVar('packing_list_id');
	          $cu_id              = $this->request->getVar('cuid');
	          $pack_level         = $this->request->getVar('pack_level');
	          
	          $table_name	      = $this->company_id.'_listpacked_'.$this->session->get('ses_comp_fy_id');
	        
	        $this->TransactionModel->finalize_cu($packing_list_id,$cu_id,$pack_level);
	        
	     }
	}
	
	public function unpack_cu_item(){
	     if ($this->request->isAJAX()) {
	        $item_id            = $this->request->getVar('item_id');
	        $qty_packed         = $this->request->getVar('to_qty_packed');
	        $packing_list_id    = $this->request->getVar('packing_list_id');
	        $cu_id              = $this->request->getVar('cu_id');
	        $unit_id            = $this->request->getVar('item_unit_id');
	        
	        $table_name	 = $this->company_id.'_listpacked_'.$this->session->get('ses_comp_fy_id');
	        
	        $this->TransactionModel->remove_pcklistqty($item_id,$packing_list_id,$cu_id,$unit_id);
	        
	        echo $qty_packed;
	        die();
	    }
	    
	}
	
	
	public function save_level_packing(){
		 if ($this->request->isAJAX()) {
			
			 $pack_level         = $this->request->getVar('pack_level');
		     $packing_type       = $this->request->getVar('packing_type');	
			 $unit_id            = $this->request->getVar('item_unit_id');
			 $item_id            = $this->request->getVar('item_id');
			 $batch_no           = $this->request->getVar('batch_no');
			 $item_qty           = $this->request->getVar('item_qty');
			 $specification_no   = $this->request->getVar('specification_no');
			 $AvailQty           = $this->request->getVar('AvailQty');
			 $packing_list_id    = $this->request->getVar('packing_list_id');
			 $carrying_unit_id   = $this->request->getVar('to_carrying_unit_id');
			 $to_uom_id          = $unit_id;			
			 $qty_packed         = $this->request->getVar('to_qty_packed');
			 $cu_label           = $this->request->getVar('to_cu_label');
			 	 
			 
			   // create listcumast 
			$insert_data   = array("list_id"=>$packing_list_id,"cu_label"=>$cu_label,"cu_unit"=>$carrying_unit_id,
			                       "list_level_id"=>$pack_level);
			 $table_name = $this->company_id.'_listcumast_'.$this->session->get('ses_comp_fy_id');	 
			 $cu_id      = $this->TransactionModel->add_cumast_list($table_name,$insert_data); 
	         
			  // cupackingn  insert			  
			  $cupackingn_insert_data   = array("list_id"=>$packing_list_id,"cu_id"=>$cu_id,"cu_unit"=>$carrying_unit_id,
			                                    "list_level_id"=>$pack_level,"cu_status"=>1,"pck_status"=>0);
			  $cupackingn_table         = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id');	 
			  $packing_id = $this->TransactionModel->add_cupackingn_list($cupackingn_table,$cupackingn_insert_data);  
			  
			  if($packing_id){	  
			  	 // leveltrack  insert			 
				  if($pack_level >=2){
					  if($this->request->getVar('cu_id')){
						 $cu_id         = $this->request->getVar('cu_id');
					  }else
					   $cu_id  =0;
				   
					  if($cu_id>0){
						$previous_packing_info =  $this->TransactionModel->previous_packing_info($cu_id,$packing_list_id);
						$prev_packing_id       = $previous_packing_info['packing_id'];
					   $leveltrack_insert_data   = array("packing_id"=>$packing_id,"list_id"=>$packing_list_id,"nxt_packing_id"=>0,
																"prev_packing_id"=>$prev_packing_id);
					   
						// update all previous cu status to 0
						$cu_update_data = array("cu_status"=>"0");
						$this->TransactionModel->update_prev_cu_status($cupackingn_table,$packing_list_id,$prev_packing_id,$cu_update_data);					   
																
					 }	 
					 else{
						      $prev_packing_id =0;
							  $leveltrack_insert_data   = array("packing_id"=>$packing_id,"list_id"=>$packing_list_id,"nxt_packing_id"=>0,
																"prev_packing_id"=>$prev_packing_id);
							  									
																
					 }
					  $leveltrack_table         = $this->company_id.'_leveltrack_'.$this->session->get('ses_comp_fy_id');	 
					  $nxt_packing_id = $this->TransactionModel->add_list($leveltrack_table,$leveltrack_insert_data); 
					  
					  $this->TransactionModel->update_leveltrack($leveltrack_table,array("nxt_packing_id"=>$nxt_packing_id),$prev_packing_id); 
					  
					 }
		  
					}
			  
			  
			 
		 }
	}
	
	public function save_packing(){
		 if ($this->request->isAJAX()) {
			
			 $pack_level         = $this->request->getVar('pack_level');
		     $packing_type       = $this->request->getVar('packing_type');	
			 $unit_id            = $this->request->getVar('item_unit_id');
			 $item_id            = $this->request->getVar('item_id');
			 $batch_no           = $this->request->getVar('batch_no');
			 $item_qty           = $this->request->getVar('item_qty');
			 $specification_no   = $this->request->getVar('specification_no');
			 $AvailQty           = $this->request->getVar('AvailQty');
			 $packing_list_id    = $this->request->getVar('packing_list_id');
			 $carrying_unit_id   = $this->request->getVar('to_carrying_unit_id');
			 $to_uom_id          = $unit_id;			
			 $qty_packed         = $this->request->getVar('to_qty_packed');
			 $cu_label           = $this->request->getVar('to_cu_label');
			  
			 // get voucher txn id from conso table with packing list uid in voucher tag field
			 
			 $Consoinfo = $this->TransactionModel->GetConsoPackinginfo($packing_list_id);
			 
			 $txn_data = array(
						"comp_id"				=> $this->company_id,
						"comp_vch_series_id"	=> $Consoinfo['comp_vch_series_id'],
						"voucher_txn_id" 		=> $Consoinfo['voucher_txn_id'],
						"master_id" 			=> $item_id,
						'master_id_type' 		=> 'pck'
					    );
			$txn_id    = $this->TransactionModel->add_comp_txn_data($txn_data);
			
			 
			 // add pcklistqty
			  $pcklistqty_table_name  = $this->company_id.'_pcklistqty_'.$this->session->get('ses_comp_fy_id');	 
			  $pcklistqty_data        = array("list_id"=>$packing_list_id,"item_id"=>$item_id,"item_unit"=>$unit_id,
			                       "item_qty_available"=>$item_qty,"item_qty_initial"=>$item_qty);
			  $this->TransactionModel->add_pcklistqty_list($pcklistqty_table_name,$pcklistqty_data);
			  
			    // create listcumast 
			  $insert_data   = array("list_id"=>$packing_list_id,"cu_label"=>$cu_label,"cu_unit"=>$carrying_unit_id,
			                       "list_level_id"=>$pack_level);
			 $table_name = $this->company_id.'_listcumast_'.$this->session->get('ses_comp_fy_id');	 
			 $cu_id      = $this->TransactionModel->add_cumast_list($table_name,$insert_data); 
	         
			 
			 // listpacked data
			  $insert_data   = array("list_id"=>$packing_list_id,"item_id"=>$item_id,"item_unit"=>$unit_id,
			                       "item_qty_packed"=>$qty_packed,"cu_id"=>$cu_id,"list_level_id"=>$pack_level);
			  $table_name	 = $this->company_id.'_listpacked_'.$this->session->get('ses_comp_fy_id');	 
			  $this->TransactionModel->add_list($table_name,$insert_data); 
			  
			   // update pcklistqty qty avaialble		
			  $item_qty_available = $item_qty-$qty_packed;			  
			  $update_data = array("item_qty_available"=>$item_qty_available);
			  $this->TransactionModel->update_pcklistqty($pcklistqty_table_name,$packing_list_id,$item_id,$unit_id,$update_data);
			  

			// cupackingn  insert			  
			  $cupackingn_insert_data   = array("list_id"=>$packing_list_id,"cu_id"=>$cu_id,"cu_unit"=>$carrying_unit_id,
			                                    "list_level_id"=>$pack_level,"cu_status"=>1,"pck_status"=>0);
			  $cupackingn_table         = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id');	 
			  $packing_id = $this->TransactionModel->add_cupackingn_list($cupackingn_table,$cupackingn_insert_data);  
			  
			if($packing_id){	  
					 // leveltrack  insert			 
			 
						  $leveltrack_insert_data   = array("packing_id"=>$packing_id,"list_id"=>$packing_list_id,"nxt_packing_id"=>0,
															"prev_packing_id"=>0);
				 
				  $leveltrack_table         = $this->company_id.'_leveltrack_'.$this->session->get('ses_comp_fy_id');	 
				  $this->TransactionModel->add_list($leveltrack_table,$leveltrack_insert_data); 
			}
			
			 
			 
			/*  $get_item_info      =  $this->BalancesModel->get_item_balance_info($item_id);
			 $item_open_qty      =  !empty($get_item_info['op_bal_qty']) ? $get_item_info['op_bal_qty'] : 0;
			 $item_open_value    =  !empty($get_item_info['op_bal_val']) ? $get_item_info['op_bal_val'] : 0;
			 */

			$insert_data   = array(
							'comp_id'             => $this->company_id,
							'item_txn_date'       => $Consoinfo['voucher_date'],
							'item_txn_amount'     => 0,
							'item_txn_drcr'       => 'c',
							'item_txn_qty'        => $qty_packed,
							'description'         =>'',
							'item_id'             => $item_id,
							'voucher_txn_id'      => $Consoinfo['voucher_txn_id'],
							'voucher_type_id'     => $Consoinfo['voucher_type_id'],
							'mat_cent_id'         => $Consoinfo['mat_cent_id'],
							'bo_id'				  => 0,
							'txn_id'			  => $txn_id,
							"item_unit"			  => $unit_id,
						    "item_bal_qty"		  => 0,
							"item_avail"  		  => 1,
						    "batch_id"  		  => 0,
					   );	
			$citem_txn_id = $this->TransactionModel->add_itm_txn_data($insert_data);
           /*  $baclinsert_data = array(
							"item_txn_date"	  => $Consoinfo['voucher_date'],
							"item_id"		  => $item_id,
							'item_txn_drcr'   => 'c',
							"txn_id"		  => $txn_id,
							"item_txn_id"	  => $citem_txn_id,
							"voucher_txn_id"  => $Consoinfo['voucher_txn_id'],
							"bo_id"			  => 0,
							"mat_cent_id"	  => $Consoinfo['mat_cent_id'],
							"item_unit"		  => $unit_id,
							"item_bal_qty"	  => $qty_packed,
							"item_avail"      => "1" // avaialable qty
				   );
			 $this->BalancesModel->add_itemtxnbal($item_id,$baclinsert_data,'c',$item_open_qty,$item_open_value); */

			 
		     $insert_data   = array(
							'comp_id'             => $this->company_id,
							'item_txn_date'       => $Consoinfo['voucher_date'],
							'item_txn_amount'     => 0,
							'item_txn_drcr'       => 'd',
							'item_txn_qty'        => $qty_packed,
							'description'         =>'',
							'item_id'             => $item_id,
							'voucher_txn_id'      => $Consoinfo['voucher_txn_id'],
							'voucher_type_id'     => $Consoinfo['voucher_type_id'],
							'mat_cent_id'         => $Consoinfo['mat_cent_id'],
							'bo_id'				  => 0,
							'txn_id'			  => $txn_id,
							"item_unit"			  => $unit_id,
						    "item_bal_qty"		  => 0,
							"item_avail"  		  => 2,
						    "batch_id"  		  => 0,
					   );	
			 $item_txn_id = $this->TransactionModel->add_itm_txn_data($insert_data);
			 /* $pbaclinsert_data = array(
							"item_txn_date"	  => $Consoinfo['voucher_date'],
							"item_id"		  => $item_id,
							'item_txn_drcr'   => 'd',
							"txn_id"		  => $txn_id,
							"item_txn_id"	  => $item_txn_id,
							"voucher_txn_id"  => $Consoinfo['voucher_txn_id'],
							"bo_id"			  => 0,
							"mat_cent_id"	  => $Consoinfo['mat_cent_id'],
							"item_unit"		  => $unit_id,
							"item_bal_qty"	  => $qty_packed,
							"item_avail"      => "2" //packed qty
				   );
			 $this->BalancesModel->add_itemtxnbal($item_id,$pbaclinsert_data,'d',$item_open_qty,$item_open_value); */
			 
            
			 // fetch item availability and update to grid			
			 $item_qty_avail     =  $this->TransactionModel->pcklistqty_availability($packing_list_id,$item_id,$unit_id);
			
			 echo $item_qty_avail;
			 
			 die();
		 }
		
	}
	
	public function manage_list($packing_list_id=NULL,$packed_unpacked="unpacked",$level=1)	 
    {
		 if(!$packing_list_id)
		 return redirect()->to($this->base_url.'consignment_packing');
	    
		$item_json_file          = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
	    $data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session; 
		$data['packing_list_id']         = $packing_list_id;
		$data['packing_info']    = $this->TransactionModel->packing_list_info($packing_list_id);
        
        $data['units_list']      = $this->TransactionModel->units_dropdown();
        
        $data['items_cu_dropdown']    = $this->TransactionModel->itemscu_list($packing_list_id,$level);  
        
        $data['level_id']        = $level;
		if($packed_unpacked=='unpacked'){
		  if($level>=2	)
		   $data['cupackingn']	     = $this->TransactionModel->level_packed_items_lists($packing_list_id,$level);  
		 /*  else if($level==3) // means show level 2 packed entries
		   $data['cupackingn']	     = $this->TransactionModel->level_packed_items_lists($packing_list_id,$level);  
		 else if($level==4) // means show level 2 packed entries
		   $data['cupackingn']	     = $this->TransactionModel->level_packed_items_lists($packing_list_id,$level);    
		 else if($level==5) // means show level 2 packed entries
		   $data['cupackingn']	     = $this->TransactionModel->level_packed_items_lists($packing_list_id,$level);  */   
		   else 
		   $data['cupackingn']	     = array();
		   
		
	  
		return view($this->folder_path.'consignment_packing/unpacked_manage_list',$data);
		}
	   else{
		$data['packed_items']    = $item_json_file;  
        $data['cupackingn']	     = $this->TransactionModel->nested_packed_items_lists($packing_list_id,$level);  
        
		if($level==1)
		$data['level_data']	     = $this->TransactionModel->nested_packed_items_lists($packing_list_id,1);  
	    elseif($level==2)
		$data['level_data']	     = $this->TransactionModel->nested_packed_items_lists($packing_list_id,2);  
		elseif($level==3)
		$data['level_data']	     = $this->TransactionModel->nested_packed_items_lists($packing_list_id,3);  
		elseif($level==4)
		$data['level_data']	     = $this->TransactionModel->nested_packed_items_lists($packing_list_id,4);  
		elseif($level==5)
		$data['level_data']	     = $this->TransactionModel->nested_packed_items_lists($packing_list_id,5);  
		
		$data['level']= $level;
		
	   // die();
		
		return view($this->folder_path.'consignment_packing/packed_manage_list',$data);   
	   }
	}
	
}