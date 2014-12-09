$(document).ready(function() {
	$('.edit-password').submit(function(e) {
		e.preventDefault();
		$.getJSON('../api/json/user/editUserPassword.php' + '?' + $(this).serialize(), function(data){
			if (data.result) {
				$(location).attr('href', 'index.php?page=my-profile');
			} else {
				error(data.message); 
			}
		});
	});
});