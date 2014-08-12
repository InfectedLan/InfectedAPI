<?php
require_once 'session.php';
require_once 'handlers/grouphandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('leader.group') ||
		$user->hasPermission('admin') ||
		$user->isGroupMember() && $user->isGroupLeader()) {
		if (isset($_GET['title']) &&
						isset($_GET['description']) &&
						isset($_GET['leader']) &&
						!empty($_GET['title']) &&
						!empty($_GET['description'])) {
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$description = $_GET['description'];
			$leader = $_GET['leader'];
			
			GroupHandler::createGroup($name, $title, $description, $leader);
			$result = true;
		} else {
			$message = 'Du har ikke fyllt ut alle feltene!';
		}
	} else {
		$message = "Du har ikke tillatelser til dette.";
	}
} else {
	$message = "Du er ikke logget inn.";
}

echo json_encode(array('result' => $result, 'message' => $message));

?>