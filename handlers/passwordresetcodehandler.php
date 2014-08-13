<?php
require_once 'settings.php';
require_once 'mysql.php';

class ResetCodeHandler {
	public static function createPasswordResetCode($userId) {
		$code = bin2hex(openssl_random_pseudo_bytes(16));
		
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_passwordresetcodes . '` (`userId`, `code`) 
							VALUES (\'' . $userId . '\', 
									\'' . $code . '\');');
									
		MySQL::close($con);
		
		return $code;
	}
	
	public static function hasPasswordResetCode($code) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
									  WHERE `code` = \'' . $code . '\';');
							
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		return $row ? true : false;
	}
	
	public static function getUserFromPasswordResetCode($code) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `userId` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
									  WHERE `code` = \'' . $code . '\';');
							
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return self::getUser($row['userId']);
		}
	}
	
	public static function removePasswordResetCode($code) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
							WHERE `code` = \'' . $code . '\';');
		
		MySQL::close($con);
	}
}
?>