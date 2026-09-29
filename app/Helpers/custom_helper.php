<?php 
function inputmask_gstin($string){
$response = preg_grep('/^[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z0-9]{4}$/', explode("\n", $string));	
if(!empty($response))
	return true;
 else
	return false;	
}


if (!function_exists('encrypt_data_fnc')) {
    function encrypt_data_fnc($plaintext)
    {
        $key = '029315c18ea31a03f11acc6480171018cdf46d4fa6f2e32fe26e44f21ba9072f'; // 🔴 CHANGE THIS
        $cipher = 'AES-256-CBC';

        $iv = random_bytes(openssl_cipher_iv_length($cipher));

			$encrypted = openssl_encrypt(
				$plaintext,
				$cipher,
				$key,
				OPENSSL_RAW_DATA,
				$iv
			);

			// Combine IV + encrypted
			$final = $iv . $encrypted;

			// URL-safe encode
			return rtrim(strtr(base64_encode($final), '+/', '-_'), '=');
			}
}

if (!function_exists('decrypt_data_fnc')) {
    function decrypt_data_fnc($encrypted)
    {
        $key = '029315c18ea31a03f11acc6480171018cdf46d4fa6f2e32fe26e44f21ba9072f'; // 🔴 SAME KEY
        $cipher = 'AES-256-CBC';
 $data = strtr($encrypted, '-_', '+/');
    $data .= str_repeat('=', strlen($data) % 4);

    $decoded = base64_decode($data);

    $iv_length = openssl_cipher_iv_length($cipher);

    $iv = substr($decoded, 0, $iv_length);
    $ciphertext = substr($decoded, $iv_length);

    return openssl_decrypt(
        $ciphertext,
        $cipher,
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );
    }
}
	

if (! function_exists('get_composition_rates')) {
    /**
     * Return composition tax rates based on supply type.
     *
     * @param string|int|null $cmp_supply_type Values: 1=MANUFACTURER, 2=TRADER, 3=RESTAURANT SERVICES, 4=OTHER SERVICES
     * @return array{igst:float,cgst:float,sgst:float,ut:float,cess:float}
     */
    function get_composition_rates($cmp_supply_type): array
    {
        $type = trim((string) $cmp_supply_type);

        switch ($type) {
            case '3': // Restaurant Services
                return ['igst' => 5.0, 'cgst' => 2.5, 'sgst' => 2.5, 'ut' => 2.5, 'cess' => 0.0];
            case '4': // Other Services
                return ['igst' => 6.0, 'cgst' => 3.0, 'sgst' => 3.0, 'ut' => 3.0, 'cess' => 0.0];
            case '1': // Manufacturer
            case '2': // Trader
            default:  // Goods default
                return ['igst' => 1.0, 'cgst' => 0.5, 'sgst' => 0.5, 'ut' => 0.5, 'cess' => 0.0];
        }
    }
}

function GetSupplyTypeCode($supply_type){
 $type_val='B2B';
 if($supply_type==1)
 	 $type_val='B2B';
 else if($supply_type==2)
 	 $type_val='EXPWP';
 else if($supply_type==3)
 	 $type_val='EXPWOP';
 else if($supply_type==4)
 	 $type_val='SEZWP';
 else if($supply_type==5)
 	 $type_val='SEZWOP';
 else if($supply_type==6)
 	 $type_val='B2B';
 else if($supply_type==7)
 	 $type_val='B2B';
 else if($supply_type==8)
 	 $type_val='B2B';
 else if($supply_type==9)
 	 $type_val='B2B';
 else if($supply_type==10)
 	 $type_val='B2B';
 else if($supply_type==11)
 	 $type_val='B2B';
 else if($supply_type==12)
 	 $type_val='B2B';
 else if($supply_type==13)
 	 $type_val='DE';
 else if($supply_type==14)
 	 $type_val='NILSPLY';
 else if($supply_type==15)
 	 $type_val='EXMSPLY';
 else if($supply_type==16)
 	 $type_val='NONGSTS';
 return $type_val;
}

function validate_web_url($url){
	    $session  = \Config\Services::session();
	    $model    = new \App\Models\Admin\SettingsModel();		
	    $response = $model->ajax_check_profile_rights($url);	 		
		 return $response;
	}
	function user_company_list()
{
    $list = [];
    if(auth()->id){
        $model = new \App\Models\CommonModel();
        $list = $model->ajax_all_company_list();
    }
    return $list;
}
function urlSafeBase64Decode($data) {
    $data = strtr($data, '-_', '+/');
    $pad = strlen($data) % 4;
    if ($pad) {
        $data .= str_repeat('=', 4 - $pad);
    }
    return base64_decode($data);
}

function CreateJsonFile($fy_id, $company_id, $name, $jsonString)
{
    try {
        // If someone passes an array/object by mistake, encode it
        if (!is_string($jsonString)) {
            $jsonString = json_encode($jsonString, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if ($jsonString === false) {
                throw new \RuntimeException('json_encode failed: ' . json_last_error_msg());
            }
        }

        $file_name = $name . $fy_id . '.json';
        $dir_path  = WRITEPATH . 'comp' . $company_id . '/';
        $file      = $dir_path . $file_name;
        $tmp       = $file . '.tmp';

        // Ensure directory exists
        if (!is_dir($dir_path)) {
            if (!mkdir($dir_path, 0755, true) && !is_dir($dir_path)) {
                throw new \RuntimeException('Failed to create directory: ' . $dir_path);
            }
        }

        // Write to temp with lock
        if (file_put_contents($tmp, $jsonString, LOCK_EX) === false) {
            @unlink($tmp);
            throw new \RuntimeException('file_put_contents failed for: ' . $tmp);
        }

        // Atomic rename
        if (!@rename($tmp, $file)) {
            @unlink($tmp);
            throw new \RuntimeException('rename failed for: ' . $file);
        }

        // Verify non-empty
        if (!filesize($file)) {
            throw new \RuntimeException('Written file is empty: ' . $file);
        }

        return true;
    } catch (\Throwable $e) {
        log_message('error', $name . ' JSON failed to write: ' . $e->getMessage());
        return false;
    }
}

function CreateJsonFilesss($fy_id, $company_id, $name, $file_data)
{
    try {
        // Build directory + filename
        $file_name = $name . $fy_id . '.json';
        $dir_path  = WRITEPATH . 'comp' . $company_id . '/';
        $file      = $dir_path . $file_name;

        // Ensure directory exists
        if (!is_dir($dir_path)) {
            if (!mkdir($dir_path, 0755, true) && !is_dir($dir_path)) {
                // mkdir failed
                throw new \RuntimeException('Failed to create directory: ' . $dir_path);
            }
        }

        // Write the data in a single atomic operation
        // LOCK_EX helps avoid concurrent write issues
        $bytes = file_put_contents($file, $file_data, LOCK_EX);
        if ($bytes === false) {
            throw new \RuntimeException('file_put_contents failed for: ' . $file);
        }

        // Optionally you can return true on success
        return true;

    } catch (\Throwable $e) {
        log_message('error', $name . ' Json failed to write data: ' . $e->getMessage());
        // Optionally return false on failure
        return false;
    }
}

function GetJsonFileContent($fy_id, $company_id, $name)
{
    $file_name = $name . $fy_id . '.json';
    $file_path = WRITEPATH . 'comp' . $company_id . '/' . $file_name;
    if (!file_exists($file_path)) {
        return json_encode([]); 
    }
    $data_list = file_get_contents($file_path);
    return $data_list ? $data_list : json_encode([]); 
}

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        // Check for IP from shared internet
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Check for IP passed from proxy
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        // Fallback to remote address
        return $_SERVER['REMOTE_ADDR'];
    }
}

function getMonthsBetweenDates($startDate, $endDate) {
    // Create DateTime objects for start and end dates
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);

    // Create an interval to increment by one month
    $interval = DateInterval::createFromDateString('1 month');

    // Create an array to hold the months
    $months = [];

    // Loop through each month until the end date
    while ($start <= $end) {
        // Format the current date to "YYYY-MM"
        $months[] = $start->format('Y-m-d');

        // Add 1 month to the current date
        $start->add($interval);
    }

    return $months;
}

function CheckEmptyFilters($array){
	$error=0;
	if($array){
		foreach($array as $cols){
			foreach($cols as $ckey =>$colrow){
		
		 if(trim($colrow)=='' || $colrow==NULL)
             $error++;
			}
		}
	}
	if($error==0)
		return "1";
	else
		return "0";
}
function valueExistsInMultiArray($array, $value) {
	$subArray_ar= array();
    foreach ($array as $subArray) {
        if (in_array($value, $subArray)) {
            $subArray_ar[]=$subArray;  // If value exists, return true
        }
    }
    return $subArray_ar;  // Return false if the value does not exist
}

function applyCondition($row, $condition) {
    switch ($condition['operator']) {
        case 'equal':
            return $row[$condition['column']] == $condition['value'];
        case 'great':
            return $row[$condition['column']] > $condition['value'];
		case 'notequal':
            return $row[$condition['column']] != $condition['value'];
        case 'less':
            return $row[$condition['column']] < $condition['value'];
        case 'contain':
            return strpos(strtolower($row[$condition['column']]), strtolower($condition['value'])) !== false;
        default:
            return false;
    }
}
 function GST_Sites_Types(){
 $cred_site_dropdown= array(''=>'Choose','1'=>'Gst.gov.in','2'=>'Incometax.gov.in','3'=>'Tdscpc.gov.in','4'=>'ewaybillgst.gov.in( GST Eway API)','5'=>'einvoice1.gst.gov.in(GST E-Invoice API)');	 
 return $cred_site_dropdown;
 }

 function get_b2c_invoice_type($invoice_date,$invoice_value){
    $check_date = '2024-08-01'; 
    if($invoice_date<=$check_date && $invoice_value>250000)
       $invoice_type='B2CL';
    if($invoice_date<=$check_date && $invoice_value<=250000)
       $invoice_type='B2CS';
   
    if($invoice_date>=$check_date && $invoice_value>100000)
       $invoice_type='B2CL';
    if($invoice_date>=$check_date && $invoice_value<=100000)
       $invoice_type='B2CS';
   
	return $invoice_type;
 }
												
 function InvoiceTypeList(){
    $typearray = array();
	$typearray['']='';
	/* $typearray['R']='REGULAR B2B INVOICE';
	$typearray['DE']='DEEMED EXPORTS (DE) ';
	$typearray['SEWP']='SEZ WITH PAYMENT (SEZWP)';
	$typearray['SEWOP']='SEZ WITHOUT PAYMENT (SEZWOP)';
	$typearray['CBW']='CUSTOM BONDED WAREHOUSE (CBW)'; 
	$typearray[0]='OTHERS';
	*/
	$typearray['B2B']='B2B REGULAR';
	$typearray['B2BRCM']='B2B REVERSE CHARGE';
	$typearray['B2CS']='B2CS';
	$typearray['B2CL']='B2CL (LARGE)';
	$typearray['EXPWP']='EXPORTS WITH PAYMENT';
	$typearray['EXPWOP']='EXPORTS WITHOUT PAYMENT';
	$typearray['DE']='DEEMED EXPORT';
	$typearray['SEZWP']='SEZ WITH PAYMENT';
	$typearray['SEZWOP']='SEZ WITHOUT PAYMENT';
	$typearray['CBW']='CUSTOM BONDED WAREHOUSE (CBW)';	
	return $typearray;
 }	

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824)
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    elseif ($bytes >= 1048576)
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    elseif ($bytes >= 1024)
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    elseif ($bytes > 1)
        $bytes = $bytes . ' bytes';
    elseif ($bytes == 1)
        $bytes = $bytes . ' byte';
    else
        $bytes = '0 bytes';
    
    return $bytes;
}
    
 function _format_line($level, $date, $message)
	{
		return $level.' - '.$date.' --> '.$message.PHP_EOL;
	}
	
 function SupplyTypesList(){    
    return ["1"=>"SUPPLY (REGULAR)","2"=>"EXPORT – WITH PAYMENT","3"=>"EXPORT – WITHOUT PAYMENT","4"=>"SEZ – WITH PAYMENT ","5"=>"SEZ – WITHOUT PAYMENT ",
		                        "6"=>"JOB WORK ","7"=>"SKD / CKD / LOTS","8"=>"RECEIPENT NOT KNOWN","9"=>"FOR OWN USE", "10"=>"EXHIBITION OR FAIRS",
		                        "11"=>"LINE SALES","12"=>"OTHERS","13"=>"DEEMED EXPORT","14"=>"NILL RATED","15"=>"EXEMPTED","16"=>"NON GST SUPPLIES"];
 }

 function gstintypes_list(){
     return [""=>"Choose","1"=>"Regular","2"=>"Composition","3"=>"Tax Collector(E-Commerce)","4"=>"Tax Deductor","5"=>"Non Resident Taxable Person","6"=>"Non Resident Online Service Provider"];
}

 function transport_modes(){
     return ["1"=>"Road","2"=>"Rail","3"=>"Air","4"=>"Ship","5"=>"InTransit"];
}

