<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/gameapplication.php';

class GameApplicationHandler {
	public static function getGameApplication($id) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_main_gameapplications . '` 
									  WHERE `id` = \'' . $id . '\';');
										
		$row = mysqli_fetch_array($result);
		
		if ($row) {
			return new GameApplication($row['id'], 
									   $row['game'], 
									   $row['name'], 
									   $row['tag'], 
									   $row['contactname'], 
									   $row['contactnick'], 
									   $row['phone'], 
									   $row['email']);
		}
		
		MySQL::close($con);
	}
	
	public static function getGameApplications($game) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_main_gameapplications . '` 
									  WHERE `game` = \'' . $game->getId() . '\';');
									
		$gameApplicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($gameApplicationList, self::getGameApplication($row['id']));
		}
		
		return $gameApplicationList;
		
		MySQL::close($con);
	}
	
	public static function getGameApplicationsForEvent($game, $event) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_main_gameapplications . '` 
									  WHERE `event` = \'' . $event->getId() . '\'
									  AND `game` = \'' . $game->getId() . '\';');
									
		$gameApplicationList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($gameApplicationList, self::getGameApplication($row['id']));
		}
		
		return $gameApplicationList;
		
		MySQL::close($con);
	}
	
	public static function createGameApplication($game, $name, $tag, $contactname, $contactnick, $phone, $email) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_main_gameapplications . '` (`game`, `name`, `tag`, `contactname`, `contactnick`, `phone`, `email`) 
							VALUES (\'' . $game->getId() . '\', 
									\'' . $name . '\', 
									\'' . $tag . '\', 
									\'' . $contactname . '\', 
									\'' . $contactnick . '\', 
									\'' . $phone . '\', 
									\'' . $email . '\');');
		
		MySQL::close($con);
	}
}
?>