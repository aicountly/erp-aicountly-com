<?php
namespace App\Controllers\Grpcomp;
use App\Models\Grpcomp\ItemsModel;
use App\Models\Grpcomp\ERPLogModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;


class Items extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url','text','custom']);
		$this->ItemsModel      = new ItemsModel();			
		$this->LogModel        = new ERPLogModel();
		$this->auth_session    = new auth_session();
		$this->CommonModel       =  new CommonModel();
	    $this->auth_session->group_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('GroupPath');
		$this->admin_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('GroupPath');
		$this->session    	 = \Config\Services::session();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->enc_string    = new enc_string();
    }
    
    public  function ajax_items()
	 {
		echo $response =  $this->ItemsModel->ajax_items();	
		
	 } 
  public function list_items(){
	echo $this->index();  
  }
  
   public function index()
    {	
		if(isset($_GET['grpcmpid']) && $_GET['grpcmpid']!=''){
			 $comp_id_info  = unobfuscate_link($_GET['grpcmpid']);
			 $comp_id       = $comp_id_info[1];			 
			 $this->CommonModel->choose_grp_company($comp_id);
			 $grpcmpid     = $_GET['grpcmpid'];
		}
		else{
			$this->session->set('ses_company_id','');
			$grpcmpid='';
		}
		$data['grpcmpid']        = $grpcmpid;
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['admin_url']        = $this->admin_url;
		$data['session']         = $this->session;
		$data['enc_string']      = $this->enc_string;	
      	$group_comp_list         = $this->CommonModel->all_group_companies();
		$comp_list = array();
		$comp_list[''] = 'Choose Group Company';
		if($group_comp_list){
			foreach($group_comp_list as $row){
				$comp_list[$row['encomp_id']]=$row['company_name'];	
			}
		}
        $data['comp_list'] = $comp_list;
		return view($this->folder_path.'items/view',$data);		
    } 
    
 	public function ledger_detail(){
     
     if(!isset($_GET['itmid']))
	    return redirect()->to($this->base_url.'items'); 
	    
		$item_id    	= $_GET['itmid'];    

		$from_date = !empty($_GET['frmdt']) ? date('d-m-Y',strtotime($_GET['frmdt'])) : date('01-m-Y');
		$to_date = !empty($_GET['todt']) ? date('d-m-Y',strtotime($_GET['todt'])) : date('d-m-Y');

		$unit_id     = $_GET['untid'] ?? 0;
		$mc_grp_id   = $_GET['mcgrpid'] ?? 0;
		$mc_id       = $_GET['mcid'] ?? 0;

		$data['item_name'] = '';
	  $data['val_id'] = 1;

	  $item_info  = $this->ItemsModel->get_item_info($item_id, $this->company_id);
		if($item_info){
			$data['item_name'] = $item_info['item_name'];
			$data['val_id'] =  $item_info['valmethod_id'];
		}	   
	  
		$data['val_id'] = $_GET['valuation_type'] ?? 1;


		$opening_balance = 0;
		if($unit_id != 0 && $mc_id != 0 && $mc_grp_id == 0){
			$opening_balance = $this->ItemsModel->opening_balance($item_id, $unit_id, $mc_id,date('Y-m-d', strtotime($from_date)));
		}

		$opening_balance_list = [];
		if($unit_id == 0 || $mc_id == 0 || $mc_grp_id != 0){
			$opening_balance_list = $this->ItemsModel->opening_balance_list($item_id, $unit_id, $mc_id, $mc_grp_id,date('Y-m-d', strtotime($from_date)));
		}

		$data['item_unit_list'] = $this->ItemsModel->item_unit_list($item_id);
	  
	  $data['item_id'] = $item_id;
	  $data['from_date'] = $from_date;
	  $data['to_date'] = $to_date;
	  $data['unit_id'] = $unit_id;
	  $data['mc_grp_id'] = $mc_grp_id;
	  $data['mc_id'] = $mc_id;
	  $data['opening_balance'] = $opening_balance;
	  $data['opening_balance_list'] = $opening_balance_list;
	  $data['base_url'] = $this->base_url;
	
    return view($this->folder_path.'items/item_ledger_view',$data);   
       
  }

  public function ajax_item_ledger()
	{
		$pq_curPage = (int)$_POST["pq_curpage"];
		$limit     = (int)$_POST["pq_rpp"];
	 	

		$from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
		$to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));

		$item_id    = $this->request->getVar('item_id');
		$unit_id    = $this->request->getVar('unit_id');
		$mc_id    = $this->request->getVar('mc_id');
		$mc_grp_id    = $this->request->getVar('mc_grp_id');
		$val_id    = $this->request->getVar('val_id');

		$search = '';
		$pq_filter    = $this->request->getVar('pq_filter');
		if(!empty($pq_filter)){
			$pq_filter = json_decode($pq_filter);
			$search = $pq_filter->data[0]->value;
		}

		$response = $this->ItemsModel->load_item_ledger($item_id, $unit_id, $mc_id, $mc_grp_id, $val_id, $limit, $from_date, $to_date,$pq_curPage, $search);
		

		return json_encode([
					'totalRecords' => $response['total_records'],
					'data'	=> $response['data'],
					'curPage' => $pq_curPage
			]);
	}
