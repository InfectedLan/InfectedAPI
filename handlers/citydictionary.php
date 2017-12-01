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

class CityDictionary {
	/*
	 * Returns the city from given postalcode.
	 */
	public static function getCity(int $postalCode): string {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `city` FROM `' . Settings::db_table_infected_postalcodes . '`
																WHERE `code` = \'' . $database->real_escape_string($postalCode) . '\';');

		$row = $result->fetch_array();

		return ucfirst(strtolower($row['city']));
	}

	/*
	 * Return true if the specified postal code exists.
	 */
	public static function hasPostalCode(int $postalCode): bool {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_postalcodes . '`
																WHERE `code` = \'' . $database->real_escape_string($postalCode) . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Returns the postalcode for given city.
	 */
	public static function getPostalCode(string $city): int {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `code` FROM `' . Settings::db_table_infected_postalcodes . '`
																WHERE `city` = \'' . $database->real_escape_string($city) . '\';');

		$row = $result->fetch_array();

		return $row['code'];
	}
}
?>
