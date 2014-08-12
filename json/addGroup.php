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
		if (isset($_POST['title']) &&
						isset($_POST['description']) &&
						isset($_POST['leader']) &&
						!empty($_POST['title']) &&
						!empty($_POST['description'])) {
			$name = strtolower(str_replace(' ', '-', $_POST['title']));
			$title = $_POST['title'];
			$description = $_POST['description'];
			$leader = $_POST['leader'];
			
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