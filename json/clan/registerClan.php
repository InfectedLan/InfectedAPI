<?php
include 'database.php';
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
require_once 'handlers/compohandler.php';
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
				$message = Localization::getLocale('this_compo_does_not_exist');
			}
		} else {
			$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
		}
	} else {
		$message = Localization::getLocale('you_cannot_create_a_new_clan_you_need_a_valid_ticket');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result, 'clanId' => $clan->getId()), JSON_PRETTY_PRINT);
} else {
	echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
}
?>