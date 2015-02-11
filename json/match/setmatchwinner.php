<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/matchhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('*') ||
		$user->hasPermission('event.compo')) {

		if (isset($_GET['matchId']) && isset($_GET['winnerId'])) {
			$match = MatchHandler::getMatch($_GET['matchId']);

			if (isset($match)) {
				$clan = ClanHandler::getClan($_GET['winnerId']);

				if (isset($clan)) {
					MatchHandler::setWinner($match, $clan);

					$result = true;
				} else {
					$message = "Clanen finnes ikke!";
				}
			} else {
				$message = "Matchen finnes ikke!";
			}
		} else {
			$message = 'Vi mangler felt';
		}
	} else {
		$message = "Du har ikke tillatelse til dette";
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>