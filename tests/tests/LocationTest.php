<?php
use PHPUnit\Framework\TestCase;

require_once 'handlers/locationhandler.php';
require_once 'database.php';

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
		$this->assertGreaterThan(count($locationList), 0);

		Database::cleanup():
	}
}
?>