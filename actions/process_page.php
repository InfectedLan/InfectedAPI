<?php
require_once 'database.php';
require_once 'siteDatabase.php';
require_once 'utils.php';

session_start();

$utils = new Utils();

$site = isset($_GET['site']) ? $_GET['site'] : 0;

if ($site == 0) {
	$database = new SiteDatabase();
} else if ($site == 1) {
	$database = new Database();
}

$action = isset($_GET['action']) ? $_GET['action'] : 0;
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$returnPage = isset($_GET['returnPage']) ? '../index.php?page=' . $_GET['returnPage'] : '../';

if ($utils->isAuthenticated()) {
	$user = $utils->getUser();
	
	if ($user->isGroupMember()) {
		$group = $user->getGroup();
		$teamId = isset($_GET['teamId']) ? $_GET['teamId'] : $user->getTeam()->getId();
		
		if ($user->hasPermission('functions.site-list-pages') || 
			$user->hasPermission('functions.mycrew') || 
			$user->hasPermission('functions.edit-page') || 
			$user->isGroupChief() || 
			$user->hasPermission('admin') || 
			$user->hasPermission('crew-admin') || 
			$user->hasPermission('site-admin')) {
			
			if (isset($_GET['action'])) {
				if ($action == 1) {
					if (isset($_POST['title']) &&
						isset($_POST['content'])) {
						$title = $_POST['title'];
						$name = strtolower(str_replace(' ', '-', $title));
						$content = $_POST['content'];
						
						if ($site == 0) {
							$database->createPage($name, $title, $content);
						} else if ($site == 1) {
							$database->createPage($name, $title, $content, $group->getId(), $teamId);
						}
					}
				} else if ($action == 2) {
					if (isset($_GET['id'])) {
						$database->removePage($id);
					}
				} else if ($action == 3) {
					if (isset($_GET['id'])) {
						if (isset($_POST['title']) &&
							isset($_POST['content'])) {
							$title = $_POST['title'];
							$content = $_POST['content'];
							
							$database->updatePage($id, $title, $content);
						}
					}
				}
			}
		}
	}
}

header('Location: ' . $returnPage);
?>