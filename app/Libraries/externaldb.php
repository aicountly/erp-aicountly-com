<?php
namespace App\Libraries;
class externaldb {
	public $session;
	function __construct(){			
		helper(['custom']);
		$this->session  = \Config\Services::session();						
	}
 function sispluuid_db(){
        $config['DSN']      = '';
		$config['hostname'] = "127.0.0.1";
		$config['username'] = "aicountlyin_usispl_uuid";
		$config['password'] = "(M*.rIln}g6s";
		$config['database'] = "aicountlyin_sispl_uuid";
		$config['DBDriver'] = 'MySQLi';
		$config['DBPrefix'] =  '';
		$config['pConnect'] = false;
		$config['DBDebug']  = (ENVIRONMENT !== 'production');
		$config['charset']  = 'utf8';
		$config['DBCollat'] = 'utf8_general_ci';
		$config['swapPre']  = '';
		$config['encrypt']  = true;
		$config['compress'] = true;
		$config['strictOn'] = false;
		$config['failover'] = [];
		$config['port']     = getenv("DB_PORT");    
		$comp_db = \Config\Database::connect($config);
		return $comp_db;
    }
	
	
		
 function uuid_pg_db() {
    $config['DSN']      = '';
    $config['hostname'] = '127.0.0.200';
    $config['username'] = 'erpaicountly_univaictlypg_drop';
    $config['password'] = 'y,QJ!fBei&5*b(rJ';
    $config['database'] = 'erpaicountly_univaictlydbpg_drop';
    $config['DBDriver'] = 'Postgre'; // Change from MySQLi to Postgre
    $config['DBPrefix'] = '';
    $config['pConnect'] = false;
    $config['DBDebug']  = (ENVIRONMENT !== 'production');
    $config['charset']  = 'utf8';
    $config['DBCollat'] = 'utf8_general_ci'; // Optional for PostgreSQL
    $config['swapPre']  = '';
    $config['encrypt']  = false; // Encryption not typically used for PostgreSQL
    $config['compress'] = false; // Compression not used in PostgreSQL
    $config['strictOn'] = false;
    $config['failover'] = [];
    $config['port']     = 5432; // Default PostgreSQL port

    $comp_db = \Config\Database::connect($config);
    return $comp_db;
}


	
 function myaicountly_pg_db() {
    $config['DSN']      = '';
    $config['hostname'] = '127.0.0.200';
    $config['username'] = 'myaicountly_cmpaictlyuser';
    $config['password'] = 'y,QJ!fBei&5*b(rJ';
    $config['database'] = 'myaicountly_cmpaictly';
    $config['DBDriver'] = 'Postgre'; // Change from MySQLi to Postgre
    $config['DBPrefix'] = '';
    $config['pConnect'] = false;
    $config['DBDebug']  = (ENVIRONMENT !== 'production');
    $config['charset']  = 'utf8';
    $config['DBCollat'] = 'utf8_general_ci'; // Optional for PostgreSQL
    $config['swapPre']  = '';
    $config['encrypt']  = false; // Encryption not typically used for PostgreSQL
    $config['compress'] = false; // Compression not used in PostgreSQL
    $config['strictOn'] = false;
    $config['failover'] = [];
    $config['port']     = 5432; // Default PostgreSQL port

    $comp_db = \Config\Database::connect($config);
    return $comp_db;
}


