<?php
require_once 'settings.php';
require_once 'mysql.php';

class UserPermissionsHandler {
	// Returns true if user has the given permission, otherwise false
	public static function hasUserPermission($user, $value) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `value` FROM `' . Settings::db_table_infected_userpermissions . '` 
									  WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\' 
									  AND `value` = \'' . $con->real_escape_string($value) . '\';');
								
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	public static function getUserPermissions($user) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `value` FROM `' . Settings::db_table_infected_userpermissions . '`
									  WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\';');
		
		$permissionList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($permissionList, $row['value']);
		}
		
		MySQL::close($con);

		return $permissionList;
	}
	
	public static function createUserPermission($user, $value) {
		if (!self::hasUserPermission($user, $value)) {
			$con = MySQL::open(Settings::db_name_infected);
		
			mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_userpermissions . '` (`userId`, `value`) 
								VALUES (\'' . $con->real_escape_string($user->getId()) . '\', 
										\'' . $con->real_escape_string($value) . '\')');
			
			MySQL::close($con);
		}
	}
	
	public static function removeUserPermission($user, $value) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_userpermissions . '` 
							WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\'
							AND `value` = \'' . $con->real_escape_string($value) . '\';');
		
		MySQL::close($con);
	}
}
?>