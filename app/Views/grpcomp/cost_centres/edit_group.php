
<?php $header = array('title' => 'Edit Cost Centre Group' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  


<div class=" row">
    
    <div class="col-6"><h3 class="pb-3">Edit Cost Centre Group</h3></div> 
    <div class="col-6"><span class="float-end"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a></span></div> 
    
    <div class="col-md-6">
        <?php if (session()->getFlashdata('error_message')) { ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <?php echo session()->getFlashdata('error_message'); ?>
                </div>
        <?php } ?>
    
        <?php $attributes = " id='myform' name='myform' class='needs-validation myform' novalidate";
            echo form_open(base_url().'/'.$folder_path.'cost_centres/modify_group/'.$group_id, $attributes);
        ?>
        <input type="hidden" name="cc_grp_id" value="<?= $group_id ?>" >
        
        <div class="col-12">
            <label>Name</label>
            <input type="text" name="cc_grp_name" value="<?= set_value('cc_grp_name') ? set_value('cc_grp_name') : $group_info['cc_grp_name'] ?>" class="form-control" required>
        </div>
        <div class="col-12">
            <label>Alias</label> 
            <input type="text" name="cc_grp_alias" value="<?= set_value('cc_grp_alias') ? set_value('cc_grp_alias') : $group_info['cc_grp_alias'] ?>" class="form-control" required>
        </div>
        
        <div class="col-12">
            <label>Under</label>
            <div class="w-75 d-inline-block">
            <?php	echo form_dropdown('under_cc_grp_id', $user_groups_dropdown, (set_value('under_cc_grp_id') ? set_value('under_cc_grp_id') : $group_info['under_cc_grp_id']) ,'id="under_cc_grp_id" class="form-control selectwidget"  ');?>	
            </div> 
        </div>
        
        
        <div class="col-md-12  my-3">
            <label>&nbsp;</label> 
            <input type="submit" value="SAVE" class="btn btn-primary mr-1" id="submitbtn" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
            <a href="<?php echo $base_url.'cost_centres/list_group';?>" class="btn btn-secondary">QUIT</a>
        </div>
    
        </form> 
    </div>
    
</div>

<?php echo view('includes/footer_scripts'); ?>
<script>

</script>
