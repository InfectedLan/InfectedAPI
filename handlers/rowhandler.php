<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/row.php';
require_once 'handlers/seathandler.php';
	
class RowHandler {
	public static function getRow($id) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * 
									  FROM `' . Settings::db_table_infected_tickets_rows . '`
									  WHERE `id` = \'' . $id . '\';');
									  
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if ($row) {
			return new Row($row['id'], 
						   $row['x'], 
						   $row['y'], 
						   $row['row'], 
						   $row['seatmap']);
		}
	}

	public static function getSeats($row) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` 
									  FROM `' . Settings::db_table_infected_tickets_seats . '` 
									  WHERE `section` = \'' . $row->getId() . '\';');

		$seatArray = array();

		while ($seat = mysqli_fetch_array($result)) {
			array_push($seatArray, SeatHandler::getSeat($seat['id']));
		}

		return $seatArray;
	}

	public static function createNewRow($seatmapId, $x, $y) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		mysqli_query($con, 'INSERT INTO ' . Settings::db_table_infected_tickets_rows . '(`seatmap`, `x`, `y`) VALUES (\'' . $seatmapId . '\', ' . $x . ', ' . $y . ')');

		$result = mysqli_query($con, 'SELECT id FROM ' .  Settings::db_table_infected_tickets_rows . ' ORDER BY id DESC LIMIT 1;');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row)
		{
			return self::getRow($row['id']);
		}

	}
}
?>