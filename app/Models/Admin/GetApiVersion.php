<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\CommonModel;
class GetApiVersion extends Model	{
    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->CommonModel   = new CommonModel();	
	   $this->dberpunvrsl   = $this->externaldb->erp_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
    } 	
 public function api_version(string $reportCat,string $reportType,string $userDate): ?string
   {   
    $builder = $this->dberpunvrsl->table('aictlyerp_univapiver_univdb')
        ->select('apiver_version');
	if($reportCat)	
        $builder->where('apiver_report_cat',$reportCat);
	if($reportType)
        $builder->where('apiver_report_type',$reportType);
	
        $builder->where('apiver_wef <=',$userDate);
        $builder->orderBy('apiver_wef', 'DESC') ; 
        $builder->limit(1);
       $result =  $builder->get()->getRow();
    return $result->apiver_version ?? null;
   }
	
}
?>