 function postgr_db() {
    $config['DSN']      = '';
    $config['hostname'] = '127.0.0.200';
    $config['username'] = 'booksaicountly_booksglobalpgdb_user';
    $config['password'] = '36-y9.a5C@dXeMu6';
    $config['database'] = 'booksaicountly_booksglobalpgdb';
    $config['DBDriver'] = 'Postgre'; // Change from MySQLi to Postgre
    $config['DBPrefix'] = '';
    $config['pConnect'] = false;
    $config['DBDebug']  = (ENVIRONMENT !== 'production');
    $config['charset']  = 'utf8';
    $config['DBCollat'] = 'utf8_general_ci'; // Optional for PostgreSQL
    $config['swapPre']  = '';
    $config['encrypt']  = false; // Encryption not typically used for PostgreSQL
    $config['compress'] = false; // Compression not used in PostgreSQL
    $config['strictOn'] = false;
    $config['failover'] = [];
    $config['port']     = 5432; // Default PostgreSQL port

    $comp_db = \Config\Database::connect($config);
    return $comp_db;
}

function postgr_cron_db() {
    $config['DSN']      = '';
    $config['hostname'] = '127.0.0.200';
    $config['username'] = 'booksaicountly_booksitmsnapsht_user';
    $config['password'] = 'G6#nu_jnbke3f{hS';
    $config['database'] = 'booksaicountly_booksitmsnapsht';
    $config['DBDriver'] = 'Postgre'; // Change from MySQLi to Postgre
    $config['DBPrefix'] = '';
    $config['pConnect'] = false;
    $config['DBDebug']  = (ENVIRONMENT !== 'production');
    $config['charset']  = 'utf8';
    $config['DBCollat'] = 'utf8_general_ci'; // Optional for PostgreSQL
    $config['swapPre']  = '';
    $config['encrypt']  = false; // Encryption not typically used for PostgreSQL
    $config['compress'] = false; // Compression not used in PostgreSQL
    $config['strictOn'] = false;
    $config['failover'] = [];
    $config['port']     = 5432; // Default PostgreSQL port

    $comp_db = \Config\Database::connect($config);
    return $comp_db;
}


function postgr_univaictlydb_old() { // to save auth token & session key 
    $config['DSN']      = '';
    $config['hostname'] = '127.0.0.200';
    $config['username'] = 'erpaicountly_univaictlypg_drop';
    $config['password'] = '0JDl#8sK(Mz*(EGc';
    $config['database'] = 'erpaicountly_univaictlydbpg_drop';
    $config['DBDriver'] = 'Postgre'; // Change from MySQLi to Postgre
    $config['DBPrefix'] = '';
    $config['pConnect'] = false;
    $config['DBDebug']  = (ENVIRONMENT !== 'production');
    $config['charset']  = 'utf8';
    $config['DBCollat'] = 'utf8_general_ci'; // Optional for PostgreSQL
    $config['swapPre']  = '';
    $config['encrypt']  = false; // Encryption not typically used for PostgreSQL
    $config['compress'] = false; // Compression not used in PostgreSQL
    $config['strictOn'] = false;
    $config['failover'] = [];
    $config['port']     = 5432; // Default PostgreSQL port

    $comp_db = \Config\Database::connect($config);
    return $comp_db;
}


function postgr_univaictlydb() { // to save auth token & session key 
    $config['DSN']      = '';
    $config['hostname'] = '127.0.0.200';
    $config['username'] = 'myaicountly_authaictlyuser';
    $config['password'] = '0JDl#8sK(Mz*(EGc';
    $config['database'] = 'myaicountly_authaictly';
    $config['DBDriver'] = 'Postgre'; // Change from MySQLi to Postgre
    $config['DBPrefix'] = '';
    $config['pConnect'] = false;
    $config['DBDebug']  = (ENVIRONMENT !== 'production');
    $config['charset']  = 'utf8';
    $config['DBCollat'] = 'utf8_general_ci'; // Optional for PostgreSQL
    $config['swapPre']  = '';
    $config['encrypt']  = false; // Encryption not typically used for PostgreSQL
    $config['compress'] = false; // Compression not used in PostgreSQL
    $config['strictOn'] = false;
    $config['failover'] = [];
    $config['port']     = 5432; // Default PostgreSQL port

    $comp_db = \Config\Database::connect($config);
    return $comp_db;
}

