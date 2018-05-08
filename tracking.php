<?php
	echo $header;
	if(isset($_GET['add']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="tracking"><i class="icon-arrow-left"></i></a>Add new tracking</h5>
					<trackinget>
						  <div class="form-group">
							<label class="control-label">tracking name</label>
							<input name="name" type="text"  class="form-control" />
						  </div>
						  <div class="form-group">
							<label class="control-label">tracking code</label>
							<input name="code" type="text" class="form-control"  />
						  </div>
						  <input name="add" type="submit" value="Add tracking" class="btn btn-primary" />
					</trackinget>
				</form>';
	}
	else{
	?>
	<div class="head">
		<h3>Tracking<a href="tracking?add" class="add">Add tracking</a></h3>
		<p>Track performance of your marketing compaigns</p>
	</div>
	<?php
		echo $notices;
		foreach ($codes as $code){
			echo'<div class="mini bloc">
			<h5>'.$code->name.'<div class="tools">
			<a href="tracking?delete='.$code->id.'"><i class="icon-trash"></i></a>
			</div></h5>
			<input class="form-control" type="text" onfocus="this.select();" onmouseup="return false;" value="'.url('?tracking='.$code->code).'" />
			<p>code : '.$code->code.' <br/> clicks : '.$code->clicks.'</p>
			</div>';
		}
	}
	echo $footer;
	?>