<?php
require_once '/../Settings.php';
require_once '/../MySQL.php';
require_once '/../objects/Ticket.php';
require_once 'RowHandler.php';

class SeatHandler {
	public static function getSeat($id)
	{
		$con = MySQL::open(Settings::db_name_tickets);

		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_seats . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return new Seat($row['id'], RowHandler::getRow($row['row']), $row['number']);
		}
	}
}
?>