<?php
require_once 'session.php';
require_once 'handlers/teamhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.teams') ||
		$user->isGroupLeader()) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$team = TeamHandler::getTeam($_GET['id']);
			$memberList = $team->getMembers();
			
			foreach ($memberList as $member) {
				if ($user->getId() != $member->getId()) {
					TeamHandler::removeUserFromTeam($member);
				}
			}
			
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