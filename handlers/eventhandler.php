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
require_once 'objects/event.php';
require_once 'objects/user.php';

class EventHandler {
    /*
	 * Returns the event with the given id.
	 */
    public static function getEvent($id) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `'. Settings::db_table_infected_events . '`
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();

		return $result->fetch_object('Event');
    }
    
    /*
	 * Returns the event after the current event.
	 */
    public static function getNextEvent() {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_events . '`
								    WHERE `id` > (SELECT `id` FROM `' . Settings::db_table_infected_events . '`
											      WHERE `endTime` > NOW()
											      ORDER BY `startTime` ASC
											      LIMIT 1)
								    ORDER BY `startTime` ASC
								    LIMIT 1;');
		
		$database->close();
		
        return $result->fetch_object('Event');
    }

    /*
	 * Returns the event that is closest in time, which means the next or goiong event.
	 */
    public static function getCurrentEvent() {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_events . '`
                                    WHERE endTime` >= NOW()
                                    ORDER BY `startTime` ASC
                                    LIMIT 1;');
		
		$database->close();
									  
        return $result->fetch_object('Event');
    }
	
	/*
	 * Returns the event before the current event.
	 */
    public static function getPreviousEvent() {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_events . '`
								    WHERE `id` < (SELECT `id` FROM `' . Settings::db_table_infected_events . '`
											      WHERE `endTime` > NOW()
											      ORDER BY `startTime` ASC
											      LIMIT 1)
								    ORDER BY `startTime` DESC
								    LIMIT 1;');
		
		$database->close();
		
        return $result->fetch_object('Event');
    }
    
    /*
	 * Returns a list of all registred events.
	 */
    public static function getEvents() {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_events . '`;');
        
		$database->close();
		
        $eventList = array();
        
        while ($object = $result->fetch_object('Event')) {
            array_push($eventList, $object);
        }

        return $eventList;
    }
	
	/*
	 * Returns a list of all registred events.
	 */
    public static function getEventsByYear($year) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_events . '`
								    WHERE EXTRACT(YEAR FROM `startTime`) = \'' . $year . '\';');
        
		$database->close();
		
        $eventList = array();
        
        while ($object = $result->fetch_object('Event')) {
            array_push($eventList, $object);
        }

        return $eventList;
    }
    
    /* 
     * Create new event
     */
    public static function createEvent($location, $participants, $bookingTime, $startTime, $endTime) {
    	$name = Settings::name . ' ' . (date('m', strtotime($startTime)) == 2 ? 'Vinter' : 'Høst') . ' ' . date('Y', strtotime($startTime));    
        $seatmap = SeatmapHandler::createSeatmap($name, null);
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('INSERT INTO `' . Settings::db_table_infected_events . '` (`locationId`, `participants`, `bookingTime`, `startTime`, `endTime`, `seatmapId`, `ticketTypeId`) 
					      VALUES (\'' . $database->real_escape_string($location) . '\',
							      \'' . $database->real_escape_string($participants) . '\',
							      \'' . $database->real_escape_string($bookingTime) . '\', 
							      \'' . $database->real_escape_string($startTime) . '\', 
							      \'' . $database->real_escape_string($endTime) . '\',
							      \'' . $seatmap->getId() . '\',
							      \'1\');');
        
        $database->close();
    }
    
    /* 
     * Update an event 
     */
    public static function updateEvent(Event $event, $location, $participants, $bookingTime, $startTime, $endTime) {
      	$database = Database::open(Settings::db_name_infected);

        $database->query('UPDATE `' . Settings::db_table_infected_events . '` 
					      SET `locationId` = \'' . $database->real_escape_string($location) . '\', 
						      `participants` = \'' . $database->real_escape_string($participants) . '\',
						      `bookingTime` = \'' . $database->real_escape_string($bookingTime) . '\', 
						      `startTime` = \'' . $database->real_escape_string($startTime) . '\', 
						      `endTime` = \'' . $database->real_escape_string($endTime) . '\'
					      WHERE `id` = \'' . $event->getId() . '\';');
        
        $database->close();
    }
    
    /* 
     * Remove an event
     */
    public static function removeEvent(Event $event) {
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_events . '` 
                          WHERE `id` = \'' . $event->getId() . '\';');
        
        $database->close();
    }
	
	/*
	 * Returns members and participants for given events.
	 */
	public static function getMembersAndParticipantsByEvents(array $eventList, $ageLimit) {
		$database = Database::open(Settings::db_name_infected);
		
		// Extract event id's from the event list.
		$dateLimit = date('Y-m-d', end($eventList)->getStartTime());
		$eventIdList = array();
		
		foreach ($eventList as $event) {
			array_push($eventIdList, '\'' . $event->getId() . '\'');
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
								    WHERE `eventId` IN (' . implode(', ', $eventIdList) . ')
								    AND TIMESTAMPDIFF(YEAR, `birthdate`, \'' . $dateLimit . '\') <= \'' . $ageLimit . '\'
								    GROUP BY `users`.`id`
								    ORDER BY `firstname` ASC;');
		
		$database->close();
		
		$userList = array();

        while ($object = $result->fetch_object('User')) {
            array_push($userList, $object);
        }

        return $userList;
	}
}
?>