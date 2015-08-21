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
require_once 'objects/event.php';

class UserOptionHandler {


	/*
	 * Returns true is the phone number is set to private for the specified user.
	 */
	public static function hasUserPrivatePhone(User $user) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_useroptions . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `privatePhone` = \'1\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Returns true is the phone number is set to private for the specified user.
	 */
	public static function isUserReservedFromNotifications(User $user) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_useroptions . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `reserveFromNotifications` = \'1\';');

		$database->close();

		return $result->num_rows > 0;
	}
}
?>
