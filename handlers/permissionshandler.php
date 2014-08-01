<?php
require_once 'settings.php';
require_once 'mysql.php';

class PermissionsHandler {
	// Returns true if user has the given permission, otherwise false
	public static function hasPermission($userId, $permission) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `value` FROM `' . Settings::db_table_infected_permissions . '` 
									  WHERE `userId` = \'' . $userId . '\' 
									  AND `value` = \'' . $permission . '\';');
								
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	public static function addPermission($userId, $permission) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_permissions . '` (`userId`, `value`) 
							VALUES (\'' . $userId . '\', 
									\'' . $permission . '\')');
		
		MySQL::close($con);
	}
	
	public static function removePermission($userId, $permission) {
		$con = MySQL::open(Settings::db_name_infected);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_permissions . '` 
							WHERE `userId` = \'' . $userId . '\'
							AND `value` = \'' . $permission . '\';');
		
		MySQL::close($con);
	}
	
	public static function getPermissions($userId) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_permissions . '`
									  WHERE `userId` = \'' . $userId . '\';');
		
		$permissionList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, $row['value']);
		}
		
		MySQL::close($con);

		return $permissionList;
	}
}
?>