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
								   $row['groupId'],
								   $row['userId'], 								   
								   $row['openedTime'], 
								   $row['closedTime'], 
								   $row['state'], 
								   $row['queued'],
								   $row['content'], 
								   $row['comment']);
		}
	}
	
	/* 
	 * Get a application by user
	 */
	public static function getApplicationByUser($user) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '` 
									  WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\'
									  ORDER BY `openedTime` DESC;');
									  
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
									  WHERE `eventId` = \'' . $con->real_escape_string($event->getId()) . '\'
									  AND `userId` = \'' . $user->getId(). '\'
									  ORDER BY `openedTime` DESC;');
									  
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
									  AND `queued` = \'0\'
									  ORDER BY `openedTime`;');
		
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
									  AND `state` = \'1\'
									  AND `queued` = \'0\'
									  ORDER BY `openedTime`;');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $applicationList;
	}
	
	public static function hasApplication($user) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '` 
									  WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		return $row ? true : false;
	}
	
	/* Creates an application in database */
	public static function createApplication($event, $group, $user, $content) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_crew_applications . '` (`eventId`, `groupId`, `userId`, `openedTime`, `state`, `content`) 
							VALUES (\'' . $con->real_escape_string($event->getId()) . '\', 
									\'' . $con->real_escape_string($group->getId()) . '\', 
									\'' . $con->real_escape_string($user->getId()) . '\', 
									\'' . date('Y-m-d H:i:s') . '\',
									\'1\',
									\'' . $con->real_escape_string($content) . '\');');
									
		MySQL::close($con);
	}
	
	/* 
	 * Remove a application.
	 */
	public static function removeApplication($application) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_crew_applications . '` 
							WHERE `id` = \'' . $application->getId() . '\';');
		
		MySQL::close($con);
	}

	/* 
	 * Remove a application.
	 */
	public static function removeUserApplication($user) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_crew_applications . '` 
							WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\';');
		
		MySQL::close($con);
	}	
	
	public static function acceptApplication($application, $comment) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_applications . '` 
							SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
								`state` = \'2\',
								`queued` =  \'0\',
								`comment` = \'' . $con->real_escape_string($comment) . '\'
							WHERE `id` = \'' . $application->getId() . '\';');
		
		// Set the user in the new group
		$application = self::getApplication($id);
		GroupHandler::changeGroupForUser($application->getUser(), $application->getGroup());
		
		MySQL::close($con);
	}
	
	public static function rejectApplication($application, $comment) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_applications . '` 
							SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
								`state` = \'3\', 
								`queued` =  \'0\',
								`comment` = \'' . $con->real_escape_string($comment) . '\'
							WHERE `id` = \'' . $application->getId() . '\';');
									
		MySQL::close($con);
	}
	
	/*
	 * Returns a list of all queued applications.
	 */
	public static function getQueuedApplications() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
									  WHERE `queued` = 1
									  ORDER BY `id`;');
		
		$queuedApplicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($queuedApplicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $queuedApplicationList;
	}
	
	/*
	 * Returns a list of all queued applications.
	 */
	public static function getQueuedApplicationsForGroup($group) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
									  WHERE `groupId` = \'' . $con->real_escape_string($group->getId()) .  '\'
									  AND `queued` = 1
									  ORDER BY `id`;');
		
		$queuedApplicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($queuedApplicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $queuedApplicationList;
	}
	
	/*
	 * Adds an application to the queue.
	 */
	public static function queue($application) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_applications . '` 
							SET `queued` =  \'1\'
							WHERE `id` = \'' . $application->getId() . '\';');
									
		MySQL::close($con);
	}
	
	/*
	 * Removes an application to the queue.
	 */
	public static function unqueue($application) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_applications . '` 
							SET `queued` =  \'0\'
							WHERE `id` = \'' . $application->getId() . '\';');
									
		MySQL::close($con);
	}
}
?>