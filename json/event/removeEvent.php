<?php
require_once 'session.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$event = EventHandler::getEvent($_GET['id']);

			if ($event != null) {
				EventHandler::removeEvent($event);
				$result = true;
			} else {
				$message = 'Arrangementet finnes ikke.';
			}
		} else {
			$message = 'Ikke noe arrangement spesifisert.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>