<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Models\CommonModel;
use App\Models\Admin\Gstr2abModel;
use App\Libraries\externaldb;

class GstrReportModel extends Model	{
	function __construct() {
		parent::__construct();        
		$this->externaldb    = new externaldb();	
		$this->session       = \Config\Services::session();
		$this->company_id    = $this->session->get('ses_company_id');
		$this->CommonModel   = new CommonModel();
		$this->Gstr2abModel  = new Gstr2abModel();	   
		$this->bo_id         = $this->session->get('ses_boid');		
		$this->fy_id          = $this->session->get('ses_comp_fy_id');
	} 
	
	/****************  GSTR1  Report condensed & Detailed ***************/
	 /* ============================================================
 * GSTR-1 CONDENSED
 * ============================================================ */
 public function load_gstr1_condensed(string $from_date, string $to_date, array $table_lists): array
{
    $rows = [];
    foreach ($table_lists as $tableno => $label) {
		// ✅ Special handling for 6A (Export)
		if ($tableno === '6A') {

			// Get WPAY (16)
			$tot_wp = $this->gstr1RunQueryForConfig([
				'vch_type_id' => 18,
				'is_outward' => 1,
				'inv_supply_id' => [16]
			], $from_date, $to_date);

			// Get WOPAY (17)
			$tot_wop = $this->gstr1RunQueryForConfig([
				'vch_type_id' => 18,
				'is_outward' => 1,
				'inv_supply_id' => [17]
			], $from_date, $to_date);

			// ✅ WOPAY → force tax = 0
			$tot_wop['igst'] = 0;
			$tot_wop['cgst'] = 0;
			$tot_wop['sgst'] = 0;
			$tot_wop['cess'] = 0;
			$tot_wop['total_tax'] = 0;
			$tot_wop['invoice_value'] = $tot_wop['taxable_value'];

			// ✅ Merge both
			$tot = [
				'total_records' => $tot_wp['total_records'] + $tot_wop['total_records'],
				'taxable_value' => $tot_wp['taxable_value'] + $tot_wop['taxable_value'],
				'igst'          => $tot_wp['igst'],
				'cgst'          => $tot_wp['cgst'],
				'sgst'          => $tot_wp['sgst'],
				'cess'          => $tot_wp['cess'],
				'total_tax'     => $tot_wp['total_tax'],
				'invoice_value' => ($tot_wp['taxable_value'] + $tot_wp['total_tax']) + $tot_wop['taxable_value'],
				'hsn_ignore_footer' => false
			];

		} 
		// ✅ Special handling for 6B (SEZ) 
        elseif ($tableno === '6B') {

            // SEZ WITH PAYMENT (18)
            $tot_wp = $this->gstr1RunQueryForConfig([
                'vch_type_id' => 18,
                'is_outward' => 1,
                'inv_supply_id' => [18]
            ], $from_date, $to_date);

            // SEZ WITHOUT PAYMENT (19)
            $tot_wop = $this->gstr1RunQueryForConfig([
                'vch_type_id' => 18,
                'is_outward' => 1,
                'inv_supply_id' => [19]
            ], $from_date, $to_date);

            // ✅ SEZWOP → force tax = 0
            $tot_wop['igst'] = 0;
            $tot_wop['cgst'] = 0;
            $tot_wop['sgst'] = 0;
            $tot_wop['cess'] = 0;
            $tot_wop['total_tax'] = 0;
            $tot_wop['invoice_value'] = $tot_wop['taxable_value'];

            // ✅ Merge
            $tot = [
                'total_records' => $tot_wp['total_records'] + $tot_wop['total_records'],
                'taxable_value' => $tot_wp['taxable_value'] + $tot_wop['taxable_value'],
                'igst'          => $tot_wp['igst'],
                'cgst'          => $tot_wp['cgst'],
                'sgst'          => $tot_wp['sgst'],
                'cess'          => $tot_wp['cess'],
                'total_tax'     => $tot_wp['total_tax'],
                'invoice_value' => ($tot_wp['taxable_value'] + $tot_wp['total_tax']) + $tot_wop['taxable_value'],
                'hsn_ignore_footer' => false
            ];

        }
		
		else {
			$tot = $this->gstr1SumByTable($tableno, $from_date, $to_date);
		}
        //$tot = $this->gstr1SumByTable($tableno, $from_date, $to_date);

        $rawInvoice = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['invoice_value'];
        $rawTaxable = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['taxable_value'];
        $rawIgst    = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['igst'];
        $rawCgst    = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['cgst'];
        $rawSgst    = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['sgst'];
        $rawCess    = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['cess'];
        $rawTotal   = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['total_tax'];
        
        $rows[] = [
            'from_date'     => $from_date,
            'to_date'       => $to_date,
            'htableno'      => $tableno,
            'tableno'       => $tableno,
            'table_name'    => $label,
            'total_records' => $tot['total_records'],

            // formatted for display
            'invoice_value' => formatAmount($tot['invoice_value']),
            'taxable_value' => formatAmount($tot['taxable_value']),
            'igst'          => formatAmount($tot['igst']),
            'cgst'          => formatAmount($tot['cgst']),
            'sgst'          => formatAmount($tot['sgst']),
            'cess'          => formatAmount($tot['cess']),
            'total_tax'     => formatAmount($tot['total_tax']),

            // raw numbers for footer sums (zeroed for HSN summary)
            'sm_invoice_value' => $rawInvoice,
            'sm_taxable_value' => $rawTaxable,
            'sm_igst'          => $rawIgst,
            'sm_cgst'          => $rawCgst,
            'sm_sgst'          => $rawSgst,
            'sm_cess'          => $rawCess,
            'sm_total_tax'     => $rawTotal,
        ];
    }

    return [
        'totalRecords' => count($rows),
        'curPage'      => 1,
        'data'         => $rows,
    ];
}
public function load_gstr1_condensedaaa(string $from_date, string $to_date, array $table_lists): array
{
    $rows = [];
    foreach ($table_lists as $tableno => $label) {
        $tot = $this->gstr1SumByTable($tableno, $from_date, $to_date);

        $rawInvoice = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['invoice_value'];
        $rawTaxable = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['taxable_value'];
        $rawIgst    = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['igst'];
        $rawCgst    = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['cgst'];
        $rawSgst    = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['sgst'];
        $rawCess    = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['cess'];
        $rawTotal   = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['total_tax'];

        $rows[] = [
            'from_date'     => $from_date,
            'to_date'       => $to_date,
            'htableno'      => $tableno,
            'tableno'       => $tableno,
            'table_name'    => $label,
            'total_records' => $tot['total_records'],

            // formatted for display
            'invoice_value' => formatAmount($tot['invoice_value']),
            'taxable_value' => formatAmount($tot['taxable_value']),
            'igst'          => formatAmount($tot['igst']),
            'cgst'          => formatAmount($tot['cgst']),
            'sgst'          => formatAmount($tot['sgst']),
            'cess'          => formatAmount($tot['cess']),
            'total_tax'     => formatAmount($tot['total_tax']),

            // raw numbers for footer sums (zeroed for HSN summary)
            'sm_invoice_value' => $rawInvoice,
            'sm_taxable_value' => $rawTaxable,
            'sm_igst'          => $rawIgst,
            'sm_cgst'          => $rawCgst,
            'sm_sgst'          => $rawSgst,
            'sm_cess'          => $rawCess,
            'sm_total_tax'     => $rawTotal,
        ];
    }

    return [
        'totalRecords' => count($rows),
        'curPage'      => 1,
        'data'         => $rows,
    ];
}

/* ============================================================
 * GSTR-1 DETAILED
 * ============================================================ */
public function load_gstr1_detailed(string $from_date, string $to_date, array $table_lists): array
{
    $rows = [];
    foreach ($table_lists as $tableno => $label) {
        $tot = $this->gstr1SumDetailedByTable($tableno, $from_date, $to_date);
          
		  // ✅ Special handling for EXPORTS (6A)
			if ($tableno === '6A') {

				// WITH PAYMENT (16)
				$tot_wp = $this->gstr1SumDetailedByTable('6A_EXPWP', $from_date, $to_date);

				// WITHOUT PAYMENT (17)
				$tot_wop = $this->gstr1SumDetailedByTable('6A_EXPWOP', $from_date, $to_date);

				// ✅ Force WOPAY tax = 0
				$tot_wop['igst'] = 0;
				$tot_wop['cgst'] = 0;
				$tot_wop['sgst'] = 0;
				$tot_wop['cess'] = 0;
				$tot_wop['total_tax'] = 0;
				$tot_wop['invoice_value'] = $tot_wop['taxable_value'];

				// ✅ Merge totals
				$tot = [
					'total_records' => $tot_wp['total_records'] + $tot_wop['total_records'],
					'taxable_value' => $tot_wp['taxable_value'] + $tot_wop['taxable_value'],

					// ONLY WPAY contributes tax
					'igst'      => $tot_wp['igst'],
					'cgst'      => $tot_wp['cgst'],
					'sgst'      => $tot_wp['sgst'],
					'cess'      => $tot_wp['cess'],
					'total_tax' => $tot_wp['total_tax'],

					// Invoice = WPAY (taxable+tax) + WOPAY (only taxable)
					'invoice_value' => ($tot_wp['taxable_value'] + $tot_wp['total_tax']) + $tot_wop['taxable_value'],

					'hsn_ignore_footer' => false
				];

			}
			// ✅ Special handling for SEZ (6B) 
			elseif ($tableno === '6B') {

				$tot_wp = $this->gstr1SumDetailedByTable('6B_SEZWP', $from_date, $to_date);
				$tot_wop = $this->gstr1SumDetailedByTable('6B_SEZWOP', $from_date, $to_date);

				// SEZWOP → tax = 0
				$tot_wop['igst'] = 0;
				$tot_wop['cgst'] = 0;
				$tot_wop['sgst'] = 0;
				$tot_wop['cess'] = 0;
				$tot_wop['total_tax'] = 0;
				$tot_wop['invoice_value'] = $tot_wop['taxable_value'];

				$tot = [
					'total_records' => $tot_wp['total_records'] + $tot_wop['total_records'],
					'taxable_value' => $tot_wp['taxable_value'] + $tot_wop['taxable_value'],
					'igst'      => $tot_wp['igst'],
					'cgst'      => $tot_wp['cgst'],
					'sgst'      => $tot_wp['sgst'],
					'cess'      => $tot_wp['cess'],
					'total_tax' => $tot_wp['total_tax'],
					'invoice_value' => ($tot_wp['taxable_value'] + $tot_wp['total_tax']) + $tot_wop['taxable_value'],
					'hsn_ignore_footer' => false
				];
			}
			elseif ($tableno === '13') {
				$tot_cancel = $this->gstr1SumDetailedByTable('13_candoc', $from_date, $to_date);
				$tot_net    = $this->gstr1SumDetailedByTable('13_netissued', $from_date, $to_date);
				$tot = [
					'total_records' => $tot_net['total_records'],

					'taxable_value' => 0,
					'igst' => 0,
					'cgst' => 0,
					'sgst' => 0,
					'cess' => 0,
					'total_tax' => 0,
					'invoice_value' => 0,

					'hsn_ignore_footer' => false
				];
			}
			else {
				$tot = $this->gstr1SumDetailedByTable($tableno, $from_date, $to_date);
				// ✅ Keep your existing WOPAY fix
				if ($tableno === '6A_EXPWOP' || $tableno === '6B_SEZWOP') {
					$tot['igst'] = 0;
					$tot['cgst'] = 0;
					$tot['sgst'] = 0;
					$tot['cess'] = 0;
					$tot['total_tax'] = 0;
					$tot['invoice_value'] = $tot['taxable_value'];
				}
			}

        $rawInvoice = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['invoice_value'];
        $rawTaxable = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['taxable_value'];
        $rawIgst    = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['igst'];
        $rawCgst    = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['cgst'];
        $rawSgst    = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['sgst'];
        $rawCess    = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['cess'];
        $rawTotal   = (!empty($tot['hsn_ignore_footer'])) ? 0.0 : (float)$tot['total_tax'];

        $rows[] = [
            'from_date'     => $from_date,
            'to_date'       => $to_date,
            'htableno'      => $tableno,
            'tableno'       => $tableno,
            'table_name'    => $label,
            'total_records' => $tot['total_records'],

            // formatted
            'invoice_value' => formatAmount($tot['invoice_value']),
            'taxable_value' => formatAmount($tot['taxable_value']),
            'igst'          => formatAmount($tot['igst']),
            'cgst'          => formatAmount($tot['cgst']),
            'sgst'          => formatAmount($tot['sgst']),
            'cess'          => formatAmount($tot['cess']),
            'total_tax'     => formatAmount($tot['total_tax']),

            // raw (zeroed for HSN summary)
            'sm_invoice_value' => $rawInvoice,
            'sm_taxable_value' => $rawTaxable,
            'sm_igst'          => $rawIgst,
            'sm_cgst'          => $rawCgst,
            'sm_sgst'          => $rawSgst,
            'sm_cess'          => $rawCess,
            'sm_total_tax'     => $rawTotal,
        ];
    }

    return [
        'totalRecords' => count($rows),
        'curPage'      => 1,
        'data'         => $rows,
    ];
}

/* ============================================================
 * INTERNAL: condensed mapping & summation
 * ============================================================ */
 protected function gstr1SumByTable(string $tableno, string $from_date, string $to_date): array
{
    /*
     * GSTR-1 Table mapping (corrected):
     *
     *  4A  B2B Regular          → vch_type_id=18, is_outward=1, inv_supply_id=[1], rev_chg=false
     *  4B  B2B Reverse Charge   → vch_type_id=18, is_outward=1, inv_supply_id=[1], rev_chg=true
     *  5   B2CL (Large)         → vch_type_id=18, is_outward=1, inv_supply_id=[2]
     *  6A  Exports               → vch_type_id=18, is_outward=1, inv_supply_id=[16,17]
     *  6B  SEZ                  → vch_type_id=18, is_outward=1, inv_supply_id=[18,19]
     *  6C  Deemed Export        → vch_type_id=18, is_outward=1, inv_supply_id=[20]
     *  7   B2CS (Small)         → vch_type_id=18, is_outward=1, inv_supply_id=[3]
     *  8   Nil/Exempt/Non-GST   → vch_type_id=18, is_outward=1, inv_supply_id=[4..15]
     *  9B  Credit Notes         → vch_type_id=2,  is_outward=2  (all supply types)
     * 12   HSN Summary          → hsn_summary flag
     * 13   Document Issued      → doc_count flag
     */
    $map = [
        '4A'            => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [1],              'rev_chg' => false],
        '4B'            => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [1],              'rev_chg' => true],
        '5'             => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [2]],
        '6A'            => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [16, 17]],
        '6B'            => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [18, 19]],
        '6C'            => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [20]],
        '7'             => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [3]],
        '8'             => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [4,5,6,7,8,9,10,11,12,13,14,15]],
        '9A,9C'         => ['no_data'     => true],
        '9B'            => ['credit_note' => true],  // vch_type_id=2, is_outward=2 handled inside runner
        '11A(1),11A(2)' => ['no_data'     => true],
        '11B(1),11B(2)' => ['no_data'     => true],
        '12'            => ['hsn_summary' => true],
        '13'            => ['doc_count'   => true],
        '14'            => ['no_data'     => true],
        '15'            => ['no_data'     => true],
        '16'            => ['no_data'     => true],
    ];

    $conf = $map[$tableno] ?? ['no_data' => true];
    return $this->gstr1RunQueryForConfig($conf, $from_date, $to_date);
}
protected function gstr1SumByTableaaaa(string $tableno, string $from_date, string $to_date): array
{
    $map = [
        '4A' => ['inv_supply_id' => [1]],
        '4B' => ['inv_supply_id' => [1], 'rev_chg' => true],
        '5'  => ['inv_supply_id' => [2]],
        '6A' => ['inv_supply_id' => [16,17]],
        '6B' => ['inv_supply_id' => [18,19]],
        '6C' => ['inv_supply_id' => [20]],
        '7'  => ['inv_supply_id' => [3]],
        '8'  => ['inv_supply_id' => [4,5,6,7,8,9,10,11,12,13,14,15]],
        '9A,9C' => ['no_data' => true], // blank for now
        '9B'    => ['credit_note' => true],
        '11A(1),11A(2)' => ['no_data' => true],
        '11B(1),11B(2)' => ['no_data' => true],
        '12' => ['hsn_summary' => true],   // HSN summary
        '13' => ['doc_count' => true],     // DOCUMENT ISSUED
        '14' => ['no_data' => true],
        '15' => ['no_data' => true],
        '16' => ['no_data' => true],
    ];
    return $this->gstr1RunQueryForConfig($map[$tableno] ?? ['no_data' => true], $from_date, $to_date);
}

/* ============================================================
 * INTERNAL: detailed mapping & summation
 * ============================================================ */
 protected function gstr1SumDetailedByTable(string $tableno, string $from_date, string $to_date): array
{
    $map = [
        '4A'                    => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [1],             'rev_chg' => false],
        '4B'                    => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [1],             'rev_chg' => true],
        '5'                     => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [2]],
        '6A'                    => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [16, 17]],
        '6A_EXPWP'              => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [16]],
        '6A_EXPWOP'             => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [17]],
        '6B'                    => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [18, 19]],
        '6B_SEZWP'              => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [18]],
        '6B_SEZWOP'             => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [19]],
        '6C'                    => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [20]],
        '7'                     => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [3]],
        '8'                     => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [4,5,6,7,8,9,10,11,12,13,14,15]],
        '8_Nil'                 => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [4,5,6,7]],
        '8_Exempted'            => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [8,9,10,11]],
        '8_Non-GST'             => ['vch_type_id' => 18, 'is_outward' => 1, 'inv_supply_id' => [12,13,14,15]],
        '9A,9C'                 => ['no_data' => true],
        '9B'                    => ['credit_note' => true],
        '9B_Registered'         => ['credit_note' => true, 'inv_supply_id' => [1]],
        '9B_Unregistered'       => ['credit_note' => true, 'inv_supply_id' => [2, 3]],
        '11A(1),11A(2)'         => ['no_data' => true],
        '11B(1),11B(2)'         => ['no_data' => true],
        '12'                    => ['hsn_summary' => true],
        '13'                    => ['doc_count'   => true],
        '13_candoc'             => ['doc_cancel'  => true],
        '13_netissued'          => ['doc_net'     => true],
        '14'                    => ['no_data' => true],
        '15'                    => ['no_data' => true],
        '16'                    => ['no_data' => true],
    ];

    $conf = $map[$tableno] ?? ['no_data' => true];
    return $this->gstr1RunQueryForConfig($conf, $from_date, $to_date);
}

protected function gstr1SumDetailedByTablesss(string $tableno, string $from_date, string $to_date): array
{
    $map = [
        '4A' => ['inv_supply_id' => [1]],
        '4B' => ['inv_supply_id' => [1], 'rev_chg' => true],
        '5'  => ['inv_supply_id' => [2]],
        '6A' => ['inv_supply_id' => [16,17]],
        '6A_EXPWP'   => ['inv_supply_id' => [16]],
        '6A_EXPWOP'  => ['inv_supply_id' => [17]],
        '6B'         => ['inv_supply_id' => [18,19]],
        '6B_SEZWP'   => ['inv_supply_id' => [18]],
        '6B_SEZWOP'  => ['inv_supply_id' => [19]],
        '6C'         => ['inv_supply_id' => [20]],
        '7'          => ['inv_supply_id' => [3]],
        '8'          => ['inv_supply_id' => [4,5,6,7,8,9,10,11,12,13,14,15]],
        '8_Nil'      => ['inv_supply_id' => [4,5,6,7]],
        '8_Exempted' => ['inv_supply_id' => [8,9,10,11]],
        '8_Non-GST'  => ['inv_supply_id' => [12,13,14,15]],
        '9A,9C'      => ['no_data' => true],
        '9B'         => ['credit_note' => true],
        '9B_Registered'   => ['credit_note' => true, 'inv_supply_id' => [1]],    // updated
        '9B_Unregistered' => ['credit_note' => true, 'inv_supply_id' => [2,3]],  // updated
        '11A(1),11A(2)' => ['no_data' => true],
        '11B(1),11B(2)' => ['no_data' => true],
        '12'            => ['hsn_summary' => true],  // HSN summary
        '13'            => ['doc_count' => true],    // DOCUMENT ISSUED
        '13_candoc'     => ['doc_cancel' => true],  // CANCELLED DOCS => 0
        '13_netissued'  => ['doc_net' => true],     // NET ISSUED DOCS = doc_count
        '14' => ['no_data' => true],
        '15' => ['no_data' => true],
        '16' => ['no_data' => true],
    ];
    return $this->gstr1RunQueryForConfig($map[$tableno] ?? ['no_data' => true], $from_date, $to_date);
}

