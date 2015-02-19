<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id']) && 
		isset($_GET['user'])) {
		$clan = ClanHandler::getClan($_GET['id']);

		if ($user->equals($clan->getChief())) {
			$inviteUser = UserHandler::getUser($_GET['user']);

			if ($inviteUser != null) {
				if ($inviteUser->isEligibleForCompos()) {
					$clan->invite($inviteUser);
					$result = true;
				} else {
					$message = '<p>Du kan ikke invitere denne brukeren.</p>';
				}
			} else {
				$message = '<p>Brukeren eksisterer ikke!</p>';
			}
		} else {
			$message = '<p>Du er ikke sjefen for denne klanen!</p>';
		}
	} else {
		$message = '<p>Mangler felt.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>