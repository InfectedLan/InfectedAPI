<?php
require_once 'session.php';
require_once 'handlers/avatarhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	$avatar = $user->getAvatar();

	if ($avatar != null) {
		AvatarHandler::deleteAvatar($avatar);
		$result = true;
	} else {
		$message = 'Du har ingen avatar!';
	}
} else {
	$message = 'Du er allerede logget inn!';
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>