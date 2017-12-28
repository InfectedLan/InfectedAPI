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
require_once 'handlers/applicationhandler.php';

/*
* Responsible for testing ApplicationHandler, the Application object.
*/
class ApplicationTest extends TestCase {
	public function creationTest() {

	}

	public function test() {
		$group = GroupHandler::getGroup(1);
		$user = UserHandler::getUser(1);

		// Create an application.
		$applicationCreate = ApplicationHandler::createApplication($group, $user, 'This is a test application.');
		$this->assertNotEquals(null, $applicationCreate);

		// Check if we can get an application already stored.
		$applicationGet = ApplicationHandler::getApplication($applicationCreate->getId());
		$this->assertNotEquals(null, $applicationGet);

		// Check if we can get an array of applications.
		$applicationsGet = ApplicationHandler::getApplications();
		$this->assertGreaterThan(0, count($applicationsGet));

		/*
		getPendingApplications(Event $event = null): array {
		getPendingApplicationsByGroup(Group $group, Event $event = null): array {
		getQueuedApplications(Event $event = null) {
		getQueuedApplicationsByGroup(Group $group, Event $event = null): array {
		getAcceptedApplications(Event $event = null): array {
		getAcceptedApplicationsByGroup(Group $group, Event $event = null): array {
		getRejectedApplications(Event $event = null): array {
		getRejectedApplicationsByGroup(Group $group, Event $event = null): array {
		getPreviousApplications(Event $event = null): array
		getPreviousApplicationsByGroup(Group $group, Event $event = null): array
		acceptApplication(Application $application, User $user, bool $notify) {
		rejectApplication(Application $application, User $user, string $comment, bool $notify) {
		closeApplication(Application $application, User $user) {
		queueApplication(Application $application, User $user, bool $notify) {
		unqueueApplication(Application $application, User $user) {
		isQueued(Application $application): bool {
		*/

		// Check if we can get an array of applications by user.
		$applicationsGetUser = ApplicationHandler::getUserApplications($user);
		$this->assertGreaterThan(0, count($applicationsGetUser));

		/*
		// TODO: Make sure the user is in the right group.
		hasUserApplicationsByGroup(User $user, Group $group, Event $event = null): bool {
		getUserApplicationsByGroup(User $user, Group $group, Event $event = null): array {
		*/

		// Check if we can remove an application.
		ApplicationHandler::removeApplication($applicationCreate);
		$this->assertEquals(null, ApplicationHandler::getApplication($applicationCreate->getId()));

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////


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