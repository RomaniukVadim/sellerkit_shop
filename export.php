<?php echo $header?>
<div class="head">
	<h3>Export</h3>
	<p>Save a backup of your website files or database</p>
</div>
<?php if(exec('echo EXEC') == 'EXEC'){ ?>
<div class="mini bloc">
	<h5><a href="export?database"><i class="icon-doc"></i> Database export</a></h5>
</div>
<?php } ?>
<div class="mini bloc">
	<h5><a href="export?files"><i class="icon-folder"></i> Files export</a></h5>
</div>
<?php echo $footer?>