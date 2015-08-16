<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/entrancehandler.php';
require_once 'handlers/seathandler.php';
require_once 'handlers/seatmaphandler.php';
require_once 'objects/row.php';
require_once 'objects/seatmap.php';

class RowHandler {
	/*
	 * Return the row by the internal id.
	 */
	public static function getRow($id) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_rows . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		$database->close();

		return $result->fetch_object('Row');
	}

	/*
	 * Returns a list of all rows.
	 */
	public static function getRows() {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_rows . '`;');

		$database->close();

		$rowList = array();

		while ($object = $result->fetch_object('Row')) {
			array_push($rowList, $object);
		}

		return $rowList;
	}

	/*
	 * Returns a list of all rows for the specified seatmap.
	 */
	public static function getRowsBySeatmap(Seatmap $seatmap) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_rows . '`
																WHERE `seatmapId` = \'' . $seatmap->getId() . '\';');

		$database->close();

		$rowList = array();

		while ($object = $result->fetch_object('Row')) {
			array_push($rowList, $object);
		}

		return $rowList;
	}

	/*
	 * Create a new row.
	 */
	public static function createRow(Seatmap $seatmap, $x, $y) {
		$database = Database::open(Settings::db_name_infected_tickets);

		//Find out what row is max row
		$highestRowNum = $database->query('SELECT `rowId` FROM `' . Settings::db_table_infected_tickets_rows . '`
																		   WHERE `seatmapId`=' . $seatmap->getId() . '
																		   ORDER BY `rowId` DESC
																		   LIMIT 1;');

		$row = mysqli_fetch_array($highestRowNum);

		$newRowNumber = $row['row'] + 1;
		$entrance = EntranceHandler::getEntrance(1); // TODO: Set this somewere else?

		$database->query('INSERT INTO `' . Settings::db_table_infected_tickets_rows . '` (`seatmapId`, `entranceId`, `number`, `x`, `y`)
										  VALUES (\'' . $seatmap->getId() . '\',
															\'' . $entrance->getId() . '\',
															\'' . $database->real_escape_string($newRowNumber) . '\',
														  \'' . $database->real_escape_string($x) . '\',
														  \'' . $database->real_escape_string($y) . '\');');

		$result = $database->query('SELECT * FROM `' .  Settings::db_table_infected_tickets_rows . '`
																WHERE `id` = \'' . $database->insert_id . '\';');

		$database->close();

		return $result->fetch_object('Row');
	}

	/*
	 * Move the specified row to the specified coordinates.
	 */
	public static function updateRow(Row $row, $x, $y) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$database->query('UPDATE `' . Settings::db_table_infected_tickets_rows . '`
										  SET `x` = \'' . $database->real_escape_string($x) . '\',
											  	`y` = \'' . $database->real_escape_string($y) . '\'
										  WHERE `id` = \'' . $row->getId() . '\';');

		$database->close();
	}

	/*
	 * Removes the specified row.
	 */
	public static function removeRow(Row $row) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('DELETE FROM `' . Settings::db_table_infected_tickets_rows . '`
																WHERE `id` = ' . $row->getId() . ';');

		$database->close();

		foreach (SeatHandler::getSeatsByRow($row) as $seat) {
			SeatHandler::deleteSeat($seat);
		}
	}

	/*
	 * Returns true if the row is safe to delete.
	 */
	public static function safeToDelete(Row $row) {
		$seatList = self::getSeats($row);

		foreach($seatList as $seat) {
			if (SeatHandler::hasOwner($seat)) {
				return false;
			}
		}

		return true;
	}

	/*
	 * Returns the event this row is for.
	 */
	public static function getEvent($row) {
		return SeatmapHandler::getEvent($row->getSeatmap());
	}
}
?>
