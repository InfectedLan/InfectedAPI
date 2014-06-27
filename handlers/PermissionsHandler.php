<?php
require_once 'settings.php';
require_once 'mysql.php';

	class PermissionsHandler {
		// Compatibility with petterroea's work.
		public static function getPermission($username, $value) {
			$con = MySQL::open(Settings::db_name_infected);
			
			$result = mysqli_query($con, 'SELECT value FROM ' . Settings::db_table_permissions . ' WHERE username=\'' . $username . '\' AND value=\'' . $value . '\'');
			$row = mysqli_fetch_array($result);
			
			if ($row) {
				return true;
			}
			
			MySQL::close($con);
			
			return false;
		}

		/*
		 * COMMENTED OUT AS UNKNOWN DATABASE
		 */

		/*
		// Returns true if user has the givern permission, otherwise false
		public static function hasPermission($userId, $permission) {
			$con = MySQL::open(Settings::db_name_infected);
			
			$result = mysqli_query($con, 'SELECT value FROM ' . Settings::db_table_permissions . ' WHERE userId=\'' . $userId . '\' AND value=\'' . $permission . '\'');
			$row = mysqli_fetch_array($result);
			
			MySQL::close($con);
			
			return $row ? true : false;
		}
		*/
	}
?>