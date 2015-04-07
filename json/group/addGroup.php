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
require_once 'handlers/grouphandler.php';
require_once 'handlers/restrictedpagehandler.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.groups')) {
		if (isset($_GET['title']) &&
			isset($_GET['description']) &&
			isset($_GET['leader']) &&
			isset($_GET['coleader']) &&
			!empty($_GET['title']) &&
			!empty($_GET['description'])) {
			$name = strtolower(str_replace(' ', '-', $_GET['title']));
			$title = $_GET['title'];
			$description = $_GET['description'];
			$leaderUser = UserHandler::getUser($_GET['leader']);
			$coleaderUser = UserHandler::getUser($_GET['leader']);
			
			GroupHandler::createGroup(EventHandler::getCurrentEvent(), $name, $title, $description, $leaderUser, $coleaderUser);
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

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>