<?php
namespace App\Models\Api;

use CodeIgniter\Model;
use App\Models\Admin\StockStatusModel;
use App\Models\Admin\LedgerModel;
use App\Models\Admin\ItemsModel;
use App\Models\Admin\AccountsModel;
use App\Models\Admin\ItemSummaryModel;
use App\Models\Admin\GstrReportModel;
use App\Libraries\externaldb;

class AppCommonModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $external_db         = \Config\Database::connect();
        $this->externaldb    = new externaldb();
        $this->univaictly    = $this->externaldb->univaictly_db();
		$this->aicountly_db  = $this->externaldb->aicountly_db();
		$this->sispluuid_db  = $this->externaldb->sispluuid_db();
		$this->pg_univaictlydb  = $this->externaldb->postgr_univaictlydb();
		$this->StockStatusModel =  new StockStatusModel();
		$this->LedgerModel      = new LedgerModel();  
		$this->AccountsModel    = new AccountsModel();
		$this->ItemSummaryModel = new ItemSummaryModel();
		$this->GstrReportModel  = new GstrReportModel();
		$this->ItemsModel       = new ItemsModel();
    }

   function login($postinfo)
		{				
		$builder     = $this->aicountly_db->table("aicountly_useraictly_univdb");
		$data        = $builder->where('LOWER(user_regdemail)', strtolower($postinfo['username']))->get()->getRowArray();
        if(empty($data)){
              $data  = $builder->where('LOWER(user_name)', strtolower($postinfo['username']))->get()->getRowArray();  
        }
		if($data){
			$pass          = $data['user_pass'];
			$password      = $postinfo['password'];
			$verify_pass   = password_verify($password, $pass);
			if($data['useraictly_status']!=1){
			    $return_data['success'] = '0';
				$return_data['message'] = 'Account is inactive';
				$return_data['user_verified'] = '0';				
				return $return_data;	
				
			}
			 else if($verify_pass){
				$return_data['success'] = '1';
				$return_data['message'] = 'Account verified';
				$return_data['user_verified'] = '1';
				
				$return_data['f_name'] = $data['user_firstname'];
                $return_data['m_name'] = $data['user_midname'];
				$return_data['l_name'] = $data['user_lastname'];
				
				$return_data['full_name'] = $data['user_firstname'].' '.$data['user_midname'].' '.$data['user_lastname'];
                $return_data['ses_comp_fy_id'] = 0; 
				
				$return_data['uuid']   = $data['uuid'];
				$return_data['email']  = $data['user_regdemail'];
                $return_data['phone']  = $data['user_regdmobile'];								
				return $return_data;	
				}
			  else{
				$return_data['success'] = '0';
				$return_data['message'] = 'Password is invalid';
				$return_data['user_verified'] = '0';
				return $return_data;
			   }			
		   }
           else{
				$return_data['success'] = '0';
				$return_data['message'] = 'User not found';
				$return_data['user_verified'] = '0';
				return $return_data;
			 }
		}
	public function GetInventoryStatus($compId, $bo_id,$fy_id,$inventoryData){
		return  $this->StockStatusModel->inventoryStatusPaged($compId, $bo_id,$fy_id,$inventoryData);
	}
	public function GetDayBook($compId, $bo_id,$fy_id,$reportData){
		return  $this->LedgerModel->load_day_book_condensed($compId, $bo_id,$fy_id,$reportData);
	}
	public function GetStockLedger($compId, $bo_id,$fy_id,$reportData){
		
		return  json_decode($this->ItemsModel->load_items_ledger($compId,$bo_id,$fy_id,$reportData,true));
	}
	public function GetStockSummary($compId, $bo_id,$fy_id,$reportData){
		
		return  $this->ItemSummaryModel->getMonthlySummary('',$compId,$bo_id,$fy_id,$reportData);
	}
	
	public function GetGSTSummary($compId, $bo_id,$fy_id,$reportData){
	    $view = $reportData['view'];
	    $summary_view = $reportData['summary_view'];
	    $from_date = $reportData['from_date'];
		  $to_date = $reportData['to_date'];
		  
		  if ($from_date == '') {
        $from_date = validate_fy_from_date($from_date);
        }
        if ($to_date == '') {
            $to_date = validate_fy_to_date($to_date);
        }
        
        $from_date_ymd = date('Y-m-d', strtotime($from_date));
        $to_date_ymd   = date('Y-m-d', strtotime($to_date));
    
     $gstsummary_table_lists    = array("otax"=>"OUTPUT TAX",
		                       "otax_cr"=>"OUTPUT TAX (CR. NOTE)",
		                         "total_otax"=>"TOTAL OUTPUT TAX",
								 ""=>"",
							   "itax"=>"INPUT TAX",
							   "itax_dr"=>"INPUT TAX (DR. NOTE)",
                               "total_itax"=>"TOTAL INPUT TAX"							   
							   );
		
	 if ($summary_view == 0 && $view == 1) 
        $response = $this->GstrReportModel->load_gstsummary_detailed($from_date_ymd, $to_date_ymd,$compId, $bo_id,$fy_id,$reportData);
    
    // Tax Summary - Condensed View
    else if ($summary_view == 0 && $view == 0) 
        $response = $this->GstrReportModel->load_gstsummary_condensed($from_date_ymd, $to_date_ymd,$gstsummary_table_lists,null, $compId, $bo_id,$fy_id,$reportData);
        
      return $response;  
	}
	
	public function GetAccountLedger($compId, $bo_id,$fy_id,$reportData){
		  $view = $reportData['view'];
		  $account_id = $reportData['acc_id'];
		  $from_date = $reportData['from_date'];
		  $to_date = $reportData['to_date'];
		 if ($view === 1) 
					$response = $this->AccountsModel->load_accounts_ledger_detailed($account_id, $from_date, $to_date, 0,1,$compId, $bo_id,$fy_id,$reportData);
		 elseif ($view === 0) 
			$response = $this->AccountsModel->load_accounts_ledger_condensed($account_id, $from_date, $to_date,0,1,$compId, $bo_id,$fy_id,$reportData);
		 elseif ($view === 3) {
			$response = json_decode($this->AccountsModel->load_accounts_ledger_condensed_with_columns(
				$account_id,
				$from_date,
				$to_date,
				0,
				1,$compId, $bo_id,$fy_id,$reportData
			),true);
		}
				
		
		return  $response;
	}
	
    public function token($uuid)
    {
        $token = "---" . $uuid . "---";
        return $token;
    }
	
	public function logoutByAuthToken(string $authToken): void
	{
		$now = date('Y-m-d H:i:s');

		// Fetch active auth token
		$auth = $this->pg_univaictlydb
			->table('aicauthtok')
			->where('aic_auth_token', $authToken)
			->where('aic_auth_login_state', 1)
			->get()
			->getRowArray();

		if (!$auth) {
			return;
		}

		// 1. Mark auth token as logged out
		$this->pg_univaictlydb
			->table('aicauthtok')
			->where('aic_auth_id', $auth['aic_auth_id'])
			->update([
				'aic_auth_login_state'   => 0,
				'aic_auth_last_activity' => $now
			]);

		// 2. Invalidate all active session keys
		$this->pg_univaictlydb
			->table('aicseskeyn')
			->where('aic_auth_id', $auth['aic_auth_id'])
			->where('aic_ses_state', 1)
			->update([
				'aic_ses_state' => 0
			]);
	}


	public function refreshSesKeyUsingAuthToken(string $authToken, string $ip): array
{
    $db = $this->pg_univaictlydb;
    $now = date('Y-m-d H:i:s');

    // 1️⃣ Validate auth token
    $auth = $db->table('aic_auth_token')
        ->where('aic_auth_token', $this->encryptValue($authToken))
        ->where('aic_auth_login_state', 1)
        ->where('aic_auth_expiry >=', $now)
        ->get()
        ->getRowArray();

    if (!$auth) {
        return [
            'status'  => 0,
            'message' => 'Session expired'
        ];
    }

    // 2️⃣ Extend auth token expiry (sliding window)
    $db->table('aic_auth_token')
        ->where('aic_auth_id', $auth['aic_auth_id'])
        ->update([
            'aic_last_activity' => $now,
            'aic_auth_expiry'   => date('Y-m-d H:i:s', strtotime('+24 hours')),
            'aic_last_ip'       => $ip
        ]);

    // 3️⃣ Generate new SES KEY
    $rawSesKey = bin2hex(random_bytes(32));
    $encSesKey = $this->encryptValue($rawSesKey);

    $db->table('aic_seskeyn')->insert([
        'aic_auth_id'        => $auth['aic_auth_id'],
        'aic_ses_key'        => $encSesKey,
        'aic_ses_expiry'     => date('Y-m-d H:i:s', strtotime('+15 minutes')),
        'aic_ses_created_at' => $now,
        'aic_ses_ip'         => $ip
    ]);

    return [
        'status'  => 1,
        'ses_key' => $encSesKey
    ];
}

	private function encryptValue(string $value): string
{
    $keyString = getenv('APP_ENC_KEY');
    if (!$keyString) {
        throw new \RuntimeException('APP_ENC_KEY not set');
    }

    $key = hash('sha256', $keyString, true); // 32 bytes
    $iv  = random_bytes(16);                 // 16 bytes for AES-256-CBC

    $cipherText = openssl_encrypt(
        $value,
        'AES-256-CBC',
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );

    if ($cipherText === false) {
        throw new \RuntimeException('Encryption failed');
    }

    // IV + ciphertext → base64
    return base64_encode($iv . $cipherText);
}
	
	
	private function decryptValue(string $value): string
{
    $keyString = getenv('APP_ENC_KEY');
    if (!$keyString) {
        throw new \RuntimeException('APP_ENC_KEY not set');
    }

    $key  = hash('sha256', $keyString, true);
    $data = base64_decode($value, true);

    if ($data === false || strlen($data) < 17) {
        return '';
    }

    $iv         = substr($data, 0, 16);
    $cipherText = substr($data, 16);

    $plainText = openssl_decrypt(
        $cipherText,
        'AES-256-CBC',
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );

    return $plainText === false ? '' : $plainText;
}


