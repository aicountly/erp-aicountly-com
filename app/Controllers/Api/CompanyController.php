<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Api\AppCommonModel;
use Throwable;

class CompanyController extends ResourceController
{
    use ResponseTrait;

    protected AppCommonModel $appCommon;

    public function __construct()
    {
        $this->appCommon = new AppCommonModel();
    }

    /**
 * GET /api/companies?filter=all|mine|shared|recycle&page=1&per_page=20
 * Header: Authorization: Bearer <ses_key>
 */
public function index()
{
    /* -----------------------------
     * 1. Extract Bearer token
     * ----------------------------- */
$authHeader =
    $this->request->getHeaderLine('Authorization')
    ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
    ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

    if (
        !$authHeader ||
        !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)
    ) {
        return $this->failUnauthorized('Session key missing');
    }

    $sesKey = trim($matches[1]);

    /* -----------------------------
     * 2. Validate session key
     * ----------------------------- */
    $ses = $this->appCommon->validateSesKey($sesKey);

    if (!$ses || ($ses['status'] ?? 0) !== 1) {
        return $this->failUnauthorized('Session expired or invalid');
    }

    $uuid = $ses['uuid_aictly']; // ✅ derived from ses_key

    /* -----------------------------
     * 3. Read query params
     * ----------------------------- */
    $filter  = strtolower((string) $this->request->getGet('filter'));
    $page    = max(1, (int) $this->request->getGet('page'));
    $perPage = (int) $this->request->getGet('per_page') ?: 20;
    $perPage = max(1, min(100, $perPage));

    if (!in_array($filter, ['all', 'mine', 'shared', 'recycle'], true)) {
        $filter = 'all';
    }

    /* -----------------------------
     * 4. Fetch companies
     * ----------------------------- */
    switch ($filter) {
        case 'mine':
            $data = $this->appCommon->user_company_list($uuid)['my'] ?? [];
            break;

        case 'shared':
            $companies = $this->appCommon->user_company_list($uuid);
            $data = $companies['shared'] ?? ($companies['share_grp'] ?? []);
            break;

        case 'recycle':
            $data = $this->appCommon->listDeleted($uuid);
            break;

        case 'all':
        default:
            $companies = $this->appCommon->user_company_list($uuid);
            $data = $companies['all'] ?? ($companies['my'] ?? []);
            break;
    }
/* -----------------------------
     * 5. Pagination
     * ----------------------------- */
    $total     = is_array($data) ? count($data) : 0;
    $offset    = ($page - 1) * $perPage;
    $pagedData = array_slice($data, $offset, $perPage);

    /* -----------------------------
     * 6. Response
     * ----------------------------- */
    return $this->respond([
        'success'  => '1',
        'filter'   => $filter,
        'page'     => $page,
        'per_page' => $perPage,
        'total'    => $total,
        'data'     => $pagedData,
    ]);
}

	/*
	  User Logout
	*/
	public function logout()
	{
		// Read auth token cookie
		$cookie = $this->request->getCookie('AIC_AUTH_TOKEN');

		if ($cookie) {
			$authToken = $this->decryptValue($cookie);

			if ($authToken) {
				// Invalidate auth token + ses keys
				$this->appCommon->logoutByAuthToken($authToken);
			}
		}
		// Delete cookie (important: same params as setCookie)
		$this->response->deleteCookie(
			'AIC_AUTH_TOKEN',
			'/',
			'.aicountly.com',
			true,
			true,
			'None'
		);

		return $this->response->setJSON([
			'status'  => 1,
			'message' => 'Logged out successfully'
		]);
	}


    
