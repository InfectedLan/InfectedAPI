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
											  'humanName' => $ticket->getHumanName(),
											  'owner' => $ticketUser->getDisplayName()));
			}

			$result = true;
		} else {
			$message = '<p>Arrangementet finnes ikke!</p>';
		}
	} else {
		$message = '<p>Ikke noe seatmap spesifisert.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn!</p>';
}

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result, 'tickets' => $ticketData), JSON_PRETTY_PRINT);
} else {
	echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
}
?>