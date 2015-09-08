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
	 * Returns a list of all notes that has reached the notification time, by event.
	 */
	public static function getNotesReachedNotificationTimeByEvent(Event $event) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_notes . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `notify` = \'0\'
																AND FROM_UNIXTIME(' . $event->getStartTime() . ' - `secondsOffset` - 604800) <= NOW();');

		$database->close();

		$noteList = array();

		while ($object = $result->fetch_object('Note')) {
			array_push($noteList, $object);
		}

		return $noteList;
	}

	/*
	 * Returns a list of all notes that has reached the notification time.
	 */
	public static function getNotesReachedNotificationTime() {
		return self::getNotesReachedNotificationTimeByEvent(EventHandler::getCurrentEvent());
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
	 * Returns a list of all notes by user for a specified event.
	 */
	public static function getNotesByUserAndEvent(User $user, Event $event) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_notes . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `groupId` = \'0\'
																AND `teamId` = \'0\'
																AND `userId` = \'' . $user->getId() . '\';');

		$database->close();

		$noteList = array();

		while ($object = $result->fetch_object('Note')) {
			array_push($noteList, $object);
		}

		return $noteList;
	}

	/*
	 * Returns a list of all notes by user.
	 */
	public static function getNotesByUser(User $user) {
		return self::getNotesByUserAndEvent($user, EventHandler::getCurrentEvent());
	}

	/*
	 * Returns a list of all notes by the specified event.
	 */
	public static function getNotesByGroupAndTeamAndUserAndEvent(User $user, Event $event) {
		$database = Database::open(Settings::db_name_infected_crew);

		if ($user->isGroupLeader() || $user->isGroupCoLeader()) {
			$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_notes . '`
																	WHERE `eventId` = \'' . $event->getId() . '\'
																	AND `groupId` = \'' . $user->getGroup()->getId() . '\';');


		} else if ($user->isTeamMember() && $user->isTeamLeader()) {
			$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_notes . '`
																	WHERE `eventId` = \'' . $event->getId() . '\'
																	AND `groupId` = \'' . $user->getGroup()->getId() . '\'
																	AND ((`teamId` = \'' . $user->getTeam()->getId() . '\') OR
																			 (`teamId` = \'0\' AND `userId` = \'' . $user->getId() . '\'));');
		} else {
			$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_notes . '`
																	WHERE `eventId` = \'' . $event->getId() . '\'
																	AND `groupId` = \'' . $user->getGroup()->getId() . '\'
																	AND `userId` = \'' . $user->getId() . '\';');
		}

		$database->close();

		$noteList = array();

		while ($object = $result->fetch_object('Note')) {
			array_push($noteList, $object);
		}

		return $noteList;
	}

	/*
	 * Returns a list of all notes by user.
	 */
	public static function getNotesByGroupAndTeamAndUser(User $user) {
		return self::getNotesByGroupAndTeamAndUserAndEvent($user, EventHandler::getCurrentEvent());
	}

	/*
	 * Create a new note.
	 */
	public static function createNote(Group $group = null, Team $team = null, User $user = null, $title, $content, $secondsOffset, $time = null, $done = 0) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('INSERT INTO `' . Settings::db_table_infected_crew_notes . '` (`eventId`, `groupId`, `teamId`, `userId`, `title`, `content`, `secondsOffset`, `time`, `done`)
										  VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\',
															\'' . ($group != null ? $group->getId() : 0) . '\',
															\'' . ($team != null ? $team->getId() : 0) . '\',
															\'' . ($user != null ? $user->getId() : 0) . '\',
															\'' . $database->real_escape_string($title) . '\',
															\'' . $database->real_escape_string($content) . '\',
															\'' . $database->real_escape_string($secondsOffset) . '\',
															\'' . $database->real_escape_string($deadlineTime) . '\',
															\'' . $database->real_escape_string($done) . '\')');

		$database->close();
	}

	/*
	 * Update a note.
	 */
	public static function updateNote(Note $note, Team $team = null, User $user = null, $title, $content, $secondsOffset, $time = null, $notify = 0, $done = 0) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_notes . '`
										  SET `teamId` = \'' . ($team != null ? $team->getId() : 0) . '\',
													`userId` = \'' . ($user != null ? $user->getId() : 0) . '\',
													`title` = \'' . $database->real_escape_string($title) . '\',
													`content` = \'' . $database->real_escape_string($content) . '\',
													`secondsOffset` = \'' . $database->real_escape_string($secondsOffset) . '\',
													`time` = \'' . $database->real_escape_string($time) . '\',
													`notify` = \'' . $database->real_escape_string($notify) . '\',
													`done` = \'' . $database->real_escape_string($done) . '\'
										  WHERE `id` = \'' . $note->getId() . '\';');

		$database->close();
	}

	/*
	 * Update a notes notified state.
	 */
	public static function updateNoteNotified(Note $note, $notified) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_notes . '`
											SET `notify` = \'' . $database->real_escape_string($notified) . '\'
											WHERE `id` = \'' . $note->getId() . '\'
											AND `type` = \'0\';');

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
