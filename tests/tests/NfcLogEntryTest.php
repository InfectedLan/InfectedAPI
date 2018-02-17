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

require_once 'handlers/nfcunithandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/nfcloghandler.php';
require_once 'database.php';

/* 
 * NfcLogEntryTest
 *
 * Tests the nfc logs(Access)
 *
 */
class NfcLogEntryTest extends TestCase {
	public function test() {
		$this->creation();
		$this->cleanup();
	}

    private function creation() {
        $unit = NfcUnitHandler::getGate(1);
        $card = NfcCardHandler::getCard(2);

        $entries = NfcLogHandler::getLogEntriesByUnit($unit);
        $this->assertEquals(0, count($entries));

        NfcLogHandler::createLogEntry($card, $unit, false);

        $entries = NfcLogHandler::getLogEntriesByUnit($unit);
        $this->assertEquals(1, count($entries));

        $this->assertEquals($card->getNfcId(), $entries[0]->getCard()->getNfcId());
        $this->assertEquals(false, $entries[0]->isLegalPass());
    }

	private function cleanup() {

	}
}
?>
