<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2018 Infected <https://infected.no/>.
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
require_once 'objects/bongtype.php';
require_once 'handlers/eventhandler.php';

class BongHandler {
	/*
	 * Returns the gate with the given id.
	 */
	public static function getBongType($id) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_bongTypes . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('BongType');
	}

	/*
	 * Returns a list of all bong types. If event is not specified, the current one is used
	 */
	public static function getBongTypes(Event $event = EventHandler::getCurrentEvent()) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_bongTypes . '`;');

		$bongList = [];

		while ($object = $result->fetch_object('BongType')) {
			$bongList[] = $object;
		}

		return $bongList;
	}
}
?>