	function contactaic_db(){ 
		$config['DSN']      = '';
		$config['hostname'] ="127.0.0.1";
		$config['username'] = "aicountlyin_contactaic";
		$config['password'] = ".f^+nzJ(B4{A";
		$config['database'] = "aicountlyin_contactaic";
		$config['DBDriver'] = 'MySQLi';
		$config['DBPrefix'] =  '';
		$config['pConnect'] = false;
		$config['DBDebug']  = (ENVIRONMENT !== 'production');
		$config['charset']  ='utf8';
		$config['DBCollat'] = 'utf8_general_ci';
		$config['swapPre']  = '';
		$config['encrypt']  = true;
		$config['compress'] = true;
		$config['strictOn'] = false;
		$config['failover'] = [];
		$config['port']     = getenv("DB_PORT");    
		$comp_db = \Config\Database::connect($config);
		return $comp_db;
	}
	function univaictly_db_drop(){ // Universal DB Aicountly univaictly
		$config['DSN']      = '';
		$config['hostname'] ="127.0.0.1";
		$config['username'] = "erpaicountly_univaictly";
		$config['password'] = "o+5lGr&]mie~Q]6w";
		$config['database'] = "erpaicountly_univaictly_drop";
		$config['DBDriver'] = 'MySQLi';
		$config['DBPrefix'] =  '';
		$config['pConnect'] = false;
		$config['DBDebug']  = (ENVIRONMENT !== 'production');
		$config['charset']  ='utf8';
		$config['DBCollat'] = 'utf8_general_ci';
		$config['swapPre']  = '';
		$config['encrypt']  = true;
		$config['compress'] = true;
		$config['strictOn'] = false;
		$config['failover'] = [];
		$config['port']     = getenv("DB_PORT");    
		$comp_db = \Config\Database::connect($config);
		return $comp_db;
	}
	
	function univaictly_db(){ // Universal DB Aicountly univaictly
		$config['DSN']      = '';
		$config['hostname'] ="127.0.0.200";
		$config['username'] = "myaicountly_cmpaictlyuser";
		$config['password'] = "y,QJ!fBei&5*b(rJ";
		$config['database'] = "myaicountly_cmpaictly";
		$config['DBDriver'] = 'Postgre';
		$config['DBPrefix'] =  '';
		$config['pConnect'] = false;
		$config['DBDebug']  = (ENVIRONMENT !== 'production');
		$config['charset']  ='utf8';
		$config['DBCollat'] = 'utf8_general_ci';
		$config['swapPre']  = '';
		$config['encrypt']  = true;
		$config['compress'] = true;
		$config['strictOn'] = false;
		$config['failover'] = [];
		$config['port']     = 5432;    
		$comp_db = \Config\Database::connect($config);
		return $comp_db;
	}
	
