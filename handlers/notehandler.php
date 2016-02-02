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
require_once 'utils/userutils.php';

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
																WHERE `eventId` = \'' . $event->getId() . '\'
																ORDER BY `secondsOffset`, `time`;');

		$database->close();

		$noteList = [];

		while ($object = $result->fetch_object('Note')) {
			$noteList[] = $object;
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
																AND `done` = \'0\'
																AND `notified` = \'0\'
																AND DATE_SUB(FROM_UNIXTIME(' . $event->getStartTime() . ' + `secondsOffset`), INTERVAL 3 DAY) <= NOW()
																ORDER BY `secondsOffset`, `time`;');

		$database->close();

		$noteList = [];

		while ($object = $result->fetch_object('Note')) {
			$noteList[] = $object;
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
																AND `groupId` = \'' . $group->getId() . '\'
																ORDER BY `secondsOffset`, `time`;');

		$database->close();

		$noteList = [];

		while ($object = $result->fetch_object('Note')) {
			$noteList[] = $object;
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
																AND `teamId` = \'' . $team->getId() . '\'
																ORDER BY `secondsOffset`, `time`;');

		$database->close();

		$noteList = [];

		while ($object = $result->fetch_object('Note')) {
			$noteList[] = $object;
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
																AND (`groupId` = \'0\'
																		 AND `teamId` = \'0\'
																		 AND `userId` = \'' . $user->getId() . '\')
																ORDER BY `secondsOffset`, `time`;');

		$database->close();

		$noteList = [];

		while ($object = $result->fetch_object('Note')) {
			$noteList[] = $object;
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

		$leaderInGroups = [];

		foreach (GroupHandler::getGroups() as $group) {
			if ($group->isMember($user) ||
				$group->isLeader($user) ||
				$group->isCoLeader($user)) {
			  $leaderInGroups[] = $group->getId();
			}
		}

		if ($user->isGroupLeader() || $user->isGroupCoLeader()) {
			$result = $database->query('SELECT DISTINCT `' . Settings::db_table_infected_crew_notes . '`.* FROM `' . Settings::db_table_infected_crew_notes . '`
																	LEFT JOIN `' . Settings::db_table_infected_crew_notewatches . '`
																	ON `' . Settings::db_table_infected_crew_notes . '`.`id` = `' . Settings::db_table_infected_crew_notewatches . '`.`noteId`
																	WHERE `eventId` = \'' . $event->getId() . '\'
																	AND (`groupId` IN (' . implode(',', $leaderInGroups) . ')
																				OR (`groupId` != \'0\'
																	    			AND `' . Settings::db_table_infected_crew_notes . '`.`userId` = \'' . $user->getId() . '\')
																				OR `' . Settings::db_table_infected_crew_notewatches . '`.`userId` = \'' . $user->getId() . '\')
																	ORDER BY `secondsOffset`, `time`;');

		} else if ($user->isTeamMember() && $user->isTeamLeader()) {
			$result = $database->query('SELECT DISTINCT `' . Settings::db_table_infected_crew_notes . '`.* FROM `' . Settings::db_table_infected_crew_notes . '`
																	LEFT JOIN `' . Settings::db_table_infected_crew_notewatches . '`
																	ON `' . Settings::db_table_infected_crew_notes . '`.`id` = `' . Settings::db_table_infected_crew_notewatches . '`.`noteId`
																	WHERE `eventId` = \'' . $event->getId() . '\'
																	AND ((`groupId` != \'0\'
	 			 																AND `teamId` = \'' . $user->getTeam()->getId() . '\')
																				OR (`groupId` != \'0\'
		 																	 			AND `' . Settings::db_table_infected_crew_notes . '`.`userId` = \'' . $user->getId() . '\')
																				OR `' . Settings::db_table_infected_crew_notewatches . '`.`userId` = \'' . $user->getId() . '\')
																	ORDER BY `secondsOffset`, `time`;');
		} else {
			$result = $database->query('SELECT DISTINCT `' . Settings::db_table_infected_crew_notes . '`.* FROM `' . Settings::db_table_infected_crew_notes . '`
																	LEFT JOIN `' . Settings::db_table_infected_crew_notewatches . '`
																	ON `' . Settings::db_table_infected_crew_notes . '`.`id` = `' . Settings::db_table_infected_crew_notewatches . '`.`noteId`
																	WHERE `eventId` = \'' . $event->getId() . '\'
																	AND ((`groupId` != \'0\'
																				AND `' . Settings::db_table_infected_crew_notes . '`.`userId` = \'' . $user->getId() . '\')
																				OR `' . Settings::db_table_infected_crew_notewatches . '`.`userId` = \'' . $user->getId() . '\')
		 															ORDER BY `secondsOffset`, `time`;');
		}

		$database->close();

		$noteList = [];

		while ($object = $result->fetch_object('Note')) {
			$noteList[] = $object;
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
	public static function createNote(User $creatorUser = null, Group $group = null, Team $team = null, User $user = null, $title, $content, $secondsOffset = 0, $time = null) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('INSERT INTO `' . Settings::db_table_infected_crew_notes . '` (`eventId`, `creatorId`, `groupId`, `teamId`, `userId`, `title`, `content`, `secondsOffset`, `time`)
										  VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\',
															\'' . ($creatorUser != null ? $creatorUser->getId() : 0) . '\',
															\'' . ($group != null ? $group->getId() : 0) . '\',
															\'' . ($team != null ? $team->getId() : 0) . '\',
															\'' . ($user != null ? $user->getId() : 0) . '\',
															\'' . $database->real_escape_string($title) . '\',
															\'' . $database->real_escape_string($content) . '\',
															\'' . $database->real_escape_string($secondsOffset) . '\',
															\'' . $database->real_escape_string($time) . '\');');

		$note = self::getNote($database->insert_id);

		$database->close();

		return $note;
	}

	/*
	 * Update a note.
	 */
	public static function updateNote(Note $note, Group $group = null, Team $team = null, User $user = null, $title, $content, $secondsOffset = 0, $time = null, $notified = 0) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_notes . '`
										  SET `groupId` = \'' . ($group != null ? $group->getId() : 0) . '\',
													`teamId` = \'' . ($team != null ? $team->getId() : 0) . '\',
													`userId` = \'' . ($user != null ? $user->getId() : 0) . '\',
													`title` = \'' . $database->real_escape_string($title) . '\',
													`content` = \'' . $database->real_escape_string($content) . '\',
													`secondsOffset` = \'' . $database->real_escape_string($secondsOffset) . '\',
													`time` = \'' . $database->real_escape_string($time) . '\',
													`notified` = \'' . $database->real_escape_string($notified) . '\'
										  WHERE `id` = \'' . $note->getId() . '\';');

		$database->close();
	}

	/*
	 * Update a notes notified state.
	 */
	public static function updateNoteNotified(Note $note, $notified) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_notes . '`
											SET `notified` = \'' . $database->real_escape_string($notified) . '\'
											WHERE `id` = \'' . $note->getId() . '\';');

		$database->close();
	}

	/*
	 * Update a notes done state.
	 */
	public static function updateNoteDone(Note $note, $done) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_notes . '`
											SET `done` = \'' . $database->real_escape_string($done) . '\',
													`inProgress` = \'0\'
											WHERE `id` = \'' . $note->getId() . '\';');

		$database->close();
	}

	/*
	 * Update a notes in progress state.
	 */
	public static function updateNoteInProgress(Note $note, $inProgress) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_notes . '`
											SET `done` = \'0\',
													`inProgress` = \'' . $database->real_escape_string($inProgress) . '\'
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

	/* Notes watchlist */
	/*
	 * Returns true if this user has a option.
	 */
	public static function isWatchingNote(Note $note, User $user) {
		$database = Database::open(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_notewatches . '`
																WHERE `noteId` = \'' . $note->getId() . '\'
																AND `userId` = \'' . $user->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Returns a list of all users watching the specified note.
	 */
	public static function getWatchingUsers(Note $note) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE `id` IN (SELECT `userId` FROM `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_notewatches . '`
																							 WHERE `noteId` = \'' . $note->getId() . '\')
																ORDER BY `firstname`, `lastname`;');

		$database->close();

		$userList = [];

		while ($object = $result->fetch_object('User')) {
			$userList[] = $object;
		}

		return $userList;
	}

	/*
	 * Watch a note.
	 */
	public static function watchNote(Note $note, User $user) {
		$database = Database::open(Settings::db_name_infected_crew);

		if (!self::isWatchingNote($note, $user)) {
			$database->query('INSERT INTO `' . Settings::db_table_infected_crew_notewatches . '` (`noteId`, `userId`)
											  VALUES (\'' . $note->getId() . '\',
																\'' . $user->getId() . '\');');
		}

		$database->close();
	}

	/*
	 * Unwatch a note.
	 */
	public static function unwatchNote(Note $note, User $user) {
		$database = Database::open(Settings::db_name_infected_crew);

		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_notewatches . '`
						  				WHERE `noteId` = \'' . $note->getId() . '\'
											AND `userId` = \'' . $user->getId() . '\';');

		$database->close();
	}

	/*
	 * Update watching users of a note.
	 */
	public static function updateWatchingUsers(Note $note, array $userList) {
		$database = Database::open(Settings::db_name_infected_crew);

		// Add watching users that's not already watching.
		foreach ($userList as $user) {
			if (!self::isWatchingNote($note, $user)) {
				$database->query('INSERT INTO `' . Settings::db_table_infected_crew_notewatches . '` (`noteId`, `userId`)
													VALUES (\'' . $note->getId() . '\',
																	\'' . $user->getId() . '\');');
		  }
		}

		// Remove all users that was watching before, but is not watching anymore.
		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_notewatches . '`
											WHERE `noteId` = \'' . $note->getId() . '\'
											AND `userId` NOT IN (\'' . implode('\', \'', UserUtils::toUserIdList($userList)) . '\');');

		$database->close();
	}
}
?>
