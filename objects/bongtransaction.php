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

require_once 'objects/databaseobject.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/bongtypehandler.php';

/*
 * Represents a single entry in the bong transaction log
 */
class BongTransaction extends DatabaseObject {
	private $bongType;
	private $amt;
	private $transactionHandler;
	private $timestamp;
	private $userId;

	
	/*
	 * Returns the bong type the transaction is done on
	 */
	public function getBongType() {
		return BongTypeHandler::getBongType($this->bongType);
	}

	/*
	 * Returns the amount this transaction represents. Positive means an increase in funds, negative means a decrease in user funds.
	 * in 99% of the cases, this will be negative, because we mostly handle purchases
	 */
	public function getTransactionAmount() {
		return $this->amt;
	}

	/*
	 * Returns the user account that processed the transaction
	 */
	public function getTransactionHandler() {
		return UserHandler::getUser($this->transactionHandler);
	}	

	/*
	 * Returns when the transaction was processed
	 */
	public function getTimestamp() {
		return $this->amt;
	}

	/*
	 * Returns the user that this transaction was made on
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

}
?>