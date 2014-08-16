<?php
require_once 'session.php';
require_once 'handlers/teamhandler.php';
require_once 'handlers/grouphandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.teams') ||
		$user->isGroupLeader()) {
		if (isset($_GET['teamId']) &&
			isset($_GET['groupId']) &&
			isset($_GET['title']) &&
			isset($_GET['description']) &&
			isset($_GET['leader']) &&
			is_numeric($_GET['teamId']) &&
			is_numeric($_GET['groupId']) &&
			!empty($_GET['title']) &&
			!empty($_GET['description']) &&
			is_numeric($_GET['leader'])) {
			$team = TeamHandler::getTeam($_GET['teamId']);
			$group = GroupHandler::getGroup($_GET['groupId']);
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$description = $_GET['description'];
			$leader = $_GET['leader'];

			TeamHandler::updateTeam($team, $group, $name, $title, $description, $leader);
			$result = true;
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