<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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
require_once 'database.php';
require_once 'localization.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/matchhandler.php';
require_once 'utils/dateutils.php';

$result = false;
$message = null;
$data = [];

if (Session::isAuthenticated()) {
	if (isset($_GET['id']) &&
		is_numeric($_GET['id'])) {
		$compo = CompoHandler::getCompo($_GET['id']);

		if ($compo != null) {
			foreach ($compo->getMatches() as $match) {
				$parentMatchIds = [];

				foreach ($match->getParents() as $parentMatch) {
					$parentMatchIds[] = $parentMatch->getId();
				}

				$data[] = ['matchId' => $match->getId(),
				  				 'participants' => MatchHandler::getParticipantTags($match),
				  				 'parents' => $parentMatchIds,
				  				 'startTime' => DateUtils::getDayFromInt(date('w', $match->getScheduledTime())) . ' ' . date('H:i', $match->getScheduledTime()),
				  				 'bracketOffset' => $match->getBracketOffset(),
				  				 'bracket' => $match->getBracket(),
				  				 'state' => $match->getState()];
			}

			$result = true;
		} else {
			$message = Localization::getLocale('this_compo_does_not_exist');
		}
	} else {
		$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message, 'data' => $data], JSON_PRETTY_PRINT);
Database::cleanup();
?>
