<?php
require_once '/../Settings.php';
require_once '/../MySQL.php';

class PermissionsHandler {
	// Returns true if user has the given permission, otherwise false
	public static function hasPermission($userId, $permission) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT value FROM ' . Settings::db_table_permissions . ' WHERE userId=\'' . $userId . '\' AND value=\'' . $permission . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
}
?>