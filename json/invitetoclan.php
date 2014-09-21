<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';

$result = false;
$message = null;
$clanId = 0;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if(isset($_GET['id']) && isset($_GET['user'])) {
		$clan = ClanHandler::getClan($_GET['id']);

		if($clan->getChief() == $user->getId()) {

			$invitee = UserHandler::getUser($_GET['user']);

			if(isset($invitee)) {
				if($invitee->isEligibleForCompos()) {
					$compo = ClanHandler::getCompo($clan);

					$inClan = count(ClanHandler::getInvites($clan)) + count(ClanHandler::getMembers($clan));

					if($inClan < $compo->getTeamSize()) {
						ClanHandler::inviteUser($clan, $invitee);
					} else {
						$message = "Laget er fullt!";
					}
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

if($result) {
	echo json_encode(array('result' => $result, 'clanId' => $clanId));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>