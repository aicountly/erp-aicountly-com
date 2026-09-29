<?php $header = array('title' => 'Add Sales Invoice (Without Item)' ); ?>
<?php echo view('includes/header',$header); 
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));

if(!empty($billfrm_data)){
   $from_gstin     = '<p>'.$billfrm_data['hobo_gstin'].'</p>';
   $from_tradename ='<p>'.$billfrm_data['hobo_gstin_trade_name'] ?? $ses_company_name .'</p>';
   $from_statecode ='<p>'.$billfrm_data['hobo_gstin_state_code'].'</p>';
   $from_country   ='';//'<p>'.$billfrm_data['country'] ?? ''.'</p>';
   }
else{
	 $from_gstin     ='<p>N/A</p>';
	 $from_tradename ='<p>N/A</p>';
	 $from_statecode ='<p>N/A</p>';
	 $from_country   ='<p>N/A</p>';
   }    
?>
<style> 
    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }

    .pq-grid-cell.pq-side-icon > div:before {        
        width: 20px;
        height: 20px;
        color: #ccc;
        float: right;
    }

    .pq-grid-cell.pq-drop-icon > div:before {
        content: "▼";
    }

    .pq-grid-cell.pq-calendar > div:before {
        content: "\01F4C5";
    }
    .pq-select-search-input {
    padding: 1px 2px;
    border-width: 0;
    height:23px;
    }
    .pq-select-search-input {
        box-sizing: border-box;
        width: 100%;
        font-size: inherit;
    }
