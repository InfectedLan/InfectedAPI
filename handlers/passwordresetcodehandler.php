<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/userhandler.php';

class PasswordResetCodeHandler {
	public static function createPasswordResetCode($user) {
		$code = bin2hex(openssl_random_pseudo_bytes(16));
		
		$con = MySQL::open(Settings::db_name_infected);
		
		if (!self::hasPasswordResetCode($user)) {
			mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_passwordresetcodes . '` (`userId`, `code`) 
								VALUES (\'' . $con->real_escape_string($user->getId()) . '\', 
										\'' . $con->real_escape_string($code) . '\');');
		} else {
			mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_passwordresetcodes . '` 
								SET `code` = \'' . $con->real_escape_string($code) . '\'
								WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\';');
		}
		
		MySQL::close($con);
		
		return $code;
	}
	
	public static function hasPasswordResetCode($user) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
									  WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\';');
							
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		return $row ? true : false;
	}
	
	public static function existsPasswordResetCode($code) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
									  WHERE `code` = \'' . $con->real_escape_string($code) . '\';');
							
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		return $row ? true : false;
	}
	
	public static function getUserFromPasswordResetCode($code) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `userId` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
									  WHERE `code` = \'' . $con->real_escape_string($code) . '\';');
							
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return UserHandler::getUser($row['userId']);
		}
	}
	
	public static function removePasswordResetCode($code) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
							WHERE `code` = \'' . $con->real_escape_string($code) . '\';');
		
		MySQL::close($con);
	}
	
	public static function removeUserPasswordResetCode($user) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
							WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\';');
		
		MySQL::close($con);
	}
}
?>