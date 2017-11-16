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

class NfcCardHandler {
	/*
	 * Returns the card with the given id.
	 */
	public static function getCard($id) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_nfccards . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('NfcCard');
	}

	/*
	 * Returns a list of all nfc cards by their event.
	 */
	public static function getCardsByEvent(Event $event) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_nfccards . '` WHERE `eventId` = \'' . $event->getId() . '\';');

		$cardList = [];

		while ($object = $result->fetch_object('NfcCard')) {
			$cardList[] = $object;
		}

		return $cardList;
	}

	/*
	 * Returns a list of all nfc cards for the current event
	 */
	public static function getCardsForCurrentEvent() {
		return getGatesByEvent(EventHandler::getCurrentEvent());
	}
}
?>
