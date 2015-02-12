<?php
require_once 'session.php';
require_once 'handlers/applicationhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.applications')) {
		
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$application = ApplicationHandler::getApplication($_GET['id']);
			
			if ($application != null) {
				// Only allow application for current event to be queued.
				if ($application->getEvent()->equals(EventHandler::getCurrentEvent())) {
					ApplicationHandler::queueApplication($user, $application, true);
					$result = true;
				} else {
					$message = 'Kan ikke sette søknader for tidligere arrangementer i kø.';
				}
			} else {
				$message = 'Søknaden finnes ikke.';
			}
		} else {
			$message = 'Ingen søknad spesifisert.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>