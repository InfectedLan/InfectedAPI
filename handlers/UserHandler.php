<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'user.php';

	class UserHandler {
		/* Get user by id */
		public static function getUser($id) {
			$con = MySQL::open(Settings::db_name_infected);
			
			$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_users . ' WHERE id=\'' . $id . '\'');
			$row = mysqli_fetch_array($result);
			
			MySQL::close($con);

			if ($row) {
				return new User($row['id'], 
								$row['firstname'], 
								$row['lastname'], 
								$row['username'], 
								$row['password'], 
								$row['email'], 
								$row['birthDate'], 
								$row['gender'], 
								$row['phone'], 
								$row['address'], 
								$row['postalCode'], 
								$row['nickname']);
			}
		}
		
		/* Get a list of all users */
		public static function getUsers() {
			$con = MySQL::open(Settings::db_name_infected);
			
			$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_users);
			
			$userList = array();
			
			while ($row = mysqli_fetch_array($result)) {
				array_push($userList, new User($row['id'], 
											   $row['firstname'], 
											   $row['lastname'], 
											   $row['username'], 
											   $row['password'], 
											   $row['email'], 
											   $row['birthDate'], 
											   $row['gender'], 
											   $row['phone'], 
											   $row['address'], 
											   $row['postalCode'], 
											   $row['nickname']));
			}
			
			MySQL::close($con);

			return $userList;
		}
		
		/* Check if user already exists */
		public static function userExists($username) {
			$con = MySQL::open(Settings::db_name_infected);

			$result = mysqli_query($con, 'SELECT EXISTS (SELECT * FROM ' . Settings::db_table_users . ' WHERE username=\'' . $username . '\' OR email=\'' . $username . '\')');
			$row = mysqli_fetch_array($result);
			
			MySQL::close($con);
			
			return $row ? true : false;
		}
		
		/* Get user by name */
		public static function getUserByName($username) {
			$con = MySQL::open(Settings::db_name_infected);
			
			$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_users . ' WHERE username=\'' . $username . '\' OR email=\'' . $username . '\'');
			$row = mysqli_fetch_array($result);
			
			MySQL::close($con);

			if ($row) {
				return self::getUser($row['id']);
			}
		}
		
		/* Create new user */
		public static function createUser($firstname, $lastname, $username, $password, $email, $birthDate, $gender, $phone, $address, $postalCode, $nickname) {
			$con = MySQL::open(Settings::db_name_infected);
			
			mysqli_query($con, 'INSERT INTO ' . Settings::db_table_users . ' (firstname, lastname, username, password, email, birthDate, gender, phone, address, postalCode, nickname) 
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
										\'' . $nickname . '\')');
										
			MySQL::close($con);
		}
		
		/* Remove user */
		public static function removeUser($id) {
			$con = MySQL::open(Settings::db_name_infected);
			
			mysqli_query($con, 'DELETE FROM ' . Settings::db_table_users . ' WHERE id=\'' . $id . '\'');
			
			MySQL::close($con);
		}
		
		/* Update user */
		public static function updateUser($firstname, $lastname, $username, $password, $email, $birthDate, $gender, $phone, $address, $postalCode, $nickname) {
			$con = MySQL::open(Settings::db_name_infected);
			
			mysqli_query($con, 'UPDATE ' . Settings::db_table_users . ' 
								SET firstname=\'' . $firstname . '\', 
									lastname=\'' . $lastname . '\', 
									username=\'' . $username . '\', 
									password=\'' . $password . '\', 
									email=\'' . $email . '\', 
									birthDate=\'' . $birthDate . '\', 
									gender=\'' . $gender . '\', 
									phone=\'' . $phone . '\', 
									address=\'' . $address . '\', 
									postalCode=\'' . $postalCode . '\', 
									nickname=\'' . $nickname . '\' 
								WHERE id=\'' . $id . '\'');
			
			MySQL::close($con);
		}
		
		/* Get a list of all users which is member in a group */
		public static function getMemberUsers() {
			$con = MySQL::open(Settings::db_name_infected);
			
			$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_users . '
										  LEFT JOIN infecrjn_crew.' . Settings::db_table_memberof . ' ON ' . Settings::db_table_users . '.id = userId
										  WHERE groupId IS NOT NULL 
										  ORDER BY firstname ASC');
			
			$userList = array();
			
			while ($row = mysqli_fetch_array($result)) {
				array_push($userList, new User($row['id'], 
											   $row['firstname'], 
											   $row['lastname'], 
											   $row['username'], 
										       $row['password'], 
										       $row['email'], 
											   $row['birthDate'], 
											   $row['gender'], 
											   $row['phone'], 
											   $row['address'], 
											   $row['postalCode'], 
											   $row['nickname']));
			}
			
			MySQL::close($con);
			
			return $userList;
		}
		
		/* Get a list of all users which is not member in a group */
		public static function getNonMemberUsers() {
			$con = MySQL::open(Settings::db_name_infected);
			
			$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_users . ' 
										  LEFT JOIN infecrjn_crew.' . Settings::db_table_memberof . ' ON ' . Settings::db_table_users . '.id = userId 
										  WHERE groupId IS NULL 
										  ORDER BY firstname ASC');
			
			$userList = array();
			
			while ($row = mysqli_fetch_array($result)) {
				array_push($userList, new User($row['id'], 
											   $row['firstname'], 
											   $row['lastname'], 
											   $row['username'], 
										       $row['password'], 
										       $row['email'], 
											   $row['birthDate'], 
											   $row['gender'], 
											   $row['phone'], 
											   $row['address'], 
											   $row['postalCode'], 
											   $row['nickname']));
			}
			
			MySQL::close($con);
			
			return $userList;
		}
	}
?>