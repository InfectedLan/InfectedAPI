$(document).ready(function() {
	$('.register').submit(function(e) {
		e.preventDefault();
		$.getJSON('../api/json/register.php' + '?' + $(this).serialize(), function(data){
			if (data.result) {
				//$(location).attr('href', 'index.php');
				info('Du har blitt registrert, og en e-post har blitt sendt til addressen du har oppgitt for å kunne aktivere brukeren.');
			} else {
				error(data.message); 
			}
		});
	});
});