$(document).ready(function() {
	$('.login').submit(function(e) {
		e.preventDefault();
		$.getJSON('../api/json/login.php' + '?' + $(this).serialize(), function(data){
			if (data.result) {
				location.reload();
			} else {
				error(data.message); 
			}
		});
	});
});