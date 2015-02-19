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
			$team = TeamHandler::getTeam($_GET['teamId']);
			
			if ($groupUser != null &&
				$team != null) {
				TeamHandler::changeTeamForUser($groupUser, $team);
				$result = true;
			} else {
				$message = '<p>Brukeren, gruppem eller laget finnes ikke.</p>';
			}
		} else {
			$message = '<p>Ikke noe lag spesifisert.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>