<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/locationhandler.php';
require_once 'objects/event.php';

class EventHandler {
	// Returns the event with the given id.
	public static function getEvent($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM `'. Settings::db_table_infected_events . '`
									  WHERE `id` = \'' . $id . '\';');

		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new Event($row['id'],
							 $row['theme'], 
							 $row['start'], 
							 $row['end'], 
							 $row['location'], 
							 $row['participants'], 
							 $row['seatmap'],
							 $row['ticketType']);
		}
	}
	
	// Returns the event that is closest in time, which means the next or goiong event.
	public static function getCurrentEvent() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_events . '`
									  WHERE `end` > NOW()
									  ORDER BY `start` ASC
									  LIMIT 1;');

		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return self::getEvent($row['id']);
	}
	
	// Returns a list of all registred events.
	public static function getEvents() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_events . '`;');
		
		$eventList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($eventList, self::getEvent($row['id']));
		}
		
		MySQL::close($con);

		return $eventList;
	}
	
	/* 
	 * Create new event
	 */
	public static function createEvent($theme, $start, $end, $location, $participants) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_events . '` (`theme`, `start`, `end`, `location`, `participants`) 
							VALUES (\'' . $theme . '\', 
									\'' . $start . '\', 
									\'' . $end . '\',
									\'' . $location . '\',
									\'' . $participants . '\');');
									
		MySQL::close($con);
	}
	
	/* 
	 * Update an event 
	 */
	public static function updateEvent($id, $theme, $start, $end, $location, $participants) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_events . '` 
							SET `theme` = \'' . $theme . '\', 
								`start` = \'' . $start . '\', 
								`end` = \'' . $end . '\', 
								`location` = \'' . $location . '\', 
								`participants` = \'' . $participants . '\'
							WHERE `id` = \'' . $id . '\';');
		
		MySQL::close($con);
	}
	
	/* 
	 * Remove an event
	 */
	public static function removeEvent($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_events . '` 
							WHERE `id` = \'' . $id . '\';');
		
		MySQL::close($con);
	}
}
?>
