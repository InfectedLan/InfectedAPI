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
require_once 'objects/event.php';
require_once 'objects/group.php';
require_once 'objects/user.php';

class GroupHandler {
	/*
	 * Get a group by the internal id.
	 */
	public static function getGroup($id) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');


		return $result->fetch_object('Group');
	}

	/*
	 * Get a group for the specified user.
	 */
	public static function getGroupByUser(User $user, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '`
																WHERE `id` = (SELECT `groupId` FROM `' . Settings::db_table_infected_crew_memberof . '`
																						  WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																						  AND `userId` = \'' . $user->getId() . '\'
																						  LIMIT 1);');


		return $result->fetch_object('Group');
	}

	/*
	 * Get a list of all active groups. // TODO: Check what happens if previous history MUST HAVE the group for a specific event?
	 */
	/**
	public static function getGroupsByEvent(Event $event) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																ORDER BY `id`, `name`;');

		$groupList = [];

		while ($object = $result->fetch_object('Group')) {
			$groupList[] = $object;
		}

		return $groupList;
	}
	*/

	/*
	 * Get a list of all active groups.
	 */
	public static function getGroups() {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '`
																WHERE `active` = \'1\'
																ORDER BY `id`, `name`;');

		$groupList = [];

		while ($object = $result->fetch_object('Group')) {
			$groupList[] = $object;
		}

		return $groupList;
	}

	/*
	 * Create a new group.
	 */
	public static function createGroup($name, $title, $description, User $leaderUser = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('INSERT INTO `' . Settings::db_table_infected_crew_groups . '` (`name`, `title`, `description`, `leaderId`, `active`)
										  VALUES (\'' . $database->real_escape_string($name) . '\',
														  \'' . $database->real_escape_string($title) . '\',
														  \'' . $database->real_escape_string($description) . '\',
														  \'' . ($leaderUser != null ? $leaderUser->getId() : 0) . '\',
															\'' . ($coleaderUser != null ? $coleaderUser->getId() : 0) . '\'
															\'1\');');

		$group = self::getGroup($database->insert_id);

		if ($leaderUser != null) {
			self::setGroupLeader($leaderUser, $group);
		}

		return $group;
	}

	/*
	 * Update the specified group, with the specified parameters.
	 */
	public static function updateGroup(Group $group, $name, $title, $description, User $leaderUser = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_groups . '`
			  						  SET `name` = \'' . $database->real_escape_string($name) . '\',
				  								`title` = \'' . $database->real_escape_string($title) . '\',
				  								`description` = \'' . $database->real_escape_string($description) . '\'
			  						  WHERE `id` = \'' . $group->getId() . '\';');

		self::setGroupLeader($leaderUser, $group);
	}

	/*
	 * Remove the specified group
	 */
	public static function removeGroup(Group $group) {
		/**
		self::removeUsersFromGroup($group);
		TeamHandler::removeTeamsByGroup($group);
		*/

		$database = Database::getConnection(Settings::db_name_infected_crew);

		/**
		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_groups . '`
						  				WHERE `id` = \'' . $group->getId() . '\';');
		*/

		$database->query('UPDATE `' . Settings::db_table_infected_crew_groups . '`
			  						  SET `active` = \'0\'
			  						  WHERE `id` = \'' . $group->getId() . '\';');
	}

	/*
	 * Returns an list of users that are members of this group.
	 */
	public static function getMembers(Group $group, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
																LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
																ON `' . Settings::db_table_infected_users . '`.`id` = `userId`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `groupId` = \'' . $group->getId() . '\'
																ORDER BY `firstname` ASC;');


		$memberList = [];

		while ($object = $result->fetch_object('User')) {
			$memberList[] = $object;
		}

		return $memberList;
	}

	/*
	 * Returns true of the specified user is member of a group in the given event.
	 */
	public static function isGroupMember(User $user, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_memberof . '`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `userId` = \'' . $user->getId() . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Return true if user has a leader for the given group.
	 */
	public static function hasGroupLeader(Group $group, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_memberof . '`
																WHERE `groupId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `groupId` = \'' . $group->getId() . '\'
																AND `groupLeader` > \'0\';');

		return $result->num_rows > 0;
	}

	/*
	 * Return true if user has a leader for the given group.
	 */
	public static function getGroupLeader(Group $group, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE `id` = (SELECT `userId` FROM `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
																							WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																							AND `groupId` = \'' . $group->getId() . '\'
																							AND `groupLeader` > \'0\'
																							LIMIT 1);');

		return $result->fetch_object('User');
	}

	/*
	 * Return true if the specified user is leader of a group.
	 */
	public static function isGroupLeader(User $user, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_memberof . '`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `groupLeader` = \'' . $user->getId() . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Change the specifised users grooup to the one specified.
	 */
	public static function setGroupForUser(User $user, Group $group) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		if ($user->isGroupMember()) {
			$database->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '`
											  SET `groupId` = \'' . $group->getId() . '\',
												  	`teamId` = \'0\'
											  WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
											  AND `userId` = \'' . $user->getId() . '\';');
		} else {
			$database->query('INSERT INTO `' . Settings::db_table_infected_crew_memberof . '` (`eventId`, `userId`, `groupId`, `teamId`, `groupLeader`, `teamLeader`)
											  VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\',
																\'' . $user->getId() . '\',
													  		\'' . $group->getId() . '\',
																\'0\',
																\'0\',
																\'0\');');
		}
	}

	/*
	 * Change the specifised users grooup to the one specified.
	 */
	public static function setGroupLeader(User $user = null, Group $group) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		if ($user != null && !$user->isGroupMember()) {
			self::setGroupForUser($user, $group);
		}

		$database->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '`
										  SET `groupLeader` = \'' . ($user != null ? $user->getId() : 0) . '\'
										  WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
											AND `groupId` = \'' . $user->getId() . '\';');

	}

	/*
	 * Remove a specified user from all groups.
	 */
	public static function removeUserFromGroup(User $user) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_memberof . '`
										  WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
											AND `userId` = \'' . $user->getId() . '\';');

	}

	/*
	 * Remove all users from the specified group.
	 */
	public static function removeUsersFromGroup(Group $group) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_memberof . '`
										  WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
										  AND `groupId` = \'' . $group->getId() . '\';');

	}
}
?>
