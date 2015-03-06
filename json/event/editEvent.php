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
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin-events')) {
		if (isset($_GET['id']) &&
			isset($_GET['theme']) &&
			isset($_GET['location']) &&
			isset($_GET['participants']) &&
			isset($_GET['bookingDate']) &&
			isset($_GET['bookingTime']) &&
			isset($_GET['startDate']) &&
			isset($_GET['startTime']) &&
			isset($_GET['endDate']) &&
			isset($_GET['endTime']) &&
			is_numeric($_GET['id']) &&
			is_numeric($_GET['location']) &&
			is_numeric($_GET['participants'])) {
			!empty($_GET['bookingDate']) &&
			!empty($_GET['bookingTime']) &&
			!empty($_GET['startDate']) &&
			!empty($_GET['startTime']) &&
			!empty($_GET['endDate']) &&
			!empty($_GET['endTime']) &&
			$event = EventHandler::getEvent($_GET['id']);
			$theme = $_GET['theme'];
			$location = $_GET['location'];
			$participants = $_GET['participants'];
			$bookingTime = $_GET['bookingDate'] . ' ' . $_GET['bookingTime'];
			$startTime = $_GET['startDate'] . ' ' . $_GET['startTime'];
			$endTime = $_GET['endDate'] . ' ' . $_GET['endTime'];
			
			if ($event != null) {
				EventHandler::updateEvent($event, $theme, $location, $participants, $bookingTime, $startTime, $endTime);
				$result = true;
			} else {
				$message = '<p>Arrangementet finnes ikke.</p>';
			}
		} else {
			$message = '<p>Du har ikke fyllt ut alle feltene!</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>