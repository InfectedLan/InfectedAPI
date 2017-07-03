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

class UserOptionHandler {
	/*
	 * Returns true if this user has a option.
	 */
	public static function hasUserOption(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_useroptions . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Returns true is the phone number is set to private for the specified user.
	 */
	public static function hasUserPrivatePhone(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_useroptions . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `privatePhone` = \'1\';');

		return $result->num_rows > 0;
	}

	/*
	 * Returns true is the phone number is set to private for the specified user.
	 */
	public static function isUserReservedFromNotifications(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_useroptions . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `reserveFromNotifications` = \'1\';');

		return $result->num_rows > 0;
	}

	/*
	 * Returns true is the user is set to go on swimming.
	 */
	public static function isUserSwimming(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_useroptions . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `swimming` = \'1\';');

		return $result->num_rows > 0;
	}

	/*
	 * Set a users note.
	 */
	public static function setUserSwimming(User $user, $swimming) {
		$database = Database::getConnection(Settings::db_name_infected);

		if (!self::hasUserOption($user)) {
			$database->query('INSERT INTO `' . Settings::db_table_infected_useroptions . '` (`userId`, `swimming`)
												VALUES (\'' . $user->getId() . '\',
																\'' . $database->real_escape_string($swimming) . '\');');
		} else {
			$database->query('UPDATE `' . Settings::db_table_infected_useroptions . '`
												SET `swimming` = \'' . $database->real_escape_string($swimming) . '\'
												WHERE `userId` = \'' . $user->getId() . '\';');
		}


	/*
	 * Returns true if the user has the prank option set.
	 */
	public static function hasUserEasterEgg(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_useroptions . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `easterEgg` = \'1\';');

		return $result->num_rows > 0;
	}
}
?>
