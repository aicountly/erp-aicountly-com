<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\Admin\CompanyModel;


class CurrencyModel extends Model	
{
	protected $CompanyModel;
	protected $session;	
	protected $company_id;
	protected $fy_id;
	protected $bo_id;
	function __construct() {
       parent::__construct();        
	   $this->externaldb    = new externaldb();	
       $this->CompanyModel   = new CompanyModel();
       $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->fy_id         =  $this->session->get('ses_comp_fy_id') ?? 0;
	   $this->bo_id         =  $this->session->get('ses_boid') ?? 0;
	   $this->univaictly    =  $this->externaldb->univaictly_db();
    }

	function get_currencies_list($company_id){
		$data = $this->univaictly->table("cmpfcymstn")
						->where('cmp_id', $company_id)
						->get()
						->getResultArray();
		// Add checkbox HTML
		foreach ($data as &$row) {
			$id = $row['cmp_fcy_mst_id'];
			
			$row['checkbox'] = '<input name="currency_ids[]" class="checkbox currency_row" data-id="' . $id . '" type="checkbox" value="' . $id . '">';
		}
		unset($row); // Best practice after reference foreach
		
    	return [
			'totalRecords' => count($data),
			'data' => $data,
		];
	}
	
	function add_comp_currency($data){
		$this->univaictly->table("cmpfcymstn")->insert($data);       
		return $this->univaictly->insertID();
	}
	
	function update_comp_currency($data, $cmp_fcy_mst_id){ 
		$this->univaictly->table("cmpfcymstn")->where('cmp_fcy_mst_id', $cmp_fcy_mst_id)->update($data);
    }
	
	function delete_comp_currency($cmp_fcy_mst_id){
		$this->univaictly->table("cmpfcymstn")->where('cmp_fcy_mst_id', $cmp_fcy_mst_id)->delete();
	}

	function get_currency_data($company_id, $comp_currency_id)
	{
		return $this->univaictly->table("cmpfcymstn")->where('cmp_id', $company_id)->where('cmp_fcy_mst_id', $comp_currency_id)->get()->getRowArray();
	}

	function check_currency_name_exists($data,$comp_currency_id=0){
		$cmp_id = trim($data['cmp_id']);
		$cmp_fcy_name   = strtolower(trim($data['cmp_fcy_name']));
		$cmp_fcy_symbol = strtolower(trim($data['cmp_fcy_symbol']));
		$builder = $this->univaictly->table("cmpfcymstn")->where('cmp_id', $cmp_id);
		if ($comp_currency_id > 0) {
			$builder_name   = clone $builder;
			$builder_symbol = clone $builder;
			
			$exists1 = $builder_name
				->where('cmp_fcy_mst_id !=', $comp_currency_id)
				->where('LOWER(cmp_fcy_name)', $cmp_fcy_name)
				->get()->getRowArray();

			$exists2 = $builder_symbol
				->where('cmp_fcy_mst_id !=', $comp_currency_id)
				->where('LOWER(cmp_fcy_symbol)', $cmp_fcy_symbol)
				->get()->getRowArray();
		} else {
			$exists1 = $builder
				->where('LOWER(cmp_fcy_name)', $cmp_fcy_name)
				->get()->getRowArray();

			$exists2 = $this->univaictly->table("cmpfcymstn")
				->where('cmp_id', $cmp_id)
				->where('LOWER(cmp_fcy_symbol)', $cmp_fcy_symbol)
				->get()->getRowArray();
		}

		if ($exists1) {
			return "-1";
		} elseif ($exists2) {
			return "-2";
		} else {
			return "1";
		}	
	}

}