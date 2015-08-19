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
require_once 'handlers/seatmaphandler.php';
require_once 'handlers/tickethandler.php';

$result = false;
$message = null;
$ticketData = array();

if (Session::isAuthenticated()) {
	if (isset($_GET['seatmap'])) {
		$seatmap = SeatmapHandler::getSeatmap($_GET['seatmap']);
		
		if ($seatmap != null) {
			$user = Session::getCurrentUser();
			$ticketList = TicketHandler::getTicketsSeatableByUser($user);
			
			foreach ($ticketList as $ticket) {
				$ticketUser = $ticket->getUser();

				array_push($ticketData, array('id' => $ticket->getId(),
											  'humanName' => $ticket->getString(),
											  'owner' => $ticketUser->getDisplayName()));
			}

			$result = true;
		} else {
			$message = Localization::getLocale('the_event_does_not_exist');
		}
	} else {
		$message = Localization::getLocale('no_seatmap_specified');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result, 'tickets' => $ticketData), JSON_PRETTY_PRINT);
} else {
	echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
}
?>