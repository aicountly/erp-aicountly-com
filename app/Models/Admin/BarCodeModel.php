<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Models\CommonModel;
use App\Libraries\UUIDtables; 

class BarCodeModel extends Model	{
  public function __construct() {
    parent::__construct();        
    $this->externaldb    = new externaldb();	
    $this->CommonModel   = new CommonModel();
    $this->db            =  $this->externaldb->get_company_db();
    $this->session       =  \Config\Services::session();
    $this->company_id    =  $this->session->get('ses_company_id');
    $this->comp_fy_id    =  $this->session->get('ses_comp_fy_id');
    $this->user_id       =  $this->session->get('uuid_aicountly');
    $this->enc_string    =  new enc_string();
  }

  function create_mst_base_id($id,$type)
  {
    $uuid  = $this->session->get('comp_uuid');
    $UUIDtables = new UUIDtables($uuid,$this->company_id,$this->session->get('ses_comp_fy_id'));
    $mst_base_id = $UUIDtables->get_mst_base_id($id,$type);
    return $mst_base_id;
  }

  function delete_comp_fy_mst_map($id,$type)
  {
    $uuid  = $this->session->get('comp_uuid');
    $UUIDtables = new UUIDtables($uuid,$this->company_id,$this->session->get('ses_comp_fy_id'));
    $mst_base_id = $UUIDtables->delete_comp_fy_mst_map_by_id($id,$type);
  }

  function add_barcode($data){  

    $item_id = $data['item_id'];
    $unit_id = $data['unit_id'] ?? 0;
    $batch_id = $data['batch_id'] ?? 0;
    $tracking_id = $data['tracking_id'] ?? 0;

    $barcodemst_tbl = $this->company_id.'_barcodemst_'.$this->comp_fy_id;

    $builder = $this->db->table($barcodemst_tbl);
    $builder->where('item_id', $item_id);
    $builder->where('unit_id', $unit_id);
    $builder->where('batch_id', $batch_id);
    $builder->where('tracking_id', $tracking_id);
    $exists = $builder->get()->getRowArray();
    if($exists){
      return 0;
    }

    $this->db->table($barcodemst_tbl)->insert($data);

    $barcode_id = $this->db->insertID();

      $mst_base_id = $this->create_mst_base_id($barcode_id,'barcodemst');
      $this->db->table($barcodemst_tbl)
          ->where('barcode_id',$barcode_id)
          ->update(['mst_base_id' => $mst_base_id]);

       return $barcode_id;

  }

  function update_barcode($data){  

    $item_id = $data['item_id'];
    $unit_id = $data['unit_id'] ?? 0;
    $batch_id = $data['batch_id'] ?? 0;
    $tracking_id = $data['tracking_id'] ?? 0;

    $barcodemst_tbl = $this->company_id.'_barcodemst_'.$this->comp_fy_id;

    $builder = $this->db->table($barcodemst_tbl);
    $builder->where('item_id', $item_id);
    $builder->where('unit_id', $unit_id);
    $builder->where('batch_id', $batch_id);
    $builder->where('tracking_id', $tracking_id);
    $builder->update($data);

  }

  function update_barcode2($data){  

    $barcodemst_tbl = $this->company_id.'_barcodemst_'.$this->comp_fy_id;

    $builder = $this->db->table($barcodemst_tbl);
    $builder->where('barcode_id', $data['barcode_id']);
    $builder->update($data);

  }

  function get_item_info($item_id)
  {
      $itemmaster_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
      $builder = $this->db->table($itemmaster_tbl);
      $builder->select('item_name');
      $builder->where('item_id',$item_id);  
      return $builder->get()->getRowArray();
  }

  function get_item_barcode($data)
  {
      $item_id = $data['item_id'];
      $unit_id = $data['unit_id'] ?? 0;
      $batch_id = $data['batch_id'] ?? 0;
      $tracking_id = $data['tracking_id'] ?? 0;

      $barcodemst_tbl = $this->company_id.'_barcodemst_'.$this->comp_fy_id;
      $itemmaster_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');

      $builder = $this->db->table($barcodemst_tbl);
      $builder->where('item_id', $item_id);
      $builder->where('unit_id', $unit_id);
      $builder->where('batch_id', $batch_id);
      $builder->where('tracking_id', $tracking_id);

      $result = $builder->get()->getRowArray();

      if($result){
          $builder = $this->db->table($itemmaster_tbl);
          $builder->select('item_name');
          $builder->where('item_id',$result['item_id']);  
          $result2 = $builder->get()->getRowArray();

          $result['item_name'] = $result2['item_name'] ?? '';
      }

      return $result;
  }

