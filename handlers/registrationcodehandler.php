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
require_once 'objects/user.php';

class RegistrationCodeHandler {
	/*
	 * Get the registration code by the internal id.
	 */
	public static function getRegistrationCode(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `code` FROM `' . Settings::db_table_infected_registrationcodes . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

		$row = $result->fetch_array();

		$database->close();

		return $row['code'];
	}

	/*
	 * Returns a list of all registration codes.
	 */
	public static function getRegistrationCodes() {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `code` FROM `' . Settings::db_table_infected_registrationcodes . '`;');

		$database->close();

		$codeList = [];

		while ($row = $result->fetch_array()) {
			$codeList[] = $row['code'];
		}

		return $codeList;
	}

	/*
	 * Returns true if we got the specified code.
	 */
	public static function hasRegistrationCode($code) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_registrationcodes . '`
																WHERE `code` = \'' . $database->real_escape_string($code) . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Returns true if we got a registration code for the specified user.
	 */
	public static function hasRegistrationCodeByUser(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_registrationcodes . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Create a registration code for the specified user.
	 */
	public static function createRegistrationCode(User $user) {
		$code = bin2hex(openssl_random_pseudo_bytes(16));

		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('INSERT INTO `' . Settings::db_table_infected_registrationcodes . '` (`userId`, `code`)
										  VALUES (\'' . $user->getId() . '\',
												  		\'' . $code . '\');');

		$database->close();

		return $code;
	}

	/*
	 * Remove the specified registration code.
	 */
	public static function removeRegistrationCode($code) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_registrationcodes . '`
						  				WHERE `code` = \'' . $database->real_escape_string($code) . '\';');

		$database->close();
	}

	/*
	 * Remove registration code for specified user.
	 */
	public static function removeRegistrationCodeByUser(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_registrationcodes . '`
						  				WHERE `userId` = \'' . $user->getId() . '\';');

		$database->close();
	}
}
?>
