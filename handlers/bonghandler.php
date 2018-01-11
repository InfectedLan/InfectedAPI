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
	//Entitlement types
	const ENTITLEMENT_TYPE_USER = 0;
	const ENTITLEMENT_TYPE_CREW = 1;
	/*
	 * Append types. These control how the amount is calculated torwards the grand total
	*/
	const APPEND_TYPE_ADDITIVE = 0; // The entitlement entry will always add this amount, no matter how many of this type the user is already entitled to
	const APPEND_TYPE_EXCLUSIVE = 1; // Only the highest entitlement entry of the exclusive type will count torwards the grand total. This means you can say "all crews get two", and "tech gets three", and the higher entry will override the lower, not add to it.

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
	public static function getBongTypes(Event $event = null) {
		if($event==null) {
			$event = EventHandler::getCurrentEvent();
		}

		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_bongTypes . '`;');

		$bongList = [];

		while ($object = $result->fetch_object('BongType')) {
			$bongList[] = $object;
		}

		return $bongList;
	}

	/*
	 * Calculates how much of a bong type a certain user has left
	 */
	public static function getBongEntitlement(BongType $type, User $user, Event $event = null) {
		if($event==null) {
			$event = EventHandler::getCurrentEvent();
		}

		$database = Database::getConnection(Settings::db_name_infected);

		$exclusiveNum = 0;
		$additiveNum = 0;

		//First, we find out how much of the bong that this user is entitled to
		$personalEntitlements = $database->query('SELECT * FROM `' . Settings::db_table_infected_bongEntitlements . '` WHERE `bongId` = ' . $type->getId() . ' AND `entitlementType` = ' . self::ENTITLEMENT_TYPE_USER . ' AND `entitlementArgs` = ' . $user->getId() . ';');

		while($row = $personalEntitlements->fetch_row()) {
			if($row["appendType"]==self::APPEND_TYPE_ADDITIVE) {
				$additiveNum += $row["entitlementAmt"];
			} else if($row["appendType"]==self::APPEND_TYPE_EXCLUSIVE) {
				if($row["entitlementAmt"] > $exclusiveNum) {
					$exclusiveNum = $row["entitlementAmt"];
				}
			}
		}

		if($user->isGroupMember()) {
			$group = $user->getGroup($event);
			$groupEntitlements = $database->query('SELECT * FROM `' . Settings::db_table_infected_bongEntitlements . '` WHERE `bongId` = ' . $type->getId() . ' AND `entitlementType` = ' . self::ENTITLEMENT_TYPE_CREW . ' AND (`entitlementArgs` = ' . $group->getId() . ' OR `entitlementArgs` = 0);');
			while($row = $groupEntitlements->fetch_row()) {
				if($row["appendType"]==self::APPEND_TYPE_ADDITIVE) {
					$additiveNum += $row["entitlementAmt"];
				} else if($row["appendType"]==self::APPEND_TYPE_EXCLUSIVE) {
					if($row["entitlementAmt"] > $exclusiveNum) {
						$exclusiveNum = $row["entitlementAmt"];
					}
				}
			}
		}

		return $exclusiveNum+$additiveNum;

	}

	/*
	 * Calculates the amount of transacted bongs on this account. A negative amount is a withdrawal.
	 */
	public static function sumBongTransactions(BongType $ype, User $user, Event $event = null) {
		if($event==null) {
			$event = EventHandler::getCurrentEvent();
		}

		$database = Database::getConnection(Settings::db_name_infected);

		$transactionList = $database->query('SELECT * FROM `' . Settings::db_table_infected_bongTransactions . '` WHERE `bongType` = ' . $type->getId() . ' AND `userId` = ' . $user->getId() . ';');

		$count = 0;

		foreach($transactionList as $row) {
			$count += $row["amt"];
		}

		return $count;
	}

	/*
	 * Returns how many of a given bong a user has left
	 */
	public static function getBongPosession(BongType $type, User $user, Event $event = null) {
		if($event==null) {
			$event = EventHandler::getCurrentEvent();
		}

		return self::getBongEntitlement($type, $user, $event)+self::sumBongTransactions($type, $user, $event);
	}

	/*
	 * Handles a bong transaction
	 * Note that this function is not "safe", it will allow transactions that puts the user at a negative quantity of bongs.
	 * Calling this should only be done after making sure the user can afford the transaction
	 * Also note that all transactions are connected to the user who had the permissions to process it.
	 */
	public static function processBongTransaction(BongType $type, User $user, $amount, User $responsible, Event $event = null) {
		if($event==null) {
			$event = EventHandler::getCurrentEvent();
		}

		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('INSERT INTO `' . Settings::db_table_infected_bongTransactions . '` (`bongType`, `amt`, `transactionHandler`, `timestamp`) VALUES (' . $type->getId() . ', ' . $database->real_escape_string($amount) . ', ' .  $responsible->getId(). ', ' . date('Y-m-d H:i:s') . ');');

	}
}
?>
