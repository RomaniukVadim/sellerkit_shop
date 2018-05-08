<?php echo $header?>
<div class="container">
	<div class="content product-page">
		<div class="col-md-6">
			<div id="slider" class="flexslider">
				<ul class="slides">
					<?foreach($images as $image){?>
						<li class="zoom">
							<img src="<?=url('/assets/products/'.$image)?>" />
						</li>
					<?}?>
				</ul>
			</div>
			<div id="carousel" class="flexslider">
				<ul class="slides">
					<?foreach($images as $image){?>
						<li>
							<img src="<?=url('/assets/products/'.$image)?>" />
						</li>
					<?}?>
				</ul>
			</div>
		</div>
		<div class="col-md-6">
			<a href="<?=url('/products/'.$cat->path)?>" class="smooth"><?=translate($cat->name)?></a>
			<h3><?=translate($product->title)?></h3>
			<div class="rating">
				<?php $tr = $rating; $i = 0; while($i<5){ $i++;?>
					<i class="star<?=($i<=$rating) ? '-selected' : '';?>"></i>
				<?php $tr--; }?>
				<b> <?=$total_ratings.' '.translate('Reviews')?> </b>
			</div>
			<?=string_cut(translate($product->text),600,' ...')?>
			<h5 class="price"><?=c($product->price)?></h5>
			<div class="order">
				<?if ($product->quantity > 0) { ?>
				
					<?php
						$all_options = json_decode($product->options,true);
						if(!empty($all_options)){
						?>
						<form class="options" style="background:rgb(249, 250, 252)">
						<?php
							foreach($all_options as $i=>$row){
								$type = $row['type'];
								$name = $row['name'];
								$title = $row['title'];
								$option = $row['option'];
							?>
							<div class="option">
								<h6><?php echo $title.' :';?></h6>
								<?php
									if($type == 'radio'){
									?>
									<div class="custom_radio">
										<?php
											$i=1;
											foreach ($option as $op) {
											?>
											<label for="<?php echo 'radio_'.$i; ?>" style="display: block;"><input type="radio" name="<?php echo $name;?>" value="<?php echo $op;?>" id="<?php echo 'radio_'.$i; ?>"><?php echo $op;?></label>
											<?php
												$i++;
											}
										?>
									</div>
									<?php
										} else if($type == 'text'){
									?>
									<textarea class="form-control" rows="2" style="width:100%" name="<?php echo $name;?>"></textarea>
									<?php
										} else if($type == 'select'){
									?>
									<select name="<?php echo $name; ?>" class="form-control" type="text">
										<option value=""><?php echo translate('Choose one'); ?></option>
										<?php
											foreach ($option as $op) {
											?>
											<option value="<?php echo $op; ?>" ><?php echo $op; ?></option>
											<?php
											}
										?>
									</select>
									<?php
										} else if($type == 'multi_select') {
										$j=1;
										foreach ($option as $op){
										?>
										<label for="<?php echo 'check_'.$j; ?>" style="display: block;">
											<input type="checkbox" id="<?php echo 'check_'.$j; ?>" name="<?php echo $name;?>[]" value="<?php echo $op;?>">
											<?php echo $op;?>
										</label>
										<?php
											$j++;
										}
									}
								?>
							</div>
						<?php 
							}
						?>
						</form>
					<?php
						}
					?> 
					<div class="quantity-select">
						<div class="dec rease">-</div>
						<input name="quantity" class="quantity" value="1" >
						<div class="inc rease">+</div>
					</div>
					<button class="add-cart bg" data-id="<?=$product->id?>"><?=translate('Add to cart')?></button>
					<? } else { ?>
					<p>Quantity unavailable</p>
				<? } ?>
			</div>
			<div class="share">
				<b><?=translate('Share')?> </b>
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?=url()->current()?>"><i class="icon-social-facebook"></i></a> 
				<a href="https://twitter.com/intent/tweet/?url=<?=url()->current()?>"><i class="icon-social-twitter"></i></a>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="content">
		<div class="tabs">
			<a href="#description" data-toggle="tab" class="active"><?=translate('Description')?></a>
			<a href="#reviews" data-toggle="tab"><?=translate('Reviews')?> (<?=$total_ratings?>)</a>
		</div>
		<div class="tab-content">
			<div class="tab-pane active" id="description"><?=nl2br(translate($product->text))?></div>
			<div class="tab-pane" id="reviews">
				<?php 
					$count = 0;
					foreach($reviews as $review){
						echo '<img class="review-image" src="http://www.gravatar.com/avatar/'.md5($review->email).'?s=45&d=mm">
						<div class="review-meta"><b>'.$review->name.'</b><br/>
						<span class="time">'.date('M d, Y',$review->time).'</span><br/></div>
						<div class="review">
						<div class="rating pull-right">';
						$rr = $review->rating; $i = 0; while($i<5){ $i++;?>
						<i class="star<?=($i<=$review->rating) ? '-selected' : '';?>"></i>
						<?php $rr--; }
						echo '</div>
						<div class="clearfix"></div>
						<p>'.nl2br($review->review).'</p></div>';
						$count++;
					}
					if($count > 0){
						echo '<hr/>';
					}
				?>
				<form action="" method="post" id="review" class="form-horizontal single">
					<div id="response"></div>
					<h5><?=translate('Add a review')?> :</h5>
					<fieldset>
						<div class="row">
							<div class="form-group col-md-4">
								<label class="control-label"><?=translate('Name')?></label>
								<input name="name" value="" class="form-control" type="text">
							</div>
							<div class="form-group col-md-4">
								<label class="control-label"><?=translate('E-mail')?></label>
								<input name="email" value="" class="form-control" type="text">
							</div>
							<div class="form-group col-md-4">
								<label class="control-label"><?=translate('Rating')?></label>
								<div id="star-rating">
									<input type="radio" name="rating" class="rating" value="1" />
									<input type="radio" name="rating" class="rating" value="2" />
									<input type="radio" name="rating" class="rating" value="3" />
									<input type="radio" name="rating" class="rating" value="4" />
									<input type="radio" name="rating" class="rating" value="5" />
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label"><?=translate('Review')?></label>
							<textarea name="review" type="text" rows="5" class="form-control"></textarea>
						</div>
						<button data-product="<?=$product->id?>" name="submit" id="submit-review" class="btn btn-primary" ><?=translate('submit')?></button>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>
