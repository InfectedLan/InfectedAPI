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
			$message = '<p>Ingen gruppe spesifisert.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>