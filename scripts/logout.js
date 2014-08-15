function logout() {
	$.getJSON('../api/json/logout.php', function(data){
		if (data.result) {
			location.reload();
		} else {
			error(data.message);
		}
	});
}