public function validateSesKey(string $sesKeyEnc): array
{
    $db = $this->pg_univaictlydb;

    /* --------------------------------
     * 1. Decrypt session key
     * -------------------------------- */
    $sesKeyRaw = $this->decryptValue($sesKeyEnc);
  

    if (!$sesKeyRaw) {
        return [
            'status'  => 0,
            'message' => 'Invalid session key'
        ];
    }

    /* --------------------------------
     * 2. Hash session key for lookup
     * -------------------------------- */
    $sesKeyHash = hash('sha256', $sesKeyRaw);

    /* --------------------------------
     * 3. Validate active session
     * -------------------------------- */
    $now = date('Y-m-d H:i:s');

    $ses = $db->table('aicseskeyn s')
        ->select('
            s.aic_ses_id,
            s.aic_auth_id,
            s.uuid_aictly,
            s.aic_ses_expiry,
            a.aic_auth_login_state,
            a.aic_auth_expiry
        ')
        ->join('aicauthtok a', 'a.aic_auth_id = s.aic_auth_id')
        ->where('s.aic_ses_key_hash', $sesKeyHash)
        ->where('s.aic_ses_state', 1)
        ->where('s.aic_ses_expiry >=', $now)
        ->where('a.aic_auth_login_state', 1)
        ->where('a.aic_auth_expiry >=', $now)
        ->orderBy('s.aic_ses_id', 'DESC')
        ->get()
        ->getRowArray();

    if (!$ses) {
        return [
            'status'  => 0,
            'message' => 'Session expired or invalid'
        ];
    }

    /* --------------------------------
     * 4. Extend auth activity + expiry
     * -------------------------------- */
    $db->table('aicauthtok')
        ->where('aic_auth_id', $ses['aic_auth_id'])
        ->update([
            'aic_auth_last_activity' => $now,
            'aic_auth_expiry'        => date('Y-m-d H:i:s', strtotime('+24 hours'))
        ]);

    /* --------------------------------
     * 5. Return valid context
     * -------------------------------- */
    return [
        'status'       => 1,
        'uuid_aictly'  => (int) $ses['uuid_aictly'],
        'aic_auth_id'  => (int) $ses['aic_auth_id'],
        'aic_ses_id'   => (int) $ses['aic_ses_id']
    ];
}

	public function generateSesKeyFromAuth(string $authToken): array
{

     $tokenHash = hash('sha256', $authToken);


    $auth = $this->pg_univaictlydb->table('aicauthtok')
       ->where('aic_auth_token_hash', $tokenHash)
    ->where('aic_auth_login_state', 1)
    ->where('aic_auth_expiry >=', date('Y-m-d H:i:s'))
    ->orderBy('aic_auth_id', 'DESC')
    ->get()
    ->getRowArray();
	//echo $this->pg_univaictlydb->getlastquery();	
	//die();

    if (!$auth) {
        return ['status' => 0, 'message' => 'Auth expired'];
    }

    // Extend auth expiry (sliding)
    $this->pg_univaictlydb->table('aicauthtok')
        ->where('aic_auth_id', $auth['aic_auth_id'])
        ->update([
            'aic_auth_last_activity' => date('Y-m-d H:i:s'),
            'aic_auth_expiry'        => date('Y-m-d H:i:s', strtotime('+24 hours'))
        ]);

    // Generate opaque session key
    $rawSesKey = bin2hex(random_bytes(32)); // 64 chars

    $encryptedSesKey = $this->encryptValue($rawSesKey);

	$sesKeyHash = hash('sha256', $rawSesKey);

    // Store in DB
    $this->pg_univaictlydb->table('aicseskeyn')->insert([
        'aic_auth_id'      => $auth['aic_auth_id'],
        'uuid_aictly'      => $auth['uuid_aictly'],
        'aic_ses_key'      => $encryptedSesKey,		
		'aic_ses_key_hash' => $sesKeyHash,
        'aic_ses_log_time' => date('Y-m-d H:i:s'),
        'aic_ses_expiry'   => date('Y-m-d H:i:s', strtotime('+15 minutes')),
        'aic_ses_log_ip'   => service('request')->getIPAddress(),
        'aic_ses_state'    => 1
    ]);

    return [
        'status'   => 1,
        'ses_key'  => $encryptedSesKey,
        'expires_in' => 900
    ];
}



    /**
     * Get the latest financial year info for a company.
     *
     * @param int $cmpId
     * @return array|null ['cmpfymastr_id','fy_beg_date','fy_end_date','def_val_method','cmp_id']
     */
    public function get_comp_fy_info(int $cmpId): ?array
    {
        $row = $this->db
            ->table('cmpfymastr')
            ->select('cmpfymastr_id, fy_beg_date, def_val_method, fy_end_date, cmp_id')
            ->where('cmp_id', $cmpId)
            ->orderBy('cmpfymastr_id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
 * Return companies accessible to the given user.
 * Supports optional pagination (page/perPage); defaults preserve previous behavior.
 */
public function user_company_list(string $uuid, int $page = 1, int $perPage = 0): array
{
    // normalize pagination
    $page     = max(1, $page);
    $perPage  = $perPage > 0 ? $perPage : 0; // 0 means "no pagination" (backward compatible)
    $offset   = $perPage > 0 ? ($page - 1) * $perPage : 0;

    $records = [
        'my'          => [],
        'shared'      => [],
        'all'         => [],
        'recycle_bin' => [],
        'meta'        => [
            'page'          => $page,
            'per_page'      => $perPage,
            'total_active'  => 0,
            'total_deleted' => 0,
            'total_pages'   => 0,
        ],
    ];

    // Access rows
    $acsRows = $this->univaictly
        ->table('cmpacsmstr')
        ->select('cmp_id, uuid_aictly_by, uuid_aictly_acs, uuid_acs_type')
        ->groupStart()
        ->where('uuid_aictly_by', $uuid)
        ->orWhere('uuid_aictly_acs', $uuid)
        ->groupEnd()
        ->get()
        ->getResultArray();

    if (empty($acsRows)) {
        return $records;
    }

    // Index access rows by cmp_id (owner wins)
    $acsIndexed = [];
    foreach ($acsRows as $row) {
        $cmpId = (int) $row['cmp_id'];
        if (!isset($acsIndexed[$cmpId]) || (int) $row['uuid_acs_type'] === 1) {
            $acsIndexed[$cmpId] = $row;
        }
    }
    $cmpIds = array_keys($acsIndexed);
    if (empty($cmpIds)) {
        return $records;
    }

    // Helper: fetch companies by status with optional pagination
    $fetchCompanies = function (array $ids, int $status, int $perPage, int $offset): array {
        if (empty($ids)) {
            return ['rows' => [], 'total' => 0];
        }

        $builder = $this->db
            ->table('cmpmastern AS cmp')
            ->select('cmp.cmp_id, cmp.cmp_name, cmp.cmp_short_name, cmp.cmp_status')
            ->where('cmp.cmp_status', $status)
            ->whereIn('cmp.cmp_id', $ids);

        // Total first
        $total = $builder->countAllResults(false);

        // Apply pagination only if perPage > 0
        if ($perPage > 0) {
            $builder->orderBy('cmp.cmp_name', 'ASC')->limit($perPage, $offset);
        } else {
            $builder->orderBy('cmp.cmp_name', 'ASC');
        }

        $rows = $builder->get()->getResultArray();

        return ['rows' => $rows, 'total' => $total];
    };

    // Active companies
    $activeResult    = $fetchCompanies($cmpIds, 1, $perPage, $offset);
    $activeCompanies = $activeResult['rows'];
    $records['meta']['total_active'] = $activeResult['total'];

    foreach ($activeCompanies as $cmp) {
        $cmpId     = (int) $cmp['cmp_id'];
        $acs       = $acsIndexed[$cmpId] ?? null;
        $ownType   = $acs ? (int) $acs['uuid_acs_type'] : 0; // 1 owner, 0 shared
        $ownerUuid = $acs ? $acs['uuid_aictly_by'] : null;
        $ownership = $ownType === 1 ? 'owner' : 'shared';

        $finyear = '';
        if (method_exists($this, 'get_comp_fy_info')) {
            $fyInfo = $this->get_comp_fy_info($cmpId);
            if ($fyInfo) {
                $finyear = date('d-m-Y', strtotime($fyInfo['fy_beg_date'])) .
                    ' -- ' .
                    date('d-m-Y', strtotime($fyInfo['fy_end_date']));
            }
        }

        $row = [
            'ownership'       => $ownership,
            'comp_type'       => 'cmp',
            'comp_status'     => (int) $cmp['cmp_status'],
            'comp_id'         => $cmpId,
            'company_name'    => $cmp['cmp_name'],
            'comp_short_name' => $cmp['cmp_short_name'] ?: $cmp['cmp_name'],
            'comp_code'       => erp_compcode_format($cmpId),
            'finyear'         => $finyear,
            'uuid_aicountly'  => $ownerUuid,
        ];

        if ($ownership === 'owner') {
            $records['my'][] = $row;
        } else {
            $records['shared'][] = $row;
        }
        $records['all'][] = $row;
    }

    // Recycle bin (soft-deleted)
    $deletedResult    = $fetchCompanies($cmpIds, 0, $perPage, $offset);
    $deletedCompanies = $deletedResult['rows'];
    $records['meta']['total_deleted'] = $deletedResult['total'];

    // Fetch recycle dates for only the deleted companies we are returning now
    $deletedIds = array_map(static fn ($r) => (int) $r['cmp_id'], $deletedCompanies);

    $recycleMap = [];
    if (!empty($deletedIds)) {
        // Get the latest recycle date per company
        // SELECT cmp_id, MAX(cmp_recycle_date) AS recycle_date FROM cmprecycle WHERE cmp_id IN (...) GROUP BY cmp_id
        $rowsRecycle = $this->db->table('cmprecycle')
            ->select('cmp_id, MAX(cmp_recycle_date) AS recycle_date', false)
            ->whereIn('cmp_id', $deletedIds)
            ->groupBy('cmp_id')
            ->get()
            ->getResultArray();

        foreach ($rowsRecycle as $r) {
            $cid = (int) $r['cmp_id'];
            $recycleMap[$cid] = $r['recycle_date']; // keep raw; format when assigning
        }
    }

    foreach ($deletedCompanies as $cmp) {
        $cmpId     = (int) $cmp['cmp_id'];
        $acs       = $acsIndexed[$cmpId] ?? null;
        $ownType   = $acs ? (int) $acs['uuid_acs_type'] : 0;
        $ownerUuid = $acs ? $acs['uuid_aictly_by'] : null;
        $ownership = $ownType === 1 ? 'owner' : 'shared';

        $rawRecycle = $recycleMap[$cmpId] ?? null;
        $recycleFmt = $rawRecycle ? date('d-m-Y', strtotime($rawRecycle)) : '';

        $row = [
            'ownership'       => $ownership,
            'comp_type'       => 'cmp',
            'comp_status'     => (int) $cmp['cmp_status'], // 0 = soft deleted
            'comp_id'         => $cmpId,
            'company_name'    => $cmp['cmp_name'],
            'comp_short_name' => $cmp['cmp_short_name'] ?: $cmp['cmp_name'],
            'comp_code'       => erp_compcode_format($cmpId),
            'finyear'         => '',
            'uuid_aicountly'  => $ownerUuid,
            'recycle_date'    => $recycleFmt,
        ];
        $records['recycle_bin'][] = $row;
    }

    // total pages (based on active list)
    if ($perPage > 0) {
        $records['meta']['total_pages'] = (int) ceil($records['meta']['total_active'] / $perPage);
    }

    return $records;
}


public function updateCompany(int $company_id, array $data, string $uuid): array
{
    $this->db->transBegin();

    try {

        /* -------------------------------------------------
         * 1. Company Exists Check
         * ------------------------------------------------- */
        $company = $this->db->table('cmpmastern')
            ->where('cmp_id', $company_id)
            ->get()
            ->getRowArray();

        if (!$company) {
            return ['status' => 0, 'message' => 'Company not found'];
        }

        /* -------------------------------------------------
         * 2. Duplicate Name Check (Exclude Self)
         * ------------------------------------------------- */
        if (!empty($data['comp_name'])) {
            $dup = $this->db->table('cmpmastern')
                ->where('LOWER(cmp_name)', strtolower(trim($data['comp_name'])))
                ->where('cmp_id !=', $company_id)
                ->get()
                ->getRowArray();

            if ($dup) {
                return ['status' => 0, 'message' => 'Company name already exists'];
            }
        }

        /* -------------------------------------------------
         * 3. Update Company Master
         * ------------------------------------------------- */
        $this->db->table('cmpmastern')
            ->where('cmp_id', $company_id)
            ->update([
                'cmp_name'          => $data['comp_name'],
                'cmp_print_name'    => $data['print_name'],
                'cmp_short_name'    => $data['short_name'],
                'cmp_last_accessed' => date('Y-m-d H:i:s')
            ]);

        /* -------------------------------------------------
         * 4. Registered Office Address
         * ------------------------------------------------- */
        $this->db->table('cmpaddrmst')
            ->where('cmp_id', $company_id)
            ->where('cmp_addr_type', 1)
            ->update([
                'cmp_addr1'   => $data['ro_adrs1'] ?? null,
                'cmp_addr2'   => $data['ro_adrs2'] ?? null,
                'cmp_city'    => $data['ro_city'],
                'cmp_state'   => $data['ro_state'],
                'cmp_pin_zip' => $data['ro_pin'],
                'cmp_country' => $data['ro_country']
            ]);

        /* -------------------------------------------------
         * 5. Corporate Office Address
         * ------------------------------------------------- */
        $this->db->table('cmpaddrmst')
            ->where('cmp_id', $company_id)
            ->where('cmp_addr_type', 2)
            ->update([
                'cmp_addr1'   => $data['co_adrs1'] ?? null,
                'cmp_addr2'   => $data['co_adrs2'] ?? null,
                'cmp_city'    => $data['co_city'],
                'cmp_state'   => $data['co_state'],
                'cmp_pin_zip' => $data['co_pin'],
                'cmp_country' => $data['co_country']
            ]);

        /* -------------------------------------------------
         * 6. Company Detail (Industry / Nature)
         * ------------------------------------------------- */
        $detRow = $this->db->table('cmpmstdetn')
            ->where('cmp_id', $company_id)
            ->get()
            ->getRowArray();

        $detailData = [
            'cmp_industry'    => $data['industry_type'] ?? null,
            'cmp_work_nature' => $data['nature_of_work'] ?? null
        ];

        if ($detRow) {
            $this->db->table('cmpmstdetn')
                ->where('cmp_id', $company_id)
                ->update($detailData);
        } else {
            $detailData['cmp_id'] = $company_id;
            $this->db->table('cmpmstdetn')->insert($detailData);
        }

        /* -------------------------------------------------
         * 7. Financial Year & Valuation Method
         * ------------------------------------------------- */
        $fy_start = date('Y-m-d', strtotime($data['fy_start']));
        $fy_end   = date('Y-m-d', strtotime($data['fy_end']));

        $valMap = ['FIFO' => 1, 'LIFO' => 2, 'AVG' => 3];
        $valmethod_id = $valMap[$data['default_stock']] ?? 1;

        $this->db->table('cmpfymastr')
            ->where('cmp_id', $company_id)
            ->where('fy_beg_date', $fy_start)
            ->update([
                'fy_end_date'    => $fy_end,
                'def_val_method' => $valmethod_id
            ]);

        /* -------------------------------------------------
         * 8. Commit
         * ------------------------------------------------- */
        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Update failed');
        }

        $this->db->transCommit();

        return [
            'status'     => 1,
            'company_id' => $company_id,
            'message'    => 'Company updated successfully'
        ];

    } catch (\Throwable $e) {

        $this->db->transRollback();

        return [
            'status'  => 0,
            'message' => $e->getMessage()
        ];
    }
}


   public function insertCompany(array $data, string $uuid): array
	{
		$this->db->transBegin();

		try {

			/* ---------------------------------------------------------
			 * 1. Duplicate Company Name Check
			 * --------------------------------------------------------- */
			$exists = $this->db->table('cmpmastern')
				->where('LOWER(cmp_name)', strtolower(trim($data['comp_name'])))
				->get()
				->getRowArray();

			if ($exists) {
				return [
					'status'  => 0,
					'message' => 'Company name already exists'
				];
			}

			/* ---------------------------------------------------------
			 * 2. Company Master
			 * --------------------------------------------------------- */
			$this->db->table('cmpmastern')->insert([
				'cmp_name'          => $data['comp_name'],
				'cmp_print_name'    => $data['print_name'],
				'cmp_short_name'    => $data['short_name'],
				'cmp_status'        => 1,
				'cmp_last_accessed' => date('Y-m-d H:i:s')
			]);

			$company_id = (int) $this->db->insertID();

			/* ---------------------------------------------------------
			 * 3. Company Access (Owner)
			 * --------------------------------------------------------- */
			$this->univaictly->table('cmpacsmstr')->insert([
				'cmp_id'            => $company_id,
				'uuid_acs_type'     => 1,
				'uuid_aictly_acs'   => null,
				'uuid_aictly_by'    => $uuid,
				'uuid_acs_datetime' => date('Y-m-d H:i:s')
			]);

			/* ---------------------------------------------------------
			 * 4. Registered Office Address
			 * --------------------------------------------------------- */
			$this->db->table('cmpaddrmst')->insert([
				'cmp_id'        => $company_id,
				'cmp_addr_type' => 1,
				'cmp_addr1'     => $data['ro_adrs1'] ?? null,
				'cmp_addr2'     => $data['ro_adrs2'] ?? null,
				'cmp_city'      => $data['ro_city'],
				'cmp_state'     => $data['ro_state'],
				'cmp_pin_zip'   => $data['ro_pin'],
				'cmp_country'   => $data['ro_country']
			]);

			/* ---------------------------------------------------------
			 * 5. Corporate Office Address
			 * --------------------------------------------------------- */
			$this->db->table('cmpaddrmst')->insert([
				'cmp_id'        => $company_id,
				'cmp_addr_type' => 2,
				'cmp_addr1'     => $data['co_adrs1'] ?? null,
				'cmp_addr2'     => $data['co_adrs2'] ?? null,
				'cmp_city'      => $data['co_city'],
				'cmp_state'     => $data['co_state'],
				'cmp_pin_zip'   => $data['co_pin'],
				'cmp_country'   => $data['co_country']
			]);

			/* ---------------------------------------------------------
			 * 6. Company Details
			 * --------------------------------------------------------- */
			if (!empty($data['industry_type']) || !empty($data['nature_of_work'])) {
				$this->db->table('cmpmstdetn')->insert([
					'cmp_id'          => $company_id,
					'cmp_industry'    => $data['industry_type'] ?? null,
					'cmp_work_nature' => $data['nature_of_work'] ?? null
				]);
			}

			/* ---------------------------------------------------------
			 * 7. Financial Year
			 * --------------------------------------------------------- */
			$fy_start = date('Y-m-d', strtotime($data['fy_start']));
			$fy_end   = date('Y-m-d', strtotime($data['fy_end']));


			$valmethod_id = $data['default_stock'] ?? 'AVG';

			$this->db->table('cmpfymastr')->insert([
				'cmp_id'         => $company_id,
				'fy_beg_date'    => $fy_start,
				'fy_end_date'    => $fy_end,
				'def_val_method' => $valmethod_id,
				'is_imported'    => 1
			]);

			$fy_id = (int)$this->db->insertID();

			/* ---------------------------------------------------------
			 * 8. Head Office (HO)
			 * --------------------------------------------------------- */
			$this->db->table('hobomaster')->insert([
				'cmp_id'       => $company_id,
				'mark_ho'      => 1,
				'hobo_name'    => 'HO',
				'hobo_alias'   => 'HO',
				'hobo_op_date' => $fy_start,
				'hobo_cl_date' => $fy_end
			]);

			/* =================================================
			 * 9. SYSTEM ACCOUNTS & GROUPS
			 * ================================================= */

			/* ---------- GST PAID A/C ---------- */
			$GST_NAME = 'GST PAID A/C';

			$accRow = $this->db->table('acctmaster')
				->where('cmp_id', $company_id)
				->where('UPPER(acc_name)', strtoupper($GST_NAME))
				->get()
				->getRowArray();

			if (!$accRow) {
				$this->db->table('acctmaster')->insert([
					'cmp_id' => $company_id,
					'acc_name' => $GST_NAME,
					'acc_alias' => $GST_NAME,
					'acc_print_name' => $GST_NAME,
					'acc_is_active' => 1,
					'acc_is_restrict' => 2
				]);
				$acc_id = $this->db->insertID();
			} else {
				$acc_id = $accRow['acc_id'];
			}

			$exists = $this->db->table('undercrsmt')
				->where('cmp_id', $company_id)
				->where('crs_mst_type', 1)
				->where('crs_mst_id', $acc_id)
				->get()->getRowArray();

			if (!$exists) {
				$this->db->table('undercrsmt')->insert([
					'cmp_id' => $company_id,
					'crs_mst_type' => 1,
					'crs_mst_id' => $acc_id,
					'under_crs_mst_id' => 0,
					'crs_mst_parent_id' => 13,
					'under_main_id' => 0,
					'crs_mst_is_primary' => 1,
					'cmpfymastr_id' => $fy_id,
					'crs_is_active' => 1
				]);
			}

			/* ---------- Reserves & Surplus Group ---------- */
			$GROUP_NAME = 'Reserves & Surplus';

			$grpRow = $this->db->table('accgrpmstn')
				->where('cmp_id', $company_id)
				->where('UPPER(acc_grp_name)', strtoupper($GROUP_NAME))
				->get()->getRowArray();

			if (!$grpRow) {
				$this->db->table('accgrpmstn')->insert([
					'cmp_id' => $company_id,
					'acc_grp_name' => $GROUP_NAME,
					'acc_grp_alias' => $GROUP_NAME,
					'acc_grp_is_active' => 1
				]);
				$groupId = $this->db->insertID();
			} else {
				$groupId = $grpRow['acc_grp_id'];
			}

			$this->db->table('undercrsmt')->insert([
				'cmp_id' => $company_id,
				'crs_mst_type' => 2,
				'crs_mst_id' => $groupId,
				'under_crs_mst_id' => 0,
				'crs_mst_parent_id' => 1,
				'under_main_id' => 1,
				'crs_mst_is_primary' => 1,
				'cmpfymastr_id' => $fy_id,
				'crs_is_active' => 1
			]);

			/* ---------- Profit & Loss Appropriation ---------- */
			$ACC_NAME = 'Profit & Loss Appropriation';

			$accRow = $this->db->table('acctmaster')
				->where('cmp_id', $company_id)
				->where('UPPER(acc_name)', strtoupper($ACC_NAME))
				->get()->getRowArray();

			if (!$accRow) {
				$this->db->table('acctmaster')->insert([
					'cmp_id' => $company_id,
					'acc_name' => $ACC_NAME,
					'acc_alias' => $ACC_NAME,
					'acc_print_name' => $ACC_NAME,
					'acc_is_active' => 1,
					'acc_is_restrict' => 3
				]);
				$acc_id = $this->db->insertID();
			} else {
				$acc_id = $accRow['acc_id'];
			}

			$this->db->table('undercrsmt')->insert([
				'cmp_id' => $company_id,
				'crs_mst_type' => 1,
				'crs_mst_id' => $acc_id,
				'under_crs_mst_id' => $groupId,
				'crs_mst_parent_id' => 1,
				'under_main_id' => $groupId,
				'crs_mst_is_primary' => 0,
				'cmpfymastr_id' => $fy_id,
				'crs_is_active' => 1
			]);

			/* -------------------------------------------------
			 * 10. Voucher Series
			 * ------------------------------------------------- */
			$this->db->table('vchseriesn')->insert([
				'cmp_id' => $company_id,
				'vch_series_name' => 'Main',
				'vch_series_method' => 0,
				'bank_id' => 0,
				'vch_type_id' => 23
			]);

			/* ---------------------------------------------------------
			 * Commit
			 * --------------------------------------------------------- */
			if ($this->db->transStatus() === false) {
				return [
				'status'     => 0,
				'company_id' => 0,
				'message'    => 'Transaction failed'
			];
			}

			$this->db->transCommit();

			return [
				'status'     => 1,
				'company_id' => $company_id,
				'message'    => 'Company created successfully'
			];

		 } catch (\Throwable $e) {

			$this->db->transRollback();

			return [
				'status'  => 0,
				'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()
			];
		  } 
	}
   
   public function CompanyInfo($cmpId, $uuid = null): array
	{
		$cmpId = (int) $cmpId;
		if ($cmpId <= 0) {
			return ['success' => '0', 'message' => 'Invalid company id'];
		}

		$mst = $this->db->table('cmpmastern')
				->select('*')
				->where('cmp_id', $cmpId)
				->get()
				->getRowArray() ?? [];

		// 1) Company details (ids, CIN/PAN, industry, nature, etc.)
		$det = $this->db->table('cmpmstdetn')
			->select('*')
			->where('cmp_id', $cmpId)
			->get()
			->getRowArray() ?? [];

		// 2) FY row: prefer one covering today; else latest by fy_beg_date
		$today = date('Y-m-d');
		$fy = $this->db->table('cmpfymastr')
			->where('cmp_id', $cmpId)
			->where('fy_beg_date <=', $today)
			->where('fy_end_date >=', $today)
			->orderBy('fy_beg_date', 'DESC')
			->get()
			->getRowArray();
		if (!$fy) {
			$fy = $this->db->table('cmpfymastr')
				->where('cmp_id', $cmpId)
				->orderBy('fy_beg_date', 'DESC')
				->get()
				->getRowArray();
		}
		$fy = $fy ?? [];

		// 3) Addresses: 1=Regd Office, 2=Corporate Office
		$addrRows = $this->db->table('cmpaddrmst')
			->select('*')
			->where('cmp_id', $cmpId)
			->whereIn('cmp_addr_type', [1, 2])
			->get()
			->getResultArray();

		$addrByType = [];
		foreach ($addrRows as $a) {
			$addrByType[(int)$a['cmp_addr_type']] = $a;
		}
		$ro = $addrByType[1] ?? []; // Registered Office
		$co = $addrByType[2] ?? []; // Corporate Office

		// 4) Company All FY and Branches Lists (formatted)
		$fyRows = $this->db->table('cmpfymastr')
			->select('cmpfymastr_id, fy_beg_date, fy_end_date, def_val_method')
			->where('cmp_id', $cmpId)
			->orderBy('fy_beg_date', 'DESC')
			->get()
			->getResultArray();
		$allFy = [];
		foreach ($fyRows as $r) {
			$beg = $r['fy_beg_date'] ?? '';
			$end = $r['fy_end_date'] ?? '';
			$begYear = $beg ? (int)date('Y', strtotime($beg)) : 0;
			$endYear = $end ? (int)date('Y', strtotime($end)) : 0;
			$short = ($begYear && $endYear)
				? $begYear . ' - ' . substr((string)$endYear, -2)
				: '';
			$full = ($beg && $end)
				? date('d M, Y', strtotime($beg)) . ' - ' . date('d M, Y', strtotime($end))
				: '';
			$allFy[] = [
				'fy_id'     => (int)($r['cmpfymastr_id'] ?? 0),
				'fy_short'  => $short,   // e.g. 2024 - 25
				'fy_full'   => $full,    // e.g. 01 Apr, 2024 - 31 Mar, 2025
				'fy_start'  => $beg,
				'fy_end'    => $end,
				'def_val_method' => $r['def_val_method'] ?? '',
			   ];
		   }

		$branchRows = $this->db->table('hobomaster')
					->select('hobo_id, hobo_name')
					->where('cmp_id', $cmpId)
					->orderBy('hobo_name', 'ASC')
					->get()
					->getResultArray();

				$branches = [];
				foreach ($branchRows as $b) {
					$branches[] = [
						'id'   => (int)$b['hobo_id'],
						'name' => (string)$b['hobo_name'],
					];
				}

		// Optional: try to read names from cmpmstdetn if present
		$compName   = $mst['cmp_name']        ?? ($mst['cmp_print_name'] ?? '');
		$printName  = $mst['cmp_print_name']  ?? ($mst['cmp_name'] ?? '');
		$shortName  = $mst['cmp_short_name']  ?? '';

		$industry    = $this->mapIndustry($det['cmp_industry'] ?? null);
		$workNature  = $this->mapWorkNature($det['cmp_work_nature'] ?? null);

		$data = [
			'comp_name'      => (string) $compName,
			'comp_id'        => $cmpId,
			'print_name'     => (string) $printName,
			'short_name'     => (string) $shortName,
			'fy_start'       => (string) ($fy['fy_beg_date'] ?? ''),
			'fy_end'         => (string) ($fy['fy_end_date'] ?? ''),
			'default_stock'  => (string) ($fy['def_val_method'] ?? ''),
			'industry_type'  => $industry,
			'nature_of_work' => $workNature,			
			'ro_adrs1'       => (string) ($ro['cmp_addr1'] ?? ''),
			'ro_adrs2'       => (string) ($ro['cmp_addr2'] ?? ''),
			'ro_country'     => (string) ($ro['cmp_country'] ?? ''),
			'ro_state'       => (string) ($ro['cmp_state'] ?? ''),
			'ro_city'        => (string) ($ro['cmp_city'] ?? ''),
			'ro_pin'         => (string) ($ro['cmp_pin_zip'] ?? ''),			
			'co_adrs1'       => (string) ($co['cmp_addr1'] ?? ''),
			'co_adrs2'       => (string) ($co['cmp_addr2'] ?? ''),
			'co_country'     => (string) ($co['cmp_country'] ?? ''),
			'co_state'       => (string) ($co['cmp_state'] ?? ''),
			'co_city'        => (string) ($co['cmp_city'] ?? ''),
			'co_pin'         => (string) ($co['cmp_pin_zip'] ?? ''),			
			'fy_list'        => $allFy,
			'branch_list'    => $branches,
		];

		return [
			'success' => '1',
			'data'    => $data,
		];
	}

    // Simple label mappers (adjust to your reference tables if needed)
    private function mapIndustry($id)
    {
        if ($id === null || $id === '') return '';
        $map = [
            1 => 'Manufacturing',
            2 => 'Trading',
            3 => 'Services',
            4 => 'Trading and Services',
        ];
        return $map[(int)$id] ?? (string)$id;
    }

    private function mapWorkNature($id)
    {
        if ($id === null || $id === '') return '';
        $map = [
            1 => 'Manufacturing',
            2 => 'Trading',
            3 => 'Services',
            4 => 'Trading and Services',
        ];
        return $map[(int)$id] ?? (string)$id;
    }
   
   function UserProfile($id){
	        $data = [];
           
            $builder = $this->aicountly_db->table('aicountly_useraictly_univdb');
            $builder->select('*');
            $builder->where('uuid', $id);
            $result = $builder->get()->getRowArray();

            $builder = $this->aicountly_db->table('aicountly_useraicdet_univdb');
            $builder->select('*');
            $builder->select('countryname, state_name');
            $builder->join('aicountly_countrylst_univdb', 'aicountly_countrylst_univdb.countryid = aicountly_useraicdet_univdb.user_country', 'left');
            $builder->join('aicountly_stateslist_univdb', 'aicountly_stateslist_univdb.state_id = aicountly_useraicdet_univdb.user_state', 'left');
            $builder->where('uuid', $id);
            $result2 = $builder->get()->getRowArray();
            $data = [
                'uuid'              => $id,
                'user_firstname'    => $result['user_firstname'] ?? '',
                'user_midname'      => $result['user_midname'] ?? '',
                'user_lastname'     => $result['user_lastname'] ?? '',
                'user_name'         => $result['user_name'] ?? '',
                'user_pass'         => $result['user_pass'] ?? '',
                'user_regdmobile'   => $result['user_regdmobile'] ?? '',
                'user_regdemail'    => $result['user_regdemail'] ?? '',
                'user_wamobile'     => $result['user_wamobile'] ?? '',
                'user_aicountly_business_id' => $result['user_aicountly_business_id'] ?? '',
                'user_pin'          => $result['user_pin'] ?? '',
                'user_gender'       => $result['user_gender'] ?? '',
                'user_type_profs'   => $result['user_type_profs'] ?? '',
                'user_dob'          => $result['user_dob'] ?? '',
                'user_dob'          => !empty($result['user_dob']) ? date('d M, Y', strtotime($result['user_dob'])) : '',
                'user_email2'       => $result2['user_email2'] ?? '',
                'user_mobile2'      => $result2['user_mobile2'] ?? '',
                'user_landline'     => $result2['user_landline'] ?? '',
                'user_website'      => $result2['user_website'] ?? '',
                'user_add1'         => $result2['user_add1'] ?? '',
                'user_add2'         => $result2['user_add2'] ?? '',
                'user_city'         => $result2['user_city'] ?? '',
                'user_state'        => $result2['user_state'] ?? '',
                'user_country'      => $result2['user_country'] ?? '',
                'state_name'        => $result2['state_name'] ?? '',
                'countryname'       => $result2['countryname'] ?? '',
				'dpurl'             => base_url().'user/logo'
             ];	
		if($data){
		 $return_data['success'] = '1';
		 $return_data['data']   = $data;	
		}
		else{
		$return_data['success'] = '0';
		$return_data['message'] = 'Something went wrong';	
		}
	return $return_data;	
	}


public function softDeleteCompany(int $cmpid, string $uuid): array
{
    $this->db->transBegin();

    try {

        /* ---------------------------------------------
         * 1. Company Exists Check
         * --------------------------------------------- */
        $company = $this->db->table('cmpmastern')
            ->select('cmp_id, cmp_status')
            ->where('cmp_id', $cmpid)
            ->get()
            ->getRowArray();

        if (!$company) {
            return [
                'success' => '0',
                'message' => 'Company not found'
            ];
        }

        /* ---------------------------------------------
         * 2. Access Validation (Owner / Shared)
         * --------------------------------------------- */
        $hasAccess = $this->univaictly->table('cmpacsmstr')
            ->where('cmp_id', $cmpid)
            ->groupStart()
                ->where('uuid_aictly_by', $uuid)
                ->orWhere('uuid_aictly_acs', $uuid)
            ->groupEnd()
            ->get()
            ->getRowArray();

        if (!$hasAccess) {
            return [
                'success' => '0',
                'message' => 'Unauthorized access'
            ];
        }

        /* ---------------------------------------------
         * 3. Already Deleted?
         * --------------------------------------------- */
        if ((int)$company['cmp_status'] === 0) {
            return [
                'success' => '0',
                'message' => 'Company already in recycle bin'
            ];
        }

        /* ---------------------------------------------
         * 4. Soft Delete Company
         * --------------------------------------------- */
        $this->db->table('cmpmastern')
            ->where('cmp_id', $cmpid)
            ->update([
                'cmp_status' => 0
            ]);

        /* ---------------------------------------------
         * 5. Insert into cmprecycle (if not exists)
         * --------------------------------------------- */
        $existsRecycle = $this->db->table('cmprecycle')
            ->where('cmp_id', $cmpid)
            ->get()
            ->getRowArray();

        if (!$existsRecycle) {
            $this->db->table('cmprecycle')->insert([
                'cmp_id'           => $cmpid,
                'cmp_recycle_date' => date('Y-m-d')
            ]);
        }

        /* ---------------------------------------------
         * Commit
         * --------------------------------------------- */
        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Soft delete failed');
        }

        $this->db->transCommit();

        return [
            'success' => '1',
            'message' => 'Company moved to recycle bin'
        ];

    } catch (\Throwable $e) {

        $this->db->transRollback();

        return [
            'success' => '0',
            'message' => $e->getMessage()
        ];
    }
}

public function restoreCompany(int $cmpid, string $uuid): array
{
    $this->db->transBegin();

    try {

        /* ---------------------------------------------
         * 1. Company Exists Check
         * --------------------------------------------- */
        $company = $this->db->table('cmpmastern')
            ->select('cmp_id, cmp_status')
            ->where('cmp_id', $cmpid)
            ->get()
            ->getRowArray();

        if (!$company) {
            return [
                'success' => '0',
                'message' => 'Company not found'
            ];
        }

        /* ---------------------------------------------
         * 2. Access Validation
         * --------------------------------------------- */
        $hasAccess = $this->univaictly->table('cmpacsmstr')
            ->where('cmp_id', $cmpid)
            ->groupStart()
                ->where('uuid_aictly_by', $uuid)
                ->orWhere('uuid_aictly_acs', $uuid)
            ->groupEnd()
            ->get()
            ->getRowArray();

        if (!$hasAccess) {
            return [
                'success' => '0',
                'message' => 'Unauthorized access'
            ];
        }

        /* ---------------------------------------------
         * 3. Check if Company is in Recycle Bin
         * --------------------------------------------- */
        if ((int) $company['cmp_status'] === 1) {
            return [
                'success' => '0',
                'message' => 'Company is already active'
            ];
        }

        $recycleRow = $this->db->table('cmprecycle')
            ->where('cmp_id', $cmpid)
            ->get()
            ->getRowArray();

        if (!$recycleRow) {
            return [
                'success' => '0',
                'message' => 'Recycle record not found'
            ];
        }

        /* ---------------------------------------------
         * 4. Restore Company (Activate)
         * --------------------------------------------- */
        $this->db->table('cmpmastern')
            ->where('cmp_id', $cmpid)
            ->update([
                'cmp_status' => 1
            ]);

        /* ---------------------------------------------
         * 5. Remove from Recycle Table
         * --------------------------------------------- */
        $this->db->table('cmprecycle')
            ->where('cmp_id', $cmpid)
            ->delete();

        /* ---------------------------------------------
         * Commit
         * --------------------------------------------- */
        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Restore failed');
        }

        $this->db->transCommit();

        return [
            'success' => '1',
            'message' => 'Company restored successfully'
        ];

    } catch (\Throwable $e) {

        $this->db->transRollback();

        return [
            'success' => '0',
            'message' => $e->getMessage()
        ];
    }
}

