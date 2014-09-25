<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/matchhandler.php';

$result = false;
$message = null;
$matchData = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if(isset($_GET['id'])) {
		$match = MatchHandler::getMatch($_GET['id']);

		if($match->isParticipant($user)|| $user->hasPermission('*') || $user->hasPermission('functions.compoadmin')) {
			$matchData['state'] = $match->getState();
			$matchData['ready'] = $match->isReady();
		} else {
			$message = "Du har ikke lov til å se på denne matchen!";
		}
	} else {
		$message = "Mangler felt!";
	}
} else {
	$message = 'Du er ikke logget inn.';
}

if($result) {
	echo json_encode(array('result' => $result, 'matchData' => $matchData));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>