/* ============================================================
 * INTERNAL: shared query runner
 * ============================================================ */
 protected function gstr1RunQueryForConfig(array $conf, string $from_date, string $to_date): array
{
    // ── CANCELLED DOCS ──────────────────────────────────────────────────
    if (!empty($conf['doc_cancel'])) {
        return $this->_gstr1EmptyResult();
    }

    // ── DOCUMENT ISSUED / NET ISSUED ────────────────────────────────────
    if (!empty($conf['doc_count']) || !empty($conf['doc_net'])) {
    $b = $this->db->table('vchgstsumn s');
    $b->join('vchtxnconso t', 't.vch_txn_id = s.vch_txn_id', 'inner');
    $b->select('COUNT(DISTINCT s.vch_txn_id) AS c', false);
    $b->where('s.cmp_id', $this->company_id);
    $b->where('s.vch_date >=', $from_date);
    $b->where('s.vch_date <=', $to_date);
    $b->whereIn('s.acc_bsd_type', [1, 3]);

    // ✅ FIX 1: Restrict to outward sales (18) + credit notes (2) only
    $b->whereIn('t.vch_type_id', [18, 2]);

    // ✅ FIX 2: Match is_outward correctly per vch_type_id
    // Sales (type 18) = is_outward 1, Credit Notes (type 2) = is_outward 2
    $b->where("(
        (t.vch_type_id = 18 AND s.is_outward = 1)
        OR
        (t.vch_type_id = 2  AND s.is_outward = 2)
    )", null, false);

    if (!empty($this->bo_id)) {
        $b->where('t.hobo_id', $this->bo_id);
    }

    $row   = $b->get()->getRowArray();
    $count = (int)($row['c'] ?? 0);

    return array_merge($this->_gstr1EmptyResult(), ['total_records' => $count]);
}

    // ── HSN SUMMARY (Table 12) ──────────────────────────────────────────
    if (!empty($conf['hsn_summary'])) {
		// ✅ EXCLUDE ALL WITHOUT PAYMENT TYPES (17, 19)
        $b = $this->db->table('vchgstsumn s');
        $b->join('vchtxnconso t', 't.vch_txn_id = s.vch_txn_id', 'inner');
        $b->select("
            COUNT(DISTINCT s.vch_txn_id)         AS total_records,
            COALESCE(SUM(s.vch_taxable_value),0) AS taxable_value,
            COALESCE(SUM(CASE WHEN s.inv_supply_id NOT IN (17,19) THEN s.vch_igst ELSE 0 END),0) AS igst,
			COALESCE(SUM(CASE WHEN s.inv_supply_id NOT IN (17,19) THEN s.vch_cgst ELSE 0 END),0) AS cgst,
			COALESCE(SUM(CASE WHEN s.inv_supply_id NOT IN (17,19) THEN s.vch_sgst_ugst ELSE 0 END),0) AS sgst,
			COALESCE(SUM(CASE WHEN s.inv_supply_id NOT IN (17,19) THEN s.vch_cess ELSE 0 END),0) AS cess,
			COALESCE(SUM(CASE WHEN s.inv_supply_id NOT IN (17,19) THEN s.vch_total_tax ELSE 0 END),0) AS total_tax
        ", false);
        $b->where('s.cmp_id', $this->company_id);
        $b->where('s.vch_date >=', $from_date);
        $b->where('s.vch_date <=', $to_date);
        // HSN summary = all outward vouchers (sales + credit notes)
        $b->where("(
            (t.vch_type_id = 18 AND s.is_outward = 1)
            OR
            (t.vch_type_id = 2  AND s.is_outward = 2)
        )", null, false);

        if (!empty($this->bo_id)) {
            $b->where('t.hobo_id', $this->bo_id);
        }

        $res     = $b->get()->getRowArray();
        $taxable = (float)($res['taxable_value'] ?? 0);
        $igst    = (float)($res['igst']          ?? 0);
        $cgst    = (float)($res['cgst']          ?? 0);
        $sgst    = (float)($res['sgst']          ?? 0);
        $cess    = (float)($res['cess']          ?? 0);
        $ttax    = (float)($res['total_tax']     ?? 0);

        return [
            'total_records'     => (int)($res['total_records'] ?? 0),
            'invoice_value'     => $taxable + $ttax,
            'taxable_value'     => $taxable,
            'igst'              => $igst,
            'cgst'              => $cgst,
            'sgst'              => $sgst,
            'cess'              => $cess,
            'total_tax'         => $ttax,
            'hsn_ignore_footer' => true,
        ];
    }

    // ── NO DATA TABLES ───────────────────────────────────────────────────
    if (!empty($conf['no_data'])) {
        return $this->_gstr1EmptyResult();
    }

    // ═══════════════════════════════════════════════════════════════════
    //  MAIN QUERY PATH
    //  Two distinct cases:
    //    A) credit_note  → vch_type_id = 2,  is_outward = 2
    //    B) normal sale  → vch_type_id = 18, is_outward = 1  (or from conf)
    // ═══════════════════════════════════════════════════════════════════
    $db = $this->db->table('vchgstsumn s');
    $db->join('vchtxnconso t', 't.vch_txn_id = s.vch_txn_id', 'inner');

    // ── Reverse-charge join (only needed for 4B) ─────────────────────
    if (!empty($conf['rev_chg'])) {
        $db->join('gstroutsup go', 'go.vch_txn_id = s.vch_txn_id', 'left');
    }

    $db->select("
        COUNT(DISTINCT s.vch_txn_id)         AS total_records,
        COALESCE(SUM(s.vch_taxable_value),0) AS taxable_value,
        COALESCE(SUM(s.vch_igst),0)          AS igst,
        COALESCE(SUM(s.vch_cgst),0)          AS cgst,
        COALESCE(SUM(s.vch_sgst_ugst),0)     AS sgst,
        COALESCE(SUM(s.vch_cess),0)          AS cess,
        COALESCE(SUM(s.vch_total_tax),0)     AS total_tax
    ", false);

    // ── Always scope to company & date range ─────────────────────────
    $db->where('s.cmp_id', $this->company_id);
    $db->where('s.vch_date >=', $from_date);
    $db->where('s.vch_date <=', $to_date);

    if (!empty($this->bo_id)) {
        $db->where('t.hobo_id', $this->bo_id);
    }

    // ── CREDIT NOTE path ─────────────────────────────────────────────
    if (!empty($conf['credit_note'])) {
        // Credit notes: vch_type_id = 2, is_outward = 2
        $db->where('t.vch_type_id', 2);
        $db->where('s.is_outward', 2);

        // Optional supply-type filter (e.g. 9B_Registered)
        if (!empty($conf['inv_supply_id'])) {
            $db->whereIn('s.inv_supply_id', $conf['inv_supply_id']);
        }

    } else {
        // ── NORMAL OUTWARD SALES path ─────────────────────────────────
        // Use explicit vch_type_id if provided in conf, otherwise default 18
        $vchTypeId = $conf['vch_type_id'] ?? 18;
        $isOutward = $conf['is_outward']  ?? 1;

        $db->where('t.vch_type_id', $vchTypeId);
        $db->where('s.is_outward', $isOutward);

        // Supply-type filter
        if (!empty($conf['inv_supply_id'])) {
            $db->whereIn('s.inv_supply_id', $conf['inv_supply_id']);
        }

        // Reverse charge filter (4B)
        if (!empty($conf['rev_chg'])) {
            $db->where('go.outsup_rev_chg', 1);
        }
    }

    $res = $db->get()->getRowArray();

    $taxable = (float)($res['taxable_value'] ?? 0);
    $igst    = (float)($res['igst']          ?? 0);
    $cgst    = (float)($res['cgst']          ?? 0);
    $sgst    = (float)($res['sgst']          ?? 0);
    $cess    = (float)($res['cess']          ?? 0);
    $ttax    = (float)($res['total_tax']     ?? 0);

    return [
        'total_records'     => (int)($res['total_records'] ?? 0),
        'invoice_value'     => $taxable + $ttax,
        'taxable_value'     => $taxable,
        'igst'              => $igst,
        'cgst'              => $cgst,
        'sgst'              => $sgst,
        'cess'              => $cess,
        'total_tax'         => $ttax,
        'hsn_ignore_footer' => false,
    ];
}

protected function gstr1RunQueryForConfigaaa(array $conf, string $from_date, string $to_date): array
{
    // CANCELLED DOCS => all zeros
    if (!empty($conf['doc_cancel'])) {
        return [
            'total_records' => 0,
            'invoice_value' => 0,
            'taxable_value' => 0,
            'igst'          => 0,
            'cgst'          => 0,
            'sgst'          => 0,
            'cess'          => 0,
            'total_tax'     => 0,
            'hsn_ignore_footer' => false,
        ];
    }

    // DOCUMENT ISSUED / NET ISSUED
    if (!empty($conf['doc_count']) || !empty($conf['doc_net'])) {
        $b = $this->db->table('vchgstsumn s');
        $b->join('vchtxnconso t', 't.vch_txn_id = s.vch_txn_id', 'inner');
        $b->select('COUNT(DISTINCT s.vch_txn_id) AS c', false);
        $b->where('s.cmp_id', $this->company_id);
        $b->where('s.vch_date >=', $from_date);
        $b->where('s.vch_date <=', $to_date);
        $b->whereIn('s.acc_bsd_type', [1,3]);
        $b->whereIn('s.is_outward', [1,2]); // outward + credit notes

        if (!empty($this->bo_id)) {
            $b->where('t.hobo_id', $this->bo_id);
        }

        $row = $b->get()->getRowArray();
        $count = (int)($row['c'] ?? 0);

        return [
            'total_records' => $count,
            'invoice_value' => 0,
            'taxable_value' => 0,
            'igst'          => 0,
            'cgst'          => 0,
            'sgst'          => 0,
            'cess'          => 0,
            'total_tax'     => 0,
            'hsn_ignore_footer' => false,
        ];
    }

    // HSN SUMMARY (table 12)
    if (!empty($conf['hsn_summary'])) {
        $b = $this->db->table('vchgstsumn s');
        $b->join('vchtxnconso t', 't.vch_txn_id = s.vch_txn_id', 'inner');
        $b->select("
            COUNT(DISTINCT s.vch_txn_id)         AS total_records,
            COALESCE(SUM(s.vch_taxable_value),0) AS taxable_value,
            COALESCE(SUM(s.vch_igst),0)          AS igst,
            COALESCE(SUM(s.vch_cgst),0)          AS cgst,
            COALESCE(SUM(s.vch_sgst_ugst),0)     AS sgst,
            COALESCE(SUM(s.vch_cess),0)          AS cess,
            COALESCE(SUM(s.vch_total_tax),0)     AS total_tax
        ", false);
        $b->where('s.cmp_id', $this->company_id);
        $b->where('s.vch_date >=', $from_date);
        $b->where('s.vch_date <=', $to_date);
        $b->whereIn('s.is_outward', [1,2]);
       
        if (!empty($this->bo_id)) {
            $b->where('t.hobo_id', $this->bo_id);
        }

        $res = $b->get()->getRowArray();
        $taxable = (float)($res['taxable_value'] ?? 0);
        $igst    = (float)($res['igst'] ?? 0);
        $cgst    = (float)($res['cgst'] ?? 0);
        $sgst    = (float)($res['sgst'] ?? 0);
        $cess    = (float)($res['cess'] ?? 0);
        $ttax    = (float)($res['total_tax'] ?? 0);
        $invoice = $taxable + $ttax;

        return [
            'total_records' => (int)($res['total_records'] ?? 0),
            'invoice_value' => $invoice,
            'taxable_value' => $taxable,
            'igst'          => $igst,
            'cgst'          => $cgst,
            'sgst'          => $sgst,
            'cess'          => $cess,
            'total_tax'     => $ttax,
            'hsn_ignore_footer' => true,
        ];
    }

    if (!empty($conf['no_data'])) {
        return [
            'total_records' => 0,
            'invoice_value' => 0,
            'taxable_value' => 0,
            'igst'          => 0,
            'cgst'          => 0,
            'sgst'          => 0,
            'cess'          => 0,
            'total_tax'     => 0,
            'hsn_ignore_footer' => false,
        ];
    }

    // Default path
    $db = $this->db->table('vchgstsumn s');
    $db->join('vchtxnconso t', 't.vch_txn_id = s.vch_txn_id', 'inner');

    if (!empty($conf['credit_note'])) {
        $db->join('vchtxnconso vc', 'vc.vch_txn_id = s.vch_txn_id', 'left');
    }

    $db->select("
        COUNT(DISTINCT s.vch_txn_id)         AS total_records,
        COALESCE(SUM(s.vch_taxable_value),0) AS taxable_value,
        COALESCE(SUM(s.vch_igst),0)          AS igst,
        COALESCE(SUM(s.vch_cgst),0)          AS cgst,
        COALESCE(SUM(s.vch_sgst_ugst),0)     AS sgst,
        COALESCE(SUM(s.vch_cess),0)          AS cess,
        COALESCE(SUM(s.vch_total_tax),0)     AS total_tax
    ", false);

    $db->where('s.cmp_id', $this->company_id);
    $db->where('s.vch_date >=', $from_date);
    $db->where('s.vch_date <=', $to_date);

    if (!empty($this->bo_id)) {
        $db->where('t.hobo_id', $this->bo_id);
    }

    if (!empty($conf['inv_supply_id'])) {
        $db->whereIn('s.inv_supply_id', $conf['inv_supply_id']);
    }

    if (!empty($conf['rev_chg'])) {
        $db->join('gstroutsup go', 'go.vch_txn_id = s.vch_txn_id', 'left');
        $db->where('go.outsup_rev_chg', 1);
    }

    if (!empty($conf['credit_note'])) {
        $db->where('vc.vch_type_id', 2);
        $db->where('s.is_outward', 2);
    }

    $res = $db->get()->getRowArray();

    $taxable = (float)($res['taxable_value'] ?? 0);
    $igst    = (float)($res['igst'] ?? 0);
    $cgst    = (float)($res['cgst'] ?? 0);
    $sgst    = (float)($res['sgst'] ?? 0);
    $cess    = (float)($res['cess'] ?? 0);
    $ttax    = (float)($res['total_tax'] ?? 0);
    $invoice = $taxable + $ttax;

    return [
        'total_records' => (int)($res['total_records'] ?? 0),
        'invoice_value' => $invoice,
        'taxable_value' => $taxable,
        'igst'          => $igst,
        'cgst'          => $cgst,
        'sgst'          => $sgst,
        'cess'          => $cess,
        'total_tax'     => $ttax,
        'hsn_ignore_footer' => false,
    ];
}

protected function gstr1RunQueryForConfigOldee(array $conf, string $from_date, string $to_date): array
{
    // CANCELLED DOCS => all zeros
    if (!empty($conf['doc_cancel'])) {
        return [
            'total_records' => 0,
            'invoice_value' => 0,
            'taxable_value' => 0,
            'igst'          => 0,
            'cgst'          => 0,
            'sgst'          => 0,
            'cess'          => 0,
            'total_tax'     => 0,
            'hsn_ignore_footer' => false,
        ];
    }

    // DOCUMENT ISSUED / NET ISSUED: count GSTR-1 relevant vouchers
    if (!empty($conf['doc_count']) || !empty($conf['doc_net'])) {
        $b = $this->db->table('vchgstsumn s');
        $b->select('COUNT(DISTINCT s.vch_txn_id) AS c', false);
        $b->where('s.cmp_id', $this->company_id);
        $b->where('s.vch_date >=', $from_date);
        $b->where('s.vch_date <=', $to_date);
        $b->whereIn('s.acc_bsd_type', [1,3]);
        $b->whereIn('s.is_outward', [1,2]); // outward + credit notes
        
        $row = $b->get()->getRowArray();
        $count = (int)($row['c'] ?? 0);

        return [
            'total_records' => $count,
            'invoice_value' => 0,
            'taxable_value' => 0,
            'igst'          => 0,
            'cgst'          => 0,
            'sgst'          => 0,
            'cess'          => 0,
            'total_tax'     => 0,
            'hsn_ignore_footer' => false,
        ];
    }

    // HSN SUMMARY (table 12): sum values for items (acc_bsd_type = 3) and is_outward in (1,2)
    if (!empty($conf['hsn_summary'])) {
        $b = $this->db->table('vchgstsumn s');
        $b->select("
            COUNT(DISTINCT s.vch_txn_id)       AS total_records,
            COALESCE(SUM(s.vch_taxable_value),0) AS taxable_value,
            COALESCE(SUM(s.vch_igst),0)          AS igst,
            COALESCE(SUM(s.vch_cgst),0)          AS cgst,
            COALESCE(SUM(s.vch_sgst_ugst),0)     AS sgst,
            COALESCE(SUM(s.vch_cess),0)          AS cess,
            COALESCE(SUM(s.vch_total_tax),0)     AS total_tax
        ", false);
        $b->where('s.cmp_id', $this->company_id);
        $b->where('s.vch_date >=', $from_date);
        $b->where('s.vch_date <=', $to_date);
        $b->whereIn('s.is_outward', [1,2]);
        $b->where('s.acc_bsd_type', 3); // items only
       
        $res = $b->get()->getRowArray();
        $taxable = (float)($res['taxable_value'] ?? 0);
        $igst    = (float)($res['igst'] ?? 0);
        $cgst    = (float)($res['cgst'] ?? 0);
        $sgst    = (float)($res['sgst'] ?? 0);
        $cess    = (float)($res['cess'] ?? 0);
        $ttax    = (float)($res['total_tax'] ?? 0);
        $invoice = $taxable + $ttax;

        return [
            'total_records' => (int)($res['total_records'] ?? 0),
            'invoice_value' => $invoice,
            'taxable_value' => $taxable,
            'igst'          => $igst,
            'cgst'          => $cgst,
            'sgst'          => $sgst,
            'cess'          => $cess,
            'total_tax'     => $ttax,
            'hsn_ignore_footer' => true, // display values but don't sum in footer
        ];
    }

    if (!empty($conf['no_data'])) {
        return [
            'total_records' => 0,
            'invoice_value' => 0,
            'taxable_value' => 0,
            'igst'          => 0,
            'cgst'          => 0,
            'sgst'          => 0,
            'cess'          => 0,
            'total_tax'     => 0,
            'hsn_ignore_footer' => false,
        ];
    }

    // Default path
    $db = $this->db->table('vchgstsumn s');

    $needVcJoin = !empty($conf['credit_note']);
    if ($needVcJoin) {
        $db->join('vchtxnconso vc', 'vc.vch_txn_id = s.vch_txn_id', 'left');
    }

    $db->select("
        COUNT(*)                             AS total_records,
        COALESCE(SUM(s.vch_taxable_value),0) AS taxable_value,
        COALESCE(SUM(s.vch_igst),0)          AS igst,
        COALESCE(SUM(s.vch_cgst),0)          AS cgst,
        COALESCE(SUM(s.vch_sgst_ugst),0)     AS sgst,
        COALESCE(SUM(s.vch_cess),0)          AS cess,
        COALESCE(SUM(s.vch_total_tax),0)     AS total_tax
    ", false);

    $db->where('s.cmp_id', $this->company_id);
    $db->where('s.vch_date >=', $from_date);
    $db->where('s.vch_date <=', $to_date);
    
    if (!empty($conf['inv_supply_id'])) {
        $db->whereIn('s.inv_supply_id', $conf['inv_supply_id']);
    }

    if (!empty($conf['rev_chg'])) {
        $db->join('gstroutsup go', 'go.vch_txn_id = s.vch_txn_id', 'left');
        $db->where('go.outsup_rev_chg', 1);
    }

    if (!empty($conf['credit_note'])) {
        $db->where('vc.vch_type_id', 2);
        $db->where('s.is_outward', 2);
    }

    $res = $db->get()->getRowArray();

    $taxable = (float)($res['taxable_value'] ?? 0);
    $igst    = (float)($res['igst'] ?? 0);
    $cgst    = (float)($res['cgst'] ?? 0);
    $sgst    = (float)($res['sgst'] ?? 0);
    $cess    = (float)($res['cess'] ?? 0);
    $ttax    = (float)($res['total_tax'] ?? 0);
    $invoice = $taxable + $ttax;

    return [
        'total_records' => (int)($res['total_records'] ?? 0),
        'invoice_value' => $invoice,
        'taxable_value' => $taxable,
        'igst'          => $igst,
        'cgst'          => $cgst,
        'sgst'          => $sgst,
        'cess'          => $cess,
        'total_tax'     => $ttax,
        'hsn_ignore_footer' => false,
    ];
}
	 
    /**************** End Of  GSTR1  Report condensed & Detailed ***************/
		
	
	
	public function load_gstsummary_condensed(string $fromYmd, string $toYmd, array $tableLists, ? int $isOutward = null,$compId=null, $boId=null,$fyId=null,$reportData=null): array
{
    
     if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
		if($fyId)
			$sel_fyId = $fyId;
		 else 
			$sel_fyId = $this->fy_id;
			
    // Use PostgreSQL connection
    $pg = $this->db;
    
    $cmpId  = (int)$sel_compId;
    $hoboId = (int)$sel_boId;
    
    // Build WHERE clause
    $whereConditions = [];
    $whereConditions[] = "t.vch_date >= '$fromYmd'";
    $whereConditions[] = "t.vch_date <= '$toYmd'";
    $whereConditions[] = "s.acc_bsd_type IN (1, 3)";
    
    if (! empty($cmpId)) {
        $whereConditions[] = "t. cmp_id = $cmpId";
    }
    
    if (!empty($hoboId)) {
        $whereConditions[] = "t.hobo_id = $hoboId";
    }
    
    $where = implode(' AND ', $whereConditions);

    /*
     * Filter Logic:
     * OUTPUT TAX:           vch_type_id = 18 AND is_outward = 1
     * OUTPUT TAX CR NOTE:   vch_type_id = 2  AND is_outward = 2
     * INPUT TAX:             vch_type_id = 11 AND is_outward = 2
     * INPUT TAX DR NOTE:    vch_type_id = 3  AND is_outward = 1
     */

    $sql = "
        SELECT
            -- OUTPUT TAX (vch_type_id = 18 AND is_outward = 1)
            COALESCE(SUM(CASE WHEN t. vch_type_id = 18 AND s.is_outward = 1 THEN s.vch_taxable_value END), 0) AS otax_taxable,
            COALESCE(SUM(CASE WHEN t.vch_type_id = 18 AND s.is_outward = 1 THEN s.vch_igst END), 0) AS otax_igst,
            COALESCE(SUM(CASE WHEN t.vch_type_id = 18 AND s. is_outward = 1 THEN s.vch_cgst END), 0) AS otax_cgst,
            COALESCE(SUM(CASE WHEN t.vch_type_id = 18 AND s.is_outward = 1 THEN s.vch_sgst_ugst END), 0) AS otax_sgst,
            COALESCE(SUM(CASE WHEN t.vch_type_id = 18 AND s.is_outward = 1 THEN s.vch_cess END), 0) AS otax_cess,
            COALESCE(SUM(CASE WHEN t.vch_type_id = 18 AND s. is_outward = 1 THEN s.vch_total_tax END), 0) AS otax_total,

            -- OUTPUT TAX CR NOTE (vch_type_id = 2 AND is_outward = 2)
            COALESCE(SUM(CASE WHEN t.vch_type_id = 2 AND s. is_outward = 2 THEN s.vch_taxable_value END), 0) AS otax_cr_taxable,
            COALESCE(SUM(CASE WHEN t. vch_type_id = 2 AND s.is_outward = 2 THEN s. vch_igst END), 0) AS otax_cr_igst,
            COALESCE(SUM(CASE WHEN t. vch_type_id = 2 AND s.is_outward = 2 THEN s. vch_cgst END), 0) AS otax_cr_cgst,
            COALESCE(SUM(CASE WHEN t. vch_type_id = 2 AND s.is_outward = 2 THEN s. vch_sgst_ugst END), 0) AS otax_cr_sgst,
            COALESCE(SUM(CASE WHEN t.vch_type_id = 2 AND s.is_outward = 2 THEN s.vch_cess END), 0) AS otax_cr_cess,
            COALESCE(SUM(CASE WHEN t.vch_type_id = 2 AND s.is_outward = 2 THEN s.vch_total_tax END), 0) AS otax_cr_total,

            -- INPUT TAX (vch_type_id = 11 AND is_outward = 2)
            COALESCE(SUM(CASE WHEN t.vch_type_id = 11 AND s.is_outward = 2 THEN s.vch_taxable_value END), 0) AS itax_taxable,
            COALESCE(SUM(CASE WHEN t.vch_type_id = 11 AND s.is_outward = 2 THEN s.vch_igst END), 0) AS itax_igst,
            COALESCE(SUM(CASE WHEN t.vch_type_id = 11 AND s. is_outward = 2 THEN s.vch_cgst END), 0) AS itax_cgst,
            COALESCE(SUM(CASE WHEN t. vch_type_id = 11 AND s.is_outward = 2 THEN s. vch_sgst_ugst END), 0) AS itax_sgst,
            COALESCE(SUM(CASE WHEN t.vch_type_id = 11 AND s.is_outward = 2 THEN s.vch_cess END), 0) AS itax_cess,
            COALESCE(SUM(CASE WHEN t.vch_type_id = 11 AND s.is_outward = 2 THEN s.vch_total_tax END), 0) AS itax_total,

            -- INPUT TAX DR NOTE (vch_type_id = 3 AND is_outward = 1)
            COALESCE(SUM(CASE WHEN t. vch_type_id = 3 AND s.is_outward = 1 THEN s. vch_taxable_value END), 0) AS itax_dr_taxable,
            COALESCE(SUM(CASE WHEN t. vch_type_id = 3 AND s.is_outward = 1 THEN s. vch_igst END), 0) AS itax_dr_igst,
            COALESCE(SUM(CASE WHEN t. vch_type_id = 3 AND s.is_outward = 1 THEN s. vch_cgst END), 0) AS itax_dr_cgst,
            COALESCE(SUM(CASE WHEN t. vch_type_id = 3 AND s.is_outward = 1 THEN s. vch_sgst_ugst END), 0) AS itax_dr_sgst,
            COALESCE(SUM(CASE WHEN t.vch_type_id = 3 AND s.is_outward = 1 THEN s.vch_cess END), 0) AS itax_dr_cess,
            COALESCE(SUM(CASE WHEN t.vch_type_id = 3 AND s.is_outward = 1 THEN s.vch_total_tax END), 0) AS itax_dr_total
        FROM vchgstsumn s
        INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
        WHERE {$where}
    ";

    $query = $pg->query($sql);
    $row = $query->getRowArray();
    
    // Handle null result
    if (! $row) {
        $row = [
            'otax_taxable' => 0, 'otax_igst' => 0, 'otax_cgst' => 0, 'otax_sgst' => 0, 'otax_cess' => 0, 'otax_total' => 0,
            'otax_cr_taxable' => 0, 'otax_cr_igst' => 0, 'otax_cr_cgst' => 0, 'otax_cr_sgst' => 0, 'otax_cr_cess' => 0, 'otax_cr_total' => 0,
            'itax_taxable' => 0, 'itax_igst' => 0, 'itax_cgst' => 0, 'itax_sgst' => 0, 'itax_cess' => 0, 'itax_total' => 0,
            'itax_dr_taxable' => 0, 'itax_dr_igst' => 0, 'itax_dr_cgst' => 0, 'itax_dr_sgst' => 0, 'itax_dr_cess' => 0, 'itax_dr_total' => 0,
        ];
    }

    // --- bucket helper using formatAmount ---
    $mkBucket = function (float $taxable, float $igst, float $cgst, float $sgst, float $cess, float $total) {
        $invoice = $taxable + $total;
        return [
            'invoice_value'    => formatAmount($invoice),
            'taxable_value'    => formatAmount($taxable),
            'igst'             => formatAmount($igst),
            'cgst'             => formatAmount($cgst),
            'sgst'             => formatAmount($sgst),
            'cess'             => formatAmount($cess),
            'total_tax'        => formatAmount($total),
            'sm_invoice_value' => (float)$invoice,
            'sm_taxable_value' => (float)$taxable,
            'sm_igst'          => (float)$igst,
            'sm_cgst'          => (float)$cgst,
            'sm_sgst'          => (float)$sgst,
            'sm_cess'          => (float)$cess,
            'sm_total_tax'     => (float)$total,
        ];
    };

    // --- buckets ---
    $otax = $mkBucket(
        (float)($row['otax_taxable'] ?? 0),
        (float)($row['otax_igst'] ?? 0),
        (float)($row['otax_cgst'] ?? 0),
        (float)($row['otax_sgst'] ??  0),
        (float)($row['otax_cess'] ?? 0),
        (float)($row['otax_total'] ??  0)
    );
    
    $otax_cr = $mkBucket(
        (float)($row['otax_cr_taxable'] ?? 0),
        (float)($row['otax_cr_igst'] ?? 0),
        (float)($row['otax_cr_cgst'] ?? 0),
        (float)($row['otax_cr_sgst'] ?? 0),
        (float)($row['otax_cr_cess'] ?? 0),
        (float)($row['otax_cr_total'] ?? 0)
    );

    $total_o_raw = [
        'invoice' => $otax['sm_invoice_value'] + $otax_cr['sm_invoice_value'],
        'taxable' => $otax['sm_taxable_value'] + $otax_cr['sm_taxable_value'],
        'igst'    => $otax['sm_igst'] + $otax_cr['sm_igst'],
        'cgst'    => $otax['sm_cgst'] + $otax_cr['sm_cgst'],
        'sgst'    => $otax['sm_sgst'] + $otax_cr['sm_sgst'],
        'cess'    => $otax['sm_cess'] + $otax_cr['sm_cess'],
        'total'   => $otax['sm_total_tax'] + $otax_cr['sm_total_tax'],
    ];
    $total_o = $mkBucket($total_o_raw['taxable'], $total_o_raw['igst'], $total_o_raw['cgst'], $total_o_raw['sgst'], $total_o_raw['cess'], $total_o_raw['total']);

    $itax = $mkBucket(
        (float)($row['itax_taxable'] ?? 0),
        (float)($row['itax_igst'] ??  0),
        (float)($row['itax_cgst'] ?? 0),
        (float)($row['itax_sgst'] ?? 0),
        (float)($row['itax_cess'] ?? 0),
        (float)($row['itax_total'] ?? 0)
    );
    
    $itax_dr = $mkBucket(
        (float)($row['itax_dr_taxable'] ?? 0),
        (float)($row['itax_dr_igst'] ?? 0),
        (float)($row['itax_dr_cgst'] ?? 0),
        (float)($row['itax_dr_sgst'] ?? 0),
        (float)($row['itax_dr_cess'] ?? 0),
        (float)($row['itax_dr_total'] ?? 0)
    );

    $total_i_raw = [
        'invoice' => $itax['sm_invoice_value'] + $itax_dr['sm_invoice_value'],
        'taxable' => $itax['sm_taxable_value'] + $itax_dr['sm_taxable_value'],
        'igst'    => $itax['sm_igst'] + $itax_dr['sm_igst'],
        'cgst'    => $itax['sm_cgst'] + $itax_dr['sm_cgst'],
        'sgst'    => $itax['sm_sgst'] + $itax_dr['sm_sgst'],
        'cess'    => $itax['sm_cess'] + $itax_dr['sm_cess'],
        'total'   => $itax['sm_total_tax'] + $itax_dr['sm_total_tax'],
    ];
    $total_i = $mkBucket($total_i_raw['taxable'], $total_i_raw['igst'], $total_i_raw['cgst'], $total_i_raw['sgst'], $total_i_raw['cess'], $total_i_raw['total']);

    // --- rows ---
    $rows = [];
    
    $pushRow = function (string $key, string $name, array $vals, string $tag, ? int $outward, string $sign) use (&$rows, $fromYmd, $toYmd) {
        $rows[] = array_merge([
            'ishsn'      => 0,
            'table_key'  => $key,
            'from_date'  => $fromYmd,
            'to_date'    => $toYmd,
            'table_name' => $name,
            'tag'        => $tag,
            'params'     => $outward ?  [
                'from'       => $fromYmd,
                'to'         => $toYmd,
                'is_outward' => $outward,
                'sign'       => $sign,
            ] : null,
        ], $vals);
    };
    
    $blankRow = function () use (&$rows, $fromYmd, $toYmd) {
        $z = formatAmount(0);
        $rows[] = [
            'ishsn' => 0,
            'table_key' => '',
            'from_date' => $fromYmd,
            'to_date' => $toYmd,
            'table_name' => '',
            'invoice_value' => $z,
            'taxable_value' => $z,
            'igst' => $z,
            'cgst' => $z,
            'sgst' => $z,
            'cess' => $z,
            'total_tax' => $z,
            'sm_invoice_value' => 0,
            'sm_taxable_value' => 0,
            'sm_igst' => 0,
            'sm_cgst' => 0,
            'sm_sgst' => 0,
            'sm_cess' => 0,
            'sm_total_tax' => 0,
            'tag' => 'divider',
            'params' => null,
        ];
    };

    // OUTPUT TAX section
    if ($isOutward === null || $isOutward === 1) {
        $pushRow('otax', ($tableLists['otax'] ?? 'OUTPUT TAX'), $otax, 'outward_invoices', 1, 'pos');
        $pushRow('otax_cr', ($tableLists['otax_cr'] ?? 'OUTPUT TAX (CR.  NOTE)'), $otax_cr, 'outward_cr', 1, 'neg');
        $pushRow('total_otax', '<strong>' . ($tableLists['total_otax'] ?? 'TOTAL OUTPUT TAX') . '</strong>', $total_o, 'outward_total', 1, 'all');
    }
    
    if ($isOutward === null) {
        $blankRow();
    }
    
    // INPUT TAX section
    if ($isOutward === null || $isOutward === 2) {
        $pushRow('itax', ($tableLists['itax'] ?? 'INPUT TAX'), $itax, 'inward_purchases', 2, 'pos');
        $pushRow('itax_dr', ($tableLists['itax_dr'] ?? 'INPUT TAX (DR. NOTE)'), $itax_dr, 'inward_dr', 2, 'neg');
        $pushRow('total_itax', '<strong>' . ($tableLists['total_itax'] ??  'TOTAL INPUT TAX') . '</strong>', $total_i, 'inward_total', 2, 'all');
    }
    
    $blankRow();

    return [
        'totalRecords' => count($rows),
        'curPage'      => '1',
        'data'         => $rows,
    ];
}

public function load_gstsummary_detailed(string $fromYmd, string $toYmd, ? int $isOutward = null,$compId=null,$boId=null,$fyId=null): array
{
     if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
		if($fyId)
			$sel_fyId = $fyId;
		 else 
			$sel_fyId = $this->fy_id;
			
    // Use PostgreSQL connection
    $pg = $this->db;
    
    $cmpId  = (int)$sel_compId;
    $hoboId = (int)$sel_boId;
    
    // Build WHERE clause
    $whereConditions = [];
    $whereConditions[] = "t.vch_date >= '$fromYmd'";
    $whereConditions[] = "t. vch_date <= '$toYmd'";
    $whereConditions[] = "s.acc_bsd_type IN (1, 3)";
    
    if (! empty($cmpId)) {
        $whereConditions[] = "t.cmp_id = $cmpId";
    }
    
    if (!empty($hoboId)) {
        $whereConditions[] = "t.hobo_id = $hoboId";
    }
    
    $where = implode(' AND ', $whereConditions);

    /*
     * Filter Logic:
     * OUTPUT TAX:            vch_type_id = 18 AND is_outward = 1
     * OUTPUT TAX CR NOTE:    vch_type_id = 2  AND is_outward = 2
     * INPUT TAX:            vch_type_id = 11 AND is_outward = 2
     * INPUT TAX DR NOTE:    vch_type_id = 3  AND is_outward = 1
     *
     * gst_rate_grp format:  "type,rate,cess"
     * type: 1 = Regular, 2 = Composition
     * rate: GST rate (0, 5, 12, 18, 28, etc.)
     * cess: Cess percentage
     */

    // Query to get rate-wise breakdown - wrapped in subquery to use aliases
    $sql = "
    SELECT * FROM (
        SELECT
            CASE 
                WHEN t.vch_type_id = 18 AND s.is_outward = 1 THEN 'otax'
                WHEN t.vch_type_id = 2 AND s.is_outward = 2 THEN 'otax_cr'
                WHEN t.vch_type_id = 11 AND s.is_outward = 2 THEN 'itax'
                WHEN t.vch_type_id = 3 AND s.is_outward = 1 THEN 'itax_dr'
                ELSE 'other'
            END AS tax_category,

          
            COALESCE(NULLIF(SPLIT_PART(REPLACE(COALESCE(s.gst_rate_grp,'1,0,0'),'_',','), ',', 1), ''), '1')::INTEGER AS gst_type,
            COALESCE(NULLIF(SPLIT_PART(REPLACE(COALESCE(s.gst_rate_grp,'1,0,0'),'_',','), ',', 2), ''), '0')::DECIMAL(10,2) AS gst_rate,
            COALESCE(NULLIF(SPLIT_PART(REPLACE(COALESCE(s.gst_rate_grp,'1,0,0'),'_',','), ',', 3), ''), '0')::DECIMAL(10,2) AS cess_rate,
           

            COALESCE(SUM(s.vch_taxable_value), 0) AS taxable_value,
            COALESCE(SUM(s.vch_igst), 0) AS igst,
            COALESCE(SUM(s.vch_cgst), 0) AS cgst,
            COALESCE(SUM(s.vch_sgst_ugst), 0) AS sgst,
            COALESCE(SUM(s.vch_cess), 0) AS cess,
            COALESCE(SUM(s.vch_total_tax), 0) AS total_tax

        FROM vchgstsumn s
        INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id

        WHERE {$where}
          AND (
              (t.vch_type_id = 18 AND s.is_outward = 1) OR
              (t.vch_type_id = 2 AND s.is_outward = 2) OR
              (t.vch_type_id = 11 AND s.is_outward = 2) OR
              (t.vch_type_id = 3 AND s.is_outward = 1)
          )

        GROUP BY 
            CASE 
                WHEN t.vch_type_id = 18 AND s.is_outward = 1 THEN 'otax'
                WHEN t.vch_type_id = 2 AND s.is_outward = 2 THEN 'otax_cr'
                WHEN t.vch_type_id = 11 AND s.is_outward = 2 THEN 'itax'
                WHEN t.vch_type_id = 3 AND s.is_outward = 1 THEN 'itax_dr'
                ELSE 'other'
            END,

           
            COALESCE(NULLIF(SPLIT_PART(REPLACE(COALESCE(s.gst_rate_grp,'1,0,0'),'_',','), ',', 1), ''), '1')::INTEGER,
            COALESCE(NULLIF(SPLIT_PART(REPLACE(COALESCE(s.gst_rate_grp,'1,0,0'),'_',','), ',', 2), ''), '0')::DECIMAL(10,2),
            COALESCE(NULLIF(SPLIT_PART(REPLACE(COALESCE(s.gst_rate_grp,'1,0,0'),'_',','), ',', 3), ''), '0')::DECIMAL(10,2)

    ) AS subquery

    ORDER BY 
        CASE tax_category 
            WHEN 'otax' THEN 1 
            WHEN 'otax_cr' THEN 2 
            WHEN 'itax' THEN 3 
            WHEN 'itax_dr' THEN 4 
            ELSE 5 
        END,
        gst_type,
        gst_rate,
        cess_rate
";

    $query = $pg->query($sql);
    $rows = $query ?  $query->getResultArray() : [];

    // Organize data by category
    $categories = [
        'otax' => ['label' => 'OUTPUT TAX', 'rows' => [], 'total' => null],
        'otax_cr' => ['label' => 'OUTPUT TAX (CR. NOTE)', 'rows' => [], 'total' => null],
        'itax' => ['label' => 'INPUT TAX', 'rows' => [], 'total' => null],
        'itax_dr' => ['label' => 'INPUT TAX (DR. NOTE)', 'rows' => [], 'total' => null],
    ];

    foreach ($rows as $r) {
        $cat = $r['tax_category'] ?? 'other';
        if (isset($categories[$cat])) {
            $categories[$cat]['rows'][] = $r;
        }
    }

    // Helper to create rate label
    $getRateLabel = function ($gstType, $gstRate, $cessRate) {
        $rate = (float)$gstRate;
        $cess = (float)$cessRate;
        $type = (int)$gstType;
        
        // NIL rated
        if ($rate == 0 && $type == 1) {
            return '» NIL RATED SUPPLY';
        }
        
        // Composition
        if ($type == 2) {
            $label = '» GST @ ' . rtrim(rtrim(number_format($rate, 2), '0'), '.') . '% (Composition)';
            if ($cess > 0) {
                $label .= ' + Cess @ ' . rtrim(rtrim(number_format($cess, 2), '0'), '.') . '%';
            }
            return $label;
        }
        
        // Regular GST
        $label = '» GST @ ' . rtrim(rtrim(number_format($rate, 2), '0'), '.') . '%';
        if ($cess > 0) {
            $label .= ' + Cess @ ' . rtrim(rtrim(number_format($cess, 2), '0'), '.') . '%';
        }
        return $label;
    };

    // Helper to create bucket
    $mkBucket = function (float $taxable, float $igst, float $cgst, float $sgst, float $cess, float $total) {
        $invoice = $taxable + $total;
        return [
            'invoice_value'    => formatAmount($invoice),
            'taxable_value'    => formatAmount($taxable),
            'igst'             => formatAmount($igst),
            'cgst'             => formatAmount($cgst),
            'sgst'             => formatAmount($sgst),
            'cess'             => formatAmount($cess),
            'total_tax'        => formatAmount($total),
            'sm_invoice_value' => (float)$invoice,
            'sm_taxable_value' => (float)$taxable,
            'sm_igst'          => (float)$igst,
            'sm_cgst'          => (float)$cgst,
            'sm_sgst'          => (float)$sgst,
            'sm_cess'          => (float)$cess,
            'sm_total_tax'     => (float)$total,
        ];
    };

    // Calculate category totals
    foreach ($categories as $catKey => &$catData) {
        $totTaxable = 0;
        $totIgst = 0;
        $totCgst = 0;
        $totSgst = 0;
        $totCess = 0;
        $totTotal = 0;
        
        foreach ($catData['rows'] as $r) {
            $totTaxable += (float)($r['taxable_value'] ?? 0);
            $totIgst += (float)($r['igst'] ?? 0);
            $totCgst += (float)($r['cgst'] ?? 0);
            $totSgst += (float)($r['sgst'] ?? 0);
            $totCess += (float)($r['cess'] ?? 0);
            $totTotal += (float)($r['total_tax'] ?? 0);
        }
        
        $catData['total'] = $mkBucket($totTaxable, $totIgst, $totCgst, $totSgst, $totCess, $totTotal);
    }
    unset($catData);

    // Build output rows
    $out = [];

    $pushRow = function (string $tableKey, string $label, array $vals, string $tag, array $params = []) use (&$out, $fromYmd, $toYmd) {
        $out[] = array_merge([
            'ishsn'      => 0,
            'table_key'  => $tableKey,
            'from_date'  => $fromYmd,
            'to_date'    => $toYmd,
            'table_name' => $label,
            'tag'        => $tag,
            'params'     => empty($params) ? null : $params,
        ], $vals);
    };

    $blankRow = function () use (&$out, $fromYmd, $toYmd) {
        $z = formatAmount(0);
        $out[] = [
            'ishsn' => 0,
            'table_key' => '',
            'from_date' => $fromYmd,
            'to_date' => $toYmd,
            'table_name' => '',
            'invoice_value' => $z,
            'taxable_value' => $z,
            'igst' => $z,
            'cgst' => $z,
            'sgst' => $z,
            'cess' => $z,
            'total_tax' => $z,
            'sm_invoice_value' => 0,
            'sm_taxable_value' => 0,
            'sm_igst' => 0,
            'sm_cgst' => 0,
            'sm_sgst' => 0,
            'sm_cess' => 0,
            'sm_total_tax' => 0,
            'tag' => 'divider',
            'params' => null,
        ];
    };

    // ===== OUTPUT TAX Section =====
    if ($isOutward === null || $isOutward === 1) {
        // OUTPUT TAX Header
        $pushRow(
            'otax_hdr',
            '<strong>OUTPUT TAX</strong>',
            $categories['otax']['total'],
            'otax_header',
            ['from' => $fromYmd, 'to' => $toYmd, 'is_outward' => 1, 'type' => 'otax']
        );
        
        // OUTPUT TAX Rate-wise rows
        foreach ($categories['otax']['rows'] as $r) {
            $label = $getRateLabel($r['gst_type'], $r['gst_rate'], $r['cess_rate']);
            $vals = $mkBucket(
                (float)($r['taxable_value'] ??  0),
                (float)($r['igst'] ?? 0),
                (float)($r['cgst'] ?? 0),
                (float)($r['sgst'] ?? 0),
                (float)($r['cess'] ?? 0),
                (float)($r['total_tax'] ?? 0)
            );
            $pushRow(
                'otax_rate_' . $r['gst_type'] . '_' . $r['gst_rate'] . '_' .  $r['cess_rate'],
                $label,
                $vals,
                'otax_detail',
                ['from' => $fromYmd, 'to' => $toYmd, 'is_outward' => 1, 'gst_type' => $r['gst_type'], 'gst_rate' => $r['gst_rate'], 'cess_rate' => $r['cess_rate']]
            );
        }

        // OUTPUT TAX (CR. NOTE) Header
        $pushRow(
            'otax_cr_hdr',
            '<strong>OUTPUT TAX (CR. NOTE)</strong>',
            $categories['otax_cr']['total'],
            'otax_cr_header',
            ['from' => $fromYmd, 'to' => $toYmd, 'is_outward' => 2, 'type' => 'otax_cr']
        );
        
        // OUTPUT TAX (CR. NOTE) Rate-wise rows
        foreach ($categories['otax_cr']['rows'] as $r) {
            $label = $getRateLabel($r['gst_type'], $r['gst_rate'], $r['cess_rate']);
            $vals = $mkBucket(
                (float)($r['taxable_value'] ?? 0),
                (float)($r['igst'] ?? 0),
                (float)($r['cgst'] ?? 0),
                (float)($r['sgst'] ?? 0),
                (float)($r['cess'] ?? 0),
                (float)($r['total_tax'] ??  0)
            );
            $pushRow(
                'otax_cr_rate_' .  $r['gst_type'] . '_' . $r['gst_rate'] . '_' .  $r['cess_rate'],
                $label,
                $vals,
                'otax_cr_detail',
                ['from' => $fromYmd, 'to' => $toYmd, 'is_outward' => 2, 'gst_type' => $r['gst_type'], 'gst_rate' => $r['gst_rate'], 'cess_rate' => $r['cess_rate']]
            );
        }

        // TOTAL OUTPUT TAX
        $totalOutTaxable = $categories['otax']['total']['sm_taxable_value'] + $categories['otax_cr']['total']['sm_taxable_value'];
        $totalOutIgst = $categories['otax']['total']['sm_igst'] + $categories['otax_cr']['total']['sm_igst'];
        $totalOutCgst = $categories['otax']['total']['sm_cgst'] + $categories['otax_cr']['total']['sm_cgst'];
        $totalOutSgst = $categories['otax']['total']['sm_sgst'] + $categories['otax_cr']['total']['sm_sgst'];
        $totalOutCess = $categories['otax']['total']['sm_cess'] + $categories['otax_cr']['total']['sm_cess'];
        $totalOutTotal = $categories['otax']['total']['sm_total_tax'] + $categories['otax_cr']['total']['sm_total_tax'];
        
        $pushRow(
            'total_otax',
            '<strong>TOTAL OUTPUT TAX</strong>',
            $mkBucket($totalOutTaxable, $totalOutIgst, $totalOutCgst, $totalOutSgst, $totalOutCess, $totalOutTotal),
            'outward_total',
            ['from' => $fromYmd, 'to' => $toYmd, 'is_outward' => 1, 'type' => 'total_otax']
        );
        
        $blankRow();
    }

    // ===== INPUT TAX Section =====
    if ($isOutward === null || $isOutward === 2) {
        // INPUT TAX Header
        $pushRow(
            'itax_hdr',
            '<strong>INPUT TAX</strong>',
            $categories['itax']['total'],
            'itax_header',
            ['from' => $fromYmd, 'to' => $toYmd, 'is_outward' => 2, 'type' => 'itax']
        );
        
        // INPUT TAX Rate-wise rows
        foreach ($categories['itax']['rows'] as $r) {
            $label = $getRateLabel($r['gst_type'], $r['gst_rate'], $r['cess_rate']);
            $vals = $mkBucket(
                (float)($r['taxable_value'] ?? 0),
                (float)($r['igst'] ?? 0),
                (float)($r['cgst'] ?? 0),
                (float)($r['sgst'] ?? 0),
                (float)($r['cess'] ?? 0),
                (float)($r['total_tax'] ?? 0)
            );
            $pushRow(
                'itax_rate_' . $r['gst_type'] . '_' . $r['gst_rate'] . '_' . $r['cess_rate'],
                $label,
                $vals,
                'itax_detail',
                ['from' => $fromYmd, 'to' => $toYmd, 'is_outward' => 2, 'gst_type' => $r['gst_type'], 'gst_rate' => $r['gst_rate'], 'cess_rate' => $r['cess_rate']]
            );
        }

        // INPUT TAX (DR. NOTE) Header
        $pushRow(
            'itax_dr_hdr',
            '<strong>INPUT TAX (DR.  NOTE)</strong>',
            $categories['itax_dr']['total'],
            'itax_dr_header',
            ['from' => $fromYmd, 'to' => $toYmd, 'is_outward' => 1, 'type' => 'itax_dr']
        );
        
        // INPUT TAX (DR. NOTE) Rate-wise rows
        foreach ($categories['itax_dr']['rows'] as $r) {
            $label = $getRateLabel($r['gst_type'], $r['gst_rate'], $r['cess_rate']);
            $vals = $mkBucket(
                (float)($r['taxable_value'] ?? 0),
                (float)($r['igst'] ?? 0),
                (float)($r['cgst'] ?? 0),
                (float)($r['sgst'] ?? 0),
                (float)($r['cess'] ?? 0),
                (float)($r['total_tax'] ??  0)
            );
            $pushRow(
                'itax_dr_rate_' .  $r['gst_type'] . '_' . $r['gst_rate'] . '_' .  $r['cess_rate'],
                $label,
                $vals,
                'itax_dr_detail',
                ['from' => $fromYmd, 'to' => $toYmd, 'is_outward' => 1, 'gst_type' => $r['gst_type'], 'gst_rate' => $r['gst_rate'], 'cess_rate' => $r['cess_rate']]
            );
        }

        // TOTAL INPUT TAX
        $totalInTaxable = $categories['itax']['total']['sm_taxable_value'] + $categories['itax_dr']['total']['sm_taxable_value'];
        $totalInIgst = $categories['itax']['total']['sm_igst'] + $categories['itax_dr']['total']['sm_igst'];
        $totalInCgst = $categories['itax']['total']['sm_cgst'] + $categories['itax_dr']['total']['sm_cgst'];
        $totalInSgst = $categories['itax']['total']['sm_sgst'] + $categories['itax_dr']['total']['sm_sgst'];
        $totalInCess = $categories['itax']['total']['sm_cess'] + $categories['itax_dr']['total']['sm_cess'];
        $totalInTotal = $categories['itax']['total']['sm_total_tax'] + $categories['itax_dr']['total']['sm_total_tax'];
        
        $pushRow(
            'total_itax',
            '<strong>TOTAL INPUT TAX</strong>',
            $mkBucket($totalInTaxable, $totalInIgst, $totalInCgst, $totalInSgst, $totalInCess, $totalInTotal),
            'inward_total',
            ['from' => $fromYmd, 'to' => $toYmd, 'is_outward' => 2, 'type' => 'total_itax']
        );
        
        $blankRow();
    }

    return [
        'totalRecords' => count($out),
        'curPage'      => '1',
        'data'         => $out,
    ];
}

public function load_gstsummary_detailedssss(string $fromYmd, string $toYmd, ?int $isOutward = null): array
{
    // ---------- WHERE & PARAMS ----------
    $where  = 't.vch_date >= ? AND t.vch_date <= ? AND s.acc_bsd_type IN (1,3)';
    $params = [$fromYmd, $toYmd];

    if (property_exists($this, 'company_id') && !empty($this->company_id)) {
        $where   .= ' AND t.cmp_id = ?';
        $params[] = $this->company_id;
    }

    $hoboId = (int)$this->bo_id;
    if (!empty($hoboId)) {
        $where   .= ' AND t.hobo_id = ?';
        $params[] = $hoboId;
    }
    if ($isOutward === 1 || $isOutward === 2) {
        $where   .= ' AND s.is_outward = ?';
        $params[] = $isOutward;
    }

    // ---------- 1) SECTION TOTALS ----------
    $sqlTotals = "
        SELECT
            s.is_outward,
            CASE WHEN s.vch_total_tax >= 0 THEN 'pos' ELSE 'neg' END AS sign_grp,
            COALESCE(SUM(s.vch_taxable_value),0) AS taxable_value,
            COALESCE(SUM(s.vch_igst),0)          AS igst,
            COALESCE(SUM(s.vch_cgst),0)          AS cgst,
            COALESCE(SUM(s.vch_sgst_ugst),0)     AS sgst,
            COALESCE(SUM(s.vch_cess),0)          AS cess,
            COALESCE(SUM(s.vch_total_tax),0)     AS total_tax
        FROM vchgstsumn s
        INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
        WHERE {$where}
        GROUP BY s.is_outward, sign_grp
    ";
    $totRows = $this->db->query($sqlTotals, $params)->getResultArray();

    $secSum = [
        '1_pos' => ['taxable'=>0,'igst'=>0,'cgst'=>0,'sgst'=>0,'cess'=>0,'total'=>0],
        '1_neg' => ['taxable'=>0,'igst'=>0,'cgst'=>0,'sgst'=>0,'cess'=>0,'total'=>0],
        '2_pos' => ['taxable'=>0,'igst'=>0,'cgst'=>0,'sgst'=>0,'cess'=>0,'total'=>0],
        '2_neg' => ['taxable'=>0,'igst'=>0,'cgst'=>0,'sgst'=>0,'cess'=>0,'total'=>0],
    ];
    foreach ($totRows as $r) {
        $k = (int)$r['is_outward'] . '_' . $r['sign_grp'];
        if (!isset($secSum[$k])) continue;
        $secSum[$k]['taxable'] += (float)$r['taxable_value'];
        $secSum[$k]['igst']    += (float)$r['igst'];
        $secSum[$k]['cgst']    += (float)$r['cgst'];
        $secSum[$k]['sgst']    += (float)$r['sgst'];
        $secSum[$k]['cess']    += (float)$r['cess'];
        $secSum[$k]['total']   += (float)$r['total_tax'];
    }

    // ---------- 2) RATE GROUPS ----------
    $sqlGroups = "
        SELECT
            s.is_outward,
            CASE WHEN s.vch_total_tax >= 0 THEN 'pos' ELSE 'neg' END AS sign_grp,

            CAST(SUBSTRING_INDEX(REPLACE(REPLACE(COALESCE(s.gst_rate_grp,'0_0_0'), ',', '_'), ' ', ''), '_', 1) AS UNSIGNED)                                AS rate_type,
            CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(COALESCE(s.gst_rate_grp,'0_0_0'), ',', '_'), ' ', ''), '_', 2), '_', -1) AS DECIMAL(10,2)) AS rate_val,
            CAST(SUBSTRING_INDEX(REPLACE(REPLACE(COALESCE(s.gst_rate_grp,'0_0_0'), ',', '_'), ' ', ''), '_', -1) AS DECIMAL(10,2))                           AS cess_val,

            COALESCE(SUM(s.vch_taxable_value),0) AS taxable_value,
            COALESCE(SUM(s.vch_igst),0)          AS igst,
            COALESCE(SUM(s.vch_cgst),0)          AS cgst,
            COALESCE(SUM(s.vch_sgst_ugst),0)     AS sgst,
            COALESCE(SUM(s.vch_cess),0)          AS cess,
            COALESCE(SUM(s.vch_total_tax),0)     AS total_tax
        FROM vchgstsumn s
        INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
        WHERE {$where}
        GROUP BY s.is_outward, sign_grp, rate_type, rate_val, cess_val
    ";
    $grpRows = $this->db->query($sqlGroups, $params)->getResultArray();

    // ---------- helpers ----------
    $fmtRupee = function ($n): string {
        $neg = ($n < 0); $n = abs((float)$n);
        $dec = number_format($n, 2, '.', '');
        [$i,$d] = explode('.', $dec);
        $last3 = substr($i, -3); $rest = substr($i, 0, -3);
        if ($rest !== '') $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest).',';
        return ($neg ? '-&#8377;' : '&#8377;') . $rest . $last3 . '.' . $d;
    };
    $mkVals = function (float $taxable, float $igst, float $cgst, float $sgst, float $cess, float $total) use ($fmtRupee) {
        $inv = $taxable + $total;
        return [
            'invoice_value'    => $fmtRupee($inv),
            'taxable_value'    => $fmtRupee($taxable),
            'igst'             => $fmtRupee($igst),
            'cgst'             => $fmtRupee($cgst),
            'sgst'             => $fmtRupee($sgst),
            'cess'             => $fmtRupee($cess),
            'total_tax'        => $fmtRupee($total),
            // numeric copies for front-end totals
            'sm_invoice_value' => $inv,
            'sm_taxable_value' => $taxable,
            'sm_igst'          => $igst,
            'sm_cgst'          => $cgst,
            'sm_sgst'          => $sgst,
            'sm_cess'          => $cess,
            'sm_total_tax'     => $total,
        ];
    };

    $labelFor = function (int $rateType, $rateVal, $cessVal): string {
        $r = rtrim(rtrim((string)$rateVal, '0'), '.');
        $c = rtrim(rtrim((string)$cessVal, '0'), '.');
        switch ($rateType) {
            case 1: return ($c !== '' && (float)$cessVal > 0) ? "GST @ {$r}% + Cess @{$c}%" : "GST @ {$r}%";
            case 2: return ($c !== '' && (float)$cessVal > 0) ? "GST @ {$r}% + Composition @{$c}%" : "GST @ {$r}% (Composition)";
            case 3: return 'EXPORT(WP)';
            case 4: return 'EXPORT(WOP)';
            case 5: return 'DE';
            case 6: return 'NILL Rated';
            case 7: return 'EXEMPT SUPPLY';
            case 8: return 'SEZ(WP)';
            case 9: return 'SEZ(WOP)';
            default: return 'Other';
        }
    };

    $sortKey = function (int $type, $rate): int {
        $r = (int)round((float)$rate * 10);
        switch ($type) {
            case 6: return 10; case 7: return 11;
            case 3: return 20; case 4: return 21; case 5: return 22;
            case 8: return 30; case 9: return 31;
            case 1: return 1000 + $r;
            case 2: return 2000 + $r;
            default: return 9999;
        }
    };

    // split into sections + "Other" bucket
    $sections = [
        'otax'    => ['is_outward'=>1, 'sign'=>'pos', 'title'=>'OUTPUT TAX'],
        'otax_cr' => ['is_outward'=>1, 'sign'=>'neg', 'title'=>'OUTPUT TAX (CR. NOTE)'],
        'itax'    => ['is_outward'=>2, 'sign'=>'pos', 'title'=>'INPUT TAX'],
        'itax_dr' => ['is_outward'=>2, 'sign'=>'neg', 'title'=>'INPUT TAX (DR. NOTE)'],
    ];
    $groups  = ['otax'=>[], 'otax_cr'=>[], 'itax'=>[], 'itax_dr'=>[]];
    $others  = [
        'otax'   => ['taxable'=>0,'igst'=>0,'cgst'=>0,'sgst'=>0,'cess'=>0,'total'=>0, 'is_outward'=>1, 'sign'=>'pos'],
        'otax_cr'=> ['taxable'=>0,'igst'=>0,'cgst'=>0,'sgst'=>0,'cess'=>0,'total'=>0, 'is_outward'=>1, 'sign'=>'neg'],
        'itax'   => ['taxable'=>0,'igst'=>0,'cgst'=>0,'sgst'=>0,'cess'=>0,'total'=>0, 'is_outward'=>2, 'sign'=>'pos'],
        'itax_dr'=> ['taxable'=>0,'igst'=>0,'cgst'=>0,'sgst'=>0,'cess'=>0,'total'=>0, 'is_outward'=>2, 'sign'=>'neg'],
    ];

    foreach ($grpRows as $r) {
        foreach ($sections as $secKey => $meta) {
            if ((int)$r['is_outward'] === $meta['is_outward'] && $r['sign_grp'] === $meta['sign']) {
                $label = $labelFor((int)$r['rate_type'], $r['rate_val'], $r['cess_val']);

                $goOther = false;
                if ((int)$r['rate_type'] === 0 || $label === 'Other') $goOther = true;
                if (in_array((int)$r['rate_type'], [1,2], true) &&
                    (float)$r['rate_val'] == 0.0 && (float)$r['cess_val'] == 0.0) {
                    $goOther = true;
                }

                if ($goOther) {
                    $others[$secKey]['taxable'] += (float)$r['taxable_value'];
                    $others[$secKey]['igst']    += (float)$r['igst'];
                    $others[$secKey]['cgst']    += (float)$r['cgst'];
                    $others[$secKey]['sgst']    += (float)$r['sgst'];
                    $others[$secKey]['cess']    += (float)$r['cess'];
                    $others[$secKey]['total']   += (float)$r['total_tax'];
                } else {
                    $r['_label'] = $label;
                    $r['_sort']  = $sortKey((int)$r['rate_type'], $r['rate_val']);
                    $groups[$secKey][] = $r;
                }
                break;
            }
        }
    }
    foreach ($groups as $k => &$arr) {
        usort($arr, function($a,$b){
            if ($a['_sort'] === $b['_sort']) return 0;
            return ($a['_sort'] < $b['_sort']) ? -1 : 1;
        });
    }
    unset($arr);

    // ---------- builders ----------
    $makeHeader = function(string $secKey, string $title) use ($secSum, $mkVals, $fromYmd, $toYmd) {
        $k = ($secKey === 'otax')   ? '1_pos'
            : ($secKey === 'otax_cr' ? '1_neg'
            : ($secKey === 'itax'    ? '2_pos'
            :                          '2_neg'));
        $s = $secSum[$k];
        $vals = $mkVals($s['taxable'],$s['igst'],$s['cgst'],$s['sgst'],$s['cess'],$s['total']);
        return array_merge([
            'ishsn'=>0,
            'table_key'=>$secKey.'_hdr',
            'from_date'=>$fromYmd,
            'to_date'=>$toYmd,
            'table_name'=>"<strong>{$title}</strong>",
            'tag'=>$secKey.'_header',
            'params'=>null,
        ], $vals);
    };

    $makeDetail = function(string $secKey, array $g, string $forcedLabel=null) use ($mkVals, $labelFor, $fromYmd, $toYmd) {
        $vals  = $mkVals((float)$g['taxable_value'], (float)$g['igst'], (float)$g['cgst'], (float)$g['sgst'], (float)$g['cess'], (float)$g['total_tax']);
        $label = $forcedLabel ?: ($g['_label'] ?? $labelFor((int)$g['rate_type'], $g['rate_val'], $g['cess_val']));
        $key   = $secKey.'_rate_'.((int)($g['rate_type'] ?? 0)).'_'.(string)($g['rate_val'] ?? '0').'_' .(string)($g['cess_val'] ?? '0');
        return array_merge([
            'ishsn'=>0,
            'table_key'=>$key,
            'from_date'=>$fromYmd,
            'to_date'=>$toYmd,
            'table_name'=>'&raquo; '.$label,
            'tag'=>$secKey.'_detail',
            'params'=>[
                'from'=>$fromYmd,'to'=>$toYmd,
                'is_outward'=>(int)($g['is_outward'] ?? (($secKey==='otax'||$secKey==='otax_cr')?1:2)),
                'sign'=>$g['sign_grp'] ?? (($secKey==='otax'||$secKey==='itax')?'pos':'neg'),
                'rate_type'=>(int)($g['rate_type'] ?? 0),
                'rate'=>(string)($g['rate_val'] ?? '0'),
                'cess'=>(string)($g['cess_val'] ?? '0'),
            ],
        ], $vals);
    };

    $makeDetailFromOther = function(string $secKey, array $sum, string $label) use ($mkVals, $fromYmd, $toYmd) {
        $vals = $mkVals($sum['taxable'],$sum['igst'],$sum['cgst'],$sum['sgst'],$sum['cess'],$sum['total']);
        return array_merge([
            'ishsn'=>0,
            'table_key'=>$secKey.'_other',
            'from_date'=>$fromYmd,
            'to_date'=>$toYmd,
            'table_name'=>'&raquo; '.$label,
            'tag'=>$secKey.'_detail_other',
            'params'=>[
                'from'=>$fromYmd,'to'=>$toYmd,
                'is_outward'=>$sum['is_outward'],
                'sign'=>$sum['sign'],
                'rate_type'=>0,'rate'=>'0','cess'=>'0',
            ],
        ], $vals);
    };

    $makeTotal = function(string $baseKey, string $label) use ($secSum, $mkVals, $fromYmd, $toYmd) {
        $a = ($baseKey === 'otax') ? $secSum['1_pos'] : $secSum['2_pos'];
        $b = ($baseKey === 'otax') ? $secSum['1_neg'] : $secSum['2_neg'];
        $vals = $mkVals(
            $a['taxable'] + $b['taxable'],
            $a['igst']    + $b['igst'],
            $a['cgst']    + $b['cgst'],
            $a['sgst']    + $b['sgst'],
            $a['cess']    + $b['cess'],
            $a['total']   + $b['total']
        );
        return array_merge([
            'ishsn'=>0,
            'table_key'=>'total_'.$baseKey,              // <-- for bolding via renderer
            'from_date'=>$fromYmd,
            'to_date'=>$toYmd,
            'table_name'=>'<strong>'.$label.'</strong>', // <-- bold label
            'tag'=>$baseKey.'_total',
            'params'=>null,
        ], $vals);
    };

    $makeBlank = function() use ($fmtRupee, $fromYmd, $toYmd) {
        $zero = $fmtRupee(0);
        return [
            'ishsn'=>0,
            'table_key'=>'',
            'from_date'=>$fromYmd,
            'to_date'=>$toYmd,
            'table_name'=>'',
            'invoice_value'=>$zero,'taxable_value'=>$zero,'igst'=>$zero,'cgst'=>$zero,'sgst'=>$zero,'cess'=>$zero,'total_tax'=>$zero,
            'sm_invoice_value'=>0,'sm_taxable_value'=>0,'sm_igst'=>0,'sm_cgst'=>0,'sm_sgst'=>0,'sm_cess'=>0,'sm_total_tax'=>0,
            'tag'=>'divider','params'=>null,
        ];
    };

    // ---------- assemble output ----------
    $out = [];

    if ($isOutward === null || $isOutward === 1) {
        $out[] = $makeHeader('otax', 'OUTPUT TAX');
        if ($others['otax']['taxable'] != 0 || $others['otax']['total'] != 0) {
            $out[] = $makeDetailFromOther('otax', $others['otax'], 'Other');
        }
        foreach ($groups['otax'] as $g)   { $out[] = $makeDetail('otax', $g); }

        $out[] = $makeHeader('otax_cr', 'OUTPUT TAX (CR. NOTE)');
        if ($others['otax_cr']['taxable'] != 0 || $others['otax_cr']['total'] != 0) {
            $out[] = $makeDetailFromOther('otax_cr', $others['otax_cr'], 'Other');
        }
        foreach ($groups['otax_cr'] as $g) { $out[] = $makeDetail('otax_cr', $g); }

        // TOTAL OUTPUT TAX (bold via renderer) + blank row after
        $out[] = $makeTotal('otax', 'TOTAL OUTPUT TAX');
        $out[] = $makeBlank(); // requested blank row after TOTAL OUTPUT TAX
    }

    if ($isOutward === null || $isOutward === 2) {
        $out[] = $makeHeader('itax', 'INPUT TAX');
        if ($others['itax']['taxable'] != 0 || $others['itax']['total'] != 0) {
            $out[] = $makeDetailFromOther('itax', $others['itax'], 'Other');
        }
        foreach ($groups['itax'] as $g)   { $out[] = $makeDetail('itax', $g); }

        $out[] = $makeHeader('itax_dr', 'INPUT TAX (DR. NOTE)');
        if ($others['itax_dr']['taxable'] != 0 || $others['itax_dr']['total'] != 0) {
            $out[] = $makeDetailFromOther('itax_dr', $others['itax_dr'], 'Other');
        }
        foreach ($groups['itax_dr'] as $g) { $out[] = $makeDetail('itax_dr', $g); }

        // TOTAL INPUT TAX (bold via renderer) + blank row after
        $out[] = $makeTotal('itax', 'TOTAL INPUT TAX');
        $out[] = $makeBlank(); // requested blank row after TOTAL INPUT TAX
    }

    return [
        'totalRecords' => count($out),
        'curPage'      => '1',
        'data'         => $out,
    ];
}

public function ajax_hsnsummary_list(
    string $fromYmd,
    string $toYmd,
    string $tableKey,
    int    $limit   = 100,
    int    $curPage = 1,
    bool   $useLimit = true   // ✅ NEW PARAM
): array {

    $pg = $this->db;

    $tableKey = preg_replace('/^\d+_/', '', $tableKey);

    $isOutward = 1;
    $mode      = 'ch';
    $chapter   = 'NA';
    $hsn       = null;

    // -------- PATTERN --------
    if (preg_match('/^hsn_(out|in)_ch_(.*)$/i', $tableKey, $m)) {
        $isOutward = (strtolower($m[1]) === 'out') ? 1 : 2;
        $mode = 'ch';
        $chapter = strtoupper(trim($m[2])) ?: 'NA';
    }
    elseif (preg_match('/^hsn_(out|in)_hsn_(.+)$/i', $tableKey, $m)) {
        $isOutward = (strtolower($m[1]) === 'out') ? 1 : 2;
        $mode = 'hsn';
        $hsn = preg_replace('/\D/', '', $m[2]);
    }
    elseif (preg_match('/^hsn_(out|in)_([^_]+)_(.+)$/i', $tableKey, $m)) {
        $isOutward = (strtolower($m[1]) === 'out') ? 1 : 2;

        $part2 = strtoupper(trim($m[2]));
        $part3 = strtoupper(trim($m[3]));

        if ($part2 === 'CH') {
            $mode = 'ch';
            $chapter = ($part3 === '' || $part3 === 'NA') ? 'NA' : $part3;
        } elseif ($part2 === 'HSN') {
            $mode = 'hsn';
            $hsn = preg_replace('/\D/', '', $part3);
        } elseif ($part2 === 'NA') {
            $mode = 'ch';
            $chapter = 'NA';
        } elseif ($part3 === 'NA' || $part3 === '') {
            $mode = 'ch';
            $chapter = $part2;
        } else {
            $mode = 'hsn';
            $chapter = $part2;
            $hsn = preg_replace('/\D/', '', $part3);
        }
    }

    // -------- TYPE FILTER --------
    if ($isOutward === 1) {
        $typeFilter = "((t.vch_type_id = 18 AND s.is_outward = 1) OR (t.vch_type_id = 2 AND s.is_outward = 2))";
    } else {
        $typeFilter = "((t.vch_type_id = 11 AND s.is_outward = 2) OR (t.vch_type_id = 3 AND s.is_outward = 1))";
    }

    // -------- WHERE --------
    $where = [];
    $where[] = "t.vch_date >= '$fromYmd'";
    $where[] = "t.vch_date <= '$toYmd'";
    $where[] = "s.acc_bsd_type IN (1,3)";
    $where[] = $typeFilter;

    if (!empty($this->company_id)) {
        $where[] = "t.cmp_id = " . (int)$this->company_id;
    }

    if (!empty($this->bo_id)) {
        $where[] = "t.hobo_id = " . (int)$this->bo_id;
    }

    // -------- HSN FILTER --------
    $hsnClean = "regexp_replace(COALESCE(s.vch_hsn_sac,''), '[^0-9]', '', 'g')";

    if ($mode === 'hsn' && $hsn) {
        $where[] = "$hsnClean = '$hsn'";
    } elseif ($mode === 'ch') {

        if ($chapter === 'NA') {
            $where[] = "($hsnClean = '' OR $hsnClean IS NULL)";
        } else {
            $chapDigits = preg_replace('/\D/', '', $chapter);

            $chapExpr = "CASE 
                WHEN $hsnClean = '' OR $hsnClean IS NULL THEN 'NA'
                WHEN length($hsnClean) < 2 THEN 'NA'
                ELSE lpad(substr($hsnClean, 1, 2), 2, '0')
            END";

            $where[] = "$chapExpr = '$chapDigits'";
        }
    }

    $whereSql = implode(' AND ', $where);

    // -------- PARTY JOIN --------
    $partySub = "
        LEFT JOIN (
            SELECT cmp_id, vch_txn_id, MIN(txn_id) AS min_txn_id
            FROM cmptxnmstn
            WHERE master_id_type='acc'
            GROUP BY cmp_id, vch_txn_id
        ) pm ON pm.cmp_id = s.cmp_id AND pm.vch_txn_id = s.vch_txn_id
        LEFT JOIN cmptxnmstn m ON m.txn_id = pm.min_txn_id
        LEFT JOIN acctmaster a ON a.acc_id = m.master_id
    ";

    // -------- COUNT (ONLY WHEN PAGINATION) --------
    $total = 0;

    if ($useLimit) {
        $sqlCnt = "
            SELECT COUNT(*) AS cnt
            FROM (
                SELECT s.vch_txn_id
                FROM vchgstsumn s
                INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
                WHERE {$whereSql}
                GROUP BY s.vch_txn_id
            ) Z
        ";
        $total = (int)($pg->query($sqlCnt)->getRow('cnt') ?? 0);
    }

    // -------- MAIN QUERY --------
    $limitSql = '';

    if ($useLimit) {
        $limit   = max(1, (int)$limit);
        $curPage = max(1, (int)$curPage);
        $offset  = ($curPage - 1) * $limit;

        $limitSql = " LIMIT {$limit} OFFSET {$offset}";
    }

    $sql = "
        SELECT
            s.vch_txn_id,
            t.vch_date,
            t.vch_type_id,
            t.hobo_id,
            a.acc_name AS party,
            COALESCE(SUM(s.vch_taxable_value), 0) AS taxable,
            COALESCE(SUM(s.vch_igst), 0)          AS igst,
            COALESCE(SUM(s.vch_cgst), 0)          AS cgst,
            COALESCE(SUM(s.vch_sgst_ugst), 0)     AS sgst,
            COALESCE(SUM(s.vch_cess), 0)          AS cess,
            COALESCE(SUM(s.vch_total_tax), 0)     AS total_tax
        FROM vchgstsumn s
        INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
        {$partySub}
        WHERE {$whereSql}
        GROUP BY s.vch_txn_id, t.vch_date, t.vch_type_id, t.hobo_id, a.acc_name
        ORDER BY t.vch_date, s.vch_txn_id
        {$limitSql}
    ";

    $rows = $pg->query($sql)->getResultArray() ?? [];

    // -------- FORMAT --------
    $fmtDate = fn($ymd) => ($ymd ? date('d-m-Y', strtotime($ymd)) : '');

    $displayHsn = ($mode === 'hsn' && $hsn)
        ? $hsn
        : (($chapter === 'NA' || !$chapter) ? 'NA' : $chapter);

    $data = [];

    foreach ($rows as $r) {

        $taxable = (float)$r['taxable'];
        $igst    = (float)$r['igst'];
        $cgst    = (float)$r['cgst'];
        $sgst    = (float)$r['sgst'];
        $cess    = (float)$r['cess'];
        $total   = (float)$r['total_tax'];

        $invoice = $taxable + $total;

        $data[] = [
            'hsn_sac'        => $displayHsn,
            'date'           => $fmtDate($r['vch_date']),
            'party'          => $r['party'] ?? '',
            'invoice_value'  => formatAmount($invoice),
            'taxable_value'  => formatAmount($taxable),
            'igst'           => formatAmount($igst),
            'cgst'           => formatAmount($cgst),
            'sgst'           => formatAmount($sgst),
            'cess'           => formatAmount($cess),
            'total_tax'      => formatAmount($total),
            'voucher_txn_id' => $r['vch_txn_id'],
            'voucher_type_id'=> $r['vch_type_id'],
            'bo_id'          => $r['hobo_id'],
        ];
    }

    return [
        'totalRecords' => $useLimit ? $total : count($data), // ✅ smart handling
        'curPage'      => (string)$curPage,
        'data'         => $data,
    ];
}


public function load_gstsummary_hsn_condensed(string $fromYmd, string $toYmd): array
{
    // Use PostgreSQL connection
    $pg = $this->db;
    
    $cmpId  = (int)$this->company_id;
    $hoboId = (int)$this->bo_id;
    
    // Build WHERE clause
    $whereConditions = [];
    $whereConditions[] = "t.vch_date >= '$fromYmd'";
    $whereConditions[] = "t.vch_date <= '$toYmd'";
    $whereConditions[] = "s.acc_bsd_type IN (1, 3)";
    
    if (! empty($cmpId)) {
        $whereConditions[] = "t.cmp_id = $cmpId";
    }
    
    if (!empty($hoboId)) {
        $whereConditions[] = "t.hobo_id = $hoboId";
    }
    
    $where = implode(' AND ', $whereConditions);

    /*
     * Filter Logic (Merged):
     * OUTWARD (Sales + Credit Note):  (vch_type_id = 18 AND is_outward = 1) OR (vch_type_id = 2 AND is_outward = 2)
     * INWARD (Purchase + Debit Note): (vch_type_id = 11 AND is_outward = 2) OR (vch_type_id = 3 AND is_outward = 1)
     * 
     * HSN comes from vch_hsn_sac column in vchgstsumn table
     * Group by first 2 digits of HSN (Chapter code)
     */

    // OUTWARD query (Sales + Credit Note merged)
    $sqlOutward = "
        SELECT
            CASE
                WHEN TRIM(COALESCE(s. vch_hsn_sac, '')) = '' THEN 'NA'
                WHEN REGEXP_REPLACE(COALESCE(s. vch_hsn_sac, ''), '[^0-9]', '', 'g') = '' THEN 'NA'
                ELSE LPAD(SUBSTRING(REGEXP_REPLACE(COALESCE(s.vch_hsn_sac, ''), '[^0-9]', '', 'g'), 1, 2), 2, '0')
            END AS chapter_code,
            COALESCE(SUM(s.vch_taxable_value), 0) AS taxable_value,
            COALESCE(SUM(s. vch_igst), 0) AS igst,
            COALESCE(SUM(s. vch_cgst), 0) AS cgst,
            COALESCE(SUM(s. vch_sgst_ugst), 0) AS sgst,
            COALESCE(SUM(s.vch_cess), 0) AS cess,
            COALESCE(SUM(s.vch_total_tax), 0) AS total_tax
        FROM vchgstsumn s
        INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
        WHERE {$where}
          AND (
              (t.vch_type_id = 18 AND s.is_outward = 1) OR
              (t.vch_type_id = 2 AND s.is_outward = 2)
          )
        GROUP BY chapter_code
        ORDER BY 
            CASE WHEN CASE
                WHEN TRIM(COALESCE(s.vch_hsn_sac, '')) = '' THEN 'NA'
                WHEN REGEXP_REPLACE(COALESCE(s.vch_hsn_sac, ''), '[^0-9]', '', 'g') = '' THEN 'NA'
                ELSE LPAD(SUBSTRING(REGEXP_REPLACE(COALESCE(s.vch_hsn_sac, ''), '[^0-9]', '', 'g'), 1, 2), 2, '0')
            END = 'NA' THEN 1 ELSE 0 END,
            chapter_code
    ";

    // INWARD query (Purchase + Debit Note merged)
    $sqlInward = "
        SELECT
            CASE
                WHEN TRIM(COALESCE(s.vch_hsn_sac, '')) = '' THEN 'NA'
                WHEN REGEXP_REPLACE(COALESCE(s.vch_hsn_sac, ''), '[^0-9]', '', 'g') = '' THEN 'NA'
                ELSE LPAD(SUBSTRING(REGEXP_REPLACE(COALESCE(s.vch_hsn_sac, ''), '[^0-9]', '', 'g'), 1, 2), 2, '0')
            END AS chapter_code,
            COALESCE(SUM(s.vch_taxable_value), 0) AS taxable_value,
            COALESCE(SUM(s.vch_igst), 0) AS igst,
            COALESCE(SUM(s.vch_cgst), 0) AS cgst,
            COALESCE(SUM(s.vch_sgst_ugst), 0) AS sgst,
            COALESCE(SUM(s.vch_cess), 0) AS cess,
            COALESCE(SUM(s.vch_total_tax), 0) AS total_tax
        FROM vchgstsumn s
        INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
        WHERE {$where}
          AND (
              (t.vch_type_id = 11 AND s.is_outward = 2) OR
              (t.vch_type_id = 3 AND s.is_outward = 1)
          )
        GROUP BY chapter_code
        ORDER BY 
            CASE WHEN CASE
                WHEN TRIM(COALESCE(s.vch_hsn_sac, '')) = '' THEN 'NA'
                WHEN REGEXP_REPLACE(COALESCE(s. vch_hsn_sac, ''), '[^0-9]', '', 'g') = '' THEN 'NA'
                ELSE LPAD(SUBSTRING(REGEXP_REPLACE(COALESCE(s. vch_hsn_sac, ''), '[^0-9]', '', 'g'), 1, 2), 2, '0')
            END = 'NA' THEN 1 ELSE 0 END,
            chapter_code
    ";

    // Execute queries
    $queryOutward = $pg->query($sqlOutward);
    $outward = $queryOutward ?  $queryOutward->getResultArray() : [];

    $queryInward = $pg->query($sqlInward);
    $inward = $queryInward ? $queryInward->getResultArray() : [];

    // --- bucket helper using formatAmount ---
    $mkVals = function (float $taxable, float $igst, float $cgst, float $sgst, float $cess, float $total) {
        $inv = $taxable + $total;
        return [
            'invoice_value'    => formatAmount($inv),
            'taxable_value'    => formatAmount($taxable),
            'igst'             => formatAmount($igst),
            'cgst'             => formatAmount($cgst),
            'sgst'             => formatAmount($sgst),
            'cess'             => formatAmount($cess),
            'total_tax'        => formatAmount($total),
            'sm_invoice_value' => (float)$inv,
            'sm_taxable_value' => (float)$taxable,
            'sm_igst'          => (float)$igst,
            'sm_cgst'          => (float)$cgst,
            'sm_sgst'          => (float)$sgst,
            'sm_cess'          => (float)$cess,
            'sm_total_tax'     => (float)$total,
        ];
    };

    // Sum bucket function
    $sumBucket = function (array $arr): array {
        $t = ['taxable' => 0, 'igst' => 0, 'cgst' => 0, 'sgst' => 0, 'cess' => 0, 'total' => 0];
        foreach ($arr as $r) {
            $t['taxable'] += (float)($r['taxable_value'] ?? 0);
            $t['igst']    += (float)($r['igst'] ?? 0);
            $t['cgst']    += (float)($r['cgst'] ?? 0);
            $t['sgst']    += (float)($r['sgst'] ?? 0);
            $t['cess']    += (float)($r['cess'] ??  0);
            $t['total']   += (float)($r['total_tax'] ?? 0);
        }
        return $t;
    };

    // Calculate totals
    $totOutward = $sumBucket($outward);
    $totInward = $sumBucket($inward);

    // --- build grid payload ---
    $out = [];

    $pushRow = function (string $tableKey, string $label, array $vals, string $tag, array $params = []) use (&$out, $fromYmd, $toYmd) {
        $out[] = array_merge([
            'ishsn'      => 1,
            'table_key'  => $tableKey,
            'from_date'  => $fromYmd,
            'to_date'    => $toYmd,
            'table_name' => $label,
            'tag'        => $tag,
            'params'     => empty($params) ? null : $params,
        ], $vals);
    };

    $blankRow = function () use (&$out, $fromYmd, $toYmd) {
        $z = formatAmount(0);
        $out[] = [
            'ishsn' => 1,
            'table_key' => '',
            'from_date' => $fromYmd,
            'to_date' => $toYmd,
            'table_name' => '',
            'invoice_value' => $z,
            'taxable_value' => $z,
            'igst' => $z,
            'cgst' => $z,
            'sgst' => $z,
            'cess' => $z,
            'total_tax' => $z,
            'sm_invoice_value' => 0,
            'sm_taxable_value' => 0,
            'sm_igst' => 0,
            'sm_cgst' => 0,
            'sm_sgst' => 0,
            'sm_cess' => 0,
            'sm_total_tax' => 0,
            'tag' => 'divider',
            'params' => null,
        ];
    };

    // ===== OUTWARD SUPPLY (Sales + Credit Note merged by HSN Chapter) =====
    foreach ($outward as $r) {
        $chapter = $r['chapter_code'] ??  'NA';
        $label   = ($chapter === 'NA') ? 'NA' : ('CHAPTER ' . $chapter);
        $vals    = $mkVals(
            (float)($r['taxable_value'] ??  0),
            (float)($r['igst'] ??  0),
            (float)($r['cgst'] ??  0),
            (float)($r['sgst'] ??  0),
            (float)($r['cess'] ??  0),
            (float)($r['total_tax'] ?? 0)
        );
        $pushRow(
            'hsn_out_ch_' .  $chapter,
            $label,
            $vals,
            'hsn_outward_chapter',
            ['from' => $fromYmd, 'to' => $toYmd, 'is_outward' => 1, 'chapter' => $chapter]
        );
    }

    // OUTWARD total
    $pushRow(
        'total_outward_supply_hsn',
        '<strong>TOTAL OUTWARD SUPPLY (HSN-WISE)</strong>',
        $mkVals(
            $totOutward['taxable'],
            $totOutward['igst'],
            $totOutward['cgst'],
            $totOutward['sgst'],
            $totOutward['cess'],
            $totOutward['total']
        ),
        'hsn_outward_total'
    );

    // Divider
    $blankRow();

    // ===== INWARD SUPPLY (Purchase + Debit Note merged by HSN Chapter) =====
    foreach ($inward as $r) {
        $chapter = $r['chapter_code'] ?? 'NA';
        $label   = ($chapter === 'NA') ? 'NA' : ('CHAPTER ' . $chapter);
        $vals    = $mkVals(
            (float)($r['taxable_value'] ?? 0),
            (float)($r['igst'] ?? 0),
            (float)($r['cgst'] ?? 0),
            (float)($r['sgst'] ?? 0),
            (float)($r['cess'] ?? 0),
            (float)($r['total_tax'] ?? 0)
        );
        $pushRow(
            'hsn_in_ch_' . $chapter,
            $label,
            $vals,
            'hsn_inward_chapter',
            ['from' => $fromYmd, 'to' => $toYmd, 'is_outward' => 2, 'chapter' => $chapter]
        );
    }

    // INWARD total
    $pushRow(
        'total_inward_supply_hsn',
        '<strong>TOTAL INWARD SUPPLY (HSN-WISE)</strong>',
        $mkVals(
            $totInward['taxable'],
            $totInward['igst'],
            $totInward['cgst'],
            $totInward['sgst'],
            $totInward['cess'],
            $totInward['total']
        ),
        'hsn_inward_total'
    );

    return [
        'totalRecords' => count($out),
        'curPage'      => '1',
        'data'         => $out,
    ];
}
public function load_gstr_listings(
    string $fromYmd,
    string $toYmd,
    string $tableno,
    array  $statesDropdown,
    int    $limit = 10,
    int    $page  = 1
): array {
    $pg     = $this->db;
    $cmpId  = (int)$this->company_id;
    $hoboId = (int)$this->bo_id;

    $tab = strtolower(trim($tableno));
    $vchTypeId = 18;
    $isOutward = 1;
    $rateType = null;
    $rateVal  = null;
    $cessVal  = null;
    $hasRateFilter = false;

    // ── Parse tab name ──
    if (preg_match('/^(otax|otax_cr|itax|itax_dr)_rate_([0-9]+)_([0-9.]+)_([0-9.]+)$/i', $tab, $m)) {
        $cat      = $m[1];
        $rateType = (int)$m[2];
        $rateVal  = (float)$m[3];
        $cessVal  = (float)$m[4];
        $hasRateFilter = true;
    } else {
        // Extract category prefix (order matters: check longer prefixes first)
        if     (strpos($tab, 'otax_cr') === 0) { $cat = 'otax_cr'; }
        elseif (strpos($tab, 'itax_dr') === 0) { $cat = 'itax_dr'; }
        elseif (strpos($tab, 'otax')    === 0) { $cat = 'otax'; }
        elseif (strpos($tab, 'itax')    === 0) { $cat = 'itax'; }
        else                                    { $cat = 'otax'; }
    }

    // Map category to voucher type and direction
    $catMap = [
        'otax'    => ['vch' => 18, 'out' => 1],
        'otax_cr' => ['vch' => 2,  'out' => 2],
        'itax'    => ['vch' => 11, 'out' => 2],
        'itax_dr' => ['vch' => 3,  'out' => 1],
    ];
    $vchTypeId = $catMap[$cat]['vch'];
    $isOutward = $catMap[$cat]['out'];

    // ── Build WHERE conditions ──
    $where = [];
    $binds = [];

    $where[] = "t.vch_date BETWEEN ? AND ?";
    $binds[] = $fromYmd;
    $binds[] = $toYmd;

    $where[] = "s.acc_bsd_type IN (1,3)";
    $where[] = "t.vch_type_id = ?";
    $binds[] = $vchTypeId;
    $where[] = "s.is_outward = ?";
    $binds[] = $isOutward;

    if ($cmpId > 0) {
        $where[] = "t.cmp_id = ?";
        $binds[] = $cmpId;
    }
    if ($hoboId > 0) {
        $where[] = "t.hobo_id = ?";
        $binds[] = $hoboId;
    }

    // ── Rate filter (pre-compute normalized group once) ──
    if ($hasRateFilter) {
        $grpNorm = "regexp_replace(replace(replace(COALESCE(s.gst_rate_grp,''), '_', ','), ' ', ''), '[^0-9,.]', '', 'g')";
        $where[] = "COALESCE(NULLIF(split_part($grpNorm, ',', 1), ''), '1')::int = ?";
        $binds[] = $rateType;
        $where[] = "ROUND(COALESCE(NULLIF(split_part($grpNorm, ',', 2), ''), '0')::numeric, 2) = ?";
        $binds[] = number_format($rateVal, 2, '.', '');
        $where[] = "ROUND(COALESCE(NULLIF(split_part($grpNorm, ',', 3), ''), '0')::numeric, 2) = ?";
        $binds[] = number_format($cessVal, 2, '.', '');
    }

    $whereSql = implode("\n        AND ", $where);

    // ── POS column ──
    $posSelect = ($isOutward === 1)
        ? "COALESCE(o.outsup_pos::text, '')"
        : "COALESCE(i.inwsup_pos::text, '')";
    $posJoin = ($isOutward === 1)
        ? "LEFT JOIN gstroutsup o ON o.vch_txn_id = s.vch_txn_id"
        : "LEFT JOIN gstrinwsup i ON i.vch_txn_id = s.vch_txn_id";

    // ── OPTIMIZATION: Single query with COUNT(*) OVER() ──
    // Eliminates separate COUNT query = 1 round-trip instead of 2
    $offset = max(0, ($page - 1) * max(1, $limit));

    // ── OPTIMIZATION: Replace LATERAL JOIN with DISTINCT ON subquery ──
    // DISTINCT ON is much faster than LATERAL + LIMIT 1 in PG 10
    $sql = "
        WITH voucher_agg AS (
            SELECT
                s.vch_txn_id,
                t.vch_date,
                t.vch_type_id,
                t.hobo_id,
                {$posSelect}                         AS pos_code,
                COALESCE(SUM(s.vch_taxable_value), 0) AS taxable_value,
                COALESCE(SUM(s.vch_igst), 0)          AS igst,
                COALESCE(SUM(s.vch_cgst), 0)          AS cgst,
                COALESCE(SUM(s.vch_sgst_ugst), 0)     AS sgst,
                COALESCE(SUM(s.vch_cess), 0)          AS cess,
                COALESCE(SUM(s.vch_total_tax), 0)     AS total_tax
            FROM vchgstsumn s
            INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
            {$posJoin}
            WHERE {$whereSql}
            GROUP BY s.vch_txn_id, t.vch_date, t.vch_type_id, t.hobo_id, pos_code
        ),
        party_first AS (
            SELECT DISTINCT ON (cm.vch_txn_id)
                cm.vch_txn_id,
                am.acc_name AS party_name,
                cm.master_id AS party_id,
				COALESCE(ad.acc_gstin, '') AS gstin
            FROM cmptxnmstn cm
            INNER JOIN acctmaster am ON am.acc_id = cm.master_id
			LEFT JOIN acctmstdet ad ON ad.acc_id = cm.master_id
            WHERE cm.master_id_type = 'acc'
              AND cm.vch_txn_id IN (SELECT vch_txn_id FROM voucher_agg)
            ORDER BY cm.vch_txn_id, cm.txn_id
        )
        SELECT
            v.*,
            COALESCE(pf.party_name, '')  AS party_name,
            COALESCE(pf.party_id, 0)     AS party_id,
			COALESCE(pf.gstin, '')       AS gstin,
            COUNT(*) OVER()              AS total_count
        FROM voucher_agg v
        LEFT JOIN party_first pf ON pf.vch_txn_id = v.vch_txn_id
        ORDER BY v.vch_date, v.vch_txn_id
        LIMIT {$limit} OFFSET {$offset}
    ";

    $rows = $pg->query($sql, $binds)->getResultArray() ?? [];

    // ── Extract total from first row (avoids separate COUNT query) ──
    $total = !empty($rows) ? (int)$rows[0]['total_count'] : 0;

    // ── Build response ──
    $data = [];
    foreach ($rows as $r) {
        $taxable  = (float)$r['taxable_value'];
        $igst     = (float)$r['igst'];
        $cgst     = (float)$r['cgst'];
        $sgst     = (float)$r['sgst'];
        $cess     = (float)$r['cess'];
        $totalTax = (float)$r['total_tax'];
        $invoice  = $taxable + $totalTax;

        // POS formatting
        $posCode = trim((string)$r['pos_code']);
        $pos = '';
        if ($posCode !== '') {
            $cc  = str_pad($posCode, 2, '0', STR_PAD_LEFT);
            $pos = $cc . (isset($statesDropdown[$cc]) ? ' - ' . $statesDropdown[$cc] : '');
        }

        // Party name
        $partyName = trim((string)$r['party_name']);
        if ($partyName === '' && !empty($r['party_id'])) {
            $partyName = 'A/c #' . $r['party_id'];
        }

        $data[] = [
            'date'              => $r['vch_date'] ? date('d-m-Y', strtotime($r['vch_date'])) : '',
            'party'             => $partyName,
			'gstin'             => $r['gstin'],
            'pos'               => $pos,
            'voucher_txn_id'    => $r['vch_txn_id'],
            'voucher_type_id'   => $r['vch_type_id'],
            'bo_id'             => $r['hobo_id'],
            'invoice_value'     => formatAmount($invoice),
            'taxable_value'     => formatAmount($taxable),
            'igst'              => formatAmount($igst),
            'cgst'              => formatAmount($cgst),
            'sgst'              => formatAmount($sgst),
            'cess'              => formatAmount($cess),
            'total_tax'         => formatAmount($totalTax),
            'sm_invoice_value'  => $invoice,
            'sm_taxable_value'  => $taxable,
            'sm_igst'           => $igst,
            'sm_cgst'           => $cgst,
            'sm_sgst'           => $sgst,
            'sm_cess'           => $cess,
            'sm_total_tax'      => $totalTax,
        ];
    }

    return [
        'totalRecords' => $total,
        'curPage'      => (string)$page,
        'data'         => $data,
    ];
}

public function load_gstr_listings_olodee(
    string $fromYmd,
    string $toYmd,
    string $tableno,
    array  $statesDropdown,
    int    $limit  = 10,
    int    $page   = 1
): array {
    $pg    = $this->db;
    $cmpId = (int)$this->company_id;
    $hoboId= (int)$this->bo_id;

    $tab = strtolower(trim($tableno));
    $vchTypeId = 18; $isOutward = 1;
    $rateType = null; $rateVal = null; $cessVal = null; $hasRateFilter = false;

    if (preg_match('/^(otax|otax_cr|itax|itax_dr)_rate_([0-9]+)_([0-9.]+)_([0-9.]+)$/i', $tab, $m)) {
        $cat      = $m[1];
        $rateType = (int)$m[2];
        $rateVal  = (float)$m[3];
        $cessVal  = (float)$m[4];
        $hasRateFilter = true;

        if     ($cat === 'otax')    { $vchTypeId = 18; $isOutward = 1; }
        elseif ($cat === 'otax_cr'){ $vchTypeId = 2;  $isOutward = 2; }
        elseif ($cat === 'itax')   { $vchTypeId = 11; $isOutward = 2; }
        elseif ($cat === 'itax_dr'){ $vchTypeId = 3;  $isOutward = 1; }
    } else {
        if     (strpos($tab, 'otax_cr') === 0) { $vchTypeId = 2;  $isOutward = 2; }
        elseif (strpos($tab, 'otax')    === 0) { $vchTypeId = 18; $isOutward = 1; }
        elseif (strpos($tab, 'itax_dr') === 0) { $vchTypeId = 3;  $isOutward = 1; }
        elseif (strpos($tab, 'itax')    === 0) { $vchTypeId = 11; $isOutward = 2; }
    }

    $where = [];
    $binds = [];
    $where[] = "t.vch_date >= ?";        $binds[] = $fromYmd;
    $where[] = "t.vch_date <= ?";        $binds[] = $toYmd;
    $where[] = "s.acc_bsd_type IN (1,3)";
    $where[] = "t.vch_type_id = ?";      $binds[] = $vchTypeId;
    $where[] = "s.is_outward = ?";       $binds[] = $isOutward;
    if (!empty($cmpId))  { $where[] = "t.cmp_id = ?";  $binds[] = $cmpId; }
    if (!empty($hoboId)) { $where[] = "t.hobo_id = ?"; $binds[] = $hoboId; }

    // Rate filter
    $grpNorm = "regexp_replace(replace(replace(COALESCE(s.gst_rate_grp,''), '_', ','), ' ', ''), '[^0-9,\\.]', '', 'g')";
    if ($hasRateFilter) {
        $where[] = "COALESCE(NULLIF(split_part($grpNorm, ',', 1), ''), '1')::int = ?";
        $binds[] = (int)$rateType;
        $where[] = "ROUND(COALESCE(NULLIF(split_part($grpNorm, ',', 2), ''), '0')::numeric, 2) = ?";
        $binds[] = number_format($rateVal, 2, '.', '');
        $where[] = "ROUND(COALESCE(NULLIF(split_part($grpNorm, ',', 3), ''), '0')::numeric, 2) = ?";
        $binds[] = number_format($cessVal, 2, '.', '');
    }

    $whereSql = implode(' AND ', $where);

    // POS join
    $posSelect = ($isOutward === 1) ? "COALESCE(o.outsup_pos::text, '')" : "COALESCE(i.inwsup_pos::text, '')";
    $posJoin   = ($isOutward === 1)
        ? "LEFT JOIN gstroutsup o ON o.vch_txn_id = s.vch_txn_id"
        : "LEFT JOIN gstrinwsup i ON i.vch_txn_id = s.vch_txn_id";

    // Party join
    $partyJoin = "
        LEFT JOIN LATERAL (
            SELECT master_id
            FROM cmptxnmstn
            WHERE vch_txn_id = s.vch_txn_id AND master_id_type = 'acc'
            ORDER BY txn_id
            LIMIT 1
        ) p ON true
        LEFT JOIN acctmaster am ON am.acc_id = p.master_id
    ";

    /* ---------- COUNT: force integer ---------- */
    $sqlCount = "
        SELECT CAST(COUNT(*) AS BIGINT) AS c FROM (
            SELECT s.vch_txn_id
            FROM vchgstsumn s
            INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
            {$posJoin}
            {$partyJoin}
            WHERE {$whereSql}
            GROUP BY s.vch_txn_id
        ) sub
    ";
    $countRow = $pg->query($sqlCount, $binds)->getRowArray();
    $total = (int) floor((float) ($countRow['c'] ?? 0));

    // DATA (paged)
    $offset = max(0, ($page - 1) * max(1, $limit));
    $sqlData = "
        SELECT
            s.vch_txn_id,
            t.vch_date,
            {$posSelect} AS pos_code,
            p.master_id AS party_id,
            t.vch_type_id,
            t.hobo_id,
            COALESCE(am.acc_name, '') AS party_name,
            COALESCE(SUM(s.vch_taxable_value),0) AS taxable_value,
            COALESCE(SUM(s.vch_igst),0)          AS igst,
            COALESCE(SUM(s.vch_cgst),0)          AS cgst,
            COALESCE(SUM(s.vch_sgst_ugst),0)     AS sgst,
            COALESCE(SUM(s.vch_cess),0)          AS cess,
            COALESCE(SUM(s.vch_total_tax),0)     AS total_tax
        FROM vchgstsumn s
        INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
        {$posJoin}
        {$partyJoin}
        WHERE {$whereSql}
        GROUP BY s.vch_txn_id, t.vch_date, pos_code, p.master_id, t.vch_type_id, t.hobo_id, am.acc_name
        ORDER BY t.vch_date, s.vch_txn_id
        LIMIT {$limit} OFFSET {$offset}
    ";
    $rows = $pg->query($sqlData, $binds)->getResultArray() ?? [];

    $fmtDate = fn($ymd) => ($ymd ? date('d-m-Y', strtotime($ymd)) : '');
    $data = [];
    foreach ($rows as $r) {
        $taxable = (float)($r['taxable_value'] ?? 0);
        $igst    = (float)($r['igst'] ?? 0);
        $cgst    = (float)($r['cgst'] ?? 0);
        $sgst    = (float)($r['sgst'] ?? 0);
        $cess    = (float)($r['cess'] ?? 0);
        $totalTax= (float)($r['total_tax'] ?? 0);
        $invoice = $taxable + $totalTax;

        $posCode = trim((string)($r['pos_code'] ?? ''));
        if ($posCode !== '') {
            $cc  = str_pad($posCode, 2, '0', STR_PAD_LEFT);
            $pos = $cc . (isset($statesDropdown[$cc]) ? ' - ' . $statesDropdown[$cc] : '');
        } else {
            $pos = '';
        }

        $partyName = trim((string)($r['party_name'] ?? ''));
        if ($partyName === '' && !empty($r['party_id'])) {
            $partyName = 'A/c #' . $r['party_id'];
        }

        $data[] = [
            'date'              => $fmtDate($r['vch_date'] ?? ''),
            'party'             => $partyName,
            'pos'               => $pos,
            'voucher_txn_id'    => $r['vch_txn_id'] ?? '',
            'voucher_type_id'   => $r['vch_type_id'] ?? '',
            'bo_id'             => $r['hobo_id'] ?? '',
            'invoice_value'     => formatAmount($invoice),
            'taxable_value'     => formatAmount($taxable),
            'igst'              => formatAmount($igst),
            'cgst'              => formatAmount($cgst),
            'sgst'              => formatAmount($sgst),
            'cess'              => formatAmount($cess),
            'total_tax'         => formatAmount($totalTax),
            // raw for footer totals
            'sm_invoice_value'  => $invoice,
            'sm_taxable_value'  => $taxable,
            'sm_igst'           => $igst,
            'sm_cgst'           => $cgst,
            'sm_sgst'           => $sgst,
            'sm_cess'           => $cess,
            'sm_total_tax'      => $totalTax,
        ];
    }

    return [
        'totalRecords' => $total,
        'curPage'      => (string)$page,
        'data'         => $data,
    ];
}

public function load_gstr1_listings(
    string $fromYmd,
    string $toYmd,
    string $tableno,
    array  $statesDropdown,
    int    $limit = 10,
    int    $page  = 1
): array {
    $cmpId  = (int)$this->company_id;
    $hoboId = (int)$this->bo_id;

    // Mapping: tableno -> inv_supply_id + flags
    $map = [
        '4A'           => ['inv_supply_id' => [1],            'is_outward' => 1, 'rev_chg_val' => 0], // exclude RC
        '4B'           => ['inv_supply_id' => [1],            'is_outward' => 1, 'rev_chg_val' => 1], // only RC
        '5'            => ['inv_supply_id' => [2],            'is_outward' => 1],
        '6A'           => ['inv_supply_id' => [16,17],        'is_outward' => 1],
        '6A_EXPWP'     => ['inv_supply_id' => [16],           'is_outward' => 1],
        '6A_EXPWOP'    => ['inv_supply_id' => [17],           'is_outward' => 1],
        '6B'           => ['inv_supply_id' => [18,19],        'is_outward' => 1],
        '6B_SEZWP'     => ['inv_supply_id' => [18],           'is_outward' => 1],
        '6B_SEZWOP'    => ['inv_supply_id' => [19],           'is_outward' => 1],
        '6C'           => ['inv_supply_id' => [20],           'is_outward' => 1],
        '7'            => ['inv_supply_id' => [3],            'is_outward' => 1],
        '8'            => ['inv_supply_id' => [4,5,6,7,8,9,10,11,12,13,14,15], 'is_outward' => 1],
        '8_Nil'        => ['inv_supply_id' => [4,5,6,7],      'is_outward' => 1],
        '8_Exempted'   => ['inv_supply_id' => [8,9,10,11],    'is_outward' => 1],
        '8_Non-GST'    => ['inv_supply_id' => [12,13,14,15],  'is_outward' => 1],
        '9B'           => ['credit_note' => true, 'is_outward' => 2],
        '9B_Registered'   => ['credit_note' => true, 'is_outward' => 2],
        '9B_Unregistered' => ['credit_note' => true, 'is_outward' => 2],
        '9A,9C'        => ['no_data' => true],
        '11A(1),11A(2)' => ['no_data' => true],
        '11B(1),11B(2)' => ['no_data' => true],
        '12'           => ['no_data' => true],
        '13'           => ['no_data' => true],
        '13_candoc'    => ['no_data' => true],
        '13_netissued' => ['no_data' => true],
        '14'           => ['no_data' => true],
        '15'           => ['no_data' => true],
        '16'           => ['no_data' => true],
    ];
    $conf = $map[$tableno] ?? ['no_data' => true];
    if (!empty($conf['no_data'])) {
        return ['totalRecords' => 0, 'curPage' => (int)$page, 'data' => []];
    }

    $isOutward = (int)($conf['is_outward'] ?? 1);
    $invList   = $conf['inv_supply_id'] ?? [];
    $isCredit  = !empty($conf['credit_note']);
    $revChgVal = $conf['rev_chg_val'] ?? null; // 0 for regular, 1 for RC

    // POS join based on outward/inward
    $posJoin   = ($isOutward === 1)
        ? "LEFT JOIN gstroutsup o ON o.vch_txn_id = s.vch_txn_id"
        : "LEFT JOIN gstrinwsup i ON i.vch_txn_id = s.vch_txn_id";
    $posSelect = ($isOutward === 1) ? "COALESCE(o.outsup_pos::text,'')" : "COALESCE(i.inwsup_pos::text,'')";

    // Party join
    $partyJoin = "
        LEFT JOIN LATERAL (
            SELECT master_id
            FROM cmptxnmstn
            WHERE vch_txn_id = s.vch_txn_id AND master_id_type = 'acc'
            ORDER BY txn_id
            LIMIT 1
        ) p ON true
        LEFT JOIN acctmaster am ON am.acc_id = p.master_id
    ";

    // WHERE (parameterized)
    $where = [];
    $binds = [];

    $where[] = "t.vch_date >= ?"; $binds[] = $fromYmd;
    $where[] = "t.vch_date <= ?"; $binds[] = $toYmd;
    $where[] = "s.is_outward = ?"; $binds[] = $isOutward;
    $where[] = "s.acc_bsd_type IN (1,3)";

    if (!empty($cmpId))  { $where[] = "t.cmp_id = ?";  $binds[] = $cmpId; }
    if (!empty($hoboId)) { $where[] = "t.hobo_id = ?"; $binds[] = $hoboId; }

    if (!empty($invList)) {
        $where[] = "s.inv_supply_id = ANY(?)";
        $binds[] = '{' . implode(',', array_map('intval', $invList)) . '}'; // PG array literal
    }

    if ($isCredit) {
        $where[] = "t.vch_type_id = 2"; // credit note
    }

    if ($revChgVal !== null) {
        // RC filter applies on outsup_rev_chg (join exists when outward)
        $where[] = ($isOutward === 1)
            ? "COALESCE(o.outsup_rev_chg,0) = ?"
            : "COALESCE(o.outsup_rev_chg,0) = ?";
        $binds[] = (int)$revChgVal;
    }

    $whereSql = implode(' AND ', $where);

    /* ---------- COUNT distinct vouchers (force BIGINT -> int) ---------- */
    $sqlCount = "
        SELECT CAST(COUNT(*) AS BIGINT) AS c FROM (
            SELECT s.vch_txn_id
            FROM vchgstsumn s
            INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
            {$posJoin}
            {$partyJoin}
            WHERE {$whereSql}
            GROUP BY s.vch_txn_id
        ) sub
    ";
    $countRow = $this->db->query($sqlCount, $binds)->getRowArray();
    $total = isset($countRow['c']) ? (int)$countRow['c'] : 0;

    // Paging
    $offset = max(0, ($page - 1) * max(1, $limit));

    // DATA
    $sqlData = "
        SELECT
            s.vch_txn_id,
			s.inv_supply_id,
            t.vch_date,
            {$posSelect} AS pos_code,
            p.master_id AS party_id,
            t.vch_type_id,
            t.hobo_id,
            COALESCE(am.acc_name, '') AS party_name,
            COALESCE(SUM(s.vch_taxable_value),0) AS taxable_value,
            COALESCE(SUM(s.vch_igst),0)          AS igst,
            COALESCE(SUM(s.vch_cgst),0)          AS cgst,
            COALESCE(SUM(s.vch_sgst_ugst),0)     AS sgst,
            COALESCE(SUM(s.vch_cess),0)          AS cess,
            COALESCE(SUM(s.vch_total_tax),0)     AS total_tax
        FROM vchgstsumn s
        INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
        {$posJoin}
        {$partyJoin}
        WHERE {$whereSql}
        GROUP BY s.inv_supply_id,s.vch_txn_id, t.vch_date, pos_code, p.master_id, t.vch_type_id, t.hobo_id, am.acc_name
        ORDER BY t.vch_date, s.vch_txn_id
        LIMIT {$limit} OFFSET {$offset}
    ";
    $rows = $this->db->query($sqlData, $binds)->getResultArray() ?? [];


    $fmtDate = fn($ymd) => ($ymd ? date('d-m-Y', strtotime($ymd)) : '');
    $data = [];
    foreach ($rows as $r) {
        $taxable = (float)($r['taxable_value'] ?? 0);
        $igst    = (float)($r['igst'] ?? 0);
        $cgst    = (float)($r['cgst'] ?? 0);
        $sgst    = (float)($r['sgst'] ?? 0);
        $cess    = (float)($r['cess'] ?? 0);
        $totalTax= (float)($r['total_tax'] ?? 0);
		
		// ✅ SINGLE CHECK: Export Without Payment (WOPAY)
		if ((int)($r['inv_supply_id'] ?? 0) === 17) {
			$igst = 0;
			$cgst = 0;
			$sgst = 0;
			$cess = 0;
			$totalTax = 0;

			// Invoice = only taxable value
			$invoice = $taxable;
		} else {
			$invoice = $taxable + $totalTax;
		}
       

        $posCode = trim((string)($r['pos_code'] ?? ''));
        if ($posCode !== '') {
            $cc  = str_pad($posCode, 2, '0', STR_PAD_LEFT);
            $pos = $cc . (isset($statesDropdown[$cc]) ? ' - ' . $statesDropdown[$cc] : '');
        } else {
            $pos = '';
        }

        $partyName = trim((string)($r['party_name'] ?? ''));
        if ($partyName === '' && !empty($r['party_id'])) {
            $partyName = 'A/c #' . $r['party_id'];
        }

        $data[] = [
            'date'              => $fmtDate($r['vch_date'] ?? ''),
            'party'             => $partyName,
            'pos'               => $pos,
            'voucher_txn_id'    => $r['vch_txn_id'] ?? '',
            'voucher_type_id'   => $r['vch_type_id'] ?? '',
            'bo_id'             => $r['hobo_id'] ?? '',
            'invoice_value'     => formatAmount($invoice),
            'taxable_value'     => formatAmount($taxable),
            'igst'              => formatAmount($igst),
            'cgst'              => formatAmount($cgst),
            'sgst'              => formatAmount($sgst),
            'cess'              => formatAmount($cess),
            'total_tax'         => formatAmount($totalTax),
            // raw for footer totals
            'sm_invoice_value'  => $invoice,
            'sm_taxable_value'  => $taxable,
            'sm_igst'           => $igst,
            'sm_cgst'           => $cgst,
            'sm_sgst'           => $sgst,
            'sm_cess'           => $cess,
            'sm_total_tax'      => $totalTax,
        ];
    }

    return [
        'totalRecords' => $total,   // integer
        'curPage'      => (int)$page,
        'data'         => $data,
    ];
}

public function load_gstsummary_hsn_detailed(string $fromYmd, string $toYmd): array
{
    $pg = $this->db;

    $cmpId  = (int)$this->company_id;
    $hoboId = (int)$this->bo_id;

    // Build WHERE clause
    $where = [];
    $where[] = "t.vch_date >= '$fromYmd'";
    $where[] = "t.vch_date <= '$toYmd'";
    $where[] = "s.acc_bsd_type IN (1, 3)";
    if (!empty($cmpId))  $where[] = "t.cmp_id = $cmpId";
    if (!empty($hoboId)) $where[] = "t.hobo_id = $hoboId";
    $whereSql = implode(' AND ', $where);

    // OUTWARD query
    $sqlOutward = "
        SELECT * FROM (
            SELECT
                CASE
                    WHEN TRIM(COALESCE(s.vch_hsn_sac, '')) = '' THEN 'NA'
                    WHEN LEFT(TRIM(COALESCE(s.vch_hsn_sac, '')), 1) ~ '[^0-9]' THEN 'NA'
                    WHEN LENGTH(REGEXP_REPLACE(COALESCE(s.vch_hsn_sac, ''), '[^0-9]', '', 'g')) < 2 THEN 'NA'
                    ELSE LPAD(LEFT(REGEXP_REPLACE(COALESCE(s.vch_hsn_sac, ''), '[^0-9]', '', 'g'), 2), 2, '0')
                END AS chapter_code,
                CASE
                    WHEN TRIM(COALESCE(s.vch_hsn_sac, '')) = '' THEN 'NA'
                    WHEN LEFT(TRIM(COALESCE(s.vch_hsn_sac, '')), 1) ~ '[^0-9]' THEN 'NA'
                    ELSE REGEXP_REPLACE(COALESCE(s.vch_hsn_sac, ''), '[^0-9]', '', 'g')
                END AS hsn_code,
                COALESCE(SUM(s.vch_taxable_value), 0) AS taxable_value,
                COALESCE(SUM(s.vch_igst), 0) AS igst,
                COALESCE(SUM(s.vch_cgst), 0) AS cgst,
                COALESCE(SUM(s.vch_sgst_ugst), 0) AS sgst,
                COALESCE(SUM(s.vch_cess), 0) AS cess,
                COALESCE(SUM(s.vch_total_tax), 0) AS total_tax
            FROM vchgstsumn s
            INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
            WHERE {$whereSql}
              AND (
                  (t.vch_type_id = 18 AND s.is_outward = 1) OR
                  (t.vch_type_id = 2 AND s.is_outward = 2)
              )
            GROUP BY chapter_code, hsn_code
        ) AS subquery
    ";

    // INWARD query
    $sqlInward = "
        SELECT * FROM (
            SELECT
                CASE
                    WHEN TRIM(COALESCE(s.vch_hsn_sac, '')) = '' THEN 'NA'
                    WHEN LEFT(TRIM(COALESCE(s.vch_hsn_sac, '')), 1) ~ '[^0-9]' THEN 'NA'
                    WHEN LENGTH(REGEXP_REPLACE(COALESCE(s.vch_hsn_sac, ''), '[^0-9]', '', 'g')) < 2 THEN 'NA'
                    ELSE LPAD(LEFT(REGEXP_REPLACE(COALESCE(s.vch_hsn_sac, ''), '[^0-9]', '', 'g'), 2), 2, '0')
                END AS chapter_code,
                CASE
                    WHEN TRIM(COALESCE(s.vch_hsn_sac, '')) = '' THEN 'NA'
                    WHEN LEFT(TRIM(COALESCE(s.vch_hsn_sac, '')), 1) ~ '[^0-9]' THEN 'NA'
                    ELSE REGEXP_REPLACE(COALESCE(s.vch_hsn_sac, ''), '[^0-9]', '', 'g')
                END AS hsn_code,
                COALESCE(SUM(s.vch_taxable_value), 0) AS taxable_value,
                COALESCE(SUM(s.vch_igst), 0) AS igst,
                COALESCE(SUM(s.vch_cgst), 0) AS cgst,
                COALESCE(SUM(s.vch_sgst_ugst), 0) AS sgst,
                COALESCE(SUM(s.vch_cess), 0) AS cess,
                COALESCE(SUM(s.vch_total_tax), 0) AS total_tax
            FROM vchgstsumn s
            INNER JOIN vchtxnconso t ON t.vch_txn_id = s.vch_txn_id
            WHERE {$whereSql}
              AND (
                  (t.vch_type_id = 11 AND s.is_outward = 2) OR
                  (t.vch_type_id = 3 AND s.is_outward = 1)
              )
            GROUP BY chapter_code, hsn_code
        ) AS subquery
    ";

    $outwardRows = $pg->query($sqlOutward)->getResultArray() ?? [];
    $inwardRows  = $pg->query($sqlInward)->getResultArray() ?? [];

    // helpers
    $mkVals = function (float $taxable, float $igst, float $cgst, float $sgst, float $cess, float $total, bool $asBlank=false) {
        if ($asBlank) {
            return [
                'invoice_value'    => '',
                'taxable_value'    => '',
                'igst'             => '',
                'cgst'             => '',
                'sgst'             => '',
                'cess'             => '',
                'total_tax'        => '',
                'sm_invoice_value' => 0,
                'sm_taxable_value' => 0,
                'sm_igst'          => 0,
                'sm_cgst'          => 0,
                'sm_sgst'          => 0,
                'sm_cess'          => 0,
                'sm_total_tax'     => 0,
            ];
        }
        $inv = $taxable + $total;
        return [
            'invoice_value'    => formatAmount($inv),
            'taxable_value'    => formatAmount($taxable),
            'igst'             => formatAmount($igst),
            'cgst'             => formatAmount($cgst),
            'sgst'             => formatAmount($sgst),
            'cess'             => formatAmount($cess),
            'total_tax'        => formatAmount($total),
            'sm_invoice_value' => $inv,
            'sm_taxable_value' => $taxable,
            'sm_igst'          => $igst,
            'sm_cgst'          => $cgst,
            'sm_sgst'          => $sgst,
            'sm_cess'          => $cess,
            'sm_total_tax'     => $total,
        ];
    };

    $groupByChapter = function (array $rows): array {
        $chapters = [];
        foreach ($rows as $r) {
            $chap = $r['chapter_code'] ?? 'NA';
            if (!isset($chapters[$chap])) {
                $chapters[$chap] = [
                    'hsn_rows' => [],
                    'total' => ['taxable' => 0, 'igst' => 0, 'cgst' => 0, 'sgst' => 0, 'cess' => 0, 'total' => 0]
                ];
            }
            $chapters[$chap]['hsn_rows'][] = $r;
            $chapters[$chap]['total']['taxable'] += (float)($r['taxable_value'] ?? 0);
            $chapters[$chap]['total']['igst']    += (float)($r['igst'] ?? 0);
            $chapters[$chap]['total']['cgst']    += (float)($r['cgst'] ?? 0);
            $chapters[$chap]['total']['sgst']    += (float)($r['sgst'] ?? 0);
            $chapters[$chap]['total']['cess']    += (float)($r['cess'] ?? 0);
            $chapters[$chap]['total']['total']   += (float)($r['total_tax'] ?? 0);
        }
        return $chapters;
    };

    // sort chapters ascending, NA last
    $sortChapters = function (array $chapters): array {
        $keys = array_keys($chapters);
        usort($keys, function($a,$b){
            if ($a === 'NA' && $b !== 'NA') return 1;
            if ($b === 'NA' && $a !== 'NA') return -1;
            return strcmp($a, $b);
        });
        $sorted = [];
        foreach ($keys as $k) $sorted[$k] = $chapters[$k];
        return $sorted;
    };

    $outChaps = $sortChapters($groupByChapter($outwardRows));
    $inChaps  = $sortChapters($groupByChapter($inwardRows));

    $calcGrand = function (array $chapters): array {
        $g = ['taxable'=>0,'igst'=>0,'cgst'=>0,'sgst'=>0,'cess'=>0,'total'=>0];
        foreach ($chapters as $chap) {
            $g['taxable'] += $chap['total']['taxable'];
            $g['igst']    += $chap['total']['igst'];
            $g['cgst']    += $chap['total']['cgst'];
            $g['sgst']    += $chap['total']['sgst'];
            $g['cess']    += $chap['total']['cess'];
            $g['total']   += $chap['total']['total'];
        }
        return $g;
    };

    $totOut = $calcGrand($outChaps);
    $totIn  = $calcGrand($inChaps);

    // build grid in condensed order: chapters first, then total
    $out = [];
    $pushRow = function (string $tableKey, string $label, array $vals, string $tag, array $params = []) use (&$out, $fromYmd, $toYmd) {
        $out[] = array_merge([
            'ishsn'      => 1,
            'table_key'  => $tableKey,
            'from_date'  => $fromYmd,
            'to_date'    => $toYmd,
            'table_name' => $label,
            'tag'        => $tag,
            'params'     => empty($params) ? null : $params,
        ], $vals);
    };
    $blankRow = function () use (&$out, $fromYmd, $toYmd) {
        $out[] = [
            'ishsn' => 1,
            'table_key' => '',
            'from_date' => $fromYmd,
            'to_date' => $toYmd,
            'table_name' => '',
            'invoice_value' => '',
            'taxable_value' => '',
            'igst' => '',
            'cgst' => '',
            'sgst' => '',
            'cess' => '',
            'total_tax' => '',
            'sm_invoice_value' => 0,
            'sm_taxable_value' => 0,
            'sm_igst' => 0,
            'sm_cgst' => 0,
            'sm_sgst' => 0,
            'sm_cess' => 0,
            'sm_total_tax' => 0,
            'tag' => 'divider',
            'params' => null,
        ];
    };
    $emptyVals = $mkVals(0,0,0,0,0,0,true);
    $seq = 1; $pk = function($n) use (&$seq){ return sprintf('%02d_%s',$seq++,$n); };

    // OUTWARD: chapters first, then total
    foreach ($outChaps as $chapCode => $chapData) {
        $chapLabel = ($chapCode === 'NA') ? '<strong>NA</strong>' : ('<strong>CHAPTER ' . $chapCode . '</strong>');
        $pushRow($pk('hsn_out_ch_hdr_' . $chapCode), $chapLabel, $emptyVals, 'hsn_outward_chapter_header',
            ['from'=>$fromYmd,'to'=>$toYmd,'is_outward'=>1,'chapter'=>$chapCode]);
        foreach ($chapData['hsn_rows'] as $r) {
            $hsnCode = $r['hsn_code'] ?? 'NA';
            $hsnLabel = ($hsnCode === 'NA') ? 'NA' : $hsnCode;
            $vals = $mkVals(
                (float)($r['taxable_value'] ?? 0),
                (float)($r['igst'] ?? 0),
                (float)($r['cgst'] ?? 0),
                (float)($r['sgst'] ?? 0),
                (float)($r['cess'] ?? 0),
                (float)($r['total_tax'] ?? 0)
            );
            $pushRow($pk('hsn_out_' . $chapCode . '_' . $hsnCode), $hsnLabel, $vals, 'hsn_outward_detail',
                ['from'=>$fromYmd,'to'=>$toYmd,'is_outward'=>1,'chapter'=>$chapCode,'hsn'=>$hsnCode]);
        }
    }
    $pushRow(
        $pk('total_outward_supply_hsn'),
        '<strong>TOTAL OUTWARD SUPPLY (HSN-WISE)</strong>',
        $mkVals($totOut['taxable'],$totOut['igst'],$totOut['cgst'],$totOut['sgst'],$totOut['cess'],$totOut['total']),
        'hsn_outward_total'
    );

    $blankRow();

    // INWARD: chapters first, then total
    foreach ($inChaps as $chapCode => $chapData) {
        $chapLabel = ($chapCode === 'NA') ? '<strong>NA</strong>' : ('<strong>CHAPTER ' . $chapCode . '</strong>');
        $pushRow($pk('hsn_in_ch_hdr_' . $chapCode), $chapLabel, $emptyVals, 'hsn_inward_chapter_header',
            ['from'=>$fromYmd,'to'=>$toYmd,'is_outward'=>2,'chapter'=>$chapCode]);
        foreach ($chapData['hsn_rows'] as $r) {
            $hsnCode = $r['hsn_code'] ?? 'NA';
            $hsnLabel = ($hsnCode === 'NA') ? 'NA' : $hsnCode;
            $vals = $mkVals(
                (float)($r['taxable_value'] ?? 0),
                (float)($r['igst'] ?? 0),
                (float)($r['cgst'] ?? 0),
                (float)($r['sgst'] ?? 0),
                (float)($r['cess'] ?? 0),
                (float)($r['total_tax'] ?? 0)
            );
            $pushRow($pk('hsn_in_' . $chapCode . '_' . $hsnCode), $hsnLabel, $vals, 'hsn_inward_detail',
                ['from'=>$fromYmd,'to'=>$toYmd,'is_outward'=>2,'chapter'=>$chapCode,'hsn'=>$hsnCode]);
        }
    }
    $pushRow(
        $pk('total_inward_supply_hsn'),
        '<strong>TOTAL INWARD SUPPLY (HSN-WISE)</strong>',
        $mkVals($totIn['taxable'],$totIn['igst'],$totIn['cgst'],$totIn['sgst'],$totIn['cess'],$totIn['total']),
        'hsn_inward_total'
    );

    return [
        'totalRecords' => count($out),
        'curPage'      => '1',
        'data'         => $out,
    ];
}
public function load_gstr2ab_listings($from_date, $to_date,$key,$states,$limit,$pq_curPage){
		 $tableinfo     = array('cmp_tax_short_code'=>array('REGSPLY'),'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>array('B2B'),'outsup_eco'=>'0','value'=>0);	
		 $response_info = $this->Gstr2abModel->gstr2ab_transactions($key,$from_date, $to_date,$tableinfo, $states,$limit,$pq_curPage);
		/* 
		if($key==5){
		$response_info = $this->Gstr2abModel->gstr2ab_transactions($key,$from_date, $to_date,$tableinfo, $states,$limit,$pq_curPage);
		}
		else if($key=='5A_IMP' || $key=='5B_RCDSEZ'){
		$response_info = $this->Gstr2abModel->IMPORTS_SEZ_SUPPLIES_transactions($key,$from_date, $to_date, $states,$limit,$pq_curPage);
		}
		else if($key=='7A'){
		$response_info = $this->Gstr2abModel->interstatesupply_transactions($from_date, $to_date, $states,$limit,$pq_curPage);
		}else if($key=='7A_1' || $key=='7A_2' || $key=='7A_3' || $key=='7A_4'){
		$response_info = $this->Gstr2abModel->INTERSTATE_SUPPLIES7A_transactions($key,$from_date, $to_date, $states,$limit,$pq_curPage);
		}
		else if($key=='7B_1' || $key=='7B_2' || $key=='7B_3' || $key=='7B_4'){
		$response_info = $this->Gstr2abModel->INTERSTATE_SUPPLIES7B_transactions($key,$from_date, $to_date,$states,$limit,$pq_curPage);
		}
		else if($key=='4A_B2B' || $key=='4A_B2BUR' || $key=='4A_IMPRT'){
		$response_info = $this->Gstr2abModel->B2B_LIABLE_RCM4_transactions($key,$from_date, $to_date, $states,$limit,$pq_curPage);
		}
		else if($key=='7B'){
		$response_info = $this->Gstr2abModel->intrastatesupply_transactions($key,$from_date, $to_date, $states,$limit,$pq_curPage);
		}else if($key=='13'){
		$response_info = $this->Gstr2abModel->hsnsummary_transactions($key,$from_date, $to_date, $states,$limit,$pq_curPage);
		}
		else{
		  $response_info = $this->Gstr2abModel->gstr2ab_transactions($key,$from_date, $to_date,$tableinfo, $states,$limit,$pq_curPage);
		} */
		return $response_info;
	}
	
	public function prepare_extended_table_data($key, $lable, $from_date, $to_date, $result, $tableinfo, $method = 'gstr2ab_table_info') {

   if($key==5){
	$response_info = $this->Gstr2abModel->importsezsupply_table_info($from_date, $to_date, $result, $tableinfo);
    }
	else if($key=='5A_IMP' || $key=='5B_RCDSEZ'){
	$response_info = $this->Gstr2abModel->IMPORTS_SEZ_SUPPLIES_table_info($from_date, $to_date, $result, $tableinfo);
    }
	else if($key=='7A'){
	$response_info = $this->Gstr2abModel->interstatesupply_table_info($from_date, $to_date, $result, $tableinfo);
    }else if($key=='7A_1' || $key=='7A_2' || $key=='7A_3' || $key=='7A_4'){
	$response_info = $this->Gstr2abModel->INTERSTATE_SUPPLIES7A_table_info($from_date, $to_date, $result, $tableinfo);
    }
	else if($key=='7B_1' || $key=='7B_2' || $key=='7B_3' || $key=='7B_4'){
	$response_info = $this->Gstr2abModel->INTERSTATE_SUPPLIES7B_table_info($from_date, $to_date, $result, $tableinfo);
    }
	else if($key=='4A_B2B' || $key=='4A_B2BUR' || $key=='4A_IMPRT'){
	$response_info = $this->Gstr2abModel->B2B_LIABLE_RCM4_table_info($from_date, $to_date, $result, $tableinfo);
    }
	else if($key=='7B'){
	$response_info = $this->Gstr2abModel->intrastatesupply_table_info($from_date, $to_date, $result, $tableinfo);
    }else if($key=='13'){
	$response_info = $this->Gstr2abModel->hsnsummary_table_info($from_date, $to_date, $result, $tableinfo);
    }
	else{
	 $response_info = $this->Gstr2abModel->$method($from_date, $to_date, $result, $tableinfo);
	}
    if (!$response_info) return null;

    return array(
        'tableno' => $key,
        'htableno' => $key,
        'from_date' => $from_date,
        'to_date' => $to_date,
        'table_name' => ucwords($lable),
        'total_records' => $response_info['total_vouchers'],
        'invoice_value' => formatAmount($response_info['invoice_value']),
        'taxable_value' => formatAmount($response_info['taxable_amt']),
        'igst' => formatAmount($response_info['igst']),
        'cgst' => formatAmount($response_info['cgst']),
        'sgst' => formatAmount($response_info['sgst']),
        'cess' => formatAmount($response_info['cess']),
        'total_tax' => formatAmount($response_info['total_tax']),
        'sm_invoice_value' => parseAmount($response_info['invoice_value']),
        'sm_taxable_value' => parseAmount($response_info['taxable_amt']),
        'sm_igst' => parseAmount($response_info['igst']),
        'sm_cgst' => parseAmount($response_info['cgst']),
        'sm_sgst' => parseAmount($response_info['sgst']),
        'sm_cess' => parseAmount($response_info['cess']),
        'sm_total_tax' => parseAmount($response_info['total_tax'])
    ); 
}
	function load_gstr2ab_condensed($from_date, $to_date,$table_lists){
		 $start = microtime(true);
	        $to_date          = date("Y-m-t", strtotime($to_date));
	        $voucher_tbl      = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	      	$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	      	
	      	$builder = $this->db->table($voucher_tbl);
	      	$builder->select($voucher_tbl.'.*');
	      	$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	      	$builder->select($voucher_type_tbl.'.comp_vch_type');	      	
	      	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	      	$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
	      	if($from_date!='')
	      		$builder->where('voucher_date >=', $from_date);
	      	if($to_date!='')
	      		$builder->where('voucher_date <=', $to_date);			
	      	$builder->where($voucher_tbl.'.bo_id', $this->bo_id);	  
			$builder->whereIn($voucher_type_tbl.'.voucher_type_id', [11,3]);	
		    $builder->whereNotIn('voucher_tag', ['OPTIONL','RJVHTXP']);
	      	$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$result = $builder->get()->getResultArray();
	
			 $customMap = [
			'3' => ['tableinfo' => ['tablekey'=>'3','cmp_tax_short_code'=>'REGSPLY','outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>['B2B'],'outsup_eco'=>'0','value'=>0]],
			'4' => ['tableinfo' => ['tablekey'=>'4','cmp_tax_short_code'=>'REGSPLY','outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'1','outsup_inv_type'=>['B2BRCM'],'outsup_eco'=>'0','value'=>0]],
			'5' => ['tableinfo' => ['tablekey'=>'5','cmp_tax_short_code'=>'REGSPLY','outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0']],
			'7A' =>['tableinfo' => ['tablekey'=>'7A','cmp_tax_short_code'=>['EXMSPLY','NILSPLY','NONGSTS'],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']],
			'7B' =>['tableinfo' => ['tablekey'=>'7B','cmp_tax_short_code'=>['EXMSPLY','NILSPLY','NONGSTS'],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']],
			'13' =>['tableinfo' => ['tablekey'=>'13','cmp_tax_short_code'=>[],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']]
			];

			$records = [];
			foreach ($table_lists as $key => $lable) {
				if (isset($customMap[$key])) {
					$cfg = $customMap[$key];
					$record = $this->prepare_extended_table_data($key, $lable, $from_date, $to_date, $result, $cfg['tableinfo']);
					if ($record) $records[] = $record;
				} elseif (in_array($key,['6','8','9','10','11','12'])) {
					$records[] = [
						'tableno' => $key,
						'htableno' => $key,
						'from_date' => $from_date,
						'to_date' => $to_date,
						'table_name' => ucwords($lable),
						'total_records' => 0,
						'invoice_value' => formatAmount(0),
						'taxable_value' => formatAmount(0),
						'igst' => formatAmount(0),
						'cgst' => formatAmount(0),
						'sgst' => formatAmount(0),
						'cess' => formatAmount(0),
						'total_tax' => formatAmount(0),
						'sm_invoice_value' => parseAmount(0),
						'sm_taxable_value' => parseAmount(0),
						'sm_igst' => parseAmount(0),
						'sm_cgst' => parseAmount(0),
						'sm_sgst' => parseAmount(0),
						'sm_cess' => parseAmount(0),
						'sm_total_tax' => parseAmount(0)
					];
				} else {
					$records[] = [
						'tableno' => $key,
						'htableno' => $key,
						'from_date' => $from_date,
						'to_date' => $to_date,
						'table_name' => ucwords($lable),
						'total_records' => 0,
						'invoice_value' => formatAmount(0),
						'taxable_value' => formatAmount(0),
						'igst' => formatAmount(0),
						'cgst' => formatAmount(0),
						'sgst' => formatAmount(0),
						'cess' => formatAmount(0),
						'total_tax' => formatAmount(0),
						'sm_invoice_value' => parseAmount(0),
						'sm_taxable_value' => parseAmount(0),
						'sm_igst' => parseAmount(0),
						'sm_cgst' => parseAmount(0),
						'sm_sgst' => parseAmount(0),
						'sm_cess' => parseAmount(0),
						'sm_total_tax' => parseAmount(0)
					];
				}
			}
	 
	 $elapsed = round((microtime(true) - $start) * 1000, 3);
	  $lsquery = $this->db->getlastquery().'<br />' ;
			$qry_response=['url'=>current_url(true)->getPath(),'query'=>$lsquery,'time_seconds'=>$elapsed];
			
	 $totalrec   = count($records);
	 $pq_curPage =1;
     return  "{\"totalRecords\":" .$totalrec . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
	
	}
	
	function load_gstr2ab_detailed($from_date, $to_date,$table_lists){
	        $to_date          = date("Y-m-t", strtotime($to_date));
	        $voucher_tbl      = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	      	$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	      	
	      	$builder = $this->db->table($voucher_tbl);
	      	$builder->select($voucher_tbl.'.*');
	      	$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	      	$builder->select($voucher_type_tbl.'.comp_vch_type');	      	
	      	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	      	$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
	      	if($from_date!='')
	      		$builder->where('voucher_date >=', $from_date);
	      	if($to_date!='')
	      		$builder->where('voucher_date <=', $to_date);			
	      	$builder->where($voucher_tbl.'.bo_id', $this->bo_id);	  
			$builder->whereIn($voucher_type_tbl.'.voucher_type_id', [11,3]);	
		    $builder->whereNotIn('voucher_tag', ['OPTIONL','RJVHTXP']);
	      	$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$result = $builder->get()->getResultArray();
	
			 $customMap = [
			'3' => ['tableinfo' => ['tablekey'=>'3','cmp_tax_short_code'=>'REGSPLY','outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>['B2B'],'outsup_eco'=>'0','value'=>0]],
			'4' => ['tableinfo' => ['tablekey'=>'4','cmp_tax_short_code'=>'REGSPLY','outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'1','outsup_inv_type'=>['B2BRCM'],'outsup_eco'=>'0','value'=>0]],
			'4A_B2B' => ['tableinfo' => ['tablekey'=>'4A_B2B','cmp_tax_short_code'=>['REGSPLY'],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'1','outsup_inv_type'=>['B2BRCM'],'outsup_eco'=>'0','value'=>0]],
			'4A_B2BUR' => ['tableinfo' => ['tablekey'=>'4A_B2BUR','cmp_tax_short_code'=>['REGSPLY'],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'1','outsup_inv_type'=>['B2BRCM'],'outsup_eco'=>'0','value'=>0]],
			'4A_IMPRT' => ['tableinfo' => ['tablekey'=>'4A_IMPRT','cmp_tax_short_code'=>['REGSPLY'],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'1','outsup_inv_type'=>['B2BRCM'],'outsup_eco'=>'0','value'=>0]],
			'5' => ['tableinfo' => ['tablekey'=>'5','cmp_tax_short_code'=>'REGSPLY','outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0']],
			'5A_IMP' => ['tableinfo' => ['tablekey'=>'5A_IMP','cmp_tax_short_code'=>'REGSPLY','outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0']],
			'5B_RCDSEZ' => ['tableinfo' => ['tablekey'=>'5B_RCDSEZ','cmp_tax_short_code'=>'REGSPLY','outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0']],
			'7A' =>['tableinfo' => ['tablekey'=>'7A','cmp_tax_short_code'=>['EXMSPLY','NILSPLY','NONGSTS'],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']],
			'7A_1' =>['tableinfo' => ['tablekey'=>'7A_1','cmp_tax_short_code'=>[],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']],
			'7A_2' =>['tableinfo' => ['tablekey'=>'7A_2','cmp_tax_short_code'=>['EXMSPLY'],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']],
			'7A_3' =>['tableinfo' => ['tablekey'=>'7A_3','cmp_tax_short_code'=>['NILSPLY'],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']],
			'7A_4' =>['tableinfo' => ['tablekey'=>'7A_4','cmp_tax_short_code'=>['NONGSTS'],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']],
			'7B' =>['tableinfo' => ['tablekey'=>'7B','cmp_tax_short_code'=>['EXMSPLY','NILSPLY','NONGSTS'],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']],
			'7B_1' =>['tableinfo' => ['tablekey'=>'7B_1','cmp_tax_short_code'=>[],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']],
			'7B_2' =>['tableinfo' => ['tablekey'=>'7B_2','cmp_tax_short_code'=>['EXMSPLY'],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']],
			'7B_3' =>['tableinfo' => ['tablekey'=>'7B_3','cmp_tax_short_code'=>['NILSPLY'],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']],
			'7B_4' =>['tableinfo' => ['tablekey'=>'7B_4','cmp_tax_short_code'=>['NONGSTS'],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']],
			'13' =>['tableinfo' => ['tablekey'=>'13','cmp_tax_short_code'=>[],'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>[],'outsup_eco'=>'0','value'=>'','cnd'=>'<=']]
			];

			$records = [];
			foreach ($table_lists as $key => $lable) {
				if (isset($customMap[$key])) {
					$cfg = $customMap[$key];
					$record = $this->prepare_extended_table_data($key, $lable, $from_date, $to_date, $result, $cfg['tableinfo']);
					if ($record) $records[] = $record;
				} elseif (in_array($key,['6','8','9','10','11','12'])) {
					$records[] = [
						'tableno' => $key,
						'htableno' => $key,
						'from_date' => $from_date,
						'to_date' => $to_date,
						'table_name' => ucwords($lable),
						'total_records' => 0,
						'invoice_value' => formatAmount(0),
						'taxable_value' => formatAmount(0),
						'igst' => formatAmount(0),
						'cgst' => formatAmount(0),
						'sgst' => formatAmount(0),
						'cess' => formatAmount(0),
						'total_tax' => formatAmount(0),
						'sm_invoice_value' => parseAmount(0),
						'sm_taxable_value' => parseAmount(0),
						'sm_igst' => parseAmount(0),
						'sm_cgst' => parseAmount(0),
						'sm_sgst' => parseAmount(0),
						'sm_cess' => parseAmount(0),
						'sm_total_tax' => parseAmount(0)
					];
				} else {
					$records[] = [
						'tableno' => $key,
						'htableno' => $key,
						'from_date' => $from_date,
						'to_date' => $to_date,
						'table_name' => ucwords($lable),
						'total_records' => 0,
						'invoice_value' => formatAmount(0),
						'taxable_value' => formatAmount(0),
						'igst' => formatAmount(0),
						'cgst' => formatAmount(0),
						'sgst' => formatAmount(0),
						'cess' => formatAmount(0),
						'total_tax' => formatAmount(0),
						'sm_invoice_value' => parseAmount(0),
						'sm_taxable_value' => parseAmount(0),
						'sm_igst' => parseAmount(0),
						'sm_cgst' => parseAmount(0),
						'sm_sgst' => parseAmount(0),
						'sm_cess' => parseAmount(0),
						'sm_total_tax' => parseAmount(0)
					];
				}
			}
	 $totalrec   = count($records);
	 $pq_curPage =1;
     return  "{\"totalRecords\":" .$totalrec . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
	
	}
	
	/* ============================================================
 * INTERNAL: empty result helper
 * ============================================================ */
protected function _gstr1EmptyResult(): array
{
    return [
        'total_records'     => 0,
        'invoice_value'     => 0,
        'taxable_value'     => 0,
        'igst'              => 0,
        'cgst'              => 0,
        'sgst'              => 0,
        'cess'              => 0,
        'total_tax'         => 0,
        'hsn_ignore_footer' => false,
    ];
}
}