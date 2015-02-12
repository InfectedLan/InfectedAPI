<?php
require_once 'session.php';
require_once 'handlers/slidehandler.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('event.screen')) {
		
		if (isset($_GET['title']) &&
			isset($_GET['content']) &&
			isset($_GET['startTime']) &&
			isset($_GET['startDate']) &&
			isset($_GET['endTime']) &&
			isset($_GET['endDate']) &&
			!empty($_GET['title']) &&
			!empty($_GET['content']) &&
			!empty($_GET['startTime']) &&
			!empty($_GET['startDate']) &&
			!empty($_GET['endTime']) &&
			!empty($_GET['endDate'])) {
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$content = $_GET['content'];
			$startTime = $_GET['startDate'] . ' ' . $_GET['startTime'];
			$endTime = $_GET['endDate'] . ' ' . $_GET['endTime'];
			$published = 1;
			
			SlideHandler::createSlide(EventHandler::getCurrentEvent(), $name, $title, $content, $startTime, $endTime, $published);
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