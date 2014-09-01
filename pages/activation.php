<?php
require_once 'handlers/registrationcodehandler.php';

if (isset($_GET['code'])) {
	$code = $_GET['code'];

	if (RegistrationCodeHandler::hasRegistrationCode($code)) {
		RegistrationCodeHandler::removeRegistrationCode($_GET['code']);
		
		echo '<li>';
			echo '<p>Brukeren din er nå aktivert og klar for bruk.</p>';
		echo '</li>';
	} else {
		echo '<li>';
			echo '<p>Brukeren din er allerede aktivert.</p>';
		echo '</li>';
	}
} else {
	echo '<li>';
		echo '<p>En feil oppstod.</p>';
	echo '</li>';
}
?>