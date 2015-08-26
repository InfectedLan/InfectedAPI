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
require_once 'handlers/eventhandler.php';
require_once 'objects/user.php';

class FriendHandler {
	/*
	 * Returns true is the specified user is friend with the specified friend user.
	 */
	public static function isUserFriendsWith(User $user, User $friend) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_friends . '`
																WHERE (`userId` = \'' . $user->getId() . '\' AND `friendId` = \'' . $friend->getId() . '\')
																OR (`friendId` = \'' . $friend->getId() . '\' AND `userId` = \'' . $user->getId() . '\');');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Create a new agenda entry.
	 */
	public static function addFriend(User $user, User $friend) {
		$database = Database::open(Settings::db_name_infected);

		$database->query('INSERT INTO `' . Settings::db_table_infected_friends . '` (`userId`, `friendId`, `datetime`)
										  VALUES (\'' . $user->getId() . '\',
														  \'' . $friend->getId() . '\',
														  \'' . date('Y-m-d H:i:s') . '\');');

		$database->close();
	}

	/*
	 * Remove an agenda entry.
	 */
	public static function removeFriend(User $user, User $friend) {
		$database = Database::open(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_friends . '`
						  				WHERE `userId` = \'' . $user->getId() . '\'
											AND `friendId` = \'' . $friend->getId() . '\';');

		$database->close();
	}
}
?>
