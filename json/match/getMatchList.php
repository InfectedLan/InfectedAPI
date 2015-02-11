<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/clanhandler.php';

$result = false;
$message = null;
$matchArray = array();

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('*') ||
		$user->hasPermission('event.compo')) {
		
		if (isset($_GET['id'])) {
			$compo = CompoHandler::getCompo($_GET['id']);

			//First, get pending matches
			$pendingMatches = MatchHandler::getPendingMatches($compo);

			$pendingArray = array();

			foreach ($pendingMatches as $match) {
				$matchData = array();

				$matchData['id'] = $match->getId();
				$matchData['startTime'] = $match->getScheduledTime();
				$matchData['startString'] = date('d F H:i', $match->getScheduledTime());
				$matchData['connectData'] = $match->getConnectDetails();

				$matchData['participants'] = MatchHandler::getParticipantString($match);

				array_push($pendingArray, $matchData);
			}

			$matchArray['pending'] = $pendingArray;

			//Get current matches
			$currentMatches = MatchHandler::getCurrentMatches($compo);

			$currentArray = array();

			foreach ($currentMatches as $match) {
				$matchData = array();

				$matchData['id'] = $match->getId();
				$matchData['startTime'] = $match->getScheduledTime();
				$matchData['startString'] = date('d F H:i', $match->getScheduledTime());
				$matchData['connectData'] = $match->getConnectDetails();
				$matchData['state'] = $match->getState();

				$participantData = array();
				$participantData['strings'] = MatchHandler::getParticipantString($match);
				$participantObjects = MatchHandler::getParticipants($match);
				$participantData['list'] = array();

				foreach ($participantObjects as $participant) {
					$data = array();

					$data['name'] = $participant->getName();
					$data['id'] = $participant->getId();
					$data['tag'] = $participant->getTag();

					array_push($participantData['list'], $data);
				}


				$matchData['participants'] = $participantData;

				array_push($currentArray, $matchData);
			}
			$matchArray['current'] = $currentArray;

			//Get finished matches

			$finishedMatches = MatchHandler::getFinishedMatches($compo);

			$finishedArray = array();

			foreach ($finishedMatches as $match) {
				$matchData = array();

				$matchData['id'] = $match->getId();
				$matchData['startTime'] = $match->getScheduledTime();
				$matchData['startString'] = date('d F H:i', $match->getScheduledTime());
				$matchData['connectData'] = $match->getConnectDetails();

				//Winner stuff
				$winnerArray = array();

					$winnerArray['id'] = $match->getWinner();
					$clan = ClanHandler::getClan($match->getWinner());
					$winnerArray['name'] = $clan->getName() . ' - ' . $clan->getTag();

				$matchData['winner'] = $winnerArray;

				//$matchData['participants'] = MatchHandler::getParticipantString($match);

				$participantData = array();
				$participantData['list'] = array();

				foreach ($participantObjects as $participant) {
					$data = array();

					$data['name'] = $participant->getName();
					$data['id'] = $participant->getId();
					$data['tag'] = $participant->getTag();

					array_push($participantData['list'], $data);
				}


				$matchData['participants'] = $participantData;

				array_push($finishedArray, $matchData);
			}
			$matchArray['finished'] = $finishedArray;

			$result = true;
		} else {
			$message = 'Vi mangler felt';
		}
	} else {
		$message = "Du har ikke tillatelse!";
	}
} else {
	$message = 'Du er ikke logget inn.';
}

if ($result) {
	echo json_encode(array('result' => $result, 'data' => $matchArray));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>