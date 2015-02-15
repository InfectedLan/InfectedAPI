<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/event.php';
require_once 'handlers/userhandler.php';

class EventHandler {
    /*
	 * Returns the event with the given id.
	 */
    public static function getEvent($id) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `'. Settings::db_table_infected_events . '`
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();

		return $result->fetch_object('Event');
    }
    
    /*
	 * Returns the event that is closest in time, which means the next or goiong event.
	 */
    public static function getCurrentEvent() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_events . '`
                                 WHERE `endTime` > NOW()
                                 ORDER BY `startTime` ASC
                                 LIMIT 1;');
		
		$mysql->close();
									  
        return $result->fetch_object('Event');
    }
	
	/*
	 * Returns the event before the current event.
	 */
    public static function getPreviousEvent() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_events . '`
								 WHERE `id` < (SELECT `id` FROM `' . Settings::db_table_infected_events . '`
											   WHERE `endTime` > NOW()
											   ORDER BY `startTime` ASC
											   LIMIT 1)
								 ORDER BY `startTime` DESC
								 LIMIT 1;');
		
		$mysql->close();
		
        return $result->fetch_object('Event');
    }
    
    /*
	 * Returns a list of all registred events.
	 */
    public static function getEvents() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_events . '`;');
        
		$mysql->close();
		
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
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_events . '`
								 WHERE EXTRACT(YEAR FROM `startTime`) = \'' . $year . '\';');
        
		$mysql->close();
		
        $eventList = array();
        
        while ($object = $result->fetch_object('Event')) {
            array_push($eventList, $object);
        }

        return $eventList;
    }
    
    /* 
     * Create new event
     */
    public static function createEvent($theme, $location, $participants, $bookingTime, $startTime, $endTime) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_events . '` (`theme`, `location`, `participants`, `bookingTime`, `startTime`, `endTime`) 
					   VALUES (\'' . $mysql->real_escape_string($theme) . '\', 
							   \'' . $mysql->real_escape_string($location) . '\',
							   \'' . $mysql->real_escape_string($participants) . '\',
							   \'' . $mysql->real_escape_string($bookingTime) . '\', 
							   \'' . $mysql->real_escape_string($startTime) . '\', 
							   \'' . $mysql->real_escape_string($endTime) . '\');');
                                    
        $mysql->close();
    }
    
    /* 
     * Update an event 
     */
    public static function updateEvent($id, $theme, $location, $participants, $bookingTime, $startTime, $endTime) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_events . '` 
					   SET `theme` = \'' . $mysql->real_escape_string($theme) . '\', 
						   `location` = \'' . $mysql->real_escape_string($location) . '\', 
						   `participants` = \'' . $mysql->real_escape_string($participants) . '\',
						   `bookingTime` = \'' . $mysql->real_escape_string($bookingTime) . '\', 
						   `startTime` = \'' . $mysql->real_escape_string($startTime) . '\', 
						   `endTime` = \'' . $mysql->real_escape_string($endTime) . '\'
					   WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
    
    /* 
     * Remove an event
     */
    public static function removeEvent($id) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_events . '` 
                       WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
	
	/*
	 * Returns members and participants for given events.
	 */
	public static function getMembersAndParticipantsForEvents($eventList, $ageLimit) {
		$mysql = MySQL::open(Settings::db_name_infected);
		
		// Extract event id's from the event list.
		$dateLimit = date('Y-m-d', end($eventList)->getStartTime());
		$eventIdList = array();
		
		foreach ($eventList as $event) {
			array_push($eventIdList, '\'' . $event->getId() . '\'');
		}
		
		$result = $mysql->query('SELECT * FROM (SELECT `' . Settings::db_table_infected_users . '`.*, `eventId` FROM `' . Settings::db_table_infected_users . '`
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
		
		$mysql->close();
		
		$userList = array();

        while ($object = $result->fetch_object('User')) {
            array_push($userList, $object);
        }

        return $userList;
	}
	
	/*
	 * Clones members from fromEvent to toEvent, but only if toEvent don't have any members yet (Maybe improve in the future).
	 */
    public static function cloneMembers($fromEvent, $toEvent) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_memberof . '` (`eventId`, `userId`, `groupId`, `teamId`)
					   SELECT \'' . $toEvent->getId() . '\', `userId`, `groupId`, `teamId` FROM `' . Settings::db_table_infected_crew_memberof . '`
					   WHERE `eventId` = \'' . $fromEvent->getId() . '\'
					   AND NOT EXISTS (SELECT `id` FROM `' . Settings::db_table_infected_crew_memberof . '`
									   WHERE `eventId` = \'' . $toEvent->getId() . '\');');
        
        $mysql->close();
    }
}
?>