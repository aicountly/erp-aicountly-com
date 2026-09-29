<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Api\ResaveSaleVouchersModel;
use App\Models\Api\ResavePurchaseVouchersModel;
use Throwable;

class ResaveVouchersController extends ResourceController
{
    use ResponseTrait;

    protected $appSaleCommon;
	protected $appPurchaseCommon;

    public function __construct()
    {
        $this->appSaleCommon = new ResaveSaleVouchersModel();
		$this->appPurchaseCommon = new ResavePurchaseVouchersModel();
    }

    public function resave()
    {
        $input = $this->request->getJSON(true) ?? [];

        $bo_id       = isset($input['bo_id'])   ? (int) $input['bo_id']   : 0;
        $fy_id       = isset($input['fy_id'])   ? (int) $input['fy_id']   : 0;
        $compId      = isset($input['cmp_id'])  ? (int) $input['cmp_id']  : 0;
        $startDate   = $input['start_date']   ?? '';
        $endDate     = $input['end_date']     ?? '';
        $voucherType = $input['voucher_type'] ?? 'sale';

        if ($compId === 0 || $bo_id === 0 || $fy_id === 0) {
            return $this->respond([
                'status'  => false,
                'message' => 'cmp_id, bo_id, and fy_id are required.'
            ], 400);
        }

        if ($startDate === '' || $endDate === '') {
            return $this->respond([
                'status'  => false,
                'message' => 'start_date and end_date are required.'
            ], 400);
        }
		if($voucherType=='sale')
			$resp = $this->appSaleCommon->ResaveVoucherStatus($compId, $bo_id, $fy_id, $input);
        else if($voucherType=='purchase')
			$resp = $this->appPurchaseCommon->ResaveVoucherStatus($compId, $bo_id, $fy_id, $input);
        
        return $this->respond(json_decode($resp, true));
    }
}