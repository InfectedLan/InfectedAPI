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
require_once 'database.php';
require_once 'objects/payment.php';
require_once 'objects/user.php';
require_once 'objects/tickettype.php';

class PaymentHandler {
	/*
	 * Returns the payment by the internal id.
	 */
	public static function getPayment(int $id): ?Payment {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_payments . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('Payment');
	}

	/*
	 * Returns a list of all payments.
	 */
	public static function getPayments(): array {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_payments . '`;');

		$paymentList = [];

		while ($object = $result->fetch_object('Payment')) {
			$paymentList[] = $object;
		}

		return $paymentList;
	}

	/*
	 * Create a new payment.
	 */
	public static function createPayment(User $user, TicketType $ticketType, int $amount, int $price, string $transactionId) {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$database->query('INSERT INTO `' . Settings::db_table_infected_tickets_payments . '` (`userId`, `ticketTypeId`, `amount`, `price`, `transactionId`, `datetime`)
										  VALUES (\'' . $user->getId() . '\',
														  \'' . $ticketType->getId() . '\',
														  \'' . $database->real_escape_string($amount) . '\',
														  \'' . $database->real_escape_string($price) . '\',
														  \'' . $database->real_escape_string($transactionId) . '\',
														  \'' . date('Y-m-d H:i:s') . '\');');

		return self::getPayment($database->insert_id);
	}
}
?>
