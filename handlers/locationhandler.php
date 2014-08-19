<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/location.php';

class LocationHandler {
	// Returns the location with the given id.
	public static function getLocation($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM `'. Settings::db_table_infected_locations . '`
									  WHERE `id` = \'' . $con->real_escape_string($id) . '\';');

		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new Location($row['id'],
								$row['name'],
								$row['title']);
		}
	}
	
	// Returns a list of all locations.
	public static function getLocations() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_locations . '`;');
		
		$locationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($locationList, self::getLocation($row['id']));
		}
		
		MySQL::close($con);

		return $locationList;
	}
}
?>
