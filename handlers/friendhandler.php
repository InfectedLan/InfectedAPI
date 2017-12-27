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
require_once 'handlers/eventhandler.php';
require_once 'objects/user.php';

class FriendHandler {
	/*
	 * Returns true is the specified user is friend with the specified friend user.
	 */
	public static function isUserFriendsWith(User $user, User $friend): bool {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_friends . '`
																WHERE (`userId` = \'' . $user->getId() . '\' AND `friendId` = \'' . $friend->getId() . '\')
																OR (`friendId` = \'' . $friend->getId() . '\' AND `userId` = \'' . $user->getId() . '\');');

		return $result->num_rows > 0;
	}

	/*
	 * Get a list of all users that the specified user is friends with.
	 */
	public static function getFriendsByUser(User $user): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE `id` IN (SELECT `friendId` FROM `' . Settings::db_table_infected_userfriends . '`
																					     WHERE `userId` = \'' . $user->getId() . '\')
															  ORDER BY `firstname`, `lastname`;');

		$userList = [];

		while ($object = $result->fetch_object('User')) {
			$userList[] = $object;
		}

		return $userList;
	}

	/*
	 * Create a new agenda entry.
	 */
	public static function addUserFriend(User $user, User $friend) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('INSERT INTO `' . Settings::db_table_infected_friends . '` (`userId`, `friendId`, `datetime`)
										  VALUES (\'' . $user->getId() . '\',
														  \'' . $friend->getId() . '\',
														  \'' . date('Y-m-d H:i:s') . '\');');
	}

	/*
	 * Remove an agenda entry.
	 */
	public static function removeUserFriend(User $user, User $friend) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_friends . '`
						  				WHERE `userId` = \'' . $user->getId() . '\'
											AND `friendId` = \'' . $friend->getId() . '\';');
	}
}
?>
