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
require_once 'objects/nfccard.php';
require_once 'objects/event.php';
require_once 'objects/user.php';
require_once 'handlers/eventhandler.php';

class NfcCardHandler {
	/*
	 * Registers a NFC card for a user
	*/
	public static function registerCard(User $user, Event $event, $nfcid) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('INSERT INTO `' . Settings::db_table_infected_nfccards . '` (`userId`, `eventId`, `nfcId`)
																VALUES (\'' . $user->getId() . '\',
																				\'' . $event->getId() . '\', 
																				\'' . $database->real_escape_string($nfcid) . '\');');


		return self::getCard($database->insert_id);
	}

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

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_nfccards . '` WHERE `eventId` = \'' . $database->real_escape_string($event->getId()) . '\';');

		$cardList = [];

		while ($object = $result->fetch_object('NfcCard')) {
			$cardList[] = $object;
		}

		return $cardList;
	}

	/*
	 * Returns the NFC card given an user and an event
	 */
	public static function getCardByUserAndEvent(Event $event, User $user) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_nfccards . '`
																WHERE `eventId` = \'' . $database->real_escape_string($event->getId()) . '\' AND `userId` = \'' . $database->real_escape_string($user->getId()) . '\';');

		return $result->fetch_object('NfcCard');
	}

	/*
	 * Returns a user's NFC card for the current event
	 */
	public static function getCardByUserForCurrentEvent(User $user) {
		return self::getCardByUserAndEvent(EventHandler::getCurrentEvent(), $user);
	}

	/*
	 * Returns a list of all nfc cards for the current event
	 */
	public static function getCardsForCurrentEvent() {
		return self::getCardsByEvent(EventHandler::getCurrentEvent());
	}
}
?>
