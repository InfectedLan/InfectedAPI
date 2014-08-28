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
			isset($_GET['deadlineTime']) &&
			isset($_GET['published']) &&
			is_numeric($_GET['id']) &&
			!empty($_GET['title']) &&
			is_numeric($_GET['price']) &&
			!empty($_GET['mode']) &&
			!empty($_GET['description']) &&
			!empty($_GET['deadlineTime']) &&
			is_numeric($_GET['published'])) {
			$id = $_GET['id'];
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$price = $_GET['price'];
			$mode = $_GET['mode'];
			$description = $_GET['description'];
			$deadlineTime = $_GET['deadlineTime'];
			$published = $_GET['published'];

			GameHandler::updateGame($id, $name, $title, $price, $mode, $description, $deadlineTime, $published);
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