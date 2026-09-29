<?php
    $local_session     = \Config\Services::session(); 
    
	if($local_session->get('ses_company_id')!=''){
	  $user_id           = $local_session->get('ses_company_id');	
      $ses_company_code  = $local_session->get('ses_compl_company_code');
      $s_name            = ucwords($local_session->get('ses_company_print_name'));   
      $comp_fy_year      = $local_session->get('ses_company_fy_beginning'); 	  
      $folder_path       = getenv('AdminPath');
      $base_url          = base_url().'/'.$folder_path;
?>
<div class="header header-one">
   <div class="header-left active">
      <a href="<?php echo $base_url;?>" class="logo logo-normal">
      
      </a>
      <a href="<?php echo $base_url;?>" class="logo logo-white">
   
      </a>
      <a href="<?php echo $base_url;?>" class="logo-small">
     
      </a>
   </div>
   <a id="mobile_btn" class="mobile_btn" href="#sidebar">
   <span class="bar-icon">
   <span></span>
   <span></span>
   <span></span>
   </span>
   </a>
   <ul class="nav user-menu">
      <li class="nav-item dropdown has-arrow main-drop">
	   <h6><?php echo $s_name;?>(<?php echo $ses_company_code;?>)</h6>&nbsp; F.Y.&nbsp;&nbsp;<?php echo $comp_fy_year;?>
      </li>
   </ul>
</div> 
	<?php } ?>