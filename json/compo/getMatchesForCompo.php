<?php
require_once 'session.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/matchhandler.php';
require_once 'utils/dateutils.php';

$result = false;
$message = null;
$data = array();

if (Session::isAuthenticated()) {
	if (isset($_GET['id']) &&
		is_numeric($_GET['id'])) {
		$compo = CompoHandler::getCompo($_GET['id']);

		if ($compo != null) {
			foreach ($compo->getMatches() as $match) {
				$parentMatchIds = array();
				
				foreach ($match->getParents() as $parentMatch) {
					array_push($parentMatchIds, $parentMatch->getId());
				}

				array_push($data, array('matchId' => $match->getId(),
					  					'participants' => MatchHandler::getParticipantTags($match),
					  					'parents' => $parentMatchIds,
					  					'startTime' => DateUtils::getDayFromInt(date('w', $match->getScheduledTime())) . ' ' . date('H:i', $match->getScheduledTime()),
					  					'bracketOffset' => $match->getBracketOffset(),
					  					'bracket' => $match->getBracket(),
					  					'state' => $match->getState()));
			}

			$result = true;
		} else {
			$message = '<p>Compo\'en du oppga finnes ikke.</p>';
		}
	} else {
		$message = '<p>Du har ikke fylt ut alle feltene.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message, 'data' => $data));
?>