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
require_once 'handlers/teamhandler.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('chief.team')) {
		if (isset($_GET['teamId']) &&
			isset($_GET['groupId']) &&
			isset($_GET['title']) &&
			isset($_GET['description']) &&
			isset($_GET['leader']) &&
			is_numeric($_GET['teamId']) &&
			is_numeric($_GET['groupId']) &&
			!empty($_GET['title']) &&
			!empty($_GET['description']) &&
			is_numeric($_GET['leader'])) {
			$team = TeamHandler::getTeam($_GET['teamId']);
			$group = GroupHandler::getGroup($_GET['groupId']);
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$description = $_GET['description'];
			$leaderUser = UserHandler::getUser($_GET['leader']);

			if ($group != null && $team != null) {
				TeamHandler::updateTeam($team, $group, $name, $title, $description, $leaderUser);
				$result = true;
			} else {
				$message = Localization::getLocale('this_team_does_not_exist');
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
