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
require_once 'handlers/userhandler.php';
require_once 'handlers/matchhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('*') ||
		$user->hasPermission('event.compo')) {
		if (isset($_GET['matchId']) && 
			isset($_GET['winnerId'])) {
			$match = MatchHandler::getMatch($_GET['matchId']);

			if ($match != null) {
				$clan = ClanHandler::getClan($_GET['winnerId']);

				if ($clan != null) {
					MatchHandler::setWinner($match, $clan);
					$result = true;
				} else {
					$message = '<p>Clanen finnes ikke!</p>';
				}
			} else {
				$message = '<p>Matchen finnes ikke!</p>';
			}
		} else {
			$message = '<p>Vi mangler felt.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>