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
require_once 'handlers/applicationhandler.php';
require_once 'handlers/eventhandler.php';

$result = false;
$status = http_response_code();
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('chief.application')) {
		if (isset($_POST['applicationId']) &&
            isset($_POST['comment']) &&
			is_numeric($_POST['applicationId']) &&
			!empty($_POST['comment'])) {
            $application = ApplicationHandler::getApplication($_POST['applicationId']);

            if ($application != null) {
                // Only allow application for current event to be rejected.
                if ($application->getEvent()->equals(EventHandler::getCurrentEvent())) {
                    $application->reject($user, $_POST['comment'], true);
                    $result = true;
                    $status = 202; // Accepted.
                } else {
                    $message = Localization::getLocale('you_can_not_reject_applications_from_previous_events');
                }
            } else {
                $status = 404; // Not found.
                $message = Localization::getLocale('this_application_does_not_exist');
            }
        } else {
            $status = 400; // Bad Request.
            $message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
        }
    } else {
        $message = Localization::getLocale('you_do_not_have_permission_to_do_that');
    }
} else {
    $message = Localization::getLocale('you_are_not_logged_in');
}

http_response_code($status);
header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();