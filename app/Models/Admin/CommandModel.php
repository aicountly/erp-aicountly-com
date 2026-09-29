<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\CommonModel;
use App\Models\Admin\TransactionModel;
use App\Libraries\enc_string;
use App\Libraries\UUIDtables;
use App\Libraries\ERPtables;
class CommandModel extends Model	{
    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->CommonModel   =  new CommonModel();
	   $this->TransactionModel   =  new TransactionModel();	
	   $this->aicountly_db  = $this->externaldb->aicountly_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
       $this->bo_id         = $this->session->get('ses_boid');
	  
    } 


   public function crs_types($keyword, $child)
{
    $keyword = strtolower(trim($keyword));
    $company_id = $this->session->get('ses_company_id');
    $comp_fy_id = $this->session->get('ses_comp_fy_id');
    $acctmaster_tbl = $company_id . '_' . $child . '_' . $comp_fy_id;

    // Step 1: Fetch all data
    $rows = $this->db->table($acctmaster_tbl)->select('acc_id, acc_name, acc_name_alias, acc_short_code')->get()->getResultArray();

    // Step 2: Prioritize based on keyword match
    $exactMatches = [];
    $beginsWithMatches = [];
    $containsMatches = [];

    foreach ($rows as $row) {
        $name = strtolower($row['acc_name'] ?? '');
        $alias = strtolower($row['acc_name_alias'] ?? '');

        if ($name === $keyword || $alias === $keyword) {
            $exactMatches[] = $row;
        } elseif (str_starts_with($name, $keyword) || str_starts_with($alias, $keyword)) {
            $beginsWithMatches[] = $row;
        } elseif (str_contains($name, $keyword) || str_contains($alias, $keyword)) {
            $containsMatches[] = $row;
        }
    }

    // Step 3: Return merged results
    return array_merge($exactMatches, $beginsWithMatches, $containsMatches);
}


}