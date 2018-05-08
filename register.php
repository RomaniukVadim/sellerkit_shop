<?php echo $header?>
	<div class="bheader bg">
		<h2><?=translate('Registration')?></h2>
	</div>
	<div class="col-md-4 account">
		<?php
			if (isset($error)){
				if ($error == false){
					echo '<div class="alert alert-success">'.translate('Your account has been registred successfully').'</div>';
				} else {
					echo '<div class="alert alert-warning">'.translate($error).'</div>';
				}
			}
		?>
		<form action="" method="post" class="form-horizontal single">
			<?=csrf_field() ?>
			<fieldset>
				<div class="form-group">
					<label class="control-label"><?=translate('Name') ?></label>
					<input name="name" type="text" value="<?=isset($_POST['name']) ? $_POST['name'] : '' ?>" class="form-control" />
				</div>
				<div class="form-group">
					<label class="control-label"><?=translate('E-mail') ?></label>
					<input name="email" type="email" value="<?=isset($_POST['email']) ? $_POST['email'] : '' ?>" class="form-control"  />
				</div>
				<div class="form-group">
					<label class="control-label"><?=translate('Password') ?></label>
					<input name="password" type="password" class="form-control"  />
				</div>
				<input name="register" type="submit" value="<?=translate('Register') ?>" class="btn btn-primary" />
			</fieldset>
		</form>
	</div>
	<div class="clearfix"></div>
<?php echo $footer?>