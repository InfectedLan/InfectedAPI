<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/emergencycontacthandler.php';
require_once 'objects/user.php';

class UserHandler {
	/* 
	 * Get an user by the internal id.
	 */
	public static function getUser($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_users . '` 
									  WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
							
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new User($row['id'], 
							$row['firstname'], 
							$row['lastname'], 
							$row['username'], 
							$row['password'], 
							$row['email'], 
							$row['birthdate'], 
							$row['gender'], 
							$row['phone'], 
							$row['address'], 
							$row['postalcode'], 
							$row['nickname']);
		}
	}
	
	/* 
	 * Get user by it's username.
	 */
	public static function getUserByName($username) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_users . '` 
									  WHERE `username` = \'' . $con->real_escape_string($username) . '\' 
									  OR `email` = \'' . $con->real_escape_string($username) . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return self::getUser($row['id']);
		}
	}
	
	/* 
	 * Get a list of all users.
	 */
	public static function getUsers() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_users . '`
									  ORDER BY `firstname` ASC;');
									  
		$userList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($userList, self::getUser($row['id']));
		}
		
		MySQL::close($con);

		return $userList;
	}

	/*
	 * Returns all users that have one or more permission values in the permissions table.
	 */
	public static function getPermissionUsers() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT DISTINCT `userId` FROM `' . Settings::db_table_infected_userpermissions . '`;');
		
		$userList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($userList, self::getUser($row['userId']));
		}
		
		MySQL::close($con);

		return $userList;
	}
	
	/* 
	 * Get a list of all users which is member in a group
	 */
	public static function getMemberUsers() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `' . Settings::db_table_infected_users . '`.`id` FROM `' . Settings::db_table_infected_users . '`
									  LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '` ON `' . Settings::db_table_infected_users . '`.`id` = `userId`
									  WHERE `groupId` IS NOT NULL 
									  ORDER BY `firstname` ASC;');
		
		$userList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($userList, self::getUser($row['id']));
		}
		
		MySQL::close($con);
		
		return $userList;
	}
	
	/* 
	 * Get a list of all users which is not member in a group
	 */
	public static function getNonMemberUsers() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `' . Settings::db_table_infected_users . '`.`id` FROM ' . Settings::db_table_infected_users . ' 
									  LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '` ON `' . Settings::db_table_infected_users . '`.`id` = `userId`
									  WHERE `groupId` IS NULL 
									  ORDER BY `firstname` ASC;');
		
		$userList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($userList, self::getUser($row['id']));
		}
		
		MySQL::close($con);
		
		return $userList;
	}
	
	/*
	 * Create a new user
	 */
	public static function createUser($firstname, $lastname, $username, $password, $email, $birthDate, $gender, $phone, $address, $postalCode, $nickname) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_users . '` (`firstname`, `lastname`, `username`, `password`, `email`, `birthdate`, `gender`, `phone`, `address`, `postalcode`, `nickname`) 
							VALUES (\'' . $con->real_escape_string($firstname) . '\', 
									\'' . $con->real_escape_string($lastname) . '\', 
									\'' . $con->real_escape_string($username) . '\', 
									\'' . $con->real_escape_string($password) . '\', 
									\'' . $con->real_escape_string($email) . '\', 
									\'' . $con->real_escape_string($birthDate) . '\', 
									\'' . $con->real_escape_string($gender) . '\', 
									\'' . $con->real_escape_string($phone) . '\', 
									\'' . $con->real_escape_string($address) . '\', 
									\'' . $con->real_escape_string($postalCode) . '\', 
									\'' . $con->real_escape_string($nickname) . '\');');
									
		MySQL::close($con);
	}
	
	/* 
	 * Update a user
	 */
	public static function updateUser($id, $firstname, $lastname, $username, $password, $email, $birthDate, $gender, $phone, $address, $postalCode, $nickname) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_users . '` 
							SET `firstname` = \'' . $con->real_escape_string($firstname) . '\', 
								`lastname` = \'' . $con->real_escape_string($lastname) . '\', 
								`username` = \'' . $con->real_escape_string($username) . '\', 
								`password` = \'' . $con->real_escape_string($password) . '\', 
								`email` = \'' . $con->real_escape_string($email) . '\', 
								`birthdate` = \'' . $con->real_escape_string($birthDate) . '\', 
								`gender` = \'' . $con->real_escape_string($gender) . '\', 
								`phone` = \'' . $con->real_escape_string($phone) . '\', 
								`address` = \'' . $con->real_escape_string($address) . '\', 
								`postalcode` = \'' . $con->real_escape_string($postalCode) . '\', 
								`nickname` = \'' . $con->real_escape_string($nickname) . '\' 
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		MySQL::close($con);
	}
	
	/* 
	 * Remove a user
	 */
	public static function removeUser($user) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_users . '` 
							WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\';');
		
		if ($user->hasEmergencyContact()) {
			EmergencyContactHandler::removeEmergenctContact($user);
		}
		
		MySQL::close($con);
	}
	
	/* 
	 * Update a users password
	 */
	public static function updateUserPassword($userId, $password) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_users . '` 
							SET `password` = \'' . $con->real_escape_string($password) . '\'
							WHERE `id` = \'' . $con->real_escape_string($userId) . '\';');
		
		MySQL::close($con);
	}
	
	/* 
	 * Check if a user with given username or email already exists.
	 */
	public static function userExists($username) {
		$con = MySQL::open(Settings::db_name_infected);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_users . '` 
									  WHERE `username` = \'' . $con->real_escape_string($username) . '\' 
									  OR `email` = \'' . $con->real_escape_string($username) . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		return $row ? true : false;
	}
	
	/*
	 * Lookup users by set values and return a list of users as result.
	 */
	public static function search($query) {
		$con = MySQL::open(Settings::db_name_infected);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_users . '` 
									  WHERE `firstname` LIKE \'%' . $con->real_escape_string($query) . '%\'
									  OR `lastname` LIKE \'%' . $con->real_escape_string($query) . '%\' 
									  OR `username` LIKE \'%' . $con->real_escape_string($query) . '%\' 
									  OR `email` LIKE \'%' . $con->real_escape_string($query) . '%\' 
									  OR `phone` LIKE \'%' . $con->real_escape_string($query) . '%\' 
									  OR `nickname` LIKE \'%' . $con->real_escape_string($query) . '%\' 
									  LIMIT 10;');
		
		$userList = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($userList, self::getUser($row['id']));
		}
		
		MySQL::close($con);
		
		return $userList;
	}
}
?>