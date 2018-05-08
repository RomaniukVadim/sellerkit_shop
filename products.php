<div class="container products" >
	<h4 class="pull-left"><?=translate('Products');?></h4>
    <a class="theme-btn pull-right smooth bg" data-title="<?=translate('Products');?>" href="products"><?=translate('All Products');?> &#8594;</a>
	<div class="clearfix"></div>
	<div id="products" class="product-container">
		<?php
			$products = DB::select("SELECT * FROM products ORDER BY id DESC LIMIT 4");
			foreach($products as $product){
				echo '<div class="col-md-3">
					<div class="product" id="'.$product->id.'">
						<div class="pi">
							<img src="'.url('/assets/products/'.image_order($product->images)).'"/>
						</div>
						<h5>'.translate($product->title).'</h5>
						<b>'.c($product->price).'</b>
					</div>
					<div class="bg view">
						<h5>'.translate($product->title).'</h5>
						<p>'.mb_substr(translate($product->text),0,200).'</p>
						<a href="product/'.path($product->title,$product->id).'" data-title="'.translate($product->title).'" class="smooth"><i class="icon-eye"></i> Details</a>
					</div>
				</div>';
			}
		?>
		<div class="clearfix"></div>
	</div>
</div>