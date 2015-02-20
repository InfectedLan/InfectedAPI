$(document).ready(function() {
	$('.register').submit(function(e) {
		e.preventDefault();
		$.getJSON('../api/json/user/addUser.php' + '?' + $(this).serialize(), function(data){
			if (data.result) {
				info(data.message, function() {
					location.reload();
				});
			} else {
				error(data.message); 
			}
		});
	});
});