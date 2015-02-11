<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (isset($_GET['user']) && isset($_GET['clan'])) {
		$targetUser = UserHandler::getUser($_GET['user']);
		
		if (isset($targetUser)) {
			$clan = ClanHandler::getClan($_GET['clan']);
			
			if (isset($clan)) {
				if ($clan->getChief() == $user->getId()) {
					$compo = ClanHandler::getCompo($clan);
					$currentMainPlayers = ClanHandler::getPlayingMembers($clan);

					if (count($currentMainPlayers) < $compo->getTeamSize()) {
						ClanHandler::setMemberStepinState($clan, $targetUser, ClanHandler::STATE_MAIN_PLAYER);
						$result = true;
					} else {
						$message = "Det er allerede for mange spillere som deltar!";
					}
				} else {
					$message = "Du er ikke chief";
				}
			} else {
				$message = "Klanen finnes ikke!";
			}
		} else {
			$message = "Brukeren du prøvde å kicke finnes ikke!";
		}
	} else {
		$message = 'Vi mangler felt';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>