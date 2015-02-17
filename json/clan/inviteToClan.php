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
			$invite = UserHandler::getUser($_GET['user']);

			if ($invite != null) {
				if ($invite->isEligibleForCompos()) {
					$compo = ClanHandler::getCompo($clan);
					$inClan = count(ClanHandler::getInvites($clan)) + count(ClanHandler::getMembers($clan));

					ClanHandler::inviteUser($clan, $invite);
					$result = true;
				} else {
					$message = "Du kan ikke invitere denne brukeren.";
				}
			} else {
				$message = "Brukeren eksisterer ikke!";
			}
		} else {
			$message = "Du er ikke sjefen for denne klanen!";
		}
	} else {
		$message = "Mangler felt!";
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>