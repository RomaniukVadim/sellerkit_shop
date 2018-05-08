<?php
	echo $header;
	if(isset($_GET['edit']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="templates"><i class="icon-arrow-left"></i></a>Edit template</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">template title</label>
							<input name="title" value="'.$template->title.'" type="text"  class="form-control" required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">template content</label>
							<textarea class="form-control" name="template" id="template" rows="10" cols="80" required>'.$template->template.'</textarea>
						  </div>
						  <input name="edit" type="submit" value="Edit template" class="btn btn-primary" />
					</fieldset>
				</form>';
	} else {
?>
<div class="head">
	<h3>Templates</h3>
	<p>Manage your website email templates</p>
</div>
<?php
	
		foreach ($templates as $template){
			echo'<div class="bloc">
			<h5>'.$template->title.'<b> ('.$template->code.') </b><div class="tools"><a href="templates?edit='.$template->id.'"><i class="icon-pencil"></i></a></div></h5>
			</div>';
		}
	}
	echo $footer;
?>