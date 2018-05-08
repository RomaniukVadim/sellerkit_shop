<?php
	echo $header;
	if (isset($_GET['add'])) {
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="lang"><i class="icon-arrow-left"></i></a>Add new Language</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Language</label>
							<input name="name" type="text"  class="form-control" required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">Language code</label>
							<input name="code" type="text" class="form-control" required />
						  </div>
						  <input name="add" type="submit" value="Add Language" class="btn btn-primary" />
					</fieldset>
				</form>';
	}
	elseif(isset($_GET['edit']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="lang"><i class="icon-arrow-left"></i></a>Edit Language</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Language</label>
							<input name="name" type="text"  value="'.$lang->name.'" class="form-control" required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">Language code</label>
							<input name="code" type="text" value="'.$lang->code.'" class="form-control"  required/>
						  </div>
						  <input name="edit" type="submit" value="Edit Language" class="btn btn-primary" />
					</fieldset>
				</form>';
	} else {
	?>
	<div class="head">
	<h3>Languages<a href="lang?add" class="add">Add language</a></h3>
	<p>Manage your site languages and translations .</p>
	</div>
	<h4>Available languages :</h4>
	<?php
		echo $notices;
		foreach ($langs as $lang){
			echo '<div class="bloc">
				<h5>
					<a href="lang?lang='.$lang->code.'">'.$lang->name.'</a>
					<div class="tools">
						<a href="lang?delete_language='.$lang->id.'"><i class="icon-trash"></i></a>
						<a href="lang?edit='.$lang->id.'"><i class="icon-pencil"></i></a>
					</div>
				</h5>
			</div>';
		}
		echo '<h4>Translations ('.$l.') :</h4>';
		foreach ($translations as $translation){
			echo'<div class="bloc" style="padding: 0px;">
				<div class="col-md-6" style="padding: 22px 25px;"><b>'.htmlspecialchars($translation->word).'</b><div class="tools"><a href="lang?lang='.$l.'&delete='.$translation->id.'"><i class="icon-trash"></i></a></div></div>
				<div class="col-md-6 tt t"><input value="'.htmlspecialchars($translation->translation).'"/><i id="'.$translation->id.'" class="icon-check translate"></i></div>
				<div class="clearfix"></div>
			</div>';
		}
	}
	?>
	</div>
	<script>
		$('.translate').click(function(){
			var data= $(this).attr('id');
			var translation = $(this).closest("div").find("input").val();
			$.post('lang?save='+data,{translation: translation,_token: '<?=csrf_token()?>'},function(d){
				alert('Saved');
			});
		});
	</script>