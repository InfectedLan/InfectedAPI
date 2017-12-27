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

require_once 'handlers/userhandler.php';
require_once 'handlers/tickettypehandler.php';
require_once 'objects/databaseobject.php';

class Payment extends DatabaseObject {
	private $userId;
	private $ticketTypeId;
	private $amount;
	private $price;
	private $transactionId;
	private $datetime;

	/*
	 * Returns this payments user.
	 */
	public function getUser(): User {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the ticket type for this payment.
	 */
	public function getTicketType(): TicketType {
		return TicketTypeHandler::getTicketType($this->ticketTypeId);
	}

	/*
	 * Returns the amount for this payment.
	 */
	public function getAmount(): int {
		return $this->amount;
	}

	/*
	 * Returns the total price for this payment.
	 */
	public function getPrice(): int {
		return $this->price;
	}

	/*
	 * Returns the transaction id of this payment.
	 */
	public function getTransactionId(): int {
		return $this->transactionId;
	}

	/*
	 * Returns the datetime of this payment.
	 */
	public function getDateTime(): int {
		return strtotime($this->datetime);
	}
}