/*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
}
</style>
<div class="row pb-2">
  <div class="col-md-6 order-1"><h3>Sales Invoice (Without Item)</h3></div>  
  <div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
<a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>      
 <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>

<a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history"><span class="material-symbols-outlined">visibility</span></a>
      <a href="#"><span class="material-symbols-outlined">offline_bolt</span></a> 

    <a href="#"><span class="material-symbols-outlined">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">download</span></a>
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
    <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>  
 </div>
 </div> 

<div class="col-md-6 order-2 order-md-3">
<div class="col-md-6">  
    <div class="input-group">
	<select name="type" id="type" class="form-select d-inline-block" style="width:120px;" onchange="handleSaleRedirect(this.value)" disabled>
      <option value="item">With Item</option>
      <option value="non_item" selected="selected">Without Item</option>
    </select>	
    <select form="salefrm" name="currency_id" id="currency_id" class="form-select d-inline-block" style="width:120px;">
     <?php foreach ($currency_list as $value) { ?>
    <option 
        data-id="<?= $value['curr_symbol'] ?>" 
        value="<?= $value['comp_currency_id'] ?>"
        <?= ($value['curr_symbol'] == '₹') ? 'selected' : '' ?>
    >
        <?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)
    </option>
	<?php } ?>
    </select>
	<div id="fcratespopup" title="Add Forex Rates" alt="Add Forex Rates" class="btn btn-sm btn-success">#</div>
	</div>
	
   </div>
    <div class="form-check d-inline-block me-2">
    <input class="form-check-input" type="checkbox" value="1" id="bbbCheck">
    <label class="form-check-label" for="bbbCheck">Bill By Bill</label>
   </div>
   <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" value="1" id="prCheck">
      <label class="form-check-label" for="prCheck">Project Reporting</label>
    </div>
	
	 <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" value="1" form="salefrm" name="taxInclusive" id="taxInclusive">
      <label class="form-check-label" for="taxInclusive">Tax Inclusive</label>
    </div>
	
	
	 <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" value="1" form="salefrm" name="memoCheck" id="memoCheck">
      <label class="form-check-label" for="memoCheck">Memorandum</label>
    </div>

    <div class="form-check d-inline-block me-2">
      <input class="form-check-input" type="checkbox" form="salefrm" value="1" name="EINVOICECheck" id="EINVOICECheck">
      <label class="form-check-label" for="EINVOICECheck">GENERATE E-INVOICE</label>
    </div>
   
  </div>  

<div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
    <a href="#" class="btn btn-success dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
    <ul class="dropdown-menu">
    <li>
        <a class="dropdown-item" id="applytax_calculate" href="javascript:void(0);" data-type="acc">Apply Tax</a>
      </li> 
  </ul>  
  <button class="btn btn-success m-1" type="button">Templates</button>
  <a href="<?php echo history_back();?>" class="btn btn-outline-success showinline-md">Back</a>
  </div> 
</div>

<div id="validation_errors"></div>
<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
 <div class="col-12">
  <div class="row m-0 p-0 align-items-center">
      
   <div class="col-md-3 col-6 card p-2">
    <div class="input-group">
     <span class="input-group-text">Series:</span>
	  <select name="voucher_series" id="voucher_series" class="voucher_series form-select required" required>
			<?php			
			foreach($voucher_series as $vch_series_info){ ?>
               <option id="<?php echo $vch_series_info['vch_series_id']; ?>" data-srsmethod="<?php echo $vch_series_info['vch_series_method']; ?>"  value="<?php echo $vch_series_info['vch_series_id']; ?>"><?= $vch_series_info['vch_series_name'] ?></option>
            <?php } ?>
		  </select>
    </div>
   </div>
   <div class="col-md-3 col-6 card p-2" id="shsupply_type" style="display:none;">
          <div class="input-group">
            <span class="input-group-text">Supply Type:</span>           
             <?php echo form_dropdown('sub_supply_type', [], '',' id="sub_supply_type" class="sale_type form-select required" required '); ?> 
           
          </div>
   </div>
   
   <div class="col-md-3 col-6 card p-2">
          <div class="input-group">
            <span class="input-group-text">EWB Supply Type:</span>
           
            <?php echo form_dropdown('supply_type', $supply_type_list, '',' id="supply_type" class="sale_type form-select required" required '); ?>
           
            <div data-code="0" id="taxpopup" title="Add Description" alt="Add Description" class="btn btn-sm btn-success SupplyDescModal">#</div>
          </div>
        </div>
       <div class="col-md-3 col-6 card p-2">
          <div class="input-group">
            <span class="input-group-text">EWB TXN Type:</span>
            <?php echo form_dropdown('tax_type_id', $tax_type_list, '',' id="tax_type_id" class="sale_type form-select required" required '); ?>           
            <div data-code="0" id="taxtypepopup" title="Add Description" alt="Add Description" class="btn btn-sm btn-success">#</div>
          </div>
        </div>	
        <?php if($bo_gstin_type==2){ ?>
		  <div class="col-md-3 col-6 card p-2">
          <div class="input-group">
            <span class="input-group-text">CMP Supply Type:</span>
            <?php echo form_dropdown('cmp_suply_type', $comp_supply_types_dropdown, $sess_cmp_suply_type,' id="cmp_suply_type" class="sale_type form-select" '); ?>           
           
          </div>
        </div>
	   <?php } ?>
  	
   <div class="col-md-2 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Date:</label> 
           <input type="text"  id="voucher_date" name="voucher_date" value="<?php echo $voucher_date;?>" class="datepicker form-control form-control-sm">
       </div>
    </div>  
      
   <div class="col-md-2 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Voucher No:</label>
           <input type="text" name="voucher" id="voucher" class="form-control form-control-sm" value="<?php echo $voucher_auto_no;?>" disabled>
       </div>
   </div>
      
   <div class="col-md-2 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">ITC Eligibility:</label>
           <input type="text" id="itc_eligibility" name="itc_eligibility" class="form-control form-control-sm" disabled>
       </div>
   </div>
   <div class="col-md-2 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Bill No:</label>
		  <?php if($voucher_bill_no!=''){ ?>
		  <input type="text" name="billno" id="billno" value="<?php echo $voucher_bill_no;?>" class="form-control form-control-sm" disabled>
		  <?php } else{ ?>
          <input type="text" name="billno" id="billno" class="form-control form-control-sm">
		  <?php } ?>
        </div>
      </div>  
	  
   <div class="col-md-3 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Party:</label> 
          <input list="party_ids" id="clone_party_id" name="clone_party_id" class="form-control form-control-sm required" onmouseover="focus();" required>
		  <datalist id="party_ids">
		    <?php
			foreach($party_dropdown as $value){ ?>
               <option data-is_sez="<?php echo $value['is_sez']?>" data-grpid="<?php echo $value['grpid']?>" data-gstin="<?php echo $value['gstin']?>" id="<?= $value['acc_id']?>" data-statecode="<?= $value['state_code'] ?>" data-is_bbb="<?= $value['is_bbb'] ?>" value="<?= $value['acc_name'] ?>" >
            <?php } ?>
		  </datalist>
        </div>
    </div>
 <div class="col-md-2 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">POS:</label>
          <?php echo form_dropdown('pos', $states_lists, "",' id="pos" class="sale_type form-select required" '); ?>
        </div>
      </div>
    <?php if($bo_gstin_type==1){ ?>
	  <div class="col-md-2 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Reverse Charges:</label>         
          <?php 
          $revrchrgs_lists=array("0"=>"No","1"=>"Yes","2"=>"ECO");
          echo form_dropdown('reverse_charges', $revrchrgs_lists, "0",' id="reverse_charges" class="sale_type form-select" ');
		  ?>         
        </div>
      </div>
	
      <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Ecomerce Operator:</label>
          <?php echo form_dropdown('eco_id', $eco_dropdown, "",' id="eco_id" class="form-select" '); ?>
        </div>
      </div>
	  <?php } ?>
	 <div class="col-md-3 col-6 card p-2" id="party_gstin_label" style="display:none;">
      <table class="table-sm">
        <tbody>
          <tr>
            <td>Party GSTIN</td>
            <td class="party_gstin_name"></td>
          </tr>         
        </tbody>
      </table>
    </div>
	  <input type="hidden" name="invoice_type" id="invoice_type">
      <div class="col-md-12 col-12 card p-2">
          <div class="input-group">
              <label class="input-group-text">Narration:</label> 
            <textarea  name="narration" class="form-control form-control-sm"></textarea>
          </div>
      </div>
      <input type="hidden" name="hsndata" id="hsndata">
      <input type="hidden" name="itmsdata" id="itmsdata">
      <input type="hidden" name="bbbdata" id="bbbdata">
      <input type="hidden" name="prdata" id="prdata">
	   <input type="hidden" name="btnid" id="btnid">
      <input type="hidden" name="billsndrydata" id="billsndrydata">
	  <input type="hidden" name="sbnum" id="hidden_sbnum">
<input type="hidden" name="sbpcode" id="hidden_sbpcode">
<input type="hidden" name="sbdt" id="hidden_sbdt">
  </div>
 </div>

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
    <div class="col-12"><div class="row">
        <!-- Tax Summary -->
        <div class="col-md-8">
           <h4 class="text-center">Bill Sundry</h4>
           <div id="billsundry_search" style="margin:auto;"></div> 
         
        </div>
       <!-- Tax Summary Ends -->
      
       <!-- Bill Summary Starts --> 
       <div class="col-md-4">
          
           <div id="calsummary" style="margin:auto;">
		     <div class="row">
		     <div class="col-md-6"><br></div>
			 <div class="col-md-6"><br> </div>
		   </div>
		   <div class="row mb-3">
		     <div class="col-md-5" id="servicelabel"><label>Value Of Service</label></div>
			 <div class="col-md-7" id="servoicevalue"></div>
		   </div>
		  <div class="row mb-3">
		     <div class="col-md-5" id="billsundrylabel"><label>Bill Sundry</label></div>
			 <div class="col-md-7" id="billsundryvalue"></div>
		   </div>
		 <div class="row mb-3">
		     <div class="col-md-5" id="totalsupplylabel"><label>Total Supply Value</label></div>
			 <div class="col-md-7" id="totalsupplyvalue"></div>
		   </div>
		   <div class="row mb-3">
			<div id="taxContainer"></div>
		  </div>  		  
		 <div class="row mb-3">
		     <div class="col-md-5"><label>Total Invoice Value</label></div>
			 <div class="col-md-7" id="invoice_total"></div>
		   </div>	
		   </div> 
       </div>

    </div> 
	
     <div class="col-12 text-center">
         <br><br>
         
        <input type="button" id="submitbtn" class="btn btn-success btn-lg" value="SAVE" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
        <input type="button" id="submitbtn_drft" name="submitbtn_drft" value="SAVE AS DRAFT"   class="btn btn-lg btn-success mx-2">
		<button type="button" id="taxsummarybtn" class="btn btn-success btn-lg">TAXABLE SUMMARY</button>
        <a href="<?php echo history_back();?>"  class="btn btn-lg btn-secondary mx-2">QUIT</a>
        
    </div>
        
    </div>
	<select style="display:none;" name="party_id" id="party_id" class="form-select">
						<option data-grpid=""  data-is_bbb="0" value="">
						</option>			
					  </select>
                
                <div class="modal fade pt-5" id="view_fcrates_modal" tabindex="-1" aria-labelledby="proinfoLabel" data-keyboard="false" data-backdrop="static">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Forex Rates</h4>
                        <button type="button" class="btn-close clsoefcratemodal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					  <div class="form-row align-items-center">  
						<div class="col-sm-12 my-1">
						  <label class="sr-only" for="inlineFormInputGroupUsername">1 INR</label>
						  <div class="input-group">
							<div class="input-group-prepend">
							  <div class="input-group-text" id="selcyrlabel">@</div>
							</div>
							<input type="text" class="form-control" name="fcy_forex_rate" id="fcy_forex_rate" onkeydown="return validatefcrate(event,this.value);" required>
						  </div>
						</div>
						
					</div>
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-success" id="save_forex_rates">Save</button>
                      </div>
                    </div>
                  </div>
                </div>
                
                
             <div class="modal fade pt-5" id="add_reg_taxtype_modal" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Tax Type(REGULAR)</h4>
                        <button type="button" class="btn-close close_btn"  data-bs-dismiss="modal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					  <div class="row">
					   <div class="col-md-6">
					   <h5 class="pb-2">Bill From</h5>
					    <p class="col-12">
						 <label>FROM GSTIN</label>
					     <span class="billfrm_gstin"><?php echo $from_gstin;?></span>
                      </p>
					  <p class="col-12">
						 <label>FROM TRADE NAME</label>
					     <span class="billfrm_tradename"><?php echo $from_tradename;?></span>
                      </p> 
					   <p class="col-12">
						 <label>FROM STATE CODE</label>
					      <span class="billfrm_statecode"><?php echo $from_statecode;?></span>
                      </p>
					   <p class="col-12">
						 <label>FROM COUNTRY</label>
					      <span class="billfrm_country"><?php echo $from_country;?></span>
                      </p>
					  
					   </div>
					   <div class="col-md-6">
					    <h5 class="pb-2">Bill To</h5>
						  <p class="col-12">
						 <label>TO GSTIN</label>
					     <span class="billto_gstin"></span>
                      </p>
					  <p class="col-12">
						 <label>TO TRADE NAME</label>
					     <span class="billto_tradename"></span>
                      </p> 
					   <p class="col-12">
						 <label>TO STATE CODE</label>
					     <span class="billto_statecode"></span>
                      </p> 
					   <p class="col-12">
						 <label>TO COUNTRY</label>
					     <span class="billto_country"></span>
                      </p> 
					   </div>
					   
					  </div>
					 </div>
                     
                    </div>
                  </div>
                </div>
<div class="modal fade pt-5" id="add_billtoshipto_taxtype_modal" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Tax Type(BILL TO - SHIP TO)</h4>
                        <button type="button" class="btn-close close_btn"  data-bs-dismiss="modal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					  <div class="row">
					   <div class="col-md-3">
					   <h5 class="pb-2">Bill From</h5>
					     <p class="col-12">
						 <label>FROM GSTIN</label>
					     <span class="billfrm_gstin"><?php echo $from_gstin;?></span>
                      </p>
					  <p class="col-12">
						 <label>FROM TRADE NAME</label>
					     <span class="billfrm_tradename"><?php echo $from_tradename;?></span>
                      </p> 
					   <p class="col-12">
						 <label>FROM STATE CODE</label>
					      <span class="billfrm_statecode"><?php echo $from_statecode;?></span>
                      </p>
					  <p class="col-12">
						 <label>FROM COUNTRY</label>
					      <span class="billfrm_country"><?php echo $from_country;?></span>
                      </p>
					  
					   </div>
					   <div class="col-md-3">
					    <h5 class="pb-2">Bill To</h5>
						 <p class="col-12">
						 <label>TO GSTIN</label>
					     <span class="billto_gstin"></span>
                      </p>
					  <p class="col-12">
						 <label>TO TRADE NAME</label>
					     <span class="billto_tradename"></span>
                      </p> 
					   <p class="col-12">
						 <label>TO STATE CODE</label>
					     <span class="billto_statecode"></span>
                      </p> 
					  <p class="col-12">
						 <label>TO COUNTRY</label>
					     <span class="billto_country"></span>
                      </p>
					   </div>
					   <div class="col-md-6">
					    <h5 class="pb-2">Ship To</h5>
						<div class="row">
						   <div class="col-md-6"> <p class="col-12">
						 <label>TO GSTIN</label>
					     <input type="text" name="shipto2_gstin"  value="" maxlength="255" class="form-control" >
                      </p></div>
						    <div class="col-md-6">  <p class="col-12">
						 <label>TO LEGAL NAME</label>
					     <input type="text" name="shipto2_legalname" value="" maxlength="255" class="form-control">
                      </p> </div>
						</div>
						
						
						<div class="row">
						 <div class="col-md-6"> <p class="col-12">
						 <label>TO TRADE NAME</label>
					     <input type="text" name="shipto2_tradename"  value="" maxlength="255" class="form-control" >
                      </p></div>
					  
						   <div class="col-md-6"> <p class="col-12">
						 <label>TO ADDRESS 1</label>
					     <input type="text" name="shipto2_addr1"  value="" maxlength="255" class="form-control" >
                      </p></div>
						  
						</div>
						<div class="row">
						  <div class="col-md-6">  <p class="col-12">
						 <label>TO ADDRESS 2</label>
					     <input type="text" name="shipto2_addr2" value="" maxlength="255" class="form-control">
                      </p> </div>
						   <div class="col-md-6"><p class="col-12">
						 <label>TO PLACE</label>
					     <input type="text" name="shipto2_place" value="" maxlength="255" class="form-control">
                      </p> </div>
						
						  </div> 
					
					   
					   <div class="row">
					      <div class="col-md-6"><p class="col-12">
						 <label>TO PINCODE</label>
					     <input type="text" name="shipto2_pin" value="" maxlength="255" class="form-control" minlength="3" required="">
                      </p> </div>
						   <div class="col-md-6">
					   <p class="col-12">
						 <label>TO STATE CODE</label>
					      <?php echo form_dropdown('shipto2_state_code', $states_lists, "",' class="sale_type form-select" '); ?>
                      </p> 
					  </div></div>
					  
					   </div>
					   
					  </div>
					   
					  	
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
                      </div>
                    </div>
                  </div>
                </div>   
                
               
<div class="modal fade pt-5" id="view_exports_modal" tabindex="-1" aria-labelledby="proinfoLabel" data-keyboard="false" data-backdrop="static">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Export Options</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					  <div class="form-row align-items-center">  
						<div class="col-sm-12 my-1">
														  <div class="mb-3 row align-items-center">
								  <label class="col-sm-5 col-form-label">Shipping Bill Number</label>
								  <div class="col-sm-7">
									<input type="text" class="form-control" id="sbnum" name="sbnum">
								  </div>
								</div>

								<div class="mb-3 row align-items-center">
								  <label class="col-sm-5 col-form-label">Port Code</label>
								  <div class="col-sm-7">
									<input type="text" class="form-control" id="sbpcode"  name="sbpcode">
								  </div>
								</div>

								<div class="mb-3 row align-items-center">
								  <label class="col-sm-5 col-form-label">Shipping Bill Date</label>
								  <div class="col-sm-7">
									<input type="text" class="form-control datepicker" id="sbdt" name="sbdt">
								  </div>
								</div>
						</div>
						
					</div>
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-primary" id="saveExport">Save</button>
                      </div>
                    </div>
                  </div>
                </div>	
			   </form>


<!-- The Bill By Bill Modal -->
<div class="modal" id="billsModal" style="z-index: 9999">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">
    <!-- Modal Header -->
     <div class="modal-header d-flex justify-content-between align-items-center">
  <!-- Left: Title -->
  <div class="flex-grow-1 text-start">
    <h4 class="modal-title">
      <span id="bills_page">
      </span> Bill by Bill</h4>
  </div>

  <!-- Center: Warning -->
  <div class="flex-grow-1 text-center">
    <div class="text-warning fw-bold" id="bill_warning">
      <!-- Optional: Warning text goes here -->
    </div>
  </div>

  <!-- Right: Close Button -->
  <div class="flex-grow-1 text-end">
    <button type="button" class="btn-close" data-bs-dismiss="modal">
  </div>
</div>
    <!-- Modal body -->
    <div class="modal-body">
      <div class="row">
        <div class="col-md-6 text-center">
          <h4>Account: <span id="bills_account">
          </span>
          </h4>
        </div>
        <div class="col-md-6 text-center">
          <h4>Total: <span id="bills_total">
          </span>&nbsp;<span id="bills_drcr">
        </span>
        </h4>
        </div>
      </div>
      <div id="bill_by_bill_grid">
      </div>
      <button type="button" class="btn btn-success prevBtn" onclick="readyBillsModel('prev')">Previous</button>
      <button type="button" class="btn btn-success nextBtn" onclick="readyBillsModel('next')">Next</button>

      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="save_bill_by_bill">Save</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- The Project Reporting Modal -->
<div class="modal" id="prModal" style="z-index: 9999">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header d-flex justify-content-between align-items-center">
  <!-- Left: Title -->
  <div class="flex-grow-1 text-start">
    <h4 class="modal-title">
      <span id="pr_page">
      </span> Project Reporting</h4>
  </div>

  <!-- Center: Warning -->
  <div class="flex-grow-1 text-center">
    <div class="text-warning fw-bold" id="pr_warning">
      <!-- Optional: Warning text goes here -->
    </div>
  </div>

  <!-- Right: Close Button -->
  <div class="flex-grow-1 text-end">
    <button type="button" class="btn-close close_pr_data" data-bs-dismiss="modal">
  </div>
</div>
    <!-- Modal body -->
    <div class="modal-body">
      <div class="row">
        <div class="col-md-6 text-center">
          <h4>Account: <span id="pr_account">
          </span>
          </h4>
        </div>
        <div class="col-md-6 text-center">
          <h4>Total: <span id="pr_total">
          </span>&nbsp;<span id="pr_drcr">
          </span>
          </h4>
        </div>
      </div>
      <div id="pr_grid">
      </div>
      <button type="button" class="btn btn-success prevBtn" onclick="readyPrModel('prev')">Previous</button>
      <button type="button" class="btn btn-success nextBtn" onclick="readyPrModel('next')">Next</button>

    </div>
    <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="save_pr">Save</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- List View The Modal Starts here -->
<div class="modal" id="listviewModal">
  <div class="modal-dialog  modal-md">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">List View</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
<ol class="list-group list-group-flush">
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">List Name 1 goes here</div>
      Content for list item
    </div>
    <span class="badge bg-primary rounded-pill">14</span>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">List Name 2 goes here</div>
      Content for list item
    </div>
    <span class="badge bg-primary rounded-pill">14</span>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">3rd List name here</div>
      Content for list item
    </div>
    <span class="badge bg-primary rounded-pill">14</span>
  </li>
</ol>
 <p class="col-md-12 text-end mt-2"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button> </p>
      </div>
           

    </div>
  </div>
</div>

<!-- List View The Modal Ends Here -->

<?php echo view('includes/footer_scripts'); 
for($i=1;$i<=500;$i++)
    $json_data[] =array("acc_id"=>'',"account_name"=>'','pq_cellattr'=> array('account_name'=>array('title'=>'')), 'description'=>'','amount'=>'');

for($i=1;$i<=50;$i++)
    $billsundry_json_data[] = array("billsundry_id"=>'',"billsundry_name"=>'','billsundry_rate'=>'','billsundry_amount'=>'');
?>

<style>
    .boldcell{font-weight:700;}
</style>
<script>
const subTypes = <?php echo $SuppltSubSupplyTypes;?>;
var dateFrom = '<?php echo $fy_begndt;?>';
var dateTo   = '<?php echo $fy_end;?>';
var bsd_comp_applytax_json=[];
var bo_gstin_type='<?php echo $bo_gstin_type;?>';
var goods_rate   ='1';
var services_rate='6';
var taxes_list=<?php echo json_encode($taxes_list);?>;
var  get_total = 0;
var  get_fcy_total = 0;
var  total_tax_val = 0;
var  total_fcy_tax_val = 0;
var vchtyp_id = <?php echo $voucher_type_id;?>;
var default_pos        ='0';
document.addEventListener('DOMContentLoaded', () => {
  const sel = document.getElementById('type');
  sel.disabled = false;
  sel.value = 'non_item'; // auto-select "With Item"
});

$('#supply_type').on('change', function() {
 invoice_type_changes();
 
});

$("#taxsummarybtn").on("click",function(){
    show_taxsummary_items_hsnwise("",'woitm');
});
$('#sub_supply_type').on('change', function() {
	var sub_supply_type = $(this).val();
 if(sub_supply_type=='EXPWP' || sub_supply_type=='EXPWOP')
   $("#view_exports_modal").modal('show');
 item_bsd_totals();
});

$(document).on('click', '#saveExport', function () {

    $('#hidden_sbnum').val($('#sbnum').val());
    $('#hidden_sbpcode').val($('#sbpcode').val());
    $('#hidden_sbdt').val($('#sbdt').val());

    let modal = bootstrap.Modal.getInstance(document.getElementById('view_exports_modal'));
    modal.hide();
});
function handleSaleRedirect(value) {
    // Prevent double redirect
    if (window.__redirecting) return;
    window.__redirecting = true;

    if (value === 'item') {
        window.location.href = "<?php echo $base_url; ?>sales/item";
    } else {
        window.location.href = "<?php echo $base_url; ?>sales/non_item";
    }
}


$("#voucher_date").on("change", function () {
    invoice_type_changes();
    let voucher_date  = $(this).val();
    let $series       = $('select[name="voucher_series"] option:selected');
    let vouchermethod = $series.data("srsmethod");
    let series_id     = $series.val();
    var data = $("#grid_search").pqGrid('option', 'dataModel.data');
    $.each(data, function(index, obj) {
          if (obj.acc_id != '' && obj.acc_id != undefined) {
              /*
             console.log(taxes_list);
             console.log("----");
             console.log(obj.tax_cat_id);
             console.log("----");
             console.log(voucher_date); 
              */
			var taxDetails = getTaxDetails(taxes_list,obj.tax_cat_id, voucher_date);
		     // rd.tax_details  = taxDetails;
              data[index]['tax_details'] = taxDetails;
          }
      });
   // console.log("date changed and afer date grid datra ");
  //  console.log(data);

      $("#grid_search").pqGrid('option', 'dataModel.data', data);
      $("#grid_search").pqGrid('refreshDataAndView');
    
    if (vouchermethod == "1") {
        $.ajax({
            url: baseurl + "admin/ajax/GetAutoBillNo", 
            type: "POST",
            dataType: "json",
            data: {
                voucher_type_id: "<?php echo $voucher_type_id;?>",
                series_id: series_id,
                voucher_date: voucher_date
            },
            success: function (response) {
                if (response && response.billno) {
                    
                    $('input[name="billno"]').val(response.billno).prop("disabled", true);
                    item_bsd_totals();
                }
            },
            complete: function () {
                stop_loader();
            },
            error: function (jqXHR, exception) {
                let error = "";
                if (jqXHR.status === 0) {
                    error = "Not connected. Verify Network.";
                } else if (jqXHR.status == 404) {
                    error = "Requested page not found [404].";
                } else if (jqXHR.status == 500) {
                    error = "Internal Server Error [500].";
                } else if (exception === "parsererror") {
                    error = "Requested JSON parse failed.";
                } else if (exception === "timeout") {
                    error = "Time out error.";
                } else if (exception === "abort") {
                    error = "Ajax request aborted.";
                } else {
                    error = "Uncaught Error:\n" + jqXHR.responseText;
                }
                alert_notification(error);
            }
        });
    
     
    }else{
      item_bsd_totals(); 
    }
    
});
$(document).ready(function () {
    // Run once on page load
    $("#voucher_series").trigger("change");
});
$("#voucher_series").on("change", function () {
    show_loader();
    let series_id     = $(this).val();
    let voucher_date  = $("#voucher_date").val();
    let vouchermethod = $("#voucher_series option:selected").data("srsmethod");
    if (vouchermethod == "1") {		
		$.ajax({
            url: baseurl + "admin/ajax/GetAutoBillNo",
            type: "POST",
            dataType: "json",
            data: {
                voucher_type_id: "18",
                series_id: series_id,
                voucher_date: voucher_date
            },
            success: function (response) {
                if (response && response.billno) {
                    tax_details = response.rates;
                    $('input[name="billno"]').val(response.billno).prop("disabled", true);
                    item_bsd_totals()
                }
            },
            complete: function () {
                stop_loader();
            },
            error: function (jqXHR, exception) {
                let error = "";
                if (jqXHR.status === 0) {
                    error = "Not connected. Verify Network.";
                } else if (jqXHR.status == 404) {
                    error = "Requested page not found [404].";
                } else if (jqXHR.status == 500) {
                    error = "Internal Server Error [500].";
                } else if (exception === "parsererror") {
                    error = "Requested JSON parse failed.";
                } else if (exception === "timeout") {
                    error = "Time out error.";
                } else if (exception === "abort") {
                    error = "Ajax request aborted.";
                } else {
                    error = "Uncaught Error:\n" + jqXHR.responseText;
                }
                alert_notification(error);
            }
        });
       
    } else {
        item_bsd_totals();
        $('input[name="billno"]').val("").prop("disabled", false);
        stop_loader();
    }
    
});

