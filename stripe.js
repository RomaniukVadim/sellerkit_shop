$(document).ready(function(){
	Stripe.setPublishableKey(stripe_key);
	function stripeResponseHandler(status, response) {
		if (response.error) {
			// re-enable the submit button
			$(".stripe-pay").removeAttr("disabled");
			// show the error
			alert(response.error.message);
		} else {
			var order = $('.stripe-pay').data('order')
			// token contains id, last4, and card type
			var token = response["id"];
			// insert the token into the form so it gets submitted to the server
			$("#payment-form").append('<input type="hidden" name="stripeToken" value="'+token+'" />');
			// and submit
			$(".cart-payment").html('<div class="loading"></div>');
			$.ajax({ 
				url: 'api/pay?method=stripe&order='+order,
				type: 'post',
				data: $("#payment-form").serialize(),
				crossDomain: true,
			}).done(function(response){
				res = JSON.parse(response);
				$("#cart-header").html(res.header);
				delete res.header;
				html = "";
				$.each(res, function(index,re){
					html += re;
				});
				$("#cart-content").html(html);
			}).fail(function() {
				alert('Error processing your payment');
			});
		}
	}
	$("body").on("submit","#payment-form",function(e) {
		e.preventDefault();
		// disable the submit button to prevent repeated clicks
		$(".stripe-pay").attr("disabled", "disabled");
		// createToken returns immediately - the supplied callback submits the form if there are no errors
		Stripe.createToken({
			number: $(".card-number").val(),
			cvc: $(".card-cvc").val(),
			exp_month: $(".card-expiry-month").val(),
			exp_year: $(".card-expiry-year").val()
		}, stripeResponseHandler);
		return false; // submit from callback
	});
});