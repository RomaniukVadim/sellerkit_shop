<?php echo $header?>
<link href="https://fonts.googleapis.com/css?family=Source+Code+Pro" rel="stylesheet"> 
<?=$notices?>
<div class="editor">
	<h4><i class="icon-docs"></i>Theme editor : <?=$cfg->theme?></h4>
	<div class="col-md-2">
		<?php
			foreach ($files as $file){
				echo '<a href="editor?file='.basename($file).'" class="file"><i class="icon-doc"></i>'.basename($file).'</a>';
			}
		?>
	</div>
	<div class="col-md-10">
		<form action="" style="padding: 0px;border-radius: 0px;max-width: initial;" method="post">
			<?=csrf_field()?>
			<textarea name="text" rows="20"><?=htmlspecialchars($text)?></textarea>
			<input type="submit" value="Edit"/>
			<input type="reset" value="Reset"/>
		</form>
	</div>
	<div class="clearfix"></div>
</div>
<?php echo $footer?>