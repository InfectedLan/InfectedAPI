﻿$(document).ready(function() {
	$('.register').submit(function(e) {
		e.preventDefault();
		$.getJSON('../api/json/register.php' + '?' + $(this).serialize(), function(data){
			if (data.result) {
				//$(location).attr('href', 'index.php');
				info(data.message);
			} else {
				error(data.message); 
			}
		});
	});
});