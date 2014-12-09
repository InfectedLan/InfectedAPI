$(document).ready(function() {
	$('.register').submit(function(e) {
		e.preventDefault();
		$.getJSON('../api/json/user/registerUser.php' + '?' + $(this).serialize(), function(data){
			if (data.result) {
				//$(location).attr('href', 'index.php');
				info(data.message, function() {location.reload();});
			} else {
				error(data.message); 
			}
		});
	});
});