/**
     * GET /api/companies/activities?cmp_id=11&page=1&per_page=20
     * Header: X-User-UUID (required)
     */
    public function activities()
    {
        /* -----------------------------
     * 1. Extract Bearer token
     * ----------------------------- */
$authHeader =
    $this->request->getHeaderLine('Authorization')
    ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
    ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

    if (
        !$authHeader ||
        !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)
    ) {
        return $this->failUnauthorized('Session key missing');
    }

    $sesKey = trim($matches[1]);

    /* -----------------------------
     * 2. Validate session key
     * ----------------------------- */
    $ses = $this->appCommon->validateSesKey($sesKey);

    if (!$ses || ($ses['status'] ?? 0) !== 1) {
        return $this->failUnauthorized('Session expired or invalid');
    }

    $uuid = $ses['uuid_aictly']; // ✅ derived from ses_key
        $filter    = strtolower((string)$this->request->getGet('filter'));
        $page      = max(1, (int)$this->request->getGet('page'));
		$cmp_id    = max(1, (int)$this->request->getGet('cmp_id'));
        $perPage   = (int)$this->request->getGet('per_page') ?: 20;
        $perPage   = max(1, min(100, $perPage)); // clamp between 1 and 100


        // Default to "all" if not provided
        if (!in_array($filter, ['all', 'mine', 'shared', 'recycle'], true)) {
            $filter = 'all';
        }
		$response = $this->appCommon->getCompanyActivities(
        (int) $cmp_id,
		$uuid,
        $page,
        $perPage
    );

    return $this->respond($response);
    }

    /**
     * POST /api/companies
     * Body: JSON payload with company fields
     */
    public function create_company()
    {
        /* -----------------------------
     * 1. Extract Bearer token
     * ----------------------------- */
$authHeader =
    $this->request->getHeaderLine('Authorization')
    ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
    ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

    if (
        !$authHeader ||
        !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)
    ) {
        return $this->failUnauthorized('Session key missing');
    }

    $sesKey = trim($matches[1]);

    /* -----------------------------
     * 2. Validate session key
     * ----------------------------- */
    $ses = $this->appCommon->validateSesKey($sesKey);

    if (!$ses || ($ses['status'] ?? 0) !== 1) {
        return $this->failUnauthorized('Session expired or invalid');
    }

    $uuid = $ses['uuid_aictly']; // ✅ derived from ses_key
        if (!$uuid) return $this->failUnauthorized('Missing user context');

        $payload = $this->request->getJSON(true) ?? [];

        // TODO: add validation rules as needed
        if (empty($payload['comp_name'])) {
            return $this->failServerError('comp_name is required');
        }

        //try {
            $response = $this->appCommon->insertCompany($payload, $uuid);
             return $this->respond($response);
        /* } catch (Throwable $e) {
            return $this->failServerError($e->getMessage());
        } */
    }

    /**
     * PUT /api/companies/{id}
     */
    public function modify($id = null)
    {
      /* -----------------------------
     * 1. Extract Bearer token
     * ----------------------------- */
$authHeader =
    $this->request->getHeaderLine('Authorization')
    ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
    ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

    if (
        !$authHeader ||
        !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)
    ) {
        return $this->failUnauthorized('Session key missing');
    }

    $sesKey = trim($matches[1]);

    /* -----------------------------
     * 2. Validate session key
     * ----------------------------- */
    $ses = $this->appCommon->validateSesKey($sesKey);

    if (!$ses || ($ses['status'] ?? 0) !== 1) {
        return $this->failUnauthorized('Session expired or invalid');
    }

    $uuid = $ses['uuid_aictly']; // ✅ derived from ses_key

        $payload = $this->request->getJSON(true) ?? [];

        // TODO: authorize ownership or edit permission
        $response = $this->appCommon->updateCompany($id, $payload, $uuid);
		return $this->respond($response);
        
    }

    /**
     * DELETE /api/companies/{id}
     * Soft delete => recycle bin
     */
    public function delete($id = null)
    {
       /* -----------------------------
     * 1. Extract Bearer token
     * ----------------------------- */
$authHeader =
    $this->request->getHeaderLine('Authorization')
    ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
    ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

    if (
        !$authHeader ||
        !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)
    ) {
        return $this->failUnauthorized('Session key missing');
    }

    $sesKey = trim($matches[1]);

    /* -----------------------------
     * 2. Validate session key
     * ----------------------------- */
    $ses = $this->appCommon->validateSesKey($sesKey);

    if (!$ses || ($ses['status'] ?? 0) !== 1) {
        return $this->failUnauthorized('Session expired or invalid');
    }

    $uuid = $ses['uuid_aictly']; // ✅ derived from ses_key

        try {
            $response = $this->appCommon->softDeleteCompany($id, $uuid);
            return $this->respond($response);
        } catch (Throwable $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * GET /api/companies/recycle-bin
     */
    public function recycleBin()
    {
       /* -----------------------------
     * 1. Extract Bearer token
     * ----------------------------- */
$authHeader =
    $this->request->getHeaderLine('Authorization')
    ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
    ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

    if (
        !$authHeader ||
        !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)
    ) {
        return $this->failUnauthorized('Session key missing');
    }

    $sesKey = trim($matches[1]);

    /* -----------------------------
     * 2. Validate session key
     * ----------------------------- */
    $ses = $this->appCommon->validateSesKey($sesKey);

    if (!$ses || ($ses['status'] ?? 0) !== 1) {
        return $this->failUnauthorized('Session expired or invalid');
    }

    $uuid = $ses['uuid_aictly']; // ✅ derived from ses_key

        $rows = $this->appCommon->listDeleted($uuid);
        return $this->respond([
            'success' => '1',
            'data'    => $rows,
        ]);
    }
	
	/**
     * GET /api/userprofile
     */
    public function userprofile()
    {
       /* -----------------------------
     * 1. Extract Bearer token
     * ----------------------------- */
$authHeader =
    $this->request->getHeaderLine('Authorization')
    ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
    ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

    if (
        !$authHeader ||
        !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)
    ) {
        return $this->failUnauthorized('Session key missing');
    }

    $sesKey = trim($matches[1]);

    /* -----------------------------
     * 2. Validate session key
     * ----------------------------- */
    $ses = $this->appCommon->validateSesKey($sesKey);

    if (!$ses || ($ses['status'] ?? 0) !== 1) {
        return $this->failUnauthorized('Session expired or invalid');
    }

    $uuid = $ses['uuid_aictly']; // ✅ derived from ses_key

        $response = $this->appCommon->UserProfile($uuid);
        return $this->respond($response);
    }
	
	/**
     * GET /api/companyinfo
     */

    public function companyinfo()
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
        

         $compId = (int)($_GET['comp_id']);
	    if ($compId === '') {
            return $this->failValidationErrors(['CMP-ID' => 'Company id  missing']);
        }

        // 3) Validate session key
        $ses = $this->appCommon->validateSesKey($sesKey);
        if (!$ses || ($ses['status'] ?? 0) !== 1) {
            return $this->failUnauthorized('Session expired or invalid');
        }

        $uuid = $ses['uuid_aictly'] ?? ''; // derived from ses_key

        // 4) Fetch company info
        $resp = $this->appCommon->CompanyInfo($compId, $uuid);

        // 5) Respond
        return $this->respond($resp);
    }
	
	/**
     * GET /api/businessid
     */
    public function businessid()
    {
       /* -----------------------------
     * 1. Extract Bearer token
     * ----------------------------- */
$authHeader =
    $this->request->getHeaderLine('Authorization')
    ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
    ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

    if (
        !$authHeader ||
        !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)
    ) {
        return $this->failUnauthorized('Session key missing');
    }

     $sesKey = trim($matches[1]);

    /* -----------------------------
     * 2. Validate session key
     * ----------------------------- */
    $ses = $this->appCommon->validateSesKey($sesKey);

    if (!$ses || ($ses['status'] ?? 0) !== 1) {
        return $this->failUnauthorized('Session expired or invalid');
    }

    $uuid = $ses['uuid_aictly']; // ✅ derived from ses_key

        $response = $this->appCommon->GetBusinessId($uuid);
        return $this->respond($response);
    }
	
