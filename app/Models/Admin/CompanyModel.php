<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;

class CompanyModel extends Model	{
    protected $session;
	protected $company_id;
	protected $comp_fy_id;
	protected $externaldb;
	protected $erp_db;
	protected $aicountly_db;
	
	public function __construct() {
		parent::__construct();
		$this->session      = \Config\Services::session();
		$this->company_id   = $this->session->get('ses_company_id');
		$this->comp_fy_id   = $this->session->get('ses_comp_fy_id');
		$this->externaldb   = new externaldb();
		$this->erp_db       = $this->externaldb->erp_db();
		$this->aicountly_db = $this->externaldb->aicountly_db();	
		$this->myaicountly_pg = $this->externaldb->myaicountly_pg_db();
		$this->univaictly    =  $this->externaldb->univaictly_db();
	}

	/****************************** FY CHANGE START **********************************/  


	function get_sticky_notes()
	{
		$notes = [
			[
				'primary' => 1,
				'heading' => 'Note 1',
				'details' => '',
				'color'		=> '#DBFFD2',
			],
			[
				'primary' => 2,
				'heading' => 'Note 2',
				'details' => '',
				'color'		=> '#DBF3FB',
			],
			[
				'primary' => 3,
				'heading' => 'Note 3',
				'details' => '',
				'color'		=> '#FFF8D7',
			],
			[
				'primary' => 4,
				'heading' => 'Note 4',
				'details' => '',
				'color'		=> '#FBE2D2',
			],
			[
				'primary' => 5,
				'heading' => 'Note 5',
				'details' => '',
				'color'		=> '#FFEAED',
			],
		];
		try{

		$compnotesn_tbl = $this->company_id.'_compnotesn_'.$this->session->get('ses_comp_fy_id');

		foreach ($notes as $key => $value) {

			$result = $this->db->table($compnotesn_tbl)
											->where('note_primary', $value['primary'])
											->get()->getRowArray();
			if($result){
				$notes[$key]['heading'] = $result['note_heading'];
				$notes[$key]['details'] = $result['note_details'];
			}
		}

		}
		catch(\Exception $e){

		}
		
		return $notes;
	}

	/* public function updateFYStatus($fy_id){
		if($fy_id>0){   
			$this->aicountly_db->table("aicountly_cmpfymastr_univdb")
			->where('comp_fy_id',$fy_id)
			->where('comp_id',$this->company_id)->update(array('is_imported'=>'1'));   
		}

	} */
	public function GetTableName($company_id,$table_name,$fy_id){
		return $company_id.$table_name.$fy_id;  	   
	}

	public function GetAccountBalance($account_id){
		$final_data     = array(); 
		$current_fy_id  = $this->session->get('ses_comp_fy_id'); 
		$hobomaster_tbl = $this->company_id.'_hobomaster_'.$current_fy_id;
		$accoppybal_tbl = $this->company_id.'_accoppybal_'.$current_fy_id;
		$txn_tablename  = $this->company_id.'_accnttxnnn_'.$account_id.'_'.$current_fy_id;

		$bo_data = $this->db->table($hobomaster_tbl)
		->select('bo_id,comp_id')
		->where('comp_id', $this->company_id)
		->get()->getResultArray();
		if($bo_data){
			foreach($bo_data as $brow){
				$bo_id = $brow['bo_id'];

				$accoppybal_data = $this->db->table($accoppybal_tbl)
				->select('bo_id,acc_id,acc_op_bal')
				->where('acc_id', $account_id)
				->where('bo_id', $bo_id)
				->get()->getRowArray();


				$builder = $this->db->table($txn_tablename);
				$builder->where('comp_id',$this->company_id);
				$builder->where('acc_id',$account_id);
				$builder->where('bo_id',$bo_id);
				$builder->orderBy('acc_txn_date','DESC');	 
				$builder->orderBy('voucher_txn_id','DESC');	 
				$builder->orderBy('acc_txn_id','DESC');	 		  
				$builder->limit("1");
				$result = $builder->get()->getRowArray();
				if($result)
					$balance = $result['acc_bal'];
				else if($accoppybal_data)
					$balance= $accoppybal_data['acc_op_bal'];		
				else
					$balance=0;

				$final_data[]=array('acc_id'=>$account_id,'acc_py_bal'=>$balance,'acc_op_bal'=>$balance,'bo_id'=>$bo_id);			

			} 		  
		}						  
		return $final_data;				  
	}
	
	
	function get_fy_details($comp_fy_id,$comp_id){		
		$response =  $this->db->table('cmpfymastr')->where('cmpfymastr_id', $comp_fy_id)->where('cmp_id', $comp_id)->get()->getRowArray();   	   
	
		return $response;
	}

	public function GetBillSundryBalance($account_id){
		$final_data     = array(); 
		$current_fy_id  = $this->session->get('ses_comp_fy_id'); 
		$hobomaster_tbl = $this->company_id.'_hobomaster_'.$current_fy_id;
		$accoppybal_tbl = $this->company_id.'_bsdoppybal_'.$current_fy_id;
		$txn_tablename  = $this->company_id.'_sundrytxnn_'.$account_id.'_'.$current_fy_id;

		$bo_data = $this->db->table($hobomaster_tbl)
		->select('bo_id,comp_id')
		->where('comp_id', $this->company_id)
		->get()->getResultArray();
		if($bo_data){
			foreach($bo_data as $brow){
				$bo_id = $brow['bo_id'];

				$accoppybal_data = $this->db->table($accoppybal_tbl)
				->select('bo_id,bill_sundry_id,bsd_op_bal')
				->where('bill_sundry_id', $account_id)
				->where('bo_id', $bo_id)
				->get()->getRowArray();			 		  

				$builder = $this->db->table($txn_tablename);
				$builder->where('comp_id',$this->company_id);
				$builder->where('bill_sundry_id',$account_id);
				$builder->where('bo_id',$bo_id);
				$builder->orderBy('sundry_txn_date','DESC');	 
				$builder->orderBy('voucher_txn_id','DESC');	 
				$builder->orderBy('sundry_txn_id','DESC');	 		  
				$builder->limit("1");
				$result = $builder->get()->getRowArray();
				if($result)
					$balance = $result['bill_sundry_id'];
				else if($accoppybal_data)
					$balance= $accoppybal_data['bsd_op_bal'];		
				else
					$balance=0;

				$final_data[]=array('bill_sundry_id'=>$account_id,'bsd_op_bal'=>$balance,'bsd_py_bal'=>$balance,'bo_id'=>$bo_id);			

			} 		  
		}						  
		return $final_data;				  
	}

