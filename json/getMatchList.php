<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/matchhandler.php';

$result = false;
$message = null;
$matchArray = array();

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if($user->hasPermission("*") || $user->hasPermission("functions.compoadmin")) {
		if(isset($_GET['id'])) {
			$compo = CompoHandler::getCompo($_GET['id']);

			//First, get pending matches
			$pendingMatches = MatchHandler::getPendingMatches($compo);

			$pendingArray = array();
			foreach($pendingMatches as $match) {
				$matchData = array();

				$matchData['id'] = $match->getId();
				$matchData['startTime'] = $match->getScheduledTime();
				$matchData['startString'] = date('d F H:i', $match->getScheduledTime());

				$matchData['participants'] = MatchHandler::getParticipantString($match);

				array_push($pendingArray, $matchData);
			}
			$matchArray['pending'] = $pendingArray;

			//Get current matches
			$currentMatches = MatchHandler::getCurrentMatches($compo);

			$currentArray = array();
			foreach($currentMatches as $match) {
				$matchData = array();

				$matchData['id'] = $match->getId();
				$matchData['startTime'] = $match->getScheduledTime();
				$matchData['startString'] = date('d F H:i', $match->getScheduledTime());

				$matchData['participants'] = MatchHandler::getParticipantString($match);

				array_push($currentArray, $matchData);
			}
			$matchArray['current'] = $currentArray;

			//Get finished matches

			$finishedMatches = MatchHandler::getFinishedMatches($compo);

			$finishedArray = array();
			foreach($finishedMatches as $match) {
				$matchData = array();

				$matchData['id'] = $match->getId();
				$matchData['startTime'] = $match->getScheduledTime();
				$matchData['startString'] = date('d F H:i', $match->getScheduledTime());

				$matchData['participants'] = MatchHandler::getParticipantString($match);

				array_push($currentArray, $matchData);
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

if($result) {
	echo json_encode(array('result' => $result, 'data' => $matchArray));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>