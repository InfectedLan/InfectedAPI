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
require_once 'handlers/applicationhandler.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.applications')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$application = ApplicationHandler::getApplication($_GET['id']);
			$comment = isset($_GET['comment']) ? $_GET['comment'] : null;
			
			if ($application != null) {
				if ($application->getEvent()->equals(EventHandler::getCurrentEvent())) {
					$application->accept($user, $comment, true);
					$result = true;
				} else {
					$message = '<p>Kan ikke godkjenne søknader for tidligere arrangementer.</p>';
				}
			} else {
				$message = '<p>Søknaden finnes ikke.</p>';
			}
		} else {
			$message = '<p>Ingen søknad spesifisert.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>