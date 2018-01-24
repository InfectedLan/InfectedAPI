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
require_once 'objects/nfcgate.php';
require_once 'objects/event.php';
require_once 'handlers/eventhandler.php';

class NfcUnitHandler {
	/*
	 * Returns the gate with the given id.
	 */
	public static function getGate($id) {
		$database = Database::getConnection(Settings::db_name_infected_tech);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_tech_nfcunits . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('NfcUnit');
	}

	/*
	 * Returns the gate with the given pcbid
	 */
	public static function getGateByPcbid(string $pcbid) {
		$database = Database::getConnection(Settings::db_name_infected_tech);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_tech_nfcunits . '`
																WHERE `pcbId` = \'' . $database->real_escape_string($pcbid) . '\';');

		return $result->fetch_object('NfcUnit');
	}

	/*
	 * Returns a list of all nfc gates by their event. If the eve nt is not specified, the current one is used
	 */
	public static function getGatesByEvent(Event $event = null) {
		if($event==null) {
			$event = EventHandler::getCurrentEvent();
		}

		$database = Database::getConnection(Settings::db_name_infected_tech);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tech_nfcunits . '` WHERE `eventId` = \'' . $event->getId() . '\';');

		$gateList = [];

		while ($object = $result->fetch_object('NfcUnit')) {
			$gateList[] = $object;
		}

		return $gateList;
	}
}
?>
