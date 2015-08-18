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
require_once 'objects/group.php';
require_once 'objects/event.php';
require_once 'objects/user.php';

class GroupHandler {
	/*
	 * Get a group by the internal id.
	 */
	public static function getGroup($id) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		$database->close();

		return $result->fetch_object('Group');
	}

	/*
	 * Get a group for the specified user from the given event.
	 */
	public static function getGroupByEventAndUser(Event $event, User $user) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '`
																WHERE `id` = (SELECT `groupId` FROM `' . Settings::db_table_infected_crew_memberof . '`
																						  WHERE `eventId` = \'' . $event->getId() . '\'
																						  AND `userId` = \'' . $user->getId() . '\'
																						  LIMIT 1);');

		$database->close();

		return $result->fetch_object('Group');
	}

	/*
	 * Get a group for the specified user.
	 */
	public static function getGroupByUser(User $user) {
		return self::getGroupByEventAndUser(EventHandler::getCurrentEvent(), $user);
	}

	/*
	 * Get a list of all groups from the given event.
	 */
	public static function getGroupsByEvent(Event $event) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																ORDER BY `name`;');

		$database->close();

		$groupList = array();

		while ($object = $result->fetch_object('Group')) {
			array_push($groupList, $object);
		}

		return $groupList;
	}

	/*
	 * Get a list of all groups.
	 */
	public static function getGroups() {
		return self::getGroupsByEvent(EventHandler::getCurrentEvent());
	}

	/*
	 * Create a new group
	 */
	public static function createGroup(Event $event, $name, $title, $description, User $leaderUser = null, User $coleaderUser = null) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('INSERT INTO `' . Settings::db_table_infected_crew_groups . '` (`eventId`, `name`, `title`, `description`, `leaderId`, `coleaderId`)
										  VALUES (\'' . $event->getId() . '\',
														  \'' . $database->real_escape_string($name) . '\',
														  \'' . $database->real_escape_string($title) . '\',
														  \'' . $database->real_escape_string($description) . '\',
														  \'' . ($leaderUser != null ? $leaderUser->getId() : 0) . '\',
															\'' . ($coleaderUser != null ? $coleaderUser->getId() : 0) . '\');');

		$group = self::getGroup($database->insert_id);

		$database->close();

		return $group;
	}

	/*
	 * Update the specified group.
	 */
	public static function updateGroup(Group $group, $name, $title, $description, User $leaderUser = null, User $coleaderUser = null) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_groups . '`
			  						  SET `name` = \'' . $database->real_escape_string($name) . '\',
				  								`title` = \'' . $database->real_escape_string($title) . '\',
				  								`description` = \'' . $database->real_escape_string($description) . '\',
				  								`leaderId` = \'' . ($leaderUser != null ? $leaderUser->getId() : 0) . '\',
				  								`coleaderId` = \'' . ($coleaderUser != null ? $coleaderUser->getId() : 0) . '\'
			  						  WHERE `id` = \'' . $group->getId() . '\';');

		$database->close();
	}

	/*
	 * Remove the specified group
	 */
	public static function removeGroup(Group $group) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_groups . '`
						  				WHERE `id` = \'' . $group->getId() . '\';');

		$database->close();
	}

	/*
	 * Returns an list of users that are members of this group in the given event.
	 */
	public static function getMembersByEvent(Event $event, Group $group) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
																LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
																ON `' . Settings::db_table_infected_users . '`.`id` = `userId`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `groupId` = \'' . $group->getId() . '\'
																ORDER BY `firstname` ASC;');

		$database->close();

		$memberList = array();

		while ($object = $result->fetch_object('User')) {
			array_push($memberList, $object);
		}

		return $memberList;
	}

	/*
	   * Returns an list of users that are members of this group.
	   */
	public static function getMembers(Group $group) {
		return self::getMembersByEvent(EventHandler::getCurrentEvent(), $group);
	}

	/*
	 * Returns true of the specified user is member of a group in the given event.
	 */
	public static function isGroupMemberByEvent(Event $event, User $user) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_memberof . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `userId` = \'' . $user->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Returns true of the specified user is member of a group.
	 */
	public static function isGroupMember(User $user) {
		return self::isGroupMemberByEvent(EventHandler::getCurrentEvent(), $user);
	}

	/*
	 * Return true if user has a leader for the given group and event.
	 */
	public static function hasGroupLeaderByEvent(Group $group, Event $event) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_groups . '`
																WHERE `id` = \'' . $group->getId() . '\'
																AND `eventId` = \'' . $event->getId() . '\'
																AND `leaderId` > \'0\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Return true if user has a leader for the given group.
	 */
	public static function hasGroupLeader(Group $group) {
		return self::hasGroupLeaderByEvent($group, EventHandler::getCurrentEvent());
	}

	/*
	 * Return true if the specified user is leader of a group.
	 */
	public static function isGroupLeaderByEvent(Event $event, User $user) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_groups . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `leaderId` = \'' . $user->getId() . '\';');
		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Return true if the specified user is leader of a group.
	 */
	public static function isGroupLeader(User $user) {
		return self::isGroupLeaderByEvent(EventHandler::getCurrentEvent(), $user);
	}

	/*
	 * Return true if user has a co-leader for the given group and event.
	 */
	public static function hasGroupCoLeaderByEvent(Group $group, Event $event) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_groups . '`
																WHERE `id` = \'' . $group->getId() . '\'
																AND `eventId` = \'' . $event->getId() . '\'
																AND `coleaderId` > \'0\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Return true if user has a co-leader for the given group.
	 */
	public static function hasGroupCoLeader(Group $group) {
		return self::hasGroupCoLeaderByEvent($group, EventHandler::getCurrentEvent());
	}

	/*
	 * Return true if user is co-leader for a group.
	 */
	public static function isGroupCoLeaderByEvent(Event $event, User $user) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_groups . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `coleaderId` = \'' . $user->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Return true if user is co-leader for a group.
	 */
	public static function isGroupCoLeader(User $user) {
		return self::isGroupCoLeaderByEvent(EventHandler::getCurrentEvent(), $user);
	}

	/*
	 * Change the specifised users grooup to the one specified.
	 */
	public static function changeGroupForUser(User $user, Group $group) {
		$database = Database::open(Settings::db_name_infected_crew);

		if ($user->isGroupMember()) {
			$database->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '`
											  SET `groupId` = \'' . $group->getId() . '\',
												  	`teamId` = \'0\'
											  WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
											  AND `userId` = \'' . $user->getId() . '\';');
		} else {
			$database->query('INSERT INTO `' . Settings::db_table_infected_crew_memberof . '` (`eventId`, `userId`, `groupId`, `teamId`)
											  VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\',
																\'' . $user->getId() . '\',
													  		\'' . $group->getId() . '\',
													  		\'0\');');
		}

		$database->close();
	}

	/*
	 * Remove a specified user from all groups.
	 */
	public static function removeUserFromGroup(User $user) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_memberof . '`
										  WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
											AND `userId` = \'' . $user->getId() . '\';');

		$database->close();
	}

	/*
	 * Remove all users from the specified group.
	 */
	public static function removeUsersFromGroup(Group $group) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_memberof . '`
										  WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
										  AND `groupId` = \'' . $group->getId() . '\';');

		$database->close();
	}
}
?>
