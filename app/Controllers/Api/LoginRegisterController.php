<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Api\AppCommonModel;
use Throwable;

class LoginRegisterController extends ResourceController
{
    use ResponseTrait;

    protected AppCommonModel $appCommon;
 
    public function __construct()
    {
        $this->appCommon = new AppCommonModel();
    }
 

   public function login()
    {	
     
        $authHeader =
            $this->request->getHeaderLine('Authorization')
            ?: ($_SERVER['HTTP_AUTHORIZATION'] ?? '')
            ?: ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');

        if (!$authHeader || !preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            return $this->failUnauthorized('Session key missing');
        }
        $sesKey = trim($matches[1]);  

      
		$compIdinfo = $this->request->getJSON(true) ?? [];
		
		$username = isset($compIdinfo['LoginName']) ? (string)$compIdinfo['LoginName'] : "";
        $password = isset($compIdinfo['Password']) ? (string)$compIdinfo['Password'] : "";
        
     
        $ses = $this->appCommon->validateSesKey($sesKey);
        if (!$ses || ($ses['status'] ?? 0) !== 1) {
            return $this->failUnauthorized('Session expired or invalid');
        } 

        // 4) Fetch inventory Status
		$postinfo     = array("username"=>$username,"password"=>$password);
        $resp = $this->appCommon->login($postinfo);

        // 5) Respond
        return $this->respond($resp);
    }
	 
  }