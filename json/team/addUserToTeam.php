<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/teamhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief..teams')) {
		
		if (isset($_GET['userId']) &&
			isset($_GET['teamId']) &&
			is_numeric($_GET['userId']) &&
			is_numeric($_GET['teamId'])) {
			$groupUser = UserHandler::getUser($_GET['userId']);
			$group = $groupUser->getGroup();
			$team = TeamHandler::getTeam($_GET['teamId']);
			
			TeamHandler::changeTeamForUser($groupUser, $group, $team);
			$result = true;
		} else {
			$message = 'Ikke noe lag spesifisert.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>