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
require_once 'handlers/locationhandler.php';

/*
 * LocationTestSuite
 *
 * Responsible for testing LocationHandler and the Location object.
 *
 */
class LocationTest extends TestCase {
	public function test() {
		$this->locationCreationTest();
	}

	private function locationCreationTest() {
		$location = LocationHandler::getLocation(1);

		/*
		 * Testing Location.
		 */

		// public function getName()
		$this->assertNotEquals($location->getName(), null);

		// public function getTitle()
		$this->assertNotEquals($location->getTitle(), null);

		/*
		 * Testing LocationHandler.
		 */

		// public static function getLocation($id)
		$location = LocationHandler::getLocation(1);
		$this->assertNotEquals($location, null);

		// public static function getLocations()
		$locationList = LocationHandler::getLocations();
		$this->assertGreaterThan(0, count($locationList));

		Database::cleanup();
	}
}
?>
