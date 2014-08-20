<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/entrance.php';

class EntranceHandler {
	public static function getEntrance($id) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_tickets_entrances . '` 
									  WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
									
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if ($row) {
			return new Entrance($row['id'],
							    $row['name'], 
							    $row['title']);
		}
	}
	
	public static function getEntrances() {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_tickets_entrance . '`;');

		$entranceList = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($entranceList, self::getEntrance($row['id']));
		}

		MySQL::close($con);

		return $entranceList;
	}
}
?>