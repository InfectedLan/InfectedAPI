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

require_once 'handlers/nfccardhandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/eventhandler.php';
require_once 'database.php';

/* 
 * NfcCardTest
 *
 * Responsible for testing the functionality of the NfcCard class
 *
 */
class NFCCardTest extends TestCase {
	public function test() {
		$this->cardRegistrationTest();
		$this->getterTest();
		$this->cleanup();
	}

	private function cardRegistrationTest() {
		//Constants used by the test
		$me = UserHandler::getUser(1);
		$nfcid = "E004010203040506";

		$cards = NfcCardHandler::getCardsForCurrentEvent();
		$this->assertEquals(0, count($cards));
	}

	private function getterTest() {
		//RegisterCard and getCardsByUserForCurrentEvent
		$me = UserHandler::getUser(1);
		$nfcid = "E004010203040506";

		NfcCardHandler::registerCard($me, $nfcid);


		$cards = NfcCardHandler::getCardsByUser($me);
		$this->assertEquals(1, count($cards));

		$card = $cards[0];
		$this->assertNotEquals($card, null);

		$cards = NfcCardHandler::getCards();
		$this->assertEquals(1, count($cards));

		$card = $cards[0];
		$this->assertNotEquals($card, null);

		$this->assertEquals($me, $card->getUser());
		$this->assertEquals($nfcid, $card->getNfcId());

		//getCard
		$newCard = NfcCardHandler::getCard($card->getId());
		$this->assertEquals($card, $newCard);
	}

	private function cleanup() {

	}
}
?>