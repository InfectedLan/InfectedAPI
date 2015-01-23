<?php
require_once 'session.php';
require_once 'handlers/avatarhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.avatars')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			AvatarHandler::rejectAvatar($_GET['id']);
			$result = true;
		} else {
			$message = 'Ingen avatar spesifisert.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>