<?php $header = array( 	'title' => 'Master Mapping' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>
<style>
 .myform .col-12 ,.formfields .col-md-6{padding-bottom:6px; padding-top:6px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>	  
 
	<div class=" row">
		<div class="col-6">
			<h3 class="pb-3">Master Mapping</h3>
		</div> 
		<div class="col-6">
			<span class="float-end">
				<a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">« Back</a>
			</span>
		</div> 
	</div>  

	<div class="row" >
		<div class="col-md-8">
			<div class="card p-4 my-2">

				<div class="list-group" style="height: 57vh;overflow-y: auto;">
					<?php foreach ($masters as $key => $value) { ?>


						<a href="<?= base_url().'/grpcomp/mapping/mapped_group/'.$key ?>" class="list-group-item list-group-item-action">
							<?= $value ?>
						</a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

<?php echo view('includes/'.$folder_path.'footer_scripts'); ?>

<script>

</script>