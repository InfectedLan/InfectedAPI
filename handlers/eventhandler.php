<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/event.php';

class EventHandler {
    /*
	 * Returns the event with the given id.
	 */
    public static function getEvent($id) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `'. Settings::db_table_infected_events . '`
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();

		$row = $result->fetch_array();
		
        if ($row) {
            return new Event($row['id'],
                             $row['theme'], 
                             $row['location'], 
                             $row['participants'], 
                             $row['bookingTime'], 
                             $row['startTime'], 
                             $row['endTime'], 
                             $row['seatmap'],
                             $row['ticketType']);
        }
    }
    
    /*
	 * Returns the event that is closest in time, which means the next or goiong event.
	 */
    public static function getCurrentEvent() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_events . '`
                                 WHERE `endTime` > NOW()
                                 ORDER BY `startTime` ASC
                                 LIMIT 1;');
		
		$mysql->close();
									  
        $row = $result->fetch_array();
        
        return self::getEvent($row['id']);
    }
	
	/*
	 * Returns the event before the current event.
	 */
    public static function getPreviousEvent() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_events . '`
								 WHERE `id` < (SELECT `id` FROM `' . Settings::db_table_infected_events . '`
											   WHERE `endTime` > NOW()
											   ORDER BY `startTime` ASC
											   LIMIT 1)
								 ORDER BY `startTime` DESC
								 LIMIT 1;');
		
		$mysql->close();
		
        $row = $result->fetch_array();
        
        return self::getEvent($row['id']);
    }
    
    /*
	 * Returns a list of all registred events.
	 */
    public static function getEvents() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_events . '`;');
        
		$mysql->close();
		
        $eventList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($eventList, self::getEvent($row['id']));
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
	 * Clones members from fromEvent to toEvent.
	 */
    public static function cloneMembers($fromEvent, $toEvent) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_memberof . '` (`eventId`, `userId`, `groupId`, `teamId`)
					   SELECT \'' . $toEvent->getId() . '\', `userId`, `groupId`, `teamId` FROM `' . Settings::db_table_infected_crew_memberof . '`
					   WHERE `eventId` = \'' . $fromEvent->getId() . '\';');
        
        $mysql->close();
    }
}
?>
