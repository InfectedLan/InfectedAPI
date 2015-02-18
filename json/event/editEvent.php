<?php
require_once 'session.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin-events')) {
		if (isset($_GET['id']) &&
			isset($_GET['theme']) &&
			isset($_GET['location']) &&
			isset($_GET['participants']) &&
			isset($_GET['bookingDate']) &&
			isset($_GET['bookingTime']) &&
			isset($_GET['startDate']) &&
			isset($_GET['startTime']) &&
			isset($_GET['endDate']) &&
			isset($_GET['endTime']) &&
			is_numeric($_GET['id']) &&
			is_numeric($_GET['location']) &&
			is_numeric($_GET['participants'])) {
			!empty($_GET['bookingDate']) &&
			!empty($_GET['bookingTime']) &&
			!empty($_GET['startDate']) &&
			!empty($_GET['startTime']) &&
			!empty($_GET['endDate']) &&
			!empty($_GET['endTime']) &&
			$event = EventHandler::getEvent($_GET['id']);
			$theme = $_GET['theme'];
			$location = $_GET['location'];
			$participants = $_GET['participants'];
			$bookingTime = $_GET['bookingDate'] . ' ' . $_GET['bookingTime'];
			$startTime = $_GET['startDate'] . ' ' . $_GET['startTime'];
			$endTime = $_GET['endDate'] . ' ' . $_GET['endTime'];
			
			if ($event != null) {
				EventHandler::updateEvent($event, $theme, $location, $participants, $bookingTime, $startTime, $endTime);
				$result = true;
			} else {
				$message = '<p>Arrangementet finnes ikke.</p>';
			}
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