function mime($file_ext)
{
    $mime = '';
    switch($file_ext) {
        case "gif": $mime="image/gif"; break;
        case "png": $mime="image/png"; break;
        case "jpeg": $mime="image/jpeg"; break;
        case "jpg": $mime="image/jpeg"; break;
        case "svg": $mime="image/svg+xml"; break;
        default: $mime="image/".$file_ext; break;
    }
    return $mime;
}

function short_str($str, $len = 12)
{
  return strlen($str) > $len ? substr($str,0,($len-3))."..." : $str;
}

function convert_jpg($source,$destination,$quality = 70) 
{
    $info = getimagesize($source);
  
    if ($info['mime'] == 'image/jpeg') 
        $image = imagecreatefromjpeg($source);

    if ($info['mime'] == 'image/png') 
        $image = imagecreatefrompng($source);

    if ($info['mime'] == 'image/webp') 
        $image = imagecreatefromwebp($source);

    if(!empty($image)){
        unlink($source);
        imagejpeg($image,$destination,$quality);
        imagedestroy($image);
        return true;  
    }
    return false;

}

function sticky_notes()
{
    $colors = ['#DBFFD2', '#DBF3FB', '#FFF8D7', '#FBE2D2', '#FFEAED'];
    $list = [];

    for ($i = 1; $i <= 20; $i++) {
        $list[] = [
            'primary' => $i,
            'heading' => 'Note ' . $i,
            'details' => '',
            'color'   => $colors[($i - 1) % count($colors)],
        ];
    }

    return $list;
}


function logo_src($comp_id)
{

  $model = new \App\Models\Admin\CompanyModel(); 
  $file = $model->getCompLogo($comp_id);

  $src ='';// 'images/logo.png';
  if($file){
    $file_name = $file['name'];
    $file_ext = $file['ext'];

    $bucketName = 'usersetup.aicountly.com.active';
    $s3Key = 'uploads/'.$file_name;

    $aws = new \App\Helpers\AWSHelper('https://s3.de.perf.cloud.ovh.net/');

    $response = $aws->download($bucketName, $s3Key);
    if($response){
      $mime = mime($file_ext);

      $src = 'data:image/' . $mime . ';base64,' . base64_encode($response["Body"]);
      
    }
  }
  return $src;
}

