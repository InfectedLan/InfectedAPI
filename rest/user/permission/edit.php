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
require_once 'handlers/permissionhandler.php';
require_once 'handlers/userpermissionhandler.php';
require_once 'handlers/sysloghandler.php';

$result = false;
$status = http_response_code();
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('admin.permission')) {
		if (is_numeric($_POST['id'])) {
			$permissionUser = UserHandler::getUser($_POST['id']);

			if ($permissionUser != null) {
				//Get list of permissions before change
				$prePermissions = $permissionUser->getPermissions();

				foreach (PermissionHandler::getPermissions() as $permission) {
					// Only allow changes by admin or user with the "admin.permissions" to give permissions that he is assigned to other users.
					if ($user->hasPermission($permission->getValue())) {
						if (isset($_POST['checkbox_' . $permission->getId()])) {
							UserPermissionHandler::createUserPermission($permissionUser, $permission);
						} else {
							UserPermissionHandler::removeUserPermission($permissionUser, $permission);
						}
					}
				}

				// Permissions after change.
				// Everything below here is for logging.
				$postPermissions = $permissionUser->getPermissions();

				//Calculate added permissions
				$addedList = [];

				foreach($postPermissions as $perm) {
					$exists = false;

					foreach ($prePermissions as $permPre) {
						if ($perm == $permPre) {
							$exists = true;
						}
					}

					if(!$exists) {
						$addedList[] = $perm->getValue();
					}
				}

				// Calculate removed permissions.
				$removedList = [];

				foreach ($prePermissions as $perm) {
					$exists = false;

					foreach ($postPermissions as $permPost) {
						if ($perm==$permPost) {
							$exists = true;
						}
					}

					if (!$exists) {
						$removedList[] = $perm->getValue();
					}
				}

                $result = true;
                $status = 202; // Accepted.
				SyslogHandler::log("Permissions for user " . $permissionUser->getId() . " were changed", "editUserPermissions", $user, SyslogHandler::SEVERITY_INFO, ["affected_user" => $permissionUser->getId(), "added" => $addedList, "removed" => $removedList]);
			} else {
                $status = 404; // Not found.
				$message = Localization::getLocale('this_user_does_not_exist');
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
