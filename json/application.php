<?php
require_once 'session.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/applicationhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (!$user->isGroupMember()) {
		if (isset($_POST['groupId']) &&
			isset($_POST['content']) &&
			/* is_int($_POST['groupId']) && */
			!empty($_POST['content'])) {
			$group = GroupHandler::getGroup($_POST['groupId']);
			$content = $_POST['content'];
		
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
	$message = "Du er allerede logget inn!";
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>