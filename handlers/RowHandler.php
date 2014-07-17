<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/Settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/MySQL.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/handlers/SeatHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/handlers/EventHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/objects/Row.php';
	
class RowHandler {
	public static function getRow($id) {
		$con = MySQL::open(Settings::db_name_tickets);

		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_sections . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row)
		{
			return new Row($row['id'], $row['x'], $row['y'], $row['row'], EventHandler::getEvent($row['event']));
		}
	}
}
?>