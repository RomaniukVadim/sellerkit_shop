$(document).ready(function(){
	$("body").on("click","#bank",function(e) {
		var order = $('#bank').data('order');
		$(".cart-payment").html('<div class="loading"></div>');
		// Update the order payment informations
			$.ajax({ 
				url: 'api/pay?method=bank&order='+order,
				type: 'post',
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
				alert('Error processing your order');
			});
	});
});