<?php
require_once 'session.php';
require_once 'utils.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/matchhandler.php';

$result = false;
$message = null;
$data = array();

if (Session::isAuthenticated()) {
	if (isset($_GET['id']) &&
		is_numeric($_GET['id'])) {
		$compo = CompoHandler::getCompo($_GET['id']);

		if ($compo != null) {
			foreach (MatchHandler::getMatchesForCompo($compo) as $match) {				
				array_push($data, array('matchId' => $match->getId(),
					  					'participants' => MatchHandler::getParticipantsJson($match),
					  					'startTime' => Utils::getDayFromInt(date('w', $match->getScheduledTime())) . ' ' . date('H:i', $match->getScheduledTime()),
					  					'bracketOffset' => $match->getBracketOffset(),
					  					'state' => $match->getState()));
			}

			$result = true;
		} else {
			$message = 'Compo\'en du oppga finnes ikke';
		}
	} else {
		$message = 'Du har ikke fylt ut alle feltene.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message, 'data' => $data));
?>