	function get_company_mc(){
		$comp_mtcnt_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$data           = $this->db->table($comp_mtcnt_tbl)->where('comp_id', $this->company_id)->orderBy('mat_cent_name','ASC')->get()->getResultArray();
		$final_result   = array();
		if($data){
			foreach($data as $row){
              $name = '';//$this->enc_string->nc_string($row['mat_cent_name'],'de');	
              $final_result[] = [
              	'name' => ucwords($name),
              	'id'    => $row['mat_cent_id']
              ];
            }
          }
          return $final_result;	
        }

        public function GetItemUnits($item_id){
	  	// check item default unit
	 	// check item opening units
    	// check item txn units

        	$unit_result = [];
        	$list = [];

        	$itemtxnnnn_tbl =  $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
        	$itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');
        	$itemmaster_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');

        	$itemmaster = $this->db->table($itemmaster_tbl)->where('item_id', $item_id)->get()->getRowArray();
        	$unit_result[] = $itemmaster['item_unit'];

        	$builder = $this->db->table($itmoppybal_tbl);
        	$builder->select('item_unit');
        	$builder->where('item_id', $item_id);
        	$builder->groupBy('item_unit');
        	$result = $builder->get()->getResultArray();
        	if($result){
        		foreach ($result as $key => $value) {
        			if(!in_array($value['item_unit'], $unit_result)){
        				$unit_result[] = $value['item_unit'];
        			}
        		}
        	}

        	$builder = $this->db->table($itemtxnnnn_tbl);
        	$builder->select('item_unit');
        	$builder->where('item_id', $item_id);
        	$builder->groupBy('item_unit');
        	$result = $builder->get()->getResultArray();
        	if($result){
        		foreach ($result as $key => $value) {
        			if(!in_array($value['item_unit'], $unit_result)){
        				$unit_result[] = $value['item_unit'];
        			}
        		}
        	}

        	foreach ($unit_result as $unit_key => $unit_value) {

        		$unit = '';
			// $item_unit_info = $this->get_units_info($unit_value,$this->company_id);
			// if(isset($item_unit_info['item_unit']))
			//    $unit = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');

        		$list[] = [
        			'unit_id' => $unit_value,
        			'unit_name' => $unit,
        		];

        	}

        	return $list;

        } 

        public function GetItemBalance($item_id){

        	$item_val_id_arr = [1,2,3];

        	$final_op_bal_result     = array();
        	$final_op_val_result     = array();

        	$current_fy_id  = $this->session->get('ses_comp_fy_id'); 
        	$hobomaster_tbl = $this->company_id.'_hobomaster_'.$current_fy_id;
        	$accoppybal_tbl = $this->company_id.'_itmoppybal_'.$current_fy_id;
        	$txn_tablename  = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$current_fy_id;
        	$itemtxnval_tbl  = $this->company_id.'_itemtxnval_'.$item_id.'_'.$current_fy_id;

        	$itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');
        	$itmoppyval_tbl = $this->company_id.'_itmoppyval_'.$this->session->get('ses_comp_fy_id');

        	$bo_data        = $this->db->table($hobomaster_tbl)
        	->select('bo_id,comp_id')
        	->where('comp_id', $this->company_id)
        	->get()->getResultArray();
        	if($bo_data){
        		foreach($bo_data as $brow1){
        			$bo_id = $brow1['bo_id'];
		   // mc loop
        			$company_all_mc = $this->get_company_mc();
        			if($company_all_mc){
        				foreach($company_all_mc as $brow2){

        					$mc_id = $brow2['id'];
					// all units loop
        					$all_item_units = $this->GetItemUnits($item_id);

        					foreach ($all_item_units as $brow3) {

        						$unit_id = $brow3['unit_id'];

        						$item_qty = 0;
        						$item_val_arr = [];
        						foreach ($item_val_id_arr as $item_val_id) {
        							$item_val_arr[$item_val_id] = 0;
        						}

        						$builder = $this->db->table($txn_tablename);
        						$builder->where('item_id', $item_id);
        						$builder->where('item_unit', $unit_id);
        						$builder->where('mat_cent_id', $mc_id);
        						$builder->where('batch_id', 0);
        						$builder->where('item_avail', 1);

        						$builder->where('bo_id', $bo_id);
        						$builder->orderBy('item_txn_date', 'desc');
        						$builder->orderBy('voucher_txn_id', 'desc');
        						$builder->orderBy('item_txn_id', 'desc');
        						$builder->limit(1);
        						$transaction = $builder->get()->getRowArray();
        						if($transaction){
        							$item_qty = floatval($transaction['item_bal_qty']);

        							foreach ($item_val_id_arr as $item_val_id) {

        								$builder = $this->db->table($itemtxnval_tbl);
        								$builder->where('item_id', $item_id);
        								$builder->where('item_txn_id', $transaction['item_txn_id']);
        								$builder->where('method_id', $item_val_id);
        								$itemtxnval = $builder->get()->getRowArray();
        								if($itemtxnval){
        									$item_val_arr[$item_val_id] = floatval($itemtxnval['item_value']);
        								}
        							}
        						}
        						else{
        							$builder = $this->db->table($itmoppybal_tbl); 
        							$builder->where('item_id', $item_id);
        							$builder->where('item_unit', $unit_id);
        							$builder->where('mat_cent_id', $mc_id);

        							$builder->where('bo_id', $bo_id);
        							$builder->where('batch_id', 0);
        							$itmoppybal = $builder->get()->getRowArray();
        							if($itmoppybal){
        								$item_qty = floatval($itmoppybal['op_bal_qty']);

        								foreach ($item_val_id_arr as $item_val_id) {

        									$builder = $this->db->table($itmoppyval_tbl); 
        									$builder->where('item_id', $item_id);
        									$builder->where('item_unit', $unit_id);
        									$builder->where('mat_cent_id', $mc_id);

        									$builder->where('bo_id', $bo_id);
        									$builder->where('batch_id', 0);
        									$builder->where('method_id', $item_val_id);
        									$itmoppyval = $builder->get()->getRowArray();

        									if($itmoppyval){
        										$item_val_arr[$item_val_id] = floatval($itmoppyval['op_bal_val']);
        									}
        								}
        							}
        						}

        						$final_op_bal_result[] = [
        							'item_id'				=> $item_id,
        							'comp_id' 			=> $this->company_id,
        							'item_unit' 		=> $unit_id,
        							'mat_cent_id'   => $mc_id,
        							'bo_id'					=> $bo_id,
        							'batch_id'			=> 0,
        							'op_bal_qty'		=> $item_qty,
        							'py_bal_qty'		=> $item_qty,
        						];

        						foreach ($item_val_arr as $item_val_id => $item_val) 
        						{
        							$final_op_val_result[] = [
        								'item_id'				=> $item_id,
        								'comp_id' 			=> $this->company_id,
        								'item_unit' 		=> $unit_id,
        								'mat_cent_id'   => $mc_id,
        								'bo_id'					=> $bo_id,
        								'batch_id'			=> 0,
        								'op_bal_val'		=> $item_val,
        								'py_bal_val'		=> $item_val,
        								'method_id'			=> $item_val_id,
        							];
        						}

        					}
        				}
        			}
        		}
        	}

        	return [
        		'final_op_bal_result'	=> 	$final_op_bal_result,
        		'final_op_val_result'	=>  $final_op_val_result,
        	];
        }						  



