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
		if (isset($_GET['matchId']) && 
			isset($_GET['winnerId'])) {
			$match = MatchHandler::getMatch($_GET['matchId']);

			if ($match != null) {
				$clan = ClanHandler::getClan($_GET['winnerId']);

				if ($clan != null) {
					MatchHandler::setWinner($match, $clan);
					$result = true;
				} else {
					$message = '<p>Clanen finnes ikke!</p>';
				}
			} else {
				$message = '<p>Matchen finnes ikke!</p>';
			}
		} else {
			$message = '<p>Vi mangler felt.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>