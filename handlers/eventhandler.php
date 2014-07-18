<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/mysql.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/objects/event.php';

class EventHandler {
	// Get event.
	public static function getEvent($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_events . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new Event($row['id'], $row['theme'], $row['participants'], $row['price'], $row['start'], $row['end'], $row['location']);
		}
	}
	
	// Get the current event, this works so that it takes the last event registred in the database, maybe refactor here and check what date that is shortest from current date.
	public static function getCurrentEvent() {
		$events = self::getEvents();
		
		return end($events);
	}
	
	// Get a list of all events.
	public static function getEvents() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_events);
		
		$eventList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($eventList, self::getEvent($row['id']));
		}
		
		MySQL::close($con);

		return $eventList;
	}
}
?>