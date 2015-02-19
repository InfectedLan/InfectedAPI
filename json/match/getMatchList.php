<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/voteoptionhandler.php';

$result = false;
$message = null;
$matchArray = array();

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('*') ||
		$user->hasPermission('event.compo')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$compo = CompoHandler::getCompo($_GET['id']);

			if ($compo != null) {
				$pendingArray = array();

				foreach (MatchHandler::getPendingMatchesByCompo($compo) as $match) {
					$matchData = array();

					$matchData['id'] = $match->getId();
					$matchData['startTime'] = $match->getScheduledTime();
					$matchData['startString'] = date('d F H:i', $match->getScheduledTime());
					$matchData['connectData'] = $match->getConnectDetails();
					$matchData['participants'] = MatchHandler::getParticipantStringByMatch($match);

					array_push($pendingArray, $matchData);
				}

				$matchArray['pending'] = $pendingArray;
				$currentArray = array();

				foreach (MatchHandler::getCurrentMatchesByCompo($compo) as $match) {
					$matchData = array();

					$matchData['id'] = $match->getId();
					$matchData['startTime'] = $match->getScheduledTime();
					$matchData['startString'] = date('d F H:i', $match->getScheduledTime());
					$matchData['connectData'] = $match->getConnectDetails();
					$matchData['state'] = $match->getState();

					$participantData = array();
					$participantData['strings'] = MatchHandler::getParticipantStringByMatch($match);
					$participantObjects = MatchHandler::getParticipantsByMatch($match);
					$participantData['list'] = array();

					foreach ($participantObjects as $participant) {
						$data = array();

						$data['name'] = $participant->getName();
						$data['id'] = $participant->getId();
						$data['tag'] = $participant->getTag();

						array_push($participantData['list'], $data);
					}


					$matchData['participants'] = $participantData;
					$compo = $match->getCompo();
					
					if ($compo->getId() == 3) {
						$hasVotedMaps = false;

						foreach (VoteOptionHandler::getVoteOptionsByCompo($compo) as $option) {
							if (!VoteOptionHandler::isVoted($option, $match)) {
								$mapData = array();

								$mapData['name'] = $option->getName();

								$matchData['mapData'] = $mapData;
								break;
							} else {
								$hasVotedMaps = true;
							}
						}

						if(!$hasVotedMaps) {
							$matchData['mapData'] = array('name' => 'pending');
						}
					}

					array_push($currentArray, $matchData);
				}

				$matchArray['current'] = $currentArray;
				$finishedArray = array();

				foreach (MatchHandler::getFinishedMatchesByCompo($compo) as $match) {
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
				$message = '<p>Vi mangler felt.</p>';
			}
		} else {
			$message = '<p>Compoen finnes ikke.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse!</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

if ($result) {
	echo json_encode(array('result' => $result, 'data' => $matchArray));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>