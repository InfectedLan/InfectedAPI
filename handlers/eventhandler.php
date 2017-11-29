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
require_once 'objects/event.php';
require_once 'objects/user.php';

class EventHandler {
	/*
	 * Returns the event with the given id.
	 */
	public static function getEvent($id) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_events . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('Event');
	}

	/*
	 * Returns true if we got an event with the given id.
	 */
	public static function hasEvent($id) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_events . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Returns the event after the current event.
	 */
	public static function getNextEvent() {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_events . '`
																WHERE `id` > (SELECT `id` FROM `' . Settings::db_table_infected_events . '`
																						  WHERE `endTime` > NOW()
																						  ORDER BY `startTime` ASC
																						  LIMIT 1)
																ORDER BY `startTime`
																LIMIT 1;');

		return $result->fetch_object('Event');
	}

	/*
	 * Returns the event that is closest in time, which means the next or on-going event.
	 */
	public static function getCurrentEvent() {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_events . '`
																WHERE DATE_ADD(DATE(`endTime`), INTERVAL 1 DAY) >= NOW()
																ORDER BY `startTime`
																LIMIT 1;');

		return $result->fetch_object('Event');
	}

	/*
	 * Returns the event before the current event.
	 */
	public static function getPreviousEvent() {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_events . '`
																WHERE `id` < (SELECT `id` FROM `' . Settings::db_table_infected_events . '`
																						  WHERE `endTime` > NOW()
																						  ORDER BY `startTime`
																						  LIMIT 1)
																ORDER BY `startTime` DESC
																LIMIT 1;');

		return $result->fetch_object('Event');
	}

	/*
	 * Returns a list of all registred events.
	 */
	public static function getEvents() {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_events . '`;');

		$eventList = [];

		while ($object = $result->fetch_object('Event')) {
			$eventList[] = $object;
		}

		return $eventList;
	}

	/*
	 * Returns a list of all registred events.
	 */
	public static function getEventsByYear($year) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_events . '`
																WHERE EXTRACT(YEAR FROM `startTime`) = \'' . $year . '\';');

		$eventList = [];

		while ($object = $result->fetch_object('Event')) {
			$eventList[] = $object;
		}

		return $eventList;
	}

	/*
	 * Create new event
	 */
	public static function createEvent($location, $participants, $bookingTime, $startTime, $endTime) {
		$name = Settings::name . ' ' . Localization::getLocale(date('m', strtotime($startTime)) == 2 ? 'winter' : 'autumn') . ' ' . date('Y', strtotime($startTime));
		$seatmap = SeatmapHandler::createSeatmap($name, null);

		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('INSERT INTO `' . Settings::db_table_infected_events . '` (`locationId`, `participants`, `bookingTime`, `startTime`, `endTime`, `seatmapId`, `ticketTypeId`)
										  VALUES (\'' . $database->real_escape_string($location) . '\',
														  \'' . $database->real_escape_string($participants) . '\',
														  \'' . $database->real_escape_string($bookingTime) . '\',
														  \'' . $database->real_escape_string($startTime) . '\',
														  \'' . $database->real_escape_string($endTime) . '\',
														  \'' . $seatmap->getId() . '\',
														  \'1\');');

		return self::getEvent($database->insert_id);
	}

	/*
	 * Update an event
	 */
	public static function updateEvent(Event $event, $location, $participants, $bookingTime, $prioritySeatingTime, $seatingTime, $startTime, $endTime) {
	  $database = Database::getConnection(Settings::db_name_infected);

		$database->query('UPDATE `' . Settings::db_table_infected_events . '`
										  SET `locationId` = \'' . $database->real_escape_string($location) . '\',
												  `participants` = \'' . $database->real_escape_string($participants) . '\',
												  `bookingTime` = \'' . $database->real_escape_string($bookingTime) . '\',
								  				  `prioritySeatingTime` = \'' . $database->real_escape_string($prioritySeatingTime) . '\',
								  				  `seatingTime` = \'' . $database->real_escape_string($seatingTime) . '\',
												  `startTime` = \'' . $database->real_escape_string($startTime) . '\',
												  `endTime` = \'' . $database->real_escape_string($endTime) . '\'
										  WHERE `id` = \'' . $event->getId() . '\';');
	}

	/*
	 * Remove an event
	 */
	public static function removeEvent(Event $event) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_events . '`
						  				WHERE `id` = \'' . $event->getId() . '\';');
	}

	/*
	 * Returns a list of everyone in a group for the specified event
	 */
	public static function getMembersByEvent(Event $event) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
													LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '` ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`.`userId`
													WHERE `eventId` = ' . $event->getId() . ';');

		$eventList = [];

		while ($object = $result->fetch_object('User')) {
			$eventList[] = $object;
		}

		return $eventList;
	}

	/*
	 * Returns list of people owning tickets for the specified event, that arent in a group
	 */
	public static function getParticipantsByEvent(Event $event) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
							LEFT JOIN `' . Settings::db_name_infected_tickets . '`.`' . Settings::db_table_infected_tickets_tickets . '`
							    ON `' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_tickets_tickets . '`.`userId`
							LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
							    ON (`' . Settings::db_table_infected_users . '`.`id` = `' . Settings::db_table_infected_crew_memberof . '`.`userId`
									    AND `' . Settings::db_table_infected_crew_memberof . '`.`eventId` = ' . $event->getId() . ')
							WHERE `' . Settings::db_table_infected_tickets_tickets . '`.`eventId` = ' . $event->getId() . '
							AND `groupId` IS NULL
							GROUP BY `' . Settings::db_table_infected_users . '`.`id`;');

		$eventList = [];

		while ($object = $result->fetch_object('User')) {
			$eventList[] = $object;
		}

		return $eventList;
	}

	/*
	 * Returns members and participants for given events.
	 */
	public static function getMembersAndParticipantsByEvents(array $eventList, $ageLimit) {
		$database = Database::getConnection(Settings::db_name_infected);

		// Extract event id's from the event list.
		$dateLimit = date('Y-m-d', end($eventList)->getStartTime());
		$eventIdList = [];

		foreach ($eventList as $event) {
			$eventIdList[] = $event->getId();
		}

		$result = $database->query('SELECT * FROM (SELECT `' . Settings::db_table_infected_users . '`.*, `eventId` FROM `' . Settings::db_table_infected_users . '`
																					     LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
																					     ON `' . Settings::db_table_infected_users . '`.`id` = `userId`
																					     WHERE `groupId` IS NOT NULL
																					     UNION ALL
																				   	   SELECT `' . Settings::db_table_infected_users . '`.*, `eventId` FROM `' . Settings::db_table_infected_users . '`
																				   	   LEFT JOIN `' . Settings::db_name_infected_tickets . '`.`' . Settings::db_table_infected_tickets_tickets . '`
																					     ON `' . Settings::db_table_infected_users . '`.`id` = `userId`
																					     WHERE `userId` IS NOT NULL) AS `users`
																WHERE `eventId` IN (\'' . implode(', ', $eventIdList) . '\')
																AND TIMESTAMPDIFF(YEAR, `birthdate`, \'' . $dateLimit . '\') <= \'' . $ageLimit . '\'
																GROUP BY `users`.`id`
																ORDER BY `firstname`;');

		$userList = [];

		while ($object = $result->fetch_object('User')) {
			$userList[] = $object;
		}

		return $userList;
	}
}
?>
