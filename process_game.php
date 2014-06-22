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
	
		if ($user->hasPermission('functions.site-list-games') || 
			$group->getId() == 26 || 
			$user->hasPermission('admin') || 
			$user->hasPermission('site-admin')) {
			if (isset($_GET['action'])) {
				if ($action == 1) {
					if (isset($_POST['title']) && 
						isset($_POST['price']) && 
						isset($_POST['mode']) && 
						isset($_POST['deadlineDate']) && 
						isset($_POST['deadlineTime']) && 
						isset($_POST['content'])) {
						$title = $_POST['title'];
						$name = strtolower(str_replace(' ', '-', str_replace(':', '', $title)));
						$price = $_POST['price'];
						$mode = $_POST['mode'];
						$description = $_POST['description'];
						$deadline = date('Y-m-d H:i:s', strtotime($_POST['deadlineDate'] . ' ' . $_POST['deadlineTime']));
						$published = true;
						
						$content = '<article class="contentBox">' . $_POST['content'] . '</article>';
						
						$database->createGame($name, $title, $price, $mode, $description, $deadline, $published);
						$database->createPage($name, $title, $content);
					}
				} else if ($action == 2) {
					if (isset($_GET['id'])) {
						$game = $database->getGame($id);
						
						if ($game != null) {
							$database->removeGame($id);
							$database->removePageByName($game->getName());
						}
					}
				} else if ($action == 3) {
					if (isset($_POST['title']) && 
						isset($_POST['price']) && 
						isset($_POST['mode']) && 
						isset($_POST['deadlineDate']) && 
						isset($_POST['deadlineTime'])) {
						$title = $_POST['title'];
						$name = strtolower(str_replace(' ', '-', $title));
						$price = $_POST['price'];
						$mode = $_POST['mode'];
						$description = $_POST['description'];
						$deadline = date('Y-m-d H:i:s', strtotime($_POST['deadlineDate'] . ' ' . $_POST['deadlineTime']));
						$published = $_POST['published'];
						
						$database->updateGame($id, $name, $title, $price, $mode, $description, $deadline, $published);
					}
				}
			}
		}
	}
}

header('Location: ' . $returnPage);
?>