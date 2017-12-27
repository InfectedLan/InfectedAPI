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
require_once 'handlers/compohandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('event.compo')) {
		if (isset($_GET['title']) &&
			isset($_GET['tag']) &&
			isset($_GET['startTime']) &&
			isset($_GET['startDate']) &&
			isset($_GET['registrationEndTime']) &&
			isset($_GET['registrationEndDate']) &&
			isset($_GET['teamSize']) &&
			!empty($_GET['title']) &&
			!empty($_GET['tag']) &&
			!empty($_GET['startTime']) &&
			!empty($_GET['startDate']) &&
			!empty($_GET['registrationEndTime']) &&
			!empty($_GET['registrationEndDate']) &&
			is_numeric($_GET['teamSize'])) {
			$compo = CompoHandler::getCompo($_GET['id']);
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$tag = $_GET['tag'];
			$description = $_GET['description'];
			$mode = $_GET['mode'];
			$startTime = $_GET['startDate'] . ' ' . $_GET['startTime'];
			$registrationEndTime = $_GET['registrationEndDate'] . ' ' . $_GET['registrationEndTime'];
			$teamSize = $_GET['teamSize'];

			if ($compo != null) {
				CompoHandler::updateCompo($compo, $name, $title, $tag, $description, $mode, $startTime, $registrationEndTime, $teamSize);
				$result = true;
			} else {
				$message = Localization::getLocale('the_compo_you_are_trying_to_change_does_not_exist');
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
