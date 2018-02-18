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
require_once 'handlers/eventhandler.php';
require_once 'objects/event.php';
require_once 'objects/group.php';
require_once 'objects/team.php';
require_once 'objects/user.php';

class TeamHandler {
	/*
	 * Get the team by the internal id.
	 */
	public static function getTeam(int $id): ?Team {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_crew_teams . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('Team');
	}

	/*
	 * Returns the team for the specified user.
	 */
	public static function getTeamByUser(User $user, Event $event = null): ?Team {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		if ($event != null && $event != EventHandler::getCurrentEvent()) {
			// Fetch all teams for previous events. Ignoring the active field, because we want historical events too.
			$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_crew_teams . '`
																	WHERE `id` = (SELECT `teamId` FROM `' . DatabaseConstants::db_table_infected_crew_memberof . '`
																								WHERE `eventId` = \'' . $event->getId() . '\'
																								AND `userId` = \'' . $user->getId() . '\'
																								AND `teamId` > \'0\'
																								LIMIT 1);');
		} else {
			// Fetch all teams for current event.
			$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_crew_teams . '`
																	WHERE `id` = (SELECT `teamId` FROM `' . DatabaseConstants::db_table_infected_crew_memberof . '`
																								WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
																								AND `userId` = \'' . $user->getId() . '\'
																								AND `teamId` > \'0\'
																								LIMIT 1)
																	AND `active` = \'1\';');
		}

