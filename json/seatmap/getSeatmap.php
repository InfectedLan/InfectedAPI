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
require_once 'handlers/seatmaphandler.php';
require_once 'handlers/rowhandler.php';
require_once 'handlers/seathandler.php';

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
					$seatData[] = ['id' => $seat->getId(),
												 'number' => $seat->getNumber(),
												 'humanName' => $seat->getString()];
				}

				$seatmapData[] = ['seats' => $seatData,
												  'id' => $row->getId(),
												  'x' => $row->getX(),
												  'y' => $row->getY(),
						  						'number' => $row->getNumber(),
						  						'horizontal' => $row->isHorizontal()];
			}

			$result = true;
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
	echo json_encode(['result' => $result, 'rows' => $seatmapData, 'backgroundImage' => $backgroundImage], JSON_PRETTY_PRINT);
} else {
	echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
}

Database::cleanup();
?>
