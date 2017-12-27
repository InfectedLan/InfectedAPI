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

require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/user.php';
require_once 'objects/permission.php';

class UserPermissionHandler {
	/*
	 * Returns true if user has the given permission, otherwise false.
	 */
	public static function hasUserPermission(User $user, Permission $permission, Event $event = null): bool {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_userpermissions . '`
                                   WHERE (`eventId` = 0 OR `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . ')
                                   AND `userId` = ' . $user->getId() . '
                                   AND `permissionId` = ' . $permission->getId() . '
                                   GROUP BY `permissionId`;');

		return $result->num_rows > 0;
	}

	/*
	 * Returns true if user has the given permission value and event, otherwise false.
	 */
	public static function hasUserPermissionByValue(User $user, string $value, Event $event = null): bool {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_userpermissions . '`
                                   WHERE (`eventId` = 0 OR `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . ')
                                   AND `userId` = ' . $user->getId() . '
                                   AND `permissionId` = (SELECT `id` FROM `' . Settings::db_table_infected_permissions . '`
                                                         WHERE `value` = ' . $database->real_escape_string($value) . '
                                                         LIMIT 1)
                                   GROUP BY `permissionId`;');

		return $result->num_rows > 0;
	}

	/*
	 * Returns true if the specified user has any permissions.
	 */
	public static function hasUserPermissions(User $user, Event $event = null): bool {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_userpermissions . '`
                                   WHERE (`eventId` = 0 OR `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . ')
                                   AND `userId` = ' . $user->getId() . '
                                   GROUP BY `permissionId`;');

		return $result->num_rows > 0;
	}

	/*
	 * Returns a list of permissions for the specified user and event.
	 */
	public static function getUserPermissions(User $user, Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `permissionId` FROM `' . Settings::db_table_infected_userpermissions . '`
								   WHERE (`eventId` = 0 OR `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . ')
								   AND `userId` = ' . $user->getId() . ';');

		$permissionIdList = [];

		while ($object = $result->fetch_assoc()) {
			$permissionIdList[] = $object['permissionId'];
		}

		return PermissionHandler::getPermissionsByValues($permissionIdList);
	}

	/*
	 * Create a new user permission.
	 */
	public static function createUserPermission(User $user, Permission $permission, Event $event = null) {
		if (!self::hasUserPermission($user, $permission)) {
			$database = Database::getConnection(Settings::db_name_infected);

			$database->query('INSERT INTO `' . Settings::db_table_infected_userpermissions . '` (`eventId`, `userId`, `permissionId`)
                             VALUES (' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . ',
                                     ' . $user->getId() . ',
                                     ' . $permission->getId() . ');');
		}
	}

	/*
	 * Remove a user permission by event.
	 */
	public static function removeUserPermission(User $user, Permission $permission, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_userpermissions . '`
                         WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
                         AND `userId` = ' . $user->getId() . '
                         AND `permissionId` = ' . $permission->getId() . ';');
	}

	/*
	 * Removes all permissions for the specified user.
	 */
	public static function removeUserPermissions(User $user, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_userpermissions . '`
                         WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
                         AND `userId` = ' . $user->getId() . ';');
	}
}