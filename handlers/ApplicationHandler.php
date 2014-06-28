<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'application.php';

class ApplicationHandler {
	/* Get a application by id */
	public static function getApplication($id) {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_applications . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new Application($row['id'], 
								$row['userId'], 
								$row['groupId'], 
								$row['content'], 
								$row['state'], 
								$row['datetime'], 
								$row['reason']);
		}
	}
	
	/* Get a list of all applications */
	public static function getApplications() {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_applications);
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $applicationList;
	}
}
?>