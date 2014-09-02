<?php
require_once 'handlers/registrationcodehandler.php';

if (isset($_GET['code'])) {
	$code = $_GET['code'];

	if (RegistrationCodeHandler::hasRegistrationCode($code)) {
		RegistrationCodeHandler::removeRegistrationCode($_GET['code']);
		
		echo '<p>Brukeren din er nå aktivert og klar for bruk.</p>';
	} else {
		echo '<p>Brukeren din er allerede aktivert.</p>';
	}
} else {
	echo '<p>En feil oppstod.</p>';
}
?>