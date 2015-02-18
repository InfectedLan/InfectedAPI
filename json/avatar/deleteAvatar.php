<?php
require_once 'session.php';
require_once 'handlers/avatarhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	// Sjekk om brukeren har en avatar.
	if ($user->hasAvatar()) {
		$avatar = $user->getAvatar();

		if ($avatar != null) {
			AvatarHandler::deleteAvatar($avatar);
			$result = true;
		} else {
			$message = '<p>Avataren finnes ikke.</p>';
		}
	} else {
		$message = '<p>Du har ingen avatar!</p>';
	}
} else {
	$message = '<p>Du er allerede logget inn!</p>';
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>