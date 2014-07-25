<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/seatmap.php';
require_once 'handlers/rowhandler.php';

class SeatmapHandler
{
	public static function getSeatmap($id)
	{
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_infected_tickets_seatmaps . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return new Seatmap($row['id'], $row['humanName']);
		}
	}
	public static function getRows($seatmap)
	{
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_infected_tickets_rows . ' WHERE seatmap=\'' . $seatmap->getId() . '\'');

		$rowArray = array();

		while($row = mysqli_fetch_array($result))
		{
			array_push($rowArray, RowHandler::getRow($row['id']));
		}
	}
}
?>