<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$removeUser = GroupHandler::getGroup($_GET['id']); 
			
			if ($removeUser != null) {
				UserHandler::removeUser($removeUser);
				$result = true;
			} else {
				$message = 'Brukeren finnes ikke.';
			}
		} else {
			$message = 'Det er ikke spessifisert noen bruker.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>