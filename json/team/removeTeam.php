<?php
require_once 'session.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('functions.teams') ||
		$user->isGroupLeader()) {
		if (isset($_GET['groupId']) &&
			isset($_GET['teamId']) &&
			is_numeric($_GET['groupId']) &&
			is_numeric($_GET['teamId'])) {
			$group = GroupHandler::getGroup($_GET['groupId']); 
			$team = TeamHandler::getTeam($_GET['teamId']); 
			
			TeamHandler::removeTeam($group, $team);
			$result = true;
		} else {
			$message = 'Det er ikke spesifisert et lag.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>