        public function CreateFYTable($old_tbl,$new_tbl){
        	try{
        		$this->db->query("DROP TABLE IF EXISTS `".$new_tbl."`;");   	
        		$this->db->query("CREATE TABLE `".$new_tbl."` LIKE `".$old_tbl."`;");
        		$this->db->query("INSERT INTO `".$new_tbl."` SELECT * FROM `".$old_tbl."`");
	//$this->db->query("CREATE TABLE `".$new_tbl."` AS SELECT * FROM `".$old_tbl."`;");
	// this command create data&structure only no primary key and indexing 
        	}
        	catch (\Exception $e) {
        		$status = 0;
        		$flag = 1;
        	}   
        }
        function GetDrpTableName($company_id,$new_tbl,$fy_id){
        	try{
        		$new_tbl = $company_id.$new_tbl.$fy_id;
        		$this->db->query("DROP TABLE IF EXISTS `".$new_tbl."`;"); 
        	}catch (\Exception $e) {
        		$status = 0;
        		$flag = 1;
        	}   
        }
        function DropAllFYTables($fy_id){
        	for($i=1;$i<=100;$i++){		
        		$this->db->query("DROP TABLE IF EXISTS `".$this->company_id.'_itemtxnnnn_'.$i.'_'.$fy_id."`;"); 		
        		$this->db->query("DROP TABLE IF EXISTS `".$this->company_id.'_accnttxnnn_'.$i.'_'.$fy_id."`;"); 	
        		$this->db->query("DROP TABLE IF EXISTS `".$this->company_id.'_sundrytxnn_'.$i.'_'.$fy_id."`;"); 	
        		$this->db->query("DROP TABLE IF EXISTS `".$this->company_id.'_itemtxnval_'.$i.'_'.$fy_id."`;"); 		

        	} 

        	$tanmastern_ntbl      = $this->GetDrpTableName($this->company_id,'_tanmastern_',$fy_id);
        	$gstinmastr_ntbl      = $this->GetDrpTableName($this->company_id,'_gstinmastr_',$fy_id);
        	$cmpprofsnl_ntbl      = $this->GetDrpTableName($this->company_id,'_cmpprofsnl_',$fy_id);
        	$cmpprofstf_ntbl      = $this->GetDrpTableName($this->company_id,'_cmpprofstf_',$fy_id);
        	$comp_txn_master_ntbl = $this->GetDrpTableName($this->company_id,'_comptxnmst_',$fy_id);                 		
        	$company_bank_ntbl    = $this->GetDrpTableName($this->company_id,'_compbanknn_',$fy_id);
        	$comp_accountant_ntbl = $this->GetDrpTableName($this->company_id,'_compatnnnn_',$fy_id);
        	$comp_auditor_ntbl    = $this->GetDrpTableName($this->company_id,'_cmpauditor_',$fy_id);
        	$comp_currency_ntbl   = $this->GetDrpTableName($this->company_id,'_compcurrcy_',$fy_id);
        	$forexrates_ntbl      = $this->GetDrpTableName($this->company_id,'_forexrates_',$fy_id);
        	$cmperplogs_ntbl      = $this->GetDrpTableName($this->company_id,'_cmperplogs_',$fy_id);
        	$hobomaster_ntbl      = $this->GetDrpTableName($this->company_id,'_hobomaster_',$fy_id); 
        	$vhtxntrail_ntbl  = $this->GetDrpTableName($this->company_id,'_vhtxntrail_',$fy_id);
        	$cmpvchseri_ntbl  = $this->GetDrpTableName($this->company_id,'_cmpvchseri_',$fy_id);
        	$vchseriesa_ntbl  = $this->GetDrpTableName($this->company_id,'_vchseriesa_',$fy_id);
        	$vchseriesm_ntbl  = $this->GetDrpTableName($this->company_id,'_vchseriesm_',$fy_id);
        	$cmpvchtype_ntbl  = $this->GetDrpTableName($this->company_id,'_cmpvchtype_',$fy_id);
        	$vchsubtype_ntbl  = $this->GetDrpTableName($this->company_id,'_vchsubtype_',$fy_id);
        	$long_narrn_ntbl  = $this->GetDrpTableName($this->company_id,'_long_narrn_',$fy_id);
        	$short_narr_ntbl  = $this->GetDrpTableName($this->company_id,'_short_narr_',$fy_id);		
        	$vhtxnconso_ntbl  = $this->GetDrpTableName($this->company_id,'_vhtxnconso_',$fy_id);
        	$acctcontnn_ntbl  = $this->GetDrpTableName($this->company_id,'_acctcontnn_',$fy_id);
        	$acctgroupn_ntbl  = $this->GetDrpTableName($this->company_id,'_acctgroupn_',$fy_id);
        	$grpparentn_ntbl  = $this->GetDrpTableName($this->company_id,'_grpparentn_',$fy_id);
        	$acctmaster_ntbl  = $this->GetDrpTableName($this->company_id,'_acctmaster_',$fy_id);
        	$mcgrpmstnn_ntbl  = $this->GetDrpTableName($this->company_id,'_mcgrpmstnn_',$fy_id);
        	$mcmasternn_ntbl  = $this->GetDrpTableName($this->company_id,'_mcmasternn_',$fy_id);
        	$mcstoremst_ntbl  = $this->GetDrpTableName($this->company_id,'_mcstoremst_',$fy_id);
        	$billsundry_ntbl  = $this->GetDrpTableName($this->company_id,'_billsundry_',$fy_id);
        	$billofmatn_ntbl  = $this->GetDrpTableName($this->company_id,'_billofmatn_',$fy_id);
        	$bominputnn_ntbl  = $this->GetDrpTableName($this->company_id,'_bominputnn_',$fy_id);
        	$bomoutputn_ntbl  = $this->GetDrpTableName($this->company_id,'_bomoutputn_',$fy_id);
        	$bomaddcost_ntbl  = $this->GetDrpTableName($this->company_id,'_bomaddcost_',$fy_id);
        	$billmaster_ntbl  = $this->GetDrpTableName($this->company_id,'_billmaster_',$fy_id);
        	$costctmstr_ntbl  = $this->GetDrpTableName($this->company_id,'_costctmstr_',$fy_id);
        	$costctgrup_ntbl  = $this->GetDrpTableName($this->company_id,'_costctgrup_',$fy_id);		
        	$itemcatmst_ntbl = $this->GetDrpTableName($this->company_id,'_itemcatmst_',$fy_id);
        	$itmgrpprnt_ntbl = $this->GetDrpTableName($this->company_id,'_itmgrpprnt_',$fy_id); 
        	$itemgrpmst_ntbl = $this->GetDrpTableName($this->company_id,'_itemgrpmst_',$fy_id);
        	$itemmaster_ntbl = $this->GetDrpTableName($this->company_id,'_itemmaster_',$fy_id);
        	$itmunitmst_ntbl = $this->GetDrpTableName($this->company_id,'_itmunitmst_',$fy_id);			
        	$itmparamtr_ntbl = $this->GetDrpTableName($this->company_id,'_itmparamtr_',$fy_id);		
        	$paramtrval_ntbl = $this->GetDrpTableName($this->company_id,'_paramtrval_',$fy_id);
        	$itmaltunit_ntbl = $this->GetDrpTableName($this->company_id,'_itmaltunit_',$fy_id);		
        	$itemdimens_ntbl = $this->GetDrpTableName($this->company_id,'_itemdimens_',$fy_id);
        	$itmbatchmt_ntbl = $this->GetDrpTableName($this->company_id,'_itmbatchmt_',$fy_id);
        	$itmtagging_ntbl = $this->GetDrpTableName($this->company_id,'_itmtagging_',$fy_id);
        	$labelmastr_ntbl = $this->GetDrpTableName($this->company_id,'_labelmastr_',$fy_id);
        	$labeldescn_ntbl = $this->GetDrpTableName($this->company_id,'_labeldescn_',$fy_id);
        	$itemlabeln_ntbl = $this->GetDrpTableName($this->company_id,'_itemlabeln_',$fy_id); 
        	$pckglistnn_ntbl = $this->GetDrpTableName($this->company_id,'_pckglistnn_',$fy_id);
        	$pckqtylist_ntbl = $this->GetDrpTableName($this->company_id,'_pckqtylist_',$fy_id); 
        	$pcklistbeg_ntbl = $this->GetDrpTableName($this->company_id,'_pcklistbeg_',$fy_id);
        	$pcklistext_ntbl = $this->GetDrpTableName($this->company_id,'_pcklistext_',$fy_id);		
        	$itemtrackn_ntbl = $this->GetDrpTableName($this->company_id,'_itemtrackn_',$fy_id);		
        	$pcklistmst_ntbl = $this->GetDrpTableName($this->company_id,'_pcklistmst_',$fy_id);
        	$listpacked_ntbl = $this->GetDrpTableName($this->company_id,'_listpacked_',$fy_id);
        	$pcklistqty_ntbl = $this->GetDrpTableName($this->company_id,'_pcklistqty_',$fy_id);
        	$listcumast_ntbl = $this->GetDrpTableName($this->company_id,'_listcumast_',$fy_id);
        	$cupackingn_ntbl = $this->GetDrpTableName($this->company_id,'_cupackingn_',$fy_id);
        	$leveltrack_ntbl = $this->GetDrpTableName($this->company_id,'_leveltrack_',$fy_id);
        	$barcodemst_ntbl = $this->GetDrpTableName($this->company_id,'_barcodemst_',$fy_id);
        	$itemtxnoth_tbl  = $this->GetDrpTableName($this->company_id,'_itemtxnoth_',$fy_id);
        	$itemcrsref_tbl  = $this->GetDrpTableName($this->company_id,'_itemcrsref_',$fy_id);
        	$itmoppyval_tbl  = $this->GetDrpTableName($this->company_id,'_itmoppyval_',$fy_id);
        	$itmoppybal_tbl  = $this->GetDrpTableName($this->company_id,'_itmoppybal_',$fy_id);
        	$accttxnoth_tbl = $this->GetDrpTableName($this->company_id,'_accttxnoth_',$fy_id); 
        	$acctcrsref_tbl = $this->GetDrpTableName($this->company_id,'_acctcrsref_',$fy_id);		
        	$accoppybal_tbl = $this->GetDrpTableName($this->company_id,'_accoppybal_',$fy_id); 

        	$bsdoppybal_tbl = $this->GetDrpTableName($this->company_id,'_bsdoppybal_',$fy_id); 
        	$itemtrackn_tbl = $this->GetDrpTableName($this->company_id,'_itemtrackn_',$fy_id); 
        	$itemtrackn_tbl = $this->GetDrpTableName($this->company_id,'_cmpvchseri_',$fy_id); 
        	$itemtrackn_tbl = $this->GetDrpTableName($this->company_id,'_cmpvchtype_',$fy_id); 
        	$itemtrackn_tbl = $this->GetDrpTableName($this->company_id,'_compcurrcy_',$fy_id); 
        	$itemtrackn_tbl = $this->GetDrpTableName($this->company_id,'_comptxnmst_',$fy_id); 
        	$itemtrackn_tbl = $this->GetDrpTableName($this->company_id,'_billstxnnn_',$fy_id); 
        	$itemtrackn_tbl = $this->GetDrpTableName($this->company_id,'_costcttxnn_',$fy_id); 
        	$itemtrackn_tbl = $this->GetDrpTableName($this->company_id,'_prntconfig_',$fy_id); 
        	$itemtrackn_tbl = $this->GetDrpTableName($this->company_id,'_prntdesign_',$fy_id);		
        	$taxcatmstn_tbl = $this->GetDrpTableName($this->company_id,'_taxcatmstn_',$fy_id); 
        	$itemvalmst_tbl = $this->GetDrpTableName($this->company_id,'_itemvalmst_',$fy_id);

        }


