<?php
require_once '/../Settings.php';
require_once '/../MySQL.php';
require_once '/../objects/Section.php';
require_once 'SeatHandler.php';
require_once 'EventHandler.php';
require_once '/../objects/Row.php';
	
	class RowHandler {
		public static function getRow($id)
		{
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