  function get_item_batches($data)
  {
      $itmbatchmt_tbl = $this->company_id.'_itmbatchmt_'.$this->comp_fy_id;
      $barcodemst_tbl = $this->company_id.'_barcodemst_'.$this->comp_fy_id;

      $builder = $this->db->table($itmbatchmt_tbl);
      $builder->select($itmbatchmt_tbl.'.batch_id, batch_no, batch_mfr, batch_expiry');
      $builder->join($barcodemst_tbl, $itmbatchmt_tbl.'.batch_id = '.$barcodemst_tbl.'.batch_id', 'left');
      $builder->select('barcode_id, barcode, barcode_standard');
      $builder->where($itmbatchmt_tbl.'.batch_no !=', 'UNDEFINED');
      $builder->where($itmbatchmt_tbl.'.item_id', $data['item_id']);
      // $builder->where($itmbatchmt_tbl.'.batch_unit', $data['unit_id']);
      $result = $builder->get()->getResultArray();

      return $result;
  }

  function get_item_trackings($data)
  {
      $itmbatchmt_tbl = $this->company_id.'_itmbatchmt_'.$this->comp_fy_id;
      $barcodemst_tbl = $this->company_id.'_barcodemst_'.$this->comp_fy_id;
      $itemtrackn_tbl = $this->company_id.'_itemtrackn_'.$this->comp_fy_id;

      $builder = $this->db->table($itemtrackn_tbl);
      $builder->select($itemtrackn_tbl.'.tracking_id, tracking_no');
      $builder->select('"" as batch_id,"" as batch_no,"" as batch_mfr,"" as batch_expiry');
      $builder->join($barcodemst_tbl, $itemtrackn_tbl.'.tracking_id = '.$barcodemst_tbl.'.tracking_id', 'left');
      $builder->select('barcode_id, barcode, barcode_standard');
      // $builder->where($itmbatchmt_tbl.'.batch_no !=', 'UNDEFINED');
      $builder->where($itemtrackn_tbl.'.item_id', $data['item_id']);
      // $builder->where($itmbatchmt_tbl.'.batch_unit', $data['unit_id']);
      $result = $builder->get()->getResultArray();

      return $result;
  }

  function add_item_batch($data){    
    $itmbatchmt_tbl = $this->company_id.'_itmbatchmt_'.$this->comp_fy_id;

    $builder = $this->db->table($itmbatchmt_tbl);
    $builder->where('item_id', $data['item_id']);
    $builder->where('batch_no', $data['batch_no']);
    $exists = $builder->get()->getRowArray();
    if($exists){
      return 0;
    }

    $this->db->table($itmbatchmt_tbl)->insert($data);

    $batch_id = $this->db->insertID();

      $mst_base_id = $this->create_mst_base_id($batch_id,'itmbatchmt');
      $this->db->table($itmbatchmt_tbl)
          ->where('batch_id',$batch_id)
          ->update(['mst_base_id' => $mst_base_id]);

       return $batch_id;

  }
 
  function load_item_barcode($pq_curPage, $limit, $search, $data_type)
  {
      $itemmaster_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
      $barcodemst_tbl = $this->company_id.'_barcodemst_'.$this->comp_fy_id;

      $builder = $this->db->table($itemmaster_tbl);
      $builder->select($itemmaster_tbl.'.item_id, item_name, item_upc');
      $builder->join($barcodemst_tbl, $barcodemst_tbl.'.item_id = '.$itemmaster_tbl.'.item_id AND unit_id = 0 AND batch_id = 0 AND tracking_id = 0', 'left');
      $builder->select('barcode_id, barcode, barcode_standard');

      if($data_type == 'existing_barcodes')
        $builder->where('barcode_id  IS NOT NULL');
      if($data_type == 'unmapped_barcodes')
        $builder->where('barcode_id  IS NULL');

      $total_Records = $builder->countAllResults();

      if($pq_curPage==0){ $pq_curPage=1;}
      $offset = ($limit * ($pq_curPage - 1));

      if ($offset > $total_Records) {        
          $pq_curPage = ceil($total_Records / $limit);
          $offset = ($limit * ($pq_curPage - 1));
      }
            

      $builder = $this->db->table($itemmaster_tbl);
      $builder->select($itemmaster_tbl.'.item_id, item_name, item_upc');
      $builder->join($barcodemst_tbl, $barcodemst_tbl.'.item_id = '.$itemmaster_tbl.'.item_id AND unit_id = 0 AND batch_id = 0 AND tracking_id = 0', 'left');
      $builder->select('barcode_id, barcode, barcode_standard');

      if($data_type == 'existing_barcodes')
        $builder->where('barcode_id  IS NOT NULL');
      if($data_type == 'unmapped_barcodes')
        $builder->where('barcode_id  IS NULL');
      $builder->limit($limit,$offset);  
      $result = $builder->get()->getResultArray();

      foreach ($result as $key => $value) {
        
        $builder = $this->db->table($barcodemst_tbl);
        $builder->where('item_id', $value['item_id']);
        $result[$key]['no_of_barcodes'] = $builder->countAllResults();

      }

      return [
          'totalRecords' => $total_Records,
          'curPage'      => $pq_curPage,
          'data'         => $result,
      ];

  }
}