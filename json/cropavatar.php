<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/avatarhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	$avatar = $user->getAvatar();
	if($user->hasAvatar()) {
		// Sjekk om avataren skal croppes
		$state = $avatar->getState();
		if($state == 0) {
			if(isset($_GET["x"])&&isset($_GET["y"])&&isset($_GET["w"])&&isset($_GET["h"])) {
				
			} else {
				$message = "Felt mangler!";
			}
		} else {
			$message = "Du har ingen avatar som ikke har blitt beskjært!";
		}
	} else {
		$message = "Du har ingen avatar!";
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));

?>