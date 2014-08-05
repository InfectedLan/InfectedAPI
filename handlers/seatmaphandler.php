<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/rowhandler.php';
require_once 'objects/seatmap.php';

class SeatmapHandler {
	public static function getSeatmap($id) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * 
									  FROM `' . Settings::db_table_infected_tickets_seatmaps . '` 
									  WHERE `id` = \'' . $id . '\';');
									  
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if ($row) {
			return new Seatmap($row['id'], 
							   $row['humanName'],
							   $row['backgroundImage']);
		}
	}

	public static function createNewSeatmap($name, $backgroundImage) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		mysqli_query($con, 'INSERT INTO ' . Settings::db_table_infected_tickets_seatmaps . '(`humanName`, `backgroundImage`) VALUES (\'' . $name . '\', \'' . $backgroundImage . '\')');

		$result = mysqli_query($con, 'SELECT id FROM ' .  Settings::db_table_infected_tickets_seatmaps . ' ORDER BY id DESC LIMIT 1;');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row)
		{
			return self::getSeatmap($row['id']);
		}

	}
	
	public static function getSeatmaps() {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` 
									  FROM `' . Settings::db_table_infected_tickets_seatmaps . '`;');

		$seatmapArray = array();

		while ($row = mysqli_fetch_array($result)) {
			array_push($seatmapArray, self::getSeatmap($row['id']));
		}

		return $seatmapArray;
	}
	
	public static function getRows($seatmap) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` 
									  FROM `' . Settings::db_table_infected_tickets_rows . '` 
									  WHERE `seatmap` = \'' . $seatmap->getId() . '\';');

		$rowArray = array();

		while ($row = mysqli_fetch_array($result)) {
			array_push($rowArray, RowHandler::getRow($row['id']));
		}

		return $rowArray;
	}
}
?>