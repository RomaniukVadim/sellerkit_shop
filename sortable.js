/*** APP: JQuery Sortable Menu Demo
	WEbsite:packetcode.com
	Author: Krishna TEja G S
	Date: 26th April 2014
	***/

// jquery function to work with sortable
$(function(){
	$('ul').sortable({connectWith:"ul"});

	// jquery save event trigger
	$('.save').click(function(){
		var data= $('ul').sortable('serialize');
		$.post('sortable.php',{"data":data},function(d){
			alert(d);
		});
	});
});