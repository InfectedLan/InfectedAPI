<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/seathandler.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/row.php';
	
class RowHandler {
	public static function getRow($id) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_infected_tickets_sections . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return new Row($row['id'], $row['x'], $row['y'], $row['row'], $row['seatmap']);
		}
	}
}
?>