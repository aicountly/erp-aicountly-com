<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\CommonModel;
use App\Libraries\enc_string;
class TaxcategoryModel extends Model	{
    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->CommonModel   =  new CommonModel();	
	   $this->session       = \Config\Services::session();
	   $this->bo_id         = $this->session->get('ses_boid');
	   $this->fy_id         = $this->session->get('ses_comp_fy_id');
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->sub_types     = [1=>"TAXABLE", 2=>"EXEMPT",3=>"NIL RATED",4=>"NON GST"];
    } 
    
   public function add_category($data)
{
    $tax_cat_name   = strtolower(trim($data['tax_cat_name']));
    $tax_cat_type   = $data['tax_cat_type'];
    $tax_cat_rates  = $data['tax_cat_rates']; // array of rates
    $tax_cat_wef    = $data['tax_cat_wef'];   // array of dates
    $fy_last_date   = $this->session->get('ses_company_fy_end'); // FY end date

    $subTypeNames = [
        1 => "IGST",
        2 => "CGST",
        3 => "SGST",
        4 => "UT GST",
        5 => "Cess (GST)"
    ];

    // --------------------------
    // 1. Unique Category Name Check
    // --------------------------
    $builder = $this->db->table("taxcatmstn t");
    $builder->select("t.tax_cat_mst_id")
            ->join("taxcatrate r", "t.tax_cat_mst_id = r.tax_cat_mst_id AND t.cmp_id = r.cmp_id", "inner")
            ->where("t.cmp_id", $this->company_id)
            ->where("t.tax_cat_type", 1)
            ->where("LOWER(t.tax_cat_name)", $tax_cat_name);
    $exists = $builder->countAllResults();
    if ($exists > 0) {
        return [
            'tax_cat_id' => 0,
            'status'     => false,
            'message'    => 'Category name should be unique.'
        ];
    }

    // --------------------------
    // 2. Duplicate Rate Check (IGST + Cess combination must be unique)
    // --------------------------
    $igstRate = isset($tax_cat_rates[1]) && $tax_cat_rates[1] !== '' && $tax_cat_rates[1] !== null
                ? parseAmount($tax_cat_rates[1]) : null;
    $cessRate = isset($tax_cat_rates[5]) && $tax_cat_rates[5] !== '' && $tax_cat_rates[5] !== null
                ? parseAmount($tax_cat_rates[5]) : null;

    if ($igstRate !== null) {
        $builder = $this->db->table("taxcatmstn t");
        $builder->select("t.tax_cat_mst_id, t.tax_cat_name")
            ->join("taxcatrate r1", "t.tax_cat_mst_id = r1.tax_cat_mst_id AND t.cmp_id = r1.cmp_id AND r1.tax_cat_sub_type = 1", "inner")
            ->where("t.cmp_id", $this->company_id)
            ->where("t.tax_cat_type", 1)
            ->where("r1.tax_cat_rate", $igstRate);

        if ($cessRate !== null) {
            // Both IGST and Cess provided — check combination
            $builder->join("taxcatrate r2", "t.tax_cat_mst_id = r2.tax_cat_mst_id AND t.cmp_id = r2.cmp_id AND r2.tax_cat_sub_type = 5", "inner")
                ->where("r2.tax_cat_rate", $cessRate);
        } else {
            // IGST provided but no Cess — check IGST matches where Cess is also 0 or absent
            $builder->join("taxcatrate r2", "t.tax_cat_mst_id = r2.tax_cat_mst_id AND t.cmp_id = r2.cmp_id AND r2.tax_cat_sub_type = 5", "left")
                ->groupStart()
                    ->where("r2.tax_cat_rate IS NULL")
                    ->orWhere("r2.tax_cat_rate", 0)
                ->groupEnd();
        }

        $exists = $builder->get()->getRowArray();
        if ($exists) {
            $cessMsg = $cessRate !== null ? " + Cess @ {$cessRate}%" : "";
            return [
                'tax_cat_id' => 0,
                'status'     => false,
                'message'    => "IGST @ {$igstRate}%{$cessMsg} combination already exists in \"{$exists['tax_cat_name']}\"."
            ];
        }
    }

    // --------------------------
    // 3. Date Validation:
    //    - New entry must be strictly > last saved date
    //    - Must also be <= FY end date
    // --------------------------
    if (!empty($tax_cat_wef[1])) {  // IGST date given
        $newDate = date('Y-m-d', strtotime($tax_cat_wef[1]));
        $fyEnd   = date('Y-m-d', strtotime($fy_last_date));

        // Check against last saved date
        $lastDateRow = $this->db->table("taxcatrate r")
            ->select("MAX(r.tax_cat_wef) as last_date")
            ->join("taxcatmstn t", "t.tax_cat_mst_id = r.tax_cat_mst_id AND t.cmp_id = r.cmp_id", "inner")
            ->where("t.cmp_id", $this->company_id)
            ->whereIn("r.tax_cat_sub_type", [1, 2, 3, 4]) // IGST + CGST + SGST + UTGST
            ->get()->getRowArray();

        if ($lastDateRow && !empty($lastDateRow['last_date'])) {
            if (strtotime($newDate) <= strtotime($lastDateRow['last_date'])) {
                return [
                    'status'  => false,
                    'message' => "New WEF date must be greater than last saved date (" . $lastDateRow['last_date'] . ")"
                ];
            }
        }

        // Check against FY end date
        if (strtotime($newDate) > strtotime($fyEnd)) {
            return [
                'status'  => false,
                'message' => "WEF date must not exceed Financial Year End (" . $fyEnd . ")"
            ];
        }
    }

    // --------------------------
    // 4. Insert into Master Table
    // --------------------------
    $insert_data = [
        'cmp_id'            => $this->company_id,
        'tax_cat_type'      => $data['tax_cat_type'],
        'tax_cat_name'      => ucwords(clean($data['tax_cat_name'])),
        'tax_cat_section'   => $data['tax_cat_section'],
        'tax_cat_is_active' => $data['tax_cat_is_active']
    ];

    $this->db->table("taxcatmstn")->insert($insert_data);
    $tax_cat_mst_id = $this->db->insertID();

    // --------------------------
    // 5. Insert Rates + Sync Dates
    // --------------------------
    if (!empty($tax_cat_rates) && is_array($tax_cat_rates)) {
        foreach ($tax_cat_rates as $rate_type => $rate_value) {
            if ($rate_value === '' || $rate_value === null) continue;

            // Assign WEF date
            if ($rate_type == 1) {
                $wef = $tax_cat_wef[1]; // IGST date given
            } elseif (in_array($rate_type, [2, 3, 4])) {
                $wef = $tax_cat_wef[1]; // same as IGST
            } else {
                $wef = isset($tax_cat_wef[$rate_type]) ? $tax_cat_wef[$rate_type] : null;
            }

            if ($wef) {
                $wefDate = date('Y-m-d', strtotime($wef));
                $fyEnd   = date('Y-m-d', strtotime($fy_last_date));

                // Ensure within FY
                if (strtotime($wefDate) > strtotime($fyEnd)) {
                    return [
                        'status'  => false,
                        'message' => "Rate WEF date (" . $wefDate . ") for " . ($subTypeNames[$rate_type] ?? "sub-type") .
                                     " must not exceed Financial Year End (" . $fyEnd . ")"
                    ];
                }

                $rate_data = [
                    'cmp_id'           => $this->company_id,
                    'tax_cat_mst_id'   => $tax_cat_mst_id,
                    'tax_cat_sub_type' => $rate_type,
                    'tax_cat_rate'     => parseAmount($rate_value),
                    'tax_cat_wef'      => $wefDate
                ];

                $this->db->table("taxcatrate")->insert($rate_data);
            }
        }
    }

    return [
        'tax_cat_id' => $tax_cat_mst_id,
        'status'     => true,
        'message'    => 'Inserted Successfully'
    ];
}
	   
 function category_info($tax_cat_id)
    {
        $rows = $this->db->table('taxcatmstn tc')
            ->select('tc.*, ts.tax_cat_sub_type, ts.tax_cat_rate, ts.tax_cat_wef')
            ->join('taxcatrate ts', 'ts.tax_cat_mst_id = tc.tax_cat_mst_id', 'left')
            ->where('tc.tax_cat_mst_id', $tax_cat_id)
            ->get()
            ->getResultArray();
    
        if (!$rows) {
            return [];
        }
    
        // Take first row as master category info
        $category_info = $rows[0];
    
        // Prepare rates as [sub_type => rate]
        $category_info['tax_cat_rates'] = [];
        $category_info['tax_cat_wef'] = [];
        foreach ($rows as $row) {
            if (!empty($row['tax_cat_sub_type'])) {
                $category_info['tax_cat_rates'][$row['tax_cat_sub_type']] = $row['tax_cat_rate'];
                $category_info['tax_cat_wef'][$row['tax_cat_sub_type']] =  date('d-m-Y',strtotime($row['tax_cat_wef']));
            }
        }
    
        return $category_info;
    }
   
   public function update_category($data, $tax_cat_mst_id)
{
    $tax_cat_name  = strtolower(trim($data['tax_cat_name']));
    $tax_cat_type  = $data['tax_cat_type'];
    $tax_cat_rates = $data['tax_cat_rates']; // array of rates
    $tax_cat_wef   = $data['tax_cat_wef'];   // array of dates
    $sub_type_id   = $data['sub_type_id'];   // array or scalar
    $fy_last_date  = $this->session->get('ses_company_fy_end'); // FY end date
    $fyEnd         = date('Y-m-d', strtotime($fy_last_date));

    // --- Step 1: Check duplicate category name for same type (case-insensitive) ---
    $builder = $this->db->table("taxcatmstn t");
    $builder->select("t.tax_cat_mst_id")
        ->join("taxcatrate r", "t.tax_cat_mst_id = r.tax_cat_mst_id AND t.cmp_id = r.cmp_id", "inner")
        ->where("t.cmp_id", $this->company_id)
        ->where("t.tax_cat_type", 1)
        ->where("t.tax_cat_mst_id !=", $tax_cat_mst_id)
        ->where("LOWER(t.tax_cat_name)", $tax_cat_name);

    $exists = $builder->countAllResults();
    if ($exists > 0) {
        return ['tax_cat_id' => 0, 'status' => false, 'message' => 'Category name should be unique.'];
    }

    // --- Step 2: Check duplicate rates (IGST + Cess combination must be unique) ---
$igstRate = isset($tax_cat_rates[1]) && $tax_cat_rates[1] !== '' && $tax_cat_rates[1] !== null
            ? parseAmount($tax_cat_rates[1]) : null;
$cessRate = isset($tax_cat_rates[5]) && $tax_cat_rates[5] !== '' && $tax_cat_rates[5] !== null
            ? parseAmount($tax_cat_rates[5]) : null;

if ($igstRate !== null) {
    $builder = $this->db->table("taxcatmstn t");
    $builder->select("t.tax_cat_mst_id, t.tax_cat_name")
        ->join("taxcatrate r1", "t.tax_cat_mst_id = r1.tax_cat_mst_id AND t.cmp_id = r1.cmp_id AND r1.tax_cat_sub_type = 1", "inner")
        ->where("t.cmp_id", $this->company_id)
        ->where("t.tax_cat_type", 1)
        ->where("t.tax_cat_mst_id !=", $tax_cat_mst_id)
        ->where("r1.tax_cat_rate", $igstRate);

    if ($cessRate !== null) {
        // Both IGST and Cess provided — check combination
        $builder->join("taxcatrate r2", "t.tax_cat_mst_id = r2.tax_cat_mst_id AND t.cmp_id = r2.cmp_id AND r2.tax_cat_sub_type = 5", "inner")
            ->where("r2.tax_cat_rate", $cessRate);
    } else {
        // IGST provided but no Cess — check IGST matches where Cess is also 0 or absent
        $builder->join("taxcatrate r2", "t.tax_cat_mst_id = r2.tax_cat_mst_id AND t.cmp_id = r2.cmp_id AND r2.tax_cat_sub_type = 5", "left")
            ->groupStart()
                ->where("r2.tax_cat_rate IS NULL")
                ->orWhere("r2.tax_cat_rate", 0)
            ->groupEnd();
    }

    $exists = $builder->get()->getRowArray();
    if ($exists) {
        $cessMsg = $cessRate !== null ? " + Cess @ {$cessRate}%" : "";
        return [
            'status'  => false,
            'message' => "IGST @ {$igstRate}%{$cessMsg} combination already exists in \"{$exists['tax_cat_name']}\"."
        ];
    }
}

    // --- Step 3: Update category master ---
    $update_data = [
        'tax_cat_type'    => $data['tax_cat_type'],
        'tax_cat_name'    => ucwords(clean($data['tax_cat_name'])),
        'tax_cat_section' => $data['tax_cat_section'],
        'sub_type_id'     => $data['sub_type_id']
    ];

    $this->db->table("taxcatmstn")
        ->where('cmp_id', $this->company_id)
        ->where('tax_cat_mst_id', $tax_cat_mst_id)
        ->update($update_data);

    // --- Step 4: Delete old rates for this category ---
    $this->db->table("taxcatrate")
        ->where('cmp_id', $this->company_id)
        ->where('tax_cat_mst_id', $tax_cat_mst_id)
        ->delete();

    // --- Step 5: Insert new rates with WEF dates ---
    if (!empty($tax_cat_rates) && is_array($tax_cat_rates)) {
        foreach ($tax_cat_rates as $rate_type => $rate_value) {
            if ($rate_value === '' || $rate_value === null) continue;

            // Apply IGST WEF to CGST/SGST/UTGST when IGST provided
            if ($rate_type == 1 && isset($tax_cat_wef[1]) && !empty($tax_cat_wef[1])) {
                $tax_cat_wef[2] = $tax_cat_wef[3] = $tax_cat_wef[4] = $tax_cat_wef[1];
            }

            $wef = $tax_cat_wef[$rate_type] ?? null;
            if ($wef) {
                $wefDate = date('Y-m-d', strtotime($wef));

                // FY End Validation
                if (strtotime($wefDate) > strtotime($fyEnd)) {
                    return [
                        'status'  => false,
                        'message' => "WEF date ($wefDate) for " . ($subTypeNames[$rate_type] ?? "sub-type") .
                                     " must not exceed Financial Year End ($fyEnd)"
                    ];
                }

                $rate_data = [
                    'cmp_id'           => $this->company_id,
                    'tax_cat_mst_id'   => $tax_cat_mst_id,
                    'tax_cat_sub_type' => $rate_type,
                    'tax_cat_rate'     => parseAmount($rate_value),
                    'tax_cat_wef'      => $wefDate
                ];

                $this->db->table("taxcatrate")->insert($rate_data);
            }
        }
    }

    return ['status' => true, 'tax_cat_id' => $tax_cat_mst_id];
}

  public function update_category_oldee($data, $tax_cat_mst_id)
{
    $tax_cat_name   = strtolower(trim($data['tax_cat_name']));
    $tax_cat_type   = $data['tax_cat_type'];
    $tax_cat_rates  = $data['tax_cat_rates']; // array of rates
    $tax_cat_wef    = $data['tax_cat_wef'];   // array of dates
	 $sub_type_id    = $data['sub_type_id'];   // array of dates
    $fy_last_date   = $this->session->get('ses_company_fy_end'); // FY end date
    $fyEnd          = date('Y-m-d', strtotime($fy_last_date));

    // --- Step 1: Check duplicate category name for same type (case-insensitive) ---
    $builder = $this->db->table("taxcatmstn t");
    $builder->select("t.tax_cat_mst_id")
        ->join("taxcatrate r", "t.tax_cat_mst_id = r.tax_cat_mst_id AND t.cmp_id = r.cmp_id", "inner")
        ->where("t.cmp_id", $this->company_id)
        ->where("t.tax_cat_type", 1)
        ->where("t.tax_cat_mst_id !=", $tax_cat_mst_id)
        ->where("LOWER(t.tax_cat_name)", $tax_cat_name);

    $exists = $builder->countAllResults();
    if ($exists > 0) {
        return ['tax_cat_id' => 0, 'status' => false, 'message' => 'Category name should be unique.'];
    }

    // --- Step 2: Check duplicate rates ---
    $builder = $this->db->table("taxcatmstn t");
    $builder->select("t.tax_cat_mst_id,r.tax_cat_sub_type")
        ->join("taxcatrate r", "t.tax_cat_mst_id = r.tax_cat_mst_id AND t.cmp_id = r.cmp_id", "inner")
        ->where("t.cmp_id", $this->company_id)
        ->where("t.tax_cat_type", 1)
        ->where("t.tax_cat_mst_id !=", $tax_cat_mst_id);

    $checkSubTypes = [1, 5];
    $subTypeNames = [
        1 => "IGST",
        2 => "CGST",
        3 => "SGST",
        4 => "UT GST",
        5 => "Cess (GST)"
    ];

    $builder->groupStart();
    foreach ($checkSubTypes as $subType) {
        if (isset($tax_cat_rates[$subType]) && !empty($tax_cat_rates[$subType])) {
            $builder->orGroupStart()
                ->where("r.tax_cat_sub_type", $subType)
                ->where("r.tax_cat_rate", $tax_cat_rates[$subType])
				->where("t.tax_cat_mst_id !=", $tax_cat_mst_id)
                ->groupEnd();
        }
    }
    $builder->groupEnd();

    $exists = $builder->get()->getRowArray();
    if ($exists) {
        return [
            'status' => false,
            'message' => "Rate already exists for " . $subTypeNames[$exists['tax_cat_sub_type']]
        ];
    }

    // --- Step 3: Update category master ---
    $update_data = [
        'tax_cat_type'    => $data['tax_cat_type'],
        'tax_cat_name'    => ucwords(clean($data['tax_cat_name'])),
        'tax_cat_section' => $data['tax_cat_section'],
		'sub_type_id'     => $data['sub_type_id']
        ];
	
    $this->db->table("taxcatmstn")
        ->where('cmp_id', $this->company_id)
        ->where('tax_cat_mst_id', $tax_cat_mst_id)
        ->update($update_data);

    // --- Step 4: Delete old rates for this category ---
    $this->db->table("taxcatrate")
        ->where('cmp_id', $this->company_id)
        ->where('tax_cat_mst_id', $tax_cat_mst_id)
        ->delete();

    // --- Step 5: Insert new rates with WEF dates ---
    if (!empty($tax_cat_rates) && is_array($tax_cat_rates)) {
        foreach ($tax_cat_rates as $rate_type => $rate_value) {
            if ($rate_value === '' || $rate_value === null) continue;

            // --- Fix: Apply IGST WEF to CGST/SGST/UTGST ---
            if ($rate_type == 1 && isset($tax_cat_wef[1]) && !empty($tax_cat_wef[1])) {
                $tax_cat_wef[2] = $tax_cat_wef[3] = $tax_cat_wef[4] = $tax_cat_wef[1];
            }

            $wef = $tax_cat_wef[$rate_type] ?? null;

            if ($wef) {
                $wefDate = date('Y-m-d', strtotime($wef));

                // --- FY End Validation ---
                if (strtotime($wefDate) > strtotime($fyEnd)) {
                    return [
                        'status'  => false,
                        'message' => "WEF date ($wefDate) for " . ($subTypeNames[$rate_type] ?? "sub-type") .
                                     " must not exceed Financial Year End ($fyEnd)"
                    ];
                }

                $rate_data = [
                    'cmp_id'           => $this->company_id,
                    'tax_cat_mst_id'   => $tax_cat_mst_id,
                    'tax_cat_sub_type' => $rate_type,
                    'tax_cat_rate'     => parseAmount($rate_value),
                    'tax_cat_wef'      => $wefDate
                ];

                $this->db->table("taxcatrate")->insert($rate_data);
            }
        }
    }

    return ['status' => true, 'tax_cat_id' => $tax_cat_mst_id];
}
	
   public function remove_tax_category($tax_cat_id){
     
	$count = $this->db->table('acctmaster')
                      ->where('tax_cat_mst_id', $tax_cat_id)
                      ->where('cmp_id', $this->company_id)
                      ->countAllResults();

    if ($count > 0) {
        return [
            'status' => false,
            'message' => 'Cannot delete. Tax category is already used in Account Master.'
        ];
    }   
	
	$count = $this->db->table('itemmaster')
                      ->where('tax_cat_mst_id', $tax_cat_id)
                      ->where('cmp_id', $this->company_id)
                      ->countAllResults();

    if ($count > 0) {
        return [
            'status' => false,
            'message' => 'Cannot delete. Tax category is already used in Item Master.'
        ];
    }   
     
     $this->db->table("taxcatrate")->where('tax_cat_mst_id',$tax_cat_id)->delete(); 	 
	 $this->db->table("taxcatmstn")->where('tax_cat_mst_id',$tax_cat_id)->delete(); 
	 
	 
	 return [
        'status' => true,
        'message' => 'Category deleted successfully.'
    ];
   }
    public function check_billsundry_exists($id,$name){
        $data = $this->db->table("taxcatmstn")->select('tax_cat_name')->where('	tax_cat_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(tax_cat_name)', strtolower($name))->get()->getRowArray();
       if($data)
        return true; 
        else
        return false;
     }
   
   public function get_account_name($id){
        $data = $this->db->table("taxcatmstn")->select('tax_cat_name')->where('cmp_id',$this->company_id)->where('tax_cat_mst_id', $id)->get()->getRowArray();
        $acc_name = $data['tax_cat_name'] ?? '';
        return $acc_name;
     }
   
   
   public function changestatus_single_accounts($id,$status){
    $this->db->table("taxcatmstn")->where('cmp_id',$this->company_id)->where("tax_cat_mst_id",$id)->update(["tax_cat_is_active"=>$status]);
     
 } 
   
   public function ajax_taxcategory_list()
{
    $subTypes = [
        1 => [1 => "IGST", 2 => "CGST", 3 => "SGST", 4 => "UT GST", 5 => "Cess (GST)"],
        2 => [1 => "TDS IGST", 2 => "TDS CGST", 3 => "TDS SGST", 4 => "TDS UT GST"],
        3 => [1 => "TCS IGST", 2 => "TCS CGST", 3 => "TCS SGST", 4 => "TCS UT GST"],
        4 => [1 => "TDS"],
        5 => [1 => "TCS"],
        99 => [0 => "Other Taxes"]
    ];

    $comp_id       = $this->session->get('ses_company_id');
    $request       = service('request');
    $GSTTaxCategory = GSTTaxCategory();

    // Handle filters
    if ($request->getPost("pq_filter")) {
        $filter_data = json_decode($request->getPost("pq_filter"), true);
        $pq_filters  = $filter_data['data'][0] ?? [];
        $search_text = strtolower($pq_filters['value'] ?? '');
        $dataIndx    = $pq_filters['dataIndx'] ?? '';
    } else {
        $search_text = '';
        $dataIndx    = '';
    }

    // Pagination
    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;
    $offset = ($pq_rPP * ($pq_curPage - 1));

    // --- Step 1: Count & paginate on taxcatmstn ONLY (no join) ---
    $masterBuilder = $this->db->table("taxcatmstn t")
        ->where("t.cmp_id", $comp_id);

    if (!empty($search_text) && !empty($dataIndx)) {
        $masterBuilder->like("LOWER(t.$dataIndx)", $search_text);
    }

    // Total count of categories
    $countBuilder  = clone $masterBuilder;
    $totalRecords  = $countBuilder->countAllResults(false);

    if ($offset > $totalRecords) {
        $pq_curPage = ceil($totalRecords / $pq_rPP);
        $offset = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($offset < 0) $offset = 0;

    // Get paginated category IDs
    $masterBuilder->select("t.tax_cat_mst_id, t.tax_cat_name, t.tax_cat_type, t.tax_cat_section, t.tax_cat_is_active")
        ->orderBy("t.tax_cat_name")
        ->limit($pq_rPP, $offset);

    $masterRows = $masterBuilder->get()->getResultArray();

    if (empty($masterRows)) {
        return json_encode([
            "totalRecords" => $totalRecords,
            "curPage"      => $pq_curPage,
            "data"         => []
        ]);
    }

    // --- Step 2: Fetch rates for these category IDs only ---
    $catIds = array_column($masterRows, 'tax_cat_mst_id');

    $rateRows = $this->db->table("taxcatrate")
        ->select("tax_cat_mst_id, tax_cat_sub_type, tax_cat_rate")
        ->where("cmp_id", $comp_id)
        ->whereIn("tax_cat_mst_id", $catIds)
        ->get()
        ->getResultArray();

    // Group rates by category ID
    $rateMap = [];
    foreach ($rateRows as $r) {
        $rateMap[$r['tax_cat_mst_id']][] = $r;
    }

    // --- Step 3: Build final records ---
    $records = [];
    foreach ($masterRows as $row) {
        $id   = $row['tax_cat_mst_id'];
        $type = (int)$row['tax_cat_type'];

        $category = [
            'tax_cat_id'      => $id,
            'category_name'   => $row['tax_cat_name'],
            'category_type'   => $GSTTaxCategory[$row['tax_cat_type']] ?? '',
            'section_name'    => $row['tax_cat_section'],
            'tc_status_vl'    => ($row['tax_cat_is_active'] == 1) ? 0 : 1,
            'tc_status'       => ($row['tax_cat_is_active'] == 1) ? 'ACTIVE' : 'INACTIVE',
            'alert_tc_status' => ($row['tax_cat_is_active'] == 1) ? 'INACTIVE' : 'ACTIVE',
            'rates'           => []
        ];

        if (isset($rateMap[$id])) {
            foreach ($rateMap[$id] as $rate) {
                $rateId   = (int)$rate['tax_cat_sub_type'];
                $rateName = $subTypes[$type][$rateId] ?? "Unknown";

                $category['rates'][] = [
                    'rate_name' => $rateName,
                    'rate'      => $rate['tax_cat_rate']
                ];
            }
        }

        $records[] = $category;
    }

    return json_encode([
        "totalRecords" => $totalRecords,
        "curPage"      => $pq_curPage,
        "data"         => $records
    ]);
} 
}