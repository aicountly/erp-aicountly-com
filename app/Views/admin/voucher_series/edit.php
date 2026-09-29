<?php $header = array('title' => 'Edit Voucher Series' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
<?php
$series            = $series_info['series_info'];
$series_type       = $series_info['series_type']; // a or m
if($series_type=='a'){
$auto_series_info  = $series_info['seriesam_info'];
$manual_info       = array('vch_series_blank'=>'');
}
if($series_type=='m'){
$auto_series_info  = array('comp_vch_renum_freq'=>'0','comp_vch_prefix'=>'','comp_vch_suffix'=>'',
                           'comp_vch_start'=>'','comp_vch_no_length'=>'','comp_vch_no_padding'=>''
						   );
$manual_info       = $series_info['seriesam_info'];	
}

?>

<div id="validation_errors"></div>

<form action="" method="post" id="myform" name="myform" class="needs-validation myform" novalidate>
    
<div class=" row">
    
    <div class="col-6"><h3 class="pb-3">Update Voucher Series</h3></div> 
    <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
    
    <div class="col-md-12">
        <?php if (session()->getFlashdata('error_message')) { ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <?php echo session()->getFlashdata('error_message'); ?>
                </div>
        <?php } ?>
    </div>
    
    
    <div class="col-md-12">
        <div class="card p-4 my-2">
            <h5 class="pb-2">General Info</h5>       
            
            <div class="col-12">
                <label>Series Name <span class="red">*</span></label>
                <input type="text" name="comp_vch_series" value="<?= $series['vch_series_name'] ?>" class="form-control" required>
            </div>            
            <div class="col-12">
                <label>Mark Default Bank</label>
					<?php	
					if($banks_dropdown)
					echo form_dropdown('comp_bank_id', $banks_dropdown,$series['bank_id'],'id="comp_bank_id" class="form-control w-75" ');
				   else
					    echo 'No bank found';
					   ?>  
            </div>
            <div class="col-12">
                <label>Voucher Type <span class="red">*</span></label>
                    <select class="form-select" name="voucher_type_id" style="display:none;">
                        <?php foreach ($voucher_types as $key => $value) { 
							if($value['vch_type_id']=='6')
                                 $vname ='Inward Challan';								 
							  else if($value['vch_type_id']=='7')
                                 $vname ='Delivery Challan';
							  else
								 $vname =$value['vch_name']; 
							 if($series['vch_type_id'] == $value['vch_type_id']){
								 $show_voucher_name = $vname;
						?>
                            <option <?= $series['vch_type_id'] == $value['vch_type_id'] ? 'selected' : '' ?> value="<?= $value['vch_type_id'] ?>"><?= $vname ?></option>
                        <?php } } ?>
                    </select>
<?php echo $show_voucher_name;?>					
            </div>

            <div class="col-12">
                <label>Voucher Numbering <span class="red">*</span></label>
                <div class="input-group w-75">
                    <div class="input-group-text py-1">
                        <input type="radio" name="comp_vch_method" value="1" id="automatic" class="form-check-input"  <?= $series['vch_series_method'] == '1' ? 'checked' : '' ?> required>&nbsp;
                        <label for="automatic">Automatic</label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="comp_vch_method" value="0" id="manual" class="form-check-input" <?= $series['vch_series_method'] == '0' ? 'checked' : '' ?> required>&nbsp;
                        <label for="manual">Manual</label>
                    </div> 
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-12 numbering_config" id="manual_numbering_config" <?= $series['vch_series_method'] == '0' ? '' : 'style="display: none;"' ?> >
        <div class="card p-4 my-2">
            <h5 class="pb-2">Manual Numbering  Configuration</h5>        
            <div class="col-12">
                <label>Allow Blank Bill No.</label>
                <div class="input-group w-75">
                    <div class="input-group-text py-1">
                        <input type="radio" name="allowblank_no" value="1" id="allowblank_no_yes" class="form-check-input" <?= $manual_info['vch_series_blank'] == '1' ? 'checked' : '' ?>>&nbsp;
                        <label for="allowblank_no_yes">Yes</label>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        <input type="radio" name="allowblank_no" value="0" id="allowblank_no_no" class="form-check-input" <?= $manual_info['vch_series_blank'] == '' ? 'checked' : '' ?>>&nbsp;
                        <label for="allowblank_no_no">No</label>
                    </div> 
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-12 numbering_config" id="automatic_numbering_config" <?= $series['vch_series_method'] == '1' ? '' : 'style="display: none;"' ?>>
        <div class="card p-4 my-2">
            <h5 class="pb-2">Automatic Numbering  Configuration</h5>
        <div class="row">
            <div class="col-8">
                <label>Renumbering Frequency</label>
                <div class="input-group w-75">
                    <div class="input-group-text py-1">
					 <div class="form-check form-check-inline">
					 <?php if(isset($auto_series_info['vch_series_renum']))
						     $comp_vch_renum_freq= $auto_series_info['vch_series_renum'];
						   else
							  $comp_vch_renum_freq= 0;
?>						  
					  <input class="form-check-input" type="radio" name="comp_vch_renum_freq" id="comp_vch_renum_freq_none" value="0" <?= $comp_vch_renum_freq == '0' ? 'checked' : '' ?>>
					  <label  for="comp_vch_renum_freq_none">None</label>&nbsp;&nbsp;
					</div>
					 <div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio" name="comp_vch_renum_freq" id="comp_vch_renum_freq_yearly" value="1" <?= $comp_vch_renum_freq == '1' ? 'checked' : '' ?>>
					  <label  for="comp_vch_renum_freq_yearly">Yearly</label>&nbsp;&nbsp;
					</div>
					 <div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio" name="comp_vch_renum_freq" id="comp_vch_renum_freq_halfyearly" value="2" <?= $comp_vch_renum_freq == '2' ? 'checked' : '' ?>>
					  <label  for="comp_vch_renum_freq_halfyearly">Half Yearly</label>&nbsp;&nbsp;
					</div>
					 <div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio" name="comp_vch_renum_freq" id="comp_vch_renum_freq_quaterly" value="3" <?= $comp_vch_renum_freq == '3' ? 'checked' : '' ?>>
					  <label  for="comp_vch_renum_freq_quaterly">Quaterly</label>&nbsp;&nbsp;
					</div>
					<div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio" name="comp_vch_renum_freq" id="comp_vch_renum_freq_daily" value="4" <?= $comp_vch_renum_freq == '4' ? 'checked' : '' ?>>
					  <label  for="comp_vch_renum_freq_daily">Daily</label>
					</div>
				        
                    </div> 
                </div>
            </div>
			<div class="col-4">
			 <h4>Series Format: <span id="series_format"></span> </h4> 
			</div>
			</div>

            <div class="col-12" id="identoificvation_div" style="<?= $comp_vch_renum_freq >0 ? '' : 'display:none;' ?>">
                <label>Whether To Show Year / Half Year / Quarter / Daily Identification In Suffix Or Prefix <span class="red">*</span></label>
                <div class="input-group w-75">
                    <div class="input-group-text py-1">
                        <input type="radio" name="embed_type" value="P" id="embed_type_prefix" class="form-check-input">&nbsp;
                        <label for="embed_type_prefix">As a Prefix</label>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        <input type="radio" name="embed_type" value="S" id="embed_type_suffix" class="form-check-input">&nbsp;
                        <label for="embed_type_suffix">As a Suffix</label>
                    </div> 
                </div>
            </div>

          <div class="row">
                <div class="col-md-6" id="embeddedformat_div" style="<?= $comp_vch_renum_freq >0 ? '' : 'display:none;' ?>">
                    <div class="col-12">
                        <label>Embedded Format <span class="red">*</span></label>
						<select name="embed_format" id="embed_format" class="form-control">
						<option value=""></option>
						</select>                        
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-12">
                        <label>Starting No.</label>
						<?php if(isset($auto_series_info['vch_series_start'])){
							$comp_vch_start=$auto_series_info['vch_series_start'];
						}else {$comp_vch_start='';}
						?>
                        <input type="text" name="comp_vch_start" class="form-control" value="<?= $comp_vch_start;?>" minlength="1" maxlength="15" onkeyup="this.value=this.value.replace(/[^\d]/,'')" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="col-12">
                        <label>Padding</label>
						<?php if(isset($auto_series_info['vch_series_padding'])){
							$comp_vch_no_padding=$auto_series_info['vch_series_padding'];
						}else {$comp_vch_no_padding='';}
						?>
                        <input type="text" name="comp_vch_no_padding" value="<?= $comp_vch_no_padding;?>" class="form-control" minlength="1" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/,'')" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-12">
                        <label>Minimum Fixed Length of Numeric Part</label>
						<?php if(isset($auto_series_info['vch_series_length'])){
							$comp_vch_no_length=$auto_series_info['vch_series_length'];
						}else {$comp_vch_no_length='';}
						?>
                        <input type="text" name="comp_vch_no_length" id="comp_vch_no_length" value="<?= $comp_vch_no_length;?>" class="form-control" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
				<div class="row">
                    <div class="col-6">
                        <label>Prefix</label>
						<?php if(isset($auto_series_info['vch_series_prefix'])){
							$comp_vch_prefix=$auto_series_info['vch_series_prefix'];
						}else {$comp_vch_prefix='';}
						?>
                        <input type="text" name="comp_vch_prefix" id="comp_vch_prefix" maxlength="6" value="<?= substr($comp_vch_prefix,0,-1);?>" autocomplete="off" class="form-control">
                    </div>
					 <div class="col-6">
                        <label>Seperator</label>
						<?php
						$prefix_seperator = substr($comp_vch_prefix, -1);
						$backslash= "\ ";
						?>
						<select class="form-select" name="prefix_seperator" id="prefix_seperator">
                        <option value="-" <?= $prefix_seperator == '-' ? 'selected' : '' ?>>-</option>
						 <option value="/" <?= $prefix_seperator == '/' ? 'selected' : '' ?>>/</option>
						  <option value="\" <?= $prefix_seperator == trim($backslash) ? 'selected' : '' ?>>\</option>
						</select>                       
                    </div>
					</div>
                </div>
                <div class="col-md-6">
				<div class="row">
                    <div class="col-6">
                        <label>Suffix</label>
						<?php if(isset($auto_series_info['vch_series_suffix'])){
							$comp_vch_suffix=$auto_series_info['vch_series_suffix'];
						}else {$comp_vch_suffix='';}
						?>
                        <input type="text" name="comp_vch_suffix" id="comp_vch_suffix" value="<?= substr($comp_vch_suffix,1);?>" maxlength="6" autocomplete="off" class="form-control">
                    </div>
					
					 <div class="col-6">
					 <?php
						$suffix_seperator = substr($comp_vch_suffix, -1);
						$backslash= "\ ";
						?>
                        <label>Seperator</label>
						<select class="form-select" name="suffix_seperator" id="suffix_seperator">
                        <option value="-" <?= $prefix_seperator == '-' ? 'selected' : '' ?>>-</option>
						 <option value="/" <?= $prefix_seperator == '/' ? 'selected' : '' ?>>/</option>
						  <option value="\" <?= $prefix_seperator == trim($backslash) ? 'selected' : '' ?>>\</option>
						</select>                       
                    </div>
					</div>
					
                </div>
            </div>

        </div>
    </div>
    

    
    <div class="col-md-12  text-center">
        <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
        <a href="<?php echo $base_url.'voucher_series';?>" class="btn btn-secondary">QUIT</a>
    </div>
    
    
    
