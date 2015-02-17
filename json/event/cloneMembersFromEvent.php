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
			$fromEvent = EventHandler::getEvent($_GET['id']);
			$toEvent = EventHandler::getCurrentEvent();
			
			if ($fromEvent != null &&
				$toEvent != null) {
				EventHandler::cloneMembers($fromEvent, $toEvent);
				$result = true;
				$message = 'Alle medlemene fra det tidligere arrangement ble overført til dette.';
			} else {
				$message = 'Arrangementene oppgitt ble ikke funnet.';
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