/*********************  FC Rates   Start         *********************/


function calculate_fcChanged(currency_id,billsundry_colModel,colModel,currency_symbol){
     var fcrates           =  $("#view_fcrates_modal #fcy_forex_rate").val();
	 if(fcrates >0){
                $("#view_fcrates_modal #selcyrlabel").html(currency_symbol);				
				$("#view_fcrates_modal #fcy_voucher_no").html($("#voucher").val());
				$("#view_fcrates_modal #fcy_voucher_date").html($("#voucher_date").val());				
			    $("#view_fcrates_modal").modal('show');	
			    colModel[2].hidden=false;
                colModel[2].width= 180;
				colModel[2].title='AMOUNT('+currency_symbol+')';
				colModel[3].editable=false;
                $("#grid_search").pqGrid( "option", "colModel", colModel );             
                $("#grid_search").pqGrid( {autoFit: true} );
                $("#grid_search").pqGrid("refreshCM");
                $("#grid_search").pqGrid("refresh");	
		}               
         else{	colModel[2].hidden=true;
                colModel[2].width= 180;
               	colModel[3].editable=true;
				$("#grid_search").pqGrid( "option", "colModel", colModel );             
                $("#grid_search").pqGrid( {autoFit: true} );
                $("#grid_search").pqGrid("refreshCM");
                $("#grid_search").pqGrid("refresh");	
		    }
    }
    
