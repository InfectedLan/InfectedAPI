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
require_once 'handlers/agendahandler.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('event.agenda')) {
		if (isset($_GET['title']) &&
			isset($_GET['description']) &&
			isset($_GET['startTime']) &&
			isset($_GET['startDate']) &&
			!empty($_GET['title']) &&
			!empty($_GET['description']) &&
			!empty($_GET['startTime']) &&
			!empty($_GET['startDate'])) {
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$description = $_GET['description'];
			$startTime = $_GET['startDate'] . ' ' . $_GET['startTime'];

			AgendaHandler::createAgenda(EventHandler::getCurrentEvent(), $name, $title, $description, $startTime);
			$result = true;
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
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
?>