	function univerpaic_db(){ // Universal DB Aicountly univerpaic
		$config['DSN']      = '';
		$config['hostname'] ="127.0.0.1";
		$config['username'] = "erpaicountly_univerpaic";
		$config['password'] = "8~{cVC87[iS41^-*";
		$config['database'] = "erpaicountly_univerpaic_erp3o_old";
		$config['DBDriver'] = 'MySQLi';
		$config['DBPrefix'] =  '';
		$config['pConnect'] = false;
		$config['DBDebug']  = (ENVIRONMENT !== 'production');
		$config['charset']  ='utf8';
		$config['DBCollat'] = 'utf8_general_ci';
		$config['swapPre']  = '';
		$config['encrypt']  = true;
		$config['compress'] = true;
		$config['strictOn'] = false;
		$config['failover'] = [];
		$config['port']     = getenv("DB_PORT");    
		$comp_db = \Config\Database::connect($config);
		return $comp_db;
	}
	
	
	function postgr_myaicountlydb() { // to save auth token & session key 
    $config['DSN']      = '';
    $config['hostname'] = '127.0.0.200';
    $config['username'] = 'myaicountly_uuidaictlyuser';
    $config['password'] = 'y,QJ!fBei&5*b(rJ';
    $config['database'] = 'myaicountly_uuidaictly';
    $config['DBDriver'] = 'Postgre'; // Change from MySQLi to Postgre
    $config['DBPrefix'] = '';
    $config['pConnect'] = false;
    $config['DBDebug']  = (ENVIRONMENT !== 'production');
    $config['charset']  = 'utf8';
    $config['DBCollat'] = 'utf8_general_ci'; // Optional for PostgreSQL
    $config['swapPre']  = '';
    $config['encrypt']  = false; // Encryption not typically used for PostgreSQL
    $config['compress'] = false; // Compression not used in PostgreSQL
    $config['strictOn'] = false;
    $config['failover'] = [];
    $config['port']     = 5432; // Default PostgreSQL port

    $comp_db = \Config\Database::connect($config);
    return $comp_db;
}

	
	function aicountly_db(){ // default
		$config['DSN']      = '';
		$config['hostname'] ="127.0.0.1";
		$config['username'] = "aicountlyin_uaicountly";
		$config['password'] = "3DLVvk,).2o=";
		$config['database'] = "aicountlyin_aicountly_erp1o_3o_old";
		$config['DBDriver'] = 'MySQLi';
		$config['DBPrefix'] =  '';
		$config['pConnect'] = false;
		$config['DBDebug']  = (ENVIRONMENT !== 'production');
		$config['charset']  ='utf8';
		$config['DBCollat'] = 'utf8_general_ci';
		$config['swapPre']  = '';
		$config['encrypt']  = true;
		$config['compress'] = true;
		$config['strictOn'] = false;
		$config['failover'] = [];
		$config['port']     = getenv("DB_PORT");    
		$comp_db = \Config\Database::connect($config);
		return $comp_db;
	}
	function erp_db(){
		$config['DSN']      = '';
		$config['hostname'] = "127.0.0.1";
		$config['username'] = "aicountlyin_uerp";
		$config['password'] = "=7,%Yc}r#FXU";
		$config['database'] = "aicountlyin_erp_erp1o_3o_old";
		$config['DBDriver'] = 'MySQLi';
		$config['DBPrefix'] =  '';
		$config['pConnect'] = false;
		$config['DBDebug']  = (ENVIRONMENT !== 'production');
		$config['charset']  = 'utf8';
		$config['DBCollat'] = 'utf8_general_ci';
		$config['swapPre']  = '';
		$config['encrypt']  = true;
		$config['compress'] = true;
		$config['strictOn'] = false;
		$config['failover'] = [];
		$config['port']     = getenv("DB_PORT");    
		$comp_db = \Config\Database::connect($config);
		return $comp_db;
	}
	function profsindia_db(){
		$config['DSN']      = '';
		$config['hostname'] = "127.0.0.1";
		$config['username'] = "aicountlyin_uprofsindia";
		$config['password'] = "a)nLf@4djPpn";
		$config['database'] = "aicountlyin_profsindia";
		$config['DBDriver'] = 'MySQLi';
		$config['DBPrefix'] =  '';
		$config['pConnect'] = false;
		$config['DBDebug']  = (ENVIRONMENT !== 'production');
		$config['charset']  = 'utf8';
		$config['DBCollat'] = 'utf8_general_ci';
		$config['swapPre']  = '';
		$config['encrypt']  = true;
		$config['compress'] = true;
		$config['strictOn'] = false;
		$config['failover'] = [];
		$config['port']     = getenv("DB_PORT");    
		$comp_db = \Config\Database::connect($config);
		return $comp_db;
	}
	function sispl_admin_db(){
		$config['DSN']      = '';
		$config['hostname'] = "127.0.0.1";
		$config['username'] = "aicountlyin_usispl_admin";
		$config['password'] = "7w*SPWISLRq2";
		$config['database'] = "aicountlyin_sispl_admin";
		$config['DBDriver'] = 'MySQLi';
		$config['DBPrefix'] =  '';
		$config['pConnect'] = false;
		$config['DBDebug']  = (ENVIRONMENT !== 'production');
		$config['charset']  = 'utf8';
		$config['DBCollat'] = 'utf8_general_ci';
		$config['swapPre']  = '';
		$config['encrypt']  = true;
		$config['compress'] = true;
		$config['strictOn'] = false;
		$config['failover'] = [];
		$config['port']     = getenv("DB_PORT");    
		$comp_db = \Config\Database::connect($config);
		return $comp_db;
	}
	function sispl_super_db(){
		$config['DSN']      = '';
		$config['hostname'] = "127.0.0.1";
		$config['username'] = "aicountlyin_usispl_super";
		$config['password'] = "k}I8Y0ONI355";
		$config['database'] = "aicountlyin_sispl_super";
		$config['DBDriver'] = 'MySQLi';
		$config['DBPrefix'] =  '';
		$config['pConnect'] = false;
		$config['DBDebug']  = (ENVIRONMENT !== 'production');
		$config['charset']  = 'utf8';
		$config['DBCollat'] = 'utf8_general_ci';
		$config['swapPre']  = '';
		$config['encrypt']  = true;
		$config['compress'] = true;
		$config['strictOn'] = false;
		$config['failover'] = [];
		$config['port']     = getenv("DB_PORT");    
		$comp_db = \Config\Database::connect($config);
		return $comp_db;
	}
	function sispl_uuid_db(){
		$config['DSN']      = '';
		$config['hostname'] = "127.0.0.1";
		$config['username'] = "aicountlyin_usispl_uuid";
		$config['password'] = "(M*.rIln}g6s";
		$config['database'] = "aicountlyin_sispl_uuid";
		$config['DBDriver'] = 'MySQLi';
		$config['DBPrefix'] =  '';
		$config['pConnect'] = false;
		$config['DBDebug']  = (ENVIRONMENT !== 'production');
		$config['charset']  = 'utf8';
		$config['DBCollat'] = 'utf8_general_ci';
		$config['swapPre']  = '';
		$config['encrypt']  = true;
		$config['compress'] = true;
		$config['strictOn'] = false;
		$config['failover'] = [];
		$config['port']     = getenv("DB_PORT");    
		$comp_db = \Config\Database::connect($config);
		return $comp_db;
	}

