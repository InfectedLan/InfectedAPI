<?php
require_once 'session.php';
require_once 'handlers/grouphandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.groups')) {
		
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$group = GroupHandler::getGroup($_GET['id']);
			$memberList = $group->getMembers();
			
			foreach ($memberList as $member) {
				if (!$user->equals($member)) {
					GroupHandler::removeUserFromGroup($member);
				}
			}
			
			$result = true;
		} else {
			$message = 'Ingen gruppe spesifisert.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>