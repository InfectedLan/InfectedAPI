$(document).ready(function() {
	$('.request-reset-password').submit(function(e) {
		e.preventDefault();
	    $.getJSON('../api/json/user/resetUserPassword.php' + '?' + $(this).serialize(), function(data){
			if (data.result) {
				$(location).attr('href', '.');
			} else {
				error(data.message); 
			}
		});
	});
	
	$('.reset-password').submit(function(e) {
		e.preventDefault();
		$.getJSON('../api/json/user/resetUserPassword.php' + '?' + $(this).serialize(), function(data){
			if (data.result) {
				$(location).attr('href', '.');
			} else {
				error(data.message); 
			}
		});
	});
});
