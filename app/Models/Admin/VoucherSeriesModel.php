<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb; 

class VoucherSeriesModel extends Model	{

    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();		  
	   $this->session       = \Config\Services::session();
	   $this->company_id    =  $this->session->get('ses_company_id');
	   $this->ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
       $this->univerpaic_db =  $this->externaldb->univerpaic_db();		
       
       $this->contactaic_db  = $this->externaldb->contactaic_db();	 
	   $this->univaictly    =  $this->externaldb->univaictly_db();
	   
    }
	public function banks_dropdown(){
		 $data             =   $this->univaictly->table("cmpbankmst")->where('cmp_id', $this->company_id)->orderBy('bank_name','ASC')->get()->getResultArray();
	     $final_result     = array();	
		 $final_result[''] = 'Choose';		 
	     if($data){
		  foreach($data as $row){
              $final_result[$row['bank_id']] = ucwords($row['bank_name']);			   
	        }
        }
	  return $final_result;	
     }
	
  function AutoSeriesExists($voucher_type_id,$series_format,$series_id){
	$builder        = $this->db->table('vchseriesn');
	$builder->join('vchseriesa', 'vchseriesa.vch_series_id=vchseriesn.vch_series_id');
	$builder->where('vchseriesn.vch_type_id', $voucher_type_id);	
	if($series_id>0){
	 $builder->where('vchseriesn.vch_series_id !=',$series_id);	
	}
	$response      = $builder->get()->getResultArray();	
	
	$series_exists = 0;
	if($response){
		foreach($response as $row){
			if($row['vch_series_padding'] >0)
			$comp_vch_start     = sprintf("%'.0".trim($row['vch_series_padding'])."d", trim($row['vch_series_start']));
			else
			$comp_vch_start     = trim($row['vch_series_start']);	
			$saved_seriesformat = trim($row['vch_series_prefix']).$comp_vch_start.trim($row['vch_series_suffix']);
			if(strtolower(trim($saved_seriesformat))==strtolower(trim($series_format)))
				 $series_exists=$series_exists+1;
			 }
	   }
	  $ajax_response = ['series_exists'=>$series_exists];
	return $ajax_response;
  } 
  
	function get_voucher_types()
	{
		$result = $this->db->table("vchtypemst")->get()->getResultArray();
		return $result;
	}

	function voucher_type_info($vch_type_id)
	{
		$result = $this->db->table("vchtypemst")->where('vch_type_id', $vch_type_id)->get()->getRowArray();
		return $result;
	}

	function get_series_list($pq_curPage, $limit, $offset, $voucher_type_id)
	{
    	$totalRecords   = $this->db->table("vchseriesn")->where('cmp_id', $this->company_id)->where('vch_type_id', $voucher_type_id)->countAllResults();

		if($pq_curPage==0) $pq_curPage=1;
    	if ($offset > $totalRecords){        
            $pq_curPage = ceil($totalRecords / $limit);
            $offset = ($limit * ($pq_curPage - 1));
        }

    	$data = $this->db->table("vchseriesn")->where('cmp_id', $this->company_id)->where('vch_type_id', $voucher_type_id)->limit($limit,$offset)->get()->getResultArray();

    	foreach ($data as $key => $value) {
    		$data[$key]['no_of_vouchers'] = $this->db->table("vchtxnconso")->where('vch_type_id', $voucher_type_id)->where('vch_series_id', $value['vch_series_id'])->countAllResults();
			$data[$key]['comp_vch_method'] =($value['vch_series_method']=="1")?"Automatic":"Manual";
    		$data[$key]['checkbox'] = '<input name="item_ids[]" class="checkbox items_row"  data-id="'.$value['vch_series_id'].'"  type="checkbox" value="'.$value['vch_series_id'].'">';
    	 }
		return [
			'totalRecords' => $totalRecords,
			'data' => $data,
		 ];
	}
	
	function add_comp_vch_series($data)
	{
		$this->db->table("vchseriesn")->insert($data);
		$comp_vch_series_id =  $this->db->insertID();
		return $comp_vch_series_id;
	}
	
	function add_vchseriesm($data){
		$this->db->table("vchseriesm")->insert($data);
	}
	
	function add_vchseriesa($data){
		$this->db->table("vchseriesa")->insert($data);
	}

	function update_comp_vch_series($comp_vch_series_id, $data)
	{
		$this->db->table("vchseriesn")->where('cmp_id', $this->company_id)->where('vch_series_id',$comp_vch_series_id)->update($data);
	}

	function delete_comp_vch_series($comp_vch_series_id)
	{
		$this->db->table("vchseriesa")->where('cmp_id', $this->company_id)->where('vch_series_id',$comp_vch_series_id)->delete();
		$this->db->table("vchseriesm")->where('cmp_id', $this->company_id)->where('vch_series_id',$comp_vch_series_id)->delete();
		$this->db->table("vchseriesn")->where('cmp_id', $this->company_id)->where('vch_series_id',$comp_vch_series_id)->delete();
     	// Delete from universal back date entry table erpbdeacsn  as per series deleted
	//	$this->univerpaic_db->table("erpbdeacsn")->where('vch_series_id',$comp_vch_series_id)->delete();
	}
	
	function remove_auto_series($comp_vch_series_id)
	{
		$this->db->table("vchseriesa")->where('cmp_id', $this->company_id)->where('vch_series_id',$comp_vch_series_id)->delete();
	}
	
    function remove_manual_series($comp_vch_series_id)
	{
	    $this->db->table("vchseriesm")->where('cmp_id', $this->company_id)->where('vch_series_id',$comp_vch_series_id)->delete();
	}

	function get_series_data($comp_vch_series_id)
	{		
		$series_info = $this->db->table("vchseriesn")->where('cmp_id', $this->company_id)->where('vch_series_id',$comp_vch_series_id)->get()->getRowArray();
		if($series_info){
		// automatic or mannual
		if($series_info['vch_series_method']==1){
		  $series_type='a';	
		  $seriesam_info = $this->db->table("vchseriesa")->where('cmp_id', $this->company_id)->where('vch_series_id',$comp_vch_series_id)->get()->getRowArray();			
		}
		else if($series_info['vch_series_method']==0){
			$series_type='m';
		    $seriesam_info = $this->db->table("vchseriesm")->where('cmp_id', $this->company_id)->where('vch_series_id',$comp_vch_series_id)->get()->getRowArray();			
		}		
		return ["series_info"=>$series_info,"seriesam_info"=>$seriesam_info,'series_type'=>$series_type];
		}
	}

	function check_series_name_exists($comp_vch_series, $voucher_type_id, $comp_vch_series_id='')
	{
		$builder = $this->db->table("vchseriesn");
		$builder->where('LOWER(vch_series_name)',strtolower($comp_vch_series));
		$builder->where('vch_type_id',$voucher_type_id);
		if($comp_vch_series_id >0)
		$builder->where('vch_series_id !=',$comp_vch_series_id);	
		$builder->where('cmp_id', $this->company_id);
		$data = $builder->get()->getRowArray();		
		if($data)
			return true;
		return false;
	}

	function check_series_vouchers($comp_vch_series_id)
	{
		$no_of_vouchers = $this->db->table("vchtxnconso")->where('cmp_id', $this->company_id)->where('vch_series_id', $comp_vch_series_id)->countAllResults();
    	return $no_of_vouchers > 0 ? true : false;
	}

	function check_series_count($voucher_type_id)
	{
		$count = $this->db->table("vchseriesn")->where('cmp_id', $this->company_id)->where('vch_type_id', $voucher_type_id)->countAllResults();
    	return $count == 1 ? true : false;
	}

}