<link rel="stylesheet" href="<?=$tp;?>/assets/flexslider.css" type="text/css">
<script src="<?=$tp;?>/assets/jquery.flexslider.js"></script>
<script src="<?=$tp;?>/assets/jquery.zoom.js"></script>
<style>
	.zoomImg {
	background: white;
	}
</style>
<script>
	
	$('#star-rating').rating();
	$('#carousel').flexslider({
		animation: "slide",
		controlNav: false,
		animationLoop: false,
		slideshow: false,
		itemWidth: 210,
		itemMargin: 5,
		minItems: 4,
		maxItems: 6,
		asNavFor: '#slider'
	});
	
	$('#slider').flexslider({
		animation: "slide",
		controlNav: false,
		animationLoop: false,
		slideshow: false,
		sync: "#carousel",
		touch: true,
		keyboard: true,
		smoothHeight: true, 
	});
	$(document).ready(function(){
		$('.zoom').zoom({magnify: 3});
		var maxQuantity = <?=$product->quantity?>;
		$("body").on('click',".rease", function() {
			
			var $button = $(this);
			var oldValue = $button.parent().find("input").val();
			if ($button.text() == "+") {
				if (oldValue < maxQuantity) {
					var newVal = parseFloat(oldValue) + 1;
					} else {
					newVal = maxQuantity;
				}
				} else {
				if (oldValue > 1) {
					var newVal = parseFloat(oldValue) - 1;
					} else {
					newVal = 1;
				}
			}
			
			$button.parent().find("input").val(newVal);
			
		});
		$("body").on('change',".quantity", function() {
			var $button = $(this);
			var oldValue = $button.val();
			if (oldValue > maxQuantity) {
				var newVal = maxQuantity;
			}
			else if (oldValue < 1) {
				var newVal = 1;
			}
			$button.parent().find("input").val(newVal);
		});
	});
</script>
<?php echo $footer?>