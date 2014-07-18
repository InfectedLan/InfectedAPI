<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/mysql.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/handlers/seathandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/handlers/eventhandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/objects/row.php';
	
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