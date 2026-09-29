<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;

use App\Models\CommonModel;

class BulkUpdationModel extends Model	{

    public function __construct() {
       parent::__construct();        
       $this->externaldb     = new externaldb();	
	  
	   $this->session        = \Config\Services::session();
	   $this->dberpunvrsl    = $this->externaldb->erp_db();
	   $this->CommonModel    = new CommonModel();
	   $this->company_id     = $this->session->get('ses_company_id');
	   $this->ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
	   $this->bo_id         = $this->session->get('ses_boid');
	   $this->fy_id         = $this->session->get('ses_comp_fy_id');
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->aicountly_db   = $this->externaldb->aicountly_db();
    }

  function sales_accounts_lists(){
	 	$array = $this->get_groups_by_parent(8);

	   $acc_sales_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	   if($array){
	   $data =  $this->db->table($acc_sales_tbl)->whereIn('acc_grp_id', $array)->orWhere('acc_grp_parent_id', 8)->orderBy('acc_name','ASC')->get()->getResultArray();
	       
	   }
	   else{
	   $data =  $this->db->table($acc_sales_tbl)->orWhere('acc_grp_parent_id', 8)->orderBy('acc_name','ASC')->get()->getResultArray();
	       
	   }
	   $final_result = array();
	   if($data){
		  foreach($data as $row){
              $final_result[] = array("id"=>$row['acc_id'],"label"=>$row['acc_name'],"value"=>$row['acc_name']);			   
	        }
        }
	  return $final_result;	
     }
     
    function purchase_accounts_lists(){
    	$array = $this->get_groups_by_parent(7);

	   $acc_purchase_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	  if($array)
	   $data =  $this->db->table($acc_purchase_tbl)->whereIn('acc_grp_id', $array)->orWhere('acc_grp_parent_id', 7)->orderBy('acc_name','ASC')->get()->getResultArray();
	  else{
	    $data =  $this->db->table($acc_purchase_tbl)->orWhere('acc_grp_parent_id', 7)->orderBy('acc_name','ASC')->get()->getResultArray();
	    
	  }
	  $final_result = array();
	   if($data){
		  foreach($data as $row){
              $final_result[] = array("id"=>$row['acc_id'],"label"=>$row['acc_name'],"value"=>$row['acc_name']);	   
	        }
        }
	  return $final_result;	
     }	
  public function update_bdstaxmstn($billsundry_id,$bdstaxmstn_data){
	 $acctaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
	 $this->db->table($acctaxmstn_tbl)->where('bill_sundry_id',$billsundry_id)->update($bdstaxmstn_data);
  }
  function all_branchs(){
		$tbl_name     = $this->company_id.'_hobomaster_'.$this->session->get('ses_comp_fy_id');
		$data         = $this->db->table($tbl_name)
		->select('bo_id,bo_name,bo_ho')
		->get()->getResultArray();
		$all_bo['']='';				  
		if($data){
			foreach($data as $row){
				if($row['bo_ho']=='1')
					$all_bo[$row['bo_id']]=$row['bo_name'].'(HO)';
				else
					$all_bo[$row['bo_id']]=$row['bo_name'];	
			}
			
		}				  
		return 	$all_bo;		
		
	}
  public function update_acctaxmstn($data){
	    $acctaxmstn_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
	    $exist_response = $this->db->table($acctaxmstn_tbl)->where('acc_id',$data['acc_id'])->where('comp_id',$data['comp_id'])->get()->getRowArray();
	    if($exist_response){
			$updata= array("tax_cat_id"=>$data['tax_cat_id'],'cmp_tax_cat_id'=>$data['cmp_tax_cat_id']);
		    $this->db->table($acctaxmstn_tbl)->where('acc_id',$data['acc_id'])->where('comp_id',$data['comp_id'])->update($updata);
		
		}
	    else{
			$updata= array("comp_id"=>$data['comp_id'],"acc_id"=>$data['acc_id'],"tax_cat_id"=>$data['tax_cat_id'],'cmp_tax_cat_id'=>$data['cmp_tax_cat_id']);
	        $this->db->table($acctaxmstn_tbl)->insert($data);		
		 }
   }
   