private function encryptValue(string $value): string
{
    $key = hash('sha256', getenv('APP_ENC_KEY'), true);
    $iv  = random_bytes(16);

    $cipherText = openssl_encrypt(
        $value,
        'AES-256-CBC',
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );

    return base64_encode($iv . $cipherText);
}
private function decryptValue(string $value): string
{
    $key = hash('sha256', env('APP_ENC_KEY'), true);
    $data = base64_decode($value);

    $iv = substr($data, 0, 16);
    $cipherText = substr($data, 16);

    return openssl_decrypt(
        $cipherText,
        'AES-256-CBC',
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );
}

  public function createSesKey()
{
    // 1) Prefer Authorization: Bearer <auth_token>
    $authHeader =
        $this->request->getHeaderLine('Authorization')
        ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
        ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

    $bearerToken = null;
    if ($authHeader && preg_match('/Bearer\s+(.+)/i', $authHeader, $m)) {
        $bearerToken = trim($m[1]);
    }

    if (!$bearerToken) {
        return $this->response
            ->setStatusCode(401)
            ->setJSON(['message' => 'Auth token missing']);
    }

    /**
     * 3) Decide what you treat as "plain auth token"
     * RECOMMENDED: Bearer token is plain; cookie is encrypted.
     */
    $plainAuthToken = null;

    if ($bearerToken) {
        // Treat Bearer as PLAIN auth token (recommended)
        $plainAuthToken = $bearerToken;
    } else {
        // Cookie fallback is encrypted -> decrypt to plain
        $plainAuthToken = $this->decryptValue($cookieEnc);
    }

    if (!$plainAuthToken) {
        return $this->response
            ->setStatusCode(401)
            ->setJSON(['message' => 'Invalid auth token']);
    }

    // 4) Generate ses_key using existing DB logic
    $result = $this->appCommon->generateSesKeyFromAuth(
        $plainAuthToken,
        $this->request->getIPAddress(),
        $this->request->getUserAgent()
    );

    if (($result['status'] ?? 0) !== 1) {
        return $this->response
            ->setStatusCode(401)
            ->setJSON(['message' => $result['message'] ?? 'Unauthorized']);
    }

    return $this->response->setJSON([
        'ses_key'    => $result['ses_key'],
        'expires_in' => 900
    ]);
}

