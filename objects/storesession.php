/*
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

<?php
require_once 'handlers/userhandler.php';
require_once 'handlers/tickettypehandler.php';
require_once 'objects/object.php';

class StoreSession extends Object {
	private $userId;
	private $ticketType;
	private $amount;
	private $code;
	private $price;
	private $datetime;

	/*
	 * Returns the user connected to this session.
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the ticket type the user is buying.
	 */
	public function getTicketType() {
		return TicketTypeHandler::getTicketType($this->ticketType);
	}

	/*
	 * Returns the amount of tickets the user is buying.
	 */
	public function getAmount() {
		return $this->amount;
	}

	/*
	 * Returns the key used during purchasing.
	 */
	public function getCode() {
		return $this->code;
	}

	/*
	 * Returns the price the user was supposed to pay.
	 */
	public function getPrice() {
		return $this->price;
	}
	
	/*
	 * Returns the time this session was created.
	 */
	public function getTimeCreated() {
		return strtotime($this->datetime);
	}
}
?>