<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'team.php';

class TeamHandler {
	/* Get a team by id */
	public static function getTeam($id) {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_teams . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return new Team($row['id'], 
							$row['groupId'], 
							$row['name'], 
							$row['title'], 
							$row['description'], 
							$row['chief']);
		}
	}
	
	/* Get an array of all teams */
	public static function getTeams() {
		$con = MySQL::open(Settings::db_name_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_teams);
		
		$teamList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($teamList, self::getTeam($row['id']));
		}
		
		MySQL::close($con);
		
		return $teamList;
	}
	
	/* Create a new team */
	public static function createTeam($groupId, $name, $title, $description, $chief) {
		$con = MySQL::open(Settings::db_name_crew);
		
		mysqli_query($con, 'INSERT INTO ' . Settings::db_table_teams . ' (groupId, name, title, description, chief) 
							VALUES (\'' . $groupId . '\', 
									\'' . $name . '\', 
									\'' . $title . '\', 
									\'' . $description . '\', 
									\'' . $chief . '\')');
		
		MySQL::close($con);
	}
	
	/* Remove a team */
	public static function removeTeam($id) {
		$con = MySQL::open(Settings::db_name_crew);
		
		mysqli_query($con, 'DELETE FROM ' . Settings::db_table_teams . ' WHERE id=\'' . $id . '\'');
		
		MySQL::close($con);
	}
	
	/* Update a team */
	public static function updateTeam($id, $groupId, $name, $title, $description, $chief) {
		$con = MySQL::open(Settings::db_name_crew);
		
		mysqli_query($con, 'UPDATE ' . Settings::db_table_teams . ' SET groupId=\'' . $groupId . '\', name=\'' . $name . '\', title=\'' . $title . '\', description=\'' . $description . '\', chief=\'' . $chief . '\' WHERE id=\'' . $id . '\'');
		
		MySQL::close($con);
	}
}

?>