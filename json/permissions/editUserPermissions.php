<?php
include 'database.php';
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
require_once 'handlers/userhandler.php';
require_once 'handlers/permissionhandler.php';
require_once 'handlers/userpermissionhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('admin.permissions')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$permissionUser = UserHandler::getUser($_GET['id']);

			if ($permissionUser != null) {
				foreach (PermissionHandler::getPermissions() as $permission) {
					// Only allow changes by admin or user with the "admin.permissions" to give permissions that he is assigned to other users.
					if ($user->hasPermission($permission->getValue())) {
						if (isset($_GET['checkbox_' . $permission->getId()])) {
							UserPermissionHandler::createUserPermission($permissionUser, $permission);
						} else {
							UserPermissionHandler::removeUserPermission($permissionUser, $permission);
						}
					}
				}

				$result = true;
			} else {
				$message = Localization::getLocale('this_user_does_not_exist');
			}
		} else {
			$message = Localization::getLocale('no_user_specified');
		}
	} else {
		$message = Localization::getLocale('you_do_not_have_permission_to_do_that');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
?>
