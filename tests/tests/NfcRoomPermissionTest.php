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

require_once 'handlers/nfcroompermissionhandler.php';
require_once 'handlers/userhandler.php';
require_once 'database.php';

/*
 * NfcRoomPermissionTest
 *
 * Permissions for rooms
 *
 */
class NfcRoomPermissionTest extends TestCase {
    public function test() {
        $this->integrityTest();
        $this->permissionTest();
        $this->creationTest();
        $this->dbFetchTest();
        $this->cleanup();
    }

    private function integrityTest() {
        $testRoom = RoomHandler::getRoom(1);
        $permissions = NfcRoomPermissionHandler::getPermissionsByRoom($testRoom);
        $this->assertEquals(1, count($permissions));
    }

    private function permissionTest() {
        $testRoom = RoomHandler::getRoom(1);
        $testUserWithGroup = UserHandler::getUser(1);
        $testUserWithoutGroup = UserHandler::getUser(10);

        $this->assertEquals(true, NfcRoomPermissionHandler::hasUserPermission($testRoom, $testUserWithGroup));
        $this->assertEquals(false, NfcRoomPermissionHandler::hasUserPermission($testRoom, $testUserWithoutGroup));
    }

    private function dbFetchTest() {
        $getterTest = NfcRoomPermissionHandler::getPermission(1);
        $this->assertNotEquals(null, $getterTest);
    }

    private function creationTest() {
        $testRoom = RoomHandler::getRoom(1);
        $testUserWithoutGroup = UserHandler::getUser(10);

        $created = NfcRoomPermissionHandler::createPermission($testRoom, 0, 10);
        $this->assertEquals(true, NfcRoomPermissionHandler::hasUserPermission($testRoom, $testUserWithoutGroup));

        $this->assertNotEquals(null, $created);
        $this->assertEquals(0, $created->getPermissionType());
        $this->assertEquals(10, $created->getPermissionArg());
    }


    private function cleanup() {

    }
}
?>