function Eway_errors_codes($error_code){
	$show_error='';
	$errorts_codes=json_decode('[{"errorCode":"437","errorDesc":"Invalid Supplier ship from State Code for the given pincode"},{"errorCode":"702","errorDesc":"The distance between the pincodes given is too high"},{"errorCode":"100","errorDesc":"Invalid Json"},{"errorCode":"101","errorDesc":"Invalid Username"},{"errorCode":"102","errorDesc":"Invalid Password"},{"errorCode":"103","errorDesc":"Invalid Client -Id"},{"errorCode":"104","errorDesc":"Invalid Client Secret"},{"errorCode":"105","errorDesc":"Invalid Token"},{"errorCode":"106","errorDesc":"Token Expired"},{"errorCode":"107","errorDesc":"Authentication failed. Pls. inform the helpdesk"},{"errorCode":"108","errorDesc":"Invalid login credentials."},{"errorCode":"109","errorDesc":"Decryption of data failed"},{"errorCode":"110","errorDesc":"Invalid Client-ID/Client-Secret"},{"errorCode":"111","errorDesc":"GSTIN is not registerd to this GSP"},{"errorCode":"112","errorDesc":"Inactive Client"},{"errorCode":"113","errorDesc":"Inactive User"},{"errorCode":"114","errorDesc":"Technical Error, Pl. contact the helpdesk."},{"errorCode":"115","errorDesc":"Request payload data cannot be empty"},{"errorCode":"116","errorDesc":"Auth token is not valid for this client"},{"errorCode":"117","errorDesc":"This option is not enabled in Eway Bill 2"},{"errorCode":"118","errorDesc":"Try after 5 minutes"},{"errorCode":"201","errorDesc":"Invalid Supply Type"},{"errorCode":"202","errorDesc":"Invalid Sub-supply Type"},{"errorCode":"203","errorDesc":"Sub-transaction type does not belongs to transaction type"},{"errorCode":"204","errorDesc":"Invalid Document type"},{"errorCode":"205","errorDesc":"Document type does not match with transaction \u0026 Sub trans type"},{"errorCode":"206","errorDesc":"Invaild Invoice Number"},{"errorCode":"207","errorDesc":"Invalid Invoice Date"},{"errorCode":"208","errorDesc":"Invalid Supplier GSTIN"},{"errorCode":"209","errorDesc":"Blank Supplier Address"},{"errorCode":"210","errorDesc":"Invalid or Blank Supplier PIN Code"},{"errorCode":"211","errorDesc":"Invalid or Blank Supplier state Code"},{"errorCode":"212","errorDesc":"Invalid Consignee GSTIN"},{"errorCode":"213","errorDesc":"Invalid Consignee Address"},{"errorCode":"214","errorDesc":"Invalid Consignee PIN Code"},{"errorCode":"215","errorDesc":"Invalid Consignee State Code"},{"errorCode":"216","errorDesc":"Invalid HSN Code"},{"errorCode":"217","errorDesc":"Invalid UQC Code"},{"errorCode":"218","errorDesc":"Invalid Tax Rate for Intra State Transaction"},{"errorCode":"219","errorDesc":"Invalid Tax Rate for Inter State Transaction"},{"errorCode":"220","errorDesc":"Invalid Trans mode"},{"errorCode":"221","errorDesc":"Invalid Approximate Distance"},{"errorCode":"222","errorDesc":"Invalid Transporter Id"},{"errorCode":"223","errorDesc":"Invalid Transaction Document Number"},{"errorCode":"224","errorDesc":"Invalid Transaction Date"},{"errorCode":"225","errorDesc":"Invalid Vehicle Number Format"},{"errorCode":"226","errorDesc":"Both Transaction and Vehicle Number Blank"},{"errorCode":"227","errorDesc":"User Gstin cannot be blank"},{"errorCode":"228","errorDesc":"User id cannot be blank"},{"errorCode":"229","errorDesc":"Supplier name is required"},{"errorCode":"230","errorDesc":"Supplier place is required"},{"errorCode":"231","errorDesc":"Consignee name is required"},{"errorCode":"232","errorDesc":"Consignee place is required"},{"errorCode":"233","errorDesc":"Eway bill does not contains any items"},{"errorCode":"234","errorDesc":"Total amount/Taxable amout is mandatory"},{"errorCode":"235","errorDesc":"Tax rates for Intra state transaction is blank"},{"errorCode":"236","errorDesc":"Tax rates for Inter state transaction is blank"},{"errorCode":"237","errorDesc":"Invalid client -Id/client-secret"},{"errorCode":"238","errorDesc":"Invalid auth token"},{"errorCode":"239","errorDesc":"Invalid action"},{"errorCode":"240","errorDesc":"Could not generate eway bill, pls contact helpdesk"},{"errorCode":"250","errorDesc":"Invalid Vehicle Release Date Format"},{"errorCode":"251","errorDesc":"CGST nad SGST TaxRate should be same"},{"errorCode":"252","errorDesc":"Invalid CGST Tax Rate"},{"errorCode":"253","errorDesc":"Invalid SGST Tax Rate"},{"errorCode":"254","errorDesc":"Invalid IGST Tax Rate"},{"errorCode":"255","errorDesc":"Invalid CESS Rate"},{"errorCode":"278","errorDesc":"User Gstin does not match with Transporter Id"},{"errorCode":"280","errorDesc":"Status is not ACTIVE"},{"errorCode":"281","errorDesc":"Eway Bill is already expired hence update transporter is not allowed."},{"errorCode":"282","errorDesc":"At least 4 digit HSN code is mandatory for taxpayers with turnover less than 5Cr."},{"errorCode":"283","errorDesc":"At least 6 digit HSN code is mandatory for taxpayers with turnover 5Cr. and above"},{"errorCode":"301","errorDesc":"Invalid eway bill number"},{"errorCode":"302","errorDesc":"Invalid transporter mode"},{"errorCode":"303","errorDesc":"Vehicle number is required"},{"errorCode":"304","errorDesc":"Invalid vehicle format"},{"errorCode":"305","errorDesc":"Place from is required"},{"errorCode":"306","errorDesc":"Invalid from state"},{"errorCode":"307","errorDesc":"Invalid reason"},{"errorCode":"308","errorDesc":"Invalid remarks"},{"errorCode":"309","errorDesc":"Could not update vehicle details, pl contact helpdesk"},{"errorCode":"311","errorDesc":"Validity period lapsed, you cannot update vehicle details"},{"errorCode":"312","errorDesc":"This eway bill is either not generated by you or cancelled"},{"errorCode":"315","errorDesc":"Validity period lapsed, you cannot cancel this eway bill"},{"errorCode":"316","errorDesc":"Eway bill is already verified, you cannot cancel it"},{"errorCode":"317","errorDesc":"Could not cancel eway bill, please contact helpdesk"},{"errorCode":"320","errorDesc":"Invalid state to"},{"errorCode":"321","errorDesc":"Invalid place to"},{"errorCode":"322","errorDesc":"Could not generate consolidated eway bill"},{"errorCode":"325","errorDesc":"Could not retrieve data"},{"errorCode":"326","errorDesc":"Could not retrieve GSTIN details for the given GSTIN number"},{"errorCode":"327","errorDesc":"Could not retrieve data from hsn"},{"errorCode":"328","errorDesc":"Could not retrieve transporter details from gstin"},{"errorCode":"329","errorDesc":"Could not retrieve States List"},{"errorCode":"330","errorDesc":"Could not retrieve UQC list"},{"errorCode":"331","errorDesc":"Could not retrieve Error code"},{"errorCode":"334","errorDesc":"Could not retrieve user details by userid "},{"errorCode":"336","errorDesc":"Could not retrieve transporter data by gstin "},{"errorCode":"337","errorDesc":"Could not retrieve HSN details for the given HSN number"},{"errorCode":"338","errorDesc":"You cannot update transporter details, as the current tranporter is already entered Part B details of the eway bill"},{"errorCode":"339","errorDesc":"You are not assigned to update the tranporter details of this eway bill"},{"errorCode":"341","errorDesc":"This e-way bill is generated by you and hence you cannot reject it"},{"errorCode":"342","errorDesc":"You cannot reject this e-way bill as you are not the other party to do so"},{"errorCode":"343","errorDesc":"This e-way bill is cancelled"},{"errorCode":"344","errorDesc":"Invalid eway bill number"},{"errorCode":"345","errorDesc":"Validity period lapsed, you cannot reject the e-way bill"},{"errorCode":"346","errorDesc":"You can reject the e-way bill only within 72 hours from generated timel"},{"errorCode":"347","errorDesc":"Validation of eway bill number failed, while rejecting ewaybill"},{"errorCode":"350","errorDesc":"Could not generate consolidated eway bill"},{"errorCode":"351","errorDesc":"Invalid state code"},{"errorCode":"352","errorDesc":"Invalid rfid date"},{"errorCode":"353","errorDesc":"Invalid location code"},{"errorCode":"354","errorDesc":"Invalid rfid number"},{"errorCode":"355","errorDesc":"Invalid Vehicle Number Format"},{"errorCode":"356","errorDesc":"Invalid wt on bridge"},{"errorCode":"357","errorDesc":"Could not retrieve eway bill details, pl. contact helpdesk"},{"errorCode":"358","errorDesc":"GSTIN passed in request header is not matching with the user gstin mentioned in payload JSON"},{"errorCode":"359","errorDesc":"User GSTIN should match to GSTIN(from) for outward transactions"},{"errorCode":"360","errorDesc":"User GSTIN should match to GSTIN(to) for inward transactions"},{"errorCode":"361","errorDesc":"Invalid Vehicle Type"},{"errorCode":"362","errorDesc":"Transporter document date cannot be earlier than the invoice date"},{"errorCode":"363","errorDesc":"E-way bill is not enabled for intra state movement for you state"},{"errorCode":"364","errorDesc":"Error in verifying eway bill"},{"errorCode":"365","errorDesc":"Error in verifying consolidated eway bill"},{"errorCode":"366","errorDesc":"You will not get the ewaybills generated today, howerver you cann access the ewaybills of yester days"},{"errorCode":"367","errorDesc":"Could not retrieve data for officer login"},{"errorCode":"368","errorDesc":"Could not update transporter"},{"errorCode":"369","errorDesc":"GSTIN/Transin passed in request header should match with the transported Id mentioned in payload JSON"},{"errorCode":"370","errorDesc":"GSTIN/Transin passed in request header should not be the same as supplier(fromGSTIN) or recepient(toGSTIN)"},{"errorCode":"371","errorDesc":"Invalid or Blank Supplier Ship-to State Code"},{"errorCode":"372","errorDesc":"Invalid or Blank Consignee Ship-to State Code"},{"errorCode":"373","errorDesc":"The Supplier ship-from state code should be Other Country for Sub Supply Type- Export"},{"errorCode":"374","errorDesc":"The Consignee pin code should be 999999 for Sub Supply Type- Export"},{"errorCode":"375","errorDesc":"The Supplier ship-to state code should be Other Country for Sub Supply Type- Import"},{"errorCode":"376","errorDesc":"The Supplier pin code should be 999999 for Sub Supply Type- Import"},{"errorCode":"377","errorDesc":"Sub Supply Type is mentioned as Others, the description for that is mandatory"},{"errorCode":"378","errorDesc":"The supplier or conginee belong to SEZ, Inter state tax rates are applicable here"},{"errorCode":"379","errorDesc":"Eway Bill can not be extended.. Already Cancelled"},{"errorCode":"380","errorDesc":"Eway Bill Can not be Extended. Not in Active State"},{"errorCode":"381","errorDesc":"There is No PART-B/Vehicle Entry.. So Please Update Vehicle Information.."},{"errorCode":"382","errorDesc":"You Cannot Extend as EWB can be Extended only 8 hour before or after w.r.t Validity of EWB..!!"},{"errorCode":"383","errorDesc":"Error While Extending..Please Contact Helpdesk. "},{"errorCode":"384","errorDesc":"You are not current transporter or Generator of the ewayBill, with no transporter details."},{"errorCode":"385","errorDesc":"For Rail/Ship/Air transDocNo and transDocDate is mandatory"},{"errorCode":"386","errorDesc":"Reason Code, Remarks is mandatory."},{"errorCode":"387","errorDesc":"No Record Found for Entered consolidated eWay bill."},{"errorCode":"388","errorDesc":"Exception in regenration of consolidated eWayBill!!Please Contact helpdesk"},{"errorCode":"389","errorDesc":"Remaining Distance Required"},{"errorCode":"390","errorDesc":"Remaining Distance Can not be greater than Actual Distance."},{"errorCode":"391","errorDesc":"No eway bill of specified tripsheet, neither  ACTIVE nor not Valid."},{"errorCode":"392","errorDesc":"Tripsheet is already cancelled, Hence Regeration is not possible"},{"errorCode":"393","errorDesc":"Invalid GSTIN"},{"errorCode":"394","errorDesc":"For other than Road Transport, TransDoc number is required"},{"errorCode":"395","errorDesc":"Eway Bill Number should be numeric only"},{"errorCode":"396","errorDesc":"Either Eway Bill Number Or Consolidated Eway Bill Number is required for Verification"},{"errorCode":"397","errorDesc":"Error in Multi Vehicle Movement Initiation"},{"errorCode":"398","errorDesc":"Eway Bill Item List is Empty"},{"errorCode":"399","errorDesc":"Unit Code is not matching with any of the Unit Code from ItemList"},{"errorCode":"400","errorDesc":"total quantity is exceeding from multi vehicle movement initiation quantity"},{"errorCode":"401","errorDesc":"Error in inserting multi vehicle details"},{"errorCode":"402","errorDesc":"total quantity can not be less than or equal to zero"},{"errorCode":"403","errorDesc":"Error in multi vehicle details"},{"errorCode":"405","errorDesc":"No record found for multi vehicle update with specified ewbNo groupNo and old vehicleNo/transDocNo with status as ACT"},{"errorCode":"406","errorDesc":"Group number cannot be empty or zero"},{"errorCode":"407","errorDesc":"Invalid old vehicle number format"},{"errorCode":"408","errorDesc":"Invalid new vehicle number format"},{"errorCode":"409","errorDesc":"Invalid old transDoc number"},{"errorCode":"410","errorDesc":"Invalid new transDoc number"},{"errorCode":"411","errorDesc":"Multi Vehicle Initiation data is not there for specified ewayBill and group No"},{"errorCode":"412","errorDesc":"Multi Vehicle movement is already Initiated,hence PART B updation not allowed"},{"errorCode":"413","errorDesc":"Unit Code is not matching with unit code of first initiaton"},{"errorCode":"415","errorDesc":"Error in fetching in verification data for officer"},{"errorCode":"416","errorDesc":"Date range is exceeding allowed date range "},{"errorCode":"417","errorDesc":"No verification data found for officer "},{"errorCode":"418","errorDesc":"No record found"},{"errorCode":"419","errorDesc":"Error in fetching search result for taxpayer/transporter"},{"errorCode":"420","errorDesc":"Minimum six character required for Tradename/legalname search"},{"errorCode":"421","errorDesc":"Invalid pincode"},{"errorCode":"422","errorDesc":"Invalid mobile number"},{"errorCode":"423","errorDesc":"Error in fetching ewaybill list by vehicle number"},{"errorCode":"424","errorDesc":"Invalid PAN number"},{"errorCode":"432","errorDesc":"invalid vehicle released value"},{"errorCode":"433","errorDesc":"invalid goods detained parameter value"},{"errorCode":"434","errorDesc":"invalid ewbNoAvailable parameter value"},{"errorCode":"435","errorDesc":"Part B is already updated,hence updation is not allowed"},{"errorCode":"436","errorDesc":"Invalid email id"},{"errorCode":"442","errorDesc":"Error in inserting verification details"},{"errorCode":"443","errorDesc":"invalid invoice available value"},{"errorCode":"444","errorDesc":"This eway bill cannot be cancelled as it is generated from Eway Bill 1"},{"errorCode":"445","errorDesc":"This eway bill cannot be cancelled as it is generated from Eway Bill 2"},{"errorCode":"446","errorDesc":"Transport details cannot be updated here as it is generated from Eway Bill 1"},{"errorCode":"447","errorDesc":"Transport details cannot be updated here as it is generated from Eway Bill 2"},{"errorCode":"448","errorDesc":"Part B cannot be updated as this Ewaybill Part A is generated in Eway Bill 1"},{"errorCode":"449","errorDesc":"Part B cannot be updated as this Ewaybill Part A is generated in Eway Bill 2"},{"errorCode":"450","errorDesc":"For outward-export ewaybill, To GSTIN has to be either URP or SEZ "},{"errorCode":"451","errorDesc":"For inward-import ewaybill, From GSTIN has to be either URP or SEZ"},{"errorCode":"452","errorDesc":"Consolidate Ewaybill cannot be generated as this Ewaybill Part A is generated in Eway Bill 2"},{"errorCode":"600","errorDesc":"Invalid category"},{"errorCode":"601","errorDesc":"Invalid date format"},{"errorCode":"602","errorDesc":"Invalid File Number"},{"errorCode":"603","errorDesc":"For file details file number is required"},{"errorCode":"604","errorDesc":"E-way bill(s) are already generated for the same document number, you cannot generate again on same document number"},{"errorCode":"605","errorDesc":" If the goods are moving towards transporter location, the value of toTransporterLoc should be Y"},{"errorCode":"606","errorDesc":"Vehicle type is mandatory, if the goods are moving to transporter place"},{"errorCode":"607","errorDesc":"dispatch from gstin is mandatary "},{"errorCode":"608","errorDesc":"ship to from gstin is mandatary"},{"errorCode":"609","errorDesc":" invalid ship to from gstin "},{"errorCode":"610","errorDesc":"invalid dispatch from gstin "},{"errorCode":"611","errorDesc":"invalid document type for the given supply type "},{"errorCode":"612","errorDesc":"Invalid transaction type"},{"errorCode":"614","errorDesc":"Transaction type is mandatory"},{"errorCode":"617","errorDesc":"Bill-from and dispatch-from gstin should not be same for this transaction type"},{"errorCode":"618","errorDesc":"Bill-to and ship-to gstin should not be same for this transaction type"},{"errorCode":"619","errorDesc":"Transporter Id is mandatory for generation of Part A slip"},{"errorCode":"620","errorDesc":"Total invoice value cannot be less than the sum of total assessible value and tax values"},{"errorCode":"621","errorDesc":"trans mode is mandatory since vehicle number is present"},{"errorCode":"622","errorDesc":"trans mode is mandatory since trans doc number is present"},{"errorCode":"627","errorDesc":"Total value should not be negative"},{"errorCode":"628","errorDesc":"Total invoice value should not be negative"},{"errorCode":"629","errorDesc":"IGST value should not be negative"},{"errorCode":"630","errorDesc":"CGST value should not be negative"},{"errorCode":"631","errorDesc":"SGST value should not be negative"},{"errorCode":"632","errorDesc":"Cess value should not be negative"},{"errorCode":"633","errorDesc":"Cess non advol should not be negative"},{"errorCode":"634","errorDesc":"Vehicle type should not be ODC when transmode is other than road"},{"errorCode":"635","errorDesc":"You cannot update part B, as the current tranporter is already entered Part B details of the eway bill"},{"errorCode":"636","errorDesc":"You are not assigned to update part B"},{"errorCode":"637","errorDesc":"You cannot extend ewaybill, as the current tranporter is already entered Part B details of the ewaybill"},{"errorCode":"638","errorDesc":"Transport mode is mandatory as Vehicle Number/Transport Document Number is given"},{"errorCode":"640","errorDesc":"Tolal Invoice value is mandatory"},{"errorCode":"641","errorDesc":"For outward CKD/SKD/Lots supply type, Bill To state should be as Other Country, since the  Bill To GSTIN given is of SEZ unit"},{"errorCode":"642","errorDesc":"For inward CKD/SKD/Lots supply type, Bill From state should be as Other Country, since the  Bill From GSTIN given is of SEZ unit"},{"errorCode":"643","errorDesc":"For regular transaction, Bill from state code and Dispatch from state code should be same"},{"errorCode":"644","errorDesc":"For regular transaction, Bill to state code and Ship to state code should be same"},{"errorCode":"645","errorDesc":"You cannot do Multi Vehicle movement, as current transporeter already entered part B"},{"errorCode":"646","errorDesc":"You are not assigned to do multi vehicle movement"},{"errorCode":"647","errorDesc":"Could not insert RFID data, please contact to helpdesk"},{"errorCode":"648","errorDesc":"Multi Vehicle movement is already Initiated,hence generation of consolidated eway bill is not allowed"},{"errorCode":"649","errorDesc":"You cannot generate consolidated eway bill , as the current tranporter is already entered Part B details of the eway bill"},{"errorCode":"650","errorDesc":"You are not assigned to generate consolidated ewaybill"},{"errorCode":"651","errorDesc":"For Category PartA or PartB ewbDt is mandatory"},{"errorCode":"652","errorDesc":"For Category EWB03 procDt is mandatory"},{"errorCode":"653","errorDesc":"The Ewaybill is cancelled"},{"errorCode":"654","errorDesc":"This GSTIN has generated a common Enrolment Number. Hence you are not allowed to generate Eway bill"},{"errorCode":"655","errorDesc":"This GSTIN has generated a common Enrolment Number. Hence you cannot mention it as a transporter"},{"errorCode":"656","errorDesc":"This Eway Bill does not belongs to your state"},{"errorCode":"657","errorDesc":"Eway Bill Category wise details will be available after 4 days only"},{"errorCode":"658","errorDesc":"You are blocked for accesing this API as the allowed number of requests has been exceeded"},{"errorCode":"659","errorDesc":"Remarks is mandatory"},{"errorCode":"670","errorDesc":"Invalid Month Parameter"},{"errorCode":"671","errorDesc":"Invalid Year Parameter"},{"errorCode":"672","errorDesc":"User Id is mandatory"},{"errorCode":"673","errorDesc":"Error in getting officer dashboard"},{"errorCode":"675","errorDesc":"Error in getting EWB03 details by acknowledgement date range"},{"errorCode":"678","errorDesc":"Invalid Uniq No"},{"errorCode":"679","errorDesc":"Invalid EWB03 Ack No"},{"errorCode":"680","errorDesc":"Invalid Close Reason"},{"errorCode":"681","errorDesc":"Error in Closing EWB  Verification Data"},{"errorCode":"682","errorDesc":"No Record available to Close"},{"errorCode":"683","errorDesc":"Error in fetching WatchList Data"},{"errorCode":"700","errorDesc":"You are not assigned to extend e-waybill"},{"errorCode":"711","errorDesc":"Invalid value for isInTransit field"},{"errorCode":"712","errorDesc":"Transit Type is not required as the good are not in movement"},{"errorCode":"713","errorDesc":"Transit Address is not required as the good are not in movement"},{"errorCode":"714","errorDesc":"Document type - Tax Invoice is not allowed for composite tax payer"},{"errorCode":"715","errorDesc":"The Consignor GSTIN is blocked from e-waybill generation as Return is not filed for past 2 months"},{"errorCode":"716","errorDesc":"The Consignee GSTIN is blocked from e-waybill generation as Return is not filed for past 2 months"},{"errorCode":"717","errorDesc":"The Transporter GSTIN is blocked from e-waybill generation as Return is not filed for past 2 months"},{"errorCode":"718","errorDesc":"The User GSTIN is blocked from Transporter Updation as Return is not filed for past 2 months"},{"errorCode":"719","errorDesc":"The Transporter GSTIN is blocked from Transporter Updation as Return is not filed for past 2 months"},{"errorCode":"800","errorDesc":"Redis server Is not Working  try after some time"},{"errorCode":"801","errorDesc":"Transporter id is not required for ewaybill for gold"},{"errorCode":"802","errorDesc":"Transporter name is not required for ewaybill for gold"},{"errorCode":"803","errorDesc":"TransDocNo is not required for ewaybill for gold"},{"errorCode":"804","errorDesc":"TransDocDate is not required for ewaybill for gold"},{"errorCode":"805","errorDesc":"Vehicle No is not required for ewaybill for gold"},{"errorCode":"806","errorDesc":"Vehicle Type is not required for ewaybill for gold"},{"errorCode":"807","errorDesc":"Transmode is mandatory for ewaybill for gold"},{"errorCode":"808","errorDesc":"Inter-State ewaybill is not allowed for gold"},{"errorCode":"809","errorDesc":"Other items are not allowed with eway bill for gold"},{"errorCode":"810","errorDesc":"Transport can not be updated for EwayBill For Gold"},{"errorCode":"811","errorDesc":"Vehicle can not be updated for EwayBill For Gold"},{"errorCode":"812","errorDesc":"ConsolidatedEWB cannot be generated for EwayBill For Gold "},{"errorCode":"813","errorDesc":"Transporter id is not required for ewaybill for gold"},{"errorCode":"814","errorDesc":"Transporter name is not required for ewaybill for gold"},{"errorCode":"815","errorDesc":"TransDocNo is not required for ewaybill for gold"},{"errorCode":"816","errorDesc":"TransDocDate is not required for ewaybill for gold"},{"errorCode":"817","errorDesc":"Vehicle No is not required for ewaybill for gold"},{"errorCode":"818","errorDesc":"Validity period lapsed.Cannot generate consolidated Eway Bill"},{"errorCode":"819","errorDesc":"Ewaybill cannot be generated for the document date which is prior to 01/07/2017"}]',true);
	foreach($errorts_codes as $rows){
		if($rows['errorCode']==$error_code){
			$show_error=$rows['errorDesc'];	
			break;			
		}
	}
	return $show_error;
}

