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
require_once 'objects/avatar.php';
require_once 'objects/user.php';

class AvatarHandler {
	const STATE_NEW = 0;
    const STATE_PENDING = 1;
    const STATE_ACCEPTED = 2;
    const STATE_REJECTED = 3;

	/*
	 * Get an avatar by the internal id.
	 */
	public static function getAvatar(int $id): ?Avatar {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('Avatar');
	}

	/*
	 * Get an avatar for a specified user.
	 */
	public static function getAvatarByUser(User $user): ?Avatar {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '`
								   WHERE `userId` = ' . $user->getId() . ';');

		return $result->fetch_object('Avatar');
	}

	/*
	 * Returns a list of all avatars.
	 */
	public static function getAvatars(): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '`;');

		$avatarList = [];

		while ($object = $result->fetch_object('Avatar')) {
			$avatarList[] = $object;
		}

		return $avatarList;
	}

	/*
	 * Returns a list of all pending avatars.
	 */
	public static function getPendingAvatars(): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '`
								   WHERE `state` = ' . self::STATE_PENDING . ';');

		$avatarList = [];

		while ($object = $result->fetch_object('Avatar')) {
			$avatarList[] = $object;
		}

		return $avatarList;
	}

	/*
	 * Returns true if the specificed user have an avatar.
	 */
	public static function hasAvatar(User $user): bool {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '`
								   WHERE `userId` = ' . $user->getId() . ';');

		return $result->num_rows > 0;
	}

	/*
	 * Returns true if the specificed user have a cropped avatar.
	 */
	public static function hasCroppedAvatar(User $user): bool {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '`
                                   WHERE `userId` = ' . $user->getId() . '
                                   AND (`state` = ' . self::STATE_PENDING . ' OR `state` = ' . self::STATE_ACCEPTED . ');');

		return $result->num_rows > 0;
	}

	/*
	 * Returns true if the specificed user have a valid vatar.
	 */
	public static function hasValidAvatar(User $user):bool {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_avatars . '`
                                   WHERE `userId` = ' . $user->getId() . '
                                   AND `state` = ' . self::STATE_ACCEPTED . ';');

		return $result->num_rows > 0;
	}

	/*
	 * Creates an new avatar.
	 */
	public static function createAvatar(string $fileName, User $user): string {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('INSERT INTO `' . Settings::db_table_infected_crew_avatars . '` (`userId`, `fileName`, `state`)
                                   VALUES (\'' . $user->getId() . '\',
                                           \'' . $fileName . '\',
                                           ' . self::STATE_NEW . ');');

		return Settings::api_path . Settings::api_relative_avatar_path . 'temp/' . $fileName;
	}

	/*
	 * Updates the specified avatar.
	 */
	public static function updateAvatar(Avatar $avatar, int $state, string $fileName) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_avatars . '`
                         SET `state` = ' . $database->real_escape_string($state) . ',
                             `fileName` = \'' . $database->real_escape_string($fileName) . '\'
                         WHERE `id` = ' . $avatar->getId() . ';');
	}

	/*
	 * Deletes an avatar.
	 */
	public static function removeAvatar(Avatar $avatar) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_avatars . '`
						 WHERE `id` = ' . $avatar->getId() . ';');

		// Delete all avatars.
		$avatar->deleteFiles();
	}

	/*
	 * Accept the specificed avatar.
	 */
	public static function acceptAvatar(Avatar $avatar) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_avatars . '`
                         SET `state` = ' . self::STATE_ACCEPTED . '
                         WHERE `id` = ' . $avatar->getId() . ';');
	}

	/*
	 * Reject the specified avatar.
	 */
	public static function rejectAvatar(Avatar $avatar) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_avatars . '`
                         SET `state` = ' . self::STATE_REJECTED . '
                         WHERE `id` = ' . $avatar->getId() . ';');
	}

	/*
	 * Get the default avatar for the specified user.
	 */
	public static function getDefaultAvatar(User $user): string {
		if ($user->getAge() >= 18) {
			if ($user->getGender()) {
				$file = 'default_gutt.png';
			} else {
				$file = 'default_jente.png';
			}
		} else {
			$file = 'default_child.png';
		}

		return Settings::api_relative_avatar_path . 'default/' . $file;
	}
}