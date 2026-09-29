<?php
namespace App\Libraries;

class message_output {
	var $success       = array();
	var $error         = array();
    public $session;
 function __construct(){			
		$this->session  = \Config\Services::session();	
		if($this->session->getFlashdata('_error')) $this->session->getFlashdata('_error');
		if($this->session->getFlashdata('_success')) $this->session->getFlashdata('_success');				
	}	

	function set_error($msg){		
		$this->session->setFlashdata('_error', $msg);		
	}
	
	function set_success($msg){
		$this->session->setFlashdata('_success', $msg);	
		
	}
	
	function run(){
		
			$success = $this->session->getFlashdata('_success');
			$error   = $this->session->getFlashdata('_error');
	
		if($success!=''){
			?>
			<div class="alert-success-custom">
			<i class="bi bi-check-circle-fill"></i>
			<div>
			  <strong>Success!</strong> <?php echo $success; ?>.
			</div>
			<button type="button" class="btn-close" aria-label="Close"></button>
		  </div>

			<?php			
		}
		if($error!=''){
			?>
		 <div class="alert-error-custom">
			<i class="bi bi-x-circle-fill"></i>
			<div>
			<strong>Error!</strong> <?php echo $error; ?>.
			</div>
			<button type="button" class="btn-close" aria-label="Close"></button>
		</div>
		 
			<?php	
		}
	}

}