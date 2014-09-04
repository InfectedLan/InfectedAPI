<?php
require_once 'session.php';
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/restrictedpage.php';
require_once 'objects/user.php';

class RestrictedPageHandler {
	/*
	 * Get page by id.
	 */
	public static function getPage($id) {
		if (Session::isAuthenticated()) {
			$user = Session::getCurrentUser();
		
			$con = MySQL::open(Settings::db_name_infected_crew);
		
			if ($user->hasPermission('*') ||
				$user->isGroupMember()) {
				
				if ($user->isGroupMember()) {
					if ($user->isTeamMember()) {
						$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
													  WHERE `id` = \'' . $con->real_escape_string($id) . '\' 
													  AND (`groupId` = \'0\' OR `groupId` = \'' . $con->real_escape_string($user->getGroup()->getId()) . '\') 
													  AND (`teamId` = \'0\' OR `teamId` = \'' . $con->real_escape_string($user->getTeam()->getId()) . '\');');
					} else {
						$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
													  WHERE `id` = \'' . $con->real_escape_string($id) . '\' 
													  AND (`groupId` = \'0\' OR `groupId` = \'' . $con->real_escape_string($user->getGroup()->getId()) . '\') 
													  AND `teamId` = \'0\';');
					}
				} else {
					$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
												  WHERE `id` = \'' . $con->real_escape_string($id) . '\' AND `private` = 0;');
				}
			} else {
				$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
											  WHERE `id` = \'' . $con->real_escape_string($id) . '\' 
											  AND `groupId` = 0
											  AND `teamId` = 0
											  AND `private` = 0;');
			}
			
			$row = mysqli_fetch_array($result);
				
			MySQL::close($con);
			
			if ($row) {
				return new RestrictedPage($row['id'], 
										  $row['name'], 
										  $row['title'], 
										  $row['content'], 
										  $row['groupId'], 
										  $row['teamId'],
										  $row['private']);
			}
		}
	}
	
	/* 
	 * Get page by name.
	 */
	public static function getPageByName($name) {
		if (Session::isAuthenticated()) {
			$con = MySQL::open(Settings::db_name_infected_crew);
			
			$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_pages . '`
										  WHERE `name` = \'' . $con->real_escape_string($name) . '\';');
			
			$row = mysqli_fetch_array($result);
			
			MySQL::close($con);

			if ($row) {
				return self::getPage($row['id']);
			}
		}
	}
	
	/*
	 * Get a list of all pages.
	 */
	public static function getPages() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_pages . '`;');
		
		$pageList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, self::getPage($row['id']));
		}
		
		MySQL::close($con);

		return $pageList;
	}
	
	/* 
	 * Get a list of pages for specified group.
	 */
	public static function getPagesForGroup($groupId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_pages . '`
									  WHERE `groupId` = \'' . $con->real_escape_string($groupId) . '\'
									  AND `teamId` = \'0\';');
		
		$pageList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, self::getPage($row['id']));
		}

		MySQL::close($con);
		
		return $pageList;
	}
	
	/*
	 * Get a list of pages for specified team.
	 */
	public static function getPagesForTeam($groupId, $teamId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_pages . '`
									  WHERE `groupId` = \'' . $con->real_escape_string($groupId) . '\'
									  AND `teamId` = \'' . $con->real_escape_string($teamId) . '\';');
		
		$pageList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, self::getPage($row['id']));
		}

		MySQL::close($con);
		
		return $pageList;
	}
	
	/* 
	 * Create a new page.
	 */
	public static function createPage($name, $title, $content, $groupId, $teamId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_crew_pages . '` (`name`, `title`, `content`, `groupId`, `teamId`) 
							VALUES (\'' . $con->real_escape_string($name) . '\', 
									\'' . $con->real_escape_string($title) . '\', 
									\'' . $con->real_escape_string($content) . '\', 
									\'' . $con->real_escape_string($groupId) . '\', 
									\'' . $con->real_escape_string($teamId) . '\')');
		
		MySQL::close($con);
	}
	
	/*
	 * Remove a page.
	 */
	public static function removePage($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_crew_pages . '` 
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		MySQL::close($con);
	}
	
	/*
	 * Update a page.
	 */
	public static function updatePage($id, $name, $title, $content) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_pages . '` 
							SET `name` = \'' . $con->real_escape_string($name) . '\', 
								`title` = \'' . $con->real_escape_string($title) . '\', 
								`content` = \'' . $con->real_escape_string($content) . '\' 
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		MySQL::close($con);
	}
}
?>