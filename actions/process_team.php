<?php
require_once 'database.php';
require_once 'utils.php';

session_start();

$database = new Database();
$utils = new Utils();

$action = isset($_GET['action']) ? $_GET['action'] : 0;
$groupId = isset($_GET['groupId']) ? $_GET['groupId'] : 0;
$teamId = isset($_GET['teamId']) ? $_GET['teamId'] : 0;
$userId = isset($_GET['userId']) ? $_GET['userId'] : 0;
$returnPage = isset($_GET['returnPage']) ? '../index.php?page=' . $_GET['returnPage'] : '../';

if (isset($_GET['returnPage']) && $utils->isAuthenticated()) {
	$user = $utils->getUser();

	if ($user->hasPermission('chief.teams') || 
		$user->isGroupMember() && $user->isGroupChief() || 
		$user->hasPermission('admin') || 
		$user->hasPermission('crew-admin')) {
		if (isset($_GET['action'])) {
			/* Add team */
			if ($action == 1) {
				if (isset($_GET['groupId']) && 
					isset($_POST['title']) && 
					isset($_POST['description']) && 
					isset($_POST['chief'])) {
					$name = strtolower(str_replace(' ', '-', $_POST['title']));
					$title = $_POST['title'];
					$description = $_POST['description'];
					$chief = $_POST['chief'];
					
					$database->createTeam($groupId, $name, $title, $description, $chief);
				}
			/* Remove team */
			} else if ($action == 2) {
				if (isset($_GET['teamId'])) {
					$database->removeTeam($teamId);
				}
			/* Update team */
			} else if ($action == 3) {
				if (isset($_GET['groupId']) && 
					isset($_POST['title']) && 
					isset($_POST['description']) && 
					isset($_POST['chief'])) {
					$name = strtolower(str_replace(' ', '-', $_POST['title']));
					$title = $_POST['title'];
					$description = $_POST['description'];
					$chief = $_POST['chief'];
					
					$database->updateTeam($teamId, $groupId, $name, $title, $description, $chief);
				}
			/* Adds user to team */
			} else if ($action == 4) {
				if (isset($_GET['teamId']) && 
					isset($_POST['userId'])) {
					$user = $database->getUser($_POST['userId']);
					
					// Sets team to the specified teamId.
					$user->setTeam($teamId);
				}
			/* Removes user from team */
			} else if ($action == 5) {
				if (isset($_GET['userId'])) {
					$user = $database->getUser($_GET['userId']);
					
					// Sets team to 0, which means none.
					$user->setTeam(0);
				}
			}
		}
	}
}

header('Location: ' . $returnPage);
?>