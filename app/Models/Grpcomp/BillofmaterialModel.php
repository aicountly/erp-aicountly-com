<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class BillofmaterialModel extends Model	{

    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    =  $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
    }
	
	function deletebom($bom_id){
		$billofmatn_tble =  $this->company_id.'_billofmatn_'.$this->session->get('ses_comp_fy_id');
		$bominputnn_tble =  $this->company_id.'_bominputnn_'.$this->session->get('ses_comp_fy_id');
		$bomoutputn_tble =  $this->company_id.'_bomoutputn_'.$this->session->get('ses_comp_fy_id');
		$bomaddcost_tble =  $this->company_id.'_bomaddcost_'.$this->session->get('ses_comp_fy_id'); 
		 
		 $this->db->table($billofmatn_tble)->where('bom_id', $bom_id)->delete();
		 $this->db->table($bominputnn_tble)->where('bom_id',$bom_id)->delete();
		 $this->db->table($bomoutputn_tble)->where('bom_id',$bom_id)->where('bom_output_type','f')->delete();
		 $this->db->table($bomoutputn_tble)->where('bom_id',$bom_id)->where('bom_output_type','p')->delete();
		 $this->db->table($bomaddcost_tble)->where('bom_id',$bom_id)->delete();
	}
	
    function update_bom_data($bom_name,$item_consumed,$item_produced,$byproducts_produced,$additional_cost,$bom_id){
		
		$billofmatn_tble =  $this->company_id.'_billofmatn_'.$this->session->get('ses_comp_fy_id');
		$bominputnn_tble =  $this->company_id.'_bominputnn_'.$this->session->get('ses_comp_fy_id');
		$bomoutputn_tble =  $this->company_id.'_bomoutputn_'.$this->session->get('ses_comp_fy_id');
		$bomaddcost_tble =  $this->company_id.'_bomaddcost_'.$this->session->get('ses_comp_fy_id');
	
		 $this->db->table($billofmatn_tble)->where('bom_id', $bom_id)->update(array("bom_name"=>$bom_name));
		 
		 $this->db->table($bominputnn_tble)->where('bom_id',$bom_id)->delete();
		 $this->db->table($bomoutputn_tble)->where('bom_id',$bom_id)->where('bom_output_type','f')->delete();
		 $this->db->table($bomoutputn_tble)->where('bom_id',$bom_id)->where('bom_output_type','p')->delete();
		 $this->db->table($bomaddcost_tble)->where('bom_id',$bom_id)->delete();
		 
        if($item_consumed){
			foreach($item_consumed as $item_row){
				if(isset($item_row['item_id'])){
				$item_info   = $this->get_item_info($item_row['item_id']);	
				$unit_id     = $item_info['item_unit'];	
				$item_data            =  array("bom_id"=>$bom_id,"item_id"=>$item_row['item_id'],"item_uom"=>$unit_id,"item_qty"=>$item_row['item_qty'],"item_amt"=>$item_row['item_total_amount']);
                $this->db->table($bominputnn_tble)->insert($item_data);	
			    }
			}
		}
		
	if($item_produced){
			foreach($item_produced as $item_prd_row){
				if(isset($item_prd_row['item_id'])){
				$item_info   = $this->get_item_info($item_prd_row['item_id']);	
				$unit_id     = $item_info['item_unit'];		
				$item_prd_data =  array("bom_id"=>$bom_id,"item_id"=>$item_prd_row['item_id'],"item_uom"=>$unit_id,"item_qty"=>$item_prd_row['item_qty'],"item_usr_amt"=>$item_prd_row['item_total_amount'],"item_amt"=>'0','bom_output_type'=>'f');
                $this->db->table($bomoutputn_tble)->insert($item_prd_data);	
			    }
			}
		}
		
	if($byproducts_produced){		
			foreach($byproducts_produced as $by_prd_row){
				if(isset($by_prd_row['item_id'])){
				$item_info   = $this->get_item_info($by_prd_row['item_id']);	
				$unit_id     = $item_info['item_unit'];		
				$item_byprd_data =  array("bom_id"=>$bom_id,"item_id"=>$by_prd_row['item_id'],"item_uom"=>$unit_id,"item_qty"=>$by_prd_row['item_qty'],"item_usr_amt"=>$by_prd_row['item_total_amount'],"item_amt"=>'0','bom_output_type'=>'p');
                $this->db->table($bomoutputn_tble)->insert($item_byprd_data);	
			    }
			}
		}
		
		
     if($additional_cost){		 
			foreach($additional_cost as $additional_cost_row){
				if(isset($additional_cost_row['expense_id'])){
				$adcost_data =  array("bom_id"=>$bom_id,"bom_addcost_acc_id"=>$additional_cost_row['expense_id'],"bom_addcost_type"=>$additional_cost_row['expense_type'],"bom_addcost_amt"=>$additional_cost_row['expense_amount']);
                $this->db->table($bomaddcost_tble)->insert($adcost_data);	
			    }
			}
		}
	return true;	
	 }
	 
	
	function save_data($bom_name,$item_consumed,$item_produced,$byproducts_produced,$additional_cost){
		
		$billofmatn_tble =  $this->company_id.'_billofmatn_'.$this->session->get('ses_comp_fy_id');
		$bominputnn_tble =  $this->company_id.'_bominputnn_'.$this->session->get('ses_comp_fy_id');
		$bomoutputn_tble =  $this->company_id.'_bomoutputn_'.$this->session->get('ses_comp_fy_id');
		$bomaddcost_tble =  $this->company_id.'_bomaddcost_'.$this->session->get('ses_comp_fy_id');
		
		
		$data            =  array("bom_name"=>$bom_name);
        $this->db->table($billofmatn_tble)->insert($data);	
		$bom_id  = $this->db->insertID();

        if($item_consumed){
			foreach($item_consumed as $item_row){
				if(isset($item_row['item_id']) && $item_row['item_id']!=''){
				$item_info   = $this->get_item_info($item_row['item_id']);	
				$unit_id     = $item_info['item_unit'];	
				$item_data            =  array("bom_id"=>$bom_id,"item_id"=>$item_row['item_id'],"item_uom"=>$unit_id,"item_qty"=>$item_row['item_qty'],"item_amt"=>$item_row['item_total_amount']);
                $this->db->table($bominputnn_tble)->insert($item_data);	
			    }
			}
		}
		
	if($item_produced){
			foreach($item_produced as $item_prd_row){
				if(isset($item_prd_row['item_id']) && $item_prd_row['item_id']!=''){
				$item_info   = $this->get_item_info($item_prd_row['item_id']);	
				$unit_id     = $item_info['item_unit'];		
				$item_prd_data =  array("bom_id"=>$bom_id,"item_id"=>$item_prd_row['item_id'],"item_uom"=>$unit_id,"item_qty"=>$item_prd_row['item_qty'],"item_usr_amt"=>$item_prd_row['item_total_amount'],"item_amt"=>0,'bom_output_type'=>'final');
                $this->db->table($bomoutputn_tble)->insert($item_prd_data);	
			    }
			}
		}
		
	if($byproducts_produced){
			foreach($byproducts_produced as $by_prd_row){
				if(isset($by_prd_row['item_id']) && $by_prd_row['item_id']!=''){
				$item_info   = $this->get_item_info($by_prd_row['item_id']);	
				$unit_id     = $item_info['item_unit'];		
				$item_byprd_data =  array("bom_id"=>$bom_id,"item_id"=>$by_prd_row['item_id'],"item_uom"=>$unit_id,"item_qty"=>$by_prd_row['item_qty'],"item_usr_amt"=>$by_prd_row['item_total_amount'],"item_amt"=>0,'bom_output_type'=>'product');
                $this->db->table($bomoutputn_tble)->insert($item_byprd_data);	
			    }
			}
		}
		
     if($additional_cost){
			foreach($additional_cost as $additional_cost_row){
				if(isset($additional_cost_row['expense_id']) && $additional_cost_row['expense_id']!=''){
				$adcost_data =  array("bom_id"=>$bom_id,"bom_addcost_acc_id"=>$additional_cost_row['expense_id'],"bom_addcost_type"=>$additional_cost_row['expense_type_id'],"bom_addcost_amt"=>$additional_cost_row['expense_amount']);
                $this->db->table($bomaddcost_tble)->insert($adcost_data);	
			    }
			}
		}
		
	 }	
	
	function get_item_info($item_id){	 
	  $comp_id = $this->company_id;
	  $item_master_tbl = $comp_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($item_master_tbl)->where('item_id', $item_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    } 
   
   function billofmaterial_info($bom_id){	 
        $billofmatn_tble =  $this->company_id.'_billofmatn_'.$this->session->get('ses_comp_fy_id');
		
	    $bominputnn_tble =  $this->company_id.'_bominputnn_'.$this->session->get('ses_comp_fy_id');
		$bomoutputn_tble =  $this->company_id.'_bomoutputn_'.$this->session->get('ses_comp_fy_id');
		$bomaddcost_tble =  $this->company_id.'_bomaddcost_'.$this->session->get('ses_comp_fy_id');
		
		$bom_data = $this->db->table($billofmatn_tble)->where('bom_id', $bom_id)->get()->getRowArray(); 
		$bom_name  = $bom_data['bom_name'];
		
		
	    $item_consumed_data = $this->db->table($bominputnn_tble)->where('bom_id', $bom_id)->get()->getResultArray(); 
		$item_produced_data = $this->db->table($bomoutputn_tble)->where('bom_id', $bom_id)->where('bom_output_type','f')->get()->getResultArray(); 
		$byproduct_produced_data = $this->db->table($bomoutputn_tble)->where('bom_id', $bom_id)->where('bom_output_type','p')->get()->getResultArray(); 
		$additional_cost_data   = $this->db->table($bomaddcost_tble)->where('bom_id', $bom_id)->get()->getResultArray(); 
		
		//item_consumed_data
		$final_item_consumed_data=array();
		$item_consumed_unit_labels=array();
		if($item_consumed_data){
			foreach($item_consumed_data as $row1){
				$item_info   = $this->get_item_info($row1['item_id']);	
				$unit_id     = $item_info['item_unit'];	
				$item_name   = $item_info['item_name'];
				
				
				$get_units_info  = $this->get_units_info($unit_id);
				$unit_name       =  $this->enc_string->nc_string($get_units_info['item_unit'],'de');
				$item_consumed_unit_labels[]= array("label"=>$item_name,"value"=>$unit_name);
				$final_item_consumed_data[]= array("id"=>$row1['bom_id'],"item_id"=>$row1['item_id'],"item_name"=>$item_name,'item_qty'=>$row1['item_qty'],'item_unit'=>$unit_name,'item_price'=>($row1['item_amt']/$row1['item_qty']),'item_amount'=>$row1['item_amt']);
				
			}
			
		}
		
		
		//item_produced_data
		$item_produced_unit_labels= array();
		$final_item_produced_data = array();
		if($item_produced_data){
			foreach($item_produced_data as $row2){
			    $item_info   = $this->get_item_info($row2['item_id']);	
			 	$unit_id     = $item_info['item_unit'];	
				$item_name   = $item_info['item_name'];
				
				$get_units_info  = $this->get_units_info($unit_id);
				$unit_name       =  $this->enc_string->nc_string($get_units_info['item_unit'],'de');
				$item_produced_unit_labels[]= array("label"=>$item_name,"value"=>$unit_name);
				$final_item_produced_data[]= array("id"=>$row2['bom_id'],"item_id"=>$row2['item_id'],"item_name"=>$item_name,'item_qty'=>$row2['item_qty'],'item_unit'=>$unit_name,'item_price'=>($row2['item_usr_amt']/$row2['item_qty']),'item_amount'=>$row2['item_usr_amt'],'item_value'=>'');
					
				
				
			}
			
		}
		
		
		//byproduct_produced_data
		$byproduct_produced_unit_labels = array();
		$final_byproducts_produced_data = array();
		if($byproduct_produced_data){
			foreach($byproduct_produced_data as $row3){
				$item_info   = $this->get_item_info($row3['item_id']);	
			 	$unit_id     = $item_info['item_unit'];	
				$item_name   = $item_info['item_name'];
				
				$get_units_info  = $this->get_units_info($unit_id);
				$unit_name       = $this->enc_string->nc_string($get_units_info['item_unit'],'de');
				$byproduct_produced_unit_labels[]= array("label"=>$item_name,"value"=>$unit_name);
				$final_byproducts_produced_data[]= array("id"=>$row3['bom_id'],"item_id"=>$row3['item_id'],"item_name"=>$item_name,'item_qty'=>$row3['item_qty'],'item_unit'=>$unit_name,'item_price'=>($row3['item_usr_amt']/$row3['item_qty']),'item_amount'=>$row3['item_usr_amt'],'item_value'=>'');
				
			}
			
		}
		
		//additional_cost_data
		$final_additional_cost_data = array();
		if($additional_cost_data){
			foreach($additional_cost_data as $row4){
			   
				if(strtolower($row4['bom_addcost_type'])=='p')
					  $bom_addcost_type ='Percentage';
				else if(strtolower($row4['bom_addcost_type'])=='a')
					  $bom_addcost_type ='Absolute';
				
				  $account_table_name = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
                  $account_table = $this->db->table($account_table_name)->where('acc_id', $row4['bom_addcost_acc_id'])->get()->getRowArray();
				  $expense_name   = $account_table['acc_name'];
				$final_additional_cost_data[]= array("id"=>$row4['bom_id'],"expense_name"=>$expense_name,'expense_type'=>$bom_addcost_type,'expense_amount'=>$row4['bom_addcost_amt']);
						
			}			
		}
		
		$final_array = array('bom_name'=>$bom_name,'item_consumed_unit_labels'=>$item_consumed_unit_labels,
		                     'item_produced_unit_labels'=> $item_produced_unit_labels,
							 'byproduct_produced_unit_labels' => $byproduct_produced_unit_labels,
		                     'item_consumed_data'=>$final_item_consumed_data,'item_produced_data'=>$final_item_produced_data,
							 'byproduct_produced_data'=>$final_byproducts_produced_data,
		                     'additional_cost_data'=>$final_additional_cost_data);
		return $final_array;					 
    } 
    
	function get_units_info($unit_id){	
      $comp_id	= $this->company_id;
	  $item_unit_master_tbl = $comp_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    }  
	
   	
	function units_dropdown($comp_id){
	   $item_unit_master_tbl =  $comp_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	 
	   $data =  $this->db->table($item_unit_master_tbl)->where('comp_id', $comp_id)->orderBy('item_unit','ASC')->get()->getResultArray();
	   $final_result      = array();
	   if($data){
		  foreach($data as $row){
		      $item_name      = $this->enc_string->nc_string($row['item_unit'],'de');
              $final_result[] =array("label"=>$item_name,"value"=>$row['unit_id']);			   
	        }
        }
	  return $final_result;	
     }

    function get_eh_groups()
    { 
		$p_groups = [11,13];
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

    function expense_heads_dropdown($comp_id){

    	$eh_groups = $this->get_eh_groups();

    	$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	  	return $this->db->table($account_master_tbl)
	  					->select('acc_name as label, acc_id as value')
	  					->whereIn('acc_grp_id', $eh_groups)
	  					->orWhereIn('acc_grp_parent_id', [11,13])
	  					->get()->getResultArray(); 
    }

   function expense_heads_dropdown2($comp_id){ //delete it
	   $groups_list = array('12','14');  // Direct/Indirect Expense
	   $accounts    = array();
	   foreach($groups_list as $group_id){
		   $accounts[]= $this->get_all_accounts($group_id);
		   
	   }
	  $accountsarray=array(); 
	  if($accounts){
		  foreach($accounts as $rows){
			  if($rows){
			   foreach($rows as $account_id => $account_name){
				  $accountsarray[]=  array("label"=>ucwords($account_name),'value'=> $account_id); 
			   }
				  
			   }
			  
		  }
		  
	  } 	
	   return $accountsarray;
     }  
	 
   function get_all_accounts($group_id){
	    $final_accounts  = array();
	    $comp_id         = $this->company_id;
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
		$tbl_name        = $comp_id.'_acctgroupn_'.$ses_comp_fy_id;
	    $sql             = $this->db->query("select `acc_grp_id`, `acc_grp_parent_id`, `acc_grp_name`,`under_acc_grp_id` from (select * from `".$tbl_name."` order by `under_acc_grp_id`, `acc_grp_id`) `".$tbl_name."` , (select @pv := '".$group_id."') initialisation where find_in_set(`under_acc_grp_id`, @pv) > 0 and @pv := concat(@pv, ',', `acc_grp_id`);");
               $result     = $sql->getResultArray();
               foreach($result as $key => $row){
                   if($row!=''){
                       $company_all_accounts  = $this->get_group_all_accounts($row['acc_grp_id'],$comp_id);	
                        foreach($company_all_accounts as $company_row){	
                          $final_accounts[$company_row['acc_id']]=$company_row['acc_name'];						
						}
				   }
			   }
			  return $final_accounts; 
       }
   
    function get_group_all_accounts($group_id,$company_id){	
	    $account_master_tbl = $company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($account_master_tbl); 
		$builder->select(array('acc_id','acc_name','acc_op_bal','acc_grp_id'));
        $builder->where('comp_id', $company_id);
		$builder->where('acc_grp_id', $group_id);
		$result = $builder->get()->getResultArray();
	    return $result;
     }   

   public function ajax_billofmaterial_list(){
       
	    $comp_id         = $this->session->get('ses_company_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $billofmatn_tbl = $this->company_id.'_billofmatn_'.$this->session->get('ses_comp_fy_id'); 
	    $builder         = $this->db->table($billofmatn_tbl); 
        $total_Records = $builder->countAll();
        if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
            
         $offset = ($pq_curPage > 1) ? ($pq_rPP * ($pq_curPage - 1)) : 0;
         $builder->orderBy('bom_name');                
	 	
	 	 $builder->limit($pq_rPP,$offset);
		 $result = $builder->get()->getResultArray();
         
         $records=array(); 		
          foreach($result as $values){
         	$records[]       = array(	
			                      'checkbox'     =>'<input name="item_ids[]" class="checkbox items_row"  data-id="'.$values['bom_id'].'"  type="checkbox" value="'.$values['bom_id'].'">',
    							  'bom_id'     => $values['bom_id'],
    							  'bom_name'   => ucwords($values['bom_name']),
    							 );  
		           }
       echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
        
        
     }


}