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

require_once 'handlers/userhandler.php';
require_once 'handlers/tickettypehandler.php';
require_once 'objects/object.php';

class Payment extends Object{
	private $userId;
	private $ticketTypeId;
	private $amount;
	private $price;
	private $transactionId;
	private $datetime;

	/*
	 * Returns this payments user.
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the ticket type for this payment.
	 */
	public function getTicketType() {
		return TicketTypeHandler::getTicketType($this->ticketTypeId);
	}

	/*
	 * Returns the amount for this payment.
	 */
	public function getAmount() {
		return $this->amount;
	}

	/*
	 * Returns the total price for this payment.
	 */
	public function getPrice() {
		return $this->price;
	}

	/*
	 * Returns the transaction id of this payment.
	 */
	public function getTransactionId() {
		return $this->transactionId;
	}

	/*
	 * Returns the datetime of this payment.
	 */
	public function getDateTime() {
		return strtotime($this->datetime);
	}
}
?>
