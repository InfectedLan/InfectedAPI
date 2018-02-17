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

require_once 'handlers/roomhandler.php';
require_once 'handlers/nfcloghandler.php';
require_once 'handlers/nfccardhandler.php';
require_once 'handlers/nfcunithandler.php';
require_once 'database.php';

/*
 * RoomTest
 *
 * Tests the room structure
 *
 */
class RoomTest extends TestCase {
    public function test() {
        $this->integrityTest();
        $this->creationTest();
        $this->movementTest();
        $this->cleanup();
    }

    private function integrityTest() {
        $this->assertEquals(5, count(RoomHandler::getRooms()));
    }

    private function creationTest() {
        $preRoomCount = count(RoomHandler::getRooms());
        $newRoom = RoomHandler::createRoom("dummy room", false);

        $rooms = RoomHandler::getRooms();
        $this->assertEquals($preRoomCount+1, count($rooms));
        $this->assertEquals("dummy room", $rooms[count($rooms)-1]->getName());
        $this->assertEquals(false, $rooms[count($rooms)-1]->isTimeLimited());

        $newRoom = RoomHandler::createRoom("dummy room v2", true);

        $rooms = RoomHandler::getRooms();
        $this->assertEquals($preRoomCount+2, count($rooms));
        $this->assertEquals("dummy room v2", $rooms[count($rooms)-1]->getName());
        $this->assertEquals(true, $rooms[count($rooms)-1]->isTimeLimited());

        $this->assertEquals(true, RoomHandler::getRoom(5)->isTimeLimited());
        $this->assertEquals(false, RoomHandler::getRoom(4)->isTimeLimited());
    }

    private function movementTest() {
        $testRoom = RoomHandler::getRoom(1); //Crew room
        $testCard = NfcCardHandler::getCard(1);
        $testUser = $testCard->getUser();

        $toCrewArea = NfcUnitHandler::getGate(2);
        $fromCrewArea = NfcUnitHandler::getGate(4);

        $currentInRoom = RoomHandler::getLogEntriesInRoom($testRoom);
        $this->assertEquals(0, count($currentInRoom));

        NfcLogHandler::createLogEntry($testCard, $toCrewArea, true);

        $currentInRoom = RoomHandler::getLogEntriesInRoom($testRoom);
        $this->assertEquals(1, count($currentInRoom));
        $this->assertEquals(1, $currentInRoom[0]->getCard()->getId());

        NfcLogHandler::createLogEntry($testCard, $fromCrewArea, true);

        $currentInRoom = RoomHandler::getLogEntriesInRoom($testRoom);
        $this->assertEquals(0, count($currentInRoom));

        NfcLogHandler::createLogEntry($testCard, $fromCrewArea, false);

        $currentInRoom = RoomHandler::getLogEntriesInRoom($testRoom);
        $this->assertEquals(1, count($currentInRoom));
        $this->assertEquals(1, $currentInRoom[0]->getCard()->getId());
    }

    private function cleanup() {

    }
}
?>
