<?php
	echo $header;
	if(isset($_GET['add']))
	{		
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="bottom"><i class="icon-arrow-left"></i></a>Add new menu</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">menu item link</label>
							<input name="link" type="text"  class="form-control" required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">menu item title</label>
							<input name="title" type="text" class="form-control" required/>
						  </div>
						  <input name="add" type="submit" value="Add menu item" class="btn btn-primary" />
					</fieldset>
				</form>';
	}
	elseif(isset($_GET['edit']))
	{		
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="bottom"><i class="icon-arrow-left"></i></a>Edit menu item</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">menu item title</label>
							<input name="title" type="text"  value="'.$item->title.'" class="form-control" required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">menu item link</label>
							<input name="link" type="text" value="'.$item->link.'" class="form-control" required/>
						  </div>
						  <input name="edit" type="submit" value="Edit menu item" class="btn btn-primary" />
					</fieldset>
				</form>';
	}else{
	?>
	<div class="head">
	<h3>Menu<a href="bottom?add" class="add">Add menu item</a></h3>
	<p>Manage footer menu links</p>
	</div>
	<?php
		echo $notices."<ul>";
		foreach ($items as $item){
			echo '<li id="'.$item->id.'" class="m">'.$item->title.'
			<div class="tools">
			<a href="bottom?delete='.$item->id.'"><i class="icon-trash"></i></a>
			<a href="bottom?edit='.$item->id.'"><i class="icon-pencil"></i></a>
			</div>
			<p>'.$item->link.'</p></li>';
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
			$.post('bottom?save',{"data":data,_token: '<?=csrf_token()?>'},function(d){
				alert('Saved');
			});
		});
	});
	</script>