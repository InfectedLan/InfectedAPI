<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/Settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/MySQL.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/objects/TicketType.php';

class TicketTypeHandler {
	public static function getTicketType($id)
	{
		$con = MySQL::open(Settings::db_name_tickets);

		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_ticketTypes . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return new TicketType($row['id'], $row['humanName']);
		}
	}
}
?>