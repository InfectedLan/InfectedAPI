<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/application.php';

class ApplicationHandler {
	/* 
	 * Get an application by it's internal id.
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
								   $row['content'], 
								   $row['comment']);
		}
	}
	
	/*
	 * Returns a list of all applications.
	 */
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
	
	/* 
	 * Returns a list of pending applications.
	 */
	public static function getPendingApplications() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `' . Settings::db_table_infected_crew_applications . '`.`id` FROM `' . Settings::db_table_infected_crew_applications . '`
								      LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
									  ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
									  WHERE `applicationId` IS NULL
									  AND `state` = \'1\'
									  ORDER BY `openedTime`;');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $applicationList;
	}
	
	/* 
	 * Returns a list of pending applications.
	 */
	public static function getPendingApplicationsForGroup($group) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `' . Settings::db_table_infected_crew_applications . '`.`id` FROM `' . Settings::db_table_infected_crew_applications . '`
								      LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
									  ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
									  WHERE `applicationId` IS NULL
									  AND `groupId` = \'' . $con->real_escape_string($group->getId()) .  '\'
									  AND `state` = \'1\'
									  ORDER BY `openedTime`;');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $applicationList;
	}
	
	/*
	 * Returns a list of all queued applications.
	 */
	public static function getQueuedApplications() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `' . Settings::db_table_infected_crew_applications . '`.`id` FROM `' . Settings::db_table_infected_crew_applications . '`
									  LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
								      ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
									  WHERE `applicationId` IS NOT NULL
									  AND `state` = \'1\'
									  ORDER BY `' . Settings::db_table_infected_crew_applicationqueue . '`.`id`;');
		
		$queuedApplicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($queuedApplicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $queuedApplicationList;
	}
	
	/*
	 * Returns a list of all queued applications for a given group.
	 */
	public static function getQueuedApplicationsForGroup($group) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `' . Settings::db_table_infected_crew_applications . '`.`id` FROM `' . Settings::db_table_infected_crew_applications . '`
									  LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
									  ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
									  WHERE `applicationId` IS NOT NULL
									  AND `groupId` = \'' . $con->real_escape_string($group->getId()) .  '\'
									  AND `state` = \'1\'
									  ORDER BY `' . Settings::db_table_infected_crew_applicationqueue . '`.`id`;');
		
		$queuedApplicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($queuedApplicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $queuedApplicationList;
	}
	
	/* 
	 * Create a new application. 
	 */
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
	 * Remove an application.
	 */
	public static function removeApplication($application) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		// Remove the application.
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_crew_applications . '` 
							WHERE `id` = \'' . $con->real_escape_string($application->getId()) . '\';');
		
		// Remove the application from the queue, if present.
		self::unqueueApplication($application);
		
		MySQL::close($con);
	}
	
	/*
	 * Accepts an application, with a optional comment.
	 */
	public static function acceptApplication($application, $comment) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_applications . '` 
							SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
								`state` = \'2\',
								`comment` = \'' . $con->real_escape_string($comment) . '\'
							WHERE `id` = \'' . $con->real_escape_string($application->getId()) . '\';');
		
		// Remove the application from the queue, if present.
		self::unqueueApplication($application);
		
		// Set the user in the new group
		GroupHandler::changeGroupForUser($application->getUser(), $application->getGroup());
		
		MySQL::close($con);
	}
	
	/*
	 * Rejects an application, with a optional comment.
	 */
	public static function rejectApplication($application, $comment) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_applications . '` 
							SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
								`state` = \'3\', 
								`comment` = \'' . $con->real_escape_string($comment) . '\'
							WHERE `id` = \'' . $con->real_escape_string($application->getId()) . '\';');
							
		// Remove the application from the queue, if present.
		self::unqueueApplication($application);
							
		MySQL::close($con);
	}
	
	/*
	 * Checks if an application is queued.
	 */
	public static function isQueued($application) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_applicationqueue . '` 
									  WHERE `applicationId` = \'' . $con->real_escape_string($application->getId()) . '\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	/*
	 * Puts an application in queue.
	 */
	public static function queueApplication($application) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		if (!self::isQueued($application)) {
			mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_crew_applicationqueue . '` (`applicationId`) 
								VALUES (\'' . $con->real_escape_string($application->getId()) . '\');');
		}
									
		MySQL::close($con);
	}
	
	/*
	 * Removes an application from queue.
	 */
	public static function unqueueApplication($application) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_crew_applicationqueue . '` 
							WHERE `applicationId` = \'' . $con->real_escape_string($application->getId()) . '\';');
									
		MySQL::close($con);
	}
	
	/*
	 * Returns a list of all applications for given user.
	 */
	public static function getUserApplications($user) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
									  WHERE `userId` = \'' . $user->getId() . '\';');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $applicationList;
	}
	
	/*
	 * Returns a list of all applications for that event.
	 */
	public static function getApplicationsForEvent($event) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
									  WHERE `eventId` = \'' . $event->getId() . '\';');
		
		$applicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($applicationList, self::getApplication($row['id']));
		}
		
		MySQL::close($con);
		
		return $applicationList;
	}
}
?>