function convert_jpg2($source, $destination = '', $quality = 70)
{
    // Detect mime
    $info = getimagesize($source);
    if (!$info) return false;

    $mime = $info['mime'];
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            // flatten alpha for consistent JPEG output
            $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
            $white = imagecolorallocate($bg, 255, 255, 255);
            imagefilledrectangle($bg, 0, 0, imagesx($bg), imagesy($bg), $white);
            imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            imagedestroy($image);
            $image = $bg;
            break;
        default:
            return false; // unsupported format
    }

    $width  = imagesx($image);
    $height = imagesy($image);

    // simple 25% shrink
    $newWidth  = max(1, (int)($width  * 0.75));
    $newHeight = max(1, (int)($height * 0.75));

    $resized = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Write out as JPEG (or back to original path)
    $target = $destination !== '' ? $destination : $source;
    $ok = imagejpeg($resized, $target, $quality);

    imagedestroy($resized);
    imagedestroy($image);

    return $ok;
}

function convert_jpg2_old($source,$destination='',$compression=70)
{
  $iMagick = new Imagick($source);
    
  try{
    $iMagick->setImageResolution(72,72);
    $iMagick->resampleImage(72,72,imagick::FILTER_UNDEFINED,1);

    $info = getimagesize($source);

    $dimentions = $iMagick->getImageGeometry();
    $width = $dimentions['width'];
    $height = $dimentions['height'];


    // if($width > $height){
    //   $len = intval($height - ($height/100) * 25);
    //   $newHeight = $len;
    //   $newWidth = intval(($len / $height) * $width);

    //   $crop_x = intval(($newWidth - $width) / 2);
    //   $crop_y = 0;
    // }else{
    //   $len = intval($width - ($width/100) * 25);
    //   $newWidth = $len;
    //   $newHeight = intval(($len / $width) * $height);

    //   $crop_x = 0;
    //   $crop_y = intval(($newHeight - $height) / 2);
    // }

    $newHeight = intval($height - ($height/100) * 25);
    $newWidth = intval($width - ($width/100) * 25);

    $crop_x = intval(($newWidth - $width) / 2);
    $crop_y = intval(($newHeight - $height) / 2);

    $iMagick->adaptiveResizeImage($newWidth,$newHeight);
    // $iMagick->cropImage($newWidth,$newHeight,0,0);

    if($info['mime'] == 'image/jpeg'){
      $iMagick->setImageCompression(Imagick::COMPRESSION_JPEG);
    }else if($info['mime'] == 'image/png'){
      $iMagick->setImageCompression(Imagick::COMPRESSION_ZIP);
    }else {
      $iMagick->setImageCompression(Imagick::COMPRESSION_UNDEFINED);
    }
    
    $iMagick->setImageCompressionQuality($compression);
    // $iMagick->setImageFormat('jpg');
    $iMagick->trimImage(0);

    $profiles = $iMagick->getImageProfiles("icc", true);
    $iMagick->stripImage();
    if(!empty($profiles)){
      $iMagick->profileImage("icc", $profiles['icc']);
    }
      
    if($destination != ''){
      unlink($source);
      $iMagick->writeImage($destination);
    }
    else{
      $iMagick->writeImage($source);
    }
    
    $iMagick->clear();
  }
  catch (Exception $e) {
    echo "Error: ".$e->getMessage();

    return false;
  }

  $iMagick->clear();
  return true;
}

 function vehicle_types(){
     return ["R"=>"R","O"=>"ODC"];
}

 function TXNTypesList(){
    return ["1"=>"REGULAR","2"=>"BILL TO – SHIP TO","3"=>"BILL FROM – DISPATCH FROM","4"=>"BILL TO – SHIP TO – BILL FROM – DISPATCH FROM"];
}
 function TXNTypesWOList(){
    return ["1"=>"REGULAR","2"=>"BILL TO – SHIP TO"];
}

