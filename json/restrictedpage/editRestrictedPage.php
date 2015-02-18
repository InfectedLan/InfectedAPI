<?php
require_once 'session.php';
require_once 'handlers/restrictedpagehandler.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.my-crew')) {
		if (isset($_GET['id']) &&
			isset($_GET['title']) &&
			isset($_GET['content']) &&
			is_numeric($_GET['id']) &&
			!empty($_GET['title']) &&
			!empty($_GET['content'])) {
			$page = RestrictedPageHandler::getPage($_GET['id']);
			
			if ($page != null) {
				if ($user->hasPermission('*') ||
					$user->hasPermission('chief.my-crew') &&
					($page->getGroup()->equals($user->getGroup()))) {
					$title = $_GET['title'];
					$content = $_GET['content'];
					$group = isset($_GET['groupId']) ? GroupHandler::getGroup($_GET['groupId']) : $page->getGroup();
					$team = isset($_GET['teamId']) ? TeamHandler::getTeam($_GET['teamId']) : $page->getTeam();
					
					RestrictedPageHandler::updatePage($page, $title, $content, $group, $team);
					$result = true;
				} else {
					$message = '<p>Du har ikke rettigheter til dette.</p>';
				}
			} else {
				$message = '<p>Siden finnes ikke.</p>';
			}
		} else {
			$message = '<p>Du har ikke fylt ut alle feltene.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>