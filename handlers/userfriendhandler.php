<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2018 Infected <https://infected.no/>.
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

class UserFriendHandler {
	const STATE_PENDING = 0;
	const STATE_ACCEPTED = 1;
	const STATE_REJECTED = 2;

	/*
	 * Returns true is the specified user is friend with the specified friend user.
	 */
	public static function isUserFriendsWith(User $user, User $friend): bool {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_userfriends . '`
								   WHERE ((`fromId` = ' . $user->getId() . ' AND `toId` = ' . $friend->getId() . ')
								   		  OR (`toId` = ' . $user->getId() . ' AND `fromId` = ' . $friend->getId() . '))
								   AND `state` = ' . self::STATE_ACCEPTED . ';');

		return $result->num_rows > 0;
	}

	/*
	 * Get a list of all users that the specified user is friends with.
	 */
	public static function getFriendsByUser(User $user, int $state = self::STATE_ACCEPTED): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
								   WHERE `id` IN (SELECT `toId` FROM `' . Settings::db_table_infected_userfriends . '`
												  WHERE `fromId` = ' . $user->getId() . '
												  AND `state` = ' . $state . '
												  UNION
												  SELECT `fromId` FROM `' . Settings::db_table_infected_userfriends . '`
												  WHERE `toId` = ' . $user->getId() . '
												  AND `state` = ' . $state . ')
							  	   ORDER BY `firstname`, `lastname`;');

		$userList = [];

		while ($object = $result->fetch_object('User')) {
			$userList[] = $object;
		}

		return $userList;
	}

	/*
	 * Adds a friendship with another user.
	 */
	public static function addUserFriend(User $user, User $friend) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('INSERT INTO `' . Settings::db_table_infected_userfriends . '` (`fromId`, `toId`, `datetime`, `state`)
						 VALUES (' . $user->getId() . ',
							     ' . $friend->getId() . ',
								 \'' . date('Y-m-d H:i:s') . '\',
								 ' . self::STATE_PENDING . ');');
	}

	/*
	 * Removes a users friendship.
	 */
	public static function removeUserFriend(User $user, User $friend) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_userfriends . '`
						 WHERE (`fromId` = ' . $user->getId() . ' AND `toId` = ' . $friend->getId() . ')
					     OR (`toId` = ' . $user->getId() . ' AND `fromId` = ' . $friend->getId() . ');');
	}

	/*
	 * Accepts a users friendship with another user.
	 */
	public static function acceptUserFriend(User $user, User $friend) {
		self::updateUserFriend($user, $friend, self::STATE_ACCEPTED);
	}

	/*
	 * Rejects a users friendship with another user.
	 */
	public static function rejectUserFriend(User $user, User $friend) {
		self::updateUserFriend($user, $friend, self::STATE_REJECTED);
	}

	/*
	 * Updates the state of a users friendship.
	 */
	public static function updateUserFriend(User $user, User $friend, int $state) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('UPDATE `' . Settings::db_table_infected_userfriends . '`
						 SET `state` = ' . $state . '
						 WHERE (`fromId` = ' . $user->getId() . ' AND `toId` = ' . $friend->getId() . ')
					     OR (`toId` = ' . $user->getId() . ' AND `fromId` = ' . $friend->getId() . ');');
	}
}