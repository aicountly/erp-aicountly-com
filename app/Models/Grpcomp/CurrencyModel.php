<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Models\Admin\CompanyModel;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class CurrencyModel extends Model	
{
	function __construct() {
       parent::__construct();        
       $this->CompanyModel   = new CompanyModel();
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
    }

	function get_currencies_list($pq_curPage, $limit, $offset, $company_id)
	{
		$company_info = $this->CompanyModel->get_info($company_id);				  
		$ses_comp_fy_id =  $company_info['comp_fy_id'];

        $extdb  = $this->externaldb->single_company_db($company_info['comp_code']);

		$compcurrcy_tbl = $company_id.'_compcurrcy_'.$ses_comp_fy_id;
    	$totalRecords = $extdb->table($compcurrcy_tbl)->where('comp_id', $company_id)->countAllResults();

    	if ($offset > $totalRecords){        
            $pq_curPage = ceil($totalRecords / $limit);
            $offset = ($limit * ($pq_curPage - 1));
        }

    	$data = $extdb->table($compcurrcy_tbl)->where('comp_id', $company_id)->limit($limit,$offset)->get()->getResultArray();

    	foreach ($data as $key => $value) {

    		$data[$key]['checkbox'] = '<input name="item_ids[]" class="checkbox items_row"  data-id="'.$value['comp_currency_id'].'"  type="checkbox" value="'.$value['comp_currency_id'].'">';

    	}

		return [
			'totalRecords' => $totalRecords,
			'data' => $data,
		];
	}

	function add_comp_currency($company_id, $data)
	{
		$company_info = $this->CompanyModel->get_info($company_id);				  
		$ses_comp_fy_id =  $company_info['comp_fy_id'];

        $extdb  = $this->externaldb->single_company_db($company_info['comp_code']);

		$compcurrcy_tbl = $company_id.'_compcurrcy_'.$ses_comp_fy_id;
		$extdb->table($compcurrcy_tbl)->insert($data);

		return $extdb->insertID();
	}

	function add_forex_rates($company_id, $data)
	{
		$company_info = $this->CompanyModel->get_info($company_id);				  
		$ses_comp_fy_id =  $company_info['comp_fy_id'];

        $extdb  = $this->externaldb->single_company_db($company_info['comp_code']);

		$forexrates_tbl = $company_id.'_forexrates_'.$ses_comp_fy_id;
		$extdb->table($forexrates_tbl)->insert($data);
	}

	function update_comp_currency($company_id, $data, $comp_currency_id)
	{
		$company_info = $this->CompanyModel->get_info($company_id);				  
		$ses_comp_fy_id =  $company_info['comp_fy_id'];

        $extdb  = $this->externaldb->single_company_db($company_info['comp_code']);

		$compcurrcy_tbl = $company_id.'_compcurrcy_'.$ses_comp_fy_id;
		$extdb->table($compcurrcy_tbl)->where('comp_currency_id', $comp_currency_id)->update($data);
	}

	function update_forex_rates($company_id, $data, $forex_rate_id)
	{
		$company_info = $this->CompanyModel->get_info($company_id);				  
		$ses_comp_fy_id =  $company_info['comp_fy_id'];

        $extdb  = $this->externaldb->single_company_db($company_info['comp_code']);

		$forexrates_tbl = $company_id.'_forexrates_'.$ses_comp_fy_id;
		$extdb->table($forexrates_tbl)->where('forex_rate_id', $forex_rate_id)->update($data);
	}



	function delete_comp_currency($company_id, $comp_currency_id)
	{
		$company_info = $this->CompanyModel->get_info($company_id);				  
		$ses_comp_fy_id =  $company_info['comp_fy_id'];

        $extdb  = $this->externaldb->single_company_db($company_info['comp_code']);

		$compcurrcy_tbl = $company_id.'_compcurrcy_'.$ses_comp_fy_id;
		$extdb->table($compcurrcy_tbl)->where('comp_currency_id', $comp_currency_id)->delete();

		$forexrates_tbl = $company_id.'_forexrates_'.$ses_comp_fy_id;
		$extdb->table($forexrates_tbl)->where('comp_currency_id', $comp_currency_id)->delete();
	}

	function get_currency_data($company_id, $comp_currency_id)
	{
		$company_info = $this->CompanyModel->get_info($company_id);				  
		$ses_comp_fy_id =  $company_info['comp_fy_id'];

        $extdb  = $this->externaldb->single_company_db($company_info['comp_code']);

		$compcurrcy_tbl = $company_id.'_compcurrcy_'.$ses_comp_fy_id;
		return $extdb->table($compcurrcy_tbl)->where('comp_currency_id', $comp_currency_id)->get()->getRowArray();
	}

	function check_currency_name_exists($company_id, $curr_name, $comp_currency_id = 0)
	{
		$company_info = $this->CompanyModel->get_info($company_id);				  
		$ses_comp_fy_id =  $company_info['comp_fy_id'];

        $extdb  = $this->externaldb->single_company_db($company_info['comp_code']);

		$compcurrcy_tbl = $company_id.'_compcurrcy_'.$ses_comp_fy_id;
		$builder = $extdb->table($compcurrcy_tbl);
		$builder->where('comp_id',$company_id);
		$builder->where('curr_name',$curr_name);
		if($comp_currency_id != 0){
			$builder->where('comp_currency_id !=',$comp_currency_id);
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

	function get_forex_rate_list($pq_curPage, $limit, $offset, $company_id, $comp_currency_id)
	{
		$company_info = $this->CompanyModel->get_info($company_id);				  
		$ses_comp_fy_id =  $company_info['comp_fy_id'];

        $extdb  = $this->externaldb->single_company_db($company_info['comp_code']);

		$forexrates_tbl = $company_id.'_forexrates_'.$ses_comp_fy_id;
    	$totalRecords = $extdb->table($forexrates_tbl)->where('comp_currency_id', $comp_currency_id)->countAllResults();

    	if ($offset > $totalRecords){        
            $pq_curPage = ceil($totalRecords / $limit);
            $offset = ($limit * ($pq_curPage - 1));
        }

    	$data = $extdb->table($forexrates_tbl)->where('comp_currency_id', $comp_currency_id)->orderBy('curr_date', 'desc')->limit($limit,$offset)->get()->getResultArray();

    	foreach ($data as $key => $value) {

    		if($data[$key]['forex_type'] == 'A')
    			$data[$key]['forex_type'] = 'Automatic';

    		if($data[$key]['forex_type'] == 'M')
    			$data[$key]['forex_type'] = 'Manual';


    		$data[$key]['curr_date'] = ($value['curr_date'] == '' || $value['curr_date'] == '0000-00-00') ? '' : date('d-m-Y', strtotime($value['curr_date']));

    		$data[$key]['checkbox'] = '<input name="item_ids[]" class="checkbox items_row"  data-id="'.$value['forex_rate_id'].'"  type="checkbox" value="'.$value['forex_rate_id'].'">';
    	}

		return [
			'totalRecords' => $totalRecords,
			'data' => $data,
		];
	}

	public function check_forex_date_exists($company_id, $curr_date, $comp_currency_id, $forex_rate_id = 0)
	{
		$company_info = $this->CompanyModel->get_info($company_id);				  
		$ses_comp_fy_id =  $company_info['comp_fy_id'];

        $extdb  = $this->externaldb->single_company_db($company_info['comp_code']);

        $forexrates_tbl = $company_id.'_forexrates_'.$ses_comp_fy_id;
    	$builder = $extdb->table($forexrates_tbl);
    	$builder->where('curr_date', $curr_date);
    	$builder->where('comp_currency_id', $comp_currency_id);
    	if($forex_rate_id != 0)
    		$builder->where('forex_rate_id !=', $forex_rate_id);
    	$result = $builder->get()->getResultArray();

    	if($result){
    		return true;
    	}

    	return false;
	}

}