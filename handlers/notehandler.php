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
require_once 'objects/note.php';
require_once 'objects/event.php';

class NoteHandler {
	/*
	 * Return the note by the internal id.
	 */
	public static function getNote($id) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_notes . '`
																WHERE id = \'' . $database->real_escape_string($id) . '\';');

		$database->close();

		return $result->fetch_object('Note');
	}

	/*
	 * Returns a list of all notes by the specified event.
	 */
	public static function getNotesByEvent(Event $event) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_notes . '`
																WHERE `eventId` = \'' . $event->getId() . '\';');

		$database->close();

		$noteList = array();

		while ($object = $result->fetch_object('Note')) {
			array_push($noteList, $object);
		}

		return $noteList;
	}

	/*
	 * Returns a list of all notes.
	 */
	public static function getNotes() {
		return self::getNotesByEvent(EventHandler::getCurrentEvent());
	}

	/*
	 * Returns a list of all notes by the specified event.
	 */
	public static function getNotesByGroupAndEvent(Group $group, Event $event) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_notes . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `groupId` = \'' . $group->getId() . '\';');

		$database->close();

		$noteList = array();

		while ($object = $result->fetch_object('Note')) {
			array_push($noteList, $object);
		}

		return $noteList;
	}

	/*
	 * Returns a list of all notes by group.
	 */
	public static function getNotesByGroup(Group $group) {
		return self::getNotesByGroupAndEvent($group, EventHandler::getCurrentEvent());
	}

	/*
	 * Returns a list of all notes by group for a specified event.
	 */
	public static function getNotesByTeamAndEvent(Team $team, Event $event) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_notes . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `groupId` = \'' . $team->getGroup()->getId() . '\'
																AND `teamId` = \'' . $team->getId() . '\';');

		$database->close();

		$noteList = array();

		while ($object = $result->fetch_object('Note')) {
			array_push($noteList, $object);
		}

		return $noteList;
	}

	/*
	 * Returns a list of all notes by team.
	 */
	public static function getNotesByTeam(Team $team) {
		return self::getNotesByTeamAndEvent($team, EventHandler::getCurrentEvent());
	}

	/*
	 * Create a new note.
	 */
	public static function createNote(Group $group = null, Team $team = null, User $user = null, $content, $done) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('INSERT INTO `' . Settings::db_table_infected_crew_notes . '` (`eventId`, `groupId`, `teamId`, `userId`, `content`, `deadlineTime`, `done`)
										  VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\',
															\'' . ($group != null ? $group->getId() : 0) . '\',
															\'' . ($team != null ? $team->getId() : 0) . '\',
															\'' . ($user != null ? $user->getId() : 0) . '\',
															\'' . $database->real_escape_string($content) . '\',
															\'' . date('Y-m-d H:i:s') . '\',
															\'' . $database->real_escape_string($done) . '\')');

		$database->close();
	}

	/*
	 * Update a note.
	 */
	public static function updateNote(Note $note, User $user = null, $content, $done) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_notes . '`
										  SET `userId` = \'' . ($user != null ? $user->getId() : 0) . '\',
													`content` = \'' . $database->real_escape_string($content) . '\',
													`done` = \'' . $database->real_escape_string($done) . '\'
										  WHERE `id` = \'' . $note->getId() . '\';');

		$database->close();
	}

	/*
	 * Remove a note.
	 */
	public static function removeNote(Note $note) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_notes . '`
						  				WHERE `id` = \'' . $note->getId() . '\';');

		$database->close();
	}
}
?>