public function getCompanyActivities(int $cmpId,$uuid, int $page = 1, int $perPage = 20): array
{
    $page     = max(1, $page);
    $perPage  = max(1, $perPage);
    $offset   = ($page - 1) * $perPage;

    /* -------------------------------------------------
     * 1. Validate company exists
     * ------------------------------------------------- */
    $exists = $this->db->table('cmpmastern')
        ->select('cmp_id')
        ->where('cmp_id', $cmpId)
        ->get()
        ->getRowArray();

    if (!$exists) {
        return [
            'success' => '0',
            'message' => 'Company not found',
            'data'    => [],
            'meta'    => []
        ];
    }

    /* -------------------------------------------------
     * 2. Total count
     * ------------------------------------------------- */
    $total = $this->db->table('erpactivty')
        ->where('cmp_id', $cmpId)
		 ->where('uuid', $uuid)
        ->countAllResults();

    /* -------------------------------------------------
     * 3. Fetch paginated activity
     * ------------------------------------------------- */
    $rows = $this->db->table('erpactivty')
        ->select([
            'erp_activity_id',
            'cmp_id',
            'uuid',
            'erp_activity_date_time',
            'erp_activity_log',
            'vch_txn_id',
            'txn_id'
        ])
        ->where('cmp_id', $cmpId)
		->where('uuid', $uuid)
        ->orderBy('erp_activity_date_time', 'DESC')
        ->limit($perPage, $offset)
        ->get()
        ->getResultArray();

    /* -------------------------------------------------
     * 4. Response
     * ------------------------------------------------- */
    return [
        'success' => '1',
        'data'    => $rows,
        'page'        => $page,
        'per_page'    => $perPage,
        'total'       => $total,
        'total_pages' => (int) ceil($total / $perPage)
        
    ];
}

