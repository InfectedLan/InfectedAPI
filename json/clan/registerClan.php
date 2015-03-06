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
require_once 'handlers/componhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;
$clanId = 0;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->isEligibleForCompos()) {
		if (isset($_GET['name']) &&
			isset($_GET['tag']) &&
			isset($_GET['compo'])) {
			$compo = CompoHandler::getCompo($_GET['compo']);

			if ($compo != null) {
				$clan = ClanHandler::createClan(EventHandler::getCurrentEvent(), $_GET['name'], $_GET['tag'], $compo, $user);
				$result = true;
			} else {
				$message = '<p>Compo\'en finnes ikke.</p>';
			}
		} else {
			$message = '<p>Mangler felt!</p>';
		}
	} else {
		$message = '<p>Du kan ikke lage en clan! Har du en billett?</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result, 'clanId' => $clan->getId()), JSON_PRETTY_PRINT);
} else {
	echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
}
?>