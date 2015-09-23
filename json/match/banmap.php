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
						}
					} else {
						$message = Localization::getLocale('this_map_does_not_exist');
					}
				}
			} else {
				$message = Localization::getLocale('this_match_is_currently_starting');
			}
		} else {
			$message = Localization::getLocale('this_match_does_not_exist');
		}
	} else {
		$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result, 'clanId' => $clanId), JSON_PRETTY_PRINT);
} else {
	echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
}
?>