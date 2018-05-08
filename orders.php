<?php
	echo $header;
	if (isset($_GET['details'])){
		echo $notices;
		echo '<br/><div class="col-md-8">
		<div class="list">
		<div class="title">
			<i class="icon-user"></i>Customer details
		</div>
		<div class="item order"><h6><b>Order ID</b> : #'.$order->id.'</h6></div>
		<div class="item order"><h6><b>Order time</b> : '.date('l jS \of F Y H:i:s',$order->time).'</h6></div>
		';
		foreach($fields as $field){
			$code = $field->code;
			if ($code == 'country') {$order->$code = country($order->$code);}
			echo '<div class="item order"><h6><b>'.$field->name.'</b> : '.$order->$code.'</h6></div>';
		}
		echo '</div>
		<div class="list">
		<div class="title">
			<i class="icon-basket"></i>Products
		</div>';
			$products_data = $order->products;
			$products = json_decode($products_data, true);
			if(count($products)>0){
				$ids = "";
				foreach($products as $data){
					$ids = $ids . $data['id'] . ",";
					$q[$data['id']] = $data['quantity'];
					$options_list[$data['id']] = $data['options'];
				}
				$ids = rtrim($ids, ',');
				$order_products = DB::select("SELECT * FROM products WHERE id IN (".$ids.")  ORDER BY id DESC ");
				foreach ($order_products as $product){
					$options = json_decode($options_list[$product->id],true);
					$option_array = array();
					foreach ($options as $option) {
						$option_array[] = '<i>'.$option['title'].'</i> : '.$option['value'];
					}
					echo '<div class="item"><h6>'.$product->title.' x '.$q[$product->id].'<b class="pull-right">'.c($product->price).'</b><br/>'.implode('<br/>',$option_array).'</div>';
				}
				
				if (!empty($order->coupon)) {
					// Check if coupon is valid
					if (DB::select("SELECT COUNT(code) as count FROM coupons WHERE code = '".$order->coupon."'")[0]->count > 0){
						$coupon_data = DB::select("SELECT * FROM coupons WHERE code = '".$order->coupon."'")[0];
						echo '<div class="item order text-right"> Coupon : <b>'.$coupon_data->discount.$coupon_data->type.'</b></div>';
					}
				}
				if ($order->shipping != 0) {
					// Check if coupon is valid
					echo '<div class="item order text-right"> Shipping : <b>'.c($order->shipping).'</b></div>';
				}
				echo '<div class="item order text-right">Total : <b>'.c($order->summ).'</b></div>';
			}
		echo '</div>
		<div class="text-center"><a href="orders?delete='.$order->id.'" class="btn btn-danger">Delete order</a></div>
		</div>
		<div class="col-md-4">
			<div class="list">
			<div class="title">
				<i class="icon-credit-card"></i>Payment details
			</div>';
			$payment_data = json_decode($order->payment,true);
			if ($payment_data['payment_status'] == 'unpaid'){
				echo '<div class="alert alert-warning">Order unpaid</div>';
			} else {
				if ($payment_data['method'] == 'paypal') {
					echo '<div class="item order"><h6><b>Payment method : </b>PayPal</h6></div>
					<div class="item order"><h6><b>Payment status : </b>'.$payment_data['payment_status'].'</h6></div>
					<div class="item order"><h6><b>Transcation ID : </b>'.$payment_data['txn_id'].'</h6></div>
					<div class="item order"><h6><b>Receiver e-mail : </b>'.$payment_data['receiver_email'].'</h6></div>
					<div class="item order"><h6><b>Payer e-mail : </b>'.$payment_data['payer_email'].'</h6></div>
					<div class="item order"><h6><b>Payment amount : </b>'.$payment_data['payment_amount'].'</h6></div>
					<div class="item order"><h6><b>Payment currency : </b>'.$payment_data['payment_currency'].'</h6></div>';
				} elseif ($payment_data['method'] == 'stripe') {
					echo '<div class="item order"><h6><b>Payment method : </b>Stripe</h6></div>
					<div class="item order"><h6><b>Charge ID : </b>'.$payment_data['charge_id'].'</h6></div>
					<div class="item order"><h6><b>Balance transaction : </b>'.$payment_data['balance_transaction'].'</h6></div>
					<div class="item order"><h6><b>Card ID : </b>'.$payment_data['card'].'</h6></div>
					<div class="item order"><h6><b>Card brand : </b>'.$payment_data['card_brand'].'</h6></div>
					<div class="item order"><h6><b>Last 4 : </b>'.$payment_data['last4'].'</h6></div>
					<div class="item order"><h6><b>Expiry month : </b>'.$payment_data['exp_month'].'</h6></div>
					<div class="item order"><h6><b>Expiry year : </b>'.$payment_data['exp_year'].'</h6></div>
					<div class="item order"><h6><b>Fingerprint : </b>'.$payment_data['fingerprint'].'</h6></div>';
				} elseif ($payment_data['method'] == 'cash') {
					echo '<div class="item order"><h6><b>Payment method : </b>Cash on delivery</h6></div>';
				} elseif ($payment_data['method'] == 'bank') {
					echo '<div class="item order"><h6><b>Payment method : </b>Bank Transfer</h6></div>';
				}
			}
			echo '</div>
			<div class="list">
			<div class="title">
				<i class="icon-badge"></i>Order status
			</div>
			<div class="item order text-center"><br/><h6>'.status($order->stat).'</h6><br/></div>
			<div class="item order">
				<form action="orders?details='.$order->id.'" style="padding: 0px 15px;" method="post" class="form-horizontal">
				'.csrf_field().'
				<div class="form-group">
					<label class="control-label">Change to</label>
					<select name="stat" class="form-control">
						<option value="1">Pending</option>
						<option value="2">Shipped</option>
						<option value="3">Delivered</option>
						<option value="4">Canceled</option>
					</select>
				</div>
				<input style="margin-top: 10px;" name="save" value="Save" class="btn btn-primary" type="submit">
				</form>
			</div>
			</div>
		</div>';
	} else {
		echo '<div class="head">
		<h3>Orders</h3>
		<p>View and manage your website orders</p>
		</div><div>';
		echo $notices;
		foreach ($orders as $order){
			echo'<div class="bloc">
			<h5>'.$order->name.'<div class="tools">
			<a href="orders?details='.$order->id.'"><i class="icon-eye"></i></a>
			<a href="orders?delete='.$order->id.'"><i class="icon-trash"></i></a>
			</div></h5>
			<p>';
			foreach($fields as $field){
				$code = $field->code;
				if ($field->code == 'country') {$order->$code = country($order->$code);}
				echo $field->name.' : '.$order->$code.'<br/>';
			}
			echo '</p><div class="op">';
			$products = json_decode($order->products, true);
			if(count($products)>0){
				$ids = "";
				foreach($products as $data){
					$ids = $ids . $data['id'] . ",";
					$q[$data['id']] = $data['quantity'];

				}
				$ids = rtrim($ids, ',');
				$products = DB::select("SELECT * FROM products WHERE id IN (".$ids.")  ORDER BY id DESC ");
				$total_price=0;
				foreach ($products as $product){
					echo '<div id='.$product->id.' ><p>'.$product->title.' x '.$q[$product->id].'</p><b>'.$product->price.'$</b><div style="clear:both"></div></div>';
				}
				
				echo '<b>Total : '.c($order->summ).'</b>';
			}
			echo'<div style="clear:both;"></div></div></div>';
		}
		echo'</div>';
	}
	echo $footer;
?>