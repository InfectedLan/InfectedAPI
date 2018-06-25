<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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
require_once 'databaseconstants.php';
require_once 'database.php';
require_once 'handlers/tickethandler.php';
require_once 'objects/storesession.php';
require_once 'objects/user.php';
require_once 'objects/tickettype.php';
require_once 'objects/payment.php';

class StoreSessionHandler {
	/*
	 * Get a store session by the internal id.
	 */
	public static function getStoreSession(int $id): ?StoreSession {
		$database = Database::getConnection(Settings::getValue("db_name_infected_tickets"));

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_tickets_storesessions . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('StoreSession');
	}

	/*
	 * Get a list of all store sessions.
	 */
	public static function getStoreSessions(): array {
		$database = Database::getConnection(Settings::getValue("db_name_infected_tickets"));

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_tickets_storesessions . '`;');

		$storeSessionList = [];

		while ($object = $result->fetch_object('StoreSession')) {
			$storeSessionList[] = $object;
		}

		return $storeSessionList;
	}

	/*
	 * Returns the store session for the specified user.
	 */
	public static function getStoreSessionByUser(User $user): ?StoreSession {
		$database = Database::getConnection(Settings::getValue("db_name_infected_tickets"));

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_tickets_storesessions . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

		return $result->fetch_object('StoreSession');
	}

	/*
	 * Returns the store session by the specified key.
	 */
	private static function getStoreSessionByCode(string $code): ?StoreSession {
		$database = Database::getConnection(Settings::getValue("db_name_infected_tickets"));

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_tickets_storesessions . '`
																WHERE `code` = \'' . $database->real_escape_string($code) . '\'
																AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

		return $result->fetch_object('StoreSession');
	}

	/*
	 * Returns true if the specified user have a store session.
	 */
	public static function hasStoreSession(User $user): bool {
		$database = Database::getConnection(Settings::getValue("db_name_infected_tickets"));

		$result = $database->query('SELECT `id` FROM `' . DatabaseConstants::db_table_infected_tickets_storesessions . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Create a new store session.
	 */
	public static function createStoreSession(User $user, TicketType $ticketType, int $amount, int $price): ?string {
		$code = bin2hex(openssl_random_pseudo_bytes(16));

		$database = Database::getConnection(Settings::getValue("db_name_infected_tickets"));

		$result = $database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_tickets_storesessions . '` (`userId`, `ticketTypeId`, `amount`, `code`, `price`, `datetime`)
																VALUES (\'' . $user->getId() . '\',
																				\'' . $ticketType->getId() . '\',
																				\'' . $database->real_escape_string($amount) . '\',
																				\'' . $code . '\',
																				\'' . $database->real_escape_string($price) . '\',
																				\'' . date('Y-m-d H:i:s') . '\');');

		return $code;
	}

	/*
	 * Removes the specified store session.
	 */
	public static function removeStoreSession(StoreSession $storeSession) {
		$database = Database::getConnection(Settings::getValue("db_name_infected_tickets"));

		$result = $database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_tickets_storesessions . '`
																WHERE `id` = \'' . $storeSession->getId() . '\';');
	}

	/*
	 * This is used to validate a payment.
	 */
	public static function isPaymentValid($totalPrice, StoreSession $storeSession): bool {
		return $storeSession->getPrice() == $totalPrice;
	}

	/*
	 * Returns the amount of reserved tickets for the specified ticket type.
	 */
	public static function getReservedTicketCount(TicketType $ticketType): int {
		$database = Database::getConnection(Settings::getValue("db_name_infected_tickets"));

		$result = $database->query('SELECT `amount` FROM `' . DatabaseConstants::db_table_infected_tickets_storesessions . '`
																WHERE `ticketTypeId` = \'' . $ticketType->getId() . '\'
																AND `datetime` > \'' . self::oldestValidTimestamp() . '\';');

		$reservedCount = 0;

		while ($row = $result->fetch_array()) {
			$reservedCount += $row['amount'];
		}

		return $reservedCount;
	}

	/*
	 * Returns the oldest valid time a store session can be from.
	 */
	private static function oldestValidTimestamp(): string {
		return date('Y-m-d H:i:s', time() - Settings::getValue("storeSessionTime"));
	}

	/*
	 * Returns the user with a store session with the specified code.
	 */
	public static function getUserByStoreSessionCode(string $code): ?User {
		$database = Database::getConnection(Settings::getValue("db_name_infected"));

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_users . '`
																WHERE `id` = (SELECT `userId` FROM `' . Settings::getValue("db_name_infected_tickets") . '`.`' . DatabaseConstants::db_table_infected_tickets_storesessions . '`
																			  			WHERE `code`= \'' . $database->real_escape_string($code) . '\'
																			  			AND `datetime` > \'' . self::oldestValidTimestamp() . '\');');

		return $result->fetch_object('User');
	}

	public static function purchaseComplete(StoreSession $storeSession, Payment $payment): bool {
		if ($storeSession != null) {
			// Checks are ok, lets buy!
			for ($i = 0; $i < $storeSession->getAmount(); $i++) {
				TicketHandler::createTicket($storeSession->getUser(), $storeSession->getTicketType(), $payment);
			}

			self::removeStoreSession($storeSession);

			return true;
		}

		return false;
	}
}
?>
