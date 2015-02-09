<?php
require_once 'session.php';
require_once 'handlers/applicationhandler.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.applications')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$application = ApplicationHandler::getApplication($_GET['id']);
			$comment = isset($_GET['comment']) ? $_GET['comment'] : null;
			
			// Only allow application for current event to be accepted.
			if ($application->getEvent()->getId() == EventHandler::getCurrentEvent()->getId()) {
				ApplicationHandler::acceptApplication($user, $application, $comment, true);
				$result = true;
			} else {
				$message = 'Kan ikke godkjenne søknader for tidligere arrangementer.';
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