<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
require_once 'localization.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/voteoptionhandler.php';

$result = false;
$message = null;
$matchArray = [];

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('event.compo')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$compo = CompoHandler::getCompo($_GET['id']);

			if ($compo != null) {
				$pendingArray = [];

				foreach (MatchHandler::getPendingMatchesByCompo($compo) as $match) {
					$matchData = [];

					$matchData['id'] = $match->getId();
					$matchData['startTime'] = $match->getScheduledTime();
					$matchData['startString'] = date('d F H:i', $match->getScheduledTime());
					$matchData['connectData'] = $match->getConnectDetails();
					$matchData['participants'] = MatchHandler::getParticipantStringByMatch($match);

					array_push($pendingArray, $matchData);
				}

				$matchArray['pending'] = $pendingArray;
				$currentArray = [];

				foreach (MatchHandler::getCurrentMatchesByCompo($compo) as $match) {
					$matchData = [];

					$matchData['id'] = $match->getId();
					$matchData['startTime'] = $match->getScheduledTime();
					$matchData['startString'] = date('d F H:i', $match->getScheduledTime());
					$matchData['connectData'] = $match->getConnectDetails();
					$matchData['state'] = $match->getState();

					$participantData = [];
					$participantData['strings'] = MatchHandler::getParticipantStringByMatch($match);
					$participantObjects = MatchHandler::getParticipantsByMatch($match);
					$participantData['list'] = [];

					foreach ($participantObjects as $participant) {
						$data = [];

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
								$mapData = [];

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
				$finishedArray = [];

				foreach (MatchHandler::getFinishedMatchesByCompo($compo) as $match) {
					$matchData = [];

					$matchData['id'] = $match->getId();
					$matchData['startTime'] = $match->getScheduledTime();
					$matchData['startString'] = date('d F H:i', $match->getScheduledTime());
					$matchData['connectData'] = $match->getConnectDetails();

					//Winner stuff
					$winnerArray = [];
					$winnerArray['id'] = $match->getWinner();
					$clan = ClanHandler::getClan($match->getWinner());
					$winnerArray['name'] = $clan->getName() . ' - ' . $clan->getTag();

					$matchData['winner'] = $winnerArray;

					//$matchData['participants'] = MatchHandler::getParticipantString($match);

					$participantData = [];
					$participantData['list'] = [];

					foreach ($participantObjects as $participant) {
						$data = [];

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
				$message = Localization::getLocale('this_compo_does_not_exist');
			}
		} else {
			$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
		}
	} else {
		$message = Localization::getLocale('you_do_not_have_permission_to_do_that');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result, 'data' => $matchArray), JSON_PRETTY_PRINT);
} else {
	echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
}
?>
