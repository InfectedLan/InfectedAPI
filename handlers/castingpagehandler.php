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
require_once 'objects/castingpage.php';
require_once 'objects/compo.php';
require_once 'objects/match.php';

class CastingPageHandler {
	/*
	 * Get a server by internal id
	 */
	public static function getCastingPage(int $id): ?CastingPage {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_castingpages . '`
																WHERE `id` = \'' . $id . '\';');

		return $result->fetch_object('CastingPage');
	}

	/*
	 * Get a server for a specified compo.
	 */
	public static function getCastingPages(Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_castingpages . '`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\';');

		$serverList = [];

		while ($object = $result->fetch_object('CastingPage')) {
			$serverList[] = $object;
		}

		return $serverList;
	}

    /*
     * Creates a new server entry
     */
	public static function createCastingPage(Event $event, string $name, string $data, string $template): int {
    $database = Database::getConnection(Settings::db_name_infected_crew);

    $database->query('INSERT INTO `' . Settings::db_table_infected_crew_castingpages . '`(`eventId`, `name`, `data`, `template`)
											VALUES (\'' . $event->getId() . '\',
															\'' . $database->real_escape_string($name) . '\',
															\'' . $database->real_escape_string($data) . '\',
															\'' . $database->real_escape_string($template) . '\');');

    return $database->insert_id;
	}

  /*
   * Deletes a server entry
   */
  public static function deleteCastingPage(CastingPage $castingPage) {
    $database = Database::getConnection(Settings::db_name_infected_crew);

    $database->query('DELETE FROM `' . Settings::db_table_infected_crew_castingpages . '`
											WHERE `id` = \'' . $castingPage->getId() . '\';');
  }

	/*
	 * Sets the connection details of a server
	 */
	public static function setData(CastingPage $castingPage, string $data) {
    $database = Database::getConnection(Settings::db_name_infected_crew);

    $result = $database->query('UPDATE `' . Settings::db_table_infected_crew_castingpages . '`
																SET `data` = \'' . $database->real_escape_string($data) . '\'
																WHERE `` = \'' . $castingPage->getId() . '\';');
	}

	/*
	 * Sets the human name of a server
	 */
	public static function setName(CastingPage $castingPage, string $name) {
    $database = Database::getConnection(Settings::db_name_infected_crew);

    $result = $database->query('UPDATE `' . Settings::db_table_infected_crew_castingpages . '`
																SET `name` = \'' . $database->real_escape_string($name) . '\'
																WHERE `` = \'' . $castingPage->getId() . '\';');
	}

  /*
   * Sets the template for a castingPage
   */
  public static function setTemplate(CastingPage $castingPage, string $template) {
    $database = Database::getConnection(Settings::db_name_infected_crew);

    $result = $database->query('UPDATE `' . Settings::db_table_infected_crew_castingpages . '`
																SET `template` = \'' . $database->real_escape_string($template) . '\'
																WHERE `` = \'' . $castingPage->getId() . '\';');
  }
}
?>
