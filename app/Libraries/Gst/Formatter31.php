<?php
namespace App\Libraries\Gst;

class Formatter31 implements FormatterInterface
{
	public function format(array $invoice,string $section_type): array
    {
        switch ($section_type) {
            case 'hsn':
                return $this->hsn($invoice);   // v4.1 keeps split buckets
            case 'b2b':
                return $this->b2b($invoice);
			case 'b2cl':
                return $this->b2cl($invoice);
            case 'b2cs':
                return $this->b2cs($invoice);
		    case 'export':
                return $this->exprt($invoice);// export
            default:
                throw new \InvalidArgumentException("Unknown section $section_type");
        }
    }
	
	
    private function hsn(array $rows): array
    {
      $bucket = [];
      foreach ($rows as $r) {
        /* GST rate: pick IGST if present else CGST+SGST */
        $rate = (float)parseAmount($r['acc_igst_rate']) ?: (
                (float)parseAmount($r['acc_cgst_rate']) +
                (float)parseAmount($r['acc_sgst_rate'])
        );
        $hsn       = $r['hsn_sac'] ?: '';
        $bucketKey = $hsn . '_' . $rate;             // unique bucket id
		if($r['hsn_sac_uom_name']=='')
			$uqc = 'NA';
		else
		$uqc = trim($r['hsn_sac_uom_name']);
		$type = strtoupper($r['outsup_inv_type'] ?? '');
		if($uqc=='NA')
		 $qty = 0;
	    else 
		$qty = (float) parseAmount ($r['hsn_qty']);
        if (!isset($bucket[$bucketKey])) {
            $bucket[$bucketKey] = [
                'num'    => 0,                       // temp; fix later
                'hsn_sc' => (string)$hsn,
                'desc'   => 'OTH',
                'uqc'    => $uqc,
                'qty'    => $qty,
                'rt'     => $rate,
                'txval'  => 0,
                'iamt'   => 0,
                'samt'   => 0,
                'camt'   => 0,
                'csamt'  => 0,
            ];
        }
		
        // simple counts; change if you need actual quantity field
        $bucket[$bucketKey]['qty']   += $qty;
        $bucket[$bucketKey]['txval'] += (float)parseAmount($r['taxable_amt']);
		if($r['acc_igst']==0){
		 $bucket[$bucketKey]['samt']  += (float)parseAmount($r['acc_sgst']);
         $bucket[$bucketKey]['camt']  += (float)parseAmount($r['acc_cgst']);	
		}
		else{
		$bucket[$bucketKey]['iamt']  += (float)parseAmount($r['acc_igst']);
		}
        
        $bucket[$bucketKey]['csamt'] += (float)parseAmount($r['acc_cess']);
       }

		/* ── final tidy-up: re-index + renumber “num” ──────────────────── */
		$data = array_values($bucket);        // reset numeric keys
		foreach ($data as $idx => &$row) {
			$row['num'] = $idx + 1;           // 1-based serial
		}
		unset($row);
        return [
        'data' => $data
        ];
    }
	private function exprt(array $rows): array
    { 
    return ['inv'=>$rows];
    }
	private function b2b(array $rows): array
    {
        // Structure  matches 3.1 spec
        return $rows;
    }
	private function b2cl(array $rows): array
    {
        // Structure  matches 3.1 spec
        return $rows;
    }
	private function b2cs(array $rows): array
    {      
        return $rows;
    }
}