function logout() {
	$.getJSON('../api/json/session/logout.php', function(data){
		if (data.result) {
			location.reload();
		} else {
			error(data.message);
		}
	});
}

