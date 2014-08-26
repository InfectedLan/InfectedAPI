<?php
require_once 'handlers/registrationcodehandler.php';

if (isset($_GET['code'])) {
	RegistrationCodeHandler::removeRegistrationCode($_GET['code']);
	
	echo 'Brukeren din er nå aktivert og klar for bruk.';
} else {
	echo 'Brukeren din er allerede aktivert.';
}
?>