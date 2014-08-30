<?php
require_once 'session.php';
require_once 'handlers/gamehandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') || 
		$user->hasPermission('functions.site-list-games')) {
		if (isset($_GET['title']) &&
			isset($_GET['price']) &&
			isset($_GET['mode']) &&
			isset($_GET['description']) &&
			isset($_GET['deadlineDate']) &&
			isset($_GET['deadlineTime']) &&
			!empty($_GET['title']) &&
			is_numeric($_GET['price']) &&
			!empty($_GET['mode']) &&
			!empty($_GET['description']) &&
			!empty($_GET['deadlineDate']) &&
			!empty($_GET['deadlineTime'])) {
			$id = $_GET['id'];
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$price = $_GET['price'];
			$mode = $_GET['mode'];
			$description = $_GET['description'];
			$deadlineTime = $_GET['deadlineDate'] . ' ' . $_GET['deadlineTime'];

			GameHandler::createGame($$name, $title, $price, $mode, $description, $deadlineTime, 1);
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