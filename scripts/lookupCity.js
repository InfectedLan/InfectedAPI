$(document).ready(function() {
	$('.postalcode').change(function() {
		$.getJSON('../api/json/location/citydictionary.php?postalcode=' + encodeURIComponent($('.postalcode').val()), function(data) {
			if (data.result) {
				$('.city').text(data.message);
			}
		});
	});
});