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
		$this->creationTest();
		$this->cleanup();
	}

	private function creationTest() {
		$type = BongTypeHandler::getBongType(1);
		$entitlements = BongEntitlementHandler::getBongEntitlements($type); //No user, all entitlements
		$this->assertEquals(count($entitlements), 1);

		foreach($entitlements as $entitlement) {
			$this->assertEquals($entitlement, BongEntitlementHandler::getBongEntitlement($entitlement->getId()));
		}

		$new = BongEntitlementHandler::createBongEntitlement($type, 1, BongEntitlement::APPEND_TYPE_ADDITIE, BongEntitlement::ENTITLEMENT_TYPE_USER, 1); //1 additive bong for user 1

		$bongs = BongTypeHandler::getBongTypes(); //Current event
		$this->assertEquals(count($bongs), 4);

		$this->assertEquals($bongs[count($bongs)-1], $new);
	}

	private function cleanup() {

	}
}
?>