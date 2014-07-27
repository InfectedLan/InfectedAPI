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
	
		/* Is member of a team which means it's not a plain user */
	public function isTeamMember($userId) {
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
	public function isTeamLeader($userId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `leader` 
									  FROM `' . Settings::db_table_infected_crew_teams . '` 
									  WHERE `leader` = \'' . $userId . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	/* Sets the users team */
	public function changeTeam($userId, $teamId) {
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