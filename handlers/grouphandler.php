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
	/* OK!
	 * Get a group by the internal id.
	 */
	public static function getGroup($id) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');


		return $result->fetch_object('Group');
	}

	/* OK!
	 * Get a group for the specified user.
	 */
	public static function getGroupByUser(User $user, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		if ($event != null && $event != EventHandler::getCurrentEvent()) {
			// Fetch all teams for previous events. Ignoring the active field, because we want historical events too.
			$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '`
																	WHERE `id` = (SELECT `groupId` FROM `' . Settings::db_table_infected_crew_memberof . '`
																								WHERE `eventId` = \'' . $event->getId() . '\'
																								AND `userId` = \'' . $user->getId() . '\'
																								LIMIT 1);');
		} else {
			// Fetch all teams for current event.
			$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '`
																	WHERE `id` = (SELECT `groupId` FROM `' . Settings::db_table_infected_crew_memberof . '`
																								WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
																								AND `userId` = \'' . $user->getId() . '\'
																								LIMIT 1)
																	AND `active` = \'1\';');
		}

		return $result->fetch_object('Group');
	}

	/* OK!
	 * Get a list of all groups.
	 */
	public static function getGroups(Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		if ($event != null && $event != EventHandler::getCurrentEvent()) {
			// Fetch all teams for previous events. Ignoring the active field, because we want historical events too.
			$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '`
																	WHERE `id` IN (SELECT `groupId` FROM `' . Settings::db_table_infected_crew_memberof . '`
																								 WHERE `eventId` = \'' . $event->getId() . '\')
																	ORDER BY `name`;');

		} else {
			// Fetch all teams for current event.
			$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '`
																	WHERE `active` = \'1\'
																	ORDER BY `name`;');
		}

		$groupList = [];

		while ($object = $result->fetch_object('Group')) {
			$groupList[] = $object;
		}

		return $groupList;
	}

	/* OK!
	 * Create a new group.
	 */
	public static function createGroup($name, $title, $description, User $leaderUser = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('INSERT INTO `' . Settings::db_table_infected_crew_groups . '` (`name`, `title`, `description`, `active`)
										  VALUES (\'' . $database->real_escape_string($name) . '\',
														  \'' . $database->real_escape_string($title) . '\',
														  \'' . $database->real_escape_string($description) . '\',
															\'1\');');

		$group = self::getGroup($database->insert_id);

		if ($leaderUser != null) {
			self::setGroupLeader($leaderUser, $group);
		}

		return $group;
	}

	/* OK!
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

	/* OK!
	 * Remove the specified group
	 */
	public static function removeGroup(Group $group) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_groups . '`
			  						  SET `active` = \'0\'
			  						  WHERE `id` = \'' . $group->getId() . '\';');

		// TODO: What to do with teams here?
	}

	/* OK!
	 * Returns true of the specified user is member of a group in the given event.
	 */
	public static function isGroupMember(User $user, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `' . Settings::db_table_infected_crew_memberof . '`.* FROM `' . Settings::db_table_infected_crew_memberof . '`
																INNER JOIN `' . Settings::db_table_infected_crew_groups . '`
																ON `groupId` = `' . Settings::db_table_infected_crew_groups . '`.`id`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `userId` = \'' . $user->getId() . '\'
																AND `active` != \'0\';');

		return $result->num_rows > 0;
	}

	/* OK!
	 * Returns true of the specified user is member of a group in the given event.
	 */
	public static function isGroupMemberOf(User $user, Group $group, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_memberof . '`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `userId` = \'' . $user->getId() . '\'
																AND `groupId` = \'' . $group->getId() . '\';');

		return $result->num_rows > 0;
	}

	/* OK! Multi-group support!
	 * Change the specifised users grooup to the one specified.
	 */
	public static function addGroupMember(User $user, Group $group, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		if (!$group->isMember($user)) {
			$database->query('INSERT INTO `' . Settings::db_table_infected_crew_memberof . '` (`eventId`, `userId`, `groupId`, `teamId`, `groupLeader`, `teamLeader`)
											  VALUES (\'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\',
																\'' . $user->getId() . '\',
													  		\'' . $group->getId() . '\',
																\'0\',
																\'0\',
																\'0\');');
		}
	}

	/* OK!
	 * Remove a specified user from all groups.
	 */
	public static function removeGroupMember(User $user, Group $group, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_memberof . '`
										  WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
											AND `userId` = \'' . $user->getId() . '\'
											AND `groupId` = \'' . $group->getId() . '\';');
	}

	/* OK!
	 * Returns an list of users that are members of this group.
	 */
	public static function getGroupMembers(Group $group, Event $event = null) {
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

	/* OK!
	 * Remove all users from the specified group.
	 */
	public static function removeGroupMembers(Group $group, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_memberof . '`
											WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
											AND `groupId` = \'' . $group->getId() . '\';');
	}

	/* OK!
	 * Return true if user has a leader for the given group.
	 */
	public static function hasGroupLeader(Group $group, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_memberof . '`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `groupId` = \'' . $group->getId() . '\'
																AND `groupLeader` > \'0\';');

		return $result->num_rows > 0;
	}

	/* OK!
	 * Return true if the specified user is leader of a group.
	 */
	public static function isGroupLeader(User $user, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `' . Settings::db_table_infected_crew_memberof . '`.* FROM `' . Settings::db_table_infected_crew_memberof . '`
																INNER JOIN `' . Settings::db_table_infected_crew_groups . '`
																ON `groupId` = `' . Settings::db_table_infected_crew_groups . '`.`id`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `userId` = \'' . $user->getId() . '\'
																AND `groupLeader` > \'0\'
																AND `active` != \'0\';');

		return $result->num_rows > 0;
	}

	/* OK!
	 * Return true if the specified user is leader of a group.
	 */
	public static function isGroupLeaderOf(User $user, Group $group, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_memberof . '`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `userId` = \'' . $user->getId() . '\'
																AND `groupId` = \'' . $group->getId() . '\'
																AND `groupLeader` = \'' . $group->getId() . '\';');

		return $result->num_rows > 0;
	}

	/* OK!
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

	/* OK! Multi-group support!
	 * Change the specifised users group to the one specified.
	 */
	public static function setGroupLeader(User $user = null, Group $group, Event $event = null) {
		if ($user != null && !$group->isMember($user)) {
			self::addGroupMember($user, $group);
		}

		$database = Database::getConnection(Settings::db_name_infected_crew);

		// Remove old leaders of the group, to avoid duplicates.
		$database->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '`
											SET `groupLeader` = \'0\'
											WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
											AND `groupId` = \'' . $group->getId() . '\';');

		// Make our user the leader of the group, if one where specified.
		if ($user != null) {
			$database->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '`
												SET `groupLeader` = \'' . $group->getId() . '\'
												WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
												AND `userId` = \'' . $user->getId() . '\'
												AND `groupId` = \'' . $group->getId() . '\';');
		}
	}
}
?>
