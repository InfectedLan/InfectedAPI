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
require_once 'database.php';
require_once 'localization.php';
require_once 'handlers/applicationhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('chief.application')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			if (isset($_GET['comment']) &&
				!empty($_GET['comment'])) {
				$application = ApplicationHandler::getApplication($_GET['id']);
				$comment = $_GET['comment'];

				if ($application != null) {
					// Only allow application for current event to be rejected.
					if ($application->getEvent()->equals(EventHandler::getCurrentEvent())) {
						$application->reject($user, $comment, true);
						$result = true;
					} else {
						$message = Localization::getLocale('you_can_not_reject_applications_from_previous_events');
					}
				} else {
					$message = Localization::getLocale('this_application_does_not_exist');
				}
			} else {
				$message = Localization::getLocale('you_must_provide_a_reason_why_the_application_shall_be_rejected');
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
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();
?>