public function remove_items($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'items/list_items');
			
		
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 foreach($ids2 as $item_id){
		    $this->ItemsModel->remove_item_itmoppybal($item_id); 
		    $stat = true;
		    $name = $this->ItemsModel->get_item_name($item_id);;
		     
		     if ($this->ItemsModel->check_item_with_voucher($item_id)){ 
		         $stat = false;
		         array_push($errors, 'Failed! Item "'.$name.'" has one or more associated Vouchers');
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
    		            'log_field_type' => 'item',
    		            ];
    		     $this->LogModel->add_log($log);
    		     
    		     $this->ItemsModel->remove_single_items($item_id);
    		     $this->ItemsModel->remove_itm_prmydmns($item_id);
		     }
		     
		 }
		 
		 $file  =     WRITEPATH.'comp'.$this->company_id.'/item.json';
			    if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
			        file_put_contents($file, ""); 
		            $item_list  = $this->ItemsModel->company_all_items();
		            $comp_folder    = 'comp'.$this->company_id;
		            $f = fopen($file, 'a');
                    fwrite($f,$item_list);
			     }
			     
		 
		 if(count($errors))
		 {
             $this->session->setFlashdata('error_array_message', $errors);
		 }
		 
		return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);	
	  }

	  
   public function add_item()
    {  
	     $material_centre_dropdown = $this->ItemsModel->material_centre_dropdown($this->company_id);
		  if($this->session->get('ses_boid')!='')
									$bo_id = $this->session->get('ses_boid');
								else 
									 $bo_id =1;
		 if($this->request->getMethod() == 'post'){	
		
		
		    $item_name          = $this->request->getVar('item_name'); 
		    $item_sku           = $this->request->getVar('item_sku'); 
		    $item_alias         = $this->request->getVar('item_alias');
			$item_printname     = $this->request->getVar('item_printname');
		    $item_shortname     = $this->request->getVar('item_shortname');
			$item_group_id      = $this->request->getVar('item_group_id'); 
		    $item_unit_id       = $this->request->getVar('item_unit_id');
		    $item_tax           = $this->request->getVar('item_tax');
            $item_ces           = $this->request->getVar('item_ces'); 
		    $item_hsn           = $this->request->getVar('item_hsn');
			$item_sales_acc     = $this->request->getVar('item_sales_acc');
		    $item_pur_acc       = $this->request->getVar('item_pur_acc');						
		    $item_catg_id       = $this->request->getVar('item_catg_id');
			
			$item_unit_idm       = $this->request->getVar('item_unit_idm');
		    $item_op_bal_qtym    = $this->request->getVar('item_op_bal_qtym');
		    		
			$dimension_txt      = $this->request->getVar('dimension_txt');
			$dimension_unit     = $this->request->getVar('dimension_unit');
			
			$iteminfo_tag       = $this->request->getVar('iteminfo_tag');
			$iteminfo_tag_value = $this->request->getVar('iteminfo_tag_value');
		    
            $parameters         =  $this->request->getVar('parameters'); 
            $parameter_val      =  $this->request->getVar('parameter_val'); 
            $batchinfo			= $this->request->getVar('batchinfo'); 
			$unitdetail_array	= $this->request->getVar('unitdetail_array');
			$batchinfo_array    = $this->request->getVar('batchinfo_array'); 
            	

             $mcqtywise_qty     = $this->request->getVar('mcqtywise_qty'); 
			 $mcqtywise_avg     = $this->request->getVar('mcqtywise_avg'); 
			 $mcqtywise_fifo    = $this->request->getVar('mcqtywise_fifo'); 
			 $mcqtywise_lifo    = $this->request->getVar('mcqtywise_lifo'); 
			 $valmethod_id      = $this->request->getVar('valmethod_id'); 
			 
			 $batch_total_difference=0;	
			$rules = [				
				'item_name' => [
					'label'  => 'Item Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter item name',
					   ]]				  			   
			       ];
			
            if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }else{
               
			    	$total_batch_balance=array();
				    $insert_data   = [
						'comp_id'            => $this->company_id,
						'item_name'          => ucwords(clean($item_name)),
						'item_sku'           => $item_sku,
						'item_unit'          => $item_unit_id,
						'item_grp_id'        => $item_group_id,
						'item_alias'         => ucwords(clean($item_alias)),
						'item_print'         => $this->enc_string->nc_string(ucwords(clean($item_printname)),'en'),
						'item_upc'           => ucwords(clean($item_shortname)),
						'tax_id'             => $item_tax,
						'item_hsn'           => $item_hsn,
						'item_sales_acc'     => $item_sales_acc,
						'item_pur_acc'       => $item_pur_acc,						
						'item_cat'           => $item_catg_id,
						'bar_code'           => '',
                        'valmethod_id'       => $valmethod_id						
					  ];					 
				$response = $this->ItemsModel->add_item($insert_data);		 
			    if(!$response['status']){
					return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $response['message']]);
					
				  //  $this->message_output->set_error($response['message']);
			     }
			    else{
			        $item_id = $response['item_id'];
				
				
				//itmoppyval
			  $itmoppyval_tbl = $this->company_id.'_itmoppyval_'.$this->session->get('ses_comp_fy_id');		
			  if($item_unit_idm){	
			  foreach($item_unit_idm as $item_unit_val){
				  if($item_unit_val >0){
				  
				if($material_centre_dropdown){
					foreach($material_centre_dropdown as $mcid => $mcname){
						if($mcid>0){
						$mc_qty  =  $mcqtywise_qty[$mcid][0];
						$mc_avg  =  $mcqtywise_avg[$mcid][0];  // valuation method id 0
						$mc_fifo =  $mcqtywise_fifo[$mcid][0];  // valuation method id 1
						$mc_lifo =  $mcqtywise_lifo[$mcid][0];  // valuation method id 2
						
						
						 $itmoppyval_info1 = array("item_id"=>$item_id,"comp_id"=>$this->company_id ,"item_unit"=>$item_unit_val,
											   "op_bal_val"=>$mc_avg,"py_bal_val"=>"","method_id"=>"0","mat_cent_id"=>$mcid,
												"bo_id"=>$bo_id);
					      $this->ItemsModel->InsertTable($itmoppyval_tbl,$itmoppyval_info1);
						  
						  
						  $itmoppyval_info2 = array("item_id"=>$item_id,"comp_id"=>$this->company_id ,"item_unit"=>$item_unit_val,
											   "op_bal_val"=>$mc_fifo,"py_bal_val"=>"","method_id"=>"1","mat_cent_id"=>$mcid,
												"bo_id"=>$bo_id);
					      $this->ItemsModel->InsertTable($itmoppyval_tbl,$itmoppyval_info2);
						  
						  
						   $itmoppyval_info3 = array("item_id"=>$item_id,"comp_id"=>$this->company_id ,"item_unit"=>$item_unit_val,
											   "op_bal_val"=>$mc_lifo,"py_bal_val"=>"","method_id"=>"2","mat_cent_id"=>$mcid,
												"bo_id"=>$bo_id);
					      $this->ItemsModel->InsertTable($itmoppyval_tbl,$itmoppyval_info3);
					}
						
					  }
				    } 	
			     }		
		       }
			  }
				
			
			    //itmoppybal
				$itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');	
				if($item_unit_idm ){
					foreach($item_unit_idm  as $kk => $item_unit_val){
				      if($item_unit_val!='' ){
					      if($material_centre_dropdown){
					   foreach($material_centre_dropdown as $mcid => $mcname){
						  if($mcid>0){
					         $mc_qty  =  $mcqtywise_qty[$mcid][0];
							 
							
							 
				  	         $itmoppybal_info = array("item_id"=>$item_id,"comp_id"=>$this->company_id ,"item_unit"=>$item_unit_val,
											           "op_bal_qty"=>$mc_qty,"py_bal_qty"=>"","mat_cent_id"=>$mcid,'bo_id'=>$bo_id);
					         $this->ItemsModel->InsertTable($itmoppybal_tbl,$itmoppybal_info);
				             }
					      }					
				       }
					}
				}					
		     }				  
					
					// items dimensions
					if($dimension_txt){
						foreach($dimension_txt as $item_dmns_id => $item_dmns_val){
							if($item_dmns_val!=''){
								 $item_dmns_unit_id = $dimension_unit[$item_dmns_id];						 
								 $dimensions_info   = array("item_id"=>$item_id,"item_dmns_id"=>$item_dmns_id ,"item_dmns_val"=>$item_dmns_val,
															"item_dmns_unit_id"=>$item_dmns_unit_id);
								 $this->ItemsModel->add_item_dimensions($dimensions_info);
							   }
							}
						}
					// items info
					if($iteminfo_tag){
						foreach($iteminfo_tag as $line_id => $itmparamtr_line){
							if($itmparamtr_line!=''){
								 $iteminfotag_value = $iteminfo_tag_value[$line_id];
								 $item_info_val   = array("item_id"=>$item_id,"iteminfo_line"=>$itmparamtr_line ,"iteminfo_val"=>$iteminfotag_value,
														  "iteminfo_label"=>"Label ".($line_id+1));
								 $this->ItemsModel->add_item_info($item_info_val);
							   }
							}
						}
					// item parameters
					if($parameters){
						foreach($parameters as $key => $parameter_name){
							if($parameter_name!=''){
							     $parameter_val_new    = $parameter_val[$key];  
							     $parameter_info_val   = array("item_id"=>$item_id,"paramtr_name"=>$parameter_name);
								 $paramtr_id           = $this->ItemsModel->add_item_parameters($parameter_info_val);
								 
								 if($parameter_val_new){
									foreach($parameter_val_new as $prkey => $paramtr_val){ 
									    if($paramtr_val!=''){
								          $parameter_value_info = array("paramtr_id"=>$paramtr_id,"paramtr_val"=>$paramtr_val);
								          $this->ItemsModel->add_parameters_value($parameter_value_info);
									    }
									}
								 }								 
							}
						}
					}
			       if(!empty($batchinfo_array)){
					  $batchinfo_array  =  json_decode($batchinfo_array,true);
					  $batch_total_difference=0;
					  foreach($batchinfo_array as $brow){
							if($brow['batch_no']!=''){
							 $batch_mfr    = date("Y-m-d",strtotime($brow['manufacturing_date']));
							 $batch_expiry = date("Y-m-d",strtotime($brow['expiry_date']));
							 $batch_qty    = $brow['batch_qty'];
							 $batch_uom    = $brow['batch_uom_id'];
							  if(isset($brow['batch_difference'])) {							 
							 if(isset($total_batch_balance[$batch_uom]))
							 $total_batch_balance[$batch_uom] += $brow['batch_difference'];
						    else
							 $total_batch_balance[$batch_uom] = $brow['batch_difference'];
							  }
							  else
								$total_batch_balance[$batch_uom] = 0;
							
						 
							 if(isset($brow['batch_difference'])) 
							 $batch_total_difference = $batch_total_difference+$brow['batch_difference'];
							
						     $batch_value_info = array("batch_no"=>$brow['batch_no'],"batch_mfr"=>$batch_mfr,
						                                 "batch_expiry"=>$batch_expiry,"item_id"=>$item_id,
														 "batch_qty"=>$batch_qty,"batch_unit"=>$batch_uom);
						     $batch_id = $this->ItemsModel->add_batch_value($batch_value_info);
							 
							 if(abs($total_batch_balance[$batch_uom]) >0 ){
							$difference_info = array("item_id"=>$item_id,"comp_id"=>$this->company_id ,"item_unit"=>$batch_uom,
											         "op_bal_qty"=>abs($total_batch_balance[$batch_uom]),
													 "py_bal_qty"=>"","batch_id"=>$batch_id,'bo_id'=>$bo_id);
				             $this->ItemsModel->InsertTable($itmoppybal_tbl,$difference_info);
							 }
							 
						      }
						  
						  }
				      }
				  // undefined entry
				    $btchdata = array("batch_no"=>"UNDEFINED","item_id"=>$item_id,"batch_qty"=>abs($batch_total_difference));
				    $this->ItemsModel->CreateUndefinedBatch($btchdata,$item_id);
				   
			        $log = [
				            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
				            'log_date' => date('Y-m-d'),
				            'log_time' => date('H:i:s'),
				            'log_action_tags' => 'add',
				            'log_field_id' => $item_id,
				            'log_field_name' => $item_name,
				            'log_field_type' => 'items',
				        ];
				    $this->LogModel->add_log($log);
				    
			       
					$ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
					
					$item_txn_table_name = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$ses_comp_fy_id;					
					$this->ItemsModel->CreateItemTxnTable($item_txn_table_name);
                    
					// $item_txn_balance_tabl =$this->company_id.'_itemtxnbal_'.$item_id.'_'.$ses_comp_fy_id;					
					// $this->ItemsModel->CreateItemTxnBalanceTable($item_txn_balance_tabl);						
					
					$item_txn_valueation_tabl =$this->company_id.'_itemtxnvaln_'.$item_id.'_'.$ses_comp_fy_id;					
					$this->ItemsModel->CreateItemTxnValuationTable($item_txn_valueation_tabl);	
					
					
					$file  =     WRITEPATH.'comp'.$this->company_id.'/item.json';
			    if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
			        file_put_contents($file, ""); 
		            $item_list  = $this->ItemsModel->company_all_items();
		            $comp_folder    = 'comp'.$this->company_id;
		            $f = fopen($file, 'a');
                    fwrite($f,$item_list);
			     }
			     
			     
					 return json_encode(['status' => true, 'message' => 'Data Added']);
				   // return redirect()->to($this->base_url.'items/list_items');
				    //die;	
				  }				 
		    	}					 									
		   }						
		   
		$data['message_output']           = $this->message_output;
		$data['base_url']                 = $this->base_url;	
		$data['folder_path']              = $this->folder_path;	
        $data['item_group']               = $this->ItemsModel->items_group_dropdown($this->company_id);	
        $data['item_units']               = $this->ItemsModel->units_dropdown($this->company_id);		
		$data['item_category']            = $this->ItemsModel->category_dropdown($this->company_id);
		$data['material_centre_dropdown'] = $material_centre_dropdown;
		$data['sales_acc_dropdown']       = $this->ItemsModel->sales_acc_dropdown($this->company_id);
		$data['purchase_acc_dropdown']    = $this->ItemsModel->purchase_acc_dropdown($this->company_id);
		$data['product_dimensions']       = $this->ItemsModel->product_dimensions_list();		
		$data['tax_category']             = GSTTaxCategory();	
		
		
	    return view($this->folder_path.'items/add_item',$data);		
    }	
  
  public function modify_item($item_id)
    {    if($this->session->get('ses_boid')!='')
									$bo_id = $this->session->get('ses_boid');
								else 
									 $bo_id =1;
		 $material_centre_dropdown = $this->ItemsModel->material_centre_dropdown($this->company_id);
		 
		if(!$item_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
		
		 if($this->request->getMethod() == 'post'){	
			$item_name          = $this->request->getVar('item_name'); 
			$item_sku           = $this->request->getVar('item_sku'); 
			$item_alias         = $this->request->getVar('item_alias');
			$item_printname     = $this->request->getVar('item_printname');
			$item_shortname     = $this->request->getVar('item_shortname');
			$item_group_id      = $this->request->getVar('item_group_id'); 
			$item_unit_id       = $this->request->getVar('item_unit_id');
			$item_tax           = $this->request->getVar('item_tax');
			$item_ces           = $this->request->getVar('item_ces'); 
			$item_hsn           = $this->request->getVar('item_hsn');
			$item_sales_acc     = $this->request->getVar('item_sales_acc');
			$item_pur_acc       = $this->request->getVar('item_pur_acc');						
			$item_catg_id       = $this->request->getVar('item_catg_id');
			$item_op_bal_qty    = $this->request->getVar('item_op_bal_qty');
			$item_op_bal_val    = $this->request->getVar('item_op_bal_val');
			$item_op_val_basis  = $this->request->getVar('item_op_val_basis');
			
			$item_unit_idm       = $this->request->getVar('item_unit_idm');
		    $item_op_bal_qtym    = $this->request->getVar('item_op_bal_qtym');
			
			$dimension_txt      = $this->request->getVar('dimension_txt');
			$dimension_unit     = $this->request->getVar('dimension_unit');
			
			$iteminfo_tag       = $this->request->getVar('iteminfo_tag');
			$iteminfo_tag_value = $this->request->getVar('iteminfo_tag_value');
			
            $parameters         =  $this->request->getVar('parameters'); 
            $parameter_val      =  $this->request->getVar('parameter_val');
			$batchinfo			 = $this->request->getVar('batchinfo'); 
			
			$unitdetail_array	= $this->request->getVar('unitdetail_array');
			$batchinfo_array    = $this->request->getVar('batchinfo_array'); 
            $mcqtywise_qty     = $this->request->getVar('mcqtywise_qty'); 
			 $mcqtywise_avg     = $this->request->getVar('mcqtywise_avg'); 
			 $mcqtywise_fifo    = $this->request->getVar('mcqtywise_fifo'); 
			 $mcqtywise_lifo    = $this->request->getVar('mcqtywise_lifo'); 
			 $valmethod_id      = $this->request->getVar('valmethod_id'); 		
			
				
		    $total_batch_balance=array();
		     $batch_total_difference=0;	
			$rules 	= [				
						'item_name' => [
							'label'  => 'Item Name',
							'rules'  => 'required',
							'rules'  => "required",
							'errors' => [
								'required' => 'Please enter item name',
							   ]]				  			   
					      ];
			
            if(!$this->validate($rules)){
             $errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
			  
            }else{				
			
			
				
				 $update_data   = [
						'item_name'          => ucwords(clean($item_name)),
						'item_sku'           => $item_sku,
						'item_unit'          => $item_unit_id,
						'item_grp_id'        => $item_group_id,
						'item_alias'         => ucwords(clean($item_alias)),
						'item_print'         => $this->enc_string->nc_string(ucwords(clean($item_printname)),'en'),
						'item_upc'           => ucwords(clean($item_shortname)),
						'tax_id'             => $item_tax,
						'item_hsn'           => $item_hsn,
						'item_sales_acc'     => $item_sales_acc,
						'item_pur_acc'       => $item_pur_acc,						
						'item_cat'           => $item_catg_id,
						'bar_code'           => '',			
						'valmethod_id'       => $valmethod_id		
					   ];	
					  
				        $response = $this->ItemsModel->update_item($update_data,$item_id);
				    if(!$response['status']){
    				    return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $response['message']]);
    			     }
    			     else{					 
				 
				//itmoppyval
			  $itmoppyval_tbl = $this->company_id.'_itmoppyval_'.$this->session->get('ses_comp_fy_id');		
			  if($item_unit_idm){	
			  foreach($item_unit_idm as $item_unit_val){
				  if($item_unit_val >0){
				  // delete old entries first
					$this->ItemsModel->remove_item_itmoppyvalue($item_id); 
					
				if($material_centre_dropdown){
					foreach($material_centre_dropdown as $mcid => $mcname){
						if($mcid>0){
						$mc_qty  =  $mcqtywise_qty[$mcid][0];
						$mc_avg  =  $mcqtywise_avg[$mcid][0];  // valuation method id 0
						$mc_fifo =  $mcqtywise_fifo[$mcid][0];  // valuation method id 1
						$mc_lifo =  $mcqtywise_lifo[$mcid][0];  // valuation method id 2
						
						
								 
						
						 $itmoppyval_info1 = array("item_id"=>$item_id,"comp_id"=>$this->company_id ,"item_unit"=>$item_unit_val,
											   "op_bal_val"=>$mc_avg,"py_bal_val"=>"","method_id"=>"0","mat_cent_id"=>$mcid,
												"bo_id"=>$bo_id);
					      $this->ItemsModel->InsertTable($itmoppyval_tbl,$itmoppyval_info1);
						  
						  
						  $itmoppyval_info2 = array("item_id"=>$item_id,"comp_id"=>$this->company_id ,"item_unit"=>$item_unit_val,
											   "op_bal_val"=>$mc_fifo,"py_bal_val"=>"","method_id"=>"1","mat_cent_id"=>$mcid,
												"bo_id"=>$bo_id);
					      $this->ItemsModel->InsertTable($itmoppyval_tbl,$itmoppyval_info2);
						  
						  
						   $itmoppyval_info3 = array("item_id"=>$item_id,"comp_id"=>$this->company_id ,"item_unit"=>$item_unit_val,
											   "op_bal_val"=>$mc_lifo,"py_bal_val"=>"","method_id"=>"2","mat_cent_id"=>$mcid,
												"bo_id"=>$bo_id);
					      $this->ItemsModel->InsertTable($itmoppyval_tbl,$itmoppyval_info3);
					}
						
					  }
				    } 	
			     }		
		       }
			  }
				
			
			
			    //itmoppybal
				$itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');	
				if($item_unit_idm ){
					// delete old entries first
					$this->ItemsModel->remove_item_itmoppybal($item_id); 
					
					foreach($item_unit_idm  as $kk => $item_unit_val){
				      if($item_unit_val!='' ){
					      if($material_centre_dropdown){
					   foreach($material_centre_dropdown as $mcid => $mcname){
						  if($mcid>0){
					         $mc_qty  =  $mcqtywise_qty[$mcid][0];
				  	         $itmoppybal_info = array("item_id"=>$item_id,"comp_id"=>$this->company_id ,"item_unit"=>$item_unit_val,
											           "op_bal_qty"=>$mc_qty,"py_bal_qty"=>"","mat_cent_id"=>$mcid,"bo_id"=>$bo_id);
					         $this->ItemsModel->InsertTable($itmoppybal_tbl,$itmoppybal_info);
				             }
					      }					
				       }
					}
				}					
		     }
			 
			 
					
				if(!empty($batchinfo_array)){
					  $batchinfo_array  =  json_decode($batchinfo_array,true);
					  $batch_total_difference=0;
					  foreach($batchinfo_array as $brow){
							if($brow['batch_no']!='' && $brow['batch_no']!='UNDEFINED'){
							 $batch_mfr    = date("Y-m-d",strtotime($brow['manufacturing_date']));
							 $batch_expiry = date("Y-m-d",strtotime($brow['expiry_date']));
							 $batch_qty    = $brow['batch_qty'];
							 $batch_uom    = $brow['batch_uom_id'];
							  if(isset($brow['batch_difference'])) {							 
							 if(isset($total_batch_balance[$batch_uom]))
							 $total_batch_balance[$batch_uom] += $brow['batch_difference'];
						    else
							 $total_batch_balance[$batch_uom] = $brow['batch_difference'];
							  }
							  else
								$total_batch_balance[$batch_uom] = 0;
							
						 
							 if(isset($brow['batch_difference'])) 
							 $batch_total_difference = $batch_total_difference+$brow['batch_difference'];
							
						     $batch_value_info = array("batch_no"=>$brow['batch_no'],"batch_mfr"=>$batch_mfr,
						                                 "batch_expiry"=>$batch_expiry,"item_id"=>$item_id,
														 "batch_qty"=>$batch_qty,"batch_unit"=>$batch_uom);
						     $batch_id = $this->ItemsModel->add_batch_value($batch_value_info);
							 
							 if(abs($total_batch_balance[$batch_uom]) >0 ){
								$isexists =  $this->ItemsModel->ItmbatchDifrExist($itmoppybal_tbl,$item_id,$batch_uom,$batch_id);
								if($isexists){
								$difference_info = array("op_bal_qty"=>abs($total_batch_balance[$batch_uom]));	
								$this->ItemsModel->UpdateOpBalBatchTable($itmoppybal_tbl,$item_id,$batch_uom,$batch_id,$difference_info);	
								}
							   else{
							   $difference_info = array("item_id"=>$item_id,"comp_id"=>$this->company_id ,"item_unit"=>$batch_uom,
											"op_bal_qty"=>abs($total_batch_balance[$batch_uom]),"py_bal_qty"=>"","batch_id"=>$batch_id);
							    $this->ItemsModel->UpdateOpBalBatchTable($itmoppybal_tbl,$item_id,$batch_uom,$batch_id,$difference_info);
								
							       }
							     }
							 
						      }
						  
						  }
						// update undefined entry
				    $btchdata = array("batch_qty"=>abs($batch_total_difference));
				    $this->ItemsModel->update_undefined_batch($btchdata,$item_id);  
				      }
				  
					
				  
					// items dimensions
					if($dimension_txt){
						// delete old entries first
					$this->ItemsModel->remove_item_dimentions($item_id);
					
						foreach($dimension_txt as $item_dmns_id => $item_dmns_val){
							if($item_dmns_val!=''){
								 $item_dmns_unit_id = $dimension_unit[$item_dmns_id];						 
								 $dimensions_info   = array("item_id"=>$item_id,"item_dmns_id"=>$item_dmns_id ,"item_dmns_val"=>$item_dmns_val,
															"item_dmns_unit_id"=>$item_dmns_unit_id);
								 $this->ItemsModel->add_item_dimensions($dimensions_info);
							   }
							}
						}
					// items info
					if($iteminfo_tag){
						// delete old entries first
					$this->ItemsModel->remove_items_info($item_id);
					
						foreach($iteminfo_tag as $line_id => $itmparamtr_line){
							if($itmparamtr_line!=''){
								 $iteminfotag_value = $iteminfo_tag_value[$line_id];
								 $item_info_val   = array("item_id"=>$item_id,"iteminfo_line"=>$itmparamtr_line ,"iteminfo_val"=>$iteminfotag_value,
														  "iteminfo_label"=>"Label ".($line_id+1));
								 $this->ItemsModel->add_item_info($item_info_val);
							   }
							}
						}
					// item parameters
					if($parameters){
						// delete old entries first
					$this->ItemsModel->remove_item_parameters($item_id);
						foreach($parameters as $key => $parameter_name){
							if($parameter_name!=''){
							     $parameter_val_new    = $parameter_val[$key];  
							     $parameter_info_val   = array("item_id"=>$item_id,"paramtr_name"=>$parameter_name);
								 $paramtr_id           = $this->ItemsModel->add_item_parameters($parameter_info_val);
								 
								 if($parameter_val_new){
									foreach($parameter_val_new as $prkey => $paramtr_val){ 
									    if($paramtr_val!=''){
								          $parameter_value_info = array("paramtr_id"=>$paramtr_id,"paramtr_val"=>$paramtr_val);
								          $this->ItemsModel->add_parameters_value($parameter_value_info);
									    }
									}
								 }								 
							}
						}
					}
						/*
    				    $ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
    				   	$item_txn_table_name = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$ses_comp_fy_id; */
    				   
					   
					   
					   // $this->ItemsModel->update_txn_entries($item_txn_table_name,$item_op_bal_val,$item_op_bal_qty);
    				  
    				    $log = [
    				            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    				            'log_date' => date('Y-m-d'),
    				            'log_time' => date('H:i:s'),
    				            'log_action_tags' => 'update',
    				            'log_field_id' => $item_id,
    				            'log_field_name' => $item_name,
    				            'log_field_type' => 'items',
    				          ];
    				    $this->LogModel->add_log($log);
    				    
    				    $file  =     WRITEPATH.'comp'.$this->company_id.'/item.json';
			    if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
			        file_put_contents($file, ""); 
		            $item_list  = $this->ItemsModel->company_all_items();
		            $comp_folder    = 'comp'.$this->company_id;
		            $f = fopen($file, 'a');
                    fwrite($f,$item_list);
			     }
			      return json_encode(['status' => true, 'message' => 'Data Updated']);
    			      //  return redirect()->to($this->base_url.'items/list_items');
    				    die;
    			     }
				    
				    		 
		    	}					 									
		   }		
		  $ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
		  $get_item_detail_info = $this->ItemsModel->get_item_detail_info($item_id,$this->company_id);
		  if(!$get_item_detail_info)
		  	throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		  	
		  	$opntable_name = $this->company_id.'_itmoppybal_'.$ses_comp_fy_id;
			$itmoppyval_name = $this->company_id.'_itmoppyval_'.$ses_comp_fy_id;
			$item_itemdimens_info = $this->ItemsModel->get_item_itemdimens_info($item_id);
			$get_item_iteminfonn_info = $this->ItemsModel->get_item_iteminfonn_info($item_id);
			
			$get_item_itmparamtr_info = $this->ItemsModel->get_item_itmparamtr_info($item_id);
		
			
			 $data['get_item_batch']           = $this->ItemsModel->get_item_batch($item_id);
			 
			
		    $data['GetOpnBalanItm']           = $this->ItemsModel->GetOpnBalanItm($opntable_name,$item_id);
			$data['GetOpnValueItm']           = $this->ItemsModel->GetOpnValueItm($itmoppyval_name,$item_id);
			
			$data['message_output']           = $this->message_output;
			$data['folder_path']              = $this->folder_path;	
			
			$data['item_itemdimens_info']     = $item_itemdimens_info;
            $data['iteminfonn_info']          = $get_item_iteminfonn_info;
            $data['itmparamtr_info']          = $get_item_itmparamtr_info;				
			
			$data['item_info']                = $get_item_detail_info;	
			$data['item_group']               = $this->ItemsModel->items_group_dropdown($this->company_id);	
			$data['item_units']               = $this->ItemsModel->units_dropdown($this->company_id);		
			$data['item_category']            = $this->ItemsModel->category_dropdown($this->company_id);
			$data['material_centre_dropdown'] = $material_centre_dropdown;
			$data['tax_category']             = GSTTaxCategory();
			$data['enc_string']               = $this->enc_string;
			$data['base_url']                 = $this->base_url;	
			$data['item_id']                  = $item_id;		
			$data['sales_acc_dropdown']       = $this->ItemsModel->sales_acc_dropdown($this->company_id);
			$data['purchase_acc_dropdown']    = $this->ItemsModel->purchase_acc_dropdown($this->company_id);
			$data['product_dimensions']       = $this->ItemsModel->product_dimensions_list();		
	    return view($this->folder_path.'items/edit_item',$data);		
    }		

  public function ajax_list_groups()
	{
	  
		 if(isset($_POST["pq_curpage"]))
		$pq_curPage = (int)$_POST["pq_curpage"];
	   else
		  $pq_curPage = 1;
	  
	  if(isset($_POST["pq_rpp"]))
		$limit     = (int)$_POST["pq_rpp"];
	  else
		 $limit     = 100; 
	 	$response = $this->ItemsModel->ajax_group_list($limit,$pq_curPage);

		return json_encode([
					'totalRecords' => $response['total_records'],
					'data'	=> $response['data'],
					'curPage' => $pq_curPage
			]);
	}
	
  public function list_group()
    {
		if(isset($_GET['grpcmpid']) && $_GET['grpcmpid']!=''){
			 $comp_id_info  = unobfuscate_link($_GET['grpcmpid']);
			 $comp_id       = $comp_id_info[1];			 
			 $this->CommonModel->choose_grp_company($comp_id);
			 $grpcmpid     = $_GET['grpcmpid'];
		}
		else{
			$this->session->set('ses_company_id','');
			$grpcmpid='';
		}
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['admin_url']        = $this->admin_url;
		$data['session']         = $this->session;
		$data['grpcmpid']        = $grpcmpid;
		$group_comp_list         = $this->CommonModel->all_group_companies();
		$comp_list = array();
		$comp_list[''] = 'Choose Group Company';
		if($group_comp_list){
			foreach($group_comp_list as $row){
				$comp_list[$row['encomp_id']]=$row['company_name'];	
			}
		}
        $data['comp_list'] = $comp_list;       	        		
		return view($this->folder_path.'items/list_group',$data);		
    } 
   
   public function add_group()
    {
		 if($this->request->getMethod() == 'post'){	
		    $group_name        = $this->request->getVar('group_name'); 
		    $group_name_alias  = $this->request->getVar('group_name_alias');
			$primary_group     = $this->request->getVar('primary_group');	
			$primary_group     = $this->request->getVar('primary_group');
			
		   	if($primary_group=='Y'){
			  $acc_grp_parent_id    = 0;
			  $under_acc_grp_id     = 0;
		   	}
			elseif($primary_group=='N'){
			    $under_acc_grp_id    = $this->request->getVar('no_group_under');
			    $acc_grp_parent_id   =0;
			}
			
			
			$rules = [				
				    'group_name' => [
					'label'  => 'Group Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter group name',
					     ]
					   ]	   
			       ];
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{				
				 $insert_data   = [
						'comp_id'            => $this->company_id,
						'item_grp_name'      => $this->enc_string->nc_string(ucwords(clean($group_name)),'en'),
						'item_grp_alias'     => $this->enc_string->nc_string(ucwords(clean($group_name_alias)),'en'),
						'item_grp_primary'   => $primary_group,
						'item_grp_parent_id' => $acc_grp_parent_id,
						'under_acc_grp_id'   => $under_acc_grp_id
					   ];				
			    $exists = $this->ItemsModel->add_group($insert_data);
			   if($exists=="0"){
				  $this->message_output->set_error('Group name already exists.');
			   }
				else{	
				    $log = [
				            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
				            'log_date' => date('Y-m-d'),
				            'log_time' => date('H:i:s'),
				            'log_action_tags' => 'add',
				            'log_field_id' => $exists,
				            'log_field_name' => $group_name,
				            'log_field_type' => 'item group',
				        ];
				    $this->LogModel->add_log($log);
				    
					 return redirect()->to($this->base_url.'items/list_group');
					 die;	
				  }				 
		    	}					 									
		   }			
		$data['message_output']           = $this->message_output;
		$data['folder_path']              = $this->folder_path;	
		$data['base_url']                 = $this->base_url;
	
	    $data['user_groups_dropdown']     = $this->ItemsModel->group_main_dropdown();
	    return view($this->folder_path.'items/add_group',$data);		
    }
   
   public function modify_group($group_id)
    {
		if(!$group_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 

		 if($this->request->getMethod() == 'post'){	
		    $group_name       = $this->request->getVar('group_name'); 
		    $group_name_alias = $this->request->getVar('group_name_alias');
				$primary_group    = $this->request->getVar('primary_group');
				$rules = [				
				'group_name' => [
					'label'  => 'Group Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter group name',
					   ]]				  			   
			       ];
			
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{				
                
                	if($primary_group=='Y'){
        			  $acc_grp_parent_id    = 0;
        			  $under_acc_grp_id     = 0;
        		   	}
        			elseif($primary_group=='N'){
        			    $under_acc_grp_id    = $this->request->getVar('no_group_under');
        			    $acc_grp_parent_id   =0;
        			}
        			
			
				$update_data   = [
				        'item_grp_name'    => $this->enc_string->nc_string(ucwords(clean($group_name)),'en'),
						'item_grp_alias'   => $this->enc_string->nc_string(ucwords(clean($group_name_alias)),'en'),
						'item_grp_primary' => $primary_group,
						'item_grp_parent_id' => $acc_grp_parent_id,
						'under_acc_grp_id'   => $under_acc_grp_id
					  ];
			        $this->ItemsModel->update_group($update_data,$group_id);
			        
			        $log = [
				            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
				            'log_date' => date('Y-m-d'),
				            'log_time' => date('H:i:s'),
				            'log_action_tags' => 'update',
				            'log_field_id' => $group_id,
				            'log_field_name' => $group_name,
				            'log_field_type' => 'item group',
				        ];
				    $this->LogModel->add_log($log);
				    
					return redirect()->to($this->base_url.'items/list_group');
					die;		 
		    	}					 									
		   }	 


		 $item_group_info = $this->ItemsModel->item_group_info($group_id,$this->company_id);
		 if(!$item_group_info)
		 		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		   
		$data['get_info']         = $item_group_info;
		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['group_id']         = $group_id;		
        $data['base_url']         = $this->base_url;	
        $data['enc_string']       = $this->enc_string;	
        $data['user_groups_dropdown']     = $this->ItemsModel->group_main_dropdown();
	    return view($this->folder_path.'items/edit_group',$data);		
    }
   
 public function remove_groups($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'items/list_group'); 
		 
		 $default_groups = [];
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 foreach($ids2 as $item_id){
		     
		    $stat = true;
		    $name = $this->ItemsModel->get_item_group_name($item_id);;
		     if (in_array($item_id, $default_groups)){ //check if group belongs to defaults
		         $stat = false;
		         array_push($errors, 'Failed! Item Group "'.$name.'" belongs to Default Group');
		     }
		     if ($this->ItemsModel->check_item_with_group($item_id)){ //check if account exist with that group
		         $stat = false;
		         array_push($errors, 'Failed! Item Group "'.$name.'" has one or more associated items');
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
    		            'log_field_type' => 'item group',
    		            ];
    		     $this->LogModel->add_log($log);
    		     
    		     $this->ItemsModel->remove_single_groups($item_id);
		     }
		     
		 }
		 
		 if(count($errors))
		 {
             $this->session->setFlashdata('error_array_message', $errors);
		 }
		 
		 
		return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);		
	  }   

    public  function ajax_category_view()
	 {
		echo $response =  $this->ItemsModel->ajax_category_list();	
		
	 } 
	 

  public function stock_category()
    {
	if(isset($_GET['grpcmpid']) && $_GET['grpcmpid']!=''){
			 $comp_id_info  = unobfuscate_link($_GET['grpcmpid']);
			 $comp_id       = $comp_id_info[1];			 
			 $this->CommonModel->choose_grp_company($comp_id);
			 $grpcmpid     = $_GET['grpcmpid'];
		}
		else{
			$this->session->set('ses_company_id','');
			$grpcmpid='';
		}
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['admin_url']        = $this->admin_url;
		$data['session']         = $this->session;
		$data['grpcmpid']        = $grpcmpid;
		$group_comp_list         = $this->CommonModel->all_group_companies();
		$comp_list = array();
		$comp_list[''] = 'Choose Group Company';
		if($group_comp_list){
			foreach($group_comp_list as $row){
				$comp_list[$row['encomp_id']]=$row['company_name'];	
			}
		}
        $data['comp_list'] = $comp_list;		
		return view($this->folder_path.'items/list_category',$data);		
    } 
      
   public function add_category()
    {
		 if($this->request->getMethod() == 'post'){	
		    $item_cat = $this->request->getVar('item_cat'); 
		    $item_cat_alias = $this->request->getVar('item_cat_alias'); 
			$rules = [				
				    'item_cat' => [
					'label'  => 'Category Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter category name',
					     ]
					   ]	   
			       ];
				   
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{				
				 $insert_data   = [
						'comp_id'    => $this->company_id,
						'item_cat'   => $this->enc_string->nc_string(ucwords(clean($item_cat)),'en'),
						'item_cat_alias'   => ucwords(clean($item_cat_alias))
					   ];				
			     $exists = $this->ItemsModel->add_category($insert_data);
			     if($exists=="0"){
				  $this->message_output->set_error('Category name already exists.');
			     }
			     else{
        			   $log = [
            		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
            		            'log_date' => date('Y-m-d'),
            		            'log_time' => date('H:i:s'),
            		            'log_action_tags' => 'add',
            		            'log_field_id' => $exists,
            		            'log_field_name' => $this->ItemsModel->get_item_category_name($exists),
            		            'log_field_type' => 'stock category',
            		            ];
            		     $this->LogModel->add_log($log);
					 return redirect()->to($this->base_url.'items/stock_category');
					 die;	
				   }				 
		    	}					 									
		   }							 
		$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url;
		$data['folder_path']      = $this->folder_path;			
	    return view($this->folder_path.'items/add_category',$data);		
    }
	
	 public function remove_catgeory($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'items/stock_category');
			
		 $default_groups = [];
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 foreach($ids2 as $item_cat_id){
		     
		    $stat = true;
		    $name = $this->ItemsModel->get_item_category_name($item_cat_id);
		    
		     if (in_array($item_cat_id, $default_groups)){ //check if group belongs to defaults
		         $stat = false;
		         array_push($errors, 'Failed! Item Category "'.$name.'" belongs to Default Categories');
		     }
		     if ($this->ItemsModel->check_item_with_category($item_cat_id)){ //check if account exist with that group
		         $stat = false;
		         array_push($errors, 'Failed! Item Category "'.$name.'" has one or more associated items');
		     }
		     
		     if($stat)
		     {
    		     $log = [
    		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    		            'log_date' => date('Y-m-d'),
    		            'log_time' => date('H:i:s'),
    		            'log_action_tags' => 'delete',
    		            'log_field_id' => $item_cat_id,
    		            'log_field_name' => $name,
    		            'log_field_type' => 'stock category',
    		            ];
    		     $this->LogModel->add_log($log);
    		     
    		     $this->ItemsModel->remove_single_category($item_cat_id);
		     }
		     
		 }
		 
		 if(count($errors))
		 {
             $this->session->setFlashdata('error_array_message', $errors);
		 }
		return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);		
	  } 
	  
	public function duplicate_item($item_id)
    {
		if(!$item_id)
			return redirect()->to($this->base_url.'items/list_items');
		 $id = $this->ItemsModel->duplicate_item($item_id,$this->company_id);
		 $log = [
	            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
	            'log_date' => date('Y-m-d'),
	            'log_time' => date('H:i:s'),
	            'log_action_tags' => 'add',
	            'log_field_id' => $id,
	            'log_field_type' => 'items',
	        ];
	    $this->LogModel->add_log($log);
				    
		 return redirect()->to($this->base_url.'items/list_items');
		 die;	
		 
	}
	
	public function modify_category($category_id)
    {
		 if($this->request->getMethod() == 'post'){	
		    $item_cat        = $this->request->getVar('item_cat'); 
		    $item_cat_alias = $this->request->getVar('item_cat_alias'); 
			$rules = [				
				    'item_cat' => [
					'label'  => 'Category Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter category name',
					     ]
					   ]	   
			       ];
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{				
				 $update_data   = [
						'item_cat' => $this->enc_string->nc_string(ucwords(clean($item_cat)),'en'),
						'item_cat_alias'   => ucwords(clean($item_cat_alias))
					   ];				
			   $this->ItemsModel->modify_category($update_data,$category_id);
			   $log = [
    		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    		            'log_date' => date('Y-m-d'),
    		            'log_time' => date('H:i:s'),
    		            'log_action_tags' => 'update',
    		            'log_field_id' => $category_id,
    		            'log_field_name' => $this->ItemsModel->get_item_category_name($category_id),
    		            'log_field_type' => 'stock category',
    		            ];
    		     $this->LogModel->add_log($log);
    		     
			   $this->message_output->set_success('Record Updated successfully');					
			   return redirect()->to($this->base_url.'items/stock_category');
		  	   die;	
				  				 
		    	}					 									
		   }			
				 
		   $item_category_info = $this->ItemsModel->item_category_info($category_id,$this->company_id);
		   if(!$item_category_info)
		   		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();


		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;	
		$data['base_url']         = $this->base_url;   
        $data['category_info']    = $item_category_info;	
		$data['category_id']      = $category_id;
		$data['enc_string']       = $this->enc_string;	        
	    return view($this->folder_path.'items/edit_category',$data);		
    }
}