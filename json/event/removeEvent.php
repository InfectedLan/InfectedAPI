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
				$message = '<p>Arrangementet finnes ikke.</p>';
			}
		} else {
			$message = '<p>Ikke noe arrangement spesifisert.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>