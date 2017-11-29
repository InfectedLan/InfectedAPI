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
require_once 'objects/permission.php';

class PermissionHandler {
	/*
	 * Get the permission by the internal id.
	 */
	public static function getPermission($id) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('Permission');
	}

	/*
	 * Returns the permission with the given value.
	 */
	public static function getPermissionByValue($value) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
																WHERE `value` = \'' . $database->real_escape_string($value) . '\';');

		return $result->fetch_object('Permission');
	}

	/*
	 * Returns a list of all permissions.
	 */
	public static function getPermissions() {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
																ORDER BY `value` ASC;');

		$permissionList = [];

		while ($object = $result->fetch_object('Permission')) {
			$permissionList[] = $object;
		}

		return $permissionList;
	}
}
?>
