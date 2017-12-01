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
require_once 'handlers/eventhandler.php';
require_once 'handlers/tickethandler.php';

$result = false;
$message = null;
$data = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	if ($user->hasPermission('stats')) {
		if (isset($_GET['id'])) {
			$event = EventHandler::getEvent($_GET["id"]);

			if ($event != null) {
				//Crew
				$members = EventHandler::getMembersByEvent($event);

				$memberAgeSlots = array_fill(0,100,0);

				foreach($members as $person) {
					$memberAgeSlots[floor($person->getAge($event))]++;
				}

				//Participants
				$participants = EventHandler::getParticipantsByEvent($event);

				$participantAgeSlots = array_fill(0,100,0);
				foreach($participants as $person) {
					$participantAgeSlots[floor($person->getAge($event))]++;
				}

				$data = ["participants" => $participantAgeSlots, "crew" => $memberAgeSlots];
				$result = true;
			} else {
				$message = Localization::getLocale('this_event_does_not_exist');
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
if($result) {
	echo json_encode(['result' => $result, 'message' => $message, "data" => $data], JSON_PRETTY_PRINT);
} else {
	echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
}
Database::cleanup();
?>
