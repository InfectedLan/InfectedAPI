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
					($page->getGroup()->getId() == $user->getGroup()->getId())) {
					$title = $_GET['title'];
					$content = $_GET['content'];
					$group = isset($_GET['groupId']) ? GroupHandler::getGroup($_GET['groupId']) : $page->getGroup();
					$team = isset($_GET['teamId']) ? TeamHandler::getTeam($_GET['teamId']) : $page->getTeam();
					
					RestrictedPageHandler::updatePage($page, $title, $content, $group, $team);
					$result = true;
				} else {
					$message = 'Du har ikke rettigheter til dette.';
				}
			} else {
				$message = 'Siden finnes ikke.';
			}
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