public function GetBusinessId($uuid){
	$builder = $this->sispluuid_db->table('sispluuid_bussisplnn_univdb');
        $builder->join('sispluuid_busuuidmap_univdb','sispluuid_busuuidmap_univdb.business_id=sispluuid_bussisplnn_univdb.business_id');
        $builder->select('sispluuid_bussisplnn_univdb .*');
        $builder->where('sispluuid_busuuidmap_univdb.uuid', $uuid);
        $builder->where('sispluuid_busuuidmap_univdb.project_id',1);
        $result = $builder->get()->getRowArray();
		
		if (!$result) {
            return [
                'success' => '0',
                'message' => 'No Result Found',
				'data'    =>[]
            ];
        }else{
			return [
                'success' => 1,
                'message' => 'Result Found',
				'data'    => $result['business_id']
            ];
		}
}

public function hardDeleteCompany(int $cmpid, string $uuid): array
{
    $this->db->transBegin();

    try {

        /* -------------------------------------------------
         * 1. Validate company existence & recycle state
         * ------------------------------------------------- */
        $company = $this->db->table('cmpmastern')
            ->select('cmp_id, cmp_status')
            ->where('cmp_id', $cmpid)
            ->get()
            ->getRowArray();

        if (!$company) {
            return [
                'success' => '0',
                'message' => 'Company not found'
            ];
        }

        if ((int)$company['cmp_status'] !== 0) {
            return [
                'success' => '0',
                'message' => 'Company must be in recycle bin before permanent deletion'
            ];
        }

        /* -------------------------------------------------
         * 3. Remove recycle entry
         * ------------------------------------------------- */
        $this->db->table('cmprecycle')
            ->where('cmp_id', $cmpid)
            ->delete();

        /* =================================================
         * 4. DELETE DEPENDENT DATA (ORDER IS IMPORTANT)
         * ================================================= */

        /* ---------- Accounting ---------- */
        $this->db->table('acctamtinc')->where('cmp_id', $cmpid)->delete();
        $this->db->table('accttxnmst')->where('cmp_id', $cmpid)->delete();
        $this->db->table('acctvchreg')->where('cmp_id', $cmpid)->delete();
        $this->db->table('accoppybal')->where('cmp_id', $cmpid)->delete();
        $this->db->table('acctmstdet')->where('cmp_id', $cmpid)->delete();
        $this->db->table('subacctxnm')->where('cmp_id', $cmpid)->delete();
        $this->db->table('suboppybal')->where('cmp_id', $cmpid)->delete();
        $this->db->table('subacctmst')->where('cmp_id', $cmpid)->delete();

        /* ---------- Bills / GST ---------- */
        $this->db->table('billtxnmst')->where('cmp_id', $cmpid)->delete();
        $this->db->table('billoppybal')->where('cmp_id', $cmpid)->delete();
        $this->db->table('billmaster')->where('cmp_id', $cmpid)->delete();
        $this->db->table('billsundry')->where('cmp_id', $cmpid)->delete();
        $this->db->table('bsdconfign')->where('cmp_id', $cmpid)->delete();

        /* ---------- Inventory ---------- */
        $this->db->table('itemtxnmst')->where('cmp_id', $cmpid)->delete();
        $this->db->table('itmvchregn')->where('cmp_id', $cmpid)->delete();
        $this->db->table('itmoppybal')->where('cmp_id', $cmpid)->delete();
        $this->db->table('itmoppyval')->where('cmp_id', $cmpid)->delete();
        $this->db->table('itmmstdetn')->where('cmp_id', $cmpid)->delete();
        $this->db->table('itemmaster')->where('cmp_id', $cmpid)->delete();
        $this->db->table('itemgrpmst')->where('cmp_id', $cmpid)->delete();
        $this->db->table('itemcatmst')->where('cmp_id', $cmpid)->delete();
        $this->db->table('itmunitmst')->where('cmp_id', $cmpid)->delete();

        /* ---------- Batch ---------- */
        $this->db->table('batchtxnmt')->where('cmp_id', $cmpid)->delete();
        $this->db->table('batchoppyb')->where('cmp_id', $cmpid)->delete();
        $this->db->table('batchmastr')->where('cmp_id', $cmpid)->delete();

        /* ---------- Material Centre ---------- */
        $this->db->table('matcentdet')->where('cmp_id', $cmpid)->delete();
        $this->db->table('matcentgrp')->where('cmp_id', $cmpid)->delete();
        $this->db->table('matcentmst')->where('cmp_id', $cmpid)->delete();

        /* ---------- Projects ---------- */
        $this->db->table('prjtxnmstn')->where('cmp_id', $cmpid)->delete();
        $this->db->table('prjoppybal')->where('cmp_id', $cmpid)->delete();
        $this->db->table('projectmst')->where('cmp_id', $cmpid)->delete();
        $this->db->table('prjgrpmstn')->where('cmp_id', $cmpid)->delete();

        /* ---------- Cost Centres ---------- */
        $this->db->table('cctxnmstnn')->where('cmp_id', $cmpid)->delete();
        $this->db->table('ccoppybaln')->where('cmp_id', $cmpid)->delete();
        $this->db->table('ccmasternn')->where('cmp_id', $cmpid)->delete();
        $this->db->table('ccgrpmstnn')->where('cmp_id', $cmpid)->delete();

        /* ---------- GST / E-Invoice / E-Way ---------- */
        $this->db->table('einvmaster')->where('cmp_id', $cmpid)->delete();
        $this->db->table('ewbmastern')->where('cmp_id', $cmpid)->delete();
        $this->db->table('gstdispfrm')->where('cmp_id', $cmpid)->delete();
        $this->db->table('gstshipton')->where('cmp_id', $cmpid)->delete();
        $this->db->table('gstrinwsup')->where('cmp_id', $cmpid)->delete();
        $this->db->table('gstroutsup')->where('cmp_id', $cmpid)->delete();

        /* ---------- Vouchers ---------- */
        $this->db->table('vchgstfcyn')->where('cmp_id', $cmpid)->delete();
        $this->db->table('vchgstsumn')->where('cmp_id', $cmpid)->delete();
        $this->db->table('vchhsnsacn')->where('cmp_id', $cmpid)->delete();
        $this->db->table('vchlongnar')->where('cmp_id', $cmpid)->delete();
        $this->db->table('vchshrtnar')->where('cmp_id', $cmpid)->delete();
        $this->db->table('vchfcyrate')->where('cmp_id', $cmpid)->delete();
        $this->db->table('vchseriesa')->where('cmp_id', $cmpid)->delete();
        $this->db->table('vchseriesm')->where('cmp_id', $cmpid)->delete();
        $this->db->table('vchseriesn')->where('cmp_id', $cmpid)->delete();
        $this->db->table('vchbridgen')->where('cmp_id', $cmpid)->delete();
        $this->db->table('vchtxnconso')->where('cmp_id', $cmpid)->delete();
        /* ---------- Masters ---------- */
        $this->db->table('taxcatrate')->where('cmp_id', $cmpid)->delete();
        $this->db->table('taxcatmstn')->where('cmp_id', $cmpid)->delete();
        $this->db->table('cmpbankmst')->where('cmp_id', $cmpid)->delete();
        $this->db->table('cmpfcymstn')->where('cmp_id', $cmpid)->delete();

        /* ---------- Offices ---------- */
        $this->db->table('hoboaddrmt')->where('cmp_id', $cmpid)->delete();
        $this->db->table('hobogstdet')->where('cmp_id', $cmpid)->delete();
        $this->db->table('hobogstinm')->where('cmp_id', $cmpid)->delete();
        $this->db->table('hobotanmst')->where('cmp_id', $cmpid)->delete();
        $this->db->table('hobomaster')->where('cmp_id', $cmpid)->delete();

        /* ---------- Company Core ---------- */
        $this->db->table('cmpaddrmst')->where('cmp_id', $cmpid)->delete();
        $this->db->table('cmpmstdetn')->where('cmp_id', $cmpid)->delete();
        $this->db->table('cmpfymastr')->where('cmp_id', $cmpid)->delete();

        /* ---------- Access Control (CORRECT DB) ---------- */
        $this->univaictly
            ->table('cmpacsmstr')
            ->where('cmp_id', $cmpid)
            ->delete();

        /* ---------- Finally delete company ---------- */
        $this->db->table('cmpmastern')
            ->where('cmp_id', $cmpid)
            ->delete();

        /* -------------------------------------------------
         * Commit
         * ------------------------------------------------- */
        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Hard delete failed');
        }

        $this->db->transCommit();

        return [
            'success' => '1',
            'message' => 'Company permanently deleted'
        ];

    } catch (\Throwable $e) {

        $this->db->transRollback();

        return [
            'success' => '0',
            'message' => $e->getMessage()
        ];
    }
}


