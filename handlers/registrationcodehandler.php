<?php
require_once 'settings.php';
require_once 'mysql.php';

class RegistrationCodeHandler {
	/* 
	 * Get the registration code for a given user, if one exists.
	 */
	public static function getRegistrationCode($user) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `code` FROM `' . Settings::db_table_infected_registrationcodes . '` 
									  WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\';');
							
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return $row['code'];
		}
	}
	
	public static function hasRegistrationCode($code) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_registrationcodes . '` 
									  WHERE `code` = \'' . $con->real_escape_string($code) . '\';');
							
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		return $row ? true : false;
	}
	
	public static function hasUserRegistrationCode($user) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_registrationcodes . '` 
									  WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\';');
							
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		return $row ? true : false;
	}
	
	/*
	 * Create a registration code for given user.
	 */
	public static function createRegistrationCode($user) {
		$code = bin2hex(openssl_random_pseudo_bytes(16));
		
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_registrationcodes . '` (`userId`, `code`) 
							VALUES (\'' . $con->real_escape_string($user->getId()) . '\', 
									\'' . $code . '\');');
									
		MySQL::close($con);
		
		return $code;
	}
	
	/*
	 * Remove registration code for current user, if one exists.
	 */
	public static function removeRegistrationCode($code) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_registrationcodes . '` 
							WHERE `code` = \'' . $con->real_escape_string($code) . '\';');
		
		MySQL::close($con);
	}
	
	/*
	 * Remove registration code for current user, if one exists.
	 */
	public static function removeUserRegistrationCode($user) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_registrationcodes . '` 
							WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\';');
		
		MySQL::close($con);
	}
}
?>