</div>
</form>

<?php echo view('includes/footer_scripts'); ?>
<?php
$calendaer_data   = fy_calender_js(); 
$half_years       = $calendaer_data['half_years'];
$quarters         = $calendaer_data['quarters'];
$current_date     = strtotime(date('Y-m-d'));
$quarters1        = $quarters[0]; 
$quarters2        = $quarters[1]; 
$quarters3        = $quarters[2]; 
$quarters4        = $quarters[3];  

$year1start      = $quarters1['from_date'];
$year1end        = $quarters1['to_date'];
$qr1short_start_month= date('m',strtotime($year1start));
$qr1short_end_month  = date('m',strtotime($year1end));	
$qr1startend      = $qr1short_start_month.'-'.$qr1short_end_month;

$year2start      = $quarters2['from_date'];
$year2end        = $quarters2['to_date'];
$qr2short_start_month= date('m',strtotime($year2start));
$qr2short_end_month  = date('m',strtotime($year2end));	
$qr2startend      = $qr2short_start_month.'-'.$qr2short_end_month;

$year3start      = $quarters3['from_date'];
$year3end        = $quarters3['to_date'];
$qr3short_start_month= date('m',strtotime($year3start));
$qr3short_end_month  = date('m',strtotime($year3end));	
$qr3startend      = $qr3short_start_month.'-'.$qr3short_end_month;

