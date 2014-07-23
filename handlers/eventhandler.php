<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/locationhandler.php';
require_once 'objects/event.php';

class EventHandler {
	// Returns the event with the given id.
	public static function getEvent($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * 
					      FROM `'. Settings::db_table_infected_events . '`
					      WHERE `id` = \'' . $id . '\';');

		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new Event($row['id'],
					 $row['theme'], 
					 $row['start'], 
					 $row['end'], 
					 LocationHandler::getLocation($row['location']), 
					 $row['participants'], 
					 $row['price']);
		}
	}
	
	// Returns the event that is closest in time, which means the next or goiong event.
	public static function getCurrentEvent() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id`
					      FROM `' . Settings::db_table_infected_events . '`
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
		
		$result = mysqli_query($con, 'SELECT `id` 
					      FROM `' . Settings::db_table_infected_events . '`;');
		
		$eventList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($eventList, self::getEvent($row['id']));
		}
		
		MySQL::close($con);

		return $eventList;
	}
}
?>
