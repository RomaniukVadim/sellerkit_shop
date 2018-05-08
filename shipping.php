<?php
	echo $header;
	if(isset($_GET['add']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="shipping"><i class="icon-arrow-left"></i></a>Add new cost</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Shipping cost</label>
							<input name="cost" type="text" class="form-control" required />
						  </div>
						  <div class="form-group">
							<label class="control-label">Country</label>
							<select id="country" name="country" class="form-control">';
							foreach ($countries as $country){
								echo '<option value="'.$country->iso.'" data-phone="'.$country->phonecode.'">'.$country->nicename.'</option>';
							}
							echo '</select>
						  </div>
						  <input name="add" type="submit" value="Add cost" class="btn btn-primary" />
					</fieldset>
				</form>';
	}
	elseif(isset($_GET['edit']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="shipping"><i class="icon-arrow-left"></i></a>Edit cost</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Shipping cost</label>
							<input name="cost" value="'.$cost->cost.'" type="text" class="form-control" required />
						  </div>
						  <div class="form-group">
							<label class="control-label">Country</label>
							<select id="country" name="country" class="form-control">';
							foreach ($countries as $country){
								echo '<option value="'.$country->iso.'" '.($country->iso == $cost->country ? 'selected' : '').'>'.$country->nicename.'</option>';
							}
							echo '</select>
						  </div>
						  <input name="edit" type="submit" value="Edit cost" class="btn btn-primary" />
					</fieldset>
				</form>';
	} else {
?>
<div class="head">
<h3>Shipping cost<a href="shipping?add" class="add">Add cost</a></h3>
<p>Set your shipping costs for specific countries</p>
</div>
<?php
		echo $notices;
		foreach ($costs as $cost){
			echo '<div class="mini bloc">
			<h5>'.country($cost->country).' ('.c($cost->cost).')
				<div class="tools">
					<a href="shipping?delete='.$cost->id.'"><i class="icon-trash"></i></a>
					<a href="shipping?edit='.$cost->id.'"><i class="icon-pencil"></i></a>
				</div>
			</h5>
			</div>';
		}
	}
?>