<?php
require_once 'session.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.events') ||
		$user->isGroupLeader()) {
		if (isset($_GET['theme']) &&
			isset($_GET['startDate']) &&
			isset($_GET['startTime']) &&
			isset($_GET['endDate']) &&
			isset($_GET['endTime']) &&
			isset($_GET['location']) &&
			isset($_GET['participants']) &&
			!empty($_GET['theme']) &&
			!empty($_GET['startDate']) &&
			!empty($_GET['startTime']) &&
			!empty($_GET['endDate']) &&
			!empty($_GET['endTime']) &&
			is_numeric($_GET['location']) &&
			is_numeric($_GET['participants'])) {
			$theme = $_GET['theme'];
			$start = $_GET['startDate'] . ' ' . $_GET['startTime'];
			$end = $_GET['endDate'] . ' ' . $_GET['endTime'];
			$location = $_GET['location'];
			$participants = $_GET['participants'];
			
			EventHandler::createEvent($theme, $start, $end, $location, $participants);
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