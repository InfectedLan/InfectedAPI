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
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('admin.event')) {
		if (isset($_GET['location']) &&
			isset($_GET['participants']) &&
			isset($_GET['bookingDate']) &&
			isset($_GET['bookingTime']) &&
			isset($_GET['startDate']) &&
			isset($_GET['startTime']) &&
			isset($_GET['endDate']) &&
			isset($_GET['endTime']) &&
			is_numeric($_GET['location']) &&
			is_numeric($_GET['participants']) &&
			!empty($_GET['bookingDate']) &&
			!empty($_GET['bookingTime']) &&
			!empty($_GET['startDate']) &&
			!empty($_GET['startTime']) &&
			!empty($_GET['endDate']) &&
			!empty($_GET['endTime'])) {
			$location = LocationHandler::getLocation($_GET['location']);
			$participants = $_GET['participants'];
			$bookingTime = $_GET['bookingDate'] . ' ' . $_GET['bookingTime'];
			$startTime = $_GET['startDate'] . ' ' . $_GET['startTime'];
			$endTime = $_GET['endDate'] . ' ' . $_GET['endTime'];

			if ($location != null) {
				EventHandler::createEvent($location, $participants, $bookingTime, $startTime, $endTime);
				$result = true;
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

header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();
?>
