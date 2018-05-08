<?php
	echo $header;
	if(isset($_GET['add']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="fields"><i class="icon-arrow-left"></i></a>Add new fields</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">field name</label>
							<input name="name" type="text"  class="form-control" required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">field code</label>
							<input name="code" type="text" class="form-control"  required/>
						  </div>
						  <input name="add" type="submit" value="Add field" class="btn btn-primary" />
					</fieldset>
				</form>';
	}
	elseif(isset($_GET['edit']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="fields"><i class="icon-arrow-left"></i></a>Edit field</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">field name</label>
							<input name="name" type="text"  value="'.$field->name.'" class="form-control" required/>
						  </div>
						  ';
						  echo ($field->code != 'name' && $field->code != 'email' && $field->code != 'country' ) ? '<div class="form-group">
							<label class="control-label">field code</label>
							<input name="code" type="text" value="'.$field->code.'" class="form-control"  required/>
						  </div>' : '<input name="code" type="hidden" value="'.$field->code.'" required/><p>You can\'t edit this field code because statistics depends on it</p>';
						  echo '<input name="edit" type="submit" value="Edit field" class="btn btn-primary" />
					</fieldset>
				</form>';
	} else {
?>
	<div class="head">
	<h3>Extrafields<a href="fields?add" class="add">Add field</a></h3>
	<p>Manage checkout fields</p>
	</div>
<?php
		echo $notices;
		foreach ($fields as $field){
			echo'<div class="mini bloc">
				<h5>'.$field->name.'
				<div class="tools">';
						echo ($field->code != 'name' && $field->code != 'email' && $field->code != 'country' ) ? '<a href="fields?delete='.$field->id.'"><i class="icon-trash"></i></a> ' : '';
						echo '<a href="fields?edit='.$field->id.'"><i class="icon-pencil"></i></a>
					</div>
				</h5>
				<p>'.$field->code.'</p>
			</div>';
		}
	}
	echo $footer;
?>