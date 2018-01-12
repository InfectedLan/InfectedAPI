<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2018 Infected <https://infected.no/>.
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
			$seatmapData = [];
			$backgroundImage = $seatmap->getBackgroundImage();

			foreach ($seatmap->getRows() as $row) {
				$seatData = [];

				foreach ($row->getSeats() as $seat) {
					$data = [];

					$data['id'] = $seat->getId();
					$data['number'] = $seat->getNumber();
					$data['humanName'] = $seat->getString();

					if ($seat->hasTicket()) {
						$ticket = $seat->getTicket();

						$data['occupied'] = true;
						$data['occupiedTicket'] = ['id' => $ticket->getId(),
												   'owner' => htmlspecialchars($ticket->getUser()->getDisplayName()),
												   'isFriend' => $user->isFriendsWith($ticket->getUser())];
					} else {
						$data['occupied'] = false;
					}

					$seatData[] = $data;
				}

				$seatmapData[] = ['seats' => $seatData,
								  'id' => $row->getId(),
								  'x' => $row->getX(),
								  'y' => $row->getY(),
								  'number' => $row->getNumber(),
								  'horizontal' => $row->isHorizontal()];

				$result = true;
			}
		} else {
			$message = Localization::getLocale('this_seatmap_does_not_exist');
		}
	} else {
		$message = Localization::getLocale('no_seatmap_specified');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: application/json');

if ($result) {
	echo json_encode(array('result' => $result, 'rows' => $seatmapData, 'backgroundImage' => $backgroundImage), JSON_PRETTY_PRINT);
} else {
	echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
}

Database::cleanup();