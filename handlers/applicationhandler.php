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
									  WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new Application($row['id'], 
								   $row['eventId'], 
								   $row['userId'], 
								   $row['groupId'], 
								   $row['content'], 
								   $row['datetime'], 
								   $row['state'], 
								   $row['reason']);
		}
	}
	
	/* 
	 * Get a application by user
	 */
	public static function getApplicationByUser($user) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '` 
									  WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\'
									  ORDER BY `datetime` DESC;');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return self::getApplication($row['id']);
		}
	}
	
	/* 
	 * Get a application by user
	 */
	public static function getApplicationByUserAndEvent($user, $event) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '` 
									  WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\'
									  AND `eventId` = \'' . $event->getId(). '\'
									  ORDER BY `datetime` DESC;');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return self::getApplication($row['id']);
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
	public static function getPendingApplications() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
									  WHERE `state` = 1
									  ORDER BY `datetime`;');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $applicationList;
	}
	
	/* Returns a list of pending applications */
	public static function getPendingApplicationsForGroup($group) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
									  WHERE `groupId` = \'' . $con->real_escape_string($group->getId()) .  '\'
									  AND `state` = 1
									  ORDER BY `datetime`;');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $applicationList;
	}
	
	/* Creates an application in database */
	public static function createApplication($event, $user, $group, $content) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_crew_applications . '` (`eventId`, `userId`, `groupId`, `content`, `datetime`, `state`) 
							VALUES (\'' . $con->real_escape_string($event->getId()) . '\', 
									\'' . $con->real_escape_string($user->getId()) . '\', 
									\'' . $con->real_escape_string($group->getId()) . '\', 
									\'' . $con->real_escape_string($content) . '\', 
									\'' . date('Y-m-d H:i:s') . '\',
									\'1\');');
									
		MySQL::close($con);
	}
	
	/* 
	 * Remove a application.
	 */
	public static function removeApplication($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_crew_applications . '` 
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		MySQL::close($con);
	}
	
	public static function acceptApplication($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_applications . '` 
							SET `state` = \'2\'
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		// Set the user in the new group
		$application = self::getApplication($id);
		GroupHandler::changeGroupForUser($application->getUser(), $application->getGroup());
		
		MySQL::close($con);
	}
	
	public static function rejectApplication($id, $reason) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_applications . '` 
							SET `state` =  \'3\', 
								`reason` = \'' . $con->real_escape_string($reason) . '\'
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
									
		MySQL::close($con);
	}
}
?>