	function comp_db($comp_id){	
		
		$comp_code = erp_compcode_format($comp_id);
		$database = strtolower($comp_code);

		$username =  getenv("DB_PREFIX_NAME").'u'.$database;
		$password =  getenv("DB_USR_PASSWORD");

		$config_app1['DSN']      = '';
		$config_app1['hostname'] ="127.0.0.1";
		$config_app1['username'] = $username;
		$config_app1['password'] = $password;
		$config_app1['database'] = getenv("DB_PREFIX_NAME").$database;
		$config_app1['DBDriver'] = 'MySQLi';
		$config_app1['DBPrefix'] =  '';
		$config_app1['pConnect'] = false;
		$config_app1['DBDebug']  = (ENVIRONMENT !== 'production');
		$config_app1['charset']  ='utf8';
		$config_app1['DBCollat'] = 'utf8_general_ci';
		$config_app1['swapPre']  = '';
		$config_app1['encrypt']  = true;
		$config_app1['compress'] = true;
		$config_app1['strictOn'] = false;
		$config_app1['failover'] = [];
		$config_app1['port']     = 3306;       
		
		$external_comp_db = \Config\Database::connect($config_app1);
		return $external_comp_db;
		
	}

	function company_db($dbname,$username,$upasswd){	

		$companydb_name         =  strtolower($dbname);
		$companydb_userpassword = $upasswd;

		$config_app1['DSN']      = '';
		$config_app1['hostname'] = "127.0.0.1";
		$config_app1['username'] = $username;
		$config_app1['password'] = $companydb_userpassword;
		$config_app1['database'] = $companydb_name;
		$config_app1['DBDriver'] = 'MySQLi';
		$config_app1['DBPrefix'] =  '';
		$config_app1['pConnect'] = false;
		$config_app1['DBDebug']  = (ENVIRONMENT !== 'production');
		$config_app1['charset']  ='utf8';
		$config_app1['DBCollat'] = 'utf8_general_ci';
		$config_app1['swapPre']  = '';
		$config_app1['encrypt']  = true;
		$config_app1['compress'] = true;
		$config_app1['strictOn'] = false;
		$config_app1['failover'] = [];
		$config_app1['port']     = 3306;       

		$external_comp_db = \Config\Database::connect($config_app1);
		return $external_comp_db;

	} 	 
	function get_shivansherp_db(){
		$config_app2['DSN']      = '';
		$config_app2['hostname'] ="127.0.0.1";
		$config_app2['username'] = "shivanshispl_erp_shivansh";
		$config_app2['password'] = "Ric(N]i;Tch_";
		$config_app2['database'] = "shivanshispl_shivansherp";
		$config_app2['DBDriver'] = 'MySQLi';
		$config_app2['DBPrefix'] =  '';
		$config_app2['pConnect'] = false;
		$config_app2['DBDebug']  = (ENVIRONMENT !== 'production');
		$config_app2['charset']  ='utf8';
		$config_app2['DBCollat'] = 'utf8_general_ci';
		$config_app2['swapPre']  = '';
		$config_app2['encrypt']  = true;
		$config_app2['compress'] = true;
		$config_app2['strictOn'] = false;
		$config_app2['failover'] = [];
		$config_app2['port']     = getenv("DB_PORT");    
		$comp_db = \Config\Database::connect($config_app2);
		return $comp_db;
	}

