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

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message, 'data' => $data), JSON_PRETTY_PRINT);
?>