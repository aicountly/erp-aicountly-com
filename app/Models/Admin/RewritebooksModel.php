<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\ERPtables;
use App\Libraries\UUIDtables;

class RewritebooksModel extends Model	{
	
 public function __construct() {
       parent::__construct();        
		$this->externaldb    = new externaldb();	
			
		$this->session       = \Config\Services::session();
		$this->bo_id         = $this->session->get('ses_boid');
		$this->fy_id         = $this->session->get('ses_comp_fy_id');
		$this->company_id    = $this->session->get('ses_company_id');
		$this->univaictly    =  $this->externaldb->univaictly_db();
    }

	public function get_account_page($offset, $limit)
	{
		return $this->db->table('acctmaster')
			->select('acc_id as id, acc_name as name')
			->where('cmp_id', $this->company_id)
			->orderBy('acc_id', 'ASC')
			->limit($limit, $offset)
			->get()
			->getResultArray();
	}

  public function get_account_count()
	{
		return $this->db->table('acctmaster')
			->where('cmp_id', $this->company_id)
			->countAllResults();
	}
	
   public function NextFyExists(){
    $fy_id = $this->session->get('ses_comp_fy_id');
    $count = $this->univaictly->table('cmpfymastr')
        ->where('is_imported', '1')
        ->where('cmp_id', $this->company_id)
        ->where('cmpfymastr_id >', $fy_id)
        ->countAllResults();
       return $count > 0;
     }
   
   function get_company_all_fy_list(){
	 return $this->univaictly->table("cmpfymastr")
		->where('cmp_id',$this->company_id)->orderBy('fy_beg_date')->get()->getResultArray();
	}
	
