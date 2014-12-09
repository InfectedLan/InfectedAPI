<?php
require_once 'session.php';
require_once 'handlers/gamehandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') || 
		$user->hasPermission('functions.site-list-games')) {
		if (isset($_GET['id']) &&
			isset($_GET['title']) &&
			isset($_GET['price']) &&
			isset($_GET['mode']) &&
			isset($_GET['description']) &&
			isset($_GET['startDate']) &&
			isset($_GET['startTime']) &&
			isset($_GET['endDate']) &&
			isset($_GET['endTime']) &&
			isset($_GET['published']) &&
			is_numeric($_GET['id']) &&
			!empty($_GET['title']) &&
			is_numeric($_GET['price']) &&
			!empty($_GET['mode']) &&
			!empty($_GET['description']) &&
			!empty($_GET['startDate']) &&
			!empty($_GET['startTime']) &&
			!empty($_GET['endDate']) &&
			!empty($_GET['endTime']) &&
			is_numeric($_GET['published'])) {
			$id = $_GET['id'];
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$price = $_GET['price'];
			$mode = $_GET['mode'];
			$description = $_GET['description'];
			$startTime = $_GET['startDate'] . ' ' . $_GET['startTime'];
			$endTime = $_GET['endDate'] . ' ' . $_GET['endTime'];
			$published = $_GET['published'];

			GameHandler::updateGame($id, $name, $title, $price, $mode, $description, $startTime, $endTime, $published);
			$result = true;
		} else {
			$message = 'Du har ikke fylt ut alle feltene.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>