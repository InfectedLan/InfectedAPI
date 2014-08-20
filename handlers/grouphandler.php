<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/group.php';
require_once 'objects/user.php';

class GroupHandler {
	/* Get a group by id */
	public static function getGroup($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_crew_groups . '` 
									  WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
									
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return new Group($row['id'], 
							 $row['name'], 
							 $row['title'], 
							 $row['description'], 
							 $row['leader']);
		}
	}
	
	/* Get a group by userId */
	public static function getGroupForUser($userId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `groupId` FROM `' . Settings::db_table_infected_crew_memberof . '` 
									  WHERE `userId` = \'' . $con->real_escape_string($userId) . '\';');
									
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return self::getGroup($row['groupId']);
		}
	}
	
	/* Get an array of all groups */
	public static function getGroups() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_groups . '` 
									  ORDER BY `name`;');
		
		$groupList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($groupList, self::getGroup($row['id']));
		}
		
		MySQL::close($con);
		
		return $groupList;
	}
	
	/* Create a new group */
	public static function createGroup($name, $title, $description, $leader) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_crew_groups . '` (`name`, `title`, `description`, `leader`) 
							VALUES (\'' . $con->real_escape_string($name) . '\', 
									\'' . $con->real_escape_string($title) . '\', 
									\'' . $con->real_escape_string($description) . '\', 
									\'' . $con->real_escape_string($leader) . '\');');
		
		MySQL::close($con);
	}
	
	/* Remove a page */
	public static function removeGroup($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_crew_groups . '` 
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		MySQL::close($con);
	}
	
	/* Update a page */
	public static function updateGroup($id, $name, $title, $description, $leader) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_groups . '` 
							SET `name` = \'' . $con->real_escape_string($name) . '\', 
								`title` = \'' . $con->real_escape_string($title) . '\', 
								`description` = \'' . $con->real_escape_string($description) . '\', 
								`leader` = \'' . $con->real_escape_string($leader) . '\'
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		MySQL::close($con);
	}
	
	/* Returns an array of users that are members of this group */
	public static function getMembers($groupId) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `' . Settings::db_table_infected_users . '`.`id` FROM `' . Settings::db_table_infected_users . '`
									  LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
									  ON `' . Settings::db_table_infected_users . '`.`id` = `userId` 
									  WHERE `groupId` = \'' . $con->real_escape_string($groupId) . '\'
									  ORDER BY `firstname` ASC;');
		
		$memberList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($memberList, UserHandler::getUser($row['id']));
		}
		
		MySQL::close($con);
		
		return $memberList;
	}

	/* 
	 * Is member of a group which means it's not a member user.
	 */
	public static function isGroupMember($userId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `groupId` 
									  FROM `' . Settings::db_table_infected_crew_memberof . '`
									  WHERE `userId` = \'' . $con->real_escape_string($userId) . '\'
									  AND `groupId` != \'0\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	/* 
	 * Return true if user is leader for a group.
	 */
	public static function isGroupLeader($userId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `leader` 
									  FROM `' . Settings::db_table_infected_crew_groups . '` 
									  WHERE `leader` = \'' . $con->real_escape_string($userId) . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}

	/*
	 * Sets the users group.
	 */
	public static function changeGroup($user, $group) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		if ($user->isGroupMember()) {	
			mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_memberof . '` 
								SET `groupId` = \'' . $con->real_escape_string($group->getId()) . '\', 
									`teamId` = \'0\' 
								WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\';');
		} else {
			mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_crew_memberof . '` (`userId`, `groupId`, `teamId`) 
								VALUES (\'' . $con->real_escape_string($user->getId()) . '\', 
										\'' . $con->real_escape_string($group->getId()) . '\', 
										\'0\');');
		}
		
		MySQL::close($con);
	}
	
	/*
	 * Removes a user from a group.
	 */
	public static function removeUserFromGroup($user) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_crew_memberof . '` 
							WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\';');
		
		MySQL::close($con);
	}
}
?>