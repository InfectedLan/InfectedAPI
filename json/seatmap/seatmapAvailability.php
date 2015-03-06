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

$result = false;
$message = null;
$seatmapData = null; //Array of rows
$backgroundImage = null; //File name of background image. Didnt know how else to do this.


if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id'])) {
		$seatmap = SeatmapHandler::getSeatmap($_GET['id']);
		
		if ($seatmap != null) {
			$seatmapData = array();
			$backgroundImage = $seatmap->getBackgroundImage();
			
			foreach ($seatmap->getRows() as $row) {
				$seatData = array();

				foreach ($row->getSeats() as $seat) {
					$data = array();

					$data['id'] = $seat->getId();
					$data['number'] = $seat->getNumber();
					$data['humanName'] = $seat->getString();

					$ticketUser = $seat->getTicket()->getUser();

					if ($ticketUser != null) {
						$data['occupied'] = true;
						$ticket = $seat->getTicket();
						$data['occupiedTicket'] = array('id' => $ticket->getId(), 
														'owner' => htmlspecialchars($owner->getDisplayName()) );
					} else {
						$data['occupied'] = false;
					}

					array_push($seatData, $data);
				}

				array_push($seatmapData, array('seats' => $seatData, 
											   'id' => $row->getId(), 
											   'x' => $row->getX(), 
											   'y' => $row->getY(), 
											   'number' => $row->getNumber()));
				$result = true;
			}
		} else {
			$message = '<p>Seatmappet finnes ikke.</p>';
		}
	} else {
		$message = '<p>Ikke noe seatmap spesifisert.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result, 'rows' => $seatmapData, 'backgroundImage' => $backgroundImage), JSON_PRETTY_PRINT);
} else {
	echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
}
?>