<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/grouphandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('functions.groups') ||
		$user->isGroupLeader()) {
		if (isset($_GET['userId']) &&
			isset($_GET['groupId']) &&
			is_numeric($_GET['userId']) &&
			is_numeric($_GET['groupId'])) {
			$groupUser = UserHandler::getUser($_GET['userId']);
			$group = GroupHandler::getGroup($_GET['groupId']);
			
			GroupHandler::changeGroupForUser($groupUser, $group);
			$result = true;
		} else {
			$message = 'Ingen gruppe spesifisert.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>