<?php
	echo $header;
	if(isset($_GET['edit']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="payment"><i class="icon-arrow-left"></i></a>Edit payment method</h5>
					<fieldset>
						<div class="form-group">
							<label class="control-label">Payment method title</label>
							<input name="title" value="'.$method->title.'" type="text"  class="form-control" required/>
						</div>';
						$method_options = json_decode($method->options,true);
						foreach ($method_options as $key => $value){
							echo '<div class="form-group">
								<label class="control-label">'.$key.'</label>
								<input name="'.$key.'" value="'.$value.'" type="text"  class="form-control" required/>
							</div>';
						}
						echo '
						<div class="form-group">
							<label class="control-label">Payment method status</label>
							<select name="active" class="form-control" >
								<option '.($method->active == 1 ? 'selected' : '').' value="1">Enabled</option>
								<option '.($method->active == 0 ? 'selected' : '').' value="0">Disabled</option>
							</select>
						</div>
						<input name="edit" type="submit" value="Update payment method" class="btn btn-primary" />
					</fieldset>
				</form>';
	} else {
?>
<div class="head">
	<h3>Payment</h3>
	<p>Manage your website payment methods</p>
</div>
<?php
		foreach ($methods as $method){
			echo'<div class="mini bloc">
			<h5>'.$method->title.' <b>('.$method->code.')</b>';
			echo ($method->active == 1) ?  ' <i class="icon-check"></i> ' : ' <i class="icon-close"></i> ';		
			echo '<div class="tools"><a href="payment?edit='.$method->id.'"><i class="icon-pencil"></i></a></div></h5>
			</div>';
		}
	}
	echo $footer;
?>