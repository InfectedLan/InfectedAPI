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
require_once 'objects/location.php';

class LocationHandler {
	/*
	 * Returns the location with the given id.
	 */
	public static function getLocation($id) {
		$database = Database::open(Settings::db_name_infected);
		
		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_locations . '`
									WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
		
		$database->close();
		
		return $result->fetch_object('Location');
	}
	
	/* 
	 * Returns a list of all locations.
	 */
	public static function getLocations() {
		$database = Database::open(Settings::db_name_infected);
		
		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_locations . '`;');
		
		$database->close();

		$locationList = array();
		
		while ($object = $result->fetch_object('Location')) {
			array_push($locationList, $object);
		}

		return $locationList;
	}
}
?>
