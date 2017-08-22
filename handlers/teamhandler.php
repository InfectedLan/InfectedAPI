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
require_once 'objects/team.php';
require_once 'objects/event.php';
require_once 'objects/user.php';
require_once 'objects/group.php';

class TeamHandler {
	/*
	 * Get the team by the internal id.
	 */
	public static function getTeam($id) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_teams . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		$database->close();

		return $result->fetch_object('Team');
	}

	/*
	 * Returns the team for the specified user and event.
	 */
	public static function getTeamByUserAndEvent(Event $event, User $user) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_teams . '`
																WHERE `id` = (SELECT `teamId` FROM `' . Settings::db_table_infected_crew_memberof . '`
																							WHERE `eventId` = \'' . $event->getId() . '\'
																							AND `userId` = \'' . $user->getId() . '\'
																							LIMIT 1);');

		$database->close();

		return $result->fetch_object('Team');
	}

	/*
	 * Returns the team of the specified user.
	 */
	public static function getTeamByUser(User $user) {
		return self::getTeamByUserAndEvent(EventHandler::getCurrentEvent(), $user);
	}

	/*
	 * Return the team of the specified leader.
	 */
	public static function getTeamByLeaderAndEvent(Event $event, User $user) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_teams . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `leaderId` = \'' . $user->getId() . '\';');

		$database->close();

		return $result->fetch_object('Team');
	}

	/*
	 * Return the team of the specified leader.
	 */
	public static function getTeamByLeader(User $user) {
		return self::getTeamByLeaderAndEvent(EventHandler::getCurrentEvent(), $user);
	}

	 /*
	 * Returns a list of all teams by event.
	 */
	public static function getTeamsByEvent(Event $event) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_teams . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																ORDER BY `groupId`, `name`;');

		$database->close();

		$teamList = [];

		while ($object = $result->fetch_object('Team')) {
			$teamList[] = $object;
		}

		return $teamList;
	}

	/*
	 * Returns a list of all teams.
	 */
	public static function getTeams() {
		return self::getTeamsByEvent(EventHandler::getCurrentEvent());
	}

	/*
	 * Returns a list of all teams in the specified group.
	 */
	public static function getTeamsByEventAndGroup(Event $event, Group $group) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_teams . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `groupId` = \'' . $group->getId() . '\'
																ORDER BY `groupId`, `name`;');

		$database->close();

		$teamList = [];

		while ($object = $result->fetch_object('Team')) {
			$teamList[] = $object;
		}

		return $teamList;
	}

	/*
	 * Returns a list of all teams in the specified group.
	 */
	public static function getTeamsByGroup(Group $group) {
		return self::getTeamsByEventAndGroup(EventHandler::getCurrentEvent(), $group);
	}

	/*
	 * Create a new team
	 */
	public static function createTeam(Event $event, Group $group, $name, $title, $description, User $leaderUser = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('INSERT INTO `' . Settings::db_table_infected_crew_teams . '` (`eventId`, `groupId`, `name`, `title`, `description`, `leaderId`)
										  VALUES (\'' . $event->getId() . '\',
														  \'' . $group->getId() . '\',
														  \'' . $database->real_escape_string($name) . '\',
														  \'' . $database->real_escape_string($title) . '\',
														  \'' . $database->real_escape_string($description) . '\',
														  \'' . ($leaderUser != null ? $leaderUser->getId() : 0) . '\')');

		$team = self::getTeam($database->insert_id);

		$database->close();

		return $team;
	}

	/*
	 * Update a team.
	 */
	public static function updateTeam(Team $team, Group $group, $name, $title, $description, User $leaderUser = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_teams . '`
										  SET `groupId` = \'' . $group->getId() . '\',
												  `name` = \'' . $database->real_escape_string($name) . '\',
												  `title` = \'' . $database->real_escape_string($title) . '\',
												  `description` = \'' . $database->real_escape_string($description) . '\',
												  `leaderId` = \'' . ($leaderUser != null ? $leaderUser->getId() : 0) . '\'
										  WHERE `id` = \'' . $team->getId() . '\';');

		$database->close();
	}

	/*
	 * Remove a team.
	 */
	public static function removeTeam(Team $team) {
		self::removeUsersFromTeam($team);

		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_teams . '`
						  				WHERE `id` = \'' . $team->getId() . '\';');

		$database->close();
	}

	/*
	 * Remove all teams linked to a specified group.
	 */
	public static function removeTeamsByGroup(Group $group) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_teams . '`
						  				WHERE `groupId` = \'' . $group->getId() . '\';');

		$database->close();
	}

	/*
	 * Returns an array of users that are members of this team in the given event.
	 */
	public static function getMembersByEvent(Event $event, Team $team) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
																LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
																ON `' . Settings::db_table_infected_users . '`.`id` = `userId`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `teamId` = \'' . $team->getId() . '\'
																ORDER BY `firstname` ASC;');

		$database->close();

		$memberList = [];

		while ($object = $result->fetch_object('User')) {
			$memberList[] = $object;
		}

		return $memberList;
	}

	/*
	 * Returns an array of users that are members of this team.
	 */
	public static function getMembers(Team $team) {
		return self::getMembersByEvent(EventHandler::getCurrentEvent(), $team);
	}

	/*
	 * Is member of a team in the given event.
	 */
	public static function isTeamMemberByEvent(User $user, Event $event) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_memberof. '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `userId` = \'' . $user->getId() . '\'
																AND `teamId` != \'0\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Is member of a team which means it's not a plain user.
	 */
	public static function isTeamMember(User $user) {
		return self::isTeamMemberByEvent($user, EventHandler::getCurrentEvent());
	}

	/*
	 * Return true if user has a leader for the given team and event.
	 */
	public static function hasTeamLeaderByEvent(Team $team, Event $event) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_teams . '`
																WHERE `id` = \'' . $team->getId() . '\'
																AND `eventId` = \'' . $event->getId() . '\'
																AND `leaderId` > \'0\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Return true if user has a leader for the given team.
	 */
	public static function hasTeamLeader(Team $team) {
		return self::hasTeamLeaderByEvent($team, EventHandler::getCurrentEvent());
	}

	/*
	 * Return true if user is leader for a team.
	 */
	public static function isTeamLeaderByEvent(User $user, Event $event) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_teams . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `leaderId` = \'' . $user->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Return true if user is leader for a team.
	 */
	public static function isTeamLeader(User $user) {
		return self::isTeamLeaderByEvent($user, EventHandler::getCurrentEvent());
	}

	/*
	 * Sets the users team.
	 */
	public static function changeTeamForUser(User $user, Team $team) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		if ($user->isGroupMember()) {
			$database->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '`
											  SET `teamId` = \'' . $team->getId() . '\'
												WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
											  AND `userId` = \'' . $user->getId() . '\'
											  AND `groupId` = \'' . $team->getGroup()->getId() . '\';');
		}

		$database->close();
	}

	/*
	 * Removes a user from a team.
	 */
	public static function removeUserFromTeam(User $user) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '`
										  SET `teamId` = \'0\'
										  WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
											AND `userId` = \'' . $user->getId() . '\';');

		$database->close();
	}

	/*
	 * Removes all users from the specified team.
	 */
	public static function removeUsersFromTeam(Team $team) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '`
										  SET `teamId` = \'0\'
										  WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
										  AND `teamId` = \'' . $team->getId() . '\';');

		$database->close();
	}
}
?>
