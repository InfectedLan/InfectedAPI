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
			$avatar = AvatarHandler::getAvatar($_GET['id']);

			if ($avatar != null) {
				AvatarHandler::acceptAvatar($avatar);
				$result = true;
			} else {
				$message = '<p>Avataren finnes ikke.</p>';
			}
		} else {
			$message = '<p>Ingen avatar spesifisert.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>