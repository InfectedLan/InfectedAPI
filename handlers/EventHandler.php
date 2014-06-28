<?php
require_once '/../Settings.php';
require_once '/../MySQL.php';
require_once '/../objects/Event.php';

	class EventHandler {
		// Get event.
		public static function getEvent($id) {
			$con = MySQL::open(Settings::db_name_infected);
			
			$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_events . ' WHERE id=\'' . $id . '\'');
			$row = mysqli_fetch_array($result);
			
			MySQL::close($con);

			if ($row) {
				return new Event($row['id'], $row['theme'], $row['participants'], $row['price'], $row['start'], $row['end']);
			}
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