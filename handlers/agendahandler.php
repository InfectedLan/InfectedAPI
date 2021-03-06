<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2018 Infected <https://infected.no/>.
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
require_once 'objects/agenda.php';
require_once 'objects/event.php';

class AgendaHandler {
	/*
	 * Get an agenda by the internal id.
	 */
	public static function getAgenda(int $id): ?Agenda {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '`
								   WHERE `id` = ' . $database->real_escape_string($id) . ';');

		return $result->fetch_object('Agenda');
	}

	/*
	   * Returns agendas.
	   */
	public static function getAgendas(Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '`
								   WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
								   ORDER BY `startTime`;');

		$agendaList = [];

		while ($object = $result->fetch_object('Agenda')) {
			$agendaList[] = $object;
		}

		return $agendaList;
	}

	/*
	 * Returns published agendas.
  	 */
	public static function getPublishedAgendas(Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '`
								   WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
								   AND `published` = 1
								   ORDER BY `startTime`;');

		$agendaList = [];

		while ($object = $result->fetch_object('Agenda')) {
		  $agendaList[] = $object;
		}

		return $agendaList;
	}

	/*
	 * Returns only published agendas that have not happend yet.
	 */
	public static function getPublishedNotHappendAgendas(Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '`
								   WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
								   AND DATE_ADD(`startTime`, INTERVAL 1 HOUR) >= NOW()
								   AND `published` = 1
								   ORDER BY `startTime`;');

		$agendaList = [];

		while ($object = $result->fetch_object('Agenda')) {
			$agendaList[] = $object;
		}

		return $agendaList;
	}

	/*
	 * Create a new agenda entry.
	 */
	public static function createAgenda(Event $event, string $name, string $title, string $description, string $startTime): Agenda {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$database->query('INSERT INTO `' . Settings::db_table_infected_main_agenda . '` (`eventId`, `name`, `title`, `description`, `startTime`, `published`)
						 VALUES (\'' . $event->getId() . '\',
							     \'' . $database->real_escape_string($name) . '\',
								 \'' . $database->real_escape_string($title) . '\',
								 \'' . $database->real_escape_string($description) . '\',
								 \'' . $database->real_escape_string($startTime) . '\',
								1);');

		return self::getAgenda($database->insert_id);
	}

	/*
	 * Update an agenda.
	 */
	public static function updateAgenda(Agenda $agenda, string $title, string $description, string $startTime, bool $published) {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$database->query('UPDATE `' . Settings::db_table_infected_main_agenda . '`
						 SET `title` = \'' . $database->real_escape_string($title) . '\',
						  	 `description` = \'' . $database->real_escape_string($description) . '\',
							 `startTime` = \'' . $database->real_escape_string($startTime) . '\',
							 `published` = \'' . $database->real_escape_string($published) . '\'
						 WHERE `id` = ' . $agenda->getId() . ';');
	}

	/*
	 * Remove an agenda entry.
	 */
	public static function removeAgenda(Agenda $agenda) {
		$database = Database::getConnection(Settings::db_name_infected_main);

		$database->query('DELETE FROM `' . Settings::db_table_infected_main_agenda . '`
						 WHERE `id` = ' . $agenda->getId() . ';');
	}
}