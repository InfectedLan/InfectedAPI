<?php
require_once 'session.php';
require_once 'handlers/restrictedpagehandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('functions.mycrew') || 
		$user->hasPermission('functions.edit-page') || 
		$user->isGroupLeader()) {
		if (isset($_GET['id']) &&
			isset($_GET['title']) &&
			isset($_GET['content']) &&
			is_numeric($_GET['id']) &&
			!empty($_GET['title']) &&
			!empty($_GET['content'])) {
			$id = $_GET['id'];
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$price = $_GET['content'];

			RestrictedPageHandler::updatePage($id, $name, $title, $content);
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