<?php
require_once 'testApi/TestSuite.php';
require_once 'testApi/TestResult.php';

require_once 'handlers/userhandler.php';
require_once 'objects/user.php';

/*
 * HEY!
 * 
 * I know this is overly commented, but that is the meaning. This is a "tutorial" on how we assert stuff, and so i explain a lot of stuff
 * If you are learning test writing from this, try to learn the way of thinking from reading the code :)
 *
 * - Liam
 */
/*
 * UserTestSuite
 *
 * Responsible for testing UserHandler and the User object
 *
 */
class UserTestSuite extends TestSuite {
	//Override this
	public function test() {
		$this->userCreationTest();
	}

	private function userCreationTest() {
		//We expect 21 users to exist from the deployment code
		$users = UserHandler::getUsers(); //Get users
		$this->assert_greater_than(count($users), 0); //This asserts if the number of users is 21. It will fail if the left side does not equal the right side

		//Check that the user does not exist. This is done to test that the function does not return bogus data, and is a reccomended thing to test. Test everything, basically.
		$user = UserHandler::getUserByIdentifier("assertUser");
		$this->assert_equals($user, null);
		//Check that we can get the user by email
		$user = UserHandler::getUserByIdentifier("assertUser@infected.no");
		$this->assert_equals($user, null);

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

		if ($this->assert_not_equals($createdUser, null)) {
			//Check that we can get the user by username
			$user = UserHandler::getUserByIdentifier("assertUser");
			$this->assert_not_equals($user, null);
			//Check that we can get the user by email
			$email_user = UserHandler::getUserByIdentifier("assertUser@infected.no");
			$this->assert_not_equals($email_user, null);

			//Check if the two accounts are the same account
			$this->assert_equals($user->getId(), $email_user->getId());
			//Check if this is the user we inserted
			$this->assert_equals($user->getId(), $createdUser->getId());

			//Check that the fields we inserted are intact
			$this->assert_equals($user->getFirstname(), "assertionFirstname");
			$this->assert_equals($user->getLastname(), "assertionLastname");
			$this->assert_equals($user->getUsername(), "assertUser");
			$this->assert_equals($user->getPassword(), "32cdb619196200050ab0af581a10fb83cfc63b1a20f58d4bafb6313d55a3f0e9");
			$this->assert_equals($user->getEmail(), "assertUser@infected.no");
			$this->assert_equals($user->getBirthdate(), 890953200);
			$this->assert_equals($user->getGenderAsString(), "Gutt");
			$this->assert_equals($user->getPhoneAsString(), "12 34 56 78");
			$this->assert_equals($user->getPhone(), 12345678);
			$this->assert_equals($user->getAddress(), "Test address");
			$this->assert_equals($user->getPostalCode(), "1337");
			$this->assert_equals($user->getNickname(), "AssertNick");
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
		
		if ($this->assert_not_equals($createdUser, null)) {
			$user = UserHandler::getUserByIdentifier("assertGirl");

			$this->assert_not_equals($user, null);

			$this->assert_equals($user->getGenderAsString(), "Jente");
		}
	}
}
?>