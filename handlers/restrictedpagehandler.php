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

require_once 'session.php';
require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/restrictedpage.php';
require_once 'objects/group.php';
require_once 'objects/team.php';

class RestrictedPageHandler {
	/*
	 * Get page by the internal id.
	 */
	public static function getPage(int $id): ?RestrictedPage {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('RestrictedPage');
	}

	/*
	 * Get page by name.
	 */
	public static function getPageByName(string $name): ?RestrictedPage {
		if (Session::isAuthenticated()) {
			$user = Session::getCurrentUser();

			if ($user->hasPermission('*') ||
				$user->isGroupMember()) {
				$database = Database::getConnection(Settings::db_name_infected_crew);

  			if ($user->hasPermission('*')) {
					$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
																			WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
																			AND `name` = \'' . $database->real_escape_string($name) . '\';');
  			} else if ($user->isGroupLeader()) {
					$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
																			WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
																			AND `name` = \'' . $database->real_escape_string($name) . '\'
																			AND (`groupId` = \'0\' OR `groupId` = \'' . $user->getGroup()->getId() . '\');');
				} else if ($user->isTeamMember()) {
					$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
																			WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
																			AND `name` = \'' . $database->real_escape_string($name) . '\'
																			AND (`groupId` = \'0\' OR `groupId` = \'' . $user->getGroup()->getId() . '\')
																			AND (`teamId` = \'0\' OR `teamId` = \'' . $user->getTeam()->getId() . '\');');
				} else {
					$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
																			WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
																			AND `name` = \'' . $database->real_escape_string($name) . '\'
																			AND (`groupId` = \'0\' OR `groupId` = \'' . $user->getGroup()->getId() . '\')
																			AND `teamId` = \'0\';');
				}

				return $result->fetch_object('RestrictedPage');
			}
		}
	}

	/*
	 * Get a list of all pages.
	 */
	public static function getPages(): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
																WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\';');

		$restrictedPageList = [];

		while ($object = $result->fetch_object('RestrictedPage')) {
			$restrictedPageList[] = $object;
		}

		return $restrictedPageList;
	}

	/*
	 * Get a list of pages for specified group.
	 */
	public static function getPagesForGroup(Group $group): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
																WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
																AND `groupId` = \'' . $group->getId() . '\'
																AND `teamId` = \'0\';');

		$restrictedPageList = [];

		while ($object = $result->fetch_object('RestrictedPage')) {
			$restrictedPageList[] = $object;
		}

		return $restrictedPageList;
	}

	  /*
	 * Get a list of all pages for specified group, ignoring the teamId.
	 */
	public static function getAllPagesForGroup(Group $group): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
																WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
																AND `groupId` = \'' . $group->getId() . '\';');


		$restrictedPageList = [];

		while ($object = $result->fetch_object('RestrictedPage')) {
			$restrictedPageList[] = $object;
		}

		return $restrictedPageList;
	}

	/*
	 * Get a list of pages for specified team.
	 */
	public static function getPagesForGroupAndTeam(Group $group, Team $team): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
																WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
																AND `groupId` = \'' . $group->getId() . '\'
																AND (`teamId` = \'' . $team->getId() . '\' OR `teamId` = \'0\');');

		$restrictedPageList = [];

		while ($object = $result->fetch_object('RestrictedPage')) {
			$restrictedPageList[] = $object;
		}

		return $restrictedPageList;
	}

	/*
	 * Create a new page.
	 */
	public static function createPage(string $name, string $title, string $content, Group $group = null, Team $team = null): RestrictedPage {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('INSERT INTO `' . Settings::db_table_infected_crew_pages . '` (`eventId`, `name`, `title`, `content`, `groupId`, `teamId`)
										  VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\',
														  \'' . $database->real_escape_string($name) . '\',
														  \'' . $database->real_escape_string($title) . '\',
														  \'' . $database->real_escape_string($content) . '\',
														  \'' . ($group != null ? $group->getId() : '0') . '\',
														  \'' . ($team != null ? $team->getId() : '0') . '\')');

		return self::getRestrictedPage($database->insert_id);
	}

	  /*
	 * Update a page.
	 */
	public static function updatePage(RestrictedPage $page, string $title, string $content, Group $group = null, Team $team = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_pages . '`
										  SET `title` = \'' . $database->real_escape_string($title) . '\',
											  	`content` = \'' . $database->real_escape_string($content) . '\',
													`groupId` = \'' . ($group != null ? $group->getId() : '0') . '\',
													`teamId` = \'' . ($team != null ? $team->getId() : '0') . '\'
										  WHERE `id` = \'' . $page->getId() . '\';');
	}

	/*
	 * Remove a page.
	 */
	public static function removePage(RestrictedPage $page) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_pages . '`
						  				WHERE `id` = \'' . $page->getId() . '\';');
	}
}
?>