function validatefcrate(e,a){return IsNumericFCRate(a,e.keyCode?e.keyCode:e.charCode)}	
var isShiftt = false;
function IsNumericFCRate(_,e){if(16==e&&(isShiftt=!0),(!(e>=48)||!(e<=57))&&8!=e&&!(e<=37)&&!(e<=39)&&(!(e>=96)||!(e<=105))&&190!=e&&110!=e||!1!=isShiftt||_.includes(".")&&(190==e||110==e))return!1;if(_.includes(".")){if(_.split(".")[1].length>=8&&(e>=96&&e<=105||e>=48&&e<=57))return!1}else if(_.length>=6&&(e>=96&&e<=105||e>=48&&e<=57))return!1;return!0}
$(".clsoefcratemodal").on("click",function(){
	$("#view_fcrates_modal #fcy_voucher_no").html("");
	$("#view_fcrates_modal #fcy_voucher_date").html("");
	$("#view_fcrates_modal").modal("hide");
	});	
$("#view_fcrates_modal").on("hidden.bs.modal",function(){
	$("#view_fcrates_modal #fcy_voucher_no").html("");
	$("#view_fcrates_modal #fcy_voucher_date").html("");
   });

/*********************  FC Rates  END          *********************/
function grid_total_item(obj,currency=''){
        var total = 0;
        if($(obj).pqGrid('instance')){ 
            var data = $(obj).pqGrid('option', 'dataModel.data');
 
            data.forEach(function(row){
                if(row.account_name != '' && row.account_name != undefined && row.amount != '' && row.amount != undefined)
                {
				  if(currency=='€' || currency=='$'){
					total += parseAmount(row.amountfc); 
				 }
				 else if(currency=='memo'){
					total += parseAmount(row.memo_amt); 
				 }
				 else{
					total += parseAmount(row.amount);					
				 }
				}
            });
        }
        return total;
    }
	function grid_total_bsd(obj,currency=''){
        var total = 0;
        if($(obj).pqGrid('instance')){ 
            var data = $(obj).pqGrid('option', 'dataModel.data');
			
            data.forEach(function(row){
                if(row.billsundry_id != '' && row.billsundry_id != undefined && row.billsundry_amount != '' && row.billsundry_amount != undefined)
                { 
				 if(currency=='€' || currency=='$'){
					total += parseAmount(row.billsundry_amountfc); 
				 }
				 else if(currency=='memo'){
					total += parseAmount(row.billsundry_memoamnt); 
				 }else{
					total += parseAmount(row.billsundry_amount);
				 }				 
				}
            });
        }
        return total;
    }
$(document).on("change","#reverse_charges",function(){
	show_loader();
	invoice_type_changes();
	 if($(this).val()==1 || $(this).val()==2){
		  
		var bsd_taxsummary_json=[];
		for(var i=1;i<=50;i++){		 
	     bsd_taxsummary_json.push({'bl_nature':'','is_tax_account':0,'tax_item_id':"","tax_hsn_sac":"","tax_amt":"","cess_rate":"","tax_rate":"","tax_rate_string":"","igst":"","cess":"","cgst":"","sgst":"",'total_tax':""});	 
	   }
	  $("#billsundry_search").pqGrid('option', 'dataModel.data', bsd_taxsummary_json);
	  $("#billsundry_search").pqGrid('refreshDataAndView');	  
	  stop_loader();
	 }else{
	   stop_loader();
	 }		 
 });	
 

$(function(){	
	let input = document.getElementById('clone_party_id');
   let timeout = null;
  input.addEventListener('keyup', function (e) {  	
	var val = input.value;			
    var match =$('#party_ids option').filter(function(){
		return (this.value === val);               
    });
	if(match.length==0){
		$("#pos").val("");		
		$("#grid_search").pqGrid("option", "editable", false);
			 $("#billsundry_search").pqGrid("option", "editable", false);
			 $("#party_id option").val('');
			 $("#party_id option").attr("data-is_bbb",0);
			 $("#party_id option").attr("data-gstin","");
			 $("#party_id option").attr("data-grpid","");
			 $("#party_id option").text('');			 
			 $('#invoice_type option[value=""]').prop('selected', true);
			 $('#invoice_type_label').html('');
			 invoice_type_changes();
			 refreshSubSupplyType();
			 $("#pos").trigger('change');
		    
	}else{
	
	var opt = $('#party_ids option[value="'+val+'"]');
	var opid = (opt.attr('id'));
	var opstcode = (opt.data('statecode'));	
	var opgstin = (opt.data('gstin'));
	
	$("#pos").val(opstcode);	
	$("#grid_search").pqGrid("option", "editable", true);
	$("#billsundry_search").pqGrid("option", "editable", true);
	$("#party_id option").val(opt.attr('id'));
	$("#party_id option").attr("data-is_bbb",opt.attr('data-is_bbb'));
	$("#party_id option").attr("data-grpid",opt.attr('data-grpid'));
	$("#party_id option").attr("data-gstin",opgstin);	
	$("#party_id option").text($(this).val());	
	$('#party_id option[value="'+opt.attr('id')+'"]').prop('selected', true);
	invoice_type_changes();
	refreshSubSupplyType();
	$("#pos").trigger('change');		
	}   
  });
});

var ugst_states        = ['35','04','26','25','31','38','34','97'];
var accounts_json_file = <?php echo ($accounts_json_file!='')?$accounts_json_file:'[]'; ?>;
var bsd_json_file  = <?php echo ($bsd_json_file!='')?$bsd_json_file:'[]';?>;
var bo_state_code  = '<?php echo sprintf('%02d', $bo_state_code);?>';
var billsundry_dataModel = {"data":<?php echo json_encode($billsundry_json_data);?>};
var iscrnote=0;

</script>
<script src="<?php echo base_url();?>public/js/sale_without_item.js"></script>
<script src="<?php echo base_url();?>public/js/sale_without_item_grid.js"></script>
<script>
var billsundry_autoComplete = function (ui) {
        var rd      = ui.rowData;
        var $inp    = ui.$cell.find("input");
        var element = {};
		// Filter out tax accounts - only show records where is_tax_account is NOT 1
        var filtered_bsd_json = bsd_json_file.filter(function(item) {
            return parseFloat(item.is_tax_account) !== 1;
        });
        $inp.autocomplete({
                source:  filtered_bsd_json,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {					
					// check if item choosed or not 					
					event.preventDefault();
					var voucherDate = $("#voucher_date").val();
                    rd.tax_catg_id     = ui.item.tx_ct_id;
                    rd.billsundry_name = ui.item.label;
                    rd.billsundry_id   = ui.item.id;
                    
                    var taxDetails = getTaxDetails(taxes_list,rd.tax_catg_id, voucherDate);
		               rd.tax_details  = taxDetails;
		               rd.igst_rate    = (taxDetails && taxDetails.igst) ? taxDetails.igst : 0;
		         	   rd.cess_rate    = (taxDetails && taxDetails.cess) ? taxDetails.cess : 0;
			
			
                    rd.bl_tax_rate     =  (taxDetails && taxDetails.igst) ? taxDetails.igst : 0;
					rd.bl_hsn_sac      = ui.item.bl_hsn_sac;
					rd.bl_nature       = ui.item.bl_nature;
					rd.is_tax_account  = ui.item.is_tax_account;
					rd.bl_ipt_ott      = ui.item.bl_ipt_ott;
					
					if(ui.item.is_tax_account=="1" && bo_gstin_type=="2"){
						alert_notification("Composition Gstin Dealers Are Not Allowed To Add Tax In Sale Invoice");
						return false;
					}
					$(this).val(ui.item.label);										
			    	$("#billsundry_search").pqGrid('refreshDataAndView');
  				
	           }
             
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.billsundry_name = '';
                rd.billsundry_id = '';
            }).focusout(function () {              
                if(rd.billsundry_id == '')
                {
                    rd.billsundry_name = '';
                    rd.billsundry_id = '';
                }
			
				 
			var errors=0;			
			var bsddata = $("#billsundry_search").pqGrid('option', 'dataModel.data');
			 for (var i = 0; i < bsddata.length; i++) {											 
			   if( (bsddata[ i ]['billsundry_id'] == rd.billsundry_id) && (bsddata[ i ] != ui.rowData && rd.is_tax_account=="1") ) {
					errors=1;
					$("#billsundry_search").pqGrid( "updateRow",{ rowIndx: ui.rowIndx, row: {'billsundry_name': '','billsundry_amount': '' }});  
				   }
			    }
            });
           
        }
    
