<?php
require_once 'database.php';
require_once 'utils.php';

session_start();

$database = new Database();
$utils = new Utils();

$action = isset($_GET['action']) ? $_GET['action'] : 0;
$groupId = isset($_GET['groupId']) ? $_GET['groupId'] : 0;
$returnPage = isset($_GET['returnPage']) ? '../index.php?page=' . $_GET['returnPage'] : '../';

if (isset($_GET['returnPage']) && $utils->isAuthenticated()) {
	$user = $utils->getUser();
	
	if ($user->hasPermission('chief.groups') || 
		$user->isGroupMember() && $user->isGroupChief() || 
		$user->hasPermission('admin') || 
		$user->hasPermission('crew-admin')) {
		if (isset($_GET['action'])) {
			/* Add group */
			if ($action == 1) {
				if (isset($_POST['title']) &&
					isset($_POST['description']) && 
					isset($_POST['chief'])) {
					$name = strtolower(str_replace(' ', '-', $_POST['title']));
					$title = $_POST['title'];
					$description = $_POST['description'];
					$chief = $_POST['chief'];
					
					$database->createGroup($name, $title, $description, $chief);
				}
			/* Remove group */
			} else if ($action == 2) {
				if (isset($_GET['groupId'])) {
					$database->removeGroup($groupId);
				}
			/* Update group */
			} else if ($action == 3) {
				if (isset($_GET['groupId']) && 
					isset($_POST['title']) && 
					isset($_POST['description']) && 
					isset($_POST['chief'])) {
					$name = strtolower(str_replace(' ', '-', $_POST['title']));
					$title = $_POST['title'];
					$description = $_POST['description'];
					$chief = $_POST['chief'];
					
					$database->updateGroup($groupId, $name, $title, $description, $chief);
				}
			/* Add user to group */
			} else if ($action == 4) {
				if (isset($_GET['groupId']) && 
					isset($_POST['userId'])) {
					$user = $database->getUser($_POST['userId']);
					
					// Sets group to the specified groupId.
					$user->setGroup($groupId);
				}
			/* Removes user from any group */
			} else if ($action == 5) {
				if (isset($_GET['userId'])) {
					$user = $database->getUser($_GET['userId']);
					
					// Sets group to 0, which means none.
					$user->setGroup(0);
				}
			}
		}
	}
}

header('Location: ' . $returnPage);
?>