  public function add_itemtaxmstn($data){
	    $itemtaxmst_tbl = $this->company_id.'_itemtaxmst_'.$this->session->get('ses_comp_fy_id');
	    $exist_response = $this->db->table($itemtaxmst_tbl)->where('item_id',$data['item_id'])->where('comp_id',$data['comp_id'])->get()->getRowArray();
	    if($exist_response){
			$updata = array("cmp_tax_cat_id"=>$data["cmp_tax_cat_id"],"tax_cat_id"=>$data["tax_cat_id"]);
		  $this->db->table($itemtaxmst_tbl)->where('item_id',$data['item_id'])->where('comp_id',$data['comp_id'])->update($updata);		
		 }else{
	      $this->db->table($itemtaxmst_tbl)->insert($data);		
		 }
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
   function sales_acc_dropdown(){
	 	$array = $this->get_groups_by_parent(8);

	   $acc_sales_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	   if($array){
	   $data =  $this->db->table($acc_sales_tbl)->whereIn('acc_grp_id', $array)->orWhere('acc_grp_parent_id', 8)->orderBy('acc_name','ASC')->get()->getResultArray();
	       
	   }
	   else{
	   $data =  $this->db->table($acc_sales_tbl)->orWhere('acc_grp_parent_id', 8)->orderBy('acc_name','ASC')->get()->getResultArray();
	       
	   }
	   $final_result = array();	   
	   if($data){
		  foreach($data as $row){
				$final_result[]=array("label"=>$row['acc_name'],"value"=>$row['acc_name'],'sid'=>$row['acc_id']);		  
	        }
        }
	  return $final_result;	
     }
     
    function purchase_acc_dropdown(){
    	$array = $this->get_groups_by_parent(7);

	   $acc_purchase_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	  if($array)
	   $data =  $this->db->table($acc_purchase_tbl)->whereIn('acc_grp_id', $array)->orWhere('acc_grp_parent_id', 7)->orderBy('acc_name','ASC')->get()->getResultArray();
	  else{
	    $data =  $this->db->table($acc_purchase_tbl)->orWhere('acc_grp_parent_id', 7)->orderBy('acc_name','ASC')->get()->getResultArray();
	    
	  }
	  $final_result = array();	  
	   if($data){
		  foreach($data as $row){
              $final_result[]=array("label"=>$row['acc_name'],"value"=>$row['acc_name'],'pid'=>$row['acc_id']);		   
	        }
        }
	  return $final_result;	
     }
   
   function ajax_tax_category(){     		
	       $final = array();
			$list = $this->dberpunvrsl->table('aictlyerp_taxcatmstn_univdb')->where('status', '1')->get()->getResultArray();
			if($list)
				foreach($list as $row){				
					$final[]=array("label"=>$row['tax_cat_name'],"value"=>$row['tax_cat_name'],'id'=>$row['tax_cat_id'],'tax_cat_basis'=>$row['tax_cat_basis']);	
				}
		
  	 return $final;				   
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
	
	public function update_txn_entries($table_name, $acc_op_bal, $acc_op_drcr){
    $this->db->transStart();
	    $builder = $this->db->table($table_name); 
		$builder->orderBy('acc_txn_id');	
        $result =  $builder->get()->getResultArray();
		$balance =0;
		$counter=0;
		$first_balance=0;
        if($result){
            
            if($acc_op_drcr == 'dr'){
                $balance = parseAmount($acc_op_bal);
            }
            else if($acc_op_drcr == 'cr'){
                $balance = -parseAmount($acc_op_bal);
            }
            
		   foreach($result as $row){
			   $counter=$counter+1;
			     $sel_voucher_typer = $row['acc_txn_drcr'];
			      if(strtolower($sel_voucher_typer)=='c'){
					  $acc_txn_drcr_amount = $row['acc_txn_amount'];
					  $amount_type ='c';					  
					}
					else if(strtolower($sel_voucher_typer)=='d'){
					  $acc_txn_drcr_amount = $row['acc_txn_amount'];
					  $amount_type  = 'd';					 
					} 
				if($amount_type=='c')
				$balance -= parseAmount($acc_txn_drcr_amount);
			   else if($amount_type=='d')
				$balance +=  parseAmount($acc_txn_drcr_amount);
		   
          $this->db->table($table_name)->where('acc_txn_id',$row['acc_txn_id'])->where('comp_id',$row['comp_id'])->where('acc_id',$row['acc_id'])->update(array('acc_bal'=>$balance));		   
		 
		   }				
		}    
        
    }
   function update_account_balance($data){
	  $accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
      $update_data    = array("acc_op_bal"=>$data['acc_op_bal']);
      $exists = $this->db->table($accoppybal_tbl)->where('bo_id', $data['bo_id'])->where('acc_id', $data['acc_id'])->get()->getRowArray();
	  if($exists)
	    $this->db->table($accoppybal_tbl)->where('bo_id', $data['bo_id'])->where('acc_id', $data['acc_id'])->update($update_data);	  
      else{
		$insert_data = array("acc_id"=>$data['acc_id'],"acc_op_bal"=>$data['acc_op_bal'],"acc_py_bal"=>"0","bo_id"=>$data['bo_id']);  
		$this->db->table($accoppybal_tbl)->insert($insert_data);
	  }
   }	
	
   function update_item($item_id,$update_data){
      $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
      $this->db->table($item_master_tbl)->where('item_id', $item_id)->update($update_data);   
      //echo $this->db->getlastquery();
	}

function get_item_local_taxinfo($item_id){
   $itemtaxmst_tbl = $this->company_id.'_itemtaxmst_'.$this->session->get('ses_comp_fy_id');
   $cmptaxcatm_tbl = $this->company_id.'_cmptaxcatm_'.$this->session->get('ses_comp_fy_id');
   
   $response = $this->db->table($itemtaxmst_tbl)->where('item_id', $item_id)->get()->getRowArray();   
   if($response){
	  $cmp_tax_cat_id = $response['cmp_tax_cat_id'];
	  $resp = $this->db->table($cmptaxcatm_tbl)->where('cmp_tax_cat_id', $cmp_tax_cat_id)->get()->getRowArray();   
      if($resp)
	  $category_name = $resp['cmp_tax_cat_name'];
	  else
	 $category_name ='';	  
	  
   }  else
      	 $category_name ='';
  return $category_name;	 
 }
function get_item_taxinfo($tax_catg_id){
    
	   if($tax_catg_id){				        
			$taxinfo = $this->dberpunvrsl->table('aictlyerp_taxcatmstn_univdb')
				->where('tax_cat_id', $tax_catg_id)
				->where('status', '1')->get()->getRowArray();
			if($taxinfo)	
			$category_name = $taxinfo['tax_cat_name'];	
		   else
			$category_name       = '';   
			
		}else{
		    $category_name       = '';
			
		    }	
  	 return $category_name;				   
	}
  public function account_info($account_id){	 
		$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		return $this->db->table($account_master_tbl)->where('acc_id', $account_id)->get()->getRowArray();   	   
	}	
  public function load_sale_condensed($from_date, $to_date,$filter_data)
   {    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
		$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10; // default 10 rows per page
        $type =0;
        $voucher_type_id =18;
        $view=0;
        $voucher_series = $_POST['voucher_series'];
        $billno         = $_POST['billno'];
		$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
		
		if ($pq_curPage < 1) $pq_curPage = 1;
		if ($pq_rPP < 1) $pq_rPP = 10;
		
		$voucher_tbl    = 'acctvchreg';
		$account_tbl    = 'acctmaster';
		$accttxnmst_tbl = 'accttxnmst';
		$vchtypemst     = 'vchtypemst';
		$vchtxnconso    = 'vchtxnconso';
        $gstroutsup_tbl = 'gstroutsup';
		$builder = $this->db->table("$voucher_tbl v");
		
			$builder->select(
				'MAX(v.vch_date) AS vch_date,				
				 v.vch_txn_id, 
				 v.acct_vch_type,
				 conso.vch_series_id,
				 conso.vch_sub_type_id,
				 gstrsup.outsup_bill_ref_no,
				  vtmst.vch_name AS vch_type_name,
				 GROUP_CONCAT(a.acc_name ORDER BY a.acc_name SEPARATOR ", ") AS account_names, 
				 CASE WHEN v.acc_txn_dr_amt != 0 THEN v.acc_txn_dr_amt ELSE v.acc_txn_cr_amt END AS amount, 
				 MAX(v.vch_narr) AS narration,
				 v.acc_txn_dr_amt AS dr_amount,
				 v.acc_txn_cr_amt AS cr_amount',
				false
			);
			$builder->join("$account_tbl a", 'a.acc_id = v.acc_id', 'left');
			$builder->join("$vchtypemst vtmst", 'vtmst.vch_type_id = v.acct_vch_type', 'left');	
			
			$builder->join("$gstroutsup_tbl gstrsup", 'gstrsup.vch_txn_id  =v.vch_txn_id');
			$builder->join("$vchtxnconso conso", 'conso.vch_txn_id = v.vch_txn_id', 'left');
			
			$builder->where('v.cmp_id', $this->company_id);	
			if($type==2)
			$builder->where('v.acc_txn_type',2);
			else if($type==3)
			$builder->where('v.acc_txn_type',3);	
            else
            $builder->where('v.acc_txn_type',1);					
			$builder->where('v.txn_id IS NULL');	    
			$builder->where('v.hobo_id', $this->bo_id);
			
			if($filter_data['main_cond']=='and'){
			   
			   if($voucher_series!='' || $billno!='' ){
				   $builder->GroupStart();
				 if( $filter_data['series_cond']=='and'){
					 $builder->GroupStart();
				   $builder->where('conso.vch_series_id',$voucher_series);	
				   $builder->where('gstrsup.outsup_bill_ref_no',$billno);
					$builder->groupEnd();	
				}
			     if( $filter_data['series_cond']=='or'){
					 $builder->orGroupStart();
				    $builder->orLike('conso.vch_series_id',$voucher_series);	
					$builder->orLike('gstrsup.outsup_bill_ref_no',$billno);
					$builder->groupEnd();
				  }	  
				  $builder->groupEnd();   
			   }	
			}
		   if($filter_data['main_cond']=='or'){
			   
			   if($voucher_series!='' || $billno!='' ){
				   $builder->orGroupStart();
				 if( $filter_data['series_cond']=='and'){
					 $builder->GroupStart();
				   $builder->where('conso.vch_series_id',$voucher_series);	
				   $builder->where('gstrsup.outsup_bill_ref_no',$billno);
					$builder->groupEnd();	
				}
			     if( $filter_data['series_cond']=='or'){
					 $builder->orGroupStart();
				    $builder->orLike('conso.vch_series_id',$voucher_series);	
					$builder->orLike('gstrsup.outsup_bill_ref_no',$billno);
					$builder->groupEnd();
				  }	  
				  $builder->groupEnd();   
			   }	
			}
			
			
			
			if($type!=2)// optional voucher have no type will exists all vouchers
			$builder->where('v.acct_vch_type', $voucher_type_id);
			if (!empty($from_date)) {
				$builder->where('v.vch_date >=', $from_date);
			}
			if (!empty($to_date)) {
				$builder->where('v.vch_date <=', $to_date);
			}		
			$builder->groupBy('v.vch_txn_id');
		
		
		// Clone for total count before applying limit
		$countBuilder = clone $builder;
		$total_records = $countBuilder->countAllResults(false);

		// Pagination logic
		if ($pq_curPage == 0) $pq_curPage = 1;
		$offset = ($pq_rPP * ($pq_curPage - 1));

		if ($offset > $total_records) {
			$pq_curPage = ceil($total_records / $pq_rPP);
			$offset = ($pq_rPP * ($pq_curPage - 1));
		}
		if ($offset < 0) {
			$offset = 0;
		}

		// Apply order and limit
		$builder->orderBy('v.vch_date', 'ASC');		
		$builder->limit($pq_rPP, $offset);		
		// Fetch final data
		$result = $builder->get()->getResultArray();
		//echo $this->db->getlastquery();
		//die();
		$records = [];
		$last_vch_no = 0;
		foreach($result as $key => $value)
        {
		      $voucher_date   = date("d-m-Y", strtotime($value['vch_date']));
			  $voucher_no     = $value['vch_txn_id']; 
			  $vch_date       = $voucher_date;
			
			 
			 if($value['dr_amount'] >0)
			      $dr_cr='DR';
			  else
				      $dr_cr='CR';

			$records[] = [
			  'voucher_txn_id'  => $value['vch_txn_id'],
			  'voucher_type_id' => $value['acct_vch_type'],
			  'voucher_type'    => $value['vch_type_name'],
			  'account_name'    => $value['account_names'],
			  'voucher_date'    => $vch_date,
			  'amount'          => formatAmount($value['amount']),
			  'amount_total'    => parseAmount($value['amount']),
			  'narration'		=> $value['narration'],
			  'date'            => $vch_date,
			  'particulars'     => $value['account_names'],
			  'vch_subtype_id'  => $value['vch_sub_type_id'],
			  'bill_ref_no'		=> $value['outsup_bill_ref_no']	  
			  ];
			  
		}
		
	
		$totalPages = ($total_records/$pq_rPP);
        $totalPages = ceil($totalPages);
        $total_index = ceil($total_records/$pq_rPP);
		
	echo  "{\"offset\":" .$offset . ",\"total_index\":" .$total_index . ",\"limit\":" .$pq_rPP . ",\"renderRecods\":" .count($records) . ",\"totalPages\":" .$totalPages . ",\"totalRecords\":" .$total_records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
   
   }
   public function load_purchase_condensed($pq_curPage, $limit, $filter_data)
   {        $view=1;
			$search="";
	      	$voucher_tbl         = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	      	$voucher_type_tbl    = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	      	$voucher_series_tbl  = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	      	$comp_txn_tbl        = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	      	$acctcrsref_tbl      = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
			
			$gstroutsup_tbl      = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
			
			
			
			$voucher_series      = $filter_data['voucher_series'];
			$billno              = $filter_data['billno'];			
			$from_date           = $filter_data['from_date'];
			$to_date             = $filter_data['to_date'];
			
			
	      	$builder = $this->db->table($voucher_tbl);
	      	$builder->select($voucher_tbl.'.*');
	      	$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	      	$builder->select($voucher_type_tbl.'.comp_vch_type');
	      	$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	      	$builder->select($voucher_series_tbl.'.comp_vch_series');
	      	
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id');
	      	$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');
	      	
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	      	$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
	      	$builder->where($voucher_tbl.'.voucher_type_id !=',8);
			$builder->where($voucher_type_tbl.'.voucher_type_id', 11);
			$builder->whereNotIn('voucher_tag', ['OPTIONL','RJVHTXP']);
			
			if($from_date!='')
	      		$builder->where('voucher_date >=', $from_date);
	      	if($to_date!='')
	      		$builder->where('voucher_date <=', $to_date);
			
			if($filter_data['main_cond']=='and'){
			   
			   if($voucher_series!='' || $billno!='' ){
				   $builder->GroupStart();
				 if( $filter_data['series_cond']=='and'){
					 $builder->GroupStart();
				   $builder->where($voucher_tbl.'.comp_vch_series_id',$voucher_series);	
				   $builder->where($gstroutsup_tbl.'.inwsup_bill_ref_no',$billno);
					$builder->groupEnd();	
				}
			     if( $filter_data['series_cond']=='or'){
					 $builder->orGroupStart();
				    $builder->orLike($voucher_tbl.'.comp_vch_series_id',$voucher_series);	
					$builder->orLike($gstroutsup_tbl.'.inwsup_bill_ref_no',$billno);
					$builder->groupEnd();
				  }	  
				  $builder->groupEnd();   
			   }	
			}
		   if($filter_data['main_cond']=='or'){
			   
			   if($voucher_series!='' || $billno!='' ){
				   $builder->orGroupStart();
				 if( $filter_data['series_cond']=='and'){
					 $builder->GroupStart();
				   $builder->where($voucher_tbl.'.comp_vch_series_id',$voucher_series);	
				   $builder->where($gstroutsup_tbl.'.inwsup_bill_ref_no',$billno);
					$builder->groupEnd();	
				}
			     if( $filter_data['series_cond']=='or'){
					 $builder->orGroupStart();
				    $builder->orLike($voucher_tbl.'.comp_vch_series_id',$voucher_series);	
					$builder->orLike($gstroutsup_tbl.'.inwsup_bill_ref_no',$billno);
					$builder->groupEnd();
				  }	  
				  $builder->groupEnd();   
			   }	
			}
			
	      	$builder->orderBy('voucher_txn_id');
	      	$total_Records = $builder->countAllResults();
			

	      	if($pq_curPage==0){ $pq_curPage=1;}
	      	$offset = ($limit * ($pq_curPage - 1));

	      	if ($offset > $total_Records)
	      	{        
	      		$pq_curPage = ceil($total_Records / $limit);
	      		$offset = ($limit * ($pq_curPage - 1));
	      	}


	      	$builder = $this->db->table($voucher_tbl);
	      	$builder->select($voucher_tbl.'.*');
	      	$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	      	$builder->select($voucher_type_tbl.'.comp_vch_type');
	      	$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	      	$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id');
	      	$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');
	      	
	      	$builder->where($voucher_tbl.'.comp_id', $this->company_id);	
	      	$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where($voucher_type_tbl.'.voucher_type_id', 11);

	      	if($from_date!='')
	      		$builder->where('voucher_date >=', $from_date);
	      	if($to_date!='')
	      		$builder->where('voucher_date <=', $to_date);
			if($filter_data['main_cond']=='and'){
			   
			   if($voucher_series!='' || $billno!='' ){
				   $builder->GroupStart();
				 if( $filter_data['series_cond']=='and'){
					 $builder->GroupStart();
				   $builder->where($voucher_tbl.'.comp_vch_series_id',$voucher_series);	
				   $builder->where($gstroutsup_tbl.'.inwsup_bill_ref_no',$billno);
					$builder->groupEnd();	
				}
			     if( $filter_data['series_cond']=='or'){
					 $builder->orGroupStart();
				    $builder->orLike($voucher_tbl.'.comp_vch_series_id',$voucher_series);	
					$builder->orLike($gstroutsup_tbl.'.inwsup_bill_ref_no',$billno);
					$builder->groupEnd();
				  }	  
				  $builder->groupEnd();   
			   }	
			}
		   if($filter_data['main_cond']=='or'){
			   
			   if($voucher_series!='' || $billno!='' ){
				   $builder->orGroupStart();
				 if( $filter_data['series_cond']=='and'){
					 $builder->GroupStart();
				   $builder->where($voucher_tbl.'.comp_vch_series_id',$voucher_series);	
				   $builder->where($gstroutsup_tbl.'.inwsup_bill_ref_no',$billno);
					$builder->groupEnd();	
				}
			     if( $filter_data['series_cond']=='or'){
					 $builder->orGroupStart();
				    $builder->orLike($voucher_tbl.'.comp_vch_series_id',$voucher_series);	
					$builder->orLike($gstroutsup_tbl.'.inwsup_bill_ref_no',$billno);
					$builder->groupEnd();
				  }	  
				  $builder->groupEnd();   
			   }	
			}
	      	$builder->orderBy('voucher_date');
	      	$builder->orderBy('voucher_txn_id');
	      	$builder->limit($limit,$offset);  
	      	$result = $builder->get()->getResultArray();
			
	      	$data = [];
			foreach($result as $key => $value)
	      	{
	      		$voucher_txn_id  = $value['voucher_txn_id'];
	      		$voucher_type_id = $value['voucher_type_id'];
	      		$particulars = '';
	      		$credit = '';
	      		$debit = '';
	      		$credit_total = 0;
	      		$debit_total = 0;
	      		$bom_id = 0;
	      		$bom_batches = 0;
				
				try{
	        		$builder = $this->db->table($comp_txn_tbl);
	        		$builder->orderBy('txn_id');
	        		$builder->where('voucher_txn_id', $voucher_txn_id);
	        		$builder->where('master_id_type', 'acc');
	        		$builder->limit(1);
	        		$txn_result = $builder->get()->getRowArray();
	        		
	        			$account_info = $this->account_info($txn_result['master_id']);
	        			$particulars  = $account_info['acc_name'];
	        			$table = $this->company_id.'_accnttxnnn_'.$txn_result['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	        			$builder = $this->db->table($table);
	        			$builder->where('txn_id', $txn_result['txn_id']);
	        			$builder->where('bo_id', $this->bo_id);
	        			$account = $builder->get()->getRowArray();
	        			if($account){
	        				if($account['acc_txn_drcr'] == 'c'){
	        					$credit = formatAmount($account['acc_txn_amount']);
	        					$credit_total = parseAmount($account['acc_txn_amount']);
	        				}
	        				if($account['acc_txn_drcr'] == 'd'){
	        					$debit = formatAmount($account['acc_txn_amount']);
	        					$debit_total = parseAmount($account['acc_txn_amount']);
	        				}
	        			}

				
	        	
			    $date         = date("d-m-Y", strtotime($value['voucher_date']));
	        	$data[] = [
	        		'particulars'     => $particulars,
					'txn_id'          => $txn_result['txn_id'], 
					'master_id'       => $txn_result['master_id'], 
	        		'date'            => $date,
	        		'credit'          => $credit,
	        		'debit'           => $debit,
	        		'credit_total'    => $credit_total,
	        		'debit_total'     => $debit_total,
	        		'voucher_no'      => $value['comp_vch_no'],
	        		'voucher_type'    => $value['comp_vch_type'],
					'voucher_sub_type'=> $value['vch_subtype_id'],
	        		'voucher_type_id' => $value['voucher_type_id'],
	        		'voucher_txn_id'  => $value['voucher_txn_id'],
	        		'bom_id'          => $bom_id,
					'from_date'       => $from_date,
					'to_date'         => $to_date,
	        		'bom_batches'     => $bom_batches
			];	
				}
				 catch (\Exception $e) {
					 $error= $particulars.'->txnid: '.$value['voucher_txn_id'].'-->Message: ' .$e->getMessage().' at line@'.$e->getLine();
					 SaveErrorLog($error);
					
				}
			}
			
     $totalPages = ($total_Records/$limit);
     $totalPages = ceil($totalPages);
	 $total_index = ceil($total_Records/$limit);
     echo  "{\"offset\":" .$offset . ",\"total_index\":" .$total_index . ",\"limit\":" .$limit . ",\"renderRecods\":" .count($data) . ",\"totalPages\":" .$totalPages . ",\"totalRecords\":" .$total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($data)."}"; 
   }
   public function acctgstsum_invoice_info($voucher_txn_id){
	   $acctgstsum_table    = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id'); 
	   $builders = $this->db->table($acctgstsum_table);
	   $builders->select('vch_txn_id,taxable_amt,total_tax');
	   $builders->where('vch_txn_id',$voucher_txn_id);
	   $response = $builders->get()->getResultArray();
	   $total_invoice_value=0;
	   if($response){
		   foreach($response as $row){
			  $total_invoice_value+= parseAmount($row['taxable_amt']+$row['total_tax']);
		   }
	   }	   
	  return $total_invoice_value; 
   }
   
   function account_adrs_info($account_id){	 
       $acctaddmst_tbl = $this->company_id.'_acctaddmst_'.$this->session->get('ses_comp_fy_id');
       return $this->db->table($acctaddmst_tbl)->where('acc_id', $account_id)->get()->getRowArray();   	   
   }
   
 public function reedit_sale($row){
	/*  echo '<pre>';
	 print_r($row);
	die(); */
	$comp_id             = $this->company_id;
    $comp_fy_id          = $this->session->get('ses_comp_fy_id');	
	$acctgstsum_table    = $comp_id.'_acctgstsum_'.$comp_fy_id;
    $gstrinwsup_tbl      = $comp_id.'_gstrinwsup_'.$comp_fy_id;	
	$gstroutsup_tbl      = $comp_id.'_gstroutsup_'.$comp_fy_id;
	$acctgstmst_tbl      = $comp_id.'_acctgstmst_'.$comp_fy_id;	
	$comp_txn_master_tbl = $comp_id.'_comptxnmst_'.$comp_fy_id;
	$itemtaxmst_tbl      = $comp_id.'_itemtaxmst_'.$comp_fy_id;
	$acctaxmstn_tbl      = $comp_id.'_acctaxmstn_'.$comp_fy_id;
	$cmpgstcatn_tbl      = $comp_id.'_cmpgstcatn_'.$comp_fy_id;	 
	$vhtxnconso_tbl      = $comp_id.'_vhtxnconso_'.$comp_fy_id;
	$ewbmstreqn_tbl      = $comp_id.'_ewbmstreqn_'.$comp_fy_id;
	
	$voucher_date     = $row['voucher_date'];
	$item_id          = $row['master_id'];
	$voucher_txn_id   = $row['voucher_txn_id'];
	$txn_id           = $row['txn_id'];		
	$voucher_sub_type = $row['voucher_sub_type'];		
	$party_info        = $this->db->table($comp_txn_master_tbl)->where('voucher_txn_id',$voucher_txn_id)->where('master_id_type','acc')->orderBy('txn_id')->limit(1)->get()->getRowArray();			
    
	if($voucher_sub_type>0){
	$tax_data        = $this->db->table($itemtaxmst_tbl)
	   					->where('comp_id',$comp_id)
						->where('item_id',$item_id)
						->orderBy('cmp_tax_cat_id','DESC')						
	   					->get()->getRowArray();
	}
	else{
	$tax_data        = $this->db->table($acctaxmstn_tbl)
	   					->where('comp_id',$comp_id)
						->where('acc_id',$item_id)
						->orderBy('cmp_tax_cat_id','DESC')						
	   					->get()->getRowArray();	
	}
							 			 
	if($tax_data){			
		$tax_rates =  $this->db->table($cmpgstcatn_tbl)->where('cmp_tax_cat_id',$tax_data['cmp_tax_cat_id'])->get()->getRowArray();
	    if($tax_rates){
		$cmp_tax_short_code = $tax_rates['cmp_tax_short_code'];				     
		$pddata  = array("cmp_tax_short_code"=>$cmp_tax_short_code,'acc_txn_date'=>date('Y-m-d',strtotime($voucher_date)));
		//echo  "update `".$acctgstsum_table."` set `cmp_tax_short_code`='".$cmp_tax_short_code."',`acc_txn_date`='".$voucher_date."' where `txn_id`='".$txn_id."'  ";
		//echo '<br>';
		$this->db->table($acctgstsum_table)->where('vch_txn_id',$voucher_txn_id)->update($pddata);
		$sve = $this->db->getlastquery();
		SaveErrorLog($sve);
	   }		     
	}
	$invoice_value     = $this->acctgstsum_invoice_info($voucher_txn_id);
    $from_date         = date("Y-m-d",strtotime($row['from_date']));	
    $to_date           = date("Y-m-d",strtotime($row['to_date']));  				
    
	
	
	$ewbmstreqn_row    = $this->db->table($ewbmstreqn_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray(); 
    $voucher_type_row  = $this->db->table($vhtxnconso_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray(); 
    if($voucher_type_row){				
 	  $voucher_type      = $voucher_type_row['voucher_type_id'];
	  $voucher_date      = $voucher_type_row['voucher_date'];			  
    if($ewbmstreqn_row)
 	  $supply_type       = $ewbmstreqn_row['ewb_supply_type'];					
      else
    $supply_type       ="1";	  
    $party_gstin_data  = $this->db->table($acctgstmst_tbl)->where('acc_id',$party_info['master_id'])->get()->getRowArray(); 
    $party_adrsinfo_data  = $this->account_adrs_info($party_info['master_id']); 
    if($party_adrsinfo_data){
		   $acc_country   =  $party_adrsinfo_data['acc_country'];
		   $acc_state     =  $party_adrsinfo_data['acc_state'];
		   $state_info    =  $this->CommonModel->get_state_info($acc_country,$acc_state);
		   if($state_info)
			$state_code   = $state_info['state_code'];
		   else
			$state_code   = '4';
		   
		 }
	      else{
			$state_code    = "4";
			$acc_country   = "1";
			$acc_state     = "31";
		  }
   
      $pos_state_code = sprintf( '%02d', $state_code);
   // echo $party_info['master_id'].'<pre>';
    //print_r($party_gstin_data);
	  
    if($party_gstin_data){
     $party_gstin      = trim($party_gstin_data['acc_gstin']);
     }
	 else 
	 $party_gstin      = "";
	 // echo '--'.$party_gstin;
	// echo '<br>';
	$gstroutsup_info     = $this->db->table($gstroutsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray();
			
	 if($voucher_type=='18'){ // sale & debit
		if(isset($gstroutsup_info) && $party_gstin!='' && $supply_type=='1' && $gstroutsup_info['outsup_rev_chg']=="0"){
			 $invoice_type = 'B2B';				
		   }
	  else if(isset($gstroutsup_info) && $party_gstin!='' && $gstroutsup_info['outsup_rev_chg']=="1" && $supply_type=='1'){  
		  	 $invoice_type = 'B2BRCM';							  
	   }			
	 else  if(isset($gstroutsup_info) && $party_gstin=='' && $gstroutsup_info['outsup_rev_chg']=="0"){
	  //check  voucher total 
	 $invoice_type = get_b2c_invoice_type($voucher_date,$invoice_value);
	 //$invoice_type = 'B2C';				 
	}
	else if($supply_type=='2'){
		$invoice_type = 'EXPWP'; 
	 }			 
	elseif($supply_type=='3'){
		$invoice_type = 'EXPWOP'; 
	 }
	 elseif($supply_type=='13'){
		$invoice_type = 'DE'; 
	 }	
	 elseif($supply_type=='4'){
		$invoice_type = 'SEZWP'; 
	 }
	 elseif($supply_type=='5'){
		$invoice_type = 'SEZWOP'; 
	 }
	 
	 elseif($supply_type=='14'){
		$invoice_type = 'NILSPLY'; 
	 }
	 elseif($supply_type=='15'){
		$invoice_type = 'EXMSPLY'; 
	 }elseif($supply_type=='16'){
		$invoice_type = 'NONGSTS'; 
	 }
	 
	 
	 else
	$invoice_type = 'CBW';  
            
	// echo  "update `".$gstroutsup_tbl."` set `outsup_inv_type`='".$invoice_type."' where `voucher_txn_id`='".$voucher_txn_id."' ";
	// echo '<br>';
	if($voucher_type=='3') 
	 $psddata=array("outsup_pos"=>$pos_state_code,"outsup_inv_type"=>$invoice_type,'outsup_dr_note'=>'1');
	   else
	$psddata=array("outsup_pos"=>$pos_state_code,"outsup_inv_type"=>$invoice_type,'outsup_dr_note'=>'0');	
		 
	$this->db->table($gstroutsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->update($psddata);
					 	
	}
	else if($voucher_type=='11' || $voucher_type=='2'){ // purchase & credit
		$gstrinwsup_info     = $external_db->table($gstrinwsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray();
	  if(isset($gstrinwsup_info) && $party_gstin!='' && $supply_type=='1' && $gstrinwsup_info['inwsup_rev_chg']=="0"){
		 $invoice_type = 'B2B';				
	   }
  	 else if(isset($gstrinwsup_info) && $supply_type=='1' && $party_gstin!=''&& $gstrinwsup_info['inwsup_rev_chg']=="1"){  
	 	 $invoice_type = 'B2BRCM';							  
	  }					
	 else if(isset($gstrinwsup_info) && $party_gstin=='' && $supply_type=='1' && $gstrinwsup_info['inwsup_rev_chg']=="0"){
		 $invoice_type = 'B2C';				 
	  }
	 else if($supply_type=='2'){
		$invoice_type = 'EXPWP'; 
	 }			 
	 elseif($supply_type=='3'){
		$invoice_type = 'EXPWOP'; 
	 }
	 elseif($supply_type=='13'){
		$invoice_type = 'DE'; 
	 }	
	 elseif($supply_type=='4'){
		$invoice_type = 'SEZWP'; 
	 }
	 elseif($supply_type=='5'){
		$invoice_type = 'SEZWOP'; 
	 }else
		$invoice_type = 'CBW';  
          
	// echo  "update `".$gstrinwsup_tbl."` set `inwsup_inv_type`='".$invoice_type."' where `voucher_txn_id`='".$voucher_txn_id."' ";
	// echo '<br>';
	 if($voucher_type=='2')
	   $pwddata = array("inwsup_inv_type"=>$invoice_type,'inwsup_cr_note'=>'1');
	    else
	  $pwddata = array("inwsup_inv_type"=>$invoice_type,'inwsup_cr_note'=>'0');	 
	  //$this->db->table($gstrinwsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->update($pwddata);
	   }
     } 	
	return true; 
 }
 public function billsundry_balance($billsundry_id)
    {
    	$bsdoppybal_tbl = $this->company_id.'_bsdoppybal_'.$this->session->get('ses_comp_fy_id');
    	return $this->db->table($bsdoppybal_tbl)->where('bill_sundry_id', $billsundry_id)->get()->getRowArray();
    }
	
 public function get_account_group_name($id){
         $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
      
         $data = $this->db->table($account_grp_tbl)->select('acc_grp_name')->where('acc_grp_id', $id)->get()->getRowArray();
         return $data['acc_grp_name'];
     }
 
 public function get_parent_group_name($id){
         $account_grp_tbl = $this->company_id.'_grpparentn_'.$this->session->get('ses_comp_fy_id');
         $data = $this->db->table($account_grp_tbl)->select('acc_grp_parent')->where('acc_grp_parent_id', $id)->get()->getRowArray();
          if($data)      
	     	return $data['acc_grp_parent'];
	      else
			return '';   
     }  
	 

 
function get_branch_groups_list()
    {
      
		$p_groups = [14];
		$tbl_name = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
		$data =  $this->db->table($tbl_name)
	   					 ->select('acc_grp_id')
	   					 ->whereIn('acc_grp_parent_id', $p_groups)
	   					 ->get()->getResultArray();
        $final = [];
        if($data){
        	$final = array_column($data, 'acc_grp_id');
        }
        
        return $final;
    }

function group_parent_info($acc_grp_id){	 
      $account_grp_tbl = $this->company_id.'_grpparentn_'.$this->session->get('ses_comp_fy_id'); 
  	  return $this->db->table($account_grp_tbl)->where('acc_grp_parent_id', $acc_grp_id)->get()->getRowArray();   	   
   }
   
   public function acc_opn_balance_info($acc_id){
       $accoppybal_tbl =$this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	   if($this->session->get('ses_boid')!='')
	return $this->db->table($accoppybal_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('acc_id', $acc_id)->get()->getRowArray(); 
	   else
       return $this->db->table($accoppybal_tbl)->where('acc_id', $acc_id)->get()->getRowArray();  
      } 
  function main_group_info($acc_grp_id){	 
      $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id'); 
  	  return $this->db->table($account_grp_tbl)->where('acc_grp_id', $acc_grp_id)->get()->getRowArray();   	   
   }	  
   
  function account_taxinfo($accid){
	   $acctaxmstn_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id'); 
  	  return $this->db->table($acctaxmstn_tbl)->where('acc_id', $accid)->get()->getRowArray();   	   
   
  }
  
  function bsd_taxinfo($bill_sundry_id){
	   $bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id'); 
  	  return $this->db->table($bdstaxmstn_tbl)->where('bill_sundry_id', $bill_sundry_id)->get()->getRowArray();   	   
   
  }
  


   public function ajax_items(){
         $limit  = 100;
    	 $search = '';
		
		if(isset($_POST["pq_filter"])){
	       $pq_filter     = $_POST["pq_filter"];
	       
	       $filter_data  = json_decode($_POST["pq_filter"],true);
	      
	    
	      $pq_filters   = $filter_data['data'][0];
		  
		  $search = $pq_filter->data[0]->value;
	    
	       if(isset($pq_filters['condition']))
	      $condition   = $pq_filters['condition'];
	   else
		  $condition='';
	      $search_text = strtolower($pq_filters['value']);
	      $dataIndx    = $pq_filters['dataIndx'];  
	       
	    }
	    else{
	      $pq_filter     ='';
	      $search_text   ='';
	      $dataIndx      ='';
	      $condition='';
	    }
	   $pq_curPage=1;
	   if(isset($_POST["pq_curpage"])  )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
               
            } 
        
	  $offset = ($limit * ($pq_curPage - 1));
      

	    $comp_id = $this->session->get('ses_company_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id'); 
	    $builder         = $this->db->table($item_master_tbl);
	    if($search != ''){
	    	$builder->like('item_name', $search);
	    } 
        $totalrecords = $builder->countAll();
		if ($offset > $totalrecords)
            {        
                $pq_curPage = ceil($totalrecords / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
        

      if($search != ''){
	    	$builder->like('item_name', $search);
	    } 
         
         $builder->orderBy('item_name');                
	 	 $builder->where('comp_id', $comp_id);
	 	  $builder->limit($limit,$offset); 
		 $result = $builder->get()->getResultArray();
         
		 
         $records=array(); 		
          foreach($result as $values){
             $item_unit_info  = $this->get_units_info($values['item_unit'],$values['comp_id']);
			$item_group_info = $this->item_group_info($values['item_grp_id'],$values['comp_id']);			
			if(isset($item_group_info['item_grp_id']))
				$item_grp = $item_group_info['item_grp_name'];
			else 
				$item_grp = '';

			 if(isset($item_unit_info['item_unit']))
				$item_unit = $item_unit_info['item_unit'];
			else 
				$item_unit = ''; 
			
			$item_catg_info  = $this->item_category_info($values['item_cat'],$values['comp_id']);	
            if($item_catg_info){
				$item_cat = $item_catg_info['item_cat'];
			}			
			else
			$item_cat = '';
			if($values['tax_id']>0)
		    $tax_category_name  = $this->get_item_taxinfo($values['tax_id']);
		    else
            $tax_category_name  = $this->get_item_local_taxinfo($values['item_id']);
		     				
			
			//hsn and supply type 
			$itemtaxmst_tbl = $this->company_id.'_itemtaxmst_'.$this->session->get('ses_comp_fy_id');
	        $tax_infodata       = $this->db->table($itemtaxmst_tbl)
	   					->where('comp_id',$this->company_id)
						->where('item_id',$values['item_id'])
						->orderBy('cmp_tax_cat_id','DESC')						
	   					->get()->getRowArray();
			if($tax_infodata){			
			$item_supply_type = $tax_infodata['item_supply_type'];
			$item_hsn_sac = $tax_infodata['item_hsn_sac'];
			}else{
			$item_supply_type = '';
			$item_hsn_sac = '';	
			}
			
			if($item_supply_type=="1")
				$item_supply_name = "Goods";
			else if($item_supply_type=="2")
				$item_supply_name = "Services";
			else if($item_supply_type=="3")
				$item_supply_name = "Capital Goods";
			 else
				 $item_supply_name = "Goods";
			
			$sale_account_info = $this->account_info($values['item_sales_acc']);
			$purchase_account_info = $this->account_info($values['item_pur_acc']);
			$sale_account_name ='N/A';
			$purchase_account_name ='N/A';
			if($sale_account_info)
				$sale_account_name = $sale_account_info['acc_name'];
			if($purchase_account_info)
				$purchase_account_name = $purchase_account_info['acc_name'];
			
			
			$valuation_id =$values['valmethod_id'];
			if($valuation_id==1)
		    	$item_valuation_method = 'AVG';
			else if($valuation_id==2)
				$item_valuation_method = 'FIFO';
			else if($valuation_id==3)
				$item_valuation_method = 'LIFO';
			else 
                $item_valuation_method = 'AVG';				
			
			$records[]       = array(	
			                      'item_upc'     => $values['item_upc'],
    							  'item_name'   => ucwords($values['item_name']),
    							  'item_alias'  => $values['item_alias'],
								  'item_printnme' => $values['item_print'],
    							  'item_grp'    => $item_grp,
    							  'item_unit'   => $item_unit,
    							  'item_cat'    => $item_cat,
    							  'item_sku'    => $values['item_sku'],
								  'item_id'     => $values['item_id'],
								  'item_tax_catg'=> $tax_category_name,
								  'item_aliasname'   => $values['item_alias'],
								  'item_sale_account'   => $sale_account_name,
								  'item_purchase_account'   => $purchase_account_name,
								  'id'           => $values['tax_id'],
								  'sales_acc_id'    =>$values['item_sales_acc'],
								  'purchase_acc_id' =>$values['item_pur_acc'],								  
								  'tax_cat_id'  =>  $values['tax_id'],
								  'supplytype'   => $item_supply_name,
								  'supplyid'     => $item_supply_type,
								  'supplytype_id'     => $item_supply_type,
								  'item_valuation_method'=>$item_valuation_method,
								  'valuation_id'=>$valuation_id,
								  
 				                 );  
		           }
     $totalPages = ($totalrecords/$limit);
     $totalPages = ceil($totalPages);
    echo  "{\"offset\":" .$offset . ",\"limit\":" .$limit . ",\"renderRecods\":" .count($records) . ",\"totalPages\":" .$totalPages . ",\"totalRecords\":" .$totalrecords . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
   
   }
   
    function ajax_bsd_list(){
		$limit=100;
	    $branch_groups      = $this->get_branch_groups_list();	
        $billsundry_nature  =  billsundry_nature();		
		 if(isset($_POST["pq_filter"])){
	       $pq_filter     = $_POST["pq_filter"];
	       
	       $filter_data  = json_decode($_POST["pq_filter"],true);
	      
	    
	      $pq_filters   = $filter_data['data'][0];
	    
	       if(isset($pq_filters['condition']))
	      $condition   = $pq_filters['condition'];
	   else
		  $condition='';
	      $search_text = strtolower($pq_filters['value']);
	      $dataIndx    = $pq_filters['dataIndx'];  
	       
	    }
	    else{
	      $pq_filter     ='';
	      $search_text   ='';
	      $dataIndx      ='';
	      $condition='';
	    }
	   $pq_curPage=1;        

		if(isset($_POST["pq_curpage"])  )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
               
            } 
        
	  $offset = ($limit * ($pq_curPage - 1));
            
	    $comp_id         = $this->session->get('ses_company_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $item_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id'); 
	    $builder         = $this->db->table($item_master_tbl); 
	    $builder->where('acc_grp_parent_id !=',14); // BO ACCOUNTS RESTRICTION TO NOT TO SHOW IN ACCOUNT GROUP MASTER INTERFACE / LIST.
	     
	     if($branch_groups)
	     	$builder->whereNotIn('acc_grp_id', $branch_groups);
           $totalrecords = $builder->countAllResults();
      if ($offset > $totalrecords)
            {        
                $pq_curPage = ceil($totalrecords / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
        
        $builder->where('acc_grp_parent_id !=',14); // BO ACCOUNTS RESTRICTION TO NOT TO SHOW IN ACCOUNT GROUP MASTER INTERFACE / LIST.
	     
	     if($branch_groups)
	     	$builder->whereNotIn('acc_grp_id', $branch_groups);
        
         $builder->orderBy('bill_sundry_name');                
	 	  $builder->limit($limit,$offset);
		 $result = $builder->get()->getResultArray();
		
         $records=array(); 		
          foreach($result as $values){
             
			  if($values['sundry_type']=='1'){
				  $sundry_type ='Taxable Account'; 
			   }
			   else{
				  $sundry_type ='Non Taxable Account';   
			   }
			  
			  if(isset($billsundry_nature[$values['sundry_nature']]) && $values['sundry_nature']!='')
			   $billsundry_nature_val = $billsundry_nature[$values['sundry_nature']];
			   else
			   $billsundry_nature_val = '';
                
                if($values['acc_grp_id'] != 0) 
					$billsundry_group_name = $this->get_account_group_name($values['acc_grp_id']);
				else
					$billsundry_group_name = $this->get_parent_group_name($values['acc_grp_parent_id']).' (Parent)';
							
				$billsundry_balance = $this->billsundry_balance($values['bill_sundry_id']);

				$bsd_op_bal = '0.00';
				$bsd_op_bal_drcr = 'DR';

				if($billsundry_balance){
				  	if($billsundry_balance['bsd_op_bal'] < 0){
				  		 $bsd_op_bal = abs($billsundry_balance['bsd_op_bal']);
				  		 $bsd_op_bal_drcr = 'CR'; 
				  	}
				  	else{
				  			$bsd_op_bal = $billsundry_balance['bsd_op_bal'];
				  		 	$bsd_op_bal_drcr = 'DR';
				  	}
				  }
				$taxinfo = $this->bsd_taxinfo($values['bill_sundry_id']);
			  if($taxinfo){
				 $tax_id =$taxinfo['tax_cat_id'];
				  $tax_category_name  = $this->get_item_taxinfo($tax_id);
			  }else{
				$tax_id=0;  
				$tax_category_name='';
			  }
			  
				$records[] = array(	
			                      'bill_sundry_id'     => $values['bill_sundry_id'],
    							  'billsndry_name'   => ucwords($values['bill_sundry_name']),
    							  'billsndry_type'  => $sundry_type,
    							  'billsndry_nature'    => $billsundry_nature_val,
								  'billsundry_group_name' => $billsundry_group_name,
								  'bsd_op_bal' => floatval($bsd_op_bal),
								  'bsd_op_bal_drcr' => $bsd_op_bal_drcr,
								  'sundry_nature'   => $values['sundry_nature'],
								  'account_type'    => $values['sundry_type'],
								  'item_tax_catg'   => $tax_category_name,
								  'id'              => $tax_id,
								  'tax_cat_id'      => $tax_id,
								  
				                 );  		           
		   }
       $totalPages = ($totalrecords/$limit);
     $totalPages = ceil($totalPages);
    echo  "{\"offset\":" .$offset . ",\"limit\":" .$limit . ",\"renderRecods\":" .count($records) . ",\"totalPages\":" .$totalPages . ",\"totalRecords\":" .$totalrecords . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
   
   }

  public function ajax_accounts_list(){
        $limit         = 100;
   		$branch_groups = $this->get_branch_groups_list();
		$all_branchs   = $this->all_branchs();
	    $dealer_types  = DealerTypes();
	    $comp_id       = $this->session->get('ses_company_id');
	    $base_url      = base_url().'/'.getenv('AdminPath');
	    
	    if(isset($_POST["pq_filter"])){
	       $pq_filter     = $_POST["pq_filter"];
	       
	       $filter_data  = json_decode($_POST["pq_filter"],true);
	      
	    
	      $pq_filters   = $filter_data['data'][0];
	    
	       if(isset($pq_filters['condition']))
	      $condition   = $pq_filters['condition'];
	   else
		  $condition='';
	      $search_text = strtolower($pq_filters['value']);
	      $dataIndx    = $pq_filters['dataIndx'];  
	       
	    }
	    else{
	      $pq_filter     ='';
	      $search_text   ='';
	      $dataIndx      ='';
	      $condition='';
	    }
	   $pq_curPage=1;
	    
	   
	    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id'); 
	    $account_groupn_tabl     = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
	    $account_opbalance_tabl     = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	    $accoppybal_tabl     = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	    
	    
	    if(isset($_POST["pq_curpage"])  )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
               
            } 
        
	  $offset = ($limit * ($pq_curPage - 1));

     
			
                      
       /*   if($dataIndx=='account_name'){
	    
    	    if($condition=='begin'){
    	         $builder->Like('LOWER(acc_name)',$search_text,'after');
    	         $builder->orLike('LOWER(acc_name_alias)',$search_text,'after');
    	         $builder->orLike('LOWER(acc_name_print)',$search_text,'after');
    	    }
    	   else if( $condition=='contain'){
    	        $builder->Like('LOWER(acc_name)',$search_text,'both');
    	        $builder->orLike('LOWER(acc_name_alias)',$search_text,'both');
    	        $builder->orLike('LOWER(acc_name_print)',$search_text,'both');
    	    } 
    	   else if( $condition=='notcontain'){
    	        $builder->notLike('LOWER(acc_name)',$search_text,'both');
    	        $builder->orNotLike('LOWER(acc_name_alias)',$search_text,'both');
    	        $builder->orNotLike('LOWER(acc_name_print)',$search_text,'both');
    	    }   
    	   else if($condition=='end'){
    	        $builder->Like('LOWER(acc_name)',$search_text,'before');
    	        $builder->orLike('LOWER(acc_name_alias)',$search_text,'before');
    	        $builder->orLike('LOWER(acc_name_print)',$search_text,'before');
    	    } 
    	   else if($condition=='equal'){
    	        $builder->where('( LOWER(acc_name)="'.$search_text.'" OR LOWER(acc_name_alias)="'.$search_text.'" OR LOWER(acc_name_print)="'.$search_text.'")'   );
    	     }
    	    else if($condition=='notequal'){
    	        $builder->where('( LOWER(acc_name)="'.$search_text.'" OR LOWER(acc_name_alias)="'.$search_text.'" OR LOWER(acc_name_print)="'.$search_text.'")'   );
    	     }   
	     
	     }

	     else if($dataIndx=='acc_id'){
	    
    	    if($condition=='begin'){
    	         $builder->Like($account_master_tbl.'.acc_id',$search_text,'after');
    	    }
    	   else if( $condition=='contain'){
    	        $builder->Like($account_master_tbl.'.acc_id',$search_text,'both');
    	    } 
    	   else if( $condition=='notcontain'){
    	        $builder->notLike($account_master_tbl.'.acc_id',$search_text,'both');
    	    }   
    	   else if($condition=='end'){
    	        $builder->Like($account_master_tbl.'.acc_id',$search_text,'before');
    	    } 
    	   else if($condition=='equal'){
    	        $builder->where($account_master_tbl.'.acc_id', $search_text);
    	     }
    	    else if($condition=='notequal'){
    	        $builder->where($account_master_tbl.'.acc_id !=', $search_text);
    	     }   
	     
	     }
	     else if($dataIndx=='group_name'){
		     if($condition=='begin'){
	    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) LIKE "'.$search_text.'%" OR LOWER(acc_grp_alias) LIKE "'.$search_text.'%" ))  ');
	    	         
	    	    }
	    	 if($condition=='contain'){
	    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) LIKE "%'.$search_text.'%" OR LOWER(acc_grp_alias) LIKE "%'.$search_text.'%" ))  ');
	    	         
	    	    }    
		   	if($condition=='notcontain'){
	    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) NOT LIKE "%'.$search_text.'%" OR LOWER(acc_grp_alias) NOT LIKE "%'.$search_text.'%" ))  ');
	    	         
	    	    } 
	      	if($condition=='end'){
	    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) LIKE "%'.$search_text.'" OR LOWER(acc_grp_alias) LIKE "%'.$search_text.'" ))  ');
	    	         
	    	    }
	      	if($condition=='equal'){
	    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) = "'.$search_text.'" OR LOWER(acc_grp_alias) = "'.$search_text.'" ))  ');
	    	         
	    	}
	    	    
	       	if($condition=='notequal'){
	    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) != "'.$search_text.'" OR LOWER(acc_grp_alias) != "'.$search_text.'" ))  ');
	    	         
	    	}
	    
		} 
	    else if($dataIndx=='op_bal'){
	        if($condition=='equal'){
				if($this->session->get('ses_boid')!=''){
				$builder->where($account_opbalance_tabl.'.bo_id', $this->session->get('ses_boid'));
			    $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
				}
				else
    	        $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
    	         
    	    }
          elseif($condition=='notequal'){
			  if($this->session->get('ses_boid')!=''){
				$builder->where($account_opbalance_tabl.'.bo_id', $this->session->get('ses_boid'));
			    $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
				}
				else
    	         $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal) !=',(float)$search_text);
    	         
    	    }
    	  elseif($condition=='less'){
			  if($this->session->get('ses_boid')!=''){
				$builder->where($account_opbalance_tabl.'.bo_id', $this->session->get('ses_boid'));
			    $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
				}
				else
    	         $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal) <',(float)$search_text);
    	         
    	    }  
    	  elseif($condition=='great'){
			  if($this->session->get('ses_boid')!=''){
				$builder->where($account_opbalance_tabl.'.bo_id', $this->session->get('ses_boid'));
			    $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
				}
				else
    	         $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal) >',(float)$search_text);
    	         
    	    }  
	       
	    }
	   
	    else if($dataIndx=='bal_type'){
	        if($condition=='equal'){
				if($this->session->get('ses_boid')!=''){
				$builder->where($account_opbalance_tabl.'.bo_id', $this->session->get('ses_boid'));
			    $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
				}
				else
    	        $builder->where($account_opbalance_tabl.'.acc_op_bal <',0);
    	         
    	    }
          elseif($condition=='notequal'){
			  if($this->session->get('ses_boid')!=''){
				$builder->where($account_opbalance_tabl.'.bo_id', $this->session->get('ses_boid'));
			    $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
				}
				else
    	       $builder->where($account_opbalance_tabl.'.acc_op_bal >',0);
    	    }
    	   
	       
	    }
	     */
		 $builder            = $this->db->table($account_master_tbl);
         $builder->orderBy('acc_name'); 
		 $builder->select($account_master_tbl.'.*');
	     $builder->where('acc_grp_parent_id !=',14); // BO ACCOUNTS RESTRICTION TO NOT TO SHOW IN ACCOUNT GROUP MASTER INTERFACE / LIST.
	     
	     if($branch_groups)
	     	$builder->whereNotIn('acc_grp_id', $branch_groups);
		
		
		 $builder->where('comp_id', $comp_id);
	 	 $builder->join($account_opbalance_tabl, $account_master_tbl.'.acc_id = '.$account_opbalance_tabl.'.acc_id AND '.$account_opbalance_tabl.'.bo_id = '.$this->bo_id, 'left');
	 	 $builder->select($account_opbalance_tabl.'.acc_op_bal');
	 	 $totalrecords = $builder->countAllResults();
		  if ($offset > $totalrecords)
            {        
                $pq_curPage = ceil($totalrecords / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
			
		 
		 $builder            = $this->db->table($account_master_tbl);
         $builder->orderBy('acc_name');
		 $builder->select($account_master_tbl.'.*');
	     $builder->where('acc_grp_parent_id !=',14); // BO ACCOUNTS RESTRICTION TO NOT TO SHOW IN ACCOUNT GROUP MASTER INTERFACE / LIST.
	     
	     if($branch_groups)
	     	$builder->whereNotIn('acc_grp_id', $branch_groups);
		
		
		 $builder->where('comp_id', $comp_id);
	 	 $builder->join($account_opbalance_tabl, $account_master_tbl.'.acc_id = '.$account_opbalance_tabl.'.acc_id AND '.$account_opbalance_tabl.'.bo_id = '.$this->bo_id, 'left');
	 	 $builder->select($account_opbalance_tabl.'.acc_op_bal');
		 $builder->limit($limit,$offset); 
	 	 $result = $builder->get()->getResultArray();
		 
		
		foreach($result as $values){
           $branchs_wise_op_balance =[];
		   
			if($values['acc_grp_id'] != 0){
				$group_info   =  $this->main_group_info($values['acc_grp_id']);
			   	if(isset($group_info['acc_grp_name']))
				    $show_group = $group_info['acc_grp_name'];
				else
					$show_group = '';
			}
			else{
				$group_parent_info   =  $this->group_parent_info($values['acc_grp_parent_id']);
			   	if(isset($group_parent_info['acc_grp_parent']))
				    $show_group = $group_parent_info['acc_grp_parent']. ' <i>(PARENT)</i>';
				else
					$show_group = '';
			}
			
			$acc_grp_parent_id = $values['acc_grp_parent_id'];
		if($acc_grp_parent_id=="0"){
			$main_group_info = $this->main_group_info($values['acc_grp_id']);
			$acc_grp_parent_id = $main_group_info['acc_grp_parent_id'];
		}
		
		$account_opn_not_allowed=[6,7,11,13,8,10,9,12];
		
		if(in_array($acc_grp_parent_id,$account_opn_not_allowed))
			$cannotselected=1;
		  else
			$cannotselected=0;  
				
			
			   $get_opn_balance_info = $this->acc_opn_balance_info($values['acc_id']);
			   if(!$get_opn_balance_info){
				   $acc_op_bal = '0';
				   $acc_op_type = '';
			   }
	          	else{
			      $acc_op_bal = abs($get_opn_balance_info['acc_op_bal']);
				  
				  $acc_op_type = ($get_opn_balance_info['acc_op_bal']<0)?'CR.':'DR.';
				}
			  $taxinfo = $this->account_taxinfo($values['acc_id']);
			  if($taxinfo){
				 $tax_id =$taxinfo['tax_cat_id'];
				  $tax_category_name  = $this->get_item_taxinfo($tax_id);
			  }else{
				$tax_id=0;  
				$tax_category_name='';
			  }
				$records[] = array(	
			          'acc_id'       => $values['acc_id'],
                      'account_name' => ucwords($values['acc_name']),
					  'vendor_code' => $values['vendor_code'] ?? '',
					  'group_name'   => $show_group,                     
					  'isedited'     =>'0',
					  'acc_short_code'=> $values['acc_short_code'],
					  'item_tax_catg'=> $tax_category_name,
					  'id'           => $tax_id,
					  'tax_cat_id'  =>  $tax_id,
					  'cannotselected' => $cannotselected
 					  );
					  
			
			 			
		           }
       
	   $final_drcr='DR.';
       foreach($records as $key =>  $rows){
					$final_balance=0;			
					foreach($all_branchs as $branchid => $branchname){
					  if($branchid > 0){	
					  $balance_info = $this->db->table($accoppybal_tabl)->where('bo_id', $branchid)->where('acc_id', $rows['acc_id'])->get()->getRowArray();
		               $balance_type='DR.';
					   $op_balance=0;
					   if($balance_info){
						   $op_balance=$balance_info['acc_op_bal'];
						   $final_balance = $final_balance+$op_balance;
						   if($balance_info['acc_op_bal']<0)
							   $balance_type='CR.';
					      }else
							 $final_balance = $final_balance+0;	

						
						$records[$key]['bal_type_'.$branchid]=$balance_type;
						$records[$key]['op_bal_'.$branchid]=abs($op_balance);
                        $records[$key]['brnchbal'.$branchid]=abs($op_balance);						
					  }
				     }

					if($final_balance<0)
                      $final_drcr='CR.';
					$records[$key]['bal_type']=$final_drcr;
					$records[$key]['op_bal']=abs($final_balance);
					
			     }				   
	
	
	$totalPages = ($totalrecords/$limit);
    $totalPages = ceil($totalPages);
    echo  "{\"offset\":" .$offset . ",\"limit\":" .$limit . ",\"renderRecods\":" .count($records) . ",\"totalPages\":" .$totalPages . ",\"totalRecords\":" .$totalrecords . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
        
        
     } 	
	
 }