function SaveErrorLog($msg){
		$filepath = WRITEPATH.'errors/log-'.date('Y-m-d').'.php';
		$message = '';

		if ( ! file_exists($filepath))
		{
			$newfile = TRUE;
			// Only add protection to php files
			$message .= "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n\n";
			
		}

		if ( ! $fp = @fopen($filepath, 'ab'))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);
       $date = date('Y-m-d H:i:s');
		

		$message .= _format_line(1, $date, $msg);

		for ($written = 0, $length = strlen($message); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, substr($message, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		if (isset($newfile) && $newfile === TRUE)
		{
			chmod($filepath, 0644);
		}
	
	
}

function SaveValuationLog($msg,$item_id,$method_id){
		$filepath = WRITEPATH.'errors/log-'.$item_id.'-'.$method_id.'-'.date('Y-m-d').'.php';
		$message = '';

		if ( ! file_exists($filepath))
		{
			$newfile = TRUE;
			// Only add protection to php files
			$message .= "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n\n";
			
		}

		if ( ! $fp = @fopen($filepath, 'ab'))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);
       $date = date('Y-m-d H:i:s');
		

		$message .= _format_line(1, $date, $msg);

		for ($written = 0, $length = strlen($message); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, substr($message, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		if (isset($newfile) && $newfile === TRUE)
		{
			chmod($filepath, 0644);
		}
	
	
}

function get_fy_name($bgn_date,$end_date)
{
    $total_year = 1;
    if(date('m-d', strtotime($bgn_date)) != '01-01')
        $total_year = 2;

    $fy_from_year  = date("Y", strtotime($bgn_date));
    $fy_to_year  = date("Y", strtotime($end_date));
    if($total_year == 1)
        $fy_name = 'FY: '.$fy_from_year;
    else{
        $sub_from = substr($fy_from_year, 0, 2);
        $sub_to = substr($fy_to_year, 0, 2);

        if($sub_from == $sub_to){
           $fy_name = 'FY: '.$fy_from_year.'-'.substr($fy_to_year, 2, 2);
           $fy_short = substr($fy_from_year, 2, 2).'-'.substr($fy_to_year, 2, 2); 
        }
        else{
            $fy_name = 'FY: '.$fy_from_year.'-'.$fy_to_year;
            $fy_short = 'FY: '.$fy_from_year.'-'.$fy_to_year;
        }
    }
    return $fy_name;
}


function check_date_by_fy($date)
{
    $session = \Config\Services::session();
    $final = [];

    if($session->get('ses_comp_fy_id')){
        $fy_bgn_date = $session->get('ses_company_fy_beginning');
        $fy_end_date = $session->get('ses_company_fy_end');
    }
    else{
        $fy_bgn_date = date('Y-01-01');
        $fy_end_date = date('Y-12-31');
    }


    $date_ymd = date('Y-m-d',strtotime($date));
   
    if($date_ymd <= $fy_bgn_date)
        return true;

    if($date_ymd >= $fy_end_date)
        return true;

    return false;
}

function validate_date_by_fy($date = '')
{
    $session = \Config\Services::session();
    $final = [];

    if($session->get('ses_comp_fy_id')){
        $fy_bgn_date = $session->get('ses_company_fy_beginning');
        $fy_end_date = $session->get('ses_company_fy_end');
    }
    else{
        $fy_bgn_date = date('Y-01-01');
        $fy_end_date = date('Y-12-31');
    }

    if($date != '')
        $date_ymd = date('Y-m-d', strtotime($date));
    else
        $date_ymd = date('Y-m-d');
   
    if($date_ymd < $fy_bgn_date)
        $date_ymd = $fy_bgn_date;

    if($date_ymd > $fy_end_date)
        $date_ymd = $fy_end_date;

    return $date_ymd;
}

function validate_from_date($date = '')
{
    $session = \Config\Services::session();
    $final = [];

    if($session->get('ses_comp_fy_id')){
        $fy_bgn_date = $session->get('ses_company_fy_beginning');
        $fy_end_date = $session->get('ses_company_fy_end');
    }
    else{
        $fy_bgn_date = date('Y-01-01');
        $fy_end_date = date('Y-12-31');
    }

    if($date != '')
        $date_ymd = date('Y-m-d', strtotime($date));
    else
        $date_ymd = date('Y-m-01');
   
    if($date_ymd < $fy_bgn_date || $date_ymd > $fy_end_date)
        $date_ymd = $fy_bgn_date;

    return date('d-m-Y', strtotime($date_ymd));
}

function validate_to_date($date = '')
{
    $session = \Config\Services::session();
    $final = [];

    if($session->get('ses_comp_fy_id')){
        $fy_bgn_date = $session->get('ses_company_fy_beginning');
        $fy_end_date = $session->get('ses_company_fy_end');
    }
    else{
        $fy_bgn_date = date('Y-01-01');
        $fy_end_date = date('Y-12-31');
    }

    if($date != '')
        $date_ymd = date('Y-m-d', strtotime($date));
    else
        $date_ymd = date('Y-m-d');
   
    if($date_ymd < $fy_bgn_date || $date_ymd > $fy_end_date)
        $date_ymd = $fy_end_date;

    return date('d-m-Y', strtotime($date_ymd));
}

function validate_fy_from_date($date = '')
{
    $session = \Config\Services::session();
    $final = [];

    if($session->get('ses_comp_fy_id')){
        $fy_bgn_date = $session->get('ses_company_fy_beginning');
        $fy_end_date = $session->get('ses_company_fy_end');
    }
    else{
        $fy_bgn_date = date('Y-01-01');
        $fy_end_date = date('Y-12-31');
    }


    if($date != ''){
        $date_ymd = date('Y-m-d', strtotime($date));
        if($date_ymd < $fy_bgn_date)
            $date_ymd = $fy_bgn_date;
    }
    else{
        $date_ymd = $fy_bgn_date;
    }

    return date('d-m-Y', strtotime($date_ymd));
}
function validate_fy_to_date($date = '')
{
    $session = \Config\Services::session();
    $final = [];

    if($session->get('ses_comp_fy_id')){
        $fy_bgn_date = $session->get('ses_company_fy_beginning');
        $fy_end_date = $session->get('ses_company_fy_end');
    }
    else{
        $fy_bgn_date = date('Y-01-01');
        $fy_end_date = date('Y-12-31');
    }

    if($date != ''){
        $date_ymd = date('Y-m-d', strtotime($date));
        if($date_ymd > $fy_end_date)
            $date_ymd = $fy_end_date;
    }
    else{
        $date_ymd = $fy_end_date;
    }

    return date('d-m-Y', strtotime($date_ymd));
}



function grp_comp($clear = true)
{
    $session = \Config\Services::session();

    $id = 0;
    $name = '';
    $alias = '';
    $code = '';
    $list = '[]';
    $fy_id = '';
    $fy_bgn_date = '';
    $fy_end_date = '';

    $fy_name = '';
    $fy_list = '[]';

    $fy_bgn_date_dmy = '';
    $fy_end_date_dmy = '';

    if($session->get('grp_comp_id')){

        if($clear){
            if($session->get('ses_company_id')){
                clear_comp_sess();
            }
        }
            

        $id = $session->get('grp_comp_id');
        $name = $session->get('grp_comp_name');
        $alias = $session->get('grp_comp_alias');
        $code = $session->get('grp_comp_code');
        $list = $session->get('grp_comp_list');
        $fy_id = $session->get('grp_comp_fy_id');
        $fy_bgn_date = $session->get('grp_comp_fy_bgn_date');
        $fy_end_date = $session->get('grp_comp_fy_end_date');
        $fy_name = $session->get('grp_comp_fy_name');
        $fy_list = $session->get('grp_comp_fy_list');

        $fy_bgn_date_dmy  = date('d-m-Y', strtotime($fy_bgn_date));
        $fy_end_date_dmy  = date('d-m-Y', strtotime($fy_end_date));
    }
    

    return (object)[
        'id'               => $id,
        'name'             => $name,
        'alias'            => $alias,
        'code'             => $code,
        'list'             => json_decode($list),
        'fy_id'            => $fy_id,
        'fy_bgn_date'      => $fy_bgn_date,
        'fy_end_date'      => $fy_end_date,
        'fy_name'          => $fy_name, 
        'fy_list'          => json_decode($fy_list),
        'fy_bgn_date_dmy'  => $fy_bgn_date_dmy,
        'fy_end_date_dmy'  => $fy_end_date_dmy,
    ];
}

function grp_comp_branch_list()
{
    $list = [];
    if(grp_comp()->id){
        $model = new \App\Models\Grpcomp\CompanyModel();
        $list = $model->group_branch_list();
    }
    return $list;
}

function grp_comp_details($comp_id)
{
    $comp_name = '';
    $comp_fy_id = 0;

    if(grp_comp()->id){
        $comp_list = grp_comp()->list;

        foreach ($comp_list as $key => $value) {
            if($value->comp_id == $comp_id){
                $comp_name = $value->comp_name;
                $comp_fy_id = $value->comp_fy_id;
            }
        }
    }
    return (object)[
        'comp_name'    => $comp_name,
        'comp_fy_id'   => $comp_fy_id,
    ];
}


function grp_fy_calender()
{
  $months = [];
  if(grp_comp()->id){

    $fy_bgn_date = grp_comp()->fy_bgn_date;
    $fy_end_date = grp_comp()->fy_end_date;

    $start_date     = strtotime($fy_bgn_date);
    $end_date       = strtotime($fy_end_date);

    $months = [];
    while( $start_date <= $end_date ) {

    $from_date = date('Y-m-01', $start_date);
    $to_date = date('Y-m-t', $start_date);
    $month = date("M, Y", $start_date);

    $months[] = [
        'from_date'  => $from_date,
        'to_date'    => $to_date,
        'month'      => $month,
    ];

    $start_date = strtotime('+1 month', $start_date);
    // $count_month++;
    }
    
  }

  return (object)$months;
}

function grp_fy_calender_js()
{
  $final = [];
  if(grp_comp()->id){

    foreach (grp_comp()->fy_list as $key => $value) {

      $fy_bgn_date = $value->bgn_date;
      $fy_end_date = $value->end_date;

      $start_date     = strtotime($fy_bgn_date);
      $end_date       = strtotime($fy_end_date);

      $fy_from_date = date('d-m-Y', $start_date);
      $fy_to_date = date('d-m-Y', $end_date);

      $fy_from_day   = date("d", $start_date);
      $fy_from_month = date("m", $start_date);
      $fy_from_year  = date("Y", $start_date);

      $fy_to_day   = date("d", $end_date);
      $fy_to_month = date("m", $end_date);
      $fy_to_year  = date("Y", $end_date);

      $start_year = date('Y', $start_date);
      $end_year = date('Y', $end_date);

      $count_month = 0;
      $count_year = 0;
      $year  = date("Y", strtotime($start_date));

      $qmonth = 1;
      $qqmonth = 0;

      $months = [];
      $type = 1;

      while( $start_date <= $end_date ) {
        if($count_month == 0)
            $from_date = date('Y-m-d', strtotime($fy_bgn_date));
        else
            $from_date = date('Y-m-01', $start_date);

        if($year != date("Y", strtotime($from_date))){
            $count_year++;
        }

        $label = date("M", strtotime($from_date));
        $month = date("m", strtotime($from_date));
        $year  = date("Y", strtotime($from_date));

        $key = 0;
        if(in_array($month, [1,2,3])){
            $qmonth = 1;
            $key = $month;
        }
        if(in_array($month, [4,5,6])){
            $qmonth = 2;
            $key = $month - 3;
        }
        if(in_array($month, [7,8,9])){
            $qmonth = 3;
            $key = $month - 6;
        }
        if(in_array($month, [10,11,12])){
            $qmonth = 4;
            $key = $month - 9;
        }
        $key = intval($key);

        $months[$year][$qmonth][$key] = [
            'label'     => strtoupper($label),
            'month'     => intval($month),
            'year'      => intval($year),
        ];
        
        $start_date = strtotime('+1 month', $start_date);
        $count_month++;
      }

      
      if($count_month == 12 && count($months) == 2 && (date('d-m', $start_date) == '01-04' || date('d-m', $start_date) == '01-01') ){

        $type = 2;
        $new_months = [];
        $k = 1;
        foreach ($months as $ke => $val) {
          foreach ($val as $ke2 => $val2) {
            $new_months[$k] = $val2;
            $k++;
          }
        }

        $k = 1;
        foreach ($new_months as $ke => $val) {
      
          $from_month = min(array_column($val, 'month'));
          $to_month = max(array_column($val, 'month'));
          $from_year = min(array_column($val, 'year'));
          $to_year = max(array_column($val, 'year'));

          $new_months[$ke][4] = [
              'label'           => 'Q'.$k,
              'from_month'      => $from_month,
              'from_year'       => $from_year,
              'to_month'        => $to_month,
              'to_year'         => $to_year,
          ];
          $k++;
        }

        $from_month = $new_months[1][1]['month'];
        $to_month = $new_months[2][3]['month'];
        $from_year = $new_months[1][1]['year'];
        $to_year = $new_months[2][3]['year'];
        $new_months[1][5] = [
              'label'           => 'H1',
              'from_month'      => $from_month,
              'from_year'       => $from_year,
              'to_month'        => $to_month,
              'to_year'         => $to_year,
          ];

        $from_month = $new_months[3][1]['month'];
        $to_month = $new_months[4][3]['month'];
        $from_year = $new_months[3][1]['year'];
        $to_year = $new_months[4][3]['year'];
        $new_months[2][5] = [
              'label'           => 'H2',
              'from_month'      => $from_month,
              'from_year'       => $from_year,
              'to_month'        => $to_month,
              'to_year'         => $to_year,
          ];

        $new_months[3][5] = false;
        $new_months[4][5] = false;

        $months = $new_months;
      }

      $final[] = [
          'from_date'     => $fy_from_date,
          'from_day'      => $fy_from_day,
          'from_month'    => $fy_from_month,
          'from_year'     => $fy_from_year,
          'to_date'       => $fy_to_date,
          'to_year'       => $fy_to_year,
          'to_day'        => $fy_to_day,
          'to_month'      => $fy_to_month,
          
          'fy_name'       => $value->fy_name,
          'fy_id'         => $value->grp_fy_id,
          'fy_months'     => $months,
          'type'          => $type,
      ];
    }
  }

  return $final;
}

function grp_validate_fy_from_date($date = '')
{
    $final = [];

    if(grp_comp()->id){
        $fy_bgn_date = grp_comp()->fy_bgn_date;
        $fy_end_date = grp_comp()->fy_end_date;
    }
    else{
        $fy_bgn_date = date('Y-01-01');
        $fy_end_date = date('Y-12-31');
    }


    if($date != ''){
        $date_ymd = date('Y-m-d', strtotime($date));
        if($date_ymd < $fy_bgn_date)
            $date_ymd = $fy_bgn_date;
    }
    else{
        $date_ymd = $fy_bgn_date;
    }

    return date('d-m-Y', strtotime($date_ymd));
}
function grp_validate_fy_to_date($date = '')
{
  
    $final = [];

    if(grp_comp()->id){
        $fy_bgn_date = grp_comp()->fy_bgn_date;
        $fy_end_date = grp_comp()->fy_end_date;
    }
    else{
        $fy_bgn_date = date('Y-01-01');
        $fy_end_date = date('Y-12-31');
    }

    if($date != ''){
        $date_ymd = date('Y-m-d', strtotime($date));
        if($date_ymd > $fy_end_date)
            $date_ymd = $fy_end_date;
    }
    else{
        $date_ymd = $fy_end_date;
    }

    return date('d-m-Y', strtotime($date_ymd));
}

function get_add_voucher_addr($voucher_type_id)
{
    $addr = '';
    if($voucher_type_id == 18)
        $addr = 'sales/item';
    if($voucher_type_id == 19)
        $addr = 'sales_order/item';
    if($voucher_type_id == 2)
        $addr = 'credit_note/item';
    if($voucher_type_id == 7)
        $addr = 'delivery_challan/add';
    if($voucher_type_id == 17)
        $addr = 'quotations/item';

    //---------
    if($voucher_type_id == 11)
        $addr = 'purchase/item';
    if($voucher_type_id == 12)
        $addr = 'purchase_order/item';
    if($voucher_type_id == 3)
        $addr = 'debit_note/item';
    if($voucher_type_id == 6)
        $addr = 'inward_challan/add';
    if($voucher_type_id == 21)
        $addr = 'purchase_requisition/with_amount';

    //---------
    if($voucher_type_id == 9)
        $addr = 'vouchers/invoice/9';
    if($voucher_type_id == 13)
        $addr = 'vouchers/invoice/13';
    if($voucher_type_id == 1)
        $addr = 'vouchers/invoice/1';
    if($voucher_type_id == 5)
        $addr = 'vouchers/invoice/5';
    if($voucher_type_id == 8)
        $addr = 'memorandum/invoice';

    //---------
    if($voucher_type_id == 15)
        $addr = 'stock_transfer/mc';
    if($voucher_type_id == 10)
        $addr = 'physical_verification/add';
    if($voucher_type_id == 14)
        $addr = 'production/add';
    if($voucher_type_id == 20)
        $addr = 'stock_journal/invoice/19';
    if($voucher_type_id == 4)
        $addr = 'consignment_packing';
    
    return $addr;
}

function get_edit_voucher_addr($voucher_type_id)
{
    $addr = '';
    if($voucher_type_id == 18)
        $addr = 'sales/edit/';
    if($voucher_type_id == 19)
        $addr = 'sales_order/edit/';
    if($voucher_type_id == 2)
        $addr = 'credit_note/edit/';
    if($voucher_type_id == 7)
        $addr = 'delivery_challan/edit/';
    if($voucher_type_id == 17)
        $addr = 'quotations/edit/';

    //---------
    if($voucher_type_id == 11)
        $addr = 'purchase/edit/';
    if($voucher_type_id == 12)
        $addr = 'purchase_order/edit/';
    if($voucher_type_id == 3)
        $addr = 'debit_note/edit/';
    if($voucher_type_id == 6)
        $addr = 'inward_challan/edit/';
    if($voucher_type_id == 21)
        $addr = 'purchase_requisition/edit/';

    //---------
    if($voucher_type_id == 9)
        $addr = 'vouchers/edit/';
    if($voucher_type_id == 13)
        $addr = 'vouchers/edit/';
    if($voucher_type_id == 1)
        $addr = 'vouchers/edit/';
    if($voucher_type_id == 5)
        $addr = 'vouchers/edit/';
    if($voucher_type_id == 8)
        $addr = 'memorandum/edit/';
    if($voucher_type_id == 16)
        $addr = 'reverse_journal/edit/';

    //---------
    if($voucher_type_id == 15)
        $addr = 'stock_transfer/edit/';
    if($voucher_type_id == 10)
        $addr = 'physical_verification/edit/';
    if($voucher_type_id == 14)
        $addr = 'production/edit/';
    if($voucher_type_id == 20)
        $addr = 'stock_journal/edit/';
    if($voucher_type_id == 4)
        $addr = 'consignment_packing/edit/';
    
    return $addr;
}

function clear_comp_sess()
{
    $session = \Config\Services::session();

    $session->remove('ses_company_id');
    $session->remove('ses_company_code');
    $session->remove('ses_compl_company_code');
    $session->remove('ses_company_name');
    $session->remove('ses_company_print_name');
    $session->remove('ses_company_short_name');
    $session->remove('ses_company_fy_beginning');
    $session->remove('ses_company_fy_end');
    $session->remove('ses_company_email');
    $session->remove('ses_company_mobile');
    $session->remove('ses_company_wa_mobile');
    $session->remove('ses_company_gstin');
    $session->remove('ses_company_tan_no');
    $session->remove('ses_company_pan_no');
    $session->remove('user_type');
    $session->remove('comp_uuid');
}

function clear_grp_comp_sess()
{
    $session = \Config\Services::session();

    $session->remove('grp_comp_id');
    $session->remove('grp_comp_name');
    $session->remove('grp_comp_alias');
    $session->remove('grp_comp_code');
    $session->remove('grp_comp_list');
    $session->remove('grp_comp_fy_id');
    $session->remove('grp_comp_fy_bgn_date');
    $session->remove('grp_comp_fy_end_date');
    $session->remove('grp_comp_fy_name');
    $session->remove('grp_comp_fy_list');
}

function comp()
{
    $session = \Config\Services::session();

    $id = 0;
    $name = '';
    $code = '';
    if($session->get('ses_company_id')){
        $id = $session->get('ses_company_id');
        $name = $session->get('ses_company_code');
        $code =  $session->get('ses_company_name');
    }

    return (object)[
        'id'             => $id,
        'name'           => $name,
        'code'           => $code,
    ];
}

function company()
{
    $session = \Config\Services::session();

    $currency = '';
    $fy_name = '';
    $bo_name = '';
    $fy_bgn_date = '';
    $fy_end_date = '';
    $bo_short = '';
    $fy_short = '';

    if($session->get('ses_company_id') && $session->get('ses_comp_fy_id') ){
        $fy_bgn_date = $session->get('ses_company_fy_beginning');
        $fy_end_date = $session->get('ses_company_fy_end');

        $start_date     = strtotime($fy_bgn_date);
        $end_date       = strtotime($fy_end_date);

        $total_year = 1;
        if(date('m-d', $start_date) != '01-01')
            $total_year = 2;

        $fy_from_year  = date("Y", $start_date);
        $fy_to_year  = date("Y", $end_date);
        if($total_year == 1)
            $fy_name = 'FY: '.$fy_from_year;
        else{
            $sub_from = substr($fy_from_year, 0, 2);
            $sub_to = substr($fy_to_year, 0, 2);

            if($sub_from == $sub_to){
               $fy_name = 'FY: '.$fy_from_year.'-'.substr($fy_to_year, 2, 2);
               $fy_short = substr($fy_from_year, 2, 2).'-'.substr($fy_to_year, 2, 2); 
            }
            else{
                $fy_name = 'FY: '.$fy_from_year.'-'.$fy_to_year;
                $fy_short = 'FY: '.$fy_from_year.'-'.$fy_to_year;
            }
        }

        $currency = '&#8377;';

        $model = new \App\Models\CommonModel();
        $GetBoInfo =  $model->GetBoInfo();
		if($GetBoInfo)
            $bo_name  = $GetBoInfo['hobo_name'];
    	else
    		$bo_name  = "N/A";

        $bo_short = substr($bo_name, 0, 3);
    }
    

    return (object)[
        'currency'          => $currency,
        'fy_name'           => $fy_name,
        'bo_name'           => $bo_name,
        'bo_short'           => $bo_short,
        'fy_short'           => $fy_short,
        'fy_from_date'      => $fy_bgn_date,
        'fy_to_date'        => $fy_end_date,
    ];
}

function fy_calender() // used in all summaries 
{
    $session = \Config\Services::session();
    $final = [];

    if($session->get('ses_company_id')){
        $fy_bgn_date = $session->get('ses_company_fy_beginning');
        $fy_end_date = $session->get('ses_company_fy_end');
    }
    else{
        $fy_bgn_date = date('Y-01-01');
        $fy_end_date = date('Y-12-31');
    }

    $start_date     = strtotime($fy_bgn_date);
    $end_date       = strtotime($fy_end_date);

    $total_year = 1;
    if(date('m-d', $start_date) != '01-01')
        $total_year = 2;

    $fy_from_year  = date("Y", $start_date);
    $fy_to_year  = date("Y", $end_date);
    if($total_year == 1)
        $fy_name = 'FY: '.$fy_from_year;
    else{
        $sub_from = substr($fy_from_year, 0, 2);
        $sub_to = substr($fy_to_year, 0, 2);

        if($sub_from == $sub_to){
           $fy_name = 'FY: '.$fy_from_year.'-'.substr($fy_to_year, 2, 2); 
        }
        else
            $fy_name = 'FY: '.$fy_from_year.'-'.$fy_to_year;
    }

    $total_months = 12;
    if(date('d', $start_date) != '01')
        $total_months = 13;

    $count_month = 1;

    while( $count_month <= $total_months ) {
        if($count_month == 1)
            $from_date = date( 'Y-m-d', strtotime($fy_bgn_date) );
        else
            $from_date = date( 'Y-m-01', $start_date );

        if($count_month == 13)
            $to_date = date( 'Y-m-d', strtotime($fy_end_date) );
        else
            $to_date = date( 'Y-m-t', $start_date );


        $month_year = date("F, Y", strtotime($from_date));
        $month = date("F", strtotime($from_date));
        $month_short = date("M", strtotime($from_date));
        $month_year_short = date("M, y", strtotime($from_date));

        $final[] = [
            'from_date'     => $from_date,
            'to_date'       => $to_date,
            'month'         => $month,
            'month_year'    => $month_year,
            'month_short'         => $month_short,
            'month_year_short'    => $month_year_short,
        ];

        $start_date = strtotime('+1 month', $start_date);
        $count_month++;
    }
     
    
    return (object)[
        'months'        => $final,
        'total_year'    => $total_year,
        'name'          => $fy_name,
        'from_date'     => $fy_bgn_date,
        'to_date'       => $fy_end_date,
    ];
}

function fy_calender_js()
{
    $session = \Config\Services::session();

    if($session->get('ses_comp_fy_id')){
        $fy_bgn_date = $session->get('ses_company_fy_beginning') ?? date('Y-01-01');
        $fy_end_date = $session->get('ses_company_fy_end') ?? date('Y-12-31');
    }
    else{
        $fy_bgn_date = date('Y-01-01');
        $fy_end_date = date('Y-12-31');
    }

    $start_date     = strtotime($fy_bgn_date);
    $end_date       = strtotime($fy_end_date);

    $fy_from_date = date('d-m-Y', $start_date);
    $fy_to_date = date('d-m-Y', $end_date);

    $fy_from_day   = date("d", $start_date);
    $fy_from_month = date("m", $start_date);
    $fy_from_year  = date("Y", $start_date);

    $fy_to_day   = date("d", $end_date);
    $fy_to_month = date("m", $end_date);
    $fy_to_year  = date("Y", $end_date);

    $start_year = date('Y', $start_date);
    $end_year = date('Y', $end_date);

    $total_months = 12;
    if(date('d', $start_date) != '01')
        $total_months = 13;

    $total_year = 1;
    if(date('m-d', $start_date) != '01-01')
        $total_year = 2;

    $count_month = 1;

    $months = [];
    while( $count_month <= $total_months ) {
        if($count_month == 1)
            $from_date = date( 'Y-m-d', strtotime($fy_bgn_date) );
        else
            $from_date = date( 'Y-m-01', $start_date );


        $label = date("M", strtotime($from_date));
        $month = date("m", strtotime($from_date));
        $year  = date("Y", strtotime($from_date));

        $months[] = [
            'label'     => strtoupper($label),
            'month'     => intval($month),
            'year'      => intval($year),
        ];

        $start_date = strtotime('+1 month', $start_date);
        $count_month++;
    }

    $start_date     = strtotime($fy_bgn_date);
    $end_date       = strtotime($fy_end_date);

    $quarters = [];
    $count_quarter = 1;;
    while( $start_date <= $end_date ) {

        $label = 'Q'.$count_quarter;

        $from_day   = date("d", $start_date);
        $from_month = date("m", $start_date);
        $from_year  = date("Y", $start_date);
        $from_date  = date("Y-m-d", $start_date);

        $q_end_date = strtotime('+3 months -1 day', $start_date);
        $to_day   = date("d", $q_end_date);
        $to_month = date("m", $q_end_date);
        $to_year  = date("Y", $q_end_date);
        $to_date  = date("Y-m-d", $q_end_date);

        $quarters[] = [
            'label'     => $label,
            'from_day'  => $from_day,
            'from_month'=> $from_month,
            'from_year' => $from_year,
            'from_date' => $from_date,
            'to_day'    => $to_day,
            'to_month'  => $to_month,
            'to_year'   => $to_year,
            'to_date'   => $to_date,
        ];

        $start_date = strtotime('+3 months', $start_date);
        $count_quarter++;
    }

    $start_date     = strtotime($fy_bgn_date);
    $end_date       = strtotime($fy_end_date);

    $half_years = [];
    $hyear = 1;
    while( $start_date <= $end_date ) {

        $label = 'H'.$hyear;

        $from_day   = date("d", $start_date);
        $from_month = date("m", $start_date);
        $from_year  = date("Y", $start_date);
        $from_date  = date("Y-m-d", $start_date);

        $q_end_date = strtotime('+6 months -1 day', $start_date);
        $to_day   = date("d", $q_end_date);
        $to_month = date("m", $q_end_date);
        $to_year  = date("Y", $q_end_date);
        $to_date  = date("Y-m-d", $q_end_date);

        $half_years[] = [
            'label'     => $label,
            'from_day'  => $from_day,
            'from_month'=> $from_month,
            'from_year' => $from_year,
            'from_date' => $from_date,
            'to_day'    => $to_day,
            'to_month'  => $to_month,
            'to_year'   => $to_year,
            'to_date'   => $to_date,
        ];

        $start_date = strtotime('+6 months', $start_date);
        $hyear++;
    }

    return [
        'from_date'     => $fy_from_date,
        'from_day'      => $fy_from_day,
        'from_month'    => $fy_from_month,
        'from_year'     => $fy_from_year,
        'to_date'       => $fy_to_date,
        'to_year'       => $fy_to_year,
        'to_day'        => $fy_to_day,
        'to_month'      => $fy_to_month,
        
        'total_months'  => $total_months,
        'total_year'    => $total_year,
        'start_year'    => intval($start_year),
        'end_year'      => intval($end_year),
        'months'        => $months,
        'quarters'      => $quarters,
        'half_years'    => $half_years,
    ];
}

function get_fy_end_date($date)
{
    return date('Y-m-d', strtotime("+1 year -1 day", strtotime($date)));

    // delete it
    $arr = explode('-', $date);

    $day    = intval($arr[0]);
    $month  = intval($arr[1]);
    $year   = intval($arr[2]);

    if($day == 1){
        if($month == 1){
            $end_year = $year;
            $end_month = 12;
            $end_day = 31;
        }
        else{
            $end_year = $year + 1;
            $end_month = $month - 1;

            $end_month_ = $end_month < 10 ? '0'.$end_month : $end_month;
            $end_day = date('t', strtotime(date('01-'.$end_month_.'-'.$end_year)));
        }
    }
    else{
        $end_year = $year + 1;
        $end_month = $month;
        $end_day = $day - 1;
    }
  
    $end_month = $end_month < 10 ? '0'.$end_month : $end_month;
    $end_day = $end_day < 10 ? '0'.$end_day : $end_day;
    $end_date = $end_day . '-' . $end_month . '-' . $end_year;

    return $end_date;
}


function getIndianCurrency(float $number)
{
	$number  = parseAmount($number); 
    $decimal = parseAmountPrice(round($number - ($no = floor($number)), 2) * 100,4);
    
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');

    $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? (($decimal == 0) ?'and ' : null ) : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $Rupees = trim($Rupees);

    $paise = '';
    if($decimal > 0){
        $Rupees = $Rupees != '' ? $Rupees . ' and ' : '';
        $paise = 'Paise ';

        if($decimal < 10){
            $paise .= $words[$decimal * 10];
        }
        else{
            $paise .= ($words[parseAmountPrice(floor($decimal / 10) * 10,4)] . " " . $words[parseAmountPrice($decimal % 10,4)]);
        }
    }
    
    return ($Rupees != '' ? 'Rupees ' .$Rupees : '') . $paise . ' Only';
}

function randomString()
{
    $length = 50;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}


function history_backdd(){
	 return 'javascript:history.go(-1)';
     /* $links_sub_array = array();
     $session 	         = \Config\Services::session();	
     $curl = current_url();
     $prev_page_url  ='';
     $links_history =  $session->get('visted_links_list');
     if($links_history){
         foreach($links_history as $kk => $linkrows){
              foreach($linkrows as $key => $link_url){
                  
                  $links_sub_array[]=$link_url;
               
                 }
         }
         
     }

    foreach($links_sub_array as $kk => $link_url){
         if($curl==$link_url){
             if(isset($links_sub_array[$kk-1])){
				 $prev_page_url =$links_sub_array[$kk-1]; 
				 if (strpos($prev_page_url, "sales/edit") !== false) {
					$prev_page_url ='1'.$links_sub_array[$kk-2]; 
				 }
				 else if (strpos($prev_page_url, "purchase/edit") !== false) {
					$prev_page_url ='2'.$links_sub_array[$kk-2];  
				 }else{
					$prev_page_url ='3'.$links_sub_array[$kk-1]; 
				 }
               
				
			 }
                }
    } 
     
    
   return $prev_page_url;*/
    
}
function merge_querystring($url = null,$query = null,$recursive = false){
  // $url = 'https://www.google.com?q=apple&type=keyword';
  // $query = '?q=banana';
  // if there's a URL missing or no query string, return
  if($url == null)
    return false;
  if($query == null)
    return $url;
  // split the url into it's components
  $url_components = parse_url($url);
  // if we have the query string but no query on the original url
  // just return the URL + query string
  if(empty($url_components['query']))
    return $url.'?'.ltrim($query,'?');
  // turn the url's query string into an array
  parse_str($url_components['query'],$original_query_string);
  // turn the query string into an array
  
  parse_str(parse_url($query,PHP_URL_QUERY),$merged_query_string);
  
  // merge the query string
  if ($recursive == true) {
    $merged_result = array_filter(array_merge_recursive($original_query_string, $merged_query_string));
} else {
    $merged_result = array_filter(array_merge($original_query_string, $merged_query_string));
}

// Find the original query string in the URL and replace it with the new one
$new_url = str_replace($url_components['query'], http_build_query($merged_result), $url);

// If the last query string removed then remove ? from url 
if(substr($new_url, -1) == '?') {
   return rtrim($new_url,'?');
}
return $new_url;
}

function history_back(){
	
	 return 'javascript:gotoback();';
     $links_sub_array = array();
     $session   = \Config\Services::session();	
     $s_ctab    = $session->get('s_ctab');
    
      $curl      = current_url(true);
     
     $prev_page_url  ='';
    
     $links_history  =  $session->get('visted_links_list');
    
     if($links_history){
     $active_tab_links = $links_history[$s_ctab];
     
    $row_index_val=0;
     foreach($active_tab_links as $kk => $link_url){
         if($link_url){
			    	
                 if($curl==$link_url){
                       $current_key = array_search($link_url, $active_tab_links);                       
                       if(isset($active_tab_links[$current_key-1])){
					      if(strpos($active_tab_links[$current_key-1], "/edit/") !== false) {
							$prev_page_url =$active_tab_links[$current_key-2];
							
					       }						
					      else{
						    $prev_page_url  = $active_tab_links[$current_key-1];
							 
						  }
					     }           
                     }
           
             
            }         
         }
     }
	 
	 


	
	 return $prev_page_url;  
}
 function CreateFyMasterEntry($mst_fy_id,$mst_type,$insert_data){
	$model = new \App\Models\Admin\TransactionModel();
	$model->CreateFyMasterEntry($mst_fy_id,$mst_type,$insert_data);
 } 
 
 function GetPrintLabelVal($usr_config_id,$label_id,$vch_series_id,$fldvalue=''){
	$model = new \App\Models\Admin\TransactionModel();
	$prntconfig_style = $model->GetPrintLabelVal($usr_config_id,$label_id,$vch_series_id,$fldvalue);
	return $prntconfig_style; 
}


function auth()
{
    $model = new \App\Models\CommonModel();
    $user = $model->auth();

    $object = new stdClass();
    $object->id = 0;
    $object->fname = '';
    $object->lname = '';
    $object->name = '';
    $object->email = '';
    $object->phone = '';

    if($user) {
        $object->id = $user['uuid'];
        $object->fname = $user['user_firstname'];
        $object->lname = $user['user_lastname'];
        $object->name = $user['user_firstname'] . ' ' . $user['user_lastname'];
        $object->email = $user['user_regdemail'];
        $object->phone = $user['user_regdmobile'];
    }

    return $object;
}

function settings()
{
    $object = new stdClass();
    $object->currency = '&#8377;';

    return $object;
}

function clean($string) {
  if($string == null)
    return '';

   $string = strip_tags($string); // remove html or php tags
   $string = preg_replace('/\s+/S', " ", $string); // tab, next line to space

   $string = preg_replace('/[^A-Za-z0-9 ~!@#$%^&*_\-+=:\[\]|:,.\/\?()\']/', '', $string); 
   //special chars \,<,>,`,;,",{,} not allowed

   $string = html_entity_decode($string, ENT_QUOTES);
   /*
      & is converted to &amp;
      " is converted to &quot;(already removed)
      ' is converted to &#039; 
      < is converted to &lt; (already removed)
      > is converted to &gt;(already removed)
   */

   $string = trim($string); // remove space from start end
   return $string; 
}


function parseAmountDigits($num, $digits): float
{
    // Guard: non-numeric input
    if (!is_numeric($num)) {
        return 0.0;
    }

    // Guard: $digits must be a positive integer (truncation needs at least 1 digit)
    // Default to 4 (matches usage in codebase for item price/qty)
    $digits = (is_numeric($digits) && (int)$digits > 0) ? (int)$digits : 4;

    // Cast to float first — normalises scientific notation (e.g. "1.5e-3" → 0.0015)
    // and resolves any existing IEEE noise to a standard float
    $num = (float) $num;

    // Normalize -0.0 → 0.0 (IEEE 754 edge case)
    if ($num == 0.0) {
        return 0.0;
    }

    // Truncate (NOT round) to $digits decimal places using bcmath
    // bcsub trick: bcadd with $digits scale simply truncates — no rounding occurs
    // This preserves the original intent of the function: "to avoid rounding off"
    $sign = ($num < 0) ? '-' : '';

    // Work on absolute value string with enough precision to avoid noise
    $absStr = number_format(abs($num), $digits + 6, '.', '');

    // Split on decimal point
    $parts = explode('.', $absStr);
    $intPart = $parts[0];
    $decPart = isset($parts[1]) ? substr($parts[1], 0, $digits) : str_repeat('0', $digits);

    // Pad decimal part if shorter than $digits (e.g. input "5.1", digits=4 → "5.1000")
    $decPart = str_pad($decPart, $digits, '0', STR_PAD_RIGHT);

    // Rebuild the clean truncated string
    $truncated = $sign . $intPart . '.' . $decPart;

    // Final (float) cast is safe here: $truncated is a canonical decimal string
    // with exactly $digits decimal places — no trailing binary noise
    return (float) $truncated;
}

function parseAmountPrice($num, $decimal): float
{
    // Guard: non-numeric input → return 0.0 (consistent float type)
    if (!is_numeric($num)) {
        return 0.0;
    }

    // Guard: ensure $decimal is a valid non-negative integer, default 2
    $decimal = (is_numeric($decimal) && (int)$decimal >= 0) ? (int)$decimal : 2;

    // Cast to float (handles string inputs e.g. "99.9999")
    $num = (float) $num;

    // Round to requested decimal places with explicit HALF_UP mode (GST standard)
    $num = round($num, $decimal, PHP_ROUND_HALF_UP);

    // Normalize IEEE 754 -0.0 edge case → +0.0
    if ($num == 0.0) {
        return 0.0;
    }

    // ✅ Return directly — DO NOT wrap in floatval()
    // floatval(round($x, $d)) re-introduces binary representation noise.
    // round() already returns a float; no further casting needed.
    return $num;
}

function parseAmount($num): float
{
    if (!is_numeric($num)) {
        return 0.0;
    }

    // Cast to float first to handle string inputs like "123.456"
    $num = (float) $num;

    // Round to 2 decimal places using PHP_ROUND_HALF_UP (GST standard)
    $num = round($num, 2, PHP_ROUND_HALF_UP);

    // Normalize -0.0 → 0.0 (IEEE 754 edge case)
    if ($num == 0.0) {
        return 0.0;
    }

    // Return as float — callers expect float, not string
    // Note: float cannot be "exact" in binary, but round() + PHP_ROUND_HALF_UP
    // is the maximum precision achievable without bcmath.
    return $num;
}
function parseAmount_old($num){
    if(!is_numeric($num)){
        return 0;
    }
    if($num == '-0'){
        return 0;
    }

    $num = round($num, 2);
    
    $num = floatval($num);

    return $num;
}


function formatAmount($num, $symbol = true,$decimalval='') {
	if($decimalval!='')
    $num = parseAmountPrice($num,$decimalval);
    else
	$num = parseAmount($num);	

    $sign = '';
    if($num < 0){
        $sign = '-';
        $num = abs($num);
    }

    $num = strval($num);
    
    $final = '0.00'; // 0.12, 0.1
    if(strpos($num, '.'))
    {
        $arr = explode(".",$num);
        $num1 = $arr[0];
        $num2 = $arr[1];
        
        $explrestunits = "" ;
        if(strlen($num1)>3) {
            $lastthree = substr($num1, strlen($num1)-3, strlen($num1));
            $restunits = substr($num1, 0, strlen($num1)-3);
            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; 
            $expunit = str_split($restunits, 2);
            for($i=0; $i<sizeof($expunit); $i++) {

                if($i==0) {
                    $explrestunits .= (int)$expunit[$i].","; 
                } else {
                    $explrestunits .= $expunit[$i].",";
                }
            }
            $final1 = $explrestunits.$lastthree;
        } else {
            $final1 = $num1;
        }
        
        if(strlen($num2) == 1){
            $final2 = $num2 . '0';
        }
        else{
            $final2 = $num2;
        }
        
        $final = $final1 . '.' . $final2;
    }
    else
    {
        $explrestunits = "" ;
        if(strlen($num)>3) {
            $lastthree = substr($num, strlen($num)-3, strlen($num));
            $restunits = substr($num, 0, strlen($num)-3);
            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; 
            $expunit = str_split($restunits, 2);
            for($i=0; $i<sizeof($expunit); $i++) {

                if($i==0) {
                    $explrestunits .= (int)$expunit[$i].","; 
                } else {
                    $explrestunits .= $expunit[$i].",";
                }
            }
            $final = $explrestunits.$lastthree.'.00';
        } else {
            $final = $num.'.00';
        }
    }
    
    $amount = $sign . $final;

    if($symbol)
        return '&#8377;' . $amount;
    else
        return trim($amount);
}



function parseValue($num){
    if(!is_numeric($num)){
        return 0;
    }
    if($num == '-0'){
        return 0;
    }

    $num = round($num, 4);
    
    $num = floatval($num);

    return $num;
}


function formatValue($num, $symbol = true) {
    $num = parseValue($num);

    $sign = '';
    if($num < 0){
        $sign = '-';
        $num = abs($num);
    }

    $num = strval($num);
    
    $final = '0.0000'; // 0.12, 0.1
    if(strpos($num, '.'))
    {
        $arr = explode(".",$num);
        $num1 = $arr[0];
        $num2 = $arr[1];
        
        $explrestunits = "" ;
        if(strlen($num1)>3) {
            $lastthree = substr($num1, strlen($num1)-3, strlen($num1));
            $restunits = substr($num1, 0, strlen($num1)-3);
            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; 
            $expunit = str_split($restunits, 2);
            for($i=0; $i<sizeof($expunit); $i++) {

                if($i==0) {
                    $explrestunits .= (int)$expunit[$i].","; 
                } else {
                    $explrestunits .= $expunit[$i].",";
                }
            }
            $final1 = $explrestunits.$lastthree;
        } else {
            $final1 = $num1;
        }
        
        if(strlen($num2) == 1){
            $final2 = $num2 . '0';
        }
        else if(strlen($num2) == 2){
            $final2 = $num2 . '00';
        }
        else if(strlen($num2) == 3){
            $final2 = $num2 . '000';
        }
        else{
            $final2 = $num2;
        }
        
        $final = $final1 . '.' . $final2;
    }
    else
    {
        $explrestunits = "" ;
        if(strlen($num)>3) {
            $lastthree = substr($num, strlen($num)-3, strlen($num));
            $restunits = substr($num, 0, strlen($num)-3);
            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; 
            $expunit = str_split($restunits, 2);
            for($i=0; $i<sizeof($expunit); $i++) {

                if($i==0) {
                    $explrestunits .= (int)$expunit[$i].","; 
                } else {
                    $explrestunits .= $expunit[$i].",";
                }
            }
            $final = $explrestunits.$lastthree.'.0000';
        } else {
            $final = $num.'.0000';
        }
    }
    
    $amount = $sign . $final;

    if($symbol)
        return '&#8377;' . $amount;
    else
        return trim($amount);
}

 function create_cpanel_db($db_name){  // not in use
        $cpanelusername = "aicountlyin";
        $cpanelpassword = "QRLFJ-PXAJp#";  
        $domain         = 'aicountly.in';
           
        $query = "https://$domain:2083/json-api/cpanel?cpanel_jsonapi_module=Mysql&cpanel_jsonapi_func=adddb&cpanel_jsonapi_apiversion=1&arg-0=".$db_name."";
        
        $curl = curl_init();                                // Create Curl Object
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);       // Allow self-signed certs
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);       // Allow certs that do not match the hostname
        curl_setopt($curl, CURLOPT_HEADER,0);               // Do not include header in output
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);       // Return contents of transfer on curl_exec
        $header[0] = "Authorization: Basic " . base64_encode($cpanelusername.":".$cpanelpassword) . "\n\r";
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);    // set the username and password
        curl_setopt($curl, CURLOPT_URL, $query);            // execute the query
        $result = curl_exec($curl);
        if ($result == false) {
            error_log("curl_exec threw error \"" . curl_error($curl) . "\" for $query");   
                                                            // log error if curl exec fails
        }
        curl_close($curl);
      
 }
 