$year4start      = $quarters4['from_date'];
$year4end        = $quarters4['to_date'];
$qr4short_start_month= date('m',strtotime($year4start));
$qr4short_end_month  = date('m',strtotime($year4end));	
$qr4startend      = $qr4short_start_month.'-'.$qr4short_end_month; 

$short_start_year = date('y',strtotime($calendaer_data['from_date']));
$short_end_year   = date('y',strtotime($calendaer_data['to_date']));
$long_start_year  = date('Y',strtotime($calendaer_data['from_date']));
$long_end_year    = date('Y',strtotime($calendaer_data['to_date']));
$year1_start      = $half_years[0]['from_date'];
$year1_end        = $half_years[0]['to_date'];
$year2_start      = $half_years[1]['from_date'];
$year2_end        = $half_years[1]['to_date'];
$short_start_month= date('m',strtotime($year1_start));
$short_end_month  = date('m',strtotime($year2_start));		   
$long_start_month = date('M',strtotime($year1_start));
$long_end_month   = date('M',strtotime($year2_start));	
$short_day        = date('d');
$long_month       = date('M');
$short_month      = date('m');



$qr_short_start_month= '';
$qr_short_end_month  = '';		   
$qr_long_start_month = '';
$qr_long_end_month   = '';

if(($current_date>=strtotime($quarters1['from_date'])) && ($current_date<=strtotime($quarters1['to_date'])) ){
$year1_start      = $quarters1['from_date'];
$year1_end        = $quarters1['to_date'];

$qr_short_start_month= date('m',strtotime($year1_start));
$qr_short_end_month  = date('m',strtotime($year1_end));		   
$qr_long_start_month = date('M',strtotime($year1_start));
$qr_long_end_month   = date('M',strtotime($year1_start));	
$short_day        = date('d',strtotime($year1_start));
$long_month       = date('M',strtotime($year1_start));
$short_month      = date('m',strtotime($year1_start));
}
if(($current_date>=strtotime($quarters2['from_date'])) && ($current_date<=strtotime($quarters2['to_date'])) ){
$year1_start      = $quarters2['from_date'];
$year1_end        = $quarters2['to_date'];

$qr_short_start_month= date('m',strtotime($year1_start));
$qr_short_end_month  = date('m',strtotime($year1_end));		   
$qr_long_start_month = date('M',strtotime($year1_start));
$qr_long_end_month   = date('M',strtotime($year1_end));
}
if(($current_date>=strtotime($quarters3['from_date'])) && ($current_date<=strtotime($quarters3['to_date'])) ){
$year1_start      = $quarters3['from_date'];
$year1_end        = $quarters3['to_date'];

$qr_short_start_month= date('m',strtotime($year1_start));
$qr_short_end_month  = date('m',strtotime($year1_end));		   
$qr_long_start_month = date('M',strtotime($year1_start));
$qr_long_end_month   = date('M',strtotime($year1_end));
	
}
if(($current_date>=strtotime($quarters4['from_date'])) && ($current_date<=strtotime($quarters4['to_date'])) ){
$year1_start      = $quarters4['from_date'];
$year1_end        = $quarters4['to_date'];
$qr_short_start_month= date('m',strtotime($year1_start));
$qr_short_end_month  = date('m',strtotime($year1_end));		   
$qr_long_start_month = date('M',strtotime($year1_start));
$qr_long_end_month   = date('M',strtotime($year1_end));	

}			
?>
<script>
var short_start_year='<?php echo $short_start_year;?>';
var short_end_year='<?php echo $short_end_year;?>';
var long_start_year='<?php echo $long_start_year;?>';
var long_end_year='<?php echo $long_end_year;?>';

