<?php
use PHPUnit_Framework_TestCase as TestCase;

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
		$this->assert_not_equals($location->getName(), null);

		// public function getTitle()
		$this->assert_not_equals($location->getTitle(), null);

		/*
		 * Testing LocationHandler.
		 */

		// public static function getLocation($id)
		$location = LocationHandler::getLocation(1);
		$this->assert_not_equals($location, null);

		// public static function getLocations()
		$locationList = LocationHandler::getLocations();
		$this->assert_greater_than(count($locationList), 0);
	}
}
?>