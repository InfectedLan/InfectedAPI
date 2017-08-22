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

class UserNoteHandler {
	/*
	 * Get a user note by the internal id.
	 */
	public static function getUserNote($id) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_usernotes . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		$database->close();

		$row = $result->fetch_array();

		return $row['content'];
	}

	/*
	 * Returns true if this user has a note.
	 */
	public static function hasUserNoteByUser(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_usernotes . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Get a users note by user.
	 */
	public static function getUserNoteByUser(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_usernotes . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

		$database->close();

		$row = $result->fetch_array();

		return $row['content'];
	}

	/*
	 * Set a users note.
	 */
	public static function setUserNote(User $user, $content) {
		$database = Database::getConnection(Settings::db_name_infected);

		if (!empty($content)) {
			if (!self::hasUserNoteByUser($user)) {
				self::createUserNote($user, $content);
			} else {
				self::updateUserNote($user, $content);
			}
		} else {
			self::removeUserNote($user);
		}

		$database->close();
	}

	/*
	 * Create a note for the the given user.
	 */
	public static function createUserNote(User $user, $content) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('INSERT INTO `' . Settings::db_table_infected_usernotes . '` (`userId`, `content`)
											VALUES (\'' . $user->getId() . '\',
															\'' . $database->real_escape_string($content) . '\');');

		$database->close();
	}

	/*
	 * Updates a users note.
	 */
	public static function updateUserNote(User $user, $content) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('UPDATE `' . Settings::db_table_infected_usernotes . '`
											SET `content` = \'' . $database->real_escape_string($content) . '\'
											WHERE `userId` = \'' . $user->getId() . '\';');

		$database->close();
	}

	/*
	 * Remove a users note.
	 */
	public static function removeUserNote(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_usernotes . '`
						  				WHERE `userId` = \'' . $user->getId() . '\';');

		$database->close();
	}
}
?>
