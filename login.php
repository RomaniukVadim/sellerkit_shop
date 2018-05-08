<?php echo $header?>
	<div class="bheader bg">
		<h2><?=translate('Login')?></h2>
	</div>
	<div class="col-md-4 account">
		<?php
			if (isset($error)){
				echo '<div class="alert alert-warning">'.translate($error).'</div>';
			}
		?>
		<form action="" method="post" class="form-horizontal single">
			<?=csrf_field() ?>
			<fieldset>
				<div class="form-group">
					<label class="control-label"><?=translate('E-mail') ?></label>
					<input name="email" type="email" value="<?=isset($_POST['email']) ? $_POST['email'] : '' ?>" class="form-control"  />
				</div>
				<div class="form-group">
					<label class="control-label"><?=translate('Password') ?></label>
					<input name="password" type="password" class="form-control"  />
				</div>
				<input name="login" type="submit" value="<?=translate('Login') ?>" class="btn btn-primary" />
			</fieldset>
		</form>
	</div>
	<div class="clearfix"></div>
<?php echo $footer?>