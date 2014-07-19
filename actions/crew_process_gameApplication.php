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
	
	if ($user->hasPermission('functions.site-list-games') || 
		$user->hasPermission('admin') || 
		$user->hasPermission('site-admin')) {
		if (isset($_GET['action'])) {
			if ($action == 1) {
				if (isset($_POST['game']) &&
					isset($_POST['name']) &&
					isset($_POST['tag']) &&
					isset($_POST['contactname']) &&
					isset($_POST['contactnick']) &&
					isset($_POST['phone']) &&
					isset($_POST['email'])) {
					$game = $_POST['game'];
					$name = $_POST['name'];
					$tag = $_POST['tag'];
					$contactname = $_POST['contactname'];
					$contactnick = $_POST['contactnick'];
					$phone = $_POST['phone'];
					$email = $_POST['email'];
					
					$database->createGameApplication($game, $name, $tag, $contactname, $contactnick, $phone, $email);
				}
			} else if ($action == 2) {
				if (isset($_GET['id'])) {
					$database->removeGameApplication($id);
				}
			}
		}
	}
}

header('Location: ' . $returnPage);
?>