var short_start_month='<?php echo $short_start_month;?>';
var short_end_month='<?php echo $short_end_month;?>';
var long_start_month='<?php echo $long_start_month;?>';
var long_end_month='<?php echo $long_end_month;?>';

var short_day='<?php echo $short_day;?>';
var long_month='<?php echo $long_month;?>';
var short_month='<?php echo $short_month;?>';

var qr_short_start_month= '<?php echo $qr_short_start_month;?>';
var qr_short_end_month= '<?php echo $qr_short_end_month;?>';
var qr_long_start_month= '<?php echo $qr_long_start_month;?>';
var qr_long_end_month= '<?php echo $qr_long_end_month;?>';

var qr1startend='<?php echo $qr1startend;?>';
var qr2startend='<?php echo $qr2startend;?>';
var qr3startend='<?php echo $qr3startend;?>';
var qr4startend='<?php echo $qr4startend;?>';




load_series_format();

function load_series_format(){
	
	var prefix='';
	var suffix='';
	var prefixval='';
	var suffixval='';
	var comp_vch_no_padding = $('input[name="comp_vch_no_padding"]').val();
	var comp_vch_start      =  $('input[name="comp_vch_start"]').val();
	var comp_vch_renum_freq = $('input[name="comp_vch_renum_freq"]:checked').val();
	var embed_type          = $('input[name="embed_type"]:checked').val();
	var comp_vch_no_length  = $('input[name="comp_vch_no_length"]').val();
	if(comp_vch_renum_freq>0){
	 var embed_format = $('select[name="embed_format"] option:selected').val();
	  if(embed_format!='' && embed_type=='P'){
		 if(comp_vch_renum_freq=='1'){// yearly
			if(embed_format=='YY-YY'){
				var prefix_label = short_start_year+'-'+short_end_year;
			}
           else if(embed_format=='YYYY-YY'){
			 var prefix_label = long_start_year+'-'+short_end_year;	
			}
			else if(embed_format=='YY/YY'){
			 var prefix_label = short_start_year+'/'+short_end_year;		
			}
			else if(embed_format=='YYYY/YY'){
			 var prefix_label = long_start_year+'/'+short_end_year;	
			}
			else{
				var prefix_label =  $('input[name="comp_vch_prefix"]').val();
			}	
		  }
		else if(comp_vch_renum_freq=='2'){// half yearly
		 if(embed_format=='MM-MM'){
				var prefix_label = short_start_month+'-'+short_end_month;
			}
           else if(embed_format=='MMM-MMM'){
			 var prefix_label = long_start_month+'-'+long_end_month;	
			}
		  else if(embed_format=='MM/MM'){
			 var prefix_label = short_start_month+'/'+short_end_month;		
			}
		  else	if(embed_format=='MMM/MMM'){
			 var prefix_label = long_start_month+'/'+long_end_month;	
			}
		  else{
				var prefix_label = $('input[name="comp_vch_prefix"]').val();
			}
		}
		else if(comp_vch_renum_freq=='3'){// Quaterly
		 if(embed_format=='MM-MM'){
				var prefix_label = qr_short_start_month+'-'+qr_short_end_month;
			}
           else if(embed_format=='MMM-MMM'){
			 var prefix_label = qr_long_start_month+'-'+qr_long_end_month;	
			}
		  else if(embed_format=='MM/MM'){
			 var prefix_label = qr_short_start_month+'/'+qr_short_end_month;		
			}
		  else	if(embed_format=='MMM/MMM'){
			 var prefix_label = qr_long_start_month+'/'+qr_long_end_month;	
			}		  
		  else if(embed_format=='Q1/Q2/Q3/Q4'){
			 var prefix_label = qr1startend+'/'+qr2startend+'/'+qr3startend+'/'+qr4startend;	
			}	
		  else{
				var prefix_label = $('input[name="comp_vch_prefix"]').val();
			}
		}
		else if(comp_vch_renum_freq=='4'){// daily
		 if(embed_format=='DD-MMM'){
			 var prefix_label= short_day+'-'+long_month;
			}
            else if(embed_format=='DD/MM'){
			 var prefix_label = short_day+'/'+short_month;	
			}
			else if(embed_format=='DD/MMM'){
			 var prefix_label = short_day+'/'+long_month;	
			} 
			else{
				var prefix_label =  $('input[name="comp_vch_prefix"]').val()
			}
		}
		else if(comp_vch_renum_freq=='0' || comp_vch_renum_freq==''){
			var prefix_label = $('input[name="comp_vch_prefix"]').val();
		 } 
		  
		  
		var prefix =prefix_label+""+$('select[name="prefix_seperator"] option:selected').val();  
		  
	  }
	  if(embed_format!='' && embed_type=='S'){
		 if(comp_vch_renum_freq=='1'){// yearly
			if(embed_format=='YY-YY'){
				var suffix_label = short_start_year+'-'+short_end_year;
			}
           else if(embed_format=='YYYY-YY'){
			 var suffix_label = long_start_year+'-'+short_end_year;	
			}
			else if(embed_format=='YY/YY'){
			 var suffix_label = short_start_year+'/'+short_end_year;		
			}
			else if(embed_format=='YYYY/YY'){
			 var suffix_label = long_start_year+'/'+short_end_year;	
			}
			else{
				var suffix_label =  $('input[name="comp_vch_suffix"]').val();
			}	
		  }
		else if(comp_vch_renum_freq=='2'){// half yearly
		 if(embed_format=='MM-MM'){
				var suffix_label = short_start_month+'-'+short_end_month;
			}
           else if(embed_format=='MMM-MMM'){
			 var suffix_label = long_start_month+'-'+long_end_month;	
			}
		  else if(embed_format=='MM/MM'){
			 var suffix_label = short_start_month+'/'+short_end_month;		
			}
		  else	if(embed_format=='MMM/MMM'){
			 var suffix_label = long_start_month+'/'+long_end_month;	
			}
		  else{
				var suffix_label = $('input[name="comp_vch_suffix"]').val();
			}
		}
		else if(comp_vch_renum_freq=='3'){// Quaterly
		 if(embed_format=='MM-MM'){
				var suffix_label = qr_short_start_month+'-'+qr_short_end_month;
			}
           else if(embed_format=='MMM-MMM'){
			 var suffix_label = qr_long_start_month+'-'+qr_long_end_month;	
			}
		  else if(embed_format=='MM/MM'){
			 var suffix_label = qr_short_start_month+'/'+qr_short_end_month;		
			}
		  else	if(embed_format=='MMM/MMM'){
			 var suffix_label = qr_long_start_month+'/'+qr_long_end_month;	
			}		  
		  else if(embed_format=='Q1/Q2/Q3/Q4'){
			 var suffix_label = qr1startend+'/'+qr2startend+'/'+qr3startend+'/'+qr4startend;	
			}	
		  else{
				var suffix_label = $('input[name="comp_vch_suffix"]').val();
			}
		}
		else if(comp_vch_renum_freq=='4'){// daily
		 if(embed_format=='DD-MMM'){
			 var suffix_label= short_day+'-'+long_month;
			}
            else if(embed_format=='DD/MM'){
			 var suffix_label = short_day+'/'+short_month;	
			}
			else if(embed_format=='DD/MMM'){
			 var suffix_label = short_day+'/'+long_month;	
			} 
			else{
				var suffix_label =  $('input[name="comp_vch_suffix"]').val();
			}
		}
		else if(comp_vch_renum_freq=='0' || comp_vch_renum_freq==''){
			var suffix_label =  $('input[name="comp_vch_suffix"]').val();
		 } 
		var suffix =$('select[name="suffix_seperator"]').val()+suffix_label;  
		  
	  }
		
	}
	
	
	if( $("#comp_vch_prefix").val()!=''){
		var prefixval = $('input[name="comp_vch_prefix"]').val();
		if(comp_vch_renum_freq=='1'){// yearly
			if(prefixval=='YY-YY'){
				var prefix_label = short_start_year+'-'+short_end_year;
			}
           else if(prefixval=='YYYY-YY'){
			 var prefix_label = long_start_year+'-'+short_end_year;	
			}
			else if(prefixval=='YY/YY'){
			 var prefix_label = short_start_year+'/'+short_end_year;		
			}
			else if(prefixval=='YYYY/YY'){
			 var prefix_label = long_start_year+'/'+short_end_year;	
			}
			else{
				var prefix_label =  $('input[name="comp_vch_prefix"]').val();
			}	
		  }
		else if(comp_vch_renum_freq=='2'){// half yearly
		 if(prefixval=='MM-MM'){
				var prefix_label = short_start_month+'-'+short_end_month;
			}
           else if(prefixval=='MMM-MMM'){
			 var prefix_label = long_start_month+'-'+long_end_month;	
			}
		  else if(prefixval=='MM/MM'){
			 var prefix_label = short_start_month+'/'+short_end_month;		
			}
		  else	if(prefixval=='MMM/MMM'){
			 var prefix_label = long_start_month+'/'+long_end_month;	
			}
		  else{
				var prefix_label = $('input[name="comp_vch_prefix"]').val();
			}
		}
		else if(comp_vch_renum_freq=='3'){// Quaterly
		 if(prefixval=='MM-MM'){
				var prefix_label = qr_short_start_month+'-'+qr_short_end_month;
			}
           else if(prefixval=='MMM-MMM'){
			 var prefix_label = qr_long_start_month+'-'+qr_long_end_month;	
			}
		  else if(prefixval=='MM/MM'){
			 var prefix_label = qr_short_start_month+'/'+qr_short_end_month;		
			}
		  else	if(prefixval=='MMM/MMM'){
			 var prefix_label = qr_long_start_month+'/'+qr_long_end_month;	
			}		  
		  else if(prefixval=='Q1/Q2/Q3/Q4'){
			 var prefix_label = qr1startend+'/'+qr2startend+'/'+qr3startend+'/'+qr4startend;	
			}	
		  else{
				var prefix_label = $('input[name="comp_vch_prefix"]').val();
			}
		}
		else if(comp_vch_renum_freq=='4'){// daily
		 if(prefixval=='DD-MMM'){
			 var prefix_label= short_day+'-'+long_month;
			}
            else if(prefixval=='DD/MM'){
			 var prefix_label = short_day+'/'+short_month;	
			}
			else if(prefixval=='DD/MMM'){
			 var prefix_label = short_day+'/'+long_month;	
			} 
			else{
				var prefix_label =  $('input[name="comp_vch_prefix"]').val();
			}
		}
		else if(comp_vch_renum_freq=='0' || comp_vch_renum_freq==''){
			var prefix_label =  $('input[name="comp_vch_prefix"]').val();
		 }
		
		var prefix =prefix_label+""+$('select[name="prefix_seperator"] option:selected').val();
	}
	if( $("#comp_vch_suffix").val()!=''){
		var suffixval = $('input[name="comp_vch_suffix"]').val();
		if(comp_vch_renum_freq=="1"){// yearly
			if(suffixval=='YY-YY'){
			 var suffix_label= short_start_year+'-'+short_end_year;
			}
           else if(suffixval=='YYYY-YY'){
			 var suffix_label = long_start_year+'-'+short_end_year;	
			}
		  else if(suffixval=='YY/YY'){
			 var suffix_label = short_start_year+'/'+short_end_year;		
			}
			else if(suffixval=='YYYY/YY'){
			  suffix_label = long_start_year+'/'+short_end_year;	
			}	
			else{
			var suffix_label =  $('input[name="comp_vch_suffix"]').val();
		    }
		}
		else if(comp_vch_renum_freq=='2'){// half yearly
		    if(suffixval=='MM-MM'){
				var suffix_label = short_start_month+'-'+short_end_month;
			}
           else if(suffixval=='MMM-MMM'){
			 var suffix_label = long_start_month+'-'+long_end_month;	
			}
			else if(suffixval=='MM/MM'){
			 var suffix_label = short_start_month+'/'+short_end_month;		
			}
			else if(suffixval=='MMM/MMM'){
			 var suffix_label = long_start_month+'/'+long_end_month;	
			}	
			else{
			var suffix_label =  $('input[name="comp_vch_suffix"]').val();
		    }	
			
		}
		
		else if(comp_vch_renum_freq=='3'){// Quaterly
		 if(suffixval=='MM-MM'){
				var suffix_label = qr_short_start_month+'-'+qr_short_end_month;
			}
           else if(suffixval=='MMM-MMM'){
			 var suffix_label = qr_long_start_month+'-'+qr_long_end_month;	
			}
		  else if(suffixval=='MM/MM'){
			 var suffix_label = qr_short_start_month+'/'+qr_short_end_month;		
			}
		  else	if(suffixval=='MMM/MMM'){
			 var suffix_label = qr_long_start_month+'/'+qr_long_end_month;	
			}		  
		  else if(suffixval=='Q1/Q2/Q3/Q4'){
			 var suffix_label = qr1startend+'/'+qr2startend+'/'+qr3startend+'/'+qr4startend;	
			}	
		  else{
				var suffix_label = $('input[name="comp_vch_suffix"]').val();
			}
		  }
		else if(comp_vch_renum_freq=='4'){// daily
		  if(suffixval=='DD-MMM'){
			 var suffix_label= short_day+'-'+$long_month;
			}
            else if(suffixval=='DD/MM'){
			 var suffix_label = short_day+'/'+$short_month;	
			}
			else if(suffixval=='DD/MMM'){
			 var suffix_label = short_day+'/'+$long_month;	
			} 
			else{
			var suffix_label = $('input[name="comp_vch_suffix"]').val();
		    }
		}else if(comp_vch_renum_freq=='0' || comp_vch_renum_freq==''){
			var suffix_label = $('input[name="comp_vch_suffix"]').val();
		 }
		
		var suffix =$('select[name="suffix_seperator"]').val()+""+suffix_label;
	}
	
	
	 var startingnumber = String(comp_vch_start).padStart(comp_vch_no_length, comp_vch_no_padding);
	
	
	var series_format = prefix+startingnumber+suffix;
	
	
	$("#series_format").html(series_format);	
}