	function get_company_db(){			 
		if ($this->session->get('ses_company_id') && $this->session->get('ses_compl_company_code'))
		{
			$companydb_name         =  strtolower($this->session->get('ses_compl_company_code'));
			$companydb_username     =  getenv("DB_PREFIX_NAME").'u'.$companydb_name;
			$companydb_userpassword =  getenv("DB_USR_PASSWORD");

			$config_app2['DSN']      = '';
			$config_app2['hostname'] ="127.0.0.1";
			$config_app2['username'] = $companydb_username;
			$config_app2['password'] = $companydb_userpassword;
			$config_app2['database'] = getenv("DB_PREFIX_NAME").$companydb_name;
			$config_app2['DBDriver'] = 'MySQLi';
			$config_app2['DBPrefix'] =  '';
			$config_app2['pConnect'] = false;
			$config_app2['DBDebug']  = (ENVIRONMENT !== 'production');
			$config_app2['charset']  ='utf8';
			$config_app2['DBCollat'] = 'utf8_general_ci';
			$config_app2['swapPre']  = '';
			$config_app2['encrypt']  = true;
			$config_app2['compress'] = true;
			$config_app2['strictOn'] = false;
			$config_app2['failover'] = [];
			$config_app2['port']     = getenv("DB_PORT");  

			$comp_db = \Config\Database::connect($config_app2);
			return $comp_db;
		}			
	}

	function single_company_db($comp_code){			 
		$companydb_name          =  getenv("DB_PREFIX_NAME").strtolower($comp_code);
		$companydb_username      =  getenv("DB_PREFIX_NAME").'u'.$comp_code;
		$companydb_userpassword  =  getenv("DB_USR_PASSWORD");
		$config_app2['DSN']      = '';
		$config_app2['hostname'] = "127.0.0.1";
		$config_app2['username'] = $companydb_username;
		$config_app2['password'] = $companydb_userpassword;
		$config_app2['database'] = $companydb_name;
		$config_app2['DBDriver'] = 'MySQLi';
		$config_app2['DBPrefix'] = '';
		$config_app2['pConnect'] = false;
		$config_app2['DBDebug']  = (ENVIRONMENT !== 'production');
		$config_app2['charset']  = 'utf8';
		$config_app2['DBCollat'] = 'utf8_general_ci';
		$config_app2['swapPre']  = '';
		$config_app2['encrypt']  = true;
		$config_app2['compress'] = true;
		$config_app2['strictOn'] = false;
		$config_app2['failover'] = [];
		$config_app2['port']     = getenv("DB_PORT");   

		$comp_db = \Config\Database::connect($config_app2);
		return $comp_db;
	}
	
