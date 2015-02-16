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
			$id = $_GET['id'];
			
			if (isset($_GET['comment']) &&
				!empty($_GET['comment'])) {
				$application = ApplicationHandler::getApplication($_GET['id']);
				$comment = $_GET['comment'];
			
				if ($application != null) {
					// Only allow application for current event to be rejected.
					if ($application->getEvent()->getId() == EventHandler::getCurrentEvent()->getId()) {
						ApplicationHandler::rejectApplication($user, $application, $comment, true);
						$result = true;
					} else {
						$message = 'Kan ikke avslå søknader for tidligere arrangementer.';
					}
				} else {
					$message = 'Søknaden finnes ikke.';
				}
			} else {
				$message = 'Du har ikke oppgitt noen grunn på hvorfor søkneden skal bli avvist.';
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