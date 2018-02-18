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

class CustomUserTitleHandler {
	/*
	 * Returns is the user has a custom title
	 */
	public static function hasCustomTitle(User $user): bool {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_crew_customusertitles . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Returns the custom title for the user, or nothing
	 */
	public static function getCustomTitle(User $user): string {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `title` FROM `' . DatabaseConstants::db_table_infected_crew_customusertitles . '`
																WHERE `userId` = \'' . $user->getId() . '\';');
		return $result->fetch_row()[0];
	}

	/*
	 * Sets the custom title of a user
	 */
	public static function setCustomTitle(User $user, string $title) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		if(self::hasCustomTitle($user)) {
			$result = $database->query('UPDATE `' . Settings::db_table_infected_customusertitles . '` SET `` = \'' . $database->real_escape_string($title) . '\'
																WHERE `userId` = \'' . $user->getId() . '\';');
		} else {
			$result = $database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_crew_customusertitles . '` (`userId`, `title`) VALUES \'' . $user->getId() . '\', \'' . $database->real_escape_string($title) . '\';');
		}
	}
}
?>
