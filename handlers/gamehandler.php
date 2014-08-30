<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/game.php';

class GameHandler {
	public static function getGame($id) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_main_games . '` 
									  WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new Game($row['id'], 
							$row['name'], 
							$row['title'], 
							$row['price'], 
							$row['mode'], 
							$row['description'], 
							$row['deadlineTime'], 
							$row['published']);
		}
	}
	
	public static function getGames() {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_main_games . '`;');
									  
		$gameList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($gameList, self::getGame($row['id']));
		}

		MySQL::close($con);
		
		return $gameList;
	}
	
	public static function getPublishedGames() {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_main_games . '` 
									  WHERE `published` = \'1\'');
		
		$gameList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($gameList, self::getGame($row['id']));
		}
		
		MySQL::close($con);

		return $gameList;
	}
	
	/* 
	 * Create a new game.
	 */
	public static function createGame($name, $title, $price, $mode, $description, $deadlineTime, $published) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_main_games . '` (`name`, `title`, `price`, `description`, `deadlineTime`, `published`) 
							VALUES (\'' . $con->real_escape_string($name) . '\', 
									\'' . $con->real_escape_string($title) . '\', 
									\'' . $con->real_escape_string($price) . '\', 
									\'' . $con->real_escape_string($mode) . '\', 
									\'' . $con->real_escape_string($description) . '\', 
									\'' . $con->real_escape_string($deadlineTime) . '\', 
									\'' . $con->real_escape_string($published) . '\');');
		
		MySQL::close($con);
	}
	
	/* 
	 * Update information about a game.
	 */
	public static function updateGame($id, $name, $title, $price, $mode, $description, $deadlineTime, $published) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_main_games . '` 
							SET `name` = \'' . $con->real_escape_string($name) . '\', 
								`title` = \'' . $con->real_escape_string($title) . '\', 
								`price` = \'' . $con->real_escape_string($price) . '\', 
								`mode` = \'' . $con->real_escape_string($mode) . '\', 
								`description` = \'' . $con->real_escape_string($description) . '\', 
								`deadlineTime` = \'' . $con->real_escape_string($deadlineTime) . '\', 
								`published` = \'' . $con->real_escape_string($published) . '\'
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		MySQL::close($con);
	}
	
	/*
	 * Remove a game from the database.
	 */
	public static function removeGame($id) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_main_games . '` 
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		MySQL::close($con);
	}
}
?>