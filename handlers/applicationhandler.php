<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/application.php';

class ApplicationHandler {
	/* 
	 * Get a application by id
	 */
	public static function getApplication($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_crew_applications . '` 
									  WHERE `id` = \'' . $id . '\';');
									  
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
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`;');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $applicationList;
	}
	
	/* Returns a list of pending applications */
	public static function getPendingApplications($group) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
									  WHERE `groupId` = \'' . $group->getId() .  '\'
									  AND `state` = 1;');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $applicationList;
	}
	
	/* Creates an application in database */
	public static function createApplication($user, $group, $content) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_crew_applications . '` (`userId`, `groupId`, `content`, `state`, `datetime`) 
							VALUES (\'' . $user->getId() . '\', 
									\'' . $group->getId() . '\', 
									\'' . $content . '\', 
									\'1\', 
									\'' . date('Y-m-d H:i:s') . '\');');
									
		MySQL::close($con);
	}
	
	public static function acceptApplication($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_applications . '` 
							SET `state` =  \'2\'
							WHERE `id` = \'' . $id . '\';');
									
		MySQL::close($con);
	}
	
	public static function rejectApplication($id, $reason) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_applications . '` 
							SET `state` =  \'3\', 
								`reason` = \'' . $reason . '\'
							WHERE `id` = \'' . $id . '\';');
									
		MySQL::close($con);
	}
}
?>