<?php
	echo $header;
	if(isset($_GET['add'])) {
		echo $notices.'<form action="" method="post" class="form-horizontal single">
			'.csrf_field().'
			<h5><a href="pages"><i class="icon-arrow-left"></i></a>Add new page</h5>
			<fieldset>
				<div class="form-group">
					<label class="control-label">Page title</label>
					<input name="title" type="text"  class="form-control" required/>
				</div>
				<div class="form-group">
					<label class="control-label">Page path</label>
					<input name="path" type="text"  class="form-control" required/>
				</div>
				<div class="form-group">
					<label class="control-label">Page content</label>
					<textarea name="content" class="summernote" rows="10" cols="80" required></textarea>
				</div>
				<input name="add" type="submit" value="Add page" class="btn btn-primary" />
			</fieldset>
			</form>';
	} elseif(isset($_GET['edit'])) {
		echo $notices.'<form action="" method="post" class="form-horizontal single">
			'.csrf_field().'
			<h5><a href="pages"><i class="icon-arrow-left"></i></a>Edit page</h5>
			<fieldset>
				<div class="form-group">
					<label class="control-label">Page title</label>
					<input name="title" value="'.$page->title.'" type="text"  class="form-control" required/>
				</div>
				<div class="form-group">
					<label class="control-label">Page path</label>
					<input name="path" value="'.$page->path.'" type="text"  class="form-control" required/>
				</div>
				<div class="form-group">
					<label class="control-label">Page content</label>
					<textarea name="content" class="summernote" rows="10" cols="80" required>'.$page->content.'</textarea>
				</div>
				<input name="edit" type="submit" value="Edit page" class="btn btn-primary" />
			</fieldset>
		</form>';
	} else {
	?>
	<div class="head">
		<h3>Pages<a href="pages?add" class="add">Add page</a></h3>
		<p>Manage your website pages</p>
	</div>
	<?php
		echo $notices;
		foreach ($pages as $page){
			echo'<div class="mini bloc">
				<h5>
					<a href="../page/'.$page->path.'">'.$page->title.'</a>
					<div class="tools">
						<a href="pages?delete='.$page->id.'"><i class="icon-trash"></i></a>
						<a href="pages?edit='.$page->id.'"><i class="icon-pencil"></i></a>
					</div>
				</h5>
				<p>'.mb_substr(strip_tags($page->content),0,70).'...</p>
				</div>';
		}
	}
?>
</div>
<script type="text/javascript" src="<?=$tp?>/admin/summernote/summernote.js"></script>
<link href="<?=$tp?>/admin/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="<?=$tp?>/admin/summernote/custom.js"></script>
<?=$footer?>