<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/grouphandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.groups')) {
		
		if (isset($_GET['userId']) &&
			isset($_GET['groupId']) &&
			is_numeric($_GET['userId']) &&
			is_numeric($_GET['groupId'])) {
			$groupUser = UserHandler::getUser($_GET['userId']);
			$group = GroupHandler::getGroup($_GET['groupId']);
			
			if ($group != null && 
				$groupUser != null) {
				GroupHandler::changeGroupForUser($groupUser, $group);
				$result = true;
			} else {
				$message = 'Noe gikk galt, mangler bruker eller gruppe.';
			}
		} else {
			$message = 'Du har ikke fyllt ut alle feltene.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>