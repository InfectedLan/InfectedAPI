<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/mysql.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/handlers/rowhandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/objects/ticket.php';

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