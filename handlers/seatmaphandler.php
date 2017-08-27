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
require_once 'handlers/eventhandler.php';
require_once 'handlers/rowhandler.php';
require_once 'handlers/seathandler.php';
require_once 'objects/seatmap.php';

class SeatmapHandler {
	/*
	 * Get a seatmap by the internal id.
	 */
	public static function getSeatmap($id) {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seatmaps . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');


		return $result->fetch_object('Seatmap');
	}

	/*
	 * Returns a list of all seatmaps.
	 */
	public static function getSeatmaps() {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seatmaps . '`;');


		$seatmapList = [];

		while ($object = $result->fetch_object('Seatmap')) {
			$seatmapList[] = $object;
		}

		return $seatmapList;
	}

	/*
	 * Creates a new seatmap.
	 */
	public static function createSeatmap($name, $backgroundImage) {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$database->query('INSERT INTO ' . Settings::db_table_infected_tickets_seatmaps . '(`humanName`, `backgroundImage`)
						  				VALUES (\'' . $database->real_escape_string($name) . '\',
								  						\'' . $database->real_escape_string($backgroundImage) . '\')');

		$seatmap = self::getSeatmap($database->insert_id);


		return $seatmap;
	}

	/*
	 * Creates a new seatmap with the contents of another seatmap
	 */
	public static function cloneSeatmap(Seatmap $sourceSeatmap) {
	    $targetSeatmap = self::createSeatmap('Clone of ' . $sourceSeatmap->getHumanName(), $sourceSeatmap->getBackgroundImage());

	    self::copySeatmap($sourceSeatmap, $targetSeatmap);
	    
	    return $targetSeatmap;
	}

	/*
	 * Copies a seatmap, destroying the old seatmap in the process
	 */
	public static function copySeatmap(Seatmap $sourceSeatmap, Seatmap $targetSeatmap) {
	    $preExistingRows = RowHandler::getRowsBySeatmap($targetSeatmap);
	    $isSafeToDelete = true;
	    foreach($preExistingRows as $row) {
		if(!RowHandler::safeToDelete($row)) {
		    $isSafeToDelete = false;
		    break;
		}
	    }
	    if(!$isSafeToDelete) {
		return false;
	    }

	    $sourceRows = RowHandler::getRowsBySeatmap($sourceSeatmap);
	    foreach($sourceRows as $sourceRow) {
		$targetRow = RowHandler::createRow($targetSeatmap, $sourceRow->getX(), $sourceRow->getY());
		$sourceSeats = SeatHandler::getSeatsByRow($sourceRow);
		foreach($sourceSeats as $sourceSeat) {
		    $targetSeat = SeatHandler::createSeat($targetRow, $sourceSeat->getNumber());
		}
	    }

	    return true;
	}

	/*
	 * Returns a list of all seatmaps.
	 */
	public static function getEvent(Seatmap $seatmap) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_events . '`
																WHERE `seatmapId` = \'' . $seatmap->getId() . '\';');


		$row = $result->fetch_array();

		return EventHandler::getEvent($row['id']);
	}

	/*
	 * Set the background of the specified seatmap.
	 */
	public static function setBackground(Seatmap $seatmap, $filename) {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$database->query('UPDATE `' . Settings::db_table_infected_tickets_seatmaps . '`
						  				SET `backgroundImage` = \'' . $database->real_escape_string($filename) . '\'
						  				WHERE `id` = \'' . $seatmap->getId() . '\';');

	}
}
?>