$(document).on('submit', '#salefrm', function(e){
		e.preventDefault();
		var form = $(this);
		var formData = new FormData(this);    
	    $.ajax({
            url: form.attr('action'), 
            type: 'POST',
            data: formData,
            dataType: "json",
            processData: false,
            cache: false,
            contentType: false,
            beforeSend: function() {
                show_loader();
                $('#submitbtn').attr('disabled', 'disabled');
                $('#validation_errors').html('');
            },
            success: function (response) {
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
					const vchrtxnid = response.vchrtxnid;
					const doEinvoice = () => $('#EINVOICECheck').is(':checked');
					const doEway     = () => $('#EWAYCheck').is(':checked');

					const run = async () => {
							try {
								console.log("Starting submission process");
								
								const results = {
									einvoice: null,
									eway: null,
									errors: []
								};

								// Submit E-Invoice if checked
								if (doEinvoice()) {
									try {
										console.log("Submitting E-Invoice...");
										results.einvoice = await submitEinvoiceIfChecked(vchrtxnid);
										
										if (results.einvoice. status === false) {
											if (Array.isArray(results.einvoice.errors)) {
												results.errors = results.errors.concat(results.einvoice.errors);
											}
										}
									} catch (err) {
										console.error("E-Invoice error:", err);
										results.errors.push("E-Invoice: " + err.message);
									}
								}

								// Submit E-Way if checked
								if (doEway()) {
									try {
										console.log("Submitting E-Way...");
										results.eway = await submitEwayIfChecked(vchrtxnid);
										
										if (results. eway.status === false) {
											if (Array.isArray(results.eway.errors)) {
												results.errors = results.errors.concat(results. eway.errors);
											}
										}
									} catch (err) {
										console. error("E-Way error:", err);
										results.errors.push("E-Way: " + err.message);
									}
								}

								// Show all errors at once
								if (results.errors.length > 0) {
									console.error("All Errors:", results.errors);
									const errorMsg = results.errors.join("\n");
									alert_notification("Validation Errors:\n\n" + errorMsg);
									return false;
								}

								// Success case
								if (doEinvoice() || doEway()) {
									console.log("All submissions successful");
									alert_success("Completed requested operations.");
									window.location.reload();
								}else
									window.location.reload();

								return true;

							} catch (err) {
								console.error("Fatal error:", err);
								alert_notification("Operation failed: " + err.message);
								return false;
							}
						};

						run().catch(console.error);
                    
                }
                else{ 
				    stop_loader();
                    alert_notification(response.message);
                    if(response.errors)
                    {
                        var list = ``;
                        $.each(response.errors, function(index, value){
                            list += `<li>${value}</li>`;
                        });

                       var html = `
	                            <div class="alert-error-custom">
								<i class="bi bi-x-circle-fill"></i>
								<div>
								<strong>Error!</strong>  <ul>${list}</ul>
								</div>
								<button type="button" class="btn-close" aria-label="Close"></button>
							</div>
	                        `;
                        $('#validation_errors').html(html);
						$('#validation_errors').show();
                        window.scrollTo(0,0);
                    }  
                }
                
            },
            complete: function() {
                stop_loader();
                $('#submitbtn').attr('disabled', false);
            },
            error: function (jqXHR, exception) {
				stop_loader();
                var error_= '';
                if (jqXHR.status === 0) {
                    error = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    error = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    error = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    error = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    error = 'Time out error.';
                } else if (exception === 'abort') {
                    error = 'Ajax request aborted.';
                } else {
                    error = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert_notification(error);
            },
        });
   
    });

 $(function () {
        var colModel = [
            { title: "PARTICULARS",sortable:false, dataIndx: "account_name", width: 100,dataType: "string",cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
                type: "textbox",
                attr: "autocomplete='off'",
                init: autoCompleteEditor,
                options: [],
                },
            render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    var account_balance = '0';
                    if(typeof rd.acc_id !== "undefined" && rd.acc_id != '' && rd.account_name != '')
                    {      var account_balance = rd.account_balance;
                        if(typeof account_balance !=="undefined"){
                     grid.addClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'pq-cell-red-tr pq-has-tooltip' });
                   	rd.pq_cellattr ={
								"account_name" : { "title":account_balance }
							};
							return ui.cellData;
                    }
                    else 
                    return ui.cellData;
                    }
                }     
            },
            { title: "DESCRIPTION", sortable:false,width: 100, dataType: "string",editable: function (ui) {
                   var account_id = ui.rowData['acc_id'];
                    if (account_id != '') {
                        return true;
                    }
                    return false;
                }, dataIndx: "description"},
				
			{ title: "AMOUNT(₹)", width: 20,sortable:false, align: "right",dataIndx: "amounttxs",hidden:true,
              	editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        
                        $inp.on("change", function (evt) {
							
							if($('#taxInclusive').is(":checked")){
								if(bo_gstin_type=="1"){ // means Regular
									var itmrate = rd.igst_rate;
								   } 
								   else if(bo_gstin_type=="2"){ // means Composition
									if((rd.tax_exempted=='n') && (rd.supply_type=="1" || rd.supply_type=="3"))
									 var itmrate = goods_rate;
									 else if(rd.tax_exempted=='n' && rd.supply_type=="2")
									 var itmrate = services_rate;
									 else
										 var itmrate =0; 
									  }
								
								 var x= parseAmount((itmrate/100));
								 var y = parseAmount(x)+parseAmount(1);
								 var totalprice = parseAmount(rd.amounttxs);
								 var taxableamount = parseAmount(totalprice/y);
								
								 rd.amount = taxableamount;
						 
							 }else{
								 rd.amount = parseAmount(rd.amounttxs);
								 
							 } 
							 grid.refreshDataAndView();
						
                        })
                    }
                },
				render: function( ui ) {
				    var rd = ui.rowData;
					if(rd.amounttxs  >0){
                        return formatAmount(parseAmount(rd.amounttxs));   
                    }
                    return '';
                }
              },	
           { title: "AMOUNT(₹)", width: 20,sortable:false, align: "right",dataIndx: "amountfc",hidden:true,dataType: "float",
           editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        
                        $inp.on("change", function (evt) {
								var currency_id = $('select[name="currency_id"] option:selected').val();						
								var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
                              if(rd.amountfc != '' && parseAmount(rd.amountfc) >= 0)
                			  {
                				  
                                                var amount =parseAmount(rd.amountfc);
                							
                								
                						       if(parseAmount(fcy_forex_rate) > 0){
                								 
                								  if(parseFloat(fcy_forex_rate)!=0)
                								    rd.amount   = parseAmount((rd.amountfc)/fcy_forex_rate);	
                							     else
                									rd.amount   = parseAmount(0); 
                								 
                							   }
                							   else{
                								  rd.amountfc = 0;
                								 								  
                								}
                							
                						
                                }else
									rd.amountfc = 0;
                          		grid.refreshDataAndView();
						
					 
                        });
						
                    }
                },
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.amountfc >0){
						var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
						 rd.amountfc =parseAmount(rd.amountfc);
					
							return formatAmount(parseAmount(rd.amountfc),currency_symbol);   
						
                    }
                    return '';
                }
              }, 
            { title: "AMOUNT(₹)", width: 20, sortable:false,align: "right",editable: function (ui) {
                   var account_id = ui.rowData['acc_id'];
                    if (account_id != '') {
                        return true;
                    }
                    return false;
                },dataIndx: "amount",dataType: "float",
                  validations: [{ type: 'gt', value: 0, msg: "should be > 0"}],
                  render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.amount  >0){
                        rd.amount = parseAmount(rd.amount);
						return formatAmount(rd.amount); 
						
                    }
                    return '';
                },
            },
			
			{ title: "MEMO(₹)", width: 20,sortable:false, align: "right",dataIndx: "memo_amt",hidden:true,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.memo_amt >0){
						rd.memo_amt = parseAmount(rd.memo_amt);
						return formatAmount(parseAmount(rd.memo_amt));
                    }
                    return '';
                }
              },	
        ];
            
        var dataModel = {"data":<?php echo json_encode($json_data);?>};
        
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            change: calculateSummary, 
            dataReady: calculateSummary,
            columnTemplate: { render: commentRender },
            colModel: colModel,  
            numberCell: { show: true },
            wrap:false,
            editable: true,
            cellSave: function(evt, ui){
                   this.refresh();
               },
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
            this.widget().pqTooltip();
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: true });
              }
        };

        newObj.cellKeyDown = function(evt, ui) {
           var rowData = ui.rowData;
           var rowIndx = ui.rowIndx;

           if (evt.keyCode == 46){
                $.each(rowData, function(index,obj){
                    rowData[index] = '';
                });
                this.refreshDataAndView();
				
                
           }
        }
        var $grid = $("#grid_search").pqGrid(newObj);
                

        
        var billsundry_colModel = [
             { title: "BILL SUNDRY", width: 100, dataType: "string", align: "left",dataIndx: "billsundry_name" ,
                editor: {                   
                          type: "textbox",
                          init: billsundry_autoComplete,
                          options: []
                      },
			editable: function (ui) {				  
				  if(ui.rowData.is_tax_account=='1' && ui.rowData.bl_nature!='199')
					  return false;
				  else 
					  return true;				
			  }
              },
			  
			  { title: "AMOUNT(₹)", width: 100,sortable:false, align: "right",dataIndx: "billsundry_amountfc",hidden:true,dataType: "float",
           editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        
                        $inp.on("change", function (evt) {
								var currency_id = $('select[name="currency_id"] option:selected').val();						
								var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
                              if(rd.billsundry_amountfc != '' && parseAmount(rd.billsundry_amountfc) >= 0) {
                                    var amount =parseAmount(rd.billsundry_amountfc);
                					 if(parseFloat(fcy_forex_rate) > 0 ){
                						if(parseFloat(fcy_forex_rate)!=0)
                							rd.billsundry_amount   = parseAmount((rd.billsundry_amountfc)/fcy_forex_rate);	
                						 else
                							rd.billsundry_amount   = parseAmount(0); 
                						}
                						else{
                						rd.billsundry_amountfc = 0;
                						}
                                }else
									rd.billsundry_amountfc = 0;
						
			            	grid.refreshDataAndView();
						
                        })
						
                    }
                },
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.billsundry_amountfc >0){
						var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
						 rd.billsundry_amountfc =parseAmount(rd.billsundry_amountfc);
					
							return formatAmount(parseAmount(rd.billsundry_amountfc),currency_symbol);   
						
                    }
                    return '';
                }
              }, 
            { title: "AMOUNT(₹)", width: 100, sortable:false,align: "right",editable: function (ui) {
                   var billsundry_id = ui.rowData['billsundry_id'];
                    if (billsundry_id != '') {
                        return true;
                    }
                    return false;
                },dataIndx: "billsundry_amount",dataType: "float",
                  validations: [{ type: 'gt', value: 0, msg: "should be > 0"}],
                  render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.billsundry_amount  >0){
                        rd.billsundry_amount = parseAmount(rd.billsundry_amount);
						return formatAmount(rd.billsundry_amount); 
						
                    }
                    return '';
                },editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd     = ui.rowData;
                        var grid   = this;
                        var $inp   = ui.$cell.find("input");
						                    
                        $inp.on("change", function (evt) {
						
                        })
                    }
                },
            },
			  
			
			{ title: "MEMO(₹)", width: 100, align: "right", dataIndx: "billsundry_memoamnt" ,hidden:true,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
					if(rd.billsundry_memoamnt >0){
						rd.billsundry_memoamnt=parseAmount(rd.billsundry_memoamnt);
						 return formatAmount(parseAmount(rd.billsundry_memoamnt));   
						
                    }
					
                    return '';
                }
				
            },
        ];
          
        var bsd_newObj2 = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 400,
            selectionModel: { type: 'row' },
            scrollModel: { autoFit: true },
            dataModel: billsundry_dataModel,
            colModel: billsundry_colModel,  
              pageModel: { type: 'local' },
            numberCell: { show: true },
            wrap:false,
            change: billsundry_calculateSummary,
            dataReady: billsundry_calculateSummary,
            editable: true,
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            cellSave: function(evt, ui){
                   this.refresh();
               },
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: false });
                }
        };
         bsd_newObj2.cellKeyDown = function(evt, ui) {
           var rowData = ui.rowData;
           var rowIndx = ui.rowIndx;

           if (evt.keyCode == 46){
                $.each(rowData, function(index,obj){
                    rowData[index] = '';
                });
                this.refreshDataAndView();
				
				item_bsd_totals();
                
           }
        } 
         $("#billsundry_search").pqGrid(bsd_newObj2); 
		 
		 $("#save_forex_rates").on("click",function(){
	var currency_id = $('select[name="currency_id"] option:selected').val();	
	var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
	if(parseAmount(fcy_forex_rate) >0 ){
			
			 var data = $('#grid_search').pqGrid('option', 'dataModel.data');	
			 $.each(data, function(index,obj){
					if(typeof obj.acc_id !== "undefined" && obj.acc_id != '')
						{
						  
						  var cr_amount   = parseAmount((obj.amountfc)/fcy_forex_rate);
						 
						  data[index]['amount']    = cr_amount;
					 	  
						  }
					}); 
						
			 }			 
			 $('#grid_search').pqGrid('option', 'dataModel.data', data);
             $('#grid_search').pqGrid('refreshDataAndView');						
		
	
		 $("#view_fcrates_modal").modal("hide")});
	
		 $("#fcratespopup").on("click",function(){
			 var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
			 var currency_id    = $('select[name="currency_id"] option:selected').val();
			 calculate_fc_rates(currency_id,billsundry_colModel,colModel,currency_symbol);  
			  
		  });
		 
	  $(document).on("change","#currency_id",function(){
		  let currency_symbol = $(this).find("option:selected").data("id"); // get selected symbol
			if(currency_symbol=='₹'){
			 var data = $('#grid_search').pqGrid('option', 'dataModel.data');	
			 $.each(data, function(index,obj){
					if(obj.acc_id != '' && obj.acc_id != undefined )
						{
							if(bo_gstin_type=="1"){ // means Regular
							var igst_rate_val = obj.igst_rate;
						}
						else if(bo_gstin_type=="2"){ // means Composition
							if((obj.tax_exempted=='n') && (obj.supply_type=="1" || obj.supply_type=="3"))
							 var igst_rate_val = goods_rate;
							 else if(obj.tax_exempted=='n' && obj.supply_type=="2")
							 var igst_rate_val = services_rate;
							 else
								 var igst_rate_val =0; 
						 }
						 if($('#taxInclusive').is(":checked")){
							var itmrate = igst_rate_val;
								 var x= parseAmount((itmrate/100));
								 var y = parseAmount(x)+parseAmount(1);
								 var totalprice = parseAmount(obj.amounttxs);
								 var taxableamount = parseAmount(totalprice/y);
								var tax_amt = parseAmount(taxableamount);
						
								
								data[index]['amounttxs'] = parseAmount(obj.amounttxs);
								data[index]['amount']    = parseAmount(tax_amt); 
						    } else{
							   
								data[index]['amount']    = parseAmount(obj.amount);
								data[index]['amounttxs'] = parseAmount(0); 
						     }
						  }
					});	
				
			$('#grid_search').pqGrid('option', 'dataModel.data', data);
                $('#grid_search').pqGrid('refreshDataAndView');	
			
				
			   calculate_fc_rates($(this).val(),billsundry_colModel,colModel,currency_symbol);
			 item_bsd_totals();
			  
			}			
			else if(currency_symbol!='₹' && $('#taxInclusive').is(":checked")){				 
				alert_notification("Tax Inclusive can not applied on foreign  currency!");
				return false;
			 }else{
			  calculate_fc_rates($(this).val(),billsundry_colModel,colModel,currency_symbol);
				item_bsd_totals();
			  }
			});
		 
		$('#memoCheck').on('change', function(){ // on change of state
		     if(this.checked) // if changed state is "CHECKED"
			  {
				memoChanged(billsundry_colModel,colModel,true);
			  }
			  else{
				memoChanged(billsundry_colModel,colModel,false);
			  }
		});
		
		
	    $('#taxInclusive').on('change', function(){ // on change of state
		     if(this.checked) // if changed state is "CHECKED"
			  {				  
				if($('select[name="currency_id"] option:selected').attr('data-id') !='₹' ){
					alert_notification("Tax Inclusive can not applied on foreign  currency!");
					$(this).prop("checked",false);
					return false;
				}  else{
				TaxInsChanged(billsundry_colModel,colModel,true);	
				
				var data = $('#grid_search').pqGrid('option', 'dataModel.data');
				
				$.each(data, function(index,obj){
					if(obj.acc_id != '' && obj.acc_id != undefined)
						{
							if(bo_gstin_type=="1"){ // means Regular
							var igst_rate_val = obj.igst_rate;
						}
						else if(bo_gstin_type=="2"){ // means Composition
							if((obj.tax_exempted=='n') && (obj.supply_type=="1" || obj.supply_type=="3"))
							 var igst_rate_val = goods_rate;
							 else if(obj.tax_exempted=='n' && obj.supply_type=="2")
							 var igst_rate_val = services_rate;
							 else
								 var igst_rate_val =0; 
						  }
							data[index]['amounttxs'] = parseAmount(obj.amount);	
							 var itmrate = igst_rate_val;
							 var x= parseAmount((itmrate/100));
							 var y = parseAmount(x)+parseAmount(1);
							 var totalprice = parseAmount(obj.amounttxs);
							 var taxableamount = parseAmount(totalprice/y);
							 var tax_amt = parseAmount(taxableamount);						 
							 data[index]['amounttxs'] = parseAmount(obj.amounttxs);
							 data[index]['amount'] = parseAmount(tax_amt);
						  }
					});
				}
				$('#grid_search').pqGrid('option', 'dataModel.data', data);
                $('#grid_search').pqGrid('refreshDataAndView');
			  }
			  else{
				TaxInsChanged(billsundry_colModel,colModel,false);
				
				var data = $('#grid_search').pqGrid('option', 'dataModel.data');
				$.each(data, function(index,obj){
					if(obj.acc_id != '' && obj.acc_id != undefined )
						{
						data[index]['amount'] = parseAmount(obj.amounttxs);
						data[index]['amounttxs'] = parseAmount(0);
							
				
						  }
					});
					
					$('#grid_search').pqGrid('option', 'dataModel.data', data);
                    $('#grid_search').pqGrid('refreshDataAndView');
			  }			
			item_bsd_totals(); 
		})	
		
		
    });
    
    
    
