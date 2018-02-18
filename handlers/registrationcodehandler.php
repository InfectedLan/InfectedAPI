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
require_once 'databaseconstants.php';
require_once 'database.php';
require_once 'objects/user.php';

class RegistrationCodeHandler {
	/*
	 * Get the registration code by the internal id.
	 */
	public static function getRegistrationCode(User $user): ?string {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `code` FROM `' . DatabaseConstants::db_table_infected_registrationcodes . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

		$row = $result->fetch_array();

		return $row['code'];
	}

	/*
	 * Returns a list of all registration codes.
	 */
	public static function getRegistrationCodes(): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `code` FROM `' . DatabaseConstants::db_table_infected_registrationcodes . '`;');


		$codeList = [];

		while ($row = $result->fetch_array()) {
			$codeList[] = $row['code'];
		}

		return $codeList;
	}

	/*
	 * Returns true if we got the specified code.
	 */
	public static function hasRegistrationCode(string $code): bool {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . DatabaseConstants::db_table_infected_registrationcodes . '`
																WHERE `code` = \'' . $database->real_escape_string($code) . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Returns true if we got a registration code for the specified user.
	 */
	public static function hasRegistrationCodeByUser(User $user): bool {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . DatabaseConstants::db_table_infected_registrationcodes . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Create a registration code for the specified user.
	 */
	public static function createRegistrationCode(User $user): ?string {
		$code = bin2hex(openssl_random_pseudo_bytes(16));

		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_registrationcodes . '` (`userId`, `code`)
										  VALUES (\'' . $user->getId() . '\',
												  		\'' . $code . '\');');

		return $code;
	}

	/*
	 * Remove the specified registration code.
	 */
	public static function removeRegistrationCode(string $code) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_registrationcodes . '`
						  				WHERE `code` = \'' . $database->real_escape_string($code) . '\';');
	}

	/*
	 * Remove registration code for specified user.
	 */
	public static function removeRegistrationCodeByUser(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_registrationcodes . '`
						  				WHERE `userId` = \'' . $user->getId() . '\';');
	}
}
?>
