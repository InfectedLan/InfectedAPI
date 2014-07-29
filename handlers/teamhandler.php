<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/team.php';

class TeamHandler {
	/* Get a team by id */
	public static function getTeam($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT * 
									  FROM `' . Settings::db_table_infected_crew_teams . '` 
									  WHERE `id` = \'' . $id . '\';');
									  
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
									  WHERE `userId` = \'' . $userId . '\';');
									
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return self::getTeam($row['teamId']);
		}
	}
	
	/* Get an array of all teams */
	public static function getTeams() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` 
									  FROM `' . Settings::db_table_infected_crew_teams . '`;');
		
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

		$result = mysqli_query($con, 'SELECT `id` 
									  FROM `' . Settings::db_table_infected_crew_teams . '`
									  WHERE `groupId` = \'' . $groupId . '\';');
		
		$teamList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($teamList, self::getTeam($row['id']));
		}
		
		MySQL::close($con);
		
		return $teamList;
	}
	
	/* Create a new team */
	public static function createTeam($groupId, $name, $title, $description, $leader) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_crew_teams . '` (`groupId`, `name`, `title`, `description`, `leader`) 
							VALUES (\'' . $groupId . '\', 
									\'' . $name . '\', 
									\'' . $title . '\', 
									\'' . $description . '\', 
									\'' . $leader . '\')');
		
		MySQL::close($con);
	}
	
	/* Remove a team */
	public static function removeTeam($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_crew_teams . '` 
							WHERE `id` = \'' . $id . '\';');
		
		MySQL::close($con);
	}
	
	/* Update a team */
	public static function updateTeam($id, $groupId, $name, $title, $description, $leader) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_teams . '` 
							SET `groupId` = \'' . $groupId . '\', 
								`name` = \'' . $name . '\', 
								`title` = \'' . $title . '\', 
								`description` = \'' . $description . '\', 
								`leader` = \'' . $leader . '\' 
							WHERE `id` = \'' . $id . '\';');
		
		MySQL::close($con);
	}
	
	/* Returns an array of users that are members of this team */
	public static function getMembers($groupId, $teamId) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `' . Settings::db_table_infected_users . '`.`id` FROM `' . Settings::db_table_infected_users . '`
									  LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
									  ON `' . Settings::db_table_infected_users . '`.`id` = `userId` 
									  WHERE `groupId` = \'' . $groupId . '\'
									  AND `teamId` = \'' . $teamId . '\' 
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
									  WHERE `userId` = \'' . $userId . '\' 
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
									  WHERE `leader` = \'' . $userId . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	/* Sets the users team */
	public static function changeTeam($userId, $teamId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		if ($this->isGroupMember) {	
			mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_memberof . '` 
								SET `teamId` = \'' . $teamId . '\' 
								WHERE `userId` = \'' . $userId . '\' 
								AND `groupId` = \'' . GroupHandler::getGroupForUser($userId)->getId() . '\';');	
		}
		
		MySQL::close($con);
	}
}
?>