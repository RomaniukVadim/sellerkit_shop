<?php
	echo $header;
	if(isset($_GET['add']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="builder"><i class="icon-arrow-left"></i></a>Add new bloc</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Area</label>
							<select name="area" class="form-control">
								<option value="home">Homepage</option>
								<option value="page">Page</option>
								<option value="post">Post</option>
							</select>
						  </div>
						  <div class="form-group">
							<label class="control-label">Content</label>
							<textarea name="content" type="text"  class="form-control" required></textarea>
						  </div>
						  <div class="form-group">
							<label class="control-label">Title</label>
							<input name="title" type="text" class="form-control" required/>
						  </div>
						  <input name="add" type="submit" value="Add bloc" class="btn btn-primary" />
					</fieldset>
				</form>';
	}
	elseif(isset($_GET['edit']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="builder"><i class="icon-arrow-left"></i></a>Edit bloc</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Content</label>
							<textarea rows="10" name="content" type="text"  class="form-control" required>'.$bloc->content.'</textarea>
						  </div>
						  <div class="form-group">
							<label class="control-label">Title</label>
							<input name="title" type="text" value="'.$bloc->title.'" class="form-control" required />
						  </div>
						  <input name="edit" type="submit" value="Edit bloc" class="btn btn-primary" />
					</fieldset>
				</form>';
	} else {
	?>
	<div class="head">
	<h3>Page builder<a href="builder?add" class="add">Add bloc</a></h3>
	<a href="builder?">Homepage</a> - 
	<a href="builder?page">Pages</a> - 
	<a href="builder?post">Blog post</a>
	</div>
	<?php
		echo $notices."<ul>";
		foreach ($blocs as $bloc){
			echo '<li id="'.$bloc->id.'" class="m">'.$bloc->title.'
			<div class="tools">
			<a href="builder?delete='.$bloc->id.'"><i class="icon-trash"></i></a>
			<a href="builder?edit='.$bloc->id.'"><i class="icon-pencil"></i></a>
			</div>
			<p>'.mb_substr(htmlspecialchars($bloc->content),0,50).'</p></li>';
		}
		echo "</ul><button class='btn save'>Save</button>";
	}
	?>

	</div>
	<script src="<?=$tp;?>/assets/jquery-ui.min.js"></script>
	<script>
	$(function(){
		function serializeList(container)
		{
		  var str = ''
		  var n = 0
		  var els = container.find('li.m')
		  for (var i = 0; i < els.length; ++i) {
			var el = els[i]
			var p = el.id
			if (p != -1) {
			  if (str != '') str = str + '&'
			  str = str + 'item[]=' + p
			  ++n
			}
		  }
		  return str
		}
		$('ul').sortable({connectWith:"ul"});
		$('.save').click(function(){
			var data= serializeList($('ul'));
			$.post('builder?save',{"data":data,_token: '<?=csrf_token()?>'},function(d){
				alert('Saved');
			});
		});
	});
	</script>
<?php echo $footer?>