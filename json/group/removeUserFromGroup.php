<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/grouphandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.groups')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$groupUser = UserHandler::getUser($_GET['id']);
			
			if ($groupUser != null) {
				GroupHandler::removeUserFromGroup($groupUser);
				$result = true;
			} else {
				$message = '<p>Brukeren finnes ikke.</p>';
			}
		} else {
			$message = '<p>Ingen bruker spesifisert.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>