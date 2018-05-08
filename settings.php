<?php 
	echo $header;
	echo $notices;
?>
<form action="" method="post" class="form-horizontal single">
	<?=csrf_field()?>
	<h5>Settings</h5>
	<fieldset>
		<!--SEO-->
		<div class="form-group">
			<label class="control-label">Site title</label>
			<input name="name" type="text"  value="<?php echo $cfg->name;?>" class="form-control" required/>
		</div>
		<div class="form-group">
			<label class="control-label">Description</label>
			<textarea name="desc" type="text" class="form-control" required><?=$cfg->desc?></textarea>
		</div>
		<div class="form-group">
			<label class="control-label">Keywords</label>
			<input name="key" type="text"  value="<?php echo $cfg->key;?>" class="form-control" required/>
		</div>
		<!--Site settings-->
		<div class="form-group">
			<label class="control-label">Logo</label>
			<input name="logo" type="text"  value="<?php echo $cfg->logo;?>" class="form-control" required/>
		</div>
		<div class="form-group">
			<label class="control-label">Registration</label>
			<select name="registration" class="form-control" >
				<option <?php echo (1 == $cfg->registration)?'selected':'';?> value="1">Enabled</option>
				<option <?php echo (0 == $cfg->registration)?'selected':'';?> value="0">Disabled</option>
			</select>
		</div>
		<div class="form-group">
			<label class="control-label">Translation</label>
			<select name="translations" class="form-control" >
				<option <?php echo (1 == $cfg->translations)?'selected':'';?> value="1">Enabled</option>
				<option <?php echo (0 == $cfg->translations)?'selected':'';?> value="0">Disabled</option>
			</select>
		</div>
		<div class="form-group">
			<label class="control-label">Theme</label>
			<select name="theme" class="form-control" >
				<?php $themes = array_filter(glob(base_path('themes').'/*'), 'is_dir');
					foreach($themes as $theme){
						echo '<option ';
						echo (basename($theme) == $cfg->theme)?'selected':'';
						echo ' value="'.basename($theme).'">'.basename($theme).'</option>';
					}
				?>
			</select>
		</div>
		<div class="form-group">
			<label class="control-label">Default language</label>
			<select name="lang" class="form-control" >
				<?php
					foreach ($languages as $language){
						echo '<option ';
						echo ($language->code == $cfg->lang)?'selected':'';
						echo ' value="'.$language->code.'">'.$language->name.'</option>';
					}
				?>
			</select>
		</div>
		<div class="form-group">
			<label class="control-label">Floating cart</label>
			<select name="floating_cart" class="form-control" >
				<option <?php echo (1 == $cfg->floating_cart)?'selected':'';?> value="1">Enabled</option>
				<option <?php echo (0 == $cfg->floating_cart)?'selected':'';?> value="0">Disabled</option>
			</select>
		</div>
		<!--Social links-->
		<div class="form-group">
			<label class="control-label">Facebook</label>
			<input name="facebook" type="text"  value="<?php echo $cfg->facebook;?>" class="form-control"/>
		</div>
		<div class="form-group">
			<label class="control-label">Twitter</label>
			<input name="twitter" type="text"  value="<?php echo $cfg->twitter;?>" class="form-control"/>
		</div>
		<div class="form-group">
			<label class="control-label">Tumblr</label>
			<input name="tumblr" type="text"  value="<?php echo $cfg->tumblr;?>" class="form-control"/>
		</div>
		<div class="form-group">
			<label class="control-label">Youtube</label>
			<input name="youtube" type="text"  value="<?php echo $cfg->youtube;?>" class="form-control"/>
		</div>
		<div class="form-group">
			<label class="control-label">Instagram</label>
			<input name="instagram" type="text"  value="<?php echo $cfg->instagram;?>" class="form-control"/>
		</div>
		<!--Contact details-->
		<div class="form-group">
			<label class="control-label">Site Email</label>
			<input name="email" type="text"  value="<?php echo $cfg->email;?>" class="form-control" required/>
		</div>
		<div class="form-group">
			<label class="control-label">Phone</label>
			<input name="phone" type="text"  value="<?php echo $cfg->phone;?>" class="form-control" required/>
		</div>
		<div class="form-group">
			<label class="control-label">Address</label>
			<textarea name="address" type="text" class="form-control" required><?=$cfg->address?></textarea>
		</div>
		<input name="save" type="submit" value="Update" class="btn btn-primary" />
	</fieldset>
</form>
<?php echo $footer?>