<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/permission.php';

class PermissionsHandler {
	public static function getPermission($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_permissions . '`
									  WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
								
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return new Permission($row['id'],
								  $row['value'],
								  $row['description']);
		}
	}
	
	public static function getPermissionByValue($value) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_permissions . '`
									  WHERE `value` = \'' . $con->real_escape_string($value) . '\';');
								
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return self::getPermission($row['id']);
		}
	}
	
	public static function getPermissions() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_permissions . '`;');
		
		$permissionList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($permissionList, self::getPermission($row['id']));
		}
		
		MySQL::close($con);

		return $permissionList;
	}
}
?>