function getPrevKey($needle, $currencies) {
   $filtered = array_filter($currencies, function ($haystack) use ($needle) {
    // return in_array($needle, $haystack);
    return isset(array_flip($haystack)[$needle]); // more efficient than in_array
});

$countriesArray = array_keys($filtered); // ['EUR']
return $countriesArray;
}
 
  function arrayfrstval($array){
    $final_array=array();
    foreach($array as $key => $val)
       {
           if($key!=''){
             $final_array[$key]= $val;
               
           }
       } 
    
    return array_key_first($final_array);
  }

 function erp_compcode_format($comp_code){
    return  'AIC'.sprintf( '%07d', $comp_code );
  }

  function grp_compcode_format($comp_id){
    return  'grp'.sprintf( '%07d', $comp_id );
  }
  
 function obfuscate_link($file_id, $type = 'documents')
		{
		$temp = array(
			date("jmY") ,
			$file_id,
			$type
		); // using date("jmY") ensures download links are specific to each day
		$temp = serialize($temp);
		$temp = base64_encode($temp);
		$link = rawurlencode($temp);
		return $link;
		}
	
    
 function unobfuscate_link($link)
		{
		$temp = rawurldecode($link);
		$temp = base64_decode($temp);
		if (!@unserialize($temp))
			{
			return "0";
			}
		  else
			{
			$download_array = unserialize($temp);
			return $download_array;
			}
		}		
		
 function CompanyUnits(){
	 $model = new \App\Models\CommonModel();
	 $unitsarray     = $model->get_common_comp_values('companyunits');
	 
    
     return $unitsarray;
 }		

 function GstTypes(){
     
	$master_type_list        = array();    
	$master_type_list['']    = ''; 
    $master_type_list['1']   = 'GST Taxpayer'; 
    $master_type_list['2']   = 'TDS (GST)'; 
    $master_type_list['3']   = 'TCS (GST)'; 
    $master_type_list['4']   = 'TDS (IT)';
	$master_type_list['5']   = 'TCS (IT)';
	$master_type_list['99']  = 'Other Taxes';
	
    return $master_type_list; 
   }
   
 function AccountItcEllig(){
	 $model = new \App\Models\CommonModel();
	 $master_type_list     =  $model->get_common_comp_values('accountItcellig');	 
   
    return $master_type_list; 
   }
 
  function AccountRcmNature(){
	$model = new \App\Models\CommonModel();
	$master_type_list     =  $model->get_common_comp_values('accountrcmnature');
	 
    return $master_type_list; 
  }
  
   function billsundry_nature(){
	$model = new \App\Models\CommonModel();
	$nature_type_list     =  $model->get_common_comp_values('billsundry_nature');	
   
    return $nature_type_list;   
   }
  
 function AccountGroupTypes(){
	$model = new \App\Models\CommonModel();
	$master_type_list     =  $model->get_master_groups();//get_common_comp_values('accountgrouptypes');	

    return $master_type_list; 
  }
  
  function AccountGroupPrimary(){
	$model = new \App\Models\CommonModel();
	$master_type_list     =  $model->get_master_groups();
	
    return $master_type_list; 
  }
   
   function AccountGroupMainList(){
	$model = new \App\Models\CommonModel();
	$master_type_list     =  $model->get_common_comp_values('accountgroupmainlist');
	
    return $master_type_list; 
  }
 
 function CompanyConstitutions(){
	$model = new \App\Models\CommonModel();
	$master_type_list     =  $model->get_common_comp_values('companyconstitutions');
	
    return $master_type_list; 
  }
  
  function GSTTaxCategory(){
	
	$master_type_list        = array();    
	$master_type_list['']    = ''; 
    $master_type_list['1']   = 'GST Taxpayer'; 
    $master_type_list['2']   = 'TDS (GST)'; 
    $master_type_list['3']   = 'TCS (GST)'; 
    $master_type_list['4']   = 'TDS (IT)';
	$master_type_list['5']   = 'TCS (IT)';
	$master_type_list['99']  = 'Other Taxes';
	
    return $master_type_list; 
  }
  
 function industry_type_list(){
	 $model = new \App\Models\CommonModel();
	 $industry_types     = $model->get_common_comp_values('industry_type_list');
	    
    return $industry_types;
   }
   
  function VoucherMasterTypes(){
	$model = new \App\Models\CommonModel();
	$master_type_list = $model->load_common_vouchers(); 
    
    return $master_type_list; 
 }
 
 
 function voucherarrayfind($array,$search_keys,$default){
     $final_list = array();
     $final_list[''] = 'Choose';
     
     if(isset($default['key']) && $default['key']!='0')
     $final_list[$default['key']] = $default['value'];
     if($array){
         foreach($array as $key => $row){
             if(in_array($key,$search_keys)){
                 $final_list[$key]=$row;
             }
         }
     }
     return $final_list;
 }
 
 function VoucherMasterShortNames(){
	$model = new \App\Models\CommonModel();
	$master_type_list = $model->load_common_vouchers();
    
    return $master_type_list; 
 }
 
 function DealerTypes(){
	 $model = new \App\Models\CommonModel();
	 $dealer_type_list     = $model->get_common_comp_values('dealertypes');
	 
    return $dealer_type_list; 
 }
 
  function natureof_work_lists(){
	 $model = new \App\Models\CommonModel();
	 $natureof_work_lists     = $model->get_common_comp_values('natureof_work_lists');
	 
    return $natureof_work_lists;      
  }
  
 
 function material_centres_grp_array()
    { 
	 $model = new \App\Models\CommonModel();
	 $grp_array     = $model->get_common_comp_values('material_centres_main_grp');
	 
       return $grp_array;
    }
    
  
 function user_type_array()
    { 
       $user_type_array = array();
       $user_type_array['']='';
        $user_type_array['A'] ='Admin';
       $user_type_array['C'] ='Client';
       $user_type_array['O'] ='Company';
       return $user_type_array;
    } 
	
 function price_format( $price ) {
  if(trim($price)!=='' && $price!='0')
  {
    $price =  round($price,2);
    $price = '$'.number_format($price,2,".",",");
    
    return $price;
  }
  else
   {
     return "";
   }
  
}

