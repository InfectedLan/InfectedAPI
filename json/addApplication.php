<?php
require_once 'session.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/applicationhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (!$user->isGroupMember()) {
		if (isset($_GET['groupId']) &&
			isset($_GET['content']) &&
			is_numeric($_GET['groupId']) &&
			!empty($_GET['content'])) {
			$group = GroupHandler::getGroup($_GET['groupId']);
			$content = $_GET['content'];
		
			ApplicationHandler::createApplication($user, $group, $content);
			
			$result = true;
			$message = 'Din søknad til ' . $group->getTitle() . ' crew er sendt.';
		} else {
			$message = 'Du har ikke fyllt ut alle feltene.';
		}
	} else {
		$message = 'Du er allerede med i et crew.';
	}
} else {
	$message = 'Du er allerede logget inn!';
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>