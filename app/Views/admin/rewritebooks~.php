<?php $header = array( 	'title' => 'Rewrite Books' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
 .myform label{width:25%; float:left;}
 .myform .form-control, .myform select {width:75%;}
</style>

<div class="row">
		     <div class="col-6">  <h3 class="pb-3">Rewrite Books</h3></div>
		     <div class="col-md-6 text-end"><div class="taskmenus">
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a>
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">CSV</a></li>
            <li><a class="dropdown-item" href="#">Excel</a></li>
            <li><a class="dropdown-item" href="#">Document</a></li>
          </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">share</span></a> 
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Facebook</a></li>
            <li><a class="dropdown-item" href="#">Twitter</a></li>
            <li><a class="dropdown-item" href="#">Instagram</a></li>
        </ul>
       
    </div>
    </div>
             
             <div class="col-6">
                <a href="javascript:void(0);" class="btn btn-success update_account_balances">Update Account Balances</a>
				<a href="javascript:void(0);" class="btn btn-danger close_account_balances" style="display:none;">Stop Process</a>
			<!--	<a href="javascript:void(0);" class="editbtn btn btn-success update_item_balances">Update Item Balances</a> -->
             </div>
             <div class="col-6 text-end">
			 <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-outline-success btn-sm">« Back</a>
			 </div> 
        </div>
	    
	<?php if ($session->getFlashdata('message')) { ?>
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php echo $session->getFlashdata('message'); ?>
            </div>
    <?php } ?>
    <?php if ($session->getFlashdata('error_array_message')) { ?>
            <div class="alert alert-danger alert-dismissible fade show mt-2" style="max-height: 150px;overflow-y: auto;">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php $errors = $session->getFlashdata('error_array_message'); ?>
                <ul>
                <?php foreach($errors as $error) { ?>
                    <li><?php echo $error ?></li>
                <?php } ?>
                </ul>
            </div>
    <?php } ?>
<br><br>

<div class="row">
<div class="col-md-6" id="total_accounts"></div>
<div class="col-md-6" id="account_working"></div>

</div>

<div class="row">
<div class="col-md-12" id="progrssbar">
 

</div>

</div>



<?php echo view('includes/footer_scripts'); ?>
<script src="<?php echo base_url();?>/public/js/jquery.progressBarTimer.js" type="text/javascript" charset="utf-8"></script>
<style>
.close_account_balances{display:none;}

#progress {
  --size: 0;
  --hsl: hsl(0, 100%, 50%);
  position: relative;
  overflow: hidden;
  width: 200px;
  height: 20px;
  border: 1px solid #888;
  background-color: var(--hsl);
  transition: background-color 1s;
}
#bar {
  width: 100%;
  height: 100%;
  background: white;
  transition: margin-left 1s;
  margin-left: calc(var(--size) * 100%);
}
</style>
<script>

$( document ).ready(function() {
	$(".close_account_balances").hide();
});
       var count = 0; 
	   
	   var subtasks;
	   $(".close_account_balances").on("click",function(event){
		   subtasks.abort();
		   
		    $(".close_account_balances").hide();
		    $(".update_account_balances").show();
			
			$("#total_accounts").html('');
			$("#account_working").html('');
		  $('#progrssbar').progressBarTimer({ animated: true }).stop();
		  $('#progrssbar').progressBarTimer({ animated: true }).reset();
	   });
	   
      $(".update_account_balances").on("click",function(event){
		 event.preventDefault();		 
		 $('#progrssbar').progressBarTimer({ animated: true,completeStyle: 'bg-success'}).start();
		 $(".close_account_balances").show();
		 $(".update_account_balances").hide();
		 
		 $.ajax({
			  type: "GET",
			  url: baseurl+"/admin/rewritebooks/load_accounts",
			  success: function(response) {
			     var parseresponse = $.parseJSON(response);
			     var accounts_list = parseresponse.data;			
			     var total_accounts = parseresponse.total;
				 var rewrite_books_id = parseresponse.rewrite_books_id;
			     $("#total_accounts").html("Total "+total_accounts+" account's found."); 			 
			     $("#account_working").html("working on accounts,please wait...");	
			     call_sub_accounts(count,accounts_list,rewrite_books_id);		  
     	       },						
			}); 
	  });
	  
	  function call_sub_accounts(count,accounts_list,rewrite_books_id){
		  if(count >= accounts_list.length){
			   $("#account_working").html('');
			   $("#total_accounts").html('');
			   $('#progrssbar').hide();
			   $('#progrssbar').progressBarTimer({ animated: true }).stop();
			   $('#progrssbar').progressBarTimer({ animated: true }).reset();
			   $(".close_account_balances").hide();
		       $(".update_account_balances").show();
			  return;}
				var _item = accounts_list[count];
				$("#account_working").html("working on account("+_item.acc_name+"),please wait...");
			    subtasks =  $.ajax({
				url: baseurl+"/admin/rewritebooks/update_account_balances/"+_item.acc_id+"/"+rewrite_books_id,							
				success: function(data) {
					 if(data=='done'){
						 $("#account_working").html("completed action of account("+_item.acc_name+")");
						_response(accounts_list,data,count,rewrite_books_id);
					 }
				   }
			  });  
	  }
	  
	  function _response(accounts_list,response,count,rewrite_books_id){
		       count++;
			   if(count >= accounts_list.length){
				   $('#progrssbar').hide();
				    $('#progrssbar').progressBarTimer({ animated: true }).stop();
					$('#progrssbar').progressBarTimer({ animated: true }).reset();
				    $("#account_working").html('');
					$("#total_accounts").html('');
				    $(".close_account_balances").hide();
		             $(".update_account_balances").show();
				   return;}
				var _item = accounts_list[count];				
				$("#account_working").html("working on account("+_item.acc_name+"),please wait...");
				subtasks =$.ajax({
				url: baseurl+"/admin/rewritebooks/update_account_balances/"+_item.acc_id+"/"+rewrite_books_id,					
				success: function(data) {
					 if(data=='done'){
					   $("#account_working").html("completed action of account("+_item.acc_name+")");	 
					  _response(accounts_list,data,count,rewrite_books_id);
					 }
				}
			  });
			}
			
	  </script>	</body></html>