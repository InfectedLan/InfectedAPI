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
require_once 'objects/seatmap.php';

class SeatmapHandler {
	/*
	 * Get a seatmap by the internal id.
	 */
	public static function getSeatmap($id) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seatmaps . '` 
									WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
		
		$database->close();
		
		return $result->fetch_object('Seatmap');
	}
	
	/*
	 * Returns a list of all seatmaps.
	 */
	public static function getSeatmaps() {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seatmaps . '`;');

		$database->close();

		$seatmapList = array();

		while ($object = $result->fetch_object('Seatmap')) {
			array_push($seatmapList, $object);
		}

		return $seatmapList;
	}

	/*
	 * Creates a new seatmap.
	 */
	public static function createSeatmap($name, $backgroundImage) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$database->query('INSERT INTO ' . Settings::db_table_infected_tickets_seatmaps . '(`humanName`, `backgroundImage`) 
						  VALUES (\'' . $database->real_escape_string($name) . '\', 
								  \'' . $database->real_escape_string($backgroundImage) . '\')');

		$result = $database->query('SELECT * FROM `' .  Settings::db_table_infected_tickets_seatmaps . '`
									WHERE `id` = \'' . $database->insert_id . '\';');

		$database->close();

		return $result->fetch_object('Seatmap');
	}

	/*
	 * Duplicate a seatmap.
	 */
	public static function cloneSeatmap(Seatmap $seatmap) {
		return self::createSeatmap('Clone of ' . $seatmap->getHumanName(), $seatmap->getBackgroundImage());
	}

	/*
	 * Returns a list of all seatmaps.
	 */
	public static function getEvent(Seatmap $seatmap) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_events . '` 
									WHERE `seatmapId` = \'' . $seatmap->getId() . '\';');

		$database->close();

		$row = $result->fetch_array();

		if ($row) {
			return EventHandler::getEvent($row['id']);
		}
	}
	
	/*
	 * Set the background of the specified seatmap.
	 */
	public static function setBackground(Seatmap $seatmap, $filename) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$database->query('UPDATE `' . Settings::db_table_infected_tickets_seatmaps . '` 
						  SET `backgroundImage` = \'' . $database->real_escape_string($filename) . '\' 
						  WHERE `id` = \'' . $seatmap->getId() . '\';');
	
		$database->close();
	}
}
?>