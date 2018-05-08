<?php echo $header?>
</div>
<div class="container content support">
	<div class="col-md-6">
		<h5><?=translate('E-mail') ?></h5>
		<b><?=$cfg->email ?></b>
		<h5><?=translate('Address') ?></h5>
		<b><?=nl2br($cfg->address) ?></b>
		<h5><?=translate('Phone') ?></h5>
		<b><?=$cfg->phone ?></b>
	</div>
	<div class="col-md-6">
		<?php
			if (isset($sent)){
				if ($sent == true){
					echo '<div class="alert alert-success">'.translate('Thank you , we have received your message').'</div>';
				}else{
					echo '<div class="alert alert-warning">'.translate('All fields are required !').'</div>';
				}
			}
		?>
		<form action="" method="post" class="form-horizontal single">
			<?=csrf_field() ?>
			<h5><?=translate('Contact us') ?></h5>
			<fieldset>
				<div class="form-group">
					<label class="control-label"><?=translate('Name') ?></label>
					<input name="name" type="text" value="<?=isset($_POST['name']) ? $_POST['name'] : '' ?>" class="form-control" />
				</div>
				<div class="form-group">
					<label class="control-label"><?=translate('E-mail') ?></label>
					<input name="email" type="text" value="<?=isset($_POST['email']) ? $_POST['email'] : '' ?>" class="form-control"  />
				</div>
				<div class="form-group">
					<label class="control-label"><?=translate('Subject') ?></label>
					<input name="subject" type="text" value="<?=isset($_POST['subject']) ? $_POST['subject'] : '' ?>" class="form-control"  />
				</div>
				<div class="form-group">
					<label class="control-label"><?=translate('Message') ?></label>
					<textarea name="message" type="text" class="form-control"><?=isset($_POST['message']) ? $_POST['message'] : '' ?></textarea>
				</div>
				<input name="send" type="submit" value="<?=translate('Send') ?>" class="btn btn-primary" />
			</fieldset>
		</form>
	</div>
</div>
<?php echo $footer?>