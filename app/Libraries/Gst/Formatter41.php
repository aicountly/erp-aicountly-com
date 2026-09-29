<?php
namespace App\Libraries\Gst;

class Formatter41 implements FormatterInterface
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
    $b2b = [];     // strictly-B2B
    $b2c = [];     // B2C + B2CL

    foreach ($rows as $r) {

        /*   Rate = IGST if >0 else CGST+SGST */
        $igst = (float) ($r['acc_igst_rate'] ?? 0);
        $rate = $igst > 0
              ? $igst
              : (float) ($r['acc_cgst_rate'] ?? 0) + (float) ($r['acc_sgst_rate'] ?? 0);

        /*  Bucket key = HSN + rate */
        $hsn  = $r['hsn_sac'] ?: '0';
        $key  = $hsn . '_' . $rate;
		$type = strtoupper($r['outsup_inv_type'] ?? '');   // B2C / EXPWP / EXPWOP / …        
        if (strtoupper($r['outsup_inv_type'] ?? '') === 'B2B') {
            $bucket =& $b2b;
        }		
		if (in_array($type, ['B2CS','B2CL', 'EXPWP', 'EXPWOP'], true)) {
			$bucket =& $b2c;  
		}
        /*  Initialise bucket row if first time */
		if($r['hsn_sac_uom_name']=='')
			$uqc = 'NA';
		else
		$uqc = trim($r['hsn_sac_uom_name']);
		if($uqc=='NA')
		 $qty = 0;
	    else 
		$qty = (float) parseAmount($r['hsn_qty']);
		if (in_array($type, ['EXPWP', 'EXPWOP'], true)) {
			if (!isset($bucket[$key])) {
            $bucket[$key] = [
                'num'        => 0,                       // fixed later
                'hsn_sc'     => (string) $hsn,
                'desc'       => $r['acc_item_desc'] ?? 'OTH',
                'user_desc'  => $r['acc_item_desc'] ?? '',
                'uqc'        => $uqc,
                'qty'        => $qty,
                'rt'         => $rate,
                'txval'      => 0,
                'iamt'       => 0,                
                'csamt'      => 0,
            ];
        }
		}
		else{
		if (!isset($bucket[$key])) {
            $bucket[$key] = [
                'num'        => 0,                       // fixed later
                'hsn_sc'     => (string) $hsn,
                'desc'       => $r['acc_item_desc'] ?? 'OTH',
                'user_desc'  => $r['acc_item_desc'] ?? '',
                'uqc'        => $uqc,
                'qty'        => $qty,
                'rt'         => $rate,
                'txval'      => 0,
                'iamt'       => 0,
                'samt'       => 0,
                'camt'       => 0,
                'csamt'      => 0,
            ];
        }	
		}
		
        /*  Aggregate into the selected map */
       
        $bucket[$key]['txval'] += (float) ($r['taxable_amt']  ?? 0);
        if (in_array($type, ['EXPWP', 'EXPWOP'], true)) {
		  $bucket[$key]['iamt']  += (float) ($r['acc_igst']     ?? 0);  
	    }
	    else{
		 $bucket[$key]['samt']  += (float) ($r['acc_sgst']     ?? 0);
         $bucket[$key]['camt']  += (float) ($r['acc_cgst']     ?? 0);   
	    }
        $bucket[$key]['csamt'] += (float) ($r['acc_cess']     ?? 0);
        unset($bucket);   // break the reference before the next loop round
    }

    /*  Re-index and serial-number each list */
    $renumber = static function (array $map): array {
        $list = array_values($map);
        foreach ($list as $i => &$row) { $row['num'] = $i + 1; }
        return $list;
    };

    return [
        'hsn_b2b' => $renumber($b2b),
        'hsn_b2c' => $renumber($b2c),
    ];
}

  private function exprt(array $rows): array
    { 
    return ['inv'=>$rows];
    }
    private function b2b(array $rows): array
    {
      
		 return $rows;
    }
	private function b2cl(array $rows): array
    {      
        return $rows;
    }
	private function b2cs(array $rows): array
    {      
        return $rows;
    }
}