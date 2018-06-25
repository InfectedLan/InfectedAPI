<?php
use PHPUnit\Framework\TestCase;

require_once 'database.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/eventhandler.php';
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
		$this->userSanityTest();
		$this->friendTest();
		$this->userCreationTest();
        $this->curfewTest();
	}

	private function userSanityTest() {
		//If this fails we aren't getting far...
		$user = UserHandler::getUser(1);
		$this->assertNotEquals(null, $user);
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

		//public static function createUser(, int $birthDate, bool $gender, int $phone, string $address, int $postalCode, ?string $nickname): User {


		//Let's create another user
		$createdUser = UserHandler::createUser("assertFirstname",
											   "assertLastname",
											   "assertUser",
											   "32cdb619196200050ab0af581a10fb83cfc63b1a20f58d4bafb6313d55a3f0e9",
											   "assertUser@infected.no",
											   "1998-03-27 00:00:00",
											   0,
											   12345678,
											   "Address",
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
		$this->assertEquals("assertFirstname", $user->getFirstname());
		$this->assertEquals("assertLastname", $user->getLastname());
		$this->assertEquals("assertUser", $user->getUsername());
		$this->assertEquals("32cdb619196200050ab0af581a10fb83cfc63b1a20f58d4bafb6313d55a3f0e9", $user->getPassword());
		$this->assertEquals("assertUser@infected.no", $user->getEmail());
		$this->assertEquals(strtotime("1998-03-27 00:00:00"), $user->getBirthdate());
		$this->assertEquals($user->getAge(EventHandler::getEvent(7)), 18); //True story
		$this->assertEquals($user->getAge(EventHandler::getEvent(6)), 17);
		$this->assertEquals(true, $user->getGender());
		$this->assertEquals("(+47) 12 34 56 78", $user->getPhoneAsString());
		$this->assertEquals(12345678, $user->getPhone());
		$this->assertEquals("Address", $user->getAddress());
		$this->assertEquals(1337, $user->getPostalCode());
		$this->assertEquals("AssertNick", $user->getNickname());

		//One last thing, check if girl string also works
		$createdUser = UserHandler::createUser("assertGirlFirstname",
											   "assertGirlLastname",
											   "assertGirl",
											   "32cdb619196200050ab0af581a10fb83cfc63b1a20f58d4bafb6313d55a3f0e9",
											   "assertGirl@infected.no",
											   "1998-03-27 00:00:00",
											   1,
											   12345678,
											   "Address",
											   1337,
											   "AssertGirl");

		$this->assertNotEquals(null, $createdUser);
		$user = UserHandler::getUserByIdentifier("assertGirl");

		$this->assertNotEquals(null, $user);
		$this->assertEquals(false, $user->getGender());

		Database::cleanup();
	}

	private function friendTest() {
		$user1 = UserHandler::getUser(1);
		$user2 = UserHandler::getUser(2);

		$this->assertEquals(true, $user1->isFriendsWith($user2));
		$this->assertEquals(true, $user2->isFriendsWith($user1));

		$this->assertEquals(1, count($user1->getFriends()));
		$this->assertEquals(1, count($user2->getFriends()));

		$this->assertEquals(0, count($user1->getPendingFriendsTo()));
		$this->assertEquals(0, count($user2->getPendingFriendsTo()));

		$this->assertEquals(0, count($user1->getPendingFriendsFrom()));
		$this->assertEquals(0, count($user2->getPendingFriendsFrom()));

		$user1->removeFriend($user2);

		$this->assertEquals(false, $user1->isFriendsWith($user2));
		$this->assertEquals(false, $user2->isFriendsWith($user1));

		$this->assertEquals(0, count($user1->getFriends()));
		$this->assertEquals(0, count($user2->getFriends()));

		$this->assertEquals(0, count($user1->getPendingFriendsTo()));
		$this->assertEquals(0, count($user2->getPendingFriendsTo()));

		$this->assertEquals(0, count($user1->getPendingFriendsFrom()));
		$this->assertEquals(0, count($user2->getPendingFriendsFrom()));

		$user1->addFriend($user2);

		$this->assertEquals(false, $user1->isFriendsWith($user2));
		$this->assertEquals(false, $user2->isFriendsWith($user1));

		$this->assertEquals(0, count($user1->getFriends()));
		$this->assertEquals(0, count($user2->getFriends()));

		$this->assertEquals(0, count($user1->getPendingFriendsTo()));
		$this->assertEquals(1, count($user2->getPendingFriendsTo()));

		$this->assertEquals(1, count($user1->getPendingFriendsFrom()));
		$this->assertEquals(0, count($user2->getPendingFriendsFrom()));

		$user1->acceptFriend($user2);

		$this->assertEquals(true, $user1->isFriendsWith($user2));
		$this->assertEquals(true, $user2->isFriendsWith($user1));

		$this->assertEquals(1, count($user1->getFriends()));
		$this->assertEquals(1, count($user2->getFriends()));

		$this->assertEquals(0, count($user1->getPendingFriendsTo()));
		$this->assertEquals(0, count($user2->getPendingFriendsTo()));

		$this->assertEquals(0, count($user1->getPendingFriendsFrom()));
		$this->assertEquals(0, count($user2->getPendingFriendsFrom()));

		$user1->removeFriend($user2);

		$this->assertEquals(false, $user1->isFriendsWith($user2));
		$this->assertEquals(false, $user2->isFriendsWith($user1));

		$this->assertEquals(0, count($user1->getFriends()));
		$this->assertEquals(0, count($user2->getFriends()));

		$this->assertEquals(0, count($user1->getPendingFriendsTo()));
		$this->assertEquals(0, count($user2->getPendingFriendsTo()));

		$this->assertEquals(0, count($user1->getPendingFriendsFrom()));
		$this->assertEquals(0, count($user2->getPendingFriendsFrom()));

		$user1->addFriend($user2);

		$user1->rejectFriend($user2);

		$this->assertEquals(false, $user1->isFriendsWith($user2));
		$this->assertEquals(false, $user2->isFriendsWith($user1));

		$this->assertEquals(0, count($user1->getFriends()));
		$this->assertEquals(0, count($user2->getFriends()));

		$this->assertEquals(0, count($user1->getPendingFriendsTo()));
		$this->assertEquals(0, count($user2->getPendingFriendsTo()));

		$this->assertEquals(0, count($user1->getPendingFriendsFrom()));
		$this->assertEquals(0, count($user2->getPendingFriendsFrom()));
	}

	private function curfewTest() {
        $curfewTestUser = UserHandler::getUser(1);
        $this->assertEquals(false, UserOptionHandler::canBypassCurfew($curfewTestUser));
        UserOptionHandler::setCanBypassCurfew($curfewTestUser, false);
        $this->assertEquals(false, UserOptionHandler::canBypassCurfew($curfewTestUser));
        UserOptionHandler::setCanBypassCurfew($curfewTestUser, true);
        $this->assertEquals(true, UserOptionHandler::canBypassCurfew($curfewTestUser));
        UserOptionHandler::setCanBypassCurfew($curfewTestUser, false);

        $this->assertEquals(false, $curfewTestUser->getCurfew());
        $curfewTestUser->setCurfew(true);
        $this->assertEquals(true, $curfewTestUser->getCurfew());
    }
}