function isAlphanumeric(str) {
  return /^[a-zA-Z0-9]+$/.test(str);
 }
var yearly_predf_identifications=["YY-YY","YYYY-YY","YY/YY","YYYY/YY"];
var halfyearly_predf_identifications=["MM-MM","MMM-MMM","MM/MM","MMM/MMM"];
var quaterly_predf_identifications=["MM-MM","MMM-MMM","MM/MM","MMM/MMM","Q1/Q2/Q3/Q4"];
var daily_predf_identifications=["DD-MMM","DD/MM","DD/MMM"];




$(document).on('blur', 'input[name="comp_vch_prefix"]', function(){
load_series_format();
});
$(document).on('blur', 'input[name="comp_vch_suffix"]', function(){
load_series_format();
});
$(document).on('change', 'select[name="prefix_seperator"]', function(){
load_series_format();
});
$(document).on('change', 'select[name="suffix_seperator"]', function(){
load_series_format();
});
$(document).on('blur', 'input[name="comp_vch_start"]', function(){
load_series_format();
});
$(document).on('blur', 'input[name="comp_vch_no_padding"]', function(){
load_series_format();
});
$(document).on('blur', 'input[name="comp_vch_no_length"]', function(){
load_series_format();
});
$(document).on('blur', 'select[name="embed_format"]', function(){
load_series_format();
});


