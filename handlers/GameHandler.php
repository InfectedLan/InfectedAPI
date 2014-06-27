<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'game.php';

	class GameHandler {
		public static function getGame($id) {
			$con = MySQL::open(Settings::db_name_infected);
			
			$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_games . ' WHERE id=\'' . $id . '\'');
			$row = mysqli_fetch_array($result);
			
			MySQL::close($con);

			if ($row) {
				return new Game($row['id'], $row['name'], $row['title'], $row['price'], $row['mode'], $row['description'], $row['deadline'], $row['published']);
			}
		}
		
		public static function getGames() {
			$con = MySQL::open(Settings::db_name_infected);
			
			$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_games);
			$gameList = array();
			
			while ($row = mysqli_fetch_array($result)) {
				array_push($gameList, self::getGame($row['id']));
			}

			MySQL::close($con);
			
			return $gameList;
		}
		
		public static function getPublishedGames() {
			$con = MySQL::open(Settings::db_name_infected);
			
			$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_games . ' WHERE published=\'1\'');
			$gameList = array();
			
			while ($row = mysqli_fetch_array($result)) {
				array_push($gameList, self::getGame($row['id']));
			}
			
			MySQL::close($con);

			return $gameList;
		}
	}
?>