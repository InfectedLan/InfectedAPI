<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/checkinstate.php';

class CheckinStateHandler {
	public static function getCheckinState($id) {
		$con = MySQL::open(Settings::db_name_infected_tickets);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_tickets_checkinstate . '` 
									  WHERE `id` = \'' . $id . '\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return new CheckinState($row['id'], 
									$row['ticketId'],
									$row['userId']);
		}
	}

	public static function isCheckedIn($ticket) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_tickets_checkinstate . '` 
									  WHERE `ticketId` = ' . $con->real_escape_string($ticket->getId()) . ';');

		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		return $row ? true : false;
	}

	public static function checkIn($ticket) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$reuslt = mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_tickets_checkinstate . '` (`ticketId`) 
									  VALUES (\'' . $con->real_escape_string($ticket->getId()) . '\');');

		MySQL::close($con);
	}
}
?>