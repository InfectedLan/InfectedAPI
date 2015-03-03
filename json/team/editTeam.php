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
require_once 'handlers/teamhandler.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.teams')) {
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

			if ($team != null &&
				$group != null) {
				TeamHandler::updateTeam($team, $group, $name, $title, $description, $leaderUser);
				$result = true;
			} else {
				$message = '<p>Laget eller gruppen finnes ikke.</p>';
			}
		} else {
			$message = '<p>Du har ikke fylt ut alle feltene.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>