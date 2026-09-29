<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Api\AppCommonModel;
use Throwable;

class ReportsController extends ResourceController
{
    use ResponseTrait;

    protected AppCommonModel $appCommon;
 
    public function __construct()
    {
        $this->appCommon = new AppCommonModel();
    }
 

   public function account_ledger()
    {	
        // 1) Extract Bearer token
        /* $authHeader =
            $this->request->getHeaderLine('Authorization')
            ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
            ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

        if (!$authHeader || !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            return $this->failUnauthorized('Session key missing');
        }
        $sesKey = trim($matches[1]);  */

        // 2) Extract X-CMP-ID header (handle hyphen/underscore variants)
		$compIdinfo = $this->request->getJSON(true) ?? [];
		
		$bo_id = isset($compIdinfo['bo_id']) ? (int)$compIdinfo['bo_id'] : 0;
        $fy_id = isset($compIdinfo['fy_id']) ? (int)$compIdinfo['fy_id'] : 0;
        $compId = isset($compIdinfo['cmp_id']) ? (int)$compIdinfo['cmp_id'] : 0;
        
	    if ($compId === 0) {
            return $this->failValidationErrors(['CMP-ID' => 'Company id  missing']);
        }

        // 3) Validate session key
        /*  $ses = $this->appCommon->validateSesKey($sesKey);
        if (!$ses || ($ses['status'] ?? 0) !== 1) {
            return $this->failUnauthorized('Session expired or invalid');
        }  */

       // $uuid = $ses['uuid_aictly'] ?? ''; // derived from ses_key       
		
        // 4) Fetch inventory Status
        $resp = $this->appCommon->GetAccountLedger($compId, $bo_id,$fy_id,$compIdinfo);
        // 5) Respond
        return $this->respond($resp);
    }
	
	public function stock_ledger()
    {	
        // 1) Extract Bearer token
        /* $authHeader =
            $this->request->getHeaderLine('Authorization')
            ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
            ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

        if (!$authHeader || !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            return $this->failUnauthorized('Session key missing');
        }
        $sesKey = trim($matches[1]);  */

        // 2) Extract X-CMP-ID header (handle hyphen/underscore variants)
		$compIdinfo = $this->request->getJSON(true) ?? [];
		
		$bo_id = isset($compIdinfo['bo_id']) ? (int)$compIdinfo['bo_id'] : 0;
        $fy_id = isset($compIdinfo['fy_id']) ? (int)$compIdinfo['fy_id'] : 0;
        $compId = isset($compIdinfo['cmp_id']) ? (int)$compIdinfo['cmp_id'] : 0;
        
	    if ($compId === 0) {
            return $this->failValidationErrors(['CMP-ID' => 'Company id  missing']);
        }

        // 3) Validate session key
        /*  $ses = $this->appCommon->validateSesKey($sesKey);
        if (!$ses || ($ses['status'] ?? 0) !== 1) {
            return $this->failUnauthorized('Session expired or invalid');
        }  */

       // $uuid = $ses['uuid_aictly'] ?? ''; // derived from ses_key       
		
        // 4) Fetch inventory Status
        $resp = $this->appCommon->GetStockLedger($compId, $bo_id,$fy_id,$compIdinfo);
        // 5) Respond
        return $this->respond($resp);
    }
	
	public function stock_summary()
    {	
        // 1) Extract Bearer token
        /* $authHeader =
            $this->request->getHeaderLine('Authorization')
            ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
            ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

        if (!$authHeader || !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            return $this->failUnauthorized('Session key missing');
        }
        $sesKey = trim($matches[1]);  */

        // 2) Extract X-CMP-ID header (handle hyphen/underscore variants)
		$compIdinfo = $this->request->getJSON(true) ?? [];
		
		$bo_id = isset($compIdinfo['bo_id']) ? (int)$compIdinfo['bo_id'] : 0;
        $fy_id = isset($compIdinfo['fy_id']) ? (int)$compIdinfo['fy_id'] : 0;
        $compId = isset($compIdinfo['cmp_id']) ? (int)$compIdinfo['cmp_id'] : 0;
        
	    if ($compId === 0) {
            return $this->failValidationErrors(['CMP-ID' => 'Company id  missing']);
        }

        // 3) Validate session key
        /*  $ses = $this->appCommon->validateSesKey($sesKey);
        if (!$ses || ($ses['status'] ?? 0) !== 1) {
            return $this->failUnauthorized('Session expired or invalid');
        }  */

       // $uuid = $ses['uuid_aictly'] ?? ''; // derived from ses_key       
		
        // 4) Fetch inventory Status
        $resp = $this->appCommon->GetStockSummary($compId, $bo_id,$fy_id,$compIdinfo);
        // 5) Respond
        return $this->respond($resp);
    }
    
