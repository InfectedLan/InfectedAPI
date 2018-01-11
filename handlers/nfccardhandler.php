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
require_once 'objects/nfccard.php';
require_once 'objects/event.php';
require_once 'objects/user.php';
require_once 'handlers/eventhandler.php';

class NfcCardHandler {
	/*
	 * Registers a NFC card for a user. If event is not specified, the current one is returned
	*/
	public static function registerCard(User $user, $nfcid, Event $event = null) {
		if($event == null) {
			$event = EventHandler::getCurrentEvent();
		}

		$database = Database::getConnection(Settings::db_name_infected_tech);

		$result = $database->query('INSERT INTO `' . Settings::db_table_infected_tech_nfccards . '` (`userId`, `eventId`, `nfcId`)
																VALUES (\'' . $user->getId() . '\',
																				\'' . $event->getId() . '\', 
																				\'' . $database->real_escape_string($nfcid) . '\');');


		return self::getCard($database->insert_id);
	}

	/*
	 * Returns the card with the given id.
	 */
	public static function getCard($id) {
		$database = Database::getConnection(Settings::db_name_infected_tech);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_tech_nfccards . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('NfcCard');
	}

	/*
	 * Returns a list of all nfc cards by a specified event, or the current one if none is specified
	 */
	public static function getCards(Event $event = null) {
		if($event==null) {
			$event = EventHandler::getCurrentEvent();
		}
		$database = Database::getConnection(Settings::db_name_infected_tech);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tech_nfccards . '` WHERE `eventId` = \'' . $database->real_escape_string($event->getId()) . '\';');

		$cardList = [];

		while ($object = $result->fetch_object('NfcCard')) {
			$cardList[] = $object;
		}

		return $cardList;
	}

	/*
	 * Returns the NFC card given a user and optionally an event
	 */
	public static function getCardsByUser(User $user, Event $event = null) {
		if($event==null) {
			$event = EventHandler::getCurrentEvent()
		}
		$database = Database::getConnection(Settings::db_name_infected_tech);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_tech_nfccards . '`
																WHERE `eventId` = \'' . $database->real_escape_string($event->getId()) . '\' AND `userId` = \'' . $database->real_escape_string($user->getId()) . '\';');

		$cardList = [];

		while ($object = $result->fetch_object('NfcCard')) {
			$cardList[] = $object;
		}

		return $cardList;
	}
}
?>
