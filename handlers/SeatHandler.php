<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/Settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/MySQL.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/handlers/RowHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/objects/Ticket.php';

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