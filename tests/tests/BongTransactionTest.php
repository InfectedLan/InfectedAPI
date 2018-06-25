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
use PHPUnit\Framework\TestCase;

require_once 'handlers/bongtypehandler.php';
require_once 'handlers/bongtransactionhandler.php';
require_once 'handlers/bongentitlementhandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/eventhandler.php';
require_once 'database.php';

/* 
 * BongTransactionTest
 *
 * Tests that bong transactions are handled properly
 *
 */
class BongTransactionTest extends TestCase {
	public function test() {
		$this->transactTest();
		$this->cleanup();
	}

	private function transactTest() {
		$bong = BongTypeHandler::getBongType(1); //Current event
		$user = UserHandler::getUser(1);
		$user2 = UserHandler::getUser(2);
		
		//Does transaction counting work?
		$entitlement = BongEntitlementHandler::calculateBongEntitlementByUser($bong, $user);
		$transactionAmt = BongTransactionHandler::sumBongTransactions($bong, $user);
		$this->assertEquals(0, $transactionAmt);

		$posession = BongTransactionHandler::getBongPosession($bong, $user);

		$this->assertEquals($posession, $entitlement+$transactionAmt);

		$transactions = BongTransactionHandler::getBongTransactions($bong, $user);
		$this->assertEquals(0, count($transactions));

		$transactions = BongTransactionHandler::getBongTransactions($bong, $user2);
		$this->assertEquals(0, count($transactions));

		$allTransactions = BongTransactionHandler::getBongTransactions($bong);
		$this->assertEquals(0, count($allTransactions));

		//Add a transaction, check that it appears in the correct places
		BongTransactionHandler::processBongTransaction($bong, $user, 5, $user);

		$posession = BongTransactionHandler::getBongPosession($bong, $user);

		$this->assertEquals($entitlement+$transactionAmt+5, $posession);

		$transactions = BongTransactionHandler::getBongTransactions($bong, $user);
		$this->assertEquals(1, count($transactions));

		$transactions = BongTransactionHandler::getBongTransactions($bong, $user2);
		$this->assertEquals(0, count($transactions));

		$allTransactions = BongTransactionHandler::getBongTransactions($bong);
		$this->assertEquals(1, count($allTransactions));
	}

	private function cleanup() {

	}
}
?>