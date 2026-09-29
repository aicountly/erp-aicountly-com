<?php $header = array( 	'title' => 'Error' ); ?>
<?php echo view('includes/header',$header); ?>

<style>
*{
    transition: all 0.6s;
}

#main{
    display: table;
    width: 100%;
    min-height: calc(100vh - 250px);
    text-align: center;
}

.fof{
	  display: table-cell;
	  vertical-align: middle;
}

.fof h1{
	color: #888;
	  font-size: 50px;
	  display: inline-block;
	  padding-right: 12px;
	  animation: type .5s alternate infinite;
}

@keyframes type{
	  from{box-shadow: inset -3px 0px 0px #888;}
	  to{box-shadow: inset -3px 0px 0px transparent;}
}
</style>

<div id="main">
	<div class="fof">
		<h1>Page Not Found</h1>
		<p>Page you are looking for does not exists anymore</p>
		<p><a href="<?= base_url() ?>/admin/dashboard">Click here</a> to redirect to dashboard.</p>
	</div>
</div>

<?php echo view('includes/footer_scripts'); ?>
