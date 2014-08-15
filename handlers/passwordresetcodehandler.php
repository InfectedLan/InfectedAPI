<?php
require_once 'settings.php';
require_once 'mysql.php';

class PasswordResetCodeHandler {
	public static function createPasswordResetCode($user) {
		$code = bin2hex(openssl_random_pseudo_bytes(16));
		
		$con = MySQL::open(Settings::db_name_infected);
		
		if (!self::hasPasswordResetCode($user)) {
			mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_passwordresetcodes . '` (`userId`, `code`) 
								VALUES (\'' . $user->getId() . '\', 
										\'' . $code . '\');');
		} else {
			mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_passwordresetcodes . '` 
								SET `code` = \'' . $code . '\'
								WHERE `userId` = \'' . $user->getId() . '\';');
		}
		
		MySQL::close($con);
		
		return $code;
	}
	
	public static function hasPasswordResetCode($user) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
									  WHERE `userId` = \'' . $user->getId() . '\';');
							
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		return $row ? true : false;
	}
	
	public static function existsPasswordResetCode($code) {
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