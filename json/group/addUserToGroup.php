/*
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

<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/grouphandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.groups')) {
		if (isset($_GET['userId']) &&
			isset($_GET['groupId']) &&
			is_numeric($_GET['userId']) &&
			is_numeric($_GET['groupId'])) {
			$groupUser = UserHandler::getUser($_GET['userId']);
			$group = GroupHandler::getGroup($_GET['groupId']);
			
			if ($group != null && 
				$groupUser != null) {
				GroupHandler::changeGroupForUser($groupUser, $group);
				$result = true;
			} else {
				$message = '<p>Noe gikk galt, mangler bruker eller gruppe.</p>';
			}
		} else {
			$message = '<p>Du har ikke fyllt ut alle feltene.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>