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
require_once 'handlers/bongentitlementhandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/grouphandler.php';
require_once 'objects/bongentitlement.php';
require_once 'database.php';

/* 
 * BongTypeTest
 *
 * Tests objects/bongtype.php and handlers/bongtypehandler.php
 *
 */
class BongEntitlementTest extends TestCase {
	public function test() {
		$this->prep();
		$this->creationTest();
		$this->cleanup();
	}

	private function prep() {
		$user = UserHandler::getUser(1);
		if(!$user->isGroupMember()) {
			GroupHandler::addGroupMember($user, GroupHandler::getGroup(1));
		}
	}

	private function creationTest() {
		$type = BongTypeHandler::getBongType(1);
		$types = BongTypeHandler::getBongTypes();

		$user = UserHandler::getUser(1);

		$entitlements = BongEntitlementHandler::getBongEntitlements($type); //No user, all entitlements
		$this->assertEquals(count($entitlements), 1);

		foreach($entitlements as $entitlement) {
			$this->assertEquals($entitlement, BongEntitlementHandler::getBongEntitlement($entitlement->getId()));
		}

		//Tests additive entitlements alone, and checks that they are created properly
		$new = BongEntitlementHandler::createBongEntitlement($type, 1, BongEntitlement::APPEND_TYPE_ADDITIVE, BongEntitlement::ENTITLEMENT_TYPE_USER, 1); //1 additive bong for user 1
		$entitlements = BongEntitlementHandler::getBongEntitlements($type); //Current event
		$this->assertEquals(2, count($entitlements));
		$this->assertEquals($entitlements[count($entitlements)-1], $new);

		$entitlements = BongEntitlementHandler::getBongEntitlements($type, $user); //Current event and current user
		$this->assertEquals(2, count($entitlements));
		$this->assertEquals($new, $entitlements[0]);

		$this->assertEquals(7, BongEntitlementHandler::calculateBongEntitlementByUser($type, $user));

		//Tests proper handling of exclusive entitlements for users
		$new2 = BongEntitlementHandler::createBongEntitlement($types[1], 3, BongEntitlement::APPEND_TYPE_EXCLUSIVE, BongEntitlement::ENTITLEMENT_TYPE_USER, 1); //1 additive bong for user 1
		$new3 = BongEntitlementHandler::createBongEntitlement($types[1], 7, BongEntitlement::APPEND_TYPE_EXCLUSIVE, BongEntitlement::ENTITLEMENT_TYPE_USER, 1); //1 additive bong for user 1

		$this->assertEquals(7, BongEntitlementHandler::calculateBongEntitlementByUser($types[1], $user)); //Tests exclusive entitlements

		//Test additive + exclusive bong entitlements for users
		$new3 = BongEntitlementHandler::createBongEntitlement($types[1], 7, BongEntitlement::APPEND_TYPE_ADDITIVE, BongEntitlement::ENTITLEMENT_TYPE_USER, 1); //7 additive bongs for user 1

		$this->assertEquals(14, BongEntitlementHandler::calculateBongEntitlementByUser($types[1], $user)); //Tests exclusive entitlements
	}

	private function cleanup() {

	}
}
?>