/**
     * Convenience: list only soft-deleted companies with pagination.
     */
    public function listDeleted(string $uuid, int $page = 1, int $perPage = 20): array
{
    $page    = max(1, $page);
    $perPage = max(1, $perPage);
    $offset  = ($page - 1) * $perPage;

    // 1. Fetch access rows
    $acsRows = $this->univaictly
        ->table('cmpacsmstr')
        ->select('cmp_id')
        ->groupStart()
            ->where('uuid_aictly_by', $uuid)
            ->orWhere('uuid_aictly_acs', $uuid)
        ->groupEnd()
        ->get()
        ->getResultArray();

    // 2. No access → empty result
    if (empty($acsRows)) {
        return [
            'data'  => [],
            'total' => 0,
        ];
    }

    $cmpIds = array_unique(array_column($acsRows, 'cmp_id'));

    // 3. Base query (deleted companies only)
    $builder = $this->db
        ->table('cmpmastern AS cmp')
        ->select('cmp.cmp_id, cmp.cmp_name, cmp.cmp_short_name, cmp.cmp_status')
        ->where('cmp.cmp_status', 0)
        ->whereIn('cmp.cmp_id', $cmpIds);

    // 4. Total count
    $total = $builder->countAllResults(false);

    // 5. Paginated data
    $rows = $builder
        ->orderBy('cmp.cmp_name', 'ASC')
        ->limit($perPage, $offset)
        ->get()
        ->getResultArray();

    $data = [];
    foreach ($rows as $cmp) {
		 $finyear = '';
            if (method_exists($this, 'get_comp_fy_info')) {
                $fyInfo = $this->get_comp_fy_info((int) $cmp['cmp_id']);
                if ($fyInfo) {
                    $finyear = date('d-m-Y', strtotime($fyInfo['fy_beg_date'])) .
                        ' -- ' .
                        date('d-m-Y', strtotime($fyInfo['fy_end_date']));
                }
            }
        $data[] = [
            'ownership'        => 'recycle',
            'comp_type'        => 'cmp',
            'comp_status'      => (int) $cmp['cmp_status'],
            'comp_id'          => (int) $cmp['cmp_id'],
            'company_name'     => $cmp['cmp_name'],
            'comp_short_name'  => $cmp['cmp_short_name'] ?: $cmp['cmp_name'],
            'comp_code'        => erp_compcode_format((int) $cmp['cmp_id']),
			'finyear'          => $finyear
        ];
    }

    return $data;
}
}