$(document).on('change', 'select[name="voucher_type_id"]', function(){
	var voucher_type_id = $(this).val();
	// for all purchase voucher types only manual option will enable,
		if(voucher_type_id=='11' || voucher_type_id=='12' || voucher_type_id=='3' || voucher_type_id=='6' || voucher_type_id=='21'){
			$('.numbering_config').css('display', 'none');
            $('#automatic_numbering_config').css('display', 'none');	
			$("#manual").prop("checked",true);
			$("#automatic").prop("checked",false);			
            $('#manual_numbering_config').css('display', 'block');				
			}
		else if(voucher_type_id=='2' || voucher_type_id=='7' || voucher_type_id=='17' || voucher_type_id=='18' || voucher_type_id=='19'){
			$('.numbering_config').css('display', 'none');
            $('#automatic_numbering_config').css('display', 'block');				
			$("#automatic").prop("checked",true);			
            $('#manual_numbering_config').css('display', 'none');
			$("#manual").prop("checked",false);				
		}else{
			$('.numbering_config').css('display', 'none');
            $('#automatic_numbering_config').css('display', 'block');
			$("#manual").prop("checked",false);
			$("#automatic").prop("checked",true);
			}
      });
	  
	  load_embedd_format_dropdown();
	function load_embedd_format_dropdown(){
		var freq    = $('input[name="comp_vch_renum_freq"]:checked').val();
		  html = '<option value=""></option>';
		 if(freq>0){
			  if(freq=="1"){
				  var embed_format = $('#embed_format');
				     
					$.each(yearly_predf_identifications, function(val,text) {
						 html += '<option value="'+text+'">'+text+'</option>';
					}); 
				embed_format.html(html);	
			    }
			 if(freq=="2"){
				  var embed_format = $('#embed_format');
					$.each(halfyearly_predf_identifications, function(val,text) {						
						html += '<option value="'+text+'">'+text+'</option>';
					});
				embed_format.html(html);		
			    }
			 if(freq=="3"){
				  var embed_format = $('#embed_format');
					$.each(quaterly_predf_identifications, function(val,text) {						
						html += '<option value="'+text+'">'+text+'</option>';
					});
				embed_format.html(html);	
			    }
			if(freq=="4"){
				  var embed_format = $('#embed_format');
					$.each(daily_predf_identifications, function(val,text) {
						html += '<option value="'+text+'">'+text+'</option>';
					}); 
					embed_format.html(html);
			    }	
			 $("#identoificvation_div").show();$("#embeddedformat_div").show();
		 }else{
			 $("#identoificvation_div").hide();$("#embeddedformat_div").hide();
		 }	
	}
	$(document).on('change', 'input[name="comp_vch_renum_freq"]', function(){
		 $('input[name="comp_vch_prefix"]').prop("disabled",false);
		 $('input[name="comp_vch_suffix"]').prop("disabled",false);	
		 $('input[name="embed_type"]').prop("checked",false);
		 $("#series_format").html("");

		 var freq = $(this).val();
		  html = '<option value=""></option>';
		 if(freq>0){
			  if(freq=="1"){
				  var embed_format = $('#embed_format');
				     
					$.each(yearly_predf_identifications, function(val,text) {
						 html += '<option value="'+text+'">'+text+'</option>';
					}); 
				embed_format.html(html);	
			    }
			 if(freq=="2"){
				  var embed_format = $('#embed_format');
					$.each(halfyearly_predf_identifications, function(val,text) {						
						html += '<option value="'+text+'">'+text+'</option>';
					});
				embed_format.html(html);		
			    }
			 if(freq=="3"){
				  var embed_format = $('#embed_format');
					$.each(quaterly_predf_identifications, function(val,text) {						
						html += '<option value="'+text+'">'+text+'</option>';
					});
				embed_format.html(html);	
			    }
			if(freq=="4"){
				  var embed_format = $('#embed_format');
					$.each(daily_predf_identifications, function(val,text) {
						html += '<option value="'+text+'">'+text+'</option>';
					}); 
					embed_format.html(html);
			    }	
			 $("#identoificvation_div").show();$("#embeddedformat_div").show();
		 }else{
			 $("#identoificvation_div").hide();$("#embeddedformat_div").hide();
		 }
		
	});
	  
    $(document).on('change', 'input[name="comp_vch_method"]', function(){
        var method = $(this).val();
        if(method == '1'){
            var voucher_type_id = $('select[name="voucher_type_id"] option:selected').val();
			// for all purchase voucher types only manual option will enable,
			if(voucher_type_id=='11' || voucher_type_id=='12' || voucher_type_id=='3' || voucher_type_id=='6' || voucher_type_id=='21'){
			$('.numbering_config').css('display', 'none');
            $('#automatic_numbering_config').css('display', 'none');	
			$("#manual").prop("checked",true);
			$(this).prop("checked",false);			
            $('#manual_numbering_config').css('display', 'block');
				
			}
		else if(voucher_type_id=='2' || voucher_type_id=='7' || voucher_type_id=='17' || voucher_type_id=='18' || voucher_type_id=='19'){
			$('.numbering_config').css('display', 'none');
            $('#automatic_numbering_config').css('display', 'block');				
			$("#automatic").prop("checked",true);			
            $('#manual_numbering_config').css('display', 'none');
			$("#manual").prop("checked",false);				
			}	
		else{
			$('.numbering_config').css('display', 'none');
            $('#automatic_numbering_config').css('display', 'block');
			$("#manual").prop("checked",false);
			$("#automatic").prop("checked",true);			
				
			}
        }
        if(method == '0'){
            $('.numbering_config').css('display', 'none');
            $('#manual_numbering_config').css('display', 'block');
        }
    });
	
	$(document).on('change', 'input[name="embed_type"]', function(){
		
		var embed_type_val = $(this).val();
		if(embed_type_val=="P"){
		$('input[name="comp_vch_prefix"]').val('');	
		 load_series_format();	
		 $('input[name="comp_vch_prefix"]').prop("disabled",true);	
		 $('input[name="comp_vch_suffix"]').prop("disabled",false);
		 
		 
		 
		}
		else if(embed_type_val=="S"){
		$('input[name="comp_vch_suffix"]').val('');	
		 load_series_format();	
		 $('input[name="comp_vch_prefix"]').prop("disabled",false);
		 $('input[name="comp_vch_suffix"]').prop("disabled",true);
		 
		 
		}else{
		$('input[name="comp_vch_prefix"]').prop("disabled",false);
		 $('input[name="comp_vch_suffix"]').prop("disabled",false);	
		}
	});
    $(document).on('submit', '#myform', function(e){
         e.preventDefault();
		 var voucher_type_id        = $('select[name="voucher_type_id"] option:selected').val();
		 var comp_vch_prefix        = $("#comp_vch_prefix").val();
		 var comp_vch_suffix        = $("#comp_vch_suffix").val();
		 var voucher_numbering_type = $('input[name="comp_vch_method"]:checked').val();
		 var comp_vch_renum_freq    = $('input[name="comp_vch_renum_freq"]:checked').val();
		 var series_format          = $("#series_format").html();
		 var embed_type             = $('input[name="embed_type"]:checked').val();
		 var embed_format           = $('select[name="embed_format"] option:selected').val();
		 var isseries_exists        = 0;
		 if(typeof embed_type=="undefined")
			  var embed_type =0;
		 

		 if(voucher_numbering_type=='1' && comp_vch_renum_freq >0 && embed_type=="S" && comp_vch_prefix!='' && comp_vch_prefix.length > 6){
			stop_loader();
			alert_notification("Prefix is allowed upto 6 characters.");	
			return false;
				
		}
		else if(voucher_numbering_type=='1' && comp_vch_renum_freq=="0" && comp_vch_prefix!='' && comp_vch_prefix.length > 6){
			stop_loader();
			alert_notification("Prefix is allowed upto 6 characters.");	
			return false;
				
		}
		
		else if(voucher_numbering_type=='1' && comp_vch_renum_freq >0 && embed_type=="P" && comp_vch_suffix!='' && comp_vch_suffix.length > 6){
			stop_loader();
			alert_notification("Suffix is allowed upto 6 characters.");	
			return false;
				
		}
		else if(voucher_numbering_type=='1' && comp_vch_renum_freq=="0" && comp_vch_suffix!='' && comp_vch_suffix.length > 6){
			stop_loader();
			alert_notification("Suffix is allowed upto 6 characters.");	
			return false;
				
		}
		
		
		else if(voucher_numbering_type=='1' && comp_vch_renum_freq >0 && embed_type==0){
			stop_loader();
			alert_notification("Choose Identification In Suffix Or Prefix.");	
			return false;
				
		}
	else if(voucher_numbering_type=='1' && comp_vch_renum_freq >0 && (embed_type=='P' || embed_type=='S') && embed_format==''){
			stop_loader();
			alert_notification("Choose Embedded Format.");	
			return false;
				
		}
		
	  else if(series_format.length>16){
		stop_loader();
			alert_notification("Series length is allowed upto 16 characters..");	
			return false;  
	  }
		
		else if(voucher_numbering_type=='1' && comp_vch_suffix!='' && comp_vch_suffix.length > 6){
			stop_loader();
			alert_notification("Suffix is allowed upto 6 characters.");	
			return false;
				
		}
		else{	
	
		$.ajax({
            url: baseurl+"admin/voucher_series/AutoSeriesExists", 
            type: 'POST',
            dataType: "json",
            async: false,
            cache: false,
			data:{"voucher_type_id":voucher_type_id,"series_format":series_format,"series_id":'<?php echo $comp_vch_series_id;?>'},
            beforeSend: function() {},
            success: function (response) {
                    if(response.series_exists=="1")
						isseries_exists=1;
					else
						isseries_exists=0;
			     },
			complete: function() {
               stop_loader();                     
            },
            error: function (jqXHR, exception) {
                var error= '';
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
				if(error!='')
                 alert_notification(error);
                },	 
			 })
		if(isseries_exists=="1"){
			alert_notification("Voucher Series already exists.");
			return false;
		}	
		else{
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
                $('#myform').attr('disabled', 'disabled');
                $('#validation_errors').html('');
            },
            success: function (response) {
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                stop_loader();

                if(response.status){
                    alert_success(response.message);
                    window.location.href='<?php echo base_url();?>/admin/voucher_series';
                }
                else{
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
      <strong>Error!</strong> <ul>${list}</ul>
    </div>
    <button type="button" class="btn-close" aria-label="Close"></button>
  </div>
                            `;
                        if(response.errors.length > 0){
                            
                            $('#validation_errors').html(html);
                            window.scrollTo(0,0);
                        }
                    }  
                }
                
            },
            complete: function() {
                stop_loader();
                $('#myform').attr('disabled', false);
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
		}
	}

    });
</script>