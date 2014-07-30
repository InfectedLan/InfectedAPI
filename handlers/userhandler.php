<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/user.php';

class UserHandler {
	/* 
	 * Get an user by the internal id.
	 */
	public static function getUser($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_users . '` 
									  WHERE `id` = \'' . $id . '\';');
							
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
									  WHERE `username` = \'' . $username . '\' 
									  OR `email` = \'' . $username . '\';');
									  
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
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_users . '`;');
		
		$userList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($userList, self::getUser($row['id']));
		}
		
		MySQL::close($con);

		return $userList;
	}

	/* Create new user */
	public static function createUser($firstname, $lastname, $username, $password, $email, $birthDate, $gender, $phone, $address, $postalCode, $nickname) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_users . '` (`firstname`, `lastname`, `username`, `password`, `email`, `birthdate`, `gender`, `phone`, `address`, `postalcode`, `nickname`) 
							VALUES (\'' . $firstname . '\', 
									\'' . $lastname . '\', 
									\'' . $username . '\', 
									\'' . $password . '\', 
									\'' . $email . '\', 
									\'' . $birthDate . '\', 
									\'' . $gender . '\', 
									\'' . $phone . '\', 
									\'' . $address. '\', 
									\'' . $postalCode . '\', 
									\'' . $nickname . '\');');
									
		MySQL::close($con);
	}
	
	/* Remove user */
	public static function removeUser($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_users . '` 
							WHERE `id` = \'' . $id . '\';');
		
		MySQL::close($con);
	}
	
	/* Update user */
	public static function updateUser($id, $firstname, $lastname, $username, $password, $email, $birthDate, $gender, $phone, $address, $postalCode, $nickname) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_users . '` 
							SET `firstname` = \'' . $firstname . '\', 
								`lastname` = \'' . $lastname . '\', 
								`username` = \'' . $username . '\', 
								`password` = \'' . $password . '\', 
								`email` = \'' . $email . '\', 
								`birthdate` = \'' . $birthDate . '\', 
								`gender` = \'' . $gender . '\', 
								`phone` = \'' . $phone . '\', 
								`address` = \'' . $address . '\', 
								`postalcode` = \'' . $postalCode . '\', 
								`nickname` = \'' . $nickname . '\' 
							WHERE `id` = \'' . $id . '\';');
		
		MySQL::close($con);
	}
	
	/* Update users password */
	public static function updateUserPassword($userId, $password) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_users . '` 
							SET `password` = \'' . $password . '\'
							WHERE `id` = \'' . $userId . '\';');
		
		MySQL::close($con);
	}
	
	/* Get a list of all users which is member in a group */
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
	
	/* Get a list of all users which is not member in a group */
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
	 * Check if a user with given username or email already exists.
	 */
	public static function userExists($username) {
		$con = MySQL::open(Settings::db_name_infected);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_users . '` 
									  WHERE `username` = \'' . $username . '\' 
									  OR `email` = \'' . $username . '\';');
									  
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
									  WHERE `firstname` LIKE "%' . $query . '%"' . ' 
									  OR `lastname` LIKE "%' . $query . '%" 
									  OR `username` LIKE "%' . $query . '%" 
									  OR `email` LIKE "%' . $query . '%" 
									  OR `nickname` LIKE "%' . $query . '%" 
									  LIMIT 10;');
		
		$userList = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($userList, self::getUser($row['id']));
		}
		
		MySQL::close($con);
		
		return $userList;
	}
	
	/* 
	 * Get the registration code for a given user.
	 */
	public static function getRegistrationCode($userId) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `code` FROM `' . Settings::db_table_infected_registrationcodes . '` 
									  WHERE `userId` = \'' . $userId . '\';');
							
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return $row['code'];
		}
	}
	
	public static function createRegistrationCode($userId) {
		$code = hash('md5', $userId * rand()); 
		
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_registrationcodes . '` (`userId`, `code`) 
							VALUES (\'' . $userId . '\', 
									\'' . $code . '\');');
									
		MySQL::close($con);
		
		return $code;
	}
	
	public static function removeRegistrationCode($code) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_registrationcodes . '` 
							WHERE `code` = \'' . $code . '\';');
		
		MySQL::close($con);
	}
}
?>