/***************  Start Bill By Bill *************************/
var billIndex = 0;
var bbb_accounts=[];
    function readyBills(total_amount,total_amountfc)
    {
		var account_id = $('select[name="party_id"]').val();
        var account_name = $('select[name="party_id"] option:selected').text();
		bbb_accounts.push({
			'account_id'    : account_id,
			'account_name'  : account_name,
			'drcr'          : 'D',
			'amount'        : total_amount,
			'amountfc'        : total_amountfc,
			'grid'          : generateBillData()
		});	
        var account_id_array = bbb_accounts.map(function(obj) { return obj.account_id; });
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>vouchers/getAccountBillRefs",
            data: {account_id_array: account_id_array},
            datatype: "json",
            success: function(response){
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status)
                {
                    $.each(response.data, function(index,obj)
                    {
                        bbb_accounts[index].ref_list = obj;
                    });
                    stop_loader();
                    readyBillsModel();
                }
            }
        });          
    }
$("#save_bill_by_bill").on("click",function(){
        var status = true;
        status = validateBills();
        if(!status){
            return;
        }
        status = validateAllBillsData();
        if(!status){
            return;
        }
        if(status){
            saveBillByBillData();
            $('#billsModal').modal('hide');
            $("#bbbdata").val(JSON.stringify(bbb_data));
            show_loader();
             if($('#prCheck').is(":checked") && pr_accounts.length > 0){ //prstart
                readyPr();
            }
            else{
                $("#salefrm").submit();
            }
        }
    });

