<?php
require_once 'session.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/restrictedpagehandler.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.groups')) {
		if (isset($_GET['title']) &&
			isset($_GET['description']) &&
			isset($_GET['leader']) &&
			isset($_GET['coleader']) &&
			!empty($_GET['title']) &&
			!empty($_GET['description'])) {
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$description = $_GET['description'];
			$leader = $_GET['leader'];
			$coleader = $_GET['leader'];
			
			GroupHandler::createGroup(EventHandler::getCurrentEvent(), $name, $title, $description, $leader, $coleader);
			$result = true;
		} else {
			$message = '<p>Du har ikke fyllt ut alle feltene!</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>