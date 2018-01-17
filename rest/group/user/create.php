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
require_once 'handlers/userhandler.php';
require_once 'handlers/grouphandler.php';

$result = false;
$status = http_response_code();
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('chief.group')) {
		if (isset($_GET['groupId']) &&
			isset($_GET['userId']) &&
			is_numeric($_GET['groupId']) &&
			is_numeric($_GET['userId'])) {
			$group = GroupHandler::getGroup($_GET['groupId']);

			if ($group != null) {
				$groupUser = UserHandler::getUser($_GET['userId']);

				if ($groupUser != null) {
					GroupHandler::addGroupMember($groupUser, $group);
					$result = true;
                    $status = 201; // Created.
				} else {
                    $status = 404; // Not found.
					$message = Localization::getLocale('this_user_does_not_exist');
				}
			} else {
                $status = 404; // Not found.
				$message = Localization::getLocale('this_group_does_not_exist');
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