	function connect_uuid_db($uuid){	
		$companydb_name          = 'aicountlyin_uuid_'.$uuid;	
		$username                = 'aicountlyin_uuid_'.$uuid.'_usr';	
		$companydb_userpassword  = getenv("DB_USR_PASSWORD");
		$config_app1['DSN']      = '';
		$config_app1['hostname'] = "127.0.0.1";
		$config_app1['username'] = $username;
		$config_app1['password'] = $companydb_userpassword;
		$config_app1['database'] = $companydb_name;
		$config_app1['DBDriver'] = 'MySQLi';
		$config_app1['DBPrefix'] = '';
		$config_app1['pConnect'] = false;
		$config_app1['DBDebug']  = (ENVIRONMENT !== 'production');
		$config_app1['charset']  = 'utf8';
		$config_app1['DBCollat'] = 'utf8_general_ci';
		$config_app1['swapPre']  = '';
		$config_app1['encrypt']  = true;
		$config_app1['compress'] = true;
		$config_app1['strictOn'] = false;
		$config_app1['failover'] = [];
		$config_app1['port']     = 3306;  
		$external_uuid_db        = \Config\Database::connect($config_app1);

		return $external_uuid_db;
	}
	
	function connect_universal_uuid_db(){	
		$companydb_name          = 'aicountlyin_uuid_erpaic';	
		$username                = 'aicountlyin_uuid_erpaic';	
        $companydb_userpassword  = 'Ric(N]i;Tch_';
        $config_app1['DSN']      = '';
		$config_app1['hostname'] = "127.0.0.1";
		$config_app1['username'] = $username;
		$config_app1['password'] = $companydb_userpassword;
		$config_app1['database'] = $companydb_name;
		$config_app1['DBDriver'] = 'MySQLi';
		$config_app1['DBPrefix'] = '';
		$config_app1['pConnect'] = false;
		$config_app1['DBDebug']  = (ENVIRONMENT !== 'production');
		$config_app1['charset']  = 'utf8';
		$config_app1['DBCollat'] = 'utf8_general_ci';
		$config_app1['swapPre']  = '';
		$config_app1['encrypt']  = true;
		$config_app1['compress'] = true;
		$config_app1['strictOn'] = false;
		$config_app1['failover'] = [];
		$config_app1['port']     = 3306;  
		$external_uuid_db        = \Config\Database::connect($config_app1);
		
		return $external_uuid_db;
   }
   
	function grp_comp_db(){
		if($this->session->get('grp_comp_id')){
			$comp_code = $this->session->get('grp_comp_code');
			$companydb_name          =  getenv("DB_PREFIX_NAME").strtolower($comp_code);
			$companydb_username      =  getenv("DB_PREFIX_NAME").'u'.$comp_code;
			$companydb_userpassword  =  getenv("DB_USR_PASSWORD");
			$config_app2['DSN']      = '';
			$config_app2['hostname'] = "127.0.0.1";
			$config_app2['username'] = $companydb_username;
			$config_app2['password'] = $companydb_userpassword;
			$config_app2['database'] = $companydb_name;
			$config_app2['DBDriver'] = 'MySQLi';
			$config_app2['DBPrefix'] = '';
			$config_app2['pConnect'] = false;
			$config_app2['DBDebug']  = (ENVIRONMENT !== 'production');
			$config_app2['charset']  = 'utf8';
			$config_app2['DBCollat'] = 'utf8_general_ci';
			$config_app2['swapPre']  = '';
			$config_app2['encrypt']  = true;
			$config_app2['compress'] = true;
			$config_app2['strictOn'] = false;
			$config_app2['failover'] = [];
			$config_app2['port']     = getenv("DB_PORT");   

			$comp_db = \Config\Database::connect($config_app2);
			return $comp_db;
		}
	}	
}