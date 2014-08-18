<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/team.php';

class TeamHandler {
	/* Get a team by id */
	public static function getTeam($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_crew_teams . '` 
									  WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return new Team($row['id'], 
							$row['groupId'], 
							$row['name'], 
							$row['title'], 
							$row['description'], 
							$row['leader']);
		}
	}
	
	/* Get a group by userId */
	public static function getTeamForUser($userId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `teamId`
									  FROM `' . Settings::db_table_infected_crew_memberof . '` 
									  WHERE `userId` = \'' . $con->real_escape_string($userId) . '\';');
									
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return self::getTeam($row['teamId']);
		}
	}
	
	/* Get an array of all teams */
	public static function getTeams() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_teams . '`;');
		
		$teamList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($teamList, self::getTeam($row['id']));
		}
		
		MySQL::close($con);
		
		return $teamList;
	}
	
	/* Get an array of all teams */
	public static function getTeamsForGroup($groupId) {
		$con = MySQL::open(Settings::db_name_infected_crew);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_crew_teams . '`
									  WHERE `groupId` = \'' . $con->real_escape_string($groupId) . '\';');
		
		$teamList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($teamList, self::getTeam($row['id']));
		}
		
		MySQL::close($con);
		
		return $teamList;
	}
	
	/* Create a new team */
	public static function createTeam($group, $name, $title, $description, $leader) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_crew_teams . '` (`groupId`, `name`, `title`, `description`, `leader`) 
							VALUES (\'' . $con->real_escape_string($group->getId()) . '\', 
									\'' . $con->real_escape_string($name) . '\', 
									\'' . $con->real_escape_string($title) . '\', 
									\'' . $con->real_escape_string($description) . '\', 
									\'' . $con->real_escape_string($leader) . '\')');
		
		MySQL::close($con);
	}
	
	/* Remove a team */
	public static function removeTeam($group, $team) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_crew_teams . '` 
							WHERE `id` = \'' . $con->real_escape_string($team->getId()) . '\'
							AND `groupId` = \'' . $con->real_escape_string($group->getId()) . '\';');
		
		MySQL::close($con);
	}
	
	/* Update a team */
	public static function updateTeam($team, $group, $name, $title, $description, $leader) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_teams . '` 
							SET `groupId` = \'' . $con->real_escape_string($group->getId()) . '\', 
								`name` = \'' . $con->real_escape_string($name) . '\', 
								`title` = \'' . $con->real_escape_string($title) . '\', 
								`description` = \'' . $con->real_escape_string($description) . '\', 
								`leader` = \'' . $con->real_escape_string($leader) . '\' 
							WHERE `id` = \'' . $con->real_escape_string($team->getId()) . '\';');
		
		MySQL::close($con);
	}
	
	/* Returns an array of users that are members of this team */
	public static function getMembers($groupId, $teamId) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `' . Settings::db_table_infected_users . '`.`id` FROM `' . Settings::db_table_infected_users . '`
									  LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
									  ON `' . Settings::db_table_infected_users . '`.`id` = `userId` 
									  WHERE `groupId` = \'' . $con->real_escape_string($groupId) . '\'
									  AND `teamId` = \'' . $con->real_escape_string($teamId) . '\' 
									  ORDER BY `firstname` ASC;');
		
		$memberList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($memberList, UserHandler::getUser($row['id']));
		}
		
		MySQL::close($con);
		
		return $memberList;
	}
	
	/* Is member of a team which means it's not a plain user */
	public static function isTeamMember($userId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `teamId` 
									  FROM `' . Settings::db_table_infected_crew_memberof. '` 
									  WHERE `userId` = \'' . $con->real_escape_string($userId) . '\' 
									  AND `teamId` != \'0\'');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	/* Return true if user is leader for a team */
	public static function isTeamLeader($userId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `leader` 
									  FROM `' . Settings::db_table_infected_crew_teams . '` 
									  WHERE `leader` = \'' . $con->real_escape_string($userId) . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	/* Sets the users team */
	public static function changeTeamForUser($user, $group, $team) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		if ($user->isGroupMember()) {	
			mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_memberof . '` 
								SET `teamId` = \'' . $con->real_escape_string($team->getId()) . '\' 
								WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\' 
								AND `groupId` = \'' . $con->real_escape_string($group->getId()) . '\';');	
		}
		
		MySQL::close($con);
	}
	
	/*
	 * Removes a user from a team.
	 */
	public static function removeUserFromTeam($user) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_memberof . '` 
							SET `teamId` = \'0\'
							WHERE `userId` = \'' . $con->real_escape_string($user->getId()) . '\';');	
		
		MySQL::close($con);
	}
}
?>