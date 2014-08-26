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
									  WHERE `id` = \'' . $con->real_escape_string($id) . '\';');

		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

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
	
	// Returns the event that is closest in time, which means the next or goiong event.
	public static function getCurrentEvent() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_events . '`
									  WHERE `endTime` > NOW()
									  ORDER BY `startTime` ASC
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
	public static function createEvent($theme, $location, $participants, $bookingTime, $startTime, $endTime) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_events . '` (`theme`, `location`, `participants`, `bookingTime`, `startTime`, `endTime`) 
							VALUES (\'' . $con->real_escape_string($theme) . '\', 
									\'' . $con->real_escape_string($location) . '\',
									\'' . $con->real_escape_string($participants) . '\',
									\'' . $con->real_escape_string($bookingTime) . '\', 
									\'' . $con->real_escape_string($startTime) . '\', 
									\'' . $con->real_escape_string($endTime) . '\',
);');
									
		MySQL::close($con);
	}
	
	/* 
	 * Update an event 
	 */
	public static function updateEvent($id, $theme, $location, $participants, $bookingTime, $startTime, $endTime) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_events . '` 
							SET `theme` = \'' . $con->real_escape_string($theme) . '\', 
								`location` = \'' . $con->real_escape_string($location) . '\', 
								`participants` = \'' . $con->real_escape_string($participants) . '\',
								`bookingTime` = \'' . $con->real_escape_string($bookingTime) . '\', 
								`startTime` = \'' . $con->real_escape_string($startTime) . '\', 
								`endTime` = \'' . $con->real_escape_string($endTime) . '\'
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		MySQL::close($con);
	}
	
	/* 
	 * Remove an event
	 */
	public static function removeEvent($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_events . '` 
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		MySQL::close($con);
	}
}
?>
