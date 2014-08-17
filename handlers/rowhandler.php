<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/row.php';
require_once 'handlers/seatmaphandler.php';
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
						   $row['number'],
						   $row['x'], 
						   $row['y'], 
						   $row['entrance'], 
						   $row['seatmap']);
		}
	}

	public static function getSeats($row) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_tickets_seats . '` 
									  WHERE `rowId` = \'' . $row->getId() . '\';');

		$seatArray = array();

		while ($seat = mysqli_fetch_array($result)) {
			array_push($seatArray, SeatHandler::getSeat($seat['id']));
		}

		MySQL::close($con);

		return $seatArray;
	}

	public static function createNewRow($seatmapId, $x, $y) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		//Find out what row is max row
		$highestRowNum = mysqli_query($con, 'SELECT `row` FROM `' . Settings::db_table_infected_tickets_rows . '` WHERE `seatmap`=' . $seatmapId . ' ORDER BY `row` DESC LIMIT 1;');

		$row = mysqli_fetch_array($highestRowNum);

		$newRowNumber = $row['row']+1;

		mysqli_query($con, 'INSERT INTO ' . Settings::db_table_infected_tickets_rows . '(`number`, `x`, `y`, `seatmap`) VALUES (\'' . $newRowNumber . '\', ' . $x . ', ' . $y . ', ' . $seatmapId . ')');

		$result = mysqli_query($con, 'SELECT id FROM ' .  Settings::db_table_infected_tickets_rows . ' ORDER BY id DESC LIMIT 1;');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row)
		{
			return self::getRow($row['id']);
		}

	}
	public static function safeToDelete($row)
	{
		$seats = self::getSeats($row);
		foreach($seats as $seat)
		{
			if(SeatHandler::hasOwner($seat))
			{
				return false;
			}
		}
		return true;
	}
	public static function deleteRow($row)
	{
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_tickets_rows . '` WHERE `id`=' . $row->getId() . ';');

		$seats = self::getSeats($row);
		foreach($seats as $seat)
		{
			SeatHandler::deleteSeat($seat);
		}

		MySQL::close($con);
	}
	public static function addSeat($row)
	{
		$con = MySQL::open(Settings::db_name_infected_tickets);

		//Find out what seat number we are at
		$highestSeatNum = mysqli_query($con, 'SELECT `number` FROM `' . Settings::db_table_infected_tickets_seats . '` WHERE `rowId`=' . $row->getId() . ' ORDER BY `number` DESC LIMIT 1;');

		$seatRow = mysqli_fetch_array($highestSeatNum);

		$newSeatNumber = $seatRow['number']+1;

		mysqli_query($con, 'INSERT INTO `seats` (`rowId`, `number`) VALUES (' . $row->getId() . ', ' . $newSeatNumber . ')');

		MySQL::close($con);
	}
	public static function moveRow($row, $x, $y)
	{
		$con = MySQL::open(Settings::db_name_infected_tickets);

		mysqli_query($con, 'UPDATE `rows` SET `x`=' . $x . ',`y`=' . $y . ' WHERE `id`=' . $row->getId() . ';');

		MySQL::close($con);
	}

	public static function getEvent($row)
	{
		return SeatmapHandler::getEvent(SeatmapHandler::getSeatmap($row->getSeatmap()));
	}
}
?>