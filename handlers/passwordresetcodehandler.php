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
require_once 'objects/user.php';

class PasswordResetCodeHandler {
	/*
	 * Get the password reset code by the internal id.
	 */
	public static function getPasswordResetCode(int $id): ?string {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `code` FROM `' . Settings::db_table_infected_passwordresetcodes . '`
								  WHERE `id` = ' . $database->real_escape_string($id) . ';');

		$row = $result->fetch_array();

		return $row['code'];
	}

	/*
	 * Returns a list of all password reset codes.
	 */
	public static function getPasswordResetCodes(): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `code` FROM `' . Settings::db_table_infected_passwordresetcodes . '`;');

		$codeList = [];

		while ($row = $result->fetch_array()) {
			$codeList[] = $row['code'];
		}

		return $codeList;
	}

	/*
	 * Returns true if we've got the specified code.
	 */
	public static function hasPasswordResetCode(string $code): bool {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_passwordresetcodes . '`
								   WHERE `code` = \'' . $database->real_escape_string($code) . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Returns true if we've got a code for the specified user.
	 */
	public static function hasPasswordResetCodeByUser(User $user): bool {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_passwordresetcodes . '`
								   WHERE `userId` = ' . $user->getId() . ';');

		return $result->num_rows > 0;
	}

	/*
	 * Create a new password reset code for the specified user.
	 */
	public static function createPasswordResetCode(User $user): ?string {
		$code = bin2hex(openssl_random_pseudo_bytes(16));

		$database = Database::getConnection(Settings::db_name_infected);

		if (!self::hasPasswordResetCodeByUser($user)) {
			$database->query('INSERT INTO `' . Settings::db_table_infected_passwordresetcodes . '` (`userId`, `code`)
							 VALUES (' . $user->getId() . ',
							 		 \'' . $database->real_escape_string($code) . '\');');
		} else {
			$database->query('UPDATE `' . Settings::db_table_infected_passwordresetcodes . '`
							 SET `code` = \'' . $database->real_escape_string($code) . '\'
							 WHERE `userId` = ' . $user->getId() . ';');
		}

		return $code;
	}

	/*
	 * Remove the specified password reset code.
	 */
	public static function removePasswordResetCode(string $code) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_passwordresetcodes . '`
						 WHERE `code` = \'' . $database->real_escape_string($code) . '\';');
	}

	/*
	 * Remove the password reset code for the specified user.
	 */
	public static function removePasswordResetCodeByUser(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_passwordresetcodes . '`
						 WHERE `userId` = ' . $user->getId() . ';');
	}

	/*
	 * Returns the user with the specified password reset code.
	 */
	public static function getUserFromPasswordResetCode($code): ?User {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
								   WHERE `id` = (SELECT `userId` FROM `' . Settings::db_table_infected_passwordresetcodes . '`
												 WHERE `code` = \'' . $database->real_escape_string($code) . '\'
												 LIMIT 1);');

		return $result->fetch_object('User');
	}
}