   public function get_account_details(){	     
    $builder = $this->db->table("acctmaster acct");
    $builder->join(
        "undercrsmt", 
        "undercrsmt.crs_mst_id = acct.acc_id 
         AND undercrsmt.crs_mst_type IN (1, 14) 
         AND undercrsmt.cmp_id = $this->company_id 
         AND undercrsmt.cmpfymastr_id = $this->fy_id", 
        'left'
    );
    $builder->select('acct.acc_id as id, acct.acc_name as name, undercrsmt.under_crs_mst_id as acc_grp_id, undercrsmt.crs_mst_parent_id as acc_grp_parent_id, acct.bsd_id');
    $builder->where("acct.cmp_id", $this->company_id);
    $builder->where("(undercrsmt.cmpfymastr_id = $this->fy_id)");
    $result = $builder->get()->getResultArray();
    return $result;
}
   
   
   public function get_item_count(): int{
    // Adjust table/filters to match your schema (e.g., active items only)
    $row = $this->db->query("
        SELECT COUNT(*) AS cnt
        FROM itemmaster i
        WHERE i.cmp_id = ? 
    ", [$this->company_id])->getRowArray();

    return (int)($row['cnt'] ?? 0);
   }
   public function get_item_page(int $offset, int $limit): array
{
    // Only the columns needed for processing + UI errors
    return $this->db->query("
        SELECT i.itm_id AS id, i.itm_name AS name
        FROM itemmaster i
        WHERE i.cmp_id = ?
        ORDER BY i.itm_id
        LIMIT ? OFFSET ?
    ", [$this->company_id, $limit, $offset])->getResultArray();
}
   public function get_item_details($item_id=""){
	  $builder = $this->db->table("itemmaster itmst");
	  $builder->select('itmst.itm_id as id, itmst.itm_name as named');
	  $builder->where("itmst.cmp_id",$this->company_id);
	  $result = $builder->get()->getResultArray();
	
      if($item_id!=''){	 
	  $builder->where("item_id",$item_id);
	  }
	  $result = $builder->get()->getResultArray();		
	  return $result;
   }

  
   function get_master_db_details()
   {        $default_db_name ='aicountlyin_erp0000001'; 
	        $current_db_info = $this->db->query('SELECT DATABASE() as db')->getRowArray();		
		    $current_db      = $current_db_info['db'];
			$list = [];
			$columns=array();	
			$default_comp_code ='erp0000001';
			$external_db    = $this->externaldb->single_company_db($default_comp_code);
			$tables         = $external_db->query('SHOW TABLES')->getResultArray();
			foreach ($tables as $key => $value) {
				$table_name = array_values($value)[0];
				if(str_contains($table_name, '_accnttxnnn_')  || str_contains($table_name, '_itemtxnnnn_') || str_contains($table_name, '_itemtxnval_')  || str_contains($table_name, '_sundrytxnn_') ) { 
				}else{
				  if(substr_count($table_name, '_') <= 2){
					 $builder =$external_db->table('INFORMATION_SCHEMA.COLUMNS');
					 $builder->select('COLUMN_NAME,ORDINAL_POSITION,DATA_TYPE,COLUMN_TYPE');
					 $builder->where('TABLE_SCHEMA', $default_db_name);
					 $builder->where('TABLE_NAME', $table_name);
					 $query = $builder->get();
					 $results = $query->getResultArray();
					 	
					 if($results){
						foreach($results as $row){
						//$columns[obfuscate_link($table_name)][] = array('colname'=>obfuscate_link($row['COLUMN_NAME']),'col_type'=>obfuscate_link($row['DATA_TYPE']))	;
					 	$columns[$table_name][] = array('colname'=>$row['COLUMN_NAME'],'col_type'=>$row['DATA_TYPE'],'col_position'=>$row['ORDINAL_POSITION']);
					 	
						} 
					 }
					 
					 $list[] = obfuscate_link($table_name);
				   }
				}
			}
		// echo "<pre>";print_r(array_values($list));exit;
		return array("list"=>$list,"columns"=>$columns,"dbenc"=>obfuscate_link($current_db));
   }
   
   
   
   function array_diff_recursive($array1, $array2) {
    $result = [];
    
    foreach ($array1 as $key => $value) {
        // If the key is not in the second array, or the value is different, add it to the result
        if (!array_key_exists($key, $array2)) {
            $result[$key] = $value;
        } else {
            // If both arrays have the same key, check if it's an array or value
            if (is_array($value) && is_array($array2[$key])) {
                // If both are arrays, recursively compare them
                $recursive_diff = $this->array_diff_recursive($value, $array2[$key]);
                if ($recursive_diff) {
                    $result[$key] = $recursive_diff;
                }
            } elseif ($value !== $array2[$key]) {
                // If the value is different, add it to the result
                $result[$key] = $value;
            }
        }
    }
    
    return $result;
  }
  private function compareArrays(array $defaultCols, array $otherCols): array {
    $in_default_not_other = [];
    $differences = [];

    // Loop over default table structure
    foreach ($defaultCols as $index => $defaultCol) {
        $defaultName = $defaultCol['COLUMN_NAME'];

        // Try to find matching column by name in otherCols
        $match = array_filter($otherCols, function ($col) use ($defaultName) {
            return $col['COLUMN_NAME'] === $defaultName;
        });

        if (empty($match)) {
            // Column not found in otherCols
            $in_default_not_other[$index] = $defaultCol;
        } else {
            $otherCol = reset($match);

            // Compare column properties
            $diffFields = [];
            foreach (['COLUMN_NAME', 'COLUMN_TYPE', 'IS_PRIMARY_KEY', 'IS_AUTO_INCREMENT'] as $key) {
                if ($defaultCol[$key] !== $otherCol[$key]) {
                    $diffFields[$key] = [$defaultCol[$key], $otherCol[$key]];
                }
            }

            if (!empty($diffFields)) {
                $differences[] = [
                    'column' => $defaultName,
                    'differences' => $diffFields,
                    'array1' => $defaultCol,
                    'array2' => $otherCol,
                ];
            }
        }
    }

    return [
        'in_array1_not_in_array2' => $in_default_not_other,
        'differences' => $differences
    ];
}
private function logQuery($query, $context = '') {
    $logText = "[" . date('Y-m-d H:i:s') . "] $context\n$query\n\n";
    file_put_contents(APPPATH . 'logs/query_log.txt', $logText, FILE_APPEND);
}
  
  function compareArraysoldee($array1, $array2) {
    $differences = [
        'in_array1_not_in_array2' => [],
       // 'in_array2_not_in_array1' => [],
        'differences' => []
    ];

    // Index arrays by COLUMN_NAME for easy comparison
    $array1Index = array_column($array1, null, 'COLUMN_NAME');
    $array2Index = array_column($array2, null, 'COLUMN_NAME');

    // Compare arrays
    foreach ($array1Index as $columnName => $columnData) {
        if (!isset($array2Index[$columnName])) {
            $differences['in_array1_not_in_array2'][$columnData['ORDINAL_POSITION']-1] = $columnData;
        } else {
            $array2Data = $array2Index[$columnName];
            // Check for differences in attributes
            if ($columnData['COLUMN_TYPE'] !== $array2Data['COLUMN_TYPE'] || 
                $columnData['IS_PRIMARY_KEY'] !== $array2Data['IS_PRIMARY_KEY'] || 
				$columnData['IS_AUTO_INCREMENT'] !== $array2Data['IS_AUTO_INCREMENT']) {
                $differences['differences'][] = [
                    'column' => $columnName,
                    'array1' => $columnData,
                    'array2' => $array2Data
                ];
            }
        }
    }

   /*  // Find columns in array2 but not in array1
    foreach ($array2Index as $columnName => $columnData) {
        if (!isset($array1Index[$columnName])) {
            $differences['in_array2_not_in_array1'][] = $columnData;
        }
    }
 */
    return $differences;
}

function verify_table_details($default_table_name, $current_db_name, $default_db_name) {
    $default_comp_code = 'erp0000001';
    $external_db = $this->externaldb->single_company_db($default_comp_code);

    $table_info = explode("_", $default_table_name);
    $actual_tblname = isset($table_info[2]) && !is_numeric($table_info[2])
        ? strtolower($table_info[1]) . '_' . strtolower($table_info[2])
        : strtolower($table_info[1]);

    // Get default company table structure
    $default_comp_tables = $this->get_table_structure($external_db, $default_table_name, $default_db_name);

    // Get current company tables matching structure
    $results = $this->get_table_structure($this->db, "%_$actual_tblname%", $current_db_name, true);

    $other_comp_tables = [];
    foreach ($results as $row) {
        $other_comp_tables[$row['TABLE_NAME']][] = [
            'COLUMN_NAME'       => $row['COLUMN_NAME'],
            'ORDINAL_POSITION'  => $row['ORDINAL_POSITION'],
            'COLUMN_TYPE'       => $row['COLUMN_TYPE'],
            'IS_PRIMARY_KEY'    => $row['IS_PRIMARY_KEY'],
            'IS_AUTO_INCREMENT' => $row['IS_AUTO_INCREMENT'],
        ];
    }

    $columns_mismatch = [];
    foreach ($other_comp_tables as $table_name => $cols) {
        $columns_mismatch[$table_name] = $this->compareArrays($default_comp_tables, $cols);
    }

    $status = 1;
    $errors = [];
    $queries_run = [];

    try {
        foreach ($columns_mismatch as $table => $column_diff) {
            // Add missing columns
            foreach ($column_diff['in_array1_not_in_array2'] ?? [] as $idx => $col) {
                if (!isset($other_comp_tables[$table][$idx])) {
                    $col_name = $default_comp_tables[$idx]['COLUMN_NAME'];
                    $col_type = strtoupper($default_comp_tables[$idx]['COLUMN_TYPE']);
                    $sql = "ALTER TABLE `$table` ADD COLUMN `$col_name` $col_type DEFAULT NULL;";
                    $this->db->query($sql);
                    SaveErrorLog("$table<br>$sql");
                    $queries_run[] = $sql;
                }
            }

            // Rename mismatched columns
            foreach ($column_diff['in_array1_not_in_array2'] ?? [] as $idx => $col) {
                if (isset($other_comp_tables[$table][$idx]) &&
                    $default_comp_tables[$idx]['COLUMN_NAME'] != $other_comp_tables[$table][$idx]['COLUMN_NAME']) {
                    $old_name = $other_comp_tables[$table][$idx]['COLUMN_NAME'];
                    $new_name = $default_comp_tables[$idx]['COLUMN_NAME'];
                    $col_type = strtoupper($default_comp_tables[$idx]['COLUMN_TYPE']);
                    $sql = "ALTER TABLE `$table` CHANGE `$old_name` `$new_name` $col_type DEFAULT NULL;";
                    $this->db->query($sql);
                    SaveErrorLog("$table<br>$sql");
                    $queries_run[] = $sql;
                }
            }

            // Handle primary key / auto-increment differences
            foreach ($column_diff['differences'] ?? [] as $diff) {
                $col_name = $diff['column'];
                $type = strtoupper($diff['array1']['COLUMN_TYPE']);

                if ($diff['array1']['IS_PRIMARY_KEY'] == 'PRIMARY KEY' && $diff['array2']['IS_PRIMARY_KEY'] == 'NO') {
                   try {
  				     $sql = "ALTER TABLE `$table` MODIFY COLUMN `$col_name` $type AUTO_INCREMENT PRIMARY KEY;";
                    $this->db->query($sql);
                    SaveErrorLog("$table<br>$sql");
                    $queries_run[] = $sql;
				   }catch (\Throwable $e) {
            SaveErrorLog("AUTO_INCREMENT error in `$table`: " . $e->getMessage());
        }
                }

                if ($diff['array1']['IS_AUTO_INCREMENT'] == 'YES' && $diff['array2']['IS_AUTO_INCREMENT'] == 'NO') {
                   try {
				    $sql = "ALTER TABLE `$table` MODIFY COLUMN `$col_name` $type AUTO_INCREMENT;";
                    $this->db->query($sql);
					$status = 1;
					$queries_run[] = $sql;
					  SaveErrorLog("$table<br>$sql");
				   }
				   catch (\Throwable $e) {
            SaveErrorLog("AUTO_INCREMENT error in `$table`: " . $e->getMessage());
        }
                   
                }

                if ($diff['array1']['IS_PRIMARY_KEY'] == 'NO' && $diff['array2']['IS_PRIMARY_KEY'] == 'PRIMARY KEY') {
					try {
				   $sql = "ALTER TABLE `$table` DROP PRIMARY KEY;";
                    $this->db->query($sql);
                    SaveErrorLog("$table<br>$sql");
                    $queries_run[] = $sql;
					}
				   catch (\Throwable $e) {
            SaveErrorLog("AUTO_INCREMENT error in `$table`: " . $e->getMessage());
        }
                }
            }
        }
    } catch (\Exception $e) {
        $errors[] = $e->getMessage();
        $status = 0;
    }

    return [
        'status' => $status,
        'message' => $errors,
        'queries' => $queries_run
    ];
}

private function get_table_structure($db, $table, $schema, $like = false) {
    $operator = $like ? 'LIKE' : '=';
    $sql = "SELECT c.TABLE_NAME, c.COLUMN_NAME, c.ORDINAL_POSITION, c.COLUMN_TYPE, 
                   IF(k.COLUMN_NAME IS NOT NULL, 'PRIMARY KEY', 'NO') AS IS_PRIMARY_KEY,
                   IF(c.EXTRA LIKE '%auto_increment%', 'YES', 'NO') AS IS_AUTO_INCREMENT
            FROM INFORMATION_SCHEMA.COLUMNS c
            LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
              ON c.TABLE_SCHEMA = k.TABLE_SCHEMA
             AND c.TABLE_NAME = k.TABLE_NAME
             AND c.COLUMN_NAME = k.COLUMN_NAME
             AND k.CONSTRAINT_NAME = 'PRIMARY'
            WHERE c.TABLE_SCHEMA = ?
              AND c.TABLE_NAME $operator ?
            ORDER BY c.ORDINAL_POSITION";
    return $db->query($sql, [$schema, $table])->getResultArray();
}

  function verify_table_details_oldee($default_table_name,$current_db_name,$default_db_name){
	 $default_comp_code ='erp0000001';
	 $external_db       = $this->externaldb->single_company_db($default_comp_code);
			
	 $table_name_info   = explode("_",$default_table_name);
	 if(isset($table_name_info[2]) && is_numeric($table_name_info[2])) 	
 	    $actual_tblname = strtolower($table_name_info[1]);
	 elseif(isset($table_name_info[2]) && !is_numeric($table_name_info[2])) 
	   $actual_tblname = strtolower($table_name_info[1]).'_'.strtolower($table_name_info[2]);	
	 else					
	    $actual_tblname = strtolower($table_name_info[1]);
	
	
	     $sql = "SELECT c.TABLE_NAME,c.COLUMN_NAME,c.ORDINAL_POSITION,c.COLUMN_TYPE, 
                IF(k.COLUMN_NAME IS NOT NULL, 'PRIMARY KEY', 'NO') AS IS_PRIMARY_KEY,
				IF(c.EXTRA LIKE '%auto_increment%', 'YES', 'NO') AS IS_AUTO_INCREMENT
			    FROM INFORMATION_SCHEMA.COLUMNS c
		  	    LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
				ON c.TABLE_SCHEMA = k.TABLE_SCHEMA
				AND c.TABLE_NAME = k.TABLE_NAME
				AND c.COLUMN_NAME = k.COLUMN_NAME
				AND k.CONSTRAINT_NAME = 'PRIMARY'
			    WHERE c.TABLE_SCHEMA = '$default_db_name'
			    AND c.TABLE_NAME ='".$default_table_name."'
				ORDER BY c.ORDINAL_POSITION;";
		$query = $external_db->query($sql);
		$default_comp_tables = $query->getResultArray();
		
		 
	    $sql = "SELECT c.TABLE_NAME,c.COLUMN_NAME,c.ORDINAL_POSITION,c.COLUMN_TYPE, 
                IF(k.COLUMN_NAME IS NOT NULL, 'PRIMARY KEY', 'NO') AS IS_PRIMARY_KEY,
				IF(c.EXTRA LIKE '%auto_increment%', 'YES', 'NO') AS IS_AUTO_INCREMENT
			    FROM INFORMATION_SCHEMA.COLUMNS c
		  	    LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
				ON c.TABLE_SCHEMA = k.TABLE_SCHEMA
				AND c.TABLE_NAME = k.TABLE_NAME
				AND c.COLUMN_NAME = k.COLUMN_NAME
				AND k.CONSTRAINT_NAME = 'PRIMARY'
			    WHERE c.TABLE_SCHEMA = '$current_db_name'
			    AND c.TABLE_NAME LIKE '%_".$actual_tblname."%'
				ORDER BY c.ORDINAL_POSITION;";
		$query   = $this->db->query($sql);
		$results = $query->getResultArray();
		$other_comp_tables=[];
		if($results){
			foreach($results as $rows){
				$other_comp_tables[$rows['TABLE_NAME']][]=array("COLUMN_NAME"=>$rows['COLUMN_NAME'],"ORDINAL_POSITION"=>$rows['ORDINAL_POSITION'],"COLUMN_TYPE"=>$rows['COLUMN_TYPE'],'IS_PRIMARY_KEY'=>$rows['IS_PRIMARY_KEY'],'IS_AUTO_INCREMENT'=>$rows['IS_AUTO_INCREMENT']);
			} 
		 }	
	
	
	
	$columns_in_table1_not_in_table2=array();
	if($other_comp_tables){
			foreach($other_comp_tables as $table_name => $table_cols){
			//echo '<pre>';
			//print_r($table_cols);
		
		  $columns_in_table1_not_in_table2[$table_name] = $this->compareArrays($default_comp_tables, $table_cols);
         
		  //$columns_in_table1_not_in_table2[$table_name] = $this->array_diff_recursive($default_comp_tables, $table_cols);
           //$columns_in_table2_not_in_table1[$default_table_name] = $this->array_diff_recursive($table_cols, $default_comp_tables);
		}
		
	}	
	 // echo '<pre>';
	//print_r($columns_in_table1_not_in_table2);
	
	
	//die(); 
	
	$errors=[];
	$status=1;	
	try{		
		$queryes=[];	
		if($columns_in_table1_not_in_table2){	
		    foreach($columns_in_table1_not_in_table2 as $tablename => $columns){
                if($tablename!='' && is_array($columns['in_array1_not_in_array2']) && count($columns['in_array1_not_in_array2']) >0){
				    
					foreach($columns['in_array1_not_in_array2'] as $position_id => $col_rows){	
					    		
					  if(isset($default_comp_tables[$position_id]['COLUMN_NAME']) && isset($default_comp_tables[$position_id]['ORDINAL_POSITION'])){				
							//$other_comp_db_colname = $default_comp_tables[$position_id]['COLUMN_NAME'];
					       // $other_comp_db_colpos  = $default_comp_tables[$position_id]['ORDINAL_POSITION'];
					 
					 
							if(!isset($other_comp_tables[$tablename][$position_id])){
							 $column_name = $default_comp_tables[$position_id]['COLUMN_NAME'];
							 $column_type = strtoupper($default_comp_tables[$position_id]['COLUMN_TYPE']);
							try{
							 // Add New Column at the last in current company db	
						 	$queryes ="ALTER TABLE `".$tablename."` ADD COLUMN `".$column_name."` ".$column_type." DEFAULT NULL;";
						   //echo '<br>';
							$this->db->query($queryes);     	    	 
							SaveErrorLog($tablename.'<br>'.$queryes);
							$status=1; 
							}catch (\Exception $e) {}							
							}
							else if($default_comp_tables[$position_id]['COLUMN_NAME']!=$other_comp_tables[$tablename][$position_id]['COLUMN_NAME']){
							$new_column_name = $default_comp_tables[$position_id]['COLUMN_NAME'];
							$old_column_name = $other_comp_tables[$tablename][$position_id]['COLUMN_NAME'];
							$column_type     = strtoupper($default_comp_tables[$position_id]['COLUMN_TYPE']);
							try{
							 $queryes="ALTER TABLE `".$tablename."` CHANGE `".$old_column_name."` `".$new_column_name."` ".$column_type." DEFAULT NULL;";
							//echo '<br>';
							SaveErrorLog($tablename.'<br>'.$queryes);
							$this->db->query($queryes);     	    	 
							$status=1;   
							}catch (\Exception $e) {}
					    }					   
				    }
			    } 
				
				
			   }
			   
			   if(isset($columns['differences']) && count($columns['differences']) >0){
						//check primary keys exists or not 
						
						foreach($columns['differences'] as $differencesid => $diff_rows){	
						   $column_name               = $diff_rows['column'];
						   $is_primary_set            = $diff_rows['array1']['IS_PRIMARY_KEY'];// PRIMARY KEY						    
						   $primary_exts_othertbl     = $diff_rows['array2']['IS_PRIMARY_KEY'];// PRIMARY KEY						   
						  
						   $is_autoincrmnt_set        = $diff_rows['array1']['IS_AUTO_INCREMENT'];// IS_AUTO_INCREMENT
						   $autoincrmnt_exts_othertbl = $diff_rows['array2']['IS_AUTO_INCREMENT'];// IS_AUTO_INCREMENT
						  
						   $column_type               = strtoupper($diff_rows['array1']['COLUMN_TYPE']);
						   if($primary_exts_othertbl=='NO' && $is_primary_set=='PRIMARY KEY'){							 
							   try{
								$queryes ="ALTER TABLE `".$tablename."` MODIFY COLUMN `".$column_name."` ".$column_type." AUTO_INCREMENT PRIMARY KEY;";   
								$this->db->query($queryes);
								SaveErrorLog($tablename.'<br>'.$queryes);
							    $status=1;    
							   }
							   catch (\Exception $e) {
								   SaveErrorLog($tablename.'<br>'.$e->getMessage());
							   }
						   }
						   if($autoincrmnt_exts_othertbl=='NO' && $is_autoincrmnt_set=='YES'){
							 
							   try{
								$queryes ="ALTER TABLE `".$tablename."` MODIFY COLUMN `".$column_name."` ".$column_type." AUTO_INCREMENT;";   
								$this->db->query($queryes);
								SaveErrorLog($tablename.'<br>'.$queryes);
							    $status=1;    
							   }
							   catch (\Exception $e) {
								   
								 SaveErrorLog($tablename.'<br>'.$e->getMessage());  
							   }
						   }
						   if($primary_exts_othertbl=='PRIMARY KEY' && $is_primary_set=='NO'){
							 
							   try{
								$queryes ="ALTER TABLE `".$tablename."` DROP PRIMARY KEY;"; 
								 $this->db->query($queryes);								
								SaveErrorLog($tablename.'<br>'.$queryes);
							    $status=1;    
							   }
							   catch (\Exception $e) {
								   SaveErrorLog($e->getMessage());
								   
							   }
						   }
						}
					}
			   
			   
			   
			   
			   
		  }
		}	
	
	}
	catch (\Exception $e) {			
			$errors[] = $e->getMessage();
		    $status=0;
	} 
	
   $response_result = array('status'=>$status,'message'=>$errors,'queryes'=>$queryes);	
   return $response_result;	
  }
 
  function verify_table_details_without_primarykey($default_table_name,$current_db_name,$default_db_name){
	 $default_comp_code ='erp0000001';
	 $external_db       = $this->externaldb->single_company_db($default_comp_code);
			
	 $table_name_info   = explode("_",$default_table_name);
	 if(isset($table_name_info[2]) && is_numeric($table_name_info[2])) 	
 	    $actual_tblname = strtolower($table_name_info[1]);
	 elseif(isset($table_name_info[2]) && !is_numeric($table_name_info[2])) 
	   $actual_tblname = strtolower($table_name_info[1]).'_'.strtolower($table_name_info[2]);	
	 else					
	    $actual_tblname = strtolower($table_name_info[1]);
	    $sql = "SELECT COLUMN_NAME,ORDINAL_POSITION,COLUMN_TYPE
						 FROM INFORMATION_SCHEMA.COLUMNS
						 WHERE TABLE_SCHEMA = '$default_db_name' AND TABLE_NAME LIKE '%_".$actual_tblname."%'";
		$query = $external_db->query($sql);
		$default_comp_tables = $query->getResultArray();
		
	    $sql = "SELECT TABLE_NAME,COLUMN_NAME,ORDINAL_POSITION,COLUMN_TYPE
						 FROM INFORMATION_SCHEMA.COLUMNS
						 WHERE TABLE_SCHEMA = '$current_db_name' AND TABLE_NAME LIKE '%_".$actual_tblname."%'";
		$query   = $this->db->query($sql);
		$results = $query->getResultArray();
		$other_comp_tables=[];
		if($results){
			foreach($results as $rows){
				$other_comp_tables[$rows['TABLE_NAME']][]=array("COLUMN_NAME"=>$rows['COLUMN_NAME'],"ORDINAL_POSITION"=>$rows['ORDINAL_POSITION'],"COLUMN_TYPE"=>$rows['COLUMN_TYPE']);
			} 
		 }	
	
	
	if($other_comp_tables){
		foreach($other_comp_tables as $table_name => $table_cols){
		   $columns_in_table1_not_in_table2[$table_name] = $this->array_diff_recursive($default_comp_tables, $table_cols);
           //$columns_in_table2_not_in_table1[$default_table_name] = $this->array_diff_recursive($table_cols, $default_comp_tables);
		}
		
	}
	$errors=[];
	$status=1;	
	try{		
		$queryes=[];	
		if($columns_in_table1_not_in_table2){	
		    foreach($columns_in_table1_not_in_table2 as $tablename => $columns){
                if($tablename!='' && is_array($columns) && count($columns) >0){
				    foreach($columns as $position_id => $col_rows){	
					    		
					  if(isset($default_comp_tables[$position_id]['COLUMN_NAME']) && isset($default_comp_tables[$position_id]['ORDINAL_POSITION'])){				
							$other_comp_db_colname = $default_comp_tables[$position_id]['COLUMN_NAME'];
					        $other_comp_db_colpos  = $default_comp_tables[$position_id]['ORDINAL_POSITION'];
					  
							if(!isset($other_comp_tables[$tablename][$position_id])){
							 $column_name = $default_comp_tables[$position_id]['COLUMN_NAME'];
							 $column_type = strtoupper($default_comp_tables[$position_id]['COLUMN_TYPE']);
							try{
							 // Add New Column at the last in current company db	
							$queryes ="ALTER TABLE `".$tablename."` ADD COLUMN `".$column_name."` ".$column_type." DEFAULT NULL;";
							$this->db->query("ALTER TABLE `".$tablename."` ADD COLUMN `".$column_name."` ".$column_type." DEFAULT NULL;");     	    	 
							SaveErrorLog($queryes);
							$status=1; 
							}catch (\Exception $e) {}							
							}
							else if($default_comp_tables[$position_id]['COLUMN_NAME']!=$other_comp_tables[$tablename][$position_id]['COLUMN_NAME']){
							$new_column_name = $default_comp_tables[$position_id]['COLUMN_NAME'];
							$old_column_name = $other_comp_tables[$tablename][$position_id]['COLUMN_NAME'];
							$column_type     = strtoupper($default_comp_tables[$position_id]['COLUMN_TYPE']);
							try{
							$queryes="ALTER TABLE `".$tablename."` CHANGE `".$old_column_name."` `".$new_column_name."` ".$column_type." DEFAULT NULL;";
							SaveErrorLog($queryes);
							$this->db->query("ALTER TABLE `".$tablename."` CHANGE `".$old_column_name."` `".$new_column_name."` ".$column_type." DEFAULT NULL;");     	    	 
							$status=1;   
							}catch (\Exception $e) {}
					    }					   
				    }
			    } 
			}
		  }
		}	
	
	}
	catch (\Exception $e) {			
			$errors[] = $e->getMessage();
		    $status=0;
	}  
	
   $response_result = array('status'=>$status,'message'=>$errors,'queryes'=>$queryes);	
   return $response_result;	
  }
   
   
   
   function verify_table_details11($default_table_name,$current_db_name,$default_db_name,$columns_info){
	  $table_name_info  = explode("_",$default_table_name);
	  $response_array   = array();
	  if(isset($table_name_info[0]) && isset($table_name_info[2])){
		  $actual_tblname = strtolower($table_name_info[1]);
		  
		$list = [];
		$create_tables=[];
		$tables_diff=[];
		$db_tables_list=[];
	    $tables = $this->db->query("SHOW TABLES LIKE '%%'")->getResultArray();
	    foreach ($tables as $key => $value) {
				$table_name = array_values($value)[0];
				if(str_contains($table_name, '_accnttxnnn_') || str_contains($table_name, '_acctmaster_') || str_contains($table_name, '_itemtxnnnn_') || str_contains($table_name, '_itemtxnval_') || str_contains($table_name, '_itemmaster_') || str_contains($table_name, '_sundrytxnn_') || str_contains($table_name, '_billsundry_')) { 
				}else{
				  if(substr_count($table_name, '_') <= 2){
					  $db_table_name_info = explode("_",$table_name);
                      if(isset($db_table_name_info[2]) && is_numeric($db_table_name_info[2])) 	
 					   $db_tblname = strtolower($db_table_name_info[1]);
					  elseif(isset($db_table_name_info[2]) && !is_numeric($db_table_name_info[2])) 
						 $db_tblname = strtolower($db_table_name_info[1]).'_'.strtolower($db_table_name_info[2]);	
						else					
					    $db_tblname = strtolower($db_table_name_info[1]);
					
					
					
					  $db_tables_list[$table_name]=$db_tblname;					  
				   }
				}
			}			
		
		if(!in_array(strtolower($actual_tblname),$db_tables_list)){
			$create_tables[]=$default_table_name;
		}
		if(in_array(strtolower($actual_tblname),$db_tables_list)){
            $other_table = array_search ($actual_tblname, $db_tables_list);					
			 $sQuery = "SELECT column_name,ordinal_position,data_type,column_type FROM
						(   SELECT column_name,ordinal_position,data_type,column_type,COUNT(1) rowcount
							FROM information_schema.columns
							WHERE
							(
								(table_schema='$default_db_name' AND table_name='$default_table_name') OR
								(table_schema='$current_db_name' AND table_name='$other_table')
							)
							AND table_name IN ('$default_table_name','$other_table')
							GROUP BY column_name,ordinal_position,data_type,column_type HAVING COUNT(1)=1
						) A; ";
								
		    $response = $this->db->query($sQuery);
			$res_array = $response->getResultArray();
			echo '<pre>';
			print_r($res_array);
			echo $this->db->getlastquery();
			/* $columnsarr = array();		
			if($response){				
				foreach($response as $rows){					
					$columnsarr = array("column_name"=>$rows['column_name'],"data_type"=>$rows['data_type']);
				    $tables_diff[$default_table_name][]=$columnsarr;
				  }
			   }
			else
			 $tables_diff[$default_table_name][]=''; */	
		 // $response_array = array('tablename'=>$default_table_name,"tables_diff"=>json_encode($response),"create_tables"=>$create_tables);			 
	  
		}	
		
	  }else
		  $response_array = array('tablename'=>$default_table_name,"tables_diff"=>[],"create_tables"=>[]);			 
	  
	  
	  
	  return $response_array;
	   
   }

   


   public function get_bbb_groups()
   {
        
		$groups = [16,22];
		$array = $this->get_sub_group_ids($groups);
        
      return $array;
   }

   
  
}