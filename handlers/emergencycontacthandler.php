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
require_once 'objects/emergencycontact.php';
require_once 'objects/user.php';

class EmergencyContactHandler {
	/*
	 * Get an emergenctcontacts by the internal id.
	 */
	public static function getEmergencyContact($id) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_emergencycontacts . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('EmergencyContact');
	}

	/*
	 * Get the emergency contact for the given user.
	 */
	public static function getEmergencyContactByUser(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_emergencycontacts . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

		return $result->fetch_object('EmergencyContact');
	}

	/*
	 * Returns a list of all emergency contacts.
	 */
	public static function getEmergencyContacts() {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_emergencycontacts . '`;');

		$emergencyContactsList = [];

		while ($object = $result->fetch_object('EmergencyContact')) {
			$emergenctContactList[] = $object;
		}

		return $emergencyContactsList;
	}

	/*
	 * Returns true if the specified user has an emergency contact.
	 */
	public static function hasEmergencyContactByUser(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `'. Settings::db_table_infected_emergencycontacts . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Create a new emergency contact.
	 */
	public static function createEmergencyContact(User $user, $phone) {
		if (!self::hasEmergencyContactByUser($user)) {
			$database = Database::getConnection(Settings::db_name_infected);

			$database->query('INSERT INTO `' . Settings::db_table_infected_emergencycontacts . '` (`userId`, `phone`)
											  VALUES (\'' . $user->getId() . '\',
													  		\'' . $database->real_escape_string($phone) . '\');');
		} else {
			if (!empty($phone) && $phone != 0) {
				self::updateEmergencyContact($user, $phone);
			} else {
				self::removeEmergencyContact($user);
			}
		}
	}

	/*
	 * Update information about a emergency contact.
	 */
	public static function updateEmergencyContact(User $user, $phone) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('UPDATE `' . Settings::db_table_infected_emergencycontacts . '`
										  SET `phone` = \'' . $database->real_escape_string($phone) . '\'
										  WHERE `userId` = \'' . $user->getId() . '\';');
	}

	/*
	 * Remove a emergency contact.
	 */
	public static function removeEmergencyContact(User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_emergencycontacts . '`
						  				WHERE `userId` = \'' . $user->getId() . '\';');
	}
}
?>
