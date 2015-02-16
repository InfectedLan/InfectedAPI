<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';

$result = false;
$message = null;
$clanId = 0;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id'])) {
		$invite = InviteHandler::getInvite($_GET['id']);
		
		if ($invite != null) {
			if ($invite->getUserId() == $user->getId()) {
				$invite->accept();
				$result = true;
			} else {
				$message = 'Denne invitasjonen er ikke din!';
			}
		} else {
			$message = 'Invitasjonen finnes ikke!';
		}
	} else {
		$message = 'Felt mangler!';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

if ($result) {
	echo json_encode(array('result' => $result, 'clanId' => $clanId));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>