        public function create_company_default_tables($fy_id){
        	$current_fy_id       = $this->session->get('ses_comp_fy_id');
	   //New Tables With New FY ID
        	$tanmastern_ntbl      = $this->GetTableName($this->company_id,'_tanmastern_',$fy_id);
        	$gstinmastr_ntbl      = $this->GetTableName($this->company_id,'_gstinmastr_',$fy_id);
        	$cmpprofsnl_ntbl      = $this->GetTableName($this->company_id,'_cmpprofsnl_',$fy_id);
        	$cmpprofstf_ntbl      = $this->GetTableName($this->company_id,'_cmpprofstf_',$fy_id);

        	$company_bank_ntbl    = $this->GetTableName($this->company_id,'_compbanknn_',$fy_id);
        	$comp_accountant_ntbl = $this->GetTableName($this->company_id,'_compatnnnn_',$fy_id);
        	$comp_auditor_ntbl    = $this->GetTableName($this->company_id,'_cmpauditor_',$fy_id);


        	$comp_txn_master_ntbl = $this->GetTableName($this->company_id,'_comptxnmst_',$fy_id);
        	$comp_currency_ntbl   = $this->GetTableName($this->company_id,'_compcurrcy_',$fy_id);
        	$forexrates_ntbl      = $this->GetTableName($this->company_id,'_forexrates_',$fy_id);
        	$cmperplogs_ntbl      = $this->GetTableName($this->company_id,'_cmperplogs_',$fy_id);
        	$hobomaster_ntbl      = $this->GetTableName($this->company_id,'_hobomaster_',$fy_id);
        	$prntconfig_ntbl      = $this->GetTableName($this->company_id,'_prntconfig_',$fy_id);
        	$prntdesign_ntbl      = $this->GetTableName($this->company_id,'_prntdesign_',$fy_id);

        	$cmpgtcscat_ntbl      = $this->GetTableName($this->company_id,'_cmpgtcscat_',$fy_id);
        	$cmpgstcatn_ntbl      = $this->GetTableName($this->company_id,'_cmpgstcatn_',$fy_id);
        	$cmptaxcatm_ntbl      = $this->GetTableName($this->company_id,'_cmptaxcatm_',$fy_id);
        	$cmpgwhtcat_ntbl      = $this->GetTableName($this->company_id,'_cmpgwhtcat_',$fy_id);
        	$cmpitcscat_ntbl      = $this->GetTableName($this->company_id,'_cmpitcscat_',$fy_id);
        	$cmpiwhtcat_ntbl      = $this->GetTableName($this->company_id,'_cmpiwhtcat_',$fy_id);

		//Old Tables With Curret FY ID
        	$tanmastern_otbl      = $this->GetTableName($this->company_id,'_tanmastern_',$current_fy_id);
        	$gstinmastr_otbl      = $this->GetTableName($this->company_id,'_gstinmastr_',$current_fy_id);
        	$cmpprofsnl_otbl      = $this->GetTableName($this->company_id,'_cmpprofsnl_',$current_fy_id);
        	$cmpprofstf_otbl      = $this->GetTableName($this->company_id,'_cmpprofstf_',$current_fy_id);
        	$comp_txn_master_otbl = $this->GetTableName($this->company_id,'_comptxnmst_',$current_fy_id);                 		
        	$company_bank_otbl    = $this->GetTableName($this->company_id,'_compbanknn_',$current_fy_id);
        	$comp_accountant_otbl = $this->GetTableName($this->company_id,'_compatnnnn_',$current_fy_id);
        	$comp_auditor_otbl    = $this->GetTableName($this->company_id,'_cmpauditor_',$current_fy_id);
        	$comp_currency_otbl   = $this->GetTableName($this->company_id,'_compcurrcy_',$current_fy_id);
        	$forexrates_otbl      = $this->GetTableName($this->company_id,'_forexrates_',$current_fy_id);
        	$cmperplogs_otbl      = $this->GetTableName($this->company_id,'_cmperplogs_',$current_fy_id);
        	$hobomaster_otbl      = $this->GetTableName($this->company_id,'_hobomaster_',$current_fy_id);
        	$prntconfig_otbl      = $this->GetTableName($this->company_id,'_prntconfig_',$current_fy_id);
        	$prntdesign_otbl      = $this->GetTableName($this->company_id,'_prntdesign_',$current_fy_id);

        	$cmpgtcscat_otbl      = $this->GetTableName($this->company_id,'_cmpgtcscat_',$current_fy_id);
        	$cmpgstcatn_otbl      = $this->GetTableName($this->company_id,'_cmpgstcatn_',$current_fy_id);
        	$cmptaxcatm_otbl      = $this->GetTableName($this->company_id,'_cmptaxcatm_',$current_fy_id);
        	$cmpgwhtcat_otbl      = $this->GetTableName($this->company_id,'_cmpgwhtcat_',$current_fy_id);
        	$cmpitcscat_otbl      = $this->GetTableName($this->company_id,'_cmpitcscat_',$current_fy_id);
        	$cmpiwhtcat_otbl      = $this->GetTableName($this->company_id,'_cmpiwhtcat_',$current_fy_id);

        	$status = 1;
        	$flag = 0;
        	try{
        		$this->CreateFYTable($tanmastern_otbl,$tanmastern_ntbl);
        		$this->CreateFYTable($gstinmastr_otbl,$gstinmastr_ntbl);
        		$this->CreateFYTable($cmpprofsnl_otbl,$cmpprofsnl_ntbl);
        		$this->CreateFYTable($cmpprofstf_otbl,$cmpprofstf_ntbl);
        		$this->CreateFYTable($comp_txn_master_otbl,$comp_txn_master_ntbl);
        		$this->CreateFYTable($company_bank_otbl,$company_bank_ntbl);
        		$this->CreateFYTable($comp_accountant_otbl,$comp_accountant_ntbl);
        		$this->CreateFYTable($comp_auditor_otbl,$comp_auditor_ntbl);
        		$this->CreateFYTable($comp_currency_otbl,$comp_currency_ntbl);
        		$this->CreateFYTable($forexrates_otbl,$forexrates_ntbl);
        		$this->CreateFYTable($cmperplogs_otbl,$cmperplogs_ntbl);
        		$this->CreateFYTable($hobomaster_otbl,$hobomaster_ntbl);
        		$this->CreateFYTable($prntconfig_otbl,$prntconfig_ntbl);
        		$this->CreateFYTable($prntdesign_otbl,$prntdesign_ntbl);		
        		$this->CreateFYTable($cmpgtcscat_otbl,$cmpgtcscat_ntbl);
        		$this->CreateFYTable($cmpgstcatn_otbl,$cmpgstcatn_ntbl);
        		$this->CreateFYTable($cmptaxcatm_otbl,$cmptaxcatm_ntbl);
        		$this->CreateFYTable($cmpgwhtcat_otbl,$cmpgwhtcat_ntbl);
        		$this->CreateFYTable($cmpitcscat_otbl,$cmpitcscat_ntbl);
        		$this->CreateFYTable($cmpiwhtcat_otbl,$cmpiwhtcat_ntbl);


        		$this->db->query('INSERT INTO `'.$prntconfig_ntbl.'`  (`prntconfig_id`, `comp_id`, `vch_series_id`, `usr_config_id`) VALUES ('.$this->company_id.',13,17);');
$this->db->query("INSERT INTO `".$prntdesign_ntbl."` 
	(`prntconfig_id`, `erpprevaln_label_id`, `prntconfig_style`) VALUES
	(1,	6,	'font-weight: bold; font-family: Verdana; font-size: 12px;'),
	(1,	7,	'font-weight: bold; font-family: Verdana; font-size: 12px;'),
	(1,	8,	'text-align: center; padding: 0px; margin: 0px; font-family: Verdana; text-decoration: underline; font-weight: bold; font-size: 12px;'),
	(1,	9,	'line-height: 42px; padding: 0px; margin: 0px; text-align: center; font-weight: bold; font-family: Verdana; font-size: 16px;'),
	(1,	10,	'font-family: Verdana; font-size: 12px;'),
	(1,	11,	'font-family: Verdana; font-size: 12px;'),
	(1,	12,	'font-family: Verdana; font-size: 12px;'),
	(1,	13,	'width: 150px; float: left; font-family: Verdana; font-size: 12px;'),
	(1,	14,	'width: 150px; float: left; font-family: Verdana; font-size: 12px;'),
	(1,	15,	'width: 150px; float: left; font-family: Verdana; font-size: 12px;'),
	(1,	16,	'width: 150px; float: left; font-family: Verdana; font-size: 12px;'),
	(1,	17,	'padding-left: 29px; font-family: Verdana; font-size: 12px;'),
	(1,	18,	'padding-left: 8px; font-family: Verdana; font-size: 12px;'),
	(1,	19,	'text-align: right; font-family: Verdana; font-size: 14px;'),
	(1,	20,	'font-family: Verdana; font-size: 14px; font-weight: bold;'),
	(1,	26,	'padding-left: 10px; font-weight: bold; font-family: Verdana; font-size: 12px;'),
	(1,	27,	'padding-left: 10px; font-weight: bold; font-family: Verdana; font-size: 12px;'),
	(1,	28,	'padding-left: 18px; font-family: Verdana; font-size: 12px;'),
	(1,	29,	'padding-left: 18px; font-family: Verdana; font-size: 12px;'),
	(1,	30,	'font-family: Verdana; font-size: 12px;'),
	(1,	31,	'font-family: Verdana; font-size: 12px;'),
	(1,	32,	'font-family: Verdana; font-size: 12px;'),
	(1,	33,	'font-family: Verdana; font-size: 12px;'),
	(1,	34,	'text-align: center; padding: 0px 0px 8px; line-height: 20px; margin: 0px; font-family: Verdana; font-size: 12px;'),
	(1,	36,	'padding: 6px 0px; font-family: Verdana; font-size: 14px;'),
	(1,	37,	'font-family: Verdana; font-size: 12px;');");

$this->db->query('INSERT INTO `'.$prntconfig_ntbl.'`  (`prntconfig_id`, `comp_id`, `vch_series_id`, `usr_config_id`) VALUES ('.$this->company_id.',13,18);');
$this->db->query("INSERT INTO `".$prntdesign_ntbl."` 
	(`prntconfig_id`, `erpprevaln_label_id`, `prntconfig_style`) VALUES
	(2,	6,	'font-family: Verdana; font-size: 12px;'),
	(2,	26,	'padding-left: 10px; font-family: Verdana; font-size: 12px;'),
	(2,	7,	'font-family: Verdana; font-size: 12px;'),
	(2,	27,	'padding-left: 10px; font-family: Verdana; font-size: 12px;'),
	(2,	8,	'font-family: Verdana; font-size: 24px; text-decoration: underline;'),
	(2,	9,	'margin-top: 15px; line-height: 42px; font-family: Verdana; font-size: 14px;'),
	(2,	34,	'font-family: Verdana; font-size: 12px;'),
	(2,	28,	'font-family: Verdana; font-size: 12px; font-weight: bold;'),
	(2,	11,	'font-family: Verdana; font-size: 12px;'),
	(2,	29,	'padding-left: 12px; font-family: Verdana; font-size: 12px;'),
	(2,	12,	'border-right: 1px solid rgb(0, 0, 0); padding: 6px; width: 56%; font-family: Verdana; font-size: 12px;'),
	(2,	30,	'border-right: 1px solid rgb(0, 0, 0); padding: 6px; width: 56%; font-family: Verdana; font-size: 12px;'),
	(2,	13,	'undefined'),
	(2,	31,	'undefined'),
	(2,	14,	'border-right: 1px solid rgb(0, 0, 0); padding: 6px; width: 24%; font-family: Verdana; font-size: 12px;'),
	(2,	32,	'border-right: 1px solid rgb(0, 0, 0); padding: 6px; width: 24%; font-family: Verdana; font-size: 12px;'),
	(2,	15,	'undefined'),
	(2,	33,	'undefined'),
	(2,	16,	'padding: 6px; text-align: right; width: 20%; font-family: Verdana; font-size: 12px;'),
	(2,	37,	'padding: 6px; text-align: right; width: 20%; font-family: Verdana; font-size: 12px;'),
	(2,	17,	'padding-left: 29px; font-family: Verdana; font-size: 12px;'),
	(2,	18,	'padding-left: 8px; font-family: Verdana; font-size: 12px;'),
	(2,	19,	'text-align: right; font-family: Verdana; font-size: 14px;'),
	(2,	36,	'font-family: Verdana; font-size: 14px;'),
	(2,	20,	'font-family: Verdana; font-size: 12px;'),
	(2,	24,	'font-family: Verdana; font-size: 12px;'),
	(2,	25,	'font-family: Verdana; font-size: 12px;');");	
}
catch (\Exception $e) {
	$status = 0;
	$flag = 1;
}
}

function FetchCompUUId(){
	$response = $this->aicountly_db->table('aicountly_compidgenr_univdb')->select('uuid')->where('comp_id',$this->company_id)->get()->getRowArray();
	return $response['uuid'];
}

public function CompFYAndMappingMaster($master_ntbl,$master_otbl,$master_type,$new_fy_id,$last_fy_info){
	$uuid         = $this->FetchCompUUId();	 
	$table_columns  =  $this->db->query("SHOW COLUMNS FROM `".$master_otbl."`;")->getResultArray();   

	$ext_uuid_db  = $this->externaldb->connect_uuid_db($uuid);	  
	$this->db->query("CREATE TABLE IF NOT EXISTS `".$master_ntbl."` LIKE `".$master_otbl."`;");	  

	/************ save master data in cofymstmap table user wise like **********/
	/************** aicountlyin_uuid_2 with new FY id and master id   **********/
	$builder        =  $this->db->table($master_otbl); 	  
	$master_res     =  $builder->get()->getResultArray();	 
	if($master_res){
		foreach($master_res as $mrow){
			  if($master_type=='act') //Account Master
			  $last_mst_fy_id  = $mrow['acc_id'];
			   if($master_type=='acg') //Account Group Master
			   $last_mst_fy_id  = $mrow['acc_grp_id'];
			  if($master_type=='cst') //Cost Center Master
			  $last_mst_fy_id  = $mrow['cc_id'];
			  if($master_type=='ccg') //Cost Center Group Master
			  $last_mst_fy_id  = $mrow['cc_grp_id'];	
			  if($master_type=='bsd') //Bill Subdry Master
			  $last_mst_fy_id  = $mrow['bill_sundry_id'];		
			  if($master_type=='bsd') //Bill Subdry Master
			  $last_mst_fy_id  = $mrow['bill_sundry_id'];			
			  if($master_type=='itm') //Item Master
			  $last_mst_fy_id  = $mrow['item_id'];	
			  if($master_type=='itg') //Item Group Master
			  $last_mst_fy_id  = $mrow['item_grp_id'];
			  if($master_type=='itu') //Item Unit Master
			  $last_mst_fy_id  = $mrow['unit_id'];		
			  if($master_type=='itc') //Item Category Master
			  $last_mst_fy_id  = $mrow['icatgms_id'];				
			  if($master_type=='mcm') //Material Center Master
			  $last_mst_fy_id  = $mrow['mat_cent_id'];	
			  if($master_type=='mcg') //Material Center Group
			  $last_mst_fy_id  = $mrow['mc_grp_id'];
			  if($master_type=='mcs') //Material Center Store
			  $last_mst_fy_id  = $mrow['mc_store_id'];	
			  if($master_type=='brc') //Barcode Master
			  $last_mst_fy_id  = $mrow['barcode_id']; 		


			  $builder3 = $ext_uuid_db->table('cofymstmap'); 
			  $builder3->where('comp_id', $this->company_id);
			  $builder3->where('comp_fy_id', $last_fy_info['comp_fy_id']);
			  $builder3->where('mst_type', $master_type);
			  $builder3->where('mst_fy_id', $last_mst_fy_id);
			  $ext_uuid_res = $builder3->get()->getRowArray();				
			  if($ext_uuid_res){
			  	$mst_base_id     = $ext_uuid_res['mst_base_id'];
			  	$table_col_val   = '';				

			  	foreach($table_columns as $key => $table_field){
			  		if(!isset($mrow[trim($table_field['Field'])]))
			  			$tblfield = 0;
			  		else 
			  			$tblfield =  $mrow[trim($table_field['Field'])];					 
			  		$table_col_val .='`'.trim($table_field['Field']).'`="'.trim($tblfield).'",';

			  	}
				//echo $mst_base_id.'=> '.$table_col_val;
				//echo '<br>';
			  	if($table_col_val){
			  		$table_col_val = rtrim($table_col_val,",");

			  		$this->db->query("INSERT INTO `".$master_ntbl."` SET ".$table_col_val);
				//echo $this->db->GetLastQuery();
				//echo '<br>';
			  		$new_mst_fy_id    = $this->db->insertID();				
			  		$cofymstmap_data  = array('comp_id'=>$this->company_id,'comp_fy_id'=>$new_fy_id,
			  			'mst_type'=>$master_type,'mst_base_id'=>$mst_base_id,
			  			'mst_fy_id'=>$new_mst_fy_id
			  		);
			  		$ext_uuid_db->table('cofymstmap')->insert($cofymstmap_data);
			  	}	

				}//else
					//echo 'no mst base id found ->'.$last_fy_info['comp_fy_id'].'--'.$last_mst_fy_id.'<br>';
				
			}
		}

	}
	






/****************************** FY CHANGE END **********************************/

 public function save_comp_tax($tax_update_data){
	$comptaxmst_tbl = 'comptaxmst';
	$exists = $this->myaicountly_pg->table($comptaxmst_tbl)->where('comp_id',$this->company_id)->get()->getRowArray();		
	if($exists)
		$this->myaicountly_pg->table($comptaxmst_tbl)->where('comp_id',$this->company_id)->update($tax_update_data);		  
	else
		$this->myaicountly_pg->table($comptaxmst_tbl)->insert($tax_update_data);
 } 

 function get_company_address_info($company_id,$comp_addr_type){	 
	return $this->univaictly->table('cmpaddrmst')->where('cmp_addr_type', $comp_addr_type)->where('cmp_id', $company_id)->get()->getRowArray();   	   
 }

 function get_other_info($company_id){	 
	return $this->univaictly->table('cmpmstdetn')->where('cmp_id', $company_id)->get()->getRowArray();   	   
 }

  public function update_company_address($company_id, $data)
{
    $db = $this->univaictly;
    $db->transStart();

    // ---------- Registered Office (addr_type = 1)
    $ro = [
        'cmp_addr1'   => $data['ro_add1'] ?? null,
        'cmp_addr2'   => $data['ro_add2'] ?? null,
        'cmp_city'    => $data['ro_city'] ?? null,
        'cmp_state'   => (int)$data['ro_state'] ?? 0,
        'cmp_pin_zip' => $data['ro_pin'] ?? null,
        'cmp_country' => (int)$data['ro_country'] ?? 0,
        'cmp_id'      => $company_id,
        'cmp_addr_type' => 1,
    ];

    $existsRo = $db->table('cmpaddrmst')
        ->where('cmp_id', $company_id)
        ->where('cmp_addr_type', 1)
        ->countAllResults();

    if ($existsRo) {
        $db->table('cmpaddrmst')
           ->where('cmp_id', $company_id)
           ->where('cmp_addr_type', 1)
           ->update($ro);
    } else {
        $db->table('cmpaddrmst')->insert($ro);
    }

    // ---------- Corporate Office (addr_type = 2)
    $co = [
        'cmp_addr1'   => $data['co_add1'] ?? null,
        'cmp_addr2'   => $data['co_add2'] ?? null,
        'cmp_city'    => $data['co_city'] ?? null,
        'cmp_state'   => (int)$data['co_state'] ?? 0,
        'cmp_pin_zip' => $data['co_pin'] ?? null,
        'cmp_country' => (int)$data['co_country'] ?? 0,
        'cmp_id'      => $company_id,
        'cmp_addr_type' => 2,
    ];

    $existsCo = $db->table('cmpaddrmst')
        ->where('cmp_id', $company_id)
        ->where('cmp_addr_type', 2)
        ->countAllResults();

    if ($existsCo) {
        $db->table('cmpaddrmst')
           ->where('cmp_id', $company_id)
           ->where('cmp_addr_type', 2)
           ->update($co);
    } else {
        $db->table('cmpaddrmst')->insert($co);
    }

    // ---------- FY master update
    $db->table('cmpfymastr')
       ->where('cmp_id', $company_id)
       ->update(['def_val_method' => $data['def_val_method'] ?? null]);

    $db->transComplete();
    return $db->transStatus();
} 

   public function update_company($company_id,$update_data){
	$this->univaictly->table('cmpmastern')->where('cmp_id',$company_id)->update($update_data);		
	return TRUE;		
	}
	
	public function update_company_details($company_id,$data){
	$response = $this->univaictly->table('cmpmstdetn')->where('cmp_id',$company_id)->get()->getRowArray();	
	
	if($response)	
    	$this->univaictly->table('cmpmstdetn')->where('cmp_id',$company_id)->update($data);		
    else{
	  $insert_data = array("cmp_id"=>$company_id,"cmp_tel"=>$data["cmp_tel"],"cmp_mobile"=>$data["cmp_mobile"],"cmp_wa_mobile"=>$data["cmp_wa_mobile"],"cmp_email"=>$data["cmp_email"],
	                      "cmp_industry"=>$data["cmp_industry"],"cmp_work_nature"=>$data["cmp_work_nature"]
						  );	
	  $this->univaictly->table('cmpmstdetn')->insert($insert_data);			
	}	   	
	return TRUE;		
	}

	function get_info($comp_id){	 
		$response   =  $this->univaictly->table('cmpmastern')->where('cmp_id', $comp_id)->get()->getRowArray();   	   
		$cmpfymastr =  $this->univaictly->table('cmpfymastr')->where('cmp_id', $comp_id)->get()->getRowArray();   	   
	    if($cmpfymastr)
			$response['def_val_method']= $cmpfymastr['def_val_method'];
		else
			$response['def_val_method'] = 1;
		
		 return $response;
	} 

	

	function saveDocument($data)
	{
		$this->myaicountly_pg->table('aicdocidgr')
												->insert($data);

		return $this->myaicountly_pg->insertID();
	}

	function updateCompanyMaster($comp_id,$data)
	{
		$this->univaictly->table('cmpmstdetn')
												->where('cmp_id', $comp_id)
												->update($data);

	}

	function getCompLogo($comp_id)
	{
		$file = [];
		$comp = $this->univaictly->table('cmpmstdetn')
												->select('cmp_logo')
												->where('cmp_id', $comp_id)
												->get()->getRowArray();

		if(!empty($comp['cmp_logo'])){
			$doc_id = $comp['cmp_logo'];
			$doc = $this->myaicountly_pg->table('aicdocidgr')
										->where('doc_id', $doc_id)
										->get()->getRowArray();
			if($doc){
				$file['name'] = $doc['doc_name'];
				$file['ext'] = $doc['doc_file_ext'];
			}
		}

		return $file;
	}

}