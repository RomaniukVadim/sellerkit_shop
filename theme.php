<?php 
	echo $header;
	echo $notices;
?>
<form action="" method="post" class="form-horizontal single">
	<?=csrf_field()?>
	<h5>Theme settings</h5>
	<fieldset>
		<div class="form-group">
			<label class="control-label">Slogan</label>
			<input name="slogan" type="text"  value="<?php echo $style->slogan;?>" class="form-control" required/>
		</div>
		<div class="form-group">
			<label class="control-label">Description</label>
			<input name="desc" type="text"  value="<?php echo $style->desc;?>" class="form-control" required/>
		</div>
		<div class="form-group">
			<label class="control-label">Primary color</label>
			<input name="color1" type="text"  value="<?php echo explode(',',$style->background)[0];?>" class="form-control" required/>
		</div>
		<div class="form-group">
			<label class="control-label">Secondary color</label>
			<input name="color2" type="text"  value="<?php echo explode(',',$style->background)[1];?>" class="form-control" required/>
		</div>
		<div class="form-group">
			<label class="control-label">Button text</label>
			<input name="button_text" type="text"  value="<?php echo explode(',',$style->button)[0];?>" class="form-control" required/>
		</div>
		<div class="form-group">
			<label class="control-label">Button link</label>
			<input name="button_link" type="text"  value="<?php echo explode(',',$style->button)[1];?>" class="form-control" required/>
		</div>
		<div class="form-group">
			<label class="control-label">Video / Photo</label>
			<input name="media" type="text"  value="<?php echo $style->media;?>" class="form-control" required/>
			<span>To include video , past video url from Youtube</span>
		</div>
		<div class="form-group">
			<label class="control-label">Custom CSS</label>
			<textarea name="css" type="text" class="form-control"><?php echo $style->css;?></textarea>
		</div>
		<div class="form-group">
			<label class="control-label">Custom Javascript</label>
			<textarea name="js" type="text" class="form-control"><?php echo $style->js;?></textarea>
		</div>
		<input name="save" type="submit" value="Update" class="btn btn-primary" />
	</fieldset>
</form>
<?php echo $footer;?>