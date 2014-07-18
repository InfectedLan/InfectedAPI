<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/rowhandler.php';
require_once 'objects/ticket.php';

class SeatHandler {
	public static function getSeat($id) {
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