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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
require_once 'handlers/agendahandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('event.agenda')) {
		if (isset($_GET['id']) &&
			isset($_GET['title']) &&
			isset($_GET['description']) &&
			isset($_GET['startTime']) &&
			isset($_GET['startDate']) &&
			is_numeric($_GET['id']) &&
			!empty($_GET['title']) &&
			!empty($_GET['description']) &&
			!empty($_GET['startTime']) &&
			!empty($_GET['startDate'])) {
			$agenda = AgendaHandler::getAgenda($_GET['id']);
			$title = $_GET['title'];
			$description = $_GET['description'];
			$startTime = $_GET['startDate'] . ' ' . $_GET['startTime'];
			$published = isset($_GET['published']) ? $_GET['published'] : 0;
			
			if ($agenda != null) {
				AgendaHandler::updateAgenda($agenda, $title, $description, $startTime, $published);
				$result = true;
			} else {
				$message = Localization::getLocale('the_object_you_are_trying_to_change_does_not_exist');
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

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>