    	public function gst_summary()
    {	
        // 1) Extract Bearer token
        /* $authHeader =
            $this->request->getHeaderLine('Authorization')
            ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
            ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

        if (!$authHeader || !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            return $this->failUnauthorized('Session key missing');
        }
        $sesKey = trim($matches[1]);  */

        // 2) Extract X-CMP-ID header (handle hyphen/underscore variants)
		$compIdinfo = $this->request->getJSON(true) ?? [];
		
		$bo_id = isset($compIdinfo['bo_id']) ? (int)$compIdinfo['bo_id'] : 0;
        $fy_id = isset($compIdinfo['fy_id']) ? (int)$compIdinfo['fy_id'] : 0;
        $compId = isset($compIdinfo['cmp_id']) ? (int)$compIdinfo['cmp_id'] : 0;
        
	    if ($compId === 0) {
            return $this->failValidationErrors(['CMP-ID' => 'Company id  missing']);
        }

        // 3) Validate session key
        /*  $ses = $this->appCommon->validateSesKey($sesKey);
        if (!$ses || ($ses['status'] ?? 0) !== 1) {
            return $this->failUnauthorized('Session expired or invalid');
        }  */

       // $uuid = $ses['uuid_aictly'] ?? ''; // derived from ses_key       
		
        // 4) Fetch inventory Status
        $resp = $this->appCommon->GetGSTSummary($compId, $bo_id,$fy_id,$compIdinfo);
        // 5) Respond
        return $this->respond($resp);
    }
	
	public function inventory_status()
    {	
        // 1) Extract Bearer token
        $authHeader =
            $this->request->getHeaderLine('Authorization')
            ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
            ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

        if (!$authHeader || !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            return $this->failUnauthorized('Session key missing');
        }
        $sesKey = trim($matches[1]); 

        // 2) Extract X-CMP-ID header (handle hyphen/underscore variants)
		$compIdinfo = $this->request->getJSON(true) ?? [];
		
		$bo_id = isset($compIdinfo['bo_id']) ? (int)$compIdinfo['bo_id'] : 0;
        $fy_id = isset($compIdinfo['fy_id']) ? (int)$compIdinfo['fy_id'] : 0;
        $compId = isset($compIdinfo['cmp_id']) ? (int)$compIdinfo['cmp_id'] : 0;
        
	    if ($compId === 0) {
            return $this->failValidationErrors(['CMP-ID' => 'Company id  missing']);
        }

        // 3) Validate session key
         $ses = $this->appCommon->validateSesKey($sesKey);
        if (!$ses || ($ses['status'] ?? 0) !== 1) {
            return $this->failUnauthorized('Session expired or invalid');
        } 

       // $uuid = $ses['uuid_aictly'] ?? ''; // derived from ses_key       
		
        // 4) Fetch inventory Status
        $resp = $this->appCommon->GetInventoryStatus($compId, $bo_id,$fy_id,$compIdinfo);

        // 5) Respond
        return $this->respond(json_decode($resp, true));
    }
	 public function daybook()
    {
	
        // 1) Extract Bearer token
        $authHeader =
            $this->request->getHeaderLine('Authorization')
            ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
            ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

         if (!$authHeader || !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            return $this->failUnauthorized('Session key missing');
        }
        $sesKey = trim($matches[1]);

        // 2) Extract X-CMP-ID header (handle hyphen/underscore variants)
		$compIdinfo = $this->request->getJSON(true) ?? [];
		
		$bo_id = isset($compIdinfo['bo_id']) ? (int)$compIdinfo['bo_id'] : 0;
        $fy_id = isset($compIdinfo['fy_id']) ? (int)$compIdinfo['fy_id'] : 0;
        $compId = isset($compIdinfo['cmp_id']) ? (int)$compIdinfo['cmp_id'] : 0;
        
	    if ($compId === 0) {
            return $this->failValidationErrors(['CMP-ID' => 'Company id  missing']);
        }

        // 3) Validate session key
         $ses = $this->appCommon->validateSesKey($sesKey);
        if (!$ses || ($ses['status'] ?? 0) !== 1) {
            return $this->failUnauthorized('Session expired or invalid');
        } 

       // $uuid = $ses['uuid_aictly'] ?? ''; // derived from ses_key       
		
        // 4) Fetch inventory Status
        $resp = $this->appCommon->GetDayBook($compId, $bo_id,$fy_id,$compIdinfo);

        // 5) Respond
        return $this->respond(json_decode($resp, true));
    }
	
	
	public function BalanceSheet()
    {
	
        // 1) Extract Bearer token
        $authHeader =
            $this->request->getHeaderLine('Authorization')
            ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
            ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

         if (!$authHeader || !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            return $this->failUnauthorized('Session key missing');
        }
        $sesKey = trim($matches[1]);

        // 2) Extract X-CMP-ID header (handle hyphen/underscore variants)
		$compIdinfo = $this->request->getJSON(true) ?? [];
		
		$bo_id = isset($compIdinfo['bo_id']) ? (int)$compIdinfo['bo_id'] : 0;
        $fy_id = isset($compIdinfo['fy_id']) ? (int)$compIdinfo['fy_id'] : 0;
        $compId = isset($compIdinfo['cmp_id']) ? (int)$compIdinfo['cmp_id'] : 0;
        
	    if ($compId === 0) {
            return $this->failValidationErrors(['CMP-ID' => 'Company id  missing']);
        }

        // 3) Validate session key
         $ses = $this->appCommon->validateSesKey($sesKey);
        if (!$ses || ($ses['status'] ?? 0) !== 1) {
            return $this->failUnauthorized('Session expired or invalid');
        } 

       // $uuid = $ses['uuid_aictly'] ?? ''; // derived from ses_key       
		
        // 4) Fetch inventory Status
        $resp = $this->appCommon->GetDayBook($compId, $bo_id,$fy_id,$compIdinfo);

        // 5) Respond
        return $this->respond(json_decode($resp, true));
    }
}