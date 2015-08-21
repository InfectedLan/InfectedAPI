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
require_once 'handlers/tickethandler.php';
require_once 'handlers/emergencycontacthandler.php';
require_once 'handlers/passwordresetcodehandler.php';
require_once 'handlers/registrationcodehandler.php';
require_once 'handlers/userpermissionhandler.php';
require_once 'handlers/applicationhandler.php';
require_once 'handlers/avatarhandler.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/user.php';
require_once 'objects/event.php';

class UserHandler {
	/*
	 * Get an user by the internal id.
	 */
	public static function getUser($id) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		$database->close();

		return $result->fetch_object('User');
	}

	/*
	 * Get user by it's identifier.
	 */
	public static function getUserByIdentifier($identifier) {
		$database = Database::open(Settings::db_name_infected);

		$safeIdentifier = $database->real_escape_string($identifier);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE `username` = \'' . $safeIdentifier . '\'
																OR `email` = \'' . $safeIdentifier . '\'
																OR `phone` = \'' . $safeIdentifier . '\';');

		$database->close();

		return $result->fetch_object('User');
	}

	/*
	 * Get a list of all users.
	 */
	public static function getUsers() {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																ORDER BY `firstname` ASC;');

		$database->close();

		$userList = array();

		while ($object = $result->fetch_object('User')) {
			array_push($userList, $object);
		}

		return $userList;
	}

	/*
	 * Returns all users that have one or more permission values in the permissions table.
	 */
	public static function getPermissionUsersByEvent(Event $event) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT DISTINCT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
																LEFT JOIN `' . Settings::db_table_infected_userpermissions . '` ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_userpermissions . '`.`userId`
																WHERE `' . Settings::db_table_infected_userpermissions . '`.`id` IS NOT NULL
																AND `' . Settings::db_table_infected_userpermissions . '`.`eventId` = \'' . $event->getId() . '\'
																ORDER BY `' . Settings::db_table_infected_users . '`.`firstname` ASC;');

		$database->close();

		$userList = array();

		while ($object = $result->fetch_object('User')) {
			array_push($userList, $object);
		}

		return $userList;
	}

	/*
	 * Returns all users that have one or more permission values in the permissions table.
	 */
	public static function getPermissionUsers() {
		return self::getPermissionUsersByEvent(EventHandler::getCurrentEvent());
	}

	/*
	 * Returns all users that have one or more permission values in the permissions table and is member of the specifed group.
	 */
	public static function getPermissionUsersByGroupAndEvent(Group $group = null, Event $event) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT DISTINCT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
																LEFT JOIN `' . Settings::db_table_infected_userpermissions . '` ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_userpermissions . '`.`userId`
																LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '` ON `users`.`id` = `' . Settings::db_table_infected_crew_memberof . '`.`userId`
																WHERE `' . Settings::db_table_infected_userpermissions . '`.`id` IS NOT NULL
																AND (`' . Settings::db_table_infected_userpermissions . '`.`eventId` = \'' . $event->getId() . '\' OR `' . Settings::db_table_infected_userpermissions . '`.`eventId` = \'0\')
																AND `' . Settings::db_table_infected_crew_memberof . '`.`groupId` ' . ($group != null ? '= \'' . $group->getId() . '\'' : 'IS NULL') . '
																ORDER BY `' . Settings::db_table_infected_users . '`.`firstname` ASC;');

		$database->close();

		$userList = array();

		while ($object = $result->fetch_object('User')) {
			array_push($userList, $object);
		}

		return $userList;
	}

	/*
	 * Returns all users that have one or more permission values in the permissions table and is member of the specifed group.
	 */
	public static function getPermissionUsersByGroup(Group $group = null) {
		return self::getPermissionUsersByGroupAndEvent($group, EventHandler::getCurrentEvent());
	}

	/*
	 * Get a list of all users which is member in a group
	 */
	public static function getMemberUsers() {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
																LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '` ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_crew_memberof . '`.`userId`
																WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
																AND `' . Settings::db_table_infected_crew_memberof . '`.`groupId` IS NOT NULL
																ORDER BY `' . Settings::db_table_infected_users . '`.`firstname` ASC;');

		$database->close();

		$userList = array();

		while ($object = $result->fetch_object('User')) {
			array_push($userList, $object);
		}

		return $userList;
	}

	/*
	 * Get a list of all users which is not member in a group
	 */
	public static function getNonMemberUsers() {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
																LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '` ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_crew_memberof . '`.`userId`
																WHERE `' . Settings::db_table_infected_crew_memberof . '`.`eventId` IS NULL
																OR `' . Settings::db_table_infected_crew_memberof . '`.`eventId` != \'' . EventHandler::getCurrentEvent()->getId() . '\'
																ORDER BY `' . Settings::db_table_infected_users . '`.`firstname` ASC;');

		$database->close();

		$userList = array();

		while ($object = $result->fetch_object('User')) {
			array_push($userList, $object);
		}

		return $userList;
	}

	/*
	 * Get a list of all users which is a participant of current event.
	 */
	public static function getParticipantUsers(Event $event) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT DISTINCT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
																LEFT JOIN `' . Settings::db_name_infected_tickets . '`.`' . Settings::db_table_infected_tickets_tickets . '` ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_tickets_tickets . '`.`userId`
																WHERE `' . Settings::db_table_infected_tickets_tickets . '`.`eventId` = ' . $event->getId() . '
																AND `' . Settings::db_table_infected_tickets_tickets . '`.`id` IS NOT NULL
																ORDER BY `' . Settings::db_table_infected_users . '`.`firstname` ASC;');

		$database->close();

		$userList = array();

		while ($object = $result->fetch_object('User')) {
			array_push($userList, $object);
		}

		return $userList;
	}

	/*
	 * Get a list of all users which was a participant of an event in the given timeperiod.
	 */
	public static function getPreviousParticipantUsers() {
		$currentEvent = EventHandler::getCurrentEvent();
		$previousEvent = EventHandler::getEvent($currentEvent->getId() - 3);
		$userList = array();

		// Just checking that we're not out of bounds in this array.
		if (count(EventHandler::getEvents()) >= $previousEvent->getId()) {
  			$database = Database::open(Settings::db_name_infected);

  			$result = $database->query('SELECT DISTINCT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
							  										LEFT JOIN `' . Settings::db_name_infected_tickets . '`.`' . Settings::db_table_infected_tickets_tickets . '` ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_tickets_tickets . '`.`userId`
							  										WHERE `' . Settings::db_table_infected_tickets_tickets . '`.`eventId` >= ' . $previousEvent->getId() . '
							  										AND `' . Settings::db_table_infected_tickets_tickets . '`.`eventId` <= ' . $currentEvent->getId() . '
							  										ORDER BY `' . Settings::db_table_infected_users . '`.`firstname` ASC;');

  			$database->close();

			while ($object = $result->fetch_object('User')) {
				array_push($userList, $object);
			}
		}

		return $userList;
	}

	/*
	 * Check if a user with given username or email already exists.
	 */
	public static function hasUser($identifier) {
		$database = Database::open(Settings::db_name_infected);

		$safeIdentifier = $database->real_escape_string($identifier);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_users . '`
																WHERE `username` = \'' . $safeIdentifier . '\'
																OR `email` = \'' . $safeIdentifier . '\'
																OR `phone` = \'' . $safeIdentifier . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Create a new user
	 */
	public static function createUser($firstname, $lastname, $username, $password, $email, $birthDate, $gender, $phone, $address, $postalCode, $nickname) {
		$database = Database::open(Settings::db_name_infected);

		$database->query('INSERT INTO `' . Settings::db_table_infected_users . '` (`firstname`, `lastname`, `username`, `password`, `email`, `birthdate`, `gender`, `phone`, `address`, `postalcode`, `nickname`, `registereddate`)
										  VALUES (\'' . $database->real_escape_string($firstname) . '\',
														  \'' . $database->real_escape_string($lastname) . '\',
														  \'' . $database->real_escape_string($username) . '\',
														  \'' . $database->real_escape_string($password) . '\',
														  \'' . $database->real_escape_string($email) . '\',
														  \'' . $database->real_escape_string($birthDate) . '\',
														  \'' . $database->real_escape_string($gender) . '\',
														  \'' . $database->real_escape_string($phone) . '\',
														  \'' . $database->real_escape_string($address) . '\',
														  \'' . $database->real_escape_string($postalCode) . '\',
														  \'' . $database->real_escape_string($nickname) . '\',
														  \'' . date('Y-m-d H:i:s') . '\');');

		$user = self::getUser($database->insert_id);

		$database->close();

		return $user;
	}

	/*
	 * Update a user
	 */
	public static function updateUser(User $user, $firstname, $lastname, $username, $email, $birthDate, $gender, $phone, $address, $postalCode, $nickname) {
		$database = Database::open(Settings::db_name_infected);

		$database->query('UPDATE `' . Settings::db_table_infected_users . '`
										  SET `firstname` = \'' . $database->real_escape_string($firstname) . '\',
												  `lastname` = \'' . $database->real_escape_string($lastname) . '\',
												  `username` = \'' . $database->real_escape_string($username) . '\',
												  `email` = \'' . $database->real_escape_string($email) . '\',
												  `birthdate` = \'' . $database->real_escape_string($birthDate) . '\',
												  `gender` = \'' . $database->real_escape_string($gender) . '\',
												  `phone` = \'' . $database->real_escape_string($phone) . '\',
												  `address` = \'' . $database->real_escape_string($address) . '\',
												  `postalcode` = \'' . $database->real_escape_string($postalCode) . '\',
												  `nickname` = \'' . $database->real_escape_string($nickname) . '\'
										  WHERE `id` = \'' . $user->getId() . '\';');

		$database->close();
	}

	/*
	 * Remove a user.
	 */
	public static function removeUser(User $user) {
		// Only remove users without a ticket, for now...
		if (!TicketHandler::hasUserAnyTicket($user)) {
			$database = Database::open(Settings::db_name_infected);

			$database->query('DELETE FROM `' . Settings::db_table_infected_users . '`
							  				WHERE `id` = \'' . $user->getId() . '\';');

			$database->close();

			// Remove users emergencycontact.
			if (EmergencyContactHandler::hasEmergencyContactByUser($user)) {
				EmergencyContactHandler::removeEmergenctContact($user);
			}

			// Remove users passwordresetcode.
			if (PasswordResetCodeHandler::hasPasswordResetCodeByUser($user)) {
				PasswordResetCodeHandler::removeUserPasswordResetCode($user);
			}

			// Remove users registrationscode.
			if (RegistrationCodeHandler::hasRegistrationCodeByUser($user)) {
				RegistrationCodeHandler::removeUserRegistrationCode($user);
			}

			// Remove users permissions.
			if (UserPermissionsHandler::hasUserPermissions($user)) {
				UserPermissionsHandler::removeUserPermissions($user);
			}

			// Remove users application.
			if (ApplicationHandler::hasApplication($user)) {
				ApplicationHandler::removeUserApplication($user);
			}

			// Remove users avatar.
			if (AvatarHandler::hasAvatar($user)) {
				AvatarHandler::deleteAvatar($user->getAvatar());
			}

			// Remove users memberof entry.
			if (GroupHandler::isGroupMember($user)) {
				GroupHandler::removeUserFromGroup($user);
			}
		}
	}

	/*
	 * Update a users password
	 */
	public static function updateUserPassword(User $user, $password) {
		$database = Database::open(Settings::db_name_infected);

		$database->query('UPDATE `' . Settings::db_table_infected_users . '`
										  SET `password` = \'' . $database->real_escape_string($password) . '\'
										  WHERE `id` = \'' . $user->getId() . '\';');

		$database->close();
	}

	/*
	 * Lookup users by set values and return a list of users as result.
	 */
	public static function search($query) {
		$database = Database::open(Settings::db_name_infected);

		// Sanitize the input and split the query string into an array.
		$queryList = explode(' ', $query);
		$wordList = array();

		// Build the word list, and add "+" and "*" to the start and end of every word.
		foreach ($queryList as $value) {
		  array_push($wordList, '+' . $value . '*');
		}

		// Query the database using a Full-Text Search.
		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE MATCH (`firstname`, `lastname`, `username`, `email`, `nickname`)
																AGAINST (\'' . $database->real_escape_string(implode(' ', $wordList)) . '\' IN BOOLEAN MODE)
																LIMIT 15;');

		$database->close();

		$userList = array();

		while ($object = $result->fetch_object('User')) {
			array_push($userList, $object);
		}

		return $userList;
	}

	/*
	 * Returns true is the phone number is set to private for the specified user.
	 */
	public static function hasPrivatePhone(User $user) {
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
	public static function isReservedFromNotifications(User $user) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_useroptions . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `reserveFromNotifications` = \'1\';');

		$database->close();

		return $result->num_rows > 0;
	}
}
?>
