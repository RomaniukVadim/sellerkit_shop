<?php 
	echo $header;
	echo $notices;
?>
<form action="" method="post" class="form-horizontal single">
	<?=csrf_field()?>
	<h5>Newsletter</h5>
	<fieldset>
		<div class="form-group">
			<label class="control-label">Users group</label>
			<select name="group" class="form-control">
				<option value="all" selected>All E-mails</option>
				<option value="orders">Order E-mails</option>
				<option value="newsletter">Subscribers E-mails</option>
				<option value="support">Support E-mails</option>
			</select>
		</div>
		<div class="form-group">
			<label class="control-label">E-mail title</label>
			<input name="title" type="text" value="<?=(isset($_GET['title'])) ? $_GET['title'] : '';?>" class="form-control" required/>
		</div>
		<div class="form-group">
			<label class="control-label">E-mail content</label>
			<textarea class="form-control" name="content" rows="10" cols="80" required><?=(isset($_GET['content'])) ? $_GET['content'] : '';?></textarea>
		</div>
		<input name="send" type="submit" value="Send E-mail" class="btn btn-primary" />
	</fieldset>
</form>
</div>
<?php echo $footer;?>