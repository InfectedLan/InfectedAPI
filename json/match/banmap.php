<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/votehandler.php';
require_once 'handlers/voteoptionhandler.php';

$result = false;
$message = null;
$clanId = 0;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id']) && 
		isset($_GET['matchId']) &&
		is_numeric($_GET['id']) &&
		is_numeric($_GET['matchId'])) {
		$match = MatchHandler::getMatch($_GET['matchId']);
		
		if ($match != null) {
			$numBanned = VoteHandler::getNumBanned($match->getId());
			$turn = VoteHandler::getCurrentBanner($numBanned);
			
			if ($turn != 2) {
				$participants = MatchHandler::getParticipantsByMatch($match);
				$clan = $participants[$turn];
				
				if ($user->equals($clan->getChief())) {
					$voteOption = VoteOptionHandler::getVoteOption($_GET['id']);
					
					if ($voteOption != null) {
						if ($voteOption->getCompo()->equals($match->getCompo())) {
							VoteHandler::banMap($voteOption, $match->getId());
							
							if ($numBanned == 6) {
								$match->setState(2);
							}

							$result = true;
						} else {
							$message = '<p>Dette mappet er ikke for denne compoen!</p>';
						}
					} else {
						$message = '<p>Mappet finnes ikke!</p>';
					}
				} else {
					$message = '<p>Du har ikke lov til å banne nå!</p>';
				}
			} else {
				$message = '<p>Matchen holder på å starte!</p>';
			}
		} else {
			$message = '<p>Matchen finnes ikke.</p>';
		}
	} else {
		$message = '<p>Felt mangler!</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result, 'clanId' => $clanId), JSON_PRETTY_PRINT);
} else {
	echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
}
?>