<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/seat.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/rowhandler.php';

class SeatHandler {
	public static function getSeat($id) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_tickets_seats . '` 
									  WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
									  
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if ($row) {
			return new Seat($row['id'], 
							$row['rowId'], 
							$row['number']);
		}
	}

	/*
	 * Returns a string representation of the seat
	 */
	public static function getHumanString($seat) {
		$row = $seat->getRow();
		return 'R' . $row->getNumber() . ' S' . $seat->getNumber();
	}

	public static function deleteSeat($seat) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_tickets_seats . '` 
									  WHERE `id` = ' . $con->real_escape_string($seat->getId()) . ';');

		MySQL::close($con);
	}

	public static function hasOwner($seat) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
									  WHERE `seatId` = ' . $con->real_escape_string($seat->getId()) . ';');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		return $row ? true : false;
	}

	public static function getOwner($seat) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `ownerId` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
									  WHERE `seatId` = ' . $con->real_escape_string($seat->getId()) . ';');
		
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return UserHandler::getUser($row['ownerId']);
		}		
	}

	public static function getTicket($seat) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
									  WHERE `seatId` = ' . $con->real_escape_string($seat->getId()) . ';');
		
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if ($row) {
			return TicketHandler::getTicket($row['id']);
		}		
	}

	public static function getEvent($seat) {
		return RowHandler::getEvent($seat->getRow());
	}
}
?>