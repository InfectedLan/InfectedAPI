<?php
require_once 'session.php';
require_once 'handlers/agendahandler.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('event.agenda')) {
		if (isset($_GET['title']) &&
			isset($_GET['description']) &&
			isset($_GET['startTime']) &&
			isset($_GET['startDate']) &&
			!empty($_GET['title']) &&
			!empty($_GET['description']) &&
			!empty($_GET['startTime']) &&
			!empty($_GET['startDate'])) {
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$description = $_GET['description'];
			$startTime = $_GET['startDate'] . ' ' . $_GET['startTime'];
			
			AgendaHandler::createAgenda(EventHandler::getCurrentEvent(), $name, $title, $description, $startTime);
			$result = true;
		} else {
			$message = 'Du har ikke fyllt ut alle feltene!';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>