function months_list(){
    $all_months = array();
    $all_months[''] ='Choose Month';
    for ($m=1; $m<=12; $m++) {
     $month  = date('F', mktime(0,0,0,$m, 1, date('Y')));
     
     if($m<=9)
     $show_m = '0'.$m;
     else
     $show_m = $m;
     $all_months[$show_m] = $month;
     }
   return $all_months; 
}


function AmountInWords(float $amount)
{
   $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
   // Check if there is any number after decimal
   $amt_hundred = null;
   $count_length = strlen($num);
   $x = 0;
   $string = array();
   $change_words = array(0 => '', 1 => 'One', 2 => 'Two',
     3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
     7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
     10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
     13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
     16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
     19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
     40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
     70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while( $x < $count_length ) {
      $get_divider = ($x == 2) ? 10 : 100;
      $amount = floor($num % $get_divider);
      $num = floor($num / $get_divider);
      $x += $get_divider == 10 ? 1 : 2;
      if ($amount) {
       $add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
       $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
       $string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
       '.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
       '.$here_digits[$counter].$add_plural.' '.$amt_hundred;
        }
   else $string[] = null;
   }
   
   $implode_to_Rupees = implode('', array_reverse($string));
   $get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
   " . $change_words[$amount_after_decimal % 10]) . ' Paise' : '';
   return ($implode_to_Rupees ? $implode_to_Rupees . 'Rupees ' : '') . $get_paise;
}


if(!function_exists('randomPassword')) {
      //generates a random password of length minimum 8 
//contains at least one lower case letter, one upper case letter,
// one number and one special character, 
//not including ambiguous characters like iIl|1 0oO 
    function randomPassword($len = 8) {

        //enforce min length 8
        if($len < 8)
            $len = 8;
        
        //define character libraries - remove ambiguous characters like iIl|1 0oO
        $sets     = array();
        $sets[]   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $sets[]   = 'abcdefghjkmnpqrstuvwxyz';
        $sets[]   = '23456789';
        $sets[]   = '~!@#$%^&*(){}[],./?';
        $password = '';
        
        //append a character from each set - gets first 4 characters
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
           }

        //use all characters to fill up to $len
        while(strlen($password) < $len) {
            //get a random set
            $randomSet = $sets[array_rand($sets)];
            
            //add a random char from the random set
            $password .= $randomSet[array_rand(str_split($randomSet))]; 
        }
        
        //shuffle the password string before returning!
        return str_shuffle($password);
    }
    
    
    
function dateDiff($entry_time, $today)
    {
    $date1 = new DateTime($entry_time);
    $date2 = new DateTime($today);
    $interval = $date1->diff($date2);
    if ($interval->y > 0) $post_time = $interval->y." years";
    elseif ($interval->m > 0) $post_time = $interval->m." months";
    elseif ($interval->d > 0) $post_time = $interval->d." days";
//    elseif ($interval->h > 0) $post_time = $interval->h." hours ago";
//    elseif ($interval->i > 0) $post_time = $interval->i." minutes ago";
//    elseif ($interval->s > 0) $post_time = $interval->s." seconds ago";
    else $post_time = "just now";
    if($interval->m >0 && $interval->d >0)
    return $interval->m." months ,".$interval->d." days";
    else if($interval->m >0)
       return $interval->m." months ";
    else if($interval->d >0)
    return $interval->d." days";   
    }
    
    function qualification_array()
    { 
	   $model = new \App\Models\CommonModel();
	   $qualification_array = $model->get_common_comp_values('qualifications');
	   /*
       $qualification_array = array();
       $qualification_array['']='';
       $qualification_array['1'] ='Business Owner';
       $qualification_array['2'] ='Business Manager';
       $qualification_array['3'] ='Business Employees';
       $qualification_array['4'] ='Chartered Accountant';
       $qualification_array['5'] ='Business Accountant';
       $qualification_array['6'] ='Company Secretary';
       $qualification_array['7'] ='Advocate';
       $qualification_array['8'] ='Professional\'s Staff';
       $qualification_array['9'] ='Other Professional';*/
       return $qualification_array;
    }
    
    function getRandomString() {
        $n = 10;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
     
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
     
        return $randomString;
    }
    
    function bills_status_list(){
	   $model = new \App\Models\CommonModel();
	   $bills_status_list     = $model->get_common_comp_values('bills_status_list');
	 
        /* $bills_status_list = array();
        $bills_status_list['']  = '';
        $bills_status_list['1'] = 'Pending';
        $bills_status_list['2'] = 'Cleared';
        $bills_status_list['3'] = 'Overdue'; */
        return $bills_status_list;      
    }
    function bills_method_list(){
		$model = new \App\Models\CommonModel();
	    $bills_method_list     = $model->get_common_comp_values('bills_method_list');
	   
     /*  $bills_method_list = array();
        $bills_method_list[0] = '';
        $bills_method_list[1] = 'New Ref.';
        $bills_method_list[2] = 'Adjustment'; */
        return $bills_method_list;      
    }
	function fetchDivContentByClass($url, $className) {
    // Initialize cURL session
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);  // Disable SSL verification (use true in production)
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0'); // Optional: set user-agent
    
    // Get the page content
    $pageContent = curl_exec($ch);
    
    // Check for errors
    if ($pageContent === false) {
        echo "Error fetching page: " . curl_error($ch);
        curl_close($ch);
        return;
    }
    
    // Close cURL session
    curl_close($ch);
    
    // Parse the HTML content using DOMDocument
    $dom = new DOMDocument();
    
    // Suppress warnings due to malformed HTML (common with real-world pages)
    @$dom->loadHTML($pageContent);
    
    // Find all <div> tags in the document
    $divs = $dom->getElementsByTagName('div');
    
    // Initialize an array to hold the content of matching <div> elements
    $matchingContent = [];
    
    // Iterate through the <div> tags and check for the class
    foreach ($divs as $div) {
        if ($div->hasAttribute('class') && in_array($className, explode(' ', $div->getAttribute('class')))) {
            // Store the full HTML of the matching <div>, including styles and nested HTML
            $matchingContent[] = $dom->saveHTML($div);  // saveHTML() returns the entire <div> HTML
        }
    }
    
    // Return the matching content
    return $matchingContent;
   }

}


