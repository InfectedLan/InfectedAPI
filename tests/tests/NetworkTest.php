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

use PHPUnit\Framework\TestCase;

require_once 'database.php';
require_once 'handlers/networkhandler.php';

/*
* Responsible for testing NetworkHandler, the Network and NetworkType object.
*/
class NetworkTest extends TestCase {
    public function test() {
        // Create a new network, and check if it was created succesfully.
        $network = NetworkHandler::createNetwork("network_test", 'Network Test', 'This is a network test.', 1);
        $this->assertEquals('network_test', $network->getName());

        // Updating the network.
        $network = NetworkHandler::updateNetwork($network, 'network_test_2', 'Network Test 2', 'This is a network test 2.', 2);
        $this->assertEquals('network_test_2', $network->getName());

        // Check that a network with id one exists.
        $network = NetworkHandler::getNetwork($network->getId());
        $this->assertNotEquals(null, $network);

        // Check that network count is greater than zero.
        $networks = NetworkHandler::getNetworks();
        $this->assertGreaterThan(0, count($networks));

        // Removing the network.
        NetworkHandler::removeNetwork($network);
        $this->assertEquals(null, NetworkHandler::getNetwork($network->getId()));

        // Check that network type count is greater than zero.
        $networkTypes = NetworkHandler::getNetworkTypes();
        $this->assertGreaterThan(0, count($networkTypes));

        // Create a new network type, and check if it was created succesfully.
        $networkType = NetworkHandler::createNetworkType('network_type_test', 'Network Type Test', 'Wireless-802.11');
        $this->assertEquals('network_type_test', $networkType->getName());

        // Updating the network type.
        $networkType = NetworkHandler::updateNetworkType($networkType, 'network_type_test_2', 'Network Type Test 2', 'Wireless-802.11');
        $this->assertEquals('network_type_test_2', $networkType->getName());

        // Check that a network type with id one exists.
        $networkType = NetworkHandler::getNetworkType($networkType->getId());
        $this->assertNotEquals(null, $networkType);

        /* TODO: Implement tests for this.
        public static function getNetworkTypeByPortType(string $portType): ?NetworkType {
        public static function hasNetworkAccess(User $user, NetworkType $networkType, Event $event = null): bool {
        public static function getNetworkByUser(User $user, NetworkType $networkType, Event $event = null): Network {
        */

        Database::cleanup();
    }
}