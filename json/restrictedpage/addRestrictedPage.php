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
require_once 'handlers/restrictedpagehandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') || 
		$user->hasPermission('chief.my-crew')) {
		if (isset($_GET['title']) &&
			isset($_GET['content']) &&
			!empty($_GET['title']) &&
			!empty($_GET['content'])) {
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$content = $_GET['content'];
			$group = isset($_GET['groupId']) ? GroupHandler::getGroup($_GET['groupId']) : null;
			$team = isset($_GET['teamId']) ? TeamHandler::getTeam($_GET['teamId']) : null;
			
			RestrictedPageHandler::createPage($name, $title, $content, $group, $team);
			$result = true;
		} else {
			$message = '<p>Du har ikke fyllt ut alle feltene!</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>