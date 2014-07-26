<?php
require_once 'settings.php';
require_once 'mysql.php';

class PasswordResetHandler {
	/* Get a password reset code by userId */
	public static function getResetCode($userId) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `code` 
									  FROM `' . Settings::db_table_infected_passresets . '` 
									  WHERE `userId` = \'' . $userId . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return $row['code'];
		}
	}
	
	/* Set a password reset code for user */
	public static function setResetCode($userId, $code) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_passresets . '` (`userId`, `code`) 
							VALUES (\'' . $userId . '\', 
									\'' . $code . '\')');
		
		MySQL::close($con);
	}
}
?>