		return $result->fetch_object('Team');
	}

	/*
	 * Returns a list of all teams.
	 */
	public static function getTeams(Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		if ($event != null && $event != EventHandler::getCurrentEvent()) {
			// Fetch all teams for previous events. Ignoring the active field, because we want historical events too.
			$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_crew_teams . '`
																	WHERE `id` IN (SELECT `teamId` FROM `' . DatabaseConstants::db_table_infected_crew_memberof . '`
																								 WHERE `eventId` = \'' . $event->getId() . '\')
																	ORDER BY `name`;');

		} else {
			// Fetch all teams for current event.
			$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_crew_teams . '`
																	WHERE `active` = \'1\'
																	ORDER BY `name`;');
		}

		$teamList = [];

		while ($object = $result->fetch_object('Team')) {
			$teamList[] = $object;
		}

		return $teamList;
	}

	/*
	 * Returns a list of all teams in the specified group.
	 */
	public static function getTeamsByGroup(Group $group, Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		if ($event != null && $event != EventHandler::getCurrentEvent()) {
			// Fetch all teams for previous events. Ignoring the active field, because we want historical events too.
			$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_crew_teams . '`
																	WHERE `id` IN (SELECT `teamId` FROM `' . DatabaseConstants::db_table_infected_crew_memberof . '`
																								 WHERE `eventId` = \'' . $event->getId() . '\'
																								 AND `groupId` = \'' . $group->getId() . '\')
																  ORDER BY `name`;');
		} else {
			// Fetch all teams for current event.
			$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_crew_teams . '`
																	WHERE `groupId` = \'' . $group->getId() . '\'
																	AND `active` = \'1\'
																	ORDER BY `name`;');
		}

		$teamList = [];

		while ($object = $result->fetch_object('Team')) {
			$teamList[] = $object;
		}

		return $teamList;
	}

	/*
	 * Create a new team
	 */
	public static function createTeam(Group $group, string $name, string $title, string $description): Team {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_crew_teams . '` (`groupId`, `name`, `title`, `description`, `active`)
										  VALUES (\'' . $group->getId() . '\',
														  \'' . $database->real_escape_string($name) . '\',
														  \'' . $database->real_escape_string($title) . '\',
														  \'' . $database->real_escape_string($description) . '\',
															\'1\')');

		$team = self::getTeam($database->insert_id);

		if ($leaderUser != null) {
			self::setTeamLeader($leaderUser, $team);
		}

		return $team;
	}

	/*
	 * Update a team.
	 */
	public static function updateTeam(Team $team, Group $group, string $name, string $title, string $description, User $leaderUser = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . DatabaseConstants::db_table_infected_crew_teams . '`
										  SET `groupId` = \'' . $group->getId() . '\',
												  `name` = \'' . $database->real_escape_string($name) . '\',
												  `title` = \'' . $database->real_escape_string($title) . '\',
												  `description` = \'' . $database->real_escape_string($description) . '\'
										  WHERE `id` = \'' . $team->getId() . '\';');

	  self::setTeamLeader($leaderUser, $team);
	}

	/*
	 * Remove a team.
	 */
	public static function removeTeam(Team $team) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . DatabaseConstants::db_table_infected_crew_teams . '`
										  SET `active` = \'0\'
										  WHERE `id` = \'' . $team->getId() . '\';');
	}

	/*
	 * Remove all teams linked to a specified group.
	 */
	public static function removeTeams(Group $group) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . DatabaseConstants::db_table_infected_crew_teams . '`
										  SET `active` = \'0\'
										  WHERE `groupId` = \'' . $group->getId() . '\';');
	}

	/*
	 * Is member of a team.
	 */
	public static function isTeamMember(User $user, Event $event = null): bool {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		if ($event != null && $event != EventHandler::getCurrentEvent()) {
			$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_crew_memberof . '`
																	INNER JOIN `' . DatabaseConstants::db_table_infected_crew_teams . '`
																	ON `teamId` = `' . DatabaseConstants::db_table_infected_crew_teams . '`.`id`
																	WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																	AND `userId` = \'' . $user->getId() . '\'
																	AND `active` = \'1\';');
		} else {
			$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_crew_memberof . '`
																	INNER JOIN `' . DatabaseConstants::db_table_infected_crew_teams . '`
																	ON `teamId` = `' . DatabaseConstants::db_table_infected_crew_teams . '`.`id`
																	WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																	AND `userId` = \'' . $user->getId() . '\';');
		}

		return $result->num_rows > 0;
	}

	/*
	 * Is member of a team.
	 */
	public static function isTeamMemberOf(User $user, Team $team, Event $event = null): bool {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_crew_memberof . '`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `userId` = \'' . $user->getId() . '\'
																AND `teamId` = \'' . $team->getId() . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Sets the users team.
	 */
	public static function addTeamMember(User $user, Team $team, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$group = $team->getGroup();

		if ($group->isMember($user)) {
			if ($user->isTeamMember() && !$team->isMember($user)) {
				$database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_crew_memberof . '` (`eventId`, `userId`, `groupId`, `teamId`, `groupLeader`, `teamLeader`)
												  VALUES (\'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\',
																	\'' . $user->getId() . '\',
														  		\'' . $group->getId() . '\',
																	\'' . $team->getId() . '\',
																	\'0\',
																	\'0\');');
			} else {
				$database->query('UPDATE `' . DatabaseConstants::db_table_infected_crew_memberof . '`
													SET `teamId` = \'' . $team->getId() . '\'
													WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
													AND `groupId` = \'' . $group->getId() . '\'
													AND `userId` = \'' . $user->getId() . '\';');
			}
		}
	}

	/*
	 * Removes a user from a team.
	 */
	public static function removeTeamMember(User $user, Team $team, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . DatabaseConstants::db_table_infected_crew_memberof . '`
										  SET `teamId` = \'0\'
										  WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
											AND `userId` = \'' . $user->getId() . '\'
											AND `teamId` = \'' . $team->getId() . '\';');
	}

	/*
	 * Returns an array of users that are members of this team in the given event.
	 */
	public static function getTeamMembers(Team $team, Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `' . DatabaseConstants::db_table_infected_users . '`.* FROM `' . DatabaseConstants::db_table_infected_users . '`
																INNER JOIN `' . Settings::db_name_infected_crew . '`.`' . DatabaseConstants::db_table_infected_crew_memberof . '`
																ON `' . DatabaseConstants::db_table_infected_users . '`.`id` = `userId`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `teamId` = \'' . $team->getId() . '\'
																ORDER BY `firstname` ASC;');

		$memberList = [];

		while ($object = $result->fetch_object('User')) {
			$memberList[] = $object;
		}

		return $memberList;
	}

	/*
	 * Removes all users from the specified team.
	 */
	public static function removeTeamMembers(Team $team, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . DatabaseConstants::db_table_infected_crew_memberof . '`
						 SET `teamId` = 0
						 WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
						 AND `teamId` = ' . $team->getId() . ';');
	}

	/*
	 * Return true if user has a leader for the given team.
	 */
	public static function hasTeamLeader(Team $team, Event $event = null): bool {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_crew_memberof . '`
								   WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
								   AND `teamId` = ' . $team->getId() . '
								   AND `teamLeader` != 0;');

		return $result->num_rows > 0;
	}

	/*
	 * Return true if user is leader for a team.
	 */
	public static function isTeamLeader(User $user, Event $event = null): bool {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `' . DatabaseConstants::db_table_infected_crew_memberof . '`.* FROM `' . DatabaseConstants::db_table_infected_crew_memberof . '`
								   INNER JOIN `' . DatabaseConstants::db_table_infected_crew_teams . '` ON `teamId` = `' . DatabaseConstants::db_table_infected_crew_teams . '`.`id`
								   WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
								   AND `userId` = ' . $user->getId() . '
								   AND `teamLeader` != 0
								   AND `active` != 0;');

		return $result->num_rows > 0;
	}

	/*
	 * Return true if user is leader for a team.
	 */
	public static function isTeamLeaderOf(User $user, Team $team, Event $event = null): bool {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `' . DatabaseConstants::db_table_infected_crew_memberof . '`.* FROM `' . DatabaseConstants::db_table_infected_crew_memberof . '`
								   WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
								   AND `userId` = ' . $user->getId() . '
								   AND `teamId` = ' . $team->getId() . '
								   AND `teamLeader` = 1;');

		return $result->num_rows > 0;
	}

	/*
	 * Return the team leader for a team.
	 */
	public static function getTeamLeader(Team $team, Event $event = null): ?User {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_users . '`
								   WHERE `id` = (SELECT `' . DatabaseConstants::db_table_infected_crew_memberof . '`.`userId` FROM `' . Settings::db_name_infected_crew . '`.`' . DatabaseConstants::db_table_infected_crew_memberof . '`
												 WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
												 AND `groupId` = ' . $team->getGroup()->getId() . '
												 AND `teamId` = ' . $team->getId() . '
												 AND `teamLeader` != 0
												 LIMIT 1);');

		return $result->fetch_object('User');
	}

	/*
	 * Sets the teams leader.
	 */
	public static function setTeamLeader(User $user = null, Team $team, Event $event = null) {
		if ($user != null && !$team->isMember($user)) {
			self::addTeamMember($user, $team);
		}

		$database = Database::getConnection(Settings::db_name_infected_crew);

		// Remove old leaders of the team, to avoid duplicates.
		$database->query('UPDATE `' . DatabaseConstants::db_table_infected_crew_memberof . '`
						 SET `teamLeader` = 0
						 WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
						 AND `groupId` = ' . $team->getGroup()->getId() . '
						 AND `teamId` = ' . $team->getId() . ';');

		// Make our user the leader of the team, if one where specified.
		if ($user != null) {
			$database->query('UPDATE `' . DatabaseConstants::db_table_infected_crew_memberof . '`
							 SET `teamLeader` = 1
							 WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
							 AND `userId` = ' . $user->getId() . '
							 AND `groupId` = ' . $team->getGroup()->getId() . '
							 AND `teamId` = ' . $team->getId() . ';');
	  }
	}
}
