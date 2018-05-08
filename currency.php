<?php
	echo $header;
	if(isset($_GET['add']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="currency"><i class="icon-arrow-left"></i></a>Add new currency</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Name</label>
							<input name="name" type="text" class="form-control" required />
						  </div>
						  <div class="form-group">
							<label class="control-label">Code</label>
							<input name="code" type="text" class="form-control" required />
						  </div>
						  <div class="form-group">
							<label class="control-label">Exchange rate</label>
							<input name="rate" type="text" class="form-control" required />
						  </div>
						  <input name="add" type="submit" value="Add currency" class="btn btn-primary" />
					</fieldset>
				</form>';
	}
	elseif(isset($_GET['edit']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="currency"><i class="icon-arrow-left"></i></a>Edit currency</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Name</label>
							<input name="name" value="'.$currency->name.'" type="text" class="form-control" required />
						  </div>
						  <div class="form-group">
							<label class="control-label">Code</label>
							<input name="code" value="'.$currency->code.'" type="text" class="form-control" required />
						  </div>
						  <div class="form-group">
							<label class="control-label">Exchange rate</label>
							<input name="rate" value="'.$currency->rate.'" type="text" class="form-control" required />
						  </div>
						  <input name="edit" type="submit" value="Edit currency" class="btn btn-primary" />
					</fieldset>
				</form>';
	} else {
?>
<div class="head">
<h3>Currency<a href="currency?add" class="add">Add currency</a></h3>
<p>Manage the currencies you accept in your website</p>
</div>
<?php
		echo $notices;
		foreach ($currencies as $currency){
			echo '<div class="mini bloc">
			<h5>'.$currency->name.' ('.$currency->code.')
				<div class="tools">';
					echo (count($currencies) != 1) ? '<a href="currency?delete='.$currency->id.'"><i class="icon-trash"></i></a>' : '';
					echo ' <a href="currency?edit='.$currency->id.'"><i class="icon-pencil"></i></a>';
					echo ($currency->default == 0) ? ' <a href="currency?default='.$currency->id.'" title="Set as default"><i class="icon-pin "></i></a>' : ' <i class="icon-check" title="Default currency"></i>';
					echo '</div>
			</h5>
			</div>';
		}
	}
?>