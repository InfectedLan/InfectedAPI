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
require_once 'objects/user.php';
require_once 'objects/event.php';

class UserHistoryHandler {
	/*
	 * Get a list of events that the given user has history with.
   */
	public static function getParticipatedEvents(User $user): array {
	  $database = Database::getConnection(Settings::db_name_infected);
	  $result = $database->query('SELECT * FROM (SELECT `' . DatabaseConstants::db_table_infected_events . '`.* FROM `' . DatabaseConstants::db_table_infected_events . '`
				 WHERE `' . DatabaseConstants::db_table_infected_events . '`.`id` IN (SELECT `eventId` FROM `' . Settings::db_name_infected_crew . '`.`' . DatabaseConstants::db_table_infected_crew_memberof . '`
																						 												 WHERE `userId` = \'' . $user->getId() . '\')
				 UNION ALL
				 SELECT `' . DatabaseConstants::db_table_infected_events . '`.* FROM `' . DatabaseConstants::db_table_infected_events . '`
				 WHERE `' . DatabaseConstants::db_table_infected_events . '`.`id` IN (SELECT `eventId` FROM `' . Settings::db_name_infected_tickets . '`.`' . DatabaseConstants::db_table_infected_tickets_tickets . '`
																						 												 WHERE `userId` = \'' . $user->getId() . '\')
				 ) AS `' . DatabaseConstants::db_table_infected_events . '`
				 GROUP BY `' . DatabaseConstants::db_table_infected_events . '`.`id`;');

	  $eventList = [];

		while ($object = $result->fetch_object('Event')) {
			$eventList[] = $object;
		}

	  return $eventList;
	}
}
?>
