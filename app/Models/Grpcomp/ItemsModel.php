<?php
namespace App\Models\Grpcomp;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\CommonModel;
use App\Libraries\enc_string;

class ItemsModel extends Model	{

    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->common        = \Config\Database::connect();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    =  $this->session->get('ses_company_id');
	   $this->CommonModel   =  new CommonModel();
	   $this->enc_string    = new enc_string();
    }
    
	public function product_dimensions_list(){
		$erp_db = $this->externaldb->erp_db();
	 $newcompval_master_tbl = "aictlyerp_newcompval_univdb";
     return $erp_db->table($newcompval_master_tbl)->where('tag', 'product_dimension')->get()->getResultArray(); 	  	
	}
   
   public function InsertTable($tablname,$data){
	 $this->db->table($tablname)->insert($data);
	 return $this->db->insertID();    	  	
	}
	
	
   public function add_item_parameters($data){
	 $itmparamtr_tbl = $this->company_id.'_itmparamtr_'.$this->session->get('ses_comp_fy_id');    
	 $this->db->table($itmparamtr_tbl)->insert($data);
	 return $this->db->insertID();    	  	
	}
	
   public function add_parameters_value($data){
	 $paramtrval_tbl = $this->company_id.'_paramtrval_'.$this->session->get('ses_comp_fy_id');    
	 $this->db->table($paramtrval_tbl)->insert($data);	     	  	
	} 
  
    function company_all_items(){
      $company_id  = $this->company_id;
      $comp_fy_id  = $this->session->get('ses_comp_fy_id');
      
       $item_master_tbl = $company_id.'_itemmaster_'.$comp_fy_id;
	   $data =  $this->db->table($item_master_tbl)
	   					->select('item_name as label, item_name as value,item_upc, item_id, item_unit')
	   					->orderBy('item_name','ASC')
	   					->get()->getResultArray();

	   $final_result = array();
	   if($data){
		  foreach($data as $row){
		      
		       	$item_unit_info  = $this->item_unit_info($row['item_unit']); 
             	if($item_unit_info)
                	$item_unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');				 
              	else
                	$item_unit_name ='' ;
			  
		      
               $final_result[]    = array(
               		"label"=>ucwords($row['label']),
               		'value'=> $row['value'],
               		'item_id'=> $row['item_id'],
					'item_upc' => $row['item_upc'],
               		'item_unit'=>$item_unit_name,
					"item_name"=>ucwords($row['value']),
               		'item_unit_id'=>$row['item_unit'],
               	);	
		      }
        }
	   return json_encode($final_result);
     
 }  
 
   public function add_item_dimensions($data){
	$itemdimens_tbl = $this->company_id.'_itemdimens_'.$this->session->get('ses_comp_fy_id');    
	$this->db->table($itemdimens_tbl)->insert($data);
	return $this->db->insertID();   
   }   
   
    public function ajax_items(){
	    $comp_id         = $this->session->get('ses_company_id');
	    if($this->company_id){
		$item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id'); 
	    $builder         = $this->db->table($item_master_tbl); 
        $total_Records = $builder->countAll();
        if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
         
         if($pq_curPage==0){$pq_curPage=1;}
         	$offset = ($pq_rPP * ($pq_curPage - 1));
        if ($offset > $total_Records)
          {        
           $pq_curPage = ceil($total_Records / $pq_rPP);
           $offset = ($pq_rPP * ($pq_curPage - 1));
          }
          
         
         $builder->orderBy('item_name');                
	 	 $builder->where('comp_id', $comp_id);
	 	 $builder->limit($pq_rPP,$offset);
		 $result = $builder->get()->getResultArray();
         
         $records=array(); 		
          foreach($result as $values){
            $item_unit_info  = $this->get_units_info($values['item_unit'],$values['comp_id']);
			$item_group_info = $this->item_group_info($values['item_grp_id'],$values['comp_id']);			
			if(isset($item_group_info['item_grp_id']))
				$item_grp = $this->enc_string->nc_string($item_group_info['item_grp_name'],'de');
			 else
				$item_grp = ''; 
			if(isset($item_unit_info['item_unit']))
				$item_unit = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
			 else
				$item_unit = ''; 
			
			$item_catg_info  = $this->item_category_info($values['item_cat'],$values['comp_id']);	
            if($item_catg_info){
				$item_cat = $item_catg_info['item_cat'];
			}			
			else
			$item_cat = '';
			$records[]       = array(	
			                      'checkbox'     =>'<input name="item_ids[]" class="checkbox items_row"  data-id="'.$values['item_id'].'"  type="checkbox" value="'.$values['item_id'].'">',
    							  'item_upc'     => $values['item_upc'],
    							  'item_name'   => ucwords($values['item_name']),
    							  'item_alias'  => $values['item_alias'],
    							  'item_grp'    => $item_grp,
    							  'item_unit'   => $item_unit,
    							  'item_cat'    => $item_cat,
    							  'item_sku'    => $values['item_sku'],
								  'item_id'     => $values['item_id']
 				                 );  
		           }
				   
		}
		else{
		$total_Records=0;	
		$pq_curPage =1;
		$records = array();
		}
       echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
        
        
     }
 
   public function item_opn_balance($itm_transaction_table,$item_id,$comp_id,$start_date,$op_bal_qty){
          if(strtotime($start_date)>strtotime(date('Y-m-d')))
             $month_start_date = date('Y-m');
           else
            $month_start_date = date('Y-m', strtotime($start_date. ' -1 months'));
       
            $builder          =  $this->db->table($itm_transaction_table); 
            $builder->limit(1);
    	  	$builder->orderBy('item_txn_id','DESC');
    		$builder->where('comp_id', $comp_id);
    	    $builder->where('item_id', $item_id);
			if($this->session->get('ses_boid')!='')
           $builder->where('bo_id', $this->session->get('ses_boid'));

    		$builder->where("DATE_FORMAT(`item_txn_date`, '%Y-%m')", $month_start_date);
    		$result = $builder->get()->getRowArray();
    		if($result){
    		    $item_qty_bal = 0;//$result['item_qty_bal'];
    		}
    		else if($op_bal_qty >0)
    		  $item_qty_bal   =$op_bal_qty;
    		 else
    		  $item_qty_bal   ='0';
    	   return $item_qty_bal;
 	}
    
    public function update_txn_entries($table_name,$item_open_value,$item_open_qty){
        
        $this->db->transStart();
	    $builder = $this->db->table($table_name); 
		$builder->orderBy('item_txn_id');	
        $result =  $builder->get()->getResultArray();
		$balance =0;
		$counter=0;
		$qty_balance =0;
		$first_balance=0;
        if($result){
            
           $balance = $item_open_value ? $item_open_value : 0 ;
           $qty_balance = $item_open_qty ? $item_open_qty : 0 ;
           
           
		   foreach($result as $key => $row){
		        $counter=$counter+1;
		         $sel_voucher_typer = $row['item_txn_drcr'];

		        if(strtolower($sel_voucher_typer)=='c'){
					  $acc_txn_drcr_amount = -($row['item_txn_amount']*$row['item_txn_qty']);
					  $acc_txn_drcr_qty    = -$row['item_txn_qty'];
					  $amount_type ='c';
					}
				else if(strtolower($sel_voucher_typer)=='d'){
					  $acc_txn_drcr_amount = ($row['item_txn_amount']*$row['item_txn_qty']);
					  $acc_txn_drcr_qty    = $row['item_txn_qty'];
					  $amount_type ='d';
					}

	            $balance += $acc_txn_drcr_amount;
				$qty_balance += $acc_txn_drcr_qty;
	        
		
			  if($this->session->get('ses_boid')!='')			
             $this->db->table($table_name)->where('bo_id', $this->session->get('ses_boid'))->where('item_txn_id',$row['item_txn_id'])->where('comp_id',$row['comp_id'])->where('item_id',$row['item_id'])->update(array('item_value_bal'=>$balance,'item_qty_bal'=>$qty_balance));		   
		     else
				 $this->db->table($table_name)->where('item_txn_id',$row['item_txn_id'])->where('comp_id',$row['comp_id'])->where('item_id',$row['item_id'])->update(array('item_value_bal'=>$balance,'item_qty_bal'=>$qty_balance));		   
		    if ($this->db->transStatus() === true) {
				$this->db->transComplete();
		     }

		   }				
		} 
        
    }      
     
    public function item_txn_info($item_id,$txn_id){
	  $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	  
	  if($this->session->get('ses_boid')!='')
		  return  $this->db->table($itemtxnnnn_tbl)->where('txn_id', $txn_id)->where('bo_id', $this->session->get('ses_boid'))->where('comp_id', $this->company_id)->get()->getRowArray();
	   else	  
	  return  $this->db->table($itemtxnnnn_tbl)->where('txn_id', $txn_id)->where('comp_id', $this->company_id)->get()->getRowArray();
	}
	
	public function GetUnqUnits($item_id){
	  $itemtxnbal_tbl = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	  $builder        =  $this->db->table($itemtxnbal_tbl); 
      $builder->select('DISTINCT(item_unit) as item_unit');    		
      $builder->where('item_id', $item_id);
	  if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
	  $result      = $builder->get()->getResultArray();
	  $final_units = array();
	  if($result){
		  foreach($result as $row){
			    $final_units[$row['item_unit']] =0; 			  
		     }		  
	      }
	    return $final_units;
	}
	public function GetUnqMCs($item_id){
	  $itemtxnbal_tbl = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	  $builder        =  $this->db->table($itemtxnbal_tbl); 
      $builder->select('DISTINCT(mat_cent_id) as mat_cent_id');    		
      $builder->where('item_id', $item_id);
	  if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
	  $result      = $builder->get()->getResultArray();
	  $final_mcs = array();
	  if($result){
		  foreach($result as $row){
			    $final_mcs[$row['mat_cent_id']] =0; 			  
		     }		  
	      }
	    return $final_mcs;
	}
	
	
	public function mcgrp_mc_lists($mc_grp_id){
	  $mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	  $builder        =  $this->db->table($mcmasternn_tbl); 
      $builder->select('mat_cent_id');    		
      $builder->where('mat_cent_grp_id', $mc_grp_id);
	  $result      = $builder->get()->getResultArray();
	  $final_mcids = array();
	  if($result){
		  foreach($result as $row){
			    $final_mcids[$row['mat_cent_id']] = $row['mat_cent_id'];		  
		     }		  
	      }
	    return $final_mcids;	
		
		
	}
	function production_credit_debit_result($voucher_txn_id){
	
	    $comp_id         = $this->company_id;
	    $comptxnmst_tble = $comp_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');	   
		$final_d         = array();$credit=$debit=0;
		$items           = $this->db->table($comptxnmst_tble)->where('master_id_type','itm')->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->get()->getResultArray();	   
		 if($items){
			 foreach($items as $item_row){
				 
				  $itemtxnntable = $comp_id.'_itemtxnnnn_'.$item_row['master_id'].'_'.$this->session->get('ses_comp_fy_id');
				  $builder = $this->db->table($itemtxnntable);
				  $builder->where('voucher_txn_id',$voucher_txn_id);
				  if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				  $result_txn = $builder->get()->getResultArray();
				  if($result_txn){
					  foreach($result_txn as $txnrow){
						   if($txnrow['item_txn_drcr'] == 'c'){
                             $credit       = $credit+$txnrow['item_txn_amount'];
							}
							if($txnrow['item_txn_drcr'] == 'd'){
								$debit        = $debit+$txnrow['item_txn_amount'];
							}
							
					  }
				  }		
				 
			 }			 
			 
		 } 
	  return array('cr'=>$credit,'dr'=>$debit);
	}
	
	public function get_bom_info($voucher_txn_id){
		 $acctcrsref_tbl      =  $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
		 $billofmatn_tbl      =  $this->company_id.'_billofmatn_'.$this->session->get('ses_comp_fy_id');
		 $builder = $this->db->table($billofmatn_tbl.' bomtbl');
		 $builder->select('bomtbl.*');
		 $builder->join($acctcrsref_tbl.' acctcrsref','acctcrsref.acc_cross_ref_data=bomtbl.bom_id','left');
         $builder->where('acctcrsref.acc_cross_ref_type','bom_id'); 
		 $builder->where('acctcrsref.voucher_txn_id',$voucher_txn_id);
		 $builder->where('acctcrsref.comp_id',$this->company_id);
		 if($this->session->get('ses_boid')!='')
		  $builder->where($acctcrsref_tbl.'.bo_id', $this->session->get('ses_boid'));
		 $bom_result = $builder->get()->getRowArray();
		 
		
		 $builder2 = $this->db->table($acctcrsref_tbl);
         $builder2->like('acc_cross_ref_type','bom_batches','before'); 
		 $builder2->where('voucher_txn_id',$voucher_txn_id);
		 $builder2->where('comp_id',$this->company_id);
		 if($this->session->get('ses_boid')!='')
		  $builder->where('bo_id', $this->session->get('ses_boid'));
		 $bom_batch_result = $builder2->get()->getRowArray();
		
		 $final_result= array();
		 if($bom_batch_result)
		   $final_result['bom_batches']= $bom_batch_result['acc_cross_ref_data'];
		 else
		   $final_result['bom_batches']= 1;
	   
		 if($bom_result){
			 $final_result['bom_name']= $bom_result['bom_name'];
			 $final_result['bom_id']= $bom_result['bom_id'];			
		 }
		
	  
		 return $final_result;
	}
	
	
	public function comptxnmst_tleinfo($comp_id,$ses_comp_fy_id,$voucher_txn_id){
	   $company_txn_master_table = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	  
	   $builder = $this->db->table($company_txn_master_table); 
					$builder->select('master_id');
					$builder->where('voucher_txn_id', $voucher_txn_id);
					$builder->where('master_id_type', 'acc');
					$fresult = $builder->get()->getRowArray();
					if($fresult) 
					return $fresult;
				  else
					return "0";  
					 
	    
	}
	
