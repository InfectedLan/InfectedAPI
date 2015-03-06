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
require_once 'handlers/userhandler.php';
require_once 'handlers/permissionhandler.php';
require_once 'handlers/userpermissionhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.permissions')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$permissionUser = UserHandler::getUser($_GET['id']);
			
			if ($permissionUser != null) {
				foreach (PermissionHandler::getPermissions() as $permission) {
					// Only allow changes by admin or user with the "admin.permissions" to give permissions that he is assigned to other users.
					if ($user->hasPermission('*') ||
						$user->hasPermission('admin.permissions') && 
						$user->hasPermission($permission->getValue())) {
						if (isset($_GET['checkbox_' . $permission->getId()])) {
							UserPermissionHandler::createUserPermission($permissionUser, $permission);
						} else {
							UserPermissionHandler::removeUserPermission($permissionUser, $permission);
						}
					}
				}
		
				$result = true;
			} else  {
				$message = '<p>Brukeren finnes ikke.</p>';
			}
		} else {
			$message = '<p>Bruker ikke spesifisert.</p>';
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