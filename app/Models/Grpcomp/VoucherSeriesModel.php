<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class VoucherSeriesModel extends Model	{

    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    =  $this->session->get('ses_company_id');
	   $this->ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
	   $this->enc_string    = new enc_string();
    }
	
	function get_voucher_types()
	{
		$cmpvchtype_tbl = $this->company_id.'_cmpvchtype_'.$this->ses_comp_fy_id;
    	$result = $this->db->table($cmpvchtype_tbl)->get()->getResultArray();

		return $result;
	}

	function get_series_list($pq_curPage, $limit, $offset, $voucher_type_id)
	{
		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
    	$totalRecords = $this->db->table($cmpvchseri_tbl)->where('voucher_type_id', $voucher_type_id)->countAllResults();

    	if ($offset > $totalRecords){        
            $pq_curPage = ceil($totalRecords / $limit);
            $offset = ($limit * ($pq_curPage - 1));
        }

    	$data = $this->db->table($cmpvchseri_tbl)->where('voucher_type_id', $voucher_type_id)->limit($limit,$offset)->get()->getResultArray();

    	foreach ($data as $key => $value) {



    		$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
    		$data[$key]['no_of_vouchers'] = $this->db->table($voucher_tbl)->where('voucher_type_id', $voucher_type_id)->where('comp_vch_series_id', $value['comp_vch_series_id'])->countAllResults();

    		$data[$key]['checkbox'] = '<input name="item_ids[]" class="checkbox items_row"  data-id="'.$value['comp_vch_series_id'].'"  type="checkbox" value="'.$value['comp_vch_series_id'].'">';
    	}

		return [
			'totalRecords' => $totalRecords,
			'data' => $data,
		];
	}

	function add_comp_vch_series($data)
	{
		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$this->db->table($cmpvchseri_tbl)->insert($data);

		return $this->db->insertID();
	}

	function update_comp_vch_series($comp_vch_series_id, $data)
	{
		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$this->db->table($cmpvchseri_tbl)->where('comp_vch_series_id',$comp_vch_series_id)->update($data);
	}

	function delete_comp_vch_series($comp_vch_series_id)
	{
		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$this->db->table($cmpvchseri_tbl)->where('comp_vch_series_id',$comp_vch_series_id)->delete();
	}

	function get_series_data($comp_vch_series_id)
	{
		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		return $this->db->table($cmpvchseri_tbl)->where('comp_vch_series_id',$comp_vch_series_id)->get()->getRowArray();
	}

	function check_series_name_exists($comp_vch_series, $voucher_type_id, $comp_vch_series_id = 0)
	{
		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$builder = $this->db->table($cmpvchseri_tbl);
		$builder->where('comp_vch_series',$comp_vch_series);
		$builder->where('voucher_type_id',$voucher_type_id);
		if($comp_vch_series_id != 0){
			$builder->where('comp_vch_series_id !=',$comp_vch_series_id);
		}
		$data = $builder->get()->getRowArray();

		if($data)
			return true;

		return false;
	}

	function check_series_vouchers($comp_vch_series_id)
	{
		$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
    	$no_of_vouchers = $this->db->table($voucher_tbl)->where('comp_vch_series_id', $comp_vch_series_id)->countAllResults();

    	return $no_of_vouchers > 0 ? true : false;
	}

	function check_series_count($voucher_type_id)
	{
		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
    	$count = $this->db->table($cmpvchseri_tbl)->where('voucher_type_id', $voucher_type_id)->countAllResults();

    	return $count == 1 ? true : false;
	}

}