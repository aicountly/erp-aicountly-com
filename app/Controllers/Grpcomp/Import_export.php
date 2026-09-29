<?php
namespace App\Controllers\Admin;
use App\Models\Admin\ItemsModel;
use App\Models\Admin\AccountsModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class Import_export extends BaseController{
  function __construct(){  
	    helper(['form', 'url','text']);
	    $this->ItemsModel     = new ItemsModel();
	    $this->AccountsModel  = new AccountsModel();
		$this->auth_session    = new auth_session();
	    $this->auth_session->user_restrict();
	    $this->auth_session->is_company_opened();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->forge         = \Config\Database::forge();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->enc_string    = new enc_string();
		
    }
    
   public function index(){
        
        $final_item_array = [];
        $errors = [];
        $warnings = [];
        
        $final_account_array = [];
        if($this->request->getMethod() == 'post'){	
         
         $arr = explode(".", $_FILES["import_file"]["name"]);
         $extension = end($arr); 
         $allowed_extension = array("xls", "xlsx", "csv"); 
         if(in_array($extension, $allowed_extension)) 
         {
             $file = $_FILES["import_file"]["tmp_name"];
             $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
             $spreadsheet = $reader->load($file);
             $worksheet = $spreadsheet->getActiveSheet();  
             $worksheet_arr = $worksheet->toArray(); 
             
             if($_POST['type'] == 'items')
             {
                 $i = 1;
                 foreach($worksheet_arr as $key => $value)
                 {
                     if($key != 0)
                     {
                         if($value[0] == ''){
                             array_push($errors, 'Sr No '.$i.'- Item Name can not be empty');
                         }else if(strlen($value[0]) < 3){
                             array_push($errors, 'Sr No '.$i.'- Item Name must be of minimum 3 characters');
                         }
                         
                         if($value[1] != '' && strlen($value[1]) < 3){
                             array_push($errors, 'Sr No '.$i.'- Short Name must be of minimum 3 characters');
                         }
                         if($value[2] != '' && strlen($value[2]) < 3){
                             array_push($errors, 'Sr No '.$i.'- Alias Name must be of minimum 3 characters');
                         }
                         if($value[3] != '' && strlen($value[3]) < 3){
                             array_push($errors, 'Sr No '.$i.'- Print Name must be of minimum 3 characters');
                         }
                         
                         
                         if($value[4] != '' && strlen($value[4]) < 3){
                             array_push($errors, 'Sr No '.$i.'- Sales Account must be of minimum 3 characters');
                         }
                         if($value[4] != '' && strlen($value[4]) >= 3 && !$this->AccountsModel->check_account($value[4], 'Sales')){
                             array_push($warnings, 'Sr No '.$i.'- New Sales Account "'.$value[4].'" will be created');
                         }
                         
                         if($value[5] != '' && strlen($value[5]) < 3){
                             array_push($errors, 'Sr No '.$i.'- Purchase Account must be of minimum 3 characters');
                         }
                         if($value[5] != '' && strlen($value[5]) >= 3 && !$this->AccountsModel->check_account($value[5], 'Purchase')){
                             array_push($warnings, 'Sr No '.$i.'- New Purchase Account "'.$value[5].'" will be created');
                         }
                         
                         if($value[6] != '' && !$this->ItemsModel->check_item_group($value[6])){
                             array_push($warnings, 'Sr No '.$i.'- New Item Group "'.$value[6].'" will be created');
                         }
                         
                         array_push($final_item_array, [
                            'item_name'          => $value[0],  
                            'item_short'         => $value[1],  
                            'item_alias'         => $value[2],  
                            'item_print'         => $value[3],
                            'item_sales_acc'     => $value[4],
    						'item_pur_acc'       => $value[5],
    						'item_grp_id'        => $value[6],
    						'item_cat'           => $value[7],
    						'item_unit'          => $value[8],  
                            'op_bal_qty'         => $value[9],  
                            'op_bal_val'         => $value[10],
                            'op_val_basis'       => $value[11],
                        ]);
                        $i++;
                     }
                 }
             }
             if($_POST['type'] == 'accounts')
             {
                 
             }
             
         }
         else{
             echo "Extension not supported";exit;
         }
            
        }
       		
		$data['base_url']           = $this->base_url;	
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		$data['session']           = $this->session;
		$data['errors']             = $errors;
		$data['warnings']           = $warnings;
		$data['items']              = $final_item_array;
		$data['accounts']           = $final_account_array;
        return view($this->folder_path.'import_export/view',$data);  
   }

   public function import(){
        
        $final_item_array = [];
        $errors = [];
        $warnings = [];
        
        $final_account_array = [];
        
            
        $data['base_url']           = $this->base_url;  
        $data['message_output']     = $this->message_output;
        $data['folder_path']        = $this->folder_path;
        $data['base_url']           = $this->base_url;
        $data['session']            = $this->session;
        $data['errors']             = $errors;
        $data['warnings']           = $warnings;
        $data['items']              = $final_item_array;
        $data['accounts']           = $final_account_array;
        return view($this->folder_path.'import_export/import',$data);  
   }
   public function export(){
        
        $final_item_array = [];
        $errors = [];
        $warnings = [];
        
        $final_account_array = [];
        
            
        $data['base_url']           = $this->base_url;  
        $data['message_output']     = $this->message_output;
        $data['folder_path']        = $this->folder_path;
        $data['base_url']           = $this->base_url;
        $data['session']            = $this->session;
        $data['errors']             = $errors;
        $data['warnings']           = $warnings;
        $data['items']              = $final_item_array;
        $data['accounts']           = $final_account_array;
        return view($this->folder_path.'import_export/export',$data);  
   }
   public function validate_items()
   {
       echo json_encode(['status' => 200,'data' => $_POST['items']]);
   }
   public function save_items()
   {
      $data = json_decode($_POST['items']);
      $final_item_array = [];
    //   echo '<pre>';print_r($data);exit;
       foreach($data as $key => $value)
       {
            $value = (array)$value;
             $item_sales_acc     = $this->AccountsModel->get_or_create_account_id((string)$value['item_sales_acc'], 'Sales');
             $item_pur_acc       = $this->AccountsModel->get_or_create_account_id((string)$value['item_pur_acc'], 'Purchase');
             $item_group_id      = $this->ItemsModel->get_or_create_group_id((string)$value['item_grp_id']);
             $item_catg_id       = $this->ItemsModel->get_or_create_category_id((string)$value['item_cat']);
             $item_unit          = $this->ItemsModel->get_or_create_unit_id((string)$value['item_unit']);
             
             array_push($final_item_array, [
                 'comp_id'           => $this->session->get('ses_company_id'),
                'item_name'          => $value['item_name'],  
                'item_short'         => (($value['item_short'] != '') ? $value['item_short'] : $value['item_name']),  
                'item_alias'         => (($value['item_alias'] != '') ? $value['item_alias'] : $value['item_name']),  
                'item_print'         => (($value['item_print'] != '') ? $value['item_print'] : $value['item_name']),
                'item_sales_acc'     => $item_sales_acc,
				'item_pur_acc'       => $item_pur_acc,
				'item_grp_id'        => $item_group_id,
				'item_cat'           => $item_catg_id,
				'item_unit'          => $item_unit,  
                'op_bal_qty'         => ((is_int($value['op_bal_qty'])) ? $value['op_bal_qty'] : 0), 
                'op_bal_val'         => ((is_numeric($value['op_bal_val'])) ? $value['op_bal_val'] : 0),
                'op_val_basis'       => $value['op_val_basis'],
            ]);
       }
    //   echo '<pre>';print_r($final_item_array);exit;
       
       $this->ItemsModel->insert_items_batch($final_item_array);
       $this->session->setFlashdata('message', 'Data Inserted');
       return redirect()->to(base_url().'/admin/import_export');
       
   }
   
   public function ajax_save_items()
   {
      $data = json_decode($_POST['items']);
      $final_item_array = [];
    //   echo '<pre>';print_r($data);exit;
       foreach($data as $key => $value)
       {
            $value = (array)$value;
             $item_sales_acc     = $this->AccountsModel->get_or_create_account_id((string)$value['item_sales_acc'], 'Sales');
             $item_pur_acc       = $this->AccountsModel->get_or_create_account_id((string)$value['item_pur_acc'], 'Purchase');
             $item_group_id      = $this->ItemsModel->get_or_create_group_id((string)$value['item_grp_id']);
             $item_catg_id       = $this->ItemsModel->get_or_create_category_id((string)$value['item_cat']);
             $item_unit          = $this->ItemsModel->get_or_create_unit_id((string)$value['item_unit']);
             
             array_push($final_item_array, [
                 'comp_id'           => $this->session->get('ses_company_id'),
                'item_name'          => $value['item_name'],  
                'item_short'         => (($value['item_short'] != '') ? $value['item_short'] : $value['item_name']),  
                'item_alias'         => (($value['item_alias'] != '') ? $value['item_alias'] : $value['item_name']),  
                'item_print'         => (($value['item_print'] != '') ? $value['item_print'] : $value['item_name']),
                'item_sales_acc'     => $item_sales_acc,
				'item_pur_acc'       => $item_pur_acc,
				'item_grp_id'        => $item_group_id,
				'item_cat'           => $item_catg_id,
				'item_unit'          => $item_unit,  
                'op_bal_qty'         => ((is_int($value['op_bal_qty'])) ? $value['op_bal_qty'] : 0), 
                'op_bal_val'         => ((is_numeric($value['op_bal_val'])) ? $value['op_bal_val'] : 0),
                'op_val_basis'       => $value['op_val_basis'],
            ]);
       }
    //   echo '<pre>';print_r($final_item_array);exit;
       
       $this->ItemsModel->insert_items_batch($final_item_array);
       $this->session->setFlashdata('message', 'Data Inserted');
       return redirect()->to(base_url().'/admin/import_export');
       
   }
   
   public function item_sample()
   {
       $extension="excel";
       $spreadsheet = new Spreadsheet();
		$fileNames    = 'item_sample'; 
		
        $sheet = $spreadsheet->getActiveSheet();
		$spreadsheet->getActiveSheet()->setTitle('Items');

		
		
		$sheet->getStyle('A1:L1')->getAlignment()->setHorizontal('center'); 

		$sheet->getStyle('A1:L1')->getFont()->setName('Times New roman')->setBold( true );
		 
		$sheet->setCellValue('A1', 'Item Name');
		$sheet->setCellValue('B1', 'Short Name');
		$sheet->setCellValue('C1', 'Alias Name');
		$sheet->setCellValue('D1', 'Print Name');
		
		$sheet->setCellValue('E1', 'Sales Account');
		$sheet->setCellValue('F1', 'Purchase Account');
		
		$sheet->setCellValue('G1', 'Item Group');
		$sheet->setCellValue('H1', 'Item Category');
		
		$sheet->setCellValue('I1', 'Unit');
		$sheet->setCellValue('J1', 'Op Balance Qty');
		$sheet->setCellValue('K1', 'Op Balance Value');
		$sheet->setCellValue('L1', 'Op Value Basis');

		
		foreach(range('A','L') as $columnID) {
		  $sheet->getColumnDimension($columnID)->setAutoSize(true);
		 } 
        //dummy data 
// 		 for($i=2;$i<=1001;$i++)
// 		 {
// 		        $sheet->setCellValue('A'.$i, 'item'.$i-1); 
//         		$sheet->setCellValue('B'.$i, 'item'.$i-1);
//         		$sheet->setCellValue('C'.$i, 'item'.$i-1);
//         		$sheet->setCellValue('D'.$i, 'item'.$i-1);
        		
//         		$sheet->setCellValue('E'.$i, 'SalesAccount'.rand(1,2));
//         		$sheet->setCellValue('F'.$i, 'PurchaseAccount'.rand(1,2));
        		
//         		$sheet->setCellValue('G'.$i, 'ItemGroup'.rand(1,2));
//         		$sheet->setCellValue('H'.$i, 'ItemCategory'.rand(1,2));
        		
//         		$sheet->setCellValue('I'.$i, 'NA');
//         		$sheet->setCellValue('J'.$i, rand(1,10));
//         		$sheet->setCellValue('K'.$i, rand(1,10).'00');
//         		$sheet->setCellValue('L'.$i, 'something');
// 		 }
	  
	  $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
	  $fileName = $fileNames.'.xlsx';
	
        header('Content-Type: application/x-www-form-urlencoded');
        header('Content-Transfer-Encoding: Binary');
        header("Content-disposition: attachment; filename=\"".$fileName."\"");
        
         $writer->save('php://output');
         $spreadsheet->disconnectWorksheets();
	    unset($spreadsheet);
	    exit();
	
      
   }
   
  
    
}