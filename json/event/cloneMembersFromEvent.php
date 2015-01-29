<?php
require_once 'session.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.cloneMembersFromEvent')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$fromEvent = EventHandler::getEvent($_GET['id']);
			$toEvent = EventHandler::getCurrentEvent();
			
			EventHandler::cloneMembers($fromEvent, $toEvent);
			$result = true;
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