if (!function_exists('getCommandMappings')) {
    function getCommandMappings()
    {
        $filePath = WRITEPATH . 'commands.json';
        
        if (!file_exists($filePath)) {
            return [];  // Safeguard if file missing
        }

        $jsonContent = file_get_contents($filePath);
       $commands = json_decode($jsonContent, true);



if (!is_array($commands)) {
    return []; 
}


$commandNameMap = [];
foreach ($commands as $cmd) {
    $commandNameMap[$cmd['erp_cmd_id']] = $cmd['erp_cmd']; 
}


$mapped = [];

foreach ($commands as $cmd) {
    $parentId = $cmd['erp_cmd_parent'];
    $parentName = $commandNameMap[$parentId] ?? null;

    $mapped[$cmd['erp_cmd']] = [
        'id'     => $cmd['erp_cmd_id'],
        'cmd'    => $cmd['erp_cmd'],
        'name'   => $cmd['erp_rpt_name'],
        'alias'  => $cmd['erp_rpt_alias'],
        'url'    => $cmd['erp_cmd_url'],
        'parent' => $cmd['erp_cmd_parent'],
        'parent_name' => $parentName,  
        'token'  => $cmd['erpotparpv_id'],
        'priority'  => $cmd['erp_cmd_priority'],
    ];

    if (!empty($cmd['crs_name'])) {
        $mapped[$cmd['erp_cmd']]['crs_name'] = $cmd['crs_name'];
    }
}


        return $mapped;
    }
}

function command_line_json($file_data)
{
    try {
        // Encode to JSON
        $json_data = json_encode($file_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Define file path
        $file_name = 'commands.json';
        $dir_path  = WRITEPATH;
        $file      = $dir_path . $file_name;

        // Ensure directory exists
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0755, true);
        }

        // Write JSON data (overwrite mode)
        if (file_put_contents($file, $json_data) === false) {
            throw new \Exception("Failed to write JSON to file: {$file}");
        }

    } catch (\Throwable $e) {
        log_message('error', 'Json failed to write data: ' . $e->getMessage());
    }
}