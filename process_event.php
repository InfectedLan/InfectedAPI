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
	
	if ($user->hasPermission('admin.events') || 
		$user->hasPermission('admin')) {
		if (isset($_GET['action'])) {
			if ($action == 1) {
				if (isset($_POST['theme']) &&
					isset($_POST['participants']) &&
					isset($_POST['price']) &&
					isset($_POST['startDate']) &&
					isset($_POST['startTime']) &&
					isset($_POST['endDate']) &&
					isset($_POST['endTime'])) {
					$theme = $_POST['theme'];
					$participants = $_POST['participants'];
					$price = $_POST['price'];
					$start = date('Y-m-d H:i:s', strtotime($_POST['startDate'] . ' ' . $_POST['startTime']));
					$end = date('Y-m-d H:i:s', strtotime($_POST['endDate'] . ' ' . $_POST['endTime']));
					
					$database->createEvent($theme, $participants, $price, $start, $end);
				}
			} else if ($action == 2) {
				if (isset($_GET['id'])) {
					$database->removeEvent($id);
				}
			} else if ($action == 3) {
				if (isset($_GET['id'])) {
					if (isset($_POST['theme']) &&
						isset($_POST['participants']) &&
						isset($_POST['price']) &&
						isset($_POST['startDate']) &&
						isset($_POST['startTime']) &&
						isset($_POST['endDate']) &&
						isset($_POST['endTime'])) {
						$theme = $_POST['theme'];
						$participants = $_POST['participants'];
						$price = $_POST['price'];
						$start = date('Y-m-d H:i:s', strtotime($_POST['startDate'] . ' ' . $_POST['startTime']));
						$end = date('Y-m-d H:i:s', strtotime($_POST['endDate'] . ' ' . $_POST['endTime']));
						
						$database->updateEvent($id, $theme, $participants, $price, $start, $end);
					}
				}
			}
		}
	}
}

header('Location: ' . $returnPage);
?>