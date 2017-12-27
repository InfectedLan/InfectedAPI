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
	public static function getUser(int $id): ?User {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('User');
	}

	/*
	 * Get user by it's identifier.
	 */
	public static function getUserByIdentifier(string $identifier): ?User {
		$database = Database::getConnection(Settings::db_name_infected);

		$safeIdentifier = $database->real_escape_string($identifier);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE `username` = \'' . $safeIdentifier . '\'
																OR `email` = \'' . $safeIdentifier . '\'
																OR `phone` = \'' . $safeIdentifier . '\';');

		return $result->fetch_object('User');
	}

	/*
	 * Get a list of all users.
	 */
	public static function getUsers(): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																ORDER BY `firstname`, `lastname`;');

		$userList = [];

		while ($object = $result->fetch_object('User')) {
			$userList[] = $object;
		}

		return $userList;
	}

	/*
	 * Returns all users that have one or more permission values in the permissions table.
	 */
	public static function getPermissionUsers(Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT DISTINCT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
																LEFT JOIN `' . Settings::db_table_infected_userpermissions . '` ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_userpermissions . '`.`userId`
																WHERE `' . Settings::db_table_infected_userpermissions . '`.`id` IS NOT NULL
																AND `' . Settings::db_table_infected_userpermissions . '`.`eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																ORDER BY `' . Settings::db_table_infected_users . '`.`firstname`, `' . Settings::db_table_infected_users . '`.`lastname`;');

		$userList = [];

		while ($object = $result->fetch_object('User')) {
			$userList[] = $object;
		}

		return $userList;
	}

	/*
	 * Returns all users that have one or more permission values in the permissions table and is member of the specifed group.
	 */
	public static function getPermissionUsersByGroup(Group $group = null, Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT DISTINCT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
																LEFT JOIN `' . Settings::db_table_infected_userpermissions . '` ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_userpermissions . '`.`userId`
																LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '` ON `users`.`id` = `' . Settings::db_table_infected_crew_memberof . '`.`userId`
																WHERE `' . Settings::db_table_infected_userpermissions . '`.`id` IS NOT NULL
																AND (`' . Settings::db_table_infected_userpermissions . '`.`eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\' OR `' . Settings::db_table_infected_userpermissions . '`.`eventId` = \'0\')
																AND `' . Settings::db_table_infected_crew_memberof . '`.`groupId` ' . ($group != null ? '= \'' . $group->getId() . '\'' : 'IS NULL') . '
																ORDER BY `' . Settings::db_table_infected_users . '`.`firstname`, `' . Settings::db_table_infected_users . '`.`lastname`;');

		$userList = [];

		while ($object = $result->fetch_object('User')) {
			$userList[] = $object;
		}

		return $userList;
	}

	/*
	 * Get a list of all users which is member in a group
	 */
	public static function getMemberUsers(): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
																LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '` ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_crew_memberof . '`.`userId`
																WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
																AND `' . Settings::db_table_infected_crew_memberof . '`.`groupId` IS NOT NULL
																GROUP BY `' . Settings::db_table_infected_users . '`.`id`
																ORDER BY `' . Settings::db_table_infected_users . '`.`firstname`, `' . Settings::db_table_infected_users . '`.`lastname`;');

		$userList = [];

		while ($object = $result->fetch_object('User')) {
			$userList[] = $object;
		}

		return $userList;
	}

	/*
	 * Get a list of all users which is not member in a group
	 */
	public static function getNonMemberUsers(): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
																LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '` ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_crew_memberof . '`.`userId`
																WHERE `' . Settings::db_table_infected_crew_memberof . '`.`eventId` IS NULL
																OR `' . Settings::db_table_infected_crew_memberof . '`.`eventId` != \'' . EventHandler::getCurrentEvent()->getId() . '\'
																GROUP BY `' . Settings::db_table_infected_users . '`.`id`
																ORDER BY `' . Settings::db_table_infected_users . '`.`firstname`, `' . Settings::db_table_infected_users . '`.`lastname`;');

		$userList = [];

		while ($object = $result->fetch_object('User')) {
			$userList[] = $object;
		}

		return $userList;
	}

	/*
	 * Get a list of all users which is a participant of current event.
	 */
	public static function getParticipantUsers(Event $event): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT DISTINCT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
																LEFT JOIN `' . Settings::db_name_infected_tickets . '`.`' . Settings::db_table_infected_tickets_tickets . '` ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_tickets_tickets . '`.`userId`
																WHERE `' . Settings::db_table_infected_tickets_tickets . '`.`eventId` = ' . $event->getId() . '
																AND `' . Settings::db_table_infected_tickets_tickets . '`.`id` IS NOT NULL
																ORDER BY `' . Settings::db_table_infected_users . '`.`firstname`, `' . Settings::db_table_infected_users . '`.`lastname`;');

		$userList = [];

		while ($object = $result->fetch_object('User')) {
			$userList[] = $object;
		}

		return $userList;
	}

	/*
	 * Get a list of all users which was a participant of an event in the given timeperiod.
	 */
	public static function getPreviousParticipantUsers(): array {
		$currentEvent = EventHandler::getCurrentEvent();
		$previousEvent = EventHandler::getEvent($currentEvent->getId() - 3);
		$userList = [];

		// Just checking that we're not out of bounds in this array.
		if (count(EventHandler::getEvents()) >= $previousEvent->getId()) {
  		$database = Database::getConnection(Settings::db_name_infected);

  		$result = $database->query('SELECT DISTINCT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
							  									LEFT JOIN `' . Settings::db_name_infected_tickets . '`.`' . Settings::db_table_infected_tickets_tickets . '` ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_tickets_tickets . '`.`userId`
							  									WHERE `' . Settings::db_table_infected_tickets_tickets . '`.`eventId` >= ' . $previousEvent->getId() . '
							  									AND `' . Settings::db_table_infected_tickets_tickets . '`.`eventId` <= ' . $currentEvent->getId() . '
							  									ORDER BY `' . Settings::db_table_infected_users . '`.`firstname`, `' . Settings::db_table_infected_users . '`.`lastname`;');

			while ($object = $result->fetch_object('User')) {
				$userList[] = $object;
			}
		}

		return $userList;
	}

	/*
	 * Check if a user with given username or email already exists.
	 */
	public static function hasUser(string $identifier): bool {
		$database = Database::getConnection(Settings::db_name_infected);

		$safeIdentifier = $database->real_escape_string($identifier);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_users . '`
																WHERE `username` = \'' . $safeIdentifier . '\'
																OR `email` = \'' . $safeIdentifier . '\'
																OR `phone` = \'' . $safeIdentifier . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Create a new user
	 * TEST FIXERS NOTE: Gender can't be boolean. It will give an mysql error, as mysql expects an integer.
	 */
	public static function createUser(string $firstname, string $lastname, string $username, string $password, string $email, string $birthDate, $gender, int $phone, string $address, int $postalCode, string $nickname): User {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('INSERT INTO `' . Settings::db_table_infected_users . '` (`firstname`, `lastname`, `username`, `password`, `email`, `birthdate`, `gender`, `phone`, `address`, `postalcode`, `countryId`, `nickname`, `registereddate`)
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
															\'165\',
														  \'' . $database->real_escape_string($nickname) . '\',
														  \'' . date('Y-m-d H:i:s') . '\');');

		return self::getUser($database->insert_id);
	}

	/*
	 * Update a user
	 */
	public static function updateUser(User $user, string $firstname, string $lastname, string $username, string $email, string $birthDate, bool $gender, int $phone, string $address, int $postalCode, string $nickname) {
		$database = Database::getConnection(Settings::db_name_infected);

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
	}

	/*
	 * Remove a user.
	 */
	public static function removeUser(User $user) {
		// Only remove users without a ticket, for now...
		if (!TicketHandler::hasUserAnyTicket($user)) {
			$database = Database::getConnection(Settings::db_name_infected);

			$database->query('DELETE FROM `' . Settings::db_table_infected_users . '`
							  				WHERE `id` = \'' . $user->getId() . '\';');

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
	public static function updateUserPassword(User $user, string $password) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('UPDATE `' . Settings::db_table_infected_users . '`
										  SET `password` = \'' . $database->real_escape_string($password) . '\'
										  WHERE `id` = \'' . $user->getId() . '\';');
	}

	/*
	 * Lookup users by set values and return a list of users as result.
	 */
	public static function search(string $query): array {
		// Sanitize the input and split the query string into an array.
		$queryList = explode(' ', $query);
		$keywordList = [];

		// Build the word list, and add "+" and "*" to the start and end of every word.
		foreach ($queryList as $keyword) {
			// This is to prevent crashes caused by the word starting or ending with "@".
			$sanitizedKeyword = preg_replace('/[^\p{L}\p{N}_]+/u', ' ', $keyword);

			// Wrapping the sanitized keyword with SQL match perameters.
			$keywordList[] = '+' . $sanitizedKeyword . '*';
		}

		$database = Database::getConnection(Settings::db_name_infected);

		// Query the database using a "full-text" search.
		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE MATCH (`firstname`, `lastname`, `username`, `nickname`)
																AGAINST (\'' . $database->real_escape_string(implode(' ', $keywordList)) . '\' IN BOOLEAN MODE)
																OR `email` = \'' . $database->real_escape_string($queryList[0]) . '\'
																OR `phone` = \'' . $database->real_escape_string($queryList[0]) . '\'
																LIMIT 15;');

		$userList = [];

		while ($object = $result->fetch_object('User')) {
			$userList[] = $object;
		}

		return $userList;
	}
	/*
	 * Returns the steam id of a user, or null if undefined
	 */
	public static function getSteamId(User $user): ?string {
    $database = Database::getConnection(Settings::db_name_infected_compo);

    $result = $database->query('SELECT `steamId` FROM `' . Settings::db_table_infected_compo_steamids . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

    return $result->fetch_array()[0];
	}

	/*
	 * Sets the steam id
	 */
	public static function setSteamId(User $user, string $steamId) {
    $database = Database::getConnection(Settings::db_name_infected_compo);

    $result = $database->query('SELECT `steamId` FROM `' . Settings::db_table_infected_compo_steamids . '`
																WHERE `userId` = \'' . $user->getId() . '\';');
    $count = $result->num_rows;

    if ($count == 0) {
			$database->query('INSERT INTO `' . Settings::db_table_infected_compo_steamids . '`(`userId`, `steamId`)
												VALUES (\'' . $user->getId() . '\',
																\'' . $database->real_escape_string($steamId) . '\');');
    } else {
			$database->query('UPDATE `' . Settings::db_table_infected_compo_steamids . '`
												SET `steamId` = \'' . $database->real_escape_string($steamId) . '\'
												WHERE `userId` = \'' . $user->getId() . '\';');
    }
	}
}
?>
