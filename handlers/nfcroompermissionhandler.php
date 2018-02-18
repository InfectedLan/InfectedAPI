<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2018 Infected <https://infected.no/>.
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

require_once 'handlers/roomhandler.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/event.php';
require_once 'objects/room.php';
require_once 'objects/nfcroompermission.php';
require_once 'objects/user.php';
require_once 'settings.php';
require_once 'databaseconstants.php';
require_once 'database.php';

class NfcRoomPermissionHandler {

	public static function getPermission(int $id): NfcRoomPermission {
		$database = Database::getConnection(Settings::db_name_infected_tech);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_tech_roompermissions . '` WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('NfcRoomPermission');
	}

	public static function createPermission(Room $room, int $type, int $arg): NfcRoomPermission {
		$database = Database::getConnection(Settings::db_name_infected_tech);

		$database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_tech_roompermissions . '` (`roomId`, `permissionType`, `permissionArg`) VALUES (\'' . $database->real_escape_string($room->getId()) . '\', ' .
																																									$database->real_escape_string($type) . ', ' .
																																									$database->real_escape_string($arg) . ');');

		return self::getPermission($database->insert_id);
	}

	public static function getPermissionsByRoom(Room $room) : array {
		$database = Database::getConnection(Settings::db_name_infected_tech);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_tech_roompermissions . '` WHERE `roomId` = ' . $database->real_escape_string($room->getId()) . ';');

		$entryList = [];

		while($object = $result->fetch_object('NfcRoomPermission')) {
			$entryList[] = $object;
		}

		return $entryList;
	}

	/*
	 * Returns if an user is allowed to enter a given room for a given event
	 */
	public static function hasUserPermission(Room $room, User $user, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_tech);

		if($event == null) {
			$event = EventHandler::getCurrentEvent();
		}

		$selfCheck = $database->query('SELECT `id` FROM ' . DatabaseConstants::db_table_infected_tech_roompermissions . ' WHERE `permissionType` = 0 AND `permissionArg` = ' . $user->getId() . ';');
		if($selfCheck->num_rows != 0)
			return true;

		if($user->isGroupMember()) {
			$group = $user->getGroup($event);

			$groupCheck = $database->query('SELECT `id` FROM `' . DatabaseConstants::db_table_infected_tech_roompermissions . '` WHERE `permissionType` = 1 AND (`permissionArg` = 0 OR `permissionArg` = ' . $group->getId() . ')');

			return $groupCheck->num_rows != 0;
		}
		return false;
	}
}
?>
