<?php
	$in = '';
	if (!empty($error)){$in = 'in';}
		$response[] = '<button data-toggle="collapse" data-target="#stripe-payment" class="payment" id="cc">
			<img src="'.url('/assets/stripe.svg').'">
			<h5>'.translate("Credit Card").'</h5>
		</button>
		<script src="'.url('/themes/default/assets/stripe.js').'"></script>
		<div id="stripe-payment" class="collapse '.$in.'">
			<span class="errors">'.$error.'</span>
			<span class="success">'.$success.'</span>
			<form action="" method="POST" id="payment-form">
				<div class="form-group">
					<input type="text" placeholder="'.translate('Card number').'" maxlength="16" autocomplete="off" class="card-number" />
				</div>
				<div class="form-group">
					<input type="text" placeholder="CVC" autocomplete="off" maxlength="4" size="3" class="card-cvc" />
					<input type="text" placeholder="YYYY" maxlength="4" size="4" class="card-expiry-year pull-right"/>
					<input type="text" placeholder="MM" maxlength="2" size="2" class="card-expiry-month pull-right"/>
				</div>
				<div class="clearfix"></div>
				<div class="btn-clear"></div>
				<button type="submit" name="stripe-pay" data-order="'.$order.'" class="cart-btn stripe-pay bg">Pay</button>
			</form>
		</div>';
?>