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
			$clan = ClanHandler::getClan($invite->getClanId());

			if ($user->equals($invite->getUser()) || 
				$user->equals($clan->getChief())) {
				$invite->decline();
				$result = true;
			} else {
				$message = '<p>Denne invitasjonen er ikke din!</p>';
			}
		} else {
			$message = '<p>Invitasjonen finnes ikke!</p>';
		}
	} else {
		$message = '<p>Felt mangler!</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

if ($result) {
	echo json_encode(array('result' => $result, 'clanId' => $clanId));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>