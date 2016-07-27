<?php
use PHPUnit\Framework\TestCase;

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

		if ($this->assertNotEquals($createdUser, null)) {
			//Check that we can get the user by username
			$user = UserHandler::getUserByIdentifier("assertUser");
			$this->assertNotEquals($user, null);
			//Check that we can get the user by email
			$email_user = UserHandler::getUserByIdentifier("assertUser@infected.no");
			$this->assertNotEquals($email_user, null);

			//Check if the two accounts are the same account
			$this->assertEquals($user->getId(), $email_user->getId());
			//Check if this is the user we inserted
			$this->assertEquals($user->getId(), $createdUser->getId());

			//Check that the fields we inserted are intact
			$this->assertEquals($user->getFirstname(), "assertionFirstname");
			$this->assertEquals($user->getLastname(), "assertionLastname");
			$this->assertEquals($user->getUsername(), "assertUser");
			$this->assertEquals($user->getPassword(), "32cdb619196200050ab0af581a10fb83cfc63b1a20f58d4bafb6313d55a3f0e9");
			$this->assertEquals($user->getEmail(), "assertUser@infected.no");
			$this->assertEquals($user->getBirthdate(), 890953200);
			$this->assertEquals($user->getGenderAsString(), "Gutt");
			$this->assertEquals($user->getPhoneAsString(), "12 34 56 78");
			$this->assertEquals($user->getPhone(), 12345678);
			$this->assertEquals($user->getAddress(), "Test address");
			$this->assertEquals($user->getPostalCode(), "1337");
			$this->assertEquals($user->getNickname(), "AssertNick");
		}

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
		
		if ($this->assertNotEquals($createdUser, null)) {
			$user = UserHandler::getUserByIdentifier("assertGirl");

			$this->assertNotEquals($user, null);

			$this->assertEquals($user->getGenderAsString(), "Jente");
		}
	}
}
?>