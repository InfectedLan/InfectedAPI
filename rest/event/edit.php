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
require_once 'handlers/eventhandler.php';
require_once 'handlers/locationhandler.php';

$result = false;
$status = http_response_code();
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('admin.event')) {
		if (isset($_POST['id']) &&
		    isset($_POST['locationId']) &&
            isset($_POST['participantCount']) &&
            isset($_POST['bookingDate']) &&
            isset($_POST['bookingTime']) &&
            isset($_POST['prioritySeatingDate']) &&
            isset($_POST['prioritySeatingTime']) &&
            isset($_POST['seatingDate']) &&
            isset($_POST['seatingTime']) &&
            isset($_POST['startDate']) &&
            isset($_POST['startTime']) &&
            isset($_POST['endDate']) &&
            isset($_POST['endTime']) &&
		    is_numeric($_POST['id']) &&
			is_numeric($_POST['locationId']) &&
			is_numeric($_POST['participantCount']) &&
            !empty($_POST['bookingDate']) &&
			!empty($_POST['bookingTime']) &&
			!empty($_POST['prioritySeatingDate']) &&
			!empty($_POST['prioritySeatingTime']) &&
			!empty($_POST['seatingDate']) &&
			!empty($_POST['seatingTime']) &&
			!empty($_POST['startDate']) &&
			!empty($_POST['startTime']) &&
			!empty($_POST['endDate']) &&
			!empty($_POST['endTime'])) {
			$event = EventHandler::getEvent($_POST['id']);
			$location = LocationHandler::getLocation($_POST['locationId']);
			$participantCount = $_POST['participantCount'];
			$bookingTime = $_POST['bookingDate'] . ' ' . $_POST['bookingTime'];
			$prioritySeatingTime = $_POST['prioritySeatingDate'] . ' ' . $_POST['prioritySeatingTime'];
			$seatingTime = $_POST['seatingDate'] . ' ' . $_POST['seatingTime'];
			$startTime = $_POST['startDate'] . ' ' . $_POST['startTime'];
			$endTime = $_POST['endDate'] . ' ' . $_POST['endTime'];

			if ($event != null && $location != null) {
                EventHandler::updateEvent($event, $location, $participantCount, $bookingTime, $prioritySeatingTime, $seatingTime, $startTime, $endTime);
                $result = true;
                $status = 202; // Accepted.
			} else {
                $status = 404; // Not found.
				$message = Localization::getLocale('this_event_does_not_exist');
			}
		} else {
            $status = 400; // Bad Request.
			$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
		}
	} else {
		$message = Localization::getLocale('you_do_not_have_permission_to_do_that');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

http_response_code($status);
header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();