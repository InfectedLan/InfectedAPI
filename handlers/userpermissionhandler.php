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

require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/user.php';
require_once 'objects/permission.php';

class UserPermissionHandler {
	/*
	 * Returns true if user has the given permission and event, otherwise false.
	 */
	public static function hasUserPermissionByEvent(User $user, Event $event, Permission $permission) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_userpermissions . '`
																WHERE (`eventId` = \'0\' OR `eventId` = \'' . $event->getId() . '\')
																AND `userId` = \'' . $user->getId() . '\'
																AND `permissionId` = \'' . $permission->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Returns true if user has the given permission, otherwise false.
	 */
	public static function hasUserPermission(User $user, Permission $permission) {
		return self::hasUserPermissionByEvent($user, EventHandler::getCurrentEvent(), $permission);
	}

	/*
	 * Returns true if user has the given permission value and event, otherwise false.
	 */
	public static function hasUserPermissionByValueAndEvent(User $user, Event $event, $value) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_userpermissions . '`
																WHERE (`eventId` = \'0\' OR `eventId` = \'' . $event->getId() . '\')
																AND `userId` = \'' . $user->getId() . '\'
																AND `permissionId` = (SELECT `id` FROM `' . Settings::db_table_infected_permissions . '`
																											WHERE `value` = \'' . $database->real_escape_string($value) . '\');');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Returns true if user has the given permission value, otherwise false.
	 */
	public static function hasUserPermissionByValue(User $user, $value) {
		return self::hasUserPermissionByValueAndEvent($user, EventHandler::getCurrentEvent(), $value);
	}

	/*
	 * Returns true if the specified user has any permissions by the given event.
	 */
	public static function hasUserPermissionsByEvent(User $user, Event $event) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_userpermissions . '`
																WHERE (`eventId` = \'0\' OR `eventId` = \'' . $event->getId() . '\')
																AND `userId` = \'' . $user->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Returns true if the specified user has any permissions.
	 */
	public static function hasUserPermissions(User $user) {
		return self::hasUserPermissionsByEvent($user, EventHandler::getCurrentEvent());
	}

	/*
	 * Returns a list of permissions for the specified user and event.
	 */
	public static function getUserPermissionsByEvent(User $user, Event $event) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
																WHERE `id` IN (SELECT `permissionId` FROM `' . Settings::db_table_infected_userpermissions . '`
																							 WHERE (`eventId` = \'0\' OR `eventId` = \'' . $event->getId() . '\')
																							 AND `userId` = \'' . $user->getId() . '\');');

		$database->close();

		$permissionList = [];

		while ($object = $result->fetch_object('Permission')) {
			$permissionList[] = $object;
		}

		return $permissionList;
	}

	/*
	 * Returns a list of permissions for the specified user.
	 */
	public static function getUserPermissions(User $user) {
		return self::getUserPermissionsByEvent($user, EventHandler::getCurrentEvent());
	}

	/*
	 * Create a new user permission.
	 */
	public static function createUserPermission(User $user, Permission $permission) {
		if (!self::hasUserPermission($user, $permission)) {
			$database = Database::open(Settings::db_name_infected);

			$database->query('INSERT INTO `' . Settings::db_table_infected_userpermissions . '` (`eventId`, `userId`, `permissionId`)
											  VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\',
															  \'' . $user->getId() . '\',
															  \'' . $permission->getId() . '\')');

			$database->close();
		}
	}

	/*
	 * Remove a user permission by event.
	 */
	public static function removeUserPermissionByEvent(User $user, Event $event, Permission $permission) {
		$database = Database::open(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_userpermissions . '`
											WHERE (`eventId` = \'0\' OR `eventId` = \'' . $event->getId() . '\')
											AND `userId` = \'' . $user->getId() . '\'
											AND `permissionId` = \'' . $permission->getId() . '\';');

		$database->close();
	}

	/*
	 * Remove a user permission.
	 */
	public static function removeUserPermission(User $user, Permission $permission) {
		self::removeUserPermissionByEvent($user, EventHandler::getCurrentEvent(), $permission);
	}

	/*
	 * Removes all permissions for the specified user and event.
	 */
	public static function removeUserPermissionsByEvent(User $user, Event $event) {
		$database = Database::open(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_userpermissions . '`
											WHERE (`eventId` = \'0\' OR `eventId` = \'' . $event->getId() . '\')
											AND `userId` = \'' . $user->getId() . '\';');

		$database->close();
	}

	/*
	 * Removes all permissions for the specified user.
	 */
	public static function removeUserPermissions(User $user) {
		self::removeUserPermissionsByEvent($user, EventHandler::getCurrentEvent());
	}
}
?>
