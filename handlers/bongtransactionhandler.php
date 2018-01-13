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
require_once 'objects/bongtransaction.php';
require_once 'objects/bongtype.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/bongentitlementhandler.php';

class BongTransactionHandler {
	
	/* 
	 * Returns a single transaction
	 */
	public static function getBongTransaction(int $id) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_bongTransactions . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('BongTransaction');
	}

	/* 
	 * Returns a list of all bong transactions for the given bong and optionally user
	 */
	public static function getBongTransactions(BongType $type, User $user = null) {
		$database = Database::getConnection(Settings::db_name_infected);

		$transactionList = [];

		if($user==null) {
			$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_bongTransactions . '`
																WHERE `bongType` = \'' . $type->getId() . '\';');
			while ($object = $result->fetch_object('BongTransaction')) {
				$transactionList[] = $object;
			}

		} else {
			$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_bongTransactions . '`
																WHERE `bongType` = \'' . $type->getId() . '\' AND `userId` =  ' . $user->getId() . ';');
			while ($object = $result->fetch_object('BongTransaction')) {
				$transactionList[] = $object;
			}
		}

		return $transactionList;
	}


	/*
	 * Calculates the amount of transacted bongs on this account. A negative amount is a withdrawal.
	 */
	public static function sumBongTransactions(BongType $type, User $user) {
		$transactionList = self::getBongTransactions($type, $user);

		$count = 0;

		foreach($transactionList as $transaction) {
			$count += $transaction->getTransactionAmount();
		}

		return $count;
	}

	/*
	 * Returns how many of a given bong a user has left
	 */
	public static function getBongPosession(BongType $type, User $user) {
		return BongEntitlementHandler::calculateBongEntitlementByUser($type, $user)+self::sumBongTransactions($type, $user);
	}

	/*
	 * Handles a bong transaction
	 * Note that this function is not "safe", it will allow transactions that puts the user at a negative quantity of bongs.
	 * Calling this should only be done after making sure the user can afford the transaction
	 * Also note that all transactions are connected to the user who had the permissions to process it.
	 */
	public static function processBongTransaction(BongType $type, User $user, int $amount, User $responsible) {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('INSERT INTO `' . Settings::db_table_infected_bongTransactions . '` (`bongType`, `amt`, `transactionHandler`, `timestamp`, `userId`) VALUES (' . $type->getId() . ', ' . $database->real_escape_string($amount) . ', ' .  $responsible->getId(). ', \'' . date('Y-m-d H:i:s') . '\', ' . $user->getId() . ');');

	}
}
?>
