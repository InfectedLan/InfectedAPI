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
require_once 'handlers/applicationhandler.php';
require_once 'handlers/grouphandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	// Only allow non-members to apply.
	if (!$user->isGroupMember()) {
		
		// Check that the user has an cropped avatar.
		if ($user->hasCroppedAvatar()) {
			if (isset($_GET['groupId']) &&
				isset($_GET['content']) &&
				is_numeric($_GET['groupId']) &&
				!empty($_GET['content'])) {
				$group = GroupHandler::getGroup($_GET['groupId']);
				$content = $_GET['content'];
				
				if (!ApplicationHandler::hasUserApplicationByGroup($user, $group)) {
					ApplicationHandler::createApplication($group, $user, $content);
					$result = true;
					$message = '<p>Din søknad til crewet "' . $group->getTitle() . '" er nå sendt.</p>';
				} else {
					$message = '<p>Du har allerede søkt til ' . $group->getTitle() . ' crew. Du kan søke igjen hvis søknaden din skulle bli avslått.</p>';
				}
			} else {
				$message = '<p>Du har ikke fyllt ut alle feltene.</p>';
			}
		} else {
			$message = '<p>Du må laste opp en avatar før du kan søke!</p>';
		}
	} else {
		$message = '<p>Du er allerede med i et crew.</p>';
	}
} else {
	$message = '<p>Du er allerede logget inn!</p>';
} 

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>