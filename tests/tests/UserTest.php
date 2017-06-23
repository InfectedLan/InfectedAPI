<?php
require_once 'PHPUnit/Autoload.php';

require_once 'handlers/userhandler.php';
require_once 'objects/user.php';

/* 
 * UserTest
 *
 * Responsible for testing UserHandler and the User object
 *
 * First class converted to phpunit, lol
 */
class UserTest extends TestCase {
	public function test() {
		$this->userCreationTest();
	}

	private function userCreationTest() {
		//We expect 21 users to exist from the deployment code
		$users = UserHandler::getUsers(); //Get users
		$this->assertGreaterThan(0, count($users)); //This asserts if the number of users is 21.

		//Check that the user does not exist. This is done to test that the function does not return bogus data, and is a reccomended thing to test. Test everything, basically.
		$user = UserHandler::getUserByIdentifier("assertUser");
		$this->assertEquals(null, $user);
		//Check that we can get the user by email
		$user = UserHandler::getUserByIdentifier("assertUser@infected.no");
		$this->assertEquals(null, $user);

		//Let's create another user
		$createdUser = UserHandler::createUser("assertionFirstname", 
											   "assertionLastname", 
											   "assertUser", 
											   "32cdb619196200050ab0af581a10fb83cfc63b1a20f58d4bafb6313d55a3f0e9", 
											   "assertUser@infected.no", 
											   "1998-03-27 00:00:00", 
											   0, 
											   "12345678", 
											   "Test address", 
											   1337, 
											   "AssertNick");

		$this->assertNotEquals(null, $createdUser);
		//Check that we can get the user by username
		$user = UserHandler::getUserByIdentifier("assertUser");
		$this->assertNotEquals(null, $user);
		//Check that we can get the user by email
		$email_user = UserHandler::getUserByIdentifier("assertUser@infected.no");
		$this->assertNotEquals(null, $email_user);

		//Check if the two accounts are the same account
		$this->assertEquals($email_user->getId(), $user->getId());
		//Check if this is the user we inserted
		$this->assertEquals($createdUser->getId(), $user->getId());

		//Check that the fields we inserted are intact
		$this->assertEquals("assertionFirstname", $user->getFirstname());
		$this->assertEquals("assertionLastname", $user->getLastname());
		$this->assertEquals("assertUser", $user->getUsername());
		$this->assertEquals("32cdb619196200050ab0af581a10fb83cfc63b1a20f58d4bafb6313d55a3f0e9", $user->getPassword());
		$this->assertEquals("assertUser@infected.no", $user->getEmail());
		$this->assertEquals(890956800, $user->getBirthdate());
		$this->assertEquals("Gutt", $user->getGenderAsString());
		$this->assertEquals("12 34 56 78", $user->getPhoneAsString());
		$this->assertEquals(12345678, $user->getPhone());
		$this->assertEquals("Test address", $user->getAddress());
		$this->assertEquals("1337", $user->getPostalCode());
		$this->assertEquals("AssertNick", $user->getNickname());

		//One last thing, check if girl string also works
		$createdUser = UserHandler::createUser("assertionGirlFirst", 
											   "assertionGirlLast", 
											   "assertGirl", 
											   "32cdb619196200050ab0af581a10fb83cfc63b1a20f58d4bafb6313d55a3f0e9", 
											   "assertGirl@infected.no", 
											   "1998-03-27 00:00:00", 
											   1, 
											   "12345678", 
											   "Test address", 
											   1337, 
											   "AssertGirl");
		
		$this->assertNotEquals(null, $createdUser);
		$user = UserHandler::getUserByIdentifier("assertGirl");

		$this->assertNotEquals(null, $user);

		$this->assertEquals("Jente", $user->getGenderAsString());
	}
}
?>