function saveBillByBillData(){
        $.each(bbb_accounts, function(index,obj)
        {
            var account_id = obj.account_id;
            
            $.each(obj.grid, function(index2, obj2)
            {
                if(obj2.method != '' && obj2.reference != '')
                {
                    var method     = obj2.method;
                    var reference  = obj2.reference;
                    var reference_id  = obj2.reference_id;
                    var amount     = obj2.amount;
					var amountfc   = obj2.amountfc;
                    var drcr       = obj2.drcr;
                    var due_date   = obj2.due_date;
                    var narration  = obj2.narration;

                    bbb_data.push({
                        "account_id": account_id,
                        "method"    : method,
                        "reference" : reference,
                        "reference_id" : reference_id,
                        "amount"    : parseAmount(amount),
						"amountfc"  : parseAmount(amountfc),
                        "drcr"      : drcr,
                        "due_date"  : due_date,
                        "narration" : narration
                    });
                }
            });
            
        });
    }
function validateAllBillsData() {
    var isAnyGridValid = false;

    for (var i = 0; i < bbb_accounts.length; i++) {
        var obj = bbb_accounts[i];

        var final_amount = parseAmount(obj.amount);
        var final_drcr = obj.drcr;
        var final_total = (final_drcr === 'C') ? -final_amount : final_amount;

        var total = 0;
        var hasValidRow = false;

        for (var j = 0; j < obj.grid.length; j++) {
            var obj2 = obj.grid[j];

            var method = obj2.method;
            var reference = obj2.reference;
            var amount = obj2.amount;
            var drcr = obj2.drcr;

            // 🚨 detect partial row
            if ((method !== '' || reference !== '' || amount !== '') &&
                (method === '' || reference === '')) {
                $('#bill_warning').text('Incomplete bill entry detected.');
                return false;
            }

            if (method !== '' && reference !== '') {
                var parsedAmount = parseAmount(amount);

                if (parsedAmount <= 0) {
                    $('#bill_warning').text('Amount must be greater than zero.');
                    return false;
                }

                var sub = (drcr === 'D') ? parsedAmount : -parsedAmount;
                total += sub;
                hasValidRow = true;
            }
        }

        if (hasValidRow) {
            if (Math.abs(total - final_total) > 0.01) {
                $('#bill_warning').text('Bill total mismatch for account: ' + (obj.account_name || ''));
                return false;
            }

            isAnyGridValid = true;
        }
    }

    if (!isAnyGridValid) {
        $('#bill_warning').text('Please fill at least one bill grid.');
        return false;
    }

    return true;
}	


   function validateBills()
{
    var sum = 0;
    var data = $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data');
    var error = 0;
    var count = 0;
    
    for (var i = 0; i < data.length; i++) {
        var method     = data[i]['method'];
        var reference  = data[i]['reference'];
        var amount     = data[i]['amount'];
        var drcr       = data[i]['drcr'];

        var parsedAmount = parseAmount(amount);

        // 🚨 Detect partial row (very important fix)
        if ((method !== '' || reference !== '' || amount !== '' || drcr !== '') &&
            (method === '' || reference === '' || drcr === '')) {
            $('#bill_warning').text("Incomplete bill entry detected.");
            return false;
        }

        if (method !== '' && reference !== '') {
            count++;

            if (parsedAmount <= 0) {
                $('#bill_warning').text("Amount must be greater than zero.");
                return false;
            }

            var sub = (drcr === 'D') ? parsedAmount : -parsedAmount;
            sum += sub;
        }
    }

    if (count === 0) {
        $('#bill_warning').text("Please fill at least one bill entry.");
        return false;
    }

    var final_amount = parseAmount(bbb_accounts[billIndex].amount);
    var final_drcr = bbb_accounts[billIndex].drcr;

    if (final_drcr === 'C') {
        final_amount = -final_amount;
    }

    // ✅ tolerance comparison (important)
    if (Math.abs(sum - final_amount) > 0.01) {
        $('#bill_warning').text("Total Mismatch");
        return false;
    }

    return true;
}

    function readyBillsModel(step = '')
    {
        if(step == ''){
            billIndex = 0;
        }
        else{
            var status = validateBills();
            if(!status){
                return;
            }
        }
        if(step == 'next'){
            if(billIndex < (bbb_accounts.length - 1)){
                billIndex = billIndex + 1;
            }
        }
        if(step == 'prev'){
            if(billIndex > 0){
                billIndex = billIndex - 1;
            }
        }
        if(bbb_accounts[billIndex])
        {
			var fcrates           =  $("#view_fcrates_modal #fcy_forex_rate").val();

            var account_name = bbb_accounts[billIndex].account_name;
            var amount = bbb_accounts[billIndex].amount;
			var amountfc = bbb_accounts[billIndex].amountfc;
            var drcr = bbb_accounts[billIndex].drcr;
            var page = (billIndex + 1) + '/' + bbb_accounts.length;
            var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
            $('#bills_account').text(account_name);
            if(fcrates > 0)
              $('#bills_total').html(formatAmount(amountfc,currency_symbol)+ " &nbsp; "+formatAmount(amount));
	       else 
		        $('#bills_total').html(formatAmount(amount));	     
				$('#bills_drcr').text(drcr + 'r');
				$('#bills_page').text(page);
				$('#billsModal').modal('show');			
			if(fcrates > 0){
	             bill_colModel[2].hidden=false;
                bill_colModel[2].width= 150;
				bill_colModel[2].title='AMOUNT('+currency_symbol+')';
			    $("#bill_by_bill_grid").pqGrid( "option", "colModel", bill_colModel );             
                $("#bill_by_bill_grid").pqGrid( {autoFit: true} );
                $("#bill_by_bill_grid").pqGrid("refreshCM");
                $("#bill_by_bill_grid").pqGrid("refresh");				
			  }
			  else{
				 bill_colModel[2].hidden=true;
				 bill_colModel[2].width= 150;
				 bill_colModel[2].title='AMOUNT('+currency_symbol+')';  							
				 $("#bill_by_bill_grid").pqGrid( "option", "colModel", bill_colModel );             
				 $("#bill_by_bill_grid").pqGrid( {autoFit: true} );
				 $("#bill_by_bill_grid").pqGrid("refreshCM");
				 $("#bill_by_bill_grid").pqGrid("refresh");
			   }            
              $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data', bbb_accounts[billIndex].grid);
              $("#bill_by_bill_grid").pqGrid('refreshDataAndView');
              $('input[type="command-line"]').focus();//tempararily shift focus
              $("#bill_by_bill_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 0, focus: true });
        }        
    }
                
    function generateBillData(account_id = 0){
        var json = [];        
        if(account_id != 0){
           var i = saved_bill_txns.findIndex(function(o) {
               return o.acc_id == account_id;
            });
            if(i >= 0){
                $.each(saved_bill_txns[i].bills_txn_list, function(index, obj){
                    json.push({'method': 'Adjustment', 'reference': obj.bill_ref_name, 'reference_id': obj.bill_ref_id, 'amountfc': obj.bill_txn_fcy,'amount': obj.bill_txn_amt, 'drcr': obj.bill_txn_dr_cr,
                              'due_date': obj.bill_due_date, 'narration': obj.bill_txn_narr});
                });
            }
        }
        
        for(var i=0;i<500;i++){
            json.push({'method': '', 'reference': '', 'reference_id': '', 'amountfc': '', 'amount': '', 'drcr': '', 'due_date': '', 'narration': ''});
        }
        return json;
    }
                
    function dateEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,                
            grid = this;

        $inp.on("focusout", function (e) {
            var date = rd.due_date;
            
            if(!isValidDate(date)){
              rd.due_date = '';
              e.preventDefault();
            }
        });
        
        $inp.inputmask("99/99/9999", {
            mask: "99-99-9999",
            alias: "date",
            placeholder: "dd-mm-yyyy",
            insertMode: false,
        }).datepicker({
            altFormat: "dd-mm-yyyy",
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            changeYear: true,
            minDate:'<?php echo $fy_begndt;?>',
            maxDate:'<?php echo $fy_end;?>',
            onClose: function () {
                this.focus();
            }

        });
    };
                
    function methodEditor(ui) {
        var $inp = ui.$cell.find("select"),
            di = ui.dataIndx,
            rd = ui.rowData,               
            grid = this;
        
        $inp.on("change", function (evt) {
            var method = $(this).val();
            
            rd.reference = '';
            rd.reference_id = '';
            rd.amount = '';
            rd.drcr = '';
            rd.due_date = '';
            rd.narration = '';

            if(method != ''){
                var expected = expected_bills_amount(grid);
                rd.amount = expected.amount;
                rd.drcr = expected.drcr;
            }
        })
    };

    function expected_bills_amount(grid)
    {
        var amount = 0;
        var data = grid.option('dataModel.data');

        var master_amount = bbb_accounts[billIndex].amount; // 1000
        var master_drcr = bbb_accounts[billIndex].drcr; // D

        if(master_drcr == 'D')
            amount = parseAmount(master_amount);
        if(master_drcr == 'C')
            amount = -parseAmount(master_amount);

        data.forEach(function(row){
            if(row.amount != '' && row.drcr != ''){
                if(row.drcr == 'C')
                    amount  += parseAmount(row.amount);
                if(row.drcr == 'D')
                    amount  -= parseAmount(row.amount);
            }
        });

        if(amount < 0){
            return {amount: Math.abs(amount), drcr: 'C'};
        }
        if(amount > 0){
            return {amount: amount, drcr: 'D'};
        }

        return {amount: '', drcr: ''};
    }
                
    function referenceEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,
            grid = this;
            
        if(rd.method == 'Adjustment')
        {
            $inp.autocomplete({
                source:  bbb_accounts[billIndex].ref_list,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    rd.reference_id = ui.item.id;
                    rd.reference = ui.item.label;
                    rd.due_date = ui.item.due_date;
                }

            }).focus(function () {              
                $(this).autocomplete("search", "");
                rd.reference = '';
                rd.reference_id = '';
                rd.due_date = '';
            }).focusout(function () {              
                if(rd.reference != '' && rd.reference_id == '')
                {
                    var index = bbb_accounts[billIndex].ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == rd.reference.toLowerCase();
                    });
                    if(index > -1){
                        
                        var downKeyEvent = $.Event("keydown");
                        downKeyEvent.keyCode = $.ui.keyCode.DOWN;  // event for pressing "down" key
                    
                        var enterKeyEvent = $.Event("keydown");
                        enterKeyEvent.keyCode = $.ui.keyCode.ENTER;  // event for pressing "enter" key

                        $inp.val(bbb_accounts[billIndex].ref_list[index].value); 
                        $inp.trigger(downKeyEvent); 
                        $inp.trigger(downKeyEvent);
                        $inp.trigger(enterKeyEvent);
                    }
                    else{
                        rd.reference = '';
                        rd.reference_id = '';
                        rd.due_date = '';
                    }
                }
            });
        }
        if(rd.method == 'New Ref.')
        {
            $inp.on("focusout", function () {
                var reference = $(this).val();
                if(reference)
                {
                    var index = bbb_accounts[billIndex].ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == reference.toLowerCase();
                    });
                    if(index > -1){
                        alert('Refernce alredy exists. To use this reference change method to adjustment');
                        rd.reference = '';
                        
                    }
                }

            });
        }

        $inp.on("change", function (evt) {
            grid.refreshDataAndView();
        });
    }
                
    function calculateBillSummary() {
        var total = 0,
		amountfcTotal=0,
            sub = 0,
			subfc=0,
            data = this.option('dataModel.data');
        
        data.forEach(function(row){
            
            if(row.method != '' && row.reference != '' && row.amount != '' && row.drcr != '')
            {
                sub = 0;
				subfc=0;
                if(row.drcr == 'D'){
                    sub = parseAmount(row.amount);
					subfc = parseAmount(row.amountfc);
                }
                if(row.drcr == 'C'){
                    sub = -parseAmount(row.amount);
					subfc = parseAmount(row.amountfc);
                }
                total  += sub;
				amountfcTotal +=subfc
            }
        })
        var drcr = 'Dr';
        if(total < 0){
            drcr = 'Cr';
            total = -total;
        }
        
        var totalData = {
			amountfc  : parseAmount(amountfcTotal),
            amount : '',
            narration : formatAmount(total) + ' (' + drcr + ')',
            pq_rowcls : 'grid_footer_color',
            summaryRow: true
        }
        this.option('summaryData', [totalData]);
    }


                
    var methods = <?php echo json_encode($bills_method_list); ?>;
    var drcrlist     = [{"":""},{"C":"C"},{"D":"D"}];


    var bill_dataModel = {"data":generateBillData()} 
    var bill_colModel = [
        { title: "METHOD", dataIndx: "method", width: 100, cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: methods,
                init: methodEditor
            },
        },
        { title: "REFERENCE", width: 100, dataIndx: "reference" ,cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
                  type: "textbox",
                  init: referenceEditor
            },
            editable: function (ui) {
               var method = ui.rowData['method'];
                if (method != '') {
                    return true;
                }
                return false;
            },
        },
		{ title: "AMOUNT(₹)", sortable:false,width: 100, align: "right",hidden:true,dataIndx: "amountfc",dataType: "float",
                editable: function (ui) {
				 var reference = ui.rowData['reference'];
				  if (reference != '') {
					  return true;
				  }
				  return false;
			  },
				editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");                        
                        $inp.on("change", function (evt){
								var currency_id = $('select[name="currency_id"] option:selected').val();						
								var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
                                if(rd.amountfc != '' && parseAmount(rd.amountfc) >= 0)
                			    {
                				    if(parseAmount(fcy_forex_rate) > 0){
                					  if(parseFloat(fcy_forex_rate)!=0 && parseFloat(fcy_forex_rate) >0)
                					    rd.amount   = parseAmount((rd.amountfc)/fcy_forex_rate);	
                				     else
                				     	rd.amount   = parseAmount(0);                 								 
                				     }
                				   else{
                					  rd.amountfc = 0;
                					}	
                                }else
									rd.amountfc = 0;
							grid.refreshDataAndView();
                        })
                    }
                },
				
				render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
					if(rd.amountfc != ''){
					return formatAmount(parseAmount(rd.amountfc),currency_symbol);
					}
                }
				
              },
        { title: "AMOUNT", width: 100,  dataIndx: "amount" ,dataType: "float",
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.amount != ''){
                    rd.amount = parseAmount(rd.amount);
                    return formatAmount(rd.amount);   
                }
                return '';
            },
            editable: function (ui) {
               var reference = ui.rowData['reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "Dr/Cr", dataIndx: "drcr", width: 100, cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: drcrlist
            },
            editable: function (ui) {
               var reference = ui.rowData['reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "DUE DATE", dataIndx: "due_date", width: 100 ,dataType: 'string',
            editor: {
                type: 'textbox',
                init: dateEditor
            },
            editable: function (ui) {
               var reference = ui.rowData['reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "NARRATION", width: 100, dataType: "string", dataIndx: "narration",
            editable: function (ui) {
               var reference = ui.rowData['reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        }
    ];

    var billsObj = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, //disable maximize,toggle button.
        height: 'flex',
        selectionModel: { type: 'cell' }, 
        scrollModel: { autoFit: true },
        dataModel: bill_dataModel,
        colModel: bill_colModel,  
        pageModel: { type: 'local', rPP: 5 },
        numberCell: { show: true },
        change: calculateBillSummary,
        dataReady: calculateBillSummary,
        editable: true,
        cellSave: function(evt, ui){
           this.refresh();
        },
        editModel: {
            clicksToEdit: 1,
                keyUpDown: false
            },
        showTitle: true,
        create: function (evt, ui) {// make first row auto selected
            var grid = this,
            $select_row = $(".select-row"),
            data = ui.dataModel.data;
            grid.setSelection({ rowIndx: 0, focus: true });
        }
    };
                
    billsObj.cellKeyDown = function(evt, ui) {
       var rowData = ui.rowData;
       var rowIndx = ui.rowIndx;

       if (evt.keyCode == 46){
            $.each(rowData, function(index,obj){
                rowData[index] = '';
            });
            this.refreshDataAndView();
            return false;
       }
    }        

       $("#billsModal").on('shown.bs.modal', function () {   
        if($("#bill_by_bill_grid").pqGrid('instance')){     
            $("#bill_by_bill_grid").pqGrid('refresh');
        }
        else
            $("#bill_by_bill_grid").pqGrid(billsObj);
        });
		
		var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
			
		if(currency_symbol != '₹'){
			bill_colModel[2].hidden=false;
			bill_colModel[2].width= 150;
			bill_colModel[2].title='AMOUNT('+currency_symbol+')';
			if($("#bill_by_bill_grid").pqGrid('instance')){   
			$("#bill_by_bill_grid").pqGrid( "option", "colModel", bill_colModel );             
			$("#bill_by_bill_grid").pqGrid( {autoFit: true} );
			$("#bill_by_bill_grid").pqGrid("refreshCM");
			$("#bill_by_bill_grid").pqGrid("refresh");
			}						
		  }
		  else{
			 bill_colModel[2].hidden = true;
			 bill_colModel[2].width  = 150;
			 bill_colModel[2].title  = 'AMOUNT('+currency_symbol+')';  
			if($("#bill_by_bill_grid").pqGrid('instance')){ 
			 $("#bill_by_bill_grid").pqGrid( "option", "colModel", bill_colModel );             
			 $("#bill_by_bill_grid").pqGrid( {autoFit: true} );
			 $("#bill_by_bill_grid").pqGrid("refreshCM");
			 $("#bill_by_bill_grid").pqGrid("refresh");
			}
		  }
		  
/***********  End of Bill By Bill **********************/	


 </script>
</body>
</html>