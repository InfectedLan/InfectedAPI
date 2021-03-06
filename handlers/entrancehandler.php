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
require_once 'objects/entrance.php';

class EntranceHandler {
	/*
	 * Get an entrance by the internal id.
	 */
	public static function getEntrance(int $id): ?Entrance {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_entrances . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('Entrance');
	}

	/*
	 * Get an entrance by name.
	 */
	public static function getEntranceByName(string $name): ?Entrance {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_entrances . '`
																WHERE `name` = \'' . $database->real_escape_string($name) . '\';');

		return $result->fetch_object('Entrance');
	}

	/*
	 * Get a list of all entrances.
	 */
	public static function getEntrances(): array {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_entrance . '`;');

		$entranceList = [];

		while ($object = $result->fetch_object('Entrance')) {
			$entranceList[] = $object;
		}

		return $entranceList;
	}
}
?>
