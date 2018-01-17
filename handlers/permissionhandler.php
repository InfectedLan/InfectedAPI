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
require_once 'objects/permission.php';

class PermissionHandler {
	/*
	 * Get the permission by the internal id.
	 */
	public static function getPermission(int $id): ?Permission {
		$json = json_decode(file_get_contents(Settings::file_json_permissions));

		foreach ($json as $key => $data) {
			if ($data->id = $id) {
				return new Permission($data->id, $data->value, $data->description);
			}
		}

		return null;
	}

	/*
	 * Returns the permission with the given value.
	 */
	public static function getPermissionByValue(string $value): ?Permission {
		$json = json_decode(file_get_contents(Settings::file_json_permissions));

		foreach ($json as $key => $data) {
			if ($data->value == $value) {
				return new Permission($data->id, $data->value, $data->description);
			}
		}

		return null;
	}

	/*
	 * Returns a list of all permissions.
	 */
	public static function getPermissions(): array {
		$json = json_decode(file_get_contents(Settings::file_json_permissions));
		$permissionList = [];

		foreach ($json as $key => $data) {
			$permissionList[] = new Permission($data->id, $data->value, $data->description);
		}

		return $permissionList;
	}

	/*
	 * Returns a list of all permissions.
	 */
	public static function getPermissionsByValues(array $values): array {
		$json = json_decode(file_get_contents(Settings::file_json_permissions));
		$permissionList = [];

		foreach ($json as $key => $data) {
			if (in_array($data->id, $values)) {
				$permissionList[] = new Permission($data->id, $data->value, $data->description);
			}
		}

		return $permissionList;
	}
}