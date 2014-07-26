<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/group.php';

class GroupHandler {
	/* Get a group by id */
	public static function getGroup($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT * 
									  FROM `' . Settings::db_table_infected_crew_groups . '` 
									  WHERE `id` = \'' . $id . '\';');
									
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return new Group($row['id'], 
							 $row['name'], 
							 $row['title'], 
							 $row['description'], 
							 $row['chief']);
		}
	}
	
	/* Get an array of all groups */
	public static function getGroups() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT `id` 
									  FROM `' . Settings::db_table_infected_crew_groups . '` 
									  ORDER BY `name`;');
		
		$groupList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($groupList, self::getGroup($row['id']));
		}
		
		MySQL::close($con);
		
		return $groupList;
	}
	
	/* Create a new group */
	public static function createGroup($name, $title, $description, $chief) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_crew_groups . '` (`name`, `title`, `description`, `chief`) 
							VALUES (\'' . $name . '\', 
									\'' . $title . '\', 
									\'' . $description . '\', 
									\'' . $chief . '\');');
		
		MySQL::close($con);
	}
	
	/* Remove a page */
	public static function removeGroup($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_crew_groups . '` 
							WHERE `id` = \'' . $id . '\';');
		
		MySQL::close($con);
	}
	
	/* Update a page */
	public static function updateGroup($id, $name, $title, $description, $chief) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_crew_groups . '` 
							SET `name` = \'' . $name . '\', 
								`title` = \'' . $title . '\', 
								`description` = \'' . $description . '\', 
								`chief` = \'' . $chief . '\'
							WHERE `id` = \'' . $id . '\';');
		
		MySQL::close($con);
	}
}
?>