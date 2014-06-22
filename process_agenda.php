<?php
require_once 'siteDatabase.php';
require_once 'utils.php';

session_start();

$database = new SiteDatabase();
$utils = new Utils();

$action = isset($_GET['action']) ? $_GET['action'] : 0;
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$returnPage = isset($_GET['returnPage']) ? '../index.php?page=' . $_GET['returnPage'] : '../';

if ($utils->isAuthenticated()) {
	$user = $utils->getUser();
	
	if ($user->isGroupMember()) {
		$group = $user->getGroup();
	
		if ($user->hasPermission('functions.info') || 
			$group->getId() == 15 || 
			$group->getId() == 26 || 
			$user->hasPermission('admin') || 
			$user->hasPermission('site-admin')) {
			if (isset($_GET['action'])) {
				if ($action == 1) {
					if (isset($_POST['date']) && 
						isset($_POST['time']) && 
						isset($_POST['name']) && 
						isset($_POST['description'])) {
						$date = $_POST['date'];
						$time = date('Y-m-d H:i:s', strtotime($date . ' ' . $_POST['time']));
						$name = $_POST['name'];
						$description = $_POST['description'];
						
						$database->createAgenda($time, $name, $description);
					}
				} else if ($action == 2) {
					if (isset($_GET['id'])) {
						$database->removeAgenda($id);
					}
				} else if ($action == 3) {
					if (isset($_GET['id'])) {
						if (isset($_POST['date']) && 
							isset($_POST['time']) && 
							isset($_POST['name']) && 
							isset($_POST['description'])) {
							$date = $_POST['date'];
							$time = $date . ' ' . $_POST['time'];
							$name = $_POST['name'];
							$description = $_POST['description'];
							
							$database->updateAgenda($id, $time, $name, $description);
						}
					}
				}
			}
		}
	}
}

header('Location: ' . $returnPage);
?>