function item_unit_info($unit_id){
       $company_id          = $this->company_id;
       $comp_fy_id          = $this->session->get('ses_comp_fy_id');
       $item_unit_master_tbl =  $company_id.'_itmunitmst_'.$comp_fy_id;	 
	   return  $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $company_id)->orderBy('item_unit','ASC')->get()->getRowArray();   
    }

    function opening_balance($item_id, $item_unit, $mat_cent_id, $date)
    {
    	$balance = 0;
    	$itemtxnnnn_tbl =  $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
    	$itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');

    	$builder = $this->db->table($itemtxnnnn_tbl);
    	$builder->where('item_id', $item_id);
    	$builder->where('item_unit', $item_unit);
    	$builder->where('mat_cent_id', $mat_cent_id);
    	$builder->where('item_txn_date <', $date);
    	$builder->where('batch_id', 0);
    	$builder->where('item_avail', 1);	
    	$builder->orderBy('item_txn_date', 'desc');
    	$builder->orderBy('voucher_txn_id', 'desc');
    	$builder->orderBy('item_txn_id', 'desc');
    	$builder->limit(1);
    	$transaction = $builder->get()->getRowArray();

    	if($transaction){
    		$balance = floatval($transaction['item_bal_qty']);
    	}
    	else{
    		$builder = $this->db->table($itmoppybal_tbl); 
			$builder->where('item_id', $item_id);
			$builder->where('item_unit', $item_unit);
			$builder->where('mat_cent_id', $mat_cent_id);
			if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
			$builder->where('batch_id', 0);	
	    	$itmoppybal = $builder->get()->getRowArray();
	    	if($itmoppybal){
	    		$balance = floatval($itmoppybal['op_bal_qty']);
	    	}
    	}

    	return $balance;
    }

    function opening_balance_list($item_id, $item_unit, $mat_cent_id, $mat_cent_grp_id, $date)
    {
    	$final = [];

    	$mcmasternn_tbl  = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
    	$itemtxnnnn_tbl =  $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
    	$itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');

    	$builder = $this->db->table($mcmasternn_tbl);
    	if($mat_cent_id != 0 && $mat_cent_grp_id == 0)
    		$builder->where('mat_cent_id', $mat_cent_id);
    	if($mat_cent_id == 0 && $mat_cent_grp_id != 0)
    		$builder->where('mat_cent_grp_id', $mat_cent_grp_id);
    	$mc_result = $builder->get()->getResultArray();

    	foreach ($mc_result as $mc_key => $mc_value) {
    		$mc = $this->enc_string->nc_string($mc_value['mat_cent_name'],'de');
    		
    		$unit_result = [];
    		if($item_unit != 0){
    			$unit_result = [$item_unit];
    		}
    		else{

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
    		}

    		foreach ($unit_result as $unit_key => $unit_value) {
    			$balance = 0;

    			$unit = '';
    			$item_unit_info  = $this->get_units_info($unit_value,$this->company_id);
				if(isset($item_unit_info['item_unit']))
				   $unit = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');

    			$builder = $this->db->table($itemtxnnnn_tbl);
		    	$builder->where('item_id', $item_id);
		    	$builder->where('item_unit', $unit_value);
		    	$builder->where('mat_cent_id', $mc_value['mat_cent_id']);
		    	$builder->where('item_txn_date <', $date);
		    	$builder->where('batch_id', 0);
		    	$builder->where('item_avail', 1);
		    	$builder->orderBy('item_txn_date', 'desc');
		    	$builder->orderBy('voucher_txn_id', 'desc');
		    	$builder->orderBy('item_txn_id', 'desc');
		    	$builder->limit(1);
		    	$transaction = $builder->get()->getRowArray();

		    	if($transaction){
		    		$balance = floatval($transaction['item_bal_qty']);
		    	}
		    	else{
		    		$builder = $this->db->table($itmoppybal_tbl); 
					$builder->where('item_id', $item_id);
					$builder->where('item_unit', $unit_value);
					$builder->where('mat_cent_id', $mc_value['mat_cent_id']);
					if($this->session->get('ses_boid')!='')
						$builder->where('bo_id', $this->session->get('ses_boid'));
					$builder->where('batch_id', 0);
			    	$itmoppybal = $builder->get()->getRowArray();
			    	if($itmoppybal){
			    		$balance = floatval($itmoppybal['op_bal_qty']);
			    	}
		    	}

		    	$final[] = [
		    		'item_id' => $item_id,
		    		'item_unit' => $unit_value,
		    		'unit_name'	=>	$unit,
		    		'mat_cent_id' => $mc_value['mat_cent_id'],
		    		'mc_name' => $mc,
		    		'balance' => $balance,
		    	];
    		}
    	}

    	

    	return $final;
    }

    function item_unit_list($item_id)
    {
    	$unit_result = [];
    	$list = [];

    	$itemtxnnnn_tbl =  $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
    	$itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');

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
			$item_unit_info = $this->get_units_info($unit_value,$this->company_id);
			if(isset($item_unit_info['item_unit']))
			   $unit = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');

			$list[] = [
				'unit_id' => $unit_value,
				'unit_name' => $unit,
			];

		}

		return $list;
	}

	public function get_mc_list_by_group($mc_grp_id)
	{
		$final = [];
		$mcmasternn_tbl  = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');

    	$builder = $this->db->table($mcmasternn_tbl);
    	$builder->where('mat_cent_grp_id', $mc_grp_id);
    	$result = $builder->get()->getResultArray();

    	foreach ($result as $key => $value) {
    		$final[] = $value['mat_cent_id'];
    	}

    	return $final;
	}

	public function load_item_ledger($item_id, $unit_id, $mc_id, $mc_grp_id, $val_id, $limit, $from_date, $to_date,$pq_curPage, $search)
	{	
		$mc_list = [];
		if($mc_id != 0)
			$mc_list = [$mc_id];
		if($mc_grp_id != 0)
			$mc_list = $this->get_mc_list_by_group($mc_grp_id);

		$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
		$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
		$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
		$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$itm_txn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		$itemtxnval_tbl  = $this->company_id.'_itemtxnval_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	    
		$builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND master_id_type = "itm"');
	    $builder->select('master_id, master_id_type');
	    $builder->join($itm_txn_tbl, $itm_txn_tbl.'.txn_id  = '.$comp_txn_tbl.'.txn_id');		
	    $builder->select('item_id, item_txn_date, item_txn_drcr, item_txn_qty, item_bal_qty, item_txn_amount, item_unit');
	
		$builder->where($itm_txn_tbl.'.item_id', $item_id);
		if($unit_id != 0)
			$builder->where($itm_txn_tbl.'.item_unit', $unit_id);
		if(count($mc_list))
			$builder->whereIn($itm_txn_tbl.'.mat_cent_id', $mc_list);
		
		if($from_date!='')
			$builder->where('voucher_date >=', $from_date);
		if($to_date!='')
			$builder->where('voucher_date <=', $to_date);

      	if($this->session->get('ses_boid')!='')
			$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));

		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->orderBy('item_txn_id');
		$total_records = $builder->countAllResults();

		if($pq_curPage==0){$pq_curPage=1;}
       	$offset = ($limit * ($pq_curPage - 1));
     	if ($offset > $total_records){        
            $pq_curPage = ceil($total_records / $limit);
            $offset = ($limit * ($pq_curPage - 1));
        }

		$builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND master_id_type = "itm"');
	    $builder->select('master_id, master_id_type');
	    $builder->join($itm_txn_tbl, $itm_txn_tbl.'.txn_id  = '.$comp_txn_tbl.'.txn_id');		
	    $builder->select('item_txn_id, item_id, item_txn_date, item_txn_drcr, item_txn_qty, item_bal_qty, item_txn_amount, item_unit');

		$builder->where($itm_txn_tbl.'.item_id', $item_id);
		if($unit_id != 0)
			$builder->where($itm_txn_tbl.'.item_unit', $unit_id);
		if(count($mc_list))
			$builder->whereIn($itm_txn_tbl.'.mat_cent_id', $mc_list);
		
		if($from_date!='')
			$builder->where('voucher_date >=', $from_date);
		if($to_date!='')
			$builder->where('voucher_date <=', $to_date);

      	if($this->session->get('ses_boid')!='')
			$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));

		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->orderBy('item_txn_id');

		$builder->limit($limit,$offset); 
		$result = $builder->get()->getResultArray();

	
		$final_result = [];

	   if($result){
		foreach($result as $key => $value){

			$voucher_date  = date("d-m-Y", strtotime($value['voucher_date']));
			$particulars = '';
			$unit_name = '';

			$inward_amount = '';
	    	$outward_amount = '';
	    	
	    	$inward_amount_total = 0;
	    	$outward_amount_total = 0;

	    	$inward_qty = '';
	    	$outward_qty = '';
	    	
	    	$inward_qty_total = 0;
	    	$outward_qty_total = 0;

	    	$closing_qty  = 0;
	    	$valuation = 0;

	    	if($value['item_txn_drcr']=='d'){
				$inward_qty = floatval($value['item_txn_qty']);
				$inward_qty_total = floatval($value['item_txn_qty']);

				$inward_amount = formatAmount($value['item_txn_amount']);
				$inward_amount_total = $value['item_txn_amount'];
			}
			if($value['item_txn_drcr']=='c'){
				$outward_qty = floatval($value['item_txn_qty']);
				$outward_qty_total = floatval($value['item_txn_qty']);

				$outward_amount = formatAmount($value['item_txn_amount']);
				$outward_amount_total = $value['item_txn_amount'];
			}

			$closing_qty = floatval($value['item_bal_qty']);

			$item_info  = $this->get_item_info($item_id, $this->company_id);
			if($item_info){
				$acc_id = 0;
				if($value['item_txn_drcr']=='d')
					$acc_id = $item_info['item_pur_acc'];

				if($value['item_txn_drcr']=='c')
					$acc_id = $item_info['item_sales_acc'];

				$account_info = $this->get_account_info($acc_id);
				$particulars = $account_info['acc_name'] ?? '';
			}

			$item_unit_info = $this->get_units_info($value['item_unit'],$this->company_id);
			if(isset($item_unit_info['item_unit']))
			   $unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');

			$builder = $this->db->table($itemtxnval_tbl);
			$builder->where('item_id', $value['item_id']);
			$builder->where('item_txn_id', $value['item_txn_id']);
			$builder->where('method_id', $val_id);
			$itemtxnval = $builder->get()->getRowArray();
			if($itemtxnval){
				$valuation = floatval($itemtxnval['item_value']);
			}
            					

			$final_result[]    = array(

				'voucher_no'		=> $value['comp_vch_no'],
				'voucher_txn_id'	=> $value['voucher_txn_id'],
				'comp_vch_series_id'=> $value['comp_vch_series_id'],
				'voucher_type_id'	=> $value['voucher_type_id'],
				'voucher_date'		=> $voucher_date,
				'voucher_type'		=> $value['comp_vch_type'],
				'particulars'		=> $particulars,
				'unit_name'			=> $unit_name,
				'inward_amount'			=> $inward_amount,
				'outward_amount'		=> $outward_amount,
				'inward_amount_total'	=> $inward_amount_total,
				'outward_amount_total'	=> $outward_amount_total,
				'inward_qty'			=> $inward_qty,
				'outward_qty'			=> $outward_qty,
				'inward_qty_total'		=> $inward_qty_total,
				'outward_qty_total'		=> $outward_qty_total,
				'closing_qty'			=> $closing_qty,
				'valuation'				=> $valuation,

			);
		  }
	   }
	
		// return $final_result;
		return [
			'total_records'	=> $total_records,
			'data'	=> $final_result
		];
	}

	function get_account_info($acc_id){	 
	  	$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	  	return $this->db->table($account_master_tbl)->where('acc_id', $acc_id)->get()->getRowArray();   	   
    }
    
    public function get_voucher_info($voucher_txn_id,$comp_id){
	    $ses_comp_fy_id   = $this->session->get('ses_comp_fy_id');
		$tbl_name         = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
		$builder          = $this->db->table($tbl_name); 
		$builder->where('comp_id', $comp_id);
		$builder->where('voucher_txn_id', $voucher_txn_id);
		if($this->session->get('ses_boid')!='')
		  $builder->where('bo_id', $this->session->get('ses_boid'));
		$result =  $builder->get()->getRowArray();
		return $result;
	 }
	 
	public function get_item_name($id){
         $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
         $data = $this->db->table($item_master_tbl)->select('item_name')->where('item_id', $id)->get()->getRowArray();
         $item_name = $data['item_name'];
         return $item_name;
     }
   
  	public function get_itemstable_rownums($tblname){
        $builder = $this->db->table($tblname);
        $builder->select('*,ROW_NUMBER() OVER (ORDER BY item_txn_date) row_num');
        $result = $builder->get()->getResultArray();
        return $result;
     }
    
   public function GetOpnBalanItm($table_name,$item_id){
       $comp_id =  $this->company_id;
	   if($this->session->get('ses_boid')!='')		
	  return $this->db->table($table_name)->where($table_name.'.bo_id', $this->session->get('ses_boid'))->where('batch_id',0)->where('item_id',$item_id)->where('comp_id',$comp_id)->get()->getResultArray();
	  else
      return $this->db->table($table_name)->where('batch_id',0)->where('item_id',$item_id)->where('comp_id',$comp_id)->get()->getResultArray();
       
    } 


	public function GetOpnValueItm($table_name,$item_id){
       $comp_id =  $this->company_id;
	   $all_units_op_value = array();
	   if($this->session->get('ses_boid')!='')     
		$result =  $this->db->table($table_name)->where('bo_id', $this->session->get('ses_boid'))->where('item_id',$item_id)->where('comp_id',$comp_id)->get()->getResultArray();
       else 
       $result =  $this->db->table($table_name)->where('item_id',$item_id)->where('comp_id',$comp_id)->get()->getResultArray();
	   if($result){
		   foreach($result as $row){
			    if($row["method_id"]=='0')
			       $all_units_op_value[$row["mat_cent_id"]][$row["item_unit"]][0] = $row["op_bal_val"]; 
			   if($row["method_id"]=='1')
			       $all_units_op_value[$row["mat_cent_id"]][$row["item_unit"]][1] = $row["op_bal_val"]; 
			   if($row["method_id"]=='2')
			       $all_units_op_value[$row["mat_cent_id"]][$row["item_unit"]][2] = $row["op_bal_val"]; 
		   }
		   
	   }
    return $all_units_op_value;
    } 

  public function ItmbatchDifrExist($table_name,$item_id,$item_unit,$batch_id){
	$response =  $this->db->table($table_name)->where('batch_id',$batch_id)->where('item_id',$item_id)->where('item_unit',$item_unit)->get()->getResultArray();  
	 if($response)
		return "1";
		else
		return "0";	
  }	
	
  public function UpdateOpBalBatchTable($table_name,$item_id,$item_unit,$batch_id,$update_data){
	$this->db->table($table_name)->where('batch_id',$batch_id)->where('item_id',$item_id)->where('item_unit',$item_unit)->update($update_data);  
	
  }
  
  public function get_item_unit_balance($table_name,$item_id,$unit_id){
	return  $this->db->table($table_name)->where('batch_id',0)->where('item_id',$item_id)->where('item_unit',$unit_id)->get()->getRowArray(); 
	  
  }
  
   public function undefined_balance($item_id){
	$table_name = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id');	   
    $row   = $this->db->table($table_name)->where('batch_no >','UNDEFINED')->get()->getRowArray();
     $all_list = array();
	if($row){		
			 if($row['	batch_unit']){
			   $get_units_info  = $this->get_units_info($row['batch_unit'],$this->company_id);
		       $batch_unit_name = $this->enc_string->nc_string($get_units_info['item_unit'],'de');
			   }
		     else 
			  $batch_unit_name = '';
			$all_list = array("batch_id"=>$row['batch_id'],"batch_no"=>$row['batch_no'],"batch_expiry"=>"","batch_mfr"=>"","item_id"=>$row['item_id'],
			                   "batch_qty"=>$row['batch_qty'],"batch_unit"=>$row['batch_unit'],"batch_unit_name"=>$batch_unit_name);
		
		
	}
   return $all_list;
   }
   
   public function get_item_batch($item_id){       
	   $table_name = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id');	   
       $batch_data =  $this->db->table($table_name)->where('item_id',$item_id)->get()->getResultArray();
	  
	   $alllist = array();
	   if($batch_data){
		   foreach($batch_data as $row){
			   // check if item , unit id has undefined balance then show 
			$undefined_balance = $this->undefined_balance($row['item_id']);
			if($undefined_balance)
			   $alllist[] = $undefined_balance;
		   	   
			if($row['batch_unit']){
			   $get_units_info  = $this->get_units_info($row['batch_unit'],$this->company_id);
		       $batch_unit_name       = $this->enc_string->nc_string($get_units_info['item_unit'],'de');
			   }
		     else 
			  $batch_unit_name = '';  
		  
		   $row['batch_unit_name'] = $batch_unit_name;
		   if($row)
		   $alllist[]=$row;
		    }
	   }	
      return $alllist; 
    }     
     
   public function UpdateOpBalTable($table_name,$update_data,$item_unit,$item_id){
       $comp_id =  $this->company_id;
        $this->db->table($table_name)->where('item_id',$item_id)->where('item_unit',$item_unit)->where('comp_id',$comp_id)->update($update_data);	
     }
     
       
   public function update_all_entries($table_name,$update_data,$txn_id,$comp_id){
       $this->db->table($table_name)->where('txn_id',$txn_id)->where('comp_id',$comp_id)->update($update_data);	
   }
   
   public function update_item_all_balances($table_name){
	    $this->db->transStart();
	    $builder = $this->db->table($table_name); 
		$builder->orderBy('item_txn_id');	
        $result =  $builder->get()->getResultArray();
		$balance =0;
		$counter=0;
		$first_balance=0;
        if($result){
		   foreach($result as $row){
			   $counter=$counter+1;
			     $sel_voucher_typer = $row['item_txn_drcr'];
			      if(strtolower($sel_voucher_typer)=='c'){
					  $acc_txn_drcr_amount = $row['item_bal'];
					  $amount_type ='c';					  
					}
					else if(strtolower($sel_voucher_typer)=='d'){
					  $acc_txn_drcr_amount = $row['item_bal'];
					  $amount_type  = 'd';					 
					} 
			
				$balance +=  $acc_txn_drcr_amount;
		   
             $this->db->table($table_name)->where('item_txn_id',$row['item_txn_id'])->where('comp_id',$row['comp_id'])->where('item_id',$row['item_id'])->update(array('item_op_qty'=>$balance));		   
		     if ($this->db->transStatus() === true) {
				$this->db->transComplete();
		      }

		   }				
		}
		
    }
	
   public function remove_item_dimentions($item_id){
	  $item_tbl = $this->company_id.'_itemdimens_'.$this->session->get('ses_comp_fy_id');  
	  $this->db->transStart();
	  $this->db->table($item_tbl)->where("item_id",$item_id)->delete(); 
      $this->db->transComplete();
      
	  if ($this->db->transStatus() === FALSE)
        {
            $this->db->transRollback();           
        }
        else{
            $this->db->transCommit();            
        }
   }
   
   public function add_batch_value($data){
	 $batchval_tbl = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id');   

	$exists = $this->db->table($batchval_tbl)->where("item_id",$data['item_id'])->where('LOWER(batch_no)',strtolower($data['batch_no']))->get()->getRowArray(); 
	    if($exists){
		  $batch_id = $exists['batch_id'];			  
		}
		else{
	     $this->db->table($batchval_tbl)->insert($data);
		 $batch_id = $this->db->insertID();
		}
		return $batch_id;	     	  	
	}
   
   public function CreateUndefinedBatch($data,$item_id){
	    $table_name = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id'); 
	    $exists = $this->db->table($table_name)->where("item_id",$item_id)->where('batch_no','UNDEFINED')->get()->getRowArray(); 
	    if($exists){
		  $batch_id = $exists['batch_id'];			  
		}
		else{
	     $this->db->table($table_name)->insert($data);
		 $batch_id = $this->db->insertID();
		}
		return $batch_id;
    }
   
   public function update_batch_value($update_data,$batch_id){
	    $table_name = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id'); 
	    $this->db->table($table_name)->where('batch_id',$batch_id)->update($update_data);
   }
   public function update_undefined_batch($update_data,$item_id){
	    $table_name = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id'); 
	    $this->db->table($table_name)->where('item_id',$item_id)->where('batch_no','UNDEFINED')->update($update_data);
   }
   
    public function remove_item_itmoppyvalue($item_id){
	  $item_tbl = $this->company_id.'_itmoppyval_'.$this->session->get('ses_comp_fy_id');  
	  $this->db->transStart();
	  $this->db->table($item_tbl)->where("item_id",$item_id)->delete(); 
      $this->db->transComplete();
      
	  if ($this->db->transStatus() === FALSE)
        {
            $this->db->transRollback();           
        }
        else{
            $this->db->transCommit();            
        }
   }
   
   
   public function remove_item_itmoppybal($item_id){
	  $item_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');  
	  $this->db->transStart();
	  
	  if($this->session->get('ses_boid')!='')      
		$this->db->table($item_tbl)->where('bo_id', $this->session->get('ses_boid'))->where("item_id",$item_id)->where("batch_id",0)->delete(); 
	
      else 
	  $this->db->table($item_tbl)->where("item_id",$item_id)->where("batch_id",0)->delete(); 
      $this->db->transComplete();
      
	  if ($this->db->transStatus() === FALSE)
        {
            $this->db->transRollback();           
        }
        else{
            $this->db->transCommit();            
        }
   }
   public function remove_items_info($item_id){
	  $item_tbl = $this->company_id.'_iteminfonn_'.$this->session->get('ses_comp_fy_id');  
	  $this->db->transStart();
	  $this->db->table($item_tbl)->where("item_id",$item_id)->delete(); 
      $this->db->transComplete();
      
	  if ($this->db->transStatus() === FALSE)
        {
            $this->db->transRollback();           
        }
        else{
            $this->db->transCommit();            
        }
   } 
   public function remove_item_parameters($item_id){
	  $item_tbl = $this->company_id.'_itmparamtr_'.$this->session->get('ses_comp_fy_id'); 
	   $item_prmtr_valtbl = $this->company_id.'_itmparamtr_'.$this->session->get('ses_comp_fy_id'); 
	  $get_item_itmparamtr_info = $this->get_item_itmparamtr_info($item_id);
	  if($get_item_itmparamtr_info){
		  foreach($get_item_itmparamtr_info as $rr){
			  
			    $this->db->transStart();
				$this->db->table($item_prmtr_valtbl)->where("paramtr_id",$rr['paramtr_id'])->delete(); 
				$this->db->transComplete(); 
		  }
		  
	  }
	  
	  $this->db->transStart();
	  $this->db->table($item_tbl)->where("item_id",$item_id)->delete(); 
      $this->db->transComplete();
      
	  if ($this->db->transStatus() === FALSE)
        {
            $this->db->transRollback();           
        }
        else{
            $this->db->transCommit();            
        }
   }	
	
   public function add_item_info($data)
   {
       $item_info_tbl = $this->company_id.'_iteminfonn_'.$this->session->get('ses_comp_fy_id');
       $this->db->table($item_info_tbl)->insert($data);
   } 
    
   public function add_item($data){	    
        $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');

	    $table = $this->db->table($item_master_tbl)->where('comp_id',$data['comp_id'])
	                                                ->where('LOWER(item_name)', strtolower(trim($data['item_name'])))
	                                                ->orWhere('comp_id',$data['comp_id'])
	                                                ->where('LOWER(item_upc)', strtolower(trim($data['item_name'])))
	                                                ->orWhere('comp_id',$data['comp_id'])
	                                                ->where('LOWER(item_alias)', strtolower(trim($data['item_name'])))
	                                               ->get()->getRowArray();
	                                               
	    if($table){
		    return ['status' => false, 'message' => 'Item Name must be unique'];
	    }
	    $table = $this->db->table($item_master_tbl)->where('comp_id',$data['comp_id'])
	                                                ->where('LOWER(item_name)', strtolower(trim($data['item_upc'])))
	                                                ->orWhere('comp_id',$data['comp_id'])
	                                                ->where('LOWER(item_upc)', strtolower(trim($data['item_upc'])))
	                                                ->orWhere('comp_id',$data['comp_id'])
	                                                ->where('LOWER(item_alias)', strtolower(trim($data['item_upc'])))
	                                               ->get()->getRowArray();
	                                               
	    if($table){
		    return ['status' => false, 'message' => 'Product ID must be unique'];
	    }
	    $table = $this->db->table($item_master_tbl)->where('comp_id',$data['comp_id'])
	                                                ->where('LOWER(item_name)', strtolower(trim($data['item_alias'])))
	                                                ->orWhere('comp_id',$data['comp_id'])
	                                                ->where('LOWER(item_upc)', strtolower(trim($data['item_alias'])))
	                                                ->orWhere('comp_id',$data['comp_id'])
	                                                ->where('LOWER(item_alias)', strtolower(trim($data['item_alias'])))
	                                               ->get()->getRowArray();
	                                               
	    if($table){
		    return ['status' => false, 'message' => 'Item Alias must be unique'];
	    }


      $ses_comp_fy_id   = $this->session->get('ses_comp_fy_id');    
	  $this->db->table($item_master_tbl)->insert($data);
	  $item_id         = $this->db->insertID();
	  return ['status' => true, 'item_id' => $item_id];
	     
     }
 
  function account_info($account_id){	 
       $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
       return $this->db->table($account_master_tbl)->where('acc_id', $account_id)->get()->getRowArray();   	   
   }   
       
   public function remove_items($ids,$comp_id){
		$ses_comp_fy_id   =  $this->session->get('ses_comp_fy_id');
		$item_master_tbl  = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
		$ids = explode(",",$ids);
        foreach($ids as $item_id){			
			$this->db->table($item_master_tbl)->where('item_id',$item_id)->delete();
			
		 }
		return TRUE;
	 } 
	 
	 public function remove_single_items($id){
		$item_master_tbl  = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
		$this->db->table($item_master_tbl)->where('item_id',$id)->delete();
		return TRUE;
	 }
	 
	 public function remove_itm_prmydmns($id){
		$item_info_tbl = $this->company_id.'_itemdimens_'.$this->session->get('ses_comp_fy_id');
		$this->db->table($item_info_tbl)->where('item_id',$id)->delete();
		
		$item_info_tbl = $this->company_id.'_itmparamtr_'.$this->session->get('ses_comp_fy_id');
		$this->db->table($item_info_tbl)->where('item_id',$id)->delete();		
		return TRUE;
	 }
	 
	 function check_item_with_voucher($id)
     {
         $voucher_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($voucher_tbl)->where('master_id', $id)->where('master_id_type','itm')->get()->getRowArray();
	     if(!empty($data)){
	         return 1;
	     }
	     return 0;
     }

	 public function remove_groups($ids,$comp_id){
		$ses_comp_fy_id   =  $this->session->get('ses_comp_fy_id');
		$item_grp_master_tbl  =  $this->company_id.'_itemgrpmst_'.$ses_comp_fy_id;
		$ids = explode(",",$ids);
        foreach($ids as $group_id){			
			$this->db->table($item_grp_master_tbl)->where('item_grp_id',$group_id)->delete();			
		 }
		return TRUE;
	 }
	 public function remove_single_groups($id){
		$ses_comp_fy_id   =  $this->session->get('ses_comp_fy_id');
		$item_grp_master_tbl  =  $this->company_id.'_itemgrpmst_'.$ses_comp_fy_id;
		
		$this->db->table($item_grp_master_tbl)->where('item_grp_id',$id)->delete();			
		return TRUE;
	 }

    public function remove_catgeory($ids,$comp_id){
		$ses_comp_fy_id       =  $this->session->get('ses_comp_fy_id');
		$item_cat_master_tbl  = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');
		$ids = explode(",",$ids);
        foreach($ids as $category_id){			
			$this->db->table($item_cat_master_tbl)->where('icatgms_id',$category_id)->delete();			
		 }
		return TRUE;
	 }
	 public function remove_single_category($id){
		$ses_comp_fy_id       =  $this->session->get('ses_comp_fy_id');
		$item_cat_master_tbl  = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');
					
		$this->db->table($item_cat_master_tbl)->where('icatgms_id',$id)->delete();			
		return TRUE;
	 } 
	 
	public function get_item_category_name($id)
	{
	    $item_cat_master_tbl  = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');
	    $data = $this->db->table($item_cat_master_tbl)->select('item_cat')->where('icatgms_id', $id)->get()->getRowArray();
        $item_cat_name = $this->enc_string->nc_string($data['item_cat'],'de');
        return $item_cat_name;
	}
	 
   public function add_category($data){
	   $item_cat_master_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');
	    $item_cat = $this->enc_string->nc_string($data['item_cat'],'de');
	    $exists   = $this->db->table($item_cat_master_tbl)->where('comp_id',$data['comp_id'])->where('LOWER(item_cat)', strtolower(trim($item_cat)))->get()->getRowArray(); 
	    if($exists)
		 return "0";
	   else{
		  $this->db->table($item_cat_master_tbl)->insert($data);
		  return $this->db->insertID();
	     }
   }
   
  public function modify_category($update_data,$category_id){
	  $item_cat_master_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');
	    $this->db->table($item_cat_master_tbl)->where('icatgms_id',$category_id)->update($update_data);		
		return true;	
   } 
  
   public function CreateItemTxnBalanceTable($tablename){			
	    $table_query ="CREATE TABLE IF NOT EXISTS `".$tablename."`(`itemtxnbal_id` BIGINT AUTO_INCREMENT,`item_txn_date` DATE,`item_id` BIGINT,`txn_id` BIGINT,`item_txn_drcr` VARCHAR(5),`item_txn_id` BIGINT,`voucher_txn_id` BIGINT,`bo_id` BIGINT DEFAULT 1,`mat_cent_id` BIGINT,`item_unit` INT,`item_bal_qty` VARCHAR(20), `batch_id` BIGINT NULL DEFAULT NULL,`item_avail` VARCHAR(1) NOT NULL DEFAULT '1', KEY `itemtxnbal index` (`item_id`,`txn_id`,`item_txn_id`,`voucher_txn_id`,`mat_cent_id`,`item_unit`) USING BTREE,PRIMARY KEY (`itemtxnbal_id`) ) ENGINE=MyISAM;";
	    $this->db->query($table_query);		
	   }

    public function CreateItemTxnValuationTable($tablename){	          
	    $table_query ='CREATE TABLE `'.$tablename.'` (`valuation_id` BIGINT NOT NULL AUTO_INCREMENT,`item_id` BIGINT,`item_txn_id` BIGINT,`method_id` INT(5),`item_value` VARCHAR(20),`avg_cost` VARCHAR(20),KEY `itemtxnvaln indexing` (`item_id`,`item_txn_id`,`method_id`) USING BTREE,	PRIMARY KEY (`valuation_id`)) ENGINE=MyISAM;';
	    $this->db->query($table_query);		
	   }
	   
    public function CreateItemTxnTable($tablename){	          
	    $table_query ='CREATE TABLE `'.$tablename.'` (
	    	`item_txn_id` BIGINT AUTO_INCREMENT,
	    	`comp_id` BIGINT,
	    	`item_txn_date` DATE,
	    	`item_txn_amount` DECIMAL(18,2),
	    	`item_txn_drcr` VARCHAR(5),
	    	`item_txn_qty` DECIMAL(18,4) NOT NULL,
	    	`item_id` BIGINT,
	    	`itemtxnbal_id` BIGINT,
	    	`voucher_txn_id` BIGINT,
	    	`bo_id` BIGINT DEFAULT 1,
	    	`txn_id` BIGINT,
	    	`mat_cent_id` BIGINT,
	    	`voucher_type_id` INT(5),
	    	`batch_id` BIGINT NOT NULL,
	    	`tagging_id` BIGINT,
	    	`item_unit` INT NOT NULL
	    	`item_bal_qty` DECIMAL(18,4) NOT NULL
	    	`item_avail` INT NOT NULL DEFAULT "1"

	    	 KEY `itemtxnnnn index` (`comp_id`, `item_id`, `voucher_txn_id`, `txn_id`, `mat_cent_id`, `voucher_type_id`, `batch_id`) USING BTREE,PRIMARY KEY (`item_txn_id`) ) ENGINE=InnoDB;';
	    $this->db->query($table_query);		
	   }
	   
	public function get_item_group_name($id){
         $item_grp_master_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
         $data = $this->db->table($item_grp_master_tbl)->select('item_grp_name')->where('item_grp_id', $id)->get()->getRowArray();
         $item_group_name = $this->enc_string->nc_string($data['item_grp_name'],'de');
         return $item_group_name;
     }   
	   
   public function add_group($data){
	    $item_grp_master_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
	   $item_grp = $this->enc_string->nc_string($data['item_grp_name'],'de');
	   $exists   = $this->db->table($item_grp_master_tbl)->where('comp_id',$data['comp_id'])->where('LOWER(item_grp_name)', strtolower(trim($item_grp)))->get()->getRowArray(); 
	    if($exists)
		 return "0";
	   else{
		  $this->db->table($item_grp_master_tbl)->insert($data);
		  return $this->db->insertID();
	     }
     }   
     
   public function update_group($update_data,$group_id){
	    $item_grp_master_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
	    $this->db->table($item_grp_master_tbl)->where('item_grp_id',$group_id)->update($update_data);		
		return true;	
   } 
   
    public function duplicate_item($item_id,$comp_id){
		$item_info = $this->get_item_info($item_id,$comp_id);
		$insert_data = [
						'comp_id'          => $comp_id,
						'item_name'          => $item_info['item_name'],
						'item_unit'          => $item_info['item_cat'],
						'item_grp_id'        => $item_info['item_grp_id'],
						'item_alias'         => $item_info['item_alias'],
						'item_print'         => $item_info['item_print'],
						'op_bal_qty'        => $item_info['op_bal_qty'],
						'op_bal_val'        => $item_info['op_bal_val'],
						'op_val_basis'        => $item_info['op_val_basis'],
						'tax_id'           => $item_info['tax_id'],
				// 		'item_cess'          => $item_info['item_cess'],
						'item_hsn'           => $item_info['item_hsn'],
						'item_sales_acc'     => $item_info['item_sales_acc'],
						'item_pur_acc'       => $item_info['item_pur_acc'],
						'mat_cent_id'        => $item_info['mat_cent_id'],
						'item_cat'           => $item_info['item_cat']
					    ];	 					
		$item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');		
	    $this->db->table($item_master_tbl)->insert($insert_data);	
		return $this->db->insertID();	
   } 
   
   public function update_item($data,$item_id){
	   $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');

	    $table = $this->db->table($item_master_tbl)->where('comp_id',$this->company_id)
	                                                ->where('LOWER(item_name)', strtolower(trim($data['item_name'])))
	                                                ->where('item_id !=',$item_id)
	                                                ->orWhere('comp_id',$this->company_id)
	                                                ->where('LOWER(item_upc)', strtolower(trim($data['item_name'])))
	                                                ->where('item_id !=',$item_id)
	                                                ->orWhere('comp_id',$this->company_id)
	                                                ->where('LOWER(item_alias)', strtolower(trim($data['item_name'])))
	                                                ->where('item_id !=',$item_id)
	                                               ->get()->getRowArray();
	                                               
	    if($table){
		    return ['status' => false, 'message' => 'Item Name must be unique'];
	    }
	    $table = $this->db->table($item_master_tbl)->where('comp_id',$this->company_id)
	                                                ->where('LOWER(item_name)', strtolower(trim($data['item_upc'])))
	                                                ->where('item_id !=',$item_id)
	                                                ->orWhere('comp_id',$this->company_id)
	                                                ->where('LOWER(item_upc)', strtolower(trim($data['item_upc'])))
	                                                ->where('item_id !=',$item_id)
	                                                ->orWhere('comp_id',$this->company_id)
	                                                ->where('LOWER(item_alias)', strtolower(trim($data['item_upc'])))
	                                                ->where('item_id !=',$item_id)
	                                               ->get()->getRowArray();
	                                               
	    if($table){
		    return ['status' => false, 'message' => 'Product ID must be unique'];
	    }
	    $table = $this->db->table($item_master_tbl)->where('comp_id',$this->company_id)
	                                                ->where('LOWER(item_name)', strtolower(trim($data['item_alias'])))
	                                                ->where('item_id !=',$item_id)
	                                                ->orWhere('comp_id',$this->company_id)
	                                                ->where('LOWER(item_upc)', strtolower(trim($data['item_alias'])))
	                                                ->where('item_id !=',$item_id)
	                                                ->orWhere('comp_id',$this->company_id)
	                                                ->where('LOWER(item_alias)', strtolower(trim($data['item_alias'])))
	                                                ->where('item_id !=',$item_id)
	                                               ->get()->getRowArray();
	                                               
	    if($table){
		    return ['status' => false, 'message' => 'Item Alias must be unique'];
	    }
	    
	    $this->db->table($item_master_tbl)->where('item_id',$item_id)->update($data);		
		return ['status' => true, 'item_id' => $item_id];	
   } 
   public function update_item_info($update_data,$item_id){
	    $item_info_tbl = $this->company_id.'_iteminfonn_'.$this->session->get('ses_comp_fy_id');
	    $data = $this->db->table($item_info_tbl)->where('item_id',$item_id)->get()->getRowArray();
	    if($data){
	        $this->db->table($item_info_tbl)->where('item_id',$item_id)->update($update_data);
	    }
	    else{
	        $update_data['item_id'] = $item_id;
	        $this->db->table($item_info_tbl)->insert($update_data);
	    }
	    		
		return true;	
   } 
   
   public function ajax_category_list(){
	   $pq_curPage=1;
	    if($this->company_id){
		$item_cat_master_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');
        $builder  = $this->db->table($item_cat_master_tbl); 
        $builder->orderBy('item_cat');
		$builder->where('comp_id', $this->company_id);		 	
        $result  = $builder->get()->getResultArray(); 
        $records = array();       
        foreach($result as $values){
			$item_cat = $this->enc_string->nc_string($values['item_cat'],'de');
           $records[] = array(	
		              'category_id' => $values['icatgms_id'],
                      'item_catg'   => ucwords($item_cat),
                      'item_cat_alias'   =>$values['item_cat_alias']
				   );  
		       }	
		   }
		else{
			$records=array();
		  }		
		$totalrec = count($records);
	   echo  "{\"totalRecords\":" .$totalrec . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
     }
   
   public function ajax_group_list($limit,$pq_curPage){
	    $records = array();		
		if($this->company_id){
		
		$comp_id  = $this->session->get('ses_company_id');
	    	
        $item_grp_master_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');		
        $builder  = $this->db->table($item_grp_master_tbl); 
        $builder->orderBy('item_grp_name');
		$builder->where('comp_id', $comp_id);		 	
        $result  = $builder->get()->getResultArray(); 
        
        foreach($result as $values){
           $records[] = array(	
                      'item_grp_id'      => $values['item_grp_id'],
				  	  'item_grp_name'    => ucwords($this->enc_string->nc_string($values['item_grp_name'],'de')),
					  'item_grp_alias'   => $this->enc_string->nc_string($values['item_grp_alias'],'de'),
					  'item_grp_primary' => ucwords($values['item_grp_primary'])					  					  	
				       ); 
		            }	
					
		}
		$total_records = count($records);
		return [
			'total_records'	=> $total_records,
			'data'	=> $records
		];
     }
   
   public function ajax_items_list(){
	    $comp_id    =  $this->session->get('ses_company_id');
	    $base_url      = base_url().'/'.getenv('AdminPath');
        $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($item_master_tbl); 
        $builder->orderBy('item_name');
		$builder->where('comp_id', $comp_id);		 	
        $result = $builder->get()->getResultArray(); 
        $records = array();		
        foreach($result as $values){
            $item_unit_info  = $this->get_units_info($values['item_unit'],$values['comp_id']);
			$item_group_info = $this->item_group_info($values['item_grp_id'],$values['comp_id']);			
			if(isset($item_group_info['item_grp_id']))
				$item_grp = $this->enc_string->nc_string($item_group_info['item_grp_name'],'de');
			 else
				$item_grp = ''; 
			if(isset($item_unit_info['item_unit']))
				$item_unit = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
			 else
				$item_unit = ''; 
			
			$item_catg_info  = $this->item_category_info($values['item_cat'],$values['comp_id']);	
            if($item_catg_info){
				$item_cat = $item_catg_info['item_cat'];
			}			
			else
				$item_cat = '';
			$records[]       = array(	
    							  'item_id'     => $values['item_id'],
    							  'item_name'   => ucwords($values['item_name']),
    							  'item_alias'  => $values['item_alias'],
    							  'item_grp'    => $item_grp,
    							  'item_unit'   => $item_unit,
    							  'item_sku'   => $values['item_sku'],
    							  'item_cat'    => $item_cat					  
				                 );  
		           }	
 	       return $records;
     }
     
     
         
         
    function get_item_detail_info($item_id,$comp_id)
    {
        $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($item_master_tbl);
        $builder->select($item_master_tbl.'.*');
        $builder->select($item_master_tbl.'.item_id as item_id');
        $result = $builder->where($item_master_tbl.'.item_id', $item_id)->where('comp_id', $comp_id)->get()->getRowArray();
        
        return $result;
    }
    
	function get_item_info($item_id,$comp_id){	 
	  $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($item_master_tbl)->where('item_id', $item_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    }

    function get_item_itemdimens_info($item_id){
      $comp_id = $this->company_id;
	  $item_master_tbl = $this->company_id.'_itemdimens_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($item_master_tbl)->where('item_id', $item_id)->get()->getResultArray();   	   
    }
	function get_item_itmparamtr_info($item_id){	
      $comp_id = $this->company_id;	
	  $item_master_tbl = $this->company_id.'_itmparamtr_'.$this->session->get('ses_comp_fy_id');
	  $result = $this->db->table($item_master_tbl)->where('item_id', $item_id)->get()->getResultArray();   	   
	  $all_info = array();
	  if($result){
		  foreach($result as $row){
			 $parameters_values = $this->get_parameterval_info($row['paramtr_id']); 
			 $all_info[]= array('paramtr_id'=>$row['paramtr_id'],'paramtr_name'=>$row['paramtr_name'],'paramtr_values'=>$parameters_values); 
		  }
	  }
	 return $all_info;
    }
	
	
	 function all_items_list(){
      $company_id  = $this->company_id;
      $comp_fy_id  = $this->session->get('ses_comp_fy_id');
      
       $item_master_tbl = $company_id.'_itemmaster_'.$comp_fy_id;
	   $data =  $this->db->table($item_master_tbl)
	   					->select('item_name as label, item_name as value,item_upc, item_id, item_unit')
	   					->orderBy('item_name','ASC')
	   					->get()->getResultArray();

	   $final_result = array();
	   if($data){
		  foreach($data as $row){
		      
		       	$item_unit_info  = $this->item_unit_info($row['item_unit']); 
             	if($item_unit_info)
                	$item_unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');				 
              	else
                	$item_unit_name ='' ;			  
		      
               $final_result[]    = array(
               		'item_id'      => $row['item_id'],
					'item_upc'     => $row['item_upc'],
               		'item_unit'    => $item_unit_name,
					"item_name"    => ucwords($row['value']),
               		'item_unit_id' => $row['item_unit'],
				    'noof_barcode' => '',
                  	'noof_label'   => '',
					'barcode'      => '',
					'barcode_type' => '' 					
               	    );	
		         }
           }		
	   return $final_result;
     
 }  
	
	function get_parameterval_info($paramtr_id){	 
	  $comp_id = $this->company_id;
	  $paramtrval_tbl = $this->company_id.'_paramtrval_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($paramtrval_tbl)->where('paramtr_id', $paramtr_id)->get()->getResultArray();   	   
    } 	
	
	function get_item_iteminfonn_info($item_id){	 
	  $comp_id = $this->company_id;
	  $item_master_tbl = $this->company_id.'_iteminfonn_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($item_master_tbl)->where('item_id', $item_id)->get()->getResultArray();   	   
    }
	
    function get_units_info($unit_id,$comp_id){	 
	
	 $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    }   
	
	function item_category_info($catg_id,$comp_id){	 
	 $item_cat_master_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($item_cat_master_tbl)->where('icatgms_id', $catg_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    }  
	
   function item_group_info($item_grp_id,$comp_id){
       $item_grp_master_tbl =  $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id'); 	   
	  return $this->db->table($item_grp_master_tbl)->where('item_grp_id', $item_grp_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
   }   
   	 
   function items_group_dropdown($comp_id){
	   $item_grp_master_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
	   $data =  $this->db->table($item_grp_master_tbl)->where('comp_id', $comp_id)->orderBy('item_grp_name','ASC')->get()->getResultArray();
	   $final_result      = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['item_grp_id']] = $this->enc_string->nc_string($row['item_grp_name'],'de');			   
	        }
        }
	  return $final_result;	
     } 
	 
	 function get_groups_by_parent($id)
	 {
	 	$array = [];
	 	$tbl_name = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$data = $this->db->table($tbl_name)
    					->select('acc_grp_id')
    					->where('acc_grp_parent_id', $id)
    					->get()->getResultArray();

    	if($data){
    		foreach($data as $key => $value) {
    			array_push($array, $value['acc_grp_id']);
    		}
    	}
    	return $array;
	 }
	 
	 function sales_acc_dropdown($comp_id){
	 	$array = $this->get_groups_by_parent(8);

	   $acc_sales_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	   if($array){
	   $data =  $this->db->table($acc_sales_tbl)->whereIn('acc_grp_id', $array)->orWhere('acc_grp_parent_id', 8)->orderBy('acc_name','ASC')->get()->getResultArray();
	       
	   }
	   else{
	   $data =  $this->db->table($acc_sales_tbl)->orWhere('acc_grp_parent_id', 8)->orderBy('acc_name','ASC')->get()->getResultArray();
	       
	   }
	   $final_result = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['acc_id']] = $row['acc_name'];			   
	        }
        }
	  return $final_result;	
     }
     
    function purchase_acc_dropdown($comp_id){
    	$array = $this->get_groups_by_parent(7);

	   $acc_purchase_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	  if($array)
	   $data =  $this->db->table($acc_purchase_tbl)->whereIn('acc_grp_id', $array)->orWhere('acc_grp_parent_id', 7)->orderBy('acc_name','ASC')->get()->getResultArray();
	  else{
	    $data =  $this->db->table($acc_purchase_tbl)->orWhere('acc_grp_parent_id', 7)->orderBy('acc_name','ASC')->get()->getResultArray();
	    
	  }
	  $final_result = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['acc_id']] = $row['acc_name'];			   
	        }
        }
	  return $final_result;	
     }
     
     
     
     function group_main_dropdown(){
	   $item_groups_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
	   $data =  $this->db->table($item_groups_tbl)->where('comp_id', $this->company_id)->orderBy('item_grp_id','ASC')->get()->getResultArray();
	   $final_result = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['item_grp_id']] = $this->enc_string->nc_string($row['item_grp_name'],'de');			   
	        }
        }
	  return $final_result;	
     } 
     
	 
	 function units_dropdown($comp_id){
	   $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	   $data =  $this->db->table($item_unit_master_tbl)->where('comp_id', $comp_id)->orderBy('item_unit','ASC')->get()->getResultArray();
	   $final_result = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['unit_id']] = $this->enc_string->nc_string($row['item_unit'],'de');			   
	        }
        }
	  return $final_result;	
     } 
	 
	 function category_dropdown($comp_id){
	   $item_cat_master_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');
	   $data =  $this->db->table($item_cat_master_tbl)->where('comp_id', $comp_id)->orderBy('item_cat','ASC')->get()->getResultArray();
	   $final_result      = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['icatgms_id']] = $this->enc_string->nc_string($row['item_cat'],'de');			   
	        }
        }
	  return $final_result;	
     }
	 
	 function material_centre_dropdown($comp_id){
	   $mat_centre_master_tbl =$this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	   $data =  $this->db->table($mat_centre_master_tbl)->where('comp_id', $comp_id)->orderBy('mat_cent_name','ASC')->get()->getResultArray();
	   $final_result      = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['mat_cent_id']] = $this->enc_string->nc_string($row['mat_cent_name'],'de');   
	        }
        }
	  return $final_result;	
     }
     
     function check_item_with_category($cat_id)
     {
         $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($item_master_tbl)->where('item_cat', $cat_id)->get()->getRowArray();
	     if(!empty($data)){
	         return 1;
	     }
	     return 0;
     }
     
     function check_item_with_group($group_id)
     {
         $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($item_master_tbl)->where('item_grp_id', $group_id)->get()->getRowArray();
	     if(!empty($data)){
	         return 1;
	     }
	     return 0;
     }
     
     function check_item_group($value)
     {
         $en_value = $this->enc_string->nc_string($value,'en');
         $item_grp_master_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($item_grp_master_tbl)->where('comp_id',$this->company_id)->where('LOWER(item_grp_name)', strtolower(trim($en_value)))->get()->getRowArray();
	     if(!empty($data)){
	         return 1;
	     }
	     return 0;
     }
     
     function get_or_create_group_id($value)
     {
         if(trim($value) == ''){
             $value = 'Main';
         }
         else{
             $value = trim($value);
         }
         $en_value = $this->enc_string->nc_string($value,'en');
         $item_grp_master_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($item_grp_master_tbl)->where('comp_id',$this->company_id)->where('LOWER(item_grp_name)', strtolower(trim($en_value)))->get()->getRowArray();
	     if(!empty($data)){
	         return $data['item_grp_id'];
	     }
	     else{
	         $item_group = [
	             'comp_id'          => $this->company_id,
	             'item_grp_name'    => $en_value,
	             'item_grp_alias'   => $en_value,
	             'item_grp_primary' => ''
	             ];
	         $this->db->table($item_grp_master_tbl)->insert($item_group);
	        return $this->db->insertID();
	     }
     }
     
     public function get_or_create_category_id($value)
     {
        if(trim($value) == ''){
            return 0;
        }
        $item_cat_master_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');
	    $en_value = $this->enc_string->nc_string(trim($value),'en');
	    $data   = $this->db->table($item_cat_master_tbl)->where('comp_id',$this->company_id)->where('LOWER(item_cat)', strtolower(trim($en_value)))->get()->getRowArray(); 
	    if(!empty($data))
		 return $data['icatgms_id'];
	   else{
	       $item_category = [
	            'comp_id'    => $this->company_id,
	            'item_cat'   => $en_value
	       ];
		  $this->db->table($item_cat_master_tbl)->insert($item_category);
		  return $this->db->insertID();
	     }
     }
     
     public function get_or_create_unit_id($value)
     {
         if(trim($value) == ''){
             $name = 'NA';
         }
         else{
             $name = trim($value);
         }
        $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	    $en_name = $this->enc_string->nc_string($name,'en');
	    $data   = $this->db->table($item_unit_master_tbl)->where('comp_id',$this->company_id)->where('LOWER(item_unit)', strtolower(trim($en_name)))->get()->getRowArray(); 
	    if(!empty($data))
		 return $data['unit_id'];
	    else{
	       $item_unit = [
	            'comp_id'           => $this->company_id,
	            'item_unit'         => $en_name,
	            'item_unit_alias'   => $en_name,
	            'item_unit_print'   => $en_name,
	            'item_unit_uqc'     => ''
	       ];
		  $this->db->table($item_unit_master_tbl)->insert($item_unit);
		  return $this->db->insertID();
	     }
     }
	 
    public function insert_items_batch($data)
    {
        $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($item_master_tbl)->insertBatch($data);
    }
}