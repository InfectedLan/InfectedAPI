<?php
require_once 'session.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.groups')) {
		
		if (isset($_GET['id']) &&
			isset($_GET['title']) &&
			isset($_GET['description']) &&
			isset($_GET['leader']) &&
			isset($_GET['coleader']) &&
			!empty($_GET['title']) &&
			!empty($_GET['description'])) {
			$group = GroupHandler::getGroup($_GET['id']);
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$description = $_GET['description'];
			$leader = UserHandler::getUser($_GET['leader']);
			$coleader = UserHandler::getUser($_GET['coleader']);

			if ($group != null) {
				GroupHandler::updateGroup($group, $name, $title, $description, $leader, $coleader);
				$result = true;
			} else {
				$message = 'Gruppen finnes ikke.';
			}
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