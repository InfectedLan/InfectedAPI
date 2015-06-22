<?php
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
require_once 'handlers/applicationhandler.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.application')) {
		if (isset($_GET['applicationId']) &&
			is_numeric($_GET['applicationId'])) {
			$application = ApplicationHandler::getApplication($_GET['applicationId']);
			$comment = isset($_GET['comment']) ? $_GET['comment'] : null;
			
			if ($application != null) {
				if ($application->getEvent()->equals(EventHandler::getCurrentEvent())) {
					$application->accept($user, $comment, true);
					$result = true;
				} else {
					$message = Localization::getLocale('you_can_not_approve_applications_from_previous_events');
				}
			} else {
				$message = Localization::getLocale('this_application_does_not_exist');
			}
		} else {
			$message = Localization::getLocale('no_application_specified');
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