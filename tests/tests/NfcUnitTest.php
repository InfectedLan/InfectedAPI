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
require_once 'database.php';

/* 
 * NfcUnitTest 
 *
 * Tests the nfc unit itself.
 *
 */
class NfcUnitTest extends TestCase {
	public function test() {
		$this->creation();
		$this->roomEntering();
		$this->cleanup();
	}

    private function creation() {
        $unit = NfcUnitHandler::getGatesByEvent();

        $this->assertEquals(3, count($unit));
        $this->assertEquals('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF', $unit[0]->getPcbId());
        $this->assertEquals(2, $unit[2]->getType());
    }

    private function roomEntering() {
        $gateUnit = NfcUnitHandler::getGate(2);
        $notGateUnitWithInvalidData = NfcUnitHandler::getGate(1);

        $this->assertEquals(3, $gateUnit->getFromRoom()->getId());
        $this->assertEquals(1, $gateUnit->getToRoom()->getId());

        $this->expectException(TypeError::class);
        $this->assertEquals(null, $notGateUnitWithInvalidData->getFromRoom());
        $this->assertEquals(null, $notGateUnitWithInvalidData->getToRoom());
    }

	private function cleanup() {

	}
}
?>
