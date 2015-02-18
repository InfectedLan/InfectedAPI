<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/teamhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.teams')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$groupUser = UserHandler::getUser($_GET['id']);
			
			TeamHandler::removeUserFromTeam($groupUser);
			$result = true;
		} else {
			$message = '<p>Ingen bruker spesifisert.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>