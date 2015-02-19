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
			$fromEvent = EventHandler::getCurrentEvent();
			
			if ($fromEvent != null &&
				$toEvent != null) {
				$event->cloneMembersFrom($fromEvent);
				$result = true;
				$message = '<p>Alle medlemene fra det tidligere arrangement ble overført til dette.</p>';
			} else {
				$message = '<p>Arrangementene oppgitt ble ikke funnet.</p>';
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