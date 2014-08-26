$(document).ready(function() {
	$('.edit-profile').submit(function(e) {
		e.preventDefault();
		$.getJSON('../api/json/editUser.php' + '?' + $(this).serialize(), function(data){
			if (data.result) {
				$(location).attr('href', 'index.php?page=profile');
			} else {
				error(data.message); 
			}
		});
	});
});