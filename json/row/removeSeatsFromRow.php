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
require_once 'handlers/rowhandler.php';
require_once 'handlers/seathandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('admin.seatmap')) {
		if (isset($_GET['row'])) {
			$row = RowHandler::getRow($_GET['row']);

			if ($row != null) {
				if (isset($_GET['numSeats']) &&
					is_numeric($_GET['numSeats'])) {
					$numSeats = $_GET['numSeats'];
					$seats = SeatHandler::getSeatsByRow($row);

					if (count($seats)>=$numSeats) {
						$startIndex = count($seats) - 1;
						$endIndex = $startIndex - $numSeats;
						$safeToDelete = true;

						for ($i = $startIndex; $i > $endIndex; $i--) {
							if (SeatHandler::hasTicket($seats[$i])) {
								$safeToDelete = false;
							}
						}

						if ($safeToDelete) {
							for ($i = $startIndex; $i > $endIndex; $i--) {
								SeatHandler::removeSeat($seats[$i]);
							}

							$result = true;
						} else {
							$message = Localization::getLocale('the_seat_you_are_trying_to_delete_is_occupied');
						}
					} else {
						$message = Localization::getLocale('there_are_fewer_seats_in_the_row_than_you_are_trying_to_delete');
					}
				} else {
					$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
				}
			} else {
				$message = Localization::getLocale('this_row_does_not_exist');
			}
		} else {
			$message = Localization::getLocale('no_row_specified');
		}
	} else {
		$message = Localization::getLocale('you_do_not_have_permission_to_do_that');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>
