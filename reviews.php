	<?php echo $header;?>
	<div class="head">
		<h3>Reviews</h3>
		<p>Manage customer reviews and approve them</p>
	</div>
	<?php
		foreach ($reviews as $review){
			echo'<div class="bloc">
			<h5>'.DB::select("SELECT title FROM products WHERE id ='".$review->product."'")[0]->title.' - <b>'.$review->rating.' stars</b><div class="tools">';
			echo ($review->active != 1) ? '<a href="reviews?approve='.$review->id.'"><i class="icon-like "></i></a>' : '<i class="icon-check"></i>';
			echo '</div></h5><p>'.nl2br(htmlspecialchars($review->review)).'</p>
			<b>'.$review->name.' - '.$review->email.'</b>
			</div>';
		}
	echo $footer;
	?>