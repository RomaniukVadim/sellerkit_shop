<?php echo $header?>
	<div class="bheader bg">
		<h2><?=translate('Cart')?></h2>
	</div>
	<div id="non-floating-cart">
			<div id="cart-header">
				Cart
				<button class="pull-right" onclick="$('#cart').toggle('300');"><i class="icon-close"></i></button>
			</div>
			<div id="cart-content">
				<div class="loading"></div>
			</div>
	</div>
	<script>
		$("#cart-content").html('<div class="loading"></div>');
		$.getJSON('api/cart', function (data){
			$("#cart-header").html(data.header);
			if (data.count > 0){
				var cart = '';
				$.each(data.products, function(index,elem){
					cart += '<div class="cart-product"><img src="assets/products/'+elem.images+'"><div class="details"><h6>'+elem.title+'<i data-id="'+elem.id+'" class="remove-cart icon-trash"></i></h6><p>'+elem.price+' x '+elem.quantity+'<b>'+elem.total+'</b></p></div><div class="clearfix"></div></div>';
				});
				cart += data.coupon;
				cart += '<div class="btn-clear"></div><button class="cart-btn cart-checkout bg">'+checkout+'</button>';
				$("#cart-content").html(cart);
			}else{
				$("#cart-content").html('<div class="empty-cart"><i class="icon-basket"></i><h5>'+empty_cart+'</h5></div>');
			}
		});
		$('#cart-content').slimScroll({
			height: 'auto',
			scrollTo : 0,
		});
	</script>
<?php echo $footer?>