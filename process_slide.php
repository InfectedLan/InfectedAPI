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
					if (isset($_POST['startDate']) && 
						isset($_POST['endDate']) && 
						isset($_POST['startTime']) && 
						isset($_POST['endTime']) && 
						isset($_POST['title']) && 
						isset($_POST['content'])) {		
						$endDate = $_POST['enddate'];
						$start = date('Y-m-d H:i:s', strtotime($_POST['startDate'] . ' ' . $_POST['startTime']));
						$end = date('Y-m-d H:i:s', strtotime($_POST['endDate'] . ' ' . $_POST['endTime']));
						$title = $_POST['title'];
						$content = $_POST['content'];
						$published = true;
						
						$database->createSlide($start, $end, $title, $content, $published);
					}
				} else if ($action == 2) {
					if (isset($_GET['id'])) {
						$database->removeSlide($id);
					}
				} else if ($action == 3) {
					if (isset($_GET['id'])) {
						if (isset($_POST['startDate']) && 
							isset($_POST['endDate']) && 
							isset($_POST['startTime']) && 
							isset($_POST['endTime']) && 
							isset($_POST['title']) && 
							isset($_POST['content'])) {
							$start = date('Y-m-d H:i:s', strtotime($_POST['startDate'] . ' ' . $_POST['startTime']));
							$end = date('Y-m-d H:i:s', strtotime($_POST['endDate'] . ' ' . $_POST['endTime']));
							$title = $_POST['title'];
							$content = $_POST['content'];
							$published = $_POST['published'];
							
							$database->updateSlide($id, $start, $end, $title, $content, $published);
						}
					}
				}
			}
		}
	}
}

header('Location: ' . $returnPage);
?>