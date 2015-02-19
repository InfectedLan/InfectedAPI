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
			
			if ($application != null) {
				if ($application->getEvent()->equals(EventHandler::getCurrentEvent())) {
					$application->accept($user, $comment, true);
					$result = true;
				} else {
					$message = '<p>Kan ikke godkjenne søknader for tidligere arrangementer.</p>';
				}
			} else {
				$message = '<p>Søknaden finnes ikke.</p>';
			}
		} else {
			$message = '<p>Ingen søknad spesifisert.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>