/**
     * POST /api/auth/refresh
     */
    public function refresh_authtoken()
    {
        $cookie = $this->request->getCookie('AIC_AUTH');

        if (!$cookie) {
            return $this->response->setStatusCode(401)
                ->setJSON([
                    'status'  => 0,
                    'message' => 'Auth token missing'
                ]);
        }

        $authToken = $this->decryptValue($cookie);

        if (!$authToken) {
            return $this->response->setStatusCode(401)
                ->setJSON([
                    'status'  => 0,
                    'message' => 'Invalid auth token'
                ]);
        }

        $result = $this->appCommon->refreshSesKeyUsingAuthToken(
            $authToken,
            $this->request->getIPAddress()
        );

        if ($result['status'] !== 1) {
            return $this->response->setStatusCode(401)
                ->setJSON($result);
        }

        return $this->response->setJSON([
            'status'     => 1,
            'ses_key'    => $result['ses_key'],
			'uuid_aictly' =>$result['uuid_aictly'],
            'expires_in' => 900
        ]);
    }


    /**
     * POST /api/companies/{id}/restore
     */
    public function restore($id = null)
    {
        /* -----------------------------
     * 1. Extract Bearer token
     * ----------------------------- */
$authHeader =
    $this->request->getHeaderLine('Authorization')
    ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
    ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

    if (
        !$authHeader ||
        !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)
    ) {
        return $this->failUnauthorized('Session key missing');
    }

     $sesKey = trim($matches[1]);

    /* -----------------------------
     * 2. Validate session key
     * ----------------------------- */
    $ses = $this->appCommon->validateSesKey($sesKey);

    if (!$ses || ($ses['status'] ?? 0) !== 1) {
        return $this->failUnauthorized('Session expired or invalid');
    }

    $uuid = $ses['uuid_aictly']; // ✅ derived from ses_key

        $response = $this->appCommon->restoreCompany($id, $uuid);        
         return $this->respond($response);
    }

    /**
     * DELETE /api/companies/{id}/destroy
     * Hard delete
     */
    public function destroy($id = null)
    {
        /* -----------------------------
     * 1. Extract Bearer token
     * ----------------------------- */
$authHeader =
    $this->request->getHeaderLine('Authorization')
    ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
    ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

    if (
        !$authHeader ||
        !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)
    ) {
        return $this->failUnauthorized('Session key missing');
    }

     $sesKey = trim($matches[1]);

    /* -----------------------------
     * 2. Validate session key
     * ----------------------------- */
    $ses = $this->appCommon->validateSesKey($sesKey);

    if (!$ses || ($ses['status'] ?? 0) !== 1) {
        return $this->failUnauthorized('Session expired or invalid');
    }

    $uuid = $ses['uuid_aictly']; // ✅ derived from ses_key

        try {
            $response = $this->appCommon->hardDeleteCompany($id, $uuid);
             return $this->respond($response);
        } catch (Throwable $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}