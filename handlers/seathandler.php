<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/rowhandler.php';
require_once 'objects/ticket.php';
require_once 'objects/seat.php';

class SeatHandler {
	public static function getSeat($id) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_tickets_seats . '` 
									  WHERE `id` = \'' . $id . '\';');
									  
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if ($row) {
			return new Seat($row['id'], 
							$row['section'], 
							$row['number']);
		}
	}

	/*
	 * Returns a string representation of the seat
	 */
	public static function getHumanString($seat) {
		$row = RowHandler::getRow($seat->getRow());
		return 'R' . $row->getNumber() . ' S' . $seat->getNumber();
	}
}
?>