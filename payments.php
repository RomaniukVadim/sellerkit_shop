<?php
	if (isset($order)){
		require 'init.php';
		if (isset($_POST["stripeToken"])) {
			\Stripe\Stripe::setApiKey(json_decode($payment_method->options,true)['secret']);
			try {
				if (!isset($_POST['stripeToken'])) throw new Exception("The Stripe Token was not generated correctly");
				$charge = \Stripe\Charge::create(array("amount" => $total*100,
				"currency" => "usd",
				"description" => $this->cfg->name." - Order #".$order,
				"source" => $_POST['stripeToken']));
				if ($charge->paid == true){
					$data['method'] = 'stripe';
					$data['payment_status'] = 'paid';
					$data['charge_id'] = $charge->id;
					$data['balance_transaction'] = $charge->balance_transaction;
					$data['created'] = $charge->created;
					$data['card'] = $charge->source->id;
					$data['card_brand'] = $charge->source->brand;
					$data['exp_month'] = $charge->source->exp_month;
					$data['exp_year'] = $charge->source->exp_year;
					$data['fingerprint'] = $charge->source->fingerprint;
					$data['last4'] = $charge->source->last4;
					$payment = json_encode($data,true);
					DB::update("UPDATE orders SET payment = '$payment' WHERE id = ".$order);
				} else {
					$unpaid = true;
					$error = "The charge wasn't successfull";
				}
			}
			catch (Exception $e) {
				$error = $e->getMessage();
				$unpaid = true;
			}
		}
	} else {
		echo 'direct access not allowed';
	}
?>