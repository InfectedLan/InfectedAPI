<?php
require_once 'settings.php';
require_once 'mysql.php';

class PermissionsHandler {
	// Returns true if user has the given permission, otherwise false
	public static function hasPermission($user, $value) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `value` FROM `' . Settings::db_table_infected_permissions . '` 
									  WHERE `userId` = \'' . $user->getId() . '\' 
									  AND `value` = \'' . $value . '\';');
								
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	public static function getPermissions($user) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `value` FROM `' . Settings::db_table_infected_permissions . '`
									  WHERE `userId` = \'' . $user->getId() . '\';');
		
		$permissionList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($permissionList, $row['value']);
		}
		
		MySQL::close($con);

		return $permissionList;
	}
	
	public static function createPermission($user, $value) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_permissions . '` (`userId`, `value`) 
							VALUES (\'' . $user->getId() . '\', 
									\'' . $value . '\')');
		
		MySQL::close($con);
	}
	
	public static function removePermission($user, $value) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_permissions . '` 
							WHERE `userId` = \'' . $user->getId() . '\'
							AND `value` = \'' . $value . '\';');
		
		MySQL::close($con);
	}
}
?>